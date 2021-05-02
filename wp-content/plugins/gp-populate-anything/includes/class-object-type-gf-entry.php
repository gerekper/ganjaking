<?php

class GPPA_Object_Type_GF_Entry extends GPPA_Object_Type {

	private static $excluded_fields = array(
		'list',
	);

	public function __construct( $id ) {
		parent::__construct( $id );

		add_action( 'gppa_pre_object_type_query_gf_entry', array( $this, 'add_filter_hooks' ) );

		add_filter( 'gppa_process_template', array( $this, 'replace_gf_merge_tags_for_entry' ), 15, 6 );
		add_filter( 'gppa_process_template', array( $this, 'maybe_combine_multi_input_entry_template' ), 5, 7 );
	}

	public function add_filter_hooks() {
		add_filter( 'gppa_object_type_gf_entry_filter', array( $this, 'process_filter_default' ), 10, 4 );
	}

	/**
	 * Extract unique identifier for a given GF Entry.
	 *
	 * @param $object
	 * @param  null  $primary_property_value
	 *
	 * @return string|number
	 */
	public function get_object_id( $object, $primary_property_value = null ) {
		return isset( $object->id ) ? $object->id : null;
	}

	public function get_label() {
		return esc_html__( 'Gravity Forms Entry', 'gp-populate-anything' );
	}

	public function get_groups() {
		return array(
			'fields' => array(
				'label' => esc_html__( 'Fields', 'gp-populate-anything' ),
			),
			'meta'   => array(
				'label' => esc_html__( 'Entry Meta', 'gp-populate-anything' ),
			),
		);
	}

	public function get_primary_property() {
		return array(
			'id'       => 'form',
			'label'    => esc_html__( 'Form', 'gp-populate-anything' ),
			'callable' => array( $this, 'get_forms' ),
		);
	}

	public function get_properties( $form_id = null ) {

		if ( ! $form_id ) {
			return array();
		}

		$properties = array(
			'id'           => array(
				'label'    => 'Entry ID',
				'value'    => 'id',
				'callable' => array( $this, 'get_col_rows' ),
				'args'     => array( GFFormsModel::get_entry_table_name(), 'id' ),
				'orderby'  => true,
			),
			'created_by'   => array(
				'label'    => 'Created by (User ID)',
				'value'    => 'created_by',
				'callable' => '__return_empty_array',
				'orderby'  => true,
			),
			'date_created' => array(
				'label'    => 'Date Created',
				'value'    => 'date_created',
				'callable' => '__return_empty_array',
				'orderby'  => true,
			),
			'date_updated' => array(
				'label'    => 'Date Updated',
				'value'    => 'date_updated',
				'callable' => '__return_empty_array',
				'orderby'  => true,
			),
			'status'       => array(
				'label'    => 'Status',
				'value'    => 'status',
				'callable' => array( $this, 'get_col_rows' ),
				'args'     => array( GFFormsModel::get_entry_table_name(), 'status' ),
				'orderby'  => true,
			),
		);

		foreach ( $this->get_form_fields( $form_id ) as $form_field ) {
			$properties[ 'gf_field_' . $form_field['value'] ] = array(
				'value'    => $form_field['value'],
				'group'    => 'fields',
				'label'    => $form_field['label'],
				'callable' => array( $this, 'get_form_fields_values' ),
				'args'     => array( $form_id, $form_field['value'] ),
				'orderby'  => true,
			);
		}

		foreach ( GFFormsModel::get_entry_meta( $form_id ) as $meta_key => $meta ) {
			$properties[ 'gf_field_' . $meta_key ] = array(
				'value'    => $meta_key,
				'group'    => 'meta',
				'label'    => $meta['label'],
				'callable' => array( $this, 'get_form_fields_values' ),
				'args'     => array( $form_id, $meta_key ),
				'orderby'  => true,
			);
		}

		return $properties;

	}

	public function get_object_prop_value( $object, $prop ) {

		$prop = preg_replace( '/^gf_field_/', '', $prop );

		if ( ! isset( $object->{$prop} ) ) {
			return null;
		}

		return $object->{$prop};

	}

