<?php

abstract class GPPA_Object_Type {

	public $id;

	protected $_restricted = false;

	abstract public function query( $args );

	abstract public function get_label();

	/**
	 * Extract unique identifier for a given object. For example, if an object type has a uuid property, this method
	 * should extract that prop from the object.
	 *
	 * @param $object
	 * @param  null  $primary_property_value
	 *
	 * @return string|number
	 */
	abstract public function get_object_id( $object, $primary_property_value = null );

	abstract public function get_properties( $primary_property_value = null );

	/**
	 * Returns a string based off of the args passed into form a unique identifier for a given query.
	 *
	 * Return null if the object type doesn't support query caching.
	 *
	 * @param $args
	 *
	 * @return string|null
	 */
	public function query_cache_hash( $args ) {
		return null;
	}

	public function get_properties_filtered( $primary_property_value = null ) {
		/**
		 * Modify the properties that are available for filtering and ordering for the current object type.
		 *
		 * @since 1.0-beta-3.35
		 *
		 * @param array  $props       The properties available for filtering/ordering for the current object type.
		 * @param string $object_type The current object type.
		 */
		return gf_apply_filters( array( 'gppa_object_type_properties', $this->id ), $this->get_properties( $primary_property_value ), $this->id );
	}

	public function isRestricted() {
		return apply_filters( 'gppa_object_type_restricted_' . $this->id, $this->_restricted );
	}

	public function __construct( $id ) {
		$this->id = $id;

		add_filter( 'gppa_replace_filter_value_variables_' . $this->id, array( $this, 'replace_gf_field_value' ), 10, 2 );
		add_filter( 'gppa_replace_filter_value_variables_' . $this->id, array( $this, 'replace_special_values' ), 10 );
		add_filter( 'gppa_replace_filter_value_variables_' . $this->id, array( $this, 'clean_numbers' ), 10 );
	}

	public function get_primary_property() {
		return null;
	}

	public function get_groups() {
		return array();
	}

	public function get_default_templates() {
		return array();
	}

	public function default_query_args( $args ) {
		return array();
	}

	public function to_simple_array() {

		$output = array(
			'id'         => $this->id,
			'label'      => $this->get_label(),
			'properties' => array(),
			'groups'     => $this->get_groups(),
			'templates'  => $this->get_default_templates(),
			'restricted' => $this->isRestricted(),
		);

		if ( $this->get_primary_property() ) {
			$output['primary-property'] = $this->get_primary_property();
		}

		return $output;

	}

	public function replace_gf_field_value( $value, $field_values ) {

		if ( preg_match_all( '/{\w+:gf_field_(\d+)}/', $value, $field_matches ) ) {
			if ( count( $field_matches ) && ! empty( $field_matches[0] ) ) {
				foreach ( $field_matches[0] as $index => $match ) {
					$field_id       = $field_matches[1][ $index ];
					$replaced_value = $this->replace_gf_field_value( "gf_field:{$field_id}", $field_values );
					$value          = str_replace( $match, $replaced_value, $value );
				}

				return $value;
			}
		}

		if ( strpos( $value, 'gf_field' ) !== 0 ) {
			return $value;
		}

		if ( ! $field_values ) {
			return null;
		}

		$value_exploded = explode( ':', $value );
		$value          = gp_populate_anything()->get_field_value_from_field_values( $value_exploded[1], $field_values );

		return $value === '' ? null : $value;

	}

