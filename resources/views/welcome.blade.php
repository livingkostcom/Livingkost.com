<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $appName ?? 'Fluty Kos' }} - {{ $appTagline ?? 'Sistem Manajemen Kos' }}</title>

    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
    </style>

    @vite(['resources/css/app.css'])
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $appName ?? 'Fluty Kos' }}</h1>
            <p class="text-gray-600 mb-8">Sistem Manajemen Kos Modern</p>
            <a href="{{ route('login') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                Masuk ke Sistem
            </a>
        </div>
    </div>
</body>

</html>
