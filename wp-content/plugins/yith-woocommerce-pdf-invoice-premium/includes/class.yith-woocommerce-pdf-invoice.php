<?php // phpcs:ignore WordPress.NamingConventions.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_WooCommerce_Pdf_Invoice' ) ) {

	/**
	 * Implements features of Yith WooCommerce Pdf Invoice
	 *
	 * @class   YITH_WooCommerce_Pdf_Invoice
	 * @package YITH\PDF_Invoice\Classes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 */
	class YITH_WooCommerce_Pdf_Invoice {

		/**
		 * Set is subtotal should include discount.
		 *
		 * @var bool
		 */
		public $subtotal_incl_discount = true;

		/**
		 * Action name to create the invoice.
		 *
		 * @var string
		 */
		public $action_create_invoice = 'create-invoice';

		/**
		 * Set if packing slip generation is enabled
		 *
		 * @var bool
		 */
		public $enable_packing_slip = false;

		/**
		 * Shortcut to backend instance
		 *
		 * @var YITH_YWPI_Backend
		 */
		public $backend = null;

		/**
		 * Premium version landing link
		 *
		 * @var string
		 */
		protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-pdf-invoice/'; //phpcs:ignore

		/**
		 * Plugin official documentation
		 *
		 * @var string
		 */
		protected $_official_documentation = 'https://docs.yithemes.com/yith-woocommerce-pdf-invoice/'; //phpcs:ignore

		/**
		 * Official plugin landing page
		 *
		 * @var string
		 */
		protected $_premium_live = 'https://plugins.yithemes.com/yith-woocommerce-pdf-invoice/'; //phpcs:ignore

		/**
		 * Official plugin support page
		 *
		 * @var string
		 */
		protected $_support = 'https://yithemes.com/my-account/support/dashboard/'; //phpcs:ignore

		/**
		 * Yith WooCommerce Pdf invoice panel page
		 *
		 * @var string
		 */
		protected $_panel_page = 'yith_woocommerce_pdf_invoice_panel'; //phpcs:ignore

		/**
		 * Boolean to control if credit note should be automatically created.
		 *
		 * @var bool
		 */
		protected $automatic_credit_note = true;

		/**
		 * Array to save the invoice numbers created.
		 *
		 * @var bool
		 */
		protected $stored_invoice_numbers = array();

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

			$this->init_plugin_options();

			$this->backend = YITH_YWPI_Backend::get_instance();

			$this->add_buttons_on_customer_orders_page();
			$this->add_features_on_admin_orders_page();

			$this->add_order_status_related_actions();

			// Check if invoice should be attached to emails.
			add_filter( 'woocommerce_email_attachments', array( $this, 'attach_documents_to_email' ), 99, 3 );

			// Add stylesheets and scripts files to back-end.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			/**
			 * Manage the action 'create'
			 */
			add_action(
				'admin_action_create',
				array(
					$this,
					'manage_document_action',
				)
			);

			/**
			 * Manage the action 'view'
			 */
			add_action(
				'admin_action_view',
				array(
					$this,
					'manage_document_action',
				)
			);

			/**
			 * Manage the action 'reset'
			 */
			add_action(
				'admin_action_reset',
				array(
					$this,
					'manage_document_action',
				)
			);

			/**
			 * Manage the action 'preview'
			 */
			add_action(
				'admin_action_preview',
				array(
					$this,
					'manage_document_action',
				)
			);

			/**
			 * Manage the action 'regenerate'
			 */
			add_action(
				'admin_action_regenerate',
				array(
					$this,
					'manage_document_action',
				)
			);

			add_action(
				'init',
				array(
					$this,
					'manage_myaccount_buttons',
				)
			);

			/* === Show Plugin Information === */

			add_filter(
				'plugin_action_links_' . plugin_basename( YITH_YWPI_DIR . '/' . basename( YITH_YWPI_FILE ) ),
				array(
					$this,
					'action_links',
				)
			);

			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

		}


		/**
		 * Enqueue js file
		 *
		 * @since  1.0
		 * @author YITH
		 */
		public function enqueue_scripts() {

			/* ====== Script ====== */

			wp_register_script(
				'ywpi_' . YITH_YWPI_ASSETS_URL . '-js',
				YITH_YWPI_ASSETS_URL . '/js/yith-wc-pdf-invoice-admin.js',
				array(
					'jquery',
					'jquery-ui-sortable',
				),
				YITH_YWPI_ENQUEUE_VERSION,
				true
			);

			wp_localize_script(
				'ywpi_' . YITH_YWPI_ASSETS_URL . '-js',
				'yith_wc_pdf_invoice_free_object',
				apply_filters(
					'yith_wc_pdf_invoice_free_admin_localize',
					array(
						'ajax_url'                        => admin_url( 'admin-ajax.php' ),
						'ajax_loader'                     => 'ywpi_css',
						YITH_YWPI_ASSETS_URL . '/images/ajax-loader.gif',
						'logo_message_1'                  => esc_html__( 'The logo you are uploading is ', 'yith-woocommerce-pdf-invoice' ),
						'logo_message_2'                  => esc_html__( 'Logo must be no bigger than 300 x 150 pixels.', 'yith-woocommerce-pdf-invoice' ),
						'electronic_invoice'              => get_option( 'ywpi_electronic_invoice_enable' ),
						'alert_refund_credit_note'        => esc_html__( 'In order to correctly generate a credit note, you should set the amount only on single order items.', 'yith-woocommerce-pdf-invoice' ),
						'alert_refund_credit_reason'      => esc_html__( 'Before proceeding, it is mandatory to enter the reason for the refund.', 'yith-woocommerce-pdf-invoice' ),
						'search_invoice_placeholder' => isset( $_REQUEST['sub_tab'] ) && 'documents_type-credit-notes' === $_REQUEST['sub_tab'] ? //phpcs:ignore
							esc_html__( 'Search credit note', 'yith-woocommerce-pdf-invoice' ) : esc_html__( 'Search invoice', 'yith-woocommerce-pdf-invoice' ), // phpcs:ignore
						'regenerate_confirm_title'        => esc_html__( 'Confirm regenerate', 'yith-woocommerce-pdf-invoice' ),
						'regenerate_confirm_message_invoices' => esc_html__( 'Do you want to regenerate the selected invoices?', 'yith-woocommerce-pdf-invoice' ),
						'regenerate_confirm_message_credit_notes' => esc_html__( 'Do you want to regenerate the selected credit notes?', 'yith-woocommerce-pdf-invoice' ),
						'regenerate_confirm_button'       => esc_html__( 'Confirm', 'yith-woocommerce-pdf-invoice' ),
						'delete_confirm_title'            => esc_html__( 'Confirm delete', 'yith-woocommerce-pdf-invoice' ),
						'delete_confirm_message_invoices' => esc_html__( 'Do you want to delete the selected invoices?', 'yith-woocommerce-pdf-invoice' ),
						'delete_confirm_message_credit_notes' => esc_html__( 'Do you want to delete the selected credit notes?', 'yith-woocommerce-pdf-invoice' ),
						'delete_cancel_button'            => esc_html__( 'No', 'yith-woocommerce-pdf-invoice' ),
						'delete_confirm_button'           => esc_html__( 'Yes, delete', 'yith-woocommerce-pdf-invoice' ),

					)
				)
			);

			wp_enqueue_script( 'ywpi_' . YITH_YWPI_ASSETS_URL . '-js' );

		}

		/**
		 * Enqueue css file
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 */
		public function enqueue_styles() {

			if ( is_checkout() || apply_filters( 'yith_ywpi_enqueue_frontend_styles', false ) ) {

				wp_enqueue_style( 'ywpi_css', YITH_YWPI_ASSETS_URL . '/css/ywpi.css', array(), YITH_YWPI_ENQUEUE_VERSION ); //phpcs:ignore

				wp_add_inline_style( 'ywpi_css', $this->get_custom_css() );
			}

			wp_register_script( 'ywpi_frontend_js', YITH_YWPI_ASSETS_URL . '/js/yith-wc-pdf-invoice-frontend.js', array( 'jquery' ), YITH_YWPI_ENQUEUE_VERSION, true );

			wp_localize_script(
				'ywpi_frontend_js',
				'ywpi_frontend',
				array(
					'open_file_option' => ywpi_get_option( 'ywpi_pdf_invoice_behaviour', 'download' ),
				)
			);

			if ( is_account_page() || apply_filters( 'yith_ywpi_enqueue_frontend_script', false ) ) {
				wp_enqueue_script( 'ywpi_frontend_js' );
			}
		}
		/**
		 * Get custom CSS.
		 */
		public function get_custom_css() {
			$custom_css = '';

			if ( 'YITH Proteo' == wp_get_theme()->get( 'Name' ) || 'YITH Proteo Child' == wp_get_theme()->get( 'Name' ) ) { //phpcs:ignore
				$custom_css .= '
					#billing_receiver_type_field .radiobutton {
						float: left;
						clear: both;
					}

					p#billing_receiver_type_field span.woocommerce-input-wrapper label {
						display: inline-block;
						float: left;
						top: 0px;
						position: relative;
					}
				';

			}

			return $custom_css;
		}
		/**
		 * Manage My Account buttons.
		 */
		public function manage_myaccount_buttons() {
			if ( is_admin() ) {
				return;
			}

			$this->manage_document_action();
		}

		/**
		 * Create invoice
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 * @throws Exception The exception if it's not allowed type.
		 */
		public function manage_document_action() {

			if ( ! isset( $_REQUEST['type'] ) ) {
				return;
			}

			if ( ! isset( $_REQUEST['id'] ) ) {
				return;
			}

			if ( ! isset( $_REQUEST['action'] ) ) {
				return;
			}

			if ( ! isset( $_REQUEST['_wpnonce'] ) ) {
				return;
			}

			$type       = wp_unslash( $_REQUEST['type'] ); //phpcs:ignore
			$order_id   = wp_unslash( $_REQUEST['id'] ); //phpcs:ignore
			$action     = wp_unslash( $_REQUEST['action'] ); //phpcs:ignore
			$force_view = isset( $_REQUEST['force_view'] ) ? wp_unslash( $_REQUEST['force_view'] ) : false; //phpcs:ignore

			$nonce        =  wp_unslash( $_REQUEST['_wpnonce'] ); //phpcs:ignore
			$nonce_action = $action . $type . $order_id;

			$extension = isset( $_REQUEST['extension'] ) ? wp_unslash( $_REQUEST['extension'] ) : 'pdf'; //phpcs:ignore

			if ( ! wp_verify_nonce( $nonce, $nonce_action ) ) {
				return;
			}

			$allowed_type = array( 'invoice', 'packing-slip', 'proforma', 'credit-note', 'xml' );
			if ( ! in_array( strtolower( $type ), $allowed_type ) ) { //phpcs:ignore
				throw new Exception( esc_html__( 'YITH PDF Invoice: unknown document type. Unable to manage this action.', 'yith-woocommerce-pdf-invoice' ) );
			}

			switch ( $action ) {
				case 'create':
					$this->create_document( $order_id, $type, $extension );

					do_action( 'ywpi_create_document', $this, $order_id, $type, $extension );

					break;

				case 'view':
					$this->view_document( $order_id, $type, $extension );
					break;

				case 'reset':
					$this->reset_document( $order_id, $type, $extension );
					break;

				case 'preview':
					$this->view_document( $order_id, $type, $extension, $force_view );
					break;

				case 'regenerate':
					$this->regenerate_document( $order_id, $type, $extension );

					do_action( 'ywpi_regenerate_document', $this, $order_id, $type, $extension );

					break;

				case 'download':
					$this->download_document( $order_id, $type, $extension );
					break;

				case 'send_customer':
					$this->send_document_to_customer( $order_id, $type, $extension );
					break;
				default:
					return;
			}

			if ( is_admin() && isset( $_SERVER['HTTP_REFERER'] ) ) {
				$location = $_SERVER['HTTP_REFERER']; //phpcs:ignore

				if ( ! wp_safe_redirect( $location ) ) {
					wp_redirect( $location ); //phpcs:ignore
				}
				exit();
			}
		}


		/**
		 * Retrieve the plugin option value for the document notes visibility
		 *
		 * @param YITH_Document $document The document object.
		 *
		 * @return mixed|void
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function is_visible_document_notes( $document ) {
			$is_visible = '';

			if ( $document instanceof YITH_Credit_Note ) {
				$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_show_credit_note_notes', $document, 'yes' ) );
			} elseif ( ywpi_document_behave_as_invoice( $document ) ) {
				$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_show_invoice_notes', $document, 'yes' ) );
			} elseif ( $document instanceof YITH_Shipping ) {
				$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_packing_slip_show_notes', $document, 'yes' ) );
			}

			return apply_filters( 'is_visible_document_notes', $is_visible, $document );
		}


		/**
		 * Retrieve the notes to be shown when printing a generic document
		 *
		 * @param YITH_Document $document The document object.
		 *
		 * @return mixed
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function get_document_notes( $document ) {
			$notes = '';
			if ( $document instanceof YITH_Invoice ) {
				$notes = ywpi_get_option( 'ywpi_invoice_notes', $document );
			} elseif ( $document instanceof YITH_Pro_Forma ) {
				$notes = ywpi_get_option( 'ywpi_pro_forma_notes', $document );
			} elseif ( $document instanceof YITH_Shipping ) {
				$notes = ywpi_get_option( 'ywpi_packing_slip_notes', $document );
			} elseif ( $document instanceof YITH_Credit_Note ) {
				$notes = ywpi_get_option( 'ywpi_credit_note_notes', $document );
			}

			return apply_filters( 'get_document_notes', $this->replace_customer_details_pattern( $notes, $document->order->get_id() ), $document );
		}

		/**
		 * Check if a footer should be shown for a document
		 *
		 * @param YITH_Document $document The document object.
		 *
		 * @return mixed|void
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function is_visible_document_footer( $document ) {
			$is_visible = '';

			if ( $document instanceof YITH_Credit_Note ) {
				$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_show_credit_note_footer', $document, 'yes' ) );
			} elseif ( ywpi_document_behave_as_invoice( $document ) ) {
				$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_show_invoice_footer', $document, 'yes' ) );
			} elseif ( $document instanceof YITH_Shipping ) {
				$is_visible = 'yes' === strval( ywpi_get_option( 'ywpi_packing_slip_show_footer', $document, 'yes' ) );
			}

			return apply_filters( 'is_visible_document_footer', $is_visible, $document );
		}

		/**
		 * Retrieve the document title based on the document type
		 *
		 * @param YITH_Document $document The document object.
		 *
		 * @author Lorenzo Giuffrida
		 *
		 * @return string
		 *
		 * @since  1.0.0
		 */
		public function get_document_title( $document ) {
			$title = '';

			if ( $document instanceof YITH_Invoice ) {
				$title = esc_html__( 'Invoice', 'yith-woocommerce-pdf-invoice' );
			} elseif ( $document instanceof YITH_Shipping ) {
				$title = esc_html__( 'Packing slip', 'yith-woocommerce-pdf-invoice' );
			}

			return apply_filters( 'ywpi_document_title', $title, $document );
		}

		/**
		 * Initialize the plugin options
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function init_plugin_options() {

			$this->subtotal_incl_discount = 'yes' === get_option( 'ywpi_subtotal_inclusive_discount', 'yes' );
			$this->enable_packing_slip    = 'yes' == ywpi_get_option( 'ywpi_enable_packing_slip', 'no' ); //phpcs:ignore
		}

		/**
		 * Add some actions triggered by order status
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function add_order_status_related_actions() {
			// If invoice generation is only manual, no automatic actions will be added.
			if ( 'auto' !== strval( ywpi_get_option( 'ywpi_invoice_generation' ) ) && 'auto' !== strval( ywpi_get_option( 'ywpi_packing_slip_generation' ) ) ) {
				return;
			}

			if ( 'new' === ywpi_get_option( 'ywpi_create_invoice_on' ) ) {
				$array_status = array( 'on-hold', 'processing', 'completed' );
				foreach ( $array_status as $status ) {
					add_action( 'woocommerce_order_status_' . $status, array( $this, 'create_automatic_invoice' ) );
				}
			} elseif ( 'processing' === ywpi_get_option( 'ywpi_create_invoice_on' ) ) {
				add_action( 'woocommerce_order_status_processing', array( $this, 'create_automatic_invoice' ) );
			} elseif ( 'completed' === ywpi_get_option( 'ywpi_create_invoice_on' ) ) {
				add_action( 'woocommerce_order_status_completed', array( $this, 'create_automatic_invoice' ) );
			}
		}

		/**
		 * Create an invoice for a specific order
		 *
		 * @param int $order_id The order id.
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function create_automatic_invoice( $order_id ) {

			// Lets a third party plugin to stop the creation of invoices in automatic mode.
			if ( ! apply_filters( 'yith_ywpi_create_automatic_invoices', true, $order_id ) ) {
				return;
			}

			$invoice_generation = strval( get_option( 'ywpi_invoice_generation' ) );

			if ( 'auto' === $invoice_generation ) {
				$this->create_document( $order_id, 'invoice' );
			}

			$packing_slip_generation = strval( get_option( 'ywpi_packing_slip_generation' ) );

			if ( 'auto' === $packing_slip_generation ) {
				$this->create_document( $order_id, 'packing-slip' );
			}

			do_action( 'ywpi_create_automatic_invoice', $order_id );
		}


		/**
		 * Create the credit note automatically.
		 *
		 * @param  mixed $order_id The order id.
		 * @return void
		 */
		public function create_automatic_credit_note( $order_id ) {

			$order         = wc_get_order( $order_id );
			$order_refunds = $order->get_refunds();

			if ( isset( $order_refunds[0] ) ) {
				$this->create_document( $order_refunds[0]->get_id(), 'credit-note' );
			}
		}

		/**
		 * Retrieve the link for a specific action on the order
		 *
		 * @param string  $action   The action to perform. It could be 'view','create', 'reset' or 'preview'.
		 * @param string  $type     The type of document. It could be 'invoice', 'packing-slip', 'credit-note', 'proforma'.
		 * @param int     $order_id The order id.
		 * @param string  $extension The extension of the document.
		 * @param boolean $force_view Force the view of the document.
		 *
		 * @return string
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function get_action_url( $action, $type, $order_id, $extension = 'pdf', $force_view = false ) {

			$args = array(
				'action'    => $action,
				'type'      => $type,
				'id'        => $order_id,
				'extension' => $extension,
				'extension' => $extension,
			);

			if ( 'preview' === $action ) {
				$args['force_view'] = $force_view;
			}

			$nonce_action = $action . $type . $order_id;

			return esc_url( wp_nonce_url( add_query_arg( $args ), $nonce_action ) );
		}


		/**
		 * Attach the documents to the email
		 *
		 * @param array  $attachments The array of attachments.
		 * @param string $status The status of order.
		 * @param mixed  $object The order object.
		 *
		 * @return array
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function attach_documents_to_email( $attachments, $status, $object ) {

			if ( ! $object instanceof WC_Order ) {
				return $attachments;
			}

			if ( $this->automatic_credit_note && ( 'customer_refunded_order' == $status || 'customer_partially_refunded_order' == $status && 'manual' != ywpi_get_option( 'ywpi_invoice_generation' ) ) ) { //phpcs:ignore
				$this->create_automatic_credit_note( $object->get_id() );
			}

			$order_refunds = $object->get_refunds();

			if ( isset( $order_refunds[0] ) && apply_filters( 'ywpi_allow_attach_credit_note', true ) ) {

				$credit_note_path = $this->get_credit_note_attachment( $status, $order_refunds[0]->get_id() );

				if ( $credit_note_path ) {
					$attachments[] = $credit_note_path;
				}
			}

			$invoice_path = $this->get_invoice_attachment( $status, yit_get_prop( $object, 'id' ) );

			if ( $invoice_path ) {
				$attachments[] = $invoice_path;
			}

			$pro_forma_path = $this->get_pro_forma_attachment( $status, yit_get_prop( $object, 'id' ) );
			if ( $pro_forma_path ) {
				$attachments[] = $pro_forma_path;
			}

			return $attachments;
		}

		/**
		 * Retrieve the order invoice path
		 *
		 * @param string $status   current order status.
		 * @param int    $order_id order id.
		 *
		 * @return string
		 */
		public function get_invoice_attachment( $status, $order_id ) {

			$allowed_statuses = apply_filters(
				'ywpi_attach_invoice_on_order_status',
				array(
					'customer_invoice',
					'customer_processing_order',
					'customer_completed_order',
				)
			);

			if ( isset( $status ) && in_array( $status, $allowed_statuses ) ) { //phpcs:ignore
				$invoice = new YITH_Invoice( $order_id );

				if ( $invoice->is_valid() && $invoice->generated() ) {
					return $invoice->get_full_path();
				}
			}

			return '';
		}


		/**
		 * Retrieve the credit note path.
		 *
		 * @param string $status   current order status.
		 * @param int    $refund_id refund id.
		 * @return string
		 */
		public function get_credit_note_attachment( $status, $refund_id ) {

			if ( get_option( 'ywpi_enable_credit_notes', 'no' ) != 'yes' ) { //phpcs:ignore
				return;
			}

			$allowed_statuses = apply_filters(
				'ywpi_attach_credit_on_order_status',
				array(
					'customer_refunded_order',
					'customer_partially_refunded_order',
				)
			);

			if ( isset( $status ) && in_array( $status, $allowed_statuses ) ) { //phpcs:ignore

				$credit_note = new YITH_Credit_Note( $refund_id );

				if ( $credit_note->generated() ) {
					return $credit_note->get_full_path();
				}
			}

			return '';
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

			return '';
		}

		/**
		 * Add front-end button for actions available for customers
		 */
		public function add_buttons_on_customer_orders_page() {
			/**
			 * Show print invoice button on frontend orders page
			 */
			add_action(
				'woocommerce_my_account_my_orders_actions',
				array(
					$this,
					'print_invoice_button',
				),
				10,
				2
			);
		}

		/**
		 * Add back-end buttons for actions available for admins
		 */
		public function add_features_on_admin_orders_page() {
			add_action(
				'manage_shop_order_posts_custom_column',
				array(
					$this,
					'show_invoice_custom_column_data',
				),
				99
			);

		}

		/**
		 * Append invoice information on order_title column, if current order has an invoice associated
		 *
		 * @param string $column the column name being shown.
		 */
		public function show_invoice_custom_column_data( $column ) {
			global $post;
			$column_to_check = version_compare( WC()->version, '3.3', '<' ) ? 'order_title' : 'order_number';
			if ( $column_to_check != $column ) { //phpcs:ignore
				return;
			}

			$this->show_invoice_information_link( $post );
		}

		/**
		 * Show a link with the order invoiced status
		 *
		 * @param WP_Post $post The post object (order).
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function show_invoice_information_link( $post ) {
			$order = wc_get_order( $post );

			$invoice = new YITH_Invoice( yit_get_prop( $order, 'id' ) );

			if ( ! $invoice->is_valid() || ! $invoice->generated() ) {
				return;
			}

			$url = apply_filters( 'ywpi_invoice_information_url', $this->get_action_url( 'view', 'invoice', yit_get_prop( $order, 'id' ) ), $order );

			$is_receipt = get_post_meta( yit_get_prop( $order, 'id' ), '_billing_invoice_type', true );

			if ( 'receipt' !== strval( $is_receipt ) ) {
				?>
				<div class="ywpi-invoiced-order">
					<a class="meta ywpi-invoice-information"
					target="_blank" href="<?php echo esc_url( $url ); ?>"
					title="<?php esc_html_e( 'View Invoice', 'yith-woocommerce-pdf-invoice' ); ?>">
						<?php // translators: Invoice number. ?>
						<?php echo sprintf( esc_html__( 'View Invoice No. %s', 'yith-woocommerce-pdf-invoice' ), wp_kses_post( $invoice->get_formatted_document_number() ) ); ?>
					</a>
					<?php do_action( 'ywpi_show_invoice_information_link', $invoice, $order ); ?>
				</div>
				<?php
			} else {
				?>
				<div class="ywpi-invoiced-order">
					<a class="meta ywpi-invoice-information"
					target="_blank" href="<?php echo esc_url( $url ); ?>"
					title="<?php esc_html_e( 'View Receipt', 'yith-woocommerce-pdf-invoice' ); ?>">
						<?php echo esc_html__( 'View Receipt', 'yith-woocommerce-pdf-invoice' ); ?>
					</a>
					<?php do_action( 'ywpi_show_invoice_information_link', $invoice, $order ); ?>
				</div>
				<?php
			}

		}


		/**
		 * Check nounce when an action for generating documents is called
		 *
		 * @param YITH_Document $document the document that is going to be created.
		 *
		 * @return bool
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function check_invoice_url_for_action( $document ) {

			if ( ! $document ) {
				return false;
			}

			// Check if the document is for a valid order.
			if ( ! $document->is_valid() ) {
				return false;
			}

			return true;
		}


		/**
		 * Create a new document of the type requested, for a specific order
		 *
		 * @param int    $order_id      The order id for which the document is created.
		 * @param string $document_type The document type to be generated.
		 * @param string $extension     The document extension.
		 *
		 * @return YITH_Document|null
		 */
		public function create_document( $order_id, $document_type = '', $extension = 'pdf' ) {

			$is_receipt = get_post_meta( $order_id, '_billing_invoice_type', true );

			if ( ! apply_filters( 'yith_ywpi_can_create_document', true, $order_id, $document_type ) ) {
				return null;
			}

			$document = ywpi_get_order_document_by_type( $order_id, $document_type );

			if ( null == $document ) { //phpcs:ignore
				return null;
			}

			if ( ! $document instanceof YITH_Document ) {
				return null;
			}

			/**
			 * If a document of that type exists, return it instead of creating a new one
			 */
			if ( $document->generated() && apply_filters( 'ywpi_skip_document_generation', true, $order_id, $document_type, $extension ) ) {
				return $document;
			}

			/**
			 * For YITH_Invoice and YITH_Credit_Note, assign a new unique number and set other details
			 */
			if ( ! $document->generated() && ( ( $document instanceof YITH_Invoice ) ||
				( $document instanceof YITH_Credit_Note ) )
			) {

				// Set the document value.
				$document->date = apply_filters( 'yith_ywpi_set_document_date', current_time( 'mysql', 0 ), $document );

				if ( $document instanceof YITH_Credit_Note ) {
					$document_type    = 'credit-note';
					$document->number = $this->get_next_number( $document, $document_type );

				} else {
					$document_type         = 'invoice';
					$invoice_number_option = get_option( 'ywpi_invoices_numbers', 'sequential' );
					switch ( $invoice_number_option ) {

						case 'order_number':
							$document->number = $document->order->get_order_number();
							break;

						case 'order_id':
							$document->number = $document->order->get_id();
							break;

						case 'sequential':
							$document->number = $this->get_next_number( $document, $document_type );
							break;
					}
				}
				$prefix           = $document instanceof YITH_Credit_Note ? 'ywpi_credit_note_prefix' : 'ywpi_invoice_prefix';
				$document->prefix = $this->replace_placeholders( ywpi_get_option( $prefix, $document->order ), $document->date, $document_type );

				$suffix           = $document instanceof YITH_Credit_Note ? 'ywpi_credit_note_suffix' : 'ywpi_invoice_suffix';
				$document->suffix = $this->replace_placeholders( ywpi_get_option( $suffix, $document->order ), $document->date, $document_type );

				$formatted_key    = $document instanceof YITH_Credit_Note ? 'ywpi_credit_note_number_format' : 'ywpi_invoice_number_format';
				$formatted_number = ywpi_get_option_with_placeholder( $formatted_key, '[number]' );

				$order_number = $document->order instanceof WC_Order ? $document->order->get_order_number() : '';

				$date_to_show = ywpi_get_option( 'ywpi_date_to_show_in_invoice' );

				switch ( $date_to_show ) {
					case 'new':
						$date = getdate( strtotime( $document->order->get_date_created() ) );
						break;

					case 'completed':
						if ( $document->order->get_status() == 'completed' && ! $document->order instanceof WC_Order_Refund ) { //phpcs:ignore
							$date = getdate( strtotime( $document->order->get_date_completed() ) );
						} elseif ( ! $document instanceof YITH_Shipping && ! $document instanceof YITH_Pro_Forma ) {
							$date = getdate( strtotime( $document->date ) );
						} else {
							$date = getdate( strtotime( $document->order->get_date_created() ) );
						}
						break;

					case 'invoice_creation':
						if ( ! $document instanceof YITH_Shipping && ! $document instanceof YITH_Pro_Forma ) {
							$date = getdate( strtotime( $document->date ) );
						} else {
							$date = getdate( strtotime( $document->order->get_date_created() ) );
						}
						break;

					default:
						$date = getdate( strtotime( $document->order->get_date_created() ) );
						break;
				}

				$replace_placeholders = str_replace(
					array(
						'[prefix]',
						'[suffix]',
						'[number]',
						'[year]',
						'[month]',
						'[day]',
						'[order_number]',
					),
					array(
						$document->prefix,
						$document->suffix,
						$document->number,
						$date['year'],
						sprintf( '%02d', $date['mon'] ),
						sprintf( '%02d', $date['mday'] ),
						$order_number,
					),
					$formatted_number
				);

				$document->formatted_number = apply_filters( 'yith_ywpi_formatted_invoice_number', $replace_placeholders, $formatted_number, $document );

			}

			if ( $this->create_file( $document, $extension ) ) {

				$document->save( $extension );

				do_action( 'yith_ywpi_document_created', $document, $document_type, $extension );

			}

			return $document;
		}


		/**
		 * Regenerate the document for the specific order
		 *
		 * @param  int    $order_id      the order id for which the document is created.
		 * @param string $document_type the document type to be generated.
		 * @param string $extension     The document extension.
		 *
		 * @return YITH_Document|null
		 */
		public function regenerate_document( $order_id, $document_type = '', $extension = 'pdf' ) {

			if ( ! apply_filters( 'yith_ywpi_can_create_document', true, $order_id, $document_type ) ) {
				return null;
			}

			$document = ywpi_get_order_document_by_type( $order_id, $document_type );

			if ( null == $document ) { //phpcs:ignore
				return null;
			}

			if ( ! $document instanceof YITH_Document ) {
				return null;
			}

			/**
			 * For YITH_Invoice and YITH_Credit_Note, assign a new unique number and set other details
			 */
			if ( ( $document instanceof YITH_Invoice ) ||
				( $document instanceof YITH_Credit_Note )
			) {
				// Set the document value.
				$document->date = apply_filters( 'yith_ywpi_set_document_date', $document->date, $document );

				if ( $document instanceof YITH_Invoice ) {
					$document_type         = 'invoice';
					$invoice_number_option = get_option( 'ywpi_invoices_numbers', 'sequential' );

					switch ( $invoice_number_option ) {

						case 'order_number':
							$document->number = $document->order->get_order_number();
							break;

						case 'order_id':
							$document->number = $document->order->get_id();
							break;
					}
				}

				$formatted_key    = $document instanceof YITH_Credit_Note ? 'ywpi_credit_note_number_format' : 'ywpi_invoice_number_format';
				$formatted_number = ywpi_get_option_with_placeholder( $formatted_key, '[number]' );

				$date = getdate( strtotime( $document->date ) );

				$order_number = $document->order instanceof WC_Order ? $document->order->get_order_number() : '';

				$replace_placeholders = str_replace(
					array(
						'[prefix]',
						'[suffix]',
						'[number]',
						'[year]',
						'[month]',
						'[day]',
						'[order_number]',
					),
					array(
						$document->prefix,
						$document->suffix,
						$document->number,
						$date['year'],
						sprintf( '%02d', $date['mon'] ),
						sprintf( '%02d', $date['mday'] ),
						$order_number,
					),
					$formatted_number
				);

				$document->formatted_number = apply_filters( 'yith_ywpi_formatted_invoice_number', $replace_placeholders, $formatted_number, $document );
			}

			if ( $this->create_file( $document, $extension ) ) {
				do_action( 'yith_ywpi_document_regenerated', $document, $document_type, $extension );
			}

			if ( 'xml' !== strval( $extension ) ) {
				$document->save();
			}

			return $document;
		}

		/**
		 * Return the next available invoice number
		 *
		 * @param YITH_Document $document the document that need a new invoice number.
		 * @param string        $type     the type of document, 'invoice'(default) or 'credit-note'.
		 *
		 * @return int|mixed
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		private function get_next_number( $document, $type = 'invoice' ) {
			/** $document should be an instance of  YITH_Invoice or YITH_Credit_Note */
			if ( ! ( $document instanceof YITH_Invoice ) && ! ( $document instanceof YITH_Credit_Note ) && ! ( $document instanceof YITH_XML ) ) {
				return;
			}

			$reset_option     = 'invoice' == $type ? 'ywpi_invoice_reset' : 'ywpi_credit_note_reset'; //phpcs:ignore
			$last_year_option = 'invoice' == $type ? 'ywpi_invoice_year_billing' : 'ywpi_credit_note_year_billing'; //phpcs:ignore

			// Check if this is the first invoice of the year, in this case, if reset on new year is enabled, restart from 1.
			if ( 'yes' === get_option( $reset_option ) ) {
				$last_year = get_option( $last_year_option );

				if ( isset( $last_year ) && is_numeric( $last_year ) ) {
					$current_year = getdate();
					$current_year = $current_year['year'];

					if ( $last_year < $current_year ) {
						// Set new year as last invoiced year and reset invoice number.
						update_option( $last_year_option, $current_year );

						if ( $document instanceof YITH_Invoice ) {
							return apply_filters( 'yith_ywpi_reset_year_invoice_number', 1 );
						}
						if ( $document instanceof YITH_Credit_Note ) {
							return apply_filters( 'yith_ywpi_reset_year_document_note_number', 1 );

						}
					}
				}
			}

			$number_option = ( $document instanceof YITH_Credit_Note ) ? 'ywpi_credit_note_next_number' : 'ywpi_invoice_number';

			$current_invoice_number = $this->check_actual_invoice_number( get_option( $number_option ), $document, $type );

			if ( ! isset( $current_invoice_number ) || ! is_numeric( $current_invoice_number ) ) {
				$current_invoice_number = 1;
			}

			if ( 'yes' == get_option( 'ywpi_enable_number_of_digits', 'no' ) && $document instanceof YITH_Invoice ) { //phpcs:ignore

				$number_of_digits = get_option( 'ywpi_number_of_digits_invoice', '0' );

				if ( $number_of_digits > 0 ) {
					$current_invoice_number = str_pad( $current_invoice_number, $number_of_digits, '0', STR_PAD_LEFT );
				}
			}

			return apply_filters( 'yith_ywpi_current_invoice_number', $current_invoice_number, $document->order, $document );
		}


		/**
		 * Save the next available invoice number
		 *
		 * @param YITH_Invoice $document The document object.
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		private function increment_next_document_number( $document ) {

			/** $document should be an instance of  YITH_Invoice or YITH_Credit_Note */
			if ( ! ( $document instanceof YITH_Invoice ) && ! ( $document instanceof YITH_Credit_Note ) && ! ( $document instanceof YITH_XML ) ) {
				return;
			}

			// If it's a receipt, do not increment the document number.
			$is_receipt = get_post_meta( $document->order->get_id(), '_billing_invoice_type', true );
			if ( 'receipt' === strval( $is_receipt ) ) {
				return;
			}

			$number_option = ( $document instanceof YITH_Credit_Note ) ? 'ywpi_credit_note_next_number' : 'ywpi_invoice_number';

			$next_invoice_number = intval( $document->number ) + 1; // the next invoice number.

			ywpi_update_option( $number_option, $next_invoice_number );
		}


		/**
		 * Check actual invoice number.
		 *
		 * @param  mixed $current_invoice_number The current invoice number.
		 * @param  mixed $document               The document object.
		 * @param  mixed $type                   The type of the document.
		 * @return int
		 */
		private function check_actual_invoice_number( $current_invoice_number, $document, $type ) {
			if ( ! $document instanceof YITH_Credit_Note && 'yes' === get_option( 'ywpi_invoice_check_duplicated', 'no' ) ) {

				global $wpdb;

				$key = '_ywpi_invoice_number';

				$reset_option = 'invoice' == $type ? 'ywpi_invoice_reset' : 'ywpi_credit_note_reset'; //phpcs:ignore

				if ( empty( $this->stored_invoice_numbers ) ) {
					if ( 'yes' === get_option( $reset_option ) ) {

						$first_day_of_year = date( 'Y-m-d', strtotime( 'first day of january this year' ) ); //phpcs:ignore

						$stored_invoice_numbers = $wpdb->get_col( //phpcs:ignore
							$wpdb->prepare(
								"
					SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm
					LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
					WHERE pm.meta_key = %s
					AND p.post_date > %s

					",
								$key,
								$first_day_of_year
							)
						);
						$this->stored_invoice_numbers = $stored_invoice_numbers;

					} else {
						$stored_invoice_numbers = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm WHERE pm.meta_key = %s", $key ) ); //phpcs:ignore
						$this->stored_invoice_numbers = $stored_invoice_numbers;

					}
				}

				if ( ! empty( $this->stored_invoice_numbers ) && in_array( $current_invoice_number, $this->stored_invoice_numbers ) ) { //phpcs:ignore
					/*** PRINT
					 * error_log( print_r( "ERROR: The invoice number is not incremented properly, proceeding to correct it. Order:  " . $document->order->get_id() , true ) );
					 */
					$current_invoice_number = $this->check_actual_invoice_number( $current_invoice_number + 1, $document, $type );
				}
			}

			return $current_invoice_number;

		}

		/**
		 * Replace fixed placeholders from a specific string
		 *
		 * @param string $text the string to be parsed.
		 * @param string $date The date.
		 * @param string $document_type The document type.
		 *
		 * @return mixed
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		private function replace_placeholders( $text, $date, $document_type ) {

			if ( 'invoice' === $document_type && 'no' === ywpi_get_option( 'ywpi_enable_invoice_prefix_sufix' ) ||
			'credit-note' === $document_type && 'no' === ywpi_get_option( 'ywpi_enable_credit_note_prefix_sufix' )
			) {
				return '';
			}

			$date = getdate( strtotime( $date ) );

			$replaced_text = str_replace(
				array(
					'[year]',
					'[month]',
					'[day]',
				),
				array(
					$date['year'],
					sprintf( '%02d', $date['mon'] ),
					sprintf( '%02d', $date['mday'] ),
				),
				$text
			);

			return $replaced_text;
		}

		/**
		 * Show a document
		 *
		 * @param int     $order_id      The order id.
		 * @param string  $document_type The type of document to show.
		 * @param string  $extension     The document extension.
		 * @param boolean $force_view    Force to view directly the file instead download it (depending on the option).
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function view_document( $order_id, $document_type, $extension, $force_view = false ) {
			$document = ywpi_get_order_document_by_type( $order_id, $document_type );

			if ( ( null == $document ) || ! $this->check_invoice_url_for_action( $document ) ) { //phpcs:ignore
				return;
			}

			$this->show_file( $document->get_full_path( $extension, $order_id ), $extension, $order_id, $force_view );
		}

		/**
		 * Show a file on browser or ask for download, according with related option
		 *
		 * @param YITH_Document|string $resource    The document to show or the path of the file to be shown.
		 * @param string               $extension   The document extension.
		 * @param int                  $order_id    The order id.
		 * @param boolean              $force_view  Force to view the document.
		 */
		public function show_file( $resource, $extension = 'pdf', $order_id = null, $force_view = false ) {

			$path = $resource;

			if ( $resource instanceof YITH_Document ) {
				$path = $resource->get_full_path( $extension, $order_id );
			}

			$content_type = apply_filters( 'ywpi_file_content_type', 'Content-type: application/pdf', $resource, $extension );
			if ( 'open' === strval( ywpi_get_option( 'ywpi_pdf_invoice_behaviour' ) ) || 'open_tab' === strval( ywpi_get_option( 'ywpi_pdf_invoice_behaviour' ) ) || $force_view ) {
				header( $content_type );
				header( 'Content-Disposition: inline; filename = "' . basename( $path ) . '"' );
				header( 'Content-Transfer-Encoding: binary' );
				if ( file_exists( $path ) ) {
					header( 'Content-Length: ' . filesize( $path ) );
				}
				header( 'Accept-Ranges: bytes' );
				@readfile( $path ); //phpcs:ignore
				exit();
			} else {
				header( $content_type );
				header( 'Content-Disposition: attachment; filename = "' . basename( $path ) . '"' );
				@readfile( $path ); //phpcs:ignore
				exit();
			}
		}

		/**
		 * Download a file.
		 *
		 * @param int    $order_id      The order id.
		 * @param string $document_type The document type.
		 * @param string $extension     The document extension.
		 */
		public function download_document( $order_id, $document_type = 'invoice', $extension = 'pdf' ) {

			$document = ywpi_get_order_document_by_type( $order_id, $document_type );

			if ( ( null == $document ) || ! $this->check_invoice_url_for_action( $document ) ) { //phpcs:ignore
				return;
			}

			$path = $document->get_full_path( $extension, $order_id );

			if ( $path instanceof YITH_Document ) {
				$path = $path->get_full_path( $extension, $order_id );
			}

			$content_type = 'Content-type: application/pdf';

				header( $content_type );
				header( 'Content-Disposition: attachment; filename = "' . basename( $path ) . '"' );
				@readfile( $path ); //phpcs:ignore
				exit();
		}
		/**
		 *
		 * Send email with the invoice attached.
		 *
		 * @param int    $order_id      The order id.
		 * @param string $document_type The document type.
		 * @param string $extension     The document extension.
		 */
		public function send_document_to_customer( $order_id, $document_type = 'invoice', $extension = 'pdf' ) {
			$order  = wc_get_order( $order_id );
			$mailer = WC()->mailer();
			$mails  = $mailer->get_emails();

			$this->automatic_credit_note = false;

			if ( ! empty( $mails ) ) {

				if ( $order instanceof WC_Order ) {
					WC()->mailer()->customer_invoice( $order );
				}
				if ( $order instanceof WC_Order_Refund ) {
					foreach ( $mails as $mail ) {
						if ( 'customer_refunded_order' === $mail->id ) {
							$mail->trigger( $order->get_parent_id() );
						}
					}
				}
			}
		}

		/**
		 * Check if the current user can delete a document type for a specific order
		 *
		 * @param int    $order_id The order id.
		 * @param string $document_type The document type.
		 *
		 * @return bool
		 */
		public function user_can_delete_document( $order_id, $document_type ) {

			$enabled_capabilities = apply_filters(
				'yith_ywpi_delete_document_capabilities',
				array( 'manage_woocommerce' ),
				$order_id,
				$document_type
			);

			if ( $enabled_capabilities ) {
				foreach ( $enabled_capabilities as $enabled_capability ) {
					if ( current_user_can( $enabled_capability ) ) {
						return true;
					}
				}
			}

			return false;
		}

		/**
		 * Cancel an order document
		 *
		 * @param int    $order_id      the order id.
		 * @param string $document_type the type of document to reset.
		 * @param mixed  $extension     The document extension.
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function reset_document( $order_id, $document_type, $extension ) {

			if ( $this->user_can_delete_document( $order_id, $document_type ) ) {

				$document = ywpi_get_order_document_by_type( $order_id, $document_type );

				if ( $this->check_invoice_url_for_action( $document ) ) {
					$document->reset();
					wp_delete_file( $document->get_full_path() );
				}
			}
		}


		/**
		 * Add a button to print invoice, if exists, from order page on frontend.
		 *
		 * @param array    $actions current actions.
		 * @param WC_Order $order   the order object.
		 *
		 * @return array
		 */
		public function print_invoice_button( $actions, $order ) {
			$invoice = new YITH_Invoice( yit_get_prop( $order, 'id' ) );

			if ( $invoice->generated() ) {
				// Add the print button.
				$actions['print-invoice'] = array(
					'url'  => $this->get_action_url( 'view', 'invoice', yit_get_prop( $order, 'id' ) ),
					'name' => esc_html__( 'Invoice', 'yith-woocommerce-pdf-invoice' ),
				);
			}

			return $actions;
		}

		/**
		 * Replace the customer details using the plugin option pattern
		 *
		 * @param string $pattern The pattern.
		 * @param int    $order_id The order id.
		 *
		 * @return string
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function replace_customer_details_pattern( $pattern, $order_id ) {

			preg_match_all( '/{{([^}}]*)}}/', $pattern, $matches );

			$customer_details = $pattern;
			if ( isset( $matches[1] ) ) {
				foreach ( $matches[1] as $match ) {

					$order_id = wc_get_order( $order_id )->get_parent_id() != 0 ? wc_get_order( $order_id )->get_parent_id() : $order_id; //phpcs:ignore

					$replace_value = yit_get_prop( wc_get_order( $order_id ), $match, true );

					if ( empty( $replace_value ) ) {
						$replace_value = get_post_meta( $order_id, $match, true );
					}

					// Convert country code and convert it to the country name.
					if ( ( '_billing_country' == $match ) || ( '_shipping_country' == $match ) ) { //phpcs:ignore
						$countries = WC()->countries->get_countries();
						if ( isset( $countries[ $replace_value ] ) ) {
							$replace_value = $countries[ $replace_value ];
						}
					}

					// Convert state code and convert it to the state name.
					if ( ( '_shipping_state' == $match ) ) { //phpcs:ignore
						$state_code   = yit_get_prop( wc_get_order( $order_id ), '_shipping_state', true );
						$country_code = yit_get_prop( wc_get_order( $order_id ), '_shipping_country', true );
						$states       = WC()->countries->get_states();

						if ( isset( $states[ $country_code ][ $state_code ] ) ) {
							$replace_value = $states[ $country_code ][ $state_code ];
						}
					}

					if ( ( '_billing_state' == $match ) ) { //phpcs:ignore
						$state_code   = yit_get_prop( wc_get_order( $order_id ), '_billing_state', true );
						$country_code = yit_get_prop( wc_get_order( $order_id ), '_billing_country', true );
						$states       = WC()->countries->get_states();

						if ( isset( $states[ $country_code ][ $state_code ] ) ) {
							$replace_value = $states[ $country_code ][ $state_code ];
						}
					}

					if ( '_date_paid' === $match && $replace_value instanceof WC_DateTime ) {
						$date_format = get_option( 'ywpi_invoice_date_format', 'd/m/Y' );
						$replace_value = $replace_value->date( $date_format );
					}

					$replace_value = apply_filters( 'yith_ywpi_replace_customer_details', $replace_value, $match, $order_id );

					/*
					 * Integration with YITH WooCommerce EU VAT
					 */
					if ( ( '_yith_eu_vat' ) === $match ) {
						$eu_vat_data   = yit_get_prop( wc_get_order( $order_id ), '_ywev_order_vat_paid', true );
						$replace_value = ( is_array( $eu_vat_data ) && array_key_exists( 'vat_number', $eu_vat_data ) ) ? $eu_vat_data['vat_number'] : '';
					}

					/*
					 * Integration with YITH WooCommerce Checkout Manager
					 */
					if ( defined( 'YWCCP' ) ) {
						$fields = ywccp_get_custom_fields( 'additional' );
						foreach ( $fields as $key => $field ) {
							if ( $key == $match || '_' . $key == $match ) { //phpcs:ignore
								$key           = apply_filters( 'yith_ywpi_checkout_manager_additional_field_key', $key );
								$replace_value = yit_get_prop( wc_get_order( $order_id ), $key, true );
							}
						}
					}

					do_action( 'yith_ywpi_before_replace_customer_details' );

					$customer_details = str_replace( '{{' . $match . '}}', $replace_value, $customer_details );
				}
			}

			// Clean up white space.
			$replace_details = preg_replace( '/  +/', ' ', trim( $customer_details ) );

			$replace_details = preg_replace( '/\n\n+/', "\n", $replace_details );

			$replace_details = apply_filters( 'yith_pdf_invoice_customer_replace_details', $replace_details );

			// Remove blank lines.
			// $replace_details = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $replace_details); .

			// Break newlines apart and remove empty lines/trim commas and white space.
			$replace_details = explode( "\n", $replace_details );

			// Add html breaks.
			$replace_details = implode( '<br/>', apply_filters( 'yith_pdf_invoice_customer_details_pattern', $replace_details ) );

			return $replace_details;
		}

		/**
		 * Get the customer billing details
		 *
		 * @param int $order_id the order id.
		 *
		 * @return string
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function get_customer_billing_details( $order_id ) {
			$customer_details = ywpi_get_option( 'ywpi_customer_billing_details', '' );

			return $this->replace_customer_details_pattern( $customer_details, $order_id );
		}

		/**
		 * Get the customer shipping details
		 *
		 * @param int $order_id the order id.
		 *
		 * @return string
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function get_customer_shipping_details( $order_id ) {
			$customer_details = ywpi_get_option( 'ywpi_customer_shipping_details', '' );

			return $this->replace_customer_details_pattern( $customer_details, $order_id );
		}

		/**
		 * Get the footer details
		 *
		 * @param int    $order_id the order id.
		 * @param object $document the document object.
		 */
		public function get_footer_details( $order_id, $document ) {

			if ( $document instanceof YITH_Invoice ) {
				$customer_details = ywpi_get_option( 'ywpi_invoice_footer', '' );
			}

			if ( $document instanceof YITH_Credit_Note ) {
				$customer_details = ywpi_get_option( 'ywpi_credit_note_footer', '' );
			}

			if ( $document instanceof YITH_Pro_Forma ) {
				$customer_details = ywpi_get_option( 'ywpi_pro_forma_footer', '' );
			}

			if ( $document instanceof YITH_Shipping ) {
				$customer_details = ywpi_get_option( 'ywpi_packing_slip_footer', '' );
			}

			$customer_details = str_replace( '|', '-', $customer_details );

			return $this->replace_customer_details_pattern( $customer_details, $order_id );
		}


		/**
		 * Return the folder where documents have to be stored. Create the folder path if not exists.
		 *
		 * @param YITH_Document $document  The document object.
		 * @param mixed         $extension The document extension.
		 *
		 * @return string
		 */
		private function create_storing_folder( $document, $extension = 'pdf' ) {

			/* Create folders for storing documents */
			$folder_path = get_option( 'ywpi_invoice_folder_format' );

			$date_to_show = ywpi_get_option( 'ywpi_date_to_show_in_invoice' );

			switch ( $date_to_show ) {
				case 'new':
					$date = getdate( strtotime( $document->order->get_date_created() ) );
					break;

				case 'completed':
					if ( $document->order->get_status() == 'completed' && ! $document->order instanceof WC_Order_Refund ) { //phpcs:ignore
						$date = getdate( strtotime( $document->order->get_date_completed() ) );
					} elseif ( ! $document instanceof YITH_Shipping && ! $document instanceof YITH_Pro_Forma ) {
						$date = getdate( strtotime( $document->date ) );
					} else {
						$date = getdate( strtotime( $document->order->get_date_created() ) );
					}
					break;

				case 'invoice_creation':
					if ( ! $document instanceof YITH_Shipping && ! $document instanceof YITH_Pro_Forma ) {
						$date = getdate( strtotime( $document->date ) );
					} else {
						$date = getdate( strtotime( $document->order->get_date_created() ) );
					}
					break;

				default:
					$date = getdate( strtotime( $document->order->get_date_created() ) );
					break;
			}

			$folder_path = str_replace(
				array(
					'[year]',
					'[month]',
					'[day]',
				),
				array(
					$date['year'],
					sprintf( '%02d', $date['mon'] ),
					sprintf( '%02d', $date['mday'] ),
				),
				$folder_path
			);

			$folder_path = apply_filters( 'ywpi_storing_folder', $folder_path, $document, $extension );

			if ( ! file_exists( YITH_YWPI_DOCUMENT_SAVE_DIR . $folder_path ) ) {
				wp_mkdir_p( YITH_YWPI_DOCUMENT_SAVE_DIR . $folder_path );
			}

			return $folder_path;
		}


		/**
		 * Retrieve a PDF or XML file for a specific document
		 *
		 * @param YITH_Document $document the document for which a PDF/XML file should be created.
		 * @param mixed         $extension The document extension.
		 *
		 * @return int
		 * @author Alessio Torrisi
		 * @since  1.9.0
		 */
		public function create_file( $document, $extension = 'pdf' ) {

			/*  Some document type will cause the next available number to be incremented */

			if ( $document instanceof YITH_Credit_Note ) {
				$invoice_number = get_post_meta( $document->order->get_id(), '_ywpi_credit_note_number', true );
			} else {
				$invoice_number = get_post_meta( $document->order->get_id(), '_ywpi_invoice_number', true );
			}

			if ( ! $invoice_number ) {

				if ( ! $document instanceof YITH_Shipping && ! $document instanceof YITH_Pro_Forma ) {
					update_post_meta( $document->order->get_id(), '_ywpi_invoice_number', $document->number );
				}

				if ( ! $document->generated() ) {

					$this->increment_next_document_number( $document );
				}
			}
			$content = $this->generate_template( $document, $extension );

			$document->save_folder = $this->create_storing_folder( $document, $extension );

			$document->save_path = sprintf( '%s.%s', $this->get_document_filename( $document, $extension ), $extension );

			return file_put_contents( $document->get_full_path(), $content ); //phpcs:ignore
		}

		/**
		 * Return the filename associated to the document, based on plugin settings.
		 *
		 * @param YITH_Document $document  The document object.
		 * @param mixed         $extension The document extension.
		 *
		 * @return mixed|string|void
		 */
		public function get_document_filename( $document, $extension = 'pdf' ) {

			if ( 'xml' === strval( $extension ) ) {

				if ( $document->save_path_xml != '' ) { //phpcs:ignore
					return basename( $document->save_path_xml, '.xml' );
				} else {
					return YITH_Electronic_Invoice()->get_next_progressive_filename();
				}
			}

			$pattern = '';

			$is_receipt = get_post_meta( $document->order->get_id(), '_billing_invoice_type', true );

			if ( ( $document instanceof YITH_Invoice && 'receipt' !== strval( $is_receipt ) ) ||
				( $document instanceof YITH_Credit_Note )
			) {

				if ( $document instanceof YITH_Invoice && 'pdf' === strval( $extension ) ) {
					$option_name = 'ywpi_invoice_filename_format';
				} elseif ( $document instanceof YITH_Credit_Note ) {
					$option_name = 'ywpi_credit_note_filename_format';
				}

				// Filter to change filename format of invoice or credit note document. Use placeholder [number] to get automatically order of invoice or credit note.
				$pattern = apply_filters( 'ywpi_pattern_filename_invoice_or_credit_note', ywpi_get_option_with_placeholder( $option_name, '[number]' ), $document );

				$order_number = $document->order instanceof WC_Order ? $document->order->get_order_number() : '';

				$pattern = str_replace(
					array(
						'[number]',
						'[prefix]',
						'[suffix]',
						'[order_number]',
						'[shop_ssn]',
					),
					array(
						$document->number,
						$document->prefix,
						$document->suffix,
						$order_number,
						get_option( 'ywpi_electronic_invoice_transmitter_id' ),
					),
					$pattern
				);
			} elseif ( $document instanceof YITH_Pro_Forma ) {
				// Filter to change filename format of proforma document. Use placeholder [order_number] to get order number.
				$pattern = apply_filters( 'ywpi_pattern_filename_proforma', ywpi_get_option_with_placeholder( 'ywpi_pro_forma_invoice_filename_format', '[order_number]' ), $document );

				$pattern = apply_filters( 'ywpi_filename_proforma', str_replace( '[order_number]', $document->order->get_order_number(), $pattern ), $pattern, $document );

			} elseif ( $document instanceof YITH_Shipping ) {
				// Filter to change filename format of packing slip document. Use placeholder [order_number] to get order number.
				$pattern = apply_filters( 'ywpi_pattern_filename_shipping', ywpi_get_option_with_placeholder( 'ywpi_packing_slip_filename_format', '[order_number]' ), $document );
				$pattern = apply_filters( 'ywpi_filename_packing_slip', str_replace( '[order_number]', $document->order->get_order_number(), $pattern ), $pattern, $document );

			} elseif ( $document instanceof YITH_Invoice && 'receipt' === strval( $is_receipt ) ) {

				$pattern = apply_filters( 'ywpi_filename_receipt', esc_html__( 'receipt', 'yith-woocommerce-pdf-invoice' ) . '_' . $document->order->get_order_number(), $document );
			}

			$date_to_show = ywpi_get_option( 'ywpi_date_to_show_in_invoice' );

			switch ( $date_to_show ) {
				case 'new':
					$date = getdate( strtotime( $document->order->get_date_created() ) );
					break;

				case 'completed':
					if ( $document->order->get_status() == 'completed' && ! $document->order instanceof WC_Order_Refund ) { //phpcs:ignore
						$date = getdate( strtotime( $document->order->get_date_completed() ) );
					} elseif ( ! $document instanceof YITH_Shipping && ! $document instanceof YITH_Pro_Forma ) {
						$date = getdate( strtotime( $document->date ) );
					} else {
						$date = getdate( strtotime( $document->order->get_date_created() ) );
					}
					break;

				case 'invoice_creation':
					if ( ! $document instanceof YITH_Shipping && ! $document instanceof YITH_Pro_Forma ) {
						$date = getdate( strtotime( $document->date ) );
					} else {
						$date = getdate( strtotime( $document->order->get_date_created() ) );
					}
					break;

				default:
					$date = getdate( strtotime( $document->order->get_date_created() ) );
					break;
			}

			$pattern = str_replace(
				array(
					'[year]',
					'[month]',
					'[day]',
				),
				array(
					$date['year'],
					sprintf( '%02d', $date['mon'] ),
					sprintf( '%02d', $date['mday'] ),
				),
				$pattern
			);

			// Sanitize the filename.
			$pattern = apply_filters( 'yith_ywpi_sanitize_document_pattern', true ) ? preg_replace( '/[^a-z0-9-_.#\s]/i', '_', $pattern ) : $pattern ;

			return $pattern;
		}


		// ------------------------------------------------------------------------------------------------------------------

		/**
		 * Set a maximum execution time
		 *
		 * @param int $seconds time in seconds.
		 */
		private function set_time_limit( $seconds ) {
			$check_safe_mode = ini_get( 'safe_mode' );
			if ( ( ! $check_safe_mode ) || ( 'OFF' == strtoupper( $check_safe_mode ) ) ) { //phpcs:ignore

				@set_time_limit( $seconds ); //phpcs:ignore
			}
		}

		/**
		 * Generate the PDF template (with the MPDF library)
		 *
		 * @param  mixed $document The document object.
		 * @return Mpdf object
		 */
		private function generate_template_mpdf( $document ) {
			$this->set_time_limit( 120 );
			$template_path     = yith_ywpi_get_template_path();
			$template_selected = yith_ywpi_get_selected_template();

			// Get HTML of the template.
			$main_class = '';
			if ( 'modern' === $template_selected ) {
				$main_class = 'modern-template';
			}
			ob_start();
			wc_get_template(
				'yith-pdf-invoice/invoice-template.php',
				array(
					'document'   => $document,
					'main_class' => apply_filters( 'yith_ywpi_add_body_class', $main_class ),
				),
				'',
				YITH_YWPI_TEMPLATE_DIR
			);
			$html = ob_get_clean();
			$html = apply_filters( 'yith_ywpi_before_pdf_rendering_html', $html, $document );

			// Get style of the template.
			ob_start();
			wc_get_template(
				$template_path . 'invoice-style.css',
				null,
				'',
				YITH_YWPI_TEMPLATE_DIR
			);
			$style = ob_get_clean();

			// Get footer of the template.
			ob_start();
			wc_get_template(
				'yith-pdf-invoice/document-footer.php',
				array(
					'document' => $document,
				),
				'',
				YITH_YWPI_TEMPLATE_DIR
			);
			$footer = ob_get_clean();
			require_once realpath( __DIR__ . '/..' ) . '/lib/vendor/autoload.php';

			$mpdf_args = apply_filters(
				'yith_ywpdi_mpdf_args',
				array(
					'setAutoBottomMargin' => 'stretch',
					'autoScriptToLang'    => true,
					'autoLangToFont'      => true,
				)
			);

			if ( is_array( $mpdf_args ) ) {
				$mpdf = new \Mpdf\Mpdf( $mpdf_args );
			} else {
				$mpdf = new \Mpdf\Mpdf();
			}

			/* Template color */
			$body_color = ( 'default' === $template_selected ) ? get_option( 'ywpi_body_color' ) : get_option( 'ywpi_body_color_' . $template_selected );
			if ( 'modern' === $template_selected ) {
				$color_options = ywpi_get_option( 'ywpi_body_color_modern' );
				$first_color   = isset( $color_options['first'] ) ? $color_options['first'] : '#2F4354';
				$second_color  = isset( $color_options['second'] ) ? $color_options['second'] : '#2F4354';

				$body_color = 'repeating-linear-gradient(' . esc_html( $first_color ) . ' 0px, ' . $first_color . ' 25%, ' . $second_color . ' 25%, ' . $second_color . ' 50%)';
			}
			$mpdf->SetDefaultBodyCSS( 'background', $body_color );

			/* Direction: right to left or left to right */
			$direction = is_rtl() ? 'rtl' : 'ltr';

			$mpdf->directionality = apply_filters( 'yith_ywpdi_mpdf_directionality', $direction );

			/* Footer on last page */
			$footer_last_page = strval( get_option( 'ywpi_invoice_footer_last_page', 'no' ) );

			if ( 'no' === $footer_last_page ) {
				$mpdf->setFooter( $footer, '' );
			}

			do_action( 'yith_ywpi_after_write_mpdf_html_template_pdf', $mpdf, $document );

			$style = apply_filters( 'yith_ywpi_style_before_pdf_creation', $style, $template_selected, $document );

			$mpdf->WriteHTML( $style, 1 );
			$mpdf->WriteHTML( $html, 2 );

			if ( 'yes' === $footer_last_page ) {
				$mpdf->setFooter( $footer, '' );
			}

			do_action( 'yith_ywpi_after_generate_template_pdf', $mpdf, $document );

			// The next call will store the entire PDF as a string in $pdf.
			$pdf = $mpdf->Output( 'document', 'S' );

			return $pdf;
		}


		/**
		 * Generate the XML template.
		 *
		 * @param  mixed $document The document object.
		 * @param  mixed $type     The document type.
		 * @return string
		 */
		private function generate_template_xml( $document, $type = 'electronic-invoice-ita' ) {

			ob_start();
			wc_get_template(
				'yith-pdf-invoice/xml/' . $type . '/main.php',
				array(
					'document' => $document,
					'type'     => $type,
				),
				'',
				YITH_YWPI_TEMPLATE_DIR
			);

			$content = ob_get_clean();

			return $content;
		}


		/**
		 * Generate the template for a document.
		 *
		 * @param YITH_Document $document the document to create.
		 * @param mixed         $extension The document extension.
		 *
		 * @return string
		 * @author YITH
		 * @since  1.0.0
		 */
		private function generate_template( $document, $extension = 'pdf' ) {

			do_action( 'yith_ywpdi_before_generate_template', $document, $extension );

			$file_content = 'xml' === strval( $extension ) ? $this->generate_template_xml( $document ) : $this->generate_template_mpdf( $document );

			return $file_content;
		}

		/**
		 * Action links
		 *
		 * @param  string $links The action links.
		 * @return string.
		 * @since  1.8.5
		 * @author YITH
		 */
		public function action_links( $links ) {

			$links = yith_add_action_links( $links, $this->_panel_page, false, YITH_YWPI_SLUG );
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
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_YWPI_FREE_INIT' ) {

			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) { //phpcs:ignore
				$new_row_meta_args['slug'] = YITH_YWPI_SLUG;
			}

			return $new_row_meta_args;
		}


	}
}
