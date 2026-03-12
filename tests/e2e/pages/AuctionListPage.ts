/**
 * ===================================
 * AUCTION LIST PAGE OBJECT
 * ===================================
 */

import { Page, Locator } from '@playwright/test';

export class AuctionListPage {
    readonly page: Page;
    readonly searchInput: Locator;
    readonly searchButton: Locator;
    readonly categoryFilter: Locator;
    readonly priceMinInput: Locator;
    readonly priceMaxInput: Locator;
    readonly sortSelect: Locator;
    readonly auctionCards: Locator;
    readonly gridViewButton: Locator;
    readonly listViewButton: Locator;
    readonly filterButton: Locator;
    readonly clearFiltersButton: Locator;

    constructor(page: Page) {
        this.page = page;
        this.searchInput = page.locator('input[name="search"]');
        this.searchButton = page.locator('button:has-text("Pretraži")');
        this.categoryFilter = page.locator('select[name="category"]');
        this.priceMinInput = page.locator('input[name="price_min"]');
        this.priceMaxInput = page.locator('input[name="price_max"]');
        this.sortSelect = page.locator('select[name="sort"]');
        this.auctionCards = page.locator('[data-testid="auction-card"]');
        this.gridViewButton = page.locator('button[aria-label="Grid view"]');
        this.listViewButton = page.locator('button[aria-label="List view"]');
        this.filterButton = page.locator('button:has-text("Filteri")');
        this.clearFiltersButton = page.locator('button:has-text("Resetuj")');
    }

    async goto() {
        await this.page.goto('/aukcije');
    }

    async search(query: string) {
        await this.searchInput.fill(query);
        await this.searchButton.click();
    }

    async filterByCategory(category: string) {
        await this.categoryFilter.selectOption(category);
    }

    async filterByPriceRange(min: number, max: number) {
        await this.priceMinInput.fill(min.toString());
        await this.priceMaxInput.fill(max.toString());
        await this.searchButton.click();
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
        await this.page.locator(`[data-testid="auction-card"]:has-text("${title}")`).click();
    }

    async isVisible(): Promise<boolean> {
        return await this.searchInput.isVisible();
    }

    async getCurrentUrl(): Promise<string> {
        return this.page.url();
    }
}