	public function replace_special_values( $value ) {

		if ( ! is_scalar( $value ) || strpos( $value, 'special_value:' ) !== 0 ) {
			return $value;
		}

		$special_value       = str_replace( 'special_value:', '', $value );
		$special_value_parts = explode( ':', $special_value );

		switch ( $special_value_parts[0] ) {
			case 'current_user':
				$user = wp_get_current_user();

				if ( $user && $user->ID > 0 ) {
					return $user->{$special_value_parts[1]};
				}

				break;
			case 'current_post':
				$post    = get_post();
				$referer = rgar( $_SERVER, 'HTTP_REFERER' );

				if ( ! $post && $referer && $referer_post_id = url_to_postid( $referer ) ) {
					$post = get_post( $referer_post_id );
				}

				if ( $post ) {
					return $post->{$special_value_parts[1]};
				}

				break;
		}

		/* No current post or user, return impossible ID */
		return apply_filters( 'gppa_special_value_no_result', -1, $value, $special_value );

	}

	public function clean_numbers( $value ) {

		// @todo Consider cleaning numbers inside the array?
		if ( is_array( $value ) ) {
			return $value;
		}

		if ( GFCommon::is_numeric( $value, 'decimal_dot' ) ) {
			return GFCommon::clean_number( $value, 'decimal_dot' );
		}

		if ( GFCommon::is_numeric( $value, 'decimal_comma' ) ) {
			return GFCommon::clean_number( $value, 'decimal_comma' );
		}

		return $value;

	}

	public function get_object_prop_value( $object, $prop ) {

		if ( ! isset( $object->{$prop} ) ) {
			return null;
		}

		return $object->{$prop};

	}

	public function get_col_rows( $table, $col, $where = '' ) {

		static $_cache;

		global $wpdb;

		/**
		 * Filter the query used to pull property values into the dropdowns displayed under the GP Populate Anything
		 * field settings in the Form Editor.
		 *
		 * @since 1.0-beta-1.9
		 *
		 * @param string $sql SQL query that will be ran to fetch the property values.
		 * @param string $col Column that property values are being fetched from.
		 * @param array $table Table that property values are being fetched from.
		 * @param \GPPA_Object_Type $this The current object type.
		 *
		 * @example https://github.com/gravitywiz/snippet-library/blob/master/gp-populate-anything/gppa-postmeta-property-value-limit.php
		 */
		$query = apply_filters( 'gppa_object_type_col_rows_query', "SELECT DISTINCT $col FROM $table {$where} LIMIT 1000", $col, $table, $this );
		if ( isset( $_cache[ $query ] ) ) {
			return $_cache[ $query ];
		}

		$result = $wpdb->get_col( $query );

		$_cache[ $query ] = is_array( $result ) ? $this->filter_values( $result ) : array();

		return $_cache[ $query ];
	}

	public function get_meta_values( $meta_key, $table ) {

		global $wpdb;

		$query  = $wpdb->prepare( "SELECT DISTINCT meta_value FROM $table WHERE meta_key = '%s'", $meta_key );
		$result = $wpdb->get_col( $query );

		return is_array( $result ) ? $this->filter_values( $result ) : array();

	}

	public function process_filter_groups( $args, $processed_filter_groups = array() ) {

		/**
		 * @var $primary_property_value string
		 * @var $field_values array
		 * @var $filter_groups array
		 * @var $ordering array
		 * @var $field array
		 */
		extract( $args );

		$properties = $this->get_properties_filtered( $primary_property_value );

		gf_do_action( array( 'gppa_pre_object_type_query', $this->id ), $processed_filter_groups, $args );

		if ( ! is_array( $filter_groups ) ) {
			return $processed_filter_groups;
		}

		foreach ( $filter_groups as $filter_group_index => $filter_group ) {
			foreach ( $filter_group as $filter ) {
				$filter_value = gp_populate_anything()->extract_custom_value( $filter['value'] );
				$filter_value = GFCommon::replace_variables_prepopulate( $filter_value, false, false, true );

				if ( ! $filter['value'] || ! $filter['property'] ) {
					continue;
				}

				$property = rgar( $properties, $filter['property'] );

				if ( ! $property ) {
					continue;
				}

				$filter_value   = apply_filters( 'gppa_replace_filter_value_variables_' . $this->id, $filter_value, $field_values, $primary_property_value, $filter, $ordering, $field, $property );
				$wp_filter_name = 'gppa_object_type_' . $this->id . '_filter_' . $filter['property'];

				if ( ! has_filter( $wp_filter_name ) && $group = rgar( $property, 'group' ) ) {
					$wp_filter_name = 'gppa_object_type_' . $this->id . '_filter_group_' . $group;
				}

				if ( ! has_filter( $wp_filter_name ) ) {
					$wp_filter_name = 'gppa_object_type_' . $this->id . '_filter';
				}

				$processed_filter_groups = apply_filters(
					$wp_filter_name,
					$processed_filter_groups,
					array(
						'filter_value'           => $filter_value,
						'filter'                 => $filter,
						'field'                  => $field,
						'filter_group'           => $filter_group,
						'filter_group_index'     => $filter_group_index,
						'primary_property_value' => $primary_property_value,
						'property'               => $property,
						'property_id'            => $filter['property'],
					)
				);
			}
		}

		$processed_filter_groups = apply_filters( 'gppa_object_type_query', $processed_filter_groups, $args );
		$processed_filter_groups = apply_filters( 'gppa_object_type_query_' . $this->id, $processed_filter_groups, $args );

		return $processed_filter_groups;

	}

