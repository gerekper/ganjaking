<?php

/*
Class Name: VI_WBOOSTSALES_Admin_Upsell
Author: Andy Ha (support@villatheme.com)
Author URI: http://villatheme.com
Copyright 2016 villatheme.com. All rights reserved.
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_WBOOSTSALES_Admin_Upsell {
	protected $settings;

	public function __construct() {
		$this->settings = new VI_WBOOSTSALES_Data();
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_filter( 'set-screen-option', array( $this, 'save_screen_options' ), 10, 3 );
		add_action( 'wp_ajax_wbs_search_product', array( $this, 'wbs_search_product' ) );
		add_action( 'wp_ajax_wbs_u_save_product', array( $this, 'wbs_u_save_product' ) );
		add_action( 'wp_ajax_wbs_u_remove_product', array( $this, 'wbs_u_remove_product' ) );
		add_action( 'wp_ajax_wbs_ba_save_product', array( $this, 'wbs_ba_save_product' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 99999 );
		add_action( 'wp_ajax_wbs_u_sync_product', array( $this, 'wbs_u_sync_product' ) );
		add_action( 'wp_ajax_wbs_u_sync_product_revert', array( $this, 'wbs_u_sync_product_revert' ) );
		add_action( 'wp_ajax_wbs_ba_save_all_product', array( $this, 'wbs_ba_save_all_product' ) );
		add_action( 'wp_ajax_wbs_ajax_enable_upsell', array( $this, 'ajax_enable_upsell' ) );
		add_action( 'set_object_terms', array( $this, 'set_object_terms' ), 10, 5 );
	}

	/**
	 * Delete upsells transient if Products in category is enabled
	 *
	 * @param $object_id
	 * @param $terms
	 * @param $tt_ids
	 * @param $taxonomy
	 * @param $append
	 */
	public function set_object_terms( $object_id, $terms, $tt_ids, $taxonomy, $append ) {
		if ( $taxonomy == 'product_cat' ) {
			delete_transient( 'vi_woocommerce_boost_sales_product_in_category_ids_' . $object_id );
		}
	}

	public function ajax_enable_upsell() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		global $wbs_settings;
		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], '_wbs_upsells_search' ) ) {
			$wbs_settings['enable']        = 1;
			$wbs_settings['enable_upsell'] = 1;
			update_option( '_woocommerce_boost_sales', $wbs_settings );
		}
		die;
	}

	/**
	 * Sync product up sells
	 */
	public function wbs_u_sync_product() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$paged = 1;
		while ( true ) {
			$args      = array(
				'post_status'    => VI_WBOOSTSALES_Data::search_product_statuses(),
				'post_type'      => 'product',
				'posts_per_page' => 50,
				'paged'          => $paged,
				'fields'         => 'ids'
			);
			$the_query = new WP_Query( $args );
			// The Loop
			if ( $the_query->have_posts() ) {
				while ( $the_query->have_posts() ) {
					$the_query->the_post();
					// Do Stuff
					$meta = get_post_meta( get_the_ID(), '_wbs_upsells', true );
					$u_id = get_post_meta( get_the_ID(), '_upsell_ids', true );
					if ( ! is_array( $meta ) ) {
						$meta = array();
					}
					if ( ! is_array( $u_id ) ) {
						$u_id = array();
					}
					$meta = array_merge( $meta, $u_id );
					$meta = array_unique( $meta );
					if ( in_array( get_the_ID(), $meta ) ) {
						$index = array_search( get_the_ID(), $meta );
						unset( $meta[ $index ] );
						$meta = array_values( $meta );
					}
					update_post_meta( get_the_ID(), '_wbs_upsells', $meta );
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

	/**
	 * Sync product up sells to WooCommerce
	 */
	public function wbs_u_sync_product_revert() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$response = array(
			'status' => 'success'
		);
		$paged    = 1;
		while ( true ) {
			$args      = array(
				'post_status'    => VI_WBOOSTSALES_Data::search_product_statuses(),
				'post_type'      => 'product',
				'posts_per_page' => 50,
				'paged'          => $paged,
				'fields'         => 'ids'
			);
			$the_query = new WP_Query( $args );
			// The Loop
			if ( $the_query->have_posts() ) {
				while ( $the_query->have_posts() ) {
					$the_query->the_post();
					$product_id = get_the_ID();
					// Do Stuff
					$meta    = get_post_meta( $product_id, '_wbs_upsells', true );
					$upsells = get_post_meta( $product_id, '_upsell_ids', true );
					if ( ! is_array( $meta ) ) {
						$meta = array();
					} else {
						$meta = array_unique( array_filter( $meta ) );
					}
					if ( ! is_array( $upsells ) ) {
						$upsells = array();
					}
					if ( count( $meta ) || count( $upsells ) ) {
						update_post_meta( $product_id, '_upsell_ids', $meta );
					}
				}
			} else {
				break;
			}

			$paged ++;
			wp_reset_postdata();
		}
		wp_send_json( $response );
	}

	/**
	 * Save bulk adds up sells
	 */
	public function wbs_ba_save_product() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		ob_start();

		$p_id = filter_input( INPUT_POST, 'p_id', FILTER_SANITIZE_STRING );
		$u_id = filter_input( INPUT_POST, 'u_id', FILTER_SANITIZE_STRING );
		$msg  = array();

		if ( empty( $p_id ) || empty( $u_id ) ) {
			die();
		}
		$u_id = array_filter( explode( ',', $u_id ) );
		$p_id = array_filter( explode( ',', $p_id ) );
		if ( count( $u_id ) && count( $p_id ) ) {
			foreach ( $p_id as $id ) {
				$meta = get_post_meta( $id, '_wbs_upsells', true );
				if ( ! is_array( $meta ) ) {
					$meta = array();
				}
				$meta = array_merge( $meta, $u_id );
				$meta = array_unique( $meta );
				if ( in_array( $id, $meta ) ) {
					$index = array_search( $id, $meta );
					unset( $meta[ $index ] );
					$meta = array_values( $meta );
				}
				update_post_meta( $id, '_wbs_upsells', $meta );
			}
			$msg['check'] = 'done';
		} else {
			$msg['check'] = 'error';
		}
		ob_clean();
		echo json_encode( $msg );
		die;
	}

	/**
	 * Save up sells with all products
	 */
	public function wbs_ba_save_all_product() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		ob_start();
		$u_id = filter_input( INPUT_POST, 'u_id', FILTER_SANITIZE_STRING );
		$msg  = array();
		if ( empty( $u_id ) ) {
			die();
		}
		$u_id = array_filter( explode( ',', $u_id ) );

		$args_all = array(
			'post_type'      => 'product',
			'post_status'    => VI_WBOOSTSALES_Data::search_product_statuses(),
			'posts_per_page' => - 1
		);
		$all_pid  = new WP_Query( $args_all );
		$p_id     = array();
		if ( ! empty( $all_pid ) ) {
			$post_a = $all_pid->posts;
			foreach ( $post_a as $pa ) {
				$p_id[] = $pa->ID;
			}
		}
		// Reset Post Data
		wp_reset_postdata();

		if ( count( $u_id ) && count( $p_id ) ) {
			foreach ( $p_id as $id ) {
				$meta = get_post_meta( $id, '_wbs_upsells', true );
				if ( ! is_array( $meta ) ) {
					$meta = array();
				}
				$meta = array_merge( $meta, $u_id );
				$meta = array_unique( $meta );
				if ( in_array( $id, $meta ) ) {
					$index = array_search( $id, $meta );
					unset( $meta[ $index ] );
					$meta = array_values( $meta );
				}
				update_post_meta( $id, '_wbs_upsells', $meta );
			}
			$msg['check'] = 'done';
		} else {
			$msg['check'] = 'error';
		}
		ob_clean();
		echo json_encode( $msg );
		die;
	}

	/**
	 * Remove all Upsell
	 */
	public function wbs_u_remove_product() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		ob_start();
		$p_id = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_STRING );
		$msg  = array();

		if ( empty( $p_id ) ) {
			die();
		}
		delete_post_meta( $p_id, '_wbs_upsells' );
		$msg['check'] = 'done';
		ob_clean();
		echo json_encode( $msg );
		die;
	}

	/**
	 * Save up sells
	 */
	public function wbs_u_save_product() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$p_id       = isset( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
		$u_id       = ! empty( $_POST['u_id'] ) ? array_map( 'sanitize_text_field', $_POST['u_id'] ) : array();
		$u_cate_ids = ! empty( $_POST['u_cate_ids'] ) ? array_map( 'sanitize_text_field', $_POST['u_cate_ids'] ) : array();
		$msg        = array(
			'check' => 'done'
		);
		if ( ! empty( $p_id ) ) {
			if ( ! empty( $u_id ) ) {
				update_post_meta( $p_id, '_wbs_upsells', $u_id );
			} else {
				delete_post_meta( $p_id, '_wbs_upsells' );
			}
			if ( ! empty( $u_cate_ids ) ) {
				update_post_meta( $p_id, '_wbs_upsells_categories', $u_cate_ids );
			} else {
				delete_post_meta( $p_id, '_wbs_upsells_categories' );
			}
		}
		wp_send_json( $msg );
	}

	/**
	 * Select 2 Search ajax
	 */
	public function wbs_search_product() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		ob_start();

		$keyword = filter_input( INPUT_GET, 'keyword', FILTER_SANITIZE_STRING );
		$p_id    = filter_input( INPUT_GET, 'p_id', FILTER_SANITIZE_STRING );

		if ( empty( $keyword ) ) {
			die();
		}
		$arg            = array(
			'post_status'    => VI_WBOOSTSALES_Data::search_product_statuses(),
			'post_type'      => 'product',
			'posts_per_page' => 50,
			's'              => $keyword,
			'post__not_in'   => array( $p_id ),
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
					'terms'    => array(
						'simple',
						'variable',
						'external',
						'subscription',
						'variable-subscription',
						'member',
						'woosb',
					),
					'operator' => 'IN'
				),
			)
		);
		$the_query      = new WP_Query( $arg );
		$found_products = array();
		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$_product = wc_get_product( get_the_ID() );
				$parent   = '';
				if ( $_product->get_type() == 'variable' && $_product->has_child() ) {
					$parent = '(#VARIABLE)';
				}
				$product          = array(
					'id'   => $_product->get_id(),
					'text' => get_the_title() . ' (#' . get_the_ID() . ') ' . $parent
				);
				$found_products[] = $product;
			}
		}
		// Reset Post Data
		wp_reset_postdata();
		wp_send_json( $found_products );
		die;
	}

	/**
	 * Init scripts
	 */
	public function enqueue_scripts() {
		$page = isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : '';
		if ( $page == 'woocommerce-boost-sales-upsell' ) {
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
			wp_enqueue_script( 'select2-v4', VI_WBOOSTSALES_JS . 'select2.js', array( 'jquery' ), '4.0.3', true );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'jquery-ui-tooltip' );
			wp_enqueue_script( 'woocommerce-boost-sales-upsell-admin', VI_WBOOSTSALES_JS . 'woocommerce-boost-sales-upsell-admin.js', array( 'jquery' ), VI_WBOOSTSALES_VERSION, true );
		}
	}

	/**
	 * Add Menu
	 */
	public function admin_menu() {
		$send_now = add_submenu_page(
			'woocommerce-boost-sales', __( 'Up-Sells', 'woocommerce-boost-sales' ), __( 'Up-Sells', 'woocommerce-boost-sales' ), 'manage_options', 'woocommerce-boost-sales-upsell', array(
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
		if ( 'wbs_per_page' == $option ) {
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
			'default' => 50,
			'option'  => 'wbs_per_page'
		);

		add_screen_option( $option, $args );
	}

	/**
	 * Menu page call back
	 */
	public function page_callback() {
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
            <h2><?php esc_html_e( 'UP-SELLS', 'woocommerce-boost-sales' ) ?></h2>
            <p class="description"><?php esc_html_e( 'Up-sells are products that you recommend instead of the currently product added to cart. They are typically products that are more profitable or better quality or more expensive', 'woocommerce-boost-sales' ) ?>
                <br>
                <a href="javascript:void(0)" id="wbs_different_up-cross-sell" title=""
                   data-wbs_up_crosssell="http://new2new.com/envato/woocommerce-boost-sales/product-upsells.gif"><?php esc_html_e( 'What is UPSELLS?', 'woocommerce-boost-sales' ); ?></a>
            </p>
			<?php
			if ( ! $this->settings->get_option( 'enable' ) || ! $this->settings->get_option( 'enable_upsell' ) ) {
				?>
                <div class="error">
                    <p><?php _e( 'Up-sells feature is currently disabled. <a class="wbs-upsells-ajax-enable button button-primary" href="javascript:void(0)">Enable now</a>', 'woocommerce-boost-sales' ) ?></p>
                </div>
				<?php
			}
			?>
            <form action="<?php echo esc_url( admin_url( 'admin.php?page=woocommerce-boost-sales-upsell' ) ) ?>"
                  method="post">
				<?php wp_nonce_field( '_wbs_upsells_search', '_wsm_nonce' ) ?>
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
							array(
								'taxonomy' => 'product_type',
								'field'    => 'slug',
								'terms'    => 'wbs_bundle',
								'operator' => 'NOT IN'
							),
						)
					);
					/*Search product*/
					$keyword = '';
					if ( isset( $_POST['wbs_us_search'] ) && isset( $_POST['_wsm_nonce'] ) ) {
						if ( wp_verify_nonce( $_POST['_wsm_nonce'], '_wbs_upsells_search' ) ) {
							$keyword   = $_POST['wbs_us_search'];
							$args['s'] = $keyword;
						}

					}
					$the_query = new WP_Query( $args );
					?>

                    <div class="alignleft actions bulkactions">
                        <span class="button action btn-bulk-adds"><?php esc_html_e( 'Bulk Adds Up-Sells', 'woocommerce-boost-sales' ) ?></span>
                    </div>
                    <div class="tablenav-pages">
                        <span class="button action btn-sync-upsell"
                              title="<?php esc_attr_e( 'Create Up-sells to use with WooCommerce Boost Sales plugin from Up-sells data in WooCommerce single product settings.', 'woocommerce-boost-sales' ) ?>"><?php esc_html_e( 'Get Product Up-Sells', 'woocommerce-boost-sales' ) ?></span>
                        <span class="button action btn-sync-upsell-revert"
                              title="<?php esc_attr_e( 'Up-sells data in WooCommerce single product settings will be OVERRIDDEN by Up-sells data managed by WooCommerce Boost Sales plugin.', 'woocommerce-boost-sales' ) ?>"><?php esc_html_e( 'Sync to WooCommerce Upsells', 'woocommerce-boost-sales' ) ?></span>
                        <input class="text short" name="wbs_us_search"
                               placeholder="<?php esc_html_e( 'Search product', 'woocommerce-boost-sales' ) ?>"
                               value="<?php echo esc_attr( $keyword ) ?>">
                    </div>
                </div>
            </form>
            <div class="bulk-adds" style="display: none;">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                    <tr>
                        <th><?php esc_attr_e( 'Products', 'woocommerce-boost-sales' ) ?></th>
                        <th><?php esc_attr_e( 'Up sells', 'woocommerce-boost-sales' ) ?></th>
                        <th><?php esc_attr_e( 'Action', 'woocommerce-boost-sales' ) ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <select multiple="multiple" name="_wbs_up_sell" class="ba-product product-search">
                            </select>
                            <label for="vi_chk_selectall">
                                <input type="checkbox" value=""
                                       id="vi_chk_selectall"/> <?php esc_attr_e( 'Select all', 'woocommerce-boost-sales' ) ?>
                            </label>
                        </td>
                        <td>
                            <select multiple="multiple" name="_wbs_up_sell" class="product-search ba-u-product">
                            </select>
                        </td>
                        <td>
                            <span class="button button-primary ba-button-save"><?php esc_attr_e( 'Add', 'woocommerce-boost-sales' ) ?></span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="list-products">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                    <tr>
                        <th scope="col" id="product-name"
                            class="manage-column column-product-name column-primary sortable desc">
                            <a href="#">
                                <span><?php esc_html_e( 'Product Name', 'woocommerce-boost-sales' ) ?></span>
                            </a>
                        </th>
                        <th scope="col" id="up-sells" class="manage-column column-up-sells sortable desc">
                            <span><?php esc_html_e( 'Up-sells products', 'woocommerce-boost-sales' ) ?></span>
                        </th>
                        <th scope="col" id="up-sells-categories" class="manage-column column-up-sells sortable desc">
                            <span><?php esc_html_e( 'Up-sells categories', 'woocommerce-boost-sales' ) ?></span>
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
							?>
                            <tr id="product-<?php echo get_the_ID() ?>">
                                <td class="product column-product has-row-actions column-primary"
                                    data-colname="product-name">
                                    <a href="<?php echo esc_url( 'post.php?action=edit&post=' . get_the_ID() ) ?>"><?php echo '[#' . get_the_ID() . '] ' . the_title( '', '', '' ) ?></a>
                                </td>
                                <td data-id="<?php echo get_the_ID() ?>" class="name column-up-sells"
                                    data-colname="<?php esc_attr_e( 'Up sells', 'woocommerce-boost-sales' ) ?>">
									<?php
									$products = get_post_meta( get_the_ID(), '_wbs_upsells', true );
									if ( ! is_array( $products ) ) {
										$products = array();
									}
									?>
                                    <select multiple="multiple" name="_wbs_up_sell"
                                            class="product-search u-product-<?php echo get_the_ID() ?>">
										<?php if ( count( $products ) ) {
											foreach ( $products as $product ) {
												$data = wc_get_product( $product );
												if ( $data ) {
													$parent = $out_stock = '';
													if ( ! $data->is_type( 'wbs_bundle' ) ) {
														if ( $data->is_type( 'variable' ) && $data->has_child() ) {
															$parent = '(#VARIABLE)';
														}
														if ( ! $data->is_in_stock() ) {
															$out_stock = '(' . esc_html__( 'Out of stock', 'woocommerce-boost-sales' ) . ')';
														}
														?>
                                                        <option selected="selected"
                                                                value="<?php echo esc_attr( $data->get_id() ) ?>"><?php echo esc_html( $data->get_title() . ' (#' . $data->get_id() ) . ') ' . $parent . $out_stock ?></option>
														<?php
													}
												}

											}

										}
										?>
                                    </select>
                                <td data-id="<?php echo get_the_ID() ?>">
									<?php
									$categories = get_post_meta( get_the_ID(), '_wbs_upsells_categories', true );
									if ( ! is_array( $categories ) ) {
										$categories = array();
									}
									?>
                                    <select multiple="multiple" name="_wbs_up_sell_categories"
                                            class="wbs-category-search u-categories-<?php echo get_the_ID() ?>">
										<?php if ( count( $categories ) ) {
											foreach ( $categories as $category_id ) {
												$category = get_term( $category_id );
												if ( $category ) {
													?>
                                                    <option value="<?php echo $category_id ?>"
                                                            selected><?php echo $category->name; ?></option>
													<?php
												}
											}
										}
										?>
                                    </select>
                                </td>
                                </td>
                                <td class="email column-action product-action-<?php echo get_the_ID() ?>"
                                    data-colname="<?php esc_attr_e( 'Actions', 'woocommerce-boost-sales' ) ?>"
                                    data-id="<?php echo get_the_ID() ?>">
                                    <a target="_blank" href="<?php the_permalink( get_the_ID() ) ?>"
                                       class="button"><?php esc_attr_e( 'View', 'woocommerce-boost-sales' ) ?></a>
                                    <span
                                            class="button button-save"><?php esc_attr_e( 'Save', 'woocommerce-boost-sales' ) ?></span>
                                    <span
                                            class="button button-remove"><?php esc_attr_e( 'Remove all', 'woocommerce-boost-sales' ) ?></span>

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
									'page'  => 'woocommerce-boost-sales-upsell',
									'paged' => $p_paged
								), admin_url( 'admin.php' )
							); ?>
                            <a class="prev-page" href="<?php echo esc_url( $p_url ) ?>"><span
                                        class="screen-reader-text"><?php esc_html_e( 'Previous Page', 'woocommerce-boost-sales' ) ?></span><span
                                        aria-hidden="true">‹</span></a>
						<?php } else { ?>
                            <span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
						<?php } ?>
                        <span
                                class="screen-reader-text"><?php esc_html_e( 'Current Page', 'woocommerce-boost-sales' ) ?></span>
                        <span id="table-paging" class="paging-input">
							<span class="tablenav-paging-text"><?php echo esc_html( $paged ) ?> of <span
                                        class="total-pages"><?php echo esc_html( $total_page ) ?></span>
							</span>
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
									'page'  => 'woocommerce-boost-sales-upsell',
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