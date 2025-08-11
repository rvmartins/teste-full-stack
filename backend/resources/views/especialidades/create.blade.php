@extends('layouts.app')

@section('title', 'Nova Especialidade')

@section('content')
<div class="page-header">
    <h1>
        <i class="fa fa-plus"></i> Nova Especialidade Médica
        <small>Cadastrar nova especialidade no sistema</small>
    </h1>
</div>

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <form method="POST" action="{{ route('especialidades.store') }}" id="especialidadeForm" class="no-loading">
            {{ csrf_field() }}

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-stethoscope"></i> Informações da Especialidade
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="nome">
                            Nome da Especialidade <span class="text-danger">*</span>
                            <span class="char-counter pull-right">
                                <span id="nomeCount">0</span>/255
                            </span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="nome" 
                               name="nome" 
                               value="{{ old('nome') }}" 
                               required
                               maxlength="255"
                               placeholder="Ex: Cardiologia, Neurologia, Pediatria, etc.">
                        <small class="help-block">Digite o nome da especialidade médica</small>
                    </div>

                    <div class="form-group">
                        <label for="descricao">
                            Descrição
                            <span class="char-counter pull-right">
                                <span id="descricaoCount">0</span>/500
                            </span>
                        </label>
                        <textarea class="form-control" 
                                  id="descricao" 
                                  name="descricao" 
                                  rows="4"
                                  maxlength="500"
                                  placeholder="Descreva brevemente a especialidade (opcional)">{{ old('descricao') }}</textarea>
                        <small class="help-block">Descrição opcional da especialidade</small>
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" 
                                       name="ativa" 
                                       value="1" 
                                       {{ old('ativa', true) ? 'checked' : '' }}> 
                                <strong>Especialidade Ativa</strong>
                            </label>
                            <small class="help-block">Especialidades inativas não aparecem na seleção de clínicas</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                    <i class="fa fa-save"></i> Criar Especialidade
                </button>
                <a class="btn btn-default btn-lg" href="{{ route('especialidades.index') }}">
                    <i class="fa fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('extra-css')
<style>
    .char-counter { 
        font-size: 12px; 
        color: #666; 
    }
    .text-warning { 
        color: #f0ad4e !important; 
    }
</style>
@endsection

@section('extra-js')
<script>
    $(document).ready(function() {
        // Contador de caracteres para nome
        $('#nome').on('input', function() {
            const length = $(this).val().length;
            $('#nomeCount').text(length);
            
            if (length > 200) {
                $('#nomeCount').parent().addClass('text-warning');
            } else {
                $('#nomeCount').parent().removeClass('text-warning');
            }
        });

        // Contador de caracteres para descrição
        $('#descricao').on('input', function() {
            const length = $(this).val().length;
            $('#descricaoCount').text(length);
            
            if (length > 400) {
                $('#descricaoCount').parent().addClass('text-warning');
            } else {
                $('#descricaoCount').parent().removeClass('text-warning');
            }
        });

        // Inicializar contadores
        $('#nome').trigger('input');
        $('#descricao').trigger('input');

        // Prevenir double submit
        $('#especialidadeForm').on('submit', function(e) {
            const submitBtn = $('#submitBtn');
            
            if (submitBtn.hasClass('disabled')) {
                e.preventDefault();
                return false;
            }
            
            // Validação simples
            const nome = $('#nome').val().trim();
            if (!nome) {
                alert('O nome da especialidade é obrigatório.');
                $('#nome').focus();
                return false;
            }
            
            submitBtn.addClass('disabled').html('<i class="fa fa-spinner fa-spin"></i> Salvando...');
            $('#loadingOverlay').show();
            $('form').addClass('form-loading');
        });

        // Foco inicial no campo nome
        $('#nome').focus();
    });
</script>
@endsection