<?php
/**
 * Functions file
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

/* === TESTER FUNCTIONS === */

if ( ! function_exists( 'yith_wcwl_is_wishlist' ) ) {
	/**
	 * Check if we're printing wishlist shortcode
	 *
	 * @return bool
	 * @since 2.0.0
	 */
	function yith_wcwl_is_wishlist() {
		global $yith_wcwl_is_wishlist;

		return $yith_wcwl_is_wishlist;
	}
}

if ( ! function_exists( 'yith_wcwl_is_wishlist_page' ) ) {
	/**
	 * Check if current page is wishlist
	 *
	 * @return bool
	 * @since 2.0.13
	 */
	function yith_wcwl_is_wishlist_page() {
		$wishlist_page_id = YITH_WCWL()->get_wishlist_page_id();

		if ( ! $wishlist_page_id ) {
			return false;
		}

		return apply_filters( 'yith_wcwl_is_wishlist_page', is_page( $wishlist_page_id ) );
	}
}

if ( ! function_exists( 'yith_wcwl_is_single' ) ) {
	/**
	 * Returns true if it finds that you're printing a single product
	 * Should return false in any loop (including the ones inside single product page)
	 *
	 * @return bool Whether you're currently on single product template
	 * @since 3.0.0
	 */
	function yith_wcwl_is_single() {
		return apply_filters( 'yith_wcwl_is_single', is_product() && ! in_array( wc_get_loop_prop( 'name' ), array( 'related', 'up-sells' ) ) && ! wc_get_loop_prop( 'is_shortcode' ) );
	}
}

if ( ! function_exists( 'yith_wcwl_is_mobile' ) ) {
	/**
	 * Returns true if we're currently on mobile view
	 *
	 * @return bool Whether you're currently on mobile view
	 * @since 3.0.0
	 */
	function yith_wcwl_is_mobile() {
		global $yith_wcwl_is_mobile;

		return apply_filters( 'yith_wcwl_is_wishlist_responsive', true ) && ( wp_is_mobile() || $yith_wcwl_is_mobile );
	}
}

/* === TEMPLATE FUNCTIONS === */

if ( ! function_exists( 'yith_wcwl_locate_template' ) ) {
	/**
	 * Locate the templates and return the path of the file found
	 *
	 * @param string $path Path to locate.
	 * @param array  $var  Unused.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	function yith_wcwl_locate_template( $path, $var = null ) {
		$woocommerce_base = WC()->template_path();

		$template_woocommerce_path = $woocommerce_base . $path;
		$template_path             = '/' . $path;
		$plugin_path               = YITH_WCWL_DIR . 'templates/' . $path;

		$located = locate_template(
			array(
				$template_woocommerce_path, // Search in <theme>/woocommerce/.
				$template_path,             // Search in <theme>/.
			)
		);

		if ( ! $located && file_exists( $plugin_path ) ) {
			return apply_filters( 'yith_wcwl_locate_template', $plugin_path, $path );
		}

		return apply_filters( 'yith_wcwl_locate_template', $located, $path );
	}
}

if ( ! function_exists( 'yith_wcwl_get_template' ) ) {
	/**
	 * Retrieve a template file.
	 *
	 * @param string $path   Path to get.
	 * @param mixed  $var    Variables to send to template.
	 * @param bool   $return Whether to return or print the template.
	 *
	 * @return string|void
	 * @since 1.0.0
	 */
	function yith_wcwl_get_template( $path, $var = null, $return = false ) {
		$located = yith_wcwl_locate_template( $path, $var );

		if ( $var && is_array( $var ) ) {
			$atts = $var;
			extract( $var );
		}

		if ( $return ) {
			ob_start();
		}

		// include file located.
		include( $located );

		if ( $return ) {
			return ob_get_clean();
		}
	}
}