	public function date_to_time( $date, $format = 'mdy' ) {

		$delimiter = '/';

		if ( strpos( $date, '-' ) !== false ) {
			$delimiter = '-';
		} elseif ( strpos( $date, '.' ) !== false ) {
			$delimiter = '.';
		}

		/**
		 * Check for the delimiter prior to converting below as PHP notices will result if the delimiter is not present.
		 */
		if ( strpos( $date, $delimiter ) === false ) {
			return null;
		}

		if ( strpos( $format, 'ymd' ) === 0 ) {
			list( $year, $month, $day ) = explode( $delimiter, $date );
		} elseif ( strpos( $format, 'dmy' ) === 0 ) {
			list( $day, $month, $year ) = explode( $delimiter, $date );
		} elseif ( strpos( $format, 'mdy' ) === 0 ) {
			list( $month, $day, $year ) = explode( $delimiter, $date );
		}

		// Convert m/d/y to integer values
		$month = intval( $month );
		$day   = intval( $day );
		$year  = intval( $year );

		// Esnure m/d/y are available
		if ( ! $month || ! $day || ! $year ) {
			return null;
		}

		return mktime( 0, 0, 0, $month, $day, $year );

	}

	public function process_filter_default( $gf_query_where, $args ) {

		/**
		 * @var $filter_value
		 * @var $filter
		 * @var $filter_group
		 * @var $filter_group_index
		 * @var $primary_property_value
		 * @var $property
		 * @var $property_id
		 */
		extract( $args );

		if ( ! isset( $gf_query_where[ $filter_group_index ] ) ) {
			$gf_query_where[ $filter_group_index ] = array();
		}

		switch ( strtoupper( $filter['operator'] ) ) {
			case 'CONTAINS':
				$operator     = GF_Query_Condition::LIKE;
				$filter_value = $this->get_sql_value( $filter['operator'], $filter_value );
				break;
			case 'STARTS_WITH':
				$operator     = GF_Query_Condition::LIKE;
				$filter_value = $this->get_sql_value( $filter['operator'], $filter_value );
				break;
			case 'ENDS_WITH':
				$operator     = GF_Query_Condition::LIKE;
				$filter_value = $this->get_sql_value( $filter['operator'], $filter_value );
				break;
			case 'IS NOT':
			case 'ISNOT':
			case '<>':
				$operator = GF_Query_Condition::NEQ;
				break;
			case 'LIKE':
				$operator = GF_Query_Condition::LIKE;
				break;
			case 'NOT IN':
				$operator = GF_Query_Condition::NIN;
				break;
			case 'IN':
				$operator = GF_Query_Condition::IN;
				break;
			case '>=':
				$operator = GF_Query_Condition::GTE;
				break;
			case '<=':
				$operator = GF_Query_Condition::LTE;
				break;
			case '<':
				$operator = GF_Query_Condition::LT;
				break;
			case '>':
				$operator = GF_Query_Condition::GT;
				break;
			case 'IS':
			case '=':
				$operator = GF_Query_Condition::EQ;
				// Implemented to support Checkbox fields as a Form Field Value filters.
				if ( is_array( $filter_value ) ) {
					$operator = GF_Query_Condition::IN;
				}
				break;
			default:
				return $gf_query_where;
		}

		if ( is_array( $filter_value ) ) {
			foreach ( $filter_value as &$_filter_value ) {
				$_filter_value = new GF_Query_Literal( $_filter_value );
			}
			unset( $_filter_value );
			$filter_value = new GF_Query_Series( $filter_value );
		} else {
			/**
			 * Get current source field to parse the query value appropriately
			 */
			$form_id      = $primary_property_value;
			$field_id     = str_replace( 'gf_field_', '', rgar( $args, 'property_id' ) );
			$source_field = GFAPI::get_field( $form_id, absint( $field_id ) );
			$is_field     = is_a( $source_field, 'GF_Field' );

			if ( $is_field && $source_field->type === 'number' ) {
				$filter_value = floatval( $filter_value );
			}
			/**
			 * Force a value to be parsed as a date to enable date comparison using operators such as >, <, <=, etc.
			 *
			 * By default, values from date fields will be treated as dates. Using this filter, non-date fields can have
			 * their values parsed as dates.
			 *
			 * @since 1.0-beta-4.89
			 *
			 * @param boolean $value Whether or not to parse the value as a date.
			 * @param \GF_Field $field The field that is having its value parsed.
			 */
			$gppa_process_value_as_date = gf_apply_filters( array_filter( array( 'gppa_process_value_as_date', $form_id, $is_field ? $source_field->id : null ) ), $is_field && $source_field->type === 'date', $source_field );

			/**
			 * Convert date string to ISO 8601 for MySQL date comparisons
			 *
			 * strtotime doesn't play nicely with formats like d/m/y out of the box so we need to parse the date
			 * ourselves into a time based on the format from the actual date field saved in the form that we're
			 * pulling entries from.
			 */
			if ( $gppa_process_value_as_date && strlen( $filter_value ) > 1 && $source_field ) {
				$date_format = rgar( (array) $source_field, 'dateFormat' );
				$time        = false;
				if ( $date_format ) {
					$time = $this->date_to_time( $filter_value, $date_format );
				}

				if (
					! is_numeric( $filter_value )
					&& ( $time || ( strlen( $filter_value ) > 1 && strtotime( $filter_value ) ) )
				) {
					if ( ! $time ) {
						$time = strtotime( $filter_value );
					}

					$filter_value = date( 'Y-m-d', $time );
				}
			}

			$filter_value = new GF_Query_Literal( $filter_value );
		}

		$gf_query_where[ $filter_group_index ][] = new GF_Query_Condition(
			new GF_Query_Column( rgar( $property, 'value' ), (int) $primary_property_value ),
			$operator,
			$filter_value
		);

		return $gf_query_where;

	}

