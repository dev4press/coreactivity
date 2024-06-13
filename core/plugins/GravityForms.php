<?php

namespace Dev4Press\Plugin\CoreActivity\Plugins;

use Dev4Press\Plugin\CoreActivity\Base\Plugin;
use Dev4Press\v49\Core\Quick\Sanitize;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GravityForms extends Plugin {
	protected $plugin = 'coreactivity';
	protected $name = 'gravityforms';
	protected $object_type = 'gform';
	protected $icon = 'brand-gravityforms';
	protected $plugin_file = 'gravityforms/gravityforms.php';

	public function registered_object_types( array $object_types ) : array {
		$object_types['gform']  = __( 'Gravity Form', 'coreactivity' );
		$object_types['gentry'] = __( 'Gravity Entry', 'coreactivity' );

		return $object_types;
	}

	public function tracking() {
		if ( $this->is_active( 'created' ) ) {
			add_action( 'gform_after_save_form', array( $this, 'event_after_save_form' ), 10, 2 );
		}

		if ( $this->is_active( 'deleted' ) ) {
			add_action( 'gform_before_delete_form', array( $this, 'event_before_delete_form' ) );
		}

		if ( $this->is_active( 'trashed' ) ) {
			add_action( 'gform_post_form_trashed', array( $this, 'event_post_form_trashed' ) );
		}

		if ( $this->is_active( 'restored' ) ) {
			add_action( 'gform_post_form_restored', array( $this, 'event_post_form_restored' ) );
		}

		if ( $this->is_active( 'activated' ) ) {
			add_action( 'gform_post_form_activated', array( $this, 'event_post_form_activated' ) );
		}

		if ( $this->is_active( 'deactivated' ) ) {
			add_action( 'gform_post_form_deactivated', array( $this, 'event_post_form_deactivated' ) );
		}
	}

	public function label() : string {
		return __( 'Gravity Forms', 'coreactivity' );
	}

	protected function get_events() : array {
		return array(
			'created'     => array(
				'label' => __( 'Form Created', 'coreactivity' ),
			),
			'deleted'     => array(
				'label' => __( 'Form Deleted', 'coreactivity' ),
			),
			'trashed'     => array(
				'label' => __( 'Form Trashed', 'coreactivity' ),
			),
			'restored'    => array(
				'label' => __( 'Form Restored', 'coreactivity' ),
			),
			'activated'   => array(
				'label' => __( 'Form Activated', 'coreactivity' ),
			),
			'deactivated' => array(
				'label' => __( 'Form Deactivated', 'coreactivity' ),
			),
		);
	}

	public function event_after_save_form( $meta, $is_new ) {
		if ( $is_new ) {
			$this->log( 'created', array( 'object_id' => absint( $meta['id'] ) ) );
		}
	}

	public function event_before_delete_form( $form_id ) {
		$this->log( 'deleted', array( 'object_id' => absint( $form_id ) ) );
	}

	public function event_post_form_trashed( $form_id ) {
		$this->log( 'trashed', array( 'object_id' => absint( $form_id ) ) );
	}

	public function event_post_form_restored( $form_id ) {
		$this->log( 'restored', array( 'object_id' => absint( $form_id ) ) );
	}

	public function event_post_form_activated( $form_id ) {
		$this->log( 'activated', array( 'object_id' => absint( $form_id ) ) );
	}

	public function event_post_form_deactivated( $form_id ) {
		$this->log( 'deactivated', array( 'object_id' => absint( $form_id ) ) );
	}
}
