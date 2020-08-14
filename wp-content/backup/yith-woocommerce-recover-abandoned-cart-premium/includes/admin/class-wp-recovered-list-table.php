<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAC_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Email Template List Table
 *
 * @class   YITH_YWRAC_Recovered_List_Table
 * @package YITH WooCommerce Recover Abandoned Cart
 * @since   1.0.0
 * @author YITH
 */

class YITH_YWRAC_Recovered_List_Table extends WP_List_Table {

	private $post_type;

	public function __construct( $args = array() ) {
		parent::__construct( array() );
		$this->post_type = 'shop_order';
	}

	function get_columns() {
		$columns = array(
			'post_title' => __( 'Order', 'yith-woocommerce-recover-abandoned-cart' ),
			'purchased'  => __( 'Purchased', 'yith-woocommerce-recover-abandoned-cart' ),
			'type'       => __( 'Type', 'yith-woocommerce-recover-abandoned-cart' ),
			'coupons'    => __( 'Coupons', 'yith-woocommerce-recover-abandoned-cart' ),
			'date'       => __( 'Date', 'yith-woocommerce-recover-abandoned-cart' ),
			'total'      => __( 'Total', 'yith-woocommerce-recover-abandoned-cart' ),
		);
		return $columns;
	}

	function prepare_items() {
		global $wpdb, $_wp_column_headers;

		$screen = get_current_screen();

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$order_string = 'ORDER BY ywrac_p.post_date DESC ';

		$query = $wpdb->prepare(
			"SELECT ywrac_p.* FROM $wpdb->posts as ywrac_p INNER JOIN " . $wpdb->prefix . "postmeta as ywrac_pm ON ( ywrac_p.ID = ywrac_pm.post_id )
        AND ywrac_pm.meta_key = %s
        GROUP BY ywrac_p.ID $order_string",
			'_ywrac_recovered'
		);

		$totalitems = $wpdb->query( $query );

		$perpage = 10;
		// Which page is this?
		$paged = ! empty( $_GET['paged'] ) ? $_GET['paged'] : '';
		// Page Number
		if ( empty( $paged ) || ! is_numeric( $paged ) || $paged <= 0 ) {
			$paged = 1;
		}
		// How many pages do we have in total?
		$totalpages = ceil( $totalitems / $perpage );
		// adjust the query to take pagination into account
		if ( ! empty( $paged ) && ! empty( $perpage ) ) {
			$offset = ( $paged - 1 ) * $perpage;
			$query .= ' LIMIT ' . (int) $offset . ',' . (int) $perpage;
		}

		/* -- Register the pagination -- */
		$this->set_pagination_args(
			array(
				'total_items' => $totalitems,
				'total_pages' => $totalpages,
				'per_page'    => $perpage,
			)
		);
		// The pagination links are automatically built according to those parameters

		$_wp_column_headers[ $screen->id ] = $columns;
		$this->items                       = $wpdb->get_results( $query );

	}

