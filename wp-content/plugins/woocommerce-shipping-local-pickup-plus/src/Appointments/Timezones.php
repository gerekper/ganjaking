<?php
/**
 * WooCommerce Local Pickup Plus
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Local Pickup Plus to newer
 * versions in the future. If you wish to customize WooCommerce Local Pickup Plus for your
 * needs please refer to http://docs.woocommerce.com/document/local-pickup-plus/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2022, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Local_Pickup_Plus\Appointments;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * Timezones helper class.
 *
 * @since 2.7.0
 */
class Timezones {


	/**
	 * Evaluates whether a timezone is equivalent to the site timezone.
	 *
	 * @since 2.7.0
	 *
	 * @param \DateTimeZone $timezone a timezone object
	 * @return bool
	 */
	public static function is_site_timezone( \DateTimeZone $timezone ) {

		return $timezone->getName() === wc_timezone_string() || $timezone->getOffset() === wc_timezone_offset();
	}


	/**
	 * Evaluates whether two timezones are identical.
	 *
	 * @since 2.7.0
	 *
	 * @param \DateTimeZone $timezone_a first timezone to compare
	 * @param \DateTimeZone $timezone_b second timezone to compare
	 * @return bool
	 */
	public static function is_same_timezone( \DateTimeZone $timezone_a, \DateTimeZone $timezone_b ) {

		return $timezone_a->getName() === $timezone_b->getName();
	}


	/**
	 * Gets a timezone from an address.
	 *
	 * @since 2.7.0
	 *
	 * @param \WC_Local_Pickup_Plus_Address $address an address object
	 * @param null|string $default default timezone to return in case of no match or error (defaults to site timezone when unspecified)
	 * @return \DateTimeZone defaults to site timezone
	 */
	public static function get_timezone_from_address( $address, $default = null ) {

		// will handle address object validity and default timezone
		$timezone = self::get_timezone_identifier_from_address( $address, $default );

		try {

			$timezone = new \DateTimeZone( $timezone );

		} catch ( \Exception $e ) {

			$site_tz = wc_timezone_string();

			wc_local_pickup_plus()->log( sprintf( 'Error while parsing parse timezone identifier %1$s, will make another attempt using the site timezone (%2$s): %3$s', $timezone, $site_tz, $e->getMessage() ) );

			try {

				$timezone = new \DateTimeZone( $site_tz );

			} catch ( \Exception $e ) {

				// this shouldn't really happen as the site timezone should always be a valid identifier
				wc_local_pickup_plus()->log( sprintf( 'Error while parsing timezone identifier %1$s, will make another attempt in UTC: %2$s', $timezone, $e->getMessage() ) );

				$timezone = new \DateTimeZone( 'UTC' );
			}
		}

		return $timezone;
	}


