<?php

class GPBUA_Merge_Tags {

  public function __construct() {

    add_filter( 'the_content', array( $this, 'replace_merge_tags' ), 15 );

  }

  public function replace_merge_tags( $content ) {

    if ( $GLOBALS['post']->ID != gpbua_get_activation_page_id() ) {
      return $content;
    }

    // get the gf activation signup
    $activate = GPBUA_Activate::get_instance();
    $signup = $activate->get_signup();

    $aux_data = array( 'gpbua' => $this->get_merge_tags_data() );

    // set form and lead if possible to support entry tags
    $form = array();
    $entry = array();

    if ($signup instanceof GFSignup) {

      // we have signup
      $form = $signup->form;
      $entry = $signup->lead;

    } elseif( is_wp_error( $signup ) && $signup->get_error_code() == 'already_active' ) {

      // no signup object but we can load from error object if the error is "already_active"
      $error_data = $signup->get_error_data( $signup->get_error_code() );
      $signup = unserialize( $error_data->meta );
      $entry = GFAPI::get_entry( $signup['lead_id'] );
      $form = GFAPI::get_form( $entry['form_id'] );

    }

    return GFCommon::replace_variables( $content, $form, $entry, false, false, false, 'html', $aux_data );

  }

	private function define_merge_tags() {
		return array(
			'login'              => array( 'label' => __( 'Login', 'gp-better-user-activation' ),              'value' => $this->tag_login(),              'tag' => '{gpbua:login}' ),
			'login_url'          => array( 'label' => __( 'Login URL', 'gp-better-user-activation' ),          'value' => $this->tag_login_url(),          'tag' => '{gpbua:login_url}' ),
			'home'               => array( 'label' => __( 'Home', 'gp-better-user-activation' ),               'value' => $this->tag_home(),               'tag' => '{gpbua:home}' ),
			'home_url'           => array( 'label' => __( 'Home URL', 'gp-better-user-activation' ),           'value' => $this->tag_home_url(),           'tag' => '{gpbua:home_url}' ),
			'reset_password'     => array( 'label' => __( 'Reset Password', 'gp-better-user-activation' ),     'value' => $this->tag_reset_password(),     'tag' => '{gpbua:reset_password}' ),
			'reset_password_url' => array( 'label' => __( 'Reset Password URL', 'gp-better-user-activation' ), 'value' => $this->tag_reset_password_url(), 'tag' => '{gpbua:reset_password_url}' ),
			'username'           => array( 'label' => __( 'Username', 'gp-better-user-activation' ),           'value' => $this->tag_username(),           'tag' => '{gpbua:username}' ),
			'password'           => array( 'label' => __( 'Password', 'gp-better-user-activation' ),           'value' => $this->tag_password(),           'tag' => '{gpbua:password}' ),
			'email'              => array( 'label' => __( 'Email', 'gp-better-user-activation' ),              'value' => $this->tag_email(),              'tag' => '{gpbua:email}' ),
			'activation_form'    => array( 'label' => __( 'Activation Form', 'gp-better-user-activation' ),    'value' => $this->tag_activation_form(),    'tag' => '{gpbua:activation_form}' ),
			'error_message'      => array( 'label' => __( 'Error Message', 'gp-better-user-activation' ),      'value' => $this->tag_error_message(),      'tag' => '{gpbua:error_message}' ),
		);
	}

	public function get_merge_tags_data() {
  	    $data = array();
  	    foreach( $this->define_merge_tags() as $key => $tag ) {
  	    	$data[ $key ] = $tag['value'];
        }
        return $data;
	}

  public function tag_login() {
    return '<a class="gpbua-login-link" href="' . wp_login_url() . '">' . __( 'Login', 'gp-better-user-activation' ) . '</a>';
  }

  public function tag_login_url() {
    return wp_login_url();
  }

  public function tag_home() {
    return '<a class="gpbua-home-link" href="' . get_home_url() . '">' . __( 'Home', 'gp-better-user-activation' ) . '</a>';
  }

  public function tag_home_url() {
    return get_home_url();
  }

  public function tag_reset_password() {
    return '<a class="gpbua-reset-password-link" href="' . wp_lostpassword_url() . '">' . __( 'Reset your password', 'gp-better-user-activation' ) . '</a>';
  }

  public function tag_reset_password_url() {
    return wp_lostpassword_url();
  }

	public function tag_username() {

		$user = $this->get_user_from_activation_result();
		if( $user ) {
			return $user->user_login;
		}

		return '';
	}

	public function tag_email() {
		$user = $this->get_user_from_activation_result();
		if( $user ) {
			return $user->user_email;
		}
	}

	public function tag_activation_form() {
		ob_start();
		require( gpbua()->get_base_path() . '/templates/activate-form.php' );
		return ob_get_clean();
	}

	private function tag_password() {

		// get the gf activation result
		$activate = GPBUA_Activate::get_instance();
		$activation_result = $activate->get_result();

		if( ! is_wp_error( $activation_result ) ) {
			return rgar( $activation_result, 'password' );
		}

		return '';
	}

	public function tag_error_message() {

	    $activate = GPBUA_Activate::get_instance();
		$result = $activate->get_result();

		if( is_wp_error( $result ) ) {
			return $result->get_error_message();
		}

		return '';
	}

	/*
	* Pass filter as no_entry to reduce list to only those that do not require an entry (not entry-based)
	*/
	public static function get_merge_tags( $view ) {

		$views = array(
			'success'              => array( 'login', 'login_url', 'home', 'home_url', 'reset_password', 'reset_password_url', 'username', 'password', 'email', 'activation_form' ),
			'error_already_active' => array( 'login', 'login_url', 'home', 'home_url', 'reset_password', 'reset_password_url', 'username', 'email', 'error_message', 'activation_form' ),
			'error_no_key'         => array( 'login', 'login_url', 'home', 'home_url', 'reset_password', 'reset_password_url', 'activation_form' ),
			'error'                => array( 'login', 'login_url', 'home', 'home_url', 'reset_password', 'reset_password_url', 'error_message', 'activation_form' ),
		);

		$mt        = new GPBUA_Merge_Tags;
		$tags      = array();
		$view_tags = $views[ $view ];

		$defined = $mt->define_merge_tags();

		foreach( $defined as $key => $tag ) {
			if( in_array( $key, $view_tags ) ) {
				$tags[ $key ] = $tag;
			}
		}

		return $tags;
	}

	public function get_user_from_activation_result() {

		$activate = GPBUA_Activate::get_instance();
		$result   = $activate->get_result();

		if( is_wp_error( $result ) ) {
			if( $result->get_error_code() == 'already_active' ) {
				return new WP_User( 0, $result->get_error_data( 'already_active' )->user_login );
			} else {
				return false;
			}
		}

		return new WP_User( $result['user_id'] );
	}

}