	function column_default( $item, $column_name ) {
		$the_order = wc_get_order( $item->ID );
		switch ( $column_name ) {
			case 'post_title':
				$customer_tip = array();

				if ( $address = $the_order->get_formatted_billing_address() ) {
					$customer_tip[] = __( 'Billing:', 'woocommerce' ) . ' ' . $address . '<br/><br/>';
				}

				if ( yit_get_prop( $the_order, 'billing_phone' ) ) {
					$customer_tip[] = __( 'Tel:', 'woocommerce' ) . ' ' . yit_get_prop( $the_order, 'billing_phone' );
				}

				echo '<div class="tips" data-tip="' . wc_sanitize_tooltip( implode( '<br/>', $customer_tip ) ) . '">';

				if ( yit_get_prop( $the_order, 'user_id' ) ) {
					$user_info = get_userdata( yit_get_prop( $the_order, 'user_id' ) );
				}

				if ( ! empty( $user_info ) ) {

					$username = '<a href="user-edit.php?user_id=' . absint( $user_info->ID ) . '">';

					if ( $user_info->first_name || $user_info->last_name ) {
						$username .= esc_html( ucfirst( $user_info->first_name ) . ' ' . ucfirst( $user_info->last_name ) );
					} else {
						$username .= esc_html( ucfirst( $user_info->display_name ) );
					}

					$username .= '</a>';

				} else {
					if ( yit_get_prop( $the_order, 'billing_first_name' ) || yit_get_prop( $the_order, 'billing_last_name' ) ) {
						$username = trim( yit_get_prop( $the_order, 'billing_first_name' ) . ' ' . yit_get_prop( $the_order, 'billing_last_name' ) );
					} else {
						$username = __( 'Guest', 'woocommerce' );
					}
				}

				printf( _x( '%1$s by %2$s', 'Order number by X', 'woocommerce' ), '<a href="' . admin_url( 'post.php?post=' . absint( $item->ID ) . '&action=edit' ) . '"><strong>#' . esc_attr( $the_order->get_order_number() ) . '</strong></a>', $username );

				if ( yit_get_prop( $the_order, 'billing_email' ) ) {
					echo '<small class="meta email"><a href="' . esc_url( 'mailto:' . yit_get_prop( $the_order, 'billing_email' ) ) . '">' . esc_html( yit_get_prop( $the_order, 'billing_email' ) ) . '</a></small>';
				}

				echo '</div>';
				break;
			case 'purchased':
				echo apply_filters( 'woocommerce_admin_order_item_count', sprintf( _n( '%d item', '%d items', $the_order->get_item_count(), 'woocommerce' ), $the_order->get_item_count() ), $the_order );

				break;
			case 'type':
				$opt = get_post_meta( $item->ID, '_ywrac_email_id_processed', true );
				echo empty( $opt ) ? __( 'abandoned cart', 'yith-woocommerce-recover-abandoned-cart' ) : __( 'pending order', 'yith-woocommerce-recover-abandoned-cart' );

				break;
			case 'coupons':
				if ( version_compare( WC()->version , '3.7', '<' )  ) {
					$coupons = $the_order->get_used_coupons();
				} else {
					$coupons = $the_order->get_coupon_codes();
				}

				$coupon  = '';
				if ( ! empty( $coupons ) ) {
					foreach ( $coupons as $coup ) {
						$coupon .= $coup . '<br>';
					}
				}
				echo $coupon;
				break;
			case 'date':
				if ( '0000-00-00 00:00:00' == $item->post_date ) {
					$t_time = $h_time = __( 'Unpublished', 'woocommerce' );
				} else {
					$t_time = get_the_time( __( 'Y/m/d g:i:s A', 'woocommerce' ), $item );
					$h_time = get_the_time( __( 'Y/m/d', 'woocommerce' ), $item );
				}

				$date = '<abbr title="' . esc_attr( $t_time ) . '">' . esc_html( apply_filters( 'post_date_column_time', $h_time, $item ) ) . '</abbr>';
				return $date;
				break;
			case 'total':
				if ( $the_order->get_total_refunded() > 0 ) {
					$currency = version_compare( WC()->version, '3.0.0', '>=' ) ? $the_order->get_currency() : $the_order->get_order_currency();
					echo '<del>' . strip_tags( $the_order->get_formatted_order_total() ) . '</del> <ins>' . wc_price( $the_order->get_total() - $the_order->get_total_refunded(), array( 'currency' => $currency ) ) . '</ins>';
				} else {
					echo esc_html( strip_tags( $the_order->get_formatted_order_total() ) );
				}

				if ( yit_get_prop( $the_order, 'payment_method_title' ) ) {
					echo '<small class="meta">' . __( 'Via', 'woocommerce' ) . ' ' . esc_html( yit_get_prop( $the_order, 'payment_method_title' ) ) . '</small>';
				}
				break;
			default:
				return ''; // Show the whole array for troubleshooting purposes
		}
	}



	function get_sortable_columns() {
		$sortable_columns = array(
			'post_title' => array( 'post_title', false ),
			'status'     => array( 'status', false ),
		);
		return $sortable_columns;
	}



}
