<?php
require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/lib/seed_cspv5_lessc.inc.php';

$settings = json_decode( json_encode( $settings ), false );
$settings = stripslashes_deep( $settings );
//var_dump($settings);
$title = $page->name;
if ( ! empty( $settings->seo_title ) ) {
	$title = $settings->seo_title;
}

// Enable wp_head if GF is the selected list
$enable_wp_head_footer_list = apply_filters( 'seed_cspv5_legacy_enable_wp_head_footer_list', array() );
if ( in_array( $settings->emaillist, $enable_wp_head_footer_list ) ) {
	$settings->enable_wp_head_footer = '1';
}


$scheme = 'http';
if ( $_SERVER['SERVER_PORT'] == '443' ) {
	$scheme = 'https';
}
if ( ! empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) {
	$scheme = 'https';
}
$ogurl = "$scheme://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

// YouTube video ID
function seed_cspv5_legacy_youtube_id_from_url( $url ) {
	$pattern =
		'%^# Match any youtube URL
        (?:https?://)?  # Optional scheme. Either http or https
        (?:www\.)?      # Optional www subdomain
        (?:             # Group host alternatives
          youtu\.be/    # Either youtu.be,
        | youtube\.com  # or youtube.com
          (?:           # Group path alternatives
            /embed/     # Either /embed/
          | /v/         # or /v/
          | /watch\?v=  # or /watch\?v=
          )             # End path alternatives.
        )               # End host alternatives.
        ([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
        $%x';
	$result  = preg_match( $pattern, $url, $matches );
	if ( false !== $result ) {
		if ( isset( $matches[1] ) ) {
			return $matches[1];
		}
	}
	return false;
}
$video_id = seed_cspv5_legacy_youtube_id_from_url( $settings->bg_video_url );


$url = '';



?>
<!DOCTYPE html>
<html class="cspio">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title><?php echo esc_html( $title ); ?></title>

	<?php if ( ! empty( $settings->favicon ) ) : ?>
	<link href="<?php echo $settings->favicon; ?>" rel="shortcut icon" type="image/x-icon" />
	<?php endif; ?>
	<meta name="generator" content="seedprod.com 5.0.0" />
	<meta name="description" content="<?php echo esc_attr( $settings->seo_description ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<!-- Open Graph -->
	<meta property="og:url" content="<?php echo $ogurl; ?>" />
	<meta property="og:type" content="website" />
	<meta property="og:title" content="<?php echo esc_attr( $title ); ?>" />
	<meta property="og:description" content="<?php echo esc_attr( $settings->seo_description ); ?>" />
	<?php if ( ! empty( $settings->facebook_thumbnail ) ) : ?>
	<meta property="og:image" content="<?php echo $settings->facebook_thumbnail; ?>" />
	<?php elseif ( ! empty( $settings->logo ) ) : ?>
	<meta property="og:image" content="<?php echo $settings->logo; ?>" />
	<?php endif; ?>

	<!-- Twitter Card -->
	<meta name="twitter:card" content="summary" />
	<meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>" />
	<meta name="twitter:description" content="<?php echo esc_attr( $settings->seo_description ); ?>" />
	<?php if ( ! empty( $settings->facebook_thumbnail ) ) : ?>
	<meta property="twitter:image" content="<?php echo $settings->facebook_thumbnail; ?>" />
	<?php elseif ( ! empty( $settings->logo ) ) : ?>
	<meta property="twitter:image" content="<?php echo $settings->logo; ?>" />
	<?php endif; ?>

	<!--[if lte IE 9]>
	  <script src="<?php echo SEEDPROD_PRO_PLUGIN_URL; ?>app/backwards/template/js/html5shiv.js"></script>
	  <script src="<?php echo SEEDPROD_PRO_PLUGIN_URL; ?>app/backwards/template/js/respond.min.js"></script>
	<![endif]-->	

	<!-- Font Awesome -->
	<?php if ( ! defined( 'SEED_CSPV5_REMOVE_FONTAWESOME' ) ) : ?>
	<link rel="stylesheet" href="<?php echo SEEDPROD_PRO_PLUGIN_URL; ?>app/backwards/template/css/all.css" >
	<?php endif; ?>

	<!-- Bootstrap and default Style -->
	<link href="<?php echo SEEDPROD_PRO_PLUGIN_URL; ?>app/backwards/template/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="<?php echo SEEDPROD_PRO_PLUGIN_URL; ?>app/backwards/template/style.css">


	<!-- Google Fonts -->
	<?php if ( ! empty( $settings->headline_font ) ) : ?>
		<?php if ( strpos( $settings->headline_font, ',' ) === false ) : ?>
	<link class="gf-headline" href='https://fonts.googleapis.com/css?family=<?php echo urlencode( str_replace( "'", '', stripslashes( $settings->headline_font ) ) ); ?>:<?php echo $settings->headline_weight; ?>&subset=<?php echo $settings->headline_subset; ?>' rel='stylesheet' type='text/css'>
	<?php endif; ?>
	<?php endif; ?>
	<?php if ( ! empty( $settings->text_font ) ) : ?>
		<?php if ( strpos( $settings->text_font, ',' ) === false ) : ?>
	<link class="gf-text" href='https://fonts.googleapis.com/css?family=<?php echo urlencode( str_replace( "'", '', stripslashes( $settings->text_font ) ) ); ?>:<?php echo $settings->text_weight; ?>&subset=<?php echo $settings->text_subset; ?>' rel='stylesheet' type='text/css'>
	<?php endif; ?>
	<?php endif; ?>
	<?php if ( ! empty( $settings->button_font ) ) : ?>
		<?php if ( strpos( $settings->button_font, ',' ) === false ) : ?>
	<link class="gf-button" href='https://fonts.googleapis.com/css?family=<?php echo urlencode( str_replace( "'", '', stripslashes( $settings->button_font ) ) ); ?>:<?php echo $settings->button_weight; ?>&subset=<?php echo $settings->button_subset; ?>' rel='stylesheet' type='text/css'>
	<?php endif; ?>
	<?php endif; ?>

	<!-- RTL -->
	<?php
	$force_rtl = false;

	if ( ! empty( $langs ) && ! empty( $lang_id ) ) {
		$language_name = $langs[ $lang_id ];
		$language_name = explode( '|', $language_name );
		if ( ! empty( $language_name[1] ) && $language_name[1] == 'rtl' ) {
			$force_rtl = true;
		}
		if ( ! empty( $language_name[1] ) && $language_name[1] == 'nortl' ) {
			$force_rtl = false;
		}
	}

	?>
	<?php if ( is_rtl() || $force_rtl ) { ?>
		<link rel="stylesheet" href="<?php echo SEEDPROD_PRO_PLUGIN_URL; ?>app/backwards/template/rtl.css">
	<?php } ?>


	<?php if ( ! empty( $settings->container_effect_animation ) ) : ?>
	<!-- Animate CSS -->
	<link rel="stylesheet" href="<?php echo SEEDPROD_PRO_PLUGIN_URL; ?>app/backwards/template/css/animate.min.css">
	<?php endif; ?>

	<style type="text/css">
	<?php if ( ! empty( $settings->enable_progressbar ) ) : ?>
		<?php
		$css = "
	@primaryColor: {$settings->button_color};
	@secondaryColor: darken(@primaryColor, 15%);
	#gradient {
		.vertical(@startColor: #555, @endColor: #333) {
		    background-color: @startColor;
		    background-image: -moz-linear-gradient(top, @startColor, @endColor); // FF 3.6+
		    background-image: -ms-linear-gradient(top, @startColor, @endColor); // IE10
		    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(@startColor), to(@endColor)); // Safari 4+, Chrome 2+
		    background-image: -webkit-linear-gradient(top, @startColor, @endColor); // Safari 5.1+, Chrome 10+
		    background-image: -o-linear-gradient(top, @startColor, @endColor); // Opera 11.10
		    background-image: linear-gradient(top, @startColor, @endColor); // The standard
		    background-repeat: repeat-x;
		    filter: e(%(\"progid:DXImageTransform.Microsoft.gradient(startColorstr='%d', endColorstr='%d', GradientType=0)\",@startColor,@endColor)); // IE9 and down
		}
	}
	.cspio .progress-bar{
		#gradient > .vertical(@primaryColor, @secondaryColor);
	}
	";
		try {
			//if($settings->progressbar_effect == 'basic'){
			if ( 1 == 1 ) {
				$less  = new seed_cspv5_legacy_lessc();
				$style = $less->parse( $css );
				echo $style;
			}
		} catch ( Exception $e ) {
			echo $e;
		}
		?>
	.progress-striped .progress-bar, .progress.active .progress-bar{
		background-color:<?php echo $settings->button_color; ?>
	}
	<?php endif; ?>

	<?php if ( ! empty( $settings->enable_countdown ) ) : ?>
		<?php
		$css = "
	@primaryColor: {$settings->button_color};
	@secondaryColor: darken(@primaryColor, 15%);
	#gradient {
		.vertical(@startColor: #555, @endColor: #333) {
		    background-color: @startColor;
		    background-image: -moz-linear-gradient(top, @startColor, @endColor); // FF 3.6+
		    background-image: -ms-linear-gradient(top, @startColor, @endColor); // IE10
		    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(@startColor), to(@endColor)); // Safari 4+, Chrome 2+
		    background-image: -webkit-linear-gradient(top, @startColor, @endColor); // Safari 5.1+, Chrome 10+
		    background-image: -o-linear-gradient(top, @startColor, @endColor); // Opera 11.10
		    background-image: linear-gradient(top, @startColor, @endColor); // The standard
		    background-repeat: repeat-x;
		    filter: e(%(\"progid:DXImageTransform.Microsoft.gradient(startColorstr='%d', endColorstr='%d', GradientType=0)\",@startColor,@endColor)); // IE9 and down
		}
	}
	.countdown_section{
		#gradient > .vertical(@primaryColor, @secondaryColor);
	}
	";
		try {
			if ( ! empty( $settings->enable_countdown ) ) {
				$less  = new seed_cspv5_legacy_lessc();
				$style = $less->parse( $css );
				echo $style;
			}
		} catch ( Exception $e ) {
			echo $e;
		}
		?>
	<?php endif; ?>


	/* Background Style */
	html{
		height:100%;
		<?php if ( ! empty( $settings->background_image ) ) : ?>
				background: <?php echo $settings->background_color; ?> url(<?php echo $settings->background_image; ?>) <?php echo $settings->background_repeat; ?> <?php echo $settings->background_position; ?> <?php echo $settings->background_attachment; ?>;
				<?php if ( ! empty( $settings->background_size ) ) : ?>
					-webkit-background-size: <?php echo $settings->background_size; ?>;
					-moz-background-size: <?php echo $settings->background_size; ?>;
					-o-background-size: <?php echo $settings->background_size; ?>;
					background-size: <?php echo $settings->background_size; ?>;
				<?php endif; ?>
		<?php else : ?>
			background: <?php echo $settings->background_color; ?>;
		<?php endif; ?>
	}

	<?php if ( ! empty( $settings->enable_background_overlay ) && ! empty( $settings->background_overlay ) ) : ?>
	#cspio-page{
		background-color: <?php echo $settings->background_overlay; ?>;
	}
	<?php endif; ?>

	<?php
	if ( ! empty( $settings->form_width ) && $settings->form_width < 100 ) :
		?>
	#cspio-field-wrapper{
		width: <?php echo $settings->form_width; ?>%;
	}
	<?php endif; ?>



	.flexbox #cspio-page{
	<?php if ( $settings->container_position == '1' ) : ?>
		-webkit-align-items: center;
		-webkit-justify-content: center;
		align-items: center;
		justify-content: center;
	<?php endif; ?>
	<?php if ( $settings->container_position == '2' ) : ?>
		-webkit-align-items: flex-start;
		-webkit-justify-content: center;
		align-items: flex-start;
		justify-content: center;
	<?php endif; ?>
	<?php if ( $settings->container_position == '3' ) : ?>
		-webkit-align-items: flex-end;
		-webkit-justify-content: center;
		align-items: flex-end;
		justify-content: center;
	<?php endif; ?>
	<?php if ( $settings->container_position == '4' ) : ?>
		-webkit-align-items: center;
		-webkit-justify-content: flex-start;
		align-items: center;
		justify-content: flex-start;
	<?php endif; ?>
	<?php if ( $settings->container_position == '5' ) : ?>
		-webkit-align-items: flex-start;
		-webkit-justify-content: flex-start;
		align-items: flex-start;
		justify-content: flex-start;
	<?php endif; ?>
	<?php if ( $settings->container_position == '6' ) : ?>
		-webkit-align-items: flex-end;
		-webkit-justify-content: flex-start;
		align-items: flex-end;
		justify-content: flex-start;
	<?php endif; ?>
	<?php if ( $settings->container_position == '7' ) : ?>
		-webkit-align-items: center;
		-webkit-justify-content: flex-end;	
		align-items: center;
		justify-content: flex-end;
	<?php endif; ?>
	<?php if ( $settings->container_position == '8' ) : ?>
		-webkit-align-items: flex-start;
		-webkit-justify-content: flex-end;
		align-items: flex-start;
		justify-content: flex-end;
	<?php endif; ?>
	<?php if ( $settings->container_position == '9' ) : ?>
		-webkit-align-items: flex-end;
		-webkit-justify-content: flex-end;
		align-items: flex-end;
		justify-content: flex-end;
	<?php endif; ?>
	}

	/* IE 11 Flexbox hack */
	@media screen and (-ms-high-contrast: active), (-ms-high-contrast: none) {
		.flexbox #cspio-page{
			flex-shrink: 0;
			overflow:visible;
		}
	}

	.cspio body{
		background: transparent;
	}

	/* Text Styles */
	<?php if ( ! empty( $settings->text_font ) ) : ?>
		.cspio body, .cspio body p{
			font-family: <?php echo $settings->text_font; ?>;
			font-weight: <?php echo preg_replace( '/[a-zA-Z]/', '', $settings->text_weight ); ?>;
			font-style: <?php echo preg_replace( '/[0-9]/', '', $settings->text_weight ); ?>;
			font-size: <?php echo $settings->text_size; ?>px;
			line-height: <?php echo $settings->text_line_height; ?>em;
			<?php if ( ! empty( $settings->text_color ) ) : ?>
			color:<?php echo $settings->text_color; ?>;
			<?php endif; ?>
		 }

		::-webkit-input-placeholder {
			font-family:<?php echo $settings->text_font; ?>;
			font-weight: <?php echo preg_replace( '/[a-zA-Z]/', '', $settings->text_weight ); ?>;
			font-style: <?php echo preg_replace( '/[0-9]/', '', $settings->text_weight ); ?>;
		}
		::-moz-placeholder {
			font-family:<?php echo $settings->text_font; ?>;
			font-weight: <?php echo preg_replace( '/[a-zA-Z]/', '', $settings->text_weight ); ?>;
			font-style: <?php echo preg_replace( '/[0-9]/', '', $settings->text_weight ); ?>;
		} /* firefox 19+ */
		:-ms-input-placeholder {
			font-family:<?php echo $settings->text_font; ?>;
			font-weight: <?php echo preg_replace( '/[a-zA-Z]/', '', $settings->text_weight ); ?>;
			font-style: <?php echo preg_replace( '/[0-9]/', '', $settings->text_weight ); ?>;
		} /* ie */
		:-moz-placeholder {
			font-family:<?php echo $settings->text_font; ?>;
			font-weight: <?php echo preg_replace( '/[a-zA-Z]/', '', $settings->text_weight ); ?>;
			font-style: <?php echo preg_replace( '/[0-9]/', '', $settings->text_weight ); ?>;
		}

	<?php endif; ?>


	.cspio h1, .cspio h2, .cspio h3, .cspio h4, .cspio h5, .cspio h6{
	<?php if ( ! empty( $settings->headline_font ) ) : ?>
		font-family: <?php echo $settings->headline_font; ?>;
	<?php endif; ?>
		<?php if ( ! empty( $settings->headline_color ) ) : ?>
		color:<?php echo $settings->headline_color; ?>;
		<?php endif; ?>
	}
	#cspio-headline{
	<?php if ( ! empty( $settings->headline_font ) ) : ?>
		font-family: <?php echo $settings->headline_font; ?>;
		font-weight: <?php echo preg_replace( '/[a-zA-Z]/', '', $settings->headline_weight ); ?>;
		font-style: <?php echo preg_replace( '/[0-9]/', '', $settings->headline_weight ); ?>;
	<?php else : ?>
		font-family: <?php echo $settings->text_font; ?>;
		font-weight: <?php echo preg_replace( '/[a-zA-Z]/', '', $settings->text_weight ); ?>;
		font-style: <?php echo preg_replace( '/[0-9]/', '', $settings->text_weight ); ?>;
	<?php endif; ?>
		font-size: <?php echo $settings->headline_size; ?>px;
		color:<?php echo $settings->headline_color; ?>;
		line-height: <?php echo $settings->headline_line_height; ?>em;
	}

	<?php if ( ! empty( $settings->button_font ) ) : ?>
		.cspio button{
			font-family: <?php echo $settings->button_font; ?>;
			font-weight: <?php echo preg_replace( '/[a-zA-Z]/', '', $settings->button_weight ); ?>;
			font-style: <?php echo preg_replace( '/[0-9]/', '', $settings->button_weight ); ?>;
		}
	<?php else : ?>
		.cspio button{
			font-family: <?php echo $settings->text_font; ?>;
			font-weight: <?php echo preg_replace( '/[a-zA-Z]/', '', $settings->text_weight ); ?>;
			font-style: <?php echo preg_replace( '/[0-9]/', '', $settings->text_weight ); ?>;
		}
	<?php endif; ?>

	/* Link Styles */
	<?php if ( ! empty( $settings->button_color ) ) : ?>
		.cspio a, .cspio a:visited, .cspio a:hover, .cspio a:active{
			color: <?php echo $settings->button_color; ?>;
		}

		.cspio-subscriber-count,.cspio-reveal{
			color: <?php echo $settings->button_color; ?>;
		}

		<?php
		$css = "
		   #cspio-socialprofiles a{
			color: {$settings->text_color};
		  }
		  .buttonBackground(@startColor, @endColor) {
		  .gradientBar(@startColor, @endColor);
		  *background-color: @endColor; /* Darken IE7 buttons by default so they stand out more given they won't have borders */
		  .reset-filter();
		  &:hover, &:active, &.active, &.disabled, &[disabled] {
		    background-color: @endColor;
		    *background-color: darken(@endColor, 5%);
		  }
		  // IE 7 + 8 can't handle box-shadow to show active, so we darken a bit ourselves
		  &:active,
		  &.active {
		    background-color: darken(@endColor, 10%) e(\"\9\");
		  }
		}
		.reset-filter() {
		  filter: e(%(\"progid:DXImageTransform.Microsoft.gradient(enabled = false)\"));
		}
		.gradientBar(@primaryColor, @secondaryColor) {
		  #gradient > .vertical(@primaryColor, @secondaryColor);
		  border-color: @secondaryColor @secondaryColor darken(@secondaryColor, 15%);
		  border-color: rgba(0,0,0,.1) rgba(0,0,0,.1) fadein(rgba(0,0,0,.1), 15%);
		}
		#gradient {
			.vertical(@startColor: #555, @endColor: #333) {
		    background-color: @startColor;
		    background-image: -moz-linear-gradient(top, @startColor, @endColor); // FF 3.6+
		    background-image: -ms-linear-gradient(top, @startColor, @endColor); // IE10
		    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(@startColor), to(@endColor)); // Safari 4+, Chrome 2+
		    background-image: -webkit-linear-gradient(top, @startColor, @endColor); // Safari 5.1+, Chrome 10+
		    background-image: -o-linear-gradient(top, @startColor, @endColor); // Opera 11.10
		    background-image: linear-gradient(top, @startColor, @endColor); // The standard
		    background-repeat: repeat-x;
		    filter: e(%(\"progid:DXImageTransform.Microsoft.gradient(startColorstr='%d', endColorstr='%d', GradientType=0)\",@startColor,@endColor)); // IE9 and down
		  }
		}
		.lightordark (@c) when (lightness(@c) >= 65%) {
			color: black;
			text-shadow: 0 -1px 0 rgba(256, 256, 256, 0.3);
		}
		.lightordark (@c) when (lightness(@c) < 65%) {
			color: white;
			text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.3);
		}
		@btnColor: {$settings->button_color};
		@btnDarkColor: darken(@btnColor, 15%);
		@btnDarkColor: darken(@btnColor, 15%);
		.cspio a.btn-primary, .cspio .btn-primary, .cspio .btn-primary:focus, .gform_button, #mc-embedded-subscribe, .mymail-wrapper .submit-button, .mailster-wrapper .submit-button,  input[type='button'].ninja-forms-field, .wpcf7-submit, .frm_button_submit {
		  .lightordark (@btnColor);
		  .buttonBackground(@btnColor, @btnDarkColor);
		  //border-color: darken(@btnColor, 0%);
		}
		@inputBackgroundColor: {$settings->form_color};
		.form-control,.progress{
			background-color:@inputBackgroundColor;
		}
		// Change form color input based on light or dark
		.form-control{
			.lightordark (@inputBackgroundColor);
		}
		
		#cspio-progressbar span,.countdown_section{
			.lightordark (@btnColor);
		}
		.cspio .btn-primary:hover,.cspio .btn-primary:active {
		  .lightordark (@btnColor);
		  border-color: darken(@btnColor, 10%);
		}
		.cspio input[type='text']{
			//border-color: @btnDarkColor @btnDarkColor darken(@btnDarkColor, 15%);
		}
		@hue: hue(@btnDarkColor);
		@saturation: saturation(@btnDarkColor);
		@lightness: lightness(@btnDarkColor);
		.cspio input[type='text']:focus {
			//border-color: hsla(@hue, @saturation, @lightness, 0.8);
			webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075),0 0 8px hsla(@hue, @saturation, @lightness, 0.6);
			-moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075),0 0 8px hsla(@hue, @saturation, @lightness, 0.6);
			box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075),0 0 8px hsla(@hue, @saturation, @lightness, 0.6);
		}
		";
		try {
			$less  = new seed_cspv5_legacy_lessc();
			$style = $less->parse( $css );
			echo $style;
		} catch ( Exception $e ) {
			echo $e;
		}
		?>
	<?php endif; ?>

	<?php if ( ! empty( $settings->contactform_color ) ) : ?>
		#cspio-contact-form a{
			color: <?php echo $settings->contactform_color; ?>;
		}
	<?php endif; ?>

	<?php if ( ! empty( $settings->socialprofile_color ) ) : ?>
		#cspio-socialprofiles a{
			color: <?php echo $settings->socialprofile_color; ?>;
		}
	<?php endif; ?>


	<?php if ( ! empty( $settings->footer_text_color ) ) : ?>
	#cspio-credit,#cspio-credit a{
		color: <?php echo $settings->footer_text_color; ?>
	}
	<?php endif; ?>

	<?php if ( ! empty( $settings->credit_position ) ) : ?>
		<?php if ( $settings->credit_position == 'center' ) : ?>
		.flexbox #cspio-page{
			flex-wrap: wrap;
		}

		#cspio-credit{
			width:100%;
			position:static;
		}

		#cspio-credit img{
			margin-left:auto;
			margin-right:auto;
		}
		 <?php endif; ?>
		<?php if ( $settings->credit_position == 'float_left' ) : ?>
		#cspio-credit {
			right:inherit;
			left: 20px;
		}
		 <?php endif; ?>
	<?php endif; ?>



	<?php

	//Container
	$enable_container = true;
	if ( ! empty( $enable_container ) ) {
		$dropshadow = 0;

		$border       = 0;
		$thickness    = 0;
		$border_color = 0;
		$border_style = 'solid';
		$border_color = '#cccccc';
		$glow         = 0;
		#$dropshadow = 0;
		/*      if(!empty($container_border)){
			$border = 1;
			$thickness = $container_border['border-top'];
			$border_style = $container_border['border-style'];
			if(empty($container_border['border-color'])){
				$border_color = ($link_color['color']);
			}else{
				$border_color = ($container_border['border-color']);
			}
		}*/
		$roundedcorners = 0;
		$radius         = 0;
		if ( ! empty( $settings->container_radius ) ) {
			$roundedcorners = 1;
			$radius         = ( $settings->container_radius ) . 'px';
		}
		$opacity = 1;
		if ( empty( $container_color['color'] ) ) {
			$container_color['color'] = '#000000';
		}
		if ( empty( $container_color['alpha'] ) ) {
			$container_color['alpha'] = '0';
		}
		$display = '';
		if ( ! empty( $settings->container_effect_animation ) ) {
			$display = 'display:none;';
		}
		$container_color['alpha'] = $container_color['alpha'] * 100;

		$css = "
    	@dropshadow: $dropshadow;
		.dropshadow() when (@dropshadow = 1){
			-moz-box-shadow:    0px 11px 15px -5px rgba(69, 69, 69, 0.8);
			-webkit-box-shadow: 0px 11px 15px -5px rgba(69, 69, 69, 0.8);
			box-shadow: 0px 11px 15px -5px rgba(69, 69, 69, 0.8);
  		}
  		@glow: $glow;
		.glow() when (@glow = 1){
			-moz-box-shadow:    0px 0px 50px 5px {$container_color['color']};
			-webkit-box-shadow: 0px 0px 50px 5px {$container_color['color']};
			box-shadow: 0px 0px 50px 15px {$container_color['color']};
  		}
  		@border: $border;
  		@thickness: $thickness;
		.border() when (@border = 1){
			border: @thickness $border_style $border_color;
  		}
  		@roundedcorners: $roundedcorners;
  		@radius: $radius;
		.roundedcorners() when (@roundedcorners = 1){
			-webkit-border-radius: $radius;
			border-radius: $radius;
			-moz-background-clip: padding; -webkit-background-clip: padding-box; background-clip: padding-box;
  		}
  		@opacity: $opacity;
		.opacity() when (@opacity = 1){
			background-color: fade({$container_color['color']},{$container_color['alpha']});
  		}
    	#cspio-content{
  			$display
  			max-width: {$settings->container_width}px;
    		background-color: {$settings->container_color};
    		//.dropshadow(); /* dropshadow */
    		//.glow(); /* glow */
    		//.border(); /* border */
    		.roundedcorners(); /* rounded corners */
    		//.opacity(); /* opacity */
		}";
		try {
			$less  = new seed_cspv5_legacy_lessc();
			$style = $less->parse( $css );
			echo $style;
		} catch ( Exception $e ) {
			echo $e;
		}
	}

	?>

	<?php if ( ! empty( $settings->container_transparent ) ) : ?>
	#cspio-content{
		background-color:transparent;
	}
	<?php endif; ?>

	<?php if ( ! empty( $settings->container_effect_animation ) ) : ?>
	#cspio-content{
		 /* display:none; */
	}
	<?php endif; ?>


	<?php if ( ! empty( $settings->container_flat ) ) : ?>
		<?php
		$css   = "
		@primaryColor: {$settings->button_color};
		.cspio a.btn-primary,.cspio .progress-bar, .countdown_section, .cspio .btn-primary,.cspio .btn-primary:focus, .gform_button, .mymail-wrapper .submit-button, .mailster-wrapper .submit-button, input[type='button'].ninja-forms-field, .wpcf7-submit,.frm_button_submit{
			background-image:none;
			text-shadow:none;
		}
		.countdown_section, .cspio .progress-bar{
		-webkit-box-shadow:none;
		box-shadow:none;
		}
		.cspio input, .cspio input:focus  {
			//border-color:@primaryColor !important;
			-webkit-box-shadow:none !important;
			box-shadow:none !important;
		}
		";
		$less  = new seed_cspv5_legacy_lessc();
		$style = $less->parse( $css );
		echo $style;
		?>
	<?php endif; ?>

	<?php // Set background to black if a video is being used ?>
	<?php if ( ! empty( $settings->bg_video ) ) : ?>
		html{background-color:#000;}
	<?php endif; ?>

	<?php //Backgound IOS Fix ?>
	<?php if ( empty( $settings->bg_slideshow ) ) : ?>
		<?php if ( empty( $settings->bg_video ) ) : ?>
			<?php if ( $settings->background_size == 'cover' && ! empty( $settings->background_image ) ) : ?>

			@supports (-webkit-overflow-scrolling: touch) {
				html {
				height: 100%;
				overflow: hidden;
				}
				body
				{
				height:100%;
				overflow: auto;
				-webkit-overflow-scrolling: touch;
				}
			}

		<?php endif; ?>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ( ! empty( $settings->theme_css ) ) : ?>
		<?php echo $settings->theme_css; ?>
	<?php endif; ?>
	<?php if ( ! empty( $settings->custom_css ) ) : ?>
	/* Custom CSS */
		<?php echo $settings->custom_css; ?>
	<?php endif; ?>

	<?php
	if ( $settings->emaillist == 'mymail' ) {
		$css = "
	  .mailster-form-fields{
	  	text-align:left;
	  }
	  @primaryColor: {$settings->button_color};
	  @secondaryColor: darken(@primaryColor, 15%);
	  .mymail-wrapper label, .mailster-wrapper label{
	    font-weight:normal;
	  }
	  .cspio input{
	    border-width:0px;
	    border-radius: 4px;
	    background-color: $settings->form_color;
	  }
	  .mailster-wrapper .input{
	  	padding:10px;
	  }
	  .submit-button:hover{
	    background:@secondaryColor !important;
	  }
	  .submit-button{
	    border-radius: 4px;
	    padding:10px;
	  }
	  .mymail-form-info p, .mailster-form-info p{
	    color: #fff !important;
	  }
	  .mymail-wrapper, .mailster-wrapper{
	    margin-bottom:10px !important;
	  }

	  ";


		$less  = new seed_cspv5_legacy_lessc();
		$style = $less->parse( $css );
		echo $style;

	}

	if ( $settings->emaillist == 'ninjaforms' ) {
		$css = "
	  .nf-form-cont {
	  	text-align:left;
	  }
	  @primaryColor: {$settings->button_color};
	  @secondaryColor: darken(@primaryColor, 15%);
	  .nf-field-label label {
	    font-weight:normal;
	  }
	  .cspio input, .cspio textarea{
	    border-width:0px;
	    border-radius: 4px;
	    background-color: $settings->form_color;
	  }
	  .nf-form-content input{
	  	padding:10px;
	  }


	  .nf-form-cont {
	    margin-bottom:10px !important;
	  }

	  ";


		$less  = new seed_cspv5_legacy_lessc();
		$style = $less->parse( $css );
		echo $style;

	}

	if ( $settings->emaillist == 'formidable' ) {
		$css = "
	  @primaryColor: {$settings->button_color};
	  @secondaryColor: darken(@primaryColor, 15%);

	  .cspio input, .cspio textarea{
	    border-width:0px !important;
	    border-radius: 4px !important;
	    background-color: $settings->form_color !important;
	  }

	.lightordark (@c) when (lightness(@c) >= 65%) {
		color: black;
		text-shadow: 0 -1px 0 rgba(256, 256, 256, 0.3);
	}
	.lightordark (@c) when (lightness(@c) < 65%) {
		color: white;
		text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.3);
	}

	.frm_button_submit {
		.lightordark (@primaryColor) !important;
		background: @primaryColor !important;
		border: none !important;
		box-shadow: none !important;
	}

	  ";


		$less  = new seed_cspv5_legacy_lessc();
		$style = $less->parse( $css );
		echo $style;

		?>
	  .cspio label{
			font-family: <?php echo $settings->text_font; ?> !important;
			font-weight: <?php echo preg_replace( '/[a-zA-Z]/', '', $settings->text_weight ); ?> !important;
			font-style: <?php echo preg_replace( '/[0-9]/', '', $settings->text_weight ); ?> !important;
			font-size: <?php echo $settings->text_size; ?>px !important;
			line-height: <?php echo $settings->text_line_height; ?>em !important;
			<?php if ( ! empty( $settings->text_color ) ) : ?>
			color:<?php echo $settings->text_color; ?> !important;
			<?php endif; ?>
	  }

	  .frm_description{
		font-family: <?php echo $settings->text_font; ?> !important;
		font-weight: <?php echo preg_replace( '/[a-zA-Z]/', '', $settings->text_weight ); ?> !important;
		<?php if ( ! empty( $settings->text_color ) ) : ?>
			color:<?php echo $settings->text_color; ?> !important;
			<?php endif; ?>
	  }

		<?php

	}


	?>


	<?php if ( ! empty( $settings->enable_recaptcha ) && ! empty( $settings->recaptcha_site_key ) && ! empty( $settings->recaptcha_secret_key ) ) { ?>
		<?php if ( ! empty( $settings->enable_invis_recaptcha ) ) { ?>
			.grecaptcha-badge{
				display:none;
			}
		<?php } ?>
	<?php } ?>

	<?php if ( ! empty( $settings->enable_wp_head_footer ) ) { ?>
	#cspio-cf-modal .modal-dialog{
		z-index:1050;
	}
	<?php } ?>


	</style>

	<?php if ( ! empty( $settings->enable_recaptcha ) && ! empty( $settings->recaptcha_site_key ) && ! empty( $settings->recaptcha_secret_key ) ) { ?>
			<script>
			 var emplacementRecaptcha = '';
			 var emplacementRecaptcha2 = '';
			 var IRonloadCallback = function(){
					<?php if ( ! empty( $settings->enable_form ) ) { ?>
					emplacementRecaptcha = grecaptcha.render("emplacementRecaptcha",{
						"sitekey": "<?php echo $settings->recaptcha_site_key; ?>",
						"badge": "inline",
						"type": "image",
						<?php if ( ! empty( $settings->enable_invis_recaptcha ) ) { ?>
						"size": "invisible",
						"callback": send_request
						<?php } else { ?>
						"size": "normal"
						<?php } ?>
						

						
					});
					//console.log(emplacementRecaptcha);
					<?php } ?>

					<?php if ( ! empty( $settings->enable_cf_form ) ) { ?>
		
					emplacementRecaptcha2 = grecaptcha.render("emplacementRecaptcha2",{
						"sitekey": "<?php echo $settings->recaptcha_site_key; ?>",
						"badge": "inline",
						"type": "image",
						<?php if ( ! empty( $settings->enable_invis_recaptcha ) ) { ?>
						"size": "invisible",
						"callback": cf_send_request
						<?php } else { ?>
						"size": "normal"
						<?php } ?>
						
					});
					//console.log(emplacementRecaptcha2);

				<?php } ?>
				};


			</script>

	<?php } ?>



	<?php
	//var_dump($_SERVER['HTTP_ACCEPT_LANGUAGE']);
	if ( ! empty( $langs ) ) {
		?>
	<link rel="stylesheet" href="<?php echo SEEDPROD_PRO_PLUGIN_URL; ?>app/backwards/template/css/select2.min.css">
	<?php } ?>
	<!-- JS -->
	<?php
	$include_url = trailingslashit( includes_url() );
	if ( empty( $settings->enable_wp_head_footer ) ) {
		echo '<script src="' . $include_url . 'js/jquery/jquery.js"></script>' . "\n";
	}
	?>

	<!-- Modernizr -->
	<script src="<?php echo SEEDPROD_PRO_PLUGIN_URL; ?>app/backwards/template/js/modernizr-custom.js"></script>
	<!-- Retina JS -->
	<?php if ( ! empty( $settings->enable_retinajs ) ) { ?>
	<script src="<?php echo SEEDPROD_PRO_PLUGIN_URL; ?>app/backwards/template/js/retina.min.js"></script>
	<?php } ?>


	<?php if ( ! empty( $settings->enable_recaptcha ) && ! empty( $settings->recaptcha_site_key ) && ! empty( $settings->recaptcha_secret_key ) ) { ?>
	<!-- Recaptcha -->
	<script src="https://www.google.com/recaptcha/api.js?onload=IRonloadCallback&render=explicit" async defer></script>
	<?php } ?>





	
	
	<?php
	if ( ! empty( $settings->typekit_id ) ) {
		$output  = '<!-- Typekit -->' . PHP_EOL;
		$output .= '<script type="text/javascript" src="//use.typekit.com/' . $settings->typekit_id . '.js"></script>' . PHP_EOL;
		$output .= '<script type="text/javascript">try{Typekit.load();}catch(e){}</script>' . PHP_EOL;
		echo $output;
	}
	?>
	



