<?php
/**
 * Theme Options - fields and args
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

require_once(get_theme_file_path('/muffin-options/fonts.php'));
require_once(get_theme_file_path('/muffin-options/options.php'));


/**
 * Options Page | Helper Functions
 */

if( ! function_exists( 'mfna_header_style' ) )
{
	/**
	 * Header Style
	 * @return array
	 */

	function mfna_header_style(){
		return array(
			'classic' => array('title' => 'Classic', 'img' => MFN_OPTIONS_URI.'img/select/header/classic.png'),
			'modern' => array('title' => 'Modern', 'img' => MFN_OPTIONS_URI.'img/select/header/modern.png'),
			'plain' => array('title' => 'Plain', 'img' => MFN_OPTIONS_URI.'img/select/header/plain.png'),
			'stack,left' => array('title' => 'Stack: Left', 'img' => MFN_OPTIONS_URI.'img/select/header/stack-left.png'),
			'stack,center' => array('title' => 'Stack: Center',	'img' => MFN_OPTIONS_URI.'img/select/header/stack-center.png'),
			'stack,right' => array('title' => 'Stack: Right', 'img' => MFN_OPTIONS_URI.'img/select/header/stack-right.png'),
			'stack,magazine' => array('title' => 'Magazine', 'img' => MFN_OPTIONS_URI.'img/select/header/magazine.png'),
			'creative' => array('title' => 'Creative', 'img' => MFN_OPTIONS_URI.'img/select/header/creative.png'),
			'creative,rtl' => array('title' => 'Creative Right', 'img' => MFN_OPTIONS_URI.'img/select/header/creative-right.png'),
			'creative,open' => array('title' => 'Creative: Always Open', 'img' => MFN_OPTIONS_URI.'img/select/header/creative-open.png'),
			'creative,open,rtl' => array('title' => 'Creative Right: Always Open', 'img' => MFN_OPTIONS_URI.'img/select/header/creative-open-right.png'),
			'fixed' => array('title' => 'Fixed', 'img' => MFN_OPTIONS_URI.'img/select/header/fixed.png'),
			'transparent' => array('title' => 'Transparent', 'img' => MFN_OPTIONS_URI.'img/select/header/transparent.png'),
			'simple' => array('title' => 'Simple', 'img' => MFN_OPTIONS_URI.'img/select/header/simple.png'),
			'simple,empty' => array('title' => 'Empty: Subpage without Header', 'img' => MFN_OPTIONS_URI.'img/select/header/empty.png'),
			'below' => array('title' => 'Below Slider', 'img' => MFN_OPTIONS_URI.'img/select/header/below.png'),
			'split' => array('title' => 'Split Menu<br /><i>&#8226; Page Options: Custom Menu is not supported</i>', 'img' => MFN_OPTIONS_URI.'img/select/header/split.png'),
			'split,semi' => array('title' => 'Split Menu Semitransparent<br /><i>&#8226; Page Options: Custom Menu is not supported</i>', 'img' => MFN_OPTIONS_URI.'img/select/header/split-semi.png'),
			'below,split' => array('title' => 'Below Slider with Split Menu<br /><i>&#8226; Page Options: Custom Menu is not supported</i>', 'img' => MFN_OPTIONS_URI.'img/select/header/below-split.png'),
			'overlay,transparent'	=> array('title' => 'Overlay Menu<br /><i>&#8226; Menu has only 1 level<br />&#8226; Sticky Header affects only the menu button</i>', 'img' => MFN_OPTIONS_URI.'img/select/header/overlay.png'),
		);
	}
}

if( ! function_exists( 'mfna_bg_position' ) )
{
	/**
	 * Background Position
	 *
	 * @param string $body
	 * @return array
	 */

	function mfna_bg_position( $element = false ){
		$array = array(

			'no-repeat;left top;;' => __( 'Left Top | no-repeat', 'mfn-opts' ),
			'repeat;left top;;' => __( 'Left Top | repeat', 'mfn-opts' ),
			'no-repeat;left center;;' => __( 'Left Center | no-repeat', 'mfn-opts' ),
			'repeat;left center;;' => __( 'Left Center | repeat', 'mfn-opts' ),
			'no-repeat;left bottom;;' => __( 'Left Bottom | no-repeat', 'mfn-opts' ),
			'repeat;left bottom;;' => __( 'Left Bottom | repeat', 'mfn-opts' ),

			'no-repeat;center top;;' => __( 'Center Top | no-repeat', 'mfn-opts' ),
			'repeat;center top;;' => __( 'Center Top | repeat', 'mfn-opts' ),
			'repeat-x;center top;;' => __( 'Center Top | repeat-x', 'mfn-opts' ),
			'repeat-y;center top;;' => __( 'Center Top | repeat-y', 'mfn-opts' ),
			'no-repeat;center;;' => __( 'Center Center | no-repeat', 'mfn-opts' ),
			'repeat;center;;' => __( 'Center Center | repeat', 'mfn-opts' ),
			'no-repeat;center bottom;;' => __( 'Center Bottom | no-repeat', 'mfn-opts' ),
			'repeat;center bottom;;' => __( 'Center Bottom | repeat', 'mfn-opts' ),
			'repeat-x;center bottom;;' => __( 'Center Bottom | repeat-x', 'mfn-opts' ),
			'repeat-y;center bottom;;' => __( 'Center Bottom | repeat-y', 'mfn-opts' ),

			'no-repeat;right top;;' => __( 'Right Top | no-repeat', 'mfn-opts' ),
			'repeat;right top;;' => __( 'Right Top | repeat', 'mfn-opts' ),
			'no-repeat;right center;;' => __( 'Right Center | no-repeat', 'mfn-opts' ),
			'repeat;right center;;' => __( 'Right Center | repeat', 'mfn-opts' ),
			'no-repeat;right bottom;;' => __( 'Right Bottom | no-repeat', 'mfn-opts' ),
			'repeat;right bottom;;' => __( 'Right Bottom | repeat', 'mfn-opts' ),
		);

		if( $element == 'column' ){

			// Column
			// do NOT change: backward compatibility

		} elseif( $element == 'header' ){

			// Header

			$array['fixed'] = __( 'Center | no-repeat | fixed', 'mfn-opts' );
			$array['no-repeat;center;fixed;cover;still'] = __( 'Center | no-repeat | fixed | cover', 'mfn-opts' );
			$array['parallax'] = __( 'Parallax', 'mfn-opts' );

		} elseif( $element ){

			// Site Body | <html> tag

			$array['no-repeat;center top;fixed;;'] = __( 'Center | no-repeat | fixed', 'mfn-opts' );
			$array['no-repeat;center;fixed;cover'] = __( 'Center | no-repeat | fixed | cover', 'mfn-opts' );

		} else {

			// Section / Wrap

			$array['no-repeat;center top;fixed;;still'] = __( 'Center | no-repeat | fixed', 'mfn-opts' );
			$array['no-repeat;center;fixed;cover;still'] = __( 'Center | no-repeat | fixed | cover', 'mfn-opts' );
			$array['no-repeat;center top;fixed;cover'] = __( 'Parallax', 'mfn-opts' );

		}

		return $array;
	}
}

if( ! function_exists( 'mfna_bg_size' ) )
{
	/**
	 * Skin
	 *
	 * @return array
	 */

	function mfna_bg_size(){
		return array(
			'auto' => __('Auto', 'mfn-opts'),
			'contain' => __('Contain', 'mfn-opts'),
			'cover' => __('Cover', 'mfn-opts'),
			'cover-ultrawide'	=> __('Cover, on ultrawide screens only > 1920px', 'mfn-opts'),
		);
	}
}

if( ! function_exists( 'mfna_skin' ) )
{
	/**
	 * Skin
	 *
	 * @return array
	 */

	function mfna_skin(){
		return array(
			'custom' => __('- Custom Skin -', 'mfn-opts'),
			'one' => __('- One Color Skin -', 'mfn-opts'),
			'blue' => __('Blue', 'mfn-opts'),
			'brown' => __('Brown', 'mfn-opts'),
			'chocolate'	=> __('Chocolate', 'mfn-opts'),
			'gold' => __('Gold', 'mfn-opts'),
			'green' => __('Green', 'mfn-opts'),
			'olive' => __('Olive', 'mfn-opts'),
			'orange' => __('Orange', 'mfn-opts'),
			'pink' => __('Pink', 'mfn-opts'),
			'red' => __('Red', 'mfn-opts'),
			'sea' => __('Seagreen', 'mfn-opts'),
			'violet' => __('Violet', 'mfn-opts'),
			'yellow' => __('Yellow', 'mfn-opts'),
		);
	}
}

if( ! function_exists( 'mfna_utc' ) )
{
	/**
	 * UTC – Coordinated Universal Time
	 *
	 * @return array
	 */

	function mfna_utc(){
		return array(
			'-12' 	=> '-12:00',
			'-11' 	=> '-11:00 Pago Pago',
			'-10' 	=> '-10:00 Papeete, Honolulu',
			'-9.5' 	=> '-9:30',
			'-9' 		=> '-9:00 Anchorage',
			'-8' 		=> '-8:00 Los Angeles, Vancouver, Tijuana',
			'-7' 		=> '-7:00 Phoenix, Calgary, Ciudad Juárez',
			'-6' 		=> '-6:00 Chicago, Guatemala City, Mexico City, San José, San Salvador, Winnipeg',
			'-5' 		=> '-5:00 New York, Lima, Toronto, Bogotá, Havana, Kingston',
			'-4' 		=> '-4:00 Caracas, Santiago, La Paz, Manaus, Halifax, Santo Domingo',
			'-3.5' 	=> '-3:30 St. John\'s',
			'-3' 		=> '-3:00 Buenos Aires, Montevideo, São Paulo',
			'-2' 		=> '-2:00',
			'-1' 		=> '-1:00 Praia',
			'0' 		=> '±0:00 Accra, Casablanca, Dakar, Dublin, Lisbon, London',
			'+1' 		=> '+1:00 Berlin, Lagos, Madrid, Paris, Rome, Tunis, Vienna, Warsaw',
			'+2' 		=> '+2:00 Athens, Bucharest, Cairo, Helsinki, Jerusalem, Johannesburg, Kiev',
			'+3' 		=> '+3:00 Istanbul, Moscow, Nairobi, Baghdad, Doha, Minsk, Riyadh',
			'+3.5' 	=> '+3:30 Tehran',
			'+4' 		=> '+4:00 Baku, Dubai, Samara, Muscat',
			'+4.5'	=> '+4:30 Kabul',
			'+5' 		=> '+5:00 Karachi, Tashkent, Yekaterinburg',
			'+5.5' 	=> '+5:30 Delhi, Colombo',
			'+5.75'	=> '+5:45 Kathmandu',
			'+6' 		=> '+6:00 Almaty, Dhaka, Omsk',
			'+6.5' 	=> '+6:30 Yangon',
			'+7' 		=> '+7:00 Jakarta, Bangkok, Krasnoyarsk, Ho Chi Minh City',
			'+8' 		=> '+8:00 Beijing, Hong Kong, Taipei, Singapore, Kuala Lumpur, Perth, Manila, Denpasar, Irkutsk',
			'+8.5'	=> '+8:30 Pyongyang',
			'+8.75'	=> '+8:45',
			'+9' 		=> '+9:00 Seoul, Tokyo, Ambon, Yakutsk',
			'+9.5' 	=> '+9:30 Adelaide',
			'+10'		=> '+10:00 Port Moresby, Brisbane, Vladivostok, Sydney',
			'+10.5'	=> '+10:30',
			'+11' 	=> '+11:00 Nouméa',
			'+12' 	=> '+12:00 Auckland, Suva',
			'+12.75'=> '+12:45',
			'+13' 	=> '+13:00 Apia, Nukuʻalofa',
			'+14' 	=> '+14:00',
		);
	}
}

if( ! function_exists( 'mfna_layout' ) )
{
	/**
	 * Layouts
	 *
	 * @return array
	 */

	function mfna_layout(){
		$layouts = array( 0 => __( '-- Theme Options --', 'mfn-opts' ) );
		$args = array(
			'post_type' => 'layout',
			'posts_per_page'=> -1,
		);
		$lay = get_posts( $args );

		if( is_array( $lay ) ){
			foreach ( $lay as $v ){
				$layouts[$v->ID] = $v->post_title;
			}
		}

		return $layouts;
	}
}

if( ! function_exists( 'mfna_menu' ) )
{
	/**
	 * Menus
	 *
	 * @return array
	 */

	function mfna_menu(){
		$aMenus = array( 0 => __( '-- Default --', 'mfn-opts' ) );
		$oMenus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );

		if( is_array( $oMenus ) ){

			foreach( $oMenus as $menu ){
				$aMenus[ $menu->term_id ] = $menu->name;

				$term_trans_id = apply_filters( 'wpml_object_id', $menu->term_id, 'nav_menu', false );
				if( $term_trans_id != $menu->term_id ){
					unset( $aMenus[ $menu->term_id ] );
				}
			}
		}

		return $aMenus;
	}
}

/**
 * Options Page | Main Functions
 */

