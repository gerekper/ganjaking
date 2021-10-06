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

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Field component to select a pickup location.
 *
 * @since 2.1.0
 */
abstract class Pickup_Location_Field extends Field {


	/** @var null|\WC_Local_Pickup_Plus_Pickup_Location cached user default pickup location */
	protected $user_default_pickup_location = [];


	/**
	 * Returns the (current) user default pickup location.
	 *
	 * @since 2.3.5
	 *
	 * @param int $user_id the user (defaults to current user)
	 * @return null|\WC_Local_Pickup_Plus_Pickup_Location
	 */
	protected function get_user_default_pickup_location( $user_id = 0 ) {

		$user_id = $user_id > 0 ? (int) $user_id : get_current_user_id();

		if ( empty( $this->user_default_pickup_location[ $user_id ] ) ) {
			$this->user_default_pickup_location[ $user_id ] = wc_local_pickup_plus_get_user_default_pickup_location( $user_id );
		}

		return $this->user_default_pickup_location[ $user_id ];
	}


	/**
	 * Get the current user default lookup area.
	 *
	 * @since 2.1.0
	 *
	 * @return array
	 */
	protected function get_user_default_lookup_area() {

		$user   = wp_get_current_user();
		$lookup = [
			'country' => '',
			'state'   => '',
		];

		if ( $user instanceof \WP_User && ( $default_pickup = $this->get_user_default_pickup_location( $user->ID ) ) ) {
			$lookup['country'] = $default_pickup->get_address( 'country' );
			$lookup['state']   = $default_pickup->get_address( 'state' );
		}

		return $lookup;
	}


	/**
	 * Get default lookup country:state area.
	 *
	 * @since 2.1.0
	 *
	 * @return array
	 */
	protected function get_lookup_area() {

		$plugin    = wc_local_pickup_plus();
		$geocoding = $plugin->geocoding_enabled();
		$codes     = $plugin->get_pickup_locations_instance()->get_available_pickup_location_country_state_codes();
		$chosen    = $this->data_store->get_pickup_data( 'lookup_area' );
		$country   = '';
		$state     = '';

		// get selected value
		if ( ! empty( $chosen ) ) {

			$chosen = is_string( $chosen ) ? explode( ':', $chosen ) : $chosen;

			if ( is_array( $chosen ) ) {
				$country = isset( $chosen[0] ) ? $chosen[0] : '';
				$state   = isset( $chosen[1] ) ? $chosen[1] : '';
			}
		}

		// get or fallback to default value
		if ( empty( $chosen ) || empty( $country ) ) {

			if ( $this->get_user_default_pickup_location() ) {

				$preferred_lookup = $this->get_user_default_lookup_area();

				if ( ! empty( $preferred_lookup['country'] ) ) {

					$country = $preferred_lookup['country'];
					$state   = ! empty( $preferred_lookup['state'] ) ? $preferred_lookup['state'] : '';

				} else {

					$location = wc_get_customer_default_location();
					$country  = isset( $location['country'] ) ? $location['country'] : '';
					$state    = isset( $location['state'] )   ? $location['state'] : '';
				}

			} elseif ( $geocoding ) {

				$country = 'anywhere';
				$state   = '';
			}
		}

		// sanity check:
		if ( 'anywhere' !== $country && ( '' === $country || ( ! in_array( "{$country}:{$state}", $codes, true ) && ! in_array( $country, $codes, true ) ) ) ) {
			$country = ! $geocoding ? WC()->countries->get_base_country() : 'anywhere';
			$state   = ! $geocoding ? WC()->countries->get_base_state()   : '';
		}

		return [ 'country' => $country, 'state' => $state ];
	}


