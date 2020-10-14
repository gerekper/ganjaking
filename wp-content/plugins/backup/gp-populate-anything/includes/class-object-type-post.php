<?php

class GPPA_Object_Type_Post extends GPPA_Object_Type {

	private $meta_query_counter = 0;

	public function __construct($id) {
		parent::__construct($id);

		add_action( 'gppa_pre_object_type_query_post', array( $this, 'add_filter_hooks' ) );

		/* Label Transforms */
		add_filter( 'gppa_property_label_post_post_author', array( $this, 'label_transform_author' ) );
		add_filter( 'gppa_property_label_post_post_type', array( $this, 'label_transform_post_type' ) );
	}

	/**
	 * Extract unique identifier for a given post.
	 *
	 * @param $object WP_Post
	 * @param  null  $primary_property_value
	 *
	 * @return string|number
	 */
	public function get_object_id( $object, $primary_property_value = null ) {
		if ( empty( $object ) ) {
			return null;
		}

		return $object->ID;
	}

	public function get_label() {
		return esc_html__( 'Post', 'gp-populate-anything' );
	}

	public function get_default_templates() {
		return array(
			'value' => 'ID',
			'label' => 'post_title',
		);
	}

	public function get_groups() {
		return array(
			'taxonomies' => array(
				'label'     => esc_html__( 'Post Taxonomies', 'gp-populate-anything' ),
				'operators' => array(
					'is',
					'isnot',
					'is_in',
					'is_not_in',
				),
			),
			'meta'       => array(
				'label'     => esc_html__( 'Post Meta', 'gp-populate-anything' ),
			),
		);
	}

	public function add_filter_hooks() {
		add_filter('gppa_object_type_post_filter', array( $this, 'process_filter_default'), 10, 4 );
		add_filter('gppa_object_type_post_filter_post_date', array( $this, 'process_filter_post_date'), 10, 4 );
		add_filter('gppa_object_type_post_filter_group_meta', array( $this, 'process_filter_meta'), 10, 4 );
		add_filter('gppa_object_type_post_filter_group_taxonomies', array( $this, 'process_filter_taxonomy'), 10, 4 );
		add_filter('gppa_object_type_query_post', array( $this, 'maybe_add_post_status_where'), 10, 2 );
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
		extract($args);

		$query_builder_args['where'][ $filter_group_index ][] = $this->build_where_clause( $wpdb->posts, rgar( $property, 'value' ), $filter['operator'], $filter_value );

		return $query_builder_args;

	}

	/**
	 * Transform date into MySQL DATETIME and adjust time based on operator
	 */
	public function process_filter_post_date( $query_builder_args, $args ) {

		global $wpdb;

		/**
		 * @var $filter_value
		 * @var $filter
		 * @var $filter_group
		 * @var $filter_group_index
		 * @var $property
		 * @var $property_id
		 */
		extract($args);

		if ( $filter['operator'] === '>=' || $filter['operator'] === '>' ) {
			$filter_value = date( "Y-m-d 00:00:00", strtotime( $filter_value ) );
		} elseif ( $filter['operator'] === '<=' || $filter['operator'] === '<' ) {
			$filter_value = date( "Y-m-d 23:59:59", strtotime( $filter_value ) );
		}

		$query_builder_args['where'][ $filter_group_index ][] = $this->build_where_clause( $wpdb->posts, rgar( $property, 'value' ), $filter['operator'], $filter_value );

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
		extract($args);

		$meta_specification = $this->get_value_specification( $filter_value, $filter['operator'] );
		$meta_operator      = $this->get_sql_operator( $filter['operator'] );
		$meta_value         = $this->get_sql_value( $filter['operator'], $filter_value );

		$this->meta_query_counter++;
		$as_table = 'mq' . $this->meta_query_counter;

		$query_builder_args['where'][ $filter_group_index ][] = $wpdb->prepare( "( {$as_table}.meta_key = %s AND {$as_table}.meta_value {$meta_operator} {$meta_specification} )", rgar( $property, 'value' ), $meta_value );
		$query_builder_args['joins'][$as_table] = "LEFT JOIN {$wpdb->postmeta} AS {$as_table} ON ( {$wpdb->posts}.ID = {$as_table}.post_id )";

		return $query_builder_args;

	}

