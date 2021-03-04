<?php
/**
 * WooCommerce Checkout Add-Ons
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Checkout Add-Ons to newer
 * versions in the future. If you wish to customize WooCommerce Checkout Add-Ons for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-checkout-add-ons/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2014-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Checkout_Add_Ons\Frontend;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Add_On;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Add_On_Factory;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Plugin;

defined( 'ABSPATH' ) or exit;

/**
 * Frontend class
 *
 * @since 1.0
 */
class Frontend {


	/** @var string Separator used between add-on name and selected/entered value in order review area */
	private $label_separator = ' - ';

	/** @var bool Are we currently in checkout order review? **/
	private $is_checkout_order_review = false;


	/**
	 * Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {

		// Load frontend styles and scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'load_styles_scripts' ) );

		// set up the add-on renderer -- needs to hook after `wp_loaded` so the customizer preview works
		add_action( 'wp', array( $this, 'setup_add_on_renderer' ) );

		// Add add-on fields to checkout fields
		add_filter( 'woocommerce_checkout_fields', array( $this, 'add_checkout_fields' ) );

		// restores possible missing data from post
		add_filter( 'woocommerce_checkout_posted_data', [ $this, 'restore_missing_posted_data' ] );

		// validate the add-on fields at checkout
		add_action( 'woocommerce_after_checkout_validation', array( $this, 'validate_checkout_fields' ) );

		// Add any selected add-ons as fees to cart
		add_action( 'woocommerce_cart_calculate_fees', array( $this, 'add_cart_fees' ) );

		// add recurring fee row to recurring totals tables in Subscriptions v2.0.18 - 2.2.0
		add_action( 'woocommerce_subscriptions_after_recurring_shipping_rates', array( $this, 'display_subscriptions_recurring_fees' ), 10, 3 );

		// Flatten the fee display of add-ons with no associated cost
		add_filter( 'woocommerce_cart_totals_fee_html', array( $this, 'adjust_fee_html' ), 10, 2 );

		// Handle file uploads for the `file` add-on
		add_action( 'wp_ajax_wc_checkout_add_on_upload_file',        array( $this, 'upload_file' ) );
		add_action( 'wp_ajax_nopriv_wc_checkout_add_on_upload_file', array( $this, 'upload_file' ) );
		add_action( 'wp_ajax_wc_checkout_add_on_remove_file',        array( $this, 'remove_file' ) );
		add_action( 'wp_ajax_nopriv_wc_checkout_add_on_remove_file', array( $this, 'remove_file' ) );

		// Add support for radio, file and multicheckbox form field types
		add_filter( 'woocommerce_form_field_wc_checkout_add_ons_multicheckbox',  array( $this, 'form_field' ), 11, 4 );
		add_filter( 'woocommerce_form_field_wc_checkout_add_ons_multiselect',    array( $this, 'form_field' ), 11, 4 );
		add_filter( 'woocommerce_form_field_wc_checkout_add_ons_radio',          array( $this, 'form_field' ), 11, 4 );
		add_filter( 'woocommerce_form_field_wc_checkout_add_ons_file',           array( $this, 'form_field' ), 11, 4 );
		add_filter( 'woocommerce_form_field_wc_checkout_add_ons_checkbox',       array( $this, 'form_field' ), 11, 4 );

		// Save add-on meta on checkout:
		// (this also saves the add-on meta for subscriptions)
		add_action( 'woocommerce_new_order_item', array( $this, 'save_add_on_meta' ), 10, 3 );

		// Initialize session
		add_action( 'woocommerce_init', array( $this, 'init_session' ) );

		// Get correct checkout-add-on value
		add_filter( 'woocommerce_checkout_get_value', array( $this, 'checkout_get_add_on_value' ), 10, 2 );

		// clear session after checkout has been processed
		// this is done intentionally late so that Subscriptions can use the session data as well
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'clear_session' ), 150 );

		// Handle displaying selected/entered add-on values in checkout order review area
		add_action( 'woocommerce_before_template_part', array( $this, 'indicate_checkout_order_review' ), 1 );
		add_action( 'woocommerce_after_template_part',   array( $this, 'clear_checkout_order_review_indicator' ) );
		add_filter( 'esc_html', array( $this, 'display_add_on_value_in_checkout_order_review' ), 10, 2 );
		add_filter( 'esc_html', [ $this, 'display_add_on_label_in_checkout_order_review' ], 10, 2 );

		// Handle displaying selected/entered add-on values in my-account/view-order screen
		add_filter( 'woocommerce_get_order_item_totals',                array( $this, 'append_order_add_on_fee_meta' ), 10, 2 );
		add_filter( 'woocommerce_get_order_item_totals_excl_free_fees', array( $this, 'include_free_order_add_on_fee_meta' ), 10, 2 );

		// refresh add-on checkout fields when cart is updated
		add_filter( 'woocommerce_update_order_review_fragments', [ $this, 'refresh_checkout_fields' ] );

		// enqueue script to labels of textarea and file add-ons so that the HTML code included is rendered properly
		add_action( 'woocommerce_thankyou', [ $this, 'enqueue_script_to_fix_add_on_labels'] );
		add_action( 'woocommerce_view_order', [ $this, 'enqueue_script_to_fix_add_on_labels'] );
	}


	/**
	 * Handles add-on file uploads.
	 *
	 * @internal
	 *
	 * @since 1.0
	 */
	public function upload_file() {

		check_ajax_referer( 'wc-checkout-add-ons-upload-file', 'security' );

		if ( empty( $_FILES ) || $_FILES['file']['error'] ) {
			return false;
		}

		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
			require_once( ABSPATH . "wp-admin" . '/includes/file.php' );
			require_once( ABSPATH . "wp-admin" . '/includes/media.php' );
		}

		$file_info = pathinfo( $_FILES['file']['name'] );

		$_FILES['file']['name'] = $file_info['filename'] . '-' . uniqid( '', false ) . '.' . $file_info['extension'];

		$attachment_id = media_handle_upload( 'file', 0 );

		if ( is_wp_error( $attachment_id ) ) {

			echo json_encode( $attachment_id );

		} else {

			$this->store_uploaded_file_in_session( $attachment_id );

			echo json_encode( array(
				'id'    => $attachment_id,
				'title' => get_the_title( $attachment_id ) . '.' . strtolower( $file_info['extension'] ),
				'url'   => wp_get_attachment_url( $attachment_id ),
			) );
		}

