<?php
/**
 * Get option and check the key exists in it.
 *
 * @since 1.0.0
 * @version 1.4.3
 * * * * * * * * * * * * * * * */


 /**
 * @var loginpress_array get_option
 * @since 1.0.0
 */
$loginpress_array  = (array) get_option( 'loginpress_customization' );
$loginpress_preset = get_option( 'customize_presets_settings', 'default1' );

function loginpress_get_option_key( $loginpress_key, $loginpress_array ) {

	if ( array_key_exists( $loginpress_key, $loginpress_array ) ) {

		if ( 'loginpress_custom_css' == $loginpress_key ) {
			return $loginpress_array[ $loginpress_key ];
		} else {
			return esc_js( $loginpress_array[ $loginpress_key ] );
		}

	}
}

/**
 * [loginpress_bg_option Check the background image of the template.]
 * @param  string $loginpress_key   [description]
 * @param  array $loginpress_array [description]
 * @return string                   [description]
 * @since 1.1.0
 * @version 1.1.1
 */
function loginpress_bg_option( $loginpress_key, $loginpress_array ) {

	if ( array_key_exists( $loginpress_key, $loginpress_array ) ) {

		return $loginpress_array[ $loginpress_key ];
	} else {
    return true;
  }
}

/**
 * [loginpress_check_px Return the value with 'px']
 * @param  string $value [description]
 * @return string        [description]
 * @since 1.1.0
 */
function loginpress_check_px( $value ) {

  if ( strpos( $value, "px" ) ) {
    return $value;
  } else {
		if ( ! empty( $value ) ) {
			return $value . 'px';
		}
  }
}

/**
 * [loginpress_check_percentage Return the value with '%']
 * @param  string $value [description]
 * @return string        [description]
 * @since 1.1.0
 */
function loginpress_check_percentage( $value ) {

  if ( strpos( $value, "%" ) ) {
    return $value;
  } else {
		if ( ! empty( $value ) ) {
			return $value . '%';
		}
  }
}

/**
 * [if for login page background]
 * @since 1.1.0
 * @version 1.1.2
 * @return string
 */
$loginpress_custom_background  = loginpress_get_option_key( 'setting_background', $loginpress_array );
$loginpress_gallery_background = loginpress_get_option_key( 'gallery_background', $loginpress_array );
if ( ! empty ( $loginpress_custom_background ) ) { // Use Custom Background
	$loginpress_background_img = $loginpress_custom_background;
} else if ( ! empty ( $loginpress_gallery_background ) ) { // Background from Gallery Control.
	if ( LOGINPRESS_DIR_URL . 'img/gallery/img-1.jpg' == $loginpress_gallery_background ) { // If user select 1st image from gallery control then show template's default image.
		$loginpress_background_img = '';
	} else { // Use selected image from gallery control.
		$loginpress_background_img = $loginpress_gallery_background;
	}
} else { // exceptional case (use default image).
	$loginpress_background_img = '';
}

/**
 * Add !important with property's value. To avoid overriding from theme.
 * @return string
 * @since 1.1.2
 */
function loginpress_important() {

	$important = '';
	if ( ! is_customize_preview() ) { // Avoid !important in customizer previewer.
		$important = ' !important';
	}
	return $important;
}

