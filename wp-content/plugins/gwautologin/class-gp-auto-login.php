<?php

/**
* TODO:
* - Provide settings UI for where to redirect after registration.
*/

class GP_Auto_Login extends GWPerk {

	public $version = GP_AUTO_LOGIN_VERSION;

    protected $min_gravity_perks_version = '1.0';
    protected $min_gravity_forms_version = '1.8.9';
    protected $min_wp_version = '3.5';

    private $user_id;
    private $password;

    private static $instance = null;

    public static function get_instance( $perk_file ) {
        if( null == self::$instance )
            self::$instance = new self( $perk_file );
        return self::$instance;
    }

    public function init() {

        /*
         * @deprecated 1.3
         */
        $this->add_tooltip($this->key('auto_login'), '<h6>' . __('Auto Login', 'gravityperks') . '</h6>' . __('Enable this option to automatically log users in after registration.', 'gravityperks') );

        // Add Actions
        add_filter( 'gform_userregistration_feed_settings_fields', array( $this, 'add_auto_login_setting' ), 10, 2 );
        add_action( 'gform_user_registered', array( $this, 'maybe_auto_login' ), 10, 4 );

        // Support for User Activation
        add_action( 'init', array( $this, 'auto_login_on_redirect' ), 11 );

        /*
         * @deprecated 1.3
         */
        add_action( 'gform_user_registration_add_option_group', array( $this, 'add_auto_login_option' ), 10, 3 );
        add_filter( 'gform_user_registration_save_config', array( $this, 'save_auto_login_option' ) );

    }

    public function requirements() {
        return array(
            array(
                'class' => 'GFUser',
                'message' => 'GP Auto Login requires the Gravity Forms User Registration add-on.'
            )
        );
    }

    public function is_auto_login_enabled( $feed ) {

        // different setting in earlier version of UR (>3.0)
        $old_setting_enabled = rgars( $feed, "meta/{$this->key('auto_login')}" ) == true;
        $enabled = rgars( $feed, 'meta/autoLogin' ) == true;

        return $enabled || $old_setting_enabled;
    }

    public function maybe_auto_login( $user_id, $feed, $entry, $password ) {

	    if ( ! apply_filters( 'gpal_auto_login', $this->is_auto_login_enabled( $feed ), $user_id, $feed, $entry, $password ) ) {
		    return;
	    }

        if( rgget( 'page' ) == 'gf_activation' ) {

        	$redirect_url = $this->get_auto_login_url( $user_id, $password );
            echo '<script type="text/javascript"> window.location = "' . $redirect_url . '"; </script>';

            $this->user_id = $user_id;
            $this->password = $password;
            add_filter( 'gpbua_activation_redirect_url', array( $this, 'set_gpbua_activation_redirect_url' ) );

        } else {
            $this->auto_login( $user_id, $password );
        }

    }

    public function get_auto_login_url( $user_id, $password, $url = null ) {
	    return add_query_arg( array(
		    'user' => $user_id,
		    'pass' => rawurlencode( is_callable( array( 'GFCommon', 'openssl_encrypt' ) ) ? GFCommon::openssl_encrypt( $password ) : GFCommon::encrypt( $password ) ),
		    'gpalfr' => true, // auto-login from redirect
	    ), $url ? $url : $_SERVER['REQUEST_URI'] );
    }

    public function set_gpbua_activation_redirect_url( $url ) {
    	return $this->get_auto_login_url( $this->user_id, $this->password, $url );
    }

    public function auto_login_on_redirect() {

        $is_activation_page = rgget( 'page' ) == 'gf_activation'; // @todo With the introduction of the $force_redirect; we might not need to check for this anymore...
        $user_id = rgget( 'user' );
        $force_redirect = rgget( 'gpalfr' );

	    // Don't pass empty password to GFCommon::openssl_decrypt(); generates notice for some users.
	    $password = (string) rawurldecode( rgget( 'pass' ) );
	    if( empty( $password ) ) {
		    return;
	    }

	    $password = is_callable( array( 'GFCommon', 'openssl_decrypt' ) ) ? GFCommon::openssl_decrypt( $password ) : GFCommon::decrypt( $password );

        if( ( ! $is_activation_page && ! $force_redirect ) || ! $user_id || ! $password ) {
            return;
        }

        $this->auto_login( $user_id, $password );

        $redirect_url = apply_filters( 'gpal_auto_login_on_redirect_redirect_url', remove_query_arg( array( 'user', 'pass', 'gpalfr' ) ), $user_id );

        if( ! empty( $redirect_url ) ) {
            wp_redirect( $redirect_url );
            exit;
        }

    }

    public function auto_login( $user_id, $password ) {

        do_action( 'gpal_pre_auto_login', $user_id, $password );

        $user = new WP_User( $user_id );
        $user_data = array(
            'user_login'     => $user->user_login,
            'user_password'    => $password,
            'remember'        => false
        );

        $result = wp_signon( $user_data );

        if( ! is_wp_error( $result ) ) {
            global $current_user;
            $current_user = $result;
        }

        do_action( 'gpal_post_auto_login', $user_id, $password, $result );

    }

    public function add_auto_login_setting( $fields, $form ) {

        $fields['additional_settings']['fields'][] = array(
            'name'      => 'autoLogin',
            'label'     => __( 'Auto Login', 'gp-auto-login' ),
            'type'      => 'checkbox',
            'choices'   => array(
                array(
                    'label'         => __( 'Automatically log the user in once they are registered.', 'gp-auto-login' ),
                    'value'         => 1,
                    'name'          => 'autoLogin'
                )
            ),
            'tooltip' => sprintf( '<h6>%s</h6> %s', __( 'Auto Login', 'gp-auto-login' ), __( 'Enable this option to automatically log users in after registration.', 'gp-auto-login' ) ),
            'dependency'  => array(
                'field'   => 'feedType',
                'values'  => 'create'
            )
        );

        return $fields;
    }

    public function documentation() {
        return array(
            'type' => 'url',
            'value' => 'http://gravitywiz.com/documentation/gp-auto-login/'
        );
    }



    /**
     * @deprecated 1.3 Use add_auto_login_setting()
     */
    public function add_auto_login_option($feed, $form, $is_validation_error) {
        ?>
        <div class="margin_vertical_10">
            <label class="left_header" for="<?php echo $this->key('auto_login'); ?>">
                <?php _e('Auto Login', 'gravityperks'); ?> <?php gform_tooltip( $this->key('auto_login') ); ?>
            </label>
            <input type="checkbox" id="<?php echo $this->key('auto_login'); ?>" name="<?php echo $this->key('auto_login'); ?>" value="1" <?php checked(rgars($feed, "meta/{$this->key('auto_login')}")); ; ?> />
            <label for="<?php echo $this->key('auto_login'); ?>" class="checkbox-label">
                <?php _e('Automatically log the user in once they are registered.', 'gravityperks'); ?>
            </label>
        </div>
        <?php
    }

    /**
     * @deprecated 1.3
     */
    public function save_auto_login_option($feed) {
        $feed['meta'][$this->key('auto_login')] = rgpost($this->key('auto_login'));
        return $feed;
    }

}

class GWAutoLogin extends GP_Auto_Login { }

function gp_auto_login() {
    return GP_Auto_Login::get_instance( null );
}