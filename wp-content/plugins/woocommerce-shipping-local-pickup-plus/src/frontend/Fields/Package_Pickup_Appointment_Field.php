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

use SkyVerge\WooCommerce\Local_Pickup_Plus\Data_Store\Package_Pickup_Data;
use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * Field component to schedule an appointment for items to be picked up at checkout.
 *
 * @since 2.7.0
 */
class Package_Pickup_Appointment_Field extends Field {


	/** @var int|string key index of current package this field is associated to */
	private $package_id;

	/** @var array associative array to cache the latest pickup date (values) associated to a pickup location id (keys) */
	private $location_pickup_date = [];


	/**
	 * Field constructor.
	 *
	 * @since 2.7.0
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
	 * @since 2.7.0
	 *
	 * @return int|string
	 */
	private function get_package_id() {

		return $this->package_id;
	}


	/**
	 * Get any set pickup appointment for the package pickup.
	 *
	 * In 2.7.0, extracted from WC_Local_Pickup_Plus_Pickup_Location_Package_Field
	 *
	 * @since 2.0.0
	 *
	 * @return string a date as a string
	 */
	private function get_pickup_date() {

		$pickup_date        = '';
		$pickup_location_id = $this->data_store->get_pickup_location_id();

		if ( 0 === $pickup_location_id || ! $this->pickup_location_has_changed() ) {
			$pickup_date = $this->data_store->get_pickup_data( 'pickup_date' );
		}

		if ( empty( $pickup_date ) ) {
			$pickup_date = array_key_exists( $pickup_location_id, $this->location_pickup_date ) ? $this->location_pickup_date[ $pickup_location_id ] : '';
		} else {
			$this->location_pickup_date[ $pickup_location_id ] = $pickup_date;
		}

		return $pickup_date;
	}


