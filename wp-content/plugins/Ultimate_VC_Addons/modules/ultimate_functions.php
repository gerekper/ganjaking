<?php
/**
 * Ultimate Function file
 *
 *  @package Ultimate Function file
 */

if ( ! function_exists( 'ultimate_get_icon_position_json' ) ) {
	/**
	 * Function that display the icon in different positions.
	 *
	 * @method ultimate_get_icon_position_json
	 */
	function ultimate_get_icon_position_json() {
		$json = '{
			"Display Text and Icon - Always":{
				"Icon_at_Left":"ubtn-sep-icon-at-left",
				"Icon_at_Right":"ubtn-sep-icon-at-right"
			},
			"Display Icon With Text - On_Hover":{
				"Bring_in_Icon_from_Left":"ubtn-sep-icon-left",
				"Bring_in_Icon_from_Right":"ubtn-sep-icon-right",
				"Push_Icon_to_Left":"ubtn-sep-icon-left-rev",
				"Push_Icon_to_Right":"ubtn-sep-icon-right-rev"
			},
			"Replace Text by Icon - On Hover":{
				"Push_out_Text_to_Top":"ubtn-sep-icon-bottom-push",
				"Push_out_Text_to_Bottom":"ubtn-sep-icon-top-push",
				"Push_out_Text_to_Left":"ubtn-sep-icon-right-push",
				"Push_out_Text_to_Right‏":"ubtn-sep-icon-left-push"
			}
		}';
		return $json;
	}
}
/**
 * Function that display the banner in different styles.
 *
 * @method ultimate_get_banner2_json
 */
function ultimate_get_banner2_json() {
	$json = '{
		"Long_Text":{
			"Style_1":"style1",
			"Style_2":"style5",
			"Style_3":"style13"
		},
		"Medium_Text":{
			"Style_4":"style2",
			"Style_5":"style4",
			"Style_6":"style6",
			"Style_7":"style7",
			"Style_8":"style10",
			"Style_9":"style14"
		},
		"Short_Description":{
			"Style_10":"style9",
			"Style_11":"style11",
			"Style_12":"style15"
		}
	}';
	return $json;
}
if ( ! function_exists( 'ultimate_get_animation_json' ) ) {
	/**
	 * Function that display the animation json in different styles.
	 *
	 * @method ultimate_get_animation_json
	 */
	function ultimate_get_animation_json() {
		$json = '{
		  "attention_seekers": {
			"No Animation": true,
			"bounce": true,
			"flash": true,
			"pulse": true,
			"rubberBand": true,
			"shake": true,
			"swing": true,
			"tada": true,
			"wobble": true
		  },
		  "bouncing_entrances": {
			"bounceIn": true,
			"bounceInDown": true,
			"bounceInLeft": true,
			"bounceInRight": true,
			"bounceInUp": true
		  },
		  "bouncing_exits": {
			"bounceOut": true,
			"bounceOutDown": true,
			"bounceOutLeft": true,
			"bounceOutRight": true,
			"bounceOutUp": true
		  },
		  "fading_entrances": {
			"fadeIn": true,
			"fadeInDown": true,
			"fadeInDownBig": true,
			"fadeInLeft": true,
			"fadeInLeftBig": true,
			"fadeInRight": true,
			"fadeInRightBig": true,
			"fadeInUp": true,
			"fadeInUpBig": true
		  },
		  "fading_exits": {
			"fadeOut": true,
			"fadeOutDown": true,
			"fadeOutDownBig": true,
			"fadeOutLeft": true,
			"fadeOutLeftBig": true,
			"fadeOutRight": true,
			"fadeOutRightBig": true,
			"fadeOutUp": true,
			"fadeOutUpBig": true
		  },
		  "flippers": {
			"flip": true,
			"flipInX": true,
			"flipInY": true,
			"flipOutX": true,
			"flipOutY": true
		  },
		  "lightspeed": {
			"lightSpeedIn": true,
			"lightSpeedOut": true
		  },
		  "rotating_entrances": {
			"rotateIn": true,
			"rotateInDownLeft": true,
			"rotateInDownRight": true,
			"rotateInUpLeft": true,
			"rotateInUpRight": true
		  },
		  "rotating_exits": {
			"rotateOut": true,
			"rotateOutDownLeft": true,
			"rotateOutDownRight": true,
			"rotateOutUpLeft": true,
			"rotateOutUpRight": true
		  },
		  "sliders": {
			"slideInDown": true,
			"slideInLeft": true,
			"slideInRight": true,
			"slideOutLeft": true,
			"slideOutRight": true,
			"slideOutUp": true,
			"slideInUp": true,
			"slideOutDown": true
		  },
		  "specials": {
			"hinge": true,
			"rollIn": true,
			"rollOut": true
		  },
		  "zooming_entrances": {
			"zoomIn": true,
			"zoomInDown": true,
			"zoomInLeft": true,
			"zoomInRight": true,
			"zoomInUp": true
		  },
		  
		  "zooming_exits": {
			"zoomOut": true,
			"zoomOutDown": true,
			"zoomOutLeft": true,
			"zoomOutRight": true,
			"zoomOutUp": true
		  },
		  
		  "infinite_animations": {
			"InfiniteRotate": true,
			"InfiniteRotateCounter": true,
			"InfiniteDangle": true,
			"InfiniteSwing": true,
			"InfinitePulse": true,	
			"InfiniteHorizontalShake": true,
			"InfiniteVericalShake": true,
			"InfiniteBounce": true,
			"InfiniteFlash": true,
			"InfiniteTADA": true,	
			"InfiniteRubberBand": true,
			"InfiniteHorizontalFlip": true,
			"InfiniteVericalFlip": true,
			"InfiniteHorizontalScaleFlip": true,
			"InfiniteVerticalScaleFlip": true
		  }
		}';
		return $json;
	}
}
/**
 * GetBrowser funtion.
 *
 * @method ult_getBrowser
 */
