<?php

use function Dev4Press\v51\Functions\panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="d4p-group d4p-dashboard-card d4p-card-double">
    <h3><?php esc_html_e( 'Blog Activity Logs', 'coreactivity' ); ?></h3>
    <div class="d4p-group-inner">
        <p>
			<?php esc_html_e( 'This is a limited use dashboard for the individual blog on your multisite network. You can see only log entries logged for this individual blog only. To access plugin settings, events, and logs for the whole network, you need to go to the Network Admin.', 'coreactivity' ); ?>
        </p>
    </div>
    <div class="d4p-group-footer">
        <a class="button-primary" href="<?php echo esc_url( panel()->a()->panel_url( 'dashboard', '', '', true ) ); ?>"><?php esc_html_e( 'Network Dashboard', 'coreactivity' ); ?></a>
        <a class="button-secondary" href="<?php echo esc_url( panel()->a()->panel_url( 'logs', '', '', true ) ); ?>"><?php esc_html_e( 'Full Activity Logs', 'coreactivity' ); ?></a>
        <a class="button-secondary" href="<?php echo esc_url( panel()->a()->panel_url( 'events', '', '', true ) ); ?>"><?php esc_html_e( 'Events Management', 'coreactivity' ); ?></a>
        <a class="button-secondary" href="<?php echo esc_url( panel()->a()->panel_url( 'settings', '', '', true ) ); ?>"><?php esc_html_e( 'Plugin Settings', 'coreactivity' ); ?></a>
    </div>
</div>
