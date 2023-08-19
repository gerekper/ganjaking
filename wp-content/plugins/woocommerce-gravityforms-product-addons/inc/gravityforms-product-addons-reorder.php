<?php


class WC_GFPA_Reorder {

	private static $instance;

	public static function register() {
		if ( self::$instance == null ) {
			self::$instance = new WC_GFPA_Reorder;
		}
	}


	protected function __construct() {

		//Order Again
		add_filter( 'woocommerce_order_again_cart_item_data', array(
			$this,
			'on_get_order_again_cart_item_data'
		), 10, 3 );

		add_action( 'wcs_before_renewal_setup_cart_subscriptions', array(
			$this,
			'on_wcs_before_renewal_setup_cart_subscriptions'
		) );
		add_action( 'wcs_after_renewal_setup_cart_subscriptions', array(
			$this,
			'on_wcs_after_renewal_setup_cart_subscriptions'
		) );

		add_filter( 'woocommerce_order_item_permalink', array(
			$this,
			'get_woocommerce_order_item_permalink'
		), 10, 3 );

	}

	/**
	 * Update the permalink from the orders screen to users can visit the product page and see their previous data.
	 *
	 * @param string $permalink
	 * @param WC_Order_Item $order_item
	 * @param WC_Order $order
	 *
	 * @return string
	 */
	public function get_woocommerce_order_item_permalink( string $permalink, WC_Order_Item $order_item, WC_Order $order ): string {
		//Check the order item is a product, and that the order item is a WC_Order_Item_Product
		if ( $order_item->get_type() !== 'line_item' || ! $order_item instanceof WC_Order_Item_Product ) {
			return $permalink;
		}

		//Check the product is a gravity form product
		$product_id                = $order_item->get_product_id();
		$current_gravity_form_data = wc_gfpa()->get_gravity_form_data( $product_id );
		if ( empty( $current_gravity_form_data ) ) {
			return $permalink;
		}

		//Check the order item has a lead and data
		$form_submission_history = $order_item->get_meta( '_gravity_forms_history', true );
		if ( empty( $form_submission_history ) ) {
			return $permalink;
		}

		//Check the form submission history has a lead and data
		$lead_data = $form_submission_history['_gravity_form_lead'];
		$form_data = $form_submission_history['_gravity_form_data'];

		if ( empty( $lead_data ) || empty( $form_data ) ) {
			return $permalink;
		}

		//Check the form still exists
		$form = GFAPI::get_form( $lead_data['form_id'] );
		if ( ! $form ) {
			return $permalink;
		}

		//Check the current form matches the form in the order item
		if ( $current_gravity_form_data['id'] != $lead_data['form_id'] ) {
			return $permalink;
		}

		//Check the form is active and not a draft and not in the trash
		if ( $form['is_active'] != 1 || $form['is_trash'] == 1 ) {
			return $permalink;
		}

		//Everything looks good, add the order id, order item id and key to the permalink
		$permalink = add_query_arg( array(
			'wc_gforms_reorder_id'      => $order->get_id(),
			'wc_gforms_reorder_item_id' => $order_item->get_id(),
		), $permalink );

		return $permalink;
	}

	public function on_wcs_before_renewal_setup_cart_subscriptions() {
		remove_filter( 'woocommerce_order_again_cart_item_data', array(
			$this,
			'on_get_order_again_cart_item_data'
		), 10 );
	}

	public function on_wcs_after_renewal_setup_cart_subscriptions() {
		add_filter( 'woocommerce_order_again_cart_item_data', array(
			$this,
			'on_get_order_again_cart_item_data'
		), 10, 3 );
	}

