<?php
if( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}

class Mfn_HB_Styles {

	/**
	 * Add inline style
	 */
	public static function add_inline_style( $builder ){

		$custom_css = '';
		$items_css = '';

		foreach( $builder as $device_name => $device ){

			if( isset( $device['grid']['status'] ) && ( 'custom' != $device['grid']['status'] ) ){
				continue; // skip grid status auto and off
			}

			// grid ----------

			$prefix_device = '.mhb-view.'. Mfn_HB_Helper::camel_to_other( $device_name, '.' );

			$options = $device['grid']['options'];
			$style = array();

			if( isset( $options['backgroundColor'] ) && $options['backgroundColor'] ){
				$style[] = 'background-color:'. $options['backgroundColor'];
			}

			if( isset( $options['backgroundImage'] ) && $options['backgroundImage']['bgImg'] ){

				$style[] = 'background-image:url('. $options['backgroundImage']['bgImg'] .')';
				$style[] = 'background-position:'. $options['backgroundImage']['positionHorizontal'] .' '. $options['backgroundImage']['positionVertical'];
				$style[] = 'background-repeat:'. $options['backgroundImage']['repeat'];
				$style[] = 'background-size:'. $options['backgroundImage']['size'];

			}

			$style = implode( ';', $style );

			if( $style ){
				$custom_css .= $prefix_device. '{'. $style .'}';
			}

			// row ----------

			foreach( $device as $row_name => $row ){

				if( ! isset( $row[ 'items' ] ) ){
					continue; // skip for other than row
				}

				if( ( 'firstRow' != $row_name ) && empty( $row['active'] ) ){
					continue; // skip not active rows
				}

				$prefix_row = $prefix_device .' .'.  Mfn_HB_Helper::camel_to_other( $row_name, '-' );

				$options = $row['options'];
				$style = array();

				if( isset( $options['backgroundColor'] ) && $options['backgroundColor'] ){
					$style[] = 'background-color:'. $options['backgroundColor'];
				}

				$style = implode( ';', $style );

				if( $style ){
					$custom_css .= $prefix_row. '{'. $style .'}';
				}

				// row height

				if( isset( $options['height'] ) && $options['height'] ){

					$options['height'] = intval( $options['height'], 10 );

					$custom_css .= $prefix_row. ' .mhb-row-wrapper{min-height:'. $options['height'] .'px}';
					// logo - overflow
					$custom_css .= $prefix_row. ' .overflow.mhb-item-inner{height:'. $options['height'] .'px}';
					// menu
					$custom_css .= $prefix_row. ' .mhb-menu .menu > li > a > span{line-height:'. ( $options['height'] - 20 ) .'px}';
					// image
					$custom_css .= $prefix_row. ' .mhb-image img{max-height:'. $options['height'] .'px}';
				}

				// item ----------

				foreach( $row[ 'items' ] as $cell ){
					foreach( $cell as $item ){

						if( isset( $item['style'] ) ){

							$type = Mfn_HB_Helper::camel_to_snake( $item[ 'name' ] );

							if( method_exists( 'Mfn_HB_Styles', $type ) ){
								$selector = '.mhb-custom-'. $item['uuid'];
								$items_css .= Mfn_HB_Styles::$type( $selector, $item['style'] );
							}

						}

					}
				}

			}

		}

		$custom_css .= $items_css;

    wp_add_inline_style( 'mfn-hb', $custom_css );

	}

	private static function menu( $selector, $style ){

		$css = '';

		// link color
		if( isset( $style['linkColor'] ) && $style['linkColor'] ){
			$css .= $selector .' .menu > li > a{color:'. $style['linkColor'] .'}';
		}

		// active color
		if( isset( $style['activeLinkColor'] ) && $style['activeLinkColor'] ){
			$css .= $selector .' .menu > li.current-menu-item > a, '. $selector .' .menu > li.current-menu-ancestor > a, '. $selector .' .menu > li.current-page-item > a, '. $selector .' .menu > li.current-page-ancestor > a{color:'. $style['activeLinkColor'] .'}';
		}

		// hover color
		if( isset( $style['hoverLinkColor'] ) && $style['hoverLinkColor'] ){
			$css .= $selector .' .menu > li > a:hover{color:'. $style['hoverLinkColor'] .'}';
		}

		// submenu - background
		if( isset( $style['subBackgroundColor'] ) && $style['subBackgroundColor'] ){
			$css .= $selector .' .menu li ul{background-color:'. $style['subBackgroundColor'] .'}';
		}

		// submenu - link color
		if( isset( $style['subLinkColor'] ) && $style['subLinkColor'] ){
			$css .= $selector .' .menu li ul li a{color:'. $style['subLinkColor'] .'}';
		}

		// submenu - active color
		if( isset( $style['subActiveLinkColor'] ) && $style['subActiveLinkColor'] ){
			$css .= $selector .' .menu li ul li.current-menu-item > a, '. $selector .' .menu li ul li.current-menu-ancestor > a, '. $selector .' .menu li ul li.current-page-item > a, '. $selector .' .menu li ul li.current-page-ancestor > a{color:'. $style['subActiveLinkColor'] .'}';
		}

		// submenu - hover color
		if( isset( $style['subHoverLinkColor'] ) && $style['subHoverLinkColor'] ){
			$css .= $selector .' .menu li ul li a:hover{color:'. $style['subHoverLinkColor'] .'}';
		}

		// font

		if( isset( $style['font'] ) && is_array( $style['font'] ) ){

			// weight

			$font_weight = 400;

			if( $weight = str_replace('italic', '', $style['font']['fontStyle']) ){

				$css .= $selector .' .menu > li > a{font-weight:'. $weight .'}';

				$font_weight .= ','. $weight;
			}

			// family

			if( $font_family = $style['font']['fontFamily'] ){

				$css .= $selector .' .menu > li > a{font-family:"'. $font_family .'"}';

				if (in_array($font_family, mfn_fonts('all'))) {
					$font_family = str_replace(' ', '+', $font_family);
					wp_enqueue_style('mfn-hb-'. $font_family .'-'. $weight, 'https://fonts.googleapis.com/css?family='. $font_family .':'. $font_weight );
				}

			}

			// font size

			if( $font_size = $style['font']['fontSize'] ){
				$css .= $selector .' .menu > li > a{font-size:'. $font_size .'px}';
			}

			// style

			if( strpos($style['font']['fontStyle'], 'italic') ) {
				$css .= $selector .' .menu > li > a{font-style:italic}';
			}

			// letter spacing

			if( $letter_spacing = $style['font']['letterSpacing'] ){
				$css .= $selector .' .menu > li > a{letter-spacing:'. $letter_spacing .'px}';
			}

		}

		return $css;

	}

