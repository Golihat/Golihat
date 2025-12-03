import { apiClient } from './client';

export type Campaign = {
  id: number;
  name: string;
  status: string;
};

export function fetchCampaigns() {
  return apiClient.get<Campaign[]>('/campaigns');
}
