<?php

namespace Dev4Press\Plugin\CoreActivity\plugins;

use Dev4Press\Plugin\CoreActivity\Base\Component;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UserSwitching extends Component {
	protected $plugin = 'coreactivity';
	protected $name = 'user-switching';
	protected $group = 'plugin';

	public function tracking() {
		// TODO: Implement tracking() method.
	}

	public function label() : string {
		return __( "User Switching" );
	}

	protected function get_events() : array {
		return array(
			'404'        => array( 'label' => __( "404 Not Found", "coreactivity" ) ),
			'404-php'    => array( 'label' => __( "404 Not Found PHP", "coreactivity" ) ),
			'404-file'   => array( 'label' => __( "404 Not Found File", "coreactivity" ) ),
			'404-media'  => array( 'label' => __( "404 Not Found Media", "coreactivity" ) ),
			'404-script' => array( 'label' => __( "404 Not Found Script", "coreactivity" ) ),
			'404-style'  => array( 'label' => __( "404 Not Found Style", "coreactivity" ) )
		);
	}
}
