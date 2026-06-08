<?php
/**
 * Addon bootstrap.
 *
 * Wires the Assets and Api subsystems together once the parent
 * plugin's Core has finished booting. Also exposes the
 * `register_addon` callback used by the parent's Freemius layer.
 *
 * @package DuckDev\FourNotFour\LogsExporter
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\LogsExporter;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Class Plugin
 *
 * @since 1.0.0
 */
final class Plugin {

	/**
	 * Guard against double-booting.
	 *
	 * `404_to_301_init` only fires once in a normal request, but the
	 * parent does emit it again from CLI bootstraps and integration
	 * tests, and double-registering hooks here would attach the
	 * admin-post handler twice.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	private static $booted = false;

	/**
	 * Hook the addon into the parent's Addons / Freemius registry.
	 *
	 * Without this entry the addon is effectively invisible even when
	 * active on disk — the parent gates the Addons UI on this filter.
	 *
	 * @since 1.0.0
	 *
	 * @param array $addons Addons keyed by Freemius project id.
	 *
	 * @return array
	 */
	public static function register_addon( array $addons ): array {
		$addons[ D404_LOGS_EXPORTER_FREEMIUS_ID ] = array(
			'slug'       => 'logs-exporter',
			'main_file'  => plugin_basename( D404_LOGS_EXPORTER_FILE ),
			'public_key' => D404_LOGS_EXPORTER_FREEMIUS_PK,
			'is_premium' => true,
			'has_addons' => false,
		);

		return $addons;
	}

	/**
	 * Wire each subsystem's hooks.
	 *
	 * Each class owns its own `register()` so the boot sequence here
	 * stays a flat, readable list of subsystems.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}

		Assets::instance()->register();
		Api::instance()->register();

		self::$booted = true;
	}
}
