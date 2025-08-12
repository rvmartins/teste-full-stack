import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router, ActivatedRoute, RouterLink } from '@angular/router';

import { EntidadesService } from '../../core/services/entidades.service';
import { EspecialidadesService } from '../../core/services/especialidades.service';
import { Entidade, CreateEntidadeRequest, UpdateEntidadeRequest, Especialidade, REGIONAIS_OPTIONS, ApiError } from '../../shared/models';
import { NavbarComponent } from '../../shared/components/navbar.component';
import { CnpjMaskDirective } from '../../shared/directives/cnpj-mask.directive';

@Component({
  selector: 'app-entidades-form',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterLink, NavbarComponent, CnpjMaskDirective],
  templateUrl: './entidades-form.component.html',
  styleUrl: './entidades-form.component.css'
})
export class EntidadesFormComponent implements OnInit {
  entidadeForm: FormGroup;
  isLoading = false;
  initialLoading = false;
  errorMessage = '';
  fieldErrors: { [key: string]: string[] } = {};
  
  isEditMode = false;
  entidadeId: number | null = null;
  currentEntidade: Entidade | null = null;

  // Dados para os selects
  regionaisOptions = REGIONAIS_OPTIONS;
  especialidades: Especialidade[] = [];
  especialidadesLoading = false;

  constructor(
    private fb: FormBuilder,
    private entidadesService: EntidadesService,
    private especialidadesService: EspecialidadesService,
    private router: Router,
    private route: ActivatedRoute
  ) {
    this.entidadeForm = this.createForm();
  }

  ngOnInit(): void {
    // Carrega especialidades disponíveis
    this.loadEspecialidades();

    // Verifica se é modo de edição
    const id = this.route.snapshot.paramMap.get('id');
    
    if (id && id !== 'new') {
      this.isEditMode = true;
      this.entidadeId = parseInt(id, 10);
      this.loadEntidade();
    }

    // Limpa erros quando usuário digita (DESABILITADO TEMPORARIAMENTE PARA DEBUG)
    // this.entidadeForm.valueChanges.subscribe(() => {
    //   console.log('🟠 Form value changed - limpando erros');
    //   this.errorMessage = '';
    //   this.fieldErrors = {};
    // });
  }

  /**
   * Cria o formulário reativo
   */
  private createForm(): FormGroup {
    return this.fb.group({
      razao_social: ['', [Validators.required, Validators.minLength(2), Validators.maxLength(255)]],
      nome_fantasia: ['', [Validators.required, Validators.maxLength(255)]],
      cnpj: ['', [Validators.required, this.cnpjValidator]],
      regional: ['', [Validators.required]],
      data_inauguracao: ['', [Validators.required]],
      ativa: [true],
      especialidades: [[], [this.minEspecialidadesValidator]] // Array de IDs selecionados com validação mínima
    });
  }

  /**
   * Validador personalizado para CNPJ
   */
  private cnpjValidator(control: any) {
    if (!control.value) return null;
    
    const cnpj = control.value.replace(/\D/g, '');
    
    // CNPJ deve ter 14 dígitos
    if (cnpj.length !== 14) {
      return { cnpjInvalid: true };
    }
    
    // Verifica se todos os dígitos são iguais
    if (/^(.)\1{13}$/.test(cnpj)) {
      return { cnpjInvalid: true };
    }
    
    // Validação dos dígitos verificadores (algoritmo oficial)
    // Primeiro dígito verificador
    let soma = 0;
    let peso = 5;
    for (let i = 0; i < 12; i++) {
      soma += parseInt(cnpj[i]) * peso;
      peso = peso === 2 ? 9 : peso - 1;
    }
    let resto = soma % 11;
    let digito1 = resto < 2 ? 0 : 11 - resto;
    
    if (parseInt(cnpj[12]) !== digito1) {
      return { cnpjInvalid: true };
    }
    
    // Segundo dígito verificador
    soma = 0;
    peso = 6;
    for (let i = 0; i < 13; i++) {
      soma += parseInt(cnpj[i]) * peso;
      peso = peso === 2 ? 9 : peso - 1;
    }
    resto = soma % 11;
    let digito2 = resto < 2 ? 0 : 11 - resto;
    
    if (parseInt(cnpj[13]) !== digito2) {
      return { cnpjInvalid: true };
    }
    
    return null;
  }

  /**
   * Validador personalizado para especialidades (mínimo 2)
   */
  private minEspecialidadesValidator(control: any) {
    if (!control.value || control.value.length < 2) {
      return { minEspecialidades: true };
    }
    return null;
  }


