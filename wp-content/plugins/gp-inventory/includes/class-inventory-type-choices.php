<?php

class GP_Inventory_Type_Choices extends GP_Inventory_Type_Advanced {

	private static $instance = null;

	public static $type = 'choices';

	public $input_types;

	public $choiceless;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct() {
		parent::__construct();

		$this->input_types = apply_filters( 'gpi_choice_input_types', array( 'radio', 'select', 'checkbox', 'multiselect' ) );

		// Form Validation & Submission
		add_action( 'gform_entry_created', array( $this, 'flush_choice_count_cache_post_entry_creation' ), 10, 2 );
	}

	/**
	 * @param GF_Field $field
	 *
	 * @return bool
	 */
	public function is_in_stock( $field ) {
		$is_in_stock = ! isset( $this->choiceless[ $field->formId ] ) || ! in_array( $field['id'], $this->choiceless[ $field->formId ], true );

		/** This filter is documented in includes/class-inventory-type-advanced.php */
		return gf_apply_filters( array( 'gpi_is_in_stock', $field->formId, $field->id ), $is_in_stock, $field, null );
	}

	public function is_applicable_field( $field, $bypass_choice_type_exclusion = false ) {
		$is_inventory_enabled = ! ! rgar( $field, 'gpiInventory' ) && in_array( $field->get_input_type(), $this->input_types, true );

		return $is_inventory_enabled;
	}

	public function pre_render( $form ) {

		if ( ! is_array( $form ) ) {
			return $form;
		}

		foreach ( $form['fields'] as &$field ) {

			if ( ! $this->is_applicable_field( $field ) ) {
				continue;
			}

			// Only process the choices once. Some 3rd party plugins call gform_pre_render twice which can cause issues.
			if ( $field->gpiChoiceIsProcessed ) {
				break;
			} else {
				$field->gpiChoiceIsProcessed = true;
			}

			if ( ! isset( $this->choiceless[ $form['id'] ] ) ) {
				$this->choiceless[ $form['id'] ] = array();
			}

			$field->choices = $this->apply_choice_limits( $field->choices, $field, $form );

		}

		if ( $this->has_disabled_choice( $form ) ) {
			add_filter( 'gform_field_content', array( $this, 'disable_choice' ), 15, 2 );
			add_filter( 'gppa_hydrate_input_html', array( $this, 'disable_choice' ), 15, 2 );
		}

		return $form;
	}

	public function disable_choice( $content, $field ) {

		$field_type = GFFormsModel::get_input_type( $field );
		if ( ! in_array( $field_type, $this->input_types, true ) ) {
			return $content;
		}

		foreach ( $field['choices'] as $choice_id => $choice ) {

			if ( ! rgar( $choice, 'isDisabled' ) ) {
				continue;
			}

			if ( is_array( $field['inputs'] ) ) {
				foreach ( $field['inputs'] as $input_index => $input ) {
					if ( $input_index == $choice_id ) {
						$pieces    = explode( '.', $input['id'] );
						$choice_id = $pieces[1];
						break;
					}
				}
			}

			switch ( $field_type ) {
				case 'multiselect':
				case 'select':
					if ( in_array( $field['type'], array( 'product', 'option' ) ) ) {
						$price = GFCommon::to_number( $choice['price'] ) === false ? 0 : GFCommon::to_number( $choice['price'] );
						$value = sprintf( '%s|%s', $choice['value'], $price );
					} else {
						$value = $choice['value'];
					}
					$value  = esc_attr( $value );
					$search = "<option value='{$value}'";
					break;
				default:
					if ( version_compare( GFCommon::$version, '1.8.20.7', '>=' ) ) {
						$choice_html_id = "choice_{$field['formId']}_{$field['id']}_{$choice_id}";
					} else {
						$choice_html_id = "choice_{$field['id']}_{$choice_id}";
					}
					$search = "id='{$choice_html_id}'";
					break;
			}

			$replace = "$search disabled='disabled' class='gwlc-disabled'";
			$content = str_replace( $search, $replace, $content );

		}

		return $content;
	}


