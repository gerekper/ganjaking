<?php
/**
 * class-woocommerce-product-search-term-node.php
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
 * @since 2.1.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Term node.
 */
class WooCommerce_Product_Search_Term_Node {

	const CACHE_GROUP = 'ixwpsnode';

	/**
	 * Node's taxonomy
	 *
	 * @var string
	 */
	private $taxonomy = null;

	/**
	 * Index of nodes
	 *
	 * @var array array[WooCommerce_Product_Search_Term_node]
	 */
	private $index = null;

	/**
	 * Node's term ID
	 *
	 * @var int
	 */
	private $term_id = null;

	/**
	 * Node's parent term ID
	 *
	 * @var int
	 */
	private $parent_term_id = null;

	/**
	 * Parent node
	 *
	 * @var WooCommerce_Product_Search_Term_Node
	 */
	private $parent = null;

	/**
	 * Child nodes
	 *
	 * @var array[WooCommerce_Product_Search_Term_Node]
	 */
	private $children = array();

	/**
	 * Order by
	 *
	 * @var string
	 */
	private $orderby = 'name';

	/**
	 * Order (asc/desc)
	 *
	 * @var string
	 */
	private $order = 'ASC';

	/**
	 * Term index
	 *
	 * @var array[WP_Term]
	 */
	private $terms = null;


	public function __construct( $term_ids, $taxonomy, $options = null, &$index = null, &$terms = null ) {

		if ( $index === null ) {
			$this->index = array();
			$index = &$this->index;
		} else {
			$this->index = &$index;
		}

		$this->taxonomy = $taxonomy;

		if ( $terms === null ) {

			$this->terms = array();
			$_terms = get_terms( array( 'taxonomy' => $taxonomy, 'include' => $term_ids ) );
			if ( is_array( $_terms ) ) {
				foreach ( $_terms as $term ) {
					$this->terms[$term->term_id] = $term;

				}
			}
			unset( $_terms );
			$terms = &$this->terms;
		} else {
			$this->terms = &$terms;
		}

		if ( is_array( $term_ids ) ) {
			foreach ( $term_ids as $term_id ) {
				$term_id = intval( $term_id );
				try {
					if ( key_exists( $term_id, $this->index ) ) {
						$node = $this->index[$term_id];
					} else {
						$node = new WooCommerce_Product_Search_Term_Node( $term_id, $taxonomy, $options, $this->index, $this->terms );
					}
					$this->attach( $node->get_ancestor() );
				} catch ( Exception $e ) {
					wps_log_warning( $e->getMessage() );
				}
			}

			foreach ( $this->index as $term_id => $node ) {
				if (
					$node->parent_term_id !== null &&
					( $node->parent === null || $node->parent !== null && $node->parent->term_id === null )
				) {
					if ( key_exists( $node->parent_term_id, $this->index ) ) {
						if ( $node->parent !== null ) {
							$node->parent->detach( $node );
						}
						$this->index[$node->parent_term_id]->attach( $node );
					}
				}
			}

			if ( isset( $options['hide_empty'] ) && $options['hide_empty'] ) {
				$this->prune_empty();
			}

		} else {
			if ( $term_ids !== null && is_numeric( $term_ids ) ) {
				$term_id = intval( $term_ids );
				$term = $this->get_term( $term_id );
				if ( ( $term !== null ) && !( $term instanceof WP_Error ) ) {
					$this->term_id = $term->term_id;

					if ( isset( $term->parent ) && $term->parent !== null && $term->parent !== 0 ) {
						$this->parent_term_id = $term->parent;
					}

					$_options = $options;
					$_options['bubble_down'] = false; 
					$bubble_up = !isset( $options['bubble_up'] ) || $options['bubble_up'];
					$bubble_up_levels = isset( $options['bubble_up_levels'] ) ? $options['bubble_up_levels'] - 1 : null;
					if ( $bubble_up_levels !== null ) {
						$_options['bubble_up_levels'] = $bubble_up_levels;
					}

					if ( $bubble_up && ( $bubble_up_levels === null || $bubble_up_levels >= 0 ) ) {
						if ( !empty( $term->parent ) ) {
							if ( key_exists( $term->parent, $this->index ) ) {
								$this->index[$term->parent]->attach( $this );
							} else {
								try {
									$parent = new WooCommerce_Product_Search_Term_Node(
										$term->parent,
										$taxonomy,
										$_options,
										$this->index,
										$this->terms
									);
									$parent->attach( $this );
								} catch( Exception $e ) {
									wps_log_warning( $e->getMessage() );
								}
							}
						}
					}

					$_options = $options;
					$_options['bubble_up'] = false; 
					$bubble_down = !isset( $options['bubble_down'] ) || $options['bubble_down'];
					$bubble_down_levels = isset( $options['bubble_down_levels'] ) ? $options['bubble_down_levels'] - 1 : null;
					if ( $bubble_down_levels !== null ) {
						$_options['bubble_down_levels'] = $bubble_down_levels;
					}

					if ( $bubble_down && ( $bubble_down_levels === null || $bubble_down_levels >= 0 ) ) {

						$all_children = wp_cache_get( 'all-children', self::CACHE_GROUP );
						if ( $all_children === false ) {
							$all_children = array();
							global $wpdb;
							$term_taxonomies = $wpdb->get_results( $wpdb->prepare(
								"SELECT term_id, parent FROM $wpdb->term_taxonomy WHERE parent IS NOT NULL AND parent != 0 AND taxonomy = %s",
								$taxonomy
							) );
							if ( is_array( $term_taxonomies ) ) {
								foreach( $term_taxonomies as $term_taxonomy ) {
									$all_children[intval( $term_taxonomy->parent )][] = intval( $term_taxonomy->term_id );
								}
							}
							wp_cache_set( 'all-children', $all_children, self::CACHE_GROUP );
						}
						if ( isset( $all_children[$term->term_id] ) ) {
							$children = $all_children[$term->term_id];
						} else {
							$children = array();
						}

						if ( is_array( $children ) && count( $children ) > 0 ) {
							foreach ( $children as $child ) {
								$child = intval( $child );
								if ( !key_exists( $child, $this->index ) ) {
									try {
										$child_node = new WooCommerce_Product_Search_Term_Node(
											$child,
											$taxonomy,
											$_options,
											$this->index,
											$this->terms
										);
										$this->attach( $child_node );
									} catch ( Exception $e ) {
										wps_log_warning( $e->getMessage() );
									}
								}
							}
						}
					}
				} else {
					throw new Exception(
						sprintf( 'Failed to retrieve term %d for taxonomy %s', $term_id, esc_attr( $taxonomy ) )
					);
				}
			}
		}
	}

