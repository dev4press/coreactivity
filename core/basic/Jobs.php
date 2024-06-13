<?php

namespace Dev4Press\Plugin\CoreActivity\Basic;

use Dev4Press\Plugin\CoreActivity\Log\Cleanup;
use Dev4Press\Plugin\CoreActivity\Log\GEO as LogLocation;
use Dev4Press\Plugin\CoreActivity\Log\Notifications;
use Dev4Press\Plugin\CoreActivity\Log\Statistics;
use Dev4Press\v49\Core\Quick\WPR;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Jobs {
	public function __construct() {
		add_action( 'coreactivity_instant_notification', array( $this, 'instant_notification' ) );

		if ( is_main_site() ) {
			add_action( 'coreactivity_daily_maintenance', array( $this, 'daily_maintenance' ) );
			add_action( 'coreactivity_daily_digest', array( $this, 'daily_digest' ) );
			add_action( 'coreactivity_daily_statistics', array( $this, 'daily_statistics' ) );
			add_action( 'coreactivity_weekly_digest', array( $this, 'weekly_digest' ) );
			add_action( 'coreactivity_weekly_maintenance', array( $this, 'weekly_maintenance' ) );
			add_action( 'coreactivity_task_log_purge', array( $this, 'task_log_purge' ) );
			add_action( 'coreactivity_task_geo_db', array( $this, 'task_geo_db_update' ) );
			add_action( 'coreactivity_task_users_meta', array( $this, 'task_users_meta' ) );

			$this->jobs_scheduler_init();
		}
	}

	public static function instance() : Jobs {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Jobs();
		}

		return $instance;
	}

	public function jobs_scheduler_init() {
		if ( ! wp_next_scheduled( 'coreactivity_task_log_purge' ) ) {
			if ( coreactivity()->s()->get( 'auto_cleanup_active' ) ) {
				$cron_time = mktime( 3, 35, 0, gmdate( 'm' ), gmdate( 'd' ), gmdate( 'Y' ) );

				wp_schedule_event( $cron_time, 'daily', 'coreactivity_task_log_purge' );
			}
		} else {
			if ( ! coreactivity()->s()->get( 'auto_cleanup_active' ) ) {
				WPR::remove_cron( 'coreactivity_task_log_purge' );
			}
		}

		if ( ! wp_next_scheduled( 'coreactivity_daily_statistics' ) ) {
			$cron_time = strtotime( 'tomorrow' ) + 5 * HOUR_IN_SECONDS;

			wp_schedule_event( $cron_time, 'daily', 'coreactivity_daily_statistics' );
		}

		if ( ! wp_next_scheduled( 'coreactivity_daily_maintenance' ) ) {
			$cron_time = mktime( 3, 5, 0, gmdate( 'm' ), gmdate( 'd' ), gmdate( 'Y' ) );

			wp_schedule_event( $cron_time, 'daily', 'coreactivity_daily_maintenance' );
		}

		if ( ! wp_next_scheduled( 'coreactivity_weekly_maintenance' ) ) {
			$cron_time = mktime( 4, 5, 0, gmdate( 'm' ), gmdate( 'd' ), gmdate( 'Y' ) );

			wp_schedule_event( $cron_time, 'weekly', 'coreactivity_weekly_maintenance' );
		}

		Notifications::instance()->schedule_digests();
	}

	public function instant_notification() {
		Notifications::instance()->scheduled_instant();
	}

	public function daily_maintenance() {
		coreactivity()->clean_cron_jobs();

		$this->task_users_meta();
	}

	public function daily_digest() {
		Notifications::instance()->scheduled_daily();
	}

	public function daily_statistics() {
		Statistics::instance()->daily_update();
	}

	public function weekly_digest() {
		coreactivity()->clean_cron_jobs();

		Notifications::instance()->scheduled_weekly();
	}

	public function weekly_maintenance() {
		$this->task_geo_db_update();
	}

	public function task_log_purge() {
		Cleanup::instance()->auto_cleanup_log();
	}

	public function task_geo_db_update() {
		if ( coreactivity()->s()->get( 'geolocation_method' ) == 'ip2location' ) {
			LogLocation::instance()->ip2location_db_update();
		}

		if ( coreactivity()->s()->get( 'geolocation_method' ) == 'geoip2' ) {
			LogLocation::instance()->geoip2_db_update();
		}
	}

	public function task_users_meta() {
		$ids = DB::instance()->get_users_without_activity_keys();

		foreach ( $ids as $id ) {
			$id = absint( $id );

			if ( $id > 0 ) {
				update_user_option( $id, 'coreactivity_last_activity', 0, true );
				update_user_option( $id, 'coreactivity_last_login', 0, true );
				update_user_option( $id, 'coreactivity_last_logout', 0, true );
			}
		}

		if ( count( $ids ) == 500 ) {
			if ( ! wp_next_scheduled( 'coreactivity_task_users_meta' ) ) {
				wp_schedule_single_event( time() + 5, 'coreactivity_task_users_meta' );
			}
		}
	}
}
