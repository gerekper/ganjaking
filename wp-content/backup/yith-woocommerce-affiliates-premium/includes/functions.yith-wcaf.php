<?php
/**
 * Utility functions
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'yith_wcaf_locate_template' ) ) {
	/**
	 * Locate template for Affiliate plugin
	 *
	 * @param $filename string Template name (with or without extension)
	 * @param $section  string Subdirectory where to search
	 *
	 * @return string Found template
	 */
	function yith_wcaf_locate_template( $filename, $section = '' ) {
		$ext = preg_match( '/^.*\.[^\.]+$/', $filename ) ? '' : '.php';

		$template_name = $section . '/' . $filename . $ext;
		$template_path = WC()->template_path() . 'yith-wcaf/';
		$default_path  = YITH_WCAF_DIR . 'templates/';

		if ( defined( 'YITH_WCAF_PREMIUM' ) ) {
			$premium_template = str_replace( '.php', '-premium.php', $template_name );
			$located_premium  = wc_locate_template( $premium_template, $template_path, $default_path );
			$template_name    = file_exists( $located_premium ) ? $premium_template : $template_name;
		}

		return wc_locate_template( $template_name, $template_path, $default_path );
	}
}

if ( ! function_exists( 'yith_wcaf_get_template' ) ) {
	/**
	 * Get template for Affiliate plugin
	 *
	 * @param $filename string Template name (with or without extension)
	 * @param $args     mixed Array of params to use in the template
	 * @param $section  string Subdirectory where to search
	 */
	function yith_wcaf_get_template( $filename, $args = array(), $section = '' ) {
		$ext = preg_match( '/^.*\.[^\.]+$/', $filename ) ? '' : '.php';

		$template_name = $section . '/' . $filename . $ext;
		$template_path = WC()->template_path() . 'yith-wcaf/';
		$default_path  = YITH_WCAF_DIR . 'templates/';

		if ( defined( 'YITH_WCAF_PREMIUM' ) ) {
			$premium_template = str_replace( '.php', '-premium.php', $template_name );
			$located_premium  = wc_locate_template( $premium_template, $template_path, $default_path );
			$template_name    = file_exists( $located_premium ) ? $premium_template : $template_name;
		}

		wc_get_template( $template_name, $args, $template_path, $default_path );
	}
}

