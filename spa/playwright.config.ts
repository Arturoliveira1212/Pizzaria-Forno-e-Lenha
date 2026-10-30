import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
	testDir: './e2e',
	fullyParallel: false,
	workers: 1,
	use: {
		baseURL: 'http://localhost:5174',
	},
	projects: [
		{
			name: 'autenticacao',
			testMatch: /autenticacao\.setup\.ts/,
		},
		{
			name: 'e2e',
			dependencies: ['autenticacao'],
			use: {
				storageState: 'e2e/.auth/usuario.json',
			},
		},
	],
	webServer: [
		{
			command: 'composer start:test',
			cwd: '../api',
			port: 8081,
			reuseExistingServer: false,
			timeout: 60_000,
		},
		{
			command: 'pnpm start --port 5174 --strictPort',
			port: 5174,
			reuseExistingServer: false,
			env: {
				VITE_API_URL: 'http://localhost:8081',
			},
		},
	],
});
