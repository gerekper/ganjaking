<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

global $gm_supported_module;
if ( 'crane' === $gm_supported_module['theme'] ) {
	$gm_supported_module['GroovyMenuSingleMetaPreset'] = true;
	$gm_supported_module['GroovyMenuShowIntegration']  = false;

	$support_post_types = array(
		'crane_portfolio' => esc_html__( 'Portfolios', 'groovy-menu' ),
		'product'         => esc_html__( 'Products', 'groovy-menu' ),
	);

	foreach ( $support_post_types as $post_type => $post_name ) {
		$gm_supported_module['post_types'][ $post_type ] = $post_name;
	}

	$gm_supported_module['search_post_type_additional'] = array(
		'crane_portfolio' => array(
			'title'     => esc_html__( 'Search in portfolio', 'groovy-menu' ),
			'condition' => array( 'search_form', 'in', array( 'fullscreen', 'dropdown-without-ajax' ) ),
		),
	);

	$gm_supported_module['activate'][]   = 'gm_activate_action_for_crane_theme';
	$gm_supported_module['deactivate'][] = 'gm_deactivate_action_for_crane_theme';
	$gm_supported_module['check_update'] = true;

}

function gm_activate_action_for_crane_theme() {
	$menu_crane_options = get_option( 'gm_menu_crane_options' );
	if ( ! $menu_crane_options ) {
		return;
	}

	$crane_options = get_option( 'crane_options' );

	global $gm_supported_module;

	foreach ( $menu_crane_options as $option => $value ) {
		$crane_options[ $option ] = $menu_crane_options[ $option ];
	}

	gm_save_redux_options( $crane_options );

}

function gm_deactivate_action_for_crane_theme() {
	$crane_options = get_option( 'crane_options' );

	$menu_crane_options = array(
		'regular-page-menu'     => '',
		'portfolio-menu'        => '',
		'portfolio-single-menu' => '',
		'blog-menu'             => '',
		'blog-single-menu'      => '',
		'shop-menu'             => '',
		'shop-single-menu'      => '',
	);

	foreach ( $menu_crane_options as $option => $value ) {
		if ( isset( $crane_options[ $option ] ) ) {
			$menu_crane_options[ $option ] = $crane_options[ $option ];
		}
	}

	update_option( 'gm_menu_crane_options', $menu_crane_options, false );

}


/**
 * Save redux options
 *
 * @param array $_options kay value array of redux options.
 *
 * @return null|void
 */
function gm_save_redux_options( $_options ) {
	if ( ! class_exists( 'Redux' ) || ! class_exists( '\ReduxFrameworkInstances' ) ) {
		return;
	}

	$redux = \ReduxFrameworkInstances::get_instance( 'crane_options' );

	if ( ! method_exists( $redux, 'set_options' ) ) {
		return;
	}

	try {
		if ( isset( $redux->validation_ran ) ) {
			unset( $redux->validation_ran );
		}

		if ( is_array( $_options ) && isset( $_options['redux-backup'] ) ) {
			unset( $_options['redux-backup'] );
		}

		$redux->set_options( $_options );


		if ( ! empty( $_options['favicon'] ) && is_array( $_options['favicon'] ) ) {
			$favicon_arr = $_options['favicon'];

			if ( ! empty( $favicon_arr['id'] ) ) {
				$image_full  = wp_get_attachment_image_src( $favicon_arr['id'], 'full' );
				$image_thumb = wp_get_attachment_image_src( $favicon_arr['id'], 'thumbnail' );
			} else {
				$image_full  = [ '', '', '' ];
				$image_thumb = [ '', '', '' ];
			}

			\Redux::setOption( 'crane_options', 'favicon', [
				'url'       => isset( $image_full[0] ) ? $image_full[0] : '',
				'id'        => $favicon_arr['id'],
				'height'    => isset( $image_full[2] ) ? strval( $image_full[2] ) : '',
				'width'     => isset( $image_full[1] ) ? strval( $image_full[1] ) : '',
				'thumbnail' => isset( $image_thumb[0] ) ? $image_thumb[0] : '',
			] );

			update_option( 'site_icon', $favicon_arr['id'] );
		}
	} catch ( Exception $e ) {
		$error_message = array( 'status' => $e->getMessage() );
	}

}


