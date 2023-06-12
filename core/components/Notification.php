<?php

namespace Dev4Press\Plugin\CoreActivity\Components;

use Dev4Press\Plugin\CoreActivity\Base\Component;
use Dev4Press\v42\Core\Mailer\Detection;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Notification extends Component {
	protected $plugin = 'coreactivity';
	protected $name = 'notifications';
	protected $icon = 'ui-envelope';
	protected $object_type = 'notification';
	protected $storage = array();

	public function tracking() {
		Detection::instance();

		add_action( 'd4p_mailer_notification_detected', array( $this, 'prepare_detection' ) );

		if ( $this->is_active( 'email-sent' ) || $this->is_active( 'email-sent-unknown' ) ) {
			add_action( 'wp_mail_succeeded', array( $this, 'event_sent' ) );
		}

		if ( $this->is_active( 'email-failed' ) || $this->is_active( 'email-failed-unknown' ) ) {
			add_action( 'wp_mail_failed', array( $this, 'event_failed' ) );
		}
	}

	public function label() : string {
		return __( "Notifications", "coreactivity" );
	}

	protected function get_events() : array {
		return array(
			'email-sent'           => array( 'label' => __( "Email Sent", "coreactivity" ) ),
			'email-failed'         => array( 'label' => __( "Email Failed", "coreactivity" ) ),
			'email-sent-unknown'   => array( 'label' => __( "Unknown Email Sent", "coreactivity" ) ),
			'email-failed-unknown' => array( 'label' => __( "Unknown Email Failed", "coreactivity" ) )
		);
	}

	public function prepare_detection( $data ) {
		$this->storage = $data;
	}

	public function event_sent( $email_data ) {
		$event = isset( $this->storage[ 'name' ] ) && ! empty( $this->storage[ 'name' ] ) ? 'email-sent' : 'email-sent-unknown';

		$this->event_final( $event, $email_data );
	}

	public function event_failed( $error ) {
		if ( is_wp_error( $error ) ) {
			$email_data = $error->get_error_data();

			$event = isset( $this->storage[ 'name' ] ) && ! empty( $this->storage[ 'name' ] ) ? 'email-failed' : 'email-failed-unknown';

			$this->event_final( $event, $email_data, $error->get_error_message() );
		}
	}

	protected function event_final( $event, $email_data, $error = '' ) {
		if ( $this->is_active( $event ) ) {
			$this->log( $event, array(
				'object_name' => $this->storage[ 'name' ] ?? ''
			), array(
				'subject' => isset( $email_data[ 'subject' ] ) ? esc_sql( $email_data[ 'subject' ] ) : '',
				'email'   => isset( $email_data[ 'to' ] ) ? esc_sql( $email_data[ 'to' ] ) : '',
				'error'   => $error,
				'source'  => $this->storage[ 'call' ]
			) );
		}
	}
}
