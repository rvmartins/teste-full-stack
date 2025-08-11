@extends('layouts.app')

@section('title', 'Lista de Usuários')

@section('content')
<div class="page-header">
    <h1>
        <i class="fa fa-users"></i> Lista de Usuários
        <small>Gerenciar usuários do sistema</small>
    </h1>
</div>

<div class="row">
    <!-- Filtros -->
    <div class="col-md-6">
        <div class="form-group">
            <div class="input-group">
                <input type="text" 
                       id="searchInput" 
                       class="form-control" 
                       placeholder="Buscar por nome ou email..."
                       value="{{ request('busca') }}">
                <div class="input-group-addon" id="clearSearch" style="cursor: pointer; display: none;">
                    <i class="fa fa-times"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <select class="form-control" id="ativoFilter">
            <option value="">Todos os usuários</option>
            <option value="1" {{ request('ativo') == '1' ? 'selected' : '' }}>Apenas ativos</option>
            <option value="0" {{ request('ativo') == '0' ? 'selected' : '' }}>Apenas inativos</option>
        </select>
    </div>
    
    <div class="col-md-3">
        <a class="btn btn-success btn-block" href="{{ route('users.create') }}">
            <i class="fa fa-plus"></i> Novo Usuário
        </a>
    </div>
</div>

<div id="loadingIndicator" style="display: none;" class="text-center">
    <i class="fa fa-spinner fa-spin"></i> Carregando...
</div>

<div id="tableContainer">
    @if($users->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>
                            <a href="#" class="sort-link" data-sort="nome">
                                Nome
                                @if(request('sort_by') == 'nome')
                                    <i class="fa fa-sort-{{ request('sort_order', 'asc') == 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fa fa-sort"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="#" class="sort-link" data-sort="email">
                                Email
                                @if(request('sort_by') == 'email')
                                    <i class="fa fa-sort-{{ request('sort_order', 'asc') == 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fa fa-sort"></i>
                                @endif
                            </a>
                        </th>
                        <th>Status</th>
                        <th>
                            <a href="#" class="sort-link" data-sort="created_at">
                                Criado em
                                @if(request('sort_by') == 'created_at')
                                    <i class="fa fa-sort-{{ request('sort_order', 'asc') == 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fa fa-sort"></i>
                                @endif
                            </a>
                        </th>
                        <th width="120">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                    <tr>
                        <td><strong>{{ $user->nome }}</strong></td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->ativo)
                                <span class="label label-success">Ativo</span>
                            @else
                                <span class="label label-danger">Inativo</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="btn-group btn-group-xs">
                                <a class="btn btn-info" href="{{ route('users.show', $user->id) }}" title="Visualizar">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a class="btn btn-primary" href="{{ route('users.edit', $user->id) }}" title="Editar">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-danger delete-btn" 
                                        data-id="{{ $user->id }}" 
                                        data-nome="{{ $user->nome }}" 
                                        title="Excluir">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center" style="padding: 40px;">
            <i class="fa fa-info-circle fa-3x text-muted"></i>
            <h4>Nenhum usuário encontrado</h4>
            <p class="text-muted">Não há usuários cadastrados ou que atendam aos filtros aplicados.</p>
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fa fa-plus"></i> Cadastrar primeiro usuário
            </a>
        </div>
    @endif
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
                <p>Tem certeza que deseja excluir o usuário <strong id="entityName"></strong>?</p>
                <p class="text-danger">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <form method="POST" id="deleteForm" style="display: inline;" class="no-loading">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra-js')
<script>
    $(document).ready(function() {
        let searchTimeout;
        let lastSearch = '';
        let isLoading = false;

        // Debounce na busca
        $('#searchInput').on('input', function() {
            const searchTerm = $(this).val();
            
            // Mostrar/ocultar botão limpar
            if (searchTerm) {
                $('#clearSearch').show();
            } else {
                $('#clearSearch').hide();
            }

            // Cancelar busca anterior
            clearTimeout(searchTimeout);
            
            // Nova busca após 500ms
            searchTimeout = setTimeout(function() {
                if (searchTerm !== lastSearch && !isLoading) {
                    performSearch();
                }
            }, 500);
        });

        // Limpar busca
        $('#clearSearch').click(function() {
            $('#searchInput').val('').trigger('input');
            performSearch();
        });

        // Filtro de status
        $('#ativoFilter').change(function() {
            performSearch();
        });

        // Ordenação
        $('.sort-link').click(function(e) {
            e.preventDefault();
            const sortBy = $(this).data('sort');
            const currentSort = new URLSearchParams(window.location.search).get('sort_by');
            const currentOrder = new URLSearchParams(window.location.search).get('sort_order') || 'asc';
            
            let newOrder = 'asc';
            if (currentSort === sortBy && currentOrder === 'asc') {
                newOrder = 'desc';
            }
            
            const url = new URL(window.location);
            url.searchParams.set('sort_by', sortBy);
            url.searchParams.set('sort_order', newOrder);
            window.location.href = url.toString();
        });

        // Exclusão
        $('.delete-btn').click(function() {
            const id = $(this).data('id');
            const nome = $(this).data('nome');
            
            $('#entityName').text(nome);
            $('#deleteForm').attr('action', '/users/' + id);
            $('#deleteModal').modal('show');
        });

        function performSearch() {
            if (isLoading) return;
            
            isLoading = true;
            const busca = $('#searchInput').val();
            const ativo = $('#ativoFilter').val();
            
            lastSearch = busca;
            
            $('#loadingIndicator').show();
            $('#tableContainer').addClass('loading');

            const url = new URL(window.location.href);
            url.searchParams.set('busca', busca);
            url.searchParams.set('ativo', ativo);
            url.searchParams.delete('page');
            
            window.location.href = url.toString();
        }

        // Mostrar ícone limpar se há texto
        if ($('#searchInput').val()) {
            $('#clearSearch').show();
        }
    });
</script>
@endsection

@section('extra-css')
<style>
    .loading { opacity: 0.6; pointer-events: none; }
</style>
@endsection