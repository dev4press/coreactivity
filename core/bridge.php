<?php

use Dev4Press\Plugin\CoreActivity\Admin\Plugin as AdminPlugin;
use Dev4Press\Plugin\CoreActivity\Basic\DB;
use Dev4Press\Plugin\CoreActivity\Basic\Plugin;
use Dev4Press\Plugin\CoreActivity\Basic\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function coreactivity() : Plugin {
	return Plugin::instance();
}

function coreactivity_settings() : Settings {
	return Settings::instance();
}

function coreactivity_db() : DB {
	return DB::instance();
}

function coreactivity_admin() : AdminPlugin {
	return AdminPlugin::instance();
}
