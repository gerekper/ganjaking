<?php
if ( ! defined ( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists ( 'YITH_WooCommerce_Pdf_Invoice_Premium' ) ) {

	/**
	 * Implements features of YITH WooCommerce Pdf Invoice
	 *
	 * @class   YITH_WooCommerce_Pdf_Invoice_Premium
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 */
	class YITH_WooCommerce_Pdf_Invoice_Premium extends YITH_WooCommerce_Pdf_Invoice {

		/**
		 * @var bool set if proforma invoice are available
		 */
		public $enable_pro_forma = false;

		/**
		 * @var bool set if a proforma invoice should be sent
		 */
		public $send_pro_forma = false;

		/**
		 * @var bool set if credit notes are enabled
		 */
		public $enable_credit_note = false;

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
			if ( is_null ( self::$instance ) ) {
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
			parent::__construct ();

            /**
             * Including the GDRP
             */
            add_action( 'plugins_loaded', array( $this, 'load_privacy' ), 20 );

			add_filter ( 'woocommerce_admin_settings_sanitize_option_ywpi_dropbox_key', array(
				$this,
				'custom_save_ywpi_dropbox'
			) );


			add_action ( 'woocommerce_update_option', array( $this, 'copy_invoice_logo_to_local_path' ) );

			/**
			 * Sync new documents with DropBox
			 */
			add_action ( 'yith_ywpi_document_created', array(
					$this,
					'sync_to_dropbox'
				)
			);

            add_action ( 'yith_ywpi_document_regenerated', array(
                    $this,
                    'sync_to_dropbox'
                )
            );


            // Register custom fields of checkout
            add_action ( 'woocommerce_checkout_update_order_meta', array( $this, 'yith_ywpi_add_item_metas' ), 10, 1 );

            //Hide the order metas created
            add_filter('woocommerce_hidden_order_itemmeta',  array( $this, 'yith_ywpi_hidden_order_itemmeta' ), 10, 1);


            /**
			 * Check for third party plugin compatibility
			 */
			$this->check_third_part_compatibility ();

			/**
			 * Add some fields on checkout process, that can be added on invoices.
			 */
			YITH_Checkout_addon::get_instance ()->initialize ();

			add_action ( 'yith_pdf_invoice_before_total', array(
				$this,
				'show_gift_card_discount'
			) );


			add_action( 'init', array($this,'force_update_option_invoice_date_to_show_in_invoice') );
		}

        /**
         * Including the GDRP
         */
        public function load_privacy() {

            if ( class_exists( 'YITH_Privacy_Plugin_Abstract' ) )
                require_once( YITH_YWPI_DIR . 'lib/class.yith-woocommerce-pdf-invoice-privacy.php' );

        }

		public function show_gift_card_discount( $order ) {
			$gift_card_total = get_post_meta ( yit_get_prop ( $order, 'id' ), '_ywgc_applied_gift_cards_totals', true );

			if ( ! empty( $gift_card_total ) ) {
				ob_start ();
				?>
				<tr>
					<td class="left-content column-product"><?php esc_html_e( "Gift card discount:", 'yith-woocommerce-pdf-invoice' ); ?></td>
					<td class="right-content column-total"><?php echo wc_price ( $gift_card_total ); ?></td>
				</tr>
				<?php
				$res = ob_get_clean ();
				echo apply_filters ( 'yith_pdf_invoice_show_gift_card_amount', $res, $gift_card_total, $order );
			}
		}

		/**
		 * Sync new documents with DropBox
		 */
		public function sync_to_dropbox( $document ) {

			if ( ywpi_get_option( 'ywpi_dropbox_access_token' ) ) {
				YITH_PDF_Invoice_DropBox::get_instance()->send_document_to_dropbox( $document );
			}
		}

		/**
		 * Retrive the document title
		 *
		 * @param YITH_Document $document the document
		 *
		 * @author Lorenzo Giuffrida
		 * @return string
		 * @since  1.0.0
		 */
		public function get_document_title( $document ) {

			$is_receipt = get_post_meta( $document->order->get_id(), '_billing_invoice_type' , true );

			if ( $document instanceof YITH_Pro_Forma ) {
				$title = apply_filters('yith_ywpi_proforma_document_title', esc_html__( 'Proforma Invoice', 'yith-woocommerce-pdf-invoice' ),10,1);

				return $title;
			} elseif ( $document instanceof YITH_Credit_Note ) {
				$title = apply_filters('yith_ywpi_credit_note_document_title', esc_html__( 'Credit note', 'yith-woocommerce-pdf-invoice' ),10,1);

				return $title;
			} elseif ( $document instanceof YITH_Invoice && $is_receipt == 'receipt' ) {
				$title = apply_filters('yith_ywpi_receipt_document_title', esc_html__( 'Receipt', 'yith-woocommerce-pdf-invoice' ),10,1);

			return $title;
			}

			return parent::get_document_title ( $document );
		}

		/**
		 * Initialize the plugin options
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function init_plugin_options() {
			$this->enable_pro_forma   = ( 'yes' == ywpi_get_option ( 'ywpi_enable_pro_forma' ) );
			$this->send_pro_forma     = ( 'yes' == ywpi_get_option ( 'ywpi_send_pro_forma' ) );
			$this->enable_credit_note = ( 'yes' == ywpi_get_option ( 'ywpi_enable_credit_notes' ) );

			parent::init_plugin_options ();
		}

		/**
		 * Show the document section with the document data like number, data, ...
		 *
		 * @param YITH_Document $document
		 */
		public function yith_ywpi_template_document_details( $document ) {

			if ( $document instanceof YITH_Pro_Forma ) {
				wc_get_template ( 'yith-pdf-invoice/document-data-proforma.php',
					array(
						'document' => $document,
					),
					'',
					YITH_YWPI_TEMPLATE_DIR );
			}
		}

		/**
		 * Make a local copy of the image to be used as company logo
		 *
		 * @param array $option
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function copy_invoice_logo_to_local_path( $option ) {
			if ( 'ywpi_company_logo' === $option["id"] ) {
				if ( ! empty( $_POST["ywpi_company_logo"] ) ) {

					$upload_dir = wp_upload_dir ();
					$local_path = str_replace ( $upload_dir["baseurl"], $upload_dir["basedir"], $_POST["ywpi_company_logo"] );
					if ( file_exists($local_path))
					    copy ( $local_path, YITH_YWPI_INVOICE_LOGO_PATH );
				}
			}
		}


		/**
		 * Check for third party plugin compatibility needs
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function check_third_part_compatibility() {
			if ( ywpi_is_active_woo_eu_vat_number () ) {
				add_filter ( 'ywpi_general_options', array( $this, 'add_vat_number_source_option' ) );
			}
		}

		/**
		 * Add an option to choose the VAT number source
		 *
		 * @param array $settings current option list
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
					'yith'          => "Show the standard VAT number field from YITH WooCommerce PDF Invoice",
					'eu-vat-number' => "Use the VAT number field from WooThemes EU VAT number plugin",
				),
				'default' => 'yith',
			);

			$settings['general'] = array_slice ( $settings['general'], 0, count ( $settings['general'] ) - 2, true ) +
			                       array( 'vat_number_source' => $vat_number_source ) +
			                       array_slice ( $settings['general'], 3, count ( $settings['general'] ) - 1, true );

			return $settings;
		}

		/**
		 * Check if there are action to be done at this moment, related to back end
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function execute_generation_actions() {
			//  Check actions from the base version
			parent::execute_generation_actions ();

			//  Check generation actions
			if ( isset( $_GET[ YITH_YWPI_RESET_DROPBOX ] ) ) {
				YITH_PDF_Invoice_DropBox::get_instance ()->disable_dropbox_backup ();

				wp_redirect ( esc_url_raw ( remove_query_arg ( YITH_YWPI_RESET_DROPBOX ) ) );
			}
		}

		/**
		 * add the right action based on GET var current used
		 *
		 */
		public function execute_visualization_actions() {
			parent::execute_visualization_actions ();

			if ( isset( $_GET[ YITH_YWPI_CREATE_PRO_FORMA_INVOICE_ACTION ] ) ) {
				$this->get_pro_forma_invoice ( $_GET[ YITH_YWPI_CREATE_PRO_FORMA_INVOICE_ACTION ] );
			}
		}

		/**
		 * Create a new proforma invoice document if not exists, else return the previous one
		 *
		 * @param int $order_id the order id for which the document is created
		 */
		public function get_pro_forma_invoice( $order_id ) {

			if ( ! $this->enable_pro_forma ) {
				return;
			}

			$document = $this->create_document ( $order_id, 'proforma' );

			/*
			 * Check for url validation
			 */
			if ( ! $document ) {
				return;
			}

			//  Create the file and show it
			$this->show_file ( $document );
		}


		public function custom_save_ywpi_dropbox() {

			YITH_PDF_Invoice_DropBox::get_instance ()->custom_save_ywpi_dropbox ();
		}


		/**
		 * Add a button to print invoice, if exists, from order page on frontend.
		 * If not exists add a button for a proforma document.
		 *
		 * @param array    $actions current actions
		 * @param WC_Order $order   the order being shown
		 *
		 * @return mixed
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function print_invoice_button( $actions, $order ) {

			$invoice = new YITH_Invoice( yit_get_prop ( $order, 'id' ) );

			if ( $invoice->generated () ) {
				// Add the print button
				$actions['print-invoice'] = array(
					'url'  => $this->get_action_url ( 'view', 'invoice', yit_get_prop ( $order, 'id' ) ),
					'name' => apply_filters('yith_ywpi_print_invoice_name_frontend', esc_html__( 'Invoice', 'yith-woocommerce-pdf-invoice' )),
				);
			} else if ( $this->enable_pro_forma && apply_filters ( 'yith_ywpi_show_pro_forma_invoice_button_view_order', true, $order ) ) {

				$actions['print-pro-forma-invoice'] = array(
					'url'  => $this->get_action_url ( 'preview', 'proforma', yit_get_prop ( $order, 'id' ) ),
					'name' => apply_filters( 'yith_ywpi_pro_forma_button_text_my_account', esc_html__( 'Proforma', 'yith-woocommerce-pdf-invoice' ) ),
				);
			}

            $refunds = $order->get_refunds();

            foreach ( $refunds as $refund ){
                $credit_note = new YITH_Credit_Note( yit_get_prop ( $refund, 'id' ) );

                if ( $credit_note->generated () ) {
                    // Add the print button
                    $actions['print-credit-note'] = array(
                        'url'  => $this->get_action_url ( 'view', 'credit-note', yit_get_prop ( $refund, 'id' ) ),
                        'name' => apply_filters('yith_ywpi_print_credit_note_name_frontend', esc_html__( 'Credit Note', 'yith-woocommerce-pdf-invoice' )),
                    );
                }

            }

			return apply_filters ( 'yith_ywpi_my_order_actions', $actions, $order );
		}

		/**
		 * Retrieve the proforma invoice path
		 *
		 * @param string $status   current order status
		 * @param int    $order_id order id
		 *
		 * @return string
		 */
		public function get_pro_forma_attachment( $status, $order_id ) {

			$proforma_path = '';

			if ( $this->enable_pro_forma && $this->send_pro_forma ) {
				$attach_to_emails = apply_filters ( 'yith_ywpi_pro_forma_attachment_on_emails_ids',
					array(
						'customer_processing_order',
						'new_order',
						'customer_on_hold_order'
					), $order_id );

				if ( in_array ( $status, $attach_to_emails ) ) {

					$document = $this->create_document ( $order_id, 'proforma' );
					if ( $document && $document->generated () ) {
						$proforma_path = $document->get_full_path ();
					}
				}
			}

			return $proforma_path;
		}


		/** Add custom metas to the order items */
		public function yith_ywpi_add_item_metas( $order_id ){

		    $order = wc_get_order( $order_id );
		    $items = $order->get_items();

		    foreach ( $items as $item_id => $item ){

		        $product = $order->get_product_from_item( $item );

		        if( ! $product instanceof WC_Product ){
		            continue;
                }

		        $product_regular_price = $product->get_regular_price();
		        $product_sku = $product->get_sku();
		        $product_short_description = $product->get_short_description();

		        wc_update_order_item_meta( $item_id, '_ywpi_product_regular_price', $product_regular_price );
                wc_update_order_item_meta( $item_id, '_ywpi_product_sku', $product_sku );
                wc_update_order_item_meta( $item_id, '_ywpi_product_short_description', $product_short_description );

                do_action( 'yith_ywpi_custom_item_metas', $item_id, $order );
            }

        }

        public function yith_ywpi_hidden_order_itemmeta( $meta_array )
        {
            $meta_array[] = '_ywpi_product_regular_price';
            $meta_array[] = '_ywpi_product_sku';
            $meta_array[] = '_ywpi_product_short_description';

            return $meta_array;
        }

        /**
         * Action links
         *
         *
         * @return void
         * @since    1.8.5
         * @author   Daniel Sanchez <daniel.sanchez@yithemes.com>
         */
        public function action_links( $links ) {

            $links = yith_add_action_links( $links, $this->_panel_page, true );
            return $links;

        }

        /**
         * Plugin Row Meta
         *
         *
         * @return void
         * @since    1.8.5
         * @author   Daniel Sanchez <daniel.sanchez@yithemes.com>
         */
        public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_YWPI_INIT' ) {

            $new_row_meta_args = parent::plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file );

            if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ){
                $new_row_meta_args[ 'is_premium' ] = true;
            }

            return $new_row_meta_args;
        }


        /**
         * @since 2.1.3
         * Set automatically the option "Date to show in the invoice"
         */
        public function force_update_option_invoice_date_to_show_in_invoice(){

            $force_update_option_invoice_date_to_show_in_invoice = get_option( 'yith_ywpdi_force_update_option_invoice_date_to_show_in_invoice' );


            if( ! $force_update_option_invoice_date_to_show_in_invoice ){ // Remove NOT logic condition to execute again the process (if the option is already saved)


                if( ywpi_get_option( 'ywpi_invoice_generation' ) == 'auto' ){

                    $create_on = ywpi_get_option( 'ywpi_create_invoice_on' );

                    $value = $create_on == 'processing' ? 'invoice_creation' : $create_on;

                }else{

                    $value = 'completed';

                }

                update_option( 'ywpi_date_to_show_in_invoice',$value );

                update_option( 'yith_ywpdi_force_update_option_invoice_date_to_show_in_invoice',true );


            }

        }

	}
}
