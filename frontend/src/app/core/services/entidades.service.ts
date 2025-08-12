import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, catchError, throwError, map } from 'rxjs';

import { 
  Entidade, 
  CreateEntidadeRequest, 
  UpdateEntidadeRequest,
  EntidadesResponse,
  EntidadeResponse,
  RegionaisResponse,
  ApiError 
} from '../../shared/models';

@Injectable({
  providedIn: 'root'
})
export class EntidadesService {
  private readonly API_URL = 'http://localhost:8000/api/entidades';

  constructor(private http: HttpClient) {}

  /**
   * Lista todas as entidades
   */
  getEntidades(): Observable<EntidadesResponse> {
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
   * Obtém uma entidade específica
   */
  getEntidade(id: number): Observable<EntidadeResponse> {
    return this.http.get<any>(`${this.API_URL}/${id}`)
      .pipe(
        map(response => {
          // Se a resposta tem um wrapper (como {data: entidade})
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
   * Cria uma nova entidade
   */
  createEntidade(entidadeData: CreateEntidadeRequest): Observable<EntidadeResponse> {
    return this.http.post<any>(this.API_URL, entidadeData)
      .pipe(
        map(response => {
          // Se a resposta tem um wrapper (como {data: entidade})
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
   * Atualiza uma entidade existente
   */
  updateEntidade(id: number, entidadeData: UpdateEntidadeRequest): Observable<EntidadeResponse> {
    return this.http.put<any>(`${this.API_URL}/${id}`, entidadeData)
      .pipe(
        map(response => {
          // Se a resposta tem um wrapper (como {data: entidade})
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
   * Remove uma entidade
   */
  deleteEntidade(id: number): Observable<{ success: boolean; message: string }> {
    return this.http.delete<any>(`${this.API_URL}/${id}`)
      .pipe(
        map(response => {
          // Laravel pode retornar diretamente {message: "..."} ou com wrapper
          const message = response.message || response.data?.message || 'Entidade excluída com sucesso';
          return { success: true, message };
        }),
        catchError(this.handleError)
      );
  }

  /**
   * Obtém opções de regionais
   */
  getRegionais(): Observable<RegionaisResponse> {
    // Como as regionais são estáticas, podemos retornar diretamente
    const regionais = {
      'Norte': 'Norte',
      'Nordeste': 'Nordeste',
      'Centro-Oeste': 'Centro-Oeste',
      'Sudeste': 'Sudeste',
      'Sul': 'Sul'
    };

    return new Observable(observer => {
      observer.next({ success: true, data: regionais });
      observer.complete();
    });
  }

  /**
   * Obtém entidades por regional
   */
  getEntidadesRegionais(): Observable<any> {
    return this.http.get<any>(`${this.API_URL}-regionais`)
      .pipe(
        map(response => ({ success: true, data: response.data || response })),
        catchError(this.handleError)
      );
  }

  /**
   * Obtém especialidades por entidade
   */
  getEntidadesEspecialidades(): Observable<any> {
    return this.http.get<any>(`${this.API_URL}-especialidades`)
      .pipe(
        map(response => ({ success: true, data: response.data || response })),
        catchError(this.handleError)
      );
  }

  /**
   * Verifica se um CNPJ já existe no sistema
   */
  checkCnpjExists(cnpj: string, excludeId?: number): Observable<boolean> {
    const cleanCnpj = cnpj.replace(/\D/g, '');
    return this.getEntidades().pipe(
      map(response => {
        if (response.success && response.data) {
          return response.data.some(entidade => {
            const entidadeCnpj = entidade.cnpj.replace(/\D/g, '');
            return entidadeCnpj === cleanCnpj && (!excludeId || entidade.id !== excludeId);
          });
        }
        return false;
      }),
      catchError(() => {
        // Em caso de erro, assumimos que não existe para não bloquear o usuário
        return [false];
      })
    );
  }

  /**
   * Manipula erros da API
   */
  private handleError = (error: any): Observable<never> => {
    console.error('Erro na API de entidades:', error);
    
    const errorMessage = error.error?.message || 'Erro desconhecido';
    return throwError(() => ({
      success: false,
      message: errorMessage,
      errors: error.error?.errors
    } as ApiError));
  }
}