<?php

/*
Class Name: VI_WBOOSTSALES_Admin_Crosssell
Author: Andy Ha (support@villatheme.com)
Author URI: http://villatheme.com
Copyright 2016 villatheme.com. All rights reserved.
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_WBOOSTSALES_Admin_ZCrosssell {
	protected $settings;

	public function __construct() {
		$this->settings = new VI_WBOOSTSALES_Data();
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_filter( 'set-screen-option', array( $this, 'save_screen_options' ), 10, 3 );
		add_action( 'wp_ajax_wbs_search_product_crs', array( $this, 'wbs_search_product_crs' ) );
		add_action( 'wp_ajax_wbs_search_product_bundle', array( $this, 'wbs_search_product_bundle' ) );
		add_action( 'wp_ajax_wbs_c_save_product', array( $this, 'wbs_c_save_product' ) );
		add_action( 'wp_ajax_wbs_update_product', array( $this, 'wbs_update_product' ) );
		add_action( 'wp_ajax_wbs_c_remove_product', array( $this, 'wbs_c_remove_product' ) );
		add_action( 'wp_ajax_wbs_u_create_bundle_from_crosssells', array(
			$this,
			'wbs_u_create_bundle_from_crosssells'
		) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 99999 );

		add_action( 'admin_init', array( $this, 'cross_sells_data_update' ), 90 );
		add_action( 'wp_ajax_wbs_ajax_enable_crosssell', array( $this, 'ajax_enable_crosssell' ) );
	}

	public function wbs_u_create_bundle_from_crosssells() {
		global $wp_error;
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], '_wbs_cross_sells_search' ) ) {
			$user         = wp_get_current_user();
			$current_user = $user->get( 'ID' );
			$paged        = 1;
			while ( true ) {
				$args      = array(
					'post_status'    => VI_WBOOSTSALES_Data::search_product_statuses(),
					'post_type'      => 'product',
					'posts_per_page' => 50,
					'paged'          => $paged
				);
				$the_query = new WP_Query( $args );
				// The Loop
				if ( $the_query->have_posts() ) {
					while ( $the_query->have_posts() ) {
						$the_query->the_post();
						$p_id = get_the_ID();
						// Do Stuff
						$woo_c_ids      = get_post_meta( $p_id, '_crosssell_ids', true );
						$created_bundle = get_post_meta( $p_id, '_wbs_crosssells', true );
						if ( is_array( $created_bundle ) && count( $created_bundle ) ) {
							continue;
						}

						if ( is_array( $woo_c_ids ) && count( $woo_c_ids ) ) {

							$product_bundle_name = $this->settings->get_option( 'product_bundle_name' ) ? $this->settings->get_option( 'product_bundle_name' ) : 'Bundle of {product_title}';
							$product_name        = str_replace( '{product_title}', esc_html( get_post_field( 'post_title', $p_id ) ), $product_bundle_name );
							$woo_b_ids           = array_unique( array_merge( array( $p_id ), $woo_c_ids ) );

							$arry_pa    = array();
							$get_prices = array();
							foreach ( $woo_b_ids as $absr => $pa_id ) {
								$indiv_product = wc_get_product( $pa_id );
								$get_prices[]  = $indiv_product->get_price();
								$step          = $absr + 1;
								$arry          = array(
									'bundle_order' => $step,
									'product_id'   => $pa_id,
									'bp_quantity'  => 1
								);
								$arry_pa[]     = $arry;
							}
							$total_price    = array_sum( $get_prices );
							$price_from     = $this->settings->get_option( 'bundle_price_from' );
							$discount_value = $this->settings->get_option( 'bundle_price_discount_value' );
							$discount_type  = $this->settings->get_option( 'bundle_price_discount_type' );
							$level_count    = count( $price_from );
							if ( is_array( $price_from ) && count( $price_from ) ) {
								$match = $level_count - 1;
								for ( $i = 1; $i < $level_count; $i ++ ) {
									if ( $total_price < $price_from[ $i ] ) {
										$match = $i - 1;
										break;
									}
								}
								if ( $discount_type[ $match ] == 'fixed' ) {
									if ( $total_price > $discount_value[ $match ] ) {
										$total_price = $total_price - $discount_value[ $match ];
									}
								} else {
									if ( 1 > $discount_value[ $match ] / 100 ) {
										$total_price = $total_price * ( 1 - $discount_value[ $match ] / 100 );
									}
								}
							}

							$post_individual = array(
								'post_author'  => $current_user,
								'post_content' => '',
								'post_status'  => 'publish',
								'post_title'   => $product_name,
								'post_parent'  => '',
								'post_type'    => "product",
							);

							$post_id = wp_insert_post( $post_individual, $wp_error );
							if ( $post_id ) {
								$attach_id = get_post_meta( $p_id, "_thumbnail_id", true );
								add_post_meta( $post_id, '_thumbnail_id', $attach_id );
							}
							wp_set_object_terms( $post_id, 'wbs_bundle', 'product_type' );

							update_post_meta( $post_id, '_visibility', 'hidden' );
							update_post_meta( $post_id, '_stock_status', 'instock' );
							update_post_meta( $post_id, 'total_sales', '0' );
							update_post_meta( $post_id, '_downloadable', 'no' );
							update_post_meta( $post_id, '_virtual', 'yes' );
							update_post_meta( $post_id, '_regular_price', $total_price );
							update_post_meta( $post_id, '_sale_price', '' );
							update_post_meta( $post_id, '_purchase_note', '' );
							update_post_meta( $post_id, '_featured', 'no' );
							update_post_meta( $post_id, '_weight', '' );
							update_post_meta( $post_id, '_length', '' );
							update_post_meta( $post_id, '_width', '' );
							update_post_meta( $post_id, '_height', '' );
							update_post_meta( $post_id, '_sku', '' );
							update_post_meta( $post_id, '_product_attributes', array() );
							update_post_meta( $post_id, '_sale_price_dates_from', '' );
							update_post_meta( $post_id, '_sale_price_dates_to', '' );
							update_post_meta( $post_id, '_price', $total_price );
							update_post_meta( $post_id, '_sold_individually', '' );
							update_post_meta( $post_id, '_manage_stock', 'no' );
							update_post_meta( $post_id, '_backorders', 'no' );
							update_post_meta( $post_id, '_stock', '' );


							$product_new = wc_get_product( $post_id );
							$terms       = array( 'exclude-from-search', 'exclude-from-catalog' );

							if ( ! is_wp_error( wp_set_post_terms( $post_id, $terms, 'product_visibility', false ) ) ) {
								delete_transient( 'wc_featured_products' );
								do_action( 'woocommerce_product_set_visibility', $post_id, $product_new->get_catalog_visibility() );
							}

							if ( count( $arry_pa ) ) {
								update_post_meta( $post_id, '_wbs_wcpb_bundle_data', $arry_pa );
								//update_post_meta( $p_id, '_wbs_cross_sell_of', $post_id );
								update_post_meta( $p_id, '_wbs_crosssells', array( $post_id ) );
							}
						}
					}
				} else {
					break;
				}

				$paged ++;
				wp_reset_postdata();
			}
			$msg['check'] = 'done';
			echo json_encode( $msg );
			die;
		}
	}

	public function ajax_enable_crosssell() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		global $wbs_settings;
		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], '_wbs_cross_sells_search' ) ) {
			$wbs_settings['enable']           = 1;
			$wbs_settings['crosssell_enable'] = 1;
			update_option( '_woocommerce_boost_sales', $wbs_settings );
		}
		die;
	}

	/**
	 * @throws WC_Data_Exception
	 */
	function cross_sells_data_update() {
		$dismiss_opt = get_option( 'dismiss_update_crsells' );
		if ( empty( $dismiss_opt ) || $dismiss_opt != '1' ) {
			$user_id   = get_current_user_id();
			$arg_first = array(
				'post_status'    => 'publish',
				'post_type'      => 'product',
				'posts_per_page' => - 1,
				'meta_query'     => array(
					array(
						'key'     => '_wbs_crosssells',
						'value'   => '',
						'compare' => '!='
					)
				)
			);
			$get_post  = get_posts( $arg_first );

			if ( count( $get_post ) ) {
				$array2 = array();
				foreach ( $get_post as $list_id ) {
					$get_pmt_cross     = get_post_meta( $list_id->ID, '_wbs_crosssells' );
					$array2['id'][]    = $list_id->ID;
					$array2['value'][] = $get_pmt_cross[0][0];
				}

				$duplicate_element = array_unique( array_diff_assoc( $array2['value'], array_unique( $array2['value'] ) ) );
				if ( count( $duplicate_element ) ) {
					foreach ( $duplicate_element as $key_sep => $separate_e ) {
						$dupp = $array2['id'][ $key_sep ];
						$prda = wc_get_product( $separate_e );

						if ( get_post_status( $separate_e ) == 'publish' ) {
							$meta_to_exclude = array_filter( apply_filters( 'woocommerce_duplicate_product_exclude_meta', array() ) );
							$duplicate       = clone $prda;
							$duplicate->set_id( 0 );
							$duplicate->set_name( sprintf( __( '%s (Copy)', 'woocommerce-boost-sales' ), $duplicate->get_name() ) );
							$duplicate->set_total_sales( 0 );
							if ( '' !== $prda->get_sku( 'edit' ) ) {
								$duplicate->set_sku( wc_product_generate_unique_sku( 0, $prda->get_sku( 'edit' ) ) );
							}
							$duplicate->set_status( 'publish' );
							$duplicate->set_date_created( null );
							$duplicate->set_slug( '' );
							$duplicate->set_rating_counts( 0 );
							$duplicate->set_average_rating( 0 );
							$duplicate->set_review_count( 0 );

							foreach ( $meta_to_exclude as $meta_key ) {
								$duplicate->delete_meta_data( $meta_key );
							}

							// This action can be used to modify the object further before it is created - it will be passed by reference. @since 3.0
							do_action( 'woocommerce_product_duplicate_before_save', $duplicate, $prda );

							// Save parent product.
							$duplicate->save();
							$dup_id = $duplicate->get_id();
							update_post_meta( $dupp, '_wbs_crosssells', array( $dup_id ) );
							add_user_meta( $user_id, 'dismiss_cross_sells_data_update', 'true', true );

						}
					}
				}
			}
			update_option( 'dismiss_update_crsells', 1 );
		}
	}

	/**
	 * Get all cross sells product chosen
	 */
	public function get_crs_select( $p_id ) {
		global $wpdb;
		$prds = wc_get_product( $p_id );
		if ( $prds->has_child() && $prds->get_type() == 'variable' ) {
			$children = $prds->get_children();
			if ( count( $children ) ) {
				foreach ( $children as $child ) {
					$sql_parent    = $wpdb->prepare( "SELECT post_id FROM " . $wpdb->prefix . "postmeta WHERE meta_key = '_wbs_wcpb_bundle_data' AND meta_value LIKE '%s'", '%' . $child . '%' );
					$result_parent = $wpdb->get_results( $sql_parent, OBJECT );

					if ( ! $result_parent ) {
						continue;
					} else {
						foreach ( $result_parent as $post_id ) {
							$array_pid  = (array) $post_id;
							$get_status = get_post_status( $array_pid['post_id'] );
							$get_pid    = get_post_meta( $array_pid['post_id'], '_wbs_wcpb_bundle_data' );

							if ( is_array( $get_pid ) && $get_status == 'publish' ) {
								if ( count( array_filter( $get_pid ) ) ) {
									foreach ( $get_pid as $items ) {
										foreach ( $items as $item ) {
											if ( in_array( $p_id, $item ) ) {
												return $array_pid['post_id'];
											}
										}
									}
								}
							} else {
								return 0;
							}
						}
					}
				}
			}
		}

		$sql    = $wpdb->prepare( "SELECT post_id FROM " . $wpdb->prefix . "postmeta WHERE meta_key = '_wbs_wcpb_bundle_data' AND meta_value LIKE '%s'", '%' . $p_id . '%' );
		$result = $wpdb->get_results( $sql, OBJECT );
		if ( ! $result ) {
			return 0;
		} else {
			foreach ( $result as $post_id ) {
				$array_pid   = (array) $post_id;
				$get_status2 = get_post_status( $array_pid['post_id'] );
				$get_pid     = get_post_meta( $array_pid['post_id'], '_wbs_wcpb_bundle_data' );

				if ( is_array( $get_pid ) && $get_status2 == 'publish' ) {
					if ( count( array_filter( $get_pid ) ) ) {
						foreach ( $get_pid as $items ) {
							foreach ( $items as $item ) {
								if ( in_array( $p_id, $item ) ) {
									return $array_pid['post_id'];
								}
							}
						}
					}
				} else {
					return 0;
				}
			}
		}

	}

	/**
	 * Get product bundle from id
	 */
	protected function get_product_bundle_from_id( $p_id ) {
		$array_wbs_bundle = array( $p_id );
		$arg_first        = array(
			'post_status'    => 'publish',
			'post_type'      => 'product',
			'posts_per_page' => - 1,
			'meta_query'     => array(
				array(
					'key'     => '_wbs_wcpb_bundle_data',
					'value'   => '',
					'compare' => '!='
				)
			)
		);
		$post_alls        = get_posts( $arg_first );
		if ( count( $post_alls ) ) {
			foreach ( $post_alls as $post_all ) {
				$meta_a = get_post_meta( $post_all->ID, '_wbs_wcpb_bundle_data' );
				if ( count( $meta_a ) ) {
					foreach ( $meta_a as $meta_b ) {
						foreach ( $meta_b as $all_items ) {
							$array_wbs_bundle[] = $all_items['product_id'];
						}
					}
				}
			}
		}

		return $array_wbs_bundle;
	}

	/**
	 * Select 2 Search ajax
	 */
	public function wbs_search_product_bundle() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		ob_start();

		$keyword     = filter_input( INPUT_GET, 'keyword', FILTER_SANITIZE_STRING );
		$p_bundle_id = filter_input( INPUT_GET, 'p_bundle_id', FILTER_SANITIZE_STRING );

		if ( empty( $keyword ) ) {
			die();
		}

		$arg            = array(
			'post_status'    => 'publish',
			'post_type'      => 'product',
			'posts_per_page' => - 1,
			's'              => $keyword,
			'post__not_in'   => array( $p_bundle_id ),
			'tax_query'      => array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => 'wbs_bundle',
					'compare'  => '='
				),
			)
		);
		$the_query      = new WP_Query( $arg );
		$found_products = array();
		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$get_wc_product = wc_get_product( get_the_ID() );
				if ( $get_wc_product->is_in_stock() ) {
					$product = array(
						'id'   => get_the_ID(),
						'text' => get_the_title() . ' (#' . get_the_ID() . ')'
					);
				} else {
					$product = array(
						'id'   => get_the_ID(),
						'text' => get_the_title() . ' (#' . get_the_ID() . ')(' . esc_html__( 'Out of stock', 'woocommerce-boost-sales' ) . ')'
					);
				}
				$found_products[] = $product;

			}
		}
		// Reset Post Data
		wp_reset_postdata();
		wp_send_json( $found_products );
		die;
	}

	/**
	 * Select 2 Search ajax
	 */
	public function wbs_search_product_crs() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		ob_start();

		$keyword = filter_input( INPUT_GET, 'keyword', FILTER_SANITIZE_STRING );
		//		$p_id    = filter_input( INPUT_GET, 'p_id', FILTER_SANITIZE_STRING );

		if ( empty( $keyword ) ) {
			die();
		}

		$arg            = array(
			'post_status'    => VI_WBOOSTSALES_Data::search_product_statuses(),
			'post_type'      => 'product',
			'posts_per_page' => - 1,
			's'              => $keyword,
			//			'post__not_in'   => array( $p_id ),
			'tax_query'      => array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => 'wbs_bundle',
					'operator' => 'NOT IN'
				),
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => array( 'simple', 'variable', 'subscription', 'variable-subscription', 'member' ),
					'operator' => 'IN'
				),
			)
		);
		$the_query      = new WP_Query( $arg );
		$found_products = array();
		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$get_wc_product = wc_get_product( get_the_ID() );

				if ( $get_wc_product->has_child() && $get_wc_product->get_type() == 'variable' ) {
					if ( $get_wc_product->is_in_stock() ) {
						$product = array(
							'id'   => get_the_ID(),
							'text' => get_the_title() . ' (#' . get_the_ID() . ') (#VARIABLE) '
						);
					} else {
						$product = array(
							'id'   => get_the_ID(),
							'text' => get_the_title() . ' (#' . get_the_ID() . ') (#VARIABLE)(' . esc_html__( 'Out of stock', 'woocommerce-boost-sales' ) . ')'
						);
					}

					$found_products[]  = $product;
					$children_variable = $get_wc_product->get_children();
					foreach ( $children_variable as $child ) {
						$product_child = wc_get_product( $child );
						if ( $product_child->is_in_stock() ) {
							$product = array(
								'id'   => $child,
								'text' => $product_child->get_name() . ' (#' . $child . ')'
							);
						} else {
							$product = array(
								'id'   => $child,
								'text' => $product_child->get_name() . ' (#' . $child . ')(' . esc_html__( 'Out of stock', 'woocommerce-boost-sales' ) . ')'
							);
						}
						$found_products[] = $product;
					}
				} else {
					if ( $get_wc_product->is_in_stock() ) {
						$product = array(
							'id'   => get_the_ID(),
							'text' => get_the_title() . ' (#' . get_the_ID() . ')'
						);
					} else {
						$product = array(
							'id'   => get_the_ID(),
							'text' => get_the_title() . ' (#' . get_the_ID() . ')(' . esc_html__( 'Out of stock', 'woocommerce-boost-sales' ) . ')'
						);
					}
					$found_products[] = $product;
				}
			}
		}
		// Reset Post Data
		wp_reset_postdata();
		wp_send_json( $found_products );
		die;
	}

	/**
	 * Remove all Cross-sell
	 */
	public function wbs_c_remove_product() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		ob_start();
		$p_id              = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_STRING );
		$product_bundle_id = filter_input( INPUT_POST, 'product_bundle_id', FILTER_SANITIZE_STRING );
		$msg               = array();

		if ( empty( $p_id ) ) {
			die();
		}
		update_post_meta( $p_id, '_wbs_crosssells_bundle', '' );
		if ( $product_bundle_id ) {
			wp_delete_post( $product_bundle_id );
			update_post_meta( $p_id, '_wbs_crosssells', '' );
			$arg       = array(
				'post_status'    => 'publish',
				'post_type'      => 'product',
				'posts_per_page' => - 1,
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'key'   => '_wbs_crosssells_bundle',
						'value' => $product_bundle_id,
					),
				)
			);
			$the_query = new WP_Query( $arg );
			if ( $the_query->have_posts() ) {
				while ( $the_query->have_posts() ) {
					$the_query->the_post();
					update_post_meta( get_the_ID(), '_wbs_crosssells_bundle', '' );
				}
			}
			wp_reset_postdata();
		}

		$msg['check'] = 'done';
		ob_clean();
		echo json_encode( $msg );
		die;
	}

	/**
	 * Save cross sells
	 */
	public function wbs_c_save_product() {
		global $wp_error;


		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$user         = wp_get_current_user();
		$current_user = $user->get( 'ID' );
		ob_start();

		$p_id              = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_STRING );
		$c_id              = filter_input( INPUT_POST, 'c_id', FILTER_SANITIZE_STRING );
		$product_bundle_id = filter_input( INPUT_POST, 'product_bundle_id', FILTER_SANITIZE_STRING );
		$other_bundle_id   = isset( $_POST['other_bundle_id'] ) ? sanitize_text_field( $_POST['other_bundle_id'] ) : '';
		$msg               = array();

		if ( empty( $p_id ) ) {
			die;
		}

		update_post_meta( $p_id, '_wbs_crosssells_bundle', $other_bundle_id );
		if ( ( empty( $c_id ) || $c_id == 'null' ) ) {
			if ( $other_bundle_id ) {
				$msg['check'] = 'done';
				ob_clean();

				echo json_encode( $msg );
				die;
			} else {
				/*delete_post_meta( $product_bundle_id, '_wbs_wcpb_bundle_data' );
			$post3 = array( 'ID' => $product_bundle_id, 'post_status' => 'draft' );
			wp_update_post( $post3 );*/
				$msg['check'] = 'wrong';
				ob_clean();

				echo json_encode( $msg );
				die;
			}
		}
        $categories=$this->settings->get_option('bundle_categories');

		$c_id = array_filter( explode( ',', $c_id ) );
		if ( count( $c_id ) ) {
			$b_ids      = array_unique( array_merge( array( $p_id ), $c_id ) );
			$arry_pa    = array();
			$get_prices = array();
			foreach ( $b_ids as $absr => $pa_id ) {
				$indiv_product = wc_get_product( $pa_id );
				$get_prices[]  = $indiv_product->get_price();
				$step          = $absr + 1;
				$arry          = array(
					'bundle_order' => $step,
					'product_id'   => $pa_id,
					'bp_quantity'  => 1
				);
				$arry_pa[]     = $arry;
			}
			$total_price = array_sum( $get_prices );

			$price_from     = $this->settings->get_option( 'bundle_price_from' );
			$discount_value = $this->settings->get_option( 'bundle_price_discount_value' );
			$discount_type  = $this->settings->get_option( 'bundle_price_discount_type' );
			$level_count    = count( $price_from );
			if ( is_array( $price_from ) && count( $price_from ) ) {
				$match = $level_count - 1;
				for ( $i = 1; $i < $level_count; $i ++ ) {
					if ( $total_price < $price_from[ $i ] ) {
						$match = $i - 1;
						break;
					}
				}
				if ( $discount_type[ $match ] == 'fixed' ) {
					if ( $total_price > $discount_value[ $match ] ) {
						$total_price = $total_price - floatval( $discount_value[ $match ] );
					}
				} else {
					if ( 1 > $discount_value[ $match ] / 100 ) {
						$total_price = $total_price * ( 1 - floatval( $discount_value[ $match ] ) / 100 );
					}
				}
				$total_price = round( $total_price, wc_get_price_decimals() );
			}
			if ( empty( $product_bundle_id ) ) {
				$product_bundle_name = $this->settings->get_option( 'product_bundle_name' ) ? $this->settings->get_option( 'product_bundle_name' ) : 'Bundle of {product_title}';
				$product_name        = str_replace( '{product_title}', esc_html( get_post_field( 'post_title', $p_id ) ), $product_bundle_name );
				$post_individual     = array(
					'post_author'  => $current_user,
					'post_content' => '',
					'post_status'  => 'publish',
					'post_title'   => $product_name,
					'post_parent'  => '',
					'post_type'    => "product",
				);

				$post_id = wp_insert_post( $post_individual, $wp_error );
				if ( $post_id ) {
					$attach_id = get_post_meta( $p_id, "_thumbnail_id", true );
					add_post_meta( $post_id, '_thumbnail_id', $attach_id );
				}
				wp_set_object_terms( $post_id, 'wbs_bundle', 'product_type' );
				update_post_meta( $post_id, '_visibility', 'hidden' );
				update_post_meta( $post_id, '_stock_status', 'instock' );
				update_post_meta( $post_id, 'total_sales', '0' );
				update_post_meta( $post_id, '_downloadable', 'no' );
				update_post_meta( $post_id, '_virtual', 'yes' );
				update_post_meta( $post_id, '_regular_price', $total_price );
				update_post_meta( $post_id, '_sale_price', '' );
				update_post_meta( $post_id, '_purchase_note', '' );
				update_post_meta( $post_id, '_featured', 'no' );
				update_post_meta( $post_id, '_weight', '' );
				update_post_meta( $post_id, '_length', '' );
				update_post_meta( $post_id, '_width', '' );
				update_post_meta( $post_id, '_height', '' );
				update_post_meta( $post_id, '_sku', '' );
				update_post_meta( $post_id, '_product_attributes', array() );
				update_post_meta( $post_id, '_sale_price_dates_from', '' );
				update_post_meta( $post_id, '_sale_price_dates_to', '' );
				update_post_meta( $post_id, '_price', $total_price );
				update_post_meta( $post_id, '_sold_individually', '' );
				update_post_meta( $post_id, '_manage_stock', 'no' );
				update_post_meta( $post_id, '_backorders', 'no' );
				update_post_meta( $post_id, '_stock', '' );
				$product_new = wc_get_product( $post_id );
				$terms       = array( 'exclude-from-search', 'exclude-from-catalog' );
                if(is_array($categories)&&count($categories)){
	                wp_set_post_terms( $post_id, $categories, 'product_cat', false );
                }
				if ( ! is_wp_error( wp_set_post_terms( $post_id, $terms, 'product_visibility', false ) ) ) {
					delete_transient( 'wc_featured_products' );
					do_action( 'woocommerce_product_set_visibility', $post_id, $product_new->get_catalog_visibility() );
				}

				if ( count( $arry_pa ) ) {
					update_post_meta( $post_id, '_wbs_wcpb_bundle_data', $arry_pa );
					//update_post_meta( $p_id, '_wbs_cross_sell_of', $post_id );
					update_post_meta( $p_id, '_wbs_crosssells', array( $post_id ) );
				}
				$msg['check'] = 'done';
			} else {
				if ( count( $arry_pa ) ) {
					$post2 = array( 'ID' => $product_bundle_id, 'post_status' => 'publish' );
					wp_update_post( $post2 );
					update_post_meta( $product_bundle_id, '_wbs_wcpb_bundle_data', $arry_pa );
					update_post_meta( $p_id, '_wbs_crosssells', array( $product_bundle_id ) );
					update_post_meta( $product_bundle_id, '_regular_price', $total_price );
					update_post_meta( $product_bundle_id, '_price', $total_price );
				}
				$msg['check'] = 'done';
			}

		} else {
			$msg['check'] = 'error';
		}
		ob_clean();

		echo json_encode( $msg );
		die;
	}

	/**
	 * Update product bundle
	 */
	public function wbs_update_product() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		ob_start();

		$p_id  = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_STRING );
		$title = filter_input( INPUT_POST, 'title', FILTER_SANITIZE_STRING );
		$price = filter_input( INPUT_POST, 'price', FILTER_SANITIZE_STRING );

		if ( empty( $p_id ) ) {
			die;
		}

		$post_bundle = array(
			'ID'         => $p_id,
			'post_title' => $title
		);

		wp_update_post( $post_bundle, true );

		$meta_price = get_post_meta( $p_id, '_sale_price', true );
		if ( trim( $meta_price ) ) {
			update_post_meta( $p_id, '_sale_price', $price );
		} else {
			update_post_meta( $p_id, '_regular_price', $price );
		}
		update_post_meta( $p_id, '_price', $price );

		$msg = array();
		if ( ! is_wp_error( $p_id ) ) {
			$msg['check'] = 'done';
		} else {
			$msg['check'] = 'wrong';
			$errors       = $p_id->get_error_messages();
			foreach ( $errors as $error ) {
				$msg['detail_err'] = $error;
			}
		}

		ob_clean();
		echo json_encode( $msg );
		die;
	}

	/**
	 * Init scripts
	 */
	public function enqueue_scripts() {
		$page = isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : '';
		if ( $page == 'woocommerce-boost-sales-crosssell' ) {
			global $wp_scripts, $wp_styles;
			$scripts = $wp_scripts->registered;

			foreach ( $scripts as $k => $script ) {
				preg_match( '/select2/i', $k, $result );
				if ( count( array_filter( $result ) ) ) {
					unset( $wp_scripts->registered[ $k ] );
					wp_dequeue_script( $script->handle );
				}
				preg_match( '/bootstrap/i', $k, $result );
				if ( count( array_filter( $result ) ) ) {
					unset( $wp_scripts->registered[ $k ] );
					wp_dequeue_script( $script->handle );
				}
			}
			wp_enqueue_style( 'select2', VI_WBOOSTSALES_CSS . 'select2.min.css' );
			wp_enqueue_script( 'select2-v4', VI_WBOOSTSALES_JS . 'select2.js', array( 'jquery' ), '4.0.3' );
			wp_enqueue_script( 'jquery-ui-tooltip' );
			wp_enqueue_script( 'woocommerce-boost-sales-crosssell-admin', VI_WBOOSTSALES_JS . 'woocommerce-boost-sales-crosssell-admin.js', array( 'jquery' ), VI_WBOOSTSALES_VERSION );
		}
	}

	/**
	 * Add Menu
	 */
	public function admin_menu() {
		$send_now = add_submenu_page(
			'woocommerce-boost-sales', esc_html__( 'Cross-Sells', 'woocommerce-boost-sales' ), esc_html__( 'Cross-Sells', 'woocommerce-boost-sales' ), 'manage_options', 'woocommerce-boost-sales-crosssell', array(
				$this,
				'page_callback'
			)
		);
		add_action( "load-$send_now", array( $this, 'screen_options_page' ) );
	}

	/**
	 * Save options from screen options
	 *
	 * @param $status
	 * @param $option
	 * @param $value
	 *
	 * @return mixed
	 */
	public function save_screen_options( $status, $option, $value ) {
		if ( 'wbsc_per_page' == $option ) {
			return $value;
		}

		return $status;
	}

	/**
	 * Add Screen Options
	 */
	public function screen_options_page() {

		$option = 'per_page';

		$args = array(
			'label'   => esc_html__( 'Number of items per page', 'wp-admin' ),
			'default' => 30,
			'option'  => 'wbsc_per_page'
		);

		add_screen_option( $option, $args );
	}

	/**
	 * Menu page call back
	 */
	public function page_callback() {
		global $wpdb;
		$user     = get_current_user_id();
		$screen   = get_current_screen();
		$option   = $screen->get_option( 'per_page', 'option' );
		$per_page = get_user_meta( $user, $option, true );

		if ( empty ( $per_page ) || $per_page < 1 ) {
			$per_page = $screen->get_option( 'per_page', 'default' );
		}
		$paged = isset( $_GET['paged'] ) ? $_GET['paged'] : 1;

		?>
        <div class="wrap">
            <h2><?php esc_html_e( 'CROSS-SELLS', 'woocommerce-boost-sales' ) ?></h2>

            <p class="description"><?php esc_html_e( 'Cross-sells are products that instead of you buy this product you can buy bundle products contain this product, based on the current product. **For example, if you are selling a laptop, cross-sells might be a protective case or stickers or a special adapter.**', 'woocommerce-boost-sales' ) ?>
                <br>
                <a href="javascript:void(0)" id="wbs_different_up-cross-sell" title=""
                   data-wbs_up_crosssell="http://new2new.com/envato/woocommerce-boost-sales/product-cross-sells.gif"><?php esc_html_e( 'What is CROSS-SELLS?', 'woocommerce-boost-sales' ); ?></a>
            </p>
			<?php
			if ( ! $this->settings->get_option( 'enable' ) || ! $this->settings->get_option( 'crosssell_enable' ) ) {
				?>
                <div class="error">
                    <p><?php _e( 'Cross-sells feature is currently disabled. <a class="wbs-crosssells-ajax-enable button button-primary" href="javascript:void(0)">Enable now</a>', 'woocommerce-boost-sales' ) ?></p>
                </div>
				<?php
			}
			?>
            <form action="<?php echo esc_url( admin_url( 'admin.php?page=woocommerce-boost-sales-crosssell' ) ) ?>"
                  method="post">
				<?php wp_nonce_field( '_wbs_cross_sells_search', '_wsm_nonce' ) ?>

                <div class="tablenav top">
					<?php
					$args = array(
						'post_type'      => 'product',
						'post_status'    => VI_WBOOSTSALES_Data::search_product_statuses(),
						'order'          => 'DESC',
						'orderby'        => 'ID',
						'posts_per_page' => $per_page,
						'paged'          => $paged,
						'tax_query'      => array(
							'relation' => 'AND',
							array(
								'taxonomy' => 'product_type',
								'field'    => 'slug',
								'terms'    => 'wbs_bundle',
								'operator' => '!='
							),
							array(
								'taxonomy' => 'product_type',
								'field'    => 'slug',
								'terms'    => array(
									'simple',
									'variable',
									'external',
									'subscription',
									'variable-subscription',
									'member'
								),
								'operator' => 'IN'
							),
						)
					);


					$keyword = '';
					if ( isset( $_POST['wbs_cs_search'] ) && isset( $_POST['_wsm_nonce'] ) ) {
						if ( wp_verify_nonce( $_POST['_wsm_nonce'], '_wbs_cross_sells_search' ) ) {
							$keyword   = $_POST['wbs_cs_search'];
							$args['s'] = $keyword;
						}

					}
					$the_query = new WP_Query( $args ); ?>

                    <div class="tablenav-pages">
                        <span class="button action btn-sync-crosssell"
                              title="<?php esc_html_e( 'Create bundle from WooCommerce cross-sells for products whose bundles are not set yet', 'woocommerce-boost-sales' ) ?>"><?php esc_html_e( 'Sync Cross-Sells', 'woocommerce-boost-sales' ) ?></span>
                        <input class="text short" name="wbs_cs_search"
                               placeholder="<?php esc_html_e( 'Search product', 'woocommerce-boost-sales' ) ?>"
                               value="<?php echo esc_attr( $keyword ) ?>">
                    </div>
                </div>
            </form>
            <div class="list-products">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                    <tr>
                        <th scope="col" id="product-name"
                            class="manage-column column-product-name column-primary sortable desc">
                            <a href="#"><span><?php esc_html_e( 'Product Name', 'woocommerce-boost-sales' ) ?></span></a>
                        </th>
                        <th scope="col" id="up-sells" class="manage-column column-up-sells sortable desc">
                            <span><?php esc_html_e( 'Cross-sells', 'woocommerce-boost-sales' ) ?></span>
                        </th>
                        <th scope="col" id="actions" class="manage-column column-actions sortable desc">
							<?php esc_html_e( 'Actions', 'woocommerce-boost-sales' ) ?>
                        </th>
                    </tr>
                    </thead>
					<?php if ( $the_query->have_posts() ) { ?>
                        <tbody id="the-list" data-wp-lists="list:product">
						<?php
						while ( $the_query->have_posts() ) {
							$the_query->the_post();
							$p_id = get_the_ID(); ?>
                            <tr id="product-<?php echo $p_id ?>">
                                <td class="product column-product has-row-actions column-primary"
                                    data-colname="product-name">
                                    <a href="<?php echo esc_url( 'post.php?action=edit&post=' . $p_id ) ?>"><?php echo '[#' . get_the_ID() . '] ' . the_title( '', '', '' ) ?></a>
                                </td>
                                <td data-id="<?php echo $p_id ?>" class="name column-cross-sells"
                                    data-colname="<?php esc_attr_e( 'Cross sells', 'woocommerce-boost-sales' ) ?>">
									<?php
									$post_meta_of     = get_post_meta( $p_id, '_wbs_crosssells', true );
									$bundle_of_others = get_post_meta( $p_id, '_wbs_crosssells_bundle', true );
									if ( is_array( $post_meta_of ) && count( array_filter( $post_meta_of ) ) ) {
										$bundle_id = isset( $post_meta_of[0] ) ? $post_meta_of[0] : '';
									} else {
										$bundle_id = '';
									}
									?>
                                    <p>
                                        <input class="wbs-use-other-bundle"
                                               id="<?php echo 'wbs-use-other-bundle-' . $p_id ?>"
                                               type="checkbox" <?php if ( $bundle_of_others )
											echo 'checked' ?>><label
                                                for="<?php echo 'wbs-use-other-bundle-' . $p_id ?>"><?php esc_html_e( 'Use a bundle of other products', 'woocommerce-boost-sales' ) ?></label>
                                    </p>
                                    <input type="hidden" name="_wbs_cross_sell_of"
                                           value="<?php echo esc_attr( $bundle_id ) ?>">
                                    <div class="wbs-product-search-bundle-container" <?php if ( ! $bundle_of_others ) {
										echo 'style="display:none;"';
									} ?>>
                                        <select name="_wbs_cross_sell_bundle"
                                                class="product-search-bundle bundle-product-<?php echo get_the_ID() ?>">
											<?php
											if ( $bundle_of_others ) {
												$bundle_of_others_object = wc_get_product( $bundle_of_others );
												if ( $bundle_of_others_object ) {
													$bundle_of_others_object_title = $bundle_of_others_object->get_title() . '(#' . $bundle_of_others . ')';
													?>
                                                    <option value="<?php echo $bundle_of_others ?>"
                                                            selected><?php echo $bundle_of_others_object_title ?></option>
													<?php
												}
											}
											?>
                                        </select>
                                    </div>
                                    <div class="product-search-crs-container" <?php if ( $bundle_of_others ) {
										echo 'style="display:none;"';
									} ?>>
                                        <select multiple="multiple" name="_wbs_cross_sell"
                                                class="product-search-crs u-product-<?php echo get_the_ID() ?>">
											<?php
											if ( $bundle_id ) {
												if ( get_post_status( $bundle_id ) == 'publish' ) {
													$product_chosen = get_post_meta( $bundle_id, '_wbs_wcpb_bundle_data', true );
													if ( is_array( $product_chosen ) && count( $product_chosen ) ) {
														foreach ( $product_chosen as $product_chose ) {
															if ( isset( $product_chose['product_id'] ) && intval( $product_chose['product_id'] ) ) {
																$dt_product = wc_get_product( $product_chose['product_id'] );
																if ( $dt_product ) {
																	$parent = $out_stock = '';
																	if ( $dt_product->has_child() && $dt_product->get_type() == 'variable' ) {
																		$parent = ' (#PARENT)';
																	}
																	if ( ! $dt_product->is_in_stock() ) {
																		$out_stock = '(' . esc_html__( 'Out of stock', 'woocommerce-boost-sales' ) . ')';
																	}
																	if ( get_post_status( $product_chose['product_id'] ) == 'publish' ) {
																		?>
                                                                        <option selected="selected"
                                                                                value="<?php echo esc_attr( $product_chose['product_id'] ); ?>">
																			<?php echo esc_html( $dt_product->get_name() . ' (#' . $product_chose['product_id'] . ')' . $parent . $out_stock ) ?>
                                                                        </option>
																		<?php
																	}
																}
															}
														}
													}
												}
											}
											?>
                                        </select>
										<?php
										if ( $bundle_id ) {
											if ( get_post_status( $bundle_id ) == 'publish' ) {
												$detail_bundle = wc_get_product( $bundle_id ); ?>
                                                <br>
                                                <a target="_blank"
                                                   href="<?php echo get_edit_post_link( $bundle_id ); ?>"
                                                   class="button-edit"><?php esc_attr_e( 'Edit product bundle', 'woocommerce-boost-sales' ) ?></a> |
                                                <span class="button-edit button-quick-edit"><?php esc_attr_e( 'Quick Edit product bundle', 'woocommerce-boost-sales' ) ?></span>
                                                <div class="inline-edit-row"
                                                     data-product_bundle_id="<?php echo $bundle_id; ?>">
                                                    <fieldset class="">
                                                        <legend class="inline-edit-legend">Quick Edit</legend>
                                                        <div class="inline-edit-col">
                                                            <label>
                                                                <span class="title"><?php esc_html_e( 'Title', 'woocommerce-boost-sales' ) ?></span>
                                                                <span class="input-text-wrap"><input type="text"
                                                                                                     name="post_bundle_title"
                                                                                                     class="ptitle"
                                                                                                     value="<?php echo $detail_bundle->get_title(); ?>"></span>
                                                            </label>

                                                            <label>
                                                                <span class="title"><?php esc_html_e( 'Price', 'woocommerce-boost-sales' ) ?></span>
                                                                <span class="input-text-wrap"><input
                                                                            class="text wc_input_price" type="text"
                                                                            name="product_bundle_regular_price"
                                                                            title="<?php esc_html_e( 'Please enter the number of price', 'woocommerce-boost-sales' ) ?>"
                                                                            value="<?php echo $detail_bundle->get_price(); ?>"></span>
                                                            </label>

                                                        </div>
                                                    </fieldset>
                                                    <p class="submit inline-edit-save">
														<?php wp_nonce_field( 'wp_update_bundle_product', '_wbs_update_nonce' ) ?>
                                                        <button type="button"
                                                                class="button cancel alignleft button-cancel">
                                                            Cancel
                                                        </button>
                                                        <button type="button"
                                                                class="button button-primary save alignright button-update">
                                                            Update
                                                        </button>
                                                        <span class="spinner"></span>
                                                    </p>
                                                </div>
												<?php
											}
										}
										?>
                                    </div>
                                </td>
                                <td class="email column-action product-action-<?php echo esc_attr( $p_id ); ?>"
                                    data-colname="<?php esc_attr_e( 'Actions', 'woocommerce-boost-sales' ) ?>"
                                    data-id="<?php echo esc_attr( $p_id ); ?>">
                                    <a class="button" target="_blank"
                                       href="<?php the_permalink( $p_id ) ?>"><?php esc_attr_e( 'View', 'woocommerce-boost-sales' ) ?></a>
                                    <span class="button button-save"><?php esc_attr_e( 'Save', 'woocommerce-boost-sales' ) ?></span>
                                    <span class="button button-remove"><?php esc_attr_e( 'Remove all', 'woocommerce-boost-sales' ) ?></span>
                                </td>
                            </tr>
						<?php } ?>
                        </tbody>
					<?php }
					// Reset Post Data
					wp_reset_postdata();
					?>
                </table>
                <div class="tablenav ">
                    <div class="tablenav-pages">
						<?php
						$count      = wp_count_posts( 'product' );
						$count      = $count->publish;
						$total_page = ( $count % $per_page ) == 0 ? intval( $count / $per_page ) : intval( $count / $per_page ) + 1;

						/*Previous button*/
						if ( $per_page * $paged > $per_page ) {
							$p_paged = $paged - 1;
						} else {
							$p_paged = 0;
						}
						if ( $p_paged ) {
							$p_url = add_query_arg(
								array(
									'page'  => 'woocommerce-boost-sales-crosssell',
									'paged' => $p_paged
								), admin_url( 'admin.php' )
							); ?>
                            <a class="prev-page" href="<?php echo esc_url( $p_url ) ?>"><span
                                        class="screen-reader-text"><?php esc_html_e( 'Previous Page', 'woocommerce-boost-sales' ) ?></span><span
                                        aria-hidden="true">‹</span></a>
						<?php } else { ?>
                            <span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
						<?php } ?>
                        <span class="screen-reader-text"><?php esc_html_e( 'Current Page', 'woocommerce-boost-sales' ) ?></span>
                        <span id="table-paging" class="paging-input">
								<span class="tablenav-paging-text"><?php echo esc_html( $paged ) ?> of <span
                                            class="total-pages"><?php echo esc_html( $total_page ) ?></span></span>
							</span>
						<?php /*Next button*/
						if ( $per_page * $paged < $count ) {
							$n_paged = $paged + 1;
						} else {
							$n_paged = 0;
						}
						if ( $n_paged ) {
							$n_url = add_query_arg(
								array(
									'page'  => 'woocommerce-boost-sales-crosssell',
									'paged' => $n_paged
								), admin_url( 'admin.php' )
							); ?>
                            <a class="next-page" href="<?php echo esc_url( $n_url ) ?>"><span
                                        class="screen-reader-text"><?php esc_html_e( 'Next Page', 'woocommerce-boost-sales' ) ?></span><span
                                        aria-hidden="true">›</span></a>
						<?php } else { ?>
                            <span class="tablenav-pages-navspan" aria-hidden="true">›</span>
						<?php } ?>
                    </div>
                </div>
            </div>
        </div>
	<?php }
}