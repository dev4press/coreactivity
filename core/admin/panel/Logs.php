<?php

namespace Dev4Press\Plugin\CoreActivity\Admin\Panel;

use Dev4Press\Plugin\CoreActivity\Table\Logs as LogsTable;
use Dev4Press\Plugin\CoreActivity\Admin\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Logs extends Panel {
	protected $table = true;
	protected $sidebar = false;

	public function screen_options_show() {
		$args = array(
			'label'   => __( "Rows", "coreactivity" ),
			'default' => 25,
			'option'  => 'coreactivity_logs_rows_per_page'
		);

		add_screen_option( 'per_page', $args );

		new LogsTable();
	}
}
