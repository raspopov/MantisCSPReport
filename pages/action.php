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

form_security_validate( 'plugin_MantisCSPReport_action' );

access_ensure_global_level( config_get( 'manage_site_threshold' ) );

form_security_purge( 'plugin_MantisCSPReport_action' );

$f_clear = gpc_get_string( 'clear', '' );
if( $f_clear === 'all' ) {
	db_query( 'DELETE FROM ' . plugin_table( 'reports' ) );
}

print_header_redirect( plugin_page( 'view.php', true ) );
