import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, catchError, throwError, map } from 'rxjs';

import { 
  Especialidade, 
  CreateEspecialidadeRequest, 
  UpdateEspecialidadeRequest,
  EspecialidadesResponse,
  EspecialidadeResponse,
  ApiError 
} from '../../shared/models';

@Injectable({
  providedIn: 'root'
})
export class EspecialidadesService {
  private readonly API_URL = 'http://localhost:8000/api/especialidades';

  constructor(private http: HttpClient) {}

  /**
   * Lista todas as especialidades
   */
  getEspecialidades(): Observable<EspecialidadesResponse> {
    return this.http.get<any>(this.API_URL)
      .pipe(
        map(response => {
          // Se a resposta é um array direto (Laravel resource collection)
          if (Array.isArray(response)) {
            return { success: true, data: response };
          }
          // Se a resposta tem formato wrapper
          return { success: true, data: response.data || response };
        }),
        catchError(this.handleError)
      );
  }

  /**
   * Obtém uma especialidade específica
   */
  getEspecialidade(id: number): Observable<EspecialidadeResponse> {
    return this.http.get<any>(`${this.API_URL}/${id}`)
      .pipe(
        map(response => {
          // Se a resposta tem um wrapper (como {data: especialidade})
          if (response.data) {
            return { success: true, data: response.data };
          }
          // Se a resposta é o objeto direto
          return { success: true, data: response };
        }),
        catchError(this.handleError)
      );
  }

  /**
   * Cria uma nova especialidade
   */
  createEspecialidade(especialidadeData: CreateEspecialidadeRequest): Observable<EspecialidadeResponse> {
    return this.http.post<any>(this.API_URL, especialidadeData)
      .pipe(
        map(response => {
          // Se a resposta tem um wrapper (como {data: especialidade})
          if (response.data) {
            return { success: true, data: response.data };
          }
          // Se a resposta é o objeto direto
          return { success: true, data: response };
        }),
        catchError(this.handleError)
      );
  }

  /**
   * Atualiza uma especialidade existente
   */
  updateEspecialidade(id: number, especialidadeData: UpdateEspecialidadeRequest): Observable<EspecialidadeResponse> {
    return this.http.put<any>(`${this.API_URL}/${id}`, especialidadeData)
      .pipe(
        map(response => {
          // Se a resposta tem um wrapper (como {data: especialidade})
          if (response.data) {
            return { success: true, data: response.data };
          }
          // Se a resposta é o objeto direto
          return { success: true, data: response };
        }),
        catchError(this.handleError)
      );
  }

  /**
   * Remove uma especialidade
   */
  deleteEspecialidade(id: number): Observable<{ success: boolean; message: string }> {
    return this.http.delete<any>(`${this.API_URL}/${id}`)
      .pipe(
        map(response => {
          // Laravel pode retornar diretamente {message: "..."} ou com wrapper
          const message = response.message || response.data?.message || 'Especialidade excluída com sucesso';
          return { success: true, message };
        }),
        catchError(this.handleError)
      );
  }

  /**
   * Manipula erros da API
   */
  private handleError = (error: any): Observable<never> => {
    console.error('Erro na API de especialidades:', error);
    
    const errorMessage = error.error?.message || 'Erro desconhecido';
    return throwError(() => ({
      success: false,
      message: errorMessage,
      errors: error.error?.errors
    } as ApiError));
  }
}