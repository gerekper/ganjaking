<?php

abstract class GP_Inventory_Type {

	/**
	 * String representation of the inventory type. This is the string that will be saved to the field settings.
	 *
	 * @var string
	 * @example 'simple'
	 * @example 'advanced'
	 */
	public static $type;

	public function __construct() {
		add_filter( 'gform_pre_render', array( $this, 'pre_render' ) );
		add_filter( 'gform_pre_render', array( $this, 'maybe_lockout' ), 11 );
		add_filter( 'gform_validation', array( $this, 'validation' ) );
	}

	/**
	 * @param $field GF_Field
	 *
	 * @return number
	 */
	abstract public function get_available_stock( $field );

	/**
	 * @param $field GF_Field
	 *
	 * @return boolean
	 */
	abstract public function is_in_stock( $field );

	/**
	 * @param $field GF_Field
	 *
	 * @return int
	 */
	abstract public function get_stock_quantity( $field );

	/**
	 * @param $field GF_Field
	 *
	 * @return int
	 */
	public function get_claimed_inventory( $field ) {
		global $wpdb;

		$this->add_query_hooks( $field );

		$query = $this->get_claimed_inventory_query( $field );
		$sql   = implode( "\n", $query );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$result = $wpdb->get_var( $sql );
		$sum    = intval( $result );

		$this->remove_query_hooks();

		/**
		 * Filter the amount of inventory that has been claimed.
		 *
		 * @since 1.0-beta-1.1
		 *
		 * @param int      $claimed_inventory The amount of inventory that has been claimed.
		 * @param GF_Field $field             The current field for which the inventory has been claimed.
		 */
		$sum = gf_apply_filters( array( 'gpi_claimed_inventory', $field->formId, $field->id ), $sum, $field );

		return $sum;
	}

	/**
	 * Method to be ran on the form and its fields for the current inventory type. Useful for modifying choices, labels,
	 * etc.
	 *
	 * @param $form array
	 *
	 * @return array Form
	 */
	public function pre_render( $form ) {
		return $form;
	}

	/**
	 * Gets the input ID that determines the quantity for the field.
	 *
	 * @param $field GF_Field
	 *
	 * @returns number[]
	 */
	public function get_quantity_input_ids( $field ) {
		if ( GFCommon::is_product_field( $field->type ) ) {
			return $this->get_product_quantity_input_ids( $field );
		} elseif ( ! empty( $field->choices ) ) {
			return array();
		}

		return array( $field->id );
	}

	/**
	 * Get the Quantity field or Product field where quantity ordered will be provided.
	 *
	 * @param $product_field GF_Field
	 * @param $form array
	 *
	 * @return GF_Field[]
	 */
	public function get_product_quantity_fields( $product_field ) {
		$form            = GFAPI::get_form( $product_field->formId );
		$product_field   = $product_field->type === 'product' ? $product_field : GFFormsModel::get_field( $form, $product_field->productField );
		$quantity_fields = GFCommon::get_product_fields_by_type( $form, array( 'quantity' ), $product_field->id );

		if ( empty( $quantity_fields ) ) {
			$quantity_fields = array( $product_field );
		}

		return $quantity_fields;
	}

	/**
	 * @param GF_Field $product_field
	 *
	 * @return number[] Quantity Input IDs
	 */
	public function get_product_quantity_input_ids( $product_field ) {
		$quantity_fields    = $this->get_product_quantity_fields( $product_field );
		$quantity_input_ids = array();

		foreach ( $quantity_fields as $quantity_field ) {
			if ( $quantity_field->type === 'quantity' ) {
				$quantity_input_ids[] = $quantity_field->id;
			} elseif ( in_array( GFFormsModel::get_input_type( $quantity_field ), array( 'singleproduct', 'calculation' ), true ) ) {
				$quantity_input_ids[] = "{$quantity_field->id}.3";
			}
		}

		return $quantity_input_ids;
	}