$loginpress_logo_img 						= loginpress_get_option_key( 'setting_logo', $loginpress_array );
$loginpress_logo_display				= loginpress_get_option_key( 'setting_logo_display', $loginpress_array );
$loginpress_get_logo_width 			= loginpress_get_option_key( 'customize_logo_width', $loginpress_array );
$loginpress_logo_width          = loginpress_check_px( $loginpress_get_logo_width );
$loginpress_get_logo_height 		= loginpress_get_option_key( 'customize_logo_height', $loginpress_array );
$loginpress_logo_height         = loginpress_check_px( $loginpress_get_logo_height );
$loginpress_get_logo_padding 		= loginpress_get_option_key( 'customize_logo_padding', $loginpress_array );
$loginpress_logo_padding        = loginpress_check_px( $loginpress_get_logo_padding );
$loginpress_btn_bg 							= loginpress_get_option_key( 'custom_button_color', $loginpress_array );
$loginpress_btn_border 					= loginpress_get_option_key( 'button_border_color', $loginpress_array );
$loginpress_btn_shadow 					= loginpress_get_option_key( 'custom_button_shadow', $loginpress_array );
$loginpress_btn_color 					= loginpress_get_option_key( 'button_text_color', $loginpress_array );
$loginpress_btn_hover_color			= loginpress_get_option_key( 'button_hover_text_color', $loginpress_array );
$loginpress_btn_hover_bg 				= loginpress_get_option_key( 'button_hover_color', $loginpress_array );
$loginpress_btn_hover_border 	  = loginpress_get_option_key( 'button_hover_border', $loginpress_array );
// $loginpress_background_img			= loginpress_get_option_key( 'setting_background', $loginpress_array );
$loginpress_background_color		= loginpress_get_option_key( 'setting_background_color', $loginpress_array );
$loginpress_background_repeat	  = loginpress_get_option_key( 'background_repeat_radio', $loginpress_array );
$loginpress_background_postion	= loginpress_get_option_key( 'background_position', $loginpress_array );
$loginpress_background_postion	= str_replace( '-', ' ', $loginpress_background_postion );
$loginpress_background_image_size = loginpress_get_option_key( 'background_image_size', $loginpress_array );
$loginpress_form_background_img = loginpress_get_option_key( 'setting_form_background', $loginpress_array );
$loginpress_form_display_bg 		= loginpress_get_option_key( 'setting_form_display_bg', $loginpress_array );
$loginpress_form_background_clr = loginpress_get_option_key( 'form_background_color', $loginpress_array );
$loginpress_forget_form_bg_img  = loginpress_get_option_key( 'forget_form_background', $loginpress_array );
$loginpress_forget_form_bg_clr  = loginpress_get_option_key( 'forget_form_background_color', $loginpress_array );
$loginpress_form_width 			 	  = loginpress_get_option_key( 'customize_form_width', $loginpress_array );
$loginpress_get_form_height 		= loginpress_get_option_key( 'customize_form_height', $loginpress_array );
$loginpress_form_height         = loginpress_check_px( $loginpress_get_form_height );
$loginpress_form_padding 			  = loginpress_get_option_key( 'customize_form_padding', $loginpress_array );
$loginpress_form_border 			 	= loginpress_get_option_key( 'customize_form_border', $loginpress_array );
$loginpress_form_field_width 	  = loginpress_get_option_key( 'textfield_width', $loginpress_array );
$loginpress_form_field_margin 	= loginpress_get_option_key( 'textfield_margin', $loginpress_array );
$loginpress_form_field_bg 			= loginpress_get_option_key( 'textfield_background_color', $loginpress_array );
$loginpress_form_field_color 	  = loginpress_get_option_key( 'textfield_color', $loginpress_array );
$loginpress_form_field_label 	  = loginpress_get_option_key( 'textfield_label_color', $loginpress_array );
$loginpress_form_remeber_label  = loginpress_get_option_key( 'remember_me_label_size', $loginpress_array );
$loginpress_welcome_bg_color		= loginpress_get_option_key( 'message_background_color', $loginpress_array );
$loginpress_welcome_bg_border   = loginpress_get_option_key( 'message_background_border', $loginpress_array );
$loginpress_footer_display			= loginpress_get_option_key( 'footer_display_text', $loginpress_array );
$loginpress_footer_decoration   = loginpress_get_option_key( 'login_footer_text_decoration', $loginpress_array );
$loginpress_footer_text_color   = loginpress_get_option_key( 'login_footer_color', $loginpress_array );
$loginpress_footer_text_hover   = loginpress_get_option_key( 'login_footer_color_hover', $loginpress_array );
$loginpress_get_footer_font_size= loginpress_get_option_key( 'login_footer_font_size', $loginpress_array );
$loginpress_footer_font_size    = loginpress_check_px( $loginpress_get_footer_font_size );
$loginpress_remember_me_font_size= loginpress_get_option_key( 'remember_me_font_size', $loginpress_array );
$loginpress_form_label_font_size= loginpress_get_option_key( 'customize_form_label', $loginpress_array );
$loginpress_login_button_top		= loginpress_get_option_key( 'login_button_top', $loginpress_array );
$loginpress_login_button_bottom	= loginpress_get_option_key( 'login_button_bottom', $loginpress_array );
$loginpress_login_button_radius	= loginpress_get_option_key( 'login_button_radius', $loginpress_array );
$loginpress_login_button_shadow	= loginpress_get_option_key( 'login_button_shadow', $loginpress_array );
$loginpress_login_button_shadow_opacity	= loginpress_get_option_key( 'login_button_shadow_opacity', $loginpress_array );
$loginpress_login_button_width	= loginpress_get_option_key( 'login_button_size', $loginpress_array );
$loginpress_login_form_radius 	= loginpress_get_option_key( 'customize_form_radius', $loginpress_array );
$loginpress_login_form_shadow	= loginpress_get_option_key( 'customize_form_shadow', $loginpress_array );
$loginpress_login_form_inset	= loginpress_get_option_key( 'textfield_inset_shadow', $loginpress_array );
$loginpress_login_form_opacity	= loginpress_get_option_key( 'customize_form_opacity', $loginpress_array );
$loginpress_login_textfield_radius= loginpress_get_option_key( 'textfield_radius', $loginpress_array );
$loginpress_login_button_text_size= loginpress_get_option_key( 'login_button_text_size', $loginpress_array );
$loginpress_textfield_shadow	= loginpress_get_option_key( 'textfield_shadow', $loginpress_array );
$loginpress_textfield_shadow_opacity= loginpress_get_option_key( 'textfield_shadow_opacity', $loginpress_array );
$loginpress_footer_bg_color 		= loginpress_get_option_key( 'login_footer_bg_color', $loginpress_array );
$loginpress_footer_links_font_size = loginpress_get_option_key( 'login_footer_links_text_size', $loginpress_array );
$loginpress_footer_links_hover_size = loginpress_get_option_key( 'login_footer_links_hover_size', $loginpress_array );
$loginpress_header_text_color   = loginpress_get_option_key( 'login_head_color', $loginpress_array );
$loginpress_header_text_hover   = loginpress_get_option_key( 'login_head_color_hover', $loginpress_array );
$loginpress_header_font_size 	  = loginpress_get_option_key( 'login_head_font_size', $loginpress_array );
$loginpress_header_bg_color 		= loginpress_get_option_key( 'login_head_bg_color', $loginpress_array );
$loginpress_back_display			 	= loginpress_get_option_key( 'back_display_text', $loginpress_array );
$loginpress_back_decoration  	  = loginpress_get_option_key( 'login_back_text_decoration', $loginpress_array );
$loginpress_back_text_color  	  = loginpress_get_option_key( 'login_back_color', $loginpress_array );
$loginpress_back_text_hover  	  = loginpress_get_option_key( 'login_back_color_hover', $loginpress_array );
$loginpress_get_back_font_size 	= loginpress_get_option_key( 'login_back_font_size', $loginpress_array );
$loginpress_back_font_size      = loginpress_check_px( $loginpress_get_back_font_size );
$copyright_background_color 				= loginpress_get_option_key( 'copyright_background_color', $loginpress_array );
$copyright_text_color 							= loginpress_get_option_key( 'copyright_text_color', $loginpress_array );
// $show_some_love_text_color 			= loginpress_get_option_key( 'show_some_love_text_color', $loginpress_array );
$loginpress_back_bg_color 			= loginpress_get_option_key( 'login_back_bg_color', $loginpress_array );
$loginpress_footer_link_color	  = loginpress_get_option_key( 'login_footer_text_color', $loginpress_array );
$loginpress_footer_link_hover	  = loginpress_get_option_key( 'login_footer_text_hover', $loginpress_array );
$loginpress_footer_link_bg_clr	= loginpress_get_option_key( 'login_footer_backgroung_hover', $loginpress_array );
$loginpress_custom_css 			 	  = loginpress_get_option_key( 'loginpress_custom_css', $loginpress_array );
$loginpress_display_bg 	        = loginpress_bg_option( 'loginpress_display_bg', $loginpress_array );
$loginpress_display_bg_video    = loginpress_bg_option( 'loginpress_display_bg_video', $loginpress_array );
$loginpress_bg_video 	  				= loginpress_get_option_key( 'background_video', $loginpress_array );
$loginpress_bg_video 						= wp_get_attachment_url( $loginpress_bg_video );
$loginpress_bg_video_size 	  	= loginpress_get_option_key( 'background_video_object', $loginpress_array );
$loginpress_bg_video_position  	= loginpress_get_option_key( 'video_obj_position', $loginpress_array );
$loginpress_bg_video_muted      = loginpress_bg_option( 'background_video_muted', $loginpress_array );
$loginpress_theme_tem           = get_option( 'customize_presets_settings', 'default1' );
$loginpress_video_voice 				= ( 1 == $loginpress_bg_video_muted ) ? 'muted' : '';

