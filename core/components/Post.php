<?php

namespace Dev4Press\Plugin\CoreActivity\Components;

use Dev4Press\Plugin\CoreActivity\Base\Component;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Post extends Component {
	protected $plugin = 'coreactivity';
	protected $name = 'post';
	protected $object_type = 'post';

	public function tracking() {

	}

	public function label() : string {
		return __( "Posts", "coreactivity" );
	}

	protected function get_events() : array {
		return array(
			'status-change' => array( 'label' => __( "Post Status Change", "coreactivity" ) )
		);
	}
}
