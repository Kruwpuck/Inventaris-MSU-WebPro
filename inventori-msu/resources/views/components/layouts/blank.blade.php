<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Page Title' }}</title>
    <link rel="icon" href="{{ asset('aset/loogoo.png') }}" type="image/x-icon">
    @stack('styles')
    @livewireStyles
</head>

<body>
    {{ $slot }}

    @livewireScripts
    @stack('scripts')
</body>

</html>