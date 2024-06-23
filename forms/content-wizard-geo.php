<?php

use function Dev4Press\v49\Functions\panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="d4p-wizard-panel-header">
    <p>
		<?php esc_html_e( 'Main Logs panel has a lot of moving parts and elements, and some of things may be useful to have depending on what your preferences are.', 'coreactivity' ); ?>
    </p>
</div>

<div class="d4p-wizard-panel-content">
    <div class="d4p-wizard-option-block d4p-wizard-block-yesno">
        <p><?php esc_html_e( 'Do you want to store country code for each activity logged?', 'coreactivity' ); ?></p>
        <div>
            <em><?php esc_html_e( 'This will improve speed for geo location later on, and will add additional information layer for requests activity analysis, and can be beneficial to other plugins related to the coreActivity.', 'coreactivity' ); ?></em>
			<?php coreactivity_wizard()->render_yes_no( 'geo', 'country' ); ?>
        </div>
    </div>
    <div class="d4p-wizard-option-block d4p-wizard-block-yesno">
        <p><?php esc_html_e( 'Do you want to store extended geo location data?', 'coreactivity' ); ?></p>
        <div>
            <em><?php esc_html_e( 'All retrieved geo-location data can be stored in the log as a meta data. Right now, it is not used much, but it can be useful to view the IP information at the time it was logged.', 'coreactivity' ); ?></em>
			<?php coreactivity_wizard()->render_yes_no( 'geo', 'expanded', 'no' ); ?>
        </div>
    </div>
    <div class="d4p-wizard-option-block">
        <p><?php esc_html_e( 'Setting up the geo location feature', 'coreactivity' ); ?></p>
        <div>
            <em><?php esc_html_e( 'Plugin can geo-locate IPs using Online service, and it has support for using geo-location databases. Both supported databases require registration to get token or key used to download the database, and both services are free. Check out the dedicated settings panel to configure this further.', 'coreactivity' ); ?></em>
            <a target="_blank" class="button-secondary" href="<?php echo panel()->a()->panel_url( 'settings', 'geo' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>"><?php esc_html_e( 'GEO-Location Settings', 'coreactivity' ); ?></a>
        </div>
    </div>
</div>
