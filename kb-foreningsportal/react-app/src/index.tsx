import React from 'react';
import { createRoot } from 'react-dom/client';
import App from './App';
import './styles/globals.css';
import './styles/theme.css';

const container = document.getElementById('kb-portal-root');

if (container) {
  const mode = container.getAttribute('data-mode') === 'admin' ? 'admin' : 'org';
  const root = createRoot(container);
  root.render(<App mode={mode} />);
}