	/**
	 * @param $choices
	 * @param GF_Field $field
	 * @param $form
	 */
	public function apply_choice_limits( $choices, $field, $form ) {

		$filtered_choices = array();
		$choice_counts    = $this->get_choice_counts( $form['id'], $field );

		// allows to prevent the removal of choices, validation still occurs
		$remove = rgar( $field, 'gpiHideChoice', false );

		/**
		 * Filter whether choices should be removed.
		 *
		 * @since 1.0-beta-1.0
		 *
		 * @param boolean    $remove_choices  `true` to remove choices.
		 * @param GF_Field   $field           The current field.
		 * @param array      $form            The current form.
		 */
		$remove_choices = gf_apply_filters( array( 'gpi_remove_choices', $form['id'], $field->id ), $remove, $field, $form );

		/**
		 * Filter whether choices should be disabled if not removed.
		 *
		 * @since 1.0-beta-1.0
		 *
		 * @param boolean    $disable_choices  `true` to disable choices.
		 * @param GF_Field   $field            The current field.
		 * @param array      $form             The current form.
		 */
		$disable_choices = gf_apply_filters( array( 'gpi_disable_choices', $form['id'], $field->id ), ! $remove_choices, $field, $form );

		foreach ( $choices as $choice ) {

			$limit    = $this->get_choice_inventory_limit( $choice, $field, $form );
			$no_limit = rgblank( $limit );
			$limit    = intval( $limit );

			if ( $no_limit ) {
				$filtered_choices[] = $choice;
				continue;
			}

			// if choice count is greater than or equal to choice limit, limit has been exceeded
			$value          = $field->sanitize_entry_value( $choice['value'], $form['id'] );
			$choice_count   = intval( rgar( $choice_counts, $value ) );
			$exceeded_limit = $choice_count >= $limit;

			if ( $this->is_edit_view( $form ) && in_array( $value, $this->get_selected_values( $field ), true ) ) {
				$exceeded_limit = false;
			}

			// add $choice to $disabled_choices, will be used to disable choice via JS
			if ( $exceeded_limit && $disable_choices ) {
				$choice['is_disabled'] = true;
				$choice['isDisabled']  = true;
				$choice['isSelected']  = false;
			}

			if ( rgar( $field, 'gpiShowAvailableInventory', false ) ) {
				$how_many_left = max( $limit - $choice_count, 0 );

				$message = $this->get_inventory_available_message( $field );
				$message = $this->replace_choice_available_inventory_merge_tags( $message, $field, $form, $choice, $how_many_left );

				$choice['text'] = $choice['text'] . " $message";
			}

			/**
			 * Filter to modify choices (includes whether the choice has exceeded its limit).
			 *
			 * @since 1.0-beta-1.0
			 *
			 * @param array     $choice           The current choice.
			 * @param boolean   $exceeded_limit   Whether the current choice has exceeded its inventory limit.
			 * @param GF_Field  $field            The current field.
			 * @param array     $form             The current form.
			 * @param array     $choice_count     Counts of claimed inventory for each choice.
			 */
			// provide custom opportunity to modify choices (includes whether the choice has exceeded limit)
			$choice = gf_apply_filters( array( 'gpi_pre_render_choice', $form['id'], $field->id ), $choice, $exceeded_limit, $field, $form, $choice_count );

			if ( ! $exceeded_limit || ! $remove_choices ) {
				$filtered_choices[] = $choice;
			}
		}

		$all_disabled = true;

		foreach ( $filtered_choices as $choice ) {
			if ( ! rgar( $choice, 'isDisabled' ) ) {
				$all_disabled = false;
				break;
			}
		}

		if ( empty( $filtered_choices ) || $all_disabled ) {
			$this->choiceless[ $form['id'] ][] = $field['id'];
		}

		return $filtered_choices;
	}

	public function replace_choice_available_inventory_merge_tags( $message, $field, $form, $choice, $available = null ) {

		$message = str_replace( '{limit}', number_format_i18n( $this->get_choice_inventory_limit( $choice, $field, $form ) ), $message );
		$message = str_replace( '{claimed}', number_format_i18n( $this->get_choice_count( $choice['value'], $field, $form['id'] ) ), $message );
		$message = $this->replace_available_inventory_merge_tags( $message, $field, $available );

		return $message;
	}

