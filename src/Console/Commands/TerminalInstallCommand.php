<?php

namespace MWGuerra\WebTerminal\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\note;
use function Laravel\Prompts\select;
use function Laravel\Prompts\warning;

class TerminalInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'terminal:install
                            {--with-tenant : Include tenant_id column in migration}
                            {--no-tenant : Use standard migration without tenant support}
                            {--force : Overwrite existing files}';

    /**
     * The console command description.
     */
    protected $description = 'Install WebTerminal with logging support';

    /**
     * The filesystem instance.
     */
    protected Filesystem $files;

    /**
     * Create a new command instance.
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->displayWelcome();

        // Validate mutually exclusive options
        if ($this->option('with-tenant') && $this->option('no-tenant')) {
            $this->error('Cannot use --with-tenant and --no-tenant together.');

            return self::FAILURE;
        }

        // Determine tenant support
        $withTenant = $this->determineTenantSupport();

        // Ask what to install
        $toInstall = $this->askWhatToInstall();

        // Publish selected components
        $this->publishComponents($toInstall, $withTenant);

        // Ask about running migration
        if (in_array('migration', $toInstall)) {
            $this->handleMigration();
        }

        $this->displayCompletion();

        return self::SUCCESS;
    }

    /**
     * Display welcome message.
     */
    protected function displayWelcome(): void
    {
        $this->newLine();
        note('WebTerminal Installation');
        info('Welcome to WebTerminal installer!');
        info('This will set up terminal logging for your application.');
        $this->newLine();
    }

    /**
     * Determine if tenant support should be included.
     */
    protected function determineTenantSupport(): bool
    {
        if ($this->option('with-tenant')) {
            return true;
        }

        if ($this->option('no-tenant')) {
            return false;
        }

        // Interactive prompt
        return select(
            label: 'Is this a multi-tenant application?',
            options: [
                false => 'No - Standard installation',
                true => 'Yes - Add tenant_id column to logs',
            ],
            default: false,
        );
    }

    /**
     * Ask what components to install.
     */
    protected function askWhatToInstall(): array
    {
        if ($this->option('no-interaction')) {
            return ['config', 'migration'];
        }

        return multiselect(
            label: 'What would you like to install?',
            options: [
                'config' => 'Configuration file',
                'migration' => 'Database migration',
                'views' => 'Blade views (for customization)',
            ],
            default: ['config', 'migration'],
            required: true,
        );
    }

    /**
     * Publish selected components.
     */
    protected function publishComponents(array $toInstall, bool $withTenant): void
    {
        foreach ($toInstall as $component) {
            match ($component) {
                'config' => $this->publishConfig(),
                'migration' => $this->publishMigration($withTenant),
                'views' => $this->publishViews(),
            };
        }
    }

    /**
     * Publish configuration file.
     */
    protected function publishConfig(): void
    {
        $source = __DIR__ . '/../../../config/web-terminal.php';
        $destination = config_path('web-terminal.php');

        if ($this->files->exists($destination) && ! $this->option('force')) {
            if (! confirm('Configuration file already exists. Overwrite?', default: false)) {
                warning('Skipped configuration file.');

                return;
            }
        }

        $this->files->copy($source, $destination);
        info('Configuration published to config/web-terminal.php');
    }

    /**
     * Publish migration file.
     */
    protected function publishMigration(bool $withTenant): void
    {
        $stubName = $withTenant
            ? 'create_terminal_logs_table_with_tenant.php.stub'
            : 'create_terminal_logs_table.php.stub';

        $source = __DIR__ . '/../../../database/migrations/' . $stubName;
        $timestamp = date('Y_m_d_His');
        $destination = database_path("migrations/{$timestamp}_create_terminal_logs_table.php");

        // Check if migration already exists
        $existingMigrations = glob(database_path('migrations/*_create_terminal_logs_table.php'));
        if (! empty($existingMigrations) && ! $this->option('force')) {
            if (! confirm('Terminal logs migration already exists. Publish anyway?', default: false)) {
                warning('Skipped migration file.');

                return;
            }
        }

        $this->files->copy($source, $destination);

        $tenantInfo = $withTenant ? ' (with tenant support)' : '';
        info("Migration published to database/migrations/{$timestamp}_create_terminal_logs_table.php{$tenantInfo}");
    }

    /**
     * Publish views.
     */
    protected function publishViews(): void
    {
        $source = __DIR__ . '/../../../resources/views';
        $destination = resource_path('views/vendor/web-terminal');

        if ($this->files->isDirectory($destination) && ! $this->option('force')) {
            if (! confirm('Views directory already exists. Overwrite?', default: false)) {
                warning('Skipped views.');

                return;
            }
        }

        $this->files->copyDirectory($source, $destination);
        info('Views published to resources/views/vendor/web-terminal');
    }

    /**
     * Handle migration execution.
     */
    protected function handleMigration(): void
    {
        if ($this->option('no-interaction')) {
            return;
        }

        $runMigration = select(
            label: 'Run database migration now?',
            options: [
                true => 'Yes',
                false => 'No - I\'ll run it manually',
            ],
            default: true,
        );

        if ($runMigration) {
            $this->call('migrate');
            info('Migration completed successfully');
        } else {
            note('Run `php artisan migrate` when ready.');
        }
    }

    /**
     * Display completion message.
     */
    protected function displayCompletion(): void
    {
        $this->newLine();
        info('Installation complete!');
        note('Configure logging in config/web-terminal.php');
        $this->newLine();
    }
}
