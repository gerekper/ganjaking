<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase, WordPress.Files.FileName.InvalidClassFileName
/**
 * YITH Electronic Invoice class.
 *
 * Handles the electronic invoice document.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\PDFInvoice\Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_Electronic_Invoice' ) ) {

	/**
	 * Enable Module Eletronic Invoice for Italian Customers
	 *
	 * @class   YITH_Electronic_Invoice
	 * @package YITH\PDFInvoice\Classes
	 * @since   1.9.0
	 */
	class YITH_Electronic_Invoice {

		/**
		 * Single instance of the class
		 *
		 * @var object
		 * @since 1.9.0
		 */
		protected static $instance;

		/**
		 * Array of generated documents filename
		 *
		 * @var array
		 */
		private $generated_filenames;

		/**
		 *  Progressive number used to generate the filename
		 *
		 * @var int
		 */
		private $file_id_progressive_number;

		/**
		 * Progressive letter used to generate the filename
		 *
		 * @var string
		 */
		private $file_id_progressive_letter;

		/**
		 * Progressive filename used to generate the filename.
		 *
		 * @var string
		 */
		public $next_progressive_filename;

		/**
		 * Label for the billing receiver ID field.
		 *
		 * @var string
		 */
		public $receiver_id_label;

		/**
		 * Label for the billing receiver PEC field.
		 *
		 * @var string
		 */
		public $receiver_pec_label;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.9.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * YITH_Electronic_Invoice constructor.
		 */
		public function __construct() {
			if ( 'yes' === strval( $this->enable ) ) {
				$this->initialize();
			}
		}

		/**
		 * Initialize all functions of the module
		 */
		private function initialize() {
			/**
			 * Enqueue scripts for checkout process
			 */
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			add_action( 'init', array( $this, 'init_attributes' ), 30 );

			add_action( 'init', array( $this, 'save_counters_for_progressive_file_id' ), 40 );

			/* Serialize and ssave all XML filename in one option */
			add_action( 'init', array( $this, 'save_xml_generated_filename' ), 30 );

			/** Customize checkout and order detail page fields */
			add_filter( 'woocommerce_billing_fields', array( $this, 'customize_billing_fields' ) );
			add_filter( 'woocommerce_admin_billing_fields', array( $this, 'customize_billing_fields' ) );

			/* Add Electronic Invoice fields for edit profile page */
			add_filter( 'woocommerce_customer_meta_fields', array( $this, 'customize_admin_user_fields' ) );

			/* Load billing fields in ajax when create a new order by backend */
			add_filter( 'woocommerce_ajax_get_customer_details', array( $this, 'load_customer_billing_details' ), 10, 3 );

			/* Generate XML documents */
			add_action( 'ywpi_create_document', array( $this, 'create_document' ), 10, 4 );

			/* Increase Progressive File ID */
			add_action( 'yith_ywpi_document_created', array( $this, 'increase_next_progressive_file_id' ), 10, 3 );

			/* Regenerate XML documents */
			add_action( 'ywpi_regenerate_document', array( $this, 'regenerate_document' ), 10, 3 );

			/* Check if Electronic Document has been generated */
			add_filter( 'ywpi_skip_document_generation', array( $this, 'check_if_electronic_document_is_generated' ), 10, 2 );

			/* Set different content type for XML documents */
			add_filter( 'ywpi_file_content_type', array( $this, 'set_content_type_to_open_file' ), 10, 3 );

			/* Filter document save path */
			add_filter( 'ywpi_document_save_path', array( $this, 'filter_document_save_path' ), 10, 4 );

			/* Save Invoice Document Props */
			add_action( 'ywpi_document_save_props', array( $this, 'save_document_props' ), 10, 3 );

			/* Filter date format */
			add_filter( 'ywpi_invoice_date_format', array( $this, 'filter_date_format' ), 10, 2 );

			/* Print View/Create Electronic Invoice button in order detail page */
			add_action( 'yith_ywpi_after_view_pdf_invoice', array( $this, 'print_view_electronic_invoice_button' ) );

			/* Print View/Create Electronic Credit Note */
			add_action( 'yith_ywpi_after_view_pdf_credit_note', array( $this, 'print_view_electronic_credit_note_button' ) );

			/* Show XML information link for each order in main Orders page */
			add_action( 'ywpi_show_invoice_information_link', array( $this, 'show_invoice_information_link' ), 10, 2 );

			/* Create automatically XML documents */
			add_action( 'ywpi_create_automatic_invoice', array( $this, 'create_automatically_document' ) );

			/* Validate Checkout fields */
			add_action( 'woocommerce_after_checkout_validation', array( $this, 'validate_checkout_fields' ), 10, 2 );

			// Set Vat Number and SSN as required or not.
			// add_filter( 'yith_ywpi_vat_number_is_required_option', '__return_false',20 );   .
			// add_filter( 'yith_ywpi_ssn_is_required_option', '__return_false',20 );    .

			/* WooCommerce Product Bundle Compatibility */
			add_filter( 'ywpi_invoce_taxes', array( $this, 'customize_invoce_taxes' ), 10, 2 );

			/* Use EU VAT field when YITH WooCommerce EU VAT is active */
			if ( defined( 'YITH_YWEV_PREMIUM' ) ) {
				add_filter( 'yith_wcpdi_billing_vat_field_id', array( $this, 'replace_billing_vat_field' ) );
			}
		}

		/**
		 * Magic method to recover module options
		 *
		 * @param string $key The key of the electronic invoice to get the option.
		 * @return mixed
		 */
		public function __get( $key ) {
			return get_option( 'ywpi_electronic_invoice_' . $key );
		}

		/**
		 * Enqueue scripts for Electronic Invoice module.
		 */
		public function enqueue_scripts() {
			if ( ! is_checkout() && ! is_account_page() ) {
				return;
			}

			wp_enqueue_script(
				'ywpi_checkout',
				YITH_YWPI_ASSETS_URL . '/js/yith-wc-pdf-invoice-checkout.js',
				array(
					'jquery',
				),
				YITH_YWPI_ENQUEUE_VERSION,
				true
			);

			// APPLY_FILTERS: ywpi_checkout_optional_label | Filter the checkout "(optional)" label. | @param string the label. | @return string.

			// APPLY_FILTERS: ywpi_checkout_receipt_ssn_mandatory | Filter the condition to set the receipt SSN as mandatory. | @param bool True to set it as mandatory, false to not. | @return bool.

			// APPLY_FILTERS: ywpi_validate_ssn_msg_error | Filter the SSN error message. | @param string the message. | @return string.

			// APPLY_FILTERS: ywpi_codice_fiscale_length_error | Filter the "Codice fiscale" lenght error message. | @param string the message. | @return string.

			// APPLY_FILTERS: ywpi_codice_fiscale_char1_error | Filter the "Codice fiscale" invalid characters error message. | @param string the message. | @return string.

			// APPLY_FILTERS: ywpi_codice_fiscale_char2_error | Filter the "Codice fiscale" digits and letters error message. | @param string the message. | @return string.

			// APPLY_FILTERS: ywpi_codice_fiscale_errato | Filter the wrong "Codice fiscale" error message. | @param string the message. | @return string.

			$options = array(
				'billing_vat_field_id'        => $this->get_billing_vat_field_id(),
				'is_ssn_mandatory'            => get_option( 'ask_ssn_number_required', 'no' ),
				'is_vat_mandatory'            => get_option( 'ask_vat_number_required', 'no' ),
				'optional_label'              => apply_filters( 'ywpi_checkout_optional_label', __( '(optional)' ) ),
				'receipt_ssn_mandatory'       => apply_filters( 'ywpi_checkout_receipt_ssn_mandatory', 'false' ),
				'validate_ssn_msg_error'      => apply_filters( 'ywpi_validate_ssn_msg_error', esc_html__( 'The fiscal code entered is incorrect.', 'yith-woocommerce-pdf-invoice' ) ),
				'codice_fiscale_length_error' => apply_filters(
					'ywpi_codice_fiscale_length_error',
					esc_html__( 'The fiscal code length is incorrect: the fiscal code should be exactly 16 characters long.', 'yith-woocommerce-pdf-invoice' )
				),
				'codice_fiscale_char1_error'  => apply_filters(
					'ywpi_codice_fiscale_char1_error',
					esc_html__( 'The fiscal code includes invalid characters.', 'yith-woocommerce-pdf-invoice' )
				),
				'codice_fiscale_char2_error'  => apply_filters(
					'ywpi_codice_fiscale_char2_error',
					esc_html__( 'Only digits and letters are allowed in the fiscal code.', 'yith-woocommerce-pdf-invoice' )
				),
				'codice_fiscale_errato'       => apply_filters(
					'ywpi_codice_fiscale_errato',
					esc_html__( 'The fiscal code is incorrect.', 'yith-woocommerce-pdf-invoice' )
				),
				'validate_ssn'                => apply_filters( 'ywpi_checkout_ssn_validation', 'yes' ),
			);

			wp_localize_script( 'ywpi_checkout', 'ywpi_checkout', $options );
		}

		/**
		 * Get id for checkout field billing vat
		 *
		 * @return mixed|void
		 */
		public function get_billing_vat_field_id() {
			/**
			 * APPLY_FILTERS: yith_wcpdi_billing_vat_field_id
			 *
			 * Filter the ID of the billing VAT field.
			 *
			 * @param string $field_id Field ID
			 *
			 * @return string
			 */
			return apply_filters( 'yith_wcpdi_billing_vat_field_id', 'billing_vat_number' );
		}

		/**
		 * Init the electronic invoice attributes.
		 */
		public function init_attributes() {
			$this->file_id_progressive_number = ywpi_get_option( 'ywpi_electronic_invoice_progressive_file_id_number', 0 );

			$this->file_id_progressive_letter = ywpi_get_option( 'ywpi_electronic_invoice_progressive_file_id_letter', 0 );

			$this->receiver_id_label  = get_option( 'ywpi_electronic_invoice_receiver_id_label', 'Receiver ID' );
			$this->receiver_pec_label = get_option( 'ywpi_electronic_invoice_receiver_pec_label', 'PEC Destinatario' );
		}

		/**
		 * Init counters to use to increase Progressive Filename ID
		 */
		public function save_counters_for_progressive_file_id() {
			if ( ! $this->file_id_progressive_number ) {
				ywpi_update_option( 'ywpi_electronic_invoice_progressive_file_id_number', 0, 0 );
			}

			if ( ! $this->file_id_progressive_letter ) {
				ywpi_update_option( 'ywpi_electronic_invoice_progressive_file_id_letter', 'AA', 0 );
			}
		}

		/**
		 * Increase next progressive File ID to use for the XML filename
		 *
		 * @param object $document The document.
		 * @param int    $order_id Order ID.
		 * @param string $extension Document extension.
		 */
		public function increase_next_progressive_file_id( $document, $order_id, $extension ) {
			$is_create = $document->generated();

			$is_xml = 'xml' === $extension;

			if ( ! $is_create || ! $is_xml ) {
				return;
			}

			if ( 999 === intval( $this->file_id_progressive_number ) ) { // Once the number reaches 9999, increase the letter by one and reset number to 0.
				$this->file_id_progressive_number = 0;
				++$this->file_id_progressive_letter;
			} elseif ( $is_create && $is_xml ) {
				++$this->file_id_progressive_number;
			}

			/**
			 * APPLY_FILTERS: ywpi_next_progressive_file_id
			 *
			 * Filter the next progessive file ID.
			 *
			 * @param int the next progressive file ID.
			 * @param int the actual file ID.
			 *
			 * @return int
			 */
			$next_progressive_file_id = apply_filters( 'ywpi_next_progressive_file_id', $this->get_next_progressive_file_id(), $this->file_id_progressive_number );

			/**
			 * APPLY_FILTERS: ywpi_electronic_invoice_filename
			 *
			 * Filter the electronic invoice filename.
			 *
			 * @param string the file name.
			 * @param int $next_progressive_file_id the next actual file ID.
			 *
			 * @return int
			 */
			$filename = apply_filters( 'ywpi_electronic_invoice_filename', 'IT' . ywpi_get_option( 'ywpi_electronic_invoice_transmitter_id', 0 ) . '_' . $next_progressive_file_id, $next_progressive_file_id );

			if ( $this->filename_exists( $filename ) ) {
				self::increase_next_progressive_file_id();
			}

			ywpi_update_option( 'ywpi_electronic_invoice_progressive_file_id_number', $this->file_id_progressive_number, 0 );
			ywpi_update_option( 'ywpi_electronic_invoice_progressive_file_id_letter', $this->file_id_progressive_letter, 0 );
		}

		/**
		 * Get progressive file ID
		 *
		 * @return string
		 */
		public function get_next_progressive_file_id() {
			return $this->file_id_progressive_letter . sprintf( '%03d', $this->file_id_progressive_number );
		}

		/**
		 * Get new progressive filename
		 *
		 * @return string
		 */
		public function get_next_progressive_filename() {
			$filename = apply_filters( 'ywpi_electronic_invoice_filename', 'IT' . ywpi_get_option( 'ywpi_electronic_invoice_transmitter_id', 0 ) . '_' . $this->get_next_progressive_file_id(), $this->get_next_progressive_file_id() );

			return $filename;
		}

		/**
		 * Save an option which contains all olderst filename already generated, before to switch to alphanumer progressive unique id
		 */
		public function save_xml_generated_filename() {
			global $wpdb;

			/* Check if XML Invoices has been generated and if the procedure of update has been already processed */
			$this->generated_filenames = get_option( 'ywpi_electronic_invoice_generated_filename' );
			$documents                 = $wpdb->get_results( "SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = '_ywpi_xml_path'", ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

			if ( ( count( $documents ) > 0 ) & ! $this->generated_filenames ) {
				$documents_filename = array();

				foreach ( $documents as $document ) {
					$documents_filename[] = basename( $document['meta_value'], '.xml' );
				}

				update_option( 'ywpi_electronic_invoice_generated_filename', serialize( $documents_filename ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
			}
		}

		/**
		 * Check if a document has already generated with provided filename
		 *
		 * @param string $filename The filename.
		 * @return bool
		 */
		public function filename_exists( $filename ) {
			$exists = false;

			$generated_filenames = unserialize( $this->generated_filenames ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize

			if ( is_array( $generated_filenames ) && in_array( $filename, $generated_filenames, true ) ) {
				$exists = true;
			}

			return $exists;
		}

		/**
		 * Add Receiver ID and PEC fields to WooCommerce default fields
		 *
		 * @param array $fields Array with the default WooCommerce fields.
		 * @return mixed
		 */
		public function customize_billing_fields( $fields ) {
			$current_action    = current_action();
			$key_receiver_id   = 'woocommerce_billing_fields' === strval( $current_action ) ? 'billing_receiver_id' : 'receiver_id';
			$key_receiver_pec  = 'woocommerce_billing_fields' === strval( $current_action ) ? 'billing_receiver_pec' : 'receiver_pec';
			$key_receiver_type = 'woocommerce_billing_fields' === strval( $current_action ) ? 'billing_receiver_type' : 'receiver_type';

			/**
			 * APPLY_FILTERS: ywpi_show_receiver_id_field
			 *
			 * Filter the condition to show the "Receiver ID" field at checkout for the electronic invoices.
			 *
			 * @param bool true to show it, false to not.
			 *
			 * @return bool
			 */
			if ( apply_filters( 'ywpi_show_receiver_id_field', true ) ) {
				$fields[ $key_receiver_id ] = array(
					'label'        => YITH_Electronic_Invoice()->receiver_id_label,
					'required'     => false,
					'class'        => 'woocommerce_admin_billing_fields' === strval( $current_action ) ? '' : array( 'form-row-wide' ),
					'autocomplete' => 'given-name',
					'priority'     => 90,
				);
			}

			/**
			 * APPLY_FILTERS: ywpi_show_receiver_pec_field
			 *
			 * Filter the condition to show the "PEC" field at checkout for the electronic invoices.
			 *
			 * @param bool true to show it, false to not.
			 *
			 * @return bool
			 */
			if ( apply_filters( 'ywpi_show_receiver_pec_field', true ) ) {
				$fields[ $key_receiver_pec ] = array(
					'label'        => YITH_Electronic_Invoice()->receiver_pec_label,
					'required'     => false,
					'class'        => 'woocommerce_admin_billing_fields' === strval( $current_action ) ? '' : array( 'form-row-wide' ),
					'autocomplete' => 'given-name',
					'priority'     => 100,
					'validate'     => array( 'email' ),
					'type'         => 'email',
				);
			}

			// APPLY_FILTERS: ywpi_receiver_type_field_label | Filter the "Customer type" label at checkout for italian customers. | @param string the label. | @return string.

			// APPLY_FILTERS: ywpi_receiver_type_field_private_label | Filter the "private" option label in the "Customer type" option for italian customers. | @param string the label. | @return string.

			// APPLY_FILTERS: ywpi_receiver_type_field_freelance_label | Filter the "freelance" option label in the "Customer type" option for italian customers. | @param string the label. | @return string.

			// APPLY_FILTERS: ywpi_receiver_type_field_company_label | Filter the "company" option label in the "Customer type" option for italian customers. | @param string the label. | @return string.

			$fields[ $key_receiver_type ] = array(
				'label'        => apply_filters( 'ywpi_receiver_type_field_label', get_option( 'ywpi_electronic_invoice_receiver_type', 'Tipologia utente' ) ),
				'class'        => 'woocommerce_admin_billing_fields' === strval( $current_action ) ? '' : array( 'form-row-wide' ),
				'autocomplete' => 'given-name',
				'priority'     => 21,
				'required'     => true,
				'type'         => 'woocommerce_admin_billing_fields' === strval( $current_action ) ? 'select' : 'radio',
				'options'      => array(
					'private'   => apply_filters( 'ywpi_receiver_type_field_private_label', esc_html__( 'Private', 'yith-woocommerce-pdf-invoice' ) ),
					'freelance' => apply_filters( 'ywpi_receiver_type_field_freelance_label', esc_html__( 'Freelance', 'yith-woocommerce-pdf-invoice' ) ),
					'company'   => apply_filters( 'ywpi_receiver_type_field_company_label', esc_html__( 'Company', 'yith-woocommerce-pdf-invoice' ) ),
				),
				'default'      => 'private',
			);

			return $fields;
		}

		/**
		 * Retrieve the invoice document number
		 *
		 * @param int    $current_invoice_number The current invoice number.
		 * @param object $order The order object.
		 * @param object $document The document object.
		 * @return mixed|string
		 */
		public function set_invoice_number_for_xml_documents( $current_invoice_number, $order, $document ) {
			if ( $document instanceof YITH_XML ) {
				$invoice = ywpi_get_invoice( $order->get_id() );

				if ( null !== $invoice->number ) {
					$current_invoice_number = $invoice->number;
				}
			} elseif ( $document instanceof YITH_Invoice ) {
				$invoice_number = $document->order->get_meta( 'ywpi_invoice_number' );

				if ( $invoice_number ) {
					$current_invoice_number = $invoice_number;
				}
			}

			return $current_invoice_number;
		}

		/**
		 * Print metabox for electronic invoices
		 *
		 * @param object $order The order object.
		 */
		public function print_view_electronic_invoice_button( $order ) {
			$is_receipt = $order->get_meta( '_billing_invoice_type' );

			if ( 'receipt' === strval( $is_receipt ) ) {
				return;
			}

			$is_generated = $order->get_meta( '_ywpi_has_xml' );

			/**
			 * APPLY_FILTERS: yith_ywpi_hide_electronic_invoice_button
			 *
			 * Filter the condition to hide the option to create the electronic invoice in the order edit page.
			 *
			 * @param bool true to hide it, false to not.
			 *
			 * @return bool
			 */
			if ( apply_filters( 'yith_ywpi_hide_electronic_invoice_button', false, $order ) ) {
				return;
			}

			if ( ! $is_generated ) {
				$action = 'create';
				$label  = esc_html__( 'Create XML', 'yith-woocommerce-pdf-invoice' );
			} else {
				$action = 'view';
				$label  = esc_html__( 'XML', 'yith-woocommerce-pdf-invoice' );
			}

			?>
			<a
				<?php
				if ( 'open_tab' === ywpi_get_option( 'ywpi_pdf_invoice_behaviour' ) ) {
					echo 'target="_blank"';
				}
				?>
				class="button tips ywpi_view_xml" data-tip="<?php esc_html_e( 'XML', 'yith-woocommerce-pdf-invoice' ); ?>" href=" <?php echo esc_url( YITH_PDF_Invoice()->get_action_url( $action, 'invoice', $order->get_id(), 'xml' ) ); ?>">
				<?php echo esc_html( $label ); ?>
			</a>
			<?php
		}

		/**
		 * Show Create/View credit note for Electronic Documents
		 *
		 * @param object $post The post object.
		 */
		public function print_view_electronic_credit_note_button( $post ) {
			$is_receipt = $post->get_meta( '_billing_invoice_type' );

			if ( 'receipt' === strval( $is_receipt ) ) {
				return;
			}

			$is_generated = $post->get_meta( '_ywpi_xml_credit_note' );

			if ( apply_filters( 'yith_ywpi_hide_electronic_invoice_button', false, $post ) ) {
				return;
			}

			if ( ! $is_generated ) {
				$action = 'create';
				$label  = esc_html__( 'Create XML', 'yith-woocommerce-pdf-invoice' );
			} else {
				$action = 'view';
				$label  = esc_html__( 'XML', 'yith-woocommerce-pdf-invoice' );
			}

			?>
			<a
				<?php
				if ( 'open_tab' === ywpi_get_option( 'ywpi_pdf_invoice_behaviour' ) ) {
					echo 'target="_blank"';
				}
				?>
				class="button tips ywpi_view_xml" data-tip="<?php esc_html_e( 'XML', 'yith-woocommerce-pdf-invoice' ); ?>" href=" <?php echo esc_url( YITH_PDF_Invoice()->get_action_url( $action, 'credit-note', $post->get_id(), 'xml' ) ); ?>">
				<?php echo esc_html( $label ); ?>
			</a>
			<?php
		}

		/**
		 * Show Invoice information link in orders page.
		 *
		 * @param object $invoice The invoice document.
		 * @param object $order The order object.
		 */
		public function show_invoice_information_link( $invoice, $order ) {
			$url = YITH_PDF_Invoice()->get_action_url( 'view', 'invoice', $order->get_id(), 'xml' );

			if ( $order->get_meta( '_ywpi_has_xml' ) ) {
				?>
				<a class="meta ywpi-invoice-information" target="_blank" href="<?php echo esc_url( $url ); ?>" title="<?php esc_html_e( 'View Invoice', 'yith-woocommerce-pdf-invoice' ); ?>">
					<?php
					// translators: %s is the invoice number.
					printf( esc_html__( 'View Invoice No. %s (XML)', 'yith-woocommerce-pdf-invoice' ), wp_kses_post( $invoice->get_formatted_document_number() ) );
					?>
				</a>
				<?php
			}
		}

		/**
		 * Set content type to open correctly xml file
		 *
		 * @param string $content_type The context type of the document (text/xml).
		 * @param string $document The resource.
		 * @param string $extension The extension of the file.
		 * @return string
		 */
		public function set_content_type_to_open_file( $content_type, $document, $extension ) {
			if ( 'xml' === strval( $extension ) ) {
				$content_type = 'Content-type: text/xml';
			}

			return $content_type;
		}

		/**
		 * Filter Document Save Path for Electronic Invoices
		 *
		 * @param string $save_path The path where is saved the document.
		 * @param string $extension The extension of the document.
		 * @param int    $order_id The order id.
		 * @param object $document The document object.
		 * @return mixed
		 */
		public function filter_document_save_path( $save_path, $extension, $order_id, $document ) {
			$order = wc_get_order( $order_id );

			if ( 'xml' === strval( $extension ) ) {
				if ( $document instanceof YITH_Invoice ) {
					$save_path = $order->get_meta( '_ywpi_xml_path' );
				} elseif ( $document instanceof YITH_Credit_Note ) {
					$save_path = $order->get_meta( '_ywpi_xml_credit_note_path' );
				}
			}

			return $save_path;
		}

		/**
		 * Create document action for Electronic Documents
		 *
		 * @param object $document The document object.
		 * @param int    $order_id The order id.
		 * @param string $type The document type.
		 */
		public function create_document( $document, $order_id, $type ) {
			if ( 'invoice' === $type || 'credit-note' === $type ) {
				$document->create_document( $order_id, $type, 'xml' );
			}
		}

		/**
		 * Regenerate document for Electronic Invoice
		 *
		 * @param object $document The document object.
		 * @param int    $order_id The order id.
		 * @param string $type The document type.
		 */
		public function regenerate_document( $document, $order_id, $type ) {
			if ( 'invoice' === $type || 'credit-note' === $type ) {
				$document->regenerate_document( $order_id, $type, 'xml' );
			}
		}

		/**
		 * Check if Electronic Invoice has been generated
		 *
		 * @param boolean $skip True or False if it has been generated.
		 * @param int     $order_id The order id.
		 * @return bool
		 */
		public function check_if_electronic_document_is_generated( $skip, $order_id ) {
			$order = wc_get_order( $order_id );

			$skip = $order->get_meta( '_ywpi_has_xml' ) ? true : false;

			return $skip;
		}

		/**
		 * Save document props whene elctronic invoice is generated
		 *
		 * @param object $document The document object.
		 * @param object $order The order object.
		 * @param string $extension The document extension.
		 * @return void
		 */
		public function save_document_props( $document, $order, $extension ) {
			if ( 'xml' === strval( $extension ) ) {
				if ( $document instanceof YITH_Invoice ) {
					$order->update_meta_data( '_ywpi_has_xml', true );
					$order->update_meta_data( '_ywpi_xml_path', sprintf( '%s.%s', YITH_PDF_Invoice()->get_document_filename( $document, $extension ), $extension ) );

					// TODO remove it in the future when the HPOS sync works correctly.
					update_post_meta( $order->get_id(), '_ywpi_has_xml', true );
					update_post_meta( $order->get_id(), '_ywpi_xml_path', sprintf( '%s.%s', YITH_PDF_Invoice()->get_document_filename( $document, $extension ), $extension ) );

					$order->save();
				} elseif ( $document instanceof YITH_Credit_Note ) {
					$order->update_meta_data( '_ywpi_xml_credit_note', true );
					$order->update_meta_data( '_ywpi_xml_credit_note_path', sprintf( '%s.%s', YITH_PDF_Invoice()->get_document_filename( $document, $extension ), $extension ) );

					// TODO remove it in the future when the HPOS sync works correctly.
					update_post_meta( $order->get_id(), '_ywpi_xml_credit_note', true );
					update_post_meta( $order->get_id(), '_ywpi_xml_credit_note_path', sprintf( '%s.%s', YITH_PDF_Invoice()->get_document_filename( $document, $extension ), $extension ) );

					$order->save();
				}
			}
		}

		/**
		 * Filter date format as Y-m-d
		 *
		 * @param string $date_format The date format.
		 * @param string $extension The document extension.
		 * @return string
		 */
		public function filter_date_format( $date_format, $extension = 'pdf' ) {
			if ( 'xml' === strval( $extension ) ) {
				$date_format = 'Y-m-d';
			}

			return $date_format;
		}

		/**
		 * Create automatically document
		 *
		 * @param int $order_id The order id.
		 */
		public function create_automatically_document( $order_id ) {
			$order = wc_get_order( $order_id );

			$is_receipt = $order->get_meta( '_billing_invoice_type' );

			if ( 'receipt' === strval( $is_receipt ) ) {
				return;
			}

			YITH_PDF_Invoice()->create_document( $order_id, 'invoice', 'xml' );
		}

		/**
		 * Validate checkout fields ( Billing Receiver ID and Billing Receiver PEC )
		 *
		 * @param  array    $data   An array of posted data.
		 * @param  WP_Error $errors Validation errors.
		 */
		public function validate_checkout_fields( $data, $errors ) {
			$is_receipt_enabled = ywpi_get_option( 'ywpi_enable_receipts', null, 'no' );

			$is_ssn_mandatory = apply_filters( 'yith_ywpi_ssn_is_required_option', 'yes' ) === get_option( 'ask_ssn_number_required', 'no' );

			/**
			 * APPLY_FILTERS: yith_ywpi_validate_checkout_fields_conditions
			 *
			 * Filter the condition to validate the checkout fields.
			 *
			 * @param bool true to validate, false to not.
			 *
			 * @return bool
			 */
			if ( apply_filters( 'yith_ywpi_validate_checkout_fields_conditions', true, $data ) ) {
				if ( in_array( $data['billing_receiver_type'], array( 'freelance', 'company' ), true ) ) {
					if ( 'IT' === strval( $data['billing_country'] ) ) {
						if ( '' === $data['billing_receiver_id'] && isset( $data['billing_receiver_pec'] ) && '' === $data['billing_receiver_pec'] ) {
							$message = $this->receiver_mandatory_id_pec_message;
							$errors->add( 'validation', $message );
						}

						if ( '' !== $data['billing_receiver_id'] & strlen( (string) $data['billing_receiver_id'] ) !== 7 ) {
							$message = $this->receiver_wrong_id_message;
							$errors->add( 'validation', $message );
						}

						if ( '' === $data[ $this->get_billing_vat_field_id() ] ) {
							$message = $this->receiver_mandatory_vat_message;
							$errors->add( 'validation', $message );
						}

						if ( '' === $data['billing_company'] ) {
							$message = $this->receiver_mandatory_company_message;
							$errors->add( 'validation', $message );
						}
					}
				} elseif ( 'IT' === $data['billing_country'] && isset( $data['billing_vat_ssn'] ) && ( ( ( 'yes' === $is_receipt_enabled && 'invoice' === $data['billing_invoice_type'] ) ) || 'no' === $is_receipt_enabled ) ) {
					// Check if SSN is valid.
					if ( ! '' === $data['billing_vat_ssn'] && 'organization' !== $data['billing_receiver_type'] && ! preg_match( '/^[A-Z]{6}\d{2}[A-Z]\d{2}[A-Z]\d{3}[A-Z]$/i', $data['billing_vat_ssn'] ) ) {
						$message = $this->receiver_wrong_ssn_message;
						$errors->add( 'validation', $message );
					} elseif ( '' === $data['billing_vat_ssn'] ) {
						$message = $this->receiver_mandatory_ssn_message;
						$errors->add( 'validation', $message );
					}
				}
			}
		}

		/**
		 * Allow editing of Receiver ID and Receiver PEC editing user profile by backend
		 *
		 * @param array $meta_fields The array of meta fields.
		 * @return mixed
		 */
		public function customize_admin_user_fields( $meta_fields ) {
			$meta_fields['billing']['fields']['billing_receiver_id']   = array(
				'label'       => 'Codice Univoco (fatturazione elettronica)',
				'description' => '',
			);
			$meta_fields['billing']['fields']['billing_receiver_pec']  = array(
				'label'       => 'PEC (fatturazione elettronica)',
				'description' => '',
			);
			$meta_fields['billing']['fields']['billing_receiver_type'] = array(
				'label'       => 'Tipologia utente (fatturazione elettronica)',
				'description' => '',
			);

			return $meta_fields;
		}

		/** Loading in ajax of Receiver ID and Receiver PEC on user profile when an order is created manually by backend
		 *
		 * @param array  $data array with all user fields.
		 * @param object $customer object customer.
		 * @param int    $user_id the user id user_id.
		 * @return mixed array
		 */
		public function load_customer_billing_details( $data, $customer, $user_id ) {
			$data['billing']['receiver_id']   = get_user_meta( $user_id, 'billing_receiver_id', true );
			$data['billing']['receiver_pec']  = get_user_meta( $user_id, 'billing_receiver_pec', true );
			$data['billing']['receiver_type'] = get_user_meta( $user_id, 'billing_receiver_type', true );

			return $data;
		}

		/**
		 * Get billing receiver ID (if the country is IT, the value can be filled or set as 0000000 if customer filled
		 * the PEC Email field. For not ITA customers field is set as XXXXXXX
		 *
		 * @param object $document The document object.
		 * @return string
		 */
		public function get_billing_receiver_id( $document ) {
			if ( $document->order instanceof WC_Order_Refund ) {
				$order = wc_get_order( $document->order->get_parent_id() );
			} else {
				$order = $document->order;
			}

			/* private */
			$billing_country = $order->get_billing_country();

			if ( $order->get_meta( '_billing_receiver_id' ) === '' || $this->is_private( $order ) ) {
				$billing_receiver_id = 'IT' !== $billing_country ? 'XXXXXXX' : '0000000';
			} else {
				$billing_receiver_id = strtoupper( $order->get_meta( '_billing_receiver_id' ) );
			}

			/* per aziende codce destinatario o pec */

			return $billing_receiver_id;
		}

		/**
		 * Get billing VAT/SSN value
		 *
		 * @param object $document The document object.
		 * @return string
		 */
		public function get_billing_vat_ssn( $document ) {
			if ( $document->order instanceof WC_Order_Refund ) {
				$order = wc_get_order( $document->order->get_parent_id() );
			} else {
				$order = $document->order;
			}

			$billing_vat_ssn = '';

			$billing_country = $order->get_billing_country();

			if ( $this->is_private( $order ) ) {
				if ( 'IT' === $billing_country ) {
					$billing_vat_ssn = $order->get_meta( '_billing_vat_ssn' );
				} else {
					$billing_vat_ssn = '0000000';
				}
			} elseif ( $this->is_eur_country( $billing_country ) ) {
				$billing_vat_ssn = $order->get_meta( '_billing_vat_ssn' );
			} else {
				$billing_vat_ssn = 'OO99999999999';
			}

			return $billing_vat_ssn;
		}

		/**
		 *  Get billing vat number
		 *
		 * @param object $document The document object.
		 * @return string
		 */
		public function get_billing_vat_number( $document ) {
			if ( $document->order instanceof WC_Order_Refund ) {
				$order = wc_get_order( $document->order->get_parent_id() );
			} else {
				$order = $document->order;
			}

			if ( '' === $order->get_meta( '_' . $this->get_billing_vat_field_id() ) ) {
				$billing_vat_number = '99999999999';
			} else {
				$billing_vat_number = $order->get_meta( '_' . $this->get_billing_vat_field_id() );
			}

			return $billing_vat_number;
		}

		/**
		 *  Recover dinamically the payment method
		 *
		 * @param object $document The document object.
		 * @return string
		 */
		public function get_payment_method( $document ) {
			if ( $document->order instanceof WC_Order_Refund ) {
				$order = wc_get_order( $document->order->get_parent_id() );
			} else {
				$order = $document->order;
			}

			switch ( $order->get_payment_method() ) {
				case 'bacs':
					$payment_method = 'MP05';
					break;

				case 'cheque':
					$payment_method = 'MP02';
					break;

				case 'cod':
					$payment_method = 'MP01';
					break;

				default:
					$payment_method = 'MP08';
			}

			return $payment_method;
		}

		/**
		 * Get discount applied for each item
		 *
		 * @param object $item The item object.
		 * @param string $number_format The number format.
		 * @param int    $quantity The quantity.
		 * @return string
		 */
		public function get_discount_increment( $item, $number_format = false, $quantity = 1 ) {
			$discount = ( $item->get_subtotal() - $item->get_total() ) / $quantity;

			return $number_format ? number_format( $discount, 8, '.', '' ) : $discount;
		}

		/**
		 *  Get item price
		 *
		 * @param object $item The item object.
		 * @param string $number_format The number format.
		 * @return float|int|mixed|string
		 */
		public function get_order_item_price( $item, $number_format = false ) {
			$price = 0;
			$qty   = $item->get_quantity();

			if ( $qty > 0 ) {
				$price = ( $item instanceof WC_Order_Item_Fee || $item instanceof WC_Order_Item_Shipping ) ? $item['total'] : $item['subtotal'] / $item->get_quantity();

				if ( '' === $price ) {
					$price = 0;
				}
			} else {
				$subtotal = abs( $item->get_subtotal() );
				$qty      = 0 === abs( $qty ) ? 1 : abs( $qty );
				$price    = $subtotal / $qty;
			}

			return $number_format ? number_format( $price, 2, '.', '' ) : $price;
		}

		/**
		 * Get the order item name.
		 *
		 * @param object $item The item object.
		 * @return string
		 */
		public function get_order_item_name( $item ) {
			/**
			 * APPLY_FILTERS: yith_ywpi_item_name_xml
			 *
			 * Filter the order item name for the XML file.
			 *
			 * @param string the item name.
			 * @param array the item.
			 *
			 * @return string
			 */
			return apply_filters( 'yith_ywpi_item_name_xml', $this->encode_text( $item['name'] ), $item );
		}

		/**
		 * Check if the receiver type is private.
		 *
		 * @param  mixed $order The order object.
		 * @return boolean
		 */
		public function is_private( $order ) {
			return $order->get_meta( '_billing_receiver_type' ) === 'private' || ( $order->get_meta( '_billing_receiver_type' ) === '' && $order->get_meta( '_billing_company' ) !== '' ) ? true : false;
		}

		/**
		 * Encode special characters
		 *
		 * @param string $text The text.
		 * @return string
		 */
		public function encode_text( $text ) {
			$replace_to    = array( '€', '&', 'à', 'À', '’' );
			$replace_      = array( 'euro', 'e', 'a', 'A', ' ' );
			$replaced_text = str_replace( $replace_to, $replace_, $text );
			$final_text    = html_entity_decode( $replaced_text );

			/**
			 * APPLY_FILTERS: yith_ywpi_encode_text
			 *
			 * Filter the encode text return for the electronic invoice.
			 *
			 * @param string $final_textthe the encoded text.
			 * @param string $text the original text.
			 *
			 * @return string
			 */
			return apply_filters( 'yith_ywpi_encode_text', $final_text, $text );
		}

		/**
		 * Get item quantity
		 *
		 * @param object $item The item object.
		 * @param object $document The document object.
		 * @param string $number_format The number format.
		 * @return float|int|string
		 */
		public function get_item_quantity( $item, $document, $number_format = false ) {
			if ( $document instanceof YITH_Invoice ) {
				$quantity = $item->get_quantity();
			} else {
				$quantity = $item->get_quantity() === 0 ? 1 : abs( $item->get_quantity() );
			}

			return $number_format ? number_format( $quantity, 2, '.', '' ) : $quantity;
		}

		/**
		 * Customize Invoice Taxes
		 *
		 * @param array   $taxes Array of the taxes.
		 * @param boolean $bundle_exists Boolean checking if bundle exists.
		 * @return mixed
		 */
		public function customize_invoce_taxes( $taxes, $bundle_exists ) {
			if ( $bundle_exists ) {
				if ( ! array_key_exists( '0.00', $taxes ) ) {
					$taxes['0.00'] = array(
						'total'     => '0.00',
						'total_tax' => '0.00',
					);
				}
			}

			return $taxes;
		}

		/**
		 * Get progressive id saved for document.
		 *
		 * @param object $order The order object.
		 * @return mixed
		 */
		public function get_progressive_id_saved_for_document( $order ) {
			$save_path = $order->get_meta( '_ywpi_xml_path' );

			return str_replace( '.xml', '', $save_path );
		}

		/**
		 * Get the id code (SSN).
		 *
		 * @return int
		 */
		public function get_id_codice() {
			$id = empty( ywpi_get_option( 'ywpi_electronic_invoice_third_intermediary_ssn' ) ) ? ywpi_get_option( 'ywpi_electronic_invoice_transmitter_id' ) : ywpi_get_option( 'ywpi_electronic_invoice_third_intermediary_ssn' );

			return $id;
		}

		/**
		 * Get natura
		 *
		 * @param object $order The order object.
		 * @return mixed
		 */
		public function get_natura( $order ) {
			$order_natura_custom = $order->get_meta( 'yith_wcpdi_natura' );

			if ( $order_natura_custom && '' !== $order->get_meta( 'yith_wcpdi_natura' ) ) {
				$natura = $order->get_meta( 'yith_wcpdi_natura' );
			} else {
				$natura  = ywpi_get_option( 'ywpi_electronic_invoice_natura', 'N4' );
				$country = $order->get_billing_country();

				if ( $this->is_eur_country( $country ) ) {
					if ( strtolower( $order->get_billing_city() ) === 'livigno' ) {
						$natura = 'N2.1';
					} elseif ( 'SM' === strval( $country ) ) {
						$natura = 'N3.3';
					} elseif ( 'IT' !== strval( $country ) ) {
						$natura = 'N3.2';
					}
				} else {
					$natura = 'N2.1';
				}
			}

			return $natura;
		}

		/**
		 * Check if is a European country.
		 *
		 * @param  string $country The country selected.
		 * @return boolean
		 */
		public function is_eur_country( $country ) {
			$eur_countries = array(
				'AT', // Austria.
				'BE', // Belgium.
				'EE', // Estonia.
				'FI', // Finlandia.
				'FR', // Francia.
				'DE', // Germania.
				'GR', // Grecia.
				'CY', // Cipro.
				'IE', // Irlanda.
				'IT', // Italia.
				'LV', // Lettonia.
				'LT', // Lituania.
				'LU', // Lussenburgo.
				'MT', // Malta.
				'NL', // Paesi Bassi.
				'PT', // Portogallo.
				'SK', // Slovacchia.
				'SI', // Slovenia.
				'ES', // Spagna.
			);
			return in_array( $country, $eur_countries, true ) ? true : false;
		}

		/**
		 * Get the invoice detals.
		 *
		 * @param  object $document The document object.
		 * @return array
		 */
		public function get_invoice_details( $document ) {
			$is_refund = $document->order instanceof WC_Order_Refund ? true : false;

			$main_order = $is_refund ? wc_get_order( $document->order->get_parent_id() ) : null;

			$customer_billing_data = $is_refund ? $main_order->get_data()['billing'] : $document->order->get_data()['billing'];

			$parent_invoice = $is_refund ? new YITH_Invoice( $document->order->get_parent_id() ) : null;

			/**
			 * APPLY_FILTERS: yith_ywpi_get_order_items_for_invoice
			 *
			 * Filter the order items to show in the invoice.
			 *
			 * @param array the order items.
			 * @param object the order object.
			 *
			 * @return array
			 */
			$order_items = apply_filters( 'yith_ywpi_get_order_items_for_invoice', $document->order->get_items(), $document->order );

			/**
			 * APPLY_FILTERS: yith_ywpi_get_order_fee_for_invoice
			 *
			 * Filter the order fee to show in the invoice.
			 *
			 * @param array the order fee.
			 * @param object the order object.
			 *
			 * @return array
			 */
			$fee = apply_filters( 'yith_ywpi_get_order_fee_for_invoice', $document->order->get_items( 'fee' ), $document->order );

			/**
			 * APPLY_FILTERS: yith_ywpi_get_order_shipping_for_invoice
			 *
			 * Filter the order shipping to show in the invoice.
			 *
			 * @param array the order shipping.
			 * @param object the order object.
			 *
			 * @return array
			 */
			$shipping = apply_filters( 'yith_ywpi_get_order_shipping_for_invoice', $document->order->get_items( 'shipping' ), $document->order );

			$order_items = array_merge( $order_items, $fee, $shipping );

			/**
			 * APPLY_FILTERS: yith_ywpi_formatted_number
			 *
			 * Filter the invoice formatted number.
			 *
			 * @param string the formatted number.
			 * @param object $document the document object.
			 *
			 * @return string
			 */
			$formatted_number = apply_filters( 'yith_ywpi_formatted_number', $document->formatted_number, $document );

			return array(
				'is_refund'            => $is_refund,
				'main_order'           => $main_order,
				'customer'             => array(
					'billing_country'    => $is_refund ? $main_order->get_billing_country() : $document->order->get_billing_country(),
					'billing_company'    => $is_refund ? $this->encode_text( $main_order->get_billing_company() ) : $this->encode_text( $document->order->get_billing_company() ),
					'is_private'         => $is_refund ? $this->is_private( $main_order ) : $this->is_private( $document->order ),
					'billing_id'         => $this->get_billing_receiver_id( $document ),
					'billing_pec'        => $is_refund ? $main_order->get_meta( '_billing_receiver_pec' ) : $document->order->get_meta( '_billing_receiver_pec' ),
					'billing_vat_number' => $this->get_billing_vat_number( $document ),
					'billing_ssn'        => $this->get_billing_vat_ssn( $document ),
					'billing_first_name' => $this->encode_text( $customer_billing_data['first_name'] ),
					'billing_last_name'  => $this->encode_text( $customer_billing_data['last_name'] ),
					'billing_address_1'  => $this->encode_text( $customer_billing_data['address_1'] ),
					'billing_postcode'   => $this->encode_text( $customer_billing_data['postcode'] ),
					'billing_city'       => $this->encode_text( $customer_billing_data['city'] ),
					'billing_state'      => $this->encode_text( $customer_billing_data['state'] ),
					'pec'                => $is_refund ? $main_order->get_meta( '_billing_receiver_pec' ) : $document->order->get_meta( '_billing_receiver_pec' ),
				),
				'transmitter'          => array(
					'country_id'      => ywpi_get_option( 'ywpi_electronic_invoice_country_id', $document, 'IT' ),
					'ssn'             => ywpi_get_option( 'ywpi_electronic_invoice_transmitter_id', $document ),
					'vat'             => ywpi_get_option( 'ywpi_electronic_invoice_company_vat', $document ),
					'registered_name' => $this->encode_text( ywpi_get_option( 'ywpi_electronic_invoice_company_registered_name', $document ) ),
					'fiscal_regime'   => ywpi_get_option( 'ywpi_electronic_invoice_fiscal_regime', $document ),
					'address'         => $this->encode_text( ywpi_get_option( 'ywpi_electronic_invoice_company_address', $document ) ),
					'cap'             => ywpi_get_option( 'ywpi_electronic_invoice_company_cap', $document ),
					'city'            => ywpi_get_option( 'ywpi_electronic_invoice_company_city', $document ),
					'province'        => ywpi_get_option( 'ywpi_electronic_invoice_company_province', $document ),
					'name'            => ywpi_get_option( 'ywpi_electronic_invoice_transmitter_name', $document ),
					'lastname'        => ywpi_get_option( 'ywpi_electronic_invoice_transmitter_lastname', $document ),
					'phone'           => ywpi_get_option( 'ywpi_electronic_invoice_company_phone', $document ),
					'email'           => ywpi_get_option( 'ywpi_electronic_invoice_company_email', $document ),
				),
				'third_intermediary'   => array(
					'enable'          => ywpi_get_option( 'ywpi_electronic_invoice_third_intermediary', $document, 'no' ),
					'vat'             => ywpi_get_option( 'ywpi_electronic_invoice_third_intermediary_vat', $document ),
					'country'         => ywpi_get_option( 'ywpi_electronic_invoice_third_intermediary_country', $document ),
					'ssn'             => ywpi_get_option( 'ywpi_electronic_invoice_third_intermediary_ssn', $document ),
					'registered_name' => ywpi_get_option( 'ywpi_electronic_invoice_third_intermediary_registred_name', $document ),
					'name'            => ywpi_get_option( 'ywpi_electronic_invoice_third_intermediary_name', $document ),
					'lastname'        => ywpi_get_option( 'ywpi_electronic_invoice_third_intermediary_lastname', $document ),
					'qualification'   => ywpi_get_option( 'ywpi_electronic_invoice_third_intermediary_qualification', $document ),
					'codeori'         => ywpi_get_option( 'ywpi_electronic_invoice_third_intermediary_codeori', $document ),
				),
				'idcodice'             => $this->get_id_codice(),
				'transmission_format'  => 'FPR12',
				'formatted_number'     => $formatted_number,
				'reason'               => $is_refund ? $document->order->get_reason() : $document->order->get_meta( 'reason' ),
				'document_type'        => $is_refund ? 'TD04' : 'TD01',
				'order_currency'       => $document->order->get_currency(),
				'order_number'         => $is_refund ? $main_order->get_order_number() : $document->order->get_order_number(),
				'document_date'        => $document->get_formatted_document_date( 'xml' ),
				'order_date'           => $is_refund ? $main_order->get_date_created()->date_i18n() : $document->order->get_date_created()->date_i18n(),
				'document_number'      => $formatted_number,
				'document_order_total' => $is_refund ? $document->order->get_amount() : $document->order->get_total(),
				'document_id'          => $document->order->get_meta( '_ywpi_invoice_number' ),
				'refund_document_id'   => $parent_invoice ? $parent_invoice->get_formatted_document_number() : null,
				'refund_document_date' => $parent_invoice ? $parent_invoice->get_formatted_document_date( 'xml' ) : null,
				'order_items'          => $order_items,
				'progressive_id'       => $this->get_progressive_id_saved_for_document( $document->order ),
				'chargeability_vat'    => ywpi_get_option( 'ywpi_electronic_invoice_chargeability_vat', 'I' ),
				'natura'               => $is_refund ? $this->get_natura( $main_order ) : $this->get_natura( $document->order ),
				'payment_info'         => array(
					'conditions'   => 'TP02',
					'mode'         => $this->get_payment_method( $document ),
					'total_amount' => isset( $main_order ) ? $document->order->get_amount() : $document->order->get_total(),
				),
			);
		}

		/**
		 * Replace vat field ID with the one provided by YITH WooCommerce EU VAT
		 *
		 * @param string $field_id Field ID.
		 *
		 * @return string
		 */
		public function replace_billing_vat_field( $field_id ) {
			$field_id = 'billing_yweu_vat';

			return $field_id;
		}
	}
}
/**
 * Get instance of YITH_Electronic_Invoice class.
 */
function YITH_Electronic_Invoice() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid, Universal.Files.SeparateFunctionsFromOO
	return YITH_Electronic_Invoice::get_instance();
}
