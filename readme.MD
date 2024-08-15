# Gravity Forms IP Submission Limit

**Plugin Name:** Gravity Forms IP Submission Limit  
**Description:** A WordPress plugin that limits the number of form submissions allowed from a single IP address within a 24-hour period using Gravity Forms.  
**Version:** 1.1  
**Author:** Angelo Marasa

## Description

The Gravity Forms IP Submission Limit plugin allows you to restrict the number of form submissions that can be made from a single IP address within a 24-hour period. This is useful for preventing spam and managing form submissions on your website.

### Features

-   **Submission Limit:** Set a maximum number of form submissions allowed per IP address within a 24-hour period.
-   **Blocked IP List:** View a list of IP addresses that have been blocked due to reaching the submission limit.
-   **Clear IP Submissions:** Easily clear the submission count for specific IP addresses from the admin panel.
-   **Customizable:** Adjust the submission limit directly from the Gravity Forms settings.

## Installation

1. Download the plugin files.
2. Upload the plugin folder to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Navigate to **Forms > Settings > IP Restrictions** to configure the plugin.

## Usage

1. Set the submission limit in the **IP Restrictions** tab under **Forms > Settings**.
2. Monitor blocked IPs under the same tab. Blocked IPs are those that have reached or exceeded the submission limit within the last 24 hours.
3. To clear the submission count for an IP, click the trash icon in the Actions column.

## Changelog

### 1.0

-   Initial release of the plugin.
-   Features: Submission limit, blocked IP list, and clear IP action.

## Frequently Asked Questions

### How do I set the submission limit?

You can set the submission limit under **Forms > Settings > IP Restrictions**. The limit defines how many times a single IP address can submit a form within 24 hours.

### What happens when an IP reaches the submission limit?

When an IP reaches the submission limit, the form will no longer be displayed to the user. Instead, a message will inform them that they have reached the submission limit.

### Can I unblock an IP manually?

Yes, you can clear the submission count for any IP by clicking the trash icon in the Actions column under **Forms > Settings > IP Restrictions**.

## License

This plugin is licensed under the [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html).

---

_Developed by Angelo Marasa._
