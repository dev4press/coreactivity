<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="d4p-content">
    <div class="d4p-group d4p-group-information d4p-group-updater">
        <h3><?php esc_html_e( "Update status", "coreactivity" ); ?></h3>
        <div class="d4p-group-inner">
			<?php

			include( COREACTIVITY_PATH . 'forms/setup-database.php' );

			?>
        </div>
    </div>
</div>
