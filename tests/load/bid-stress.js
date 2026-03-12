/**
 * ===================================
 * K6 LOAD TEST CONFIGURATION
 * ===================================
 * Performance and load testing
 */

import http from 'k6/http';
import { check, sleep } from 'k6';
import ws from 'k6/ws';

// Test configuration
export const options = {
    // Common thresholds
    thresholds: {
        http_req_duration: ['p(95)<500', 'p(99)<1000'], // 95% of requests should be below 500ms
        http_req_failed: ['rate<0.01'], // Error rate should be less than 1%
        http_reqs: ['rate>100'], // At least 100 requests per second
    },

    // Scenarios
    scenarios: {
        // Normal browsing traffic
        browse_auctions: {
            executor: 'constant-vus',
            vus: 50,
            duration: '5m',
            exec: 'browseAuctions',
            tags: { type: 'browse' },
        },

        // Bidding spike (auction ending)
        bidding_spike: {
            executor: 'ramping-vus',
            startVUs: 10,
            stages: [
                { duration: '30s', target: 100 },
                { duration: '1m', target: 500 }, // Peak: 500 concurrent bidders
                { duration: '30s', target: 0 },
            ],
            exec: 'placeBids',
            tags: { type: 'bidding' },
        },

        // Search stress test
        search_stress: {
            executor: 'constant-vus',
            vus: 200,
            duration: '3m',
            exec: 'searchAuctions',
            tags: { type: 'search' },
        },

        // WebSocket connections
        websocket_connections: {
            executor: 'ramping-vus',
            startVUs: 10,
            stages: [
                { duration: '1m', target: 100 },
                { duration: '5m', target: 1000 }, // 1000 concurrent WS connections
                { duration: '1m', target: 0 },
            ],
            exec: 'websocketTest',
            tags: { type: 'websocket' },
        },
    },
};

/**
 * Browse auctions scenario
 */
export function browseAuctions() {
    const baseUrl = __ENV.BASE_URL || 'http://localhost:8000';

    // Homepage
    let res = http.get(baseUrl);
    check(res, {
        'homepage status is 200': (r) => r.status === 200,
        'homepage loads in time': (r) => r.timings.duration < 500,
    });
    sleep(1);

    // Auction listing
    res = http.get(`${baseUrl}/aukcije`);
    check(res, {
        'auction list status is 200': (r) => r.status === 200,
    });
    sleep(1);

    // Filter by category
    res = http.get(`${baseUrl}/aukcije?category=elektronika`);
    check(res, {
        'filtered list status is 200': (r) => r.status === 200,
    });
    sleep(1);

    // Sort by ending soon
    res = http.get(`${baseUrl}/aukcije?sort=ending_soon`);
    check(res, {
        'sorted list status is 200': (r) => r.status === 200,
    });
    sleep(1);
}

/**
 * Place bids scenario (high concurrency)
 */
export function placeBids() {
    const baseUrl = __ENV.BASE_URL || 'http://localhost:8000';
    const auctionId = __ENV.TEST_AUCTION_ID || 'test-auction-uuid';

    // Get auth token (in real test, this would be from login)
    const token = authenticate();

    // Place bid
    const payload = JSON.stringify({
        amount: Math.floor(Math.random() * 100) + 50,
    });

    const params = {
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
            'X-CSRF-TOKEN': getCsrfToken(),
        },
    };

    const res = http.post(`${baseUrl}/api/v1/auctions/${auctionId}/bid`, payload, params);

    check(res, {
        'bid status is 200 or 409': (r) => [200, 409].includes(r.status),
        'bid response time < 500ms': (r) => r.timings.duration < 500,
    });

    sleep(Math.random() * 2);
}

/**
 * Search auctions scenario
 */
export function searchAuctions() {
    const baseUrl = __ENV.BASE_URL || 'http://localhost:8000';

    const searchTerms = ['Samsung', 'iPhone', 'Laptop', 'Sat', 'Kamera'];
    const term = searchTerms[Math.floor(Math.random() * searchTerms.length)];

    const res = http.get(`${baseUrl}/aukcije?search=${term}`);

    check(res, {
        'search status is 200': (r) => r.status === 200,
        'search response time < 300ms': (r) => r.timings.duration < 300,
    });

    sleep(0.5);
}

/**
 * WebSocket test scenario
 */
export function websocketTest() {
    const baseUrl = __ENV.BASE_URL || 'http://localhost:8000';
    const auctionId = __ENV.TEST_AUCTION_ID || 'test-auction-uuid';

    const wsUrl = `ws://localhost:8080/app/auction.${auctionId}`;

    const res = ws.connect(wsUrl, {}, function (socket) {
        socket.on('open', () => {
            console.log('WebSocket connected');
        });

        socket.on('message', (data) => {
            console.log('Message received:', data);
        });

        socket.on('close', () => {
            console.log('WebSocket closed');
        });

        socket.on('error', (error) => {
            console.log('WebSocket error:', error);
        });

        socket.setTimeout(() => {
            socket.close();
        }, 30000);
    });

    check(res, {
        'websocket status is 101': (r) => r && r.status === 101,
    });
}

/**
 * Helper: Authenticate user
 */
function authenticate() {
    const baseUrl = __ENV.BASE_URL || 'http://localhost:8000';

    const res = http.post(`${baseUrl}/_test/login`, {
        email: 'buyer@test.com',
        password: 'Password123!',
    });

    if (res.status === 200) {
        const body = JSON.parse(res.body);
        return body.token;
    }

    return '';
}

/**
 * Helper: Get CSRF token
 */
function getCsrfToken() {
    // In real test, this would fetch from cookie or meta tag
    return 'test-csrf-token';
}

/**
 * Default function (required by k6)
 */
export default function () {
    browseAuctions();
}
