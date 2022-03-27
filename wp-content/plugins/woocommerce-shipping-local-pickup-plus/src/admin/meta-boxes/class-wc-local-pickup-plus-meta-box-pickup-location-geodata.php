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

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * Pickup Location Data Meta Box.
 *
 * @since 2.0.0
 */
class WC_Local_Pickup_Plus_Meta_Box_Pickup_Location_Geodata extends \WC_Local_Pickup_Plus_Meta_Box {


	/** @var bool whether an error message upon saving coordinates has been displayed already. */
	private static $error_message = false;


	/**
	 * Meta box constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->id       = 'wc-local-pickup-plus-pickup-location-geodata';
		$this->context  = 'side';
		$this->priority = 'default';
		$this->screens  = array( 'wc_pickup_location' );

		parent::__construct();
	}


	/**
	 * Get the meta box title.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Pickup Location Coordinates', 'woocommerce-shipping-local-pickup-plus' );
	}


	/**
	 * Meta box output.
	 *
	 * @since 2.0.0
	 *
	 * @param \WP_Post $post
	 */
	public function output( \WP_Post $post ) {

		$pickup_location = wc_local_pickup_plus_get_pickup_location( $post );

		?>
		<div id="pickup-location-geocoded-status" class="geocoded_status">
			<table>
				<tbody>

					<?php $geocode_override = 'yes' === $this->get_post_meta( '_pickup_location_override_geocoding', 'no' ); ?>

					<?php if ( $pickup_location->has_coordinates() ) : ?>

						<tr>
							<th><span class="dashicons dashicons-admin-site"></span></th>
							<td><?php esc_html_e( 'Address is geocoded', 'woocommerce-shipping-local-pickup-plus' ); ?> <span class="geocoded-status-dot has-coordinates"></span></td>
						</tr>
						<tr>
							<th><span class="dashicons dashicons-external"></span></th>
							<td>
								<?php $help_tip = __( 'The position determined by Google for this address is only meant for searching locations by distance and visual precision is not a requirement.', 'woocommerce-shipping-local-pickup-plus' ); ?>
								<a target="_blank" href="http://maps.google.com/maps?q=<?php echo $pickup_location->get_latitude(); ?>,<?php echo $pickup_location->get_longitude(); ?>"><?php esc_html_e( 'View on Google Maps', 'woocommerce-shipping-local-pickup-plus' ); ?></a><?php echo wc_help_tip( $help_tip ); ?>
							</td>
						</tr>

					<?php else : ?>

						<tr>
							<th><span class="dashicons dashicons-admin-site"></span></th>
							<td><?php esc_html_e( 'Address is not geocoded', 'woocommerce-shipping-local-pickup-plus' ) ?> <span class="geocoded-status-dot no-coordinates"></span></td>
						</tr>
						<tr>
							<td colspan="2"><span class="description"><?php esc_html_e( 'Enter a pickup location address and save it to obtain coordinates automatically. You can also override coordinates manually.', 'woocommerce-shipping-local-pickup-plus' ); ?></span></td>
						</tr>

					<?php endif; ?>

					<tr>
						<th><input
								id="edit-pickup-location-coordinates"
								name="_override_pickup_location_geocoding"
								type="checkbox"
								value="yes" <?php checked( true, $geocode_override ); ?>/></th>
						<td><label for="edit-pickup-location-coordinates"><a style="text-decoration: underline;"><?php esc_html_e( 'Enter Coordinates Manually', 'woocommerce-shipping-local-pickup-plus' ); ?></a></label></td>
					</tr>
					<tr class="edit-pickup-location-coordinates" <?php if ( ! $geocode_override ) { echo 'style="display: none;"'; } ?>>
						<td colspan="2">
							<label>
								<?php esc_html_e( 'Latitude', 'woocommerce-shipping-local-pickup-plus' ); ?>
								<input
									type="text"
									step="any"
									name="_pickup_location_latitude"
									value="<?php echo $pickup_location->get_latitude(); ?>" />
							</label>
						</td>
					</tr>
					<tr class="edit-pickup-location-coordinates" <?php if ( ! $geocode_override ) { echo 'style="display: none;"'; } ?>>
						<td colspan="2">
							<label><?php esc_html_e( 'Longitude', 'woocommerce-shipping-local-pickup-plus' ); ?>
								<input
									type="text"
									name="_pickup_location_longitude"
									value="<?php echo $pickup_location->get_longitude(); ?>" />
							</label>
						</td>
					</tr>
					<tr class="edit-pickup-location-coordinates" <?php if ( ! $geocode_override ) { echo 'style="display: none;"'; } ?>>
						<td colspan="2"><span class="description geocode-override"><?php esc_html_e( 'Overriding default coordinates', 'woocommerce-shipping-local-pickup-plus' ); ?></span></td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
	}


	/**
	 * Save the pickup location coordinates.
	 *
	 * @since 2.0.0
	 *
	 * @param int $post_id the Pickup Location post ID
	 * @param \WP_Post $post the Pickup Location post object
	 */
	public function update_data( $post_id, \WP_Post $post ) {

		wc_local_pickup_plus()->check_tables();

		$pickup_location = new \WC_Local_Pickup_Plus_Pickup_Location( $post );

		if ( isset( $_POST['_override_pickup_location_geocoding'] ) && 'yes' === $_POST['_override_pickup_location_geocoding'] ) {

			$lat = isset( $_POST['_pickup_location_latitude'] )  ? $_POST['_pickup_location_latitude']  : null;
			$lon = isset( $_POST['_pickup_location_longitude'] ) ? $_POST['_pickup_location_longitude'] : null;

			if ( is_numeric( $lat ) && is_numeric( $lon ) ) {

				$pickup_location->set_coordinates( $lat, $lon );

				update_post_meta( $post_id, '_pickup_location_override_geocoding', 'yes' );
			}

		} else {

			$address_array   = $pickup_location->get_address()->get_array();
			$new_coordinates = wc_local_pickup_plus()->get_geocoding_api_instance()->get_coordinates( $address_array );

			$lat = null !== $new_coordinates && isset( $new_coordinates['lat'] ) && is_numeric( $new_coordinates['lat'] ) ? (float) $new_coordinates['lat'] : null;
			$lon = null !== $new_coordinates && isset( $new_coordinates['lon'] ) && is_numeric( $new_coordinates['lon'] ) ? (float) $new_coordinates['lon'] : null;

			if ( is_float( $lat ) && is_float( $lon ) ) {

				$pickup_location->set_coordinates( $lat, $lon );

			} elseif ( ! self::$error_message ) {

				wc_local_pickup_plus()->get_message_handler()->add_error( __( 'Could not save or update coordinates for this pickup location, please review the pickup location address details or ensure that your Google Maps Geocoding API key is valid and Google is returning results.', 'woocommerce-shipping-local-pickup-plus' ) );

				$pickup_location->delete_coordinates();

				self::$error_message = true;
			}

			delete_post_meta( $post_id, '_pickup_location_override_geocoding' );
		}
	}


}
