<?php

use Dev4Press\Plugin\CoreActivity\Basic\InstallDB;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="d4p-install-block">
    <h4>
		<?php esc_html_e( "Additional database tables", "coreactivity" ); ?>
    </h4>
    <div>
		<?php

		$db = InstallDB::instance();

		$list_db = $db->install();

		if ( ! empty( $list_db ) ) {
			echo '<h5>' . esc_html__( "Database Upgrade Notices", "coreactivity" ) . '</h5>';
			echo join( '<br/>', $list_db ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '<h5>' . esc_html__( "Database Tables Check", "coreactivity" ) . '</h5>';
		$check = $db->check();

		$msg = array();
		foreach ( $check as $table => $data ) {
			if ( $data['status'] == 'error' ) {
				$_proceed  = false;
				$_error_db = true;
				$msg[]     = '<span class="gdpc-error">[' . esc_html__( "ERROR", "coreactivity" ) . '] - <strong>' . $table . '</strong>: ' . $data['msg'] . '</span>';
			} else {
				$msg[] = '<span class="gdpc-ok">[' . esc_html__( "OK", "coreactivity" ) . '] - <strong>' . $table . '</strong></span>';
			}
		}

		echo join( '<br/>', $msg ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		?>
    </div>
</div>