	/**
	 * Detect whether the pickup location ID was updated by the user.
	 *
	 * In 2.7.0, extracted from WC_Local_Pickup_Plus_Pickup_Location_Package_Field
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	private function pickup_location_has_changed() {

		$package_session_data = $this->data_store->get_pickup_data();
		$pickup_location_id   = $this->data_store->get_pickup_location_id();

		return ! empty( $package_session_data['pickup_location_id'] ) && $pickup_location_id !== (int) $package_session_data['pickup_location_id'];
	}


	/**
	 * Get the field HTML.
	 *
	 * In 2.7.0, extracted from {@see \WC_Local_Pickup_Plus_Pickup_Location_Package_Field::get_html}
	 *
	 * @since 2.7.0
	 *
	 * @return string HTML
	 */
	public function get_html() {

		$field_html      = '';
		$shipping_method = wc_local_pickup_plus_shipping_method();
		$mode            = wc_local_pickup_plus_shipping_method()->pickup_appointments_mode();
		$chosen_location = $this->data_store->get_pickup_location();
		$chosen_date     = $this->get_pickup_date();
		$chosen_offset   = $this->data_store->get_pickup_data( 'appointment_offset' );

		if (
			    $chosen_location
			 && is_checkout()
			 && 'disabled' !== $shipping_method->pickup_appointments_mode()
		) :

			ob_start();

			?>
			<div
				id="pickup-appointment-field-for-<?php echo esc_attr( $this->get_package_id() ); ?>"
				class="pickup-location-field pickup-location-field-<?php echo sanitize_html_class( $shipping_method->pickup_selection_mode() ); ?> pickup-location-<?php echo sanitize_html_class( $this->get_object_type() ); ?>-field"
				data-pickup-object-id="<?php echo esc_attr( $this->get_package_id() ); ?>">

				<div class="pickup-location-appointment update_totals_on_change">

					<div class="pickup-location-calendar">

						<small class="pickup-location-field-label">
							<?php /* translators: Placeholder: %s - outputs an "(optional)" note if pickup appointments are optional */
							printf( __( 'Schedule a pickup appointment %s', 'woocommerce-shipping-local-pickup-plus' ), 'required' !== $mode ? __( '(optional)', 'woocommerce-shipping-local-pickup-plus' ) : '' ); ?>
							<?php if ( 'required' === $mode ) : ?>
								<abbr class="required" title="<?php esc_attr_e( 'Required', 'woocommerce-shipping-local-pickup-plus' ); ?>" style="border:none;">*</abbr>
							<?php endif; ?>
						</small>

						<input
							type="hidden"
							id="wc-local-pickup-plus-pickup-date-<?php echo esc_attr( $this->get_package_id() ); ?>"
							class="pickup-location-appointment-date-alt"
							name="_shipping_method_pickup_date[<?php echo esc_attr( $this->get_package_id() ); ?>]"
							value="<?php echo esc_attr( $chosen_date ); ?>"
						/>

						<div style="white-space: nowrap;">
							<input
								type="text"
								readonly="readonly"
								<?php echo 'required' === $mode ? 'required="required"' : ''; ?>
								id="wc-local-pickup-plus-datepicker-<?php echo esc_attr( $this->get_package_id() ); ?>"
								class="pickup-location-appointment-date"
								value="<?php echo esc_attr( $chosen_date ); ?>"
								data-location-id="<?php echo esc_attr( $chosen_location->get_id() ); ?>"
								data-package-id="<?php echo esc_attr( $this->get_package_id() ); ?>"
								data-pickup-date="<?php echo esc_attr( $chosen_date ); ?>"
							/>
							<div
								id="wc-local-pickup-plus-datepicker-<?php echo esc_attr( $this->get_package_id() ); ?>-live-region"
								style="display:none;"
								role="log"
								aria-live="assertive"
								aria-atomic="true"
								aria-relevant="additions"><?php echo esc_html( $chosen_date ); ?></div>
						</div>

						<?php if ( 'required' !== $mode && ! empty( $chosen_date ) ) : ?>
							<small class="pickup-location-field-label">
								<a href="#" id="wc-local-pickup-plus-date-clear-<?php echo esc_attr( $this->get_package_id() ); ?>">
									<?php echo esc_html_x( 'Clear', 'Clear a chosen pickup appointment date', 'woocommerce-shipping-local-pickup-plus' ); ?>
								</a>
							</small>
						<?php endif; ?>

						<div class="pickup-location-schedule" <?php if ( empty( $chosen_date ) ) { echo ' style="display:none;" '; } ?>>
							<?php

							try {
								$chosen_datetime = ! empty( $chosen_date ) && is_string( $chosen_date ) ? new \DateTime( $chosen_date, $chosen_location->get_address()->get_timezone() ) : null;
								$available_times = ! empty( $chosen_date ) ? $chosen_location->get_appointments()->get_available_times( $chosen_datetime ) : [];
							} catch ( \Exception $e ) {
								$available_times = [];
							}

							$chosen_day    = ! empty( $chosen_datetime ) ? $chosen_datetime->format( 'w' ) : null;
							$minimum_hours = ! empty( $chosen_datetime ) ? $chosen_location->get_appointments()->get_schedule_minimum_hours( $chosen_datetime ) : null;
							$opening_hours = null !== $chosen_day        ? $chosen_location->get_business_hours()->get_schedule( $chosen_day, false, $minimum_hours ) : null;

							if ( ! empty( $chosen_datetime ) ) {
								$timezone_string = $chosen_datetime->format( 'T' );
								// check if it is an offset
								if ( ! empty( intval( $timezone_string ) ) ) {
									// use full name instead
									$timezone_string = $chosen_datetime->format( 'e' );
								}
							}

							if ( ! $chosen_location->get_appointments()->is_anytime_appointments_enabled() ) :

								?>
								<small class="pickup-location-field-label">
									<?php printf(
										/* translators: Placeholder: %1$s - day of the week name, %2$s - location timezone */
										__( 'Available pickup times on %1$s (all times in %2$s):', 'woocommerce-shipping-local-pickup-plus' ),
										'<strong>' . date_i18n( 'l', strtotime( $chosen_date ) ) . '</strong>',
										! empty( $timezone_string ) ? $timezone_string : __( 'the location timezone', 'woocommerce-shipping-local-pickup-plus' )
									); ?>
								</small>
								<?php

								if ( $available_times ) :

									$start_of_the_day = ( clone $available_times[0] )->setTime( 0, 0, 0 );

									?>
									<select
										id="wc-local-pickup-plus-pickup-appointment-offset-<?php echo esc_attr( $this->get_package_id() ); ?>"
										class="pickup-location-appointment-offset"
										name="_shipping_method_pickup_appointment_offset[<?php echo esc_attr( $this->get_package_id() ); ?>]"
										style="width:100%;">
										<?php foreach ( $available_times as $datetime ): ?>

											<?php $offset = $datetime->getTimestamp() - $start_of_the_day->getTimestamp(); ?>

											<?php // all in the same line to avoid empty space and new lines to show up in the browser tooltip for the selected option ?>
											<option value="<?php echo esc_attr( $offset ); ?>" <?php selected( $offset, $chosen_offset ); ?>><?php echo esc_html( date_i18n( wc_time_format(), $datetime->getTimestamp() + $datetime->getOffset() ) ); ?></option>

										<?php endforeach; ?>
									</select>

									<?php

								endif;

							elseif ( ! empty( $opening_hours ) ) :

								?>
								<small class="pickup-location-field-label">
									<?php printf(
										/* translators: Placeholder: %s - day of the week name */
										__( 'Opening hours for pickup on %s:', 'woocommerce-shipping-local-pickup-plus' ),
										'<strong>' . date_i18n( 'l', strtotime( $chosen_date ) ) . '</strong>'
									); ?>
								</small>
								<ul>
									<?php foreach ( $opening_hours as $time_string ) : ?>
										<li><small><?php echo esc_html( $time_string ); ?></small></li>
									<?php endforeach; ?>
								</ul>
								<input
									type="hidden"
									name="_shipping_method_pickup_appointment_offset[<?php echo esc_attr( $this->get_package_id() ); ?>]"
									value="<?php echo esc_attr( (int) $minimum_hours ); ?>"
								/>
								<?php

							endif;

							?>
						</div>

					</div>

				</div>

			</div>
			<?php

			$field_html .= ob_get_clean();

		endif;

		/**
		 * Filter the package pickup appointment field HTML.
		 *
		 * @since 2.7.0
		 *
		 * @param string $field_html input field HTML
		 * @param int|string $package_id the current package identifier
		 * @param array $package the current package array
		 */
		return (string) apply_filters( 'wc_local_pickup_plus_get_package_pickup_appointment_field_html', $field_html, $this->get_package_id(), $this->data_store->get_package() );
	}


}
