<?php
if ( ! defined( 'YITH_WOCC' ) ) {
	exit; // Exit if accessed directly
}

if( ! class_exists( 'YITH_WOCC_Exclusions_Table' ) ) {
	/**
	 * Exclusion table
	 *
	 * @class   YITH_WOCC_Exclusions_Table
	 * @package YITH Woocommerce One-Click Checkout
	 * @since   1.0.0
	 * @author  Yithemes
	 *
	 */
	class YITH_WOCC_Exclusions_Table {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WOCC_Exclusions_Table
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Meta exclude
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_meta_exclude = '_yith_wocc_exclude_list';

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WOCC_Exclusions_Table
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

			$table = new YITH_WOCC_Custom_Table( array(
				'singular' => __( 'product', 'yith-woocommerce-one-click-checkout' ),
				'plural'   => __( 'products', 'yith-woocommerce-one-click-checkout' )
			) );

			$table->options = array(
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
					'cb'      => '<input type="checkbox" />',
					'product'   => __( 'Product', 'yith-woocommerce-one-click-checkout' ),
					'stock'     => __( 'Stock Quantity', 'yith-woocommerce-one-click-checkout' ),
					'price'     => __( 'Price', 'yith-woocommerce-one-click-checkout'),
					'categories' => __( 'Categories', 'yith-woocommerce-one-click-checkout' ),
					'actions'   => __( 'Action', 'yith-woocommerce-one-click-checkout' )
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

						return sprintf( '<strong><a class="tips" target="_blank" href="%s" data-tip="%s">%s</a></strong>', esc_url( $product_url ), __( 'Edit product', 'yith-woocommerce-one-click-checkout' ), $thumb . $product->get_title() );
					},
					'column_stock'   => function ( $item, $me, $product ) {
                        /**
                         * @type $product \WC_Product
                         */
						$status = $product->get_availability();
						$class = ( isset( $status['class'] ) && $status['class'] != '' ) ? $status['class'] : 'in-stock';
						$availability = ( isset( $status['availability'] ) && $status['availability'] != '' ) ? $status['availability'] : __( 'In stock', 'woocommerce' );

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
						$last = is_array($cat  ) ? end( $cat ) : $cat;

						if( empty( $cat ) ) {
							echo ' - ';
						}
						else {
							foreach( $cat as $key => $value ) {
								echo esc_html( $value->name );
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

						return '<a href="' . esc_url( $delete_url ) . '" class="button">' . __( 'Remove', 'yith-woocommerce-one-click-checkout' ) . '</a>';

					}
				),
				'bulk_actions'     => array(
					'actions'   => array(
						'delete' => __( 'Remove from list', 'yith-woocommerce-one-click-checkout' )
					)
				),
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
			$action  = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';


			if( $page != 'yith_wocc_panel' || $tab != 'exclusions' || $action == '' ) {
				return;
			}

			$mess = '';

			// Delete product/products from exclude list
			if ( 'delete' === $action && isset( $_GET['id'] ) ) {

				$ids = isset( $_GET['id'] ) ? $_GET['id'] : array();
				if ( ! is_array( $ids ) ) {
					$ids = explode( ',', $ids );
				}
				// delete post meta
				foreach( $ids as $id ) {
				    $product = wc_get_product( $id );
					yit_delete_prop( $product, $this->_meta_exclude );
				}
				// add message
				if( empty( $ids ) ) {
					$mess = 1;
				}
				else {
					$mess = 2;
				}

			}
			// Add product to exclude list
			elseif ( 'insert' === $action && ! empty( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'yith_wocc_add_exclusions' ) && ! empty( $_POST['products'] ) ) {

				$products_id = $_POST['products'];
                ! is_array( $products_id ) && $products_id = explode( ',', $products_id );
				// update post meta for each product
				foreach ( $products_id as $product_id ) {
				    $product = wc_get_product( $product_id );
				    if( ! $product ) {
				        continue;
                    }
					yit_save_prop( $product, $this->_meta_exclude, 1 );
				}

				// add message
				if( empty( $product_id ) ) {
					$mess = 4;
				}
				else {
					$mess = 3;
				}
			}

			$list_query_args = array(
				'page'          => $page,
				'tab'           => $tab,
			);
			// Add message
			if( isset( $mess ) ) {
				$list_query_args['wocc_mess'] = $mess;
			}

			$list_url = add_query_arg( $list_query_args, admin_url( 'admin.php' ) );

			wp_redirect( $list_url );
			exit;
		}
	}
}
/**
 * Unique access to instance of YITH_WCWTL_Exclusions_Table class
 *
 * @return \YITH_WOCC_Exclusions_Table
 * @since 1.0.0
 */
function YITH_WOCC_Exclusions_Table(){
	return YITH_WOCC_Exclusions_Table::get_instance();
}