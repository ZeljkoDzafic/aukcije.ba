/**
 * ===================================
 * PLAYWRIGHT VISUAL REGRESSION TESTS
 * ===================================
 * Screenshot comparison tests for UI consistency
 */

import { test, expect } from '@playwright/test';
import { devices } from '@playwright/test';

test.describe.skip('Visual Regression Tests', () => {
    // Configure screenshot options
    const screenshotOptions = {
        fullPage: true,
        animations: 'disabled',
    };

    // Test key pages on desktop
    test.describe('Desktop Visual Tests', () => {
        test('Homepage', async ({ page }) => {
            await page.goto('/');
            
            await expect(page).toHaveScreenshot('homepage-desktop.png', screenshotOptions);
        });

        test('Login Page', async ({ page }) => {
            await page.goto('/login');
            
            await expect(page).toHaveScreenshot('login-page-desktop.png', screenshotOptions);
        });

        test('Register Page', async ({ page }) => {
            await page.goto('/register');
            
            await expect(page).toHaveScreenshot('register-page-desktop.png', screenshotOptions);
        });

        test('Auction Listing Page', async ({ page }) => {
            await page.goto('/aukcije');
            
            await expect(page).toHaveScreenshot('auction-listing-desktop.png', screenshotOptions);
        });

        test('Auction Detail Page', async ({ page }) => {
            // Get first auction or use test data
            const auctionId = 'test-auction-id';
            await page.goto(`/aukcije/${auctionId}`);
            
            await expect(page).toHaveScreenshot('auction-detail-desktop.png', screenshotOptions);
        });

        test('Dashboard Page', async ({ page }) => {
            // Login first
            await page.goto('/login');
            await page.fill('input[name="email"]', 'buyer@test.com');
            await page.fill('input[name="password"]', 'Password123!');
            await page.click('button[type="submit"]');
            await page.waitForURL('/dashboard');
            
            await expect(page).toHaveScreenshot('dashboard-desktop.png', screenshotOptions);
        });

        test('Wallet Page', async ({ page }) => {
            await page.goto('/novcanik');
            
            await expect(page).toHaveScreenshot('wallet-page-desktop.png', screenshotOptions);
        });

        test('Admin Dashboard', async ({ page }) => {
            await page.goto('/admin/dashboard');
            
            await expect(page).toHaveScreenshot('admin-dashboard-desktop.png', screenshotOptions);
        });
    });

    // Test key pages on mobile
    test.describe('Mobile Visual Tests', () => {
        test.use({
            ...devices['iPhone 12'],
        });

        test('Homepage Mobile', async ({ page }) => {
            await page.goto('/');
            
            await expect(page).toHaveScreenshot('homepage-mobile.png', screenshotOptions);
        });

        test('Login Page Mobile', async ({ page }) => {
            await page.goto('/login');
            
            await expect(page).toHaveScreenshot('login-page-mobile.png', screenshotOptions);
        });

        test('Auction Listing Mobile', async ({ page }) => {
            await page.goto('/aukcije');
            
            await expect(page).toHaveScreenshot('auction-listing-mobile.png', screenshotOptions);
        });

        test('Auction Detail Mobile', async ({ page }) => {
            const auctionId = 'test-auction-id';
            await page.goto(`/aukcije/${auctionId}`);
            
            await expect(page).toHaveScreenshot('auction-detail-mobile.png', screenshotOptions);
        });

        test('Dashboard Mobile', async ({ page }) => {
            await page.goto('/login');
            await page.fill('input[name="email"]', 'buyer@test.com');
            await page.fill('input[name="password"]', 'Password123!');
            await page.click('button[type="submit"]');
            await page.waitForURL('/dashboard');
            
            await expect(page).toHaveScreenshot('dashboard-mobile.png', screenshotOptions);
        });
    });

    // Test key pages on tablet
    test.describe('Tablet Visual Tests', () => {
        test.use({
            ...devices['iPad Mini'],
        });

        test('Homepage Tablet', async ({ page }) => {
            await page.goto('/');
            
            await expect(page).toHaveScreenshot('homepage-tablet.png', screenshotOptions);
        });

        test('Auction Listing Tablet', async ({ page }) => {
            await page.goto('/aukcije');
            
            await expect(page).toHaveScreenshot('auction-listing-tablet.png', screenshotOptions);
        });
    });

    // Test UI components in isolation
    test.describe('Component Visual Tests', () => {
        test('Auction Card Component', async ({ page }) => {
            await page.goto('/aukcije');
            
            // Screenshot first auction card
            const auctionCard = page.locator('[data-testid="auction-card"]').first();
            await expect(auctionCard).toHaveScreenshot('auction-card-component.png');
        });

        test('Bidding Console Component', async ({ page }) => {
            const auctionId = 'test-auction-id';
            await page.goto(`/aukcije/${auctionId}`);
            
            // Screenshot bidding console
            const biddingConsole = page.locator('[data-testid="bidding-console"]');
            await expect(biddingConsole).toHaveScreenshot('bidding-console-component.png');
        });

        test('Countdown Timer Component', async ({ page }) => {
            const auctionId = 'test-auction-id';
            await page.goto(`/aukcije/${auctionId}`);
            
            // Screenshot countdown timer
            const timer = page.locator('[data-testid="countdown-timer"]');
            await expect(timer).toHaveScreenshot('countdown-timer-component.png');
        });

        test('Navigation Component', async ({ page }) => {
            await page.goto('/');
            
            // Screenshot navigation
            const nav = page.locator('nav[aria-label="Main navigation"]');
            await expect(nav).toHaveScreenshot('navigation-component.png');
        });

        test('Footer Component', async ({ page }) => {
            await page.goto('/');
            
            // Screenshot footer
            const footer = page.locator('footer');
            await expect(footer).toHaveScreenshot('footer-component.png');
        });
    });

    // Test different states
    test.describe('State Visual Tests', () => {
        test('Button States', async ({ page }) => {
            await page.goto('/');
            
            // Find buttons and screenshot different states
            const buttons = page.locator('button').first();
            
            // Default state
            await expect(buttons).toHaveScreenshot('button-default.png');
            
            // Hover state
            await buttons.hover();
            await expect(buttons).toHaveScreenshot('button-hover.png');
            
            // Focus state
            await buttons.focus();
            await expect(buttons).toHaveScreenshot('button-focus.png');
            
            // Disabled state (if applicable)
            await buttons.evaluate(el => el.setAttribute('disabled', 'true'));
            await expect(buttons).toHaveScreenshot('button-disabled.png');
        });

        test('Form Input States', async ({ page }) => {
            await page.goto('/register');
            
            const input = page.locator('input[name="email"]').first();
            
            // Default state
            await expect(input).toHaveScreenshot('input-default.png');
            
            // Focus state
            await input.focus();
            await expect(input).toHaveScreenshot('input-focus.png');
            
            // With value
            await input.fill('test@example.com');
            await expect(input).toHaveScreenshot('input-with-value.png');
            
            // Error state
            await input.evaluate(el => el.classList.add('error'));
            await expect(input).toHaveScreenshot('input-error.png');
        });

        test('Alert/Notification States', async ({ page }) => {
            await page.goto('/');
            
            // Trigger different alert types via URL params or JS
            await page.goto('/?alert=success');
            const successAlert = page.locator('.alert-success');
            if (await successAlert.isVisible()) {
                await expect(successAlert).toHaveScreenshot('alert-success.png');
            }
            
            await page.goto('/?alert=error');
            const errorAlert = page.locator('.alert-error');
            if (await errorAlert.isVisible()) {
                await expect(errorAlert).toHaveScreenshot('alert-error.png');
            }
            
            await page.goto('/?alert=warning');
            const warningAlert = page.locator('.alert-warning');
            if (await warningAlert.isVisible()) {
                await expect(warningAlert).toHaveScreenshot('alert-warning.png');
            }
        });
    });

    // Test dark mode (if implemented)
    test.describe('Dark Mode Visual Tests', () => {
        test('Homepage Dark Mode', async ({ page }) => {
            await page.goto('/');
            
            // Enable dark mode
            await page.evaluate(() => {
                document.documentElement.classList.add('dark');
            });
            
            await expect(page).toHaveScreenshot('homepage-darkmode.png', screenshotOptions);
        });

        test('Dashboard Dark Mode', async ({ page }) => {
            await page.goto('/login');
            await page.fill('input[name="email"]', 'buyer@test.com');
            await page.fill('input[name="password"]', 'Password123!');
            await page.click('button[type="submit"]');
            await page.waitForURL('/dashboard');
            
            // Enable dark mode
            await page.evaluate(() => {
                document.documentElement.classList.add('dark');
            });
            
            await expect(page).toHaveScreenshot('dashboard-darkmode.png', screenshotOptions);
        });
    });

    // Test loading states
    test.describe('Loading State Visual Tests', () => {
        test('Skeleton Loading', async ({ page }) => {
            // Navigate to page with loading state
            await page.goto('/aukcije?loading=true');
            
            // Screenshot skeleton state
            await expect(page).toHaveScreenshot('skeleton-loading.png');
        });

        test('Spinner Loading', async ({ page }) => {
            await page.goto('/?loading=spinner');
            
            const spinner = page.locator('.spinner');
            await expect(spinner).toHaveScreenshot('spinner-loading.png');
        });
    });

    // Test error states
    test.describe('Error State Visual Tests', () => {
        test('404 Page', async ({ page }) => {
            await page.goto('/nonexistent-page-12345');
            
            await expect(page).toHaveScreenshot('404-page.png', screenshotOptions);
        });

        test('500 Error Page', async ({ page }) => {
            await page.goto('/_error/500');
            
            await expect(page).toHaveScreenshot('500-page.png', screenshotOptions);
        });

        test('Form Validation Errors', async ({ page }) => {
            await page.goto('/register');
            
            // Submit empty form to trigger validation
            await page.click('button[type="submit"]');
            
            // Wait for validation errors
            await page.waitForTimeout(500);
            
            await expect(page).toHaveScreenshot('form-validation-errors.png', screenshotOptions);
        });
    });
});
