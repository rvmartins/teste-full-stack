@extends('layouts.app')

@section('title', 'Detalhes da Clínica')

@section('content')
<div class="page-header">
    <h1>
        <i class="fa fa-hospital-o"></i> {{ $entidade->nome_fantasia }}
        @if($entidade->ativa)
            <span class="label label-success">Ativa</span>
        @else
            <span class="label label-danger">Inativa</span>
        @endif
        <small>Detalhes da clínica</small>
    </h1>
</div>

<div class="row">
    <!-- Informações Básicas -->
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-info-circle"></i> Informações Básicas
                </h3>
            </div>
            <div class="panel-body">
                <dl class="dl-horizontal">
                    <dt>ID:</dt>
                    <dd>#{{ $entidade->id }}</dd>
                    
                    <dt>Razão Social:</dt>
                    <dd>{{ $entidade->razao_social }}</dd>
                    
                    <dt>Nome Fantasia:</dt>
                    <dd><strong>{{ $entidade->nome_fantasia }}</strong></dd>
                    
                    <dt>CNPJ:</dt>
                    <dd>{{ $entidade->cnpj_formatado }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- Informações Operacionais -->
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-building"></i> Informações Operacionais
                </h3>
            </div>
            <div class="panel-body">
                <dl class="dl-horizontal">
                    <dt>Regional:</dt>
                    <dd><span class="label label-primary">{{ $entidade->regional_nome }}</span></dd>
                    
                    <dt>Inauguração:</dt>
                    <dd>
                        <i class="fa fa-calendar"></i> {{ $entidade->data_inauguracao_formatada }}
                    </dd>
                    
                    <dt>Status:</dt>
                    <dd>
                        @if($entidade->ativa)
                            <i class="fa fa-check-circle text-success"></i> Ativa
                        @else
                            <i class="fa fa-times-circle text-danger"></i> Inativa
                        @endif
                    </dd>
                    
                    <dt>Cadastrada em:</dt>
                    <dd>{{ $entidade->created_at->format('d/m/Y H:i:s') }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<!-- Especialidades -->
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-stethoscope"></i> Especialidades Médicas 
                    <span class="badge">{{ $entidade->especialidades->count() }}</span>
                </h3>
            </div>
            <div class="panel-body">
                @if($entidade->especialidades->count() > 0)
                    <div class="row">
                        @foreach($entidade->especialidades->take(5) as $especialidade)
                            <div class="col-md-3 col-sm-4 col-xs-6" style="margin-bottom: 10px;">
                                <span class="label label-info" style="display: block; padding: 8px; font-size: 12px; text-align: center;">
                                    {{ $especialidade->nome }}
                                </span>
                            </div>
                        @endforeach
                        
                        @if($entidade->especialidades->count() > 5)
                            <div class="col-md-3 col-sm-4 col-xs-6" style="margin-bottom: 10px;">
                                <button class="btn btn-default btn-block" 
                                        data-toggle="modal" 
                                        data-target="#especialidadesModal"
                                        style="padding: 8px; font-size: 12px;">
                                    +{{ $entidade->especialidades->count() - 5 }} mais
                                </button>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center text-muted">
                        <i class="fa fa-info-circle fa-2x"></i>
                        <p>Nenhuma especialidade cadastrada.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Ações -->
<div class="row">
    <div class="col-md-12">
        <div class="btn-group" role="group">
            <a class="btn btn-primary btn-lg" href="{{ route('entidades.edit', $entidade->id) }}">
                <i class="fa fa-edit"></i> Editar
            </a>
            <a class="btn btn-default btn-lg" href="{{ route('entidades.index') }}">
                <i class="fa fa-arrow-left"></i> Voltar à Lista
            </a>
            <button type="button" class="btn btn-danger btn-lg" data-toggle="modal" data-target="#deleteModal">
                <i class="fa fa-trash"></i> Excluir
            </button>
        </div>
    </div>
</div>

<!-- Modal de Especialidades -->
@if($entidade->especialidades->count() > 5)
<div class="modal fade" id="especialidadesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                    <i class="fa fa-stethoscope"></i> Todas as Especialidades
                    <span class="badge">{{ $entidade->especialidades->count() }}</span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    @foreach($entidade->especialidades as $especialidade)
                        <div class="col-md-4 col-sm-6" style="margin-bottom: 10px;">
                            <span class="label label-info" style="display: block; padding: 8px; font-size: 12px; text-align: center;">
                                {{ $especialidade->nome }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Modal de confirmação de exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Confirmar Exclusão</h4>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir a clínica <strong>{{ $entidade->nome_fantasia }}</strong>?</p>
                <p class="text-danger">
                    <i class="fa fa-warning"></i> Esta ação não pode ser desfeita e removerá todos os vínculos com especialidades.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <form method="POST" action="{{ route('entidades.destroy', $entidade->id) }}" style="display: inline;" class="no-loading">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-trash"></i> Excluir
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection