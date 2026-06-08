/**
 * 404 to 301 — Logs Exporter: entry point.
 *
 * Loaded by the addon on the parent plugin's Logs screen. Registers
 * a single `addFilter` callback on `d404.logs.toolbar` that mounts
 * the Export-CSV button above the DataViews table.
 */
import { addFilter } from '@wordpress/hooks'

import ExportButton from './export-button'

addFilter(
	'd404.logs.toolbar',
	'logs-exporter/export-button',
	( existing, ctx ) => (
		<>
			{ existing }
			<ExportButton ctx={ ctx } key="logs-exporter-export-button" />
		</>
	),
)
