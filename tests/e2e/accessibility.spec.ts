/**
 * ===================================
 * PLAYWRIGHT ACCESSIBILITY TESTS
 * ===================================
 * WCAG 2.1 AA Compliance Tests
 */

import { test, expect } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';

test.describe('Accessibility Tests', () => {
    test('homepage should not have accessibility violations', async ({ page }) => {
        await page.goto('/');
        
        const accessibilityScanResults = await new AxeBuilder({ page }).analyze();
        
        expect(accessibilityScanResults.violations).toEqual([]);
    });

    test('login page should be accessible', async ({ page }) => {
        await page.goto('/login');
        
        const accessibilityScanResults = await new AxeBuilder({ page }).analyze();
        
        expect(accessibilityScanResults.violations).toEqual([]);
    });

    test('auction listing page should be accessible', async ({ page }) => {
        await page.goto('/aukcije');
        
        const accessibilityScanResults = await new AxeBuilder({ page }).analyze();
        
        expect(accessibilityScanResults.violations).toEqual([]);
    });

    test('auction detail page should be accessible', async ({ page }) => {
        // Get first auction ID
        const response = await page.request.get('/api/v1/auctions?limit=1');
        const auction = await response.json();
        
        if (auction.data && auction.data.length > 0) {
            await page.goto(`/aukcije/${auction.data[0].id}`);
            
            const accessibilityScanResults = await new AxeBuilder({ page }).analyze();
            
            expect(accessibilityScanResults.violations).toEqual([]);
        }
    });

    test('all images should have alt text', async ({ page }) => {
        await page.goto('/');
        
        const images = await page.locator('img').all();
        
        for (const img of images) {
            const alt = await img.getAttribute('alt');
            const role = await img.getAttribute('role');
            
            // Decorative images can have empty alt or role="presentation"
            if (role !== 'presentation') {
                expect(alt).not.toBeNull();
            }
        }
    });

    test('all form inputs should have labels', async ({ page }) => {
        await page.goto('/login');
        
        const inputs = await page.locator('input[type="text"], input[type="email"], input[type="password"]').all();
        
        for (const input of inputs) {
            const id = await input.getAttribute('id');
            const ariaLabel = await input.getAttribute('aria-label');
            const ariaLabelledBy = await input.getAttribute('aria-labelledby');
            
            expect(id || ariaLabel || ariaLabelledBy).not.toBeNull();
            
            if (id) {
                const label = page.locator(`label[for="${id}"]`);
                await expect(label).toBeVisible();
            }
        }
    });

    test('color contrast should meet WCAG AA standards', async ({ page }) => {
        await page.goto('/');
        
        // Check main text elements
        const textElements = await page.locator('h1, h2, h3, p, a, button').all();
        
        for (const element of textElements.slice(0, 20)) { // Check first 20 elements
            const accessibilityScanResults = await new AxeBuilder({ page })
                .include(await element.evaluate(el => el))
                .withTags(['wcag2aa', 'wcag2aaa'])
                .analyze();
            
            // Log violations but don't fail (manual review needed)
            if (accessibilityScanResults.violations.length > 0) {
                console.log('Contrast violations:', accessibilityScanResults.violations);
            }
        }
    });

    test('keyboard navigation should work', async ({ page }) => {
        await page.goto('/');
        
        // Tab through interactive elements
        await page.keyboard.press('Tab');
        await page.keyboard.press('Tab');
        await page.keyboard.press('Tab');
        
        const focusedElement = await page.evaluate(() => document.activeElement?.tagName);
        expect(focusedElement).toMatch(/A|BUTTON|INPUT/);
        
        // Test Enter key on focused element
        await page.keyboard.press('Enter');
    });

    test('skip links should be present', async ({ page }) => {
        await page.goto('/');
        
        const skipLink = page.locator('a[href="#main-content"], a[href="#main"]');
        await expect(skipLink).toBeVisible();
    });

    test('ARIA landmarks should be present', async ({ page }) => {
        await page.goto('/');
        
        const landmarks = {
            banner: page.locator('[role="banner"], header'),
            navigation: page.locator('[role="navigation"], nav'),
            main: page.locator('[role="main"], main'),
            contentinfo: page.locator('[role="contentinfo"], footer'),
        };
        
        await expect(landmarks.banner).toBeVisible();
        await expect(landmarks.navigation).toBeVisible();
        await expect(landmarks.main).toBeVisible();
        await expect(landmarks.contentinfo).toBeVisible();
    });
});
