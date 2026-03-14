/**
 * ===================================
 * PLAYWRIGHT CRITICAL USER FLOW TESTS
 * ===================================
 * Real browser smoke coverage for launch-critical surfaces.
 */

import { test, expect } from './fixtures/test-fixtures';
import { LoginPage } from './pages/LoginPage';
import { AuctionListPage } from './pages/AuctionListPage';
import { AuctionDetailPage } from './pages/AuctionDetailPage';

test.describe('Critical User Flows', () => {
    test('Buyer smoke: login → search → open auction → inspect bid panel', async ({ page }) => {
        const loginPage = new LoginPage(page);
        const auctionListPage = new AuctionListPage(page);
        const auctionDetailPage = new AuctionDetailPage(page);

        await test.step('Login as buyer', async () => {
            await loginPage.loginAsBuyer();
            await page.waitForURL('/dashboard');
            await expect(page.getByRole('heading', { name: 'Sve što pratiš, licitiraš i kupuješ na jednom mjestu.' })).toBeVisible();
        });

        await test.step('Browse and search auctions', async () => {
            await auctionListPage.goto();
            await expect(page.getByRole('heading', { name: /Pretraži aukcije kao pravi marketplace/i })).toBeVisible();
            await auctionListPage.search('Samsung');
            await expect(page.locator('body')).toContainText('Samsung');
            await auctionListPage.sortBy('ending_soon');
        });

        await test.step('Open auction detail', async () => {
            const auctionCount = await auctionListPage.getAuctionCount();
            expect(auctionCount).toBeGreaterThan(0);

            await auctionListPage.clickAuction(0);
            await expect(auctionDetailPage.title).toBeVisible();
            await expect(auctionDetailPage.priceDisplay).toBeVisible();
            await expect(auctionDetailPage.countdownTimer).toBeVisible();
            await expect(auctionDetailPage.description).toBeVisible();
            await expect(auctionDetailPage.sellerInfo).toBeVisible();
        });

        await test.step('Inspect bid controls', async () => {
            await expect(auctionDetailPage.bidInput).toBeVisible();
            await expect(auctionDetailPage.proxyBidInput).toBeVisible();
            await expect(auctionDetailPage.bidButton).toBeVisible();
            await expect(auctionDetailPage.watchlistButton).toBeVisible();
            await expect(auctionDetailPage.bidHistory).toBeVisible();
        });
    });

    test('Seller smoke: login → open create auction wizard → walk core steps', async ({ page }) => {
        const loginPage = new LoginPage(page);

        await test.step('Login as seller', async () => {
            await loginPage.loginAsSeller();
            await page.waitForURL('/seller/dashboard');
            await expect(page.getByRole('heading', { name: 'Prodajna kontrolna tabla' })).toBeVisible();
        });

        await test.step('Open auction wizard', async () => {
            await page.goto('/seller/aukcije/nova');
            await expect(page.getByRole('heading', { name: 'Kreiraj novu aukciju' })).toBeVisible();
            await expect(page.locator('input[name="title"]')).toBeVisible();
            await expect(page.locator('select[name="category"]')).toBeVisible();
            await expect(page.locator('[contenteditable="true"]')).toBeVisible();
        });

        await test.step('Walk pricing and shipping steps', async () => {
            await page.fill('input[name="title"]', `Smoke aukcija ${Date.now()}`);
            await page.click('button:has-text("Sljedeći korak")');
            await page.click('button:has-text("Sljedeći korak")');
            await expect(page.locator('input[name="start_price"]')).toBeVisible();
            await expect(page.locator('select[name="duration_days"]')).toBeVisible();
            await page.click('button:has-text("Sljedeći korak")');
            await expect(page.locator('select[name="shipping_method"]')).toBeVisible();
            await expect(page.locator('input[name="location"]')).toBeVisible();
        });
    });

    test('Admin smoke: login → key backoffice surfaces render', async ({ page }) => {
        const loginPage = new LoginPage(page);

        await test.step('Login as admin', async () => {
            await loginPage.loginAsAdmin();
            await page.waitForURL('/admin/dashboard');
            await expect(page.getByRole('heading', { name: 'Admin Dashboard' })).toBeVisible();
        });

        await test.step('Open moderation queues', async () => {
            await page.goto('/admin/aukcije');
            await expect(page.getByRole('heading', { name: 'Moderacija aukcija' })).toBeVisible();

            await page.goto('/admin/korisnici');
            await expect(page.getByRole('heading', { name: 'Korisnici' })).toBeVisible();

            await page.goto('/admin/sporovi');
            await expect(page.getByRole('heading', { name: 'Sporovi' })).toBeVisible();
        });
    });

    test('Buyer operations smoke: orders and notifications render', async ({ page }) => {
        const loginPage = new LoginPage(page);

        await loginPage.loginAsBuyer();
        await page.waitForURL('/dashboard');

        await page.goto('/orders');
        await expect(page.getByRole('heading', { name: 'Narudžbe' })).toBeVisible();

        await page.goto('/notifications');
        await expect(page.getByRole('heading', { name: 'Obavijesti' })).toBeVisible();
    });
});
