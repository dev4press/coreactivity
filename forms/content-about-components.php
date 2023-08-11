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
		echo '<h3>' . esc_html( $label ) . '</h3>';

		foreach ( $components as $component ) {
			if ( $component->category != $category || ! isset( $component->plugin ) || $component->plugin != 'coreactivity' ) {
				continue;
			}

			echo '<h4><i class="d4p-icon d4p-' . esc_attr( $component->icon ) . ' d4p-icon-fw"></i> ' . esc_html( $component->label );
			echo ' <span>(' . esc_html( $component->component ) . ')</span></h4>';
			echo '<ul>';

			foreach ( $events[ $component->component ] as $event ) {
				$version = isset( $event->version ) && $event->version != '1.0' ? ' <em>- v' . $event->version . '</em>' : '';
				echo '<li>' . esc_html( $event->label ) . ' <span>(' . esc_html( $event->event ) . ')</span>' . $version . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			echo '</ul>';
		}
	}

	?>
</div>
