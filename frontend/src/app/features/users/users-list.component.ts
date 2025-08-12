import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterLink } from '@angular/router';

import { UsersService } from '../../core/services/users.service';
import { User } from '../../shared/models';
import { NavbarComponent } from '../../shared/components/navbar.component';

@Component({
  selector: 'app-users-list',
  standalone: true,
  imports: [CommonModule, RouterLink, NavbarComponent],
  templateUrl: './users-list.component.html',
  styleUrl: './users-list.component.css'
})
export class UsersListComponent implements OnInit {
  users: User[] = [];
  isLoading = false;
  errorMessage = '';
  deletingUserId: number | null = null;

  constructor(
    private usersService: UsersService,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.loadUsers();
  }

  /**
   * Carrega lista de usuários
   */
  private loadUsers(): void {
    this.isLoading = true;
    this.errorMessage = '';

    this.usersService.getUsers().subscribe({
      next: (response) => {
        if (response.success) {
          this.users = response.data;
        } else {
          this.errorMessage = response.message || 'Erro ao carregar usuários';
        }
      },
      error: (error) => {
        console.error('Erro ao carregar usuários:', error);
        this.errorMessage = error.message || 'Erro ao carregar usuários';
      },
      complete: () => {
        this.isLoading = false;
      }
    });
  }

  /**
   * Confirma exclusão do usuário
   */
  confirmDelete(user: User): void {
    if (confirm(`Tem certeza que deseja excluir o usuário "${user.nome}"?`)) {
      this.deleteUser(user.id);
    }
  }

  /**
   * Exclui usuário
   */
  private deleteUser(userId: number): void {
    this.deletingUserId = userId;

    this.usersService.deleteUser(userId).subscribe({
      next: (response) => {
        if (response.success) {
          this.users = this.users.filter(u => u.id !== userId);
        } else {
          this.errorMessage = response.message || 'Erro ao excluir usuário';
        }
      },
      error: (error) => {
        console.error('Erro ao excluir usuário:', error);
        this.errorMessage = error.message || 'Erro ao excluir usuário';
      },
      complete: () => {
        this.deletingUserId = null;
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
}