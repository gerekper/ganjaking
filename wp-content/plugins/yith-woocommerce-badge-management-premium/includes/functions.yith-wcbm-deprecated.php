<?php
/**
 * Deprecated functions
 * Where functions come to die.
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagement\Functions
 */

defined( 'YITH_WCBM' ) || exit;

if ( ! function_exists( 'yith_wcbm_deprecated_hook' ) ) {
	/**
	 * Wrapper for deprecated hook, so we can apply some extra logic.
	 *
	 * @param string $hook        The hook that was used.
	 * @param string $version     The Booking plugin version that deprecated the hook.
	 * @param string $replacement The hook that should have been used.
	 * @param string $message     A message regarding the change.
	 *
	 * @since 2.1
	 */
	function yith_wcbm_deprecated_hook( $hook, $version, $replacement = null, $message = null ) {
		// phpcs:disable
		if ( yith_wcbm_show_deprecated_notices() ) {
			$backtrace   = ' Backtrace: ' . wp_debug_backtrace_summary();
			$errors_mode = yith_wcbm_debug_errors_mode();

			if ( 'trigger_error' === $errors_mode ) {
				_deprecated_hook( $hook, $version, $replacement, $message );
			} else {
				do_action( 'deprecated_hook_run', $hook, $replacement, $version, $message );

				$message    = empty( $message ) ? '' : ' ' . $message;
				$log_string = "{$hook} is deprecated since version {$version}";

				$log_string .= $replacement ? "! Use {$replacement} instead." : ' with no alternative available.';
				$log_string .= $message;
				$log_string .= $backtrace;

				yith_wcbm_debug_errors_trigger( $log_string, $errors_mode );
			}
		}
		// phpcs:enable
	}
}

if ( ! function_exists( 'yith_wcbm_show_deprecated_notices' ) ) {
	/**
	 * Should I show noticed for deprecated functions/hooks?
	 *
	 * @return bool
	 * @since 2.0.0
	 */
	function yith_wcbm_show_deprecated_notices() {
		return ! ! apply_filters( 'yith_wcbm_show_deprecated_notices', true );
	}
}

if ( ! function_exists( 'yith_wcbm_debug_errors_mode' ) ) {
	/**
	 * Should I show noticed for deprecated functions/hooks?
	 *
	 * @return string
	 * @since 2.0.0
	 */
	function yith_wcbm_debug_errors_mode() {
		$available_modes = array( 'trigger_error', 'wc_logger', 'error_log' );
		$mode            = 'trigger_error';
		$conditions      = array(
			'is_ajax'             => defined( 'DOING_AJAX' ) && DOING_AJAX,
			'is_rest_api_request' => ( is_callable( array( WC(), 'is_rest_api_request' ) ) && WC()->is_rest_api_request() ),
		);

		if ( $conditions['is_ajax'] || $conditions['is_rest_api_request'] ) {
			$mode = 'error_log';
		}

		$filtered_mode = apply_filters( 'yith_wcbm_debug_errors_mode', $mode, $conditions );
		if ( in_array( $filtered_mode, $available_modes, true ) ) {
			$mode = $filtered_mode;
		}

		return $mode;
	}
}

if ( ! function_exists( 'yith_wcbm_deprecated_function' ) ) {
	/**
	 * Wrapper for deprecated functions, so we can apply some extra logic.
	 *
	 * @param string $function    Function used.
	 * @param string $version     Version the message was added in.
	 * @param string $replacement Replacement for the called function.
	 *
	 * @since 2.0.0
	 */
	function yith_wcbm_deprecated_function( $function, $version, $replacement = null ) {
		// phpcs:disable
		if ( yith_wcbm_show_deprecated_notices() ) {
			$backtrace   = ' Backtrace: ' . wp_debug_backtrace_summary();
			$errors_mode = yith_wcbm_debug_errors_mode();

			if ( 'trigger_error' === $errors_mode ) {
				_deprecated_function( $function, $version, $replacement );
			} else {
				do_action( 'deprecated_function_run', $function, $replacement, $version );
				$log_string = "The {$function} function is deprecated since version {$version}.";

				$log_string .= $replacement ? " Replace with {$replacement}." : '';
				$log_string .= $backtrace;

				yith_wcbm_debug_errors_trigger( $log_string, $errors_mode );
			}
		}
		// phpcs:enable
	}
}