if ( ! function_exists( 'gm_get_current_category_options' ) ) {
	/**
	 * Get options data bu category Id or current category
	 *
	 * @param null|int $category_id id of category.
	 *
	 * @return array
	 */
	function gm_get_current_category_options( $category_id = null ) {
		if ( empty( $category_id ) ) {

			$current_cat = get_queried_object();
			$term_id     = isset( $current_cat->term_id ) ? $current_cat->term_id : null;

			if ( $term_id ) {
				$category_id = $term_id;
			} else {
				return null;
			}
		}

		global $gm_supported_module;

		if ( 'crane' === $gm_supported_module['theme'] ) {
			$options = maybe_unserialize( get_term_meta( $category_id, 'crane_term_additional_meta', true ) );
		} else {
			$options['custom_options'] = '1';
		}

		return $options;
	}
}


if ( ! function_exists( 'gm_get_shop_is_catalog' ) ) {
	/**
	 * Check if woocommerce switched to catalog mode
	 *
	 * @return bool
	 */
	function gm_get_shop_is_catalog() {

		global $gm_supported_module;
		$current_theme = $gm_supported_module['theme'];
		$theme_options = isset( $GLOBALS[ $current_theme . '_options' ] ) ? $GLOBALS[ $current_theme . '_options' ] : array();

		if ( isset( $theme_options['shop-is-catalog'] ) && $theme_options['shop-is-catalog'] ) {
			return true;
		}

		return false;
	}
}


if ( ! function_exists( 'gm_debug_message' ) ) {
	/**
	 * @param $message
	 */
	function gm_debug_message( $message ) {
		if ( function_exists( 'crane_debug_message' ) ) {
			crane_debug_message( $message );
		}
	}
}


if ( ! function_exists( 'gm_debug_value' ) ) {

	/**
	 * Write some variable value to debug file, when it's hard to output it directly
	 *
	 * @param $value
	 * @param bool|FALSE $with_backtrace
	 * @param bool $append
	 */
	function gm_debug_value( $value, $with_backtrace = false, $append = false ) {
		if ( function_exists( 'crane_debug_value' ) ) {
			crane_debug_value( $value, $with_backtrace, $append );

			return;
		}

		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			if ( file_exists( ABSPATH . '/wp-admin/includes/file.php' ) ) {
				require_once ABSPATH . '/wp-admin/includes/file.php';
				WP_Filesystem();
			}
		}
		if ( empty( $wp_filesystem ) ) {
			return;
		}

		$data = '';
		static $auto_append = false;

		$data .= '[' . date( 'm/d/Y h:i:s a', time() ) . ']' . "\n";

		if ( $with_backtrace ) {
			$backtrace = debug_backtrace();
			array_shift( $backtrace );
			$data .= print_r( $backtrace, true ) . ":\n";
		}

		$upload_dir_data = wp_upload_dir();
		$basedir         = get_template_directory();
		if ( isset( $upload_dir_data['basedir'] ) ) {
			$basedir = $upload_dir_data['basedir'];
		}

		$filename = $basedir . '/crane_debug.html';

		if ( file_exists( $filename && ! is_writable( $filename ) ) ) {
			$wp_filesystem->chmod( $filename, 0666 );
		}

		ob_start();
		var_dump( $value );
		$data      .= ob_get_clean() . "\n\n";
		$is_append = $append ? : $auto_append;


		if ( is_writable( $filename ) || ( ! file_exists( $filename ) && is_writable( dirname( $filename ) ) ) ) {
			if ( $is_append ) {
				$data = $wp_filesystem->get_contents( $filename ) . $data;
			}

			$wp_filesystem->put_contents( $filename, $data );

		}


		$auto_append = true;
	}
}

