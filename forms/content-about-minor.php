<?php

use Dev4Press\v44\Core\Quick\KSES;
use function Dev4Press\v44\Functions\panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="d4p-about-minor">
    <h3><?php esc_html_e( 'Maintenance and Security Releases', 'coreactivity' ); ?></h3>
    <p>
        <strong><?php esc_html_e( 'Version', 'coreactivity' ); ?> <span>1.1</span></strong> &minus;
        Logs updates and new settings. Few minor changes and fixes.
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
