<?php

use function Dev4Press\v42\Functions\panel;

?>

<div class="d4p-content">
	<div class="d4p-group d4p-group-information d4p-group-export">
		<h3><?php _e( "Important Information", "coresocial" ); ?></h3>
		<div class="d4p-group-inner">
			<p><?php _e( "With this tool you export plugin settings into plain text file (JSON serialized content). Do not modify export file! Making changes to export file will make it unusable.", "coresocial" ); ?></p>
			<p><?php _e( "This tool doesn't export the data from the dedicated database tables.", "coresocial" ); ?></p>
		</div>
	</div>

	<?php panel()->include_accessibility_control(); ?>
</div>
