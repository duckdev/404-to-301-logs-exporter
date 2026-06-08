<?php
/**
 * Plugin Name:       404 to 301 - Logs Exporter
 * Description:       Export the 404 error log table as a downloadable CSV file from the Logs page.
 * Version:           1.0.0
 * Author:            Joel James
 * Author URI:        https://duckdev.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       404-to-301-logs-exporter
 * Requires Plugins:  404-to-301
 * Requires PHP:      7.4
 * Requires at least: 6.4
 *
 * Light-weight addon for the {@see https://wordpress.org/plugins/404-to-301/ 404 to 301}
 * plugin: adds an Export button to the Logs page and streams the
 * matching rows out as a CSV file.
 *
 * @package DuckDev\FourNotFour\LogsExporter
 */

declare( strict_types = 1 );

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

/*
 * Plugin constants.
 *
 * Defined once here so every class can read the addon's version, paths
 * and Freemius ids without re-deriving them.
 */

// Plugin version (kept in sync with the `Version:` header above).
const D404_LOGS_EXPORTER_VERSION = '1.0.0';

// Absolute path to this bootstrap file.
define( 'D404_LOGS_EXPORTER_FILE', __FILE__ );

// Absolute plugin directory path (with a trailing slash).
define( 'D404_LOGS_EXPORTER_DIR', plugin_dir_path( __FILE__ ) );

// Plugin directory URL (with a trailing slash).
define( 'D404_LOGS_EXPORTER_URL', plugin_dir_url( __FILE__ ) );

/*
 * Freemius project id + public key for this addon.
 *
 * Kept as constants so a clobbered options table can't shift a paying
 * customer onto the wrong project.
 */
const D404_LOGS_EXPORTER_FREEMIUS_ID = 30709;
const D404_LOGS_EXPORTER_FREEMIUS_PK = 'pk_0c2337f30dc81f2e4338cfbf2cc7b';

/*
 * `admin-post.php` action name used by the download endpoint. Browsers
 * navigate to `admin-post.php?action=<this>` to trigger the CSV stream
 * — handler lives in {@see \DuckDev\FourNotFour\LogsExporter\Api}.
 */
const D404_LOGS_EXPORTER_ACTION = 'd404_logs_export';

/*
 * Class loader.
 *
 * The addon is small enough that wiring up Composer would be heavier
 * than the code it autoloads — a hand-written loader for our handful
 * of `class-*.php` files keeps the addon dependency-free while still
 * following the parent plugin's file-naming convention.
 */
require_once D404_LOGS_EXPORTER_DIR . 'includes/class-plugin.php';
require_once D404_LOGS_EXPORTER_DIR . 'includes/class-assets.php';
require_once D404_LOGS_EXPORTER_DIR . 'includes/class-exporter.php';
require_once D404_LOGS_EXPORTER_DIR . 'includes/class-api.php';

/*
 * Register with the parent on `plugins_loaded` so the Addons page +
 * Freemius see us regardless of load order. The rest of the addon
 * boots on `404_to_301_init`, once Core has finished wiring up.
 */
add_filter(
	'404_to_301_register_addon',
	array( \DuckDev\FourNotFour\LogsExporter\Plugin::class, 'register_addon' )
);

add_action(
	'404_to_301_init',
	array( \DuckDev\FourNotFour\LogsExporter\Plugin::class, 'boot' )
);
