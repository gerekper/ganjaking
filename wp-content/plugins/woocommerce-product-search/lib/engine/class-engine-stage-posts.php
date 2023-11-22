<?php
/**
 * class-engine-stage-posts.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 5.0.0
 */

namespace com\itthinx\woocommerce\search\engine;

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class Engine_Stage_Posts extends Engine_Stage {

	const CACHE_GROUP = 'ixwps_scriptum';

	const CACHE_LIFETIME = Cache::UNLIMITED;

	protected $stage_id = 'posts';

	private $status = 'publish';

	private $order = null;

	private $orderby = null;

	private $page = null;

	private $per_page = -1;

	private $offset = null;

	public function __construct( $args = array() ) {
		$args = apply_filters( 'woocommerce_product_search_engine_stage_parameters', $args, $this );
		parent::__construct( $args );
		if ( is_array( $args ) && count( $args ) > 0 ) {
			$params = array();
			foreach ( $args as $key => $value ) {
				$set_param = true;
				switch ( $key ) {
					case 'status':

						if ( is_string( $value ) ) {
							$values = array( $value );
						} else if ( is_array( $value ) ) {
							$values = $value;
						} else {

							$values = array();
						}

						$statuses = array();
						foreach ( $values as $status ) {
							switch ( $status ) {
								case 'any':
								case 'publish':
								case 'pending':
								case 'draft':
								case 'private':
								case 'future':
								case 'trash':
								case 'auto-draft':
								case 'inherit':
									break;
								default:

									$post_stati = get_post_stati();
									if ( is_array( $post_stati ) && in_array( $status, $post_stati, true ) ) {
									} else {
										$status = null;
									}
							}
							if ( $status !== null && !in_array( $status, $statuses ) ) {
								$statuses[] = $status;
							}
						}

						if ( count( $statuses ) > 0 ) {

							if ( count( $statuses ) === 1 ) {
								$value = array_pop( $statuses );
							} else {
								$value = $statuses;
							}
						} else {
							$set_param = false;
						}
						break;
					case 'order':
						if ( $value !== null ) {
							$value = sanitize_text_field( trim( $value ) );
						}
						switch ( $value ) {
							case 'asc':
							case 'desc':
								break;
							default:
								$value = null;
						}
						break;
					case 'orderby':
						if ( $value !== null ) {
							$value = sanitize_text_field( trim( $value ) );
						}
						switch ( $value ) {

							case null:
							case 'date':
							case 'id':
							case 'menu_order':
							case 'modified':
							case 'name':
							case 'price':
							case 'price-desc':
							case 'popularity':
							case 'rand':
							case 'rating':
							case 'relevance':
							case 'sku':
							case 'slug':
							case 'title':
								break;
							default:

								$value = sanitize_text_field( trim( $value ) );
								$value = preg_replace( '/[^a-zA-Z0-9_-]/', '', $value );
								if ( is_string( $value ) ) {
									$value = trim( $value );
									if ( strlen( $value ) === 0 ) {
										$value = null;
									}
								} else {
									$value = null;
								}
						}
						break;
					case 'page':
						if ( is_numeric( $value ) ) {
							$value = intval( $value );
							if ( $value < 1 ) {
								$value = null;
							}
						} else {
							$value = null;
						}
						break;
					case 'per_page':
						if ( is_numeric( $value ) ) {
							$value = intval( $value );
							if ( $value < -1 ) {
								$value = -1;
							}
						} else {
							$value = null;
						}
						break;
					case 'offset':
						if ( is_numeric( $value ) ) {
							$value = intval( $value );
							if ( $value < 0 ) {
								$value = null;
							}
						} else {
							$value = null;
						}
						break;
					default:
						$set_param = false;
				}
				if ( $set_param ) {
					$params[$key] = $value;
				}
			}
			foreach ( $params as $key => $value ) {
				$this->$key = $value;
			}
		}
	}

	public function get_parameters() {
		return array_merge(
			array(
				'status'   => $this->status,
				'order'    => $this->order,
				'orderby'  => $this->orderby,
				'page'     => $this->page,
				'per_page' => $this->per_page,
				'offset'   => $this->offset
			),
			parent::get_parameters()
		);
	}

	public function get_matching_ids( &$ids ) {

		$this->timer->start();

		$cache_context = $this->get_parameters();
		$cache_key = $this->get_cache_key( $cache_context );

		$cache = Cache::get_instance();
		$ids = $cache->get( $cache_key, self::CACHE_GROUP );
		if ( is_array( $ids ) ) {
			$this->count = count( $ids );
			$this->is_cache_hit = true;
			$this->timer->stop();
			$this->timer->log( 'verbose' );
			return;
		}
		$this->is_cache_hit = false;

		$filter_posts_clauses = false;

		$args = array();
		if ( $this->status !== null ) {
			$args['post_status'] = $this->status;
		}
		if ( $this->orderby !== null ) {

			$orderby = $this->orderby;
			switch ( $this->orderby ) {
				case 'id':
				case 'ID':
					$orderby = 'ID';
					if ( $this->order === null ) {
						$this->order = 'asc';
					}
					break;
				case 'menu_order':
					$orderby = 'menu_order title';
					if ( $this->order === null ) {
						$this->order = 'asc';
					}
					break;
				case 'name':
				case 'title':
					$orderby = 'title';
					if ( $this->order === null ) {
						$this->order = 'asc';
					}
					break;
				case 'relevance':
					$orderby = 'relevance';
					if ( $this->order === null ) {
						$this->order = 'desc';
					}
					break;
				case 'date':
					$orderby = 'date ID';
					if ( $this->order === null ) {
						$this->order = 'desc';
					}
					break;
				case 'modified':
					$orderby = 'modified ID';
					if ( $this->order === null ) {
						$this->order = 'desc';
					}
					break;
				case 'price':
					$filter_posts_clauses = true;
					if ( $this->order === null ) {
						$this->order = 'asc';
					}
					break;
				case 'popularity':
					$filter_posts_clauses = true;
					if ( $this->order === null ) {
						$this->order = 'desc';
					}
					break;
				case 'rating':
					$filter_posts_clauses = true;
					if ( $this->order === null ) {
						$this->order = 'desc';
					}
					break;
				case 'sku':
					$filter_posts_clauses = true;
					if ( $this->order === null ) {
						$this->order = 'asc';
					}
					break;
				case 'slug':
					$orderby = 'post_name';
					if ( $this->order === null ) {
						$this->order = 'asc';
					}
					break;
				default:
					$orderby = null;
			}
			if ( $orderby !== null ) {
				$args['orderby'] = $orderby;
			}
		}
		if ( $this->order !== null ) {
			$order = strtoupper( $this->order );
			switch ( $order ) {
				case 'ASC':
				case 'DESC':
					$args['order'] = $order;
					break;
			}
		}
		if ( $this->page !== null ) {
			$args['paged'] = $this->page;
		}
		if ( $this->per_page !== null ) {
			$args['posts_per_page'] = $this->per_page;
		}
		if ( $this->offset !== null ) {
			$args['offset'] = $this->offset;
		}
		if ( $this->limit !== null ) {
			$args['posts_per_page'] = $this->limit;
		}

		if ( apply_filters( 'woocommerce_product_search_engine_stage_posts_use_wp_query', false, $this ) ) {

			$base_args = array(
				'fields'              => 'ids',
				'ignore_sticky_posts' => true,
				'orderby'             => 'none',
				'post_type'           => $this->variations ? array( 'product', 'product_variation' ) : 'product',
				'posts_per_page'      => -1,
				'post_status'         => 'publish',
				'suppress_filters'    => false,
				'no_found_rows'       => true

			);
			$args = array_merge( $base_args, $args );

			if ( $filter_posts_clauses ) {
				add_filter( 'posts_clauses', array( $this, 'posts_clauses' ) );
			}

			$query = new \WP_Query();
			$ids = $query->query( $args );
			if ( is_array( $ids ) && count( $ids ) > 0 ) {
				Tools::int( $ids );
			} else {
				$ids = array();
			}

			if ( $filter_posts_clauses ) {
				remove_filter( 'posts_clauses', array( $this, 'posts_clauses' ) );
			}

		} else {

			$base_args = array(
				'return'           => 'ids',
				'orderby'          => 'none',
				'post_type'        => $this->variations ? array( 'product', 'product_variation' ) : 'product',
				'posts_per_page'   => -1,
				'status'           => 'publish',
				'suppress_filters' => false,
				'no_found_rows'    => true
			);
			if ( isset( $args['posts_per_page'] ) ) {
				$args['limit'] = $args['posts_per_page'];
			}
			if ( isset( $args['paged'] ) ) {
				$args['page'] = $args['paged'];
			}
			if ( isset( $args['post_status'] ) ) {
				$args['status'] = $args['post_status'];
			}
			$args = array_merge( $base_args, $args );

			if ( $filter_posts_clauses ) {
				add_filter( 'posts_clauses', array( $this, 'posts_clauses' ) );
			}

			if ( $this->variations ) {

				add_filter( 'woocommerce_product_data_store_cpt_get_products_query', array( $this, 'woocommerce_product_data_store_cpt_get_products_query' ), 10, 3 );
			}

			$query = new \WC_Product_Query( $args );
			$ids = $query->get_products();
			if ( is_array( $ids ) && count( $ids ) > 0 ) {
				Tools::int( $ids );
			} else {
				$ids = array();
			}

			if ( $this->variations ) {
				remove_filter( 'woocommerce_product_data_store_cpt_get_products_query', array( $this, 'woocommerce_product_data_store_cpt_get_products_query' ), 10 );
			}

			if ( $filter_posts_clauses ) {
				remove_filter( 'posts_clauses', array( $this, 'posts_clauses' ) );
			}

		}

		$this->count = count( $ids );

		$this->is_cache_write = $cache->set( $cache_key, $ids, self::CACHE_GROUP, $this->get_cache_lifetime() );

		$this->timer->stop();
		$this->timer->log( 'verbose' );
	}

	/**
	 * Filter query parameters.
	 *
	 * @param array $wp_query_args
	 * @param array $query_vars
	 * @param \WC_Product_Data_Store_CPT $data_store
	 *
	 * @return array
	 */
	public function woocommerce_product_data_store_cpt_get_products_query( $wp_query_args, $query_vars, $data_store ) {

		$wp_query_args['post_type'] = array( 'product', 'product_variation' );
		unset( $wp_query_args['tax_query'] );
		return $wp_query_args;
	}

	public function is_sorting() {
		return $this->orderby !== null && $this->order !== null;
	}

	/**
	 * Filter posts_clauses.
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function posts_clauses( $args ) {

		global $wpdb;

		$join_suffix = '';

		switch ( $this->orderby ) {
			case 'price':
			case 'popularity':
			case 'rating':
			case 'sku':
				if ( !empty( $args['join'] ) ) {
					$join_suffix = ' ' . $args['join'];
				}

				break;
		}
		switch ( $this->orderby ) {
			case 'price':
				$args['join'] = " LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON $wpdb->posts.ID = wc_product_meta_lookup.product_id " . $join_suffix;
				switch ( $this->order ) {
					case 'desc':
						$args['orderby'] = ' wc_product_meta_lookup.max_price DESC, wc_product_meta_lookup.product_id DESC ';
						break;
					default:
						$args['orderby'] = ' wc_product_meta_lookup.min_price ASC, wc_product_meta_lookup.product_id ASC ';
				}
				break;
			case 'popularity':
				$args['join'] = " LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON $wpdb->posts.ID = wc_product_meta_lookup.product_id " . $join_suffix;
				switch( $this->order ) {
					case 'asc':
						$args['orderby'] = ' wc_product_meta_lookup.total_sales ASC, wc_product_meta_lookup.product_id DESC ';
						break;
					default:
						$args['orderby'] = ' wc_product_meta_lookup.total_sales DESC, wc_product_meta_lookup.product_id DESC ';
				}
				break;
			case 'rating':
				$args['join'] = " LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON $wpdb->posts.ID = wc_product_meta_lookup.product_id " . $join_suffix;
				switch ( $this->order ) {
					case 'asc':
						$args['orderby'] = ' wc_product_meta_lookup.average_rating ASC, wc_product_meta_lookup.rating_count DESC, wc_product_meta_lookup.product_id DESC ';
						break;
					default:
						$args['orderby'] = ' wc_product_meta_lookup.average_rating DESC, wc_product_meta_lookup.rating_count DESC, wc_product_meta_lookup.product_id DESC ';
				}
				break;
			case 'sku':
				$args['join'] = " LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON $wpdb->posts.ID = wc_product_meta_lookup.product_id " . $join_suffix;
				switch ( $this->order ) {
					case 'desc':
						$args['orderby'] = ' wc_product_meta_lookup.sku DESC, wc_product_meta_lookup.product_id DESC ';
						break;
					default:
						$args['orderby'] = ' wc_product_meta_lookup.sku ASC, wc_product_meta_lookup.product_id ASC ';
				}
				break;
		}
		return $args;
	}

}
