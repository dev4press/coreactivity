<?php

use Dev4Press\Plugin\CoreActivity\Log\GEO;
use function Dev4Press\v44\Functions\panel;

$_tabs  = coreactivity_view_dialog_tabs();
$_items = $_grid->items ?? array();

function _coreactivity_dialog_tab_info( $item ) {
	$log = array(
		'log_id'      => __( "Log ID" ),
		'blog_id'     => __( "Blog ID" ),
		'ip'          => __( "IP" ),
		'component'   => __( "Component" ),
		'event'       => __( "Event" ),
		'context'     => __( "Context" ),
		'method'      => __( "Method" ),
		'protocol'    => __( "Protocol" ),
		'logged'      => __( "Date/Time" ),
		'object_type' => __( "Object" ),
		'object_id'   => __( "Object ID" ),
		'object_name' => __( "Object Name" ),
		'request'     => __( "Request" ),
	);

	echo '<div id="coreactivity-popup-data-info-' . $item->log_id . '">';
	echo '<dl class="d4p-ctrl-list">';

	foreach ( $log as $key => $label ) {
		if ( ! empty( $item->$key ) ) {
			echo '<dt>' . $label . '</dt>';
			echo '<dd>' . $item->$key . '</dd>';
		}
	}

	echo '</dl>';
	echo '<div>';

	do_action( 'coreactivity_logs_dialog_info', $item );

	echo '</div>';
	echo '</div>';
}

function _coreactivity_dialog_tab_meta( $item ) {
	echo '<div id="coreactivity-popup-data-meta-' . $item->log_id . '">';
	echo '<dl class="d4p-ctrl-list">';

	if ( isset( $item->meta ) ) {
		foreach ( $item->meta as $key => $value ) {
			if ( $key != 'geo_location' ) {
				$value = is_scalar( $value ) ? esc_html( $value ) : ( is_array( $value ) && count( $value ) < 20 ? coreactivity_print_array( $value ) : json_encode( $value ) );

				echo '<dt>' . $key . '</dt>';
				echo '<dd>' . $value . '</dd>';
			}
		}
	}

	echo '</dl>';
	echo '<div>';

	do_action( 'coreactivity_logs_dialog_meta', $item );

	echo '</div>';
	echo '</div>';
}

function _coreactivity_dialog_tab_location( $item ) {
	echo '<div id="coreactivity-popup-data-location-' . $item->log_id . '">';
	if ( ! empty( $item->country_code ) ) {
		echo '<dl class="d4p-ctrl-list">';
		echo '<dt>' . __( "Country Code" ) . '</dt>';
		echo '<dd>' . $item->country_code . '</dd>';
		echo '<dt>' . __( "Country" ) . '</dt>';
		echo '<dd>' . GEO::instance()->country( $item->country_code ) . '</dd>';
		echo '<dt>' . __( "Continent" ) . '</dt>';
		echo '<dd>' . GEO::instance()->continent( $item->country_code ) . '</dd>';
		echo '</dl>';
	}

	if ( isset( $item->meta['geo_location'] ) ) {
		echo '<dl class="d4p-ctrl-list">';
		foreach ( $item->meta['geo_location'] as $key => $value ) {
			echo '<dt>' . $key . '</dt>';
			echo '<dd>' . $value . '</dd>';
		}
		echo '</dl>';
	}

	if ( ! empty( $item->country_code ) ) {
		echo '<div>';

		do_action( 'coreactivity_logs_dialog_location', $item );

		echo '</div>';
	}
	echo '</div>';
}

?>
<div class="coreactivity-dialog-data" style="display: none;">
	<?php

	foreach ( $_items as $item ) {
		foreach ( array_keys( $_tabs ) as $tab ) {
			$fnc = apply_filters( 'coreactivity_dialog_item_data_callback_' . $tab, '_coreactivity_dialog_tab_' . $tab );

			if ( is_callable( $fnc ) ) {
				call_user_func( $fnc, $item );
			}
		}
	}

	?>
</div>
