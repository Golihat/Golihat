import { useEffect, useState } from 'react';

export type Notification = {
  id: string;
  message: string;
  level: 'info' | 'warning' | 'error';
};

export function useNotifications() {
  const [items, setItems] = useState<Notification[]>([]);

  useEffect(() => {
    setItems([
      { id: 'welcome', message: 'Välkommen till KB Föreningsportal!', level: 'info' },
    ]);
  }, []);

  return { items };
}
