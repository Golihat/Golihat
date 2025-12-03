import { apiClient } from './client';

export type Org = {
  id: number;
  name: string;
};

export function fetchOrgs() {
  return apiClient.get<Org[]>('/orgs');
}
