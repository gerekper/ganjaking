<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* REGISTER PAGE */
add_action( 'admin_menu', 'mpc_register_page_installer' );
function mpc_register_page_installer() {
	add_submenu_page( 'ma-panel', __( 'Page Installer', 'mpc' ),  __( 'Page Installer', 'mpc' ), 'manage_options', 'mpc-panel-page-installer', 'mpc_panel_page_installer' );
}

add_action( 'admin_enqueue_scripts', 'mpc_register_scripts_page_installer' );
function mpc_register_scripts_page_installer( $hook ) {
	if ( $hook != 'massive-panel_page_mpc-panel-page-installer' ) {
		return;
	}

	wp_enqueue_style( 'mpc-panel-css', mpc_get_plugin_path( __FILE__ ) . '/assets/css/mpc-panel.css' );

	wp_enqueue_script( 'mpc-panel-js', mpc_get_plugin_path( __FILE__ ) . '/assets/js/mpc-panel.js', array( 'jquery', 'underscore' ), MPC_MASSIVE_VERSION, true );
}

function mpc_panel_page_installer() {
	$templates = file_get_contents( mpc_get_plugin_path( __FILE__, 'dir' ) . '/assets/pages/pages.json' );
	if ( $templates ) {
		$templates = json_decode( $templates, true );
	} else {
		$templates = array();
	}

	?>
	<div id="mpc_panel" class="mpc-panel">
		<header class="mpc-panel__header">
			<img class="mpc-panel__logo" src="<?php echo mpc_get_plugin_path( __FILE__ ); ?>/assets/images/logo_dark.png" alt="Logo" width="56" height="56">
			<h1 class="mpc-panel__name">
				<?php _e( 'Page Installer', 'mpc' ); ?>
			</h1>
		</header>

		<div class="mpc-section mpc-section--pages">
			<h2 class="mpc-section__title">
				<?php _e( 'Templates', 'mpc' ); ?>
				<ul id="mpc_pages__filter" class="mpc-pages__filter">
					<li><a href="#all" class="mpc-active"><?php _e( 'All', 'mpc' ); ?></a></li>
					<li><a href="#home"><?php _e( 'Home', 'mpc' ); ?></a></li>
					<li><a href="#page"><?php _e( 'Page', 'mpc' ); ?></a></li>
<!--					<li><a href="#section">--><?php //_e( 'Section', 'mpc' ); ?><!--</a></li>-->
				</ul>
			</h2>
			<div class="mpc-section__content">
				<?php if ( ! $templates ) : ?>
					<p><?php _e( 'Something went wrong with reading page templates. Please try again :)', 'mpc' ) ?></p>
				<?php else : ?>
					<?php foreach( $templates as $template_id => $template_values ) : ?>
						<div class="mpc-preset mpc-page-template mpc-template--<?php echo $template_values[ 'type' ]; ?>" data-preset="<?php echo $template_id; ?>">
							<img src="<?php echo mpc_get_plugin_path( __FILE__ ) . '/assets/pages/preview/' . $template_id . '.jpg'; ?>" width="240" height="100" alt="<?php _e( 'Page Template', 'mpc' ); ?>">
							<p><?php echo $template_values[ 'name' ]; ?></p>
							<div class="mpc-installed-badge"><i class="dashicons dashicons-yes"></i></div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>

		<!-- FOOTER -->
		<footer class="mpc-panel__footer">
			<a href="#install" id="mpc_pages__install" class="mpc-pages__install mpc-panel__primary" data-message="<?php _e( 'Are you sure you want to install selected pages?', 'mpc' ); ?>">
				<span class="mpc-default"><?php _e( 'Install', 'mpc' ); ?></span>
				<span class="mpc-working"><?php _e( 'Installing...', 'mpc' ); ?></span>
				<span class="mpc-finished"><?php _e( 'Installed :)', 'mpc' ); ?></span>
				<span class="mpc-install__progress mpc-progress"></span>
			</a>
			<select name="mpc-pages__settings" id="mpc_pages__settings" class="mpc-pages__settings">
				<option value=""><?php _e( 'Use current panel settings', 'mpc' ); ?></option>
			</select>
		</footer>

		<div id="mpc_panel__error" class="mpc-panel__error">
			<i class="dashicons dashicons-warning"></i>
			<span class="mpc-panel__error-message"><?php _e( 'Something went wrong :(<br>', 'mpc' ); ?></span>
			<span class="mpc-panel__error-response"></span>
			<span class="mpc-panel__success-message"><?php _e( 'Success! You can check newly installed templates at <a target="_blank" href="/wp-admin/edit.php?post_type=page&orderby=date&order=desc">Pages</a>.', 'mpc' ); ?></span>
			<i class="dashicons dashicons-no mpc-panel__close"></i>
		</div>

		<div id="mpc_panel__cover" class="mpc-panel__cover">
			<div class="mpc-ajax"><div><span></span><span></span><span></span></div></div>
		</div>
	</div>

	<?php wp_nonce_field( 'mpc-ma-page-installer' );
}

