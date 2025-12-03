export const apiBase = '/wp-json/kb-forening/v1';

async function request<T>(url: string, options?: RequestInit): Promise<T> {
  const response = await fetch(url, {
    headers: { 'Content-Type': 'application/json' },
    ...options,
  });

  if (!response.ok) {
    throw new Error(`API error ${response.status}`);
  }

  return response.json() as Promise<T>;
}

export const apiClient = {
  get: <T>(path: string) => request<T>(`${apiBase}${path}`),
  post: <T>(path: string, body: unknown) => request<T>(`${apiBase}${path}`, { method: 'POST', body: JSON.stringify(body) }),
};
