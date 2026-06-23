@props([
    'route' => '',
    'query' => '',
    'placeholder' => 'Cari...',
    'live' => false,
])

<form method="get" class="flex items-center gap-2"
    @if($live)
        x-data="{ q: '{{ $query }}', timer: null }"
        x-init="$watch('q', value => {
            clearTimeout(timer);
            timer = setTimeout(() => {
                if (value !== '{{ $query }}') $el.submit();
            }, 400);
        })"
    @endif
>
    <div class="relative">
        <span class="absolute top-1/2 left-0 -translate-y-1/2 border-r border-gray-200 px-3.5 py-3 text-gray-500 dark:border-gray-800 dark:text-gray-400">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M3.04175 9.37363C3.04175 5.87693 5.87711 3.04199 9.37508 3.04199C12.8731 3.04199 15.7084 5.87693 15.7084 9.37363C15.7084 12.8703 12.8731 15.7053 9.37508 15.7053C5.87711 15.7053 3.04175 12.8703 3.04175 9.37363ZM9.37508 1.54199C5.04902 1.54199 1.54175 5.04817 1.54175 9.37363C1.54175 13.6991 5.04902 17.2053 9.37508 17.2053C11.2674 17.2053 13.003 16.5344 14.357 15.4176L17.177 18.238C17.4699 18.5309 17.9448 18.5309 18.2377 18.238C18.5306 17.9451 18.5306 17.4703 18.2377 17.1774L15.418 14.3573C16.5365 13.0033 17.2084 11.2669 17.2084 9.37363C17.2084 5.04817 13.7011 1.54199 9.37508 1.54199Z" fill="currentColor"/>
            </svg>
        </span>
        <input type="text" name="q"
            @if($live)
                x-model="q"
                x-init="$el.focus()"
            @else
                value="{{ $query }}"
            @endif
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full sm:w-[280px] rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-[62px] text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
            placeholder="{{ $placeholder }}">
    </div>

    @if(!$live)
        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">Cari</button>
    @endif

    @if(!empty($query))
        <a href="{{ $route }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.641 4.887C5.934 4.594 6.409 4.594 6.702 4.887L10 8.185L13.298 4.887C13.591 4.594 14.066 4.594 14.359 4.887C14.652 5.18 14.652 5.655 14.359 5.948L11.061 9.246L14.359 12.544C14.652 12.837 14.652 13.312 14.359 13.605C14.066 13.898 13.591 13.898 13.298 13.605L10 10.307L6.702 13.605C6.409 13.898 5.934 13.898 5.641 13.605C5.348 13.312 5.348 12.837 5.641 12.544L8.939 9.246L5.641 5.948C5.348 5.655 5.348 5.18 5.641 4.887Z" fill="currentColor"/></svg>
        </a>
    @endif
</form>
