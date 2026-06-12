/**
 * <ExportButton> — toolbar button that triggers the CSV download.
 *
 * The actual streaming happens server-side; this component just builds
 * the `admin-post.php` URL with the current view's filters/search and
 * navigates the browser to it. Letting the browser handle the request
 * (rather than `fetch` + `Blob`) means we don't have to materialise
 * the whole CSV in JS memory, and the file is saved through the
 * normal download chrome.
 */
import { __ } from '@wordpress/i18n'
import { Button } from '@wordpress/components'
import { addQueryArgs } from '@wordpress/url'

// Localised by `wp_localize_script` in includes/class-assets.php. Falls
// back to an empty shape so a misconfigured page doesn't throw before
// the user sees the (disabled) button.
const config = window.d404LogsExporter || {
	endpoint: '',
	action: '',
	nonce: '',
}

/**
 * Translate the DataViews `view` into the same query-arg names the
 * parent's `/logs` REST endpoint accepts. The server-side exporter
 * re-reads them from `$_REQUEST` so the CSV honours the filters the
 * user is currently looking at.
 */
const viewToParams = ( view ) => {
	const params = {}

	if ( ! view ) {
		return params
	}

	if ( view.search ) {
		params.search = view.search
	}

	if ( view.sort?.field ) {
		params.orderby = view.sort.field
		params.order = view.sort.direction === 'asc' ? 'asc' : 'desc'
	}

	if ( Array.isArray( view.filters ) ) {
		view.filters.forEach( ( filter ) => {
			if (
				filter &&
				filter.field &&
				filter.value !== undefined &&
				filter.value !== ''
			) {
				params[ filter.field ] = filter.value
			}
		} )
	}

	return params
}

const ExportButton = ( { ctx } ) => {
	const view = ctx?.view

	// Nothing to export when the current (filtered) view holds no rows,
	// or while the table is still fetching — `total` is the row count the
	// parent reports for the view the CSV would mirror. Guard `total`
	// being absent (older parent) so the button stays usable there.
	const isEmpty = ctx?.total === 0
	const isLoading = !! ctx?.isLoading

	const onClick = () => {
		if ( ! config.endpoint || ! config.action ) {
			return
		}

		const href = addQueryArgs( config.endpoint, {
			action: config.action,
			_wpnonce: config.nonce,
			...viewToParams( view ),
		} )

		// Same-tab navigation; the response carries a Content-Disposition
		// header so the browser saves it rather than rendering it.
		window.location.href = href
	}

	return (
		<div className="d404-logs-exporter__toolbar">
			<Button
				__next40pxDefaultSize
				variant="secondary"
				icon="download"
				onClick={ onClick }
				disabled={ ! config.endpoint || isEmpty || isLoading }
			>
				{ __( 'Export CSV', '404-to-301-logs-exporter' ) }
			</Button>
		</div>
	)
}

export default ExportButton
