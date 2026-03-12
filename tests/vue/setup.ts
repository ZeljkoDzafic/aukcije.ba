/**
 * ===================================
 * VITEST SETUP FILE
 * ===================================
 * Global test setup for Vue component tests
 */

import '@testing-library/jest-dom';
import { config } from '@vue/test-utils';
import { vi } from 'vitest';

/**
 * Mock window.matchMedia
 */
Object.defineProperty(window, 'matchMedia', {
    writable: true,
    value: vi.fn().mockImplementation(query => ({
        matches: false,
        media: query,
        onchange: null,
        addListener: vi.fn(),
        removeListener: vi.fn(),
        addEventListener: vi.fn(),
        removeEventListener: vi.fn(),
        dispatchEvent: vi.fn(),
    })),
});

/**
 * Mock IntersectionObserver
 */
global.IntersectionObserver = class IntersectionObserver {
    constructor() {}
    disconnect() {}
    observe() {}
    takeRecords() {
        return [];
    }
    unobserve() {}
} as any;

/**
 * Global test utilities
 */

/**
 * Wait for next tick
 */
export const nextTick = async () => {
    await vi.runAllTimersAsync();
};

/**
 * Mock scrollIntoView
 */
Element.prototype.scrollIntoView = vi.fn();

/**
 * Mock localStorage
 */
const localStorageMock = {
    store: {} as Record<string, string>,
    clear() {
        this.store = {};
    },
    getItem(key: string) {
        return this.store[key] || null;
    },
    setItem(key: string, value: string) {
        this.store[key] = value.toString();
    },
    removeItem(key: string) {
        delete this.store[key];
    },
    get length() {
        return Object.keys(this.store).length;
    },
    key(index: number) {
        return Object.keys(this.store)[index] || null;
    },
};

Object.defineProperty(window, 'localStorage', {
    value: localStorageMock,
});

/**
 * Mock sessionStorage
 */
Object.defineProperty(window, 'sessionStorage', {
    value: localStorageMock,
});

/**
 * Configure Vue Test Utils
 */
config.global.stubs = {
    Transition: true,
    TransitionGroup: true,
    Teleport: true,
};

/**
 * Mock Echo (WebSocket)
 */
vi.mock('laravel-echo', () => ({
    default: class MockEcho {
        channel() {
            return {
                listen: vi.fn(),
                stopListening: vi.fn(),
            };
        }
        private() {
            return {
                listen: vi.fn(),
                stopListening: vi.fn(),
            };
        }
    },
}));

/**
 * Mock Alpine.js
 */
vi.mock('alpinejs', () => ({
    default: {
        start: vi.fn(),
        data: vi.fn(),
        magic: vi.fn(),
        directive: vi.fn(),
    },
}));

/**
 * Global test matchers
 */
expect.extend({
    toBeVisible(element: HTMLElement) {
        const isVisible = element.offsetParent !== null;
        return {
            pass: isVisible,
            message: () => `Expected element to be ${isVisible ? 'hidden' : 'visible'}`,
        };
    },
    toBeDisabled(element: HTMLElement) {
        const isDisabled = element.hasAttribute('disabled');
        return {
            pass: isDisabled,
            message: () => `Expected element to be ${isDisabled ? 'enabled' : 'disabled'}`,
        };
    },
});