	/**
	 * @param array $data
	 * @param WC_Order_Item $item
	 * @param WC_Order $order
	 *
	 * @return mixed
	 */
	public function on_get_order_again_cart_item_data( array $data, WC_Order_Item $item, WC_Order $order ) {

		// Make sure the order item is a WC_Order_Item_Product. If not, return the data.
		if ( $item->get_type() !== 'line_item' || ! $item instanceof WC_Order_Item_Product ) {
			return $data;
		}

		// Try to get the product from the order item. If it's not a product, return the data.
		$product = $item->get_product();
		if ( ! $product instanceof WC_Product ) {
			return $data;
		}


		// Regular add to cart validation is disabled in the gravityforms-product-addons-cart.php during reorder.
		// This is so we can do different validation on re-order versus add to cart.
		add_filter( 'woocommerce_add_to_cart_validation', [ $this, 'add_to_cart_validation' ], 99, 6 );

		if ( isset( $data['subscription_resubscribe'] ) ) {
			return $data;
		}

		$order_id              = $order->get_id();
		$order_item_id         = $item->get_id();
		$order_item_product_id = $item->get_product_id();

		GFCommon::log_debug( "Gravity Forms Product Addons: Getting Order Again Item Data (#{$order_id}), Item: (#{$order_item_id}), Product: (#{$order_item_product_id})" );
		$history = $item->get_meta( '_gravity_forms_history', true );

		if ( $history ) {
			$previous_entry_data = isset( $history['_gravity_form_lead'] ) ? $history['_gravity_form_lead'] : false;
			$form_fields_hash    = isset( $history['_gravity_form_hash'] ) ? $history['_gravity_form_hash'] : false;
			$gravity_form_data   = wc_gfpa()->get_gravity_form_reorder_data( $order_item_product_id, $history['_gravity_form_data'] );

			if ( $previous_entry_data && $gravity_form_data ) {

				$hydrate_defaults        = isset( $gravity_form_data['reorder_hydrate_defaults'] ) && $gravity_form_data['reorder_hydrate_defaults'] == 'yes';
				$reorder_processing_type = $gravity_form_data['reorder_processing'] ?? 'none';

				$entry = false;
				GFCommon::log_debug( "Gravity Forms Product Addons: Order Again Item Data - History Found (#{$order_id}), Item: (#{$order_item_id})" );

				$data['_gravity_form_data'] = $gravity_form_data;
				$data['_gravity_form_hash'] = $form_fields_hash;

				if ( $reorder_processing_type == 'resubmit' ) {

					try {
						// Disable notifications, confirmations and other processing.
						WC_GFPA_Hook_Manager::disable_notifications_and_confirmations( $gravity_form_data['id'] );

						// Disable all hooks that may be triggered during the submission process.
						WC_GFPA_Hook_Manager::disable_gform_after_submission_hooks( $gravity_form_data['id'] );

						// Resubmit the entry to get the latest values.
						$resubmission_result = WC_GFPA_Submission_Helpers::resubmit_entry( $gravity_form_data['id'], $previous_entry_data, $hydrate_defaults );

						// Re-enable all hooks that may be triggered during the submission process.  In case the form is submitted for some reason by another process. This is unlikely but possible.
						WC_GFPA_Hook_Manager::enable_gform_after_submission_hooks( $gravity_form_data['id'] );

						if ( is_wp_error( $resubmission_result ) ) {
							GFCommon::log_debug( "Gravity Forms Product Addons: Order Again Item Data - Error Resubmitting Entry (#{$order_id}), Item: (#{$order_item_id})" );
							GFCommon::log_debug( $resubmission_result->get_error_message() );
						} else if ( ! $resubmission_result['is_valid'] ) {
							$entry = $this->prepare_validation_error_for_cart( $order_id, $order_item_id, $resubmission_result, $gravity_form_data['id'] );
						} else {
							// Everything was ok with the resubmission.  Get the entry and use that for the cart item data.
							$entry = GFAPI::get_entry( $resubmission_result['entry_id'] );

							// Make sure to clean up the entry. Use our safe delete so that file uploads are left intact.
							WC_GFPA_Helpers_Entry::safe_delete_entry( $entry );
						}
					} catch ( Exception $e ) {
						GFCommon::log_debug( "Gravity Forms Product Addons: Order Again Item Data - Error Resubmitting Entry (#{$order_id}), Item: (#{$order_item_id})" );
						GFCommon::log_debug( $e->getMessage() );
						$entry = $previous_entry_data;
					}

				} else if ( $reorder_processing_type == 'revalidate' ) {

					try {
						// Revalidate the entry to get make sure it is still valid.
						$validation_result = WC_GFPA_Submission_Helpers::revalidate_entry( $gravity_form_data['id'], $previous_entry_data );

						if ( is_wp_error( $validation_result ) ) {
							GFCommon::log_debug( "Gravity Forms Product Addons: Order Again Item Data - Error Revalidating Entry (#{$order_id}), Item: (#{$order_item_id})" );
							GFCommon::log_debug( $validation_result->get_error_message() );
						} else if ( ! $validation_result['is_valid'] ) {
							$entry = $this->prepare_validation_error_for_cart( $order_id, $order_item_id, $validation_result, $gravity_form_data['id'] );
						} else {

							// Everything was ok with the revalidation.  Use the previous entry data as is.
							$entry = $previous_entry_data;
						}
					} catch ( Exception $e ) {
						GFCommon::log_debug( "Gravity Forms Product Addons: Order Again Item Data - Error Revalidating Entry (#{$order_id}), Item: (#{$order_item_id})" );
						GFCommon::log_debug( $e->getMessage() );
						$entry = $previous_entry_data;
					}
				} else {

					// Use the previous entry data as is.
					$entry = $previous_entry_data;

					// Unset the entry id so that a new entry can be created when the order is placed.
					unset( $entry['id'] );
				}

				// Add the entry.  This is either the previous entry or the resubmitted entry, or a validation error.
				$data['_gravity_form_lead'] = $entry;
			} else {
				GFCommon::log_debug( "Gravity Forms Product Addons: Order Again Item Data - No _gravity_form_lead or _gravity_form_data found (#{$order_id}), Item: (#{$order_item_id})" );
			}

		} else {
			GFCommon::log_debug( "Gravity Forms Product Addons: Order Again Item Data - No Gravity Forms History Found (#{$order_id}), Item: (#{$order_item_id})" );
		}

		return $data;
	}

