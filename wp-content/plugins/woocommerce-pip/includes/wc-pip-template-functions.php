<?php
/**
 * WooCommerce Print Invoices/Packing Lists
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Print
 * Invoices/Packing Lists to newer versions in the future. If you wish to
 * customize WooCommerce Print Invoices/Packing Lists for your needs please refer
 * to http://docs.woocommerce.com/document/woocommerce-print-invoice-packing-list/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2011-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;


if ( ! function_exists( 'wc_pip_get_merge_tags' ) ) {

	/**
	 * Gets merge tags.
	 *
	 * TODO by version 4.0.0 quit making this function "pluggable" and remove the function_exists() wrap {FN 2018-12-10}
	 *
	 * @since 3.0.0
	 *
	 * @param string $context optional \WC_PIP_Document type, optional, used in filter
	 * @param null|\WC_Order $order optional, associated order object
	 * @return array
	 */
	function wc_pip_get_merge_tags( $context = '', $order = null ) {

		// base tags
		$merge_tags = array(
			'{D}'    => date_i18n( 'j' ), // day of the month without leading zeros
			'{DD}'   => date_i18n( 'd' ), // day of the month with leading zeros (2 digits)
			'{M}'    => date_i18n( 'n' ), // month, without leading zeros
			'{MM}'   => date_i18n( 'm' ), // month, with leading zeros (2 digits)
			'{YY}'   => date_i18n( 'y' ), // year (2 digits)
			'{YYYY}' => date_i18n( 'Y' ), // year (4 digits)
			'{H}'    => date_i18n( 'G' ), // 24-hour format of an hour without leading zeros
			'{HH}'   => date_i18n( 'H' ), // 24-hour format of an hour with leading zeros
			'{N}'    => date_i18n( 'i' ), // minutes with leading zeros
			'{S}'    => date_i18n( 's' ), // seconds with leading zeros
		);

		// additional tags
		if ( $order instanceof \WC_Order && $document = wc_pip()->get_document( $context, array( 'order' => $order ) ) ) {

			$merge_tags = array_merge( $merge_tags, array(
				'{order_number}'   => $order->get_order_number(),
				'{invoice_number}' => $document->get_invoice_number(),
			) );
		}

		/**
		 * Filters the merge tags used for invoice field replacements.
		 *
		 * @since 3.0.0
		 *
		 * @param array $merge_tags associative array
		 * @param string $context \WC_PIP_Document type
		 * @param null|\WC_Order $order optional order object, default null
		 */
		return apply_filters( 'wc_pip_merge_tags', $merge_tags, $context, $order );
	}

}


if ( ! function_exists( 'wc_pip_parse_merge_tags' ) ) {

	/**
	 * Parses merge tags.
	 *
	 * TODO by version 4.0.0 quit making this function "pluggable" and remove the function_exists() wrap {FN 2018-12-10}
	 *
	 * @since 3.0.0
	 *
	 * @param string $string string with possible tags to merge
	 * @param string $context optional, \WC_PIP_Document type or context where a string is parsed
	 * @param null|\WC_Order $order optional order object, default null
	 * @return string formatted string with merged tags
	 */
	function wc_pip_parse_merge_tags( $string, $context = '', $order = null ) {

		$merge_tags = wc_pip_get_merge_tags( $context, $order );

		if ( $merge_tags && is_array( $merge_tags ) ) {

			foreach ( $merge_tags as $tag => $value ) {
				// replace is case insensitive
				$string = str_ireplace( $tag, $value, $string );
			}
		}

		return $string;
	}

}


if ( ! function_exists( 'wc_pip_print_button' ) ) {

	/**
	 * Outputs a print button for template use.
	 *
	 * TODO by version 4.0.0 quit making this function "pluggable" and remove the function_exists() wrap {FN 2018-12-10}
	 *
	 * @since 3.0.0
	 */
	function wc_pip_print_button() {
		?>
		<a class="button woocommerce-pip-print" href="#" onclick="window.print()"><?php esc_html_e( 'Print', 'woocommerce-pip' ); ?></a>
		<style type="text/css">
			.woocommerce-pip-print {
				-moz-box-shadow: inset 0 1px 0 0 #FFF;
				-webkit-box-shadow: inset 0 1px 0 0 #FFF;
				box-shadow: inset 0 1px 0 0 #FFF;
				background: -webkit-gradient(linear, left top, left bottom, color-stop(0.05, #F9F9F9), color-stop(1, #E9E9E9));
				background: -moz-linear-gradient(top, #F9F9F9 5%, #E9E9E9 100%);
				background: -webkit-linear-gradient(top, #F9F9F9 5%, #E9E9E9 100%);
				background: -o-linear-gradient(top, #F9F9F9 5%, #E9E9E9 100%);
				background: -ms-linear-gradient(top, #F9F9F9 5%, #E9E9E9 100%);
				background: linear-gradient(to bottom, #F9F9F9 5%, #E9E9E9 100%);
				filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#f9f9f9', endColorstr='#e9e9e9',GradientType=0);
				background-color: #F9F9F9;
				-moz-border-radius: 6px;
				-webkit-border-radius: 6px;
				border-radius: 6px;
				border: 1px solid #DCDCDC;
				display: inline-block;
				cursor: pointer;
				color: #666;
				font-family: Helvetica, Arial, sans-serif;
				font-size: 15px;
				font-weight: bold;
				left: 8px;
				padding: 6px 24px;
				position: fixed;
				text-decoration: none;
				text-shadow: 0 1px 0 #FFF;
				top: 8px;
				z-index: 10000;
			}
			.woocommerce-pip-print:hover {
				background: -webkit-gradient(linear, left top, left bottom, color-stop(0.05, #E9E9E9), color-stop(1, #F9F9F9));
				background: -moz-linear-gradient(top, #E9E9E9 5%, #E9E9E9 100%);
				background: -webkit-linear-gradient(top, #E9E9E9 5%, #E9E9E9 100%);
				background: -o-linear-gradient(top, #E9E9E9 5%, #E9E9E9 100%);
				background: -ms-linear-gradient(top, #E9E9E9 5%, #E9E9E9 100%);
				background: linear-gradient(to bottom, #E9E9E9 5%, #E9E9E9 100%);
				filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#E9E9E9', endColorstr='#F9F9F9',GradientType=0);
				background-color: #E9E9E9;
			}
			@media print {
				.woocommerce-pip-print {
					display: none;
				}
			}
		</style>
		<?php
	}

}


/**
 * Gets the current WordPress site name.
 *
 * This function exists as a wrapper for the corresponding framework method for template use to avoid calling the namespaced framework directly.
 *
 * @since 3.6.2
 *
 * @return string
 */
function wc_pip_get_site_name() {

	return Framework\SV_WC_Helper::get_site_name();
}
