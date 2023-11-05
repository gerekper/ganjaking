<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://woocommerce.com/
 * @since      1.0.0
 *
 * @package    coupon-referral-program
 * @subpackage coupon-referral-program/public
 */
use Automattic\WooCommerce\Utilities\OrderUtil;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    coupon-referral-program
 * @subpackage coupon-referral-program/public
 */
class Coupon_Referral_Program_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}
	/**
	 * Register the shortcodes.
	 *
	 * @since    1.0.0
	 */
	public function woocommerce_register_shortcode() {
		if ( $this->check_shortcode_is_enable() ) {

			add_shortcode( 'crp_popup_button', array( $this, 'mwb_crp_referral_button_shortcode' ) );
		}
		add_shortcode( 'crp_referral_link', array( $this, 'mwb_crp_referral_link_shortcode' ) );
		add_shortcode( 'crp_referral_code', array( $this, 'mwb_crp_referral_code_shortcode' ) );
		add_shortcode( 'crp_referral_dashboard', array( $this, 'mwb_crp_referral_dashboard_shortcode' ) );
		// ===========Add Rewrite Rule============
		// phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules
		add_rewrite_endpoint( 'referral_coupons', EP_PAGES );
	}

	/**
	 * Register the endpoints on my_account page.
	 *
	 * @param array $vars .
	 * @since    1.6.0
	 */
	public function mwb_crp_custom_endpoint_query_vars( $vars ) {
		$vars[] = 'referral_coupons';
		return $vars;
	}

	/**
	 * My account end point compatibility with WPML.
	 *
	 * @param mixed $query_vars .
	 * @param mixed $wc_vars .
	 * @param mixed $obj .
	 * @since    1.6.0
	 */
	public function mwb_crp_wpml_register_endpoint( $query_vars, $wc_vars, $obj ) {

		$query_vars['referral_coupons'] = $obj->get_endpoint_translation( 'referral_coupons', isset( $wc_vars['referral_coupons'] ) ? $wc_vars['referral_coupons'] : 'referral_coupons' );
		return $query_vars;
	}

	/**
	 * My account end point compatibility with WPML.
	 *
	 * @param mixed $endpoint .
	 * @param mixed $key .
	 * @since    1.6.0
	 */
	public function mwb_crp_endpoint_permalink_filter( $endpoint, $key ) {

		if ( 'referral_coupons' === $key ) {
			return 'referral_coupons';
		}
		return $endpoint;
	}
	/**
	 * Display referral link using shortcode.
	 *
	 * @since    1.0.0
	 */
	public function mwb_crp_referral_link_shortcode() {
		if ( $this->is_social_sharing_enabled() && is_user_logged_in() ) {
			$user_ID           = get_current_user_ID();
			$mwb_crp_link_html = '<fieldset><code>' . $this->get_referral_link( $user_ID ) . '</code></fieldset>';
			return $mwb_crp_link_html;
		}
	}
	/**
	 * Display the referral button for the shortcode.
	 *
	 * @since    1.0.0
	 */
	public function mwb_crp_referral_button_shortcode() {
		$user_ID = get_current_user_ID();
		$user    = new WP_User( $user_ID );
		?>
		<a id="mwb_crp_shortcode_btn"href="javascript:;" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored modal__trigger mwb-pr-drag-btn" data-modal="#mwb_modal" style="background-color: <?php echo wp_kses_post( Coupon_Referral_Program_Admin::get_selected_color() ); ?>"><?php echo wp_kses_post( Coupon_Referral_Program_Admin::get_visible_text() ); ?></a>
		<?php

	}
	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		global $post;
		wp_enqueue_style( $this->plugin_name, COUPON_REFERRAL_PROGRAM_DIR_URL . 'public/css/coupon-referral-program-public.css', array(), $this->version, 'all' );

		if ( $this->is_selected_page() || $this->check_shortcode_is_enable() ) {
			wp_enqueue_style( 'material_modal', COUPON_REFERRAL_PROGRAM_DIR_URL . 'modal/css/material-modal.css', array(), $this->version, 'all' );

			wp_enqueue_style( 'modal_style', COUPON_REFERRAL_PROGRAM_DIR_URL . 'modal/css/style.css', array(), $this->version, 'all' );
		} elseif ( is_account_page() || has_shortcode( $post->post_content, 'crp_referral_tab' ) ) {
			wp_enqueue_style( 'account_page', COUPON_REFERRAL_PROGRAM_DIR_URL . 'public/css/coupon-referral-program-public.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		global $post;
		if ( $this->is_selected_page() || $this->check_shortcode_is_enable() || is_account_page() || has_shortcode( $post->post_content, 'crp_referral_tab' ) ) {
			$mwb_crp_animation = get_option( 'mwb_crp_animation', false );

			$mwb_crp_arr = array(
				'mwb_crp_animation' => $mwb_crp_animation,
				'is_account_page'   => is_account_page(),
				'Showing_page'      => __( 'Showing page _PAGE_ of _PAGES_', 'coupon-referral-program' ),
				'no_record'         => __( 'No records available', 'coupon-referral-program' ),
				'nothing_found'     => __( 'Nothing found', 'coupon-referral-program' ),
				'display_record'    => __( 'Display _MENU_ Entries', 'coupon-referral-program' ),
				'filtered_info'     => __( '(filtered from _MAX_ total records)', 'coupon-referral-program' ),
				'search'            => __( 'Search', 'coupon-referral-program' ),
				'previous'          => __( 'Previous', 'coupon-referral-program' ),
				'next'              => __( 'Next', 'coupon-referral-program' ),
				'mwb_crp_nonce'     => wp_create_nonce( 'mwb-crp-verify-nonce' ),
				'ajaxurl'           => admin_url( 'admin-ajax.php' ),
				'apply_text'        => __( 'Apply', 'coupon-referral-program' ),
				'remove_text'       => __( 'Remove', 'coupon-referral-program' ),
				'apply'             => __( 'Apply', 'coupon-referral-program' ),
				'empty_email'       => __( 'Email Field is empty', 'coupon-referral-program' ),
				'invalid_email'     => __( 'Invalid Email', 'coupon-referral-program' ),
				'is_shortcode_post' => isset( $post->post_content ) ? has_shortcode( $post->post_content, 'crp_referral_tab' ) : false,
			);

			wp_enqueue_script( 'datatables', '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js', array( 'jquery' ), $this->version, true );
			wp_register_script( $this->plugin_name, COUPON_REFERRAL_PROGRAM_DIR_URL . 'public/js/coupon-referral-program-public.js', array( 'jquery', 'datatables', 'jquery-ui-draggable' ), $this->version, false );
			wp_localize_script( $this->plugin_name, 'mwb_crp', $mwb_crp_arr );
			wp_enqueue_script( $this->plugin_name );

			wp_enqueue_script( 'mwb_materal_modal_min', COUPON_REFERRAL_PROGRAM_DIR_URL . 'modal/js/material-modal.min.js', array( 'jquery' ), $this->version, true );

			wp_register_script( 'mwb_wpr_clipboard', COUPON_REFERRAL_PROGRAM_DIR_URL . 'public/js/dist/clipboard.min.js', array(), $this->version, true );

			wp_enqueue_script( 'mwb_wpr_clipboard' );
		}

	}
	/**
	 * Shortcode for the button.
	 *
	 * @since    1.0.0
	 */
	public function mwb_crp_referral_button() {
		$user_ID = get_current_user_ID();
		$user    = new WP_User( $user_ID );
		if ( $this->is_popup_button_enable() ) {
			?>
			<style type="text/css"><?php echo wp_kses_post( $this->get_custom_style_popup_btn() ); ?></style>
			<a href="javascript:;" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored modal__trigger mwb-pr-drag-btn <?php echo wp_kses_post( $this->get_position_popup_button() ); ?>" data-modal="#mwb_modal" id="mwb-cpr-drag" class="animated slideInDown" style="background-color: <?php echo wp_kses_post( Coupon_Referral_Program_Admin::get_selected_color() ); ?>"><?php echo wp_kses_post( Coupon_Referral_Program_Admin::get_visible_text() ); ?></a>
			<?php
		}
	}
	/**
	 * Including the html for render the button as well as the popup style
	 *
	 * @since 1.0.0
	 */
	public function mwb_crp_load_html() {
		$user_ID = get_current_user_ID();
		$user    = new WP_User( $user_ID );
		if ( $this->is_selected_page() || $this->check_shortcode_is_enable() ) {

			include_once COUPON_REFERRAL_PROGRAM_DIR_PATH . 'modal/referral-program-notify.php';
		}
		include_once COUPON_REFERRAL_PROGRAM_DIR_PATH . 'modal/apply-coupon-on-subscriptions.php';
	}
	/**
	 * Get custom style of the button.
	 *
	 * @since 1.0.0
	 */
	public static function get_custom_style_popup_btn() {
		$mwb_custom_style = get_option( 'referral_button_custom_css', false );
		return $mwb_custom_style;
	}

	/**
	 * Check is popup button is enable
	 *
	 * @since 1.0.0
	 */
	public function is_popup_button_enable() {
		$is_enable = false;

		$mwb_check_popup = get_option( 'mwb_cpr_button_enable', 'yes' );
		if ( ! empty( $mwb_check_popup ) && 'yes' === $mwb_check_popup ) {

			$is_enable = true;
		}

		return $is_enable;
	}
	/**
	 * Show button for the selected pages
	 *
	 * @since 1.0.0
	 */
	public function woocommerce_referral_button_show() {
		if ( $this->is_selected_page() ) {

			$this->mwb_crp_referral_button();

		}
	}
	/**
	 * Check which page is being selected
	 *
	 * @since 1.0.0
	 * @return boolean
	 */
	public function is_selected_page() {

		global $wp_query;

		$is_selected        = false;
		$mwb_selected_pages = array();
		$mwb_selected_pages = $this->mwb_get_selected_pages();

		if ( empty( $mwb_selected_pages ) && ! $this->check_shortcode_is_enable() ) {

			$is_selected = true;
		} elseif ( is_single() && ! $this->check_shortcode_is_enable() && ! empty( $mwb_selected_pages ) ) {

			$page_id = 'details';
			if ( in_array( $page_id, $mwb_selected_pages ) ) {

				$is_selected = true;
			}
		} elseif ( empty( $mwb_selected_pages ) && ! $this->check_shortcode_is_enable() ) {

			$is_selected = true;
		} elseif ( ! is_shop() && ! is_home() && ! empty( $mwb_selected_pages ) && ! $this->check_shortcode_is_enable() ) {

			$page    = $wp_query->get_queried_object();
			$page_id = isset( $page->ID ) ? $page->ID : '';

			if ( in_array( $page_id, $mwb_selected_pages ) ) {

				$is_selected = true;
			}
		} elseif ( is_shop() && ! $this->check_shortcode_is_enable() && ! empty( $mwb_selected_pages ) ) {
			$page_id = wc_get_page_id( 'shop' );

			if ( in_array( $page_id, $mwb_selected_pages ) ) {

				$is_selected = true;
			}
		} else {

			$is_selected = false;
		}

		return $is_selected;
	}

	/**
	 * Get a referral link.
	 *
	 * @since 1.0.0
	 * @param int $user_id for which the referral link needs to be generated .
	 * @return referral link
	 */
	public function get_referral_link( $user_id ) {
		$referral_link = '';
		if ( isset( $user_id ) && ! empty( $user_id ) ) {
			$referral_key = get_user_meta( $user_id, 'referral_key', true );
			if ( empty( $referral_key ) ) {
				$referral_key = $this->set_referral_key( $user_id );
			}
			/**
			 * Filter referral site url.
			 *
			 * @since 1.6.4
			 * @param string site url() .
			 */
			$referral_url  = apply_filters( 'mwb_crp_referral_link_url', site_url() );
			$referral_link = $referral_url . '?ref=' . $referral_key;
		}
		return $referral_link;
	}

	/**
	 * Get a referral link.
	 *
	 * @since 1.0.0
	 * @param int $user_id for which the referral link needs to be set.
	 * @return referral link
	 */
	public function set_referral_key( $user_id ) {
		$generated_key = generate_referral_key();
		update_user_meta( $user_id, 'referral_key', $generated_key );
		// Check if plugin is enabled.
		$enable = get_option( 'mwb_crp_referal_via_code', false );
		if ( isset( $enable ) && 'yes' === $enable ) {
			$generated_key = $this->mwb_crp_generate_referral_coupon_callback( $generated_key, $user_id );
		}
		return $generated_key;
	}

	/**
	 * Provides Discount coupon to each registered user if required is enabled
	 *
	 * @since 1.0.0
	 * @param int $user_id is the customer id who has registered himself successfully.
	 */
	public function woocommerce_created_customer_discount( $user_id ) {

		if ( $this->check_signup_is_enable() && ! $this->check_reffral_signup_is_enable() ) {
			if ( ! self::check_is_points_rewards_enable() ) {
				if ( $this->wps_referral_discount_management('user_signup', $user_id ) ) {
					$coupon_code = $this->mwb_create_coupon_send_email( $user_id );
					$this->save_singup_coupon_code( 'singup', $coupon_code, $user_id );
				}
			}
		}
		/* ============== Set the referre user id to current user data ============= */
		$enable_plugin = is_enable_coupon_referral_program();
		if ( $enable_plugin ) {
			$cookie_val = isset( $_COOKIE['mwb_cpr_cookie_set'] ) ? unserialize( base64_decode( sanitize_text_field( wp_unslash( $_COOKIE['mwb_cpr_cookie_set'] ) ) ) ) : '';
			if ( isset( $cookie_val['referral_key'] ) ) {

				$retrive_data = $cookie_val['referral_key'];
			}
			if ( isset( $retrive_data ) && ! empty( $retrive_data ) ) {
				$args['meta_query'] = array(
					array(
						'key'     => 'referral_key',
						'value'   => trim( $retrive_data ),
						'compare' => '==',
					),
				);
				$referral_user_data = get_users( $args );
				if ( ! empty( $referral_user_data ) ) {
					$refree_id      = $referral_user_data[0]->data->ID;
					$referral_user  = get_user_by( 'ID', $refree_id );
					$referral_email = $this->get_user_email( $referral_user );
					if ( isset( $refree_id ) && ! empty( $refree_id ) && ! empty( $user_id ) ) {
						/**
						 * Filter check WPS PAR is active.
						 *
						 * @since 1.6.4
						 * @param bool false flag .
						 */
						$check_par_enable = apply_filters( 'mwb_crp_par_check_enable', false );
						update_user_meta( $user_id, 'mwb_cpr_user_referred_by', $refree_id );
						if ( $this->check_signup_is_enable() && $this->check_reffral_signup_is_enable() ) {
							if ( ! self::check_is_points_rewards_enable() ) {
								if ( $this->wps_referral_discount_management( 'referral_user_signup', $user_id ) ) {
									$coupon_code = $this->mwb_create_coupon_send_email( $user_id );
									$this->save_singup_coupon_code( 'singup', $coupon_code, $user_id );
								}
							} elseif ( $check_par_enable ) {
								/**
								 * Add points for referral sigup for user.
								 *
								 * @since 1.6.4
								 * @param integer $user_id .
								 */
								do_action( 'mwb_crp_par_referral_signup_add_points', $user_id );
							} else {
								$points = $this->get_points_for_signup();
								WC_Points_Rewards_Manager::increase_points( $user_id, $points, 'reffral-account-signup' );
							}
						}
						// referal signup.
						if ( $this->check_reffre_signup_is_enable() ) {
							$total_allowed_referral = get_option( 'mwb_crp_total_number_referred_users', 0 );
							if ( empty( $total_allowed_referral ) || ( $this->mwb_crp_get_total_referred_users( $retrive_data ) <= $total_allowed_referral ) ) {
								if ( ! self::check_is_points_rewards_enable() ) {
									if ( $this->wps_crp_allow_referee_signup_discount( $retrive_data ) ) {
										if ( $this->wps_referral_discount_management( 'referee_user_signup', $refree_id ) ) {
											$coupon_code = $this->mwb_create_coupon_send_email_for_refree( $refree_id );
											$this->save_referal_singup_coupon_code( $coupon_code, $refree_id, $user_id );
										}
									}
								} elseif ( $check_par_enable ) {
									/**
									 * Add points for referee after user signup.
									 *
									 * @since 1.6.4
									 * @param integer $user_id .
									 * @param integer $refree_id .
									 */
									do_action( 'mwb_crp_par_referee_add_points_for_new_user_signup', $user_id, $refree_id );
								} else {
									$points = $this->get_points_for_refree_signup();
									WC_Points_Rewards_Manager::increase_points( $refree_id, $points, 'reffree-account-on-referal-signup' );
								}
							}
						}
					}
				}
			}
		}
		/*============== End of Set the referre user id to current user data =============*/
	}

	/**
	 * Create coupon and send mail for reffral signup for refree.
	 *
	 * @since 1.0.0
	 * @param int $user_id is the customer id who has registered himself successfully.
	 */
	public function mwb_create_coupon_send_email_for_refree( $user_id ) {
		$mwb_cpr_coupon_length = $this->mwb_get_coupon_length();
		$mwb_cpr_coupon_amount = get_option( 'refree_discount_value', 1 );
		$mwb_cpr_coupon_expiry = $this->mwb_get_coupon_expiry();
		$expirydate            = $this->mwb_expiry_date_saved( $mwb_cpr_coupon_expiry );
		$bloginfo              = get_bloginfo();

		/*Get the coupon configration of the coupon*/
		$mwb_cpr_discount_type  = $this->mwb_crp_get_discount_signup_type_refree();
		$coupon_amount_with_css = $this->mwb_formatted_amount( $mwb_cpr_coupon_amount, $mwb_cpr_discount_type );
		$user                   = get_user_by( 'ID', $user_id );
		$user_email             = $this->get_user_email( $user );
		$mwb_cpr_code           = $this->mwb_cpr_coupon_generator( $mwb_cpr_coupon_length );
		$coupon_description     = esc_html__( 'Coupon for Reffrerd user on signup', 'coupon-referral-program' );

		if ( $this->mwb_cpr_create_coupons( $mwb_cpr_code, $mwb_cpr_coupon_amount, $user_id, $mwb_cpr_discount_type, $expirydate, $coupon_description, $user_email ) ) {

			/* === Send the Email to the Registered User === */
			if ( empty( $expirydate ) ) {
				$expirydate = esc_html__( 'No Expiry', 'coupon-referral-program' );
			}
			$customer_email = WC()->mailer()->emails['crp_refree_email'];
			$email_status   = $customer_email->trigger( $user_id, $mwb_cpr_code, $coupon_amount_with_css, $expirydate );
		}
		return $mwb_cpr_code;
	}
	/**
	 * Save coupon code for the refree signup.
	 *
	 * @since 1.0.0
	 * @param string $coupon_code .
	 * @param int    $refree_id .
	 * @param int    $user_id is the customer id who has registered himself successfully.
	 */
	public function save_referal_singup_coupon_code( $coupon_code, $refree_id, $user_id ) {

		$mwb_crp_referral_signup_array = array();
		$mwb_crp_referral_signup       = get_user_meta( $refree_id, 'mwb_crp_referal_signup_coupon', true );
		$coupon                        = new WC_Coupon( $coupon_code );
		$coupon_code                   = $coupon->get_id();
		if ( ! empty( $mwb_crp_referral_signup ) ) {

			$mwb_crp_referral_signup[ $coupon_code ] = $user_id;

			update_user_meta( $refree_id, 'mwb_crp_referal_signup_coupon', $mwb_crp_referral_signup );
		} else {
			$mwb_crp_referral_signup_array[ $coupon_code ] = $user_id;
			update_user_meta( $refree_id, 'mwb_crp_referal_signup_coupon', $mwb_crp_referral_signup_array );
		}
	}
	/**
	 * Get signup coupons
	 *
	 * @name get_signup_coupon
	 * @since 1.0.0
	 * @param int $user_id .
	 * @return signup purchase array
	 */
	public function mwb_crp_get_referal_signup_coupon( $user_id ) {
		$mwb_crp_referal_signup_coupon = get_user_meta( $user_id, 'mwb_crp_referal_signup_coupon', true );
		if ( empty( $mwb_crp_referal_signup_coupon ) ) {
			$mwb_crp_referal_signup_coupon = array();
		}
		return $mwb_crp_referal_signup_coupon;

	}

	/**
	 * Save coupon code for the signup
	 *
	 * @since 1.0.0
	 * @param string $text .
	 * @param string $coupon_code .
	 * @param int    $user_id is the customer id who has registered himself successfully.
	 */
	public function save_singup_coupon_code( $text, $coupon_code, $user_id ) {
		$mwb_crp_signup_array          = array();
		$coupon                        = new WC_Coupon( $coupon_code );
		$coupon_code                   = $coupon->get_id();
		$mwb_crp_signup_array[ $text ] = $coupon_code;
		if ( ! empty( $mwb_crp_signup_array ) ) {
			update_user_meta( $user_id, 'mwb_crp_signup_coupon', $mwb_crp_signup_array );
		}
	}

	/**
	 * Get signup coupons
	 *
	 * @name get_signup_coupon
	 * @since 1.0.0
	 * @param int $user_id .
	 * @return signup purchase array
	 */
	public function get_signup_coupon( $user_id ) {
		$mwb_crp_signup_coupon = get_user_meta( $user_id, 'mwb_crp_signup_coupon', true );
		if ( empty( $mwb_crp_signup_coupon ) ) {
			$mwb_crp_signup_coupon = array();
		}
		return $mwb_crp_signup_coupon;

	}

	/**
	 * Create coupon and send mail for normal signup and reffral signup.
	 *
	 * @since 1.0.0
	 * @param int $user_id is the customer id who has registered himself successfully.
	 */
	public function mwb_create_coupon_send_email( $user_id ) {
		$mwb_cpr_coupon_length = $this->mwb_get_coupon_length();
		$mwb_cpr_coupon_amount = $this->mwb_get_coupon_amount();
		$mwb_cpr_coupon_expiry = $this->mwb_get_coupon_expiry();
		$expirydate            = $this->mwb_expiry_date_saved( $mwb_cpr_coupon_expiry );
		$bloginfo              = get_bloginfo();

		/*Get the coupon configration of the coupon*/
		$mwb_cpr_discount_type                    = $this->mwb_get_discount_signup_type();
		$coupon_amount_with_css                   = $this->mwb_formatted_amount( $mwb_cpr_coupon_amount, $mwb_cpr_discount_type );
		$user                                     = get_user_by( 'ID', $user_id );
		$user_email                               = $this->get_user_email( $user );
		$mwb_cpr_code                             = $this->mwb_cpr_coupon_generator( $mwb_cpr_coupon_length );
		$coupon_description                       = 'Coupon on Registration for UserID';
		$mwb_cpr_coupon_on_registration_custom_id = get_option( 'mwb_cpr_coupon_on_registration_custom_id', true );

		if ( $this->mwb_cpr_create_coupons( $mwb_cpr_code, $mwb_cpr_coupon_amount, $user_id, $mwb_cpr_discount_type, $expirydate, $coupon_description, $user_email ) ) {

			/* === Send the Email to the Registered User === */
			if ( empty( $expirydate ) ) {
				$expirydate = esc_html__( 'No Expiry', 'coupon-referral-program' );
			}
			$customer_email = WC()->mailer()->emails['crp_signup_email'];
			$email_status   = $customer_email->trigger( $user_id, $mwb_cpr_code, $coupon_amount_with_css, $expirydate );

		}
		return $mwb_cpr_code;
	}
	/**
	 * Check whether the Reffral Signup  Feature is enable
	 *
	 * @since 1.0.0
	 * @return bool value
	 */
	public function check_reffral_signup_is_enable() {
		$is_enable               = false;
		$mwb_cpr_referral_enable = get_option( 'mwb_crp_signup_enable_value', 'yes' );
		if ( ! empty( $mwb_cpr_referral_enable ) && 'no' === $mwb_cpr_referral_enable ) {

			$is_enable = true;
		}
		return $is_enable;
	}

	/**
	 * Check whether the Signup Discount Feature is enable
	 *
	 * @since 1.0.0
	 * @return bool value
	 */
	public function check_signup_is_enable() {
		$enable        = false;
		$enable_signup = get_option( 'mwb_crp_signup_enable', false );
		if ( ! empty( $enable_signup ) && 'yes' === $enable_signup ) {
			$enable = true;
		}
		return $enable;
	}

	/**
	 * Check whether the Shortcode  Feature is enable
	 *
	 * @since 1.0.0
	 * @return bool value
	 */
	public function check_shortcode_is_enable() {
		$enable           = false;
		$enable_shortcode = get_option( 'referral_button_positioning', false );
		if ( ! empty( $enable_shortcode ) && 'shortcode' === $enable_shortcode ) {
			$enable = true;
		}
		return $enable;
	}

	/**
	 * Returns the Position of the Popup Button
	 *
	 * @since 1.0.0
	 * @return Position of the Popup Button
	 */
	public static function get_position_popup_button() {
		$class       = '';
		$get_postion = get_option( 'referral_button_positioning', false );
		if ( ! empty( $get_postion ) && 'left_bottom' === $get_postion ) {
			$class = 'mwb_crp_btn_left_bottom';
		}
		if ( ! empty( $get_postion ) && 'right_bottom' === $get_postion ) {
			$class = 'mwb_crp_btn_right_bottom';
		}
		if ( ! empty( $get_postion ) && 'top_left' === $get_postion ) {
			$class = 'mwb_crp_btn_top_left';
		}
		if ( ! empty( $get_postion ) && 'top_right' === $get_postion ) {
			$class = 'mwb_crp_btn_top_right';
		}
		return $class;
	}

	/**
	 * Returns the Coupon Length
	 *
	 * @since 1.0.0
	 * @return Coupon Length Value
	 */
	public function mwb_get_coupon_length() {
		$coupon_length = get_option( 'coupon_length', 5 );
		return $coupon_length;
	}

	/**
	 * Returns the array of the pages
	 *
	 * @since 1.0.0
	 * @return array mwb_selected_pages
	 */
	public function mwb_get_selected_pages() {
		$mwb_selected_pages = array();
		$mwb_selected_pages = get_option( 'referral_button_page', array() );
		return $mwb_selected_pages;
	}

	/**
	 * Returns the Coupon Expiry; How many days has been set in backend
	 *
	 * @since 1.0.0
	 * @return Coupon Expiry Days Value
	 */
	public function mwb_get_coupon_expiry() {
		$coupon_expiry = get_option( 'coupon_expiry', '' );
		return $coupon_expiry;
	}

	/**
	 * Returns the Coupon Amount
	 *
	 * @since 1.0.0
	 * @return Coupon Amount Value for Signup/Registration
	 */
	public function mwb_get_coupon_amount() {
		$coupon_amount = get_option( 'signup_discount_value', ' ' );
		return $coupon_amount;
	}

	/**
	 * Returns the Expiry Date in WP Date-format, It will calculate the exact date when coupon will get expired
	 *
	 * @since 1.0.0
	 * @param string $mwb_cpr_coupon_expiry .
	 * @return Coupon Expiry Value in WP format
	 */
	public function mwb_expiry_date_saved( $mwb_cpr_coupon_expiry ) {
		$todaydate   = date_i18n( 'Y-m-d' );
		$date_format = get_option( 'date_format', 'Y-m-d' );
		if ( $mwb_cpr_coupon_expiry > 0 || 0 === $mwb_cpr_coupon_expiry ) {
			$expirydate = date_i18n( $date_format, strtotime( "$todaydate +$mwb_cpr_coupon_expiry day" ) );
		} else {
			$expirydate = '';
		}
		return $expirydate;
	}

	/**
	 * Returns the Discount Type has been set in the backend
	 *
	 * @since 1.0.0
	 * @return Discount Type: whether Fixed or Percentage
	 */
	public function mwb_get_discount_type() {
		$mwb_cpr_discount_type = get_option( 'signup_discount_type', 'mwb_cpr_fixed' );
		return $mwb_cpr_discount_type;
	}

	/**
	 * This function determine the which type of the coupon will be generated during the signup.
	 *
	 * @name mwb_get_discount_signup_discount
	 * @since 1.0.0
	 * @return Discount Type:Whether Fixed or Percentage.
	 */
	public function mwb_get_discount_signup_type() {
		$mwb_cpr_discount_type_signup = get_option( 'signup_discount_coupon_type', 'mwb_cpr_fixed' );
		return $mwb_cpr_discount_type_signup;
	}

	/**
	 * Returns the amount in Formatted way.
	 *
	 * @since 1.0.0
	 * @param int    $mwb_cpr_coupon_amount .
	 * @param string $mwb_cpr_discount_type .
	 * @return In Formatted Way as per the Discount Type has been set
	 */
	public function mwb_formatted_amount( $mwb_cpr_coupon_amount, $mwb_cpr_discount_type ) {
		if ( 'mwb_cpr_fixed' === $mwb_cpr_discount_type ) {
			$coupon_amount_with_css = '<span style="font-size: 30px;font-weight: bold;display: inline-block;">' . wc_price( $mwb_cpr_coupon_amount ) . '</span>';
		} else {
			$coupon_amount_with_css = '<span style="font-size: 30px;font-weight: bold;display: inline-block;">' . $mwb_cpr_coupon_amount . '%</span>';
		}
		return $coupon_amount_with_css;
	}

	/**
	 * Returns the Random Number Digit Coupon Code
	 *
	 * @since 1.0.0
	 * @param int $length for taking the input for generating the random number.
	 * @return $password for set the Coupon Code
	 */
	public function mwb_cpr_coupon_generator( $length ) {
		if ( '' == $length ) {
			$length = 5;
		}
		$password    = '';
		$alphabets   = range( 'A', 'Z' );
		$numbers     = range( '0', '9' );
		$final_array = array_merge( $alphabets, $numbers );
		while ( $length-- ) {
			$key       = array_rand( $final_array );
			$password .= $final_array[ $key ];
		}
		$mwb_cpr_coupon_prefix = get_option( 'coupon_prefix', '' );
		$password              = $mwb_cpr_coupon_prefix . $password;
		/**
		 * Filter coupon code.
		 *
		 * @since 1.6.4
		 * @param string $password
		 */
		$password = apply_filters( 'mwb_cpr_coupons', $password );
		return $password;
	}

	/**
	 * This Function is used to create the coupons
	 *
	 * @since 1.0.0
	 * @param mixed $mwb_cpr_code are required to generate the coupons .
	 * @param mixed $mwb_cpr_coupon_amount .
	 * @param mixed $creation_id .
	 * @param mixed $mwb_cpr_discount_type .
	 * @param mixed $expirydate .
	 * @param mixed $coupon_description .
	 * @param mixed $customer_email .
	 * @return bool on successfully creation
	 */
	public function mwb_cpr_create_coupons( $mwb_cpr_code, $mwb_cpr_coupon_amount, $creation_id, $mwb_cpr_discount_type, $expirydate, $coupon_description, $customer_email = '' ) {
		if ( isset( $creation_id ) && ! empty( $creation_id ) ) {
			$woo_ver     = WC()->version;
			$coupon_code = $mwb_cpr_code;
			$amount      = $mwb_cpr_coupon_amount;
			if ( 'mwb_cpr_fixed' === $mwb_cpr_discount_type ) {
				$discount_type = 'fixed_cart';
			} elseif ( 'mwb_cpr_percent' === $mwb_cpr_discount_type ) {
				$discount_type = 'percent';
			}
			$coupon_description = $coupon_description . " #$creation_id";
			$coupon             = array(
				'post_title'   => $coupon_code,
				'post_content' => $coupon_description,
				'post_excerpt' => $coupon_description,
				'post_status'  => 'publish',
				'post_author'  => get_current_user_id(),
				'post_type'    => 'shop_coupon',
			);
			$new_coupon_id      = wp_insert_post( $coupon );
			// Added since woocommerce 3.9.0.
			if ( $new_coupon_id ) {
				$coupon_obj = new WC_Coupon( $new_coupon_id );
				$coupon_obj->save();
			}
			$coupon_usage                = get_option( 'coupon_usage', '' );
			$coupon_individual           = get_option( 'coupon_individual', 'no' );
			$coupon_freeshipping         = get_option( 'coupon_freeshipping', 'no' );
			$mwb_crp_sales_item          = $this->mwb_crp_exclude_sales_item();
			$mwb_crp_get_min_spend       = $this->mwb_crp_get_min_spend();
			$mwb_crp_get_max_spend       = $this->mwb_crp_get_max_spend();
			$mwb_crp_get_include_product = $this->mwb_crp_get_include_product();
			if ( isset( $mwb_crp_get_include_product ) && ! empty( $mwb_crp_get_include_product ) ) {
				$mwb_crp_get_include_product = implode( ',', $mwb_crp_get_include_product );
			}
			$mwb_crp_get_exclude_pro = $this->mwb_crp_get_exclude_pro();
			if ( isset( $mwb_crp_get_exclude_pro ) && ! empty( $mwb_crp_get_exclude_pro ) ) {
				$mwb_crp_get_exclude_pro = implode( ',', $mwb_crp_get_exclude_pro );
			}

			update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
			update_post_meta( $new_coupon_id, 'free_shipping', $coupon_freeshipping );
			update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
			update_post_meta( $new_coupon_id, 'individual_use', $coupon_individual );
			update_post_meta( $new_coupon_id, 'usage_limit', $coupon_usage );

			update_post_meta( $new_coupon_id, 'minimum_amount', $mwb_crp_get_min_spend );
			update_post_meta( $new_coupon_id, 'maximum_amount', $mwb_crp_get_max_spend );
			update_post_meta( $new_coupon_id, 'exclude_sale_items', $mwb_crp_sales_item );

			if ( isset( $mwb_crp_get_include_product ) && ! empty( $mwb_crp_get_include_product ) ) {

				update_post_meta( $new_coupon_id, 'product_ids', $mwb_crp_get_include_product );
			}
			if ( isset( $mwb_crp_get_exclude_pro ) && ! empty( $mwb_crp_get_exclude_pro ) ) {

				update_post_meta( $new_coupon_id, 'exclude_product_ids', $mwb_crp_get_exclude_pro );
			}

			// add exlucde/include categories data

			$wps_crp_get_include_cat = $this->wps_crp_get_include_cat();

			$wps_crp_get_exclude_cat = $this->wps_crp_get_exclude_cat();
			
			if ( isset( $wps_crp_get_include_cat ) && ! empty( $wps_crp_get_include_cat ) ) {

				update_post_meta( $new_coupon_id, 'product_categories', $wps_crp_get_include_cat );
			}
			if ( isset( $wps_crp_get_exclude_cat ) && ! empty( $wps_crp_get_exclude_cat ) ) {

				update_post_meta( $new_coupon_id, 'exclude_product_categories', $wps_crp_get_exclude_cat );
			}
			/*coupon code expiry*/
			$coupon_expiry = get_option( 'coupon_expiry', '' );
			$todaydate     = date_i18n( 'Y-m-d' );
			if ( 0 < $coupon_expiry || 0 === $coupon_expiry ) {
				$expirydate_save = date_i18n( 'Y-m-d', strtotime( "$todaydate +$coupon_expiry day" ) );
			} else {
				$expirydate_save = '';
			}

			if ( $woo_ver < '3.6.0' ) {
				update_post_meta( $new_coupon_id, 'expiry_date', $expirydate_save );
			} else {
				if ( ! empty( $expirydate_save ) ) {
					$expirydate_save = strtotime( $expirydate_save );
				}
				update_post_meta( $new_coupon_id, 'date_expires', $expirydate_save );
			}

			update_post_meta( $new_coupon_id, 'coupon_created_to', $creation_id );
			// add allowed email to customer.
			$customer_email_array = array();
			if ( isset( $customer_email ) && ! empty( $customer_email ) ) {
				$customer_email_array[] = $customer_email;
				$customer_email         = array_filter( array_map( 'sanitize_email', $customer_email_array ) );
				update_post_meta( $new_coupon_id, 'customer_email', $customer_email );
			}
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get the Signup Discount Amount along with HTML format
	 *
	 * @since 1.0.0
	 * @return html with Signup Disocunt amount
	 */
	public function get_signup_discount_html() {
		$signup_discount_type  = $this->mwb_get_discount_signup_type();
		$signup_discount_value = get_option( 'signup_discount_value', '' );
		if ( empty( $signup_discount_value ) ) {
			$signup_discount_value = 1;
		}
		if ( 'mwb_cpr_fixed' === $signup_discount_type ) {
			return wc_price( $signup_discount_value );
		} else {
			return $signup_discount_value . '%';
		}
	}

	/**
	 * Get the User Email.
	 *
	 * @since 1.0.0
	 * @param object $user_obj Object of particular User.
	 * @return string containing user email
	 */
	public function get_user_email( $user_obj ) {
		$user_email = '';
		if ( ! empty( $user_obj ) ) {
			$user_email = $user_obj->user_email;
		}
		return $user_email;
	}

	/**
	 * Get the Subject for Signup Discount Email
	 *
	 * @since 1.0.0
	 * @return string containing the subject
	 */
	public function get_discount_coupon_signup_subject() {
		$mwb_cpr_coupon_on_registration = __( 'Discount Coupon on Signup', 'coupon-referral-program' );
		$mwb_cpr_coupon_on_registration = get_option( 'mwb_cpr_coupon_on_registration', $mwb_cpr_coupon_on_registration );
		return $mwb_cpr_coupon_on_registration;
	}

	/**
	 * This function we used to set the referral key in cookie and then we reward them
	 *
	 * @since 1.0.0
	 */
	public function wp_loaded_set_referral_key() {
		if ( ! is_admin() ) {
			if ( ! is_user_logged_in() ) {
				$mwb_cpr_ref_link_expiry = get_option( 'mwb_cpr_ref_link_expiry', 2 );
				$cookie_val              = isset( $_COOKIE['mwb_cpr_cookie_set'] ) ? unserialize( base64_decode( sanitize_text_field( wp_unslash( $_COOKIE['mwb_cpr_cookie_set'] ) ) ) ) : '';
				if ( isset( $_GET['ref'] ) && ! empty( $_GET['ref'] ) && sanitize_text_field( wp_unslash( $_GET['ref'] ) ) ) {
					$referral_key = sanitize_text_field( wp_unslash( $_GET['ref'] ) );
					$referral_key = trim( $referral_key );
					if ( ! empty( $referral_key ) ) {
						$todaydate    = date_i18n( 'Y-m-d' );
						$expirydate   = date_i18n( 'Y-m-d', strtotime( "$todaydate +$mwb_cpr_ref_link_expiry day" ) );
						$referral_key = array(
							'referral_key' => $referral_key,
							'expirydate'   => $expirydate,
						);
						if ( ! empty( $cookie_val ) &&
							! empty( $cookie_val['referral_key'] )
							&& ! empty( $cookie_val['expirydate'] ) ) {
							if ( $referral_key !== $cookie_val['referral_key'] ) {
								setcookie( 'mwb_cpr_cookie_set', base64_encode( serialize( $referral_key ) ), time() + ( 86400 * $mwb_cpr_ref_link_expiry ), '/' );
							} elseif ( $todaydate > $expirydate ) {
								setcookie( 'mwb_cpr_cookie_set', base64_encode( serialize( $referral_key ) ), time() + ( 86400 * $mwb_cpr_ref_link_expiry ), '/' );
							}
						} elseif ( ! empty( $mwb_cpr_ref_link_expiry ) ) {
							setcookie( 'mwb_cpr_cookie_set', base64_encode( serialize( $referral_key ) ), time() + ( 86400 * $mwb_cpr_ref_link_expiry ), '/' );
						}
					}
					$redirection_url = esc_url_raw( get_option( 'referral_link_redirection', false ) );
					if ( $redirection_url &&  wp_http_validate_url( $redirection_url ) ) {
						wp_safe_redirect( $redirection_url );
						exit();
					}
				}
			}
		}
	}

	/**
	 * Get the Referral Discount on which % would be calculated over Order Total
	 *
	 * @param int $user_id .
	 */
	public function get_referral_discount_order( $user_id ) {
		$referral_first_discount = get_option( 'mwb_crp_enable_first_referal_purchase', 'no' );
		$total_order_placed      = $this->get_number_of_orders_placed( $user_id );
		if ( 'yes' === $referral_first_discount && 1 == $total_order_placed ) {
			$referral_discount_on_order = get_option( 'first_referral_discount_on_order', 1 );
		} else {
			$referral_discount_on_order = get_option( 'referral_discount_on_order', 1 );
		}
		return $referral_discount_on_order;
	}
	/**
	 * Check whether the Social Sharing is enabled or not
	 *
	 * @since 1.0.0
	 * @return bool value
	 */
	public function is_social_sharing_enabled() {
		$mwb_cpr_social_enable = get_option( 'mwb_cpr_social_enable', 'off' );
		if ( 'yes' === $mwb_cpr_social_enable ) {
			$mwb_cpr_social_enable = true;
		} else {
			$mwb_cpr_social_enable = false;
		}
		return $mwb_cpr_social_enable;
	}

	/**
	 * Get the html for social button icons as per required setting has enabled in backend
	 *
	 * @since 1.0.0
	 * @param int $user_id .
	 * @return $content as the HTMl
	 */
	public function get_social_sharing_html( $user_id ) {

		$user_reference_key = get_user_meta( $user_id, 'referral_key', true );
		/**
		 * Filter referral site url.
		 *
		 * @since 1.6.4
		 * @param string site url() .
		 */
		$page_permalink = apply_filters( 'mwb_crp_referral_link_url', site_url() );
		$referral_link  = $page_permalink . '?ref=' . $user_reference_key;
		$content        = '';
		$content        = $content . '<div class="mwb_crp_wrapper_button">';

		$twitter_button = '<div class="mwb_crp_btn mwb_crp_common_class"><a class="twitter-share-button" href="https://twitter.com/intent/tweet?text=' . $page_permalink . '?ref=' . $user_reference_key . '" target="_blank"><img src ="' . COUPON_REFERRAL_PROGRAM_DIR_URL . '/public/images/twitter.png" alt="twitter icon">' . esc_html__( 'Tweet', 'coupon-referral-program' ) . '</a></div>';

		$fb_button = '<div id="fb-root"></div>
		<div class="fb-share-button mwb_crp_common_class" data-href="' . $page_permalink . '?ref=' . $user_reference_key . '" data-layout="button_count" data-size="small" data-mobile-iframe="true"><a class="fb-xfbml-parse-ignore" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=' . $page_permalink . '?ref=' . $user_reference_key . '">' . __( 'Share', 'coupon-referral-program' ) . '</a></div>';

		$mail = '<a class="mwb_wpr_mail_button mwb_crp_common_class" href="#" rel="nofollow"><img src ="' . COUPON_REFERRAL_PROGRAM_DIR_URL . 'public/images/email.png" alt="email icon"></a>';

		$whtsapp = '<a target="_blank" href=https://web.whatsapp.com/send?text=' . $referral_link . ' class="mwb_crp_common_class mwb_crp_whatsapp_button"><img src ="' . COUPON_REFERRAL_PROGRAM_DIR_URL . 'public/images/watsap.png" alt="whatsapp icon"></a>';

		if ( 'yes' === get_option( 'mwb_cpr_facebook', 'no' ) ) {

			$content = $content . $fb_button;
		}
		if ( 'yes' === get_option( 'mwb_cpr_twitter', 'no' ) ) {

			$content = $content . $twitter_button;
		}
		if ( 'yes' === get_option( 'mwb_crp_share_whtsapp', 'no' ) ) {
			$content = $content . $whtsapp;
		}
		if ( 'yes' === get_option( 'mwb_cpr_email', 'no' ) ) {

			$content = $content . $mail;
		}
		$content = $content . '</div>';
		return $content;
	}

	/**
	 * Reward the Discount Coupon to the referrer on the purchasing of referred user.
	 *
	 * @since 1.0.0
	 * @param mixed $order_id .
	 * @param mixed $old_status .
	 * @param mixed $new_status .
	 */
	public function woocommerce_order_status_changed_discount( $order_id, $old_status, $new_status ) {
		if ( $old_status !== $new_status ) {
			if ( 'completed' === $new_status ) {
				$order = wc_get_order( $order_id );
				/* Start Save utilization of the coupon */
				$this->save_utilize_coupon_aomunt( $order, $order_id );
				$bloginfo                 = get_bloginfo();
				$is_referral_has_rewarded = $order->get_meta( 'referral_has_rewarded' );

				if ( isset( $is_referral_has_rewarded ) && ! empty( $is_referral_has_rewarded ) ) {
					return;
				}
				$user_id = absint( $order->get_user_id() );

				/*======= Set the Number of Order User has placed  =======*/

				$this->set_number_of_orders( $user_id );
				// Check if referl purchase enable.
				if ( ! $this->check_referl_purchase_is_enable() ) {
					return;
				}
				$already_placed_orders = $this->get_number_of_orders_placed( $user_id );
				$restrict_no_of_order  = $this->get_number_of_orders_required();

				if ( $this->check_share_vai_referal_code() ) {
					$this->mwb_crp_referal_purchase_discount_by_referal_coupon( $order );
				}
				// check if the minimum order limit exceed
				if ( $this->wps_crp_minimum_order_total_limit( $order ) ) {
					return;
				}

				/*======= Reward the Discount only if the User has minimum number of Order has been placed from the Limit  =======*/
				if ( $already_placed_orders <= $restrict_no_of_order ) {
					$refree_id          = get_user_meta( $user_id, 'mwb_cpr_user_referred_by', true );
					$refree             = get_user_by( 'ID', $refree_id );
					$coupon_description = 'Coupon on Order for OrderID';
					$refree_email       = $this->get_user_email( $refree );
					if ( ! empty( $order ) ) {
						$order_total                = $order->get_total();
						$referral_discount_on_order = $this->get_referral_discount_order( $user_id );
						if ( isset( $refree_id ) && ! empty( $refree_id ) ) {
							/**
							 * Filter check WPS PAR is active.
							 *
							 * @since 1.6.4
							 * @param bool false flag .
							 */
							$check_par_enable = apply_filters( 'mwb_crp_par_check_enable', false );
							if ( ! self::check_is_points_rewards_enable() ) {
								$mwb_cpr_coupon_amount  = $this->get_referral_coupon_amount( $referral_discount_on_order, $order_total );
								$mwb_cpr_coupon_length  = $this->mwb_get_coupon_length();
								$mwb_cpr_code           = $this->mwb_cpr_coupon_generator( $mwb_cpr_coupon_length );
								$mwb_cpr_coupon_expiry  = $this->mwb_get_coupon_expiry();
								$mwb_cpr_discount_type  = $this->mwb_get_discount_type();
								$coupon_amount_with_css = $this->mwb_formatted_amount( $mwb_cpr_coupon_amount, $mwb_cpr_discount_type );
								$expirydate             = $this->mwb_expiry_date_saved( $mwb_cpr_coupon_expiry );

								/*======= Create the Woocommerce Coupons  =======*/

								if ( $this->wps_referral_discount_management('referal_purchase', $refree_id ) ) {
									if ( $this->mwb_cpr_create_coupons( $mwb_cpr_code, $mwb_cpr_coupon_amount, $order_id, $mwb_cpr_discount_type, $expirydate, $coupon_description, $refree_email ) ) {
	
										/* === Send the Email to the relevant customer === */
	
										$customer_email = WC()->mailer()->emails['crp_order_email'];
										if ( empty( $expirydate ) ) {
											$expirydate = esc_html__( 'No Expiry', 'coupon-referral-program' );
	
										}
										$email_status = $customer_email->trigger( $refree_id, $mwb_cpr_code, $coupon_amount_with_css, $expirydate );
	
										update_post_meta( $order_id, 'referral_has_rewarded', $refree_id );
										$this->save_referral_coupon_code( $mwb_cpr_code, $refree_id, $user_id );
									}
								}
							} elseif ( $check_par_enable ) {
								/**
								 * Add points for referee on the order purchases.
								 *
								 * @since 1.6.4
								 * @param integer $user_id .
								 * @param integer $refree_id .
								 */
								do_action( 'mwb_crp_par_order_purchase_add_points', $user_id, $refree_id );
							} else {
								$points = $this->get_points_for_reffral_purchase();
								WC_Points_Rewards_Manager::increase_points( $refree_id, $points, 'refrral-order-purchase' );
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Save the utilize coupon amount of the order
	 *
	 * @since 1.0.0
	 * @param object $order .
	 * @param int    $order_id .
	 */
	public function save_utilize_coupon_aomunt( $order, $order_id ) {

		$utilize_coupon_amount_updated = $order->get_meta( 'utilize_coupon_amount_updated' );
		if ( isset( $utilize_coupon_amount_updated ) && 'set' === $utilize_coupon_amount_updated ) {
			return;
		}
		$coupons                            = $order->get_items( 'coupon' );
		$user_id                            = $order->get_user_id();
		$signup_coupon                      = $this->get_signup_coupon( $user_id );
		$referral_purchase_coupons          = $this->get_referral_purchase_coupons( $user_id );
		$crp_get_utilize_coupon_amount      = $this->get_utilize_coupon_amount( $user_id );
		$mwb_crp_get_referal_signup_coupon  = $this->mwb_crp_get_referal_signup_coupon( $user_id );
		$referral_purchase_coupons_on_guest = $this->get_referral_purchase_coupons_on_guest( $user_id );
		$wps_crp_trial_ended_discount       = $this->wps_crp_get_paid_referal_trial_ended_coupons( $user_id );


		if ( $this->check_array_is_not_empty( $coupons ) ) {
			foreach ( $coupons as $item_id => $item ) {
				$coupon_obj = new WC_Coupon( $item->get_code() );
				/*Check is referral coupon is applied or not*/
				if ( $this->check_array_is_not_empty( $referral_purchase_coupons ) ) {
					foreach ( $referral_purchase_coupons as $coupon_id => $userid ) {
						if ( $coupon_obj->get_id() == $coupon_id ) {
							$crp_get_utilize_coupon_amount = (float) $crp_get_utilize_coupon_amount + (float) $item->get_discount();
						}
					}
				}

				/*Check is sigup coupon is applied or not*/
				if ( $this->check_array_is_not_empty( $signup_coupon ) ) {
					if ( $coupon_obj->get_id() == $signup_coupon['singup'] ) {
						$crp_get_utilize_coupon_amount = (float) $crp_get_utilize_coupon_amount + (float) $item->get_discount();
					}
				}

				/*Check is referral signup coupon is applied or not*/
				if ( $this->check_array_is_not_empty( $mwb_crp_get_referal_signup_coupon ) ) {
					foreach ( $mwb_crp_get_referal_signup_coupon as $coupon_id => $userid ) {
						if ( $coupon_obj->get_id() == $coupon_id ) {
							$crp_get_utilize_coupon_amount = (float) $crp_get_utilize_coupon_amount + (float) $item->get_discount();
						}
					}
				}
				/*Check is referral signup coupon is applied or not*/
				if ( $this->check_array_is_not_empty( $referral_purchase_coupons_on_guest ) ) {
					foreach ( $referral_purchase_coupons_on_guest as $coupon_id => $email_id ) {
						if ( $coupon_obj->get_id() == $coupon_id ) {
							$crp_get_utilize_coupon_amount = (float) $crp_get_utilize_coupon_amount + (float) $item->get_discount();
						}
					}
				}

				/*  trial ended discount coupon */
				if ( $this->check_array_is_not_empty( $wps_crp_trial_ended_discount ) ) {
					foreach ( $wps_crp_trial_ended_discount as $coupon_id => $sub_id ) {
						if ( $coupon_obj->get_id() == $coupon_id ) {
							$crp_get_utilize_coupon_amount = (float) $crp_get_utilize_coupon_amount + (float) $item->get_discount();
						}
					}
				}
			}
		}
		/*Update coupon amount in usermeta of the utilize coupon*/
		if ( ! empty( $crp_get_utilize_coupon_amount ) ) {
			update_user_meta( $user_id, 'utilize_coupon_amount', $crp_get_utilize_coupon_amount );
			update_post_meta( $order_id, 'utilize_coupon_amount_updated', 'set' );
		}

	}

	/**
	 * Get the utilize coupon amount of the order
	 *
	 * @since 1.0.0
	 * @param int $user_id .
	 */
	public function get_utilize_coupon_amount( $user_id ) {
		$utilize_coupon_amount = 0;
		$utilize_amount        = get_user_meta( $user_id, 'utilize_coupon_amount', true );
		if ( ! empty( $utilize_amount ) ) {
			$utilize_coupon_amount = $utilize_amount;
		}
		return $utilize_coupon_amount;
	}

	/**
	 * Get the utilize coupon amount of the order
	 *
	 * @since 1.0.0
	 * @param int $user_id .
	 * @param int $amount .
	 */
	public function update_utilize_coupon_amount( $user_id, $amount ) {
		$total_amount = $this->get_utilize_coupon_amount( $user_id );
		if ( ! empty( $amount ) ) {
			$total_amount += $amount;
			update_user_meta( $user_id, 'utilize_coupon_amount', $total_amount );
		}
		return true;
	}
	/**
	 * Check is any array not empty
	 *
	 * @since 1.0.0
	 * @param array $array .
	 */
	public function check_array_is_not_empty( $array ) {
		$is_not_empty = false;
		if ( is_array( $array ) && ! empty( $array ) ) {
			$is_not_empty = true;
		}
		return $is_not_empty;
	}

	/**
	 * Save referrals purchase coupon
	 *
	 * @name save_referral_coupon_code
	 * @since 1.0.0
	 * @param string $mwb_cpr_code .
	 * @param int    $refree_id .
	 * @param int    $user_id .
	 */
	public function save_referral_coupon_code( $mwb_cpr_code, $refree_id, $user_id ) {
		$mwb_crp_referral_purchase_array = array();
		$mwb_crp_referral_purchase       = get_user_meta( $refree_id, 'mwb_crp_referral_purchase_coupons', true );
		$coupon                          = new WC_Coupon( $mwb_cpr_code );
		$mwb_cpr_code                    = $coupon->get_id();
		if ( ! empty( $mwb_crp_referral_purchase ) ) {

			$mwb_crp_referral_purchase[ $mwb_cpr_code ] = $user_id;

			update_user_meta( $refree_id, 'mwb_crp_referral_purchase_coupons', $mwb_crp_referral_purchase );
		} else {
			$mwb_crp_referral_purchase_array[ $mwb_cpr_code ] = $user_id;
			update_user_meta( $refree_id, 'mwb_crp_referral_purchase_coupons', $mwb_crp_referral_purchase_array );
		}

	}

	/**
	 * Get referral purchase coupon.
	 *
	 * @name get_referral_purchase_coupons
	 * @param int $user_id .
	 * @since 1.0.0
	 * @return referral purchase array
	 */
	public function get_referral_purchase_coupons( $user_id ) {
		$mwb_crp_referral_purchase = get_user_meta( $user_id, 'mwb_crp_referral_purchase_coupons', true );
		if ( empty( $mwb_crp_referral_purchase ) ) {
			$mwb_crp_referral_purchase = array();
		}
		return $mwb_crp_referral_purchase;

	}

	/**
	 * Get the referral coupon amount type
	 *
	 * @since 1.0.0
	 */
	public function get_referral_coupon_amount_type() {
		$referral_coupon_amount_type = get_option( 'referral_discount_type', 'mwb_cpr_referral_percent' );
		return $referral_coupon_amount_type;
	}

	/**
	 * Get the referral coupon amount limit
	 *
	 * @since 1.0.0
	 */
	public function get_referral_coupon_amount_limit_html() {
		$referral_discount_maxi_amt = get_option( 'referral_discount_upto', 0 );
		$mwb_cpr_amount_html        = '';
		if ( 0 != $referral_discount_maxi_amt ) {

			if ( 'mwb_cpr_referral_percent' === $this->get_referral_coupon_amount_type() && 'mwb_cpr_percent' === $this->mwb_get_discount_type() ) {

				$mwb_cpr_amount_html = __( ' upto ', 'coupon-referral-program' ) . '<span class="mwb_cpr_highlight" style="color:' . Coupon_Referral_Program_Admin::get_selected_color() . '">' . $referral_discount_maxi_amt . '%.</span>';
			} else {
				$mwb_cpr_amount_html = __( ' upto ', 'coupon-referral-program' ) . '<span class="mwb_cpr_highlight" style="color:' . Coupon_Referral_Program_Admin::get_selected_color() . '">' . wc_price( $referral_discount_maxi_amt ) . '.</span>';

			}
		}

		echo wp_kses_post( $mwb_cpr_amount_html );
	}

	/**
	 * Get the referral coupon amount
	 *
	 * @param mixed $referral_discount_on_order .
	 * @param mixed $order_total .
	 * @since 1.0.0
	 */
	public function get_referral_coupon_amount( $referral_discount_on_order, $order_total ) {
		$referral_coupon_amount_type = get_option( 'referral_discount_type', 'mwb_cpr_referral_percent' );
		if ( 'mwb_cpr_referral_fixed' === $referral_coupon_amount_type ) {
			$mwb_cpr_coupon_amount = $referral_discount_on_order;
		} elseif ( 'mwb_cpr_referral_percent' === $referral_coupon_amount_type ) {
			$mwb_cpr_coupon_amount = round( ( $referral_discount_on_order * $order_total ) / 100 );
			$mwb_cpr_coupon_amount = $this->get_upto_discount( $mwb_cpr_coupon_amount );
		}
		return $mwb_cpr_coupon_amount;
	}

	/**
	 * Get the upto discount.
	 *
	 * @param int $mwb_cpr_coupon_amount .
	 * @since 1.0.0
	 */
	public function get_upto_discount( $mwb_cpr_coupon_amount ) {

		$referral_discount_maxi_amt = get_option( 'referral_discount_upto', 0 );
		if ( empty( $referral_discount_maxi_amt ) ) {
			$referral_discount_maxi_amt = 0;
		}

		if ( $mwb_cpr_coupon_amount > $referral_discount_maxi_amt && 0 != $referral_discount_maxi_amt ) {
			$mwb_cpr_coupon_amount = $referral_discount_maxi_amt;
		}
		return $mwb_cpr_coupon_amount;

	}

	/**
	 * Get the number of orders which is limited for rewarding the discount to the Referee
	 *
	 * @since 1.0.0
	 */
	public function get_number_of_orders_required() {
		$restrict_no_of_order = get_option( 'restrict_no_of_order', 1 );
		$restrict_no_of_order = isset( $restrict_no_of_order ) ? $restrict_no_of_order : 1;
		return $restrict_no_of_order;
	}

	/**
	 * Get the number of orders for one particular user.
	 *
	 * @since 1.0.0
	 * @param int $user_id for which the number of placed orders you want to get.
	 */
	public function get_number_of_orders_placed( $user_id ) {
		$no_of_orders = get_user_meta( $user_id, 'mwb_crp_number_of_orders', true );
		$no_of_orders = isset( $no_of_orders ) ? $no_of_orders : 1;
		return $no_of_orders;
	}

	/**
	 * Set the number of orders for one particular user.
	 *
	 * @since 1.0.0
	 * @param int $user_id for which the number of orders needs to be set.
	 */
	public function set_number_of_orders( $user_id ) {
		$pre_existing_orders = get_user_meta( $user_id, 'mwb_crp_number_of_orders', true );
		if ( isset( $pre_existing_orders ) && ! empty( $pre_existing_orders ) ) {
			$pre_existing_orders++;
		} else {
			$pre_existing_orders = 1;
		}
		update_user_meta( $user_id, 'mwb_crp_number_of_orders', $pre_existing_orders );
	}

	/**
	 * Check is points and rewards settings checkbox is enable
	 *
	 * @since 1.0.0
	 */
	public static function check_is_points_rewards_enable() {
		$mwb_is_enable             = false;
		$mwb_points_rewards_enable = get_option( 'mwb_crp_points_rewards_enable', false );
		if ( ! empty( $mwb_points_rewards_enable ) && 'yes' === $mwb_points_rewards_enable ) {
			$mwb_is_enable = true;
		}
		if ( ! is_plugin_active( 'woocommerce-points-and-rewards/woocommerce-points-and-rewards.php' ) ) {
			$mwb_is_enable = false;
		}
		return $mwb_is_enable;
	}

	/**
	 * Get points of the signup.
	 *
	 * @since 1.0.0
	 */
	public function get_points_for_signup() {
		$mwb_crp_points_rewards_signup_points = get_option( 'mwb_crp_points_rewards_signup_points', false );
		if ( empty( $mwb_crp_points_rewards_signup_points ) ) {
			$mwb_crp_points_rewards_signup_points = 0;
		}
		return $mwb_crp_points_rewards_signup_points;
	}

	/**
	 * Get points of the referee signup.
	 *
	 * @since 1.0.0
	 */
	public function get_points_for_refree_signup() {
		$mwb_crp_points_rewards_refree_signup_points = get_option( 'mwb_crp_points_rewards_reffree_points', 0 );
		return $mwb_crp_points_rewards_refree_signup_points;
	}

	/**
	 * Get points reffral purchase.
	 *
	 * @since 1.0.0
	 */
	public function get_points_for_reffral_purchase() {

		$mwb_crp_reffral_purchase_points = get_option( 'mwb_crp_points_rewards_reffral_points', 0 );
		if ( empty( $mwb_crp_reffral_purchase_points ) && is_numeric( $mwb_crp_reffral_purchase_points ) ) {
			$mwb_crp_reffral_purchase_points = 0;
		}
		return $mwb_crp_reffral_purchase_points;
	}

	/**
	 * Add points log for the woocommerce points reward extension.
	 *
	 * @since 1.0.0
	 * @param mixed $event_description .
	 * @param mixed $event_type .
	 * @param mixed $event .
	 */
	public function wc_points_rewards_event_description( $event_description, $event_type, $event ) {
		global $wc_points_rewards;
		$points_label = $wc_points_rewards->get_points_label( $event ? $event->points : null );

		// set the description if we know the type.
		switch ( $event_type ) {
			case 'refrral-order-purchase':
				// translators: Point_label.
				$event_description = sprintf( __( '%s earned for referral order purchase', 'woocommerce-points-and-rewards' ), $points_label );
				break;
			case 'reffral-account-signup':
				// translators: Point_label.
				$event_description = sprintf( __( '%s earned for referral account signup', 'woocommerce-points-and-rewards' ), $points_label );
				break;
			case 'reffree-account-on-referal-signup':
				// translators: Point_label.
				$event_description = sprintf( __( '%s earned for referral signup points for referee', 'woocommerce-points-and-rewards' ), $points_label );
				break;

		}

		return $event_description;
	}

	/**
	 * This function is used to set User Role to see Referral Coupon Tab in MY ACCOUNT Page
	 *
	 * @since 1.0.0
	 * @param array $items .
	 */
	public function crp_referral_coupon_dashboard( $items ) {

		$logout = $items['customer-logout'];
		unset( $items['customer-logout'] );
		$items['referral_coupons'] = __( 'Referrals', 'coupon-referral-program' );
		$items['customer-logout']  = $logout;
		return $items;
	}

	/**
	 * This function is used to show coupon on the myaccount page
	 *
	 * @since 1.0.0
	 */
	public function crp_coupon_account_points() {
		$user_ID = get_current_user_ID();
		$user    = new WP_User( $user_ID );

		/**
		 * This filter used to show the referral dashboard template.
		 *
		 * @since 1.6.6
		 */
		require apply_filters( 'crp_referral_dashboard_template', COUPON_REFERRAL_PROGRAM_DIR_PATH . 'public/partials/coupon-referral-program-public-display.php' );
	}

	/**
	 * This function is used to calculate the total earning and total report
	 *
	 * @since 1.0.0
	 * @param mixed $user_id .
	 * @name get_revenue
	 */
	public function get_revenue( $user_id ) {
		$signup_coupon                     = $this->get_signup_coupon( $user_id );
		$referral_purchase_coupons         = $this->get_referral_purchase_coupons( $user_id );
		$mwb_crp_get_referal_signup_coupon = $this->mwb_crp_get_referal_signup_coupon( $user_id );
		$wps_trial_ended                   = $this->wps_crp_get_paid_referal_trial_ended_coupons( $user_id );
		$mwb_referred_user                 = array();
		$mwb_crp_total_earn                = 0;
		$mwb_coupon_count                  = 0;
		if ( ! empty( $referral_purchase_coupons ) && is_array( $referral_purchase_coupons ) ) {
			foreach ( $referral_purchase_coupons as $coupon_code => $uid ) {
				$coupon = new WC_Coupon( $coupon_code );
				if ( 'publish' === get_post_status( $coupon_code ) ) {
					$mwb_crp_total_earn += $coupon->get_amount();
					array_push( $mwb_referred_user, $uid );
					$mwb_coupon_count++;
				}
			}
			$mwb_referred_user = array_unique( $mwb_referred_user );
		}
		// Referal signup discount coupon.
		if ( ! empty( $mwb_crp_get_referal_signup_coupon ) && is_array( $mwb_crp_get_referal_signup_coupon ) ) {
			foreach ( $mwb_crp_get_referal_signup_coupon as $coupon_code => $uid ) {
				$coupon  = new WC_Coupon( $coupon_code );
				if ( 'publish' === get_post_status( $coupon_code ) ) {
					$mwb_crp_total_earn += $coupon->get_amount();
					array_push( $mwb_referred_user, $uid );
					$mwb_coupon_count++;
				}
			}
			$mwb_referred_user = array_unique( $mwb_referred_user );
		}
		if ( ! empty( $signup_coupon['singup'] ) && is_array( $signup_coupon ) ) {
			$coupon = new WC_Coupon( $signup_coupon['singup'] );
			if ( 'publish' === get_post_status( $signup_coupon['singup'] ) ) {
				$mwb_crp_total_earn += $coupon->get_amount();
				$mwb_coupon_count++;
			}
		}
		if ( ! empty( $wps_trial_ended ) && is_array( $wps_trial_ended ) ) {
			foreach ( $wps_trial_ended as $coupon_code => $subid ) {
				$coupon  = new WC_Coupon( $coupon_code );
				if ( 'publish' === $coupon->get_status() ) {
					$mwb_crp_total_earn += $coupon->get_amount();
					$mwb_coupon_count++;
				}
			}
		}
		if ( empty( $mwb_referred_user ) ) {
			$mwb_referred_user = 0;
		} else {
			$mwb_referred_user = count( $mwb_referred_user );
		}

		$referral_purchase_coupons_on_guest = $this->get_referral_purchase_coupons_on_guest( $user_id );
		$mwb_referred_user_via_code         = array();
		if ( ! empty( $referral_purchase_coupons_on_guest ) && is_array( $referral_purchase_coupons_on_guest ) ) {
			foreach ( $referral_purchase_coupons_on_guest as $coupon_code => $user_email ) {
				$coupon = new WC_Coupon( $coupon_code );
				if ( 'publish' === get_post_status( $coupon_code ) ) {
					$mwb_crp_total_earn += $coupon->get_amount();
					array_push( $mwb_referred_user_via_code, $user_email );
					$mwb_coupon_count++;
				}
			}

			$mwb_referred_user_via_code = array_unique( $mwb_referred_user_via_code );
			$mwb_referred_user          = $mwb_referred_user + count( $mwb_referred_user_via_code );
		}
		// Correct referred user total code.
		$users = get_users(
			array(
				'meta_key'   => 'mwb_cpr_user_referred_by',
				'meta_value' => $user_id,
			)
		);
		if ( is_array( $users ) && ! empty( $users ) ) {
			$mwb_referred_user = 0;
			foreach ( $users as $key => $user ) {
				$mwb_referred_user++;
			}
		}
		return array(
			'total_earning'  => $mwb_crp_total_earn,
			'referred_users' => $mwb_referred_user,
			'total_coupon'   => $mwb_coupon_count,
		);
	}


	/**
	 * This function is used check is enable subscription
	 *
	 * @since 1.0.0
	 * @name mwb_crp_is_enable_subscription
	 */
	public function mwb_crp_is_enable_subscription() {
		$is_enable_woo_sub      = false;
		$mwb_get_value_woo_subs = get_option( 'mwb_crp_woo_subscriptions_enable', 'off' );
		if ( ! empty( $mwb_get_value_woo_subs ) && 'yes' === $mwb_get_value_woo_subs ) {
			$is_enable_woo_sub = true;
		}
		return $is_enable_woo_sub;
	}

	/**
	 * This function is used check is enable multiple apply coupons.
	 *
	 * @since 1.0.0
	 * @name mwb_crp_is_enable_subscription
	 */
	public function mwb_crp_is_enable_multiple_apply_coupons() {
		$is_enable_multiple_coupon   = false;
		$mwb_get_value_single_coupon = get_option( 'mwb_crp_apply_all_coupon_on_subscription', 'off' );
		if ( ! empty( $mwb_get_value_single_coupon ) && 'yes' === $mwb_get_value_single_coupon ) {
			$is_enable_multiple_coupon = true;
		}
		return $is_enable_multiple_coupon;
	}


	/**
	 * This function is used to apply button on the subscriptions
	 *
	 * @param object $subscription .
	 * @since 1.0.0
	 * @name mwb_crp_add_button_for_the_apply_coupon
	 */
	public function mwb_crp_add_button_for_the_apply_coupon( $subscription ) {
		if ( ! $this->mwb_crp_is_enable_multiple_apply_coupons() && $this->mwb_crp_is_enable_subscription() ) {
			?>
			<div id="mwb_crp_loader" class="mwb_crp_hide_element">
				<img src="<?php echo esc_html( COUPON_REFERRAL_PROGRAM_DIR_URL ); ?>public/images/loading.gif" alt="loading gif">
			</div>
			<a href="javascript:;" data-id="<?php echo esc_html( $subscription->get_id() ); ?>"class="button view mwb-coupon-view-btn mwb_crp_default"><?php echo esc_html_x( 'Apply Coupon', 'view a subscription', 'coupon-referral-program' ); ?></a>
			<?php
		}
	}

	/**
	 * This function is used to apply discount on the recurring payments
	 *
	 * @param object $renewal_order .
	 * @param object $subscription .
	 * @since 1.0.0
	 * @name mwb_crp_change_renewal_order_total
	 */
	public function mwb_crp_change_renewal_order_total( $renewal_order, $subscription ) {
		global $woocommerce;

		// Allow the discount for the first renewal only.
		if ( $this->wps_crp_discount_first_renewal_only() ) {
			$renewal_orders = $subscription->get_related_orders( 'ids', 'renewal' );
			if ( is_array( $renewal_orders ) && count( $renewal_orders ) > 1 ) {
				return $renewal_order;
			}
		}
		/*Get the renewal order total amount*/
		$order_total = $renewal_order->get_subtotal();
		$order_id    = $renewal_order->get_id();
		/*Get the referral purchase coupons*/
		$user_id                   = $subscription->get_user_id();
		$mwb_crp_total_earn        = 0;
		$referral_purchase_coupons = $this->get_referral_purchase_coupons( $user_id );
		/*Get the signup coupon*/
		$signup_coupon = $this->get_signup_coupon( $user_id );
		/*Check is subscription is enable*/
		if ( $this->mwb_crp_is_enable_multiple_apply_coupons() && $this->mwb_crp_is_enable_subscription() ) {

			/*Check is not empty signup coupon*/
			if ( ! empty( $signup_coupon['singup'] ) && is_array( $signup_coupon ) ) {
				$coupon = new WC_Coupon( $signup_coupon['singup'] );
				if ( 'publish' === get_post_status( $coupon ) && $this->mwb_crp_validate_coupon( $coupon ) ) {
					/*check is coupon is valid*/

					if ( $order_total >= $mwb_crp_total_earn ) {
						$renewal_order->apply_coupon( $coupon->get_code() );

						$applied_coupons = $renewal_order->get_items( 'coupon' );
						if ( $this->check_array_is_not_empty( $applied_coupons ) ) {
							foreach ( $applied_coupons as $item_id => $item ) {
								$coupon_obj = new WC_Coupon( $item->get_code() );
								if ( $coupon_obj->get_code() == $coupon->get_code() ) {
									$mwb_crp_total_earn = (float) $mwb_crp_total_earn + (float) $item->get_discount();
								}
							}
						}
						$this->mwb_decrease_coupon_count( $coupon->get_id() );
					}
				}
			}
			/*Check is not empty referral purchase coupon*/
			if ( ! empty( $referral_purchase_coupons ) && is_array( $referral_purchase_coupons ) ) {
				foreach ( $referral_purchase_coupons as $coupon_code => $mwb_user_id ) {
					$coupon = new WC_Coupon( $coupon_code );
					if ( 'publish' === get_post_status( $coupon ) && $this->mwb_crp_validate_coupon( $coupon ) ) {

						/*check is coupon is valid*/
						if ( $order_total >= $mwb_crp_total_earn ) {
							$renewal_order->apply_coupon( $coupon->get_code() );

							$applied_coupons = $renewal_order->get_items( 'coupon' );
							if ( $this->check_array_is_not_empty( $applied_coupons ) ) {
								foreach ( $applied_coupons as $item_id => $item ) {
									$coupon_obj = new WC_Coupon( $item->get_code() );
									if ( $coupon_obj->get_code() == $coupon->get_code() ) {
										$mwb_crp_total_earn = (float) $mwb_crp_total_earn + (float) $item->get_discount();
									}
								}
							}
							$this->mwb_decrease_coupon_count( $coupon->get_id() );
						}
					}
				}
				/*Update ultilization amount*/
				$this->update_utilize_coupon_amount( $user_id, $mwb_crp_total_earn );
				update_post_meta( $order_id, 'utilize_coupon_amount_updated', 'set' );

			}
		} elseif ( ! $this->mwb_crp_is_enable_multiple_apply_coupons() && $this->mwb_crp_is_enable_subscription() ) {

			$subscription_id = $subscription->get_id();

			$assinged_coupon = $subscription->get_meta( 'assinged_coupons' );

			if ( ! empty( $assinged_coupon ) && is_array( $assinged_coupon ) ) {
				foreach ( $assinged_coupon as $key => $coupon_code ) {
					$coupon = new WC_Coupon( $coupon_code );
					if ( 'publish' === get_post_status( $coupon ) && $this->mwb_crp_validate_coupon( $coupon ) ) {
						/*check is coupon is valid*/
						$renewal_order->apply_coupon( $coupon->get_code() );

						$applied_coupons = $renewal_order->get_items( 'coupon' );
						if ( $this->check_array_is_not_empty( $applied_coupons ) ) {
							foreach ( $applied_coupons as $item_id => $item ) {
								$coupon_obj = new WC_Coupon( $item->get_code() );
								if ( $coupon_obj->get_code() == $coupon->get_code() ) {
									$mwb_crp_total_earn = (float) $item->get_discount();
								}
							}
						}
						$this->mwb_decrease_coupon_count( $coupon->get_id() );
					}
					/*Update ultilization amount*/
					$this->update_utilize_coupon_amount( $user_id, $mwb_crp_total_earn );
					update_post_meta( $order_id, 'utilize_coupon_amount_updated', 'set' );
				}
			}
		}
		return $renewal_order;
	}

	/**
	 * This function is used for the decresing the coupon usages.
	 *
	 * @since 1.0.0
	 * @name mwb_decrease_coupon_count
	 * @param int $coupon_id Coupon id of the coupon.
	 */
	public function mwb_decrease_coupon_count( $coupon_id ) {
		$coupon_usage = (int) get_post_meta( $coupon_id, 'usage_count', true );
		if ( ! empty( $coupon_usage ) && $coupon_usage > 0 ) {
			$coupon_usage--;
			update_post_meta( $coupon_id, 'usage_count', $coupon_usage );
		}
		return true;
	}

	/**
	 * This function is used for get the html of the coupons.
	 *
	 * @since 1.0.0
	 * @name mwb_crp_change_renewal_order_total
	 */
	public function mwb_crp_coupons_popup() {
		check_ajax_referer( 'mwb-crp-verify-nonce', 'mwb_nonce' );

		$mwb_crp_html = '';
		if ( isset( $_POST['subscription_id'] ) && function_exists( 'wcs_get_subscription' ) ) {
			$subscription_id = sanitize_text_field( wp_unslash( $_POST['subscription_id'] ) );

			$subscription    = wcs_get_subscription( $subscription_id );
			$assinged_coupon = $subscription->get_meta( 'assinged_coupons' );

			$user_id      = $subscription->get_user_id();

			$referral_purchase_coupons = $this->get_referral_purchase_coupons( $user_id );

			/*Get the signup coupon*/
			$signup_coupon = $this->get_signup_coupon( $user_id );
			if ( ! empty( $signup_coupon['singup'] ) && is_array( $signup_coupon ) ) {
				$coupon = new WC_Coupon( $signup_coupon['singup'] );
				if ( 'publish' === get_post_status( $coupon ) && $coupon->is_valid() ) {
					/*check is coupon is valid*/
					$mwb_crp_html = $this->generate_html_for_coupons( $subscription_id, $coupon, $assinged_coupon );
				}
			}
			/*Check is not empty referral purchase coupon*/
			if ( ! empty( $referral_purchase_coupons ) && is_array( $referral_purchase_coupons ) ) {

				foreach ( $referral_purchase_coupons as $coupon_code => $user_id ) {
					$coupon = new WC_Coupon( $coupon_code );
					if ( 'publish' === get_post_status( $coupon ) && $coupon->is_valid() ) {
						/*check is coupon is valid*/
						$mwb_crp_html .= $this->generate_html_for_coupons( $subscription_id, $coupon, $assinged_coupon );
					}
				}
			}
			if ( '' != $mwb_crp_html ) {
				$response['html'] = $mwb_crp_html;
			} else {
				$mwb_crp_html     = '<h2>' . esc_html__( 'No available Coupons', 'coupon-referral-program' ) . '</h2>';
				$response['html'] = $mwb_crp_html;

			}
		}
		echo wp_json_encode( $response );
		wp_die();
	}

	/**
	 * This function is used for generating the html for the coupons.
	 *
	 * @since 1.0.0
	 * @param array  $subscription_id array of the subscription.
	 * @param object $coupon array of the coupon.
	 * @param int    $assinged_coupon id of the coupon.
	 * @name mwb_crp_change_renewal_order_total
	 */
	public function generate_html_for_coupons( $subscription_id, $coupon, $assinged_coupon ) {
		$mwb_crp_html      = '';
		$mwb_coupon_amount = ( 'fixed_cart' === $coupon->get_discount_type() ) ? wc_price( $coupon->get_amount() ) : $coupon->get_amount() . '%';
		$expiry_date       = $coupon->get_date_expires( 'edit' ) ? $coupon->get_date_expires( 'edit' )->date( wc_date_format() ) : __( 'No-Expiry', 'coupon-referral-program' );
		$mwb_crp_html     .=
		'<div class="mwb-coupon-popup-column">
	  			<div class="mwb-coupon-inner">
	  				<div class="mwb-coupon-code">' . strtoupper( $coupon->get_code() ) . '</div>
	  				<div class="mwb-coupon-value">' . $mwb_coupon_amount . '</div>
	  				<div class="mwb-coupon-ex-date">' . $expiry_date . '</div>
	  			</div>
	  			<div class="mwb-coupon-scissor"><img src="' . COUPON_REFERRAL_PROGRAM_DIR_URL . 'public/images/scissors.png" alt="scissors gif">';
		if ( empty( $assinged_coupon ) ) {
			$mwb_crp_html .= '</div>
	  		<div class="mwb-coupon-apply-btn"><a class="mwb_crp_apply_button" id="' . $coupon->get_id() . '"data-subscription = "' . $subscription_id . '"data-id="' . $coupon->get_id() . '" href="#">' . __( 'Apply', 'coupon-referral-program' ) . '</a></div>
	  		</div>';
		} elseif ( is_array( $assinged_coupon ) && in_array( $coupon->get_id(), $assinged_coupon ) ) {
			$mwb_crp_html .= '</div>
	  		<div class="mwb-coupon-apply-btn"><a class="mwb_crp_remove_button" id="' . $coupon->get_id() . '"data-subscription = "' . $subscription_id . '"data-id="' . $coupon->get_id() . '" href="#">' . __( 'Remove', 'coupon-referral-program' ) . '</a></div>
	  		</div>';
		} else {
			$mwb_crp_html .= '</div>
	  		<div class="mwb-coupon-apply-btn"><a class="mwb_crp_apply_button" id="' . $coupon->get_id() . '"data-subscription = "' . $subscription_id . '"data-id="' . $coupon->get_id() . '" href="#">' . __( 'Apply', 'coupon-referral-program' ) . '</a></div>
	  		</div>';
		}
		return $mwb_crp_html;
	}

	/**
	 * This function is used for apply coupons.
	 *
	 * @since 1.0.0
	 * @name mwb_crp_change_renewal_order_total.
	 */
	public function mwb_crp_coupon_apply() {
		check_ajax_referer( 'mwb-crp-verify-nonce', 'mwb_nonce' );
		if ( isset( $_POST['subscription_id'] ) && isset( $_POST['coupon_id'] ) && function_exists( 'wcs_get_subscription' ) ) {
			$subscription_id = sanitize_text_field( wp_unslash( $_POST['subscription_id'] ) );
			$coupon_id       = sanitize_text_field( wp_unslash( $_POST['coupon_id'] ) );
			$mwb_array       = array();
			$subscription    = wcs_get_subscription( $subscription_id );
			$assinged_coupon = $subscription->get_meta( 'assinged_coupons' );
			if ( empty( $assinged_coupon ) ) {
				$mwb_array[] = $coupon_id;
				$subscription->update_meta_data( 'assinged_coupons', $mwb_array );
				$subscription->save();
			} elseif ( ! empty( $assinged_coupon ) && is_array( $assinged_coupon ) && ! in_array( $coupon_id, $assinged_coupon ) ) {
				$assinged_coupon[] = $coupon_id;
				$subscription->update_meta_data( 'assinged_coupons', $assinged_coupon );
				$subscription->save();
			}
			$response = true;
		}
		echo wp_json_encode( $response );
		wp_die();
	}

	/**
	 * This function is used for removing applied coupons.
	 *
	 * @since 1.0.0
	 * @name mwb_crp_change_renewal_order_total.
	 */
	public function mwb_crp_coupon_remove() {
		check_ajax_referer( 'mwb-crp-verify-nonce', 'mwb_nonce' );
		if ( isset( $_POST['subscription_id'] ) && isset( $_POST['coupon_id'] ) && function_exists( 'wcs_get_subscription' ) ) {
			$subscription_id = sanitize_text_field( wp_unslash( $_POST['subscription_id'] ) );
			$coupon_id       = sanitize_text_field( wp_unslash( $_POST['coupon_id'] ) );
			
			$subscription    = wcs_get_subscription( $subscription_id );
			
			$assinged_coupon = $subscription->get_meta( 'assinged_coupons' );
			if ( ! empty( $assinged_coupon ) && is_array( $assinged_coupon ) ) {
				foreach ( $assinged_coupon as $key => $value ) {
					if ( $coupon_id == $value ) {
						unset( $assinged_coupon[ $key ] );
					}
				}
				$subscription->update_meta_data( 'assinged_coupons', $assinged_coupon );
				$subscription->save();
			}
			$response = true;
		}
		echo wp_json_encode( $response );
		wp_die();
	}

	/**
	 * This function is used to add the button on the subscription details page.
	 *
	 * @name mwb_crp_add_button_order_details_page
	 * @param object $subscription Array of the subscription.
	 * @since 1.3.4
	 */
	public function mwb_crp_add_button_order_details_page( $subscription ) {
		if ( ! $this->mwb_crp_is_enable_multiple_apply_coupons() && $this->mwb_crp_is_enable_subscription() ) {
			?>
			<div id="mwb_crp_loader" class="mwb_crp_hide_element">
				<img src="<?php echo esc_url( COUPON_REFERRAL_PROGRAM_DIR_URL ); ?>public/images/loading.gif" alt="loading gif">
			</div>
			<a href="javascript:;" data-id="<?php echo esc_html( $subscription->get_id() ); ?>"class="button view mwb-coupon-view-btn mwb_crp_default"><?php echo esc_html_x( 'Apply Coupon', 'view a subscription', 'coupon-referral-program' ); ?></a>
			<?php
		}
	}

	/**
	 * Check whether the Reffree Signup  Feature is enable
	 *
	 * @since 1.4.3
	 * @return bool value
	 */
	public function check_reffre_signup_is_enable() {
		$is_enable              = false;
		$mwb_cpr_referre_enable = get_option( 'mwb_crp_refree_discount_enable', ' ' );
		if ( isset( $mwb_cpr_referre_enable ) && 'yes' === $mwb_cpr_referre_enable ) {

			$is_enable = true;
		}
		return $is_enable;
	}

	/**
	 * This function determine the which type of the coupon will be generated during the refferal signup.
	 *
	 * @name mwb_crp_get_discount_signup_type_refree
	 * @since 1.4.3
	 * @return Discount Type:Whether Fixed or Percentage.
	 */
	public function mwb_crp_get_discount_signup_type_refree() {
		$mwb_cpr_discount_type_signup = get_option( 'referee_discount_type', 'mwb_cpr_fixed' );
		return $mwb_cpr_discount_type_signup;
	}
	/**
	 * Get the Refree Signup Discount Amount along with HTML format
	 *
	 * @since 1.0.0
	 * @return html with Signup Disocunt amount
	 */
	public function get_refree_signup_discount_html() {
		$refree_signup_discount_type  = $this->mwb_crp_get_discount_signup_type_refree();
		$refree_signup_discount_value = get_option( 'refree_discount_value', '' );
		if ( empty( $refree_signup_discount_value ) ) {
			$refree_signup_discount_value = 1;
		}
		if ( 'mwb_cpr_fixed' === $refree_signup_discount_type ) {
			return wc_price( $refree_signup_discount_value );
		} else {
			return $refree_signup_discount_value . '%';
		}
	}

	/**
	 * Check whether the Referal purchase Feature is enable
	 *
	 * @since 1.4.3
	 * @return bool value
	 */
	public function check_referl_purchase_is_enable() {
		$is_enable                       = false;
		$mwb_crp_enable_referal_purchase = get_option( 'mwb_crp_enable_referal_purchase', 'yes' );
		if ( isset( $mwb_crp_enable_referal_purchase ) && 'yes' === $mwb_crp_enable_referal_purchase ) {
			$is_enable = true;
		}
		return $is_enable;
	}

	/**
	 * Get exclude sales item enable.
	 *
	 * @since 1.5.0
	 */
	public function mwb_crp_exclude_sales_item() {
		$mwb_crp_exclude_sales = get_option( 'mwb_crp_exclude_sales', 'no' );
		return $mwb_crp_exclude_sales;
	}

	/**
	 * Get minimum spend for coupon.
	 *
	 * @since 1.5.0
	 */
	public function mwb_crp_get_min_spend() {
		$mwb_crp_coupon_min_val = get_option( 'mwb_crp_coupon_min_val', '' );
		return $mwb_crp_coupon_min_val;
	}

	/**
	 * Get maximum spend for coupon.
	 *
	 * @since 1.5.0
	 */
	public function mwb_crp_get_max_spend() {
		$mwb_crp_coupon_max_val = get_option( 'mwb_crp_coupon_max_val', '' );
		return $mwb_crp_coupon_max_val;
	}

	/**
	 * Get include product.
	 *
	 * @since 1.5.0
	 */
	public function mwb_crp_get_include_product() {
		$mwb_crp_include_pro = get_option( 'mwb_crp_include_pro', '' );
		return $mwb_crp_include_pro;
	}

	/**
	 * Get exclude product.
	 *
	 * @since 1.5.0
	 */
	public function mwb_crp_get_exclude_pro() {
		$mwb_crp_exclude_pro = get_option( 'mwb_crp_exclude_pro', '' );
		return $mwb_crp_exclude_pro;
	}

	/**
	 * Validate coupon.
	 *
	 * @param object $coupon .
	 * @since 1.5.0
	 */
	public function mwb_crp_validate_coupon( $coupon ) {
		$valid     = false;
		$coupon_id = $coupon->get_id();
		if ( isset( $coupon_id ) && ! empty( $coupon_id ) ) {
			$coupon_usage_count = get_post_meta( $coupon_id, 'usage_count', true );
			$coupon_usage_limit = get_post_meta( $coupon_id, 'usage_limit', true );
			if ( ( $coupon->get_date_expires() && time() > $coupon->get_date_expires()->getTimestamp() ) ) {
				return false;
			}
			if ( empty( $coupon_usage_limit ) || $coupon_usage_limit > $coupon_usage_count ) {

				return true;
			}
		} else {
			$valid = false;
		}
		return $valid;
	}

	/**
	 * This function is used to send the Referal link via Email.
	 * * @since 1.6.0
	 */
	public function mwb_crp_send_referal_link_mail() {
		check_ajax_referer( 'mwb-crp-verify-nonce', 'mwb_nonce' );
		$reponse['result'] = false;
		$reponse['msg']    = __( 'Mail not sent, due to some error!', 'coupon-referral-program' );
		$emal_ids          = isset( $_POST['email'] ) ? map_deep( wp_unslash( $_POST['email'] ), 'sanitize_text_field' ) : '';

		$user_id            = get_current_user_id();
		$user_reference_key = get_user_meta( $user_id, 'referral_key', true );
		/**
		 * Filter referral site url.
		 *
		 * @since 1.6.4
		 * @param string site url() .
		 */
		$page_permalink     = apply_filters( 'mwb_crp_referral_link_url', site_url() );
		$referral_link      = $page_permalink . '?ref=' . $user_reference_key;
		if ( is_array( $emal_ids ) && ! empty( $emal_ids ) ) {
			foreach( $emal_ids as $emal_id ) {
				if ( filter_var( $emal_id, FILTER_VALIDATE_EMAIL ) ) {
					if ( isset( $user_id ) && ! empty( $user_id ) ) {
						$customer_email     = WC()->mailer()->emails['crp_share_via_email'];
						$email_status       = $customer_email->trigger( $user_id, $referral_link, $emal_id, $user_reference_key );
						$reponse['result']  = true;
						$reponse['msg']     = __( 'Mail send successfully', 'coupon-referral-program' );
					}
				} 
			}
		} else {
			$reponse['msg'] = __( 'Invalid Emails', 'coupon-referral-program' );
		}

		wp_send_json( $reponse );
	}


	/**
	 * This function is used create coupon code for referal on MY ACCOUNT PAGE.
	 *
	 * @name mwb_crp_generate_referral_coupon_callback
	 * @since 1.0.0
	 * @param string $coupon_code coupon code .
	 * @param int    $user_id user id .
	 */
	public function mwb_crp_generate_referral_coupon_callback( $coupon_code, $user_id ) {

		$woo_ver       = WC()->version;
		$amount        = get_option( 'mwb_crp_referral_user_coupon_amount', 1 );
		$discount_type = get_option( 'mwb_crp_user_referral_coupon_type', 'mwb_cpr_referral_user_coupon_percent' );
		if ( 'mwb_cpr_referral_user_coupon_fixed' === $discount_type ) {
			$discount_type = 'fixed_cart';
		} else {
			$discount_type = 'percent';
		}
		$coupon_description = 'Referral Coupon';
		$coupon_description = $coupon_description . " #$user_id";
		$coupon             = array(
			'post_title'   => $coupon_code,
			'post_content' => $coupon_description,
			'post_excerpt' => $coupon_description,
			'post_status'  => 'publish',
			'post_author'  => get_current_user_id(),
			'post_type'    => 'shop_coupon',
		);
		$new_coupon_id      = wp_insert_post( $coupon );
		// Added since woocommerce 3.9.0 .
		if ( $new_coupon_id ) {
			$coupon_obj = new WC_Coupon( $new_coupon_id );
			$coupon_obj->save();
		}
		update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
		update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
		update_post_meta( $new_coupon_id, 'usage_limit_per_user', 1 );
		update_post_meta( $new_coupon_id, 'mwb_crp_coupon_user_id', $user_id );
		update_user_meta( $user_id, 'mwb_crp_previous_key', $coupon_code );

		$apply_coupon_config = get_option( 'mwb_crp_referal_code_restriction' );
		if ( 'yes' === $apply_coupon_config  ) {
			$coupon_individual           = get_option( 'coupon_individual', 'no' );
			$coupon_freeshipping         = get_option( 'coupon_freeshipping', 'no' );
			$mwb_crp_sales_item          = $this->mwb_crp_exclude_sales_item();

			$coupon_expiry = get_option( 'coupon_expiry', '' );
			$todaydate     = date_i18n( 'Y-m-d' );
			if ( 0 < $coupon_expiry || 0 === $coupon_expiry ) {
				$expirydate_save = date_i18n( 'Y-m-d', strtotime( "$todaydate +$coupon_expiry day" ) );
			} else {
				$expirydate_save = '';
			}

			$coupon_usage                = get_option( 'coupon_usage', '' );
			$mwb_crp_get_min_spend       = $this->mwb_crp_get_min_spend();
			$mwb_crp_get_max_spend       = $this->mwb_crp_get_max_spend();
			$mwb_crp_get_include_product = $this->mwb_crp_get_include_product();
			if ( isset( $mwb_crp_get_include_product ) && ! empty( $mwb_crp_get_include_product ) ) {
				$mwb_crp_get_include_product = implode( ',', $mwb_crp_get_include_product );
			}
			$mwb_crp_get_exclude_pro = $this->mwb_crp_get_exclude_pro();
			if ( isset( $mwb_crp_get_exclude_pro ) && ! empty( $mwb_crp_get_exclude_pro ) ) {
				$mwb_crp_get_exclude_pro = implode( ',', $mwb_crp_get_exclude_pro );
			}


			update_post_meta( $new_coupon_id, 'individual_use', $coupon_individual );
			update_post_meta( $new_coupon_id, 'free_shipping', $coupon_freeshipping );
			update_post_meta( $new_coupon_id, 'exclude_sale_items', $mwb_crp_sales_item );
			update_post_meta( $new_coupon_id, 'date_expires', $expirydate_save );
			update_post_meta( $new_coupon_id, 'usage_limit', $coupon_usage );
			update_post_meta( $new_coupon_id, 'minimum_amount', $mwb_crp_get_min_spend );
			update_post_meta( $new_coupon_id, 'maximum_amount', $mwb_crp_get_max_spend );

			if ( isset( $mwb_crp_get_include_product ) && ! empty( $mwb_crp_get_include_product ) ) {

			update_post_meta( $new_coupon_id, 'product_ids', $mwb_crp_get_include_product );
			}
			if ( isset( $mwb_crp_get_exclude_pro ) && ! empty( $mwb_crp_get_exclude_pro ) ) {

			update_post_meta( $new_coupon_id, 'exclude_product_ids', $mwb_crp_get_exclude_pro );
			}

			// add exlucde/include categories data

			$wps_crp_get_include_cat = $this->wps_crp_get_include_cat();

			$wps_crp_get_exclude_cat = $this->wps_crp_get_exclude_cat();

			if ( isset( $wps_crp_get_include_cat ) && ! empty( $wps_crp_get_include_cat ) ) {

				update_post_meta( $new_coupon_id, 'product_categories', $wps_crp_get_include_cat );
			}
			if ( isset( $wps_crp_get_exclude_cat ) && ! empty( $wps_crp_get_exclude_cat ) ) {

				update_post_meta( $new_coupon_id, 'exclude_product_categories', $wps_crp_get_exclude_cat );
			}
		}
		return $coupon_code;
	}

	/**
	 * This function is used create coupon code for referal on MY ACCOUNT PAGE.
	 *
	 * @name mwb_crp_get_referrl_code
	 * @since 1.0.0
	 * @param int $user_id user id.
	 */
	public function mwb_crp_get_referrl_code( $user_id ) {
		if ( self::mwb_crp_points_rewards_hide_referal() ) {
			return;
		}
		$enable = get_option( 'mwb_crp_referal_via_code', false );
		if ( isset( $enable ) && 'yes' === $enable ) {
			$referral_code = '';
			$referral_key  = get_user_meta( $user_id, 'referral_key', true );
			if ( empty( $referral_key ) ) {
				$this->get_referral_link( $user_id );
				$referral_key  = get_user_meta( $user_id, 'referral_key', true );
			}
			if ( isset( $referral_key ) && ! empty( $referral_key ) ) {
				$coupon = new WC_Coupon( $referral_key );
				if ( isset( $coupon ) && ! empty( $coupon ) ) {
					$coupon_id = $coupon->get_id();
					if ( isset( $coupon_id ) && ! empty( $coupon_id ) ) {
						$coupon_user_id = get_post_meta( $coupon_id, 'mwb_crp_coupon_user_id', true );
						if ( $user_id == $coupon_user_id ) {
							$referral_code = $referral_key;
						}
					} else {
						$referral_code = $this->mwb_crp_generate_referral_coupon_callback( $referral_key, $user_id );
					}
				}
			}
			if ( isset( $referral_code ) && ! empty( $referral_code ) ) {
				?>
			<div class="mwb_crp_referal_code_wrap">
				<div class="mwb_cpr_logged_wrapper">
					<span class="mwb_crp_addon_referral_code"><?php esc_html_e( 'Referral Code: ', 'coupon-referral-program' ); ?></span>
					<div class="mwb_cpr_refrral_code_copy">
						<p id="mwb_cpr_referal_code_copy">
							<code id="mwb_cpr_referal_copyy_code" class="mwb_crp_referl_code"><?php echo esc_html( $referral_code ); ?></code>
							<span class="mwb_cpr_copy_btn_wrap">
								<button class="mwb_cpr_btn_copy mwb_tooltip" data-clipboard-target="#mwb_cpr_referal_copyy_code" aria-label="copied">
								<span class="mwb_tooltiptext"><?php esc_html_e( 'Copy', 'coupon-referral-program' ); ?></span>
								<span class="mwb_tooltiptext_copied mwb_tooltiptext"><?php esc_html_e( 'Copied', 'coupon-referral-program' ); ?></span>
								<img src="<?php echo esc_html( COUPON_REFERRAL_PROGRAM_DIR_URL ) . 'admin/images/copy.png'; ?>" alt="copy icon">
								</button>
							</span>
						</p>
					</div>
					<div class="clear">
					</div>
					<small><?php esc_html_e( 'Your friend can use this referral code as discount coupon', 'coupon-referral-program' ); ?></small>
				</div>
			</div>
				<?php
			}
		}
	}

	/**
	 * This function is check referal code enable.
	 *
	 * @name check_share_vai_referal_code
	 * @since 1.0.0
	 */
	public function check_share_vai_referal_code() {
		$enable = get_option( 'mwb_crp_referal_via_code', false );
		if ( isset( $enable ) && 'yes' === $enable ) {
			$enable = true;
		} else {
			$enable = false;
		}
		return $enable;
	}

	/**
	 * This function is used to give discount coupon on referal via code.
	 *
	 * @param object $order .
	 * @name mwb_crp_referal_purchase_discount_by_referal_coupon
	 * @since 1.6.0
	 */
	public function mwb_crp_referal_purchase_discount_by_referal_coupon( $order ) {
		if ( ! empty( $order ) ) {
			$order_id    = $order->get_id();
			$used_cuopon = $order->get_coupon_codes();
			if ( isset( $used_cuopon ) && ! empty( $used_cuopon ) && is_array( $used_cuopon ) ) {
				foreach ( $used_cuopon as $coupon_code ) {
					$coupon_obj = new WC_Coupon( $coupon_code );
					$coupon_id  = $coupon_obj->get_id();
					if ( isset( $coupon_id ) && ! empty( $coupon_id ) ) {
						$refree_id = get_post_meta( $coupon_id, 'mwb_crp_coupon_user_id', true );
						if ( isset( $refree_id ) && ! empty( $refree_id ) ) {
							$user_id = absint( $order->get_user_id() );
							if ( empty( $user_id ) ) {
								if ( ! self::check_is_points_rewards_enable() ) {
									$order_total                = $order->get_total();
									$referral_discount_on_order = $this->get_referral_discount_order( $user_id );
									$mwb_cpr_coupon_amount      = $this->get_referral_coupon_amount( $referral_discount_on_order, $order_total );
									$mwb_cpr_coupon_length      = $this->mwb_get_coupon_length();
									$mwb_cpr_code               = $this->mwb_cpr_coupon_generator( $mwb_cpr_coupon_length );
									$mwb_cpr_coupon_expiry      = $this->mwb_get_coupon_expiry();
									$mwb_cpr_discount_type      = $this->mwb_get_discount_type();
									$coupon_amount_with_css     = $this->mwb_formatted_amount( $mwb_cpr_coupon_amount, $mwb_cpr_discount_type );
									$expirydate                 = $this->mwb_expiry_date_saved( $mwb_cpr_coupon_expiry );
									$refree                     = get_user_by( 'ID', $refree_id );
									$refree_email               = $this->get_user_email( $refree );
									/*======= Create the Woocommerce Coupons  =======*/
									$coupon_description = 'Coupon on Order for OrderID';
									if ( $this->wps_referral_discount_management('referal_purchase', $refree_id ) ) {
										if ( $this->mwb_cpr_create_coupons( $mwb_cpr_code, $mwb_cpr_coupon_amount, $order_id, $mwb_cpr_discount_type, $expirydate, $coupon_description, $refree_email ) ) {
	
											/* === Send the Email to the relevant customer === */
	
											$customer_email = WC()->mailer()->emails['crp_order_email'];
											if ( empty( $expirydate ) ) {
												$expirydate = __( 'No Expiry', 'coupon-referral-program' );
											}
											$email_status  = $customer_email->trigger( $refree_id, $mwb_cpr_code, $coupon_amount_with_css, $expirydate );
											$billing_email = $order->get_billing_email();
											update_post_meta( $order_id, 'referral_has_rewarded', $refree_id );
											$this->save_referral_coupon_code_on_guest( $mwb_cpr_code, $refree_id, $billing_email );
										}
									}
								}
							} else {
								$alredy_referred = get_user_meta( $user_id, 'mwb_cpr_user_referred_by', true );
								if ( empty( $alredy_referred ) ) {
									$already_placed_orders = $this->get_number_of_orders_placed( $user_id );
									if ( 1 == $already_placed_orders ) {
										update_user_meta( $user_id, 'mwb_cpr_user_referred_by', $refree_id );
									}
								}
							}
						}
					}
				}
			}
		}
	}

	/**
	 * This function is used to save referal coupon on guest user purchase.
	 *
	 * @param string $mwb_cpr_code .
	 * @param int    $refree_id .
	 * @param string $email .
	 * @name save_referral_coupon_code_on_guest
	 * @since 1.6.0
	 */
	public function save_referral_coupon_code_on_guest( $mwb_cpr_code, $refree_id, $email ) {
		$mwb_crp_referral_purchase_array = array();
		$mwb_crp_referral_purchase       = get_user_meta( $refree_id, 'mwb_crp_referral_purchase_coupons_on_guest', true );
		$coupon                          = new WC_Coupon( $mwb_cpr_code );
		$mwb_cpr_code                    = $coupon->get_id();
		if ( ! empty( $mwb_crp_referral_purchase ) ) {
			$mwb_crp_referral_purchase[ $mwb_cpr_code ] = $email;
			update_user_meta( $refree_id, 'mwb_crp_referral_purchase_coupons_on_guest', $mwb_crp_referral_purchase );
		} else {
			$mwb_crp_referral_purchase_array[ $mwb_cpr_code ] = $email;
			update_user_meta( $refree_id, 'mwb_crp_referral_purchase_coupons_on_guest', $mwb_crp_referral_purchase_array );
		}
	}

	/**
	 * This function is used to get referal coupon on guest user purchase.
	 *
	 * @param int $user_id .
	 * @name get_referral_purchase_coupons_on_guest
	 * @since 1.6.0
	 */
	public function get_referral_purchase_coupons_on_guest( $user_id ) {
		$mwb_crp_referral_purchase = get_user_meta( $user_id, 'mwb_crp_referral_purchase_coupons_on_guest', true );
		if ( empty( $mwb_crp_referral_purchase ) ) {
			$mwb_crp_referral_purchase = array();
		}
		return $mwb_crp_referral_purchase;

	}

	/**
	 * This function is used to display referal code on popup.
	 *
	 * @param int $user_id .
	 * @name get_referral_code_on_popup
	 * @since 1.6.0
	 */
	public function get_referral_code_on_popup( $user_id ) {
		if ( $this->check_share_vai_referal_code() ) {
			$referal_code = '';
			$referral_key = get_user_meta( $user_id, 'referral_key', true );
			if ( isset( $referral_key ) && ! empty( $referral_key ) ) {
				$coupon = new WC_Coupon( $referral_key );
				if ( isset( $coupon ) && ! empty( $coupon ) ) {
					$coupon_id = $coupon->get_id();
					if ( isset( $coupon_id ) && ! empty( $coupon_id ) ) {
						$coupon_user_id = get_post_meta( $coupon_id, 'mwb_crp_coupon_user_id', true );
						if ( $user_id == $coupon_user_id ) {
							$referal_code = $referral_key;
						}
					}
				}
			}
			if ( empty( $referal_code ) ) {
				return;
			}
			?>
			<h6><?php esc_html_e( 'Referral Code: ', 'coupon-referral-program' ); ?></h6>
			<div class="mwb_cpr_refrral_code_copy">
				<p id="mwb_cpr_copy">
					<code id="mwb_cpr_copyy_code" style="background-color: <?php echo wp_kses_post( Coupon_Referral_Program_Admin::get_selected_color() ); ?>">
					<?php echo esc_html( $referal_code ); ?>
					</code>
					<span class="mwb_cpr_copy_btn_wrap">
					<button class="mwb_cpr_btn_copy mwb_tooltip" data-clipboard-target="#mwb_cpr_copyy_code" aria-label="copied">
						<span class="mwb_tooltiptext"><?php esc_html_e( 'Copy', 'coupon-referral-program' ); ?></span>
						<span class="mwb_tooltiptext_copied mwb_tooltiptext"><?php esc_html_e( 'Copied', 'coupon-referral-program' ); ?></span>
						<img src="<?php echo esc_html( COUPON_REFERRAL_PROGRAM_DIR_URL ) . 'admin/images/copy.png'; ?>" alt="copy icon">
					</button>
					</span>
				</p>
			</div>
			<?php
		}
	}

	/**
	 * Check is points and rewards hide referal checkbox is enable
	 *
	 * @since 1.0.0
	 */
	public static function mwb_crp_points_rewards_hide_referal() {
		$mwb_is_enable             = false;
		$mwb_points_rewards_enable = get_option( 'mwb_crp_points_rewards_hide_referal', false );
		if ( ! empty( $mwb_points_rewards_enable ) && 'yes' === $mwb_points_rewards_enable ) {
			$mwb_is_enable = true;
		}
		if ( ! is_plugin_active( 'woocommerce-points-and-rewards/woocommerce-points-and-rewards.php' ) ) {
			$mwb_is_enable = false;
		}
		return $mwb_is_enable;
	}

	/**
	 * This function is used to apply auto coupon for refered user.
	 *
	 * @param bool   $valid .
	 * @param object $coupon .
	 * @param object $obj .
	 * @throws \Exception Throw the exception.
	 * @name mwb_crp_woocommerce_coupon_is_valid
	 * @since 1.6.0
	 */
	public function mwb_crp_woocommerce_coupon_is_valid( $valid, $coupon, $obj ) {
		$coupon_id = $coupon->get_id();
		$user_id   = get_current_user_id();
		if ( empty( $user_id ) ) {
			return $valid;
		}
		$coupon_user_id = get_post_meta( $coupon_id, 'mwb_crp_coupon_user_id', true );
		if ( isset( $coupon_id ) && $user_id == $coupon_user_id ) {
			throw new Exception( esc_html__( 'Referral code cannot be used by self', 'coupon-referral-program' ), 100 );
		}
		return $valid;
	}

	/**
	 * This function is used to get coupon expiry date.
	 *
	 * @param object $coupon .
	 * @name mwb_crp_get_transalted_coupon_exp_date
	 * @since 1.6.0
	 */
	public function mwb_crp_get_transalted_coupon_exp_date( $coupon ) {
		$expiry_date = $coupon->get_date_expires();
		if ( $expiry_date ) {
			$date_format = get_option( 'date_format', 'Y-m-d' );
			$expiry_date = $expiry_date->date_i18n( $date_format );
		} else {
			$expiry_date = __( 'Never', 'coupon-referral-program' );
		}
		return $expiry_date;
	}



	/**
	 * This function is used to get coupon creation date.
	 *
	 * @param object $coupon .
	 * @name mwb_crp_get_transalted_coupon_created_date
	 * @since 1.6.0
	 */
	public function mwb_crp_get_transalted_coupon_created_date( $coupon ) {
		$created_date = $coupon->get_date_created();
		if ( $created_date ) {
			$date_format  = get_option( 'date_format', 'Y-m-d' );
			$created_date = $created_date->date_i18n( $date_format );
		} else {
			$created_date = __( '---', 'coupon-referral-program' );
		}

		return $created_date;
	}
	/**
	 * Get total referred users
	 *
	 * @param string $referral_key .
	 * @return $referral_count
	 */
	public function mwb_crp_get_total_referred_users( $referral_key ) {
		$referral_count     = 0;
		$args               = array(
			'limit'  => - 1,
			'fields' => array( 'ID' ),
		);
		$args['meta_query'] = array(
			array(
				'key'     => 'referral_key',
				'value'   => trim( $referral_key ),
				'compare' => '==',
			),
		);
		$referral_user_data = get_users( $args );
		$refree_id          = $referral_user_data[0]->ID;
		if ( ! empty( $refree_id ) ) {
			$args               = array(
				'limit'  => - 1,
				'fields' => array( 'ID' ),
			);
			$args['meta_query'] = array(
				array(
					'key'     => 'mwb_cpr_user_referred_by',
					'value'   => $refree_id,
					'compare' => '==',
				),
			);
			$referral_user_data = get_users( $args );
			if ( ! empty( $referral_user_data ) && is_array( $referral_user_data ) ) {
				$referral_count = count( $referral_user_data );
			}
		}
		return $referral_count;
	}

	/**
	 * Display referral code using shortcode.
	 *
	 * @since    1.6.5
	 */
	public function mwb_crp_referral_code_shortcode() {
		if ( $this->check_share_vai_referal_code() && is_user_logged_in() ) {
			$user_ID      = get_current_user_ID();
			$referral_key = get_user_meta( $user_ID, 'referral_key', true );
			if ( empty( $referral_key ) ) {
				$referral_key = $this->set_referral_key( $user_ID );
			}
			$mwb_crp_code_html = '<fieldset><code>' . $referral_key . '</code></fieldset>';
			return $mwb_crp_code_html;
		}
	}

	/**
	 * Used the prevent user for an specific domains.
	 *
	 * @since 1.6.5
	 *
	 * @param object $errors .
	 * @param mixed  $sanitized_user_login .
	 * @param string $user_email .
	 */
	public function mwb_crp_prevent_user_resgiration( $errors, $sanitized_user_login, $user_email ) {
		$mwb_crp_saved_domains = get_option( 'mwb_crp_email_domains', false );
		if ( $this->check_prevent_fraudlent_is_enable() && ( $this->check_signup_is_enable() || $this->check_reffral_signup_is_enable() ) && ! empty( $mwb_crp_saved_domains ) && ! empty( $user_email ) ) {
			$user_email_split      = explode( '@', $user_email )[1];
			$mwb_crp_saved_domains = explode( ',', $mwb_crp_saved_domains );
			if ( ! in_array( $user_email_split, $mwb_crp_saved_domains, true ) ) {
				$errors->add( 'blacklist_error', 'This email domain is not allowed to register on this site.' );
			}
		}
		return $errors;

	}

	/**
	 * Check whether the Prevent Fraudulent Feature is enable
	 *
	 * @since 1.0.0
	 * @return bool value
	 */
	public function check_prevent_fraudlent_is_enable() {
		$enable        = false;
		$enable_signup = get_option( 'mwb_crp_email_domains_enable', false );
		if ( ! empty( $enable_signup ) && 'yes' === $enable_signup ) {
			$enable = true;
		}
		return $enable;
	}

	/** Add referral tab shortcode */
	public function mwb_crp_referral_dashboard_shortcode() {
		return include_once COUPON_REFERRAL_PROGRAM_DIR_PATH . 'public/partials/coupon-referral-program-public-display-shortcode.php';
	}

	/**
	 * Add a custom Product tab for refer
	 *
	 * @param array() $tabs .
	 * @return $tabs
	 */
	public function wps_crp_add_custom_tabs( $tabs ) {
		$tabs['refer_a_friend'] = array(
			'title'    => esc_attr__( 'Refer A Friend', 'coupon-referral-program' ),
			'callback' => array( $this, 'wps_crp_refer_a_freind_template_callback' ),
			'priority' => 50,
		);
		return $tabs;
	}


	/**
	 * Shows the refer a freind content/
	 *
	 * @param string $slug .
	 * @param array  $tab .
	 * @return void
	 */
	public function wps_crp_refer_a_freind_template_callback( $slug, $tab ) {
		include_once COUPON_REFERRAL_PROGRAM_DIR_PATH . 'public/partials/coupon-referral-program-public-referal-sharing-section.php';
	}

	/**
	 *  Allow the nth singup discount for referee.
	 * 
	 * @param string $referee_key .
	 */
	public function wps_crp_allow_referee_signup_discount( $referee_key ) {
		$nth_signup_discount = get_option( 'nth_signup_discount_value' );
		$allow = false;
		if ( ! empty( $nth_signup_discount ) ) {
			$total_referred_user = $this->mwb_crp_get_total_referred_users( $referee_key );
			$reminder = $total_referred_user % $nth_signup_discount;
			if ( 0 === $reminder && ! empty( $total_referred_user ) ) {			
				$allow = true;
			}
		} else {
			$allow = true;
		}
		return $allow;
	}

	/**
	 * This function check minimum order total limit.
	 * 
	 * @param object $order order object .
	 */
	public function wps_crp_minimum_order_total_limit( $order ) {
		$minimum_referred_order_total = get_option( 'mwb_crp_min_order_limit_referred_users', 0 );
		if ( ! empty( $minimum_referred_order_total ) ) {
			if ( ! empty( $minimum_referred_order_total ) && $order->get_total() < $minimum_referred_order_total ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Allow to apply discount for first renewal order only. 
	 */
	public function wps_crp_discount_first_renewal_only() {
		$first_discount = get_option( 'mwb_crp_woo_subscriptions_discount_first_renewal', 'no' );
		if ( 'yes' === $first_discount ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Give the coupon discount when the referred customer paid after the free trial ended.
	 * 
	 * @param array $renewal_order .
	 * @param object $subscription .
	 */
	public function wps_crp_discount_free_trial_ended( $renewal_order, $subscription ) {

		$enable = get_option( 'mwb_crp_discount_free_trial_ended' );
		if ( 'yes' === $enable ) {
			$user_id          = $subscription->get_user_id();
			$referred_user_id = get_user_meta( $user_id, 'mwb_cpr_user_referred_by', true );
			$parent_id        = $subscription->get_parent_id();
			
			if ( $parent_id && $referred_user_id ) {
				$parent_order = wc_get_order( $parent_id );
				$order_total  = (float) $parent_order->get_total();
				if ( empty( $order_total ) ) {
					$get_renewal_orders = $subscription->get_related_orders();
					if ( is_array( $get_renewal_orders ) && count( $get_renewal_orders ) == 2 ) {
						$coupon_amount = get_option( 'wps_crp_free_trial_coupon_amount' ) ? get_option( 'wps_crp_free_trial_coupon_amount' ) : 1;
						$coupon_type   = get_option( 'wps_crp_free_trial_coupon_type' );

						$user                  = get_user_by( 'ID', $user_id );
						$user_email            = $this->get_user_email( $user );
						$coupon_description    = 'Trial ended discount for subscription  #' . $subscription->get_id();
						$mwb_cpr_coupon_length = $this->mwb_get_coupon_length();
						$mwb_cpr_coupon_expiry = $this->mwb_get_coupon_expiry();
						$expirydate            = $this->mwb_expiry_date_saved( $mwb_cpr_coupon_expiry );
						$mwb_cpr_code          = $this->mwb_cpr_coupon_generator( $mwb_cpr_coupon_length );

						$this->mwb_cpr_create_coupons( $mwb_cpr_code, $coupon_amount, $user_id, $coupon_type, $expirydate, $coupon_description, $user_email );

						$mwb_crp_referral_trial_ended_array = array();
						$mwb_crp_trial_ended_coupon       = get_user_meta( $user_id, 'wps_crp_referral_trial_ended_coupon', true );
						if ( ! empty( $mwb_crp_trial_ended_coupon ) ) {
							$mwb_crp_trial_ended_coupon[ $mwb_cpr_code ] = $subscription->get_id();
							update_user_meta( $user_id, 'wps_crp_referral_trial_ended_coupon', $mwb_crp_trial_ended_coupon );
						} else {
							$mwb_crp_referral_trial_ended_array[ $mwb_cpr_code ] = $subscription->get_id();
							update_user_meta( $user_id, 'wps_crp_referral_trial_ended_coupon', $mwb_crp_referral_trial_ended_array );
						}
					}
				}
			}
		}
		return $renewal_order;
	}

	/**
	 * Get paid referred customer trial ended coupons
	 *
	 * @name get_signup_coupon
	 * @since 1.6.9
	 * @param int $user_id .
	 * @return $mwb_crp_referal_trial_ended_coupons .
	 */
	public function wps_crp_get_paid_referal_trial_ended_coupons( $user_id ) {
		$mwb_crp_referal_trial_ended_coupons = get_user_meta( $user_id, 'wps_crp_referral_trial_ended_coupon', true );
		if ( empty( $mwb_crp_referal_trial_ended_coupons ) ) {
			$mwb_crp_referal_trial_ended_coupons = array();
		}
		return $mwb_crp_referal_trial_ended_coupons;

	}

	/**
	 * Get include cat.
	 *
	 * @since 1.7.0
	 */
	public function wps_crp_get_include_cat() {
		$wps_crp_include_cat = get_option( 'wps_crp_include_cat', '' );
		return $wps_crp_include_cat;
	}

	/**
	 * Get exclude cat.
	 *
	 * @since 1.7.0
	 */
	public function wps_crp_get_exclude_cat() {
		$wps_crp_exclude_cat = get_option( 'wps_crp_exclude_cat', '' );
		return $wps_crp_exclude_cat;
	}

	/** 
	 * function to get match current user roles and saved user roles
	 * 
	 * @param string $type of the discount allowed for the user roles.
	 * @param integer $user_id .
	 *
	 */
	public function wps_referral_discount_management( $type, $user_id ) {
		$selected_roles = array();
		$user_roles     = array();
		$user_data      = get_userdata( $user_id );
		if ( $user_data ) {
			$user_roles = $user_data->roles;
		}
		if ( 'user_signup' === $type ) {
			$selected_roles = get_option( 'wps_crp_signup_users', array() );
		} elseif ( 'referral_user_signup' === $type ) {
			$selected_roles = get_option( 'wps_crp_referral_signup_users', array() );
		} elseif ( 'referee_user_signup' === $type ) {
			$selected_roles = get_option( 'wps_crp_referrer_signup_users', array() );
		} elseif ( 'referal_purchase' === $type ) {
			$selected_roles = get_option( 'wps_crp_referral_purchase_users', array() );
		}
		$common_value = array_intersect( $selected_roles, $user_roles );
		if ( ! empty( $common_value ) || empty( $selected_roles ) ) {
			return true;
		}
		return false;
	}
}