  /**
   * Carrega especialidades disponíveis
   */
  private loadEspecialidades(): void {
    this.especialidadesLoading = true;

    this.especialidadesService.getEspecialidades().subscribe({
      next: (response) => {
        if (response.success) {
          this.especialidades = response.data.filter(e => e.ativa); // Só especialidades ativas
        }
      },
      error: (error) => {
        console.error('Erro ao carregar especialidades:', error);
      },
      complete: () => {
        this.especialidadesLoading = false;
      }
    });
  }

  /**
   * Carrega dados da entidade para edição
   */
  private loadEntidade(): void {
    if (!this.entidadeId) return;

    this.initialLoading = true;
    this.errorMessage = '';

    this.entidadesService.getEntidade(this.entidadeId).subscribe({
      next: (response) => {
        if (response.success) {
          this.currentEntidade = response.data;
          this.setupEditForm();
        } else {
          this.errorMessage = response.message || 'Erro ao carregar entidade';
        }
      },
      error: (error) => {
        console.error('Erro ao carregar entidade:', error);
        this.errorMessage = error.message || 'Erro ao carregar entidade';
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
    if (!this.currentEntidade) return;

    // Formatar data para input date
    let dataFormatted = '';
    if (this.currentEntidade.data_inauguracao) {
      const date = new Date(this.currentEntidade.data_inauguracao);
      dataFormatted = date.toISOString().split('T')[0];
    }

    // IDs das especialidades vinculadas
    const especialidadesIds = this.currentEntidade.especialidades ? 
      this.currentEntidade.especialidades.map(e => e.id) : [];

    this.entidadeForm.patchValue({
      razao_social: this.currentEntidade.razao_social,
      nome_fantasia: this.currentEntidade.nome_fantasia || '',
      cnpj: this.currentEntidade.cnpj,
      regional: this.currentEntidade.regional,
      data_inauguracao: dataFormatted,
      ativa: this.currentEntidade.ativa,
      especialidades: especialidadesIds
    });

    // Aplica a formatação visual nos campos
    setTimeout(() => {
      // Formatar CNPJ
      const cnpjInput = document.getElementById('cnpj') as HTMLInputElement;
      if (cnpjInput && this.currentEntidade?.cnpj) {
        cnpjInput.value = this.formatCnpjForDisplay(this.currentEntidade.cnpj);
      }
    });
  }

  /**
   * Formata CNPJ para exibição
   */
  private formatCnpjForDisplay(cnpj: string): string {
    if (!cnpj) return '';
    
    const cleaned = cnpj.replace(/\D/g, '');
    if (cleaned.length === 14) {
      return cleaned.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
    }
    return cnpj;
  }


  /**
   * Submete o formulário
   */
  onSubmit(): void {
    if (this.entidadeForm.invalid) {
      this.markFormGroupTouched();
      return;
    }

    this.isLoading = true;
    this.errorMessage = '';
    this.fieldErrors = {};
    this.entidadeForm.disable();

    if (this.isEditMode) {
      this.updateEntidade();
    } else {
      this.createEntidade();
    }
  }

  /**
   * Cria nova entidade
   */
  private createEntidade(): void {
    const formValue = this.entidadeForm.value;
    
    const entidadeData: CreateEntidadeRequest = {
      razao_social: formValue.razao_social,
      nome_fantasia: formValue.nome_fantasia,
      cnpj: formValue.cnpj,
      regional: formValue.regional,
      data_inauguracao: formValue.data_inauguracao,
      ativa: formValue.ativa,
      especialidades: formValue.especialidades || []
    };

    console.log('Dados sendo enviados para API:', entidadeData);

    this.entidadesService.createEntidade(entidadeData).subscribe({
      next: (response) => {
        console.log('Resposta da API:', response);
        if (response.success) {
          this.router.navigate(['/entidades']);
        } else {
          this.handleError(response.message || 'Erro ao criar entidade');
        }
      },
      error: (error) => {
        console.log('🔴 ERRO CAPTURADO NO COMPONENTE:', error);
        console.log('🔴 Tipo do erro:', typeof error);
        console.log('🔴 Propriedades do erro:', Object.keys(error));
        
        // Tratamento específico para erro de CNPJ duplicado
        if (error.errors?.cnpj) {
          const cnpjError = error.errors.cnpj[0];
          console.log('🔴 Erro de CNPJ detectado:', cnpjError);
          if (cnpjError.includes('já está cadastrado') || cnpjError.includes('CNPJ inválido') || cnpjError.includes('unique')) {
            this.handleError('CNPJ inválido ou já cadastrado no sistema', { cnpj: error.errors.cnpj });
            return;
          }
        }
        
        // Se há erros de validação, mostra eles
        if (error.errors) {
          console.log('🔴 Mostrando erros de validação:', error.errors);
          this.handleError(error.message || 'Erro de validação', error.errors);
          return;
        }
        
        console.log('🔴 Mostrando erro genérico');
        this.handleError(error.message || 'Erro ao criar entidade');
      }
    });
  }

  /**
   * Atualiza entidade existente
   */
  private updateEntidade(): void {
    if (!this.entidadeId) return;

    const formValue = this.entidadeForm.value;
    
    const entidadeData: UpdateEntidadeRequest = {
      razao_social: formValue.razao_social,
      nome_fantasia: formValue.nome_fantasia,
      cnpj: formValue.cnpj,
      regional: formValue.regional,
      data_inauguracao: formValue.data_inauguracao,
      ativa: formValue.ativa,
      especialidades: formValue.especialidades || []
    };

    this.entidadesService.updateEntidade(this.entidadeId, entidadeData).subscribe({
      next: (response) => {
        if (response.success) {
          this.router.navigate(['/entidades']);
        } else {
          this.handleError(response.message || 'Erro ao atualizar entidade');
        }
      },
      error: (error) => {
        console.error('Update error:', error);
        this.handleError(error.message || 'Erro ao atualizar entidade', error.errors);
      }
    });
  }

  /**
   * Manipula erros
   */
  private handleError(message: string, errors?: { [key: string]: string[] }): void {
    console.log('🟡 handleError chamado com:', { message, errors });
    this.isLoading = false;
    this.errorMessage = message;
    this.fieldErrors = errors || {};
    this.entidadeForm.enable();
    
    console.log('🟡 Após setError - errorMessage:', this.errorMessage);
    console.log('🟡 Após setError - fieldErrors:', this.fieldErrors);
  }

  /**
   * Marca todos os campos como tocados
   */
  private markFormGroupTouched(): void {
    Object.keys(this.entidadeForm.controls).forEach(field => {
      const control = this.entidadeForm.get(field);
      control?.markAsTouched({ onlySelf: true });
    });
  }

  /**
   * Verifica se campo tem erro
   */
  hasFieldError(fieldName: string): boolean {
    const field = this.entidadeForm.get(fieldName);
    return !!(field?.invalid && field?.touched) || !!this.fieldErrors[fieldName];
  }

  /**
   * Obtém mensagem de erro do campo
   */
  getFieldError(fieldName: string): string {
    const field = this.entidadeForm.get(fieldName);
    
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
      if (field.errors?.['cnpjInvalid']) {
        return 'CNPJ inválido - verifique os números';
      }
      if (field.errors?.['cnpjExists']) {
        return 'Este CNPJ já está cadastrado no sistema';
      }
      if (field.errors?.['minEspecialidades']) {
        return 'Selecione pelo menos 2 especialidades';
      }
    }

    return '';
  }

  /**
   * Obtém label do campo
   */
  private getFieldLabel(fieldName: string): string {
    const labels: { [key: string]: string } = {
      razao_social: 'Razão Social',
      nome_fantasia: 'Nome Fantasia',
      cnpj: 'CNPJ',
      regional: 'Regional',
      data_inauguracao: 'Data de Inauguração',
      especialidades: 'Especialidades'
    };
    return labels[fieldName] || fieldName;
  }

  /**
   * Verifica se o formulário é válido para submissão
   */
  isFormValid(): boolean {
    return this.entidadeForm.valid;
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
   * Formata data para formato brasileiro (dd/mm/yyyy)
   */
  formatDateBR(dateString?: string): string {
    if (!dateString) return '';
    
    try {
      const date = new Date(dateString);
      const day = date.getDate().toString().padStart(2, '0');
      const month = (date.getMonth() + 1).toString().padStart(2, '0');
      const year = date.getFullYear().toString();
      return `${day}/${month}/${year}`;
    } catch {
      return '';
    }
  }

  /**
   * Obtém título da página
   */
  getPageTitle(): string {
    return this.isEditMode ? 'Editar Entidade' : 'Nova Entidade';
  }

  /**
   * Obtém texto do botão de submit
   */
  getSubmitButtonText(): string {
    return this.isEditMode ? 'Atualizar' : 'Criar';
  }



  /**
   * Manipula mudança na seleção de especialidades
   */
  onEspecialidadeChange(especialidadeId: number, event: any): void {
    const especialidades = this.entidadeForm.get('especialidades')?.value || [];
    
    if (event.target.checked) {
      // Adiciona a especialidade se não estiver na lista
      if (!especialidades.includes(especialidadeId)) {
        especialidades.push(especialidadeId);
      }
    } else {
      // Remove a especialidade da lista
      const index = especialidades.indexOf(especialidadeId);
      if (index > -1) {
        especialidades.splice(index, 1);
      }
    }

    this.entidadeForm.patchValue({ especialidades });
  }

  /**
   * Verifica se especialidade está selecionada
   */
  isEspecialidadeSelecionada(especialidadeId: number): boolean {
    const especialidades = this.entidadeForm.get('especialidades')?.value || [];
    return especialidades.includes(especialidadeId);
  }
}