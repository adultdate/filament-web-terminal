{{-- Terminal Output Area --}}
<div
    class="terminal-output flex-1 overflow-y-auto p-3 scroll-smooth text-left"
    x-ref="output"
    role="log"
    aria-live="polite"
    aria-label="Terminal output"
>
    @foreach($output as $line)
        @if(trim($line['content'] ?? '') !== '')
            <div class="whitespace-pre-wrap break-words m-0 p-0 leading-snug text-left block w-full
                @if(($line['type'] ?? '') === 'stdout') text-slate-700 dark:text-zinc-200
                @elseif(($line['type'] ?? '') === 'stderr') text-red-600 dark:text-red-300
                @elseif(($line['type'] ?? '') === 'error') text-red-700 dark:text-red-500 font-semibold
                @elseif(($line['type'] ?? '') === 'info') text-blue-600 dark:text-blue-400
                @elseif(($line['type'] ?? '') === 'command') text-emerald-600 dark:text-emerald-400 font-medium pt-1 pb-0.5
                @elseif(($line['type'] ?? '') === 'system') text-slate-500 dark:text-gray-500 italic
                @else text-slate-700 dark:text-zinc-200
                @endif
            ">{!! $this->convertAnsiToHtml($line['content']) !!}</div>
        @endif
    @endforeach
</div>
