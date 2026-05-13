# KISS Upcoming Subscription Renewals

A simple WordPress/WooCommerce admin page that displays upcoming subscription renewals sorted by next payment date.

## Description

KISS Upcoming Subscription Renewals adds an **Upcoming Renewals** submenu page under the WooCommerce admin menu. It lists all active subscriptions with a future renewal date, sorted chronologically, and shows key details such as the customer name, renewal date, time remaining, order total, payment gateway, and billing cycle.

The goal of this plugin is to keep things simple (Keep It Simple, Stupid) — no settings, no bloat, just the data you need at a glance.

## Requirements

- WordPress 5.0 or later
- [WooCommerce](https://wordpress.org/plugins/woocommerce/) (latest stable recommended)
- [WooCommerce Subscriptions](https://woocommerce.com/products/woocommerce-subscriptions/)

## Installation

1. Upload the `kiss-woo-upcoming-renewals` folder to the `/wp-content/plugins/` directory, or install it through the WordPress Plugins screen.
2. Activate the plugin through the **Plugins** screen in WordPress.
3. Navigate to **WooCommerce → Upcoming Renewals** in the WordPress admin menu to view upcoming renewals.

## Usage

Once activated, visit **WooCommerce → Upcoming Renewals** in the WordPress admin. The page displays:

| Column | Description |
|---|---|
| Sub ID | Subscription ID, linked to the edit screen |
| Next Renewal | Renewal date/time in your site's local timezone |
| Time Until | Countdown (days/hours/minutes) until the renewal |
| Customer | Billing name and email address |
| Total | Subscription renewal total |
| Gateway | Payment method title |
| Billing Cycle | Renewal interval and period (e.g., "Every 1 month") |

Use the **Show: 10 | 25 | 50** links at the top of the page to control how many renewals are displayed at once.

## License

This plugin is licensed under the [GNU General Public License v2.0 or later](LICENSE.md).

## Disclaimer

**Use at your own risk.** This plugin is provided as-is, without any warranty of any kind, express or implied. The authors and contributors are not responsible for any data loss, site breakage, or other issues that may arise from using this plugin. Always test on a staging environment before deploying to production.
