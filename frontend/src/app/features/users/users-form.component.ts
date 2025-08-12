import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators, AbstractControl } from '@angular/forms';
import { Router, ActivatedRoute, RouterLink } from '@angular/router';

import { UsersService, CreateUserRequest, UpdateUserRequest } from '../../core/services/users.service';
import { User, ApiError } from '../../shared/models';
import { NavbarComponent } from '../../shared/components/navbar.component';

@Component({
  selector: 'app-users-form',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterLink, NavbarComponent],
  templateUrl: './users-form.component.html',
  styleUrl: './users-form.component.css'
})
export class UsersFormComponent implements OnInit {
  userForm: FormGroup;
  isLoading = false;
  initialLoading = false;
  errorMessage = '';
  fieldErrors: { [key: string]: string[] } = {};
  
  isEditMode = false;
  userId: number | null = null;
  currentUser: User | null = null;

  constructor(
    private fb: FormBuilder,
    private usersService: UsersService,
    private router: Router,
    private route: ActivatedRoute
  ) {
    this.userForm = this.createForm();
  }

  ngOnInit(): void {
    // Verifica se é modo de edição
    const id = this.route.snapshot.paramMap.get('id');
    
    if (id && id !== 'new') {
      this.isEditMode = true;
      this.userId = parseInt(id, 10);
      this.loadUser();
    }

    // Limpa erros quando usuário digita
    this.userForm.valueChanges.subscribe(() => {
      this.errorMessage = '';
      this.fieldErrors = {};
    });
  }

  /**
   * Cria o formulário reativo
   */
  private createForm(): FormGroup {
    return this.fb.group({
      nome: ['', [Validators.required, Validators.minLength(2), Validators.maxLength(100)]],
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(6)]],
      password_confirmation: ['', [Validators.required]],
      ativo: [true]
    }, {
      validators: this.passwordMatchValidator
    });
  }

  /**
   * Carrega dados do usuário para edição
   */
  private loadUser(): void {
    if (!this.userId) return;

    this.initialLoading = true;
    this.errorMessage = '';

    this.usersService.getUser(this.userId).subscribe({
      next: (response) => {
        if (response.success) {
          this.currentUser = response.data;
          this.setupEditForm();
        } else {
          this.errorMessage = response.message || 'Erro ao carregar usuário';
        }
      },
      error: (error) => {
        console.error('Erro ao carregar usuário:', error);
        this.errorMessage = error.message || 'Erro ao carregar usuário';
      },
      complete: () => {
        this.initialLoading = false;
      }
    });
  }

  /**
   * Configura formulário para modo de edição
   */
  private setupEditForm(): void {
    if (!this.currentUser) return;

    // Inclui campos opcionais de senha para edição
    this.userForm = this.fb.group({
      nome: [this.currentUser.nome, [Validators.required, Validators.minLength(2), Validators.maxLength(100)]],
      email: [this.currentUser.email, [Validators.required, Validators.email]],
      password: ['', [Validators.minLength(6)]],
      password_confirmation: [''],
      ativo: [this.currentUser.ativo]
    }, {
      validators: this.passwordMatchValidator
    });
  }

  /**
   * Validador personalizado para confirmar senha
   */
  private passwordMatchValidator = (control: AbstractControl): { [key: string]: boolean } | null => {
    const password = control.get('password');
    const passwordConfirmation = control.get('password_confirmation');

    if (!password || !passwordConfirmation) {
      return null;
    }

    // No modo de edição, só valida se algum campo de senha foi preenchido
    if (this.isEditMode && !password.value && !passwordConfirmation.value) {
      return null;
    }

    // Se uma senha foi preenchida, ambas devem coincidir
    if ((password.value || passwordConfirmation.value) && password.value !== passwordConfirmation.value) {
      return { passwordMismatch: true };
    }

    return null;
  }

  /**
   * Submete o formulário
   */
  onSubmit(): void {
    if (this.userForm.invalid) {
      this.markFormGroupTouched();
      return;
    }

    this.isLoading = true;
    this.errorMessage = '';
    this.fieldErrors = {};
    this.userForm.disable();

    if (this.isEditMode) {
      this.updateUser();
    } else {
      this.createUser();
    }
  }

  /**
   * Cria novo usuário
   */
  private createUser(): void {
    const userData: CreateUserRequest = this.userForm.value;

    this.usersService.createUser(userData).subscribe({
      next: (response) => {
        if (response.success) {
          this.router.navigate(['/users']);
        } else {
          this.handleError(response.message || 'Erro ao criar usuário');
        }
      },
      error: (error) => {
        this.handleError(error.message || 'Erro ao criar usuário', error.errors);
      }
    });
  }

  /**
   * Atualiza usuário existente
   */
  private updateUser(): void {
    if (!this.userId) return;

    const formValue = this.userForm.value;
    const userData: any = {
      nome: formValue.nome,
      email: formValue.email,
      ativo: formValue.ativo
    };

    // Inclui senha apenas se foi preenchida
    if (formValue.password && formValue.password.trim() !== '') {
      userData.password = formValue.password;
      userData.password_confirmation = formValue.password_confirmation;
    }

    this.usersService.updateUser(this.userId, userData).subscribe({
      next: (response) => {
        if (response.success) {
          this.router.navigate(['/users']);
        } else {
          this.handleError(response.message || 'Erro ao atualizar usuário');
        }
      },
      error: (error) => {
        console.error('Update error:', error);
        this.handleError(error.message || 'Erro ao atualizar usuário', error.errors);
      }
    });
  }

  /**
   * Manipula erros
   */
  private handleError(message: string, errors?: { [key: string]: string[] }): void {
    this.isLoading = false;
    this.errorMessage = message;
    this.fieldErrors = errors || {};
    this.userForm.enable();
  }

  /**
   * Marca todos os campos como tocados
   */
  private markFormGroupTouched(): void {
    Object.keys(this.userForm.controls).forEach(field => {
      const control = this.userForm.get(field);
      control?.markAsTouched({ onlySelf: true });
    });
  }

  /**
   * Verifica se campo tem erro
   */
  hasFieldError(fieldName: string): boolean {
    const field = this.userForm.get(fieldName);
    const formErrors = this.userForm.errors;
    
    return !!(field?.invalid && field?.touched) || 
           !!this.fieldErrors[fieldName] ||
           (fieldName === 'password_confirmation' && formErrors?.['passwordMismatch'] && field?.touched);
  }

  /**
   * Obtém mensagem de erro do campo
   */
  getFieldError(fieldName: string): string {
    const field = this.userForm.get(fieldName);
    const formErrors = this.userForm.errors;
    
    // Erros do backend
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

  /**
   * Verifica se o formulário é válido para submissão
   */
  isFormValid(): boolean {
    // Campos obrigatórios básicos
    const nome = this.userForm.get('nome');
    const email = this.userForm.get('email');
    
    if (!nome?.valid || !email?.valid) {
      return false;
    }

    // Validação de senha para novo usuário
    if (!this.isEditMode) {
      const password = this.userForm.get('password');
      const passwordConfirmation = this.userForm.get('password_confirmation');
      
      if (!password?.valid || !passwordConfirmation?.valid) {
        return false;
      }
    }

    // Validação de confirmação de senha
    if (this.userForm.errors?.['passwordMismatch']) {
      return false;
    }

    return true;
  }

  /**
   * Formata data para exibição
   */
  formatDate(dateString?: string): string {
    if (!dateString) return 'N/A';
    
    try {
      const date = new Date(dateString);
      return date.toLocaleString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    } catch {
      return 'Data inválida';
    }
  }
}