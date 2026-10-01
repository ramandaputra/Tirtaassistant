<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Knowledge Base - TirtAssistant</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-900">
    <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6">
        @livewire('knowledge-manager')
    </main>

    @livewireScripts
</body>
</html>