	public function add_to_cart_validation( $valid, $product_id, $quantity, $variation_id, $variations, $cart_item_data ) {
		remove_filter( 'woocommerce_add_to_cart_validation', [ $this, 'add_to_cart_validation' ], 99 );

		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return $valid;
		}

		if ( isset( $cart_item_data['_gravity_form_lead'] ) ) {

			$entry_data = $cart_item_data['_gravity_form_lead'];
			if ( empty( $entry_data ) ) {
				return false;
			}

			//Make sure the form still exists
			$form = GFAPI::get_form( $entry_data['form_id'] ?? 0 );
			if ( ! $form ) {
				return false;
			}

			$gravity_form_data = wc_gfpa()->get_gravity_form_data( $product_id );
			if ( empty( $gravity_form_data ) ) {
				return false;
			}

			// If the lead was not successfully created during the order_again_item_data filter, then we should have a validation error / message from Gravity Forms.
			// If so, then we should not allow the item to be added to the cart, and we should display the validation message.
			if ( isset( $entry_data['is_valid'] ) && ! $entry_data['is_valid'] ) {

				// If we have the wc_gforms_reorder_id and wc_gforms_reorder_item_id query string parameters, then we should use those to generate a permalink to the product.
				$permalink = '';

				if ( isset( $entry_data['wc_gforms_reorder_id'] ) && isset( $entry_data['wc_gforms_reorder_item_id'] ) ) {
					$permalink = get_permalink( $product_id );
					$permalink = add_query_arg( [
						'wc_gforms_reorder_id'      => $entry_data['wc_gforms_reorder_id'],
						'wc_gforms_reorder_item_id' => $entry_data['wc_gforms_reorder_item_id'],
					], $permalink );
				}

				// If we have a permalink, then we should display the validation message as a wc_notice.
				if ( ! empty( $permalink ) ) {
					wc_add_notice( sprintf( __( 'You will need to add %s to your cart again. You can <a href="%s">click here to reorder the item.</a>', 'woocommerce-gravityforms-product-addons' ), $product->get_name(), $permalink ), 'error' );
				}

				return false;
			}

			// Make sure the form id from the entry matches the form attached to the product.
			if ( isset( $gravity_form_data['bulk_id'] ) ) {
				if ( $gravity_form_data['id'] != $entry_data['form_id'] && $gravity_form_data['bulk_id'] != $entry_data['form_id'] ) {
					return false;
				}
			} elseif ( $gravity_form_data['id'] != $entry_data['form_id'] ) {
				return false;
			}


			//Make sure the form and the entries form id match
			if ( $form['id'] != $entry_data['form_id'] ) {
				return false;
			}

			//Make sure the form is active
			if ( $form['is_active'] != 1 ) {
				return false;
			}

			//Make sure the form is not a draft
			if ( $form['is_trash'] == 1 ) {
				return false;
			}


		}

