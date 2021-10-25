<?php

namespace WPForms\Integrations\DefaultThemes;

use WPForms\Integrations\IntegrationInterface;

/**
 * Class DefaultThemes.
 *
 * @since 1.6.6
 */
class DefaultThemes implements IntegrationInterface {

	/**
	 * Twenty Twenty theme name.
	 *
	 * @since 1.6.6
	 */
	const TT = 'twentytwenty';

	/**
	 * Twenty Twenty-One theme name.
	 *
	 * @since 1.6.6
	 */
	const TT1 = 'twentytwentyone';

	/**
	 * Current theme name.
	 *
	 * @since 1.6.6
	 *
	 * @var string
	 */
	private $current_theme;

	/**
	 * Determinate default theme.
	 *
	 * @since 1.6.6
	 *
	 * @return string
	 */
	private function get_current_default_theme() {

		$allowed_themes = [ self::TT, self::TT1 ];
		$theme          = wp_get_theme();
		$theme_name     = $theme->get_template();
		$theme_parent   = $theme->parent();
		$default_themes = array_intersect( array_filter( [ $theme_name, $theme_parent ] ), $allowed_themes );

		return ! empty( $default_themes[0] ) ? $default_themes[0] : '';
	}

	/**
	 * Allow load integration.
	 *
	 * @since 1.6.6
	 *
	 * @return bool
	 */
	public function allow_load() {

		$this->current_theme = $this->get_current_default_theme();

		return ! empty( $this->current_theme );
	}

	/**
	 * Load integration.
	 *
	 * @since 1.6.6
	 */
	public function load() {

		if ( $this->current_theme === self::TT ) {
			$this->tt_hooks();

			return;
		}

		if ( $this->current_theme === self::TT1 ) {
			$this->tt1_hooks();

			return;
		}
	}

	/**
	 * Load hooks for the Twenty Twenty theme.
	 *
	 * @since 1.6.6
	 */
	private function tt_hooks() {

		add_action( 'wp_enqueue_scripts', [ $this, 'tt_iframe_fix' ], 11 );
	}

	/**
	 * Load hooks for the Twenty Twenty-One theme.
	 *
	 * @since 1.6.6
	 */
	private function tt1_hooks() {

		add_action( 'wp_enqueue_scripts', [ $this, 'tt1_multiple_fields_fix' ], 11 );
	}


	/**
	 * Apply fix for checkboxes and radio fields in the Twenty Twenty-One theme.
	 *
	 * @since 1.6.6
	 */
	public function tt1_multiple_fields_fix() {

		wp_add_inline_style(
			'twenty-twenty-one-style',
			'@supports (-webkit-appearance: none) or (-moz-appearance: none) {
				div.wpforms-container-full .wpforms-form input[type=checkbox] {
					-webkit-appearance: checkbox;
					-moz-appearance: checkbox;
				}
				div.wpforms-container-full .wpforms-form input[type=radio] {
					-webkit-appearance: radio;
					-moz-appearance: radio;
				}
				div.wpforms-container-full .wpforms-form input[type=checkbox]:after,
				div.wpforms-container-full .wpforms-form input[type=radio]:after {
					content: none;
				}
			}'
		);
	}

	/**
	 * Apply resize-fix for iframe HTML element, when the next page was clicked in the Twenty Twenty theme.
	 *
	 * @since 1.6.6
	 */
	public function tt_iframe_fix() {

		wp_add_inline_script(
			'twentytwenty-js',
			'window.addEventListener( "load", function() {

				if ( typeof jQuery === "undefined" ) {
					return;
				}

				jQuery( document ).on( "wpformsPageChange", function() { 

					if ( typeof twentytwenty === "undefined" || typeof twentytwenty.intrinsicRatioVideos === "undefined" || typeof twentytwenty.intrinsicRatioVideos.makeFit === "undefined" ) {
						return;
					}
	
					twentytwenty.intrinsicRatioVideos.makeFit();
				} );
			} );'
		);
	}
}
