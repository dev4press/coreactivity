<?php

use function Dev4Press\v48\Functions\panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="d4p-content">
    <input type="hidden" name="page" value="coreactivity-events"/>
    <input type="hidden" name="coreactivity_handler" value="getback"/>

	<?php

	$_grid = panel()->get_table_object();
	$_grid->prepare_table();
	$_grid->prepare_items();

	$_grid->search_box( esc_html__( 'Search', 'coreactivity' ), 'events-search' );

	$_grid->display();

	?>
</div>