<!-- Header Scripts -->
	<?php if ( ! empty( $settings->header_scripts ) ) : ?>
		<?php echo $settings->header_scripts; ?>
	<?php endif; ?>

<!-- Google Analytics -->
<?php if ( ! empty( $settings->ga_analytics ) ) : ?>
	<?php if ( substr( trim( $settings->ga_analytics ), 0, 3 ) === 'UA-' ) { ?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', '<?php echo $settings->ga_analytics; ?>', 'auto');
  ga('send', 'pageview');

</script>
		<?php
	} else {
		echo $settings->ga_analytics;
	}
	?>
<?php endif; ?>

<?php
// Check if wp_head is enabled
if ( ! empty( $settings->enable_wp_head_footer ) ) {
	echo "<!-- wp_head() -->\n";

	if ( $settings->emaillist == 'gravityforms' ) {
		if ( class_exists( 'RGFormsModel' ) ) {
			if ( ! empty( $gravityforms_form_id ) ) {
				gravity_form_enqueue_scripts( $gravityforms_form_id, false );
			}
		}
	}

	wp_enqueue_script( 'jquery' );
	wp_head();

}
?>

	<script src="<?php echo SEEDPROD_PRO_PLUGIN_URL; ?>app/backwards/template/js/purl.min.js"></script>
	<script src="<?php echo SEEDPROD_PRO_PLUGIN_URL; ?>app/backwards/template/js/js.cookie.js"></script>
	<script>
	var csp_cookies = '';
	var email_cookie = '';
	var return_user = false;
	jQuery( document ).ready(function() {
		csp_cookies = Cookies.noConflict();
		email_cookie = csp_cookies.get('email');
	
	
	<?php if ( empty( $_GET['seed_cspv5_preview'] ) && ( ( ( ! empty( $settings->enable_reflink ) && $settings->enable_reflink == '1' ) && ( ! empty( $settings->rp_return_user ) && $settings->rp_return_user == '1' ) ) || ( ( ! empty( $settings->enable_prize_levels ) && $settings->enable_prize_levels == '1' ) && ( ! empty( $settings->rp_return_user ) && $settings->rp_return_user == '1' ) ) ) ) { ?>
	if(typeof email_cookie !== "undefined"){
		return_user = true;
	}
	<?php } ?>
	});
	



	</script>
	
