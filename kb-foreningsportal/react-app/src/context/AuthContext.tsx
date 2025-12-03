import React, { createContext, useContext } from 'react';
import { useAuth } from '../hooks/useAuth';

const AuthContext = createContext<{ role: 'admin' | 'org' }>({ role: 'org' });

export const AuthProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const { role } = useAuth();
  return <AuthContext.Provider value={{ role }}>{children}</AuthContext.Provider>;
};

export function useAuthContext() {
  return useContext(AuthContext);
}
