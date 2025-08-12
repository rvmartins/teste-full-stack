import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router, ActivatedRoute, RouterLink } from '@angular/router';

import { EspecialidadesService } from '../../core/services/especialidades.service';
import { Especialidade, CreateEspecialidadeRequest, UpdateEspecialidadeRequest, ApiError } from '../../shared/models';
import { NavbarComponent } from '../../shared/components/navbar.component';

@Component({
  selector: 'app-especialidades-form',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterLink, NavbarComponent],
  templateUrl: './especialidades-form.component.html',
  styleUrl: './especialidades-form.component.css'
})
export class EspecialidadesFormComponent implements OnInit {
  especialidadeForm: FormGroup;
  isLoading = false;
  initialLoading = false;
  errorMessage = '';
  fieldErrors: { [key: string]: string[] } = {};
  
  isEditMode = false;
  especialidadeId: number | null = null;
  currentEspecialidade: Especialidade | null = null;

  constructor(
    private fb: FormBuilder,
    private especialidadesService: EspecialidadesService,
    private router: Router,
    private route: ActivatedRoute
  ) {
    this.especialidadeForm = this.createForm();
  }

  ngOnInit(): void {
    // Verifica se é modo de edição
    const id = this.route.snapshot.paramMap.get('id');
    
    if (id && id !== 'new') {
      this.isEditMode = true;
      this.especialidadeId = parseInt(id, 10);
      this.loadEspecialidade();
    }

    // Limpa erros quando usuário digita
    this.especialidadeForm.valueChanges.subscribe(() => {
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
      descricao: ['', [Validators.maxLength(500)]],
      ativa: [true]
    });
  }

  /**
   * Carrega dados da especialidade para edição
   */
  private loadEspecialidade(): void {
    if (!this.especialidadeId) return;

    this.initialLoading = true;
    this.errorMessage = '';

    this.especialidadesService.getEspecialidade(this.especialidadeId).subscribe({
      next: (response) => {
        if (response.success) {
          this.currentEspecialidade = response.data;
          this.setupEditForm();
        } else {
          this.errorMessage = response.message || 'Erro ao carregar especialidade';
        }
      },
      error: (error) => {
        console.error('Erro ao carregar especialidade:', error);
        this.errorMessage = error.message || 'Erro ao carregar especialidade';
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
    if (!this.currentEspecialidade) return;

    this.especialidadeForm.patchValue({
      nome: this.currentEspecialidade.nome,
      descricao: this.currentEspecialidade.descricao || '',
      ativa: this.currentEspecialidade.ativa
    });
  }

  /**
   * Submete o formulário
   */
  onSubmit(): void {
    if (this.especialidadeForm.invalid) {
      this.markFormGroupTouched();
      return;
    }

    this.isLoading = true;
    this.errorMessage = '';
    this.fieldErrors = {};
    this.especialidadeForm.disable();

    if (this.isEditMode) {
      this.updateEspecialidade();
    } else {
      this.createEspecialidade();
    }
  }

  /**
   * Cria nova especialidade
   */
  private createEspecialidade(): void {
    const especialidadeData: CreateEspecialidadeRequest = {
      nome: this.especialidadeForm.value.nome,
      descricao: this.especialidadeForm.value.descricao,
      ativa: this.especialidadeForm.value.ativa
    };

    this.especialidadesService.createEspecialidade(especialidadeData).subscribe({
      next: (response) => {
        if (response.success) {
          this.router.navigate(['/especialidades']);
        } else {
          this.handleError(response.message || 'Erro ao criar especialidade');
        }
      },
      error: (error) => {
        this.handleError(error.message || 'Erro ao criar especialidade', error.errors);
      }
    });
  }

  /**
   * Atualiza especialidade existente
   */
  private updateEspecialidade(): void {
    if (!this.especialidadeId) return;

    const especialidadeData: UpdateEspecialidadeRequest = {
      nome: this.especialidadeForm.value.nome,
      descricao: this.especialidadeForm.value.descricao,
      ativa: this.especialidadeForm.value.ativa
    };

    this.especialidadesService.updateEspecialidade(this.especialidadeId, especialidadeData).subscribe({
      next: (response) => {
        if (response.success) {
          this.router.navigate(['/especialidades']);
        } else {
          this.handleError(response.message || 'Erro ao atualizar especialidade');
        }
      },
      error: (error) => {
        console.error('Update error:', error);
        this.handleError(error.message || 'Erro ao atualizar especialidade', error.errors);
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
    this.especialidadeForm.enable();
  }

  /**
   * Marca todos os campos como tocados
   */
  private markFormGroupTouched(): void {
    Object.keys(this.especialidadeForm.controls).forEach(field => {
      const control = this.especialidadeForm.get(field);
      control?.markAsTouched({ onlySelf: true });
    });
  }

  /**
   * Verifica se campo tem erro
   */
  hasFieldError(fieldName: string): boolean {
    const field = this.especialidadeForm.get(fieldName);
    return !!(field?.invalid && field?.touched) || !!this.fieldErrors[fieldName];
  }

  /**
   * Obtém mensagem de erro do campo
   */
  getFieldError(fieldName: string): string {
    const field = this.especialidadeForm.get(fieldName);
    
    // Erros do backend
    if (this.fieldErrors[fieldName]) {
      return this.fieldErrors[fieldName][0];
    }

    // Erros de validação do frontend
    if (field?.invalid && field?.touched) {
      if (field.errors?.['required']) {
        return `${this.getFieldLabel(fieldName)} é obrigatório`;
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
      descricao: 'Descrição'
    };
    return labels[fieldName] || fieldName;
  }

  /**
   * Verifica se o formulário é válido para submissão
   */
  isFormValid(): boolean {
    return this.especialidadeForm.valid;
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

  /**
   * Obtém título da página
   */
  getPageTitle(): string {
    return this.isEditMode ? 'Editar Especialidade' : 'Nova Especialidade';
  }

  /**
   * Obtém texto do botão de submit
   */
  getSubmitButtonText(): string {
    return this.isEditMode ? 'Atualizar' : 'Criar';
  }
}