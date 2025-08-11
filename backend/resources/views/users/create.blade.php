@extends('layouts.app')

@section('title', 'Novo Usuário')

@section('content')
<div class="page-header">
    <h1>
        <i class="fa fa-plus"></i> Novo Usuário
        <small>Cadastrar novo usuário no sistema</small>
    </h1>
</div>

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <form method="POST" action="{{ route('users.store') }}" id="userForm" class="no-loading">
            {{ csrf_field() }}

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-user"></i> Informações do Usuário
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="nome">
                            Nome <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="nome" 
                               name="nome" 
                               value="{{ old('nome') }}" 
                               required
                               maxlength="255"
                               placeholder="Nome completo do usuário">
                    </div>

                    <div class="form-group">
                        <label for="email">
                            Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" 
                               class="form-control" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required
                               maxlength="255"
                               placeholder="exemplo@email.com">
                    </div>

                    <div class="form-group">
                        <label for="password">
                            Senha <span class="text-danger">*</span>
                        </label>
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               required
                               minlength="6"
                               placeholder="Mínimo 6 caracteres">
                        <small class="help-block">A senha deve ter pelo menos 6 caracteres</small>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">
                            Confirmar Senha <span class="text-danger">*</span>
                        </label>
                        <input type="password" 
                               class="form-control" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               required
                               minlength="6"
                               placeholder="Digite a senha novamente">
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" 
                                       name="ativo" 
                                       value="1" 
                                       {{ old('ativo', true) ? 'checked' : '' }}> 
                                <strong>Usuário Ativo</strong>
                            </label>
                            <small class="help-block">Usuários inativos não conseguem fazer login</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                    <i class="fa fa-save"></i> Criar Usuário
                </button>
                <a class="btn btn-default btn-lg" href="{{ route('users.index') }}">
                    <i class="fa fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('extra-js')
<script>
    $(document).ready(function() {
        // Validação de senhas em tempo real
        $('#password_confirmation').on('input', function() {
            const password = $('#password').val();
            const confirmation = $(this).val();
            
            if (confirmation && password !== confirmation) {
                $(this).addClass('has-error');
                if (!$('#password-match-error').length) {
                    $(this).after('<small id="password-match-error" class="help-block text-danger">As senhas não coincidem</small>');
                }
            } else {
                $(this).removeClass('has-error');
                $('#password-match-error').remove();
            }
        });

        // Prevenir double submit
        $('#userForm').on('submit', function(e) {
            const submitBtn = $('#submitBtn');
            
            if (submitBtn.hasClass('disabled')) {
                e.preventDefault();
                return false;
            }
            
            // Validação
            const password = $('#password').val();
            const confirmation = $('#password_confirmation').val();
            
            if (password !== confirmation) {
                alert('As senhas não coincidem.');
                $('#password_confirmation').focus();
                return false;
            }
            
            submitBtn.addClass('disabled').html('<i class="fa fa-spinner fa-spin"></i> Salvando...');
            $('#loadingOverlay').show();
            $('form').addClass('form-loading');
        });

        // Foco inicial
        $('#nome').focus();
    });
</script>
@endsection