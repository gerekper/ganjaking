<?php

class GP_Inventory_Type_Advanced extends GP_Inventory_Type_Simple {

	public static $type = 'advanced';

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct() {
		parent::__construct();

		// @todo this does not work with duplicating.
		add_action( 'gform_after_save_form', array( $this, 'attach_advanced_fields_to_resource' ) );
		add_action( 'gform_after_delete_field', array( $this, 'prune_field_from_resource_gpi_fields' ), 10, 2 );

		add_action( 'gform_after_delete_form', array( $this, 'purge_form_from_resource_gpi_fields' ) );

		// Auto-refreshing when property is changed
		add_action( 'wp_ajax_gpi_refresh_field', array( $this, 'ajax_refresh' ) );
		add_action( 'wp_ajax_nopriv_gpi_refresh_field', array( $this, 'ajax_refresh' ) );

		// Prevent Gravity Forms from handling request as submission. It's important to send gform_submit to get accurate
		// submission values
		if ( isset( $_POST['action'] ) && $_POST['action'] === 'gpi_refresh_field' ) {
			remove_action( 'wp', array( 'GFForms', 'maybe_process_form' ), 9 );
			remove_action( 'admin_init', array( 'GFForms', 'maybe_process_form' ), 9 );
		}

		add_filter( 'gform_register_init_scripts', array( $this, 'add_init_script' ), 10, 2 );

		add_filter( 'gpi_property_map_field_value', array( $this, 'property_map_field_value_use_first_choice' ), 10, 2 );
		add_filter( 'gpi_property_map_field_value', array( $this, 'property_map_use_save_and_continue_values' ), 11, 2 );
		add_filter( 'gpi_property_map_field_value', array( $this, 'property_map_use_save_value' ), 15, 3 );
	}

	public function ajax_refresh() {
		$entry = GFFormsModel::get_current_lead();

		if ( ! $entry ) {
			wp_send_json_error();
			return;
		}

		$form_id = rgpost( 'form_id' );

		check_ajax_referer( 'gp-inventory-refresh-form-' . $form_id, 'security' );

		$form  = gf_apply_filters( array( 'gform_pre_render', $form_id ), GFAPI::get_form( $form_id ), false, array() );
		$field = GFFormsModel::get_field( $form, rgpost( 'target_field_id' ) );

		if ( $field->get_input_type() === 'html' ) {
			$content = GWPreviewConfirmation::preview_replace_variables( $field->content, $form );
		} else {
			$inputs = rgar( $field, 'inputs' );

			if ( is_array( $inputs ) ) {
				$value = array();

				foreach ( $inputs as $input ) {
					$value[ $input['id'] ] = rgpost( 'input_' . str_replace( '.', '_', $input['id'] ) );
				}
			} else {
				$value = rgpost( 'input_' . $field->id );
			}

			/**
			 * Filter whether to reset the value of the current field with inventory that is using properties.
			 *
			 * Clearing out the value of choice-based fields will reset the selected choice back to the placeholder
			 * or the first available option.
			 *
			 * @since 1.0-beta-1.0
			 *
			 * @param boolean  $reset  Whether to reset the value. Defaults to `true` if choice-based field, `false` otherwise.
			 * @param GF_Field $field  The current field.
			 * @param array    $form   The current form.
			 */
			if ( gf_apply_filters( array( 'gpi_reset_value_on_property_change', $field->formId, $field->id ), ! ! rgar( $field, 'choices' ), $field, $form ) && ! rgpost( 'gpi_initial_property_refresh' ) ) {
				$value = null;
			}

			$content = $field->get_field_content( $value, true, $form );
			$content = str_replace( '{FIELD}', GFCommon::get_field_input( $field, $value, $entry['id'], $form['id'], $form ), $content );

			// Run through gform_field_content to disable choices.
			$content = gf_apply_filters( array( 'gform_field_content', $form['id'], $field->id ), $content, $field, $value, 0, $form['id'] );
		}

		wp_send_json_success( $content );
	}

