import { apiClient } from './client';

export type Material = {
  id: number;
  title: string;
  url: string;
};

export function fetchMaterials(orgId: number) {
  return apiClient.get<Material[]>(`/materials?org=${orgId}`);
}
