<?php

use Dev4Press\v44\Core\Quick\KSES;
use function Dev4Press\v44\Functions\panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$db = \Dev4Press\Plugin\CoreActivity\Log\GEO::instance()->ip2download();

if ($db) {
	debugpress_r( $db->getDate() );
	debugpress_r( $db->getDatabaseVersion() );

    $info = \Dev4Press\Plugin\CoreActivity\Location\IP2Location::instance()->locate('8.8.8.8');
    debugpress_r($info->meta());

	$info = \Dev4Press\Plugin\CoreActivity\Location\IP2Location::instance()->locate('127.0.0.1');
	debugpress_r($info->meta());

	$records = $db->lookup( '8.8.8.8', \IP2Location\Database::ALL );
	debugpress_r( $records );

	$records = $db->lookup( '127.0.0.1', \IP2Location\Database::ALL );
	debugpress_r( $records );
}

?>
<div class="d4p-about-minor">
    <h3><?php esc_html_e( 'Maintenance and Security Releases', 'coreactivity' ); ?></h3>
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
