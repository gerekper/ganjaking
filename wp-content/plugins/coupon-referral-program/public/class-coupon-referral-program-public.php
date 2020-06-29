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

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    coupon-referral-program
 * @subpackage coupon-referral-program/public
 * @author     WooCommerce
 */
class Coupon_Referral_Program_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		

	}
	/**
	 * Register the shortcodes.
	 *
	 * @since    1.0.0
	 */
	public function woocommerce_register_shortcode() {
		if( $this->check_shortcode_is_enable() ) {

			add_shortcode( 'crp_popup_button', array( $this ,'mwb_crp_referral_button_shortcode') );
		}

		add_shortcode('crp_referral_link', array( $this, 'mwb_crp_referral_link_shortcode') );
		/*===========Add Rewrite Rule============*/
		flush_rewrite_rules();//phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules
		add_rewrite_endpoint( 'referral_coupons', EP_PAGES );
	}
	/**
	 * Display referral link using shortcode.
	 *
	 * @since    1.0.0
	 */
	public function mwb_crp_referral_link_shortcode() {
		if( $this->is_social_sharing_enabled() 
			&& is_user_logged_in()) 
		{

			$user_ID = get_current_user_ID();
			$mwb_crp_link_html = "<fieldset><code>
			".$this->get_referral_link($user_ID)."
		     </code></fieldset>";
		    return $mwb_crp_link_html;
	    }
    }
	/**
	 * Display the referral button for the shortcode.
	 *
	 * @since    1.0.0
	 */
	public function mwb_crp_referral_button_shortcode()
	{
		$user_ID = get_current_user_ID();

		$user = new WP_User( $user_ID );

		if( !in_array('administrator', $user->roles)) {

			?>
			<a id="mwb_crp_shortcode_btn"href="javascript:;" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored modal__trigger mwb-pr-drag-btn " data-modal="#mwb_modal"style="background-color: <?php echo Coupon_Referral_Program_Admin::get_selected_color();?>"><?php echo Coupon_Referral_Program_Admin::get_visible_text();?></a>
			<?php 
		}
	}
	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		if($this->is_selected_page()||$this->check_shortcode_is_enable() )
		{
			wp_enqueue_style( $this->plugin_name, COUPON_REFERRAL_PROGRAM_DIR_URL . 'public/css/coupon-referral-program-public.css', array(), $this->version, 'all' );

			wp_enqueue_style( 'material_modal', COUPON_REFERRAL_PROGRAM_DIR_URL . 'modal/css/material-modal.css' );

			wp_enqueue_style( 'modal_style', COUPON_REFERRAL_PROGRAM_DIR_URL . 'modal/css/style.css' );
		}
		if(is_account_page()){

			wp_enqueue_style( 'account_page', COUPON_REFERRAL_PROGRAM_DIR_URL . 'public/css/coupon-referral-program-public.css', array(), $this->version, 'all' );
		}
		
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if($this->is_selected_page()||$this->check_shortcode_is_enable()|| is_account_page())
		{
			$mwb_crp_animation = get_option('mwb_crp_animation',false);

			$mwb_crp_arr = array(
				'mwb_crp_animation'=> $mwb_crp_animation,
				'is_account_page'  => is_account_page(),
				'Showing_page'	   => __("Showing page _PAGE_ of _PAGES_","coupon-referral-program"),
				"no_record"			=> __("No records available","coupon-referral-program"),
				"nothing_found"		=> __("Nothing found","coupon-referral-program"),
				"display_record"	=> __("Display _MENU_ Entries","coupon-referral-program"),
				"filtered_info"		=> __("(filtered from _MAX_ total records)","coupon-referral-program"),
				"search"			=>__("Search","coupon-referral-program"),
				"previous"			=>__("Previous","coupon-referral-program"),
				"next"			    =>__("Next","coupon-referral-program"),
				'mwb_crp_nonce' =>  wp_create_nonce( "mwb-crp-verify-nonce" ),
				'ajaxurl' => admin_url('admin-ajax.php'),
				'apply_text' => __('Apply','coupon-referral-program'),
				'remove_text' => __('Remove','coupon-referral-program'),
				'apply' => __('Apply','coupon-referral-program'),
				);

			wp_enqueue_script("datatables","//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js",array( 'jquery' ), $this->version, true);
			wp_register_script($this->plugin_name, COUPON_REFERRAL_PROGRAM_DIR_URL . 'public/js/coupon-referral-program-public.js', array( 'jquery',"datatables","jquery-ui-draggable"), $this->version, false );
			wp_localize_script($this->plugin_name, 'mwb_crp', $mwb_crp_arr );
			wp_enqueue_script( $this->plugin_name );

			wp_enqueue_script( 'mwb_materal_modal_min', COUPON_REFERRAL_PROGRAM_DIR_URL . 'modal/js/material-modal.min.js', array( 'jquery' ), $this->version, true );

			wp_register_script("mwb_wpr_clipboard", COUPON_REFERRAL_PROGRAM_DIR_URL."public/js/dist/clipboard.min.js");
			
			wp_enqueue_script('mwb_wpr_clipboard' );
		}
	
	}
	/**
	 * Shortcode for the button.
	 *
	 * @since    1.0.0
	 */
	public function mwb_crp_referral_button()
	{ 
		$user_ID = get_current_user_ID();
		$user = new WP_User( $user_ID );
		if( !in_array('administrator', $user->roles) && $this->is_popup_button_enable()) { 

			?>
			<style type="text/css"><?php echo self::get_custom_style_popup_btn();?></style>
			<a href="javascript:;" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored modal__trigger mwb-pr-drag-btn <?php echo self::get_position_popup_button(); ?>" data-modal="#mwb_modal" id="mwb-cpr-drag" class="animated slideInDown" style="background-color: <?php echo Coupon_Referral_Program_Admin::get_selected_color();?>"><?php echo Coupon_Referral_Program_Admin::get_visible_text();?></a>
			<?php 
		}
	}
	/**
	 * Including the html for render the button as well as the popup style
	 *
	 * @since 1.0.0
	 */
	public function mwb_crp_load_html(){
		$user_ID = get_current_user_ID();
		$user = new WP_User( $user_ID );
		if( !in_array('administrator', $user->roles) 
			&& ($this->is_selected_page()
				|| $this->check_shortcode_is_enable()) ) {

			include_once COUPON_REFERRAL_PROGRAM_DIR_PATH.'modal/referral_program_notify.php';
	}
	include_once COUPON_REFERRAL_PROGRAM_DIR_PATH.'modal/apply_coupon_on_subscriptions.php';

}
	/**
	 * get custom style of the button
	 *
	 * @since 1.0.0
	 */
	public static function get_custom_style_popup_btn()
	{
		$mwb_custom_style = get_option("referral_button_custom_css",false);
		return $mwb_custom_style;
	}
	/**
	 * Check is popup button is enable
	 *
	 * @since 1.0.0
	 */
	public function is_popup_button_enable()
	{   
		$is_enable = false;

		$mwb_check_popup = get_option("mwb_cpr_button_enable","yes");
		if( !empty($mwb_check_popup) && $mwb_check_popup == 'yes') {

			$is_enable = true;
		}

		return $is_enable;
	}
	/**
	 * Show button for the selected pages
	 *
	 * @since 1.0.0
	 */
	public function woocommerce_referral_button_show()
	{
		if($this->is_selected_page()) {

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

		$is_selected = false;
		$mwb_selected_pages = array();
		$mwb_selected_pages = $this->mwb_get_selected_pages();

		if( empty($mwb_selected_pages) && !$this->check_shortcode_is_enable()) {

			$is_selected = true;
		}
		elseif (is_single()&&!$this->check_shortcode_is_enable()&&!empty($mwb_selected_pages)) {

			$page_id ='details';
			if(in_array($page_id, $mwb_selected_pages)){

				$is_selected = true;
			}
		}
		elseif(empty($mwb_selected_pages) &&!$this->check_shortcode_is_enable()) {

			$is_selected = true;
		}
		elseif(!is_shop()&&!is_home() &&!empty($mwb_selected_pages)&&!$this->check_shortcode_is_enable()) {

			$page = $wp_query->get_queried_object(); 
			$page_id = $page->ID;

			if(in_array($page_id, $mwb_selected_pages)) {

				$is_selected = true;
			}
			
		}
		elseif (is_shop()&&!$this->check_shortcode_is_enable()&&!empty($mwb_selected_pages)) {
			$page_id = wc_get_page_id('shop');

			if(in_array($page_id, $mwb_selected_pages)) {

				$is_selected = true;
			}

		}
		
		else {

			$is_selected = false;
		}

		return $is_selected;
	}

	/**
	 * Get a referral link.
	 *
	 * @since 1.0.0
	 * @param $user_id for which the referral link needs to be generated
	 * @return referral link
	 */
	public function get_referral_link($user_id) {
		$referral_link = '';
		if(isset($user_id) && !empty($user_id)) {
			$referral_key = get_user_meta($user_id,'referral_key',true);
			if(empty($referral_key)) {
				$referral_key = $this->set_referral_key($user_id);
			}
			$referral_link = site_url().'?ref='.$referral_key;
		}
		return $referral_link;
	}

	/**
	 * Get a referral link.
	 *
	 * @since 1.0.0
	 * @param $user_id for which the referral link needs to be set
	 * @return referral link
	 */
	public function set_referral_key($user_id) {
		$generated_key = generate_referral_key();
		update_user_meta($user_id,'referral_key',$generated_key);
		return $generated_key;
	}

	/**
	 * Provides Discount coupon to each registered user if required is enabled
	 *
	 * @since 1.0.0
	 * @param $user_id is the customer id who has registered himself successfully
	 */
	public function woocommerce_created_customer_discount($user_id) {

		// return if user role is not customer.
		if ( ! user_can( $user_id, 'customer' ) ) {
			return;
		}

		if($this->check_signup_is_enable() && !$this->check_reffral_signup_is_enable()) {
			if(!self::check_is_points_rewards_enable()) {
				$coupon_code = $this->mwb_create_coupon_send_email($user_id);
				$this->save_singup_coupon_code('singup',$coupon_code,$user_id);
			}
		}
		/* ============== Set the referre user id to current user data ============= */
		$enable_plugin = is_enable_coupon_referral_program();
		if($enable_plugin) {
			$cookie_val = isset($_COOKIE['mwb_cpr_cookie_set']) ? unserialize(base64_decode($_COOKIE['mwb_cpr_cookie_set'])): '';
			if(isset($cookie_val['referral_key'])) {

				$retrive_data = $cookie_val['referral_key'];	
			}
			if(!empty($retrive_data)){				
				$args['meta_query'] = array(						
					array(
						'key'=>'referral_key',
						'value'=>trim($retrive_data),
						'compare'=>'=='
						)
					);
				$referral_user_data = get_users( $args );
				$refree_id = $referral_user_data[0]->data->ID;
				$referral_user = get_user_by('ID',$refree_id);
				$referral_email = $this->get_user_email($referral_user);
				if(isset($refree_id) && !empty($refree_id) && !empty($user_id)){
					update_user_meta($user_id,'mwb_cpr_user_referred_by',$refree_id);
					if($this->check_signup_is_enable() && $this->check_reffral_signup_is_enable()){
						if(!self::check_is_points_rewards_enable()) {
							$coupon_code = $this->mwb_create_coupon_send_email($user_id);
							$this->save_singup_coupon_code('singup',$coupon_code,$user_id);
						}
						else {
							$points = $this->get_points_for_signup();
							WC_Points_Rewards_Manager::increase_points( $user_id, $points, 'reffral-account-signup' );
						}
					}
					//referal signup.
					if( $this->check_reffre_signup_is_enable()){
						if(!self::check_is_points_rewards_enable()) {
							$coupon_code = $this->mwb_create_coupon_send_email_for_refree($refree_id);
							$this->save_referal_singup_coupon_code($coupon_code,$refree_id,$user_id);
						}
						else {
							$points = $this->get_points_for_refree_signup();
							WC_Points_Rewards_Manager::increase_points( $refree_id, $points, 'reffree-account-on-referal-signup' );
						}
					}

				}
			}
		}
		/*============== End of Set the referre user id to current user data =============*/
	}

			/**
	 * create coupon and send mail for reffral signup for refree.
	 *
	 * @since 1.0.0
	 * @param $user_id is the customer id who has registered himself successfully
	 */
	public function mwb_create_coupon_send_email_for_refree($user_id) {
		$mwb_cpr_coupon_length = $this->mwb_get_coupon_length();
		$mwb_cpr_coupon_amount = get_option('refree_discount_value',1);
		$mwb_cpr_coupon_expiry = $this->mwb_get_coupon_expiry();
		$expirydate = $this->mwb_expiry_date_saved($mwb_cpr_coupon_expiry);
		$bloginfo = get_bloginfo();
		// $mwb_cpr_discount_type = $this->mwb_get_discount_type();
		/*Get the coupon configration of the coupon*/
		$mwb_cpr_discount_type = $this->mwb_crp_get_discount_signup_type_refree();
		$coupon_amount_with_css = $this->mwb_formatted_amount($mwb_cpr_coupon_amount,$mwb_cpr_discount_type);
		$user=get_user_by('ID',$user_id);
		$user_email = $this->get_user_email($user);
		$mwb_cpr_code = $this->mwb_cpr_coupon_generator($mwb_cpr_coupon_length);
		$coupon_description = "Coupon for Reffrerd user on signup";

		if($this->mwb_cpr_create_coupons($mwb_cpr_code,$mwb_cpr_coupon_amount,$user_id,$mwb_cpr_discount_type,$expirydate,$coupon_description)) {

			/* === Send the Email to the Registered User === */
			if(empty($expirydate)) {
				$expirydate = __("No Expiry","coupon-referral-program");
			}
			$customer_email = WC()->mailer()->emails['crp_refree_email'];
			$email_status = $customer_email->trigger( $user_id , $mwb_cpr_code,$coupon_amount_with_css,$expirydate);

		}
		return $mwb_cpr_code ;
	}
	/**
	 * Save coupon code for the refree signup.
	 *
	 * @since 1.0.0
	 * @param $user_id is the customer id who has registered himself successfully
	 */
	public function save_referal_singup_coupon_code($coupon_code,$refree_id,$user_id) {
		
		$mwb_crp_referral_signup_array = array();
		$mwb_crp_referral_signup = get_user_meta($refree_id,'mwb_crp_referal_signup_coupon',true);
		$coupon = new WC_Coupon($coupon_code);
		$coupon_code = $coupon->get_id();
		if (!empty($mwb_crp_referral_signup)) {
			

			$mwb_crp_referral_signup[$coupon_code] = $user_id;
			
			update_user_meta($refree_id,"mwb_crp_referal_signup_coupon",$mwb_crp_referral_signup);
		}
		else {
			$mwb_crp_referral_signup_array[$coupon_code] = $user_id;
			update_user_meta($refree_id,"mwb_crp_referal_signup_coupon",$mwb_crp_referral_signup_array);
		}

	}
	/**
	 * Get signup coupons
	 * @name get_signup_coupon
	 * @since 1.0.0
	 * @return signup purchase array
	 */
	public function mwb_crp_get_referal_signup_coupon($user_id) {
		$mwb_crp_referal_signup_coupon = get_user_meta($user_id,'mwb_crp_referal_signup_coupon',true);
		if(empty($mwb_crp_referal_signup_coupon)) {
			$mwb_crp_referal_signup_coupon = array();
		}
		return $mwb_crp_referal_signup_coupon;

	}

	/**
	 * Save coupon code for the signup 
	 *
	 * @since 1.0.0
	 * @param $user_id is the customer id who has registered himself successfully
	 */
	public function save_singup_coupon_code($text,$coupon_code,$user_id) {
		$mwb_crp_signup_array = array();
		$coupon = new WC_Coupon($coupon_code);
		$coupon_code = $coupon->get_id();
		$mwb_crp_signup_array[$text] = $coupon_code;
		if(!empty($mwb_crp_signup_array)) {
			update_user_meta($user_id,'mwb_crp_signup_coupon',$mwb_crp_signup_array);
		}
	}

	/**
	 * Get signup coupons
	 * @name get_signup_coupon
	 * @since 1.0.0
	 * @return signup purchase array
	 */
	public function get_signup_coupon($user_id) {
		$mwb_crp_signup_coupon = get_user_meta($user_id,'mwb_crp_signup_coupon',true);
		if(empty($mwb_crp_signup_coupon)) {
			$mwb_crp_signup_coupon = array();
		}
		return $mwb_crp_signup_coupon;

	}

	/**
	 * create coupon and send mail for normal signup and reffral signup
	 *
	 * @since 1.0.0
	 * @param $user_id is the customer id who has registered himself successfully
	 */
	public function mwb_create_coupon_send_email($user_id) {
		$mwb_cpr_coupon_length = $this->mwb_get_coupon_length();
		$mwb_cpr_coupon_amount = $this->mwb_get_coupon_amount();
		$mwb_cpr_coupon_expiry = $this->mwb_get_coupon_expiry();
		$expirydate = $this->mwb_expiry_date_saved($mwb_cpr_coupon_expiry);
		$bloginfo = get_bloginfo();
		// $mwb_cpr_discount_type = $this->mwb_get_discount_type();
		/*Get the coupon configration of the coupon*/
		$mwb_cpr_discount_type = $this->mwb_get_discount_signup_type();
		$coupon_amount_with_css = $this->mwb_formatted_amount($mwb_cpr_coupon_amount,$mwb_cpr_discount_type);
		$user=get_user_by('ID',$user_id);
		$user_email = $this->get_user_email($user);
		$mwb_cpr_code = $this->mwb_cpr_coupon_generator($mwb_cpr_coupon_length);
		$coupon_description = "Coupon on Registration for UserID";
		$mwb_cpr_coupon_on_registration_custom_id = get_option('mwb_cpr_coupon_on_registration_custom_id',true);

		if($this->mwb_cpr_create_coupons($mwb_cpr_code,$mwb_cpr_coupon_amount,$user_id,$mwb_cpr_discount_type,$expirydate,$coupon_description)) {

			/* === Send the Email to the Registered User === */
			if(empty($expirydate)) {
				$expirydate = __("No Expiry","coupon-referral-program");
			}
			$customer_email = WC()->mailer()->emails['crp_signup_email'];
			$email_status = $customer_email->trigger( $user_id , $mwb_cpr_code,$coupon_amount_with_css,$expirydate);

		}
		return $mwb_cpr_code ;
	}
	/**
	 * Check whether the Reffral Signup  Feature is enable
	 *
	 * @since 1.0.0
	 * @param not required
	 * @return bool value
	 */
	public function check_reffral_signup_is_enable() {
		$is_enable = false;
		$mwb_cpr_referral_enable = get_option('mwb_crp_signup_enable_value',"yes");
		if(!empty($mwb_cpr_referral_enable) && $mwb_cpr_referral_enable == "no") {

			$is_enable = true;
		}
		return $is_enable;   	
	}

	/**
	 * Check whether the Signup Discount Feature is enable
	 *
	 * @since 1.0.0
	 * @param not required
	 * @return bool value
	 */

	function check_signup_is_enable(){
		$enable = false;
		$enable_signup = get_option('mwb_crp_signup_enable',false);
		if(!empty($enable_signup)&&$enable_signup =='yes'){
			$enable = true;
		}
		return $enable;
	}

	/**
	 * Check whether the Shortcode  Feature is enable
	 *
	 * @since 1.0.0
	 * @param not required
	 * @return bool value
	 */
	function check_shortcode_is_enable(){
		$enable = false;
		$enable_shortcode = get_option('referral_button_positioning',false);
		if(!empty($enable_shortcode)&&$enable_shortcode =='shortcode'){
			$enable = true;
		}
		return $enable;
	}

	/**
	 * Returns the Position of the Popup Button
	 *
	 * @since 1.0.0
	 * @param not required
	 * @return Position of the Popup Button
	 */
	public static function get_position_popup_button()
	{
		$class ='';
		$get_postion = get_option('referral_button_positioning',false);
		if(!empty($get_postion)&&$get_postion =='left_bottom'){
			$class = 'mwb_crp_btn_left_bottom' ;
		}
		if(!empty($get_postion)&&$get_postion =='right_bottom'){
			$class = 'mwb_crp_btn_right_bottom' ;
		}
		if(!empty($get_postion)&&$get_postion =='top_left'){
			$class = 'mwb_crp_btn_top_left' ;
		}
		if(!empty($get_postion)&&$get_postion =='top_right'){
			$class = 'mwb_crp_btn_top_right' ;
		}
		return $class;

	}

	/**
	 * Returns the Coupon Length
	 *
	 * @since 1.0.0
	 * @param not required
	 * @return Coupon Length Value
	 */

	function mwb_get_coupon_length(){
		$coupon_length = get_option('coupon_length',5);
		return $coupon_length;
	}

	/**
	 * Returns the array of the pages
	 *
	 * @since 1.0.0
	 * @param not required
	 * @return selected pages
	 */
	function mwb_get_selected_pages()
	{
		$mwb_selected_pages = array();
		$mwb_selected_pages = get_option('referral_button_page',array());
		return $mwb_selected_pages;
	}
	
	/**
	 * Returns the Coupon Expiry; How many days has been set in backend
	 *
	 * @since 1.0.0
	 * @param not required
	 * @return Coupon Expiry Days Value
	 */

	function mwb_get_coupon_expiry(){
		$coupon_expiry = get_option('coupon_expiry','');
		return $coupon_expiry;
	}

	/**
	 * Returns the Coupon Amount
	 *
	 * @since 1.0.0
	 * @param not required
	 * @return Coupon Amount Value for Signup/Registration
	 */

	function mwb_get_coupon_amount(){
		$coupon_amount = get_option('signup_discount_value',' ');
		return $coupon_amount;
	}

	/**
	 * Returns the Expiry Date in WP Date-format, It will calculate the exact date when coupon will get expired
	 *
	 * @since 1.0.0
	 * @param not required
	 * @return Coupon Expiry Value in WP format
	 */

	function mwb_expiry_date_saved($mwb_cpr_coupon_expiry){
		$todaydate = date_i18n("Y-m-d");
		$date_format = get_option('date_format','Y-m-d');
		if($mwb_cpr_coupon_expiry > 0 || $mwb_cpr_coupon_expiry === 0){
			$expirydate = date_i18n( "Y-m-d", strtotime( "$todaydate +$mwb_cpr_coupon_expiry day" ) );
		}
		else{
			$expirydate ="";
		}
		return $expirydate;
	}

	/**
	 * Returns the Discount Type has been set in the backend
	 *
	 * @since 1.0.0
	 * @param not required
	 * @return Discount Type: whether Fixed or Percentage
	 */

	function mwb_get_discount_type(){
		$mwb_cpr_discount_type = get_option('signup_discount_type','mwb_cpr_fixed');
		return $mwb_cpr_discount_type;
	}

	/**
	 * This function determine the which type of the coupon will be generated during the signup.
	 * @name mwb_get_discount_signup_discount 
	 * @since 1.0.0
	 * @param not required
	 * @return Discount Type:Whether Fixed or Percentage.
	 */
	public function mwb_get_discount_signup_type() {
		$mwb_cpr_discount_type_signup = get_option('signup_discount_coupon_type', 'mwb_cpr_fixed' );
		return $mwb_cpr_discount_type_signup;
	}

	/**
	 * Returns the amount in Formatted way
	 *
	 * @since 1.0.0
	 * @param not required
	 * @return In Formatted Way as per the Discount Type has been set
	 */

	function mwb_formatted_amount($mwb_cpr_coupon_amount ,$mwb_cpr_discount_type){
		if($mwb_cpr_discount_type == "mwb_cpr_fixed"){
			$coupon_amount_with_css = '<span style="font-size: 30px;font-weight: bold;display: inline-block;">'.wc_price($mwb_cpr_coupon_amount).'</span>';
		}else{
			$coupon_amount_with_css = '<span style="font-size: 30px;font-weight: bold;display: inline-block;">'.$mwb_cpr_coupon_amount.'%</span>';
		}
		return $coupon_amount_with_css;
	}

	/**
	 * Returns the Random Number Digit Coupon Code
	 *
	 * @since 1.0.0
	 * @param $length for taking the input for generating the random number
	 * @return $password for set the Coupon Code
	 */

	function mwb_cpr_coupon_generator($length){
		if( $length == "" ){
			$length = 5;
		}
		$password = '';
		$alphabets = range('A','Z');
		$numbers = range('0','9');
		$final_array = array_merge($alphabets,$numbers);
		while($length--){
			$key = array_rand($final_array);
			$password .= $final_array[$key];
		}
		$mwb_cpr_coupon_prefix = get_option('coupon_prefix','');
		$password = $mwb_cpr_coupon_prefix.$password;
		$password = apply_filters('mwb_cpr_coupons', $password);
		return $password;
	}

	/**
	 * This Function is used to create the coupons 
	 *
	 * @since 1.0.0
	 * @param all parameters are required to generate the coupons
	 * @return bool on successfully creation
	 */

	function mwb_cpr_create_coupons($mwb_cpr_code,$mwb_cpr_coupon_amount,$creation_id,$mwb_cpr_discount_type,$expirydate,$coupon_description){
		if(isset($creation_id) && !empty($creation_id))
		{
			$woo_ver = WC()->version;
			$coupon_code = $mwb_cpr_code; // Code
			$amount = $mwb_cpr_coupon_amount; // Amount
			if($mwb_cpr_discount_type == 'mwb_cpr_fixed')
			{
				$discount_type = 'fixed_cart'; 
			}
			else if($mwb_cpr_discount_type == 'mwb_cpr_percent')
			{
				$discount_type = 'percent';
			}
			$coupon_description = $coupon_description." #$creation_id";
			$coupon = array(
				'post_title' => $coupon_code,
				'post_content' => $coupon_description,
				'post_excerpt' => $coupon_description,
				'post_status' => 'publish',
				'post_author' => get_current_user_id(),
				'post_type'		=> 'shop_coupon'
				);
			$new_coupon_id = wp_insert_post( $coupon );
			//Added since woocommerce 3.9.0
			if ( $new_coupon_id ) {
				$coupon_obj = new WC_Coupon( $new_coupon_id );
				$coupon_obj->save();
			}
			$coupon_usage = get_option('coupon_usage','');
			$coupon_individual = get_option('coupon_individual',"no");
			$coupon_freeshipping = get_option('coupon_freeshipping',"no");
			update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
			update_post_meta( $new_coupon_id, 'free_shipping', $coupon_freeshipping );
			update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
			update_post_meta( $new_coupon_id, 'individual_use', $coupon_individual );
			update_post_meta( $new_coupon_id, 'usage_limit', $coupon_usage );
			if(!empty($expirydate)) {
				if($woo_ver < "3.6.0") {
					$expirydate = strtotime($expirydate);
					update_post_meta( $new_coupon_id, 'expiry_date', $expirydate );
				}
				else {
					update_post_meta( $new_coupon_id, 'date_expires', $expirydate );
				}
			}
			update_post_meta( $new_coupon_id, 'coupon_created_to', $creation_id );
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Get the Signup Discount Amount along with HTML format
	 *
	 * @since 1.0.0
	 * @param not required
	 * @return html with Signup Disocunt amount
	 */

	function get_signup_discount_html(){
		$signup_discount_type = $this->mwb_get_discount_signup_type();
		$signup_discount_value = get_option('signup_discount_value','');
		if(empty($signup_discount_value)){
			$signup_discount_value = 1;
		}
		if($signup_discount_type == 'mwb_cpr_fixed'){
			return wc_price($signup_discount_value);
		}
		else{
			return $signup_discount_value.'%';
		}
	}

	/**
	 * Get the User Email
	 *
	 * @since 1.0.0
	 * @param $user_object Object of particular User
	 * @return string containing user email
	 */

	function get_user_email($user_obj){
		$user_email = '';
		if(!empty($user_obj)){
			$user_email = $user_obj->user_email;
		}
		return $user_email;
	}

	/**
	 * Get the Subject for Signup Discount Email
	 *
	 * @since 1.0.0
	 * @param not required
	 * @return string containing the subject
	 */

	function get_discount_coupon_signup_subject(){
		$mwb_cpr_coupon_on_registration = __( 'Discount Coupon on Signup','coupon-referral-program');
		$mwb_cpr_coupon_on_registration = get_option( 'mwb_cpr_coupon_on_registration',$mwb_cpr_coupon_on_registration);
		return $mwb_cpr_coupon_on_registration;
	}

	/**
	 * This function we used to set the referral key in cookie and then we reward them 
	 *
	 * @since 1.0.0
	 */
	public function wp_loaded_set_referral_key() {
		if(!is_admin()){
			if(!is_user_logged_in()){
				$mwb_cpr_ref_link_expiry = get_option("mwb_cpr_ref_link_expiry",2);

				$cookie_val = isset($_COOKIE['mwb_cpr_cookie_set']) ? unserialize(base64_decode($_COOKIE['mwb_cpr_cookie_set'])): '';
				if(isset($_GET['ref']) && !empty($_GET['ref'])){
					$referral_key = trim($_GET['ref']);
					if(!empty($referral_key)) {
						$todaydate = date_i18n("Y-m-d");
						$expirydate = date_i18n( "Y-m-d", strtotime( "$todaydate +$mwb_cpr_ref_link_expiry day" ) );
						$referral_key = array('referral_key' => $referral_key,
							'expirydate'=> $expirydate);			
						if(!empty($cookie_val) && 
							!empty($cookie_val['referral_key'])
							&& !empty($cookie_val['expirydate'])) {
							if(  $referral_key != $cookie_val['referral_key']
								) {
								setcookie( 'mwb_cpr_cookie_set',base64_encode(serialize($referral_key)), time() + (86400 * $mwb_cpr_ref_link_expiry), "/" );
						}
						elseif( $todaydate > $expirydate ) {
							setcookie( 'mwb_cpr_cookie_set',base64_encode(serialize($referral_key)), time() + (86400 * $mwb_cpr_ref_link_expiry), "/" );
						}
					}
					elseif(!empty($mwb_cpr_ref_link_expiry)) {
						setcookie( 'mwb_cpr_cookie_set',base64_encode(serialize($referral_key)), time() + (86400 * $mwb_cpr_ref_link_expiry), "/" );
					}
				}
			}
		}
	}
}


	/**
	 * Get the Referral Discount on which % would be calculated over Order Total
	 *
	 * @since 1.0.0
	 * @param not required
	 * @return number value of Referral Discount
	 */

	function get_referral_discount_order(){
		$referral_discount_on_order = get_option('referral_discount_on_order',1);
		return $referral_discount_on_order;
	}
	/**
	 * Check whether the Social Sharing is enabled or not
	 *
	 * @since 1.0.0
	 * @param not required
	 * @return bool value 
	 */

	function is_social_sharing_enabled(){
		$mwb_cpr_social_enable = get_option('mwb_cpr_social_enable','off');
		if($mwb_cpr_social_enable == 'yes'){
			$mwb_cpr_social_enable = true;
		}else{
			$mwb_cpr_social_enable = false;
		}
		return $mwb_cpr_social_enable;
	}

	/**
	 * Get the html for social button icons as per required setting has enabled in backend
	 *
	 * @since 1.0.0
	 * @param $user_id
	 * @return $content as the HTMl
	 */

	function get_social_sharing_html($user_id) {
		
		$user_reference_key =  get_user_meta($user_id, 'referral_key', true);
		$page_permalink = site_url();
		$content = '';
		$content = $content.'<div class="mwb_crp_wrapper_button">';

		$twitter_button = '<div class="mwb_crp_btn mwb_crp_common_class"><a class="twitter-share-button" href="https://twitter.com/intent/tweet?text='.$page_permalink.'?ref='.$user_reference_key.'" target="_blank"><img src ="'.COUPON_REFERRAL_PROGRAM_DIR_URL.'/public/images/twitter.png">'.__("Tweet","coupon-referral-program").'</a></div>';

		$fb_button = '<div id="fb-root"></div>
		<script>(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s); js.id = id;
			js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.9";
			fjs.parentNode.insertBefore(js, fjs);
		}(document, "script", "facebook-jssdk"));</script>
		<div class="fb-share-button mwb_crp_common_class" data-href="'.$page_permalink.'?ref='.$user_reference_key.'" data-layout="button_count" data-size="small" data-mobile-iframe="true"><a class="fb-xfbml-parse-ignore" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u='.$page_permalink.'?ref='.$user_reference_key.'">'.__("Share","coupon-referral-program").'</a></div>';
		$mail = '<a class="mwb_wpr_mail_button mwb_crp_common_class" href="mailto:enteryour@addresshere.com?subject=Click on this link &body=Check%20this%20out:%20'.$page_permalink.'?ref='.$user_reference_key.'" rel="nofollow"><img src ="'.COUPON_REFERRAL_PROGRAM_DIR_URL.'public/images/email.png"></a>';
		

		if( get_option('mwb_cpr_facebook','no') == 'yes' ){

			$content =  $content.$fb_button;
		}
		if( get_option('mwb_cpr_twitter','no') == 'yes' ){

			$content =  $content.$twitter_button;
		}
		if( get_option('mwb_cpr_email','no') == 'yes' ){

			$content =  $content.$mail;
		}
		$content = $content.'</div>';
		return $content;
	}

	/**
	 * Reward the Discount Coupon to the referree on the purchasing of referred user
	 *
	 * @since 1.0.0
	 * @param $order_id, $old_status, $new_status
	 */

	public function woocommerce_order_status_changed_discount($order_id, $old_status, $new_status){
		if( $old_status != $new_status ){
			if( $new_status == 'completed' ){
				$order = wc_get_order( $order_id );
				/* Start Save utilization of the coupon */
				$this->save_utilize_coupon_aomunt($order);
				$bloginfo = get_bloginfo();
				$is_referral_has_rewarded = get_post_meta($order_id,'referral_has_rewarded',true);
				if(!isset($is_referral_has_rewarded) && empty($referral_has_rewarded)){
					return;
				}
				$user_id = absint( $order->get_user_id() );

				/*======= Set the Number of Order User has placed  =======*/

				$this->set_number_of_orders($user_id);
				//Check if referl purchase enable.
				if (!$this->check_referl_purchase_is_enable() ) {
					return;
				}
				$already_placed_orders = $this->get_number_of_orders_placed($user_id);
				$restrict_no_of_order = $this->get_number_of_orders_required();

				/*======= Reward the Discount only if the User has minimum number of Order has been placed from the Limit  =======*/
				if($already_placed_orders <= $restrict_no_of_order){
					$refree_id = get_user_meta( $user_id, 'mwb_cpr_user_referred_by', true );
					$refree = get_user_by( 'ID',$refree_id );
					$coupon_description = "Coupon on Order for OrderID";
					$refree_email = $this->get_user_email( $refree );
					if(!empty($order)){
						$order_total = $order->get_total();
						$referral_discount_on_order = $this->get_referral_discount_order();
						if( isset( $refree_id ) && !empty( $refree_id ) ){
							/*$mwb_cpr_coupon_amount = round(($referral_discount_on_order * $order_total)/100);*/
							if(!self::check_is_points_rewards_enable()) {
								$mwb_cpr_coupon_amount = $this->get_referral_coupon_amount($referral_discount_on_order,$order_total);
								$mwb_cpr_coupon_length = $this->mwb_get_coupon_length();
								$mwb_cpr_code = $this->mwb_cpr_coupon_generator($mwb_cpr_coupon_length);
								$mwb_cpr_coupon_expiry = $this->mwb_get_coupon_expiry();
								$mwb_cpr_discount_type = $this->mwb_get_discount_type();
								$coupon_amount_with_css = $this->mwb_formatted_amount($mwb_cpr_coupon_amount,$mwb_cpr_discount_type);
								$expirydate = $this->mwb_expiry_date_saved($mwb_cpr_coupon_expiry);

								/*======= Create the Woocommerce Coupons  =======*/

								if($this->mwb_cpr_create_coupons($mwb_cpr_code,$mwb_cpr_coupon_amount,$order_id,$mwb_cpr_discount_type,$expirydate,$coupon_description)) {

									/* === Send the Email to the relevant customer === */

									$customer_email = WC()->mailer()->emails['crp_order_email'];
									if(empty($expirydate)) {
										$expirydate = __("No Expiry","coupon-referral-program");

									}
									$email_status = $customer_email->trigger( $refree_id , $mwb_cpr_code,$coupon_amount_with_css,$expirydate);

									update_post_meta($order_id,'referral_has_rewarded',$refree_id);
									$this->save_referral_coupon_code($mwb_cpr_code,$refree_id,$user_id);
								}
							}
							else {
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
	 * @param $order
	 */
	public function save_utilize_coupon_aomunt($order) {

		$coupons = $order->get_items( 'coupon' );
		$user_id = $order->get_user_id();
		$signup_coupon = $this->get_signup_coupon($user_id);
		$referral_purchase_coupons = $this->get_referral_purchase_coupons($user_id);
		$crp_get_utilize_coupon_amount = $this->get_utilize_coupon_amount($user_id);
		$mwb_crp_get_referal_signup_coupon = $this->mwb_crp_get_referal_signup_coupon($user_id);

		if($this->check_array_is_not_empty($coupons)
			&&($this->check_array_is_not_empty($signup_coupon) || $this->check_array_is_not_empty($referral_purchase_coupons))) {
			foreach ( $coupons as $item_id => $item ) {
				$coupon_obj = new WC_Coupon($item->get_code());

				/*Check is referral coupon is applied or not*/
				if($this->check_array_is_not_empty($referral_purchase_coupons)) {
					foreach ($referral_purchase_coupons as $coupon_id => $userid) {
						if($coupon_obj->get_id() == $coupon_id) {
							$crp_get_utilize_coupon_amount = (float)$crp_get_utilize_coupon_amount + (float)$item->get_discount();
						}
					}
				}
				
			    /*Check is sigup coupon is applied or not*/
			    if($this->check_array_is_not_empty($signup_coupon)) {
			    	if($coupon_obj->get_id() == $signup_coupon['singup']) {
			    		$crp_get_utilize_coupon_amount = (float)$crp_get_utilize_coupon_amount + (float)$item->get_discount();
			    	}
			    }

			    /*Check is referral signup coupon is applied or not*/
				if($this->check_array_is_not_empty($mwb_crp_get_referal_signup_coupon)) {
					foreach ($mwb_crp_get_referal_signup_coupon as $coupon_id => $userid) {
						if($coupon_obj->get_id() == $coupon_id) {
							$crp_get_utilize_coupon_amount = (float)$crp_get_utilize_coupon_amount + (float)$item->get_discount();
						}
					}
				}

			}

		}
		/*Update coupon amount in usermeta of the utilize coupon*/
		if(!empty($crp_get_utilize_coupon_amount)) {
			update_user_meta($user_id,"utilize_coupon_amount",$crp_get_utilize_coupon_amount);
		}

	}

	/**
	 * Get the utilize coupon amount of the order
	 *
	 * @since 1.0.0
	 * @param $user_id
	 */
	public function get_utilize_coupon_amount($user_id) {
		$utilize_coupon_amount =0;
		$utilize_amount = get_user_meta($user_id,"utilize_coupon_amount",true);
		if(!empty($utilize_amount)) {
			$utilize_coupon_amount = $utilize_amount;
		}
		return $utilize_coupon_amount;
	}

	/**
	 * Get the utilize coupon amount of the order
	 *
	 * @since 1.0.0
	 * @param $user_id
	 */
	public function update_utilize_coupon_amount($user_id,$amount) {
		$total_amount = $this->get_utilize_coupon_amount($user_id);
		if (!empty($amount)) {
			$total_amount +=$amount;
			update_user_meta($user_id,'utilize_coupon_amount',$total_amount);
		}
		return true;
	}
	/**
	 * Check is any array not empty
	 *
	 * @since 1.0.0
	 * @param $array
	 */
	public function check_array_is_not_empty($array) {
		$is_not_empty = false;
		if(is_array($array) && !empty($array)) {
			$is_not_empty = true;
		}
		return $is_not_empty;
	}

	/**
	 * Save referrals purchase coupon
	 * @name save_referral_coupon_code
	 * @since 1.0.0
	 * @param $mwb_cpr_code,$refree_id,$user_id
	 */
	public function save_referral_coupon_code($mwb_cpr_code,$refree_id,$user_id) {
		$mwb_crp_referral_purchase_array = array();
		$mwb_crp_referral_purchase = get_user_meta($refree_id,'mwb_crp_referral_purchase_coupons',true);
		$coupon = new WC_Coupon($mwb_cpr_code);
		$mwb_cpr_code = $coupon->get_id();
		if (!empty($mwb_crp_referral_purchase)) {
			

			$mwb_crp_referral_purchase[$mwb_cpr_code] = $user_id;
			
			update_user_meta($refree_id,"mwb_crp_referral_purchase_coupons",$mwb_crp_referral_purchase);
		}
		else {
			$mwb_crp_referral_purchase_array[$mwb_cpr_code] = $user_id;
			update_user_meta($refree_id,"mwb_crp_referral_purchase_coupons",$mwb_crp_referral_purchase_array);
		}

	}

	/**
	 * get referral purchase coupon
	 * @name get_referral_purchase_coupons
	 * @since 1.0.0
	 * @return referral purchase array
	 */
	public function get_referral_purchase_coupons($user_id) {
		$mwb_crp_referral_purchase = get_user_meta($user_id,'mwb_crp_referral_purchase_coupons',true);
		if(empty($mwb_crp_referral_purchase)) {
			$mwb_crp_referral_purchase = array();
		}
		return $mwb_crp_referral_purchase;

	}

	/**
	 * Get the referral coupon amount type
	 *
	 * @since 1.0.0
	 * 
	 */
	public function get_referral_coupon_amount_type() {
		$referral_coupon_amount_type = get_option('referral_discount_type','mwb_cpr_referral_percent');
		return $referral_coupon_amount_type;
	}

	/**
	 * Get the referral coupon amount limit
	 *
	 * @since 1.0.0
	 * 
	 */
	public function get_referral_coupon_amount_limit_html() {
		$referral_discount_maxi_amt = get_option('referral_discount_upto',0);
		$mwb_cpr_amount_html = '';
		if($referral_discount_maxi_amt != 0) {

			if( $this->get_referral_coupon_amount_type() == 'mwb_cpr_referral_percent' && $this->mwb_get_discount_type() == 'mwb_cpr_percent') {

				$mwb_cpr_amount_html= __(' upto ','coupon-referral-program').'<span class="mwb_cpr_highlight" style="color:'.Coupon_Referral_Program_Admin::get_selected_color().'">'.$referral_discount_maxi_amt.'%.</span>';
			}
			else {
				$mwb_cpr_amount_html= __(' upto ','coupon-referral-program').'<span class="mwb_cpr_highlight" style="color:'.Coupon_Referral_Program_Admin::get_selected_color().'">'.wc_price($referral_discount_maxi_amt).'.</span>';

			}


		}
		
		echo $mwb_cpr_amount_html;
	}

	/**
	 * Get the referral coupon amount
	 *
	 * @since 1.0.0
	 * 
	 */
	public function get_referral_coupon_amount($referral_discount_on_order,$order_total) {
		$referral_coupon_amount_type = get_option('referral_discount_type','mwb_cpr_referral_percent');
		if($referral_coupon_amount_type == 'mwb_cpr_referral_fixed') {
			$mwb_cpr_coupon_amount = $referral_discount_on_order;
		}
		elseif($referral_coupon_amount_type == 'mwb_cpr_referral_percent') {
			$mwb_cpr_coupon_amount = round(($referral_discount_on_order * $order_total)/100);
			$mwb_cpr_coupon_amount = $this->get_upto_discount($mwb_cpr_coupon_amount);
		}
		return	$mwb_cpr_coupon_amount;	
	}

	/**
	 * Get the upto discount
	 *
	 * @since 1.0.0
	 * 
	 */
	public function get_upto_discount($mwb_cpr_coupon_amount) {

		$referral_discount_maxi_amt = get_option('referral_discount_upto',0);
		if(empty($referral_discount_maxi_amt)) {
			$referral_discount_maxi_amt =0;
		}

		if($mwb_cpr_coupon_amount > $referral_discount_maxi_amt && $referral_discount_maxi_amt != 0 ) {
			$mwb_cpr_coupon_amount = $referral_discount_maxi_amt;
		}
		return $mwb_cpr_coupon_amount;

	}

	/**
	 * Echo the HTML on Dashboard Page
	 *
	 * @since 1.0.0
	 * 
	 */
	public function woocommerce_account_dashboard_social_sharing(){
		
		if($this->is_social_sharing_enabled()){
			$user_ID = get_current_user_ID();
			?>
			<fieldset>
				<p class="mwb_cpr_heading"><?php _e('Refer your friends and you’ll earn discounts on their purchases','coupon-referral-program');?></p>
				<span class="mwb_crp_referral_link"><?php _e('Referral Link: ','coupon-referral-program'); ?></span>
				<code><?php echo $this->get_referral_link($user_ID); ?></code>
				<?php $html = $this->get_social_sharing_html($user_ID); echo $html;?>
			</fieldset>
			<?php
		}
	}

	/**
	 * Get the number of orders which is limited for rewarding the discount to the Referee
	 *
	 * @since 1.0.0
	 */

	function get_number_of_orders_required(){
		$restrict_no_of_order = get_option('restrict_no_of_order',1);
		return $restrict_no_of_order = isset($restrict_no_of_order) ? $restrict_no_of_order : 1;
	}

	/**
	 * Get the number of orders for one particular user
	 *
	 * @since 1.0.0
	 * @param $user_id for which the number of placed orders you want to get
	 */

	function get_number_of_orders_placed($user_id){
		$no_of_orders = get_user_meta($user_id,'mwb_crp_number_of_orders',true);
		return $no_of_orders = isset($no_of_orders) ? $no_of_orders : 1;
	}

	/**
	 * Set the number of orders for one particular user
	 *
	 * @since 1.0.0
	 * @param $user_id for which the number of orders needs to be set
	 */

	function set_number_of_orders($user_id){
		$pre_existing_orders = get_user_meta($user_id,'mwb_crp_number_of_orders',true);
		if(isset($pre_existing_orders) && !empty($pre_existing_orders)){
			$pre_existing_orders += 1;
		}else{
			$pre_existing_orders = 1;
		}
		update_user_meta($user_id,'mwb_crp_number_of_orders',$pre_existing_orders);
	}

	/**
	 * Check is points and rewards settings checkbox is enable
	 *
	 * @since 1.0.0
	 */
	public static function check_is_points_rewards_enable() {
		$mwb_is_enable = false;
		$mwb_points_rewards_enable = get_option('mwb_crp_points_rewards_enable',false);
		if(!empty($mwb_points_rewards_enable) && $mwb_points_rewards_enable == "yes") {
			$mwb_is_enable = true;
		}
		if (!is_plugin_active( 'woocommerce-points-and-rewards/woocommerce-points-and-rewards.php' ) ) {
			$mwb_is_enable = false;
		}
		return $mwb_is_enable;
	}

	/**
	 * get points of the signup
	 *
	 * @since 1.0.0
	 */
	public function get_points_for_signup() {
		$mwb_crp_points_rewards_signup_points = get_option('mwb_crp_points_rewards_signup_points',false);
		if(empty($mwb_crp_points_rewards_signup_points)) {
			$mwb_crp_points_rewards_signup_points = 0;
		}
		return $mwb_crp_points_rewards_signup_points;
	}

	/**
	 * get points of the referee signup.
	 *
	 * @since 1.0.0
	 */
	public function get_points_for_refree_signup() {
		$mwb_crp_points_rewards_refree_signup_points = get_option('mwb_crp_points_rewards_reffree_points',0);
		return $mwb_crp_points_rewards_refree_signup_points;
	}

	/**
	 * get points reffral purchase
	 *
	 * @since 1.0.0
	 */ 
	public function get_points_for_reffral_purchase() {

		$mwb_crp_reffral_purchase_points = get_option('mwb_crp_points_rewards_reffral_points',0);
		if(empty($mwb_crp_reffral_purchase_points) && is_numeric($mwb_crp_reffral_purchase_points)) {
			$mwb_crp_reffral_purchase_points = 0;
		}
		return $mwb_crp_reffral_purchase_points;
	}
	
	/**
	 * Add points log for the woocommerce points reward extension 
	 *
	 * @since 1.0.0
	 * @param $event_description,$event_type,$event
	 */
	public function wc_points_rewards_event_description($event_description, $event_type, $event) {
		global $wc_points_rewards;

		$points_label = $wc_points_rewards->get_points_label( $event ? $event->points : null );

		// set the description if we know the type
		switch ( $event_type ) {
			case 'refrral-order-purchase': $event_description = sprintf( __( '%s earned for refrral-order-purchase ', 'woocommerce-points-and-rewards' ), $points_label ); break;
			case 'reffral-account-signup': $event_description = sprintf( __( '%s earned for reffral acount signup', 'woocommerce-points-and-rewards' ), $points_label ); break;
			case 'reffree-account-on-referal-signup': $event_description = sprintf( __( '%s earned for referral signup points for referee', 'woocommerce-points-and-rewards' ), $points_label ); break;

			
		}

		return $event_description;
	}

	/**
	 * This function is used to set User Role to see Referral Coupon Tab in MY ACCOUNT Page
	 *
	 * @since 1.0.0
	 * @param $user_id for which the number of orders needs to be set
	 */
	public function crp_referral_coupon_dashboard($items) {
		$user_ID = get_current_user_ID();
		$user = new WP_User( $user_ID );
		if(in_array('subscriber', $user->roles) || in_array('customer', $user->roles)){

			$logout = $items['customer-logout'];
			unset( $items['customer-logout'] );
			$items['referral_coupons'] = __('Referral coupons','coupon-referral-program');
			$items['customer-logout'] = $logout;
		}
		return $items;	
	}

	/**
	 * This function is used to show coupon on the myaccount page
	 *
	 * @since 1.0.0
	 * @param $user_id for which the number of orders needs to be set
	 */
	public function crp_coupon_account_points()
	{
		$user_ID = get_current_user_ID();
		$user = new WP_User( $user_ID );
		if(in_array('subscriber', $user->roles) || in_array('customer', $user->roles)){
			require COUPON_REFERRAL_PROGRAM_DIR_PATH.'public/partials/coupon-referral-program-public-display.php';
		}
	}

	/**
	 * This function is used to calculate the total earning and total report
	 *
	 * @since 1.0.0
	 * @name get_revenue
	 */
	public function get_revenue($user_id) {
		$signup_coupon = $this->get_signup_coupon($user_id);
		$referral_purchase_coupons = $this->get_referral_purchase_coupons($user_id);
		$mwb_crp_get_referal_signup_coupon = $this->mwb_crp_get_referal_signup_coupon($user_id);
		
		$mwb_referred_user = array();
		$mwb_crp_total_earn = 0;
		$mwb_coupon_count = 0;
		if (!empty($referral_purchase_coupons) && is_array($referral_purchase_coupons)) {
			foreach ($referral_purchase_coupons as $coupon_code => $user_id) {
				$coupon  = new WC_Coupon( $coupon_code );
				if('publish' == get_post_status ( $coupon_code )) {
					$mwb_crp_total_earn += $coupon->get_amount();
					array_push($mwb_referred_user, $user_id);
					$mwb_coupon_count++;
				}
			}

			$mwb_referred_user = array_unique($mwb_referred_user);
		}
		//Referal signup discount coupon.
		if (!empty($mwb_crp_get_referal_signup_coupon) && is_array($mwb_crp_get_referal_signup_coupon)) {
			foreach ($mwb_crp_get_referal_signup_coupon as $coupon_code => $user_id) {
				$coupon  = new WC_Coupon( $coupon_code );
				if('publish' == get_post_status ( $coupon_code )) {
					$mwb_crp_total_earn += $coupon->get_amount();
					//array_push($mwb_referred_user, $user_id);
					$mwb_coupon_count++;
				}
			}
			//$mwb_referred_user = array_unique($mwb_referred_user);
		}
		if(!empty($signup_coupon['singup']) && is_array($signup_coupon) ) {
			$coupon  = new WC_Coupon($signup_coupon['singup'] );
			if('publish' == get_post_status ( $signup_coupon['singup'] )) {
				$mwb_crp_total_earn += $coupon->get_amount();
				$mwb_coupon_count = $mwb_coupon_count +1;
			}
		}
		if(empty($mwb_referred_user)) {
			$mwb_referred_user = 0;
		}
		else {
			$mwb_referred_user = count($mwb_referred_user);
		}
		return array('total_earning'=> $mwb_crp_total_earn,
			"referred_users"=>$mwb_referred_user,
			"total_coupon" => $mwb_coupon_count);
	}

	/**
	 * This function is used check is enable subscription
	 *
	 * @since 1.0.0
	 * @name mwb_crp_is_enable_subscription
	 */
	public function mwb_crp_is_enable_subscription() {
		$is_enable_woo_sub = false;
		$mwb_get_value_woo_subs = get_option('mwb_crp_woo_subscriptions_enable','off');
		if (!empty($mwb_get_value_woo_subs) && 'yes' === $mwb_get_value_woo_subs) {
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
	public function mwb_crp_is_enable_multiple_apply_coupons () {
		$is_enable_multiple_coupon = false;
		$mwb_get_value_single_coupon = get_option('mwb_crp_apply_all_coupon_on_subscription','off');
		if (!empty($mwb_get_value_single_coupon) && 'yes' === $mwb_get_value_single_coupon) {
			$is_enable_multiple_coupon = true;
		}
		return $is_enable_multiple_coupon;
	}


	/**
	 * This function is used to apply button on the subscriptions
	 * @param array $subscription 
	 * @since 1.0.0
	 * @name mwb_crp_add_button_for_the_apply_coupon
	 */
	public function mwb_crp_add_button_for_the_apply_coupon($subscription) {
		if(!$this->mwb_crp_is_enable_multiple_apply_coupons() && $this->mwb_crp_is_enable_subscription()) {
			?>
			<div id="mwb_crp_loader" style="display: none;">
				<img src="<?php echo COUPON_REFERRAL_PROGRAM_DIR_URL;?>public/images/loading.gif">
			</div>
			<a href="javascript:;" data-id="<?php echo $subscription->get_id();?>"class="button view mwb-coupon-view-btn mwb_crp_default"><?php echo esc_html_x( 'Apply Coupon', 'view a subscription', 'coupon-referral-program' ); ?></a>
			<?php
		}
	}

	/**
	 * This function is used to apply discount on the recurring payments
	 * @param array $renewal_order 
	 * @param array $subscription
	 * @since 1.0.0
	 * @name mwb_crp_change_renewal_order_total
	 */
	public function mwb_crp_change_renewal_order_total($renewal_order, $subscription) {
		global $woocommerce;
		/*Get the renewal order total amount*/
		$order_total = $renewal_order->get_subtotal();
		/*Get the referral purchase coupons*/ 
		$user_id =  $subscription->get_user_id();
		$mwb_crp_total_earn = 0;
		$referral_purchase_coupons = $this->get_referral_purchase_coupons($user_id);
		/*Get the signup coupon*/
		$signup_coupon = $this->get_signup_coupon($user_id);
		/*Check is subscription is enable*/
		if ($this->mwb_crp_is_enable_multiple_apply_coupons() && $this->mwb_crp_is_enable_subscription()) {
			/*Check is not empty signup coupon*/
			if(!empty($signup_coupon['singup']) && is_array($signup_coupon) ) {
				$coupon = new WC_Coupon( $signup_coupon['singup']);
				if('publish' === get_post_status ( $coupon ) && $coupon->is_valid()) {
					/*check is coupon is valid*/
					$mwb_crp_total_earn += $coupon->get_amount();
					if ($order_total >= $mwb_crp_total_earn) {
						$renewal_order->apply_coupon($coupon->get_code());
						// $coupon->decrease_usage_count( $user_id );
						$this->mwb_decrease_coupon_count($coupon->get_id());
					}
				}
			}
			/*Check is not empty referral purchase coupon*/
			if (!empty($referral_purchase_coupons) && is_array($referral_purchase_coupons)) {
				foreach ($referral_purchase_coupons as $coupon_code => $mwb_user_id) {
					$coupon = new WC_Coupon( $coupon_code );
					if('publish' == get_post_status ( $coupon ) && $coupon->is_valid()) {
						
						/*check is coupon is valid*/
						$mwb_crp_total_earn += $coupon->get_amount();
						if ($order_total >= $mwb_crp_total_earn) {
							$renewal_order->apply_coupon($coupon->get_code());
							$this->mwb_decrease_coupon_count($coupon->get_id());
							// $coupon->decrease_usage_count( $user_id );
						}
					}
				}
				/*Update ultilization amount*/
				$this->update_utilize_coupon_amount($user_id,$mwb_crp_total_earn);

			}
		}
		elseif (!$this->mwb_crp_is_enable_multiple_apply_coupons() && $this->mwb_crp_is_enable_subscription()) {
			$subscription_id = $subscription->get_id();
			$assinged_coupon = get_post_meta($subscription_id,'assinged_coupons',true);
			if (!empty($assinged_coupon) && is_array($assinged_coupon)) {
				foreach ($assinged_coupon as $key => $coupon_code) {
					$coupon = new WC_Coupon( $coupon_code );
					if('publish' == get_post_status ( $coupon ) && $coupon->is_valid()) {
						/*check is coupon is valid*/
						$renewal_order->apply_coupon($coupon->get_code());
						$this->mwb_decrease_coupon_count( $coupon->get_id() );
						// $coupon->decrease_usage_count( $user_id );
					}
					/*Update ultilization amount*/
					$this->update_utilize_coupon_amount($user_id,$coupon->get_amount());
				}
			}
		}
		return $renewal_order;
	}

	/**
	 * This function is used for the decresing the coupon usages.
	 * @since 1.0.0
	 * @name mwb_decrease_coupon_count
	 * @param int $coupon_id Coupon id of the coupon.
	 */
	public function mwb_decrease_coupon_count( $coupon_id ) {
		$coupon_usage = (int)get_post_meta( $coupon_id , 'usage_count',true);
		if( !empty( $coupon_usage ) && $coupon_usage > 0 ) {
			$coupon_usage -= 1;
			update_post_meta($coupon_id, 'usage_count', $coupon_usage);   
		}
		return true;
	}

	/**
	 * This function is used for get the html of the coupons.
	 * @since 1.0.0
	 * @name mwb_crp_change_renewal_order_total
	 */
	public function mwb_crp_coupons_popup() {
	  check_ajax_referer( 'mwb-crp-verify-nonce', 'mwb_nonce' );
	  //$response['html'] = "<p>".__('No available Coupons','coupon-referral-program')."</p>";
	  $mwb_crp_html = '';
	  if (isset($_POST['subscription_id'])) {
	  	$subscription_id = sanitize_post($_POST['subscription_id']);
	  	$assinged_coupon = get_post_meta($subscription_id,'assinged_coupons',true);

	  	$subscription = wc_get_order($subscription_id);
	  	$user_id = $subscription->get_user_id();

	  	$referral_purchase_coupons = $this->get_referral_purchase_coupons($user_id);

	  	/*Get the signup coupon*/
	  	$signup_coupon = $this->get_signup_coupon($user_id);
	  	if(!empty($signup_coupon['singup']) && is_array($signup_coupon) ) {
	  		$coupon = new WC_Coupon( $signup_coupon['singup']);
	  		if('publish' == get_post_status ( $coupon ) && $coupon->is_valid()) {
	  			/*check is coupon is valid*/
	  			$mwb_crp_html = $this->generate_html_for_coupons($subscription_id,$coupon,$assinged_coupon);
	  		}
	  	}
	  	/*Check is not empty referral purchase coupon*/
	  	if (!empty($referral_purchase_coupons) && is_array($referral_purchase_coupons)) {

	  		foreach ($referral_purchase_coupons as $coupon_code => $user_id) {
	  			$coupon = new WC_Coupon( $coupon_code );
	  			if('publish' == get_post_status ( $coupon ) && $coupon->is_valid()) {
	  				/*check is coupon is valid*/
	  				$mwb_crp_html.= $this->generate_html_for_coupons($subscription_id,$coupon,$assinged_coupon);
	  			}
	  		}
	  	}
	  	if ( $mwb_crp_html != '' ) {
	  		$response['html'] = $mwb_crp_html;
	  	}
	  	else {
	  		$mwb_crp_html = "<h2>".__('No available Coupons','coupon-referral-program')."</h2>";
	  		$response['html'] = $mwb_crp_html;
			
	  	}

	  	
	  }
      echo wp_json_encode($response);
	  wp_die();
	}

	/**
	 * This function is used for generating the html for the coupons.
	 * @since 1.0.0
	 * @param array $subscription_id array of the subscription.
	 * @param array $coupon array of the coupon.
	 * @param int $assinged_coupon id of the coupon.
	 * @name mwb_crp_change_renewal_order_total
	 */
	public function generate_html_for_coupons($subscription_id,$coupon,$assinged_coupon) {
		$mwb_coupon_amount = ($coupon->get_discount_type() == "fixed_cart")?wc_price($coupon->get_amount()) : $coupon->get_amount()."%";
	  		$expiry_date = $coupon->get_date_expires( 'edit' ) ? $coupon->get_date_expires( 'edit' )->date( wc_date_format()) : __("No-Expiry","coupon-referral-program");
		$mwb_crp_html.= 
		'<div class="mwb-coupon-popup-column">
	  			<div class="mwb-coupon-inner">
	  				<div class="mwb-coupon-code">'.strtoupper($coupon->get_code()).'</div>
	  				<div class="mwb-coupon-value">'.$mwb_coupon_amount.'</div>
	  				<div class="mwb-coupon-ex-date">'.$expiry_date.'</div>
	  			</div>
	  			<div class="mwb-coupon-scissor"><img src="'. COUPON_REFERRAL_PROGRAM_DIR_URL.'public/images/scissors.png'.' " alt="">';
	  	if (empty($assinged_coupon)) {
	  		$mwb_crp_html.= '</div>
	  		<div class="mwb-coupon-apply-btn"><a class="mwb_crp_apply_button" id="'.$coupon->get_id().'"data-subscription = "'.$subscription_id.'"data-id="'.$coupon->get_id().'" href="#">'.__('Apply','coupon-referral-program').'</a></div>
	  		</div>';
	  	}
	  	elseif(is_array($assinged_coupon) && in_array($coupon->get_id(), $assinged_coupon)) {
	  		$mwb_crp_html.= '</div>
	  		<div class="mwb-coupon-apply-btn"><a class="mwb_crp_remove_button" id="'.$coupon->get_id().'"data-subscription = "'.$subscription_id.'"data-id="'.$coupon->get_id().'" href="#">'.__('Remove','coupon-referral-program').'</a></div>
	  		</div>';
	  	}
	  	else {
	  		$mwb_crp_html.= '</div>
	  		<div class="mwb-coupon-apply-btn"><a class="mwb_crp_apply_button" id="'.$coupon->get_id().'"data-subscription = "'.$subscription_id.'"data-id="'.$coupon->get_id().'" href="#">'.__('Apply','coupon-referral-program').'</a></div>
	  		</div>';
	  	}
	  	return $mwb_crp_html;
	}

	/**
	 * This function is used for apply coupons.
	 * @since 1.0.0
	 * @name mwb_crp_change_renewal_order_total.
	 */
	public function mwb_crp_coupon_apply() {
		check_ajax_referer( 'mwb-crp-verify-nonce', 'mwb_nonce' );
		if (isset($_POST['subscription_id']) && isset($_POST['coupon_id'])) {
			$subscription_id = sanitize_post($_POST['subscription_id']);
			$coupon_id = sanitize_post($_POST['coupon_id']);
			$mwb_array = array();
			$assinged_coupon = get_post_meta($subscription_id,'assinged_coupons',true);
			if (empty($assinged_coupon)) {
				$mwb_array[] = $coupon_id;
				update_post_meta($subscription_id,'assinged_coupons',$mwb_array);
			}
			elseif (!empty($assinged_coupon) && is_array($assinged_coupon) && !in_array($coupon_id, $assinged_coupon)) {
				$assinged_coupon[] = $coupon_id;
				update_post_meta($subscription_id,'assinged_coupons',$assinged_coupon);
			}
			$response = true;
		}
		echo wp_json_encode($response);
		wp_die();
	}

	/**
	 * This function is used for removing applied coupons.
	 * @since 1.0.0
	 * @name mwb_crp_change_renewal_order_total.
	 */
	public function mwb_crp_coupon_remove() {
		check_ajax_referer( 'mwb-crp-verify-nonce', 'mwb_nonce' );
		if (isset($_POST['subscription_id']) && isset($_POST['coupon_id'])) {
			$subscription_id = sanitize_post($_POST['subscription_id']);
			$coupon_id = sanitize_post( $_POST['coupon_id'] );
			$assinged_coupon = get_post_meta($subscription_id,'assinged_coupons',true);
			if (!empty($assinged_coupon) && is_array($assinged_coupon)) {
				foreach ($assinged_coupon as $key => $value) {
					if ($coupon_id == $value) {
						unset($assinged_coupon[$key]);
					}
				}
				update_post_meta($subscription_id,'assinged_coupons',$assinged_coupon);
			}
			$response = true;
		}
		echo wp_json_encode($response);
		wp_die();
	}

	/**
	 * This function is used to add the button on the subscription details page.
	 * @name mwb_crp_add_button_order_details_page
	 * @param array $subscription Array of the subscription
	 * @since 1.3.4
	 */
	public function mwb_crp_add_button_order_details_page( $subscription ) {
		if(!$this->mwb_crp_is_enable_multiple_apply_coupons() && $this->mwb_crp_is_enable_subscription()) {
			?>
			<div id="mwb_crp_loader" style="display: none;">
				<img src="<?php echo esc_url(COUPON_REFERRAL_PROGRAM_DIR_URL);?>public/images/loading.gif">
			</div>
			<a href="javascript:;" data-id="<?php echo $subscription->get_id();?>"class="button view mwb-coupon-view-btn mwb_crp_default"><?php echo esc_html_x( 'Apply Coupon', 'view a subscription', 'coupon-referral-program' ); ?></a>
			<?php
		}
	}

	/**
	 * This function is used to remove the coupon hold time.
	 * @name mwb_crp_remove_coupon_time
	 * @since 1.4.2
	 */
	public function mwb_crp_remove_coupon_time() {
		return false;
	}

	/**
	 * Check whether the Reffree Signup  Feature is enable
	 *
	 * @since 1.4.3
	 * @param not required
	 * @return bool value
	 */
	public function check_reffre_signup_is_enable() {
		$is_enable = false;
		$mwb_cpr_referre_enable = get_option('mwb_crp_refree_discount_enable'," ");
		if( isset( $mwb_cpr_referre_enable ) && 'yes' == $mwb_cpr_referre_enable ) {

			$is_enable = true;
		}
		return $is_enable;   	
	}

	/**
	 * This function determine the which type of the coupon will be generated during the refferal signup.
	 * @name mwb_crp_get_discount_signup_type_refree 
	 * @since 1.4.3
	 * @param not required
	 * @return Discount Type:Whether Fixed or Percentage.
	 */
	public function mwb_crp_get_discount_signup_type_refree() {
		$mwb_cpr_discount_type_signup = get_option('referee_discount_type', 'mwb_cpr_fixed' );
		return $mwb_cpr_discount_type_signup;
	}
		/**
	 * Get the Refree Signup Discount Amount along with HTML format
	 *
	 * @since 1.0.0
	 * @param not required
	 * @return html with Signup Disocunt amount
	 */

	function get_refree_signup_discount_html(){
		$refree_signup_discount_type = $this->mwb_crp_get_discount_signup_type_refree();
		$refree_signup_discount_value = get_option('refree_discount_value','');
		if(empty($refree_signup_discount_value)){
			$refree_signup_discount_value = 1;
		}
		if($refree_signup_discount_type == 'mwb_cpr_fixed'){
			return wc_price($refree_signup_discount_value);
		}
		else{
			return $refree_signup_discount_value.'%';
		}
	}

		/**
	 * Check whether the Referal purchase Feature is enable
	 *
	 * @since 1.4.3
	 * @param not required
	 * @return bool value
	 */
	public function check_referl_purchase_is_enable() {
		$is_enable = false;
		$mwb_crp_enable_referal_purchase = get_option('mwb_crp_enable_referal_purchase',"yes");
		if( isset( $mwb_crp_enable_referal_purchase ) && 'yes' == $mwb_crp_enable_referal_purchase ) {
			$is_enable = true;
		}
		return $is_enable;   	
	}
}
