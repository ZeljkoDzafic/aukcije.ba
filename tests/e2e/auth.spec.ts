/**
 * ===================================
 * AUTH E2E TESTS
 * ===================================
 * Test authentication flows
 */

import { test, expect } from '../fixtures/test-fixtures';
import { LoginPage } from '../pages/LoginPage';
import { RegisterPage } from '../pages/RegisterPage';

test.describe('Authentication', () => {
    let loginPage: LoginPage;
    let registerPage: RegisterPage;

    test.beforeEach(async ({ page }) => {
        loginPage = new LoginPage(page);
        registerPage = new RegisterPage(page);
    });

    test.describe('Login', () => {
        test('should display login page', async ({ page }) => {
            await loginPage.goto();
            await expect(loginPage).toBeVisible();
            await expect(loginPage.emailInput).toBeVisible();
            await expect(loginPage.passwordInput).toBeVisible();
            await expect(loginPage.submitButton).toBeVisible();
        });

        test('should login successfully with valid credentials', async ({ page }) => {
            await loginPage.login('buyer@test.com', 'Password123!');
            await page.waitForURL('/dashboard');
            await expect(page).toHaveURL('/dashboard');
        });

        test('should show error with invalid credentials', async ({ page }) => {
            await loginPage.login('invalid@test.com', 'wrongpassword');
            await expect(loginPage.errorMessage).toBeVisible();
        });

        test('should redirect to dashboard after login', async ({ page }) => {
            await loginPage.loginAsBuyer();
            await page.waitForURL('/dashboard');
            await expect(page.locator('h1')).toContainText('Dashboard');
        });

        test('should redirect seller to seller dashboard', async ({ page }) => {
            await loginPage.loginAsSeller();
            await page.waitForURL('/seller/dashboard');
            await expect(page.locator('h1')).toContainText('Seller Dashboard');
        });

        test('should redirect admin to admin panel', async ({ page }) => {
            await loginPage.loginAsAdmin();
            await page.waitForURL('/admin/dashboard');
            await expect(page.locator('h1')).toContainText('Admin Dashboard');
        });
    });

    test.describe('Register', () => {
        test('should display register page', async ({ page }) => {
            await registerPage.goto();
            await expect(registerPage).toBeVisible();
            await expect(registerPage.typeSelect).toBeVisible();
            await expect(registerPage.nameInput).toBeVisible();
            await expect(registerPage.emailInput).toBeVisible();
        });

        test('should register as buyer', async ({ page }) => {
            const email = `buyer_${Date.now()}@test.com`;
            await registerPage.registerAsBuyer('New Buyer', email, 'Password123!');
            await page.waitForURL(/\/dashboard|\/verify-email/);
        });

        test('should register as seller', async ({ page }) => {
            const email = `seller_${Date.now()}@test.com`;
            await registerPage.registerAsSeller('New Seller', email, 'Password123!');
            await page.waitForURL(/\/dashboard|\/verify-email|\/kyc/);
        });

        test('should show validation errors for empty fields', async ({ page }) => {
            await registerPage.goto();
            await registerPage.submitButton.click();
            const errors = await registerPage.getValidationErrors();
            expect(errors.length).toBeGreaterThan(0);
        });

        test('should show error for duplicate email', async ({ page }) => {
            await registerPage.registerAsBuyer('Test User', 'buyer@test.com', 'Password123!');
            await expect(registerPage.errorMessage).toBeVisible();
        });

        test('should show error for password mismatch', async ({ page }) => {
            await registerPage.goto();
            await registerPage.typeSelect.selectOption('buyer');
            await registerPage.nameInput.fill('Test User');
            await registerPage.emailInput.fill('test@test.com');
            await registerPage.passwordInput.fill('Password123!');
            await registerPage.passwordConfirmationInput.fill('DifferentPassword123!');
            await registerPage.submitButton.click();
            const errors = await registerPage.getValidationErrors();
            expect(errors.join(' ')).toContain('lozinka');
        });
    });

    test.describe('Logout', () => {
        test('should logout successfully', async ({ authenticatedBuyer, page }) => {
            // Already logged in via fixture
            await page.click('button:has-text("Odjavi se")');
            await page.waitForURL('/login');
            await expect(loginPage).toBeVisible();
        });
    });

    test.describe('Password Reset', () => {
        test('should display forgot password page', async ({ page }) => {
            await loginPage.goto();
            await loginPage.forgotPasswordLink.click();
            await page.waitForURL('/forgot-password');
            await expect(page.locator('input[name="email"]')).toBeVisible();
        });
    });
});