if ( ! function_exists( 'yith_wcwl_get_template_part' ) ) {
	/**
	 * Search and include a template part
	 *
	 * @param string $template        Template to include.
	 * @param string $template_part   Template part.
	 * @param string $template_layout Template variation.
	 * @param array  $var             Array of variables to be passed to template.
	 * @param bool   $return          Whether to return template or print it.
	 *
	 * @return string|null
	 */
	function yith_wcwl_get_template_part( $template = '', $template_part = '', $template_layout = '', $var = array(), $return = false ) {
		if ( ! empty( $template_part ) ) {
			$template_part = '-' . $template_part;
		}

		if ( ! empty( $template_layout ) ) {
			$template_layout = '-' . $template_layout;
		}

		$template_hierarchy = apply_filters(
			'yith_wcwl_template_part_hierarchy',
			array_merge(
				! yith_wcwl_is_mobile() ? array() : array(
					"wishlist-{$template}{$template_layout}{$template_part}-mobile.php",
					"wishlist-{$template}{$template_part}-mobile.php",
				),
				array(
					"wishlist-{$template}{$template_layout}{$template_part}.php",
					"wishlist-{$template}{$template_part}.php",
				)
			),
			$template,
			$template_part,
			$template_layout,
			$var
		);

		foreach ( $template_hierarchy as $filename ) {
			$located = yith_wcwl_locate_template( $filename );

			if ( $located ) {
				return yith_wcwl_get_template( $filename, $var, $return );
			}
		}
	}
}

/* === COUNT FUNCTIONS === */

if ( ! function_exists( 'yith_wcwl_count_products' ) ) {
	/**
	 * Retrieve the number of products in the wishlist.
	 *
	 * @param string|bool $wishlist_token Optional wishlist token.
	 *
	 * @return int
	 * @since 1.0.0
	 */
	function yith_wcwl_count_products( $wishlist_token = false ) {
		return YITH_WCWL()->count_products( $wishlist_token );
	}
}

if ( ! function_exists( 'yith_wcwl_count_all_products' ) ) {
	/**
	 * Retrieve the number of products in all the wishlists.
	 *
	 * @return int
	 * @since 2.0.13
	 */
	function yith_wcwl_count_all_products() {
		return YITH_WCWL()->count_all_products();
	}
}

if ( ! function_exists( 'yith_wcwl_count_add_to_wishlist' ) ) {
	/**
	 * Count number of times a product was added to users wishlists
	 *
	 * @param int|bool $product_id Product id.
	 *
	 * @return int Number of times the product was added to wishlists
	 * @since 2.0.13
	 */
	function yith_wcwl_count_add_to_wishlist( $product_id = false ) {
		return YITH_WCWL()->count_add_to_wishlist( $product_id );
	}
}

if ( ! function_exists( 'yith_wcwl_get_count_text' ) ) {
	/**
	 * Returns the label that states how many users added a specific product to wishlist
	 *
	 * @param int|bool $product_id Product id or false, when you want to use global product as reference.
	 *
	 * @return string Label with count of items
	 */
	function yith_wcwl_get_count_text( $product_id = false ) {
		$count              = yith_wcwl_count_add_to_wishlist( $product_id );
		$current_user_count = $count ? YITH_WCWL_Wishlist_Factory::get_times_current_user_added_count( $product_id ) : 0;

		// if no user added to wishlist, return empty string.
		if ( ! $count ) {
			return apply_filters( 'yith_wcwl_count_text_empty', '', $product_id );
		} elseif ( ! $current_user_count ) {
			// translators: 1. Number of users.
			$count_text = sprintf( _n( '%d user', '%d users', $count, 'yith-woocommerce-wishlist' ), $count );
			$text       = _n( 'has this item in wishlist', 'have this item in wishlist', $count, 'yith-woocommerce-wishlist' );
		} elseif ( $count == $current_user_count ) {
			$count_text = __( 'You\'re the first', 'yith-woocommerce-wishlist' );
			$text       = __( 'to add this item in wishlist', 'yith-woocommerce-wishlist' );
		} else {
			$other_count = $count - $current_user_count;
			// translators: 1. Count of users when many, or "another" when only one.
			$count_text = sprintf( _n( 'You and %s user', 'You and %d users', $other_count, 'yith-woocommerce-wishlist' ), 1 == $other_count ? __( 'another', 'yith-woocommerce-wishlist' ) : $other_count );
			$text       = __( 'have this item in wishlist', 'yith-woocommerce-wishlist' );
		}

		$label = sprintf( '<div class="count-add-to-wishlist"><span class="count">%s</span> %s</div>', $count_text, $text );

		return apply_filters( 'yith_wcwl_count_text', $label, $product_id, $current_user_count, $count );
	}
}

/* === COOKIE FUNCTIONS === */

if ( ! function_exists( 'yith_wcwl_get_cookie_expiration' ) ) {
	/**
	 * Returns default expiration for wishlist cookie
	 *
	 * @return int Number of seconds the cookie should last.
	 */
	function yith_wcwl_get_cookie_expiration() {
		return intval( apply_filters( 'yith_wcwl_cookie_expiration', 60 * 60 * 24 * 30 ) );
	}
}

