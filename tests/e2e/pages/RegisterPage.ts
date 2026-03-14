/**
 * ===================================
 * REGISTER PAGE OBJECT
 * ===================================
 */

import { Page, Locator } from '@playwright/test';

export class RegisterPage {
    readonly page: Page;
    readonly nameInput: Locator;
    readonly emailInput: Locator;
    readonly passwordInput: Locator;
    readonly passwordConfirmationInput: Locator;
    readonly submitButton: Locator;
    readonly loginLink: Locator;
    readonly errorMessage: Locator;
    readonly termsCheckbox: Locator;

    constructor(page: Page) {
        this.page = page;
        this.nameInput = page.locator('input[name="name"]');
        this.emailInput = page.locator('input[name="email"]');
        this.passwordInput = page.locator('input[name="password"]');
        this.passwordConfirmationInput = page.locator('input[name="password_confirmation"]');
        this.submitButton = page.locator('button[type="submit"]');
        this.loginLink = page.locator('a:has-text("Prijavi se")');
        this.errorMessage = page.locator('.text-red-600, [role="alert"]');
        this.termsCheckbox = page.locator('input[type="checkbox"][required]');
    }

    async goto() {
        await this.page.goto('/register');
    }

    async registerAsBuyer(name: string, email: string, password: string) {
        await this.goto();
        await this.page.locator('input[name="marketplace_focus"][value="buyer"]').check();
        await this.nameInput.fill(name);
        await this.emailInput.fill(email);
        await this.passwordInput.fill(password);
        await this.passwordConfirmationInput.fill(password);
        await this.termsCheckbox.check();
        await this.submitButton.click();
    }

    async registerAsSeller(name: string, email: string, password: string) {
        await this.goto();
        await this.page.locator('input[name="marketplace_focus"][value="seller"]').check();
        await this.nameInput.fill(name);
        await this.emailInput.fill(email);
        await this.passwordInput.fill(password);
        await this.passwordConfirmationInput.fill(password);
        await this.termsCheckbox.check();
        await this.submitButton.click();
    }

    async getErrorMessage(): Promise<string> {
        return await this.errorMessage.textContent() || '';
    }

    async getValidationErrors(): Promise<string[]> {
        const errors = await this.page.locator('.text-red-600, .error-text').allTextContents();
        return errors.filter(text => text.trim() !== '');
    }

    async isVisible(): Promise<boolean> {
        return await this.nameInput.isVisible();
    }
}
