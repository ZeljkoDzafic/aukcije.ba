/**
 * ===================================
 * LOGIN PAGE OBJECT
 * ===================================
 */

import { Page, Locator } from '@playwright/test';

export class LoginPage {
    readonly page: Page;
    readonly emailInput: Locator;
    readonly passwordInput: Locator;
    readonly submitButton: Locator;
    readonly registerLink: Locator;
    readonly forgotPasswordLink: Locator;
    readonly errorMessage: Locator;

    constructor(page: Page) {
        this.page = page;
        this.emailInput = page.locator('input[name="email"]');
        this.passwordInput = page.locator('input[name="password"]');
        this.submitButton = page.locator('button[type="submit"]');
        this.registerLink = page.locator('a:has-text("Registruj se")');
        this.forgotPasswordLink = page.locator('a:has-text("Zaboravili ste lozinku")');
        this.errorMessage = page.locator('.text-red-600, [role="alert"]');
    }

    async goto() {
        await this.page.goto('/login');
    }

    async login(email: string, password: string) {
        await this.goto();
        await this.emailInput.fill(email);
        await this.passwordInput.fill(password);
        await this.submitButton.click();
    }

    async loginAsBuyer() {
        await this.login('buyer@test.com', 'Password123!');
    }

    async loginAsSeller() {
        await this.login('seller@test.com', 'Password123!');
    }

    async loginAsAdmin() {
        await this.login('admin@aukcije.ba', 'AdminPassword123!');
    }

    async logout() {
        await this.page.getByRole('button', { name: 'Odjava' }).first().click();
    }

    async getErrorMessage(): Promise<string> {
        return await this.errorMessage.textContent() || '';
    }

    async isVisible(): Promise<boolean> {
        return await this.emailInput.isVisible();
    }
}
