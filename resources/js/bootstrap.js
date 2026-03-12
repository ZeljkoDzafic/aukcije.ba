/**
 * ===================================
 * AUKCIJSKA PLATFORMA - BOOTSTRAP
 * ===================================
 * Configure Axios and Laravel Echo for real-time functionality
 */

import axios from 'axios';

// Configure Axios
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;

// Configure CSRF token
const token = document.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.warn('CSRF token meta tag not found');
}

/**
 * Laravel Echo Configuration
 * Used for WebSocket real-time functionality (Reverb)
 */
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Make Pusher available globally for Echo
window.Pusher = Pusher;

// Configure Echo
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
    
    // Encryption
    encrypted: true,
    
    // Authentication
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
        },
    },
    
    // Logging (disable in production)
    disableStats: true,

    // Retry logic
    maxReconnectionAttempts: 10,
    reconnectionDelay: 1000,
    
    // Error handling
    onError: (error) => {
        console.error('Echo connection error:', error);
    },
});

/**
 * Broadcast channels for real-time updates
 */

// Public channel for auction updates
export const auctionChannel = (auctionId) => {
    return window.Echo.channel(`auction.${auctionId}`);
};

// Private channel for user notifications
export const userChannel = (userId) => {
    return window.Echo.private(`user.${userId}`);
};

// Global channel for site-wide announcements
export const globalChannel = window.Echo.channel('aukcije.global');

/**
 * Event listeners for common events
 */

// Listen for bid updates on any auction page
export function listenToAuctionUpdates(auctionId, callbacks = {}) {
    const channel = auctionChannel(auctionId);
    
    // New bid placed
    channel.listen('BidPlaced', (data) => {
        console.log('📈 New bid placed:', data);
        callbacks.onBid?.(data);
    });
    
    // Auction extended (anti-sniping)
    channel.listen('AuctionExtended', (data) => {
        console.log('⏰ Auction extended:', data);
        callbacks.onExtended?.(data);
    });
    
    // Auction ended
    channel.listen('AuctionEnded', (data) => {
        console.log('🏁 Auction ended:', data);
        callbacks.onEnded?.(data);
    });
    
    return () => {
        channel.stopListening('BidPlaced');
        channel.stopListening('AuctionExtended');
        channel.stopListening('AuctionEnded');
    };
}

/**
 * Axios interceptors for error handling
 */

// Response interceptor
axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response) {
            const status = error.response.status;
            
            // Handle 401 Unauthorized
            if (status === 401) {
                console.warn('Unauthorized - redirecting to login');
                // window.location = '/login';
            }
            
            // Handle 403 Forbidden
            if (status === 403) {
                console.warn('Forbidden action');
            }
            
            // Handle 404 Not Found
            if (status === 404) {
                console.warn('Resource not found');
            }
            
            // Handle 419 CSRF Token Mismatch
            if (status === 419) {
                console.error('CSRF token mismatch - refreshing page');
                window.location.reload();
            }
            
            // Handle 429 Too Many Requests
            if (status === 429) {
                console.warn('Rate limit exceeded');
            }
            
            // Handle 500 Server Error
            if (status === 500) {
                console.error('Server error');
            }
        }
        
        return Promise.reject(error);
    }
);

export default axios;
