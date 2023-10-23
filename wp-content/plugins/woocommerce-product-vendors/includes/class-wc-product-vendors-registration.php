<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Vendor Registration.
 *
 * Handles the vendor form registration process .
 *
 * @category Registration
 * @package  WooCommerce Product Vendors/Registration
 * @version  2.0.0
 */
class WC_Product_Vendors_Registration {
	/**
	 * Init
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return bool
	 */
	public function __construct() {
		if ( is_admin() ) {
			add_action( 'wp_ajax_wc_product_vendors_registration', array( $this, 'registration_ajax' ) );
			add_action( 'wp_ajax_nopriv_wc_product_vendors_registration', array( $this, 'registration_ajax' ) );

			// Create user if vendor creating from admin dashboard
			if ( isset( $_POST['action'] ) && $_POST['action'] === 'add-tag'  ) {
				add_action( 'created_' . WC_PRODUCT_VENDORS_TAXONOMY, array( $this, 'create_user_on_vendor_term_creation' ), 100 );
			}
		}

		return true;
	}

	/**
	 * Add scripts
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return bool
	 */
	public function add_scripts() {
		// Get the current global post object.
		$post = get_post();

		if ( ! is_admin() && ( ! $post || ! has_shortcode( $post->post_content, 'wcpv_registration' ) ) ) {
			// Do nothing if not admin and there is no global post or the shortcode is not present.
			return;
		}

		wp_enqueue_script( 'wcpv-frontend-scripts' );

		$localized_vars = array(
			'ajaxurl'               => admin_url( 'admin-ajax.php' ),
			'ajaxRegistrationNonce' => wp_create_nonce( '_wc_product_vendors_registration_nonce' ),
			'success'               => __( 'Your request has been submitted.  You will be contacted shortly.', 'woocommerce-product-vendors' ),
		);

		wp_localize_script( 'wcpv-frontend-scripts', 'wcpv_registration_local', $localized_vars );

		return true;
	}

	/**
	 * Handles the registration via AJAX
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return bool
	 */
	public function registration_ajax() {
		if ( ! isset( $_POST['form_items'] ) ) {
			return false;
		}
		if ( ! is_array( $_POST['form_items'] ) ) {
			parse_str( $_POST['form_items'], $form_items ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- POST data will be sanitized and unslashed below.
		} else {
			$form_items = $_POST['form_items']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- POST data will be sanitized and unslashed below.
		}

		$form_items = array_map(
			function( $item ) {
				return sanitize_text_field( wp_unslash( $item ) );
			},
			$form_items // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- POST data is both sanitized and unslashed above.
		);
		$this->registration_form_validation( $form_items );

		return true;
	}

	/**
	 * Includes the registration form
	 *
	 * Also allows for the form template to be overwritten.
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.1.5
	 */
	public function include_form() {
		wc_get_template( 'shortcode-registration-form.php', array(), 'woocommerce-product-vendors', WC_PRODUCT_VENDORS_TEMPLATES_PATH );
	}

	/**
	 * Validates the registration form
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param array $form_items forms items to validate
	 * @return bool
	 */
	public function registration_form_validation( $form_items = array() ) {
		global $errors;

		if ( ! isset( $form_items ) ) {
			wp_die( esc_html__( 'Cheatin&#8217; huh?', 'woocommerce-product-vendors' ) );
		}

		if ( ! isset( $_POST['ajaxRegistrationNonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ajaxRegistrationNonce'] ) ), '_wc_product_vendors_registration_nonce' ) ) {
			wp_die( esc_html__( 'Cheatin&#8217; huh?', 'woocommerce-product-vendors' ) );
		}

