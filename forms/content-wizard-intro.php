<div class="d4p-wizard-panel-header">
    <p>
		<?php esc_html_e( "Welcome to the setup wizard for coreActivity plugin! Here you can quickly set up the plugin, and if you need to adjust all the plugin features in more detail, you can do that later through various plugin panels.", "coresecurity" ); ?>
    </p>
    <p>
		<?php esc_html_e( "Using this wizard will reconfigure the plugin. Each option might affect one or more plugin settings.", "coresecurity" ); ?>
    </p>
    <p>
		<?php esc_html_e( "Let's start with few basics.", "coresecurity" ); ?>
    </p>
</div>

<div class="d4p-wizard-panel-content">
    <div class="d4p-wizard-option-block d4p-wizard-block-yesno">
        <p><?php esc_html_e( "Do you want to log Referer URL for each request?", "gd-knowledge-base" ); ?></p>
        <div>
            <em><?php esc_html_e( "Each request should referer URL, and depending on the request, this can be quite a long string. Referer is useful for checking the source of the request, but it can be faked by the request source. CoreActivity currently has no direct use for this value.", "gd-knowledge-base" ); ?></em>
			<?php coreactivity_wizard()->render_yes_no( 'intro', 'referer', 'no' ); ?>
        </div>
    </div>
    <div class="d4p-wizard-option-block d4p-wizard-block-yesno">
        <p><?php esc_html_e( "Do you want to log User Agent for each request?", "gd-knowledge-base" ); ?></p>
        <div>
            <em><?php esc_html_e( "Each request contains User Agent of the software (usually browser) making the request. This value can be faked by the request source. CoreActivity currently has no use for this value, but other plugins using the log may use it for some additional processing.", "gd-knowledge-base" ); ?></em>
			<?php coreactivity_wizard()->render_yes_no( 'intro', 'user_agent', 'no' ); ?>
        </div>
    </div>
</div>
