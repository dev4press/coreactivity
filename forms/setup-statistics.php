<?php

use Dev4Press\Plugin\CoreActivity\Log\Statistics;

$statistics = coreactivity_settings()->get( 'statistics', 'storage' );

if ( empty( $statistics ) ) {
	Statistics::instance()->initial_update();
}
