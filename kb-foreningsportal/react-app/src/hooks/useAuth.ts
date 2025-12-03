import { useEffect, useState } from 'react';
import { getPortalModeFromDataset } from '../api/auth';

export function useAuth() {
  const [role, setRole] = useState<'admin' | 'org'>('org');

  useEffect(() => {
    setRole(getPortalModeFromDataset());
  }, []);

  return { role };
}
