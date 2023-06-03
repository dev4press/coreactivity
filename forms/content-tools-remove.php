<?php

use function Dev4Press\v42\Functions\panel;

?>

<div class="d4p-content">
	<div class="d4p-group d4p-group-information">
		<h3><?php esc_html_e( "Important Information", "coreactivity" ); ?></h3>
		<div class="d4p-group-inner">
			<?php esc_html_e( "This tool can remove plugin settings saved in the WordPress options table added by the plugin and you can remove share and likes data gathered by the plugin share blocks.", "coreactivity" ); ?>
			<br/><br/>
			<?php esc_html_e( "Deletion operations are not reversible, and it is highly recommended to create database backup before proceeding with this tool.", "coreactivity" ); ?>
			<?php esc_html_e( "If you choose to remove plugin settings, once that is done, all settings will be reinitialized to default values if you choose to leave plugin active.", "coreactivity" ); ?>
		</div>
	</div>

	<div class="d4p-group d4p-group-tools">
		<h3><?php esc_html_e( "Remove plugin settings", "coreactivity" ); ?></h3>
		<div class="d4p-group-inner">
			<label>
				<input type="checkbox" class="widefat" name="coreactivitytools[remove][settings]" value="on"/> <?php esc_html_e( "Main Plugin Settings", "coreactivity" ); ?>
			</label>
		</div>
	</div>

	<div class="d4p-group d4p-group-tools">
		<h3><?php esc_html_e( "Remove database data and tables", "coreactivity" ); ?></h3>
		<div class="d4p-group-inner">
			<p style="font-weight: bold"><?php esc_html_e( "This will remove all shares and likes data!", "coreactivity" ); ?></p>
			<label>
				<input type="checkbox" class="widefat" name="coreactivitytools[remove][drop]" value="on"/> <?php esc_html_e( "Remove plugins database table and all data in them", "coreactivity" ); ?>
			</label>
			<label>
				<input type="checkbox" class="widefat" name="coreactivitytools[remove][truncate]" value="on"/> <?php esc_html_e( "Remove all data from database table", "coreactivity" ); ?>
			</label><br/>
			<hr/>
			<p><?php esc_html_e( "Database tables that will be affected", "coreactivity" ); ?>:</p>
			<ul style="list-style: inside disc;">
				<li><?php echo esc_html( coreactivity_db()->events ); ?></li>
				<li><?php echo esc_html( coreactivity_db()->logs ); ?></li>
                <li><?php echo esc_html( coreactivity_db()->logmeta ); ?></li>
			</ul>
		</div>
	</div>

	<div class="d4p-group d4p-group-tools">
		<h3><?php esc_html_e( "Disable Plugin", "coreactivity" ); ?></h3>
		<div class="d4p-group-inner">
			<label>
				<input type="checkbox" class="widefat" name="coreactivitytools[remove][disable]" value="on"/> <?php esc_html_e( "Disable plugin", "coreactivity" ); ?>
			</label>
		</div>
	</div>

	<?php panel()->include_accessibility_control(); ?>
</div>