	/**
	 * Gets a timezone identifier from an address.
	 *
	 * @since 2.7.0
	 *
	 * @param \WC_Local_Pickup_Plus_Address $address an address object
	 * @param null|string $default default timezone to return in case of no match or error (defaults to site timezone when unspecified)
	 * @return string timezone identifier (defaults to site timezone)
	 */
	public static function get_timezone_identifier_from_address( $address, $default = null ) {

		$identifier = ! is_string( $default ) ? wc_timezone_string() : $default;
		$country    = $address instanceof \WC_Local_Pickup_Plus_Address ? $address->get_country() : '';
		$state      = $address instanceof \WC_Local_Pickup_Plus_Address ? $address->get_state() : '';

		switch ( $country ) {

			case 'AD':
				$identifier = 'Europe/Andorra';
			break;

			case 'AE':
				$identifier = 'Asia/Dubai';
			break;

			case 'AF':
				$identifier = 'Asia/Kabul';
			break;

			case 'AG':
				$identifier = 'America/Antigua';
			break;

			case 'AI':
				$identifier = 'America/Anguilla';
			break;

			case 'AL':
				$identifier = 'Europe/Tirane';
			break;

			case 'AM':
				$identifier = 'Asia/Yerevan';
			break;

			case 'AN':
			case 'SX':
				$identifier = 'America/Curacao';
			break;

			case 'AO':
				$identifier = 'Africa/Luanda';
			break;

			case 'AQ':
				$identifier = 'Antarctica/South_Pole';
			break;

			case 'AR':

				switch ( $state ) {

					case 'B':
					case 'C':
					default:
						$identifier = 'America/Argentina/Buenos_Aires';
					break;

					case 'K':
					case 'U':
						$identifier = 'America/Argentina/Catamarca';
					break;

					case 'T':
						$identifier = 'America/Argentina/Tucuman';
					break;

					case 'Z':
						$identifier = 'America/Argentina/Rio_Gallegos';
					break;

					case 'E':
					case 'G':
					case 'H':
					case 'N':
					case 'P':
					case 'S':
					case 'W':
					case 'X':
						$identifier = 'America/Argentina/Cordoba';
					break;

					case 'Y':
						$identifier = 'America/Argentina/Jujuy';
					break;

					case 'D':
						$identifier = 'America/Argentina/San_Luis';
					break;

					case 'F':
						$identifier = 'America/Argentina/La_Rioja';
					break;

					case 'M':
						$identifier = 'America/Argentina/Mendoza';
					break;

					case 'A':
					case 'L':
					case 'Q':
					case 'R':
						$identifier = 'America/Argentina/Salta';
					break;

					case 'J':
						$identifier = 'America/Argentina/San_Juan';
					break;

					case 'V':
						$identifier = 'America/Argentina/Ushuaia';
					break;
				}

			break;

			case 'AS':
				$identifier = 'Pacific/Pago_Pago';
			break;

			case 'AT':
				$identifier = 'Europe/Vienna';
			break;

			case 'AU':

				switch ( $state ) {

					case 'ACT':
					case 'NSW':
					default:
						$identifier = 'Australia/Sydney';
					break;

					case 'NT':
						$identifier = 'Australia/Darwin';
					break;

					case 'QLD':
						$identifier = 'Australia/Brisbane';
					break;

					case 'SA':
						$identifier = 'Australia/Adelaide';
					break;

					case 'TAS':
						$identifier = 'Australia/Hobart';
					break;

					case 'VIC':
						$identifier = 'Australia/Melbourne';
					break;

					case 'WA':
						$identifier = 'Australia/Perth';
					break;
				}

			break;

			case 'AW':
				$identifier = 'America/Aruba';
			break;

			case 'AX':
				$identifier = 'Europe/Mariehamn';
			break;

			case 'AZ':
				$identifier = 'Asia/Baku';
			break;

			case 'BA':
				$identifier = 'Europe/Sarajevo';
			break;

			case 'BB':
				$identifier = 'America/Barbados';
			break;

			case 'BD':
				$identifier = 'Asia/Dhaka';
			break;

			case 'BE':
				$identifier = 'Europe/Brussels';
			break;

			case 'BF':
				$identifier = 'Africa/Ouagadougou';
			break;
			case 'BG':
				$identifier = 'Europe/Sofia';
			break;

			case 'BH':
				$identifier = 'Asia/Bahrain';
			break;

			case 'BI':
				$identifier = 'Africa/Bujumbura';
			break;

			case 'BJ':
				$identifier = 'Africa/Porto-Novo';
			break;

			case 'BL':
				$identifier = 'America/St_Barthelemy';
			break;

			case 'BM':
				$identifier = 'Atlantic/Bermuda';
			break;

			case 'BN':
				$identifier = 'Asia/Brunei';
			break;

			case 'BO':
				$identifier = 'America/La_Paz';
			break;

			case 'BQ':
				$identifier = 'America/Curacao';
			break;

			case 'BR':

				switch ( $state ) {

					case 'AC':
						$identifier = 'America/Rio_Branco';
					break;

					case 'AL':
					case 'PI':
					case 'SE':
						$identifier = 'America/Maceio';
					break;

					case 'DF':
					case 'ES':
					case 'MG':
					case 'PR':
					case 'RJ':
					case 'RS':
					case 'SC':
					case 'SP':
					default:
						$identifier = 'America/Sao_Paulo';
					break;

					case 'AM':
						$identifier = 'America/Manaus';
					break;

					case 'BA':
						$identifier = 'America/Bahia';
					break;

					case 'CE':
					case 'MA':
					case 'RN':
						$identifier = 'America/Fortaleza';
					break;

					case 'MS':
						$identifier = 'America/Campo_Grande';
					break;

					case 'AP':
					case 'PA':
						$identifier = 'America/Belem';
					break;

					case 'MT':
						$identifier = 'America/Cuiaba';
					break;

					case 'PB':
						$identifier = 'America/Recife';
					break;

					case 'RO':
						$identifier = 'America/Porto_Velho';
					break;

					case 'RR':
						$identifier = 'America/Boa_Vista';
					break;

					case 'GO':
					case 'TO':
						$identifier = 'America/Araguaina';
					break;
				}

			break;

			case 'BS':
				$identifier = 'America/Nassau';
			break;

			case 'BT':
				$identifier = 'Asia/Thimphu';
			break;

			case 'BV':
				$identifier = 'Antarctica/Syowa';
			break;

			case 'BW':
				$identifier = 'Africa/Gaborone';
			break;

			case 'BY':
				$identifier = 'Europe/Minsk';
			break;

			case 'BZ':
				$identifier = 'America/Belize';
			break;

			case 'CA':

				switch ( $state ) {

					case 'AB':
						$identifier = 'America/Edmonton';
					break;

					case 'BC':
						$identifier = 'America/Vancouver';
					break;

					case 'MB':
						$identifier = 'America/Winnipeg';
					break;

					case 'NB':
					case 'NS':
					case 'PE':
						$identifier = 'America/Halifax';
					break;

					case 'NL':
						$identifier = 'America/St_Johns';
					break;

					case 'NT':
						$identifier = 'America/Yellowknife';
					break;

					case 'NU':
						$identifier = 'America/Rankin_Inlet';
					break;

					case 'ON':
					default:
						$identifier = 'America/Toronto';
					break;

					case 'QC':
						$identifier = 'America/Montreal';
					break;

					case 'SK':
						$identifier = 'America/Regina';
					break;

					case 'YT':
						$identifier = 'America/Whitehorse';
					break;
				}

			break;

			case 'CC':
				$identifier = 'Indian/Cocos';
			break;

			case 'CD':
				$identifier = 'Africa/Kinshasa';
			break;

			case 'CF':
				$identifier = 'Africa/Bangui';
			break;

			case 'CG':
				$identifier = 'Africa/Brazzaville';
			break;

			case 'CH':
				$identifier = 'Europe/Zurich';
			break;

			case 'CI':
				$identifier = 'Africa/Abidjan';
			break;

			case 'CK':
				$identifier = 'Pacific/Rarotonga';
			break;

			case 'CL':
				$identifier = 'America/Santiago';
			break;

			case 'CM':
				$identifier = 'Africa/Lagos';
			break;

			case 'CN':

				switch ( $state ) {

					case 'CN1':
					case 'CN2':
					case 'CN3':
					case 'CN4':
					case 'CN7':
					case 'CN9':
					case 'CN10':
					case 'CN12':
					case 'CN23':
					case 'CN25':
					case 'CN28':
					default:
						$identifier = 'Asia/Shanghai';
					break;

					case 'CN5':
					case 'CN8':
					case 'CN19':
					case 'CN20':
					case 'CN22':
						$identifier = 'Asia/Harbin';
					break;

					case 'CN6':
					case 'CN11':
					case 'CN14':
					case 'CN15':
					case 'CN16':
					case 'CN18':
					case 'CN21':
					case 'CN24':
					case 'CN26':
					case 'CN29':
					case 'CN30':
					case 'CN31':
						$identifier = 'Asia/Chongqing';
					break;

					case 'CN32':
						$identifier = 'Asia/Urumqi';
					break;
				}

			break;

			case 'CO':
				$identifier = 'America/Bogota';
			break;

			case 'CR':
				$identifier = 'America/Costa_Rica';
			break;

			case 'CU':
				$identifier = 'America/Havana';
			break;

			case 'CV':
				$identifier = 'Atlantic/Cape_Verde';
			break;

			case 'CW':
				$identifier = 'America/Curacao';
			break;

			case 'CX':
				$identifier = 'Indian/Christmas';
			break;

			case 'CY':
				$identifier = 'Asia/Nicosia';
			break;

			case 'CZ':
				$identifier = 'Europe/Prague';
			break;

			case 'DE':
				$identifier = 'Europe/Berlin';
			break;

			case 'DJ':
				$identifier = 'Africa/Djibouti';
			break;

			case 'DK':
				$identifier = 'Europe/Copenhagen';
			break;

			case 'DM':
				$identifier = 'America/Dominica';
			break;

			case 'DO':
				$identifier = 'America/Santo_Domingo';
			break;

			case 'DZ':
				$identifier = 'Africa/Algiers';
			break;

			case 'EC':
				$identifier = 'America/Guayaquil';
			break;

			case 'EE':
				$identifier = 'Europe/Tallinn';
			break;

			case 'EG':
				$identifier = 'Africa/Cairo';
			break;

			case 'EH':
				$identifier = 'Africa/El_Aaiun';
			break;

			case 'ER':
				$identifier = 'Africa/Asmara';
			break;

			case 'ES':

				switch ( $state ) {

					case 'C':
					case 'VI':
					case 'AB':
					case 'A':
					case 'AL':
					case 'O':
					case 'AV':
					case 'BA':
					case 'PM':
					case 'B':
					case 'BU':
					case 'CC':
					case 'CA':
					case 'S':
					case 'CS':
					case 'CR':
					case 'CO':
					case 'CU':
					case 'GI':
					case 'GR':
					case 'GU':
					case 'SS':
					case 'H':
					case 'HU':
					case 'J':
					case 'LO':
					case 'LE':
					case 'L':
					case 'LU':
					case 'M':
					case 'MA':
					case 'MU':
					case 'NA':
					case 'OR':
					case 'P':
					case 'PO':
					case 'SA':
					case 'SG':
					case 'SE':
					case 'SO':
					case 'T':
					case 'TE':
					case 'TO':
					case 'V':
					case 'VA':
					case 'BI':
					case 'ZA':
					case 'Z':
					default:
						$identifier = 'Europe/Madrid';
					break;

					case 'CE':
					case 'ML':
						$identifier = 'Africa/Ceuta';
					break;

					case 'GC':
					case 'TF':
						$identifier = 'Atlantic/Canary';
					break;
				}

			break;

			case 'ET':
				$identifier = 'Africa/Addis_Ababa';
			break;

			case 'FI':
				$identifier = 'Europe/Helsinki';
			break;

			case 'FJ':
				$identifier = 'Pacific/Fiji';
			break;

			case 'FK':
				$identifier = 'Atlantic/Stanley';
			break;

			case 'FM':
				$identifier = 'Pacific/Pohnpei';
			break;

			case 'FO':
				$identifier = 'Atlantic/Faroe';
			break;

			case 'FR':
			case 'FX':
				$identifier = 'Europe/Paris';
			break;

			case 'GA':
				$identifier = 'Africa/Libreville';
			break;

			case 'GB':
				$identifier = 'Europe/London';
			break;

			case 'GD':
				$identifier = 'America/Grenada';
			break;

			case 'GE':
				$identifier = 'Asia/Tbilisi';
			break;

			case 'GF':
				$identifier = 'America/Cayenne';
			break;

			case 'GG':
				$identifier = 'Europe/Guernsey';
			break;

			case 'GH':
				$identifier = 'Africa/Accra';
			break;

			case 'GI':
				$identifier = 'Europe/Gibraltar';
			break;

			case 'GL':
				$identifier = 'America/Thule';
			break;

			case 'GM':
				$identifier = 'Africa/Banjul';
			break;

			case 'GN':
				$identifier = 'Africa/Conakry';
			break;

			case 'GP':
				$identifier = 'America/Guadeloupe';
			break;

			case 'GQ':
				$identifier = 'Africa/Malabo';
			break;

			case 'GR':
				$identifier = 'Europe/Athens';
			break;

			case 'GS':
				$identifier = 'Atlantic/South_Georgia';
			break;

			case 'GT':
				$identifier = 'America/Guatemala';
			break;

			case 'GU':
				$identifier = 'Pacific/Guam';
			break;

			case 'GW':
				$identifier = 'Africa/Bissau';
			break;

			case 'GY':
				$identifier = 'America/Guyana';
			break;

			case 'HK':
				$identifier = 'Asia/Hong_Kong';
			break;

			case 'HN':
				$identifier = 'America/Tegucigalpa';
			break;

			case 'HR':
				$identifier = 'Europe/Zagreb';
			break;

			case 'HT':
				$identifier = 'America/Port-au-Prince';
			break;

			case 'HU':
				$identifier = 'Europe/Budapest';
			break;

			case 'ID':

				switch ( $state ) {

					case 'KB':
					case 'KI':
					case 'KT':
					case 'KR':
						$identifier = 'Asia/Pontianak';
					break;

					case 'BA':
					case 'GO':
					case 'KS':
					case 'KU':
					case 'NB':
					case 'NT':
					case 'SA':
					case 'SG':
					case 'SN':
					case 'SR':
					case 'ST':
						$identifier = 'Asia/Makassar';
					break;

					case 'AC':
					case 'BB':
					case 'BE':
					case 'BT':
					case 'JA':
					case 'JB':
					case 'JK':
					case 'JI':
					case 'JT':
					case 'LA':
					case 'RI':
					case 'SB':
					case 'SS':
					case 'SU':
					case 'YO':
					default:
						$identifier = 'Asia/Jakarta';
					break;

					case 'MA':
					case 'MU':
					case 'PA':
					case 'PB':
						$identifier = 'Asia/Jayapura';
					break;
				}

			break;

			case 'IE':
				$identifier = 'Europe/Dublin';
			break;

			case 'IL':
				$identifier = 'Asia/Jerusalem';
			break;

			case 'IM':
				$identifier = 'Europe/Isle_of_Man';
			break;

			case 'IN':
				$identifier = 'Asia/Kolkata';
			break;

			case 'IO':
				$identifier = 'Indian/Chagos';
			break;

			case 'IQ':
				$identifier = 'Asia/Baghdad';
			break;

			case 'IR':
				$identifier = 'Asia/Tehran';
			break;

			case 'IS':
				$identifier = 'Atlantic/Reykjavik';
			break;

			case 'IT':
				$identifier = 'Europe/Rome';
			break;

			case 'JE':
				$identifier = 'Europe/Jersey';
			break;

			case 'JM':
				$identifier = 'America/Jamaica';
			break;

			case 'JO':
				$identifier = 'Asia/Amman';
			break;

			case 'JP':
				$identifier = 'Asia/Tokyo';
			break;

			case 'KE':
				$identifier = 'Africa/Nairobi';
			break;

			case 'KG':
				$identifier = 'Asia/Bishkek';
			break;

			case 'KH':
			case 'VN':
				$identifier = 'Asia/Phnom_Penh';
			break;

			case 'KI':
				$identifier = 'Pacific/Tarawa';
			break;

			case 'KM':
				$identifier = 'Indian/Comoro';
			break;

			case 'KN':
				$identifier = 'America/St_Kitts';
			break;

			case 'KP':
				$identifier = 'Asia/Pyongyang';
			break;

			case 'KR':
				$identifier = 'Asia/Seoul';
			break;

			case 'KW':
				$identifier = 'Asia/Kuwait';
			break;

			case 'KY':
				$identifier = 'America/Cayman';
			break;

			case 'KZ':
				$identifier = 'Asia/Almaty';
			break;

			case 'LA':
				$identifier = 'Asia/Vientiane';
			break;

			case 'LB':
				$identifier = 'Asia/Beirut';
			break;

			case 'LC':
				$identifier = 'America/St_Lucia';
			break;

			case 'LI':
				$identifier = 'Europe/Vaduz';
			break;

			case 'LK':
				$identifier = 'Asia/Colombo';
			break;

			case 'LR':
				$identifier = 'Africa/Monrovia';
			break;

			case 'LS':
				$identifier = 'Africa/Maseru';
			break;

			case 'LT':
				$identifier = 'Europe/Vilnius';
			break;

			case 'LU':
				$identifier = 'Europe/Luxembourg';
			break;

			case 'LV':
				$identifier = 'Europe/Riga';
			break;

			case 'LY':
				$identifier = 'Africa/Tripoli';
			break;

			case 'MA':
				$identifier = 'Africa/Casablanca';
			break;

			case 'MC':
				$identifier = 'Europe/Monaco';
			break;

			case 'MD':
				$identifier = 'Europe/Chisinau';
			break;

			case 'ME':
				$identifier = 'Europe/Podgorica';
			break;

			case 'MF':
				$identifier = 'America/Marigot';
			break;

			case 'MG':
				$identifier = 'Indian/Antananarivo';
			break;

			case 'MH':
				$identifier = 'Pacific/Kwajalein';
			break;

			case 'MK':
				$identifier = 'Europe/Skopje';
			break;

			case 'ML':
				$identifier = 'Africa/Bamako';
			break;

			case 'MM':
				$identifier = 'Asia/Rangoon';
			break;

			case 'MN':
				$identifier = 'Asia/Ulaanbaatar';
			break;

			case 'MO':
				$identifier = 'Asia/Macau';
			break;

			case 'MP':
				$identifier = 'Pacific/Saipan';
			break;

			case 'MQ':
				$identifier = 'America/Martinique';
			break;

			case 'MR':
				$identifier = 'Africa/Nouakchott';
			break;

			case 'MS':
				$identifier = 'America/Montserrat';
			break;

			case 'MT':
				$identifier = 'Europe/Malta';
			break;

			case 'MU':
				$identifier = 'Indian/Mauritius';
			break;

			case 'MV':
				$identifier = 'Indian/Maldives';
			break;

			case 'MW':
				$identifier = 'Africa/Blantyre';
			break;

			case 'MX':

				switch ( $state ) {

					case 'AG':
					case 'CL':
					case 'DF':
					case 'GR':
					case 'GT':
					case 'HG':
					case 'JA':
					case 'MI':
					case 'MO':
					case 'MX':
					case 'OA':
					case 'PU':
					case 'QT':
					case 'SL':
					case 'TB':
					case 'TL':
					case 'VE':
					default:
						$identifier = 'America/Mexico_City';
					break;

					case 'BC':
						$identifier = 'America/Tijuana';
					break;

					case 'SO':
						$identifier = 'America/Hermosillo';
					break;

					case 'CM':
					case 'CS':
					case 'YT':
						$identifier = 'America/Merida';
					break;

					case 'CH':
						$identifier = 'America/Chihuahua';
					break;

					case 'CO':
					case 'DG':
					case 'NL':
					case 'TM':
					case 'ZA':
						$identifier = 'America/Monterrey';
					break;

					case 'BS':
					case 'NA':
					case 'SI':
						$identifier = 'America/Mazatlan';
					break;

					case 'QR':
						$identifier = 'America/Cancun';
					break;
				}

			break;

			case 'MY':

				switch ( $state ) {

					case 'JHR':
					case 'KDH':
					case 'KTN':
					case 'KUL':
					case 'MLK':
					case 'NSN':
					case 'PHG':
					case 'PRK':
					case 'PLS':
					case 'SGR':
					case 'TRG':
					case 'PJY':
					default:
						$identifier = 'Asia/Kuala_Lumpur';
					break;

					case 'LBN':
					case 'SWK':
					case 'SBH':
						$identifier = 'Asia/Kuching';
					break;
				}

			break;

			case 'MZ':
				$identifier = 'Africa/Maputo';
			break;

			case 'NA':
				$identifier = 'Africa/Windhoek';
			break;

			case 'NC':
				$identifier = 'Pacific/Noumea';
			break;

			case 'NE':
				$identifier = 'Africa/Niamey';
			break;

			case 'NF':
				$identifier = 'Pacific/Norfolk';
			break;

			case 'NG':
				$identifier = 'Africa/Lagos';
			break;

			case 'NI':
				$identifier = 'America/Managua';
			break;

			case 'NL':
				$identifier = 'Europe/Amsterdam';
			break;

			case 'NO':
				$identifier = 'Europe/Oslo';
			break;

			case 'NP':
				$identifier = 'Asia/Kathmandu';
			break;

			case 'NR':
				$identifier = 'Pacific/Nauru';
			break;

			case 'NU':
				$identifier = 'Pacific/Niue';
			break;

			case 'NZ':
				$identifier = 'Pacific/Auckland';
			break;

			case 'OM':
				$identifier = 'Asia/Muscat';
			break;

			case 'PA':
				$identifier = 'America/Panama';
			break;

			case 'PE':
				$identifier = 'America/Lima';
			break;

			case 'PF':
				$identifier = 'Pacific/Marquesas';
			break;

			case 'PG':
				$identifier = 'Pacific/Port_Moresby';
			break;

			case 'PH':
				$identifier = 'Asia/Manila';
			break;

			case 'PK':
				$identifier = 'Asia/Karachi';
			break;

			case 'PL':
				$identifier = 'Europe/Warsaw';
			break;

			case 'PM':
				$identifier = 'America/Miquelon';
			break;

			case 'PN':
				$identifier = 'Pacific/Pitcairn';
			break;

			case 'PR':
				$identifier = 'America/Puerto_Rico';
			break;

			case 'PS':
				$identifier = 'Asia/Gaza';
			break;

			case 'PT':

				$identifier = 'Europe/Lisbon';

				// Note: the Azores have a different timezone. Continental Portugal and Madeira share the same time.
				// The following will try to determine the location from the address lines.

				if ( $address instanceof \WC_Local_Pickup_Plus_Address ) {

					$address_lines = $address->get_address_line_1() . ' ' . $address->get_address_line_2();

					if ( Framework\SV_WC_Helper::str_exists( strtolower( $address_lines ), 'madeira' ) ) {
						$identifier = 'Atlantic/Madeira';
					} elseif ( Framework\SV_WC_Helper::str_exists( strtolower( $address_lines ), 'azores' ) ) {
						$identifier = 'Atlantic/Azores';
					}
				}

			break;

			case 'PW':
				$identifier = 'Pacific/Palau';
			break;

			case 'PY':
				$identifier = 'America/Asuncion';
			break;

			case 'QA':
				$identifier = 'Asia/Qatar';
			break;

			case 'RE':
				$identifier = 'Indian/Reunion';
			break;

			case 'RO':
				$identifier = 'Europe/Bucharest';
			break;

			case 'RS':
			case 'YU':
				$identifier = 'Europe/Belgrade';
			break;

			case 'RU':

				/**
				 * TODO Russian Federation administrative divisions are not implemented in WooCommerce {FN 2019-12-02}
				 *
				 * Possible timezones:
				 *
				 * 'Europe/Kaliningrad'
				 * 'Europe/Moscow'
				 * 'Europe/Samara'
				 * 'Europe/Volgograd'
				 * 'Asia/Anadyr'
				 * 'Asia/Krasnoyarsk'
				 * 'Asia/Irkutsk'
				 * 'Asia/Novokuznetsk'
				 * 'Asia/Novosibirsk'
				 * 'Asia/Yekaterinburg'
				 * 'Asia/Vladivostok'
				 * 'Asia/Kamchatka'
				 * 'Asia/Omsk'
				 * 'Asia/Magadan'
				 * 'Asia/Yakutsk'
				 * 'Asia/Sakhalin'
				 */

				$identifier = 'Europe/Moscow';

			break;

			case 'RW':
				$identifier = 'Africa/Kigali';
			break;

			case 'SA':
				$identifier = 'Asia/Riyadh';
			break;

			case 'SB':
				$identifier = 'Pacific/Guadalcanal';
			break;

			case 'SC':
				$identifier = 'Indian/Mahe';
			break;

			case 'SD':
				$identifier = 'Africa/Khartoum';
			break;

			case 'SE':
				$identifier = 'Europe/Stockholm';
			break;

			case 'SG':
				$identifier = 'Asia/Singapore';
			break;

			case 'SH':
				$identifier = 'Atlantic/St_Helena';
			break;

			case 'SI':
				$identifier = 'Europe/Ljubljana';
			break;

			case 'SJ':
				$identifier = 'Arctic/Longyearbyen';
			break;

			case 'SK':
				$identifier = 'Europe/Bratislava';
			break;

			case 'SL':
				$identifier = 'Africa/Freetown';
			break;

			case 'SM':
				$identifier = 'Europe/San_Marino';
			break;

			case 'SN':
				$identifier = 'Africa/Dakar';
			break;

			case 'SO':
				$identifier = 'Africa/Mogadishu';
			break;

			case 'SR':
				$identifier = 'America/Paramaribo';
			break;

			case 'SS':
				$identifier = 'Africa/Juba';
			break;

			case 'ST':
				$identifier = 'Africa/Sao_Tome';
			break;

			case 'SV':
				$identifier = 'America/El_Salvador';
			break;

			case 'SY':
				$identifier = 'Asia/Damascus';
			break;

			case 'SZ':
				$identifier = 'Africa/Mbabane';
			break;

			case 'TC':
				$identifier = 'America/Grand_Turk';
			break;

			case 'TD':
				$identifier = 'Africa/Ndjamena';
			break;

			case 'TF':
				$identifier = 'Indian/Kerguelen';
			break;

			case 'TG':
				$identifier = 'Africa/Lome';
			break;

			case 'TH':
				$identifier = 'Asia/Bangkok';
			break;

			case 'TJ':
				$identifier = 'Asia/Dushanbe';
			break;

			case 'TK':
				$identifier = 'Pacific/Fakaofo';
			break;

			case 'TL':
				$identifier = 'Asia/Dili';
			break;

			case 'TM':
				$identifier = 'Asia/Ashgabat';
			break;

			case 'TN':
				$identifier = 'Africa/Tunis';
			break;

			case 'TO':
				$identifier = 'Pacific/Tongatapu';
			break;

			case 'TR':
				$identifier = 'Asia/Istanbul';
			break;

			case 'TT':
				$identifier = 'America/Port_of_Spain';
			break;

			case 'TV':
				$identifier = 'Pacific/Funafuti';
			break;

			case 'TW':
				$identifier = 'Asia/Taipei';
			break;

			case 'TZ':
				$identifier = 'Africa/Dar_es_Salaam';
			break;

			case 'UA':

				/**
				 * TODO Ukraine administrative divisions are not implemented in WooCommerce {FN 2019-12-02}
				 *
				 * Possible timezones (only 2 timezones observed):
				 *
				 * 'Europe/Kiev'
				 * 'Europe/Uzhgorod'
				 * 'Europe/Zaporozhye'
				 * 'Europe/Simferopol'
				 */

				$identifier = 'Europe/Kiev';

			break;

			case 'UG':
				$identifier = 'Africa/Kampala';
			break;

			case 'UM':
				$identifier = 'Pacific/Wake';
			break;

			case 'US':

				switch ( $state ) {

					case 'AK':
						$identifier = 'America/Anchorage';
					break;

					case 'AL':
					case 'AR':
					case 'KS':
					case 'IA':
					case 'IL':
					case 'LA':
					case 'MN':
					case 'MO':
					case 'MS':
					case 'ND':
					case 'NE':
					case 'OK':
					case 'SD':
					case 'TN':
					case 'TX':
					case 'WI':
						$identifier = 'America/Chicago';
					break;

					case 'AZ':
						$identifier = 'America/Phoenix';
					break;

					case 'CA':
					case 'NV':
					case 'OR':
					case 'WA':
						$identifier = 'America/Los_Angeles';
					break;

					case 'CO':
					case 'ID':
					case 'MT':
					case 'NM':
					case 'UT':
					case 'WY':
						$identifier = 'America/Denver';
					break;

					case 'CT':
					case 'DC':
					case 'DE':
					case 'FL':
					case 'GA':
					case 'KY':
					case 'MA':
					case 'MD':
					case 'ME':
					case 'MI':
					case 'NC':
					case 'NH':
					case 'NJ':
					case 'NY':
					case 'OH':
					case 'PA':
					case 'RI':
					case 'SC':
					case 'VA':
					case 'VT':
					case 'WV':
					default:
						$identifier = 'America/New_York';
					break;

					case 'HI':
						$identifier = 'Pacific/Honolulu';
					break;

					case 'IN':
						$identifier = 'America/Indiana/Indianapolis';
					break;
				}

			break;

			case 'UY':
				$identifier = 'America/Montevideo';
			break;

			case 'UZ':
				$identifier = 'Asia/Samarkand';
			break;

			case 'VA':
				$identifier = 'Europe/Vatican';
			break;

			case 'VC':
				$identifier = 'America/St_Vincent';
			break;

			case 'VE':
				$identifier = 'America/Caracas';
			break;

			case 'VG':
				$identifier = 'America/Tortola';
			break;

			case 'VI':
				$identifier = 'America/St_Thomas';
			break;

			case 'VU':
				$identifier = 'Pacific/Efate';
			break;

			case 'WF':
				$identifier = 'Pacific/Wallis';
			break;

			case 'WS':
				$identifier = 'Pacific/Pago_Pago';
			break;

			case 'YE':
				$identifier = 'Asia/Aden';
			break;

			case 'YT':
				$identifier = 'Indian/Mayotte';
			break;

			case 'ZA':
				$identifier = 'Africa/Johannesburg';
			break;

			case 'ZM':
				$identifier = 'Africa/Lusaka';
			break;

			case 'ZW':
				$identifier = 'Africa/Harare';
			break;
		}

		/**
		 * Filters a timezone identifier.
		 *
		 * @since 2.7.0
		 *
		 * @param string $identifier a valid timezone identifier
		 * @param \WC_Local_Pickup_Plus_Address $address the related address to obtain the timezone from
		 */
		return (string) apply_filters( 'wc_local_pickup_plus_timezone_identifier', $identifier, $address );
	}


}
