<?php

class GPPA_Object_Type_Database extends GPPA_Object_Type {

	protected $_restricted = true;

	protected $_primary_key_cache = array();

	private static $blacklisted_columns = array( 'password', 'user_pass', 'user_activation_key' );

	public function __construct( $id ) {
		parent::__construct( $id );

		add_action( sprintf( 'gppa_pre_object_type_query_%s', $id ), array( $this, 'add_filter_hooks' ) );
	}

	public function add_filter_hooks() {
		add_filter( sprintf( 'gppa_object_type_%s_filter', $this->id ), array( $this, 'process_filter_default' ), 10, 4 );
	}

	/**
	 * Due to complexities with passing through field value objects and getting the primary key using SHOW COLUMNS,
	 * the easiest thing to do is simply to select the first column since 99 times out of 100, it'll be a unique ID
	 * column.
	 *
	 * @param $object
	 *
	 * @return number|string
	 */
	public function get_object_id( $object, $primary_property_value = null ) {
		if ( ! $object || ! $primary_property_value ) {
			return null;
		}

		if ( ! isset( $this->_primary_key_cache[ $primary_property_value ] ) ) {
			$props = array_keys( $object );
			$this->_primary_key_cache[ $primary_property_value ] = $props[0];
		}

		$key = $this->_primary_key_cache[ $primary_property_value ];

		return $object[ $key ];
	}

	public function get_label() {
		return esc_html__( 'Database: ', 'gp-populate-anything' ) . DB_NAME;
	}

	public function get_groups() {
		return array(
			'columns' => array(
				'label' => esc_html__( 'Columns', 'gp-populate-anything' ),
			),
		);
	}

	public function get_primary_property() {
		return array(
			'id'       => 'table',
			'label'    => esc_html__( 'Table', 'gp-populate-anything' ),
			'callable' => array( $this, 'get_tables' ),
		);
	}

	public function get_properties( $table = null ) {

		if ( ! $table ) {
			return array();
		}

		$properties = array();

		foreach ( $this->get_columns( $table ) as $column ) {
			$properties[ $column['value'] ] = array(
				'group'     => 'columns',
				'label'     => $column['label'],
				'value'     => $column['value'],
				'orderby'   => true,
				'callable'  => array( $this, 'get_column_values' ),
				'args'      => array( $table, $column['value'] ),
				'operators' => array(
					'is',
					'isnot',
					'>',
					'>=',
					'<',
					'<=',
					'contains',
					'does_not_contain',
					'starts_with',
					'ends_with',
					'like',
					'is_in',
					'is_not_in',
				),
			);
		}

		return $properties;

	}

	public function get_db() {
		global $wpdb;

		return $wpdb;
	}

	public function get_tables() {
		$result = $this->get_db()->get_results( 'SHOW FULL TABLES', ARRAY_N );

		return wp_list_pluck( $result, 0 );
	}

	public function get_columns( $table ) {
		$table   = self::esc_sql_ident( $table );
		$columns = array();

		$results = $this->get_db()->get_results( "SHOW COLUMNS FROM $table", ARRAY_N );

		foreach ( $results as $column ) {
			$columns[] = array(
				'value' => $column[0],
				'label' => $column[0],
			);
		}

		return $columns;
	}

	public function get_column_values( $table, $col ) {
		$table = self::esc_sql_ident( $table );
		$col   = self::esc_sql_ident( $col );

		$query  = apply_filters( 'gppa_object_type_database_column_value_query', "SELECT DISTINCT $col FROM $table", $table, $col, $this );
		$result = $this->get_db()->get_results( $query, ARRAY_N );

		return $this->filter_values( wp_list_pluck( $result, 0 ) );
	}

	public function process_filter_default( $query_builder_args, $args ) {

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

		$query_builder_args['where'][ $filter_group_index ][] = $this->build_where_clause( $primary_property_value, $property_id, $filter['operator'], $filter_value );

		return $query_builder_args;

	}

	public function default_query_args( $args ) {

		/**
		 * @var $primary_property_value string
		 * @var $field_values array
		 * @var $templates array
		 * @var $filter_groups array
		 * @var $ordering array
		 * @var $field array
		 * @var $unique boolean
		 */
		extract( $args );

		$orderby = rgar( $ordering, 'orderby' );
		$order   = rgar( $ordering, 'order', 'ASC' );

		return array(
			'select'   => '*',
			'from'     => $primary_property_value,
			'where'    => array(),
			'order_by' => $orderby,
			'order'    => $order,
		);

	}

	public function query_cache_hash( $args ) {
		$query_args = $this->process_filter_groups( $args, $this->default_query_args( $args ) );

		return $this->build_mysql_query( apply_filters( 'gppa_object_type_database_pre_query_parts', $query_args, $this ), rgar( $args, 'field' ) );
	}

	public function query( $args ) {

		$query_args = $this->process_filter_groups( $args, $this->default_query_args( $args ) );

		$query = $this->build_mysql_query( apply_filters( 'gppa_object_type_database_pre_query_parts', $query_args, $this ), rgar( $args, 'field' ) );

		return $this->get_db()->get_results( apply_filters( 'gppa_object_type_database_query', $query, $args, $this ), ARRAY_A );

	}

	public function get_object_prop_value( $object, $prop ) {

		if ( in_array( $prop, self::$blacklisted_columns ) ) {
			return null;
		}

		if ( ! isset( $object[ $prop ] ) ) {
			return null;
		}

		return $object[ $prop ];

	}

	public function build_where_clause( $table, $column, $operator, $value ) {
		$value = $this->maybe_convert_to_date( $table, $column, $value );
		return parent::build_where_clause( $table, $column, $operator, $value );
	}

	private $tables_cache = array(); // MySQL tables format cache

	/**
	 * Converts $value to MySQL friendly date if the table column is of type date.
	 *
	 * @param $table  string  Table name to look up
	 * @param $column string  Column name
	 * @param $value  string  Value to convert
	 *
	 * @return string  Converted date value if applicable.
	 */
	private function maybe_convert_to_date( $table, $column, $value ) {
		$is_date = false;
		if ( isset( $this->tables_cache[ $table ] ) ) {
			$is_date = $this->tables_cache[ $table ][ $column ] === 'date';
		} else {
			global $wpdb;
			$structure = $wpdb->get_results( sprintf( 'DESCRIBE `%s`', esc_sql( $table ) ), ARRAY_N );
			foreach ( $structure as $index => $row ) {
				$this->tables_cache[ $table ][ $row[0] ] = $row[1];
				if ( $row[0] === $column && $row[1] === 'date' ) {
					$is_date = true;
				}
			}
		}
		if ( $is_date ) {
			$value = gmdate( 'Y-m-d', strtotime( $value ) );
		}
		return $value;
	}

}