</head>
<body>

	<div id="cspio-page">
		<?php if ( ! empty( $langs ) ) { ?>
			<?php if ( count( $langs ) > 1 ) { ?>
		<div id="cspio-langs-wrapper"><?php seed_cspv5_legacy_select_lang( 'cspio-langs', $langs, $lang_id ); ?></div>
			<?php } ?>
		<?php } ?>
		<div id="cspio-content">
<?php
$show_col = false;
if ( $settings->blocks[0] != 'column' ) {
	if ( $settings->blocks[ count( $settings->blocks ) - 1 ] != 'column' ) {
		$show_col = true;
	}
}

?>
<?php if ( $show_col ) : ?>
<div class="row">
<div class="col-sm-6">
<?php endif; ?>
<?php
//var_dump($settings);
//die();
foreach ( $settings->blocks as $v ) :
	if ( $v == 'column' ) {
		if ( $show_col ) {
			 include SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/template/show_' . $v . '.php';
		}
	} else {
		include SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/template/show_' . $v . '.php';
	}
	endforeach;
?>
<?php if ( $show_col ) : ?>
</div>
</div>
<?php endif; ?>

		</div><!-- end of #seed-cspio-content -->
		<?php if ( ! empty( $settings->enable_footercredit ) ) : ?>

			<?php if ( $settings->credit_type == 'text' ) : ?>
				<div id="cspio-credit">
					<?php if ( empty( $settings->footer_credit_link ) ) : ?>
					<span><?php echo $settings->footer_credit_text; ?></span>
					<?php else : ?>
					<span><a target="_blank" href="<?php echo $settings->footer_credit_link; ?>"><?php echo $settings->footer_credit_text; ?></a></span>
					<?php endif; ?>
				</div>
			<?php elseif ( $settings->credit_type == 'image' ) : ?>
				<div id="cspio-credit">
					<span><a target="_blank" href="<?php echo $settings->footer_credit_link; ?>"><img src="<?php echo $settings->footer_credit_img; ?>" class="img-responsive"/></a>
				</div>
			<?php elseif ( $settings->credit_type == 'affiliate' ) : ?>
				<div id="cspio-credit" style="background-color: rgba(0,0,0,0.8);">
					<span><a target="_blank" href="<?php echo $settings->footer_affiliate_link; ?>"><img id="aff" src="<?php echo SEEDPROD_PRO_PLUGIN_URL; ?>app/backwards/template/images/seedprod-credit.png" class="img-responsive"/></a>
				</div>
			<?php endif; ?>

		<?php endif; ?>
	</div>

	<script src="<?php echo SEEDPROD_PRO_PLUGIN_URL; ?>app/backwards/template/js/bootstrap.min.js"></script>
	<!-- Contact Form -->
	<?php if ( ! empty( $settings->enable_cf_form ) ) { ?>
	<div id="cspio-cf-modal" class="modal fade" tabindex="-1" role="dialog">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-body">
			<form id="cspio-cf-form">

		
				<input id="cspio-page_id" name="page_id" type="hidden" value="<?php echo $page->id; ?>" />
				<input id="cspio-lang" name="lang" type="hidden" value="<?php echo ( isset( $_GET['lang'] ) ) ? $_GET['lang'] : ''; ?>" />
			  <div class="form-group">
				<label for="cspio-cf-email"><?php echo $settings->txt_contact_form_email; ?></label>
				<input type="email" class="form-control" id="cspio-cf-email" name="cspio-cf-email" required>
			  </div>
			   <div class="form-group">
				<label for="cspio-cf-msg"><?php echo $settings->txt_contact_form_msg; ?></label>
				<textarea class="form-control" id="cspio-cf-msg" name="cspio-cf-msg"></textarea>
			  </div>
			  <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->

				<?php if ( ! empty( $settings->enable_recaptcha ) && ! empty( $settings->recaptcha_site_key ) && ! empty( $settings->recaptcha_secret_key ) ) { ?>
					<div id="emplacementRecaptcha2"></div>
				<?php } ?>
			  <button id="cspio-cf-send-button" type="button" class="btn btn-primary"><?php echo $settings->txt_contact_form_send; ?></button>
			</form>
		  </div>

		</div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<script>
		<?php
		$contact_form_callback_ajax_url = html_entity_decode( wp_nonce_url( admin_url() . 'admin-ajax.php?action=seed_cspv5_legacy_contactform_callback', 'seed_cspv5_legacy_contactform_callback' ) );
		?>

		<?php if ( ! empty( $settings->enable_recaptcha ) && ! empty( $settings->enable_invis_recaptcha ) && ! empty( $settings->recaptcha_site_key ) && ! empty( $settings->recaptcha_secret_key ) ) { ?>
		jQuery( "#cspio-cf-send-button" ).click(function( event ) {
			event.preventDefault();
			grecaptcha.execute(emplacementRecaptcha2);
		});
		<?php } else { ?>
		jQuery( "#cspio-cf-send-button" ).click(function( event ) {
			//if($("form")[0].checkValidity()){
			event.preventDefault();
			cf_send_request();
			//}
		});
		<?php } ?>



			function cf_send_request() {
				jQuery.ajax({
				url: "<?php echo $contact_form_callback_ajax_url; ?>",
				dataType: "jsonp",
				timeout: 30000,
				data: jQuery("#cspio-cf-form").serialize(),
			 
				// Work with the response
				success: function( response ) {
					if(response.status == '200'){
						if(response.html == ''){
							jQuery('#cspio-cf-email').val('');
							jQuery('#cspio-cf-msg').val('');
							jQuery('#cspio-cf-modal').modal('hide');
						}else{
							jQuery( "#cspio-cf-form" ).replaceWith( response.html )
						}

					}else{
						<?php if ( ! empty( $settings->enable_recaptcha ) ) { ?>
								grecaptcha.reset(emplacementRecaptcha2);
						<?php } ?>
						alert(response.status);
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					console.log(jqXHR);
					console.log(errorThrown);
					<?php if ( ! empty( $settings->enable_recaptcha ) && ! empty( $settings->recaptcha_site_key ) && ! empty( $settings->recaptcha_secret_key ) ) { ?>
					grecaptcha.reset(emplacementRecaptcha2);
					<?php } ?>
					alert('The email could not be sent. Possible reason: your host may have disabled the mail() function.');
				}
			});

		}
	</script>
	<?php } ?>

	
	<?php if ( ! empty( $settings->privacy_policy ) ) { ?>
	<!-- Privacy Policy -->
	<div id="cspio-pp-modal" class="modal fade" tabindex="-1" role="dialog">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-body">
			<?php echo $settings->privacy_policy; ?>
		  </div>

		</div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<?php } ?>


	
	<?php
	// WP Footer
	if ( ! empty( $settings->enable_wp_head_footer ) ) {
		echo "<!-- wp_footer() -->\n";
		wp_footer();
		//$include_theme_stylesheet = seed_get_plugin_api_value('include_theme_stylesheet');
		if ( empty( $include_theme_stylesheet ) ) {
			echo "<script>\n";
			echo 'jQuery(\'link[href*="' . get_stylesheet_directory_uri() . '"]\').remove();';
			echo "</script>\n";
		}
	}

	//WPML
	require_once ABSPATH . 'wp-admin/includes/plugin.php';

	if ( is_plugin_active( 'wpml-string-translation/plugin.php' ) ) {
		if ( ! empty( $display_lang_switcher ) ) {
			do_action( 'icl_language_selector' );
		}
	}
	?>

	<?php if ( ! empty( $settings->enable_fitvid ) ) { ?>
	<!-- FitVid -->
	<script src="<?php echo SEEDPROD_PRO_PLUGIN_URL; ?>app/backwards/template/js/jquery.fitvids.min.js"></script>
	<script>jQuery(document).ready(function($){$("#cspio-description,#cspio-thankyoumsg").fitVids();});</script>
	<?php } ?>
	
	<!-- Scripts -->
	<script src="<?php echo SEEDPROD_PRO_PLUGIN_URL; ?>app/backwards/template/js/scripts.js"></script>

	<?php if ( ! empty( $settings->bg_video ) && ! empty( $video_id ) ) : ?>
	<!-- Tubular -->
	<script src="<?php echo SEEDPROD_PRO_PLUGIN_URL; ?>app/backwards/template/js/tubular.js"></script>
	<?php endif; ?>
	
	<?php if ( ! empty( $settings->bg_video ) && empty( $video_id ) ) : ?>
	<!-- BigVideo -->
	<script src="<?php echo SEEDPROD_PRO_PLUGIN_URL; ?>app/backwards/template/js/video.js"></script>
	<script src="<?php echo SEEDPROD_PRO_PLUGIN_URL; ?>app/backwards/template/js/bigvideo.js"></script>
	<?php endif; ?>

	<!-- Animate -->
	<?php if ( ! empty( $settings->container_effect_animation ) ) : ?>
		<?php if ( ! empty( $settings->background_image ) ) : ?>

		<script>
		if(return_user == false){
			jQuery('<img/>').attr('src', '<?php echo $settings->background_image; ?>').load(function() {
			   jQuery(this).remove(); // prevent memory leaks as @benweet suggested
			   jQuery("#cspio-content").show().addClass('animated <?php echo $settings->container_effect_animation; ?>');
			}).error(function() {
			   jQuery(this).remove(); // prevent memory leaks as @benweet suggested
			   jQuery("#cspio-content").show().addClass('animated <?php echo $settings->container_effect_animation; ?>');	
			});
		}
		</script>
	<?php else : ?>
	<script>
	jQuery("#cspio-content").show().addClass('animated <?php echo $settings->container_effect_animation; ?>');
	</script>
	<?php endif; ?>
	<?php endif; ?>

	<?php if ( ! empty( $settings->bg_slideshow ) ) : ?>

		<!-- Slideshow -->
		<script>
		jQuery(document).ready(function($){

		$.backstretch([
			<?php if ( ! empty( $settings->bg_slideshow_images ) ) : ?>
				<?php $i = 0; ?>
				<?php
				if ( ! empty( $settings->bg_slideshow_randomize ) ) {
					shuffle( $settings->bg_slideshow_images );
				}

				?>
				<?php foreach ( $settings->bg_slideshow_images as $k => $v ) { ?>
					<?php if ( $i !== 0 ) : ?>
				,
				<?php endif; ?>
					<?php if ( ! empty( $v ) ) : ?>
					'<?php echo trim( $v ); ?>'
				<?php endif; ?>
					<?php $i++; ?>
			<?php } ?>
		<?php endif; ?>
		], {
			fade: 750,
		<?php if ( ! empty( $settings->bg_slideshow_slide_speed ) ) : ?>
			duration: <?php echo $settings->bg_slideshow_slide_speed * 1000; ?>,
		<?php else : ?>
			duration:3000,
		<?php endif; ?>
		});

		});


		</script>
	<?php endif; ?>

	<?php if ( ! empty( $settings->bg_video ) ) : ?>
		<?php if ( ! empty( $settings->bg_video_url ) ) : ?>

				<?php
				//$bg_video_url_arr = '';
				//parse_str( parse_url( $settings->bg_video_url, PHP_URL_QUERY ), $bg_video_url_arr );


				$audio = 'false';
				if ( empty( $settings->bg_video_audio ) ) {
					$audio = 'true';
				}

				 $loop = 'true';
				if ( empty( $settings->bg_video_loop ) ) {
					$loop = 'false';
				}
				?>

	
				<script>
				<?php if ( ! empty( $video_id ) ) { ?>
				jQuery(document).ready(function($){
					if (Modernizr.touchevents == false) {
						$('#cspio-page').tubular({
						videoId: '<?php echo $video_id; ?>',
						mute: <?php echo $audio; ?>,
						repeat: <?php echo $loop; ?>,
						});
					}
				});
				<?php } else { ?>
				jQuery(document).ready(function($){
					if (Modernizr.touchevents == false) {
						var BV = new $.BigVideo();
						BV.init();
						BV.show('<?php echo $settings->bg_video_url; ?>',{ambient:<?php echo $audio; ?>,doLoop:<?php echo $loop; ?>});
						$('#big-video-wrap').show()
					}
				});
				<?php } ?>
				</script>


		<?php endif; ?>
	<?php endif; ?>

	<?php if ( ! empty( $langs ) ) { ?>
	<script type="text/javascript" src="<?php echo SEEDPROD_PRO_PLUGIN_URL; ?>app/backwards/template/js/select2.min.js"></script>
	<script>
	var langs_arr = '<?php echo json_encode( $lang_settings_all ); ?>';
	function formatState (state) {
	  if (!state.id) { return state.text; }
	  var prop = state.element.value;
	  if(prop == 0){
		  prop = 'default_lang';
	  }
	  var flags = jQuery.parseJSON(langs_arr);
	  flag = flags[prop].flag;
	  //console.log(flag);
	  var $state = jQuery(
		'<span><img style="display:inline;vertical-align: text-bottom;" src="<?php echo SEEDPROD_PRO_PLUGIN_URL . 'app/backwards/template/images/flags-iso/flat/'; ?>' + flag + '" class="img-flag" /> ' + state.text + '</span>'
	  );
	  return $state;
	};
	</script>
	<?php } ?>
	
	<script>
	function resize(){
			jQuery('head').append("<style id='form-style' type='text/css'></style>");
			jQuery('#form-style').html('.cspio #cspio-field-wrapper .input-group-btn, .cspio #cspio-field-wrapper .input-group{display:block;width:100%;}.cspio #cspio-subscribe-btn{margin-left:0;width:100%;display:block;}.cspio .input-group .form-control:first-child, .cspio .input-group-addon:first-child, .cspio .input-group-btn:first-child>.btn, .cspio .input-group-btn:first-child>.dropdown-toggle, .cspio .input-group-btn:last-child>.btn:not(:last-child):not(.dropdown-toggle) {border-bottom-right-radius: 4px;border-top-right-radius: 4px;}.cspio .input-group .form-control:last-child, .cspio .input-group-addon:last-child, .cspio .input-group-btn:last-child>.btn, .cspio .input-group-btn:last-child>.dropdown-toggle, .cspio .input-group-btn:first-child>.btn:not(:first-child) {border-bottom-left-radius: 4px;border-top-left-radius: 4px;}');
	}
	
	jQuery('#cspio-content').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
		var width = jQuery('#cspio-field-wrapper').width();
		//console.log(width);
		if(width < 350 && width != 0){
			resize();
		}
	}
	);

	<?php if ( ! empty( $langs ) ) { ?>

	jQuery( document ).ready(function($) {
		jQuery("#cspio-langs").select2({
			templateResult: formatState,
			templateSelection: formatState
		});
		jQuery('#cspio-langs').change(function() {	
			if (location.href.indexOf("?") >= 0){
				if(location.href.indexOf("lang") >= 0){
					location.href = location.href.replace(/lang=\d|lang=lang_\d/g, 'lang='+jQuery(this).val());
				}else{
					location.href = location.href+ '&lang='+jQuery(this).val();
				}
			}else{
				location.href = location.href+ '?lang='+jQuery(this).val();
			}	
		});
	});
	<?php } ?>
	</script>




	<?php if ( ! empty( $settings->footer_scripts ) ) : ?>
	<!-- Footer Scripts -->
		<?php echo $settings->footer_scripts; ?>
	<?php endif; ?>
	<?php if ( ! empty( $settings->theme_scripts ) ) : ?>
	<!-- Theme Scripts -->
		<?php echo $settings->theme_scripts; ?>
	<?php endif; ?>
</body>
</html>


<!-- This page was generated by SeedProd.com | Learn more: http://www.seedprod.com -->
