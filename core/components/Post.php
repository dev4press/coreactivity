<?php

namespace Dev4Press\Plugin\CoreActivity\Components;

use Dev4Press\Plugin\CoreActivity\Base\Component;
use WP_Post;

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
		if ( $this->is_active( 'status-change' ) ) {
			add_action( 'transition_post_status', array( $this, 'event_transition_post_status' ), 10, 3 );
		}

		if ( $this->is_active( 'deleted' ) ) {
			add_action( 'delete_post', array( $this, 'event_delete_post' ), 10, 2 );
		}
	}

	public function init() {
		$this->do_not_log = apply_filters( 'coreactivity_post_do_not_log_post_types', array( 'revision', 'attachment' ) );
	}

	public function is_post_type_allowed( string $post_type ) : bool {
		return ! in_array( $post_type, $this->do_not_log );
	}

	public function label() : string {
		return __( "Posts", "coreactivity" );
	}

	protected function get_events() : array {
		return array(
			'status-change' => array( 'label' => __( "Post Status Change", "coreactivity" ) ),
			'deleted'       => array( 'label' => __( "Post Deleted", "coreactivity" ) )
		);
	}

	public function event_transition_post_status( $new_status, $old_status, $post ) {
		if ( $post instanceof WP_Post && $this->is_post_type_allowed( $post->post_type ) ) {
			$this->log( 'status-change', array(
				'object_id' => $post->ID
			), array(
				'old_status' => $old_status,
				'new_status' => $new_status
			) );
		}
	}

	public function event_delete_post( $post_id, $post ) {
		if ( $post instanceof WP_Post && $this->is_post_type_allowed( $post->post_type ) ) {
			$this->log( 'deleted', array(
				'object_id' => $post->ID
			), array(
				'post_type'   => $post->post_type,
				'post_status' => $post->post_status,
				'post_name'   => $post->post_name,
				'post_title'  => $post->post_title
			) );
		}
	}
}
