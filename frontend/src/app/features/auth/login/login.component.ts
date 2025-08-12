import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';

import { AuthService } from '../../../core/auth/auth.service';
import { LoginRequest, ApiError } from '../../../shared/models';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterLink],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css'
})
export class LoginComponent implements OnInit {
  loginForm: FormGroup;
  isLoading = false;
  errorMessage = '';
  fieldErrors: { [key: string]: string[] } = {};
  showPassword = false;

  constructor(
    private fb: FormBuilder,
    private authService: AuthService,
    private router: Router
  ) {
    this.loginForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(6)]]
    });
  }

  ngOnInit(): void {
    // Limpa mensagens de erro quando usuário digita
    this.loginForm.valueChanges.subscribe(() => {
      this.errorMessage = '';
      this.fieldErrors = {};
    });
  }

  /**
   * Submete o formulário de login
   */
  onSubmit(): void {
    if (this.loginForm.invalid) {
      this.markFormGroupTouched();
      return;
    }

    this.isLoading = true;
    this.errorMessage = '';
    this.fieldErrors = {};

    // Desabilitar formulário durante carregamento
    this.loginForm.disable();

    const credentials: LoginRequest = this.loginForm.value;

    this.authService.login(credentials).subscribe({
      next: (response) => {
        console.log('Login realizado com sucesso:', response);
        this.router.navigate(['/dashboard']);
      },
      error: (error: ApiError) => {
        console.error('Erro no login:', error);
        this.isLoading = false;
        this.errorMessage = error.message;
        this.fieldErrors = error.errors || {};
        // Reabilitar formulário após erro
        this.loginForm.enable();
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
    Object.keys(this.loginForm.controls).forEach(field => {
      const control = this.loginForm.get(field);
      control?.markAsTouched({ onlySelf: true });
    });
  }

  /**
   * Verifica se campo tem erro
   */
  hasFieldError(fieldName: string): boolean {
    const field = this.loginForm.get(fieldName);
    return !!(field?.invalid && field?.touched) || !!this.fieldErrors[fieldName];
  }

  /**
   * Obtém mensagem de erro do campo
   */
  getFieldError(fieldName: string): string {
    const field = this.loginForm.get(fieldName);
    
    // Erros de validação do backend
    if (this.fieldErrors[fieldName]) {
      return this.fieldErrors[fieldName][0];
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
    }

    return '';
  }

  /**
   * Obtém label do campo
   */
  private getFieldLabel(fieldName: string): string {
    const labels: { [key: string]: string } = {
      email: 'Email',
      password: 'Senha'
    };
    return labels[fieldName] || fieldName;
  }

  /**
   * Alterna visibilidade da senha
   */
  togglePasswordVisibility(): void {
    this.showPassword = !this.showPassword;
  }
}