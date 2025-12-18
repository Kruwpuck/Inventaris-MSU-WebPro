<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ $title ?? 'Masjid Syamsul Ulum' }}</title>
  <link rel="icon" href="{{ asset('fe-guest/loogoo.png') }}" type="image/x-icon">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Inter font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap">
  <!-- Custom CSS -->
  @stack('styles')
  @livewireStyles
</head>

<body>

  {{ $slot }}

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/main.js') }}?v=2"></script>
  @stack('scripts')
  @livewireScripts
</body>

</html>
