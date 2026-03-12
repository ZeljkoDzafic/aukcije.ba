# 👨‍💼 Admin Guide

## Aukcije.ba Administration Manual

This guide is for administrators and moderators managing the Aukcije.ba platform.

---

## Table of Contents

1. [Getting Started](#getting-started)
2. [Admin Dashboard](#admin-dashboard)
3. [User Management](#user-management)
4. [Auction Moderation](#auction-moderation)
5. [Category Management](#category-management)
6. [Dispute Resolution](#dispute-resolution)
7. [KYC Verification](#kyc-verification)
8. [Feature Flags](#feature-flags)
9. [Statistics & Reports](#statistics--reports)
10. [System Settings](#system-settings)
11. [Security Procedures](#security-procedures)
12. [Emergency Procedures](#emergency-procedures)

---

## Getting Started

### Accessing Admin Panel

**URL:** https://aukcije.ba/admin

**Requirements:**
- Admin or Moderator role
- MFA enabled
- IP whitelist (for sensitive actions)

### Admin Roles

| Role | Permissions |
|------|-------------|
| **Super Admin** | Full access to all features |
| **Moderator** | Content moderation, user management, dispute resolution |
| **Support** | User support, basic moderation |
| **Finance** | Payment oversight, refund approval |

### First Login

1. Receive invitation email
2. Set up password
3. Enable MFA (required)
4. Complete security training
5. Review admin guidelines

---

## Admin Dashboard

### Overview

The dashboard provides real-time overview of platform health:

**Key Metrics:**
- Total users (active, new today)
- Active auctions
- Today's bids
- Revenue (today, this month)
- Open disputes
- Pending KYC reviews

**Quick Actions:**
- Moderate auction
- Review KYC
- Resolve dispute
- Ban user
- Send announcement

**Recent Activity:**
- New user registrations
- Large transactions
- Dispute openings
- Flagged content

### Monitoring Alerts

**Real-time alerts for:**
- High-value transactions (>1000 BAM)
- Multiple failed login attempts
- Unusual bidding patterns
- System errors
- Queue backlog

---

## User Management

### Viewing Users

**Navigate to:** Admin → Korisnici

**Filters:**
- Search by name, email
- Role (buyer, seller, admin)
- KYC status (pending, approved, rejected)
- Status (active, banned, suspended)
- Registration date

### User Details

**Information available:**
- Profile information
- Email/phone verification status
- KYC level and documents
- Auction history (as buyer and seller)
- Transaction history
- Ratings and reviews
- Dispute history
- Login history (IP, device)

### Actions

**Edit User:**
- Change email/phone
- Update profile
- Adjust limits

**Role Management:**
- Assign/remove roles
- Set permissions
- Upgrade seller tier

**Restrictions:**
- Temporary suspension
- Permanent ban
- Trading restrictions
- Withdrawal hold

**Reasons for action:**
- Fraud suspicion
- Terms violation
- Multiple negative ratings
- Non-delivery
- Shill bidding

### Bulk Actions

- Export user list
- Send bulk email
- Apply role changes
- Export transaction history

---

## Auction Moderation

### Pending Approval

**Navigate to:** Admin → Aukcije → Pending

**Review checklist:**
- [ ] Photos are clear and appropriate
- [ ] Description is accurate
- [ ] Category is correct
- [ ] Price is reasonable
- [ ] No prohibited items
- [ ] No contact information in description
- [ ] Complies with terms of service

**Actions:**
- **Approve:** Auction goes live
- **Reject:** Send reason to seller
- **Request changes:** Specify required changes

### Reported Auctions

**Priority queue for:**
- Copyright infringement
- Counterfeit items
- Prohibited items
- Fraudulent listings
- Inappropriate content

**Response time:** Within 2 hours

### Featured Auctions

**Approve featured requests:**
- Check item quality
- Verify seller rating
- Confirm pricing is competitive
- Approve/reject within 24 hours

### Bulk Moderation

**Tools:**
- Approve all pending (batch)
- Cancel expired auctions
- Feature multiple auctions
- Export auction data

---

## Category Management

### Creating Categories

**Navigate to:** Admin → Kategorije

**Category structure:**
- Parent categories (top level)
- Subcategories (nested)
- Attributes per category

**Example structure:**
```
Elektronika
├── Mobilni telefoni
├── Laptopi
├── TV i audio
└── Kamere

Vozila
├── Automobili
├── Motocikli
└── Dijelovi
```

### Managing Categories

**Actions:**
- Add/edit/delete categories
- Reorder (drag & drop)
- Set icons
- Activate/deactivate
- Set commission rates per category

### Category Attributes

Define specific attributes per category:

**Example - Mobilni telefoni:**
- Brand (Apple, Samsung, etc.)
- Model
- Storage capacity
- Condition
- Color

---

## Dispute Resolution

### Dispute Queue

**Navigate to:** Admin → Sporovi

**Priority order:**
1. High-value disputes (>500 BAM)
2. Escalated disputes (>48 hours old)
3. Standard disputes (FIFO)

### Reviewing Disputes

**Information to review:**
- Order details
- Communication history
- Evidence from both parties
- Seller rating and history
- Buyer history

### Resolution Options

**For buyers:**
- Full refund
- Partial refund (% or amount)
- Return for refund

**For sellers:**
- Release payment
- Partial payment
- No action (buyer at fault)

### Communication

**With parties:**
- Request additional evidence
- Clarify issues
- Propose settlement
- Explain decision

**Template messages:**
- Evidence request
- Settlement proposal
- Decision notification
- Appeal information

### Escalation

**When to escalate:**
- High value (>1000 BAM)
- Repeat offenders
- Legal implications
- Media risk

**Escalation path:**
1. Support moderator
2. Senior moderator
3. Admin lead
4. Legal team

---

## KYC Verification

### KYC Levels

| Level | Requirements | Privileges |
|-------|-------------|------------|
| **0** | None | Browse only |
| **1** | Email verified | Bid, buy |
| **2** | Phone verified | Sell up to 5 items |
| **3** | Document verified | Unlimited selling |

### Review Queue

**Navigate to:** Admin → KYC Reviews

**Documents to verify:**
- ID card / Passport
- Proof of address
- Selfie with ID (if required)

### Verification Checklist

**ID Document:**
- [ ] Document is valid (not expired)
- [ ] Photo is clear
- [ ] Name matches account
- [ ] Document number visible
- [ ] No signs of tampering

**Proof of Address:**
- [ ] Recent (within 3 months)
- [ ] Name matches account
- [ ] Address visible
- [ ] Official document (utility bill, bank statement)

### Actions

**Approve:**
- Upgrade KYC level
- Notify user
- Enable selling privileges

**Reject:**
- Specify reason
- Allow resubmission
- Flag for manual review if suspicious

**Red Flags:**
- Blurry documents
- Mismatched names
- Expired documents
- Signs of editing
- Multiple accounts with same document

---

## Feature Flags

### Managing Features

**Navigate to:** Admin → Feature Flags

**Available flags:**
- `proxy_bidding` - Enable/disable proxy bidding
- `anti_sniping` - Enable/disable auction extension
- `escrow` - Enable/disable escrow protection
- `wallet` - Enable/disable wallet payments
- `stripe` - Enable/disable Stripe payments
- `monri` - Enable/disable Monri payments
- `corvuspay` - Enable/disable CorvusPay
- `euroexpress` - Enable/disable EuroExpress shipping

### Feature Rollout

**Staged rollout:**
1. Internal testing (0% users)
2. Beta users (5% users)
3. Gradual rollout (25%, 50%, 75%)
4. Full rollout (100%)

**Monitoring:**
- Error rates
- User feedback
- Performance metrics
- Support tickets

### Emergency Disable

**Quick disable for:**
- Critical bugs
- Security issues
- Performance problems

**Process:**
1. Toggle flag OFF
2. Notify users via status page
3. Fix issue
4. Re-enable when resolved

---

## Statistics & Reports

### Dashboard Reports

**Real-time metrics:**
- Active users (last 5 min, 1 hour, 24 hours)
- Auctions created/ended today
- Bids placed (count, value)
- Revenue (fees, commissions)
- Disputes (open, resolved)

### Custom Reports

**Navigate to:** Admin → Statistike

**Report types:**
- User growth (daily, weekly, monthly)
- Auction performance
- Revenue breakdown
- Category performance
- Geographic distribution
- Device usage

**Date ranges:**
- Today
- Last 7 days
- Last 30 days
- Custom range

### Export Options

**Formats:**
- CSV
- Excel (XLSX)
- PDF

**Scheduled reports:**
- Daily summary (email)
- Weekly report (email)
- Monthly report (PDF)

---

## System Settings

### Platform Configuration

**General settings:**
- Site name and logo
- Default currency
- Default language
- Timezone
- Maintenance mode

### Email Settings

**Configuration:**
- SMTP settings
- From address
- Email templates
- Notification settings

### Payment Settings

**Gateway configuration:**
- API keys (Stripe, Monri, CorvusPay)
- Webhook secrets
- Currency settings
- Fee structures

### Shipping Settings

**Courier configuration:**
- API keys
- Shipping zones
- Rate tables
- Tracking URLs

---

## Security Procedures

### Access Control

**MFA Requirements:**
- All admins must enable MFA
- Hardware key required for super admins
- Backup codes stored securely

**IP Whitelist:**
- Admin panel accessible from office IPs only
- VPN required for remote access
- Emergency access via approval

### Audit Logging

**All admin actions logged:**
- User ID
- Action performed
- Timestamp
- IP address
- User agent
- Affected records

**Log retention:** 2 years

### Sensitive Actions

**Require dual approval:**
- User data export
- Mass email (>1000 recipients)
- Financial adjustments (>500 BAM)
- Permanent bans
- Database changes

**Approval process:**
1. Admin initiates action
2. Second admin approves
3. Action executed
4. Both admins notified

---

## Emergency Procedures

### Security Incident

**Signs of incident:**
- Unusual admin activity
- Data breach indicators
- DDoS attack
- Payment fraud spike

**Response:**
1. **Immediate:** Enable maintenance mode
2. **Assess:** Determine scope
3. **Contain:** Disable affected features
4. **Notify:** Security team, management
5. **Document:** All actions taken
6. **Recover:** Restore from backup if needed
7. **Review:** Post-incident analysis

### System Outage

**Response:**
1. Check monitoring dashboards
2. Identify affected services
3. Restart failed services
4. Check database connectivity
5. Review error logs
6. Notify users via status page
7. Document outage details

### Data Breach

**Response:**
1. Disable affected accounts
2. Preserve evidence
3. Notify DPO (Data Protection Officer)
4. Assess data exposed
5. Notify affected users (within 72 hours)
6. Notify authorities (if required)
7. Engage security firm for investigation

### Contact List

| Role | Name | Phone | Email |
|------|------|-------|-------|
| On-Call Admin | [Name] | [Phone] | [Email] |
| Security Lead | [Name] | [Phone] | [Email] |
| DPO | [Name] | [Phone] | [Email] |
| Legal | [Name] | [Phone] | [Email] |
| CEO | [Name] | [Phone] | [Email] |

---

## Appendix

### Keyboard Shortcuts

| Shortcut | Action |
|----------|--------|
| `Ctrl + K` | Quick search |
| `Ctrl + /` | Open help |
| `G + D` | Go to dashboard |
| `G + U` | Go to users |
| `G + A` | Go to auctions |
| `G + S` | Go to disputes |

### Email Templates

Available in Admin → Settings → Email Templates:
- Welcome email
- KYC approval/rejection
- Dispute notifications
- Suspension/ban notices
- Payment confirmations

### API Access

**Admin API endpoints:**
- `/api/admin/users` - User management
- `/api/admin/auctions` - Auction moderation
- `/api/admin/disputes` - Dispute resolution
- `/api/admin/reports` - Generate reports

**Rate limits:** 100 requests/minute

---

**Last Updated:** March 2026  
**Version:** 1.0  
**Access:** Admin/Moderator roles only
