import { Especialidade } from './especialidade.model';

export interface Entidade {
  id: number;
  razao_social: string;
  nome_fantasia?: string;
  cnpj: string;
  regional: string;
  data_inauguracao?: string;
  ativa: boolean;
  created_at?: string;
  updated_at?: string;
  especialidades?: Especialidade[];
  cnpj_formatado?: string;
  data_inauguracao_formatted?: string;
  status?: string;
}

export interface CreateEntidadeRequest {
  razao_social: string;
  nome_fantasia?: string;
  cnpj: string;
  regional: string;
  data_inauguracao?: string;
  ativa?: boolean;
  especialidades?: number[]; // IDs das especialidades
}

export interface UpdateEntidadeRequest {
  razao_social: string;
  nome_fantasia?: string;
  cnpj: string;
  regional: string;
  data_inauguracao?: string;
  ativa: boolean;
  especialidades?: number[]; // IDs das especialidades
}

export interface EntidadesResponse {
  success: boolean;
  data: Entidade[];
  message?: string;
}

export interface EntidadeResponse {
  success: boolean;
  data: Entidade;
  message?: string;
}

export interface RegionaisResponse {
  success: boolean;
  data: { [key: string]: string };
  message?: string;
}

// Opções de regionais
export const REGIONAIS_OPTIONS = [
  { value: 'Norte', label: 'Norte' },
  { value: 'Nordeste', label: 'Nordeste' },
  { value: 'Centro-Oeste', label: 'Centro-Oeste' },
  { value: 'Sudeste', label: 'Sudeste' },
  { value: 'Sul', label: 'Sul' }
];