add_action( 'wp_ajax_mpc_import_pages_templates', 'mpc_import_pages_templates' );
function mpc_import_pages_templates() {
	if ( ! isset( $_POST[ 'templates_ids' ] ) || ! isset( $_POST[ 'panel_id' ] ) || ! isset( $_POST[ '_wpnonce' ] ) ) {
		wp_send_json_error();
	}

	check_ajax_referer( 'mpc-ma-page-installer' );

	ini_set( 'max_execution_time', 0 );

	if ( ! defined( 'FS_CHMOD_DIR' ) ) {
		define( 'FS_CHMOD_DIR', ( 0755 & ~ umask() ) );
	}

	if ( ! defined( 'FS_CHMOD_FILE' ) ) {
		define( 'FS_CHMOD_FILE', ( 0644 & ~ umask() ) );
	}

	$issues = array();
	foreach ( $_POST[ 'templates_ids' ] as $template ) {
		if ( ! mpc_import_page_template( $template ) ) {
			$issues[] = $template;
		}
	}

	if ( count( $issues ) == 0 ) {
		wp_send_json_success();
	} else {
		wp_send_json_error( $issues );
	}
}

function mpc_import_page_template( $template ) {

	$templates_url = 'https://massive.mpcthemes.net/templates/';

	$wp_upload_dir = wp_upload_dir();
	$template_path = $wp_upload_dir[ 'basedir' ] . '/mpc_templates';

	if ( is_dir( $template_path ) ) {
		mpc_import_clear_directory( $template_path );
		rmdir( $template_path );
	}

	if ( ! mkdir( $template_path ) ) {
		wp_send_json_error(
			'Sorry couldn\'t create directory: ' . $template_path .
			'<br>Please ask your hosting admin to check PHP permissions to create or remove directories.'
		);
	}

	if ( ! file_exists( $template_path ) ) {
		wp_send_json_error(
			'Directory does not exist: ' . $template_path .
			'<br>Please ask your hosting admin to check PHP permissions to create directories.'
		);
	}

	$template_file = $template_path . '/' . $template . '.zip';
	$template_url  = $templates_url . $template . '.zip';

	if ( file_exists( $template_file ) ) {
		unlink( $template_file );
	}

	$downloaded_file = download_url( $template_url, HOUR_IN_SECONDS );
	copy( $downloaded_file, $template_file );
	unlink( $downloaded_file );

	WP_Filesystem();
	$unzipped_file = unzip_file( $template_file, $template_path );

	unlink( $template_file );

	if ( ! $unzipped_file ) {
		wp_send_json_error(
			'Sorry couldn\'t unzip template file: ' . $template_file .
			'<br>Please ask your hosting admin to check if ZIP extension is enabled on your server.'
		);
	} else {
		$status = mpc_import_local_page_template( $template );
	}

	mpc_import_clear_directory( $template_path );

	rmdir( $template_path );

	return $status;
}

function mpc_import_clear_directory( $template_path ) {
	$dir_iterator = new RecursiveDirectoryIterator( $template_path, RecursiveDirectoryIterator::SKIP_DOTS );
	$files        = new RecursiveIteratorIterator( $dir_iterator, RecursiveIteratorIterator::CHILD_FIRST );
	foreach ( $files as $file ) {
		if ( $file->isDir() ) {
			rmdir( $file->getRealPath() );
		} else {
			unlink( $file->getRealPath() );
		}
	}
}

