<div class="d4p-wizard-panel-header">
    <p>
		<?php esc_html_e( 'Main Logs panel has a lot of moving parts and elements, and some of things may be useful to have depending on what your preferences are.', 'coreactivity' ); ?>
    </p>
</div>

<div class="d4p-wizard-panel-content">
    <div class="d4p-wizard-option-block d4p-wizard-block-yesno">
        <p><?php esc_html_e( 'Do you want to show the Geolocation information based on IP?', 'coreactivity' ); ?></p>
        <div>
            <em><?php esc_html_e( 'For each request, IP is logged, and based on the IP, Geo location can be retrieved, and plugin can show Flag and other useful location information.', 'coreactivity' ); ?></em>
			<?php coreactivity_wizard()->render_yes_no( 'log', 'flag', 'no' ); ?>
        </div>
    </div>
    <div class="d4p-wizard-option-block d4p-wizard-block-yesno">
        <p><?php esc_html_e( 'Do you want to show Gravatar for users?', 'coreactivity' ); ?></p>
        <div>
            <em><?php esc_html_e( 'If the user is logged for the event, alongside name and other information, plugin can also display the avatar image provided by the Gravatar service.', 'coreactivity' ); ?></em>
			<?php coreactivity_wizard()->render_yes_no( 'log', 'avatar', 'no' ); ?>
        </div>
    </div>
</div>
