<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * WPBakery WPBakery Page Builder Main manager.
 *
 * @package WPBakeryPageBuilder
 * @since   4.2
 */
if ( ! function_exists( 'vc_manager' ) ) {
	/**
	 * WPBakery Page Builder manager.
	 * @return Vc_Manager
	 * @since 4.2
	 */
	function vc_manager() {
		return Vc_Manager::getInstance();
	}
}
if ( ! function_exists( 'visual_composer' ) ) {
	/**
	 * WPBakery Page Builder instance.
	 * @return Vc_Base
	 * @since 4.2
	 */
	function visual_composer() {
		return vc_manager()->vc();
	}
}
if ( ! function_exists( 'vc_mapper' ) ) {
	/**
	 * Shorthand for Vc Mapper.
	 * @return Vc_Mapper
	 * @since 4.2
	 */
	function vc_mapper() {
		return vc_manager()->mapper();
	}
}
if ( ! function_exists( 'vc_settings' ) ) {
	/**
	 * Shorthand for WPBakery Page Builder settings.
	 * @return Vc_Settings
	 * @since 4.2
	 */
	function vc_settings() {
		return vc_manager()->settings();
	}
}
if ( ! function_exists( 'vc_license' ) ) {
	/**
	 * Get License manager
	 * @return Vc_License
	 * @since 4.2
	 */
	function vc_license() {
		return vc_manager()->license();
	}
}
if ( ! function_exists( 'vc_automapper' ) ) {
	/**
	 * @return Vc_Automapper
	 * @since 4.2
	 */
	function vc_automapper() {
		return vc_manager()->automapper();
	}
}
if ( ! function_exists( 'vc_frontend_editor' ) ) {
	/**
	 * Shorthand for VC frontend editor
	 * @return Vc_Frontend_Editor
	 * @since 4.2
	 */
	function vc_frontend_editor() {
		return vc_manager()->frontendEditor();
	}
}
if ( ! function_exists( 'vc_backend_editor' ) ) {
	/**
	 * Shorthand for VC frontend editor
	 * @return Vc_Backend_Editor
	 * @since 4.2
	 */
	function vc_backend_editor() {
		return vc_manager()->backendEditor();
	}
}
if ( ! function_exists( 'vc_updater' ) ) {
	/**
	 * @return Vc_Updater
	 * @since 4.2
	 */
	function vc_updater() {
		return vc_manager()->updater();
	}
}
if ( ! function_exists( 'vc_is_network_plugin' ) ) {
	/**
	 * Vc is network plugin or not.
	 * @return bool
	 * @since 4.2
	 */
	function vc_is_network_plugin() {
		return vc_manager()->isNetworkPlugin();
	}
}
if ( ! function_exists( 'vc_path_dir' ) ) {
	/**
	 * Get file/directory path in Vc.
	 *
	 * @param string $name - path name
	 * @param string $file
	 *
	 * @return string
	 * @since 4.2
	 */
	function vc_path_dir( $name, $file = '' ) {
		return vc_manager()->path( $name, $file );
	}
}
if ( ! function_exists( 'vc_asset_url' ) ) {
	/**
	 * Get full url for assets.
	 *
	 * @param string $file
	 *
	 * @return string
	 * @since 4.2
	 */
	function vc_asset_url( $file ) {
		return vc_manager()->assetUrl( $file );
	}
}
if ( ! function_exists( 'vc_upload_dir' ) ) {
	/**
	 * Temporary files upload dir;
	 * @return string
	 * @since 4.2
	 */
	function vc_upload_dir() {
		return vc_manager()->uploadDir();
	}
}
if ( ! function_exists( 'vc_template' ) ) {
	/**
	 * @param $file
	 *
	 * @return string
	 * @since 4.2
	 */
	function vc_template( $file ) {
		return vc_path_dir( 'TEMPLATES_DIR', $file );
	}
}
if ( ! function_exists( 'vc_post_param' ) ) {
	/**
	 * Get param value from $_POST if exists.
	 *
	 * @param $param
	 * @param $default
	 *
	 * @param bool $check
	 * @return null|string - null for undefined param.
	 * @since 4.2
	 */
	function vc_post_param( $param, $default = null, $check = false ) {
		if ( 'admin' === $check ) {
			check_admin_referer();
		} elseif ( 'ajax' === $check ) {
			check_ajax_referer();
		}

		return isset( $_POST[ $param ] ) ? $_POST[ $param ] : $default;
	}
}
if ( ! function_exists( 'vc_get_param' ) ) {
	/**
	 * Get param value from $_GET if exists.
	 *
	 * @param string $param
	 * @param $default
	 *
	 * @param bool $check
	 * @return null|string - null for undefined param.
	 * @since 4.2
	 */
	function vc_get_param( $param, $default = null, $check = false ) {
		if ( 'admin' === $check ) {
			check_admin_referer();
		} elseif ( 'ajax' === $check ) {
			check_ajax_referer();
		}

		// @codingStandardsIgnoreLine
		return isset( $_GET[ $param ] ) ? $_GET[ $param ] : $default;
	}
}
if ( ! function_exists( 'vc_request_param' ) ) {
	/**
	 * Get param value from $_REQUEST if exists.
	 *
	 * @param $param
	 * @param $default
	 *
	 * @param bool $check
	 * @return null|string - null for undefined param.
	 * @since 4.4
	 */
	function vc_request_param( $param, $default = null, $check = false ) {
		if ( 'admin' === $check ) {
			check_admin_referer();
		} elseif ( 'ajax' === $check ) {
			check_ajax_referer();
		}

		// @codingStandardsIgnoreLine
		return isset( $_REQUEST[ $param ] ) ? $_REQUEST[ $param ] : $default;
	}
}
if ( ! function_exists( 'vc_is_frontend_editor' ) ) {
	/**
	 * @return bool
	 * @since 4.2
	 */
	function vc_is_frontend_editor() {
		return 'admin_frontend_editor' === vc_mode();
	}
}
if ( ! function_exists( 'vc_is_page_editable' ) ) {
	/**
	 * @return bool
	 * @since 4.2
	 */
	function vc_is_page_editable() {
		return 'page_editable' === vc_mode();
	}
}
if ( ! function_exists( 'vc_action' ) ) {
	/**
	 * Get VC special action param.
	 * @return string|null
	 * @since 4.2
	 */
	function vc_action() {
		$vc_action = wp_strip_all_tags( vc_request_param( 'vc_action' ) );

		return $vc_action;
	}
}
if ( ! function_exists( 'vc_is_inline' ) ) {
	/**
	 * Get is inline or not.
	 * @return bool
	 * @since 4.2
	 */
	function vc_is_inline() {
		global $vc_is_inline;
		if ( is_null( $vc_is_inline ) ) {
			$vc_is_inline = ( current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' ) ) && 'vc_inline' === vc_action() || ! is_null( vc_request_param( 'vc_inline' ) ) || 'true' === vc_request_param( 'vc_editable' );
		}

		return $vc_is_inline;
	}
}
if ( ! function_exists( 'vc_is_frontend_ajax' ) ) {
	/**
	 * @return bool
	 * @since 4.2
	 */
	function vc_is_frontend_ajax() {
		return 'true' === vc_post_param( 'vc_inline' ) || vc_get_param( 'vc_inline' );
	}
}
/**
 * @depreacted since 4.8 ( use vc_is_frontend_editor )
 * @return bool
 * @since 4.2
 */
function vc_is_editor() {
	return vc_is_frontend_editor();
}

/**
 * @param $value
 * @param bool $encode
 *
 * @return string
 * @since 4.2
 */
function vc_value_from_safe( $value, $encode = false ) {
	// @codingStandardsIgnoreLine
	$value = preg_match( '/^#E\-8_/', $value ) ? rawurldecode( base64_decode( preg_replace( '/^#E\-8_/', '', $value ) ) ) : $value;
	if ( $encode ) {
		$value = htmlentities( $value, ENT_COMPAT, 'UTF-8' );
	}

	return $value;
}

/**
 * @param bool $disable
 * @since 4.2
 *
 */
function vc_disable_automapper( $disable = true ) {
	vc_automapper()->setDisabled( $disable );
}

/**
 * @return bool
 * @since 4.2
 */
function vc_automapper_is_disabled() {
	return vc_automapper()->disabled();
}

/**
 * @param $param
 * @param $value
 *
 * @return mixed|string
 * @since 4.2
 */
function vc_get_dropdown_option( $param, $value ) {
	if ( '' === $value && is_array( $param['value'] ) ) {
		$value = array_shift( $param['value'] );
	}
	if ( is_array( $value ) ) {
		reset( $value );
		$value = isset( $value['value'] ) ? $value['value'] : current( $value );
	}
	$value = preg_replace( '/\s/', '_', $value );

	return ( '' !== $value ? $value : '' );
}

/**
 * @param $prefix
 * @param $color
 *
 * @return string
 * @since 4.2
 */
function vc_get_css_color( $prefix, $color ) {
	$rgb_color = preg_match( '/rgba/', $color ) ? preg_replace( array(
		'/\s+/',
		'/^rgba\((\d+)\,(\d+)\,(\d+)\,([\d\.]+)\)$/',
	), array(
		'',
		'rgb($1,$2,$3)',
	), $color ) : $color;
	$string = $prefix . ':' . $rgb_color . ';';
	if ( $rgb_color !== $color ) {
		$string .= $prefix . ':' . $color . ';';
	}

	return $string;
}

/**
 * @param $param_value
 * @param string $prefix
 *
 * @return string
 * @since 4.2
 */
function vc_shortcode_custom_css_class( $param_value, $prefix = '' ) {
	$css_class = preg_match( '/\s*\.([^\{]+)\s*\{\s*([^\}]+)\s*\}\s*/', $param_value ) ? $prefix . preg_replace( '/\s*\.([^\{]+)\s*\{\s*([^\}]+)\s*\}\s*/', '$1', $param_value ) : '';

	return $css_class;
}

/**
 * @param $subject
 * @param $property
 * @param bool|false $strict
 *
 * @return bool
 * @since 4.9
 */
function vc_shortcode_custom_css_has_property( $subject, $property, $strict = false ) {
	$styles = array();
	$pattern = '/\{([^\}]*?)\}/i';
	preg_match( $pattern, $subject, $styles );
	if ( array_key_exists( 1, $styles ) ) {
		$styles = explode( ';', $styles[1] );
	}
	$new_styles = array();
	foreach ( $styles as $val ) {
		$val = explode( ':', $val );
		if ( is_array( $property ) ) {
			foreach ( $property as $prop ) {
				$pos = strpos( $val[0], $prop );
				$full = ( $strict ) ? ( 0 === $pos && strlen( $val[0] ) === strlen( $prop ) ) : true;
				if ( false !== $pos && $full ) {
					$new_styles[] = $val;
				}
			}
		} else {
			$pos = strpos( $val[0], $property );
			$full = ( $strict ) ? ( 0 === $pos && strlen( $val[0] ) === strlen( $property ) ) : true;
			if ( false !== $pos && $full ) {
				$new_styles[] = $val;
			}
		}
	}

	return ! empty( $new_styles );
}

/**
 * Plugin name for VC.
 *
 * @return string
 * @since 4.2
 */
function vc_plugin_name() {
	return vc_manager()->pluginName();
}

/**
 * @param $filename
 *
 * @return bool|mixed|string
 * @since 4.4.3 used in vc_base when getting an custom css output
 *
 */
function vc_file_get_contents( $filename ) {
	global $wp_filesystem;
	if ( empty( $wp_filesystem ) ) {
		require_once ABSPATH . '/wp-admin/includes/file.php';
		WP_Filesystem( false, false, true );
	}
	/** @var WP_Filesystem_Base $wp_filesystem */
	$output = '';
	if ( is_object( $wp_filesystem ) ) {
		$output = $wp_filesystem->get_contents( $filename );
	}

	if ( ! $output ) {
		// @codingStandardsIgnoreLine
		$output = file_get_contents( $filename );
	}

	return $output;
}

/**
 * HowTo: vc_role_access()->who('administrator')->with('editor')->can('frontend_editor');
 * @return Vc_Role_Access;
 * @since 4.8
 */
function vc_role_access() {
	return vc_manager()->getRoleAccess();
}

/**
 * Get access manager for current user.
 * HowTo: vc_user_access()->->with('editor')->can('frontend_editor');
 * @return Vc_Current_User_Access;
 * @since 4.8
 */
function vc_user_access() {
	return vc_manager()->getCurrentUserAccess();
}

/**
 * @return array
 * @throws \Exception
 */
function vc_user_roles_get_all() {
	require_once vc_path_dir( 'SETTINGS_DIR', 'class-vc-roles.php' );
	$vc_roles = new Vc_Roles();
	$capabilities = array();
	foreach ( $vc_roles->getParts() as $part ) {
		$partObj = vc_user_access()->part( $part );
		$capabilities[ $part ] = array(
			'state' => (is_multisite() && is_super_admin()) ? true : $partObj->getState(),
			'state_key' => $partObj->getStateKey(),
			'capabilities' => $partObj->getAllCaps(),
		);
	}

	return $capabilities;
}

/**
 * @param $data
 *
 * @return string
 */
function vc_generate_nonce( $data, $from_esi = false ) {
	if ( ! $from_esi && ! vc_is_frontend_editor() ) {
		if ( method_exists( 'LiteSpeed_Cache_API', 'esi_enabled' ) && LiteSpeed_Cache_API::esi_enabled() ) {
			if ( method_exists( 'LiteSpeed_Cache_API', 'v' ) && LiteSpeed_Cache_API::v( '1.3' ) ) {
				$params = array( 'data' => $data );

				return LiteSpeed_Cache_API::esi_url( 'js_composer', 'WPBakery Page Builder', $params, 'default', true );// The last parameter is to remove ESI comment wrapper
			}
		}
	}

	return wp_create_nonce( is_array( $data ) ? ( 'vc-nonce-' . implode( '|', $data ) ) : ( 'vc-nonce-' . $data ) );
}

/**
 * @param $params
 *
 * @return string
 */
function vc_hook_esi( $params ) {
	$data = $params['data'];
	echo vc_generate_nonce( $data, true );
	exit;
}

/**
 * @param $nonce
 * @param $data
 *
 * @return bool
 */
function vc_verify_nonce( $nonce, $data ) {
	return (bool) wp_verify_nonce( $nonce, ( is_array( $data ) ? ( 'vc-nonce-' . implode( '|', $data ) ) : ( 'vc-nonce-' . $data ) ) );
}

/**
 * @param $nonce
 *
 * @return bool
 */
function vc_verify_admin_nonce( $nonce = '' ) {
	return (bool) vc_verify_nonce( ! empty( $nonce ) ? $nonce : vc_request_param( '_vcnonce' ), 'vc-admin-nonce' );
}

/**
 * @param $nonce
 *
 * @return bool
 */
function vc_verify_public_nonce( $nonce = '' ) {
	return (bool) vc_verify_nonce( ( ! empty( $nonce ) ? $nonce : vc_request_param( '_vcnonce' ) ), 'vc-public-nonce' );
}

/**
 * @param $type
 * @return bool|mixed|void
 * @throws \Exception
 */
function vc_check_post_type( $type = '' ) {
	if ( empty( $type ) ) {
		$type = get_post_type();
	}
	$valid = apply_filters( 'vc_check_post_type_validation', null, $type );
	if ( is_null( $valid ) ) {
		if ( is_multisite() && is_super_admin() ) {
			return true;
		}
		$state = vc_user_access()->part( 'post_types' )->getState();
		if ( null === $state ) {
			return in_array( $type, vc_default_editor_post_types(), true );
		} elseif ( true === $state && ! in_array( $type, vc_default_editor_post_types(), true ) ) {
			$valid = false;
		} else {
			$valid = vc_user_access()->part( 'post_types' )->can( $type )->get();
		}
	}

	return $valid;
}

/**
 * @param $shortcode
 * @return bool|mixed|void
 */
function vc_user_access_check_shortcode_edit( $shortcode ) {
	$do_check = apply_filters( 'vc_user_access_check-shortcode_edit', null, $shortcode );
	if ( is_multisite() && is_super_admin() ) {
		return true;
	}
	if ( is_null( $do_check ) ) {
		$state_check = vc_user_access()->part( 'shortcodes' )->checkStateAny( true, 'edit', null )->get();
		if ( $state_check ) {
			return true;
		} else {
			return vc_user_access()->part( 'shortcodes' )->canAny( $shortcode . '_all', $shortcode . '_edit' )->get();
		}
	} else {
		return $do_check;
	}
}

/**
 * @param $shortcode
 * @return bool|mixed|void
 * @throws \Exception
 */
function vc_user_access_check_shortcode_all( $shortcode ) {
	$do_check = apply_filters( 'vc_user_access_check-shortcode_all', null, $shortcode );
	if ( is_multisite() && is_super_admin() ) {
		return true;
	}
	if ( is_null( $do_check ) ) {
		return vc_user_access()->part( 'shortcodes' )->checkStateAny( true, 'custom', null )->can( $shortcode . '_all' )->get();
	} else {
		return $do_check;
	}
}

/**
 * htmlspecialchars_decode_deep
 * Call the htmlspecialchars_decode to a gived multilevel array
 *
 * @param mixed $value The value to be stripped.
 *
 * @return mixed Stripped value.
 * @since 4.8
 *
 */
function vc_htmlspecialchars_decode_deep( $value ) {
	if ( is_array( $value ) ) {
		$value = array_map( 'vc_htmlspecialchars_decode_deep', $value );
	} elseif ( is_object( $value ) ) {
		$vars = get_object_vars( $value );
		foreach ( $vars as $key => $data ) {
			$value->{$key} = vc_htmlspecialchars_decode_deep( $data );
		}
	} elseif ( is_string( $value ) ) {
		$value = htmlspecialchars_decode( $value );
	}

	return $value;
}

/**
 * @param $str
 * @return mixed
 */
function vc_str_remove_protocol( $str ) {
	return str_replace( array(
		'https://',
		'http://',
	), '//', $str );
}
