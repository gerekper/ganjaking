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
 * @copyright   Copyright (c) 2012-2021, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Local_Pickup_Plus\Fields;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\Local_Pickup_Plus\Data_Store\Package_Pickup_Data;
use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Field component to attach pickup data for items to be picked up at checkout.
 *
 * @since 2.0.0
 */
class Package_Pickup_Location_Field extends Pickup_Location_Field {


	/** @var int|string key index of current package this field is associated to */
	private $package_id;


	/**
	 * Field constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param int|string $package_id the package key index
	 */
	public function __construct( $package_id ) {

		$this->object_type = 'package';
		$this->package_id  = $package_id;
		$this->data_store  = new Package_Pickup_Data( $package_id );
	}


	/**
	 * Get the ID of the package for this field.
	 *
	 * @since 2.0.0
	 *
	 * @return int|string
	 */
	private function get_package_id() {
		return $this->package_id;
	}


	/**
	 * Returns the current product object, if there is only a single one in package.
	 *
	 * @since 2.2.0
	 *
	 * @return \WC_Product|null
	 */
	private function get_single_product() {

		$product = null;
		$package = $this->data_store->get_package();

		if ( ! empty( $package['contents'] ) && 1 === count( $package['contents'] ) ) {
			$content = current( $package['contents'] );
			$product = isset( $content['data'] ) && $content['data'] instanceof \WC_Product ? $content['data'] : null;
		}

		return $product;
	}


	/**
	 * Gets the pickup location select HTML.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	protected function get_pickup_location_html() {

		$shipping_method = wc_local_pickup_plus_shipping_method();
		$chosen_location = $this->data_store->get_pickup_location();

		ob_start(); ?>

		<?php if ( $shipping_method->is_per_order_selection_enabled() ) : ?>

			<?php echo $this->get_location_select_html( $this->get_package_id(), $chosen_location, $this->get_single_product() ); ?>

		<?php elseif ( $chosen_location ) : ?>

			<?php // record the chosen pickup location ID ?>

			<input
				type="hidden"
				name="_shipping_method_pickup_location_id[<?php echo esc_attr( $this->get_package_id() ); ?>]"
				value="<?php echo esc_attr( $chosen_location->get_id() ); ?>"
				data-package-id="<?php echo esc_attr( $this->get_package_id() ); ?>"
			/>

		<?php endif; ?>

		<?php if ( $chosen_location ) : ?>

			<?php // display pickup location name, address & description ?>

			<div class="pickup-location-address">

				<?php if ( is_cart() && $shipping_method->is_per_item_selection_enabled() ) : ?>
					<?php /* translators: Placeholder: %s - the name of the pickup location */
					echo sprintf( __( 'Pickup Location: %s', 'woocommerce-shipping-local-pickup-plus' ), esc_html( $chosen_location->get_name() ) ) . '<br />'; ?>
				<?php endif; ?>

				<?php $address = $chosen_location->get_address()->get_formatted_html( true ); ?>
				<?php echo ! empty( $address ) ? wp_kses_post( $address . '<br />' ) : ''; ?>
				<?php $description = $chosen_location->get_description(); ?>
				<?php echo ! empty( $description ) ? wp_kses_post( $description . '<br />' ) : ''; ?>
			</div>

		<?php elseif ( is_checkout() ) : ?>

			<?php // the customer has previously selected items for pickup without specifying a location ?>

			<em><?php esc_html_e( 'Please choose a pickup location', 'woocommerce-shipping-local-pickup-plus' ); ?></em>

		<?php endif; ?>

		<?php

		return ob_get_clean();
	}


	/**
	 * Get the field HTML.
	 *
	 * @since 2.0.0
	 *
	 * @return string HTML
	 */
	public function get_html() {

		$field_html      = '';
		$shipping_method = wc_local_pickup_plus_shipping_method();

		ob_start();

		?>
		<div
			id="pickup-location-field-for-<?php echo esc_attr( $this->get_package_id() ); ?>"
			class="pickup-location-field pickup-location-field-<?php echo sanitize_html_class( $shipping_method->pickup_selection_mode() ); ?> pickup-location-<?php echo sanitize_html_class( $this->get_object_type() ); ?>-field"
			data-pickup-object-id="<?php echo esc_attr( $this->get_package_id() ); ?>">

			<?php // display the selected location, or location select field ?>
			<?php echo $this->get_pickup_location_html(); ?>

		</div>
		<?php

		$field_html .= ob_get_clean();

		/**
		 * Filter the package pickup location field HTML.
		 *
		 * @since 2.0.0
		 *
		 * @param string $field_html input field HTML
		 * @param int|string $package_id the current package identifier
		 * @param array $package the current package array
		 */
		return apply_filters( 'wc_local_pickup_plus_get_pickup_location_package_field_html', $field_html, $this->get_package_id(), $this->data_store->get_package() );
	}


}

class_alias( Package_Pickup_Location_Field::class, 'WC_Local_Pickup_Plus_Pickup_Location_Package_Field' );
