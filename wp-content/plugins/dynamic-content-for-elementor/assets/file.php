<?php
/** Loads the WordPress Environment and Template */
define( 'WP_USE_THEMES', false );
require '../../../../wp-blog-header.php';

$element_id = empty( $_GET['element_id'] ) ? 0 : sanitize_text_field( $_GET['element_id'] );
$md5 = empty( $_GET['md5'] ) ? 0 : sanitize_text_field( $_GET['md5'] );

if ( $element_id && $md5 ) {

	function glob_recursive( $base, $pattern, $flags = 0 ) {
		if ( substr( $base, -1 ) !== DIRECTORY_SEPARATOR ) {
			$base .= DIRECTORY_SEPARATOR;
		}

		$files = glob( $base . $pattern, $flags );

		foreach ( glob( $base . '*', GLOB_ONLYDIR | GLOB_NOSORT | GLOB_MARK ) as $dir ) {
			$dir_files = glob_recursive( $dir, $pattern, $flags );
			if ( false !== $dir_files ) {
				$files = array_merge( $files, $dir_files );
			}
		}

		return $files;
	}

	// static settings
	$widget = \DynamicContentForElementor\Helper::get_elementor_element_by_id( $element_id );

	$settings = $widget->get_settings_for_display();

	$everyonehidden = false;
	if ( ! empty( $settings['private_access'] ) ) {
		$temp_current_user = wp_get_current_user();
		if ( $temp_current_user && $temp_current_user->ID ) {
			$user_roles = $temp_current_user->roles; // It's possible to have multiple roles
			if ( ! is_array( $user_roles ) ) {
				$user_roles = array( $user_roles );
			}
			if ( is_array( $settings['user_role'] ) ) {
				$tmp_role = array_intersect( $user_roles, $settings['user_role'] );
				if ( ! empty( $tmp_role ) ) {
					$everyonehidden = true;
				}
			}
		} else {
			if ( in_array( 'visitor', $settings['user_role'], true ) ) {
				$everyonehidden = true;
			}
		}
	}
	if ( $everyonehidden ) {
		$base_dir = false;
		switch ( $settings['path_selection'] ) {
			case 'custom':
				$base_dir = $settings['folder_custom'];
				break;
			case 'uploads':
				$base_dir = $settings['folder'];
				$base_title = $settings['folder'];
				if ( $settings[ 'subfolder_' . $settings['folder'] ] ) {
					$base_dir .= $settings[ 'subfolder_' . $settings['folder'] ];
				}
				break;
		}

		if ( $base_dir ) {
			$folder = \DynamicContentForElementor\Widgets\FileBrowser::getRootDir( $base_dir, $settings );
			$files = glob_recursive( $folder, '*' );
			foreach ( $files as $afile ) {
				$afile_md5 = md5( $afile );
				if ( $afile_md5 === $md5 ) {

					status_header( 200 );
					global $wp_query;
					$wp_query->is_singular = true;
					$wp_query->is_page = $wp_query->is_singular;
					$wp_query->is_404 = false;

					$file_name = urlencode( basename( $afile ) );

					header( 'Content-Type: ' . mime_content_type( $afile ) );
					header( 'Content-Disposition: attachment; filename=' . $file_name );
					header( 'Content-Length: ' . filesize( $afile ) );
					readfile( $afile );

					exit();

				}
			}
		}
	} else {

		if ( ! is_user_logged_in() ) {
			wp_safe_redirect( wp_login_url() );
			exit;
		}

		if ( ! empty( $settings['user_redirect'] ) ) {
			$location = $settings['user_redirect']['url'];
			wp_safe_redirect( $location );
			exit();
		}
	}
}

status_header( 403 );
nocache_headers();
global $wp_query;
$wp_query->is_singular = false;
$wp_query->is_page = $wp_query->is_singular;
$wp_query->is_404 = true;
get_template_part( 'template-parts/404' );
