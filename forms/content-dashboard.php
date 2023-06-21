<div class="d4p-content">
    <div class="d4p-cards-wrapper">
		<?php

        \Dev4Press\Plugin\CoreActivity\Basic\DB::instance()->prepare("SELECT * FROM wp_posts", 1);
		wp_dashboard_plugins_output('');

        include_once (ABSPATH.'/wp-includes/theme-compat/sidebar.php');

        include( COREACTIVITY_PATH . 'forms/content-dashboard-overall.php' );
		include( COREACTIVITY_PATH . 'forms/content-dashboard-statistics.php' );

		?>
    </div>
</div>
