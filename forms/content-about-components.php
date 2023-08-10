<?php

use Dev4Press\Plugin\CoreActivity\Log\Activity;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$components = Activity::instance()->get_all_components();
$events     = Activity::instance()->get_all_events();

?>
<div class="coreactivity-about-components">
	<?php

	foreach ( Activity::instance()->get_all_categories() as $category => $label ) {
		echo '<h3>' . $label . '</h3>';

		foreach ( $components as $component ) {
			if ( $component->category != $category || ! isset( $component->plugin ) || $component->plugin != 'coreactivity' ) {
				continue;
			}

			echo '<h4><i class="d4p-icon d4p-' . $component->icon . ' d4p-icon-fw"></i> ' . $component->label;
			echo ' <span>(' . $component->component . ')</span></h4>';
			echo '<ul>';

			foreach ( $events[ $component->component ] as $event ) {
				$version = isset( $event->version ) && $event->version != '1.0' ? ' <em>- v' . $event->version . '</em>' : '';
				echo '<li>' . $event->label . ' <span>(' . $event->event . ')</span>' . $version . '</li>';
			}

			echo '</ul>';
		}
	}

	?>
</div>