	/**
	 * Get the default lookup area label.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	protected function get_lookup_area_label() {

		$lookup = $this->get_lookup_area();

		if ( ! empty( $lookup['state'] ) ) {
			$states    = WC()->countries->get_states( $lookup['country'] );
			$label     = $states[ $lookup['state'] ];
		} elseif ( 'anywhere' === $lookup['country'] ) {
			$label     = __( 'Anywhere', 'woocommerce-shipping-local-pickup-plus' );
		} else {
			$countries = WC()->countries->get_countries();
			$label     = $countries[ $lookup['country'] ];
		}

		return $label;
	}


	/**
	 * Get dropdown options with countries and states with available pickup locations.
	 *
	 * @see \WC_Countries::country_dropdown_options()
	 *
	 * @since 2.1.0
	 *
	 * @return string HTML
	 */
	protected function get_country_dropdown_options() {

		$chosen         = $this->get_lookup_area();
		$chosen_country = isset( $chosen['country'] ) ? $chosen['country'] : '';
		$chosen_state   = isset( $chosen['state'] )   ? $chosen['state']   : '';
		$countries      = wc_local_pickup_plus()->get_pickup_locations_instance()->get_available_pickup_location_countries();

		ob_start();

		if ( ! empty( $countries ) ) :

			?>
			<option value="anywhere" <?php selected( $chosen_country, 'anywhere' ); ?>><?php esc_html_e( 'Anywhere', 'woocommerce-shipping-local-pickup-plus' ); ?></option>
			<?php

			foreach ( $countries as $country_code => $country_label ) :

				if ( $states = wc_local_pickup_plus()->get_pickup_locations_instance()->get_available_pickup_location_states( $country_code ) ) :

					?>
					<optgroup label="<?php echo esc_attr( $country_label ); ?>">
						<?php foreach ( $states as $state_code => $state_label ) : ?>
							<option
								value="<?php echo esc_attr( "{$country_code}:{$state_code}" ); ?>"
								<?php if ( $chosen_country === $country_code && $chosen_state === $state_code ) { echo 'selected="selected"'; } ?>
							><?php echo esc_html( sprintf( '%1$s &mdash; %2$s', $country_label, $state_label ) ); ?></option>
						<?php endforeach; ?>
					</optgroup>
					<?php

				else :

					?>
					<option
						value="<?php echo esc_attr( $country_code ); ?>"
						<?php if ( $chosen_country === $country_code ) { echo 'selected="selected"'; } ?>
					><?php echo esc_html( $country_label ); ?></option>
					<?php

				endif;

			endforeach;

		endif;

		return ob_get_clean();
	}


	/**
	 * Get a dropdown with available country/state options for available pickup locations.
	 *
	 * @since 2.1.0
	 *
	 * @param string $object_id cart item or package ID
	 * @return string HTML
	 */
	protected function get_country_state_dropdown( $object_id ) {

		ob_start();

		?>
		<select
			id="pickup-location-lookup-area-for-<?php echo sanitize_html_class( $object_id ); ?>"
			class="wc-enhanced-select country_to_state country_select pickup-location-lookup-area"
			style="width:100%;"
			placeholder="<?php echo esc_html_x( 'Choose an area&hellip;', 'Geographic area to search', 'woocommerce-shipping-local-pickup-plus' ); ?>"
			data-placeholder="<?php echo esc_html_x( 'Choose an area&hellip;', 'Geographic area to search', 'woocommerce-shipping-local-pickup-plus' ); ?>"
			autocomplete="country">
			<?php if ( $this->use_enhanced_search() ) : ?>
				<?php echo $this->get_country_dropdown_options(); ?>
			<?php else : ?>
				<option value="anywhere" selected="selected"><?php esc_html_e( 'Anywhere', 'woocommerce-shipping-local-pickup-plus' ); ?></option>
			<?php endif; ?>
		</select>
		<?php

		return ob_get_clean();
	}


	/**
	 * Whether to enable enhanced search.
	 *
	 * If there are more than 80 published locations, use enhanced search (perhaps with geocoding) in lookup fields.
	 *
	 * @since 2.1.0
	 *
	 * @return bool
	 */
	protected function use_enhanced_search() {
		return wc_local_pickup_plus_shipping_method()->is_enhanced_search_enabled();
	}


	/**
	 * Returns all locations available.
	 *
	 * This should be only used when simple dropdown is active and locations are less than a hundred or will cause performance issues.
	 *
	 * @since 2.1.0
	 *
	 * @return \WC_Local_Pickup_Plus_Pickup_Location[]
	 */
	protected function get_all_pickup_locations() {
		return wc_local_pickup_plus()->get_pickup_locations_instance()->get_sorted_pickup_locations();
	}


