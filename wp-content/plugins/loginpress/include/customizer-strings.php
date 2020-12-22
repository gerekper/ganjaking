<?php
/**
 * Customizer strings for the logo control.
 * @since 1.1.3
 * @version 1.1.22
 */
$logo_range_control = array( 'customize_logo_width', 'customize_logo_height', 'customize_logo_padding' );
$logo_range_default = array( '84', '84', '0' );
$logo_range_label = array( __( 'Logo Width:', 'loginpress' ), __( 'Logo Height:', 'loginpress' ), __( 'Space Bottom:', 'loginpress' ) );
$logo_range_attrs = array(
  array( 'min' => 0, 'max' => 500, 'step' => 1, 'suffix' => 'px' ),
  array( 'min' => 0, 'max' => 500, 'step' => 1, 'suffix' => 'px' ),
  array( 'min' => 0, 'max' => 100, 'step' => 1, 'suffix' => 'px' )
);
$logo_range_unit    = array( 'px', 'px', 'px' );

/**
 * Customizer strings for the grouping control.
 * @since 1.1.3
 */
$group_control  = array( 'login_input_group', 'login_label_group', 'login_form_group', 'footer_form_group', 'footer_back_group', 'footer_group', 'bg_image_group', 'bg_video_group' );
$group_label    = array(
  __( 'Input Fields:', 'loginpress'),
  __( 'Input Field Labels:', 'loginpress'),
  __( 'Login Form:', 'loginpress'),
  __( 'Lost Your Password Text', 'loginpress' ),
  __( 'Back To Site Text', 'loginpress' ),
  __( 'LoginPress Footer Text', 'loginpress' ),
  __( 'Background Image', 'loginpress' ),
  __( 'Background Video', 'loginpress' )  );
$group_info     = array(
  __( 'This section helps you to easily Customize the login form input field elements.', 'loginpress' ),
  __( 'This section helps you to easily Customize the login form input field labels.', 'loginpress' ),
  __( 'This section helps you to easily Customize the login form elements whether they are form lables, fields or backgrounds.', 'loginpress' ),
  __( ' Customize the "Lost your password" and "Register" text section under the form.', 'loginpress' ),
  __( 'Customize the "Back to" text section under the form.', 'loginpress' ),
  __( 'Customize the copyright note and branding sections at the footer of login page.', 'loginpress' ),
  __( 'Customize the background Image.', 'loginpress' ),
  __( 'Customize the background Video.', 'loginpress' ) );
/** ------------------Grouping Control-------------------- */

/**
 * [ Customizer strings for the section login form. ]
 * @since 1.1.3
 */
$form_range_control = array( 'customize_form_width', 'customize_form_height', 'customize_form_radius', 'customize_form_shadow', 'customize_form_opacity', 'textfield_width', 'textfield_radius', 'textfield_shadow', 'textfield_shadow_opacity', 'customize_form_label', 'remember_me_font_size' );
$form_range_default = array( '350', '200', '0', '0', '0', '100', '0', '0', '80', '14', '13' );
$form_range_label   = array(
  __( 'Form Width:', 'loginpress' ),
  __( 'Form Minimum Height:', 'loginpress' ),
  __( 'Form Radius:', 'loginpress' ),
  __( 'Form Shadow:', 'loginpress' ),
  __( 'Form Shadow Opacity:', 'loginpress' ),
  __( 'Input Text Field Width:', 'loginpress' ),
  __( 'Input Text Field Radius:', 'loginpress' ),
  __( 'Input Text Field Shadow:', 'loginpress' ),
  __( 'Input Text Field Shadow Opacity:', 'loginpress' ),
  __( 'Input Field Label Font Size:', 'loginpress' ),
  __( 'Remember Me Font Size:', 'loginpress' ) );