if ( ! function_exists( 'yith_setcookie' ) ) {
	/**
	 * Create a cookie.
	 *
	 * @param string $name     Cookie name.
	 * @param mixed  $value    Cookie value.
	 * @param int    $time     Cookie expiration time.
	 * @param bool   $secure   Whether cookie should be available to secured connection only.
	 * @param bool   $httponly Whether cookie should be available to HTTP request only (no js handling).
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	function yith_setcookie( $name, $value = array(), $time = null, $secure = false, $httponly = false ) {
		if ( ! apply_filters( 'yith_wcwl_set_cookie', true ) || empty( $name ) ) {
			return false;
		}

		$time = null != $time ? $time : time() + yith_wcwl_get_cookie_expiration();

		$value      = json_encode( stripslashes_deep( $value ) );
		$expiration = apply_filters( 'yith_wcwl_cookie_expiration_time', $time ); // Default 30 days.

		$_COOKIE[ $name ] = $value;
		wc_setcookie( $name, $value, $expiration, $secure, $httponly );

		return true;
	}
}

if ( ! function_exists( 'yith_getcookie' ) ) {
	/**
	 * Retrieve the value of a cookie.
	 *
	 * @param string $name Cookie name.
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	function yith_getcookie( $name ) {
		if ( isset( $_COOKIE[ $name ] ) ) {
			return json_decode( sanitize_text_field( wp_unslash( $_COOKIE[ $name ] ) ), true );
		}

		return array();
	}
}

if ( ! function_exists( 'yith_destroycookie' ) ) {
	/**
	 * Destroy a cookie.
	 *
	 * @param string $name Cookie name.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	function yith_destroycookie( $name ) {
		yith_setcookie( $name, array(), time() - 3600 );
	}
}

/* === GET FUNCTIONS === */

if ( ! function_exists( 'yith_wcwl_get_hidden_products' ) ) {
	/**
	 * Retrieves a list of hidden products, whatever WC version is running
	 *
	 * WC switched from meta _visibility to product_visibility taxonomy since version 3.0.0,
	 * forcing a split handling (Thank you, WC!)
	 *
	 * @return array List of hidden product ids
	 * @since 2.1.1
	 */
	function yith_wcwl_get_hidden_products() {
		if ( version_compare( WC()->version, '3.0.0', '<' ) ) {
			$hidden_products = get_posts(
				array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'posts_per_page' => - 1,
					'fields'         => 'ids',
					'meta_query'     => array(
						array(
							'key'   => '_visibility',
							'value' => 'visible',
						),
					),
				)
			);
		} else {
			$hidden_products = wc_get_products(
				array(
					'limit'      => - 1,
					'status'     => 'publish',
					'return'     => 'ids',
					'visibility' => 'hidden',
				)
			);
		}

		/**
		 * array_filter was added to prevent errors when previous query returns for some reason just 0 index
		 *
		 * @since 2.2.6
		 */
		return apply_filters( 'yith_wcwl_hidden_products', array_filter( $hidden_products ) );
	}
}

if ( ! function_exists( 'yith_wcwl_get_wishlist' ) ) {
	/**
	 * Retrieves wishlist by ID
	 *
	 * @param int|string $wishlist_id Wishlist ID or Wishlist Token.
	 *
	 * @return \YITH_WCWL_Wishlist|bool Wishlist object; false on error
	 */
	function yith_wcwl_get_wishlist( $wishlist_id ) {
		return YITH_WCWL_Wishlist_Factory::get_wishlist( $wishlist_id );
	}
}

if ( ! function_exists( 'yith_wcwl_get_plugin_icons' ) ) {
	/**
	 * Return array of available icons
	 *
	 * @param string $none_label   Label to use for none option.
	 * @param string $custom_label Label to use for custom option.
	 *
	 * @return array Array of available icons, in class => name format
	 */
	function yith_wcwl_get_plugin_icons( $none_label = '', $custom_label = '' ) {
		$icons = json_decode( file_get_contents( YITH_WCWL_DIR . 'assets/js/admin/yith-wcwl-icons.json' ), true );

		$icons['none']   = $none_label ? $none_label : __( 'None', 'yith-woocommerce-wishlist' );
		$icons['custom'] = $custom_label ? $custom_label : __( 'Custom', 'yith-woocommerce-wishlist' );

		return $icons;
	}
}

