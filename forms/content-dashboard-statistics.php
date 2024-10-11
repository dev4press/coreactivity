<?php

use Dev4Press\Plugin\CoreActivity\Log\Statistics;
use function Dev4Press\v51\Functions\panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$_blog_id   = is_multisite() && ! is_network_admin() ? get_current_blog_id() : - 1;
$statistics = Statistics::instance()->detailed( 30, $_blog_id );

?>

<div class="d4p-group d4p-dashboard-card d4p-card-double">
    <h3><?php esc_html_e( 'Last 30 days statistics', 'coreactivity' ); ?></h3>
    <div class="d4p-group-inner">
        <div class="coreactivity-overall-components">
			<?php

			if ( $statistics['total'] == 0 ) {
				?>

                <p><?php esc_html_e( 'There are no events logged in the past 30 days.', 'coreactivity' ); ?></p>

				<?php
			} else {
				foreach ( $statistics['components'] as $component => $data ) {
					$width = ( $data['count'] / $statistics['max'] ) * 100;

					if ( $data['count'] > 0 ) {

						?>

                        <div class="coreactivity-component">
                            <div class="__label">
                                <a href="<?php echo esc_url( panel()->a()->panel_url( 'logs', '', 'view=component&filter-component=' . $component ) ); ?>">
                                    <i title="<?php echo esc_attr( $data['label'] ); ?>" class="d4p-icon d4p-<?php echo esc_attr( $data['icon'] ); ?> d4p-icon-fw"></i>
                                </a>
                            </div>
                            <div class="__bar">
                                <div class="__component"><?php echo esc_html( $data['label'] ); ?><span> (<?php echo esc_html( $component ); ?>)</span></div>
                                <div class="__inner" style="width: <?php echo esc_attr( $width ); ?>%;"></div>
                            </div>
                            <div class="__count"><?php echo esc_html( $data['count'] ); ?></div>
                        </div>

						<?php
					}
				}
			}

			?>
        </div>
    </div>
</div>
