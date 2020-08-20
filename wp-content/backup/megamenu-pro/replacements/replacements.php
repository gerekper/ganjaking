<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // disable direct access
}

if ( ! class_exists('Mega_Menu_Replacements') ) :

/**
 *
 */
class Mega_Menu_Replacements {

	/**
	 * Constructor
	 *
	 * @since 1.3
	 */
	public function __construct() {
		add_filter( 'megamenu_tabs', array( $this, 'add_replacements_tab'), 10, 5 );
		add_filter( 'megamenu_walker_nav_menu_start_el', array( $this, 'process_replacements'), 10, 4 );
		add_filter( 'megamenu_scss_variables', array( $this, 'add_vars_to_scss'), 10, 4 );
		add_filter( 'megamenu_load_scss_file_contents', array( $this, 'append_scss'), 10 );

		add_shortcode( 'maxmegamenu_woo_cart_count', array($this, 'shortcode_woo_cart_count') );
		add_shortcode( 'maxmegamenu_woo_cart_total', array($this, 'shortcode_woo_cart_total') );
		add_shortcode( 'maxmegamenu_logout_url', array($this, 'shortcode_logout_url') );
		add_shortcode( 'maxmegamenu_user_info', array($this, 'shortcode_user_info') );
		add_shortcode( 'maxmegamenu_user_gravatar', array($this, 'shortcode_user_gravatar') );

		add_shortcode( 'maxmegamenu_buddypress_avatar', array($this, 'shortcode_buddypress_avatar') );
		add_shortcode( 'maxmegamenu_buddypress_notifications', array($this, 'shortcode_buddypress_notifications') );
		
		add_shortcode( 'maxmegamenu_edd_cart_count', array($this, 'shortcode_edd_cart_count') );
		add_shortcode( 'maxmegamenu_edd_cart_total', array($this, 'shortcode_edd_cart_total') );

		add_filter( 'woocommerce_add_to_cart_fragments', array($this, 'woocommerce_header_add_to_cart_fragment' ) );

	}

	/**
	 * Update cart total/count via AJAX
	 *
	 * @since 1.3.3
	 */
	public function woocommerce_header_add_to_cart_fragment( $fragments ) {

		$fragments['span.mega-menu-woo-cart-total'] = "<span class='mega-menu-woo-cart-total amount'>" . wp_kses_data( apply_filters("megamenu_woo_cart_total", WC()->cart->get_cart_subtotal() ) ) . "</span>";
		$fragments['span.mega-menu-woo-cart-count'] = "<span class='mega-menu-woo-cart-count'>" . WC()->cart->cart_contents_count . "</span>";

		return $fragments;
	}


	/**
	 * Append the logo SCSS to the main SCSS file
	 *
	 * @since 1.3
	 * @param string $scss
	 * @param string
	 */
	public function append_scss( $scss ) {

		$path = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'scss/replacements.scss';

		$contents = file_get_contents( $path );

		return $scss . $contents;

	}


	/**
	 * Create a new variable containing the IDs and icons of menu items to be used by the SCSS file
	 *
	 * @param array $vars
	 * @param string $location
	 * @param string $theme
	 * @param int $menu_id
	 * @return array - all custom SCSS vars
	 * @since 1.3
	 */
	public function add_vars_to_scss( $vars, $location, $theme, $menu_id ) {

		$menu_items = wp_get_nav_menu_items( $menu_id );

		$custom_vars = array();

		if ( is_array( $menu_items ) ) {

			foreach ( $menu_items as $menu_order => $item ) {

				if ( $settings = get_post_meta( $item->ID, "_megamenu", true ) ) {

					if ( isset( $settings['replacements']['type'] ) && in_array($settings['replacements']['type'], array('search')) ) {

						$custom_search_icon_enabled = isset($settings['replacements']['search']['search_icon_type']) && $settings['replacements']['search']['search_icon_type'] == 'custom' && isset($settings['icon']) && ($settings['icon'] == 'custom');

						if ( $custom_search_icon_enabled && isset( $settings['custom_icon']['id']) ) {
							$custom_search_icon_url = $this->get_resized_image_url( $settings['custom_icon']['id'], $settings['custom_icon']['width'], $settings['custom_icon']['height'] );
						} else {
							$custom_search_icon_url = "";
						}

						if ( $custom_search_icon_enabled && isset( $settings['custom_icon']['id_hover']) && $settings['custom_icon']['id_hover'] > 0 ) {
							$custom_search_icon_url_hover = $this->get_resized_image_url( $settings['custom_icon']['id_hover'], $settings['custom_icon']['width'], $settings['custom_icon']['height'] );
						} else {
							$custom_search_icon_url_hover = $custom_search_icon_url;
						}

						if ( $custom_search_icon_enabled && isset( $settings['custom_icon']['width']) ) {
							$custom_search_icon_width = $settings['custom_icon']['width'] . "px";
						} else {
							$custom_search_icon_width = isset($settings['replacements']['search']['width']) ? $settings['replacements']['search']['width'] : '200px';
						}

						if ( $custom_search_icon_enabled && isset( $settings['custom_icon']['height']) ) {
							$custom_search_icon_height = $settings['custom_icon']['height'] . "px";
						} else {
							$custom_search_icon_height = isset($settings['replacements']['search']['height']) ? $settings['replacements']['search']['height'] : '200px';
						}

						$styles = array(
							'id' => $item->ID,
							'search_height' => isset($settings['replacements']['search']['height']) ? $settings['replacements']['search']['height'] : '30px',
							'search_text_color' => isset($settings['replacements']['search']['text_color']) ? $settings['replacements']['search']['text_color'] : '#333',
							'search_icon_color_closed' => isset($settings['replacements']['search']['icon_color_closed']) ? $settings['replacements']['search']['icon_color_closed'] : '#fff',
							'search_icon_color_open' => isset($settings['replacements']['search']['icon_color_open']) ? $settings['replacements']['search']['icon_color_open'] : '#333',
							'search_background_color_closed' => isset($settings['replacements']['search']['background_color_closed']) ? $settings['replacements']['search']['background_color_closed'] : 'transparent',
							'search_background_color_open' => isset($settings['replacements']['search']['background_color_open']) ? $settings['replacements']['search']['background_color_open'] : '#fff',
							'search_border_radius' => isset($settings['replacements']['search']['border_radius']) ? $settings['replacements']['search']['border_radius'] : '2px',
							'search_vertical_offset' => isset($settings['replacements']['search']['vertical_offset']) ? $settings['replacements']['search']['vertical_offset'] : '0px',
							'search_width' => isset($settings['replacements']['search']['width']) ? $settings['replacements']['search']['width'] : '200px',
							'search_custom_icon_enabled' => $custom_search_icon_enabled ? 'true' : 'false',
							'search_custom_icon_url' => "'" . $custom_search_icon_url . "'",
							'search_custom_icon_url_hover' => "'" . $custom_search_icon_url_hover . "'",
							'search_custom_icon_width' => $custom_search_icon_width,
							'search_custom_icon_height' => $custom_search_icon_height,

						);

						$custom_vars[ $item->ID ] = $styles;

					}

				}

			}

		}

		//$custom_styles:(
		// (123, red, 150px),
		// (456, green, null),
		// (789, blue, 90%),());

		if ( count( $custom_vars ) ) {

			$list = "(";

			foreach ( $custom_vars as $id => $vals ) {
				$list .= "(" . implode( ",", $vals ) . "),";
			}

			// Always add an empty list item to meke sure there are always at least 2 items in the list
			// Lists with a single item are not treated the same way by SASS
			$list .= "());";

			$vars['replacements_search'] = $list;

		} else {

			$vars['replacements_search'] = "()";

		}

		return $vars;

	}


