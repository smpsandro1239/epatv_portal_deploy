<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Portal de Empregos EPATV</title>
    <meta name="description" content="Portal de Empregos da Escola Profissional Amar Terra Verde">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        secondary: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            200: '#99f6e4',
                            300: '#5eead4',
                            400: '#2dd4bf',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a',
                        },
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @yield('styles')
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-primary-700 text-white shadow-md">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <img src="{{ asset('images/logo.png') }}" alt="EPATV Logo" class="h-10">
                <span class="font-bold text-xl">Portal de Empregos</span>
            </a>
            
            <div class="hidden md:flex items-center space-x-6">
                <a href="{{ route('jobs.index') }}" class="hover:text-primary-200 transition">Ofertas</a>
                <a href="{{ route('companies.index') }}" class="hover:text-primary-200 transition">Empresas</a>
                
                @auth
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center space-x-1 hover:text-primary-200 transition">
                            <span>{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" x-cloak
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                            
                            @if(Auth::user()->isSuperAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-gray-800 hover:bg-primary-50">
                                    <i class="fas fa-tachometer-alt w-5 text-primary-600"></i> Dashboard
                                </a>
                                <a href="{{ route('admin.users') }}" class="block px-4 py-2 text-gray-800 hover:bg-primary-50">
                                    <i class="fas fa-users w-5 text-primary-600"></i> Utilizadores
                                </a>
                                <a href="{{ route('admin.registration-windows') }}" class="block px-4 py-2 text-gray-800 hover:bg-primary-50">
                                    <i class="fas fa-calendar-alt w-5 text-primary-600"></i> Janelas de Registo
                                </a>
                            @elseif(Auth::user()->isCompany())
                                <a href="{{ route('company.dashboard') }}" class="block px-4 py-2 text-gray-800 hover:bg-primary-50">
                                    <i class="fas fa-tachometer-alt w-5 text-primary-600"></i> Dashboard
                                </a>
                                <a href="{{ route('company.jobs') }}" class="block px-4 py-2 text-gray-800 hover:bg-primary-50">
                                    <i class="fas fa-briefcase w-5 text-primary-600"></i> Minhas Ofertas
                                </a>
                                <a href="{{ route('company.applications') }}" class="block px-4 py-2 text-gray-800 hover:bg-primary-50">
                                    <i class="fas fa-file-alt w-5 text-primary-600"></i> Candidaturas
                                </a>
                                <a href="{{ route('company.profile') }}" class="block px-4 py-2 text-gray-800 hover:bg-primary-50">
                                    <i class="fas fa-building w-5 text-primary-600"></i> Perfil da Empresa
                                </a>
                            @else
                                <a href="{{ route('student.dashboard') }}" class="block px-4 py-2 text-gray-800 hover:bg-primary-50">
                                    <i class="fas fa-tachometer-alt w-5 text-primary-600"></i> Dashboard
                                </a>
                                <a href="{{ route('student.applications') }}" class="block px-4 py-2 text-gray-800 hover:bg-primary-50">
                                    <i class="fas fa-file-alt w-5 text-primary-600"></i> Minhas Candidaturas
                                </a>
                                <a href="{{ route('student.saved-jobs') }}" class="block px-4 py-2 text-gray-800 hover:bg-primary-50">
                                    <i class="fas fa-bookmark w-5 text-primary-600"></i> Ofertas Guardadas
                                </a>
                                <a href="{{ route('student.profile') }}" class="block px-4 py-2 text-gray-800 hover:bg-primary-50">
                                    <i class="fas fa-user w-5 text-primary-600"></i> Meu Perfil
                                </a>
                            @endif
                            
                            <hr class="my-1">
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-gray-800 hover:bg-primary-50">
                                    <i class="fas fa-sign-out-alt w-5 text-primary-600"></i> Sair
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hover:text-primary-200 transition">Entrar</a>
                    <a href="{{ route('register') }}" class="bg-white text-primary-700 px-4 py-2 rounded-md hover:bg-primary-100 transition">Registar</a>
                @endauth
            </div>
            
            <!-- Mobile menu button -->
            <div class="md:hidden" x-data="{ open: false }">
                <button @click="open = !open" class="text-white focus:outline-none">
                    <i x-show="!open" class="fas fa-bars text-xl"></i>
                    <i x-show="open" x-cloak class="fas fa-times text-xl"></i>
                </button>
                
                <!-- Mobile menu -->
                <div x-show="open" x-cloak class="absolute top-16 right-0 left-0 bg-primary-700 z-50 shadow-md">
                    <div class="container mx-auto px-4 py-3 flex flex-col space-y-3">
                        <a href="{{ route('jobs.index') }}" class="hover:text-primary-200 transition py-2">Ofertas</a>
                        <a href="{{ route('companies.index') }}" class="hover:text-primary-200 transition py-2">Empresas</a>
                        
                        @auth
                            <hr class="border-primary-600">
                            
                            @if(Auth::user()->isSuperAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-200 transition py-2">
                                    <i class="fas fa-tachometer-alt w-5"></i> Dashboard
                                </a>
                                <a href="{{ route('admin.users') }}" class="hover:text-primary-200 transition py-2">
                                    <i class="fas fa-users w-5"></i> Utilizadores
                                </a>
                                <a href="{{ route('admin.registration-windows') }}" class="hover:text-primary-200 transition py-2">
                                    <i class="fas fa-calendar-alt w-5"></i> Janelas de Registo
                                </a>
                            @elseif(Auth::user()->isCompany())
                                <a href="{{ route('company.dashboard') }}" class="hover:text-primary-200 transition py-2">
                                    <i class="fas fa-tachometer-alt w-5"></i> Dashboard
                                </a>
                                <a href="{{ route('company.jobs') }}" class="hover:text-primary-200 transition py-2">
                                    <i class="fas fa-briefcase w-5"></i> Minhas Ofertas
                                </a>
                                <a href="{{ route('company.applications') }}" class="hover:text-primary-200 transition py-2">
                                    <i class="fas fa-file-alt w-5"></i> Candidaturas
                                </a>
                                <a href="{{ route('company.profile') }}" class="hover:text-primary-200 transition py-2">
                                    <i class="fas fa-building w-5"></i> Perfil da Empresa
                                </a>
                            @else
                                <a href="{{ route('student.dashboard') }}" class="hover:text-primary-200 transition py-2">
                                    <i class="fas fa-tachometer-alt w-5"></i> Dashboard
                                </a>
                                <a href="{{ route('student.applications') }}" class="hover:text-primary-200 transition py-2">
                                    <i class="fas fa-file-alt w-5"></i> Minhas Candidaturas
                                </a>
                                <a href="{{ route('student.saved-jobs') }}" class="hover:text-primary-200 transition py-2">
                                    <i class="fas fa-bookmark w-5"></i> Ofertas Guardadas
                                </a>
                                <a href="{{ route('student.profile') }}" class="hover:text-primary-200 transition py-2">
                                    <i class="fas fa-user w-5"></i> Meu Perfil
                                </a>
                            @endif
                            
                            <hr class="border-primary-600">
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left hover:text-primary-200 transition py-2">
                                    <i class="fas fa-sign-out-alt w-5"></i> Sair
                                </button>
                            </form>
                        @else
                            <hr class="border-primary-600">
                            <a href="{{ route('login') }}" class="hover:text-primary-200 transition py-2">Entrar</a>
                            <a href="{{ route('register') }}" class="bg-white text-primary-700 px-4 py-2 rounded-md hover:bg-primary-100 transition text-center">Registar</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">Portal de Empregos EPATV</h3>
                    <p class="text-gray-300">Conectando ex-alunos e empresas para oportunidades profissionais de sucesso.</p>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Links Úteis</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}" class="text-gray-300 hover:text-white transition">Início</a></li>
                        <li><a href="{{ route('jobs.index') }}" class="text-gray-300 hover:text-white transition">Ofertas de Emprego</a></li>
                        <li><a href="{{ route('companies.index') }}" class="text-gray-300 hover:text-white transition">Empresas</a></li>
                        <li><a href="https://www.epatv.pt" target="_blank" class="text-gray-300 hover:text-white transition">Site EPATV</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contactos</h3>
                    <ul class="space-y-2 text-gray-300">
                        <li><i class="fas fa-map-marker-alt w-5"></i> Praça das Comunidades Geminadas, 1, 4731-909 Vila Verde</li>
                        <li><i class="fas fa-phone w-5"></i> 253 322 016</li>
                        <li><i class="fas fa-envelope w-5"></i> geral@epatv.pt</li>
                    </ul>
                </div>
            </div>
            
            <div class="mt-8 pt-6 border-t border-gray-700 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} Escola Profissional Amar Terra Verde. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Notification System -->
    <div x-data="{ notifications: [] }" 
         @notification.window="notifications.push($event.detail); setTimeout(() => { notifications.shift() }, 5000)"
         class="fixed bottom-4 right-4 z-50 space-y-4">
        <template x-for="(notification, index) in notifications" :key="index">
            <div x-show="notification" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-x-8"
                 x-transition:enter-end="opacity-100 transform translate-x-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 transform translate-x-0"
                 x-transition:leave-end="opacity-0 transform translate-x-8"
                 class="bg-white rounded-lg shadow-lg p-4 max-w-sm flex items-start"
                 :class="{
                    'border-l-4 border-green-500': notification.type === 'success',
                    'border-l-4 border-red-500': notification.type === 'error',
                    'border-l-4 border-blue-500': notification.type === 'info',
                    'border-l-4 border-yellow-500': notification.type === 'warning'
                 }">
                <div class="mr-3 text-xl" :class="{
                    'text-green-500': notification.type === 'success',
                    'text-red-500': notification.type === 'error',
                    'text-blue-500': notification.type === 'info',
                    'text-yellow-500': notification.type === 'warning'
                }">
                    <i class="fas" :class="{
                        'fa-check-circle': notification.type === 'success',
                        'fa-times-circle': notification.type === 'error',
                        'fa-info-circle': notification.type === 'info',
                        'fa-exclamation-circle': notification.type === 'warning'
                    }"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-800" x-text="notification.title"></h4>
                    <p class="text-gray-600" x-text="notification.message"></p>
                </div>
                <button @click="notifications = notifications.filter((_, i) => i !== index)" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </template>
    </div>

    <!-- Scripts -->
    <script>
        function showNotification(type, title, message) {
            window.dispatchEvent(new CustomEvent('notification', {
                detail: { type, title, message }
            }));
        }
    </script>
    
    @yield('scripts')
</body>
</html>
