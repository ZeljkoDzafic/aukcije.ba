/**
 * ===================================
 * PLAYWRIGHT GLOBAL TEARDOWN
 * ===================================
 * Runs after all tests
 */

import { FullConfig } from '@playwright/test';

async function globalTeardown(config: FullConfig) {
    const { baseURL } = config.projects[0].use;

    console.log('🧹 Starting Playwright global teardown...');

    try {
        // Optionally: Clean up test data
        // await cleanupTestData(baseURL);

        console.log('✅ Global teardown complete');
    } catch (error) {
        console.error('❌ Global teardown failed:', error);
    }
}

/**
 * Clean up test data after all tests
 */
async function cleanupTestData(baseURL: string) {
    console.log('🗑️ Cleaning up test data...');

    // This would call an API endpoint to clean up test data
    // Only enabled in test environment

    const response = await fetch(baseURL + '/_test/cleanup', {
        method: 'POST',
    });

    if (response.ok) {
        console.log('✅ Test data cleaned up');
    }
}

export default globalTeardown;
