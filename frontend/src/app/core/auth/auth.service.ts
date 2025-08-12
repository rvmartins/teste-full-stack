import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, BehaviorSubject, map, tap, catchError, throwError } from 'rxjs';

import { 
  User, 
  LoginRequest, 
  RegisterRequest, 
  AuthResponse, 
  ChangePasswordRequest,
  ApiError 
} from '../../shared/models';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private readonly API_URL   = 'http://localhost:8000/api';
  private readonly TOKEN_KEY = 'auth_token';
  private readonly USER_KEY  = 'current_user';

  private currentUserSubject = new BehaviorSubject<User | null>(this.getUserFromStorage());
  public currentUser$ = this.currentUserSubject.asObservable();

  private isAuthenticatedSubject = new BehaviorSubject<boolean>(this.hasValidToken());
  public isAuthenticated$ = this.isAuthenticatedSubject.asObservable();

  constructor(private http: HttpClient) {}

  /**
   * Realiza login do usuário
   */
  login(credentials: LoginRequest): Observable<AuthResponse> {
    return this.http.post<AuthResponse>(`${this.API_URL}/login`, credentials)
      .pipe(
        tap(response => {
          if (response.success) {
            this.setAuthData(response.data.token, response.data.user);
          }
        }),
        catchError(this.handleError)
      );
  }

  /**
   * Registra novo usuário
   */
  register(userData: RegisterRequest): Observable<AuthResponse> {
    return this.http.post<AuthResponse>(`${this.API_URL}/register`, userData)
      .pipe(
        tap(response => {
          if (response.success) {
            this.setAuthData(response.data.token, response.data.user);
          }
        }),
        catchError(this.handleError)
      );
  }

  /**
   * Realiza logout
   */
  logout(): Observable<any> {
    return this.http.post(`${this.API_URL}/logout`, {})
      .pipe(
        tap(() => this.clearAuthData()),
        catchError(() => {
          // Mesmo com erro na API, limpa os dados locais
          this.clearAuthData();
          return throwError(() => new Error('Erro no logout'));
        })
      );
  }

  /**
   * Obtém dados do usuário atual
   */
  getCurrentUser(): Observable<User> {
    return this.http.get<{ success: boolean; data: User }>(`${this.API_URL}/me`)
      .pipe(
        map(response => response.data),
        tap(user => {
          this.currentUserSubject.next(user);
          localStorage.setItem(this.USER_KEY, JSON.stringify(user));
        }),
        catchError(this.handleError)
      );
  }

  /**
   * Atualiza token de autenticação
   */
  refreshToken(): Observable<AuthResponse> {
    return this.http.post<AuthResponse>(`${this.API_URL}/refresh`, {})
      .pipe(
        tap(response => {
          if (response.success) {
            this.setToken(response.data.token);
          }
        }),
        catchError(this.handleError)
      );
  }

  /**
   * Altera senha do usuário
   */
  changePassword(passwordData: ChangePasswordRequest): Observable<any> {
    return this.http.post(`${this.API_URL}/change-password`, passwordData)
      .pipe(catchError(this.handleError));
  }

  /**
   * Verifica se há token válido
   */
  private hasValidToken(): boolean {
    const token = localStorage.getItem(this.TOKEN_KEY);
    return !!token;
  }

  /**
   * Obtém usuário do localStorage
   */
  private getUserFromStorage(): User | null {
    const userJson = localStorage.getItem(this.USER_KEY);
    return userJson ? JSON.parse(userJson) : null;
  }

  /**
   * Define dados de autenticação
   */
  private setAuthData(token: string, user: User): void {
    localStorage.setItem(this.TOKEN_KEY, token);
    localStorage.setItem(this.USER_KEY, JSON.stringify(user));
    this.currentUserSubject.next(user);
    this.isAuthenticatedSubject.next(true);
  }

  /**
   * Define apenas o token
   */
  private setToken(token: string): void {
    localStorage.setItem(this.TOKEN_KEY, token);
  }

  /**
   * Limpa dados de autenticação
   */
  private clearAuthData(): void {
    localStorage.removeItem(this.TOKEN_KEY);
    localStorage.removeItem(this.USER_KEY);
    this.currentUserSubject.next(null);
    this.isAuthenticatedSubject.next(false);
  }

  /**
   * Obtém token atual
   */
  getToken(): string | null {
    return localStorage.getItem(this.TOKEN_KEY);
  }

  /**
   * Obtém usuário atual (síncrono)
   */
  getCurrentUserValue(): User | null {
    return this.currentUserSubject.value;
  }

  /**
   * Verifica se usuário está autenticado (síncrono)
   */
  isAuthenticatedValue(): boolean {
    return this.isAuthenticatedSubject.value;
  }

  /**
   * Manipula erros da API
   */
  private handleError = (error: any): Observable<never> => {
    console.error('Erro na API:', error);
    
    // Se erro 401, limpa dados de auth
    if (error.status === 401) {
      this.clearAuthData();
    }

    // Retorna mensagem de erro formatada
    const errorMessage = error.error?.message || 'Erro desconhecido';
    return throwError(() => ({
      success: false,
      message: errorMessage,
      errors: error.error?.errors
    } as ApiError));
  }
}