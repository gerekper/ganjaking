<?php
/**
 * Process Imports Theme Templates JSON
 *
 * @param mixed $json_content Json content of theme template.
 * @return void.
 */
function seedprod_pro_theme_import_json( $json_content = null ) {

	$full_code = $json_content;

	$theme = $full_code->theme;

	$shortcode_update = $full_code->mapped;

	$old_home_url = $full_code->current_home_url;
	$new_home_url = home_url();

	$imports = array();
	if ( count( $theme ) > 0 ) {
		foreach ( $theme as $k => $v ) {
			$imports[] = array(
				'post_content'          => base64_decode( $v->post_content ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
				'post_content_filtered' => base64_decode( $v->post_content_filtered ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
				'post_title'            => base64_decode( $v->post_title ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
				'meta'                  => json_decode( base64_decode( $v->meta ) ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
				'order'                 => $v->order,
			);
		}
	}

	$shortcode_array = array();
	if ( count( $shortcode_update ) > 0 ) {
		foreach ( $shortcode_update as $k => $t ) {
			$shortcode_array[] = array(
				'shortcode'  => base64_decode( $t->shortcode ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
				'page_title' => $t->page_title,
			);
		}
	}

	$import_page_array = array();
	if ( count( $imports ) > 0 ) {
		foreach ( $imports as $k1 => $v1 ) {

			$meta = $v1['meta'];

			$data = array(
				'comment_status' => 'closed',
				'menu_order'     => $v1['order'],
				'ping_status'    => 'closed',
				'post_status'    => 'publish',
				'post_title'     => $v1['post_title'],
				'post_type'      => 'seedprod',
				'meta_input'     => array(
					'_seedprod_page'               => true,
					'_seedprod_is_theme_template'  => true,
					'_seedprod_page_uuid'          => wp_generate_uuid4(),
					'_seedprod_page_template_type' => $meta->_seedprod_page_template_type[0],
				),
			);

			$id = wp_insert_post(
				$data,
				true
			);

			$import_page_array[] = array(
				'id'                    => $id,
				'title'                 => $v1['post_title'],
				'post_content'          => $v1['post_content'],
				'post_content_filtered' => $v1['post_content_filtered'],
			);

			// reinsert settings because wp_insert screws up json.
			$post_content_filtered = $v1['post_content_filtered'];
			$post_content          = $v1['post_content'];
			global $wpdb;
			$tablename     = $wpdb->prefix . 'posts';
			$sql           = "UPDATE $tablename SET post_content_filtered = %s,post_content = %s WHERE id = %d";
			$safe_sql      = $wpdb->prepare( $sql, $post_content_filtered, $post_content, $id ); // phpcs:ignore 
			$update_result = $wpdb->get_var( $safe_sql ); // phpcs:ignore

			// add meta.
			if ( 'css' === $meta->_seedprod_page_template_type[0] ) {
				// set css file.
				// find and replace url.
				$css         = str_replace( $old_home_url, $new_home_url, $v1['post_content'] );
				$css         = str_replace( 'seedprod-themes-exports', 'seedprod-themes-imports', $css );
				$custom_css  = $meta->_seedprod_custom_css[0];
				$custom_css  = '';
				$builder_css = $meta->_seedprod_builder_css[0];

				update_post_meta( $id, '_seedprod_css', $css );
				update_post_meta( $id, '_seedprod_custom_css', $custom_css );
				update_post_meta( $id, '_seedprod_builder_css', $builder_css );
				update_option( 'global_css_page_id', $id );
				// generate css.
				$css = $css . $custom_css;

				// trash current css file and set css file pointer.
				$current_css_file = get_option( 'seedprod_global_css_page_id' );
				if ( ! empty( $current_css_file ) ) {
					wp_trash_post( $current_css_file );
				}

				update_option( 'seedprod_global_css_page_id', $id );
				seedprod_pro_generate_css_file( $id, $css );
			} else {
				// find and replace preview urls.
				$new_post_content = str_replace( $old_home_url, $new_home_url, $v1['post_content'] );
				$new_post_content = str_replace( 'seedprod-themes-exports', 'seedprod-themes-imports', $new_post_content );
				$code             = seedprod_pro_extract_page_css( $new_post_content, $id );
				update_post_meta( $id, '_seedprod_theme_template_condition', $meta->_seedprod_theme_template_condition[0] );
				update_post_meta( $id, '_seedprod_css', $code['css'] );
				update_post_meta( $id, '_seedprod_html', $code['html'] );
				seedprod_pro_generate_css_file( $id, $code['css'] );
				// process conditon to see if we need to create a placeholder page.
				$conditions = $meta->_seedprod_theme_template_condition[0];

				if ( ! empty( $conditions ) ) {

					$conditions = json_decode( $conditions );
					if ( is_array( $conditions ) ) {
						if ( 1 === count( $conditions ) && 'include' === $conditions[0]->condition && 'is_page(x)' === $conditions[0]->type && ! empty( $conditions[0]->value ) && ! is_numeric( $conditions[0]->value ) ) {
							// check if slug exists.
							$slug_tablename = $wpdb->prefix . 'posts';
							$sql            = "SELECT id FROM $slug_tablename WHERE post_name = %s AND post_type = 'page' AND post_status != 'trash'";
                            $safe_sql        = $wpdb->prepare($sql, $conditions[0]->value); // phpcs:ignore
                        $this_slug_exist = $wpdb->get_var($safe_sql);// phpcs:ignore
							if ( empty( $this_slug_exist ) ) {
								// create page with content
								$page_details = array(
									'post_title'   => $v1['post_title'],
									'post_name'    => $conditions[0]->value,
									'post_content' => $new_post_content,
									'post_status'  => 'publish',
									'post_type'    => 'page',
								);
								$seedprod_remove_page_template = apply_filters( 'seedprod_remove_page_template', true );
                                if ($seedprod_remove_page_template) {
                                    $new_page_id = wp_insert_post($page_details);
                                    if (!empty($new_page_id)) {
                                        // add meta
                                        update_post_meta($new_page_id, '_seedprod_edited_with_seedprod', true);
                                        // reinsert settings because wp_insert screws up json.
                                        $post_content_filtered_new_page = $v1['post_content_filtered'];
                                        global $wpdb;
                                        $tablename     = $wpdb->prefix . 'posts';
                                        $sql           = "UPDATE $tablename SET post_content_filtered = %s,post_content = %s WHERE id = %d";
                                        $safe_sql      = $wpdb->prepare($sql, $post_content_filtered_new_page, $new_post_content, $new_page_id); // phpcs:ignore
                                    $update_result_new_page = $wpdb->get_var($safe_sql); // phpcs:ignore
                                    // update import array map with new id
                                    foreach ($import_page_array as $k5 => $v5) {
                                        if ($v5['id'] == $id) {
                                            $import_page_array[$k5]['id'] = $new_page_id;
                                        }
                                    }
                                    
                                    // remove template page
                                    wp_delete_post($id, true);
                                     
                                    }
                                }else{
									// add place holder page.
									wp_insert_post($page_details);
								}

							}
						}
					}
				}
			}
		}
	}

	// find and replace shortcodes.
	if ( count( $import_page_array ) > 0 ) {
		foreach ( $import_page_array as $t => $val ) {
            if ($val['title'] != 'Global CSS') {
                $post_content          = $val['post_content'];
                $post_content_filtered = $val['post_content_filtered'];
                $post_id               = $val['id'];

                $processed_data_import = seedprod_pro_process_image_filenames_import_theme($post_content_filtered, $post_content);
                $post_content          = $processed_data_import['html'];
                $post_content_filtered = $processed_data_import['data'];

                $code             = seedprod_pro_extract_page_css($post_content, $post_id);
                update_post_meta($post_id, '_seedprod_css', $code['css']);
                update_post_meta($post_id, '_seedprod_html', $code['html']);
                seedprod_pro_generate_css_file($post_id, $code['css']);

                if (count($shortcode_array) > 0) {
                    foreach ($shortcode_array as $k => $t) {
                        $shortcode_page_title = $shortcode_array[ $k ]['page_title'];
                        $fetch_shortcode_key  = array_search($shortcode_page_title, array_column($import_page_array, 'title')); // phpcs:ignore
                        $fetch_shortcode_id   = $import_page_array[ $fetch_shortcode_key ]['id'];

                        $shortcode_page_sc = $shortcode_array[ $k ]['shortcode'];
                        $shortcode_page_sc = str_replace('[sp_template_part id="', '', $shortcode_page_sc);
                        $shortcode_page_sc = str_replace('"]', '', $shortcode_page_sc);

                        if ($fetch_shortcode_id) {
                            $shortcode_array[ $k ]['updated_shortcode'] = '[sp_template_part id="' . $fetch_shortcode_id . '"]';
                            $post_content                               = str_replace($shortcode_array[ $k ]['shortcode'], $shortcode_array[ $k ]['updated_shortcode'], $post_content);

                            $shortcode_array[ $k ]['updated_shortcode_filtered'] = '"templateparts":"' . $fetch_shortcode_id . '"';
                            $shortcode_array[ $k ]['shortcode_filtered_id']      = $shortcode_page_sc;
                            $shortcode_array[ $k ]['shortcode_filtered']         = '"templateparts":"' . $shortcode_page_sc . '"';

                            $post_content_filtered = str_replace($shortcode_array[ $k ]['shortcode_filtered'], $shortcode_array[ $k ]['updated_shortcode_filtered'], $post_content_filtered);

                            // update generated html.
                            $generate_html = get_post_meta($post_id, '_seedprod_html', true);
                            $generate_html = str_replace($shortcode_array[ $k ]['shortcode'], $shortcode_array[ $k ]['updated_shortcode'], $generate_html);
                            update_post_meta($post_id, '_seedprod_html', $generate_html);
                        }
                    }
                }

                global $wpdb;
                $tablename     = $wpdb->prefix . 'posts';
                $sql           = "UPDATE $tablename SET post_content_filtered = %s,post_content = %s WHERE id = %d";
                $safe_sql      = $wpdb->prepare($sql, $post_content_filtered, $post_content, absint($post_id)); // phpcs:ignore
            	$update_result = $wpdb->get_var($safe_sql); // phpcs:ignore
            }
		}
	}

}



/**
 * Process Imports Landing Pages JSON
 *
 * @param mixed $json_content Json content of theme template.
 * @return void.
 */
function seedprod_pro_landing_import_json( $json_content = null ) {

	$full_code = $json_content;

	$theme = $full_code->theme;

	$shortcode_update = $full_code->mapped;

	$imports = array();
	foreach ( $theme as $k => $v ) {
		$imports[] = array(
			'post_content'          => base64_decode( $v->post_content ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
			'post_content_filtered' => base64_decode( $v->post_content_filtered ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
			'post_title'            => base64_decode( $v->post_title ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
			'post_type'             => base64_decode( $v->post_type ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
			'post_status'           => base64_decode( $v->post_status ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
			'ptype'                 => base64_decode( $v->ptype ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		'meta'                      => json_decode( base64_decode( $v->meta ) ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
			'order'                 => $v->order,
		);
	}

	$shortcode_array = array();
	foreach ( $shortcode_update as $k => $t ) {
		$shortcode_array[] = array(
			'shortcode'  => base64_decode( $t->shortcode ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
			'page_title' => $t->page_title,
		);
	}

	$import_page_array = array();

	foreach ( $imports as $k1 => $v1 ) {

		$meta = $v1['meta'];
		$new_meta = array();

		// clean meta and get new meta.
		foreach($meta as $mk => $mv){
			if(substr($mk, 0, 9 ) === "_seedprod" && $mk != '_seedprod_page_uuid'){
				$new_meta[$mk] = $mv[0];
			}
		}
		$new_meta['_seedprod_page_uuid'] = wp_generate_uuid4();


		$data = array(
			'comment_status' => 'closed',
			'menu_order'     => $v1['order'],
			'ping_status'    => 'closed',
			'post_status'    => $v1['post_status'],
			'post_title'     => $v1['post_title'],
			'post_type'      => $v1['post_type'],
			'meta_input'     => $new_meta,
		);

		$id = wp_insert_post(
			$data,
			true
		);

		$csp_id    = get_option( 'seedprod_coming_soon_page_id' );
		$mmp_id    = get_option( 'seedprod_maintenance_mode_page_id' );
		$p404_id   = get_option( 'seedprod_404_page_id' );
		$loginp_id = get_option( 'seedprod_login_page_id' );

		$ptype = $v1['ptype'];
		if ( 'cs' === $ptype ) {
			if ( '' !== $csp_id ) {
				update_option( 'seedprod_coming_soon_page_id', $id );
			} else {
				add_option( 'seedprod_coming_soon_page_id', $id );
			}
		}
		if ( 'mm' === $ptype ) {
			if ( '' !== $mmp_id ) {
				update_option( 'seedprod_maintenance_mode_page_id', $id );
			} else {
				add_option( 'seedprod_maintenance_mode_page_id', $id );
			}
		}
		if ( 'p404' === $ptype ) {
			if ( '' !== $p404_id ) {
				update_option( 'seedprod_404_page_id', $id );
			} else {
				add_option( 'seedprod_404_page_id', $id );
			}
		}
		if ( 'loginp' === $ptype ) {
			if ( '' !== $loginp_id ) {
				update_option( 'seedprod_login_page_id', $id );
			} else {
				add_option( 'seedprod_login_page_id', $id );
			}
		}

		$import_page_array[] = array(
			'id'                    => $id,
			'title'                 => $v1['post_title'],
			'post_content'          => $v1['post_content'],
			'post_content_filtered' => $v1['post_content_filtered'],
		);

		// reinsert settings because wp_insert screws up json.
		$post_content_filtered = $v1['post_content_filtered'];
		$post_content          = $v1['post_content'];
		global $wpdb;
		$tablename     = $wpdb->prefix . 'posts';
		$sql           = "UPDATE $tablename SET post_content_filtered = %s,post_content = %s WHERE id = %d";
		$safe_sql      = $wpdb->prepare( $sql, $post_content_filtered, $post_content, absint( $id ) ); // phpcs:ignore 
		$update_result = $wpdb->get_var( $safe_sql ); // phpcs:ignore

	}

	// find and replace shortcodes.
	foreach ( $import_page_array as $t => $val ) {

		$post_content          = $val['post_content'];
		$post_content_filtered = $val['post_content_filtered'];
		$post_id               = $val['id'];

		$processed_data_import = seedprod_pro_process_image_filenames_import_theme( $post_content_filtered, $post_content );
		$post_content          = $processed_data_import['html'];
		$post_content_filtered = $processed_data_import['data'];

		$code             = seedprod_pro_extract_page_css( $post_content, $post_id );
		update_post_meta( $post_id, '_seedprod_css', $code['css'] );
		update_post_meta( $post_id, '_seedprod_html', $code['html'] );
		seedprod_pro_generate_css_file( $post_id, $code['css'] );

		foreach ( $shortcode_array as $k => $t ) {

			$shortcode_page_title = $shortcode_array[ $k ]['page_title'];
			$fetch_shortcode_key  = array_search( $shortcode_page_title, array_column( $import_page_array, 'title' ) ); // phpcs:ignore
			$fetch_shortcode_id   = $import_page_array[ $fetch_shortcode_key ]['id'];

			$shortcode_page_sc = $shortcode_array[ $k ]['shortcode'];
			$shortcode_page_sc = str_replace( '[sp_template_part id="', '', $shortcode_page_sc );
			$shortcode_page_sc = str_replace( '"]', '', $shortcode_page_sc );

			if ( $fetch_shortcode_id ) {
				$shortcode_array[ $k ]['updated_shortcode'] = '[sp_template_part id="' . $fetch_shortcode_id . '"]';
				$post_content                               = str_replace( $shortcode_array[ $k ]['shortcode'], $shortcode_array[ $k ]['updated_shortcode'], $post_content );

				$shortcode_array[ $k ]['updated_shortcode_filtered'] = '"templateparts":"' . $fetch_shortcode_id . '"';
				$shortcode_array[ $k ]['shortcode_filtered_id']      = $shortcode_page_sc;
				$shortcode_array[ $k ]['shortcode_filtered']         = '"templateparts":"' . $shortcode_page_sc . '"';

				$post_content_filtered = str_replace( $shortcode_array[ $k ]['shortcode_filtered'], $shortcode_array[ $k ]['updated_shortcode_filtered'], $post_content_filtered );

			}
		}

		global $wpdb;
		$tablename     = $wpdb->prefix . 'posts';
		$sql           = "UPDATE $tablename SET post_content_filtered = %s,post_content = %s WHERE id = %d";
		$safe_sql      = $wpdb->prepare( $sql, $post_content_filtered, $post_content, $post_id ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$update_result = $wpdb->get_var( $safe_sql ); // phpcs:ignore 

	}

}


if ( defined( 'DOING_AJAX' ) ) {
	add_action( 'wp_ajax_seedprod_pro_export_theme_files', 'seedprod_pro_export_theme_files' );
	add_action( 'wp_ajax_seedprod_pro_import_theme_files', 'seedprod_pro_import_theme_files' );

	add_action( 'wp_ajax_seedprod_pro_import_theme_by_url', 'seedprod_pro_import_theme_by_url' );
}

/**
 * Add svg mimes type so can add svg files into media files while importing theme data
 *
 * @param array $m Mime types.
 */
function seedprod_pro_custom_mtypes( $m ) {
	$m['svg']  = 'image/svg+xml';
	$m['svgz'] = 'image/svg+xml';
	$m['ico']  = 'image/x-icon';
	return $m;
}
add_filter( 'upload_mimes', 'seedprod_pro_custom_mtypes' );

/**
 * Download all images locally.
 *
 * @param array $img_arr Images.
 */
function seedprod_pro_save_images_locally( $img_arr ) {
	foreach ( $img_arr as $image ) {
		$file_content = wp_remote_get( $image['old_url'], array( 'sslverify' => false ) );
		if ( is_wp_error( $file_content ) ) {
			$error_code    = wp_remote_retrieve_response_code( $file_content );
			$error_message = wp_remote_retrieve_response_message( $file_content );
			wp_send_json_error( $error_message );
		}
		seedprod_pro_write_to_filesystem( $image['filename'], $file_content['body'] ); // phpcs:ignore
	}
}

/**
 * Download all images locally.
 *
 *  @param string $file_name     File name.
 * @param mixed  $file_contents Images.
 */
function seedprod_pro_write_to_filesystem( $file_name, $file_contents ) {

	global $wp_filesystem;
	$upload_dir = wp_upload_dir();
	$contentdir = trailingslashit( $upload_dir['basedir'] ) . 'seedprod-themes-exports/';
	$wp_filesystem->put_contents(
		$contentdir . $file_name,
		$file_contents,
		FS_CHMOD_FILE
	);

}

/**
 * Replaces images links to locally download files.
 *
 *  @param string $data Data.
 * @param mixed  $html Page HTML.
 */
function seedprod_pro_process_image_filenames( $data, $html ) {
	$output = array(
		'data'   => '',
		'html'   => '',
		'images' => array(),
	);

	$regex = '/(http)[^\s\'"]+?(png|jpg|jpeg|gif|ico|svg|bmp|tiff|webp)[^\s\'"]*?(?=[\'"])/i';

	$img_srcs = array();

	preg_match_all( $regex, $data, $img_srcs );

	preg_match_all( $regex, $html, $img_srcs );

	// Eliminate duplicates & pair with extension match from above.
	$unique_img_srcs_extensions = array();
	foreach ( $img_srcs[0] as $index => $img_src ) {
		$unique_img_srcs_extensions[ $img_src ] = $img_srcs[2][ $index ];
	}

	// Need to decode data as WordPress is encoding special characters such as & to &amp; which is.
	// interfering with Unsplash URLs & making it hard to find / replace URLs in strings.
	$processed_data = wp_specialchars_decode( $data );
	$processed_html = wp_specialchars_decode( $html );

	foreach ( $unique_img_srcs_extensions as $old_url => $extension ) {
		$prefix = 'theme-builder';

		// Likewise, decode search string.
		$old_url_decoded = wp_specialchars_decode( $old_url );

		$alphanumeric_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$random_chars       = substr( str_shuffle( $alphanumeric_chars ), 0, 16 );

		$filename = $prefix . '-' . $random_chars . '.' . $extension;

		$upload_dir = wp_upload_dir();
		$contentdir = trailingslashit( $upload_dir['baseurl'] ) . 'seedprod-themes-exports/';

		$new_url = $contentdir . $filename;

		// Remove URL for local preview.

		$processed_data = str_replace( $old_url_decoded, $new_url, $processed_data );
		$processed_html = str_replace( $old_url_decoded, $new_url, $processed_html );

		$output['images'][] = array(
			'prefix'    => $prefix,
			'extension' => $extension,
			'filename'  => $filename,
			'old_url'   => $old_url_decoded,
			'new_url'   => $new_url,
		);
	}

	$output['data'] = $processed_data;
	$output['html'] = $processed_html;

	return $output;
}

/**
 * Upload files to media library
 *
 * @param string $url            Url.
 * @param mixed  $parent_post_id Parent Id.
 */
function seedprod_pro_insert_attachment_from_url( $url, $parent_post_id = null ) {

	if ( ! class_exists( 'WP_Http' ) ) {
		include_once ABSPATH . WPINC . '/class-http.php';
	}

	$http     = new WP_Http();
	$response = $http->request( $url, array( 'sslverify' => false ) );
	if ( 200 !== $response['response']['code'] ) {
		return false;
	}

	$upload = wp_upload_bits( basename( $url ), null, $response['body'] );
	if ( ! empty( $upload['error'] ) ) {
		return false;
	}

	$file_path        = $upload['file'];
	$file_name        = basename( $file_path );
	$file_type        = wp_check_filetype( $file_name, null );
	$attachment_title = sanitize_file_name( pathinfo( $file_name, PATHINFO_FILENAME ) );
	$wp_upload_dir    = wp_upload_dir();

	$post_info = array(
		'guid'           => $wp_upload_dir['url'] . '/' . $file_name,
		'post_mime_type' => $file_type['type'],
		'post_title'     => $attachment_title,
		'post_content'   => '',
		'post_status'    => 'inherit',
	);

	// Create the attachment.
	$attach_id = wp_insert_attachment( $post_info, $file_path, $parent_post_id );

	// Include image.php.
	require_once ABSPATH . 'wp-admin/includes/image.php';

	// Define attachment metadata.
	$attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );

	// Assign metadata to attachment.
	wp_update_attachment_metadata( $attach_id, $attach_data );

	return $attach_id;

}



/**
 * At import theme files, download files from local folder to media library
 * fix import images urls to new images
 *
 *  @param string $data Page data.
 *  @param string $html Page html.
 */
function seedprod_pro_process_image_filenames_import_theme( $data, $html ) {

	$output = array(
		'data'   => '',
		'html'   => '',
		'images' => array(),
	);

	$regex = '/(http)[^\s\'"]+?(png|jpg|jpeg|gif|ico|svg|bmp|tiff|webp)[^\s\'"]*?(?=[\'"])/i';

	$img_srcs = array();

	preg_match_all( $regex, $data, $img_srcs );

	preg_match_all( $regex, $html, $img_srcs );

	// Eliminate duplicates & pair with extension match from above.
	$unique_img_srcs_extensions = array();
	foreach ( $img_srcs[0] as $index => $img_src ) {
		$unique_img_srcs_extensions[ $img_src ] = $img_srcs[2][ $index ];
	}

	// Need to decode data as WordPress is encoding special characters such as & to &amp; which is.
	// interfering with Unsplash URLs & making it hard to find / replace URLs in strings.
	$processed_data = wp_specialchars_decode( $data );
	$processed_html = wp_specialchars_decode( $html );

	foreach ( $unique_img_srcs_extensions as $old_url => $extension ) {
		$prefix = 'themebuilder';

		// Likewise, decode search string.
		$old_url_decoded = wp_specialchars_decode( $old_url );

		$alphanumeric_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$random_chars       = substr( str_shuffle( $alphanumeric_chars ), 0, 16 );

		$image_url = trim( urldecode( $old_url ) );

		$upload_dir       = wp_upload_dir();
		$contentdirimport = trailingslashit( $upload_dir['baseurl'] ) . 'seedprod-themes-imports/';

		$plugin_img_url = $contentdirimport . basename( $image_url );
		$new_url_id     = seedprod_pro_insert_attachment_from_url( $plugin_img_url );
		$new_url        = wp_get_attachment_url( $new_url_id );

		if ( is_wp_error( $new_url ) ) {
			echo esc_html( $new_url->get_error_message() );
		} else {

			$processed_data = str_replace( $old_url_decoded, $new_url, $processed_data );
			$processed_html = str_replace( $old_url_decoded, $new_url, $processed_html );
		}

		$output['images'][] = array(
			'prefix'    => $prefix,
			'extension' => $extension,
			'old_url'   => $old_url_decoded,
			'new_url'   => $new_url,
		);
	}

	$output['data'] = $processed_data;
	$output['html'] = $processed_html;

	return $output;
}

/**
 * Adding theme json files and images in zipped folder and download zipped theme folder
 *
 * @param string $filenames   Filenames of images.
 * @param string $export_json Export json data.
 * @param string $type        Export type.
 */
function seedprod_pro_prepare_zip( $filenames, $export_json, $type = 'theme' ) {

	global $wp_filesystem;
	$upload_dir = wp_upload_dir();
	$contentdir = trailingslashit( $upload_dir['basedir'] ) . 'seedprod-themes-exports/';

	$zip = new ZipArchive();
	if ( 'page' === $type ) {
		$zip_filename = 'seedprod-export-landing-page-files.zip';
	} else {
		$zip_filename = 'seedprod-export-theme-files.zip';
	}

	if ( $zip->open( $zip_filename, ZipArchive::CREATE ) !== true ) { // phpcs:ignore
		exit( esc_html( "Can't open $zip_filename" ) );
	}

	foreach ( $filenames as $filename ) {
		$zip->addFile( $contentdir . $filename, $filename );
	}

	if ( 'page' === $type ) {
		$zip->addFromString( 'export_page.json', $export_json );
	} else {
		$zip->addFromString( 'export_theme.json', $export_json );
	}

	$zip->close();

	ob_end_clean();
	header( 'Content-Type: application/zip' );
	header( 'Content-Disposition: attachment; filename=' . basename( $zip_filename ) );
	header( 'Content-Length: ' . filesize( $zip_filename ) );
	readfile( $zip_filename ); // phpcs:ignore
	unlink( $zip_filename );

}

/**
 * Remove all files inside importer directory
 *
 * @param string $dir Path.
 */
function recursive_rmdir( $dir ) {
	$files = array_diff( scandir( $dir ), array( '.', '..' ) );
	foreach ( $files as $file ) {
		( is_dir( "$dir/$file" ) ) ? recursive_rmdir( "$dir/$file" ) : unlink( "$dir/$file" );
	}
	return rmdir( $dir );
}


/**
 * Export theme files
 */
function seedprod_pro_export_theme_files() {

	if ( check_ajax_referer( 'seedprod_pro_export_theme_files' ) ) {

		if ( ! current_user_can( apply_filters( 'seedprod_import_export', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		$url = wp_nonce_url( 'admin.php?page=seedprod_pro_export_theme_files', 'seedprod_import_theme_request' );
		if ( false === ( $creds = request_filesystem_credentials( $url, '', false, false, null ) ) ) { // phpcs:ignore
			return;
		}

		if ( ! WP_Filesystem( $creds ) ) {
			request_filesystem_credentials( $url, '', true, false, null );
			return;
		}

		global $wpdb;
		$tablename      = $wpdb->prefix . 'posts';
		$meta_tablename = $wpdb->prefix . 'postmeta';
		// step get list of theme and create json file.
		$sql = "SELECT * FROM $tablename p LEFT JOIN $meta_tablename pm ON (pm.post_id = p.ID)";

		$sql             .= ' WHERE post_status="publish" AND post_type = "seedprod" AND meta_key = "_seedprod_is_theme_template"';
		$results          = $wpdb->get_results( $sql ); // phpcs:ignore
		$processed_data[] = array();

		$name = 'Export Theme';

		$update_template_id = null;
		$type               = 'theme';

		$export                     = array();
		$export['type']             = 'theme-builder';
		$export['current_home_url'] = home_url();
		$export['theme']            = array();
		$export['mapped']           = array();

		$shortcode_exports = array();

		foreach ( $results as $k => $v ) {
			// get_post_meta.
			$meta             = json_encode( get_post_meta( $v->ID ) ); // phpcs:ignore
			$content          = $v->post_content;
			$content_filtered = $v->post_content_filtered;

			// replace image links.
			$processed_data[ $k ] = seedprod_pro_process_image_filenames( $content_filtered, $content );

			$export['theme'][] = array(
				'order'                 => $v->menu_order,
				'post_content'          => base64_encode( $processed_data[ $k ]['html'] ), // phpcs:ignore
				'post_content_filtered' => base64_encode( $processed_data[ $k ]['data'] ), // phpcs:ignore
				'post_title'            => base64_encode( $v->post_title ), // phpcs:ignore
				'meta'                  => base64_encode( $meta ), // phpcs:ignore
			);

			$post_content_shortcode = base64_decode( base64_encode( $processed_data[ $k ]['html'] ) ); // phpcs:ignore
			$re                     = '/((\[)(sp_template_part id="){1}[0-9]*["](\]))/m';

			preg_match_all( $re, $post_content_shortcode, $matches, PREG_SET_ORDER, 0 );

			if ( $matches ) {

				foreach ( $matches as $t => $val ) {

					$shortcode_content = $val[0];
					$shortcode_page_sc = str_replace( '[sp_template_part id="', '', $shortcode_content );
					$shortcode_page_sc = str_replace( '"]', '', $shortcode_page_sc );

					$shortcode_exports[ $shortcode_page_sc ] = array(
						'id'        => $shortcode_page_sc,
						'shortcode' => $shortcode_content,
					);

				}
			}
		}

		foreach ( $shortcode_exports as $k => $sc_val ) {
			$page_id        = $sc_val['id'];
			$page_shortcode = $sc_val['shortcode'];

			$sql      = "SELECT p.post_title FROM $tablename p LEFT JOIN $meta_tablename pm ON (pm.post_id = p.ID)";
			$sql     .= ' WHERE p.id = %d and post_status="publish" AND post_type = "seedprod" AND meta_key = "_seedprod_is_theme_template"';
			$safe_sql = $wpdb->prepare( $sql, absint( $page_id ) ); // phpcs:ignore
			$page     = $wpdb->get_row( $safe_sql ); // phpcs:ignore

			if ( ! empty( $page ) ) {
				$export['mapped'][] = array(
					'id'         => $page_id,
					'shortcode'  => base64_encode( $page_shortcode ), // phpcs:ignore
					'page_title' => $page->post_title,
				);
			}
		}

		$export_json       = wp_json_encode( $export );
		$files_to_download = array();

		global $wp_filesystem;

		$upload_dir = wp_upload_dir();
		$path       = trailingslashit( $upload_dir['basedir'] ) . 'seedprod-themes-exports/';

		$targetdir = $path; // target directory.

		if ( is_dir( $targetdir ) ) {
			recursive_rmdir( $targetdir );
		}
		mkdir( $targetdir, 0777 );

		// save images locally.
		foreach ( $processed_data as $k1 => $v1 ) {
			seedprod_pro_save_images_locally( $processed_data[ $k1 ]['images'] );
			foreach ( $processed_data[ $k1 ]['images'] as $image ) {
				$files_to_download[] = $image['filename'];
			}
		}

		// create zip and force download zipped folder.
		$zip_download = seedprod_pro_prepare_zip( $files_to_download, $export_json );

		wp_send_json( true );
	}

	exit;

}

/**
 * Theme Import files method
 */
function seedprod_pro_import_theme_files() {

	if ( check_ajax_referer( 'seedprod_pro_import_theme_files' ) ) {

		if ( ! current_user_can( apply_filters( 'seedprod_import_export', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		// set script timeout longer
		set_time_limit(60);

		$url = wp_nonce_url( 'admin.php?page=seedprod_pro_import_theme_files', 'seedprod_import_theme_files' );
		if ( false === ( $creds = request_filesystem_credentials( $url, '', false, false, null ) ) ) { // phpcs:ignore
			return;
		}

		if ( ! WP_Filesystem( $creds ) ) {
			request_filesystem_credentials( $url, '', true, false, null );
			return;
		}

		if ( isset( $_FILES['seedprod_theme_files']['name'] ) ) {
			$filename = wp_unslash( $_FILES['seedprod_theme_files']['name']); // phpcs:ignore
			$source   = $_FILES['seedprod_theme_files']['tmp_name']; // phpcs:ignore
			$type     = $_FILES['seedprod_theme_files']['type']; // phpcs:ignore

			$name           = explode( '.', $filename );
			$accepted_types = array( 'application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed' );
			foreach ( $accepted_types as $mime_type ) {
				if ( $mime_type === $type ) {
					$okay = true;
					break;
				}
			}

			$filename_import = 'seedprod-themes-imports';

			global $wp_filesystem;

			$upload_dir = wp_upload_dir();
			$path       = trailingslashit( $upload_dir['basedir'] );
			$webpath    = trailingslashit( $upload_dir['baseurl'] );

			$filenoext = basename( $filename_import, '.zip' );  // absolute path to the directory where zipper.php is in (lowercase).
			$filenoext = basename( $filenoext, '.ZIP' );  // absolute path to the directory where zipper.php is in (when uppercase).

			$targetdir    = $path . $filenoext; // target directory.
			$targetzip    = $path . $filename; // target zip file.
			$webtargetdir = $webpath . $filenoext;

			if ( is_dir( $targetdir ) ) {
				recursive_rmdir( $targetdir );
			}
			mkdir( $targetdir, 0777 );

			if ( move_uploaded_file( $source, $targetzip ) ) {
				$zip = new ZipArchive();
				$x   = $zip->open( $targetzip );  // open the zip file to extract.
				if ( true === $x ) {
					$zip->extractTo( $targetdir ); // place in the directory with same name.
					$zip->close();

					unlink( $targetzip );
				}
				$theme_json_data     = $targetdir . '/export_theme.json';
				$web_theme_json_data = $webtargetdir . '/export_theme.json';

				if ( file_exists( $theme_json_data ) ) {
					$file_theme_json = wp_remote_get( $web_theme_json_data, array( 'sslverify' => false ) );
					if ( is_wp_error( $file_theme_json ) ) {
						$error_code    = wp_remote_retrieve_response_code( $file_theme_json );
						$error_message = wp_remote_retrieve_response_message( $file_theme_json );
						wp_send_json_error( $error_message );
					}
					$data = json_decode( $file_theme_json['body'] ); // phpcs:ignore
					if ( ! empty( $data->type ) && 'theme-builder' !== $data->type ) {
						$message = 'This does not appear to be a SeedProd theme.';
						wp_send_json_error( $message );
					}
					seedprod_pro_theme_import_json( $data );
					// remove the json file for security.
					wp_delete_file( $theme_json_data );

					wp_send_json( true );
				}
			} else {
				$message = 'There was a problem with the upload. Please try again.';
				wp_send_json_error( $message );
			}
		} else {
			$message = 'There was a problem with the upload. Please try again.';
			wp_send_json_error( $message );
		}
	}
}

/**
 * Theme Import url method
 */
function seedprod_pro_import_theme_by_url( $theme_url = null ) {
	$is_ajax_request = false;
	if ( null == $theme_url ) {
		$is_ajax_request = check_ajax_referer( 'seedprod_pro_import_theme_by_url' );
	}

	if ( $is_ajax_request || ! empty( $theme_url ) ) {

		if ( ! current_user_can( apply_filters( 'seedprod_import_export', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		$url = wp_nonce_url( 'admin.php?page=seedprod_pro_import_theme_by_url', 'seedprod_import_theme_files' );
		if ( false === ( $creds = request_filesystem_credentials( $url, '', false, false, null ) ) ) { // phpcs:ignore
			return;
		}

		if ( ! WP_Filesystem( $creds ) ) {
			request_filesystem_credentials( $url, '', true, false, null );
			return;
		}

		$source = isset( $_REQUEST['seedprod_theme_url'] ) ? wp_kses_post( wp_unslash( $_REQUEST['seedprod_theme_url'] ) ) : '';

		if(!empty($theme_url)) {
			$source = $theme_url;
		}

		$file_import_url_json = wp_remote_get( $source, array( 'sslverify' => false ) );
		if ( is_wp_error( $file_import_url_json ) ) {
			$error_code    = wp_remote_retrieve_response_code( $file_import_url_json );
			$error_message = wp_remote_retrieve_response_message( $file_import_url_json );
			wp_send_json_error( $error_message );
		}
		preg_match( '/zip/', $file_import_url_json['headers']['content-type'], $match );
		if ( is_array( $match ) && count( $match ) <= 0 ) {
			$error_message = 'There was a problem with the upload. Please try again.';
			wp_send_json_error( $error_message );
		}

		if (  '' !=$source && $file_import_url_json['body'] ) { // phpcs:ignore

			$url_data = pathinfo( $source );

			$filename = $url_data['basename'];
			$type     = $url_data['extension'];

			$filename = substr( $filename, 0, strpos( $filename, '.zip' ) + 4 );

			$name           = explode( '.', $filename );
			$accepted_types = array( 'application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed' );
			foreach ( $accepted_types as $mime_type ) {
				if ( $mime_type === $type ) {
					$okay = true;
					break;
				}
			}

			$continue = strtolower( $name[1] ) === 'zip' ? true : false;
			if ( ! $continue ) {
				$message = 'The file you are trying to upload is not a .zip file. Please try again.';
				wp_send_json_error( $message );
			}

			$filename_import = 'seedprod-themes-imports';

			global $wp_filesystem;
			$upload_dir = wp_upload_dir();
			$path       = trailingslashit( $upload_dir['basedir'] );
			$webpath    = trailingslashit( $upload_dir['baseurl'] );

			$filenoext = basename( $filename_import, '.zip' );  // absolute path to the directory where zipper.php is in (lowercase).
			$filenoext = basename( $filenoext, '.ZIP' );  // absolute path to the directory where zipper.php is in (when uppercase).

			$targetdir    = $path . $filenoext; // target directory.
			$targetzip    = $path . $filename; // target zip file.
			$webtargetdir = $webpath . $filenoext;

			if ( is_dir( $targetdir ) ) {
				recursive_rmdir( $targetdir );
			}
			mkdir( $targetdir, 0777 );
			if ( file_put_contents( $targetzip, $file_import_url_json['body']) ) {  // phpcs:ignore

				$zip = new ZipArchive();
				$x   = $zip->open( $targetzip );  // open the zip file to extract.
				if ( true === $x ) {
					$zip->extractTo( $targetdir ); // place in the directory with same name.
					$zip->close();

					unlink( $targetzip );
				}
				$theme_json_data     = $targetdir . '/export_theme.json';
				$web_theme_json_data = $webtargetdir . '/export_theme.json';

				if ( file_exists( $theme_json_data ) ) {
					$file_theme_json = wp_remote_get( $web_theme_json_data, array( 'sslverify' => false ) );
					if ( is_wp_error( $file_theme_json ) ) {
						$error_code    = wp_remote_retrieve_response_code( $file_theme_json );
						$error_message = wp_remote_retrieve_response_message( $file_theme_json );
						wp_send_json_error( $error_message );
					}
					$data = json_decode( $file_theme_json['body'] );  // phpcs:ignore
					if ( ! empty( $data->type ) && 'theme-builder' !== $data->type ) {
						$message = 'This does not appear to be a SeedProd theme.';
						wp_send_json_error( $message );
					}
					seedprod_pro_theme_import_json( $data );
					// remove the json file for security.
					wp_delete_file( $theme_json_data );

					wp_send_json( true );
				}
			} else {
				$message = 'There was a problem with the upload. Please try again.';
				wp_send_json_error( $message );
			}
		} else {
			$message = 'There was a problem with the upload. Please try again.';
			wp_send_json_error( $message );
		}
	}

}
