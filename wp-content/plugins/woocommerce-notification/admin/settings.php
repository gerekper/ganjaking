<?php

/*
Class Name: WP_SM_Admin_Settings
Author: Andy Ha (support@villatheme.com)
Author URI: http://villatheme.com
Copyright 2016-2019 villatheme.com. All rights reserved.
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_WNOTIFICATION_Admin_Settings {
	static $params;

	public function __construct() {
		add_action( 'admin_init', array( $this, 'save_meta_boxes' ) );
		add_action( 'wp_ajax_wcn_search_product', array( $this, 'search_product' ) );
		add_action( 'wp_ajax_wcn_search_product_parent', array( $this, 'search_product_parent' ) );
		add_action( 'wp_ajax_wcn_search_cate', array( $this, 'search_cate' ) );
	}

	/**
	 * Search product category ajax
	 */

	public function search_cate() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		ob_start();
		$keyword = isset($_GET['keyword'])? sanitize_text_field($_GET['keyword']):'';

		if ( empty( $keyword ) ) {
			die();
		}
		$categories = get_terms(
			array(
				'taxonomy' => 'product_cat',
				'orderby'  => 'name',
				'order'    => 'ASC',
				'search'   => $keyword,
				'number'   => 100
			)
		);
		$items      = array();
		if ( count( $categories ) ) {
			foreach ( $categories as $category ) {
				$item    = array(
					'id'   => $category->term_id,
					'text' => $category->name
				);
				$items[] = $item;
			}
		}
		wp_send_json( $items );
		die;
	}

	/*Ajax Product Search*/
	public function search_product( $x = '', $post_types = array( 'product' ) ) {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		ob_start();

		$keyword = isset($_GET['keyword'])? sanitize_text_field($_GET['keyword']):'';

		if ( empty( $keyword ) ) {
			die();
		}
		$arg            = array(
			'post_status'    => 'publish',
			'post_type'      => $post_types,
			'posts_per_page' => 50,
			's'              => $keyword

		);
		$the_query      = new WP_Query( $arg );
		$found_products = array();
		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$prd           = wc_get_product( get_the_ID() );
				$product_id    = get_the_ID();
				$product_title = get_the_title() . '(#' . $product_id . ')';
				$the_product   = new WC_Product( $product_id );
				if ( ! $the_product->is_in_stock() ) {
					$product_title .= ' (out-of-stock)';
				}

				if ( $prd->has_child() && $prd->is_type( 'variable' ) ) {
					$product_title    .= '(#VARIABLE)';
					$product          = array( 'id' => $product_id, 'text' => $product_title );
					$found_products[] = $product;
					$product_children = $prd->get_children();
					if ( count( $product_children ) ) {
						foreach ( $product_children as $product_child ) {
							if ( woocommerce_version_check() ) {
								$product = array(
									'id'   => $product_child,
									'text' => get_the_title( $product_child ) . '(#' . $product_child . ')'
								);

							} else {
								$child_wc  = wc_get_product( $product_child );
								$get_atts  = $child_wc->get_variation_attributes();
								$attr_name = array_values( $get_atts )[0];
								$product   = array(
									'id'   => $product_child,
									'text' => get_the_title() . ' - ' . $attr_name
								);

							}
							$found_products[] = $product;
						}

					}
				} else {
					$product          = array( 'id' => $product_id, 'text' => $product_title );
					$found_products[] = $product;
				}
			}
		}
		wp_send_json( $found_products );
		die;
	}

	/*Ajax Product Search*/
	public function search_product_parent( $x = '', $post_types = array( 'product' ) ) {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		ob_start();

		$keyword = isset($_GET['keyword'])? sanitize_text_field($_GET['keyword']):'';

		if ( empty( $keyword ) ) {
			die();
		}
		$arg            = array(
			'post_status'    => 'publish',
			'post_type'      => $post_types,
			'posts_per_page' => 50,
			's'              => $keyword

		);
		$the_query      = new WP_Query( $arg );
		$found_products = array();
		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();

				$product_id    = get_the_ID();
				$product_title = get_the_title() . '(#' . $product_id . ')';
				$the_product   = wc_get_product( $product_id );
				if ( $the_product->is_type( 'variation' ) ) {
					continue;
				}
				if ( ! $the_product->is_in_stock() ) {
					$product_title .= ' (out-of-stock)';
				}
				$product          = array( 'id' => $product_id, 'text' => $product_title );
				$found_products[] = $product;
			}
		}
		wp_send_json( $found_products );
		die;
	}

	/**
	 * Get files in directory
	 *
	 * @param $dir
	 *
	 * @return array|bool
	 */
	static private function scan_dir( $dir ) {
		$ignored = array( '.', '..', '.svn', '.htaccess', 'test-log.log' );

		$files = array();
		foreach ( scandir( $dir ) as $file ) {
			if ( in_array( $file, $ignored ) ) {
				continue;
			}
			$files[ $file ] = filemtime( $dir . '/' . $file );
		}
		arsort( $files );
		$files = array_keys( $files );

		return ( $files ) ? $files : false;
	}

	private function stripslashes_deep( $value ) {
		$value = is_array( $value ) ? array_map( 'stripslashes_deep', $value ) : stripslashes( $value );

		return $value;
	}

	/**
	 * Save post meta
	 *
	 * @param $post
	 *
	 * @return bool
	 */
	public function save_meta_boxes() {
		global $woocommerce_notification_settings;
		if ( ! isset( $_POST['_wnotification_nonce'] ) || ! isset( $_POST['wnotification_params'] ) ) {
			return false;
		}
		if ( ! wp_verify_nonce( $_POST['_wnotification_nonce'], 'wnotification_save_email_settings' ) ) {
			return false;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}
		$data                      = $_POST['wnotification_params'];
		$data['message_purchased'] = $this->stripslashes_deep( $data['message_purchased'] );
		$data['custom_shortcode']  = $this->stripslashes_deep( $data['custom_shortcode'] );
		$data['conditional_tags']  = $this->stripslashes_deep( $data['conditional_tags'] );
		$data ['virtual_name']     = $this->stripslashes_deep( $data['virtual_name'] );
		$data ['virtual_city']     = $this->stripslashes_deep( $data['virtual_city'] );
		$data ['custom_css']       = $this->stripslashes_deep( $data['custom_css'] );
		$data ['virtual_country']  = $this->stripslashes_deep( $data['virtual_country'] );
		update_option( '_woocommerce_notification_prefix', substr( md5( date( "YmdHis" ) ), 0, 10 ) );
		if ( isset( $data['check_key'] ) ) {
			unset( $data['check_key'] );
			delete_transient( '_site_transient_update_plugins' );
			delete_transient( 'villatheme_item_5846' );
			delete_option( 'woocommerce-notification_messages' );
		}
		//delete
		$data ['avartar_user_enable']  = $woocommerce_notification_settings['avartar_user_enable'] ?? '';
		$data ['avartar_count_notify']  = $woocommerce_notification_settings['avartar_count_notify'] ?? '3';
		$data ['avartar_message_title']  = $woocommerce_notification_settings['avartar_message_title'] ?? '';
		$data ['avartar_message_in_single']  = $woocommerce_notification_settings['avartar_message_in_single'] ?? '';
		$data ['avartar_message_in_archive_page']  = $woocommerce_notification_settings['avartar_message_in_archive_page'] ?? '';
		//delete
		update_option( 'wnotification_params', $data );
		if ( is_plugin_active( 'wp-fastest-cache/wpFastestCache.php' ) ) {
			$cache = new WpFastestCache();
			$cache->deleteCache( true );
		}
		$woocommerce_notification_settings = $data;
	}

	/**
	 * Set Nonce
	 * @return string
	 */
	protected static function set_nonce() {
		return wp_nonce_field( 'wnotification_save_email_settings', '_wnotification_nonce' );
	}

	/**
	 * Set field in meta box
	 *
	 * @param      $field
	 * @param bool $multi
	 *
	 * @return string
	 */
	protected static function set_field( $field, $multi = false ) {
		if ( $field ) {
			if ( $multi ) {
				return 'wnotification_params[' . $field . '][]';
			} else {
				return 'wnotification_params[' . $field . ']';
			}
		} else {
			return '';
		}
	}

	/**
	 * Get Post Meta
	 *
	 * @param $field
	 *
	 * @return bool
	 */
	public static function get_field( $field, $default = '' ) {
		$params = get_option( 'wnotification_params', array() );

		if ( self::$params ) {
			$params = self::$params;
		} else {
			self::$params = $params;
		}
		if ( isset( $params[ $field ] ) && $field ) {
			return $params[ $field ];
		} else {
			return $default;
		}
	}


	/**
	 *
	 */

	public static function page_callback() {
		self::$params = get_option( 'wnotification_params', array() );
		?>
        <div class="wrap woocommerce-notification">
            <h2><?php esc_attr_e( 'WooCommerce Notification Settings', 'woocommerce-notification' ) ?></h2>
            <form method="post" action="" class="vi-ui form">
				<?php echo ent2ncr( self::set_nonce() ) ?>

                <div class="vi-ui attached tabular menu">
                    <div class="item active" data-tab="general">
                        <a href="#general"><?php esc_html_e( 'General', 'woocommerce-notification' ) ?></a>
                    </div>
                    <div class="item" data-tab="design">
                        <a href="#design"><?php esc_html_e( 'Design', 'woocommerce-notification' ) ?></a>
                    </div>
                    <div class="item" data-tab="messages">
                        <a href="#messages"><?php esc_html_e( 'Messages', 'woocommerce-notification' ) ?></a>
                    </div>
                    <div class="item" data-tab="products">
                        <a href="#products"><?php esc_html_e( 'Products', 'woocommerce-notification' ) ?></a>
                    </div>
                    <div class="item" data-tab="product-detail">
                        <a href="#product-detail"><?php esc_html_e( 'Product Detail', 'woocommerce-notification' ) ?></a>
                    </div>
                    <div class="item" data-tab="time">
                        <a href="#time"><?php esc_html_e( 'Time', 'woocommerce-notification' ) ?></a>
                    </div>
                    <div class="item" data-tab="sound">
                        <a href="#sound"><?php esc_html_e( 'Sound', 'woocommerce-notification' ) ?></a>
                    </div>
                    <div class="item" data-tab="assign">
                        <a href="#assign"><?php esc_html_e( 'Assign', 'woocommerce-notification' ) ?></a>
                    </div>
                    <div class="item" data-tab="logs">
                        <a href="#logs"><?php esc_html_e( 'Report', 'woocommerce-notification' ) ?></a>
                    </div>
                    <div class="item" data-tab="update">
                        <a href="#update"><?php esc_html_e( 'Update', 'woocommerce-notification' ) ?></a>
                    </div>
                </div>
                <div class="vi-ui bottom attached tab segment active" data-tab="general">
                    <!-- Tab Content !-->
                    <table class="optiontable form-table">
                        <tbody>
                        <tr valign="top">
                            <th scope="row">
                                <label for="<?php echo self::set_field( 'enable' ) ?>">
									<?php esc_html_e( 'Enable', 'woocommerce-notification' ) ?>
                                </label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input id="<?php echo self::set_field( 'enable' ) ?>"
                                           type="checkbox" <?php checked( self::get_field( 'enable' ), 1 ) ?>
                                           tabindex="0" class="vi_hidden" value="1"
                                           name="<?php echo self::set_field( 'enable' ) ?>"/>
                                    <label></label>
                                </div>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="<?php echo self::set_field( 'enable_mobile' ) ?>">
									<?php esc_html_e( 'Mobile', 'woocommerce-notification' ) ?>
                                </label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input id="<?php echo self::set_field( 'enable_mobile' ) ?>"
                                           type="checkbox" <?php checked( self::get_field( 'enable_mobile' ), 1 ) ?>
                                           tabindex="0" class="vi_hidden" value="1"
                                           name="<?php echo self::set_field( 'enable_mobile' ) ?>"/>
                                    <label></label>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <!--Products-->
                <div class="vi-ui bottom attached tab segment" data-tab="products">
                    <!-- Tab Content !-->
                    <?php
                    $product_visibility = self::get_field('product_visibility',['visible','catalog','search']);
                    ?>
                    <table class="optiontable form-table">
                        <tbody>
                        <tr valign="top">
                            <th scope="row">
                                <label><?php esc_html_e( 'Show Products', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <select name="<?php echo self::set_field( 'archive_page' ) ?>"
                                        class="vi-ui fluid dropdown">
                                    <option <?php selected( self::get_field( 'archive_page' ), 0 ) ?>
                                            value="0"><?php esc_attr_e( 'Get from Billing', 'woocommerce-notification' ) ?></option>
                                    <option <?php selected( self::get_field( 'archive_page' ), 1 ) ?>
                                            value="1"><?php esc_attr_e( 'Select Products', 'woocommerce-notification' ) ?></option>
                                    <option <?php selected( self::get_field( 'archive_page' ), 2 ) ?>
                                            value="2"><?php esc_attr_e( 'Latest Products', 'woocommerce-notification' ) ?></option>
                                    <option <?php selected( self::get_field( 'archive_page' ), 3 ) ?>
                                            value="3"><?php esc_attr_e( 'Select Categories', 'woocommerce-notification' ) ?></option>
                                    <option <?php selected( self::get_field( 'archive_page' ), 4 ) ?>
                                            value="4"><?php esc_attr_e( 'Recently Viewed Products', 'woocommerce-notification' ) ?></option>
                                </select>

                                <p class="description"><?php esc_html_e( 'You can arrange product order or special product which you want to up-sell.', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="<?php echo self::set_field( 'enable_current_category' ) ?>"><?php esc_html_e( 'Current category', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input id="<?php echo self::set_field( 'enable_current_category' ) ?>"
                                           type="checkbox" <?php checked( self::get_field( 'enable_current_category' ), 1 ) ?>
                                           tabindex="0" class="vi_hidden" value="1"
                                           name="<?php echo self::set_field( 'enable_current_category' ) ?>"/>
                                    <label></label>
                                </div>
                                <p class="description"><?php esc_html_e( 'Notifications which are displayed on a category page are only related to the products of that category', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="<?php echo self::set_field( 'enable_out_of_stock_product' ) ?>"><?php esc_html_e( 'Out-of-stock products', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input id="<?php echo self::set_field( 'enable_out_of_stock_product' ) ?>"
                                           type="checkbox" <?php checked( self::get_field( 'enable_out_of_stock_product' ), 1 ) ?>
                                           tabindex="0" class="vi_hidden" value="1"
                                           name="<?php echo self::set_field( 'enable_out_of_stock_product' ) ?>"/>
                                    <label></label>
                                </div>
                                <p class="description"><?php esc_html_e( 'Turn on to show out-of-stock products on notifications.', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="<?php echo self::set_field( 'product_visibility' ) ?>"><?php esc_html_e( 'Product visibility', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <select name="<?php echo self::set_field( 'product_visibility',true ) ?>"
                                        class="vi-ui fluid dropdown" multiple>
                                    <option <?php selected( in_array('visible',$product_visibility), true ) ?>
                                            value="visible"><?php esc_attr_e( 'Shop and search results', 'woocommerce-notification' ) ?></option>
                                    <option <?php selected( in_array('catalog',$product_visibility), true ) ?>
                                            value="catalog"><?php esc_attr_e( 'Shop only', 'woocommerce-notification' ) ?></option>
                                    <option <?php selected( in_array('search',$product_visibility), true ) ?>
                                            value="search"><?php esc_attr_e( 'Search results only', 'woocommerce-notification' ) ?></option>
                                    <option <?php selected( in_array('hidden',$product_visibility), true ) ?>
                                            value="hidden"><?php esc_attr_e( 'Hidden', 'woocommerce-notification' ) ?></option>
                                </select>
                            </td>
                        </tr>
                        <!--	Select Categories-->
                        <tr valign="top" class="select-categories vi_hidden">
                            <th scope="row">
                                <label><?php esc_html_e( 'Select Categories', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
								<?php
								$cates = self::get_field( 'select_categories', array() ); ?>
                                <select multiple="multiple"
                                        name="<?php echo self::set_field( 'select_categories', true ) ?>"
                                        class="category-search"
                                        placeholder="<?php esc_attr_e( 'Please select category', 'woocommerce-notification' ) ?>">
									<?php
									if ( count( $cates ) ) {
										$categories = get_terms(
											array(
												'taxonomy' => 'product_cat',
												'include'  => $cates,
											)
										);
										if ( count( $categories ) ) {
											foreach ( $categories as $category ) { ?>
                                                <option selected="selected"
                                                        value="<?php echo esc_attr( $category->term_id ) ?>"><?php echo esc_html( $category->name ) ?></option>
												<?php
											}
										}
									} ?>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top" class="vi_hidden select-categories">
                            <th scope="row">
                                <label><?php esc_html_e( 'Exclude Products', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
								<?php $products = self::get_field( 'cate_exclude_products', array() ); ?>
                                <select multiple="multiple"
                                        name="<?php echo self::set_field( 'cate_exclude_products', true ) ?>"
                                        class="product-search-parent">
									<?php if ( count( $products ) ) {
										$args_p      = array(
											'post_type'      => array( 'product' ),
											'post_status'    => 'publish',
											'post__in'       => $products,
											'posts_per_page' => - 1,
										);
										$the_query_p = new WP_Query( $args_p );
										if ( $the_query_p->have_posts() ) {
											$products = $the_query_p->posts;
											foreach ( $products as $product ) {
												$data = wc_get_product( $product );
												if ( $data ) {
													$product_id = $data->get_id();
													if ( woocommerce_version_check() ) {
														if ( $data->get_type() == 'variation' ) {
															$name_prd = $data->get_name();
														} else {
															$name_prd = $data->get_title();
														}
														$name_prd .= '(#' . $product_id . ')';
														if ( ! $data->is_in_stock() ) {
															$name_prd .= ' (out-of-stock)';
														}
													} else {
														$prd_var_title = $data->post->post_title;
														$prd_var_title .= '(#' . $product_id . ')';
														if ( $data->get_type() == 'variation' ) {
															$prd_var_attr = $data->get_variation_attributes();
															$attr_name1   = array_values( $prd_var_attr )[0];
															$name_prd     = $prd_var_title . ' - ' . $attr_name1;
														} else {
															$name_prd = $prd_var_title;
														}
													}
													if ( $data->is_type( 'variable' ) ) {
														$name_prd .= '(#VARIABLE)';
													}
													?>
                                                    <option selected="selected"
                                                            value="<?php echo esc_attr( $product_id ) ?>"><?php echo esc_html( $name_prd ) ?></option>
													<?php
												}
											}
										}
										// Reset Post Data
										wp_reset_postdata();
									} ?>
                                </select>

                                <p class="description"><?php esc_html_e( 'These products will not display on notification.', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        <tr valign="top" class="vi_hidden latest-product-select-categories">
                            <th scope="row">
                                <label><?php esc_html_e( 'Product limit', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <input id="<?php echo self::set_field( 'limit_product' ) ?>" type="number" tabindex="0"
                                       min="0"
                                       value="<?php echo self::get_field( 'limit_product', 50 ) ?>"
                                       name="<?php echo self::set_field( 'limit_product' ) ?>"/>

                                <p class="description"><?php esc_html_e( 'Product quantity will be got in list latest products.', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        <tr valign="top" class="vi_hidden exclude_products">
                            <th scope="row">
                                <label><?php esc_html_e( 'Exclude Products', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
								<?php $products = self::get_field( 'exclude_products', array() ); ?>
                                <select multiple="multiple"
                                        name="<?php echo self::set_field( 'exclude_products', true ) ?>"
                                        class="product-search-parent">
									<?php if ( count( $products ) ) {
										$args_p      = array(
											'post_type'      => array( 'product', 'product_variation' ),
											'post_status'    => 'publish',
											'post__in'       => $products,
											'posts_per_page' => - 1,
										);
										$the_query_p = new WP_Query( $args_p );
										if ( $the_query_p->have_posts() ) {
											$products = $the_query_p->posts;
											foreach ( $products as $product ) {
												$data = wc_get_product( $product );
												if ( $data ) {
													$product_id = $data->get_id();
													if ( woocommerce_version_check() ) {
														if ( $data->get_type() == 'variation' ) {
															$name_prd = $data->get_name();
														} else {
															$name_prd = $data->get_title();
														}
														$name_prd .= '(#' . $product_id . ')';
														if ( ! $data->is_in_stock() ) {
															$name_prd .= ' (out-of-stock)';
														}
													} else {
														$prd_var_title = $data->post->post_title;
														$prd_var_title .= '(#' . $product_id . ')';
														if ( $data->get_type() == 'variation' ) {
															$prd_var_attr = $data->get_variation_attributes();
															$attr_name1   = array_values( $prd_var_attr )[0];
															$name_prd     = $prd_var_title . ' - ' . $attr_name1;
														} else {
															$name_prd = $prd_var_title;
														}
													}
													if ( $data->is_type( 'variable' ) ) {
														$name_prd .= '(#VARIABLE)';
													}

													?>
                                                    <option selected="selected"
                                                            value="<?php echo esc_attr( $product_id ) ?>"><?php echo esc_html( $name_prd ) ?></option>
													<?php
												}
											}
										}
										// Reset Post Data
										wp_reset_postdata();
									} ?>
                                </select>

                                <p class="description"><?php esc_html_e( 'These products will not show on notification.', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="<?php echo self::set_field( 'product_link' ) ?>"><?php esc_html_e( 'External link', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input id="<?php echo self::set_field( 'product_link' ) ?>"
                                           type="checkbox" <?php checked( self::get_field( 'product_link' ), 1 ) ?>
                                           tabindex="0" class="vi_hidden" value="1"
                                           name="<?php echo self::set_field( 'product_link' ) ?>"/>
                                    <label></label>
                                </div>
                                <p class="description"><?php esc_html_e( 'Working with  External/Affiliate product. Product link is product URL.', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        <tr valign="top" class="get_from_billing vi_hidden">
                            <th scope="row">
                                <label><?php esc_html_e( 'Order Time', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <div class="fields">
                                    <div class="twelve wide field">
                                        <input type="number" min="0"
                                               value="<?php echo self::get_field( 'order_threshold_num', 30 ) ?>"
                                               name="<?php echo self::set_field( 'order_threshold_num' ) ?>"/>
                                    </div>
                                    <div class="two wide field">
                                        <select name="<?php echo self::set_field( 'order_threshold_time' ) ?>"
                                                class="vi-ui fluid dropdown">
                                            <option <?php selected( self::get_field( 'order_threshold_time' ), 0 ) ?>
                                                    value="0"><?php esc_attr_e( 'Hours', 'woocommerce-notification' ) ?></option>
                                            <option <?php selected( self::get_field( 'order_threshold_time' ), 1 ) ?>
                                                    value="1"><?php esc_attr_e( 'Days', 'woocommerce-notification' ) ?></option>
                                            <option <?php selected( self::get_field( 'order_threshold_time' ), 2 ) ?>
                                                    value="2"><?php esc_attr_e( 'Minutes', 'woocommerce-notification' ) ?></option>
                                        </select>
                                    </div>
                                </div>
                                <p class="description"><?php esc_html_e( 'Products in this recently time will get from order.  ', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        <tr valign="top" class="get_from_billing vi_hidden">
                            <th scope="row">
                                <label><?php esc_html_e( 'Order Status', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
								<?php
								$order_statuses = self::get_field( 'order_statuses', array( 'wc-completed' ) );
								$statuses       = wc_get_order_statuses();

								?>
                                <select multiple="multiple"
                                        name="<?php echo self::set_field( 'order_statuses', true ) ?>"
                                        class="vi-ui fluid dropdown">
									<?php foreach ( $statuses as $k => $status ) {
										$selected = '';
										if ( in_array( $k, $order_statuses ) ) {
											$selected = 'selected="selected"';
										}
										?>
                                        <option <?php echo $selected; ?>
                                                value="<?php echo esc_attr( $k ) ?>"><?php echo esc_html( $status ) ?></option>
									<?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top" class="select_only_product vi_hidden">
                            <th scope="row">
                                <label><?php esc_html_e( 'Select Products', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
								<?php
								$products_ach = self::get_field( 'archive_products', array() ); ?>
                                <select multiple="multiple"
                                        name="<?php echo self::set_field( 'archive_products', true ) ?>"
                                        class="product-search"
                                        placeholder="<?php esc_attr_e( 'Please select products', 'woocommerce-notification' ) ?>">
									<?php if ( count( $products_ach ) ) {
										$args_p      = array(
											'post_type'      => array( 'product', 'product_variation' ),
											'post_status'    => 'publish',
											'post__in'       => $products_ach,
											'posts_per_page' => - 1,
										);
										$the_query_p = new WP_Query( $args_p );
										if ( $the_query_p->have_posts() ) {
											$products_ach = $the_query_p->posts;
											foreach ( $products_ach as $product_ach ) {
												$data_ach = wc_get_product( $product_ach );
												if ( woocommerce_version_check() ) {
													if ( $data_ach->get_type() == 'variation' ) {
														$name_prd = $data_ach->get_name();
													} else {
														$name_prd = $data_ach->get_title();
													}
													if ( ! $data_ach->is_in_stock() ) {
														$name_prd .= ' (out-of-stock)';
													}
												} else {
													$prd_var_title = $data_ach->post->post_title;
													if ( $data_ach->get_type() == 'variation' ) {
														$prd_var_attr = $data_ach->get_variation_attributes();
														$attr_name1   = array_values( $prd_var_attr )[0];
														$name_prd     = $prd_var_title . ' - ' . $attr_name1;
													} else {
														$name_prd = $prd_var_title;
													}
												}
												if ( $data_ach ) { ?>
                                                    <option selected="selected"
                                                            value="<?php echo esc_attr( $data_ach->get_id() ); ?>"><?php echo esc_html( $name_prd ); ?></option>
												<?php }
											}
										}
										// Reset Post Data
										wp_reset_postdata();
									} ?>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top" class="select_product vi_hidden">
                            <th scope="row">
                                <label><?php esc_html_e( 'Virtual First Name', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
								<?php
								$first_names = self::get_field( 'virtual_name' )
								?>
                                <textarea
                                        name="<?php echo self::set_field( 'virtual_name' ) ?>"><?php echo $first_names ?></textarea>

                                <p class="description"><?php esc_html_e( 'Virtual first name what will show on notification. Each first name on a line.', 'woocommerce-notification' ) ?></p>
								<?php
								/*WPML.org*/
								if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
									$languages = $langs = icl_get_languages( 'skip_missing=N&orderby=KEY&order=DIR&link_empty_to=str' );

									if ( count( $languages ) ) {
										foreach ( $languages as $key => $language ) {
											if ( $language['active'] ) {
												continue;
											}
											$wpml_name = self::get_field( 'virtual_name_' . $key );
											if ( ! $wpml_name ) {
												$wpml_name = $first_names;
											}
											?>
                                            <h4><?php echo esc_html( $language['native_name'] ) ?></h4>
                                            <textarea
                                                    name="<?php echo self::set_field( 'virtual_name_' . $key ) ?>"><?php echo $wpml_name ?></textarea>
										<?php }
									}
								} /*Polylang*/
                                elseif ( class_exists( 'Polylang' ) ) {
									$languages = pll_languages_list();

									foreach ( $languages as $language ) {
										$default_lang = pll_default_language( 'slug' );

										if ( $language == $default_lang ) {
											continue;
										}
										$wpml_name = self::get_field( 'virtual_name_' . $language );
										if ( ! $wpml_name ) {
											$wpml_name = $first_names;
										}
										?>
                                        <h4><?php echo esc_html( $language ) ?></h4>
                                        <textarea
                                                name="<?php echo self::set_field( 'virtual_name_' . $language ) ?>"><?php echo $wpml_name ?></textarea>
										<?php
									}
								}
								?>
                            </td>
                        </tr>
                        <tr valign="top" class="select_product vi_hidden">
                            <th scope="row">
                                <label><?php esc_html_e( 'Virtual Time', 'woocommerce-notification' ) ?></label></th>
                            <td>
                                <div class="vi-ui form">
                                    <div class="inline fields">
                                        <input type="number" name="<?php echo self::set_field( 'virtual_time' ) ?>"
                                               min="0"
                                               value="<?php echo self::get_field( 'virtual_time', '10' ) ?>"/>
                                        <label><?php esc_html_e( 'hours', 'woocommerce-notification' ) ?></label>
                                    </div>
                                </div>
                                <p class="description"><?php esc_html_e( 'Time will auto get random in this time threshold ago.', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="<?php echo self::set_field( 'change_virtual_time_enable' ) ?>"><?php esc_html_e( 'Auto change Virtual Time', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input id="<?php echo self::set_field( 'change_virtual_time_enable' ) ?>"
                                           type="checkbox" <?php checked( self::get_field( 'change_virtual_time_enable' ), 1 ) ?>
                                           tabindex="0" class="vi_hidden" value="1"
                                           name="<?php echo self::set_field( 'change_virtual_time_enable' ) ?>"/>
                                    <label></label>
                                </div>
                                <p class="description"><?php esc_html_e( 'Auto change Virtual Time for site Timezone', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        <tr valign="top" class="select_product vi_hidden">
                            <th scope="row">
                                <label><?php esc_html_e( 'Address', 'woocommerce-notification' ) ?></label></th>
                            <td>
                                <select name="<?php echo self::set_field( 'country' ) ?>" class="vi-ui fluid dropdown">
                                    <option <?php selected( self::get_field( 'country' ), 0 ) ?>
                                            value="0"><?php esc_attr_e( 'Auto Detect', 'woocommerce-notification' ) ?></option>
                                    <option <?php selected( self::get_field( 'country' ), 2 ) ?>
                                            value="2"><?php esc_attr_e( 'WooCommerce Geolocation', 'woocommerce-notification' ) ?></option>
                                    <option <?php selected( self::get_field( 'country' ), 1 ) ?>
                                            value="1"><?php esc_attr_e( 'Virtual', 'woocommerce-notification' ) ?></option>
                                </select>

                                <p class="description"><?php esc_html_e( 'You can use auto detect address or make virtual address of customer.', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        <tr valign="top" class="virtual_address vi_hidden">
                            <th scope="row">
                                <label><?php esc_html_e( 'Virtual City', 'woocommerce-notification' ) ?></label></th>
                            <td>
								<?php
								$virtual_city = self::get_field( 'virtual_city' );
								?>
                                <textarea
                                        name="<?php echo self::set_field( 'virtual_city' ) ?>"><?php echo esc_attr( $virtual_city ) ?></textarea>

                                <p class="description"><?php esc_html_e( 'Virtual city name what will show on notification. Each city name on a line.', 'woocommerce-notification' ) ?></p>
								<?php
								/*WPML.org*/
								if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
									$languages = $langs = icl_get_languages( 'skip_missing=N&orderby=KEY&order=DIR&link_empty_to=str' );

									if ( count( $languages ) ) {
										foreach ( $languages as $key => $language ) {
											if ( $language['active'] ) {
												continue;
											}
											$wpml_city = self::get_field( 'virtual_city_' . $key );
											if ( ! $wpml_city ) {
												$wpml_city = $virtual_city;
											}
											?>
                                            <h4><?php echo esc_html( $language['native_name'] ) ?></h4>
                                            <textarea
                                                    name="<?php echo self::set_field( 'virtual_city_' . $key ) ?>"><?php echo $wpml_city ?></textarea>
										<?php }
									}
								} /*Polylang*/
                                elseif ( class_exists( 'Polylang' ) ) {
									$languages = pll_languages_list();

									foreach ( $languages as $language ) {
										$default_lang = pll_default_language( 'slug' );

										if ( $language == $default_lang ) {
											continue;
										}

										$wpml_city = self::get_field( 'virtual_city_' . $language );
										if ( ! $wpml_city ) {
											$wpml_city = $virtual_city;
										}
										?>
                                        <h4><?php echo esc_html( $language ) ?></h4>
                                        <textarea
                                                name="<?php echo self::set_field( 'virtual_city_' . $language ) ?>"><?php echo $wpml_city ?></textarea>
										<?php
									}
								} ?>
                            </td>
                        </tr>
                        <tr valign="top" class="virtual_address vi_hidden">
                            <th scope="row">
                                <label><?php esc_html_e( 'Virtual Country', 'woocommerce-notification' ) ?></label></th>
                            <td>
								<?php $virtual_country = self::get_field( 'virtual_country' ) ?>
                                <input type="text" name="<?php echo self::set_field( 'virtual_country' ) ?>"
                                       value="<?php echo esc_attr( $virtual_country ) ?>"/>

                                <p class="description"><?php esc_html_e( 'Virtual country name what will show on notification.', 'woocommerce-notification' ) ?></p>
								<?php /*WPML.org*/
								if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
									$languages = $langs = icl_get_languages( 'skip_missing=N&orderby=KEY&order=DIR&link_empty_to=str' );

									if ( count( $languages ) ) {
										foreach ( $languages as $key => $language ) {
											if ( $language['active'] ) {
												continue;
											}
											$wpml_country = self::get_field( 'virtual_country_' . $key );
											if ( ! $wpml_country ) {
												$wpml_country = $virtual_country;
											}
											?>
                                            <label><?php echo esc_html( $language['native_name'] ) ?></label>
                                            <input type="text"
                                                   name="<?php echo self::set_field( 'virtual_country_' . $key ) ?>"
                                                   value="<?php echo esc_attr( $wpml_country ) ?>"/>
										<?php }
									}
								} elseif ( class_exists( 'Polylang' ) ) {

									$languages = pll_languages_list();

									foreach ( $languages as $language ) {
										//										$cur_language = pll_current_language( 'slug' );
										$default_lang = pll_default_language( 'slug' );

										if ( $language == $default_lang ) {
											continue;
										}
										$wpml_country = self::get_field( 'virtual_country_' . $language );
										if ( ! $wpml_country ) {
											$wpml_country = $virtual_country;
										}
										?>
                                        <h4><?php echo esc_html( $language ) ?></h4>
                                        <input type="text"
                                               name="<?php echo self::set_field( 'virtual_country_' . $language ) ?>"
                                               value="<?php echo esc_attr( $wpml_country ) ?>"/>
										<?php
									}
								} ?>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label><?php esc_html_e( 'Product image size', 'woocommerce-notification' ) ?></label>
                            </th>

                            <td>
								<?php global $_wp_additional_image_sizes; ?>
                                <select name="<?php echo self::set_field( 'product_sizes' ) ?>"
                                        class="vi-ui fluid dropdown">
                                    <option <?php selected( self::get_field( 'product_sizes' ), 'shop_thumbnail' ) ?>
                                            value="shop_thumbnail"><?php esc_attr_e( 'shop_thumbnail', 'woocommerce-notification' ) ?>
                                        - <?php echo isset( $_wp_additional_image_sizes['shop_thumbnail'] ) ? $_wp_additional_image_sizes['shop_thumbnail']['width'] . 'x' . $_wp_additional_image_sizes['shop_thumbnail']['height'] : ''; ?></option>
                                    <option <?php selected( self::get_field( 'product_sizes' ), 'shop_catalog' ) ?>
                                            value="shop_catalog"><?php esc_attr_e( 'shop_catalog', 'woocommerce-notification' ) ?>
                                        - <?php echo isset( $_wp_additional_image_sizes['shop_catalog'] ) ? $_wp_additional_image_sizes['shop_catalog']['width'] . 'x' . $_wp_additional_image_sizes['shop_catalog']['height'] : ''; ?></option>
                                    <option <?php selected( self::get_field( 'product_sizes' ), 'shop_single' ) ?>
                                            value="shop_single"><?php esc_attr_e( 'shop_single', 'woocommerce-notification' ) ?>
                                        - <?php echo isset( $_wp_additional_image_sizes['shop_single'] ) ? $_wp_additional_image_sizes['shop_single']['width'] . 'x' . $_wp_additional_image_sizes['shop_single']['height'] : ''; ?></option>
                                </select>

                                <p class="description"><?php esc_html_e( 'Image size will get form your WordPress site.', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="<?php echo self::set_field( 'non_ajax' ) ?>"><?php esc_html_e( 'Non Ajax', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input id="<?php echo self::set_field( 'non_ajax' ) ?>"
                                           type="checkbox" <?php checked( self::get_field( 'non_ajax' ), 1 ) ?>
                                           tabindex="0" class="vi_hidden" value="1"
                                           name="<?php echo self::set_field( 'non_ajax' ) ?>"/>
                                    <label></label>
                                </div>
                                <p class="description"><?php esc_html_e( 'Load popup will not use ajax. Your site will be load faster. It creates cache. It is not working with Get product from Billing feature and options of Product detail tab.', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Product detail !-->
                <div class="vi-ui bottom attached tab segment" data-tab="product-detail">
                    <!-- Tab Content !-->
                    <table class="optiontable form-table">
                        <tbody>
                        <tr valign="top">
                            <th scope="row">
                                <label for="<?php echo self::set_field( 'enable_single_product' ) ?>"><?php esc_html_e( 'Run single product', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input id="<?php echo self::set_field( 'enable_single_product' ) ?>"
                                           type="checkbox" <?php checked( self::get_field( 'enable_single_product' ), 1 ) ?>
                                           tabindex="0" class="vi_hidden" value="1"
                                           name="<?php echo self::set_field( 'enable_single_product' ) ?>"/>
                                    <label></label>
                                </div>
                                <p class="description"><?php esc_html_e( 'Notification will only display current product in product detail page that they are viewing.', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="<?php echo self::set_field( 'notification_product_show_type' ) ?>"><?php esc_html_e( 'Notification show', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>

                                <select name="<?php echo self::set_field( 'notification_product_show_type' ) ?>"
                                        class="vi-ui fluid dropdown">
                                    <option <?php selected( self::get_field( 'notification_product_show_type', 0 ), '0' ) ?>
                                            value="0"><?php echo esc_html__( 'Current product', 'woocommerce-notification' ) ?></option>
                                    <option <?php selected( self::get_field( 'notification_product_show_type' ) ) ?>
                                            value="1"><?php echo esc_html__( 'Products in the same category', 'woocommerce-notification' ) ?></option>
                                </select>

                                <p class="description"><?php esc_html_e( 'In product single page, Notification can only display current product or other products in the same category.', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        <tr valign="top" class="only_current_product vi_hidden">
                            <th scope="row">
                                <label for="<?php echo self::set_field( 'show_variation' ) ?>"><?php esc_html_e( 'Show variation', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input id="<?php echo self::set_field( 'show_variation' ) ?>"
                                           type="checkbox" <?php checked( self::get_field( 'show_variation' ), 1 ) ?>
                                           tabindex="0" class="vi_hidden" value="1"
                                           name="<?php echo self::set_field( 'show_variation' ) ?>"/>
                                    <label></label>
                                </div>
                                <p class="description"><?php esc_html_e( 'Show variation instead of product variable.', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>

                        </tbody>
                    </table>
                </div>
                <!-- Design !-->
                <div class="vi-ui bottom attached tab segment" data-tab="design">
                    <!-- Tab Content !-->
                    <table class="optiontable form-table">
                        <tbody>
                        <tr valign="top">
                            <th scope="row">
                                <label><?php esc_html_e( 'Highlight color', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <input data-ele="highlight" type="text" class="color-picker"
                                       name="<?php echo self::set_field( 'highlight_color' ) ?>"
                                       value="<?php echo self::get_field( 'highlight_color', '#000000' ) ?>"
                                       style="background-color: <?php echo esc_attr( self::get_field( 'highlight_color', '#000000' ) ) ?>"/>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label><?php esc_html_e( 'Text color', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <input data-ele="textcolor"
                                       style="background-color: <?php echo esc_attr( self::get_field( 'text_color', '#000000' ) ) ?>"
                                       type="text" class="color-picker"
                                       name="<?php echo self::set_field( 'text_color' ) ?>"
                                       value="<?php echo self::get_field( 'text_color', '#000000' ) ?>"/>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label><?php esc_html_e( 'Background color', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <input style="background-color: <?php echo esc_attr( self::get_field( 'background_color', '#ffffff' ) ) ?>"
                                       data-ele="backgroundcolor" type="text" class="color-picker"
                                       name="<?php echo self::set_field( 'background_color' ) ?>"
                                       value="<?php echo self::get_field( 'background_color', '#ffffff' ) ?>"/>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label><?php esc_html_e( 'Image padding', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <div class="vi-ui fluid right labeled input">
                                    <input type="number" min="0" max="20"
                                           name="<?php echo self::set_field( 'image_padding' ) ?>"
                                           value="<?php echo self::get_field( 'image_padding', '0' ) ?>"/>
                                    <label class="vi-ui label"><?php esc_html_e( 'px', 'woocommerce-notification' ) ?></label>
                                </div>
                                <p class="description"><?php echo esc_html__( 'Gap between product image and notification\'s border', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        <tr valign="top" class="background-image">
                            <th scope="row">
                                <label><?php esc_html_e( 'Templates', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <div class="vi-ui grid">
                                    <div class="four wide column">
                                        <div class="vi-ui toggle checkbox center aligned segment">
                                            <input id="<?php echo self::set_field( 'background_image' ) ?>"
                                                   type="radio" <?php checked( self::get_field( 'background_image', 0 ), 0 ) ?>
                                                   tabindex="0" class="vi_hidden" value="0"
                                                   name="<?php echo self::set_field( 'background_image' ) ?>"/>
                                            <label><?php esc_attr_e( 'None', 'woocommerce-notification' ) ?></label>
                                        </div>

                                    </div>
									<?php
									$b_images = woocommerce_notification_background_images();
									foreach ( $b_images as $k => $b_image ) {
										?>
                                        <div class=" four wide column">
                                            <img src="<?php echo esc_url( $b_image ) ?>"
                                                 class="vi-ui centered medium  middle aligned "/>

                                            <div class="vi-ui toggle checkbox center aligned segment">
                                                <input id="<?php echo self::set_field( 'background_image' ) ?>"
                                                       type="radio" <?php checked( self::get_field( 'background_image' ), $k ) ?>
                                                       tabindex="0" class="vi_hidden"
                                                       value="<?php echo esc_attr( $k ) ?>"
                                                       name="<?php echo self::set_field( 'background_image' ) ?>"/>
                                                <label><?php echo ucwords( str_replace( '_', ' ', esc_attr( $k ) ) ) ?></label>
                                            </div>
                                        </div>
										<?php
									}
									?>
                                </div>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label><?php esc_html_e( 'Image Position', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <select name="<?php echo self::set_field( 'image_position' ) ?>"
                                        class="vi-ui fluid dropdown">
                                    <option <?php selected( self::get_field( 'image_position' ), 0 ) ?>
                                            value="0"><?php esc_attr_e( 'Left', 'woocommerce-notification' ) ?></option>
                                    <option <?php selected( self::get_field( 'image_position' ), 1 ) ?>
                                            value="1"><?php esc_attr_e( 'Right', 'woocommerce-notification' ) ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label><?php esc_html_e( 'Position', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <div class="vi-ui form">
                                    <div class="fields">
                                        <div class="four wide field">
                                            <img src="<?php echo VI_WNOTIFICATION_IMAGES . 'position_1.jpg' ?>"
                                                 class="vi-ui centered medium image middle aligned "/>

                                            <div class="vi-ui toggle checkbox center aligned segment">
                                                <input id="<?php echo self::set_field( 'position' ) ?>"
                                                       type="radio" <?php checked( self::get_field( 'position', 0 ), 0 ) ?>
                                                       tabindex="0" class="vi_hidden" value="0"
                                                       name="<?php echo self::set_field( 'position' ) ?>"/>
                                                <label><?php esc_attr_e( 'Bottom left', 'woocommerce-notification' ) ?></label>
                                            </div>

                                        </div>
                                        <div class="four wide field">
                                            <img src="<?php echo VI_WNOTIFICATION_IMAGES . 'position_2.jpg' ?>"
                                                 class="vi-ui centered medium image middle aligned "/>

                                            <div class="vi-ui toggle checkbox center aligned segment">
                                                <input id="<?php echo self::set_field( 'position' ) ?>"
                                                       type="radio" <?php checked( self::get_field( 'position' ), 1 ) ?>
                                                       tabindex="0" class="vi_hidden" value="1"
                                                       name="<?php echo self::set_field( 'position' ) ?>"/>
                                                <label><?php esc_attr_e( 'Bottom right', 'woocommerce-notification' ) ?></label>
                                            </div>
                                        </div>
                                        <div class="four wide field">
                                            <img src="<?php echo VI_WNOTIFICATION_IMAGES . 'position_4.jpg' ?>"
                                                 class="vi-ui centered medium image middle aligned "/>

                                            <div class="vi-ui toggle checkbox center aligned segment">
                                                <input id="<?php echo self::set_field( 'position' ) ?>"
                                                       type="radio" <?php checked( self::get_field( 'position' ), 2 ) ?>
                                                       tabindex="0" class="vi_hidden" value="2"
                                                       name="<?php echo self::set_field( 'position' ) ?>"/>
                                                <label><?php esc_attr_e( 'Top left', 'woocommerce-notification' ) ?></label>
                                            </div>
                                        </div>
                                        <div class="four wide field">
                                            <img src="<?php echo VI_WNOTIFICATION_IMAGES . 'position_3.jpg' ?>"
                                                 class="vi-ui centered medium image middle aligned "/>

                                            <div class="vi-ui toggle checkbox center aligned segment">
                                                <input id="<?php echo self::set_field( 'position' ) ?>"
                                                       type="radio" <?php checked( self::get_field( 'position' ), 3 ) ?>
                                                       tabindex="0" class="vi_hidden" value="3"
                                                       name="<?php echo self::set_field( 'position' ) ?>"/>
                                                <label><?php esc_attr_e( 'Top right', 'woocommerce-notification' ) ?></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="<?php echo self::set_field( 'rounded_corner' ) ?>">
									<?php esc_html_e( 'Rounded corner style', 'woocommerce-notification' ) ?>
                                </label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input id="<?php echo self::set_field( 'rounded_corner' ) ?>"
                                           type="checkbox" <?php checked( self::get_field( 'rounded_corner' ), 1 ) ?>
                                           tabindex="0" class="vi_hidden" value="1"
                                           name="<?php echo self::set_field( 'rounded_corner' ) ?>"/>
                                    <label></label>
                                </div>
                                <p class="description"><?php echo esc_html__( 'Message will be rounded and product image is round instead of square', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        <tr valign="top" class="wn-rounded-conner-depending">
                            <th scope="row">
                                <label><?php esc_html_e( 'Custom Rounded corner', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <div class="vi-ui fluid right labeled input">
                                    <input type="number" name="<?php echo self::set_field( 'border_radius' ) ?>" min="0"
                                           value="<?php echo self::get_field( 'border_radius', '0' ) ?>"/>
                                    <label class="vi-ui label"><?php esc_html_e( 'px', 'woocommerce-notification' ) ?></label>
                                </div>
                                <p class="description"><?php echo esc_html__( 'This option is used only if you do not select any background templates', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="<?php echo self::set_field( 'show_close_icon' ) ?>">
									<?php esc_html_e( 'Show Close Icon', 'woocommerce-notification' ) ?>
                                </label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input id="<?php echo self::set_field( 'show_close_icon' ) ?>"
                                           type="checkbox" <?php checked( self::get_field( 'show_close_icon' ), 1 ) ?>
                                           tabindex="0" class="vi_hidden" value="1"
                                           name="<?php echo self::set_field( 'show_close_icon' ) ?>"/>
                                    <label></label>
                                </div>
                            </td>
                        </tr>

                        <tr valign="top" class="show-close-icon vi_hidden">
                            <th scope="row">
                                <label><?php esc_html_e( 'Time close', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <div class="vi-ui fluid right labeled input">
                                    <input type="number" name="<?php echo self::set_field( 'time_close' ) ?>" min="0"
                                           value="<?php echo self::get_field( 'time_close', '24' ) ?>"/>
                                    <label class="vi-ui label"><?php esc_html_e( 'hour', 'woocommerce-notification' ) ?></label>
                                </div>
                            </td>
                        </tr>
                        <tr valign="top" class="show-close-icon vi_hidden">
                            <th scope="row">
                                <label><?php esc_html_e( 'Close icon color', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <input data-ele="close_icon_color"
                                       style="background-color: <?php echo esc_attr( self::get_field( 'close_icon_color', '#000000' ) ) ?>"
                                       type="text" class="color-picker"
                                       name="<?php echo self::set_field( 'close_icon_color' ) ?>"
                                       value="<?php echo self::get_field( 'close_icon_color', '#000000' ) ?>"/>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="<?php echo self::set_field( 'image_redirect' ) ?>">
									<?php esc_html_e( 'Image redirect', 'woocommerce-notification' ) ?>
                                </label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input id="<?php echo self::set_field( 'image_redirect' ) ?>"
                                           type="checkbox" <?php checked( self::get_field( 'image_redirect' ), 1 ) ?>
                                           tabindex="0" class="vi_hidden" value="1"
                                           name="<?php echo self::set_field( 'image_redirect' ) ?>"/>
                                    <label></label>
                                </div>
                                <p class="description"><?php echo esc_html__( 'When click image, you will redirect to product single page.', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="<?php echo self::set_field( 'image_redirect_target' ) ?>">
									<?php esc_html_e( 'Link target', 'woocommerce-notification' ) ?>
                                </label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input id="<?php echo self::set_field( 'image_redirect_target' ) ?>"
                                           type="checkbox" <?php checked( self::get_field( 'image_redirect_target' ), 1 ) ?>
                                           tabindex="0" class="vi_hidden" value="1"
                                           name="<?php echo self::set_field( 'image_redirect_target' ) ?>"/>
                                    <label></label>
                                </div>
                                <p class="description"><?php echo esc_html__( 'Open link on new tab.', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row">
                                <label for="<?php echo self::set_field( 'message_display_effect' ) ?>">
									<?php esc_html_e( 'Message display effect', 'woocommerce-notification' ) ?>
                                </label>
                            </th>
                            <td>
                                <select name="<?php echo self::set_field( 'message_display_effect' ) ?>"
                                        class="vi-ui fluid dropdown"
                                        id="<?php echo self::set_field( 'message_display_effect' ) ?>">
                                    <optgroup label="Bouncing Entrances">
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'bounceIn' ) ?>
                                                value="bounceIn"><?php esc_attr_e( 'bounceIn', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'bounceInDown' ) ?>
                                                value="bounceInDown"><?php esc_attr_e( 'bounceInDown', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'bounceInLeft' ) ?>
                                                value="bounceInLeft"><?php esc_attr_e( 'bounceInLeft', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'bounceInRight' ) ?>
                                                value="bounceInRight"><?php esc_attr_e( 'bounceInRight', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'bounceInUp' ) ?>
                                                value="bounceInUp"><?php esc_attr_e( 'bounceInUp', 'woocommerce-notification' ) ?></option>
                                    </optgroup>
                                    <optgroup label="Fading Entrances">
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'fade-in' ) ?>
                                                value="fade-in"><?php esc_attr_e( 'fadeIn', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'fadeInDown' ) ?>
                                                value="fadeInDown"><?php esc_attr_e( 'fadeInDown', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'fadeInDownBig' ) ?>
                                                value="fadeInDownBig"><?php esc_attr_e( 'fadeInDownBig', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'fadeInLeft' ) ?>
                                                value="fadeInLeft"><?php esc_attr_e( 'fadeInLeft', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'fadeInLeftBig' ) ?>
                                                value="fadeInLeftBig"><?php esc_attr_e( 'fadeInLeftBig', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'fadeInRight' ) ?>
                                                value="fadeInRight"><?php esc_attr_e( 'fadeInRight', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'fadeInRightBig' ) ?>
                                                value="fadeInRightBig"><?php esc_attr_e( 'fadeInRightBig', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'fadeInUp' ) ?>
                                                value="fadeInUp"><?php esc_attr_e( 'fadeInUp', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'fadeInUpBig' ) ?>
                                                value="fadeInUpBig"><?php esc_attr_e( 'fadeInUpBig', 'woocommerce-notification' ) ?></option>
                                    </optgroup>
                                    <optgroup label="Flippers">
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'flipInX' ) ?>
                                                value="flipInX"><?php esc_attr_e( 'flipInX', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'flipInY' ) ?>
                                                value="flipInY"><?php esc_attr_e( 'flipInY', 'woocommerce-notification' ) ?></option>
                                    </optgroup>
                                    <optgroup label="Lightspeed">
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'lightSpeedIn' ) ?>
                                                value="lightSpeedIn"><?php esc_attr_e( 'lightSpeedIn', 'woocommerce-notification' ) ?></option>
                                    </optgroup>
                                    <optgroup label="Rotating Entrances">
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'rotateIn' ) ?>
                                                value="rotateIn"><?php esc_attr_e( 'rotateIn', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'rotateInDownLeft' ) ?>
                                                value="rotateInDownLeft"><?php esc_attr_e( 'rotateInDownLeft', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'rotateInDownRight' ) ?>
                                                value="rotateInDownRight"><?php esc_attr_e( 'rotateInDownRight', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'rotateInUpLeft' ) ?>
                                                value="rotateInUpLeft"><?php esc_attr_e( 'rotateInUpLeft', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'rotateInUpRight' ) ?>
                                                value="rotateInUpRight"><?php esc_attr_e( 'rotateInUpRight', 'woocommerce-notification' ) ?></option>
                                    </optgroup>
                                    <optgroup label="Sliding Entrances">
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'slideInUp' ) ?>
                                                value="slideInUp"><?php esc_attr_e( 'slideInUp', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'slideInDown' ) ?>
                                                value="slideInDown"><?php esc_attr_e( 'slideInDown', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'slideInLeft' ) ?>
                                                value="slideInLeft"><?php esc_attr_e( 'slideInLeft', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'slideInRight' ) ?>
                                                value="slideInRight"><?php esc_attr_e( 'slideInRight', 'woocommerce-notification' ) ?></option>
                                    </optgroup>
                                    <optgroup label="Zoom Entrances">
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'zoomIn' ) ?>
                                                value="zoomIn"><?php esc_attr_e( 'zoomIn', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'zoomInDown' ) ?>
                                                value="zoomInDown"><?php esc_attr_e( 'zoomInDown', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'zoomInLeft' ) ?>
                                                value="zoomInLeft"><?php esc_attr_e( 'zoomInLeft', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'zoomInRight' ) ?>
                                                value="zoomInRight"><?php esc_attr_e( 'zoomInRight', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'zoomInUp' ) ?>
                                                value="zoomInUp"><?php esc_attr_e( 'zoomInUp', 'woocommerce-notification' ) ?></option>
                                    </optgroup>
                                    <optgroup label="Special">
                                        <option <?php selected( self::get_field( 'message_display_effect' ), 'rollIn' ) ?>
                                                value="rollIn"><?php esc_attr_e( 'rollIn', 'woocommerce-notification' ) ?></option>
                                    </optgroup>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="<?php echo self::set_field( 'message_hidden_effect' ) ?>">
									<?php esc_html_e( 'Message popup animation', 'woocommerce-notification' ) ?>
                                </label>
                            </th>
                            <td>
                                <select name="<?php echo self::set_field( 'message_hidden_effect' ) ?>"
                                        class="vi-ui fluid dropdown"
                                        id="<?php echo self::set_field( 'message_hidden_effect' ) ?>">
                                    <optgroup label="Bouncing Exits">
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'bounceOut' ) ?>
                                                value="bounceOut"><?php esc_attr_e( 'bounceOut', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'bounceOutDown' ) ?>
                                                value="bounceOutDown"><?php esc_attr_e( 'bounceOutDown', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'bounceOutLeft' ) ?>
                                                value="bounceOutLeft"><?php esc_attr_e( 'bounceOutLeft', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'bounceOutRight' ) ?>
                                                value="bounceOutRight"><?php esc_attr_e( 'bounceOutRight', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'bounceOutUp' ) ?>
                                                value="bounceOutUp"><?php esc_attr_e( 'bounceOutUp', 'woocommerce-notification' ) ?></option>
                                    </optgroup>
                                    <optgroup label="Fading Exits">
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'fade-out' ) ?>
                                                value="fade-out"><?php esc_attr_e( 'fadeOut', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'fadeOutDown' ) ?>
                                                value="fadeOutDown"><?php esc_attr_e( 'fadeOutDown', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'fadeOutDownBig' ) ?>
                                                value="fadeOutDownBig"><?php esc_attr_e( 'fadeOutDownBig', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'fadeOutLeft' ) ?>
                                                value="fadeOutLeft"><?php esc_attr_e( 'fadeOutLeft', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'fadeOutLeftBig' ) ?>
                                                value="fadeOutLeftBig"><?php esc_attr_e( 'fadeOutLeftBig', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'fadeOutRight' ) ?>
                                                value="fadeOutRight"><?php esc_attr_e( 'fadeOutRight', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'fadeOutRightBig' ) ?>
                                                value="fadeOutRightBig"><?php esc_attr_e( 'fadeOutRightBig', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'fadeOutUp' ) ?>
                                                value="fadeOutUp"><?php esc_attr_e( 'fadeOutUp', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'fadeOutUpBig' ) ?>
                                                value="fadeOutUpBig"><?php esc_attr_e( 'fadeOutUpBig', 'woocommerce-notification' ) ?></option>
                                    </optgroup>
                                    <optgroup label="Flippers">
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'flipOutX' ) ?>
                                                value="flipOutX"><?php esc_attr_e( 'flipOutX', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'flipOutY' ) ?>
                                                value="flipOutY"><?php esc_attr_e( 'flipOutY', 'woocommerce-notification' ) ?></option>
                                    </optgroup>
                                    <optgroup label="Lightspeed">
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'lightSpeedOut' ) ?>
                                                value="lightSpeedOut"><?php esc_attr_e( 'lightSpeedOut', 'woocommerce-notification' ) ?></option>
                                    </optgroup>
                                    <optgroup label="Rotating Exits">
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'rotateOut' ) ?>
                                                value="rotateOut"><?php esc_attr_e( 'rotateOut', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'rotateOutDownLeft' ) ?>
                                                value="rotateOutDownLeft"><?php esc_attr_e( 'rotateOutDownLeft', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'rotateOutDownRight' ) ?>
                                                value="rotateOutDownRight"><?php esc_attr_e( 'rotateOutDownRight', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'rotateOutUpLeft' ) ?>
                                                value="rotateOutUpLeft"><?php esc_attr_e( 'rotateOutUpLeft', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'rotateOutUpRight' ) ?>
                                                value="rotateOutUpRight"><?php esc_attr_e( 'rotateOutUpRight', 'woocommerce-notification' ) ?></option>
                                    </optgroup>
                                    <optgroup label="Sliding Exits">
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'slideOutUp' ) ?>
                                                value="slideOutUp"><?php esc_attr_e( 'slideOutUp', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'slideOutDown' ) ?>
                                                value="slideOutDown"><?php esc_attr_e( 'slideOutDown', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'slideOutLeft' ) ?>
                                                value="slideOutLeft"><?php esc_attr_e( 'slideOutLeft', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'slideOutRight' ) ?>
                                                value="slideOutRight"><?php esc_attr_e( 'slideOutRight', 'woocommerce-notification' ) ?></option>
                                    </optgroup>
                                    <optgroup label="Zoom Exits">
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'zoomOut' ) ?>
                                                value="zoomOut"><?php esc_attr_e( 'zoomOut', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'zoomOutDown' ) ?>
                                                value="zoomOutDown"><?php esc_attr_e( 'zoomOutDown', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'zoomOutLeft' ) ?>
                                                value="zoomOutLeft"><?php esc_attr_e( 'zoomOutLeft', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'zoomOutRight' ) ?>
                                                value="zoomOutRight"><?php esc_attr_e( 'zoomOutRight', 'woocommerce-notification' ) ?></option>
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'zoomOutUp' ) ?>
                                                value="zoomOutUp"><?php esc_attr_e( 'zoomOutUp', 'woocommerce-notification' ) ?></option>
                                    </optgroup>
                                    <optgroup label="Special">
                                        <option <?php selected( self::get_field( 'message_hidden_effect' ), 'rollOut' ) ?>
                                                value="rollOut"><?php esc_attr_e( 'rollOut', 'woocommerce-notification' ) ?></option>
                                    </optgroup>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="<?php echo self::set_field( 'custom_css' ) ?>">
									<?php esc_html_e( 'Custom CSS', 'woocommerce-notification' ) ?>
                                </label>
                            </th>
                            <td>
                                <textarea class=""
                                          name="<?php echo self::set_field( 'custom_css' ) ?>"><?php echo self::get_field( 'custom_css' ) ?></textarea>
                            </td>
                        </tr>
                        </tbody>
                    </table>
					<?php
					$class = array();
					switch ( self::get_field( 'position' ) ) {
						case 1:
							$class[] = 'bottom_right';
							break;
						case 2:
							$class[] = 'top_left';
							break;
						case 3:
							$class[] = 'top_right';
							break;
						default:
							$class[] = '';
					}
					$background_image = self::get_field( 'background_image' );
					if ( $background_image ) {
						$class[] = 'wn-extended';
						$class[] = 'wn-' . $background_image;
					}
					$class[] = 'vi-wn-show';

					if ( self::get_field( 'rounded_corner' ) ) {
						$class[] = 'wn-rounded-corner';
					}
					$class[] = 'wn-product-with-image';
					$class[] = self::get_field( 'image_position' ) ? 'img-right' : '';
					?>
                    <div class="<?php echo esc_attr( implode( ' ', $class ) ) ?>"
                         id="message-purchased"
                         data-effect_display="<?php echo esc_attr( self::get_field( 'message_display_effect' ) ); ?>"
                         data-effect_hidden="<?php echo esc_attr( self::get_field( 'message_hidden_effect' ) ); ?>">
                        <div class="message-purchase-main">
                            <span class="wn-notification-image-wrapper"><img class="wn-notification-image"
                                                                             src="<?php echo esc_url( VI_WNOTIFICATION_IMAGES . 'demo-image.jpg' ) ?>"></span>
                            <p class="wn-notification-message-container">Joe Doe in London, England purchased a
                                <a href="#">Ninja Silhouette</a>
                                <small>About 9 hours ago</small>
                            </p>
                        </div>
                        <div id="notify-close"></div>
                    </div>
                </div>
                <!-- Time !-->
                <div class="vi-ui bottom attached tab segment" data-tab="time">
                    <!-- Tab Content !-->
                    <table class="optiontable form-table">
                        <tbody>
                        <tr valign="top">
                            <th scope="row">
                                <label for="<?php echo self::set_field( 'loop' ) ?>"><?php esc_html_e( 'Loop', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input id="<?php echo self::set_field( 'loop' ) ?>"
                                           type="checkbox" <?php checked( self::get_field( 'loop' ), 1 ) ?> tabindex="0"
                                           class="vi_hidden" value="1" name="<?php echo self::set_field( 'loop' ) ?>"/>
                                    <label></label>
                                </div>
                            </td>
                        </tr>
                        <tr valign="top" class="vi_hidden time_loop">
                            <th scope="row">
                                <label for="<?php echo self::set_field( 'loop_session' ) ?>"><?php esc_html_e( 'Loop by session', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input id="<?php echo self::set_field( 'loop_session' ) ?>"
                                           type="checkbox" <?php checked( self::get_field( 'loop_session' ), 1 ) ?>
                                           tabindex="0"
                                           class="vi_hidden" value="1"
                                           name="<?php echo self::set_field( 'loop_session' ) ?>"/>
                                    <label></label>
                                </div>
                                <p class="description"><?php esc_html_e( '"Next time display" applies for notifications of the whole site instead of each page', 'woocommerce-notification' ) ?></p>
                                <p class="description"><?php esc_html_e( '"Initial time" is applied only for the first notification of a session', 'woocommerce-notification' ) ?></p>
                                <p class="description"><?php esc_html_e( 'Count variable for "Notifications per page" is reset after a session expires', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        <tr valign="top" class="vi_hidden time_loop">
                            <th scope="row">
                                <label><?php esc_html_e( 'Notifications per session', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <input type="number" name="<?php echo self::set_field( 'loop_session_total' ) ?>"
                                       min="0"
                                       value="<?php echo self::get_field( 'loop_session_total', 60 ) ?>"/>
                                <p class="description"><?php esc_html_e( 'Number of notifications in a session.', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        <tr valign="top" class="vi_hidden time_loop">
                            <th scope="row">
                                <label><?php esc_html_e( 'Session duration', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <div class="vi-ui form">
                                    <div class="inline fields">
                                        <input type="number" min="0"
                                               name="<?php echo self::set_field( 'loop_session_duration' ) ?>"
                                               value="<?php echo self::get_field( 'loop_session_duration', 1 ) ?>"/>
                                        <label></label>
                                        <div>
                                            <select class="vi-ui dropdown"
                                                    name="<?php echo self::set_field( 'loop_session_duration_unit' ) ?>">
                                                <option value="h" <?php selected( self::get_field( 'loop_session_duration_unit', 'h' ), 'h' ) ?>><?php esc_html_e( 'Hour', 'woocommerce-notification' ) ?></option>
                                                <option value="m" <?php selected( self::get_field( 'loop_session_duration_unit', 'h' ), 'm' ) ?>><?php esc_html_e( 'Minute', 'woocommerce-notification' ) ?></option>
                                                <option value="s" <?php selected( self::get_field( 'loop_session_duration_unit', 'h' ), 's' ) ?>><?php esc_html_e( 'Second', 'woocommerce-notification' ) ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <p class="description"><?php esc_html_e( 'How long should a session last?', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        <tr valign="top" class="vi_hidden time_loop">
                            <th scope="row">
                                <label><?php esc_html_e( 'Next time display', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <div class="vi-ui form">
                                    <div class="inline fields">
                                        <input type="number" name="<?php echo self::set_field( 'next_time' ) ?>" min="0"
                                               value="<?php echo self::get_field( 'next_time', 60 ) ?>"/>
                                        <label><?php esc_html_e( 'seconds', 'woocommerce-notification' ) ?></label>
                                    </div>
                                </div>
                                <p class="description"><?php esc_html_e( 'Time to show next notification ', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        <tr valign="top" class="vi_hidden time_loop">
                            <th scope="row">
                                <label><?php esc_html_e( 'Notifications per page', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <input type="number" name="<?php echo self::set_field( 'notification_per_page' ) ?>"
                                       min="0"
                                       value="<?php echo self::get_field( 'notification_per_page', 30 ) ?>"/>

                                <p class="description"><?php esc_html_e( 'Number of notifications on a page.', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="<?php echo self::set_field( 'initial_delay_random' ) ?>"><?php esc_html_e( 'Initial time random', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input id="<?php echo self::set_field( 'initial_delay_random' ) ?>"
                                           type="checkbox" <?php checked( self::get_field( 'initial_delay_random' ), 1 ) ?>
                                           tabindex="0" class="vi_hidden" value="1"
                                           name="<?php echo self::set_field( 'initial_delay_random' ) ?>"/>
                                    <label></label>
                                </div>
                                <p class="description"><?php esc_html_e( 'Initial time will be random from 0 to current value.', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        <tr valign="top" class="vi_hidden initial_delay_random">
                            <th scope="row">
                                <label><?php esc_html_e( 'Minimum initial delay time', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <div class="vi-ui form">
                                    <div class="inline fields">
                                        <input type="number" name="<?php echo self::set_field( 'initial_delay_min' ) ?>"
                                               min="0"
                                               value="<?php echo self::get_field( 'initial_delay_min', 0 ) ?>"/>
                                        <label><?php esc_html_e( 'seconds', 'woocommerce-notification' ) ?></label>
                                    </div>
                                </div>
                                <p class="description"><?php esc_html_e( 'Time will be random from Initial delay time min to Initial time.', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label><?php esc_html_e( 'Initial delay', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <div class="vi-ui fluid right labeled input">
                                    <input type="number" name="<?php echo self::set_field( 'initial_delay' ) ?>" min="0"
                                           value="<?php echo self::get_field( 'initial_delay', 0 ) ?>"/>
                                    <label class="vi-ui label"><?php esc_html_e( 'second', 'woocommerce-notification' ) ?></label>
                                </div>
                                <p class="description"><?php esc_html_e( 'When your site loads, notifications will show after this amount of time', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row">
                                <label><?php esc_html_e( 'Display time', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <div class="vi-ui fluid right labeled input">
                                    <input type="number" name="<?php echo self::set_field( 'display_time' ) ?>"
                                           value="<?php echo self::get_field( 'display_time', 5 ) ?>"/>
                                    <label class="vi-ui label"><?php esc_html_e( 'second', 'woocommerce-notification' ) ?></label>
                                </div>
                                <p class="description"><?php esc_html_e( 'Time your notification display.', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Sound !-->
                <div class="vi-ui bottom attached tab segment" data-tab="sound">
                    <!-- Tab Content !-->
                    <table class="optiontable form-table">
                        <tbody>
                        <tr valign="top">
                            <th scope="row">
                                <label for="<?php echo self::set_field( 'sound_enable' ) ?>"><?php esc_html_e( 'Enable', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input id="<?php echo self::set_field( 'sound_enable' ) ?>"
                                           type="checkbox" <?php checked( self::get_field( 'sound_enable' ), 1 ) ?>
                                           tabindex="0" class="vi_hidden" value="1"
                                           name="<?php echo self::set_field( 'sound_enable' ) ?>"/>
                                    <label></label>
                                </div>
                                <p class="description"><?php printf( __( 'Modern browsers recently changed their policy to let users able to disable auto play audio so this option is not working correctly now. More details at <a href="%s" target="_blank">%s</a>', 'woocommerce-notification' ), 'https://developers.google.com/web/updates/2017/09/autoplay-policy-changes', 'https://developers.google.com/web/updates/2017/09/autoplay-policy-changes' ) ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label><?php esc_html_e( 'Sound', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
								<?php
								$sounds = self::scan_dir( VI_WNOTIFICATION_SOUNDS );
								?>
                                <select name="<?php echo self::set_field( 'sound' ) ?>" class="vi-ui fluid dropdown">
									<?php foreach ( $sounds as $sound ) { ?>
                                        <option <?php selected( self::get_field( 'sound', 'cool' ), $sound ) ?>
                                                value="<?php echo esc_attr( $sound ) ?>"><?php echo esc_html( $sound ) ?></option>
									<?php } ?>
                                </select>

                                <p class="description"><?php echo esc_html__( 'Please select sound. Notification rings when show.', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Messages !-->
                <div class="vi-ui bottom attached tab segment" data-tab="messages">
                    <!-- Tab Content !-->
                    <table class="optiontable form-table">
                        <tbody>
                        <tr valign="top">
                            <th scope="row">
                                <label><?php esc_html_e( 'Message purchased', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <table class="vi-ui message-purchased optiontable form-table">
									<?php $messages = self::get_field( 'message_purchased' );
									if ( ! $messages ) {
										$messages = array( 'Someone in {city}, {country} purchased a {product_with_link} {time_ago}' );
									} elseif ( ! is_array( $messages ) && $messages ) {
										$messages = array( $messages );
									}

									if ( count( $messages ) ) {
										foreach ( $messages as $k => $message ) {

											?>
                                            <tr>
                                                <td width="90%">

                                                    <textarea
                                                            name="<?php echo self::set_field( 'message_purchased', 1 ) ?>"><?php echo strip_tags( $message ) ?></textarea>

													<?php
													/*WPML.org*/
													if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
														$languages = $langs = icl_get_languages( 'skip_missing=N&orderby=KEY&order=DIR&link_empty_to=str' );

														if ( count( $languages ) ) {
															foreach ( $languages as $key => $language ) {
																if ( $language['active'] ) {
																	continue;
																}
																$wpml_messages = self::get_field( 'message_purchased_' . $key );
																if ( ! $wpml_messages ) {
																	$wpml_messages = array( 'Someone in {city}, {country} purchased a {product_with_link} {time_ago}' );
																} elseif ( ! is_array( $wpml_messages ) && $wpml_messages ) {
																	$wpml_messages = array( $wpml_messages );
																}
																?>
                                                                <h4><?php echo esc_html( $language['native_name'] ) ?></h4>
                                                                <textarea
                                                                        name="<?php echo self::set_field( 'message_purchased_' . $key, 1 ) ?>"><?php echo isset( $wpml_messages[ $k ] ) ? strip_tags( $wpml_messages[ $k ] ) : $message ?></textarea>
															<?php }
														}
													} /*Polylang*/
                                                    elseif ( class_exists( 'Polylang' ) ) {
														$languages = pll_languages_list();

														foreach ( $languages as $language ) {
															$default_lang = pll_default_language( 'slug' );

															if ( $language == $default_lang ) {
																continue;
															}
															$wpml_messages = self::get_field( 'message_purchased_' . $language );
															if ( ! $wpml_messages ) {
																$wpml_messages = array( 'Someone in {city}, {country} purchased a {product_with_link} {time_ago}' );
															} elseif ( ! is_array( $wpml_messages ) && $wpml_messages ) {
																$wpml_messages = array( $wpml_messages );
															}
															?>
                                                            <h4><?php echo esc_html( $language ) ?></h4>
                                                            <textarea
                                                                    name="<?php echo self::set_field( 'message_purchased_' . $language, 1 ) ?>"><?php echo isset( $wpml_messages[ $k ] ) ? strip_tags( $wpml_messages[ $k ] ) : $message ?></textarea>
															<?php
														}
													}
													?>

                                                </td>
                                                <td>
                                                    <span class="vi-ui button remove-message red"><?php esc_html_e( 'Remove', 'woocommerce-notification' ) ?></span>
                                                </td>
                                            </tr>
										<?php }
									} ?>
                                </table>
                                <p>
                                    <span class="vi-ui button add-message green"><?php esc_html_e( 'Add New', 'woocommerce-notification' ) ?></span>
                                </p>
                                <ul class="description" style="list-style: none">
                                    <li>
                                        <span>{first_name}</span>
                                        - <?php esc_html_e( 'Customer\'s first name', 'woocommerce-notification' ) ?>
                                    </li>
                                    <li>
                                        <span>{city}</span>
                                        - <?php esc_html_e( 'Customer\'s city', 'woocommerce-notification' ) ?>
                                    </li>
                                    <li>
                                        <span>{state}</span>
                                        - <?php esc_html_e( 'Customer\'s state', 'woocommerce-notification' ) ?>
                                    </li>
                                    <li>
                                        <span>{country}</span>
                                        - <?php esc_html_e( 'Customer\'s country', 'woocommerce-notification' ) ?>
                                    </li>
                                    <li>
                                        <span>{product}</span>
                                        - <?php esc_html_e( 'Product title', 'woocommerce-notification' ) ?>
                                    </li>
                                    <li>
                                        <span>{product_with_link}</span>
                                        - <?php esc_html_e( 'Product title with link', 'woocommerce-notification' ) ?>
                                    </li>
                                    <li>
                                        <span>{time_ago}</span>
                                        - <?php esc_html_e( 'Time after purchase', 'woocommerce-notification' ) ?>
                                    </li>
                                    <li>
                                        <span>{custom}</span>
                                        - <?php esc_html_e( 'Use custom shortcode', 'woocommerce-notification' ) ?>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        <!--						<tr valign="top">-->
                        <!--							<th scope="row">-->
                        <!--								<label>-->
						<?php //esc_html_e( 'Message checkout', 'woocommerce-notification' ) ?><!--</label>-->
                        <!--							</th>-->
                        <!--							<td>-->
                        <!--								<textarea name="-->
						<?php //echo self::set_field( 'message_checkout' ) ?><!--">-->
						<?php //echo self::get_field( 'message_checkout' ) ?><!--</textarea>-->
                        <!--							</td>-->
                        <!--						</tr>-->
                        <tr valign="top">
                            <th scope="row">
                                <label for="<?php echo self::set_field( 'custom_shortcode' ) ?>"><?php esc_html_e( 'Custom', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
								<?php $custom_shortcode = self::get_field( 'custom_shortcode', esc_attr( '{number} people seeing this product right now' ) ); ?>
                                <input id="<?php echo self::set_field( 'custom_shortcode' ) ?>" type="text" tabindex="0"
                                       value="<?php echo $custom_shortcode ?>"
                                       name="<?php echo self::set_field( 'custom_shortcode' ) ?>"/>

                                <p class="description"><?php esc_html_e( 'This is {custom} shortcode content.', 'woocommerce-notification' ) ?></p>
								<?php
								/*WPML.org*/
								if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
									$languages = $langs = icl_get_languages( 'skip_missing=N&orderby=KEY&order=DIR&link_empty_to=str' );

									if ( count( $languages ) ) {
										foreach ( $languages as $key => $language ) {
											if ( $language['active'] ) {
												continue;
											}
											$wpml_custom_shortcode = self::get_field( 'custom_shortcode_' . $key );
											if ( ! $wpml_custom_shortcode ) {
												$wpml_custom_shortcode = $custom_shortcode;
											}
											?>
                                            <h4><?php echo esc_html( $language['native_name'] ) ?></h4>
                                            <input id="<?php echo self::set_field( 'custom_shortcode_' . $key ) ?>"
                                                   type="text"
                                                   tabindex="0"
                                                   value="<?php echo $wpml_custom_shortcode ?>"
                                                   name="<?php echo self::set_field( 'custom_shortcode_' . $key ) ?>"/>
										<?php }
									}
								} /*Polylang*/
                                elseif ( class_exists( 'Polylang' ) ) {
									$languages = pll_languages_list();

									foreach ( $languages as $language ) {
										$default_lang = pll_default_language( 'slug' );

										if ( $language == $default_lang ) {
											continue;
										}
										$wpml_custom_shortcode = self::get_field( 'custom_shortcode_' . $language );
										if ( ! $wpml_custom_shortcode ) {
											$wpml_custom_shortcode = $custom_shortcode;
										}
										?>
                                        <h4><?php echo esc_html( $language ) ?></h4>
                                        <input id="<?php echo self::set_field( 'custom_shortcode_' . $language ) ?>"
                                               type="text"
                                               tabindex="0"
                                               value="<?php echo $wpml_custom_shortcode ?>"
                                               name="<?php echo self::set_field( 'custom_shortcode_' . $language ) ?>"/>
										<?php
									}
								}
								?>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="<?php echo self::set_field( 'min_number' ) ?>"><?php esc_html_e( 'Min Number', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <input id="<?php echo self::set_field( 'min_number' ) ?>" type="number" tabindex="0"
                                       min="0"
                                       value="<?php echo self::get_field( 'min_number', 100 ) ?>"
                                       name="<?php echo self::set_field( 'min_number' ) ?>"/>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="<?php echo self::set_field( 'max_number' ) ?>"><?php esc_html_e( 'Max number', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <input id="<?php echo self::set_field( 'max_number' ) ?>" type="number" tabindex="0"
                                       min="0"
                                       value="<?php echo self::get_field( 'max_number', 200 ) ?>"
                                       name="<?php echo self::set_field( 'max_number' ) ?>"/>

                                <p class="description"><?php esc_html_e( 'Number will random from Min number to Max number', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="<?php echo self::set_field( 'change_message_number_enable' ) ?>"><?php esc_html_e( 'Change low number', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input id="<?php echo self::set_field( 'change_message_number_enable' ) ?>"
                                           type="checkbox" <?php checked( self::get_field( 'change_message_number_enable' ), 1 ) ?>
                                           tabindex="0" class="vi_hidden" value="1"
                                           name="<?php echo self::set_field( 'change_message_number_enable' ) ?>"/>
                                    <label></label>
                                </div>

                                <p class="description"><?php esc_html_e( 'Number will change in a reasonable way', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Assign !-->
                <div class="vi-ui bottom attached tab segment" data-tab="assign">
                    <!-- Tab Content !-->
                    <table class="optiontable form-table">
                        <tbody>
                        <tr valign="top">
                            <th scope="row">
                                <label for="<?php echo self::set_field( 'is_home' ) ?>"><?php esc_html_e( 'Home page', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input id="<?php echo self::set_field( 'is_home' ) ?>"
                                           type="checkbox" <?php checked( self::get_field( 'is_home' ), 1 ) ?>
                                           tabindex="0" class="vi_hidden" value="1"
                                           name="<?php echo self::set_field( 'is_home' ) ?>"/>
                                    <label></label>
                                </div>
                                <p class="description"><?php esc_html_e( 'Turn on to hide notification on Home page', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="<?php echo self::set_field( 'is_checkout' ) ?>"><?php esc_html_e( 'Checkout page', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input id="<?php echo self::set_field( 'is_checkout' ) ?>"
                                           type="checkbox" <?php checked( self::get_field( 'is_checkout' ), 1 ) ?>
                                           tabindex="0" class="vi_hidden" value="1"
                                           name="<?php echo self::set_field( 'is_checkout' ) ?>"/>
                                    <label></label>
                                </div>
                                <p class="description"><?php esc_html_e( 'Turn on to hide notification on Checkout page', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="<?php echo self::set_field( 'is_cart' ) ?>"><?php esc_html_e( 'Cart page', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input id="<?php echo self::set_field( 'is_cart' ) ?>"
                                           type="checkbox" <?php checked( self::get_field( 'is_cart' ), 1 ) ?>
                                           tabindex="0" class="vi_hidden" value="1"
                                           name="<?php echo self::set_field( 'is_cart' ) ?>"/>
                                    <label></label>
                                </div>
                                <p class="description"><?php esc_html_e( 'Turn on to hide notification on Cart page', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
								<?php esc_html_e( 'Conditional Tags', 'woocommerce-notification' ) ?>
                            </th>
                            <td>
                                <input placeholder="<?php esc_html_e( 'eg: !is_page(array(34,98,73))', 'woocommerce-notification' ) ?>"
                                       type="text"
                                       value="<?php echo htmlentities( self::get_field( 'conditional_tags' ) ) ?>"
                                       name="<?php echo self::set_field( 'conditional_tags' ) ?>"/>

                                <p class="description"><?php esc_html_e( 'Let you adjust which pages will appear using WP\'s conditional tags.', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Logs !-->
                <div class="vi-ui bottom attached tab segment" data-tab="logs">
                    <!-- Tab Content !-->
                    <table class="optiontable form-table">
                        <tbody>
                        <tr valign="top">
                            <th scope="row">
                                <label for="<?php echo self::set_field( 'save_logs' ) ?>"><?php esc_html_e( 'Save Logs', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input id="<?php echo self::set_field( 'save_logs' ) ?>"
                                           type="checkbox" <?php checked( self::get_field( 'save_logs' ), 1 ) ?>
                                           tabindex="0" class="vi_hidden" value="1"
                                           name="<?php echo self::set_field( 'save_logs' ) ?>"/>
                                    <label></label>
                                </div>
                            </td>
                        </tr>
                        <tr valign="top" class="vi_hidden save_logs">
                            <th scope="row">
                                <label><?php esc_html_e( 'History time', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <div class="vi-ui form">
                                    <div class="inline fields">
                                        <input type="text" name="<?php echo self::set_field( 'history_time' ) ?>"
                                               value="<?php echo self::get_field( 'history_time', 30 ) ?>"/>
                                        <label><?php esc_html_e( 'days', 'woocommerce-notification' ) ?></label>
                                    </div>
                                </div>
                                <p class="description"><?php echo esc_html__( 'Logs will be saved at ', 'woocommerce-notification' ) . VI_WNOTIFICATION_CACHE . esc_html__( ' in time', 'woocommerce-notification' ) ?></p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Logs !-->
                <div class="vi-ui bottom attached tab segment" data-tab="update">
                    <!-- Tab Content !-->
                    <table class="optiontable form-table">
                        <tbody>
                        <tr valign="top">
                            <th scope="row">
                                <label><?php esc_html_e( 'Auto Update Key', 'woocommerce-notification' ) ?></label>
                            </th>
                            <td>
                                <div class="fields">
                                    <div class="ten wide field">
                                        <input class="villatheme-autoupdate-key-field" type="text"
                                               name="<?php echo self::set_field( 'key' ) ?>"
                                               value="<?php echo self::get_field( 'key' ) ?>"/>
                                    </div>
                                    <div class="six wide field">
                                        <span class="vi-ui button green villatheme-get-key-button"
                                              data-href="https://api.envato.com/authorization?response_type=code&client_id=villatheme-download-keys-6wzzaeue&redirect_uri=https://villatheme.com/update-key"
                                              data-id="16586926"><?php echo esc_html__( 'Get Key', 'woocommerce-notification' ) ?></span>
                                    </div>
                                </div>
								<?php do_action( 'woocommerce-notification_key' ) ?>
                                <p class="description"><?php echo esc_html__( 'Please fill your key what you get from ', 'woocommerce-notification' ) . '<a target="_blank" href="https://villatheme.com/my-download">https://villatheme.com/my-download</a>. ' . esc_html__( 'You can auto update WooCommerce Notification plugin. See guide ', 'woocommerce-notification' ) . '<a href="https://villatheme.com/knowledge-base/how-to-use-auto-update-feature/" target="_blank">https://villatheme.com/knowledge-base/how-to-use-auto-update-feature/</a>' ?></p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <p style="position: relative; margin-bottom: 70px; display: inline-block;">
                    <button class="vi-ui button labeled icon primary wn-submit">
                        <i class="send icon"></i> <?php esc_html_e( 'Save', 'woocommerce-notification' ) ?>
                    </button>
                    <button class="vi-ui button labeled icon wn-submit"
                            name="<?php echo self::set_field( 'check_key' ) ?>">
                        <i class="send icon"></i> <?php esc_html_e( 'Save & Check Key', 'woocommerce-notification' ) ?>
                    </button>
                </p>
            </form>
			<?php do_action( 'villatheme_support_woocommerce-notification' ) ?>
        </div>
	<?php }
} ?>