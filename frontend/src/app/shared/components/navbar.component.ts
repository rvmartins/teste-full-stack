import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterLink, RouterLinkActive } from '@angular/router';

import { AuthService } from '../../core/auth/auth.service';
import { User } from '../models';

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [CommonModule, RouterLink, RouterLinkActive],
  template: `
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
      <div class="container-fluid">
        <!-- Brand -->
        <a class="navbar-brand" routerLink="/dashboard">
          <i class="bi bi-hospital me-2"></i>
          Sistema Médico
        </a>

        <!-- Toggle button para mobile -->
        <button 
          class="navbar-toggler" 
          type="button" 
          data-bs-toggle="collapse" 
          data-bs-target="#navbarNav"
          aria-controls="navbarNav" 
          aria-expanded="false" 
          aria-label="Toggle navigation"
          (click)="toggleMenu()"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu items -->
        <div class="collapse navbar-collapse" id="navbarNav" [class.show]="isMenuOpen">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <a 
                class="nav-link" 
                routerLink="/dashboard"
                routerLinkActive="active"
                [routerLinkActiveOptions]="{exact: true}"
              >
                <i class="bi bi-speedometer2 me-1"></i>
                Dashboard
              </a>
            </li>
            <li class="nav-item">
              <a 
                class="nav-link" 
                routerLink="/users"
                routerLinkActive="active"
              >
                <i class="bi bi-people me-1"></i>
                Usuários
              </a>
            </li>
            <!-- Futuros menus -->
            <li class="nav-item">
              <a 
                class="nav-link disabled" 
                href="#"
                tabindex="-1"
                aria-disabled="true"
              >
                <i class="bi bi-building me-1"></i>
                Entidades
                <small class="ms-1 text-muted">(Em breve)</small>
              </a>
            </li>
            <li class="nav-item">
              <a 
                class="nav-link" 
                routerLink="/especialidades"
                routerLinkActive="active"
              >
                <i class="bi bi-heart-pulse me-1"></i>
                Especialidades
              </a>
            </li>
          </ul>

          <!-- User menu -->
          <ul class="navbar-nav">
            <li class="nav-item dropdown" *ngIf="currentUser$ | async as user">
              <a 
                class="nav-link dropdown-toggle" 
                href="#" 
                id="navbarDropdown" 
                role="button" 
                data-bs-toggle="dropdown"
                aria-expanded="false"
                (click)="toggleUserMenu($event)"
              >
                <i class="bi bi-person-circle me-1"></i>
                {{ user.nome }}
              </a>
              <ul class="dropdown-menu" [class.show]="isUserMenuOpen" 
                  style="position: absolute; right: 0; left: auto; top: 100%; z-index: 1000; min-width: 250px; max-width: 90vw;"
              >
                <li>
                  <h6 class="dropdown-header">
                    <i class="bi bi-person me-1"></i>
                    {{ user.nome }}
                  </h6>
                </li>
                <li>
                  <span class="dropdown-item-text">
                    <small class="text-muted">{{ user.email }}</small>
                  </span>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <a class="dropdown-item disabled" href="#">
                    <i class="bi bi-gear me-2"></i>
                    Configurações
                    <small class="ms-auto text-muted">Em breve</small>
                  </a>
                </li>
                <li>
                  <a class="dropdown-item disabled" href="#">
                    <i class="bi bi-key me-2"></i>
                    Alterar Senha
                    <small class="ms-auto text-muted">Em breve</small>
                  </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <button class="dropdown-item text-danger" (click)="logout()">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    Sair
                  </button>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  `,
  styles: [`
    .navbar {
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .navbar-brand {
      font-weight: 600;
    }
    
    .nav-link.active {
      font-weight: 600;
      background-color: rgba(255, 255, 255, 0.1);
      border-radius: 0.375rem;
    }
    
    .nav-link:hover:not(.disabled) {
      background-color: rgba(255, 255, 255, 0.05);
      border-radius: 0.375rem;
    }
    
    .dropdown-menu {
      border: none;
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .dropdown-item:hover:not(.disabled) {
      background-color: #f8f9fa;
    }
    
    .dropdown-item.text-danger:hover {
      background-color: #f8d7da;
      color: #721c24 !important;
    }
    
    .nav-link.disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }
    
    .dropdown-menu.show {
      display: block;
    }
    
    @media (max-width: 991.98px) {
      .navbar-nav {
        padding: 0.5rem 0;
      }
      
      .dropdown-menu {
        position: relative !important;
        box-shadow: none;
        width: 100%;
        right: auto !important;
        left: 0 !important;
        top: auto !important;
        min-width: auto;
        max-width: none;
      }
    }
  `]
})
export class NavbarComponent {
  currentUser$ = this.authService.currentUser$;
  isMenuOpen = false;
  isUserMenuOpen = false;

  constructor(
    private authService: AuthService,
    private router: Router
  ) {}

  /**
   * Toggle do menu principal (mobile)
   */
  toggleMenu(): void {
    this.isMenuOpen = !this.isMenuOpen;
  }

  /**
   * Toggle do menu de usuário
   */
  toggleUserMenu(event: Event): void {
    event.preventDefault();
    this.isUserMenuOpen = !this.isUserMenuOpen;
  }

  /**
   * Logout do usuário
   */
  logout(): void {
    this.authService.logout().subscribe({
      next: () => {
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