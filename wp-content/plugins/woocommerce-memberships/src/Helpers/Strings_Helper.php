<?php
/**
 * WooCommerce Memberships
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2024, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\Helpers;

defined( 'ABSPATH' ) or exit;

/**
 * An helper to manipulate strings.
 *
 * Should only contain static methods.
 *
 * @since 1.22.4
 */
class Strings_Helper {


	/**
	 * Converts an array to an escaped string for SQL use in IN clause.
	 *
	 * @since 1.22.4
	 *
	 * @param array $value array of numbers or array of strings
	 * @return string comma separated list of items
	 */
	private static function esc_sql_in( array $value ) : string {

		return implode( ',', array_unique( $value ) );
	}


	/**
	 * Escapes IDs to be used inside a IN clause in a SQL statement.
	 *
	 * @since 1.22.4
	 *
	 * @param int[]|string[] $ids
	 * @return string comma separated list of numbers
	 */
	public static function esc_sql_in_ids( array $ids ) : string {

		return static::esc_sql_in( array_map( 'absint', $ids ) );
	}


	/**
	 * Escapes strings to be used inside a IN clause in a SQL statement.
	 *
	 * @since 1.22.4
	 *
	 * @param array $values
	 * @return string comma separated list of items
	 */
	public static function esc_sql_in_strings( array $values ) : string {

		// @TODO this method doesn't wrap strings into quotes so it's not safe to use, but it's currently not utilized in Memberships - we should update it with an upcoming FW method {unfulvio 2021-08-18}
		return static::esc_sql_in( esc_sql( $values ) );
	}


	/**
	 * Converts an array of items to a comma-separated, human-readable list of items.
	 *
	 * Note: does not automatically append a period at the end of the list.
	 *
	 * @since 1.22.4
	 *
	 * @param string[]|int[] $items an array of strings or numbers
	 * @param string $conjunction optional: will auto-translate 'and' (default) or 'or', can pass an arbitrary one which will be used to connect the last two items int he list
	 * @return string e.g. "a, b and c", "a or b", etc.
	 */
	public static function get_human_readable_items_list(array $items, string $conjunction = '' ) : string {

		if ( empty( $items ) ) {
			return '';
		}

		if ( ! $conjunction || 'or' === $conjunction ) {
			$conjunction = _x( 'or', 'Conjunction used in a list of items, e.g.: "a, b or c"', 'woocommerce-memberships' );
		} elseif ( 'and' === $conjunction ) {
			$conjunction = _x( 'and', 'Conjunction used in a list of items, e.g.: "a, b and c"', 'woocommerce-memberships' );
		}

		array_splice( $items, -2, 2, implode( ' ' . $conjunction . ' ', array_slice( $items, -2, 2 ) ) );

		return implode( ', ', $items );
	}


}
