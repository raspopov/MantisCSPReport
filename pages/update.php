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

$t_enable = gpc_get_int( 'enable', ON );
if( isset( $t_enable ) && $t_enable === OFF ) {
	plugin_config_set( 'enable', OFF );
} else {
	plugin_config_delete( 'enable' );
}

$t_ignore = array_filter( preg_split( '/[\s,]+/', gpc_get_string( 'ignore' ) ),
	function( $p_key ) { return strlen( $p_key ); } );
if( $t_ignore ) {
	plugin_config_set( 'ignore', $t_ignore );
} else {
	plugin_config_delete( 'ignore' );
}

print_header_redirect( plugin_page( 'config.php', true ) );
