<?php

use Dev4Press\Plugin\CoreActivity\Admin\Settings;
use Dev4Press\v53\Core\Options\Render;
use function Dev4Press\v53\Functions\panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="d4p-content">
    <div class="d4p-group d4p-group-information">
        <h3><?php esc_html_e( 'Important', 'coreactivity' ); ?></h3>
        <div class="d4p-group-inner">
			<?php esc_html_e( 'These tools will remove logged data from the database. Create database backup before using these tools to avoid data loss in case you change your mind.', 'coreactivity' ); ?>
        </div>
    </div>

	<?php

	$options = Settings::instance();
	$groups  = $options->tools_cleanup();

	Render::instance( 'coreactivity', panel()->a()->plugin_prefix )->prepare( 'tools-cleanup', $groups )->render();

	?>
</div>
