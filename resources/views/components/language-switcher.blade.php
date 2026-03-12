{{-- Language Switcher Component --}}
<div class="language-switcher relative" x-data="{ open: false }">
    <button 
        @click="open = !open" 
        @click.away="open = false"
        class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors"
    >
        <span class="text-xl">{{ $currentLocaleConfig['flag'] ?? '🌐' }}</span>
        <span class="text-sm font-medium hidden sm:inline">
            {{ $currentLocaleConfig['native'] ?? 'Language' }}
        </span>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div 
        x-show="open"
        x-transition
        class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50"
        style="display: none;"
    >
        @foreach($locales as $locale)
            <a 
                href="{{ url()->current() }}?lang={{ $locale['code'] }}"
                class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100 transition-colors {{ $currentLocale === $locale['code'] ? 'bg-gray-100' : '' }}"
            >
                <span class="text-xl">{{ $locale['flag'] }}</span>
                <div class="flex-1">
                    <div class="text-sm font-medium">{{ $locale['native'] }}</div>
                    @if($locale['code'] !== $locale['name'])
                        <div class="text-xs text-gray-500">{{ $locale['name'] }}</div>
                    @endif
                </div>
                @if($currentLocale === $locale['code'])
                    <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                @endif
            </a>
        @endforeach

        @if(config('localization.script_conversion.enabled', false) && $currentLocale === 'sr')
            <div class="border-t border-gray-200 my-2"></div>
            <div class="px-4 py-2 text-xs text-gray-500 uppercase">Script / Скрипт</div>
            <a 
                href="{{ url()->current() }}?lang=sr-cyrl"
                class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100 transition-colors"
            >
                <span class="text-lg">Ћи</span>
                <span class="text-sm">Ћирилица</span>
            </a>
            <a 
                href="{{ url()->current() }}?lang=sr"
                class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100 transition-colors"
            >
                <span class="text-lg">Lat</span>
                <span class="text-sm">Latinica</span>
            </a>
        @endif
    </div>
</div>
