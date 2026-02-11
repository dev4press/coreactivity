<?php

use Dev4Press\Plugin\CoreActivity\Admin\AJAX;
use Dev4Press\Plugin\CoreActivity\Admin\Plugin as AdminPlugin;
use Dev4Press\Plugin\CoreActivity\Basic\DB;
use Dev4Press\Plugin\CoreActivity\Basic\Plugin;
use Dev4Press\Plugin\CoreActivity\Basic\Settings;
use Dev4Press\Plugin\CoreActivity\Basic\Wizard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function coreactivity() : Plugin {
	return Plugin::i();
}

function coreactivity_settings() : Settings {
	return Settings::instance();
}

function coreactivity_db() : DB {
	return DB::i();
}

function coreactivity_admin() : AdminPlugin {
	return AdminPlugin::i();
}

function coreactivity_ajax() : AJAX {
	return AJAX::i();
}

function coreactivity_wizard() : Wizard {
	return Wizard::i();
}