	/**
	 * Generate MySQL query using the provided select, from, joins, wheres, group_by, order, and order_by.
	 *
	 * @see GPPA_Object_Type::process_filter_groups()
	 *
	 * @param $query_args array Typically generated by GPPA_Object_Type::process_filter_groups()
	 * @param $field GF_Field
	 *
	 * @return string
	 */
	public function build_mysql_query( $query_args, $field ) {

		global $wpdb;

		$query = array();

		$select = ! is_array( $query_args['select'] ) ? array( $query_args['select'] ) : $query_args['select'];
		$select = array_map( array( __CLASS__, 'esc_property_to_ident' ), $select );
		$select = implode( ', ', $select );

		$from = self::esc_property_to_ident( $query_args['from'] );

		$query[] = "SELECT {$select} FROM {$from}";

		if ( ! empty( $query_args['joins'] ) ) {
			foreach ( $query_args['joins'] as $join_name => $join ) {
				$query[] = $join;
			}
		}

		if ( ! empty( $query_args['where'] ) ) {
			$where_clauses = array();

			foreach ( $query_args['where'] as $where_or_grouping => $where_or_grouping_clauses ) {
				$where_clauses[] = '(' . implode( ' AND ', $where_or_grouping_clauses ) . ')';
			}

			$query[] = "WHERE \n" . implode( "\n OR ", $where_clauses );
		}

		if ( ! empty( $query_args['group_by'] ) ) {
			$group_by = self::esc_property_to_ident( $query_args['group_by'] );

			$query[] = "GROUP BY {$group_by}";
		}

		if ( ! empty( $query_args['order_by'] ) && ! empty( $query_args['order'] ) ) {
			$order_by = self::esc_property_to_ident( $query_args['order_by'], 'order_by' );
			$order    = $query_args['order'];

			if ( ! in_array( strtoupper( $order ), array( 'ASC', 'DESC', 'RAND' ), true ) ) {
				$order = 'DESC';
			} elseif ( strtoupper( $order ) === 'RAND' ) {
				// Use MySQL's rand() function if random ordering is requested.
				$order_by = 'rand()';
				$order    = '';
			}

			$query[] = "ORDER BY {$order_by} {$order}";
		}

		$query_limit = gp_populate_anything()->get_query_limit( $this, $field );
		$query[]     = $wpdb->prepare( 'LIMIT %d', $query_limit );

		return implode( "\n", $query );

	}