if ( ! function_exists( 'yith_wcwl_get_privacy_label' ) ) {
	/**
	 * Returns privacy label
	 *
	 * @param int  $privacy  Privacy value.
	 * @param bool $extended Whether to show extended or simplified label.
	 *
	 * @return string Privacy label
	 * @since 3.0.0
	 */
	function yith_wcwl_get_privacy_label( $privacy, $extended = false ) {

		switch ( $privacy ) {
			case 1:
				$privacy_label = 'shared';
				$privacy_text  = __( 'Shared', 'yith-woocommerce-wishlist' );

				if ( $extended ) {
					$privacy_text = '<b>' . $privacy_text . '</b> - ';
					$privacy_text .= __( 'Only people with a link to this list can see it', 'yith-woocommerce-wishlist' );
				}

				break;
			case 2:
				$privacy_label = 'private';
				$privacy_text  = __( 'Private', 'yith-woocommerce-wishlist' );

				if ( $extended ) {
					$privacy_text = '<b>' . $privacy_text . '</b> - ';
					$privacy_text .= __( 'Only you can see this list', 'yith-woocommerce-wishlist' );
				}

				break;
			default:
				$privacy_label = 'public';
				$privacy_text  = __( 'Public', 'yith-woocommerce-wishlist' );

				if ( $extended ) {
					$privacy_text = '<b>' . $privacy_text . '</b> - ';
					$privacy_text .= __( 'Anyone can search for and see this list', 'yith-woocommerce-wishlist' );
				}

				break;
		}

		return apply_filters( "yith_wcwl_{$privacy_label}_wishlist_visibility", $privacy_text, $extended );
	}
}

if ( ! function_exists( 'yith_wcwl_get_privacy_value' ) ) {
	/**
	 * Returns privacy numeric value
	 *
	 * @param string $privacy_label Privacy label.
	 *
	 * @return int Privacy value
	 * @since 3.0.0
	 */
	function yith_wcwl_get_privacy_value( $privacy_label ) {

		switch ( $privacy_label ) {
			case 'shared':
				$privacy_value = 1;
				break;
			case 'private':
				$privacy_value = 2;
				break;
			default:
				$privacy_value = 0;
				break;
		}

		return $privacy_value;
	}
}

if ( ! function_exists( 'yith_wcwl_get_current_url' ) ) {
	/**
	 * Retrieves current url
	 *
	 * @return string Current url
	 * @since 3.0.0
	 */
	function yith_wcwl_get_current_url() {
		global $wp;

		/**
		 * Returns empty string by default, to avoid problems with unexpected redirects
		 * Added filter to change default behaviour, passing what we think is current page url
		 *
		 * @since 3.0.12
		 */
		return apply_filters( 'yith_wcwl_current_url', '', add_query_arg( $wp->query_vars, home_url( $wp->request ) ) );
	}
}

/* === UTILITY FUNCTIONS === */

if ( ! function_exists( 'yith_wcwl_merge_in_array' ) ) {
	/**
	 * Merges an array of items into a specific position of an array
	 *
	 * @param array  $array    Origin array.
	 * @param array  $element  Elements to merge.
	 * @param string $pivot    Index to use as pivot.
	 * @param string $position Where elements should be merged (before or after the pivot).
	 *
	 * @return array Result of the merge
	 */
	function yith_wcwl_merge_in_array( $array, $element, $pivot, $position = 'after' ) {
		// search for the pivot inside array.
		$pos = array_search( $pivot, array_keys( $array ) );

		if ( false === $pos ) {
			return $array;
		}

		// separate array into chunks.
		$i      = 'after' == $position ? 1 : 0;
		$part_1 = array_slice( $array, 0, $pos + $i );
		$part_2 = array_slice( $array, $pos + $i );

		return array_merge( $part_1, $element, $part_2 );
	}
}

