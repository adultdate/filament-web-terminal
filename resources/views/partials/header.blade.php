{{-- Terminal Header Bar --}}
<div class="flex items-center px-4 py-3 bg-slate-200/80 dark:bg-black/30 border-b border-slate-300 dark:border-white/5">
    @if($showWindowControls)
    <div class="flex gap-2">
        <span class="w-3 h-3 rounded-full bg-[#ff5f56] hover:opacity-80 transition-opacity"></span>
        <span class="w-3 h-3 rounded-full bg-[#ffbd2e] hover:opacity-80 transition-opacity"></span>
        <span class="w-3 h-3 rounded-full bg-[#27c93f] hover:opacity-80 transition-opacity"></span>
    </div>
    @endif
    <div class="flex-1 text-center text-xs font-medium text-slate-500 dark:text-white/50 tracking-wide">{{ $title }}</div>
    {{-- Header Actions --}}
    <div class="flex items-center gap-2">
        {{-- Info Toggle Button --}}
        <button
            type="button"
            @click="showInfoPanel = !showInfoPanel"
            class="flex items-center justify-center w-7 h-7 rounded-full transition-all duration-200"
            :class="{
                'bg-blue-500/20 text-blue-600 ring-1 ring-blue-500/40 dark:bg-blue-500/30 dark:text-blue-400 dark:ring-blue-500/50': showInfoPanel,
                'bg-slate-300/50 text-slate-500 hover:bg-slate-300 hover:text-slate-700 dark:bg-white/5 dark:text-white/40 dark:hover:bg-white/10 dark:hover:text-white/60': !showInfoPanel
            }"
            title="Connection info"
        >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
            </svg>
        </button>
        <button
            type="button"
            @click="handleToggle()"
            class="relative flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-medium rounded-full transition-all duration-200 overflow-hidden"
            :class="{
                'bg-emerald-500/15 text-emerald-600 border border-emerald-500/40 hover:bg-emerald-500/25 dark:bg-emerald-500/20 dark:text-emerald-400 dark:border-emerald-500/30 dark:hover:bg-emerald-500/30': !isConnected && !cooldownActive,
                'bg-red-500/10 text-red-600 border border-red-500/40 hover:bg-red-500/20 dark:text-red-400 dark:border-red-500/30': isConnected && !cooldownActive,
                'text-red-600 border border-red-500/40 dark:text-red-400 dark:border-red-500/30': isConnected && cooldownActive,
                'text-emerald-600 border border-emerald-500/40 dark:text-emerald-400 dark:border-emerald-500/30': !isConnected && cooldownActive
            }"
            :title="cooldownActive ? 'Please wait...' : (isConnected ? 'Disconnect terminal' : 'Connect terminal')"
        >
            {{-- Cooldown fill background --}}
            <div
                x-show="cooldownActive"
                class="absolute inset-0 rounded-full"
                :class="isConnected ? 'bg-red-500/40' : 'bg-emerald-500/40'"
                :style="'width: ' + cooldownProgress + '%'"
            ></div>
            {{-- Icon --}}
            <svg
                x-show="!isConnected"
                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                class="relative z-10 w-3.5 h-3.5 shrink-0"
            >
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM6.75 9.25a.75.75 0 000 1.5h4.59l-2.1 1.95a.75.75 0 001.02 1.1l3.5-3.25a.75.75 0 000-1.1l-3.5-3.25a.75.75 0 10-1.02 1.1l2.1 1.95H6.75z" clip-rule="evenodd" />
            </svg>
            <svg
                x-show="isConnected"
                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                class="relative z-10 w-3.5 h-3.5 shrink-0"
            >
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
            </svg>
            {{-- Text --}}
            <span x-show="!isConnected" class="relative z-10">Connect</span>
            <span x-show="isConnected" class="relative z-10">Disconnect</span>
        </button>
    </div>
</div>
