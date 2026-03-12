<?php

declare(strict_types=1);

return [

    // Navigation
    'nav' => [
        'home' => 'Home',
        'auctions' => 'Auctions',
        'categories' => 'Categories',
        'dashboard' => 'Dashboard',
        'watchlist' => 'Watchlist',
        'messages' => 'Messages',
        'wallet' => 'Wallet',
        'profile' => 'Profile',
        'settings' => 'Settings',
        'logout' => 'Logout',
        'login' => 'Login',
        'register' => 'Register',
    ],

    // Auctions
    'auctions' => [
        'title' => 'Auctions',
        'active' => 'Active Auctions',
        'ending_soon' => 'Ending Soon',
        'new' => 'New Auctions',
        'featured' => 'Featured',
        'create' => 'Create Auction',
        'edit' => 'Edit Auction',
        'view' => 'View Auction',
        'details' => 'Auction Details',
        'description' => 'Description',
        'category' => 'Category',
        'condition' => 'Condition',
        'start_price' => 'Start Price',
        'current_price' => 'Current Price',
        'buy_now' => 'Buy Now',
        'reserve_price' => 'Reserve Price',
        'duration' => 'Duration',
        'ends_at' => 'Ends At',
        'time_remaining' => 'Time Remaining',
        'bids' => 'Bids',
        'bid_count' => ':count bids',
        'watchers' => 'Watchers',
        'views' => 'Views',
        'seller' => 'Seller',
        'winner' => 'Winner',
        'status' => 'Status',
        'no_auctions' => 'No auctions found',
        'search_placeholder' => 'Search auctions...',
    ],

    // Bidding
    'bidding' => [
        'place_bid' => 'Place Bid',
        'your_bid' => 'Your Bid',
        'minimum_bid' => 'Minimum Bid',
        'bid_amount' => 'Bid Amount',
        'bid_history' => 'Bid History',
        'highest_bid' => 'Highest Bid',
        'you_are_winning' => 'You are winning!',
        'you_are_outbid' => 'You have been outbid!',
        'bid_placed' => 'Bid placed successfully',
        'bid_too_low' => 'Bid amount is too low',
        'cannot_bid_own' => 'You cannot bid on your own auction',
        'auction_ended' => 'This auction has ended',
        'proxy_bid' => 'Proxy Bid',
        'max_bid' => 'Maximum Bid',
        'auto_bid' => 'Auto-bid up to your maximum',
    ],

    // Auth
    'auth' => [
        'login' => 'Login',
        'register' => 'Register',
        'logout' => 'Logout',
        'email' => 'Email',
        'password' => 'Password',
        'password_confirm' => 'Confirm Password',
        'remember_me' => 'Remember me',
        'forgot_password' => 'Forgot Password?',
        'reset_password' => 'Reset Password',
        'send_reset_link' => 'Send Reset Link',
        'name' => 'Name',
        'phone' => 'Phone',
        'register_as' => 'Register as',
        'buyer' => 'Buyer',
        'seller' => 'Seller',
        'already_have_account' => 'Already have an account?',
        'dont_have_account' => "Don't have an account?",
        'verify_email' => 'Verify Email',
        'verification_sent' => 'Verification link sent',
        'login_success' => 'Login successful',
        'logout_success' => 'Logout successful',
        'register_success' => 'Registration successful',
    ],

    // User Types
    'user_types' => [
        'buyer' => 'Buyer',
        'seller' => 'Seller',
        'verified_seller' => 'Verified Seller',
        'admin' => 'Administrator',
        'moderator' => 'Moderator',
    ],

    // Dashboard
    'dashboard' => [
        'title' => 'Dashboard',
        'welcome' => 'Welcome, :name!',
        'active_bids' => 'Active Bids',
        'won_auctions' => 'Won Auctions',
        'watchlist_count' => 'Watchlist Items',
        'wallet_balance' => 'Wallet Balance',
        'recent_activity' => 'Recent Activity',
        'quick_links' => 'Quick Links',
    ],

    // Wallet
    'wallet' => [
        'title' => 'Wallet',
        'balance' => 'Balance',
        'available' => 'Available',
        'in_escrow' => 'In Escrow',
        'total' => 'Total',
        'deposit' => 'Deposit',
        'withdraw' => 'Withdraw',
        'transactions' => 'Transactions',
        'transaction_history' => 'Transaction History',
        'amount' => 'Amount',
        'type' => 'Type',
        'date' => 'Date',
        'status' => 'Status',
        'deposit_success' => 'Deposit successful',
        'withdraw_success' => 'Withdrawal successful',
        'insufficient_funds' => 'Insufficient funds',
    ],

    // Orders
    'orders' => [
        'title' => 'Orders',
        'order' => 'Order',
        'my_orders' => 'My Orders',
        'order_number' => 'Order #:number',
        'total' => 'Total',
        'status' => 'Status',
        'pending_payment' => 'Pending Payment',
        'paid' => 'Paid',
        'awaiting_shipment' => 'Awaiting Shipment',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'disputed' => 'Disputed',
        'pay_now' => 'Pay Now',
        'track_order' => 'Track Order',
        'confirm_delivery' => 'Confirm Delivery',
    ],

    // Shipping
    'shipping' => [
        'title' => 'Shipping',
        'method' => 'Shipping Method',
        'address' => 'Shipping Address',
        'city' => 'City',
        'postal_code' => 'Postal Code',
        'country' => 'Country',
        'tracking' => 'Tracking',
        'tracking_number' => 'Tracking Number',
        'courier' => 'Courier',
        'estimated_delivery' => 'Estimated Delivery',
        'shipped_by' => 'Shipped by :seller',
    ],

    // Categories
    'categories' => [
        'all' => 'All Categories',
        'electronics' => 'Electronics',
        'vehicles' => 'Vehicles',
        'fashion' => 'Fashion',
        'home_garden' => 'Home & Garden',
        'sports' => 'Sports & Outdoors',
        'collectibles' => 'Collectibles',
        'toys' => 'Toys & Hobbies',
        'other' => 'Other',
    ],

    // Conditions
    'conditions' => [
        'new' => 'New',
        'used' => 'Used',
        'refurbished' => 'Refurbished',
        'excellent' => 'Excellent',
        'good' => 'Good',
        'fair' => 'Fair',
        'poor' => 'Poor',
    ],

    // Messages
    'messages' => [
        'title' => 'Messages',
        'send' => 'Send',
        'reply' => 'Reply',
        'message' => 'Message',
        'from' => 'From',
        'to' => 'To',
        'subject' => 'Subject',
        'no_messages' => 'No messages',
        'write_message' => 'Write a message',
    ],

    // Notifications
    'notifications' => [
        'title' => 'Notifications',
        'mark_read' => 'Mark as read',
        'mark_unread' => 'Mark as unread',
        'delete' => 'Delete',
        'no_notifications' => 'No notifications',
        'outbid' => 'You have been outbid',
        'won' => 'You won an auction',
        'payment_received' => 'Payment received',
        'item_shipped' => 'Item shipped',
    ],

    // Validation
    'validation' => [
        'required' => 'This field is required',
        'email' => 'Please enter a valid email',
        'min' => 'Minimum :min characters',
        'max' => 'Maximum :max characters',
        'numeric' => 'Please enter a number',
        'confirmed' => 'Confirmation does not match',
        'unique' => 'This value is already taken',
        'accepted' => 'Must be accepted',
    ],

    // Errors
    'errors' => [
        'not_found' => 'Not found',
        'unauthorized' => 'Unauthorized',
        'forbidden' => 'Forbidden',
        'server_error' => 'Server error',
        'page_not_found' => 'Page not found',
        'go_home' => 'Go Home',
    ],

    // Buttons
    'buttons' => [
        'save' => 'Save',
        'cancel' => 'Cancel',
        'delete' => 'Delete',
        'edit' => 'Edit',
        'view' => 'View',
        'search' => 'Search',
        'filter' => 'Filter',
        'reset' => 'Reset',
        'submit' => 'Submit',
        'confirm' => 'Confirm',
        'back' => 'Back',
        'next' => 'Next',
        'previous' => 'Previous',
        'close' => 'Close',
        'yes' => 'Yes',
        'no' => 'No',
    ],

    // Time
    'time' => [
        'days' => 'days',
        'hours' => 'hours',
        'minutes' => 'minutes',
        'seconds' => 'seconds',
        'day' => 'day',
        'hour' => 'hour',
        'minute' => 'minute',
        'second' => 'second',
        'ago' => 'ago',
        'just_now' => 'Just now',
    ],

    // Status
    'status' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'success' => 'Success',
        'error' => 'Error',
        'warning' => 'Warning',
        'info' => 'Info',
    ],

    // Footer
    'footer' => [
        'about' => 'About',
        'contact' => 'Contact',
        'terms' => 'Terms of Service',
        'privacy' => 'Privacy Policy',
        'help' => 'Help',
        'faq' => 'FAQ',
        'copyright' => '© :year Aukcije.ba. All rights reserved.',
    ],

    // Misc
    'misc' => [
        'loading' => 'Loading...',
        'no_results' => 'No results found',
        'show_more' => 'Show more',
        'show_less' => 'Show less',
        'read_more' => 'Read more',
        'share' => 'Share',
        'copy' => 'Copy',
        'copied' => 'Copied!',
        'success' => 'Success',
        'error' => 'Error',
    ],

];
