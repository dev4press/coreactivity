<?php

namespace Dev4Press\Plugin\CoreActivity\Admin;

use Dev4Press\Plugin\CoreActivity\Basic\DB;
use Dev4Press\Plugin\CoreActivity\Log\Activity;
use Dev4Press\v44\Core\Admin\GetBack as BaseGetBack;
use Dev4Press\v44\Core\Quick\Sanitize;

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
				} else if ( $this->a()->panel == 'logs' ) {
					$this->action_logs();
				}
			}
		}

		do_action( 'coreactivity_admin_getback_handler', $this->p(), $this->a() );
	}

	private function action_dashboard() {
		$action = $this->get_single_action();

		if ( in_array( $action, array( 'disable-logging', 'enable-logging' ) ) ) {
			check_admin_referer( 'coreactivity-' . $action );

			$value = $action == 'enable-logging';

			coreactivity_change_logging_status( $value );

			wp_redirect( $this->a()->current_url() );
			exit;
		}
	}

	private function action_logs() {
		$action = $this->get_single_action();

		if ( $action == 'do-not-log' ) {
			$object_type = Sanitize::_get_slug( 'object-type' );
			$object_name = isset( $_GET['object-name'] ) ? Sanitize::basic( wp_unslash( urldecode( $_GET['object-name'] ) ) ) : '';

			if ( ! empty( $object_name ) && ! empty( $object_type ) ) {
				check_admin_referer( 'coreactivity-do-not-log-' . $object_name );

				$option = 'exceptions_' . $object_type . '_list';

				$list = coreactivity_settings()->get( $option );

				if ( ! in_array( $object_name, $list ) ) {
					$list[] = $object_name;

					coreactivity_settings()->set( $option, $list, 'settings', true );
				}
			}

			wp_redirect( $this->a()->current_url() );
			exit;
		}
	}

	private function bulk_panel_events() {
		check_admin_referer( 'bulk-events' );

		$action = $this->get_bulk_action();

		if ( $action != '' ) {
			$ids = Sanitize::_get_ids( 'event' );

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
