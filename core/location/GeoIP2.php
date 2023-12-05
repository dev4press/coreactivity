<?php

namespace Dev4Press\Plugin\CoreActivity\Location;

use Dev4Press\Plugin\CoreActivity\Log\GEO;
use Dev4Press\v45\Core\Helpers\IP;
use Dev4Press\v45\Service\GEOIP\Location;
use Dev4Press\v45\Service\GEOIP\Locator;
use GeoIp2\Database\Reader;
use Exception;

class GeoIP2 extends Locator {
	public function locate( string $ip ) : ?Location {
		if ( IP::is_private( $ip ) ) {
			return new Location(
				array(
					'status'       => 'private',
					'ip'           => $ip,
					'country_code' => 'XX',
				)
			);
		}

		$db = GEO::instance()->geoip2();

		if ( $db instanceof Reader ) {
			$data = array(
				'status' => 'active',
			);

			try {
				$city = $db->city( $ip );

				$data['continent_code'] = $city->continent->code;
				$data['country_code']   = $city->country->isoCode;
				$data['country_name']   = $city->country->name;
				$data['city']           = $city->city->name;
				$data['latitude']       = $city->location->latitude;
				$data['longitude']      = $city->location->longitude;
				$data['time_zone']      = $city->location->timeZone;
				$data['zip_code']       = $city->postal->code;
			} catch ( Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement,Squiz.Commenting.EmptyCatchComment
			}

			if ( ! isset( $data['country_code'] ) ) {
				try {
					$country = $db->country( $ip );

					$data['continent_code'] = $country->continent->code;
					$data['country_code']   = $country->country->isoCode;
					$data['country_name']   = $country->country->name;
				} catch ( Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement,Squiz.Commenting.EmptyCatchComment
				}
			}

			return new Location( $data );
		}

		return null;
	}

	public function bulk( array $ips ) {

	}

	protected function url( $ips ) : string {
		return '';
	}

	protected function process( $raw ) : ?Location {
		return null;
	}
}