/**
 * loginpress_box_shadow [if user pass 0 then we're not going to set the value of box-shedow because it effects the pro templates.]
 * @param  integer $shadow         [Shadow Value]
 * @param  integer $opacity        [Opacity Value]
 * @param  integer $default_shadow [Sset shadow's default value]
 * @param  boolean $inset 				 [description]
 * @return string                  [box-border value]
 * @since 1.1.3
 */
$loginpress_inset = $loginpress_login_form_inset ? true : false; //var_dump($loginpress_inset);
function loginpress_box_shadow( $shadow, $opacity, $default_shadow = 0, $inset = false ) {

	$loginpress_shadow  = ! empty( $shadow )  ? $shadow  : $default_shadow;
	$loginpress_opacity = ! empty( $opacity ) ? $opacity : 80;
	$inset              = $inset ? ' inset'              : '';
	$opacity_convertion = $loginpress_opacity / 100;
	$loginpress_rgba    = 'rgba( 0,0,0,' . $opacity_convertion . ' )';

	return '0 0 ' . $loginpress_shadow . 'px ' . $loginpress_rgba . $inset . ';';
}
// ob_start();
?>
<style type="text/css">
*{
	box-sizing: border-box;
}


.login form input[type=checkbox]:focus{
	box-shadow: none;
    outline: none;
}
.login form .forgetmenot{
	float: none;
}
.login form .forgetmenot label{
	display:inline-block;
	margin: 0;
}
#login::after{
  <?php if ( ( $loginpress_theme_tem == 'default6' || $loginpress_theme_tem == 'default10' ) && ! empty( $loginpress_background_img ) && $loginpress_display_bg ) : ?>
	background-image: url(<?php echo $loginpress_background_img; ?>);

  <?php elseif (  ( $loginpress_theme_tem == 'default6' || $loginpress_theme_tem == 'default10' ) &&  isset( $loginpress_display_bg ) && ! $loginpress_display_bg ) : ?>
	background-image: url();
	<?php endif; ?>
  <?php if( in_array( $loginpress_theme_tem, array( 'default6', 'default10' ) ) ) : ?>
    <?php if ( ! empty( $loginpress_background_color ) ) : ?>
  	background-color: <?php echo $loginpress_background_color; ?>;
  	<?php endif; ?>
    <?php if ( ! empty( $loginpress_background_repeat ) ) : ?>
  	background-repeat: <?php echo $loginpress_background_repeat; ?>;
  	<?php endif; ?>
  	<?php if ( ! empty( $loginpress_background_postion ) ) : ?>
  	background-position: <?php echo $loginpress_background_postion; ?>;
  	<?php endif; ?>
  	<?php if ( ! empty( $loginpress_background_image_size ) ) : ?>
  	background-size: <?php echo $loginpress_background_image_size; ?>;
  	<?php endif; ?>
	<?php endif; ?>
}

