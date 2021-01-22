<?php
/**
 * WooCommerce Checkout Add-Ons
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Checkout Add-Ons to newer
 * versions in the future. If you wish to customize WooCommerce Checkout Add-Ons for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-checkout-add-ons/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2014-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * File Add-On Class
 *
 * @since 2.0.0
 */
class File extends Add_On {


	/** @var string the add-on type */
	protected $add_on_type = 'file';


	/**
	 * Sets up the add-on.
	 *
	 * @since 2.0.0
	 *
	 * @param Add_On|array|int Add_On object, data array, or ID
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function __construct( $data = 0 ) {

		parent::__construct( $data );

		$this->add_on_type_name = _x( 'File', 'Add-on type', 'woocommerce-checkout-add-ons' );
	}


	/**
	 * No-Op: The 'file' add-on type does not support a default value.
	 *
	 * @since 2.0.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return string
	 */
	public function get_default_value( $context = 'view' ) {

		return '';
	}


	/**
	 * No-Op: The 'file' add-on type does not support a default value.
	 *
	 * @since 2.0.0
	 *
	 * @param string $value
	 */
	public function set_default_value( $value ) {}


	/**
	 * Normalizes the add-on value.
	 *
	 * Provided the value(s), looks up the proper label(s) and returns them
	 * as a comma-separated string or an array.
	 *
	 * @since 1.0
	 *
	 * @param string|array $value sanitized key or array of keys
	 * @param bool $implode whether to glue labels together with commas
	 * @return mixed string|array label or array of labels matching the value
	 */
	public function normalize_value( $value, $implode ) {

		$file_ids = explode( ',', $value );

		// multiple files
		if ( count( $file_ids ) > 1 ) {

			$label       = __( 'Uploaded files:', 'woocommerce-checkout-add-ons' );
			$file_labels = array();

			foreach ( $file_ids as $key => $file_id ) {

				if ( $url = wp_get_attachment_url( $file_id ) ) {

					$file_labels[] = '<a href="' . $url . '">' . sprintf( __( 'File %d', 'woocommerce-checkout-add-ons' ), $key + 1 ) . '</a>';

				} else {

					$file_labels[] = __( '(File has been removed)', 'woocommerce-checkout-add-ons' );
				}
			}

			$label .= implode( ', ', $file_labels );

		// single file
		} elseif ( $url = wp_get_attachment_url( $file_ids[0] ) ) {

			$label = '<a href="' . $url . '">' . __( 'Uploaded file', 'woocommerce-checkout-add-ons' ) . '</a>';

		} else {

			$label = __( '(File has been removed)', 'woocommerce-checkout-add-ons' );
		}

		return parent::normalize_value( $label, $implode );
	}


	/**
	 * Gets an array of the supported attributes for this add-on.
	 *
	 * Removes 'sortable' since file add-ons can't be sorted.
	 *
	 * @since 2.0.0
	 *
	 * @return string[]
	 */
	public static function get_supported_attributes() {

		$supported_attributes = array_flip( parent::get_supported_attributes() );

		unset( $supported_attributes['sortable'] );

		return array_keys( $supported_attributes );
	}


}
