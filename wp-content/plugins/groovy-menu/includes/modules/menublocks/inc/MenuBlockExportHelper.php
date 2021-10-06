<?php

namespace GroovyMenu;

defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Class MenuBlockExportHelper
 */
class MenuBlockExportHelper {

	/**
	 * Groovy Menu Menu Block bulk export action name.
	 */
	const BULK_EXPORT_ACTION_NAME = 'groovy-menu_menu_block__export';

	/**
	 * Groovy Menu temporary files folder.
	 */
	const TEMP_FILES_DIR = 'groovy/tmp';

	/**
	 * Preset export params skel.
	 */
	private $preset_default = array();


	public function __construct() {
		$theme = wp_get_theme();
		if ( ! empty( $theme['Template'] ) ) {
			$theme = wp_get_theme( $theme['Template'] );
		}

		$this->preset_default = array(
			'template_slug' => $theme['Template'],
			'preset_name'   => '',
			'preset_type'   => 'page',
			'screenshots'   => array(),
			'posts'         => array(),
			'assets'        => array(),
			'plugins'       => array(),
			'options'       => array(),
			'sidebars'      => array(),
			'versions'      => array(
				'template_version'   => $theme['Version'],
				'preset_create_date' => date( 'Y-m-d H:i:s' ),
			),
			'id_regexp'     => array(),
		);


		// Register Bulk action.
		add_filter( 'bulk_actions-edit-gm_menu_block', array( $this, 'register_export_bulk_action' ) );

		// Handle export Bulk action.
		add_filter( 'handle_bulk_actions-edit-gm_menu_block', array( $this, 'export_bulk_action_handler' ), 10, 3 );

	}

	public function register_export_bulk_action( $bulk_actions ) {
		$bulk_actions[ self::BULK_EXPORT_ACTION_NAME ] = __( 'Export', 'groovy-menu' ) . ' ' . __( 'Menu block', 'groovy-menu' );

		return $bulk_actions;
	}

	/**
	 * Add bulk export action.
	 * Handles the Groovy Menu Menu Block bulk export action.
	 * Fired by `handle_bulk_actions-edit-gm_menu_block` filter.
	 *
	 * @since  2.5.0
	 * @access public
	 *
	 * @param string $redirect_to The redirect URL.
	 * @param string $action      The action being taken.
	 * @param array  $post_ids    The items to take the action on.
	 *
	 * @return string
	 */
	public function export_bulk_action_handler( $redirect_to, $action, $post_ids ) {
		// ignore that func if wrong action.
		if ( self::BULK_EXPORT_ACTION_NAME !== $action ) {
			return $redirect_to;
		}

		$export_result = $this->export_menu_blocks( $post_ids );

		// If you reach this line, the export failed.
		wp_die( $export_result->get_error_message() );
	}

	/**
	 * Export multiple Groovy Menu Menu Block to a ZIP file.
	 *
	 * @since  2.5.0
	 * @access public
	 *
	 * @param array $post_ids An array of template IDs.
	 *
	 * @return \WP_Error WordPress error if export failed.
	 */
	public function export_menu_blocks( array $post_ids ) {
		if ( ! class_exists( 'ZipArchive' ) ) {
			return new \WP_Error( '502', sprintf( 'Cannot find PHP class ZipArchive' ) );
		}

		if ( ! defined( 'FS_METHOD' ) ) {
			define( 'FS_METHOD', 'direct' );
		}

		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			$file_path = str_replace( array(
				'\\',
				'/'
			), DIRECTORY_SEPARATOR, ABSPATH . '/wp-admin/includes/file.php' );

			if ( file_exists( $file_path ) ) {
				require_once $file_path;
				WP_Filesystem();
			}
		}
		if ( empty( $wp_filesystem ) ) {
			// if err.
			return new \WP_Error( '502', sprintf( 'Cannot start $wp_filesystem' ) );
		}


		$export_data = $this->create( array( 'posts' => array( 'gm_menu_block' => $post_ids ) ) );


		$export_assets_class = new \GroovyMenu\MenuBlockExportAssets();
		$preset_need_image   = array();

