<?php
/**
 * Class to handle feature Coupons By Location
 *
 * @author      StoreApps
 * @category    Admin
 * @package     wocommerce-smart-coupons/includes
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_Coupons_By_Location' ) ) {

	/**
	 * Class WC_SC_Coupons_By_Location
	 */
	class WC_SC_Coupons_By_Location {

		/**
		 * Variable to hold instance of this class
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Additional Locations
		 *
		 * @var $additional_locations
		 */
		public $additional_locations = array();

		/**
		 * Countries
		 *
		 * @var $countries
		 */
		public $countries;

		/**
		 * Global Additional Locations
		 *
		 * @var $global_additional_locations
		 */
		public $global_additional_locations = array();

		/**
		 * Custom Locations
		 *
		 * @var $custom_locations
		 */
		public $custom_locations = array();

		/**
		 * Locations Lookup In
		 *
		 * @var $locations_lookup_in
		 */
		public $locations_lookup_in;

		/**
		 * Address
		 *
		 * @var $address
		 */
		public $address;

		/**
		 * Constructor
		 */
		private function __construct() {

			add_action( 'init', array( $this, 'initialize_cbl_additional_locations' ) );

			add_filter( 'woocommerce_coupon_is_valid', array( $this, 'validate' ), 11, 2 );

			if ( is_admin() ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_javascript_css' ) );
				add_action( 'woocommerce_coupon_options_usage_restriction', array( $this, 'usage_restriction' ) );
				add_action( 'save_post', array( $this, 'process_meta' ), 10, 2 );
			}

			add_filter( 'wc_smart_coupons_export_headers', array( $this, 'export_headers' ) );
			add_filter( 'wc_sc_export_coupon_meta_data', array( $this, 'export_coupon_meta_data' ), 10, 2 );
			add_filter( 'smart_coupons_parser_postmeta_defaults', array( $this, 'postmeta_defaults' ) );
			add_filter( 'sc_generate_coupon_meta', array( $this, 'generate_coupon_meta' ), 10, 2 );
			add_filter( 'is_protected_meta', array( $this, 'make_action_meta_protected' ), 10, 3 );
			add_filter( 'wc_sc_process_coupon_meta_value_for_import', array( $this, 'process_coupon_meta_value_for_import' ), 10, 2 );

			add_action( 'wc_sc_new_coupon_generated', array( $this, 'copy_coupon_action_meta' ) );

		}

		/**
		 * Get single instance of this class
		 *
		 * @return this class Singleton object of this class
		 */
		public static function get_instance() {
			// Check if instance is already exists.
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Handle call to functions which is not available in this class
		 *
		 * @param string $function_name The function name.
		 * @param array  $arguments Array of arguments passed while calling $function_name.
		 * @return result of function call
		 */
		public function __call( $function_name, $arguments = array() ) {

			global $woocommerce_smart_coupon;

			if ( ! is_callable( array( $woocommerce_smart_coupon, $function_name ) ) ) {
				return;
			}

			if ( ! empty( $arguments ) ) {
				return call_user_func_array( array( $woocommerce_smart_coupon, $function_name ), $arguments );
			} else {
				return call_user_func( array( $woocommerce_smart_coupon, $function_name ) );
			}

		}

		/**
		 * Initialize additional locations
		 */
		public function initialize_cbl_additional_locations() {
			if ( empty( $this->global_additional_locations ) ) {
				$this->global_additional_locations = get_option( 'sa_cbl_additional_locations', array() );
			}
		}

		/**
		 * Styles & scripts
		 */
		public function enqueue_admin_javascript_css() {

			global $woocommerce_smart_coupon;

			$post_type = get_post_type();
			$get_page  = ( ! empty( $_GET['page'] ) ) ? wc_clean( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore

			if ( 'shop_coupon' !== $post_type && 'wc-smart-coupons' !== $get_page ) {
				return;
			}

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			// Add customized chosen javascript library for adding new location from same field.
			wp_register_script( 'sa_coupons_by_location_chosen', plugins_url( '/', dirname( __FILE__ ) ) . 'assets/js/chosen.jquery' . $suffix . '.js', array( 'jquery' ), $woocommerce_smart_coupon->plugin_data['Version'], true );
			wp_enqueue_style( 'sa_coupons_by_location_css', plugins_url( '/', dirname( __FILE__ ) ) . 'assets/css/cbl-admin' . $suffix . '.css', array(), $woocommerce_smart_coupon->plugin_data['Version'] );

		}

		/**
		 * Display field for coupon by location
		 */
		public function usage_restriction() {
			global $post;

			if ( ! wp_script_is( 'sa_coupons_by_location_chosen' ) ) {
				wp_enqueue_script( 'sa_coupons_by_location_chosen' );
			}

			$this->locations_lookup_in = get_post_meta( $post->ID, 'sa_cbl_locations_lookup_in', true );
			if ( empty( $this->locations_lookup_in ) ) {
				$this->locations_lookup_in = array( 'address' => 'billing' );
				update_post_meta( $post->ID, 'sa_cbl_locations_lookup_in', $this->locations_lookup_in );
			}

			$this->address = $this->locations_lookup_in['address'];

			?>

			<div class="options_group smart-coupons-field" id="locations">
				<p class="form-field">
					<span class='search_in'><label><?php echo esc_html__( 'Address to look in', 'woocommerce-smart-coupons' ); ?></label></span>
					<label for="billing" class="billing">
						<input type="radio" name="sa_cbl_search_in[address]" value="billing" <?php ( ! empty( $this->address ) && 'billing' === $this->address ) ? checked( $this->address, 'billing' ) : ''; ?> />
						<?php echo esc_html__( 'Billing', 'woocommerce-smart-coupons' ); ?>
					</label> &nbsp;
					<label for="shipping" class="shipping">
						<input type="radio" name="sa_cbl_search_in[address]" value="shipping" <?php ( ! empty( $this->address ) && 'shipping' === $this->address ) ? checked( $this->address, 'shipping' ) : ''; ?> />
						<?php echo esc_html__( 'Shipping', 'woocommerce-smart-coupons' ); ?>
					</label>
				</p>
				<p class="form-field">
					<label class="options_header"><?php echo esc_html__( 'Locations', 'woocommerce-smart-coupons' ); ?></label>
					<?php
						$locations = get_post_meta( $post->ID, 'sa_cbl_' . $this->address . '_locations', true );
					if ( empty( $locations ) || ! is_array( $locations ) ) {
						$locations = array();
					}
					if ( ! array_key_exists( 'additional_locations', $locations ) || ! is_array( $locations['additional_locations'] ) ) {
						$locations['additional_locations'] = array();
					}
						$this->additional_locations = array_map( 'html_entity_decode', array_map( 'strtolower', $locations['additional_locations'] ) );
						$this->countries            = array_map( 'strtolower', WC()->countries->countries );

						echo '<select name="locations[additional_locations][]" id="cc_list" data-placeholder="' . esc_html__( 'Select location', 'woocommerce-smart-coupons' ) . '..." class="sa_cbl_search_location sa_cbl_add_location" multiple>';

						// Countries.
						echo ' <optgroup label="' . esc_html__( 'Select Country', 'woocommerce-smart-coupons' ) . '"> ';
					foreach ( $this->countries as $country ) {
						echo '<option value="' . esc_attr( $country ) . '"';
						if ( ! empty( $this->additional_locations ) ) {
							$encoding        = mb_detect_encoding( $country, 'UTF-8, ISO-8859-1', true );
							$decoded_country = ( false !== $encoding ) ? html_entity_decode( $country, ENT_COMPAT, $encoding ) : $country;
							echo esc_attr( selected( in_array( $decoded_country, $this->additional_locations, true ) ) );
						}
						echo '>' . esc_html( ucwords( strtolower( $country ) ) ) . '</option>';
					}
						echo ' </optgroup> ';

						// others.
						echo ' <optgroup label="' . esc_html__( 'Select Additional Locations', 'woocommerce-smart-coupons' ) . '"> ';
					if ( ! empty( $this->global_additional_locations ) ) {
						foreach ( $this->global_additional_locations as $list ) {
							echo '<option value="' . esc_attr( $list ) . '"';
							if ( ! empty( $this->additional_locations ) ) {
								$encoding     = mb_detect_encoding( $list, 'UTF-8, ISO-8859-1', true );
								$decoded_list = ( false !== $encoding ) ? html_entity_decode( $list, ENT_COMPAT, $encoding ) : $list;
								echo esc_attr( selected( in_array( $decoded_list, $this->additional_locations, true ) ) );
							}
							echo '>' . esc_html( ucwords( strtolower( $list ) ) ) . '</option>';
						}
					}
						echo ' </optgroup> ';

						echo '</select>';
					?>
				</p>

			</div>
			<?php

			$js = "jQuery('select.sa_cbl_search_location').chosen({
						disable_search_threshold: 10,
						width: '50%'
					});";

			$js .= "jQuery('#cc_list_chosen').on('click', function(){
						var cc_height = jQuery('#cc_list_chosen').height();
						jQuery('#cc_list_chosen .chosen-drop').attr('style', 'bottom: '+cc_height+'px !important; border-bottom: 0 !important; border-top: 1px solid #aaa !important; top: auto !important;');
					});
					";

			wc_enqueue_js( $js );
		}

		/**
		 * Save coupon by location data in meta
		 *
		 * @param  Integer $post_id The coupon post ID.
		 * @param  WP_Post $post    The coupon post.
		 */
		public function process_meta( $post_id = 0, $post = null ) {

			if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) {
				return;
			}
			if ( empty( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( wc_clean( wp_unslash( $_POST['woocommerce_meta_nonce'] ) ), 'woocommerce_save_data' ) ) { // phpcs:ignore
				return;
			}
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			if ( 'shop_coupon' !== $post->post_type ) {
				return;
			}
			if ( is_int( wp_is_post_revision( $post ) ) ) {
				return;
			}
			if ( is_int( wp_is_post_autosave( $post ) ) ) {
				return;
			}
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			$locations                            = ( ! empty( $_POST['locations'] ) ) ? wc_clean( wp_unslash( $_POST['locations'] ) ) : array(); // phpcs:ignore
			$this->locations_lookup_in['address'] = ( ! empty( $_POST['sa_cbl_search_in']['address'] ) ) ? wc_clean( wp_unslash( $_POST['sa_cbl_search_in']['address'] ) ) : ''; // phpcs:ignore

			update_post_meta( $post_id, 'sa_cbl_' . $this->locations_lookup_in['address'] . '_locations', $locations );

			if ( isset( $this->locations_lookup_in['address'] ) && ! empty( $this->locations_lookup_in['address'] ) ) {
				update_post_meta( $post_id, 'sa_cbl_locations_lookup_in', $this->locations_lookup_in );
			}

			$this->countries = array_map( 'strtolower', WC()->countries->countries );
			if ( ! empty( $locations['additional_locations'] ) ) {
				$this->additional_locations = array_map( 'strtolower', $locations['additional_locations'] );
			}

			if ( count( $this->additional_locations ) > 0 ) {

				// Loop through all location entered in Billing location of coupons.
				// and collect those location which is not available in WooCommerce countries.
				foreach ( $this->additional_locations as $location ) {
					if ( ! in_array( $location, $this->countries, true ) ) {
						$this->custom_locations[] = strtolower( $location );
					}
				}

				// Add new location with already saved locations.
				if ( false !== $this->global_additional_locations && ! empty( $this->global_additional_locations ) ) {
					$this->global_additional_locations = array_merge( $this->global_additional_locations, $this->custom_locations );
				} else {
					$this->global_additional_locations = $this->custom_locations;
				}

				// Discard duplicate values, arrange alphabetically & save.
				$this->global_additional_locations = array_unique( $this->global_additional_locations );
				sort( $this->global_additional_locations );
				update_option( 'sa_cbl_additional_locations', $this->global_additional_locations, 'no' );
			}

		}

		/**
		 * Validate the coupon based on location
		 *
		 * @param  boolean   $valid  Is valid or not.
		 * @param  WC_Coupon $coupon The coupon object.
		 *
		 * @throws Exception If the coupon is invalid.
		 * @return boolean           Is valid or not
		 */
		public function validate( $valid, $coupon ) {
			global $checkout;

			// If coupon is invalid already, no need for further checks.
			if ( ! $valid ) {
				return $valid;
			}

			$coupon_id = ( $this->is_wc_gte_30() ) ? $coupon->get_id() : $coupon->id;

			$this->locations_lookup_in = get_post_meta( $coupon_id, 'sa_cbl_locations_lookup_in', true );

			if ( empty( $this->locations_lookup_in ) || empty( $this->locations_lookup_in['address'] ) ) {
				return $valid;
			}

			$locations = get_post_meta( $coupon_id, 'sa_cbl_' . $this->locations_lookup_in['address'] . '_locations', true );

			if ( ! empty( $locations ) && is_array( $locations ) && ! empty( $locations['additional_locations'] ) && is_array( $locations['additional_locations'] ) && array_key_exists( 'additional_locations', $locations ) ) {

				$wc_customer  = WC()->customer;
				$wc_countries = WC()->countries;

				// Collect country, state & city.
				if ( 'billing' === $this->locations_lookup_in['address'] ) {

					if ( $this->is_wc_gte_30() ) {
						$customer_billing_country  = $wc_customer->get_billing_country();
						$customer_billing_state    = $wc_customer->get_billing_state();
						$customer_billing_city     = $wc_customer->get_billing_city();
						$customer_billing_postcode = $wc_customer->get_billing_postcode();

						$current_country   = ( ! empty( $customer_billing_country ) ) ? $customer_billing_country : '';
						$current_state     = ( ! empty( $customer_billing_state ) ) ? $customer_billing_state : '';
						$current_city      = ( ! empty( $customer_billing_city ) ) ? $customer_billing_city : '';
						$current_post_code = ( ! empty( $customer_billing_postcode ) ) ? $customer_billing_postcode : '';
					} else {
						$current_country   = ( ! empty( $wc_customer->country ) ) ? $wc_customer->country : '';
						$current_state     = ( ! empty( $wc_customer->state ) ) ? $wc_customer->state : '';
						$current_city      = ( ! empty( $wc_customer->city ) ) ? $wc_customer->city : '';
						$current_post_code = ( ! empty( $wc_customer->postcode ) ) ? $wc_customer->postcode : '';
					}
				} else {
					if ( $this->is_wc_gte_30() ) {
						$customer_shipping_country  = $wc_customer->get_shipping_country();
						$customer_shipping_state    = $wc_customer->get_shipping_state();
						$customer_shipping_city     = $wc_customer->get_shipping_city();
						$customer_shipping_postcode = $wc_customer->get_shipping_postcode();

						$current_country   = ( ! empty( $customer_shipping_country ) ) ? $customer_shipping_country : '';
						$current_state     = ( ! empty( $customer_shipping_state ) ) ? $customer_shipping_state : '';
						$current_city      = ( ! empty( $customer_shipping_city ) ) ? $customer_shipping_city : '';
						$current_post_code = ( ! empty( $customer_shipping_postcode ) ) ? $customer_shipping_postcode : '';
					} else {
						$current_country   = ( ! empty( $wc_customer->shipping_country ) ) ? $wc_customer->shipping_country : '';
						$current_state     = ( ! empty( $wc_customer->shipping_state ) ) ? $wc_customer->shipping_state : '';
						$current_city      = ( ! empty( $wc_customer->shipping_city ) ) ? $wc_customer->shipping_city : '';
						$current_post_code = ( ! empty( $wc_customer->shipping_postcode ) ) ? $wc_customer->shipping_postcode : '';
					}
				}

				// Convert country code or state code to actual country & state.
				$country   = ( ! empty( $wc_countries->countries[ $current_country ] ) ) ? strtolower( $wc_countries->countries[ $current_country ] ) : '';
				$state     = ( ! empty( $wc_countries->states[ $current_country ][ $current_state ] ) ) ? strtolower( $wc_countries->states[ $current_country ][ $current_state ] ) : strtolower( $current_state );
				$city      = ( ! empty( $current_city ) ) ? strtolower( $current_city ) : '';
				$post_code = ( ! empty( $current_post_code ) ) ? strtolower( $current_post_code ) : '';

				// Loop through additional_locations and return true on matching with either country, state or city.
				// Return false otherwise.
				foreach ( $locations['additional_locations'] as $additional_location ) {
					if ( $country === $additional_location || $state === $additional_location || $city === $additional_location || $post_code === $additional_location ) {
						return true;
					}
				}
				throw new Exception( __( 'Coupon is not valid for the', 'woocommerce-smart-coupons' ) . ' ' . ( ( 'billing' === $this->locations_lookup_in['address'] ) ? __( 'billing address', 'woocommerce-smart-coupons' ) : __( 'shipping address', 'woocommerce-smart-coupons' ) ) );
			}

			return $valid;
		}

		/**
		 * Add meta in export headers
		 *
		 * @param  array $headers Existing headers.
		 * @return array
		 */
		public function export_headers( $headers = array() ) {

			$cbl_headers = array(
				'sa_cbl_locations_lookup_in' => __( 'Locations lookup in', 'woocommerce-smart-coupons' ),
				'sa_cbl_billing_locations'   => __( 'Billing Locations', 'woocommerce-smart-coupons' ),
				'sa_cbl_shipping_locations'  => __( 'Shipping Locations', 'woocommerce-smart-coupons' ),
			);

			return array_merge( $headers, $cbl_headers );

		}

		/**
		 * Function to handle coupon meta data during export of existing coupons
		 *
		 * @param  mixed $meta_value The meta value.
		 * @param  array $args       Additional arguments.
		 * @return string Processed meta value
		 */
		public function export_coupon_meta_data( $meta_value = '', $args = array() ) {

			if ( ! empty( $args['meta_key'] ) && in_array( $args['meta_key'], array( 'sa_cbl_billing_locations', 'sa_cbl_shipping_locations' ), true ) ) {
				switch ( $args['meta_key'] ) {
					case 'sa_cbl_billing_locations':
					case 'sa_cbl_shipping_locations':
						$meta_value = ( ! empty( $meta_value['additional_locations'] ) ) ? implode( '|', wc_clean( wp_unslash( $meta_value['additional_locations'] ) ) ) : '';
						break;
				}
			}

			return $meta_value;

		}

		/**
		 * Post meta defaults for CBL's meta
		 *
		 * @param  array $defaults Existing postmeta defaults.
		 * @return array
		 */
		public function postmeta_defaults( $defaults = array() ) {

			$cbl_defaults = array(
				'sa_cbl_locations_lookup_in' => '',
				'sa_cbl_billing_locations'   => '',
				'sa_cbl_shipping_locations'  => '',
			);

			return array_merge( $defaults, $cbl_defaults );
		}

		/**
		 * Add CBL's meta with value in coupon meta
		 *
		 * @param  array $data The row data.
		 * @param  array $post The POST values.
		 * @return array Modified data
		 */
		public function generate_coupon_meta( $data = array(), $post = array() ) {

			$data['sa_cbl_locations_lookup_in'] = ( ! empty( $post['sa_cbl_search_in']['address'] ) ) ? wc_clean( wp_unslash( $post['sa_cbl_search_in']['address'] ) ) : '';
			$data['sa_cbl_billing_locations']   = ( ! empty( $data['sa_cbl_locations_lookup_in'] ) && 'billing' === $data['sa_cbl_locations_lookup_in'] && ! empty( $post['locations']['additional_locations'] ) && is_array( $post['locations']['additional_locations'] ) ) ? implode( '|', wc_clean( wp_unslash( $post['locations']['additional_locations'] ) ) ) : '';
			$data['sa_cbl_shipping_locations']  = ( ! empty( $data['sa_cbl_locations_lookup_in'] ) && 'shipping' === $data['sa_cbl_locations_lookup_in'] && ! empty( $post['locations']['additional_locations'] ) && is_array( $post['locations']['additional_locations'] ) ) ? implode( '|', wc_clean( wp_unslash( $post['locations']['additional_locations'] ) ) ) : '';

			if ( ! empty( $post['locations']['additional_locations'] ) ) {
				$additional_locations = wc_clean( wp_unslash( $post['locations']['additional_locations'] ) );
				$this->update_global_additional_locations( $additional_locations );
			}

			return $data;
		}

		/**
		 * Make meta data of SC CBL, protected
		 *
		 * @param bool   $protected Is protected.
		 * @param string $meta_key The meta key.
		 * @param string $meta_type The meta type.
		 * @return bool $protected
		 */
		public function make_action_meta_protected( $protected, $meta_key, $meta_type ) {
			$sc_meta = array(
				'sa_cbl_locations_lookup_in' => '',
				'sa_cbl_billing_locations'   => '',
				'sa_cbl_shipping_locations'  => '',
			);
			if ( in_array( $meta_key, $sc_meta, true ) ) {
				return true;
			}
			return $protected;
		}

		/**
		 * Function to copy CBL meta in newly generated coupon
		 *
		 * @param  array $args The arguments.
		 */
		public function copy_coupon_action_meta( $args = array() ) {

			$new_coupon_id = ( ! empty( $args['new_coupon_id'] ) ) ? absint( $args['new_coupon_id'] ) : 0;
			$coupon        = ( ! empty( $args['ref_coupon'] ) ) ? $args['ref_coupon'] : false;

			if ( empty( $new_coupon_id ) || empty( $coupon ) ) {
				return;
			}

			if ( $this->is_wc_gte_30() ) {
				$locations_lookup_in = $coupon->get_meta( 'sa_cbl_locations_lookup_in' );
				$billing_locations   = $coupon->get_meta( 'sa_cbl_billing_locations' );
				$shipping_locations  = $coupon->get_meta( 'sa_cbl_shipping_locations' );
			} else {
				$old_coupon_id       = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
				$locations_lookup_in = get_post_meta( $old_coupon_id, 'sa_cbl_locations_lookup_in', true );
				$billing_locations   = get_post_meta( $old_coupon_id, 'sa_cbl_billing_locations', true );
				$shipping_locations  = get_post_meta( $old_coupon_id, 'sa_cbl_shipping_locations', true );
			}
			update_post_meta( $new_coupon_id, 'sa_cbl_locations_lookup_in', $locations_lookup_in );
			update_post_meta( $new_coupon_id, 'sa_cbl_billing_locations', $billing_locations );
			update_post_meta( $new_coupon_id, 'sa_cbl_shipping_locations', $shipping_locations );

			if ( ! empty( $billing_locations['additional_locations'] ) ) {
				$this->update_global_additional_locations( $billing_locations['additional_locations'] );
			}

			if ( ! empty( $shipping_locations['additional_locations'] ) ) {
				$this->update_global_additional_locations( $shipping_locations['additional_locations'] );
			}

		}

		/**
		 * Process coupon meta value for import
		 *
		 * @param  mixed $meta_value The meta value.
		 * @param  array $args       Additional Arguments.
		 * @return mixed $meta_value
		 */
		public function process_coupon_meta_value_for_import( $meta_value = null, $args = array() ) {

			if ( ! empty( $args['meta_key'] ) && in_array( $args['meta_key'], array( 'sa_cbl_locations_lookup_in', 'sa_cbl_billing_locations', 'sa_cbl_shipping_locations' ), true ) ) {
				switch ( $args['meta_key'] ) {
					case 'sa_cbl_locations_lookup_in':
						$meta_value = ( ! empty( $args['postmeta']['sa_cbl_locations_lookup_in'] ) ) ? array( 'address' => wc_clean( wp_unslash( $args['postmeta']['sa_cbl_locations_lookup_in'] ) ) ) : array();
						break;
					case 'sa_cbl_billing_locations':
						$meta_value = ( ! empty( $args['postmeta']['sa_cbl_billing_locations'] ) ) ? array( 'additional_locations' => explode( '|', wc_clean( wp_unslash( $args['postmeta']['sa_cbl_billing_locations'] ) ) ) ) : array();
						break;
					case 'sa_cbl_shipping_locations':
						$meta_value = ( ! empty( $args['postmeta']['sa_cbl_shipping_locations'] ) ) ? array( 'additional_locations' => explode( '|', wc_clean( wp_unslash( $args['postmeta']['sa_cbl_shipping_locations'] ) ) ) ) : array();
						break;
				}
				if ( in_array( $args['meta_key'], array( 'sa_cbl_billing_locations', 'sa_cbl_shipping_locations' ), true ) && ! empty( $meta_value['additional_locations'] ) ) {
					$this->update_global_additional_locations( $meta_value['additional_locations'] );
				}
			}

			return $meta_value;
		}

		/**
		 * Update global additional locations
		 *
		 * @param array $additional_locations The locations.
		 */
		public function update_global_additional_locations( $additional_locations = array() ) {

			if ( empty( $additional_locations ) ) {
				return;
			}

			$additional_locations = array_map( 'strtolower', $additional_locations );
			$wc_countries         = array_map( 'strtolower', WC()->countries->countries );

			foreach ( $wc_countries as $index => $country ) {
				$encoding               = mb_detect_encoding( $country, 'UTF-8, ISO-8859-1', true );
				$wc_countries[ $index ] = ( false !== $encoding ) ? html_entity_decode( $country, ENT_COMPAT, $encoding ) : $country;
			}

			if ( count( $additional_locations ) > 0 ) {

				$custom_locations = array();

				// Loop through all location entered in Billing location of coupons.
				// and collect those location which is not available in WooCommerce countries.
				foreach ( $additional_locations as $location ) {
					if ( ! in_array( $location, $wc_countries, true ) ) {
						$custom_locations[] = $location;
					}
				}

				$global_additional_locations = ( ! empty( $this->global_additional_locations ) ) ? $this->global_additional_locations : get_option( 'sa_cbl_additional_locations', array() );

				// Add new location with already saved locations.
				if ( false !== $global_additional_locations && ! empty( $global_additional_locations ) ) {
					$global_additional_locations = array_merge( $global_additional_locations, $custom_locations );
				} else {
					$global_additional_locations = $custom_locations;
				}

				// Discard duplicate values, arrange alphabetically & save.
				$global_additional_locations = array_unique( $global_additional_locations );
				sort( $global_additional_locations );
				update_option( 'sa_cbl_additional_locations', $global_additional_locations, 'no' );
			}

		}

	}
}

WC_SC_Coupons_By_Location::get_instance();
