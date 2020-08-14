<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * My Quotes
 *
 * Shows recent orders on the account page
 *
 * @package YITH Woocommerce Request A Quote
 * @since   1.0.0
 * @version 2.2.7
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! apply_filters( 'yith_ywraq_before_print_my_account_my_quotes', true ) ) {
	return;
}

$text_align = is_rtl() ? 'right' : 'left';

$customer_quotes = wc_get_orders(
	apply_filters( 'ywraq_my_account_my_quotes_query',
		array(
			'limit'       => 15,
			'ywraq_raq' => 'yes',
			'customer'    => get_userdata( get_current_user_id() )->user_email,
			'status'      => array_merge( YITH_YWRAQ_Order_Request()->raq_order_status, array_keys( wc_get_order_statuses() ) ),
		)
	)
);


?>

<h2 class="ywraq_my_account_quotes_title"><?php echo wp_kses_post( apply_filters( 'ywraq_my_account_my_quotes_title', __( 'Recent Quotes', 'yith-woocommerce-request-a-quote' ) ) ); ?></h2>

<?php if ( $customer_quotes ) : ?>

	<table class="shop_table shop_table_responsive my_account_quotes my_account_orders">

		<thead>
		<tr>
			<th class="order-status">
				<span class="nobr"><?php esc_html_e( 'Status', 'yith-woocommerce-request-a-quote' ); ?></span></th>
			<th class="order-number">
				<span class="nobr"><?php esc_html_e( 'Quote', 'yith-woocommerce-request-a-quote' ); ?></span></th>
			<th class="order-date"><span class="nobr"><?php esc_html_e( 'Date', 'yith-woocommerce-request-a-quote' ); ?></span>
			</th>
			<th class="order-actions">&nbsp;</th>
		</tr>
		</thead>

		<tbody>
		<?php

		foreach ( $customer_quotes as $customer_order ) {

			$order_id   = $customer_order->get_id();
			$order      = $customer_order;
			$item_count = $order->get_item_count();

			if ( 0 === $item_count ) {
				continue;
			}


			$order_date = $customer_order->get_date_created();
			$order_lang = yit_get_prop( $order, 'wpml_language', true );

			?>
			<tr class="quotes">
			<td class="quotes-status" data-title="<?php esc_attr_e( 'Status', 'yith-woocommerce-request-a-quote' ); ?>" style="text-align:<?php echo $text_align; ?>; white-space:nowrap;">
				<?php ywraq_get_order_status_tag( $order->get_status() ); ?>
			</td>
			<td class="quotes-number" data-title="<?php esc_attr_e( 'Order Number', 'yith-woocommerce-request-a-quote' ); ?>">
				<a href="<?php echo esc_url( YITH_YWRAQ_Order_Request()->get_view_order_url( $order_id ) ); ?>">
					#<?php echo esc_html( $order->get_order_number() ); ?>
				</a>
			</td>
			<td class="quotes-date" data-title="<?php esc_attr_e( 'Date', 'yith-woocommerce-request-a-quote' ); ?>">
				<time datetime="<?php echo esc_attr( gmdate( 'Y-m-d', strtotime( $order_date ) ) ); ?>" title="<?php echo esc_attr( strtotime( $order_date ) ); ?>"><?php echo wp_kses_post( date_i18n( get_option( 'date_format' ), strtotime( $order_date ) ) ); ?></time>
			</td>

			<td class="quotes-actions" data-order_id="<?php echo esc_attr( $order_id ); ?>">
				<?php
				$actions    = array();
				$user_email = yit_get_prop( $order, 'ywraq_customer_email', true );

				if ( YITH_Request_Quote()->enabled_checkout() && in_array( $order->get_status(), apply_filters( 'ywraq_valid_order_statuses_for_payment', array( 'pending', 'ywraq-accepted' ), $order ) ) ) {
					$actions['accept'] = array(
						'url'  => ywraq_get_accepted_quote_page( $order ),
						'name' => __( 'Checkout', 'yith-woocommerce-request-a-quote' ),
					);
				}

				if ( get_option( 'ywraq_show_accept_link' ) !== 'no' && in_array( $order->get_status(), apply_filters( 'ywraq_valid_order_statuses_for_accept', array( 'ywraq-pending' ), $order ) ) ) {
					$actions['accept'] = array(
						'url'  => ywraq_get_accepted_quote_page( $order ),
						'name' => ywraq_get_label( 'accept' ),
					);
				}


				if ( get_option( 'ywraq_show_reject_link' ) !== 'no' && in_array( $order->get_status(), apply_filters( 'ywraq_valid_order_statuses_for_reject', array( 'ywraq-pending' ), $order ) ) ) {
					$actions['reject'] = array(
						'url'  => ywraq_get_rejected_quote_page( $order ),
						'name' => ywraq_get_label( 'reject' ),
					);
				}


				$actions['view'] = array(
					'url'  => YITH_YWRAQ_Order_Request()->get_view_order_url( $order_id ),
					'name' => __( 'View', 'yith-woocommerce-request-a-quote' ),
				);

				if ( get_option( 'ywraq_enable_order_again' ) === 'yes' && $order->has_status( apply_filters( 'ywraq_valid_order_statuses_for_order_again', array( 'completed' ) ) ) ) {
					$button_label          = get_option( 'ywraq_order_again_button_label' ) !== '' ? get_option( 'ywraq_order_again_button_label' ) : __( 'Request Quote Again', 'yith-woocommerce-request-a-quote' );
					$reorder_url           = wp_nonce_url( add_query_arg( 'raq_again', $order->get_id(), YITH_Request_Quote_Premium()->get_raq_url( '' ) ), 'ywraq-order_again' );
					$actions['orderagain'] = array(
						'url'  => $reorder_url,
						'name' => apply_filters( 'ywraq_quote_again_button_label', $button_label ),
					);
				}
				$actions = apply_filters( 'ywraq_my_account_my_quotes_actions', $actions, $order );

				if ( $actions ) {
					foreach ( $actions as $key => $action ) {
						echo '<a href="' . esc_url( $action['url'] ) . '" class="button ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>';
					}
				}
				?>

			</td>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>


<?php else : ?>
	<p class="ywraq-no-quote-in-list"><?php esc_html_e( 'No quote request available.', 'yith-woocommerce-request-a-quote' ); ?></p>
<?php endif; ?>
