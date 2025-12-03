import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import path from 'path';

export default defineConfig({
  plugins: [react()],
  build: {
    outDir: path.resolve(__dirname, '../assets'),
    emptyOutDir: false,
    rollupOptions: {
      input: path.resolve(__dirname, 'src/index.tsx'),
      output: {
        entryFileNames: 'js/portal-[name].bundle.js',
        chunkFileNames: 'js/[name].chunk.js',
        assetFileNames: (assetInfo) => {
          if (assetInfo.name && assetInfo.name.endsWith('.css')) {
            return 'css/portal.css';
          }
          return 'assets/[name][extname]';
        },
      },
    },
  },
});
