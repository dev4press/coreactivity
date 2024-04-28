<?php

use Dev4Press\Plugin\CoreActivity\Admin\Data;
use function Dev4Press\v48\Functions\panel;

$methods = Data::get_geo_location_methods();
$method  = coreactivity_settings()->get( 'geolocation_method' );
$label   = $methods[ $method ] ?? __( 'Unknown', 'coreactivity' );

?>
<div class="d4p-group d4p-dashboard-card d4p-card-double d4p-dashboard-status d4p-dashboard-card-no-footer">
    <h3><?php esc_html_e( 'GEO Location', 'coreactivity' ); ?></h3>
    <div class="d4p-group-header">
        <div>
            <span class="d4p-card-badge d4p-badge-ok"><i class="d4p-icon d4p-ui-globe d4p-icon-fw"></i> <?php echo esc_html( $label ); ?></span>
            <div class="d4p-status-message"><?php esc_html_e( 'GEO Location Method', 'coreactivity' ); ?></div>
        </div>
    </div>
	<?php if ( $method === 'geoip2' ) { ?>
        <div class="d4p-group-inner">
            <p>
                <strong><?php esc_html_e( 'Database Path', 'coreactivity' ); ?></strong>:<br/>
                <code><?php echo esc_html( coreactivity_settings()->get( 'geoip2_db', 'core' ) ); ?></code>
                <br/>
                <strong><?php esc_html_e( 'Database Downloaded', 'coreactivity' ); ?></strong>:<br/>
                <code><?php echo esc_html( coreactivity()->datetime()->mysql_date( true, coreactivity_settings()->get( 'geoip2_timestamp', 'core' ) ) ); ?></code>
                <br/>
				<?php

				$_error = coreactivity_settings()->get( 'geoip2_error', 'core' );

				if ( ! empty( $_error ) ) {
					?>

                    <strong><?php esc_html_e( 'Download Error', 'coreactivity' ); ?></strong>:<br/>
                    <code><?php echo esc_html( coreactivity()->datetime()->mysql_date( true, coreactivity_settings()->get( 'geoip2_attempt', 'core' ) ) ); ?>: <?php echo esc_html( $_error ); ?></code>
                    <br/>

					<?php
				}

				?>
                <strong><?php esc_html_e( 'About the Database', 'coreactivity' ); ?></strong>:<br/>
                This product includes GeoLite2 data created by MaxMind, available from <a href="https://www.maxmind.com">https://www.maxmind.com</a>.
            </p>
        </div>
	<?php } else if ( $method === 'ip2location' ) { ?>
        <div class="d4p-group-inner">
            <p>
                <strong><?php esc_html_e( 'Database Path', 'coreactivity' ); ?></strong>:<br/>
                <code><?php echo esc_html( coreactivity_settings()->get( 'ip2location_db', 'core' ) ); ?></code>
                <br/>
                <strong><?php esc_html_e( 'Database Downloaded', 'coreactivity' ); ?></strong>:<br/>
                <code><?php echo esc_html( coreactivity()->datetime()->mysql_date( true, coreactivity_settings()->get( 'ip2location_timestamp', 'core' ) ) ); ?></code>
                <br/>
				<?php

				$_error = coreactivity_settings()->get( 'ip2location_error', 'core' );

				if ( ! empty( $_error ) ) {
					?>

                    <strong><?php esc_html_e( 'Download Error', 'coreactivity' ); ?></strong>:<br/>
                    <code><?php echo esc_html( coreactivity()->datetime()->mysql_date( true, coreactivity_settings()->get( 'ip2location_attempt', 'core' ) ) ); ?>: <?php echo esc_html( $_error ); ?></code>
                    <br/>

					<?php
				}

				?>
                <strong><?php esc_html_e( 'About the Database', 'coreactivity' ); ?></strong>:<br/>
                This site or product includes IP2Location LITE data available from <a href="https://lite.ip2location.com">https://lite.ip2location.com</a>.
            </p>
        </div>
	<?php } ?>
    <div class="d4p-group-footer">
        <a class="button-primary" href="<?php echo esc_url( panel()->a()->panel_url( 'settings', 'geo' ) ); ?>"><?php esc_html_e( 'GEO Location Settings', 'coreactivity' ); ?></a>
        <a class="button-secondary" href="<?php echo esc_url( panel()->a()->panel_url( 'settings', 'optional' ) ); ?>"><?php esc_html_e( 'IP Location Data Logging', 'coreactivity' ); ?></a>
    </div>
</div>
