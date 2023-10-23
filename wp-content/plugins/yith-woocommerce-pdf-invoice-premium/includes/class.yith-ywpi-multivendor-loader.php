<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase, WordPress.Files.FileName.InvalidClassFileName

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_YWPI_Multivendor_Loader' ) ) {
	/**
	 * Implements features related to an invoice document
	 *
	 * @class   YITH_YWPI_Multivendor_Loader
	 * @package YITH\PDF_Invoice\Classes
	 * @since   1.0.0
	 * @author  YITH <plugins@yithemes.com>
	 */
	class YITH_YWPI_Multivendor_Loader {

		/**
		 * Panel Object
		 *
		 * @var object $panel
		 */
		protected $panel;

		/**
		 * YITH WooCommerce Pdf invoice panel page
		 *
		 * @var string
		 */
		protected $panel_page = 'yith-plugins_page_pdf_invoice_for_multivendor';

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_YWPI_Multivendor_Loader
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * If vendors can manage invoices for their orders
		 *
		 * @var bool
		 */
		public $vendor_invoice_enabled = false;

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
		 * Constructor function.
		 *
		 * @return void
		 */
		public function __construct() {
			if ( ! defined( 'YITH_WPV_PREMIUM' ) ) {
				return;
			}

			$this->vendor_invoice_enabled = 'yes' === strval( get_option( 'yith_wpv_vendors_enable_pdf_invoice', false ) );

			// If vendors can't create invoices for their own orders, remove all buttons.
			if ( ! $this->vendor_invoice_enabled ) {
				add_filter( 'yith_ywpi_show_invoice_button_order_list', array( $this, 'remove_invoice_button_on_order_list' ), 10, 2 );

				add_filter( 'yith_ywpi_show_invoice_button_order_page', array( $this, 'remove_invoice_button_on_order_page' ), 10, 2 );

				add_filter( 'yith_ywpi_show_metabox_for_order', array( $this, 'show_metabox_on_suborders' ), 10, 2 );
			} else {
				// Vendors can create and manage invoices.

				/**
				 * Create a plugin option menu for vendors
				 */
				add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

				/**
				 * Modify the option name for YITH MultiVendor compatibility to fit the single vendor option name
				 */
				add_filter( 'ywpi_option_name', array( $this, 'vendor_option_name' ), 10, 2 );

				/**
				 * Set the storing folder for the vendor
				 */
				add_filter( 'ywpi_storing_folder', array( $this, 'get_vendor_storing_folder' ), 10, 2 );

				/**
				 * Filter the order items by vendor
				 */
				add_filter( 'yith_ywpi_get_order_items_for_invoice', array( $this, 'get_order_items_for_invoice' ), 10, 2 );

				/**
				 * Filter the order fee by vendor
				 */
				add_filter( 'yith_ywpi_get_order_fee_for_invoice', array( $this, 'get_order_fee_and_shipping_for_invoice' ), 10, 2 );

				/**
				 * Filter the order shipping by vendor
				 */
				add_filter( 'yith_ywpi_get_order_shipping_for_invoice', array( $this, 'get_order_fee_and_shipping_for_invoice' ), 10, 2 );

				/**
				 * Remove the default buttons related to  YITH PDF Invoice on "my-account" page
				 */
				add_filter( 'yith_ywpi_my_order_actions', array( $this, 'show_multi_invoice_button' ), 10, 2 );

				/**
				 * Show a table with invoice information in case of orders with suborders
				 */
				add_action( 'woocommerce_order_details_after_order_table', array( $this, 'show_suborder_invoice' ) );

				/**
				 * Disable the automatically generated invoices
				 */
				add_filter( 'yith_ywpi_create_automatic_invoices', array( $this, 'stop_automatic_invoices' ) );

				/**
				 * Get the corrected value for an invoice subtotal
				 */
				add_filter( 'yith_ywpi_invoice_subtotal', array( $this, 'get_parent_order_subtotal' ), 10, 4 );

				/**
				 * Get the corrected value for an invoice subtotal
				 */
				add_filter( 'yith_ywpi_invoice_subtotal', array( $this, 'get_parent_order_subtotal' ), 10, 4 );

				/**
				 * Get the corrected value for order total taxes
				 */
				add_filter( 'yith_ywpi_invoice_tax_totals', array( $this, 'get_parent_order_taxes' ), 10, 2 );

				add_action( 'yith_ywpi_template_order_number', array( $this, 'show_suborder_number_in_document' ) );

				add_filter( 'yith_ywpi_delete_document_capabilities', array( $this, 'enable_document_deletion' ) );
			}

			add_action( 'yith_wcmv_after_suborder_details', array( $this, 'show_order_invoiced_status' ) );

			add_filter( 'yith_ywpi_show_packing_slip_button_order_page', array( $this, 'show_packing_slip_buttons_in_order' ), 10, 2 );

			add_filter( 'ywpi_general_options', array( $this, 'notify_pro_forma_option_disabled' ) );
		}

		/**
		 * Get a vendor ID. Useful to add compatibility with Multi Vendor 4.0
		 *
		 * @since 4.0.0
		 * @param YITH_Vendor $vendor The vendor instance.
		 * @return integer
		 */
		public function get_vendor_id( $vendor ) {
			return method_exists( $vendor, 'get_id' ) ? $vendor->get_id() : $vendor->id;
		}

		/**
		 * Get suborders from given order ID
		 *
		 * @since 4.0.0
		 * @param integer $order_id The parent order ID.
		 * @return array
		 */
		public function get_suborders( $order_id ) {
			if ( version_compare( YITH_WPV_VERSION, '4.0.0', '>=' ) ) {
				$suborders = YITH_Vendors_Orders::get_suborders( $order_id );
			} else {
				$suborders = YITH_Orders_Premium::get_suborder( $order_id );
			}

			return $suborders;
		}

		/**
		 * Get vendor role admin cap
		 *
		 * @since 4.0.0
		 * @return string
		 */
		public function get_vendor_admin_role_cap() {
			return version_compare( YITH_WPV_VERSION, '4.0.0', '>=' ) ? YITH_Vendors_Capabilities::ROLE_ADMIN_CAP : YITH_Vendors()->admin->get_special_cap();
		}

		/**
		 * Notify that pro-forma document is not available using YITH Multi Vendor.
		 *
		 * @param  mixed $options The array of options.
		 * @return array
		 */
		public function notify_pro_forma_option_disabled( $options ) {
			$options['settings-general']['pro-forma']['desc'] .= '<br><b>' . esc_html__( 'This feature is not available when used with YITH Multi Vendor plugin.', 'yith-woocommerce-pdf-invoice' ) . '</b>';

			return $options;
		}

		/**
		 * Enable document deletion for vendors, adding their own capabilities to allowed capabilities
		 *
		 * @param array $capabilities The capabilites.
		 *
		 * @return array
		 */
		public function enable_document_deletion( $capabilities ) {
			$capabilities[] = $this->get_vendor_admin_role_cap();

			return $capabilities;
		}

		/**
		 * Set the visibility for packing slip buttons in sub orders
		 *
		 * @param bool     $visible Is visible or not.
		 * @param WC_Order $order   The order object.
		 *
		 * @return bool
		 */
		public function show_packing_slip_buttons_in_order( $visible, $order ) {
			$current_order_id = $order->get_id();
			$parent_order_id  = get_post_field( 'post_parent', $current_order_id );

			if ( $parent_order_id ) {
				$visible = false;
			}

			return $visible;
		}

		/**
		 * Set if the metabox with the invoicing buttons should be shown for a specific
		 * order
		 *
		 * @param bool    $visible Is visible or not.
		 * @param WP_Post $post The post object.
		 *
		 * @return bool
		 */
		public function show_metabox_on_suborders( $visible, $post ) {
			if ( $post->post_parent ) {
				$visible = false;
			}

			return $visible;
		}

		/**
		 * Show the suborder number in the document.
		 *
		 * @param YITH_Document $document The document object.
		 */
		public function show_suborder_number_in_document( $document ) {
			/**  If there are parent order for the current document order being generated,
			 * show the parent order number in invoice
			 * */
			$current_order_id = $document->order->get_id();
			$parent_order_id  = get_post_field( 'post_parent', $current_order_id );

			/**
			 * APPLY_FILTERS: yith_ywpi_show_parent_order_number
			 *
			 * Filter the condition to display the parent order number in the document.
			 *
			 * @param bool true to display it, false to not.
			 * @param object $document the document object.
			 *
			 * @return bool
			 */
			if ( $parent_order_id && apply_filters( 'yith_ywpi_show_parent_order_number', true, $document ) ) {
				$parent_order = wc_get_order( $parent_order_id );

				if ( $parent_order ) {
					// translators: The order number.
					echo '<br><span style="font-size: small">' . sprintf( esc_html__( '(Main order #%s)', 'yith-woocommerce-pdf-invoice' ), wp_kses_post( $parent_order->get_order_number() ) ) . '</span>';
				}
			}
		}

		/**
		 * Check if a vendor is related to a super user
		 *
		 * @param YITH_Vendor $vendor The vendor object.
		 *
		 * @return mixed
		 * @since  1.0.0
		 */
		public function is_vendor_super_admin( $vendor ) {
			$user_id = ( $vendor && $vendor->is_valid() ) ? $vendor->get_owner() : get_current_user_id();

			return user_can( $user_id, 'manage_woocommerce' ); // phpcs:ignore WordPress.WP.Capabilities.Unknown
		}

		/**
		 * Retrieve the amount of taxes for the parent order
		 *
		 * @param float    $tax_totals The tax totals.
		 * @param WC_Order $order      The order object.
		 *
		 * @return mixed
		 * @since  1.0.0
		 */
		public function get_parent_order_taxes( $tax_totals, $order ) {
			$sub_orders = $this->get_suborders( $order->get_id() );

			if ( ! $sub_orders ) {
				return $tax_totals;
			}

			$order_taxes = array();

			$parent_order_items = $order->get_items( array( 'line_item', 'fee' ) );

			foreach ( $parent_order_items as $item_id => $item ) {
				if ( isset( $item['product_id'] ) ) {
					$product_id = $item['product_id'];
					$vendor     = yith_get_vendor( $product_id, 'product' );

					if ( ! $vendor || ! $vendor->is_valid() || ! $this->is_vendor_super_admin( $vendor ) ) {
						continue;
					}
				}

				// For the product there aren't vendor or the vendor is the admin, so the tax amount should be used.
				$line_tax_data = maybe_unserialize( $item['line_tax_data'] );

				if ( isset( $line_tax_data['total'] ) ) {
					foreach ( $line_tax_data['total'] as $tax_rate_id => $tax ) {
						if ( ! isset( $order_taxes[ $tax_rate_id ] ) ) {
							$order_taxes[ $tax_rate_id ] = 0;
						}

						if ( is_array( $tax ) ) {
							$tax = array_shift( $tax );
						}

						$order_taxes[ $tax_rate_id ] += $tax;
					}
				}
			}

			foreach ( $order->get_items( array( 'shipping' ) ) as $item_id => $item ) {
				$line_tax_data = maybe_unserialize( $item['taxes'] );

				if ( isset( $line_tax_data ) ) {
					foreach ( $line_tax_data as $tax_rate_id => $tax ) {
						if ( ! isset( $order_taxes[ $tax_rate_id ] ) ) {
							$order_taxes[ $tax_rate_id ] = 0;
						}

						if ( is_array( $tax ) ) {
							$tax = array_shift( $tax );
						}

						$order_taxes[ $tax_rate_id ] += $tax;
					}
				}
			}

			$results = array();

			foreach ( ( $order_taxes ) as $tax_rate_id => $tax_amount ) {
				if ( ! isset( $results[ $tax_rate_id ] ) ) {
					$obj = new stdClass();

					$obj->label              = WC_Tax::get_rate_label( $tax_rate_id );
					$obj->amount             = 0;
					$results[ $tax_rate_id ] = $obj;
				}

				if ( is_array( $tax_amount ) ) {
					$tax_amount = array_shift( $tax );
				}

				$results[ $tax_rate_id ]->amount += $tax_amount;
			}

			return $results;
		}

		/**
		 * Get the correct value for subtotal in parent order invoice, removing all the items that do not belong to the parent order.
		 *
		 * @param float    $subtotal         The subtotal.
		 * @param WC_Order $order            The order object.
		 * @param float    $product_discount The product discount.
		 * @param float    $order_fee_amount The fee amount of the order.
		 *
		 * @return float
		 * @since  1.0.0
		 */
		public function get_parent_order_subtotal( $subtotal, $order, $product_discount, $order_fee_amount ) {
			$sub_orders = $this->get_suborders( $order->get_id() );

			if ( ! $sub_orders ) {
				return $subtotal;
			}

			return $this->calculate_parent_order_subtotal( $order, $product_discount, $order_fee_amount );
		}

		/**
		 * Calculate the parent order subtotal
		 *
		 * @param WC_order $order            The order object.
		 * @param float    $product_discount The product discount.
		 * @param float    $order_fee_amount The fee amount of the order.
		 *
		 * @return int
		 * @since  1.0.0
		 */
		public function calculate_parent_order_subtotal( $order, $product_discount, $order_fee_amount ) {
			$order_items = $this->get_parent_order_items( $order );

			$subtotal = 0;

			foreach ( $order_items as $item ) {
				$subtotal += ( isset( $item['line_subtotal'] ) ) ? $item['line_subtotal'] : 0;
			}

			$total_discount = $this->get_parent_order_total_discount( $order->get_total_discount(), $order, $product_discount );

			return $subtotal + $total_discount + $order_fee_amount + $order->get_total_shipping();
		}

		/**
		 * Calculate the total discount in parent order invoice
		 *
		 * @param WC_order $order            The order object.
		 * @param float    $product_discount The product discount.
		 *
		 * @return float
		 * @since  1.0.0
		 */
		public function calculate_parent_order_total_discount( $order, $product_discount ) {
			$sub_orders = $this->get_suborders( $order->get_id() );

			$total_discount = $order->get_total_discount();

			foreach ( $sub_orders as $order_id ) {
				$_order          = wc_get_order( $order_id );
				$total_discount -= $_order->get_total_discount();
			}

			return $total_discount + $product_discount;
		}

		/**
		 * Get the correct value for total discount in parent order invoice
		 *
		 * @param float    $total_discount   The total discount.
		 * @param WC_order $order            The order object.
		 * @param float    $product_discount The product discount.
		 *
		 * @return float
		 * @since  1.0.0
		 */
		public function get_parent_order_total_discount( $total_discount, $order, $product_discount ) {
			$sub_orders = $this->get_suborders( $order->get_id() );

			if ( ! $sub_orders ) {
				return $total_discount;
			}

			return $this->calculate_parent_order_total_discount( $order, $product_discount );
		}

		/**
		 * Show the invoice information for a suborder.
		 *
		 * @param WC_Order $order the order object.
		 *
		 * @since  1.0.0
		 */
		public function show_order_invoiced_status( $order ) {
			YITH_PDF_Invoice()->show_invoice_information_link( $order );
		}

		/**
		 * Disable the automatically generated invoices.
		 */
		public function stop_automatic_invoices() {
			return false;
		}

		/**
		 * Remove the invoice button on orders list page for the vendors
		 *
		 * @param string   $html  The html.
		 * @param WC_Order $order The order object.
		 *
		 * @return string
		 * @since  1.0.0
		 */
		public function remove_invoice_button_on_order_list( $html, $order ) {
			$post_author = get_post_field( 'post_author', $order->get_id() );
			$vendor      = yith_get_vendor( $post_author, 'user' );

			if ( ! $this->is_vendor_super_admin( $vendor ) ) {
				return '';
			}

			return $html;
		}

		/**
		 * Remove the invoice button on order page for the vendors
		 *
		 * @param bool     $enabled True or false if enabled or not.
		 * @param WC_Order $order   The order object.
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function remove_invoice_button_on_order_page( $enabled, $order ) {
			$post_author = get_post_field( 'post_author', $order->get_id() );
			$vendor      = yith_get_vendor( $post_author, 'user' );

			if ( ! $this->is_vendor_super_admin( $vendor ) ) {
				return false;
			}

			return $enabled;
		}

		/**
		 * Show a single row for invoice related infomation
		 *
		 * @param WC_Order $order The order object.
		 *
		 * @since  1.0.0
		 */
		private function show_multivendor_invoice_row( $order ) {
			$is_admin_order = $this->get_suborders( $order->get_id() );

			?>
			<tr>
				<td>
					<?php
					$vendor_id = $order->get_meta( 'vendor_id' );
					$vendor    = yith_wcmv_get_vendor( $vendor_id );

					if ( $vendor && $vendor->is_valid() ) {
						echo esc_html( method_exists( $vendor, 'get_name' ) ? $vendor->get_name() : $vendor->name );
					} elseif ( $is_admin_order ) {
						/**
						 * APPLY_FILTERS: yith_ywpi_my_account_admin_as_vendor_title
						 *
						 * Filter the admin as vendor title in My account.
						 *
						 * @param string the title.
						 *
						 * @return string
						 */
						// translators: %1$s and %2$s are <b> and </b> tags.
						echo wp_kses_post( apply_filters( 'yith_ywpi_my_account_admin_as_vendor_title', sprintf( __( '%1$sCurrent shop%2$s', 'yith-woocommerce-pdf-invoice' ), '<b>', '</b>' ) ) );
					}
					?>
				</td>
				<td>
					<?php if ( $this->order_has_invoice( $order, false ) ) : ?>
						<a target="_blank" href="<?php echo wp_kses_post( YITH_PDF_Invoice()->get_action_url( 'view', 'invoice', $order->get_id() ) ); ?>"><?php esc_html_e( 'Download', 'yith-woocommerce-pdf-invoice' ); ?></a>
					<?php endif; ?>
				</td>
			</tr>
			<?php
		}

		/**
		 * Register action to show tracking information on email for completed orders.
		 *
		 * @param WC_Order $order the order whose details should be shown.
		 */
		public function show_suborder_invoice( $order ) {
			$result = $this->get_suborders( $order->get_id() );

			if ( ! $result ) {
				return;
			}

			?>
			<h2><?php esc_html_e( 'Invoice status', 'yith-woocommerce-pdf-invoice' ); ?></h2>
			<table class="shop_table shop_table_responsive multi_vendor_invoice" id="multi_vendor_invoice">
				<thead>
					<tr class="multi_vendor_invoice_header">
						<th><?php esc_html_e( 'Vendor', 'yith-woocommerce-pdf-invoice' ); ?></th>
						<th><?php esc_html_e( 'Invoice', 'yith-woocommerce-pdf-invoice' ); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				$this->show_multivendor_invoice_row( $order );

				foreach ( $result as $suborder_id ) {
					$suborder = wc_get_order( $suborder_id );
					$this->show_multivendor_invoice_row( $suborder );
				}

				?>
				</tbody>
			</table>
			<?php
		}

		/**
		 * Show invoice buttons according to the order type and status
		 *
		 * @param array    $actions The actions.
		 * @param WC_Order $order   The order object.
		 *
		 * @return array
		 * @since  1.0.0
		 */
		public function show_multi_invoice_button( $actions, $order ) {
			// If there are suborders, remove standard buttons.
			if ( $this->get_suborders( $order->get_id() ) ) {
				unset( $actions['print-invoice'] );
				unset( $actions['print-pro-forma-invoice'] );

				if ( $this->order_has_invoice( $order ) ) {
					$actions['view-mv-invoice'] = array(
						'url'  => $order->get_view_order_url() . '#ywpi-invoice-details',
						'name' => esc_html__( 'View Invoice', 'yith-woocommerce-pdf-invoice' ),
					);
				}
			}

			return $actions;
		}

		/**
		 * Check if an order or (optionally) one of its suborders has an invoiced attached
		 *
		 * @param WC_Order  $order   The order object.
		 * @param bool|true $recursive True or false if recursive mode.
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function order_has_invoice( $order, $recursive = true ) {
			$invoice = new YITH_Invoice( $order->get_id() );

			if ( $invoice->generated() ) {
				return true;
			}

			if ( $recursive ) {
				$sub_orders = $this->get_suborders( $order->get_id() );

				foreach ( $sub_orders as $sub_order_id ) {
					$invoice = new YITH_Invoice( $sub_order_id );

					if ( $invoice->generated() ) {
						return true;
					}
				}
			}

			return false;
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return   void
		 * @since    1.0
		 * @use      /Yit_Plugin_Panel class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {
			if ( ! empty( $this->panel ) ) {
				return;
			}

			$admin_tabs = array(
				'vendor' => array(
					'title'       => __( 'Vendor settings', 'yith-woocommerce-pdf-invoice' ),
					'icon'        => 'settings',
					'description' => __( 'Configure the invoice settings.', 'yith-woocommerce-pdf-invoice' ),
				),
			);

			if ( YITH_Electronic_Invoice()->enable === 'yes' ) {
				$admin_tabs['vendor-electronic-invoice'] = array(
					'title'       => __( 'Vendor Electronic Invoice Settings', 'yith-woocommerce-pdf-invoice' ),
					'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="23px" height="20px" viewBox="0 0 23 20" enable-background="new 0 0 23 20" xml:space="preserve"><path fill="#94A2B8" d="M17.2,20H5.8C2.602,20,0,17.398,0,14.2V5.8C0,2.602,2.602,0,5.8,0h11.4C20.398,0,23,2.602,23,5.8v8.4 C23,17.398,20.398,20,17.2,20z M5.8,1.6c-2.316,0-4.2,1.884-4.2,4.2v8.4c0,2.315,1.884,4.2,4.2,4.2h11.4c2.315,0,4.2-1.885,4.2-4.2 V5.8c0-2.316-1.885-4.2-4.2-4.2H5.8z M9.374,6.931V13h3.672v-1.292h-2.091v-1.207h1.887V9.26h-1.887V8.223h2.015V6.931H9.374z"/></svg>',
					'description' => __( 'Configure the settings for the electronic invoice.', 'yith-woocommerce-pdf-invoice' ),
				);
			}

			$args = array(
				'ui_version'       => 2,
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => 'YITH WooCommerce PDF Invoices & Packing Slips',
				'menu_title'       => 'PDF Invoices & Packing Slips',
				'capability'       => $this->get_vendor_admin_role_cap(),
				'parent'           => '',
				'parent_page'      => '',
				'page'             => $this->panel_page,
				'admin-tabs'       => $admin_tabs,
				'icon_url'         => 'dashicons-admin-settings',
				'options-path'     => YITH_YWPI_DIR . '/plugin-options',
				'class'            => yith_set_wrapper_class(),
			);

			$this->panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		/**
		 * Modify the option name for YITH MultiVendor compatibility to fit the single vendor option name
		 *
		 * @param string $option_name the name of the option.
		 * @param mixed  $obj The object.
		 *
		 * @return string
		 */
		public function vendor_option_name( $option_name, $obj = null ) {
			$vendor = null;

			$vendor_option_names = array(
				'ywpi_invoice_number',
				'ywpi_invoice_year_billing',
				'ywpi_invoice_prefix',
				'ywpi_invoice_suffix',
				'ywpi_invoice_number_format',
				'ywpi_invoice_reset',
				'ywpi_company_name',
				'ywpi_company_logo',
				'ywpi_company_details',
				'ywpi_invoice_notes',
				'ywpi_invoice_footer',
				'ywpi_pro_forma_notes',
				'ywpi_pro_forma_footer',
				'ywpi_packing_slip_notes',
				'ywpi_packing_slip_footer',
				'ywpi_electronic_invoice_progressive_file_id_number',
				'ywpi_electronic_invoice_progressive_file_id_letter',
				'ywpi_electronic_invoice_transmitter_id',
				'ywpi_electronic_invoice_company_vat',
				'ywpi_electronic_invoice_fiscal_regime',
				'ywpi_electronic_invoice_chargeability_vat',
				'ywpi_electronic_invoice_company_registered_name',
				'ywpi_electronic_invoice_company_address',
				'ywpi_electronic_invoice_company_cap',
				'ywpi_electronic_invoice_company_city',
				'ywpi_electronic_invoice_company_province',
			);

			if ( ! in_array( $option_name, $vendor_option_names, true ) ) {
				return $option_name;
			}

			/**
			 * If $obj_id == 0, retrieve the vendor option name based on current user
			 */
			if ( $obj instanceof WC_Order ) {
				$order = wc_get_order( $obj );

				if ( $order ) {
					$post_author = get_post_field( 'post_author', $order->get_id() );
					$vendor      = yith_get_vendor( $post_author, 'user' );
				}
			} elseif ( $obj instanceof YITH_Document ) {
				$order = wc_get_order( $obj->order );

				if ( $order ) {
					$post_author = get_post_field( 'post_author', $order->get_id() );
					$vendor      = yith_get_vendor( $post_author, 'user' );
				}
			} elseif ( 0 === $obj ) {
				$vendor = yith_get_vendor( get_current_user_id(), 'user' );
			}

			if ( $vendor && $vendor->is_valid() ) {
				$option_name = $option_name . '_' . $this->get_vendor_id( $vendor );
			}

			return $option_name;
		}

		/**
		 * Set the storing folder for the vendor
		 *
		 * @param string        $folder_path current folder path.
		 * @param YITH_Document $document    the document to save.
		 *
		 * @return string the vendor storing folder
		 */
		public function get_vendor_storing_folder( $folder_path, $document ) {
			if ( $document->is_valid() ) {
				$post_author = get_post_field( 'post_author', $document->order->get_id() );
				$vendor      = yith_get_vendor( $post_author, 'user' );

				if ( $vendor && $vendor->is_valid() ) {
					$folder_path = sprintf( '%s/%s', $this->get_vendor_id( $vendor ), $folder_path );
				}
			}

			return $folder_path;
		}

		/**
		 * Retrieve the fee to use for the invoice
		 *
		 * @param array    $items The items.
		 * @param WC_Order $order The order object.
		 *
		 * @return array
		 *
		 * @since  1.0.0
		 */
		public function get_order_fee_and_shipping_for_invoice( $items, $order ) {
			// The order fee are always associated to the main order.

			// If there are suborders, then it's a main order and the fees are used.
			if ( $this->get_suborders( $order->get_id() ) ) {
				foreach ( $items as $key => $item ) {
					if ( $order->get_created_via() !== 'yith_wcmv_vendor_suborder' && empty( $item->get_formatted_meta_data( '' ) ) ) {
						unset( $items[ $key ] );
					}
				}
			}

			return $items;
		}

		/**
		 * Retrieve the items to use for the invoice
		 *
		 * @param array    $items The items.
		 * @param WC_Order $order The order object.
		 *
		 * @return array
		 *
		 * @since  1.0.0
		 */
		public function get_order_items_for_invoice( $items, $order ) {
			// For vendor's orders or admin order's without suborders, return all the items.
			if ( ! $this->get_suborders( $order->get_id() ) ) {
				return $items;
			}

			return $this->get_parent_order_items( $order );
		}

		/**
		 * Retrieve the parent order items, without the order items that belong to other vendors
		 *
		 * @param WC_order $order The order object.
		 *
		 * @return array
		 * @since  1.0.0
		 */
		public function get_parent_order_items( $order ) {
			// For orders with multiple vendors, filter the resulting items by main order vendor or admin alternativly.
			$post_author      = get_post_field( 'post_author', $order->get_id() );
			$vendor           = yith_get_vendor( $post_author, 'user' );
			$items_by_vendors = YITH_Orders_Premium::get_order_items_by_vendor( $order->get_id() );

			if ( $vendor && $vendor->is_valid() ) {
				$vendor_id = $this->get_vendor_id( $vendor );

				if ( isset( $items_by_vendors[ $vendor_id ] ) ) {
					return $items_by_vendors[ $vendor_id ];
				}
			} elseif ( isset( $items_by_vendors[0] ) ) { // Returns items that are not related to any vendor(so the admin will manage it).
				return $items_by_vendors[0];
			}

			return array();
		}

		/**
		 * Retrieve the YIT_Plugin_Panel_WooCommerce object
		 *
		 * @return YIT_Plugin_Panel_WooCommerce
		 */
		public function get_vendor_panel() {
			return $this->panel;
		}

		/**
		 * Retrieve the Panel Page Name
		 *
		 * @return string the panel page name
		 */
		public function get_vendor_panel_page_name() {
			return $this->panel_page;
		}
	}
}

YITH_YWPI_Multivendor_Loader::get_instance();