function ult_getBrowser() { // PHPCS:ignore:WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	$u_agent  = $_SERVER['HTTP_USER_AGENT'];
	$bname    = 'Unknown';
	$platform = 'Unknown';
	$version  = '';
	$ub       = '';

	// First get the platform?
	if ( preg_match( '/linux/i', $u_agent ) ) {
		$platform = 'linux';
	} elseif ( preg_match( '/macintosh|mac os x/i', $u_agent ) ) {
		$platform = 'mac';
	} elseif ( preg_match( '/windows|win32/i', $u_agent ) ) {
		$platform = 'windows';
	}

	// Next get the name of the useragent yes seperately and for good reason.
	if ( preg_match( '/MSIE/i', $u_agent ) && ! preg_match( '/Opera/i', $u_agent ) ) {
		$bname = 'Internet Explorer';
		$ub    = 'MSIE';
	} elseif ( preg_match( '/Firefox/i', $u_agent ) ) {
		$bname = 'Mozilla Firefox';
		$ub    = 'Firefox';
	} elseif ( preg_match( '/Chrome/i', $u_agent ) ) {
		$bname = 'Google Chrome';
		$ub    = 'Chrome';
	} elseif ( preg_match( '/Safari/i', $u_agent ) ) {
		$bname = 'Apple Safari';
		$ub    = 'Safari';
	} elseif ( preg_match( '/Opera/i', $u_agent ) ) {
		$bname = 'Opera';
		$ub    = 'Opera';
	} elseif ( preg_match( '/Netscape/i', $u_agent ) ) {
		$bname = 'Netscape';
		$ub    = 'Netscape';
	}

	// finally get the correct version number.
	$known   = array( 'Version', $ub, 'other' );
	$pattern = '#(?<browser>' . join( '|', $known ) .
	')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
	if ( ! preg_match_all( $pattern, $u_agent, $matches ) ) { //PHPCS:ignore:Generic.CodeAnalysis.EmptyStatement.DetectedIf
		// we have no matching number just continue.
	}

	// see how many we have.
	$i = count( $matches['browser'] );
	if ( 1 != $i ) {
		// we will have two since we are not using 'other' argument yet.
		// see if version is before or after the name.
		if ( strripos( $u_agent, 'Version' ) < strripos( $u_agent, $ub ) ) {
			$version = $matches['version'][0];
		} else {
			$version = $matches['version'][1];
		}
	} else {
		$version = $matches['version'][0];
	}

	// check if we have a number.
	if ( null == $version || '' == $version ) {
		$version = '?';}

	return array(
		'name'    => $bname,
		'version' => $version,
	);
}
/**
 * Funtion that prepare the array.
 *
 * @param array $atts represts module attribuits.
 * @method ult_prepareAtts
 */
function ult_prepareAtts( $atts ) { // PHPCS:ignore:WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	if ( isset( $atts ) ) {
			$return = str_replace(
				array(
					'``',
				),
				array(
					'"',
				),
				$atts
			);
	}
	return $return;
}