	public function get_selected_values( $field ) {
		// On the Gravity Flow detail page, we want to get the values from the $entry rather than the $_POST.
		if ( $this->is_gflow_edit_view() ) {
			$entry  = GFAPI::get_entry( rgget( 'lid' ) );
			$values = GFFormsModel::get_lead_field_value( $entry, $field );
		} elseif ( $this->is_gview_edit_view() ) {
			$entry  = $this->get_gview_entry();
			$values = GFFormsModel::get_lead_field_value( $entry, $field );
		} else {
			/**
			 * Filter the selected values for the current choice-based field.
			 *
			 * @since 1.0-beta-1.0
			 *
			 * @param array     $selected_values Selected values for the current field.
			 * @param GF_Field  $field           The current field.
			 */
			$values = gf_apply_filters( array( 'gpi_selected_values', $field->formId, $field->id ), GFFormsModel::get_field_value( $field ), $field );
		}

		if ( ! is_array( $values ) ) {
			$values = array( $values );
		}

		$values = array_filter( $values, array( $this, 'not_blank' ) );

		if ( $this->is_pricing_field( $field ) ) {
			foreach ( $values as &$value ) {
				$value = $this->remove_price( $value );
			}
		}

		return $values;
	}

	// Allow field values to include a vertical pipe "|" character
	// Example real-world value: 20-24lbs | $10
	public function remove_price( $value ) {
		if ( strlen( $value ) < 1 ) {
			return $value;
		}

		$value = explode( '|', $value );

		switch ( sizeof( $value ) ) {
			case 1:
				return $value;
			default:
				array_pop( $value );

				return join( '|', $value );
		}
	}

	public function is_pricing_field( $field ) {
		return GFCommon::is_pricing_field( $field['type'] );
	}

	public function has_disabled_choice( $form ) {
		foreach ( $form['fields'] as $field ) {
			foreach ( (array) $field->choices as $choice ) {
				if ( rgar( $choice, 'isDisabled' ) ) {
					return true;
				}
			}
		}
		return false;
	}

	public function get_choice_count( $value, $field, $form_id ) {
		$counts = $this->get_choice_counts( $form_id, $field );

		if ( self::is_pricing_field( $field ) ) {
			$value = rgar( explode( '|', $value ), 0 );
			$value = wp_kses( rgar( explode( '|', $value ), 0 ), wp_kses_allowed_html( 'post' ) );
		} else {
			$value = $field->sanitize_entry_value( $value, $form_id );
		}

		return intval( rgar( $counts, $value ) );
	}

	public function add_query_hooks( $field ) {
		parent::add_query_hooks( $field );

		add_filter( 'gpi_query', array( $this, 'modify_query_select_for_choices' ), 10, 2 );
		add_filter( 'gpi_query', array( $this, 'shared_resource_quantity' ), 10, 2 );
		add_filter( 'gpi_query', array( $this, 'use_like_where_clause_for_checkboxes' ), 15, 2 );
		add_filter( 'gpi_query', array( $this, 'use_meta_values_as_quantity_for_choices' ), 5, 2 );
	}

	/**
	 * Change select to include all of the columns that we need.
	 *
	 * @param array $query
	 * @param GF_Field $field Field that is having its inventory controlled.
	 *
	 * @return mixed
	 */
	public function modify_query_select_for_choices( $query, $field ) {
		if ( ! empty( $this->get_quantity_input_ids( $field ) ) ) {
			$query['select'] = 'SELECT SUM(em_quantity.meta_value) as quantity, em.meta_value';
		} else {
			$query['select'] = 'SELECT COUNT(em.meta_value) as quantity, em.meta_value';
		}

		$query['group_by'] = 'GROUP BY em.meta_value';

		return $query;
	}

