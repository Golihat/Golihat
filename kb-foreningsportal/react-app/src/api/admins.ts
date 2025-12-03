import { apiClient } from './client';

export type Admin = {
  id: number;
  name: string;
  commission_model: string;
};

export function fetchAdmins() {
  return apiClient.get<Admin[]>('/admins');
}
