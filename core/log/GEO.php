<?php

namespace Dev4Press\Plugin\CoreActivity\Log;

use Dev4Press\Plugin\CoreActivity\Basic\Plugin;
use Dev4Press\Plugin\CoreActivity\Location\GeoIP2;
use Dev4Press\Plugin\CoreActivity\Location\IP2Location;
use Dev4Press\v51\Core\Helpers\Data;
use Dev4Press\v51\Core\Quick\File;
use Dev4Press\v51\Core\Quick\Misc;
use Dev4Press\v51\Service\GEOIP\GEOJSIO;
use Dev4Press\v51\Service\GEOIP\Location;
use GeoIp2\Database\Reader;
use IP2Location\Database;
use WP_Filesystem_Direct;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GEO {
	private $method;
	private $ip2download = null;
	private $geoip2 = null;
	private $codes = array(
		'ad' => array(
			'name'      => 'Andorra',
			'continent' => 'EU',
		),
		'ae' => array(
			'name'      => 'United Arab Emirates',
			'continent' => 'AS',
		),
		'af' => array(
			'name'      => 'Afghanistan',
			'continent' => 'AS',
		),
		'ag' => array(
			'name'      => 'Antigua and Barbuda',
			'continent' => 'NA',
		),
		'ai' => array(
			'name'      => 'Anguilla',
			'continent' => 'NA',
		),
		'al' => array(
			'name'      => 'Albania',
			'continent' => 'EU',
		),
		'am' => array(
			'name'      => 'Armenia',
			'continent' => 'AS',
		),
		'ao' => array(
			'name'      => 'Angola',
			'continent' => 'AF',
		),
		'aq' => array(
			'name'      => 'Antarctica',
			'continent' => 'AN',
		),
		'ar' => array(
			'name'      => 'Argentina',
			'continent' => 'SA',
		),
		'as' => array(
			'name'      => 'American Samoa',
			'continent' => 'OC',
		),
		'at' => array(
			'name'      => 'Austria',
			'continent' => 'EU',
		),
		'au' => array(
			'name'      => 'Australia',
			'continent' => 'OC',
		),
		'aw' => array(
			'name'      => 'Aruba',
			'continent' => 'NA',
		),
		'ax' => array(
			'name'      => 'Åland Islands',
			'continent' => 'EU',
		),
		'az' => array(
			'name'      => 'Azerbaijan',
			'continent' => 'AS',
		),
		'ba' => array(
			'name'      => 'Bosnia and Herzegovina',
			'continent' => 'EU',
		),
		'bb' => array(
			'name'      => 'Barbados',
			'continent' => 'NA',
		),
		'bd' => array(
			'name'      => 'Bangladesh',
			'continent' => 'AS',
		),
		'be' => array(
			'name'      => 'Belgium',
			'continent' => 'EU',
		),
		'bf' => array(
			'name'      => 'Burkina Faso',
			'continent' => 'AF',
		),
		'bg' => array(
			'name'      => 'Bulgaria',
			'continent' => 'EU',
		),
		'bh' => array(
			'name'      => 'Bahrain',
			'continent' => 'AS',
		),
		'bi' => array(
			'name'      => 'Burundi',
			'continent' => 'AF',
		),
		'bj' => array(
			'name'      => 'Benin',
			'continent' => 'AF',
		),
		'bl' => array(
			'name'      => 'Saint Barthélemy',
			'continent' => 'NA',
		),
		'bm' => array(
			'name'      => 'Bermuda',
			'continent' => 'NA',
		),
		'bn' => array(
			'name'      => 'Brunei Darussalam',
			'continent' => 'AS',
		),
		'bo' => array(
			'name'      => 'Bolivia',
			'continent' => 'SA',
		),
		'bq' => array(
			'name'      => 'Bonaire, Sint Eustatius and Saba',
			'continent' => 'NA',
		),
		'br' => array(
			'name'      => 'Brazil',
			'continent' => 'SA',
		),
		'bs' => array(
			'name'      => 'Bahamas',
			'continent' => 'NA',
		),
		'bt' => array(
			'name'      => 'Bhutan',
			'continent' => 'AS',
		),
		'bv' => array(
			'name'      => 'Bouvet Island (Bouvetoya)',
			'continent' => 'AN',
		),
		'bw' => array(
			'name'      => 'Botswana',
			'continent' => 'AF',
		),
		'by' => array(
			'name'      => 'Belarus',
			'continent' => 'EU',
		),
		'bz' => array(
			'name'      => 'Belize',
			'continent' => 'NA',
		),
		'ca' => array(
			'name'      => 'Canada',
			'continent' => 'NA',
		),
		'cc' => array(
			'name'      => 'Cocos (Keeling) Islands',
			'continent' => 'AS',
		),
		'cd' => array(
			'name'      => 'Congo',
			'continent' => 'AF',
		),
		'cf' => array(
			'name'      => 'Central African Republic',
			'continent' => 'AF',
		),
		'cg' => array(
			'name'      => 'Congo',
			'continent' => 'AF',
		),
		'ch' => array(
			'name'      => 'Switzerland',
			'continent' => 'EU',
		),
		'ci' => array(
			'name'      => 'Cote d\'Ivoire',
			'continent' => 'AF',
		),
		'ck' => array(
			'name'      => 'Cook Islands',
			'continent' => 'OC',
		),
		'cl' => array(
			'name'      => 'Chile',
			'continent' => 'SA',
		),
		'cm' => array(
			'name'      => 'Cameroon',
			'continent' => 'AF',
		),
		'cn' => array(
			'name'      => 'China',
			'continent' => 'AS',
		),
		'co' => array(
			'name'      => 'Colombia',
			'continent' => 'SA',
		),
		'cr' => array(
			'name'      => 'Costa Rica',
			'continent' => 'NA',
		),
		'cu' => array(
			'name'      => 'Cuba',
			'continent' => 'NA',
		),
		'cv' => array(
			'name'      => 'Cape Verde',
			'continent' => 'AF',
		),
		'cw' => array(
			'name'      => 'Curaçao',
			'continent' => 'NA',
		),
		'cx' => array(
			'name'      => 'Christmas Island',
			'continent' => 'AS',
		),
		'cy' => array(
			'name'      => 'Cyprus',
			'continent' => 'AS',
		),
		'cz' => array(
			'name'      => 'Czech Republic',
			'continent' => 'EU',
		),
		'de' => array(
			'name'      => 'Germany',
			'continent' => 'EU',
		),
		'dj' => array(
			'name'      => 'Djibouti',
			'continent' => 'AF',
		),
		'dk' => array(
			'name'      => 'Denmark',
			'continent' => 'EU',
		),
		'dm' => array(
			'name'      => 'Dominica',
			'continent' => 'NA',
		),
		'do' => array(
			'name'      => 'Dominican Republic',
			'continent' => 'NA',
		),
		'dz' => array(
			'name'      => 'Algeria',
			'continent' => 'AF',
		),
		'ec' => array(
			'name'      => 'Ecuador',
			'continent' => 'SA',
		),
		'ee' => array(
			'name'      => 'Estonia',
			'continent' => 'EU',
		),
		'eg' => array(
			'name'      => 'Egypt',
			'continent' => 'AF',
		),
		'eh' => array(
			'name'      => 'Western Sahara',
			'continent' => 'AF',
		),
		'er' => array(
			'name'      => 'Eritrea',
			'continent' => 'AF',
		),
		'es' => array(
			'name'      => 'Spain',
			'continent' => 'EU',
		),
		'et' => array(
			'name'      => 'Ethiopia',
			'continent' => 'AF',
		),
		'fi' => array(
			'name'      => 'Finland',
			'continent' => 'EU',
		),
		'fj' => array(
			'name'      => 'Fiji',
			'continent' => 'OC',
		),
		'fk' => array(
			'name'      => 'Falkland Islands (Malvinas)',
			'continent' => 'SA',
		),
		'fm' => array(
			'name'      => 'Micronesia',
			'continent' => 'OC',
		),
		'fo' => array(
			'name'      => 'Faroe Islands',
			'continent' => 'EU',
		),
		'fr' => array(
			'name'      => 'France',
			'continent' => 'EU',
		),
		'ga' => array(
			'name'      => 'Gabon',
			'continent' => 'AF',
		),
		'gb' => array(
			'name'      => 'United Kingdom of Great Britain & Northern Ireland',
			'continent' => 'EU',
		),
		'gd' => array(
			'name'      => 'Grenada',
			'continent' => 'NA',
		),
		'ge' => array(
			'name'      => 'Georgia',
			'continent' => 'AS',
		),
		'gf' => array(
			'name'      => 'French Guiana',
			'continent' => 'SA',
		),
		'gg' => array(
			'name'      => 'Guernsey',
			'continent' => 'EU',
		),
		'gh' => array(
			'name'      => 'Ghana',
			'continent' => 'AF',
		),
		'gi' => array(
			'name'      => 'Gibraltar',
			'continent' => 'EU',
		),
		'gl' => array(
			'name'      => 'Greenland',
			'continent' => 'NA',
		),
		'gm' => array(
			'name'      => 'Gambia',
			'continent' => 'AF',
		),
		'gn' => array(
			'name'      => 'Guinea',
			'continent' => 'AF',
		),
		'gp' => array(
			'name'      => 'Guadeloupe',
			'continent' => 'NA',
		),
		'gq' => array(
			'name'      => 'Equatorial Guinea',
			'continent' => 'AF',
		),
		'gr' => array(
			'name'      => 'Greece',
			'continent' => 'EU',
		),
		'gs' => array(
			'name'      => 'South Georgia and the South Sandwich Islands',
			'continent' => 'AN',
		),
		'gt' => array(
			'name'      => 'Guatemala',
			'continent' => 'NA',
		),
		'gu' => array(
			'name'      => 'Guam',
			'continent' => 'OC',
		),
		'gw' => array(
			'name'      => 'Guinea-Bissau',
			'continent' => 'AF',
		),
		'gy' => array(
			'name'      => 'Guyana',
			'continent' => 'SA',
		),
		'hk' => array(
			'name'      => 'Hong Kong',
			'continent' => 'AS',
		),
		'hm' => array(
			'name'      => 'Heard Island and McDonald Islands',
			'continent' => 'AN',
		),
		'hn' => array(
			'name'      => 'Honduras',
			'continent' => 'NA',
		),
		'hr' => array(
			'name'      => 'Croatia',
			'continent' => 'EU',
		),
		'ht' => array(
			'name'      => 'Haiti',
			'continent' => 'NA',
		),
		'hu' => array(
			'name'      => 'Hungary',
			'continent' => 'EU',
		),
		'id' => array(
			'name'      => 'Indonesia',
			'continent' => 'AS',
		),
		'ie' => array(
			'name'      => 'Ireland',
			'continent' => 'EU',
		),
		'il' => array(
			'name'      => 'Israel',
			'continent' => 'AS',
		),
		'im' => array(
			'name'      => 'Isle of Man',
			'continent' => 'EU',
		),
		'in' => array(
			'name'      => 'India',
			'continent' => 'AS',
		),
		'io' => array(
			'name'      => 'British Indian Ocean Territory (Chagos Archipelago)',
			'continent' => 'AS',
		),
		'iq' => array(
			'name'      => 'Iraq',
			'continent' => 'AS',
		),
		'ir' => array(
			'name'      => 'Iran',
			'continent' => 'AS',
		),
		'is' => array(
			'name'      => 'Iceland',
			'continent' => 'EU',
		),
		'it' => array(
			'name'      => 'Italy',
			'continent' => 'EU',
		),
		'je' => array(
			'name'      => 'Jersey',
			'continent' => 'EU',
		),
		'jm' => array(
			'name'      => 'Jamaica',
			'continent' => 'NA',
		),
		'jo' => array(
			'name'      => 'Jordan',
			'continent' => 'AS',
		),
		'jp' => array(
			'name'      => 'Japan',
			'continent' => 'AS',
		),
		'ke' => array(
			'name'      => 'Kenya',
			'continent' => 'AF',
		),
		'kg' => array(
			'name'      => 'Kyrgyz Republic',
			'continent' => 'AS',
		),
		'kh' => array(
			'name'      => 'Cambodia',
			'continent' => 'AS',
		),
		'ki' => array(
			'name'      => 'Kiribati',
			'continent' => 'OC',
		),
		'km' => array(
			'name'      => 'Comoros',
			'continent' => 'AF',
		),
		'kn' => array(
			'name'      => 'Saint Kitts and Nevis',
			'continent' => 'NA',
		),
		'kp' => array(
			'name'      => 'Korea',
			'continent' => 'AS',
		),
		'kr' => array(
			'name'      => 'Korea',
			'continent' => 'AS',
		),
		'kw' => array(
			'name'      => 'Kuwait',
			'continent' => 'AS',
		),
		'ky' => array(
			'name'      => 'Cayman Islands',
			'continent' => 'NA',
		),
		'kz' => array(
			'name'      => 'Kazakhstan',
			'continent' => 'AS',
		),
		'la' => array(
			'name'      => 'Lao People\'s Democratic Republic',
			'continent' => 'AS',
		),
		'lb' => array(
			'name'      => 'Lebanon',
			'continent' => 'AS',
		),
		'lc' => array(
			'name'      => 'Saint Lucia',
			'continent' => 'NA',
		),
		'li' => array(
			'name'      => 'Liechtenstein',
			'continent' => 'EU',
		),
		'lk' => array(
			'name'      => 'Sri Lanka',
			'continent' => 'AS',
		),
		'lr' => array(
			'name'      => 'Liberia',
			'continent' => 'AF',
		),
		'ls' => array(
			'name'      => 'Lesotho',
			'continent' => 'AF',
		),
		'lt' => array(
			'name'      => 'Lithuania',
			'continent' => 'EU',
		),
		'lu' => array(
			'name'      => 'Luxembourg',
			'continent' => 'EU',
		),
		'lv' => array(
			'name'      => 'Latvia',
			'continent' => 'EU',
		),
		'ly' => array(
			'name'      => 'Libya',
			'continent' => 'AF',
		),
		'ma' => array(
			'name'      => 'Morocco',
			'continent' => 'AF',
		),
		'mc' => array(
			'name'      => 'Monaco',
			'continent' => 'EU',
		),
		'md' => array(
			'name'      => 'Moldova',
			'continent' => 'EU',
		),
		'me' => array(
			'name'      => 'Montenegro',
			'continent' => 'EU',
		),
		'mf' => array(
			'name'      => 'Saint Martin',
			'continent' => 'NA',
		),
		'mg' => array(
			'name'      => 'Madagascar',
			'continent' => 'AF',
		),
		'mh' => array(
			'name'      => 'Marshall Islands',
			'continent' => 'OC',
		),
		'mk' => array(
			'name'      => 'Macedonia',
			'continent' => 'EU',
		),
		'ml' => array(
			'name'      => 'Mali',
			'continent' => 'AF',
		),
		'mm' => array(
			'name'      => 'Myanmar',
			'continent' => 'AS',
		),
		'mn' => array(
			'name'      => 'Mongolia',
			'continent' => 'AS',
		),
		'mo' => array(
			'name'      => 'Macao',
			'continent' => 'AS',
		),
		'mp' => array(
			'name'      => 'Northern Mariana Islands',
			'continent' => 'OC',
		),
		'mq' => array(
			'name'      => 'Martinique',
			'continent' => 'NA',
		),
		'mr' => array(
			'name'      => 'Mauritania',
			'continent' => 'AF',
		),
		'ms' => array(
			'name'      => 'Montserrat',
			'continent' => 'NA',
		),
		'mt' => array(
			'name'      => 'Malta',
			'continent' => 'EU',
		),
		'mu' => array(
			'name'      => 'Mauritius',
			'continent' => 'AF',
		),
		'mv' => array(
			'name'      => 'Maldives',
			'continent' => 'AS',
		),
		'mw' => array(
			'name'      => 'Malawi',
			'continent' => 'AF',
		),
		'mx' => array(
			'name'      => 'Mexico',
			'continent' => 'NA',
		),
		'my' => array(
			'name'      => 'Malaysia',
			'continent' => 'AS',
		),
		'mz' => array(
			'name'      => 'Mozambique',
			'continent' => 'AF',
		),
		'na' => array(
			'name'      => 'Namibia',
			'continent' => 'AF',
		),
		'nc' => array(
			'name'      => 'New Caledonia',
			'continent' => 'OC',
		),
		'ne' => array(
			'name'      => 'Niger',
			'continent' => 'AF',
		),
		'nf' => array(
			'name'      => 'Norfolk Island',
			'continent' => 'OC',
		),
		'ng' => array(
			'name'      => 'Nigeria',
			'continent' => 'AF',
		),
		'ni' => array(
			'name'      => 'Nicaragua',
			'continent' => 'NA',
		),
		'nl' => array(
			'name'      => 'Netherlands',
			'continent' => 'EU',
		),
		'no' => array(
			'name'      => 'Norway',
			'continent' => 'EU',
		),
		'np' => array(
			'name'      => 'Nepal',
			'continent' => 'AS',
		),
		'nr' => array(
			'name'      => 'Nauru',
			'continent' => 'OC',
		),
		'nu' => array(
			'name'      => 'Niue',
			'continent' => 'OC',
		),
		'nz' => array(
			'name'      => 'New Zealand',
			'continent' => 'OC',
		),
		'om' => array(
			'name'      => 'Oman',
			'continent' => 'AS',
		),
		'pa' => array(
			'name'      => 'Panama',
			'continent' => 'NA',
		),
		'pe' => array(
			'name'      => 'Peru',
			'continent' => 'SA',
		),
		'pf' => array(
			'name'      => 'French Polynesia',
			'continent' => 'OC',
		),
		'pg' => array(
			'name'      => 'Papua New Guinea',
			'continent' => 'OC',
		),
		'ph' => array(
			'name'      => 'Philippines',
			'continent' => 'AS',
		),
		'pk' => array(
			'name'      => 'Pakistan',
			'continent' => 'AS',
		),
		'pl' => array(
			'name'      => 'Poland',
			'continent' => 'EU',
		),
		'pm' => array(
			'name'      => 'Saint Pierre and Miquelon',
			'continent' => 'NA',
		),
		'pn' => array(
			'name'      => 'Pitcairn Islands',
			'continent' => 'OC',
		),
		'pr' => array(
			'name'      => 'Puerto Rico',
			'continent' => 'NA',
		),
		'ps' => array(
			'name'      => 'Palestinian Territory',
			'continent' => 'AS',
		),
		'pt' => array(
			'name'      => 'Portugal',
			'continent' => 'EU',
		),
		'pw' => array(
			'name'      => 'Palau',
			'continent' => 'OC',
		),
		'py' => array(
			'name'      => 'Paraguay',
			'continent' => 'SA',
		),
		'qa' => array(
			'name'      => 'Qatar',
			'continent' => 'AS',
		),
		're' => array(
			'name'      => 'Réunion',
			'continent' => 'AF',
		),
		'ro' => array(
			'name'      => 'Romania',
			'continent' => 'EU',
		),
		'rs' => array(
			'name'      => 'Serbia',
			'continent' => 'EU',
		),
		'ru' => array(
			'name'      => 'Russian Federation',
			'continent' => 'EU',
		),
		'rw' => array(
			'name'      => 'Rwanda',
			'continent' => 'AF',
		),
		'sa' => array(
			'name'      => 'Saudi Arabia',
			'continent' => 'AS',
		),
		'sb' => array(
			'name'      => 'Solomon Islands',
			'continent' => 'OC',
		),
		'sc' => array(
			'name'      => 'Seychelles',
			'continent' => 'AF',
		),
		'sd' => array(
			'name'      => 'Sudan',
			'continent' => 'AF',
		),
		'se' => array(
			'name'      => 'Sweden',
			'continent' => 'EU',
		),
		'sg' => array(
			'name'      => 'Singapore',
			'continent' => 'AS',
		),
		'sh' => array(
			'name'      => 'Saint Helena, Ascension and Tristan da Cunha',
			'continent' => 'AF',
		),
		'si' => array(
			'name'      => 'Slovenia',
			'continent' => 'EU',
		),
		'sj' => array(
			'name'      => 'Svalbard & Jan Mayen Islands',
			'continent' => 'EU',
		),
		'sk' => array(
			'name'      => 'Slovakia (Slovak Republic)',
			'continent' => 'EU',
		),
		'sl' => array(
			'name'      => 'Sierra Leone',
			'continent' => 'AF',
		),
		'sm' => array(
			'name'      => 'San Marino',
			'continent' => 'EU',
		),
		'sn' => array(
			'name'      => 'Senegal',
			'continent' => 'AF',
		),
		'so' => array(
			'name'      => 'Somalia',
			'continent' => 'AF',
		),
		'sr' => array(
			'name'      => 'Suriname',
			'continent' => 'SA',
		),
		'ss' => array(
			'name'      => 'South Sudan',
			'continent' => 'AF',
		),
		'st' => array(
			'name'      => 'Sao Tome and Principe',
			'continent' => 'AF',
		),
		'sv' => array(
			'name'      => 'El Salvador',
			'continent' => 'NA',
		),
		'sx' => array(
			'name'      => 'Sint Maarten (Dutch part)',
			'continent' => 'NA',
		),
		'sy' => array(
			'name'      => 'Syrian Arab Republic',
			'continent' => 'AS',
		),
		'sz' => array(
			'name'      => 'Swaziland',
			'continent' => 'AF',
		),
		'tc' => array(
			'name'      => 'Turks and Caicos Islands',
			'continent' => 'NA',
		),
		'td' => array(
			'name'      => 'Chad',
			'continent' => 'AF',
		),
		'tf' => array(
			'name'      => 'French Southern Territories',
			'continent' => 'AN',
		),
		'tg' => array(
			'name'      => 'Togo',
			'continent' => 'AF',
		),
		'th' => array(
			'name'      => 'Thailand',
			'continent' => 'AS',
		),
		'tj' => array(
			'name'      => 'Tajikistan',
			'continent' => 'AS',
		),
		'tk' => array(
			'name'      => 'Tokelau',
			'continent' => 'OC',
		),
		'tl' => array(
			'name'      => 'Timor-Leste',
			'continent' => 'AS',
		),
		'tm' => array(
			'name'      => 'Turkmenistan',
			'continent' => 'AS',
		),
		'tn' => array(
			'name'      => 'Tunisia',
			'continent' => 'AF',
		),
		'to' => array(
			'name'      => 'Tonga',
			'continent' => 'OC',
		),
		'tr' => array(
			'name'      => 'Turkey',
			'continent' => 'AS',
		),
		'tt' => array(
			'name'      => 'Trinidad and Tobago',
			'continent' => 'NA',
		),
		'tv' => array(
			'name'      => 'Tuvalu',
			'continent' => 'OC',
		),
		'tw' => array(
			'name'      => 'Taiwan',
			'continent' => 'AS',
		),
		'tz' => array(
			'name'      => 'Tanzania',
			'continent' => 'AF',
		),
		'ua' => array(
			'name'      => 'Ukraine',
			'continent' => 'EU',
		),
		'ug' => array(
			'name'      => 'Uganda',
			'continent' => 'AF',
		),
		'um' => array(
			'name'      => 'United States Minor Outlying Islands',
			'continent' => 'OC',
		),
		'us' => array(
			'name'      => 'United States of America',
			'continent' => 'NA',
		),
		'uy' => array(
			'name'      => 'Uruguay',
			'continent' => 'SA',
		),
		'uz' => array(
			'name'      => 'Uzbekistan',
			'continent' => 'AS',
		),
		'va' => array(
			'name'      => 'Holy See (Vatican City State)',
			'continent' => 'EU',
		),
		'vc' => array(
			'name'      => 'Saint Vincent and the Grenadines',
			'continent' => 'NA',
		),
		've' => array(
			'name'      => 'Venezuela',
			'continent' => 'SA',
		),
		'vg' => array(
			'name'      => 'British Virgin Islands',
			'continent' => 'NA',
		),
		'vi' => array(
			'name'      => 'United States Virgin Islands',
			'continent' => 'NA',
		),
		'vn' => array(
			'name'      => 'Vietnam',
			'continent' => 'AS',
		),
		'vu' => array(
			'name'      => 'Vanuatu',
			'continent' => 'OC',
		),
		'wf' => array(
			'name'      => 'Wallis and Futuna',
			'continent' => 'OC',
		),
		'ws' => array(
			'name'      => 'Samoa',
			'continent' => 'OC',
		),
		'ye' => array(
			'name'      => 'Yemen',
			'continent' => 'AS',
		),
		'yt' => array(
			'name'      => 'Mayotte',
			'continent' => 'AF',
		),
		'za' => array(
			'name'      => 'South Africa',
			'continent' => 'AF',
		),
		'zm' => array(
			'name'      => 'Zambia',
			'continent' => 'AF',
		),
		'zw' => array(
			'name'      => 'Zimbabwe',
			'continent' => 'AF',
		),
		'xx' => array(
			'name'      => 'Localhost',
			'continent' => '',
		),
	);

	public function __construct() {
		$this->method = coreactivity_settings()->get( 'geolocation_method' );

		if ( $this->method == 'ip2download' && ! $this->is_ip2location_valid() ) {
			$this->method = 'online';
		}

		if ( $this->method == 'geoip2' && ! $this->is_geoip2_valid() ) {
			$this->method = 'online';
		}
	}

	public static function instance() : GEO {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new GEO();
		}

		return $instance;
	}

	public function is_using_library() : bool {
		return $this->method != 'online';
	}

	public function bulk( array $ips ) {
		if ( $this->method == 'ip2download' ) {
			IP2Location::instance()->bulk( $ips );
		} else if ( $this->method == 'geoip2' ) {
			GeoIP2::instance()->bulk( $ips );
		} else {
			GEOJSIO::instance()->bulk( $ips );
		}
	}

	public function locate( string $ip ) : ?Location {
		if ( $this->method == 'ip2download' ) {
			return IP2Location::instance()->locate( $ip );
		} else if ( $this->method == 'geoip2' ) {
			return GeoIP2::instance()->locate( $ip );
		} else {
			return GEOJSIO::instance()->locate( $ip );
		}
	}

	public function flag( string $ip ) : string {
		$location = $this->locate( $ip );

		if ( $location instanceof Location ) {
			return $location->flag();
		}

		return '';
	}

	public function flag_from_country( string $code ) : string {
		$continent = $this->continent( $code );
		$location  = $this->country( $code ) . ( empty( $continent ) ? '' : ' (' . $continent . ')' );

		return Misc::flag_from_country_code( $code, $location, strtolower( $code ) !== 'xx' ? 'active' : 'private' );
	}

	public function country( string $code ) : string {
		return isset( $this->codes[ strtolower( $code ) ] ) ? $this->codes[ strtolower( $code ) ]['name'] : 'Unknown';
	}

	public function continent( string $code ) : string {
		$list = Data::list_of_continents();
		$code = isset( $this->codes[ strtolower( $code ) ] ) ? $this->codes[ strtolower( $code ) ]['continent'] : '';

		return $list[ $code ] ?? '';
	}

	public function is_geoip2_valid() : bool {
		$path = coreactivity_settings()->get( 'geoip2_db', 'core' );

		return ! empty( $path ) && file_exists( $path );
	}

	public function geoip2_db_update() {
		$path = Plugin::instance()->uploads_path();

		if ( $path !== false ) {
			$path = trailingslashit( $path ) . 'geoip2/';
			$path = wp_normalize_path( $path );

			if ( wp_mkdir_p( $path ) ) {
				require_once ABSPATH . '/wp-admin/includes/file.php';

				WP_Filesystem();

				$_license = coreactivity_settings()->get( 'geolocation_geoip2_license' );
				$_db      = coreactivity_settings()->get( 'geolocation_geoip2_db' );

				coreactivity_settings()->set( 'geoip2_attempt', time(), 'core' );

				if ( ! empty( $_license ) ) {
					$_url = sprintf( 'https://download.maxmind.com/app/geoip_download?edition_id=%s&license_key=%s&suffix=tar.gz', $_db, $_license );

					$temp = download_url( $_url );

					if ( ! is_wp_error( $temp ) ) {
						$done = File::unpack_tar_gz( $temp, $path );

						if ( ! is_wp_error( $done ) ) {
							$vars = File::scan_dir( $path, 'folders', array(), '/' . $_db . '/' );

							rsort( $vars );

							if ( count( $vars ) > 1 ) {
								$dir   = new WP_Filesystem_Direct( 0 );
								$limit = count( $vars );

								for ( $i = 1; $i < $limit; $i ++ ) {
									$dir->rmdir( $path . $vars[ $i ], true );
								}
							}

							$file_path = $path . $vars[0] . '/' . $_db . '.mmdb';

							if ( file_exists( $file_path ) ) {
								coreactivity_settings()->set( 'geoip2_timestamp', time(), 'core' );
								coreactivity_settings()->set( 'geoip2_db', $file_path, 'core' );
								coreactivity_settings()->set( 'geoip2_error', '', 'core' );
							} else {
								coreactivity_settings()->set( 'geoip2_error', __( 'File is Missing', 'coreactivity' ), 'core' );
							}
						} else {
							coreactivity_settings()->set( 'geoip2_error', $done->get_error_message(), 'core' );
						}
					} else {
						coreactivity_settings()->set( 'geoip2_error', $temp->get_error_message(), 'core' );
					}
				} else {
					coreactivity_settings()->set( 'geoip2_error', __( 'Token Missing', 'coreactivity' ), 'core' );
				}

				coreactivity_settings()->save( 'core' );
			}
		}
	}

	public function geoip2() : ?Reader {
		if ( ! ( $this->geoip2 instanceof Reader ) ) {
			require_once COREACTIVITY_PATH . 'vendor/geoip2/autoload.php';

			$path = coreactivity_settings()->get( 'geoip2_db', 'core' );

			if ( ! empty( $path ) && file_exists( $path ) ) {
				$this->geoip2 = new Reader( $path );
			}
		}

		return $this->geoip2;
	}

	public function is_ip2location_valid() : bool {
		$path = coreactivity_settings()->get( 'ip2location_db', 'core' );

		return ! empty( $path ) && file_exists( $path );
	}

	public function ip2location_db_update() {
		$path = Plugin::instance()->uploads_path();

		if ( $path !== false ) {
			$path = trailingslashit( $path ) . 'ip2location/';
			$path = wp_normalize_path( $path );

			if ( wp_mkdir_p( $path ) ) {
				require_once ABSPATH . '/wp-admin/includes/file.php';

				WP_Filesystem();

				$_token = coreactivity_settings()->get( 'geolocation_ip2location_token' );
				$_db    = coreactivity_settings()->get( 'geolocation_ip2location_db' );

				coreactivity_settings()->set( 'ip2location_attempt', time(), 'core' );

				if ( ! empty( $_token ) ) {
					$_url = sprintf( 'https://www.ip2location.com/download/?token=%s&file=%s', $_token, $_db );

					$temp = download_url( $_url );

					if ( ! is_wp_error( $temp ) ) {
						$done = unzip_file( $temp, $path );

						if ( ! is_wp_error( $done ) ) {
							$file_name = $this->ip2location_file_name( $_db );
							$file_path = wp_normalize_path( $path . $file_name );

							if ( file_exists( $file_path ) ) {
								coreactivity_settings()->set( 'ip2location_timestamp', time(), 'core' );
								coreactivity_settings()->set( 'ip2location_db', $file_path, 'core' );
								coreactivity_settings()->set( 'ip2location_error', '', 'core' );
							}
						} else {
							coreactivity_settings()->set( 'ip2location_error', $done->get_error_message(), 'core' );
						}
					} else {
						coreactivity_settings()->set( 'ip2location_error', $temp->get_error_message(), 'core' );
					}
				} else {
					coreactivity_settings()->set( 'ip2location_error', __( 'Token Missing', 'coreactivity' ), 'core' );
				}

				coreactivity_settings()->save( 'core' );
			}
		}
	}

	public function ip2location() : ?Database {
		if ( ! ( $this->ip2download instanceof Database ) ) {
			require_once COREACTIVITY_PATH . 'vendor/ip2location/autoload.php';

			$path = coreactivity_settings()->get( 'ip2location_db', 'core' );

			if ( ! empty( $path ) && file_exists( $path ) ) {
				$this->ip2download = new Database( $path, Database::FILE_IO );
			}
		}

		return $this->ip2download;
	}

	private function ip2location_file_name( string $db ) : string {
		$list = array(
			'DB1LITEBINIPV6'  => 'IP2LOCATION-LITE-DB1.IPV6.BIN',
			'DB3LITEBINIPV6'  => 'IP2LOCATION-LITE-DB3.IPV6.BIN',
			'DB5LITEBINIPV6'  => 'IP2LOCATION-LITE-DB5.IPV6.BIN',
			'DB9LITEBINIPV6'  => 'IP2LOCATION-LITE-DB9.IPV6.BIN',
			'DB11LITEBINIPV6' => 'IP2LOCATION-LITE-DB11.IPV6.BIN',
		);

		return $list[ $db ] ?? '';
	}
}
