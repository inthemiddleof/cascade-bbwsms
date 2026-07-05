import { defineConfig } from "vite";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
	plugins: [tailwindcss()],
	server: {
		port: 5173,
		origin: "http://localhost:5173",
	},
	build: {
		outDir: "assets/dist",
		emptyOutDir: true,
		rollupOptions: {
			input: "./src/input.css",
		},
	},
});
