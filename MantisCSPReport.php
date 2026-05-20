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

class MantisCSPReportPlugin extends MantisPlugin {

	public const DEFAULT_ENABLE = ON;
	public const DEFAULT_IGNORE = [
		'moz-extension',
		'chrome-extension',
		'safari-extension',
		'ms-browser-extension',
		'edge-extension',
		'about',
	];
	public const DEFAULT_AGING = 24 * 60 * 60;
	public const DEFAULT_GROUPED = ON;
	public const DEFAULT_PAGE_NUMBER = 1;

	/**
	 * A method that populates the plugin information and minimum requirements.
	 *
	 * @return void
	 */
	public function register() {
		$this->name = plugin_lang_get( 'title' );
		$this->description = plugin_lang_get( 'description' );
		$this->page = 'config';

		$this->version = '1.3.0';
		$this->requires = [
			'MantisCore' => '2.28'
		];
		$this->uses = [
			'MantisGraph' => '2.28',
		];

		$this->author = 'Nikolay Raspopov';
		$this->contact = 'raspopov@cherubicsoft.com';
		$this->url = 'https://github.com/raspopov/MantisCSPReport';
	}

	/**
	 * Default plugin configuration.
	 *
	 * @return array
	 */
	function config() {
		return [
			'enable' => self::DEFAULT_ENABLE,
			'ignore' => self::DEFAULT_IGNORE,
			'aging' => self::DEFAULT_AGING,
			'grouped' => self::DEFAULT_GROUPED,
			'page_number' => self::DEFAULT_PAGE_NUMBER,
		];
	}

	/**
	 * Register event hooks for plugin.
	 *
	 * @return array
	 */
	public function hooks() {
		return [
			'EVENT_CORE_HEADERS' => 'core_headers',
			'EVENT_MENU_MANAGE' => 'menu_manage',
		];
	}

	/**
	 * Array of Database Schema changes for the plugin.
	 *
	 * @return array
	 */
	public function schema() {
		$t_table = plugin_table( 'reports' );
		return [
			[ 'CreateTableSQL', [ $t_table , "
				id        I  NOTNULL PRIMARY AUTOINCREMENT,
				date      I  NOTNULL,
				source    XL NOTNULL,
				line      I  NOTNULL,
				directive XL NOTNULL,
				document  XL NOTNULL,
				blocked   XL NOTNULL" ] ],
			[ 'CreateIndexSQL', [ 'idx_' . $this->basename . '_source',
				$t_table, [ 'source', 'line', 'directive' ] ] ],
		];
	}

	/**
	 * EVENT_CORE_HEADERS hook.
	 *
	 * @return void
	 */
	public function core_headers() {
		if( ON == plugin_config_get( 'enable', self::DEFAULT_ENABLE ) ) {
			$t_url = plugin_page( 'report.php' );
			if( http_is_protocol_https() ) {
				http_csp_add( 'report-to', 'default' );
				header( 'Reporting-Endpoints: default="' . $t_url . '"' );
			} else {
				http_csp_add( 'report-uri', $t_url );
			}
		}

		# Load Mantis Graph if installed
		if( plugin_is_loaded( 'MantisGraph' ) ) {
			if( gpc_get_string( 'page', '' ) === $this->basename . '/graphs.php' ) {
				$t_mantisgraph = plugin_get( 'MantisGraph' );
				$t_mantisgraph->include_chartjs();
				require_js( [ plugin_file( 'MantisGraph.js', false, $t_mantisgraph->basename ) ] );
			}
		}
	}

	/**
	 * EVENT_MENU_MANAGE hook.
	 *
	 * @return array
	 */
	function menu_manage() {
		$t_menu []= '<a href="' . plugin_page( 'view.php' ) . '">' . plugin_lang_get( 'view' ) . '</a>';
		if( function_exists( 'graph_bar' ) ) {
			$t_menu []= '<a href="' . plugin_page( 'graphs.php' ) . '">' . plugin_lang_get( 'graphs' ) . '</a>';
		}
		return $t_menu;
	}
	
	/**
	 * Limit string.
	 *
	 * @param string $p_text  Text.
	 * @param int    $p_limit Max length.
	 * @return string
	 */
	static function limit_text( string $p_text, int $p_limit = 48 ) {
		$t_length = strlen( $p_text );
		return ( $t_length > $p_limit )
		 ? substr( $p_text, 0, $p_limit / 2 - 1 ) . mb_chr( 8230 ) . substr( $p_text, $t_length - $p_limit / 2 )
		 : $p_text;
	}

	/**
	 * Strip the $g_path.
	 *
	 * @param string $p_url Url or text.
	 * @return string
	 */
	static function strip_path( string $p_url ) {
		$t_path = config_get_global( 'path' );
		return str_starts_with( $p_url, $t_path ) ? substr( $p_url, strlen( $t_path ) ) : $p_url;
	}

	/**
	 * Make a link if possible.
	 *
	 * @param string $p_url Url or text.
	 * @return string
	 */
	static function make_link( string $p_url ) {
		return ( str_starts_with( strtolower( $p_url ), 'http://' ) ||
				 str_starts_with( strtolower( $p_url ), 'https://' ) )
			? '<a href="' . string_attribute( $p_url ) . '">' 
				. string_attribute( self::limit_text( self::strip_path( $p_url ) ) ) . '</a>'
			: string_attribute( self::limit_text( $p_url ) );
	}
}
