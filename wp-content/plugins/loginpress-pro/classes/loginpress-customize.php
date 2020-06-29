<?php
/**
 * Customizer Sections for LoginPress Pro.
 *
 * @since 1.0.0
 */
class LoginPress_Pro_Entities {

  /* * * * * * * * * *
    * Class constructor
    * * * * * * * * * */
  public function __construct() {

    $this->_hooks();
  }

  /**
    * Hook into actions and filters
    * @since  1.0.0
    */
  public function _hooks() {

    add_action( 'customize_register', array( $this, 'customize_pro_login_panel' ) );
  }

  /**
  * Register plugin settings Panel in WP Customizer
  *
  * @param	$wp_customize
  * @since	1.0.0
  */
  public function customize_pro_login_panel( $wp_customize ) {

    //	============================================
    //	= Section for Google reCAPTCHA since 1.0.0 =
    //	============================================
    $wp_customize->add_section( 'customize_recaptcha', array(
      'title'				   => __( 'reCAPTCHA', 'loginpress-pro' ),
      // 'description'	   => __( 'reCAPTCHA Setting', 'loginpress-pro' ),
      'priority'			 => 24,
      'panel'				   => 'loginpress_panel',
      ) );

    $wp_customize->add_setting( "loginpress_customization[recaptcha_error_message]", array(
      'default'					  => __( '<strong>ERROR:</strong> Please verify reCAPTCHA', 'loginpress-pro' ),
      'type'						  => 'option',
      'capability'			  => 'manage_options',
      'transport'         => 'postMessage',
      'sanitize_callback' => 'wp_kses_post'
    ) );

    $wp_customize->add_control( "loginpress_customization[recaptcha_error_message]", array(
      'label'						   => __( 'reCAPTCHA Error Message:', 'loginpress-pro' ),
      'section'					   => 'customize_recaptcha',
      'priority'					 => 5,
      'settings'					 => "loginpress_customization[recaptcha_error_message]"
    ) );

    //  =====================
    //  = Select Scale Size =
    //  =====================
     $wp_customize->add_setting('loginpress_customization[recaptcha_size]', array(
        'default'        => '1',
        'capability'     => 'edit_theme_options',
        'transport'      => 'postMessage',
        'type'           => 'option',

    ) );
    $wp_customize->add_control( 'loginpress_customization[recaptcha_size]', array(
        'label'         => __( 'Select reCAPTCHA size:', 'loginpress-pro' ),
        'section'       => 'customize_recaptcha',
        'priority'      => 10,
        'settings'      => 'loginpress_customization[recaptcha_size]',
        'type'          => 'select',
        'description'   => __( 'Size is only apply on "V2-I\'m not robot" reCaptcha type.', 'loginpress-pro'),
        'choices'       => array(
          '.1'    => '10%',
          '.2'    => '20%',
          '.3'    => '30%',
          '.4'    => '40%',
          '.5'    => '50%',
          '.6'    => '60%',
          '.7'    => '70%',
          '.8'    => '80%',
          '.9'    => '90%',
          '1'     => '100%',
        ),
    ) );

    //	========================================
    //	= Section for Google Fonts since 2.0.0 =
    //	========================================
    $wp_customize->add_section( 'lpcustomize_google_font', array(
      'title'				   => __( 'Google Fonts', 'loginpress-pro' ),
      // 'description'	   => __( 'Select Google Font', 'loginpress-pro' ),
      'priority'			 => 2,
      'panel'				   => 'loginpress_panel',
      ) );

    // Add a Google Font control
    require_once LOGINPRESS_PRO_ROOT_PATH . '/classes/loginpress-google-font.php';
    $wp_customize->add_setting( 'loginpress_customization[google_font]', array(
      'default'          => '',
      'type'						 => 'option',
      'capability'			 => 'manage_options',
      'transport'        => 'postMessage'
    ) );
    $wp_customize->add_control( new LoginPress_Google_Fonts( $wp_customize, 'loginpress_customization[google_font]', array(
      'label'     => __( 'Select Google Font', 'loginpress-pro' ),
      'section'   => 'lpcustomize_google_font',
      'settings'  => 'loginpress_customization[google_font]',
      'priority'  => 20
    ) ) );

    //	==========================================================
    //	= Setting for reset password "Text and Hint" since 2.0.3 =
    //	==========================================================
    // $wp_customize->add_setting( "loginpress_customization[reset_hint_message]", array(
    //   'default'				   => __( 'Enter your new password below.', 'loginpress-pro' ),
    //   'capability'		   => 'manage_options',
    //   'type'					   => 'option',
    //   'transport'        => 'postMessage'
    // ) );
    //
    // $wp_customize->add_control( "loginpress_customization[reset_hint_message]", array(
    //   'label'						 => __( 'Reset Password Message:', 'loginpress-pro' ),
    //   'section'					 => 'section_welcome',
    //   'priority'				 => 30,
    //   'settings'				 => "loginpress_customization[reset_hint_message]",
    // ) );

    $wp_customize->add_setting( "loginpress_customization[reset_hint_text]", array(
      'default'				    => __( 'Hint: The password should be at least twelve characters long. To make it stronger, use upper and lower case letters, numbers, and symbols like ! " ? $ % ^ &amp; ).' ),
      'capability'		    => 'manage_options',
      'transport'         => 'postMessage',
      'sanitize_callback' => 'sanitize_text_field',
      'type'					    => 'option',
      'sanitize_callback' => 'wp_kses_post'
    ) );

    $wp_customize->add_control( "loginpress_customization[reset_hint_text]", array(
      'label'						 => __( 'Reset Password Hint:', 'loginpress-pro' ),
      'section'					 => 'section_welcome',
      'priority'				 => 32,
      'settings'				 => "loginpress_customization[reset_hint_text]",
      'type'					   => 'textarea',
      'description'      => __( "You can change the Hint text that is comes on reset password page.", 'loginpress-pro' ),
    ) );

  } // !customize_pro_login_panel().
}

?>
