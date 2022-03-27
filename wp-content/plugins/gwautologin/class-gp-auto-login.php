<?php

if ( ! class_exists( 'GP_Plugin' ) ) {
	return;
}

/**
 * TODO:
 * - Provide settings UI for where to redirect after registration.
 */
class GP_Auto_Login extends GP_Plugin {

	private static $_instance = null;

	protected $_version     = GP_AUTO_LOGIN_VERSION;
	protected $_path        = 'gwautologin/gwautologin.php';
	protected $_full_path   = __FILE__;
	protected $_slug        = 'gp-auto-login';
	protected $_title       = 'Gravity Forms Auto Login';
	protected $_short_title = 'Auto Login';

	public static function get_instance() {
		if ( self::$_instance === null ) {
			self::$_instance = isset( self::$perk ) ? new self( new self::$perk ) : new self();
		}

		return self::$_instance;
	}

	/**
	 * @depracated
	 */
	public $version = GP_AUTO_LOGIN_VERSION;

	/**
	 * @depracated
	 */
	protected $min_gravity_perks_version = '2.1.10';
	/**
	 * @depracated
	 */
	protected $min_gravity_forms_version = '1.8.9';
	/**
	 * @depracated
	 */
	protected $min_wp_version = '3.5';

	/**
	 * @depracated
	 */
	private static $instance = null;

	public function pre_init() {

		parent::pre_init();

		// As of Gravity Forms 2.5-beta-1.4, we must bind *before* init due to an order-of-events issue:
		// https://github.com/gravityforms/backlog/issues/429
		add_filter( 'gform_userregistration_feed_settings_fields', array( $this, 'add_auto_login_setting' ), 10, 2 );

	}

	public function init() {

		parent::init();

		add_action( 'gform_user_registration_signup_meta', array( $this, 'add_login_nonce_signup_meta' ), 10, 4 );
		add_action( 'gform_user_registered', array( $this, 'maybe_auto_login' ), 10, 4 );

		// Support for User Activation + Better User Activation.
		add_action( 'init', array( $this, 'auto_login_on_redirect' ), 16 );

		/*
		 * @deprecated 1.3
		 */
		add_action( 'gform_user_registration_add_option_group', array( $this, 'add_auto_login_option' ), 10, 3 );
		add_filter( 'gform_user_registration_save_config', array( $this, 'save_auto_login_option' ) );

	}

	public function tooltips( $tooltips ) {
		$tooltips['gpal_auto_login'] = '<h6>' . __( 'Auto Login', 'gp-auto-login' ) . '</h6>' . __( 'Enable this option to automatically log users in after registration.', 'gp-auto-login' );

		return $tooltips;
	}

	public function minimum_requirements() {

		$requirements = array(
			'wordpress'    => array(
				'version' => '3.5',
			),
			'gravityforms' => array(
				'version' => '1.8.9',
			),
			'plugins'      => array(
				'gravityperks/gravityperks.php' => array(
					'name'    => 'Gravity Perks',
					'version' => '2.1.10',
				),
			),
		);

		// GP 2.1.10 fixes an infinite recursion issue when checking for add-on-specific requirements.
		if ( is_callable( array( $this, 'disable_init_when_requirements_unmet' ) ) ) {
			$requirements['add-ons'] = array(
				'gravityformsuserregistration' => array(
					'name' => 'Gravity Forms User Registration',
				),
			);
		}

		return $requirements;
	}

	public function add_login_nonce_signup_meta( $meta, $form, $entry, $feed ) {

		if ( $this->is_auto_login_enabled( $feed ) ) {
			$meta['gpal_nonce'] = wp_create_nonce( "gpal_login_{$meta['email']}" );
		}

		return $meta;
	}

