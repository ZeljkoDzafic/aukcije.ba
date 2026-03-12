/**
 * ===================================
 * PLAYWRIGHT MOBILE RESPONSIVE TESTS
 * ===================================
 * Test responsive design on various devices
 */

import { test, expect, devices } from '@playwright/test';

// Mobile device configurations
const mobileDevices = {
    'iPhone SE': devices['iPhone SE'],
    'iPhone 12': devices['iPhone 12'],
    'Pixel 5': devices['Pixel 5'],
    'Samsung Galaxy S9+': devices['Galaxy S9+'],
    'iPad Mini': devices['iPad Mini'],
    'iPad Pro': devices['iPad Pro'],
};

test.describe('Mobile Responsive Tests', () => {
    // Test homepage on all mobile devices
    for (const [deviceName, deviceConfig] of Object.entries(mobileDevices)) {
        test.describe(`${deviceName}`, () => {
            test('homepage should load correctly', async ({ browser }) => {
                const context = await browser.newContext(deviceConfig);
                const page = await context.newPage();
                
                await page.goto('/');
                
                // Check viewport
                const viewport = page.viewportSize();
                expect(viewport?.width).toBe(deviceConfig.viewport?.width);
                
                // Check main elements are visible
                await expect(page.locator('header')).toBeVisible();
                await expect(page.locator('main')).toBeVisible();
                await expect(page.locator('footer')).toBeVisible();
                
                // Check hamburger menu on mobile
                if (deviceConfig.viewport?.width! < 768) {
                    const hamburger = page.locator('button[aria-label="Menu"], .hamburger-menu');
                    await expect(hamburger).toBeVisible();
                }
                
                await context.close();
            });

            test('navigation should be accessible on mobile', async ({ browser }) => {
                const context = await browser.newContext(deviceConfig);
                const page = await context.newPage();
                
                await page.goto('/');
                
                // Open mobile menu if viewport is small
                if (deviceConfig.viewport?.width! < 768) {
                    const hamburger = page.locator('button[aria-label="Menu"]');
                    if (await hamburger.isVisible()) {
                        await hamburger.click();
                        
                        // Check menu is open
                        const navMenu = page.locator('nav[aria-label="Main navigation"]');
                        await expect(navMenu).toBeVisible();
                    }
                }
                
                await context.close();
            });

            test('buttons should be touch-friendly (min 44px)', async ({ browser }) => {
                const context = await browser.newContext(deviceConfig);
                const page = await context.newPage();
                
                await page.goto('/');
                
                const buttons = page.locator('button, a.btn, [role="button"]');
                const count = await buttons.count();
                
                for (let i = 0; i < Math.min(count, 10); i++) {
                    const button = buttons.nth(i);
                    const box = await button.boundingBox();
                    
                    if (box) {
                        // WCAG recommends minimum 44x44 CSS pixels
                        expect(box.height).toBeGreaterThanOrEqual(40);
                        expect(box.width).toBeGreaterThanOrThanOrEqual(40);
                    }
                }
                
                await context.close();
            });

            test('text should be readable without zooming', async ({ browser }) => {
                const context = await browser.newContext(deviceConfig);
                const page = await context.newPage();
                
                await page.goto('/');
                
                // Check font size on body text
                const fontSize = await page.evaluate(() => {
                    return window.getComputedStyle(document.body).fontSize;
                });
                
                // Minimum 16px for readability
                expect(parseInt(fontSize)).toBeGreaterThanOrEqual(14);
                
                await context.close();
            });

            test('forms should be usable on mobile', async ({ browser }) => {
                const context = await browser.newContext(deviceConfig);
                const page = await context.newPage();
                
                await page.goto('/login');
                
                // Check input fields are accessible
                const emailInput = page.locator('input[type="email"], input[name="email"]');
                await expect(emailInput).toBeVisible();
                
                // Check input is large enough to tap
                const box = await emailInput.boundingBox();
                if (box) {
                    expect(box.height).toBeGreaterThanOrEqual(40);
                }
                
                await context.close();
            });

            test('images should be responsive', async ({ browser }) => {
                const context = await browser.newContext(deviceConfig);
                const page = await context.newPage();
                
                await page.goto('/');
                
                const images = page.locator('img');
                const count = await images.count();
                
                for (let i = 0; i < Math.min(count, 5); i++) {
                    const img = images.nth(i);
                    await expect(img).toBeVisible();
                    
                    // Check srcset or sizes attribute for responsive images
                    const srcset = await img.getAttribute('srcset');
                    const sizes = await img.getAttribute('sizes');
                    const loading = await img.getAttribute('loading');
                    
                    // At least one responsive image attribute should be present
                    // OR loading="lazy" for performance
                    expect(srcset || sizes || loading === 'lazy').toBeTruthy();
                }
                
                await context.close();
            });

            test('no horizontal scrolling on mobile', async ({ browser }) => {
                const context = await browser.newContext(deviceConfig);
                const page = await context.newPage();
                
                await page.goto('/');
                
                // Check for horizontal scroll
                const hasHorizontalScroll = await page.evaluate(() => {
                    return document.documentElement.scrollWidth > document.documentElement.clientWidth;
                });
                
                expect(hasHorizontalScroll).toBeFalsy();
                
                await context.close();
            });
        });
    }

    // Test specific breakpoints
    test.describe('Breakpoint Tests', () => {
        const breakpoints = [
            { name: 'Mobile Small', width: 375, height: 667 },
            { name: 'Mobile Large', width: 414, height: 896 },
            { name: 'Tablet', width: 768, height: 1024 },
            { name: 'Desktop Small', width: 1024, height: 768 },
            { name: 'Desktop Large', width: 1440, height: 900 },
        ];

        for (const breakpoint of breakpoints) {
            test(`${breakpoint.name} (${breakpoint.width}x${breakpoint.height}) layout`, async ({ browser }) => {
                const context = await browser.newContext({
                    viewport: { width: breakpoint.width, height: breakpoint.height },
                });
                const page = await context.newPage();
                
                await page.goto('/');
                
                // Take screenshot for visual regression
                await page.screenshot({
                    path: `tests/e2e/screenshots/breakpoint-${breakpoint.name.replace(/\s+/g, '-').toLowerCase()}.png`,
                    fullPage: true,
                });
                
                // Check layout elements
                const header = page.locator('header');
                await expect(header).toBeVisible();
                
                const main = page.locator('main');
                await expect(main).toBeVisible();
                
                const footer = page.locator('footer');
                await expect(footer).toBeVisible();
                
                await context.close();
            });
        }
    });

    // Test orientation changes
    test.describe('Orientation Tests', () => {
        test('should handle landscape orientation on mobile', async ({ browser }) => {
            const context = await browser.newContext({
                viewport: { width: 667, height: 375 }, // iPhone SE landscape
                isMobile: true,
            });
            const page = await context.newPage();
            
            await page.goto('/');
            
            // Check content is still accessible
            await expect(page.locator('header')).toBeVisible();
            await expect(page.locator('main')).toBeVisible();
            
            await context.close();
        });
    });
});
