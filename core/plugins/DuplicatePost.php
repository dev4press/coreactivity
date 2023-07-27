<?php

namespace Dev4Press\Plugin\CoreActivity\Plugins;

use Dev4Press\Plugin\CoreActivity\Base\Plugin;
use WP_Post;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DuplicatePost extends Plugin {
	protected $plugin = 'coreactivity';
	protected $name = 'duplicate-post';
	protected $object_type = 'post';
	protected $icon = 'ui-paste';
	protected $plugin_file = 'duplicate-post/duplicate-post.php';
	protected $do_not_log = array();

	public function tracking() {
		if ( $this->is_active( 'duplicated' ) ) {
			add_action( 'dp_duplicate_post', array( $this, 'event_duplicated' ), 100, 2 );
			add_action( 'dp_duplicate_page', array( $this, 'event_duplicated' ), 100, 2 );
		}
	}

	public function init() {
		/**
		 * Filter the list of post types not to log for the Duplicate Post plugin. Posts belonging to post types on this list will not be logged when duplicated.
		 *
		 * @param array $taxonomies name of the post types not to log, by default is empty array.
		 *
		 * @return array array with names of post types not to log.
		 */
		$this->do_not_log = apply_filters( 'coreactivity_duplicate_post_do_not_log_post_types', array() );
	}

	public function is_post_type_allowed( string $post_type ) : bool {
		return ! in_array( $post_type, $this->do_not_log );
	}

	public function label() : string {
		return __( "Duplicate Post", "coreactivity" );
	}

	protected function get_events() : array {
		return array(
			'duplicated' => array( 'label' => __( "Post Duplicated", "coreactivity" ) )
		);
	}

	public function event_duplicated( $new_post_id, $original_post ) {
		$post = get_post( $new_post_id );

		if ( $post instanceof WP_Post && $original_post instanceof WP_Post ) {
			if ( $this->is_post_type_allowed( $post->post_type ) ) {
				$this->log( 'duplicated', array(
					'object_id' => $post->ID
				), array(
					'source_post_id' => $original_post->ID
				) );
			}
		}
	}
}
