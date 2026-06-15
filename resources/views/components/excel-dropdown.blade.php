@props([
    'templateRoute' => null,
    'exportRoute' => null,
    'importRoute' => null,
])

<div x-data="{ dropdownOpen: false }" class="relative">
    <button @click="dropdownOpen = !dropdownOpen" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-gray-800">
        <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M2.5 5C2.5 4.58579 2.83579 4.25 3.25 4.25H16.75C17.1642 4.25 17.5 4.58579 17.5 5C17.5 5.41421 17.1642 5.75 16.75 5.75H3.25C2.83579 5.75 2.5 5.41421 2.5 5ZM4.75 10C4.75 9.58579 5.08579 9.25 5.5 9.25H14.5C14.9142 9.25 15.25 9.58579 15.25 10C15.25 10.4142 14.9142 10.75 14.5 10.75H5.5C5.08579 10.75 4.75 10.4142 4.75 10ZM7.5 14.75C7.08579 14.75 6.75 15.0858 6.75 15.5C6.75 15.9142 7.08579 16.25 7.5 16.25H12.5C12.9142 16.25 13.25 15.9142 13.25 15.5C13.25 15.0858 12.9142 14.75 12.5 14.75H7.5Z" fill="currentColor"/>
        </svg>
        Excel
        <svg x-bind:class="dropdownOpen && 'rotate-180'" width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>
    <div x-show="dropdownOpen" @click.outside="dropdownOpen = false" class="absolute right-0 z-50 mt-1 w-56 rounded-lg border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-800 dark:bg-gray-900">
        @if($templateRoute)
            <a href="{{ $templateRoute }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 3.75V16.25M16.25 10H3.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                Template Impor
            </a>
        @endif
        @if($exportRoute)
            <a href="{{ $exportRoute }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 3.125C10.4142 3.125 10.75 3.46079 10.75 3.875V12.4822L13.6161 9.61612C13.909 9.32322 14.3839 9.32322 14.6768 9.61612C14.9697 9.90901 14.9697 10.3839 14.6768 10.6768L10.6768 14.6768C10.3839 14.9697 9.90901 14.9697 9.61612 14.6768L5.61612 10.6768C5.32322 10.3839 5.32322 9.90901 5.61612 9.61612C5.90901 9.32322 6.38388 9.32322 6.67678 9.61612L9.25 12.1893V3.875C9.25 3.46079 9.58579 3.125 10 3.125ZM4.5 11C4.91421 11 5.25 11.3358 5.25 11.75V15C5.25 15.6904 5.80964 16.25 6.5 16.25H13.5C14.1904 16.25 14.75 15.6904 14.75 15V11.75C14.75 11.3358 15.0858 11 15.5 11C15.9142 11 16.25 11.3358 16.25 11.75V15C16.25 16.5188 15.0188 17.75 13.5 17.75H6.5C4.98122 17.75 3.75 16.5188 3.75 15V11.75C3.75 11.3358 4.08579 11 4.5 11Z" fill="currentColor"/></svg>
                Ekspor Data
            </a>
        @endif
        @if($templateRoute || $exportRoute)
            <hr class="my-1 border-gray-200 dark:border-gray-800">
        @endif
        @if($importRoute)
            <a href="#" @click.prevent="$dispatch('open-import')" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 16.875C9.58579 16.875 9.25 16.5392 9.25 16.125V7.51777L6.38388 10.3839C6.09099 10.6768 5.61612 10.6768 5.32322 10.3839C5.03033 10.091 5.03033 9.61612 5.32322 9.32322L9.32322 5.32322C9.61612 5.03033 10.091 5.03033 10.3839 5.32322L14.3839 9.32322C14.6768 9.61612 14.6768 10.091 14.3839 10.3839C14.091 10.6768 13.6161 10.6768 13.3232 10.3839L10.75 7.81066V16.125C10.75 16.5392 10.4142 16.875 10 16.875ZM4.5 9C4.91421 9 5.25 8.66421 5.25 8.25V5C5.25 4.30964 5.80964 3.75 6.5 3.75H13.5C14.1904 3.75 14.75 4.30964 14.75 5V8.25C14.75 8.66421 15.0858 9 15.5 9C15.9142 9 16.25 8.66421 16.25 8.25V5C16.25 3.48122 15.0188 2.25 13.5 2.25H6.5C4.98122 2.25 3.75 3.48122 3.75 5V8.25C3.75 8.66421 4.08579 9 4.5 9Z" fill="currentColor"/></svg>
                Impor Data
            </a>
        @endif
    </div>
</div>
