<?php

/*
Plugin Name:       LoggerPress
Plugin URI:        https://plugins.dev4press.com/loggerpress/
Description:       Log
Author:            Milan Petrovic
Author URI:        https://www.dev4press.com/
Text Domain:       loggerpress
Version:           1.0
Requires at least: 5.5
Tested up to:      6.2
Requires PHP:      7.3
License:           GPLv3 or later
License URI:       https://www.gnu.org/licenses/gpl-3.0.html

== Copyright ==
Copyright 2008 - 2023 Milan Petrovic (email: support@dev4press.com)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>
*/

use Dev4Press\v40\WordPress;

$loggerpress_dirname_basic = dirname( __FILE__ ) . '/';
$loggerpress_urlname_basic = plugins_url( '/', __FILE__ );

define( 'LOGGERPRESS_PATH', $loggerpress_dirname_basic );
define( 'LOGGERPRESS_URL', $loggerpress_urlname_basic );
define( 'LOGGERPRESS_D4PLIB_PATH', $loggerpress_dirname_basic . 'd4plib/' );
define( 'LOGGERPRESS_D4PLIB_URL', $loggerpress_urlname_basic . 'd4plib/' );

require_once( LOGGERPRESS_D4PLIB_PATH . 'core.php' );

require_once( LOGGERPRESS_PATH . 'core/autoload.php' );
require_once( LOGGERPRESS_PATH . 'core/bridge.php' );
require_once( LOGGERPRESS_PATH . 'core/functions.php' );
