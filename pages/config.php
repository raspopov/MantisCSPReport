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

access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );

$t_enable = plugin_config_get( 'enable', MantisCSPReportPlugin::DEFAULT_ENABLE );
$t_ignore = plugin_config_get( 'ignore', MantisCSPReportPlugin::DEFAULT_IGNORE );
$t_aging = plugin_config_get( 'aging', MantisCSPReportPlugin::DEFAULT_AGING );

layout_page_header( plugin_lang_get( 'config' ) );

layout_page_begin( 'manage_overview_page.php' );

print_manage_menu( 'manage_plugin_page.php' );
?>
<div class="col-md-12 col-xs-12">
	<div class="space-10"></div>
	<div class="form-container">
		<form action="<?php echo plugin_page( 'update.php' ) ?>" method="post">
			<?php echo form_security_field( 'plugin_MantisCSPReport_update' ) ?>
			<div class="widget-box widget-color-blue2">

				<div class="widget-header widget-header-small">
					<h4 class="widget-title lighter">
						<?php print_icon( 'fa-sliders', 'ace-icon' ) ?>
						<?php echo plugin_lang_get( 'config' ) ?>
					</h4>
				</div>

				<div class="widget-body">
					<div class="widget-main no-padding">
						<div class="table-responsive">
							<table class="table table-bordered table-condensed table-striped">
								<tbody>
									<tr>
										<th class="category width-40"><?php echo plugin_lang_get( 'enable_title' ) ?><br/>
											<span class="small"><?php echo plugin_lang_get( 'enable_details' ) ?></span>
										</th>
										<td>
											<label class="width-40">
												<input type="radio" name="enable" value="1" class="ace" <?php check_checked( $t_enable, ON ) ?>>
												<span class="lbl padding-6"><?php echo lang_get( 'yes' ) ?></span>
											</label>
											<label class="width-40">
												<input type="radio" name="enable" value="0" class="ace" <?php check_checked( $t_enable, OFF ) ?>>
												<span class="lbl padding-6"><?php echo lang_get( 'no' ) ?></span>
											</label>
										</td>
									</tr>

									<tr>
										<th class="category width-40"><?php echo plugin_lang_get( 'ignore_title' ) ?><br/>
											<span class="small"><?php echo plugin_lang_get( 'ignore_details' ) ?></span>
										</th>
										<td>
											<textarea class="form-control" name="ignore"><?php echo string_html_specialchars( implode( ', ', $t_ignore ) ) ?></textarea>
										</td>
									</tr>

									<tr>
										<th class="category width-40"><?php echo plugin_lang_get( 'aging_title' ) ?><br/>
											<span class="small"><?php echo plugin_lang_get( 'aging_details' ) ?></span>
										</th>
										<td>
											<input class="input-sm" name="aging" size="8" maxlength="8" value="<?php echo $t_aging ?>" type="text">
										</td>
									</tr>

								</tbody>
							</table>
							<div class="widget-toolbox padding-8 clearfix">
								<input class="btn btn-primary btn-sm btn-white btn-round" value="<?php echo lang_get( 'change_configuration' ) ?>" type="submit">
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
