<?php

use Dev4Press\Plugin\CoreActivity\Admin\Settings;
use Dev4Press\v42\Core\Options\Render;
use function Dev4Press\v42\Functions\panel;

?>

<div class="d4p-content">
    <div class="d4p-group d4p-group-information">
        <h3><?php _e( "Important", "gd-bbpress-toolbox" ); ?></h3>
        <div class="d4p-group-inner">
			<?php _e( "These tools will remove logged data from the database. Create database backup before using these tools to avoid data loss in case you change your mind.", "gd-bbpress-toolbox" ); ?>
        </div>
    </div>

	<?php

	$options = Settings::instance();
	$groups  = $options->tools_cleanup();

	Render::instance( 'coreactivity', panel()->a()->plugin_prefix )->prepare( 'tools-cleanup', $groups )->render();

	?>

    <div class="d4p-group d4p-group-information d4p-group-updater">
        <h3><?php esc_html_e( "Cleanup Logs database tables", "coreactivity" ); ?></h3>
        <div class="d4p-group-inner">
			<?php

			?>
        </div>
    </div>
</div>