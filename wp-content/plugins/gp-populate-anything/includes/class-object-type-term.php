<?php

class GPPA_Object_Type_Term extends GPPA_Object_Type {

	private $meta_query_counter = 0;

	public function __construct( $id ) {
		parent::__construct( $id );

		add_action( 'gppa_pre_object_type_query_term', array( $this, 'add_filter_hooks' ) );
	}

	/**
	 * Extract unique identifier for a given term.
	 *
	 * @param $object WP_Term
	 * @param  null  $primary_property_value
	 *
	 * @return string|number
	 */
	public function get_object_id( $object, $primary_property_value = null ) {
		return $object->term_id;
	}

	public function get_label() {
		return esc_html__( 'Taxonomy Term', 'gp-populate-anything' );
	}

	public function get_groups() {
		return array(
			'meta' => array(
				'label' => esc_html__( 'Term Meta', 'gp-populate-anything' ),
			),
		);
	}

	public function get_object_prop_value( $object, $prop ) {

		/* Meta */
		if ( strpos( $prop, 'meta_' ) === 0 ) {
			$meta_key = preg_replace( '/^meta_/', '', $prop );

			return get_term_meta( $object->term_id, $meta_key, true );
		}

		/* All other props */
		if ( ! isset( $object->{$prop} ) ) {
			return null;
		}

		return $object->{$prop};

	}

	public function get_properties( $primary_property = null ) {

		global $wpdb;

		return array_merge(
			array(
				'taxonomy'  => array(
					'label'    => esc_html__( 'Taxonomy', 'gp-populate-anything' ),
					'value'    => 'taxonomy',
					'callable' => array( $this, 'get_taxonomies' ),
					'orderby'  => true,
				),
				'name'      => array(
					'label'    => esc_html__( 'Name', 'gp-populate-anything' ),
					'value'    => 'name',
					'callable' => array( $this, 'get_col_rows' ),
					'args'     => array( $wpdb->terms, 'name' ),
					'orderby'  => true,
				),
				'slug'      => array(
					'label'    => esc_html__( 'Slug', 'gp-populate-anything' ),
					'value'    => 'slug',
					'callable' => array( $this, 'get_col_rows' ),
					'args'     => array( $wpdb->terms, 'slug' ),
					'orderby'  => true,
				),
				'object_id' => array(
					'label'    => esc_html__( 'Object ID', 'gp-populate-anything' ),
					'value'    => 'object_id',
					'callable' => array( $this, 'get_col_rows' ),
					'args'     => array( $wpdb->term_relationships, 'object_id' ),
					'orderby'  => false,
				),
				'term_id'   => array(
					'label'    => esc_html__( 'Term ID', 'gp-populate-anything' ),
					'value'    => 'term_id',
					'callable' => array( $this, 'get_col_rows' ),
					'args'     => array( $wpdb->terms, 'term_id' ),
					'orderby'  => true,
				),
				'parent'    => array(
					'label'    => esc_html__( 'Parent Term', 'gp-populate-anything' ),
					'value'    => 'parent',
					'callable' => array( $this, 'get_terms' ),
					'orderby'  => true,
				),
			),
			$this->get_term_meta_properties()
		);

	}

	public function get_term_meta_properties() {

		global $wpdb;

		$term_meta_properties = array();

		foreach ( $this->get_col_rows( $wpdb->termmeta, 'meta_key' ) as $term_meta_key ) {
			$term_meta_properties[ 'meta_' . $term_meta_key ] = array(
				'label'     => $term_meta_key,
				'value'     => $term_meta_key,
				'callable'  => array( $this, 'get_meta_values' ),
				'args'      => array( $term_meta_key, $wpdb->termmeta ),
				'group'     => 'meta',
				'operators' => array(
					'is',
					'isnot',
					'>',
					'>=',
					'<',
					'<=',
					'contains',
					'starts_with',
					'ends_with',
					'like',
					'is_in',
					'is_not_in',
				),
			);
		}

		return $term_meta_properties;

	}

	public function add_filter_hooks() {
		add_filter( 'gppa_object_type_term_filter', array( $this, 'process_filter_default' ), 10, 4 );
		add_filter( 'gppa_object_type_term_filter_parent', array( $this, 'process_filter_with_term_taxonomy' ), 10, 4 );
		add_filter( 'gppa_object_type_term_filter_taxonomy', array( $this, 'process_filter_with_term_taxonomy' ), 10, 4 );
		add_filter( 'gppa_object_type_term_filter_object_id', array( $this, 'process_filter_object_id' ), 10, 4 );
		add_filter( 'gppa_object_type_term_filter_group_meta', array( $this, 'process_filter_meta' ), 10, 4 );
	}

	public function process_filter_default( $query_builder_args, $args ) {

		global $wpdb;

		/**
		 * @var $filter_value
		 * @var $filter
		 * @var $filter_group
		 * @var $filter_group_index
		 * @var $property
		 * @var $property_id
		 */
		extract( $args );

		$query_builder_args['where'][ $filter_group_index ][] = $this->build_where_clause( $wpdb->terms, rgar( $property, 'value' ), $filter['operator'], $filter_value );

		return $query_builder_args;

	}

