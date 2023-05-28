<?php

namespace Dev4Press\Plugin\CoreActivity\Components;

use Dev4Press\Plugin\CoreActivity\Base\Component;

class User extends Component {
	protected $plugin = 'coreactivity';
	protected $name = 'user';

	public function label() : string {
		return __( "Users" );
	}

	protected function get_events() : array {
		return array(
			'login'  => array( 'label' => __( "Login" ) ),
			'logout' => array( 'label' => __( "Logout" ) )
		);
	}
}
