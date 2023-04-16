<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://woocommerce.com/
 * @since      1.0.0
 *
 * @package    Coupon_Referral_Program
 * @subpackage Coupon_Referral_Program/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Coupon_Referral_Program
 * @subpackage Coupon_Referral_Program/admin
 */
class Coupon_Referral_Program_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @var      string $plugin_name    The ID of this plugin.
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
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @param string $hook name of the page.
	 * @since    1.0.0
	 */
	public function enqueue_styles( $hook ) {

		/* Enqueue styles only on this plugin's menu page.*/
		if ( 'woocommerce_page_wc-settings' === $hook || 'plugins.php' === $hook ) {

			wp_enqueue_style( $this->plugin_name, COUPON_REFERRAL_PROGRAM_DIR_URL . 'admin/css/coupon-referral-program-admin.css', array(), $this->version, 'all' );

			/* Enqueue style for using WooCommerce Tooltip.*/
			wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
			if ( isset( $_GET['section'] ) && 'crp_help' === $_GET['section'] && isset( $_GET['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'mwb_crp_nonce' ) ) {
				$custom_css = 'p.submit{display: none !important;}';
				wp_add_inline_style( $this->plugin_name, $custom_css );
			}
		}
		/* Enqueue styles only for Reports only.*/
		if ( 'woocommerce_page_wc-reports' === $hook ) {
			wp_enqueue_style( 'account_page', COUPON_REFERRAL_PROGRAM_DIR_URL . 'public/css/coupon-referral-program-public.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @param string $hook name of the page.
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook ) {
		/*  Enqueue scripts only on this plugin's menu page.*/
		if ( 'woocommerce_page_wc-settings' === $hook ) {
			wp_enqueue_script( $this->plugin_name . 'admin-js', COUPON_REFERRAL_PROGRAM_DIR_URL . 'admin/js/crp-admin.js', array(), $this->version, true );

			/* Enqueue and Localize script for using WooCommerce Tooltip.*/

			wp_enqueue_script( 'woocommerce_admin', WC()->plugin_url() . '/assets/js/admin/woocommerce_admin.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip', 'wc-enhanced-select' ), WC_VERSION, true );
			$params      = array(
				'strings' => '',
				'urls'    => '',
			);
			$translation = array( 'img_url' => COUPON_REFERRAL_PROGRAM_DIR_URL . 'public/images/bg.png' );
			wp_enqueue_media();

			wp_localize_script( 'woocommerce_admin', 'woocommerce_admin', $params );
			wp_localize_script( $this->plugin_name . 'admin-js', 'woocommerce_img', $translation );

		}
		if ( 'woocommerce_page_wc-reports' === $hook ) {
			$mwb_crp_arr = array(
				'Showing_page'   => __( 'Showing page _PAGE_ of _PAGES_', 'coupon-referral-program' ),
				'no_record'      => __( 'No records available', 'coupon-referral-program' ),
				'nothing_found'  => __( 'Nothing found', 'coupon-referral-program' ),
				'display_record' => __( 'Display _MENU_ Entries', 'coupon-referral-program' ),
				'filtered_info'  => __( '(filtered from _MAX_ total records)', 'coupon-referral-program' ),
				'search'         => __( 'Search', 'coupon-referral-program' ),
				'previous'       => __( 'Previous', 'coupon-referral-program' ),
				'next'           => __( 'Next', 'coupon-referral-program' ),
				'ajaxurl'        => admin_url( 'admin-ajax.php' ),
				'nonce'          => wp_create_nonce( 'wps_crp_report_nonce' ),
			);
			wp_enqueue_script( 'datatables', '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js', array(), $this->version, true );
			wp_register_script( $this->plugin_name . 'admin-report-js', COUPON_REFERRAL_PROGRAM_DIR_URL . 'admin/js/crp-report-admin.js', array(), $this->version, false );
			wp_localize_script( $this->plugin_name . 'admin-report-js', 'mwb_crp_admin', $mwb_crp_arr );
			wp_enqueue_script( $this->plugin_name . 'admin-report-js' );
		}
	}
	/**
	 * Adding settings menu for Coupon Referral Program in Woocommerce Settings Page.
	 *
	 * @param array $settings_tabs all settings tabs.
	 * @since    1.0.0
	 * @return array of settings.
	 */
	public function woocommerce_settings_tabs_option( $settings_tabs ) {
		$settings_tabs['crp-referral_setting'] = __( 'Referrals', 'coupon-referral-program' );
		return $settings_tabs;
	}


	/**
	 * Display the html of each setting page.
	 *
	 * @since    1.0.0
	 */
	public function crp_referral_settings_tab() {

		global $current_section;

		woocommerce_admin_fields( $this->crp_get_settings( $current_section ) );
	}

	/**
	 * Display the html of each sections using Setting API.
	 *
	 * @param  array $current_section array of the display sections.
	 * @since    1.0.0
	 */
	public function crp_get_settings( $current_section ) {

		$settings = array();
		if ( '' === $current_section ) {
			$settings = array(

				array(
					'title' => __( 'General', 'coupon-referral-program' ),
					'type'  => 'title',
					'id'    => 'general_options',
				),

				array(
					'title'   => __( 'Enable/Disable ', 'coupon-referral-program' ),
					'desc'    => __( 'Enable/Disable coupon referral program ', 'coupon-referral-program' ),
					'default' => 'no',
					'type'    => 'checkbox',
					'id'      => 'mwb_crp_plugin_enable',
				),
				array(
					'title'             => __( 'Referral key length', 'coupon-referral-program' ),
					'default'           => 7,
					'type'              => 'number',
					'custom_attributes' => array(
						'min'  => '7',
						'max'  => '10',
						'step' => '1',
					),
					'id'                => 'mwb_referral_length',
					'class'             => 'mwb_crp_input_val',
					'desc_tip'          => __( 'Set the length for referral key. The minimum & maximum length a referral key can have are 7 & 10.', 'coupon-referral-program' ),
				),
				array(
					'title'             => __( 'Set days to remember referrals', 'coupon-referral-program' ),
					'default'           => 365,
					'type'              => 'number',
					'custom_attributes' => array(
						'min'  => '1',
						'step' => '1',
					),
					'id'                => 'mwb_cpr_ref_link_expiry',
					'class'             => 'mwb_crp_input_val',
					'desc_tip'          => __( 'After entered days referrals will not be treated as the referred user.', 'coupon-referral-program' ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'general_options',
				),

				array(
					'title' => __( 'Refer via referral code', 'coupon-referral-program' ),
					'type'  => 'title',
				),
				array(
					'title'   => __( 'Enable/Disable', 'coupon-referral-program' ),
					'desc'    => __( 'Allow customer to refer their friend using the referral code', 'coupon-referral-program' ),
					'default' => 'no',
					'type'    => 'checkbox',
					'id'      => 'mwb_crp_referal_via_code',
				),
				array(
					'title'    => __( 'Select the discount type', 'coupon-referral-program' ),
					'default'  => 1,
					'type'     => 'select',
					'id'       => 'mwb_crp_user_referral_coupon_type',
					'class'    => 'mwb_crp_input_val',
					'options'  => array(
						'mwb_cpr_referral_user_coupon_percent' => __( 'Percentage', 'coupon-referral-program' ),
						'mwb_cpr_referral_user_coupon_fixed'   => __( 'Fixed', 'coupon-referral-program' ),
					),
					'desc_tip' => __( 'Set the discount type for user', 'coupon-referral-program' ),
				),
				array(
					'title'             => __( 'Discount amount', 'coupon-referral-program' ),
					'default'           => 1,
					'type'              => 'number',
					'custom_attributes' => array( 'min' => '1' ),
					'id'                => 'mwb_crp_referral_user_coupon_amount',
					'class'             => 'mwb_crp_input_val',
					'desc_tip'          => __( 'Enter the discount amount.', 'coupon-referral-program' ),
				),
				array(
					'title'   => __( 'Enable/Disable', 'coupon-referral-program' ),
					'desc'    => __( 'Allow coupon configuration setting applicable for the referral code', 'coupon-referral-program' ),
					'default' => 'no',
					'type'    => 'checkbox',
					'id'      => 'mwb_crp_referal_code_restriction',
				),
				array(
					'type' => 'sectionend',
				),

				array(
					'title' => __( 'Social sharing', 'coupon-referral-program' ),
					'type'  => 'title',
				),

				array(
					'title'   => __( 'Enable/Disable social sharing', 'coupon-referral-program' ),
					'desc'    => __( 'Enable this checkbox to display social sharing option for referral link', 'coupon-referral-program' ),
					'default' => 'no',
					'type'    => 'checkbox',
					'id'      => 'mwb_cpr_social_enable',
				),

				array(
					'title'   => __( 'Facebook', 'coupon-referral-program' ),
					'default' => 'no',
					'type'    => 'checkbox',
					'id'      => 'mwb_cpr_facebook',
				),

				array(
					'title'   => __( 'Twitter', 'coupon-referral-program' ),
					'default' => 'no',
					'type'    => 'checkbox',
					'id'      => 'mwb_cpr_twitter',
				),

				array(
					'title'   => __( 'Email', 'coupon-referral-program' ),
					'default' => 'no',
					'type'    => 'checkbox',
					'id'      => 'mwb_cpr_email',
				),
				array(
					'title'   => __( 'WhatsApp', 'coupon-referral-program' ),
					'default' => 'no',
					'type'    => 'checkbox',
					'id'      => 'mwb_crp_share_whtsapp',
				),
				array(
					'type' => 'sectionend',
				),
				array(
					'type' => 'sectionend',
				),
			);
		}
		if ( 'referal_config' === $current_section ) {
			$settings = array(
				array(
					'title' => esc_html__( 'Referral signup discount for referee', 'coupon-referral-program' ),
					'type'  => 'title',
				),
				// Referee discount.
				array(
					'title'    => __( 'Enable/Disable discount', 'coupon-referral-program' ),
					'desc'     => __( 'Referral signup discount for referee', 'coupon-referral-program' ),
					'default'  => 'no',
					'type'     => 'checkbox',
					'id'       => 'mwb_crp_refree_discount_enable',
					'desc_tip' => __( 'By using this setting you can give the discount coupon for referee when referred user signup using referral link.', 'coupon-referral-program' ),
				),
				array(
					'title'             => __( 'Enter discount for referee', 'coupon-referral-program' ),
					'default'           => 1,
					'type'              => 'number',
					'desc_tip'          => __( 'The value you enter will be set as discount coupon amount for referee.', 'coupon-referral-program' ),
					'id'                => 'refree_discount_value',
					'class'             => 'mwb_crp_input_val',
					'custom_attributes' => array( 'min' => 1 ),
				),
				array(
					'title'    => __( 'Referral discount type', 'coupon-referral-program' ),
					'default'  => 1,
					'type'     => 'select',
					'id'       => 'referee_discount_type',
					'options'  => array(
						'mwb_cpr_fixed'   => __( 'Fixed', 'coupon-referral-program' ),
						'mwb_cpr_percent' => __( 'Percentage', 'coupon-referral-program' ),
					),
					'desc_tip' => __( 'Select the type for your signup discount coupon for referee when referred user signup using referred link.' ),
					'desc'     => __( 'The referee will get the selected coupon type on the referral signup.', 'coupon-referral-program' ),
				),
				array(
					'title'             => __( 'Discount on the nth referral sign-up', 'coupon-referral-program' ),
					'default'           => '',
					'type'              => 'number',
					'desc_tip'          => __( 'The value you enter will be set as the nth sign-up coupon for the Referee. If it is blank then on every referral, the referee will get the coupon', 'coupon-referral-program' ),
					'id'                => 'nth_signup_discount_value',
					'desc'     => __( 'Referee gets a coupon for every nth sign-up only.', 'coupon-referral-program' ),
					'class'             => 'mwb_crp_input_val',
				),
				array(
					'type' => 'sectionend',
				),
				array(
					'title' => __( 'Referral configuration', 'coupon-referral-program' ),
					'type'  => 'title',
				),
				array(
					'title'   => __( 'Enable discount coupon on referral purchase.', 'coupon-referral-program' ),
					'desc'    => __( 'Enable this setting to allow the customer to get the coupon on referral purchase ', 'coupon-referral-program' ),
					'default' => 'yes',
					'type'    => 'checkbox',
					'id'      => 'mwb_crp_enable_referal_purchase',
				),
				array(
					'title'   => __( 'Enable special discount coupon on first referral purchase.', 'coupon-referral-program' ),
					'desc'    => __( 'Enable this setting to allow the customer to get the special discount coupon on first referral purchase ', 'coupon-referral-program' ),
					'default' => 'no',
					'type'    => 'checkbox',
					'id'      => 'mwb_crp_enable_first_referal_purchase',
				),
				array(
					'title'             => __( 'Max. no. for referral orders', 'coupon-referral-program' ),
					'default'           => 1,
					'type'              => 'number',
					'custom_attributes' => array( 'min' => '1' ),
					'id'                => 'restrict_no_of_order',
					'class'             => 'mwb_crp_input_val',
					'desc_tip'          => __( 'Set the Maximum number of orders to get the discount on referral orders.', 'coupon-referral-program' ),
				),
				array(
					'type' => 'sectionend',
				),
				array(
					'title' => __( 'Coupon amount calculation on referral purchase', 'coupon-referral-program' ),
					'type'  => 'title',
					'desc'  => __( 'The following options will be used for calculation of the referral purchase coupon amount.', 'coupon-referral-program' ) .
					'<br><b>' . __( 'For Example:-', 'coupon-referral-program' ) . '</b><br>' .
					__( '1- If you select ', 'coupon-referral-program' ) . '<b>' . __( ' “Referral purchase discount amount type” = Percentage', 'coupon-referral-program' ) . '</b>' . __( ' and “Referral Purchase Discount” = 20. Now, the Referred customer has placed order of amount $200 then a referral coupon amount will be 40(200*20%) up to “Referral purchase discount amount upto” value.', 'coupon-referral-program' ) . '<br>' . __( '2- If you select ', 'coupon-referral-program' ) . '<b>' . __( '“Referral purchase discount amount type” = Fixed', 'coupon-referral-program' ) . '</b>' . __(
						' and “Referral Purchase Discount” = 20. Then, no matter of order total, it will be 20.',
						'coupon-referral-program'
					),
					'id'    => 'coupon_amount_calculation',
				),
				array(
					'title'    => __( 'Referral purchase discount amount type', 'coupon-referral-program' ),
					'default'  => 1,
					'type'     => 'select',
					'id'       => 'referral_discount_type',
					'class'    => 'mwb_crp_input_val',
					'options'  => array(
						'mwb_cpr_referral_fixed'   => __( 'Fixed', 'coupon-referral-program' ),
						'mwb_cpr_referral_percent' => __( 'Percentage', 'coupon-referral-program' ),
					),
					'desc_tip' => __( 'Referral coupon amount will be calculate on the referral order total', 'coupon-referral-program' ),
					'desc'     => __( 'If you select the “Percentage” option then "Referral purchase coupon amount" will be calculated on the order total purchased by referred customers otherwise fixed amount coupon will be calculated. ', 'coupon-referral-program' ),
				),
				array(
					'title'             => __( 'Referral purchase discount amount upto', 'coupon-referral-program' ),
					'default'           => 1,
					'type'              => 'number',
					'class'             => 'mwb_crp_input_val',
					'id'                => 'referral_discount_upto',
					'custom_attributes' => array( 'min' => '0' ),
					'desc_tip'          => __( 'Enter the maximum coupon amount limit when referral discount amount type is percentage,set 0 if you do not want to set limit.', 'coupon-referral-program' ),
					'desc'              => __( 'Set the max referral coupon value (max coupon amount) you want to provide to your customers when the referral customer purchase from your store.', 'coupon-referral-program' ) . '<div>' . __( 'Note- If you do not want to use Referral discount amount upto functionality then for this you have to set Referral discount amount upto to 0.', 'coupon-referral-program' ) . '</div>',
				),
				array(
					'title'             => __( 'Referral purchase discount', 'coupon-referral-program' ),
					'default'           => 1,
					'type'              => 'number',
					'custom_attributes' => array( 'min' => '1' ),
					'id'                => 'referral_discount_on_order',
					'class'             => 'mwb_crp_input_val',
					'desc_tip'          => __( 'Enter the discount value you want to give your customers, who have referred other users on your site.', 'coupon-referral-program' ),
				),
				array(
					'title'    => __( 'Referral Purchase discount type', 'coupon-referral-program' ),
					'default'  => 1,
					'type'     => 'select',
					'id'       => 'signup_discount_type',
					'options'  => array(
						'mwb_cpr_fixed'   => __( 'Fixed', 'coupon-referral-program' ),
						'mwb_cpr_percent' => __( 'Percentage', 'coupon-referral-program' ),
					),
					'desc_tip' => __( 'Select the type for your referral purchase discount coupon for referee.', 'coupon-referral-program' ),
					'desc'     => __( 'The referee will get the selected coupon type on the referral purchase.', 'coupon-referral-program' ),

				),
				array(
					'title'             => __( 'First Referral purchase discount', 'coupon-referral-program' ),
					'default'           => 1,
					'type'              => 'number',
					'custom_attributes' => array( 'min' => '1' ),
					'id'                => 'first_referral_discount_on_order',
					'class'             => 'mwb_crp_input_val',
					'desc_tip'          => __( 'Enter the discount value you want to give your customers, who have referred other users on your site. Please Note, This will be applicable on the first purchase only', 'coupon-referral-program' ),
				),
				array(
					'type' => 'sectionend',
				),
				array(
					'title' => __( 'Referee Gets Coupon', 'coupon-referral-program' ),
					'type'  => 'title',
				),
				array(
					'title'             => __( 'Number of times referee gets coupon on new user joining', 'coupon-referral-program' ),
					'default'           => '',
					'type'              => 'number',
					'custom_attributes' => array( 'min' => '0' ),
					'id'                => 'mwb_crp_total_number_referred_users',
					'class'             => 'mwb_crp_input_val',
					'desc_tip'          => __( 'Enter how many time referee get coupon when new users signup using him/her referral link. By default, it will be unlimited for blank field', 'coupon-referral-program' ),
				),
				array(
					'type' => 'sectionend',
				),

			);
		}
		if ( 'display' === $current_section ) {
			$settings = array(
				array(
					'title' => __( 'Display configuration', 'coupon-referral-program' ),
					'type'  => 'title',
				),

				array(
					'title'   => __( 'Enable/Disable', 'coupon-referral-program' ),
					'desc'    => __( 'Enable/Disable popup button', 'coupon-referral-program' ),
					'default' => 'yes',
					'type'    => 'checkbox',
					'id'      => 'mwb_cpr_button_enable',
				),

				array(
					'title'   => __( 'Enable/Disable animation', 'coupon-referral-program' ),
					'desc'    => __( 'Enable this checkbox if you want animation over the referral button', 'coupon-referral-program' ),
					'default' => 'no',
					'type'    => 'checkbox',
					'id'      => 'mwb_crp_animation',
				),

				array(
					'title'    => __( 'Button text', 'coupon-referral-program' ),
					'type'     => 'text',
					'default'  => __( 'Referral program', 'coupon-referral-program' ),
					'id'       => 'referral_button_text',
					'class'    => 'mwb_crp_input_val',
					'desc_tip' => __( 'Enter the text you want to display on the button.', 'coupon-referral-program' ),
				),

				array(
					'title'    => __( 'Button color', 'coupon-referral-program' ),
					'type'     => 'color',
					'default'  => '#E85E54',
					'id'       => 'referral_button_color',
					'desc_tip' => __( 'Select the color you want to have on the button and on the popup', 'coupon-referral-program' ),
					'desc'     => '<div class="mwb_crp_preview_div" style="background-color:' . self::get_selected_color() . ';">' . self::get_visible_text() . '</div>',
				),

				array(
					'title'    => __( 'Custom css', 'coupon-referral-program' ),
					'type'     => 'textarea',
					'id'       => 'referral_button_custom_css',
					'class'    => 'mwb_crp_input_val',
					'desc_tip' => __( 'Enter the css you want to apply on the button', 'coupon-referral-program' ),
				),

				array(
					'title'    => __( 'Select Position', 'coupon-referral-program' ),
					'type'     => 'select',
					'id'       => 'referral_button_positioning',
					'class'    => 'wc-enhanced-select',
					'desc'     => __( 'Use this shortcode [crp_popup_button] to display pop-up button.', 'coupon-referral-program' ),
					'desc_tip' => __( 'Select whether you want to display the Referral Button left, right, or want to use a shortcode.  If you select the shortcode [crp_popup_button] then the referral button will work only on the page you use this code. Note- If you select shortcode then Select Pages will not work plus you can’t change the position of the Button.', 'coupon-referral-program' ),
					'options'  => array(
						'right_bottom' => __( 'Right Bottom', 'coupon-referral-program' ),
						'left_bottom'  => __( 'Left Bottom', 'coupon-referral-program' ),
						'top_left'     => __( 'Top Left', 'coupon-referral-program' ),
						'top_right'    => __( 'Top Right', 'coupon-referral-program' ),
						'shortcode'    => __( 'shortcode', 'coupon-referral-program' ),
					),
				),

				array(
					'title'    => __( 'Select pages', 'coupon-referral-program' ),
					'type'     => 'multiselect',
					'id'       => 'referral_button_page',
					'class'    => 'wc-enhanced-select',
					'desc_tip' => __( 'Select the page where you want to display the button, leave blank if you want to display on all the pages.', 'coupon-referral-program' ),
					'options'  => self::get_pages(),
				),
				array(
					'title'    => __( 'Pop-up image', 'coupon-referral-program' ),
					'type'     => 'text',
					'default'  => self::get_selected_image(),
					'id'       => 'mwb_cpr_image',
					'desc_tip' => __( 'Select the background image for your popup', 'coupon-referral-program' ),
					'desc'     => '<div class="mwb_crp_image"><button class="mwb_crp_image_button button ">' . __( 'Upload', 'coupon-referral-program' ) . '</button><button class="mwb_crp_image_resetbtn button">' . __( 'Reset', 'coupon-referral-program' ) . '</button></div><div class="mwb_cpr_image_display"><img id="mwb_cpr_image_display" alt="color image" width="100" height="100" src="' . self::get_selected_image() . '"></div>',
				),
				array(
					'title'    => __( 'Use this shortcode for the referral link', 'coupon-referral-program' ),
					'type'     => 'text',
					'desc_tip' => __( 'You can use the given shortcode anywhere you want, it will display the referral link of your customers', 'coupon-referral-program' ),
					'default'  => '[crp_referral_link]',
					'id'       => 'mwb_crp_referral_link',
				),

				array(
					'title'    => __( 'Use this shortcode for the referral code', 'coupon-referral-program' ),
					'type'     => 'text',
					'desc_tip' => __( 'You can use the given shortcode anywhere you want, it will display the referral code of your customers', 'coupon-referral-program' ),
					'default'  => '[crp_referral_code]',
					'id'       => 'mwb_crp_referral_code',
				),

				array(
					'title'    => __( 'Use this shortcode for the referral tab', 'coupon-referral-program' ),
					'type'     => 'text',
					'desc_tip' => __( 'You can use the given shortcode anywhere you want, it will display the referral tab', 'coupon-referral-program' ),
					'default'  => '[crp_referral_tab]',
					'id'       => 'mwb_crp_referral_tab',
				),

				array(
					'title'    => __( 'Referral Tab Text', 'coupon-referral-program' ),
					'type'     => 'textarea',
					'id'       => 'referral_tab_text',
					'class'    => 'mwb_crp_input_val',
					'desc_tip' => __( 'Enter the text you want to show on the referral tab', 'coupon-referral-program' ),
					'value'    => get_option( 'referral_tab_text' ) ? get_option( 'referral_tab_text' ) : esc_html__( 'Refer your friends and you’ll earn discounts on their purchases', 'coupon-referral-program' ),
				),

				array(
					'title'    => __( 'Signup PopUp Text', 'coupon-referral-program' ),
					'type'     => 'textarea',
					'id'       => 'signup_popup_text',
					'class'    => 'mwb_crp_input_val',
					'desc_tip' => __( 'Enter the text you want to show in the sign up popup. The empty field value will allow to show default content. You can use {crp_referral_code}, {crp_referral_link} shortcodes.', 'coupon-referral-program' ),
					'value'    => get_option( 'signup_popup_text' ),
				),

				array(
					'type' => 'sectionend',
				),
			);
		}
		if ( 'signup' === $current_section ) {
			$settings = array(
				array(
					'title' => __( 'Sign up discount', 'coupon-referral-program' ),
					'type'  => 'title',
				),

				array(
					'title'    => __( 'Enable/Disable discount', 'coupon-referral-program' ),
					'desc'     => __( 'Enable/Disable discount coupon on sign up', 'coupon-referral-program' ),
					'desc_tip' => __( 'Enable this setting to give discount coupon for new user signup.', 'coupon-referral-program' ),
					'default'  => 'no',
					'type'     => 'checkbox',
					'id'       => 'mwb_crp_signup_enable',
				),
				array(
					'title'   => __( 'Select users', 'coupon-referral-program' ),
					'id'      => 'mwb_crp_signup_enable_value',
					'default' => 'yes',
					'type'    => 'radio',
					'options' => array(
						'yes' => __( 'All users', 'coupon-referral-program' ),
						'no'  => __( 'Only referred users', 'coupon-referral-program' ),
					),
				),
				array(
					'title'             => __( 'Enter discount', 'coupon-referral-program' ),
					'default'           => 1,
					'type'              => 'number',
					'desc_tip'          => __( 'The value you enter will be set as discount coupon amount.', 'coupon-referral-program' ),
					'id'                => 'signup_discount_value',
					'class'             => 'mwb_crp_input_val',
					'custom_attributes' => array( 'min' => 1 ),
				),
				array(
					'title'    => __( 'Signup Coupon type', 'coupon-referral-program' ),
					'default'  => 1,
					'type'     => 'select',
					'id'       => 'signup_discount_coupon_type',
					'options'  => array(
						'mwb_cpr_fixed'   => __( 'Fixed', 'coupon-referral-program' ),
						'mwb_cpr_percent' => __( 'Percentage', 'coupon-referral-program' ),
					),
					'desc_tip' => __( 'Select the type for your signup discount coupon which you want to offer for your customers.', 'coupon-referral-program' ),
					'desc'     => __( 'The customer will get the selected coupon type on the signup.', 'coupon-referral-program' ),
				),
				array(
					'type' => 'sectionend',
				),
			);
		}

		if ( 'coupon' === $current_section ) {

			$settings = array(
				array(
					'title' => __( 'Common coupon settings for both referral & sign up', 'coupon-referral-program' ),
					'type'  => 'title',
				),
				array(
					'title'    => __( 'Individual use of coupon', 'coupon-referral-program' ),
					'desc'     => __( 'Permit separate use of coupons', 'coupon-referral-program' ),
					'default'  => 'no',
					'type'     => 'checkbox',
					'id'       => 'coupon_individual',
					'desc_tip' => __( 'Enable this checkbox if the coupon can’t be used in conjunction with other coupons', 'coupon-referral-program' ),
				),

				array(
					'title'    => __( 'Free shipping', 'coupon-referral-program' ),
					'desc'     => __( 'Permit free shipping on coupons', 'coupon-referral-program' ),
					'default'  => 'no',
					'type'     => 'checkbox',
					'id'       => 'coupon_freeshipping',
					'desc_tip' => __( 'Enable this checkbox, if the coupon permits free shipping to customers. Note: A free shipping method must be enabled in your shipping zone and must be set to “require a valid free shipping coupon".', 'coupon-referral-program' ),
				),
				array(
					'title'    => __( 'Exclude sale items', 'coupon-referral-program' ),
					'desc'     => __( 'Exclude the sales items', 'coupon-referral-program' ),
					'default'  => 'no',
					'type'     => 'checkbox',
					'id'       => 'mwb_crp_exclude_sales',
					'desc_tip' => __( 'Check this box if the coupon should not apply to items on sale. Per-item coupons will only work if the item is not on sale. Per-cart coupons will only work if there are items in the cart that are not on sale.', 'coupon-referral-program' ),
				),
				array(
					'title'             => __( 'Enter coupon length', 'coupon-referral-program' ),
					'default'           => 5,
					'type'              => 'number',
					'id'                => 'coupon_length',
					'class'             => 'mwb_crp_input_val',
					'custom_attributes' => array(
						'min' => 5,
						'max' => 10,
					),
					'desc_tip'          => __( 'Set the coupon length excluding the prefix.(The minimum length you can set is 5)', 'coupon-referral-program' ),
				),

				array(
					'title'             => __( 'Coupon expires after days', 'coupon-referral-program' ),
					'default'           => 0,
					'type'              => 'number',
					'id'                => 'coupon_expiry',
					'class'             => 'mwb_crp_input_val',
					'custom_attributes' => array( 'min' => 0 ),
					'desc_tip'          => __( 'Enter the days after which the coupon will get expire. Set the value to “1” if the coupon will expire in one-day And set the value to “0” if the coupon has no fix expiry date.', 'coupon-referral-program' ),
				),

				array(
					'title'    => __( 'No. of time coupon can be used', 'coupon-referral-program' ),
					'default'  => 0,
					'type'     => 'number',
					'id'       => 'coupon_usage',
					'class'    => 'mwb_crp_input_val',
					'desc_tip' => __( 'How many times the coupon can be used before it gets expired.', 'coupon-referral-program' ),
				),

				array(
					'title'    => __( 'Add prefix on coupon', 'coupon-referral-program' ),
					'default'  => '',
					'type'     => 'text',
					'id'       => 'coupon_prefix',
					'class'    => 'mwb_crp_input_val',
					'desc_tip' => __( 'If you desire to add a prefix to your coupon, you can add it here.', 'coupon-referral-program' ),
				),
				array(
					'title'       => __( 'Minimum spend', 'coupon-referral-program' ),
					'default'     => '',
					'type'        => 'text',
					'id'          => 'mwb_crp_coupon_min_val',
					'class'       => 'mwb_crp_input_val wc_input_price',
					'desc_tip'    => __( 'This field allows you to set the minimum spend(subtotal) allowed to use the coupon.', 'coupon-referral-program' ),
					'placeholder' => __( 'No minimum', 'coupon-referral-program' ),
				),
				array(
					'title'       => __( 'Maximum spend', 'coupon-referral-program' ),
					'default'     => '',
					'type'        => 'text',
					'id'          => 'mwb_crp_coupon_max_val',
					'class'       => 'mwb_crp_input_val wc_input_price',
					'desc_tip'    => __( 'This field allows you to set the maximum spend(subtotal) allowed to use the coupon.', 'coupon-referral-program' ),
					'placeholder' => __( 'No maximum', 'coupon-referral-program' ),
				),
				array(
					'title'    => __( 'Include products', 'coupon-referral-program' ),
					'type'     => 'multiselect',
					'id'       => 'mwb_crp_include_pro',
					'class'    => 'wc-enhanced-select mwb_crp_input_val',
					'desc_tip' => __( 'Product that the coupon will be applied to.', 'coupon-referral-program' ),
					'options'  => $this->mwb_crp_get_all_products(),
				),

				array(
					'title'    => __( 'Exclude products', 'coupon-referral-program' ),
					'type'     => 'multiselect',
					'id'       => 'mwb_crp_exclude_pro',
					'class'    => 'wc-enhanced-select mwb_crp_input_val',
					'desc_tip' => __( 'Product that the coupon will not be applied to.', 'coupon-referral-program' ),
					'options'  => $this->mwb_crp_get_all_products(),
				),
				array(
					'type' => 'sectionend',
				),
			);
		}
		if ( 'points_rewards' === $current_section ) {
			$settings = array(
				array(
					'title' => __( 'Settings for the Points & Rewards', 'coupon-referral-program' ),
					'type'  => 'title',
				),
				array(
					'title'   => __( 'Enable/Disable ', 'coupon-referral-program' ),
					'desc'    => __( 'Enable/Disable points instead of coupon', 'coupon-referral-program' ),
					'default' => 'no',
					'type'    => 'checkbox',
					'id'      => 'mwb_crp_points_rewards_enable',
				),
				array(
					'title'   => __( 'Enter bonus points for  the referral signup ', 'coupon-referral-program' ),
					'default' => '1',
					'type'    => 'number',
					'id'      => 'mwb_crp_points_rewards_signup_points',
				),
				array(
					'title'   => __( 'Enter points for referral purchase', 'coupon-referral-program' ),
					'default' => '1',
					'type'    => 'number',
					'id'      => 'mwb_crp_points_rewards_reffral_points',
				),
				array(
					'title'   => __( 'Enter Referral signup points for referee', 'coupon-referral-program' ),
					'default' => '1',
					'type'    => 'number',
					'id'      => 'mwb_crp_points_rewards_reffree_points',
				),
				array(
					'title'   => __( 'Enable/Disable ', 'coupon-referral-program' ),
					'desc'    => __( 'Enable this if you want to hide the referral section in frontend.', 'coupon-referral-program' ),
					'default' => 'no',
					'type'    => 'checkbox',
					'id'      => 'mwb_crp_points_rewards_hide_referal',
				),
				array(
					'type' => 'sectionend',
				),
			);
		}
		if ( 'reports' === $current_section ) {
			wp_safe_redirect( admin_url( 'admin.php?page=wc-reports&tab=crp_report' ) );
			exit;
		}
		if ( 'woocommerce_subscription' === $current_section ) {
			$settings = array(
				array(
					'title' => __( 'Settings for the Woocommerce Subscriptions', 'coupon-referral-program' ),
					'type'  => 'title',
				),
				array(
					'title'   => __( 'Enable/Disable ', 'coupon-referral-program' ),
					'desc'    => __( 'Enable/Disable coupon for the subscribers on their next recurring payment.', 'coupon-referral-program' ),
					'default' => 'no',
					'type'    => 'checkbox',
					'id'      => 'mwb_crp_woo_subscriptions_enable',
				),
				array(
					'title'    => __( 'Apply coupons', 'woocommerce' ),
					'id'       => 'mwb_crp_apply_all_coupon_on_subscription',
					'default'  => 'yes',
					'type'     => 'radio',
					'options'  => array(
						'yes' => __( 'All coupons', 'coupon-referral-program' ),
						'no'  => __( 'Allow customer to choose', 'coupon-referral-program' ),
					),
					'desc_tip' => __( 'Select the “All Coupons” option to employ all coupons or “Single Coupon” to employ a single coupon for the next recurring payment of the subscription.', 'coupon-referral-program' ),
				),
				array(
					'type' => 'sectionend',
				),
			);
		}
		if ( 'crp_help' === $current_section ) {
			?>
			<div class="mwb_crp_table_wrapper">
				<div class="mwb_crp_overview_content">
					<h3 class="mwb_crp_overview_heading"><?php esc_html_e( 'Connect with us and explore more about coupon referral program', 'coupon-referral-program' ); ?></h3>
					<p>
						<a class="mwb_crp_help_link" href="https://woocommerce.com/document/coupon-referral-program/" target="_blank"><?php esc_html_e( 'Documentation', 'coupon-referral-program' ); ?></a>
						<a class="mwb_crp_help_link" href="https://wpswings.com/contact-us/" target="_blank"><?php esc_html_e( 'Contact us', 'coupon-referral-program' ); ?></a>
					</p>
					<p>
						<?php esc_html_e( 'Please click', 'coupon-referral-program' ); ?> <a class="mwb_crp_help_link" href="https://join.skype.com/invite/xCmwbfxx8MCX" target="_blank"><?php esc_html_e( 'Here', 'coupon-referral-program' ); ?></a> <?php esc_html_e( 'to connect with our team members instantly on Skype', 'coupon-referral-program' ); ?>
					</p>
				</div>
				<div class="mwb_crp_video_wrapper">
					<iframe height="400px" width="60%" src="https://www.youtube.com/embed/YzE6cp6KSxo" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
				</div>
			</div>
			<?php
		}

		if ( 'prevent_fraudulent' === $current_section ) {
			$settings = array(
				array(
					'title' => __( 'Prevent Fraudulent', 'coupon-referral-program' ),
					'type'  => 'title',
				),
				array(
					'title'   => __( 'Enable/Disable ', 'coupon-referral-program' ),
					'desc'    => __( 'Enable/Disable email restriction', 'coupon-referral-program' ),
					'default' => 'no',
					'type'    => 'checkbox',
					'id'      => 'mwb_crp_email_domains_enable',
				),
				array(
					'title'    => __( 'Add allowed email domains for signup', 'coupon-referral-program' ),
					'type'     => 'text',
					'desc_tip' => __( 'You can add the email domains to allow the signup for specific email domains. There will be no restriction for empty field', 'coupon-referral-program' ),
					'default'  => '',
					'id'       => 'mwb_crp_email_domains',
				),
				array(
					'type' => 'sectionend',
				),
				array(
					'title' => '',
					'type'  => 'title',
					'desc'  => '<br><b>' . __( 'For Example:-', 'coupon-referral-program' ) . '</b><br>' .
					__( 'You can add the multiple domains separated by comma like this', 'coupon-referral-program' ) . ':<b> gmail.com, yahoo.com</b>',
					'id'    => 'mwb_crp_email_domains_example',
				),
				array(
					'type' => 'sectionend',
				),
			);
		}
		/**
		 * Filter CRP section's settings .
		 *
		 * @since 1.6.4
		 * @param array() $settings .
		 */
		return apply_filters( 'crp_get_settings', $settings );
	}

	/**
	 * Save the data using Setting API
	 *
	 * @since    1.0.0
	 */
	public function crp_referral_setting_save() {

		global $current_section;
		$settings = $this->crp_get_settings( $current_section );
		WC_Admin_Settings::save_fields( $settings );
	}

	/**
	 * Get the color and set it for the Button's color(Referral Program Button)
	 *
	 * @since    1.0.0
	 */
	public static function get_pages() {
		$mwb_page_title = array();
		$mwb_pages      = get_pages();
		foreach ( $mwb_pages as $pagedata ) {
			$mwb_page_title[ $pagedata->ID ] = $pagedata->post_title;
		}
		$mwb_page_title['details'] = 'Product Detail';
		return $mwb_page_title;
	}

	/**
	 * Get the color and set it for the Button's color(Referral Program Button)
	 *
	 * @since    1.0.0
	 */
	public static function get_selected_color() {
		$referral_button_color = get_option( 'referral_button_color', '#E85E54' );
		return $referral_button_color;
	}

	/**
	 * Get the image url and set it for image
	 *
	 * @since    1.0.0
	 */
	public static function get_selected_image() {
		$mwb_default_image  = COUPON_REFERRAL_PROGRAM_DIR_URL . 'public/images/bg.png';
		$referral_image_url = get_option( 'mwb_cpr_image', '' );
		if ( empty( $referral_image_url ) ) {
			$referral_image_url = $mwb_default_image;
		}

		return $referral_image_url;
	}

	/**
	 * Get the text for Referral Button
	 *
	 * @since    1.0.0
	 */
	public static function get_visible_text() {
		$referral_button_text = get_option( 'referral_button_text', __( 'Referral Program', 'coupon-referral-program' ) );
		return $referral_button_text;
	}

	/**
	 * Get the discount type
	 *
	 * @since    1.0.0
	 */
	public static function get_discount_type() {
		$referral_discount_type = get_option( 'signup_discount_type', 'fixed' );
		if ( 'mwb_cpr_percent' === $referral_discount_type ) {
			$referral_discount_type = 'percentage';
		} else {
			$referral_discount_type = 'fixed';
		}
		return $referral_discount_type;
	}

	/**
	 * Print the sections
	 *
	 * @since    1.0.0
	 */
	public function crp_output_sections() {
		global $current_section;
		$sections = $this->crp_get_sections();

		echo '<ul class="subsubsub">';

		$array_keys = array_keys( $sections );
		$nonce      = wp_create_nonce( 'mwb_crp_nonce' );
		foreach ( $sections as $id => $label ) {
			echo '<li><a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=crp-referral_setting&section=' . sanitize_title( $id ) ) ) . '&nonce=' . esc_html( $nonce ) . '" class="' . ( $current_section === $id ? 'current' : '' ) . '">' . esc_html( $label ) . '</a> ' . ( end( $array_keys ) === $id ? '' : '|' ) . ' </li>';
		}
		echo '</ul><br class="clear">';
	}

	/**
	 * Set the array for each sections
	 *
	 * @since    1.0.0
	 */
	public function crp_get_sections() {

		$sections = array(
			''                   => __( 'General', 'coupon-referral-program' ),
			'referal_config'     => __( 'Referral configuration', 'coupon-referral-program' ),
			'signup'             => __( 'Sign up discount', 'coupon-referral-program' ),
			'coupon'             => __( 'Coupon configuration', 'coupon-referral-program' ),
			'display'            => __( 'Display configuration', 'coupon-referral-program' ),
			'prevent_fraudulent' => __( 'Prevent Fraudulent', 'coupon-referral-program' ),
			'reports'            => __( 'Reports', 'coupon-referral-program' ),

		);
		if ( is_plugin_active( 'woocommerce-points-and-rewards/woocommerce-points-and-rewards.php' ) ) {

			$sections['points_rewards'] = __( 'Points & Rewards', 'coupon-referral-program' );
		}
		if ( is_plugin_active( 'woocommerce-subscriptions/woocommerce-subscriptions.php' ) ) {
 
			$sections['woocommerce_subscription'] = __( 'Woocommerce subscriptions', 'coupon-referral-program' );
		}
		/**
		 * Filter CRP settings section.
		 *
		 * @since 1.6.4
		 * @param array() $settings .
		 */
		return apply_filters( 'crp_get_sections', $sections );
	}

	/**
	 * Extend the section
	 *
	 * @param array $mwb_crp_help_section .
	 */
	public function mwb_crp_help_section( $mwb_crp_help_section ) {
		$mwb_crp_help_section['crp_help'] = __( 'Help and support', 'coupon-referral-program' );
		return $mwb_crp_help_section;
	}

	/**
	 * Set the default value for the number if they are blank
	 *
	 * @param array $parm array of the post settings.
	 * @since    1.0.0
	 */
	public function mwb_save_settings( $parm ) {
		check_admin_referer( 'woocommerce-settings' );
		$mwb_crp_blank_check_field = array(
			'referral_discount_upto'     => 1,
			'referral_discount_on_order' => 1,
			'restrict_no_of_order'       => 1,
			'mwb_cpr_ref_link_expiry'    => 1,
			'signup_discount_value'      => 1,
			'coupon_length'              => 5,
			'coupon_expiry'              => 0,
			'mwb_referral_length'        => 7,
			'coupon_usage'               => 0,
			'referral_discount_upto'     => 0,
		);
		foreach ( $mwb_crp_blank_check_field as $key => $value ) {
			if ( isset( $_POST[ $key ] ) ) {
				if ( empty( $_POST[ $key ] ) ) {
					update_option( $key, $value );
				}
			}
		}
	}

	/**
	 * Display The report of the CRP in the report section
	 *
	 * @name mwb_crp_report
	 * @since    1.0.0
	 * @param array $report array of the reports.
	 */
	public function mwb_crp_report( $report ) {
		$report['crp_report'] = array(
			'title'   => __( 'Referrals', 'coupon-referral-program' ),
			'reports' => array(
				'crp_users_reports' => array(
					'title'       => __( 'Users', 'coupon-referral-program' ),
					'description' => '',
					'hide_title'  => true,
					'callback'    => array( $this, 'mwb_crp_report_users' ),
				),
			),
		);
		return $report;
	}

	/**
	 * Display The report of the users.
	 *
	 * @name mwb_crp_report
	 * @since    1.0.0
	 */
	public function mwb_crp_report_users() {
		include COUPON_REFERRAL_PROGRAM_DIR_PATH . '/admin/partials/class-coupon-referral-program-admin-display-report.php';

	}

	/**
	 * Add CRP Report button in the admin menu
	 *
	 * @param object $wp_admin_bar array of the admin bar.
	 * @name mwb_crp_report_button_link
	 * @since    1.0.0
	 */
	public function mwb_crp_report_button_link( $wp_admin_bar ) {
		$args = array(
			'id'    => 'mwb-crp-report-button',
			'title' => __( 'CRP Reports', 'coupon-referral-program' ),
			'href'  => admin_url( 'admin.php?page=wc-reports&tab=crp_report' ),
			'meta'  => array(
				'class' => 'mwb-crp-report-button',
			),
		);
		$wp_admin_bar->add_node( $args );
	}

	/**
	 * Get all products.
	 *
	 * @name mwb_crp_get_all_products
	 * @since    1.5.0
	 */
	public function mwb_crp_get_all_products() {
		$mwb_crp_product_data = array();
		$args                 = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'DESC',
		);
		$mwb_crp_all_products = get_posts( $args );
		if ( isset( $mwb_crp_all_products ) && ! empty( $mwb_crp_all_products ) && is_array( $mwb_crp_all_products ) ) {
			foreach ( $mwb_crp_all_products as $key => $mwb_product ) {
				$mwb_crp_product_data[ $mwb_product->ID ] = $mwb_product->post_title;
			}
		}
		return $mwb_crp_product_data;
	}

	/**
	 * This function is used to compatibilty with WPML.
	 *
	 * @name mwb_crp_setting_compatibility_wpml.
	 * @since    1.6.0
	 */
	public function mwb_crp_setting_compatibility_wpml() {
		/**
		 * Wpml_multilingual_options.
		 *
		 * @since 1.6.4
		 * @param string referral_button_page .
		 */
		do_action( 'wpml_multilingual_options', 'referral_button_page' );
		/**
		 * Wpml_multilingual_options.
		 *
		 * @since 1.6.4
		 * @param string mwb_crp_include_pro .
		 */
		do_action( 'wpml_multilingual_options', 'mwb_crp_include_pro' );
		/**
		 * Wpml_multilingual_options.
		 *
		 * @since 1.6.4
		 * @param string mwb_crp_exclude_pro .
		 */
		do_action( 'wpml_multilingual_options', 'mwb_crp_exclude_pro' );

	}

	/**
	 * This function is used to sanitize referral length key.
	 *
	 * @param int $value value of settings.
	 * @param int $option value of settings. 
	 * @param int $raw_value value of settings.
	 * @name mwb_crp_referral_length_sanitize_option.
	 * @since    1.6.0
	 */
	public function mwb_crp_referral_length_sanitize_option( $value, $option, $raw_value ) {

		return is_null( $raw_value ) ? 7 : absint( $raw_value );
	}

	/**
	 * This function is used to sanitize referral link expiry.
	 *
	 * @param int $value value of settings.
	 * @param int $option value of settings.
	 * @param int $raw_value value of settings.
	 * @name mwb_cpr_ref_link_expiry_sanitize_option.
	 * @since    1.6.0
	 */
	public function mwb_cpr_ref_link_expiry_sanitize_option( $value, $option, $raw_value ) {

		return is_null( $raw_value ) ? 365 : absint( $raw_value );
	}

	/**
	 * This function is used to add deactivation popup.
	 *
	 * @param array $valid_screens value of settings.
	 * @name add_mwb_deactivation_screens.
	 * @since    1.6.0
	 */
	public function add_mwb_deactivation_screens( $valid_screens = array() ) {
		if ( is_array( $valid_screens ) ) {
			// Push your screen here.
			array_push( $valid_screens, 'coupon-referral-program' );
		}
		return $valid_screens;
	}

	/**
	 * Show referral details in order edit page.
	 *
	 * @param object $order .
	 */
	public function mwb_crp_woocommerce_after_order_itemmeta( $order ) {
		$refree_id = get_post_meta( $order->get_id(), 'referral_has_rewarded', true );
		$user      = get_user_by( 'ID', $refree_id );
		$prof_url  = get_edit_profile_url( $refree_id );
		if ( ! empty( $refree_id ) && ! empty( $user ) ) :
			?>
		<div class="form-field form-field-wide">
			<h3><b>Referred by user:</b></h3>
			<b><a href="<?php echo esc_html( $prof_url ); ?>"><?php echo esc_html( $user->user_email ); ?></a></b>
		</div>
			<?php
		endif;
	}

	/**
	 * Used to generate report.
	 *
	 * @return void
	 */
	public function wps_crp_export_report_callback() {
		if ( isset( $_GET['wps_crp_export_report'] ) && ! empty( $_GET['wps_crp_export_report'] ) ) { // phpcs:ignore
			$upload_dir_path     = wp_upload_dir()['basedir'] . '/';
			$log_referral_folder = 'wps_crp_csv_report/';
			$import_referral_dir = $upload_dir_path . $log_referral_folder;
			$filename            = 'wps_crp_referral_report.csv';
			if ( ! is_dir( $import_referral_dir ) ) {
				mkdir( $import_referral_dir, $permissions = 0777 );
			}

			$output = fopen( $import_referral_dir . $filename, 'w' );
			$title = array(
				'user_id'        => __( 'User ID', 'woocommerce-subscriptions-pro' ),
				'username'       => __( 'User Name', 'woocommerce-subscriptions-pro' ),
				'user_email'     => __( 'Email', 'woocommerce-subscriptions-pro' ),
				'referred_users' => __( 'Referred Users', 'woocommerce-subscriptions-pro' ),
				'utilize'        => __( 'Total Utilization', 'woocommerce-subscriptions-pro' ),
				'no_of_coupons'  => __( 'Total no of Coupon', 'woocommerce-subscriptions-pro' ),
				'coupon_data'    => __( 'Coupons Data', 'woocommerce-subscriptions-pro' ),
			);
			fputcsv( $output, $title, ',' );

			$users              = get_users( array( 'fields' => array( 'ID' ) ) );
			$mwb_crp_data_array = array();
			foreach ( $users as $user_id ) {
				$user_id                   = $user_id->ID;
				$crp_public_obj            = new Coupon_Referral_Program_Public( 'coupon-referral-program', '1.6.5' );
				$users_crp_data            = $crp_public_obj->get_revenue( $user_id );
				$mwb_crp_user_name         = get_userdata( $user_id )->data->display_name;
				$mwb_crp_user_email        = get_userdata( $user_id )->data->user_email;
				$get_utilize_coupon_amount = $crp_public_obj->get_utilize_coupon_amount( $user_id );
				$mwb_crp_data              = array(
					'id'             => $user_id,
					'user_name'      => $mwb_crp_user_name,
					'user_email'     => $mwb_crp_user_email,
					'referred_users' => $users_crp_data['referred_users'],
					'utilize'        => $get_utilize_coupon_amount,
					'no_of_coupons'  => $users_crp_data['total_coupon'],
					'coupon_data'    => wp_json_encode( $this->wps_crp_user_coupon_data( $user_id ) ),
				);
				if ( ! empty( $mwb_crp_data ) && is_array( $mwb_crp_data ) ) {
					fputcsv( $output, $mwb_crp_data, ',' );
					array_push( $mwb_crp_data_array, $mwb_crp_data );
				}
			}

			$path_of_file_to_download = $import_referral_dir . $filename;
			if ( file_exists( $path_of_file_to_download ) ) {
				header( 'Content-Description: File Transfer' );
				header( 'Content-Type: application/csv' );
				header( 'Content-Disposition: attachment; filename="' . basename( $path_of_file_to_download ) . '"' );
				header( 'Expires: 0' );
				header( 'Cache-Control: must-revalidate' );
				header( 'Pragma: public' );
				header( 'Content-Length: ' . filesize( $path_of_file_to_download ) );
				readfile( $path_of_file_to_download );
				exit;
			}
		}
	}

	/**
	 * Get all of user's coupon data
	 *
	 * @param integer $user_id .
	 * @return user_coupon_data_all
	 */
	public function wps_crp_user_coupon_data( $user_id ) {
		$user_coupon_data_all = array();
		$crp_public_obj       = new Coupon_Referral_Program_Public( 'coupon-referral-program', '1.6.5' );
		if ( ! empty( $crp_public_obj->get_signup_coupon( $user_id ) ) && is_array( $crp_public_obj->get_signup_coupon( $user_id ) ) ) {
			$signup_coupon  = $crp_public_obj->get_signup_coupon( $user_id );
			$coupon = new WC_Coupon( $signup_coupon['singup'] );
			if ( 'publish' == get_post_status( $signup_coupon['singup'] ) ) {
				$coupon_code   = esc_html( $coupon->get_code() );
				$coupon_amount = $coupon->get_amount();
				$usage_count   = $coupon->get_usage_count();
				$event         = 'Signup Coupon';

				$user_coupon_data['coupon_code']   = $coupon_code;
				$user_coupon_data['coupon_amount'] = $coupon_amount;
				$user_coupon_data['usage_count']   = $usage_count;
				$user_coupon_data['event']         = $event;

				$user_coupon_data_all[] = $user_coupon_data;
			}
		}
		if ( ! empty( $crp_public_obj->mwb_crp_get_referal_signup_coupon( $user_id ) ) ) {
			foreach ( $crp_public_obj->mwb_crp_get_referal_signup_coupon( $user_id ) as $coupon_code => $user_id_crp_coupon ) {
				$user_id_crp_coupon = esc_html( $user_id_crp_coupon );
				$coupon             = new WC_Coupon( $coupon_code );
				$flag               = false;
				if ( 'publish' === get_post_status( $coupon_code ) ) {
					$coupon_code   = esc_html( $coupon->get_code() );
					$coupon_amount = $coupon->get_amount();
					$usage_count   = $coupon->get_usage_count();
					$event         = 'Referral Signup';

					$user_coupon_data['coupon_code']   = $coupon_code;
					$user_coupon_data['coupon_amount'] = $coupon_amount;
					$user_coupon_data['usage_count']   = $usage_count;
					$user_coupon_data['event']         = $event;

					$user_coupon_data_all[] = $user_coupon_data;
				}
			}
		}
		if ( ! empty( $crp_public_obj->get_referral_purchase_coupons( $user_id ) ) ) {
			foreach ( $crp_public_obj->get_referral_purchase_coupons( $user_id ) as $coupon_code => $user_id_crp_coupon ) {
				$coupon   = new WC_Coupon( $coupon_code );
				$flag     = false;
				$order_id = get_post_meta( $coupon->get_id(), 'coupon_created_to', true );
				if ( 'publish' === get_post_status( $coupon_code ) ) {
					$coupon_code   = esc_html( $coupon->get_code() );
					$coupon_amount = $coupon->get_amount();
					$usage_count   = $coupon->get_usage_count();
					$event         = 'Referral Purchase For #' . esc_html( $order_id );

					$user_coupon_data['coupon_code']   = $coupon_code;
					$user_coupon_data['coupon_amount'] = $coupon_amount;
					$user_coupon_data['usage_count']   = $usage_count;
					$user_coupon_data['event']         = $event;

					$user_coupon_data_all[] = $user_coupon_data;
				}
			}
		}
		if ( ! empty( $crp_public_obj->get_referral_purchase_coupons_on_guest( $user_id ) ) ) {
			foreach ( $crp_public_obj->get_referral_purchase_coupons_on_guest( $user_id ) as $coupon_code => $email ) {
				$coupon   = new WC_Coupon( $coupon_code );
				$order_id = get_post_meta( $coupon->get_id(), 'coupon_created_to', true );
				if ( 'publish' === get_post_status( $coupon_code ) ) {
					$coupon_code   = esc_html( $coupon->get_code() );
					$coupon_amount = $coupon->get_amount();
					$usage_count   = $coupon->get_usage_count();
					$event         = 'Referral Purchase Via Guest User For #' . esc_html( $order_id );

					$user_coupon_data['coupon_code']   = $coupon_code;
					$user_coupon_data['coupon_amount'] = $coupon_amount;
					$user_coupon_data['usage_count']   = $usage_count;
					$user_coupon_data['event']         = $event;

					$user_coupon_data_all[] = $user_coupon_data;
				}
			}
		}
		return $user_coupon_data_all;
	}

	/** Function to send the referral reminder email */
	public function wps_crp_send_reminder_email_callback() {
		check_ajax_referer( 'wps_crp_report_nonce', 'nonce' );

		$user_id = isset( $_POST['user_id'] ) ? sanitize_text_field( wp_unslash( $_POST['user_id'] ) ) : 0;

		$user_reference_key = get_user_meta( $user_id, 'referral_key', true );
		if ( ! empty( $user_id ) && ! empty( $user_reference_key ) ) {
			/**
			 * Filter for the site url link
			 *
			 * @since 1.6.4
			 * @param string site_url() .
			 */
			$page_permalink  = apply_filters( 'mwb_crp_referral_link_url', site_url() );
			$referral_link   = $page_permalink . '?ref=' . $user_reference_key;
			$user            = get_user_by( 'ID', $user_id );
			$recipient_email = $user->user_email;
			$customer_email  = WC()->mailer()->emails['crp_referral_reminder_email'];
			$customer_email->trigger( $user_id, $referral_link, $recipient_email, $user_reference_key );
			$res = true;

		} else {
			$res = false;
		}
		echo esc_html( $res );
		wp_die();
	}
}
