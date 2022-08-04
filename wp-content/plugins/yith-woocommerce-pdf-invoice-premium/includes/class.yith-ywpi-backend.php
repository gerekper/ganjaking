<?php // phpcs:ignore WordPress.NamingConventions.

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_YWPI_Backend' ) ) {

	/**
	 * Implements backend features
	 *
	 * @class   YITH_YWPI_Backend
	 * @package YITH\PDF_Invoice\Classes
	 * @since   1.0.0
	 * @author  YITH
	 */
	class YITH_YWPI_Backend {


		/**
		 * Single instance of the class
		 *
		 * @var \YITH_YWPI_Backend
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
			if ( isset( $_REQUEST['page'] ) && 'yith_woocommerce_pdf_invoice_panel' === $_REQUEST['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification
				YITH_PDF_Invoice_Google_Drive::get_instance();
			}

			/*
			* Enqueue styles.
			*/
			add_action(
				'admin_enqueue_scripts',
				array(
					$this,
					'enqueue_styles',
				)
			);

			/*
			* Add the document generation buttons on admin orders page
			*/
			add_action(
				'woocommerce_admin_order_actions_end',
				array(
					$this,
					'show_order_page_buttons',
				)
			);

			/*
			* Add a create/view packing slip button on admin orders page
			*/
			add_action(
				'woocommerce_admin_order_actions_end',
				array(
					$this,
					'show_packing_slip_buttons',
				)
			);

			/**
			 * Add metabox on order, to let vendor add order tracking code and carrier
			 */
			add_action(
				'add_meta_boxes',
				array(
					$this,
					'add_invoice_metabox',
				),
				10,
				2
			);

			/*
			* Show dropbox option.
			*/
			add_action(
				'woocommerce_admin_field_ywpi_dropbox',
				array(
					$this,
					'show_dropbox_option',
				),
				10,
				1
			);

			// plugin panel options.
			add_filter( 'yith_plugin_fw_panel_wc_extra_row_classes', array( $this, 'mark_options_disabled' ), 10, 23 );
		}

		/**
		 * Show DropBox option section
		 *
		 * @param array $args The arguments (id, name).
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function show_dropbox_option( $args = array() ) {

			if ( empty( $args ) ) {
				return;
			}
			$args['value'] = ( get_option( $args['id'] ) ) ? get_option( $args['id'] ) : '';
			$name          = isset( $args['name'] ) ? $args['name'] : '';

			// Dropbox API v2 fix.
			$dropbox_app_key      = YITH_PDF_Invoice_DropBox::get_instance()->dropbox_app_key;
			$dropbox_redurect_uri = YITH_PDF_Invoice_DropBox::get_instance()->dropbox_redurect_uri;
			$dropbox_accesstoken  = YITH_PDF_Invoice_DropBox::get_instance()->get_dropbox_access_token();

			?>
			<tr valign="top" class="yith-plugin-fw-panel-wc-row text deps-initialized" data-dep-target="ywpi_dropbox_key" data-dep-id="ywpi_dropbox_allow_upload" data-dep-value="yes" data-dep-type="fadeIn">
				<th scope="row" class="titledesc">
					<label for="ywpi_dropbox_key"><?php echo wp_kses_post( $name ); ?></label>
				</th>
				<td class="forminp forminp-text">
					<div class="yith-plugin-fw-field-wrapper yith-plugin-fw-text-field-wrapper">
						<fieldset>
							<legend class="screen-reader-text"><span><?php echo wp_kses_post( $name ); ?></span></legend>

							<p style="margin-bottom: 10px;">
								<?php
								$dropbox_key_field = array(
									'id'    => 'ywpi_dropbox_key',
									'name'  => 'ywpi_dropbox_key',
									'type'  => 'password',
									'value' => $dropbox_accesstoken,
								);

								yith_plugin_fw_get_field( $dropbox_key_field, true );
								?>
							</p>
							<span class="description">
								<?php
								$example_url = '<a class="thickbox" href="' . YITH_YWPI_ASSETS_IMAGES_URL . 'dropbox-howto.jpg?TB_iframe=true&width=600&height=550">';
								// translators: 1. The example URL. 2. End of the example URL Link (leave as </a>).
								echo sprintf( wp_kses_post( __( 'Copy and paste the Dropbox authorization code here. %1$sLearn how to find it >%2$s', 'yith-woocommerce-pdf-invoice' ) ), wp_kses_post( $example_url ), '</a>' );
								?>
								<div style="margin-bottom: 10px;">
									<a href="https://www.dropbox.com/1/oauth2/authorize?client_id=<?php echo wp_kses_post( $dropbox_app_key ); ?>&response_type=code&redirect_uri=<?php echo wp_kses_post( $dropbox_redurect_uri ); ?>"
									id="ywpi_enable_dropbox_button"
									target="_blank"><?php esc_html_e( 'Get your Dropbox authorization code >', 'yith-woocommerce-pdf-invoice' ); ?></a>
								</div>
							</span>
						</fieldset>
					</div>

				</td>
			</tr>
			<?php
		}

		/**
		 *  Add a metabox on backend order page, to be filled with order tracking information
		 *
		 * @param string  $post_type The post type.
		 * @param WP_Post $post      The post object.
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 * @access public
		 * @return void
		 */
		public function add_invoice_metabox( $post_type, $post ) {

			if ( 'shop_order' !== strval( $post_type ) ) {
				return;
			}

			if ( apply_filters( 'yith_ywpi_show_metabox_for_order', true, $post ) ) {

				add_meta_box(
					'yith-pdf-invoice-box',
					esc_html__( 'YITH PDF Invoice', 'yith-woocommerce-pdf-invoice' ),
					array(
						$this,
						'show_metabox',
					),
					'shop_order',
					'side',
					'high'
				);
			}
		}

		/**
		 * Enqueue css file
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 */
		public function enqueue_styles() {
			wp_enqueue_style( 'ywpi_css', YITH_YWPI_ASSETS_URL . '/css/ywpi.css', array(), YITH_YWPI_ENQUEUE_VERSION ); //phpcs:ignore
		}

		/**
		 * Add invoice actions to the orders listing
		 *
		 * @param WC_Order $order The order object.
		 *
		 * @return string
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function show_invoice_buttons( $order ) {

			$html = '';

			$invoice = ywpi_get_invoice( yit_get_prop( $order, 'id' ) );
			if ( ! $invoice->is_valid() ) {
				return $html;
			}

			if ( $invoice->generated() ) {
				$url   = YITH_PDF_Invoice()->get_action_url( 'view', 'invoice', yit_get_prop( $order, 'id' ) );
				$text  = esc_html__( 'Show invoice', 'yith-woocommerce-pdf-invoice' );
				$class = 'ywpi_view_invoice';
			} elseif ( apply_filters( 'yith_ywpi_can_create_document', true, yit_get_prop( $order, 'id' ), 'invoice' ) ) {
				$url   = YITH_PDF_Invoice()->get_action_url( 'create', 'invoice', yit_get_prop( $order, 'id' ) );
				$text  = esc_html__( 'Create invoice', 'yith-woocommerce-pdf-invoice' );
				$class = 'ywpi_create_invoice';
			} else {
				return $html;
			}

			if ( 'open' === strval( ywpi_get_option( 'ywpi_pdf_invoice_behaviour' ) ) && 'ywpi_create_invoice' !== strval( $class ) ) {
				$html = '<a target="_blank" href="' . $url . '" class="button tips ywpi_buttons ' . $class . '" data-tip="' . $text . '" title="' . $text . '">' . $text . '</a>';
			} else {
				$html = '<a href="' . $url . '" class="button tips ywpi_buttons ' . $class . '" data-tip="' . $text . '" title="' . $text . '">' . $text . '</a>';

			}

			if ( $invoice->generated() ) {
				$url   = YITH_PDF_Invoice()->get_action_url( 'regenerate', 'invoice', yit_get_prop( $order, 'id' ) );
				$text  = esc_html__( 'Regenerate invoice', 'yith-woocommerce-pdf-invoice' );
				$class = 'ywpi_regenerate_invoice';
				$html .= '<a href="' . $url . '" class="button tips ywpi_buttons ' . $class . '" data-tip="' . $text . '" title="' . $text . '">' . $text . '</a>';
			}

			return $html;
		}

		/**
		 * Add packing slip actions to the orders listing
		 *
		 * @param WC_Order $order The order object.
		 *
		 * @return string
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function show_packing_slip_buttons( $order ) {
			$html = '';

			if ( YITH_PDF_Invoice()->enable_packing_slip ) {

				$shipping_document = new YITH_Shipping( yit_get_prop( $order, 'id' ) );
				if ( ! $shipping_document->is_valid() ) {
					return $html;
				}

				if ( $shipping_document->generated() ) {
					$url   = YITH_PDF_Invoice()->get_action_url( 'view', 'packing-slip', yit_get_prop( $order, 'id' ) );
					$text  = esc_html__( 'Show packing slip', 'yith-woocommerce-pdf-invoice' );
					$class = 'ywpi_view_packing_slip';

				} elseif ( apply_filters( 'yith_ywpi_can_create_document', true, yit_get_prop( $order, 'id' ), 'packing-slip' ) ) {
					$url   = YITH_PDF_Invoice()->get_action_url( 'create', 'packing-slip', yit_get_prop( $order, 'id' ) );
					$text  = esc_html__( 'Create packing slip', 'yith-woocommerce-pdf-invoice' );
					$class = 'ywpi_create_packing_slip';
				} else {
					return $html;
				}

				if ( 'open' === strval( ywpi_get_option( 'ywpi_pdf_invoice_behaviour' ) ) && 'ywpi_create_packing_slip' !== $class ) {
					$html = '<a target="_blank" href="' . $url . '" class="button tips ywpi_buttons ' . $class . '" data-tip="' . $text . '" title="' . $text . '">' . $text . '</a>';
				} else {
					$html = '<a href="' . $url . '" class="button tips ywpi_buttons ' . $class . '" data-tip="' . $text . '" title="' . $text . '">' . $text . '</a>';

				}

				if ( $shipping_document->generated() ) {
					$url   = YITH_PDF_Invoice()->get_action_url( 'regenerate', 'packing-slip', yit_get_prop( $order, 'id' ) );
					$text  = esc_html__( 'Regenerate packing slip', 'yith-woocommerce-pdf-invoice' );
					$class = 'ywpi_regenerate_packing_slip';
					$html .= '<a href="' . $url . '" class="button tips ywpi_buttons ' . $class . '" data-tip="' . $text . '" title="' . $text . '">' . $text . '</a>';
				}
			}

			return $html;
		}

		/**
		 * Show document generation buttons on orders page
		 *
		 * @param WC_Order $order The order object.
		 */
		public function show_order_page_buttons( $order ) {

			$invoice_section = $this->show_invoice_buttons( $order );

			$invoice_action = apply_filters(
				'yith_ywpi_show_invoice_button_order_list',
				$invoice_section,
				$order
			);

			if ( $invoice_action ) {
				echo wp_kses_post( $invoice_action );
			}

			$html = $this->show_packing_slip_buttons( $order );

			$packing_slip_action = apply_filters(
				'yith_ywpi_show_packing_slip_button_order_list',
				$html,
				$order
			);

			if ( $packing_slip_action ) {
				echo wp_kses_post( $packing_slip_action );
			}

			do_action( 'ywpi_after_show_invoice_buttons', $order );

		}

		/**
		 * Show the invoice section on edit order page
		 *
		 * @param WP_Post $post the order object that is currently shown.
		 */
		public function show_invoice_status( $post ) {

			if ( apply_filters( 'yith_ywpi_show_invoice_status', false ) ) {
				return;
			}

			$order   = wc_get_order( $post );
			$invoice = ywpi_get_invoice( yit_get_prop( $order, 'id' ) );

			if ( is_object( $order ) ) {
				$is_receipt = get_post_meta( $order->get_id(), '_billing_invoice_type', true );
			} else {
				$is_receipt = '';
			}

			?>
			<div class="ywpi-document-section">
				<span class="ywpi-section-title"><?php esc_html_e( 'Invoice status', 'yith-woocommerce-pdf-invoice' ); ?></span>
				<?php if ( $invoice->generated() ) : ?>

					<?php if ( 'receipt' !== $is_receipt ) : ?>
						<div class="ywpi-section-row">
							<span class="ywpi-left-label"><?php echo esc_html( apply_filters( 'ywpi_invoice_number_label_edit_order_page', __( 'Invoice number: ', 'yith-woocommerce-pdf-invoice' ), $order, $invoice ) ); ?></span>
							<span class="ywpi-right-value"><?php echo wp_kses_post( $invoice->get_formatted_document_number() ); ?></span>
						</div>
					<?php else : ?>
						<div class="ywpi-section-row">
							<span class="ywpi-left-label"><?php echo esc_html( apply_filters( 'ywpi_receipt_label_edit_order_page', __( 'Receipt', 'yith-woocommerce-pdf-invoice' ), $order, $invoice ) ); ?></span>
						</div>

					<?php endif; ?>

					<?php if ( 'receipt' !== strval( $is_receipt ) ) : ?>
						<div class="ywpi-section-row">
							<span class="ywpi-left-label"><?php esc_html_e( 'Invoice date: ', 'yith-woocommerce-pdf-invoice' ); ?></span>
							<span class="ywpi-right-value"><?php echo wp_kses_post( $invoice->get_formatted_document_date() ); ?></span>
						</div>
					<?php else : ?>
						<div class="ywpi-section-row">
							<span class="ywpi-left-label"><?php esc_html_e( 'Receipt date: ', 'yith-woocommerce-pdf-invoice' ); ?></span>
							<span class="ywpi-right-value"><?php echo wp_kses_post( $invoice->get_formatted_document_date() ); ?></span>
						</div>
					<?php endif; ?>

				<?php else : ?>
					<div class="ywpi-section-row">
						<span><?php esc_html_e( 'There is no invoice for this order', 'yith-woocommerce-pdf-invoice' ); ?></span>
					</div>
				<?php endif; ?>

				<div class="ywpi-section-row">
					<?php if ( $invoice->generated() ) : ?>

						<a 
						target="_blank"
						class="button tips ywpi_view_invoice"
							data-tip="<?php esc_html_e( 'View', 'yith-woocommerce-pdf-invoice' ); ?>"
							href="<?php echo wp_kses_post( YITH_PDF_Invoice()->get_action_url( 'view', 'invoice', yit_get_prop( $order, 'id' ) ) ); ?>">
							<?php esc_html_e( 'PDF', 'yith-woocommerce-pdf-invoice' ); ?></a>

						<?php do_action( 'yith_ywpi_after_view_pdf_invoice', $post ); ?>

						<?php if ( YITH_PDF_Invoice()->user_can_delete_document( yit_get_prop( $order, 'id' ), 'invoice' ) ) : ?>
							<a class="button tips ywpi_cancel_invoice"
							data-tip="<?php esc_html_e( 'Remove', 'yith-woocommerce-pdf-invoice' ); ?>"
							href="<?php echo wp_kses_post( YITH_PDF_Invoice()->get_action_url( 'reset', 'invoice', yit_get_prop( $order, 'id' ) ) ); ?>">
								<?php esc_html_e( 'Remove', 'yith-woocommerce-pdf-invoice' ); ?></a>
						<?php endif; ?>
						<a class="button tips ywpi_regenerate_invoice"
						target="_self"
						data-tip="<?php esc_html_e( 'Regenerate', 'yith-woocommerce-pdf-invoice' ); ?>"
						href="<?php echo wp_kses_post( YITH_PDF_Invoice()->get_action_url( 'regenerate', 'invoice', yit_get_prop( $order, 'id' ) ) ); ?>">
							<?php esc_html_e( 'Regenerate', 'yith-woocommerce-pdf-invoice' ); ?></a>
						<div class="ywpi_view_documents">
							<?php $invoices_url = admin_url( 'admin.php' . yith_ywpi_get_panel_url( 'documents_type' ) ); ?>
							<a class="ywpi_view_invoices_list"
							target="_blank"
							data-tip="<?php esc_html_e( 'View all invoices', 'yith-woocommerce-pdf-invoice' ); ?>"
							href="<?php echo esc_url( $invoices_url ); ?>">
								<?php esc_html_e( 'View all invoices >', 'yith-woocommerce-pdf-invoice' ); ?></a>
						</div>

						<?php
					elseif ( apply_filters( 'yith_ywpi_show_invoice_button_order_page', true, $order ) &&
					apply_filters( 'yith_ywpi_can_create_document', true, yit_get_prop( $order, 'id' ), 'invoice' )
					) :
						?>
						<a class="button tips ywpi_create_invoice"
						data-tip="<?php esc_html_e( 'Create', 'yith-woocommerce-pdf-invoice' ); ?>"
						href="<?php echo wp_kses_post( YITH_PDF_Invoice()->get_action_url( 'create', 'invoice', yit_get_prop( $order, 'id' ) ) ); ?>">
								<?php esc_html_e( 'Create', 'yith-woocommerce-pdf-invoice' ); ?></a>
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
		 * @param WP_Post $post the order object that is currently shown.
		 */
		public function show_credit_note_status( $post ) {

			if ( ! YITH_PDF_Invoice()->enable_credit_note ) {
				return;
			}

			$order        = wc_get_order( $post->ID );
			$refunds      = $order->get_refunds();
			$is_generated = false;

			if ( $refunds ) {
				?>
				<div class="ywpi-document-section">
					<span class="ywpi-section-title"><?php esc_html_e( 'Credit note status', 'yith-woocommerce-pdf-invoice' ); ?></span>

					<table class="ywpi-refund-table">
						<?php
						foreach ( $refunds as $refund ) :

							$credit_note = ywpi_get_credit_note( yit_get_prop( $refund, 'id' ) );
							?>
							<tr>
								<td class="ywpi-refund-label"><?php echo esc_html__( 'Refund #', 'yith-woocommerce-pdf-invoice' ) . wp_kses_post( yit_get_prop( $refund, 'id' ) ); ?></td>
								<td class="ywpi-refund-actions">
									<?php if ( $credit_note->generated() ) : ?>
										<?php $is_generated = true; ?>
										<a target="_blank"
											class="button tips ywpi-view-credit-note"
											data-tip="<?php esc_html_e( 'View credit note', 'yith-woocommerce-pdf-invoice' ); ?>"
											href="<?php echo wp_kses_post( YITH_PDF_Invoice()->get_action_url( 'view', 'credit-note', yit_get_prop( $refund, 'id' ) ) ); ?>">
											<?php echo esc_html_x( 'PDF', 'Button text to display the credit note', 'yith-woocommerce-pdf-invoice' ); ?>
										</a>

										<?php do_action( 'yith_ywpi_after_view_pdf_credit_note', $refund ); ?>

										<?php if ( YITH_PDF_Invoice()->user_can_delete_document( yit_get_prop( $refund, 'id' ), 'credit-note' ) ) : ?>
											<a class="button tips ywpi-cancel-credit-note"
												data-tip="<?php esc_html_e( 'Remove credit note', 'yith-woocommerce-pdf-invoice' ); ?>"
												href="<?php echo wp_kses_post( YITH_PDF_Invoice()->get_action_url( 'reset', 'credit-note', yit_get_prop( $refund, 'id' ) ) ); ?>">
												<?php echo esc_html_x( 'Remove', 'Button text to delete a credit note', 'yith-woocommerce-pdf-invoice' ); ?>
											</a>
										<?php endif; ?>
										<a class="button tips ywpi-regenerate-credit-note"
											data-tip="<?php esc_html_e( 'Regenerate credit note', 'yith-woocommerce-pdf-invoice' ); ?>"
											href="<?php echo wp_kses_post( YITH_PDF_Invoice()->get_action_url( 'regenerate', 'credit-note', yit_get_prop( $refund, 'id' ) ) ); ?>">
											<?php echo esc_html_x( 'Regenerate', 'Button text to regenerate a credit note', 'yith-woocommerce-pdf-invoice' ); ?>
										</a>
									<?php elseif ( apply_filters( 'yith_ywpi_show_invoice_button_order_page', true, $refund ) ) : ?>
										<a class="button tips ywpi-create-credit-note"
											data-tip="<?php esc_html_e( 'Create', 'yith-woocommerce-pdf-invoice' ); ?>"
											href="<?php echo wp_kses_post( YITH_PDF_Invoice()->get_action_url( 'create', 'credit-note', yit_get_prop( $refund, 'id' ) ) ); ?>">
											<?php
											echo esc_html_x( 'Create', 'Button text to create the credit note', 'yith-woocommerce-pdf-invoice' );
											?>
										</a>
										<?php
									endif;
									?>
								</td>
							</tr>

						<?php endforeach; ?>
					</table>
					<?php
					if ( $is_generated ) {
						?>
						<div class="ywpi_view_documents credit_notes_view_all">
							<?php $invoices_url = admin_url( 'admin.php' . yith_ywpi_get_panel_url( 'documents_type', 'documents_type-credit-notes' ) ); ?>
							<a class="ywpi_view_credit_notes_list"
								target="_blank"
								data-tip="<?php esc_html_e( 'View all credit notes', 'yith-woocommerce-pdf-invoice' ); ?>"
								href="<?php echo esc_url( $invoices_url ); ?>">
								<?php esc_html_e( 'View all credit notes >', 'yith-woocommerce-pdf-invoice' ); ?></a>
						</div>
						<?php

					}
					?>
				</div>
				<?php
			}
		}

		/**
		 * Show the packing slip status for this order
		 *
		 * @param WP_Post $post the order object that is currently shown.
		 */
		public function show_packing_slip_status( $post ) {

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
						<a target="_blank"
						class="button tips ywpi_view_packing_slip"
							data-tip="<?php esc_html_e( 'View packing slip', 'yith-woocommerce-pdf-invoice' ); ?>"
							href=" <?php echo wp_kses_post( YITH_PDF_Invoice()->get_action_url( 'view', 'packing-slip', yit_get_prop( $order, 'id' ) ) ); ?>">
							<?php esc_html_e( 'View', 'yith-woocommerce-pdf-invoice' ); ?>
						</a>
						<?php if ( YITH_PDF_Invoice()->user_can_delete_document( yit_get_prop( $order, 'id' ), 'packing-slip' ) ) : ?>
							<a class="button tips ywpi-cancel-packing-slip"
							data-tip="<?php esc_html_e( 'Remove packing slip', 'yith-woocommerce-pdf-invoice' ); ?>"
							href="<?php echo wp_kses_post( YITH_PDF_Invoice()->get_action_url( 'reset', 'packing-slip', yit_get_prop( $order, 'id' ) ) ); ?>">
								<?php echo esc_html_x( 'Remove', 'Button text to remove a document', 'yith-woocommerce-pdf-invoice' ); ?>
							</a>
						<?php endif; ?>
						<a class="button tips ywpi-regenerate-packing-slip"
						data-tip="<?php esc_html_e( 'Regenerate packing slip', 'yith-woocommerce-pdf-invoice' ); ?>"
						href="<?php echo wp_kses_post( YITH_PDF_Invoice()->get_action_url( 'regenerate', 'packing-slip', yit_get_prop( $order, 'id' ) ) ); ?>">
							<?php echo esc_html_x( 'Regenerate', 'Button text to regenerate a document', 'yith-woocommerce-pdf-invoice' ); ?>
						</a>
					<?php elseif ( apply_filters( 'yith_ywpi_can_create_document', true, yit_get_prop( $order, 'id' ), 'packing-slip' ) ) : ?>
						<a class="button tips ywpi_create_packing_slip"
						data-tip="<?php esc_html_e( 'Create packing slip', 'yith-woocommerce-pdf-invoice' ); ?>"
						href="<?php echo wp_kses_post( YITH_PDF_Invoice()->get_action_url( 'create', 'packing-slip', yit_get_prop( $order, 'id' ) ) ); ?>">
							<?php esc_html_e( 'Create', 'yith-woocommerce-pdf-invoice' ); ?></a>
					<?php endif; ?>
				</div>
			</div>
			<?php
		}

		/**
		 * Show the proforma status for this order
		 *
		 * @param WP_Post $post the order object that is currently shown.
		 */
		public function show_proforma_status( $post ) {

			if ( 'no' === strval( ywpi_get_option( 'ywpi_enable_pro_forma' ) ) ) {
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
						<a target="_blank"
						class="button tips ywpi_view_proforma"
						data-tip="<?php esc_html_e( 'View proforma', 'yith-woocommerce-pdf-invoice' ); ?>"
						href=" <?php echo wp_kses_post( YITH_PDF_Invoice()->get_action_url( 'view', 'proforma', yit_get_prop( $order, 'id' ) ) ); ?>">
							<?php esc_html_e( 'PDF', 'yith-woocommerce-pdf-invoice' ); ?>
						</a>
						<a class="button tips ywpi-regenerate-proforma"
						data-tip="<?php esc_html_e( 'Regenerate proforma', 'yith-woocommerce-pdf-invoice' ); ?>"
						href="<?php echo wp_kses_post( YITH_PDF_Invoice()->get_action_url( 'regenerate', 'proforma', yit_get_prop( $order, 'id' ) ) ); ?>">
							<?php echo esc_html_x( 'Regenerate', 'Button text to regenerate a document', 'yith-woocommerce-pdf-invoice' ); ?>
						</a>
					<?php elseif ( apply_filters( 'yith_ywpi_can_create_document', true, yit_get_prop( $order, 'id' ), 'packing-slip' ) ) : ?>
						<a class="button tips ywpi_create_proforma"
						data-tip="<?php esc_html_e( 'Create proforma', 'yith-woocommerce-pdf-invoice' ); ?>"
						href="<?php echo wp_kses_post( YITH_PDF_Invoice()->get_action_url( 'create', 'proforma', yit_get_prop( $order, 'id' ) ) ); ?>">
							<?php esc_html_e( 'Create', 'yith-woocommerce-pdf-invoice' ); ?></a>
					<?php endif; ?>
				</div>
			</div>
			<?php
		}


		/**
		 * Show metabox content on back-end order page
		 *
		 * @param WP_Post $post the order object that is currently shown.
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 * @access public
		 * @return void
		 */
		public function show_metabox( $post ) {

			?>
			<div class="ywpi-metabox">
				<?php
				$this->show_invoice_status( $post );
				$this->show_credit_note_status( $post );
				$this->show_packing_slip_status( $post );
				$this->show_proforma_status( $post );

				do_action( 'ywpi_print_additional_sections', $post );

				?>
			</div>
			<?php

			$_order = wc_get_order( $post );
			do_action( 'yith_ywpi_after_button_order_list', $_order );
		}

		/**
		 * Adds yith-disabled class
		 * Adds class to fields when required, and when disabled state cannot be achieved any other way (eg. by dependencies)
		 *
		 * @param array $classes Array of field extra classes.
		 * @param array $field   Array of field data.
		 *
		 * @return array Filtered array of extra classes
		 */
		public function mark_options_disabled( $classes, $field ) {
			if ( isset( $field['id'] ) && 'ywpi_show_delivery_info' == $field['id'] && ! ( defined( 'YITH_DELIVERY_DATE_PREMIUM' ) && YITH_DELIVERY_DATE_PREMIUM ) ) { //phpcs:ignore
				$classes[] = 'yith-disabled';
			}

			return $classes;
		}
	}
}
