<?php

use Dev4Press\Plugin\CoreActivity\Log\Statistics;

$statistics = Statistics::instance()->detailed();

?>

<div class="d4p-group d4p-dashboard-card d4p-card-double">
    <h3><?php esc_html_e( "Last 30 days statistics", "coreactivity" ); ?></h3>
    <div class="d4p-group-inner">
        <div class="coreactivity-overall-components">
			<?php

			foreach ( $statistics[ 'components' ] as $component => $data ) {
				$width = $statistics[ 'total' ] == 0 ? 0 : ( $data[ 'count' ] / $statistics[ 'total' ] ) * 100;

				?>

                <div class="coreactivity-component">
                    <div class="__label">
                        <a href="<?php echo esc_url( \Dev4Press\v42\Functions\panel()->a()->panel_url( 'logs', '', 'view=component&filter-component=' . $component ) ); ?>">
                            <i title="<?php echo esc_attr( $data[ 'label' ] ); ?>" class="d4p-icon d4p-<?php echo esc_attr( $data[ 'icon' ] ); ?> d4p-icon-fw"></i>
                        </a>
                    </div>
                    <div class="__bar">
                        <div class="__component"><?php echo esc_html( $data[ 'label' ] ); ?><span> (<?php echo esc_html( $component ); ?>)</span></div>
                        <div class="__inner" style="width: <?php echo esc_attr( $width ); ?>%;"></div>
                    </div>
                    <div class="__count"><?php echo esc_html( $data[ 'count' ] ); ?></div>
                </div>

				<?php
			}

			?>
        </div>
    </div>
</div>