	/**
	 * Replace a menu item with the selected type
	 *
	 * @param string $item_output
	 * @param object $item
	 * @param int $depth
	 * @param array $args
	 * @return string
	 */
	public function process_replacements( $item_output, $item, $depth, $args ) {

		if ( isset( $item->megamenu_settings['replacements'] ) && $item->megamenu_settings['replacements']['type'] != 'disabled' ) {

			$type = $item->megamenu_settings['replacements']['type'];

			if ( $type == 'html' ) {
				return $this->do_html_replacement( $item, $item_output );
			}

			if ( $type == 'search' ) {
				return $this->do_search_replacement( $item, $item_output );
			}

			if ( $type == 'logo' ) {
				return $this->do_logo_replacement( $item, $item_output );
			}

			if ( $type == 'woo_cart_count' ) {
				return $this->do_woo_cart_count_replacement( $item, $item_output );
			}

			if ( $type == 'woo_cart_total' ) {
				return $this->do_woo_cart_total_replacement( $item, $item_output );
			}

			if ( $type == 'edd_cart_count' ) {
				return $this->do_edd_cart_count_replacement( $item, $item_output );
			}

			if ( $type == 'edd_cart_total' ) {
				return $this->do_edd_cart_total_replacement( $item, $item_output );
			}

			if ( $type == 'buddypress_avatar' ) {
				return $this->do_buddypress_avatar_replacement( $item, $item_output );
			}

			if ( $type == 'buddypress_notifications' ) {
				return $this->do_buddypress_notifications_replacement( $item, $item_output );
			}

		}

		return $item_output;

	}

	/**
	 * Return woocommerce cart total (e.g. $5.99)
	 *
	 * @since 1.3.3
	 * @return string
	 */
	public function shortcode_edd_cart_total() {

		if ( function_exists('edd_cart_total') ) {
			return "<span class='mega-menu-edd-cart-total'>" . edd_cart_total(false) . "</span>";
		}

	}



	/**
	 * Return user gravatar
	 *
	 * @since 1.5.3
	 * @return string
	 */
	public function shortcode_user_gravatar( $atts ) {
		$atts = shortcode_atts( array(
			'size' => '25',
		), $atts, 'maxmegamenu_user_gravatar' );

		$current_user = wp_get_current_user();

		return "<img class='mmm_gravatar' src='https://www.gravatar.com/avatar/" . md5($current_user->user_email) . "?s=" . $atts['size'] . "' />";

	}


	/**
	 * Return buddypress avatar
	 *
	 * @since 1.6.2
	 * @return string
	 */
	public function shortcode_buddypress_avatar( $atts ) {
		$atts = shortcode_atts( array(
			'size' => '50',
		), $atts, 'maxmegamenu_buddypress_avatar' );

		if ( function_exists('get_avatar') ) {

			$current_user = wp_get_current_user();

			return get_avatar( $current_user->ID, $atts['size'] );
		}

	}

	/**
	 * Return buddypress notification count
	 *
	 * @since 1.6.2
	 * @return string
	 */
	public function shortcode_buddypress_notifications( $atts ) {

		if ( function_exists('bp_notifications_get_unread_notification_count') ) {
			return bp_notifications_get_unread_notification_count( bp_loggedin_user_id() );
		}

	}


	/**
	 * Return user information
	 *
	 * @since 1.5.3
	 * @return string
	 */
	public function shortcode_user_info( $atts ) {
		$current_user = wp_get_current_user();

		$atts = shortcode_atts( array(
			'field' => 'display_name',
		), $atts, 'maxmegamenu_user_info' );

		if ( $atts['field'] == 'user_email_md5') {
			return md5($current_user->user_email);
		}

		if ( in_array( $atts['field'] , array( 'user_email', 'user_firstname', 'user_lastname', 'display_name', 'ID') ) ) {
			return $current_user->{$atts['field']};
		}

		return $current_user->display_name;

	}


