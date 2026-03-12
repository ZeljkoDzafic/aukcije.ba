/**
 * ===================================
 * VITEST CONFIGURATION
 * ===================================
 * Vue.js Component Tests
 */

import { defineConfig } from 'vitest/config';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    plugins: [vue()],

    test: {
        // Test directory
        dir: './tests/vue',

        // Globals (expect, describe, it available without import)
        globals: true,

        // Environment
        environment: 'jsdom',

        // Setup files
        setupFiles: ['./tests/vue/setup.ts'],

        // Coverage configuration
        coverage: {
            provider: 'v8',
            reporter: ['text', 'json', 'html'],
            reportsDirectory: './coverage/vue',
            include: [
                'resources/vue/**/*.{vue,ts,js}',
            ],
            exclude: [
                'resources/vue/app.js',
                '**/*.d.ts',
                '**/*.config.*',
            ],
            thresholds: {
                global: {
                    statements: 70,
                    branches: 70,
                    functions: 70,
                    lines: 70,
                },
            },
        },

        // Include patterns
        include: [
            'tests/vue/**/*.spec.ts',
            'tests/vue/**/*.spec.js',
        ],

        // Exclude patterns
        exclude: [
            '**/node_modules/**',
            '**/dist/**',
            '**/coverage/**',
        ],

        // Test timeout
        testTimeout: 10000,

        // Hook timeout
        hookTimeout: 10000,

        // Silent mode
        silent: false,

        // Restore mocks between tests
        restoreMocks: true,

        // Clear mocks between tests
        clearMocks: true,

        // Mock timers
        fakeTimers: {
            enableGlobally: true,
            now: Date.now(),
        },

        // Sequence options
        sequence: {
            shuffle: false,
            concurrent: false,
        },
    },

    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/vue'),
            '~': path.resolve(__dirname, './node_modules'),
        },
    },
});