if ( ! function_exists( 'yith_wcaf_array_column' ) ) {
	/**
	 * Implement array column for PHP older then 5.5
	 *
	 * @param $input     array Input multidimensional array
	 * @param $columnKey string Array column
	 * @param $indexKey  string Array to be used as keys for result
	 *
	 * @return Array Column extracted
	 * @since 1.0.1
	 */
	function yith_wcaf_array_column( $input = null, $columnKey = null, $indexKey = null ) {
		if ( function_exists( 'array_column' ) ) {
			return array_column( $input, $columnKey, $indexKey );
		} else {
			// Using func_get_args() in order to check for proper number of
			// parameters and trigger errors exactly as the built-in array_column()
			// does in PHP 5.5.
			$argc   = func_num_args();
			$params = func_get_args();
			if ( $argc < 2 ) {
				trigger_error( "array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING );

				return null;
			}
			if ( ! is_array( $params[0] ) ) {
				trigger_error(
					'array_column() expects parameter 1 to be array, ' . gettype( $params[0] ) . ' given',
					E_USER_WARNING
				);

				return null;
			}
			if ( ! is_int( $params[1] )
				 && ! is_float( $params[1] )
				 && ! is_string( $params[1] )
				 && $params[1] !== null
				 && ! ( is_object( $params[1] ) && method_exists( $params[1], '__toString' ) )
			) {
				trigger_error( 'array_column(): The column key should be either a string or an integer', E_USER_WARNING );

				return false;
			}
			if ( isset( $params[2] )
				 && ! is_int( $params[2] )
				 && ! is_float( $params[2] )
				 && ! is_string( $params[2] )
				 && ! ( is_object( $params[2] ) && method_exists( $params[2], '__toString' ) )
			) {
				trigger_error( 'array_column(): The index key should be either a string or an integer', E_USER_WARNING );

				return false;
			}
			$paramsInput     = $params[0];
			$paramsColumnKey = ( $params[1] !== null ) ? (string) $params[1] : null;
			$paramsIndexKey  = null;
			if ( isset( $params[2] ) ) {
				if ( is_float( $params[2] ) || is_int( $params[2] ) ) {
					$paramsIndexKey = (int) $params[2];
				} else {
					$paramsIndexKey = (string) $params[2];
				}
			}
			$resultArray = array();
			foreach ( $paramsInput as $row ) {
				$key    = $value = null;
				$keySet = $valueSet = false;
				if ( $paramsIndexKey !== null && array_key_exists( $paramsIndexKey, $row ) ) {
					$keySet = true;
					$key    = (string) $row[ $paramsIndexKey ];
				}
				if ( $paramsColumnKey === null ) {
					$valueSet = true;
					$value    = $row;
				} elseif ( is_array( $row ) && array_key_exists( $paramsColumnKey, $row ) ) {
					$valueSet = true;
					$value    = $row[ $paramsColumnKey ];
				}
				if ( $valueSet ) {
					if ( $keySet ) {
						$resultArray[ $key ] = $value;
					} else {
						$resultArray[] = $value;
					}
				}
			}

			return $resultArray;
		}
	}
}

if ( ! function_exists( 'yith_wcaf_get_current_affiliate_token' ) ) {
	/**
	 * Returns current affiliate token, if any; otherwise false
	 *
	 * @return string|bool Affiliate token or false
	 * @since 1.0.9
	 */
	function yith_wcaf_get_current_affiliate_token() {
		if ( ! did_action( 'init' ) ) {
			_doing_it_wrong( 'yith_wcaf_get_current_affiliate_token', __( 'yith_wcaf_get_current_affiliate_token() should be called after init', 'yith-woocommerce-affiliates' ), '1.0.9' );

			return false;
		}

		return YITH_WCAF_Affiliate()->get_token();
	}
}

if ( ! function_exists( 'yith_wcaf_get_current_affiliate' ) ) {
	/**
	 * Returns current affiliate token, if any; otherwise false
	 *
	 * @return string|bool Affiliate token or false
	 * @since 1.0.9
	 */
	function yith_wcaf_get_current_affiliate() {
		if ( ! did_action( 'init' ) ) {
			_doing_it_wrong( 'yith_wcaf_get_current_affiliate', __( 'yith_wcaf_get_current_affiliate() should be called after init', 'yith-woocommerce-affiliates' ), '1.0.9' );

			return false;
		}

		return YITH_WCAF_Affiliate()->get_affiliate();
	}
}

if ( ! function_exists( 'yith_wcaf_get_current_affiliate_user' ) ) {
	/**
	 * Returns current affiliate token, if any; otherwise false
	 *
	 * @return string|bool Affiliate token or false
	 * @since 1.0.9
	 */
	function yith_wcaf_get_current_affiliate_user() {
		if ( ! did_action( 'init' ) ) {
			_doing_it_wrong( 'yith_wcaf_get_current_affiliate_user', __( 'yith_wcaf_get_current_affiliate_user() should be called after init', 'yith-woocommerce-affiliates' ), '1.0.9' );

			return false;
		}

		return YITH_WCAF_Affiliate()->get_user();
	}
}

if ( ! function_exists( 'yith_wcaf_get_dashboard_links' ) ) {
	/**
	 * Returns an array with links for all the sections of affiliate dashboard
	 *
	 * @return array
	 * @since 1.1.1
	 */
	function yith_wcaf_get_dashboard_links() {
		$endpoints       = YITH_WCAF()->get_dashboard_endpoints();
		$dashboard_links = array();

		if ( ! empty( $endpoints ) ) {
			foreach ( $endpoints as $endpoint => $label ) {
				$dashboard_links[ $endpoint ] = YITH_WCAF()->get_affiliate_dashboard_url( $endpoint );
			}
		}

		return apply_filters( 'yith_wcaf_dashboard_links', $dashboard_links );
	}
}

if ( ! function_exists( 'yith_wcaf_get_dashboard_navigation_menu' ) ) {
	/**
	 * Return elements to be added to navigation menu
	 *
	 * @return array Array of navigation menu items
	 */
	function yith_wcaf_get_dashboard_navigation_menu() {
		$endpoints                 = YITH_WCAF()->get_dashboard_endpoints();
		$current_endpoint          = YITH_WCAF()->get_dashboard_endpoint();
		$dashboard_navigation_menu = array();

		if ( apply_filters( 'yith_wcaf_show_dashboard_link_for_navigation_menu', true ) ) {
			$dashboard_navigation_menu['dashboard'] = array(
				'label'  => __( 'Dashboard', 'yith-woocommerce-affiliates' ),
				'url'    => YITH_WCAF()->get_affiliate_dashboard_url(),
				'active' => empty( $current_endpoint ),
			);
		}

		if ( ! empty( $endpoints ) ) {
			foreach ( $endpoints as $endpoint => $label ) {
				if ( ! YITH_WCAF_Affiliate_Handler()->can_user_see_section( false, $endpoint ) ) {
					continue;
				}

				$dashboard_navigation_menu[ $endpoint ] = array(
					'label'  => $label,
					'url'    => YITH_WCAF()->get_affiliate_dashboard_url( $endpoint ),
					'active' => $endpoint === $current_endpoint,
				);
			}
		}

		return apply_filters( 'yith_wcaf_dashboard_navigation_menu', $dashboard_navigation_menu );
	}
}

if ( ! function_exists( 'yith_wcaf_delete_order_data' ) ) {
	/**
	 * Delete all plugin data from the order
	 *
	 * @param $order_id    int Order id
	 * @param $delete_mask int Bool mask
	 *
	 * @return void
	 * @since 1.1.1
	 */
	function yith_wcaf_delete_order_data( $order_id, $delete_mask = 0b10001111 ) {
		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return;
		}

		// removes token
		if ( $delete_mask & 128 ) {
			delete_post_meta( $order_id, '_yith_wcaf_referral' );
		}

		// removes token history
		if ( $delete_mask & 64 ) {
			delete_post_meta( $order_id, '_yith_wcaf_referral_history' );
		}

		// removes click ID
		if ( $delete_mask & 32 ) {
			delete_post_meta( $order_id, '_yith_wcaf_click_id' );
		}

		// removes conversion registered meta
		if ( $delete_mask & 16 ) {
			delete_post_meta( $order_id, '_yith_wcaf_conversion_registered' );
		}

		// removes refunded commissions meta
		if ( $delete_mask & 8 ) {
			delete_post_meta( $order_id, '_refunded_commissions' );
		}

		// removes order item meta
		$items = $order->get_items();

		if ( ! $items ) {
			return;
		}

		foreach ( $items as $item_id => $item ) {

			// removes commission id
			if ( $delete_mask & 4 ) {
				wc_delete_order_item_meta( $item_id, '_yith_wcaf_commission_id' );
			}

			// removes commission rate
			if ( $delete_mask & 2 ) {
				wc_delete_order_item_meta( $item_id, '_yith_wcaf_commission_rate' );
			}

			// removes commission amount
			if ( $delete_mask & 1 ) {
				wc_delete_order_item_meta( $item_id, '_yith_wcaf_commission_amount' );
			}
		}
	}
}

