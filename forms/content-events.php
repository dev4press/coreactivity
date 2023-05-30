<?php

use Dev4Press\Plugin\CoreActivity\Table\Events;

?>
<div class="d4p-content">
    <input type="hidden" name="page" value="coreactivity-events"/>
    <input type="hidden" name="coreactivity_handler" value="getback"/>

	<?php

	$_grid = new Events();
	$_grid->prepare_items();

	$_grid->search_box( __( "Search", "coreactivity" ), 'events-search' );

	$_grid->display();

	?>
</div>
