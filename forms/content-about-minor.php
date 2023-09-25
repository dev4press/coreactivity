<?php

use function Dev4Press\v43\Functions\panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="d4p-about-minor">
    <h3><?php esc_html_e( 'Maintenance and Security Releases', 'coreactivity' ); ?></h3>
    <p>
        <strong><?php esc_html_e( 'Version', 'coreactivity' ); ?> <span>1.0.1 / 1.0.2</span></strong> &minus;
        Library Updated
    </p>
    <p>
		<?php

		/* translators: Changelog subpanel information. %s: Subpanel URL. */
        printf( esc_html__( 'For more information, see <a href=\'%s\'>the changelog</a>.', 'coreactivity' ), esc_url_raw( panel()->a()->panel_url( 'about', 'changelog' ) ) );

        ?>
    </p>
</div>
