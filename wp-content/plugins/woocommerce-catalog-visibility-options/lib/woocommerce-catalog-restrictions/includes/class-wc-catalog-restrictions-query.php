<?php

class WC_Catalog_Restrictions_Query {

	private static $instance;

	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new WC_Catalog_Restrictions_Query();
		}

		return self::$instance;
	}

	private $table_term_meta;
	private $term_meta_id_field;

	public function __construct() {
		add_filter( 'posts_where', array( $this, 'posts_where' ), 10, 2 );
		add_filter('woocommerce_product_related_posts_query', array($this, 'related_posts_query_where'),  10, 1);
		/**
		 * @since 2.5.8
		 */
		add_filter( 'get_terms_args', array( $this, 'taxonomy_filter' ), 10, 2 );

		add_action( 'woocommerce_loaded', array( $this, 'on_woocommerce_loaded' ) );

		add_filter( 'woocommerce_get_grouped_children', array( $this, 'filter_grouped_children' ), 10, 2 );

		add_filter( 'woocommerce_shortcode_products_query', array( $this, 'filter_shortcode_query' ), 10, 3 );

		add_filter( 'woocommerce_advanced_search_get_post_ids', array(
			$this,
			'filter_advanced_search_results'
		), 10, 1 );

	}

	public function get_table_term_meta() {
		global $wpdb;
		if ( WC_Catalog_Visibility_Compatibility::use_wp_term_meta_table() ) {
			return $wpdb->termmeta;
		} else {
			return $wpdb->prefix . 'woocommerce_termmeta';
		}
	}

	public function get_term_meta_id_field() {
		global $wpdb;
		if ( WC_Catalog_Visibility_Compatibility::use_wp_term_meta_table() ) {
			return 'term_id';
		} else {
			return 'woocommerce_term_id';
		}
	}


	public function on_woocommerce_loaded() {
		global $wpdb;
		if ( WC_Catalog_Visibility_Compatibility::use_wp_term_meta_table() ) {
			//Clean up old rules. 
			$this->table_term_meta    = $wpdb->termmeta;
			$this->term_meta_id_field = 'term_id';
		} else {
			//Clean up old rules. 
			$this->table_term_meta    = $wpdb->prefix . 'woocommerce_termmeta';
			$this->term_meta_id_field = 'woocommerce_term_id';
		}
	}

	public function is_bound_to_query( $query ) {
		if ( did_action( 'woocommerce_init' ) ) {
			$post_types = $query->get( 'post_type' );

			return ( empty( $post_types ) || $post_types == 'product' || $post_types == 'any' || ( is_array( $post_types ) && ( in_array( 'product', $post_types ) || in_array( 'any', $post_types ) ) ) );
		} else {
			return false;
		}
	}

	public function filter_shortcode_query( $query_args, $shortcode_atts, $loop_name ) {
		$transient_name = 'wc_loop' . substr( md5( json_encode( $query_args ) . $loop_name ), 28 ) . WC_Cache_Helper::get_transient_version( 'product_query' );
		delete_transient( $transient_name );

		return $query_args;
	}

	public function filter_advanced_search_results( $product_ids ) {
		$disallowed_products = $this->get_disallowed_products();

		$allowed_products = array_diff( $product_ids, $disallowed_products );

		return $allowed_products;

	}

	/**
	 * Filters out grouped product children.   Since 2.8.5
	 */
	public function filter_grouped_children( $children, $product ) {
		$filtered = $this->get_disallowed_products();
		$allowed  = array();
		if ( ! empty( $filtered ) ) {
			foreach ( $children as $child ) {
				if ( ! in_array( $child, $filtered ) ) {
					$allowed[] = $child;
				}
			}
		} else {
			$allowed = $children;
		}

		return $allowed;
	}

	/**
	 * Filters out product categories the user does not have access to.
	 *
	 * @since 2.5.8
	 * @global type $wpdb
	 *
	 * @param array $args
	 * @param array $taxonomies
	 *
	 * @return array
	 */
	public function taxonomy_filter( $args, $taxonomies ) {
		global $wpdb;

		if ( in_array( 'product_cat', $taxonomies ) ) {

			$disallowed = $this->get_cached_exclusions( 'cat' );
			if ( $disallowed === false ) {
				$user_roles = $this->get_roles_for_current_user();
				$sql        = " SELECT term_id FROM {$wpdb->prefix}term_taxonomy WHERE taxonomy = 'product_cat' AND term_id NOT IN (
				SELECT term_id FROM {$wpdb->prefix}term_taxonomy WHERE taxonomy = 'product_cat' AND term_id NOT IN (SELECT {$this->get_table_term_meta()}.{$this->get_term_meta_id_field()} FROM {$this->get_table_term_meta()} WHERE meta_key = '_wc_restrictions')
				UNION ALL 
				SELECT {$this->get_table_term_meta()}.{$this->get_term_meta_id_field()} FROM {$this->get_table_term_meta()} WHERE ( (meta_key ='_wc_restrictions' AND meta_value = 'public') OR (meta_key = '_wc_restrictions_allowed' AND meta_value IN ('" . implode( "','", $user_roles ) . "') ) ) )";

				$disallowed = $wpdb->get_col( $sql );

				if ( $disallowed !== false ) {
					$cache_key = $this->get_cache_key_for_current_user( false );
					set_transient( 'twccr_' . $cache_key . '_cat', $disallowed, 60 * 60 * 24 );
				}
			}

			if ( $disallowed && count( $disallowed ) ) {
				$disallowed = array_map( 'intval', $disallowed );
				if ( isset( $args['include'] ) && ! empty( $args['include'] ) ) {
					$include = wp_parse_id_list( $args['include'] );
					$allowed = array_filter( array_diff( $include, $disallowed ) );
					if ( empty( $allowed ) ) {
						$args['include'] = array();
						$args['exclude'] = $disallowed;
					} else {
						$args['include'] = $allowed;
					}
				} else {
					$exclude         = wp_parse_id_list( $args['exclude'] );
					$args['exclude'] = isset( $exclude ) && ! empty( $exclude ) ? array_merge( $exclude, $disallowed ) : $disallowed;
				}
			}
		} else {

		}

		return $args;
	}

	public function related_posts_query_where($query) {
		global $wpdb;

		$filtered = $this->get_disallowed_products();

		if ( $filtered ) {
			if ( wc_cvo_restrictions()->use_db_filter_cache ) {
				if ( wc_cvo_restrictions()->get_setting( '_wc_restrictions_locations_enabled', 'no' ) == 'yes' ) {
					$key = $this->get_cache_key_for_current_user( true );
				} else {
					$key = $this->get_cache_key_for_current_user( false );
				}
				$table  = $wpdb->prefix . 'wc_cvo_cache';
				$filter = $wpdb->prepare( "SELECT product_id FROM $table WHERE cache_key = %s", $key );
				$query['where']  .= " AND ID NOT IN ($filter)";
			} else {
				$query['where'] .= " AND ID NOT IN ('" . implode( "','", $filtered ) . "')";
			}
		}

		return $query;
	}

	public function posts_where( $where, $query ) {
		global $wpdb;

		if ( $this->is_bound_to_query( $query ) ) {
			/*
			 * These queries work by first finding all products that do not have any rules applied.  It looks at both the 
			 * category and the product meta to determine if there are any rules present. 
			 * 
			 * Second the query finds all products that have a restriction that is not set to public or where the meta_value for the assigned roles / locations
			 * is in the values we pass in. 
			 * 
			 * Third the query finds all proudcts which do not have specific product rules, because product rules override category rules, and filters where the
			 * taxonomy meta is in the list of values we pass in. 
			 * 
			 * Finally we modify the where statement of the main query to include only the product ID's we found. 
			 */

			$filtered = $this->get_disallowed_products();

			if ( $filtered ) {
				if ( wc_cvo_restrictions()->use_db_filter_cache ) {
					if ( wc_cvo_restrictions()->get_setting( '_wc_restrictions_locations_enabled', 'no' ) == 'yes' ) {
						$key = $this->get_cache_key_for_current_user( true );
					} else {
						$key = $this->get_cache_key_for_current_user( false );
					}
					$table  = $wpdb->prefix . 'wc_cvo_cache';
					$filter = $wpdb->prepare( "SELECT product_id FROM $table WHERE cache_key = %s", $key );
					$where  .= " AND $wpdb->posts.ID NOT IN ($filter)";
				} else {
					$where .= " AND $wpdb->posts.ID NOT IN ('" . implode( "','", $filtered ) . "')";
				}
			}
		}

		return $where;
	}

	public function get_cached_exclusions( $type, $session_id = false ) {

		if ( $session_id === false ) {
			$session_id = $this->get_cache_key_for_current_user( false );
		}

		return get_transient( 'twccr_' . $session_id . '_' . $type );
	}

	public function get_roles_for_current_user() {
		$roles = array( 'guest' => 'guest' );

		if ( is_user_logged_in() ) {
			$user = new WP_User( get_current_user_id() );
			if ( ! empty( $user->roles ) && is_array( $user->roles ) ) {
				foreach ( $user->roles as $role ) {
					$roles[ $role ] = $role;
				}
			}
		}

		return apply_filters( 'woocommerce_catalog_restrictions_get_roles_for_current_user', $roles );
	}

	public function get_cache_key_for_current_user( $use_session = false ) {
		$session_id = '';
		if ( $use_session === false ) {
			//Get a key based on role, since all rules use roles.  
			$roles = $this->get_roles_for_current_user();
			if ( ! empty( $roles ) ) {
				$session_id = implode( '', $roles );
			} else {
				$session_id = 'norole';
			}
		} else {
			//Use session will be true for location filters. 
			//Location filters will have already started a woocommerce session so this value is good. 
			$session_id = WC_Catalog_Visibility_Compatibility::WC()->session->get_customer_id();
		}

		return $session_id;
	}

	public function get_locations_for_current_user() {
		global $wc_catalog_restrictions;
		$t_loc = $wc_catalog_restrictions->get_location_for_current_user();

		if ( ! is_array( $t_loc ) ) {
			$t_loc = (array) $t_loc;
		}

		$locations = array();
		foreach ( $t_loc as &$location ) {
			$location = esc_sql( $location );
			if ( strstr( $location, ':' ) ) {
				$parts = explode( ':', $location );
				foreach ( $parts as $part ) {
					$locations[] = $part;
				}
			} else {
				$locations[] = $location;
			}
		}

		$locations = apply_filters( 'woocommerce_catalog_restrictions_get_users_location_query', $locations );
		foreach ( $locations as $location ) {
			$location = empty( $location ) ? '-1' : esc_sql( $location );
		}

		return $locations;
	}

	public function query_allowed_product_ids_by_role( $pre_filtered_products = false ) {
		global $wpdb;
		$user_roles = $this->get_roles_for_current_user();
		$role_sql   = "
        SELECT DISTINCT $wpdb->posts.ID FROM $wpdb->posts INNER JOIN (
            SELECT DISTINCT post_id FROM $wpdb->postmeta 
                    WHERE post_id NOT IN (SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_wc_restrictions')
                    AND post_id NOT IN(SELECT DISTINCT tr.object_id FROM {$this->get_table_term_meta()} 
                        INNER JOIN $wpdb->term_taxonomy tt on {$this->get_table_term_meta()}.{$this->get_term_meta_id_field()} = tt.term_id
                        INNER JOIN $wpdb->term_relationships tr on tt.term_taxonomy_id = tr.term_taxonomy_id
                        WHERE tt.taxonomy = 'product_cat' AND meta_key='_wc_restrictions')
                UNION ALL
            SELECT  post_id FROM $wpdb->postmeta 
                    WHERE (meta_key = '_wc_restrictions' AND meta_value = 'public') OR (meta_key = '_wc_restrictions_allowed' AND meta_value IN ('" . implode( "','", $user_roles ) . "'))
                UNION ALL 
            SELECT  tr.object_id FROM {$this->get_table_term_meta()} 
                    INNER JOIN $wpdb->term_taxonomy tt on {$this->get_table_term_meta()}.{$this->get_term_meta_id_field()} = tt.term_id
                    INNER JOIN $wpdb->term_relationships tr on tt.term_taxonomy_id = tr.term_taxonomy_id
                    WHERE tt.taxonomy = 'product_cat' 
                    AND ( (meta_key='_wc_restrictions' AND meta_value='public') OR (meta_key = '_wc_restrictions_allowed' AND meta_value IN ('" . implode( "','", $user_roles ) . "')) )
                    AND tr.object_id NOT IN (SELECT DISTINCT post_id FROM $wpdb->postmeta WHERE meta_key = '_wc_restrictions')
            ) as rfilter on $wpdb->posts.ID = rfilter.post_id WHERE post_type = 'product'";


		if ( $pre_filtered_products ) {
			$role_sql .= " AND ID IN (" . implode( ',', $pre_filtered_products ) . ")";
		}

		$allowed = $wpdb->get_col( $role_sql );

		return ! empty( $allowed ) ? $allowed : array( - 1 );
	}

	public function query_allowed_product_ids_by_location( $pre_filtered_products = false ) {
		global $wpdb;
		$user_locations = $this->get_locations_for_current_user();

		$location_sql = "SELECT DISTINCT $wpdb->posts.ID FROM $wpdb->posts INNER JOIN (
            SELECT post_id FROM $wpdb->postmeta 
                    WHERE post_id NOT IN (SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_wc_restrictions_location')
                    AND post_id NOT IN(SELECT DISTINCT tr.object_id FROM {$this->get_table_term_meta()} 
                        INNER JOIN $wpdb->term_taxonomy tt on {$this->get_table_term_meta()}.{$this->get_term_meta_id_field()} = tt.term_id
                        INNER JOIN $wpdb->term_relationships tr on tt.term_taxonomy_id = tr.term_taxonomy_id
                        WHERE tt.taxonomy = 'product_cat' AND meta_key='_wc_restrictions_location')
                UNION ALL
            SELECT post_id FROM $wpdb->postmeta 
                    WHERE (meta_key = '_wc_restrictions_location' AND meta_value = 'public') OR (meta_key = '_wc_restrictions_locations' AND meta_value IN ('" . implode( "','", $user_locations ) . "'))
                UNION ALL 
            SELECT tr.object_id FROM {$this->get_table_term_meta()} 
                    INNER JOIN $wpdb->term_taxonomy tt on {$this->get_table_term_meta()}.{$this->get_term_meta_id_field()} = tt.term_id
                    INNER JOIN $wpdb->term_relationships tr on tt.term_taxonomy_id = tr.term_taxonomy_id
                    WHERE tt.taxonomy = 'product_cat' 
                    AND ( (meta_key='_wc_restrictions_location' AND meta_value='public') OR (meta_key = '_wc_restrictions_locations' AND meta_value IN ('" . implode( "','", $user_locations ) . "')) )
                    AND tr.object_id NOT IN (SELECT DISTINCT post_id FROM $wpdb->postmeta WHERE meta_key = '_wc_restrictions_location')
        ) as rfilter on $wpdb->posts.ID = rfilter.post_id WHERE post_type = 'product'";


		if ( $pre_filtered_products ) {
			$location_sql .= " AND ID IN (" . implode( ',', $pre_filtered_products ) . ")";
		}

		$allowed = $wpdb->get_col( $location_sql );

		return ! empty( $allowed ) ? $allowed : array( - 1 );
	}


	public function get_disallowed_products() {
		global $wc_catalog_restrictions, $wpdb;

		$filtered = false;
		if ( $wc_catalog_restrictions->get_setting( '_wc_restrictions_locations_enabled', 'no' ) == 'yes' ) {
			$filtered = $this->get_cached_exclusions( 'l' );
			if ( $filtered === false ) {
				$cache_key = $this->get_cache_key_for_current_user( true ); //Pass true to get the actual session id.
				if ( wc_cvo_restrictions()->use_db_filter_cache ) {
					$table = $wpdb->prefix . 'wc_cvo_cache';
					$wpdb->query( $wpdb->prepare( "DELETE FROM $table WHERE cache_key = %s", $cache_key ) );
				}

				$allowed_by_role     = $this->query_allowed_product_ids_by_role();
				$allowed_by_location = $this->query_allowed_product_ids_by_location( $allowed_by_role );

				if ( $allowed_by_location ) {
					$exclusion_sql = " SELECT ID FROM $wpdb->posts WHERE post_type='product' AND $wpdb->posts.ID NOT IN ('" . implode( "','", $allowed_by_location ) . "')";
					$filtered      = $wpdb->get_col( $exclusion_sql );
				}

				if ( $filtered !== false ) {
					set_transient( 'twccr_' . $cache_key . '_l', $filtered, 60 * 60 * 24 );
					if ( wc_cvo_restrictions()->use_db_filter_cache ) {
						$table = $wpdb->prefix . 'wc_cvo_cache';
						foreach ( $filtered as $item ) {
							$wpdb->insert( $table, array(
								'cache_key'  => $cache_key,
								'product_id' => $item
							), array(
								'%s',
								'%s'
							) );
						}
					}
				}
			}
		} else {
			$filtered = $this->get_cached_exclusions( 'r' );
			if ( $filtered === false ) {
				$cache_key = $this->get_cache_key_for_current_user( false );
				if ( wc_cvo_restrictions()->use_db_filter_cache ) {
					$table = $wpdb->prefix . 'wc_cvo_cache';
					$wpdb->query( $wpdb->prepare( "DELETE FROM $table WHERE cache_key = %s", $cache_key ) );
				}

				$allowed_by_role = $this->query_allowed_product_ids_by_role();

				if ( $allowed_by_role ) {
					$exclusion_sql = " SELECT ID FROM $wpdb->posts WHERE post_type='product' AND $wpdb->posts.ID NOT IN ('" . implode( "','", $allowed_by_role ) . "')";
					$filtered      = $wpdb->get_col( $exclusion_sql );
				}

				if ( $filtered !== false ) {
					set_transient( 'twccr_' . $cache_key . '_r', $filtered, 60 * 60 * 24 );
					if ( wc_cvo_restrictions()->use_db_filter_cache ) {
						$table = $wpdb->prefix . 'wc_cvo_cache';
						foreach ( $filtered as $item ) {
							$wpdb->insert( $table, array(
								'cache_key'  => $cache_key,
								'product_id' => $item
							), array(
								'%s',
								'%s'
							) );
						}
					}
				}
			}
		}

		return $filtered;
	}

}
