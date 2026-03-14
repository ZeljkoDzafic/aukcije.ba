/**
 * ===================================
 * AUCTION LIST PAGE OBJECT
 * ===================================
 */

import { Page, Locator } from '@playwright/test';

export class AuctionListPage {
    readonly page: Page;
    readonly searchInput: Locator;
    readonly categoryFilter: Locator;
    readonly priceMinInput: Locator;
    readonly priceMaxInput: Locator;
    readonly sortSelect: Locator;
    readonly auctionCards: Locator;
    readonly clearFiltersButton: Locator;

    constructor(page: Page) {
        this.page = page;
        this.searchInput = page.locator('input[name="query"]');
        this.categoryFilter = page.locator('select[name="category"]');
        this.priceMinInput = page.locator('input[name="price_min"]');
        this.priceMaxInput = page.locator('input[name="price_max"]');
        this.sortSelect = page.locator('select[name="sort"]');
        this.auctionCards = page.locator('.auction-card');
        this.clearFiltersButton = page.locator('button:has-text("Reset")');
    }

    async goto() {
        await this.page.goto('/aukcije');
    }

    async search(query: string) {
        await this.searchInput.fill(query);
        await this.searchInput.press('Tab');
    }

    async filterByCategory(category: string) {
        await this.categoryFilter.selectOption(category);
    }

    async filterByPriceRange(min: number, max: number) {
        await this.priceMinInput.fill(min.toString());
        await this.priceMaxInput.fill(max.toString());
        await this.priceMaxInput.press('Tab');
    }

    async sortBy(sortOption: string) {
        await this.sortSelect.selectOption(sortOption);
    }

    async getAuctionCount(): Promise<number> {
        return await this.auctionCards.count();
    }

    async getAuctionCard(index: number) {
        return this.auctionCards.nth(index);
    }

    async clickAuction(index: number) {
        await this.auctionCards.nth(index).click();
    }

    async clickAuctionByTitle(title: string) {
        await this.page.locator(`.auction-card:has-text("${title}") a[aria-label*="Otvori aukciju"]`).first().click();
    }

    async isVisible(): Promise<boolean> {
        return await this.searchInput.isVisible();
    }

    async getCurrentUrl(): Promise<string> {
        return this.page.url();
    }
}
