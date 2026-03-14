/**
 * ===================================
 * PLAYWRIGHT MOBILE RESPONSIVE TESTS
 * ===================================
 * Focused responsive smoke checks against the real UI.
 */

import { test, expect, devices } from '@playwright/test';

const mobileDevices = {
    'iPhone 12': devices['iPhone 12'],
    'Pixel 5': devices['Pixel 5'],
    'iPad Mini': devices['iPad Mini'],
};

test.describe('Mobile Responsive Tests', () => {
    for (const [deviceName, deviceConfig] of Object.entries(mobileDevices)) {
        test(`${deviceName} homepage and navigation`, async ({ browser }) => {
            const context = await browser.newContext(deviceConfig);
            const page = await context.newPage();

            await page.goto('/');

            await expect(page.locator('header')).toBeVisible();
            await expect(page.locator('main')).toBeVisible();
            await expect(page.locator('footer')).toBeVisible();

            if ((deviceConfig.viewport?.width ?? 0) < 768) {
                const hamburger = page.locator('button[aria-label="Otvori navigaciju"], button[aria-label="Otvori meni"]');
                await expect(hamburger).toBeVisible();
                await hamburger.click();
                await expect(page.locator('nav[aria-label="Mobile navigation"]')).toBeVisible();
            } else {
                await expect(page.locator('nav[aria-label="Main navigation"]').first()).toBeVisible();
            }

            const hasHorizontalScroll = await page.evaluate(() => {
                return document.documentElement.scrollWidth > document.documentElement.clientWidth;
            });

            expect(hasHorizontalScroll).toBeFalsy();

            await context.close();
        });

        test(`${deviceName} login form is touch friendly`, async ({ browser }) => {
            const context = await browser.newContext(deviceConfig);
            const page = await context.newPage();

            await page.goto('/login');

            const emailInput = page.locator('input[name="email"]');
            const submitButton = page.locator('button[type="submit"]');

            await expect(emailInput).toBeVisible();
            await expect(submitButton).toBeVisible();

            const emailBox = await emailInput.boundingBox();
            const buttonBox = await submitButton.boundingBox();

            if (emailBox) {
                expect(emailBox.height).toBeGreaterThanOrEqual(40);
            }

            if (buttonBox) {
                expect(buttonBox.height).toBeGreaterThanOrEqual(40);
            }

            await context.close();
        });
    }
});
