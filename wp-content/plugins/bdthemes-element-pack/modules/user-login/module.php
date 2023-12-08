<?php
namespace ElementPack\Modules\UserLogin;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

    protected $fb_app_id;
    protected $fb_app_secret;
    protected $go_client_id;

    public function get_name() {
        return 'user-login';
    }

    public function get_widgets() {

        $widgets = [
            'User_Login',
        ];

        return $widgets;
    }

    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct();

        $options                = get_option( 'element_pack_api_settings' );
        $this->fb_app_id        = (isset($options['facebook_app_id']) && !empty($options['facebook_app_id']))? sanitize_text_field($options['facebook_app_id']): '';
        $this->fb_app_secret    = (isset($options['facebook_app_secret']) && !empty($options['facebook_app_secret']))? sanitize_text_field($options['facebook_app_secret']): '';
        $this->go_client_id     = (isset($options['google_client_id']) && !empty($options['google_client_id']))? sanitize_text_field($options['google_client_id']): '';

        add_action( 'wp_ajax_element_pack_social_facebook_login', array( $this, 'get_facebook_data' ) );
        add_action( 'wp_ajax_nopriv_element_pack_social_facebook_login', array( $this, 'get_facebook_data' ) );

        add_action( 'wp_ajax_element_pack_social_google_login', array( $this, 'get_google_data' ) );
        add_action( 'wp_ajax_nopriv_element_pack_social_google_login', array( $this, 'get_google_data' ) );

        add_action( 'elementor/frontend/before_register_scripts', [ $this, 'register_site_scripts' ] );


        add_action( 'wp_head', array( $this, 'init_facebook' ) );
        add_action( 'wp_ajax_nopriv_element_pack_ajax_login', [ $this, "element_pack_ajax_login"] );

    }

    public function element_pack_ajax_login(){
        // First check the nonce, if it fails the function will break
        check_ajax_referer( 'ajax-login-nonce', 'bdt-user-login-sc' );

        /** Recaptcha*/
        $post_id   = (int) $_REQUEST['page_id'];
        $widget_id = (int) $_REQUEST['widget_id'];

        $result = $this->get_widget_settings($post_id, $widget_id);

        if(isset($result['show_recaptcha_checker']) && $result['show_recaptcha_checker'] == 'yes'){
            $gRecaptcha = esc_textarea($_REQUEST['g-recaptcha-response']);

            if ( !apply_filters('element_pack_google_recaptcha_validation', $gRecaptcha ) ) {
                echo wp_json_encode( ['loggedin' => false, 'message'=>  esc_html__('reCAPTCHA is invalid!', 'bdthemes-element-pack') ] );
                exit;
            }

        }

        // Nonce is checked, get the POST data and sign user on
        $access_info                  = [];
        $access_info['user_login']    = !empty($_POST['user_login'])? sanitize_text_field($_POST['user_login']) : "";
        $access_info['user_password'] = !empty($_POST['user_password'])? sanitize_text_field($_POST['user_password']) : "";
        $access_info['remember']      = !empty($_POST['rememberme'])? true : false;
        $user_signon                  = wp_signon( $access_info, false );

        if ( !is_wp_error($user_signon) ){
            echo wp_json_encode( ['loggedin' => true, 'message'=> esc_html__('Login successful, Redirecting...', 'bdthemes-element-pack')] );
        } else {
            echo wp_json_encode( ['loggedin' => false, 'message'=> esc_html__('Oops! Wrong username or password!', 'bdthemes-element-pack')] );
        }

        die();
    }

    public function register_site_scripts(){
        wp_register_script( 'ep-google-login', 'https://apis.google.com/js/api:client.js', ['jquery'], null, true );

    }

    public function init_facebook(){
        if(strlen($this->fb_app_id) > 10 && !is_user_logged_in()):
            ?>
            <script>
                window.fbAsyncInit = function() {
                    FB.init({
                        appId            : '<?php echo $this->fb_app_id ?>',
                        autoLogAppEvents : true,
                        xfbml            : true,
                        version          : 'v5.0'
                    });
                };

                (function(d, s, id){
                    var js, fjs = d.getElementsByTagName(s)[0];
                    if (d.getElementById(id)) {return;}
                    js = d.createElement(s); js.id = id;
                    js.src = "https://connect.facebook.net/en_US/sdk.js";
                    fjs.parentNode.insertBefore(js, fjs);
                }(document, 'script', 'facebook-jssdk'));
            </script>
        <?php endif;
    }

    /**
     * Get Google Form Data via AJAX call.
     * return void
     */
    public function get_google_data() {

        $data      = array();
        $response  = array();
        $user_data = array();
        $result    = '';

        if ( isset($_POST['id_token']) ) {

            $id_token            = filter_input( INPUT_POST, 'id_token', FILTER_SANITIZE_STRING );
            $google_client_id    = $this->go_client_id;
            $googleUserdata      = $this->verify_google_data( $id_token, $google_client_id );

            $name       = isset( $googleUserdata['name'] ) ? sanitize_text_field($googleUserdata['name']) : '';
            $email      = isset( $googleUserdata['email'] ) ? sanitize_email($googleUserdata['email']) : '';
            $should_send_email  = apply_filters('elementor_pack_send_mail_create_user',0);

            // Check if email is verified with Google.
            if ( empty( $googleUserdata ) || ( $googleUserdata['aud'] !== $google_client_id ) || ( isset( $googleUserdata['email'] ) && $googleUserdata['email'] !== $email ) ) {
                wp_send_json_error(
                    array(
                        'error' => esc_attr_x( 'Unauthorized access', 'User Login and Register', 'bdthemes-element-pack' ),
                    )
                );
            }

            $user_data = get_user_by( 'email', $email );

            $response['username'] = $name;

            if ( ! empty( $user_data ) && false !== $user_data ) {

                $user_ID    = $user_data->ID;
                $user_email = $user_data->user_email;
                wp_set_auth_cookie( $user_ID );
                wp_set_current_user( $user_ID, $name );
                do_action( 'wp_login', $user_data->user_login, $user_data );
                $response['success'] = true;

            } else {

                $password = wp_generate_password( 12, true, false );

                if ( username_exists( $name ) ) {
                    // Generate something unique to append to the username in case of a conflict with another user.
                    $suffix = '-' . zeroise( wp_rand( 0, 9999 ), 4 );
                    $name  .= $suffix;

                    $user_array = array(
                        'user_login' => strtolower( preg_replace( '/\s+/', '', $name ) ),
                        'user_pass'  => $password,
                        'user_email' => $email,
                        'first_name' => $googleUserdata['name'],
                    );
                    $user_array = apply_filters('elementor_pack_user_login_insert_user',$user_array);
                    $result     = wp_insert_user( $user_array );
                } else {
                    $user_array = array(
                        'user_login' => strtolower( $name ),
                        'user_pass'  => $password,
                        'user_email' => $email,
                        'first_name' => $googleUserdata['name'],
                    );
                    $user_array = apply_filters('elementor_pack_user_login_insert_user',$user_array);
                    $result     = wp_insert_user( $user_array );
                }

                if ( 1 == $should_send_email ) {
                    $this->send_created_user_email( $result, $should_send_email );
                }

                $user_data = get_user_by( 'email', $email );

                if ( $user_data ) {

                    $user_ID    = $user_data->ID;
                    $user_email = $user_data->user_email;

                    $user_meta = array(
                        'provider' => 'google',
                    );

                    update_user_meta( $user_ID, 'ep_login_form', $user_meta );

                    if ( wp_check_password( $password, $user_data->user_pass, $user_data->ID ) ) {

                        wp_set_auth_cookie( $user_ID );
                        wp_set_current_user( $user_ID, $name );
                        do_action( 'wp_login', $user_data->user_login, $user_data );
                        $response['success'] = true;
                    }
                }
            }

            echo wp_send_json( $response );

        } else {
            die;
        }
    }

    /**
     * Get access token info.
     */
    public function verify_google_data( $id_token, $uae_google_client_id ) {

        require_once BDTEP_MODULES_PATH . 'user-login/vendor/autoload.php';

        // Get $id_token via HTTPS POST.
        $client = new \Google_Client( array( 'client_id' => $uae_google_client_id ) );  //PHPCS:ignore:PHPCompatibility.PHP.ShortArray.Found
        $verified_data = $client->verifyIdToken($id_token);

        if ( $verified_data ) {
            return $verified_data;
        } else {
            wp_send_json_error(
                array(
                    'error' => esc_attr_x( 'Unauthorized access', 'User Login and Register', 'bdthemes-element-pack' ),
                )
            );
        }

    }

    public function get_facebook_data() {

        $data      = array();
        $response  = array();
        $user_data = array();
        $result    = '';

        if ( isset( $_POST['data'] ) ) {

            $data = $_POST['data'];

            $fb_user_id   = filter_input( INPUT_POST, 'userID', FILTER_SANITIZE_STRING );
            $access_token = filter_input( INPUT_POST, 'security_string', FILTER_SANITIZE_STRING );

            $fb_app_id     = $this->fb_app_id;
            $fb_app_secret = $this->fb_app_secret;

            $fbUserData = $this->get_fb_user_info( $access_token, $fb_app_id, $fb_app_secret );

            if ( empty( $fb_app_id ) || empty( $fb_app_secret ) || empty( $fb_user_id ) || empty( $fbUserData )
                || ( $fb_user_id !== $fbUserData['data']['user_id'] ) || ( $fb_app_id !== $fbUserData['data']['app_id'] )
                || ( ! $fbUserData['data']['is_valid'] ) ) {

                wp_send_json_error( esc_html_x('Invalid Authorized Information', 'User Login and Register', 'bdthemes-element-pack') );

            }

            $name               = sanitize_user( $data['name'] );
            $first_name         = sanitize_user( $data['first_name'] );
            $last_name          = sanitize_user( $data['last_name'] );
            $should_send_email  = apply_filters('elementor_pack_send_mail_create_user',0);


            $verified_email = $this->get_fb_user_email( $fbUserData['data']['user_id'], $access_token );

            if (  isset( $data['email'] ) && is_email($data['email']) ) {

                if ( $data['email'] === $verified_email['email'] ) {
                    $email = sanitize_email( $verified_email['email'] );
                } else {
                    wp_send_json_error( esc_html_x('Invalid Authorization', 'User Login and Register', 'bdthemes-element-pack') );
                }
            } else {
                $email = $fbUserData['data']['user_id'] . '@facebook.com';
            }

            $user_data = get_user_by( 'email', $email );

            if ( ! empty( $user_data ) && false !== $user_data ) {

                $user_ID    = $user_data->ID;
                $user_email = $user_data->user_email;
                wp_set_auth_cookie( $user_ID );
                wp_set_current_user( $user_ID, $name );
                do_action( 'wp_login', $user_data->user_login, $user_data );

                $response['success'] = true;

            } else {

                $password = wp_generate_password( 12, true, false );

                $facebook_array  = array(
                    'user_login' => $name,
                    'user_pass'  => $password,
                    'user_email' => $email,
                    'first_name' => isset( $first_name ) ? $first_name : $name,
                    'last_name'  => $last_name,
                );

                if ( username_exists( $name ) ) {
                    // Generate something unique to append to the username in case of a conflict with another user.
                    $suffix = '-' . zeroise( wp_rand( 0, 9999 ), 4 );
                    $name  .= $suffix;

                    $facebook_array['user_login'] = strtolower( preg_replace( '/\s+/', '', $name ) );
                }

                $facebook_array = apply_filters('elementor_pack_user_login_insert_user',$facebook_array);
                $result =  wp_insert_user( $facebook_array );

                if ( 1 == $should_send_email ) {
                    $this->send_created_user_email( $result, $should_send_email );
                }

                $user_data = get_user_by( 'email', $email );

                if ( $user_data ) {
                    $user_ID    = $user_data->ID;
                    $user_email = $user_data->user_email;

                    $user_meta = array(
                        'provider' => 'facebook',
                    );

                    update_user_meta( $user_ID, 'ep_login_form', $user_meta );

                    if ( wp_check_password( $password, $user_data->user_pass, $user_data->ID ) ) {
                        wp_set_auth_cookie( $user_ID );
                        wp_set_current_user( $user_ID, $name );
                        do_action( 'wp_login', $user_data->user_login, $user_data );
                        $response['success'] = true;
                    }
                }
            }

            echo wp_send_json( $response );
        } else {
            die;
        }
    }

    public function get_fb_user_info( $access_token, $uae_facebook_app_id, $uae_facebook_app_secret ) {

        $fb_url = 'https://graph.facebook.com/oauth/access_token';
        $fb_url = add_query_arg(
            array(
                'client_id'     => $uae_facebook_app_id,
                'client_secret' => $uae_facebook_app_secret,
                'grant_type'    => 'client_credentials',
            ),
            $fb_url
        );

        $fb_response = wp_remote_get( $fb_url );

        if ( is_wp_error( $fb_response ) ) {
            wp_send_json_error();
        }

        $fb_app_response = json_decode( wp_remote_retrieve_body( $fb_response ), true );

        $app_token = $fb_app_response['access_token'];

        $url = 'https://graph.facebook.com/debug_token';
        $url = add_query_arg(
            array(
                'input_token'  => $access_token,
                'access_token' => $app_token,
            ),
            $url
        );

        $response = wp_remote_get( $url );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error();
        }

        return json_decode( wp_remote_retrieve_body( $response ), true );
    }

    /**
     * Function that retrieves authenticatated Facebook email.
     */
    public function get_fb_user_email( $user_id, $access_token ) {

        $fb_email_url = 'https://graph.facebook.com/' . $user_id;
        $fb_email_url = add_query_arg(
            array(
                'fields'       => 'email',
                'access_token' => $access_token,
            ),
            $fb_email_url
        );

        $email_response = wp_remote_get( $fb_email_url );

        if ( is_wp_error( $email_response ) ) {
            wp_send_json_error();
        }

        return json_decode( wp_remote_retrieve_body( $email_response ), true );

    }

    public function send_created_user_email( $result, $notify ) {

        do_action( 'edit_user_created_user', $result, $notify );

    }

}