	public function get_root() {
		if ( $this->parent === null ) {
			return $this;
		} else {
			return $this->parent->get_root();
		}
	}

	public function get_ancestor() {
		if ( $this->parent !== null ) {
			if ( $this->parent->term_id === null ) {
				$result = $this;
			} else {
				$result = $this->parent->get_ancestor();
			}
		} else {
			$result = $this;
		}
		return $result;
	}

	public function get_term_id() {
		return $this->term_id;
	}

	public function get_children() {
		return $this->children;
	}

	public function has_children() {
		return count( $this->children ) > 0;
	}

	public function is_forest() {
		return $this->term_id === null && $this->has_children();
	}

	public function is_empty() {
		$is_empty = true;
		if ( $this->term_id !== null ) {
			$term = $this->get_term( $this->term_id );
			if ( $term instanceof WP_Term ) {
				$is_empty = intval( $term->count ) === 0;
			}
		}
		if ( $is_empty ) {
			if ( count( $this->children ) > 0 ) {
				foreach ( $this->children as $child ) {
					if ( !$child->is_empty() ) {
						$is_empty = false;
						break;
					}
				}
			}
		}
		return $is_empty;
	}

	private function prune_empty() {
		if ( count( $this->children ) > 0 ) {
			foreach ( $this->children as $child ) {
				$child->prune_empty();
				if ( $child->is_empty() ) {
					$this->detach( $child );
				}
			}
		}
	}

