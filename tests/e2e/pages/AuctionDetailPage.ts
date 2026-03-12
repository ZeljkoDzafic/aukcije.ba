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
    readonly proxyBidCheckbox: Locator;
    readonly proxyBidInput: Locator;
    readonly watchlistButton: Locator;
    readonly shareButton: Locator;
    readonly imageGallery: Locator;
    readonly description: Locator;
    readonly sellerInfo: Locator;
    readonly bidHistory: Locator;
    readonly bidHistoryItems: Locator;
    readonly errorMessage: Locator;
    readonly successMessage: Locator;

    constructor(page: Page) {
        this.page = page;
        this.title = page.locator('h1');
        this.priceDisplay = page.locator('[data-testid="current-price"]');
        this.countdownTimer = page.locator('[data-testid="countdown-timer"]');
        this.bidInput = page.locator('input[name="bid_amount"]');
        this.bidButton = page.locator('button:has-text("LICITIRAJ")');
        this.proxyBidCheckbox = page.locator('input[name="proxy_bid"]');
        this.proxyBidInput = page.locator('input[name="max_bid_amount"]');
        this.watchlistButton = page.locator('button:has-text("Watchlist")');
        this.shareButton = page.locator('button:has-text("Podijeli")');
        this.imageGallery = page.locator('[data-testid="image-gallery"]');
        this.description = page.locator('[data-testid="description"]');
        this.sellerInfo = page.locator('[data-testid="seller-info"]');
        this.bidHistory = page.locator('[data-testid="bid-history"]');
        this.bidHistoryItems = page.locator('[data-testid="bid-item"]');
        this.errorMessage = page.locator('.alert-danger, .error-message');
        this.successMessage = page.locator('.alert-success, .success-message');
    }

    async goto(auctionId: string) {
        await this.page.goto(`/aukcije/${auctionId}`);
    }

    async placeBid(amount: number) {
        await this.bidInput.fill(amount.toString());
        await this.bidButton.click();
    }

    async placeProxyBid(maxAmount: number) {
        await this.proxyBidCheckbox.check();
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
        return await this.bidHistoryItems.count();
    }

    async getLatestBid(): Promise<string> {
        const firstBid = this.bidHistoryItems.first();
        return await firstBid.locator('[data-testid="bid-amount"]').textContent() || '';
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
        return await this.sellerInfo.locator('[data-testid="seller-name"]').textContent() || '';
    }
}
