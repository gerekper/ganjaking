<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_MS_Front {

	private $wcms;
	private $country;

	public function __construct( WC_Ship_Multiple $wcms ) {
		$this->wcms = $wcms;

		// WCMS Front
		add_filter( 'body_class', array( $this, 'output_body_class' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'front_scripts' ), 11 );
		add_action( 'woocommerce_view_order', array( $this, 'show_multiple_addresses_notice' ) );
		add_action( 'woocommerce_email_after_order_table', array( $this, 'email_order_item_addresses' ) );
		add_action( 'woocommerce_order_details_after_order_table', array( $this, 'list_order_item_addresses' ) );

		// cleanup
		add_action( 'wp_logout', array( $this->wcms, 'clear_session' ) );

		add_action( 'plugins_loaded', array( $this, 'load_account_addresses' ), 11 );

		// inline script
		add_action( 'wp_footer', array( $this, 'inline_scripts' ) );

	}

	public function load_account_addresses() {
		// my account
		if ( version_compare( WC_VERSION, '2.6', '>=' ) ) {
			add_filter( 'woocommerce_my_account_get_addresses', array( $this, 'account_address_labels' ), 10, 2 );
			add_filter( 'woocommerce_my_account_my_address_formatted_address', array( $this, 'account_address_formatted' ), 10, 3 );

			add_filter( 'woocommerce_my_account_edit_address_field_value', array( $this, 'edit_address_field_value' ), 10, 3 );
			add_action( 'template_redirect', array( $this, 'save_address' ), 1 );

			// Delete address in edit address page
			add_action( 'woocommerce_before_edit_account_address_form', array( $this, 'delete_address_button' ) );
			add_action( 'wp_loaded', array( $this, 'delete_address_action' ), 20 );

			// Add address button on my account addresses page
			add_action( 'woocommerce_account_edit-address_endpoint', array( $this, 'add_address_button' ), 90 );

			// Initialize address fields
			add_action( 'woocommerce_account_content', array( $this, 'init_address_fields' ), 1 );

		} else {
			add_action( 'woocommerce_after_my_account', array( $this, 'my_account' ) );
		}
	}

	/**
	 * Add woocommerce and woocommerce-page classes to the body tag of WCMS pages
	 *
	 * @param array $classes
	 * @return array
	 */
	public function output_body_class( $classes ) {
		if ( is_page( wc_get_page_id( 'multiple_addresses' ) ) || is_page( wc_get_page_id( 'account_addresses' ) ) ) {
			$classes[] = 'woocommerce';
			$classes[] = 'woocommerce-page';
		}

		return $classes;
	}

	/**
	 * Enqueue scripts and styles for the frontend
	 */
	public function front_scripts() {
		global $post;

		$page_ids = array(
			wc_get_page_id( 'account_addresses' ),
			wc_get_page_id( 'multiple_addresses' ),
			wc_get_page_id( 'myaccount' ),
			wc_get_page_id( 'checkout' ),
			wc_get_page_id( 'cart' )
		);

		if ( ! $post || ( $post && ! in_array( $post->ID, $page_ids ) ) ) {
			return;
		}

		$user = wp_get_current_user();

		wp_enqueue_script( 'jquery',                null );
		wp_enqueue_script( 'jquery-ui-core',        null, array( 'jquery' ) );
		wp_enqueue_script( 'jquery-ui-mouse',       null, array( 'jquery-ui-core' ) );
		wp_enqueue_script( 'jquery-ui-draggable',   null, array( 'jquery-ui-core' ) );
		wp_enqueue_script( 'jquery-ui-droppable',   null, array( 'jquery-ui-core' ) );
		wp_enqueue_script( 'jquery-ui-datepicker',  null, array( 'jquery-ui-core' ) );
		wp_enqueue_script( 'jquery-masonry',        null, array( 'jquery-ui-core' ) );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_style(  'thickbox' );
		wp_enqueue_script( 'jquery-blockui' );

		// touchpunch to support mobile browsers
		wp_enqueue_script( 'jquery-ui-touch-punch', plugins_url( 'assets/js/jquery.ui.touch-punch.min.js', WC_Ship_Multiple::FILE ), array( 'jquery-ui-mouse', 'jquery-ui-widget' ) );

		if ( $user->ID != 0 ) {
			wp_enqueue_script( 'multiple_shipping_script', plugins_url( 'assets/js/front.min.js', WC_Ship_Multiple::FILE ) );

			wp_localize_script( 'multiple_shipping_script', 'WC_Shipping', array(
				// URL to wp-admin/admin-ajax.php to process the request
				'ajaxurl' => admin_url( 'admin-ajax.php' )
			) );

			$page_id = wc_get_page_id( 'account_addresses' );
			$url = get_permalink( $page_id );
			$url = add_query_arg( 'height', '400', add_query_arg( 'width', '400', add_query_arg( 'addressbook', '1', $url ) ) );
		?>
			<script type="text/javascript">
				var address = null;
				var wc_ship_url = '<?php echo $url; ?>';
			</script>
		<?php
		}

		wp_enqueue_script( 'jquery-tiptip', plugins_url( 'assets/js/jquery.tiptip.min.js', WC_Ship_Multiple::FILE ), array( 'jquery', 'jquery-ui-core' ) );

		wp_enqueue_script( 'modernizr', plugins_url( 'assets/js/modernizr.min.js', WC_Ship_Multiple::FILE ) );
		wp_enqueue_script( 'multiple_shipping_checkout', plugins_url( 'assets/js/woocommerce-checkout.min.js', WC_Ship_Multiple::FILE ), array( 'woocommerce', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-mouse' ) );

		wp_localize_script(
			'multiple_shipping_checkout',
			'WCMS',
			apply_filters(
				'wc_ms_multiple_shipping_checkout_locale',
				array(
					// URL to wp-admin/admin-ajax.php to process the request.
					'ajaxurl'           => admin_url( 'admin-ajax.php' ),
					'base_url'          => plugins_url( '', WC_Ship_Multiple::FILE ),
					'wc_url'            => WC()->plugin_url(),
					'countries'         => wp_json_encode( array_merge( WC()->countries->get_allowed_country_states(), WC()->countries->get_shipping_country_states() ) ),
					'select_state_text' => esc_attr__( 'Select an option&hellip;', 'wc_shipping_multiple_address' ),
				)
			)
		);

		if ( ! is_checkout() ) {
			wp_register_script( 'wcms-country-select', plugins_url( 'assets/js/country-select.min.js', WC_Ship_Multiple::FILE ), array( 'jquery', 'selectWoo', 'select2' ), WC_SHIPPING_MULTIPLE_ADDRESSES_VERSION, true );
			wp_localize_script(
				'wcms-country-select',
				'wcms_country_select_params',
				apply_filters(
					'wc_country_select_params',
					array(
						'countries'              => wp_json_encode( array_merge( WC()->countries->get_allowed_country_states(), WC()->countries->get_shipping_country_states() ) ),
						'i18n_select_state_text' => esc_attr__( 'Select an option&hellip;', 'wc_shipping_multiple_address' ),
					)
				)
			);
			wp_enqueue_script( 'wc-address-i18n' );
			wp_enqueue_script( 'wcms-country-select' );
			wp_enqueue_style( 'select2', WC()->plugin_url() . '/assets/css/select2.css', array(), WC_VERSION );
		}

		wp_enqueue_style( 'multiple_shipping_styles', plugins_url( 'assets/css/front.css', WC_Ship_Multiple::FILE ) );
		wp_enqueue_style( 'tiptip', plugins_url( 'assets/css/jquery.tiptip.css', WC_Ship_Multiple::FILE ) );

		global $wp_scripts;
		$ui_version = $wp_scripts->registered['jquery-ui-core']->ver;
		wp_enqueue_style( 'jquery-ui-css', "//ajax.googleapis.com/ajax/libs/jqueryui/{$ui_version}/themes/ui-lightness/jquery-ui.min.css" );

		// address validation support
		if ( class_exists( 'WC_Address_Validation' ) && is_page( wc_get_page_id( 'multiple_addresses' ) ) ) {
			$this->enqueue_address_validation_scripts();
		}

		// on the thank you page, remove the Shipping Address block if the order ships to multiple addresses
		if ( isset( $_GET['order-received'] ) || isset( $_GET['view-order'] ) ) {
			$order_id = isset( $_GET['order-received'] ) ? intval( $_GET['order-received'] ) : intval( $_GET['view-order'] );
			$order    = wc_get_order( $order_id );

			if ( ! $order ) {
				return;
			}

			$packages  = $order->get_meta( '_wcms_packages' );
			$multiship = $order->get_meta( '_multiple_shipping' );

			if ( ( $packages && count( $packages ) > 1 ) || $multiship == 'yes' ) {
				wp_enqueue_script( 'wcms_shipping_address_override', plugins_url( 'assets/js/address-override.min.js', WC_Ship_Multiple::FILE ), array( 'jquery' ) );
			}
		}
	}

	/**
	 * Address Validation scripts
	 */
	public function enqueue_address_validation_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( function_exists( 'wc_address_validation' ) ) {
			$validator  = wc_address_validation();
			$handler    = $validator->get_handler_instance();
		} else {
			$validator  = $GLOBALS['wc_address_validation'];
			$handler    = $validator->handler;
		}

		$params = array(
			'nonce'                 => wp_create_nonce( 'wc_address_validation' ),
			'debug_mode'            => 'yes' == get_option( 'wc_address_validation_debug_mode' ),
			'force_postcode_lookup' => 'yes' == get_option( 'wc_address_validation_force_postcode_lookup' ),
			'ajax_url'              => admin_url( 'admin-ajax.php', 'relative' ),
		);

		// load postcode lookup JS
		$provider = $handler->get_active_provider();

		if ( $provider && $provider->supports( 'postcode_lookup' ) ) {
			wp_enqueue_script( 'wc_address_validation_postcode_lookup', $validator->get_plugin_url() . '/assets/js/frontend/wc-address-validation-postcode-lookup' . $suffix . '.js', array( 'jquery', 'woocommerce' ), WC_Address_Validation::VERSION, true );
			wp_localize_script( 'wc_address_validation_postcode_lookup', 'wc_address_validation_postcode_lookup', $params );
		}

		// load address validation JS
		if ( $provider && $provider->supports( 'address_validation' ) && 'WC_Address_Validation_Provider_SmartyStreets' == get_class( $provider ) ) {

			// load SmartyStreets LiveAddress jQuery plugin
			wp_enqueue_script( 'wc_address_validation_smarty_streets', '//d79i1fxsrar4t.cloudfront.net/jquery.liveaddress/2.4/jquery.liveaddress.min.js', array( 'jquery' ), '2.4', true );

			wp_enqueue_script( 'wcms_address_validation', plugins_url( 'assets/js/address-validation.min.js', WC_Ship_Multiple::FILE ), array( 'jquery' ) );

			$params['smarty_streets_key'] = $provider->html_key;

			wp_localize_script( 'wcms_address_validation', 'wc_address_validation', $params );

			// add a bit of CSS to fix address correction popup from expanding to page width because of Chosen selects
			echo '<style type="text/css">.chzn-done{position:absolute!important;visibility:hidden!important;display:block!important;width:120px!important;</style>';
		}

		// allow other providers to load JS
		do_action( 'wc_address_validation_load_js', $provider, $handler, $suffix );
	}

	/**
	 * Display a note if the order ships to multiple addresses
	 *
	 * @param int $order_id
	 */
	public function show_multiple_addresses_notice($order_id) {
		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return;
		}

		$packages = $order->get_meta( '_wcms_packages' );

		if ( empty( $packages ) || count( $packages ) <= 1 ) {
			return;
		}

		$page_id    = wc_get_page_id( 'multiple_addresses' );
		$url        = add_query_arg( 'order_id', $order_id, get_permalink( $page_id ) );
	?>
		<div class="woocommerce_message woocommerce-message">
			<?php printf( __( 'This order ships to multiple addresses.  <a class="button" href="%s">View Addresses</a>', 'wc_shipping_multiple_address' ), $url ); ?>
		</div>
	<?php
	}

	/**
	 * Prints the email table of items and their shipping addresses
	 *
	 * @param int|WC_Order $order_id
	 */
	public function email_order_item_addresses( $order_id ) {
		do_action( 'wcms_order_shipping_packages_table', $order_id, true );
	}

	/**
	 * Prints the table of items and their shipping addresses
	 *
	 * @param int|WC_Order $order_id
	 */
	public function list_order_item_addresses( $order_id ) {
		do_action( 'wcms_order_shipping_packages_table', $order_id, false );
	}

	/**
	 * Show the current user's addresses in the my-account page
	 */
	public function my_account() {
		$user = wp_get_current_user();

		if ($user->ID == 0) {
			return;
		}

		$page_id    = wc_get_page_id( 'account_addresses' );
		$form_link  = get_permalink( $page_id );
		$otherAddr  = $this->wcms->address_book->get_user_addresses( $user );

		wc_get_template(
			'my-account-addresses.php',
			array(
				'user'          => $user,
				'addresses'     => $otherAddr,
				'form_url'      => $form_link
			),
			'multi-shipping',
			dirname( WC_Ship_Multiple::FILE ) .'/templates/'
		);
	}

	public function account_address_labels( $labels, $customer_id ) {
		$user = get_user_by( 'id', $customer_id );
		$addresses = $this->wcms->address_book->get_user_addresses( $user, false );

		$address_id = 0;

		foreach ( $addresses as $index => $address ) {
			$address_id++;

			$labels[ 'wcms_address_' . $index ] = sprintf( __( 'Shipping address %d', 'wc_shipping_multiple_address' ), $address_id );
		}

		return $labels;
	}

	public function account_address_formatted( $address, $customer_id, $address_id ) {
		if ( strpos( $address_id, 'wcms_address_' ) === 0 ) {
			$user = get_user_by( 'id', $customer_id );
			$addresses = $this->wcms->address_book->get_user_addresses( $user, false );

			$parts = explode( '_', $address_id );
			$index = $parts[2];

			if ( isset( $addresses[ $index ] ) ) {
				$account_address = $addresses[ $index ];

				foreach ( $account_address as $key => $value ) {
					$key = str_replace( 'shipping_', '', $key );
					$account_address[ $key ] = $value;
				}

				$address = $account_address;
			}
		}

		return $address;
	}

	public function edit_address_field_value( $value, $key, $load_address ) {
		if ( strpos( $load_address, 'wcms_address_' ) === 0 ) {
			$parts = explode( '_', $load_address );
			$index = $parts[2];

			if ( 'new' === $index ) {
				return empty( $_POST[ $key ] ) ? '' : wc_clean( $_POST[ $key ] );
			}

			$user = wp_get_current_user();
			$addresses = $this->wcms->address_book->get_user_addresses( $user, false );

			if ( ! isset( $addresses[ $index ] ) ) {
				return $value;
			}

			$key = str_replace( $load_address, 'shipping', $key );
			$value = $addresses[ $index ][ $key ];
		}

		return $value;
	}

	/**
	 * Save and and update a billing or shipping address if the
	 * form was submitted through the user account page.
	 * Copied from WC_Form_Handler::save_address and modified to save to address book
	 */
	public function save_address() {
		global $wp;

		if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) ) {
			return;
		}

		if ( version_compare( WC_VERSION, '3.4', '<' ) ) {
			if ( empty( $_POST['action'] ) || 'edit_address' !== $_POST['action'] || empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'woocommerce-edit_address' ) ) {
				return;
			}
		} else {
			if ( empty( $_POST['action'] ) || 'edit_address' !== $_POST['action'] ) {
				return;
			}
		}

		$user_id = get_current_user_id();

		if ( $user_id <= 0 ) {
			return;
		}

		$load_address = isset( $wp->query_vars['edit-address'] ) ? wc_edit_address_i18n( sanitize_title( $wp->query_vars['edit-address'] ), true ) : 'billing';

		// Only save our own addresses
		if ( strpos( $load_address, 'wcms_address_' ) !== 0 ) {
			return;
		}

		$address = WC()->countries->get_address_fields( esc_attr( $_POST[ 'shipping_country' ] ), 'shipping_' );

		foreach ( $address as $key => $field ) {

			if ( ! isset( $field['type'] ) ) {
				$field['type'] = 'text';
			}

			// Get Value.
			switch ( $field['type'] ) {
				case 'checkbox' :
					$_POST[ $key ] = (int) isset( $_POST[ $key ] );
					break;
				default :
					$_POST[ $key ] = isset( $_POST[ $key ] ) ? wc_clean( $_POST[ $key ] ) : '';
					break;
			}

			// Hook to allow modification of value.
			$_POST[ $key ] = apply_filters( 'woocommerce_process_myaccount_field_' . $key, $_POST[ $key ] );

			// Validation: Required fields.
			if ( ! empty( $field['required'] ) && empty( $_POST[ $key ] ) ) {
				wc_add_notice( sprintf( __( '%s is a required field.', 'wc_shipping_multiple_address' ), $field['label'] ), 'error' );
			}

			if ( ! empty( $_POST[ $key ] ) ) {

				// Validation rules
				if ( ! empty( $field['validate'] ) && is_array( $field['validate'] ) ) {
					foreach ( $field['validate'] as $rule ) {
						switch ( $rule ) {
							case 'postcode' :
								$_POST[ $key ] = trim( $_POST[ $key ] );

								if ( ! isset( $_POST[ $load_address . '_country' ] ) ) {
									continue 2;
								}

								if ( ! WC_Validation::is_postcode( $_POST[ $key ], $_POST[ $load_address . '_country' ] ) ) {
									wc_add_notice( __( 'Please enter a valid postcode / ZIP.', 'wc_shipping_multiple_address' ), 'error' );
								} else {
									$_POST[ $key ] = wc_format_postcode( $_POST[ $key ], $_POST[ $load_address . '_country' ] );
								}
								break;
							case 'phone' :
								$_POST[ $key ] = wc_format_phone_number( $_POST[ $key ] );

								if ( ! WC_Validation::is_phone( $_POST[ $key ] ) ) {
									wc_add_notice( sprintf( __( '%s is not a valid phone number.', 'wc_shipping_multiple_address' ), '<strong>' . $field['label'] . '</strong>' ), 'error' );
								}
								break;
							case 'email' :
								$_POST[ $key ] = strtolower( $_POST[ $key ] );

								if ( ! is_email( $_POST[ $key ] ) ) {
									wc_add_notice( sprintf( __( '%s is not a valid email address.', 'wc_shipping_multiple_address' ), '<strong>' . $field['label'] . '</strong>' ), 'error' );
								}
								break;
						}
					}
				}
			}
		}

		do_action( 'woocommerce_after_save_address_validation', $user_id, $load_address, $address );

		if ( 0 === wc_notice_count( 'error' ) ) {

			$user        = new WP_User( $user_id );
			$addresses   = $this->wcms->address_book->get_user_addresses( $user, false );
			$parts       = explode( '_', $load_address );
			$index       = $parts[2];
			$new_address = array();

			foreach ( $address as $key => $field ) {
				$new_address[ $key ] = $_POST[ $key ];
			}

			if ( 'new' === $index ) {
				$addresses[] = $new_address;
				end( $addresses );
				$index = key( $addresses );
				wc_add_notice( __( 'Address added successfully.', 'wc_shipping_multiple_address' ) );
			} else {
				$addresses[ $index ] = $new_address;
				wc_add_notice( __( 'Address changed successfully.', 'wc_shipping_multiple_address' ) );
			}

			$default_address = $this->wcms->address_book->get_user_default_address( $user->ID );

			if ( $default_address['address_1'] && $default_address['postcode'] ) {
				array_unshift( $addresses, $default_address );
			}

			$this->wcms->address_book->save_user_addresses( $user_id, $addresses );

			do_action( 'woocommerce_customer_save_address', $user_id, $load_address );

			wp_safe_redirect( wc_get_endpoint_url( 'edit-address', '', wc_get_page_permalink( 'myaccount' ) ) );
			exit;
		}

		// Prevent WC_Form_Handler::save_address
		unset( $_POST['action'] );
	}

	/**
	 * Generate inline scripts for wp_footer
	 */
	public function inline_scripts() {
		$order_id = isset( $_GET['order'] ) ? $_GET['order'] : false;
		$order    = wc_get_order( $order_id );

		if ( $order ) {
			if ( method_exists( $order, 'get_checkout_order_received_url' ) ) {
				$page_id = $order->get_checkout_order_received_url();
			} else {
				$page_id = wc_get_page_id( get_option( 'woocommerce_thanks_page_id', 'thanks' ) );
			}

			$shipping_addresses = $order->get_meta( '_shipping_addresses', false );

			if ( is_page( $page_id ) && ! empty( $shipping_addresses ) ) {
				$html       = '<div>';
				$packages   = $order->get_meta( '_wcms_packages' );

				foreach ( $packages as $package ) {
					$html .= '<address>' . wcms_get_formatted_address( $package['destination'] ) . '</address><br /><hr/>';
				}
				$html .= '</div>';
				$html = str_replace( '"', '\"', $html );
				$html = str_replace( "\n", " ", $html );
			?>
				<script type="text/javascript">
					jQuery(document).ready(function() {
						jQuery(jQuery("address")[1]).replaceWith("<?php echo $html; ?>");
					});
				</script>
			<?php
			}
		}
	}

	/**
	 * Add a delete address button on the edit address page
	 */
	public function delete_address_button() {
		$address = get_query_var( 'edit-address' );
		$edit_address = wc_get_endpoint_url( 'edit-address' );

		// Only show on multiple addresses
		if ( 0 !== strpos( $address, 'wcms_address_' ) || empty( $edit_address ) ) {
			return;
		}

		$remove_link = wp_nonce_url( add_query_arg( 'remove_address', $address, $edit_address ), 'wcms-delete-address' );
		printf( '<a href="%1$s" class="remove delete-address-button" aria-label="%2$s">&times;</a>', $remove_link, __( 'Delete address', 'wc_shipping_multiple_address' ) );
	}

	/**
	 * Handle the delete address action
	 */
	public function delete_address_action() {
		if ( ! empty( $_GET['remove_address'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'wcms-delete-address' ) ) {

			$user = wp_get_current_user();
			if ( $user->ID ) {
				$address   = wc_clean( $_GET['remove_address'] );
				$index     = ( 0 === strpos( $address, 'wcms_address_' ) ) ? substr( $address, 13 ) : '';
				$addresses = $this->wcms->address_book->get_user_addresses( $user );

				if ( isset( $addresses[ $index ] ) ) {
					unset( $addresses[ $index ] );
					$this->wcms->address_book->save_user_addresses( $user->ID, $addresses );
					wc_add_notice( __( 'Deleted address', 'wc_shipping_multiple_address' ) );
				} else {
					wc_add_notice( __( 'Address could not be found', 'wc_shipping_multiple_address' ), 'error' );
				}

				// Redirect to edit address page
				wp_safe_redirect( wc_get_account_endpoint_url( 'edit-address' ) );
				exit;
			}
		}
	}

	/**
	 * Add address button on my account page
	 */
	public function add_address_button() {
		$address = get_query_var( 'edit-address' );

		if ( empty( $address ) ) {
			$url = wc_get_endpoint_url( 'edit-address', 'wcms_address_new' );
			printf( '<a href="%s" class="button">%s</a>', esc_url( $url ), __( 'Add address', 'wc_shipping_multiple_address' ) );
		}
	}

	/**
	 * Init address fields
	 */
	public function init_address_fields() {
		$address = get_query_var( 'edit-address' );

		if ( 0 === strpos( $address, 'wcms_address_' ) ) {
			add_filter( 'woocommerce_' . $address . '_fields', array( $this, 'country_address_fields' ), 10, 2 );

			// Override checkout value for states field, see following issue for better way to resolve this:
			// https://github.com/woocommerce/woocommerce/issues/15632
			add_filter( 'woocommerce_checkout_get_value', array( $this, 'country_address_value' ), 10, 2 );
		}
	}

	/**
	 * Override address fields with country specific ones
	 */
	public function country_address_fields( $address_fields, $country ) {
		$address_country = $this->get_address_country();
		if ( false !== $address_country ) {
			$country = $address_country;
		}

		return WC()->countries->get_address_fields( $country, 'shipping_' );
	}


	/**
	 * Override address country field (to show correct list of states)
	 */
	public function country_address_value( $value, $input ) {
		if ( 'shipping_country' === $input ) {
			$country = $this->get_address_country();
			if ( false !== $country ) {
				return $country;
			}
		}
		return $value;
	}


	/**
	 * Helper function to get address country
	 * Saves it in the class to prevent multiple lookups
	 */
	public function get_address_country() {
		if ( ! empty( $this->country ) ) {
			return $this->country;
		}

		$user = wp_get_current_user();
		$addresses = $this->wcms->address_book->get_user_addresses( $user, false );

		$address = get_query_var( 'edit-address' );
		$parts = explode( '_', $address );
		$index = $parts[2];

		if ( isset( $addresses[ $index ] ) && ! empty( $addresses[ $index ]['shipping_country'] ) ) {
			$this->country = $addresses[ $index ]['shipping_country'];
			return $this->country;
		}

		return false;
	}

}
