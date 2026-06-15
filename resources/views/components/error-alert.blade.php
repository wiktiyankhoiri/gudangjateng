@props(['errors' => null])

@php
    $errorBag = $errors ?? $errors->any() ? $errors : null;
@endphp

@if($errorBag && $errorBag->any())
    <div class="mb-4 rounded-lg border-l-4 border-l-error-500 bg-error-50 p-4 dark:bg-error-500/15">
        <ul class="mb-0 list-disc ps-4 text-sm text-red-800 dark:text-red-200">
            @foreach ($errorBag->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