#login{
  <?php if ( $loginpress_theme_tem == 'default17' && ! empty( $loginpress_background_img ) && $loginpress_display_bg ) : ?>
	background-image: url(<?php echo $loginpress_background_img; ?>);
  <?php elseif ( $loginpress_theme_tem == 'default17' &&  isset( $loginpress_display_bg ) && ! $loginpress_display_bg ) : ?>
	background-image: url();
	<?php endif; ?>

  <?php if( $loginpress_theme_tem == 'default17' ) : ?>
    <?php if ( ! empty( $loginpress_background_color ) ) : ?>
  	background-color: <?php echo $loginpress_background_color; ?>;
  	<?php endif; ?>
    <?php if ( ! empty( $loginpress_background_repeat ) ) : ?>
  	background-repeat: <?php echo $loginpress_background_repeat; ?>;
  	<?php endif; ?>
  	<?php if ( ! empty( $loginpress_background_postion ) ) : ?>
  	background-position: <?php echo $loginpress_background_postion; ?>;
  	<?php endif; ?>
  	<?php if ( ! empty( $loginpress_background_image_size ) ) : ?>
  	background-size: <?php echo $loginpress_background_image_size; ?>;
    <?php endif; ?>
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_form_display_bg ) && true == $loginpress_form_display_bg ) : ?>
	background: transparent;
	<?php endif; ?>
	<?php if ( true != $loginpress_form_display_bg && ! empty( $loginpress_form_background_clr ) ) : ?>
	background-color: <?php echo $loginpress_form_background_clr; ?>;
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_login_form_radius ) ) : ?>
	border-radius: <?php echo $loginpress_login_form_radius . 'px'; ?>;
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_login_form_shadow ) && ! empty( $loginpress_login_form_opacity ) ) : ?>
	box-shadow: <?php echo loginpress_box_shadow( $loginpress_login_form_shadow, $loginpress_login_form_opacity ); ?>;
	<?php elseif ( isset( $loginpress_login_form_shadow ) && '0' == $loginpress_login_form_shadow ) : ?>
	box-shadow: none;
	-webkit-box-shadow: 0 !important;
	<?php endif; ?>
}
html[dir="rtl"] .login form .input, html[dir="rtl"] .login input[type="text"]{
	margin-right: 0;
}
body.login #loginpress_video-background-wrapper{
	<?php if( $loginpress_theme_tem == 'default6' ) : ?>
		position: absolute;
		top: 0;
		overflow: hidden;
		right: 0;
		width: calc(50% + 130px);
		height: 100%;
		z-index: 1;
	<?php endif; ?>
	<?php if( $loginpress_theme_tem == 'default8' ) : ?>
		position: absolute;
		top: 0;
		overflow: hidden;
		right: 0;
		width: 50%;
		height: 100%;
		z-index: 1;
	<?php endif; ?>
	<?php if( $loginpress_theme_tem == 'default10' ) : ?>
		position: absolute;
		top: 0;
		overflow: hidden;
		right: 0;
		width: 50%;
		height: 100%;
		z-index: 1;
	<?php endif; ?>
	<?php if( $loginpress_theme_tem == 'default17' ) : ?>
		position: absolute;
		top: 0;
		overflow: hidden;
		right: auto;
		width: 80%;
		height: 100%;
		z-index: 1;
		left: 0;
	<?php endif; ?>
}
body.login #loginpress_video-background{
	<?php if( $loginpress_theme_tem == 'default6' ) : ?>
		position: absolute !important;
	<?php endif; ?>
	<?php if( $loginpress_theme_tem == 'default8' ) : ?>
		position: absolute !important;
	<?php endif; ?>
	<?php if( $loginpress_theme_tem == 'default10' ) : ?>
		position: absolute !important;
	<?php endif; ?>
	<?php if( $loginpress_theme_tem == 'default17' ) : ?>
		min-width: inherit !important;
		position: absolute !important;
		min-height: inherit !important;
		width: 100% !important;
		height: 100% !important;
	<?php endif; ?>

	<?php if( $loginpress_bg_video_size ) : ?>
		object-fit: <?php echo $loginpress_bg_video_size ?>;
		<?php else : ?>
		object-fit: cover;
	<?php endif; ?>

	<?php if( $loginpress_bg_video_position ) : ?>
		object-position: <?php echo $loginpress_bg_video_position ?>;
	<?php endif; ?>
}
body.login:after{
  <?php if ( $loginpress_theme_tem == 'default8' && ! empty( $loginpress_background_img ) && $loginpress_display_bg ) : ?>
	background-image: url(<?php echo $loginpress_background_img; ?>);
  <?php elseif ( $loginpress_theme_tem == 'default8' &&  isset( $loginpress_display_bg ) && ! $loginpress_display_bg ) : ?>
	background-image: url();
	<?php endif; ?>

  <?php if( $loginpress_theme_tem == 'default8' ) : ?>
    <?php if ( ! empty( $loginpress_background_color ) ) : ?>
  	background-color: <?php echo $loginpress_background_color; ?>;
  	<?php endif; ?>
    <?php if ( ! empty( $loginpress_background_repeat ) ) : ?>
  	background-repeat: <?php echo $loginpress_background_repeat; ?>;
  	<?php endif; ?>
  	<?php if ( ! empty( $loginpress_background_postion ) ) : ?>
  	background-position: <?php echo $loginpress_background_postion; ?>;
  	<?php endif; ?>
  	<?php if ( ! empty( $loginpress_background_image_size ) ) : ?>
  	background-size: <?php echo $loginpress_background_image_size; ?>;
    <?php endif; ?>
	<?php endif; ?>
}
body.login {

  <?php if ( in_array( $loginpress_theme_tem, array( 'default6', 'default8', 'default10', 'default17' ) ) && ! empty( $loginpress_background_img ) && $loginpress_display_bg ) : ?>
	background-image: url();
  <?php elseif ( in_array( $loginpress_theme_tem, array( 'default6', 'default8', 'default10', 'default17' ) ) &&  isset( $loginpress_display_bg ) && ! $loginpress_display_bg ) : ?>
	background-image: url();
	<?php endif; ?>

	<?php if ( ! in_array( $loginpress_theme_tem, array( 'default6', 'default8', 'default10', 'default17' ) )  && ! empty( $loginpress_background_img ) && $loginpress_display_bg ) : ?>
	background-image: url(<?php echo $loginpress_background_img; ?>);
  <?php elseif ( ! in_array( $loginpress_theme_tem, array( 'default6', 'default8', 'default10', 'default17' ) ) && isset( $loginpress_display_bg ) && ! $loginpress_display_bg ) : ?>
	background-image: url();
	<?php endif; ?>

	<?php if ( ! empty( $loginpress_background_color ) ) : ?>
	background-color: <?php echo $loginpress_background_color; ?>;
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_background_repeat ) ) : ?>
	background-repeat: <?php echo $loginpress_background_repeat; ?>;
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_background_postion ) ) : ?>
	background-position: <?php echo $loginpress_background_postion; ?>;
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_background_image_size ) ) : ?>
	background-size: <?php echo $loginpress_background_image_size; ?>;
	<?php endif; ?>
  position: relative;
}
.login h1{
	<?php if ( ! empty( $loginpress_logo_display ) && true == $loginpress_logo_display ) : ?>
	display: none <?php echo loginpress_important(); ?>;
	<?php endif; ?>
}
.interim-login.login h1 a{
  <?php if ( ! empty( $loginpress_logo_width ) ) : ?>
  width: <?php echo $loginpress_logo_width; ?>;
  <?php else : ?>
	width: 84px;
	<?php endif; ?>
}

.login h1 a {
	<?php if ( ! empty( $loginpress_logo_img ) ) : ?>
	background-image: url( <?php echo $loginpress_logo_img; ?> ) <?php echo loginpress_important(); ?>;
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_logo_width ) ) : ?>
	width: <?php echo $loginpress_logo_width . loginpress_important(); ?>;
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_logo_height ) ) : ?>
	height: <?php echo $loginpress_logo_height . loginpress_important(); ?>;
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_logo_width ) || ! empty( $loginpress_logo_height ) ) : ?>
	background-size: contain <?php echo loginpress_important(); ?>;
	<?php else: ?>
		background-size: contain;
	<?php endif; ?>

	<?php if ( ! empty( $loginpress_logo_padding ) ) : ?>
	margin-bottom: <?php echo $loginpress_logo_padding . loginpress_important(); ?>;
	<?php endif; ?>

}

.wp-core-ui #login  .button-primary{
	<?php if ( ! empty( $loginpress_btn_bg ) ) : ?>
	background: <?php echo $loginpress_btn_bg; ?>;
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_btn_border ) ) : ?>
	border-color: <?php echo $loginpress_btn_border; ?>;
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_btn_shadow ) ) : ?>
	box-shadow: 0px 1px 0px <?php echo $loginpress_btn_shadow; ?> inset, 0px 1px 0px rgba(0, 0, 0, 0.15);
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_btn_color ) ) : ?>
	color: <?php echo $loginpress_btn_color; ?>;
	<?php endif; ?>
}

