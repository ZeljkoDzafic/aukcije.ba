# 🔧 Troubleshooting Guide

## Common Issues and Solutions

---

## Table of Contents

1. [User Issues](#user-issues)
2. [Seller Issues](#seller-issues)
3. [Payment Issues](#payment-issues)
4. [Shipping Issues](#shipping-issues)
5. [Technical Issues](#technical-issues)
6. [Admin Issues](#admin-issues)
7. [Error Codes](#error-codes)
8. [Contact Support](#contact-support)

---

## User Issues

### Cannot Login

**Symptoms:**
- "Invalid credentials" error
- Page refreshes without error
- MFA code not working

**Solutions:**

1. **Reset Password:**
   - Click "Forgot Password?"
   - Enter email address
   - Check inbox for reset link
   - Create new password

2. **Clear Browser Cache:**
   - Press Ctrl+Shift+Delete
   - Clear cookies and cache
   - Reload page

3. **Check Account Status:**
   - Email may not be verified
   - Account may be suspended
   - Contact support if banned

4. **MFA Issues:**
   - Check time is synchronized
   - Use backup codes
   - Contact support to reset MFA

### Cannot Place Bid

**Error Messages:**
- "Bid too low" → Increase bid amount
- "Auction not active" → Auction may have ended
- "Cannot bid on own auction" → Use different account
- "Insufficient funds" → Add funds to wallet
- "Email not verified" → Verify email first

**Solutions:**

1. **Check Minimum Bid:**
   - Current price + increment
   - Increment shown on auction page

2. **Verify Account:**
   - Email must be verified
   - Phone verification for high-value bids

3. **Check Wallet Balance:**
   - Go to Wallet
   - Add funds if insufficient

4. **Rate Limit:**
   - Maximum 10 bids per minute
   - Wait and try again

### Not Receiving Notifications

**Check:**

1. **Email Settings:**
   - Go to Profile → Notifications
   - Ensure email notifications enabled
   - Check spam folder

2. **Browser Notifications:**
   - Check browser permissions
   - Re-enable notifications

3. **SMS Notifications:**
   - Verify phone number
   - Check SMS balance (if applicable)

---

## Seller Issues

### Cannot Create Auction

**Error Messages:**
- "Auction limit reached" → Upgrade tier or wait
- "KYC not verified" → Complete KYC verification
- "Invalid category" → Select valid category
- "Missing required fields" → Fill all required fields

**Solutions:**

1. **Check Tier Limits:**
   - Free: 5 auctions/month
   - Premium: 50 auctions/month
   - Storefront: Unlimited

2. **Complete KYC:**
   - Level 2 required for selling
   - Upload ID document
   - Wait for approval (24-48 hours)

3. **Check Draft Auctions:**
   - Delete unused drafts
   - Drafts count toward limit

### Auction Not Appearing

**Possible Causes:**
- Pending moderation
- Rejected by admin
- Category incorrect
- Technical issue

**Solutions:**

1. **Check Status:**
   - Go to Seller Dashboard → My Auctions
   - Check status column
   - Pending = waiting for approval
   - Rejected = check email for reason

2. **Wait for Moderation:**
   - Standard: 2-4 hours
   - Peak times: up to 24 hours
   - Contact support if >24 hours

3. **Check Email:**
   - Rejection reasons sent via email
   - Fix issues and resubmit

### Cannot Ship Item

**Issues:**
- Cannot generate waybill
- Tracking not working
- Courier not available

**Solutions:**

1. **Generate Waybill:**
   - Go to Orders → Shipped
   - Click "Generate Waybill"
   - Download PDF

2. **Check Address:**
   - Verify buyer address is complete
   - Contact buyer if address incomplete

3. **Courier Issues:**
   - Try different courier
   - Contact courier support
   - Use manual tracking number

---

## Payment Issues

### Payment Failed

**Error Messages:**
- "Card declined" → Contact bank
- "Insufficient funds" → Add funds or use different card
- "Invalid card details" → Check card number, expiry, CVV
- "3D Secure failed" → Complete authentication

**Solutions:**

1. **Check Card Details:**
   - Card number (16 digits)
   - Expiry date (MM/YY)
   - CVV (3 digits on back)
   - Name on card

2. **Try Different Payment Method:**
   - Use different card
   - Use wallet balance
   - Try bank transfer

3. **Contact Bank:**
   - Card may be blocked
   - International transactions may be disabled
   - Daily limit may be exceeded

### Wallet Issues

**Cannot Deposit:**
- Minimum deposit: 10 BAM
- Maximum deposit: 10,000 BAM/day
- Check payment method is valid

**Cannot Withdraw:**
- Minimum withdrawal: 20 BAM
- Maximum withdrawal: 5,000 BAM/day
- KYC Level 3 required for large withdrawals
- Bank account must be verified

**Solutions:**

1. **Verify Bank Account:**
   - Go to Wallet → Withdraw
   - Enter bank details
   - Small test deposit made
   - Confirm amount to verify

2. **Check Limits:**
   - Daily limits apply
   - Monthly limits may apply
   - Contact support for limit increase

### Refund Issues

**Refund Not Received:**
- Processing time: 5-10 business days
- Check bank statement
- Contact support if >10 days

**Partial Refund:**
- Agreed with seller
- Dispute resolution
- Admin-approved

---

## Shipping Issues

### Package Not Delivered

**Check:**

1. **Tracking Status:**
   - Go to Orders → Track
   - Enter tracking number
   - Check courier website

2. **Delivery Attempts:**
   - Courier may have attempted delivery
   - Check for delivery notice
   - Contact local post office

3. **Address Issues:**
   - Address may be incorrect
   - Buyer may not be available
   - Contact courier to reschedule

### Damaged Package

**Actions:**

1. **Document Damage:**
   - Take photos of package
   - Take photos of item
   - Keep packaging

2. **Contact Seller:**
   - Report within 24 hours
   - Provide photos
   - Request resolution

3. **Open Dispute:**
   - If seller unresponsive
   - Within 7 days of delivery
   - Provide all evidence

### Wrong Item Received

**Actions:**

1. **Contact Seller Immediately:**
   - Explain situation
   - Provide photos
   - Request correct item or refund

2. **Return Item:**
   - Seller should provide return shipping
   - Use tracked shipping
   - Keep tracking number

3. **Open Dispute:**
   - If seller uncooperative
   - Within 7 days
   - Provide all evidence

---

## Technical Issues

### Website Not Loading

**Solutions:**

1. **Check Internet Connection:**
   - Try other websites
   - Restart router
   - Try mobile data

2. **Clear Browser Cache:**
   - Press Ctrl+Shift+Delete
   - Clear all data
   - Restart browser

3. **Try Different Browser:**
   - Chrome, Firefox, Safari, Edge
   - Update browser to latest version

4. **Check Status Page:**
   - Visit status.aukcije.ba
   - Check for known outages

### Mobile App Issues

**App Crashes:**
- Update to latest version
- Clear app cache
- Reinstall app
- Check device compatibility

**App Not Loading:**
- Check internet connection
- Force close and reopen
- Clear app data
- Reinstall app

### WebSocket Connection Issues

**Symptoms:**
- Real-time updates not working
- Bid confirmations delayed
- Countdown timer not updating

**Solutions:**
- Refresh page
- Check browser console for errors
- Disable ad blockers
- Try different browser

---

## Admin Issues

### Cannot Access Admin Panel

**Possible Causes:**
- Insufficient permissions
- IP not whitelisted
- MFA not enabled
- Account suspended

**Solutions:**
1. Check role has admin access
2. Contact IT to whitelist IP
3. Enable MFA in profile
4. Contact super admin

### Moderation Queue Stuck

**Solutions:**
1. Refresh page
2. Clear browser cache
3. Check for system notifications
4. Contact technical support

### Reports Not Generating

**Solutions:**
1. Reduce date range
2. Reduce data columns
3. Try CSV instead of PDF
4. Contact support for large reports

---

## Error Codes

### HTTP Error Codes

| Code | Meaning | Solution |
|------|---------|----------|
| 400 | Bad Request | Check request format |
| 401 | Unauthorized | Login required |
| 403 | Forbidden | Insufficient permissions |
| 404 | Not Found | Page doesn't exist |
| 419 | CSRF Token Mismatch | Refresh page |
| 429 | Too Many Requests | Wait and retry |
| 500 | Server Error | Contact support |
| 502 | Bad Gateway | Try again later |
| 503 | Service Unavailable | Maintenance mode |

### Application Error Codes

| Code | Meaning | Solution |
|------|---------|----------|
| BID_TOO_LOW | Bid below minimum increment | Increase bid amount |
| AUCTION_NOT_ACTIVE | Auction not in active status | Check auction status |
| CANNOT_BID_OWN | Trying to bid on own auction | Use different account |
| INSUFFICIENT_FUNDS | Wallet balance too low | Add funds to wallet |
| KYC_REQUIRED | Action requires KYC verification | Complete KYC |
| RATE_LIMIT_EXCEEDED | Too many requests | Wait and retry |
| AUCTION_ENDED | Auction has ended | Find another auction |
| PAYMENT_FAILED | Payment processor declined | Try different payment method |

---

## Contact Support

### Support Channels

**Email:**
- General: support@aukcije.ba
- Technical: tech@aukcije.ba
- Security: security@aukcije.ba
- Legal: legal@aukcije.ba

**Phone:**
- Support: +387 33 000 000
- Hours: Mon-Fri, 9:00-17:00

**Live Chat:**
- Available on website
- Hours: Mon-Fri, 9:00-17:00

### Response Times

| Issue Type | Response Time |
|------------|---------------|
| Critical (site down) | < 1 hour |
| High (payment issue) | < 4 hours |
| Medium (account issue) | < 24 hours |
| Low (general inquiry) | < 48 hours |

### Information to Provide

**When contacting support:**
- Email address associated with account
- Description of issue
- Steps to reproduce
- Screenshots (if applicable)
- Error messages
- Browser and version
- Device type

### Escalation

**If issue not resolved:**
1. Reply to support ticket
2. Request escalation
3. Contact support lead
4. Submit formal complaint

---

## Self-Service Resources

### Help Center

**Articles:**
- Getting started guide
- Buying tutorial
- Selling tutorial
- Payment guide
- Shipping guide
- Safety tips

**Video Tutorials:**
- How to place bid
- How to create auction
- How to use wallet
- How to ship item

### Community Forum

**Categories:**
- General discussion
- Buying tips
- Selling tips
- Technical support
- Feature requests

### Status Page

**URL:** status.aukcije.ba

**Information:**
- Current system status
- Scheduled maintenance
- Incident history
- Performance metrics

---

**Last Updated:** March 2026  
**Version:** 1.0
