<?php

use Dev4Press\Plugin\CoreActivity\Log\Statistics;

$statistics = Statistics::instance()->overall();

?>

<div class="d4p-group d4p-dashboard-card d4p-card-double">
	<h3><?php esc_html_e( "Overall Statistics", "coreactivity" ); ?></h3>
	<div class="d4p-group-inner">

	</div>
</div>
