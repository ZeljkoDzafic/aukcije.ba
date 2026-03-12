/**
 * ===================================
 * PLAYWRIGHT TEST FIXTURES
 * ===================================
 * Custom fixtures for E2E tests
 */

import { test as base, expect } from '@playwright/test';

/**
 * Test fixtures interface
 */
interface Fixtures {
    authenticatedBuyer: {
        email: string;
        password: string;
        name: string;
    };
    authenticatedSeller: {
        email: string;
        password: string;
        name: string;
    };
    authenticatedAdmin: {
        email: string;
        password: string;
        name: string;
    };
    createAuction: (data?: Partial<AuctionData>) => Promise<AuctionData>;
}

/**
 * Auction data interface
 */
interface AuctionData {
    title: string;
    description: string;
    startPrice: number;
    buyNowPrice?: number;
    duration: number;
    category: string;
}

/**
 * Extend Playwright test with custom fixtures
 */
export const test = base.extend<Fixtures>({
    /**
     * Authenticated buyer fixture
     */
    authenticatedBuyer: async ({ page }, use) => {
        const buyer = {
            email: 'buyer@test.com',
            password: 'Password123!',
            name: 'Test Buyer',
        };

        // Login before tests
        await page.goto('/login');
        await page.fill('input[name="email"]', buyer.email);
        await page.fill('input[name="password"]', buyer.password);
        await page.click('button[type="submit"]');
        await page.waitForURL('/dashboard');

        await use(buyer);

        // Cleanup after tests (optional logout)
        // await page.click('button:has-text("Odjavi se")');
    },

    /**
     * Authenticated seller fixture
     */
    authenticatedSeller: async ({ page }, use) => {
        const seller = {
            email: 'seller@test.com',
            password: 'Password123!',
            name: 'Test Seller',
        };

        // Login before tests
        await page.goto('/login');
        await page.fill('input[name="email"]', seller.email);
        await page.fill('input[name="password"]', seller.password);
        await page.click('button[type="submit"]');
        await page.waitForURL('/seller/dashboard');

        await use(seller);
    },

    /**
     * Authenticated admin fixture
     */
    authenticatedAdmin: async ({ page }, use) => {
        const admin = {
            email: 'admin@aukcije.ba',
            password: 'AdminPassword123!',
            name: 'Test Admin',
        };

        // Login before tests
        await page.goto('/login');
        await page.fill('input[name="email"]', admin.email);
        await page.fill('input[name="password"]', admin.password);
        await page.click('button[type="submit"]');
        await page.waitForURL('/admin/dashboard');

        await use(admin);
    },

    /**
     * Create auction helper fixture
     */
    createAuction: async ({ page }, use) => {
        const createAuction = async (data: Partial<AuctionData> = {}) => {
            const auctionData: AuctionData = {
                title: data.title || `Test Auction ${Date.now()}`,
                description: data.description || 'Test auction description',
                startPrice: data.startPrice || 10,
                buyNowPrice: data.buyNowPrice,
                duration: data.duration || 3,
                category: data.category || 'Elektronika',
            };

            // Navigate to create auction page
            await page.goto('/seller/aukcije/nova');

            // Fill in auction details
            await page.fill('input[name="title"]', auctionData.title);
            await page.fill('textarea[name="description"]', auctionData.description);
            await page.fill('input[name="start_price"]', auctionData.startPrice.toString());

            if (auctionData.buyNowPrice) {
                await page.fill('input[name="buy_now_price"]', auctionData.buyNowPrice.toString());
            }

            // Select category
            await page.selectOption('select[name="category_id"]', auctionData.category);

            // Select duration
            await page.selectOption('select[name="duration"]', auctionData.duration.toString());

            // Submit form
            await page.click('button:has-text("Kreiraj aukciju")');
            await page.waitForURL(/\/aukcije\/.+/);

            return auctionData;
        };

        await use(createAuction);
    },
});

/**
 * Export expect
 */
export { expect };
