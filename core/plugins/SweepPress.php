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
			'completed'    => array(
				'label' => __( 'Sweeping Completed', 'coreactivity' ),
			),
			'cron-run'     => array(
				'label' => __( 'Run CRON job', 'coreactivity' ),
			),
			'cron-deleted' => array(
				'label' => __( 'Deleted CRON job', 'coreactivity' ),
			),
		);
	}

	public function event_completed( $results ) {
		$this->log( 'completed', array(), array(
			'source'   => $results['stats']['source'],
			'sweepers' => array_keys( $results['sweepers'] ),
		) );
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
