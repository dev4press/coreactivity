<?php

use Dev4Press\Plugin\CoreActivity\Log\Statistics;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$statistics = coreactivity_settings()->get( 'statistics', 'storage' );

if ( empty( $statistics ) ) {
	Statistics::instance()->initial_update();
}
