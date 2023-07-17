<?php

use Dev4Press\Plugin\CoreActivity\Log\Core;
use Dev4Press\v43\Service\GEOIP\GEOJSIO;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enable or disable main CoreActivity logging switch.
 *
 * @param bool $status TRUE will enable logging and FALSE will disable logging.
 *
 * @return void
 */
function coreactivity_change_logging_status( bool $status ) {
	coreactivity_settings()->set( 'main_events_log_switch', $status, 'settings', true );
}