	/**
	 * Return a logout url
	 *
	 * @since 1.5.3
	 * @return string
	 */
	public function shortcode_logout_url( $atts ) {
		global $wp;

		$atts = shortcode_atts( array(
			'redirect_to' => 'current',
		), $atts, 'maxmegamenu_logout_url' );

		if ( $atts['redirect_to'] == 'home' ) {
			return wp_logout_url( home_url() );
		}

		if ( $atts['redirect_to'] == 'current' ) {
			return wp_logout_url( home_url( add_query_arg( array(), $wp->request ) ) );
		}

		return wp_logout_url();

	}

	/**
	 * Return woocommerce number of items in cart (e.g 1)
	 *
	 * @since 1.3.3
	 * @return string
	 */
	public function shortcode_edd_cart_count() {

		if ( function_exists('edd_get_cart_quantity') ) {
			return "<span class='mega-menu-edd-cart-count'>" . edd_get_cart_quantity() . "</span>";
		}

	}

	/**
	 * Replace a menu item with an EDD cart total
	 *
	 * @since 1.3.3
	 * @param object $item
	 * @param string $item_output
	 * @return string
	 */
	private function do_edd_cart_total_replacement( $item, $item_output ) {

		if ( function_exists('edd_cart_total') ) {
			$replacement = do_shortcode('[maxmegamenu_edd_cart_total]');

			return str_replace( strip_tags( $item_output ), $replacement, $item_output );
		}

		return $item_output;

	}

	/**
	 * Replace a menu item with an EDD cart count
	 *
	 * @since 1.3.3
	 * @param object $item
	 * @param string $item_output
	 * @return string
	 */
	private function do_edd_cart_count_replacement( $item, $item_output ) {

		if ( function_exists('edd_get_cart_quantity') ) {
			$replacement = do_shortcode('[maxmegamenu_edd_cart_count]');

			return str_replace( strip_tags( $item_output ), $replacement, $item_output );
		}

		return $item_output;

	}

	/**
	 * Replace a menu item with a buddypress avatar
	 *
	 * @since 1.6.2
	 * @param object $item
	 * @param string $item_output
	 * @return string
	 */
	private function do_buddypress_avatar_replacement( $item, $item_output ) {

		if ( function_exists('get_avatar') ) {
			$replacement = do_shortcode('[maxmegamenu_buddypress_avatar]');

			return str_replace( strip_tags( $item_output ), $replacement, $item_output );
		}

		return $item_output;

	}


	/**
	 * Replace a menu item with a buddypress avatar
	 *
	 * @since 1.6.2
	 * @param object $item
	 * @param string $item_output
	 * @return string
	 */
	private function do_buddypress_notifications_replacement( $item, $item_output ) {

		if ( function_exists('get_avatar') ) {
			$replacement = do_shortcode('[maxmegamenu_buddypress_notifications]');

			return str_replace( strip_tags( $item_output ), $replacement, $item_output );
		}

		return $item_output;

	}

	/**
	 * Return woocommerce cart total (e.g. $5.99)
	 *
	 * @since 1.3.3
	 * @return string
	 */
	public function shortcode_woo_cart_total() {

		if ( function_exists('WC') ) { 
			if ( WC()->cart ) {
				return "<span class='mega-menu-woo-cart-total amount'>" . wp_kses_data( apply_filters("megamenu_woo_cart_total", WC()->cart->get_cart_subtotal() ) ) . "</span>";
			} else {
				return "<span class='mega-menu-woo-cart-total amount'></span>";
			}
		}

	}


	/**
	 * Return woocommerce number of items in cart (e.g 1)
	 *
	 * @since 1.3.3
	 * @return string
	 */
	public function shortcode_woo_cart_count() {

		if ( function_exists('WC') ) {
			if ( WC()->cart ) {
				return "<span class='mega-menu-woo-cart-count'>" . WC()->cart->cart_contents_count . "</span>";
			} else {
				return "<span class='mega-menu-woo-cart-count'></span>";
			}
		}

	}

	/**
	 * Replace a menu item with a WooCommerce cart total
	 *
	 * @since 1.3.3
	 * @param object $item
	 * @param string $item_output
	 * @return string
	 */
	private function do_woo_cart_total_replacement( $item, $item_output ) {

		if ( function_exists('WC') ) {
			$replacement = do_shortcode('[maxmegamenu_woo_cart_total]');
			return str_replace( strip_tags( $item_output ), $replacement, $item_output );
		}

		return $item_output;

	}

	/**
	 * Replace a menu item with a WooCommerce cart count
	 *
	 * @since 1.3.3
	 * @param object $item
	 * @param string $item_output
	 * @return string
	 */
	private function do_woo_cart_count_replacement( $item, $item_output ) {

		if ( function_exists('WC') ) {
			$replacement = do_shortcode('[maxmegamenu_woo_cart_count]');

			return str_replace( strip_tags( $item_output ), $replacement, $item_output );
		}

		return $item_output;

	}


	/**
	 * Replace a menu item with html
	 *
	 * @since 1.3
	 * @param object $item
	 * @param string $item_output
	 * @return string
	 */
	private function do_html_replacement( $item, $item_output ) {

		if ( ! isset( $item->megamenu_settings['replacements']['html']['code'] ) ) {
			return $item_output;
		}

		if ( ! strlen( $item->megamenu_settings['replacements']['html']['code'] ) ) {
			return $item_output;
		}

		$replacement = do_shortcode( $item->megamenu_settings['replacements']['html']['code'] );

		if ( $item->megamenu_settings['replacements']['html']['mode'] == 'inner' ) {
			// ensure we only replace the menu item text
			return str_replace( ">" . strip_tags($item_output) . "<", ">" . $replacement . "<", $item_output );
		} elseif ( $item->megamenu_settings['replacements']['html']['mode'] == 'href' ) {

			return str_replace( $item->url, $replacement, $item_output );

		} else {
			return $replacement;
		}

	}