	/**
	 * @param $field GF_Field
	 * @param $bypass_choice_type_exclusion boolean The choice inventory strategy relies on _some_ logic from Simple
	 *   and Advanced. Most of the time, we don't want Simple and Advanced hooking into Choice fields, but there are
	 *   some situations where it is necessary.
	 *
	 * @returns boolean
	 */
	public function is_applicable_field( $field, $bypass_choice_type_exclusion = false ) {
		/**
		 * Prevent Simple or Advanced inventory types from trying to work their magic for choice-based fields.
		 */
		if ( ! $bypass_choice_type_exclusion && in_array( $field->get_input_type(), gp_inventory_type_choices()->input_types, true ) ) {
			return false;
		}

		if ( rgar( $field, 'gpiInventory' ) === static::$type ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if current form uses the current inventory type.
	 *
	 * @param $form array
	 */
	public function is_applicable_form( $form, $bypass_choice_check = false ) {
		if ( empty( $form['fields'] ) ) {
			return false;
		}

		foreach ( $form['fields'] as $field ) {
			if ( $this->is_applicable_field( $field, $bypass_choice_check ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Gets the applicable fields in a given form that need to have tracked inventory.
	 *
	 * @param $form array
	 * @param $bypass_choice_check bool return either simple or advanced even if a choice-based field
	 *
	 * @returns GF_Field[] | null
	 */
	public function get_applicable_fields( $form, $bypass_choice_check = false ) {
		if ( empty( $form['fields'] ) ) {
			return null;
		}

		$applicable_fields = array();

		foreach ( $form['fields'] as $field ) {
			if ( $this->is_applicable_field( $field, $bypass_choice_check ) ) {
				$applicable_fields[] = $field;
			}
		}

		return $applicable_fields;
	}

	/**
	 * @param $form array
	 *
	 * @return array
	 */
	public function maybe_lockout( $form ) {
		if ( ! $this->is_applicable_form( $form ) ) {
			return $form;
		}

		$applicable_fields = $this->get_applicable_fields( $form );

		foreach ( $applicable_fields as $field ) {
			if ( $this->is_in_stock( $field ) ) {
				continue;
			}

			// @todo this won't work with properties/AJAX refresh. We may want to hide this setting for fields attached
			//   to resources with properties.
			if ( rgar( $field, 'gpiHideForm' ) ) {
				add_filter( "gform_get_form_filter_{$form['id']}", function() use ( $field ) {
					return $this->get_inventory_exhausted_message( $field );
				} );
			} else {
				add_filter( "gform_field_input_{$form['id']}_{$field->id}", array( $this, 'hide_field' ), 10, 2 );

				$quantity_fields = GFCommon::get_product_fields_by_type( $form, array( 'quantity' ), $field->id );

				if ( ! empty( $quantity_fields ) ) {
					foreach ( $quantity_fields as $quantity_field ) {
						add_filter( "gform_field_input_{$form['id']}_{$quantity_field->id}", array( $this, 'hide_field' ), 10, 2 );
					}
				}
			}
		}

		return $form;
	}

	/**
	 * @param $validation_result array
	 *
	 * @return array
	 */
	public function validation( $validation_result ) {

		if ( ! $this->is_applicable_form( $validation_result['form'] ) ) {
			return $validation_result;
		}

		foreach ( $this->get_applicable_fields( $validation_result['form'] ) as $inventory_field ) {

			if ( GFCommon::is_product_field( $inventory_field->type ) ) {
				$quantity_input_ids = $this->get_quantity_input_ids( $inventory_field );
				if ( empty( $quantity_input_ids ) ) {
					continue;
				}
			} else {
				$quantity_input_ids = array( $inventory_field->id );
			}

			$limit                          = $this->get_stock_quantity( $inventory_field );
			$inventory_insufficient_message = $this->get_inventory_insufficient_message( $inventory_field );
			$inventory_exhausted_message    = $this->get_inventory_exhausted_message( $inventory_field );

			/**
			 * @var array
			 */
			$form = $validation_result['form'];

			$exceeded_limit = false;

			/**
			 * Loop through fields until quantity field is found.
			 */
			foreach ( $form['fields'] as &$field ) {
				if ( ! in_array( (int) $field->id, array_map( 'intval', $quantity_input_ids ), true ) ) {
					continue;
				}

				$requested_qty = $this->get_requested_quantity( $inventory_field );
				$field_sum     = $this->get_claimed_inventory( $inventory_field );

				if ( rgblank( $requested_qty ) || $field_sum + $requested_qty <= $limit ) {
					continue;
				}

				$exceeded_limit = true;
				$stock_left     = $limit - $field_sum >= 0 ? $limit - $field_sum : 0;

				$field['failed_validation'] = true;

				if ( $field_sum >= $limit ) {
					$field['validation_message'] = GFCommon::replace_variables( $inventory_exhausted_message, $form, GFFormsModel::get_current_lead() );
				} else {
					$inventory_insufficient_message = str_replace( '{requested}', number_format_i18n( $requested_qty ), $inventory_insufficient_message );
					$inventory_insufficient_message = str_replace( '{available}', number_format_i18n( $stock_left ), $inventory_insufficient_message );

					$field['validation_message'] = GFCommon::replace_variables( $inventory_insufficient_message, $form, GFFormsModel::get_current_lead() );
				}
			}

			$validation_result['form']     = $form;
			$validation_result['is_valid'] = ! $validation_result['is_valid'] ? false : ! $exceeded_limit;
		}

		return $validation_result;
	}

	public function get_requested_quantity( $field, $entry = false ) {
		$requested_quantity = ! empty( $field->choices ) ? 1 : 0;

		$quantity_input_ids = $this->get_quantity_input_ids( $field );

		if ( ! empty( $quantity_input_ids ) ) {
			$requested_quantity = 0;

			foreach ( $quantity_input_ids as $quantity_input_id ) {
				if ( $entry ) {
					$requested_quantity += (int) rgar( $entry, $quantity_input_id );
				} else {
					$requested_quantity += (int) rgpost( sprintf( 'input_%s', str_replace( '.', '_', $quantity_input_id ) ) );
				}
			}
		}

		/* Set requested quantity to 0 if the product field is hidden via Conditional Logic. */
		if ( GFFormsModel::is_field_hidden( GFAPI::get_form( $field->formId ), $field, array(), $entry ) ) {
			$requested_quantity = 0;
		}

		/**
		 * Filter the requested inventory amount.
		 *
		 * @since      1.0-beta-1.0
		 * @deprecated 1.0-beta-1.1 Use the {@see 'gpi_requested_quantity'} filter instead.
		 *
		 * @param int       $requested_quantity  Amount of inventory requested.
		 * @param GF_Field  $field               The current field.
		 */
		$requested_quantity = gf_apply_filters( array( 'gpi_requested_count', $field->formId, $field->id ), intval( $requested_quantity ), $field );

		/**
		 * Filter the requested inventory amount.
		 *
		 * @since 1.0-beta-1.1
		 *
		 * @param int       $requested_quantity  Amount of inventory requested.
		 * @param GF_Field  $field               The current field.
		 */
		return gf_apply_filters( array( 'gpi_requested_quantity', $field->formId, $field->id ), intval( $requested_quantity ), $field );
	}

	/**
	 * @param $field_content string
	 * @param $field GF_Field
	 *
	 * @return string
	 */
	public function hide_field( $field_content, $field ) {
		$quantity_input = '';

		// GF will default to a quantity of 1 if it can't find the input for a Quantity field.
		if ( $field->type === 'quantity' ) {
			$quantity_input = sprintf( '<input type="hidden" name="input_%d_%d" value="0" />', $field->formId, $field->id );
		}

		return sprintf( '<div class="ginput_container">%s%s</div>', $this->get_inventory_exhausted_message( $field ), $quantity_input );
	}

	/**
	 * @param $field GF_Field
	 *
	 * @return string
	 */
	public function get_inventory_exhausted_message( $field ) {
		return rgar( $field, 'gpiMessageInventoryExhausted', gp_inventory()->inventory_exhausted_default_message() );
	}

	/**
	 * @param $field GF_Field
	 *
	 * @return string
	 */
	public function get_inventory_insufficient_message( $field ) {
		return rgar( $field, 'gpiMessageInventoryInsufficient', gp_inventory()->inventory_insufficient_default_message() );
	}

}
