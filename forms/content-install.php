<?php

use function Dev4Press\v42\Functions\panel;

?>
<div class="d4p-content">
	<div class="d4p-setup-wrapper">
		<div class="d4p-update-info">
			<?php

			include( COREACTIVITY_PATH . 'forms/setup-database.php' );

			coreactivity_settings()->set( 'install', false, 'info' );
			coreactivity_settings()->set( 'update', false, 'info', true );

			?>

			<div class="d4p-install-block">
				<h4>
					<?php _e( "All Done", "coreactivity" ); ?>
				</h4>
				<div>
					<?php _e( "Installation completed.", "coreactivity" ); ?>
				</div>
			</div>

			<div class="d4p-install-confirm">
				<a class="button-primary" href="<?php echo panel()->a()->panel_url( 'about' ) ?>&install"><?php _e( "Click here to continue", "coreactivity" ); ?></a>
			</div>
		</div>
		<?php echo coreactivity()->recommend( 'install' ); ?>
	</div>
</div>