	public function process_filter_meta( $query_builder_args, $args ) {

		global $wpdb;

		/**
		 * @var $filter_value
		 * @var $filter
		 * @var $filter_group
		 * @var $filter_group_index
		 * @var $property
		 * @var $property_id
		 */
		extract( $args );

		$meta_operator      = $this->get_sql_operator( $filter['operator'] );
		$meta_specification = $this->get_value_specification( $filter_value, $filter['operator'], $meta_operator );
		$meta_value         = $this->get_sql_value( $filter['operator'], $filter_value );

		$this->meta_query_counter++;
		$as_table = 'mq' . $this->meta_query_counter;

		/**
		 * Add special logic when searching in a meta array. This is useful when searching for property values in
		 * meta array like Pods relationships
		 *
		 * Note: unless there's an array of arrays, meta_key needs to be scalar.
		 */
		if ( is_array( $filter_value ) && in_array( $filter['operator'], array( 'is_in', 'is_not_in' ) ) ) {

			$escaped_filter_value = array_map(
				function ( $v ) {
					return "'" . esc_sql( $v ) . "'";
				},
				$filter_value
			);

			$operator = $filter['operator'] === 'is_in' ? 'IN' : 'NOT IN';

			$where = "{$wpdb->terms}.term_id $operator (
				SELECT term_id
					FROM {$wpdb->termmeta}
					WHERE {$wpdb->termmeta}.meta_value IN (" . implode( ',', $escaped_filter_value ) . ")
						AND {$wpdb->termmeta}.meta_key = %s
				)";

			$query_builder_args['where'][ $filter_group_index ][] = $wpdb->prepare( $where, rgar( $property, 'value' ) );

			/**
			 * Traditional meta where
			 */
		} else {
			$query_builder_args['where'][ $filter_group_index ][] = $wpdb->prepare( "( {$as_table}.meta_key = %s AND {$as_table}.meta_value {$meta_operator} {$meta_specification} )", rgar( $property, 'value' ), $meta_value );
			$query_builder_args['joins'][ $as_table ]             = "LEFT JOIN {$wpdb->termmeta} AS {$as_table} ON ( {$wpdb->terms}.term_id = {$as_table}.term_id )";
		}

		return $query_builder_args;

	}

	public function process_filter_with_term_taxonomy( $query_builder_args, $args ) {

		global $wpdb;

		/**
		 * @var $filter_value
		 * @var $filter
		 * @var $filter_group
		 * @var $filter_group_index
		 * @var $property
		 * @var $property_id
		 */
		extract( $args );

		$query_builder_args['where'][ $filter_group_index ][] = $this->build_where_clause( $wpdb->term_taxonomy, rgar( $property, 'value' ), $filter['operator'], $filter_value );

		return $query_builder_args;

	}

	public function process_filter_object_id( $query_builder_args, $args ) {

		global $wpdb;

		/**
		 * @var $filter_value
		 * @var $filter
		 * @var $filter_group
		 * @var $filter_group_index
		 * @var $property
		 * @var $property_id
		 */
		extract( $args );

		$query_builder_args['where'][ $filter_group_index ][] = $this->build_where_clause( $wpdb->term_relationships, 'object_id', $filter['operator'], $filter_value );
		$query_builder_args['joins'][]                        = "LEFT JOIN {$wpdb->term_relationships} ON ( {$wpdb->term_taxonomy}.term_taxonomy_id = {$wpdb->term_relationships}.term_taxonomy_id )";

		return $query_builder_args;

	}

	public function get_terms() {
		global $wpdb;

		$result = wp_list_pluck( $wpdb->get_results( "SELECT DISTINCT term_id, name FROM $wpdb->terms" ), 'name', 'term_id' );

		natcasesort( $result );

		return $result;
	}

	public function get_taxonomies() {

		$taxonomies = array();

		foreach ( get_taxonomies( null, 'objects' ) as $taxonomy ) {
			$taxonomies[ $taxonomy->name ] = $taxonomy->labels->singular_name;
		}

		return $taxonomies;

	}

	public function default_query_args( $args ) {

		global $wpdb;

		/**
		 * @var $primary_property_value string
		 * @var $field_values array
		 * @var $filter_groups array
		 * @var $ordering array
		 * @var $field array
		 */
		extract( $args );

		$orderby = rgar( $ordering, 'orderby' );
		$order   = rgar( $ordering, 'order', 'ASC' );

		// Specify the table name for ordering since we're joinging terms and term_taxonomy
		$orderby_table = $wpdb->terms;
		if ( in_array( $orderby, array( 'taxonomy', 'parent' ), true ) ) {
			$orderby_table = $wpdb->term_taxonomy;
		}

		return array(
			'select'   => array( "{$wpdb->terms}.*", "{$wpdb->term_taxonomy}.*" ),
			'from'     => $wpdb->terms,
			'where'    => array(),
			'joins'    => array(
				"LEFT JOIN {$wpdb->term_taxonomy} ON ( {$wpdb->terms}.term_id = {$wpdb->term_taxonomy}.term_id )",
			),
			'group_by' => "{$wpdb->terms}.term_id",
			'order_by' => $orderby ? "{$orderby_table}.{$orderby}" : '', // Append terms table to make sure that `orderby` is not ambiguous. See HS#25707
			'order'    => $order,
		);

	}

	/**
	 * @param $args array  Query arguments to hash
	 *
	 * @return string   SHA1 representation of the requested query
	 */
	public function query_cache_hash( $args ) {
		return sha1( serialize( $this->process_filter_groups( $args, $this->default_query_args( $args ) ) ) );
	}

	public function query( $args ) {

		global $wpdb;

		$query_args = $this->process_filter_groups( $args, $this->default_query_args( $args ) );

		$query = $this->build_mysql_query( $query_args, rgar( $args, 'field' ) );
		$terms = $wpdb->get_results( $query );

		foreach ( $terms as $key => $term ) {
			$terms[ $key ] = new WP_Term( $term );
		}

		return $terms;

	}

}
