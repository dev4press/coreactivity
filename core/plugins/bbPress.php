<?php

namespace Dev4Press\Plugin\CoreActivity\Plugins;

use Dev4Press\Plugin\CoreActivity\Base\Plugin;

class bbPress extends Plugin {
	protected $plugin = 'coreactivity';
	protected $name = 'bbpress';
	protected $icon = 'logo-bbpress';
	protected $plugin_file = 'bbpress/bbpress.php';

	public function tracking() {

	}

	public function label() : string {
		return __( "bbPress", "coreactivity" );
	}

	protected function get_events() : array {
		return array();
	}
}