	public function get_taxonomy() {
		return $this->taxonomy;
	}

	/**
	 * Attach a node.
	 *
	 * @param WooCommerce_Product_Search_Term_Node $node node to attach
	 */
	public function attach( $node ) {


		if ( $node->term_id !== null ) {
			if ( !key_exists( $node->term_id, $this->index ) ) {
				$node->parent = $this;
				$this->children[] = $node;
				$this->index[$node->term_id] = $node;
			}
		}
	}

	/**
	 * Detach a node.
	 *
	 * @param WooCommerce_Product_Search_Term_Node $node node to detach
	 */
	public function detach( $node ) {


		if ( $node->term_id !== null ) {
			if ( key_exists( $node->term_id, $this->index ) ) {
				$node->parent = null;
				$child = array_search( $node, $this->children, true );
				if ( $child !== false ) {
					array_splice( $this->children, $child, 1 );
				}
				unset( $this->index[$node->term_id] );
			}
		}
	}

	public function append( $term_id ) {
	}

	public function insert( $term_id, $position ) {
	}

	public function prepend( $term_id ) {
	}

	public function remove( $term_id ) {
	}

	/**
	 * Sort by ...
	 *
	 * @param string $orderby 'name', 'slug', 'term_order', 'id' or 'count'
	 * @param string $order 'ASC' or 'DESC'
	 */
	public function sort( $orderby = 'name', $order = 'ASC' ) {
		switch( $orderby ) {
			case 'name' :
			case 'slug' :
			case 'term_order' :
			case 'id' :
			case 'count' :
			case 'name_num' :
				break;
			default :
				$orderby = 'name';
		}
		$order = strtoupper( trim( $order ) );
		switch( $order ) {
			case 'ASC' :
			case 'DESC' :
				break;
			default :
				$order = 'ASC';
		}

		$this->orderby = $orderby;
		$this->order = $order;

		usort( $this->children, array( $this, 'compare' ) );
		foreach ( $this->children as $child ) {
			$child->sort( $orderby, $order );
		}
	}

	/**
	 * Crop root nodes leaving only those from $start until $start + $size
	 *
	 * @param int $start [0, number of nodes)
	 * @param int $size how many to include
	 */
	public function crop( $start, $size ) {

		$n = count( $this->children );
		$nodes_to_detach = array();
		for ( $i = 0; $i < $start ; $i++ ) {
			if ( $i < $n && isset( $this->children[$i] ) ) {
				$nodes_to_detach[] = $this->children[$i];
			}
		}
		for ( $i = $start + $size; $i < $n ; $i++ ) {
			if ( $i < $n && isset( $this->children[$i] ) ) {
				$nodes_to_detach[] = $this->children[$i];
			}
		}
		foreach ( $nodes_to_detach as $node_to_detach ) {
			$this->detach( $node_to_detach );
		}
	}

