export function getPortalModeFromDataset(): 'admin' | 'org' {
  const container = document.getElementById('kb-portal-root');
  const mode = container?.getAttribute('data-mode');
  return mode === 'admin' ? 'admin' : 'org';
}
