<?php

namespace Dev4Press\Plugin\CoreActivity\Plugins;

use Dev4Press\Plugin\CoreActivity\Base\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SweepPress extends Plugin {
	protected $plugin = 'coreactivity';
	protected $name = 'sweeppress';
	protected $icon = 'plugin-sweeppress';
	protected $plugin_file = 'sweeppress/sweeppress.php';

	public function tracking() {
		if ( $this->is_active( 'completed' ) ) {
			add_action( 'sweeppress_sweep_completed', array( $this, 'event_completed' ) );
		}

		if ( $this->is_active( 'options-deleted' ) ) {
			add_action( 'sweeppress_options_deleted', array( $this, 'event_options_deleted' ) );
		}

		if ( $this->is_active( 'sitemeta-deleted' ) ) {
			add_action( 'sweeppress_sitemetas_deleted', array( $this, 'event_sitemetas_deleted' ) );
		}

		if ( $this->is_active( 'postmeta-deleted' ) ) {
			add_action( 'sweeppress_postmeta_deleted', array( $this, 'event_postmeta_deleted' ) );
		}

		if ( $this->is_active( 'termmeta-deleted' ) ) {
			add_action( 'sweeppress_termmeta_deleted', array( $this, 'event_termmeta_deleted' ) );
		}

		if ( $this->is_active( 'commentmeta-deleted' ) ) {
			add_action( 'sweeppress_commentmeta_deleted', array( $this, 'event_commentmeta_deleted' ) );
		}

		if ( $this->is_active( 'usermeta-deleted' ) ) {
			add_action( 'sweeppress_usermeta_deleted', array( $this, 'event_usermeta_deleted' ) );
		}

		if ( $this->is_active( 'blogmeta-deleted' ) ) {
			add_action( 'sweeppress_blogmeta_deleted', array( $this, 'event_blogmeta_deleted' ) );
		}

		if ( $this->is_active( 'logmeta-deleted' ) ) {
			add_action( 'sweeppress_logmeta_deleted', array( $this, 'event_logmeta_deleted' ) );
		}

		if ( $this->is_active( 'cron-run' ) ) {
			add_action( 'sweeppress_cron_run_job', array( $this, 'event_cron_job_run' ) );
		}

		if ( $this->is_active( 'cron-deleted' ) ) {
			add_action( 'sweeppress_cron_deleted_job', array( $this, 'event_cron_deleted_run' ) );
		}
	}

	public function label() : string {
		return __( 'SweepPress', 'coreactivity' );
	}

	protected function get_events() : array {
		return array(
			'completed'           => array(
				'label' => __( 'Sweeping Completed', 'coreactivity' ),
			),
			'options-deleted'     => array(
				'label' => __( 'Options Deleted', 'coreactivity' ),
			),
			'sitemeta-deleted'    => array(
				'label' => __( 'Sitemeta Deleted', 'coreactivity' ),
			),
			'postmeta-deleted'    => array(
				'label' => __( 'Postmeta Deleted', 'coreactivity' ),
			),
			'termmeta-deleted'    => array(
				'label' => __( 'Termmeta Deleted', 'coreactivity' ),
			),
			'commentmeta-deleted' => array(
				'label' => __( 'Commentmeta Deleted', 'coreactivity' ),
			),
			'usermeta-deleted'    => array(
				'label' => __( 'Usermeta Deleted', 'coreactivity' ),
			),
			'blogmeta-deleted'    => array(
				'label' => __( 'Blogmeta Deleted', 'coreactivity' ),
			),
			'logmeta-deleted'     => array(
				'label' => __( 'Logmeta Deleted', 'coreactivity' ),
			),
			'cron-run'            => array(
				'label' => __( 'Run CRON job', 'coreactivity' ),
			),
			'cron-deleted'        => array(
				'label' => __( 'Deleted CRON job', 'coreactivity' ),
			),
		);
	}

	public function event_completed( $results ) {
		$this->log( 'completed', array(), array(
			'source'   => $results['stats']['source'],
			'time'     => $results['stats']['time'],
			'records'  => $results['stats']['records'],
			'size'     => $results['stats']['size'],
			'sweepers' => array_keys( $results['sweepers'] ),
		) );
	}

	public function event_options_deleted( $results ) {
		$this->log( 'options-deleted', array(), $results );
	}

	public function event_sitemetas_deleted( $results ) {
		$this->log( 'sitemeta-deleted', array(), $results );
	}

	public function event_postmeta_deleted( $results ) {
		$this->log( 'postmeta-deleted', array(), $results );
	}

	public function event_termmeta_deleted( $results ) {
		$this->log( 'termmeta-deleted', array(), $results );
	}

	public function event_commentmeta_deleted( $results ) {
		$this->log( 'commentmeta-deleted', array(), $results );
	}

	public function event_usermeta_deleted( $results ) {
		$this->log( 'usermeta-deleted', array(), $results );
	}

	public function event_blogmeta_deleted( $results ) {
		$this->log( 'blogmeta-deleted', array(), $results );
	}

	public function event_logmeta_deleted( $results ) {
		$this->log( 'logmeta-deleted', array(), $results );
	}

	public function event_cron_job_run( $job ) {
		$this->log( 'cron-run', array(
			'object_type' => 'cron',
			'object_name' => $job,
		) );
	}

	public function event_cron_deleted_run( $job ) {
		$this->log( 'cron-deleted', array(
			'object_type' => 'cron',
			'object_name' => $job,
		) );
	}
}
