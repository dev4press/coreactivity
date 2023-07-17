<?php

namespace Dev4Press\Plugin\CoreActivity\Basic;

use WP_Term_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Helper {
	public static function get_term_ids_from_taxonomy_term_ids( array $tt_ids ) : array {
		$query = new WP_Term_Query( array(
			'term_taxonomy_id' => $tt_ids,
			'hide_empty'       => false,
			'fields'           => 'ids'
		) );

		return $query->terms;
	}
}
