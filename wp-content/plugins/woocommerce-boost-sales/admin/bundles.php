<?php
/**
 * Admin class
 *
 * @author  Cuong Nguyen
 * @package WBS
 * @version 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! defined( 'VI_WBOOSTSALES_TEMPLATES' ) ) {
	define( 'VI_WBOOSTSALES_TEMPLATES', VI_WBOOSTSALES_DIR . "templates" . DIRECTORY_SEPARATOR );
}
if ( ! class_exists( 'VI_WBOOSTSALES_Admin_Bundles' ) ) {
	/**
	 * Admin class.
	 * The class manage all the admin behaviors.
	 */
	class VI_WBOOSTSALES_Admin_Bundles {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Plugin options
		 *
		 * @var array
		 * @access public
		 * @since  1.0.0
		 */
		public $options = array();


		public $templates = array();

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
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
			//Add action links
			//add_action('init',array($this,'init'));
			add_action( 'plugins_loaded', array( $this, 'register_wbs_bundle_product_type' ), 1 );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

			add_filter( 'woocommerce_product_data_tabs', array( $this, 'woocommerce_product_data_tabs' ) );
			add_action( 'woocommerce_product_data_panels', array( $this, 'woocommerce_product_data_panels' ) );
			add_action( 'wp_ajax_wbs_wcpb_add_product_in_bundle', array( $this, 'add_product_in_bundle' ) );
			add_action( 'wp_ajax_wbs_search_product_in_bundle', array( $this, 'wbs_search_product_in_bundle' ) );
			add_action( 'woocommerce_process_product_meta', array( $this, 'woocommerce_process_product_meta' ) );
			add_action( 'woocommerce_process_product_meta_wbs_bundle', array( $this, 'save_price' ) );
			add_action( 'wbs_wcpb_admin_product_bundle_data', array(
				$this,
				'wbs_wcpb_admin_product_bundle_data'
			), 10, 4 );
			add_filter( 'product_type_selector', array( $this, 'product_type_selector' ) );

			// Admin ORDER
			add_filter( 'woocommerce_admin_html_order_item_class', array(
				$this,
				'woocommerce_admin_html_order_item_class'
			), 10, 2 );
			add_filter( 'woocommerce_admin_order_item_class', array(
				$this,
				'woocommerce_admin_html_order_item_class'
			), 10, 2 );
			add_filter( 'woocommerce_admin_order_item_count', array(
				$this,
				'woocommerce_admin_order_item_count'
			), 10, 2 );
			add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'woocommerce_hidden_order_itemmeta' ) );

		}

		public function init() {
			require_once VI_WBOOSTSALES_INCLUDES . "wbs_bundled_item.php";
		}

		public function register_wbs_bundle_product_type() {
			require_once VI_WBOOSTSALES_INCLUDES . "wbs_bundled_item.php";
			require_once VI_WBOOSTSALES_INCLUDES . "wbs_product_bundle.php";
		}

		/**
		 * Hide wbs_bundled_by meta in admin order
		 *
		 * @param array $hidden
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 * @return array
		 */
		public function woocommerce_hidden_order_itemmeta( $hidden ) {
			return array_merge( $hidden, array( '_bundled_by', '_cartstamp' ) );
		}

		/**
		 * add CSS class in admin order bundled items
		 *
		 *
		 * @param string $class
		 * @param array $item
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 * @return string
		 */
		public function woocommerce_admin_html_order_item_class( $class, $item ) {
			if ( isset( $item['wbs_bundled_by'] ) ) {
				return $class . ' wbs-wcpb-admin-bundled-item';
			}

			return $class;
		}

		/**
		 * Filter item count in admin orders page
		 *
		 * @param int $count
		 * @param WC_Order $order
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 *
		 * @return int|string
		 */
		public function woocommerce_admin_order_item_count( $count, $order ) {
			$counter = 0;
			foreach ( $order->get_items() as $item ) {
				if ( isset( $item['wbs_bundled_by'] ) ) {
					$counter += $item['qty'];
				}
			}
			if ( $counter > 0 ) {
				$non_bundled_count = $count - $counter;

				return sprintf( _n( '%1$s item [%2$s bundled elements]', '%1$s items [%2$s bundled elements]', $non_bundled_count, 'woocommerce-boost-sales' ), $non_bundled_count, $counter );
			}

			return $count;
		}

		/**
		 * add Product Bundle type in product type selector [in product wc-metabox]
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function product_type_selector( $types ) {
			$types['wbs_bundle'] = _x( 'WBS Product Bundle', 'Admin: type of product', 'woocommerce-boost-sales' );

			return $types;
		}

		/**
		 * bundle items data form
		 *
		 */
		public function wbs_wcpb_admin_product_bundle_data( $metabox_id, $product_id, $item_data, $post_id ) {
			$bp_quantity = isset( $item_data['bp_quantity'] ) ? $item_data['bp_quantity'] : 1;
			?>
            <table>
                <tr>
                    <td><?php echo _ex( 'Quantity', 'Admin: quantity of the bundled product.', 'woocommerce-boost-sales' ); ?></td>
                    <td>
                        <input type="number" size="4" value="<?php echo $bp_quantity ?>"
                               name="_wbs_wcpb_bundle_data[<?php echo $metabox_id; ?>][bp_quantity]"
                               class="wbs-wcpb-bp-quantity short"></td>
                </tr>
            </table>
			<?php
		}

		/**
		 * Ajax Called in bundle_options_metabox.js
		 * return the empty form for the item
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function add_product_in_bundle() {
			$metabox_id = intval( $_POST['id'] );
			$post_id    = intval( $_POST['post_id'] );
			$product_id = intval( $_POST['product_id'] );

			$title = get_the_title( $product_id );

			ob_start();
			include VI_WBOOSTSALES_TEMPLATES . 'admin/admin-bundled-product-item.php';
			echo ob_get_clean();

			die();
		}

		/**
		 * add Bundle Options Tab [in product wc-metabox]
		 *
		 */
		public function woocommerce_product_data_tabs( $product_data_tabs ) {
			$product_data_tabs['wbs_bundled_options'] = array(
				'label'  => __( 'Bundle Options', 'woocommerce-boost-sales' ),
				'target' => 'wbs_bundled_product_data',
				'class'  => array( 'show_if_wbs_bundle' ),
			);

			return $product_data_tabs;
		}

		/**
		 * add panel for Bundle Options Tab [in product wc-metabox]
		 *
		 */
		public function woocommerce_product_data_panels() {
			global $post;
			$bundle_data = get_post_meta( $post->ID, '_wbs_wcpb_bundle_data', true );
			$price       = get_post_meta( $post->ID, '_price', true );
			?>
            <div id="wbs_bundled_product_data" class="panel woocommerce_options_panel wc-metaboxes-wrapper">

                <div class="options_group wbs-wcpb-bundle-metaboxes-wrapper">

                    <div id="wbs-wcpb-bundle-metaboxes-wrapper-inner">
                        <p class="form-field">
                            <label for="wbs_bundle_price"><?php printf( esc_html__( 'Price(%s)', 'woocommerce-boost-sales' ), get_woocommerce_currency_symbol() ); ?></label>
                            <input id="wbs_bundle_price" type="text" class="short wc_input_price"
                                   name="wbs_bundle_price"
                                   value="<?php esc_attr_e( $price ) ?>">
                        </p>
                        <p class="toolbar">
                            <a href="#" class="close_all"><?php _e( 'Close all', 'woocommerce-boost-sales' ); ?></a>
                            <a href="#" class="expand_all"><?php _e( 'Expand all', 'woocommerce-boost-sales' ); ?></a>
                        </p>

                        <div class="wbs-wcpb-bundled-items wc-metaboxes ui-sortable">
							<?php
							if ( ! empty( $bundle_data ) ) {
								$i = 0;
								foreach ( $bundle_data as $item_id => $item_data ) {
									//$metabox_id     = $item_data[ 'bundle_order' ];
									$i ++;
									$metabox_id = $i;
									$post_id    = $post->ID;
									$product_id = $item_data['product_id'];

									$title       = get_the_title( $product_id );
									$open_closed = 'closed';
									ob_start();
									include VI_WBOOSTSALES_TEMPLATES . 'admin/admin-bundled-product-item.php';
									echo ob_get_clean();
								}
							}
							?>
                        </div>
                        <p class="wbs-wcpb-bundled-prod-toolbar toolbar">
                            <span class="wbs-wcpb-bundled-prod-toolbar-wrapper">
                                <span class="wbs-wcpb-bundled-prod-selector">
                                    <select type="hidden" class="wc-product-search" style="width: 250px;"
                                            id="wbs-wcpb-bundled-product"
                                            name="wbs_wcpb_bundled_product"
                                            data-placeholder="<?php _e( 'Search for a product&hellip;', 'woocommerce-boost-sales' ); ?>"
                                            data-exclude="<?php echo $post->ID; ?>">
									</select>
                                </span>
                                <button type="button" id="wbs-wcpb-add-bundled-product"
                                        class="button button-primary"><?php _e( 'Add Product', 'woocommerce-boost-sales' ); ?></button>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
			<?php
		}

		public function wbs_search_product_in_bundle() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			ob_start();

			$keyword = filter_input( INPUT_GET, 'keyword', FILTER_SANITIZE_STRING );

			if ( empty( $keyword ) ) {
				die();
			}
			$found_products = array();
			$args           = array(
				'post_type'      => 'product',
				'post_status'    => VI_WBOOSTSALES_Data::search_product_statuses(),
				'posts_per_page' => 100,
				's'              => $keyword
			);
			$products       = get_posts( $args );
			if ( count( $products ) ) {
				foreach ( $products as $pas ) {
					$asb = wc_get_product( $pas );
					if ( $asb->get_type() != 'wbs_bundle' ) {
						if ( $asb->get_type() == 'variable' && $asb->has_child() ) {
							$product          = array(
								'id'   => $asb->get_id(),
								'text' => $asb->get_title() . ' (#' . $asb->get_id() . ') #PARENT'
							);
							$found_products[] = $product;
							$get_ch           = $asb->get_children();
							foreach ( $get_ch as $child ) {
								$child_var        = wc_get_product( $child );
								$chi_id           = $child_var->get_id();
								$chi_name         = $child_var->get_name();
								$product          = array(
									'id'   => $chi_id,
									'text' => $chi_name . ' (#' . $chi_id . ')'
								);
								$found_products[] = $product;
							}
						} else {
							$sing_id          = $asb->get_id();
							$sing_name        = $asb->get_name();
							$product          = array(
								'id'   => $sing_id,
								'text' => $sing_name . ' (#' . $sing_id . ')'
							);
							$found_products[] = $product;
						}
					}
				}
			}

			wp_send_json( $found_products );
			die;

		}

		/**
		 * @param $post_id
		 */
		public function woocommerce_process_product_meta( $post_id ) {
			$bundle_data  = isset( $_POST['_wbs_wcpb_bundle_data'] ) ? $_POST['_wbs_wcpb_bundle_data'] : false;

			update_post_meta( $post_id, '_wbs_wcpb_bundle_data', $bundle_data );

		}
		public function save_price( $post_id ) {
			$bundle_price = isset( $_POST['wbs_bundle_price'] ) ? $_POST['wbs_bundle_price'] : '';
			update_post_meta( $post_id, '_price', $bundle_price );
			update_post_meta( $post_id, '_regular_price', $bundle_price );
		}

		public function admin_enqueue_scripts() {
			wp_enqueue_style( 'wbs-wcpb-admin-styles', VI_WBOOSTSALES_CSS . 'bundle-admin.css' );
			wp_enqueue_script( 'jquery-ui-tabs' );

			$screen = get_current_screen();
			if ( 'product' == $screen->id ) {
				wp_enqueue_script( 'jquery-ui-sortable' );
				wp_enqueue_script( 'woocommerce-boost-sales', VI_WBOOSTSALES_JS . 'bundles.js', array( 'jquery' ) );
			}
		}

	}
}

/**
 * Unique access to instance of VI_WBOOSTSALES_Admin_Bundles class
 *
 * @return \VI_WBOOSTSALES_Admin_Bundles
 * @since 1.0.0
 */
function VI_WBOOSTSALES_Admin_Bundles() {
	return VI_WBOOSTSALES_Admin_Bundles::get_instance();
}
