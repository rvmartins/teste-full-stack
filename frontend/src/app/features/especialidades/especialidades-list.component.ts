import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterLink } from '@angular/router';

import { EspecialidadesService } from '../../core/services/especialidades.service';
import { Especialidade } from '../../shared/models';
import { NavbarComponent } from '../../shared/components/navbar.component';

@Component({
  selector: 'app-especialidades-list',
  standalone: true,
  imports: [CommonModule, RouterLink, NavbarComponent],
  templateUrl: './especialidades-list.component.html',
  styleUrl: './especialidades-list.component.css'
})
export class EspecialidadesListComponent implements OnInit {
  especialidades: Especialidade[] = [];
  isLoading = false;
  errorMessage = '';
  deletingEspecialidadeId: number | null = null;

  constructor(
    private especialidadesService: EspecialidadesService,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.loadEspecialidades();
  }

  /**
   * Carrega lista de especialidades
   */
  private loadEspecialidades(): void {
    this.isLoading = true;
    this.errorMessage = '';

    this.especialidadesService.getEspecialidades().subscribe({
      next: (response) => {
        if (response.success) {
          this.especialidades = response.data;
        } else {
          this.errorMessage = response.message || 'Erro ao carregar especialidades';
        }
      },
      error: (error) => {
        console.error('Erro ao carregar especialidades:', error);
        this.errorMessage = error.message || 'Erro ao carregar especialidades';
      },
      complete: () => {
        this.isLoading = false;
      }
    });
  }

  /**
   * Confirma exclusão da especialidade
   */
  confirmDelete(especialidade: Especialidade): void {
    if (confirm(`Tem certeza que deseja excluir a especialidade "${especialidade.nome}"?`)) {
      this.deleteEspecialidade(especialidade.id);
    }
  }

  /**
   * Exclui especialidade
   */
  private deleteEspecialidade(especialidadeId: number): void {
    this.deletingEspecialidadeId = especialidadeId;

    this.especialidadesService.deleteEspecialidade(especialidadeId).subscribe({
      next: (response) => {
        if (response.success) {
          this.especialidades = this.especialidades.filter(e => e.id !== especialidadeId);
        } else {
          this.errorMessage = response.message || 'Erro ao excluir especialidade';
        }
      },
      error: (error) => {
        console.error('Erro ao excluir especialidade:', error);
        this.errorMessage = error.message || 'Erro ao excluir especialidade';
      },
      complete: () => {
        this.deletingEspecialidadeId = null;
      }
    });
  }

  /**
   * Formata data para exibição
   */
  formatDate(dateString?: string): string {
    if (!dateString) return 'N/A';
    
    try {
      const date = new Date(dateString);
      return date.toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      });
    } catch {
      return 'Data inválida';
    }
  }

  /**
   * Retorna classe CSS para status ativo/inativo
   */
  getStatusClass(ativa: boolean): string {
    return ativa ? 'badge bg-success' : 'badge bg-secondary';
  }

  /**
   * Retorna texto do status
   */
  getStatusText(ativa: boolean): string {
    return ativa ? 'Ativa' : 'Inativa';
  }
}