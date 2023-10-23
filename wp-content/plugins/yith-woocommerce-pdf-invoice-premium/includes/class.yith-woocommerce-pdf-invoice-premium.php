<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase, WordPress.Files.FileName.InvalidClassFileName

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_WooCommerce_Pdf_Invoice_Premium' ) ) {
	/**
	 * Implements features of YITH WooCommerce Pdf Invoice
	 *
	 * @class   YITH_WooCommerce_Pdf_Invoice_Premium
	 * @package YITH\PDF_Invoice\Classes
	 * @since   1.0.0
	 * @author  YITH <plugins@yithemes.com>
	 */
	class YITH_WooCommerce_Pdf_Invoice_Premium extends YITH_WooCommerce_Pdf_Invoice {

		/**
		 * Set if proforma is available
		 *
		 * @var bool
		 */
		public $enable_pro_forma = false;

		/**
		 * Set if proforma is available
		 *
		 * @var bool
		 */
		public $show_pro_forma_on_my_account = false;

		/**
		 * Set if a proforma invoice should be sent.
		 *
		 * @var bool
		 */
		public $send_pro_forma = false;

		/**
		 * Set if credit notes are enabled.
		 *
		 * @var bool
		 */
		public $enable_credit_note = false;

		/**
		 * The template type selected.
		 *
		 * @var string
		 */
		private $template_type = 'invoice';

		/**
		 * Single instance of the class
		 *
		 * @var object
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
		 * @access public
		 */
		public function __construct() {
			parent::__construct();

			// Including the GDRP.
			add_action( 'plugins_loaded', array( $this, 'load_privacy' ), 20 );

			add_filter( 'woocommerce_admin_settings_sanitize_option_ywpi_dropbox_key', array( $this, 'custom_save_ywpi_dropbox' ) );

			add_action( 'woocommerce_update_option', array( $this, 'copy_invoice_logo_to_local_path' ) );

			/**
			 * Sync new documents with DropBox
			 */
			add_action( 'yith_ywpi_document_created', array( $this, 'sync_to_dropbox' ) );
			add_action( 'yith_ywpi_document_regenerated', array( $this, 'sync_to_dropbox' ) );

			/**
			 * Sync new documents with Google Drive
			 */
			add_action( 'yith_ywpi_document_created', array( $this, 'upload_file_to_google_drive' ), 10, 3 );
			add_action( 'yith_ywpi_document_regenerated', array( $this, 'upload_file_to_google_drive' ), 10, 3 );

			// Register custom fields of checkout.
			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'yith_ywpi_add_item_metas' ), 10, 1 );

			// Hide the order metas created.
			add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'yith_ywpi_hidden_order_itemmeta' ), 10, 1 );

			add_action( 'yith_pdf_invoice_before_total', array( $this, 'show_gift_card_discount' ) );

			add_action( 'init', array( $this, 'force_update_option_invoice_date_to_show_in_invoice' ) );

			add_filter( 'woocommerce_admin_settings_sanitize_option_ywpi_invoice_folder_format', array( $this, 'sanitize_option_ywpi_invoice_folder_format_filter' ) );

			add_filter( 'ywpi_documents_options', array( $this, 'add_document_format_dependencies' ) );
			add_filter( 'ywpi_template_content_options', array( $this, 'add_document_template_dependencies' ) );
			add_filter( 'ywpi_documents_type_options', array( $this, 'add_document_type_dependencies' ) );

			add_filter( 'yith_ywpi_style_before_pdf_creation', array( $this, 'additional_style_for_pdf' ), 10, 3 );

			// Not create invoice if order total is 0.
			add_filter( 'yith_ywpi_can_create_document', array( $this, 'avoid_create_invoice_when_order_value_is_zero' ), 10, 3 );

			/**
			 * Check for third party plugin compatibility
			 */
			$this->check_third_part_compatibility();

			/**
			 * Add some fields on checkout process, that can be added on invoices.
			 */
			YITH_Checkout_addon::get_instance()->initialize();

			/**
			 * Call the correct template class.
			 */
			$this->template_type = yith_ywpi_get_selected_template();
			$this->load_document_class( $this->template_type );
		}

		/**
		 * Including the GDRP
		 */
		public function load_privacy() {
			if ( class_exists( 'YITH_Privacy_Plugin_Abstract' ) ) {
				require_once YITH_YWPI_DIR . 'includes/class.yith-woocommerce-pdf-invoice-privacy.php';
			}
		}

		/**
		 * Show the gift card discount.
		 *
		 * @param  mixed $order The order object.
		 * @return void
		 */
		public function show_gift_card_discount( $order ) {
			$gift_card_total = $order->get_meta( '_ywgc_applied_gift_cards_totals' );

			if ( ! empty( $gift_card_total ) ) {
				ob_start();
				?>
				<tr>
					<td class="left-content column-product"><?php esc_html_e( 'Gift card discount:', 'yith-woocommerce-pdf-invoice' ); ?></td>
					<td class="right-content column-total"><?php echo '-' . wp_kses_post( wc_price( $gift_card_total ) ); ?></td>
				</tr>

				<?php
				$res = ob_get_clean();
				/**
				 * APPLY_FILTERS: yith_pdf_invoice_show_gift_card_amount
				 *
				 * Filter the display of the gift card total applied to the order.
				 *
				 * @param string $res the gift card total section.
				 * @param string $gift_card_total the gift card total value.
				 * @param object $order the order object.
				 *
				 * @return string
				 */
				echo wp_kses_post( apply_filters( 'yith_pdf_invoice_show_gift_card_amount', $res, $gift_card_total, $order ) );
			}
		}

		/**
		 * Sync new documents with DropBox
		 *
		 * @param object $document The document object.
		 */
		public function sync_to_dropbox( $document ) {
			/**
			 * APPLY_FILTERS: ywpi_allow_sync_to_dropbox
			 *
			 * Filter the condition to allow the Dropbox sync.
			 *
			 * @param bool true to allow, false to not.
			 * @param object $document the document object.
			 *
			 * @return bool
			 */
			if ( 'yes' === ywpi_get_option( 'ywpi_dropbox_allow_upload', true, 'no' ) && ywpi_get_option( 'ywpi_dropbox_access_token' ) && apply_filters( 'ywpi_allow_sync_to_dropbox', true, $document ) ) {
				YITH_PDF_Invoice_DropBox::get_instance()->send_document_to_dropbox( $document );
			}
		}

		/**
		 * Sync new documents with Google Drive
		 *
		 * @param object $document The document object.
		 * @param string $document_type The document type.
		 * @param string $extension The extension.
		 */
		public function upload_file_to_google_drive( $document, $document_type, $extension ) {
			if ( 'yes' === ywpi_get_option( 'ywpi_google_drive_allow_upload', true, 'no' ) ) {
				YITH_PDF_Invoice_Google_Drive::get_instance()->upload_file( $document, $document_type, $extension );
			}
		}

		/**
		 * Retrive the document title
		 *
		 * @param YITH_Document $document the document.
		 *
		 * @return string
		 * @since  1.0.0
		 */
		public function get_document_title( $document ) {
			$is_receipt = $document->order->get_meta( '_billing_invoice_type' );

			if ( $document instanceof YITH_Pro_Forma ) {
				/**
				 * APPLY_FILTERS: yith_ywpi_proforma_document_title
				 *
				 * Filter the Proforma document title.
				 *
				 * @param string the title.
				 *
				 * @return string
				 */
				$title = apply_filters( 'yith_ywpi_proforma_document_title', esc_html__( 'Pro-forma invoice', 'yith-woocommerce-pdf-invoice' ), 10, 1 );

				return $title;
			} elseif ( $document instanceof YITH_Credit_Note ) {
				/**
				 * APPLY_FILTERS: yith_ywpi_credit_note_document_title
				 *
				 * Filter the Credit Note document title.
				 *
				 * @param string the title.
				 *
				 * @return string
				 */
				$title = apply_filters( 'yith_ywpi_credit_note_document_title', esc_html__( 'Credit note', 'yith-woocommerce-pdf-invoice' ), 10, 1 );

				return $title;
			} elseif ( $document instanceof YITH_Invoice && 'receipt' === strval( $is_receipt ) ) {
				/**
				 * APPLY_FILTERS: yith_ywpi_receipt_document_title
				 *
				 * Filter the Receipt document title.
				 *
				 * @param string the title.
				 *
				 * @return string
				 */
				$title = apply_filters( 'yith_ywpi_receipt_document_title', esc_html__( 'Receipt', 'yith-woocommerce-pdf-invoice' ), 10, 1 );

				return $title;
			}

			return parent::get_document_title( $document );
		}

		/**
		 * Initialize the plugin options
		 *
		 * @since  1.0.0
		 */
		public function init_plugin_options() {
			$this->enable_pro_forma             = ( 'yes' === strval( ywpi_get_option( 'ywpi_enable_pro_forma', null, 'yes' ) ) );
			$this->show_pro_forma_on_my_account = ( 'yes' === strval( ywpi_get_option( 'ywpi_show_pro_forma_my_account', null, 'yes' ) ) );
			$this->send_pro_forma               = ( 'yes' === strval( ywpi_get_option( 'ywpi_send_pro_forma', null, 'no' ) ) );
			$this->enable_credit_note           = ( 'yes' === strval( ywpi_get_option( 'ywpi_enable_credit_notes' ) ) );

			parent::init_plugin_options();
		}

		/**
		 * Make a local copy of the image to be used as company logo
		 *
		 * @param array $option The array of options.
		 *
		 * @since  1.0.0
		 */
		public function copy_invoice_logo_to_local_path( $option ) {
			if ( 'ywpi_company_logo' === $option['id'] ) {
				if ( ! empty( $_POST['ywpi_company_logo'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
					$upload_dir = wp_upload_dir();
					$local_path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], sanitize_key( wp_unslash( $_POST['ywpi_company_logo'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

					if ( file_exists( $local_path ) ) {
						copy( $local_path, YITH_YWPI_INVOICE_LOGO_PATH );
					}
				}
			}
		}

		/**
		 * Check for third party plugin compatibility needs
		 *
		 * @since  1.0.0
		 */
		public function check_third_part_compatibility() {
			if ( ywpi_is_active_woo_eu_vat_number() ) {
				add_filter( 'ywpi_general_options', array( $this, 'add_vat_number_source_option' ) );
			}
		}

		/**
		 * Add an option to choose the VAT number source
		 *
		 * @param array $settings current option list.
		 *
		 * @return mixed
		 * @since  1.0.0
		 */
		public function add_vat_number_source_option( $settings ) {
			$vat_number_source = array(
				'name'      => __( 'VAT number source', 'yith-woocommerce-pdf-invoice' ),
				'id'        => 'ywpi_ask_vat_number_source',
				'type'      => 'yith-field',
				'yith-type' => 'radio',
				'options'   => array(
					'yith'          => 'Show the standard VAT number field from YITH WooCommerce PDF Invoices & Packing Slips',
					'eu-vat-number' => 'Use the VAT number field from WooThemes EU VAT number plugin',
				),
				'default'   => 'yith',
			);

			$settings['settings-general'] = array_slice( $settings['settings-general'], 0, count( $settings['settings-general'] ) - 2, true ) +
			array( 'vat_number_source' => $vat_number_source ) +
			array_slice( $settings['settings-general'], 3, count( $settings['settings-general'] ) - 1, true );

			return $settings;
		}

		/**
		 * Check if there are action to be done at this moment, related to back end
		 *
		 * @since  1.0.0
		 */
		public function execute_generation_actions() {
			// Check actions from the base version.
			parent::execute_generation_actions();

			// Check generation actions.
			if ( isset( $_GET[ YITH_YWPI_RESET_DROPBOX ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				YITH_PDF_Invoice_DropBox::get_instance()->disable_dropbox_backup();

				wp_safe_redirect( esc_url_raw( remove_query_arg( YITH_YWPI_RESET_DROPBOX ) ) );
				exit();
			}
		}

		/**
		 * Add the right action based on GET var current used
		 */
		public function execute_visualization_actions() {
			parent::execute_visualization_actions();

			if ( isset( $_GET[ YITH_YWPI_CREATE_PRO_FORMA_INVOICE_ACTION ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$this->get_pro_forma_invoice( sanitize_key( wp_unslash( $_GET[ YITH_YWPI_CREATE_PRO_FORMA_INVOICE_ACTION ] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			}
		}

		/**
		 * Create a new proforma invoice document if not exists, else return the previous one
		 *
		 * @param int $order_id the order id for which the document is created.
		 */
		public function get_pro_forma_invoice( $order_id ) {
			if ( ! $this->enable_pro_forma ) {
				return;
			}

			$document = $this->create_document( $order_id, 'proforma' );

			/*
			 * Check for url validation.
			 */
			if ( ! $document ) {
				return;
			}

			// Create the file and show it.
			$this->show_file( $document );
		}

		/**
		 * Save dropbox field.
		 */
		public function custom_save_ywpi_dropbox() {
			YITH_PDF_Invoice_DropBox::get_instance()->custom_save_ywpi_dropbox();
		}

		/**
		 * Add a button to print invoice, if exists, from order page on frontend.
		 * If not exists add a button for a proforma document.
		 *
		 * @param array    $actions current actions.
		 * @param WC_Order $order   the order being shown.
		 *
		 * @return mixed
		 * @since  1.0.0
		 */
		public function print_invoice_button( $actions, $order ) {
			$invoice  = new YITH_Invoice( $order->get_id() );
			$proforma = new YITH_Pro_Forma( $order->get_id() );

			/**
			 * APPLY_FILTERS: yith_ywpi_show_invoice_button_view_order
			 *
			 * Filter the condition to show the invoice button in the order on My account.
			 *
			 * @param bool true to display it, false to not.
			 *
			 * @return bool
			 */
			if ( $invoice->generated() && apply_filters( 'yith_ywpi_show_invoice_button_view_order', true, $order ) ) {
				$action = 'view';
				$type   = 'invoice';

				$args = array(
					'action' => $action,
					'type'   => $type,
					'id'     => $order->get_id(),
				);

				$nonce_action = $action . $type . $order->get_id();

				$url = esc_url( wp_nonce_url( add_query_arg( $args ), $nonce_action ) );

				/**
				 * APPLY_FILTERS: yith_ywpi_print_invoice_name_frontend
				 *
				 * Filter the "Invoice" button name in My account.
				 *
				 * @param string the button name.
				 *
				 * @return string
				 */
				// Add the print button.
				$actions['print-invoice'] = array(
					'url'  => $url,
					'name' => apply_filters( 'yith_ywpi_print_invoice_name_frontend', esc_html__( 'Invoice', 'yith-woocommerce-pdf-invoice' ) ),
				);

				/**
				 * APPLY_FILTERS: yith_ywpi_show_pro_forma_invoice_button_view_order
				 *
				 * Filter the condition to show the pro forma button in the order on My account.
				 *
				 * @param bool true to display it, false to not.
				 * @param object $order the order object.
				 *
				 * @return bool
				 */
			} elseif ( $this->enable_pro_forma && $this->show_pro_forma_on_my_account && $proforma->generated() && apply_filters( 'yith_ywpi_show_pro_forma_invoice_button_view_order', true, $order ) ) {
				/**
				 * APPLY_FILTERS: yith_ywpi_print_invoice_name_frontend
				 *
				 * Filter the "Proforma" button name in My account.
				 *
				 * @param string the button name.
				 *
				 * @return string
				 */
				$actions['print-pro-forma-invoice'] = array(
					'url'  => $this->get_action_url( 'preview', 'proforma', $order->get_id() ),
					'name' => apply_filters( 'yith_ywpi_pro_forma_button_text_my_account', esc_html__( 'Pro-forma', 'yith-woocommerce-pdf-invoice' ) ),
				);
			}

			if ( get_option( 'ywpi_enable_credit_notes', 'no' ) === 'yes' ) {
				$refunds = $order->get_refunds();

				foreach ( $refunds as $refund ) {
					$credit_note = new YITH_Credit_Note( $refund->get_id() );

					if ( $credit_note->generated() ) {
						/**
						 * APPLY_FILTERS: yith_ywpi_print_credit_note_name_frontend
						 *
						 * Filter the "Credit Note" button name in My account.
						 *
						 * @param string the button name.
						 *
						 * @return string
						 */
						// Add the print button.
						$actions['print-credit-note'] = array(
							'url'  => $this->get_action_url( 'view', 'credit-note', $refund->get_id() ),
							'name' => apply_filters( 'yith_ywpi_print_credit_note_name_frontend', esc_html__( 'Credit Note', 'yith-woocommerce-pdf-invoice' ) ),
						);
					}
				}
			}

			/**
			 * APPLY_FILTERS: yith_ywpi_my_order_actions
			 *
			 * Filter the order actions in My account.
			 *
			 * @param array $actions the actions array.
			 * @param object $order the order object.
			 *
			 * @return array
			 */
			return apply_filters( 'yith_ywpi_my_order_actions', $actions, $order );
		}

		/**
		 * Retrieve the proforma invoice path
		 *
		 * @param string $status   current order status.
		 * @param int    $order_id order id.
		 *
		 * @return string
		 */
		public function get_pro_forma_attachment( $status, $order_id ) {
			$proforma_path = '';

			if ( $this->enable_pro_forma && $this->send_pro_forma ) {
				/**
				 * APPLY_FILTERS: yith_ywpi_pro_forma_attachment_on_emails_ids
				 *
				 * Filter the emails ID where we want to attach the proforma invoice.
				 *
				 * @param array the email ids to attach the proforma.
				 * @param int $order_id the order ID.
				 *
				 * @return array
				 */
				$attach_to_emails = apply_filters(
					'yith_ywpi_pro_forma_attachment_on_emails_ids',
					array(
						'customer_processing_order',
						'new_order',
						'customer_on_hold_order',
					),
					$order_id
				);

				if ( in_array( $status, $attach_to_emails, true ) ) {
					$document = $this->create_document( $order_id, 'proforma' );

					if ( $document && $document->generated() ) {
						$proforma_path = $document->get_full_path();
					}
				}
			}

			return $proforma_path;
		}

		/**
		 * Add custom metas to the order items
		 *
		 * @param int $order_id The order id.
		 */
		public function yith_ywpi_add_item_metas( $order_id ) {
			$order = wc_get_order( $order_id );
			$items = $order->get_items();

			foreach ( $items as $item_id => $item ) {
				$product = $item->get_product();

				if ( ! $product instanceof WC_Product ) {
					continue;
				}

				$product_regular_price     = $product->get_regular_price();
				$product_sku               = $product->get_sku();
				$product_short_description = $product->get_short_description();

				wc_update_order_item_meta( $item_id, '_ywpi_product_regular_price', $product_regular_price );
				wc_update_order_item_meta( $item_id, '_ywpi_product_sku', $product_sku );
				wc_update_order_item_meta( $item_id, '_ywpi_product_short_description', $product_short_description );

				/**
				 * DO_ACTION: yith_ywpi_custom_item_metas
				 *
				 * Allow actions in the custom item metas.
				 *
				 * @param int $item_id the item ID.
				 * @param object $order the order object
				 */
				do_action( 'yith_ywpi_custom_item_metas', $item_id, $order );
			}
		}

		/**
		 * Add to array the hidden order itemmetas.
		 *
		 * @param  array $meta_array The array with all the hidden metas.
		 */
		public function yith_ywpi_hidden_order_itemmeta( $meta_array ) {
			$meta_array[] = '_ywpi_product_regular_price';
			$meta_array[] = '_ywpi_product_sku';
			$meta_array[] = '_ywpi_product_short_description';

			return $meta_array;
		}

		/**
		 * Download the files as a zip.
		 *
		 * @param  mixed $order_ids     The order ids.
		 * @param  mixed $document_type The document type.
		 * @param  mixed $extension     The document extension.
		 * @return void
		 */
		public function download_files_as_zip( $order_ids = array(), $document_type = 'invoice', $extension = 'pdf' ) {
			$files    = array();
			$zip_name = 'credit-note' === $document_type ? 'credit_notes' : 'invoices';

			if ( 'xml' === $extension ) {
				$zip_name = 'xml';
			}

			$zip_name = $zip_name . '_' . uniqid( wp_rand(), true ) . '.zip';

			foreach ( $order_ids as $order_id ) {
				$document = ywpi_get_order_document_by_type( $order_id, $document_type );

				if ( $document ) {
					if ( is_file( $document->get_full_path( $extension, $order_id ) ) ) {
						$files[] = $document->get_full_path( $extension, $order_id );
					}
				}
			}

			$zip = new ZipArchive();
			$zip->open( $zip_name, ZipArchive::CREATE );

			foreach ( $files as $file ) {
				$filename = basename( $file );

				$zip->addFile( $file, $filename );
			}

			$zip->close();

			header( 'Content-Type: application/zip' );
			header( 'Content-disposition: attachment; filename=' . $zip_name );
			header( 'Content-Length: ' . filesize( $zip_name ) );
			header( 'Pragma: no-cache' );
			header( 'Expires: 0' );

			readfile( $zip_name ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile

			exit();
		}

		/**
		 * Set automatically the option "Date to show in the invoice"
		 */
		public function force_update_option_invoice_date_to_show_in_invoice() {
			$force_update_option_invoice_date_to_show_in_invoice = get_option( 'yith_ywpdi_force_update_option_invoice_date_to_show_in_invoice' );

			if ( ! $force_update_option_invoice_date_to_show_in_invoice ) { // Remove NOT logic condition to execute again the process (if the option is already saved).
				if ( get_option( 'ywpi_invoice_generation', 'manual' ) === 'auto' ) {
					$create_on = ywpi_get_option( 'ywpi_create_invoice_on' );
					$value     = 'processing' === strval( $create_on ) ? 'invoice_creation' : $create_on;
				} else {
					$value = 'completed';
				}

				update_option( 'ywpi_date_to_show_in_invoice', $value );
				update_option( 'yith_ywpdi_force_update_option_invoice_date_to_show_in_invoice', true );
			}
		}

		/**
		 * Filter the value of the setting ywpi_invoice_folder_format (documents storage tab)
		 *
		 * @param string $value The value of the option.
		 *
		 * @return mixed
		 */
		public function sanitize_option_ywpi_invoice_folder_format_filter( $value ) {
			$value = str_replace(
				array(
					'../',
					'./',
				),
				'',
				$value
			);

			return $value;
		}

		/**
		 * Add dependencies for document format options between different tabs depending if an option is enable or not.
		 *
		 * @param array $options The array of format options.
		 *
		 * @return mixed
		 */
		public function add_document_format_dependencies( $options ) {
			if ( 'yes' !== ywpi_get_option( 'ywpi_enable_credit_notes' ) ) {
				$credit_notes_options = array(
					'credit_notes_format_settings_title',
					'enable_credit_note_prefix_sufix',
					'credit_note_prefix',
					'credit_note_suffix',
					'credit_note_number_format',
					'credit_note_filename_format',
				);

				foreach ( $credit_notes_options as $credit_option ) {
					unset( $options['settings-documents'][ $credit_option ] );
				}
			}

			if ( 'yes' !== ywpi_get_option( 'ywpi_enable_packing_slip' ) ) {
				unset( $options['settings-documents']['packing_slip_filename_format'] );
			}

			return $options;
		}

		/**
		 * Add dependencies for document template options between different tabs depending if an option is enable or not.
		 *
		 * @param array $options The array of format options.
		 *
		 * @return mixed
		 */
		public function add_document_template_dependencies( $options ) {
			if ( 'yes' !== ywpi_get_option( 'ywpi_enable_credit_notes' ) ) {
				$credit_notes_options = array(
					'ywpi_credit_note_template_options',
					'ywpi_show_credit_note_notes',
					'ywpi_credit_note_notes',
					'ywpi_show_credit_note_footer',
					'ywpi_credit_note_footer',
					'ywpi_credit_note_template_style',
					'ywpi_credit_note_positive_values',
					'ywpi_credit_note_reason_column',
					'ywpi_credit_note_subtotal_column',
					'ywpi_credit_note_total_tax_column',
					'ywpi_credit_note_total_shipping_column',
					'ywpi_credit_note_total_column',
					'ywpi_credit_note_template_options_end',
					'ywpi_credit_note_product_name_column',
					'ywpi_credit_note_product_sku_column',
					'ywpi_credit_note_product_image_column',
				);

				foreach ( $credit_notes_options as $credit_option ) {
					unset( $options['template-content'][ $credit_option ] );
				}
			}

			if ( 'yes' !== ywpi_get_option( 'ywpi_enable_packing_slip' ) ) {
				$packing_slip_options = array(
					'section_template_packing_slip',
					'customer_shipping_details',
					'packing_slip_show_notes',
					'packing_slip_notes',
					'packing_slip_show_footer',
					'packing_slip_footer',
					'packing_slip_show_order_totals',
					'packing_slip_column_picture',
					'packing_slip_column_SKU',
					'packing_slip_column_weight',
					'packing_slip_column_short_description',
					'packing_slip_column_variation',
					'packing_slip_column_quantity',
					'packing_slip_column_regular_price',
					'packing_slip_column_sale_price',
					'packing_slip_column_product_price',
					'packing_slip_column_line_total',
					'packing_slip_column_tax',
					'packing_slip_column_percentage_tax',
					'packing_slip_column_total_taxed',
					'packing_slip_column_percentage',
					'packing_slip_general_description',
					'section_template_packing_slip_end',
				);

				foreach ( $packing_slip_options as $packing_option ) {
					unset( $options['template-content'][ $packing_option ] );
				}
			}

			return $options;
		}

		/**
		 * Add dependencies for document type options between different tabs depending if an option is enable or not.
		 *
		 * @param array $options The array of format options.
		 *
		 * @return mixed
		 */
		public function add_document_type_dependencies( $options ) {
			$credit_notes_enabled = 'yes' === ywpi_get_option( 'ywpi_enable_credit_notes' );

			if ( ! $credit_notes_enabled ) {
				unset( $options['documents_type']['documents-type-options']['sub-tabs']['documents_type-credit-notes'] );
			}

			return $options;
		}

		/**
		 * Load the class of each document.
		 *
		 * @param string $type Template type.
		 */
		public function load_document_class( $type ) {
			require_once 'templates/class.yith-ywpi-template-default.php';

			switch ( $type ) {
				case 'default':
					require_once 'templates/class.yith-ywpi-template.php';
					YITH_YWPI_Template::get_instance();
					break;

				case 'black_white':
					require_once 'templates/class.yith-ywpi-black-white-template.php';
					YITH_YWPI_Black_White_Template::get_instance();
					break;

				case 'modern':
					require_once 'templates/class.yith-ywpi-modern-template.php';
					YITH_YWPI_Modern_Template::get_instance();
					break;
			}
		}

		/**
		 * Add additional CSS for the PDF Template.
		 *
		 * @param string        $style The CSS rules.
		 * @param string        $template_selected The template selected ('default', 'black_white', 'modern').
		 * @param YITH_Document $document The document.
		 *
		 * @return string
		 */
		public function additional_style_for_pdf( $style, $template_selected, $document ) {
			$additional_css = '';

			switch ( $template_selected ) {
				case 'default':
					$additional_css = '';
					break;

				case 'black_white':
					$borders_color = ywpi_get_option( 'ywpi_borders_color_black_white' );
					$borders_color = ! empty( $borders_color ) ? $borders_color : '#000000';

					$additional_css = '
						.ywpi-document-data{
						    border: 2px solid ' . $borders_color . ';
						    border-left: none;
						    border-right:none;
						}
						.template_customer_details{
							border: 2px solid ' . $borders_color . ';
						    border-top: none;
						    border-right: none;
						    border-bottom: none;
						}
						.document-totals{
							border: 2px solid ' . $borders_color . ';
						    border-left: none;
						    border-right:none;
						}
						table.invoice-totals{
							border: 2px solid ' . $borders_color . ';
						    border-top: none;
						    border-right: none;
						    border-bottom: none;
						}
					';
					break;

				case 'modern':
					$invoice_number_color = ywpi_get_option( 'ywpi_invoice_number_color_modern' );
					$invoice_number_color = ! empty( $invoice_number_color ) ? $invoice_number_color : '#D67B64';

					$borders_color = ywpi_get_option( 'ywpi_borders_color_modern' );
					$borders_color = ! empty( $borders_color ) ? $borders_color : '#D67B64';

					$additional_css = '
						.invoice-data-content .ywpi-invoice-number, .invoice-data-content .ywpi-invoice-number-formatted, .invoice-data-content .ywpi-order-number{
							color: ' . $invoice_number_color . ';
						}
						.invoice-details thead th,
						.invoice-details thead,
						.invoice-details thead tr,
						.credit-note-details thead th,
						.credit-note-details thead,
						.credit-note-details thead tr {
							border: 2px solid ' . $borders_color . ';
							border-top: none;
							border-left: none;
							border-right: none;
						}
						.ywpi_notes_separator{
							color: ' . $borders_color . ';
						}
						.footer-separator{
							color: ' . $borders_color . ';
						}
					';
					break;
			}

			$style .= $additional_css;

			return $style;
		}

		/**
		 * Check if the existing option (ywpi_avoid_invoice_generation_order_zero) is enabled to avoid create invoices when the order value is 0.
		 *
		 * @param boolean $value         The value to return (true or false).
		 * @param int     $order_id      The order id.
		 * @param string  $document_type Document type.
		 *
		 * @return false|mixed|void
		 */
		public function avoid_create_invoice_when_order_value_is_zero( $value, $order_id, $document_type ) {
			if ( 'invoice' !== strval( $document_type ) ) {
				return $value;
			}

			$order = wc_get_order( $order_id );

			if ( ! $order instanceof WC_Order ) {
				return $value;
			}

			if ( 'yes' === get_option( 'ywpi_avoid_invoice_generation_order_zero', 'no' ) && 0.00 === floatval( $order->get_total() ) ) {
				$value = false;
			}

			return $value;
		}

		/**
		 * Action links
		 *
		 * @param array $links The actions links.
		 * @return array
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, $this->panel_page, true, YITH_YWPI_SLUG );

			return $links;
		}

		/**
		 * Plugin row meta.
		 *
		 * @param array    $new_row_meta_args The arguments of the new row meta.
		 * @param string[] $plugin_meta The plugin meta.
		 * @param string   $plugin_file The plugin file.
		 * @param array    $plugin_data The plugin data.
		 * @param string   $status The status.
		 * @param string   $init_file The init file.
		 *
		 * @return mixed
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_YWPI_INIT' ) {
			$new_row_meta_args = parent::plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file );

			if ( defined( $init_file ) && constant( $init_file ) === $plugin_file ) {
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;
		}
	}
}
