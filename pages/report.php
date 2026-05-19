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

if( ON != plugin_config_get( 'enable', MantisCSPReportPlugin::DEFAULT_ENABLE ) ) {
	http_response_code( HTTP_STATUS_UNAVAILABLE );
	exit;
}

http_response_code( HTTP_STATUS_NO_CONTENT );

/**
 * Add a new report to the database
 * @return void
 */
function report( int $p_now, $p_source, $p_line, $p_directive, $p_document, $p_blocked ) {
	if(    !is_null( $p_directive )
		&& !is_null( $p_document )
		&& !is_null( $p_blocked ) ) {

		# Ignore self
		if( str_contains( $p_document, plugin_page( '' ) ) ) {
			return;
		}

		$t_ignore = plugin_config_get( 'ignore', MantisCSPReportPlugin::DEFAULT_IGNORE );
		foreach( $t_ignore as $t_prefix ) {
			if( $p_directive === $t_prefix
				|| str_contains( $p_source, $t_prefix )
				|| str_contains( $p_blocked, $t_prefix )
				|| str_contains( $p_document, $t_prefix ) ) {
				return;
			}
		}
		
		$t_table = plugin_table( 'reports' );
		$t_aging = plugin_config_get( 'aging', MantisCSPReportPlugin::DEFAULT_AGING );

		db_param_push();
		db_query( 'DELETE FROM ' . $t_table . ' WHERE date < ' . db_param(), [ $p_now - $t_aging ] );

		db_param_push();
		db_query( 'INSERT INTO ' . $t_table . ' ( date, source, line, directive, document, blocked ) VALUES ( '
			. db_param() . ', ' . db_param() . ', ' . db_param() . ', ' . db_param() . ', ' . db_param() . ', ' . db_param() . ' )',
			[ $p_now, $p_source, $p_line, $p_directive, $p_document, $p_blocked ] );
	}
}

# POST
$t_post = @file_get_contents( 'php://input' );
if( $t_post ) {
	# JSON
	$t_data = @json_decode( $t_post, true );
	if( $t_data ) {
		#plugin_log_event( json_encode( $t_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );

		$t_now = db_now();
		if( isset( $t_data['csp-report'] ) ) {
			/* https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Content-Security-Policy/report-uri:
			{
				"csp-report": {
					"document-uri": "https://localhost/my_view_page.php",
					"referrer": "https://localhost/main_page.php",
					"violated-directive": "style-src-elem",
					"effective-directive": "style-src-elem",
					"original-policy": "report-uri https://localhost/plugin.php?page=MantisCSPReport/report.php; default-src 'self'",
					"disposition": "enforce",
					"blocked-uri": "inline",
					"line-number": 16,
					"source-file": "https://localhost/my_view_page.php",
					"status-code": 200,
					"script-sample": ""
				}
			} */
			report( $t_now,
				$t_data['csp-report']['source-file'] ?? '',
				$t_data['csp-report']['line-number'] ?? 0,
				$t_data['csp-report']['effective-directive'] ?? null,
				$t_data['csp-report']['document-uri'] ?? null,
				$t_data['csp-report']['blocked-uri'] ?? null );
		} elseif( is_array( $t_data ) ) {
			foreach( $t_data as $t_report ) {
				if( isset( $t_report['type'] ) && $t_report['type'] == "csp-violation" ) {
					/* https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Content-Security-Policy/report-to:
					[
						{
							"age": 38940,
							"body": {
								"blockedURL": "inline",
								"disposition": "enforce",
								"documentURL": "https://localhost/my_view_page.php",
								"effectiveDirective": "style-src-elem",
								"lineNumber": 16,
								"originalPolicy": "report-to mantis; default-src 'self'; frame-ancestors 'none'; style-src 'self'; script-src 'self'; img-src 'self' data:",
								"referrer": "https://localhost/view_all_bug_page.php",
								"sample": "",
								"sourceFile": "https://localhost/my_view_page.php",
								"statusCode": 200
							},
							"type": "csp-violation",
							"url": "https://localhost/my_view_page.php",
							"user_agent": "Mozilla/5.0 ..."
						},
						...
					] */
					report( $t_now,
						$t_report['body']['sourceFile'] ?? '',
						$t_report['body']['lineNumber'] ?? 0,
						$t_report['body']['effectiveDirective'] ?? null,
						$t_report['body']['documentURL'] ?? null,
						$t_report['body']['blockedURL'] ?? null );
				}
			}
		}
	}
}

