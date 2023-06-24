<?php

namespace Dev4Press\Plugin\CoreActivity\Admin;

use Dev4Press\Plugin\CoreActivity\Basic\InstallDB;
use Dev4Press\Plugin\CoreActivity\Log\Cleanup;
use Dev4Press\v43\Core\Admin\PostBack as BasePostBack;
use Dev4Press\v43\Core\Quick\Sanitize;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PostBack extends BasePostBack {
	protected function process() {
		parent::process();

		do_action( 'coreactivity_admin_postback_handler', $this->p() );
	}

	protected function tools() {
		if ( $this->a()->subpanel == 'cleanup' ) {
			$this->cleanup();
		} else {
			parent::tools();
		}
	}

	protected function cleanup() {
		$data = $_POST[ 'coreactivity' ][ 'tools-cleanup' ] ?? array();

		$when = $data[ 'period' ] ?? '';
		$what = $data[ 'events' ] ?? array();
		$msg  = 'nothing';

		if ( ! empty( $when ) && ! empty( $what ) && strlen( $when ) == 4 ) {

			$what = Sanitize::ids_list( $what );

			Cleanup::instance()->cleanup_log( $when, $what );

			$msg = 'cleanup-completed';
		}

		wp_redirect( $this->a()->current_url() . '&message=' . $msg );
		exit;
	}

	protected function remove() {
		$data = $_POST[ 'coreactivitytools' ];

		$remove  = isset( $data[ 'remove' ] ) ? (array) $data[ 'remove' ] : array();
		$message = 'nothing-removed';

		if ( ! empty( $remove ) ) {
			if ( isset( $remove[ 'settings' ] ) && $remove[ 'settings' ] == 'on' ) {
				$this->a()->settings()->remove_plugin_settings_by_group( 'settings' );
			}

			if ( isset( $remove[ 'drop' ] ) && $remove[ 'drop' ] == 'on' ) {
				InstallDB::instance()->drop();

				if ( ! isset( $remove[ 'disable' ] ) ) {
					$this->a()->settings()->mark_for_update();
				}
			} else if ( isset( $remove[ 'truncate' ] ) && $remove[ 'truncate' ] == 'on' ) {
				InstallDB::instance()->truncate();
			}

			if ( isset( $remove[ 'disable' ] ) && $remove[ 'disable' ] == 'on' ) {
				coreactivity()->deactivate();

				wp_redirect( admin_url( 'plugins.php' ) );
				exit;
			}

			$message = 'removed';
		}

		wp_redirect( $this->a()->current_url() . '&message=' . $message );
		exit;
	}
}
