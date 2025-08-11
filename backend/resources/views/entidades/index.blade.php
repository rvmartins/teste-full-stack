@extends('layouts.app')

@section('title', 'Lista de Clínicas')

@section('content')
<div class="page-header">
    <h1>
        <i class="fa fa-hospital-o"></i> Lista de Clínicas
        <small>Gerenciar clínicas do sistema</small>
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
                       placeholder="Buscar por razão social, nome fantasia ou CNPJ..."
                       value="{{ request('busca') }}">
                <div class="input-group-addon" id="clearSearch" style="cursor: pointer; display: none;">
                    <i class="fa fa-times"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <select class="form-control" id="regionalFilter">
            <option value="">Todas as regionais</option>
            @foreach(\App\Entidade::getRegionaisOptions() as $key => $value)
                <option value="{{ $key }}" {{ request('regional') == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
    </div>
    
    <div class="col-md-3">
        <a class="btn btn-success btn-block" href="{{ route('entidades.create') }}">
            <i class="fa fa-plus"></i> Nova Clínica
        </a>
    </div>
</div>

<div id="loadingIndicator" style="display: none;" class="text-center">
    <i class="fa fa-spinner fa-spin"></i> Carregando...
</div>

<div id="tableContainer">
    @if($entidades->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>
                            <a href="#" class="sort-link" data-sort="razao_social">
                                Razão Social 
                                @if(request('sort_by') == 'razao_social')
                                    <i class="fa fa-sort-{{ request('sort_order', 'asc') == 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fa fa-sort"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="#" class="sort-link" data-sort="nome_fantasia">
                                Nome Fantasia
                                @if(request('sort_by') == 'nome_fantasia')
                                    <i class="fa fa-sort-{{ request('sort_order', 'asc') == 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fa fa-sort"></i>
                                @endif
                            </a>
                        </th>
                        <th>CNPJ</th>
                        <th>Regional</th>
                        <th>Especialidades</th>
                        <th>Status</th>
                        <th width="120">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($entidades as $entidade)
                    <tr>
                        <td>{{ $entidade->razao_social }}</td>
                        <td><strong>{{ $entidade->nome_fantasia }}</strong></td>
                        <td>{{ $entidade->cnpj_formatado }}</td>
                        <td><span class="label label-info">{{ $entidade->regional_nome }}</span></td>
                        <td>
                            @foreach($entidade->especialidades->take(2) as $especialidade)
                                <span class="label label-default" style="margin: 1px;">{{ $especialidade->nome }}</span>
                            @endforeach
                            @if($entidade->especialidades->count() > 2)
                                <span class="label label-primary" style="margin: 1px;">+{{ $entidade->especialidades->count() - 2 }}</span>
                            @endif
                        </td>
                        <td>
                            @if($entidade->ativa)
                                <span class="label label-success">Ativa</span>
                            @else
                                <span class="label label-danger">Inativa</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-xs">
                                <a class="btn btn-info" href="{{ route('entidades.show', $entidade->id) }}" title="Visualizar">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a class="btn btn-primary" href="{{ route('entidades.edit', $entidade->id) }}" title="Editar">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-danger delete-btn" 
                                        data-id="{{ $entidade->id }}" 
                                        data-nome="{{ $entidade->nome_fantasia }}" 
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
            <h4>Nenhuma clínica encontrada</h4>
            <p class="text-muted">Não há clínicas cadastradas ou que atendam aos filtros aplicados.</p>
            <a href="{{ route('entidades.create') }}" class="btn btn-primary">
                <i class="fa fa-plus"></i> Cadastrar primeira clínica
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
                <p>Tem certeza que deseja excluir a clínica <strong id="entityName"></strong>?</p>
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

        // Filtro de regional
        $('#regionalFilter').change(function() {
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
            $('#deleteForm').attr('action', '/entidades/' + id);
            $('#deleteModal').modal('show');
        });

        function performSearch() {
            if (isLoading) return;
            
            isLoading = true;
            const busca = $('#searchInput').val();
            const regional = $('#regionalFilter').val();
            
            lastSearch = busca;
            
            $('#loadingIndicator').show();
            $('#tableContainer').addClass('loading');

            const url = new URL(window.location.href);
            url.searchParams.set('busca', busca);
            url.searchParams.set('regional', regional);
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