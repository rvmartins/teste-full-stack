import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, catchError, throwError, map } from 'rxjs';

import { User, ApiError } from '../../shared/models';

export interface CreateUserRequest {
  nome: string;
  email: string;
  password: string;
  password_confirmation: string;
  ativo?: boolean;
}

export interface UpdateUserRequest {
  nome: string;
  email: string;
  ativo: boolean;
}

export interface UsersResponse {
  success: boolean;
  data: User[];
  message?: string;
}

export interface UserResponse {
  success: boolean;
  data: User;
  message?: string;
}

@Injectable({
  providedIn: 'root'
})
export class UsersService {
  private readonly API_URL = 'http://localhost:8000/api/users';

  constructor(private http: HttpClient) {}

  /**
   * Lista todos os usuários
   */
  getUsers(): Observable<UsersResponse> {
    return this.http.get<User[]>(this.API_URL)
      .pipe(
        map(users => ({ success: true, data: users })),
        catchError(this.handleError)
      );
  }

  /**
   * Obtém um usuário específico
   */
  getUser(id: number): Observable<UserResponse> {
    return this.http.get<User>(`${this.API_URL}/${id}`)
      .pipe(
        map(user => ({ success: true, data: user })),
        catchError(this.handleError)
      );
  }

  /**
   * Cria um novo usuário
   */
  createUser(userData: CreateUserRequest): Observable<UserResponse> {
    return this.http.post<User>(this.API_URL, userData)
      .pipe(
        map(user => ({ success: true, data: user })),
        catchError(this.handleError)
      );
  }

  /**
   * Atualiza um usuário existente
   */
  updateUser(id: number, userData: UpdateUserRequest): Observable<UserResponse> {
    return this.http.put<User>(`${this.API_URL}/${id}`, userData)
      .pipe(
        map(user => ({ success: true, data: user })),
        catchError(this.handleError)
      );
  }

  /**
   * Remove um usuário
   */
  deleteUser(id: number): Observable<{ success: boolean; message: string }> {
    return this.http.delete<{ message: string }>(`${this.API_URL}/${id}`)
      .pipe(
        map(response => ({ success: true, message: response.message })),
        catchError(this.handleError)
      );
  }

  /**
   * Manipula erros da API
   */
  private handleError = (error: any): Observable<never> => {
    console.error('Erro na API de usuários:', error);
    
    const errorMessage = error.error?.message || 'Erro desconhecido';
    return throwError(() => ({
      success: false,
      message: errorMessage,
      errors: error.error?.errors
    } as ApiError));
  }
}