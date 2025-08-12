export interface Especialidade {
  id: number;
  nome: string;
  descricao?: string;
  ativa: boolean;
  created_at?: string;
  updated_at?: string;
  entidades?: any[]; // Para relacionamentos futuros
}

export interface CreateEspecialidadeRequest {
  nome: string;
  descricao?: string;
  ativa?: boolean;
}

export interface UpdateEspecialidadeRequest {
  nome: string;
  descricao?: string;
  ativa: boolean;
}

export interface EspecialidadesResponse {
  success: boolean;
  data: Especialidade[];
  message?: string;
}

export interface EspecialidadeResponse {
  success: boolean;
  data: Especialidade;
  message?: string;
}