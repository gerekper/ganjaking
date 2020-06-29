<?php
if( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}

class Mfn_HB_Items {

	/**
	 * Logo
	 */
	public static function logo( $attr ){

		extract( shortcode_atts( array(
			'logo' 				=> '',
			'retinaLogo' 	=> '',
			'height' 			=> '',
			'options' 		=> '',
		), $attr ) );

		/*
		TODO
		- logo options
			- link to homepage
			- wrap into h1 on homepage *
			- wrap into h1 on all other pages *
			- only 1 logo can be wrapped into H1 tag
		*/

		// classes
		$classes = array();

		if( isset( $options['overflowLogo'] ) && $options['overflowLogo'] ){
			$classes[] = 'overflow';
		}

		$classes = implode( '', $classes );

		// attributes
		$attr = array();

		if( $height ){
			$attr['height'] = 'height="'. $height .'"';
		}

		// TODO: alt & height (from media)

		$attr = implode( '', $attr );

		// output

		$output = '<div class="mhb-item-inner '. $classes .'">';

			$output .= '<h1>';

				$output .= '<a href="'. get_home_url() .'" title="'. get_bloginfo( 'name' ) .'">';

					if( $logo ){
						$output .= '<img class="logo" src="'. esc_url( $logo ) .'" data-retina="'. esc_url( $retinaLogo ) .'" '. $attr .'/>';
					}

				$output .= '</a>';

			$output .= '</h1>';

		$output .= '</div>';

		return $output;
	}

	/**
	 * Menu
	 */
	public static function menu( $attr ){

		extract( shortcode_atts( array(
			'menu' 		=> '',
			'options' => '',
			'replaceWithMenuIcon'	=> '',
		), $attr ) );

		// menu_class

		$menu_class = array( 'menu' );

		if( isset( $options['bordersBetweenItems'] ) && $options['bordersBetweenItems'] ){
			$menu_class[] = 'borders';
		}

		if( isset( $options['foldSubmenusForLast2ItemsToRight'] ) && $options['foldSubmenusForLast2ItemsToRight'] ){
			$menu_class[] = 'last';
		}

		if( isset( $options['arrowsForItemsWithSubmenu'] ) && $options['arrowsForItemsWithSubmenu'] ){
			$menu_class[] = 'arrows';
		}

		$menu_class = implode( ' ', $menu_class );

		// arguments

		$args = array(
			'menu_class'	=> $menu_class,
			'container'		=> false,
			'link_before'	=> '<span>',
			'link_after'	=> '</span>',
			'depth' 			=> 5,
			'menu'				=> $menu,
			'echo'				=> false,
		);

		// output

		$output = '<div class="mhb-item-inner '. esc_attr( $replaceWithMenuIcon ) .'">';

			$output .= wp_nav_menu( $args );

			$output .= '<a class="mobile-menu-toggle" href="#"><i class="icon-menu-fine"></i></a>';

		$output .= '</div>';

		return $output;
	}


	/**
	 * Menu Icon
	 */
	public static function menu_icon( $attr ){

		extract( shortcode_atts( array(
			'menu' 				=> '',
		), $attr ) );

		$args = array(
			'container'		=> false,
			'link_before'	=> '<span>',
			'link_after'	=> '</span>',
			'depth' 			=> 5,
			'menu'				=> $menu,
		);

		$output = '<a class="open-menu-icon" href="#"><i class="icon-menu-fine"></i></a>';

		// $output .= wp_nav_menu( $args );

		return $output;
	}

	/**
	 * Extras
	 */
	public static function extras( $attr ){

		extract( shortcode_atts( array(
			'searchStyle' 		=> '',
			'searchType' 			=> '',
			'shopIcon' 				=> '',
			'wpmlStyle' 			=> '',
			'wpmlArrangement' => '',
		), $attr ) );

		global $woocommerce;

		$translate[ 'search-placeholder' ] = mfn_opts_get( 'translate' ) ? mfn_opts_get( 'translate-search-placeholder', 'Enter your search' ) : esc_html__( 'Enter your search', 'betheme' );

		$output = '';

		// search -----

		if( 'hide' != $searchStyle ){

			$output .= '<div class="search '. esc_attr( $searchStyle ) .'">';

				if( $searchStyle == 'icon' ){
					$output .= '<a class="search-icon" href="#"><i class="icon-search-fine"></i></a>';
				}

				$output .= '<form method="GET" action="'. esc_url( home_url( '/' ) ) .'">';

					$output .= '<i class="icon-search-fine"></i>';
					$output .= '<input type="text" class="field" name="s" placeholder="'. esc_attr( $translate[ 'search-placeholder' ] ) .'" />';
					$output .= '<input type="submit" class="submit" value="" style="display:none" />';

					if( $searchType == 'products' ){
						$output .= '<input type="hidden" name="post_type" value="product" />';
					}

				$output .= '</form>';

			$output .= '</div>';

		}

		// shop -----

		if( $woocommerce && $shopIcon ){
			$output .= '<a id="header_cart" href="'. esc_url( wc_get_cart_url() ) .'">';
				$output .= '<i class="'. esc_attr( $shopIcon ) .'"></i>';
				$output .= '<span>'. esc_html( $woocommerce->cart->cart_contents_count ) .'</span>';
			$output .= '</a>';
		}

		// wpml -----

		if( ( 'hide' != $wpmlStyle ) && function_exists( 'icl_get_languages' ) ){

			$lang_options = mfn_opts_get( 'header-wpml-options' );

			if( isset( $lang_options[ 'link-to-home' ] )){
				$lang_args = 'skip_missing=0';
			} else {
				$lang_args = 'skip_missing=1';
			}

			$languages = icl_get_languages( $lang_args );

			$output .= '<div class="wpml-languages '. esc_attr( $wpmlArrangement ) .'">';

				if( $wpmlArrangement == 'dropdown' ){
					foreach( $languages as $lang_k => $lang ){
						if( $lang['active'] ){
							$active_lang = $lang;
							unset( $languages[$lang_k] );
						}
					}

					if( $active_lang ){
						$output .= '<a class="active" href="#">';

							if( $wpmlStyle == 'flags' ){
								$output .= '<img src="'. esc_url( $active_lang['country_flag_url'] ) .'" alt="'. esc_attr( $active_lang['language_code'] ) .'" width="18" height="12"/>';
							} elseif( $wpmlStyle == 'langName' ) {
								$output .= esc_html( $active_lang['native_name'] );
							} else {
								$output .= esc_html( strtoupper( $active_lang['language_code'] ) );
							}

						$output .= '</a>';
					} else {
						$languages = array();
					}
				}

				$output .= '<ul>';

					foreach( $languages as $lang ){

						$lang_class = false;
						if( $lang['active'] ){
							$lang_class = 'lang-active';
						}

						if( $wpmlStyle == 'flags' ){
							$single_escaped = '<img src="'. esc_url( $lang['country_flag_url'] ) .'" alt="'. esc_attr( $lang['language_code'] ) .'" width="18" height="12"/>';
						} elseif( $wpmlStyle == 'langName' ) {
							$single_escaped = esc_html( $lang['native_name'] );
						} else {
							$single_escaped = esc_html( strtoupper( $lang['language_code'] ) );
						}

						$output .= '<li class="'. esc_attr( $lang_class ) .'">';
							$output .= '<a href="'. esc_url( $lang['url'] ) .'">';
								// This variable has been safely escaped: 9 lines above
								$output .= $single_escaped;
							$output .= '</a>';
						$output .= '</li>';

					}

				$output .= '</ul>';
			$output .= '</div>';

		}

		return $output;
	}

