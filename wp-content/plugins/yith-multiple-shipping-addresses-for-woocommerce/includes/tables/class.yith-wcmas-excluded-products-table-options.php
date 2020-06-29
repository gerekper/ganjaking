<?php
if ( ! defined( 'YITH_WCMAS_VERSION' ) ) {
	exit; // Exit if accessed directly
}

if( ! class_exists( 'YITH_WCMAS_Excluded_Products_Table_Options' ) ) {
	/**
	 * Helper class for generating Excluded products table
	 *
	 * @class   YITH_WCMAS_Excluded_Products_Table_Options
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 * @since 1.0.0
	 */
	class YITH_WCMAS_Excluded_Products_Table_Options {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCMAS_Excluded_Products_Table_Options
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Meta exclude
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_meta_exclude = '_ycmas_exclude_for_multi_shipping';

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCMAS_Excluded_Products_Table_Options
		 * @since 1.0.0
		 */
		public static function get_instance(){
			if( is_null( self::$instance ) ){
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @access public
		 * @since 1.0.0
		 */
		public function __construct() {
			add_action( 'admin_init', array( $this, 'table_actions' ) );
		}

		/**
		 * Outputs the exclusions table template with insert form in plugin options panel
		 *
		 * @since   1.0.0
		 * @author  Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return  string
		 */
		public function prepare_table() {

			global $wpdb;

			$table = new YITH_WCMAS_Table_Template( array(
				'singular' => esc_html__( 'product', 'yith-woocommerce-one-click-checkout' ),
				'plural'   => esc_html__( 'products', 'yith-woocommerce-one-click-checkout' )
			) );

			$table->options = apply_filters( 'ywcmas_excluded_products_table_options',
				array(
				'select_table'     => $wpdb->prefix . 'postmeta a',
				'select_columns'   => array(
					'a.post_id'
				),
				'select_where'     => 'a.meta_key = "' . $this->_meta_exclude . '" AND a.meta_value = "1"',
				'select_group'     => 'a.post_id',
				'select_order'     => 'a.post_id',
				'select_limit'     => 10,
				'count_table'      => '( SELECT COUNT(*) FROM ' . $wpdb->prefix . 'postmeta a WHERE a.meta_key = "' . $this->_meta_exclude . '" AND a.meta_value="1" GROUP BY a.post_id ) AS count_table',
				'key_column'       => 'post_id',
				'view_columns'     => array(
					'cb'         => '<input type="checkbox" />',
					'product'    => esc_html__( 'Product', 'yith-multiple-shipping-addresses-for-woocommerce' ),
					'stock'      => esc_html__( 'Stock Quantity', 'yith-multiple-shipping-addresses-for-woocommerce' ),
					'price'      => esc_html__( 'Price', 'yith-multiple-shipping-addresses-for-woocommerce'),
					'categories' => esc_html__( 'Categories', 'yith-multiple-shipping-addresses-for-woocommerce' ),
					'actions'    => esc_html__( 'Action', 'yith-multiple-shipping-addresses-for-woocommerce' )
				),
				'get_product'      => 'yes',
				'hidden_columns'   => array(),
				'sortable_columns' => array(
					'product' => array( 'post_title', true )
				),
				'custom_columns'   => array(
					'column_product' => function ( $item, $me, $product ) {
                        /**
                         * @type $product \WC_Product
                         */
						$product_query_args = array(
							'post'   => yit_get_base_product_id( $product ),
							'action' => 'edit'
						);
						$product_url        = add_query_arg( $product_query_args, admin_url( 'post.php' ) );

						$thumb = $product->get_image( 'shop_thumbnail' );

						return sprintf( '<strong><a class="tips" target="_blank" href="%s" data-tip="%s">%s</a></strong>', esc_url( $product_url ), esc_html__( 'Edit product', 'yith-multiple-shipping-addresses-for-woocommerce' ), $thumb . $product->get_title() );
					},
					'column_stock'   => function ( $item, $me, $product ) {
                        /**
                         * @type $product \WC_Product
                         */
						$status = $product->get_availability();
						$class = ( isset( $status['class'] ) && $status['class'] != '' ) ? $status['class'] : 'in-stock';
						$availability = ( isset( $status['availability'] ) && $status['availability'] != '' ) ? $status['availability'] : esc_html__( 'In stock', 'woocommerce' );

						return '<span class="' . esc_attr( $class ) . '">' . esc_html( $availability ) . '</span>';
					},
					'column_price'   => function ( $item, $me, $product ) {
                        /**
                         * @type $product \WC_Product
                         */
						return $product->get_price_html();
					},
					'column_categories' => function( $item, $me, $product ) {
                        /**
                         * @type $product \WC_Product
                         */
						$cat = get_the_terms( $product->get_id(), 'product_cat' );

						if( empty( $cat ) ) {
							echo ' - ';
						}
						else {
							$last = end( $cat );
							foreach( $cat as $key => $value ) {
								echo $value->name;
								if( $last->term_id != $value->term_id ){
									echo ', ';
								}
							}
						}

					},
					'column_actions' => function( $item, $me, $product ) {

						$delete_query_args = array(
							'page'   => $_GET['page'],
							'tab'    => $_GET['tab'],
							'action' => 'delete',
							'id'     => $item['post_id']
						);
						$delete_url        = add_query_arg( $delete_query_args, admin_url( 'admin.php' ) );

						return '<a href="' . esc_url( $delete_url ) . '" class="button">' . esc_html__( 'Remove', 'yith-multiple-shipping-addresses-for-woocommerce' ) . '</a>';

					}
				),
				'bulk_actions'     => array(
					'actions'   => array(
						'delete' => esc_html__( 'Remove from list', 'yith-multiple-shipping-addresses-for-woocommerce' )
					)
				),
			)
			);

			return $table;
		}

		/**
		 * Exclusion table action
		 *
		 * @access public
		 * @since 1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function table_actions(){
			$page    = isset( $_GET['page'] ) ? $_GET['page'] : '';
			$tab     = isset( $_GET['tab'] ) ? $_GET['tab'] : '';
			$action1  = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';
			$action2 = isset( $_REQUEST['action2'] ) ? $_REQUEST['action2'] : '';
			$action = '';
			if ( $action1 && $action1 != '-1' ) {
				$action = $action1;
			} elseif ( $action2 && $action2 != '-1' ) {
				$action = $action2;
			}

			if( $page != 'yith_wcmas_panel' || $tab != 'product-exclusion' || $action == '' ) {
				return;
			}

			$mess = '';

			// Delete product/products from exclude list
			if ( 'delete' === $action && isset( $_GET['id'] ) ) {

				$products_ids = isset( $_GET['id'] ) ? $_GET['id'] : array();
				if ( ! is_array( $products_ids ) ) {
					$products_ids = explode( ',', $products_ids );
				}
				// delete post meta
				foreach( $products_ids as $product_id ) {
				    $product = wc_get_product( $product_id );
					yit_delete_prop( $product, $this->_meta_exclude );
				}
				// add message
				if( empty( $products_ids ) ) {
					$mess = 1;
				} else {
					$mess = 2;
				}

			}
			// Add product to exclude list
			elseif ( 'insert' === $action && ! empty( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'yith_wcmas_add_exclusions' ) ) {

				$products_id = $_POST['ywcmas_products_for_exclude'];
                ! is_array( $products_id ) && $products_id = explode( ',', $products_id );
				// update post meta for each product
				foreach ( $products_id as $product_id ) {
				    $product = wc_get_product( $product_id );
					yit_save_prop( $product, $this->_meta_exclude, 1 );
				}

				// add message
				if( empty( $product_id ) ) {
					$mess = 4;
				} else {
					$mess = 3;
				}
			}

			$list_query_args = array(
				'page'          => $page,
				'tab'           => $tab,
			);
			// Add message
			if( $mess ) {
				$list_query_args['wcmas_mess'] = $mess;
			}

			$list_url = add_query_arg( $list_query_args, admin_url( 'admin.php' ) );

			wp_redirect( $list_url );
			exit;
		}
	}
}

/**
 * Unique access to instance of YITH_WCMAS_Excluded_Products_Table_Options class
 *
 * @return \YITH_WCMAS_Excluded_Products_Table_Options
 * @since 1.0.0
 */
function YITH_WCMAS_Excluded_Products_Table_Options(){
	return YITH_WCMAS_Excluded_Products_Table_Options::get_instance();
}