	public function is_auto_login_enabled( $feed ) {

		// different setting in earlier version of UR (>3.0)
		$old_setting_enabled = (bool) rgars( $feed, "meta/{$this->perk->key('auto_login')}" ) === true;
		$enabled             = (bool) rgars( $feed, 'meta/autoLogin' ) === true;

		return $enabled || $old_setting_enabled;
	}

	public function maybe_auto_login( $user_id, $feed, $entry, $password ) {

		if ( ! apply_filters( 'gpal_auto_login', $this->is_auto_login_enabled( $feed ), $user_id, $feed, $entry, $password ) ) {
			return;
		}

		/* Do not auto-login if on a Gravity Flow inbox page. */
		if ( function_exists( 'gravity_flow' ) && gravity_flow()->is_workflow_detail_page() ) {
			return;
		}

		if ( $this->is_gf_activation_page() ) {

			echo '<script type="text/javascript"> window.location = "' . $this->get_auto_login_url() . '"; </script>';
			add_filter( 'gpbua_activation_redirect_url', array( $this, 'set_gpbua_activation_redirect_url' ) );

		} else {

			$this->auto_login( $user_id );

		}

	}

	public function is_gf_activation_page() {
		$is_activation_page = isset( $_GET['page'] ) && $_GET['page'] === 'gf_activation';
		// Parameter changed in GFUR 4.6 due to issues introduced by WP 5.5.
		$is_activation_page = $is_activation_page || isset( $_GET['gfur_activation'] );
		return $is_activation_page;
	}

	public function get_auto_login_url( $user_id = null, $password = null, $url = null ) {
		$key = rgget( 'gfur_activation' );
		if ( empty( $key ) ) {
			$key = rgget( 'key' );
		}
		return add_query_arg(
			array(
				'gpal' => $key,
			),
			$url ? $url : $_SERVER['REQUEST_URI']
		);
	}

	public function set_gpbua_activation_redirect_url( $url ) {
		return $this->get_auto_login_url( null, null, $url );
	}

	/*
	 * By default, Gravity Forms User Activation feature happens after headers have already been sent so we can't set
	 * the auth headers. To work around this, with use a JS redirect and append the "gpal" parameter set to the
	 * activation key. This allows us to fetch the activation, get our nonce from the activation meta, and then safely
	 * log the user in based on the activation data.
	 */
	public function auto_login_on_redirect() {
		global $wpdb;

		$activation_key = rgget( 'gpal' );
		if ( ! $activation_key ) {
			return;
		}

		require_once( gf_user_registration()->get_base_path() . '/includes/signups.php' );
		GFUserSignups::prep_signups_functionality();

		$activation = GFSignup::get( $activation_key );

		// We should only ever find ourselves here *after* the user has already been activated.
		if ( is_wp_error( $activation ) && $activation->get_error_code() === 'already_active' ) {
			$activation       = $activation->get_error_data( 'already_active' );
			$activation->meta = unserialize( $activation->meta );
		} else {
			return;
		}

		$gpal_nonce = rgar( $activation->meta, 'gpal_nonce' );
		if ( ! $gpal_nonce ) {
			return;
		}

		// The nonce can only be used once. Remove it.
		$activation->meta['gpal_nonce'] = '';
		$wpdb->update(
			$wpdb->signups,
			array(
				'meta' => serialize( $activation->meta ),
			),
			array(
				'activation_key' => $activation_key,
			)
		);

		$email = $activation->meta['email'];
		if ( ! wp_verify_nonce( $gpal_nonce, "gpal_login_{$email}" ) ) {
			return;
		}

		$user = get_user_by( 'email', $email );
		if ( ! $user ) {
			return;
		}

		$this->auto_login( $user->ID );

		/**
		 * Filter the URL the user is redirected to after being automatically logged in.
		 *
		 * @since 1.2
		 *
		 * @param string $redirect_url URL to redirect the user to.
		 * @param string $user_id ID of the user that's about to be logged in.
		 */
		$redirect_url = apply_filters( 'gpal_auto_login_on_redirect_redirect_url', remove_query_arg( 'gpal' ), $user->ID );

		if ( ! empty( $redirect_url ) ) {
			wp_redirect( $redirect_url );
			exit;
		}

	}

