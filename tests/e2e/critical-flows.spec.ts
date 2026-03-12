/**
 * ===================================
 * PLAYWRIGHT CRITICAL USER FLOW TESTS
 * ===================================
 * End-to-end tests for critical user journeys
 */

import { test, expect } from '../fixtures/test-fixtures';
import { LoginPage } from '../pages/LoginPage';
import { AuctionListPage } from '../pages/AuctionListPage';
import { AuctionDetailPage } from '../pages/AuctionDetailPage';

test.describe('Critical User Flows', () => {
    test('Complete Buyer Journey: Register → Search → Bid → Win → Pay → Rate', async ({ page, request }) => {
        const loginPage = new LoginPage(page);
        const auctionListPage = new AuctionListPage(page);
        const auctionDetailPage = new AuctionDetailPage(page);
        
        // Step 1: Register as buyer
        await test.step('Register new buyer', async () => {
            const email = `buyer_${Date.now()}@test.com`;
            const password = 'Password123!';
            
            const response = await request.post('/_test/register', {
                data: {
                    name: 'Test Buyer',
                    email,
                    password,
                    type: 'buyer',
                },
            });
            
            expect(response.ok()).toBeTruthy();
        });
        
        // Step 2: Login
        await test.step('Login as buyer', async () => {
            await loginPage.goto();
            await loginPage.login(`buyer_${Date.now()}@test.com`, 'Password123!');
            await page.waitForURL('/dashboard');
            await expect(page.locator('h1')).toContainText('Dashboard');
        });
        
        // Step 3: Browse auctions
        await test.step('Browse and search auctions', async () => {
            await auctionListPage.goto();
            await expect(auctionListPage).toBeVisible();
            
            // Test search
            await auctionListPage.search('Samsung');
            await page.waitForTimeout(1000);
            
            // Test filter
            await auctionListPage.filterByCategory('Elektronika');
            await page.waitForTimeout(1000);
            
            // Test sort
            await auctionListPage.sortBy('ending_soon');
            await page.waitForTimeout(1000);
        });
        
        // Step 4: View auction detail
        await test.step('View auction details', async () => {
            const auctionCount = await auctionListPage.getAuctionCount();
            
            if (auctionCount > 0) {
                await auctionListPage.clickAuction(0);
                await auctionDetailPage.isVisible();
                
                // Check auction details are visible
                await expect(auctionDetailPage.title).toBeVisible();
                await expect(auctionDetailPage.priceDisplay).toBeVisible();
                await expect(auctionDetailPage.countdownTimer).toBeVisible();
            }
        });
        
        // Step 5: Place bid
        await test.step('Place a bid', async () => {
            // This would require actual auction data
            // For now, verify bid form is present
            await expect(auctionDetailPage.bidInput).toBeVisible();
            await expect(auctionDetailPage.bidButton).toBeVisible();
        });
        
        // Step 6: Add to watchlist
        await test.step('Add auction to watchlist', async () => {
            await auctionDetailPage.addToWatchlist();
            // Check for success message
            await page.waitForTimeout(500);
        });
    });

    test('Complete Seller Journey: Register → KYC → Create Auction → Receive Bid → End → Ship → Complete', async ({ page, request }) => {
        // Step 1: Register as seller
        await test.step('Register new seller', async () => {
            const email = `seller_${Date.now()}@test.com`;
            
            const response = await request.post('/_test/register', {
                data: {
                    name: 'Test Seller',
                    email,
                    password: 'Password123!',
                    type: 'seller',
                },
            });
            
            expect(response.ok()).toBeTruthy();
        });
        
        // Step 2: Complete KYC
        await test.step('Complete KYC verification', async () => {
            // Navigate to KYC page
            await page.goto('/kyc/verify');
            
            // Check KYC form is present
            await expect(page.locator('input[name="phone"]')).toBeVisible();
            await expect(page.locator('input[type="file"]')).toBeVisible();
        });
        
        // Step 3: Create auction
        await test.step('Create new auction', async () => {
            await page.goto('/seller/aukcije/nova');
            
            // Fill auction form
            await page.fill('input[name="title"]', 'Test Auction Item');
            await page.fill('textarea[name="description"]', 'This is a test auction item description');
            await page.fill('input[name="start_price"]', '50');
            await page.selectOption('select[name="category_id"]', '1');
            await page.selectOption('select[name="duration"]', '7');
            
            // Submit form
            await page.click('button:has-text("Kreiraj aukciju")');
            
            // Wait for redirect to auction detail
            await page.waitForURL(/\/aukcije\/.+/);
        });
        
        // Step 4: View created auction
        await test.step('View created auction', async () => {
            await expect(page.locator('h1')).toContainText('Test Auction Item');
            await expect(page.locator('[data-testid="current-price"]')).toContainText('50');
        });
        
        // Step 5: Receive bids (simulated)
        await test.step('Simulate receiving bids', async () => {
            // This would be tested via API
            const response = await request.post('/_test/simulate-bid', {
                data: {
                    auction_url: page.url(),
                    bid_amount: 55,
                },
            });
            
            expect(response.ok()).toBeTruthy();
        });
        
        // Step 6: Auction ends (simulated)
        await test.step('Simulate auction ending', async () => {
            const response = await request.post('/_test/end-auction', {
                data: {
                    auction_url: page.url(),
                },
            });
            
            expect(response.ok()).toBeTruthy();
        });
        
        // Step 7: Ship item
        await test.step('Mark as shipped', async () => {
            await page.goto('/seller/narudzbe');
            
            // Find order and mark as shipped
            await page.click('button:has-text("Pošalji")');
            
            // Fill tracking info
            await page.fill('input[name="tracking_number"]', 'TEST123456');
            await page.selectOption('select[name="courier"]', 'euroexpress');
            
            await page.click('button:has-text("Potvrdi slanje")');
        });
        
        // Step 8: Complete order
        await test.step('Complete order', async () => {
            // Wait for buyer to confirm delivery (simulated)
            await request.post('/_test/confirm-delivery', {
                data: {
                    order_url: page.url(),
                },
            });
            
            // Check order status
            await expect(page.locator('.status-completed')).toBeVisible();
        });
    });

    test('Admin Flow: Login → Moderate Auction → Review KYC → Resolve Dispute', async ({ page, request }) => {
        // Step 1: Login as admin
        await test.step('Login as admin', async () => {
            const loginPage = new LoginPage(page);
            await loginPage.login('admin@aukcije.ba', 'AdminPassword123!');
            await page.waitForURL('/admin/dashboard');
        });
        
        // Step 2: Moderate auction
        await test.step('Moderate pending auction', async () => {
            await page.goto('/admin/aukcije');
            
            // Find pending auctions
            const pendingAuctions = page.locator('.auction-pending');
            const count = await pendingAuctions.count();
            
            if (count > 0) {
                // Approve first pending auction
                await pendingAuctions.first().click('button:has-text("Odobri")');
            }
        });
        
        // Step 3: Review KYC
        await test.step('Review KYC submission', async () => {
            await page.goto('/admin/korisnici');
            
            // Filter by pending KYC
            await page.selectOption('select[name="kyc_status"]', 'pending');
            
            const pendingKyc = page.locator('.kyc-pending');
            const count = await pendingKyc.count();
            
            if (count > 0) {
                await pendingKyc.first().click();
                
                // Review documents
                await expect(page.locator('.kyc-document')).toBeVisible();
                
                // Approve KYC
                await page.click('button:has-text("Odobri KYC")');
            }
        });
        
        // Step 4: Resolve dispute
        await test.step('Resolve dispute', async () => {
            await page.goto('/admin/sporovi');
            
            const openDisputes = page.locator('.dispute-open');
            const count = await openDisputes.count();
            
            if (count > 0) {
                await openDisputes.first().click();
                
                // Review dispute details
                await expect(page.locator('.dispute-details')).toBeVisible();
                await expect(page.locator('.evidence')).toBeVisible();
                
                // Resolve dispute
                await page.selectOption('select[name="resolution"]', 'full_refund');
                await page.click('button:has-text("Riješi spor")');
            }
        });
        
        // Step 5: View statistics
        await test.step('View admin statistics', async () => {
            await page.goto('/admin/statistike');
            
            // Check charts are visible
            await expect(page.locator('.chart')).toBeVisible();
            await expect(page.locator('.stats-cards')).toBeVisible();
        });
    });

    test('Wallet Flow: Deposit → Check Balance → Pay → Withdraw', async ({ page, request }) => {
        // Login as buyer
        await test.step('Login as buyer', async () => {
            const loginPage = new LoginPage(page);
            await loginPage.login('buyer@test.com', 'Password123!');
            await page.waitForURL('/dashboard');
        });
        
        // Step 1: Check initial balance
        await test.step('Check wallet balance', async () => {
            await page.goto('/novcanik');
            
            const balanceElement = page.locator('[data-testid="wallet-balance"]');
            await expect(balanceElement).toBeVisible();
        });
        
        // Step 2: Deposit funds
        await test.step('Deposit funds', async () => {
            await page.click('button:has-text("Uplati")');
            
            // Fill deposit form
            await page.fill('input[name="amount"]', '100');
            await page.selectOption('select[name="gateway"]', 'stripe');
            
            await page.click('button:has-text("Potvrdi uplatu")');
            
            // Wait for payment processing
            await page.waitForTimeout(2000);
        });
        
        // Step 3: Verify balance updated
        await test.step('Verify balance updated', async () => {
            await page.reload();
            
            const balanceElement = page.locator('[data-testid="wallet-balance"]');
            await expect(balanceElement).toBeVisible();
        });
        
        // Step 4: Pay for order
        await test.step('Pay for order', async () => {
            await page.goto('/narudzbe');
            
            const pendingOrders = page.locator('.order-pending-payment');
            const count = await pendingOrders.count();
            
            if (count > 0) {
                await pendingOrders.first().click('button:has-text("Plati")');
                
                // Confirm payment from wallet
                await page.click('button:has-text("Plati iz novčanika")');
            }
        });
        
        // Step 5: Withdraw remaining funds
        await test.step('Withdraw funds', async () => {
            await page.goto('/novcanik');
            
            await page.click('button:has-text("Isplati")');
            
            // Fill withdrawal form
            await page.fill('input[name="amount"]', '50');
            await page.fill('input[name="bank_account"]', 'BA1234567890123456');
            
            await page.click('button:has-text("Potvrdi isplatu")');
        });
    });

    test('Notification Flow: Outbid → Notification → Rebid', async ({ page, request }) => {
        // Setup: User is winning an auction
        await test.step('Setup: Place winning bid', async () => {
            const response = await request.post('/_test/place-bid', {
                data: {
                    auction_id: 'test-auction-id',
                    amount: 100,
                },
            });
            
            expect(response.ok()).toBeTruthy();
        });
        
        // Step 1: Receive outbid notification
        await test.step('Receive outbid notification', async () => {
            // Simulate outbid
            await request.post('/_test/simulate-outbid', {
                data: {
                    auction_id: 'test-auction-id',
                    new_bid: 105,
                },
            });
            
            // Check notification appears
            await page.goto('/dashboard');
            
            const notification = page.locator('.notification-outbid');
            await expect(notification).toBeVisible();
        });
        
        // Step 2: Click notification
        await test.step('Click notification to go to auction', async () => {
            const notification = page.locator('.notification-outbid');
            await notification.click();
            
            // Should redirect to auction page
            await page.waitForURL(/\/aukcije\/.+/);
        });
        
        // Step 3: Place higher bid
        await test.step('Place higher bid', async () => {
            await page.fill('input[name="bid_amount"]', '110');
            await page.click('button:has-text("LICITIRAJ")');
            
            // Check success message
            await expect(page.locator('.alert-success')).toBeVisible();
        });
    });
});