	/**
	 * Update JOIN for quantity to include quantity fields across forms
	 *
	 * @param $query array
	 * @param $field GF_Field
	 *
	 * @return array
	 */
	public function shared_resource_quantity( $query, $field ) {
		global $wpdb;

		if ( rgar( $field, 'gpiInventory' ) !== GP_Inventory_Type_Advanced::$type ) {
			return $query;
		}

		$resource_id     = rgar( $field, 'gpiResource' );
		$resource_fields = $this->get_resource_fields( $resource_id );

		if ( count( $resource_fields ) < 2 ) {
			return $query;
		}

		if ( ! empty( gp_inventory_resources()->get_resource_properties( $resource_id ) ) ) {
			return $query;
		}

		if ( ! preg_match( '/AND em_quantity\.meta_key/', $query['join'] ) ) {
			return $query;
		}

		$quantity_joins = array();

		foreach ( $resource_fields as $resource_field ) {
			$quantity_input_ids = $this->get_quantity_input_ids( $resource_field );

			if ( empty( $quantity_input_ids ) ) {
				continue;
			}

			$quantity_inputs_array = implode( ', ', array_map( 'esc_sql', $quantity_input_ids ) );

			$quantity_joins[] = $wpdb->prepare( "(em_quantity.form_id = %d AND em_quantity.meta_key IN ( {$quantity_inputs_array} ) AND em.meta_key = %s)", $resource_field->formId, $resource_field->id );
		}

		if ( empty( $quantity_joins ) ) {
			return $query;
		}

		$quantity_joins = ' AND (' . implode( ' OR ', $quantity_joins ) . ')';

		$query['join'] = preg_replace( '/AND em_quantity\.meta_key IN \( .*? \)/', $quantity_joins, $query['join'] );
		$query['join'] = preg_replace( '/AND em_quantity\.meta_key = \'\d+\'/', $quantity_joins, $query['join'] );

		return $query;
	}

	/**
	 * Checkboxes save each input individually in the meta table. Add in a LIKE clause to properly query for
	 * all inputs that match the field.
	 *
	 * @param $query array
	 * @param $field GF_Field
	 *
	 * @return array
	 */
	public function use_like_where_clause_for_checkboxes( $query, $field ) {
		global $wpdb;

		if ( $field->get_input_type() === 'checkbox' ) {
			$query['where'] = str_replace(
				"em.meta_key = '{$field->id}'",
				$wpdb->prepare( '(SUBSTRING_INDEX( em.meta_key, \'.\', 1 ) = %s)', $field->id ),
				$query['where']
			);
		}

		return $query;
	}

	/**
	 * For choice-based product fields, we need to query for the specific meta key for the quantity rather than
	 * simply counting/summing the submitted values like we can with non-product choice-based fields.
	 *
	 * @param $query array
	 * @param $field GF_Field
	 *
	 * @return array
	 */
	public function use_meta_values_as_quantity_for_choices( $query, $field ) {
		global $wpdb;

		$quantity_input_ids = $this->get_quantity_input_ids( $field );

		if ( ! empty( $quantity_input_ids ) && $this->is_applicable_field( $field ) ) {
			$query['select'] = str_replace( 'sum( em.meta_value ) as quantity', 'em_quantity.meta_value as quantity', $query['select'] );

			$quantity_inputs_array = implode( ', ', array_map( 'esc_sql', $quantity_input_ids ) );
			$query['join']  .= " \nINNER JOIN {$wpdb->prefix}gf_entry_meta em_quantity ON em_quantity.entry_id = e.id AND em_quantity.meta_key IN ( {$quantity_inputs_array} )";

			// Switch where from the quantity field and back the field with inventory
			$query['where'] = preg_replace( '/AND \(em\.meta_key = \'(.*?)\'\)/', $wpdb->prepare( 'AND (em.meta_key = %s)', $field->id ), $query['where'] );
			$query['where'] = preg_replace( '/AND em\.meta_key IN\( .*? \)/', $wpdb->prepare( 'AND (em.meta_key = %s)', $field->id ), $query['where'] );
		}

		return $query;
	}

