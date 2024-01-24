<?php

use Dev4Press\Plugin\CoreActivity\Log\Core;
use Dev4Press\Plugin\CoreActivity\Log\GEO;

$_tabs  = coreactivity_view_dialog_tabs();
$_items = $_grid->items ?? array();

function _coreactivity_dialog_tab_info( $item ) {
	$log = array(
		'log_id'      => __( 'Log ID', 'coreactivity' ),
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

	echo '<div id="coreactivity-popup-data-info-' . esc_attr( $item->log_id ) . '">';
	echo '<dl class="d4p-ctrl-list">';

	foreach ( $log as $key => $label ) {
		if ( ! empty( $item->$key ) ) {
			echo '<dt>' . esc_html( $label ) . '</dt>';
			echo '<dd>' . esc_html( $item->$key ) . '</dd>';
		}
	}

	echo '</dl>';

	if ( Core::instance()->show_blog_data() ) {
		$blog = get_blog_details( array( 'blog_id' => $item->blog_id ) );

		echo '<dl class="d4p-ctrl-list">';
		echo '<dt>' . esc_html__( 'Blog ID', 'coreactivity' ) . '</dt><dd>' . esc_html( $item->blog_id ) . '</dd>';

		if ( $blog instanceof WP_Site ) {
			echo '<dt>' . esc_html__( 'Blog Name', 'coreactivity' ) . '</dt><dd>' . esc_html( $blog->blogname ) . '</dd>';
			echo '<dt>' . esc_html__( 'Blog URL', 'coreactivity' ) . '</dt><dd>' . esc_url( $blog->siteurl ) . '</dd>';
		}

		echo '</dl>';
	}

	echo '<div class="coreactivity-popup-tab-actions">';

	do_action( 'coreactivity_logs_dialog_info', $item );

	echo '</div>';
	echo '</div>';
}

function _coreactivity_dialog_tab_meta( $item ) {
	echo '<div id="coreactivity-popup-data-meta-' . esc_attr( $item->log_id ) . '">';
	echo '<dl class="d4p-ctrl-list">';

	if ( isset( $item->meta ) ) {
		foreach ( $item->meta as $key => $value ) {
			if ( $key != 'geo_location' ) {
				if ( is_scalar( $value ) ) {
					$display = esc_html( $value );
				} else {
					$display = is_array( $value ) && count( $value ) < 9 ? coreactivity_print_array( $value ) : esc_html( wp_json_encode( $value ) );
				}

				echo '<dt>' . esc_html( $key ) . '</dt>';
				echo '<dd>' . $display . '</dd>'; // phpcs:ignore WordPress.Security.EscapeOutput
			}
		}
	}

	echo '</dl>';
	echo '<div class="coreactivity-popup-tab-actions">';

	do_action( 'coreactivity_logs_dialog_meta', $item );

	echo '</div>';
	echo '</div>';
}

function _coreactivity_dialog_tab_location( $item ) {
	echo '<div id="coreactivity-popup-data-location-' . esc_attr( $item->log_id ) . '">';
	if ( ! empty( $item->country_code ) ) {
		echo '<dl class="d4p-ctrl-list">';
		if ( $item->country_code !== 'XX' ) {
			echo '<dt>' . esc_html__( 'Country Code', 'coreactivity' ) . '</dt>';
			echo '<dd>' . esc_html( $item->country_code ) . '</dd>';
			echo '<dt>' . esc_html__( 'Country', 'coreactivity' ) . '</dt>';
			echo '<dd>' . esc_html( GEO::instance()->country( $item->country_code ) ) . '</dd>';
			echo '<dt>' . esc_html__( 'Continent', 'coreactivity' ) . '</dt>';
			echo '<dd>' . esc_html( GEO::instance()->continent( $item->country_code ) ) . '</dd>';
		} else {
			echo '<dt>' . esc_html__( 'Unknown', 'coreactivity' ) . '</dt>';
			echo '<dd>' . esc_html__( 'Localhost IP', 'coreactivity' ) . '</dd>';
		}
		echo '</dl>';
	}

	if ( isset( $item->meta['geo_location'] ) ) {
		echo '<dl class="d4p-ctrl-list">';
		foreach ( $item->meta['geo_location'] as $key => $value ) {
			echo '<dt>' . esc_html( $key ) . '</dt>';
			echo '<dd>' . esc_html( $value ) . '</dd>';
		}
		echo '</dl>';
	}

	if ( ! empty( $item->country_code ) ) {
		echo '<div class="coreactivity-popup-tab-actions">';

		do_action( 'coreactivity_logs_dialog_location', $item );

		echo '</div>';
	}
	echo '</div>';
}

function _coreactivity_dialog_tab_device( $item ) {
	echo '<div id="coreactivity-popup-data-device-' . esc_attr( $item->log_id ) . '">';

	if ( isset( $item->device ) ) {
		echo '<dl class="d4p-ctrl-list">';
		echo '<dt>' . esc_html__( 'Bot', 'coreactivity' ) . '</dt>';
		echo '<dd>' . ( isset( $item->device['bot'] ) ? esc_html__( 'Yes', 'coreactivity' ) : esc_html__( 'No', 'coreactivity' ) ) . '</dd>';

		if ( isset( $item->device['bot'] ) ) {
			if ( ! empty( $item->device['bot']['category'] ) ) {
				echo '<dt>' . esc_html__( 'Category', 'coreactivity' ) . '</dt>';
				echo '<dd>' . esc_html( $item->device['bot']['category'] ) . '</dd>';
			}
			echo '<dt>' . esc_html__( 'Name', 'coreactivity' ) . '</dt>';
			echo '<dd>' . esc_html( $item->device['bot']['name'] ) . '</dd>';
		} else {
			if ( ! empty( $item->device['os']['name'] ) ) {
				echo '<dt>' . esc_html__( 'OS', 'coreactivity' ) . '</dt>';
				echo '<dd>' . esc_html( ( $item->device['os']['name'] ?? '' ) . ' ' . ( $item->device['os']['version'] ?? '' ) ) . '</dd>';
			}

			if ( ! empty( $item->device['client']['name'] ) ) {
				echo '<dt>' . esc_html__( 'Client', 'coreactivity' ) . '</dt>';
				echo '<dd>' . esc_html( ( $item->device['client']['name'] ?? '' ) . ' ' . ( $item->device['client']['version'] ?? '' ) ) . '</dd>';
			}

			echo '<dt>' . esc_html__( 'Device', 'coreactivity' ) . '</dt>';
			echo '<dd>' . esc_attr( ucwords( $item->device['device'] ?? __( 'Unknown', 'coreactivity' ) ) ) . '</dd>';

			if ( ! empty( $item->device['brand'] ) ) {
				echo '<dt>' . esc_html__( 'Brand', 'coreactivity' ) . '</dt>';
				echo '<dd>' . esc_html( $item->device['brand'] ) . '</dd>';
			}

			if ( ! empty( $item->device['model'] ) ) {
				echo '<dt>' . esc_html__( 'Model', 'coreactivity' ) . '</dt>';
				echo '<dd>' . esc_html( $item->device['model'] ) . '</dd>';
			}
		}

		echo '</dl>';
	}

	echo '<div class="coreactivity-popup-tab-actions">';

	do_action( 'coreactivity_logs_dialog_device', $item );

	echo '</div>';

	echo '</div>';
}

?>
<div class="coreactivity-dialog-data" style="display: none;">
	<?php

	foreach ( $_items as $item ) {
		foreach ( array_keys( $_tabs ) as $_tab ) {
			$fnc = apply_filters( 'coreactivity_dialog_item_data_callback_' . $_tab, '_coreactivity_dialog_tab_' . $_tab );

			if ( is_callable( $fnc ) ) {
				call_user_func( $fnc, $item );
			}
		}
	}

	?>
</div>
