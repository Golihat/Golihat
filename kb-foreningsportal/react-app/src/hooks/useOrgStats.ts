import { useEffect, useState } from 'react';
import { fetchStatsSummary, StatsSummary } from '../api/stats';

export function useOrgStats() {
  const [data, setData] = useState<StatsSummary | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    fetchStatsSummary()
      .then((stats) => setData(stats))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, []);

  return { data, loading, error };
}
