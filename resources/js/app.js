/**
 * ===================================
 * AUKCIJSKA PLATFORMA - MAIN JS ENTRY
 * ===================================
 */

import './bootstrap';
import '../css/app.css';

// Import Alpine.js for interactivity
import Alpine from 'alpinejs';

// Make Alpine available globally
window.Alpine = Alpine;

// Start Alpine
Alpine.start();

// Log application info
console.log('🎉 Aukcijska Platforma loaded successfully');
console.log('📦 Environment:', import.meta.env.MODE);
console.log('🔗 Echo configured:', typeof window.Echo !== 'undefined');
