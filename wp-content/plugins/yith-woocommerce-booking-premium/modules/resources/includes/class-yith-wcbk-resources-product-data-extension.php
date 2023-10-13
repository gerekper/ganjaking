<?php
/**
 * Class YITH_WCBK_Resources_Product_Data_Extension
 * Handle product data for the Resources module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Resources
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Resources_Product_Data_Extension' ) ) {
	/**
	 * YITH_WCBK_Resources_Product_Data_Extension class.
	 */
	class YITH_WCBK_Resources_Product_Data_Extension extends YITH_WCBK_Product_Data_Extension {

		/**
		 * Get settings.
		 *
		 * @return array
		 */
		protected function get_settings(): array {
			return array(
				'meta_keys_to_props' => array(
					'_yith_booking_enable_resources'            => 'enable_resources',
					'_yith_booking_resource_assignment'         => 'resource_assignment',
					'_yith_booking_resources_layout'            => 'resources_layout',
					'_yith_booking_resources_label'             => 'resources_label',
					'_yith_booking_resources_field_label'       => 'resources_field_label',
					'_yith_booking_resources_field_placeholder' => 'resources_field_placeholder',
					'_yith_booking_resource_is_required'        => 'resource_is_required',
				),
				'internal_meta_keys' => array(
					'_yith_booking_enable_resources',
					'_yith_booking_resource_assignment',
					'_yith_booking_resources_layout',
					'_yith_booking_resources_label',
					'_yith_booking_resources_field_label',
					'_yith_booking_resources_field_placeholder',
					'_yith_booking_resource_is_required',
				),
				'tabs'               => array(
					'resources' => array(
						'id'     => 'yith_booking_resources_tab',
						'wc_key' => 'yith_booking_resources',
						'tab'    => array(
							'label'    => _x( 'Resources', 'Product tab title', 'yith-booking-for-woocommerce' ),
							'target'   => 'yith_booking_resources_tab',
							'priority' => 60,
						),
						'module' => 'resources',
					),
				),
			);
		}

		/**
		 * Save booking product meta for resources.
		 *
		 * @param WC_Product_Booking $product The booking product.
		 */
		public function set_product_meta_before_saving( WC_Product_Booking $product ) {
			// phpcs:disable WordPress.Security.NonceVerification.Missing

			$resources_data = isset( $_POST['_yith_booking_resources_data'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_resources_data'] ) ) : array();
			$resources_data = array_map( 'yith_wcbk_resource_data', $resources_data );

			$priority = 0;
			foreach ( $resources_data as &$resource_data ) {
				$resource_data->set_product_id( $product->get_id() );
				$resource_data->set_priority( $priority );
				$priority ++;
			}

			$product->set_props(
				array(
					'enable_resources'            => isset( $_POST['_yith_booking_enable_resources'] ),
					'resource_assignment'         => isset( $_POST['_yith_booking_resource_assignment'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_resource_assignment'] ) ) : null,
					'resources_layout'            => isset( $_POST['_yith_booking_resources_layout'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_resources_layout'] ) ) : null,
					'resources_label'             => isset( $_POST['_yith_booking_resources_label'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_resources_label'] ) ) : null,
					'resources_field_label'       => isset( $_POST['_yith_booking_resources_field_label'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_resources_field_label'] ) ) : null,
					'resources_field_placeholder' => isset( $_POST['_yith_booking_resources_field_placeholder'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_resources_field_placeholder'] ) ) : null,
					'resource_is_required'        => isset( $_POST['_yith_booking_resource_is_required'] ),
					'resources_data'              => $resources_data,
				)
			);

			// phpcs:enable
		}

		/**
		 * Triggered before updating product props.
		 *
		 * @param WC_Product_Booking $product The booking product.
		 */
		protected function before_updating_product_props( WC_Product_Booking $product ) {
			// Remove resources if resources are disabled.
			if ( ! $product->get_enable_resources( 'edit' ) && $product->get_resources_data( 'edit' ) ) {
				$product->set_resources_data( array() );
			}
		}

		/**
		 * Triggered before updating product props.
		 *
		 * @param mixed              $value   The value.
		 * @param string             $prop    The prop.
		 * @param WC_Product_Booking $product The booking product.
		 *
		 * @return mixed The sanitized value.
		 */
		protected function sanitize_prop_value_before_saving( $value, string $prop, WC_Product_Booking $product ) {
			switch ( $prop ) {
				case 'enable_resources':
				case 'resource_is_required':
					$value = wc_bool_to_string( $value );
					break;
			}

			return $value;
		}

		/**
		 * Update product extra data.
		 *
		 * @param WC_Product_Booking $product The booking product.
		 * @param bool               $force   Force flag.
		 *
		 * @return array Array of updated props.
		 */
		protected function update_product_extra_data( WC_Product_Booking $product, bool $force ): array {
			$updated_props = array();
			if ( $this->update_product_resources_data( $product, $force ) ) {
				$updated_props[] = 'resources_data';
			}

			return $updated_props;
		}

		/**
		 * Read product extra data.
		 *
		 * @param WC_Product_Booking $product The booking product.
		 */
		protected function read_product_extra_data( WC_Product_Booking $product ) {
			global $wpdb;
			$resources_data = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM $wpdb->yith_wcbk_product_resources WHERE product_id=%d ORDER BY priority ASC",
					$product->get_id()
				)
			);

			$resources_data = array_filter( array_map( 'yith_wcbk_resource_data', $resources_data ) );

			$product->set_resources_data( $resources_data );
		}

		/**
		 * Update resource data in product.
		 *
		 * @param WC_Product_Booking $product The booking product.
		 * @param bool               $force   Force flag.
		 *
		 * @return bool True if updated; false otherwise.
		 */
		private function update_product_resources_data( $product, $force ) {
			global $wpdb;
			$changed_props = $product->get_changes();
			$updated       = false;

			if ( $force || array_key_exists( 'resources_data', $changed_props ) ) {
				$resources_data = $product->get_resources_data( 'edit' );
				$old_ids        = $wpdb->get_col( $wpdb->prepare( "SELECT resource_id FROM $wpdb->yith_wcbk_product_resources WHERE product_id=%d", $product->get_id() ) );
				$old_ids        = array_map( 'absint', $old_ids );
				$new_ids        = array_map(
					function ( YITH_WCBK_Resource_Data $resource_data ) {
						return $resource_data->get_resource_id( 'edit' );
					},
					$resources_data
				);

				$to_add_ids    = array_diff( $new_ids, $old_ids );
				$to_remove_ids = array_map( 'absint', array_diff( $old_ids, $new_ids ) );
				$to_update_ids = array_intersect( $old_ids, $new_ids );

				if ( $to_remove_ids ) {
					$updated       = true;
					$to_remove_ids = "'" . implode( "', '", $to_remove_ids ) . "'";
					$wpdb->query(
						$wpdb->prepare(
							"DELETE FROM $wpdb->yith_wcbk_product_resources WHERE product_id = %d AND resource_id IN ($to_remove_ids)", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
							$product->get_id()
						)
					);
				}

				$to_add = array_filter(
					$resources_data,
					function ( YITH_WCBK_Resource_Data $resource_data ) use ( $to_add_ids ) {
						return in_array( $resource_data->get_resource_id( 'edit' ), $to_add_ids, true );
					}
				);

				$to_update = array_filter(
					$resources_data,
					function ( YITH_WCBK_Resource_Data $resource_data ) use ( $to_update_ids ) {
						return in_array( $resource_data->get_resource_id( 'edit' ), $to_update_ids, true );
					}
				);

				foreach ( $to_add as $data ) {
					$updated = true;
					$props   = array(
						'product_id'                      => $product->get_id(),
						'resource_id'                     => $data->get_resource_id( 'edit' ),
						'base_price'                      => $data->get_base_price( 'edit' ),
						'fixed_price'                     => $data->get_fixed_price( 'edit' ),
						'multiply_fixed_price_per_person' => $data->get_multiply_fixed_price_per_person( 'edit' ),
						'multiply_base_price_per_person'  => $data->get_multiply_base_price_per_person( 'edit' ),
						'priority'                        => $data->get_priority( 'edit' ),
					);
					$wpdb->insert(
						$wpdb->yith_wcbk_product_resources,
						$props
					);
				}

				foreach ( $to_update as $data ) {
					$updated = true;
					$props   = array(
						'base_price'                      => $data->get_base_price( 'edit' ),
						'fixed_price'                     => $data->get_fixed_price( 'edit' ),
						'multiply_fixed_price_per_person' => $data->get_multiply_fixed_price_per_person( 'edit' ),
						'multiply_base_price_per_person'  => $data->get_multiply_base_price_per_person( 'edit' ),
						'priority'                        => $data->get_priority( 'edit' ),
					);
					$where   = array(
						'product_id'  => $product->get_id(),
						'resource_id' => $data->get_resource_id( 'edit' ),
					);
					$wpdb->update(
						$wpdb->yith_wcbk_product_resources,
						$props,
						$where
					);
				}

				// Clear cashes related to added/removed resources.
				yith_wcbk_clear_resource_related_caches( $to_add_ids );
				yith_wcbk_clear_resource_related_caches( $to_remove_ids );
			}

			return $updated;
		}

		/**
		 * Delete product extra data.
		 *
		 * @param int $id The product ID.
		 */
		protected function handle_product_delete( int $id ) {
			global $wpdb;

			$resource_ids = $wpdb->get_col( $wpdb->prepare( "SELECT resource_id FROM $wpdb->yith_wcbk_product_resources WHERE product_id=%d", $id ) );

			if ( $resource_ids ) {
				yith_wcbk_clear_resource_related_caches( $resource_ids );
			}

			$wpdb->delete(
				$wpdb->yith_wcbk_product_resources,
				array(
					'product_id' => $id,
				)
			);
		}
	}
}