function mpc_import_local_page_template( $template ) {
	$wp_upload_dir = wp_upload_dir();
	$template_path = $wp_upload_dir[ 'basedir' ];

	// Importing content
	$content = @file_get_contents( $template_path . '/mpc_templates/' . $template . '/' . $template . '.txt' );
	if ( $content === false ) {
		return false;
	}

	$cached_images = get_option( 'mpc_cached_images' );
	if ( $cached_images === false ) {
		$cached_images = array();
	} else {
		if ( ! is_array( $cached_images ) ) {
			$cached_images = array();
		}
	}

	// Importing content images
	$content = preg_replace_callback( '/(css="[^"]*url\()([^)]*)(\))/i', function( $matches ) use ( &$cached_images, $template ) {
		return mpc_import_page_template__images_css( $matches, $template, $cached_images );
	}, $content );

	$content = preg_replace_callback( '/(image[s]*="|parallax_background=")([^"]*)(")/i', function( $matches ) use ( &$cached_images, $template ) {
		return mpc_import_page_template__images_ids( $matches, $template, $cached_images );
	}, $content );

	if ( $template == $_POST[ 'panel_id' ] ) {
		$settings = mpc_import_page_template__panel( $template, $cached_images );
	}

	// Importing presets
	global $mpc_sub_presets;
	$mpc_sub_presets = array(
		'mpc_pagination' => array(),
		'mpc_navigation' => array(),
		'typography'     => array(),
	);

	$sub_presets = array(
		'typography'     => 'font_preset="|icon_preset="',
		'mpc_pagination' => 'mpc_pagination__preset="',
		'mpc_navigation' => 'mpc_navigation__preset="',
	);

	foreach ( $sub_presets as $sub_preset_name => $sub_preset_pattern ) {
		$presets = array();
		preg_match_all( '/(' . $sub_preset_pattern . ')([^"]*)(")/', $content, $presets );

		if ( isset( $settings ) && $settings[ 'b_header__content' ] != '' ) {
			$header_presets = array();
			preg_match_all( '/(' . $sub_preset_pattern . ')([^"]*)(")/', $settings[ 'b_header__content' ], $header_presets );

			if ( isset( $header_presets[ 2 ] ) ) {
				if ( ! isset( $presets[ 2 ] ) ) {
					$presets[ 2 ] = array();
				}

				$presets[ 2 ] = array_merge( $presets[ 2 ], $header_presets[ 2 ] );
			}
		}

		if ( ! empty( $presets[ 2 ] ) ) {
			$presets = array_flip( $presets[ 2 ] );
			$mpc_sub_presets[ $sub_preset_name ] = array_fill_keys( array_keys( $presets ), true );
		}
	}

	do_action( 'mpc_install_template_presets' );

	// Importing custom CSS
	$css = @file_get_contents( $template_path . '/mpc_templates/' . $template . '/' . $template . '.css' );
	if ( $css === false ) {
		$css = '';
	}

	// Adding new page template
	$preset_post_id = wp_insert_post( array(
		'post_title'   => 'Template - ' . ucwords( preg_replace( '/[_-]/', ' ', $template ) ),
		'post_name'    => 'template_' . $template,
		'post_content' => $content,
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'meta_input'   => array(
			'_wpb_post_custom_css' => $css,
		),
	) );

	update_option( 'mpc_cached_images', $cached_images, false );

	return $preset_post_id != 0;
}

