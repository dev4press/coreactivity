<?php

/**
 * Plugin Name:       coreActivity
 * Plugin URI:        https://plugins.dev4press.com/coreactivity/
 * Description:       Monitor and log all kinds of activity happening on the WordPress website, with fine control over events to log, detailed log and events panels, and more.
 * Author:            Milan Petrovic
 * Author URI:        https://www.dev4press.com/
 * Text Domain:       coreactivity
 * Version:           1.0.2
 * Requires at least: 5.5
 * Tested up to:      6.3
 * Requires PHP:      7.3
 * Network:           true
 * License:           GPLv3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package CoreActivity
 *
 * == Copyright ==
 * Copyright 2008 - 2023 Milan Petrovic (email: support@dev4press.com)
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

use Dev4Press\v43\WordPress;

$coreactivity_dirname_basic = dirname( __FILE__ ) . '/';
$coreactivity_urlname_basic = plugins_url( '/', __FILE__ );

const COREACTIVITY_VERSION = '1.0.2';
const COREACTIVITY_FILE    = __FILE__;

define( 'COREACTIVITY_PATH', $coreactivity_dirname_basic );
define( 'COREACTIVITY_URL', $coreactivity_urlname_basic );
define( 'COREACTIVITY_D4PLIB_PATH', $coreactivity_dirname_basic . 'd4plib/' );
define( 'COREACTIVITY_D4PLIB_URL', $coreactivity_urlname_basic . 'd4plib/' );

require_once( COREACTIVITY_D4PLIB_PATH . 'core.php' );

require_once( COREACTIVITY_PATH . 'core/autoload.php' );
require_once( COREACTIVITY_PATH . 'core/bridge.php' );
require_once( COREACTIVITY_PATH . 'core/functions.php' );

coreactivity();
coreactivity_settings();

if ( WordPress::instance()->is_admin() ) {
	coreactivity_admin();
}

if ( WordPress::instance()->is_ajax() ) {
	coreactivity_ajax();
}
