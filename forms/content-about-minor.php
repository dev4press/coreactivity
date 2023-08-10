<?php

use function Dev4Press\v43\Functions\panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="d4p-about-minor">
    <h3><?php esc_html_e( "Maintenance and Security Releases", "coreactivity" ); ?></h3>
    <p>
        <strong><?php esc_html_e( "Version", "coreactivity" ); ?> <span>1.1</span></strong> &minus;
    </p>
    <p>
		<?php printf( __( "For more information, see <a href='%s'>the changelog</a>.", "coreactivity" ), panel()->a()->panel_url( 'about', 'changelog' ) ); ?>
    </p>
</div>