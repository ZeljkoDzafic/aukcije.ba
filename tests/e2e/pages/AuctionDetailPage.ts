/**
 * ===================================
 * AUCTION DETAIL PAGE OBJECT
 * ===================================
 */

import { Page, Locator } from '@playwright/test';

export class AuctionDetailPage {
    readonly page: Page;
    readonly title: Locator;
    readonly priceDisplay: Locator;
    readonly countdownTimer: Locator;
    readonly bidInput: Locator;
    readonly bidButton: Locator;
    readonly proxyBidInput: Locator;
    readonly watchlistButton: Locator;
    readonly description: Locator;
    readonly sellerInfo: Locator;
    readonly bidHistory: Locator;
    readonly errorMessage: Locator;
    readonly successMessage: Locator;

    constructor(page: Page) {
        this.page = page;
        this.title = page.locator('h1');
        this.priceDisplay = page.locator('text=Trenutna cijena').first();
        this.countdownTimer = page.locator('text=Vrijeme do kraja').first();
        this.bidInput = page.locator('input[name="bid_amount"]');
        this.bidButton = page.locator('button:has-text("Licitiraj odmah")');
        this.proxyBidInput = page.locator('input[name="proxy_max"]');
        this.watchlistButton = page.locator('button:has-text("Dodaj u praćenje")');
        this.description = page.locator('text=Opis artikla').first();
        this.sellerInfo = page.locator('text=Informacije o prodavcu').first();
        this.bidHistory = page.locator('text=Pregled bidova').first();
        this.errorMessage = page.locator('.text-red-600, [role="alert"]');
        this.successMessage = page.locator('.text-emerald-700, .text-green-700, [data-flash-message]');
    }

    async goto(auctionId: string) {
        await this.page.goto(`/aukcije/${auctionId}`);
    }

    async placeBid(amount: number) {
        await this.bidInput.fill(amount.toString());
        await this.bidButton.click();
    }

    async placeProxyBid(maxAmount: number) {
        await this.page.getByLabel('Uključi proxy bidding').check();
        await this.proxyBidInput.fill(maxAmount.toString());
        await this.bidButton.click();
    }

    async addToWatchlist() {
        await this.watchlistButton.click();
    }

    async getCurrentPrice(): Promise<string> {
        return await this.priceDisplay.textContent() || '';
    }

    async getBidCount(): Promise<number> {
        return await this.page.locator('text=BAM').count();
    }

    async getTimeRemaining(): Promise<string> {
        return await this.countdownTimer.textContent() || '';
    }

    async getErrorMessage(): Promise<string> {
        return await this.errorMessage.textContent() || '';
    }

    async getSuccessMessage(): Promise<string> {
        return await this.successMessage.textContent() || '';
    }

    async isVisible(): Promise<boolean> {
        return await this.title.isVisible();
    }

    async isBiddingEnabled(): Promise<boolean> {
        return await this.bidButton.isEnabled();
    }

    async getSellerName(): Promise<string> {
        return await this.page.locator('a[href*="/prodavaci/"], a[href*="/sellers/"]').first().textContent() || '';
    }
}
