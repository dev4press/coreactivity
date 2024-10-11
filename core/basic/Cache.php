<?php

namespace Dev4Press\Plugin\CoreActivity\Basic;

use Dev4Press\v51\Core\Cache\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Cache extends Core {
	public string $store = 'coreactivity';

	public function get_all_registered_events() {
		if ( ! $this->in( 'events', 'registered' ) ) {
			$events = DB::instance()->get_all_registered_events();

			$this->set( 'events', 'registered', $events );
		}

		return $this->get( 'events', 'registered' );
	}
}