.wp-core-ui #login  .button-primary:hover{
	<?php if ( ! empty( $loginpress_btn_hover_bg ) ) : ?>
	background: <?php echo $loginpress_btn_hover_bg; ?>;
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_btn_hover_border ) ) : ?>
	border-color: <?php echo $loginpress_btn_hover_border; ?>;
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_btn_hover_color ) ) : ?>
	color: <?php echo $loginpress_btn_hover_color; ?>;
	<?php endif; ?>
}
.wp-core-ui #login .button-primary, .wp-core-ui #login .two-factor-email-resend .button{

	box-shadow: <?php echo loginpress_box_shadow( $loginpress_login_button_shadow, $loginpress_login_button_shadow_opacity ); ?>
  /* box-shadow: none; */
	height: auto;
	line-height: 20px;
	padding: 13px;
	<?php if ( ! empty( $loginpress_login_button_top ) ) : ?>
	padding-top: <?php echo $loginpress_login_button_top . 'px;' ?>;
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_login_button_bottom ) ) : ?>
	padding-bottom: <?php echo $loginpress_login_button_bottom . 'px;' ?>;
	<?php endif; ?>
	float: none;
	width: 100%;
}
#loginform {

	<?php if ( ! empty( $loginpress_form_display_bg ) && true == $loginpress_form_display_bg ) : ?>
	background: transparent;
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_form_background_img ) ) : ?>
	background-image: url(<?php echo $loginpress_form_background_img; ?>);
	<?php endif; ?>
	<?php if ( true != $loginpress_form_display_bg && ! empty( $loginpress_form_background_clr ) ) : ?>
	background-color: <?php echo $loginpress_form_background_clr; ?>;
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_form_height ) ) : ?>
	min-height: <?php echo $loginpress_form_height; ?>;
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_form_padding ) ) : ?>
	padding: <?php echo $loginpress_form_padding; ?>;
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_form_border ) ) : ?>
	border: <?php echo $loginpress_form_border; ?>;
	<?php endif; ?>
}

#loginform input[type="text"], #loginform input[type="password"]{
<?php if ( ! empty( $loginpress_login_textfield_radius ) ) : ?>
border-radius: <?php echo $loginpress_login_textfield_radius . 'px;'; ?>;
<?php endif; ?>

box-shadow: <?php echo loginpress_box_shadow( $loginpress_textfield_shadow, $loginpress_textfield_shadow_opacity, '0', $loginpress_inset ); ?>
}

#registerform input[type="text"], #registerform input[type="password"], #registerform input[type="number"], #registerform input[type="email"] {
	<?php if ( ! empty( $loginpress_login_textfield_radius ) ) : ?>
	border-radius: <?php echo $loginpress_login_textfield_radius . 'px;'; ?>;
	<?php endif; ?>
	box-shadow: <?php echo loginpress_box_shadow( $loginpress_textfield_shadow, $loginpress_textfield_shadow_opacity, '0', $loginpress_inset ); ?>
}

#lostpasswordform input[type="text"]{
	<?php if ( ! empty( $loginpress_login_textfield_radius ) ) : ?>
	border-radius: <?php echo $loginpress_login_textfield_radius . 'px;'; ?>;
	<?php endif; ?>
	box-shadow: <?php echo loginpress_box_shadow( $loginpress_textfield_shadow, $loginpress_textfield_shadow_opacity, '0', $loginpress_inset ); ?>
}

#login {
	<?php if ( ! empty( $loginpress_form_width ) ) : ?>
	max-width: <?php echo loginpress_check_px( $loginpress_form_width ) . loginpress_important(); ?>;
	<?php else : ?>
	<?php endif; ?>
}

.login form .forgetmenot label {
	<?php if ( ! empty( $loginpress_form_remeber_label ) ) : ?>
	color: <?php echo $loginpress_form_remeber_label . loginpress_important(); ?>;
	<?php endif; ?>
}

.login label {
	<?php if( !empty( $loginpress_form_label_font_size ) && 'default2' != $loginpress_preset ) : ?>
	font-size: <?php echo $loginpress_form_label_font_size . 'px;'; ?>
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_form_field_label ) ) : ?>
	color: <?php echo $loginpress_form_field_label; ?>;
	<?php endif; ?>
}

.login form .input, .login input[type="text"] {
	<?php if ( ! empty( $loginpress_form_field_width ) ) : ?>
	width: <?php echo loginpress_check_percentage($loginpress_form_field_width); ?>;
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_form_field_margin ) ) : ?>
	margin: <?php echo $loginpress_form_field_margin; ?>;
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_form_field_bg ) ) : ?>
	background: <?php echo $loginpress_form_field_bg; ?>;
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_form_field_color ) ) : ?>
	color: <?php echo $loginpress_form_field_color; ?>;
	<?php endif; ?>
}

#lostpasswordform {
	<?php if ( ! empty( $loginpress_forget_form_bg_img ) ) : ?>
	background-image: url(<?php echo $loginpress_forget_form_bg_img; ?>);
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_forget_form_bg_clr ) ) : ?>
	background-color: <?php echo $loginpress_forget_form_bg_clr; ?>;
	<?php endif; ?>
  <?php if ( ! empty( $loginpress_form_padding ) ) : ?>
	padding: <?php echo $loginpress_form_padding; ?>;
	<?php endif; ?>
	<?php if ( true != $loginpress_form_display_bg && ! empty( $loginpress_form_background_clr ) ) : ?>
	background-color: <?php echo $loginpress_form_background_clr; ?>;
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_form_display_bg ) && true == $loginpress_form_display_bg ) : ?>
	background: transparent;
	<?php endif; ?>
}

#registerform {
  <?php if ( ! empty( $loginpress_form_padding ) ) : ?>
	padding: <?php echo $loginpress_form_padding; ?>;
	<?php endif; ?>
	<?php if ( true != $loginpress_form_display_bg && ! empty( $loginpress_form_background_clr ) ) : ?>
	background-color: <?php echo $loginpress_form_background_clr; ?>;
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_form_display_bg ) && true == $loginpress_form_display_bg ) : ?>
	background: transparent;
	<?php endif; ?>
}

