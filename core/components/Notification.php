<?php

namespace Dev4Press\Plugin\CoreActivity\Components;

use Dev4Press\Plugin\CoreActivity\Base\Component;
use Dev4Press\v43\Core\Mailer\Detection;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Notification extends Component {
	protected $plugin = 'coreactivity';
	protected $name = 'notification';
	protected $icon = 'ui-envelope';
	protected $object_type = 'notification';
	protected $storage = array();
	protected $do_not_log = array();
	protected $exceptions = array();

	protected $network = array(
		'wp-network-signup-blog-confirmation',
		'wp-network-signup-user-confirmation',
		'wp-network-delete-site-email-content',
		'wp-network-welcome-blog',
		'wp-network-welcome-user',
		'wp-network-new-blog-siteadmin',
		'wp-network-new-user-siteadmin',
		'wp-network-network-admin-email-confirmation',
		'wp-network-network-admin-email-notification',
	);

	public function init() {
		$this->exceptions = coreactivity_settings()->get( 'exceptions_notification_list' );

		if ( coreactivity_settings()->get( 'daily_skip_log', 'notifications' ) ) {
			$this->do_not_log[] = 'coreactivity-daily-digest';
		}

		if ( coreactivity_settings()->get( 'weekly_skip_log', 'notifications' ) ) {
			$this->do_not_log[] = 'coreactivity-weekly-digest';
		}

		if ( coreactivity_settings()->get( 'instant_skip_log', 'notifications' ) ) {
			$this->do_not_log[] = 'coreactivity-instant-notification';
		}

		/**
		 * Filter the list of notification types not to log. Notifications belonging to notification types on this list will not be tracked or logged.
		 * All supported notification types are keys of the array returned by the following call:
		 * `\Dev4Press\v43\Core\Mailer\Detection::instance()->get_supported_types()`
		 *
		 * @param array $notifications name of the post types not to log, by default is empty array.
		 *
		 * @return array array with names of notifications not to log, based on the function mentioned in the description.
		 */
		$this->do_not_log = apply_filters( 'coreactivity_notification_do_not_log_notifications', $this->do_not_log );
	}

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
		return __( 'Notifications', 'coreactivity' );
	}

	protected function get_events() : array {
		return array(
			'email-sent'           => array(
				'label' => __( 'Email Sent', 'coreactivity' ),
			),
			'email-failed'         => array(
				'label' => __( 'Email Failed', 'coreactivity' ),
			),
			'email-sent-unknown'   => array(
				'label' => __( 'Unknown Email Sent', 'coreactivity' ),
			),
			'email-failed-unknown' => array(
				'label' => __( 'Unknown Email Failed', 'coreactivity' ),
			),
		);
	}

	public function logs_meta_column_keys( array $meta_column_keys ) : array {
		$meta_column_keys[ $this->code() ] = array(
			'-' => array(
				'subject',
				'email',
				'error',
			),
		);

		return $meta_column_keys;
	}

	public function prepare_detection( $data ) {
		$this->storage = $data;
	}

	public function event_sent( $email_data ) {
		$event = isset( $this->storage['name'] ) && ! empty( $this->storage['name'] ) ? 'email-sent' : 'email-sent-unknown';

		$this->event_final( $event, $email_data );
	}

	public function event_failed( $error ) {
		if ( is_wp_error( $error ) ) {
			$email_data = $error->get_error_data();

			$event = isset( $this->storage['name'] ) && ! empty( $this->storage['name'] ) ? 'email-failed' : 'email-failed-unknown';

			$this->event_final( $event, $email_data, $error->get_error_message() );
		}
	}

	private function event_final( $event, $email_data, $error = '' ) {
		if ( $this->is_active( $event ) ) {
			$option = $this->storage['name'] ?? '';

			if ( $this->is_exception( $option ) ) {
				return;
			}

			if ( ! in_array( $this->storage['name'], $this->do_not_log ) ) {
				$data = array( 'object_name' => $option );

				if ( in_array( $this->storage['name'], $this->network ) ) {
					$data['blog_id'] = 0;
				}

				$this->log( $event, $data, array(
					'subject' => isset( $email_data['subject'] ) ? esc_sql( $email_data['subject'] ) : '',
					'email'   => isset( $email_data['to'] ) ? esc_sql( $email_data['to'] ) : '',
					'error'   => $error,
					'source'  => $this->storage['call'],
				) );
			}
		}
	}

	private function is_exception( $option ) : bool {
		return ! empty( $this->exceptions ) && in_array( $option, $this->exceptions );
	}
}
