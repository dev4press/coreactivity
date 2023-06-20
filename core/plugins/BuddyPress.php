<?php

namespace Dev4Press\Plugin\CoreActivity\Plugins;

use Dev4Press\Plugin\CoreActivity\Base\Plugin;

class BuddyPress extends Plugin {
	protected $plugin = 'coreactivity';
	protected $name = 'buddypress';
	protected $icon = 'logo-buddypress';
	protected $plugin_file = 'buddypress/bp-loader.php';

	public function tracking() {

	}

	public function label() : string {
		return __( "BuddyPress", "coreactivity" );
	}

	protected function get_events() : array {
		return array();
	}
}