	public function remove_query_hooks() {
		parent::remove_query_hooks();

		remove_filter( 'gpi_query', array( $this, 'modify_query_select_for_choices' ) );
		remove_filter( 'gpi_query', array( $this, 'shared_resource_quantity' ) );
		remove_filter( 'gpi_query', array( $this, 'use_like_where_clause_for_checkboxes' ), 5 );
		remove_filter( 'gpi_query', array( $this, 'use_meta_values_as_quantity_for_choices' ), 5 );
	}

	public function get_choice_counts( $form_id, $field ) {
		global $wpdb;

		if ( is_integer( $field ) ) {
			$form  = GFFormsModel::get_form_meta( $form_id );
			$field = GFFormsModel::get_field( $form, $field );
		}


		$this->add_query_hooks( $field );
		$query = $this->get_claimed_inventory_query( $field );
		$this->remove_query_hooks();

		$sql = implode( ' ', $query );

		/* Cache results based on the query generated. The SQL can be different even with the same field due to
			all the filters that may be influencing it such as the inventory shortcode. */
		$cache_key = sprintf( 'gpi_choice_counts_%d_%d_%d', $form_id, $field['id'], sha1( $sql ) );
		$result    = GFCache::get( $cache_key );

		if ( $result !== false ) {
			return $result;
		}

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$results = $wpdb->get_results( $sql, ARRAY_A );

		$counts = array();

		foreach ( $results as $choice ) {
			if ( GFFormsModel::get_input_type( $field ) === 'multiselect' ) {
				// New versions of GF store multiselect values as a JSON string.
				$values = json_decode( $choice['meta_value'] );

				// Older versions of Gravity Forms store the multiselect values as a comma-delimited list.
				if ( ! $values ) {
					$values = explode( ',', $choice['meta_value'] );
				}

				if ( ! is_array( $values ) ) {
					continue;
				}

				$quantity = rgar( $choice, 'quantity', 1 );

				foreach ( $values as $value ) {
					$value            = $field->sanitize_entry_value( $value, $form_id );
					$counts[ $value ] = isset( $counts[ $value ] ) ? $counts[ $value ] + $quantity : $quantity;
				}
			} else {
				$value            = $field->sanitize_entry_value( rgar( explode( '|', $choice['meta_value'] ), 0 ), $form_id );
				$quantity         = rgar( $choice, 'quantity', 1 );
				$counts[ $value ] = isset( $counts[ $value ] ) ? $counts[ $value ] + $quantity : $quantity;
			}
		}

		/**
		 * Filter the number of claimed choice counts for the given field.
		 *
		 * @param array    $counts   Associative array of the choice counts (i.e. array( 'First Choice' => 1 )).
		 * @param int      $form_id  Current form ID.
		 * @param GF_Field $field    Current field object.
		 */
		$counts = gf_apply_filters( array( 'gpi_choice_counts', $form_id, $field->id ), $counts, $form_id, $field );

		GFCache::set( $cache_key, $counts );

		return $counts;
	}

	/**
	 * Get the inventory limit for the given choice.
	 *
	 * @param array     $choice     The current choice.
	 * @param GF_Field  $field      Current field.
	 * @param array     $form       Current form.
	 */
	public function get_choice_inventory_limit( $choice, $field, $form ) {
		/**
		 * Filter the inventory limit for choices.
		 *
		 * @param int       $inventory_limit Inventory limit of the current choice.
		 * @param array     $choice          The current choice.
		 * @param GF_Field  $field           The current field.
		 * @param array     $form            Current form.
		 *
		 * @since 1.0-beta-1.5
		 */
		return gf_apply_filters( array( 'gpi_choice_inventory_limit', $field->formId, $field->id ), rgar( $choice, 'inventory_limit' ), $choice, $field, $form );
	}

