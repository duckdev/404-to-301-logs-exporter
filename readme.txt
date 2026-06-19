=== 404 to 301 - Logs Exporter ===
Contributors: joelcj91, duckdev
Tags: 404, 404 logs, export, csv export, 404 to 301
Donate link: https://www.paypal.me/JoelCJ
Requires at least: 6.4
Tested up to: 7.0
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

One-click CSV export for the 404 to 301 plugin's error log — filter-aware, streamed, and ready for Excel, Sheets or Numbers.

== Description ==

**404 to 301 – Logs Exporter** is the official CSV export add-on for the [404 to 301](https://wordpress.org/plugins/404-to-301/) plugin. It adds an **Export** button to the 404 Logs page so you can download every logged 404 error — or just the rows matching your current filters — as a clean, spreadsheet-ready CSV file.

Use it to share broken-link reports with your SEO team, back up your 404 history before pruning, hand off audits to clients, or feed your logs into any tool that reads CSV — Google Sheets, Excel, Numbers, Looker Studio, or your own scripts.

= Why use Logs Exporter? =

* **One-click CSV export** straight from the 404 Logs page — no SQL, no phpMyAdmin, no copy-paste.
* **Filter-aware** — the export respects whatever date range, status filter or search query you have applied, so you only get the rows you actually want.
* **Streamed download** — large log tables are streamed row-by-row, so exports work even on shared hosts without exhausting memory.
* **Spreadsheet-ready columns** — requested URL, referrer, IP, user agent, hit count, status and timestamps, all in standard CSV format.
* **Date-stamped filenames** — repeated exports don't clobber each other in your downloads folder.
* **GDPR-aware** — IP-masking settings from the parent plugin are respected in the export.

= Built for the 404 to 301 workflow =

This add-on is a light-weight companion to the parent plugin. It hooks into the existing 404 Logs screen and re-uses the same filters, capabilities and database tables, so there's nothing new to learn.

* No new settings page — the Export button appears right where you read your logs.
* Requires the free [404 to 301](https://wordpress.org/plugins/404-to-301/) plugin (4.0 or newer).
* Same coding standards, security model and multisite behaviour as the parent plugin.

= Related add-ons =

Browse the full add-ons catalogue at [https://duckdev.com/addons/404-to-301/](https://duckdev.com/addons/404-to-301/):

* **Redirects Importer** — Bulk import custom redirects from CSV or other redirect plugins.
* **Logs Cleaner** — Auto-prune the 404 log table by age, row count or schedule.
* **Email Reports** — Periodic email digests of your 404 activity with an attached CSV.
* **Telegram Alerts** — Real-time Telegram notifications for 404 errors and redirects.

== Source code & contributions ==

* **GitHub repository:** [https://github.com/duckdev/404-to-301-logs-exporter](https://github.com/duckdev/404-to-301-logs-exporter)
* **Documentation:** [https://docs.duckdev.com/404-to-301/addons/logs-exporter/](https://docs.duckdev.com/404-to-301/addons/logs-exporter/)
* **Support forum:** [https://wordpress.org/support/plugin/404-to-301-logs-exporter/](https://wordpress.org/support/plugin/404-to-301-logs-exporter/)

Pull requests and bug reports are welcome on GitHub.

== Installation ==

1. Make sure the free [404 to 301](https://wordpress.org/plugins/404-to-301/) plugin (version 4.0 or newer) is installed and activated.
2. Install **404 to 301 – Logs Exporter** from the WordPress.org plugin directory, or upload the plugin folder to `/wp-content/plugins/`.
3. Activate the add-on from the **Plugins** screen.
4. Open **404 to 301 → Logs**. Apply any filters you want, then click the **Export** button to download a CSV of the matching rows.

== Frequently Asked Questions ==

= Do I need the 404 to 301 plugin installed? =

Yes. This is an add-on for the free [404 to 301](https://wordpress.org/plugins/404-to-301/) plugin (4.0 or newer). Without it, there are no logs to export.

= What columns are included in the CSV? =

The export includes the requested URL, referrer, IP address (masked if you enabled IP masking in the parent plugin), user agent, hit count, lifecycle status, first-hit and last-hit timestamps.

= Does the export respect my current filters? =

Yes. Whatever date range, status filter or search query you have applied on the Logs page is applied to the export. Clear the filters to export everything.

= Will it work on large log tables? =

Yes. Rows are streamed to the browser as the CSV is written, so memory usage stays flat even on log tables with hundreds of thousands of rows.

= Is it GDPR-friendly? =

Yes. IP masking from the parent plugin's settings is respected, so masked IPs stay masked in the exported file.

= Does it support multisite? =

Yes. Each site in the network exports its own 404 logs.

= Where can I get help? =

Read the [documentation](https://docs.duckdev.com/404-to-301/addons/logs-exporter/) or post on the [support forum](https://wordpress.org/support/plugin/404-to-301-logs-exporter/).

== Screenshots ==

1. The Export button on the 404 Logs page.
2. A sample exported CSV file opened in a spreadsheet.

== Changelog ==

= 1.0.0 =
* New: Initial release. One-click, filter-aware CSV export of the 404 to 301 logs table.

== Upgrade Notice ==

= 1.0.0 =
First public release of the Logs Exporter add-on for 404 to 301.