		exit;
	}


	/**
	 * Handles add-on file removals.
	 *
	 * @internal
	 *
	 * @since 1.0
	 */
	public function remove_file() {

		check_ajax_referer( 'wc-checkout-add-ons-remove-file', 'security' );

		$file = $_POST['file'];

		// Bail out if no file
		if ( ! $file ) {
			return;
		}

		// Security: bail out if not uploaded in the same session
		if ( ! in_array( $file, WC()->session->checkout_add_ons['files'], false ) ) {
			return;
		}

		// Delete the attachment
		wp_delete_attachment( $file, true );
		$this->remove_uploaded_file_from_session( $file );

		exit;
	}


	/**
	 * Loads frontend styles and scripts on checkout page
	 *
	 * @internal
	 *
	 * @since 1.0
	 */
	public function load_styles_scripts() {

		$load_styles_scripts = is_checkout();

		if ( ! $load_styles_scripts && function_exists( 'is_wcopc_checkout' ) ) {
			$load_styles_scripts =  is_wcopc_checkout();
		}

		/**
		 * Filter if Checkout Add-ons scripts and styles should be loaded
		 *
		 * @since 1.3.0
		 *
		 * @param bool $load_styles_scripts true if scripts and styles should be loaded; false otherwise
		 */
		if ( ! apply_filters( 'wc_checkout_add_ons_load_styles_scripts', $load_styles_scripts ) ) {
			return;
		}

		$add_on_fields = Add_On_Factory::get_add_ons();

		$script_dependencies = array( 'jquery' );

		// Determine if we need to load Plupload
		if ( ! empty( $add_on_fields ) ) {
			foreach ( $add_on_fields as $add_on_field ) {
				if ( 'file' === $add_on_field->get_type() ) {
					wp_enqueue_script( 'plupload-all' );
					$script_dependencies[] = 'plupload-all';
					break;
				}
			}
		}

		// Register and load our styles and scripts
		wp_register_script( 'wc-checkout-add-ons-frontend', wc_checkout_add_ons()->get_plugin_url() . '/assets/js/frontend/wc-checkout-add-ons.min.js', $script_dependencies, Plugin::VERSION, true );

		wp_localize_script( 'wc-checkout-add-ons-frontend', 'wc_checkout_add_ons', array(
			'ajax_url'                  => admin_url( 'admin-ajax.php' ),
			'max_file_size'             => wp_max_upload_size(),
			'max_files'                 => 1,
			'mime_types'                => $this->get_mime_types(),
			'upload_nonce'              => wp_create_nonce('wc-checkout-add-ons-upload-file'),
			'remove_nonce'              => wp_create_nonce('wc-checkout-add-ons-remove-file'),
			'select_placeholder_single' => __( 'Select an Option', 'woocommerce-checkout-add-ons' ),
			'select_placeholder_multi'  => __( 'Select Some Options', 'woocommerce-checkout-add-ons' ),
			'select_no_results_text'    => __( 'No results match', 'woocommerce-checkout-add-ons' ),
		) );

		wp_enqueue_script( 'wc-checkout-add-ons-frontend' );

		wp_enqueue_style( 'wc-checkout-add-ons-frontend', wc_checkout_add_ons()->get_plugin_url() . '/assets/css/frontend/wc-checkout-add-ons.min.css', array(), Plugin::VERSION );
	}


	/**
	 * Sets up the add-on renderer.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function setup_add_on_renderer() {

		/**
		 * Filters the add-on fields position.
		 *
		 * @since 1.6.0
		 *
		 * @param string $position
		 */
		$position = apply_filters( 'wc_checkout_add_ons_position', get_option( 'wc_checkout_add_ons_position', 'woocommerce_checkout_after_customer_details' ) );

		// Render add-on fields in position based on the settings.
		add_action( $position, array( $this, 'render_add_ons' ), 20 );
	}


	/**
	 * Adds add-on fields to WC checkout fields.
	 *
	 * Adding add-on fields to checkout fields provides
	 * automatic validation and helps to keep our code
	 * more maintainable.
	 *
	 * @internal
	 *
	 * @since 1.0
	 *
	 * @param array $checkout_fields associative array of field id to definition
	 * @return array associative array of field id to definition
	 */
	public function add_checkout_fields( $checkout_fields ) {

		$add_ons = Add_On_Factory::get_add_ons();

		if ( ! empty( $add_ons ) ) {

			$checkout_fields['add_ons'] = [];

			// store names in an array so we can skip duplicates - the WC Fee API only accepts fees with unique names
			$add_on_names = [];

			$i = 1000;

			foreach ( $add_ons as $add_on ) {

				// continue to next add-on if an add-on of the same name was already added
				if ( ! $add_on->get_enabled() || in_array( $add_on->get_name(), $add_on_names, false ) ) {
					continue;
				}

				// continue to next add-on if the add-on should not be displayed as per display rules
				if ( ! $add_on->should_display() ) {
					continue;
				}

				// record the add-on name
				$add_on_names[] = $add_on->get_name();

				switch ( $add_on->get_type() ) {

					case 'file':

						$checkout_fields['add_ons'][ $add_on->get_id() ] = [
							'type'              => 'wc_checkout_add_ons_file',
							// cost is added in the template, so it is not included in validation messages
							'label'             => $this->get_formatted_label( $add_on->get_name(), $add_on->get_label(), $add_on->get_cost_html() ),
							'required'          => $add_on->is_required(),
							'custom_attributes' => [
								'data-description' => $add_on->get_description(),
							],
						];

					break;

					case 'text':
					case 'textarea':

						$checkout_fields['add_ons'][ $add_on->get_id() ] = [
							'type'              => $add_on->get_type(),
							// cost is added in the template, so it is not included in validation messages
							'label'             => $this->get_formatted_label( $add_on->get_name(), $add_on->get_label(), $add_on->get_cost_html() ),
							'required'          => $add_on->is_required(),
							// load from session or use default
							'default'           => $this->checkout_get_add_on_value( $add_on->get_default_value(), $add_on->get_id() ),
							'custom_attributes' => [
								'data-description' => $add_on->get_description(),
							],
						];

					break;

					case 'checkbox':

						$checkbox_default = 'yes' === $add_on->get_default_value() ? 1 : 0;

						$checkout_fields['add_ons'][ $add_on->get_id() ] = [
							'type'              => 'wc_checkout_add_ons_checkbox',
							// cost is added in the template, so it is not included in validation messages
							'label'             => $this->get_formatted_label( $add_on->get_name(), $add_on->get_label(), $add_on->get_cost_html() ),
							'required'          => $add_on->is_required(),
							'default'           => 'default',
							// load from session or use default
							'checkbox_default'  => $this->checkout_get_add_on_value( $checkbox_default, $add_on->get_id() ),
							'custom_attributes' => [
								'data-description' => $add_on->get_description(),
							],
						];

					break;

					case 'select':
					case 'radio':

						$options = [];
						$default = null;

						if ( $add_on->has_options() ) {

							foreach ( $add_on->get_options( 'render' ) as $option ) {

								$cost_type = isset( $option['adjustment_type'] ) ? $option['adjustment_type'] : 'fixed';
								$cost_html = $add_on->get_cost_html( $option['adjustment'], $cost_type );

								// remove HTML tags from select option costs
								if ( 'select' === $add_on->get_type() ) {
									$cost_html = wp_strip_all_tags( $cost_html );
								}

								$value = trim( $this->get_formatted_label( $option['label'], $option['label'], $cost_html ) );
								$key   = sanitize_title( esc_html( $option['label'] ), '', 'wc_checkout_add_ons_sanitize' );

								$options[ $key ] = $value;

								if ( $option['selected'] ) {
									$default = $key;
								}
							}
						}

						$checkout_fields['add_ons'][ $add_on->get_id() ] = [
							'type'              => 'radio' === $add_on->get_type() ? 'wc_checkout_add_ons_radio' : $add_on->get_type(),
							'label'             => $this->get_formatted_label( $add_on->get_name(), $add_on->get_label() ),
							'required'          => $add_on->is_required(),
							'options'           => $options,
							// load from session or use default
							'default'           => $this->checkout_get_add_on_value( $default, $add_on->get_id() ),
							'placeholder'       => $default,
							// this class is used to prevent errors before triggering update_checkout
							'input_class'       => [ 'select2-search__field' ],
							'custom_attributes' => [
								'data-description' => $add_on->get_description(),
							],
						];

					break;

					case 'multiselect':
					case 'multicheckbox':

						// Create special `wc_checkout_add_ons_multicheckbox` type for checkboxes with multiple options
						$options  = [];
						$defaults = [];

						if ( $add_on->has_options() ) {

							foreach ( $add_on->get_options( 'render' ) as $option ) {

								$cost_type = isset( $option['adjustment_type'] ) ? $option['adjustment_type'] : 'fixed';
								$cost_html = $add_on->get_cost_html( $option['adjustment'], $cost_type );

								// remove HTML tags from multiselect option costs
								if ( 'multiselect' === $add_on->get_type() ) {
									$cost_html = wp_strip_all_tags( $cost_html );
								}

								$value = trim( $this->get_formatted_label( $option['label'], $option['label'], $cost_html ) );
								$key   = sanitize_title( esc_html( $option['label'] ), '', 'wc_checkout_add_ons_sanitize' );

								$options[ $key ] = $value;

								if ( $option['selected'] ) {
									$defaults[] = $key;
								}
							}
						}

						$checkout_fields['add_ons'][ $add_on->get_id() ] = [
							'type'              => "wc_checkout_add_ons_{$add_on->get_type()}",
							'label'             => $this->get_formatted_label( $add_on->get_name(), $add_on->get_label() ),
							'required'          => $add_on->is_required(),
							'options'           => $options,
							// load from session or use default
							'default'           => $this->checkout_get_add_on_value( $defaults, $add_on->get_id() ),
							'custom_attributes' => [
								'data-description' => $add_on->get_description(),
							],
						];

					break;
				}

				$checkout_fields['add_ons'][ $add_on->get_id() ]['priority'] = $i;

				$i += 10;
			}
		}

		/**
		 * Filters checkout add-on fields.
		 *
		 * @since 2.0.5
		 *
		 * @param array $checkout_fields list of active add-on fields
		 */
		return apply_filters( 'wc_checkout_add_ons_fields', $checkout_fields );
	}


	/**
	 * Validates the add-on fields at checkout.
	 *
	 * WooCommerce handles most of the checkout field validation. This method provides extra validation
	 * that may be needed and allows others to add their own via an action.
	 *
	 * @internal
	 *
	 * @since 1.8.3
	 *
	 * @param array $post_data the posted field data
	 */
	public function validate_checkout_fields( $post_data ) {

		$add_ons = Add_On_Factory::get_add_ons();

		foreach ( $add_ons as $add_on ) {

			$key = $add_on->get_key();

			// only validate add-ons that have been posted
			if ( ! isset( $post_data[ $key ] ) ) {
				continue;
			}

			$value = $post_data[ $key ];

			/**
			 * Fires when validating an add-on field at checkout.
			 *
			 * @since 1.8.3
			 *
			 * @param string $value the add-on field's posted value
			 * @param Add_On $add_on the add-on object
			 */
			do_action( 'wc_checkout_add_ons_validate_checkout_' . $add_on->get_type() . '_field', $value, $add_on );
		}
	}


	/**
	 * Add add-on as fees when calculating cart fees.
	 *
	 * @internal
	 *
	 * @since 1.0
	 *
	 * @param \WC_Cart $cart
	 */
	public function add_cart_fees( $cart ) {

		if ( ! $_POST || ( is_admin() && ! is_ajax() ) ) {
			return;
		}

		if ( isset( $_POST['post_data'] ) ) {
			parse_str( $_POST['post_data'], $post_data );
		}

		if ( empty( $post_data ) ) {
			$post_data = [];
		}

		$this->store_add_ons_in_session( $cart, $post_data );
		$this->add_ons_as_fees( $cart, $post_data );
	}


	/**
	 * Store add-ons in session.
	 *
	 * This needs to run for all add-ons before calculating the fees
	 * because of the Other Add-On display rule.
	 *
	 * @since 2.1.0
	 *
	 * @param \WC_Cart $cart
	 * @param array $post_data
	 */
	private function store_add_ons_in_session( $cart, $post_data ) {

		$add_ons = Add_On_Factory::get_add_ons();

		foreach ( $add_ons as $add_on ) {

			// check if this cart is a recurring cart from Subscriptions
			// if so, check that the add-on is set to be renewable
			if ( wc_checkout_add_ons()->is_subscriptions_active() && $cart->start_date && ! $add_on->is_renewable() ) {
				continue;
			}

			$id    = esc_attr( $add_on->get_id() );
			$name  = $add_on->get_name();
			$value = isset( $post_data[ $id ] ) ? $post_data[ $id ] : null;
			$value = null === $value && isset( $_POST[ $id ] ) ? $_POST[ $id ] : $value;

			if ( ! is_null( $value ) ) {

				if ( in_array( $add_on->get_type(), [ 'text', 'textarea' ], true ) ) {
					$value = stripslashes( $value );
				}

				$this->store_add_on_in_session( $id, $name, $value );

			} else {

				// remove add-on from session if it has no value
				$this->remove_add_on_from_session( $id, $name );
			}
		}
	}


	/**
	 * Adds add-ons as fees to cart.
	 * For file and text inputs, simply the cost is added.
	 * For selects and radio buttons, the cost of the selected option is added.
	 * For checkboxes, the cost of each checked option is added.
	 *
	 * @internal
	 *
	 * @since 2.0.5
	 *
	 * @param \WC_Cart $cart
	 * @param array $post_data
	 */
	public function add_ons_as_fees( $cart, $post_data ) {

		$add_ons = Add_On_Factory::get_add_ons();

		foreach ( $add_ons as $add_on ) {

			// check if this cart is a recurring cart from Subscriptions
			// if so, check that the add-on is set to be renewable
			if ( wc_checkout_add_ons()->is_subscriptions_active() && $cart->start_date && ! $add_on->is_renewable() ) {
				continue;
			}

			$id    = esc_attr( $add_on->get_id() );
			$name  = $add_on->get_name();
			$value = $this->checkout_get_add_on_value( $add_on->get_default_value(), $id );

			// set value to null to make sure that if it should not be displayed,
			// the add-on is not considered for fee calculation
			if ( ! $add_on->should_display() ) {
				$value = null;
			}

			switch ( $add_on->get_type() ) {

				case 'checkbox':
				case 'text':
				case 'textarea':
				case 'file':

					if ( $value ) {

						$cost      = $add_on->get_cost();
						$taxable   = $add_on->is_taxable();
						$tax_class = $add_on->get_tax_class();

						// Calculate the percentage if needed.
						if ( 'percent' === $add_on->get_cost_type() ) {
							$cost = $add_on->calculate_percentage_adjustment( $cost );
						}

						$cart->add_fee( $name, $cost, $taxable, $tax_class );
					}

				break;

				case 'select':
				case 'radio':

					if ( $value && $add_on->has_options() ) {

						foreach ( $add_on->get_options( 'render' ) as $option ) {

							$key = sanitize_title( esc_html( $option['label'] ), '', 'wc_checkout_add_ons_sanitize' );

							if ( $value === $key ) {

								$cost      = $option['adjustment'];
								$taxable   = $add_on->is_taxable();
								$tax_class = $add_on->get_tax_class();

								// Calculate the percentage if needed.
								if ( isset( $option['adjustment_type'] ) && 'percent' === $option['adjustment_type'] ) {
									$cost = $add_on->calculate_percentage_adjustment( $cost );
								}

								$cart->add_fee( $name, $cost, $taxable, $tax_class );
							}
						}
					}

				break;

				case 'multiselect':
				case 'multicheckbox':

					$has_value = false;
					$cost      = 0;
					$value     = is_array( $value ) ? $value : array( $value );

					if ( $add_on->has_options() ) {

						foreach ( $add_on->get_options( 'render' ) as $option ) {

							$key = sanitize_title( esc_html( $option['label'] ), '', 'wc_checkout_add_ons_sanitize' );

							if ( in_array( $key, $value, false ) ) {

								$has_value = true;

								// Calculate the percentage if needed.
								if ( isset( $option['adjustment_type'] ) && 'percent' === $option['adjustment_type'] ) {
									$option['adjustment'] = $add_on->calculate_percentage_adjustment( $option['adjustment'] );
								}

								$cost += (float) $option['adjustment'];
							}
						}
					}

					if ( $has_value ) {

						$taxable   = $add_on->is_taxable();
						$tax_class = $add_on->get_tax_class();
						$cart->add_fee( $name, $cost, $taxable, $tax_class );
					}

				break;

			}
		}
	}


	/**
	 * Display recurring fees in Subscriptions < version 2.2.
	 *
	 * @since 1.10.1
	 *
	 * @param string $index Unused; subscription information with sign up date and recurrence
	 * @param string[] $base_package Unused; the subscription package data array
	 * @param \WC_Cart $recurring_cart the recurring cart object
	 */
	public function display_subscriptions_recurring_fees( $index, $base_package, $recurring_cart ) {

		// Subscriptions 2.2+ will show fees on its own, we're backporting this for 2.0 - 2.2
		if ( is_checkout() && class_exists( 'WC_Subscriptions' ) && ! empty( \WC_Subscriptions::$version ) && version_compare( \WC_Subscriptions::$version, '2.2.0', '<' ) ) {

			foreach ( $recurring_cart->get_fees() as $fee ) {
				?>
				<tr class="fee recurring-total">
					<th><?php echo esc_html( $fee->name ); ?></th>
					<td><?php wc_cart_totals_fee_html( $fee ); ?></td>
				</tr>
				<?php
			}
		}
	}


	/**
	 * Flatten (--) the fee display of add-ons with no associated cost.
	 *
	 * @since 1.6.1
	 * @param string $html The cost HTML.
	 * @param \WC_Order_Item_Fee $fee The fee.
	 * @return string $html The modified cost HTML.
	 */
	public function adjust_fee_html( $html, $fee ) {

		$add_on = null;

		// Try and find an add-on with a matching name
		foreach ( Add_On_Factory::get_add_ons() as $_add_on ) {

			if ( $fee->name === $_add_on->get_name() ) {

				$add_on = $_add_on;
				break;
			}
		}

		// No add-on was found, so bail
		if ( ! $add_on ) {
			return $html;
		}

		$cost = '';

		switch ( $add_on->get_type() ) {

			case 'multiselect':
			case 'multicheckbox':

				if ( ! empty( WC()->session->checkout_add_ons['fees'][ sanitize_title( $add_on->get_name() ) ] ) ) {

					$session_data = WC()->session->checkout_add_ons['fees'][ sanitize_title( $add_on->get_name() ) ];

					$options = $add_on->has_options() ? $add_on->get_options( 'render' ) : array();

					foreach ( (array) $session_data['value'] as $selected_option ) {

						foreach ( $options as $option ) {

							if ( '' === $option['adjustment'] ) {
								continue;
							}

							if ( $selected_option === sanitize_title( esc_html( $option['label'] ), '', 'wc_checkout_add_ons_sanitize' ) ) {

								// redefine cost for PHP 7.1+
								$cost  = 0;
								$cost += (float) $option['adjustment'];
							}
						}
					}
				}

			break;

			case 'radio':
			case 'select':

				if ( ! empty( WC()->session->checkout_add_ons['fees'][ sanitize_title( $add_on->get_name() ) ] ) ) {

					$session_data = WC()->session->checkout_add_ons['fees'][ sanitize_title( $add_on->get_name() ) ];

					$options = $add_on->has_options() ? $add_on->get_options( 'render' ) : array();

					foreach ( $options as $option ) {

						if ( $session_data['value'] === sanitize_title( esc_html( $option['label'] ), '', 'wc_checkout_add_ons_sanitize' ) ) {
							$cost = $option['adjustment'];
						}
					}
				}

			break;

			default:
				$cost = $add_on->get_cost();
			break;
		}

		if ( '' === $cost ) {
			$html = '--';
		}

		/**
		 * Filter the add-on fee display at checkout.
		 *
		 * @since 1.6.1
		 *
		 * @param string $html The add-on fee HTML.
		 * @param string $cost The calculated add-on cost
		 * @param Add_On $add_on The add-on object.
		 */
		$html = (string) apply_filters( 'wc_checkout_add_ons_checkout_add_on_fee_html', $html, $cost, $add_on );

		return $html;
	}


	/**
	 * Gets formatted label, using $label if set, otherwise $name, and includes $cost if provided.
	 *
	 * @since 1.0
	 *
	 * @param string $name field name
	 * @param string $label optional descriptive field label (default: empty string)
	 * @param string $cost optional field cost (default: empty string)
	 * @return string formatted label field
	 */
	public function get_formatted_label( $name, $label = '', $cost = '' ) {

		$add_on_label = ! empty( $label ) ? wp_kses_post( $label ) : wp_kses_post( $name );

		if ( ! empty ( $cost ) ) {
			$add_on_label .= " ({$cost})";
		}

		/**
		 * Filters the formatted label for the add-ons on the frontend of the site.
		 *
		 * @since 1.11.0
		 *
		 * @param string $add_on_label the add-on label
		 * @param string $name field name
		 * @param string $label optional descriptive field label (default: empty string)
		 * @param string $cost optional field cost (default: empty string)
		 */
		return apply_filters( 'wc_checkout_add_ons_formatted_add_on_label', $add_on_label, $name, $label, $cost );
	}


	/**
	 * Renders add-ons.
	 *
	 * @since 1.0
	 */
	public function render_add_ons() {

		$checkout_add_on_fields = isset( WC()->checkout()->checkout_fields['add_ons'] ) ? WC()->checkout()->checkout_fields['add_ons'] : null;

		// load the template
		wc_get_template(
			'checkout/add-ons.php',
			array(
				'add_on_fields' => $checkout_add_on_fields,
			),
			'',
			wc_checkout_add_ons()->get_plugin_path() . '/templates/'
		);
	}


	/**
	 * Supports radio, file and multicheckbox field types in woocommerce_form_field.
	 *
	 * @since 1.0
	 *
	 * @param string $field
	 * @param string $key
	 * @param array $args
	 * @param mixed $value
	 * @return string form field markup
	 */
	public function form_field( $field, $key, $args, $value ) {

		$after    = '';
		$required = '';

		if ( ! empty( $args['clear'] ) ) {
			$after = '<div class="clear"></div>';
		}

		if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'woocommerce-checkout-add-ons'  ) . '">*</abbr>';
		}

		// Custom attribute handling
		$custom_attributes = array();

		if ( ! empty( $args['custom_attributes'] ) && is_array( $args['custom_attributes'] ) ) {

			foreach ( $args['custom_attributes'] as $attribute => $attribute_value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}

		switch ( $args['type'] ) {

			case 'wc_checkout_add_ons_radio' :

				if ( ! empty( $args['options'] ) ) {

					$field .= '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $key ) . '_field">';

					if ( $args['label'] ) {
						$field .= '<label for="' . esc_attr( current( array_keys( $args['options'] ) ) ) . '" class="' . implode( ' ', $args['label_class'] ) .'">' . $args['label']. $required  . '</label>';
					}

					$field .= '<span class="woocommerce-input-wrapper"><input type="hidden" ' . implode( $custom_attributes ) . ' />';

					foreach ( $args['options'] as $option_key => $option_text ) {

						$field .= '<input type="radio" class="input-checkbox" value="' . esc_attr( $option_key ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '_' . esc_attr( $option_key ) . '"' . checked( $value, $option_key, false ) . ' />';

						$field .= '<label for="' . esc_attr( $key ) . '_' . esc_attr( $option_key ) . '" class="checkbox ' . implode( ' ', $args['label_class'] ) .'">' . $option_text . '</label><br>';

					}

					$field .= '</span>';

					$field .= '</p>' . $after;
				}

			break;

			case 'wc_checkout_add_ons_multiselect' :

				$value = is_array( $value ) ? $value : array( $value );

				if ( ! empty( $args['options'] ) ) {

					$options = '';
					foreach ( $args['options'] as $option_key => $option_text ) {

						$options .= '<option value="' . esc_attr( $option_key ) . '" '. selected( in_array( $option_key, $value ), 1, false ) . '>' . esc_attr( $option_text ) .'</option>';
					}

					$field = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $key ) . '_field">';

					// allow clearing the field by sending an empty value if no options are selected
					$field .= '<input type="hidden" name="' . esc_attr( $key ) . '" value="" />';

					if ( $args['label'] ) {
						$field .= '<label for="' . esc_attr( $key ) . '" class="' . implode( ' ', $args['label_class'] ) .'">' . $args['label']. $required . '</label>';
					}

					$field .= '<span class="woocommerce-input-wrapper">';

					$field .= '<select name="' . esc_attr( $key ) . '[]" id="' . esc_attr( $key ) . '" class="select select2-search__field" multiple="multiple" ' . implode( ' ', $custom_attributes ) . '>'
							. $options
							. ' </select>';

					$field .= '</span>';

					$field .= '</p>' . $after;
				}

			break;

			case 'wc_checkout_add_ons_multicheckbox' :

				$value = is_array( $value ) ? $value : array( $value );

				if ( ! empty( $args['options'] ) ) {

					$field .= '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $key ) . '_field">';

					// allow clearing the field by sending an empty value if no boxes are checked
					$field .= '<input type="hidden" name="' . esc_attr( $key ) . '" value="" />';

					if ( $args['label'] ) {
						$field .= '<label for="' . esc_attr( current( array_keys( $args['options'] ) ) ) . '" class="' . implode( ' ', $args['label_class'] ) .'">' . $args['label']. $required  . '</label>';
					}

					$field .= '<span class="woocommerce-input-wrapper"><input type="hidden" ' . implode( $custom_attributes ) . ' />';

					foreach ( $args['options'] as $option_key => $option_text ) {

						$field .= '<input type="checkbox" class="input-checkbox" value="' . esc_attr( $option_key ) . '" name="' . esc_attr( $key ) . '[]" id="' . esc_attr( $key ) . '_' . esc_attr( $option_key ) . '"' . checked( in_array( $option_key, $value ), 1, false ) . ' />';
						$field .= '<label for="' . esc_attr( $key ) . '_' . esc_attr( $option_key ) . '" class="checkbox ' . implode( ' ', $args['label_class'] ) .'">' . $option_text . '</label><br>';
					}

					$field .= '</span>';

					$field .= '</p>' . $after;
				}

			break;

			case 'wc_checkout_add_ons_file' :

				$field .= '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $key ) . '_field">';

				if ( $args['label'] ) {
					$field .= '<label for="' . esc_attr( current( array_keys( $args['options'] ) ) ) . '" class="' . implode( ' ', $args['label_class'] ) .'">' . $args['label']. $required  . '</label>';
				}

				$url   = '';
				$title = '';

				// Value === attachment ID
				if ( $value ) {

					if ( ! in_array( $value, WC()->session->checkout_add_ons['files'], false ) ) {

						// The file was not uploaded in the current session. Clear value
						$value = '';

					} else {

						// Get the url and title for the provided attachment
						$url   = wp_get_attachment_url( $value );
						$title = get_the_title( $value );

					}
				}

				$field .= '<div class="wc-checkout-add-ons-input-file-plupload ' . implode( ' ', $args['input_class'] ) .'">';

				$field .= '<a class="wc-checkout-add-ons-dropzone ' . ( $url ? 'hide' : '' ) . '">';
				$field .= __( 'Drag file here or click to upload', 'woocommerce-checkout-add-ons' );
				$field .= '<div class="wc-checkout-add-ons-progress hide"><div class="bar"></div></div>';
				$field .='</a>';

				$field .= '<div class="wc-checkout-add-ons-preview ' . ( ! $url ? 'hide' : '' ) . '">';
				$field .= '<a href="' . $url . '" class="file">' . $title  . '</a>';
				$field .= '<a href="#" class="remove-file">' . __( 'Remove', 'woocommerce-checkout-add-ons' ) . '</a>';
				$field .= '</div>';

				$field .= '<div class="wc-checkout-add-ons-feedback hide"></div>';
				$field .= '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . $value . '" ' . implode( $custom_attributes ) . ' />';
				$field .= '<noscript>' . __( 'You need to enable Javascript to upload files', 'woocommerce-checkout-add-ons' ) . '</noscript>';
				$field .= '</div>';

				if ( isset( $args['description'] ) && '' !== $args['description'] ) {
					$field .= '<span class="description" id="' . esc_attr( $key . '-description' ) . '">' . esc_html( $args['description'] ) . '</span>';
				}

				$field .= '</p>' . $after;

			break;

			case 'wc_checkout_add_ons_checkbox' :

				$field .= '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $key ) . '_field">';

				// send 0 as value if the box is unchecked to allow clearing the field when the default state is checked
				$field .= '<input type="hidden" name="' . esc_attr( $key ) . '" value="0" />';

				if ( isset( $args['description'] ) && '' !== $args['description'] ) {
					$field .= '<span class="description" id="' . esc_attr( $key . '-description' ) . '">' . esc_html( $args['description'] ) . '</span>';
				}

				if ( $args['default'] === $value ) {
					$value = isset( $args['checkbox_default'] ) ? $args['checkbox_default'] : 0;
				}

				$field .= '<label class="checkbox ' . implode( ' ', $args['label_class'] ) . '">
						<span class="woocommerce-input-wrapper"><input type="checkbox" class="input-checkbox ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="1" ' . checked( $value, 1, false ) . ' ' . implode( ' ', $custom_attributes ) . ' /></span> ' . $args['label'] . $required . '</label>';

				$field .= '</p>' . $after;

			break;
		}

		return $field;
	}


	/**
	 * Initialize checkout add-ons session
	 *
	 * Gives us an array in WooCommerce session where
	 * we can store state about the currently selected
	 * add-ons as well as cart fees and add-on relations.
	 *
	 * @since 1.0
	 */
	public function init_session() {

		if ( isset( WC()->session ) && ! WC()->session->checkout_add_ons ) {

			WC()->session->checkout_add_ons = array(
				'fields' => array(),
				'fees'   => array(),
				'files'  => array(),
			);
		}
	}


	/**
	 * Get add-on checkout value
	 *
	 * @since 1.0
	 * @param mixed $value
	 * @param string $input
	 * @return mixed
	 */
	public function checkout_get_add_on_value( $value, $input ) {

		// Get add-on value from session
		if ( isset( WC()->session->checkout_add_ons['fields'][ $input ] ) ) {
			$value = WC()->session->checkout_add_ons['fields'][ $input ]['value'];
		}

		return $value;
	}


	/**
	 * Store add-on value and related fee in session
	 *
	 * @since 1.0
	 * @param string $id add-on ID
	 * @param string $name localized name of the add-on
	 * @param mixed $value add-on value
	 */
	public function store_add_on_in_session( $id, $name, $value ) {

		$session_data = WC()->session->checkout_add_ons;

		// Store add-on data with field as key for easy lookup on checkout_get_add_on_value
		$data = array( 'id' => $id, 'value' => $value );
		$session_data['fields'][ $id ] = $session_data['fees'][ sanitize_title( $name ) ] = $data;

		WC()->session->checkout_add_ons = $session_data;
	}


	/**
	 * Remove add-on data from session
	 *
	 * @since 1.0
	 * @param string $field name used in checkout
	 */
	public function remove_add_on_from_session( $field, $name ) {

		$session_data = WC()->session->checkout_add_ons;

		if ( isset( $session_data['fields'][ $field ] ) ) {
			unset( $session_data['fields'][ $field ] );
		}

		$name = sanitize_title( $name );

		if ( isset( $session_data['fees'][ $name ] ) ) {
			unset( $session_data['fees'][ $name ] );
		}

		WC()->session->checkout_add_ons = $session_data;
	}


	/**
	 * Store a reference to the uploaded file in session
	 *
	 * @since 1.0
	 * @param string $file attachment title
	 */
	public function store_uploaded_file_in_session( $file ) {

		$session_data = WC()->session->checkout_add_ons;

		$session_data['files'][] = $file;

		WC()->session->checkout_add_ons = $session_data;
	}


	/**
	 * Remove reference to the uploaded file from session
	 *
	 * @since 1.0
	 * @param string $file attachment title
	 */
	public function remove_uploaded_file_from_session( $file ) {

		$session_data = WC()->session->checkout_add_ons;

		$key = array_search( $file, $session_data['files'], false );
		if ( $key !== false ) {
			unset( $session_data['files'][ $key ] );
		}

		WC()->session->checkout_add_ons = $session_data;
	}


	/**
	 * Clear WC Checkout Add-Ons data from session
	 *
	 * @since 1.0
	 */
	public function clear_session() {

		unset( WC()->session->checkout_add_ons );
	}


	/**
	 * Store the fact that checkout order review is being displayed
	 *
	 * This helps us to limit the use of esc_html filter for appending
	 * add-on values to names in checkout order review area
	 *
	 * @since 1.0
	 * @param string $template_name
	 */
	public function indicate_checkout_order_review( $template_name ) {

		if ( 'checkout/review-order.php' === $template_name ) {
			$this->is_checkout_order_review = true;
		}
	}


	/**
	 * Indicate that we are not in order review area anymore
	 *
	 * @since 1.0
	 * @param string $template_name
	 */
	public function clear_checkout_order_review_indicator( $template_name ) {

		if ( 'checkout/review-order.php' === $template_name ) {
			$this->is_checkout_order_review = false;
		}
	}


	/**
	 * Display add-on values in order review area
	 *
	 * Works by filtering the esc_html'ed name of the add-on/fee
	 * and appending the add-on value to the name for display
	 * purposes only
	 *
	 * @since 1.0
	 * @param string $safe_text
	 * @param string $text
	 * @return string $safe_text
	 */
	public function display_add_on_value_in_checkout_order_review( $safe_text, $text ) {

		// Bail out if not in checkout order review area
		if ( ! $this->is_checkout_order_review ) {
			return $safe_text;
		}

		$text = sanitize_title( $text );

		if ( isset( WC()->session->checkout_add_ons['fees'][ $text ] ) ) {

			$session_data = WC()->session->checkout_add_ons['fees'][ $text ];

			// Get add-on value from session and set it for add-on
			$add_on = Add_On_Factory::get_add_on( $session_data['id'] );

			// removes our own filtering to account for the rare possibility that an option value is named the same way as the add on
			remove_filter( 'esc_html', array( $this, 'display_add_on_value_in_checkout_order_review' ), 10 );

			// Format add-on value
			$value = $add_on ? $add_on->normalize_value( $session_data['value'], true ) : null;

			// re-add back our filter after normalization is done
			add_filter( 'esc_html', array( $this, 'display_add_on_value_in_checkout_order_review' ), 10, 2 );

			// Append value to add-on name
			if ( $value ) {

				if ( 'text' === $add_on->get_type() || 'textarea' === $add_on->get_type() ) {
					$value = $add_on->truncate_label( $value );
				}

				$safe_text .= $this->label_separator . $value;
			}
		}

		return $safe_text;
	}


	/**
	 * Displays add-on labels in order review area
	 *
	 * Works by filtering the esc_html'ed name of the add-on/fee
	 * and replacing it by the add-on label for display purposes only
	 *
	 * @internal
	 *
	 * @since 2.1.3
	 *
	 * @param string $safe_text
	 * @param string $text
	 * @return string $safe_text
	 */
	public function display_add_on_label_in_checkout_order_review( $safe_text, $text ) {

		// bail out if not in checkout order review area
		if ( ! $this->is_checkout_order_review ) {
			return $safe_text;
		}

		$text = sanitize_title( $text );

		if ( isset( WC()->session->checkout_add_ons['fees'][ $text ] ) ) {

			$session_data = WC()->session->checkout_add_ons['fees'][ $text ];

			// get add-on from session
			$add_on = Add_On_Factory::get_add_on( $session_data['id'] );

			// replace name by label
			if ( $add_on && $label = $add_on->get_label() ) {

				$label     = $add_on->truncate_label( $label );
				$safe_text = str_replace( $add_on->get_name(), $label, $safe_text );
			}
		}

		return $safe_text;
	}


	/**
	 * Saves meta data for add-on fees.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 * @param int $item_id The item ID.
	 * @param \WC_Order_Item_Fee $item The fee item.
	 * @param int $order_id Order ID.
	 */
	public function save_add_on_meta( $item_id, $item, $order_id ) {

		if ( is_array( $item ) || ! $item->is_type( 'fee' ) ) {
			return;
		}

		// TODO replace the usage of the legacy fee property by hooking in a new action hook introduced in WC 3.0 {FN 2017-03-17}
		$legacy_fee = isset( $item->legacy_fee ) ? $item->legacy_fee : null;

		if ( ! is_object( $legacy_fee ) || ! isset( $legacy_fee->id ) ) {
			return;
		}

		$fee_id = $legacy_fee->id;

		if ( isset( WC()->session->checkout_add_ons['fees'][ $fee_id ] ) ) {

			$add_on_data = WC()->session->checkout_add_ons['fees'][ $fee_id ];

			// Get add-on value(s) from session.
			$add_on = isset( $add_on_data['id'] )    ? Add_On_Factory::get_add_on( $add_on_data['id'] ) : null;
			$value  = isset( $add_on_data['value'] ) ? $add_on_data['value'] : '';

			// Special formatting and sanitization for textareas.
			if ( 'textarea' === $add_on->get_type() ) {

				// Textareas get no label - it will be generated when displayed.
				$label = '';

				$value = trim( $value );

				// Standardize OSX & Windows line breaks.
				$value = str_replace( array( "\r\n", "\r" ), "\n", $value );

				// Strip more than two contiguous line breaks.
				$value = preg_replace( "/\n\n+/", "\n\n", $value );

				// Sanitize each line.
				$value = array_map( 'wc_clean', explode( "\n", $value ) );

				$value = implode( "\n", $value );

			} else if ( 'file' === $add_on->get_type() ) {

				// Files get no label - it will be generated when displayed.
				$label = '';

				$files = explode( ',', $value );

				foreach ( $files as $file_id )	{

					// Attach the file to the order.
					wp_update_post( array(
						'ID'          => $file_id,
						'post_parent' => $order_id,
					) );
				}

			} else {

				// Sanitize add-on value(s) for saving:
				// `urldecode()` is used because `woocommerce_form_field()`
				// escapes values with `esc_attr()` and could mangle strings
				// with special or accented characters.
				$value = is_array( $value ) ? array_map( 'wc_clean', array_map( 'urldecode', $value ) ) : wc_clean( urldecode( $value ) );

				// Label(s) - for select, radio, etc.
				$label = $add_on->normalize_value( $value, false );
			}

			// Save add-on meta: id, label(s), value(s).
			wc_add_order_item_meta( $item_id, '_wc_checkout_add_on_id',    $add_on->get_id() );
			wc_add_order_item_meta( $item_id, '_wc_checkout_add_on_value', $value );
			wc_add_order_item_meta( $item_id, '_wc_checkout_add_on_label', $label );
		}
	}


	/**
	 * Add checkout add-on meta to order row label for display purposes in
	 * my-account/view-order and order emails.
	 *
	 * @since 1.0
	 * @param array $total_rows
	 * @param \WC_Order $order
	 * @return array $total_rows
	 */
	public function append_order_add_on_fee_meta( $total_rows, $order ) {

		foreach ( $total_rows as $row_key => $row ) {

			$parts = explode( '_', $row_key );
			$item_type = $parts[0];
			$item_id = isset( $parts[1] ) ? $parts[1] : null;

			if ( 'fee' === $item_type ) {

				$add_on_id = wc_get_order_item_meta( $item_id, '_wc_checkout_add_on_id' );

				if ( $add_on_id ) {

					$value = wc_get_order_item_meta( $item_id, '_wc_checkout_add_on_value' );
					$label = wc_get_order_item_meta( $item_id, '_wc_checkout_add_on_label' );

					if ( $add_on = Add_On_Factory::get_add_on( $add_on_id ) ) {

						// make sure this isn't a subscription before adding the value HTML, as the Subscriptions template runs this through esc_html()
						if ( ! wc_checkout_add_ons()->is_subscriptions_active() || ! wcs_is_subscription( $order ) ) {

							if ( 'textarea' === $add_on->get_type() ) {
								$label = '<div class="wc-checkout-add-ons-add-on-value textarea-value">' . $add_on->normalize_value( $value, false ) . '</div>';
							} elseif ( ! $label && 'file' === $add_on->get_type() ) {
								$label = '<span class="wc-checkout-add-ons-add-on-value">' . $add_on->normalize_value( $value, false ) . '</span>';
							}
						}
					}

					if ( $label ) {
						$label = $this->label_separator . ( is_array( $label ) ? implode( ', ', $label ) : $label );
					}

					$total_rows[ $row_key ]['label'] .= $label;
				}
			}
		}

		return $total_rows;
	}


	/**
	 * Include free order add-on fee meta
	 *
	 * @since 1.0
	 * @param bool $excl_free true is free item meta should be excluded
	 * @param int $item_id the item meta id
	 * @return bool $excl_free
	 */
	public function include_free_order_add_on_fee_meta( $excl_free, $item_id ) {

		$excl_free = wc_get_order_item_meta( $item_id, '_wc_checkout_add_on_id' ) ? false : $excl_free;

		return $excl_free;
	}


	/**
	 * Get the supported MIME types for file upload.
	 *
	 * @since 1.7.1
	 * @return array
	 */
	protected function get_mime_types() {

		$extensions = $types = array();

		$allowed_types = get_allowed_mime_types();

		// break the allowed extensions into their respective types
		foreach ( $allowed_types as $allowed_extensions => $type ) {

			$type = substr( $type, 0, strpos( $type, '/' ) );

			$extensions[ $type ][] = str_replace( '|', ',', $allowed_extensions );
		}

		// format the extensions for plupload
		foreach ( $extensions as $type => $exts ) {

			$types[] = array(
				'title'       => $type,
				'extensions' => implode( ',', $exts ),
			);
		}

		/**
		 * Filters the allowed upload mime types.
		 *
		 * @since 1.7.1
		 *
		 * @param array $types the allowed types and their extensions, each an array of {
		 *
		 *     @type string $name       the mime type name
		 *     @type string $extensions the supported extensions, comma separated
		 * }
		 */
		return apply_filters( 'wc_checkout_add_ons_mime_types', $types );
	}


	/**
	 * Refreshes add-on checkout fields when cart is updated.
	 *
	 * @internal
	 *
	 * @since 2.1.0
	 *
	 * @param array $fragments
	 * @return array
	 */
	public function refresh_checkout_fields( $fragments ) {

		ob_start();

		$this->render_add_ons();

		$fragments['#wc_checkout_add_ons'] = ob_get_clean();

		return $fragments;
	}


	/**
	 * Enqueues a JavaScript snippet to fix the appearance of textarea and file add-on values.
	 *
	 * The HTML code we include in the labels is not rendered correctly because WooCommerce passes them through esc_html().
	 *
	 * @internal
	 *
	 * @since 2.2.5
	 */
	public function enqueue_script_to_fix_add_on_labels() {

		$javascript = "
			jQuery( '.woocommerce-order-details th[scope=\"row\"]' ).each( function( i, row ) {
				if ( -1 !== row.textContent.indexOf( 'wc-checkout-add-ons-add-on-value' ) ) {
					row.innerHTML = row.textContent;
				}
			} );";

		wc_enqueue_js( $javascript );
	}


	/**
	 * Restores missing post data for custom checkout fields that may be filtered out by WooCommerce.
	 *
	 * Data loss is observed when fields contain sanitized characters from other alphabets like Cyrillic.
	 *
	 * @internal
	 *
	 * @since 2.5.1
	 *
	 * @param array $post_data the posted field data
	 * @return array
	 */
	public function restore_missing_posted_data( $post_data ) {

		$add_ons = Add_On_Factory::get_add_ons();

		foreach ( $add_ons as $add_on ) {

			$key = $add_on->get_key();

			if ( empty( $post_data[ $key ] ) && ! empty( $_POST[ $key ] ) ) {

				// restores a custom checkout field set on $_POST but missing in $post_data
				$post_data[ $key ] = $_POST[ $key ];
			}
		}

		return $post_data;
	}
}
