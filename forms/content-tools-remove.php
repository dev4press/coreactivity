<?php

use function Dev4Press\v42\Functions\panel;

?>

<div class="d4p-content">
	<div class="d4p-group d4p-group-information">
		<h3><?php _e( "Important Information", "coreactivity" ); ?></h3>
		<div class="d4p-group-inner">
			<?php _e( "This tool can remove plugin settings saved in the WordPress options table added by the plugin and you can remove share and likes data gathered by the plugin share blocks.", "coreactivity" ); ?>
			<br/><br/>
			<?php _e( "Deletion operations are not reversible, and it is highly recommended to create database backup before proceeding with this tool.", "coreactivity" ); ?>
			<?php _e( "If you choose to remove plugin settings, once that is done, all settings will be reinitialized to default values if you choose to leave plugin active.", "coreactivity" ); ?>
		</div>
	</div>

	<div class="d4p-group d4p-group-tools">
		<h3><?php _e( "Remove plugin settings", "coreactivity" ); ?></h3>
		<div class="d4p-group-inner">
			<label>
				<input type="checkbox" class="widefat" name="coreactivitytools[remove][settings]" value="on"/> <?php _e( "Main Plugin Settings", "coreactivity" ); ?>
			</label>
		</div>
	</div>

	<div class="d4p-group d4p-group-tools">
		<h3><?php _e( "Remove database data and tables", "coreactivity" ); ?></h3>
		<div class="d4p-group-inner">
			<p style="font-weight: bold"><?php _e( "This will remove all shares and likes data!", "coreactivity" ); ?></p>
			<label>
				<input type="checkbox" class="widefat" name="coreactivitytools[remove][drop]" value="on"/> <?php _e( "Remove plugins database table and all data in them", "coreactivity" ); ?>
			</label>
			<label>
				<input type="checkbox" class="widefat" name="coreactivitytools[remove][truncate]" value="on"/> <?php _e( "Remove all data from database table", "coreactivity" ); ?>
			</label><br/>
			<hr/>
			<p><?php _e( "Database tables that will be affected", "coreactivity" ); ?>:</p>
			<ul style="list-style: inside disc;">
				<li><?php echo coreactivity_db()->events; ?></li>
				<li><?php echo coreactivity_db()->logs; ?></li>
                <li><?php echo coreactivity_db()->logmeta; ?></li>
			</ul>
		</div>
	</div>

	<div class="d4p-group d4p-group-tools">
		<h3><?php _e( "Disable Plugin", "coreactivity" ); ?></h3>
		<div class="d4p-group-inner">
			<label>
				<input type="checkbox" class="widefat" name="coreactivitytools[remove][disable]" value="on"/> <?php _e( "Disable plugin", "coreactivity" ); ?>
			</label>
		</div>
	</div>

	<?php panel()->include_accessibility_control(); ?>
</div>
