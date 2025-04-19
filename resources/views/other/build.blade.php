@php
    if (!function_exists('build_version')) {
        function build_version() {
            return '?v=7'; // ← тут змінюй версію вручну або прочитай з файлу
        }
    }
@endphp