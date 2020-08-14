<?php

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_WooCommerce_Product_Shipping_Admin' ) ) {

	/**
	 * YITH WooCommerce Product Shipping Admin
	 *
	 * @since 1.0.0
	 */
	class YITH_WooCommerce_Product_Shipping_Admin {

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			/**
			 * Scripts
			 */
			if ( yith_wcps_is_wcfm() ) {
				add_action( 'wp_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
			} else {
				add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
			}

			/**
			 * Product Data Panel
			 */
			if ( apply_filters( 'yith_wcps_product_data_tab', true ) ) {
				add_filter( 'woocommerce_product_data_tabs', array( $this, 'product_shipping_tab' ) );
				add_action( 'woocommerce_product_data_panels', array( $this, 'product_shipping_panel' ) );
			}

			/**
			 * Save Shippings
			 */
			add_action( 'woocommerce_process_product_meta', array( $this, 'update_shippings' ) );
			add_action( 'woocommerce_save_product_variation', array( $this, 'update_shippings' ), 10, 2 );

			/**
			 * YITH Country States Callback
			 */
			add_action( 'wp_ajax_yith_wc_country_states', array( $this, 'yith_wc_country_states_callback' ) );

			/**
			 * YITH Plugins
			 */
			add_action( 'admin_menu', array( $this, 'add_yit_plugin_menu_item' ) );

			if ( yith_wcps_is_wcfm() && isset( $_REQUEST['act'] ) && $_REQUEST['act'] == 'save' ) {
				$this->update_shippings( $_REQUEST['product_id'] );
			}

		}

		/**
		 * YITH Plugins
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function add_yit_plugin_menu_item(){
			global $submenu;
			if ( empty( $GLOBALS['admin_page_hooks']['yith_plugin_panel'] ) ) {
				$position = apply_filters( 'yit_plugins_menu_item_position', '62.32' );
				add_menu_page( 'yith_plugin_panel', 'YITH', 'edit_products', 'yith_plugin_panel', NULL, YIT_CORE_PLUGIN_URL . '/assets/images/yith-icon.svg', $position );
			}

			$user_info = get_userdata( get_current_user_id() );
			$user_role = isset( $user_info->roles[0] ) ? $user_info->roles[0] : '';
			if ( $user_role == 'vendor' ) {

				add_submenu_page(
	                null,
	                __( 'Vendor Shippings Table', 'yith-product-shipping-for-woocommerce' ),
	                __( 'Vendor Shippings Table', 'yith-product-shipping-for-woocommerce' ),
	                'edit_products',
	                'shipping-vendor',
	                array( $this, 'shipping_vendor' )
	            );

				$submenu['yith_plugin_panel'][] = array(
					__( 'Vendor Shipping', 'yith-product-shipping-for-woocommerce' ),
					'edit_products',
					'admin.php?page=shipping-vendor',
					__( 'Product Shipping', 'yith-product-shipping-for-woocommerce' ),
				);

			} else {

				$submenu['yith_plugin_panel'][] = array(
					__( 'Product Shipping', 'yith-product-shipping-for-woocommerce' ),
					'edit_products',
					'admin.php?page=wc-settings&tab=shipping&section=yith_wc_product_shipping_method',
					__( 'Product Shipping', 'yith-product-shipping-for-woocommerce' ),
				);

			}

		}

		function shipping_vendor() { ?>

			<div class="wrap woocommerce">

				<form id="mainform">
					<h2><?php echo __( 'Shipping Vendor Table', 'yith-product-shipping-for-woocommerce' ); ?></h2>
					<?php $this->shipping_table(); ?>
				</form>

			</div>

			<?php
		}

		/**
		 * YITH Country States Callback
		 *
		 * @return void
		 * @since 1.0.0
		 */
		function yith_wc_country_states_callback() {
			$country = $_POST['country'];
			if ( $country != '' ) {
				global $woocommerce;
				$countries_obj	= new WC_Countries();
				$all_states		= array( '' => __( 'All states', 'yith-product-shipping-for-woocommerce' ) );
				$country_states	= $countries_obj->get_states( $country );
				$country_states	= is_array( $country_states ) ? array_merge( $all_states, $country_states ) : $all_states;

				foreach ( $country_states as $key => $value) {
					echo '<option value="' . $key . '">' . $value . '</option>';
				}
			} else { echo '<option>' . __( 'All states', 'yith-product-shipping-for-woocommerce' ) . '</option>'; }
			die();
		}

		/**
		 * Enqueue Scripts & Style
		 *
		 * @since 1.0.0
		 * @return void
		 */
        public function admin_enqueue_scripts() {

            global $post;

            if ( yith_wcps_is_wcfm() ) {
            	$wcfm_product_section = function_exists('YITH_Frontend_Manager') ? YITH_Frontend_Manager()->gui->get_section('products') : null;
        	}

            if ( isset( $_GET['section'] ) && $_GET['section'] == 'yith_wc_product_shipping_method' ||
                isset( $_GET['page'] ) && $_GET['page'] == 'shipping-vendor' ||
                isset( $post ) && get_post_type( $post->ID ) == 'product' ||
                isset($wcfm_product_section) && $wcfm_product_section->is_current() ) {

                wp_enqueue_style( 'yith-product-shipping-admin-style', plugins_url( 'assets/css/yith-wcps-admin.css', YITH_WCPS_FILE ) );
                wp_register_script( 'yith-product-shipping-admin-script', plugins_url( 'assets/js/yith-wcps-admin.js', YITH_WCPS_FILE ), array( 'jquery' ), YITH_WCPS_VERSION, true );
                wp_enqueue_script( 'yith-product-shipping-admin-script' );

                wp_enqueue_style( 'select2', WC()->plugin_url() . '/assets/css/select2.css' );

                wp_register_script( 'wc-enhanced-select', WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select.min.js', array( 'jquery', 'select2', 'selectWoo' ) );
                wp_enqueue_script( 'wc-enhanced-select' );

                wp_register_script( 'wc-tooltip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.min.js', array( 'jquery', 'select2', 'selectWoo' ) );
                wp_enqueue_script( 'wc-tooltip' );

            }

        }

		/**
		 * Product Shipping Panel Tab
		 *
		 * @since 1.0.0
		 * @param array $product_data_tabs
		 * @return array
		 */
		function product_shipping_tab( $product_data_tabs ) {
			$product_data_tabs['yith-woocommerce-product-shipping'] = array(
				'label'		=> __( 'Shipping Costs', 'yith-product-shipping-for-woocommerce' ),
				'target'	=> 'yith_woocommerce_product_shipping',
				'class'		=>  array( 'yith_wcps_tab_class', 'hide_if_virtual' ),
			);
			return $product_data_tabs;
		}

		/**
		 * Product Shipping Panel
		 *
		 * @since 1.0.0
		 * @return void
		 */
		function product_shipping_panel() {
			global $woocommerce, $post; ?>
			<div id="yith_woocommerce_product_shipping" class="panel woocommerce_options_panel">
				<?php

					if ( yith_wcps_is_wcfm() && isset($_GET['product_id'] )) {
						$post_id = $_GET['product_id'];
						$post = get_post( $post_id );
					}

					$_yith_product_shipping = empty( get_post_meta( $post->ID, '_yith_product_shipping', true ) ) ? 'no' : get_post_meta( $post->ID, '_yith_product_shipping', true );

					woocommerce_wp_checkbox(
						array(
							'id'            => '_yith_product_shipping',
							'label'         => __( 'Enable Shipping Costs', 'yith-product-shipping-for-woocommerce' ),
							'description'   => '',
							'value'			=> apply_filters( 'yith_wcps_product_panel_enable_shipping_costs', $_yith_product_shipping ),
						)
					);
					woocommerce_wp_text_input(
						array(
							'id'            => '_yith_product_shipping_message',
							'label'         => __( 'Product Message', 'yith-product-shipping-for-woocommerce' ),
							'description'   => 'Inform customers about shipping costs.',
						)
					);
					woocommerce_wp_select(
						array(
							'id'            => '_yith_product_shipping_message_position',
							'label'         => __( 'Message Position', 'yith-product-shipping-for-woocommerce' ),
							'description'   => '',
							'options'       => array(
								'before'   	=> __( 'Before "Add to cart" button', 'yith-product-shipping-for-woocommerce' ),
								'after'		=> __( 'After "Add to cart" button', 'yith-product-shipping-for-woocommerce' ),
							),
						)
					);
					$this->shipping_table();
				?>
			</div>
			<?php
		}

		/**
		 * Shipping Table
		 *
		 * @since 1.0.0
		 */
		public function shipping_table( $post_id = 0 ) {

			global $woocommerce, $post, $wpdb;

			$not_editable = false;

			if ( yith_wcps_is_wcfm() && isset( $_GET['product_id'] ) ) {
				$post_id = $_GET['product_id'];
				$post = get_post( $post_id );
			}
			
			if ( empty( $post_id ) && ! empty( $post->ID ) ) { $post_id = $post->ID; }
			elseif ( $post_id === 'not-editable' ) { $post_id = 0; $not_editable = true; }

			$is_edit_product = ( get_post_type( $post_id ) == 'product' || get_post_type( $post_id ) == 'product_variation' ) ? true : false;

			$is_vendor = isset( $_GET['page'] ) && $_GET['page'] == 'shipping-vendor';

			$table_rows = apply_filters( 'yith_wcps_admin_table_rows', '10' );
			$table_cols = apply_filters( 'yith_wcps_admin_table_cols', array( 'price', 'qty', 'taxy' ) );

			$show_table_role	= is_array( $table_cols ) && in_array( 'role', $table_cols ) && ! $is_edit_product && ! $not_editable;
			$show_table_price	= is_array( $table_cols ) && in_array( 'price', $table_cols );
			$show_table_qty		= is_array( $table_cols ) && in_array( 'qty', $table_cols );
			$show_table_weight	= is_array( $table_cols ) && in_array( 'weight', $table_cols );
			$show_table_taxy	= is_array( $table_cols ) && in_array( 'taxy', $table_cols ) && ! $is_edit_product && ! $not_editable;
			$show_table_geo		= is_array( $table_cols ) && in_array( 'geo', $table_cols );
			$show_table_zones	= is_array( $table_cols ) && in_array( 'zones', $table_cols );

			$hide_table_role	= $is_vendor || $show_table_role ? '' : ' style="display:none;"';
			$hide_table_price	= $is_vendor || $show_table_price ? '' : ' style="display:none;"';
			$hide_table_qty		= $is_vendor || $show_table_qty ? '' : ' style="display:none;"';
			$hide_table_weight	= $is_vendor || $show_table_weight ? '' : ' style="display:none;"';
			$hide_table_taxy	= $is_vendor || $show_table_taxy ? '' : ' style="display:none;"';
			$hide_table_geo		= $is_vendor || $show_table_geo ? '' : ' style="display:none;"';

			$hide_table_zones	= $is_vendor || apply_filters( 'yith_wcps_show_shipping_zones', false ) ? '' : ' style="display:none;"';

			// Pagination
			$pagination_page	= isset( $_REQUEST['p'] ) && $_REQUEST['p'] > 0 ? $_REQUEST['p'] : 1;
			$pagination_offset	= 0;
			$pagination_perpage	= $table_rows;
			if ( $pagination_page > 1 ) {
				$pagination_offset = ( $pagination_page - 1 ) * $pagination_perpage;
			}

			?>
			<div class="yith_product_shipping_costs_table">

				<div class="rows yith_product_shipping_rows <?php echo ! $not_editable ? 'sortable' : ''; ?>">

					<table class="widefat">
						<thead>
							<tr class="first row">

								<?php if ( $not_editable ) : ?>
									<th class="edit">&nbsp;</th>
								<?php else : ?>
									<th class="sort">&nbsp;</th>
								<?php endif; ?>
								<?php if ( $not_editable ) : ?>
									<th class="product_id"><small><?php echo __( 'Product ID', 'yith-product-shipping-for-woocommerce' ); ?></small></th>
								<?php endif; ?>

								<!-- ROLE -->
								<th class="role" <?php echo $hide_table_role; ?>><small><?php // echo __( 'Role', 'yith-product-shipping-for-woocommerce' ); ?></small></th>

								<!-- CART TOTAL PRICE -->
								<th class="cart_total" colspan="2"<?php echo $hide_table_price; ?>><small><?php echo __( 'Cart Price', 'yith-product-shipping-for-woocommerce' ); ?></small></th>

								<!-- QUANTITY -->
								<th class="cart_qty" colspan="2"<?php echo $hide_table_qty; ?>><small><?php echo __( 'Cart Quantity', 'yith-product-shipping-for-woocommerce' ); ?></small></th>
								<th class="quantity" colspan="2"<?php echo $hide_table_qty; ?>><small><?php echo __( 'Product Quantity', 'yith-product-shipping-for-woocommerce' ); ?></small></th>
								
								<!-- WEIGHT -->
								<th class="weight" colspan="2"<?php echo $hide_table_weight; ?>><small><?php echo __( 'Product Weight', 'yith-product-shipping-for-woocommerce' ); ?></small></th>
								<th class="weight" colspan="2"<?php echo $hide_table_weight; ?>><small><?php echo __( 'Cart Weight', 'yith-product-shipping-for-woocommerce' ); ?></small></th>

								<!-- TAXONOMIES -->
								<th class="taxonomies" colspan="2"<?php echo $hide_table_taxy; ?>><small><?php echo __( 'Taxonomies', 'yith-product-shipping-for-woocommerce' ); ?></small></th>

								<!-- GEOLOCATION -->
								<th class="geolocation" colspan="4"<?php echo $hide_table_geo; ?>><small><?php echo __( 'Geolocation', 'yith-product-shipping-for-woocommerce' ); ?></small></th>

								<!-- SHIPPING ZONES -->
								<th class="zones"<?php echo $hide_table_zones; ?>><small><?php echo __( 'Shipping Zones', 'yith-product-shipping-for-woocommerce' ); ?></small></th>

								<!-- COSTS -->
								<th class="costs" colspan="3"><small><?php echo __( 'Shipping Cost', 'yith-product-shipping-for-woocommerce' ); ?> (<?php echo get_woocommerce_currency_symbol(); ?>)</small></th>

							</tr>

							<tr class="second row">

								<?php if ( $not_editable ) : ?>
									<th class="edit">&nbsp;</th>
								<?php else : ?>
									<th class="sort">&nbsp;</th>
								<?php endif; ?>
								<?php if ( $not_editable ) : ?>
									<th class="product_id">&nbsp;</th>
								<?php endif; ?>

								<!-- ROLE -->
								<th class="role"<?php echo $hide_table_role; ?>><small><?php echo __( 'Role', 'yith-product-shipping-for-woocommerce' ); ?></small></th>

								<!-- CART TOTAL PRICE -->
								<th class="cart_total"<?php echo $hide_table_price; ?>><small><?php echo __( 'Min', 'yith-product-shipping-for-woocommerce' ); ?> (<?php echo get_woocommerce_currency_symbol(); ?>)</small></th>
								<th class="cart_total"<?php echo $hide_table_price; ?>><small><?php echo __( 'Max', 'yith-product-shipping-for-woocommerce' ); ?> (<?php echo get_woocommerce_currency_symbol(); ?>)</small></th>

								<!-- QUANTITY -->
								<th<?php echo $hide_table_qty; ?>><small><?php echo __( 'Min', 'yith-product-shipping-for-woocommerce' ); ?></small></th>
								<th<?php echo $hide_table_qty; ?>><small><?php echo __( 'Max', 'yith-product-shipping-for-woocommerce' ); ?></small></th>
								<th<?php echo $hide_table_qty; ?>><small><?php echo __( 'Min', 'yith-product-shipping-for-woocommerce' ); ?></small></th>
								<th<?php echo $hide_table_qty; ?>><small><?php echo __( 'Max', 'yith-product-shipping-for-woocommerce' ); ?></small></th>

								<!-- WEIGHT -->
								<th<?php echo $hide_table_weight; ?>><small><?php echo __( 'Min', 'yith-product-shipping-for-woocommerce' ); ?></small></th>
								<th<?php echo $hide_table_weight; ?>><small><?php echo __( 'Max', 'yith-product-shipping-for-woocommerce' ); ?></small></th>
								<th<?php echo $hide_table_weight; ?>><small><?php echo __( 'Min', 'yith-product-shipping-for-woocommerce' ); ?></small></th>
								<th<?php echo $hide_table_weight; ?>><small><?php echo __( 'Max', 'yith-product-shipping-for-woocommerce' ); ?></small></th>

								<!-- TAXONOMIES -->
								<th<?php echo $hide_table_taxy; ?>><small><?php echo __( 'Categories', 'yith-product-shipping-for-woocommerce' ); ?></small></th>
								<th<?php echo $hide_table_taxy; ?>><small><?php echo __( 'Tags', 'yith-product-shipping-for-woocommerce' ); ?></small></th>

								<!-- GEOLOCATION -->
								<th<?php echo $hide_table_geo; ?>><small><?php echo __( 'Action', 'yith-product-shipping-for-woocommerce' ); ?></small></th>
								<th<?php echo $hide_table_geo; ?>><small><?php echo __( 'Country', 'yith-product-shipping-for-woocommerce' ); ?></small></th>
								<th<?php echo $hide_table_geo; ?>><small><?php echo __( 'State', 'yith-product-shipping-for-woocommerce' ); ?></small></th>
								<th<?php echo $hide_table_geo; ?>><small><?php echo __( 'Postal Code', 'yith-product-shipping-for-woocommerce' ); ?></small></th>

								<!-- SHIPPING ZONES -->
								<th<?php echo $hide_table_zones; ?>><small><?php echo __( 'Zone', 'yith-product-shipping-for-woocommerce' ); ?></small></th>

								<!-- COSTS -->
								<th class="shipping_cost"><small><?php echo __( 'Per product', 'yith-product-shipping-for-woocommerce' ); ?></small></th>
								<th class="product_cost"><small><?php echo __( 'Per quantity', 'yith-product-shipping-for-woocommerce' ); ?></small></th>
								<th class="unique_cost"><small><?php echo __( 'Per order', 'yith-product-shipping-for-woocommerce' ); ?></small></th>

							</tr>
						</thead>
						<tbody>
							<?php
								if ( $post_id > 0 && get_post_type( $post ) == 'product' ) {
									$query = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}yith_wcps_shippings WHERE product_id = %d ORDER BY ord", $post_id );
									$query_total = "SELECT COUNT(*) FROM {$wpdb->prefix}yith_wcps_shippings WHERE product_id = $post_id";
								} elseif ( $post_id == 0 && $not_editable ) {
									$query = "SELECT * FROM {$wpdb->prefix}yith_wcps_shippings WHERE product_id > 0 ORDER BY product_id, ord";
								} elseif ( $is_vendor ) {
									$query = "SELECT * FROM {$wpdb->prefix}yith_wcps_shippings WHERE product_id = 0 AND vendor_id > 0 ORDER BY ord LIMIT $pagination_offset,$pagination_perpage";
									$query_total = "SELECT COUNT(*) FROM {$wpdb->prefix}yith_wcps_shippings WHERE product_id = 0 AND vendor_id > 0";
								} else {
									$query = "SELECT * FROM {$wpdb->prefix}yith_wcps_shippings WHERE product_id = 0 ORDER BY ord LIMIT $pagination_offset,$pagination_perpage";
									$query_total = "SELECT COUNT(*) FROM {$wpdb->prefix}yith_wcps_shippings WHERE product_id = 0";
								}

								$total_rows = empty( $query_total ) ? 0 : $wpdb->get_row( $query_total, ARRAY_A )['COUNT(*)'];
								$results = $wpdb->get_results( $query, ARRAY_A );

								echo '<input type="hidden" name="pagination_offset" value="' . $pagination_offset . '"';

								if ( empty( $results ) ) {
									$results[0] = array(
										'id'				=> '',
										'product_id'		=> '',
										'role'				=> '',
										'min_cart_qty'		=> '',
										'max_cart_qty'		=> '',
										'min_quantity'		=> '',
										'max_quantity'		=> '',
										'min_weight'		=> '',
										'max_weight'		=> '',
										'min_cart_weight'	=> '',
										'max_cart_weight'	=> '',
										'min_cart_total'	=> '',
										'max_cart_total'	=> '',
										'geo_exclude'		=> '',
										'country_code'		=> '',
										'state_code'		=> '',
										'postal_code'		=> '',
										'shipping_cost'		=> '',
										'product_cost'		=> '',
										'unique_cost'		=> '',
									);
								}
								foreach ( $results as $result ) : ?>
									<tr>

										<?php if ( $not_editable ) : ?>
											<td class="edit"><a href="post.php?post=<?php echo $result['product_id']; ?>&action=edit#woocommerce-product-data"><span class="dashicons dashicons-edit"></span></a></td>
										<?php else : ?>
											<td class="sort"><span class="dashicons dashicons-move"></span></td>
										<?php endif; ?>
										<?php if ( $not_editable ) : ?>
											<td class="product_id"><input type="text" value="<?php echo esc_attr( $result['product_id'] > 0 ? 'ID: ' . $result['product_id'] : '' ); ?>" placeholder="*" <?php disabled( true ); ?> /></td>
										<?php endif; ?>

										<!-- ROLE -->
										<td class="role"<?php echo $hide_table_role; ?>>
											<select name="yith_product_role[<?php echo $result['id'] > 0 ? $result['id'] : 'new]['; ?>]" <?php disabled( $not_editable ); ?>>
												<option value="0"><?php echo __( 'All roles', 'yith-product-shipping-for-woocommerce' ); ?></option>
												<?php foreach ( get_editable_roles() as $role_name => $role_info ): ?>
													<option value="<?php echo $role_name; ?>" <?php selected( $role_name, $result['role'] ); ?>><?php echo $role_info['name']; ?></option>
												<?php endforeach; ?>
											</select>
										</td>

										<!-- CART TOTAL PRICE -->
										<td class="cart_total min"<?php echo $hide_table_price; ?>><input type="text" value="<?php echo $result['min_cart_total']; ?>" placeholder="0.00" name="yith_product_min_cart_total[<?php echo $result['id'] > 0 ? $result['id'] : 'new]['; ?>]" <?php disabled( $not_editable ); ?> /></td>
										<td class="cart_total max"<?php echo $hide_table_price; ?>><input type="text" value="<?php echo $result['max_cart_total']; ?>" placeholder="&infin;" name="yith_product_max_cart_total[<?php echo $result['id'] > 0 ? $result['id'] : 'new]['; ?>]" <?php disabled( $not_editable ); ?> /></td>

										<!-- QUANTITY -->
										<td class="cart_qty min"<?php echo $hide_table_qty; ?>><input type="text" value="<?php echo esc_attr( ! $result['min_cart_qty'] > 0 ? 1 : $result['min_cart_qty'] ); ?>" placeholder="" name="yith_product_min_cart_qty[<?php echo $result['id'] > 0 ? $result['id'] : 'new]['; ?>]" <?php disabled( $not_editable ); ?> /></td>
										<td class="cart_qty max"<?php echo $hide_table_qty; ?>><input type="text" value="<?php echo esc_attr( ! $result['max_cart_qty'] > 0 ? '' : $result['max_cart_qty'] ); ?>" placeholder="&infin;" name="yith_product_max_cart_qty[<?php echo $result['id'] > 0 ? $result['id'] : 'new]['; ?>]" <?php disabled( $not_editable ); ?> /></td>
										<td class="quantity min"<?php echo $hide_table_qty; ?>><input type="text" value="<?php echo esc_attr( ! $result['min_quantity'] > 0 ? 1 : $result['min_quantity'] ); ?>" placeholder="" name="yith_product_min_quantity[<?php echo $result['id'] > 0 ? $result['id'] : 'new]['; ?>]" <?php disabled( $not_editable ); ?> /></td>
										<td class="quantity max"<?php echo $hide_table_qty; ?>><input type="text" value="<?php echo esc_attr( ! $result['max_quantity'] > 0 ? '' : $result['max_quantity'] ); ?>" placeholder="&infin;" name="yith_product_max_quantity[<?php echo $result['id'] > 0 ? $result['id'] : 'new]['; ?>]" <?php disabled( $not_editable ); ?> /></td>

										<!-- WEIGHT -->
										<td class="weight min"<?php echo $hide_table_weight; ?>><input type="text" value="<?php echo esc_attr( ! $result['min_weight'] > 0 ? 0 : $result['min_weight'] ); ?>" placeholder="" name="yith_product_min_weight[<?php echo $result['id'] > 0 ? $result['id'] : 'new]['; ?>]" <?php disabled( $not_editable ); ?> /></td>
										<td class="weight max"<?php echo $hide_table_weight; ?>><input type="text" value="<?php echo esc_attr( ! $result['max_weight'] > 0 ? '' : $result['max_weight'] ); ?>" placeholder="&infin;" name="yith_product_max_weight[<?php echo $result['id'] > 0 ? $result['id'] : 'new]['; ?>]" <?php disabled( $not_editable ); ?> /></td>
										<td class="cart_weight min"<?php echo $hide_table_weight; ?>><input type="text" value="<?php echo esc_attr( ! $result['min_cart_weight'] > 0 ? 0 : $result['min_cart_weight'] ); ?>" placeholder="" name="yith_product_min_cart_weight[<?php echo $result['id'] > 0 ? $result['id'] : 'new]['; ?>]" <?php disabled( $not_editable ); ?> /></td>
										<td class="cart_weight max"<?php echo $hide_table_weight; ?>><input type="text" value="<?php echo esc_attr( ! $result['max_cart_weight'] > 0 ? '' : $result['max_cart_weight'] ); ?>" placeholder="&infin;" name="yith_product_max_cart_weight[<?php echo $result['id'] > 0 ? $result['id'] : 'new]['; ?>]" <?php disabled( $not_editable ); ?> /></td>
										
										<!-- TAXONOMIES -->
										<td class="taxonomy"<?php echo $hide_table_taxy; ?>>
											<select multiple="multiple" name="yith_product_categories[<?php echo $result['id'] > 0 ? $result['id'] : 'new]['; ?>][]" data-placeholder="*" class="chosen_select" <?php disabled( $not_editable ); ?>>
												<?php
													$categories_array = isset( $result['categories'] ) ? explode( ',', str_replace( 'ID:', '', $result['categories'] ) ) : array();
													$this->echo_product_taxonomies_childs_of( 0, 0, 'product_cat', $categories_array );
												?>
											</select>
										</td>
										<td class="taxonomy"<?php echo $hide_table_taxy; ?>>
											<select multiple="multiple" name="yith_product_tags[<?php echo $result['id'] > 0 ? $result['id'] : 'new]['; ?>][]" data-placeholder="*" class="chosen_select" <?php disabled( $not_editable ); ?>>
												<?php
													$categories_array = isset( $result['tags'] ) ? explode( ',', str_replace( 'ID:', '', $result['tags'] ) ) : array();
													$this->echo_product_taxonomies_childs_of( 0, 0, 'product_tag', $categories_array );
												?>
											</select>
										</td>

										<!-- GEOLOCATION -->
										<td class="geo_exclude geo"<?php echo $hide_table_geo; ?>>
											<select name="yith_product_geo_exclude[<?php echo $result['id'] > 0 ? $result['id'] : 'new]['; ?>][]" <?php disabled( $not_editable ); ?>>
												<option value="0"><?php echo __( 'Include', 'yith-product-shipping-for-woocommerce' ); ?></option>
												<option value="1"<?php echo $result['geo_exclude'] ? ' selected="selected"' : ''; ?>><?php echo __( 'Exclude', 'yith-product-shipping-for-woocommerce' ); ?></option>
											</select>
										</td>
										<td class="country_code geo"<?php echo $hide_table_geo; ?>>
											<?php
											$countries_obj	= new WC_Countries();
											$all_countries	= array( '' => __( 'All countries', 'yith-product-shipping-for-woocommerce' ) );
											$countries		= $countries_obj->__get('countries');
											$countries		= is_array( $countries ) ? array_merge( $all_countries, $countries ) : $all_countries;
											$select_name	= 'yith_product_country_code[' . ( $result['id'] > 0 ? $result['id'] : 'new][' ) . ']';

											if ( $not_editable || apply_filters( 'yith_wcps_allow_multi_country_codes', false ) ) : ?>
												<input type="text" value="<?php echo esc_attr( $result['country_code'] ); ?>" placeholder="*" name="yith_product_country_code[<?php echo $result['id'] > 0 ? $result['id'] : 'new]['; ?>]" <?php disabled( $not_editable ); ?> />
											<?php else : ?>
												<?php woocommerce_form_field( $select_name, array( 'type' => 'select', 'class' => array( 'countries' ), 'options' => $countries, ), esc_attr( $result['country_code'] ) ); ?>
											<?php endif; ?>
										</td>
										<td class="state_code geo"<?php echo $hide_table_geo; ?>>
											<?php
											// $default_country		= $countries_obj->get_base_country();
											$all_states		= array( '' => __( 'All states', 'yith-product-shipping-for-woocommerce' ) );
											$country_states	= $countries_obj->get_states( $result['country_code'] );
											$country_states	= is_array( $country_states ) ? array_merge( $all_states, $country_states ) : $all_states;
											$select_name	= 'yith_product_state_code[' . ( $result['id'] > 0 ? $result['id'] : 'new][' ) . ']';

											if ( $not_editable || apply_filters( 'yith_wcps_allow_multi_state_codes', false ) ) : ?>
												<input type="text" value="<?php echo esc_attr( $result['state_code'] ); ?>" placeholder="*" name="yith_product_state_code[<?php echo $result['id'] > 0 ? $result['id'] : 'new]['; ?>]" <?php disabled( $not_editable ); ?> />
											<?php else : ?>
												<?php woocommerce_form_field( $select_name, array( 'type' => 'select', 'options' => $country_states ), esc_attr( $result['state_code'] ) ); ?>
											<?php endif; ?>
										</td>
										<td class="postal_code geo"<?php echo $hide_table_geo; ?>><input type="text" value="<?php echo esc_attr( $result['postal_code'] ); ?>" placeholder="*" name="yith_product_postal_code[<?php echo $result['id'] > 0 ? $result['id'] : 'new]['; ?>]" <?php disabled( $not_editable ); ?> /></td>

										<!-- SHIPPING ZONES -->
										<td class="zones"<?php echo $hide_table_zones; ?>>
											<select name="yith_product_zone[<?php echo $result['id'] > 0 ? $result['id'] : 'new]['; ?>][]" <?php disabled( $not_editable ); ?>>
												<option value="0"><?php echo __( 'All Shipping Zones', 'yith-product-shipping-for-woocommerce' ) ?></option>
												<?php
													$delivery_zones = WC_Shipping_Zones::get_zones();
													foreach ( (array) $delivery_zones as $key => $the_zone ) {
														echo '<option value="' . $the_zone['id'] . '"' . ( $result['zone'] == $the_zone['id'] ? ' selected="selected"' : '' ) . '>' . $the_zone['zone_name'] . '</option>';
													}
												?>
											</select>
										</td>

										<!-- COSTS -->
										<td class="shipping_cost"><input type="text" value="<?php echo $result['shipping_cost']; ?>" placeholder="0.00" name="yith_product_shipping_cost[<?php echo $result['id'] > 0 ? $result['id'] : 'new]['; ?>]" <?php disabled( $not_editable ); ?> /></td>
										<td class="product_cost"><input type="text" value="<?php echo $result['product_cost']; ?>" placeholder="0.00" name="yith_product_product_cost[<?php echo $result['id'] > 0 ? $result['id'] : 'new]['; ?>]" <?php disabled( $not_editable ); ?> /></td>
										<td class="unique_cost"><input type="text" value="<?php echo $result['unique_cost']; ?>" placeholder="0.00" name="yith_product_unique_cost[<?php echo $result['id'] > 0 ? $result['id'] : 'new]['; ?>]" <?php disabled( $not_editable ); ?> /></td>
									</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					<div class="pagination">
						<?php
							$total_pages = $pagination_perpage > 0 ? ceil( $total_rows / $pagination_perpage ) : 0;
							for ( $page=1; $page <= $total_pages; $page++ ) {
								echo '<a' . ( $page == $pagination_page ? ' class="selected"' : '' ) . ' href="admin.php?page=wc-settings&tab=shipping&section=yith_wc_product_shipping_method&p=' . $page . '">' . $page . '</a>';
							}
						?>
					</div>
					<?php if ( ! $not_editable ) : ?>
						<p>
							<a href="#" class="button button-primary insert"
								data-postid="<?php echo $post_id; ?>"
								data-show_table_role="<?php echo $show_table_role; ?>"
								data-show_table_price="<?php echo $show_table_price; ?>"
								data-show_table_qty="<?php echo $show_table_qty; ?>"
								data-show_table_weight="<?php echo $show_table_weight; ?>"
								data-show_table_taxy="<?php echo $show_table_taxy; ?>"
								data-show_table_geo="<?php echo $show_table_geo; ?>"
								><?php echo __( 'Insert row', 'yith-product-shipping-for-woocommerce' ); ?></a>
							<a href="#" class="button remove disabled"><?php echo __( 'Remove selected', 'yith-product-shipping-for-woocommerce' ); ?></a>
						</p>
					<?php endif; ?>
				</div>
			</div>
			<?php
		}
		
		/**
		 * Print Product Taxonomies
		 *
		 * @since 1.0.0
		 */
		function echo_product_taxonomies_childs_of( $id = 0, $tabs = 0, $taxonomy = 'product_cat', $categories_array = array() ) {
			if ( WC()->shipping->get_shipping_methods()['yith_wc_product_shipping_method']->settings['taxonomies'] != 'yes' ) {
				$categories = get_terms( array(
					'taxonomy'			=> $taxonomy,
					'parent'			=> $id,
					'orderby'			=> 'name',
					'order'				=> 'ASC',
					'hide_empty'		=> false,
					'suppress_filters'	=> true,
				));
				foreach ( $categories as $key => $value ) {
					echo '<option value="' . $value->term_id . '" ' . ( in_array( $value->term_id, $categories_array ) ? 'selected="selected"' : '' ) . '>' . str_repeat( '&#8212;', $tabs ) . ' ' . $value->name . '</option>';
					$childs = get_terms( array(
						'taxonomy'			=> $taxonomy,
						'parent'			=> $value->term_id,
						'orderby'			=> 'name',
						'order'				=> 'ASC',
						'hide_empty'		=> false,
						'suppress_filters'	=> true,
					));
					if ( count( $childs ) > 0 ) { $this->echo_product_taxonomies_childs_of( $value->term_id, $tabs + 1, $taxonomy, $categories_array ); }
				}
			}
		}

		/**
		 * Update Shippings
		 *
		 * @since 1.0.0
		 */
		public function update_shippings( $post_id = 0 ) {
			global $wpdb;
			if ( ! $post_id > 0 ) { $post_id = 0; }

			/**
			 * Update Post Meta
			 */
			if ( $post_id > 0 ) {
                if ( ! empty( $_POST['_yith_product_shipping'] ) || isset( $_POST['_yith_product_shipping_variation'][ $post_id ] ) ) {
                    update_post_meta( $post_id, '_yith_product_shipping', 'yes' );
                } else {
                    delete_post_meta( $post_id, '_yith_product_shipping' );
                }
                $_yith_product_shipping_message = isset( $_POST['_yith_product_shipping_message'] ) ? $_POST['_yith_product_shipping_message'] : '';
                $_yith_product_shipping_message_position = isset( $_POST['_yith_product_shipping_message_position'] ) ? $_POST['_yith_product_shipping_message_position'] : 'before';
				update_post_meta( $post_id, '_yith_product_shipping_message', $_yith_product_shipping_message );
				update_post_meta( $post_id, '_yith_product_shipping_message_position', $_yith_product_shipping_message_position );
			}

			/**
			 * Get values from $_POST array
			 */
			$roles				= ! empty( $_POST['yith_product_role'] )			? $_POST['yith_product_role']			: '';
			$min_cart_qtys		= ! empty( $_POST['yith_product_min_cart_qty'] )	? $_POST['yith_product_min_cart_qty']	: '';
			$max_cart_qtys		= ! empty( $_POST['yith_product_max_cart_qty'] )	? $_POST['yith_product_max_cart_qty']	: '';
			$min_quantities		= ! empty( $_POST['yith_product_min_quantity'] )	? $_POST['yith_product_min_quantity']	: '';
			$max_quantities		= ! empty( $_POST['yith_product_max_quantity'] )	? $_POST['yith_product_max_quantity']	: '';
			$min_weights		= ! empty( $_POST['yith_product_min_weight'] )		? $_POST['yith_product_min_weight']		: '';
			$max_weights		= ! empty( $_POST['yith_product_max_weight'] )		? $_POST['yith_product_max_weight']		: '';
			$min_cart_weights	= ! empty( $_POST['yith_product_min_cart_weight'] )	? $_POST['yith_product_min_cart_weight']: '';
			$max_cart_weights	= ! empty( $_POST['yith_product_max_cart_weight'] )	? $_POST['yith_product_max_cart_weight']: '';
			$min_cart_totals	= ! empty( $_POST['yith_product_min_cart_total'] )	? str_replace( ',', '.', $_POST['yith_product_min_cart_total'] ) : '';
			$max_cart_totals	= ! empty( $_POST['yith_product_max_cart_total'] )	? str_replace( ',', '.', $_POST['yith_product_max_cart_total'] ) : '';
			$categories			= ! empty( $_POST['yith_product_categories'] )		? $_POST['yith_product_categories']		: '';
			$tags				= ! empty( $_POST['yith_product_tags'] )			? $_POST['yith_product_tags']			: '';
			$geo_excludes		= ! empty( $_POST['yith_product_geo_exclude'] )		? $_POST['yith_product_geo_exclude']	: '';
			$country_codes		= ! empty( $_POST['yith_product_country_code'] )	? $_POST['yith_product_country_code']	: '';
			$state_codes		= ! empty( $_POST['yith_product_state_code'] )		? $_POST['yith_product_state_code']		: '';
			$postal_codes		= ! empty( $_POST['yith_product_postal_code'] )		? $_POST['yith_product_postal_code']	: '';
			$zones				= ! empty( $_POST['yith_product_zone'] )			? $_POST['yith_product_zone']			: '';
			$shipping_costs		= ! empty( $_POST['yith_product_shipping_cost'] )	? str_replace( ',', '.', $_POST['yith_product_shipping_cost'] )	: '';
			$product_costs		= ! empty( $_POST['yith_product_product_cost'] )	? str_replace( ',', '.', $_POST['yith_product_product_cost'] )	: '';
			$unique_costs		= ! empty( $_POST['yith_product_unique_cost'] )		? str_replace( ',', '.', $_POST['yith_product_unique_cost'] )	: '';

			$i = ! empty( $_POST['pagination_offset'] ) ? $_POST['pagination_offset'] : 0;

			if ( $min_quantities ) {

				foreach ( $min_quantities as $key => $value ) {

					if ( $key == 'new' ) {

						foreach ( $value as $new_key => $new_value ) {

							$role			= ! empty( $roles[ $key ][ $new_key ] )				? $roles[ $key ][ $new_key ]			: 0;
							$min_cart_qty	= ! empty( $min_cart_qtys[ $key ][ $new_key ] )		? $min_cart_qtys[ $key ][ $new_key ]	: 1;
							$max_cart_qty	= ! empty( $max_cart_qtys[ $key ][ $new_key ] )		? $max_cart_qtys[ $key ][ $new_key ]	: '';
							$min_quantity	= ! empty( $min_quantities[ $key ][ $new_key ] )	? $min_quantities[ $key ][ $new_key ]	: 1;
							$max_quantity	= ! empty( $max_quantities[ $key ][ $new_key ] )	? $max_quantities[ $key ][ $new_key ]	: '';
							$min_weight		= ! empty( $min_weights[ $key ][ $new_key ] )		? $min_weights[ $key ][ $new_key ]		: 0;
							$max_weight		= ! empty( $max_weights[ $key ][ $new_key ] )		? $max_weights[ $key ][ $new_key ]		: '';
							$min_cart_weight= ! empty( $min_cart_weights[ $key ][ $new_key ] )	? $min_cart_weights[ $key ][ $new_key ]	: 0;
							$max_cart_weight= ! empty( $max_cart_weights[ $key ][ $new_key ] )	? $max_cart_weights[ $key ][ $new_key ]	: '';
							$min_cart_total	= ! empty( $min_cart_totals[ $key ][ $new_key ] )	? str_replace( ',', '.', $min_cart_totals[ $key ][ $new_key ] )	: 0;
							$max_cart_total	= ! empty( $max_cart_totals[ $key ][ $new_key ] )	? str_replace( ',', '.', $max_cart_totals[ $key ][ $new_key ] )	: 0;
							$category_ids	= ! empty( $categories[ $key ][ $new_key ] )		? $categories[ $key ][ $new_key ]		: 0;
							$tag_ids		= ! empty( $tags[ $key ][ $new_key ] )				? $tags[ $key ][ $new_key ]				: 0;
							$geo_exclude	= ! empty( $geo_excludes[ $key ][ $new_key ] )		? $geo_excludes[ $key ][ $new_key ]		: 0;
							$country_code	= ! empty( $country_codes[ $key ][ $new_key ] )		? $country_codes[ $key ][ $new_key ]	: '';
							$state_code		= ! empty( $state_codes[ $key ][ $new_key ] )		? $state_codes[ $key ][ $new_key ]		: '';
							$postal_code	= ! empty( $postal_codes[ $key ][ $new_key ] )		? $postal_codes[ $key ][ $new_key ]		: '';
							$zone			= ! empty( $zones[ $key ][ $new_key ] )				? $zones[ $key ][ $new_key ]			: 0;
							$shipping_cost	= ! empty( $shipping_costs[ $key ][ $new_key ] )	? str_replace( ',', '.', $shipping_costs[ $key ][ $new_key ] )	: 0;
							$product_cost	= ! empty( $product_costs[ $key ][ $new_key ] ) 	? str_replace( ',', '.', $product_costs[ $key ][ $new_key ] )	: 0;
							$unique_cost	= ! empty( $unique_costs[ $key ][ $new_key ] ) 		? str_replace( ',', '.', $unique_costs[ $key ][ $new_key ] )	: 0;

							$category_ids 	= is_array( $category_ids ) ? 'ID:' . implode( ',ID:', $category_ids ) : $category_ids;
							$tag_ids 		= is_array( $tag_ids ) ? 'ID:' . implode( ',ID:', $tag_ids ) : $tag_ids;

							$geo_exclude	= is_array( $geo_exclude ) ? $geo_exclude[0] : $geo_exclude;

							$wpdb->insert(
								$wpdb->prefix . 'yith_wcps_shippings',
								array(
									'product_id'		=> absint( $post_id ),
									'role'				=> esc_attr( $role ),
									'min_cart_qty'		=> esc_attr( $min_cart_qty ),
									'max_cart_qty'		=> esc_attr( $max_cart_qty ),
									'min_quantity'		=> esc_attr( $min_quantity ),
									'max_quantity'		=> esc_attr( $max_quantity ),
									'min_weight'		=> esc_attr( $min_weight ),
									'max_weight'		=> esc_attr( $max_weight ),
									'min_cart_weight'	=> esc_attr( $min_cart_weight ),
									'max_cart_weight'	=> esc_attr( $max_cart_weight ),
									'min_cart_total'	=> esc_attr( $min_cart_total ),
									'max_cart_total'	=> esc_attr( $max_cart_total ),
									'categories'		=> esc_attr( $category_ids ),
									'tags'				=> esc_attr( $tag_ids ),
									'geo_exclude'		=> esc_attr( $geo_exclude ),
									'country_code'		=> esc_attr( $country_code ),
									'state_code'		=> esc_attr( $state_code ),
									'postal_code'		=> esc_attr( $postal_code ),
									'zone'				=> esc_attr( $zone ),
									'shipping_cost'		=> esc_attr( $shipping_cost ),
									'product_cost'		=> esc_attr( $product_cost ),
									'unique_cost'		=> esc_attr( $unique_cost ),
									'ord'				=> $i++,
								)
							);

						}

					} else {

						$role			= ! empty( $roles[ $key ] )				? $roles[ $key ]			: 0;
						$min_cart_qty	= ! empty( $min_cart_qtys[ $key ] )		? $min_cart_qtys[ $key ]	: 1;
						$max_cart_qty	= ! empty( $max_cart_qtys[ $key ] )		? $max_cart_qtys[ $key ]	: '';
						$min_quantity	= ! empty( $min_quantities[ $key ] )	? $min_quantities[ $key ]	: 1;
						$max_quantity	= ! empty( $max_quantities[ $key ] )	? $max_quantities[ $key ]	: '';
						$min_weight		= ! empty( $min_weights[ $key ] )		? $min_weights[ $key ]		: 0;
						$max_weight		= ! empty( $max_weights[ $key ] )		? $max_weights[ $key ]		: '';
						$min_cart_weight= ! empty( $min_cart_weights[ $key ] )	? $min_cart_weights[ $key ]	: 0;
						$max_cart_weight= ! empty( $max_cart_weights[ $key ] )	? $max_cart_weights[ $key ]	: '';
						$min_cart_total	= ! empty( $min_cart_totals[ $key ] )	? $min_cart_totals[ $key ]	: 0;
						$max_cart_total	= ! empty( $max_cart_totals[ $key ] )	? $max_cart_totals[ $key ]	: 0;
						$category_ids	= ! empty( $categories[ $key ] )		? $categories[ $key ]		: 0;
						$tag_ids		= ! empty( $tags[ $key ] )				? $tags[ $key ]				: 0;
						$geo_exclude	= ! empty( $geo_excludes[ $key ] )		? $geo_excludes[ $key ]		: 0;
						$country_code	= ! empty( $country_codes[ $key ] )		? $country_codes[ $key ]	: '';
						$state_code		= ! empty( $state_codes[ $key ] )		? $state_codes[ $key ]		: '';
						$postal_code	= ! empty( $postal_codes[ $key ] )		? $postal_codes[ $key ]		: '';
						$zone			= ! empty( $zones[ $key ] )				? $zones[ $key ]			: '';
						$shipping_cost	= ! empty( $shipping_costs[ $key ] )	? $shipping_costs[ $key ]	: 0;
						$product_cost	= ! empty( $product_costs[ $key ] )		? $product_costs[ $key ]	: 0;
						$unique_cost	= ! empty( $unique_costs[ $key ] )		? $unique_costs[ $key ]	: 0;

						$category_ids 	= is_array( $category_ids ) ? 'ID:' . implode( ',ID:', $category_ids ) : $category_ids;
						$tag_ids 		= is_array( $tag_ids ) ? 'ID:' . implode( ',ID:', $tag_ids ) : $tag_ids;

						$geo_exclude	= is_array( $geo_exclude ) ? $geo_exclude[0] : $geo_exclude;
						$zone			= is_array( $zone ) ? $zone[0] : $zone;

						if ( $min_quantities[ $key ] > 0 ) {

							$wpdb->update(
								$wpdb->prefix . 'yith_wcps_shippings',
								array(
									'role'				=> esc_attr( $role ),
									'min_cart_qty'		=> esc_attr( $min_cart_qty ),
									'max_cart_qty'		=> esc_attr( $max_cart_qty ),
									'min_quantity'		=> esc_attr( $min_quantity ),
									'max_quantity'		=> esc_attr( $max_quantity ),
									'min_weight'		=> esc_attr( $min_weight ),
									'max_weight'		=> esc_attr( $max_weight ),
									'min_cart_weight'	=> esc_attr( $min_cart_weight ),
									'max_cart_weight'	=> esc_attr( $max_cart_weight ),
									'min_cart_total'	=> esc_attr( $min_cart_total ),
									'max_cart_total'	=> esc_attr( $max_cart_total ),
									'categories'		=> esc_attr( $category_ids ),
									'tags'				=> esc_attr( $tag_ids ),
									'geo_exclude'		=> esc_attr( $geo_exclude ),
									'country_code'		=> esc_attr( $country_code ),
									'state_code'		=> esc_attr( $state_code ),
									'postal_code'		=> esc_attr( $postal_code ),
									'zone'				=> esc_attr( $zone ),
									'shipping_cost'		=> esc_attr( $shipping_cost ),
									'product_cost'		=> esc_attr( $product_cost ),
									'unique_cost'		=> esc_attr( $unique_cost ),
									'ord'				=> $i++,
								),
								array(
									'id' 				=> absint( $key )
								)
							);

						} else {

							$query = $wpdb->prepare( "DELETE FROM {$wpdb->prefix}yith_wcps_shippings WHERE id = %s;", absint( $key ) );
							$wpdb->query( $query );

						}
						
					}

				}

			}

		}
		
	}

}
