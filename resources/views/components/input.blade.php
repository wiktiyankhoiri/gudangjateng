@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'uppercase' => false,
    'class' => '',
])

@php
    $inputClass = 'dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 ' . $class;
@endphp

<div>
    @if($label)
        <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            {{ $label }}
        </label>
    @endif
    <input
        id="{{ $name }}"
        type="{{ $type }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        class="{{ $inputClass }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $uppercase ? 'oninput=const s=this.selectionStart;this.value=this.value.toUpperCase();this.setSelectionRange(s,s)' : '' }}
        {{ $attributes->except(['class', 'value', 'label']) }}
    >
    @error($name)
        <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
    @enderror
</div>
