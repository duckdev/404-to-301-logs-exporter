<?php
/**
 * Download endpoint.
 *
 * Wires an `admin-post.php` handler that streams the CSV. We use
 * admin-post rather than a REST route because the WP REST stack
 * insists on JSON-encoding the response body, and forcing it to emit
 * raw CSV requires either an `rest_pre_serve_request` short-circuit
 * or a custom output buffer dance — admin-post is the boring, well-trodden
 * path for "download a file from a logged-in admin click".
 *
 * @package DuckDev\FourNotFour\LogsExporter
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\LogsExporter;

use DuckDev\FourNotFour\Utils\Permission;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Class Api
 *
 * @since 1.0.0
 */
final class Api {

	/**
	 * Singleton instance.
	 *
	 * @since 1.0.0
	 * @var self|null
	 */
	private static $instance = null;

	/**
	 * Retrieve the shared instance.
	 *
	 * @since 1.0.0
	 *
	 * @return self
	 */
	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register the WordPress hooks owned by this class.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'admin_post_' . D404_LOGS_EXPORTER_ACTION, array( $this, 'download' ) );
	}

	/**
	 * Stream the CSV download.
	 *
	 * Validates the nonce + capability, emits the file headers, hands
	 * off to {@see Exporter::stream()} for the row loop, and exits
	 * before WordPress can write anything else to the response body.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function download(): void {
		// Capability check first — a 403 for an unauthenticated request
		// is cheaper than parsing the nonce and gives the right error
		// surface to admin-post's default handlers.
		if ( ! current_user_can( $this->required_cap() ) ) {
			wp_die(
				esc_html__( 'You do not have permission to export logs.', '404-to-301-logs-exporter' ),
				esc_html__( 'Forbidden', '404-to-301-logs-exporter' ),
				array( 'response' => 403 )
			);
		}

		// Nonce is generated in Assets::enqueue() and passed in via the
		// localised `d404LogsExporter.nonce` global; both the GET and
		// POST forms include it as `_wpnonce`.
		check_admin_referer( D404_LOGS_EXPORTER_ACTION );

		$filters = $this->collect_filters();

		// Suggest a date-stamped filename so repeated exports don't
		// clobber each other in the user's downloads folder.
		$filename = sprintf( '404-to-301-logs-%s.csv', gmdate( 'Y-m-d-His' ) );

		// Discard any buffered output (eg. UTF-8 BOMs from translation
		// files or stray whitespace from a misbehaving plugin) before
		// we start writing the CSV.
		while ( ob_get_level() > 0 ) {
			ob_end_clean();
		}

		// Large log tables can take a while — opt out of the request
		// time limit and keep streaming even if the user closes the
		// tab (otherwise the partial CSV they already received gets
		// truncated when the script is killed).
		if ( function_exists( 'set_time_limit' ) ) {
			@set_time_limit( 0 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		}
		ignore_user_abort( true );

		nocache_headers();
		header( 'Content-Type: text/csv; charset=UTF-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		header( 'X-Content-Type-Options: nosniff' );

		( new Exporter() )->stream( $filters );

		exit;
	}

	/**
	 * Resolve the capability needed to trigger an export.
	 *
	 * Defers to the parent plugin's `Permission::get_cap()` when
	 * present so the addon honours any custom capability filter the
	 * site sets there. Falls back to `manage_options` so the handler
	 * still gates correctly if the parent isn't loaded.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function required_cap(): string {
		if ( class_exists( Permission::class ) ) {
			return Permission::get_cap();
		}

		return 'manage_options';
	}

	/**
	 * Pull the optional filter values off the request.
	 *
	 * Each value goes through a tight type-cast / sanitiser before it
	 * reaches the exporter — the model layer will re-validate, but
	 * normalising at the boundary keeps the query args predictable.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function collect_filters(): array {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Verified in download().
		$status = isset( $_REQUEST['status'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['status'] ) ) : '';

		$filters = array(
			'search'    => isset( $_REQUEST['search'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['search'] ) ) : '',
			'orderby'   => isset( $_REQUEST['orderby'] ) ? sanitize_key( wp_unslash( (string) $_REQUEST['orderby'] ) ) : 'updated_at',
			'order'     => isset( $_REQUEST['order'] ) ? strtoupper( sanitize_key( wp_unslash( (string) $_REQUEST['order'] ) ) ) : 'DESC',
			'date_from' => isset( $_REQUEST['date_from'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['date_from'] ) ) : '',
			'date_to'   => isset( $_REQUEST['date_to'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['date_to'] ) ) : '',
		);
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		if ( '' !== $status && is_numeric( $status ) ) {
			$filters['status'] = (int) $status;
		}

		if ( ! in_array( $filters['order'], array( 'ASC', 'DESC' ), true ) ) {
			$filters['order'] = 'DESC';
		}

		return $filters;
	}
}
