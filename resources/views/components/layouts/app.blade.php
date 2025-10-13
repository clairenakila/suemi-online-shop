<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Suemi Online Shop' }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Livewire Styles (required for Filament) -->
    @livewireStyles
</head>
<body>
    {{ $slot }}

 <script>
document.addEventListener('open-new-tab', event => {
    window.open(event.detail.url, '_blank');
});
</script>

    <!-- Livewire Scripts (required for Filament) -->
    @livewireScripts
</body>
</html>
