<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="d4p-content">
    <div class="d4p-cards-wrapper">
		<?php

		include( COREACTIVITY_PATH . 'forms/content-dashboard-overall.php' );
		include( COREACTIVITY_PATH . 'forms/content-dashboard-database.php' );
		include( COREACTIVITY_PATH . 'forms/content-dashboard-statistics.php' );

		?>
    </div>
</div>
