import { Routes } from '@angular/router';
import { AuthGuard } from './core/guards/auth.guard';
import { GuestGuard } from './core/guards/guest.guard';

export const routes: Routes = [
  // Redirecionar rota raiz para dashboard
  {
    path: '',
    redirectTo: '/dashboard',
    pathMatch: 'full'
  },

  // Rotas de autenticação (apenas para usuários não autenticados)
  {
    path: 'auth',
    canActivate: [GuestGuard],
    children: [
      {
        path: 'login',
        loadComponent: () => import('./features/auth/login/login.component').then(m => m.LoginComponent)
      },
      {
        path: 'register',
        loadComponent: () => import('./features/auth/register/register.component').then(m => m.RegisterComponent)
      },
      {
        path: '',
        redirectTo: 'login',
        pathMatch: 'full'
      }
    ]
  },

  // Rotas protegidas (apenas para usuários autenticados)
  {
    path: 'dashboard',
    canActivate: [AuthGuard],
    loadComponent: () => import('./features/dashboard/dashboard.component').then(m => m.DashboardComponent)
  },

  // Rotas de usuários
  {
    path: 'users',
    canActivate: [AuthGuard],
    children: [
      {
        path: '',
        loadComponent: () => import('./features/users/users-list.component').then(m => m.UsersListComponent)
      },
      {
        path: 'new',
        loadComponent: () => import('./features/users/users-form.component').then(m => m.UsersFormComponent)
      },
      {
        path: ':id/edit',
        loadComponent: () => import('./features/users/users-form.component').then(m => m.UsersFormComponent)
      }
    ]
  },

  // Rotas de especialidades
  {
    path: 'especialidades',
    canActivate: [AuthGuard],
    children: [
      {
        path: '',
        loadComponent: () => import('./features/especialidades/especialidades-list.component').then(m => m.EspecialidadesListComponent)
      },
      {
        path: 'new',
        loadComponent: () => import('./features/especialidades/especialidades-form.component').then(m => m.EspecialidadesFormComponent)
      },
      {
        path: ':id/edit',
        loadComponent: () => import('./features/especialidades/especialidades-form.component').then(m => m.EspecialidadesFormComponent)
      }
    ]
  },

  // Rota 404 - deve ser a última
  {
    path: '**',
    redirectTo: '/dashboard'
  }
];
