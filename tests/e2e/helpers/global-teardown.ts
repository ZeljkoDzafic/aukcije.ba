/**
 * ===================================
 * PLAYWRIGHT GLOBAL TEARDOWN
 * ===================================
 * Runs after all tests
 */

import { FullConfig } from '@playwright/test';

async function globalTeardown(config: FullConfig) {
    void config;

    console.log('🧹 Starting Playwright global teardown...');

    try {
        console.log('✅ Global teardown complete');
    } catch (error) {
        console.error('❌ Global teardown failed:', error);
    }
}

export default globalTeardown;
