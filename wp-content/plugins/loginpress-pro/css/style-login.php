<?php
$loginpress_pro_array = (array) get_option( 'loginpress_customization' );

function loginpress_pro_get_option_key( $loginpress_key, $loginpress_pro_array ) {

	if ( array_key_exists( $loginpress_key, $loginpress_pro_array ) ) :

		return $loginpress_pro_array[ $loginpress_key ];
	endif;
}

$loginpress_google_font	 = loginpress_pro_get_option_key( 'google_font', $loginpress_pro_array );

/**
* Register custom fonts.
*/
if ( $loginpress_google_font ) :

	$json_file = file_get_contents( LOGINPRESS_PRO_ROOT_PATH . '/fonts/google-web-fonts.txt' );
	$json_font = json_decode($json_file);
	$json_font_array = $json_font->items;

	$font_array = array();
	foreach ( $json_font_array as $key  ) :

		 $loginpress_get_font = $loginpress_google_font ==  $key->family ? $loginpress_google_font : false;
		 if ( $loginpress_get_font ) : $font_array[] = $key; endif;
	endforeach;

	$loginpress_font_name = $font_array ? $font_array[0]->family : false;

	$loginpress_font_name = $font_array[0]->family;
	$font_weights 	 = $font_array[0]->variants;
	$font_weight		 = implode( ",", $font_weights );
	$subsets 				 = $font_array[0]->subsets;
	$subset	 				 = implode( ",", $subsets );
	$fonts_url 			 = '';
	$font_families	 = array();
	$font_families[] = "{$loginpress_font_name}:{$font_weight}";
	$query_args 		 = array(
		'family' => urlencode( implode( '|', $font_families ) ),
		'subset' => urlencode( $subset ),
	);

	$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );

	$loginpress_fonts_url = esc_url_raw( $fonts_url );
endif;


if ( $loginpress_google_font ) : ?>
	<link href="<?php echo $loginpress_fonts_url; ?>" rel='stylesheet'>
	<style type="text/css">
	body{
		font-family: <?php echo '"' . $loginpress_font_name . '"'; ?> !important;
	}
	.login input[type="submit"], .login form .input, .login input[type="text"] {
		font-family: <?php echo '"' . $loginpress_font_name . '"'; ?> !important;
	}
	</style>

<?php endif; ?>

<script type="text/javascript">
		// Resize reCAPTCHA to fit width of container
		// Since it has a fixed width, we're scaling
		// using CSS3 transforms
		// ------------------------------------------
		// captchaScale = containerWidth / elementWidth

		function scaleCaptcha(elementWidth) {
		  // Width of the reCAPTCHA element, in pixels
		  if(document.querySelector('.loginpress_recaptcha_wrapper')){
			var reCaptchaWidth = 304;
			// Get the containing element's width
				var containerWidth = document.querySelector('.loginpress_recaptcha_wrapper').clientWidth;
			// Only scale the reCAPTCHA if it won't fit
			// inside the container
			if(reCaptchaWidth > containerWidth) {
				// Calculate the scale
				var captchaScale = containerWidth / reCaptchaWidth;
				// Apply the transformation
				document.querySelector('.g-recaptcha').style.transform =  'scale(' + captchaScale + ')';
				// console.log(captchaScale);
			}
		}
		}

		// $(function() {

		//   // Initialize scaling
		//   scaleCaptcha();

		//   // Update scaling on window resize
		//   // Uses jQuery throttle plugin to limit strain on the browser


		// });
		document.addEventListener("DOMContentLoaded", function(event) {
			scaleCaptcha();
		});
		window.onresize = function(event) {
			scaleCaptcha()
		};
		window.onload = function(){
			scaleCaptcha()
		};
</script>
