@extends('layouts.app')

@section('title', 'Nova Clínica')

@section('content')
<div class="page-header">
    <h1>
        <i class="fa fa-plus"></i> Nova Clínica
        <small>Cadastrar nova clínica no sistema</small>
    </h1>
</div>

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <form method="POST" action="{{ route('entidades.store') }}" id="entityForm" class="no-loading">
            {{ csrf_field() }}

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-info-circle"></i> Informações Básicas
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="razao_social">
                            Razão Social <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="razao_social" 
                               name="razao_social" 
                               value="{{ old('razao_social') }}" 
                               required
                               maxlength="255"
                               placeholder="Ex: Clínica Médica São Paulo Ltda">
                    </div>

                    <div class="form-group">
                        <label for="nome_fantasia">
                            Nome Fantasia <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="nome_fantasia" 
                               name="nome_fantasia" 
                               value="{{ old('nome_fantasia') }}" 
                               required
                               maxlength="255"
                               placeholder="Ex: Clínica São Paulo">
                    </div>

                    <div class="form-group">
                        <label for="cnpj">
                            CNPJ <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="cnpj" 
                               name="cnpj" 
                               value="{{ old('cnpj') }}" 
                               required
                               placeholder="00.000.000/0000-00"
                               maxlength="18">
                        <div id="cnpjFeedback" class="help-block"></div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-building"></i> Informações Operacionais
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="regional">
                            Regional <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" id="regional" name="regional" required>
                            <option value="">Selecione uma regional</option>
                            @foreach($regionais as $key => $value)
                                <option value="{{ $key }}" {{ old('regional') == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="data_inauguracao">
                            Data de Inauguração <span class="text-danger">*</span>
                        </label>
                        <input type="date" 
                               class="form-control" 
                               id="data_inauguracao" 
                               name="data_inauguracao" 
                               value="{{ old('data_inauguracao') }}" 
                               required>
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" 
                                       name="ativa" 
                                       value="1" 
                                       {{ old('ativa', true) ? 'checked' : '' }}> 
                                <strong>Clínica Ativa</strong>
                            </label>
                            <small class="help-block">Clínicas inativas não aparecem em relatórios</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-stethoscope"></i> Especialidades Médicas
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="especialidades">
                            Especialidades <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" id="especialidades" name="especialidades[]" multiple required>
                            @foreach($especialidades as $especialidade)
                                <option value="{{ $especialidade->id }}" 
                                        {{ in_array($especialidade->id, old('especialidades', [])) ? 'selected' : '' }}>
                                    {{ $especialidade->nome }}
                                </option>
                            @endforeach
                        </select>
                        <small class="help-block">Selecione pelo menos 5 especialidades</small>
                        <div id="especialidadesCount" class="help-block"></div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                    <i class="fa fa-save"></i> Criar Clínica
                </button>
                <a class="btn btn-default btn-lg" href="{{ route('entidades.index') }}">
                    <i class="fa fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('extra-css')
<style>
    .required { color: red; }
    .cnpj-feedback { margin-top: 5px; font-size: 12px; }
    .cnpj-valid { color: green; }
    .cnpj-invalid { color: red; }
    .select2-container { width: 100% !important; }
    .form-loading { opacity: 0.6; pointer-events: none; }
</style>
@endsection

@section('extra-js')
<script>
    $(document).ready(function() {
        // Inicializar Select2 para especialidades
        $('#especialidades').select2({
            placeholder: 'Selecione as especialidades...',
            language: 'pt-BR',
            allowClear: true
        });

        // Contar especialidades selecionadas
        function updateEspecialidadesCount() {
            const count = $('#especialidades').val() ? $('#especialidades').val().length : 0;
            const minRequired = 5;
            const isValid = count >= minRequired;
            
            $('#especialidadesCount').html(
                `<i class="fa fa-${isValid ? 'check text-success' : 'times text-danger'}"></i> ` +
                `${count} de ${minRequired} especialidades selecionadas`
            ).toggleClass('text-success', isValid).toggleClass('text-danger', !isValid);
        }

        $('#especialidades').on('change', updateEspecialidadesCount);
        updateEspecialidadesCount();

        // Máscara e validação do CNPJ
        $('#cnpj').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            
            // Aplicar máscara
            if (value.length <= 14) {
                value = value.replace(/^(\d{2})(\d)/, '$1.$2');
                value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
                value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
                value = value.replace(/(\d{4})(\d)/, '$1-$2');
            }
            
            $(this).val(value);
            validateCNPJ(value);
        });

        function validateCNPJ(cnpj) {
            const feedback = $('#cnpjFeedback');
            const cleanCNPJ = cnpj.replace(/\D/g, '');
            
            if (cleanCNPJ.length === 0) {
                feedback.text('').removeClass('cnpj-valid cnpj-invalid');
                return;
            }
            
            if (cleanCNPJ.length !== 14) {
                feedback.text('CNPJ deve ter 14 dígitos').removeClass('cnpj-valid').addClass('cnpj-invalid text-danger');
                return;
            }
            
            if (isValidCNPJ(cleanCNPJ)) {
                feedback.html('<i class="fa fa-check"></i> CNPJ válido').removeClass('cnpj-invalid text-danger').addClass('cnpj-valid text-success');
            } else {
                feedback.html('<i class="fa fa-times"></i> CNPJ inválido').removeClass('cnpj-valid text-success').addClass('cnpj-invalid text-danger');
            }
        }

        function isValidCNPJ(cnpj) {
            if (cnpj.length !== 14) return false;
            
            // Elimina CNPJs conhecidos como inválidos
            if (/^(\d)\1{13}$/.test(cnpj)) return false;
            
            // Valida DVs
            let size = cnpj.length - 2;
            let numbers = cnpj.substring(0, size);
            let digits = cnpj.substring(size);
            let sum = 0;
            let pos = size - 7;
            
            for (let i = size; i >= 1; i--) {
                sum += numbers.charAt(size - i) * pos--;
                if (pos < 2) pos = 9;
            }
            
            let result = sum % 11 < 2 ? 0 : 11 - sum % 11;
            if (result != digits.charAt(0)) return false;
            
            size = size + 1;
            numbers = cnpj.substring(0, size);
            sum = 0;
            pos = size - 7;
            
            for (let i = size; i >= 1; i--) {
                sum += numbers.charAt(size - i) * pos--;
                if (pos < 2) pos = 9;
            }
            
            result = sum % 11 < 2 ? 0 : 11 - sum % 11;
            return result == digits.charAt(1);
        }

        // Prevenir double submit
        $('#entityForm').on('submit', function(e) {
            const submitBtn = $('#submitBtn');
            
            if (submitBtn.hasClass('disabled')) {
                e.preventDefault();
                return false;
            }
            
            submitBtn.addClass('disabled').html('<i class="fa fa-spinner fa-spin"></i> Salvando...');
            $('#loadingOverlay').show();
            $('form').addClass('form-loading');
        });

        // Foco inicial no campo razao_social
        $('#razao_social').focus();
    });
</script>
@endsection