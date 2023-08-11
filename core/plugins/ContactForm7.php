<?php

namespace Dev4Press\Plugin\CoreActivity\Plugins;

use Dev4Press\Plugin\CoreActivity\Base\Plugin;
use WP_Post;
use WPCF7_Submission;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ContactForm7 extends Plugin {
	protected $plugin = 'coreactivity';
	protected $name = 'contact-form-7';
	protected $object_type = 'wpcf7form';
	protected $icon = 'ui-edit';
	protected $plugin_file = 'contact-form-7/wp-contact-form-7.php';
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
		$object_types['wpcf7form'] = __( "Contact Form 7", "coreactivity" );

		return $object_types;
	}

	public function tracking() {
		add_filter( 'wpcf7_spam', array( $this, 'prepare_submission' ), 10, 2 );

		if ( $this->is_active( 'submission-sent' ) ) {
			add_action( 'wpcf7_mail_sent', array( $this, 'event_send' ) );
		}

		if ( $this->is_active( 'submission-failed' ) ) {
			add_action( 'wpcf7_mail_failed', array( $this, 'event_failed' ) );
		}

		if ( $this->is_active( 'form-deleted' ) ) {
			add_action( 'delete_post', array( $this, 'event_delete_post' ), 10, 2 );
		}
	}

	public function label() : string {
		return __( "Contact Form 7", "coreactivity" );
	}

	protected function get_events() : array {
		return array(
			'form-deleted'      => array(
				'label' => __( "Form Deleted", "coreactivity" ),
			),
			'submission-sent'   => array(
				'label' => __( "Submission Sent", "coreactivity" ),
			),
			'submission-failed' => array(
				'label' => __( "Submission Failed", "coreactivity" ),
			),
		);
	}

	public function prepare_submission( $spam, WPCF7_Submission $entry ) {
		$submission = array(
			'data'   => $entry->get_posted_data(),
			'fields' => $entry->get_contact_form()->scan_form_tags(),
			'meta'   => array(),
		);

		foreach ( $submission['fields'] as $field ) {
			if ( in_array( 'coreactivity:log', $field->options ) ) {
				$submission['meta'][ 'field_' . $field->name ] = $submission['data'][ $field->name ] ?? '';
			}
		}

		$this->storage[ $entry->get_contact_form()->id() ] = $submission;

		return $spam;
	}

	public function event_send( $form ) {
		if ( isset( $this->storage[ $form->id() ] ) ) {
			$this->log( 'submission-sent', array(
				'object_id' => $form->id(),
			), $this->storage[ $form->id() ]['meta'] );
		}
	}

	public function event_failed( $form ) {
		if ( isset( $this->storage[ $form->id() ] ) ) {
			$this->log( 'submission-failed', array(
				'object_id' => $form->id(),
			), $this->storage[ $form->id() ]['meta'] );
		}
	}

	public function event_delete_post( $post_id, $post ) {
		if ( $post instanceof WP_Post && $post->post_type == 'wpcf7_contact_form' ) {
			$this->log( 'deleted', array(
				'object_id' => $post->ID,
			), array(
				'post_title' => $post->post_title,
			) );
		}
	}

	private function _post_types() : array {
		return array(
			'wpcf7_contact_form',
		);
	}
}
