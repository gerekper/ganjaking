<?php

if ( defined( 'DOING_AJAX' ) ) {
	
	add_action( 'wp_ajax_seedprod_pro_get_nested_navmenu', 'seedprod_pro_get_nested_navmenu' );
	
}
/**
 * Ajax call to fetch selected WordPress inside seedprod builder.
 */
function seedprod_pro_get_nested_navmenu() {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_navmenu_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}
		$navmenu_name = isset( $_REQUEST['navmenu_name'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['navmenu_name'] ) ) : '';
		$divider      = isset( $_REQUEST['divider'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['divider'] ) ) : '';
		$layout       = isset( $_REQUEST['layout'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['layout'] ) ) : '';

		$walker_divider = true;
		if ( '' == $divider || 'v' == $layout ) {
			$walker_divider = false;
		}

		if ( true == $walker_divider ) {

			$args = array(
				'menu'            => $navmenu_name,
				'container_class' => 'nav-menu-bar',
				'menu_class'      => 'seedprod-menu-list',
				'walker'          => new SeedProd_Pro_Menu_Walker( $divider ),
			);

		} else {
			$args = array(
				'menu'            => $navmenu_name,
				'container_class' => 'nav-menu-bar',
				'menu_class'      => 'seedprod-menu-list',
			);
		}
		wp_nav_menu( $args );

		wp_die();

	}
}

/**
 * SeedProd Menu Walker Class for adding menu list divider
 */
class SeedProd_Pro_Menu_Walker extends Walker_Nav_Menu {
	/**
	 * Separators value.
	 *
	 * @var string
	 */
	private $separators;

	/**
	 * Create class instance.
	 *
	 * @param string $separators Separators passed to class.
	 */
	public function __construct( $separators = '' ) {
		$this->separators = $separators;
		add_filter( 'wp_nav_menu_items', 'seedprod_pro_remove_last_divider' );
	}

	/**
	 * Add/List separators.
	 *
	 * @param string  $output Output string.
	 * @param string  $item   Item string.
	 * @param integer $depth  Depth integer.
	 * @param array   $args   Args array.
	 * @return void
	 */
	public function end_el( &$output, $item, $depth = 0, $args = array() ) {
		$output .= '</li>';

		if ( 0 == $depth ) {
			if ( '' != $this->separators ) {
				$output .= "<li class='separator menu-item'>" . $this->separators . '</li>';
			}
		}
	}
}

/**
 * Shortcode to fetch select WordPress menu
 */
add_shortcode( 'seedprodnestedmenuwidget', 'seedprod_pro_wordpress_menuwidget' );

/**
 * WordPress Menu Widget.
 *
 * @param array $atts Shortcode attributes.
 * @return string $content
 */
function seedprod_pro_wordpress_menuwidget( $atts ) {

	$menu_atts = shortcode_atts(
		array(
			'menu'        => '',
			'menudivider' => '',
			'layout'      => 'h',
		),
		$atts
	);

	$navmenu_name = '';
	if ( isset( $menu_atts['menu'] ) ) {
		$navmenu_name = $menu_atts['menu'];
	}
	$navmenu_seperator = '';
	if ( isset( $menu_atts['menudivider'] ) ) {
		$navmenu_seperator = $menu_atts['menudivider'];
	}
	$layout = '';
	if ( isset( $menu_atts['layout'] ) ) {
		$layout = $menu_atts['layout'];
	}

	$walker_divider = true;
	if ( '' == $navmenu_seperator || 'v' == $layout ) {
		$walker_divider = false;
	}

	if ( true == $walker_divider ) {
		$args = array(
			'menu'            => $navmenu_name,
			'container_class' => 'nav-menu-bar',
			'menu_class'      => 'seedprod-menu-list',
			'walker'          => new SeedProd_Pro_Menu_Walker( $navmenu_seperator ),
		);
	} else {
		$args = array(
			'menu'            => $navmenu_name,
			'container_class' => 'nav-menu-bar',
			'menu_class'      => 'seedprod-menu-list',
		);
	}

	ob_start();
	wp_nav_menu( $args );
	$content = ob_get_contents();
	ob_end_clean();

	return $content;
}

/**
 * Remove last divider.
 *
 * @param string $items Items string.
 * @return string $items
 */
function seedprod_pro_remove_last_divider( $items ) {

	$substring = "<li class='separator menu-item'>|</li>";

	if ( substr( $items, -strlen( $substring ) ) === $substring ) {
		$items = substr( $items, 0, strlen( $items ) - strlen( $substring ) );
	}
	return $items;
}



/**
 * Shortcode to fetch  selected WordPress widget inside Seedprod builder
 */
add_shortcode( 'seedprodwpwidget', 'seedprod_pro_wordpress_widget' );

/**
 * WordPress Widget.
 *
 * @param array $atts Shortcode attributes.
 * @return string $content
 */
function seedprod_pro_wordpress_widget( $atts ) {

	$widget_name = $atts[0];
	unset( $atts[0] );

	// convert string bool
	foreach ( $atts as $k => $v ) {
		if ( 'true' === $v ) {
			$atts[ $k ] = true; }
		if ( 'false' === $v ) {
			$atts[ $k ] = false; }
		//$atts[$k] = ($v === 'true')? true: false;
	}

	global $wp_widget_factory;
	$inst     = $wp_widget_factory->widgets[ $widget_name ];
	$instance = $atts;

	ob_start();
	the_widget( $widget_name, $instance );
	$content = ob_get_contents();
	ob_end_clean();

	return $content;
}