	/**
	 * Returns a default pickup location for a product if the product has only one pickup location available.
	 *
	 * @since 2.2.0
	 *
	 * @param \WC_Product $product
	 * @return null|\WC_Local_Pickup_Plus_Pickup_Location
	 */
	private function get_product_pickup_location( $product ) {

		$shipping_method         = wc_local_pickup_plus_shipping_method();
		$products                = wc_local_pickup_plus()->get_products_instance();
		$product_pickup_location = null;

		// per-item checkout display
		if ( $product instanceof \WC_Product ) {

			$product_pickup_location = $products->get_product_pickup_location( $product );

		// per-order checkout display
		} elseif (    null === $product
		           && $this instanceof Package_Pickup_Location_Field
		           && $shipping_method
		           && is_cart() ) {

			// check the following conditions, or the pickup location display in cart will be confusing
			if ( ! ( $shipping_method->is_per_order_selection_enabled() && wc_local_pickup_plus()->get_pickup_locations_instance()->get_pickup_locations_count() > 1 ) ) {

				$package      = $this->data_store->get_package();
				$contents     = isset( $package['contents'] ) ? $package['contents'] : [];
				$location_ids = [];

				foreach ( $contents as $item ) {

					$package_product = isset( $item['data'] ) ? $item['data'] : null;

					if ( $package_product instanceof \WC_Product) {

						$available_locations = $products->get_product_pickup_locations( $package_product, [ 'fields' => 'ids' ] );
						$location_ids[]      = ! empty( $available_locations ) ? current( $available_locations ) : 0;
					}
				}

				$location_ids            = array_unique( $location_ids );
				$product_pickup_location = 1 === count( $location_ids ) ? wc_local_pickup_plus_get_pickup_location( current( $location_ids ) ) : null;
			}
		}

		return $product_pickup_location instanceof \WC_Local_Pickup_Plus_Pickup_Location ? $product_pickup_location : null;
	}


	/**
	 * Gets the simple (not enhanced search) location select HTML.
	 *
	 * @since 2.7.0
	 *
	 * @param string $object_id object ID, like cart key or package index
	 * @param \WC_Local_Pickup_Plus_Pickup_Location|null $chosen_location, chosen pickup location
	 * @param string $object_type either `cart-item` or `package`
	 * @param string $field_name HTML field name
	 * @return string field HTML
	 */
	private function get_simple_location_select_html( $object_id, $chosen_location, $object_type, $field_name ) {

		$pickup_locations = $this->get_all_pickup_locations();
		$chosen_location  = array_key_exists( $chosen_location ? $chosen_location->get_id() : null, $pickup_locations ) ? $chosen_location : null;

		ob_start(); ?>

		<select
			name="<?php echo sanitize_html_class( $field_name ); ?>[<?php echo esc_attr( $object_id ); ?>]"
			class="pickup-location-lookup"
			style="width:100%;"
			data-placeholder="<?php esc_attr_e( 'Search locations&hellip;', 'woocommerce-shipping-local-pickup-plus' ); ?>"
			data-pickup-object-type="<?php echo esc_attr( $object_type ); ?>"
			data-pickup-object-id="<?php echo esc_attr( $object_id ); ?>" >
			<option></option>
			<?php foreach ( $pickup_locations as $pickup_location ) : ?>
				<?php if ( $this->can_be_picked_up( $pickup_location ) ) : ?>
					<option
						<?php $address = $pickup_location->get_address(); ?>
						data-name="<?php echo esc_attr( $pickup_location->get_name() ); ?>"
						data-postcode="<?php echo esc_attr( $address->get_postcode() ); ?>"
						data-city="<?php echo esc_attr( $address->get_city() ); ?>"
						data-address="<?php echo esc_attr( str_replace( [ '-', ',', '.', '#', '°' ], '', $address->get_street_address( 'string', ' ' ) ) ); ?>"
						data-address-formatted="<?php echo esc_attr( wp_strip_all_tags( $address->get_formatted_html( true ) ) ); ?>"
						value="<?php echo esc_attr( $pickup_location->get_id() ); ?>"
						<?php selected( $pickup_location->get_id(), ( $chosen_location ? $chosen_location->get_id() : null ), true ); ?>>
						<?php echo esc_html( $pickup_location->get_formatted_name() ); ?>
					</option>
				<?php endif; ?>
			<?php endforeach; ?>
		</select>

		<?php return ob_get_clean();
	}