	private static function menu_icon( $selector, $style ){

		$css = '';

		// icon color
		if( isset( $style['iconColor'] ) && $style['iconColor'] ){
			$css .= $selector .' a{color:'. $style['iconColor'] .'}';
		}

		// background color
		if( isset( $style['backgroundColor'] ) && $style['backgroundColor'] ){
			$css .= $selector .' a{background-color:'. $style['backgroundColor'] .'}';
		}

		// hover color
		if( isset( $style['hoverIconColor'] ) && $style['hoverIconColor'] ){
			$css .= $selector .' a:hover{color:'. $style['hoverIconColor'] .'}';
		}

		return $css;

	}

	private static function extras( $selector, $style ){

		$css = '';

		// icon color
		if( isset( $style['iconColor'] ) && $style['iconColor'] ){
			$css .= $selector .' a,'. $selector .' .search form i{color:'. $style['iconColor'] .'}';
		}

		// hover color
		if( isset( $style['hoverColor'] ) && $style['hoverColor'] ){
			$css .= $selector .' a:hover{color:'. $style['hoverColor'] .'}';
		}

		return $css;

	}

	private static function social( $selector, $style ){

		$css = '';

		// icon color
		if( isset( $style['iconColor'] ) ){
			$css .= $selector .' a{color:'. $style['iconColor'] .'}';
		}

		// hover color
		if( isset( $style['hoverColor'] ) ){
			$css .= $selector .' a:hover{color:'. $style['hoverColor'] .'}';
		}

		return $css;

	}

	private static function text( $selector, $style ){

		$css = '';

		// text color
		if( isset( $style['textColor'] ) && $style['textColor'] ){
			$css .= $selector .' {color:'. $style['textColor'] .'}';
		}

		// link color
		if( isset( $style['linkColor'] ) && $style['linkColor'] ){
			$css .= $selector .' a{color:'. $style['linkColor'] .'}';
		}

		// hover color
		if( isset( $style['hoverLinkColor'] ) && $style['hoverLinkColor'] ){
			$css .= $selector .' a:hover{color:'. $style['hoverLinkColor'] .'}';
		}

		return $css;

	}

	private static function icon( $selector, $style ){

		$css = '';

		// icon color
		if( isset( $style['iconColor'] ) && $style['iconColor'] ){
			$css .= $selector .' a{color:'. $style['iconColor'] .'}';
		}

		// hover color
		if( isset( $style['hoverIconColor'] ) && $style['hoverIconColor'] ){
			$css .= $selector .' a:hover{color:'. $style['hoverIconColor'] .'}';
		}

		return $css;

	}

	private static function button( $selector, $style ){

		$css = '';

		// text color
		if( isset( $style['textColor'] ) && $style['textColor'] ){
			$css .= $selector .' .action_button{color:'. $style['textColor'] .'}';
		}

		// background color
		if( isset( $style['buttonColor'] ) && $style['buttonColor'] ){
			$css .= $selector .' .action_button{background-color:'. $style['buttonColor'] .'}';
		}

		// hover text color
		if( isset( $style['hoverTextColor'] ) && $style['hoverTextColor'] ){
			$css .= $selector .' .action_button:hover{color:'. $style['hoverTextColor'] .'}';
		}

		// hover background color
		if( isset( $style['hoverButtonColor'] ) && $style['hoverButtonColor'] ){
			$css .= $selector .' .action_button:hover{background-color:'. $style['hoverButtonColor'] .'}';
			$css .= $selector .' .action_button:after{display:none}';
		}

		return $css;

	}

}
