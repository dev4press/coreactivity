<?php

use Dev4Press\v55\Core\Quick\KSES;
use function Dev4Press\v55\Functions\panel;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div class="d4p-about-minor">
    <h3><?php esc_html_e( 'Maintenance and Security Releases', 'coreactivity' ); ?></h3>
    <p>
        <strong><?php esc_html_e( 'Version', 'coreactivity' ); ?> <span>3.1</span></strong> &minus;
        Security fix. Various other improvements and fixes.
    </p>
    <p>
        <strong><?php esc_html_e( 'Version', 'coreactivity' ); ?> <span>3.0</span></strong> &minus;
        PHP, WordPress system requirements. Dev4Press Library Updated. Many minor changes.
    </p>
    <p>
        <?php

        /* translators: Changelog subpanel information. %s: Subpanel URL. */
        echo KSES::standard( sprintf( __( 'For more information, see <a href=\'%s\'>the changelog</a>.', 'coreactivity' ), esc_url( panel()->a()->panel_url( 'about', 'changelog' ) ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

        ?>
    </p>
</div>
