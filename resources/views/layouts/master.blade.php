<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Mini Cart</title>

    <!-- Google Fonts / Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Initial Cart Snapshot -->
    <script>
        const initialCartSnapshot = @json(get_cart_snapshot());
    </script>
</head>

<body x-data="miniCart" class="max-w-6xl mx-auto px-4 text-gray-950">

    {{-- Include Header Part --}}
    @include('layouts.includes.header')

    {{-- Main Content --}}
    @yield('content')

    {{-- Include Footer Part --}}
    @include('layouts.includes.footer')

</body>

</html>
