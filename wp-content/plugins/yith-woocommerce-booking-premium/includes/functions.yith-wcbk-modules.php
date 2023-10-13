<?php
/**
 * Modules Functions
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Functions
 * @since   4.0.0
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! function_exists( 'yith_wcbk_get_module_path' ) ) {
	/**
	 * Retrieve a module path.
	 *
	 * @param string $module_key The module key.
	 * @param string $path       The path.
	 *
	 * @return string
	 */
	function yith_wcbk_get_module_path( string $module_key, string $path ): string {
		return YITH_WCBK_Modules::get_module_path( $module_key, $path );
	}
}

if ( ! function_exists( 'yith_wcbk_get_module_url' ) ) {
	/**
	 * Retrieve a module path.
	 *
	 * @param string $module_key The module key.
	 * @param string $url        The URL.
	 *
	 * @return string
	 */
	function yith_wcbk_get_module_url( string $module_key, string $url ): string {
		return YITH_WCBK_Modules::get_module_url( $module_key, $url );
	}
}

if ( ! function_exists( 'yith_wcbk_get_module_view' ) ) {
	/**
	 * Print a module view.
	 *
	 * @param string $module_key The module key.
	 * @param string $view       The view.
	 * @param array  $args       Arguments.
	 */
	function yith_wcbk_get_module_view( string $module_key, string $view, array $args = array() ) {
		$the_view_path = yith_wcbk_get_module_path( $module_key, 'views/' . $view );
		extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		if ( file_exists( $the_view_path ) ) {
			include $the_view_path;
		}
	}
}

if ( ! function_exists( 'yith_wcbk_get_module_view_html' ) ) {
	/**
	 * Like yith_wcbk_get_module_view, but returns the HTML instead of outputting.
	 *
	 * @param string $module_key The module key.
	 * @param string $view       The view.
	 * @param array  $args       Arguments.
	 */
	function yith_wcbk_get_module_view_html( string $module_key, string $view, array $args = array() ) {
		ob_start();
		yith_wcbk_get_module_view( $module_key, $view, $args );

		return ob_get_clean();
	}
}

if ( ! function_exists( 'yith_wcbk_is_module_active' ) ) {
	/**
	 * Is this module active?
	 *
	 * @param string $module_key The module key.
	 *
	 * @return bool
	 */
	function yith_wcbk_is_module_active( string $module_key ): bool {
		return yith_wcbk()->modules()->is_module_active( $module_key );
	}
}

if ( ! function_exists( 'yith_wcbk_is_people_module_active' ) ) {
	/**
	 * Is the "People" module active?
	 *
	 * @return bool
	 */
	function yith_wcbk_is_people_module_active(): bool {
		return yith_wcbk_is_module_active( 'people' );
	}
}

if ( ! function_exists( 'yith_wcbk_is_resources_module_active' ) ) {
	/**
	 * Is the "Resources" module active?
	 *
	 * @return bool
	 */
	function yith_wcbk_is_resources_module_active(): bool {
		return yith_wcbk_is_module_active( 'resources' );
	}
}

if ( ! function_exists( 'yith_wcbk_is_google_maps_module_active' ) ) {
	/**
	 * Is the "Google Maps" module active?
	 *
	 * @return bool
	 */
	function yith_wcbk_is_google_maps_module_active(): bool {
		return yith_wcbk_is_module_active( 'google-maps' );
	}
}

if ( ! function_exists( 'yith_wcbk_is_services_module_active' ) ) {
	/**
	 * Is the "Services" module active?
	 *
	 * @return bool
	 */
	function yith_wcbk_is_services_module_active(): bool {
		return yith_wcbk_is_module_active( 'services' );
	}
}

if ( ! function_exists( 'yith_wcbk_is_search_forms_module_active' ) ) {
	/**
	 * Is the "Search Forms" module active?
	 *
	 * @return bool
	 */
	function yith_wcbk_is_search_forms_module_active(): bool {
		return yith_wcbk_is_module_active( 'search-forms' );
	}
}

if ( ! function_exists( 'yith_wcbk_is_costs_module_active' ) ) {
	/**
	 * Is the "Costs" module active?
	 *
	 * @return bool
	 */
	function yith_wcbk_is_costs_module_active(): bool {
		return yith_wcbk_is_module_active( 'costs' );
	}
}


if ( ! function_exists( 'yith_wcbk_is_google_calendar_module_active' ) ) {
	/**
	 * Is the "Google Calendar" module active?
	 *
	 * @return bool
	 */
	function yith_wcbk_is_google_calendar_module_active(): bool {
		return yith_wcbk_is_module_active( 'google-calendar' );
	}
}

if ( ! function_exists( 'yith_wcbk_is_external_sync_module_active' ) ) {
	/**
	 * Is the "External sync" module active?
	 *
	 * @return bool
	 */
	function yith_wcbk_is_external_sync_module_active(): bool {
		return yith_wcbk_is_module_active( 'external-sync' );
	}
}

if ( ! function_exists( 'yith_wcbk_get_module_template' ) ) {
	/**
	 * Print a module template.
	 *
	 * @param string $module_key    The module key.
	 * @param string $template      The view.
	 * @param array  $args          Arguments.
	 * @param string $template_path The template path.
	 */
	function yith_wcbk_get_module_template( string $module_key, string $template, array $args = array(), string $template_path = '' ) {
		$default_path = yith_wcbk_get_module_path( $module_key, 'templates/' );

		if ( ! ! $template_path ) {
			$base_path     = is_callable( array( WC(), 'template_path' ) ) ? WC()->template_path() : 'woocommerce/';
			$template_path = trailingslashit( $base_path ) . $template_path;
		}

		wc_get_template( $template, $args, $template_path, $default_path );
	}
}

if ( ! function_exists( 'yith_wcbk_get_module_template_html' ) ) {
	/**
	 * Like yith_wcbk_get_module_template, but returns the HTML instead of outputting.
	 *
	 * @param string $module_key    The module key.
	 * @param string $template      The template.
	 * @param array  $args          Arguments.
	 * @param string $template_path The template path.
	 *
	 * @return string
	 */
	function yith_wcbk_get_module_template_html( string $module_key, string $template, array $args = array(), string $template_path = '' ): string {
		ob_start();
		yith_wcbk_get_module_template( $module_key, $template, $args, $template_path );

		return ob_get_clean();
	}
}
