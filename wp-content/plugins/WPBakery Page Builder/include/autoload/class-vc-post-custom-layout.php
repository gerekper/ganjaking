<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Manager for post custom layouts.
 * @since 7.0
 */
class Vc_PostCustomLayout {

	/**
	 * Meta key where we store layout name.
	 * @since 7.0
	 *
	 * @var string
	 */
	public $layout_meta_name = '_wpb_post_custom_layout';

	/**
	 * Vc_PostCustomLayout constructor.
	 * @since 7.0
	 */
	public function __construct() {
		add_action( 'template_include', [ $this, 'switchPostCustomLayout' ], 11 );
	}

	/**
	 * Change the path of the current template to our custom layout.
	 * @since 7.0
	 *
	 * @param string $template The path of the template to include.
	 * @return string
	 */
	public function switchPostCustomLayout( $template ) {
		if ( ! is_singular() ) {
			return $template;
		}
		$layout_name = $this->getCustomLayoutName();
		if ( ! $layout_name || 'default' === $layout_name ) {
			return $template;
		}

		$custom_layout_path = $this->getCustomLayoutPath( $layout_name );
		if ( $custom_layout_path ) {
			$template = $custom_layout_path;
		}

		return apply_filters( 'vc_post_custom_layout_template', $template, $layout_name );
	}

	/**
	 * Get name of the custom layout.
	 * @note on a plugin core level right now we have only 'blank' layout.
	 * @since 7.0
	 *
	 * @return string
	 */
	public function getCustomLayoutName() {
		global $post;
		if ( $this->isLayoutSwitchedInFrontendEditor() ) {
			$layout_name = $this->getLayoutNameFromGetParams();
		} else {
			$layout_name = $this->getLayoutFromMeta();
		}

		$layout_name = empty( $layout_name ) ? '' : $layout_name;

		if ( ! empty( $post->post_content ) && ! $layout_name ) {
			$layout_name = 'default';
		}

		return apply_filters( 'vc_post_custom_layout_name', $layout_name );
	}

	/**
	 * Check if user switched layout in frontend editor.
	 * @note in such cases we should reload the page
	 * @since 7.0
	 *
	 * @return bool
	 */
	public function isLayoutSwitchedInFrontendEditor() {
		$params = $this->getRequestParams();

		return isset( $params['vc_post_custom_layout'] );
	}

	/**
	 * For a frontend editor we keep layout as get param
	 * when we switching it inside editor and show user new layout inside editor.
	 * @since 7.0
	 *
	 * @return false|string
	 */
	public function getLayoutNameFromGetParams() {
		$params = $this->getRequestParams();

		return empty( $params['vc_post_custom_layout'] ) ? false : $params['vc_post_custom_layout'];
	}

	/**
	 * Retrieve get params.
	 * @description  we should obtain params from $_SERVER['HTTP_REFERER']
	 * if we try to get params inside iframe and from regular $_GET when outside
	 * @since 7.0
	 *
	 * @return array|false
	 */
	public function getRequestParams() {
		if ( ! vc_is_page_editable() && ! vc_is_inline() ) {
			return false;
		}

		// inside iframe
		if ( vc_is_page_editable() ) {
			$params = $this->getParamsFromServerReferer();
			// outside iframe
		} else {
			$params = $_GET;
		}

		return $params;
	}

	/**
	 * Parse $_SERVER['HTTP_REFERER'] and get params from it.
	 * @since 7.0
	 *
	 * @return array|false
	 */
	public function getParamsFromServerReferer() {
		if ( ! isset( $_SERVER['HTTP_REFERER'] ) ) {
			return false;
		}
        // phpcs:ignore
		$query = parse_url( $_SERVER['HTTP_REFERER'], PHP_URL_QUERY );
		if ( ! $query ) {
			return false;
		}

		$params = [];
		parse_str( $query,$params );

		return $params;
	}

	/**
	 * Get previously saved layout from post meta.
	 * @since 7.0
	 *
	 * @return mixed
	 */
	public function getLayoutFromMeta() {
		return get_post_meta( get_the_ID(), $this->layout_meta_name, true );
	}

	/**
	 * Get path of the custom layout.
	 * @note we keep all plugin layouts in include/templates/pages/layouts/ folder.
	 * @since 7.0
	 *
	 * @param string $layout_name
	 * @return string|false
	 */
	public function getCustomLayoutPath( $layout_name ) {
		$custom_layout_path = vc_template( '/pages/layouts/' . $layout_name . '.php' );
		if ( ! is_file( $custom_layout_path ) ) {
			return false;
		}

		return $custom_layout_path;
	}

	/**
	 * Get href for the custom layout by layout name.
	 * @since 7.0
	 *
	 * @param string $layout_name
	 * @return string
	 */
	public function getLayoutHrefByLayoutName( $layout_name ) {
		if ( vc_is_page_editable() || vc_is_inline() ) {
			$frontend_editor = new Vc_Frontend_Editor();
			$href = $frontend_editor->getInlineUrl( get_the_ID() ) . '&vc_post_custom_layout=' . $layout_name;
		} else {
			$href = '#';
		}

		return $href;
	}

	/**
	 * Check if layout active on current location.
	 * @since 7.0
	 *
	 * @param string $check_name
	 * @param string $location settings or welcome
	 * @return bool
	 */
	public function checkIfLayoutActive( $check_name, $location ) {
		$current_name = $this->getCustomLayoutName();

		if ( $current_name && $current_name == $check_name ) {
			return true;
		}

		if ( ! $current_name && 'settings' === $location && 'default' === $check_name ) {
			return true;
		}

		return false;
	}
}

new Vc_PostCustomLayout();
