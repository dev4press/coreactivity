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
	protected $icon = 'ui-memo-pad';
	protected $do_not_log = array();

	public function tracking() {

	}

	public function init() {
		$this->do_not_log = apply_filters( 'coreactivity_post_do_not_log_post_types', array() );
	}

	public function is_post_type_allowed( string $post_type ) : bool {
		return ! in_array( $post_type, $this->do_not_log );
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