	public function process_filter_taxonomy( $query_builder_args, $args ) {

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

		switch ( $filter['operator'] ) {
			case 'is':
				$operator = '=';
				break;

			case 'isnot':
				$operator = '!=';
				break;

			/**
			 * We utilize a COUNT which will return the number of matches inside the array which is why you see GTE/LT
			 * here instead of IN / NOT IN
			 */
			case 'is_in':
				$operator = '>=';
				break;

			case 'is_not_in':
				$operator = '<';
				break;

			default:
				/* Invalid operator, bail out. */
				return $query_builder_args;
				break;
		}

		$taxonomy = str_replace( 'taxonomy_', '', $property_id );
		$term = null;

		/**
		 * Add special logic when searching for terms in an array. This is useful when searching for posts that are
		 * in an array of categories.
		 */
		if ( is_array( $filter_value ) && in_array( $filter['operator'], array( 'is_in', 'is_not_in' ) ) ) {

			$escaped_filter_value = array_map( function ( $v ) {
				return "'" . esc_sql( $v ) . "'";
			}, $filter_value );

			$where = "(
	SELECT COUNT(1)
	FROM {$wpdb->term_relationships}
	WHERE {$wpdb->term_relationships}.term_taxonomy_id IN (" . implode(',', $escaped_filter_value) . ")
	AND {$wpdb->term_relationships}.object_id = {$wpdb->posts}.ID
) $operator 1";

		/**
		 * Traditional single taxonomy term lookup.
		 */
		} else {
			/* First, look for term by ID if the filter value is a number */
			if ( is_numeric( $filter_value ) ) {
				$term = get_term_by( 'id', $filter_value, $taxonomy );
			}

			/**
			 * If there's no match from above, maybe the user is searching slug to begin with or the slug is a number.
			 *
			 * The downside to this logic is that if a user is searching for a term by slug and it's numeric,
			 * it will return the term matching the ID rather than the slug.
			 */
			if ( ! $term ) {
				$term = get_term_by( 'slug', $filter_value, $taxonomy );
			}

			$filter_value = $term ? $term->term_taxonomy_id : - 1;

			$where = $wpdb->prepare( "(
	SELECT COUNT(1)
	FROM {$wpdb->term_relationships}
	WHERE {$wpdb->term_relationships}.term_taxonomy_id = %d
	AND {$wpdb->term_relationships}.object_id = {$wpdb->posts}.ID
) $operator 1", $filter_value );
		}

		$query_builder_args['where'][ $filter_group_index ][] = $where;

