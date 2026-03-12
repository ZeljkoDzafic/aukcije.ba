/**
 * ===================================
 * API HELPER FOR E2E TESTS
 * ===================================
 * Helper functions for API calls during tests
 */

import { APIRequestContext } from '@playwright/test';

/**
 * API Helper class
 */
export class ApiHelper {
    readonly request: APIRequestContext;
    readonly baseUrl: string;

    constructor(request: APIRequestContext, baseUrl: string) {
        this.request = request;
        this.baseUrl = baseUrl;
    }

    /**
     * Create a test user via API
     */
    async createUser(data: {
        name: string;
        email: string;
        password: string;
        type: 'buyer' | 'seller';
    }) {
        const response = await this.request.post(`${this.baseUrl}/_test/users`, {
            data,
        });
        return await response.json();
    }

    /**
     * Create a test auction via API
     */
    async createAuction(data: {
        sellerId: string;
        title: string;
        description: string;
        startPrice: number;
        duration: number;
        categoryId?: string;
    }) {
        const response = await this.request.post(`${this.baseUrl}/_test/auctions`, {
            data,
        });
        return await response.json();
    }

    /**
     * Create a test bid via API
     */
    async createBid(data: {
        auctionId: string;
        userId: string;
        amount: number;
    }) {
        const response = await this.request.post(`${this.baseUrl}/_test/bids`, {
            data,
        });
        return await response.json();
    }

    /**
     * Login and get auth token
     */
    async login(email: string, password: string) {
        const response = await this.request.post(`${this.baseUrl}/_test/login`, {
            data: { email, password },
        });
        return await response.json();
    }

    /**
     * Delete test data
     */
    async cleanup() {
        await this.request.post(`${this.baseUrl}/_test/cleanup`);
    }

    /**
     * Get database state
     */
    async getDbState() {
        const response = await this.request.get(`${this.baseUrl}/_test/db-state`);
        return await response.json();
    }

    /**
     * Reset database to clean state
     */
    async resetDatabase() {
        await this.request.post(`${this.baseUrl}/_test/reset`);
    }

    /**
     * Seed specific test data
     */
    async seed(data: {
        users?: boolean;
        auctions?: boolean;
        categories?: boolean;
    }) {
        const response = await this.request.post(`${this.baseUrl}/_test/seed`, {
            data,
        });
        return await response.json();
    }
}

/**
 * Create API helper instance
 */
export function createApiHelper(request: APIRequestContext, baseUrl: string): ApiHelper {
    return new ApiHelper(request, baseUrl);
}