		return $valid;
	}


	public function validate_entry( $form_id, $field_values ): bool {

		$form     = RGFormsModel::get_form_meta( $form_id );
		$is_valid = true;

		if ( $form && $form['id'] == $form_id ) {
			foreach ( $form['fields'] as &$field ) {

				// don't validate adminOnly fields.
				if ( $field->is_administrative() ) {
					continue;
				}

				//ignore validation if field is hidden
				if ( RGFormsModel::is_field_hidden( $form, $field, $field_values, $field_values ) ) {
					$field->is_field_hidden = true;

					continue;
				}

				if ( $field->get_input_type() == 'fileupload' ) {
					continue;
				}

				if ( $field->get_input_type() == 'email' ) {
					$field->emailConfirmEnabled = false;
				}


				$inputs = $field->get_entry_inputs();

				if ( is_array( $inputs ) ) {
					$value = array();
					foreach ( $inputs as $input ) {
						$v = '';

						if ( isset( $field_values[ strval( $input['id'] ) ] ) ) {
							$v = $field_values[ strval( $input['id'] ) ];
						}

						$value[ strval( $input['id'] ) ] = $v;
					}
				} else {
					$value = $field_values[ $field->id ] ?? '';
				}

				$input_type = RGFormsModel::get_input_type( $field );

				//display error message if field is marked as required and the submitted value is empty
				if ( $field->isRequired && $field->is_value_submission_empty( $form_id ) ) {
					$field->failed_validation  = true;
					$field->validation_message = empty( $field->errorMessage ) ? __( 'This field is required.', 'gravityforms' ) : $field->errorMessage;
				}

				$field->validate( $value, $form );

				$custom_validation_result = gf_apply_filters( array(
					'gform_field_validation',
					$form['id'],
					$field->id
				), array(
					'is_valid' => $field->failed_validation ? false : true,
					'message'  => $field->validation_message
				), $value, $form, $field );

				$field->failed_validation  = rgar( $custom_validation_result, 'is_valid' ) ? false : true;
				$field->validation_message = rgar( $custom_validation_result, 'message' );

				if ( $field->failed_validation ) {
					$is_valid = false;
				}
			}

			return $is_valid;
		} else {
			return false;
		}

	}

	/**
	 * @param int $order_id
	 * @param int $order_item_id
	 * @param $validation_result
	 * @param $id
	 *
	 * @return mixed
	 */
	public function prepare_validation_error_for_cart( int $order_id, int $order_item_id, $validation_result, $id ) {
		GFCommon::log_debug( "Gravity Forms Product Addons: Order Again Item Data - Entry Not Valid (#{$order_id}), Item: (#{$order_item_id})" );
		$validation_messages = $validation_result['validation_messages'] ?? [];
		foreach ( $validation_messages as $validation_message ) {
			GFCommon::log_debug( $validation_message );
		}

		// Track the form id, gravity forms validation error doesn't do this for us.  We need this for the wc_notices to generate a permalink to the product with the wc_gforms_reorder_id and wc_gforms_reorder_item_id query string parameters.
		$validation_result['form_id'] = $id;

		// Track the order id and the order item id.
		// This is used for the wc_notices to generate a permalink to the product with the wc_gforms_reorder_id and wc_gforms_reorder_item_id query string parameters.
		// See the reorder cart item validation function in this class for usage.
		$validation_result['wc_gforms_reorder_id']      = $order_id;
		$validation_result['wc_gforms_reorder_item_id'] = $order_item_id;

		$entry = $validation_result;

		return $entry;
	}

}
