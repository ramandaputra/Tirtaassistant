<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TirtaAssistant</title>
    <!-- Tailwind CSS (CDN for demo purposes) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @livewireStyles
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-900">
    @livewire('chat-interface')

    @livewireScripts

</body>
</html>
