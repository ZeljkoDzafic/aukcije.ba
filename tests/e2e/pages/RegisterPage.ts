/**
 * ===================================
 * REGISTER PAGE OBJECT
 * ===================================
 */

import { Page, Locator } from '@playwright/test';

export class RegisterPage {
    readonly page: Page;
    readonly typeSelect: Locator;
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
        this.typeSelect = page.locator('select[name="type"]');
        this.nameInput = page.locator('input[name="name"]');
        this.emailInput = page.locator('input[name="email"]');
        this.passwordInput = page.locator('input[name="password"]');
        this.passwordConfirmationInput = page.locator('input[name="password_confirmation"]');
        this.submitButton = page.locator('button[type="submit"]');
        this.loginLink = page.locator('a:has-text("Već imaš nalog")');
        this.errorMessage = page.locator('.alert-danger, .error-message');
        this.termsCheckbox = page.locator('input[name="terms"]');
    }

    async goto() {
        await this.page.goto('/register');
    }

    async registerAsBuyer(name: string, email: string, password: string) {
        await this.goto();
        await this.typeSelect.selectOption('buyer');
        await this.nameInput.fill(name);
        await this.emailInput.fill(email);
        await this.passwordInput.fill(password);
        await this.passwordConfirmationInput.fill(password);
        await this.termsCheckbox.check();
        await this.submitButton.click();
    }

    async registerAsSeller(name: string, email: string, password: string) {
        await this.goto();
        await this.typeSelect.selectOption('seller');
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
