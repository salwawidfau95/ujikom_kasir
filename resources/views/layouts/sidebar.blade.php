<!DOCTYPE html>
<html lang="id">
<head>
    @stack('head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ url('tamimarket.png') }}">
    <title>@yield('title', 'TamiMarket')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="flex bg-gray-100 font-inter">
    @stack('scripts') 
@if(Auth::check())
    <!-- Sidebar -->
    <aside class="w-60 h-screen bg-white shadow-xl p-6 flex flex-col fixed">
        <!-- Logo -->
        <div class="flex items-center space-x-2 mb-10">
            <img src="{{ url('tamimarket.png') }}" alt="Logo" class="w-34 h-14 rounded-full">
            <span class="text-xl font-semibold text-gray-900"></span>
        </div>

        <!-- Menu -->
        <nav class="flex-1">
            <ul class="space-y-4 text-gray-600">

                <li> 
                    <a class="flex items-center space-x-3 p-2 hover:bg-purple-50 hover:text-purple-600 rounded-lg" href="{{ route('dashboard') }}">
                        <i data-lucide="home"></i> <span>Dashboard</span></a>
                </li>

                <li> @if (Auth::user() && Auth::user()->role == 'admin')
                    <a class="flex items-center space-x-3 p-2 hover:bg-purple-50 hover:text-purple-600 rounded-lg" href="{{ route('products.index') }}">
                        <i data-lucide="square-chart-gantt"></i> <span>Product</span></a>
                @else
                    <a class="flex items-center space-x-3 p-2 hover:bg-purple-50 hover:text-purple-600 rounded-lg" href="{{ route('products2.index2') }}">
                        <i data-lucide="square-chart-gantt"></i> <span>Product</span></a>
                @endif 
                </li>

                <li> @if (Auth::user() && Auth::user()->role == 'admin')
                    <a class="flex items-center space-x-3 p-2 hover:bg-purple-50 hover:text-purple-600 rounded-lg" href="{{ route('transactions.index') }}">
                        <i data-lucide="shopping-cart"></i> <span>Purchase</span></a>
                @else
                    <a class="flex items-center space-x-3 p-2 hover:bg-purple-50 hover:text-purple-600 rounded-lg" href="{{ route('transactions.index2') }}">
                        <i data-lucide="shopping-cart"></i> <span>Purchase</span></a>
                @endif 
                </li>

                @if (Auth::user() && Auth::user()->role == 'admin')
                <li>
                    <a href="{{ route('users.index') }}" class="flex items-center space-x-3 p-2 hover:bg-purple-50 hover:text-purple-600 rounded-lg">
                        <i data-lucide="contact"></i> <span>User</span>
                    </a>
                </li>
                @endif

            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="ml-72 p-8 w-full">
        <h1 class="text-3xl font-semibold text-gray-900">@yield('title')</h1>
        @yield('content')
    </main>

    <script>
        lucide.createIcons();
    </script>
@endif
</body>
</html>
