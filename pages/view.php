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

$f_page_number = gpc_get_int( 'page_number', 1 );

$t_date_format = config_get( 'normal_date_format' );

$t_total_count = db_result( db_query( 'SELECT count(*) FROM ' . plugin_table( 'reports' ) ) );
$t_per_page = 10;
$t_page_count = ceil( $t_total_count / $t_per_page );
if( $t_page_count < 1 ) {
	$t_page_count = 1;
}
if( $f_page_number > $t_page_count ) {
	$f_page_number = $t_page_count;
}
if( $f_page_number < 1 ) {
	$f_page_number = 1;
}
$t_offset = ( $f_page_number - 1 ) * $t_per_page;

layout_page_header( plugin_lang_get( 'title' ) );

layout_page_begin( 'manage_overview_page.php' );

print_manage_menu( 'view.php' );
?>
<div class="col-md-12 col-xs-12">
	<div class="space-10"></div>
	<div class="form-container">
		<form action="<?php echo plugin_page( 'action.php' ) ?>" method="post">
			<?php echo form_security_field( 'plugin_MantisCSPReport_action' ) ?>
			<div class="widget-box widget-color-blue2">
			
				<div class="widget-header widget-header-small">
					<h4 class="widget-title lighter">
						<?php print_icon( 'fa-list', 'ace-icon' ) ?>
						<?php echo string_html_specialchars( plugin_lang_get( 'title' ) ) ?>
						<span class="badge"><?php echo $t_total_count ?></span>
					</h4>
				</div>

				<div class="widget-body">
					<div class="widget-main no-padding">
						<div class="table-responsive">
							<table class="table table-bordered table-condensed table-striped">
								<tbody>
									<tr>
										<th class="category"></th>
										<th class="category"><?php echo lang_get( 'date_submitted' ) ?></th>
										<th class="category"><?php echo plugin_lang_get( 'directive' ) ?></th>
										<th class="category"><?php echo plugin_lang_get( 'source' ) ?></th>
										<th class="category center"><?php echo plugin_lang_get( 'line' ) ?></th>
										<th class="category"><?php echo plugin_lang_get( 'document' ) ?></th>
										<th class="category"><?php echo plugin_lang_get( 'blocked' ) ?></th>
									</tr>
<?php
										$t_result = db_query( 'SELECT * FROM ' . plugin_table( 'reports' ) . ' ORDER BY date ASC', [], $t_per_page, $t_offset );
										$t_index = $t_offset;
										while( $t_row = db_fetch_array( $t_result ) ) {
?>
									<tr>
										<td><?php echo ++$t_index ?></td>
										<td><?php echo date( $t_date_format, $t_row['date'] ) ?></td>
										<td><?php echo string_attribute( $t_row['directive'] ) ?></td>
										<td><?php echo string_display_line_links( $t_row['source'] ) ?></td>
										<td class="center"><?php echo string_attribute( $t_row['line'] ) ?></td>
										<td><?php echo string_display_line_links( $t_row['document'] ) ?></td>
										<td><?php echo string_display_line_links( $t_row['blocked'] ) ?></td>
									</tr>
<?php
										}
?>
								</tbody>
							</table>
						</div>

						<div class="widget-toolbox padding-8 clearfix">
							<div class="pull-left">
								<input class="btn btn-primary btn-sm btn-white btn-round" value="<?php echo lang_get( 'proceed' ) ?>" type="submit">
								<label class="inline">
									<input class="ace" type="checkbox" name="clear" value="all">
									<span class="lbl padding-6 padding-left-8 red"><?php echo lang_get( 'remove_all_link' ) ?></span>
								</label>
							</div>
							<div class="btn-toolbar pull-right">
								<?php print_page_links( plugin_page( 'view.php' ), 1, $t_page_count, $f_page_number ) ?>
							</div>
						</div>
						
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<?php
layout_page_end();
