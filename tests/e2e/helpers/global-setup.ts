/**
 * ===================================
 * PLAYWRIGHT GLOBAL SETUP
 * ===================================
 * Runs before all tests
 */

import { chromium, FullConfig } from '@playwright/test';

async function globalSetup(config: FullConfig) {
    const { baseURL } = config.projects[0].use;

    console.log('🚀 Starting Playwright global setup...');
    console.log('📍 Base URL:', baseURL);

    // Launch browser for setup
    const browser = await chromium.launch();
    const context = await browser.newContext();
    const page = await context.newPage();

    try {
        // Check if application is running
        console.log('🔍 Checking application health...');
        await page.goto(baseURL + '/health', { timeout: 10000 });
        console.log('✅ Application is running');

        // Optionally: Run database migrations via API
        // This would require a special test-only endpoint
        // await page.goto(baseURL + '/_migrate', { timeout: 30000 });
        // console.log('✅ Database migrated');

        // Optionally: Seed test data
        // await seedTestData(page, baseURL);

        console.log('✅ Global setup complete');
    } catch (error) {
        console.error('❌ Global setup failed:', error);
        throw error;
    } finally {
        await browser.close();
    }
}

/**
 * Seed test data for E2E tests
 */
async function seedTestData(page: any, baseURL: string) {
    console.log('🌱 Seeding test data...');

    // This would typically call an API endpoint that seeds test data
    // For example: POST /_test/seed
    // Only enabled in test environment

    await page.request.post(baseURL + '/_test/seed', {
        data: {
            users: true,
            auctions: true,
            categories: true,
        },
    });

    console.log('✅ Test data seeded');
}

export default globalSetup;