	/**
	 * Replace a menu item with a logo
	 *
	 * @since 1.3
	 * @param object $item
	 * @param string $item_output
	 * @return string
	 */
	private function do_logo_replacement( $item, $item_output ) {

		if ( ! isset( $item->megamenu_settings['replacements']['logo']['id'] ) ) {
			return $item_output;
		} else {
			$id = $item->megamenu_settings['replacements']['logo']['id'];
		}

		$width = isset( $item->megamenu_settings['replacements']['logo']['width'] ) ? absint( $item->megamenu_settings['replacements']['logo']['width'] ) : '100';
		$height = isset( $item->megamenu_settings['replacements']['logo']['height'] ) ? absint( $item->megamenu_settings['replacements']['logo']['height'] ) : '100';
		$src = $this->get_resized_image_url( $id, $width, $height );

		$width_2x = $width * 2;
		$height_2x = $height * 2;

		$img_meta = wp_get_attachment_metadata( $id );

		$attributes = array(
			'class' => 'mega-menu-logo',
			'width' => $width,
			'height' => $height,
			'src' => $src,
			'alt' => get_post_meta( $id, '_wp_attachment_image_alt', true )
		);

		// check image is large enough to create 2x version
		if ( ( isset( $img_meta['width'] ) && $img_meta['width'] >= $width_2x ) && ( isset( $img_meta['height'] ) && $img_meta['height'] >= $height_2x ) ) {
			// double the size for 2x logo
			$src_2x = $this->get_resized_image_url( $id, $width_2x, $height_2x );
			$attributes['srcset'] = $src_2x . " 2x";
		}

		$logo_attributes = apply_filters( "megamenu_logo_attributes", $attributes, $item );

		$url = $item->url;

		if ( $url == '#' || $url == 'http://#' ) {
			$url = get_home_url();
		}
		
		$anchor_attributes = apply_filters( "megamenu_logo_anchor_attributes", array(
			'class' => 'mega-menu-link mega-menu-logo',
			'href' => $url,
			'target' => $item->target,
			'title' => $item->attr_title
		), $item );

		$replacement = "<a";

		foreach ( $anchor_attributes as $name => $val ) {
			if ( strlen( $val ) ) {
				$replacement .= " " . $name ."='" . esc_attr( $val ) . "'";
			}
		}

		$replacement .= "><img";

		foreach ( $logo_attributes as $name => $val ) {
			if ( strlen( $val ) ) {
				$replacement .= " " . $name ."='" . esc_attr( $val ) . "'";
			}
		}

		$replacement .= " /></a>";

		return $replacement;

	}


	/**
	 * Replace a menu item with a search box
	 *
	 * @since 1.3
	 * @param object $item
	 * @param string $item_output
	 * @return string
	 */
	private function do_search_replacement( $item, $item_output ) {

		$placeholder = isset($item->megamenu_settings['replacements']['search']['placeholder_text']) ? $item->megamenu_settings['replacements']['search']['placeholder_text'] : "Search...";

		$type = isset($item->megamenu_settings['replacements']['search']['type']) ? $item->megamenu_settings['replacements']['search']['type'] : "expand_to_left";

		$search_icon_type = isset($item->megamenu_settings['replacements']['search']['search_icon_type']) ? $item->megamenu_settings['replacements']['search']['search_icon_type'] : "dashicons-search";

		$woocommerce = isset($item->megamenu_settings['replacements']['search']['woocommerce']) ? $item->megamenu_settings['replacements']['search']['woocommerce'] : "false";

		$name = apply_filters("megamenu_search_var", "s");
		$action = apply_filters("megamenu_search_action", trailingslashit( home_url() ) );

		$inputs = "";

		if ($woocommerce === 'true') {
			$inputs = "<input type='hidden' name='post_type' value='product' />";
		}

		$search_icon_html = "<span class='dashicons dashicons-search search-icon'></span>";

		if ( $search_icon_type == 'custom' && isset( $item->megamenu_settings['icon'] ) ) {

			if ( strpos( $item->megamenu_settings['icon'], 'dashicons-' ) !== FALSE ) {
				$search_icon_html = "<span class='dashicons {$item->megamenu_settings['icon']} search-icon'></span>";
			}

			$icon_prefix = substr( $item->megamenu_settings['icon'], 0, 3 );

			if ( $icon_prefix == 'fa-' ) {
				$search_icon_html = "<span class='fa {$item->megamenu_settings['icon']} search-icon'></span>";
			}

			if ( in_array( $icon_prefix, array( 'fab', 'fas', 'far' ) ) ) {
				$search_icon_html = "<span class='{$item->megamenu_settings['icon']} search-icon'></span>";
			}

			if ( strpos( $item->megamenu_settings['icon'], 'genericon-' ) !== FALSE ) {
				$search_icon_html = "<span class='genericon {$item->megamenu_settings['icon']} search-icon'></span>";
			}

			if ( $item->megamenu_settings['icon'] == 'custom') {
				$search_icon_html = "<span class='search-icon'></span>";
			}

		}

		if ( $type == 'expand_to_left' ) {
			$html = "<div class='mega-search-wrap'><form class='mega-search expand-to-left mega-search-closed' role='search' action='" .  $action . "'>
						" . $search_icon_html . "
						<input type='submit' value='" . __( "Search" , "megamenupro" ) . "'>
						<input type='text' aria-label='{$placeholder}' data-placeholder='{$placeholder}' name='{$name}'>
						" . apply_filters("megamenu_search_inputs", $inputs) . "
					</form></div>";
		}

		if ( $type == 'expand_to_right' ) {
			$html = "<div class='mega-search-wrap'><form class='mega-search expand-to-right mega-search-closed' role='search' action='" .  $action . "'>
						" . $search_icon_html . "
						<input type='submit' value='" . __( "Search" , "megamenupro" ) . "'>
						<input type='text' aria-label='{$placeholder}' data-placeholder='{$placeholder}' name='{$name}'>
						" . apply_filters("megamenu_search_inputs", $inputs) . "
					</form></div>";
		}

		if ( $type == 'static' ) {
			$html = "<div class='mega-search-wrap mega-static'><form class='mega-search mega-search-open' role='search' action='" .  $action . "'>
						" . $search_icon_html . "
						<input type='submit' value='" . __( "Search" , "megamenupro" ) . "'>
						<input type='text' aria-label='{$placeholder}' data-placeholder='{$placeholder}' placeholder='{$placeholder}' name='{$name}'>
						" . apply_filters("megamenu_search_inputs", $inputs) . "
					</form></div>";
		}

		if ( function_exists("wpml_get_language_input_field") ) {
			$html = str_replace("</form>", wpml_get_language_input_field() . "</form>", $html);
		}


		return apply_filters('megamenu_search_replacement_html', $html);
	}


