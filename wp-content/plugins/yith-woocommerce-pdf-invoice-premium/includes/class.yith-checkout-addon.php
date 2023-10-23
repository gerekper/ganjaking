<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase, WordPress.Files.FileName.InvalidClassFileName
/**
 * Checkout Addons ( VAT and SSN ) class.
 *
 * Handles the Checkout fields ( SSN and VAT ).
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\PDFInvoice\Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_Checkout_Addon' ) ) {
	/**
	 * Add SSN and VAT to user information and on checkout process
	 *
	 * @class   YITH_Checkout_Addon
	 * @package YITH\PDFInvoice\Classes
	 * @since   1.0.0
	 */
	class YITH_Checkout_Addon {

		/**
		 * The ssn number.
		 *
		 * @var string
		 */
		private $ssn_number_text = 'billing_vat_ssn';

		/**
		 * The ssn number suffix.
		 *
		 * @var string
		 */
		private $ssn_number_suffix = '_vat_ssn';

		/**
		 * The ssn number args.
		 *
		 * @var string
		 */
		private $ssn_number_args = 'vat_ssn';

		/**
		 * The ssn number placeholder.
		 *
		 * @var string
		 */
		private $ssn_number_args_replace = '{vat_ssn}';

		/**
		 * The ssn number metakey.
		 *
		 * @var string
		 */
		private $ssn_number_metakey = '_billing_vat_ssn';

		/**
		 * The vat number.
		 *
		 * @var string
		 */
		private $vat_number_text = 'billing_vat_number';

		/**
		 * The vat number suffix.
		 *
		 * @var string
		 */
		private $vat_number_suffix = '_vat_number';

		/**
		 * The vat number args.
		 *
		 * @var string
		 */
		private $vat_number_args = 'vat_number';

		/**
		 * The vat number placeholder.
		 *
		 * @var string
		 */
		private $vat_number_args_replace = '{vat_number}';

		/**
		 * The vat number metakey.
		 *
		 * @var string
		 */
		private $vat_number_metakey = '_billing_vat_number';

		/**
		 * Ask for the ssn number.
		 *
		 * @var boolean
		 */
		private $ask_ssn_number = false;

		/**
		 * Ask for the vat number.
		 *
		 * @var boolean
		 */
		private $ask_vat_number = false;

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

			self::$instance->ask_ssn_number = ( 'yes' === strval( ywpi_get_option( 'ywpi_ask_ssn_number' ) ) ) ? true : false;
			self::$instance->ask_vat_number = ( 'yes' === strval( ywpi_get_option( 'ywpi_ask_vat_number' ) ) ) ? true : false;

			return self::$instance;
		}

		/**
		 * Initialize the hooks.
		 */
		public function initialize() {
			// If ssn number and vat number are not enabled, skip the following process.
			if ( $this->ask_ssn_number || $this->ask_vat_number ) {
				// Add custom field and sort fields to billing.
				add_filter( 'woocommerce_billing_fields', array( $this, 'add_sort_custom_fields' ) );

				// Format edit address form.
				add_filter( 'woocommerce_address_to_edit', array( $this, 'customize_edit_address_form_fields' ) );

				// Register custom fields of checkout.
				add_action( 'woocommerce_checkout_order_processed', array( $this, 'process_shop_order_meta' ), 10, 2 );

				// Adds order meta to template email.
				add_filter( 'woocommerce_email_order_meta_fields', array( $this, 'add_custom_email_order_meta' ), 10, 3 );

				// Update formatted billing address, for view order page.
				add_filter( 'woocommerce_order_get_formatted_billing_address', array( $this, 'update_formatted_billing_address_order' ), 10, 3 );

				// Update formatted billing address, for my-account page.
				add_filter( 'woocommerce_my_account_my_address_formatted_address', array( $this, 'update_formatted_billing_address_myaccount' ), 10, 3 );

				// Update replacements for formatted address.
				add_filter( 'woocommerce_formatted_address_replacements', array( $this, 'update_address_replacement' ), 10, 2 );

				// Adds custom detail to customer profile (public view).
				add_filter( 'woocommerce_admin_billing_fields', array( $this, 'add_custom_billing_field' ) );

				add_filter( 'woocommerce_ajax_get_customer_details', array( $this, 'get_customer_details_gt_3' ), 10, 3 );

				// Retrieve custom customer meta, for customer profile (admin view).
				add_filter( 'woocommerce_customer_meta_fields', array( $this, 'add_profile_details' ) );

				add_filter( 'yith_ywpi_template_editor_customer_info', array( $this, 'display_custom_billing_fields' ), 10, 2 );
			}

			/** Add invoice/receipt select in the checkout */
			if ( strval( get_option( 'ywpi_enable_receipts', 'no' ) ) === 'yes' ) {
				add_filter( 'woocommerce_billing_fields', array( $this, 'add_invoice_type_billing_fields' ) );
				add_filter( 'woocommerce_admin_billing_fields', array( $this, 'add_invoice_type_billing_fields' ) );
			}

			add_action( 'woocommerce_order_details_after_order_table', array( $this, 'show_invoice_button_after_order_details_table' ), 5 );
		}

		/**
		 * Get customer details via ajax.
		 *
		 * @param  mixed $data The data object.
		 * @param  mixed $customer The customer object.
		 * @param  mixed $user_id The user id.
		 * @return object
		 */
		public function get_customer_details_gt_3( $data, $customer, $user_id ) {
			if ( $this->ask_ssn_number ) {
				$data['billing']['vat_ssn']  = get_user_meta( $user_id, 'billing' . $this->ssn_number_suffix, true );
				$data['shipping']['vat_ssn'] = get_user_meta( $user_id, 'shipping' . $this->ssn_number_suffix, true );
			}

			if ( $this->ask_vat_number ) {
				$data['billing']['vat_number']  = get_user_meta( $user_id, 'billing' . $this->vat_number_suffix, true );
				$data['shipping']['vat_number'] = get_user_meta( $user_id, 'shipping' . $this->vat_number_suffix, true );
			}

			return $data;
		}

		/**
		 * Customize "Edit address" form fields
		 *
		 * @access public
		 *
		 * @param array $fields Array of fields.
		 *
		 * @return array Customized array of fields
		 * @since  1.0.0
		 */
		public function customize_edit_address_form_fields( $fields ) {
			if ( isset( $fields['billing_company'] ) ) {
				$fields['billing_company']['class'] = array( 'form-row-first' );
			}

			return $fields;
		}

		/**
		 * Add custom billing fields to all forms
		 *
		 * @access public
		 *
		 * @param array $fields Array of fields.
		 *
		 * @return array Customized array of fields
		 * @since  1.0.0
		 */
		public function add_sort_custom_fields( $fields ) {
			if ( $this->ask_ssn_number ) {
				/**
				 * APPLY_FILTERS: yith_ywpi_ssn_is_required_option
				 *
				 * Filter the condition to ask the SSN as required.
				 *
				 * @param bool True to set it as require, false to not.
				 *
				 * @return bool
				 */
				$is_required = apply_filters( 'yith_ywpi_ssn_is_required_option', 'yes' ) === get_option( 'ask_ssn_number_required', 'no' );

				/**
				 * APPLY_FILTERS: yith_ywpi_ssn_field_placeholder
				 *
				 * Filter the SSN field placeholder.
				 *
				 * @param string the placeholder.
				 *
				 * @return string
				 */
				$fields[ $this->ssn_number_text ] = array(
					'label'       => apply_filters( 'yith_ywpi_ssn_field_text', esc_html__( 'SSN', 'yith-woocommerce-pdf-invoice' ) ),
					'required'    => $is_required,
					'class'       => array( 'form-row-wide' ),
					'clear'       => 1,
					'placeholder' => apply_filters( 'yith_ywpi_ssn_field_placeholder', esc_html__( 'SSN', 'yith-woocommerce-pdf-invoice' ) ),
				);
			}

			if ( $this->ask_vat_number ) {
				$add_vat_number = true;

				// Check for compatibility mode.
				if ( ywpi_use_woo_eu_vat_number() ) {
					$add_vat_number = false;
				}

				if ( function_exists( 'yith_ywev_premium_init' ) ) {
					$add_vat_number = false;
				}

				if ( $add_vat_number ) {
					/**
					 * APPLY_FILTERS: yith_ywpi_vat_number_is_required_option
					 *
					 * Filter the condition to ask the VAT as required.
					 *
					 * @param bool True to set it as require, false to not.
					 *
					 * @return bool
					 */
					$is_required = apply_filters( 'yith_ywpi_vat_number_is_required_option', 'yes' ) === get_option( 'ask_vat_number_required', 'no' );

					/**
					 * APPLY_FILTERS: yith_ywpi_vat_field_placeholder
					 *
					 * Filter the VAT field placeholder.
					 *
					 * @param string the placeholder.
					 *
					 * @return string
					 */
					$fields[ $this->vat_number_text ] = array(
						'label'       => apply_filters( 'yith_ywpi_vat_field_text', esc_html__( 'VAT', 'yith-woocommerce-pdf-invoice' ) ),
						'required'    => $is_required,
						'class'       => array( 'form-row-wide' ),
						'clear'       => 1,
						'placeholder' => apply_filters( 'yith_ywpi_vat_field_placeholder', esc_html__( 'VAT', 'yith-woocommerce-pdf-invoice' ) ),
					);
				}
			}

			return $fields;
		}

		/**
		 * Saves VAT/SSN when processing order save (admin side)
		 *
		 * @access public
		 *
		 * @param int $order_id The order id.
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function process_shop_order_meta( $order_id ) {
			$order = wc_get_order( $order_id );

			if ( $this->ask_ssn_number ) {
				if ( isset( $_POST[ $this->ssn_number_metakey ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
					$order->update_meta_data( $this->ssn_number_metakey, sanitize_key( wp_unslash( $_POST[ $this->ssn_number_metakey ] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
					update_post_meta( $order->get_id(), $this->ssn_number_metakey, sanitize_key( wp_unslash( $_POST[ $this->ssn_number_metakey ] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
					$order->save();
				}
			}

			if ( $this->ask_vat_number ) {
				// Save VAT number value but pay attention to compatibility mode.
				if ( ywpi_use_woo_eu_vat_number() ) {
					if ( isset( $_POST['vat_number'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
						$order->update_meta_data( $this->vat_number_metakey, sanitize_key( wp_unslash( $_POST['vat_number'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
						update_post_meta( $order->get_id(), $this->vat_number_metakey, sanitize_key( wp_unslash( $_POST['vat_number'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
						$order->save();
					}
				} elseif ( isset( $_POST[ $this->vat_number_metakey ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
					$order->update_meta_data( $this->vat_number_metakey, sanitize_key( wp_unslash( $_POST[ $this->vat_number_metakey ] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
					update_post_meta( $order->get_id(), $this->vat_number_metakey, sanitize_key( wp_unslash( $_POST[ $this->vat_number_metakey ] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
					$order->save();
				}
			}
		}

		/**
		 * Sets custom fields to new order email
		 *
		 * @access public
		 *
		 * @param array     $fields         Array of fields to be used in mail construction.
		 * @param bool      $sent_to_admin  Flag set when mail will be carbon copied to admin.
		 * @param \WC_Order $order          Order object.
		 *
		 * @return array Filtered array of fields
		 * @since  1.0.0
		 */
		public function add_custom_email_order_meta( $fields, $sent_to_admin, $order ) {
			if ( $this->ask_ssn_number ) {
				$fields[] = array(
					'label' => esc_html__( 'SSN', 'yith-woocommerce-pdf-invoice' ),
					'value' => wptexturize( $order->get_meta( $this->ssn_number_metakey ) ),
				);
			}

			if ( $this->ask_vat_number ) {
				$add_vat_number = true;

				// Check for compatibility mode.
				if ( ywpi_use_woo_eu_vat_number() ) {
					$add_vat_number = false;
				}

				if ( $add_vat_number ) {
					$fields[] = array(
						'label' => esc_html__( 'VAT', 'yith-woocommerce-pdf-invoice' ),
						'value' => wptexturize( $order->get_meta( $this->vat_number_metakey ) ),
					);
				}
			}

			return $fields;
		}

		/**
		 * Adds VAT/SSN field to formatted address for order's admin view
		 *
		 * @access public
		 *
		 * @param string    $address     string with the address data.
		 * @param array     $raw_address array with the address data.
		 * @param \WC_Order $order       Order object.
		 *
		 * @return string string with the address data modified
		 * @since  3.8.0
		 */
		public function update_formatted_billing_address_order( $address, $raw_address, $order ) {
			if ( is_string( $address ) ) {
				if ( $this->ask_ssn_number ) {
					$ssn = $order->get_meta( '_billing_vat_ssn' );

					if ( $ssn ) {
						$address .= '<br>' . apply_filters( 'yith_ywpi_billing_address_ssn_label', esc_html__( 'SSN: ', 'yith-woocommerce-pdf-invoice' ) ) . $ssn;
					}
				}

				if ( $this->ask_vat_number ) {
					$vat = $order->get_meta( '_billing_vat_number' );

					if ( $vat ) {
						$address .= '<br>' . apply_filters( 'yith_ywpi_billing_address_vat_label', esc_html__( 'VAT: ', 'yith-woocommerce-pdf-invoice' ) ) . $vat;
					}
				}
			}

			return $address;
		}

		/**
		 * Adds VAT/SSN field to formatted address for my-account user view
		 *
		 * @access public
		 *
		 * @param array  $billing_fields Array of fields to be used in formatted address.
		 * @param int    $customer_id The customer id.
		 * @param string $type The field type.
		 *
		 * @return array Array of filtered fields
		 * @since  1.0.0
		 */
		public function update_formatted_billing_address_myaccount( $billing_fields, $customer_id, $type ) {
			if ( 'billing' === $type ) {
				if ( $this->ask_ssn_number ) {
					$billing_fields[ $this->ssn_number_text ] = get_user_meta( $customer_id, $this->ssn_number_text, true );
				}

				if ( $this->ask_vat_number ) {
					$billing_fields[ $this->vat_number_text ] = get_user_meta( $customer_id, $this->vat_number_text, true );
				}
			}

			return $billing_fields;
		}

		/**
		 * Update address replacement for all site address formats
		 *
		 * @access public
		 *
		 * @param array $replacements Array of available replacements.
		 * @param array $args Array of arguments to use in replacements.
		 *
		 * @return array Filtered array of replacements
		 * @since  1.0.0
		 */
		public function update_address_replacement( $replacements, $args ) {
			$replacements[ $this->ssn_number_args_replace ] = '';
			$replacements[ $this->vat_number_args_replace ] = '';

			if ( $this->ask_ssn_number ) {
				if ( ! empty( $args[ $this->ssn_number_text ] ) ) {
					$replacements[ $this->ssn_number_args_replace ] = 'SSN: ' . $args[ $this->ssn_number_text ];
				}
			}

			if ( $this->ask_vat_number ) {
				if ( ! empty( $args[ $this->vat_number_text ] ) ) {
					$replacements[ $this->vat_number_args_replace ] = 'VAT: ' . $args[ $this->vat_number_text ];
				}
			}

			return $replacements;
		}

		/**
		 * Adds VAT/SSN fields to order admin view
		 *
		 * @access public
		 *
		 * @param array $billing_data Array of registered fields.
		 *
		 * @return array Filtered array of registered fields
		 * @since  1.0.0
		 */
		public function add_custom_billing_field( $billing_data ) {
			if ( $this->ask_ssn_number ) {
				$billing_data[ $this->ssn_number_args ] = array(
					'label' => apply_filters( 'yith_ywpi_ssn_field_text', esc_html__( 'SSN', 'yith-woocommerce-pdf-invoice' ) ),
					'show'  => false,
				);
			}

			if ( $this->ask_vat_number ) {
				$billing_data[ $this->vat_number_args ] = array(
					'label' => apply_filters( 'yith_ywpi_vat_field_text', esc_html__( 'VAT', 'yith-woocommerce-pdf-invoice' ) ),
					'show'  => false,
				);
			}

			return $billing_data;
		}

		/**
		 * Add Receiver ID and PEC fields to WooCommerce default fields
		 *
		 * @param array $fields Array with the billing fields.
		 * @return mixed
		 */
		public function add_invoice_type_billing_fields( $fields ) {
			$current_action   = current_action();
			$key_invoice_type = 'woocommerce_billing_fields' === strval( $current_action ) ? 'billing_invoice_type' : 'invoice_type';

			// APPLY_FILTERS: ywpi_invoice_type_field_label | Filter the "Invoice type" label in the checkout. | @param string the label.; | @return string.

			// APPLY_FILTERS: ywpi_invoice_type_field_invoice_label | Filter the "Invoice" label in the invoice type selector at checkout. | @param string the label.; | @return string.

			// APPLY_FILTERS: ywpi_invoice_type_field_receipt_label | Filter the "Receipt" label in the invoice type selector at checkout. | @param string the label.; | @return string.

			// APPLY_FILTERS: ywpi_invoice_type_field_default_value | Filter the default value in the invoice type selector at checkout. | @param string the default value. Default: "invoice".; | @return string.

			$fields[ $key_invoice_type ] = array(
				'label'        => apply_filters( 'ywpi_invoice_type_field_label', esc_html__( 'Invoice type', 'yith-woocommerce-pdf-invoice' ) ),
				'class'        => 'woocommerce_admin_billing_fields' === strval( $current_action ) ? '' : array( 'form-row-wide' ),
				'autocomplete' => 'given-name',
				'priority'     => 22,
				'required'     => true,
				'type'         => 'select',
				'options'      => array(
					'invoice' => apply_filters( 'ywpi_invoice_type_field_invoice_label', esc_html__( 'Invoice', 'yith-woocommerce-pdf-invoice' ) ),
					'receipt' => apply_filters( 'ywpi_invoice_type_field_receipt_label', esc_html__( 'Receipt', 'yith-woocommerce-pdf-invoice' ) ),
				),
				'default'      => apply_filters( 'ywpi_invoice_type_field_default_value', 'invoice' ),
			);

			return $fields;
		}

		/**
		 * Add VAT/SSN to user profile details
		 *
		 * @access public
		 *
		 * @param array $meta Array of available meta.
		 *
		 * @return array Filtered array of available meta
		 * @since  1.0.0
		 */
		public function add_profile_details( $meta ) {
			if ( $this->ask_ssn_number ) {
				$meta['billing']['fields'][ $this->ssn_number_text ] = array(
					'label'       => esc_html__( 'SSN', 'yith-woocommerce-pdf-invoice' ),
					'description' => '',
				);
			}

			if ( $this->ask_vat_number ) {
				$meta['billing']['fields'][ $this->vat_number_text ] = array(
					'label'       => esc_html__( 'VAT', 'yith-woocommerce-pdf-invoice' ),
					'description' => '',
				);
			}

			return $meta;
		}

		/**
		 * Display custom billing fields in the templates
		 *
		 * @param array    $customer Customer data.
		 * @param WC_Order $order    Order object.
		 *
		 * @return array
		 */
		public function display_custom_billing_fields( $customer, $order ) {
			if ( $this->ask_ssn_number ) {
				$customer[ $this->ssn_number_text ] = is_object( $order ) ? $order->get_meta( $this->ssn_number_metakey ) : '123456789';
			}

			if ( $this->ask_vat_number ) {
				$customer[ $this->vat_number_text ] = is_object( $order ) ? $order->get_meta( $this->vat_number_metakey ) : '123456789';
			}

			return $customer;
		}

		/**
		 * Display an 'print invoice' button on the view order page.
		 *
		 * @param object $order Order.
		 */
		public function show_invoice_button_after_order_details_table( $order ) {
			$order_id = $order->get_id();
			$invoice  = new YITH_Invoice( $order_id );

			if ( $invoice->generated() && apply_filters( 'yith_ywpi_show_invoice_button_view_order', true, $order ) ) {
				$action = 'view';
				$type   = 'invoice';

				$args = array(
					'action' => $action,
					'type'   => $type,
					'id'     => $order_id,
				);

				$nonce_action = $action . $type . $order_id;

				$url = esc_url( wp_nonce_url( add_query_arg( $args ), $nonce_action ) );

				echo '<p class="order-again invoice-button"><a href="' . esc_url( $url ) . '" class="woocommerce-button button print-invoice" target="_blank">' . esc_html__( 'Invoice', 'yith-woocommerce-pdf-invoice' ) . '</a></p>';
			}
		}
	}
}
