<?php

use Dev4Press\v50\Core\Quick\KSES;
use function Dev4Press\v50\Functions\panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="d4p-about-minor">
    <h3><?php esc_html_e( 'Maintenance and Security Releases', 'coreactivity' ); ?></h3>
    <p>
        <strong><?php esc_html_e( 'Version', 'coreactivity' ); ?> <span>2.3.1 / 2.3.2 / 2.3.3 / 2.3.4 / 2.3.5 / 2.3.6</span></strong> &minus;
        Few minor changes and fixes.
    </p>
    <p>
        <strong><?php esc_html_e( 'Version', 'coreactivity' ); ?> <span>2.3</span></strong> &minus;
        External libraries updated. Few minor changes and fixes.
    </p>
    <p>
        <strong><?php esc_html_e( 'Version', 'coreactivity' ); ?> <span>2.2</span></strong> &minus;
        Events link to cleanup. Device Detector library update Library Updated. Few minor changes.
    </p>
    <p>
        <strong><?php esc_html_e( 'Version', 'coreactivity' ); ?> <span>2.0.1 / 2.1</span></strong> &minus;
        Library Updated. Few minor changes.
    </p>
    <p>
		<?php

		/* translators: Changelog subpanel information. %s: Subpanel URL. */
		echo KSES::standard( sprintf( __( 'For more information, see <a href=\'%s\'>the changelog</a>.', 'coreactivity' ), esc_url( panel()->a()->panel_url( 'about', 'changelog' ) ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		?>
    </p>
</div>
