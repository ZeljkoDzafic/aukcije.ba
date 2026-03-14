/**
 * ===================================
 * PLAYWRIGHT TEST FIXTURES
 * ===================================
 * Custom fixtures for E2E tests
 */

import { test as base, expect } from '@playwright/test';

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

interface AuctionData {
    title: string;
    description: string;
    startPrice: number;
    buyNowPrice?: number;
    duration: number;
    category: string;
}

export const test = base.extend<Fixtures>({
    authenticatedBuyer: async ({ page }, use) => {
        const buyer = {
            email: 'buyer@test.com',
            password: 'Password123!',
            name: 'Test Buyer',
        };

        await page.goto('/login');
        await page.fill('input[name="email"]', buyer.email);
        await page.fill('input[name="password"]', buyer.password);
        await page.click('button[type="submit"]');
        await page.waitForURL('/dashboard');

        await use(buyer);
    },

    authenticatedSeller: async ({ page }, use) => {
        const seller = {
            email: 'seller@test.com',
            password: 'Password123!',
            name: 'Test Seller',
        };

        await page.goto('/login');
        await page.fill('input[name="email"]', seller.email);
        await page.fill('input[name="password"]', seller.password);
        await page.click('button[type="submit"]');
        await page.waitForURL('/seller/dashboard');

        await use(seller);
    },

    authenticatedAdmin: async ({ page }, use) => {
        const admin = {
            email: 'admin@aukcije.ba',
            password: 'AdminPassword123!',
            name: 'Test Admin',
        };

        await page.goto('/login');
        await page.fill('input[name="email"]', admin.email);
        await page.fill('input[name="password"]', admin.password);
        await page.click('button[type="submit"]');
        await page.waitForURL('/admin/dashboard');

        await use(admin);
    },

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

            await page.goto('/seller/aukcije/nova');
            await page.fill('input[name="title"]', auctionData.title);
            await page.locator('select[name="category"]').selectOption({ label: auctionData.category }).catch(async () => {
                await page.locator('select[name="category"]').selectOption({ index: 1 });
            });
            await page.locator('[contenteditable="true"]').first().fill(auctionData.description);
            await page.click('button:has-text("Sljedeći korak")');
            await page.click('button:has-text("Sljedeći korak")');
            await page.fill('input[name="start_price"]', auctionData.startPrice.toString());

            if (auctionData.buyNowPrice) {
                await page.fill('input[name="buy_now"]', auctionData.buyNowPrice.toString());
            }

            await page.selectOption('select[name="duration_days"]', auctionData.duration.toString());

            return auctionData;
        };

        await use(createAuction);
    },
});

export { expect };
