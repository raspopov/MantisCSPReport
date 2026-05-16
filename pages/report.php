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

if( !plugin_config_get( 'enable', ON ) ) {
	http_response_code( HTTP_STATUS_UNAVAILABLE );
	exit;
}

http_response_code( HTTP_STATUS_NO_CONTENT );

# POST
$t_post = file_get_contents( 'php://input' );
if( $t_post ) {
	# JSON
	$t_data = json_decode( $t_post, true );
	if( $t_data ) {
		/* https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Content-Security-Policy/report-uri:
		{
			"csp-report": {
				"document-uri": "http://localhost/my_view_page.php",
				"referrer": "http://localhost/main_page.php",
				"violated-directive": "style-src-elem",
				"effective-directive": "style-src-elem",
				"original-policy": "report-uri http://localhost/plugin.php?page=MantisCSPReport/report.php; default-src 'self'",
				"disposition": "enforce",
				"blocked-uri": "inline",
				"line-number": 16,
				"source-file": "http://localhost/my_view_page.php",
				"status-code": 200,
				"script-sample": ""
			}
		}
		*/
		#plugin_log_event( print_r( $t_data, true ) );

		$t_source = $t_data['csp-report']['source-file'] ?? '';
		$t_line = $t_data['csp-report']['line-number'] ?? 0;
		$t_directive = $t_data['csp-report']['effective-directive'] ?? null;
		$t_document = $t_data['csp-report']['document-uri'] ?? null;
		$t_blocked = $t_data['csp-report']['blocked-uri'] ?? null;
	}
}

if(    !is_null( $t_directive )
	&& !is_null( $t_document )
	&& !is_null( $t_blocked ) ) {

	# Ignore self
	if( str_contains( $t_document, '/plugin.php?page=MantisCSPReport' ) ) {
		exit;
	}

	foreach( plugin_config_get( 'ignore' ) as $t_prefix ) {
		if(    str_starts_with( $t_source, $t_prefix )
			|| $t_directive === $t_prefix
			|| str_starts_with( $t_blocked, $t_prefix )
			|| str_starts_with( $t_document, $t_prefix ) ) {
			exit;
		}
	}

	db_param_push();
	db_query( 'INSERT INTO ' . plugin_table( 'reports' )
		. ' ( date, source, line, directive, document, blocked ) VALUES ( '
		. db_param() . ', ' . db_param() . ', ' . db_param() . ', ' . db_param() . ', ' . db_param() . ', ' . db_param() . ' )',
		[ db_now(), $t_source, $t_line, $t_directive, $t_document, $t_blocked ] );
}