	/**
	 * Add the Styling tab to the menu item options
	 *
	 * @since 1.3
	 * @param array $tabs
	 * @param int $menu_item_id
	 * @param int $menu_id
	 * @param int $menu_item_depth
	 * @param array $menu_item_meta
	 * @return string
	 */
	public function add_replacements_tab( $tabs, $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) {

		$type = isset( $menu_item_meta['replacements']['type'] ) ? $menu_item_meta['replacements']['type'] : 'disabled';

		$html_code = isset( $menu_item_meta['replacements']['html']['code'] ) ? $menu_item_meta['replacements']['html']['code'] : '';
		$html_mode = isset( $menu_item_meta['replacements']['html']['mode'] ) ? $menu_item_meta['replacements']['html']['mode'] : 'inner';
		$logo_id = isset( $menu_item_meta['replacements']['logo']['id'] ) ? $menu_item_meta['replacements']['logo']['id'] : false;
		$logo_width = isset( $menu_item_meta['replacements']['logo']['width'] ) ? absint( $menu_item_meta['replacements']['logo']['width'] ) : apply_filters("megamenu_logo_default_width", 100);
		$logo_height = isset( $menu_item_meta['replacements']['logo']['height'] ) ? absint( $menu_item_meta['replacements']['logo']['height'] ) : apply_filters("megamenu_logo_default_height", 60);
		$logo_url = isset( $menu_item_meta['replacements']['logo']['url'] ) ? $menu_item_meta['replacements']['logo']['url'] : get_home_url();
		$search_height = isset( $menu_item_meta['replacements']['search']['height'] ) ? $menu_item_meta['replacements']['search']['height'] : apply_filters("megamenu_search_default_height", '30px');
		$search_width = isset( $menu_item_meta['replacements']['search']['width'] ) ? $menu_item_meta['replacements']['search']['width'] : apply_filters("megamenu_search_default_width", '200px');
		$search_text_color = isset( $menu_item_meta['replacements']['search']['text_color'] ) ? $menu_item_meta['replacements']['search']['text_color'] : '#333';
		$search_icon_color_closed = isset( $menu_item_meta['replacements']['search']['icon_color_closed'] ) ? $menu_item_meta['replacements']['search']['icon_color_closed'] : '#fff';
		$search_icon_color_open = isset( $menu_item_meta['replacements']['search']['icon_color_open'] ) ? $menu_item_meta['replacements']['search']['icon_color_open'] : '#333';
		$search_icon_type = isset( $menu_item_meta['replacements']['search']['search_icon_type'] ) ? $menu_item_meta['replacements']['search']['search_icon_type'] : 'dashicons-search';

		$search_background_color_closed = isset( $menu_item_meta['replacements']['search']['background_color_closed'] ) ? $menu_item_meta['replacements']['search']['background_color_closed'] : 'transparent';
		$search_background_color_open = isset( $menu_item_meta['replacements']['search']['background_color_open'] ) ? $menu_item_meta['replacements']['search']['background_color_open'] : '#fff';
		$search_border_radius = isset( $menu_item_meta['replacements']['search']['border_radius'] ) ? $menu_item_meta['replacements']['search']['border_radius'] : '2px';
		$search_placeholder_text = isset( $menu_item_meta['replacements']['search']['placeholder_text'] ) ? $menu_item_meta['replacements']['search']['placeholder_text'] : 'Search...';
		$search_type = isset( $menu_item_meta['replacements']['search']['type'] ) ? $menu_item_meta['replacements']['search']['type'] : 'expand_to_left';
		$search_vertical_offset = isset( $menu_item_meta['replacements']['search']['vertical_offset'] ) ? $menu_item_meta['replacements']['search']['vertical_offset'] : '0px';
		$search_woocommerce = isset( $menu_item_meta['replacements']['search']['woocommerce'] ) ? $menu_item_meta['replacements']['search']['woocommerce'] : 'false';

		$logo_src = "";

		if ( $logo_id ) {
			$logo = wp_get_attachment_image_src( $logo_id, 'thumbnail' );
			$logo_src = $logo[0];
		}

		$inner_display = $html_mode == 'inner' ? 'block' : 'none';
		$outer_display = $html_mode == 'outer' ? 'block' : 'none';
		$href_display = $html_mode == 'href' ? 'block' : 'none';
		$logo_display = $type == 'logo' ? 'table-row' : 'none';
		$html_display = $type == 'html' ? 'table-row' : 'none';
		$search_display = $type == 'search' ? 'table-row' : 'none';

		$html  = "<form>";
		$html .= "    <input type='hidden' name='_wpnonce' value='" . wp_create_nonce('megamenu_edit') . "' />";
		$html .= "    <input type='hidden' name='menu_item_id' value='{$menu_item_id}' />";
		$html .= "    <input type='hidden' name='action' value='mm_save_menu_item_settings' />";
		$html .= "    <input type='hidden' name='clear_cache' value='true' />";
		$html .= "    <h4 class='first'>" . __("Replacements", "megamenu_pro") . "</h4>";
		$html .= "    <p class='tab-description'>" . __("Replace this menu item with something else: a logo, a search box, WooCommerce total, EDD total, custom HTML or a shortcode", "megamenu_pro") . "</p>";
		$html .= "    <table>";
		$html .= "        <tr class='type'>";
		$html .= "            <td class='mega-name'>" . __("Type", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .= "                <select name='settings[replacements][type]' id='mega_replacement_type'>";
		$html .= "                    <option value='disabled' " . selected( $type, 'disabled', false ) . ">" . __("Disabled", "megamenupro") . "</option>";
		$html .= "                    <option value='logo' " . selected( $type, 'logo', false ) . ">" . __("Logo", "megamenupro") . "</option>";
		$html .= "                    <option value='search' " . selected( $type, 'search', false ) . ">" . __("Search box", "megamenupro") . "</option>";
		$html .= "                    <option value='html' " . selected( $type, 'html', false ) . ">" . __("HTML", "megamenupro") . "</option>";
		$html .= "                    <option value='woo_cart_total' " . selected( $type, 'woo_cart_total', false ) . ">" . __("WooCommerce Cart Total", "megamenupro") . "</option>";
		$html .= "                    <option value='woo_cart_count' " . selected( $type, 'woo_cart_count', false ) . ">" . __("WooCommerce Cart Count", "megamenupro") . "</option>";
		$html .= "                    <option value='edd_cart_total' " . selected( $type, 'edd_cart_total', false ) . ">" . __("EDD Cart Total", "megamenupro") . "</option>";
		$html .= "                    <option value='edd_cart_count' " . selected( $type, 'edd_cart_count', false ) . ">" . __("EDD Cart Count", "megamenupro") . "</option>";
		$html .= "                    <option value='buddypress_avatar' " . selected( $type, 'buddypress_avatar', false ) . ">" . __("BuddyPress Avatar", "megamenupro") . "</option>";
		$html .= "                    <option value='buddypress_notifications' " . selected( $type, 'buddypress_notifications', false ) . ">" . __("BuddyPress Notifications", "megamenupro") . "</option>";
		$html .= "                </select>";
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='logo' style='display: {$logo_display}'>";
		$html .= "            <td class='mega-name'>";
		$html .=                  __("Logo", "megamenupro");
		$html .= "                <div class='mega-description'>" . __( "Choose a logo from your Media Library" , "megamenupro") . "</div>";
		$html .=              "</td>";
		$html .= "            <td class='mega-value'>";
		$html .= "                <div class='mmm_image_selector' data-src='{$logo_src}' data-field='custom_logo_id' data-menu-item-id='" . esc_attr( $menu_item_id ) . "'></div>";
		$html .= "                <input type='hidden' id='custom_logo_id' name='settings[replacements][logo][id]' value='{$logo_id}' />";
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='logo' style='display: {$logo_display}'>";
		$html .= "            <td class='mega-name'>";
		$html .=                  __("Width", "megamenupro");
		$html .=              "</td>";
		$html .= "            <td class='mega-value'>";
		$html .= "                <input type='number' name='settings[replacements][logo][width]' class='mm_logo_width' value='{$logo_width}' required='required' />px";
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='logo' style='display: {$logo_display}'>";
		$html .= "            <td class='mega-name'>";
		$html .=                  __("Height", "megamenupro");
		$html .=              "</td>";
		$html .= "            <td class='mega-value'>";
		$html .= "                <input type='number' name='settings[replacements][logo][height]' class='mm_logo_width' value='{$logo_height}' required='required' />px";
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='logo logo_tip' style='display: {$logo_display}'>";
		$html .= "            <td class='mega-name'>";
		$html .=              "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  __("Mobile Menu Tip: To hide this logo from the list of items in your mobile menu, go to the 'Settings' tab and enable 'Hide Item on Mobile'. Then you can add the logo directly to your mobile toggle bar, under Mega Menu > Menu Themes > Mobile Menu > Toggle Bar Designer", "megamenupro");
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='search' style='display: {$search_display}'>";
		$html .= "            <td class='mega-name'>";
		$html .=                  __("Style", "megamenupro");
		$html .= "                <div class='mega-description'>" . __("Select the search box style", "megamenupro") . "</div>";
		$html .=              "</td>";
		$html .= "            <td class='mega-value'>";
		$html .= "                <select name='settings[replacements][search][type]'>";
		$html .= "                    <option value='expand_to_left' " . selected( $search_type, 'expand_to_left', false ) . ">" . __("Expand to Left", "megamenupro") . "</option>";
		$html .= "                    <option value='expand_to_right' " . selected( $search_type, 'expand_to_right', false ) . ">" . __("Expand to Right", "megamenupro") . "</option>";
		$html .= "                    <option value='static' " . selected( $search_type, 'static', false ) . ">" . __("Static", "megamenupro") . "</option>";
		$html .= "                </select>";
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='search' style='display: {$search_display}'>";
		$html .= "            <td class='mega-name'>";
		$html .=                  __("Height", "megamenupro");
		$html .= "                <div class='mega-description'>" . __("Define the height of the search icon and search input box", "megamenupro") . "</div>";
		$html .=              "</td>";
		$html .= "            <td class='mega-value'>";
		$html .= "                <input type='text' name='settings[replacements][search][height]' value='{$search_height}' required='required' />";
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='search' style='display: {$search_display}'>";
		$html .= "            <td class='mega-name'>";
		$html .=                  __("Width", "megamenupro");
		$html .= "                <div class='mega-description'>" . __("Define the width of the search box when expanded", "megamenupro") . "</div>";
		$html .=              "</td>";
		$html .= "            <td class='mega-value'>";
		$html .= "                <input type='text' name='settings[replacements][search][width]' value='{$search_width}' required='required' />";
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='search' style='display: {$search_display}'>";
		$html .= "            <td class='mega-name'>";
		$html .=                  __("Text Color", "megamenupro");
		$html .= "                <div class='mega-description'>" . __("Define the color for the text within the search input box", "megamenupro") . "</div>";
		$html .=              "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_color_option('text_color', $search_text_color);
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='search' style='display: {$search_display}'>";
		$html .= "            <td class='mega-name'>";
		$html .=                  __("Icon Color", "megamenupro");
		$html .= "                <div class='mega-description'>" . __("Search icon color", "megamenupro") . "</div>";
		$html .=              "</td>";
		$html .= "            <td class='mega-value'>";
		$html .= "                <label>";
		$html .= "                    <span class='mega-short-desc'>" . __("Closed State", "megamenupro") . "</span>";
		$html .=                      $this->print_theme_color_option('icon_color_closed', $search_icon_color_closed);
		$html .= "                </label>";
		$html .= "                <label>";
		$html .= "                    <span class='mega-short-desc'>" . __("Open State", "megamenupro") . "</span>";
		$html .=                      $this->print_theme_color_option('icon_color_open', $search_icon_color_open);
		$html .= "                </label>";
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='search' style='display: {$search_display}'>";
		$html .= "            <td class='mega-name'>";
		$html .=                  __("Search Icon", "megamenupro");
		$html .= "                <div class='mega-description'>" . __("", "megamenupro") . "</div>";
		$html .=              "</td>";
		$html .= "            <td class='mega-value'>";
		$html .= "                <select name='settings[replacements][search][search_icon_type]'>";
		$html .= "                    <option value='dashicons-search' " . selected( $search_icon_type, 'dashicons-search', false ) . ">" . __("Dashicons Search", "megamenupro") . "</option>";
		$html .= "                    <option value='custom' " . selected( $search_icon_type, 'custom', false ) . ">" . __("Use Menu Item Icon", "megamenupro") . "</option>";
		$html .= "                </select>";
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='search' style='display: {$search_display}'>";
		$html .= "            <td class='mega-name'>";
		$html .=                  __("Background Color", "megamenupro");
		$html .= "                <div class='mega-description'>" . __("Background color for search icon and search input box", "megamenupro") . "</div>";
		$html .=              "</td>";
		$html .= "            <td class='mega-value'>";
		$html .= "                <label>";
		$html .= "                    <span class='mega-short-desc'>" . __("Closed State", "megamenupro") . "</span>";
		$html .=                      $this->print_theme_color_option('background_color_closed', $search_background_color_closed);
		$html .= "                </label>";
		$html .= "                <label>";
		$html .= "                    <span class='mega-short-desc'>" . __("Open State", "megamenupro") . "</span>";
		$html .=                      $this->print_theme_color_option('background_color_open', $search_background_color_open);
		$html .= "                </label>";
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='search' style='display: {$search_display}'>";
		$html .= "            <td class='mega-name'>";
		$html .=                  __("Border Radius", "megamenupro");
		$html .= "                <div class='mega-description'>" . __("Set rounded corners for the search icon and search input box", "megamenupro") . "</div>";
		$html .=              "</td>";
		$html .= "            <td class='mega-value'>";
		$html .= "                <input type='text' name='settings[replacements][search][border_radius]' value='{$search_border_radius}' required='required' />";
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='search' style='display: {$search_display}'>";
		$html .= "            <td class='mega-name'>";
		$html .=                  __("Placeholder Text", "megamenupro");
		$html .= "                <div class='mega-description'>" . __("Define the pre-populated text within the search box", "megamenupro") . "</div>";
		$html .=              "</td>";
		$html .= "            <td class='mega-value'>";
		$html .= "                <input type='text' name='settings[replacements][search][placeholder_text]' value='{$search_placeholder_text}' />";
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='search' style='display: {$search_display}'>";
		$html .= "            <td class='mega-name'>";
		$html .=                  __("Vertical Offset", "megamenupro");
		$html .= "                <div class='mega-description'>" . __("Vertical positioning for the search box", "megamenupro") . "</div>";
		$html .=              "</td>";
		$html .= "            <td class='mega-value'>";
		$html .= "                <input type='text' name='settings[replacements][search][vertical_offset]' value='{$search_vertical_offset}' required='required' />";
		$html .= "            </td>";
		$html .= "        </tr>";

		if (is_plugin_active('woocommerce/woocommerce.php')) {
			$html .= "        <tr class='search' style='display: {$search_display}'>";
			$html .= "            <td class='mega-name'>";
			$html .=                  __("WooCommerce Search", "megamenupro");
			$html .= "                <div class='mega-description'>" . __("Use WooCommerce search results template?", "megamenupro") . "</div>";
			$html .=              "</td>";
			$html .= "            <td class='mega-value'>";
			$html .= "                <select name='settings[replacements][search][woocommerce]'>";
			$html .= "                    <option value='true' " . selected( $search_woocommerce, 'true', false ) . ">" . __("Yes", "megamenupro") . "</option>";
			$html .= "                    <option value='false' " . selected( $search_woocommerce, 'false', false ) . ">" . __("No", "megamenupro") . "</option>";
			$html .= "                </select>";
			$html .= "            </td>";
			$html .= "        </tr>";
		}

		$html .= "        <tr class='html' style='display: {$html_display}'>";
		$html .= "            <td class='mega-name'>";
		$html .=                  __("Mode", "megamenupro");
		$html .=              "</td>";
		$html .= "            <td class='mega-value'>";
		$html .= "                <select name='settings[replacements][html][mode]' id='mega_replacement_mode'>";
		$html .= "                    <option value='inner' " . selected( $html_mode, 'inner', false ) . ">" . __("Replace the menu item link text", "megamenupro") . "</option>";
		$html .= "                    <option value='outer' " . selected( $html_mode, 'outer', false ) . ">" . __("Replace the whole menu item link", "megamenupro") . "</option>";
		$html .= "                    <option value='href' " . selected( $html_mode, 'href', false ) . ">" . __("Replace the menu item URL", "megamenupro") . "</option>";
		$html .= "                </select>";
		$html .= "                <div class='mega-description'>";
		$html .= "                    <div class='inner' style='display:{$inner_display}'>&lt;li class='mega-menu-item'&gt;&lt;a class='mega-menu-link' href='url'&gt;<span style='color: red; font-weight: bold;'>Link Text</span>&lt;/a&gt;&lt;/li&gt;</div>";
		$html .= "                    <div class='outer' style='display:{$outer_display}'>&lt;li class='mega-menu-item'&gt;<span style='color: red; font-weight: bold;'>&lt;a class='mega-menu-link' href='url'&gt;Link Text&lt;/a&gt;</span>&lt;/li&gt;</div>";
	   $html .= "                    <div class='href' style='display:{$href_display}'>&lt;li class='mega-menu-item'&gt;&lt;a class='mega-menu-link' href='<span style='color: red; font-weight: bold;'>url</span>'&gt;Link Text&lt;/a&gt;&lt;/li&gt;</div>";
		$html .= "                </div>";
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='html' style='display: {$html_display}'>";
		$html .= "            <td class='mega-name'>";
		$html .=                  __("HTML", "megamenupro");
		$html .= "                <div class='mega-description'>" . __("Enter the text to replace this menu item with. HTML and Shortcodes accepted.", "megamenupro") . "</div>";
		$html .=              "</td>";
		$html .= "            <td class='mega-value'>";
		$html .= "                <textarea id='codemirror' name='settings[replacements][html][code]'>{$html_code}</textarea>";
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "    </table>";
		$html .= get_submit_button();
		$html .= "</form>";

		$tabs['replacements'] = array(
			'title' => __("Replacements", "megamenupro"),
			'content' => $html
		);

		return $tabs;
	}


	/**
	 * Return the HTML for a color picker
	 *
	 * @since 1.3
	 * @param string $key
	 * @param string $value
	 * @return string
	 */
	public function print_theme_color_option( $key, $value ) {

		if ( $value == 'transparent' ) {
			$value = 'rgba(0,0,0,0)';
		}

		if ( $value == 'rgba(0,0,0,0)' ) {
			$value_text = 'transparent';
		} else {
			$value_text = $value;
		}

		$html  = "<div class='mm-picker-container'>";
		$html .= "    <input type='text' class='mm_colorpicker' name='settings[replacements][search][$key]' value='{$value}' />";
		$html .= "    <div class='chosen-color'>{$value_text}</div>";
		$html .= "</div>";

		return $html;

	}

	/**
	 * Return the image URL, crop the image to the correct dimensions if required
	 *
	 * @param int $attachment_id
	 * @param int $dest_width
	 * @param int $dest_height
	 * @since 1.3
	 * @return string resized image URL
	 */
	public function get_resized_image_url( $attachment_id, $dest_width, $dest_height ) {
		if ( get_post_type( $attachment_id ) != 'attachment' ) {
			return "false";
		}
		
		$meta = wp_get_attachment_metadata( $attachment_id );

		$full_url = wp_get_attachment_url( $attachment_id );

		if ( ! isset( $meta['width'], $meta['height'] ) ) {
			return $full_url; // image is not valid
		}

		// if the full size is the same as the required size, return the full URL
		if ( $meta['width'] == $dest_width && $meta['height'] == $dest_height ) {
			return $full_url;
		}

		$path = get_attached_file( $attachment_id );
		$info = pathinfo( $path );
		$dir = $info['dirname'];
		$ext = $info['extension'];
		$name = wp_basename( $path, ".$ext" );
		$dest_file_name = "{$dir}/{$name}-{$dest_width}x{$dest_height}.{$ext}";

		if ( file_exists( $dest_file_name ) ) {
			// good. no need for resize, just return the URL
			return str_replace( basename( $full_url ), basename( $dest_file_name ), $full_url );
		}

		$image = wp_get_image_editor( $path );

		// editor will return an error if the path is invalid
		if ( is_wp_error( $image ) ) {
			return $full_url;
		}

		$image->resize( $dest_width, $dest_height, true );

		$saved = $image->save( $dest_file_name );

		if ( is_wp_error( $saved ) ) {
			return;
		}

		// Record the new size so that the file is correctly removed when the media file is deleted.
		$backup_sizes = get_post_meta( $attachment_id, '_wp_attachment_backup_sizes', true );

		if ( ! is_array( $backup_sizes ) ) {
			$backup_sizes = array();
		}

		$backup_sizes["resized-{$dest_width}x{$dest_height}"] = $saved;
		update_post_meta( $attachment_id, '_wp_attachment_backup_sizes', $backup_sizes );

		$url = str_replace( basename( $full_url ), basename( $saved['path'] ), $full_url );

		return $url;
	}

}

endif;