<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_WCDP_YITH_PDF_Invoice_Compatibility' ) ) {

	class YITH_WCDP_YITH_PDF_Invoice_Compatibility {

		protected static $_instance;


		public function __construct() {

			add_filter( 'yith_ywpi_invoice_subtotal', array( $this, 'set_order_subtotal_in_invoice' ), 10, 4 );
			add_filter( 'yith_ywpi_line_discount', array( $this, 'set_order_total_discount_in_invoice' ), 10, 2 );
		}

		/**
		 * @return YITH_WCDP_YITH_PDF_Invoice_Compatibility unique access
		 * @since  1.0.0
		 * @author YITHEMES
		 */
		public static function get_instance() {

			if ( is_null( self::$_instance ) ) {

				self::$_instance = new self();
			}

			return self::$_instance;
		}


		/**
		 * Set right order total
		 *
		 * @param float    $order_total
		 * @param WC_Order $order
		 *
		 * @return float
		 * @since  1.0.0
		 * @author YITHEMES
		 */
		public function set_order_subtotal_in_invoice( $order_subtotal, $order, $discount, $fee ) {

			$order_id = yit_get_order_id( $order );

			$new_subtotal = 0;

			$items = $order->get_items();

			foreach ( $items as $item ) {

				$new_subtotal += $item['line_subtotal'];

			}

			return $new_subtotal > 0 ? $new_subtotal : $order_subtotal;
		}

		public function set_order_total_discount_in_invoice( $discount, $order_item ) {

			if ( ( isset( $order_item['deposit'] ) && 1 == $order_item['deposit'] ) || ( ! empty( $order_item['deposit_id'] ) ) ) {
				$discount = 0;
			}

			return $discount;
		}

		/**
		 * add fund row in total review
		 *
		 * @param WC_Order $order
		 *
		 * @since  1.0.0
		 * @author YITHEMES
		 */
		public function add_fund_total_in_invoice( $order ) {

			$funds_used = yit_get_prop( $order, '_order_funds', true );

			if ( $funds_used ) {
				?>
				<tr class="invoice-details-funds-total">
					<td class="column-product"><?php _e( "Discount for Funds Used", 'yith-woocommerce-account-funds' ); ?></td>
					<td class="column-total"><?php echo wc_price( - $funds_used ); ?></td>
				</tr>
				<?php
			}
		}
	}
}

function YITH_WCDP_YITH_PDF_Invoice_Compatibility() {

	return YITH_WCDP_YITH_PDF_Invoice_Compatibility::get_instance();
}

