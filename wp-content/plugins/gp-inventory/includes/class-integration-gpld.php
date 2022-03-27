<?php

class GP_Inventory_Integration_GPLD {

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct() {
		add_filter( 'gpld_limit_dates_options', array( $this, 'limit_date_fields' ), 10, 2 );
	}

	/**
	 * Check that the date field is a property in the inventory field's property map.
	 *
	 * @param GF_Field $inventory_field
	 * @param GF_Field $date_field
	 */
	public function is_applicable_date_property( $inventory_field, $date_field ) {
		$properties_input_ids = array_map( 'intval', array_values( rgar( $inventory_field, 'gpiResourcePropertyMap' ) ) );

		if ( ! $date_field || ! $inventory_field ) {
			return false;
		}

		if ( empty( $properties_input_ids ) ) {
			return false;
		}

		if ( ! in_array( $date_field->id, $properties_input_ids, true ) ) {
			return false;
		}

		if ( $date_field->get_input_type() !== 'date' || $date_field->dateType !== 'datepicker' ) {
			return false;
		}

		return true;
	}

	/**
	 * @param $options
	 * @param $form
	 *
	 * @return array|mixed
	 */
	public function limit_date_fields( $options, $form ) {
		global $wpdb;

		if ( ! gp_inventory_type_advanced()->is_applicable_form( $form, true ) ) {
			return $options;
		}

		$inventory_fields = gp_inventory_type_advanced()->get_applicable_fields( $form, true );

		foreach ( $options as $date_field_id => $date_field_options ) {
			$date_field = GFAPI::get_field( $form, $date_field_id );

			if ( ! $date_field || $date_field->get_input_type() !== 'date' || $date_field->dateType !== 'datepicker' ) {
				return $options;
			}

			/**
			 * Create catalog of available choices for all inventory fields relying on the date field.
			 */
			$date_field_choices_inv_limits       = array();
			$date_field_choice_avail_inv_by_date = array();

			/*
			 * Keep track of the inventory fields associated with the Date field so we know if all are exhausted on the specific date.
			 */
			$inventory_fields_without_inventory          = array();
			$inventory_fields_associated_with_date_field = array();

			foreach ( $inventory_fields as $inventory_field ) {
				if ( ! $this->is_applicable_date_property( $inventory_field, $date_field ) ) {
					continue;
				}

				$inventory_fields_associated_with_date_field[] = $inventory_field->id;

				if ( empty( $inventory_field['choices'] ) ) {
					continue;
				}

				foreach ( $inventory_field['choices'] as $choice ) {
					$date_field_choices_inv_limits[ $inventory_field->id . '|' . $choice['value'] ] = rgar( $choice, 'inventory_limit', 1 );
				}
			}

			foreach ( $inventory_fields as $inventory_field ) {
				if ( ! $this->is_applicable_date_property( $inventory_field, $date_field ) ) {
					continue;
				}

				$properties_input_ids = array_map( 'intval', array_values( rgar( $inventory_field, 'gpiResourcePropertyMap' ) ) );

				// add our Date field to the front of the array so we can reliably target it when replacing the queries below
				array_unshift( $properties_input_ids, $date_field->id );
				$properties_input_ids = array_unique( $properties_input_ids );

				if ( gp_inventory_type_choices()->is_applicable_field( $inventory_field ) ) {
					gp_inventory_type_choices()->add_query_hooks( $inventory_field );
					$query = gp_inventory_type_choices()->get_claimed_inventory_query( $inventory_field );
					gp_inventory_type_choices()->remove_query_hooks();
				} else {
					gp_inventory_type_advanced()->add_query_hooks( $inventory_field );
					$query = gp_inventory_type_advanced()->get_claimed_inventory_query( $inventory_field );
					gp_inventory_type_advanced()->remove_query_hooks();
				}

				// Remove meta_value from where's as we want to get all of the meta values to figure out which dates
				// are exhausted.
				$regex = '/ AND [a-z0-9_]+\.meta_value = \'(?:.*?)\'/';

				preg_match_all( $regex, $query['where'], $matches, PREG_SET_ORDER );
				foreach ( $matches as $match ) {
					list( $search ) = $match;
					$query['where'] = str_replace( $search, '', $query['where'] );
				}

				// Get all JOINs on entry meta for properties to coalesce them into the date column
				$regex_join_props = "/JOIN {$wpdb->prefix}gf_entry_meta (p_[a-z0-9]+)/";
				$date_joins       = array();

				preg_match_all( $regex_join_props, $query['join'], $join_props_matches, PREG_SET_ORDER );
				foreach ( $join_props_matches as $join_prop_match ) {
					list ( , $alias ) = $join_prop_match;
					$date_joins[]     = $alias . '.meta_value';
				}

				$query['select'] .= ', COALESCE(' . join( ', ', $date_joins ) . ') as date';

				if ( gp_inventory_type_choices()->is_applicable_field( $inventory_field ) ) {
					$query['group_by'] = 'GROUP BY date, em.meta_value';
				} else {
					$query['group_by'] = 'GROUP BY date';
				}

				/*
				 * The quantity can be different for individual choices. It might be worth eventually adding a HAVING
				 * group such as HAVING (quantity >= CHOICE_QUANTITY AND em.meta_value = "Choice Value), but this will
				 * primarily be for performance as we still need to verify that every choice is exhausted below in the
				 * $results foreach.
				 */
				if ( ! gp_inventory_type_choices()->is_applicable_field( $inventory_field ) ) {
					$stock_quantity  = gp_inventory_type_advanced()->get_stock_quantity( $inventory_field );
					$query['having'] = sprintf( 'HAVING quantity >= %d', $stock_quantity );
				}

				$sql = implode( "\n", $query );

				// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				$results = $wpdb->get_results( $sql );

				foreach ( $results as $result ) {
					if ( ! isset( $inventory_fields_without_inventory[ $result->date ] ) ) {
						$inventory_fields_without_inventory[ $result->date ] = array();
					}

					// For choice-based fields, we need to make sure that every choice has its inventory consumed.
					if ( gp_inventory_type_choices()->is_applicable_field( $inventory_field ) ) {
						if ( ! isset( $date_field_choice_avail_inv_by_date[ $result->date ] ) ) {
							$date_field_choice_avail_inv_by_date[ $result->date ] = $date_field_choices_inv_limits;
						}

						/* The meta value of a product field will include the price so that needs to be excluded when matching against choice values. */
						if ( GFCommon::is_product_field( $inventory_field->type ) ) {
							$exploded_meta_value = explode( '|', $result->meta_value );
							$meta_value          = $exploded_meta_value[0];
						} else {
							$meta_value = $result->meta_value;
						}

						$choice_key = $inventory_field->id . '|' . $meta_value;

						if ( isset( $date_field_choice_avail_inv_by_date[ $result->date ][ $choice_key ] ) ) {
							$date_field_choice_avail_inv_by_date[ $result->date ][ $choice_key ] -= $result->quantity;

							if ( $date_field_choice_avail_inv_by_date[ $result->date ][ $choice_key ] <= 0 ) {
								unset( $date_field_choice_avail_inv_by_date[ $result->date ][ $choice_key ] );
							}
						}

						$inventory_field_has_choices_on_current_date = false;

						foreach ( $date_field_choice_avail_inv_by_date[ $result->date ] as $choice_key => $quantity ) {
							$choice_key_split = explode( '|', $choice_key );

							if ( (int) $inventory_field->id === (int) $choice_key_split[0] ) {
								$inventory_field_has_choices_on_current_date = true;
								break;
							}
						}

						if ( $inventory_field_has_choices_on_current_date ) {
							continue;
						}
					}

					$inventory_fields_without_inventory[ $result->date ][] = $inventory_field->id;
				}
			}

			foreach ( $inventory_fields_without_inventory as $date => $fields_without_inventory ) {
				if ( count( array_unique( $fields_without_inventory ) ) !== count( array_unique( $inventory_fields_associated_with_date_field ) ) ) {
					continue;
				}

				$options[ $date_field->id ]['exceptionsMode'] = 'disable';

				if ( ! is_array( $options[ $date_field->id ]['exceptions'] ) ) {
					$options[ $date_field->id ]['exceptions'] = array();
				}

				$options[ $date_field->id ]['exceptions'][] = gmdate( 'm/d/Y', strtotime( $date ) );
			}
		}

		return $options;
	}

}

function gp_inventory_integration_gpld() {
	return GP_Inventory_Integration_GPLD::get_instance();
}
