<?php

use Dev4Press\v48\Core\UI\Icons;

$_tabs = coreactivity_view_dialog_tabs();

?>
<div style="display: none">
    <div id="coreactivity-whois-dialog" title="<?php esc_html_e( 'IP WhoIs', 'coreactivity' ); ?>">
        <div>
            <i class="d4p-icon d4p-ui-spinner d4p-icon-spin d4p-icon-fw"></i> <?php esc_html_e( 'Please wait...', 'coreactivity' ); ?>
        </div>
    </div>
    <div id="coreactivity-log-dialog" title="<?php esc_html_e( 'Log Data', 'coreactivity' ); ?>">
        <div class="d4p-ctrl-tabs d4p-tabs-have-icons d4p-tabs-in-dialog">
            <div role="tablist" aria-label="<?php esc_html_e( 'Log Dialog Popup', 'coreactivity' ); ?>">
				<?php

				$_selected = true;
				foreach ( $_tabs as $_tab => $args ) {
					$the_tab = 'coreactivity-popup-tabs-' . $_tab;
					$ctrl    = 'coreactivity-popup-tabs-' . $_tab;
					$class   = 'd4p-ctrl-tab d4p-ctrl-tab-coreactivity-popup-tabs-' . $_tab . ( $_selected ? ' d4p-ctrl-tab-is-active' : '' );

					?>
                    <button type="button" id="<?php echo esc_attr( $the_tab ); ?>-tab" aria-controls="<?php echo esc_attr( $ctrl ); ?>" aria-selected="<?php echo $_selected ? 'true' : 'false'; ?>" role="tab" data-tabname="<?php echo esc_attr( $_tab ); ?>" class="<?php echo esc_attr( $class ); ?>">
						<?php echo Icons::instance()->icon( $args['icon'], 'i', array( 'full' => true ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                        <span><?php echo esc_html( $args['label'] ); ?></span>
                    </button>
					<?php

					$_selected = false;
				}

				?>
            </div>
			<?php

			$_selected = true;
			foreach ( $_tabs as $_tab => $args ) {
				$the_tab = 'coreactivity-popup-tabs-' . $_tab;
				$class   = 'd4p-ctrl-tabs-content d4p-ctrl-tab-coreactivity-popup-tabs-' . $_tab . ( $_selected ? ' d4p-ctrl-tabs-content-active' : '' );

				?>
                <div id="<?php echo esc_attr( $the_tab ); ?>" aria-hidden="<?php echo $_selected ? 'false' : 'true'; ?>" role="tabpanel" aria-labelledby="<?php echo esc_attr( $the_tab ); ?>-tab" class="<?php echo esc_attr( $class ); ?>" <?php echo $_selected ? '' : 'hidden'; ?>>
                    <div></div>
                </div>
				<?php

				$_selected = false;
			}

			?>
        </div>
    </div>
	<?php

	do_action( 'coreactivity_dialog_logs' );

	?>
</div>
