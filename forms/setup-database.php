<?php

use Dev4Press\Plugin\CoreActivity\Basic\InstallDB;

?>
<div class="d4p-install-block">
	<h4>
		<?php _e( "Additional database tables", "coreactivity" ); ?>
	</h4>
	<div>
		<?php

		$db = InstallDB::instance();

		$list_db = $db->install();

		if ( ! empty( $list_db ) ) {
			echo '<h5>' . __( "Database Upgrade Notices", "coreactivity" ) . '</h5>';
			echo join( '<br/>', $list_db );
		}

		echo '<h5>' . __( "Database Tables Check", "coreactivity" ) . '</h5>';
		$check = $db->check();

		$msg = array();
		foreach ( $check as $table => $data ) {
			if ( $data['status'] == 'error' ) {
				$_proceed  = false;
				$_error_db = true;
				$msg[]     = '<span class="gdpc-error">[' . __( "ERROR", "coreactivity" ) . '] - <strong>' . $table . '</strong>: ' . $data['msg'] . '</span>';
			} else {
				$msg[] = '<span class="gdpc-ok">[' . __( "OK", "coreactivity" ) . '] - <strong>' . $table . '</strong></span>';
			}
		}

		echo join( '<br/>', $msg );

		?>
	</div>
</div>
