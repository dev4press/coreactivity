<?php

use function Dev4Press\v46\Functions\panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="d4p-content">
    <input type="hidden" name="page" value="coreactivity-logs"/>
    <input type="hidden" name="coreactivity_handler" value="getback"/>

	<?php

	$_grid = panel()->get_table_object();

	if ( is_multisite() && ! is_network_admin() ) {
		$_grid->set_filter_lock( 'blog_id', get_current_blog_id() );
	}

	$_grid->prepare_table();
	$_grid->prepare_items();
	$_grid->live_attributes();

	?>
    <div class="d4p-grid-alternative-view-search">
		<?php

		$_grid->views();
		$_grid->search_box( esc_html__( 'Search', 'coreactivity' ), 'logs-search' );

		?>

    </div>

	<?php

	$_grid->display();

	?>
</div>
<?php

include 'dialog-logs.php';
include 'dialog-data.php';
