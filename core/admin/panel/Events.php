<?php

namespace Dev4Press\Plugin\CoreActivity\Admin\Panel;

use Dev4Press\Plugin\CoreActivity\Admin\Panel;
use Dev4Press\Plugin\CoreActivity\Table\Events as EventsTable;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Events extends Panel {
	protected $table = true;
	protected $sidebar = false;
	protected $form = true;
	protected $form_multiform = false;
	protected $form_method = 'get';

	public function screen_options_show() {
		$args = array(
			'label'   => __( "Rows", "coreactivity" ),
			'default' => 50,
			'option'  => 'coreactivity_events_rows_per_page'
		);

		add_screen_option( 'per_page', $args );

		new EventsTable();
	}
}
