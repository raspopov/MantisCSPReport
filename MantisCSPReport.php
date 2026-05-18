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

	/**
	 * A method that populates the plugin information and minimum requirements.
	 *
	 * @return void
	 */
	public function register() {
		$this->name = plugin_lang_get( 'title' );
		$this->description = plugin_lang_get( 'description' );
		$this->page = 'config';

		$this->version = '1.1.0';
		$this->requires = [
			'MantisCore' => '2.0'
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
			'enable' => ON,
			'ignore' => [
				'moz-extension',
				'chrome-extension',
				'safari-extension',
				'ms-browser-extension',
				'edge-extension',
				'about',
			],
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
		return [
			[ 'CreateTableSQL', [ plugin_table( 'reports' ), "
				id        I  NOTNULL PRIMARY AUTOINCREMENT,
				date      I  NOTNULL,
				source    XL NOTNULL,
				line      I  NOTNULL,
				directive XL NOTNULL,
				document  XL NOTNULL,
				blocked   XL NOTNULL
			" ] ],
		];
	}

	/**
	 * EVENT_CORE_HEADERS hook.
	 *
	 * @return void
	 */
	public function core_headers() {
		if( plugin_config_get( 'enable', ON ) ) {
			$t_url = plugin_page( 'report.php' );
			if( http_is_protocol_https() ) {
				http_csp_add( 'report-to', 'default' );
				header( 'Reporting-Endpoints: default="' . $t_url . '"' );
			} else {
				http_csp_add( 'report-uri', $t_url );
			}
		}
	}

	/**
	 * EVENT_MENU_MANAGE hook.
	 *
	 * @return array
	 */
	function menu_manage() {
		return [ '<a href="' . plugin_page( 'view.php' ) . '">' . plugin_lang_get( 'label' ) . '</a>' ];
	}
}
