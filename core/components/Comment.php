<?php

namespace Dev4Press\Plugin\CoreActivity\Components;

use Dev4Press\Plugin\CoreActivity\Base\Component;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Comment extends Component {
	protected $plugin = 'coreactivity';
	protected $name = 'comment';
	protected $object_type = 'comment';
	protected $icon = 'ui-comment-dots';
	protected $do_not_log = array();

	public function tracking() {

	}

	public function init() {
		$this->do_not_log = apply_filters( 'coreactivity_comment_do_not_log_post_types', array() );
	}

	public function label() : string {
		return __( "Comments", "coreactivity" );
	}

	protected function get_events() : array {
		return array(
			'status-change' => array( 'label' => __( "Comment Status Change", "coreactivity" ) )
		);
	}
}
