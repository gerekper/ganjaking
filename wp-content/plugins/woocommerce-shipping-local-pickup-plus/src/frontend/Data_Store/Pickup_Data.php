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

namespace SkyVerge\WooCommerce\Local_Pickup_Plus\Data_Store;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * Pickup data storage component.
 *
 * @since 2.7.0
 */
abstract class Pickup_Data {


	/** @var string the cart item ID or package key for the current storage instance */
	protected $object_id;


	/**
	 * Gets the pickup location data.
	 *
	 * Extending classes should override this to retrieve data based on their
	 * specific model for storage.
	 *
	 * In 2.7.0, extracted from WC_Local_Pickup_Plus_Pickup_Location_Field
	 *
	 * @internal
	 *
	 * @since 2.1.0
	 *
	 * @param string $piece specific data to get. Defaults to getting all available data.
	 * @return array|string
	 */
	abstract public function get_pickup_data( $piece = '' );


	/**
	 * Sets the pickup location data.
	 *
	 * Extending classes should override this to set data based on their
	 * specific model for storage.
	 *
	 * In 2.7.0, extracted from WC_Local_Pickup_Plus_Pickup_Location_Field
	 *
	 * @internal
	 *
	 * @since 2.1.0
	 *
	 * @param array $pickup_data pickup data
	 */
	abstract public function set_pickup_data( array $pickup_data );


	/**
	 * Deletes the pickup location data.
	 *
	 * Extending classes should override this to delete data based on their
	 * specific model for storage.
	 *
	 * In 2.7.0, extracted from WC_Local_Pickup_Plus_Pickup_Location_Field
	 *
	 * @internal
	 *
	 * @since 2.1.0
	 */
	abstract public function delete_pickup_data();


}
