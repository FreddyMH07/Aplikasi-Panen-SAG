<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PT Sahabat Agro Group - Sistem Panen Sawit Digital')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.tailwindcss.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.tailwindcss.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/colreorder/1.7.0/css/colReorder.tailwindcss.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.tailwindcss.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.tailwindcss.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.tailwindcss.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/colreorder/1.7.0/js/dataTables.colReorder.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Minimal inline styles for critical rendering */
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-200" x-data="{ 
    sidebarOpen: false,
    sidebarHover: false
}">
">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="sidebar-transition fixed inset-y-0 left-0 z-50 flex flex-col bg-white dark:bg-gray-800 shadow-lg"
             :class="sidebarOpen || sidebarHover ? 'w-64' : 'w-16'"
             @mouseenter="sidebarHover = true"
             @mouseleave="sidebarHover = false">
            
            <!-- Logo -->
            <div class="flex items-center justify-center h-16 px-4 bg-green-600 dark:bg-green-700">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo-PTSAG.png') }}" 
                         alt="PT SAG" 
                         class="h-8 w-auto">
                    <div class="transition-opacity duration-300"
                         :class="(sidebarOpen || sidebarHover) ? 'opacity-100' : 'opacity-0'">
                        <span class="text-white font-bold text-sm block">PT SAG</span>
                        <span class="text-green-200 text-xs block">Sistem Panen</span>
                    </div>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 px-2 py-4 space-y-2 overflow-y-auto">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center px-3 py-2 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-200' : '' }}">
                    <i class="fas fa-tachometer-alt w-5 h-5"></i>
                    <span class="ml-3 transition-opacity duration-300"
                          :class="(sidebarOpen || sidebarHover) ? 'opacity-100' : 'opacity-0'">
                        Dashboard
                    </span>
                </a>
                
                <!-- Panen Menu -->
                <div x-data="{ open: {{ request()->routeIs('panen-*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="flex items-center w-full px-3 py-2 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas fa-chart-line w-5 h-5"></i>
                        <span class="ml-3 transition-opacity duration-300"
                              :class="(sidebarOpen || sidebarHover) ? 'opacity-100' : 'opacity-0'">
                            Panen
                        </span>
                        <i class="fas fa-chevron-down ml-auto transition-transform duration-200"
                           :class="open ? 'rotate-180' : ''"
                           x-show="sidebarOpen || sidebarHover"></i>
                    </button>
                    
                    <div x-show="open && (sidebarOpen || sidebarHover)" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         class="ml-6 mt-2 space-y-1">
                        <a href="{{ route('panen-harian.index') }}" 
                           class="flex items-center px-3 py-2 text-sm text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 {{ request()->routeIs('panen-harian.*') ? 'bg-green-50 dark:bg-green-900 text-green-600 dark:text-green-200' : '' }}">
                            <i class="fas fa-calendar-day w-4 h-4"></i>
                            <span class="ml-2">Report Harian</span>
                        </a>
                        <a href="{{ route('panen-bulanan.index') }}" 
                           class="flex items-center px-3 py-2 text-sm text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 {{ request()->routeIs('panen-bulanan.*') ? 'bg-green-50 dark:bg-green-900 text-green-600 dark:text-green-200' : '' }}">
                            <i class="fas fa-calendar-alt w-4 h-4"></i>
                            <span class="ml-2">Report Bulanan</span>
                        </a>
                    </div>
                </div>
                
                <!-- Master Data Menu -->
                <div x-data="{ open: {{ request()->routeIs('master.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="flex items-center w-full px-3 py-2 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas fa-database w-5 h-5"></i>
                        <span class="ml-3 transition-opacity duration-300"
                              :class="(sidebarOpen || sidebarHover) ? 'opacity-100' : 'opacity-0'">
                            Master Data
                        </span>
                        <i class="fas fa-chevron-down ml-auto transition-transform duration-200"
                           :class="open ? 'rotate-180' : ''"
                           x-show="sidebarOpen || sidebarHover"></i>
                    </button>
                    
                    <div x-show="open && (sidebarOpen || sidebarHover)" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         class="ml-6 mt-2 space-y-1">
                        <a href="{{ route('master.master-data.index') }}" 
                           class="flex items-center px-3 py-2 text-sm text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 {{ request()->routeIs('master.master-data.*') ? 'bg-green-50 dark:bg-green-900 text-green-600 dark:text-green-200' : '' }}">
                            <i class="fas fa-table w-4 h-4"></i>
                            <span class="ml-2">Data Master</span>
                        </a>
                        <a href="{{ route('master.kebun.index') }}" 
                           class="flex items-center px-3 py-2 text-sm text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 {{ request()->routeIs('master.kebun.*') ? 'bg-green-50 dark:bg-green-900 text-green-600 dark:text-green-200' : '' }}">
                            <i class="fas fa-map w-4 h-4"></i>
                            <span class="ml-2">Kebun (Legacy)</span>
                        </a>
                        <a href="{{ route('master.divisi.index') }}" 
                           class="flex items-center px-3 py-2 text-sm text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 {{ request()->routeIs('master.divisi.*') ? 'bg-green-50 dark:bg-green-900 text-green-600 dark:text-green-200' : '' }}">
                            <i class="fas fa-sitemap w-4 h-4"></i>
                            <span class="ml-2">Divisi (Legacy)</span>
                        </a>
                    </div>
                </div>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden transition-all duration-300"
             :class="sidebarOpen || sidebarHover ? 'ml-64' : 'ml-16'">
            
            <!-- Header -->
            <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center space-x-4">
                        <button @click="sidebarOpen = !sidebarOpen" 
                                class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                            @yield('page-title', 'Dashboard')
                        </h1>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="flex items-center space-x-2 text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-gray-100 focus:outline-none">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-medium">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </span>
                                </div>
                                <span class="hidden md:block">{{ auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform scale-95"
                                 x-transition:enter-end="opacity-100 transform scale-100"
                                 class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-50">
                                <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700">
                                    <p class="text-sm text-gray-700 dark:text-gray-200 font-medium">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</p>
                                </div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" 
                                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                                        <i class="fas fa-sign-out-alt w-4 h-4 mr-2"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-200 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ session('success') }}
                        </div>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-200 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ session('error') }}
                        </div>
                    </div>
                @endif
                
                @yield('content')
            </main>
            
            <!-- Footer -->
            <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 px-6 py-4">
                <div class="text-center text-sm text-gray-500 dark:text-gray-400">
                    © {{ date('Y') }} PT Sahabat Agro Group - Sistem Report Panen Sawit Digital
                </div>
            </footer>
        </div>
    </div>
    
    @stack('scripts')
</body>
</html>
