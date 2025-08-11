@extends('layouts.app')

@section('title', 'Editar Usuário')

@section('content')
<div class="page-header">
    <h1>
        <i class="fa fa-edit"></i> Editar Usuário
        <small>{{ $user->nome }}</small>
    </h1>
</div>

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <form method="POST" action="{{ route('users.update', $user->id) }}" id="userForm" class="no-loading">
            {{ csrf_field() }}
            {{ method_field('PUT') }}

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
                               value="{{ old('nome', $user->nome) }}" 
                               required
                               maxlength="255">
                    </div>

                    <div class="form-group">
                        <label for="email">
                            Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" 
                               class="form-control" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $user->email) }}" 
                               required
                               maxlength="255">
                    </div>

                    <div class="form-group">
                        <label for="password">
                            Nova Senha
                        </label>
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               minlength="6"
                               placeholder="Deixe em branco para manter a senha atual">
                        <small class="help-block">Deixe em branco se não quiser alterar a senha</small>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">
                            Confirmar Nova Senha
                        </label>
                        <input type="password" 
                               class="form-control" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               minlength="6"
                               placeholder="Digite a nova senha novamente">
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" 
                                       name="ativo" 
                                       value="1" 
                                       {{ old('ativo', $user->ativo) ? 'checked' : '' }}> 
                                <strong>Usuário Ativo</strong>
                            </label>
                            <small class="help-block">Usuários inativos não conseguem fazer login</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                    <i class="fa fa-save"></i> Atualizar Usuário
                </button>
                <a class="btn btn-default btn-lg" href="{{ route('users.index') }}">
                    <i class="fa fa-arrow-left"></i> Cancelar
                </a>
                <a class="btn btn-info btn-lg" href="{{ route('users.show', $user->id) }}">
                    <i class="fa fa-eye"></i> Visualizar
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
            
            if (password && password !== confirmation) {
                alert('As senhas não coincidem.');
                $('#password_confirmation').focus();
                return false;
            }
            
            submitBtn.addClass('disabled').html('<i class="fa fa-spinner fa-spin"></i> Salvando...');
            $('#loadingOverlay').show();
            $('form').addClass('form-loading');
        });
    });
</script>
@endsection