<?php
/**
 * Create controller for promotion.
 * @var [array]
 */
include LOGINPRESS_ROOT_PATH .'classes/control-promo.php';

$wp_customize->add_section( 'lpcustomize_google_font', array(
  'title'				    => __( 'Google Fonts', 'loginpress-pro' ),
  // 'description'	    => __( 'Select Google Font', 'loginpress-pro' ),
  'priority'			  => 49,
  'panel'				    => 'loginpress_panel',
  ) );

$wp_customize->add_setting( 'loginpress_customization[google_font]', array(
  'default'         => '',
  'type'						=> 'option',
  'capability'			=> 'manage_options',
  'transport'       => 'postMessage'
) );

$wp_customize->add_control( new LoginPress_Promo( $wp_customize, 'loginpress_customization[google_font]',
array(
  'section'         => 'lpcustomize_google_font',
  'thumbnail'       => plugins_url( 'img/promo/font_promo.png', LOGINPRESS_ROOT_FILE ),
  'promo_text'      => __( 'Unlock Premium Feature', 'loginpress' ),
  'link'            => 'https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&amp;utm_medium=fonts&amp;utm_campaign=pro-upgrade'
) ) );

$wp_customize->add_section( 'customize_recaptcha', array(
  'title'				    => __( 'reCAPTCHA', 'loginpress-pro' ),
  // 'description'	   => __( 'reCAPTCHA Setting', 'loginpress-pro' ),
  'priority'			  => 24,
  'panel'				    => 'loginpress_panel',
) );

$wp_customize->add_setting( "loginpress_customization[recaptcha_error_message]", array(
  'type'						=> 'option',
  'capability'			=> 'manage_options',
  'transport'       => 'postMessage'
) );

$wp_customize->add_control( new LoginPress_Promo( $wp_customize, 'loginpress_customization[recaptcha_error_message]',
array(
  'section'         => 'customize_recaptcha',
  'thumbnail'       => plugins_url( 'img/promo/recaptcha_option_promo.png', LOGINPRESS_ROOT_FILE ),
  'promo_text'      => __( 'Unlock Premium Feature', 'loginpress' ),
  'link'            => 'https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&amp;utm_medium=recaptcha&amp;utm_campaign=pro-upgrade'
) ) );

// $wp_customize->add_setting( "loginpress_customization[reset_hint_text]", array(
//   'type'						=> 'option',
//   'capability'			=> 'manage_options',
//   'transport'       => 'postMessage'
// ) );

// $wp_customize->add_control( new LoginPress_Promo( $wp_customize, 'loginpress_customization[reset_hint_text]',
// array(
//   'section'         => 'section_welcome',
//   'thumbnail'       => plugins_url( 'img/promo/hint_promo.png', LOGINPRESS_ROOT_FILE ),
//   'promo_text'      => __( 'Unlock Premium Feature', 'loginpress' ),
//   'priority'        => 32,
//   'link'            => 'https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&amp;utm_medium=hint&amp;utm_campaign=pro-upgrade'
// ) ) );
  ?>
