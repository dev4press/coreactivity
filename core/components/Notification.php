<?php

namespace Dev4Press\Plugin\CoreActivity\Components;

use Dev4Press\Plugin\CoreActivity\Base\Component;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Notification extends Component {
	protected $plugin = 'coreactivity';
	protected $name = 'notifications';
	protected $icon = 'plugin-coreactivity';
	protected $object_type = 'notification';

	public function tracking() {
		// TODO: Implement tracking() method.
	}

	public function label() : string {
		return __( "Notifications" );
	}

	protected function get_events() : array {
		return array();
	}
}