$form_range_attrs   = array(
  array( 'min' => 320, 'max' => 800, 'step' => 1, 'suffix' => 'px' ), // form width
  array( 'min' => 0, 'max'   => 500, 'step' => 1, 'suffix' => 'px' ), // form height
  array( 'min' => 0, 'max'   => 100, 'step' => 1, 'suffix' => 'px' ), // form radius
  array( 'min' => 0, 'max'   => 30, 'step'  => 1, 'suffix' => 'px' ), // form shadow
  array( 'min' => 0, 'max'   => 100, 'step' => 1, 'suffix' => '%' ), // form Opacity
  array( 'min' => 0, 'max'   => 100, 'step' => 1, 'suffix' => '%' ), // textfield width
  array( 'min' => 0, 'max'   => 30, 'step'  => 1, 'suffix' => 'px' ), // textfield radius
  array( 'min' => 0, 'max'   => 30, 'step'  => 1, 'suffix' => 'px' ), // textfield shadow
  array( 'min' => 0, 'max'   => 100, 'step' => 1, 'suffix' => '%' ), // textfield Opacity
  array( 'min' => 9, 'max'   => 30, 'step'  => 1, 'suffix' => 'px' ), // testfield label
  array( 'min' => 9, 'max'   => 30, 'step'  => 1, 'suffix' => 'px' ) // readme label
);
$form_range_unit    = array( 'px', 'px', 'px', 'px', '%', '%', 'px', 'px', '%', 'px', 'px' );
//--------------------
$form_color_control = array( 'form_background_color', 'textfield_background_color', 'textfield_color', 'textfield_label_color', 'remember_me_label_size' );
$form_color_default = array( '#FFF', '#FFF', '#333', '#777', '#72777c' );
$form_color_label   = array(
  __( 'Form Background Color:', 'loginpress' ),
  __( 'Input Field Background Color:', 'loginpress' ),
  __( 'Input Field Text Color:', 'loginpress' ),
  __( 'Input Field Label Color:', 'loginpress' ),
  __( 'Remember me Label Color:', 'loginpress' ),
);
//--------------------
$form_control       = array( 'customize_form_padding', 'customize_form_border', 'textfield_margin', 'form_username_label', 'form_password_label' );
$form_default       = array( '0 24px 12px', '', '2px 6px 18px 0px', __( 'Username or Email Address', 'loginpress' ), __( 'Password', 'loginpress' ) );
$form_label         = array(
  __( 'Form Padding:', 'loginpress' ),
  __( 'Border (Example: 2px dotted black):', 'loginpress' ),
  __( 'Input Text Field Margin:', 'loginpress' ),
  __( 'Username Label:', 'loginpress' ),
  __( 'Password Label:', 'loginpress' ),
);
$form_sanitization = array( 'wp_strip_all_tags', 'wp_strip_all_tags', 'wp_strip_all_tags', 'wp_strip_all_tags', 'wp_strip_all_tags' );
/** -----------------Sectin Login Form------------------ */

/**
 * [ Customizer strings for the section button beauty. ]
 * @since 1.1.3
 * @version 1.4.3
 */
 $button_control = array( 'custom_button_color', 'button_border_color', 'button_hover_color', 'button_hover_border', 'custom_button_shadow', 'button_text_color', 'button_hover_text_color' );
 $button_default = array( '#2EA2CC', '#0074A2', '#1E8CBE', '#0074A2', '#78C8E6', '#FFF', '#FFF' );
 $button_label = array(
   __( 'Button Color:', 'loginpress' ),
   __( 'Button Border Color:', 'loginpress' ),
   __( 'Button Color (Hover):', 'loginpress' ),
   __( 'Button Border (Hover):', 'loginpress' ),
   __( 'Button Box Shadow:', 'loginpress' ),
   __( 'Button Text Color:', 'loginpress' ),
   __( 'Button Text Color (Hover):', 'loginpress' )
 );

$button_range_control = array( 'login_button_size', 'login_button_top', 'login_button_bottom', 'login_button_radius', 'login_button_shadow', 'login_button_shadow_opacity', 'login_button_text_size' );
$button_range_default = array( '100', '13', '13', '5', '0', '80', '15' );
$button_range_label = array( __( 'Button Size:', 'loginpress' ), __( 'Button Top Padding:', 'loginpress' ), __( 'Button Bottom Padding:', 'loginpress' ), __( 'Radius:', 'loginpress' ), __( 'Shadow:', 'loginpress' ), __( 'Shadow Opacity:', 'loginpress' ), __( 'Text Size:', 'loginpress' ) );
$button_range_attrs = array(
  array( 'min' => 20, 'max' => 100, 'step' => 1, 'suffix' => '%' ),
  array( 'min' => 0, 'max'  => 30, 'step'  => 1, 'suffix' => 'px' ),
  array( 'min' => 0, 'max'  => 30, 'step'  => 1, 'suffix' => 'px' ),
  array( 'min' => 0, 'max'  => 50, 'step'  => 1, 'suffix' => 'px' ),
  array( 'min' => 0, 'max'  => 30, 'step'  => 1, 'suffix' => 'px' ),
  array( 'min' => 0, 'max'  => 100, 'step' => 1, 'suffix' => 'px' ),
  array( 'min' => 7, 'max'  => 35, 'step'  => 1, 'suffix' => 'px' ),
);
$button_range_unit = array( '%', 'px', 'px', 'px', 'px', '%', 'px' );
/** -----------------Section Button Beauty------------------ */

