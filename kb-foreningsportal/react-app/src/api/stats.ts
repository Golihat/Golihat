import { apiClient } from './client';

export type StatsSummary = {
  sales: number;
  commission: number;
};

export function fetchStatsSummary() {
  return apiClient.get<StatsSummary>('/stats/summary');
}
