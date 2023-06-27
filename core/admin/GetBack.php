<?php

namespace Dev4Press\Plugin\CoreActivity\Admin;

use Dev4Press\Plugin\CoreActivity\Basic\DB;
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
			}
		}

		do_action( 'coreactivity_admin_getback_handler', $this->p(), $this->a() );
	}

	public function bulk_panel_events() {
		check_admin_referer( 'bulk-events' );

		$action = $this->get_bulk_action();

		if ( $action != '' ) {
			$ids = isset( $_GET[ 'event' ] ) ? (array) $_GET[ 'event' ] : array();
			$ids = Sanitize::ids_list( $ids );

			if ( ! empty( $ids ) ) {
				$new = $action == 'enable' ? 'active' : 'inactive';

				foreach ( $ids as $event_id ) {
					DB::instance()->change_event_status( $event_id, $new );
				}
			}

			wp_redirect( $this->a()->current_url() . '&message=events-updated' );
			exit;
		}
	}
}
