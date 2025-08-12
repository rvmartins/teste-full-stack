import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterLink } from '@angular/router';

import { EntidadesService } from '../../core/services/entidades.service';
import { Entidade } from '../../shared/models';
import { NavbarComponent } from '../../shared/components/navbar.component';

@Component({
  selector: 'app-entidades-list',
  standalone: true,
  imports: [CommonModule, RouterLink, NavbarComponent],
  templateUrl: './entidades-list.component.html',
  styleUrl: './entidades-list.component.css'
})
export class EntidadesListComponent implements OnInit {
  entidades: Entidade[] = [];
  isLoading = false;
  errorMessage = '';
  deletingEntidadeId: number | null = null;

  constructor(
    private entidadesService: EntidadesService,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.loadEntidades();
  }

  /**
   * Carrega lista de entidades
   */
  private loadEntidades(): void {
    this.isLoading = true;
    this.errorMessage = '';

    this.entidadesService.getEntidades().subscribe({
      next: (response) => {
        if (response.success) {
          this.entidades = response.data;
        } else {
          this.errorMessage = response.message || 'Erro ao carregar entidades';
        }
      },
      error: (error) => {
        console.error('Erro ao carregar entidades:', error);
        this.errorMessage = error.message || 'Erro ao carregar entidades';
      },
      complete: () => {
        this.isLoading = false;
      }
    });
  }

  /**
   * Confirma exclusão da entidade
   */
  confirmDelete(entidade: Entidade): void {
    if (confirm(`Tem certeza que deseja excluir a entidade "${entidade.razao_social}"?`)) {
      this.deleteEntidade(entidade.id);
    }
  }

  /**
   * Exclui entidade
   */
  private deleteEntidade(entidadeId: number): void {
    this.deletingEntidadeId = entidadeId;

    this.entidadesService.deleteEntidade(entidadeId).subscribe({
      next: (response) => {
        if (response.success) {
          this.entidades = this.entidades.filter(e => e.id !== entidadeId);
        } else {
          this.errorMessage = response.message || 'Erro ao excluir entidade';
        }
      },
      error: (error) => {
        console.error('Erro ao excluir entidade:', error);
        this.errorMessage = error.message || 'Erro ao excluir entidade';
      },
      complete: () => {
        this.deletingEntidadeId = null;
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
   * Formata CNPJ para exibição
   */
  formatCnpj(cnpj: string): string {
    if (!cnpj) return 'N/A';
    
    // Remove todos os caracteres não numéricos
    const cleanCnpj = cnpj.replace(/\D/g, '');
    
    // Aplica a máscara se tiver 14 dígitos
    if (cleanCnpj.length === 14) {
      return cleanCnpj.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
    }
    
    return cnpj;
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

  /**
   * Retorna cor da badge para regional
   */
  getRegionalClass(regional: string): string {
    const colors = {
      'Norte': 'bg-primary',
      'Nordeste': 'bg-warning',
      'Centro-Oeste': 'bg-success',
      'Sudeste': 'bg-info',
      'Sul': 'bg-secondary'
    };
    return `badge ${colors[regional as keyof typeof colors] || 'bg-dark'}`;
  }

  /**
   * Exibe especialidades da entidade (primeiras 3)
   */
  getEspecialidadesResumidas(entidade: Entidade): string {
    if (!entidade.especialidades || entidade.especialidades.length === 0) {
      return 'Nenhuma especialidade';
    }

    const primeiras = entidade.especialidades.slice(0, 3);
    const nomes = primeiras.map(e => e.nome).join(', ');
    
    if (entidade.especialidades.length > 3) {
      return `${nomes} e mais ${entidade.especialidades.length - 3}...`;
    }
    
    return nomes;
  }
}