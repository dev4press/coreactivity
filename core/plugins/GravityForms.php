<?php

namespace Dev4Press\Plugin\CoreActivity\Plugins;

use Dev4Press\Plugin\CoreActivity\Base\Component;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GravityForms extends Component {
	public function tracking() {

	}

	public function label() : string {
		return __( "Gravity Forms", "coreactivity" );
	}

	protected function get_events() : array {
		return array();
	}
}
