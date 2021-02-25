<?php
/**
 * @link       https://www.themepunch.com
 * @package    Revslider_Login_Addon
 * @subpackage Revslider_Login_Addon/public
 * @author     ThemePunch <info@themepunch.com>
 */
class Revslider_Login_Addon_Public {

	/**
	 * The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/revslider-login-addon-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/revslider-login-addon-public.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Add Shortcodes
	 */
	public function add_shortcodes() {
	   add_shortcode( 'revslider-login-form', array($this,'revslider_login_form_shortcode') );
	}
	
	/**
	 * Filters the custom meta placeholders and calls function to replace
	 */
	public function rev_addon_insert_meta($text, $layer){
		
		return str_replace('{{revslider_login_form}}', '[revslider-login-form]', $text);
	}

	/**
	 * The actual Shortcode to display the login form
	 */
	public function revslider_login_form_shortcode( $attr ) {
	   // Set up some defaults. 

		$redirect_def = isset($_GET['redirect_to']) ? $_GET['redirect_to'] : '';
	   	
	   	if(!isset($_GET['lost_password'])){
		   	//saved values
			$revslider_login_addon_values = array();
			parse_str(get_option('revslider_login_addon'), $revslider_login_addon_values);
			$revslider_login_addon_values['revslider-login-addon-lost-password-link'] = isset($revslider_login_addon_values['revslider-login-addon-lost-password-link']) ? $revslider_login_addon_values['revslider-login-addon-lost-password-link'] : '0';
			$revslider_login_addon_values['revslider-login-addon-remember-me'] = isset($revslider_login_addon_values['revslider-login-addon-remember-me']) ? true : false;
			if($redirect_def==""){
				$redirect_def = isset($revslider_login_addon_values['revslider-login-addon-redirect-to']) ? stripslashes($revslider_login_addon_values['revslider-login-addon-redirect-to']) : '';
			}

		   	$defaults = array(
		   	  'remember' => $revslider_login_addon_values['revslider-login-addon-remember-me'],
		      'label_username' => __('Username','revslider-login-addon'),
		      'label_password' => __('Password','revslider-login-addon'),
		      'already_logged_in' => __('You are already signed in.','revslider-login-addon'),
		      'login_failed' => __('The login data you entered wasn\'t quite right. <a href="'.wp_lostpassword_url().'">Did you forget your password</a>?','revslider-login-addon'),
		      'redirect' => $redirect_def
		   );

		   // Merge the user input arguments with the defaults. 
		   $attr = shortcode_atts( $defaults, $attr );

		   if ( is_user_logged_in() ) {
		   		wp_redirect($redirect_def);
		   		return $attr['already_logged_in'];
		    }

		   // Set 'echo' to 'false' because we want it to always return instead of print for shortcodes. 
		   $attr['echo'] = false;

		   //Add Error Message
		   $error_message = isset($_GET['login_error']) ? '<div id="revslider_login_error">'.$attr['login_failed'].'</div>' : '';

		   //Add lost password link
		   $lost_password_link = $revslider_login_addon_values['revslider-login-addon-lost-password-link'] ? '<a href="'.wp_lostpassword_url().'" title="Lost Password">Lost Password</a>' : '';
		   	
		   $return_form = $error_message . wp_login_form( $attr ). $lost_password_link;
		}
		else { 
			$return_form ='<div id="password-lost-form" class="widecolumn">
			    <h3>'. __( 'Forgot Your Password?', 'revslider-login-addon' ) .' </h3>
			    <p>
			        '. __(
			                "Enter your email address and we'll send you a link you can use to pick a new password.",
			                'personalize_login'
			            ) . '
			    </p>
			 
			    <form id="lostpasswordform" action="'. wp_lostpassword_url() .'" method="post">
			        <p class="form-row">
			            <label for="user_login">'.__( 'Email', 'revslider-login-addon' ) .'
			            <input type="text" name="user_login" id="user_login">
			        </p>

			        <p class="lostpassword-submit">
			            <input type="submit" name="submit" class="lostpassword-button" value="'.__( 'Reset Password', 'revslider-login-addon' ) .'"/>
			        </p>
			    </form>
			</div>';
		}
	   return $return_form;
	}

	/**
	 * Login Fail Action
	 *
	 * @since    1.0.0
	 */
	public function front_end_login_fail( $username ) {
	   // Getting URL of the login page
	   $referrer = $_SERVER['HTTP_REFERER'];    
	   // if there's a valid referrer, and it's not the default log-in screen
	   if( !empty( $referrer ) ){ // && !strstr( $referrer,'wp-login' ) && !strstr( $referrer,'wp-admin' ) ) {
	       wp_redirect( REV_ADDON_LOGIN_URL.( 'public/partials/revslider-login-addon-public-display.php?login_error' ) ); 
	       exit;
	   }
	}

	/**
	 * Redirect login
	 */
	public function redirect_to_custom_login() {
	    if ( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
	        $redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : null;
	     
	        if ( is_user_logged_in() ) {
	            $this->redirect_logged_in_user( $redirect_to );
	            exit;
	        }
	 
	        // The rest are redirected to the login page
	        $login_url = REV_ADDON_LOGIN_URL.( 'public/partials/revslider-login-addon-public-display.php' );			

	        if ( ! empty( $redirect_to ) ) {
	            $login_url = add_query_arg( 'redirect_to', $redirect_to, $login_url );
	        }
	 
	        wp_redirect( $login_url );
	        exit;
	    }
	}

	/**
	 * Redirect Password Lost
	 */
	public function redirect_to_custom_lostpassword() {
		if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
	        if ( is_user_logged_in() ) {
	            $this->redirect_logged_in_user();
	            exit;
	        }
	 		
	 		$login_url = REV_ADDON_LOGIN_URL.( 'public/partials/revslider-login-addon-public-display.php?lost_password' );
	        wp_redirect( $login_url );
	        exit;
	    }
	}

	/**
	 * Redirect Private Page/Post to Login
	 */
	public function private_redirect_to_login() {
	  global $wp_query,$wpdb;
	  if ( is_404() ) {
	    $private = $wpdb->get_row($wp_query->request);
	    if( 'private' == $private->post_status  ) {
	      $login_url = REV_ADDON_LOGIN_URL.( 'public/partials/revslider-login-addon-public-display.php' );
	      wp_safe_redirect($login_url."?redirect_to=".get_permalink($private->ID));
	      die;
	    }
	  }
	}

}
