<?php
/**
 * Plugin Name:       coreActivity: Activity Logging plugin for WordPress
 * Plugin URI:        https://www.dev4press.com/plugins/coreactivity/
 * Description:       Monitor and log all kinds of activity happening on the WordPress website, with fine control over events to log, detailed log and events panels, and more.
 * Author:            Milan Petrovic
 * Author URI:        https://www.dev4press.com/
 * Text Domain:       coreactivity
 * Version:           2.4.2
 * Requires at least: 5.9
 * Tested up to:      6.6
 * Requires PHP:      7.4
 * Requires CP:       2.0
 * Network:           true
 * License:           GPLv3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package coreActivity
 *
 * == Copyright ==
 * Copyright 2008 - 2024 Milan Petrovic (email: support@dev4press.com)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>
 */

use Dev4Press\v50\WordPress;

const COREACTIVITY_VERSION = '2.4.1';
const COREACTIVITY_FILE    = __FILE__;

$coreactivity_dirname_basic = dirname( COREACTIVITY_FILE ) . '/';
$coreactivity_urlname_basic = plugins_url( '/', COREACTIVITY_FILE );

define( 'COREACTIVITY_PATH', $coreactivity_dirname_basic );
define( 'COREACTIVITY_URL', $coreactivity_urlname_basic );
define( 'COREACTIVITY_D4PLIB_PATH', $coreactivity_dirname_basic . 'library/' );
define( 'COREACTIVITY_D4PLIB_URL', $coreactivity_urlname_basic . 'library/' );

require_once COREACTIVITY_D4PLIB_PATH . 'core.php';

require_once COREACTIVITY_PATH . 'core/autoload.php';
require_once COREACTIVITY_PATH . 'core/bridge.php';
require_once COREACTIVITY_PATH . 'core/functions.php';

coreactivity();
coreactivity_settings();

if ( WordPress::instance()->is_admin() ) {
	coreactivity_admin();
}

if ( WordPress::instance()->is_ajax() ) {
	coreactivity_ajax();
}
