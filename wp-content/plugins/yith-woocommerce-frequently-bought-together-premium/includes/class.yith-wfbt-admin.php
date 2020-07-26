<?php
/**
 * Admin class
 *
 * @author  YITH
 * @package YITH WooCommerce Frequently Bought Together Premium
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WFBT' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WFBT_Admin' ) ) {
	/**
	 * Admin class.
	 * The class manage all the admin behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WFBT_Admin {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var \YITH_WFBT_Admin
		 */
		protected static $instance;

		/**
		 * Plugin product data options
		 *
		 * @since 1.3.0
		 * @var array
		 */
		public $product_options = array();

		/**
		 * Plugin version
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $version = YITH_WFBT_VERSION;

		/**
		 * @var $_panel Panel Object
		 */
		protected $_panel;

		/**
		 * @var string Waiting List panel page
		 */
		protected $_panel_page = 'yith_wfbt_panel';

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 * @return YITH_WFBT_Admin
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function __construct() {

			add_action( 'init', array( $this, 'init_vars' ), 1 );
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

			//Add action links
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_WFBT_DIR . '/' . basename( YITH_WFBT_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			// register plugin to licence/update system
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			// enqueue style and scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// custom tab
			add_action( 'yith_wfbt_data_table', array( $this, 'data_table' ) );

			// add section in product edit page
			add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_bought_together_tab' ), 10, 1 );
			add_action( 'woocommerce_product_data_panels', array( $this, 'add_bought_together_panel' ) );
			// delete linked product
			add_action( 'wp_ajax_yith_wfbt_delete_linked', array( $this, 'delete_linked_product' ) );
			// ajax update list of variation for variable product
			add_action( 'wp_ajax_yith_update_variation_list', array( $this, 'yith_ajax_update_variation_list' ) );
			// search product
			add_action( 'wp_ajax_yith_ajax_search_product', array( $this, 'yith_ajax_search_product' ) );

			// table action
			add_action( 'admin_init', array( $this, 'table_actions' ), 10 );

			add_filter( 'woocommerce_admin_settings_sanitize_option_yith-wfbt-discount-name', array( $this, 'sanitize_discount' ), 10, 3 );

			// save tabs options
			$product_types = apply_filters( 'yith_wfbt_product_types_meta_save', array(
				'simple',
				'variable',
				'grouped',
				'external',
				'rentable',
			) );
			foreach ( $product_types as $product_type ) {
				add_action( 'woocommerce_process_product_meta_' . $product_type, array( $this, 'save_bought_together_tab' ), 10, 1 );
			}

			// add custom image size type
			add_action( 'woocommerce_admin_field_yith_image_size', array( $this, 'custom_image_size' ), 10, 1 );

			add_action( 'yith_wfbt_product_panel_before_field_discount_type', array( $this, 'maybe_add_coupon_alert' ), 10 );

			// Support on variations
			add_filter( 'yith_wfbt_product_types_to_skip', array( $this, 'enable_variable_products' ) );
		}

		/**
		 * Init plugin admin vars
		 *
		 * @since  2.0.0
		 * @author Francesco Licandro
		 */
		public function init_vars() {
			$this->product_options = include( YITH_WFBT_DIR . '/plugin-options/product-data-options.php' );
		}

		/**
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 *
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @param $links | links plugin array
		 *
		 * @return   mixed Array
		 * @return mixed
		 * @use      plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, $this->_panel_page, true );
			return $links;
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use      /Yit_Plugin_Panel class
		 * @return   void
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {

			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$admin_tabs = array(
				'general' 		=> __( 'Settings', 'yith-woocommerce-frequently-bought-together' ),
				'label-style'	=> __( 'Label & Style', 'yith-woocommerce-frequently-bought-together' ),
				'data'    		=> __( 'Linked Products', 'yith-woocommerce-frequently-bought-together' ),
			);

			if ( defined( 'YITH_WCWL' ) && YITH_WCWL ) {
				$admin_tabs['slider'] = __( 'Slider in Wishlist', 'yith-woocommerce-frequently-bought-together' );
			}

			$args = array(
				'create_menu_page' => apply_filters( 'yith-wfbt-register-panel-create-menu-page', true ),
				'parent_slug'      => '',
				'page_title'       => _x( 'YITH WooCommerce Frequently Bought Together', 'plugin name in admin page title', 'yith-woocommerce-frequently-bought-together' ),
				'menu_title'       => _x( 'Frequently Bought Together', 'plugin name in admin WP menu', 'yith-woocommerce-frequently-bought-together' ),
				'capability'       => apply_filters( 'yith-wfbt-register-panel-capabilities', 'manage_options' ),
				'parent'           => '',
				'parent_page'      => apply_filters( 'yith-wfbt-register-panel-parent-page', 'yith_plugin_panel' ),
				'page'             => $this->_panel_page,
				'admin-tabs'       => apply_filters( 'yith-wfbt-admin-tabs', $admin_tabs ),
				'options-path'     => YITH_WFBT_DIR . '/plugin-options',
				'class'            => yith_set_wrapper_class(),
			);

			/* === Fixed: not updated theme  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once( YITH_WFBT_DIR . '/plugin-fw/lib/yit-plugin-panel-wc.php' );
			}

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		/**
		 * plugin_row_meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use      plugin_row_meta
		 * @param $plugin_data
		 * @param $status
		 *
		 * @param $plugin_meta
		 * @param $plugin_file
		 * @return   Array
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status ) {
			if ( defined( 'YITH_WFBT_INIT' ) && YITH_WFBT_INIT == $plugin_file ) {
				$new_row_meta_args['slug'] = YITH_WFBT_SLUG;

				if ( defined( 'YITH_WFBT_PREMIUM' ) ) {
					$new_row_meta_args['is_premium'] = true;
				}
			}
			return $new_row_meta_args;
		}

		/**
		 * Register plugins for activation tab
		 *
		 * @since 1.3.0
		 * @return void
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once( YITH_WFBT_DIR . 'plugin-fw/licence/lib/yit-licence.php' );
				require_once( YITH_WFBT_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php' );
			}

			YIT_Plugin_Licence()->register( YITH_WFBT_INIT, YITH_WFBT_SECRET_KEY, YITH_WFBT_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @since 1.3.0
		 * @return void
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once( YITH_WFBT_DIR . 'plugin-fw/lib/yit-upgrade.php' );
			}

			YIT_Upgrade()->register( YITH_WFBT_SLUG, YITH_WFBT_INIT );
		}

		/**
		 * Add custom image size to standard WC types
		 *
		 * @since  1.0.0
		 * @access public
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function custom_image_size( $value ) {


			$option_values = get_option( 'yith-wfbt-image-size' );
			$width         = isset( $option_values['width'] ) ? $option_values['width'] : $value['default']['width'];
			$height        = isset( $option_values['height'] ) ? $option_values['height'] : $value['default']['height'];
			$crop          = isset( $option_values['crop'] ) ? $option_values['crop'] : $value['default']['crop'];

			?>
			<tr valign="top">
			<th scope="row" class="titledesc"><?php echo esc_html( $value['title'] ) ?></th>
			<td class="forminp yith_image_size_settings">
				<div class="yith_image_size_wrap">
					<input name="<?php echo esc_attr( $value['id'] ); ?>[width]"
						id="<?php echo esc_attr( $value['id'] ); ?>-width" type="text" size="3"
						value="<?php echo esc_attr( $width ); ?>"/><span>&times;</span>
					<input name="<?php echo esc_attr( $value['id'] ); ?>[height]"
						id="<?php echo esc_attr( $value['id'] ); ?>-height" type="text" size="3"
						value="<?php echo esc_attr( $height ); ?>"/><span>px</span>

					<label><input name="<?php echo esc_attr( $value['id'] ); ?>[crop]"
							id="<?php echo esc_attr( $value['id'] ); ?>-crop" type="checkbox"
							value="1" <?php checked( 1, $crop ); ?> /> <?php _e( 'Hard Crop?', 'woocommerce' ); ?></label>
				</div>
				<span class="description"><?php echo wp_kses_post( $value['desc'] ); ?></span>
			</td>
			</tr><?php

		}

		/**
		 * Enqueue scripts
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function enqueue_scripts() {

			global $post;

			if ( ( ! empty( $post ) && get_post_type( $post->ID ) == 'product' )
				|| isset( $_GET['page'] ) && $_GET['page'] === $this->_panel_page ) {

				$suffix      = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				wp_enqueue_style( 'yith-wfbt-style', YITH_WFBT_ASSETS_URL . '/css/yith-wfbt-admin.css' );

				if( ! empty( $post ) ) {
					wp_enqueue_script( 'yith-wfbt-script', YITH_WFBT_ASSETS_URL . '/js/yith-wfbt-admin'.$suffix.'.js', array( 'jquery' ), false, true );
					wp_localize_script( 'yith-wfbt-script', 'yith_wfbt', array(
						'ajaxurl' => admin_url( 'admin-ajax.php' ),
						'postID'  => $post->ID,
					) );
				}
				elseif( isset( $_GET['tab'] ) && $_GET['tab'] === 'data' ) {

					wp_enqueue_script( 'yith-wfbt-script', YITH_WFBT_ASSETS_URL . '/js/yith-wfbt-table'.$suffix.'.js', array( 'jquery' ), false, true );
					wp_localize_script( 'yith-wfbt-script', 'yith_wfbt', array(
						'ajaxurl' 		=> admin_url( 'admin-ajax.php' ),
						'action'		=> 'yith_wfbt_delete_linked',
						'security'		=> wp_create_nonce( 'yith_wfbt_delete_linked' ),
						'deleteLabel'	=> _x( 'Delete', 'Delete single linked product', 'yith-woocommerce-frequently-bought-together' )
					) );
				}

			}
		}

		/**
		 * Print data table
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function data_table() {

			include_once 'admin-tables/class.yith-wfbt-products-table.php';
			$table = new YITH_WFBT_Products_Table();
			$table->prepare_items();

			// then template
			include_once YITH_WFBT_DIR . '/templates/admin/data-table.php';
		}

		/**
		 * Add bought together tab in edit product page
		 *
		 * @since  1.0.0
		 *
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param mixed $tabs
		 * @return mixed
		 */
		public function add_bought_together_tab( $tabs ) {

			$tabs['yith-wfbt'] = array(
				'label'  => _x( 'Frequently Bought Together', 'tab in product data box', 'yith-woocommerce-frequently-bought-together' ),
				'target' => 'yith_wfbt_data_option',
				'class'  => array( 'hide_if_grouped', 'hide_if_external', 'hide_if_bundle' ),
			);

			return $tabs;
		}

		/**
		 * Add bought together panel in edit product page
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function add_bought_together_panel() {

			global $post, $product_object;

			$product_id = $post->ID;
			is_null( $product_object ) && $product_object = wc_get_product( $product_id );
			$to_exclude = array( $product_id );

			$metas   = yith_wfbt_get_meta( $product_object );
			$options = $this->product_options;

			if ( file_exists( YITH_WFBT_DIR . '/templates/admin/product-panel.php' ) ) {
				include_once( YITH_WFBT_DIR . '/templates/admin/product-panel.php' );
			}
		}

		/**
		 * Get variations id for variable post
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param bool   $only_id get only id
		 * @param string $post_id
		 * @return mixed
		 */
		public function get_variations( $post_id, $only_id = false ) {

			// Get variations
			$args = array(
				'post_type'   => 'product_variation',
				'post_status' => array( 'private', 'publish' ),
				'numberposts' => -1,
				'orderby'     => 'menu_order',
				'order'       => 'asc',
				'post_parent' => $post_id,
			);

			$posts  = get_posts( $args );
			$return = array();

			foreach ( $posts as $post ) {
				$product_id = $post->ID;
				$product    = wc_get_product( $product_id );

				if ( ! $product ) {
					continue;
				}

				if ( $only_id ) {
					$variation = $product_id;
				} else {
					$variation['id']   = $product_id;
					$variation['name'] = '#' . $product_id;

					$attrs = $product->get_variation_attributes();
					foreach ( $attrs as $attr ) {
						$variation['name'] .= ' - ' . $attr;
					}
				}

				$return[] = $variation;
			}

			return $return;

		}

		/**
		 * Ajax action search product
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function yith_ajax_search_product() {

			global $post;

			ob_start();

			check_ajax_referer( 'search-products', 'security' );

			$term       = (string) wc_clean( stripslashes( $_GET['term'] ) );
			$post_types = array( 'product', 'product_variation' );

			$to_exclude = isset( $_GET['exclude'] ) ? explode( ',', $_GET['exclude'] ) : false;

			if ( empty( $term ) ) {
				die();
			}

			$args = array(
				'post_type'      => $post_types,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				's'              => $term,
				'fields'         => 'ids',
			);

			if ( $to_exclude ) {
				$args['post__not_in'] = $to_exclude;
			}

			if ( is_numeric( $term ) ) {

				$args2 = array(
					'post_type'      => $post_types,
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'post__in'       => array( 0, $term ),
					'fields'         => 'ids',
				);

				$args3 = array(
					'post_type'      => $post_types,
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'post_parent'    => $term,
					'fields'         => 'ids',
				);

				$args4 = array(
					'post_type'      => $post_types,
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'meta_query'     => array(
						array(
							'key'     => '_sku',
							'value'   => $term,
							'compare' => 'LIKE',
						),
					),
					'fields'         => 'ids',
				);

				$posts = array_unique( array_merge( get_posts( $args ), get_posts( $args2 ), get_posts( $args3 ), get_posts( $args4 ) ) );

			} else {

				$args2 = array(
					'post_type'      => $post_types,
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'meta_query'     => array(
						array(
							'key'     => '_sku',
							'value'   => $term,
							'compare' => 'LIKE',
						),
					),
					'fields'         => 'ids',
				);

				$posts = array_unique( array_merge( get_posts( $args ), get_posts( $args2 ) ) );

			}

			$found_products = array();

			if ( $posts ) {
				foreach ( $posts as $post ) {
					$current_id = $post;
					$product    = wc_get_product( $post );

					$types_to_skip = apply_filters( 'yith_wfbt_product_types_to_skip', array( 'variable', 'external' ) );

					// exclude variable product
					if ( ! $product || $product->is_type( $types_to_skip ) ) {
						continue;
					} elseif ( $product->is_type( 'variation' ) ) {
						$current_id = wp_get_post_parent_id( $post );
						if ( ! wc_get_product( $current_id ) ) {
							continue;
						}
					}

					// last check for vendor
					if ( get_option( 'yith-wfbt-vendor-products', 'no' ) == 'no' && function_exists( 'YITH_WFBT_Multivendor' ) && ! YITH_WFBT_Multivendor()->is_vendor_product( $current_id ) ) {
						continue;
					}

					$found_products[ $post ] = rawurldecode( $product->get_formatted_name() );
				}
			}

			wp_send_json( apply_filters( 'yith_wfbt_ajax_search_product_result', $found_products ) );
		}

		/**
		 * Ajax action that hanlde delete linked product from admin table
		 *
		 * @since 2.0.0
		 * @author Francesco Licandro
		 * @return void
		 */
		public function delete_linked_product() {
			check_ajax_referer( 'yith_wfbt_delete_linked', 'security' );

			$product_id = ! empty( $_POST[ 'product_id' ] ) ? intval( $_POST[ 'product_id' ] ) : 0;
			$linked_id 	= ! empty( $_POST[ 'linked_id' ] ) ? intval( $_POST[ 'linked_id' ] ) : 0;

			if( empty( $product_id ) || empty( $linked_id ) || ! ( $product = wc_get_product( $product_id ) ) ) {
				wp_send_json_error();
			}

			// get meta
			$products = yith_wfbt_get_meta( $product, 'products' );
			// remove
			$diff = array_diff( $products, array( $linked_id ) );
			yith_wfbt_set_meta( $product, array( 'products' => $diff ) );

			wp_send_json_success();
		}

		/**
		 * Save options
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param $post_id
		 */
		public function save_bought_together_tab( $post_id ) {

			$product  = wc_get_product( $post_id );
			$new_meta = array();

			foreach ( $this->product_options as $fields ) {
				foreach ( $fields as $key => $field ) {

					if ( ! is_array( $field ) ) {
						continue;
					}

					$val = '';
					switch ( $field['type'] ) {

						case 'checkbox':
							$val = isset( $_POST[ $field['name'] ] ) ? 'yes' : 'no';
							break;

						case 'product_select':
							// save products group
							$val = array();
							if ( isset( $_POST[ $field['name'] ] ) ) {
								$val = ! is_array( $_POST[ $field['name'] ] ) ? explode( ',', $_POST[ $field['name'] ] ) : $_POST[ $field['name'] ];
								$val = array_filter( array_map( 'intval', $val ) );
							}
							break;

						case 'variation_select':
							$selected_variation = isset( $_POST[ $field['name'] ] ) ? $_POST[ $field['name'] ] : '';
							$variations         = $this->get_variations( $post_id, true );
							// save selected if is valid
							if ( ! empty( $variations ) && in_array( $selected_variation, $variations, false ) ) {
								$val = $selected_variation;
							} // else save first
							elseif ( ! empty( $variations ) ) {
								$val = array_shift( $variations );
							}
							break;

						default:
							if ( isset( $_POST[ $field['name'] ] ) ) {
								$val = $field['type'] == 'number' ? intval( $_POST[ $field['name'] ] ) : $_POST[ $field['name'] ];

								if ( $field['type'] == 'number' && ! empty ( $field['attr'] ) ) {
									// check min
									( isset( $field['attr']['min'] ) && $val < $field['attr']['min'] ) && $val = $field['attr']['min'];
									( isset( $field['attr']['max'] ) && $val > $field['attr']['max'] ) && $val = $field['attr']['max'];
								}

								if ( isset( $field['class'] ) && $field['class'] == 'wc_input_price' ) {
									$val = wc_format_decimal( sanitize_text_field( wp_unslash( $val ) ), wc_get_price_decimals() );
								}
							}
							break;
					}

					$new_meta[ $key ] = $val;
				}
			}

			// then save
			yith_wfbt_set_meta( $product, $new_meta );
		}

		/**
		 * Update variation list after a var
		 */
		public function yith_ajax_update_variation_list() {

			if ( ! isset( $_POST['productID'] ) ) {
				die();
			}

			$id      = intval( $_POST['productID'] );
			$product = wc_get_product( $id );

			ob_start();

			$variations = $this->get_variations( $id );
			$selected   = yith_wfbt_get_meta( $product, 'default_variation' );
			foreach ( $variations as $variation ) : ?>
				<option value="<?php echo $variation['id'] ?>" <?php selected( $variation['id'], $selected ) ?>><?php echo $variation['name'] ?></option>
			<?php endforeach;

			echo ob_get_clean(); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
			die();
		}

		/**
		 * Get panel page name
		 *
		 * @access public
		 * @since  1.1.4
		 * @author Francesco Licandro
		 */
		public function get_panel_page_name() {
			return $this->_panel_page;
		}

		/**
		 * Handle table action
		 *
		 * @since  1.3.0
		 * @author Francesco Licandro
		 */
		public function table_actions() {

			$page   = isset( $_GET['page'] ) ? $_GET['page'] : '';
			$tab    = isset( $_GET['tab'] ) ? $_GET['tab'] : '';
			$action = isset( $_GET['action'] ) ? $_GET['action'] : '';

			if ( $page != $this->_panel_page || $tab != 'data' || $action == '' ) {
				return;
			}

			// remove linked
			if ( 'delete' == $action ) {

				$ids = isset( $_GET['id'] ) ? $_GET['id'] : array();
				if ( ! is_array( $ids ) ) {
					$ids = explode( ',', $ids );
				}
				// delete post meta
				foreach ( $ids as $id ) {
					$product = wc_get_product( $id );
					if ( ! $product ) {
						continue;
					}
					yith_wfbt_delete_meta( $product );
				}
				// add message
				if ( empty( $ids ) ) {
					$mess = 1;
				} else {
					$mess = 2;
				}
			}

			$list_query_args = array(
				'page' => $page,
				'tab'  => $tab,
			);
			// Set users table
			if ( isset( $_GET['view'] ) && isset( $_GET['post_id'] ) ) {
				$list_query_args['view']    = $_GET['view'];
				$list_query_args['post_id'] = $_GET['post_id'];
			}
			// Add message
			if ( isset( $mess ) && $mess != '' ) {
				$list_query_args['wfbt_mess'] = $mess;
			}

			$list_url = add_query_arg( $list_query_args, admin_url( 'admin.php' ) );

			wp_redirect( $list_url );
			exit;

		}

		/**
		 * Sanitize discount name option
		 *
		 * @since  1.3.4
		 * @author Francesco Licandro
		 * @param string $value
		 * @param array  $option
		 * @param mixed  $raw_value
		 * @return string
		 */
		public function sanitize_discount( $value, $option, $raw_value ) {
			return yith_wfbt_discount_code_validation( $value );
		}

		/**
		 * Maybe add an alert if WC coupons are disabled
		 *
		 * @since  1.4.0
		 * @author Francesco Licandro
		 * @return void
		 */
		public function maybe_add_coupon_alert() {
			if ( ! wc_coupons_enabled() ) {
				?>
				<p class="coupon-disabled-error">
					<?php printf( __( 'You must enable coupons in order to use the plugin discount feature. Please enable it <a href="%s">here.</a>', 'yith-woocommerce-frequently-bought-together' ), admin_url( 'admin.php?page=wc-settings' ) ) ?>
				</p>
				<?php
			}
		}


		/**
		 * Enable to set a variable product at backend
		 * @param $product_type
		 * @return string
		 */
		public function enable_variable_products( $product_type ) {
			$product_type = 'external';
			return $product_type;
		}
	}
}
/**
 * Unique access to instance of YITH_WFBT_Admin class
 *
 * @since 1.0.0
 * @return YITH_WFBT_Admin
 */
function YITH_WFBT_Admin() {
	return YITH_WFBT_Admin::get_instance();
}