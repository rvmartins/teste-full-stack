@extends('layouts.app')

@section('title', 'Detalhes do Usuário')

@section('content')
<div class="page-header">
    <h1>
        <i class="fa fa-user"></i> {{ $user->nome }}
        @if($user->ativo)
            <span class="label label-success">Ativo</span>
        @else
            <span class="label label-danger">Inativo</span>
        @endif
        <small>Detalhes do usuário</small>
    </h1>
</div>

<div class="row">
    <!-- Informações do Usuário -->
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-info-circle"></i> Informações do Usuário
                </h3>
            </div>
            <div class="panel-body">
                <dl class="dl-horizontal">
                    <dt>ID:</dt>
                    <dd>#{{ $user->id }}</dd>
                    
                    <dt>Nome:</dt>
                    <dd><strong>{{ $user->nome }}</strong></dd>
                    
                    <dt>Email:</dt>
                    <dd>{{ $user->email }}</dd>
                    
                    <dt>Status:</dt>
                    <dd>
                        @if($user->ativo)
                            <i class="fa fa-check-circle text-success"></i> Ativo
                        @else
                            <i class="fa fa-times-circle text-danger"></i> Inativo
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- Informações do Sistema -->
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-clock-o"></i> Informações do Sistema
                </h3>
            </div>
            <div class="panel-body">
                <dl class="dl-horizontal">
                    <dt>Cadastrado em:</dt>
                    <dd>
                        <i class="fa fa-calendar"></i> {{ $user->created_at->format('d/m/Y H:i:s') }}
                    </dd>
                    
                    <dt>Atualizado em:</dt>
                    <dd>
                        <i class="fa fa-calendar"></i> {{ $user->updated_at->format('d/m/Y H:i:s') }}
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<!-- Ações -->
<div class="row">
    <div class="col-md-12">
        <div class="btn-group" role="group">
            <a class="btn btn-primary btn-lg" href="{{ route('users.edit', $user->id) }}">
                <i class="fa fa-edit"></i> Editar
            </a>
            <a class="btn btn-default btn-lg" href="{{ route('users.index') }}">
                <i class="fa fa-arrow-left"></i> Voltar à Lista
            </a>
            <button type="button" class="btn btn-danger btn-lg" data-toggle="modal" data-target="#deleteModal">
                <i class="fa fa-trash"></i> Excluir
            </button>
        </div>
    </div>
</div>

<!-- Modal de confirmação de exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Confirmar Exclusão</h4>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o usuário <strong>{{ $user->nome }}</strong>?</p>
                <p class="text-danger">
                    <i class="fa fa-warning"></i> Esta ação não pode ser desfeita.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <form method="POST" action="{{ route('users.destroy', $user->id) }}" style="display: inline;" class="no-loading">
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