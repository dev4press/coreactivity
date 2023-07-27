<div class="d4p-wizard-panel-header">
    <p>
		<?php use function Dev4Press\v43\Functions\panel;

		esc_html_e( "The plugin has Notifications system for instant notifications with additional daily and weekly digests. For each of these notifications types, you need to enable events you want to include, and you can do that from the Events panel where each event has switches for every notification type.", "coreactivity" ); ?>
        <a target="_blank" href="<?php echo panel()->a()->panel_url( 'events', '', '', true ); ?>"><?php esc_html_e( "Events Panel", "coreactivity" ); ?></a>
    </p>
</div>

<div class="d4p-wizard-panel-content">
    <div class="d4p-wizard-option-block d4p-wizard-block-yesno">
        <p><?php esc_html_e( "Do you want to have Instant Notifications enabled?", "coreactivity" ); ?></p>
        <div>
            <em><?php esc_html_e( "These notifications are sent as soon as the eligible event is logged (with instant delay period if needed to avoid too many emails sent).", "coreactivity" ); ?></em>
			<?php coreactivity_wizard()->render_yes_no( 'notifications', 'instant', 'no' ); ?>
        </div>
    </div>
    <div class="d4p-wizard-option-block d4p-wizard-block-yesno">
        <p><?php esc_html_e( "Do you want to have Daily Digest enabled?", "coreactivity" ); ?></p>
        <div>
            <em><?php esc_html_e( "This digest is sent once a day with overview of all components and events in the previous day.", "coreactivity" ); ?></em>
			<?php coreactivity_wizard()->render_yes_no( 'notifications', 'daily', 'no' ); ?>
        </div>
    </div>
    <div class="d4p-wizard-option-block d4p-wizard-block-yesno">
        <p><?php esc_html_e( "Do you want to have Weekly Digest enabled?", "coreactivity" ); ?></p>
        <div>
            <em><?php esc_html_e( "This digest is sent once a week with overview of all components and events in the previous 7 days.", "coreactivity" ); ?></em>
			<?php coreactivity_wizard()->render_yes_no( 'notifications', 'weekly', 'no' ); ?>
        </div>
    </div>
</div>
