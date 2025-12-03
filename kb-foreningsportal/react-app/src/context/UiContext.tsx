import React, { createContext, useContext, useState } from 'react';

const UiContext = createContext<{ sidebarOpen: boolean; toggleSidebar: () => void }>({
  sidebarOpen: false,
  toggleSidebar: () => undefined,
});

export const UiProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [sidebarOpen, setSidebarOpen] = useState(false);
  const toggleSidebar = () => setSidebarOpen((prev) => !prev);

  return <UiContext.Provider value={{ sidebarOpen, toggleSidebar }}>{children}</UiContext.Provider>;
};

export function useUi() {
  return useContext(UiContext);
}
