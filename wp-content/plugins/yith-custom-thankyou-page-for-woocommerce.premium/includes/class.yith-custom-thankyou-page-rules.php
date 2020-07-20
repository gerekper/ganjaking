<?php
/**
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package     YITH Custom ThankYou Page for Woocommerce
 */

if ( ! defined( 'YITH_CTPW_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

if ( ! class_exists( 'YITH_Custom_Thankyou_Page_Rules' ) ) {
	/**
	 * Admin Premium Class
	 *
	 * @class       YITH_Custom_Thankyou_Page_Rules
	 * @package     YITH Custom ThankYou Page for Woocommerce
	 * @since       1.2.5
	 * @author      YITH
	 */
	class YITH_Custom_Thankyou_Page_Rules {
		/**
		 * Main Class Instance
		 *
		 * @var YITH_Custom_Thankyou_Page
		 * @since 1.2.5
		 * @access protected
		 */
		protected static $instance = null;
		/**
		 * Main plugin Instance
		 *
		 * @return YITH_Custom_Thankyou_Page_Rules Admin Premium
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 * @since 1.2.5
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;

		}
		/**
		 * Adds Rules Table
		 *
		 * @return void
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 * @since 1.2.5
		 */
		public function __construct() {
			add_filter( 'yith_ctpw_admin_tabs', array( $this, 'rules_options' ) );

			if ( isset( $_GET['tab'] ) && 'rules' === $_GET['tab'] ) {//phpcs:ignore WordPress.Security.NonceVerification.Recommended
				add_action( 'yctpw_rules_table', array( $this, 'rules_table' ) );
			}

			add_filter( 'woocommerce_product_data_store_cpt_get_products_query', array( $this, 'yctpw_handle_custom_wc_query_var' ), 10, 2 );
		}

		/**
		 * Add Saved Custom Thank You Pages Tab
		 *
		 * @param array $panels .
		 *
		 * @return array
		 * @since 1.2.5
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 */
		public function rules_options( $panels ) {
			$saved_list = array( 'rules' => esc_html__( 'Rules', 'yith-custom-thankyou-page-for-woocommerce' ) );
			$panels     = array_merge( $panels, $saved_list );

			return $panels;
		}

		/**
		 * Prepare and Disaply the Rules Tab
		 *
		 * @return void
		 * @since 1.2.5
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 */
		public function rules_table() {

			$posted = $_POST; //phpcs:ignore WordPress.Security.NonceVerification.Missing
			/* before to show the table, check if we are adding a new rule */
			if ( isset( $posted['action'] ) && ( 'yctpw_add_rule' === $posted['action'] || 'yctpw_update_rule' === $posted['action'] ) && wp_verify_nonce( $posted['yctpw_rule_nonce'], 'yctpw_rule_nonce' ) ) {
				$item_type = $posted['item_type'];
				$page_url  = $posted['yith_ctpw_general_page_or_url'];

				switch ( $item_type ) {
					case 'product':
					case 'product_simple':
					case 'product_variable':
						/* this check is needed for adding variation thank you page from add new form */
						if ( 'product_variation' === get_post_type( $posted['product_id'] ) ) {
							$this->add_rule( 'product_variation', $posted['product_id'], $posted['yith_ctpw_general_page_or_url'], $posted['yith_thankyou_page'], $posted['yith_thankyou_url'] );
						} else {
							$this->add_rule( 'product', $posted['product_id'], $posted['yith_ctpw_general_page_or_url'], $posted['yith_thankyou_page'], $posted['yith_thankyou_url'] );
						}
						break;
					case 'product_variation':
						$this->add_rule( 'product_variation', $posted['product_id'], $posted['yith_ctpw_general_page_or_url'], $posted['yith_thankyou_page'], $posted['yith_thankyou_url'] );
						break;
					case 'product_category':
						$this->add_rule( 'product_category', $posted['category_id'], $posted['yith_ctpw_general_page_or_url'], $posted['yith_thankyou_page'], $posted['yith_thankyou_url'] );
						break;
					case 'payment_method':
						$this->add_rule( 'payment_method', $posted['payment_method'], $posted['yith_ctpw_general_page_or_url'], $posted['yith_thankyou_page'], $posted['yith_thankyou_url'] );
						break;
				}
			}

			/* check if we are editing rule */


			/* check if we are removing rule */
			$get = $_GET;
			if ( isset( $get['action'] ) && 'delete' === $get['action'] && isset( $get['remove_edit_nonce'] ) && wp_verify_nonce( $get['remove_edit_nonce'], 'yctpw_remove_edit_rule_nonce' ) ) {
				$this->remove_rule( $get['rule_type'], $get['id'] );
			}

			/* load table class and display it */
			require_once YITH_CTPW_PATH . 'includes/admin/class.yith-custom-thankyou-page-table.php';
			$table = new YITH_YCTPW_Saved_Ctpw_Table();

			/* get items and show table */
			$table->prepare_items();
			$table->display();

		}

		/**
		 * Manage WooCommerce query in order to use a custom meta query
		 *
		 * @param array $query wc meta query.
		 * @param array $query_vars wc query vars.
		 * @return array wc query.
		 * @since 1.2.5
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 */
		public function yctpw_handle_custom_wc_query_var( $query, $query_vars ) {
			if ( ! empty( $query_vars['yith_ctpw_product_thankyou_page_url'] ) ) {
				$query['meta_query'][] = array(
					'key'     => 'yith_ctpw_product_thankyou_page_url',
					'value'   => esc_attr( $query_vars['yith_ctpw_product_thankyou_page_url'] ),
					'compare' => 'LIKE',
				);
			}

			return $query;
		}
		/**
		 * Add Rule
		 *
		 * @param string $item_type .
		 * @param int    $item_id .
		 * @param string $rule_type .
		 * @param int    $page .
		 * @param string $url .
		 *
		 * @return void
		 * @since 1.2.5
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 */
		public function add_rule( $item_type, $item_id, $rule_type, $page, $url ) {
			switch ( $item_type ) {
				case 'product':
					if ( '' !== trim( $item_id ) ) {
						if ( 'ctpw_page' === $rule_type ) {
							update_post_meta( $item_id, 'yith_product_thankyou_page', $page );
							update_post_meta( $item_id, 'yith_ctpw_product_thankyou_page_url', 'ctpw_page' );
						} else {
							update_post_meta( $item_id, 'yith_ctpw_product_thankyou_url', $url );
							update_post_meta( $item_id, 'yith_ctpw_product_thankyou_page_url', 'ctpw_url' );
						}
					}
					break;
				case 'product_variation':
					if ( '' !== trim( $item_id ) ) {
						if ( 'ctpw_page' === $rule_type ) {
							update_post_meta( $item_id, 'yith_product_thankyou_page_variation', $page );
							update_post_meta( $item_id, 'yith_ctpw_product_thankyou_page_url', 'ctpw_page' );
						} else {
							update_post_meta( $item_id, 'yith_ctpw_product_thankyou_url', $url );
							update_post_meta( $item_id, 'yith_ctpw_product_thankyou_page_url', 'ctpw_url' );
						}
					}
					break;
				case 'product_category':
					if ( '' !== trim( $item_id ) ) {
						if ( 'ctpw_page' === $rule_type ) {
							update_term_meta( $item_id, 'yith_ctpw_product_cat_thankyou_page', $page );
							update_term_meta( $item_id, 'yith_ctpw_or_url_product_cat_thankyou_page', 'ctpw_page' );
						} else {
							update_term_meta( $item_id, 'yith_ctpw_url_product_cat_thankyou_page', $url );
							update_term_meta( $item_id, 'yith_ctpw_or_url_product_cat_thankyou_page', 'ctpw_url' );
						}
					}
					break;
				case 'payment_method':
					update_option( 'yith_ctpw_general_page_or_url_' . $item_id, $rule_type );
					update_option( 'yith_ctpw_page_for_' . $item_id, $page );
					update_option( 'yith_ctpw_url_for_' . $item_id, $url );
					break;
			}
		}

		/**
		 * Remove a Rule
		 *
		 * @param string $item_type .
		 * @param int    $item_id .
		 *
		 * @return void
		 * @since 1.2.5
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 */
		public function remove_rule( $item_type, $item_id ) {
			switch ( $item_type ) {
				case 'product_simple':
				case 'product_variation':
				case 'product_variable':
				case 'product':
					delete_post_meta( $item_id, 'yith_ctpw_product_thankyou_page_url' );
					delete_post_meta( $item_id, 'yith_product_thankyou_page' );
					delete_post_meta( $item_id, 'yith_ctpw_product_thankyou_url' );
					delete_post_meta( $item_id, 'yith_product_thankyou_page_variation' );
					break;
				case 'product_category':
					delete_term_meta( $item_id, 'yith_ctpw_or_url_product_cat_thankyou_page' );
					delete_term_meta( $item_id, 'yith_ctpw_product_cat_thankyou_page' );
					delete_term_meta( $item_id, 'yith_ctpw_url_product_cat_thankyou_page' );
					break;
				case 'payment_method':
					delete_option( 'yith_ctpw_general_page_or_url_' . $item_id );
					delete_option( 'yith_ctpw_page_for_' . $item_id );
					delete_option( 'yith_ctpw_url_for_' . $item_id );
					break;
			}

		}
		/**
		 * Get an Array of all the Rules that have been set
		 *
		 * @return array
		 * @since 1.2.5
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 */
		public function get_all_rules() {
			$yctpw_items = array();
			$items       = '';

			// get products.
			$products = wc_get_products(
				array(
					'yith_ctpw_product_thankyou_page_url' => 'ctpw_',
				)
			);

			$variations = wc_get_products(
				array(
					'yith_ctpw_product_thankyou_page_url' => 'ctpw_',
					'type'                                => 'variation',
				)
			);

			$items = array_merge( $products, $variations );

			if ( ! empty( $items ) ) {

				foreach ( $items as $item ) {
					if ( $item->get_type() === 'variation' ) {
						$ctpw_url  = get_post_meta( $item->get_id(), 'yith_ctpw_product_thankyou_url', true );
						$ctpw_page = get_post_meta( $item->get_id(), 'yith_product_thankyou_page_variation', true );
					} else {
						$ctpw_url  = get_post_meta( $item->get_id(), 'yith_ctpw_product_thankyou_url', true );
						$ctpw_page = get_post_meta( $item->get_id(), 'yith_product_thankyou_page', true );
					}

					if ( 0 !== intval( $ctpw_page ) && '' !== trim( $ctpw_page ) && '0' !== $ctpw_page || '' !== trim( $ctpw_url ) ) {
						$new_item = array(
							'ID'        => $item->get_id(),
							'name'      => $item->get_name(),
							'object'    => 'product_' . $item->get_type(),
							'ctpw_type' => ( 'ctpw_page' === get_post_meta( $item->get_id(), 'yith_ctpw_product_thankyou_page_url', true ) ) ? 'ctpw_page' : 'ctpw_url',
							'ctpw'      => ( 'ctpw_page' === get_post_meta( $item->get_id(), 'yith_ctpw_product_thankyou_page_url', true ) ) ? $ctpw_page : $ctpw_url,
						);

						$yctpw_items[] = $new_item;
					}
				}
			}

			// get product categories.
			$args = array(
				'taxonomy'   => 'product_cat',
				'meta_query' => array(
					array(
						'key'     => 'yith_ctpw_or_url_product_cat_thankyou_page',
						'value'   => '',
						'compare' => '!=',
					),
				),
			);

			$terms = get_terms( $args );

			if ( ! empty( $terms ) ) {

				foreach ( $terms as $t ) {
					$ctpw_url  = get_term_meta( $t->term_id, 'yith_ctpw_url_product_cat_thankyou_page', true );
					$ctpw_page = get_term_meta( $t->term_id, 'yith_ctpw_product_cat_thankyou_page', true );

					if ( 0 !== intval( $ctpw_page ) && '' !== trim( $ctpw_page ) && '0' !== $ctpw_page || '' !== trim( $ctpw_url ) ) {
						$new_item = array(
							'ID'        => $t->term_id,
							'name'      => $t->name,
							'object'    => 'product_category',
							'ctpw_type' => ( 'ctpw_page' === get_term_meta( $t->term_id, 'yith_ctpw_or_url_product_cat_thankyou_page', true ) ) ? 'ctpw_page' : 'ctpw_url',
							'ctpw'      => ( 'ctpw_page' === get_term_meta( $t->term_id, 'yith_ctpw_or_url_product_cat_thankyou_page', true ) ) ? $ctpw_page : $ctpw_url,
						);

						$yctpw_items[] = $new_item;
					}
				}
			}

			// getting payments methods .
			$installed_payment_methods = WC()->payment_gateways->payment_gateways();
			foreach ( $installed_payment_methods as $paymentg ) {
				$ctpw_url  = get_option( 'yith_ctpw_url_for_' . $paymentg->id, '' );
				$ctpw_page = get_option( 'yith_ctpw_page_for_' . $paymentg->id, '' );
				if ( '' !== get_option( 'yith_ctpw_page_for_' . $paymentg->id, '' ) || '' !== get_option( 'yith_ctpw_url_for_' . $paymentg->id, '' ) ) {
					$new_item = array(
						'ID'        => $paymentg->id,
						'name'      => $paymentg->title,
						'object'    => 'payment_method',
						'ctpw_type' => ( 'ctpw_page' === get_option( 'yith_ctpw_general_page_or_url_' . $paymentg->id ) ) ? 'ctpw_page' : 'ctpw_url',
						'ctpw'      => ( 'ctpw_page' === get_option( 'yith_ctpw_general_page_or_url_' . $paymentg->id ) ) ? $ctpw_page : $ctpw_url,
					);

					$yctpw_items[] = $new_item;
				}
			}

			return $yctpw_items;
		}

	} // end class .
}


/**
 * Unique access to instance of YITH_YWRAQ_Admin class
 *
 * @return \YITH_Custom_Thankyou_Page_Rules
 */
function YITH_CTPW_RULES() {
	return YITH_Custom_Thankyou_Page_Rules::get_instance();
}