		// handle form submission/validation
		if ( ! empty( $form_items ) ) {
			$errors = array();

			$is_username_exist = ! empty( $form_items['username'] ) && username_exists( $form_items['username'] );
			$is_email_exist    = ! empty( $form_items['email'] ) && false !== email_exists( $form_items['email'] );

			if ( ! is_user_logged_in() ) {
				if ( empty( $form_items['firstname'] ) ) {
					$errors['firstname'] = __( 'First Name is a required field.', 'woocommerce-product-vendors' );
				}

				if ( empty( $form_items['lastname'] ) ) {
					$errors['lastname'] = __( 'Last Name is a required field.', 'woocommerce-product-vendors' );
				}

				if ( empty( $form_items['username'] ) ) {
					$errors['username'] = __( 'Username is a required field.', 'woocommerce-product-vendors' );
				}

				if ( ! validate_username( $form_items['username'] ) ) {
					$errors['username'] = __( 'Please enter a valid username.', 'woocommerce-product-vendors' );
				}

				if ( $is_username_exist ) {
					$errors['username'] = __( 'Please choose a different username.', 'woocommerce-product-vendors' );
				}

				if ( empty( $form_items['email'] ) ) {
					$errors['email'] = __( 'Email is a required field.', 'woocommerce-product-vendors' );
				}

				if ( empty( $form_items['confirm_email'] ) ) {
					$errors['confirm_email'] = __( 'Confirm email is a required field.', 'woocommerce-product-vendors' );
				}

				if ( $form_items['confirm_email'] !== $form_items['email'] ) {
					$errors['confirm_email'] = __( 'Emails must match.', 'woocommerce-product-vendors' );
				}

				if ( $is_email_exist ) {
					$errors['email'] = __( 'Email already exists in our system.', 'woocommerce-product-vendors' );
				}

				if ( ! filter_var( $form_items['email'], FILTER_VALIDATE_EMAIL ) ) {
					$errors['email'] = __( 'Email is not valid.', 'woocommerce-product-vendors' );
				}

				if ( $is_username_exist || $is_email_exist ) {
					$errors['wp_user_exists'] = sprintf(
						__(
							'If you are already an existing user on the site, please %1$slog in%2$s before registering as a Vendor.',
							'woocommerce-product-vendors'
						),
						'<a href="' . esc_url( wp_login_url( wp_get_referer() ) ) . '">',
						'</a>'
					);
				}
			}

			if ( empty( $form_items['vendor_name'] ) ) {
				$errors['vendor_name'] = __( 'Vendor Name is a required field.', 'woocommerce-product-vendors' );
			}

			// check that the vendor name is not already taken
			// checks against existing terms from "wcpv_product_vendors" taxonomy
			if ( ! empty( $form_items['vendor_name'] ) && term_exists( $form_items['vendor_name'], WC_PRODUCT_VENDORS_TAXONOMY ) ) {
				$errors['vendor_name'] = __( 'Sorry that vendor name already exists. Please enter a different one.', 'woocommerce-product-vendors' );
			}

			if ( empty( $form_items['vendor_description'] ) ) {
				$errors['vendor_description'] = __( 'Vendor Description is a required field.', 'woocommerce-product-vendors' );
			}

			do_action( 'wcpv_shortcode_registration_form_validation', $errors, $form_items );

			$errors = apply_filters( 'wcpv_shortcode_registration_form_validation_errors', $errors, $form_items );

			// no errors, lets process the form
			if ( empty( $errors ) ) {
				if ( is_user_logged_in() ) {
					$this->vendor_registration_form_process( $form_items );
				} else {
					$this->vendor_user_registration_form_process( $form_items );
				}
			} else {
				wp_send_json( array( 'errors' => $errors ) );
			}
		}
	}

	/**
	 * Process the registration for a vendor.
	 *
	 * @since 2.0.41
	 * @version 2.0.41
	 * @param array   $form_items Sanitized form items
	 * @param WP_User $user       WP User
	 * @param array   $args
	 * @return bool
	 */
	protected function register_vendor( $form_items, $user, $args = array() ) {
		$term_args = apply_filters( 'wcpv_registration_term_args', $args, $form_items );

		// add vendor name to taxonomy
		$term = wp_insert_term( $form_items['vendor_name'], WC_PRODUCT_VENDORS_TAXONOMY, $term_args );

		// no errors, term added, continue
		if ( ! is_wp_error( $term ) && ! empty( $user ) ) {
			// add user to term meta
			$vendor_data = array();

			$vendor_data['admins']               = $user->ID;
			$vendor_data['per_product_shipping'] = 'yes';
			$vendor_data['commission_type']      = 'percentage';
			$vendor_data['description']          = $form_items['vendor_description'];
			$vendor_data['email']                = $user->user_email;

			// If the description should be shown publicly, then copy it to the vendor profile.
			// Otherwise it will only be shown to the store admin.
			if ( ! empty( $form_items['vendor_description_public'] ) ) {
				$vendor_data['profile'] = $form_items['vendor_description'];
			}

			WC_Product_Vendors_Utils::set_vendor_data(
				$term['term_id'],
				apply_filters( 'wcpv_registration_default_vendor_data', $vendor_data )
			);

			// change this user's role to pending vendor
			wp_update_user( apply_filters( 'wcpv_registration_default_user_data', array(
				'ID'   => $user->ID,
				'role' => 'wc_product_vendors_pending_vendor',
			) ) );

			// Add new pending vendor to list.
			WC_Product_Vendors_Utils::set_new_pending_vendor( $user->ID );

			$default_args = array(
				'user_id'     => $user->ID,
				'user_email'  => $user->user_email,
				'first_name'  => $user->user_firstname,
				'last_name'   => $user->user_lastname,
				'user_login'  => __( 'Same as your account login', 'woocommerce-product-vendors' ),
				'user_pass'   => __( 'Same as your account password', 'woocommerce-product-vendors' ),
				'vendor_name' => $form_items['vendor_name'],
				'vendor_desc' => $form_items['vendor_description'],
			);

			$args = apply_filters( 'wcpv_registration_args', wp_parse_args( $args, $default_args ), $args, $default_args );

			do_action( 'wcpv_shortcode_registration_form_process', $args, $form_items );

			echo 'success';
			exit;
		} else {
			global $errors;

			if ( is_wp_error( $user ) ) {
				$errors[] = $user->get_error_message();
			}

			if ( is_wp_error( $term ) ) {
				$errors[] = $term->get_error_message();
			}

			wp_send_json( array( 'errors' => $errors ) );
		}

		return true;
	}

	/**
	 * Process the registration form for just vendor.
	 * As in they already have a user account on the site.
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.41
	 * @param array $form_items sanitized form items
	 * @return bool
	 */
	public function vendor_registration_form_process( $form_items ) {
		return $this->register_vendor( $form_items, wp_get_current_user() );
	}

	/**
	 * Process the registration form for vendor and user
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.41
	 * @param array $form_items sanitized form items
	 * @return bool
	 */
	public function vendor_user_registration_form_process( $form_items ) {
		$username    = $form_items['username'];
		$email       = $form_items['email'];
		$firstname   = $form_items['firstname'];
		$lastname    = $form_items['lastname'];

		$password = wp_generate_password();

		$args = apply_filters( 'wcpv_shortcode_register_vendor_args', array(
			'user_login'      => $username,
			'user_email'      => $email,
			'user_pass'       => $password,
			'first_name'      => $firstname,
			'last_name'       => $lastname,
			'display_name'    => $firstname,
			'role'            => 'wc_product_vendors_pending_vendor',
		) );

		$user_id            = wp_insert_user( $args );
		$user               = get_user_by( 'id', $user_id );
		$password_reset_key = get_password_reset_key( $user );

		$args['password_reset_key'] = $password_reset_key;

		return $this->register_vendor( $form_items, $user, $args );
	}

	/**
	 * Create user on vendor creation through admin dashboard
	 *
	 * @access public
	 * @param int $term_id newly created term id
	 * @return void
	 */
	public function create_user_on_vendor_term_creation( $term_id ) {
		// Get term and vendor data
		$term         = get_term( $term_id );
		$vendor_data  = WC_Product_Vendors_Utils::get_vendor_data_by_id( $term_id );
		$vendor_data  = ! is_array( $vendor_data ) ? array() : $vendor_data;
		$vendor_email = empty( $vendor_data['email'] ) ? null : $vendor_data['email'];

		// No need to create user if the email is already associated with any account
		if ( ! $vendor_email || email_exists( $vendor_email ) ) {
			return;
		}

		$args = apply_filters( 'wcpv_admin_register_vendor_args', array(
			'user_login'    =>  $term->slug,
			'user_email'    =>  $vendor_email,
			'user_pass'     =>  wp_generate_password(),
			'first_name'    =>  $term->name,
			'display_name'  =>  $term->name,
			'role'          =>  'wc_product_vendors_admin_vendor',
		) );

		// Create
		$user_id = wp_insert_user( $args );

		// Make the user admin of the vendor
		if ( $user_id ) {
			$admins                = ( empty( $vendor_data['admins'] ) || ! is_array( $vendor_data['admins'] ) ) ? array() : $vendor_data['admins'];
			$admins[]              = $user_id;
			$vendor_data['admins'] = array_unique( $admins );

			WC_Product_Vendors_Utils::set_vendor_data( $term_id, $vendor_data );
		}
	}
}