		// Content regexper.
		foreach ( $export_data['posts'] as $post_type => $posts ) {

			if ( empty( $posts ) ) {
				continue;
			}

			foreach ( $posts as $post_id => $post ) {

				if ( empty( $post ) ) {
					continue;
				}

				// -------- MAKE CONTENT --------.
				$raw_data = $export_assets_class->set_placeholders( $post['post_content'], $export_data['assets_regexp'], $preset_need_image );

				$export_data['posts'][ $post_type ][ $post_id ]['post_content'] = $raw_data['content'];

				foreach ( $raw_data['content_images'] as $asset_id => $asset_pattern ) {
					$preset_need_image[ $asset_id ] = $asset_pattern;
				}

				// -------- MAKE META --------.
				if ( ! empty( $post['POST_META'] ) ) {
					$post_meta = $post['POST_META'];

					foreach ( $post['POST_META'] as $meta_key => $meta_data ) {

						if ( is_string( $meta_data ) && ! empty( json_decode( $meta_data, true ) ) ) {
							$meta_data = wp_unslash( wp_json_encode( json_decode( $meta_data, true ) ) );
						}

						// TODO CHEK if $meta_data is searilized, if so - unserialize first
						$raw_data               = $export_assets_class->set_placeholders( $meta_data, $export_data['assets_regexp'], $preset_need_image );
						$post_meta[ $meta_key ] = $raw_data['content'];

						foreach ( $raw_data['content_images'] as $asset_id => $asset_pattern ) {
							$preset_need_image[ $asset_id ] = $asset_pattern;
						}
					}

					$export_data['posts'][ $post_type ][ $post_id ]['POST_META'] = $post_meta;
				}
			}
		}


		$temp_path        = $this->get_temp_dir( esc_attr( $export_data['preset_name'] ) );
		$temp_path_assets = $this->get_temp_dir( esc_attr( $export_data['preset_name'] ) . '/assets' );

		// Create temp path if it doesn't exist.
		$this->create_folder( $temp_path );
		$this->create_folder( $temp_path_assets );


		// GET assets by $preset_need_image
		foreach ( $preset_need_image as $id => $item ) {
			// Skip JUST_FILE.
			if ( isset( $item['data']['included'] ) && 'JUST_FILE' === $item['data']['included'] ) {
				continue;
			}

			$filename     = 'grnplhld_' . $id . '.' . pathinfo( basename( $item['data']['url'] ), PATHINFO_EXTENSION );
			$file         = $temp_path_assets . DIRECTORY_SEPARATOR . $filename;
			$get_file_url = $item['data']['url'];

			// GET and SAVE asset.
			$put_contents = $wp_filesystem->put_contents( $file, $wp_filesystem->get_contents( $get_file_url ), FS_CHMOD_FILE );

			$preset_need_image[ $id ]['data']['filename'] = $filename;
		}

		$export_data['assets'] = $preset_need_image;

		$complete_path = $temp_path . DIRECTORY_SEPARATOR . 'import-data.txt';

		$export_data_serialized = serialize( $export_data );

		$put_contents = $wp_filesystem->put_contents( $complete_path, $export_data_serialized, FS_CHMOD_FILE );

		if ( ! $put_contents ) {
			return new \WP_Error( '502', sprintf( 'Cannot create file "%s".', $complete_path ) );
		}

		// Create temporary .zip file.
		$zip_archive_filename = $export_data['preset_name'] . '.zip';

		$zip_archive = new \ZipArchive();

		$zip_complete_path = $this->get_temp_dir() . DIRECTORY_SEPARATOR . $zip_archive_filename;

		$ret = $zip_archive->open( $zip_complete_path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE );

