import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';

import { AuthService } from '../../core/auth/auth.service';
import { User } from '../../shared/models';

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './dashboard.component.html',
  styleUrl: './dashboard.component.css'
})
export class DashboardComponent implements OnInit {
  currentUser: User | null = null;
  isLoading = false;

  constructor(
    private authService: AuthService,
    private router: Router
  ) {}

  ngOnInit(): void {
    // Obtém dados do usuário atual
    this.authService.currentUser$.subscribe(user => {
      this.currentUser = user;
    });

    // Busca dados atualizados do usuário
    this.loadUserData();
  }

  /**
   * Carrega dados do usuário
   */
  private loadUserData(): void {
    if (this.authService.isAuthenticatedValue()) {
      this.isLoading = true;
      this.authService.getCurrentUser().subscribe({
        next: (user) => {
          console.log('Dados do usuário carregados:', user);
        },
        error: (error) => {
          console.error('Erro ao carregar dados do usuário:', error);
        },
        complete: () => {
          this.isLoading = false;
        }
      });
    }
  }

  /**
   * Realiza logout
   */
  logout(): void {
    this.authService.logout().subscribe({
      next: () => {
        console.log('Logout realizado com sucesso');
        this.router.navigate(['/auth/login']);
      },
      error: (error) => {
        console.error('Erro no logout:', error);
        // Mesmo com erro, redireciona para login
        this.router.navigate(['/auth/login']);
      }
    });
  }
}