	public function include_active_entries( $where_filter_groups ) {

		$where_active = new GF_Query_Condition(
			new GF_Query_Column( 'status' ),
			GF_Query_Condition::EQ,
			new GF_Query_Literal( 'active' )
		);

		return call_user_func_array( array( 'GF_Query_Condition', '_and' ), array( $where_filter_groups, $where_active ) );

	}

	public function query( $args ) {

		/**
		 * @var $primary_property_value string
		 * @var $field_values array
		 * @var $filter_groups array
		 * @var $ordering array
		 * @var $field GF_Field
		 */
		extract( $args );

		if ( ! $primary_property_value ) {
			return array();
		}

		$query_limit = gp_populate_anything()->get_query_limit( $this, $field );

		$order_key = str_replace( 'gf_field_', '', rgar( $ordering, 'orderby' ) );
		$gf_query  = new GF_Query(
			$primary_property_value,
			null,
			array(
				'direction' => rgar( $ordering, 'order', 'ASC' ),
				'key'       => $order_key,
			),
			array(
				'page_size' => $query_limit,
			)
		);

		$gf_query_where_groups = $this->process_filter_groups( $args, array() );

		$has_status_filter = false;
		foreach ( $gf_query_where_groups as $gf_query_where_index => $gf_query_where_group ) {
			if ( $gf_query_where_group[0]->get_columns()[0]->field_id === 'status' ) {
				$has_status_filter = true;
			}
			$gf_query_where_groups[ $gf_query_where_index ] = call_user_func_array( array( 'GF_Query_Condition', '_and' ), $gf_query_where_group );
		}

		$where_filter_groups = call_user_func_array( array( 'GF_Query_Condition', '_or' ), $gf_query_where_groups );
		// Exclude all non-active entries unless "Status" is one of the conditionals
		$where = ( ! $has_status_filter ) ? $this->include_active_entries( $where_filter_groups ) : $where_filter_groups;

		$gf_query->where( $where );

		// If we're ordering form a form field, check the field type and adjust the SQL accordingly.
		if ( strlen( $field['gppa-choices-ordering-property'] ) > 0 && is_numeric( $order_key ) ) {
			$source_field = GFAPI::get_field( $field['gppa-choices-primary-property'], $order_key );
			// 12-hour Time fields need to be parsed since "01:00 pm" < "11:00 am" to MySQL's ORDER BY
			if ( $source_field && $source_field['type'] === 'time' && $source_field['timeFormat'] === '12' ) {
				$mask = '%h:%i %p'; // MySQL's STR_TO_DATE mask
				// @param array $sql An array with all the SQL fragments: select, from, join, where, order, paginate.
				$gform_gf_query_sql_func = function ( $sql ) use ( $mask ) {
					// Regex: meta_value with a look behind to capture (`...`.`meta_value`)
					$sql['order'] = preg_replace( '((<?`[^`]*`.`)meta_value`)', sprintf( "STR_TO_DATE($0, '%s')", $mask ), $sql['order'] );
					return $sql;
				};
				add_filter( 'gform_gf_query_sql', $gform_gf_query_sql_func );
			}
		}

		$entries = $gf_query->get();

		if ( isset( $gform_gf_query_sql_func ) ) {
			remove_filter( 'gform_gf_query_sql', $gform_gf_query_sql_func );
		}

		foreach ( $entries as $entry_index => $entry ) {
			$entry_object = new StdClass();

			foreach ( $entry as $key => $value ) {
				$entry_object->{$key} = $value;
			}

			$entries[ $entry_index ] = $entry_object;
		}

		return $entries;

	}