	/**
	 * Prevent synchronous submisssion which would exceed limit.
	 *
	 * @param mixed $validation_result
	 */
	public function validation( $validation_result ) {

		$form                 = $validation_result['form'];
		$has_validation_error = false;

		foreach ( $form['fields'] as &$field ) {

			if ( ! $this->should_validate_field( $field, $form ) ) {
				continue;
			}

			$choices = $this->get_selected_choices( $field );
			if ( empty( $choices ) ) {
				continue;
			}

			// confirm whether choices are removed and/or disabled for valdiation purposes
			$remove_choices  = gf_apply_filters( array( 'gpi_remove_choices', $form['id'], $field['id'] ), true, $form['id'], $field['id'] );
			$disable_choices = gf_apply_filters( array( 'gpi_disable_choices', $form['id'], $field['id'] ), ! $remove_choices, $form['id'], $field['id'] );

			// if choices are not disabled, bypass validation
			if ( ! $remove_choices && ! $disable_choices ) {
				continue;
			}

			$existing_count = 0;
			if ( $this->is_gview_edit_view() ) {
				$entry          = $this->get_gview_entry();
				$existing_count = $this->get_requested_quantity( $field, $entry );
			}

			$validation_messages = array();

			foreach ( $choices as $choice ) {

				$limit = $this->get_choice_inventory_limit( $choice, $field, $form );
				if ( rgblank( $limit ) ) {
					continue;
				}

				$limit = intval( $limit );
				$count = $this->get_choice_count( $choice['value'], $field, $form['id'] ) - $existing_count;

				$requested_count = null;

				if ( gp_inventory_type_advanced()->is_applicable_field( $field, true ) ) {
					$resource_id     = rgar( $field, 'gpiResource' );
					$resource_fields = $this->get_resource_fields( $resource_id );

					if ( count( $resource_fields ) > 1 ) {
						$requested_count = array();

						foreach ( $resource_fields as $resource_field ) {
							$value = GFFormsModel::get_field_value( $resource_field );

							if ( is_array( $value ) ) {
								$values = array_values( $value );
							} else {
								$values = array( $value );
							}

							foreach ( $values as $value ) {
								if ( ! $value ) {
									continue;
								}

								if ( ! isset( $requested_count[ $value ] ) ) {
									$requested_count[ $value ] = 0;
								}

								$requested_count[ $value ] += $this->get_requested_quantity( $resource_field );
							}
						}
					}
				}

				if ( $requested_count === null ) {
					$requested_count = $this->get_requested_quantity( $field );
				} elseif ( is_array( $requested_count ) ) {
					$value = GFFormsModel::get_field_value( $field );

					if ( is_array( $value ) && $field->get_input_type() === 'checkbox' ) {
						$values = array_values( $value );

						$choice_value = ! empty( $choice['value'] ) || $field->enableChoiceValue ? $choice['value'] : $choice['text'];

						if ( $field->enablePrice ) {
							$price         = rgempty( 'price', $choice ) ? 0 : GFCommon::to_number( rgar( $choice, 'price' ) );
							$choice_value .= '|' . $price;
						}

						if ( in_array( $choice_value, $values, true ) ) {
							$requested_count = rgar( $requested_count, $choice_value, 0 );
						}
					} elseif ( $value ) {
						$requested_count = rgar( $requested_count, $value, 0 );
					}
				}

				if ( $requested_count === null ) {
					$requested_count = 0;
				}

				$out_of_stock     = $limit <= $count;
				$not_enough_stock = $limit < $count + $requested_count;
				$available_count  = $limit - $count;

				if ( ! ( $out_of_stock && $requested_count > 0 ) && ! $not_enough_stock && (int) $limit !== 0 ) {
					continue;
				}

				// passed to the label hooks
				$inventory_data = array(
					'limit'     => $limit,
					'count'     => $count,
					'requested' => $requested_count,
					'available' => $available_count,
				);

				if ( $out_of_stock ) {

					// translators: placeholder is selected choice label
					$out_of_stock_message = __( 'The choice, "%s", which you have selected is no longer available.', 'gp-limit-choices' );
					/**
					 * Filter validation message when the item is out of stock.
					 *
					 * @since 1.0-beta-1.0
					 *
					 * @param string    $out_of_stock_message Validation message.
					 * @param array     $form                 The current form.
					 * @param GF_Field  $field                The current field.
					 * @param array     $inventory_data       Includes the limit, count, requested count and available count.
					 */
					$out_of_stock_message = gf_apply_filters( array( 'gpi_out_of_stock_message', $form['id'], $field->id ), $out_of_stock_message, $form, $field, $inventory_data );

					$message = sprintf( $out_of_stock_message, $choice['text'] );
					$message = GFCommon::replace_variables( $message, $form, GFFormsModel::get_current_lead() );

				} elseif ( $not_enough_stock ) {

					$inventory_insufficient_message = $this->get_inventory_insufficient_message( $field );

					$inventory_insufficient_message = str_replace( '{requested}', number_format_i18n( $requested_count ), $inventory_insufficient_message );
					$inventory_insufficient_message = str_replace( '{available}', number_format_i18n( $available_count ), $inventory_insufficient_message );
					$inventory_insufficient_message = str_replace( '{limit}', number_format_i18n( $this->get_stock_quantity( $field ) ), $inventory_insufficient_message );

					$message = GFCommon::replace_variables( $inventory_insufficient_message, $form, GFFormsModel::get_current_lead() );

				}

				$validation_messages[] = $message;

			}

			if ( ! empty( $validation_messages ) ) {
				$has_validation_error        = true;
				$field['failed_validation']  = true;
				$field['validation_message'] = implode( '<br />', $validation_messages );
			}
		}

		$validation_result['form']     = $form;
		$validation_result['is_valid'] = $validation_result['is_valid'] && ! $has_validation_error;

		return $validation_result;
	}