	public function add_init_script( $form ) {

		if ( ! $this->is_applicable_form( $form, true ) ) {
			return;
		}

		foreach ( $this->get_applicable_fields( $form, true ) as $field_with_inventory ) {
			$trigger_field_ids = array_values( rgar( $field_with_inventory, 'gpiResourcePropertyMap' ) );

			if (
				empty( $trigger_field_ids )
				|| ! gp_inventory_type_advanced()->is_using_properties( $field_with_inventory )
			) {
				continue;
			}

			$args = array(
				'formId'           => $form['id'],
				'targetFieldId'    => $field_with_inventory->id,
				'triggerFieldIds'  => array_map( 'gpi_cast_to_input_id', $trigger_field_ids ),
				'ajaxUrl'          => admin_url( 'admin-ajax.php' ),
				'ajaxRefreshNonce' => wp_create_nonce( 'gp-inventory-refresh-form-' . $form['id'] ),
			);

			$script = 'new GPIProperties( ' . json_encode( $args ) . ' );';
			$slug   = implode( '_', array( 'gpi_properties', $form['id'], $field_with_inventory->id ) );

			GFFormDisplay::add_init_script( $form['id'], $slug, GFFormDisplay::ON_PAGE_RENDER, $script );
		}

	}

	/**
	 * Save form/field identifier to Resource meta to keep a record of all fields that are attached to a speicifc resource.
	 *
	 * This is necessary to create the queries for shared inventory across forms/fields that are using the same resource.
	 *
	 * For what it's worth, a pure MySQL solution isn't viable here as the form meta is stored as JSON.
	 *
	 * @param $form_meta array
	 */
	public function attach_advanced_fields_to_resource( $form_meta ) {
		global $wpdb;

		foreach ( rgar( $form_meta, 'fields', array() ) as $field ) {
			// Comparison used here rather than is_applicable_field as is_applicable_field() will not return true for
			// advanced if the field is choice-based.
			if ( $this->is_applicable_field( $field, true ) ) {
				$resource_id = rgar( $field, 'gpiResource' );

				$form_field_id = $field->formId . '_' . $field->id;

				// Remove this field from other resources if already attached.
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->postmeta} WHERE meta_key = 'gpi_field' AND meta_value = %s AND post_id != %d", $form_field_id, $resource_id ) );

				$existing_fields = get_post_meta( $resource_id, 'gpi_field' );

				if ( empty( $existing_fields ) || ! in_array( $form_field_id, $existing_fields, true ) ) {
					add_post_meta( $resource_id, 'gpi_field', $form_field_id );
				}
			}
		}
	}

	/**
	 * Remove field from resources
	 *
	 * @param int $form_id
	 * @param int $field_id
	 */
	public function prune_field_from_resource_gpi_fields( $form_id, $field_id ) {
		global $wpdb;

		$form_field_id = $form_id . '_' . $field_id;
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->postmeta} WHERE meta_key = 'gpi_field' AND meta_value = %s", $form_field_id ) );
	}

	/**
	 * Remove gpi_field's meta values set on Resources for forms that have been deleted.
	 */
	public function purge_form_from_resource_gpi_fields( $form_id ) {
		global $wpdb;

		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->postmeta} WHERE meta_key = 'gpi_field' AND meta_value LIKE %s", $wpdb->esc_like( $form_id . '_' ) . '%' ) );
	}

	/**
	 * @param number $resource_id
	 *
	 * @return GF_Field[]
	 */
	public function get_resource_fields( $resource_id ) {
		$form_field_ids = get_post_meta( $resource_id, 'gpi_field' );
		$fields         = array();

		if ( empty( $form_field_ids ) || ! is_array( $form_field_ids ) ) {
			return $fields;
		}

		foreach ( $form_field_ids as $form_field_id ) {
			$split = explode( '_', $form_field_id );
			$form  = GFAPI::get_form( $split[0] );
			$field = null;

			/* Note, we explicitly do not use GFAPI::get_field() here as this method typically runs before other add-ons and can cause the field to become cached. */
			foreach ( $form['fields'] as $current_field ) {
				if ( $current_field->id === (int) $split[1] ) {
					$field = $current_field;
					break;
				}
			}

			if ( $field ) {
				$fields[] = $field;
			}
		}

		return $fields;
	}

	public function get_stock_quantity( $field ) {
		$inventory_limit = gp_inventory_resources()->get_resource_inventory_limit( rgar( $field, 'gpiResource' ) );

		/**
		 * Filter the inventory limit for fields using the Advanced Inventory Type.
		 *
		 * @since 1.0-beta-1.0
		 *
		 * @param int       $inventory_limit  Inventory limit of the current field.
		 * @param GF_Field  $field            The current field.
		 */
		return gf_apply_filters( array( 'gpi_inventory_limit_advanced', $field->formId, $field->id ), $inventory_limit, $field );
	}

	/**
	 * @param $field GF_Field
	 *
	 * @return boolean
	 */
	public function is_using_properties( $field ) {
		return ! ! count( gp_inventory_resources()->get_resource_properties( rgar( $field, 'gpiResource' ) ) );
	}

	/**
	 * @param $field GF_Field
	 *
	 * Add hooks to customize query
	 */
	public function add_query_hooks( $field ) {
		$this->remove_query_hooks(); // prevent duplicate hooks

		parent::add_query_hooks( $field );

		add_filter( 'gpi_query', array( $this, 'resource_and_properties' ), 9, 2 );
	}

	/**
	 * Remove hooks for query customization
	 */
	public function remove_query_hooks() {
		parent::remove_query_hooks();

		remove_filter( 'gpi_query', array( $this, 'resource_and_properties' ), 9 );
	}

	/**
	 * Modify query to search for entry meta across all forms/fields that use the same resource. Additionally, add
	 * necessary statements for properties.
	 *
	 * @param $query array
	 * @param $field GF_Field
	 *
	 * @return array
	 */
	public function resource_and_properties( $query, $field ) {
		global $wpdb;

		if ( rgar( $field, 'gpiInventory' ) !== GP_Inventory_Type_Advanced::$type ) {
			return $query;
		}

		$resource_id     = rgar( $field, 'gpiResource' );
		$resource_fields = $this->get_resource_fields( $resource_id );

		/*
		 * $resource_fields should have at least the current field if we're getting to this point.
		 *
		 * The main reason why it would be empty is a discrepancy in the meta set on the resource.
		 */
		if ( empty( $resource_fields ) ) {
			$resource_fields = array( $field );
		}

		$join                = array();
		$wheres              = array();
		$form_id             = $field['formId'];
		$form                = GFAPI::get_form( $form_id );
		$property_map_values = array();

		foreach ( rgar( $field, 'gpiResourcePropertyMap' ) as $property_id => $mapped_field_id ) {
			$mapped_field = GFFormsModel::get_field( $form_id, $mapped_field_id );

			if ( ! $mapped_field ) {
				continue;
			}

			$property_map_values[ $property_id ] = $this->get_property_map_value( $mapped_field, $form );
		}

		/**
		 * Filter to modify the values that are used for scopes when querying the inventory.
		 *
		 * @param array $property_map_values Values used for the scopes.
		 *                                   Defaults to the submitted values from the form or the default value/dynamically populated if not already submitted.
		 * @param GF_Field $field The current field.
		 * @param array $form The current form.
		 *
		 * @since 1.0-beta-1.7
		 */
		$property_map_values = gf_apply_filters( array(
			'gpi_property_map_values',
			$field->formId,
			$field->id,
		), $property_map_values, $field, $form );

		foreach ( $resource_fields as $resource_field ) {
			if ( $resource_field->type === 'product' && ! gp_inventory_type_choices()->is_applicable_field( $resource_field ) ) {
				$input_ids = $this->get_quantity_input_ids( $resource_field );
			} else {
				$input_ids = array( $resource_field->id );
			}

			$property_map = rgar( $resource_field, 'gpiResourcePropertyMap' );

			if ( count( $input_ids ) > 1 ) {
				$meta_keys_array = implode( ', ', array_map( 'esc_sql', $input_ids ) );
				$where           = $wpdb->prepare( "\n(e.form_id = %d AND em.form_id = %d AND em.meta_key IN ( {$meta_keys_array} ))", $resource_field->formId, $resource_field->formId );
			} else {
				$where = $wpdb->prepare( "\n(e.form_id = %d AND em.form_id = %d AND em.meta_key = %s)", $resource_field->formId, $resource_field->formId, $input_ids[0] );
			}

			foreach ( $property_map as $property_id => $field_id ) {
				$mapped_field = GFFormsModel::get_field( $resource_field->formId, $field_id );

				if ( ! isset( $property_map_values[ $property_id ] ) || ! $mapped_field ) {
					continue;
				}

				$alias = 'p_' . md5( $property_id . $mapped_field['formId'] );

				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$join[ $alias ] = $wpdb->prepare( "\nLEFT JOIN {$wpdb->prefix}gf_entry_meta {$alias} ON (em.entry_id = {$alias}.entry_id AND em.form_id = {$alias}.form_id AND {$alias}.meta_key = %s)", $field_id );

				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$where .= $wpdb->prepare( " AND {$alias}.meta_value = %s", $property_map_values[ $property_id ] );
			}

			$wheres[] = sprintf( '( %s )', $where );
		}

		$query['join'] .= implode( "\n", $join );

		$wheres = implode( ' OR ', $wheres );

		// Replacement for multiple quantity fields
		$query['where'] = preg_replace( '/\s+em\.form_id = \d+\s+AND em\.meta_key IN\( .*? \)/', "(\n{$wheres}\n)", $query['where'] );

		// Replacement for single meta key (or single quantity field)
		$query['where'] = preg_replace( '/\s+em\.form_id = \d+\s+AND \(em\.meta_key = \'\d+\.?\d*?\'\)/', "(\n{$wheres}\n)", $query['where'] );

		return $query;
	}

	public function get_property_map_value( $field, $form ) {
		if ( empty( $field ) || empty( $form ) ) {
			return null;
		}

		// Fetch entry from submission if available. Otherwise, get default/dynpop value.
		if ( (int) rgpost( 'gform_submit' ) === (int) $form['id'] ) {
			$value = GFFormsModel::get_field_value( $field );
		} else {
			$value = $field->get_value_default_if_empty( GFFormsModel::get_parameter_value( $field->inputName, array(), $field ) );
		}

		return apply_filters( 'gpi_property_map_field_value', $value, $field, $form );
	}

	public function property_map_use_save_and_continue_values( $value, $field ) {
		$values = gpi_get_save_and_continue_values( rgar( $_REQUEST, 'gf_token' ) );

		if ( empty( $values ) ) {
			return $value;
		}

		return rgar( $values, $field->id );
	}

	public function property_map_field_value_use_first_choice( $value, $field ) {
		if ( ! rgblank( $value ) || empty( $field->choices ) ) {
			return $value;
		}

		return gpi_get_prelected_choice_value( $field );
	}

	/**
	 * Run the value through $field->get_value_save_entry() to convert it to the value that we can query the database with for existing entries.
	 *
	 * @param mixed $value
	 * @param GF_Field $field
	 * @param array $form
	 *
	 * @return mixed
	 */
	public function property_map_use_save_value( $value, $field, $form ) {
		return $field->get_value_save_entry( $value, $form, null, null, null );
	}

	public function validation( $validation_result ) {
		$validation_result = parent::validation( $validation_result );

		foreach ( $this->get_applicable_fields( $validation_result['form'] ) as $inventory_field ) {
			$quantity_input_ids = $this->get_quantity_input_ids( $inventory_field );

			if ( empty( $quantity_input_ids ) ) {
				continue;
			}

			$limit                          = $this->get_stock_quantity( $inventory_field );
			$inventory_insufficient_message = $this->get_inventory_insufficient_message( $inventory_field );
			$inventory_exhausted_message    = $this->get_inventory_exhausted_message( $inventory_field );

			/**
			 * @var array
			 */
			$form = $validation_result['form'];

			$exceeded_limit = false;

			foreach ( $form['fields'] as &$field ) {
				if ( ! in_array( (int) $field['id'], array_map( 'intval', $quantity_input_ids ), false ) ) {
					continue;
				}

				$resource_id     = rgar( $inventory_field, 'gpiResource' );
				$resource_fields = $this->get_resource_fields( $resource_id );

				if ( count( $resource_fields ) < 2 ) {
					continue;
				}

				$total_requested_qty = 0;
				$claimed_sum         = $this->get_claimed_inventory( $field );

				foreach ( $resource_fields as $resource_field ) {
					if ( rgar( $resource_field, 'formId' ) !== $field['formId'] ) {
						continue;
					}

					$quantity = $this->get_requested_quantity( $resource_field );

					if ( is_numeric( $quantity ) && $quantity > 0 ) {
						$total_requested_qty += $quantity;
					}
				}

				if ( ! $total_requested_qty || $claimed_sum + $total_requested_qty <= $limit ) {
					continue;
				}

				$exceeded_limit = true;
				$stock_left     = $limit - $claimed_sum >= 0 ? $limit - $claimed_sum : 0;

				$field['failed_validation'] = true;

				if ( $claimed_sum >= $limit ) {
					$field['validation_message'] = GFCommon::replace_variables( $inventory_exhausted_message, $form, GFFormsModel::get_current_lead() );
				} else {
					$inventory_insufficient_message = str_replace( '{requested}', number_format_i18n( $total_requested_qty ), $inventory_insufficient_message );
					$inventory_insufficient_message = str_replace( '{available}', number_format_i18n( $stock_left ), $inventory_insufficient_message );
					$inventory_insufficient_message = str_replace( '{limit}', number_format_i18n( $limit ), $inventory_insufficient_message );

					$field['validation_message'] = GFCommon::replace_variables( $inventory_insufficient_message, $form, GFFormsModel::get_current_lead() );
				}
			}

			$validation_result['is_valid'] = ! $validation_result['is_valid'] ? false : ! $exceeded_limit;
		}

		return $validation_result;
	}

}

function gp_inventory_type_advanced() {
	return GP_Inventory_Type_Advanced::get_instance();
}
