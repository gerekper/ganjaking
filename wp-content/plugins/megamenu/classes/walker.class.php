<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // disable direct access
}

if( ! class_exists( 'Mega_Menu_Walker' ) ) :

/**
 * @package WordPress
 * @since 1.0.0
 * @uses Walker
 */
class Mega_Menu_Walker extends Walker_Nav_Menu {

	/**
	 * Starts the list before the elements are added.
	 *
	 * @see Walker::start_lvl()
	 *
	 * @since 1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 */
	function start_lvl( &$output, $depth = 0, $args = array() ) {

		$indent = str_repeat("\t", $depth);

		$output .= "\n$indent<ul class=\"mega-sub-menu\">\n";
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @see Walker::end_lvl()
	 *
	 * @since 1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 */
	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	/**
	 * Custom walker. Add the widgets into the menu.
	 *
	 * @see Walker::start_el()
	 *
	 * @since 1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 * @param int    $id     Current item ID.
	 */
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		if ( property_exists( $item, 'megamenu_settings' ) ) {
			$settings = $item->megamenu_settings;
		} else {
			$settings = Mega_Menu_Nav_Menus::get_menu_item_defaults();
		}

		// Item Class
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;

		if ( is_array( $classes ) && ! in_array( "menu-column", $classes ) && ! in_array( "menu-row", $classes ) ) {
			$classes[] = 'menu-item-' . $item->ID;
		}

        $class = join( ' ', apply_filters( 'megamenu_nav_menu_css_class', array_filter( $classes ), $item, $args ) );

        // these classes are prepended with 'mega-'
        $mega_classes = explode( ' ', $class);

        // strip widget classes back to how they're intended to be output
        $class = str_replace( "mega-menu-widget-class-", "", $class );

		// Item ID
		if ( is_array( $classes ) && ! in_array( "menu-column", $classes ) && ! in_array( "menu-row", $classes ) ) {
			$id = "mega-menu-item-{$item->ID}";
		} else {
			$id = "mega-menu-{$item->ID}";
		}

		$id = esc_attr( apply_filters( 'megamenu_nav_menu_item_id', $id, $item, $args ) );

		$output .= "<li class='{$class}' id='{$id}'>";

		// output the widgets
		if ( $item->type == 'widget' && $item->content ) {

			$item_output = $item->content;

		} else {

			$atts = array();

			$atts['title'] = ! empty( $item->attr_title ) ? $item->attr_title : '';
			$atts['target'] = ! empty( $item->target ) ? $item->target : '';
			$atts['class'] = '';
			$atts['rel'] = ! empty( $item->xfn ) ? $item->xfn : '';

			if ( isset( $settings['disable_link'] ) && $settings['disable_link'] != 'true') {
				$atts['href'] = ! empty( $item->url ) ? $item->url : '';
			} else {
				$atts['tabindex'] = 0;
			}

			if ( isset( $settings['icon'] ) && $settings['icon'] != 'disabled' && $settings['icon'] != 'custom' ) {
				$atts['class'] = $settings['icon'];
			}
 
			if ( isset( $settings['icon'] ) && $settings['icon'] == 'custom' ) {
				$atts['class'] = 'mega-custom-icon';
			}

			if ( is_array( $classes ) && in_array( 'menu-item-has-children', $classes ) && $item->parent_submenu_type == 'flyout' ) {

				$atts['aria-haspopup'] = "true"; // required for Surface/Win10/Edge
				$atts['aria-expanded'] = "false";

				if ( is_array( $mega_classes ) && in_array( 'mega-toggle-on', $mega_classes ) ) {
					$atts['aria-expanded'] = "true";
				}

				if ( isset( $settings['disable_link'] ) && $settings['disable_link'] == 'true' ) {
					$atts['role'] = 'button';
				}
			}

			if ( $depth == 0 ) {
				$atts['tabindex'] = "0";
			}

			if ( isset( $settings['hide_text'] ) && $settings['hide_text'] == 'true' ) {
				$atts['aria-label'] = $item->title;
			}

			$atts = apply_filters( 'megamenu_nav_menu_link_attributes', $atts, $item, $args );

			if ( isset( $atts['class'] ) && strlen( $atts['class'] ) ) {
			    $atts['class'] = $atts['class'] . ' mega-menu-link';
			} else {
			    $atts['class'] = 'mega-menu-link';
			}

			$attributes = '';

			foreach ( $atts as $attr => $value ) {
				if ( strlen( $value ) ) {
					$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
					$attributes .= ' ' . $attr . '="' . $value . '"';
				}
			}

			$item_output = $args->before;
			$item_output .= '<a'. $attributes .'>';

			if ( is_array( $classes ) && in_array('icon-top', $classes ) ) {
			   $item_output .= "<span class='mega-title-below'>";
			}

			if ( isset( $settings['hide_text'] ) && $settings['hide_text'] == 'true' ) {
				/** This filter is documented in wp-includes/post-template.php */
			} else if ( property_exists( $item, 'mega_description' ) && strlen( $item->mega_description ) ) {
		        $item_output .= '<span class="mega-description-group"><span class="mega-menu-title">' . $args->link_before . apply_filters( 'megamenu_the_title', $item->title, $item->ID ) . $args->link_after . '</span><span class="mega-menu-description">' . $item->description . '</span></span>';
		    } else {
				$item_output .= $args->link_before . apply_filters( 'megamenu_the_title', $item->title, $item->ID ) . $args->link_after;
			}

            if ( is_array( $classes ) && in_array( 'icon-top', $classes ) ) {
                $item_output .= "</span>";
            }

			if ( is_array( $classes ) && in_array( 'menu-item-has-children', $classes ) ) {
				$item_output .= '<span class="mega-indicator"></span>';
			}

			$item_output .= '</a>';
			$item_output .= $args->after;

			if ( is_array( $classes ) && ( in_array( "menu-column", $classes ) || in_array( "menu-row", $classes ) ) ) {
				$item_output = "";
			}

		}

		$output .= apply_filters( 'megamenu_walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @see Walker::end_el()
	 *
	 * @since 1.7
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Page data object. Not used.
	 * @param int    $depth  Depth of page. Not Used.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 */
	public function end_el( &$output, $item, $depth = 0, $args = array() ) {
		$output .= "</li>"; // remove new line to remove the 4px gap between menu items
	}
}

endif;