if( ! function_exists( 'mfn_opts_setup' ) )
{
	/**
	 * Options Page | Fields & Args
	 */

	function mfn_opts_setup(){

		global $MFN_Options;

		// Navigation elements =====

		$menu = array(

			// Global

			'global' => array(
				'title' 	=> __('Global', 'mfn-opts'),
				'sections' 	=> array( 'general', 'logo', 'sliders', 'advanced', 'hooks' ),
			),

			// Header & Subheader

			'header-subheader' => array(
				'title' 	=> __('Header & Subheader', 'mfn-opts'),
				'sections' 	=> array( 'header', 'subheader', 'extras' ),
			),

			// Menu & Action Bar

			'mab' => array(
				'title' 	=> __('Menu & Action Bar', 'mfn-opts'),
				'sections' 	=> array( 'menu', 'action-bar' ),
			),

			// Sidebars

			'sidebars' => array(
				'title' 	=> __('Sidebars', 'mfn-opts'),
				'sections' 	=> array( 'sidebars' ),
			),

			// Blog, Portfolio, Shop

			'bps' => array(
				'title' 	=> __('Blog, Portfolio & Shop', 'mfn-opts'),
				'sections' 	=> array( 'bps-general', 'blog', 'portfolio', 'shop', 'featured-image' ),
			),

			// Pages

			'pages' => array(
				'title' 	=> __('Pages', 'mfn-opts'),
				'sections' 	=> array( 'pages-general', 'pages-404', 'pages-under' ),
			),

			// Footer

			'footer' => array(
				'title' 	=> __('Footer', 'mfn-opts'),
				'sections' 	=> array( 'footer' ),
			),

			// Responsive

			'responsive' => array(
				'title' 	=> __('Responsive', 'mfn-opts'),
				'sections' 	=> array( 'responsive', 'responsive-header' ),
			),

			// SEO

			'seo' => array(
				'title' 	=> __('SEO', 'mfn-opts'),
				'sections' 	=> array( 'seo' ),
			),

			// Social

			'social' => array(
				'title' 	=> __('Social', 'mfn-opts'),
				'sections' 	=> array( 'social' ),
			),

			// Addons, Plugins

			'addons-plugins' => array(
				'title' 	=> __('Addons & Plugins', 'mfn-opts'),
				'sections' 	=> array( 'addons', 'plugins' ),
			),

			// Colors

			'colors' => array(
				'title' 	=> __('Colors', 'mfn-opts'),
				'sections' 	=> array( 'colors-general', 'colors-header', 'colors-menu', 'colors-action', 'content', 'colors-footer', 'colors-sliding-top', 'headings', 'colors-shortcodes', 'colors-forms' ),
			),

			// Fonts

			'font' => array(
				'title' 	=> __('Fonts', 'mfn-opts'),
				'sections' 	=> array( 'font-family', 'font-size', 'font-custom' ),
			),

			// Translate

			'translate' => array(
				'title' 	=> __('Translate', 'mfn-opts'),
				'sections'	=> array( 'translate-general', 'translate-blog', 'translate-404', 'translate-wpml' ),
			),

			// Custom CSS, JS

			'custom' => array(
				'title' 	=> __('Custom CSS & JS', 'mfn-opts'),
				'sections' 	=> array( 'css', 'js' ),
			),

		);

		$sections = array();

		// Global =====

		// General -----
		$sections['general'] = array(
			'title'		=> __('General', 'mfn-opts'),
			'fields' 	=> array(

				array(
					'id' 		=> 'general-info-layout',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Layout', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id'		=> 'layout',
					'type' 		=> 'radio_img',
					'title' 	=> __('Layout', 'mfn-opts'),
					'options' 	=> array(
						'full-width' 	=> array('title' => 'Full width', 	'img' => MFN_OPTIONS_URI.'img/select/style/full-width.png'),
						'boxed' 		=> array('title' => 'Boxed', 		'img' => MFN_OPTIONS_URI.'img/select/style/boxed.png'),
					),
					'std' 		=> 'full-width',
					'class'		=> 'wide',
				),

				array(
					'id' 		=> 'grid-width',
					'type' 		=> 'sliderbar',
					'title' 	=> __('Grid width', 'mfn-opts'),
					'sub_desc' 	=> __('default: 1240px', 'mfn-opts'),
					'desc' 		=> __('Works only with <b>Responsive ON</b>', 'mfn-opts'),
					'param'	 	=> array(
						'min' 		=> 960,
						'max' 		=> 1920,
					),
					'std' 		=> 1240,
				),

				array(
					'id'		=> 'style',
					'type' 		=> 'radio_img',
					'title' 	=> __('Style | Main', 'mfn-opts'),
					'options' 	=> array(
						'' 			=> array('title' => 'Classic', 	'img' => MFN_OPTIONS_URI .'img/select/style/default.png'),
						'simple' 	=> array('title' => 'Simple', 	'img' => MFN_OPTIONS_URI .'img/select/style/simple.png'),
					),
					'class'		=> 'wide',
				),

				array(
					'id' => 'button-style',
					'type' => 'radio_img',
					'title' => __('Style | Button', 'mfn-opts'),
					'options' => array(
						'' => array('title' => 'Default', 'img' => MFN_OPTIONS_URI.'img/select/button/classic.png'),
						'flat' => array('title' => 'Flat', 'img' => MFN_OPTIONS_URI.'img/select/button/flat.png'),
						'round' => array('title' => 'Round', 'img' => MFN_OPTIONS_URI.'img/select/button/round.png'),
						'stroke' => array('title' => 'Stroke', 'img' => MFN_OPTIONS_URI.'img/select/button/stroke.png'),
					),
					'class' => 'wide short',
				),

				array(
					'id' 		=> 'general-info-image-frame',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Image Frame', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'image-frame-style',
					'type' 		=> 'select',
					'title' 	=> __('Style', 'mfn-opts'),
					'options' 	=> array(
						'' 			=> __('Slide Bottom', 'mfn-opts'),
						'overlay' 	=> __('Overlay', 'mfn-opts'),
						'zoom' 		=> __('Zoom | without icons', 'mfn-opts'),
						'disable' 	=> __('Disable hover effect', 'mfn-opts'),
					),
				),

				array(
					'id' 		=> 'image-frame-border',
					'type' 		=> 'select',
					'title' 	=> __('Border', 'mfn-opts'),
					'desc' 		=> __('Border for <b>Image Item</b> can be set in Item Options', 'mfn-opts'),
					'options' 	=> array(
						'' 			=> __( 'Show', 'mfn-opts' ),
						'hide' 		=> __( 'Hide', 'mfn-opts' ),
					),
				),

				array(
					'id' 		=> 'image-frame-caption',
					'type' 		=> 'select',
					'title' 	=> __('Caption', 'mfn-opts'),
					'options' 	=> array(
						'' 			=> __( 'Below the Image', 'mfn-opts' ),
						'on' 		=> __( 'On the Image', 'mfn-opts' ),
					),
				),

				array(
					'id' 		=> 'general-info-background',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Background', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 			=> 'img-page-bg',
					'type' 		=> 'upload',
					'title' 	=> __( 'Image', 'mfn-opts' ),
					'desc' 		=> __( 'Recommended image size: <b>1920 x 1080 px</b>', 'mfn-opts' ),
				),

				array(
					'id' 		=> 'position-page-bg',
					'type' 		=> 'select',
					'title' 	=> __('Position', 'mfn-opts'),
					'desc' 		=> __('iOS does <b>not</b> support background-position: fixed', 'mfn-opts'),
					'options' 	=> mfna_bg_position(1),
					'std' 		=> 'center top no-repeat',
				),

				array(
					'id' 		=> 'size-page-bg',
					'type' 		=> 'select',
					'title' 	=> __('Size', 'mfn-opts'),
					'desc' 		=> __('Does <b>not</b> work with fixed position. Works only in modern browsers', 'mfn-opts'),
					'options' 	=> mfna_bg_size(),
				),

				array(
					'id' 		=> 'transparent',
					'type' 		=> 'checkbox',
					'title' 	=> __( 'Transparent', 'mfn-opts' ),
					'options' 	=> array(
						'header'	=> __( 'Header', 'mfn-opts' ),
						'menu'		=> __( 'Top Bar with menu <span>Does <b>not</b> work with Header Below.<br />Header Creative requires background image uploaded above.</span>', 'mfn-opts' ),
						'content'	=> __( 'Content', 'mfn-opts' ),
						'footer'	=> __( 'Footer', 'mfn-opts' ),
					),
				),

				array(
					'id' 		=> 'general-info-icon',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Icon', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id'			=> 'favicon-img',
					'type'		=> 'upload',
					'title'		=> __( 'Favicon', 'mfn-opts' ),
					'desc'		=> __( '<b>.ico</b> 32x32 pixels', 'mfn-opts' )
				),

				array(
					'id'			=> 'apple-touch-icon',
					'type'		=> 'upload',
					'title'		=> __( 'Apple Touch Icon', 'mfn-opts' ),
					'desc'		=> __( '<b>apple-touch-icon.png</b> 180x180 pixels', 'mfn-opts' )
				),

			),
		);

		// Logo ------
		$sections['logo'] = array(
			'title' 	=> __('Logo', 'mfn-opts'),
			'fields' 	=> array(

				// logo
				array(
					'id' 			=> 'logo-info',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __( 'Logo', 'mfn-opts' ),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id'			=> 'logo-img',
					'type'		=> 'upload',
					'title'		=> __( 'Logo', 'mfn-opts' ),
					'class'		=> 'mhb-opt',
				),

				array(
					'id'			=> 'retina-logo-img',
					'type'		=> 'upload',
					'title'		=> __( 'Retina Logo', 'mfn-opts' ),
					'sub_desc'=> __( 'optional', 'mfn-opts' ),
					'desc'		=> __( 'Retina Logo should be 2x larger than Custom Logo', 'mfn-opts' ),
					'class'		=> 'mhb-opt',
				),

				// sticky
				array(
					'id' 			=> 'logo-info-sticky',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __( 'Sticky Header Logo', 'mfn-opts' ),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id'			=> 'sticky-logo-img',
					'type'		=> 'upload',
					'title'		=> __( 'Logo', 'mfn-opts' ),
					'sub_desc'=> __( 'optional', 'mfn-opts' ),
					'desc'		=> __( 'Use if you want different logo for Sticky Header.<br />This is Tablet Logo for Creative Header', 'mfn-opts' ),
					'class'		=> 'mhb-opt',
				),

				array(
					'id'			=> 'sticky-retina-logo-img',
					'type'		=> 'upload',
					'title'		=> __( 'Retina Logo', 'mfn-opts' ),
					'sub_desc'=> __( 'optional', 'mfn-opts' ),
					'desc'		=> __( 'Retina Logo should be 2x larger than Sticky Header Logo', 'mfn-opts' ),
					'class'		=> 'mhb-opt',
				),

				// options
				array(
					'id' 		=> 'logo-info-options',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Options', 'mfn-opts'),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id' 		=> 'logo-link',
					'type' 		=> 'checkbox',
					'title' 	=> __('Options', 'mfn-opts'),
					'options' 	=> array(
						'link'		=> __('Link to Homepage', 'mfn-opts'),
						'h1-home'	=> __('Wrap into H1 tag on Homepage', 'mfn-opts'),
						'h1-all'	=> __('Wrap into H1 tag on All other pages', 'mfn-opts'),
					),
					'std'		=> array( 'link' => 'link' ),
					'class'		=> 'mhb-opt',
				),

				array(
					'id'		=> 'logo-text',
					'type'		=> 'text',
					'title'		=> __('Text Logo', 'mfn-opts'),
					'sub_desc'	=> __('optional', 'mfn-opts'),
					'desc'		=> __('Use text <b>instead</b> of graphic logo', 'mfn-opts'),
					'class'		=> 'small-text mhb-opt',
				),

				array(
					'id'		=> 'logo-width',
					'type'		=> 'text',
					'title'		=> __('SVG Logo Width', 'mfn-opts'),
					'sub_desc'	=> __('optional', 'mfn-opts'),
					'desc'		=> __('Use <b>only</b> with <b>svg</b> logo', 'mfn-opts'),
					'class'		=> 'small-text mhb-opt',
				),

				// advanced
				array(
					'id' 		=> 'logo-info-advanced',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Advanced', 'mfn-opts'),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id'		=> 'logo-height',
					'type'		=> 'text',
					'title'		=> __('Height', 'mfn-opts'),
					'sub_desc'	=> __('default: 60', 'mfn-opts'),
					'desc'		=> __('px<br />Minimum height + padding = 60px', 'mfn-opts'),
					'class'		=> 'small-text mhb-opt',
				),

				array(
					'id'		=> 'logo-vertical-padding',
					'type'		=> 'text',
					'title'		=> __('<small>Vertical</small> Padding', 'mfn-opts'),
					'sub_desc'	=> __('default: 15', 'mfn-opts'),
					'desc'		=> __('px', 'mfn-opts'),
					'class'		=> 'small-text mhb-opt',
				),

				array(
					'id' 			=> 'logo-vertical-align',
					'type' 		=> 'select',
					'title' 	=> __( '<small>Vertical</small> Align', 'mfn-opts' ),
					'options' => array(
						'top' 		=> __( 'Top', 'mfn-opts' ),
						''				=> __( 'Middle', 'mfn-opts' ),
						'bottom'	=> __( 'Bottom', 'mfn-opts' ),
					),
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 			=> 'logo-advanced',
					'type' 		=> 'checkbox',
					'title' 	=> __( 'Advanced', 'mfn-opts' ),
					'options' => array(
						'no-margin' => __( 'Remove Left margin<span>Top margin for Header Creative</span>', 'mfn-opts' ),
						'overflow' => __( 'Overflow Logo<span>For some header styles only</span>', 'mfn-opts' ),
						'no-sticky-padding' => __( 'Sticky Logo | Remove max-height & padding', 'mfn-opts' ),
					),
					'class'		=> 'mhb-opt',
				),

			),
		);

		// Sliders ------
		$sections['sliders'] = array(
			'title' 	=> __('Sliders', 'mfn-opts'),
			'icon' 		=> MFN_OPTIONS_URI. 'img/icons/sub.png',
			'fields'	=> array(

				array(
					'id' 		=> 'slider-blog-timeout',
					'type' 		=> 'text',
					'title' 	=> __('Blog', 'mfn-opts'),
					'sub_desc' 	=> __('Milliseconds between slide', 'mfn-opts'),
					'desc' 		=> __('<strong>0 to disable auto</strong> advance.<br />1000ms = 1s', 'mfn-opts'),
					'class'		=> 'small-text',
					'std' 		=> '0',
				),

				array(
					'id' 		=> 'slider-clients-timeout',
					'type' 		=> 'text',
					'title' 	=> __('Clients', 'mfn-opts'),
					'sub_desc' 	=> __('Milliseconds between slide', 'mfn-opts'),
					'desc' 		=> __('<strong>0 to disable auto</strong> advance.<br />1000ms = 1s', 'mfn-opts'),
					'class'		=> 'small-text',
					'std' 		=> '0',
				),

				array(
					'id' 		=> 'slider-offer-timeout',
					'type' 		=> 'text',
					'title' 	=> __('Offer', 'mfn-opts'),
					'sub_desc' 	=> __('Milliseconds between slide', 'mfn-opts'),
					'desc' 		=> __('<strong>0 to disable auto</strong> advance.<br />1000ms = 1s', 'mfn-opts'),
					'class'		=> 'small-text',
					'std' 		=> '0',
				),

				array(
					'id' 		=> 'slider-portfolio-timeout',
					'type' 		=> 'text',
					'title' 	=> __('Portfolio', 'mfn-opts'),
					'sub_desc' 	=> __('Milliseconds between slide', 'mfn-opts'),
					'desc' 		=> __('<strong>0 to disable auto</strong> advance.<br />1000ms = 1s', 'mfn-opts'),
					'class'		=> 'small-text',
					'std' 		=> '0',
				),

				array(
					'id' 		=> 'slider-shop-timeout',
					'type' 		=> 'text',
					'title' 	=> __('Shop', 'mfn-opts'),
					'sub_desc' 	=> __('Milliseconds between slide', 'mfn-opts'),
					'desc' 		=> __('<strong>0 to disable auto</strong> advance.<br />1000ms = 1s', 'mfn-opts'),
					'class'		=> 'small-text',
					'std' 		=> '0',
				),

				array(
					'id' 		=> 'slider-slider-timeout',
					'type' 		=> 'text',
					'title' 	=> __('Slider', 'mfn-opts'),
					'sub_desc' 	=> __('Milliseconds between slide', 'mfn-opts'),
					'desc' 		=> __('<strong>0 to disable auto</strong> advance.<br />1000ms = 1s', 'mfn-opts'),
					'class'		=> 'small-text',
					'std' 		=> '0',
				),

				array(
					'id' 		=> 'slider-testimonials-timeout',
					'type' 		=> 'text',
					'title' 	=> __('Testimonials', 'mfn-opts'),
					'sub_desc' 	=> __('Milliseconds between slide', 'mfn-opts'),
					'desc' 		=> __('<strong>0 to disable auto</strong> advance.<br />1000ms = 1s', 'mfn-opts'),
					'class'		=> 'small-text',
					'std' 		=> '0',
				),

			),
		);

		// Advanced -----

		$sections['advanced'] = array(
			'title' => __('Advanced', 'mfn-opts'),
			'icon' => MFN_OPTIONS_URI. 'img/icons/sub.png',
			'fields' => array(

				// layout

				array(
					'id' => 'advanced-info-layout',
					'type' => 'info',
					'title' => '',
					'desc' => __('Layout', 'mfn-opts'),
					'class' => 'mfn-info',
				),

				array(
					'id' => 'layout-boxed-padding',
					'type' => 'text',
					'title' => __('Boxed Layout | Side padding', 'mfn-opts'),
					'desc' => __('Use value with <b>px</b> or <b>%</b><br/>Example: <b>10px</b> or <b>2%</b>', 'mfn-opts'),
					'class' => 'small-text',
				),

				array(
					'id' => 'builder-visibility',
					'type' => 'select',
					'title' => __( 'Builder | Visibility', 'mfn-opts' ),
					'options' => array(
						'' => __( '-- Everyone --', 'mfn-opts' ),
						'publish_posts' => __( 'Author', 'mfn-opts' ),
						'edit_pages' => __( 'Editor', 'mfn-opts' ),
						'edit_theme_options' => __( 'Administrator', 'mfn-opts' ),
						'hide' => __( 'HIDE for Everyone', 'mfn-opts' ),
					),
				),

				array(
					'id' => 'display-order',
					'type' => 'select',
					'title' => __( 'Content | Display Order', 'mfn-opts' ),
					'options' => array(
						0 => __( 'Muffin Builder - WordPress Editor', 'mfn-opts' ),
						1 => __( 'WordPress Editor - Muffin Builder', 'mfn-opts' ),
					),
				),

				array(
					'id' => 'content-remove-padding',
					'type' => 'switch',
					'title' => __('Content | Remove Padding', 'mfn-opts'),
					'desc' => __('Remove default Content Padding Top for <b>all</b> pages/posts', 'mfn-opts'),
					'options' => array( '0' => 'Off', '1' => 'On' ),
					'std' => '0'
				),

				array(
					'id' => 'no-hover',
					'type' => 'select',
					'title' => __('Hover Effects', 'mfn-opts'),
					'options' => array(
						'' => __('Enable', 'mfn-opts'),
						'tablet' => __('Enable on desktop only', 'mfn-opts'),
						'all' => __('Disable', 'mfn-opts'),
					),
				),

				// options

				array(
					'id' => 'advanced-info-options',
					'type' => 'info',
					'title' => '',
					'desc' => __('Options', 'mfn-opts'),
					'class' => 'mfn-info',
				),

				array(
					'id' => 'google-maps-api-key',
					'type' => 'text',
					'title' => __( 'Google Maps API Key', 'mfn-opts' ),
					'desc' => __( 'Google Maps API key is required for Map Basic Embed or Map Advanced. If you do not have the key please visit <a target="_blank" href="https://cloud.google.com/maps-platform/">Google Maps Platform</a>', 'mfn-opts'),
				),

				array(
					'id' => 'table-hover',
					'type' => 'select',
					'title' => __('HTML Table', 'mfn-opts'),
					'options' => array(
						'' => __('Default', 'mfn-opts'),
						'hover' => __('Rows Hover', 'mfn-opts'),
						'responsive' => __('Auto Responsive', 'mfn-opts'),
					),
				),

				array(
					'id' => 'math-animations-disable',
					'type' => 'switch',
					'title' => __('Math Animate | Disable', 'mfn-opts'),
					'sub_desc' => __('Disable animations for Counter, Quick fact', 'mfn-opts'),
					'options' => array( '0' => 'Off', '1' => 'On' ),
					'std' => '0'
				),

				array(
					'id' => 'layout-options',
					'type' => 'checkbox',
					'title' => __('Other', 'mfn-opts'),
					'options' => array(
						'no-shadows' => __('Remove shadows<span>Boxed Layout, Creative Header, Sticky Header, Subheader, etc.</span>', 'mfn-opts'),
						'boxed-no-margin' => __('Boxed Layout: Remove margin<span>Remove top and bottom margin for Layout: Boxed</span>', 'mfn-opts'),
					),
				),

				// theme functions

				array(
					'id' 		=> 'advanced-info-functions',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Theme Functions', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'post-type-disable',
					'type' 		=> 'checkbox',
					'title' 	=> __('Post Type | Disable', 'mfn-opts'),
					'desc' 		=> __('If you do not want to use any of these Types, you can disable it', 'mfn-opts'),
					'options' 	=> array(
						'client'		=> __('Clients', 'mfn-opts'),
						'layout'		=> __('Layouts', 'mfn-opts'),
						'offer'			=> __('Offer', 'mfn-opts'),
						'portfolio'		=> __('Portfolio', 'mfn-opts'),
						'slide'			=> __('Slides', 'mfn-opts'),
						'template'		=> __('Templates', 'mfn-opts'),
						'testimonial'	=> __('Testimonials', 'mfn-opts'),
					),
				),

				array(
					'id' 		=> 'theme-disable',
					'type' 		=> 'checkbox',
					'title' 	=> __('Theme Functions | Disable', 'mfn-opts'),
					'desc' 		=> __('If you do not want to use any of these functions or use external plugins to do the same, you can disable it', 'mfn-opts'),
					'options' 	=> array(
						'demo-data'				=> __('BeTheme pre-built websites', 'mfn-opts'),
						'categories-sidebars'	=> __('Categories Sidebars<span>This option affects existing sidebars. Please use before adding widgets</span>', 'mfn-opts'),
						'entrance-animations'	=> __('Entrance Animations', 'mfn-opts'),
						'mega-menu'				=> __('Mega Menu', 'mfn-opts'),
					),
				),

				// advanced
				array(
					'id' 		=> 'advanced-info-advanced',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Advanced', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' => 'static-css',
					'type' => 'switch',
					'title' => __('Static CSS', 'mfn-opts'),
					'sub_desc' => __('Static CSS file generation', 'mfn-opts'),
					'desc' => __('Some changes in Theme Options are saved as CSS and inserted into the head of your website. You can enable the static CSS option and make them a separate file that will create itself, update, and minify each time you save Theme Options.', 'mfn-opts'),
					'options' => array('0' => 'Off', '1' => 'On'),
					'std' => '0'
				),

				array(
					'id' 		=> 'builder-storage',
					'type' 		=> 'select',
					'title' 	=> __('Builder | Data Storage', 'mfn-opts'),
					'desc' 		=> __('This option will <b>not</b> affect the existing pages, only newly created or updated', 'mfn-opts'),
					'options' 	=> array(
						'' 			=> __('Serialized | Readable format, required by some plugins', 'mfn-opts'),
						'non-utf-8' => __('Serialized (safe mode) | Readable format, for non-UTF-8 server, etc.', 'mfn-opts'),
						'encode'	=> __('Encoded | Less data stored, compatible with WordPress Importer', 'mfn-opts'),
					),
				),

				array(
					'id' 		=> 'slider-shortcode',
					'type' 		=> 'text',
					'title' 	=> __('Slider | Shortcode', 'mfn-opts'),
					'sub_desc' 	=> __('Use this option to force slider for <b>all</b> pages', 'mfn-opts'),
					'desc' 		=> __('This option can <strong>not</strong> be overwritten and it is usefull for people who already have many pages and want to standardize their appearance.<br/>eg. [rev_slider alias="slider1"]', 'mfn-opts'),
				),

				array(
					'id' 		=> 'table_prefix',
					'type' 		=> 'select',
					'title' 	=> __('Table Prefix', 'mfn-opts'),
					'desc' 		=> __('For some <b>multisite</b> installations it is necessary to change table prefix to get Sliders List in Page Options. Please do <b>not</b> change if everything works.', 'mfn-opts'),
					'options' 	=> array(
						'base_prefix' 	=> 'base_prefix',
						'prefix' 		=> 'prefix',
					),
				),

			),
		);

		// Hooks ------
		$sections['hooks'] = array(
			'title' 	=> __('Hooks', 'mfn-opts'),
			'icon' 		=> MFN_OPTIONS_URI. 'img/icons/sub.png',
			'fields'	=> array(

				array(
					'id' 		=> 'hook-top',
					'type' 		=> 'textarea',
					'title' 	=> __('Top', 'mfn-opts'),
					'sub_desc'	=> __('mfn_hook_top', 'mfn-opts'),
					'desc' 		=> __('Executes <b>after</b> the opening <b>&lt;body&gt;</b> tag', 'mfn-opts'),
				),

				array(
					'id' 		=> 'hook-content-before',
					'type' 		=> 'textarea',
					'title' 	=> __('Content before', 'mfn-opts'),
					'sub_desc'	=> __('mfn_hook_content_before', 'mfn-opts'),
					'desc' 		=> __('Executes <b>before</b> the opening <b>&lt;#Content&gt;</b> tag', 'mfn-opts'),
				),

				array(
					'id' 		=> 'hook-content-after',
					'type' 		=> 'textarea',
					'title' 	=> __('Content after', 'mfn-opts'),
					'sub_desc'	=> __('mfn_hook_content_after', 'mfn-opts'),
					'desc' 		=> __('Executes <b>after</b> the closing <b>&lt;/#Content&gt;</b> tag', 'mfn-opts'),
				),

				array(
					'id' 		=> 'hook-bottom',
					'type' 		=> 'textarea',
					'title' 	=> __('Bottom', 'mfn-opts'),
					'sub_desc'	=> __('mfn_hook_bottom', 'mfn-opts'),
					'desc' 		=> __('Executes <b>before</b> the closing <b>&lt;/body&gt;</b> tag', 'mfn-opts'),
				),

			),
		);

		// Header, Subheader =====

		// Header ------
		$sections['header'] = array(
			'title' => __('Header', 'mfn-opts'),
			'fields' => array(

				// layout
				array(
					'id' 		=> 'header-info-layout',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Layout', 'mfn-opts'),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id' 			=> 'header-style',
					'type' 		=> 'radio_img',
					'title' 	=> __( 'Style', 'mfn-opts' ),
					'options'	=> mfna_header_style(),
					'class'		=> 'wide mhb-opt',
					'std'			=> 'classic',
				),

				array(
					'id'		=> 'header-fw',
					'type' 		=> 'checkbox',
					'title' 	=> __('Options', 'mfn-opts'),
					'options' 	=> array(
						'full-width'	=> __('Full Width (for layout: Full Width)', 'mfn-opts'),
						'header-boxed'	=> __('Boxed Sticky Header (for layout: Boxed)', 'mfn-opts'),
					),
					'class'		=> 'mhb-opt',
				),

				array(
					'id'		=> 'minimalist-header',
					'type'		=> 'select',
					'title'		=> __('Minimalist', 'mfn-opts'),
					'desc'		=> __('Header without background image & padding', 'mfn-opts'),
					'options'	=> array(
						'0' 		=> __('Default | OFF', 'mfn-opts'),
						'1' 		=> __('Minimalist | ON', 'mfn-opts'),
						'no' 		=> __('Minimalist without Header space', 'mfn-opts'),
					),
					'class'		=> 'mhb-opt',
				),

				// background
				array(
					'id' 		=> 'header-info-background',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Background', 'mfn-opts'),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id' 			=> 'img-subheader-bg',
					'type' 		=> 'upload',
					'title' 	=> __( 'Image', 'mfn-opts' ),
					'desc' 		=> __( 'Pages without slider. May be overwritten for single page.<br />Recommended image width: <b>1920px</b>', 'mfn-opts' ),
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 			=> 'img-subheader-attachment',
					'type' 		=> 'select',
					'title' 	=> __( 'Position', 'mfn-opts' ),
					'desc' 		=> __( 'iOS does <b>not</b> support background-position: fixed', 'mfn-opts' ),
					'options'	=> mfna_bg_position( 'header' ),
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 		=> 'size-subheader-bg',
					'type' 		=> 'select',
					'title' 	=> __('Size', 'mfn-opts'),
					'desc' 		=> __('Does <b>not</b> work with fixed position & parallax. Works only in modern browsers', 'mfn-opts'),
					'options' 	=> mfna_bg_size(),
					'class'		=> 'mhb-opt',
				),

				// top bar background
				array(
					'id' 			=> 'top-bar-info-background',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __( 'Top Bar background <span>also Header Creative background</span>', 'mfn-opts' ),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id' 			=> 'top-bar-bg-img',
					'type' 		=> 'upload',
					'title' 	=> __( 'Image', 'mfn-opts' ),
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 			=> 'top-bar-bg-position',
					'type' 		=> 'select',
					'title' 	=> __( 'Position', 'mfn-opts' ),
					'desc' 		=> __( 'iOS does <b>not</b> support background-position: fixed', 'mfn-opts' ),
					'options'	=> mfna_bg_position(),
					'class'		=> 'mhb-opt',
				),

				// sticky header
				array(
					'id' 		=> 'header-info-sticky',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Sticky Header', 'mfn-opts'),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id'			=> 'sticky-header',
					'type'		=> 'switch',
					'title'		=> __( 'Sticky', 'mfn-opts' ),
					'options'	=> array( '1' => 'On', '0' => 'Off' ),
					'std'			=> '1',
					'class'		=> 'mhb-opt',
				),

				array(
					'id'			=> 'sticky-header-style',
					'type'		=> 'select',
					'title'		=> __( 'Style', 'mfn-opts' ),
					'options'	=> array(
						'tb-color'	=> __( 'The same as Top Bar Left background', 'mfn-opts' ),
						'white'			=> __( 'White', 'mfn-opts' ),
						'dark'			=> __( 'Dark', 'mfn-opts' ),
					),
					'class'		=> 'mhb-opt',
				),

			),
		);

		// Subheader ------
		$sections['subheader'] = array(
			'title' => __('Subheader', 'mfn-opts'),
			'fields' => array(

				array(
					'id' 		=> 'subheader-info-layout',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Layout', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id'		=> 'subheader-style',
					'type'		=> 'select',
					'title'		=> __('Style', 'mfn-opts'),
					'options'	=> array(
						'both-center' 	=> __('Title & Breadcrumbs Centered', 'mfn-opts'),
						'both-left' 	=> __('Title & Breadcrumbs on the Left', 'mfn-opts'),
						'both-right' 	=> __('Title & Breadcrumbs on the Right', 'mfn-opts'),
						'' 				=> __('Title on the Left', 'mfn-opts'),
						'title-right' 	=> __('Title on the Right', 'mfn-opts'),
					),
					'std'		=> 'both-center',
				),

				array(
					'id'		=> 'subheader',
					'type' 		=> 'checkbox',
					'title' 	=> __('Hide', 'mfn-opts'),
					'options' 	=> array(
						'hide-breadcrumbs'	=> __('Breadcrumbs', 'mfn-opts'),
						'hide-title'		=> __('Page Title', 'mfn-opts'),
						'hide-subheader'	=> __('<b>Subheader</b>', 'mfn-opts'),
					),
				),

				array(
					'id' 		=> 'subheader-padding',
					'type' 		=> 'text',
					'title' 	=> __('Padding', 'mfn-opts'),
					'sub_desc' 	=> __('default: 30px 0', 'mfn-opts'),
					'desc' 		=> __('Use value with <b>px</b> or <b>em</b><br />Example: <b>20px 0</b> or <b>20px 0 30px 0</b> or <b>2em 0</b>', 'mfn-opts'),
					'class' 	=> 'small-text',
				),

				array(
					'id'		=> 'subheader-title-tag',
					'type' 		=> 'select',
					'title' 	=> __('Title tag', 'mfn-opts'),
					'options' 	=> array(
						'h1'	=> 'H1',
						'h2'	=> 'H2',
						'h3'	=> 'H3',
						'h4'	=> 'H4',
						'h5'	=> 'H5',
						'h6'	=> 'H6',
						'span'	=> 'span',
					),
				),

				array(
					'id' 		=> 'subheader-info-background',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Background', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'subheader-image',
					'type' 		=> 'upload',
					'title' 	=> __( 'Image', 'mfn-opts' ),
					'desc' 		=> __( 'Recommended image width: <b>1920px</b>', 'mfn-opts' ),
				),

				array(
					'id' 		=> 'subheader-position',
					'type' 		=> 'select',
					'title' 	=> __('Position', 'mfn-opts'),
					'desc' 		=> __('iOS does <b>not</b> support background-position: fixed', 'mfn-opts'),
					'options' 	=> mfna_bg_position(1),
					'std' 		=> 'center top no-repeat',
				),

				array(
					'id' 		=> 'subheader-size',
					'type' 		=> 'select',
					'title' 	=> __('Size', 'mfn-opts'),
					'desc' 		=> __('Does <b>not</b> work with fixed position. Works only in modern browsers', 'mfn-opts'),
					'options' 	=> mfna_bg_size(),
				),

				array(
					'id' 		=> 'subheader-transparent',
					'type' 		=> 'sliderbar',
					'title' 	=> __('Transparency (alpha)', 'mfn-opts'),
					'sub_desc' 	=> __('0 = transparent, 100 = solid', 'mfn-opts'),
					'desc' 		=> __('<b>Important:</b> This option can be used only with <b>Custom</b> or <b>One Color Skin</b>', 'mfn-opts'),
					'param'	 	=> array(
						'min' 		=> 0,
						'max' 		=> 100,
					),
					'std' 		=> '100',
				),

				array(
					'id' 		=> 'subheader-info-advanced',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Advanced', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id'		=> 'subheader-advanced',
					'type' 		=> 'checkbox',
					'title' 	=> __('Options', 'mfn-opts'),
					'options' 	=> array(
						'breadcrumbs-link'	=> __('Breadcrumbs | Last item is link (NOT for Shop)', 'mfn-opts'),
						'slider-show'		=> __('Slider | Show subheader on pages with Slider', 'mfn-opts'),
					),
				),

			),
		);

		// Extras ------
		$sections['extras'] = array(
			'title' => __( 'Extras', 'mfn-opts' ),
			'fields' => array(

				array(
					'id' 			=> 'extras-info-top-bar-right',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __( 'Top Bar Right', 'mfn-opts' ),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id'			=> 'top-bar-right-hide',
					'type'		=> 'switch',
					'title'		=> __( 'Hide', 'mfn-opts' ),
					'options'	=> array( '0' => 'Off', '1' => 'On' ),
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 			=> 'extras-info-action-button',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __( 'Action Button', 'mfn-opts' ),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id'		=> 'header-action-title',
					'type'		=> 'text',
					'title'		=> __('Title', 'mfn-opts'),
					'class'		=> 'small-text mhb-opt',
				),

				array(
					'id'		=> 'header-action-link',
					'type'		=> 'text',
					'title'		=> __('Link', 'mfn-opts'),
					'class'		=> 'mhb-opt',
				),

				array(
					'id'		=> 'header-action-target',
					'type' 		=> 'checkbox',
					'title' 	=> __('Options', 'mfn-opts'),
					'options' 	=> array(
						'target'	=> __('Open in new window', 'mfn-opts'),
						'scroll'	=> __('Scroll to section (use #SectionID as Link)', 'mfn-opts'),
					),
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 		=> 'extras-info-search',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Search', 'mfn-opts'),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id'		=> 'header-search',
					'type'		=> 'select',
					'title'		=> __('Search', 'mfn-opts'),
					'options'	=> array(
						'1' 		=> __('Icon | Default', 'mfn-opts'),
						'shop' 		=> __('Icon | Search Shop Products only', 'mfn-opts'),
						'input' 	=> __('Search Field', 'mfn-opts'),
						'0' 		=> __('Hide', 'mfn-opts'),
					),
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 		=> 'extras-info-wpml',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('WPML', 'mfn-opts'),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id'		=> 'header-wpml',
					'type'		=> 'select',
					'title'		=> __('Custom Switcher Style', 'mfn-opts'),
					'desc'		=> __('Custom Language Switcher is independent of WPML switcher options', 'mfn-opts'),
					'options'	=> array(
						''					=> __('Dropdown | Flags', 'mfn-opts'),
						'dropdown-name'		=> __('Dropdown | Language Name (native)', 'mfn-opts'),
						'horizontal'		=> __('Horizontal | Flags', 'mfn-opts'),
						'horizontal-code'	=> __('Horizontal | Language Code', 'mfn-opts'),
						'hide'				=> __('Hide', 'mfn-opts'),
					),
					'class'		=> 'mhb-opt',
				),

				array(
					'id'		=> 'header-wpml-options',
					'type' 		=> 'checkbox',
					'title'		=> __('Custom Switcher Options', 'mfn-opts'),
					'options' 	=> array(
						'link-to-home'	=> __('Link to home of language for missing translations<span>Disable this option to skip languages with missing translation</span>', 'mfn-opts'),
					),
					'class'		=> 'mhb-opt',
				),

				// other
				array(
					'id' 			=> 'extras-info-other',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __( 'Other', 'mfn-opts' ),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id' 			=> 'header-banner',
					'type' 		=> 'textarea',
					'title' 	=> __( 'Banner', 'mfn-opts' ),
					'sub_desc'=> __( 'Header Magazine (468px x 60px) or Creative (250px x 250px) Banner code ', 'mfn-opts' ),
					'desc' 		=> '&lt;a href="#" target="_blank"&gt;&lt;img src="" /&gt;&lt;/a&gt;',
					'class'		=> 'mhb-opt',
				),

				// sliding top
				array(
					'id' 			=> 'extras-info-sliding-top',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __( 'Sliding Top', 'mfn-opts' ),
					'class' 	=> 'mfn-info',
				),

				array(
					'id'			=> 'sliding-top',
					'type'		=> 'select',
					'title'		=> __( 'Sliding Top', 'mfn-opts' ),
					'desc'		=> __( 'Widgetized Sliding Top position', 'mfn-opts' ),
					'options'	=> array(
						'1' 			=> __( 'Right', 'mfn-opts' ),
						'center' 	=> __( 'Center', 'mfn-opts' ),
						'left' 		=> __( 'Left', 'mfn-opts' ),
						'0' 			=> __( 'Hide', 'mfn-opts' ),
					),
					'std'			=> '0',
				),

				array(
					'id'			=> 'sliding-top-icon',
					'type'		=> 'icon',
					'title'		=> __( 'Icon', 'mfn-opts' ),
					'std'			=> 'icon-down-open-mini',
				),

			),
		);

		// Menu, Action Bar ======

		// Menu ------
		$sections['menu'] = array(
			'title' => __('Menu', 'mfn-opts'),
			'fields' => array(

				array(
					'id' 		=> 'menu-info-layout',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Layout', 'mfn-opts'),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id'		=> 'menu-style',
					'type'		=> 'select',
					'title'		=> __('Style', 'mfn-opts'),
					'desc'		=> __('For some header style only', 'mfn-opts'),
					'options'	=> array(
						'link-color'		=> __('Link color only', 'mfn-opts'),
						''					=> __('Line above Menu', 'mfn-opts'),
						'line-below'		=> __('Line below Menu', 'mfn-opts'),
						'line-below-80'		=> __('Line below Link (80% width)', 'mfn-opts'),
						'line-below-80-1'	=> __('Line below Link (80% width, 1px height)', 'mfn-opts'),
						'arrow-top'			=> __('Arrow Top', 'mfn-opts'),
						'arrow-bottom'		=> __('Arrow Bottom', 'mfn-opts'),
						'highlight'			=> __('Highlight', 'mfn-opts'),
						'hide'				=> __('HIDE Menu', 'mfn-opts'),
					),
					'std'		=> 'link-color',
					'class'		=> 'mhb-opt',
				),

				array(
					'id'			=> 'menu-options',
					'type' 		=> 'checkbox',
					'title' 	=> __( 'Options', 'mfn-opts' ),
					'options' => array(
						'align-right'			=> __( 'Align Right', 'mfn-opts' ),
						'menu-arrows'			=> __( 'Arrows for Items with Submenu', 'mfn-opts' ),
						'hide-borders'		=> __( 'Hide Border between Items', 'mfn-opts' ),
						'submenu-active'	=> __( 'Submenu | Add active', 'mfn-opts' ),
						// 'submenu-limit'		=> __( 'Submenu | Limit width', 'mfn-opts' ),
						'last'						=> __( 'Submenu | Fold last 2 to the left<span>for Header Creative: fold to top</span>', 'mfn-opts' ),
					),
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 		=> 'menu-info-creative',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Creative <span>for creative header only</span>', 'mfn-opts'),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id'		=> 'menu-creative-options',
					'type' 		=> 'checkbox',
					'title' 	=> __('Options', 'mfn-opts'),
					'options' 	=> array(
						'scroll'	=> __('Scrollable <span>for menu with large amount of items <b>without submenus</b></span>', 'mfn-opts'),
						'dropdown'	=> __('Dropdown submenu <span>use with scrollable</span>', 'mfn-opts'),
					),
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 		=> 'menu-info-mega',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Mega Menu', 'mfn-opts'),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id'		=> 'menu-mega-style',
					'type'		=> 'select',
					'title'		=> __('Style', 'mfn-opts'),
					'options'	=> array(
						''			=> __('Default', 'mfn-opts'),
						'vertical'	=> __('Vertical Lines', 'mfn-opts'),
					),
					'class'		=> 'mhb-opt',
				),

			),
		);

		// Action Bar ------
		$sections['action-bar'] = array(
			'title' => __('Action Bar', 'mfn-opts'),
			'fields' => array(

				array(
					'id' => 'action-bar',
					'type' => 'checkbox',
					'title' => __('Action Bar', 'mfn-opts'),
					'options' => array(
						'show' => __('<b>Show</b> above the header<span>for most header styles</span>', 'mfn-opts'),
						'creative' => __('Creative Header <span>show at the bottom</span>', 'mfn-opts'),
						'side-slide' => __('Side Slide responsive menu <span>show at the bottom</span>', 'mfn-opts'),
					),
					'class'		=> 'mhb-opt',
				),

				array(
					'id'		=> 'header-slogan',
					'type'		=> 'text',
					'title'		=> __('Slogan', 'mfn-opts'),
					'class'		=> 'small-text mhb-opt',
				),

				array(
					'id'		=> 'header-phone',
					'type'		=> 'text',
					'title'		=> __('Phone', 'mfn-opts'),
					'sub_desc'	=> __('Phone number', 'mfn-opts'),
					'class'		=> 'small-text mhb-opt',
				),

				array(
					'id'		=> 'header-phone-2',
					'type'		=> 'text',
					'title'		=> __('2nd Phone', 'mfn-opts'),
					'sub_desc'	=> __('Additional Phone number', 'mfn-opts'),
					'class'		=> 'small-text mhb-opt',
				),

				array(
					'id'		=> 'header-email',
					'type'		=> 'text',
					'title'		=> __('Email', 'mfn-opts'),
					'sub_desc'	=> __('Email address', 'mfn-opts'),
					'class'		=> 'small-text mhb-opt',
				),

			),
		);

		// Sidebars ==============

		// Sidebars ------
		$sections['sidebars'] = array(
			'title' => __('General', 'mfn-opts'),
			'icon' => MFN_OPTIONS_URI. 'img/icons/sub.png',
			'fields' => array(

				array(
					'id' 		=> 'sidebar-info-sidebars',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Sidebars', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'sidebars',
					'type' 		=> 'multi_text',
					'title' 	=> __('Sidebars', 'mfn-opts'),
					'sub_desc' 	=> __('Manage custom sidebars', 'mfn-opts'),
					'desc' 		=> __('Do <b>not</b> use <b> special characters</b> or the following names: <em>buddy, events, forum, shop</em>', 'mfn-opts'),
				),

				array(
					'id' => 'sidebar-info-layout',
					'type' => 'info',
					'title' => '',
					'desc' => __('Layout', 'mfn-opts'),
					'class' => 'mfn-info',
				),

				array(
					'id' => 'sidebar-sticky',
					'type' => 'switch',
					'title' => __('Sticky', 'mfn-opts'),
					'options' => array('0' => 'Off','1' => 'On'),
				),

				array(
					'id' => 'sidebar-width',
					'type' => 'sliderbar',
					'title' => __('Width', 'mfn-opts'),
					'sub_desc' => __('default: 23%', 'mfn-opts'),
					'desc' => __('Recommended: 20%-30%. Too small or too large value may crash the layout', 'mfn-opts'),
					'param' => array(
						'min' => 10,
						'max' => 50,
					),
					'std' => '23',
				),

				array(
					'id' => 'sidebar-lines',
					'type' => 'select',
					'title' => __('Lines', 'mfn-opts'),
					'sub_desc' => __('Sidebar Lines Style', 'mfn-opts'),
					'options' => array(
						'' => __('Default', 'mfn-opts'),
						'lines-boxed' => __('Sidebar Width', 'mfn-opts'),
						'lines-hidden' => __('Hide Lines', 'mfn-opts'),
					),
					'std' => '',
				),

				array(
					'id' 		=> 'sidebar-info-page',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Pages <span>force sidebar</span>', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'single-page-layout',
					'type' 		=> 'radio_img',
					'title' 	=> __('Layout', 'mfn-opts'),
					'sub_desc' 	=> __('Use this option to force layout for all pages', 'mfn-opts'),
					'desc' 		=> __('This option can <strong>not</strong> be overwritten and it is usefull for people who already have many pages and want to standardize their appearance.', 'mfn-opts'),
					'options' 	=> array(
						'' 				=> array('title' => 'Use Page Meta', 'img' => MFN_OPTIONS_URI.'img/question.png'),
						'no-sidebar' 	=> array('title' => 'Full width without sidebar', 'img' => MFN_OPTIONS_URI.'img/1col.png'),
						'left-sidebar'	=> array('title' => 'Left Sidebar', 'img' => MFN_OPTIONS_URI.'img/2cl.png'),
						'right-sidebar'	=> array('title' => 'Right Sidebar', 'img' => MFN_OPTIONS_URI.'img/2cr.png'),
						'both-sidebars' => array('title' => 'Both Sidebars', 'img' => MFN_OPTIONS_URI.'img/2sb.png'),
					),
				),

				array(
					'id' 		=> 'single-page-sidebar',
					'type' 		=> 'text',
					'title' 	=> __('Sidebar', 'mfn-opts'),
					'sub_desc' 	=> __('Use this option to force sidebar for all pages', 'mfn-opts'),
					'desc' 		=> __('Paste the name of one of the sidebars that you added in the "Sidebars" section.', 'mfn-opts'),
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'single-page-sidebar2',
					'type' 		=> 'text',
					'title' 	=> __('Sidebar 2', 'mfn-opts'),
					'sub_desc' 	=> __('Use this option to force sidebar for all pages', 'mfn-opts'),
					'desc' 		=> __('Paste the name of one of the sidebars that you added in the "Sidebars" section.', 'mfn-opts'),
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'sidebar-info-post',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Single Posts <span>force sidebar</span>', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'single-layout',
					'type' 		=> 'radio_img',
					'title' 	=> __('Layout', 'mfn-opts'),
					'sub_desc' 	=> __('Use this option to force layout for all posts', 'mfn-opts'),
					'desc' 		=> __('This option can <strong>not</strong> be overwritten and it is usefull for people who already have many posts and want to standardize their appearance.', 'mfn-opts'),
					'options' 	=> array(
						'' 				=> array('title' => 'Use Post Meta', 'img' => MFN_OPTIONS_URI.'img/question.png'),
						'no-sidebar' 	=> array('title' => 'Full width without sidebar', 'img' => MFN_OPTIONS_URI.'img/1col.png'),
						'left-sidebar'	=> array('title' => 'Left Sidebar', 'img' => MFN_OPTIONS_URI.'img/2cl.png'),
						'right-sidebar'	=> array('title' => 'Right Sidebar', 'img' => MFN_OPTIONS_URI.'img/2cr.png'),
						'both-sidebars' => array('title' => 'Both Sidebars', 'img' => MFN_OPTIONS_URI.'img/2sb.png'),
					),
				),

				array(
					'id' 		=> 'single-sidebar',
					'type' 		=> 'text',
					'title' 	=> __('Sidebar', 'mfn-opts'),
					'sub_desc' 	=> __('Use this option to force sidebar for all posts', 'mfn-opts'),
					'desc' 		=> __('Paste the name of one of the sidebars that you added in the "Sidebars" section.', 'mfn-opts'),
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'single-sidebar2',
					'type' 		=> 'text',
					'title' 	=> __('Sidebar 2', 'mfn-opts'),
					'sub_desc' 	=> __('Use this option to force sidebar for all posts', 'mfn-opts'),
					'desc' 		=> __('Paste the name of one of the sidebars that you added in the "Sidebars" section.', 'mfn-opts'),
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'sidebar-info-project',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Single Portfolio Projects <span>force sidebar</span>', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'single-portfolio-layout',
					'type' 		=> 'radio_img',
					'title' 	=> __('Layout', 'mfn-opts'),
					'sub_desc' 	=> __('Use this option to force layout for all portfolio projects', 'mfn-opts'),
					'desc' 		=> __('This option can <strong>not</strong> be overwritten and it is usefull for people who already have many portfolio projects and want to standardize their appearance.', 'mfn-opts'),
					'options' 	=> array(
						'' 				=> array('title' => 'Use Post Meta', 'img' => MFN_OPTIONS_URI.'img/question.png'),
						'no-sidebar' 	=> array('title' => 'Full width without sidebar', 'img' => MFN_OPTIONS_URI.'img/1col.png'),
						'left-sidebar'	=> array('title' => 'Left Sidebar', 'img' => MFN_OPTIONS_URI.'img/2cl.png'),
						'right-sidebar'	=> array('title' => 'Right Sidebar', 'img' => MFN_OPTIONS_URI.'img/2cr.png'),
						'both-sidebars' => array('title' => 'Both Sidebars', 'img' => MFN_OPTIONS_URI.'img/2sb.png'),
					),
				),

				array(
					'id' 		=> 'single-portfolio-sidebar',
					'type' 		=> 'text',
					'title' 	=> __('Sidebar', 'mfn-opts'),
					'sub_desc' 	=> __('Use this option to force sidebar for all portfolio projects', 'mfn-opts'),
					'desc' 		=> __('Paste the name of one of the sidebars that you added in the "Sidebars" section.', 'mfn-opts'),
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'single-portfolio-sidebar2',
					'type' 		=> 'text',
					'title' 	=> __('Sidebar 2', 'mfn-opts'),
					'sub_desc' 	=> __('Use this option to force sidebar for all portfolio projects', 'mfn-opts'),
					'desc' 		=> __('Paste the name of one of the sidebars that you added in the "Sidebars" section.', 'mfn-opts'),
					'class' 	=> 'small-text',
				),

				// search page

				array(
					'id' => 'sidebar-info-search-page',
					'type' => 'info',
					'title' => '',
					'desc' => __('Search Page', 'mfn-opts'),
					'class' => 'mfn-info',
				),

				array(
					'id' => 'search-layout',
					'type' => 'radio_img',
					'title' => __('Layout', 'mfn-opts'),
					'options' => array(
						'no-sidebar' 	=> array('title' => 'Without sidebar', 'img' => MFN_OPTIONS_URI.'img/1col.png'),
						'left-sidebar'	=> array('title' => 'Left Sidebar', 'img' => MFN_OPTIONS_URI.'img/2cl.png'),
						'right-sidebar'	=> array('title' => 'Right Sidebar', 'img' => MFN_OPTIONS_URI.'img/2cr.png'),
					),
					'std' => 'no-sidebar',
				),

			),
		);

		// Blog, Portfolio, Shop ==================================================================

		// General -------
		$sections['bps-general'] = array(
			'title' 	=> __('General', 'mfn-opts'),
			'icon' 		=> MFN_OPTIONS_URI. 'img/icons/sub.png',
			'fields'	=> array(

				array(
					'id' 		=> 'bps-info',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Blog, Portfolio, Shop', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id'		=> 'prev-next-nav',
					'type' 		=> 'checkbox',
					'title' 	=> __('Navigation', 'mfn-opts'),
					'sub_desc' 	=> __('Prev/Next Post Navigation', 'mfn-opts'),
					'options' 	=> array(
						'hide-header'	=> __('<b>Hide</b> Header Arrows', 'mfn-opts'),
						'hide-sticky'	=> __('<b>Hide</b> Sticky Arrows', 'mfn-opts'),
						'in-same-term'	=> __('Navigate in the same category <span>excluding Shop</span>', 'mfn-opts'),
					),
				),

				array(
					'id'		=> 'prev-next-style',
					'type' 		=> 'select',
					'title' 	=> __('Navigation | Header Arrows', 'mfn-opts'),
					'options' 	=> array(
						'minimal'	=> __('Simple', 'mfn-opts'),
						''			=> __('Classic', 'mfn-opts'),
					),
					'std'		=> 'minimal'
				),

				array(
					'id'		=> 'prev-next-sticky-style',
					'type' 		=> 'select',
					'title' 	=> __( 'Navigation | Sticky Arrows', 'mfn-opts' ),
					'options' 	=> array(
						''			=> __( 'Default', 'mfn-opts' ),
						'images'	=> __( 'Images only', 'mfn-opts' ),
						'arrows'	=> __( 'Arrows only', 'mfn-opts' ),
					),
				),

				array(
					'id' 		=> 'share',
					'type' 		=> 'select',
					'title' 	=> __( 'Share Box', 'mfn-opts' ),
					'options' 	=> array(
						'1' 			=> __( 'Show', 'mfn-opts' ),
						'0' 			=> __( 'Hide', 'mfn-opts' ),
						'hide-mobile' 	=> __( 'Hide on Mobile', 'mfn-opts' ),
					),
					'std' 		=> '1'
				),

				array(
					'id' 		=> 'share-style',
					'type' 		=> 'select',
					'title' 	=> __( 'Share Box | Style', 'mfn-opts' ),
					'options' 	=> array(
						'' 			=> __( 'Classic', 'mfn-opts' ),
						'simple' 	=> __( 'Simple', 'mfn-opts' ),
					),
					'std' 		=> 'simple',
				),

				array(
					'id' 		=> 'bps-info-bp',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Blog, Portfolio', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'pagination-show-all',
					'type' 		=> 'switch',
					'title' 	=> __('All pages in pagination', 'mfn-opts'),
					'desc' 		=> __('Show all of the pages instead of a short list of the pages near the current page', 'mfn-opts'),
					'options' 	=> array('1' => 'On','0' => 'Off'),
					'std' 		=> '1'
				),

				array(
					'id' 		=> 'love',
					'type' 		=> 'switch',
					'title' 	=> __('Love Box', 'mfn-opts'),
					'sub_desc' 	=> __('Show Love Box', 'mfn-opts'),
					'options' 	=> array( '1' => 'On', '0' => 'Off' ),
					'std' 		=> '1'
				),

				array(
					'id' 		=> 'bps-info-single-bp',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Single Post, Single Portfolio Project', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'featured-image-caption',
					'type' 		=> 'select',
					'title' 	=> __('Featured Image Caption', 'mfn-opts'),
					'desc' 		=> __('Caption for Featured Image can be set in Media Library', 'mfn-opts'),
					'options' 	=> array(
						'' 				=> __('Show', 'mfn-opts'),
						'hide' 			=> __('Hide', 'mfn-opts'),
						'hide-mobile' 	=> __('Hide on Mobile', 'mfn-opts'),

					),
				),

				array(
					'id' 		=> 'related-style',
					'type' 		=> 'select',
					'title' 	=> __('Related Posts |  Style', 'mfn-opts'),
					'title' 	=> __('Related posts & projects style', 'mfn-opts'),
					'options' 	=> array(
						'simple' 	=> __('Simple', 'mfn-opts'),
						'' 			=> __('Classic', 'mfn-opts'),
					),
					'std' 		=> 'simple',
				),

				array(
					'id' => 'title-heading',
					'type' => 'select',
					'title' => __('Title tag', 'mfn-opts'),
					'options' => array(
						'1' => 'H1',
						'2' => 'H2',
						'3' => 'H3',
						'4' => 'H4',
						'5' => 'H5',
						'6' => 'H6',
					),
					'std' => '1'
				),

			),
		);

		// Blog ------
		$sections['blog'] = array(
			'title' 	=> __('Blog', 'mfn-opts'),
			'icon' 		=> MFN_OPTIONS_URI. 'img/icons/sub.png',
			'fields'	=> array(

				// layout -----

				array(
					'id' 		=> 'blog-info-layout',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Layout', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'blog-posts',
					'type' 		=> 'text',
					'title' 	=> __('Posts per page', 'mfn-opts'),
					'desc' 		=> __('This is also number of posts on search page', 'mfn-opts'),
					'class' 	=> 'small-text',
					'std' 		=> 9,
				),

				array(
					'id' => 'blog-layout',
					'type' => 'radio_img',
					'title' => __('Layout', 'mfn-opts'),
					'sub_desc' => __('Layout for Blog Page', 'mfn-opts'),
					'desc' => __('If you do not know what <b>image size</b> is being used for selected style, please navigate to the: Appearance > Theme Options > Blog, Portfolio & Shop > <b>Featured Images</b>', 'mfn-opts'),
					'options' => array(
						'grid' => array('title' => 'Grid<br /><b>2-4 columns</b>', 'img' => MFN_OPTIONS_URI.'img/select/portfolio/grid.png'),
						'classic' => array('title' => 'Classic<br /><b>1 column</b>', 'img' => MFN_OPTIONS_URI.'img/select/blog/classic.png'),
						'masonry' => array('title' => 'Masonry Blog Style<br /><b>2-4 columns</b>', 'img' => MFN_OPTIONS_URI.'img/select/blog/masonry-blog.png'),
						'masonry tiles' => array('title' => 'Masonry Tiles (Vertical Images)<br /><b>2-4 columns</b>', 'img' => MFN_OPTIONS_URI.'img/select/blog/masonry-tiles.png'),
						'photo' => array('title' => 'Photo (Horizontal Images)<br /><b>1 column</b>', 'img' => MFN_OPTIONS_URI.'img/select/blog/photo.png'),
						'photo2' => array('title' => 'Photo 2<br /><b>1-3 columns</b>', 'img' => MFN_OPTIONS_URI.'img/select/blog/photo2.png'),
						'timeline' => array('title' => 'Timeline<br /><b>1 column</b>', 'img' => MFN_OPTIONS_URI.'img/select/blog/timeline.png'),
					),
					'class' => 'wide',
					'std' => 'grid',
				),

				array(
					'id' => 'blog-columns',
					'type' => 'sliderbar',
					'title' => __('Columns', 'mfn-opts'),
					'sub_desc' => __('Recommended: 2-3', 'mfn-opts'),
					'desc' => __('This option works in: <b>Grid, Masonry, Photo 2</b>', 'mfn-opts'),
					'param' => array(
						'min' => 1,
						'max' => 6,
					),
					'std' => 3,
				),

				array(
					'id' => 'blog-title-tag',
					'type' => 'select',
					'title' => __('Title tag', 'mfn-opts'),
					'options' => array(
						'2' => 'H2',
						'3' => 'H3',
						'4' => 'H4',
						'5' => 'H5',
						'6' => 'H6',
					),
					'std' => '2'
				),

				array(
					'id' => 'blog-images',
					'type' => 'select',
					'title' => __('Post Image', 'mfn-opts'),
					'desc' => __('for all Blog styles <b>except</b> Masonry Tiles & Photo 2', 'mfn-opts'),
					'options' => array(
						'' => 'Default',
						'images-only' => 'Featured Images only (replace sliders and videos with featured image)',
					),
				),

				array(
					'id' => 'blog-full-width',
					'type' => 'switch',
					'title' => __('Full Width', 'mfn-opts'),
					'desc' => __('This option works in layout <b>Masonry</b>', 'mfn-opts'),
					'options' => array( '0' => 'Off', '1' => 'On' ),
					'std' => '0'
				),

				// options -----

				array(
					'id' 		=> 'blog-info-options',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Options', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'blog-page',
					'type' 		=> 'pages_select',
					'title' 	=> __('Blog Page', 'mfn-opts'),
					'sub_desc' 	=> __('Assign page for Blog', 'mfn-opts'),
					'desc' 		=> __('Use this option if you set <strong>Front page displays: Your latest posts</strong> in Settings > Reading', 'mfn-opts'),
					'args' 		=> array()
				),

				array(
					'id' 		=> 'blog-orderby',
					'type' 		=> 'select',
					'title' 	=> __( 'Order by', 'mfn-opts' ),
					'desc' 		=> __( 'Do not use random order with pagination or load more', 'mfn-opts' ),
					'options' 	=> array(
						'date'			=> __( 'Date', 'mfn-opts' ),
						'title'			=> __( 'Title', 'mfn-opts' ),
						'rand'			=> __( 'Random', 'mfn-opts' ),
					),
					'std' 		=> 'date'
				),

				array(
					'id' 		=> 'blog-order',
					'type' 		=> 'select',
					'title' 	=> __( 'Order', 'mfn-opts' ),
					'options' 	=> array(
						'ASC' 	=> __( 'Ascending', 'mfn-opts' ),
						'DESC'	=> __( 'Descending', 'mfn-opts' ),
					),
					'std' 		=> 'DESC'
				),

				array(
					'id' 		=> 'exclude-category',
					'type' 		=> 'text',
					'title' 	=> __('Exclude Category', 'mfn-opts'),
					'sub_desc' 	=> __('Exclude category from Blog page', 'mfn-opts'),
					'desc' 		=> __('Category <b>slug</b>. Multiple slugs should be separated with <b>coma</b> ( , )', 'mfn-opts'),
				),

				array(
					'id' 		=> 'excerpt-length',
					'type' 		=> 'text',
					'title' 	=> __('Excerpt Length', 'mfn-opts'),
					'sub_desc' 	=> __('Number of words', 'mfn-opts'),
					'class' 	=> 'small-text',
					'std' 		=> '26',
				),

				array(
					'id' 		=> 'blog-meta',
					'type' 		=> 'checkbox',
					'title' 	=> __( 'Post Meta', 'mfn-opts' ),
					'options' 	=> array(
						'author'		=> __( 'Author', 'mfn-opts' ),
						'date'			=> __( 'Date', 'mfn-opts' ),
						'categories'	=> __( 'Categories & Tags<span>for some Blog styles</span>', 'mfn-opts' ),
					),
					'std'		=> array(
						'author'		=> 'author',
						'date' 			=> 'date',
						'categories' 	=> 'categories',
					),
				),

				array(
					'id' 		=> 'blog-load-more',
					'type' 		=> 'switch',
					'title' 	=> __( 'Load More button', 'mfn-opts' ),
					'sub_desc' 	=> __( 'Show button instead of pagination links', 'mfn-opts' ),
					'desc' 		=> __( '<b>Sliders</b> will be replaced with featured images', 'mfn-opts' ),
					'options' 	=> array( '0' => 'Off', '1' => 'On' ),
					'std' 		=> '0'
				),

				array(
					'id' 		=> 'blog-filters',
					'type' 		=> 'select',
					'title' 	=> __( 'Filters', 'mfn-opts' ),
					'options' 	=> array(
						'1' 				=> __( 'Show', 'mfn-opts' ),
						'only-categories' 	=> __( 'Show only Categories', 'mfn-opts' ),
						'only-tags' 		=> __( 'Show only Tags', 'mfn-opts' ),
						'only-authors' 		=> __( 'Show only Authors', 'mfn-opts' ),
						'0' 				=> __( 'Hide', 'mfn-opts' ),
					),
					'std' 		=> '1'
				),

				array(
					'id' 		=> 'blog-isotope',
					'type' 		=> 'switch',
					'title' 	=> __( 'Filters | jQuery filtering', 'mfn-opts' ),
					'desc' 		=> __( 'Works best with all posts on single site, so please set <b>Posts per page</b> to a large value', 'mfn-opts' ),
					'options' 	=> array( '0' => 'Off', '1' => 'On' ),
				),

				// single -----

				array(
					'id' 		=> 'blog-info-single',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Single Post', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'blog-title',
					'type' 		=> 'switch',
					'title' 	=> __('Title', 'mfn-opts'),
					'sub_desc' 	=> __('Show Post Title', 'mfn-opts'),
					'options' 	=> array( '0' => 'Off', '1' => 'On' ),
					'std' 		=> '0'
				),

				array(
					'id' 		=> 'blog-single-zoom',
					'type' 		=> 'switch',
					'title' 	=> __('Zoom Image', 'mfn-opts'),
					'sub_desc' 	=> __('Zoom Featured Image on click', 'mfn-opts'),
					'options' 	=> array('1' => 'On','0' => 'Off'),
					'std' 		=> '1'
				),

				array(
					'id' 		=> 'blog-author',
					'type' 		=> 'switch',
					'title' 	=> __('Author Box', 'mfn-opts'),
					'sub_desc' 	=> __('Show Author Box', 'mfn-opts'),
					'options' 	=> array('1' => 'On','0' => 'Off'),
					'std' 		=> '1'
				),

				array(
					'id' 		=> 'blog-comments',
					'type' 		=> 'switch',
					'title' 	=> __('Comments', 'mfn-opts'),
					'sub_desc' 	=> __('Show Comments', 'mfn-opts'),
					'options' 	=> array('1' => 'On','0' => 'Off'),
					'std' 		=> '1'
				),

				array(
					'id' 		=> 'blog-single-layout',
					'type' 		=> 'text',
					'title' 	=> __('Layout ID', 'mfn-opts'),
					'sub_desc' 	=> __('Custom layout for all single posts', 'mfn-opts'),
					'class'		=> 'small-text',
				),

				array(
					'id' 		=> 'blog-single-menu',
					'type' 		=> 'select',
					'title' 	=> __('Menu', 'mfn-opts'),
					'sub_desc' 	=> __('Custom menu for all single posts', 'mfn-opts'),
					'desc' 		=> __('Does <b>not</b> work with Split Menu', 'mfn-opts'),
					'options'	=> mfna_menu(),
				),


				// related -----

				array(
					'id' 		=> 'blog-info-related',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Related Posts <span>for Single Post</span>', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'blog-related',
					'type' 		=> 'text',
					'title' 	=> __('Count', 'mfn-opts'),
					'desc' 		=> __('<b>0 to disable</b> related posts', 'mfn-opts'),
					'class'		=> 'small-text',
					'std'		=> 3,
				),

				array(
					'id' 		=> 'blog-related-columns',
					'type' 		=> 'sliderbar',
					'title' 	=> __('Columns', 'mfn-opts'),
					'sub_desc' 	=> __('default: 3', 'mfn-opts'),
					'desc' 		=> __('Recommended: 2-4. Too large value may crash the layout', 'mfn-opts'),
					'param'	 	=> array(
						'min' 		=> 2,
						'max' 		=> 6,
					),
					'std' 		=> 3,
				),

				array(
					'id' => 'blog-related-images',
					'type' => 'select',
					'title' => __('Post Image', 'mfn-opts'),
					'options' => array(
						'' => 'Default',
						'images-only' => 'Featured Images only (replace sliders and videos with featured image)',
					),
				),

				// single advanced -----

				array(
					'id' 		=> 'blog-info-single-advanced',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Single Post <span>only selected styles</span>', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'single-intro-padding',
					'type' 		=> 'text',
					'title' 	=> __('Intro | Padding', 'mfn-opts'),
					'sub_desc' 	=> __('default: 250px 10%', 'mfn-opts'),
					'desc' 		=> __('Use value with <b>px</b> or <b>em</b><br />Example: <b>20px 0</b> or <b>20px 0 30px 0</b> or <b>2em 0</b>', 'mfn-opts'),
					'class' 	=> 'small-text',
				),

				// advanced -----

				array(
					'id' 		=> 'blog-info-advanced',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Advanced', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'blog-love-rand',
					'type' 		=> 'ajax',
					'title' 	=> __('Random Love', 'mfn-opts'),
					'sub_desc' 	=> __('Generate random number of loves', 'mfn-opts'),
					'action' 	=> 'mfn_love_randomize',
				),

			),
		);

		// Portfolio ------
		$sections['portfolio'] = array(
			'title' 	=> __('Portfolio', 'mfn-opts'),
			'icon' 		=> MFN_OPTIONS_URI. 'img/icons/sub.png',
			'fields'	=> array(

				// layout -----

				array(
					'id' 		=> 'portfolio-info-layout',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Layout', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'portfolio-posts',
					'type' 		=> 'text',
					'title' 	=> __('Posts per page', 'mfn-opts'),
					'class' 	=> 'small-text',
					'std' 		=> 9,
				),

				array(
					'id' 		=> 'portfolio-layout',
					'type' 		=> 'radio_img',
					'title' 	=> __('Layout', 'mfn-opts'),
					'desc' 		=> __('If you do not know what <b>image size</b> is being used for selected style, please navigate to the: Appearance > Theme Options > Blog, Portfolio & Shop > <b>Featured Images</b>', 'mfn-opts'),
					'sub_desc' 	=> __('Layout for Portfolio Pages', 'mfn-opts'),
					'options' 	=> array(
						'grid'				=> array('title' => 'Grid', 'img' => MFN_OPTIONS_URI.'img/select/portfolio/grid.png'),
						'flat'				=> array('title' => 'Flat', 'img' => MFN_OPTIONS_URI.'img/select/portfolio/flat.png'),
						'masonry'			=> array('title' => 'Masonry Blog Style', 'img' => MFN_OPTIONS_URI.'img/select/portfolio/masonry-blog.png'),
						'masonry-hover'		=> array('title' => 'Masonry Hover Details', 'img' => MFN_OPTIONS_URI.'img/select/portfolio/masonry-hover.png'),
						'masonry-minimal'	=> array('title' => 'Masonry Minimal', 'img' => MFN_OPTIONS_URI.'img/select/portfolio/masonry-minimal.png'),
						'masonry-flat'		=> array('title' => 'Masonry Flat | 4 columns', 'img' => MFN_OPTIONS_URI.'img/select/portfolio/masonry-flat.png'),
						'list'				=> array('title' => 'List | 1 column', 'img' => MFN_OPTIONS_URI.'img/select/portfolio/list.png'),
						'exposure'			=> array('title' => 'Exposure | 1 column<br />for Full Width Portfolio', 'img' => MFN_OPTIONS_URI.'img/select/portfolio/exposure.png'),
					),
					'class' 	=> 'wide',
					'std' 		=> 'grid',
				),

				array(
					'id' 		=> 'portfolio-columns',
					'type' 		=> 'sliderbar',
					'title' 	=> __('Columns', 'mfn-opts'),
					'sub_desc' 	=> __('default: 3', 'mfn-opts'),
					'desc' 		=> __('Recommended: 2-4. Too large value may crash the layout.<br />This option works in layouts <b>Flat, Grid, Masonry Blog Style, Masonry Hover Details</b>', 'mfn-opts'),
					'param'	 	=> array(
						'min' 		=> 2,
						'max' 		=> 6,
					),
					'std' 		=> 3,
				),

				array(
					'id' 		=> 'portfolio-full-width',
					'type' 		=> 'switch',
					'title' 	=> __('Full Width', 'mfn-opts'),
					'desc' 		=> __('This option works in layouts <b>Flat, Grid, Masonry</b>', 'mfn-opts'),
					'options' 	=> array('1' => 'On','0' => 'Off'),
					'std' 		=> '0'
				),

				// options -----

				array(
					'id' 		=> 'portfolio-info-options',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Options', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'portfolio-page',
					'type' 		=> 'pages_select',
					'title' 	=> __('Portfolio Page', 'mfn-opts'),
					'sub_desc' 	=> __('Assign page for portfolio', 'mfn-opts'),
					'args' 		=> array()
				),

				array(
					'id' 		=> 'portfolio-orderby',
					'type' 		=> 'select',
					'title' 	=> __( 'Order by', 'mfn-opts' ),
					'desc' 		=> __( 'Do not use random order with pagination or load more', 'mfn-opts' ),
					'options' 	=> array(
						'date'			=> __( 'Date', 'mfn-opts' ),
						'menu_order' 	=> __( 'Menu order', 'mfn-opts' ),
						'title'			=> __( 'Title', 'mfn-opts' ),
						'rand'			=> __( 'Random', 'mfn-opts' ),
					),
					'std' 		=> 'date'
				),

				array(
					'id' 		=> 'portfolio-order',
					'type' 		=> 'select',
					'title' 	=> __( 'Order', 'mfn-opts' ),
					'options' 	=> array(
						'ASC' 	=> __( 'Ascending', 'mfn-opts' ),
						'DESC'	=> __( 'Descending', 'mfn-opts' ),
					),
					'std' 		=> 'DESC'
				),

				array(
					'id' 		=> 'portfolio-external',
					'type' 		=> 'select',
					'title' 	=> __('Project Link', 'mfn-opts'),
					'sub_desc' 	=> __('Image and Title Link', 'mfn-opts'),
					'options' 	=> array(
						''			=> __('Details', 'mfn-opts'),
						'popup'		=> __('Popup Image', 'mfn-opts'),
						'disable'	=> __('Disable Details | Only Popup Image', 'mfn-opts'),
						'_self'		=> __('Project Website | Open in the same window', 'mfn-opts'),
						'_blank'	=> __('Project Website | Open in new window', 'mfn-opts'),
					),
				),

				array(
					'id' 		=> 'portfolio-hover-title',
					'type' 		=> 'switch',
					'title' 	=> __('Hover Title', 'mfn-opts'),
					'sub_desc' 	=> __('Show Post Title instead of Hover Icons', 'mfn-opts'),
					'desc' 		=> __('Only for short post titles. Does <b>not</b> work with Image Frame style: Zoom', 'mfn-opts'),
					'options' 	=> array( '0' => 'Off', '1' => 'On' ),
					'std' 		=> '0'
				),

				array(
					'id' 		=> 'portfolio-meta',
					'type' 		=> 'checkbox',
					'title' 	=> __( 'Portfolio Meta', 'mfn-opts' ),
					'desc' 		=> __( 'Most of these options affects single portfolio project only', 'mfn-opts' ),
					'options' 	=> array(
						'author'		=> __( 'Author', 'mfn-opts' ),
						'date'			=> __( 'Date', 'mfn-opts' ),
						'categories'	=> __( 'Categories', 'mfn-opts' ),
					),
					'std'		=> array(
						'author'		=> 'author',
						'date' 			=> 'date',
						'categories' 	=> 'categories',
					),
				),

				array(
					'id' 		=> 'portfolio-load-more',
					'type' 		=> 'switch',
					'title' 	=> __( 'Load More button', 'mfn-opts' ),
					'sub_desc' 	=> __( 'Show button instead of pagination links', 'mfn-opts' ),
					'options' 	=> array( '0' => 'Off', '1' => 'On' ),
					'std' 		=> '0'
				),

				array(
					'id' 		=> 'portfolio-filters',
					'type' 		=> 'select',
					'title' 	=> __( 'Filters', 'mfn-opts' ),
					'options' 	=> array(
							'1' 				=> __( 'Show', 'mfn-opts' ),
							'only-categories' 	=> __( 'Show only Categories', 'mfn-opts' ),
							'0' 				=> __( 'Hide', 'mfn-opts' ),
					),
					'std' 		=> '1'
				),

				array(
					'id' 		=> 'portfolio-isotope',
					'type' 		=> 'switch',
					'title' 	=> __( 'Filters | jQuery filtering', 'mfn-opts' ),
					'desc' 		=> __( 'Works best with all projects on single site, so please set <b>Posts per page</b> to a large value', 'mfn-opts' ),
					'options' 	=> array( '1' => 'On', '0' => 'Off' ),
					'std' 		=> '1'
				),

				// single -----
				array(
					'id' 		=> 'portfolio-info-single',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Single Portfolio Project', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'portfolio-single-title',
					'type' 		=> 'switch',
					'title' 	=> __('Title', 'mfn-opts'),
					'sub_desc' 	=> __('Show Single Project Title', 'mfn-opts'),
					'options' 	=> array( '0' => 'Off', '1' => 'On' ),
					'std' 		=> '0'
				),

				array(
					'id' => 'portfolio-related',
					'type' => 'text',
					'title' => __('Related Projects | Count', 'mfn-opts'),
					'desc' => __('<b>0 to disable</b> related projects', 'mfn-opts'),
					'class' => 'small-text',
					'std' => 3,
				),

				array(
					'id' 		=> 'portfolio-related-columns',
					'type' 		=> 'sliderbar',
					'title' 	=> __('Related Projects | Columns', 'mfn-opts'),
					'sub_desc' 	=> __('default: 3', 'mfn-opts'),
					'desc' 		=> __('Recommended: 2-4. Too large value may crash the layout', 'mfn-opts'),
					'param'	 	=> array(
						'min' 		=> 2,
						'max' 		=> 6,
					),
					'std' 		=> 3,
				),

				array(
					'id' 		=> 'portfolio-comments',
					'type' 		=> 'switch',
					'title' 	=> __('Comments', 'mfn-opts'),
					'sub_desc' 	=> __('Show Comments', 'mfn-opts'),
					'options' 	=> array( '0' => 'Off', '1' => 'On' ),
					'std' 		=> '0'
				),

				array(
					'id' 		=> 'portfolio-single-layout',
					'type' 		=> 'text',
					'title' 	=> __('Layout ID', 'mfn-opts'),
					'sub_desc' 	=> __('Custom layout for all single portfolio projects', 'mfn-opts'),
					'class'		=> 'small-text',
				),

				array(
					'id' 		=> 'portfolio-single-menu',
					'type' 		=> 'select',
					'title' 	=> __('Menu', 'mfn-opts'),
					'sub_desc' 	=> __('Custom menu for all single portfolio projects', 'mfn-opts'),
					'desc' 		=> __('Does <b>not</b> work with Split Menu', 'mfn-opts'),
					'options'	=> mfna_menu(),
				),

				// advanced -----

				array(
					'id' 		=> 'portfolio-info-advanced',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Advanced', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'portfolio-love-rand',
					'type' 		=> 'ajax',
					'title' 	=> __('Random Love', 'mfn-opts'),
					'sub_desc' 	=> __('Generate random number of loves', 'mfn-opts'),
					'action' 	=> 'mfn_love_randomize',
					'param'	 	=> 'portfolio',
				),

				array(
					'id' 		=> 'portfolio-slug',
					'type' 		=> 'text',
					'title' 	=> __('Permalink | Single Project slug', 'mfn-opts'),
					'sub_desc' 	=> __('Do not use characters not allowed in links', 'mfn-opts'),
					'desc' 		=> __('Must be different from the Portfolio site title chosen above, eg. <b>portfolio-item</b>. After change go to <b>Settings > Permalinks</b> and click <b>Save changes</b>.', 'mfn-opts'),
					'class' 	=> 'small-text',
					'std' 		=> 'portfolio-item',
				),

				array(
					'id' 		=> 'portfolio-tax',
					'type' 		=> 'text',
					'title' 	=> __('Permalink | Category slug', 'mfn-opts'),
					'sub_desc' 	=> __('Do not use characters not allowed in links', 'mfn-opts'),
					'desc' 		=> __('Must be different from the Portfolio site title chosen above, eg. <b>portfolio-types</b>. After change go to <b>Settings > Permalinks</b> and click <b>Save changes</b>.', 'mfn-opts'),
					'class' 	=> 'small-text',
					'std' 		=> 'portfolio-types',
				),

			),
		);

		// Shop ------
		$sections['shop'] = array(
			'title' 	=> __('Shop', 'mfn-opts'),
			'icon' 		=> MFN_OPTIONS_URI. 'img/icons/sub.png',
			'fields'	=> array(

				array(
					'id' 		=> 'shop-info',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Shop requires free WooCommerce plugin. <a target="_blank" href="plugin-install.php?s=WooCommerce&tab=search&type=term">Install plugin</a>', 'mfn-opts'),
					'class' 	=> 'mfn-info desc',
				),

				// layout -----
				array(
					'id' 		=> 'shop-info-layout',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Layout', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'shop-products',
					'type' 		=> 'text',
					'title' 	=> __('Products per page', 'mfn-opts'),
					'class' 	=> 'small-text',
					'std' 		=> '12',
				),

				array(
					'id' 		=> 'shop-layout',
					'type' 		=> 'radio_img',
					'title' 	=> __('Layout', 'mfn-opts'),
					'desc' 		=> __('You can set image size in Appearance > Customize > WooCommerce > Product Images', 'mfn-opts'),
					'sub_desc' 	=> __('Layout for Shop Pages', 'mfn-opts'),
					'options' 	=> array(
						'grid'			=> array('title' => 'Grid 3 col', 'img' => MFN_OPTIONS_URI.'img/select/shop/grid.png'),
						'grid col-4'	=> array('title' => 'Grid 4 col', 'img' => MFN_OPTIONS_URI.'img/select/shop/grid-4.png'),
						'masonry'		=> array('title' => 'Masonry', 'img' => MFN_OPTIONS_URI.'img/select/shop/masonry.png'),
						'list'			=> array('title' => 'List', 'img' => MFN_OPTIONS_URI.'img/select/shop/list.png'),
					),
					'std' 		=> 'grid',
					'class' 	=> 'wide',
				),

				array(
					'id' 		=> 'shop-catalogue',
					'type' 		=> 'switch',
					'title' 	=> __('Catalogue Mode', 'mfn-opts'),
					'sub_desc' 	=> __('Remove all Add to Cart buttons', 'mfn-opts'),
					'options' 	=> array('1' => 'On','0' => 'Off'),
					'std' 		=> '0'
				),

				// options -----
				array(
					'id' 		=> 'shop-info-options',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Options', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'shop-images',
					'type' 		=> 'select',
					'title' 	=> __( 'Images', 'mfn-opts' ),
					'options' 	=> array(
						'' 			=> __( '-- Default --', 'mfn-opts' ),
						'secondary'	=> __( 'Show secondary image on hover', 'mfn-opts' ),
						'plugin'	=> __( 'Use external plugin for featured images', 'mfn-opts' ),
					),
				),

				array(
					'id' 		=> 'shop-button',
					'type' 		=> 'switch',
					'title' 	=> __('Add to Cart Button', 'mfn-opts'),
					'sub_desc' 	=> __('Show Cart button on archives', 'mfn-opts'),
					'desc' 		=> __('Required for some plugins', 'mfn-opts'),
					'options' 	=> array( '0' => 'Off', '1' => 'On' ),
					'std' 		=> '0'
				),

				array(
					'id' 		=> 'shop-excerpt',
					'type' 		=> 'switch',
					'title' 	=> __('Descriptions', 'mfn-opts'),
					'sub_desc' 	=> __('Show descriptions on archives', 'mfn-opts'),
					'options' 	=> array( '0' => 'Off', '1' => 'On' ),
					'std' 		=> '0'
				),

				array(
					'id' 		=> 'shop-sidebar',
					'type' 		=> 'select',
					'title' 	=> __('Sidebar', 'mfn-opts'),
					'sub_desc' 	=> __('Show Shop Page Sidebar on', 'mfn-opts'),
					'options' 	=> array(
						'' 			=> __('All (Shop, Categories, Products)', 'mfn-opts'),
						'shop'		=> __('Shop & Categories', 'mfn-opts'),
					),
				),

				array(
					'id' 		=> 'shop-slider',
					'type' 		=> 'select',
					'title' 	=> __('Slider', 'mfn-opts'),
					'sub_desc' 	=> __('Show Shop Page Slider on', 'mfn-opts'),
					'options' 	=> array(
						'' 			=> __('Main Shop Page', 'mfn-opts'),
						'all'		=> __('All (Shop, Categories, Products)', 'mfn-opts'),
					),
				),

				array(
					'id' 		=> 'shop-soldout',
					'type' 		=> 'text',
					'title' 	=> __('Sold out', 'mfn-opts'),
					'sub_desc' 	=> __('Sold out label', 'mfn-opts'),
					'std' 		=> __('Sold out', 'mfn-opts'),
					'class' 	=> 'small-text',
				),

				// single -----
				array(
					'id' 		=> 'shop-info-single',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Single Product', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'shop-product-style',
					'type' 		=> 'select',
					'title' 	=> __('Style', 'mfn-opts'),
					'desc' 		=> __('For <b>Modern style</b> recommended image width is <b>900px</b> (1200px without sidebar)<br />You can set image size in Appearance > Customize > WooCommerce > Product Images', 'mfn-opts'),
					'options' 	=> array(
						'' 			=> __('Accordion | Next to image', 'mfn-opts'),
						'wide' 		=> __('Accordion | Below image', 'mfn-opts'),
						'tabs' 		=> __('Tabs | Next to image', 'mfn-opts'),
						'wide tabs'	=> __('Tabs | Below image', 'mfn-opts'),
						'modern'	=> __('Modern', 'mfn-opts'),
					),
				),

				array(
					'id' => 'shop-single-image',
					'type' => 'select',
					'title' => __('Product image', 'mfn-opts'),
					'options' => array(
						'' => __('-- Default --', 'mfn-opts'),
						'disable-zoom' => __('Disable zoom effect', 'mfn-opts'),
					),
				),

				array(
					'id' 		=> 'shop-product-title',
					'type' 		=> 'select',
					'title' 	=> __('Title', 'mfn-opts'),
					'sub_desc' 	=> __('Show Product Title in', 'mfn-opts'),
					'options' 	=> array(
						'' 				=> __('Content', 'mfn-opts'),
						'content-sub'	=> __('Content & Subheader', 'mfn-opts'),
						'sub'			=> __('Subheader', 'mfn-opts'),
					),
				),

				array(
					'id' 		=> 'shop-related',
					'type' 		=> 'text',
					'title' 	=> __('Related Products | Count', 'mfn-opts'),
					'class'		=> 'small-text',
					'std'		=> 3,
				),

				// advanced -----
				array(
					'id' 		=> 'shop-info-advanced',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Advanced', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'shop-cart',
					'type' 		=> 'icon',
					'title' 	=> __('Cart | Icon', 'mfn-opts'),
					'sub_desc' 	=> __('Header Cart Icon', 'mfn-opts'),
					'desc' 		=> __('Leave this field blank to hide cart icon', 'mfn-opts'),
					'class' 	=> 'small-text',
					'std' 		=> 'icon-bag-fine',
				),

			),
		);

		// Featured Image ------
		$sections['featured-image'] = array(
			'title' 	=> __('Featured Image', 'mfn-opts'),
			'icon' 		=> MFN_OPTIONS_URI. 'img/icons/sub.png',
			'fields'	=> array(

				// list -----

				array(
					'id' 		=> 'featured-info-list',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Blog & Portfolio', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'featured-blog-portfolio-width',
					'type' 		=> 'text',
					'title' 	=> __('Width', 'mfn-opts'),
					'sub_desc' 	=> __('default: 960', 'mfn-opts'),
					'desc' 		=> __('px', 'mfn-opts'),
					'class' 	=> 'small-text',
					'std' 		=> '960',
				),

				array(
					'id' 		=> 'featured-blog-portfolio-height',
					'type' 		=> 'text',
					'title' 	=> __('Height', 'mfn-opts'),
					'sub_desc' 	=> __('default: 720', 'mfn-opts'),
					'desc' 		=> __('px', 'mfn-opts'),
					'class' 	=> 'small-text',
					'std' 		=> '720',
				),

				array(
					'id' 		=> 'featured-blog-portfolio-crop',
					'type' 		=> 'select',
					'title' 	=> __('Crop', 'mfn-opts'),
					'sub_desc' 	=> __('default: Resize & Crop', 'mfn-opts'),
					'options' 	=> array(
						'crop' 		=> __('Resize & Crop', 'mfn-opts'),
						'resize' 	=> __('Resize', 'mfn-opts'),
					),
				),

				array(
					'id' 		=> 'featured-desc-list',
					'type' 		=> 'custom',
					'title' 	=> 'Description',
					'desc' 		=> '<ul><li><b>This size is being used for:</b></li><li>Blog: style Classic</li><li>Blog: style Grid</li><li>Blog: style Masonry</li><li>Blog: style Timeline</li><li>Blog: Related Posts</li><li>Portfolio: style Flat</li><li>Portfolio: style Grid</li><li>Portfolio: style Masonry Blog Style</li><li>Portfolio: Related Projects</li></ul><ul><li><b>Original images:</b></li><li>Blog: style Masonry Tiles</li><li>Post format: Vertical Image in all blog styles</li><li>Portfolio: style Exposure</li><li>Portfolio: style Masonry Hover Details</li><li>Portfolio: style Masonry Minimal</li></ul><ul><li><b>Different sizes:</b></li><li>Blog: style Photo - the same size as Single Post</li><li>Portfolio: style List - size: 1920x750</li><li>Portfolio: style Masonry Flat - default, big: 1280x1000, wide: 1280x500, tall: 768x1200</li></ul>',
					'action' 	=> 'description',
				),

				// single -----
				array(
					'id' 		=> 'featured-info-single',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Single Post & Single Portfolio', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'featured-single-width',
					'type' 		=> 'text',
					'title' 	=> __('Width', 'mfn-opts'),
					'sub_desc' 	=> __('default: 1200', 'mfn-opts'),
					'desc' 		=> __('px', 'mfn-opts'),
					'class' 	=> 'small-text',
					'std' 		=> '1200',
				),

				array(
					'id' 		=> 'featured-single-height',
					'type' 		=> 'text',
					'title' 	=> __('Height', 'mfn-opts'),
					'sub_desc' 	=> __('default: 675', 'mfn-opts'),
					'desc' 		=> __('px', 'mfn-opts'),
					'class' 	=> 'small-text',
					'std' 		=> '675',
				),

				array(
					'id' 		=> 'featured-single-crop',
					'type' 		=> 'select',
					'title' 	=> __('Crop', 'mfn-opts'),
					'sub_desc' 	=> __('default: Resize & Crop', 'mfn-opts'),
					'options' 	=> array(
						'crop' 		=> __('Resize & Crop', 'mfn-opts'),
						'resize' 	=> __('Resize', 'mfn-opts'),
					),
				),

				array(
					'id' 		=> 'featured-desc-single',
					'type' 		=> 'custom',
					'title' 	=> 'Description',
					'desc' 		=> '<ul><li><b>This size is being used for:</b></li><li>Blog: single Post</li><li>Blog: style Photo</li><li>Portfolio: single Project</li></ul><ul><li><b>Original images:</b></li><li>Post format: Vertical Image</li><li>Template: Intro Header</li></ul>',
					'action' 	=> 'description',
				),

				// force regenerate thumbnails -----

				array(
					'id' 		=> 'featured-info-force',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('After making changes on this page please use Force Regenerate Thumbnails plugin. <a target="_blank" href="themes.php?page=tgmpa-install-plugins">Install plugin</a> and <a target="_blank" href="tools.php?page=force-regenerate-thumbnails">Regenerate thumbnails</a>', 'mfn-opts'),
					'class' 	=> 'mfn-info desc',
				),


			),
		);


		// Pages =================

		// General -----
		$sections['pages-general'] = array(
			'title' 	=> __('General', 'mfn-opts'),
			'icon'		=> MFN_OPTIONS_URI. 'img/icons/sub.png',
			'fields' 	=> array(

				array(
					'id' 		=> 'page-comments',
					'type' 		=> 'switch',
					'title' 	=> __('Page Comments', 'mfn-opts'),
					'sub_desc' 	=> __('Show Comments for pages', 'mfn-opts'),
					'desc' 		=> __('Single Page', 'mfn-opts'),
					'options' 	=> array( '0' => 'Off', '1' => 'On' ),
					'std' 		=> '0'
				),

			),
		);

		// Error 404 -----
		$sections['pages-404'] = array(
			'title' 	=> __('Error 404', 'mfn-opts'),
			'icon'		=> MFN_OPTIONS_URI. 'img/icons/sub.png',
			'fields' 	=> array(

				array(
					'id' 		=> 'error404-icon',
					'type' 		=> 'icon',
					'title' 	=> __('Icon', 'mfn-opts'),
					'sub_desc' 	=> __('Error 404 Page Icon', 'mfn-opts'),
					'class' 	=> 'small-text',
					'std' 		=> 'icon-traffic-cone',
				),

				array(
					'id' 		=> 'error404-page',
					'type' 		=> 'pages_select',
					'title' 	=> __('Custom Page', 'mfn-opts'),
					'sub_desc' 	=> __('Page Options, header & footer are disabled', 'mfn-opts'),
					'desc' 		=> __('Leave this field <b>blank</b> if you want to use <b>default</b> 404 page<br /><b>Notice: </b>Plugins like Visual Composer & Gravity Forms <b>do not work</b> on this page', 'mfn-opts'),
					'args' 		=> array()
				),

			),
		);

		// Under Construction ------
		$sections['pages-under'] = array(
			'title' 	=> __('Under Construction', 'mfn-opts'),
			'icon' 		=> MFN_OPTIONS_URI. 'img/icons/sub.png',
			'fields'	=> array(

				array(
					'id' 		=> 'construction',
					'type' 		=> 'switch',
					'title' 	=> __('Under Construction', 'mfn-opts'),
					'desc' 		=> __('Under Construction page will be visible for all NOT logged in users.', 'mfn-opts'),
					'options' 	=> array('1' => 'On','0' => 'Off'),
					'std' 		=> '0'
				),

				array(
					'id' 		=> 'construction-title',
					'type' 		=> 'text',
					'title' 	=> __('Title', 'mfn-opts'),
					'std' 		=> 'Coming Soon',
				),

				array(
					'id' 		=> 'construction-text',
					'type' 		=> 'textarea',
					'title' 	=> __('Text', 'mfn-opts'),
				),

				array(
					'id' 		=> 'construction-date',
					'type' 		=> 'text',
					'title' 	=> __('Launch Date', 'mfn-opts'),
					'desc' 		=> __('Format: 12/30/2018 12:00:00 month/day/year hour:minute:second<br />Leave this field <b>blank to hide the counter</b>', 'mfn-opts'),
					'std' 		=> '12/30/2018 12:00:00',
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'construction-offset',
					'type' 		=> 'select',
					'title' 	=> __('UTC Timezone', 'mfn-opts'),
					'options' 	=> mfna_utc(),
					'std' 		=> '0',
				),

				array(
					'id' 		=> 'construction-contact',
					'type' 		=> 'text',
					'title' 	=> __('Contact Form Shortcode', 'mfn-opts'),
					'desc' 		=> __('eg. [contact-form-7 id="000" title="Maintenance"]', 'mfn-opts'),
				),

				array(
					'id' 		=> 'construction-page',
					'type' 		=> 'pages_select',
					'title' 	=> __('Custom Page', 'mfn-opts'),
					'sub_desc' 	=> __('Page Options, header & footer are disabled', 'mfn-opts'),
					'desc' 		=> __('Leave this field <b>blank</b> if you want to use <b>default</b> Under Construction page<br /><b>Notice: </b>Plugins like Visual Composer & Gravity Forms <b>do not work</b> on this page', 'mfn-opts'),
					'args' 		=> array(),
				),

			),
		);

		// Footer ================

		// Footer ------
		$sections['footer'] = array(
			'title'		=> __('General', 'mfn-opts'),
			'fields' 	=> array(

				array(
					'id' 		=> 'footer-info-layout',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Layout', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id'			=> 'footer-layout',
					'type'		=> 'select',
					'title'		=> __( 'Layout', 'mfn-opts' ),
					'options'	=> array(
						'' => __( '-- Default --', 'mfn-opts' ),
						'5;one-fifth;one-fifth;one-fifth;one-fifth;one-fifth;'	=> '1/5 1/5 1/5 1/5 1/5 (for narrow widgets only)',
						'4;one-fourth;one-fourth;one-fourth;one-fourth'					=> '1/4 1/4 1/4 1/4',

						'3;one-fifth;two-fifth;two-fifth'				=> '1/5 2/5 2/5',
						'3;two-fifth;one-fifth;two-fifth'				=> '2/5 1/5 2/5',
						'3;two-fifth;two-fifth;one-fifth'				=> '2/5 2/5 1/5',

						'3;one-fourth;one-fourth;one-second;'		=> '1/4 1/4 1/2',
						'3;one-fourth;one-second;one-fourth;'		=> '1/4 1/2 1/4',
						'3;one-second;one-fourth;one-fourth;'		=> '1/2 1/4 1/4',
						'3;one-third;one-third;one-third;'			=> '1/3 1/3 1/3',
						'2;one-third;two-third;;'								=> '1/3 2/3',
						'2;two-third;one-third;;'								=> '2/3 1/3',
						'2;one-second;one-second;;'							=> '1/2 1/2',
						'1;one;;;'															=> '1/1',
					),
				),

				array(
					'id'			=> 'footer-style',
					'type'		=> 'select',
					'title'		=> __( 'Style', 'mfn-opts' ),
					'desc'		=> __( 'Sliding style does <b>not</b> work with transparent content', 'mfn-opts' ),
					'options'	=> array(
						''				=> __( '-- Default --', 'mfn-opts' ),
						'fixed'		=> __( 'Fixed (covers content)', 'mfn-opts' ),
						'sliding'	=> __( 'Sliding (under content)', 'mfn-opts' ),
						'stick'		=> __( 'Stick to bottom if content is too short', 'mfn-opts' ),
						'hide'		=> __( 'HIDE Footer', 'mfn-opts' ),
					),
				),

				array(
					'id' 		=> 'footer-padding',
					'type' 		=> 'text',
					'title' 	=> __('Padding', 'mfn-opts'),
					'sub_desc' 	=> __('default: 15px 0', 'mfn-opts'),
					'desc' 		=> __('Use value with <b>px</b> or <b>em</b><br />Example: <b>20px 0</b> or <b>20px 0 30px 0</b> or <b>2em 0</b>', 'mfn-opts'),
					'class' 	=> 'small-text',
					'std' 		=> '70px 0',
				),

				array(
					'id' 		=> 'footer-info-background',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Background', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 			=> 'footer-bg-img',
					'type' 		=> 'upload',
					'title' 	=> __( 'Image', 'mfn-opts' ),
				),

				array(
					'id' 		=> 'footer-bg-img-position',
					'type' 		=> 'select',
					'title' 	=> __('Position', 'mfn-opts'),
					'desc' 		=> __('iOS does <b>not</b> support background-position: fixed', 'mfn-opts'),
					'options' 	=> mfna_bg_position(1),
					'std' 		=> 'center top no-repeat',
				),

				array(
					'id' 		=> 'footer-bg-img-size',
					'type' 		=> 'select',
					'title' 	=> __('Size', 'mfn-opts'),
					'desc' 		=> __('Does <b>not</b> work with fixed position. Works only in modern browsers', 'mfn-opts'),
					'options' 	=> mfna_bg_size(),
				),

				array(
					'id' 		=> 'footer-info-advanced',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Advanced', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id'			=> 'footer-call-to-action',
					'type'		=> 'textarea',
					'title'		=> __( 'Call To Action', 'mfn-opts' ),
				),

				array(
					'id'			=> 'footer-copy',
					'type'		=> 'textarea',
					'title'		=> __( 'Copyright', 'mfn-opts' ),
					'desc'		=> __( 'Leave this field blank to show a default copyright', 'mfn-opts' ),
				),

				array(
					'id' 		=> 'footer-hide',
					'type' 		=> 'select',
					'title' 	=> __('Copyright & Social Bar', 'mfn-opts'),
					'options' 	=> array(
						'' 			=> __('Default', 'mfn-opts'),
						'center' 	=> __('Center', 'mfn-opts'),
						'1' 		=> __('Hide Copyright & Social Bar', 'mfn-opts')
					),
				),

				array(
					'id' 		=> 'footer-info-extras',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Extras', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id'		=> 'back-top-top',
					'type'		=> 'select',
					'title'		=> __('Back to Top button', 'mfn-opts'),
					'options'	=> array(
						''				=> __('Default | in Copyright area', 'mfn-opts'),
						'sticky'		=> __('Sticky', 'mfn-opts'),
						'sticky scroll'	=> __('Sticky show on scroll', 'mfn-opts'),
						'hide'			=> __('Hide', 'mfn-opts'),
					),
				),

				array(
					'id'		=> 'popup-contact-form',
					'type'		=> 'text',
					'title'		=> __('Popup Contact Form | Shortcode', 'mfn-opts'),
					'sub_desc'	=> __('<b>> 768px</b>', 'mfn-opts'),
					'desc'		=> __('	eg. [contact-form-7 id="000" title="Popup Contact Form"]', 'mfn-opts'),
				),

				array(
					'id'		=> 'popup-contact-form-icon',
					'type'		=> 'icon',
					'title'		=> __('Popup Contact Form | Icon', 'mfn-opts'),
					'std'		=> 'icon-mail-line',
				),

			),
		);

		// Responsive ================

		// General ------
		$sections['responsive'] = array(
			'title'		=> __('General', 'mfn-opts'),
			'fields' 	=> array(

				array(
					'id' 		=> 'responsive',
					'type' 		=> 'switch',
					'title' 	=> __('Responsive', 'mfn-opts'),
					'desc' 		=> __('<b>Notice:</b> Responsive menu is working only with WordPress custom menu, please add one in Appearance > Menus and select it for Theme Locations section<br /><a href="https://codex.wordpress.org/WordPress_Menu_User_Guide" target="_blank">https://codex.wordpress.org/WordPress_Menu_User_Guide</a>', 'mfn-opts'),
					'options' 	=> array('1' => 'On','0' => 'Off'),
					'std' 		=> '1'
				),

				// layout
				array(
					'id' 		=> 'responsive-info-layout',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Layout', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'mobile-grid-width',
					'type' 		=> 'sliderbar',
					'title' 	=> __('Mobile Grid width', 'mfn-opts'),
					'sub_desc' 	=> __('<b>< 768px</b>', 'mfn-opts'),
					'desc' 		=> __('default: 480', 'mfn-opts'),
					'param'	 	=> array(
						'min' 		=> 480,
						'max' 		=> 700,
					),
					'std' 		=> 480,
				),

				array(
					'id' 		=> 'font-size-responsive',
					'type' 		=> 'switch',
					'title' 	=> __('Decrease Fonts', 'mfn-opts'),
					'desc' 		=> __('Automatically decrease font size in responsive', 'mfn-opts'),
					'options' 	=> array( '1' => 'On', '0' => 'Off' ),
					'std' 		=> '1'
				),

				array(
					'id' 		=> 'responsive-zoom',
					'type' 		=> 'switch',
					'title' 	=> __('Pinch Zoom', 'mfn-opts'),
					'desc' 		=> __('Allow pinch zoom', 'mfn-opts'),
					'options' 	=> array( '0' => 'Off', '1' => 'On' ),
					'std' 		=> '0'
				),

				// options
				array(
					'id' => 'responsive-info-options',
					'type' => 'info',
					'title' => '',
					'desc' => __('Options', 'mfn-opts'),
					'class' => 'mfn-info',
				),

				array(
					'id' => 'responsive-boxed2fw',
					'type' => 'switch',
					'title' => __('Boxed to Full Width', 'mfn-opts'),
					'sub_desc' => __('<b>< 768px</b>', 'mfn-opts'),
					'desc' => __('Change layout from Boxed to Full Width on mobile', 'mfn-opts'),
					'options'	=> array( '0' => 'Off', '1' => 'On' ),
					'std' => '0',
				),

				array(
					'id' => 'no-section-bg',
					'type' => 'select',
					'title' => __( 'Section | Background Image', 'mfn-opts' ),
					'options' => array(
						'' => __( '-- Default --', 'mfn-opts' ),
						'tablet' => __( 'Show on Desktop only', 'mfn-opts' ),
					),
				),

				array(
					'id' => 'responsive-parallax',
					'type' => 'select',
					'title' => __( 'Section | Parallax', 'mfn-opts' ),
					'desc' => __( 'Works only with <b>Translate3d</b> parallax. May run slowly on older devices', 'mfn-opts' ),
					'options' => array(
						0 => __( 'Disable on mobile', 'mfn-opts' ),
						1	=> __( 'Enable on mobile', 'mfn-opts' ),
					),
				),

				array(
					'id' => 'builder-section-padding',
					'type' => 'select',
					'title' => __( 'Section | Horizontal padding', 'mfn-opts' ),
					'options' => array(
						'' => __( '-- Default --', 'mfn-opts' ),
						'no-tablet' => __( 'Disable on tablet and mobile < 960px', 'mfn-opts' ),
						'no-mobile' => __( 'Disable on mobile < 768px', 'mfn-opts' ),
					),
				),

				array(
					'id' => 'builder-wrap-moveup',
					'type' => 'select',
					'title' => __( 'Wrap | Move Up', 'mfn-opts' ),
					'options' => array(
						'' => __( '-- Default --', 'mfn-opts' ),
						'no-tablet' => __( 'Disable on tablet and mobile < 960px', 'mfn-opts' ),
						'no-move' => __( 'Disable on mobile < 768px', 'mfn-opts' ),
					),
				),

				array(
					'id' => 'footer-align',
					'type' => 'select',
					'title' => __( 'Footer | Text align', 'mfn-opts' ),
					'options' => array(
						'' => __( '-- Default --', 'mfn-opts' ),
						'center' => __( 'Center', 'mfn-opts' ),
					),
				),

				// logo
				array(
					'id' 		=> 'responsive-info-logo',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Logo <span><b>mobile</b> < 768px</span>', 'mfn-opts'),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id'			=> 'responsive-logo-img',
					'type'		=> 'upload',
					'title'		=> __( 'Logo', 'mfn-opts' ),
					'sub_desc'=> __( '<b>< 768px</b><br />optional', 'mfn-opts' ),
					'desc'		=> __( 'Use if you want different logo on mobile', 'mfn-opts' ),
					'class' 	=> 'mhb-opt',
				),

				array(
					'id'			=> 'responsive-retina-logo-img',
					'type'		=> 'upload',
					'title'		=> __( 'Retina Logo', 'mfn-opts' ),
					'sub_desc'=> __( 'optional', 'mfn-opts' ),
					'desc'		=> __( 'Retina Logo should be 2x larger than Logo', 'mfn-opts' ),
					'class' 	=> 'mhb-opt',
				),

				// logo sticky
				array(
					'id' 		=> 'responsive-sticky-info-logo',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Sticky Header Logo <span><b>mobile</b> < 768px</span>', 'mfn-opts'),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id'			=> 'responsive-sticky-logo-img',
					'type'		=> 'upload',
					'title'		=> __(' Logo', 'mfn-opts' ),
					'sub_desc'=> __( '<b>< 768px</b><br />optional', 'mfn-opts' ),
					'desc'		=> __( 'Use if you want different logo for Sticky Header on mobile', 'mfn-opts' ),
					'class' 	=> 'mhb-opt',
				),

				array(
					'id'			=> 'responsive-sticky-retina-logo-img',
					'type'		=> 'upload',
					'title'		=> __( 'Retina Logo', 'mfn-opts' ),
					'sub_desc'=> __( 'optional', 'mfn-opts' ),
					'desc'		=> __( 'Retina Logo should be 2x larger than Sticky Header Logo', 'mfn-opts' ),
					'class' 	=> 'mhb-opt',
				),

			),
		);

		// Responsive | Header ------
		$sections['responsive-header'] = array(
			'title'		=> __( 'Header', 'mfn-opts' ),
			'fields' 	=> array(

				// header
				array(
					'id' 			=> 'responsive-info-header',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __( 'Header', 'mfn-opts' ),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id'		=> 'responsive-header-tablet',
					'type' 		=> 'checkbox',
					'title' 	=> __('Tablet options', 'mfn-opts'),
					'sub_desc' 	=> __('<b>> 768px</b>', 'mfn-opts'),
					'options' 	=> array(
						'sticky'		=> __('Sticky', 'mfn-opts'),
					),
					'class'		=> 'mhb-opt',
				),

				array(
					'id'		=> 'responsive-header-mobile',
					'type' 		=> 'checkbox',
					'title' 	=> __('Mobile options', 'mfn-opts'),
					'sub_desc' 	=> __('<b>< 768px</b>', 'mfn-opts'),
					'options' 	=> array(
						'minimal'		=> __('Minimal', 'mfn-opts'),
						'sticky'		=> __('Sticky<span>works only with Sticky Header: ON</span>', 'mfn-opts'),
						'transparent'	=> __('Transparent', 'mfn-opts'),
					),
					'class'		=> 'mhb-opt',
				),

				// header | minimal
				array(
					'id' 			=> 'responsive-info-header-minimal',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __( 'Header Minimal<span>for Mobile Header: Minimal</span>', 'mfn-opts' ),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id'		=> 'responsive-header-minimal',
					'type' 		=> 'radio_img',
					'title' 	=> __('Layout', 'mfn-opts'),
					'sub_desc' 	=> __('<b>< 768px</b>', 'mfn-opts'),
					'desc' 		=> __('Do not use centered logo with more than 2 Icons in Top Bar', 'mfn-opts'),
					'options' 	=> array(
						'mr-ll' 	=> array('title' => 'Menu right | Logo left', 'img' => MFN_OPTIONS_URI.'img/select/mobile-minimal/1.png'),
						'mr-lc' 	=> array('title' => 'Menu right | Logo center', 'img' => MFN_OPTIONS_URI.'img/select/mobile-minimal/2.png'),
						'mr-lr' 	=> array('title' => 'Menu right | Logo right', 'img' => MFN_OPTIONS_URI.'img/select/mobile-minimal/3.png'),
						'ml-ll' 	=> array('title' => 'Menu left | Logo left', 'img' => MFN_OPTIONS_URI.'img/select/mobile-minimal/4.png'),
						'ml-lc' 	=> array('title' => 'Menu left | Logo center', 'img' => MFN_OPTIONS_URI.'img/select/mobile-minimal/5.png'),
						'ml-lr' 	=> array('title' => 'Menu left | Logo right', 'img' => MFN_OPTIONS_URI.'img/select/mobile-minimal/6.png'),
					),
					'class'		=> 'wide short mhb-opt',
					'std' 		=> 'mr-ll',
				),

				// top bar
				array(
					'id' 			=> 'responsive-info-top-bar',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __( 'Top Bar', 'mfn-opts' ),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id' 		=> 'responsive-top-bar',
					'type' 		=> 'select',
					'title' 	=> __('Icons', 'mfn-opts'),
					'sub_desc' 	=> __('<b>< 768px</b>', 'mfn-opts'),
					'desc' 		=> __('<b>Align</b> works only for <b>Default Header</b> for Minimal Header please use Style select above', 'mfn-opts'),
					'options' 	=> array(
						'center'	=> __('Align Center', 'mfn-opts'),
						'left' 		=> __('Align Left', 'mfn-opts'),
						'right'		=> __('Align Right', 'mfn-opts'),
						'hide'		=> __('HIDE Icons & Action Button', 'mfn-opts'),
					),
					'class'		=> 'mhb-opt',
				),

				// menu
				array(
					'id' 			=> 'responsive-info-menu',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __( 'Menu', 'mfn-opts' ),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id' 		=> 'mobile-menu-initial',
					'type' 		=> 'sliderbar',
					'title' 	=> __( 'Mobile breakpoint', 'mfn-opts' ),
					'sub_desc'=> __( 'Width at which the mobile menu is turned on', 'mfn-opts' ),
					'desc' 		=> __( 'Default: 1240px<br />Values <b>less than 1240</b> are for menu with small amount of items<br />Values <b>less than 950</b> are not suitable for Header Creative with Mega Menu', 'mfn-opts' ),
					'param'	 	=> array(
						'min' 		=> 768,
						'max' 		=> 1240,
					),
					'std' 		=> 1240,
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 		=> 'mobile-menu',
					'type' 		=> 'select',
					'title' 	=> __( 'Menu', 'mfn-opts' ),
					'sub_desc' 	=> __( 'Custom mobile main menu<br /><b>< 768px</b>', 'mfn-opts' ),
					'desc' 		=> __( 'Overrides <b>all</b> other menu select options', 'mfn-opts' ),
					'options'	=> mfna_menu(),
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 			=> 'responsive-mobile-menu',
					'type' 		=> 'select',
					'title' 	=> __( 'Style', 'mfn-opts' ),
					'sub_desc'=> __( 'Responsive Menu Style', 'mfn-opts' ),
					'desc' 		=> __( 'This option also <b>affects</b> Header Simple & Empty on desktop', 'mfn-opts' ),
					'options' => array(
						'side-slide' => __( 'Side Slide', 'mfn-opts' ),
						'' => __( 'Classic', 'mfn-opts' ),
					),
					'std'			=> 'side-slide',
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 		=> 'responsive-side-slide-width',
					'type' 		=> 'sliderbar',
					'title' 	=> __( 'Side Slide | Width', 'mfn-opts' ),
					'desc' 		=> __( 'Default: 250px', 'mfn-opts' ),
					'param'	 	=> array(
						'min' 		=> 150,
						'max' 		=> 500,
					),
					'std' 		=> 250,
					'class'		=> 'mhb-opt',
				),

				array(
					'id'		=> 'responsive-side-slide',
					'type' 		=> 'checkbox',
					'title' 	=> __('Side Slide | Hide', 'mfn-opts'),
					'desc' 		=> __('Works with Side Slide menu style selected above', 'mfn-opts'),
					'options' 	=> array(
						'button'	=> __('Action Button', 'mfn-opts'),
						'icons'		=> __('Icons', 'mfn-opts'),
						'social'	=> __('Social Icons', 'mfn-opts'),
					),
					'class'		=> 'mhb-opt',
				),

				// menu | button
				array(
					'id' 		=> 'responsive-info-menu-button',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Menu | Button', 'mfn-opts'),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id'		=> 'header-menu-text',
					'type'		=> 'text',
					'title'		=> __('Button | Text', 'mfn-opts'),
					'desc'		=> __('This text will be used instead of the menu icon', 'mfn-opts'),
					'class'		=> 'small-text mhb-opt',
				),

				array(
					'id'			=> 'header-menu-mobile-sticky',
					'type'		=> 'switch',
					'title'		=> __( 'Button | Sticky', 'mfn-opts' ),
					'desc'		=> __( 'Sticky Menu Button <b>on mobile</b> < 768px', 'mfn-opts' ),
					'options'	=> array( '0' => 'Off', '1' => 'On' ),
					'std'			=> '0',
					'class'		=> 'mhb-opt',
				),

			),
		);

		// SEO ===================

		// SEO -----
		$sections['seo'] = array(
			'title' 	=> __( 'General', 'mfn-opts' ),
			'icon' 		=> MFN_OPTIONS_URI. 'img/icons/sub.png',
			'fields'	=> array(

				array(
					'id' 		=> 'seo-info-google',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __( 'Google', 'mfn-opts' ),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'google-analytics',
					'type' 		=> 'textarea',
					'title' 	=> __( 'Google | Analytics', 'mfn-opts' ),
					'sub_desc' 	=> __( 'Paste your Google Analytics code here', 'mfn-opts' ),
					'desc' 		=> __( 'Code will be included <b>before</b> the closing <b>&lt;/head&gt;</b> tag', 'mfn-opts' ),
				),

				array(
					'id' 		=> 'facebook-pixel',
					'type' 		=> 'textarea',
					'title' 	=> __( 'Facebook | Pixel', 'mfn-opts' ),
					'sub_desc' 	=> __( 'Paste your Facebook Pixel code here', 'mfn-opts' ),
					'desc' 		=> __( 'Code will be included <b>before</b> the closing <b>&lt;/head&gt;</b> tag', 'mfn-opts' ),
				),

				array(
					'id' 		=> 'google-remarketing',
					'type' 		=> 'textarea',
					'title' 	=> __( 'Google | Remarketing', 'mfn-opts' ),
					'sub_desc' 	=> __( 'Paste your Google Remarketing code here', 'mfn-opts' ),
					'desc' 		=> __( 'Code will be included <b>before</b> the closing <b>&lt;/body&gt;</b> tag', 'mfn-opts' ),
				),

				array(
					'id' 		=> 'seo-info-fields',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __( 'SEO Fields', 'mfn-opts' ),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'mfn-seo',
					'type' 		=> 'switch',
					'title' 	=> __( 'Use built-in fields', 'mfn-opts' ),
					'desc' 		=> __( 'Turn it <b>OFF</b> if you want to use external SEO or share plugin', 'mfn-opts' ),
					'options' 	=> array( '1' => 'On', '0' => 'Off' ),
					'std' 		=> '1'
				),

				array(
					'id' 		=> 'meta-description',
					'type' 		=> 'text',
					'title' 	=> __( 'Meta | Description', 'mfn-opts' ),
					'desc' 		=> __( 'May be overwritten for single posts, pages, portfolio', 'mfn-opts' ),
					'std' 		=> get_bloginfo( 'description' ),
				),

				array(
					'id' 		=> 'meta-keywords',
					'type' 		=> 'text',
					'title' 	=> __( 'Meta | Keywords', 'mfn-opts' ),
					'desc' 		=> __( 'May be overwritten for single posts, pages, portfolio', 'mfn-opts' ),
				),

				array(
					'id' 			=> 'mfn-seo-og-image',
					'type' 		=> 'upload',
					'title' 	=> __( 'Open Graph | Image', 'mfn-opts' ),
					'sub_desc'=> __( 'e.g. Facebook share image', 'mfn-opts' ),
					'desc' 		=> __( 'May be overwritten for single posts, pages, portfolio', 'mfn-opts' ),
				),

				array(
					'id' => 'seo-fb-app-id',
					'type' => 'text',
					'title' => __( 'Facebook App ID', 'mfn-opts' ),
				),

				array(
					'id' 		=> 'seo-info-advanced',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __( 'Advanced', 'mfn-opts' ),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'mfn-seo-schema-type',
					'type' 		=> 'switch',
					'title' 	=> __( 'Schema Type', 'mfn-opts' ),
					'desc' 		=> __( 'Add Schema Type to &lt;html&gt; tag', 'mfn-opts' ),
					'options' 	=> array( '1' => 'On', '0' => 'Off' ),
					'std' 		=> '1'
				),

			),
		);

		// Social Icons ==========

		// Social Icons ------
		$sections['social'] = array(
			'title' => __( 'General', 'mfn-opts' ),
			'icon' => MFN_OPTIONS_URI. 'img/icons/sub.png',
			'fields' => array(

				array(
					'id' => 'social-attr',
					'type' => 'checkbox',
					'title' => __( 'Link attributes', 'mfn-opts' ),
					'options' 	=> array(
						'blank'	=> 'target="_blank"',
						'nofollow' => 'rel="nofollow"',
					),
				),

				array(
					'id' => 'social-skype',
					'type' => 'text',
					'title' => '<i class="icon-skype"></i> Skype',
					'desc' => __( 'Skype login. You can use <strong>callto:</strong> or <strong>skype:</strong> prefix' , 'mfn-opts' ),
				),

				array(
					'id' => 'social-whatsapp',
					'type' => 'text',
					'title' => '<i class="icon-whatsapp"></i> WhatsApp',
					'desc' => __( 'WhatsApp URL. You can use <strong>whatsapp:</strong> prefix' , 'mfn-opts' ),
				),

				array(
					'id' => 'social-facebook',
					'type' => 'text',
					'title' => '<i class="icon-facebook"></i> Facebook',
					'desc' => __('Link to the profile page', 'mfn-opts'),
				),

				array(
					'id' => 'social-twitter',
					'type' => 'text',
					'title' => '<i class="icon-twitter"></i> Twitter',
					'desc' => __('Link to the profile page', 'mfn-opts'),
				),

				array(
					'id' => 'social-vimeo',
					'type' => 'text',
					'title' => '<i class="icon-vimeo"></i> Vimeo',
					'desc' => __('Link to the profile page', 'mfn-opts'),
				),

				array(
					'id' => 'social-youtube',
					'type' => 'text',
					'title' => '<i class="icon-play"></i> YouTube',
					'desc' => __('Link to the profile page', 'mfn-opts'),
				),

				array(
					'id' => 'social-flickr',
					'type' => 'text',
					'title' => '<i class="icon-flickr"></i> Flickr',
					'desc' => __('Link to the profile page', 'mfn-opts'),
				),

				array(
					'id' => 'social-linkedin',
					'type' => 'text',
					'title' => '<i class="icon-linkedin"></i> LinkedIn',
					'desc' => __('Link to the profile page', 'mfn-opts'),
				),

				array(
					'id' => 'social-pinterest',
					'type' => 'text',
					'title' => '<i class="icon-pinterest"></i> Pinterest',
					'desc' => __('Link to the profile page', 'mfn-opts'),
				),

				array(
					'id' => 'social-dribbble',
					'type' => 'text',
					'title' => '<i class="icon-dribbble"></i> Dribbble',
					'desc' => __('Link to the profile page', 'mfn-opts'),
				),

				array(
					'id' => 'social-instagram',
					'type' => 'text',
					'title' => '<i class="icon-instagram"></i> Instagram',
					'desc' => __('Link to the profile page', 'mfn-opts'),
				),

				array(
					'id' => 'social-snapchat',
					'type' => 'text',
					'title' => '<i class="icon-snapchat"></i> Snapchat',
					'desc' => __('Link to the profile page', 'mfn-opts'),
				),

				array(
					'id' => 'social-behance',
					'type' => 'text',
					'title' => '<i class="icon-behance"></i> Behance',
					'desc' => __('Link to the profile page', 'mfn-opts'),
				),

				array(
					'id' => 'social-tumblr',
					'type' => 'text',
					'title' => '<i class="icon-tumblr"></i> Tumblr',
					'desc' => __('Link to the profile page', 'mfn-opts'),
				),

				array(
					'id' => 'social-tripadvisor',
					'type' => 'text',
					'title' => '<i class="icon-tripadvisor"></i>&nbsp; TripAdvisor',
					'desc' => __('Link to the profile page', 'mfn-opts'),
				),

				array(
					'id' => 'social-vkontakte',
					'type' => 'text',
					'title' => '<i class="icon-vkontakte"></i> VKontakte',
					'desc' => __('Link to the profile page', 'mfn-opts'),
				),

				array(
					'id' => 'social-viadeo',
					'type' => 'text',
					'title' => '<i class="icon-viadeo"></i> Viadeo',
					'desc' => __('Link to the profile page', 'mfn-opts'),
				),

				array(
					'id' => 'social-xing',
					'type' => 'text',
					'title' => '<i class="icon-xing"></i> Xing',
					'desc' => __('Link to the profile page', 'mfn-opts'),
				),

				array(
					'id' => 'social-custom-icon',
					'type' => 'icon',
					'title' => __('Custom | Icon', 'mfn-opts'),
				),

				array(
					'id' => 'social-custom-link',
					'type' => 'text',
					'title' => __('Custom | Link', 'mfn-opts'),
					'desc' => __('To show Custom Social Icon select Icon and enter Link to the profile page', 'mfn-opts'),
				),

				array(
					'id' => 'social-custom-title',
					'type' => 'text',
					'title' => __('Custom | Title', 'mfn-opts'),
					'sub_desc' => __('Custom social icon title', 'mfn-opts'),
				),

				array(
					'id' => 'social-rss',
					'type' => 'switch',
					'title' => __('RSS', 'mfn-opts'),
					'desc' => __('Show the RSS icon', 'mfn-opts'),
					'options'	=> array('1' => 'On','0' => 'Off'),
					'std' => '0'
				),

			),
		);

		// Addons, Plugins =======

		// Addons -----
		$sections['addons'] = array(
			'title'		=> __('Addons', 'mfn-opts'),
			'icon' 		=> MFN_OPTIONS_URI. 'img/icons/sub.png',
			'fields'	=> array(

				// contact form 7
				array(
					'id' 		=> 'addons-info-cf7',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Contact Form 7', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'cf7-error',
					'type' 		=> 'select',
					'title' 	=> __('Contact Form 7 | Form Error', 'mfn-opts'),
					'options' 	=> array(
						'' 			=> __('Simple X icon', 'mfn-opts'),
						'message' 	=> __('Full error message below field', 'mfn-opts'),
					),
				),

				// parallax
				array(
					'id' 		=> 'addons-info-parallax',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Parallax', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'parallax',
					'type' 		=> 'select',
					'title' 	=> __('Parallax | Plugin', 'mfn-opts'),
					'options' 	=> array(
						'translate3d' 			=> __('Translate3d', 'mfn-opts'),
						'translate3d no-safari' => __('Translate3d | Enllax in Safari (in some cases may run smoother)', 'mfn-opts'),
						'enllax' 				=> __('Enllax', 'mfn-opts'),
						'stellar' 				=> __('Stellar | old', 'mfn-opts'),
					),
				),

				// lightbox
				array(
					'id' 		=> 'addons-info-lightbox',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Lightbox', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				/**
				 * @since 17.8.3
				 * Option name 'prettyphoto-options' left only for backward compatibility
				 */
				array(
					'id' 				=> 'prettyphoto-options',
					'type' 			=> 'checkbox',
					'title' 		=> __( 'Lightbox | Options', 'mfn-opts' ),
					'options' 	=> array(
						'disable'					=> __( 'Disable<span>Disable Magnific Popup if you prefer to use other plugin</span>', 'mfn-opts' ),
						'disable-mobile'	=> __( 'Disable on Mobile only', 'mfn-opts' ),
						'title'						=> __( 'Show image Alt text as caption for lightbox image', 'mfn-opts' ),
					),
				),

				// addons
				array(
					'id' 		=> 'addons-info-addons',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Addons', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'sc-gallery-disable',
					'type' 		=> 'switch',
					'title' 	=> __('Gallery Shortcode | Disable', 'mfn-opts'),
					'sub_desc' 	=> __('Disable Theme Gallery Shortcode', 'mfn-opts'),
					'desc' 		=> __('Turn it <b>on</b> if you want to use external gallery plugin or Jetpack', 'mfn-opts'),
					'options' 	=> array( '0' => 'Off', '1' => 'On' ),
					'std' 		=> '0'
				),

			),
		);

		// Plugins ------
		$sections['plugins'] = array(
			'title' => __('Premium Plugins', 'mfn-opts'),
			'icon' => MFN_OPTIONS_URI. 'img/icons/sub.png',
			'fields' => array(

				array(
					'id' 		=> 'plugins-info',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('If you <b>purchased an extra license</b> from plugin author you can <b>disable the bundled</b> option for plugins you have purchased to get <b>support from the plugin author and premium features</b>.<br />After that please go to plugin settings page for more details.', 'mfn-opts'),
					'class' 	=> 'mfn-info desc',
				),

				array(
					'id' 		=> 'plugin-rev',
					'type' 		=> 'select',
					'title' 	=> __('Slider Revolution', 'mfn-opts'),
					'options' 	=> array(
						''			=> __('Bundled with the theme', 'mfn-opts'),
						'disable'	=> __('I purchased a licence to unlock premium features', 'mfn-opts'),
					),
				),

				array(
					'id' 		=> 'plugin-visual',
					'type' 		=> 'select',
					'title' 	=> __('WPBakery Page Builder', 'mfn-opts'),
					'options' 	=> array(
						''			=> __('Bundled with the theme', 'mfn-opts'),
						'disable'	=> __('I purchased a licence to unlock premium features', 'mfn-opts'),
					),
				),

				array(
					'id' 		=> 'plugin-layer',
					'type' 		=> 'select',
					'title' 	=> __('Layer Slider', 'mfn-opts'),
					'options' 	=> array(
						''			=> __('Bundled with the theme', 'mfn-opts'),
						'disable'	=> __('I purchased a licence to unlock premium features', 'mfn-opts'),
					),
				),

			),
		);

		// Colors ================

		// General ------
		$sections['colors-general'] = array(
			'title' => __('General', 'mfn-opts'),
			'icon' => MFN_OPTIONS_URI. 'img/icons/sub.png',
			'fields' => array(

				array(
					'id' 		=> 'colors-general-info-skin',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Skin', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'skin',
					'type' 		=> 'select',
					'title' 	=> __('Theme Skin', 'mfn-opts'),
					'sub_desc' 	=> __('Choose one of the predefined styles or set your own colors', 'mfn-opts'),
					'desc' 		=> __('<strong>Important:</strong> Color options can be used only with the <strong>Custom Skin</strong>', 'mfn-opts'),
					'options' 	=> mfna_skin(),
					'std' 		=> 'custom',
				),

				array(
					'id' 		=> 'color-one',
					'type' 		=> 'color',
					'title' 	=> __('One Color', 'mfn-opts'),
					'sub_desc' 	=> __('One Color Skin Generator', 'mfn-opts'),
					'desc' 		=> __('for <strong>One Color Skin</strong>', 'mfn-opts'),
					'std' 		=> '#0095eb',
				),

				array(
					'id' 		=> 'colors-general-info-background',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Background', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'background-html',
					'type' 		=> 'color',
					'title' 	=> __('Body background', 'mfn-opts'),
					'desc' 		=> __('for <strong>Boxed Layout</strong>', 'mfn-opts'),
					'std' 		=> '#FCFCFC',
				),

				array(
					'id' 		=> 'background-body',
					'type' 		=> 'color',
					'title' 	=> __('Content background', 'mfn-opts'),
					'std' 		=> '#FCFCFC',
				),

			),
		);

		// Header ------
		$sections['colors-header'] = array(
			'title' => __( 'Header', 'mfn-opts' ),
			'icon' => MFN_OPTIONS_URI. 'img/icons/sub.png',
			'fields' => array(

				array(
					'id' => 'background-header',
					'type' => 'color',
					'title' => __( 'Header background', 'mfn-opts' ),
					'std' => '#000119',
					'class' => 'mhb-opt',
				),

				// top bar
				array(
					'id' => 'colors-info-top-bar',
					'type' => 'info',
					'title' => '',
					'desc' => __('Top Bar', 'mfn-opts'),
					'class' => 'mfn-info mhb-opt',
				),

				array(
					'id' => 'background-top-left',
					'type' => 'color',
					'title' => __('Top Bar Left | background', 'mfn-opts'),
					'desc' => __('This is also Mobile Header & Top Bar Background for some Header Styles', 'mfn-opts'),
					'std' => '#ffffff',
					'class' => 'mhb-opt',
				),

				array(
					'id' => 'background-top-middle',
					'type' => 'color',
					'title' => __('Top Bar Middle | background', 'mfn-opts'),
					'desc' => __('for <strong>Header Modern</strong>', 'mfn-opts'),
					'std' => '#e3e3e3',
					'class' => 'mhb-opt',
				),

				array(
					'id' => 'background-top-right',
					'type' => 'color',
					'title' => __('Top Bar Right | background', 'mfn-opts'),
					'std' => '#f5f5f5',
					'class' => 'mhb-opt',
				),

				array(
					'id' => 'color-top-right-a',
					'type' => 'color',
					'title' => __('Top Bar Right | icon color', 'mfn-opts'),
					'std' => '#333333',
					'class' => 'mhb-opt',
				),

				array(
					'id' => 'border-top-bar',
					'type' => 'color',
					'title' => __('Top Bar | border bottom', 'mfn-opts'),
					'sub_desc' => __('optional', 'mfn-opts'),
					'std' => '',
					'class' => 'mhb-opt',
					'alpha' => true,
				),

				// action button
				array(
					'id' 			=> 'colors-info-action-button',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __( 'Action Button', 'mfn-opts' ),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id' 			=> 'background-action-button',
					'type' 		=> 'color',
					'title' 	=> __( 'Background', 'mfn-opts' ),
					'std' 		=> '#f7f7f7',
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 			=> 'color-action-button',
					'type' 		=> 'color',
					'title' 	=> __( 'Color', 'mfn-opts' ),
					'std' 		=> '#747474',
					'class'		=> 'mhb-opt',
				),

				// search
				array(
					'id' 		=> 'colors-info-search',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Search', 'mfn-opts'),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id' 		=> 'background-search',
					'type' 		=> 'color',
					'title' 	=> __('Background', 'mfn-opts'),
					'std' 		=> '#0095eb',
					'class'		=> 'mhb-opt',
				),

				// subheader
				array(
					'id' 		=> 'colors-info-subheader',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Subheader', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'background-subheader',
					'type' 		=> 'color',
					'title' 	=> __('Background', 'mfn-opts'),
					'std' 		=> '#F7F7F7',
				),

				array(
					'id' 		=> 'color-subheader',
					'type' 		=> 'color',
					'title' 	=> __('Title color', 'mfn-opts'),
					'std' 		=> '#444444',
				),

			),
		);

		// Menu ------
		$sections['colors-menu'] = array(
			'title' => __('Menu', 'mfn-opts'),
			'icon' => MFN_OPTIONS_URI. 'img/icons/sub.png',
			'fields' => array(

				// menu
				array(
					'id' 		=> 'colors-info-menu',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Menu', 'mfn-opts'),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id' 		=> 'color-menu-a',
					'type' 		=> 'color',
					'title' 	=> __('Link color', 'mfn-opts'),
					'std' 		=> '#444444',
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 		=> 'color-menu-a-active',
					'type' 		=> 'color',
					'title' 	=> __('Active Link color', 'mfn-opts'),
					'desc' 		=> __('This is also Active Link Border', 'mfn-opts'),
					'std' 		=> '#0095eb',
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 		=> 'background-menu-a-active',
					'type' 		=> 'color',
					'title' 	=> __('Active Link background', 'mfn-opts'),
					'desc' 		=> __('Header Plain & Menu Highlight', 'mfn-opts'),
					'std' 		=> '#F2F2F2',
					'class'		=> 'mhb-opt',
				),

				// submenu
				array(
					'id' 		=> 'colors-info-submenu',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Submenu', 'mfn-opts'),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id' 		=> 'background-submenu',
					'type' 		=> 'color',
					'title' 	=> __('Background', 'mfn-opts'),
					'std' 		=> '#F2F2F2',
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 		=> 'color-submenu-a',
					'type' 		=> 'color',
					'title' 	=> __('Link color', 'mfn-opts'),
					'std' 		=> '#5f5f5f',
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 		=> 'color-submenu-a-hover',
					'type' 		=> 'color',
					'title' 	=> __('Hover Link color', 'mfn-opts'),
					'std' 		=> '#2e2e2e',
					'class'		=> 'mhb-opt',
				),

				// responsive
				array(
					'id' 		=> 'colors-info-menu-responsive',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Menu Button <span>Responsive, Header Creative, Simple & Empty</span>', 'mfn-opts'),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id' 		=> 'color-menu-responsive-icon',
					'type' 		=> 'color',
					'title' 	=> __('Button color', 'mfn-opts'),
					'std' 		=> '#0095eb',
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 			=> 'background-menu-responsive-icon',
					'type' 		=> 'color',
					'title' 	=> __( 'Button background', 'mfn-opts' ),
					'sub_desc'=> __( 'optional', 'mfn-opts' ),
					'std' 		=> '',
					'class'		=> 'mhb-opt',
				),

				// styles
				array(
					'id' 		=> 'colors-info-menu-styles',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Styles<span>for specific header styles</span>', 'mfn-opts'),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id' 		=> 'background-overlay-menu',
					'type' 		=> 'color',
					'title' 	=> __('Overlay Menu<br />Menu background', 'mfn-opts'),
					'std' 		=> '#0095eb',
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 		=> 'background-overlay-menu-a',
					'type' 		=> 'color',
					'title' 	=> __('Overlay Menu<br />Link color', 'mfn-opts'),
					'std' 		=> '#FFFFFF',
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 		=> 'background-overlay-menu-a-active',
					'type' 		=> 'color',
					'title' 	=> __('Overlay Menu<br />Active Link color', 'mfn-opts'),
					'std' 		=> '#B1DCFB',
					'class'		=> 'mhb-opt',
				),

				array(
					'id' => 'border-menu-plain',
					'type' => 'color',
					'title' => __('Plain<br />Border color', 'mfn-opts'),
					'std' => '#F2F2F2',
					'class' => 'mhb-opt',
					'alpha' => true,
				),

				// side slide
				array(
					'id' 		=> 'colors-info-side-slide',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Side Slide<span>responsive menu style</span>', 'mfn-opts'),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id' 		=> 'background-side-menu',
					'type' 		=> 'color',
					'title' 	=> __('Background', 'mfn-opts'),
					'std' 		=> '#191919',
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 		=> 'color-side-menu-a',
					'type' 		=> 'color',
					'title' 	=> __('Link color', 'mfn-opts'),
					'sub_desc' 	=> __('Text, Link & Icon color', 'mfn-opts'),
					'std' 		=> '#A6A6A6',
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 			=> 'color-side-menu-a-hover',
					'type' 		=> 'color',
					'title' 	=> __( 'Active Link color', 'mfn-opts' ),
					'std' 		=> '#FFFFFF',
					'class'		=> 'mhb-opt',
				),

			),
		);

		// Colors | Action Bar ------
		$sections['colors-action'] = array(
			'title' => __('Action Bar', 'mfn-opts'),
			'icon' => MFN_OPTIONS_URI. 'img/icons/sub.png',
			'fields' => array(

				// desktop & tablet
				array(
					'id' 		=> 'colors-info-action-bar',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Desktop & Tablet<span>> 768px</span>', 'mfn-opts'),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id' 		=> 'background-action-bar',
					'type' 		=> 'color',
					'title' 	=> __('Background', 'mfn-opts'),
					'desc' 		=> __('For some Header Styles', 'mfn-opts'),
					'std' 		=> '#292b33',
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 		=> 'color-action-bar',
					'type' 		=> 'color',
					'title' 	=> __('Text color', 'mfn-opts'),
					'std' 		=> '#bbbbbb',
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 		=> 'color-action-bar-a',
					'type' 		=> 'color',
					'title' 	=> __('Link | Color', 'mfn-opts'),
					'std' 		=> '#0095eb',
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 		=> 'color-action-bar-a-hover',
					'type' 		=> 'color',
					'title' 	=> __('Link | Hover color', 'mfn-opts'),
					'std' 		=> '#007cc3',
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 		=> 'color-action-bar-social',
					'type' 		=> 'color',
					'title' 	=> __('Social Icon | Color', 'mfn-opts'),
					'desc' 		=> __('This is also Social Menu Link color', 'mfn-opts'),
					'std' 		=> '#bbbbbb',
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 		=> 'color-action-bar-social-hover',
					'type' 		=> 'color',
					'title' 	=> __('Social Icon | Hover color', 'mfn-opts'),
					'desc' 		=> __('This is also Social Menu Link hover color', 'mfn-opts'),
					'std' 		=> '#FFFFFF',
					'class'		=> 'mhb-opt',
				),

				// mobile
				array(
					'id' 		=> 'colors-info-action-bar-mobile',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Mobile<span>< 768px</span>', 'mfn-opts'),
					'class' 	=> 'mfn-info mhb-opt',
				),

				array(
					'id' 		=> 'mobile-background-action-bar',
					'type' 		=> 'color',
					'title' 	=> __('Background', 'mfn-opts'),
					'std' 		=> '#FFFFFF',
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 		=> 'mobile-color-action-bar',
					'type' 		=> 'color',
					'title' 	=> __('Text color', 'mfn-opts'),
					'std' 		=> '#222222',
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 		=> 'mobile-color-action-bar-a',
					'type' 		=> 'color',
					'title' 	=> __('Link | Color', 'mfn-opts'),
					'std' 		=> '#0095eb',
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 		=> 'mobile-color-action-bar-a-hover',
					'type' 		=> 'color',
					'title' 	=> __('Link | Hover color', 'mfn-opts'),
					'std' 		=> '#007cc3',
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 		=> 'mobile-color-action-bar-social',
					'type' 		=> 'color',
					'title' 	=> __('Social Icon | Color', 'mfn-opts'),
					'desc' 		=> __('This is also Social Menu Link color', 'mfn-opts'),
					'std' 		=> '#bbbbbb',
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 		=> 'mobile-color-action-bar-social-hover',
					'type' 		=> 'color',
					'title' 	=> __('Social Icon | Hover color', 'mfn-opts'),
					'desc' 		=> __('This is also Social Menu Link hover color', 'mfn-opts'),
					'std' 		=> '#777777',
					'class'		=> 'mhb-opt',
				),

			),
		);

		// Content ------
		$sections['content'] = array(
			'title' => __('Content', 'mfn-opts'),
			'icon' => MFN_OPTIONS_URI. 'img/icons/sub.png',
			'fields' => array(

				array(
					'id' => 'color-theme',
					'type' => 'color',
					'title' => __('Theme | color', 'mfn-opts'),
					'sub_desc' => __('Highlighted button background, some icons and other small elements', 'mfn-opts'),
					'desc' => __('You can use <strong>.themecolor</strong> and <strong>.themebg</strong> classes in your content', 'mfn-opts'),
					'std' => '#0095eb'
				),

				array(
					'id' 			=> 'color-text',
					'type' 		=> 'color',
					'title' 	=> __( 'Text | color', 'mfn-opts' ),
					'sub_desc'=> __( 'Content text color', 'mfn-opts' ),
					'std' 		=> '#626262'
				),

				array(
					'id' 			=> 'color-selection',
					'type' 		=> 'color',
					'title' 	=> __( 'Selection | color', 'mfn-opts' ),
					'std' 		=> '#0095eb'
				),

				// link
				array(
					'id' 			=> 'colors-info-link',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __( 'Link', 'mfn-opts' ),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'color-a',
					'type' 		=> 'color',
					'title' 	=> __('Link | color', 'mfn-opts'),
					'std' 		=> '#0095eb'
				),

				array(
					'id' 		=> 'color-a-hover',
					'type' 		=> 'color',
					'title' 	=> __('Link | hover color', 'mfn-opts'),
					'std' 		=> '#007cc3'
				),

				array(
					'id' 		=> 'color-fancy-link',
					'type' 		=> 'color',
					'title' 	=> __('Fancy Link | color', 'mfn-opts'),
					'desc' 		=> __('For some link styles only', 'mfn-opts'),
					'std' 		=> '#656B6F'
				),

				array(
					'id' 		=> 'background-fancy-link',
					'type' 		=> 'color',
					'title' 	=> __('Fancy Link | background', 'mfn-opts'),
					'desc' 		=> __('For some link styles only', 'mfn-opts'),
					'std' 		=> '#0095eb'
				),

				array(
					'id' 		=> 'color-fancy-link-hover',
					'type' 		=> 'color',
					'title' 	=> __('Fancy Link | hover color', 'mfn-opts'),
					'desc' 		=> __('For some link styles only', 'mfn-opts'),
					'std' 		=> '#0095eb'
				),

				array(
					'id' 		=> 'background-fancy-link-hover',
					'type' 		=> 'color',
					'title' 	=> __('Fancy Link | hover background', 'mfn-opts'),
					'desc' 		=> __('For some link styles only', 'mfn-opts'),
					'std' 		=> '#007cc3'
				),

				// button
				array(
					'id' 			=> 'colors-info-button',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __( 'Button', 'mfn-opts' ),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 			=> 'background-button',
					'type' 		=> 'color',
					'title' 	=> __( 'Button | background', 'mfn-opts' ),
					'std' 		=> '#f7f7f7',
				),

				array(
					'id' 			=> 'color-button',
					'type' 		=> 'color',
					'title' 	=> __( 'Button | color', 'mfn-opts' ),
					'std' 		=> '#747474',
				),

				array(
					'id' 			=> 'color-button-theme',
					'type' 		=> 'color',
					'title' 	=> __( 'Button theme | color', 'mfn-opts' ),
					'sub_desc'=> __( 'Highlighted button text color', 'mfn-opts' ),
					'std' 		=> '#ffffff',
				),

				// image frame
				array(
					'id' 		=> 'colors-info-imageframe',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Image Frame', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'border-imageframe',
					'type' 		=> 'color',
					'title'		=> __('Image Frame | Border color', 'mfn-opts'),
					'std' 		=> '#f8f8f8',
				),

				array(
					'id' 		=> 'background-imageframe-link',
					'type' 		=> 'color',
					'title'		=> __('Image Frame | Link background', 'mfn-opts'),
					'desc'		=> __('This is also Image Frame Hover Link color', 'mfn-opts'),
					'std' 		=> '#0095eb',
				),

				array(
					'id' 		=> 'color-imageframe-link',
					'type' 		=> 'color',
					'title'		=> __('Image Frame | Link color', 'mfn-opts'),
					'desc'		=> __('This is also Image Frame Hover Link background', 'mfn-opts'),
					'std' 		=> '#ffffff',
				),

				array(
					'id' 		=> 'color-imageframe-mask',
					'type' 		=> 'color',
					'title'		=> __('Image Frame | Mask color', 'mfn-opts'),
					'desc'		=> __('Mask has predefined opacity 0.4', 'mfn-opts'),
					'std' 		=> '#ffffff',
				),

				// inline shortcodes
				array(
					'id' 		=> 'colors-info-inline-shortcodes',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Inline Shortcodes', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'background-highlight',
					'type' 		=> 'color',
					'title' 	=> __('Dropcap & Highlight background', 'mfn-opts'),
					'std' 		=> '#0095eb'
				),

				array(
					'id' 		=> 'color-hr',
					'type' 		=> 'color',
					'title' 	=> __('Hr color', 'mfn-opts'),
					'desc' 		=> __('Dots, ZigZag & Theme Color', 'mfn-opts'),
					'std' 		=> '#0095eb'
				),

				array(
					'id' 		=> 'color-list',
					'type' 		=> 'color',
					'title' 	=> __('List color', 'mfn-opts'),
					'desc' 		=> __('Ordered, Unordered & Bullets List', 'mfn-opts'),
					'std' 		=> '#737E86'
				),

				array(
					'id' 		=> 'color-note',
					'type' 		=> 'color',
					'title' 	=> __('Note color', 'mfn-opts'),
					'desc' 		=> __('eg. Blog meta, Filters, Widgets meta', 'mfn-opts'),
					'std' 		=> '#a8a8a8'
				),

				// section
				array(
					'id' 		=> 'colors-info-section',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Section', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'background-highlight-section',
					'type' 		=> 'color',
					'title' 	=> __('Highlight Section background', 'mfn-opts'),
					'std' 		=> '#0095eb'
				),

			),
		);

		// Footer ------
		$sections['colors-footer'] = array(
			'title' => __('Footer', 'mfn-opts'),
			'icon' => MFN_OPTIONS_URI .'img/icons/sub.png',
			'fields' => array(

				array(
					'id' => 'color-footer-theme',
					'type' => 'color',
					'title' => __('Theme color', 'mfn-opts'),
					'sub_desc' => __('Color for icons and other small elements', 'mfn-opts'),
					'desc' => __('You can use <strong>.themecolor</strong> and <strong>.themebg</strong> classes in your footer content', 'mfn-opts'),
					'std' => '#0095eb'
				),

				array(
					'id' => 'background-footer',
					'type' => 'color',
					'title' => __('Background', 'mfn-opts'),
					'std' => '#292b33',
				),

				array(
					'id' => 'color-footer',
					'type' => 'color',
					'title' => __('Text color', 'mfn-opts'),
					'std' => '#cccccc',
				),

				array(
					'id' => 'color-footer-heading',
					'type' => 'color',
					'title' => __('Heading color', 'mfn-opts'),
					'std' => '#ffffff',
				),

				array(
					'id' => 'color-footer-note',
					'type' => 'color',
					'title' => __('Note color', 'mfn-opts'),
					'desc' => __('eg. Widget meta', 'mfn-opts'),
					'std' => '#a8a8a8',
				),

				array(
					'id' => 'border-copyright',
					'type' => 'color',
					'title' => __('Copyright border', 'mfn-opts'),
					'std' => 'rgba(255,255,255,0.1)',
					'alpha' => true,
				),

				// link
				array(
					'id' 		=> 'colors-info-footer-link',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Link', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'color-footer-a',
					'type' 		=> 'color',
					'title' 	=> __('Link | color', 'mfn-opts'),
					'std' 		=> '#0095eb',
				),

				array(
					'id' 		=> 'color-footer-a-hover',
					'type' 		=> 'color',
					'title' 	=> __('Link | hover color', 'mfn-opts'),
					'std' 		=> '#007cc3',
				),

				// social
				array(
					'id' 		=> 'colors-info-footer-social',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Social', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'color-footer-social',
					'type' 		=> 'color',
					'title' 	=> __('Social Icon | Color', 'mfn-opts'),
					'desc' 		=> __('This is also Social Menu Bottom link color', 'mfn-opts'),
					'std' 		=> '#65666C',
				),

				array(
					'id' 		=> 'color-footer-social-hover',
					'type' 		=> 'color',
					'title' 	=> __('Social Icon | Hover color', 'mfn-opts'),
					'desc' 		=> __('This is also Social Menu Bottom link hover color', 'mfn-opts'),
					'std' 		=> '#FFFFFF',
				),

				// social
				array(
					'id' 			=> 'colors-info-footer-backtotop',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __( 'Back to Top & Popup Contact Form', 'mfn-opts' ),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 			=> 'color-footer-backtotop',
					'type' 		=> 'color',
					'title' 	=> __( 'Button color', 'mfn-opts' ),
					'std' 		=> '#65666C',
				),

				array(
					'id' 			=> 'background-footer-backtotop',
					'type' 		=> 'color',
					'title' 	=> __( 'Button background', 'mfn-opts' ),
					'sub_desc'=> __( 'optional', 'mfn-opts' ),
					'std' 		=> '',
				),

			),
		);

		// Sliding Top ------
		$sections['colors-sliding-top'] = array(
			'title' => __('Sliding Top', 'mfn-opts'),
			'icon' => MFN_OPTIONS_URI. 'img/icons/sub.png',
			'fields' => array(

				array(
					'id' 		=> 'color-sliding-top-theme',
					'type' 		=> 'color',
					'title' 	=> __('Sliding Top Theme color', 'mfn-opts'),
					'sub_desc' 	=> __('Color for icons and other small elements', 'mfn-opts'),
					'desc' 		=> __('You can use <strong>.themecolor</strong> and <strong>.themebg</strong> classes in your Sliding Top content', 'mfn-opts'),
					'std' 		=> '#0095eb'
				),

				array(
					'id' 		=> 'background-sliding-top',
					'type' 		=> 'color',
					'title' 	=> __('Sliding Top background', 'mfn-opts'),
					'std' 		=> '#545454',
				),

				array(
					'id' 		=> 'color-sliding-top',
					'type' 		=> 'color',
					'title' 	=> __('Sliding Top Text color', 'mfn-opts'),
					'std' 		=> '#cccccc',
				),

				array(
					'id' 		=> 'color-sliding-top-a',
					'type' 		=> 'color',
					'title' 	=> __('Sliding Top Link color', 'mfn-opts'),
					'std' 		=> '#0095eb',
				),

				array(
					'id' 		=> 'color-sliding-top-a-hover',
					'type' 		=> 'color',
					'title' 	=> __('Sliding Top Hover Link color', 'mfn-opts'),
					'std' 		=> '#007cc3',
				),

				array(
					'id' 		=> 'color-sliding-top-heading',
					'type' 		=> 'color',
					'title' 	=> __('Sliding Top Heading color', 'mfn-opts'),
					'std' 		=> '#ffffff',
				),

				array(
					'id' 		=> 'color-sliding-top-note',
					'type' 		=> 'color',
					'title' 	=> __('Sliding Top Note color', 'mfn-opts'),
					'desc' 		=> __('eg. Widget meta', 'mfn-opts'),
					'std' 		=> '#a8a8a8',
				),

			),
		);

		// Headings ------
		$sections['headings'] = array(
			'title' => __('Headings', 'mfn-opts'),
			'icon' => MFN_OPTIONS_URI. 'img/icons/sub.png',
			'fields' => array(

				array(
					'id' 		=> 'color-h1',
					'type' 		=> 'color',
					'title' 	=> __('Heading H1 color', 'mfn-opts'),
					'std' 		=> '#161922'
				),

				array(
					'id' 		=> 'color-h2',
					'type' 		=> 'color',
					'title' 	=> __('Heading H2 color', 'mfn-opts'),
					'std' 		=> '#161922'
				),

				array(
					'id' 		=> 'color-h3',
					'type' 		=> 'color',
					'title' 	=> __('Heading H3 color', 'mfn-opts'),
					'std' 		=> '#161922'
				),

				array(
					'id' 		=> 'color-h4',
					'type' 		=> 'color',
					'title' 	=> __('Heading H4 color', 'mfn-opts'),
					'std' 		=> '#161922'
				),

				array(
					'id' 		=> 'color-h5',
					'type' 		=> 'color',
					'title' 	=> __('Heading H5 color', 'mfn-opts'),
					'std' 		=> '#161922'
				),

				array(
					'id' 		=> 'color-h6',
					'type' 		=> 'color',
					'title' 	=> __('Heading H6 color', 'mfn-opts'),
					'std' 		=> '#161922'
				),

			),
		);

		// Shortcodes ------
		$sections['colors-shortcodes'] = array(
			'title' => __('Shortcodes', 'mfn-opts'),
			'icon' => MFN_OPTIONS_URI. 'img/icons/sub.png',
			'fields' => array(

				array(
					'id' 		=> 'color-tab-title',
					'type' 		=> 'color',
					'title'		=> __('Accordion & Tabs Active Title color', 'mfn-opts'),
					'std' 		=> '#0095eb',
				),

				array(
					'id' 		=> 'color-blockquote',
					'type' 		=> 'color',
					'title'		=> __('Blockquote color', 'mfn-opts'),
					'std' 		=> '#444444',
				),

				array(
					'id' => 'background-getintouch',
					'type' => 'color',
					'title' => __('Contact Box | background', 'mfn-opts'),
					'desc' => __('This is also Infobox background', 'mfn-opts'),
					'std' => '#0095eb',
				),

				array(
					'id' => 'color-contentlink',
					'type' => 'color',
					'title' => __('Content Link | icon color', 'mfn-opts'),
					'desc' => __('This is also Content Link hover border', 'mfn-opts'),
					'std' => '#0095eb',
				),

				array(
					'id' 		=> 'color-counter',
					'type' 		=> 'color',
					'title'		=> __('Counter Icon | color', 'mfn-opts'),
					'desc'		=> __('This is also Chart Progress color', 'mfn-opts'),
					'std' 		=> '#0095eb',
				),

				array(
					'id' 		=> 'color-iconbar',
					'type' 		=> 'color',
					'title'		=> __('Icon Bar Hover Icon color', 'mfn-opts'),
					'std' 		=> '#0095eb',
				),

				array(
					'id' 		=> 'color-iconbox',
					'type' 		=> 'color',
					'title'		=> __('Icon Box Icon color', 'mfn-opts'),
					'std' 		=> '#0095eb',
				),

				array(
					'id' 		=> 'color-list-icon',
					'type' 		=> 'color',
					'title'		=> __('List & Feature List Icon color', 'mfn-opts'),
					'std' 		=> '#0095eb',
				),

				array(
					'id' 		=> 'color-pricing-price',
					'type' 		=> 'color',
					'title'		=> __('Pricing Box | Price color', 'mfn-opts'),
					'std' 		=> '#0095eb',
				),

				array(
					'id' 		=> 'background-pricing-featured',
					'type' 		=> 'color',
					'title'		=> __('Pricing Box | Featured background', 'mfn-opts'),
					'std' 		=> '#0095eb',
				),

				array(
					'id' 		=> 'background-progressbar',
					'type' 		=> 'color',
					'title'		=> __('Progress Bar background', 'mfn-opts'),
					'std' 		=> '#0095eb',
				),

				array(
					'id' 		=> 'color-quickfact-number',
					'type' 		=> 'color',
					'title'		=> __('Quick Fact Number color', 'mfn-opts'),
					'std' 		=> '#0095eb',
				),

				array(
					'id' 		=> 'background-slidingbox-title',
					'type' 		=> 'color',
					'title'		=> __('Sliding Box Title background', 'mfn-opts'),
					'std' 		=> '#0095eb',
				),

				array(
					'id' 		=> 'background-trailer-subtitle',
					'type' 		=> 'color',
					'title'		=> __('Trailer Box Subtitle background', 'mfn-opts'),
					'std' 		=> '#0095eb',
				),

			),
		);

		// Forms ------
		$sections['colors-forms'] = array(
			'title'		=> __( 'Forms', 'mfn-opts' ),
			'icon'		=> MFN_OPTIONS_URI. 'img/icons/sub.png',
			'fields' 	=> array(

				// Input, Select & Textarea
				array(
					'id' 		=> 'form-info-input',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __( 'Input, Select & Textarea', 'mfn-opts' ),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'color-form',
					'type' 		=> 'color',
					'title'		=> __( 'Text color', 'mfn-opts' ),
					'std' 		=> '#626262',
				),

				array(
					'id' 		=> 'background-form',
					'type' 		=> 'color',
					'title'		=> __( 'Background', 'mfn-opts' ),
					'std' 		=> '#FFFFFF',
				),

				array(
					'id' 		=> 'border-form',
					'type' 		=> 'color',
					'title'		=> __( 'Border color', 'mfn-opts' ),
					'std' 		=> '#EBEBEB',
				),

				array(
					'id' 		=> 'color-form-placeholder',
					'type' 		=> 'color',
					'title'		=> __( 'Placeholder color', 'mfn-opts' ),
					'desc'		=> __( 'Works only in modern browsers', 'mfn-opts' ),
					'std' 		=> '#929292',
				),

				// Focus
				array(
					'id' 		=> 'form-info-focus',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __( 'Focus', 'mfn-opts' ),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'color-form-focus',
					'type' 		=> 'color',
					'title'		=> __( 'Text color', 'mfn-opts' ),
					'std' 		=> '#1982c2',
				),

				array(
					'id' 		=> 'background-form-focus',
					'type' 		=> 'color',
					'title'		=> __( 'Background', 'mfn-opts' ),
					'std' 		=> '#e9f5fc',
				),

				array(
					'id' 		=> 'border-form-focus',
					'type' 		=> 'color',
					'title'		=> __( 'Border color', 'mfn-opts' ),
					'std' 		=> '#d5e5ee',
				),

				array(
					'id' 		=> 'color-form-placeholder-focus',
					'type' 		=> 'color',
					'title'		=> __( 'Placeholder color', 'mfn-opts' ),
					'desc'		=> __( 'Works only in modern browsers', 'mfn-opts' ),
					'std' 		=> '#929292',
				),

				// Advanced
				array(
					'id' => 'form-info-advanced',
					'type' => 'info',
					'title' => '',
					'desc' => __( 'Advanced', 'mfn-opts' ),
					'class' => 'mfn-info',
				),

				array(
					'id' => 'form-border-width',
					'type' => 'text',
					'title' => __( 'Border width', 'mfn-opts' ),
					'sub_desc' => __( 'default: 1px', 'mfn-opts' ),
					'desc' => __( 'Use value with <b>px</b><br />Example: <b>1px 1px 2px 1px</b>', 'mfn-opts' ),
					'class' => 'small-text',
				),

				array(
					'id' => 'form-border-radius',
					'type' => 'text',
					'title' => __( 'Border radius', 'mfn-opts' ),
					'sub_desc' => __( 'default: 0', 'mfn-opts' ),
					'desc' => __( 'Use value with <b>px</b><br />Example: <b>20px</b>', 'mfn-opts' ),
					'class' => 'small-text',
				),

				array(
					'id' => 'form-transparent',
					'type' => 'sliderbar',
					'title' => __( 'Background Transparency (alpha)', 'mfn-opts' ),
					'sub_desc' => __( '0 = transparent, 100 = solid', 'mfn-opts' ),
					'param' => array(
						'min' => 0,
						'max' => 100,
					),
					'std' => '100',
				),

			),
		);

		// Font Family ------
		$sections['font-family'] = array(
			'title' 	=> __( 'Family', 'mfn-opts' ),
			'fields' 	=> array(

				array(
					'id' 			=> 'font-info-family',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __( 'Font Family', 'mfn-opts' ),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 			=> 'font-content',
					'type' 		=> 'font_select',
					'title' 	=> __( 'Content', 'mfn-opts' ),
					'sub_desc'=> __( 'All theme texts except headings and menu', 'mfn-opts' ),
					'std' 		=> 'Roboto'
				),

				array(
					'id' 			=> 'font-menu',
					'type' 		=> 'font_select',
					'title' 	=> __( 'Main Menu', 'mfn-opts' ),
					'sub_desc'=> __( 'Header menu', 'mfn-opts' ),
					'std' 		=> 'Roboto',
					'class'		=> 'mhb-opt'
				),

				array(
					'id' 		=> 'font-title',
					'type' 		=> 'font_select',
					'title' 	=> __('Page Title', 'mfn-opts'),
					'std' 		=> 'Lora'
				),

				array(
					'id' 		=> 'font-headings',
					'type' 		=> 'font_select',
					'title' 	=> __('Big Headings', 'mfn-opts'),
					'sub_desc' 	=> __('H1, H2, H3 & H4 headings', 'mfn-opts'),
					'std' 		=> 'Roboto'
				),

				array(
					'id' 		=> 'font-headings-small',
					'type' 		=> 'font_select',
					'title' 	=> __('Small Headings', 'mfn-opts'),
					'sub_desc' 	=> __('H5 & H6 headings', 'mfn-opts'),
					'std' 		=> 'Roboto'
				),

				array(
					'id' 		=> 'font-blockquote',
					'type' 		=> 'font_select',
					'title' 	=> __('Blockquote', 'mfn-opts'),
					'std' 		=> 'Roboto'
				),

				array(
					'id' 		=> 'font-decorative',
					'type' 		=> 'font_select',
					'title' 	=> __('Decorative', 'mfn-opts'),
					'sub_desc' 	=> __('Digits in some items', 'mfn-opts'),
					'desc' 		=> __('eg. Chart Box, Counter, How it Works, Quick Fact, Single Product Price', 'mfn-opts'),
					'std' 		=> 'Roboto'
				),

				array(
					'id' 		=> 'font-info-google',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Google Fonts', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'font-weight',
					'type' 		=> 'checkbox',
					'title' 	=> __('Google Fonts Weight & Style', 'mfn-opts'),
					'sub_desc' 	=> __('Impact on page <b>load time</b>', 'mfn-opts'),
					'desc' 		=> __('Some of the fonts in the Google Fonts Directory support multiple styles. For a complete list of available font subsets please see <a href="https://www.google.com/webfonts" target="_blank">Google Web Fonts</a>', 'mfn-opts'),
					'options' 	=> array(
						'100'		=> __( '100 Thin', 'mfn-opts' ),
						'100italic'	=> __( '100 Thin Italic', 'mfn-opts' ),
						'200'		=> __( '200 Extra-Light', 'mfn-opts' ),
						'200italic'	=> __( '200 Extra-Light Italic', 'mfn-opts' ),
						'300'		=> __( '300 Light', 'mfn-opts' ),
						'300italic'	=> __( '300 Light Italic', 'mfn-opts' ),
						'400'		=> __( '400 Regular', 'mfn-opts' ),
						'400italic'	=> __( '400 Regular Italic', 'mfn-opts' ),
						'500'		=> __( '500 Medium', 'mfn-opts' ),
						'500italic'	=> __( '500 Medium Italic', 'mfn-opts' ),
						'600'		=> __( '600 Semi-Bold', 'mfn-opts' ),
						'600italic'	=> __( '600 Semi-Bold Italic', 'mfn-opts' ),
						'700'		=> __( '700 Bold', 'mfn-opts' ),
						'700italic'	=> __( '700 Bold Italic', 'mfn-opts' ),
						'800'		=> __( '800 Extra-Bold', 'mfn-opts' ),
						'800italic'	=> __( '800 Extra-Bold Italic', 'mfn-opts' ),
						'900'		=> __( '900 Black', 'mfn-opts' ),
						'900italic'	=> __( '900 Black Italic', 'mfn-opts' ),
					),
					'class'		=> 'float-left',
					'std'		=> array(
						'300' 		=> '300',
						'400' 		=> '400',
						'400italic' => '400italic',
						'500'		=> '500',
						'700'		=> '700',
						'700italic' => '700italic',
					),
				),

				array(
					'id' 		=> 'font-subset',
					'type' 		=> 'text',
					'title' 	=> __('Google Fonts Subset', 'mfn-opts'),
					'sub_desc' 	=> __('Specify which subsets should be downloaded. Multiple subsets should be separated with coma (,)', 'mfn-opts'),
					'desc' 		=> __('Some of the fonts in the Google Fonts Directory support multiple scripts (like Latin and Cyrillic for example). For a complete list of available font subsets please see <a href="https://www.google.com/webfonts" target="_blank">Google Web Fonts</a>', 'mfn-opts'),
					'class' 	=> 'small-text'
				),

			),
		);

		// Content Font Size ------
		$sections['font-size'] = array(
			'title' => __('Size & Style', 'mfn-opts'),
			'fields' => array(

				array(
					'id' 		=> 'font-size-info-general',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('General', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'font-size-content',
					'type' 		=> 'typography',
					'title' 	=> __( 'Content', 'mfn-opts' ),
					'sub_desc' 	=> __( 'All theme texts<br/>default: 14', 'mfn-opts' ),
					'desc' 		=> __( 'Some of Google Fonts support multiple weights & styles. Include them in <b>Theme Options > Fonts > Family > Google Fonts Weight & Style</b>', 'mfn-opts' ),
					'std' 		=> array(
						'size' 				=> 14,
						'line_height' 		=> 25,
						'weight_style' 		=> '400',
						'letter_spacing' 	=> 0,
					),
				),

				array(
					'id' 		=> 'font-size-big',
					'type' 		=> 'typography',
					'title' 	=> __('p.big', 'mfn-opts'),
					'sub_desc' 	=> __('class="big"<br />default: 16', 'mfn-opts'),
					'std' 		=> array(
						'size' 				=> 16,
						'line_height' 		=> 28,
						'weight_style' 		=> '400',
						'letter_spacing' 	=> 0,
					),
				),

				array(
					'id' 			=> 'font-size-menu',
					'type' 		=> 'typography',
					'title' 	=> __( 'Main Menu', 'mfn-opts' ),
					'sub_desc'=> __( 'First level of Main Menu<br/>default: 15', 'mfn-opts' ),
					'disable' => 'line_height',
					'std' 		=> array(
						'size' 						=> 15,
						'line_height' 		=> 0,
						'weight_style' 		=> '400',
						'letter_spacing' 	=> 0,
					),
					'class'		=> 'mhb-opt',
				),

				array(
					'id' 		=> 'font-size-title',
					'type' 		=> 'typography',
					'title' 	=> __('Page Title', 'mfn-opts'),
					'sub_desc' 	=> 'default: 30',
					'std' 		=> array(
						'size' 				=> 30,
						'line_height' 		=> 35,
						'weight_style' 		=> '400italic',
						'letter_spacing' 	=> 1,
					),
				),

				array(
					'id' 		=> 'font-size-info-heading',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Heading', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'font-size-h1',
					'type' 		=> 'typography',
					'title' 	=> __('H1', 'mfn-opts'),
					'sub_desc' 	=> 'default: 48',
					'std' 		=> array(
						'size' 				=> 48,
						'line_height' 		=> 50,
						'weight_style' 		=> '400',
						'letter_spacing' 	=> 0,
					),
				),

				array(
					'id' 		=> 'font-size-h2',
					'type' 		=> 'typography',
					'title' 	=> __('H2', 'mfn-opts'),
					'sub_desc' 	=> 'default: 30',
					'std' 		=> array(
						'size' 				=> 30,
						'line_height' 		=> 34,
						'weight_style' 		=> '300',
						'letter_spacing' 	=> 0,
					),
				),

				array(
					'id' 		=> 'font-size-h3',
					'type' 		=> 'typography',
					'title' 	=> __('H3', 'mfn-opts'),
					'sub_desc' 	=> 'default: 25',
					'std' 		=> array(
						'size' 				=> 25,
						'line_height' 		=> 29,
						'weight_style' 		=> '300',
						'letter_spacing' 	=> 0,
					),
				),

				array(
					'id' 		=> 'font-size-h4',
					'type' 		=> 'typography',
					'title' 	=> __('H4', 'mfn-opts'),
					'sub_desc' 	=> 'default: 21',
					'std' 		=> array(
						'size' 				=> 21,
						'line_height' 		=> 25,
						'weight_style' 		=> '500',
						'letter_spacing' 	=> 0,
					),
				),

				array(
					'id' 		=> 'font-size-h5',
					'type' 		=> 'typography',
					'title' 	=> __('H5', 'mfn-opts'),
					'sub_desc' 	=> 'default: 15',
					'std' 		=> array(
						'size' 				=> 15,
						'line_height' 		=> 25,
						'weight_style' 		=> '700',
						'letter_spacing' 	=> 0,
					),
				),

				array(
					'id' 		=> 'font-size-h6',
					'type' 		=> 'typography',
					'title' 	=> __('H6', 'mfn-opts'),
					'sub_desc' 	=> 'default: 14',
					'std' 		=> array(
						'size' 				=> 14,
						'line_height' 		=> 25,
						'weight_style' 		=> '400',
						'letter_spacing' 	=> 0,
					),
				),

				array(
					'id' 		=> 'font-size-info-advanced',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Advanced', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'font-size-single-intro',
					'type' 		=> 'typography',
					'title' 	=> __('Single Post | Intro', 'mfn-opts'),
					'sub_desc' 	=> 'default: 70',
					'std' 		=> array(
						'size' 				=> 70,
						'line_height' 		=> 70,
						'weight_style' 		=> '400',
						'letter_spacing' 	=> 0,
					),
				),

			),
		);

		// Font Custom ------
		$sections['font-custom'] = array(
			'title' => __( 'Custom', 'mfn-opts' ),
			'fields' => array(

				array(
					'id' 			=> 'fonts-info-custom',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __( 'Use below fields if you want to use webfonts directly from your server. For more info please see <a target="_blank" href="https://forum.muffingroup.com/betheme/discussion/38753/custom-fonts">this post</a>.', 'mfn-opts' ),
					'class' 	=> 'mfn-info desc',
				),

				// font 1
				array(
					'id' 			=> 'fonts-info-font1',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __( 'Font 1', 'mfn-opts' ),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 			=> 'font-custom',
					'type' 		=> 'text',
					'title' 	=> __( 'Name', 'mfn-opts' ),
					'sub_desc'=> __( 'Please use only letters or spaces, eg. Custom Font 1', 'mfn-opts' ),
					'desc' 		=> __( 'Name for Custom Font uploaded below.<br />Font will show on fonts list after <strong>click the Save Changes</strong> button.' , 'mfn-opts' ),
					'class' 	=> 'small-text',
				),

				array(
					'id' 			=> 'font-custom-woff',
					'type' 		=> 'upload',
					'title' 	=> __( '.woff', 'mfn-opts'),
					'sub_desc'=> __( 'recommended', 'mfn-opts'),
					'desc'		=> __( 'WordPress 5.0 blocks .woff upload. Please use <a target="_blank" href="plugin-install.php?s=Disable+Real+MIME+Check&tab=search&type=term">Disable Real MIME Check</a> plugin.', 'mfn-opts' ),
					'data'		=> 'font',
				),

				array(
					'id' 			=> 'font-custom-ttf',
					'type' 		=> 'upload',
					'title' 	=> __( '.ttf', 'mfn-opts' ),
					'sub_desc'=> __( 'optional', 'mfn-opts' ),
					'desc'		=> __( 'WordPress 5.0 blocks .ttf upload. Please use <a target="_blank" href="plugin-install.php?s=Disable+Real+MIME+Check&tab=search&type=term">Disable Real MIME Check</a> plugin.', 'mfn-opts' ),
					'data'		=> 'font',
				),

				// font 2
				array(
					'id' 			=> 'fonts-info-font2',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __( 'Font 2', 'mfn-opts' ),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 			=> 'font-custom2',
					'type' 		=> 'text',
					'title' 	=> __('Name', 'mfn-opts'),
					'sub_desc'=> __( 'Please use only letters or spaces, eg. Custom Font 2', 'mfn-opts' ),
					'desc' 		=> __( 'Name for Custom Font uploaded below.<br />Font will show on fonts list after <strong>click the Save Changes</strong> button.' , 'mfn-opts' ),
					'class' 	=> 'small-text',
				),

				array(
					'id' 			=> 'font-custom2-woff',
					'type' 		=> 'upload',
					'title' 	=> __('.woff', 'mfn-opts'),
					'sub_desc'=> __( 'recommended', 'mfn-opts'),
					'desc'		=> __( 'WordPress 5.0 blocks .woff upload. Please use <a target="_blank" href="plugin-install.php?s=Disable+Real+MIME+Check&tab=search&type=term">Disable Real MIME Check</a> plugin.', 'mfn-opts' ),
					'data'		=> 'font',
				),

				array(
					'id' 			=> 'font-custom2-ttf',
					'type' 		=> 'upload',
					'title' 	=> __( '.ttf', 'mfn-opts' ),
					'sub_desc'=> __( 'optional', 'mfn-opts' ),
					'desc'		=> __( 'WordPress 5.0 blocks .ttf upload. Please use <a target="_blank" href="plugin-install.php?s=Disable+Real+MIME+Check&tab=search&type=term">Disable Real MIME Check</a> plugin.', 'mfn-opts' ),
					'data'		=> 'font',
				),

			),
		);


		// Translate / General ------
		$sections['translate-general'] = array(
			'title' => __('General', 'mfn-opts'),
			'fields' => array(

				array(
					'id' 		=> 'translate',
					'type' 		=> 'switch',
					'title' 	=> __('Enable Translate', 'mfn-opts'),
					'desc' 		=> __('Turn it <b>off</b> if you want to use <b>.mo .po files</b> for more complex translation', 'mfn-opts'),
					'options' 	=> array('1' => 'On','0' => 'Off'),
					'std' 		=> '1'
				),

				array(
					'id' 		=> 'translate-info',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('The fields must be filled out if you are using WPML String Translation<br /><span>If you are using the English language, you can also use this tab to change some texts</span>', 'mfn-opts'),
					'class' 	=> 'mfn-info desc',
				),

				array(
					'id' 		=> 'translate-search-placeholder',
					'type' 		=> 'text',
					'title' 	=> __('Search Placeholder', 'mfn-opts'),
					'desc' 		=> __('Search Form', 'mfn-opts'),
					'std' 		=> 'Enter your search',
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'translate-search-results',
					'type' 		=> 'text',
					'title' 	=> __('results found for:', 'mfn-opts'),
					'desc' 		=> __('Search Results', 'mfn-opts'),
					'std' 		=> 'results found for:',
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'translate-home',
					'type' 		=> 'text',
					'title' 	=> __('Home', 'mfn-opts'),
					'desc' 		=> __('Breadcrumbs', 'mfn-opts'),
					'std' 		=> 'Home',
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'translate-prev',
					'type' 		=> 'text',
					'title' 	=> __('Prev page', 'mfn-opts'),
					'desc' 		=> __('Pagination', 'mfn-opts'),
					'std' 		=> 'Prev page',
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'translate-next',
					'type' 		=> 'text',
					'title' 	=> __('Next page', 'mfn-opts'),
					'desc' 		=> __('Pagination', 'mfn-opts'),
					'std' 		=> 'Next page',
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'translate-load-more',
					'type' 		=> 'text',
					'title' 	=> __('Load more', 'mfn-opts'),
					'desc' 		=> __('Pagination', 'mfn-opts'),
					'std' 		=> 'Load more',
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'translate-wpml-no',
					'type' 		=> 'text',
					'title' 	=> __('No translations available for this page', 'mfn-opts'),
					'desc' 		=> __('WPML Languages Menu', 'mfn-opts'),
					'std' 		=> 'No translations available for this page',
				),

				array(
					'id' 		=> 'translate-share',
					'type' 		=> 'text',
					'title' 	=> __( 'Share', 'mfn-opts' ),
					'desc' 		=> __( 'Share', 'mfn-opts' ),
					'std' 		=> 'Share',
					'class' 	=> 'small-text',
				),

				// items
				array(
					'id' 		=> 'translate-info-items',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Items <span>Builder items and shortcodes</span>', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'translate-before',
					'type' 		=> 'text',
					'title' 	=> __('Before', 'mfn-opts'),
					'desc' 		=> __('Before After', 'mfn-opts'),
					'std' 		=> 'Before',
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'translate-after',
					'type' 		=> 'text',
					'title' 	=> __('After', 'mfn-opts'),
					'desc' 		=> __('Before After', 'mfn-opts'),
					'std' 		=> 'After',
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'translate-days',
					'type' 		=> 'text',
					'title' 	=> __('Days', 'mfn-opts'),
					'desc' 		=> __('Countdown', 'mfn-opts'),
					'std' 		=> 'days',
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'translate-hours',
					'type' 		=> 'text',
					'title' 	=> __('Hours', 'mfn-opts'),
					'desc' 		=> __('Countdown', 'mfn-opts'),
					'std' 		=> 'hours',
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'translate-minutes',
					'type' 		=> 'text',
					'title' 	=> __('Minutes', 'mfn-opts'),
					'desc' 		=> __('Countdown', 'mfn-opts'),
					'std' 		=> 'minutes',
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'translate-seconds',
					'type' 		=> 'text',
					'title' 	=> __('Seconds', 'mfn-opts'),
					'desc' 		=> __('Countdown', 'mfn-opts'),
					'std' 		=> 'seconds',
					'class' 	=> 'small-text',
				),

			),
		);

		// Translate / Blog  ------
		$sections['translate-blog'] = array(
			'title' => __('Blog & Portfolio', 'mfn-opts'),
			'fields' => array(

				array(
					'id' 		=> 'translate-filter',
					'type' 		=> 'text',
					'title' 	=> __('Filter by', 'mfn-opts'),
					'desc' 		=> __('Blog, Portfolio', 'mfn-opts'),
					'std' 		=> 'Filter by',
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'translate-tags',
					'type' 		=> 'text',
					'title' 	=> __('Tags', 'mfn-opts'),
					'desc' 		=> __('Blog', 'mfn-opts'),
					'std' 		=> 'Tags',
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'translate-authors',
					'type' 		=> 'text',
					'title' 	=> __('Authors', 'mfn-opts'),
					'desc' 		=> __('Blog', 'mfn-opts'),
					'std' 		=> 'Authors',
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'translate-all',
					'type' 		=> 'text',
					'title' 	=> __('Show all', 'mfn-opts'),
					'desc' 		=> __('Blog, Portfolio', 'mfn-opts'),
					'std' 		=> 'Show all',
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'translate-item-all',
					'type' 		=> 'text',
					'title' 	=> __('All', 'mfn-opts'),
					'desc' 		=> __('Blog Item, Portfolio Item', 'mfn-opts'),
					'std' 		=> 'All',
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'translate-published',
					'type' 		=> 'text',
					'title' 	=> __('Published by', 'mfn-opts'),
					'desc' 		=> __('Blog, Portfolio', 'mfn-opts'),
					'std' 		=> 'Published by',
					'class' 	=> 'small-text',
				),

				array(
					'id'			=> 'translate-at',
					'type' 		=> 'text',
					'title' 	=> __('on', 'mfn-opts'),
					'sub_desc'=> __('Published by .. on', 'mfn-opts'),
					'desc' 		=> __('Blog, Portfolio', 'mfn-opts'),
					'std' 		=> 'on',
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'translate-categories',
					'type' 		=> 'text',
					'title' 	=> __('Categories', 'mfn-opts'),
					'desc' 		=> __('Blog, Portfolio', 'mfn-opts'),
					'std' 		=> 'Categories',
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'translate-tags',
					'type' 		=> 'text',
					'title' 	=> __('Tags', 'mfn-opts'),
					'desc' 		=> __('Blog', 'mfn-opts'),
					'std' 		=> 'Tags',
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'translate-readmore',
					'type' 		=> 'text',
					'title' 	=> __('Read more', 'mfn-opts'),
					'desc' 		=> __('Blog, Portfolio', 'mfn-opts'),
					'std' 		=> 'Read more',
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'translate-like',
					'type' 		=> 'text',
					'title' 	=> __('Do you like it?', 'mfn-opts'),
					'desc' 		=> __('Blog', 'mfn-opts'),
					'std' 		=> 'Do you like it?',
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'translate-related',
					'type' 		=> 'text',
					'title' 	=> __('Related posts', 'mfn-opts'),
					'desc' 		=> __('Blog, Portfolio', 'mfn-opts'),
					'std' 		=> 'Related posts',
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'translate-client',
					'type' 		=> 'text',
					'title' 	=> __('Client', 'mfn-opts'),
					'desc' 		=> __('Portfolio', 'mfn-opts'),
					'std' 		=> 'Client',
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'translate-date',
					'type' 		=> 'text',
					'title' 	=> __('Date', 'mfn-opts'),
					'desc' 		=> __('Portfolio', 'mfn-opts'),
					'std' 		=> 'Date',
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'translate-website',
					'type' 		=> 'text',
					'title' 	=> __('Website', 'mfn-opts'),
					'desc' 		=> __('Portfolio', 'mfn-opts'),
					'std' 		=> 'Website',
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'translate-view',
					'type' 		=> 'text',
					'title' 	=> __('View website', 'mfn-opts'),
					'desc' 		=> __('Portfolio', 'mfn-opts'),
					'std' 		=> 'View website',
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'translate-task',
					'type' 		=> 'text',
					'title' 	=> __('Task', 'mfn-opts'),
					'desc' 		=> __('Portfolio', 'mfn-opts'),
					'std' 		=> 'Task',
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'translate-commented-on',
					'type' 		=> 'text',
					'title' 	=> __( 'Commented on', 'mfn-opts' ),
					'desc' 		=> __( 'Muffin Recent Comments widget', 'mfn-opts' ),
					'std' 		=> 'commented on',
					'class' 	=> 'small-text',
				),
			),
		);

		// Translate Error 404 ------
		$sections['translate-404'] = array(
			'title' => __('Error 404 & Search', 'mfn-opts'),
			'fields' => array(

				array(
					'id' 		=> 'translate-info-404',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Error 404', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'translate-404-title',
					'type' 		=> 'text',
					'title' 	=> __('Title', 'mfn-opts'),
					'desc'		=> __('Ooops... Error 404', 'mfn-opts'),
					'std' 		=> 'Ooops... Error 404',
				),

				array(
					'id' 		=> 'translate-404-subtitle',
					'type' 		=> 'text',
					'title' 	=> __('Subtitle', 'mfn-opts'),
					'desc' 		=> __('We are sorry, but the page you are looking for does not exist.', 'mfn-opts'),
					'std' 		=> 'We are sorry, but the page you are looking for does not exist.',
				),

				array(
					'id' 		=> 'translate-404-text',
					'type' 		=> 'text',
					'title' 	=> __('Text', 'mfn-opts'),
					'desc' 		=> __('Please check entered address and try again or', 'mfn-opts'),
					'std' 		=> 'Please check entered address and try again or ',
				),

				array(
					'id' 		=> 'translate-404-btn',
					'type' 		=> 'text',
					'title' 	=> __('Button', 'mfn-opts'),
					'desc' 		=> __('go to homepage', 'mfn-opts'),
					'std' 		=> 'go to homepage',
					'class' 	=> 'small-text',
				),

				array(
					'id' 		=> 'translate-info-search',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('Search', 'mfn-opts'),
					'class' 	=> 'mfn-info',
				),

				array(
					'id' 		=> 'translate-search-title',
					'type' 		=> 'text',
					'title' 	=> __('Title', 'mfn-opts'),
					'desc'		=> __('Ooops...', 'mfn-opts'),
					'std' 		=> 'Ooops...',
				),

				array(
					'id' 		=> 'translate-search-subtitle',
					'type' 		=> 'text',
					'title' 	=> __('Subtitle', 'mfn-opts'),
					'desc' 		=> __('No results found for:', 'mfn-opts'),
					'std' 		=> 'No results found for:',
				),

			),
		);

		// Translate WPML ------
		$sections['translate-wpml'] = array(
			'title' => __('WPML Installer', 'mfn-opts'),
			'fields' => array(

				array(
					'id' 		=> 'translate-wpml-info',
					'type' 		=> 'info',
					'title' 	=> '',
					'desc' 		=> __('<b>WPML</b> is an optional premium plugin and it is <b>NOT</b> included into the theme', 'mfn-opts'),
					'class' 	=> 'mfn-info desc',
				),

				array(
					'id' 		=> 'translate-wpml-installer',
					'type' 		=> 'custom',
					'title' 	=> __('WPML Installer', 'mfn-opts'),
					'sub_desc'	=> __('WPML makes it easy to build multilingual sites and run them. It’s powerful enough for corporate sites, yet simple for blogs.', 'mfn-opts'),
					'action' 	=> 'wpml',
				),

			),
		);

		// Custom CSS & JS =======

		// CSS ------
		$sections['css'] = array(
			'title' => __('CSS', 'mfn-opts'),
			'fields' => array(

				array(
					'id' 		=> 'custom-css',
					'type' 		=> 'textarea',
					'title' 	=> __('Custom CSS', 'mfn-opts'),
					'sub_desc' 	=> __('Paste your custom CSS code here', 'mfn-opts'),
					'class' 	=> 'custom-css',
				),

			),
		);

		// JS ------
		$sections['js'] = array(
			'title' => __('JS', 'mfn-opts'),
			'fields' => array(

				array(
					'id' 		=> 'custom-js',
					'type' 		=> 'textarea',
					'title' 	=> __('Custom JS', 'mfn-opts'),
					'sub_desc' 	=> __('Paste your custom JS code here', 'mfn-opts'),
					'desc' 		=> __('To use jQuery code wrap it into <strong>jQuery(function($){ ... });</strong>', 'mfn-opts'),
				),

			),
		);

		$sections = apply_filters('mfn-theme-options-sections', $sections);

		$MFN_Options = new MFN_Options( $menu, $sections );
	}
}
mfn_opts_setup();

if( ! function_exists( 'mfn_opts_get' ) )
{
	/**
	 * This is used to return option value from the options array
	 */

	function mfn_opts_get( $opt_name, $default = null ){
		global $MFN_Options;
		return $MFN_Options->get( $opt_name, $default );
	}
}

if( ! function_exists( 'mfn_upload_mimes' ) )
{
	/**
	 * Add new mimes for custom font upload
	 */

	function mfn_upload_mimes( $mimes = array() ){

		$mimes['svg'] = 'font/svg';
		$mimes['woff'] = 'font/woff';
		$mimes['ttf'] = 'font/ttf';

		return $mimes;
	}
}
add_filter( 'upload_mimes', 'mfn_upload_mimes' );
