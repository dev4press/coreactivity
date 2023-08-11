<?php

namespace Dev4Press\Plugin\CoreActivity\Admin;

use Dev4Press\Plugin\CoreActivity\Basic\DB;
use Dev4Press\Plugin\CoreActivity\Log\Activity;
use Dev4Press\v43\Core\Admin\GetBack as BaseGetBack;
use Dev4Press\v43\Core\Quick\Sanitize;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GetBack extends BaseGetBack {
	protected function process() {
		parent::process();

		if ( ! empty( $this->a()->panel ) ) {
			if ( $this->is_bulk_action() ) {
				if ( $this->a()->panel == 'events' ) {
					$this->bulk_panel_events();
				}
			} else {
				if ( $this->a()->panel == 'dashboard' ) {
					$this->action_dashboard();
				}
			}
		}

		do_action( 'coreactivity_admin_getback_handler', $this->p(), $this->a() );
	}

	public function action_dashboard() {
		$action = $this->get_single_action();

		if ( in_array( $action, array( 'disable-logging', 'enable-logging' ) ) ) {
			check_admin_referer( 'coreactivity-' . $action );

			$value = $action == 'enable-logging';

			coreactivity_change_logging_status( $value );

			wp_redirect( $this->a()->current_url() );
			exit;
		}
	}

	public function bulk_panel_events() {
		check_admin_referer( 'bulk-events' );

		$action = $this->get_bulk_action();

		if ( $action != '' ) {
			$ids = isset( $_GET['event'] ) ? (array) $_GET['event'] : array();
			$ids = Sanitize::ids_list( $ids );

			if ( ! empty( $ids ) ) {
				if ( $action == 'enable' || $action == 'disable' ) {
					$new = $action == 'enable' ? 'active' : 'inactive';

					foreach ( $ids as $event_id ) {
						DB::instance()->change_event_status( $event_id, $new );
					}
				} else if ( substr( $action, 0, 13 ) == 'notifications' ) {
					$elements = explode( '-', substr( $action, 14 ) );

					if ( count( $elements ) == 2 ) {
						$notification = $elements[0];
						$status       = $elements[1];

						if ( in_array( $notification, array( 'instant', 'daily', 'weekly' ), true ) && in_array( $status, array( 'on', 'off' ), true ) ) {
							foreach ( $ids as $event_id ) {
								Activity::instance()->event_notification_toggle( $event_id, $notification, $status );
							}
						}
					}
				}
			}

			wp_redirect( $this->a()->current_url() . '&message=events-updated' );
			exit;
		}
	}
}
