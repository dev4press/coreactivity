<?php

use Dev4Press\Plugin\CoreActivity\Table\Logs;
use Dev4Press\Plugin\CoreActivity\Table\LogsBlog;

?>
<div class="d4p-content">
    <input type="hidden" name="page" value="coreactivity-logs"/>
    <input type="hidden" name="coreactivity_handler" value="getback"/>

	<?php

	if ( is_multisite() && ! is_network_admin() ) {
		$_grid = new LogsBlog();
	} else {
		$_grid = new Logs();
	}

	$_grid->prepare_items();
    $_grid->live_attributes();

	?>
    <div class="d4p-grid-alternative-view-search">
		<?php

		$_grid->views();
		$_grid->search_box( esc_html__( "Search", "coreactivity" ), 'logs-search' );

		?></div>

	<?php

	$_grid->display();

	?>
</div>
