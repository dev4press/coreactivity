<?php

namespace Dev4Press\Plugin\CoreActivity\Admin\Panel;

use Dev4Press\v51\Core\UI\Admin\PanelAbout;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class About extends PanelAbout {
	protected $history = true;

	protected function init_default_subpanels() {
		parent::init_default_subpanels();

		$this->subpanels = array_slice( $this->subpanels(), 0, 2 ) +
		                   array(
			                   'components' => array(
				                   'title' => __( 'Components', 'coreactivity' ),
				                   'icon'  => '',
			                   ),
		                   ) + array_slice( $this->subpanels(), 2 );
	}
}
