<?php

use Dev4Press\Plugin\CoreActivity\Table\Logs;

?>
<div class="d4p-content">
    <input type="hidden" name="page" value="coreactivity-events"/>
    <input type="hidden" name="coreactivity_handler" value="getback"/>

	<?php

	$_grid = new Logs();
	$_grid->prepare_items();

	$_grid->search_box( esc_html__( "Search", "coreactivity" ), 'events-search' );

	$_grid->display();

	?>
</div>
