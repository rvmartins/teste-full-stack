@extends('layouts.app')

@section('title', 'Dashboard - Sistema de Clínicas')

@section('content')
<div class="page-header">
    <h1>
        <i class="fa fa-dashboard"></i> Dashboard
        <small>Visão geral do sistema</small>
    </h1>
</div>

<div class="row">
    <!-- Estatísticas -->
    <div class="col-md-3">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-hospital-o fa-3x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge">{{ \App\Entidade::count() }}</div>
                        <div>Clínicas</div>
                    </div>
                </div>
            </div>
            <a href="{{ route('entidades.index') }}">
                <div class="panel-footer">
                    <span class="pull-left">Ver todas</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>

    <div class="col-md-3">
        <div class="panel panel-green">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-hospital-o fa-3x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge">{{ \App\Entidade::where('ativa', true)->count() }}</div>
                        <div>Ativas</div>
                    </div>
                </div>
            </div>
            <a href="{{ route('entidades.index') }}?ativa=1">
                <div class="panel-footer">
                    <span class="pull-left">Ver ativas</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>

    <div class="col-md-3">
        <div class="panel panel-yellow">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-stethoscope fa-3x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge">{{ \App\Especialidade::count() }}</div>
                        <div>Especialidades</div>
                    </div>
                </div>
            </div>
            <a href="{{ route('especialidades.index') }}">
                <div class="panel-footer">
                    <span class="pull-left">Ver todas</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>

    <div class="col-md-3">
        <div class="panel panel-red">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-users fa-3x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge">{{ \App\User::count() }}</div>
                        <div>Usuários</div>
                    </div>
                </div>
            </div>
            <a href="{{ route('users.index') }}">
                <div class="panel-footer">
                    <span class="pull-left">Ver todos</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Ações Rápidas -->
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-bolt"></i> Ações Rápidas
                </h3>
            </div>
            <div class="panel-body">
                <div class="list-group">
                    <a href="{{ route('entidades.create') }}" class="list-group-item">
                        <i class="fa fa-plus text-primary"></i> Nova Clínica
                        <span class="pull-right text-muted">
                            <i class="fa fa-chevron-right"></i>
                        </span>
                    </a>
                    <a href="{{ route('especialidades.create') }}" class="list-group-item">
                        <i class="fa fa-plus text-success"></i> Nova Especialidade
                        <span class="pull-right text-muted">
                            <i class="fa fa-chevron-right"></i>
                        </span>
                    </a>
                    <a href="{{ route('users.create') }}" class="list-group-item">
                        <i class="fa fa-plus text-warning"></i> Novo Usuário
                        <span class="pull-right text-muted">
                            <i class="fa fa-chevron-right"></i>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Últimas Clínicas -->
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-clock-o"></i> Últimas Clínicas Cadastradas
                </h3>
            </div>
            <div class="panel-body">
                @php
                    $ultimasClinicas = \App\Entidade::orderBy('created_at', 'desc')->take(5)->get();
                @endphp
                
                @if($ultimasClinicas->count() > 0)
                    <div class="list-group">
                        @foreach($ultimasClinicas as $clinica)
                            <a href="{{ route('entidades.show', $clinica->id) }}" class="list-group-item">
                                <h4 class="list-group-item-heading">{{ $clinica->nome_fantasia }}</h4>
                                <p class="list-group-item-text">
                                    <small class="text-muted">
                                        <i class="fa fa-calendar"></i> {{ $clinica->created_at->format('d/m/Y H:i') }}
                                        <span class="pull-right">
                                            @if($clinica->ativa)
                                                <span class="label label-success">Ativa</span>
                                            @else
                                                <span class="label label-danger">Inativa</span>
                                            @endif
                                        </span>
                                    </small>
                                </p>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center">
                        <i class="fa fa-info-circle"></i> Nenhuma clínica cadastrada ainda.
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Distribuição por Regional -->
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-map-marker"></i> Clínicas por Regional
                </h3>
            </div>
            <div class="panel-body">
                @php
                    $regionais = \App\Entidade::selectRaw('regional, count(*) as total')
                        ->groupBy('regional')
                        ->orderBy('total', 'desc')
                        ->get();
                @endphp
                
                @if($regionais->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Regional</th>
                                    <th class="text-center">Quantidade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($regionais as $regional)
                                    <tr>
                                        <td>{{ $regional->regional_nome }}</td>
                                        <td class="text-center">
                                            <span class="badge">{{ $regional->total }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center">
                        <i class="fa fa-info-circle"></i> Nenhuma clínica cadastrada.
                    </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Especialidades Mais Usadas -->
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-star"></i> Especialidades Mais Utilizadas
                </h3>
            </div>
            <div class="panel-body">
                @php
                    $especialidadesPopulares = \App\Especialidade::withCount('entidades')
                        ->orderBy('entidades_count', 'desc')
                        ->take(5)
                        ->get();
                @endphp
                
                @if($especialidadesPopulares->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Especialidade</th>
                                    <th class="text-center">Clínicas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($especialidadesPopulares as $especialidade)
                                    <tr>
                                        <td>{{ $especialidade->nome }}</td>
                                        <td class="text-center">
                                            <span class="badge">{{ $especialidade->entidades_count }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center">
                        <i class="fa fa-info-circle"></i> Nenhuma especialidade cadastrada.
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra-css')
<style>
    .huge {
        font-size: 40px;
        font-weight: bold;
    }
    
    .panel-green {
        border-color: #5cb85c;
    }
    
    .panel-green > .panel-heading {
        border-color: #5cb85c;
        color: white;
        background-color: #5cb85c;
    }
    
    .panel-green > a {
        color: #5cb85c;
    }
    
    .panel-green > a:hover {
        color: #3d8b3d;
    }
    
    .panel-yellow {
        border-color: #f0ad4e;
    }
    
    .panel-yellow > .panel-heading {
        border-color: #f0ad4e;
        color: white;
        background-color: #f0ad4e;
    }
    
    .panel-yellow > a {
        color: #f0ad4e;
    }
    
    .panel-yellow > a:hover {
        color: #ec971f;
    }
    
    .panel-red {
        border-color: #d9534f;
    }
    
    .panel-red > .panel-heading {
        border-color: #d9534f;
        color: white;
        background-color: #d9534f;
    }
    
    .panel-red > a {
        color: #d9534f;
    }
    
    .panel-red > a:hover {
        color: #c9302c;
    }
</style>
@endsection