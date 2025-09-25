<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PT Sahabat Agro Group - Sistem Panen Sawit Digital')</title>
    
    <!-- Initialize theme early to avoid flash (default to light) -->
    <script>
        (function() {
            // Force light theme globally (disable dark mode)
            try {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } catch (e) {}
        })();
    </script>
    <!-- Tailwind CSS (force light unless .dark class is set) -->
    <script>
        window.tailwind = window.tailwind || {};
        window.tailwind.config = Object.assign({}, window.tailwind.config || {}, { darkMode: 'class' });
    </script>
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
        /* DataTables dark-mode readability */
        .dark .dataTables_wrapper,
        .dark .dataTables_wrapper .dataTables_info,
        .dark .dataTables_wrapper .dataTables_paginate a,
        .dark .dataTables_wrapper .dataTables_paginate .paginate_button,
        .dark table.dataTable thead th,
        .dark table.dataTable tbody td,
        .dark .dataTables_wrapper .dataTables_filter label,
        .dark .dataTables_wrapper .dataTables_length label { color: #e5e7eb; }
        .dark .dataTables_wrapper .dataTables_filter input,
        .dark .dataTables_wrapper .dataTables_length select {
            color: #e5e7eb;
            background-color: #111827;
            border-color: #374151;
        }
        .dark .dt-button {
            color: #e5e7eb !important;
            background-color: #1f2937 !important;
            border-color: #374151 !important;
        }
        .dark .dt-button:hover { background-color: #374151 !important; }
        /* KPI filter form inputs */
        .dark form select, .dark form input[type=date], .dark form input[type=text], .dark form input[type=number] {
            background-color: #1f2937 !important;
            color: #f3f4f6 !important;
            border-color: #374151 !important;
        }
        .dark form label { color: #d1d5db !important; }
        /* Chart container headings */
        .dark h2, .dark h3, .dark h4 { color: #f3f4f6; }
        /* Tables */
        .dark table thead th { background-color: #1f2937; }
        .dark table tbody tr { background-color: #111827; }
        .dark table tbody tr:nth-child(even) { background-color: #1a2433; }
        .dark table tbody tr:hover { background-color: #243044; }
        /* Borders subtle */
        .dark table thead th, .dark table tbody td { border-color: #374151 !important; }
        /* Force canvas parent bg to match to avoid transparency dim look */
        .dark canvas { background-color: #111827; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 transition-colors duration-200" x-data="{ 
    sidebarOpen: false,
    sidebarHover: false
}">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="sidebar-transition fixed inset-y-0 left-0 z-50 flex flex-col bg-white dark:bg-gray-800 shadow-lg"
             :class="sidebarOpen || sidebarHover ? 'w-64' : 'w-16'"
             @mouseenter="sidebarHover = true"
             @mouseleave="sidebarHover = false">
            
                        <!-- Logo -->
                    <div class="flex items-center h-16 bg-transparent"
              :class="(sidebarOpen || sidebarHover) ? 'justify-start px-4' : 'justify-center px-0'">
             <div class="flex items-center w-full"
                 :class="(sidebarOpen || sidebarHover) ? 'space-x-3' : 'space-x-0 justify-center'">
                <img src="{{ asset('images/logo-PTSAG.png') }}" 
                    alt="PT Sahabat Agro Group" 
                    class="h-8 w-auto object-contain shrink-0 mx-auto">
                <div x-show="sidebarOpen || sidebarHover" x-transition.opacity.duration.200ms
                    class="whitespace-nowrap">
                                        <span class="text-gray-800 dark:text-gray-200 font-bold text-sm block leading-tight">PT Sahabat Agro Group</span>
                                        <span class="text-gray-500 dark:text-gray-400 text-xs block leading-tight">Sistem Panen</span>
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
                
                <!-- KPI & Analytics Menu -->
                <div x-data="{ open: {{ request()->routeIs('kpi.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="flex items-center w-full px-3 py-2 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas fa-chart-pie w-5 h-5"></i>
                        <span class="ml-3 transition-opacity duration-300"
                              :class="(sidebarOpen || sidebarHover) ? 'opacity-100' : 'opacity-0'">
                            KPI & Analytics
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
                        <a href="{{ route('kpi.index') }}" 
                           class="flex items-center px-3 py-2 text-sm text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 {{ request()->routeIs('kpi.index') ? 'bg-green-50 dark:bg-green-900 text-green-600 dark:text-green-200' : '' }}">
                            <i class="fas fa-list w-4 h-4"></i>
                            <span class="ml-2">Overview</span>
                        </a>
                        <a href="{{ route('kpi.rekonsiliasi') }}" 
                           class="flex items-center px-3 py-2 text-sm text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 {{ request()->routeIs('kpi.rekonsiliasi') ? 'bg-green-50 dark:bg-green-900 text-green-600 dark:text-green-200' : '' }}">
                            <i class="fas fa-balance-scale w-4 h-4"></i>
                            <span class="ml-2">Rekonsiliasi</span>
                        </a>
                        <a href="{{ route('kpi.restan') }}" 
                           class="flex items-center px-3 py-2 text-sm text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 {{ request()->routeIs('kpi.restan') ? 'bg-green-50 dark:bg-green-900 text-green-600 dark:text-green-200' : '' }}">
                            <i class="fas fa-exclamation-triangle w-4 h-4"></i>
                            <span class="ml-2">Restan</span>
                        </a>
                        <a href="{{ route('kpi.budget') }}" 
                           class="flex items-center px-3 py-2 text-sm text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 {{ request()->routeIs('kpi.budget') ? 'bg-green-50 dark:bg-green-900 text-green-600 dark:text-green-200' : '' }}">
                            <i class="fas fa-wallet w-4 h-4"></i>
                            <span class="ml-2">Budget</span>
                        </a>
                        <a href="{{ route('kpi.produktifitas') }}" 
                           class="flex items-center px-3 py-2 text-sm text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 {{ request()->routeIs('kpi.produktifitas') ? 'bg-green-50 dark:bg-green-900 text-green-600 dark:text-green-200' : '' }}">
                            <i class="fas fa-user-check w-4 h-4"></i>
                            <span class="ml-2">Produktivitas</span>
                        </a>
                        <a href="{{ route('kpi.quality') }}" 
                           class="flex items-center px-3 py-2 text-sm text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 {{ request()->routeIs('kpi.quality') ? 'bg-green-50 dark:bg-green-900 text-green-600 dark:text-green-200' : '' }}">
                            <i class="fas fa-gem w-4 h-4"></i>
                            <span class="ml-2">Quality</span>
                        </a>
                        <a href="{{ route('kpi.anomali') }}" 
                           class="flex items-center px-3 py-2 text-sm text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 {{ request()->routeIs('kpi.anomali') ? 'bg-green-50 dark:bg-green-900 text-green-600 dark:text-green-200' : '' }}">
                            <i class="fas fa-bug w-4 h-4"></i>
                            <span class="ml-2">Anomali</span>
                        </a>
                        <a href="{{ route('kpi.summary') }}" 
                           class="flex items-center px-3 py-2 text-sm text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 {{ request()->routeIs('kpi.summary') ? 'bg-green-50 dark:bg-green-900 text-green-600 dark:text-green-200' : '' }}">
                            <i class="fas fa-clipboard-check w-4 h-4"></i>
                            <span class="ml-2">Ringkasan</span>
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
                        @if (config('app.show_legacy'))
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
                        @endif
                        
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
                        <!-- Theme Toggle (disabled) -->
                        <button id="themeToggle" type="button" class="hidden p-2 rounded-lg border border-gray-200" aria-label="Toggle theme" tabindex="-1">
                            <i class="fas fa-moon"></i>
                        </button>
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
                                <form method="POST" action="{{ route('logout', [], false) }}">
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
    <script>
        (function() {
            // Always apply light theme defaults to Chart.js
            window.__charts = window.__charts || [];
            function applyLightTheme(){
                if (typeof Chart === 'undefined') return;
                const axisColor = '#374151';
                const gridColor = 'rgba(0,0,0,.1)';
                const tooltipBg = 'rgba(255,255,255,0.95)';
                const tooltipBorder = '#e5e7eb';
                const fontFamily = "'Inter', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Liberation Sans', sans-serif";
                const fontSize = 12;
                Chart.defaults.color = axisColor;
                Chart.defaults.borderColor = gridColor;
                Chart.defaults.font = Object.assign({}, Chart.defaults.font || {}, { family: fontFamily, size: fontSize, weight: 'normal' });
                Chart.defaults.plugins = Chart.defaults.plugins || {};
                Chart.defaults.plugins.tooltip = Object.assign({}, Chart.defaults.plugins.tooltip || {}, {
                    backgroundColor: tooltipBg,
                    titleColor: axisColor,
                    bodyColor: axisColor,
                    borderColor: tooltipBorder,
                    borderWidth: 1
                });
                Chart.defaults.plugins.legend = Object.assign({}, Chart.defaults.plugins.legend || {}, {
                    labels: Object.assign({}, (Chart.defaults.plugins.legend || {}).labels || {}, { color: axisColor, font: { family: fontFamily, size: fontSize } })
                });
                (window.__charts||[]).forEach(ch => {
                    const scales = ch.options.scales || {};
                    Object.keys(scales).forEach(k => {
                        if (!scales[k]) return;
                        if (!scales[k].ticks) scales[k].ticks = {};
                        if (!scales[k].grid) scales[k].grid = {};
                        scales[k].ticks.color = axisColor;
                        scales[k].ticks.font = Object.assign({}, scales[k].ticks.font || {}, { family: fontFamily, size: fontSize });
                        scales[k].grid.color = gridColor;
                    });
                    if (ch.options.plugins && ch.options.plugins.legend && ch.options.plugins.legend.labels){
                        ch.options.plugins.legend.labels.color = axisColor;
                        ch.options.plugins.legend.labels.font = Object.assign({}, ch.options.plugins.legend.labels.font || {}, { family: fontFamily, size: fontSize });
                    }
                    ch.options.plugins = ch.options.plugins || {};
                    ch.options.plugins.tooltip = Object.assign({}, ch.options.plugins.tooltip || {}, {
                        backgroundColor: tooltipBg,
                        titleColor: axisColor,
                        bodyColor: axisColor,
                        borderColor: tooltipBorder,
                        borderWidth: 1,
                        titleFont: Object.assign({}, (ch.options.plugins.tooltip || {}).titleFont || {}, { family: fontFamily, size: fontSize + 1 }),
                        bodyFont: Object.assign({}, (ch.options.plugins.tooltip || {}).bodyFont || {}, { family: fontFamily, size: fontSize })
                    });
                    ch.options.font = Object.assign({}, ch.options.font || {}, { family: fontFamily, size: fontSize });
                    ch.update('none');
                });
            }
            // Helper: create chart when canvas is present and visible. Retries few times.
            function isVisible(el){
                if (!el) return false;
                const style = getComputedStyle(el);
                if (style.display === 'none' || style.visibility === 'hidden') return false;
                const rect = el.getBoundingClientRect();
                return rect.width > 0 && rect.height > 0;
            }
            function createChartWhenReady(target, config, tries=12){
                const el = (typeof target === 'string') ? document.getElementById(target) : target;
                if (!el || typeof Chart === 'undefined') {
                    if (tries <= 0) return null;
                    return setTimeout(() => createChartWhenReady(target, config, tries-1), 150);
                }
                if (!isVisible(el)){
                    if (tries <= 0) return null;
                    return setTimeout(() => createChartWhenReady(target, config, tries-1), 150);
                }
                try {
                    const ch = new Chart(el, config);
                    window.__charts.push(ch);
                    applyLightTheme();
                    // Observe size changes to keep chart responsive
                    try {
                        const ro = new ResizeObserver(() => { try { ch.resize(); } catch(e){} });
                        ro.observe(el.parentElement || el);
                    } catch(e) {}
                    return ch;
                } catch(e) {
                    if (tries <= 0) return null;
                    return setTimeout(() => createChartWhenReady(target, config, tries-1), 150);
                }
            }
            // Expose helpers
            window.__registerChart = function(ch){ window.__charts.push(ch); applyLightTheme(); };
            window.__makeChart = createChartWhenReady;
            // Initial theme apply and keep charts fresh on resize/visibility
            document.addEventListener('DOMContentLoaded', () => setTimeout(applyLightTheme, 300));
            window.addEventListener('resize', () => { (window.__charts||[]).forEach(ch => { try { ch.resize(); } catch(e){} }); });
            document.addEventListener('visibilitychange', () => { if (!document.hidden) { (window.__charts||[]).forEach(ch => { try { ch.resize(); } catch(e){} }); applyLightTheme(); } });
        })();
    </script>
</body>
</html>
