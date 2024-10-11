<?php

use function Dev4Press\v51\Functions\panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="d4p-content">
    <div class="d4p-group d4p-group-information">
        <h3><?php esc_html_e( 'Important Information', 'coreactivity' ); ?></h3>
        <div class="d4p-group-inner">
			<?php esc_html_e( 'With this tool, you can easily enable or disable all events notifications status. If you want to enable all events for daily or weekly digest, or for instant notification, you can do it easily from here.', 'coreactivity' ); ?>
        </div>
    </div>

    <div class="d4p-group d4p-group-tools">
        <h3><?php esc_html_e( 'Instant Notifications', 'coreactivity' ); ?></h3>
        <div class="d4p-group-inner">
            <label>
                <input checked type="radio" class="widefat" name="coreactivity[tools-notifications][instant]" value="skip"/> <?php esc_html_e( 'Do Not Change', 'coreactivity' ); ?>
            </label>
            <label>
                <input type="radio" class="widefat" name="coreactivity[tools-notifications][instant]" value="on"/> <?php esc_html_e( 'Enable Instant Notifications for all Events', 'coreactivity' ); ?>
            </label>
            <label>
                <input type="radio" class="widefat" name="coreactivity[tools-notifications][instant]" value="off"/> <?php esc_html_e( 'Disable Instant Notifications for all Events', 'coreactivity' ); ?>
            </label>
        </div>
    </div>

    <div class="d4p-group d4p-group-tools">
        <h3><?php esc_html_e( 'Daily Digest', 'coreactivity' ); ?></h3>
        <div class="d4p-group-inner">
            <label>
                <input checked type="radio" class="widefat" name="coreactivity[tools-notifications][daily]" value="skip"/> <?php esc_html_e( 'Do Not Change', 'coreactivity' ); ?>
            </label>
            <label>
                <input type="radio" class="widefat" name="coreactivity[tools-notifications][daily]" value="on"/> <?php esc_html_e( 'Enable Daily Digest for all Events', 'coreactivity' ); ?>
            </label>
            <label>
                <input type="radio" class="widefat" name="coreactivity[tools-notifications][daily]" value="off"/> <?php esc_html_e( 'Disable Daily Digest for all Events', 'coreactivity' ); ?>
            </label>
        </div>
    </div>

    <div class="d4p-group d4p-group-tools">
        <h3><?php esc_html_e( 'Weekly Digest', 'coreactivity' ); ?></h3>
        <div class="d4p-group-inner">
            <label>
                <input checked type="radio" class="widefat" name="coreactivity[tools-notifications][weekly]" value="skip"/> <?php esc_html_e( 'Do Not Change', 'coreactivity' ); ?>
            </label>
            <label>
                <input type="radio" class="widefat" name="coreactivity[tools-notifications][weekly]" value="on"/> <?php esc_html_e( 'Enable Weekly Digest for all Events', 'coreactivity' ); ?>
            </label>
            <label>
                <input type="radio" class="widefat" name="coreactivity[tools-notifications][weekly]" value="off"/> <?php esc_html_e( 'Disable Weekly Digest for all Events', 'coreactivity' ); ?>
            </label>
        </div>
    </div>

	<?php panel()->include_accessibility_control(); ?>
</div>
