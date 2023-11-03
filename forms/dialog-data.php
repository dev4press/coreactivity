<?php

use Dev4Press\Plugin\CoreActivity\Log\GEO;

?>
<div class="coreactivity-dialog-data" style="display: none;">
	<?php

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

	foreach ( $_grid->items as $item ) {
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

		do_action( 'coreactivity-logs-dialog-info', $item );

		echo '</div>';
		echo '</div>';

		echo '<div id="coreactivity-popup-data-meta-' . $item->log_id . '">';
		echo '<dl class="d4p-ctrl-list">';

		foreach ( $item->meta as $key => $value ) {
			if ( $key != 'geo_location' ) {
				$value = is_scalar( $value ) ? esc_html( $value ) : ( is_array( $value ) && count( $value ) < 20 ? $_grid->print_array( $value ) : json_encode( $value ) );

				echo '<dt>' . $key . '</dt>';
				echo '<dd>' . $value . '</dd>';
			}
		}

		echo '</dl>';
		echo '<div>';

		do_action( 'coreactivity-logs-dialog-meta', $item );

		echo '</div>';
		echo '</div>';

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

			do_action( 'coreactivity-logs-dialog-location', $item );

			echo '</div>';
		}
		echo '</div>';
	}

	?>
</div>
