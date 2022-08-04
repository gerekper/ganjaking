<?php // phpcs:ignore WordPress.NamingConventions.

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
	 * @author  YITH
	 */
	class YITH_WooCommerce_Pdf_Invoice_Premium extends YITH_WooCommerce_Pdf_Invoice {

		/**
		 * Set if proforma is available
		 *
		 * @var bool
		 */
		public $enable_pro_forma = false;

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
		 * @author Lorenzo giuffrida
		 * @access public
		 */
		public function __construct() {
			parent::__construct();

			// Including the GDRP.
			add_action(
				'plugins_loaded',
				array(
					$this,
					'load_privacy',
				),
				20
			);

			add_filter(
				'woocommerce_admin_settings_sanitize_option_ywpi_dropbox_key',
				array(
					$this,
					'custom_save_ywpi_dropbox',
				)
			);

			add_action( 'woocommerce_update_option', array( $this, 'copy_invoice_logo_to_local_path' ) );

			/**
			 * Sync new documents with DropBox
			 */
			add_action(
				'yith_ywpi_document_created',
				array(
					$this,
					'sync_to_dropbox',
				)
			);

			add_action(
				'yith_ywpi_document_regenerated',
				array(
					$this,
					'sync_to_dropbox',
				)
			);
			/**
			 * Sync new documents with Google Drive
			 */
			add_action(
				'yith_ywpi_document_created',
				array(
					$this,
					'upload_file_to_google_drive',
				),
				10,
				3
			);

			add_action(
				'yith_ywpi_document_regenerated',
				array(
					$this,
					'upload_file_to_google_drive',
				),
				10,
				3
			);

			// Register custom fields of checkout.
			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'yith_ywpi_add_item_metas' ), 10, 1 );

			// Hide the order metas created.
			add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'yith_ywpi_hidden_order_itemmeta' ), 10, 1 );

			add_action(
				'yith_pdf_invoice_before_total',
				array(
					$this,
					'show_gift_card_discount',
				)
			);

			add_action( 'init', array( $this, 'force_update_option_invoice_date_to_show_in_invoice' ) );

			add_filter(
				'woocommerce_admin_settings_sanitize_option_ywpi_invoice_folder_format',
				array(
					$this,
					'sanitize_option_ywpi_invoice_folder_format_filter',
				)
			);

			add_filter(
				'ywpi_documents_options',
				array(
					$this,
					'add_document_format_dependencies',
				)
			);
			add_filter(
				'ywpi_template_content_options',
				array(
					$this,
					'add_document_template_dependencies',
				)
			);
			add_filter(
				'ywpi_documents_type_options',
				array(
					$this,
					'add_document_type_dependencies',
				)
			);

			add_filter( 'yith_ywpi_style_before_pdf_creation', array( $this, 'additional_style_for_pdf' ), 10, 3 );

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
			$gift_card_total = get_post_meta( yit_get_prop( $order, 'id' ), '_ywgc_applied_gift_cards_totals', true );

			if ( ! empty( $gift_card_total ) ) {
				ob_start();
				?>
				<tr>
					<td class="left-content column-product"><?php esc_html_e( 'Gift card discount:', 'yith-woocommerce-pdf-invoice' ); ?></td>
					<td class="right-content column-total"><?php echo '-' . wp_kses_post( wc_price( $gift_card_total ) ); ?></td>
				</tr>
				<?php
				$res = ob_get_clean();
				echo wp_kses_post( apply_filters( 'yith_pdf_invoice_show_gift_card_amount', $res, $gift_card_total, $order ) );
			}
		}

		/**
		 * Sync new documents with DropBox
		 *
		 * @param object $document The document object.
		 */
		public function sync_to_dropbox( $document ) {
			if ( 'yes' === ywpi_get_option( 'ywpi_dropbox_allow_upload', true, 'no' ) && ywpi_get_option( 'ywpi_dropbox_access_token' ) ) {
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
		 * @author YITH
		 * @return string
		 * @since  1.0.0
		 */
		public function get_document_title( $document ) {

			$is_receipt = get_post_meta( $document->order->get_id(), '_billing_invoice_type', true );

			if ( $document instanceof YITH_Pro_Forma ) {
				$title = apply_filters( 'yith_ywpi_proforma_document_title', esc_html__( 'Proforma Invoice', 'yith-woocommerce-pdf-invoice' ), 10, 1 );

				return $title;
			} elseif ( $document instanceof YITH_Credit_Note ) {
				$title = apply_filters( 'yith_ywpi_credit_note_document_title', esc_html__( 'Credit note', 'yith-woocommerce-pdf-invoice' ), 10, 1 );

				return $title;
			} elseif ( $document instanceof YITH_Invoice && 'receipt' === strval( $is_receipt ) ) {
				$title = apply_filters( 'yith_ywpi_receipt_document_title', esc_html__( 'Receipt', 'yith-woocommerce-pdf-invoice' ), 10, 1 );

				return $title;
			}

			return parent::get_document_title( $document );
		}

		/**
		 * Initialize the plugin options
		 *
		 * @author YITH
		 * @since  1.0.0
		 */
		public function init_plugin_options() {
			$this->enable_pro_forma   = ( 'yes' === strval( ywpi_get_option( 'ywpi_enable_pro_forma' ) ) );
			$this->send_pro_forma     = ( 'yes' === strval( ywpi_get_option( 'ywpi_send_pro_forma' ) ) );
			$this->enable_credit_note = ( 'yes' === strval( ywpi_get_option( 'ywpi_enable_credit_notes' ) ) );

			parent::init_plugin_options();
		}

		/**
		 * Make a local copy of the image to be used as company logo
		 *
		 * @param array $option The array of options.
		 *
		 * @author YITH
		 * @since  1.0.0
		 */
		public function copy_invoice_logo_to_local_path( $option ) {
			if ( 'ywpi_company_logo' === $option['id'] ) {
				if ( ! empty( $_POST['ywpi_company_logo'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification

					$upload_dir = wp_upload_dir();
					$local_path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], sanitize_key( wp_unslash( $_POST['ywpi_company_logo'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
					if ( file_exists( $local_path ) ) {
						copy( $local_path, YITH_YWPI_INVOICE_LOGO_PATH );
					}
				}
			}
		}


		/**
		 * Check for third party plugin compatibility needs
		 *
		 * @author YITH
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
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function add_vat_number_source_option( $settings ) {
			$vat_number_source = array(
				'name'    => esc_html__( 'VAT number source', 'yith-woocommerce-pdf-invoice' ),
				'id'      => 'ywpi_ask_vat_number_source',
				'type'    => 'radio',
				'options' => array(
					'yith'          => 'Show the standard VAT number field from YITH WooCommerce PDF Invoice',
					'eu-vat-number' => 'Use the VAT number field from WooThemes EU VAT number plugin',
				),
				'default' => 'yith',
			);

			$settings['general'] = array_slice( $settings['general'], 0, count( $settings['general'] ) - 2, true ) +
			array( 'vat_number_source' => $vat_number_source ) +
			array_slice( $settings['general'], 3, count( $settings['general'] ) - 1, true );

			return $settings;
		}

		/**
		 * Check if there are action to be done at this moment, related to back end
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function execute_generation_actions() {
			// Check actions from the base version.
			parent::execute_generation_actions();

			// Check generation actions.
			if ( isset( $_GET[ YITH_YWPI_RESET_DROPBOX ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				YITH_PDF_Invoice_DropBox::get_instance()->disable_dropbox_backup();

				wp_redirect( esc_url_raw( remove_query_arg( YITH_YWPI_RESET_DROPBOX ) ) ); //phpcs:ignore
			}
		}

		/**
		 * Add the right action based on GET var current used
		 */
		public function execute_visualization_actions() {
			parent::execute_visualization_actions();

			if ( isset( $_GET[ YITH_YWPI_CREATE_PRO_FORMA_INVOICE_ACTION ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				$this->get_pro_forma_invoice( sanitize_key( wp_unslash( $_GET[ YITH_YWPI_CREATE_PRO_FORMA_INVOICE_ACTION ] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
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
		 * @author YITH
		 * @since  1.0.0
		 */
		public function print_invoice_button( $actions, $order ) {

			$invoice = new YITH_Invoice( yit_get_prop( $order, 'id' ) );

			if ( $invoice->generated() ) {
				// Add the print button.
				$actions['print-invoice'] = array(
					'url'  => $this->get_action_url( 'view', 'invoice', yit_get_prop( $order, 'id' ) ),
					'name' => apply_filters( 'yith_ywpi_print_invoice_name_frontend', esc_html__( 'Invoice', 'yith-woocommerce-pdf-invoice' ) ),
				);
			} elseif ( $this->enable_pro_forma && apply_filters( 'yith_ywpi_show_pro_forma_invoice_button_view_order', true, $order ) ) {

				$actions['print-pro-forma-invoice'] = array(
					'url'  => $this->get_action_url( 'preview', 'proforma', yit_get_prop( $order, 'id' ) ),
					'name' => apply_filters( 'yith_ywpi_pro_forma_button_text_my_account', esc_html__( 'Proforma', 'yith-woocommerce-pdf-invoice' ) ),
				);
			}

			if ( get_option( 'ywpi_enable_credit_notes', 'no' ) == 'yes' ) { //phpcs:ignore
				$refunds = $order->get_refunds();

				foreach ( $refunds as $refund ) {
					$credit_note = new YITH_Credit_Note( yit_get_prop( $refund, 'id' ) );

					if ( $credit_note->generated() ) {
						// Add the print button.
						$actions['print-credit-note'] = array(
							'url'  => $this->get_action_url( 'view', 'credit-note', yit_get_prop( $refund, 'id' ) ),
							'name' => apply_filters( 'yith_ywpi_print_credit_note_name_frontend', esc_html__( 'Credit Note', 'yith-woocommerce-pdf-invoice' ) ),
						);
					}
				}
			}

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
				$attach_to_emails = apply_filters(
					'yith_ywpi_pro_forma_attachment_on_emails_ids',
					array(
						'customer_processing_order',
						'new_order',
						'customer_on_hold_order',
					),
					$order_id
				);

				if ( in_array( $status, $attach_to_emails ) ) { //phpcs:ignore

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

			readfile( $zip_name ); //phpcs:ignore

			exit();
		}

		/**
		 * Set automatically the option "Date to show in the invoice"
		 */
		public function force_update_option_invoice_date_to_show_in_invoice() {

			$force_update_option_invoice_date_to_show_in_invoice = get_option( 'yith_ywpdi_force_update_option_invoice_date_to_show_in_invoice' );

			if ( ! $force_update_option_invoice_date_to_show_in_invoice ) { // Remove NOT logic condition to execute again the process (if the option is already saved).

				if ( ywpi_get_option( 'ywpi_invoice_generation' ) == 'auto' ) { //phpcs:ignore
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
					'enable_credit_note_prefix_sufix',
					'credit_note_prefix',
					'credit_note_suffix',
					'credit_note_number_format',
					'credit_note_filename_format',
				);
				foreach ( $credit_notes_options as $credit_option ) {
					unset( $options['documents'][ $credit_option ] );
				}
			}
			if ( 'yes' !== ywpi_get_option( 'ywpi_enable_packing_slip' ) ) {
				unset( $options['documents']['packing_slip_filename_format'] );
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
			if ( 'yes' !== ywpi_get_option( 'ywpi_enable_credit_notes' ) ) {
				if ( isset( $_REQUEST['page'] ) && 'yith_woocommerce_pdf_invoice_panel' === strval( $_REQUEST['page'] ) && ( ! class_exists( 'YITH_WCPI_Documents_List_Table' ) || ! class_exists( 'YITH_WCPI_Credit_Notes_Table' ) ) ) { // phpcs:ignore
					require_once YITH_YWPI_INC_DIR . 'admin/class-yith-ywpi-documents-list-table.php';
					require_once YITH_YWPI_INC_DIR . 'admin/class-yith-ywpi-credit-notes-list-table.php';

					$args = array(
						'post_type'   => 'shop_order_refund',
						'post_status' => 'any',
						'meta_query'  => array( // phpcs:ignore
							array(
								'key'     => '_ywpi_credit_note',
								'compare' => 'EXISTS',
							),
						),
						'fields'      => 'ids',
					);

					$refunds = get_posts( $args );

					if ( empty( $refunds ) ) {
						unset( $options['documents_type']['documents-type-options']['sub-tabs']['documents_type-credit-notes'] );
					}
				}
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
					$additional_css = '

					';
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
		 * Action links
		 *
		 * @param array $links The actions links.
		 * @return array
		 * @author   YITH
		 */
		public function action_links( $links ) {

			$links = yith_add_action_links( $links, $this->_panel_page, true, YITH_YWPI_SLUG );
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

			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) { //phpcs:ignore
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;
		}
	}
}
