<?php

use Dev4Press\Plugin\CoreActivity\Admin\Data;
use function Dev4Press\v44\Functions\panel;

$methods = Data::get_geo_location_methods();
$method = coreactivity_settings()->get( 'geolocation_method' );
$label   = $methods[ $method ] ?? __( "Unknown" );

?>
<div class="d4p-group d4p-dashboard-card d4p-card-double d4p-dashboard-status d4p-dashboard-card-no-footer">
    <h3><?php esc_html_e( 'GEO Location', 'coreactivity' ); ?></h3>
    <div class="d4p-group-header">
        <div>
            <span class="d4p-card-badge d4p-badge-ok"><i class="d4p-icon d4p-ui-globe d4p-icon-fw"></i> <?php echo $label; ?></span>
            <div class="d4p-status-message"><?php esc_html_e("GEO Location Method"); ?></div>
        </div>
    </div>
    <?php if ($method === 'geoip2') { ?>
    <div class="d4p-group-inner">
        TEXT
    </div>
    <?php } else { ?>
    <div class="d4p-group-inner">
        TEXT
    </div>
	<?php } ?>
    <div class="d4p-group-footer">
        <a class="button-primary" href="<?php echo esc_url( panel()->a()->panel_url( 'settings', 'geo-locarion' ) ); ?>"><?php esc_html_e("GEO Location Settings"); ?></a>
    </div>
</div>