if ( ! function_exists( 'yith_wcbm_debug_errors_trigger' ) ) {
	/**
	 * Should I show noticed for deprecated functions/hooks?
	 *
	 * @param string $message The message to be shown.
	 * @param string $mode    The debug errors mode.
	 *
	 * @since 2.0.0
	 */
	function yith_wcbm_debug_errors_trigger( $message, $mode = false ) {
		$mode = ! ! $mode ? $mode : yith_wcbm_debug_errors_mode();

		switch ( $mode ) {
			case 'wc_logger':
				wc_get_logger()->error( $message, array( 'source' => 'yith-wcbm-debug-errors' ) );
				break;
			default:
				error_log( $message ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				break;
		}
	}
}

if ( ! function_exists( 'yith_wcbm_handle_deprecated_filters' ) ) {
	/**
	 * Handle deprecated filters
	 *
	 * @param array $deprecated_filters The deprecated filters.
	 */
	function yith_wcbm_handle_deprecated_filters( $deprecated_filters ) {
		foreach ( $deprecated_filters as $deprecated_filter => $options ) {
			$deprecated_filter = $options['deprecated'];
			$new_filter        = $options['use'];
			$params            = $options['params'];
			$since             = $options['since'];
			add_filter(
				$new_filter,
				function() use ( $deprecated_filter, $since, $new_filter ) {
					$args = func_get_args();
					$r    = $args[0];

					if ( has_filter( $deprecated_filter ) ) {
						yith_wcbm_deprecated_hook( $deprecated_filter, $since, $new_filter );

						$r = call_user_func_array( 'apply_filters', array_merge( array( $deprecated_filter ), $args ) );
					}

					return $r;
				},
				10,
				$params
			);
		}
	}
}

/** ------------------------------------------------------------------------------
 * Deprecated filters
 */

$deprecated_filters = array(
	array(
		'deprecated' => 'yith_wcmb_wpml_autosync_product_badge_translations',
		'since'      => '2.0.0',
		'use'        => 'yith_wcbm_wpml_autosync_product_badge_translations',
		'params'     => 1,
	),
	array(
		'deprecated' => 'yith_wcmb_is_wpml_parent_based_on_default_language',
		'since'      => '2.0.0',
		'use'        => 'yith_wcbm_is_wpml_parent_based_on_default_language',
		'params'     => 1,
	),
	array(
		'deprecated' => 'yith_wcbm_product_thumbnail_allowed_html',
		'since'      => '2.0.0',
		'use'        => '',
		'params'     => 1,
	),
	array(
		'deprecated' => 'yith_wcbm_sanitize_badge_css_text',
		'since'      => '2.0.0',
		'use'        => '',
		'params'     => 2,
	),
	array(
		'deprecated' => 'yith_wcbm_sanitize_badge_text',
		'since'      => '2.0.0',
		'use'        => '',
		'params'     => 2,
	),
	array(
		'deprecated' => 'yith_wcbm_metabox_options_content_args',
		'since'      => '2.0.0',
		'use'        => '',
		'params'     => 1,
	),
);

yith_wcbm_handle_deprecated_filters( $deprecated_filters );

/** ------------------------------------------------------------------------------
 * Deprecated functions
 */

if ( ! function_exists( 'yith_wcbm_get_badge' ) ) {
	/**
	 * Get the badge
	 *
	 * @param int $badge_id   The badge ID.
	 * @param int $product_id The product ID.
	 *
	 * @return string
	 * @since      1.0
	 * @depreacted since 2.0.0 | Use the Badge Object instead
	 */
	function yith_wcbm_get_badge( $badge_id, $product_id ) {
		if ( ! $badge_id || ! $product_id ) {
			return '';
		}

		$bm_meta = yith_wcbm_get_badge_meta( $badge_id );
		$default = array(
			'type'              => 'text',
			'text'              => '',
			'txt_color_default' => '#000000',
			'txt_color'         => '#000000',
			'bg_color_default'  => '#2470FF',
			'bg_color'          => '#2470FF',
			'width'             => '100',
			'height'            => '50',
			'position'          => 'top-left',
			'image_url'         => '',
			'product_id'        => $product_id,
			'id_badge'          => $badge_id,
		);

		$args          = wp_parse_args( $bm_meta, $default );
		$args          = apply_filters( 'yith_wcbm_badge_content_args', $args );
		$args['badge'] = yith_wcbm_get_badge_object( $badge_id );
		ob_start();
		yith_wcbm_get_view( 'badge-content.php', $args );
		$badge_html = ob_get_clean();

		return apply_filters( 'yith_wcbm_get_badge', $badge_html, $badge_id, $product_id );
	}
}

if ( ! function_exists( 'yith_wcbm_metabox_options_content_premium' ) ) {
	/**
	 * Print the content of metabox options [PREMIUM]
	 *
	 * @param array $args Metabox Values.
	 *
	 * @return   void
	 * @depracated Since 2.0
	 */
	function yith_wcbm_metabox_options_content_premium( $args ) {
		yith_wcbm_deprecated_function( 'yith_wcbm_metabox_options_content_premium', '2.0.0' );
	}
}

if ( ! function_exists( 'yith_wcbm_insert_image_uploader' ) ) {
	/**
	 * Insert Uploader button
	 *
	 * @return   string
	 *
	 * @depreacted Since 2.0
	 */
	function yith_wcbm_insert_image_uploader() {
		yith_wcbm_deprecated_function( 'yith_wcbm_insert_image_uploader', '2.0.0' );

		return '';
	}
}

if ( ! function_exists( 'yith_wcbm_get_transform_origin_by_positions' ) ) {
	/**
	 * Get Transform origin by position
	 *
	 * @param string $top    The top position.
	 * @param string $right  The right position.
	 * @param string $bottom The bottom position.
	 * @param string $left   The left position.
	 *
	 * @return string
	 * @depreacted since 2.0
	 */
	function yith_wcbm_get_transform_origin_by_positions( $top, $right, $bottom, $left ) {
		yith_wcbm_deprecated_function( 'yith_wcbm_get_transform_origin_by_positions', '2.0.0' );
		$x = 'auto' === $left ? 'right' : 'left';
		$y = 'auto' === $top ? 'bottom' : 'top';

		if ( strpos( $left, 'calc' ) === 0 || strpos( $right, 'calc' ) === 0 ) {
			$x = 'center';
		}

		return $x . ' ' . $y;
	}
}

if ( ! function_exists( 'yith_wcmb_is_frontend_manager' ) ) {
	/**
	 * Is this a page of YITH Frontend Manager?
	 *
	 * @return bool
	 * @depreacted Use yith_wcbm_is_frontend_manager instead
	 */
	function yith_wcmb_is_frontend_manager() {
		yith_wcbm_deprecated_function( 'yith_wcmb_is_frontend_manager', '2.0.0', 'yith_wcbm_is_frontend_manager' );

		return yith_wcbm_is_frontend_manager();
	}
}

if ( ! function_exists( 'yith_wcmb_is_wpml_parent_based_on_default_language' ) ) {
	/**
	 * Is WPML parent based on default language?
	 *
	 * @return bool
	 * @depreacted since 2.0.0
	 * @use        yith_wcbm_is_wpml_parent_based_on_default_language
	 */
	function yith_wcmb_is_wpml_parent_based_on_default_language() {
		yith_wcbm_deprecated_function( 'yith_wcmb_is_wpml_parent_based_on_default_language', '2.0.0', 'yith_wcbm_is_wpml_parent_based_on_default_language' );

		return yith_wcbm_is_wpml_parent_based_on_default_language();
	}
}

if ( ! function_exists( 'yith_wcmb_wpml_autosync_product_badge_translations' ) ) {
	/**
	 * Does WPML autosync product badge translations?
	 *
	 * @return bool
	 * @depreacted since 2.0.0
	 * @use        yith_wcmb_wpml_autosync_product_badge_translations instead
	 */
	function yith_wcmb_wpml_autosync_product_badge_translations() {
		yith_wcbm_deprecated_function( 'yith_wcmb_wpml_autosync_product_badge_translations', '2.0.0', 'yith_wcbm_wpml_autosync_product_badge_translations' );

		return yith_wcbm_wpml_autosync_product_badge_translations();
	}
}

if ( ! function_exists( 'yith_wcbm_admin_premium' ) ) {
	/**
	 * Unique access to instance of YITH_WCBM_Admin_Premium class
	 *
	 * @return YITH_WCBM_Admin_Premium
	 * @since      1.0.0
	 * @deprecated since 1.3.0
	 * @use        yith_wcbm_admin() instead
	 */
	function yith_wcbm_admin_premium() {
		return yith_wcbm_admin();
	}
}
