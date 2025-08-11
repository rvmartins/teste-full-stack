@extends('layouts.app')

@section('title', 'Detalhes da Especialidade')

@section('content')
<div class="page-header">
    <h1>
        <i class="fa fa-stethoscope"></i> {{ $especialidade->nome }}
        @if($especialidade->ativa)
            <span class="label label-success">Ativa</span>
        @else
            <span class="label label-danger">Inativa</span>
        @endif
        <small>Detalhes da especialidade</small>
    </h1>
</div>

<div class="row">
    <!-- Informações da Especialidade -->
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-info-circle"></i> Informações da Especialidade
                </h3>
            </div>
            <div class="panel-body">
                <dl class="dl-horizontal">
                    <dt>ID:</dt>
                    <dd>#{{ $especialidade->id }}</dd>
                    
                    <dt>Nome:</dt>
                    <dd><strong>{{ $especialidade->nome }}</strong></dd>
                    
                    <dt>Descrição:</dt>
                    <dd>
                        @if($especialidade->descricao)
                            {{ $especialidade->descricao }}
                        @else
                            <span class="text-muted">Sem descrição</span>
                        @endif
                    </dd>
                    
                    <dt>Status:</dt>
                    <dd>
                        @if($especialidade->ativa)
                            <i class="fa fa-check-circle text-success"></i> Ativa
                        @else
                            <i class="fa fa-times-circle text-danger"></i> Inativa
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
                    <dt>Cadastrada em:</dt>
                    <dd>
                        <i class="fa fa-calendar"></i> {{ $especialidade->created_at->format('d/m/Y H:i:s') }}
                    </dd>
                    
                    <dt>Atualizada em:</dt>
                    <dd>
                        <i class="fa fa-calendar"></i> {{ $especialidade->updated_at->format('d/m/Y H:i:s') }}
                    </dd>
                    
                    <dt>Clínicas:</dt>
                    <dd>
                        <span class="badge">{{ $especialidade->entidades->count() }}</span>
                        clínica(s) vinculada(s)
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<!-- Clínicas que atendem esta especialidade -->
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-hospital-o"></i> Clínicas que atendem esta especialidade
                    @if($especialidade->entidades->count() > 0)
                        <span class="badge">{{ $especialidade->entidades->count() }}</span>
                    @endif
                </h3>
            </div>
            <div class="panel-body">
                @if($especialidade->entidades->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nome Fantasia</th>
                                    <th>Razão Social</th>
                                    <th>Regional</th>
                                    <th>Status</th>
                                    <th width="80">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($especialidade->entidades as $entidade)
                                    <tr>
                                        <td><strong>{{ $entidade->nome_fantasia }}</strong></td>
                                        <td>{{ $entidade->razao_social }}</td>
                                        <td><span class="label label-info">{{ $entidade->regional_nome }}</span></td>
                                        <td>
                                            @if($entidade->ativa)
                                                <span class="label label-success">Ativa</span>
                                            @else
                                                <span class="label label-danger">Inativa</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('entidades.show', $entidade->id) }}" 
                                               class="btn btn-xs btn-primary" 
                                               title="Ver detalhes">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted" style="padding: 40px;">
                        <i class="fa fa-info-circle fa-3x"></i>
                        <h4>Nenhuma clínica vinculada</h4>
                        <p>Esta especialidade ainda não está vinculada a nenhuma clínica.</p>
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
            <a class="btn btn-primary btn-lg" href="{{ route('especialidades.edit', $especialidade->id) }}">
                <i class="fa fa-edit"></i> Editar
            </a>
            <a class="btn btn-default btn-lg" href="{{ route('especialidades.index') }}">
                <i class="fa fa-arrow-left"></i> Voltar à Lista
            </a>
            @if($especialidade->entidades->count() == 0)
                <button type="button" class="btn btn-danger btn-lg" data-toggle="modal" data-target="#deleteModal">
                    <i class="fa fa-trash"></i> Excluir
                </button>
            @else
                <button type="button" class="btn btn-danger btn-lg" disabled title="Não é possível excluir: está vinculada a clínicas">
                    <i class="fa fa-trash"></i> Excluir
                </button>
            @endif
        </div>
        
        @if($especialidade->entidades->count() > 0)
            <div class="alert alert-warning" style="margin-top: 20px;">
                <i class="fa fa-warning"></i>
                <strong>Atenção:</strong> Esta especialidade não pode ser excluída pois está vinculada a {{ $especialidade->entidades->count() }} clínica(s).
                Para excluí-la, primeiro remova-a de todas as clínicas.
            </div>
        @endif
    </div>
</div>

<!-- Modal de confirmação de exclusão -->
@if($especialidade->entidades->count() == 0)
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Confirmar Exclusão</h4>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir a especialidade <strong>{{ $especialidade->nome }}</strong>?</p>
                <p class="text-danger">
                    <i class="fa fa-warning"></i> Esta ação não pode ser desfeita.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <form method="POST" action="{{ route('especialidades.destroy', $especialidade->id) }}" style="display: inline;" class="no-loading">
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
@endif
@endsection