/**
 * [ Customizer strings for the group close. ]
 * @since 1.1.3
 */
$close_control = array( 'login_input_br', 'login_label_br', 'login_form_br', 'footer_form_br', 'footer_back_br', 'footer_br' );
/** -----------------Section Login Footer------------------ */

/**
 * [ Customizer strings for the error messages. ]
 * @since 1.1.22
 */
$error_control = array( 'incorrect_username', 'incorrect_password', 'empty_username', 'empty_password', 'invalid_email', 'empty_email', 'username_exists', 'email_exists', 'invalidcombo_message', 'force_email_login' );
$error_default = array(
  sprintf( __( '%1$sError:%2$s Invalid Username.', 'loginpress' ), '<strong>', '</strong>' ), sprintf( __( '%1$sError:%2$s Invalid Password.', 'loginpress' ), '<strong>', '</strong>' ),
  sprintf( __( '%1$sError:%2$s The username field is empty.', 'loginpress' ), '<strong>', '</strong>' ),
  sprintf( __( '%1$sError:%2$s The password field is empty.', 'loginpress' ), '<strong>', '</strong>' ),
  sprintf( __( '%1$sError:%2$s The email address isn\'t correct..', 'loginpress' ), '<strong>', '</strong>' ),
  sprintf( __( '%1$sError:%2$s Please type your email address.', 'loginpress' ), '<strong>', '</strong>' ),
  sprintf( __( '%1$sError:%2$s This username is already registered. Please choose another one.', 'loginpress' ), '<strong>', '</strong>' ),
  sprintf( __( '%1$sError:%2$s This email is already registered, please choose another one.', 'loginpress' ), '<strong>', '</strong>' ),
  sprintf( __( '%1$sError:%2$s Invalid username or email.', 'loginpress' ), '<strong>', '</strong>' ),
  sprintf( __( '%1$sError:%2$s Invalid Email Address', 'loginpress' ), '<strong>', '</strong>' ) );
$error_label = array(
  __( 'Incorrect Username Message:',  'loginpress' ),
  __( 'Incorrect Password Message:',  'loginpress' ),
  __( 'Empty Username Message:',      'loginpress' ),
  __( 'Empty Password Message:',      'loginpress' ),
  __( 'Invalid Email Message:',       'loginpress' ),
  __( 'Empty Email Message:',         'loginpress' ),
  __( 'Username Already Exist Message:','loginpress' ),
  __( 'Email Already Exist Message:', 'loginpress' ),
  __( 'Forget Password Message:',     'loginpress' ),
  __( 'Login with Email Message:',    'loginpress' ),
);
/** -----------------Error Section------------------ */

/**
 * [ Customizer strings for the welcome messages. ]
 * @since 1.1.22
 */
$welcome_control = array( 'lostpwd_welcome_message', 'welcome_message', 'register_welcome_message', 'logout_message', 'message_background_border' );
$welcome_default = array( 'Forgot password?', 'Welcome', 'Register For This Site', 'Logout', '' );
$welcome_label	 = array(
  __( 'Welcome Message on Lost Password:', 'loginpress' ),
  __( 'Welcome Message on Login Page:', 'loginpress' ),
  __( 'Welcome Message on Registration:', 'loginpress' ),
  __( 'Logout Message:', 'loginpress' ),
  __( 'Message Field Border: ( Example: 1px solid #00a0d2; )', 'loginpress' ),
);
$welcome_sanitization = array( 'wp_kses_post', 'wp_kses_post', 'wp_kses_post', 'wp_kses_post', 'wp_strip_all_tags' );