	/**
	 * Social
	 */
	public static function social( $attr ){

		extract( shortcode_atts( array(
			'iconsList' 						=> '',
			'openLinksInNewWindow' 	=> '',
		), $attr ) );

		// target
		if( $openLinksInNewWindow ){
			$target = 'target="_blank"';
		} else {
			$target = '';
		}

		$output = '';

		if( $iconsList ){
			$output .= '<ul>';
				foreach( $iconsList as $item ){
					$output .= '<li><a href="'. esc_url( $item['link'] ) .'" '. $target .'><i class="'. esc_attr( $item['icon'] ) .'"></i></a></li>';
				}
			$output .= '<ul>';
		}

		return $output;
	}

	/**
	 * Text
	 */
	public static function text( $attr ){

		extract( shortcode_atts( array(
			'text' => '',
		), $attr ) );

		$output = do_shortcode( $text );

		return $output;
	}

	/**
	 * Image
	 */
	public static function image( $attr ){

		extract( shortcode_atts( array(
			'image' 			=> '',
			'link' 				=> '',
			'linkTarget' 	=> '',
			'linkClass' 	=> '',
		), $attr ) );

		/*
		TODO
		- image attributes from media library
		*/

		// link

		$before = false;
		$after 	= false;

		if( $link ){

			$link_attr = array();

			if( $linkTarget == '_blank' ){
				$link_attr[] = 'target="_blank"';
			} elseif( $linkTarget == 'lightbox' ){
				$link_attr[] = 'rel="lightbox"';
			}

			if( $linkClass ){
				$link_attr[] = 'class="'. esc_attr( $linkClass ) .'"';
			}

			$link_attr = implode( ' ', $link_attr );

			$before = '<a href="'. esc_url( $link ) .'" '. $link_attr .'>';
			$after = '</a>';
		}

		// output

		$output = '';

		if( $image ){
			$output .= $before .'<img src="'. esc_url( $image ) .'" alt="">'. $after;
		}

		return $output;
	}

	/**
	 * Icon
	 */
	public static function icon( $attr ){

		extract( shortcode_atts( array(
			'icon' 			=> '',
			'link' 				=> '',
			'linkTarget' 	=> '',
			'linkClass' 	=> '',
		), $attr ) );

		// link

		$before = false;
		$after 	= false;

		if( $link ){

			$link_attr = array();

			if( $linkTarget == '_blank' ){
				$link_attr[] = 'target="_blank"';
			} elseif( $linkTarget == 'lightbox' ){
				$link_attr[] = 'rel="lightbox"';
			}

			if( $linkClass ){
				$link_attr[] = 'class="'. esc_attr( $linkClass ) .'"';
			}

			$link_attr = implode( ' ', $link_attr );

			$before = '<a href="'. esc_url( $link ) .'" '. $link_attr .'>';
			$after = '</a>';
		}

		// output

		$output = '';

		if( $icon ){
			$output .= $before .'<i class="'. esc_attr( $icon ) .'"></i>'. $after;
		}

		return $output;
	}

	/**
	 * Button
	 */
	public static function button( $attr ){

		extract( shortcode_atts( array(
			'title' 			=> 'Button',
			'link' 				=> '',
			'linkTarget' 	=> '',
			'linkClass' 	=> '',
		), $attr ) );

		// attributes

		$link_attr = array();

		if( $linkTarget == '_blank' ){
			$link_attr[] = 'target="_blank"';
		} elseif( $linkTarget == 'lightbox' ){
			$link_attr[] = 'rel="lightbox"';
		}

		$link_attr = implode( ' ', $link_attr );

		// output

		$output = '';

		if( $link ){
			$output .= '<a class="action_button '. esc_attr( $linkClass ) .'" href="'. esc_url( $link ) .'" '. $link_attr .'>'. esc_html( $title ) .'</a>';
		}

		return $output;
	}


}
