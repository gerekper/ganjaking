<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

function gm_template_header_action_buttons($styles) {

	global $groovyMenuSettings;
	$output_html ='';

	$show_gm_action = false;

	$searchForm = $groovyMenuSettings['searchForm'];
	if ( 'disable' !== $searchForm ) {
		$show_gm_action = true;
	}

	if ( ! gm_get_shop_is_catalog() && $groovyMenuSettings['woocommerceCart'] && class_exists( 'WooCommerce' ) ) {
		$show_gm_action = true;
	}

	if ( $show_gm_action ) {

		$output_html .= '<div class="gm-actions">';


		ob_start();
		/**
		 * Fires as first groovy menu action buttons.
		 *
		 * @since 2.2.0
		 */
		do_action( 'gm_main_menu_actions_button_first' );
		$output_html .= ob_get_clean();


		if ( $styles->get( 'general', 'show_divider' ) ) {
			$header_style = $styles->get( 'general', 'header' );
			if ( isset( $header_style['style'] ) && 1 === $header_style['style'] ) {
				$output_html .= '<span class="gm-nav-inline-divider"></span>';
			}
		}

		if ( 'disable' !== $searchForm ) {

			$searchIcon = 'gmi gmi-zoom-search';
			if ( $styles->getGlobal( 'misc_icons', 'search_icon' ) ) {
				$searchIcon = $styles->getGlobal( 'misc_icons', 'search_icon' );
			}

			if ( method_exists( 'GroovyMenuUtils', 'getSearchBlock' ) ) {
				$output_html .= GroovyMenuUtils::getSearchBlock( $searchIcon );
			}


		}

		if ( ! gm_get_shop_is_catalog() && $groovyMenuSettings['woocommerceCart'] && class_exists( 'WooCommerce' ) && function_exists( 'wc_get_page_id' ) ) {
			global $woocommerce;

			$qty = 0;
			if ( $woocommerce && isset( $woocommerce->cart ) ) {
				$qty = $woocommerce->cart->get_cart_contents_count();
			}
			$cartIcon = 'gmi gmi-bag';
			if ( $styles->getGlobal( 'misc_icons', 'cart_icon' ) ) {
				$cartIcon = $styles->getGlobal( 'misc_icons', 'cart_icon' );
			}

			$output_html .= '<div class="gm-minicart gm-dropdown">';

			$output_html .= '<a href="' . get_permalink( wc_get_page_id( 'cart' ) ) . '"
										   class="gm-minicart-link">
											<div class="gm-minicart-icon-wrapper">
												<i class="' . esc_attr( $cartIcon ) . '"></i>
												<span class="gm-minicart__txt">'
			                . esc_html__( 'My cart', 'groovy-menu' ) .
			                '</span>'
			                . groovy_menu_woocommerce_mini_cart_counter( $qty ) .
			                '</div>
										</a>';

			$output_html .= '<div class="gm-dropdown-menu gm-minicart-dropdown">
											<div class="widget_shopping_cart_content">';

			if ( $woocommerce && isset( $woocommerce->cart ) ) {
				ob_start();

				$template_mini_cart_path = get_stylesheet_directory() . '/woocommerce/cart/mini-cart.php';
				if ( file_exists( $template_mini_cart_path ) && is_file( $template_mini_cart_path ) ) {
					include $template_mini_cart_path;
				} elseif ( defined( 'WC_PLUGIN_FILE' ) ) {
					$original_mini_cart_path = dirname( WC_PLUGIN_FILE ) . '/templates/cart/mini-cart.php';
					if ( file_exists( $original_mini_cart_path ) && is_file( $original_mini_cart_path ) ) {
						$args['list_class'] = '';
						include $original_mini_cart_path;
					}
				}

				$output_html .= ob_get_clean();
			}


			$output_html .= '
											</div>
										</div>
									</div>
									';
		}


		ob_start();
		/**
		 * Fires as last groovy menu action buttons.
		 *
		 * @since 2.2.0
		 */
		do_action( 'gm_main_menu_actions_button_last' );
		$output_html .= ob_get_clean();


		$output_html .= '</div>';
	}


	return $output_html;

}