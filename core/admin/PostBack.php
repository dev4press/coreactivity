<?php

namespace Dev4Press\Plugin\CoreActivity\Admin;

use Dev4Press\Plugin\CoreActivity\Basic\InstallDB;
use Dev4Press\Plugin\CoreActivity\Basic\Plugin;
use Dev4Press\Plugin\CoreActivity\Log\Activity;
use Dev4Press\Plugin\CoreActivity\Log\Cleanup;
use Dev4Press\v55\Core\Admin\PostBack as BasePostBack;
use Dev4Press\v55\Core\Quick\Sanitize;
use JetBrains\PhpStorm\NoReturn;
use WP_Filesystem_Direct;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PostBack extends BasePostBack {
	protected function process() : void {
		parent::process();

		if ( $this->p() == $this->get_page_name( 'wizard' ) ) {
			coreactivity_wizard()->panel_postback();
		}

		do_action( 'coreactivity_admin_postback_handler', $this->p(), $this->a() );
	}

	protected function tools() : void {
		if ( $this->a()->subpanel == 'notifications' ) {
			$this->notifications();
		} else if ( $this->a()->subpanel == 'cleanup' ) {
			$this->cleanup();
		} else {
			parent::tools();
		}
	}

	#[NoReturn]
	protected function notifications() : void {
		$data = $_POST['coreactivity']['tools-notifications'] ?? array(); // phpcs:ignore WordPress.Security.NonceVerification,WordPress.Security.ValidatedSanitizedInput

		$msg = 'notifications-updated';

		$instant = $data['instant'] ?? 'skip';
		$daily   = $data['daily'] ?? 'skip';
		$weekly  = $data['weekly'] ?? 'skip';

		Activity::i()->events_notification_bulk_control( $instant, $daily, $weekly );

		wp_redirect( $this->a()->current_url() . '&message=' . $msg );
		exit;
	}

	#[NoReturn]
	protected function cleanup() : void {
		$data = $_POST['coreactivity']['tools-cleanup'] ?? array(); // phpcs:ignore WordPress.Security.NonceVerification,WordPress.Security.ValidatedSanitizedInput

		$when = $data['period'] ?? '';
		$what = $data['events'] ?? array();
		$msg  = 'nothing';

		if ( ! empty( $when ) && ! empty( $what ) && strlen( $when ) == 4 ) {
			$what = Sanitize::ids_list( $what );

			Cleanup::i()->cleanup_log( $when, $what );

			$msg = 'cleanup-completed';
		}

		wp_redirect( $this->a()->current_url() . '&message=' . $msg );
		exit;
	}

	#[NoReturn]
	protected function remove() : void {
		$message = 'nothing-removed';
		$remove  = Sanitize::_get_switch_array( 'coreactivity-tools', 'remove' );

		if ( ! empty( $remove ) ) {
			if ( in_array( 'settings', $remove ) ) {
				$this->a()->settings()->remove_plugin_settings_by_group( 'settings' );
			}

			if ( in_array( 'geo-db', $remove ) ) {
				$path = Plugin::i()->uploads_path();

				if ( $path !== false ) {
					WP_Filesystem();

					$dir = new WP_Filesystem_Direct( 0 );
					$dir->rmdir( $path, true );
				}
			}

			if ( in_array( 'drop', $remove ) ) {
				InstallDB::i()->drop();

				if ( ! isset( $remove['disable'] ) ) {
					$this->a()->settings()->mark_for_update();
				}
			} else if ( in_array( 'truncate', $remove ) ) {
				InstallDB::i()->truncate();
			}

			if ( in_array( 'disable', $remove ) ) {
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
