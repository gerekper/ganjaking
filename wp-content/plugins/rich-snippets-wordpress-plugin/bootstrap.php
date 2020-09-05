<?php

namespace wpbuddy\rich_snippets;

use Throwable;
use wpbuddy\rich_snippets\pro\Rich_Snippets_Plugin_Pro;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


spl_autoload_register( '\wpbuddy\rich_snippets\autoloader', true );

/**
 * The autoloader function.
 *
 * @param string $class_name
 *
 * @return bool
 * @since 1.0.0
 * @since 2.0.0 renamed
 */
function autoloader( $class_name ) {

	if ( 0 !== stripos( $class_name, 'wpbuddy\\rich_snippets\\' ) ) {
		# not our files
		return false;
	}

	# make everything lowercase
	$file_name = strtolower( $class_name );

	# remove "wpbuddy\rich_snippets\"
	$file_name = str_replace( 'wpbuddy\\rich_snippets\\pro\\', '', $file_name );
	$file_name = str_replace( 'wpbuddy\\rich_snippets\\', '', $file_name );

	# find sub-paths
	$sub_path = strtolower( str_replace( '_', '', strrchr( $file_name, '_' ) ) );

	if ( ! in_array( $sub_path, array( 'model', 'view', 'controller' ) ) ) {
		$sub_path = '';
	}

	if ( ! empty( $sub_path ) ) {
		$file_name = str_replace( '_' . $sub_path, '', $file_name );
		$sub_path  .= '/';
	}

	# replace "_" with "-"
	$file_name = str_replace( '_', '-', $file_name );

	/**
	 * PRO files
	 */
	if ( false !== stripos( $class_name, 'wpbuddy\\rich_snippets\\pro' ) ) {
		$file_path = sprintf( '%s/pro/classes/%s%s.php', __DIR__, $sub_path, $file_name );

		if ( is_file( $file_path ) ) {
			require_once $file_path;

			return true;
		}

		# check if this is in the PRO version
		$file_path = sprintf( '%s/pro/classes/%s.php', __DIR__, $file_name );

		if ( is_file( $file_path ) ) {
			require_once $file_path;

			return true;
		}

		# check if this is an object from the PRO version
		$file_path = sprintf( '%s/pro/classes/objects/%s.php', __DIR__, $file_name );

		if ( is_file( $file_path ) ) {
			require_once $file_path;

			return true;
		}
	} else {
		# full file path
		$file_path = sprintf( '%s/classes/%s%s.php', __DIR__, $sub_path, $file_name );

		if ( is_file( $file_path ) ) {
			require_once $file_path;

			return true;
		}

		# check if this is an object
		$file_path = sprintf( '%s/classes/objects/%s.php', __DIR__, $file_name );

		if ( is_file( $file_path ) ) {
			require_once $file_path;

			return true;
		}
	}

	return false;
}


/**
 * Saves a throwable error and returns it.
 *
 * @param null|Throwable $e
 *
 * @return Throwable
 * @since 2.19.0
 */
function snip_throwable( $e = null ) {
	static $val;

	if ( ! is_null( $e ) ) {
		$val = $e;
	}

	return $val;
}

/**
 * Helper function to get an instance of the rich snippets class.
 *
 * @return bool|Rich_Snippets_Plugin|Rich_Snippets_Plugin_Pro
 *
 * @throws Throwable Throws an error if WP_DEBUG is turned ON.
 * @since 2.0.0
 */
function rich_snippets() {

	try {
		if ( defined( 'WPB_RS_PRO' ) && ! WPB_RS_PRO ) {
			$instance = Rich_Snippets_Plugin::instance();
		} elseif ( class_exists( '\wpbuddy\rich_snippets\pro\Rich_Snippets_Plugin_Pro' ) ) {
			$instance = Rich_Snippets_Plugin_Pro::instance();
		} else {
			$instance = Rich_Snippets_Plugin::instance();
		}

		$instance->init();

		return $instance;
	} catch ( Throwable $e ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			throw $e;
		}

		snip_throwable( $e );

		add_action(
			'admin_notices',
			function () {
				$e = snip_throwable();

				if ( ! $e instanceof Throwable ) {
					return;
				}

				printf(
					'<div class="notice error"><p>%s</p></div>',
					sprintf(
						__(
							'Oops. SNIP crashed. Please send the following error to us so that we can analyze the error:<br />'
							. 'Error Message: %s'
							. 'File: %s'
							. 'Line: %s'
							. 'Stack Trace: %s',
							'snip'
						),
						sprintf( '<strong>%s</strong><br />', $e->getMessage() ),
						sprintf( '<strong>%s</strong><br />', $e->getFile() ),
						sprintf( '<strong>%s</strong><br />', $e->getLine() ),
						sprintf( '<pre><code>%s</code></pre><br />', $e->getTraceAsString() )
					)
				);
			}
		);

		return false;
	}
}

rich_snippets();