if ( ! function_exists( 'yith_wcwl_maybe_format_field_array' ) ) {
	/**
	 * Take a field structure from plugin saved data, and format it as required by WC to print fields
	 *
	 * @param array $field_structure Array of fields as saved on db.
	 *
	 * @return array Array of fields as required by WC
	 */
	function yith_wcwl_maybe_format_field_array( $field_structure ) {
		$fields = array();

		if ( empty( $field_structure ) ) {
			return array();
		}

		foreach ( $field_structure as $field ) {
			if ( isset( $field['active'] ) && 'yes' != $field['active'] ) {
				continue;
			}

			if ( empty( $field['label'] ) ) {
				continue;
			}

			// format type.
			$field_id = sanitize_title_with_dashes( $field['label'] );

			// format options, if needed.
			if ( ! empty( $field['options'] ) ) {
				$options     = array();
				$raw_options = explode( '|', $field['options'] );

				if ( ! empty( $raw_options ) ) {
					foreach ( $raw_options as $raw_option ) {
						if ( strpos( $raw_option, '::' ) === false ) {
							continue;
						}

						list( $id, $value ) = explode( '::', $raw_option );
						$options[ $id ] = $value;
					}
				}

				$field['options'] = $options;
			}

			// format class.
			$field['class'] = array( 'form-row-' . $field['position'] );

			// format requires.
			$field['required'] = isset( $field['required'] ) && 'yes' == $field['required'];

			// set custom attributes when field is required.
			if ( $field['required'] ) {
				$field['custom_attributes'] = array(
					'required' => 'required',
				);
			}

			// if type requires options, but no options was defined, skip field printing.
			if ( in_array( $field['type'], array( 'select', 'radio' ) ) && empty( $field['options'] ) ) {
				continue;
			}

			$fields[ $field_id ] = $field;
		}

		return $fields;
	}
}

if ( ! function_exists( 'yith_wcwl_add_notice' ) ) {
	/**
	 * Calls wc_add_notice, when it exists
	 *
	 * @param string $message     Message to print.
	 * @param string $notice_type Notice type (succcess|error|notice).
	 * @param array  $data        Optional notice data.
	 *
	 * @since 3.0.10
	 */
	function yith_wcwl_add_notice( $message, $notice_type = 'success', $data = array() ) {
		function_exists( 'wc_add_notice' ) && wc_add_notice( $message, $notice_type, $data );
	}
}

if ( ! function_exists( 'yith_wcwl_object_id' ) ) {
	/**
	 * Retrieve translated object id, if a translation plugin is active
	 *
	 * @param int    $id              Original object id.
	 * @param string $type            Object type.
	 * @param bool   $return_original Whether to return original object if no translation is found.
	 * @param string $lang            Language to use for translation ().
	 *
	 * @return int Translation id
	 * @since 1.0.0
	 */
	function yith_wcwl_object_id( $id, $type = 'page', $return_original = true, $lang = null ) {

		// process special value for $lang.
		if ( 'default' === $lang ) {
			if ( defined('ICL_SITEPRESS_VERSION') ) { // wpml default language.
				global $sitepress;
				$lang = $sitepress->get_default_language();
			} elseif( function_exists( 'pll_default_language' ) ) { // polylang default language.
				$lang = pll_default_language( 'locale' );
			} else { // cannot determine default language.
				$lang = null;
			}
		}

		// Should work with WPML and PolyLang.
		$id = apply_filters( 'wpml_object_id', $id, $type, $return_original, $lang );

		// Space for additional translations
		$id = apply_filters( 'yith_wcwl_object_id', $id, $type, $return_original, $lang );

		return $id;
	}
}

/* === DEPRECATED FUNCTIONS === */

if ( ! function_exists( 'yith_frontend_css_color_picker' ) ) {
	/**
	 * Output a colour picker input box.
	 *
	 * This function is not of the plugin YITH WCWL. It is from WooCommerce.
	 * We redeclare it only because it is needed in the tab "Styles" where it is not available.
	 * The original function name is woocommerce_frontend_css_colorpicker and it is declared in
	 * wp-content/plugins/woocommerce/admin/settings/settings-frontend-styles.php
	 *
	 * @access public
	 *
	 * @param mixed  $name  Name for the input field.
	 * @param mixed  $id    Id for the input field.
	 * @param mixed  $value Value for the input field.
	 * @param string $desc  Description to show under input field (default '').
	 *
	 * @return void
	 * @deprecated
	 */
	function yith_frontend_css_color_picker( $name, $id, $value, $desc = '' ) {
		_deprecated_function( 'yith_frontend_css_color_picker', '3.0.0' );

		$value = ! empty( $value ) ? $value : '#ffffff';

		echo '<div  class="color_box">
				  <table><tr><td>
				  <strong>' . esc_html( $name ) . '</strong>
				  <input name="' . esc_attr( $id ) . '" id="' . esc_attr( $id ) . '" type="text" value="' . esc_attr( $value ) . '" class="colorpick colorpickpreview" style="background-color: ' . esc_attr( $value ) . '" /> <div id="colorPickerDiv_' . esc_attr( $id ) . '" class="colorpickdiv"></div>
				  </td></tr></table>
			  </div>';

	}
}
