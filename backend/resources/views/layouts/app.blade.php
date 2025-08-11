<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Sistema de Clínicas')</title>
    
    <!-- CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet">
    
    <style>
        body {
            padding-top: 70px;
            background-color: #f8f9fa;
        }
        
        .navbar-brand {
            font-weight: bold;
        }
        
        .main-content {
            background-color: white;
            min-height: calc(100vh - 140px);
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .page-header {
            border-bottom: 1px solid #eee;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
        
        .page-header h1, .page-header h2 {
            margin-top: 0;
        }
        
        .loading-overlay { 
            position: fixed; 
            top: 0; 
            left: 0; 
            right: 0; 
            bottom: 0; 
            background: rgba(255,255,255,0.8); 
            z-index: 9999; 
            display: none; 
        }
        
        .loading-content { 
            position: absolute; 
            top: 50%; 
            left: 50%; 
            transform: translate(-50%, -50%); 
            text-align: center; 
        }
        
        .form-loading { 
            opacity: 0.6; 
            pointer-events: none; 
        }
        
        .footer {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            padding: 20px 0;
            margin-top: 40px;
            text-align: center;
            color: #6c757d;
        }
        
        .breadcrumb {
            background-color: transparent;
            padding: 8px 0;
            margin-bottom: 20px;
        }
        
        .alert {
            margin-bottom: 20px;
        }
        
        .navbar-nav > li > a {
            position: relative;
        }
        
        .navbar-nav > li.active > a,
        .navbar-nav > li.active > a:hover,
        .navbar-nav > li.active > a:focus {
            background-color: #337ab7;
            color: white;
        }
        
        @media (max-width: 768px) {
            body {
                padding-top: 50px;
            }
            
            .main-content {
                margin: 10px;
                padding: 15px;
            }
        }
        
        @yield('extra-css')
    </style>
</head>
<body>
    <!-- Loading Overlay Global -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <i class="fa fa-spinner fa-spin fa-3x text-primary"></i>
            <h4>Carregando...</h4>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{ url('/') }}">
                    <i class="fa fa-hospital-o"></i> Sistema de Clínicas
                </a>
            </div>
            
            <div class="collapse navbar-collapse" id="navbar">
                <ul class="nav navbar-nav">
                    <li class="{{ Request::is('/') ? 'active' : '' }}">
                        <a href="{{ url('/') }}">
                            <i class="fa fa-home"></i> Início
                        </a>
                    </li>
                    
                    <li class="dropdown {{ Request::is('entidades*') ? 'active' : '' }}">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-hospital-o"></i> Clínicas <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ route('entidades.index') }}"><i class="fa fa-list"></i> Listar Clínicas</a></li>
                            <li><a href="{{ route('entidades.create') }}"><i class="fa fa-plus"></i> Nova Clínica</a></li>
                        </ul>
                    </li>
                    
                    <li class="dropdown {{ Request::is('especialidades*') ? 'active' : '' }}">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-stethoscope"></i> Especialidades <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ route('especialidades.index') }}"><i class="fa fa-list"></i> Listar Especialidades</a></li>
                            <li><a href="{{ route('especialidades.create') }}"><i class="fa fa-plus"></i> Nova Especialidade</a></li>
                        </ul>
                    </li>
                    
                    <li class="dropdown {{ Request::is('users*') ? 'active' : '' }}">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-users"></i> Usuários <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ route('users.index') }}"><i class="fa fa-list"></i> Listar Usuários</a></li>
                            <li><a href="{{ route('users.create') }}"><i class="fa fa-plus"></i> Novo Usuário</a></li>
                        </ul>
                    </li>
                </ul>
                
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-cog"></i> Sistema <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="#"><i class="fa fa-user"></i> Perfil</a></li>
                            <li><a href="#"><i class="fa fa-cog"></i> Configurações</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="#"><i class="fa fa-sign-out"></i> Sair</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <!-- Breadcrumb -->
        @if(isset($breadcrumbs) && count($breadcrumbs) > 0)
            <ol class="breadcrumb">
                @foreach($breadcrumbs as $breadcrumb)
                    @if($loop->last)
                        <li class="active">{{ $breadcrumb['title'] }}</li>
                    @else
                        <li><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a></li>
                    @endif
                @endforeach
            </ol>
        @endif

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <i class="fa fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <i class="fa fa-warning"></i> {{ session('warning') }}
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <i class="fa fa-info-circle"></i> {{ session('info') }}
            </div>
        @endif

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4><i class="fa fa-exclamation-triangle"></i> Atenção!</h4>
                <ul style="margin-bottom: 0;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Page Content -->
        <div class="main-content">
            @yield('content')
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p class="text-muted">
                &copy; {{ date('Y') }} Sistema de Clínicas. 
                Desenvolvido com <i class="fa fa-heart text-danger"></i> usando Laravel {{ app()->version() }}
            </p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/i18n/pt-BR.js"></script>
    
    <script>
        $(document).ready(function() {
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
            
            // Global loading for navigation
            $('a[href]:not([href="#"]):not([data-toggle]):not(.no-loading), form:not(.no-loading)').on('click submit', function(e) {
                // Skip if it's a dropdown toggle or modal trigger
                if ($(this).hasClass('dropdown-toggle') || $(this).attr('data-toggle')) {
                    return;
                }
                
                setTimeout(function() {
                    $('#loadingOverlay').show();
                }, 100);
            });
            
            // Hide loading on page load
            $(window).on('load', function() {
                $('#loadingOverlay').hide();
            });
            
            // CSRF Token setup for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>
    
    @yield('extra-js')
</body>
</html>