#wfls-prompt-overlay {
	<?php if ( true != $loginpress_form_display_bg && ! empty( $loginpress_form_background_clr ) ) : ?>
	background-color: <?php echo $loginpress_form_background_clr; ?>;
	<?php endif; ?>
}

.login .custom-message {

  <?php if ( ! empty( $loginpress_welcome_bg_border ) ) : ?>
  border: <?php echo $loginpress_welcome_bg_border; ?>;
  <?php else : ?>
  border-left: 4px solid #00a0d2;
  <?php endif; ?>

	<?php if ( ! empty( $loginpress_welcome_bg_color ) ) : ?>
	background-color: <?php echo $loginpress_welcome_bg_color; ?>;
  <?php else : ?>
  background-color: #fff;
	<?php endif; ?>

  padding: 12px;
  margin-left: 0;
  margin-bottom: 20px;
  -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
  box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}

.login #nav {
	<?php if ( ! empty( $loginpress_footer_bg_color ) ) : ?>
	background-color: <?php echo $loginpress_footer_bg_color; ?>;
	<?php endif; ?>
	<?php if ( isset( $loginpress_footer_display ) && '1' != $loginpress_footer_display) : ?>
		display: none;
	<?php endif; ?>
}

.login #nav a, .login #nav{
	<?php if ( ! empty( $loginpress_footer_decoration ) ) : ?>
	text-decoration: <?php echo $loginpress_footer_decoration; ?>;
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_footer_text_color ) ) : ?>
	color: <?php echo $loginpress_footer_text_color; ?>;
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_footer_font_size ) ) : ?>
	font-size: <?php echo $loginpress_footer_font_size . ';'; ?>;
	<?php endif; ?>

}

.login form .forgetmenot label{
	<?php if ( ! empty( $loginpress_remember_me_font_size ) ) : ?>
	font-size: <?php echo $loginpress_remember_me_font_size . 'px;'; ?>;
	<?php endif; ?>
}

.login input[type="submit"]{
	<?php if ( ! empty( $loginpress_login_button_text_size ) ) : ?>
	font-size: <?php echo $loginpress_login_button_text_size . 'px !important;'; ?>;
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_login_button_width ) ) : ?>
	width: <?php echo $loginpress_login_button_width . '%;'; ?>;
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_login_button_radius ) ) : ?>
	border-radius: <?php echo $loginpress_login_button_radius . 'px;'; ?>;
	<?php endif; ?>
}

.login #nav a:hover{
	<?php if ( ! empty( $loginpress_footer_text_hover ) ) : ?>
	color: <?php echo $loginpress_footer_text_hover; ?>;
	<?php endif; ?>
}

.login #backtoblog{
	<?php if ( ! empty( $loginpress_back_bg_color ) ) : ?>
	background-color: <?php echo $loginpress_back_bg_color; ?>;
	<?php endif; ?>
}

.login .copyRight{
	<?php if ( ! empty( $copyright_background_color ) ) : ?>
	background-color: <?php echo $copyright_background_color; ?>;
	<?php endif; ?>
}
/* .loginpress-show-love, .loginpress-show-love a{
	<?php //if ( ! empty( $show_some_love_text_color ) ) : ?>
	color: <?php //echo $show_some_love_text_color; ?>;
	<?php //endif; ?>
} */

.login .copyRight{
	<?php if ( ! empty( $copyright_text_color ) ) : ?>
	color: <?php echo $copyright_text_color; ?>;
	<?php endif; ?>
}
.login #backtoblog a{
	<?php if ( ! empty( $loginpress_back_decoration ) ) : ?>
	text-decoration: <?php echo $loginpress_back_decoration; ?>;
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_back_text_color ) ) : ?>
	color: <?php echo $loginpress_back_text_color; ?>;
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_back_font_size ) ) : ?>
	font-size: <?php echo $loginpress_back_font_size; ?>;
	<?php endif; ?>
}
.login #backtoblog{
	<?php if ( isset( $loginpress_back_display ) && '1' != $loginpress_back_display ) : ?>
	display: none;
	<?php endif; ?>

}
.login #backtoblog a:hover{
	<?php if ( ! empty( $loginpress_back_text_hover ) ) : ?>
	color: <?php echo $loginpress_back_text_hover; ?>;
	<?php endif; ?>
}

.loginHead {
	<?php if ( ! empty( $loginpress_header_bg_color ) ) : ?>
	background: <?php echo $loginpress_header_bg_color; ?>;
	<?php endif; ?>
}

.loginHead p a {
	<?php if ( ! empty( $loginpress_header_text_color ) ) : ?>
	color: <?php echo $loginpress_header_text_color; ?>;
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_header_font_size ) ) : ?>
	font-size: <?php echo $loginpress_header_font_size; ?>;
	<?php endif; ?>
}

.loginHead p a:hover {
	<?php if ( ! empty( $loginpress_header_text_hover ) ) : ?>
	color: <?php echo $loginpress_header_text_hover; ?>;
	<?php endif; ?>
}

.loginFooter p a {
	margin: 0 5px;
	<?php if ( ! empty( $loginpress_footer_link_color ) ) : ?>
	color: <?php echo $loginpress_footer_link_color; ?>;
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_footer_links_font_size ) ) : ?>
	font-size: <?php echo $loginpress_footer_links_font_size; ?>;
	<?php endif; ?>
}

.loginFooter p a:hover {
	<?php if ( ! empty( $loginpress_footer_link_hover ) ) : ?>
	color: <?php echo $loginpress_footer_link_hover; ?>;
	<?php endif; ?>
	<?php if ( ! empty( $loginpress_footer_links_hover_size ) ) : ?>
	font-size: <?php echo $loginpress_footer_links_hover_size; ?>;
	<?php endif; ?>
}

.loginInner {
	<?php if ( ! empty( $loginpress_footer_link_bg_clr ) ) : ?>
	background: <?php echo $loginpress_footer_link_bg_clr; ?>;
	<?php endif; ?>
}

<?php if ( ! empty( $loginpress_custom_css ) ) : ?>
<?php echo $loginpress_custom_css; ?>
<?php endif; ?>

