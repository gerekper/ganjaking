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
 * @author     Makewebbetter
 */
class Coupon_Referral_Program_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
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
		if ( 'woocommerce_page_wc-settings' === $hook ) {

			wp_enqueue_style( $this->plugin_name, COUPON_REFERRAL_PROGRAM_DIR_URL . 'admin/css/coupon-referral-program-admin.css', array(), $this->version, 'all' );

			/* Enqueue style for using WooCommerce Tooltip.*/
			wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );

			if ( isset( $_GET['section'] ) && 'crp_help' === $_GET['section'] ) {
						 $custom_css = "
		                p.submit{
		                        display: none !important;
		                }";
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
			wp_enqueue_script( $this->plugin_name . 'admin-js', COUPON_REFERRAL_PROGRAM_DIR_URL . 'admin/js/crp-admin.js', $this->version, false );

			/* Enqueue and Localize script for using WooCommerce Tooltip.*/

			wp_enqueue_script( 'woocommerce_admin', WC()->plugin_url() . '/assets/js/admin/woocommerce_admin.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip', 'wc-enhanced-select' ), WC_VERSION );
			$params      = array(
				'strings' => '',
				'urls'    => '',
			);
			$translation = array( 'img_url' => COUPON_REFERRAL_PROGRAM_DIR_URL . 'public/images/bg.png' );
			wp_enqueue_media();

			wp_localize_script( 'woocommerce_admin', 'woocommerce_admin', $params );
			wp_localize_script( $this->plugin_name . 'admin-js', 'woocommerce_img', $translation );
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

		woocommerce_admin_fields( self::crp_get_settings( $current_section ) );
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
					'title' => __( 'General referrals ', 'coupon-referral-program' ),
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
					'title'   => __( 'Enable discount coupon on referral purchase.', 'coupon-referral-program' ),
					'desc'    => __( 'Enable this setting for allow customer to get coupon on referral purchase ', 'coupon-referral-program' ),
					'default' => 'yes',
					'type'    => 'checkbox',
					'id'      => 'mwb_crp_enable_referal_purchase',
				),
				array(
					'title'             => __( 'Referral key length', 'coupon-referral-program' ),
					'default'           => 7,
					'type'              => 'number',
					'custom_attributes' => array(
						'min' => '7',
						'max' => '10',
					),
					'id'                => 'mwb_referral_length',
					'class'             => 'mwb_crp_input_val',
					'desc_tip'          => __( 'Set the length for referral key. The minimum & maximum length a referral key can have are 7 & 10.', 'coupon-referral-program' ),
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
					'title'    => __( 'Pop-up image', 'coupon-referral-program' ),
					'type'     => 'text',
					'default'  => self::get_selected_image(),
					'id'       => 'mwb_cpr_image',
					'desc_tip' => __( 'Select the background image for your popup', 'coupon-referral-program' ),
					'desc'     => '<div class="mwb_crp_image"><button class="mwb_crp_image_button button ">' . __( 'Upload', 'coupon-referral-program' ) . '</button><button class="mwb_crp_image_resetbtn button">' . __( 'Reset', 'coupon-referral-program' ) . '</button></div><div class="mwb_cpr_image_display"><img id="mwb_cpr_image_display" width="100" height="100" src="' . self::get_selected_image() . '"></div>',
				),
				array(
					'title'             => __( 'Set days to remember referrals', 'coupon-referral-program' ),
					'default'           => 365,
					'type'              => 'number',
					'custom_attributes' => array( 'min' => '1' ),
					'id'                => 'mwb_cpr_ref_link_expiry',
					'class'             => 'mwb_crp_input_val',
					'desc_tip'          => __( 'After entred days referrals will not be treated as referred user.', 'coupon-referral-program' ),
				),

				array(
					'type' => 'sectionend',
					'id'   => 'general_options',
				),

				array(
					'title' => __( 'Coupon amount calculation on referral purchase', 'coupon-referral-program' ),
					'type'  => 'title',
					'desc'  => __( 'The following options will be used for calculation of the referral purchase coupon amount.', 'coupon-referral-program' ) .
					'<br><b>' . __( 'For Example:-', 'coupon-referral-program' ) . '</b><br>' .
					__( '1- If you select ', 'coupon-referral-program' ) . '<b>' . __( ' “Referral purchase discount amount type” = Percentage', 'coupon-referral-program' ) . '</b>' . __( ' and “Referral Purchase Discount” = 20. Now, the Referred customer has place order of amount $200 then a referral coupon amount will be 40(200*20%) up to “Referral purchase discount amount upto” value.', 'coupon-referral-program' ) . '<br>' . __( '2- If you select ', 'coupon-referral-program' ) . '<b>' . __( '“Referral purchase discount amount type” = Fixed', 'coupon-referral-program' ) . '</b>' . __(
						' and “Referral Purchase Discount” = 20. Then, no matter of order total, it will be 20.
						For the coupon type(Fixed/Percentage) visit ',
						'coupon-referral-program'
					) . '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=crp-referral_setting&section=coupon', 'coupon-referral-program' ) . '">' . __( ' Coupon configuration', 'coupon-referral-program' ) . '</a>' . __( ' section.', 'coupon-referral-program' ),
					'id'    => 'coupon_amount_calculation',
				),
				array(
					'title'    => __( 'Referral purchase discount amount type', 'coupon-referral-program' ),
					'default'  => 1,
					'type'     => 'select',
					'id'       => 'referral_discount_type',
					'class'    => 'mwb_crp_input_val',
					'css'      => 'width:25%',
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
					'desc'              => __( 'Set the max referral coupon value (max coupon amount) you want to provide to your customers when the referral customer purchase from your store.', 'coupon-referral-program' ) . '<div>' . __( 'Note- If you don’t want to use “Referral discount amount upto” functionality then for this you have to set “Referral discount amount upto ” to 0.', 'coupon-referral-program' ) . '</div>',
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
					'type' => 'sectionend',
					'id'   => 'general_options',
				),
			);
		}

		if ( 'social' === $current_section ) {
			$settings = array(
				array(
					'title' => __( 'Social sharing', 'coupon-referral-program' ),
					'type'  => 'title',
				),

				array(
					'title'   => __( 'Enable/Disable social sharing', 'coupon-referral-program' ),
					'desc'    => __( 'Enable/Disable social sharing', 'coupon-referral-program' ),
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
					'title'    => __( 'Use this shortcode for the referral link', 'coupon-referral-program' ),
					'type'     => 'text',
					'desc_tip' => __( 'You can use the given shortcode anywhere you want, it will display the referral link of your customers', 'coupon-referral-program' ),
					'default'  => '[crp_referral_link]',
					'id'       => 'mwb_crp_referral_link',
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
					'title'   => __( 'Enable/Disable discount', 'coupon-referral-program' ),
					'desc'    => __( 'Enable/Disable discount coupon on sign up', 'coupon-referral-program' ),
					'desc_tip'=> __( 'Enable this settings to give discount coupon for new user signup.', 'coupon-referral-program' ),
					'default' => 'no',
					'type'    => 'checkbox',
					'id'      => 'mwb_crp_signup_enable',
				),
				array(
					'title'   => __( 'Select users', 'woocommerce' ),
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
					'type' => 'sectionend',
				),
				array(
					'title'=>__( 'Referral signup discount for referee', 'coupon-referral-program' ),
					'type'=>'title',
				),
				//referee discount.
				array(
					'title'   => __( 'Enable/Disable discount', 'coupon-referral-program' ),
					'desc'    => __( 'Referral signup discount for referee', 'coupon-referral-program' ),
					'default' => 'no',
					'type'    => 'checkbox',
					'id'      => 'mwb_crp_refree_discount_enable',
					'desc_tip' => __( 'By using this setting you can give the discount coupon for refree when referrrd user signup using referral link.', 'coupon-referral-program' ),
				),
				array(
					'title'             => __( 'Enter discount for refree', 'coupon-referral-program' ),
					'default'           => 1,
					'type'              => 'number',
					'desc_tip'          => __( 'The value you enter will be set as discount coupon amount for refree.', 'coupon-referral-program' ),
					'id'                => 'refree_discount_value',
					'class'             => 'mwb_crp_input_val',
					'custom_attributes' => array( 'min' => 1 ),
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
					'title'    => __( 'Referral Purchase Coupon type', 'coupon-referral-program' ),
					'default'  => 1,
					'type'     => 'select',
					'id'       => 'signup_discount_type',
					'css'      => 'width:25%',
					'options'  => array(
						'mwb_cpr_fixed'   => __( 'Fixed', 'coupon-referral-program' ),
						'mwb_cpr_percent' => __( 'Percentage', 'coupon-referral-program' ),
					),
					'desc_tip' => __( 'Select the type for your referral purchase discount coupon for refree.', 'coupon-referral-program' ),
					'desc' => __( 'The referee will get the selected coupon type on the referral purchase.', 'coupon-referral-program' ),

				),
				array(
					'title'    => __('Signup Coupon type', 'coupon-referral-program'),
					'default'  => 1,
					'type'     => 'select',
					'id'       => 'signup_discount_coupon_type',
					'css'      => 'width:38.5%',
					'options'  => array(
						'mwb_cpr_fixed'   => __('Fixed', 'coupon-referral-program'),
						'mwb_cpr_percent' => __('Percentage', 'coupon-referral-program'),
					),
					'desc_tip' => __('Select the type for your signup discount coupon which you want to offer for your customers.', 'coupon-referral-program'),
					'desc' => __( 'The customer will get the selected coupon type on the signup.', 'coupon-referral-program' ),
				),
				array(
					'title'    => __( 'Referral Coupon type', 'coupon-referral-program' ),
					'default'  => 1,
					'type'     => 'select',
					'id'       => 'referee_discount_type',
					'css'      => 'width:38.5%',
					'options'  => array(
						'mwb_cpr_fixed'   => __( 'Fixed', 'coupon-referral-program' ),
						'mwb_cpr_percent' => __( 'Percentage', 'coupon-referral-program' ),
					),
					'desc_tip' => __( 'Select the type for your signup discount coupon for refree when refferd user signup using refferd link.' ),
					'desc' => __( 'The referee will get the selected coupon type on the referral signup.', 'coupon-referral-program' ),
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
					'title'             => __( 'Enter coupon length', 'coupon-referral-program' ),
					'default'           => 5,
					'type'              => 'number',
					'id'                => 'coupon_length',
					'class'             => 'mwb_crp_input_val',
					'custom_attributes' => array(
						'min' => 5,
						'max' => 10,
					),
					'desc_tip'          => __(
						'Set the coupon length excluding the prefix.
				(The minimum length you can set is 5)',
						'coupon-referral-program'
					),
				),

				array(
					'title'             => __( 'Coupon expire after days', 'coupon-referral-program' ),
					'default'           => 0,
					'type'              => 'number',
					'id'                => 'coupon_expiry',
					'class'             => 'mwb_crp_input_val',
					'custom_attributes' => array( 'min' => 0 ),
					'desc_tip'          => __( 'Enter the days after which the coupon will get expire. Set the value to “1” if the coupon will expire in one-day And set the value to “0” if the coupon has no fix expiry date.', 'coupon-referral-program' ),
				),

				array(
					'title'    => __( 'No of time coupon can be use', 'coupon-referral-program' ),
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
					'desc_tip' => __( 'If you desire to add a prefix to your coupon, you can add here.', 'coupon-referral-program' ),
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
					'title'   => __( 'Enter bonus points for  the reffral signup ', 'coupon-referral-program' ),
					'default' => '1',
					'type'    => 'number',
					'id'      => 'mwb_crp_points_rewards_signup_points',
				),
				array(
					'title'   => __( 'Enter points for  reffral purchase', 'coupon-referral-program' ),
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
					'type' => 'sectionend',
				),
			);
		}
		if ( 'reports' === $current_section ) {
			wp_redirect( admin_url( 'admin.php?page=wc-reports&tab=crp_report' ) );
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

						<a class="mwb_crp_help_link" href="https://docs.woocommerce.com/document/coupon-referral-program" target="_blank"><?php esc_html_e( 'Documentation', 'coupon-referral-program' ); ?></a>
						<a class="mwb_crp_help_link" href="https://makewebbetter.com/contact-us/" target="_blank"><?php esc_html_e( 'Contact us', 'coupon-referral-program' ); ?></a>
						<a class="mwb_crp_help_link" href="https://join.skype.com/invite/IKVeNkLHebpC" target="_blank">
						<img src="<?php echo esc_url( COUPON_REFERRAL_PROGRAM_DIR_URL . 'admin/images/skype_logo.png' ); ?>" style="height: 15px;width: 15px;"><?php esc_html_e('Chat now','coupon-referral-program'); ?></a>
					</p>
				</div>
				<div class="mwb_crp_video_wrapper">
					<iframe height="400px" width="60%" src="https://www.youtube.com/embed/YzE6cp6KSxo" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
				</div>
			</div>
			<?php 
		}
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
		$referral_button_text = get_option( 'referral_button_text', 'Referral Program' );
		return $referral_button_text;
	}

	/**
	 * Get the discount type
	 *
	 * @since    1.0.0
	 */
	public static function get_discount_type() {
		$referral_discount_type = get_option( 'signup_discount_type', 'fixed' );
		if ( $referral_discount_type == 'mwb_cpr_percent' ) {
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
		$sections = self::crp_get_sections();

		echo '<ul class="subsubsub">';

		$array_keys = array_keys( $sections );

		foreach ( $sections as $id => $label ) {
			echo '<li><a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=crp-referral_setting&section=' . sanitize_title( $id ) ) ) . '" class="' . ( $current_section == $id ? 'current' : '' ) . '">' . $label . '</a> ' . ( end( $array_keys ) == $id ? '' : '|' ) . ' </li>';//phpcs-ignore 
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
			''        => __( 'General referrals', 'coupon-referral-program' ),
			'social'  => __( 'Social sharing', 'coupon-referral-program' ),
			'signup'  => __( 'Sign up discount', 'coupon-referral-program' ),
			'coupon'  => __( 'Coupon configuration', 'coupon-referral-program' ),
			'display' => __( 'Display configuration', 'coupon-referral-program' ),
			'reports' => __( 'Reports', 'coupon-referral-program' ),

		);
		if ( is_plugin_active( 'woocommerce-points-and-rewards/woocommerce-points-and-rewards.php' ) ) {

			$sections['points_rewards'] = __( 'Points & Rewards', 'coupon-referral-program' );
		}
		if ( is_plugin_active( 'woocommerce-subscriptions/woocommerce-subscriptions.php' ) ) {

			$sections['woocommerce_subscription'] = __( 'Woocommerce subscriptions', 'coupon-referral-program' );
		}
		return apply_filters( 'crp_get_sections', $sections );
	}
	public function mwb_crp_help_section($mwb_crp_help_section){
		$mwb_crp_help_section['crp_help'] = __( 'Help and support', 'coupon-referral-program' );
		return $mwb_crp_help_section;
	}

	/**
	 * Set the default value for the number if they are blank
	 *
	 * @param array $parm array of the post settings
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
		include COUPON_REFERRAL_PROGRAM_DIR_PATH . '/admin/partials/coupon-referral-program-admin-display-report.php';

	}

	/**
	 * Add CRP Report button in the admin menu
	 *
	 * @param array $wp_admin_bar array of the admin bar.
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
}