	/**
	 * Gets the single location select field HTML.
	 *
	 * @since 2.7.0
	 *
	 * @param string $object_id object ID, like cart key or package index
	 * @param \WC_Local_Pickup_Plus_Pickup_Location|null $chosen_location, chosen pickup location
	 * @param string $field_name HTML field name
	 * @return string field HTML
	 */
	private function get_single_location_select_html( $object_id, $chosen_location, $field_name ) {

		if ( wc_local_pickup_plus_shipping_method()->is_per_order_selection_enabled() ) {

			if ( 'package' === $this->get_object_type() ) {

				// set this package for pickup with the current chosen location, since we've made sure already it's determined
				$pickup_data                       = wc_local_pickup_plus()->get_session_instance()->get_package_pickup_data( $object_id );
				$pickup_data['handling']           = 'pickup';
				$pickup_data['pickup_location_id'] = $chosen_location->get_id();

				wc_local_pickup_plus()->get_session_instance()->set_package_pickup_data( $object_id, $pickup_data );

				$package                = wc_local_pickup_plus()->get_packages_instance()->get_shipping_package( $object_id );
				$package_cart_item_keys = ! empty( $package ) ? array_keys( $package['contents'] ) : [];

				if ( ! empty( $package_cart_item_keys ) ) {

					foreach ( $package_cart_item_keys as $cart_item_key ) {

						$session_data = wc_local_pickup_plus()->get_session_instance()->get_cart_item_pickup_data( $cart_item_key );

						// set cart item handling for items in this package
						$session_data['handling']           = 'pickup';
						$session_data['pickup_location_id'] = $chosen_location->get_id();

						wc_local_pickup_plus()->get_session_instance()->set_cart_item_pickup_data( $cart_item_key, $session_data );
					}
				}
			}
		}

		ob_start(); ?>

		<?php if ( wc_local_pickup_plus_shipping_method()->is_per_order_selection_enabled() ) : ?>

			<?php if ( is_checkout() ) : ?>
				<?php echo $chosen_location->get_name(); ?>
				<input type="hidden"
				       name="<?php echo sanitize_html_class( $field_name ); ?>[<?php echo esc_attr( $object_id ); ?>]"
				       value="<?php echo $chosen_location->get_id(); ?>">
			<?php else : ?>
				<small><?php
					/* translators: Placeholder: %s - pickup location name */
					printf( esc_html__( 'Pickup Location: %s', 'woocommerce-shipping-local-pickup-plus' ), $chosen_location->get_name() ); ?></small>
			<?php endif; ?>

		<?php else : // == wc_local_pickup_plus_shipping_method()->is_per_item_selection_enabled() ?>

			<small><abbr
					title="<?php echo esc_attr( $chosen_location->get_address()->get_formatted_html( true ) ); ?>"><?php
					/* translators: Placeholder: %s - pickup location name */
					printf( esc_html__( 'Available for pickup at: %s', 'woocommerce-shipping-local-pickup-plus' ), $chosen_location->get_name() ); ?></abbr></small>

		<?php endif ?>

		<?php return ob_get_clean();
	}


	/**
	 * Gets the enhanced search lookup area HTML.
	 *
	 * @since 2.7.0
	 *
	 * @param string $object_id object ID, like cart key or package index
	 * @param bool $enhanced_search optional if false, the lookup area is printed as hidden to preserve the values
	 * @return string field HTML
	 */
	private function get_lookup_area_html( $object_id, $enhanced_search = true ) {

		ob_start(); ?>

		<div
			id="pickup-location-lookup-area-field-for-<?php echo esc_attr( $object_id ); ?>"
			class="pickup-location-lookup-area-field"
			data-pickup-object-id="<?php echo esc_attr( $object_id ); ?>"
			<?php if ( ! $enhanced_search ) { echo 'style="display: none;"'; } ?>>
			<small
				class="pickup-location-current-lookup-area"
				<?php if ( ! $enhanced_search ) { echo 'style="display: none;"'; } ?>><?php
				$change = '<a class="pickup-location-change-lookup-area" href="#">' . strtolower( esc_html__( 'Change', 'woocommerce-shipping-local-pickup-plus' ) ) . '</a>';
				/* translators: Placeholder: %s - country or state name (or "Anywhere") */
				printf( __( 'Enter a postcode or city to search for pickup locations from: %s', 'woocommerce-shipping-local-pickup-plus' ) . ' (' . $change . ')', '<em class="pickup-location-current-lookup-area-label">' . $this->get_lookup_area_label() . '</em>' ); ?>
			</small>
			<div style="display: none;">
				<?php echo $this->get_country_state_dropdown( $object_id ); ?>
			</div>
		</div>

		<?php return ob_get_clean();
	}