	/**
	 * Compare two nodes.
	 *
	 * @param WooCommerce_Product_Search_Term_Node $a
	 * @param WooCommerce_Product_Search_Term_Node $b
	 *
	 * @return number
	 */
	public function compare( $a, $b ) {
		$result = 0;
		$term_a = $this->get_term( $a->term_id );
		$term_b = $this->get_term( $b->term_id );
		switch ( $this->orderby ) {
			case 'name' :
				$term_a_name = $term_a instanceof WP_Term ? $term_a->name : '';
				$term_b_name = $term_b instanceof WP_Term ? $term_b->name : '';
				$result = strcmp( $term_a_name, $term_b_name );
				break;
			case 'slug' :
				$term_a_slug = $term_a instanceof WP_Term ? $term_a->slug : '';
				$term_b_slug = $term_b instanceof WP_Term ? $term_b->slug : '';
				$result = strcmp( $term_a_slug, $term_b_slug );
				break;
			case 'term_order' :

				$term_a_order = 0;
				$term_b_order = 0;
				if ( $term_a instanceof WP_Term ) {
					$meta_name = 'order';
					if ( defined( 'WC_VERSION' ) && ( version_compare( WC_VERSION, '3.6.0' ) < 0 ) && taxonomy_is_product_attribute( $a->taxonomy ) ) {
						$meta_name = 'order_' . esc_attr( $a->taxonomy );
					}
					$term_a_order = intval( get_term_meta( $a->term_id, $meta_name, true ) );
				}
				if ( $term_b instanceof WP_Term ) {
					$meta_name = 'order';
					if ( defined( 'WC_VERSION' ) && ( version_compare( WC_VERSION, '3.6.0' ) < 0 ) && taxonomy_is_product_attribute( $b->taxonomy ) ) {
						$meta_name = 'order_' . esc_attr( $b->taxonomy );
					}
					$term_b_order = intval( get_term_meta( $b->term_id, $meta_name, true ) );
				}
				$result = $term_a_order - $term_b_order;
				break;
			case 'id' :
				$term_a_id = $term_a instanceof WP_Term ? $term_a->term_id : '';
				$term_b_id = $term_b instanceof WP_Term ? $term_b->term_id : '';
				$result = $term_a_id - $term_b_id;
				break;
			case 'count' :
				$term_a_count = $term_a instanceof WP_Term ? $term_a->count : '';
				$term_b_count = $term_b instanceof WP_Term ? $term_b->count : '';
				$result = $term_a_count - $term_b_count;
				break;
			case 'name_num' :

				$term_a_name = $term_a instanceof WP_Term ? $term_a->name : '';
				$term_b_name = $term_b instanceof WP_Term ? $term_b->name : '';

				$term_a_numeric = preg_replace( '/[^0-9,\.]/', '', $term_a_name );

				$term_a_numeric = preg_replace( '/,/', '.', $term_a_numeric );

				$term_a_numeric = preg_replace( '/\.(?=.*\.)/', '', $term_a_numeric );

				$term_b_numeric = preg_replace( '/[^0-9,\.]/', '', $term_b_name );
				$term_b_numeric = preg_replace( '/,/', '.', $term_b_numeric );
				$term_b_numeric = preg_replace( '/\.(?=.*\.)/', '', $term_b_numeric );

				$term_a_numeric = is_numeric( $term_a_numeric ) ? floatval( $term_a_numeric ) : 0.0;
				$term_b_numeric = is_numeric( $term_b_numeric ) ? floatval( $term_b_numeric ) : 0.0;
				$delta = $term_a_numeric - $term_b_numeric;

				if ( abs( $delta ) < 0.000000001 ) {
					$delta = 0;
				}
				$result = $delta > 0 ? 1 : -1;
				break;
		}
		switch( $this->order ) {
			case 'DESC' :
				$result = -$result;
				break;
		}
		return $result;
	}

	/**
	 * Test trace.
	 *
	 * @access private
	 * @param number $i
	 */
	public function trace( $i = 0 ) {
		$fill = '';
		for ( $j = 0; $j < $i; $j++ ) {
			$fill .= '–';
		}
		$term_name = '+';
		if ( $this->term_id !== null ) {
			$term = get_term( $this->term_id, $this->taxonomy );
			$term_name = $term->name;
		}
		wps_log_info( $fill . ' ' . $this->term_id . ' ' . $term_name );
		foreach( $this->children as $node ) {
			$node->trace( $i + 1 );
		}
	}

	/**
	 * Returns the index.
	 *
	 * @return array
	 */
	public function get_index() {
		return $this->index;
	}

	/**
	 * Returns the term for the given ID.
	 *
	 * @param int $term_id
	 *
	 * @return WP_Term or null if not found
	 */
	private function get_term( $term_id ) {

		$term = null;
		if ( isset( $this->terms[$term_id] ) ) {
			$term = $this->terms[$term_id];
		} else {

			$_term = get_term( $term_id, $this->taxonomy );
			if ( $_term instanceof WP_Term ) {
				$term = $_term;

			}
			if ( $term !== null ) {

				$this->terms[$term_id] = $term;
			}
		}
		return $term;
	}
}
