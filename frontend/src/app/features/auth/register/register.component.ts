import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators, AbstractControl } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';

import { AuthService } from '../../../core/auth/auth.service';
import { RegisterRequest, ApiError } from '../../../shared/models';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterLink],
  templateUrl: './register.component.html',
  styleUrl: './register.component.css'
})
export class RegisterComponent implements OnInit {
  registerForm: FormGroup;
  isLoading = false;
  errorMessage = '';
  fieldErrors: { [key: string]: string[] } = {};

  constructor(
    private fb: FormBuilder,
    private authService: AuthService,
    private router: Router
  ) {
    this.registerForm = this.fb.group({
      nome: ['', [Validators.required, Validators.minLength(2), Validators.maxLength(100)]],
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(6)]],
      password_confirmation: ['', [Validators.required]]
    }, {
      validators: this.passwordMatchValidator
    });
  }

  ngOnInit(): void {
    // Limpa mensagens de erro quando usuário digita
    this.registerForm.valueChanges.subscribe(() => {
      this.errorMessage = '';
      this.fieldErrors = {};
    });
  }

  /**
   * Validador personalizado para confirmar senha
   */
  private passwordMatchValidator(control: AbstractControl): { [key: string]: boolean } | null {
    const password = control.get('password');
    const passwordConfirmation = control.get('password_confirmation');

    if (!password || !passwordConfirmation) {
      return null;
    }

    return password.value !== passwordConfirmation.value ? { passwordMismatch: true } : null;
  }

  /**
   * Submete o formulário de registro
   */
  onSubmit(): void {
    if (this.registerForm.invalid) {
      this.markFormGroupTouched();
      return;
    }

    this.isLoading = true;
    this.errorMessage = '';
    this.fieldErrors = {};

    // Desabilitar formulário durante carregamento
    this.registerForm.disable();

    const userData: RegisterRequest = this.registerForm.value;

    this.authService.register(userData).subscribe({
      next: (response) => {
        console.log('Registro realizado com sucesso:', response);
        this.router.navigate(['/dashboard']);
      },
      error: (error: ApiError) => {
        console.error('Erro no registro:', error);
        this.isLoading = false;
        this.errorMessage = error.message;
        this.fieldErrors = error.errors || {};
        // Reabilitar formulário após erro
        this.registerForm.enable();
      },
      complete: () => {
        this.isLoading = false;
      }
    });
  }

  /**
   * Marca todos os campos como tocados para exibir validações
   */
  private markFormGroupTouched(): void {
    Object.keys(this.registerForm.controls).forEach(field => {
      const control = this.registerForm.get(field);
      control?.markAsTouched({ onlySelf: true });
    });
  }

  /**
   * Verifica se campo tem erro
   */
  hasFieldError(fieldName: string): boolean {
    const field = this.registerForm.get(fieldName);
    const formErrors = this.registerForm.errors;
    
    return !!(field?.invalid && field?.touched) || 
           !!this.fieldErrors[fieldName] ||
           (fieldName === 'password_confirmation' && formErrors?.['passwordMismatch'] && field?.touched);
  }

  /**
   * Obtém mensagem de erro do campo
   */
  getFieldError(fieldName: string): string {
    const field = this.registerForm.get(fieldName);
    const formErrors = this.registerForm.errors;
    
    // Erros de validação do backend
    if (this.fieldErrors[fieldName]) {
      return this.fieldErrors[fieldName][0];
    }

    // Erro de confirmação de senha
    if (fieldName === 'password_confirmation' && formErrors?.['passwordMismatch'] && field?.touched) {
      return 'As senhas não coincidem';
    }

    // Erros de validação do frontend
    if (field?.invalid && field?.touched) {
      if (field.errors?.['required']) {
        return `${this.getFieldLabel(fieldName)} é obrigatório`;
      }
      if (field.errors?.['email']) {
        return 'Email deve ter um formato válido';
      }
      if (field.errors?.['minlength']) {
        return `${this.getFieldLabel(fieldName)} deve ter pelo menos ${field.errors['minlength'].requiredLength} caracteres`;
      }
      if (field.errors?.['maxlength']) {
        return `${this.getFieldLabel(fieldName)} deve ter no máximo ${field.errors['maxlength'].requiredLength} caracteres`;
      }
    }

    return '';
  }

  /**
   * Obtém label do campo
   */
  private getFieldLabel(fieldName: string): string {
    const labels: { [key: string]: string } = {
      nome: 'Nome',
      email: 'Email',
      password: 'Senha',
      password_confirmation: 'Confirmação da senha'
    };
    return labels[fieldName] || fieldName;
  }
}