		if ( $ret !== true ) {
			return new \WP_Error( '502', sprintf( 'Cannot create ZipArchive %d', $ret ) );
		} else {

			foreach (
				new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $temp_path ), \RecursiveIteratorIterator::SELF_FIRST )
				as $file_path => $file_obj
			) {
				// Ignore . | .. folders.
				if ( preg_match( '~/[.]{1,2}$~', $file_path ) ) {
					continue;
				}

				$file_rel_path = str_replace( $temp_path . DIRECTORY_SEPARATOR, '', $file_path );
				//$file_rel_path = basename( $temp_path ) . DIRECTORY_SEPARATOR . $file_rel_path;

				if ( is_dir( $file_path ) ) {
					$zip_archive->addEmptyDir( $file_rel_path );
				} elseif ( is_file( $file_path ) ) {
					$zip_archive->addFile( $file_path, $file_rel_path );
				}
			}

			$zip_archive->close();
		}

		$this->send_file_headers( $zip_archive_filename, filesize( $zip_complete_path ) );

		@ob_end_flush();

		@readfile( $zip_complete_path );

		unlink( $zip_complete_path );  // Delete Zip Archive.

		$this->delete_folder( $temp_path ); // Recursive delete temp directory (folder) with all files.

		die;
	}


	/**
	 * Generate preset data depend on arguments
	 *
	 * @param array       $args          {
	 *
	 * @type string|array $ids           Wait list of post_id in array
	 * @type string       $type          Type of preset. Wait 'page', 'pages', 'taxonomy_posts', 'site_preset'
	 * @type array        $plugins       List of plugins
	 * @type string       $preset_name   Name of current preset
	 * @type array        $assets_regexp Search assets by this regexp strings
	 * @type array        $content_args  params about content author, pubdate, status and other
	 *
	 * }
	 *
	 * @return array
	 *
	 */
	public function create( $args ) {

		$args_defaults = array(
			'posts'                 => array(),
			'preset_type'           => 'gm_menu_block',
			'preset_add_post_types' => array(),
			'parent_nav_menu'       => 0,
			'plugins'               => array(),
			'preset_name'           => 'groovy_menu_blocks__',
			'assets_regexp'         => array(),
			'content_args'          => array(),
			'site'                  => GROOVY_MENU_SITE_URI,
		);

		$args = wp_parse_args( $args, $args_defaults );

		$preset_data = array(
			'preset_name' => $args['preset_name'],
			'preset_type' => $args['preset_type'],
		);

		$preset_data = wp_parse_args( $args, $this->preset_default );

		global $wpdb, $post;

		$content_args_defaults = array(
			'author'     => false,
			'category'   => false,
			'start_date' => false,
			'end_date'   => false,
			'status'     => 'publish',
		);

		// TODO not used yet...
		$args['content_args'] = wp_parse_args( $args['content_args'], $content_args_defaults );

		$preset_posts = array();

		foreach ( $args['posts'] as $post_type => $post_ids ) {

			$preset_posts[ $post_type ] = array();

			if ( is_array( $post_ids ) ) {
				$post_ids = implode( ',', $post_ids );
			} else {
				// get all $post_type posts.
			}

			$posts_query_args = array(
				'post_type'      => $post_type,
				'post_status'    => $args['content_args']['status'],
				'include'        => $post_ids,
				'posts_per_page' => - 1,
			);

			$posts_data = get_posts( $posts_query_args );

			if ( empty( $posts_data ) ) {
				continue;
			}

			foreach ( $posts_data as $data ) {

				$post_id = $data->ID;

				$post_meta = $this->get_post_meta_by_post_id( $post_id, $post_type, true );

				$preset_posts[ $post_type ][ $post_id ] = array(
					'ID'              => $post_id,
					'post_parent'     => $data->post_parent,
					'post_type'       => $data->post_type,
					'post_date'       => $data->post_date,
					'post_title'      => $data->post_title,
					'post_name'       => $data->post_name,
					'post_content'    => $data->post_content,
					'post_excerpt'    => $data->post_excerpt,
					'guid'            => $data->guid,
					'comment_count'   => $data->comment_count,
					'POST_TAXONOMIES' => array(),
					'POST_META'       => $post_meta,
					'POST_COMMENT'    => array(),
				);

				$preset_data['preset_name'] = 'groovy-menu-blocks__id' . $post_id;

			}
		}

		$preset_data['posts'] = $preset_posts;


		return $preset_data;

	}


	/**
	 * Get all allowed meta by post id
	 *
	 * @param        $post_id
	 * @param string $post_type
	 * @param bool   $single
	 *
	 * @return array|bool
	 */
	public function get_post_meta_by_post_id( $post_id, $post_type = 'page', $single = false ) {
		$post_meta = false;

		$meta_values = get_post_meta( $post_id, '', $single );

		if ( $meta_values ) {

			$post_meta = array();

			foreach ( $meta_values as $meta_key => $meta_val ) {

				if ( $this->is_meta_key_allowed( $post_type, $meta_key ) && ! empty( $meta_val ) ) {

					if ( $single && is_array( $meta_val ) ) {

						$meta_val = array_pop( $meta_val );

					}

					$post_meta[ $meta_key ] = $meta_val;

				}
			}
		}

		return $post_meta ? : false;

	}


	public function is_meta_key_allowed( $post_type, $meta_key ) {
		$is_allowed = false;

		$allowed_meta_keys = array(
			'groovy_menu_preset' => array(
				'_wp_attached_file'       => true,
				'_wp_attachment_metadata' => true,
				'_thumbnail_id'           => true,
				'gm_preset_settings'      => true,
				'gm_preset_preview'       => true,
				'gm_preset_thumb'         => true,
				'gm_compiled_css'         => true,
				'gm_direction'            => true,
				'gm_version'              => true,
				'gm_old_id'               => true,
			),
		);


		if ( empty( $allowed_meta_keys[ $post_type ] ) ) {
			return true;
		}

		if ( isset( $allowed_meta_keys[ $post_type ][ $meta_key ] ) && $allowed_meta_keys[ $post_type ][ $meta_key ] ) {
			$is_allowed = true;
		}


		return $is_allowed;
	}


	public function get_temp_dir( $subdir = '' ) {
		$upload_dir = wp_get_upload_dir();
		$_cpath     = trailingslashit( $upload_dir['basedir'] );
		$_tmppath   = $_cpath . self::TEMP_FILES_DIR . DIRECTORY_SEPARATOR . $subdir;

		return str_replace( array(
			'\\',
			'/',
		), DIRECTORY_SEPARATOR, $_tmppath );
	}

	public function create_folder( $folder ) {
		if ( empty( $folder ) || ! is_string( $folder ) ) {
			return;
		}

		$folder = str_replace( array(
			'\\',
			'/',
		), DIRECTORY_SEPARATOR, $folder );

		if ( ! is_dir( $folder ) && $this->get_temp_dir() === substr( $folder, 0, strlen( $this->get_temp_dir() ) ) ) {
			wp_mkdir_p( $folder );
		}
	}

	function delete_folder( $folder ) {
		if ( empty( $folder ) || ! is_string( $folder ) ) {
			return;
		}

		$dir = str_replace( array(
			'\\',
			'/',
		), DIRECTORY_SEPARATOR, $folder );

		if ( is_dir( $dir ) && $this->get_temp_dir() === substr( $folder, 0, strlen( $this->get_temp_dir() ) ) ) {
			$objects = scandir( $dir );
			foreach ( $objects as $object ) {
				if ( $object !== "." && $object !== ".." ) {
					if ( is_dir( $dir . DIRECTORY_SEPARATOR . $object ) && ! is_link( $dir . DIRECTORY_SEPARATOR . $object ) ) {
						$this->delete_folder( $dir . DIRECTORY_SEPARATOR . $object );
					} else {
						unlink( $dir . DIRECTORY_SEPARATOR . $object );
					}
				}
			}
			rmdir( $dir );
		}
	}

	/**
	 * Send file headers.
	 * Set the file header when export to a file.
	 *
	 * @since  2.5.0
	 * @access private
	 *
	 * @param string $file_name File name.
	 * @param int    $file_size File size.
	 */
	private function send_file_headers( $file_name, $file_size ) {
		header( 'Content-Type: application/octet-stream' );
		header( 'Content-Disposition: attachment; filename=' . $file_name );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate' );
		header( 'Pragma: public' );
		header( 'Content-Length: ' . $file_size );
	}

}
