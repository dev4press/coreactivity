<?php

namespace Dev4Press\Plugin\CoreActivity\Admin\Panel;

use Dev4Press\Plugin\CoreActivity\Admin\Panel;
use Dev4Press\Plugin\CoreActivity\Table\Events as EventsTable;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Events extends Panel {
	protected bool $table = true;
	protected bool $sidebar = false;
	protected bool $form = true;
	protected bool $form_multiform = false;
	protected string $form_method = 'get';

	public function screen_options_show() {
		add_screen_option( 'per_page', array(
			'label'   => __( 'Rows', 'coreactivity' ),
			'default' => 50,
			'option'  => 'coreactivity_events_rows_per_page',
		) );

		$this->get_table_object();
	}

	public function get_table_object() {
		if ( is_null( $this->table_object ) ) {
			$this->table_object = new EventsTable();
		}

		return $this->table_object;
	}
}
