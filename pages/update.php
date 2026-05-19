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

form_security_validate( 'plugin_MantisCSPReport_update' );

access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );

form_security_purge( 'plugin_MantisCSPReport_update' );

$f_enable = gpc_get_int( 'enable', MantisCSPReportPlugin::DEFAULT_ENABLE );
$f_ignore = array_filter( preg_split( '/[\s,]+/', gpc_get_string( 'ignore', MantisCSPReportPlugin::DEFAULT_IGNORE ) ),
	function( $p_key ) { return strlen( $p_key ); } );
$f_aging = gpc_get_int( 'aging', MantisCSPReportPlugin::DEFAULT_AGING );

if( $f_enable != MantisCSPReportPlugin::DEFAULT_ENABLE ) {
	plugin_config_set( 'enable', $f_enable );
} else {
	plugin_config_delete( 'enable' );
}

if( $f_ignore && $f_ignore != MantisCSPReportPlugin::DEFAULT_IGNORE ) {
	plugin_config_set( 'ignore', $f_ignore );
} else {
	plugin_config_delete( 'ignore' );
}

if( $f_aging && $f_aging != MantisCSPReportPlugin::DEFAULT_AGING ) {
	plugin_config_set( 'aging', $f_aging );
} else {
	plugin_config_delete( 'aging' );
}

print_header_redirect( plugin_page( 'config.php', true ) );
