<?php

use Dev4Press\Plugin\CoreActivity\Log\Statistics;
use function Dev4Press\v51\Functions\panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$statistics = Statistics::instance()->overall();
$active     = coreactivity()->is_logging_active();

?>

<div class="d4p-group d4p-dashboard-card">
    <h3><?php esc_html_e( 'Components and Events', 'coreactivity' ); ?></h3>
    <div class="d4p-group-inner">
        <div class="coreactivity-overall-wrapper">
            <div class="coreactivity-overall-for-components">
                <i class="d4p-icon d4p-ui-folder d4p-icon-5x d4p-icon-fw"></i>
                <div>
                    <h4><?php esc_html_e( 'Components', 'coreactivity' ); ?></h4>
                    <div class="__available">
                        <strong><?php echo esc_html( $statistics['components']['available'] ); ?></strong> <?php esc_html_e( 'Available', 'coreactivity' ); ?>
                    </div>
                    <div>
                        <strong><?php echo esc_html( $statistics['components']['total'] ); ?></strong> <?php esc_html_e( 'Total Registered', 'coreactivity' ); ?>
                    </div>
                </div>
            </div>
            <div class="coreactivity-overall-for-events">
                <i class="d4p-icon d4p-ui-radar d4p-icon-5x d4p-icon-fw"></i>
                <div>
                    <h4><?php esc_html_e( 'Events', 'coreactivity' ); ?></h4>
                    <div class="__available">
                        <strong><?php echo esc_html( $statistics['events']['available'] ); ?></strong> <?php esc_html_e( 'Available', 'coreactivity' ); ?>
                    </div>
                    <div>
                        <strong><?php echo esc_html( $statistics['events']['active'] ); ?></strong> <?php esc_html_e( 'Enabled Events', 'coreactivity' ); ?>
                    </div>
                    <div>
                        <strong><?php echo esc_html( $statistics['events']['total'] ); ?></strong> <?php esc_html_e( 'Total Registered', 'coreactivity' ); ?>
                    </div>
                </div>
            </div>
        </div>
        <hr class="coreactivity-overall-sep"/>
        <div class="coreactivity-activation-control" style="margin-bottom: 4em;">
            <i class="d4p-icon d4p-ui-sliders-base d4p-icon-5x d4p-icon-fw"></i>
            <div>
                <div class="__element __full-width">
					<?php

					if ( $active ) {
						?><?php esc_html_e( 'Events Logging', 'coreactivity' ); ?>: <span class="coreactivity-badge __badge-green">
                        <i aria-hidden="true" class="d4p-icon d4p-ui-check-square"></i> <?php esc_html_e( 'Active', 'coreactivity' ) ?></span><?php
					} else {
						?><?php esc_html_e( 'Events Logging', 'coreactivity' ); ?>: <span class="coreactivity-badge __badge-red">
                        <i aria-hidden="true" class="d4p-icon d4p-ui-close-square"></i> <?php esc_html_e( 'Disabled', 'coreactivity' ) ?></span><?php
					}

					?>
                </div>
            </div>
        </div>
    </div>
    <div class="d4p-group-footer">
		<?php

		if ( $active ) {
			?>
            <a class="button-secondary" href="<?php echo esc_url( panel()->a()->action_url( 'disable-logging', 'coreactivity-disable-logging' ) ); ?>"><?php esc_html_e( 'Disable all Events Logging', 'coreactivity' ); ?></a><?php
		} else {
			?>
            <a class="button-primary" href="<?php echo esc_url( panel()->a()->action_url( 'enable-logging', 'coreactivity-enable-logging' ) ); ?>"><?php esc_html_e( 'Enable Events Logging', 'coreactivity' ); ?></a><?php
		}

		?>
    </div>
</div>
