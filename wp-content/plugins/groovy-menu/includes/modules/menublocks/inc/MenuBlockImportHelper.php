<?php

namespace GroovyMenu;

use \GroovyMenuRoleCapabilities as GroovyMenuRoleCapabilities;
use \GroovyMenuUtils as GroovyMenuUtils;

defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Class MenuBlockImportHelper
 */
class MenuBlockImportHelper {

	/**
	 * Groovy Menu temporary files folder.
	 */
	const TEMP_FILES_DIR = 'groovy/tmp';


	public function __construct() {

		if ( is_admin() ) {
			add_action( 'wp_ajax_gm_import_menu_block_from_zip_url', array( $this, 'gm_import_by_ajax' ) );
			add_action( 'admin_head', array( $this, 'notice_start' ), 7 );
		}

	}

	/**
	 * Init import notice.
	 */
	public function notice_start() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( ! empty( $screen->id ) && 'edit-gm_menu_block' === $screen->id ) {
			add_action( 'admin_notices', array( $this, 'show_import_notice' ), 23 );
		}
	}

	/**
	 * Show notice for start import.
	 */
	public function show_import_notice() {
		$gm_nonce = wp_create_nonce( 'gm_nonce_import_menu_block' );

		?>
		<div id="gm-import-notice" class="gm-msg-box gm-import-notice is-dismissible">
			<div class="gm-msg-body-block">
				<button class="gm-import-notice-btn"><?php esc_html_e( 'Import Menu Block', 'groovy-menu' ); ?></button>
			</div>
			<input type="hidden" id="gm-nonce-import-menu-block-field" name="gm_nonce"
				value="<?php echo esc_attr( $gm_nonce ); ?>">
		</div>
		<?php
	}


	/**
	 * Ajax import for Menu Block
	 */
	public function gm_import_by_ajax() {
		if ( ! isset( $_POST['gm_nonce'] ) || ! wp_verify_nonce( $_POST['gm_nonce'], 'gm_nonce_import_menu_block' ) ) {
			// Send a JSON response back to an AJAX request, and die().
			wp_send_json_error( esc_html__( 'Fail. Nonce field outdated. Try reload page.', 'groovy-menu' ) );
		}

		$cap_can = GroovyMenuRoleCapabilities::blockEdit( true );

		if ( $cap_can && defined( 'DOING_AJAX' ) && DOING_AJAX && ! empty( $_POST ) && isset( $_POST['action'] ) && $_POST['action'] === 'gm_import_menu_block_from_zip_url' ) {

			$zip_url = isset( $_POST['zipUrl'] ) ? esc_url( wp_unslash( $_POST['zipUrl'] ) ) : '';

			if ( ! empty( $zip_url ) ) {

				$import_work = $this->gm_import_menu_block_from_zip_url( $zip_url );

				// Send a JSON response back to an AJAX request, and die().
				wp_send_json_success( $import_work );
			}

			// Send by default if no one imported.
			// Send a JSON response back to an AJAX request, and die().
			wp_send_json_error( esc_html__( 'Error. ZIP Archive error', 'groovy-menu' ) );

		}

	}


	/**
	 * @param string $zip_url
	 *
	 * @return bool
	 */
	public function gm_import_menu_block_from_zip_url( $zip_url = '' ) {
		if ( empty( $zip_url ) ) {
			return false;
		}

		// Create temp path if it doesn't exist.
		$this->create_folder( $this->get_temp_dir() );

		$this->prepare_zip_package( $zip_url );
		$preset_data = $this->get_preset_data();

		if ( ! empty( $preset_data['assets'] ) ) {
			$preset_assets = $this->upload_preset_assets( $preset_data['assets'] );

			if ( ! empty( $preset_assets ) ) {
				$preset_data['assets'] = $preset_assets;
			}

			$preset_data['posts'] = $this->replace_assets_patterns( $preset_data );
		}

		// IMPORT posts.
		$this->import_posts_from_preset( $preset_data['posts'] );

		if ( is_dir( $this->get_temp_dir( 'download' ) ) ) {
			$this->delete_folder( $this->get_temp_dir( 'download' ) );
		}

		return true;
	}


	/**
	 * Process and import posts with post types
	 */
	public function import_posts_from_preset( $posts ) {

		// First, sorting the required types of posts.
		$pre_post_import = array(
			'mc4wp-form'         => array(),
			'wpcf7_contact_form' => array(),
		);
		foreach ( $pre_post_import as $type => $data ) {
			if ( array_key_exists( $type, $posts ) ) {
				$_posts_to_import          = array();
				$_posts_to_import[ $type ] = $posts[ $type ];

				foreach ( $posts[ $type ] as $export_post_id => $export_post ) {
					$pre_post_import[ $type ][ $export_post_id ]['export_post_id'] = $export_post['ID'];
				}

				unset( $posts[ $type ] );
				$posts = array_merge( $_posts_to_import, $posts );
				unset( $_posts_to_import );
			} else {
				unset( $pre_post_import[ $type ] );
			}
		}

		// Processing all post types and his posts $pre_post_import.
		foreach ( $posts as $post_type => $post_type_data ) {

			$timer_shift = count( $post_type_data ) + 5;

			foreach ( $post_type_data as $export_post_id => $export_post ) {

				// Prevent doubled post_name.
				if ( $this->post_exists_by_post_name( $export_post['post_name'], $export_post['post_type'] ) ) {
					//$this->store_import_info( 'We skip adding a new post, because this already exists [name:' . $export_post['post_name'] . ']' . ', [post_type:' . $export_post['post_type'] . ']' );
					//continue; // TODO debug ---.
				}

				// Try unserialize and JSON-ed meta value before insert to post.
				if ( ! empty( $export_post['POST_META'] ) ) {
					foreach ( $export_post['POST_META'] as $meta_index => $meta_data ) {
						if ( is_serialized( $meta_data ) ) {
							$export_post['POST_META'][ $meta_index ] = maybe_unserialize( $meta_data );
						}

						if ( is_string( $meta_data ) && ! empty( json_decode( $meta_data, true ) ) ) {
							$export_post['POST_META'][ $meta_index ] = wp_slash( wp_json_encode( json_decode( $meta_data, true ) ) );
						}
					}
				}

				// Prepare content for new ids from.
				if ( ! empty( $pre_post_import ) && ! array_key_exists( $post_type, $pre_post_import ) ) {

					foreach ( $pre_post_import as $pre_post_type => $pre_posts ) {
						if ( empty( $pre_posts ) ) {
							continue;
						}

						foreach ( $pre_posts as $pre_post_id => $pre_post_data ) {

							if ( empty( $pre_post_data['new_post_id'] ) ) {
								continue;
							}

							$_post_content  = $export_post['post_content'];
							$_post_meta     = $export_post['POST_META'];
							$post_meta_flag = 0;

							switch ( $pre_post_type ) {

								case 'mc4wp-form':
									// Example: [mc4wp_form id="OLD_ID"].
									$pattern       = '#\[mc4wp_form id="(\d+)"#im';
									$replacement   = '[mc4wp_form id="' . $pre_post_data['new_post_id'] . '"';
									$_post_content = preg_replace( $pattern, $replacement, $_post_content );

									update_option( 'mc4wp_default_form_id', $pre_post_data['new_post_id'] );

									break;

								case 'wpcf7_contact_form':
									//  Example: [contact-form-7 id="OLD_ID"].
									$pattern       = '#\[contact\-form\-7 id="(\d+)"#im';
									$replacement   = '[contact-form-7 id="' . $pre_post_data['new_post_id'] . '"';
									$_post_content = preg_replace( $pattern, $replacement, $_post_content );
									break;
							}

							if ( ! empty( $_post_content ) && $_post_content !== $export_post['post_content'] ) {
								$export_post['post_content'] = $_post_content;
							}
							if ( $post_meta_flag > 0 && ! empty( $_post_meta ) ) {
								$export_post['POST_META'] = $_post_meta;
							}

						}
					}

				}

				$timer_shift --;
				$post_date = date( 'Y-m-d H:i:s', intval( current_time( 'timestamp' ) ) - $timer_shift );

				$new_post_args = [
					'post_author'  => get_current_user_id(),
					'post_content' => $export_post['post_content'],
					'post_excerpt' => $export_post['post_excerpt'],
					'post_name'    => $export_post['post_name'],
					'post_parent'  => $export_post['post_parent'],
					'post_status'  => 'publish',
					//  Example: 'draft' | 'publish' | 'pending'| 'future' | 'private'.
					'post_title'   => $export_post['post_title'],
					'post_type'    => $export_post['post_type'],
					//'post_category'  => array( "<category id>, <...>" ),
					//'tags_input'     => array('<tag>, <tag>, <...>'), // waiting tag slug.
					//'tax_input'    => array( 'taxonomy_name' => array( 'term', 'term2', 'term3' ) ), // waiting id for terms.
					'meta_input'   => $export_post['POST_META'],
					'post_date'    => $post_date
				];


				// Inset post.
				$new_post_id = wp_insert_post( $new_post_args );


				// Store new post ID for $pre_post_import.
				if ( array_key_exists( $post_type, $pre_post_import ) ) {
					$pre_post_import[ $post_type ][ $export_post_id ]['new_post_id'] = $new_post_id;
				}


				// Implements Taxonomies.
				if ( ! empty( $export_post['POST_TAXONOMIES'] ) ) {

					$search_tax_args      = array(
						'public'   => true,
						'_builtin' => true,
					);
					$registred_taxonomies = get_taxonomies( $search_tax_args, 'names', 'or' );

					$post_taxonomies = array(
						'taxonomies' => array(),
						'terms_meta' => array(),
					);

					foreach ( $export_post['POST_TAXONOMIES'] as $post_tax_name => $post_tax_data ) {

						// Do not work with not registered taxonomies.
						if ( empty( $registred_taxonomies[ $post_tax_name ] ) ) {
							continue;
						}

						foreach ( $post_tax_data['post_terms'] as $post_term_name => $post_term_data ) {

							if ( empty( $post_term_data['slug'] ) ) {
								continue;
							}


							$searched_term = term_exists( $post_term_data['slug'], $post_tax_name );


							$current_term_id          = null;
							$current_term_update_meta = false;


							if ( ! is_wp_error( $searched_term ) && ! empty( $searched_term['term_id'] ) ) {
								$current_term_id = intval( $searched_term['term_id'] );
							} else {

								$insert_term_data = wp_insert_term(
									$post_term_data['name'],
									$post_tax_name,
									array(
										'description' => $post_term_data['description'],
										'slug'        => $post_term_data['slug'],
									)
								);

								if ( ! is_wp_error( $insert_term_data ) && ! empty( $insert_term_data['term_id'] ) ) {
									$current_term_id          = intval( $insert_term_data['term_id'] );
									$current_term_update_meta = true;
								}
							}


							if ( ! empty( $current_term_id ) ) {
								$post_taxonomies['taxonomies'][ $post_tax_name ][] = $current_term_id;
								if ( $current_term_update_meta && ! empty( $post_term_data['TERM_META_DATA'] ) ) {
									$post_taxonomies['terms_meta'][ $post_tax_name ][ $current_term_id ] = $post_term_data['TERM_META_DATA'];
								}
							}

							// TAXONOMIES implement.
							if ( ! empty( $post_taxonomies['taxonomies'] ) && $new_post_id ) {
								foreach ( $post_taxonomies['taxonomies'] as $tax_name => $terms ) {

									$set_terms = wp_set_post_terms( $new_post_id, $terms, $tax_name );

									foreach ( $terms as $term_id ) {
										if ( ! empty( $post_taxonomies['terms_meta'][ $tax_name ][ $term_id ] ) ) {

											foreach ( $post_taxonomies['terms_meta'][ $tax_name ][ $term_id ] as $tag_meta_key => $tag_meta_value ) {

												add_term_meta( $term_id, $tag_meta_key, $tag_meta_value, true );

											}
										}
									}
								}
							}
						}
					}
				} // end of [if POST_TAXONOMIES].


				// implement Comments.
				if ( ! empty( $export_post['POST_COMMENT'] ) && $new_post_id ) {

					foreach ( $export_post['POST_COMMENT'] as $comment ) {

						$comment_args = array(
							'comment_post_ID'      => $new_post_id,
							'comment_author'       => $comment['comment_author'],
							'comment_author_email' => $comment['comment_author_email'],
							'comment_author_url'   => $comment['comment_author_url'],
							'comment_content'      => $comment['comment_content'],
							'comment_type'         => '',
							'comment_parent'       => 0, // Not implemented yet
							'comment_date'         => $comment['comment_date'],
							'comment_approved'     => $comment['comment_approved'],
						);

						wp_insert_comment( wp_slash( $comment_args ) );
					}

				}


				//$this->store_import_info( 'Adding new post ' . ' [id:' . $new_post_id . ']' . ', [name:' . $export_post['post_name'] . ']' . ', [post_type:' . $export_post['post_type'] . ']' );


			}
		}

	}


	/**
	 * Read export files
	 *
	 * @param string $file_ext
	 *
	 * @param bool   $unserialize
	 *
	 * @return mixed
	 */
	function get_preset_data( $file_name = 'import-data.txt', $unserialize = true ) {
		$presets_data = array();
		$file         = $this->get_temp_dir( 'download/' . $file_name );

		// Check file if exist.
		if ( ! file_exists( $file ) ) {
			//$this->store_import_info_error( 'Error: preset ' . $preset_name . ' data file "' . $file_name . '" not exists' );

			return array();
		}

		// Init WP FileSystem
		if ( ! defined( 'FS_METHOD' ) ) {
			define( 'FS_METHOD', 'direct' );
		}

		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			if ( file_exists( ABSPATH . '/wp-admin/includes/file.php' ) ) {
				require_once ABSPATH . '/wp-admin/includes/file.php';
				WP_Filesystem();
			}
		}
		if ( empty( $wp_filesystem ) ) {
			return array();
		}

		// Get Content from file
		$file_content = $wp_filesystem->get_contents( $file );

		$presets_data = $unserialize ? @unserialize( $file_content ) : $file_content;

		return $presets_data;
	}


	/**
	 * Replace assets in the content by pattern.
	 *
	 * @param $preset_data
	 *
	 * @return mixed
	 */
	public function replace_assets_patterns( $preset_data ) {

		$search_pattern  = array();
		$replace_pattern = array();

		foreach ( $preset_data['assets'] as $export_id => $asset_info ) {

			if ( empty( $asset_info['pattern'] ) ) {
				continue;
			}

			foreach ( $asset_info['pattern'] as $export_type => $elements ) {
				foreach ( $elements as $el_num => $one_pattern ) {

					if ( empty( $one_pattern ) ) {
						continue;
					}

					$search_pattern[] = $one_pattern;

					if ( ! empty( $asset_info['data']['import_id'] ) ) {

						if ( 'ASSET_URL' === $export_type ) {
							$replace_pattern[] = $asset_info['data']['import_url'];
						} else {
							$replace_pattern[] = $asset_info['data']['import_id'];
						}

					} else {
						$replace_pattern[] = $export_id;
					}

				}
			}
		}

		if ( ! empty( $preset_data['site'] ) && esc_url( $preset_data['site'] ) ) {
			$search_pattern[]  = rawurlencode( esc_url( $preset_data['site'] ) );
			$replace_pattern[] = rawurlencode( GROOVY_MENU_SITE_URI );

			$search_pattern[]  = esc_url( $preset_data['site'] );
			$replace_pattern[] = GROOVY_MENU_SITE_URI;

			$search_pattern[]  = str_replace( '/', '\/', esc_url( $preset_data['site'] ) );
			$replace_pattern[] = str_replace( '/', '\/', GROOVY_MENU_SITE_URI );
		}

		if ( ! empty( $search_pattern ) && ! empty( $replace_pattern ) ) {
			// REPLACE assets patterns.
			$preset_data['posts'] = json_decode(
				str_replace(
					$search_pattern,
					$replace_pattern,
					json_encode( $preset_data['posts'] )
				),
				true
			);
		}

		return $preset_data['posts'];

	}

	/**
	 *   Determine if a post exists based on post_name and post_type
	 *
	 * @param $post_name string unique post name
	 * @param $post_type string post type (defaults to 'post')
	 *
	 * @return null|string
	 */
	public function post_exists_by_post_name( $post_name, $post_type = 'post' ) {
		global $wpdb;

		$query = "SELECT ID FROM $wpdb->posts WHERE 1=1";
		$args  = array();

		if ( ! empty ( $post_name ) ) {
			$query  .= " AND post_name LIKE '%s' ";
			$args[] = $post_name;
		}
		if ( ! empty ( $post_type ) ) {
			$query  .= " AND post_type = '%s' ";
			$args[] = $post_type;
		}

		if ( ! empty ( $args ) ) {
			return $wpdb->get_var( $wpdb->prepare( $query, $args ) );
		}

		return false;
	}

	/**
	 * Get attachment data by filename
	 *
	 * @param $filename
	 *
	 * @return mixed
	 */
	public function get_attachment_by_grooni_meta( $filename ) {
		$args           = array(
			'posts_per_page' => 1,
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'meta_key'       => '_grooni_import_asset_name',
			'meta_value'     => trim( $filename ),
		);
		$get_attachment = new \WP_Query( $args );

		if ( ! empty( $get_attachment ) && isset( $get_attachment->posts[0] ) && $get_attachment->posts[0] ) {
			return $get_attachment->posts[0];
		} else {
			return false;
		}
	}

	/**
	 * Upload attachments (assets) from preset data to upload folder and WP media library
	 *
	 * @param $preset_assets
	 *
	 * @return array
	 */
	function upload_preset_assets( $preset_assets ) {

		if ( empty( $preset_assets ) || ! is_array( $preset_assets ) ) {
			return array();
		}

		// Gives us access to the download_url() and wp_handle_sideload() functions
		require_once( ABSPATH . 'wp-admin/includes/file.php' );

		foreach ( $preset_assets as $export_id => $asset_info ) {

			// Check, if this asset imported before
			$exist_attachment = $this->get_attachment_by_grooni_meta( $asset_info['data']['filename'] );
			if ( ! empty( $exist_attachment->ID ) ) {

				$exist_parsed = parse_url( wp_get_attachment_url( $exist_attachment->ID ) );
				$exist_url    = dirname( $exist_parsed['path'] ) . DIRECTORY_SEPARATOR . rawurlencode( basename( $exist_parsed['path'] ) );

				// Set new imported asset id
				$preset_assets[ $export_id ]['data']['import_id']  = $exist_attachment->ID;
				$preset_assets[ $export_id ]['data']['import_url'] = $exist_url;

				//$this->store_import_info( 'Asset ' . $asset_info['data']['filename'] . ' exist with ID:' . $exist_attachment->ID . ' . Used this asset.' );
				// Skip to next asset
				continue;
			}

			// ok, it's new asset, and it must be import as new
			$tmp_asset_file = $this->get_temp_dir( 'download/assets/' . $asset_info['data']['filename'] );

			if ( is_file( $tmp_asset_file ) ) {

				$wp_filetype = wp_check_filetype( $tmp_asset_file, null );

				// preload file params
				$file_params = array(
					'name'     => $asset_info['data']['filename'],
					'type'     => $wp_filetype['type'],
					'tmp_name' => $tmp_asset_file,
					'error'    => 0,
					'size'     => filesize( $tmp_asset_file ),
				);

				$overrides = array(
					'test_form'   => false,
					'test_size'   => false,
					'test_upload' => true,
				);


				// move temp asset to wp uploads
				$load_results = wp_handle_sideload( $file_params, $overrides );


				if ( ! empty( $load_results['error'] ) ) {

					// TODO add error handler

				} else {

					$attachment = array(
						'post_mime_type' => $load_results['type'],
						'post_title'     => basename( $load_results['file'] ),
						'post_content'   => '',
						'post_status'    => 'inherit'
					);

					$imported_attach_id = wp_insert_attachment( $attachment, $load_results['file'] );

					$image_new     = get_post( $imported_attach_id );
					$fullsize_path = get_attached_file( $image_new->ID );
					$attach_data   = wp_generate_attachment_metadata( $imported_attach_id, $fullsize_path );
					wp_update_attachment_metadata( $imported_attach_id, $attach_data );
					update_post_meta( $imported_attach_id, '_grooni_import_asset_name', $asset_info['data']['filename'] );

					// Set new imported asset id
					$preset_assets[ $export_id ]['data']['import_id']  = $imported_attach_id;
					$preset_assets[ $export_id ]['data']['import_url'] = $load_results['url'];

					//$this->store_import_info( 'Asset imported with new id [' . $imported_attach_id . ']: ' . basename( $load_results['file'] ) );
				}


			}

		}

		return $preset_assets;

	}

	/**
	 * Download and unpackage preset by name
	 *
	 * @param $preset_name
	 */
	public function prepare_zip_package( $zip_url ) {

		if ( empty( $zip_url ) ) {
			//$this->store_import_info( 'Set empty preset', '', 'alert' );

			@ob_clean();
			wp_send_json( array(
				'status'  => 'alert',
				'message' => 'Set empty preset'
			) );
		}


		if ( ! $this->check_writeable() ) {

			//$this->store_import_info( sprintf( 'Could not write preset files into directory: %s', str_replace( '\\', '/', $this->get_assets_data( 'content_path' ) ) ), '', 'critical_error' );

			@ob_clean();
			wp_send_json( array(
				'status'  => 'critical_error',
				'message' => sprintf( __( 'Could not write ZIP file into directory: %s . Check folder, we need write permission.', 'groovy-menu' ), str_replace( '\\', '/', $this->get_temp_dir() ) )
			), 500 );

		}

		$this->check_limits();

		@ob_implicit_flush();

		delete_option( 'grooni_addons_download_tmp_package' );

		// Start download assets
		$this->download_package( $zip_url );

		@ob_flush();
		@flush();
	}

	public function check_limits() {
		@ini_set( 'max_execution_time', 2400 );
		@ini_set( 'output_buffering', 'on' );
		@ini_set( 'zlib.output_compression', 0 );
		@ini_set( 'memory_limit', apply_filters( 'admin_memory_limit', WP_MAX_MEMORY_LIMIT ) );
	}

	function check_writeable() {

		ob_start();

		$passed = true;

		if ( ! is_writable( $this->get_temp_dir() ) ) {
			$passed = false;
		}

		$notice = ob_get_contents();
		ob_end_clean();

		if ( $passed === false ) {
			print ( $notice );
		}

		return $passed;
	}

	/**
	 * Download the media package
	 *
	 * @param string $zip_url
	 */
	function download_package( string $zip_url ) {

		if ( ! defined( 'FS_METHOD' ) ) {
			define( 'FS_METHOD', 'direct' );
		}

		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			if ( file_exists( ABSPATH . '/wp-admin/includes/file.php' ) ) {
				require_once ABSPATH . '/wp-admin/includes/file.php';
				WP_Filesystem();
			}
		}
		if ( empty( $wp_filesystem ) ) {
			//$this->store_import_info( 'WP_Filesystem() load library error', '', 'critical_error' );

			@ob_clean();

			wp_send_json( array(
				'status'  => 'critical_error',
				'message' => esc_html__( 'WP_Filesystem() load library error', 'groovy-menu' )
			), 500 );
		}


		$package = null;

		if ( empty( $zip_url ) ) {
			//$this->store_import_info( 'Can not download assets. URL not set by theme config.', '', 'critical_error' );
			@ob_clean();
			wp_send_json( array(
				'status'  => 'critical_error',
				'message' => esc_html__( 'Can not download ZIP package.', 'groovy-menu' )
			), 500 );
		}

		//$this->store_import_info( 'Downloading assets package...', '0' );

		// create temp folder
		$_tmp = wp_tempnam( $zip_url );
		@unlink( $_tmp );
		@ob_flush();

		$package = download_url( $zip_url, 18000 );

		if ( is_dir( $this->get_temp_dir( 'download' ) ) ) {
			$this->delete_folder( $this->get_temp_dir( 'download' ) );
		}

		$this->create_folder( $this->get_temp_dir( 'download' ) );

		if ( ! is_wp_error( $package ) || ! is_dir( $this->get_temp_dir( 'download' ) ) ) {

			$unzip = unzip_file( $package, $this->get_temp_dir( 'download' ) );

			if ( is_wp_error( $unzip ) ) {

				//$this->store_import_info( sprintf( 'ERROR %s. Could not extract demo media package. Please contact our support staff.', $unzip->get_error_code() ), '', 'critical_error' );

				@ob_clean();
				wp_send_json( array(
					'status'  => 'critical_error',
					'message' => sprintf( __( 'ERROR %s. Could not extract demo media package. Please contact our support staff.', 'groovy-menu' ), $unzip->get_error_code() )
				), 500 );

			}

			@unlink( $package );

			//$this->store_import_info( 'The preset package is downloaded.' );

		} else {

			@ob_clean();

			//$this->store_import_info( sprintf( 'ERROR %s. Demo media package is not download. Please contact our support staff.', $package->get_error_code() ), '', 'critical_error' );

			wp_send_json( array(
				'status'  => 'critical_error',
				'message' => sprintf( __( 'ERROR %s. Demo media package is not download. Please contact our support staff.', 'groovy-menu' ), $package->get_error_code() ),
				'data'    => array(
					'wp_doing_ajax' => wp_doing_ajax(),
				)
			), 500 );

		}

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

}