	/**
	 * Gets the enhanced search location select field HTML.
	 *
	 * @since 2.7.0
	 *
	 * @param string $object_id object ID, like cart key or package index
	 * @param \WC_Local_Pickup_Plus_Pickup_Location|null $chosen_location, chosen pickup location
	 * @param string $object_type either `cart-item` or `package`
	 * @param string $field_name HTML field name
	 * @param \WC_Product|null optional, the current product
	 * @return string field HTML
	 */
	private function get_enhanced_search_location_select_html( $object_id, $chosen_location, $object_type, $field_name, $product = null ) {

		ob_start(); ?>

		<select
			name="<?php echo sanitize_html_class( $field_name ); ?>[<?php echo esc_attr( $object_id ); ?>]"
			class="pickup-location-lookup"
			style="width:100%;"
			data-placeholder="<?php esc_attr_e( 'Search locations&hellip;', 'woocommerce-shipping-local-pickup-plus' ); ?>"
			data-pickup-object-type="<?php echo esc_attr( $object_type ); ?>"
			data-pickup-object-id="<?php echo esc_attr( $object_id ); ?>"
			<?php if ( $product instanceof \WC_Product ) : ?>
				data-product-id="<?php echo esc_attr( $product->get_id() ); ?>"
			<?php endif; ?>
			>
			<option></option>
			<?php if ( $chosen_location instanceof \WC_Local_Pickup_Plus_Pickup_Location ) : ?>
				<option value="<?php echo $chosen_location->get_id(); ?>" selected="selected"><?php echo esc_html( $chosen_location->get_formatted_name() ); ?></option>
			<?php endif; ?>
		</select>

		<?php return ob_get_clean();
	}


	/**
	 * Gets the location select field HTML.
	 *
	 * @since 2.1.0
	 *
	 * @param string $object_id object ID, like cart key or package index
	 * @param \WC_Local_Pickup_Plus_Pickup_Location|null $chosen_location optional, chosen pickup location
	 * @param \WC_Product|null optional, the current product
	 * @return string field HTML
	 */
	protected function get_location_select_html( $object_id, $chosen_location = null, $product = null ) {

		$object_type     = $this->get_object_type();
		$enhanced_search = $this->use_enhanced_search();
		$field_name      = wc_local_pickup_plus_shipping_method()->is_per_item_selection_enabled() ? '_pickup_location_id' : '_shipping_method_pickup_location_id';
		$field_html      = '';

		$using_single_location = false;

		if ( $selected_location = $this->get_product_pickup_location( $product ) ) {
			// the product can only be picked up at only location
			$chosen_location       = $selected_location;
			$using_single_location = true;
		} elseif ( ! $chosen_location ) {
			// fallback to the user default pickup location, if a chosen location param was not provided
			$chosen_location = $this->get_user_default_pickup_location();
		}

		// the product can only be picked up at only location
		if ( $using_single_location ) {

			$field_html .= $this->get_single_location_select_html( $object_id, $chosen_location, $field_name );

		} elseif ( $enhanced_search ) {

			$field_html .= $this->get_lookup_area_html( $object_id, $enhanced_search );
			$field_html .= $this->get_enhanced_search_location_select_html( $object_id, $chosen_location, $object_type, $field_name, $product );

		} else {

			$field_html .= $this->get_lookup_area_html( $object_id, $enhanced_search );
			$field_html .= $this->get_simple_location_select_html( $object_id, $chosen_location, $object_type, $field_name, $using_single_location );

		}

		return $field_html;
	}


}

class_alias( Pickup_Location_Field::class, 'WC_Local_Pickup_Plus_Pickup_Location_Field' );
