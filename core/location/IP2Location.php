<?php

namespace Dev4Press\Plugin\CoreActivity\Location;

use Dev4Press\Plugin\CoreActivity\Log\GEO;
use Dev4Press\v46\Core\Helpers\IP;
use Dev4Press\v46\Service\GEOIP\Location;
use Dev4Press\v46\Service\GEOIP\Locator;
use IP2Location\Database;

class IP2Location extends Locator {
	private $_location_convert = array(
		'ipAddress'   => 'ip',
		'countryCode' => 'country_code',
		'countryName' => 'country_name',
		'regionName'  => 'region_name',
		'cityName'    => 'city',
		'latitude'    => 'latitude',
		'longitude'   => 'longitude',
		'zipCode'     => 'zip_code',
		'timeZone'    => 'time_zone',
	);

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

		$db = GEO::instance()->ip2location();

		if ( $db instanceof Database ) {
			$records = $db->lookup( $ip, Database::ALL );

			$data = array(
				'status' => 'active',
			);

			foreach ( $this->_location_convert as $loc => $real ) {
				if ( isset( $records[ $loc ] ) ) {
					$value = $records[ $loc ];

					if ( $value !== Database::FIELD_NOT_SUPPORTED ) {
						$data[ $real ] = $value;
					}
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
