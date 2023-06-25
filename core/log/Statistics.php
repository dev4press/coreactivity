<?php

namespace Dev4Press\Plugin\CoreActivity\Log;

use Dev4Press\Plugin\CoreActivity\Basic\DB;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Statistics {
	public function __construct() {

	}

	public static function instance() : Statistics {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Statistics();
		}

		return $instance;
	}

	public function overall() {
		$stats = Init::instance()->statistics;

		return $stats;
	}

	public function detailed( int $days = 30, int $blog_id = - 1 ) : array {
		$counts  = DB::instance()->statistics_components_log( $days, $blog_id );
		$results = array(
			'total'      => 0,
			'components' => array()
		);

		foreach ( $counts as $component => $count ) {
			$results[ 'total' ]                    += $count;
			$results[ 'components' ][ $component ] = array(
				'label' => Init::instance()->get_component_label( $component ),
				'icon'  => Init::instance()->get_component_icon( $component ),
				'count' => $count
			);
		}

		return $results;
	}
}