		return $query_builder_args;

	}

	public function maybe_add_post_status_where( $query_builder_args, $args ) {

		global $wpdb;

		/**
		 * @var $primary_property_value string
		 * @var $field_values array
		 * @var $filter_groups array
		 * @var $ordering array
		 */
		extract( $args );

		foreach ( $query_builder_args['where'] as $filter_group_index => $filter_group_wheres ) {

			$has_post_status = false;

			foreach ( $filter_group_wheres as $filter_group_where ) {
				if ( strpos( $filter_group_where, 'post_status' ) !== false ) {
					$has_post_status = true;
					break;
				}
			}

			/* Add post_status = 'publish' by default if there isn't a post status conditional in this group */
			if ( ! $has_post_status ) {
				$post_status_where = $this->build_where_clause( $wpdb->posts, 'post_status', 'is', 'publish' );
				$query_builder_args['where'][$filter_group_index][] = $post_status_where;
			}

		}

		return $query_builder_args;

	}

	public function label_transform_author( $value ) {

		$userdata = get_userdata( $value );

		return $userdata->display_name;

	}

	public function label_transform_post_type( $value ) {

		$post_type = get_post_type_object( $value );

		if ( ! $post_type ) {
			return $value;
		}

		return $post_type->labels->singular_name;

	}

	public function get_properties( $primary_property = null ) {

		global $wpdb;

		return array_merge( array(
			'post_author' => array(
				'label'    => esc_html__( 'Author', 'gp-populate-anything' ),
				'value'    => 'post_author',
				'callable' => array( $this, 'get_col_rows' ),
				'args'     => array( $wpdb->posts, 'post_author' ),
				'orderby'  => true,
			),
			'post_status' => array(
				'label'    => esc_html__( 'Post Status', 'gp-populate-anything' ),
				'value'    => 'post_status',
				'callable' => 'get_post_statuses',
				'orderby'  => true,
			),
			'post_title'  => array(
				'label'    => esc_html__( 'Post Title', 'gp-populate-anything' ),
				'value'    => 'post_title',
				'callable' => array( $this, 'get_col_rows' ),
				'args'     => array( $wpdb->posts, 'post_title' ),
				'orderby'  => true,
			),
			'post_content'  => array(
				'label'    => esc_html__( 'Post Content', 'gp-populate-anything' ),
				'value'    => 'post_content',
				'callable' => '__return_empty_array',
				'orderby'  => true,
			),
			'post_name'  => array(
				'label'    => esc_html__( 'Post Name (Slug)', 'gp-populate-anything' ),
				'value'    => 'post_name',
				'callable' => array( $this, 'get_col_rows' ),
				'args'     => array( $wpdb->posts, 'post_name' ),
				'orderby'  => true,
			),
			'ID'          => array(
				'label'    => esc_html__( 'Post ID', 'gp-populate-anything' ),
				'value'    => 'ID',
				'callable' => array( $this, 'get_col_rows' ),
				'args'     => array( $wpdb->posts, 'ID' ),
				'orderby'  => true,
			),
			'post_type'   => array(
				'label'    => esc_html__( 'Post Type', 'gp-populate-anything' ),
				'value'    => 'post_type',
				'callable' => array( $this, 'get_col_rows' ),
				'args'     => array( $wpdb->posts, 'post_type' ),
				'orderby'  => true,
			),
			'post_date'   => array(
				'label'    => esc_html__( 'Post Date', 'gp-populate-anything' ),
				'value'    => 'post_date',
				'callable' => array( $this, 'get_col_rows' ),
				'args'     => array( $wpdb->posts, 'post_date' ),
				'orderby'  => true,
			),
			'post_parent' => array(
				'label'    => esc_html__( 'Parent Post', 'gp-populate-anything' ),
				'value'    => 'post_parent',
				'callable' => array( $this, 'get_posts' ),
				'orderby'  => true,
			),
		), $this->get_post_taxonomies_properties(), $this->get_post_meta_properties() );

	}

	public function get_object_prop_value( $object, $prop ) {

		/* Taxonomies */
		if ( strpos( $prop, 'taxonomy_' ) === 0 ) {

			$taxonomy = preg_replace( '/^taxonomy_/', '', $prop );
			$terms    = wp_get_post_terms( $object->ID, $taxonomy, array( 'fields' => 'names' ) );

			return $terms;//implode( ',', $terms );

		}

		/* Meta and all other props */
		$prop = preg_replace( '/^meta_/', '', $prop );

		if ( ! isset ( $object->{$prop} ) ) {
			return null;
		}

		return $object->{$prop};

	}

	public function get_posts() {
		global $wpdb;

		$result = wp_list_pluck( $wpdb->get_results( "SELECT DISTINCT ID, post_title FROM $wpdb->posts" ), 'post_title', 'ID' );

		natcasesort( $result );

		return array_filter( $result );
	}

	public function get_post_meta_properties() {

		global $wpdb;

		$post_meta_properties = array();

		foreach ( $this->get_col_rows( $wpdb->postmeta, 'meta_key' ) as $post_meta_key ) {
			$post_meta_properties[ 'meta_' . $post_meta_key ] = array(
				'label'    => $post_meta_key,
				'value'    => $post_meta_key,
				'callable' => array( $this, 'get_meta_values' ),
				'args'     => array( $post_meta_key, $wpdb->postmeta ),
				'group'    => 'meta',
			);
		}

		return $post_meta_properties;

	}

	public function get_post_taxonomies_properties() {

		$taxonomy_properties = array();
		$taxonomy_query_args = array();

		foreach ( get_taxonomies( $taxonomy_query_args, 'objects' ) as $taxonomy ) {
			$taxonomy_properties[ 'taxonomy_' . $taxonomy->name ] = array(
				'label'    => $taxonomy->labels->singular_name,
				'value'    => $taxonomy->name,
				'group'    => 'taxonomies',
				'callable' => array( $this, 'get_taxonomy_terms' ),
			);
		}

		return $taxonomy_properties;

	}

	public function get_taxonomy_terms() {
		$taxonomy = rgar( $_POST, 'property' );
		$taxonomy = preg_replace( '/^taxonomy_/', '', $taxonomy );

		$terms = get_terms( array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false
		) );

		return array_map( function ( $term ) {
			return array( $term->term_id, $term->name );
		}, $terms );
	}

	public function default_query_args ( $args ) {

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
		$order = rgar( $ordering, 'order', 'ASC' );

		return array(
			'select'   => "{$wpdb->posts}.*",
			'from'     => $wpdb->posts,
			'where'    => array(),
			'joins'    => array(),
			'group_by' => "{$wpdb->posts}.ID",
			'order_by' => $orderby ? "{$wpdb->posts}.{$orderby}" : null,
			'order'    => $order,
		);

	}

	public function query( $args ) {
		$query_args = $this->process_filter_groups( $args, $this->default_query_args( $args ) );

		$query = new WP_Query();

		/* $self is required for PHP 5.3 compatibility. */
		$self = $this;

		/* Reset meta query counter */
		$this->meta_query_counter = 0;

		$func = function () use ( $query_args, $self, $args ) {
			return $self->build_mysql_query( $query_args, rgar( $args, 'field' ) );
		};

		add_filter( 'posts_request', $func, 9 );
		$results = $query->get_posts();
		remove_filter( 'posts_request', $func, 9 );

		return $results;
	}


}
