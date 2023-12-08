<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * WPBakery Page Builder shortcodes attributes class.
 *
 * This class and functions represents ability which will allow you to create attributes settings fields to
 * control new attributes.
 * New attributes can be added to shortcode settings by using param array in wp_map function
 *
 * @package WPBakeryPageBuilder
 *
 */

/**
 * Shortcode params class allows to create new params types.
 * class WpbakeryShortcodeParams
 * @since 4.2
 */
class WpbakeryShortcodeParams {
	/**
	 * @since 4.2
	 * @var array - store shortcode attributes types
	 */
	protected static $params = array();
	/**
	 * @since 4.2
	 * @var array - store shortcode javascript files urls
	 */
	protected static $scripts = array();
	/**
	 * @since 4.7
	 * @var array - store params not required to init
	 */
	protected static $optional_init_params = array();

	/**
	 * Get list of params that need to be initialized
	 *
	 * @return string[]
	 */
	public static function getRequiredInitParams() {
		$all_params = array_keys( self::$params );
		$optional_params = apply_filters( 'vc_edit_form_fields_optional_params', self::$optional_init_params );
		$required_params = array_diff( $all_params, $optional_params );

		return $required_params;
	}

	/**
	 * Create new attribute type
	 *
	 * @static
	 * @param $name - attribute name
	 * @param $form_field_callback - hook, will be called when settings form is shown and attribute added to shortcode
	 *     param list
	 * @param $script_url - javascript file url which will be attached at the end of settings form.
	 *
	 * @return bool - return true if attribute type created
	 * @since 4.2
	 *
	 */
	public static function addField( $name, $form_field_callback, $script_url = null ) {
		$result = false;
		if ( ! empty( $name ) && ! empty( $form_field_callback ) ) {
			self::$params[ $name ] = array(
				'callbacks' => array(
					'form' => $form_field_callback,
				),
			);
			$result = true;

			if ( is_string( $script_url ) && ! in_array( $script_url, self::$scripts, true ) ) {
				self::$scripts[] = $script_url;
			}
		}

		return $result;
	}

	/**
	 * Calls hook for attribute type
	 * @param $name - attribute name
	 * @param $param_settings - attribute settings from shortcode
	 * @param $param_value - attribute value
	 * @param $tag - attribute tag
	 *
	 * @return mixed|string - returns html which will be render in hook
	 * @since 4.2
	 * @static
	 *
	 */
	public static function renderSettingsField( $name, $param_settings, $param_value, $tag ) {
		if ( isset( self::$params[ $name ]['callbacks']['form'] ) ) {
			return call_user_func( self::$params[ $name ]['callbacks']['form'], $param_settings, $param_value, $tag );
		}

		return '';
	}

	/**
	 * List of javascript files urls for shortcode attributes.
	 * @return array - list of js scripts
	 * @since 4.2
	 * @static
	 */
	public static function getScripts() {
		return self::$scripts;
	}
}
