@extends('layouts.app')

@section('title', 'Página Inicial')

@section('content')
    <!-- Hero Section -->
    <section class="bg-primary-700 text-white py-16">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div>
                    <h1 class="text-4xl md:text-5xl font-bold mb-4">Portal de Empregos EPATV</h1>
                    <p class="text-xl mb-8">Conectando ex-alunos e empresas para oportunidades profissionais de sucesso.</p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('jobs.index') }}" class="bg-white text-primary-700 px-6 py-3 rounded-md font-semibold hover:bg-primary-100 transition">
                            Ver Ofertas
                        </a>
                        @guest
                            <a href="{{ route('register') }}" class="bg-transparent border-2 border-white px-6 py-3 rounded-md font-semibold hover:bg-white hover:text-primary-700 transition">
                                Registar
                            </a>
                        @endguest
                    </div>
                </div>
                <div class="hidden md:block">
                    <img src="{{ asset('images/hero-image.svg') }}" alt="EPATV Portal de Empregos" class="w-full">
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Jobs Section -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12">Ofertas em Destaque</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featuredJobs as $job)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                @if($job->company->company_logo)
                                    <img src="{{ asset('storage/' . $job->company->company_logo) }}" alt="{{ $job->company->company_name }}" class="w-12 h-12 object-contain mr-4">
                                @else
                                    <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center mr-4">
                                        <i class="fas fa-building text-gray-500 text-xl"></i>
                                    </div>
                                @endif
                                <div>
                                    <h3 class="font-semibold text-lg text-primary-700">{{ $job->title }}</h3>
                                    <p class="text-gray-600">{{ $job->company->company_name }}</p>
                                </div>
                            </div>
                            
                            <div class="mb-4 space-y-2">
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-map-marker-alt w-5"></i>
                                    <span>{{ $job->location }}</span>
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-briefcase w-5"></i>
                                    <span>{{ $job->contract_type }}</span>
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-tag w-5"></i>
                                    <span>{{ $job->category->name }}</span>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 text-sm">{{ $job->created_at->diffForHumans() }}</span>
                                <a href="{{ route('jobs.show', $job) }}" class="text-primary-600 hover:text-primary-800 font-medium">
                                    Ver Detalhes
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="text-center mt-10">
                <a href="{{ route('jobs.index') }}" class="inline-block bg-primary-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-primary-700 transition">
                    Ver Todas as Ofertas
                </a>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12">Como Funciona</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- For Students -->
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="bg-primary-100 w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-user-graduate text-primary-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Para Ex-Alunos</h3>
                    <ul class="text-gray-600 space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Registe-se como ex-aluno da EPATV</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Explore ofertas de emprego alinhadas com a sua formação</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Candidate-se diretamente através da plataforma</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Acompanhe o estado das suas candidaturas</span>
                        </li>
                    </ul>
                </div>
                
                <!-- For Companies -->
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="bg-secondary-100 w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-building text-secondary-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Para Empresas</h3>
                    <ul class="text-gray-600 space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Registe a sua empresa na plataforma</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Publique ofertas de emprego gratuitamente</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Receba candidaturas de ex-alunos qualificados</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Gerencie todo o processo de recrutamento</span>
                        </li>
                    </ul>
                </div>
                
                <!-- For EPATV -->
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="bg-primary-100 w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-school text-primary-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Para a EPATV</h3>
                    <ul class="text-gray-600 space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Acompanhe o percurso profissional dos ex-alunos</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Fortaleça parcerias com empresas da região</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Obtenha métricas sobre empregabilidade</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Promova a inserção profissional dos formandos</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-primary-700 text-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12">O Nosso Impacto</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div>
                    <div class="text-4xl font-bold mb-2">{{ $stats['students'] }}</div>
                    <div class="text-lg">Ex-Alunos Registados</div>
                </div>
                <div>
                    <div class="text-4xl font-bold mb-2">{{ $stats['companies'] }}</div>
                    <div class="text-lg">Empresas Parceiras</div>
                </div>
                <div>
                    <div class="text-4xl font-bold mb-2">{{ $stats['jobs'] }}</div>
                    <div class="text-lg">Ofertas Publicadas</div>
                </div>
                <div>
                    <div class="text-4xl font-bold mb-2">{{ $stats['applications'] }}</div>
                    <div class="text-lg">Candidaturas</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Companies Section -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12">Empresas em Destaque</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                @foreach($featuredCompanies as $company)
                    <a href="{{ route('companies.show', $company) }}" class="bg-white p-4 rounded-lg shadow-md hover:shadow-lg transition flex items-center justify-center h-24">
                        @if($company->company_logo)
                            <img src="{{ asset('storage/' . $company->company_logo) }}" alt="{{ $company->company_name }}" class="max-h-16 max-w-full object-contain">
                        @else
                            <div class="text-center">
                                <i class="fas fa-building text-gray-400 text-3xl mb-2"></i>
                                <p class="text-gray-600 text-sm">{{ $company->company_name }}</p>
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>
            
            <div class="text-center mt-10">
                <a href="{{ route('companies.index') }}" class="inline-block bg-primary-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-primary-700 transition">
                    Ver Todas as Empresas
                </a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gray-100">
        <div class="container mx-auto px-4">
            <div class="bg-white rounded-lg shadow-lg p-8 md:p-12">
                <div class="text-center max-w-3xl mx-auto">
                    <h2 class="text-3xl font-bold mb-6">Pronto para Começar?</h2>
                    <p class="text-gray-600 text-lg mb-8">Junte-se à nossa comunidade e descubra novas oportunidades profissionais ou encontre os melhores talentos para a sua empresa.</p>
                    
                    <div class="flex flex-col md:flex-row gap-4 justify-center">
                        <a href="{{ route('register') }}" class="bg-primary-600 text-white px-8 py-3 rounded-md font-semibold hover:bg-primary-700 transition">
                            Registar como Ex-Aluno
                        </a>
                        <a href="{{ route('register.company') }}" class="bg-secondary-600 text-white px-8 py-3 rounded-md font-semibold hover:bg-secondary-700 transition">
                            Registar como Empresa
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