	public function should_validate_field( $field, $form ) {
		$page_number  = GFFormDisplay::get_source_page( $field->formId );
		$is_last_page = (string) GFFormDisplay::get_target_page( $form, $page_number, array() ) === '0';
		return $this->is_applicable_field( $field ) && ( $is_last_page || $page_number === $field->pageNumber );
	}

	public function get_selected_choices( $field, $values = false ) {
		if ( ! $values ) {
			$values = $this->get_selected_values( $field );
		} elseif ( ! is_array( $values ) ) {
			$values = array( $values );
		}

		$choices = array();

		foreach ( $field['choices'] as $choice ) {
			if ( in_array( $choice['value'], $values, true ) ) {
				$choices[] = $choice;
			}
		}

		return $choices;
	}

	// If in an edit view, we don't want to disable the selected choice, providing the user the ability to select another choice - or - reselect the exhausted choice.
	public function is_edit_view( $form ) {
		/**
		 * Filter if the current page should be treated as an "Edit View" to bypass Inventory Limits and
		 * allow [re-]selection of exhausted choices.
		 *
		 * @since 1.0-beta-1.0
		 *
		 * @param boolean  $is_edit_view  `true` if current page is an "Edit View"
		 * @param array    $form           The current form array the fields belong to.
		 */
		return gf_apply_filters( array( 'gpi_is_edit_view', $form['id'] ), $this->is_gflow_edit_view() || $this->is_gview_edit_view(), $form );
	}

	public function is_gflow_edit_view() {
		return is_callable( 'gravity_flow' ) && gravity_flow()->is_workflow_detail_page();

	}

	public function is_gview_edit_view() {
		return is_callable( 'gravityview_get_context' ) && gravityview_get_context() === 'edit';
	}

	public function get_gview_entry() {
		$gravityview_view = GravityView_View::getInstance();
		$entries          = $gravityview_view->getEntries();
		$entry            = reset( $entries );
		return $entry;
	}

	public function not_blank( $value ) {
		return ! rgblank( $value );
	}

	public function flush_choice_count_cache_post_entry_creation( $entry, $form ) {
		$this->flush_choice_count_cache( $form );
	}

	public function flush_choice_count_cache( $form ) {
		foreach ( $form['fields'] as $field ) {
			$cache_key = sprintf( 'gpi_choice_counts_%d_%d', $form['id'], $field['id'] );
			GFCache::delete( $cache_key );
		}
	}

	/**
	 * @param $field GF_Field
	 *
	 * @return string
	 */
	public function get_inventory_available_message( $field ) {
		return rgar( $field, 'gpiMessageInventoryAvailable', gp_inventory()->inventory_available_on_choice_default_message() );
	}

}

function gp_inventory_type_choices() {
	return GP_Inventory_Type_Choices::get_instance();
}
