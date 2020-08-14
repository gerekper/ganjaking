<?php
if ( ! defined( 'YITH_WCMAS_VERSION' ) ) {
	exit; // Exit if accessed directly
}

if( ! class_exists( 'YITH_WCMAS_Excluded_Categories_Table_Options' ) ) {
	/**
	 * Helper class for generating Excluded Categories table
	 *
	 * @class   YITH_WCMAS_Excluded_Products_Table_Options
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 * @since 1.0.0
	 */
	class YITH_WCMAS_Excluded_Categories_Table_Options {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCMAS_Excluded_Categories_Table_Options
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
		 * @return \YITH_WCMAS_Excluded_Categories_Table_Options
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

			$table->options = apply_filters( 'ywcmas_excluded_categories_table_options',
				array(
				'select_table'     => $wpdb->prefix . 'termmeta a',
				'select_columns'   => array(
					'a.term_id'
				),
				'select_where'     => 'a.meta_key = "' . $this->_meta_exclude . '" AND a.meta_value = "1"',
				'select_group'     => 'a.term_id',
				'select_order'     => 'a.term_id',
				'select_limit'     => 10,
				'count_table'      => '( SELECT COUNT(*) FROM ' . $wpdb->prefix . 'termmeta a WHERE a.meta_key = "' . $this->_meta_exclude . '" AND a.meta_value="1" GROUP BY a.term_id ) AS count_table',
				'key_column'       => 'term_id',
				'view_columns'     => array(
					'cb'            => '<input type="checkbox" />',
					'category'      => esc_html__( 'Category', 'yith-multiple-shipping-addresses-for-woocommerce' ),
					'product_count' => esc_html__( 'Products in this category', 'yith-multiple-shipping-addresses-for-woocommerce' ),
					'actions'       => esc_html__( 'Action', 'yith-multiple-shipping-addresses-for-woocommerce' )
				),
				'get_product'      => 'yes',
				'hidden_columns'   => array(),
				'sortable_columns' => array(
					'category' => array( 'post_title', true )
				),
				'custom_columns'   => array(
					'column_category' => function( $item, $me, $category ) {
						return $category->name;
					},
					'column_product_count' => function( $item, $me, $category ) {
						return $category->count;
					},
					'column_actions' => function( $item, $me, $product ) {

						$delete_query_args = array(
							'page'   => $_GET['page'],
							'tab'    => $_GET['tab'],
							'action' => 'delete',
							'id'     => $item['term_id']
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

			if( $page != 'yith_wcmas_panel' || $tab != 'cat-exclusion' || $action == '' ) {
				return;
			}

			$mess = '';

			// Delete product/products from exclude list
			if ( 'delete' === $action && isset( $_GET['id'] ) ) {
				$categories_ids = isset( $_GET['id'] ) ? $_GET['id'] : array();
				if ( ! is_array( $categories_ids ) ) {
					$categories_ids = explode( ',', $categories_ids );
				}
				// delete post meta
				foreach( $categories_ids as $category_id ) {
					update_term_meta( $category_id, $this->_meta_exclude, 0 );
				}
				// add message
				if( empty( $categories_ids ) ) {
					$mess = 1;
				} else {
					$mess = 2;
				}
			}
			// Add product to exclude list
			elseif ( 'insert' === $action && ! empty( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'yith_wcmas_add_exclusions' ) ) {
				$categories_ids = isset( $_POST['ywcmas_categories_for_exclude'] ) ? $_POST['ywcmas_categories_for_exclude'] : '';
				if ( ! is_array( $categories_ids ) ) {
					$categories_ids = explode( ',', $categories_ids );
				}
				// update term meta for each category
				foreach ( $categories_ids as $category_id ) {
					update_term_meta( $category_id, $this->_meta_exclude, 1 );
				}
				// add message
				if( empty( $categories_id ) ) {
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
 * Unique access to instance of YITH_WCMAS_Excluded_Categories_Table_Options class
 *
 * @return \YITH_WCMAS_Excluded_Categories_Table_Options
 * @since 1.0.0
 */
function YITH_WCMAS_Excluded_Categories_Table_Options(){
	return YITH_WCMAS_Excluded_Categories_Table_Options::get_instance();
}