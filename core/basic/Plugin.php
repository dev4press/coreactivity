<?php

namespace Dev4Press\Plugin\CoreActivity\Basic;

use Dev4Press\Plugin\CoreActivity\Log\Activity;
use Dev4Press\Plugin\CoreActivity\Log\Activity as LogActivity;
use Dev4Press\Plugin\CoreActivity\Log\Core as LogCore;
use Dev4Press\Plugin\CoreActivity\Log\GEO as LogLocation;
use Dev4Press\Plugin\CoreActivity\Log\Metas as LogMetas;
use Dev4Press\Plugin\CoreActivity\Log\Users as LogUsers;
use Dev4Press\Plugin\CoreActivity\Log\Notifications;
use Dev4Press\v51\Core\Plugins\Core;
use Dev4Press\v51\Core\Quick\WPR;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin extends Core {
	public string $svg_icon = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA2NDAgNTEyIj48cGF0aCBmaWxsPSIjMDAwMDAwIiBkPSJNMzYuNDI3LDQ0NkwzNi40MjcsNTBDMzYuNDI3LDI1LjcxNiA1Ni4xNDIsNiA4MC40MjcsNkw1MDQuNDI3LDZDNTI4LjcxMSw2IDU0OC40MjcsMjUuNzE2IDU0OC40MjcsNTBMNTQ4LjQyNywyNTcuMTM4QzU0Mi43NjMsMjU3LjEzNCA1MzcuMTY5LDI1OS42MjggNTMzLjQyNiwyNjQuNDA4TDUyNy4wOTMsMjcyLjQ5M0w1MjcuMDkzLDUwQzUyNy4wOTMsMzcuNDkgNTE2LjkzNywyNy4zMzMgNTA0LjQyNywyNy4zMzNMODAuNDI3LDI3LjMzM0M2Ny45MTcsMjcuMzMzIDU3Ljc2LDM3LjQ5IDU3Ljc2LDUwQzU3Ljc2LDUwIDU3Ljc2LDYxLjg0OSA1Ny43Niw4MS4zMzNMMzg0LjQyNyw4MS4zMzNMMzg0LjQyNywxMDIuNjY3TDU3Ljc2LDEwMi42NjdDNTcuNzYsMjA1Ljg1MyA1Ny43Niw0MjQuNjY3IDU3Ljc2LDQyNC42NjdMMjEyLjc0NSw0MjQuNjY3TDIxMi43NDUsNDQ2TDM2LjQyNyw0NDZaTTc4LjQyNyw2NS42NjdMNzguNDI3LDQ0LjMzM0wxNDguNDI3LDQ0LjMzM0wxNDguNDI3LDY1LjY2N0w3OC40MjcsNjUuNjY3Wk0xNzguNDI3LDY1LjY2N0wxNzguNDI3LDQ0LjMzM0wyMjguNDI3LDQ0LjMzM0wyMjguNDI3LDY1LjY2N0wxNzguNDI3LDY1LjY2N1pNMjU4LjQyNyw2NS42NjdMMjU4LjQyNyw0NC4zMzNMMjg4LjQyNyw0NC4zMzNMMjg4LjQyNyw2NS42NjdMMjU4LjQyNyw2NS42NjdaTTI1NC4wNzEsMjk5LjA5OEwyNTQuMDcxLDQ2OC45MTFDMjU0LjA3MSw0NzcuMDg1IDI2MC43MDcsNDgzLjcyMiAyNjguODgyLDQ4My43MjJMMzk5LjIyNCw0ODMuNzIyTDM5OS4yMjQsNTA1LjA1NUwyNjguODgyLDUwNS4wNTVDMjQ4LjkzMyw1MDUuMDU1IDIzMi43MzcsNDg4Ljg1OSAyMzIuNzM3LDQ2OC45MTFMMjMyLjczNywyNDcuMzY1QzIzMi43MzcsMjI3LjQxNyAyNDguOTMzLDIxMS4yMjEgMjY4Ljg4MiwyMTEuMjIxQzI2OC44ODIsMjExLjIyMSAyODEuMTA4LDIxMS4yMjEgMjgxLjEwOCwyMTEuMjIxTDI4MS4xMDgsMTg5LjM5NEMyODEuMTA4LDE4My41MDMgMjg1Ljg4NCwxNzguNzI4IDI5MS43NzUsMTc4LjcyOEwzMzMuMTMsMTc4LjcyOEMzMzkuMDIxLDE3OC43MjggMzQzLjc5NywxODMuNTAzIDM0My43OTcsMTg5LjM5NEwzNDMuNzk3LDIxMS4yMjFDMzQzLjc5NywyMTEuMjIxIDM5OC4xNTgsMjExLjIyMSAzOTguMTU4LDIxMS4yMjFMMzk4LjE1OCwxODkuMzk0QzM5OC4xNTgsMTgzLjUwMyA0MDIuOTM0LDE3OC43MjggNDA4LjgyNSwxNzguNzI4TDQ1MC4xOCwxNzguNzI4QzQ1Ni4wNzEsMTc4LjcyOCA0NjAuODQ3LDE4My41MDMgNDYwLjg0NywxODkuMzk0TDQ2MC44NDcsMjExLjIyMUw0NzIuNzA0LDIxMS4yMjFDNDkyLjY1MiwyMTEuMjIxIDUwOC44NDgsMjI3LjQxNyA1MDguODQ4LDI0Ny4zNjVMNTA4Ljg0OCwyNzcuNzY1TDUwOC45MDgsMjc3Ljc2NUw1MDguOTA4LDI5OS4wOThMMjU0LjA3MSwyOTkuMDk4Wk00ODcuNTE1LDI3Ny43NjVMNDg3LjUxNSwyNDcuMzY1QzQ4Ny41MTUsMjM5LjE5MSA0ODAuODc4LDIzMi41NTQgNDcyLjcwNCwyMzIuNTU0TDQ1MC4xOCwyMzIuNTU0QzQ0NC4yODksMjMyLjU1NCA0MzkuNTEzLDIyNy43NzkgNDM5LjUxMywyMjEuODg4TDQzOS41MTMsMjAwLjA2MUM0MzkuNTEzLDIwMC4wNjEgNDE5LjQ5MSwyMDAuMDYxIDQxOS40OTEsMjAwLjA2MUw0MTkuNDkxLDIyMS44ODhDNDE5LjQ5MSwyMjcuNzc5IDQxNC43MTYsMjMyLjU1NCA0MDguODI1LDIzMi41NTRMMzMzLjEzLDIzMi41NTRDMzI3LjIzOSwyMzIuNTU0IDMyMi40NjMsMjI3Ljc3OSAzMjIuNDYzLDIyMS44ODhMMzIyLjQ2MywyMDAuMDYxQzMyMi40NjMsMjAwLjA2MSAzMDIuNDQyLDIwMC4wNjEgMzAyLjQ0MiwyMDAuMDYxTDMwMi40NDIsMjIxLjg4OEMzMDIuNDQyLDIyNy43NzkgMjk3LjY2NiwyMzIuNTU0IDI5MS43NzUsMjMyLjU1NEwyNjguODgyLDIzMi41NTRDMjYwLjcwNywyMzIuNTU0IDI1NC4wNzEsMjM5LjE5MSAyNTQuMDcxLDI0Ny4zNjVMMjU0LjA3MSwyNzcuNzY1TDQ4Ny41MTUsMjc3Ljc2NVpNMjc2LjYzNiwzNDAuMzczQzI3MC43NDksMzQwLjM3MyAyNjUuOTY5LDMzNS41OTMgMjY1Ljk2OSwzMjkuNzA2QzI2NS45NjksMzIzLjgxOSAyNzAuNzQ5LDMxOS4wNCAyNzYuNjM2LDMxOS4wNEw0NjcuOTA0LDMxOS4wNEM0NzMuNzkxLDMxOS4wNCA0NzguNTcsMzIzLjgxOSA0NzguNTcsMzI5LjcwNkM0NzguNTcsMzM1LjU5MyA0NzMuNzkxLDM0MC4zNzMgNDY3LjkwNCwzNDAuMzczTDI3Ni42MzYsMzQwLjM3M1pNMjc2LjYzNiw0NTkuNTY2QzI3MC43NDksNDU5LjU2NiAyNjUuOTY5LDQ1NC43ODYgMjY1Ljk2OSw0NDguODk5QzI2NS45NjksNDQzLjAxMiAyNzAuNzQ5LDQzOC4yMzIgMjc2LjYzNiw0MzguMjMyTDMxNy45OTEsNDM4LjIzMkMzMjMuODc4LDQzOC4yMzIgMzI4LjY1OCw0NDMuMDEyIDMyOC42NTgsNDQ4Ljg5OUMzMjguNjU4LDQ1NC43ODYgMzIzLjg3OCw0NTkuNTY2IDMxNy45OTEsNDU5LjU2NkwyNzYuNjM2LDQ1OS41NjZaTTI3Ni42MzYsMzgwLjU0OEMyNzAuNzQ5LDM4MC41NDggMjY1Ljk2OSwzNzUuNzY4IDI2NS45NjksMzY5Ljg4MUMyNjUuOTY5LDM2My45OTQgMjcwLjc0OSwzNTkuMjE0IDI3Ni42MzYsMzU5LjIxNEwzNDguMjY5LDM1OS4yMTRDMzU0LjE1NiwzNTkuMjE0IDM1OC45MzYsMzYzLjk5NCAzNTguOTM2LDM2OS44ODFDMzU4LjkzNiwzNzUuNzY4IDM1NC4xNTYsMzgwLjU0OCAzNDguMjY5LDM4MC41NDhMMjc2LjYzNiwzODAuNTQ4Wk0yNzYuNjM2LDQyMC40MjZDMjcwLjc0OSw0MjAuNDI2IDI2NS45NjksNDE1LjY0NiAyNjUuOTY5LDQwOS43NTlDMjY1Ljk2OSw0MDMuODcyIDI3MC43NDksMzk5LjA5MyAyNzYuNjM2LDM5OS4wOTNMMzg2LjY3LDM5OS4wOTNDMzkyLjU1NywzOTkuMDkzIDM5Ny4zMzcsNDAzLjg3MiAzOTcuMzM3LDQwOS43NTlDMzk3LjMzNyw0MTUuNjQ2IDM5Mi41NTcsNDIwLjQyNiAzODYuNjcsNDIwLjQyNkwyNzYuNjM2LDQyMC40MjZaTTM3OC41NDcsMzgwLjU0OEMzNzIuNjYsMzgwLjU0OCAzNjcuODgsMzc1Ljc2OCAzNjcuODgsMzY5Ljg4MUMzNjcuODgsMzYzLjk5NCAzNzIuNjYsMzU5LjIxNCAzNzguNTQ3LDM1OS4yMTRMNDM2Ljg4NywzNTkuMjE0QzQ0Mi43NzQsMzU5LjIxNCA0NDcuNTU0LDM2My45OTQgNDQ3LjU1NCwzNjkuODgxQzQ0Ny41NTQsMzc1Ljc2OCA0NDIuNzc0LDM4MC41NDggNDM2Ljg4NywzODAuNTQ4TDM3OC41NDcsMzgwLjU0OFpNNTk3LjI3OSwzMzAuNzI2TDQ5NS4xMTcsNDYxLjE2OUM0OTQuMjc0LDQ2Mi4yNDUgNDkzLjIzMSw0NjMuMTQ5IDQ5Mi4wNDcsNDYzLjgzMkw0MjQuNzI0LDUwMi42NUM0MjAuOTU2LDUwNC44MjMgNDE2LjI0NCw1MDQuNDkgNDEyLjgxOSw1MDEuODA3QzQwOS4zOTQsNDk5LjEyNSA0MDcuOTQyLDQ5NC42MzEgNDA5LjE0OCw0OTAuNDUxTDQzMC43MDMsNDE1Ljc4OEM0MzEuMDgzLDQxNC40NzQgNDMxLjcxMSw0MTMuMjQ2IDQzMi41NTQsNDEyLjE3TDUzNC43MTYsMjgxLjcyN0M1NDQuODE2LDI2OC44MzEgNTYzLjU1NCwyNjYuNTY2IDU3Ni41NDEsMjc2LjczN0w1OTIuMTAyLDI4OC45MjRDNjA1LjA4OCwyOTkuMDk1IDYwNy4zNzksMzE3LjgzIDU5Ny4yNzksMzMwLjcyNlpNNTI3LjgyOSwzMjUuMTE5TDQ1MC42MjUsNDIzLjY5NEw0MzYuOTgsNDcwLjk1OEw0NzkuNTk4LDQ0Ni4zODVMNTU2LjgwMSwzNDcuODFMNTI3LjgyOSwzMjUuMTE5Wk01NjkuOTU1LDMzMS4wMTVMNTgwLjQ4MywzMTcuNTcyQzU4My4zNDksMzEzLjkxMyA1ODIuNjMyLDMwOC42MDYgNTc4Ljk0OCwzMDUuNzJMNTYzLjM4NywyOTMuNTMyQzU1OS43MDIsMjkwLjY0NiA1NTQuMzc3LDI5MS4yMjIgNTUxLjUxMSwyOTQuODgxTDU0MC45ODMsMzA4LjMyNEw1NjkuOTU1LDMzMS4wMTVaTTE3OC4yNTIsNDE3QzE4MC40MzIsNDEyLjE1MiAxODIuMTU4LDQwNy4xMDQgMTgzLjQwMiw0MDEuOTIxTDE4NS4wOTEsMzk0Ljg4OEMxODUuMDkxLDM5NC44ODggMjA5LjM1NywzOTEuMzgxIDIwOS4zNTcsMzkxLjM4MUMyMDkuODk0LDM4NS4xMzkgMjA5Ljg5NCwzNzguODYyIDIwOS4zNTcsMzcyLjYxOUwxODUuMDkxLDM2OS4xMTJMMTgzLjQwMiwzNjIuMDc5QzE4MS4zMTIsMzUzLjM3MiAxNzcuODY0LDM0NS4wNDggMTczLjE4NSwzMzcuNDEzTDE2OS40MDYsMzMxLjI0N0MxNjkuNDA2LDMzMS4yNDcgMTg0LjA4NSwzMTEuNjA4IDE4NC4wODUsMzExLjYwOEMxODAuMDUxLDMwNi44MTQgMTc1LjYxMywzMDIuMzc1IDE3MC44MTgsMjk4LjM0MUwxNTEuMTgsMzEzLjAyTDE0NS4wMTMsMzA5LjI0MUMxMzcuMzc4LDMwNC41NjMgMTI5LjA1NCwzMDEuMTE1IDEyMC4zNDcsMjk5LjAyNEwxMTMuMzE1LDI5Ny4zMzZMMTA5LjgwOCwyNzMuMDdDMTAzLjU2NSwyNzIuNTMyIDk3LjI4OCwyNzIuNTMyIDkxLjA0NiwyNzMuMDdMODcuNTM4LDI5Ny4zMzZMODAuNTA2LDI5OS4wMjRDNzUuMzIyLDMwMC4yNjkgNzAuMjc0LDMwMS45OTUgNjUuNDI3LDMwNC4xNzVMNjUuNDI3LDI5Mi42MDhDNjkuNTIxLDI5MS4wMDQgNzMuNzI3LDI4OS42ODIgNzguMDE2LDI4OC42NTJMODEuNjU0LDI2My40NzdDOTQuMDkyLDI2MS41MDggMTA2Ljc2MiwyNjEuNTA4IDExOS4xOTksMjYzLjQ3N0wxMjIuODM3LDI4OC42NTJDMTMyLjYzMywyOTEuMDA0IDE0MS45OTcsMjk0Ljg4MyAxNTAuNTg2LDMwMC4xNDdMMTcwLjk2MSwyODQuOTE4QzE4MS4xNDgsMjkyLjMyIDE5MC4xMDcsMzAxLjI3OCAxOTcuNTA5LDMxMS40NjZMMTgyLjI4LDMzMS44NEMxODcuNTQ0LDM0MC40MjkgMTkxLjQyMiwzNDkuNzk0IDE5My43NzQsMzU5LjU4OUwyMTguOTQ5LDM2My4yMjhDMjIwLjkxOSwzNzUuNjY1IDIyMC45MTksMzg4LjMzNSAyMTguOTQ5LDQwMC43NzJMMTkzLjc3NCw0MDQuNDExQzE5Mi43NDUsNDA4LjY5OSAxOTEuNDIyLDQxMi45MDUgMTg5LjgxOSw0MTdMMTc4LjI1Miw0MTdaTTEwMC40MjcsMzQ3LjMzM0MxMTkuNTYsMzQ3LjMzMyAxMzUuMDkzLDM2Mi44NjcgMTM1LjA5MywzODJDMTM1LjA5Myw0MDEuMTMzIDExOS41Niw0MTYuNjY3IDEwMC40MjcsNDE2LjY2N0M4MS4yOTQsNDE2LjY2NyA2NS43Niw0MDEuMTMzIDY1Ljc2LDM4MkM2NS43NiwzNjIuODY3IDgxLjI5NCwzNDcuMzMzIDEwMC40MjcsMzQ3LjMzM1pNMTAwLjQyNywzNThDODcuMTgxLDM1OCA3Ni40MjcsMzY4Ljc1NCA3Ni40MjcsMzgyQzc2LjQyNywzOTUuMjQ2IDg3LjE4MSw0MDYgMTAwLjQyNyw0MDZDMTEzLjY3Myw0MDYgMTI0LjQyNywzOTUuMjQ2IDEyNC40MjcsMzgyQzEyNC40MjcsMzY4Ljc1NCAxMTMuNjczLDM1OCAxMDAuNDI3LDM1OFoiLz48L3N2Zz4=';

	public string $plugin = 'coreactivity';

	public function __construct() {
		$this->url  = COREACTIVITY_URL;
		$this->path = COREACTIVITY_PATH;

		parent::__construct();
	}

	public function s() {
		return coreactivity_settings();
	}

	public function f() {
		return null;
	}

	public function b() {
		return null;
	}

	public function l() {
		return null;
	}

	public function run() {
		do_action( 'coreactivity_load_settings' );

		LogActivity::instance();
		LogLocation::instance();
		LogMetas::instance();
		LogCore::instance();
		LogUsers::instance();

		do_action( 'coreactivity_plugin_core_ready' );

		Notifications::instance();

		add_action( 'init', array( $this, 'init' ), 100 );
		add_action( 'debugpress-tracker-plugins-call', array( $this, 'debugpress' ) );

		if ( coreactivity_settings()->get( 'admin_bar_integration' ) ) {
			AdminBar::instance();
		}
	}

	public function after_setup_theme() {
		do_action( 'coreactivity_prepare' );

		Jobs::instance();
	}

	public function debugpress() {
		if ( function_exists( 'debugpress_store_for_plugin' ) ) {
			debugpress_store_for_plugin( COREACTIVITY_FILE, array(
				'data' => array(
					'components' => Activity::instance()->get_all_components(),
					'events'     => Activity::instance()->get_all_events(),
					'statistics' => Activity::instance()->statistics,
				),
				'log'  => LogCore::instance()->get_current_page_log(),
			) );
		}
	}

	public function init() {
		do_action( 'coreactivity_init' );
	}

	public function is_logging_active() : bool {
		return coreactivity_settings()->get( 'main_events_log_switch' );
	}

	public function schedule_geo_db_update() {
		if ( ! is_main_site() ) {
			return;
		}

		if (  ! wp_next_scheduled( 'coreactivity_task_geo_db' ) ) {
			wp_schedule_single_event( time() + 5, 'coreactivity_task_geo_db' );
		}
	}

	public function uploads_path() {
		$dir = wp_upload_dir();

		if ( $dir['error'] !== false ) {
			return false;
		}

		$path = trailingslashit( $dir['basedir'] ) . 'coreactivity/';

		if ( wp_mkdir_p( $path ) ) {
			if ( wp_is_writable( $path ) ) {
				return $path;
			}
		}

		return false;
	}

	public function clean_cron_jobs() {
		if ( ! is_main_site() ) {
			$jobs = array(
				'coreactivity_task_log_purge',
				'coreactivity_task_geo_db',
				'coreactivity_task_users_meta',
				'coreactivity_daily_statistics',
				'coreactivity_daily_maintenance',
				'coreactivity_weekly_maintenance',
				'coreactivity_daily_digest',
				'coreactivity_weekly_digest',
			);

			foreach ( $jobs as $job ) {
				if ( wp_next_scheduled( $job ) ) {
					WPR::remove_cron( $job );
				}
			}
		}
	}
}