	public function get_value_specification( $value, $operator, $sql_operator ) {

		$specification = '%s';

		// Cast numeric strings to the appropriate type for operators such as > and <.
		// Regex check ensures that strings mimicing scientific notation like "1e4465" are not cast
		// to infinity which breaks SQL.
		if ( is_numeric( $value ) && ! preg_match( '/[a-z]/i', $value ) ) {
			$value = ( $value == (int) $value ) ? (int) $value : (float) $value;
		}

		if ( $sql_operator !== 'LIKE' ) {
			if ( is_int( $value ) ) {
				$specification = '%d';
			} elseif ( is_float( $value ) ) {
				$specification = '%f';
			}
		}

		if ( in_array( $sql_operator, array( 'IN', 'NOT IN' ), true ) ) {
			$specification_array = array_map( function ( $v ) {
				return '%s';
			}, $value );

			$specification = '(' . join( ',', $specification_array ) . ')';
		}

		return $specification;

	}

	public function get_sql_value( $operator, $value ) {

		global $wpdb;

		switch ( $operator ) {
			case 'starts_with':
				return $wpdb->esc_like( $value ) . '%';

			case 'ends_with':
				return '%' . $wpdb->esc_like( $value );

			case 'contains':
			case 'does_not_contain':
				return '%' . $wpdb->esc_like( $value ) . '%';

			case 'is_in':
			case 'is_not_in':
				$value = is_array( $value ) ? $value : explode( ',', $value );
				return array_map( $wpdb->esc_like, $value );

			default:
				return $value;
		}

	}

	public function get_sql_operator( $operator ) {

		switch ( $operator ) {
			case 'starts_with':
				return 'LIKE';

			case 'ends_with':
				return 'LIKE';

			case 'contains':
				return 'LIKE';

			case 'does_not_contain':
				return 'NOT LIKE';

			case 'is':
				return '=';

			case 'isnot':
				return '!=';

			case 'is_in':
				return 'IN';

			case 'is_not_in':
				return 'NOT IN';

			default:
				return $operator;
		}

	}

	public function build_where_clause( $table, $column, $operator, $value ) {

		global $wpdb;

		$sql_operator  = $this->get_sql_operator( $operator );
		$value         = $this->get_sql_value( $operator, $value );
		$specification = $this->get_value_specification( $value, $operator, $sql_operator );

		$ident = self::esc_property_to_ident( "{$table}.{$column}" );

		return $wpdb->prepare( "{$ident} {$sql_operator} {$specification}", $value );

	}

	/*
	 * array_filter - Remove serialized values
	 * array_filter - Remove falsey values
	 * array_unique - Ran to make sequential for json_encode
	 */
	public function filter_values( $values ) {

		$values = array_values(
			array_unique(
				array_filter(
					array_filter(
						$values,
						array(
							__class__,
							'is_not_serialized',
						)
					)
				)
			)
		);

		natcasesort( $values );

		/* Run array values again so it's an ordered indexed array again */
		return array_values( $values );

	}

	public static function is_not_serialized( $value ) {
		return ! is_serialized( $value );
	}

	/**
	 * Escapes property for an SQL query
	 *
	 * Prepares $property for use in an SQL statement. 'table.name' would be escaped as '`table`.`name`'.
	 * If 'order_by' is passed in $clause and $property contained spaces, this function will return $property
	 * without any modifications.
	 *
	 * @param string $property String to escape
	 * @param string $clause Current clause being processed (accepts 'order_by')
	 *
	 * @return string
	 */
	public static function esc_property_to_ident( $property, $clause = '' ) {
		if ( preg_match( '/\s/', $property ) && $clause === 'order_by' ) {
			return $property;
		}
		return implode( '.', self::esc_sql_ident( explode( '.', $property ) ) );
	}

	public static function esc_sql_ident( $ident ) {
		if ( is_string( $ident ) ) {
			return self::esc_sql_ident_cb( $ident );
		}

		return array_map( array( __CLASS__, 'esc_sql_ident_cb' ), $ident );
	}

	public static function esc_sql_ident_cb( $ident ) {
		if ( $ident === '*' ) {
			return $ident;
		}

		return '`' . str_replace( '`', '``', $ident ) . '`';
	}

}
