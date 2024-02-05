<?php

namespace Dev4Press\Plugin\CoreActivity\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Data {
	public static function get_ip_display_method() : array {
		return array(
			'full'     => __( 'Full IP', 'coreactivity' ),
			'partial' => __( 'Mask last Byte', 'coreactivity' ),
			'half' => __( 'Mask last 2 Bytes', 'coreactivity' ),
			'hide' => __( 'Maks whole IP', 'coreactivity' ),
		);
	}

	public static function get_period_list() : array {
		return array(
			''     => __( 'Select the data age', 'coreactivity' ),
			'd000' => __( 'All logged data', 'coreactivity' ),
			'd001' => __( 'Logged data older than 1 day', 'coreactivity' ),
			'd003' => __( 'Logged data older than 3 days', 'coreactivity' ),
			'd007' => __( 'Logged data older than 7 days', 'coreactivity' ),
			'd014' => __( 'Logged data older than 14 days', 'coreactivity' ),
			'd030' => __( 'Logged data older than 30 days', 'coreactivity' ),
			'd060' => __( 'Logged data older than 60 days', 'coreactivity' ),
			'd090' => __( 'Logged data older than 90 days', 'coreactivity' ),
			'd180' => __( 'Logged data older than 180 days', 'coreactivity' ),
			'm012' => __( 'Logged data older than 1 year', 'coreactivity' ),
			'm024' => __( 'Logged data older than 2 years', 'coreactivity' ),
		);
	}

	public static function get_week_days() : array {
		global $wp_locale;

		$list = array();

		for ( $day_index = 0; $day_index <= 6; $day_index ++ ) {
			$list[ 'D' . $day_index ] = $wp_locale->get_weekday( $day_index );
		}

		return $list;
	}

	public static function get_geo_location_methods() : array {
		return array(
			'online'      => __( 'Online', 'coreactivity' ),
			'ip2location' => __( 'IP2Location Database', 'coreactivity' ),
			'geoip2'      => __( 'MaxMind GeoIP2 and GeoLite2 Database', 'coreactivity' ),
		);
	}

	public static function get_ip2location_db() : array {
		return array(
			'DB1LITEBINIPV6'  => __( 'Lite: Country', 'coreactivity' ),
			'DB3LITEBINIPV6'  => __( 'Lite: Country, Region, City', 'coreactivity' ),
			'DB5LITEBINIPV6'  => __( 'Lite: Country, Region, City, Location', 'coreactivity' ),
			'DB9LITEBINIPV6'  => __( 'Lite: Country, Region, City, Location, ZIP', 'coreactivity' ),
			'DB11LITEBINIPV6' => __( 'Lite: Country, Region, City, Location, ZIP, Timezone', 'coreactivity' ),
		);
	}

	public static function get_geoip2_db() : array {
		return array(
			'GeoLite2-Country' => __( 'GeoLite2: Country', 'coreactivity' ),
			'GeoLite2-City'    => __( 'GeoLite2: Country, City, Location, ZIP, Timezone', 'coreactivity' ),
		);
	}
}
