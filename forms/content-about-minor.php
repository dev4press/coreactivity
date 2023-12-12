<?php

use Dev4Press\v45\Core\Quick\KSES;
use function Dev4Press\v45\Functions\panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="d4p-about-minor">
    <h3><?php esc_html_e( 'Maintenance and Security Releases', 'coreactivity' ); ?></h3>
    <p>
        <strong><?php esc_html_e( 'Version', 'coreactivity' ); ?> <span>1.5.1 / 1.5.2 / 1.5.3</span></strong> &minus;
        Minor improvements and fixes..
    </p>
    <p>
        <strong><?php esc_html_e( 'Version', 'coreactivity' ); ?> <span>1.5</span></strong> &minus;
        Library Updated. Minor updates and improvements.
    </p>
    <p>
        <strong><?php esc_html_e( 'Version', 'coreactivity' ); ?> <span>1.4</span></strong> &minus;
        New components. Many tweaks, improvements and fixes.
    </p>
    <p>
        <strong><?php esc_html_e( 'Version', 'coreactivity' ); ?> <span>1.3</span></strong> &minus;
        MaxMind GeoLite2 geolocation support. New DebugPress events. Many tweaks, improvements and fixes.
    </p>
    <p>
        <strong><?php esc_html_e( 'Version', 'coreactivity' ); ?> <span>1.2</span></strong> &minus;
        IP2Download geolocation support. Expanded DB table. Many tweaks, improvements and fixes.
    </p>
    <p>
        <strong><?php esc_html_e( 'Version', 'coreactivity' ); ?> <span>1.1</span></strong> &minus;
        Logs updates and new settings. WooCommerce component. Many tweaks, changes and fixes.
    </p>
    <p>
        <strong><?php esc_html_e( 'Version', 'coreactivity' ); ?> <span>1.0.1 / 1.0.2 / 1.0.3 / 1.0.4 / 1.0.5</span></strong> &minus;
        Library Updated. Few minor changes.
    </p>
    <p>
		<?php

		/* translators: Changelog subpanel information. %s: Subpanel URL. */
		echo KSES::standard( sprintf( __( 'For more information, see <a href=\'%s\'>the changelog</a>.', 'coreactivity' ), esc_url( panel()->a()->panel_url( 'about', 'changelog' ) ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		?>
    </p>
</div>