.wp-core-ui .button-primary{
text-shadow: none;
}

/*input:-webkit-autofill{
  transition: all 100000s ease-in-out 0s !important;
  transition-property: background-color, color !important;
}*/
.copyRight{
	padding: 12px 170px;
}
.loginpress-show-love{
  float: right;
  font-style: italic;
  padding-right: 20px;
  padding-bottom: 10px;
  position: absolute;
  bottom: 3px;
  right: 0;
  z-index: 10;
}
.loginpress-show-love a{
  text-decoration: none;
}
.love-postion{
	left: 0;
	padding-left: 20px;
}
.header-cell{
	/* display: table-cell; */
	height: 100px;
}
.loginHeaderMenu{
	text-align: center;
	position: relative;
	z-index: 10;
	list-style: none;
	background: #333;

}
.loginHeaderMenu>ul>li{
	display: inline-block;
	vertical-align: top;
	position: relative;
	list-style: none;
}
.loginHeaderMenu>ul>li>a{
	color: #fff;
	text-transform: uppercase;
	text-decoration: none;
	font-size: 16px;
	padding: 17px 20px;
	display: inline-block;
}
.loginHeaderMenu>ul>li:hover>a{
	background: #4CAF50;
	color: #fff;
}
.loginHeaderMenu>ul>li>ul{
	position: absolute;
	width: 200px;
	padding: 0;
	top: 100%;
	left: 0;
	background: #fff;
	list-style: none;
	text-align: left;
	border-radius: 0 0 5px 5px;
	-webkit-box-shadow: 0px 5px 10px -1px rgba(0,0,0,0.31);
	-moz-box-shadow: 0px 5px 10px -1px rgba(0,0,0,0.31);
	box-shadow: 0px 5px 10px -1px rgba(0,0,0,0.31);
	overflow: hidden;
	opacity: 0;
	visibility: hidden;
}
.loginHeaderMenu>ul>li:hover>ul{
	opacity: 1;
	visibility: visible;
}
.loginHeaderMenu>ul>li>ul>li{
	font-size: 15px;
	color: #333;
}
.loginHeaderMenu>ul>li>ul>li>a{
	color: #333;
	padding: 10px;
	display: block;
	text-decoration: none;
}
.loginHeaderMenu>ul>li>ul>li>a:hover {
	background: rgba(51, 51, 51, 0.35);
	color: #fff;
}
.loginHeaderMenu>ul {
    flex-wrap: wrap;
    display: flex;
    justify-content: center;
}
.loginFooterMenu{
	text-align: center;
	background-color: rgba(0,0,0,.7);
}
.loginFooterMenu>ul{
	display: inline-flex;
}

.loginFooterMenu>ul>li{
	display: inline-block;
	padding: 18px;
}
.loginFooterMenu>ul>li:focus{
	outline: none;
	border: 0;
}
.loginFooterMenu>ul>li>a:focus{
	outline: none;
	border: 0;
}
.loginFooterMenu>ul>li>a{
	color: #fff;
	text-transform: uppercase;
	text-decoration: none;
	font-size: 14px;
}
.loginFooterMenu>ul {
    flex-wrap: wrap;
    display: flex;
    justify-content: center;
}
.loginpress-caps-lock{
	background: rgba(51, 56, 61, 0.9);
    color: #fff;
    display: none;
    font-size: 14px;
    width: 120px;
	padding: 5px 10px;
	line-height: 20px;
    position: absolute;
    left: calc(100% + 10px);
    top: 50%;
    transform: translateY(-50%);
    border-radius: 5px;
    -webkit-transition: all 0.3s ease-in-out;
    transition: all 0.3s ease-in-out;
    text-align: center;
    -webkit-box-shadow: 0 0 9px 0px rgba(0, 0, 0, 0.20);
    box-shadow: 0 0 9px 0px rgba(0, 0, 0, 0.20);
    margin-left: 5px;
    font-weight: normal;
	margin: 0;
	display: none;
}
.loginpress-caps-lock:before{
	content: '';
    width: 0;
    height: 0;
    border-style: solid;
    border-width: 5px 5px 5px 0;
    border-color: transparent rgba(51, 56, 61, 0.9) transparent transparent;
    position: absolute;
    top: 50%;
    right: 100%;
    margin-left: 0;
    margin-top: -5px;
    -webkit-transition: all 0.3s ease-in-out;
    transition: all 0.3s ease-in-out;
    z-index: 1;
}
.login form{
    overflow: visible;
		border: none;
}
#loginform .user-pass-fields input{
    margin-bottom: 0;
}
#loginform .user-pass-fields {
	margin-bottom: 18px;
	position: relative;
}
#login form p.submit{
	position: relative;
  clear: both;
}
input[type=checkbox]:checked::before{
	margin: -.35rem 0 0 -.375rem;
}
 /* LoginPress input field since 1.1.20 */
 /* .loginpress-input-wrap{
	 position: relative;
 }
.loginpres-input-field {
	transition: 0.4s;
}
.loginpres-input-field ~ .focus-border:before, .loginpres-input-field ~ .focus-border:after{
	content: "";
	position: absolute;
	top: 0;
	left: 50%;
	width: 0;
	height: 2px;
	background-color: #3399FF;
	transition: 0.4s;
	z-index: 999;
}
.loginpres-input-field ~ .focus-border:after{
	top: auto; bottom: 0;
}
.loginpres-input-field ~ .focus-border i:before, .loginpres-input-field ~ .focus-border i:after{
	content: "";
	position: absolute;
	top: 50%;
	left: 0;
	width: 2px;
	height: 0;
	background-color: #3399FF;
	transition: 0.6s;
}
.loginpres-input-field ~ .focus-border i:after{
	left: auto; right: 0;
}
.loginpres-input-field:focus ~ .focus-border:before, .loginpres-input-field:focus ~ .focus-border:after{
	left: 0;
	width: 100%;
	transition: 0.4s;
}
.loginpres-input-field:focus ~ .focus-border i:before, .loginpres-input-field:focus ~ .focus-border i:after{
	top: 0;
	height: 100%;
	transition: 0.6s;
} */
/* ! LoginPress input field since 1.1.20 */
@media screen and (max-width: 1239px) and (min-width: 768px){
	body.login #loginpress_video-background-wrapper{
		<?php if( $loginpress_theme_tem == 'default10' ) : ?>
		    width: calc(58vw - 90px);
		<?php endif; ?>
	}
}
@media screen and (max-width: 767px) {
		.login h1 a {
				max-width: 100%;
				background-size: contain !important;
		}
    .copyRight{
    	padding: 12px;
	}
	.loginpress-caps-lock{
		left: auto;
		right: 0;
		top: 149%;
	}
	.loginpress-caps-lock:before{
		content: '';
		width: 0;
		height: 0;
		border-style: solid;
		border-width: 0 5px 5px 5px;
		border-color: transparent transparent rgba(51, 56, 61, 0.9) transparent;
		position: absolute;
		top: 0px;
		left: 5px;
		right: auto;
	}
	.loginpress-show-love{
		display: none !important;
	}
body.login #loginpress_video-background-wrapper{
	<?php if( $loginpress_theme_tem == 'default6' ) : ?>
		z-index: -1;
		width: 100%;
	<?php endif; ?>
	<?php if( $loginpress_theme_tem == 'default10' ) : ?>
		z-index: -1;
		width: 100%;
	<?php endif; ?>
	<?php if( $loginpress_theme_tem == 'default17' ) : ?>
		z-index: 0;
		width: 100%;
	<?php endif; ?>
	<?php if( $loginpress_theme_tem == 'default8' ) : ?>
		z-index: 1;
		width: 100%;
	<?php endif; ?>
}
}
@media screen and (max-height: 700px) {
	.loginpress-show-love{
		display: none !important;
	}
}
/* The only rule that matters */
#loginpress_video-background {
/*  making the video fullscreen  */
  position: fixed !important ;
  right: 0 !important ;
  bottom: 0 !important ;
  width: 100% !important ;
  height: 100% !important ;
  z-index: -100 !important ;
}
body.login #login.login_tranparent,body.login  #login.login_tranparent #loginform{
	background: none !important;
}
body.login{
	display: flex;
	flex-direction: column;
}
body.login label[for="authcode"]:after{
	display: none;
}
body.login label[for="authcode"]+input{
padding-left: 15px;
}
</style>

