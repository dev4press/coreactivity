<?php

namespace Dev4Press\Plugin\CoreActivity\Admin\Panel;

use Dev4Press\Plugin\CoreActivity\Basic\Render;
use Dev4Press\Plugin\CoreActivity\Table\Logs as LogsTable;
use Dev4Press\Plugin\CoreActivity\Admin\Panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Logs extends Panel {
	protected bool $table = true;
	protected bool $sidebar = false;
	protected bool $form = true;
	protected bool $form_multiform = false;
	protected string $form_method = 'get';

	public function screen_options_show() {
		add_screen_option( 'per_page', array(
			'label'   => __( 'Rows', 'coreactivity' ),
			'default' => 25,
			'option'  => 'coreactivity_logs_rows_per_page',
		) );

		$this->get_table_object();
	}

	public function get_table_object() {
		if ( is_null( $this->table_object ) ) {
			$this->table_object = new LogsTable();
		}

		return $this->table_object;
	}

	public function header_fill() : string {
		return Render::panel_header_ip_block();
	}
}
