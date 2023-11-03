<div style="display: none">
    <div id="coreactivity-log-dialog" title="<?php _e( 'Log Data', 'coreactivity' ); ?>">
        <div class="d4p-ctrl-tabs d4p-tabs-have-icons d4p-tabs-in-dialog">
            <div role="tablist" aria-label="<?php _e( "Log Dialog Popup" ); ?>">
                <button type="button" id="coreactivity-popup-tabs-info-tab" aria-controls="coreactivity-popup-tabs-info" aria-selected="true" role="tab" data-tabname="info" class="d4p-ctrl-tab d4p-ctrl-tab-coreactivity-popup-tabs-info d4p-ctrl-tab-is-active">
                    <i class="d4p-icon d4p-ui-calendar-pen d4p-icon-fw"></i><span><?php esc_html_e( "Data" ); ?></span>
                </button>
                <button type="button" id="coreactivity-popup-tabs-meta-tab" aria-controls="coreactivity-popup-tabs-meta" aria-selected="false" role="tab" data-tabname="meta" class="d4p-ctrl-tab d4p-ctrl-tab-coreactivity-popup-tabs-meta">
                    <i class="d4p-icon d4p-ui-newspaper d4p-icon-fw"></i><span><?php esc_html_e( "Meta" ); ?></span>
                </button>
                <button type="button" id="coreactivity-popup-tabs-location-tab" aria-controls="coreactivity-popup-tabs-location" aria-selected="false" role="tab" data-tabname="location" class="d4p-ctrl-tab d4p-ctrl-tab-coreactivity-popup-tabs-location">
                    <i class="d4p-icon d4p-ui-globe d4p-icon-fw"></i><span><?php esc_html_e( "Location" ); ?></span>
                </button>
            </div>
            <div id="coreactivity-popup-tabs-info" aria-hidden="false" role="tabpanel" aria-labelledby="coreactivity-popup-tabs-info-tab" class="d4p-ctrl-tabs-content d4p-ctrl-tab-coreactivity-popup-tabs-info d4p-ctrl-tabs-content-active">
                <div></div>
            </div>
            <div id="coreactivity-popup-tabs-meta" aria-hidden="true" role="tabpanel" aria-labelledby="coreactivity-popup-tabs-meta-tab" class="d4p-ctrl-tabs-content d4p-ctrl-tab-coreactivity-popup-tabs-meta" hidden>
                <div></div>
            </div>
            <div id="coreactivity-popup-tabs-location" aria-hidden="true" role="tabpanel" aria-labelledby="coreactivity-popup-tabs-location-tab" class="d4p-ctrl-tabs-content d4p-ctrl-tab-coreactivity-popup-tabs-location" hidden>
                <div></div>
            </div>
        </div>
    </div>
	<?php

	do_action( 'coreactivity-dialog-logs' );

	?>
</div>