	public function auto_login( $user_id, $password = null ) {

		/**
		 * Do something before the user is automatically logged in.
		 *
		 * @since 1.2.4
		 *
		 * @param string $user_id  ID of the user that's about to be logged in.
		 * @param string $password Deprecated. User's password. Always set to null as of GPAL 2.0
		 */
		do_action( 'gpal_pre_auto_login', $user_id, $password );

		// GFUR 4.5 sets the hashed password after the user has been created with a direct query and fails to update the
		// user cache. Let's clear the user cache to avoid errors where the auth cookie may not be generated with the
		// correct password.
		clean_user_cache( $user_id );

		wp_clear_auth_cookie();
		wp_set_current_user( $user_id );
		/**
		 * Filter whether the auto-login should be remembered. A remembered login will keep the user logged in longer.
		 *
		 * @since 2.2.1
		 *
		 * @param bool $should_remember Whether the login should be remembered. Defaults to `false`.
		 */
		$should_remember = apply_filters( 'gpal_should_remember', false );
		wp_set_auth_cookie( $user_id, $should_remember, is_ssl() );

		/**
		 * Do something after the user is automatically logged in.
		 *
		 * @since 1.2.4
		 *
		 * @param string $user_id  ID of the user that was automatically logged in.
		 * @param string $password Deprecated. User's password. Always set to null as of GPAL 2.0
		 */
		do_action( 'gpal_post_auto_login', $user_id, $password, true );

	}

	public function add_auto_login_setting( $fields, $form ) {

		$fields['additional_settings']['fields'][] = array(
			'name'       => 'autoLogin',
			'label'      => __( 'Auto Login', 'gp-auto-login' ),
			'type'       => 'checkbox',
			'choices'    => array(
				array(
					'label' => __( 'Automatically log the user in once they are registered.', 'gp-auto-login' ),
					'value' => 1,
					'name'  => 'autoLogin',
				),
			),
			'tooltip'    => sprintf( '<h6>%s</h6> %s', __( 'Auto Login', 'gp-auto-login' ), __( 'Enable this option to automatically log users in after registration.', 'gp-auto-login' ) ),
			'dependency' => array(
				'field'  => 'feedType',
				'values' => 'create',
			),
		);

		return $fields;
	}


	/**
	 * @deprecated 1.3 Use add_auto_login_setting()
	 */
	public function add_auto_login_option( $feed, $form, $is_validation_error ) {
		?>
		<div class="margin_vertical_10">
			<label class="left_header" for="<?php echo $this->key( 'auto_login' ); ?>">
				<?php _e( 'Auto Login', 'gravityperks' ); ?><?php gform_tooltip( $this->key( 'auto_login' ) ); ?>
			</label>
			<input type="checkbox" id="<?php echo $this->key( 'auto_login' ); ?>" name="<?php echo $this->key( 'auto_login' ); ?>" value="1" 
			<?php
				checked( rgars( $feed, "meta/{$this->key('auto_login')}" ) );
			?>
			/>
			<label for="<?php echo $this->key( 'auto_login' ); ?>" class="checkbox-label">
				<?php _e( 'Automatically log the user in once they are registered.', 'gravityperks' ); ?>
			</label>
		</div>
		<?php
	}

	/**
	 * @deprecated 1.3
	 */
	public function save_auto_login_option( $feed ) {
		$feed['meta'][ $this->key( 'auto_login' ) ] = rgpost( $this->key( 'auto_login' ) );

		return $feed;
	}

}

class GWAutoLogin extends GP_Auto_Login {
}

function gp_auto_login() {
	return GP_Auto_Login::get_instance();
}

GFAddOn::register( 'GP_Auto_Login' );
