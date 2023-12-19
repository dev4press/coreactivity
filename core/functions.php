<?php

use Dev4Press\Plugin\CoreActivity\Basic\DB;
use Dev4Press\Plugin\CoreActivity\Log\Activity;

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

/**
 * Change the status of the event. It will activate or deactivate the event.
 *
 * @param string $component name of the component
 * @param string $event     name of the event
 * @param string $status    status for the event ('active', 'inactive')
 *
 * @return bool TRUE if the status has been changed, FALSE if the event is not found or status is not valid.
 */
function coreactivity_change_event_status( string $component, string $event, string $status ) : bool {
	if ( ! in_array( $status, array( 'active', 'inactive' ) ) ) {
		return false;
	}

	$event_id = Activity::instance()->get_event_id( $component, $event );

	if ( $event_id == 0 ) {
		return false;
	}

	DB::instance()->change_event_status( $event_id, $status );

	return true;
}

function coreactivity_print_array( $input ) : string {
	$render = array();

	foreach ( $input as $key => $value ) {
		$render[] = $key . ': ' . esc_html( is_scalar( $value ) ? $value : esc_html( wp_json_encode( $value ) ) );
	}

	return join( '<br/>', $render );
}

function coreactivity_view_dialog_tabs() : array {
	return apply_filters( 'coreactivity_log_dialog_tabs', array(
		'info'     => array(
			'label' => __( 'Data', 'coreactivity' ),
			'icon'  => 'ui-calendar-pen',
		),
		'meta'     => array(
			'label' => __( 'Meta', 'coreactivity' ),
			'icon'  => 'ui-newspaper',
		),
		'location' => array(
			'label' => __( 'Location', 'coreactivity' ),
			'icon'  => 'ui-location-map',
		),
	) );
}