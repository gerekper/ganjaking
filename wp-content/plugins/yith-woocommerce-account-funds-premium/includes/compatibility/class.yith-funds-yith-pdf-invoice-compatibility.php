<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_Funds_YITH_PDF_Invoice_Compatibility' ) ) {

	class YITH_Funds_YITH_PDF_Invoice_Compatibility {

		protected static $_instance;


		public function __construct() {
			//add_filter('yith_ywpi_invoice_total', array( $this,'set_order_total_in_invoice'),10, 2 );
			//add_action('yith_pdf_invoice_before_total', array( $this, 'add_fund_total_in_invoice' ),10 ,1 );

			add_filter( 'yith_ywpi_create_automatic_invoices', array( $this, 'exclude_deposit_to_invoice' ), 10, 2 );
			add_filter( 'yith_ywpi_show_invoice_button_order_list', array(
				$this,
				'hide_invoice_button_order_list'
			), 10, 2 );
			add_filter( 'yith_ywpi_show_packing_slip_button_order_list', array(
				$this,
				'hide_invoice_button_order_list'
			), 10, 2 );
			add_filter( 'yith_ywpi_show_invoice_button_order_page', array(
				$this,
				'hide_invoice_button_order_page'
			), 10, 2 );
			add_filter( 'yith_ywpi_show_packing_slip_button_order_page', array(
				$this,
				'hide_packing_slip_button_order_page'
			), 10, 2 );
			add_filter( 'yith_ywpi_show_pro_forma_invoice_button_view_order', array(
				$this,
				'hide_pro_forma_invoice_button_view_order'
			), 10, 2 );
			add_action( 'yith_ywpi_after_button_order_list', array( $this, 'after_button_order_list' ), 10 );
			add_filter( 'yith_ywpi_print_document_notes', array( $this, 'print_order_funds_usage_note' ), 10, 2 );
		}

		/**
		 * @author YITHEMES
		 * @since 1.0.0
		 * @return YITH_Funds_YITH_PDF_Invoice_Compatibility unique access
		 */
		public static function get_instance() {

			if ( is_null( self::$_instance ) ) {

				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * exclude order deposit from automatic inovice
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param bool $make_invoice
		 * @param int $the_order
		 *
		 * @return bool
		 */
		public function exclude_deposit_to_invoice( $make_invoice, $the_order ) {

			if ( ! ( $the_order instanceof WC_Order ) ) {
				$the_order = wc_get_order( $the_order );
			}
			if ( ywf_order_has_deposit( $the_order ) ) {

				$make_invoice = false;
			}

			return $make_invoice;
		}

		/**
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param string $html ,
		 * @param WC_Order $order
		 *
		 * @return string
		 */
		public function hide_invoice_button_order_list( $html, $order ) {


			if ( ywf_order_has_deposit( $order ) ) {

				$html = '';
			}

			return $html;
		}

		/**
		 * hide invoice button in deposit order
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param bool $show_button
		 * @param WC_Order $order
		 *
		 * @return bool
		 */
		public function hide_invoice_button_order_page( $show_button, $order ) {

			if ( ywf_order_has_deposit( $order ) ) {

				$show_button = false;
			}

			return $show_button;
		}


		/**
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param WC_Order $order
		 */
		public function after_button_order_list( $order ) {

			if ( ywf_order_has_deposit( $order ) ) {

				echo sprintf( '<small>%s</small>', __( 'It is not possible to create an invoice for deposit', 'yith-woocommerce-account-funds' ) );
			}
		}

		/**
		 * hide pro forma invoice button in my-account
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param bool $show_button
		 * @param WC_Order $order
		 *
		 * @return bool
		 */
		public function hide_pro_forma_invoice_button_view_order( $show_button, $order ) {

			if ( ywf_order_has_deposit( $order ) ) {

				$show_button = false;
			}

			return $show_button;
		}


		/**
		 * hide invoice packing slip button in deposit order
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param bool $show_button
		 * @param WC_Order $order
		 *
		 * @return bool
		 */
		public function hide_packing_slip_button_order_page( $show_button, $order ) {

			if ( ywf_order_has_deposit( $order ) ) {

				$show_button = false;
			}


			return $show_button;
		}

		/**
		 * Set right order total
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param float $order_total
		 * @param int $order_id
		 *
		 * @return float
		 */
		public function set_order_total_in_invoice( $order_total, $the_order ) {

			if ( ! ( $the_order instanceof WC_Order ) ) {
				$the_order = wc_get_order( $the_order );
			}
			$funds_used = $the_order->get_meta( '_order_funds' );


			if ( $funds_used ) {
				$order_total = $order_total - $funds_used;
			}

			return $order_total;
		}

		/**
		 * add fund row in total review
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param WC_Order $order
		 */
		public function add_fund_total_in_invoice( $order ) {

			$funds_used = $order->get_meta( '_order_funds' );

			if ( $funds_used ) {
				?>
                <tr class="invoice-details-funds-total">
                    <td class="column-product"><?php _e( "Discount for Funds Used", 'yith-woocommerce-account-funds' ); ?></td>
                    <td class="column-total"><?php echo wc_price( - $funds_used ); ?></td>
                </tr>
				<?php
			}
		}

		/**
		 * @param string $notes
		 * @param YITH_Document $document
		 */
		public function print_order_funds_usage_note( $notes, $document ) {

			$order = $document->order;
			$payment_method = $order->get_meta( 'payment_method' );
			$br = $notes ? '<br>' : '';

			if ( 'yith_funds' === $payment_method ) {

				$notes .= sprintf( '%s%s', $br, __( 'Order paid through funds', 'yith-woocommerce-account-funds' ) );


			}

			return $notes;
		}
	}
}

function YITH_Funds_YITH_PDF_Invoice_Compatibility() {

	return YITH_Funds_YITH_PDF_Invoice_Compatibility::get_instance();
}

