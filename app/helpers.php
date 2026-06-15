<?php

if (!function_exists('esc')) {
    /**
     * Escape HTML (CI4 compatibility alias for Laravel's e())
     */
    function esc($value): string
    {
        return e($value);
    }
}

if (!function_exists('satuan_options')) {
    function satuan_options(): array
    {
        return ['PCS' => 'PCS', 'SET' => 'SET'];
    }
}

if (!function_exists('field_error_class')) {
    function field_error_class(string $field): string
    {
        // Cek validation errors
        $errors = session()->get('errors');
        if ($errors && $errors->has($field)) {
            return 'border-error-500 focus:border-error-500 focus:ring-error-500/10';
        }
        // Cek business logic field errors (dari mapFieldError)
        $fieldErrors = session('field_errors');
        if (isset($fieldErrors[$field])) {
            return 'border-error-500 focus:border-error-500 focus:ring-error-500/10';
        }
        return '';
    }
}

if (!function_exists('field_error_msg')) {
    function field_error_msg(string $field): string
    {
        // Cek validation errors
        $errors = session()->get('errors');
        if ($errors && $errors->has($field)) {
            return e($errors->first($field));
        }
        // Cek business logic field errors (dari mapFieldError)
        $fieldErrors = session('field_errors');
        if (isset($fieldErrors[$field])) {
            return e($fieldErrors[$field]);
        }
        return '';
    }
}
