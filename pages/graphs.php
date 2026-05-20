<?php
/**
 * MantisCSPReport - A MantisBT plugin for Content Security Policy (CSP) reporting.
 *
 * MantisCSPReport is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * MantisCSPReport is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with MantisCSPReport.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Copyright (C) 2026 Nikolay Raspopov <raspopov@cherubicsoft.com>
 */

auth_reauthenticate();

access_ensure_global_level( config_get( 'manage_site_threshold' ) );

layout_page_header( plugin_lang_get( 'graphs' ) );

layout_page_begin( 'manage_overview_page.php' );

print_manage_menu( 'graphs.php' );

?>
<div class="col-md-12 col-xs-12 no-padding">
<?php
if( function_exists( 'graph_bar' ) ) {
	$t_table = plugin_table( 'reports' );
	foreach( [ 'directive', 'source', 'document', 'blocked' ] as $t_graph ) {
?>
	<div class="col-md-6 col-xs-12">
		<div class="space-10"></div>
		<div class="widget-box widget-color-blue2">
			<div class="widget-header widget-header-small">
				<h4 class="widget-title lighter">
					<?php print_icon( 'fa-bar-chart-o', 'ace-icon' ); ?>
					<?php echo plugin_lang_get( $t_graph ) ?>
				</h4>
			</div>
<?php
		$t_result = db_query( "SELECT $t_graph, COUNT(*) count FROM $t_table GROUP BY $t_graph ORDER BY count DESC LIMIT 5" );
		$t_bars = [];
		while( $t_row = db_fetch_array( $t_result ) ) {
			$t_bars[MantisCSPReportPlugin::limit_text( MantisCSPReportPlugin::strip_path( $t_row[$t_graph] ) )] = $t_row['count'];
		}
		graph_bar( $t_bars, 3, true );
?>
		</div>
	</div>
<?php
	}
}
?>
</div>
<?php
layout_page_end();
