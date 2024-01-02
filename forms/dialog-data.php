<?php

use Dev4Press\Plugin\CoreActivity\Log\GEO;

$_tabs  = coreactivity_view_dialog_tabs();
$_items = $_grid->items ?? array();

function _coreactivity_dialog_tab_info( $item ) {
	$log = array(
		'log_id'      => __( 'Log ID', 'coreactivity' ),
		'blog_id'     => __( 'Blog ID', 'coreactivity' ),
		'ip'          => __( 'IP', 'coreactivity' ),
		'component'   => __( 'Component', 'coreactivity' ),
		'event'       => __( 'Event', 'coreactivity' ),
		'context'     => __( 'Context', 'coreactivity' ),
		'method'      => __( 'Method', 'coreactivity' ),
		'protocol'    => __( 'Protocol', 'coreactivity' ),
		'logged'      => __( 'Date/Time', 'coreactivity' ),
		'object_type' => __( 'Object', 'coreactivity' ),
		'object_id'   => __( 'Object ID', 'coreactivity' ),
		'object_name' => __( 'Object Name', 'coreactivity' ),
		'request'     => __( 'Request', 'coreactivity' ),
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
				if ( is_scalar( $value ) ) {
					$display = esc_html( $value );
				} else {
					$display = is_array( $value ) && count( $value ) < 9 ? coreactivity_print_array( $value ) : esc_html( wp_json_encode( $value ) );
				}

				echo '<dt>' . $key . '</dt>';
				echo '<dd>' . $display . '</dd>';
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
		echo '<dt>' . __( 'Country Code', 'coreactivity' ) . '</dt>';
		echo '<dd>' . $item->country_code . '</dd>';
		echo '<dt>' . __( 'Country', 'coreactivity' ) . '</dt>';
		echo '<dd>' . GEO::instance()->country( $item->country_code ) . '</dd>';
		echo '<dt>' . __( 'Continent', 'coreactivity' ) . '</dt>';
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

function _coreactivity_dialog_tab_device( $item ) {
	echo '<div id="coreactivity-popup-data-device-' . $item->log_id . '">';

	if ( isset( $item->device ) ) {
		echo '<dl class="d4p-ctrl-list">';
		echo '<dt>' . __( 'Bot', 'coreactivity' ) . '</dt>';
		echo '<dd>' . ( isset( $item->device['bot'] ) ? __( 'Yes' ) : __( 'No' ) ) . '</dd>';

		if ( isset( $item->device['bot'] ) ) {
			echo '<dt>' . __( 'Category', 'coreactivity' ) . '</dt>';
			echo '<dd>' . $item->device['bot']['category'] . '</dd>';
			echo '<dt>' . __( 'Name', 'coreactivity' ) . '</dt>';
			echo '<dd>' . $item->device['bot']['name'] . '</dd>';
		} else {
			if ( ! empty( $item->device['os']['name'] ) ) {
				echo '<dt>' . __( 'OS', 'coreactivity' ) . '</dt>';
				echo '<dd>' . ( $item->device['os']['name'] ?? '' ) . ' ' . ( $item->device['os']['version'] ?? '' ) . '</dd>';
			}

			if ( ! empty( $item->device['client']['name'] ) ) {
				echo '<dt>' . __( 'Client', 'coreactivity' ) . '</dt>';
				echo '<dd>' . ( $item->device['client']['name'] ?? '' ) . ' ' . ( $item->device['client']['version'] ?? '' ) . '</dd>';
			}

			echo '<dt>' . __( 'Device', 'coreactivity' ) . '</dt>';
			echo '<dd>' . ucwords( $item->device['device'] ?? __( 'Unknown' ) ) . '</dd>';

			if ( ! empty( $item->device['brand'] ) ) {
				echo '<dt>' . __( 'Brand', 'coreactivity' ) . '</dt>';
				echo '<dd>' . $item->device['brand'] . '</dd>';
			}

			if ( ! empty( $item->device['model'] ) ) {
				echo '<dt>' . __( 'Model', 'coreactivity' ) . '</dt>';
				echo '<dd>' . $item->device['model'] . '</dd>';
			}
		}

		echo '</dl>';
	}

	echo '<div>';

	do_action( 'coreactivity_logs_dialog_device', $item );

	echo '</div>';

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