function mpc_import_page_template__panel( $template, &$cached_images ) {
	$wp_upload_dir = wp_upload_dir();
	$template_path = $wp_upload_dir[ 'basedir' ];

	// Importing header content images
	$settings = @file_get_contents( $template_path . '/mpc_templates/' . $template . '/' . $template . '.json' );
	if ( $settings !== false ) {
		$settings = json_decode( $settings, true );

		if ( ! $settings ) {
			$settings = array();
		}
	} else {
		$settings = array();
	}

	if ( $settings[ 'b_header__content' ] != '' ) {
		$settings[ 'b_header__content' ] = preg_replace_callback( '/(css="[^"]*url\()([^)]*)(\))/i', function( $matches ) use ( &$cached_images, $template ) {
			return mpc_import_page_template__images_css( $matches, $template, $cached_images );
		}, $settings[ 'b_header__content' ] );

		$settings[ 'b_header__content' ] = preg_replace_callback( '/(image[s]*="|parallax_background=")([^"]*)(")/i', function( $matches ) use ( &$cached_images, $template ) {
			return mpc_import_page_template__images_ids( $matches, $template, $cached_images );
		}, $settings[ 'b_header__content' ] );
	}

	// Importing panel images
	$base_path = wp_upload_dir();
	$base_path = $base_path[ 'basedir' ] . '/mpc_templates/' . $template;

	foreach ( $settings as $name => $value ) {
		if ( $name == 'b_logo__image' || $name == 'b_mobile_logo__image' ) {
			if ( $value[ 'url' ] != '' ) {
				if ( ! isset( $cached_images[ $value[ 'url' ] ] ) ) {
					$image_path = preg_replace( '/.*uploads/', $base_path, $value[ 'url' ] );

					$image_id = mpc_import_single_image( $image_path, true );

					if ( $image_id != '' ) {
						$cached_images[ $value[ 'url' ] ] = $image_id;
					}
				}

				$full_image  = wp_get_attachment_image_src( $cached_images[ $value[ 'url' ] ], 'full' );
				$thumb_image = wp_get_attachment_image_src( $cached_images[ $value[ 'url' ] ] );

				$value[ 'id' ]        = $cached_images[ $value[ 'url' ] ];
				$value[ 'url' ]       = isset( $full_image[ 0 ] ) ? $full_image[ 0 ] : '';
				$value[ 'width' ]     = isset( $full_image[ 1 ] ) ? $full_image[ 1 ] : '';
				$value[ 'height' ]    = isset( $full_image[ 2 ] ) ? $full_image[ 2 ] : '';
				$value[ 'thumbnail' ] = isset( $thumb_image[ 0 ] ) ? $thumb_image[ 0 ] : '';

				$settings[ $name ] = $value;
			}
		} elseif ( strpos( $name, 'background-default' ) !== false ) {
			if ( $value[ 'background-image' ] != '' ) {
				if ( ! isset( $cached_images[ $value[ 'background-image' ] ] ) ) {
					$image_path = preg_replace( '/.*uploads/', $base_path, $value[ 'background-image' ] );

					$image_id = mpc_import_single_image( $image_path, true );

					if ( $image_id != '' ) {
						$cached_images[ $value[ 'background-image' ] ] = $image_id;
					}
				}

				$full_image  = wp_get_attachment_image_src( $cached_images[ $value[ 'background-image' ] ], 'full' );
				$thumb_image = wp_get_attachment_image_src( $cached_images[ $value[ 'background-image' ] ] );

				$value[ 'media' ][ 'id' ]        = $cached_images[ $value[ 'background-image' ] ];
				$value[ 'background-image' ]     = isset( $full_image[ 0 ] ) ? $full_image[ 0 ] : '';
				$value[ 'media' ][ 'width' ]     = isset( $full_image[ 1 ] ) ? $full_image[ 1 ] : '';
				$value[ 'media' ][ 'height' ]    = isset( $full_image[ 2 ] ) ? $full_image[ 2 ] : '';
				$value[ 'media' ][ 'thumbnail' ] = isset( $thumb_image[ 0 ] ) ? $thumb_image[ 0 ] : '';

				$settings[ $name ] = $value;
			}
		}
	}

	// Importing panel settings
	if ( class_exists( 'ReduxFrameworkInstances' ) ) {
		$redux = ReduxFrameworkInstances::get_instance( 'mpc_bober' );

		if ( ! empty( $settings ) ) {
			$settings = array_merge( $redux->options, $settings );

			update_option( 'mpc_bober', $settings );
		}
	}

	return $settings;
}

function mpc_import_page_template__images_css( $matches, $template, &$cached_images ) {
	if ( isset( $cached_images[ $matches[ 2 ] ] ) && get_post_status( $cached_images[ $matches[ 2 ] ] ) !== false ) {
		$url = wp_get_attachment_url( (int) $matches[ 2 ] );
		$url .= '?id=' . $cached_images[ $matches[ 2 ] ];
	} else {
		$base_path = wp_upload_dir();
		$base_path = $base_path[ 'basedir' ] . '/mpc_templates/' . $template;

		$image_path = preg_replace( '/.*uploads/', $base_path, $matches[ 2 ] );

		$image_id = mpc_import_single_image( $image_path, true );
		$url = wp_get_attachment_url( $image_id );

		if ( $image_id != '' ) {
			$cached_images[ $matches[ 2 ] ] = $image_id;
			$url .= '?id=' . $image_id;
		}
	}

	return $matches[ 1 ] . $url . $matches[ 3 ];
}

function mpc_import_page_template__images_ids( $matches, $template, &$cached_images ) {
	if ( strpos( $matches[ 2 ], ',' ) !== false ) {
		$images = explode( ',', $matches[ 2 ] );
	} else {
		$images = array( $matches[ 2 ] );
	}

	$base_path = wp_upload_dir();
	$base_path = $base_path[ 'basedir' ] . '/mpc_templates/' . $template;

	$urls = array();
	foreach ( $images as $image ) {
		if ( isset( $cached_images[ $image ] ) && get_post_status( $cached_images[ $image ] ) !== false ) {
			$urls[] = $cached_images[ $image ];
		} else {
			$image_path = preg_replace( '/.*uploads/', $base_path, $image );

			$image_id = mpc_import_single_image( $image_path, true );

			if ( $image_id != '' ) {
				$cached_images[ $image ] = $image_id;
				$urls[] = $image_id;
			}
		}
	}

	return $matches[ 1 ] . implode( ',', $urls ) . $matches[ 3 ];
}