	/**
	 * Hashes GF Entry Query Arguments
	 *
	 * @param $args array  Query arguments to hash
	 *
	 * @return string   SHA1 representation of the requested query
	 */
	public function query_cache_hash( $args ) {
		return sha1( sprintf( '%s-%s-%s-%s-%s',
			$args['field']->formId,
			json_encode( $args['filter_groups'] ),
			json_encode( $args['ordering'] ),
			json_encode( $args['primary_property_value'] ),
			json_encode( $args['unique'] )
		) );
	}

	public function get_forms() {

		$forms = GFFormsModel::get_forms();

		return wp_list_pluck( $forms, 'title', 'id' );

	}

	public function get_form_fields( $form_id ) {

		$form = GFAPI::get_form( $form_id );

		if ( ! $form || ! $form_id ) {
			return array();
		}

		$output = array();

		foreach ( $form['fields'] as $field ) {
			if ( array_search( $field['type'], self::$excluded_fields ) !== false ) {
				continue;
			}

			/**
			 * Use admin label when listing out fields
			 */
			$use_admin_label_prev = $field->get_context_property( 'use_admin_label' );
			$field->set_context_property( 'use_admin_label', true );

			if ( empty( $field['inputs'] ) || in_array( $field['type'], GP_Populate_Anything::get_interpreted_multi_input_field_types() ) ) {
				$output[] = array(
					'value' => $field['id'],
					'label' => GFCommon::get_label( $field ),
				);
			} elseif ( is_array( $field['inputs'] ) ) {
				$output[] = array(
					'value' => $field['id'],
					'label' => GFCommon::get_label( $field ),
				);

				foreach ( $field['inputs'] as $input ) {
					$output[] = array(
						'value' => $input['id'],
						'label' => GFCommon::get_label( $field, $input['id'] ),
					);
				}
			}

			$field->set_context_property( 'use_admin_label', $use_admin_label_prev );
		}

		return $output;

	}

	public function get_form_fields_values( $form_id, $input_id ) {

		global $wpdb;

		$entry_meta_table = GFFormsModel::get_entry_meta_table_name();

		$sql = "SELECT meta_value from $entry_meta_table WHERE form_id = %d AND meta_key = %s";

		return $wpdb->get_col( $wpdb->prepare( $sql, $form_id, $input_id ) );

	}

	/**
	 * Combine multi-input fields such as checkboxes into a single array. Useful for populating multi-selectable choice
	 * inputs with the value of a multi-selectable choice entry field.
	 *
	 * @param $template_value
	 * @param $field
	 * @param $template
	 * @param $populate
	 * @param $object
	 * @param $object_type
	 * @param $objects
	 *
	 * @return mixed
	 */
	public function maybe_combine_multi_input_entry_template( $template_value, $field, $template, $populate, $object, $object_type, $objects ) {

		if ( $object_type->id !== $this->id ) {
			return $template_value;
		}

		if ( ! $object || is_scalar( $object ) ) {
			return $template_value;
		}

		$templates = rgar( $field, 'gppa-' . $populate . '-templates', array() );
		$template  = rgar( $templates, $template );

		if ( strpos( $template, 'gf_field_' ) !== 0 ) {
			return $template_value;
		}

		$field_id = str_replace( 'gf_field_', '', $template );

		/**
		 * We do not want to loop the object below unless the field ID is an integer and nothing is found in the entry
		 * with the supplied field ID.
		 *
		 * Coerce field ID string to an integer using "+ 0". This trick works well with is_float as well.
		 */
		if ( ( isset( $object->{$field_id} ) && is_scalar( $object->{$field_id} ) ) || ! is_int( $field_id + 0 ) ) {
			return $template_value;
		}

		$output = array();

		foreach ( $object as $key => $value ) {

			if ( absint( $key ) === absint( $field_id ) ) {
				$output[ $key ] = $value;
			}
		}

		$output = array_unique( array_filter( $output ) );

		return json_encode( $output );

	}

	public function replace_gf_merge_tags_for_entry( $template_value, $field, $template, $populate, $object, $object_type ) {

		if ( $object_type->id !== $this->id ) {
			return $template_value;
		}

		if ( empty( $object->form_id ) || ! $form = GFAPI::get_form( $object->form_id ) ) {
			return $template_value;
		}

		$entry = (array) $object;

		return GFCommon::replace_variables( $template_value, $form, $entry, false, false, false, 'text' );

	}

	public function does_field_accept_json( $field ) {
		return ! empty( $field->inputs ) || $field->storageType === 'json';
	}

}