<?php // $content = ob_get_clean(); ?>
<?php if ( isset( $loginpress_display_bg_video ) && $loginpress_display_bg_video && ! empty( $loginpress_bg_video ) ) : ?>
<?php if ( ( $loginpress_theme_tem == 'default6' || $loginpress_theme_tem == 'default10' || $loginpress_theme_tem == 'default17' ) ) : ?>
	<script>
	document.addEventListener( 'DOMContentLoaded', function(){
			// document.body.innerHTML="<video autoplay loop id=\"loginpress_video-background\" muted playsinline>\n" + "<source src=\"<?php // echo $loginpress_bg_video;?>\">\n" + "</video>\n"+document.body.innerHTML;
			// '"<video autoplay loop id=\"loginpress_video-background\" muted playsinline>\n" + "<source src=\"<?php // echo $loginpress_bg_video;?>\">\n" + "</video>\n"'.
			// document.getElementById("login").appendChild("<video autoplay loop id=\"loginpress_video-background\" muted playsinline>\n" + "<source src=\"<?php  // echo $loginpress_bg_video;?>\">\n" + "</video>\n");
			// (function($){
			// 	$('<div id="loginpress_video-background-wrapper"><video autoplay loop id="loginpress_video-background" <?php echo $loginpress_video_voice; ?> playsinline><source src="<?php //echo $loginpress_bg_video;?>"></video></div>').appendTo($('#login'));
			// }(jQuery));
			var el = document.getElementById('login');
			var elChild = document.createElement('div');
			elChild.setAttribute( 'id', 'loginpress_video-background-wrapper' );
			elChild.innerHTML = '<video autoplay loop id=\"loginpress_video-background\" <?php echo $loginpress_video_voice; ?> playsinline>\n" + "<source src=\"<?php echo $loginpress_bg_video; ?>\">\n" + "</video>';

			// Prepend it
			el.appendChild(elChild);
		}, false );
	</script>
<?php else: ?>
	<script>
	<?php if ( $loginpress_theme_tem == 'default17' ) : ?>
	document.addEventListener( 'DOMContentLoaded', function(){
			var el = document.getElementsByClassName('login')[0];
			var elChild = document.createElement('div');
			elChild.setAttribute( 'id', 'loginpress_video-background-wrapper' );
			elChild.innerHTML = '<video autoplay loop id=\"loginpress_video-background\" <?php echo $loginpress_video_voice; ?> playsinline>\n" + "<source src=\"<?php echo $loginpress_bg_video; ?>\">\n" + "</video>';

			// Prepend it
			el.appendChild(elChild);
		}, false );
	<?php endif; ?>
	<?php if ( $loginpress_theme_tem == 'default8' ) : ?>
	document.addEventListener( 'DOMContentLoaded', function(){
			var el = document.getElementsByClassName('login')[0];
			var elChild = document.createElement('div');
			elChild.setAttribute( 'id', 'loginpress_video-background-wrapper' );
			elChild.innerHTML = '<video autoplay loop id=\"loginpress_video-background\" <?php echo $loginpress_video_voice; ?> playsinline>\n" + "<source src=\"<?php echo $loginpress_bg_video; ?>\">\n" + "</video>';

			// Prepend it
			el.appendChild(elChild);
		}, false );
	<?php endif; ?>
	<?php if ( $loginpress_theme_tem != 'default17' && $loginpress_theme_tem != 'default8' ) : ?>
	document.addEventListener( 'DOMContentLoaded', function(){
			var el = document.getElementsByClassName('login')[0];
			var elChild = document.createElement('div');
			elChild.setAttribute( 'id', 'loginpress_video-background-wrapper' );
			elChild.innerHTML = '<video autoplay loop id=\"loginpress_video-background\" <?php echo $loginpress_video_voice; ?> playsinline>\n" + "<source src=\"<?php echo $loginpress_bg_video; ?>\">\n" + "</video>';

			// Prepend it
			el.appendChild(elChild);
		}, false );
	<?php endif; ?>
	</script>
<?php endif; ?>
<?php endif; ?>
