<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YITH_YWPI_Backend' ) ) {

	/**
	 * Implements backend features
	 *
	 * @class   YITH_YWPI_Backend
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 */
	class YITH_YWPI_Backend {


		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
		protected static $instance;

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
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 * @access public
		 */
		private function __construct() {

			add_action( 'admin_enqueue_scripts', array(
				$this,
				'enqueue_styles'
			) );

			/*
			* Add the document generation buttons on admin orders page
			*/
			add_action( 'woocommerce_admin_order_actions_end', array(
				$this,
				'show_order_page_buttons'
			) );

			/*
			* Add a create/view packing slip button on admin orders page
			*/
			add_action( 'woocommerce_admin_order_actions_end', array(
				$this,
				'show_packing_slip_buttons'
			) );

			/**
			 * Add metabox on order, to let vendor add order tracking code and carrier
			 */
			add_action( 'add_meta_boxes', array(
				$this,
				'add_invoice_metabox'
			), 10, 2 );

			add_action( 'woocommerce_admin_field_ywpi_dropbox', array(
				$this,
				'show_dropbox_option'
			), 10, 1 );

		}

		/**
		 * Show DropBox option section
		 *
		 * @param array $args
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function show_dropbox_option( $args = array() ) {

			if ( empty( $args ) ) {
				return;
			}

			$args['value']       = ( get_option( $args['id'] ) ) ? get_option( $args['id'] ) : '';
			$name                = isset( $args['name'] ) ? $args['name'] : '';
			$dropbox_accesstoken = ywpi_get_option( 'ywpi_dropbox_access_token' );

			$show_dropbox_login = false;

			// Dropbox API v2 fix
			$dropbox_app_key		= YITH_PDF_Invoice_DropBox::get_instance()->dropbox_app_key;
			$dropbox_redurect_uri	= YITH_PDF_Invoice_DropBox::get_instance()->dropbox_redurect_uri;
			$dropbox_accesstoken	= YITH_PDF_Invoice_DropBox::get_instance()->get_dropbox_access_token();

			?>
			<tr valign="top">
				<th scope="row">
					<label for="ywpi_enable_dropbox"><?php echo $name; ?></label>
				</th>
				<td class="forminp forminp-color plugin-option">
					<fieldset>
						<legend class="screen-reader-text"><span><?php echo $name; ?></span></legend>

						<p style="margin-bottom: 10px;">
							<?php
							$example_url = '<a class="thickbox" href="' . YITH_YWPI_ASSETS_IMAGES_URL . 'dropbox-howto.jpg?TB_iframe=true&width=600&height=550">';
							echo sprintf( __('Authorize this plugin to access to your Dropbox space.<br/>All <b>new documents</b> will be sent to your Dropbox space as soon as they are created.<br/>Copy and paste the authorization code here, as in %sthis short guide%s.','yith-woocommerce-pdf-invoice'), $example_url, '</a>' );
							?>
						</p>

						<p style="margin-bottom: 10px;">
							<label for="ywpi_dropbox_key"><strong><?php esc_html_e( 'Access Token', 'yith-woocommerce-pdf-invoice' ); ?>:</strong></label>
							<input type="password" id="ywpi_dropbox_key" name="ywpi_dropbox_key" value="<?php echo $dropbox_accesstoken; ?>" style="width: 50%;">
						</p>

						<div style="margin-bottom: 10px;">
							<a href="https://www.dropbox.com/1/oauth2/authorize?client_id=<?php echo $dropbox_app_key; ?>&response_type=code&redirect_uri=<?php echo $dropbox_redurect_uri; ?>"
							   id="ywpi_enable_dropbox_button"
							   class="button button-primary"
							   target="_blank"><?php esc_html_e( 'Get new Access Token', 'yith-woocommerce-pdf-invoice' ); ?></a>
						</div>

					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="ywpi_dropbox_folder"><?php esc_html_e( 'Dropbox folder', 'yith-woocommerce-pdf-invoice' ); ?></label>
				</th>
				<td class="forminp forminp-text">
					<input name="ywpi_dropbox_folder" id="ywpi_dropbox_folder" type="text" style="" value="<?php echo ywpi_get_option( 'ywpi_dropbox_folder' ); ?>" class="" placeholder="">
					<span class="description"><?php esc_html_e( 'Choose the name of the Dropbox folder where to save the files', 'yith-woocommerce-pdf-invoice' ); ?></span>
				</td>
			</tr>
			<?php
		}

		/**
		 *  Add a metabox on backend order page, to be filled with order tracking information
		 *
		 * @param string  $post_type
		 * @param WP_Post $post
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 * @access public
		 * @return void
		 */
		function add_invoice_metabox( $post_type, $post ) {

			if ( 'shop_order' != $post_type ) {
				return;
			}

			if ( apply_filters( 'yith_ywpi_show_metabox_for_order', true, $post ) ) {

				add_meta_box( 'yith-pdf-invoice-box',
					esc_html__( 'YITH PDF Invoice', 'yith-woocommerce-pdf-invoice' ),
					array(
						$this,
						'show_metabox',
					),
					'shop_order',
					'side',
					'high' );
			}
		}

		/**
		 * Enqueue css file
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 */
		public function enqueue_styles() {

			wp_enqueue_style( 'ywpi_css', YITH_YWPI_ASSETS_URL . '/css/ywpi.css' );
		}

		/**
		 * Add invoice actions to the orders listing
		 *
		 * @param WC_Order $order
		 *
		 * @return string
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function show_invoice_buttons( $order ) {

			$html = '';

			//  In "Preview mode" it's not possible to generate valid documents
			if ( YITH_PDF_Invoice()->preview_mode ) {
				return $html;
			}

			$invoice = ywpi_get_invoice( yit_get_prop( $order, 'id' ) );
			if ( ! $invoice->is_valid() ) {
				return $html;
			}

			if ( $invoice->generated() ) {
				$url   = YITH_PDF_Invoice()->get_action_url( 'view', 'invoice', yit_get_prop( $order, 'id' ) );
				$text  = esc_html__( "Show invoice", 'yith-woocommerce-pdf-invoice' );
				$class = "ywpi_view_invoice";
			} elseif ( apply_filters( 'yith_ywpi_can_create_document', true, yit_get_prop( $order, 'id' ), 'invoice' ) ) {
				$url   = YITH_PDF_Invoice()->get_action_url( 'create', 'invoice', yit_get_prop( $order, 'id' ) );
				$text  = esc_html__( "Create invoice", 'yith-woocommerce-pdf-invoice' );
				$class = "ywpi_create_invoice";
			} else {
				return $html;
			}

			if ( 'open' == ywpi_get_option( 'ywpi_pdf_invoice_behaviour' ) && $class != "ywpi_create_invoice" ){
				$html = '<a target="_blank" href="' . $url . '" class="button tips ywpi_buttons ' . $class . '" data-tip="' . $text . '" title="' . $text . '">' . $text . '</a>';
			}
			else{
				$html = '<a href="' . $url . '" class="button tips ywpi_buttons ' . $class . '" data-tip="' . $text . '" title="' . $text . '">' . $text . '</a>';

			}

			if ( $invoice->generated() ) {
				$url   = YITH_PDF_Invoice()->get_action_url( 'regenerate', 'invoice', yit_get_prop( $order, 'id' ) );
				$text  = esc_html__( "Regenerate invoice", 'yith-woocommerce-pdf-invoice' );
				$class = "ywpi_regenerate_invoice";
				$html .= '<a href="' . $url . '" class="button tips ywpi_buttons ' . $class . '" data-tip="' . $text . '" title="' . $text . '">' . $text . '</a>';
			}

			return $html;
		}

		/**
		 * Add packing slip actions to the orders listing
		 *
		 * @param WC_Order $order
		 *
		 * @return string
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function show_packing_slip_buttons( $order ) {
			$html = '';

			//  In "Preview mode" it's not possible to generate valid documents
			if ( YITH_PDF_Invoice()->preview_mode ) {
				return $html;
			}

			if ( YITH_PDF_Invoice()->enable_packing_slip ) {

				$shipping_document = new YITH_Shipping( yit_get_prop( $order, 'id' ) );
				if ( ! $shipping_document->is_valid() ) {
					return $html;
				}

				if ( $shipping_document->generated() ) {
					$url   = YITH_PDF_Invoice()->get_action_url( 'view', 'packing-slip', yit_get_prop( $order, 'id' ) );
					$text  = esc_html__( "Show packing slip", 'yith-woocommerce-pdf-invoice' );
					$class = "ywpi_view_packing_slip";

				} elseif ( apply_filters( 'yith_ywpi_can_create_document', true, yit_get_prop( $order, 'id' ), 'packing-slip' ) ) {
					$url   = YITH_PDF_Invoice()->get_action_url( 'create', 'packing-slip', yit_get_prop( $order, 'id' ) );
					$text  = esc_html__( "Create packing slip", 'yith-woocommerce-pdf-invoice' );
					$class = "ywpi_create_packing_slip";
				} else {
					return $html;
				}

				if ( 'open' == ywpi_get_option( 'ywpi_pdf_invoice_behaviour' ) && $class != "ywpi_create_packing_slip" ){
					$html = '<a target="_blank" href="' . $url . '" class="button tips ywpi_buttons ' . $class . '" data-tip="' . $text . '" title="' . $text . '">' . $text . '</a>';
				}
				else{
					$html = '<a href="' . $url . '" class="button tips ywpi_buttons ' . $class . '" data-tip="' . $text . '" title="' . $text . '">' . $text . '</a>';

				}

				if ( $shipping_document->generated() ) {
					$url   = YITH_PDF_Invoice()->get_action_url( 'regenerate', 'packing-slip', yit_get_prop( $order, 'id' ) );
					$text  = esc_html__( "Regenerate packing slip", 'yith-woocommerce-pdf-invoice' );
					$class = "ywpi_regenerate_packing_slip";
					$html .= '<a href="' . $url . '" class="button tips ywpi_buttons ' . $class . '" data-tip="' . $text . '" title="' . $text . '">' . $text . '</a>';
				}

			}

			return $html;
		}

		/**
		 * Show document generation buttons on orders page
		 *
		 * @param WC_Order $order
		 */
		public function show_order_page_buttons( $order ) {

			$invoice_section = $this->show_invoice_buttons( $order );

			$invoice_action  = apply_filters( 'yith_ywpi_show_invoice_button_order_list',
				$invoice_section, $order );

			if ( $invoice_action ) {
				echo $invoice_action;
			}

			$html = $this->show_packing_slip_buttons( $order );

			$packing_slip_action = apply_filters( 'yith_ywpi_show_packing_slip_button_order_list',
				$html, $order );

			if ( $packing_slip_action ) {
				echo $packing_slip_action;
			}

			do_action( 'ywpi_after_show_invoice_buttons', $order );

		}

		/**
		 * Show the preview metabox for testing the current PDF template
		 *
		 * @param WP_Post $post
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		function show_preview_mode_metabox( $post ) {
			if ( ! YITH_PDF_Invoice()->preview_mode ) {

				return;
			}

			$order = wc_get_order( $post );

			?>
			<div class="invoice-information">
				<p>
					<a class="button tips ywpi_create_invoice"
					   data-tip="<?php esc_html_e( "Preview invoice", 'yith-woocommerce-pdf-invoice' ); ?>"
					   href="<?php echo YITH_PDF_Invoice()->get_action_url( 'preview', 'invoice', yit_get_prop( $order, 'id' ) ); ?>">
						<?php esc_html_e( "Preview invoice", 'yith-woocommerce-pdf-invoice' ); ?></a>
				</p>
			</div>
			<?php
		}

		/**
		 * Show the invoice section on edit order page
		 *
		 * @param WP_Post $post the order object that is currently shown
		 */
		public function show_invoice_status( $post ) {
			if ( YITH_PDF_Invoice()->preview_mode ) {
				return;
			}

			if ( apply_filters( 'yith_ywpi_show_invoice_status', false ) ){
				return;
			}

			/** @var YITH_Invoice $invoice */
			$order   = wc_get_order( $post );
			$invoice = ywpi_get_invoice( yit_get_prop( $order, 'id' ) );

			$is_receipt = get_post_meta( $order->get_id(), '_billing_invoice_type' , true );

			?>
			<div class="ywpi-document-section">
				<span class="ywpi-section-title"><?php esc_html_e( 'Invoice status', 'yith-woocommerce-pdf-invoice' ); ?></span>
				<?php if ( $invoice->generated() ) :  ?>

					<?php if ( $is_receipt != 'receipt' ) :  ?>
						<div class="ywpi-section-row">
							<span class="ywpi-left-label"><?php echo apply_filters('ywpi_invoice_number_label_edit_order_page',esc_html__( 'Invoice number: ', 'yith-woocommerce-pdf-invoice' ),$order,$invoice); ?></span>
							<span class="ywpi-right-value"><?php echo $invoice->get_formatted_document_number(); ?></span>
						</div>
					<?php else : ?>
						<div class="ywpi-section-row">
							<span class="ywpi-left-label"><?php echo apply_filters('ywpi_receipt_label_edit_order_page',esc_html__( 'Receipt', 'yith-woocommerce-pdf-invoice' ),$order,$invoice); ?></span>
						</div>

					<?php endif; ?>

					<?php if ( $is_receipt != 'receipt' ) :  ?>
						<div class="ywpi-section-row">
							<span class="ywpi-left-label"><?php esc_html_e( 'Invoice date: ', 'yith-woocommerce-pdf-invoice' ); ?></span>
							<span class="ywpi-right-value"><?php echo $invoice->get_formatted_document_date(); ?></span>
						</div>
					<?php else : ?>
						<div class="ywpi-section-row">
							<span class="ywpi-left-label"><?php esc_html_e( 'Receipt date: ', 'yith-woocommerce-pdf-invoice' ); ?></span>
							<span class="ywpi-right-value"><?php echo $invoice->get_formatted_document_date(); ?></span>
						</div>
					<?php endif; ?>

				<?php else : ?>
					<div class="ywpi-section-row">
						<span><?php esc_html_e( 'There is no invoice for this order', 'yith-woocommerce-pdf-invoice' ); ?></span>
					</div>
				<?php endif; ?>

				<div class="ywpi-section-row">
					<?php if ( $invoice->generated() ) : ?>

						<a <?php if ( 'open' == ywpi_get_option( 'ywpi_pdf_invoice_behaviour' ) ) {
							echo 'target="_blank"';
						} ?> class="button tips ywpi_view_invoice"
						     data-tip="<?php esc_html_e( "View", 'yith-woocommerce-pdf-invoice' ); ?>"
						     href="<?php echo YITH_PDF_Invoice()->get_action_url( 'view', 'invoice', yit_get_prop( $order, 'id' ) ); ?>">
							<?php esc_html_e( "PDF", 'yith-woocommerce-pdf-invoice' ); ?></a>

						<?php do_action('yith_ywpi_after_view_pdf_invoice', $post); ?>

						<?php if ( YITH_PDF_Invoice()->user_can_delete_document( yit_get_prop( $order, 'id' ), 'invoice' ) ) : ?>
							<a class="button tips ywpi_cancel_invoice"
							   data-tip="<?php esc_html_e( "Remove", 'yith-woocommerce-pdf-invoice' ); ?>"
							   href="<?php echo YITH_PDF_Invoice()->get_action_url( 'reset', 'invoice', yit_get_prop( $order, 'id' ) ); ?>">
								<?php esc_html_e( "Remove", 'yith-woocommerce-pdf-invoice' ); ?></a>
						<?php endif; ?>
						<a class="button tips ywpi_regenerate_invoice"
						   target="_self"
						   data-tip="<?php esc_html_e( "Regenerate", 'yith-woocommerce-pdf-invoice' ); ?>"
						   href="<?php echo YITH_PDF_Invoice()->get_action_url( 'regenerate', 'invoice', yit_get_prop( $order, 'id' ) ); ?>">
							<?php esc_html_e( "Regenerate", 'yith-woocommerce-pdf-invoice' ); ?></a>
					<?php elseif ( apply_filters( 'yith_ywpi_show_invoice_button_order_page', true, $order ) &&
					               apply_filters( 'yith_ywpi_can_create_document', true, yit_get_prop( $order, 'id' ), 'invoice' )
					) : ?>
						<a class="button tips ywpi_create_invoice"
						   data-tip="<?php esc_html_e( "Create", 'yith-woocommerce-pdf-invoice' ); ?>"
						   href="<?php echo YITH_PDF_Invoice()->get_action_url( 'create', 'invoice', yit_get_prop( $order, 'id' ) ); ?>">
							<?php esc_html_e( "Create", 'yith-woocommerce-pdf-invoice' ); ?></a>
					<?php
					endif;
					?>
				</div>

				<?php do_action( 'yith_ywpi_bottom_invoice_section', $post ); ?>
			</div>
			<?php
		}

		/**
		 * Show the credit note section for the current order
		 *
		 * @param WP_Post $post the order object that is currently shown
		 */
		public function show_credit_note_status( $post ) {
			if ( YITH_PDF_Invoice()->preview_mode ) {

				return;
			}

			if ( ! YITH_PDF_Invoice()->enable_credit_note ) {
				return;
			}

			$order   = wc_get_order( $post->ID );
			$refunds = $order->get_refunds();

			if ( $refunds ) {
				?>
				<div class="ywpi-document-section">
					<span class="ywpi-section-title"><?php esc_html_e( 'Credit note status', 'yith-woocommerce-pdf-invoice' ); ?></span>

					<table class="ywpi-refund-table">
						<?php foreach ( $refunds as $refund ):
							/** @var WC_Order_Refund $refund */
							$credit_note = ywpi_get_credit_note( yit_get_prop( $refund, 'id' ) );
							$target = 'open' == ywpi_get_option( 'ywpi_pdf_invoice_behaviour' ) ? 'target="_blank"' : '';
							?>
							<tr>
								<td><?php echo esc_html__( 'Refund #', 'yith-woocommerce-pdf-invoice' ) . yit_get_prop( $refund, 'id' ); ?></td>
								<td class="ywpi-refund-actions">
									<?php if ( $credit_note->generated() ) : ?>
										<a class="button tips ywpi-view-credit-note"
											<?php echo $target; ?>
                                           data-tip="<?php esc_html_e( "View credit note", 'yith-woocommerce-pdf-invoice' ); ?>"
                                           href="<?php echo YITH_PDF_Invoice()->get_action_url( 'view', 'credit-note', yit_get_prop( $refund, 'id' ) ); ?>">
											<?php _ex( 'PDF', 'Button text to display the credit note', 'yith-woocommerce-pdf-invoice' ); ?>
										</a>

										<?php do_action('yith_ywpi_after_view_pdf_credit_note', $refund); ?>

										<?php if ( YITH_PDF_Invoice()->user_can_delete_document( yit_get_prop( $refund, 'id' ), 'credit-note' ) ) : ?>
											<a class="button tips ywpi-cancel-credit-note"
											   data-tip="<?php esc_html_e( "Remove credit note", 'yith-woocommerce-pdf-invoice' ); ?>"
											   href="<?php echo YITH_PDF_Invoice()->get_action_url( 'reset', 'credit-note', yit_get_prop( $refund, 'id' ) ); ?>">
												<?php _ex( "Remove", 'Button text to delete a credit note', 'yith-woocommerce-pdf-invoice' ); ?>
											</a>
										<?php endif; ?>
										<a class="button tips ywpi-regenerate-credit-note"
										   data-tip="<?php esc_html_e( "Regenerate credit note", 'yith-woocommerce-pdf-invoice' ); ?>"
										   href="<?php echo YITH_PDF_Invoice()->get_action_url( 'regenerate', 'credit-note', yit_get_prop( $refund, 'id' ) ); ?>">
											<?php _ex( "Regenerate", 'Button text to regenerate a credit note', 'yith-woocommerce-pdf-invoice' ); ?>
										</a>
									<?php elseif ( apply_filters( 'yith_ywpi_show_invoice_button_order_page', true, $refund ) ) : ?>
										<a class="button tips ywpi-create-credit-note"
										   data-tip="<?php esc_html_e( "Create", 'yith-woocommerce-pdf-invoice' ); ?>"
										   href="<?php echo YITH_PDF_Invoice()->get_action_url( 'create', 'credit-note', yit_get_prop( $refund, 'id' ) ); ?>">
											<?php _ex( 'Create', 'Button text to create the credit note', 'yith-woocommerce-pdf-invoice' );
											?></a>
									<?php
									endif;
									?>
								</td>
							</tr>

						<?php endforeach; ?>
					</table>
				</div>
				<?php
			}
		}

		/**
		 * Show the packing slip status for this order
		 *
		 * @param WP_Post $post the order object that is currently shown
		 */
		public function show_packing_slip_status( $post ) {
			if ( YITH_PDF_Invoice()->preview_mode ) {

				return;
			}

			if ( ! YITH_PDF_Invoice()->enable_packing_slip ) {
				return;
			}

			$order = wc_get_order( $post->ID );
			if ( ! apply_filters( 'yith_ywpi_show_packing_slip_button_order_page', true, $order ) ) {
				return;
			}

			$shipping = ywpi_get_packing_slip( yit_get_prop( $order, 'id' ) );
			?>

			<div class="ywpi-document-section">
				<span class="ywpi-section-title">
					<?php esc_html_e( 'Packing slip status', 'yith-woocommerce-pdf-invoice' ); ?>
				</span>
				<?php if ( $shipping->generated() ) : ?>
					<div class="ywpi-section-row">
						<span><?php esc_html_e( 'Packing slip created.', 'yith-woocommerce-pdf-invoice' ); ?></span>
					</div>
				<?php else : ?>
					<div class="ywpi-section-row">
						<span><?php esc_html_e( 'There is no packing slip available for this order.', 'yith-woocommerce-pdf-invoice' ); ?></span>
					</div>
				<?php endif; ?>

				<div class="ywpi-section-row">
					<?php if ( $shipping->generated() ) : ?>
						<a <?php if ( 'open' == ywpi_get_option( 'ywpi_pdf_invoice_behaviour' ) ) {
							echo 'target="_blank"';
						} ?> class="button tips ywpi_view_packing_slip"
						     data-tip="<?php esc_html_e( "View packing slip", 'yith-woocommerce-pdf-invoice' ); ?>"
						     href=" <?php echo YITH_PDF_Invoice()->get_action_url( 'view', 'packing-slip', yit_get_prop( $order, 'id' ) ); ?>">
							<?php esc_html_e( "View", 'yith-woocommerce-pdf-invoice' ); ?>
						</a>
						<?php if ( YITH_PDF_Invoice()->user_can_delete_document( yit_get_prop( $order, 'id' ), 'packing-slip' ) ) : ?>
							<a class="button tips ywpi-cancel-packing-slip"
							   data-tip="<?php esc_html_e( "Remove packing slip", 'yith-woocommerce-pdf-invoice' ); ?>"
							   href="<?php echo YITH_PDF_Invoice()->get_action_url( 'reset', 'packing-slip', yit_get_prop( $order, 'id' ) ); ?>">
								<?php _ex( "Remove", 'Button text to remove a document', 'yith-woocommerce-pdf-invoice' ); ?>
							</a>
						<?php endif; ?>
						<a class="button tips ywpi-regenerate-packing-slip"
						   data-tip="<?php esc_html_e( "Regenerate packing slip", 'yith-woocommerce-pdf-invoice' ); ?>"
						   href="<?php echo YITH_PDF_Invoice()->get_action_url( 'regenerate', 'packing-slip', yit_get_prop( $order, 'id' ) ); ?>">
							<?php _ex( "Regenerate", 'Button text to regenerate a document', 'yith-woocommerce-pdf-invoice' ); ?>
						</a>
					<?php elseif ( apply_filters( 'yith_ywpi_can_create_document', true, yit_get_prop( $order, 'id' ), 'packing-slip' ) ) : ?>
						<a class="button tips ywpi_create_packing_slip"
						   data-tip="<?php esc_html_e( "Create packing slip", 'yith-woocommerce-pdf-invoice' ); ?>"
						   href="<?php echo YITH_PDF_Invoice()->get_action_url( 'create', 'packing-slip', yit_get_prop( $order, 'id' ) ); ?>">
							<?php esc_html_e( "Create", 'yith-woocommerce-pdf-invoice' ); ?></a>
					<?php endif; ?>
				</div>
			</div>
			<?php
		}

		/**
		 * Show the proforma status for this order
		 *
		 * @param WP_Post $post the order object that is currently shown
		 */
		public function show_proforma_status( $post ) {

			if ( YITH_PDF_Invoice()->preview_mode || 'no' == ywpi_get_option ( 'ywpi_enable_pro_forma' ) ) {

				return;
			}

			$order = wc_get_order( $post->ID );
			if ( ! apply_filters( 'yith_ywpi_show_proforma_button_order_page', true, $order ) ) {
				return;
			}

			$proforma = ywpi_get_pro_forma( yit_get_prop( $order, 'id' ) );

			?>

			<div class="ywpi-document-section">
				<span class="ywpi-section-title">
					<?php esc_html_e( 'Proforma status', 'yith-woocommerce-pdf-invoice' ); ?>
				</span>
				<?php if ( $proforma->generated() ) : ?>
					<div class="ywpi-section-row">
						<span><?php esc_html_e( 'Proforma created.', 'yith-woocommerce-pdf-invoice' ); ?></span>
					</div>
				<?php else : ?>
					<div class="ywpi-section-row">
						<span><?php esc_html_e( 'There is no proforma for this order.', 'yith-woocommerce-pdf-invoice' ); ?></span>
					</div>
				<?php endif; ?>

				<div class="ywpi-section-row">
					<?php if ( $proforma->generated() ) : ?>
						<a <?php if ( 'open' == ywpi_get_option( 'ywpi_pdf_invoice_behaviour' ) ) {
							echo 'target="_blank"';
						} ?> class="button tips ywpi_view_proforma"
						     data-tip="<?php esc_html_e( "View proforma", 'yith-woocommerce-pdf-invoice' ); ?>"
						     href=" <?php echo YITH_PDF_Invoice()->get_action_url( 'view', 'proforma', yit_get_prop( $order, 'id' ) ); ?>">
							<?php esc_html_e( "View", 'yith-woocommerce-pdf-invoice' ); ?>
						</a>
						<a class="button tips ywpi-regenerate-proforma"
						   data-tip="<?php esc_html_e( "Regenerate proforma", 'yith-woocommerce-pdf-invoice' ); ?>"
						   href="<?php echo YITH_PDF_Invoice()->get_action_url( 'regenerate', 'proforma', yit_get_prop( $order, 'id' ) ); ?>">
							<?php _ex( "Regenerate", 'Button text to regenerate a document', 'yith-woocommerce-pdf-invoice' ); ?>
						</a>
					<?php elseif ( apply_filters( 'yith_ywpi_can_create_document', true, yit_get_prop( $order, 'id' ), 'packing-slip' ) ) : ?>
						<a class="button tips ywpi_create_proforma"
						   data-tip="<?php esc_html_e( "Create proforma", 'yith-woocommerce-pdf-invoice' ); ?>"
						   href="<?php echo YITH_PDF_Invoice()->get_action_url( 'create', 'proforma', yit_get_prop( $order, 'id' ) ); ?>">
							<?php esc_html_e( "Create", 'yith-woocommerce-pdf-invoice' ); ?></a>
					<?php endif; ?>
				</div>
			</div>
			<?php
		}


		/**
		 * Show metabox content on back-end order page
		 *
		 * @param WP_Post $post the order object that is currently shown
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 * @access public
		 * @return void
		 */
		function show_metabox( $post ) {

			if ( YITH_PDF_Invoice()->preview_mode ) {

				return $this->show_preview_mode_metabox( $post );
			}
			?>
			<div class="ywpi-metabox">
				<?php
				$this->show_invoice_status( $post );
				$this->show_credit_note_status( $post );
				$this->show_packing_slip_status( $post );
				$this->show_proforma_status( $post );

				do_action('ywpi_print_additional_sections',$post);

				?>
			</div>
			<?php

			$_order = wc_get_order( $post );
			do_action( 'yith_ywpi_after_button_order_list', $_order );
		}
	}
}
