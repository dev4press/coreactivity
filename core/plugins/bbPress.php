<?php

namespace Dev4Press\Plugin\CoreActivity\Plugins;

use Dev4Press\Plugin\CoreActivity\Base\Plugin;
use WP_Post;

class bbPress extends Plugin {
	protected $plugin = 'coreactivity';
	protected $name = 'bbpress';
	protected $icon = 'logo-bbpress';
	protected $object_type = 'post';
	protected $plugin_file = 'bbpress/bbpress.php';
	protected $post_types = array();

	public function __construct() {
		parent::__construct();

		if ( $this->is_available() ) {
			add_filter( 'coreactivity_post_do_not_log_post_types', array( $this, 'init_post_types' ) );
		}
	}

	public function init() {
		$this->post_types = array( bbp_get_forum_post_type(), bbp_get_topic_post_type(), bbp_get_reply_post_type() );
	}

	public function init_post_types( $post_types ) {
		return array_merge( $post_types, array( bbp_get_forum_post_type(), bbp_get_topic_post_type(), bbp_get_reply_post_type() ) );
	}

	public function tracking() {
		if ( $this->is_active( 'status-change' ) ) {
			add_action( 'transition_post_status', array( $this, 'event_transition_post_status' ), 10, 3 );
		}
	}

	public function label() : string {
		return __( "bbPress", "coreactivity" );
	}

	protected function get_events() : array {
		return array(
			'status-change' => array( 'label' => __( "Post Status Change", "coreactivity" ) )
		);
	}

	public function event_transition_post_status( $new_status, $old_status, $post ) {
		debugpress_error_log( $post );
		if ( $post instanceof WP_Post && in_array( $post->post_type, $this->post_types ) ) {
			$this->log( 'status-change', array(
				'object_id' => $post->ID
			), array(
				'old_status' => $old_status,
				'new_status' => $new_status
			) );
		}
	}
}
