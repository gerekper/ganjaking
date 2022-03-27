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

namespace SkyVerge\WooCommerce\Local_Pickup_Plus\Fields;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\Local_Pickup_Plus\Data_Store\Pickup_Data;
use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * Field component.
 *
 * @since 2.1.0
 */
abstract class Field {


	/** @var string the object type for the current storage instance */
	protected $object_type = '';

	/** @var Pickup_Data the data storage handler for the current field instance */
	protected $data_store = '';


	/**
	 * Gets the object type for this storage.
	 *
	 * This lets us differentiate between cart items and packages in the JS.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	protected function get_object_type() {
		return $this->object_type;
	}


	/**
	 * Gets the data storage.
	 *
	 * @since 2.7.0
	 *
	 * @return Pickup_Data
	 */
	public function get_data_store() {
		return $this->data_store;
	}


	/**
	 * Determines if the current object can be picked up, or must be shipped.
	 *
	 * @since 2.1.0
	 *
	 * @param \WC_Local_Pickup_Plus_Pickup_Location $pickup_location pickup location to check
	 * @return bool
	 */
	protected function can_be_picked_up( $pickup_location ) {
		return true;
	}


	/**
	 * Get the field HTML.
	 *
	 * @since 2.1.0
	 *
	 * @return string HTML
	 */
	abstract public function get_html();


	/**
	 * Output the field HTML.
	 *
	 * @since 2.1.0
	 */
	public function output_html() {

		echo $this->get_html();
	}


}
