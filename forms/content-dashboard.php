<?php

use Dev4Press\v50\Core\Scope;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="d4p-content">
    <div class="d4p-cards-wrapper">
		<?php

		if ( Scope::instance()->is_master_network_admin() ) {
			include COREACTIVITY_PATH . 'forms/content-dashboard-overall.php';
			include COREACTIVITY_PATH . 'forms/content-dashboard-database.php';
		} else {
			include COREACTIVITY_PATH . 'forms/content-dashboard-blog.php';
		}

		include COREACTIVITY_PATH . 'forms/content-dashboard-statistics.php';

		if ( Scope::instance()->is_master_network_admin() ) {
			include COREACTIVITY_PATH . 'forms/content-dashboard-location.php';
		}

		?>
    </div>
</div>
