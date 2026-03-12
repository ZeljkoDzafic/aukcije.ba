import { defineConfig, devices } from '@playwright/test';

/**
 * Playwright Configuration for Aukcije.ba
 * 
 * See: https://playwright.dev/docs/test-configuration
 */
export default defineConfig({
    // Test directory
    testDir: './tests/e2e',

    // Timeout for each test
    timeout: 30 * 1000,

    // Timeout for expect assertions
    expect: {
        timeout: 5000,
    },

    // Run tests in parallel
    fullyParallel: true,

    // Number of workers (CPU cores by default)
    workers: process.env.CI ? 2 : undefined,

    // Fail the build on CI if you accidentally left test.only in the source code
    forbidOnly: !!process.env.CI,

    // Retry on CI only
    retries: process.env.CI ? 2 : 0,

    // Reporter configuration
    reporter: [
        ['html', { outputFolder: 'playwright-report', open: 'never' }],
        ['junit', { outputFile: 'playwright-report/junit.xml' }],
        ['list', { printSteps: true }],
        ...(process.env.CI ? [['github'] as const] : []),
    ],

    // Shared settings for all the projects below
    use: {
        // Base URL for all tests
        baseURL: process.env.BASE_URL || 'http://localhost:8000',

        // Collect trace when retrying the failed test
        trace: 'on-first-retry',

        // Screenshot on failure
        screenshot: 'only-on-failure',

        // Video on failure
        video: 'retain-on-failure',

        // Browser context options
        viewport: { width: 1280, height: 720 },

        // Actionability options
        actionTimeout: 10000,

        // Locale and timezone
        locale: 'bs-BA',
        timezoneId: 'Europe/Sarajevo',

        // Permissions for geolocation tests (if needed)
        permissions: [],

        // Color scheme
        colorScheme: 'light',
    },

    // Configure projects for major browsers
    projects: [
        {
            name: 'chromium',
            use: { ...devices['Desktop Chrome'] },
        },

        {
            name: 'firefox',
            use: { ...devices['Desktop Firefox'] },
        },

        {
            name: 'webkit',
            use: { ...devices['Desktop Safari'] },
        },

        // Mobile browsers
        {
            name: 'Mobile Chrome',
            use: { ...devices['Pixel 5'] },
        },

        {
            name: 'Mobile Safari',
            use: { ...devices['iPhone 12'] },
        },

        // Test against branded browsers
        // {
        //   name: 'Microsoft Edge',
        //   use: { ...devices['Desktop Edge'], channel: 'msedge' },
        // },
        // {
        //   name: 'Google Chrome',
        //   use: { ...devices['Desktop Chrome'], channel: 'chrome' },
        // },
    ],

    // Folder for test artifacts
    outputDir: 'playwright-report/results/',

    // Run setup before all tests
    globalSetup: require.resolve('./tests/e2e/helpers/global-setup'),

    // Run teardown after all tests
    globalTeardown: require.resolve('./tests/e2e/helpers/global-teardown'),
});