if ( ! function_exists( 'yith_wcaf_is_affiliate_dashboard_page' ) ) {
	/**
	 * Returns true if current page is Affiliate Dashboard page
	 *
	 * @return bool Whether current page is Affiliate Dashboard page
	 * @since 1.2.2
	 */
	function yith_wcaf_is_affiliate_dashboard_page() {
		if ( ! did_action( 'wp' ) ) {
			_doing_it_wrong( __FUNCTION__, _x( 'Should be called after wp hook, in order to know current page', 'Dev warning; do not translate', 'yith-woocommerce-affiliates' ), '2.1.2' );
		}

		$dashboard_page_id = get_option( 'yith_wcaf_dashboard_page_id' );

		return is_page( $dashboard_page_id );
	}
}

if ( ! function_exists( 'yith_wcaf_is_affiliate_dashboard_shortcode' ) ) {
	/**
	 * Returns true while printing affiliate dashboard shortcode
	 *
	 * @return bool Whether we're currently printing affiliates dashboard shortcode
	 * @since 1.2.2
	 */
	function yith_wcaf_is_affiliate_dashboard_shortcode() {
		return YITH_WCAF_Shortcode::$is_affiliate_dashboard;
	}
}

if ( ! function_exists( 'yith_wcaf_get_promote_methods' ) ) {
	/**
	 * Return promotional methods options, available for the site
	 * Set can be filtered to add or remove items
	 *
	 * @return array Promotional methods available
	 * @since 1.2.5
	 */
	function yith_wcaf_get_promote_methods() {
		return apply_filters( 'yith_wcaf_how_promote_methods', array(
			'website'    => __( 'Website / Blog', 'yith-woocommere-affiliates' ),
			'newsletter' => __( 'Newsletter / Mail Marketing', 'yith-woocommerce-affiliates' ),
			'socials'    => __( 'Social media', 'yith-woocommerce-affiliates' ),
			'others'     => __( 'Others', 'yith-woocommerce-affiliates' )
		) );
	}
}

if ( ! function_exists( 'yith_wcaf_append_items' ) ) {
	/**
	 * Adds items inside set array, placing them after the item with the index specified
	 *
	 * @param $set          array Array where we need to add items
	 * @param $before_index string Index we need to search inside $set
	 * @param $items        mixed Items that we need to add to $set
	 *
	 * @return array Array with new items
	 * @since 1.2.5
	 */
	function yith_wcaf_append_items( $set, $before_index, $items ) {
		$before_index_position = array_search( $before_index, array_keys( $set ) );

		if ( $before_index_position < 0 ) {
			return $set;
		}

		$settings_options_chunk_1 = array_slice( $set, 0, $before_index_position + 1 );
		$settings_options_chunk_2 = array_slice( $set, $before_index_position + 1, count( $set ) );

		return array_merge(
			$settings_options_chunk_1,
			$items,
			$settings_options_chunk_2
		);
	}
}