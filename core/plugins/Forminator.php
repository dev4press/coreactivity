<?php

namespace Dev4Press\Plugin\CoreActivity\Plugins;

use Dev4Press\Plugin\CoreActivity\Base\Plugin;
use WP_Post;
use WPCF7_Submission;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Forminator extends Plugin {
	protected $plugin = 'coreactivity';
	protected $name = 'forminator';
	protected $object_type = 'forminator';
	protected $icon = 'ui-memo';
	protected $plugin_file = 'forminator/forminator.php';
	protected $storage = array();

	public function __construct() {
		parent::__construct();

		if ( $this->is_available() ) {
			add_filter( 'coreactivity_post_do_not_log_post_types', array( $this, 'init_post_types' ) );
		}
	}

	public function init_post_types( $post_types ) {
		return array_merge( $post_types, $this->_post_types() );
	}

	public function registered_object_types( array $object_types ) : array {
		$object_types['forminator'] = __( 'Forminator', 'coreactivity' );

		return $object_types;
	}

	public function tracking() {
		if ( $this->is_active( 'form-deleted' ) ) {
			add_action( 'delete_post', array( $this, 'event_delete_post' ), 10, 2 );
		}
	}

	public function label() : string {
		return __( 'Forminator', 'coreactivity' );
	}

	protected function get_events() : array {
		return array(
			'form-deleted'      => array(
				'label' => __( 'Form Deleted', 'coreactivity' ),
			),
		);
	}

	public function event_delete_post( $post_id, $post ) {
		if ( $post instanceof WP_Post && $post->post_type == 'forminator_forms' ) {
			$this->log( 'deleted', array(
				'object_id' => $post->ID,
			), array(
				'post_title' => $post->post_title,
			) );
		}
	}

	private function _post_types() : array {
		return array(
			'forminator_forms',
		);
	}
}
