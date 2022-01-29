<?php


function seedprod_pro_check_for_free_version() {
	try {
		$seedprod_unsupported_feature = array();
		$migration                    = get_option( 'seedprod_migration_run_once' );
		if ( empty( $migration ) || ! empty( $_GET['sp-force-migrate'] ) ) {

			// migrate old licnese key if available
			$old_key = get_option( 'seed_cspv5_license_key' );
			if ( ! empty( $old_key ) ) {
				update_option( 'seedprod_api_key', $old_key );
				$r = seedprod_pro_save_api_key( $old_key );
			}

			// see if free version old settings exists and they do not have the pro version
			// && empty(get_option('seed_cspv5_settings_content'))
			if ( ! empty( $_GET['sp-force-migrate'] ) || empty( get_option( 'seed_cspv5_settings_content' ) ) && empty( get_option( 'seedprod_coming_soon_page_id' ) ) && empty( get_option( 'seedprod_maintenance_mode_page_id' ) ) && ! empty( get_option( 'seed_csp4_settings_content' ) ) && get_option( 'seedprod_csp4_migrated' ) === false && get_option( 'seedprod_csp4_imported' ) === false ) {

				// import csp4 settings to plugin

				// get settings
				$s1 = get_option( 'seed_csp4_settings_content' );
				$s2 = get_option( 'seed_csp4_settings_design' );
				$s3 = get_option( 'seed_csp4_settings_advanced' );

				if ( empty( $s1 ) ) {
					$s1 = array();
				}

				if ( empty( $s2 ) ) {
					$s2 = array();
				}

				if ( empty( $s3 ) ) {
					$s3 = array();
				}

				$csp4_settings = $s1 + $s2 + $s3;

				// update global settings

				$ts                = get_option( 'seedprod_settings' );
				$seedprod_settings = json_decode( $ts, true );

				$type = 'cs';
				if ( ! empty( $csp4_settings['status'] ) && $csp4_settings['status'] == 1 ) {
					$seedprod_settings['enable_coming_soon_mode'] = true;
					$seedprod_settings['enable_maintenance_mode'] = false;
					$type = 'cs';
				}
				if ( ! empty( $csp4_settings['status'] ) && $csp4_settings['status'] == 2 ) {
					$seedprod_settings['enable_maintenance_mode'] = true;
					$seedprod_settings['enable_coming_soon_mode'] = false;
					$type = 'mm';
				}

				update_option( 'seedprod_settings', json_encode( $seedprod_settings ) );

				// update page settings
				$csp4_template_file = SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/csp4-template.json';
				$csp4_template      = json_decode( file_get_contents( $csp4_template_file ), true );

				//$csp4_template
				// page to publish if active from v4
				if ( ! empty( $csp4_settings['status'] ) && $csp4_settings['status'] == 1 || $csp4_settings['status'] == 2 ) {
					$csp4_template['post_status'] = 'published';
				}

				// set page type
				$csp4_template['page_type'] = $type;

				// set custom html
				if ( ! empty( $csp4_settings['html'] ) ) {
					$custom_html = json_decode(
						'{
                "id": "iuf8h9",
                "elType": "block",
                "type": "custom-html",
                "settings": {
                    "code": "Full Page Custom HTML is no longer supported in this builder. However your custom html page is still being display and will continue to be displayed as long as you DO NOT save this page. There is Custom HTML block you can use in the builder.",
                    "marginTop": "0",
                    "paddingTop": "",
                    "paddingBottom": "",
                    "paddingLeft": "",
                    "paddingRight": "",
                    "paddingSync": true
                }}
            '
					);
					if ( ! empty( $custom_html ) ) {
						$csp4_template['document']['sections'][0]['rows'][0]['cols'][0]['blocks']   = array();
						$csp4_template['document']['sections'][0]['rows'][0]['cols'][0]['blocks'][] = $custom_html;
					}

					$csp4_template['document']['settings']['contentPosition']             = '1';
					$csp4_template['document']['sections'][0]['settings']['contentWidth'] = '1';
				} else {

					// set logo
					if ( ! empty( $csp4_settings['logo'] ) ) {
						$csp4_template['document']['sections'][0]['rows'][0]['cols'][0]['blocks'][0]['settings']['src'] = $csp4_settings['logo'];
					} else {
						$csp4_template['document']['sections'][0]['rows'][0]['cols'][0]['blocks'][0]['settings']['src'] = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z/C/HgAGgwJ/lK3Q6wAAAABJRU5ErkJggg==';
					}

					// set headline
					if ( ! empty( $csp4_settings['headline'] ) ) {
						$csp4_template['document']['sections'][0]['rows'][0]['cols'][0]['blocks'][1]['settings']['headerTxt'] = $csp4_settings['headline'];
					} else {
						$csp4_template['document']['sections'][0]['rows'][0]['cols'][0]['blocks'][1]['settings']['headerTxt'] = '';
					}

					// set description
					if ( ! empty( $csp4_settings['description'] ) ) {
						$csp4_template['document']['sections'][0]['rows'][0]['cols'][0]['blocks'][2]['settings']['txt'] = $csp4_settings['description'];
					} else {
						$csp4_template['document']['sections'][0]['rows'][0]['cols'][0]['blocks'][2]['settings']['txt'] = '';
					}

					// set footer credit
					if ( ! empty( $csp4_settings['footer_credit'] ) ) {
						$csp4_template['show_powered_by_link'] = true;
					}

					// favicon
					if ( ! empty( $csp4_settings['favicon'] ) ) {
						$csp4_template['favicon'] = $csp4_settings['favicon'];
					}

					// title
					if ( ! empty( $csp4_settings['seo_title'] ) ) {
						$csp4_template['seo_title'] .= $csp4_settings['seo_title'];
					}

					// meta
					if ( ! empty( $csp4_settings['seo_description'] ) ) {
						$csp4_template['seo_description'] .= $csp4_settings['seo_description'];
					}

					// set google analytics
					if ( ! empty( $csp4_settings['ga_analytics'] ) ) {
						$csp4_template['footer_scripts'] = $csp4_settings['ga_analytics'];
					}

					// set bg color
					if ( ! empty( $csp4_settings['bg_color'] ) ) {
						$csp4_template['document']['settings']['bgColor'] = $csp4_settings['bg_color'];
					}

					// set bg dimming
					if ( ! empty( $csp4_settings['bg_overlay'] ) ) {
						$csp4_template['document']['settings']['bgDimming'] = '50';
					}

					// set bg image
					if ( ! empty( $csp4_settings['bg_image'] ) ) {
						$csp4_template['document']['settings']['bgImage'] = $csp4_settings['bg_image'];
					}

					// set bg cover
					if ( ! empty( $csp4_settings['bg_cover'] ) ) {
						if ( ! empty( $csp4_settings['bg_size'] ) && $csp4_settings['bg_size'] == 'cover' ) {
							$csp4_template['document']['settings']['bgPosition'] = 'cover';
						}

						if ( ! empty( $csp4_settings['bg_size'] ) && $csp4_settings['bg_size'] == 'contain' ) {
							$csp4_template['document']['settings']['bgPosition'] = 'full';
						}
					} else {
						if ( ! empty( $csp4_settings['bg_repeat'] ) && $csp4_settings['bg_repeat'] == 'repeat' ) {
							$csp4_template['document']['settings']['bgPosition'] = 'repeat';
						}

						if ( ! empty( $csp4_settings['bg_repeat'] ) && $csp4_settings['bg_repeat'] == 'repeat-x' ) {
							$csp4_template['document']['settings']['bgPosition'] = 'repeattop';
						}

						if ( ! empty( $csp4_settings['bg_repeat'] ) && $csp4_settings['bg_repeat'] == 'repeat-y' ) {
							$csp4_template['document']['settings']['bgPosition'] = 'repeatvc';
						}
					}

					//$csp4_template['document']['settings']['customCss'] .=

					// set width
					if ( ! empty( $csp4_settings['max_width'] ) ) {
						$csp4_template['document']['sections'][0]['settings']['width'] = $csp4_settings['max_width'];
					}

					// enable well
					if ( ! empty( $csp4_settings['enable_well'] ) ) {
						$csp4_template['document']['sections'][0]['settings']['bgColor']        = '#ffffff';
						$csp4_template['document']['sections'][0]['settings']['borderRadiusTL'] = '4';
					}

					// set text color
					if ( ! empty( $csp4_settings['text_color'] ) ) {
						$csp4_template['document']['settings']['textColor'] = $csp4_settings['text_color'];
					}

					// set headline color
					if ( ! empty( $csp4_settings['headline_color'] ) ) {
						$csp4_template['document']['settings']['headerColor'] = $csp4_settings['headline_color'];
					} else {
						$csp4_template['document']['settings']['headerColor'] = $csp4_settings['text_color'];
					}

					// set link color
					if ( ! empty( $csp4_settings['link_color'] ) ) {
						$csp4_template['document']['settings']['linkColor']   = $csp4_settings['link_color'];
						$csp4_template['document']['settings']['buttonColor'] = $csp4_settings['link_color'];
					}

					// set font
					if ( ! empty( $csp4_settings['text_font'] ) ) {
						$csp4_template['document']['settings']['textFontVariant']   = '400';
						$csp4_template['document']['settings']['headerFontVariant'] = '400';

						if ( $csp4_settings['text_font'] == '_arial' ) {
							$csp4_template['document']['settings']['textFont']   = "'Helvetica Neue', Arial, sans-serif";
							$csp4_template['document']['settings']['headerFont'] = "'Helvetica Neue', Arial, sans-serif";
						}
						if ( $csp4_settings['text_font'] == '_arial_black' ) {
							$csp4_template['document']['settings']['textFont']          = "'Helvetica Neue', Arial, sans-serif";
							$csp4_template['document']['settings']['headerFont']        = "'Helvetica Neue', Arial, sans-serif";
							$csp4_template['document']['settings']['textFontVariant']   = '700';
							$csp4_template['document']['settings']['headerFontVariant'] = '700';
						}
						if ( $csp4_settings['text_font'] == '_georgia' ) {
							$csp4_template['document']['settings']['textFont']   = 'Georgia, serif';
							$csp4_template['document']['settings']['headerFont'] = 'Georgia, serif';
						}
						if ( $csp4_settings['text_font'] == '_helvetica_neue' ) {
							$csp4_template['document']['settings']['textFont']   = "'Helvetica Neue', Arial, sans-serif";
							$csp4_template['document']['settings']['headerFont'] = "'Helvetica Neue', Arial, sans-serif";
						}
						if ( $csp4_settings['text_font'] == '_impact' ) {
							$csp4_template['document']['settings']['textFont']   = 'Impact, Charcoal, sans-serif';
							$csp4_template['document']['settings']['headerFont'] = 'Impact, Charcoal, sans-serif';
						}
						if ( $csp4_settings['text_font'] == '_lucida' ) {
							$csp4_template['document']['settings']['textFont']   = "'Helvetica Neue', Arial, sans-serif";
							$csp4_template['document']['settings']['headerFont'] = "'Helvetica Neue', Arial, sans-serif";
						}
						if ( $csp4_settings['text_font'] == '_palatino' ) {
							$csp4_template['document']['settings']['textFont']   = "'Helvetica Neue', Arial, sans-serif";
							$csp4_template['document']['settings']['headerFont'] = "'Helvetica Neue', Arial, sans-serif";
						}
						if ( $csp4_settings['text_font'] == '_tahoma' ) {
							$csp4_template['document']['settings']['textFont']   = 'Tahoma, Geneva, sans-serif';
							$csp4_template['document']['settings']['headerFont'] = 'Tahoma, Geneva, sans-serif';
						}
						if ( $csp4_settings['text_font'] == '_times' ) {
							$csp4_template['document']['settings']['textFont']   = "'Times New Roman', Times, serif";
							$csp4_template['document']['settings']['headerFont'] = "'Times New Roman', Times, serif";
						}
						if ( $csp4_settings['text_font'] == '_trebuchet' ) {
							$csp4_template['document']['settings']['textFont']   = "'Trebuchet MS', Helvetica, sans-serif";
							$csp4_template['document']['settings']['headerFont'] = "'Trebuchet MS', Helvetica, sans-serif";
						}
						if ( $csp4_settings['text_font'] == '_verdana' ) {
							$csp4_template['document']['settings']['textFont']   = 'Verdana, Geneva, sans-serif';
							$csp4_template['document']['settings']['headerFont'] = 'Verdana, Geneva, sans-serif';
						}
					}

					// set custom css
					if ( ! empty( $csp4_settings['custom_css'] ) ) {
						$csp4_template['document']['settings']['customCss'] .= $csp4_settings['custom_css'];
					}

					// set exclude urls
					if ( ! empty( $csp4_settings['disable_default_excluded_urls'] ) ) {
						$csp4_template['disable_default_excluded_urls'] = true;
					}

					// set header scripts
					if ( ! empty( $csp4_settings['header_scripts'] ) ) {
						$csp4_template['header_scripts'] .= $csp4_settings['header_scripts'];
					}

					// set footer scripts
					if ( ! empty( $csp4_settings['footer_scripts'] ) ) {
						$csp4_template['footer_scripts'] .= $csp4_settings['footer_scripts'];
					}

					// set append html
					if ( ! empty( $csp4_settings['append_html'] ) ) {
						$append_html = json_decode(
							'{
                "id": "iuf8h9",
                "elType": "block",
                "type": "custom-html",
                "settings": {
                    "code": "' . $csp4_settings['append_html'] . '",
                    "marginTop": "0",
                    "paddingTop": "",
                    "paddingBottom": "",
                    "paddingLeft": "",
                    "paddingRight": "",
                    "paddingSync": true
                }}
           '
						);
						if ( ! empty( $append_html ) ) {
							$csp4_template['document']['sections'][0]['rows'][0]['cols'][0]['blocks'][] = $append_html;
						}
					}
				}

				// create the coming soon or maintenance page and inject settings
				$slug = '';
				$cpt  = 'page';
				if ( $type == 'cs' || $type == 'mm' || $type == 'p404' ) {
					$cpt = 'seedprod';
				}
				if ( $type == 'cs' ) {
					$slug = 'sp-cs';
				}
				if ( $type == 'mm' ) {
					$slug = 'sp-mm';
				}

				$id = wp_insert_post(
					array(
						'comment_status' => 'closed',
						'ping_status'    => 'closed',
						'post_content'   => '',
						'post_status'    => 'publish',
						'post_title'     => 'seedprod',
						'post_type'      => $cpt,
						'post_name'      => $slug,
						'meta_input'     => array(
							'_seedprod_page'      => true,
							'_seedprod_page_uuid' => wp_generate_uuid4(),
						),
					),
					true
				);

				// update post because wp screws our json settings
				global $wpdb;
				$tablename = $wpdb->prefix . 'posts';
				$r         = $wpdb->update(
					$tablename,
					array(
						'post_content_filtered' => json_encode( $csp4_template ),
					),
					array( 'ID' => $id ),
					array(
						'%s',
					),
					array( '%d' )
				);

				if ( $type == 'cs' ) {
					update_option( 'seedprod_coming_soon_page_id', $id );
				}
				if ( $type == 'mm' ) {
					update_option( 'seedprod_maintenance_mode_page_id', $id );
				}

				// do we need to show it?
				update_option( 'seedprod_csp4_imported', true );
				update_option( 'seedprod_show_csp4', true );
				// flush rewrite rules
				flush_rewrite_rules();
			}

			
			// see if pro version old settings exists
			if ( empty( get_option( 'seedprod_coming_soon_page_id' ) ) && empty( get_option( 'seedprod_maintenance_mode_page_id' ) ) && ! empty( get_option( 'seed_cspv5_settings_content' ) ) && get_option( 'seedprod_cspv5_migrated' ) === false && get_option( 'seedprod_cspv5_imported' ) === false ) {

				// import cspv5 settings to plugin
				$token = get_option( 'seedprod_token' );
				if ( empty( $token ) ) {
					add_option( 'seedprod_token', wp_generate_uuid4() );
				}

				// get settings
				$csp5_settings = get_option( 'seed_cspv5_settings_content' );

				//Find Coming Soon Page
				$cs_page_id = get_option( 'seed_cspv5_coming_soon_page_id' );
				global $wpdb;
				$tablename = $wpdb->prefix . 'cspv5_pages';
				$sql       = "SELECT * FROM $tablename WHERE id= %d";
				$safe_sql  = $wpdb->prepare( $sql, $cs_page_id );
				$page      = $wpdb->get_row( $safe_sql );

				// Check for base64 encoding of settings
				if ( base64_encode( base64_decode( $page->settings, true ) ) === $page->settings ) {
					$csp5_page_settings = unserialize( base64_decode( $page->settings ) );
				} else {
					$csp5_page_settings = unserialize( $page->settings );
				}

				// update global settings

				$ts                = get_option( 'seedprod_settings' );
				$seedprod_settings = json_decode( $ts, true );

				$type = 'cs';
				if ( ! empty( $csp5_settings['status'] ) && $csp5_settings['status'] == 1 ) {
					$seedprod_settings['enable_coming_soon_mode'] = true;
					$seedprod_settings['enable_maintenance_mode'] = false;
					$type = 'cs';
				}
				if ( ! empty( $csp5_settings['status'] ) && $csp5_settings['status'] == 2 ) {
					$seedprod_settings['enable_maintenance_mode'] = true;
					$seedprod_settings['enable_coming_soon_mode'] = false;
					$type = 'mm';
				}
				if ( ! empty( $csp5_settings['status'] ) && $csp5_settings['status'] == 3 ) {
					$seedprod_unsupported_feature[] = 'rm';
				}

				update_option( 'seedprod_settings', json_encode( $seedprod_settings ) );

				// update page settings
				$csp5_template_file = SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/csp5-template.json';
				$csp5_template      = json_decode( file_get_contents( $csp5_template_file ), true );

				// page to publish if active from v4
				if ( ! empty( $csp5_settings['status'] ) && $csp5_settings['status'] == 1 || $csp5_settings['status'] == 2 ) {
					$csp5_template['post_status'] = 'published';
				}

				// set page type
				$csp5_template['page_type'] = $type;

				// set page settings
				if ( ! empty( $csp5_settings['disable_default_excluded_urls'] ) ) {
					$csp5_template['disable_default_excluded_urls'] = true;
				}

				if ( ! empty( $csp5_settings['include_exclude_options'] ) ) {
					$csp5_template['include_exclude_type'] = $csp5_settings['include_exclude_options'];
				}

				if ( ! empty( $csp5_settings['include_url_pattern'] ) ) {
					$csp5_template['include_list'] = $csp5_settings['include_url_pattern'];
				}

				if ( ! empty( $csp5_settings['exclude_url_pattern'] ) ) {
					$csp5_template['exclude_list'] = $csp5_settings['exclude_url_pattern'];
				}

				if ( ! empty( $csp5_settings['client_view_url'] ) ) {
					$csp5_template['bypass_phrase'] = $csp5_settings['client_view_url'];
				}

				if ( ! empty( $csp5_settings['bypass_expires'] ) ) {
					$csp5_template['bypass_expires'] = $csp5_settings['bypass_expires'];
				}

				if ( ! empty( $csp5_settings['alt_bypass'] ) ) {
					$csp5_template['bypass_cookie'] = true;
				}

				if ( ! empty( $csp5_settings['ip_access'] ) ) {
					$csp5_template['access_by_ip'] = $csp5_settings['ip_access'];
				}

				if ( ! empty( $csp5_settings['include_roles'] ) ) {
					$new_roles = array();
					foreach ( $csp5_settings['include_roles'] as $v ) {
						if ( $v == 'anyone' ) {
							$new_roles[] = 'Anyone Logged In';
						} else {
							$new_roles[] = ucfirst( str_replace( '_', ' ', $v ) );
						}
					}
					$csp5_template['access_by_role'] = $new_roles;
				}

				// migrate settings
				$page_uuid     = wp_generate_uuid4();
				$csp5_template = seedprod_pro_migrate_v5_settings( $csp5_page_settings, $csp5_template, $page_uuid, $page->id );

				// create the coming soon or maintenance page and inject settings
				$slug = '';
				$cpt  = 'page';
				if ( $type == 'cs' || $type == 'mm' || $type == 'p404' ) {
					$cpt = 'seedprod';
				}
				if ( $type == 'cs' ) {
					$slug = 'sp-cs';
				}
				if ( $type == 'mm' ) {
					$slug = 'sp-mm';
				}

				$id = wp_insert_post(
					array(
						'comment_status' => 'closed',
						'ping_status'    => 'closed',
						'post_content'   => '',
						'post_status'    => 'publish',
						'post_title'     => 'seedprod',
						'post_type'      => $cpt,
						'post_name'      => $slug,
						'meta_input'     => array(
							'_seedprod_page'      => true,
							'_seedprod_page_uuid' => $page_uuid,
						),
					),
					true
				);

				// update post because wp screws our json settings
				global $wpdb;
				$tablename = $wpdb->prefix . 'posts';
				$r         = $wpdb->update(
					$tablename,
					array(
						'post_content_filtered' => json_encode( $csp5_template ),
					),
					array( 'ID' => $id ),
					array(
						'%s',
					),
					array( '%d' )
				);

				if ( $type == 'cs' ) {
					update_option( 'seedprod_coming_soon_page_id', $id );
				}
				if ( $type == 'mm' ) {
					update_option( 'seedprod_maintenance_mode_page_id', $id );
				}

				// do we need to show it?
				update_option( 'seedprod_cspv5_imported', true );
				update_option( 'seedprod_show_cspv5', true );

				// find and import pages

				global $wpdb;
				$tablename = $wpdb->prefix . 'cspv5_pages';
				$sql       = "SELECT * FROM $tablename WHERE type = 'lp'";
				$pages     = $wpdb->get_results( $sql );

				foreach ( $pages as $page ) {
					// Check for base64 encoding of settings
					if ( base64_encode( base64_decode( $page->settings, true ) ) === $page->settings ) {
						$csp5_page_settings = unserialize( base64_decode( $page->settings ) );
					} else {
						$csp5_page_settings = unserialize( $page->settings );
					}

					// migrate settings
					$page_uuid     = wp_generate_uuid4();
					$csp5_template = json_decode( file_get_contents( $csp5_template_file ), true );
					$csp5_template = seedprod_pro_migrate_v5_settings( $csp5_page_settings, $csp5_template, $page_uuid, $page->id );

					// set migrated id
					$csp5_template['cspv5_id'] = $page->id;

					// create the coming soon or maintenance page and inject settings
					$slug = $page->path;
					$name = $page->path;
					if ( ! empty( $page->name ) ) {
						$name = $page->name;
					}
					$cpt = 'page';

					$id = false;
					$id = wp_insert_post(
						array(
							'comment_status' => 'closed',
							'ping_status'    => 'closed',
							'post_content'   => '',
							'post_status'    => 'publish',
							'post_title'     => $name,
							'post_type'      => $cpt,
							'post_name'      => $slug,
							'meta_input'     => array(
								'_seedprod_page'      => true,
								'_seedprod_page_uuid' => $page_uuid,
							),
						),
						true
					);

					// update post because wp screws our json settings
					if ( ! empty( $id ) ) {
						global $wpdb;
						$tablename = $wpdb->prefix . 'posts';
						$r         = $wpdb->update(
							$tablename,
							array(
								'post_content_filtered' => json_encode( $csp5_template ),
							),
							array( 'ID' => $id ),
							array(
								'%s',
							),
							array( '%d' )
						);
					}
				}
			}
			
			update_option( 'seedprod_migration_run_once', true );
		}
	} catch ( Exception $e ) {
		return $e;
	}
}


function seedprod_pro_migrate_v5_settings( $csp5_page_settings, $csp5_template, $page_uuid, $page_id ) {

		// set logo
	if ( ! empty( $csp5_page_settings['logo'] ) ) {
		$csp5_template['document']['sections'][0]['rows'][0]['cols'][0]['blocks'][0]['settings']['src'] = $csp5_page_settings['logo'];
	}

	if ( ! empty( $csp5_page_settings['headline'] ) ) {
		$csp5_template['document']['sections'][0]['rows'][0]['cols'][0]['blocks'][1]['settings']['headerTxt'] = $csp5_page_settings['headline'];
	}

	if ( ! empty( $csp5_page_settings['description'] ) ) {
		$csp5_template['document']['sections'][0]['rows'][0]['cols'][0]['blocks'][3]['settings']['txt'] = $csp5_page_settings['description'];
	}

	if ( ! empty( $csp5_page_settings['favicon'] ) ) {
		$csp5_template['header_scripts'] .= '<link href="' . esc_attr( $csp5_page_settings['favicon'] ) . '" rel="shortcut icon" type="image/x-icon" />';
	}

	if ( ! empty( $csp5_page_settings['seo_description'] ) ) {
		$csp5_template['header_scripts'] .= '<meta name="description" content="' . esc_attr( $csp5_page_settings['seo_description'] ) . '">';
	}

	if ( ! empty( $csp5_page_settings['ga_analytics'] ) ) {
		$csp5_template['footer_scripts'] = $csp5_page_settings['ga_analytics'];
	}

	if ( ! empty( $csp5_page_settings['background_color'] ) ) {
		$csp5_template['document']['settings']['bgColor'] = $csp5_page_settings['background_color'];
	}

	if ( ! empty( $csp5_page_settings['background_image'] ) ) {
		$csp5_template['document']['settings']['bgImage'] = $csp5_page_settings['background_image'];
	}

	if ( ! empty( $csp5_page_settings['enable_background_overlay'] ) && ! empty( $csp5_page_settings['background_overlay'] ) ) {
		sscanf( $csp5_page_settings['background_overlay'], 'rgba(%d,%d,%d,%f)', $r, $g, $b, $a );
		if ( ! empty( $csp5_page_settings['background_overlay'] ) ) {
			$csp5_template['document']['settings']['bgDimming'] = $a * 100;
		}
	}

	if ( ! empty( $csp5_page_settings['background_size'] ) ) {
		if ( $csp5_page_settings['background_size'] == 'auto' ) {
			$csp5_template['document']['settings']['bgPosition'] = '';
			if ( ! empty( $csp5_page_settings['background_repeat'] ) ) {
				if ( $csp5_page_settings['background_repeat'] == 'repeat' ) {
					$csp5_template['document']['settings']['bgPosition'] = 'repeat';
				}
				if ( $csp5_page_settings['background_repeat'] == 'repeat-x' ) {
					$csp5_template['document']['settings']['bgPosition'] = 'repeattop';
				}
				if ( $csp5_page_settings['background_repeat'] == 'repeat-y' ) {
					$csp5_template['document']['settings']['bgPosition'] = 'repeatvc';
				}
			}
		}
		if ( $csp5_page_settings['background_size'] == 'cover' ) {
			$csp5_template['document']['settings']['bgPosition'] = 'cover';
		}
		if ( $csp5_page_settings['background_size'] == 'contain' ) {
			$csp5_template['document']['settings']['bgPosition'] = 'full';
		}
	}

	if ( ! empty( $csp5_page_settings['bg_video'] ) ) {
		$csp5_template['document']['settings']['useVideoBg'] = true;
	}

	if ( ! empty( $csp5_page_settings['bg_video_url'] ) ) {
		$csp5_template['document']['settings']['useVideoBgUrl'] = $csp5_page_settings['bg_video_url'];
	}

	if ( ! empty( $csp5_page_settings['bg_slideshow'] ) ) {
		$csp5_template['document']['settings']['useSlideshowBg'] = true;
	}

	if ( ! empty( $csp5_page_settings['bg_slideshow_images'] ) ) {
		foreach ( $bg_slideshow_images as $v ) {
			$csp5_template['document']['settings']['useSlideshowImgs'][] = $v;
		}
	}

	if ( empty( $csp5_page_settings['container_transparent'] ) ) {
		$csp5_template['document']['sections'][0]['settings']['bgColor']        = $csp5_page_settings['container_color'];
		$csp5_template['document']['sections'][0]['settings']['borderRadiusTL'] = $csp5_page_settings['container_radius'];
	} else {
		$csp5_template['document']['sections'][0]['settings']['bgColor'] = '';
	}

	if ( ! empty( $csp5_page_settings['container_position'] ) ) {
		if ( $csp5_page_settings['container_position'] == '1' ) {
			$csp5_template['document']['settings']['contentPosition'] = 4;
		}
		if ( $csp5_page_settings['container_position'] == '2' ) {
			$csp5_template['document']['settings']['contentPosition'] = 1;
		}
		if ( $csp5_page_settings['container_position'] == '3' ) {
			$csp5_template['document']['settings']['contentPosition'] = 7;
		}
		if ( $csp5_page_settings['container_position'] == '4' ) {
			$csp5_template['document']['settings']['contentPosition'] = 5;
		}
		if ( $csp5_page_settings['container_position'] == '5' ) {
			$csp5_template['document']['settings']['contentPosition'] = 2;
		}
		if ( $csp5_page_settings['container_position'] == '6' ) {
			$csp5_template['document']['settings']['contentPosition'] = 8;
		}
		if ( $csp5_page_settings['container_position'] == '7' ) {
			$csp5_template['document']['settings']['contentPosition'] = 6;
		}
		if ( $csp5_page_settings['container_position'] == '8' ) {
			$csp5_template['document']['settings']['contentPosition'] = 3;
		}
		if ( $csp5_page_settings['container_position'] == '9' ) {
			$csp5_template['document']['settings']['contentPosition'] = 9;
		}
	}

	if ( ! empty( $csp5_page_settings['container_width'] ) ) {
		$csp5_template['document']['sections'][0]['settings']['width'] = $csp5_page_settings['container_width'];
	}

	if ( ! empty( $csp5_page_settings['button_color'] ) ) {
		$csp5_template['document']['settings']['buttonColor'] = $csp5_page_settings['button_color'];
		$csp5_template['document']['settings']['linkColor']   = $csp5_page_settings['button_color'];
	}

	# TODO Set background color and flatness
	if ( empty( $csp5_page_settings['container_flat'] ) ) {
		$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['btnStyle'] = '3d';
	}
	if ( ! empty( $csp5_page_settings['form_color'] ) ) {
		$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['fieldBGColor']     = $csp5_page_settings['form_color'];
		$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['fieldBorderColor'] = $csp5_page_settings['form_color'];
	}

	if ( ! empty( $csp5_page_settings['text_font'] ) ) {
		$csp5_template['document']['settings']['textFontVariant'] = 400;
		if ( ! empty( $csp5_page_settings['text_font'] ) ) {
			$csp5_template['document']['settings']['textFontVariant'] = $csp5_page_settings['text_font'];
		}

		$csp5_template['document']['settings']['textFont'] = str_replace( "'", '', $csp5_page_settings['text_font'] );

		if ( $csp5_page_settings['text_font'] == 'Helvetica, Arial, sans-serif' ) {
			$csp5_template['document']['settings']['textFont'] = "'Helvetica Neue', Arial, sans-serif";
		}

		if ( $csp5_page_settings['text_font'] == "Arial Black', Gadget, sans-serif" ) {
			$csp5_template['document']['settings']['textFont']        = "'Helvetica Neue', Arial, sans-serif";
			$csp5_template['document']['settings']['textFontVariant'] = '700';
		}

		if ( $csp5_page_settings['text_font'] == "Bookman Old Style', serif" ) {
			$csp5_template['document']['settings']['textFont'] = 'Georgia, serif';
		}

		if ( $csp5_page_settings['text_font'] == "'Comic Sans MS', cursive" ) {
			$csp5_template['document']['settings']['textFont'] = "'Comic Sans MS', cursive";
		}

		if ( $csp5_page_settings['text_font'] == 'Courier, monospace' ) {
			$csp5_template['document']['settings']['textFont'] = 'Courier, monospace';
		}

		if ( $csp5_page_settings['text_font'] == 'Garamond, serif' ) {
			$csp5_template['document']['settings']['textFont'] = 'Georgia, serif';
		}

		if ( $csp5_page_settings['text_font'] == 'Georgia, serif' ) {
			$csp5_template['document']['settings']['textFont'] = 'Georgia, serif';
		}

		if ( $csp5_page_settings['text_font'] == 'Impact, Charcoal, sans-serif' ) {
			$csp5_template['document']['settings']['textFont'] = 'Impact, Charcoal, sans-serif';
		}

		if ( $csp5_page_settings['text_font'] == "'Lucida Console', Monaco, monospace" ) {
			$csp5_template['document']['settings']['textFont'] = 'Courier, monospace';
		}

		if ( $csp5_page_settings['text_font'] == "'Lucida Sans Unicode', 'Lucida Grande', sans-serif" ) {
			$csp5_template['document']['settings']['textFont'] = "'Helvetica Neue', Arial, sans-serif";
		}

		if ( $csp5_page_settings['text_font'] == "'MS Sans Serif', Geneva, sans-serif" ) {
			$csp5_template['document']['settings']['textFont'] = "'Helvetica Neue', Arial, sans-serif";
		}

		if ( $csp5_page_settings['text_font'] == "'MS Serif', 'New York', sans-serif" ) {
			$csp5_template['document']['settings']['textFont'] = "'Times New Roman', Times, serif";
		}

		if ( $csp5_page_settings['text_font'] == "'Palatino Linotype', 'Book Antiqua', Palatino, serif" ) {
			$csp5_template['document']['settings']['textFont'] = "'Helvetica Neue', Arial, sans-serif";
		}

		if ( $csp5_page_settings['text_font'] == 'Tahoma,Geneva, sans-serif' ) {
			$csp5_template['document']['settings']['textFont'] = 'Tahoma, Geneva, sans-serif';
		}

		if ( $csp5_page_settings['text_font'] == "'Times New Roman', Times,serif" ) {
			$csp5_template['document']['settings']['textFont'] = "'Times New Roman', Times, serif";
		}

		if ( $csp5_page_settings['text_font'] == "'Trebuchet MS', Helvetica, sans-serif" ) {
			$csp5_template['document']['settings']['textFont'] = "'Trebuchet MS', Helvetica, sans-serif";
		}

		if ( $csp5_page_settings['text_font'] == 'Verdana, Geneva, sans-serif' ) {
			$csp5_template['document']['settings']['textFont'] = 'Verdana, Geneva, sans-serif';
		}
	}

	if ( ! empty( $csp5_page_settings['text_color'] ) ) {
		$csp5_template['document']['settings']['textColor'] = $csp5_page_settings['text_color'];
		$csp5_template['document']['sections'][0]['rows'][0]['cols'][0]['blocks'][3]['settings']['textColor'] = $csp5_page_settings['text_color'];
	}
	if ( ! empty( $csp5_page_settings['text_size'] ) ) {
		$csp5_template['document']['sections'][0]['rows'][0]['cols'][0]['blocks'][3]['settings']['fontSize'] = $csp5_page_settings['text_size'];
	}

	if ( ! empty( $csp5_page_settings['headline_font'] ) ) {
		$csp5_template['document']['settings']['headerFontVariant'] = 400;
		if ( ! empty( $csp5_page_settings['headline_font'] ) ) {
			$csp5_template['document']['settings']['headerFontVariant'] = $csp5_page_settings['headline_font'];
		}

		$csp5_template['document']['settings']['headerFont'] = str_replace( "'", '', $csp5_page_settings['headline_font'] );

		if ( $csp5_page_settings['headline_font'] == 'Helvetica, Arial, sans-serif' ) {
			$csp5_template['document']['settings']['headerFont'] = "'Helvetica Neue', Arial, sans-serif";
		}

		if ( $csp5_page_settings['headline_font'] == "Arial Black', Gadget, sans-serif" ) {
			$csp5_template['document']['settings']['headerFont']        = "'Helvetica Neue', Arial, sans-serif";
			$csp5_template['document']['settings']['headerFontVariant'] = '700';
		}

		if ( $csp5_page_settings['headline_font'] == "Bookman Old Style', serif" ) {
			$csp5_template['document']['settings']['headerFont'] = 'Georgia, serif';
		}

		if ( $csp5_page_settings['headline_font'] == "'Comic Sans MS', cursive" ) {
			$csp5_template['document']['settings']['headerFont'] = "'Comic Sans MS', cursive";
		}

		if ( $csp5_page_settings['headline_font'] == 'Courier, monospace' ) {
			$csp5_template['document']['settings']['headerFont'] = 'Courier, monospace';
		}

		if ( $csp5_page_settings['headline_font'] == 'Garamond, serif' ) {
			$csp5_template['document']['settings']['headerFont'] = 'Georgia, serif';
		}

		if ( $csp5_page_settings['headline_font'] == 'Georgia, serif' ) {
			$csp5_template['document']['settings']['headerFont'] = 'Georgia, serif';
		}

		if ( $csp5_page_settings['headline_font'] == 'Impact, Charcoal, sans-serif' ) {
			$csp5_template['document']['settings']['headerFont'] = 'Impact, Charcoal, sans-serif';
		}

		if ( $csp5_page_settings['headline_font'] == "'Lucida Console', Monaco, monospace" ) {
			$csp5_template['document']['settings']['headerFont'] = 'Courier, monospace';
		}

		if ( $csp5_page_settings['headline_font'] == "'Lucida Sans Unicode', 'Lucida Grande', sans-serif" ) {
			$csp5_template['document']['settings']['headerFont'] = "'Helvetica Neue', Arial, sans-serif";
		}

		if ( $csp5_page_settings['headline_font'] == "'MS Sans Serif', Geneva, sans-serif" ) {
			$csp5_template['document']['settings']['headerFont'] = "'Helvetica Neue', Arial, sans-serif";
		}

		if ( $csp5_page_settings['headline_font'] == "'MS Serif', 'New York', sans-serif" ) {
			$csp5_template['document']['settings']['headerFont'] = "'Times New Roman', Times, serif";
		}

		if ( $csp5_page_settings['headline_font'] == "'Palatino Linotype', 'Book Antiqua', Palatino, serif" ) {
			$csp5_template['document']['settings']['headerFont'] = "'Helvetica Neue', Arial, sans-serif";
		}

		if ( $csp5_page_settings['headline_font'] == 'Tahoma,Geneva, sans-serif' ) {
			$csp5_template['document']['settings']['headerFont'] = 'Tahoma, Geneva, sans-serif';
		}

		if ( $csp5_page_settings['headline_font'] == "'Times New Roman', Times,serif" ) {
			$csp5_template['document']['settings']['headerFont'] = "'Times New Roman', Times, serif";
		}

		if ( $csp5_page_settings['headline_font'] == "'Trebuchet MS', Helvetica, sans-serif" ) {
			$csp5_template['document']['settings']['headerFont'] = "'Trebuchet MS', Helvetica, sans-serif";
		}

		if ( $csp5_page_settings['headline_font'] == 'Verdana, Geneva, sans-serif' ) {
			$csp5_template['document']['settings']['headerFont'] = 'Verdana, Geneva, sans-serif';
		}
	}

	if ( ! empty( $csp5_page_settings['headline_color'] ) ) {
		$csp5_template['document']['settings']['headerColor'] = $csp5_page_settings['headline_color'];
		$csp5_template['document']['sections'][0]['rows'][0]['cols'][0]['blocks'][1]['settings']['textColor'] = $csp5_page_settings['headline_color'];
	}
	if ( ! empty( $csp5_page_settings['headline_size'] ) ) {
		$csp5_template['document']['sections'][0]['rows'][0]['cols'][0]['blocks'][1]['settings']['fontSize'] = $csp5_page_settings['headline_size'];
	}

	if ( ! empty( $csp5_page_settings['button_font'] ) ) {
		$csp5_template['document']['settings']['textFontVariant'] = 400;
		if ( ! empty( $csp5_page_settings['button_font'] ) ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['fontVariant'] = $csp5_page_settings['button_font'];
		}

		$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['font'] = str_replace( "'", '', $csp5_page_settings['button_font'] );

		if ( $csp5_page_settings['button_font'] == 'Helvetica, Arial, sans-serif' ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['font'] = "'Helvetica Neue', Arial, sans-serif";
		}

		if ( $csp5_page_settings['button_font'] == "Arial Black', Gadget, sans-serif" ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['font']        = "'Helvetica Neue', Arial, sans-serif";
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['fontVariant'] = '700';
		}

		if ( $csp5_page_settings['button_font'] == "Bookman Old Style', serif" ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['font'] = 'Georgia, serif';
		}

		if ( $csp5_page_settings['button_font'] == "'Comic Sans MS', cursive" ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['font'] = "'Comic Sans MS', cursive";
		}

		if ( $csp5_page_settings['button_font'] == 'Courier, monospace' ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['font'] = 'Courier, monospace';
		}

		if ( $csp5_page_settings['button_font'] == 'Garamond, serif' ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['font'] = 'Georgia, serif';
		}

		if ( $csp5_page_settings['button_font'] == 'Georgia, serif' ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['font'] = 'Georgia, serif';
		}

		if ( $csp5_page_settings['button_font'] == 'Impact, Charcoal, sans-serif' ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['font'] = 'Impact, Charcoal, sans-serif';
		}

		if ( $csp5_page_settings['button_font'] == "'Lucida Console', Monaco, monospace" ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['font'] = 'Courier, monospace';
		}

		if ( $csp5_page_settings['button_font'] == "'Lucida Sans Unicode', 'Lucida Grande', sans-serif" ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['font'] = "'Helvetica Neue', Arial, sans-serif";
		}

		if ( $csp5_page_settings['button_font'] == "'MS Sans Serif', Geneva, sans-serif" ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['font'] = "'Helvetica Neue', Arial, sans-serif";
		}

		if ( $csp5_page_settings['button_font'] == "'MS Serif', 'New York', sans-serif" ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['font'] = "'Times New Roman', Times, serif";
		}

		if ( $csp5_page_settings['button_font'] == "'Palatino Linotype', 'Book Antiqua', Palatino, serif" ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['font'] = "'Helvetica Neue', Arial, sans-serif";
		}

		if ( $csp5_page_settings['button_font'] == 'Tahoma,Geneva, sans-serif' ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['font'] = 'Tahoma, Geneva, sans-serif';
		}

		if ( $csp5_page_settings['button_font'] == "'Times New Roman', Times,serif" ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['font'] = "'Times New Roman', Times, serif";
		}

		if ( $csp5_page_settings['button_font'] == "'Trebuchet MS', Helvetica, sans-serif" ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['font'] = "'Trebuchet MS', Helvetica, sans-serif";
		}

		if ( $csp5_page_settings['button_font'] == 'Verdana, Geneva, sans-serif' ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['font'] = 'Verdana, Geneva, sans-serif';
		}
	}

	if ( ! empty( $csp5_page_settings['button_color'] ) ) {
		$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['textColor'] = $csp5_page_settings['button_color'];
	}
	if ( ! empty( $csp5_page_settings['button_size'] ) ) {
		$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['fontSize'] = $csp5_page_settings['button_size'];
	}

	// form

	if ( ! empty( $csp5_page_settings['enable_form'] ) ) {
		if ( ! empty( $csp5_page_settings['txt_email_field'] ) ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['items'][0]['label'] = $csp5_page_settings['txt_email_field'];
		}

		if ( ! empty( $csp5_page_settings['txt_name_field'] ) ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['items'][1]['label'] = $csp5_page_settings['txt_name_field'];
		}

		if ( ! empty( $csp5_page_settings['display_name'] ) ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['items'][1]['display'] = true;
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['items'][0]['width']   = 100;
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['btnWidth']            = 100;
		}

		if ( ! empty( $csp5_page_settings['require_name'] ) ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['items'][1]['required'] = true;
		}

		if ( ! empty( $csp5_page_settings['display_optin_confirm'] ) ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['items'][2]['display'] = true;

			if ( ! empty( $csp5_page_settings['optin_confirmation_text'] ) ) {
				$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['items'][2]['label'] = $csp5_page_settings['optin_confirmation_text'];
			}
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['items'][0]['width'] = 100;
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['btnWidth']          = 100;
		}

		if ( ! empty( $csp5_page_settings['txt_optin_confirmation_required'] ) ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['items'][2]['label'] = $csp5_page_settings['txt_optin_confirmation_required'];
		}

		if ( ! empty( $csp5_page_settings['txt_subscribe_button'] ) ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['btnTxt'] = $csp5_page_settings['txt_subscribe_button'];
		}

		#TODO recaptcha
		if ( ! empty( $csp5_page_settings['enable_recaptcha'] ) ) {
			$csp5_template['enable_recaptcha'] = true;
		} else {
			$csp5_template['enable_recaptcha'] = false;
		}

		#TODO import mailer
		try {
			// html form or shortcode
			if ( ! empty( $csp5_page_settings['emaillist'] ) && $csp5_page_settings['emaillist'] == 'htmlwebform' ) {
				$html = get_option( 'seed_cspv5_' . $page_id . '_htmlwebform' );
				if ( ! empty( $html ) ) {
					if ( ! empty( $html['html_integration'] ) ) {
						$legacy_html = $html['html_integration'];
					}
				}
			}

			// gravity forms
			if ( ! empty( $csp5_page_settings['emaillist'] ) && $csp5_page_settings['emaillist'] == 'formidable' ) {
				$gf = get_option( 'seed_cspv5_' . $page_id . '_gravityforms' );
				if ( ! empty( $gf ) ) {
					if ( ! empty( $gf['gravityforms_form_id'] ) ) {
						$legacy_html = '[gravityform id="' . $gf['gravityforms_form_id'] . '" title="false" description="false" ajax="true"]';
					}
				}
			}

			// formidable
			if ( ! empty( $csp5_page_settings['emaillist'] ) && $csp5_page_settings['emaillist'] == 'formidable' ) {
				$gf = get_option( 'seed_cspv5_' . $page_id . '_formidable' );
				if ( ! empty( $gf ) ) {
					if ( ! empty( $gf['formidable_form_id'] ) ) {
						$legacy_html = '[formidable id="' . $gf['formidable_form_id'] . '"]';
					}
				}
			}

			// ninja forms
			if ( ! empty( $csp5_page_settings['emaillist'] ) && $csp5_page_settings['emaillist'] == 'ninjaforms' ) {
				$gf = get_option( 'seed_cspv5_' . $page_id . '_ninjaforms' );
				if ( ! empty( $gf ) ) {
					if ( ! empty( $gf['ninjaforms_form_id'] ) ) {
						$legacy_html = '[ninja_form id="' . $gf['ninjaforms_form_id'] . '"]';
					}
				}
			}

			// sendy
			if ( ! empty( $csp5_page_settings['emaillist'] ) && $csp5_page_settings['emaillist'] == 'sendy' ) {
				$email_template_file = SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/email-template.json';
				$email_template      = json_decode( file_get_contents( $email_template_file ), true );

				// get credentials
				$mc = get_option( 'seed_cspv5_' . $page_id . '_sendy' );
				if ( ! empty( $mc ) ) {
					$mc = maybe_unserialize( $mc );

					// set credentials
					$email_template['integration_type']         = 'sendy';
					$email_template['sendy']['url']             = $mc['sendy_url'];
					$email_template['sendy']['api_key']         = $mc['sendy_api_key'];
					$email_template['sendy']['connection_name'] = '';
					$email_template['sendy']['list_id']         = $mc['sendy_list_id'];

					// set recaptcha
					$email_template['recaptcha']['enabled'] = $csp5_template['enable_recaptcha'];

					// set data
					$data = array(
						'api_token'        => get_option( 'seedprod_api_token' ),
						'integration_type' => 'sendy',
						'page_uuid'        => $page_uuid,
						'emdata'           => json_encode( $email_template ),
					);

					$headers = array();

					// Build the headers of the request.
					$headers = wp_parse_args(
						$headers,
						array(
							'Accept' => 'application/json',
						)
					);

					$url      = SEEDPROD_PRO_API_URL . 'set-emaillist';
					$response = wp_remote_post(
						$url,
						array(
							'timeout' => 5,
							'body'    => $data,
							'headers' => $headers,
						)
					);
					if ( ! is_wp_error( $response ) ) {
						$body = wp_remote_retrieve_body( $response );
					}
				}
			}

			// zapier
			if ( ! empty( $csp5_page_settings['emaillist'] ) && $csp5_page_settings['emaillist'] == 'zapier' ) {
				$email_template_file = SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/email-template.json';
				$email_template      = json_decode( file_get_contents( $email_template_file ), true );

				// get credentials
				$mc = get_option( 'seed_cspv5_' . $page_id . '_zapier' );
				if ( ! empty( $mc ) ) {
					$mc = maybe_unserialize( $mc );

					// set credentials
					$email_template['integration_type']          = 'zapier';
					$email_template['zapier']['enabled']         = true;
					$email_template['zapier']['url']             = $mc['zapier_url'];
					$email_template['zapier']['connection_name'] = '';

					// set recaptcha
					$email_template['recaptcha']['enabled'] = $csp5_template['enable_recaptcha'];

					// set data
					$data = array(
						'api_token'        => get_option( 'seedprod_api_token' ),
						'integration_type' => 'zapier',
						'page_uuid'        => $page_uuid,
						'emdata'           => json_encode( $email_template ),
					);

					$headers = array();

					// Build the headers of the request.
					$headers = wp_parse_args(
						$headers,
						array(
							'Accept' => 'application/json',
						)
					);

					$url      = SEEDPROD_PRO_API_URL . 'set-emaillist';
					$response = wp_remote_post(
						$url,
						array(
							'timeout' => 5,
							'body'    => $data,
							'headers' => $headers,
						)
					);
					if ( ! is_wp_error( $response ) ) {
						$body = wp_remote_retrieve_body( $response );
					}
				}
			}

			// mailchimp
			if ( ! empty( $csp5_page_settings['emaillist'] ) && $csp5_page_settings['emaillist'] == 'mailchimp' ) {
				$email_template_file = SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/email-template.json';
				$email_template      = json_decode( file_get_contents( $email_template_file ), true );

				// get credentials
				$mc = get_option( 'seed_cspv5_' . $page_id . '_mailchimp' );
				if ( ! empty( $mc ) ) {
					$mc       = maybe_unserialize( $mc );
					$mc_lists = get_transient( 'seed_cspv5_mailchimp_lists_' . $page_id );

					// set credentials
					$email_template['integration_type']             = 'mailchimp';
					$email_template['mailchimp']['api_key']         = $mc['mailchimp_api_key'];
					$email_template['mailchimp']['connection_name'] = '';
					$email_template['mailchimp']['list_id']         = $mc['mailchimp_listid'];
					$email_template['mailchimp']['lists']           = maybe_unserialize( $mc_lists );
					if ( ! empty( $mc['mailchimp_enable_double_optin'] ) ) {
						$email_template['mailchimp']['double_optin'] = true;
					}

					// set recaptcha
					$email_template['recaptcha']['enabled'] = $csp5_template['enable_recaptcha'];

					// set data
					$data = array(
						'api_token'        => get_option( 'seedprod_api_token' ),
						'integration_type' => 'mailchimp',
						'page_uuid'        => $page_uuid,
						'emdata'           => json_encode( $email_template ),
					);

					$headers = array();

					// Build the headers of the request.
					$headers = wp_parse_args(
						$headers,
						array(
							'Accept' => 'application/json',
						)
					);

					$url      = SEEDPROD_PRO_API_URL . 'set-emaillist';
					$response = wp_remote_post(
						$url,
						array(
							'timeout' => 5,
							'body'    => $data,
							'headers' => $headers,
						)
					);
					if ( ! is_wp_error( $response ) ) {
						$body = wp_remote_retrieve_body( $response );
					}
				}
			}

			// activecampaign
			if ( ! empty( $csp5_page_settings['emaillist'] ) && $csp5_page_settings['emaillist'] == 'activecampaign' ) {
				$email_template_file = SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/email-template.json';
				$email_template      = json_decode( file_get_contents( $email_template_file ), true );

				// get credentials
				$mc = get_option( 'seed_cspv5_' . $page_id . '_activecampaign' );
				if ( ! empty( $mc ) ) {
					$mc       = maybe_unserialize( $mc );
					$mc_lists = get_transient( 'seed_cspv5_activecampaign_lists_' . $page_id );

					// set credentials
					$email_template['integration_type']                  = 'activecampaign';
					$email_template['activecampaign']['api_key']         = $mc['activecampaign_api_key'];
					$email_template['activecampaign']['api_url']         = $mc['activecampaign_api_url'];
					$email_template['activecampaign']['connection_name'] = '';
					$email_template['activecampaign']['list_id']         = $mc['activecampaign_listid'];
					$email_template['activecampaign']['lists']           = maybe_unserialize( $mc_lists );

					// set recaptcha
					$email_template['recaptcha']['enabled'] = $csp5_template['enable_recaptcha'];

					// set data
					$data = array(
						'api_token'        => get_option( 'seedprod_api_token' ),
						'integration_type' => 'activecampaign',
						'page_uuid'        => $page_uuid,
						'emdata'           => json_encode( $email_template ),
					);

					$headers = array();

					// Build the headers of the request.
					$headers = wp_parse_args(
						$headers,
						array(
							'Accept' => 'application/json',
						)
					);

					$url      = SEEDPROD_PRO_API_URL . 'set-emaillist';
					$response = wp_remote_post(
						$url,
						array(
							'timeout' => 5,
							'body'    => $data,
							'headers' => $headers,
						)
					);
					if ( ! is_wp_error( $response ) ) {
						$body = wp_remote_retrieve_body( $response );
					}
				}
			}

			// aweber
			if ( ! empty( $csp5_page_settings['emaillist'] ) && $csp5_page_settings['emaillist'] == 'aweber' ) {
				$email_template_file = SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/email-template.json';
				$email_template      = json_decode( file_get_contents( $email_template_file ), true );

				// get credentials
				$mc = get_option( 'seed_cspv5_' . $page_id . '_aweber' );
				if ( ! empty( $mc ) ) {
					$mc       = maybe_unserialize( $mc );
					$mc_lists = get_transient( 'seed_cspv5_aweber_lists_' . $page_id );

					// set credentials
					$email_template['integration_type']             = 'aweber';
					$email_template['aweber']['authorization_code'] = $mc['aweber_authorization_code'];
					$email_template['aweber']['consumer_key']       = $mc['consumer_key'];
					$email_template['aweber']['consumer_secret']    = $mc['consumer_secret'];
					$email_template['aweber']['access_key']         = $mc['access_key'];
					$email_template['aweber']['access_secret']      = $mc['access_secret'];
					$email_template['aweber']['connection_name']    = '';
					$email_template['aweber']['list_id']            = $mc['aweber_listid'];
					$email_template['aweber']['lists']              = maybe_unserialize( $mc_lists );

					// set recaptcha
					$email_template['recaptcha']['enabled'] = $csp5_template['enable_recaptcha'];

					// set data
					$data = array(
						'api_token'        => get_option( 'seedprod_api_token' ),
						'integration_type' => 'aweber',
						'page_uuid'        => $page_uuid,
						'emdata'           => json_encode( $email_template ),
					);

					$headers = array();

					// Build the headers of the request.
					$headers = wp_parse_args(
						$headers,
						array(
							'Accept' => 'application/json',
						)
					);

					$url      = SEEDPROD_PRO_API_URL . 'set-emaillist';
					$response = wp_remote_post(
						$url,
						array(
							'timeout' => 5,
							'body'    => $data,
							'headers' => $headers,
						)
					);
					if ( ! is_wp_error( $response ) ) {
						$body = wp_remote_retrieve_body( $response );
					}
				}
			}

			// campaignmonitor
			if ( ! empty( $csp5_page_settings['emaillist'] ) && $csp5_page_settings['emaillist'] == 'campaignmonitor' ) {
				$email_template_file = SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/email-template.json';
				$email_template      = json_decode( file_get_contents( $email_template_file ), true );

				// get credentials
				$mc = get_option( 'seed_cspv5_' . $page_id . '_campaignmonitor' );
				if ( ! empty( $mc ) ) {
					$mc       = maybe_unserialize( $mc );
					$mc_lists = get_transient( 'seed_cspv5_campaignmonitor_lists_' . $page_id );

					// set credentials
					$email_template['integration_type']                   = 'campaignmonitor';
					$email_template['campaignmonitor']['api_key']         = $mc['campaignmonitor_api_key'];
					$email_template['campaignmonitor']['client_id']       = $mc['campaignmonitor_client_id'];
					$email_template['campaignmonitor']['connection_name'] = '';
					$email_template['campaignmonitor']['list_id']         = $mc['campaignmonitor_listid'];
					$email_template['campaignmonitor']['lists']           = maybe_unserialize( $mc_lists );

					// set recaptcha
					$email_template['recaptcha']['enabled'] = $csp5_template['enable_recaptcha'];

					// set data
					$data = array(
						'api_token'        => get_option( 'seedprod_api_token' ),
						'integration_type' => 'campaignmonitor',
						'page_uuid'        => $page_uuid,
						'emdata'           => json_encode( $email_template ),
					);

					$headers = array();

					// Build the headers of the request.
					$headers = wp_parse_args(
						$headers,
						array(
							'Accept' => 'application/json',
						)
					);

					$url      = SEEDPROD_PRO_API_URL . 'set-emaillist';
					$response = wp_remote_post(
						$url,
						array(
							'timeout' => 5,
							'body'    => $data,
							'headers' => $headers,
						)
					);
					if ( ! is_wp_error( $response ) ) {
						$body = wp_remote_retrieve_body( $response );
					}
				}
			}

			// convertkit
			if ( ! empty( $csp5_page_settings['emaillist'] ) && $csp5_page_settings['emaillist'] == 'convertkit' ) {
				$email_template_file = SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/email-template.json';
				$email_template      = json_decode( file_get_contents( $email_template_file ), true );

				// get credentials
				$mc = get_option( 'seed_cspv5_' . $page_id . '_convertkit' );
				if ( ! empty( $mc ) ) {
					$mc       = maybe_unserialize( $mc );
					$mc_lists = get_transient( 'seed_cspv5_convertkit_lists_' . $page_id );

					// set credentials
					$email_template['integration_type']              = 'convertkit';
					$email_template['convertkit']['api_key']         = $mc['convertkit_api_key'];
					$email_template['convertkit']['api_secret']      = $mc['convertkit_api_secret'];
					$email_template['convertkit']['connection_name'] = '';
					$email_template['convertkit']['list_id']         = $mc['convertkit_form_listid'];
					$email_template['convertkit']['lists']           = maybe_unserialize( $mc_lists );

					// set recaptcha
					$email_template['recaptcha']['enabled'] = $csp5_template['enable_recaptcha'];

					// set data
					$data = array(
						'api_token'        => get_option( 'seedprod_api_token' ),
						'integration_type' => 'convertkit',
						'page_uuid'        => $page_uuid,
						'emdata'           => json_encode( $email_template ),
					);

					$headers = array();

					// Build the headers of the request.
					$headers = wp_parse_args(
						$headers,
						array(
							'Accept' => 'application/json',
						)
					);

					$url      = SEEDPROD_PRO_API_URL . 'set-emaillist';
					$response = wp_remote_post(
						$url,
						array(
							'timeout' => 5,
							'body'    => $data,
							'headers' => $headers,
						)
					);
					if ( ! is_wp_error( $response ) ) {
						$body = wp_remote_retrieve_body( $response );
					}
				}
			}

			// drip
			if ( ! empty( $csp5_page_settings['emaillist'] ) && $csp5_page_settings['emaillist'] == 'drip' ) {
				$email_template_file = SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/email-template.json';
				$email_template      = json_decode( file_get_contents( $email_template_file ), true );

				// get credentials
				$mc = get_option( 'seed_cspv5_' . $page_id . '_drip' );
				if ( ! empty( $mc ) ) {
					$mc       = maybe_unserialize( $mc );
					$mc_lists = get_transient( 'seed_cspv5_drip_lists_' . $page_id );

					// set credentials
					$email_template['integration_type']        = 'drip';
					$email_template['drip']['api_token']       = $mc['drip_api_token'];
					$email_template['drip']['account_id']      = $mc['drip_account_id'];
					$email_template['drip']['connection_name'] = '';
					$email_template['drip']['list_id']         = $mc['drip_listid'];
					$email_template['drip']['lists']           = maybe_unserialize( $mc_lists );

					// set recaptcha
					$email_template['recaptcha']['enabled'] = $csp5_template['enable_recaptcha'];

					// set data
					$data = array(
						'api_token'        => get_option( 'seedprod_api_token' ),
						'integration_type' => 'drip',
						'page_uuid'        => $page_uuid,
						'emdata'           => json_encode( $email_template ),
					);

					$headers = array();

					// Build the headers of the request.
					$headers = wp_parse_args(
						$headers,
						array(
							'Accept' => 'application/json',
						)
					);

					$url      = SEEDPROD_PRO_API_URL . 'set-emaillist';
					$response = wp_remote_post(
						$url,
						array(
							'timeout' => 5,
							'body'    => $data,
							'headers' => $headers,
						)
					);
					if ( ! is_wp_error( $response ) ) {
						$body = wp_remote_retrieve_body( $response );
					}
				}
			}

			// getresponse
			if ( ! empty( $csp5_page_settings['emaillist'] ) && $csp5_page_settings['emaillist'] == 'getresponse' ) {
				$email_template_file = SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/email-template.json';
				$email_template      = json_decode( file_get_contents( $email_template_file ), true );

				// get credentials
				$mc = get_option( 'seed_cspv5_' . $page_id . '_getresponse' );
				if ( ! empty( $mc ) ) {
					$mc       = maybe_unserialize( $mc );
					$mc_lists = get_transient( 'seed_cspv5_getresponse_lists_' . $page_id );

					// set credentials
					$email_template['integration_type']               = 'getresponse';
					$email_template['getresponse']['api_key']         = $mc['getresponse_api_key'];
					$email_template['getresponse']['connection_name'] = '';
					$email_template['getresponse']['list_id']         = $mc['getresponse_listid'];
					$email_template['getresponse']['lists']           = maybe_unserialize( $mc_lists );

					// set recaptcha
					$email_template['recaptcha']['enabled'] = $csp5_template['enable_recaptcha'];

					// set data
					$data = array(
						'api_token'        => get_option( 'seedprod_api_token' ),
						'integration_type' => 'getresponse',
						'page_uuid'        => $page_uuid,
						'emdata'           => json_encode( $email_template ),
					);

					$headers = array();

					// Build the headers of the request.
					$headers = wp_parse_args(
						$headers,
						array(
							'Accept' => 'application/json',
						)
					);

					$url      = SEEDPROD_PRO_API_URL . 'set-emaillist';
					$response = wp_remote_post(
						$url,
						array(
							'timeout' => 5,
							'body'    => $data,
							'headers' => $headers,
						)
					);
					if ( ! is_wp_error( $response ) ) {
						$body = wp_remote_retrieve_body( $response );
					}
				}
			}

			// icontact
			if ( ! empty( $csp5_page_settings['emaillist'] ) && $csp5_page_settings['emaillist'] == 'icontact' ) {
				$email_template_file = SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/email-template.json';
				$email_template      = json_decode( file_get_contents( $email_template_file ), true );

				// get credentials
				$mc = get_option( 'seed_cspv5_' . $page_id . '_icontact' );
				if ( ! empty( $mc ) ) {
					$mc       = maybe_unserialize( $mc );
					$mc_lists = get_transient( 'seed_cspv5_icontact_lists_' . $page_id );

					// set credentials
					$email_template['integration_type']            = 'icontact';
					$email_template['icontact']['username']        = $mc['icontact_username'];
					$email_template['icontact']['password']        = $mc['icontact_password'];
					$email_template['icontact']['connection_name'] = '';
					$email_template['icontact']['list_id']         = $mc['icontact_listid'];
					$email_template['icontact']['lists']           = maybe_unserialize( $mc_lists );

					// set recaptcha
					$email_template['recaptcha']['enabled'] = $csp5_template['enable_recaptcha'];

					// set data
					$data = array(
						'api_token'        => get_option( 'seedprod_api_token' ),
						'integration_type' => 'icontact',
						'page_uuid'        => $page_uuid,
						'emdata'           => json_encode( $email_template ),
					);

					$headers = array();

					// Build the headers of the request.
					$headers = wp_parse_args(
						$headers,
						array(
							'Accept' => 'application/json',
						)
					);

					$url      = SEEDPROD_PRO_API_URL . 'set-emaillist';
					$response = wp_remote_post(
						$url,
						array(
							'timeout' => 5,
							'body'    => $data,
							'headers' => $headers,
						)
					);
					if ( ! is_wp_error( $response ) ) {
						$body = wp_remote_retrieve_body( $response );
					}
				}
			}

			// madmimi
			if ( ! empty( $csp5_page_settings['emaillist'] ) && $csp5_page_settings['emaillist'] == 'madmimi' ) {
				$email_template_file = SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/email-template.json';
				$email_template      = json_decode( file_get_contents( $email_template_file ), true );

				// get credentials
				$mc = get_option( 'seed_cspv5_' . $page_id . '_madmimi' );
				if ( ! empty( $mc ) ) {
					$mc       = maybe_unserialize( $mc );
					$mc_lists = get_transient( 'seed_cspv5_madmimi_lists_' . $page_id );

					// set credentials
					$email_template['integration_type']           = 'madmimi';
					$email_template['madmimi']['username']        = $mc['madmimi_username'];
					$email_template['madmimi']['api_key']         = $mc['madmimi_api_key'];
					$email_template['madmimi']['connection_name'] = '';
					$email_template['madmimi']['list_id']         = $mc['madmimi_listid'];
					$email_template['madmimi']['lists']           = maybe_unserialize( $mc_lists );

					// set recaptcha
					$email_template['recaptcha']['enabled'] = $csp5_template['enable_recaptcha'];

					// set data
					$data = array(
						'api_token'        => get_option( 'seedprod_api_token' ),
						'integration_type' => 'madmimi',
						'page_uuid'        => $page_uuid,
						'emdata'           => json_encode( $email_template ),
					);

					$headers = array();

					// Build the headers of the request.
					$headers = wp_parse_args(
						$headers,
						array(
							'Accept' => 'application/json',
						)
					);

					$url      = SEEDPROD_PRO_API_URL . 'set-emaillist';
					$response = wp_remote_post(
						$url,
						array(
							'timeout' => 5,
							'body'    => $data,
							'headers' => $headers,
						)
					);
					if ( ! is_wp_error( $response ) ) {
						$body = wp_remote_retrieve_body( $response );
					}
				}
			}
		} catch ( Exception $e ) {
		}

		if ( ! empty( $csp5_page_settings['thankyou_msg'] ) ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0]['settings']['thankyouTxt'] = $csp5_page_settings['thankyou_msg'];
		}
	}

	// add contact form
	if ( ! empty( $csp5_page_settings['enable_cf_form'] ) && ! empty( $csp5_page_settings['cf_form_emails'] ) ) {
		$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][10]['settings']['txt']       = "<a href='mailto:" . $csp5_page_settings['cf_form_emails'] . "'>Contact Us</a>";
		$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][10]['settings']['textColor'] = $csp5_page_settings['text_color'];
	}

	#social profiles

	if ( ! empty( $csp5_page_settings['enable_socialprofiles'] ) ) {
		$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][5]['settings']['items'] = array();
		foreach ( $csp5_page_settings['social_profiles'] as $v ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][5]['settings']['items'][] = array(
				'type'  => str_replace( 'fa-', '', $v['icon'] ),
				'label' => '',
				'url'   => $v['url'],
			);
		}

		if ( ! empty( $csp5_page_settings['socialprofile_color'] ) ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][5]['settings']['iconColor'] = $csp5_page_settings['socialprofile_color'];
		}

		if ( ! empty( $csp5_page_settings['social_profiles_size'] ) ) {
			if ( $csp5_page_settings['social_profiles_size'] == 'fa-lg' ) {
				$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][5]['settings']['Size'] = 16;
			}
			if ( $csp5_page_settings['social_profiles_size'] == 'fa-2x' ) {
				$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][5]['settings']['Size'] = 24;
			}
			if ( $csp5_page_settings['social_profiles_size'] == 'fa-3x' ) {
				$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][5]['settings']['Size'] = 32;
			}
			if ( $csp5_page_settings['social_profiles_size'] == 'fa-4x' ) {
				$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][5]['settings']['Size'] = 32;
			}
			if ( $csp5_page_settings['social_profiles_size'] == 'fa-5x' ) {
				$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][5]['settings']['Size'] = 32;
			}
		}
	}

	// social sharing buttons
	if ( ! empty( $csp5_page_settings['enable_socialbuttons'] ) ) {
		$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][7]['settings']['items'] = array();

		if ( ! empty( $csp5_page_settings['share_buttons']['facebook'] ) ) {
			if ( $v['icon'] == 'fa-facebook-official' ) {
				$v['icon'] = 'fa-facebook';
			}
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][7]['settings']['items'][] = array(
				'type'  => 'facebook',
				'url'   => 'facebook',
				'label' => '',
			);
		}

		if ( ! empty( $csp5_page_settings['share_buttons']['twitter'] ) ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][7]['settings']['items'][] = array(
				'type'  => 'twitter',
				'url'   => 'facebook',
				'meta'  => $csp5_page_settings['tweet_text'],
				'label' => '',
			);
		}

		if ( ! empty( $csp5_page_settings['share_buttons']['linkedin'] ) ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][7]['settings']['items'][] = array(
				'type'  => 'linkedin',
				'url'   => 'linkedin',
				'label' => '',
			);
		}

		if ( ! empty( $csp5_page_settings['share_buttons']['pinterest'] ) ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][7]['settings']['items'][] = array(
				'type'  => 'pinterest',
				'url'   => 'pinterest',
				'meta'  => $csp5_page_settings['pinterest_thumbnail'],
				'label' => '',
			);
		}
	}

	// privacy policy
	$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][1]['settings']['textColor'] = $csp5_page_settings['text_color'];

	if ( ! empty( $csp5_page_settings['privacy_policy_link_text'] ) ) {
		$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][1]['settings']['txt'] = $csp5_page_settings['privacy_policy_link_text'];
	}

	// countdown

	if ( ! empty( $csp5_page_settings['enable_countdown'] ) ) {
		if ( $csp5_page_settings['countdown_timezone'] ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][3]['settings']['timezone'] = $csp5_page_settings['countdown_timezone'];
		}

		if ( $csp5_page_settings['countdown_date'] ) {
			$parsed_ctd = date_parse( $csp5_page_settings['countdown_date'] );
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][3]['settings']['endDate'] = $parsed_ctd['year'] . '-' . $parsed_ctd['month'] . '-' . $parsed_ctd['day'];

			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][3]['settings']['endTime'] = $parsed_ctd['hour'] . ':00';

			$ends           = $parsed_ctd['year'] . '-' . $parsed_ctd['month'] . '-' . $parsed_ctd['day'] . ' ' . $parsed_ctd['hour'] . ':00';
			$ends_timestamp = strtotime( $ends . ' ' . $csp5_page_settings['countdown_timezone'] );

			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][3]['settings']['endTimestamp'] = $ends_timestamp;

			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][3]['settings']['labelColor'] = $csp5_page_settings['text_color'];
		}

		if ( ! empty( $csp5_page_settings['txt_countdown_days'] ) ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][3]['settings']['dayTxt'] = $csp5_page_settings['txt_countdown_days'];
		}

		if ( ! empty( $csp5_page_settings['txt_countdown_hours'] ) ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][3]['settings']['hourTxt'] = $csp5_page_settings['txt_countdown_hours'];
		}

		if ( ! empty( $csp5_page_settings['txt_countdown_minutes'] ) ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][3]['settings']['minuteTxt'] = $csp5_page_settings['txt_countdown_minutes'];
		}

		if ( ! empty( $csp5_page_settings['txt_countdown_seconds'] ) ) {
			$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][3]['settings']['secondTxt'] = $csp5_page_settings['txt_countdown_seconds'];
		}
	}

	// progress bar

	if ( ! empty( $csp5_page_settings['enable_progressbar'] ) ) {
		$a = 0;
		if ( ! empty( $csp5_page_settings['progressbar_percentage'] ) ) {
			$a = $csp5_page_settings['progressbar_percentage'];
		}
		$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][9]['settings']['txt'] = $a . '%';

		$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][9]['settings']['width'] = $a;
	}

	// legacy html
	if ( ! empty( $legacy_html ) ) {
		$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][11]['settings']['code'] = $legacy_html;
		// move blocks
		$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0] = $csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][11];
		unset( $csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][11] );
	}

	// footer credit
	if ( ! empty( $csp5_page_settings['enable_footercredit'] ) ) {
		$csp5_template['show_powered_by_link'] = true;
		if ( ! empty( $csp5_page_settings['footer_affiliate_link'] ) ) {
			$csp5_template['affiliate_url'] = $csp5_page_settings['footer_affiliate_link'];
		}
	} else {
		$csp5_template['show_powered_by_link'] = false;
	}

	// custom css
	if ( ! empty( $csp5_page_settings['custom_css'] ) ) {
		$csp5_template['document']['settings']['customCss'] .= $csp5_page_settings['custom_css'];
	}

	// header scripts
	if ( ! empty( $csp5_page_settings['header_scripts'] ) ) {
		$csp5_template['header_scripts'] .= $csp5_page_settings['header_scripts'];
	}

	// footer scripts
	if ( ! empty( $csp5_page_settings['footer_scripts'] ) ) {
		$csp5_template['footer_scripts'] .= $csp5_page_settings['footer_scripts'];
	}

	#TODO conversion scripts

	# remove unused blocks
	if ( empty( $csp5_page_settings['logo'] ) ) {
		unset( $csp5_template['document']['sections'][0]['rows'][0]['cols'][0]['blocks'][0] );
	}

	if ( empty( $csp5_page_settings['headline'] ) ) {
		unset( $csp5_template['document']['sections'][0]['rows'][0]['cols'][0]['blocks'][1] );
	}

	if ( empty( $csp5_page_settings['description'] ) ) {
		unset( $csp5_template['document']['sections'][0]['rows'][0]['cols'][0]['blocks'][3] );
	}

	$csp5_template['document']['sections'][0]['rows'][0]['cols'][0]['blocks'] = array_values( $csp5_template['document']['sections'][0]['rows'][0]['cols'][0]['blocks'] );

	if ( empty( $csp5_page_settings['enable_form'] ) ) {
		unset( $csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][0] );
		unset( $csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][1] );
	}

	if ( empty( $csp5_page_settings['enable_cf_form'] ) ) {
		unset( $csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][10] );
	}

	if ( empty( $csp5_page_settings['enable_socialprofiles'] ) ) {
		unset( $csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][5] );
	}

	if ( empty( $csp5_page_settings['enable_socialbuttons'] ) ) {
		unset( $csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][7] );
	}

	if ( empty( $csp5_page_settings['enable_countdown'] ) ) {
		unset( $csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][3] );
	}

	if ( empty( $csp5_page_settings['enable_progressbar'] ) ) {
		unset( $csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][9] );
	}

	if ( empty( $legacy_html ) ) {
		unset( $csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'][11] );
	}

	$csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'] = array_values( $csp5_template['document']['sections'][0]['rows'][1]['cols'][0]['blocks'] );

	#record unsupport feature
	// rm - reditrct mode
	// rl - referral link
	// pz - prizes
	// ml - multilingual
	// ar - auto responder
	// ht - edit html
	// pb - progress bar start and end date
	// al - auto launch
	// fc - footer credit custom image or text
	if ( ! empty( $csp5_page_settings['enable_reflink'] ) ) {
		$seedprod_unsupported_feature[] = 'rl';
	}
	if ( ! empty( $csp5_page_settings['enable_prize_levels'] ) ) {
		$seedprod_unsupported_feature[] = 'pz';
	}
	if ( ! empty( get_option( 'seed_cspv5_1_language' ) ) ) {
		$seedprod_unsupported_feature[] = 'ml';
	}
	if ( ! empty( get_option( 'seed_cspv5_1_autoresponder' ) ) ) {
		$seedprod_unsupported_feature[] = 'ar';
	}
	if ( ! empty( $page->html ) ) {
		$seedprod_unsupported_feature[] = 'ht';
	}
	if ( ! empty( $csp5_page_settings['enable_progressbar'] ) && ! empty( $csp5_page_settings['progress_bar_method'] ) && $csp5_page_settings['progress_bar_method'] == 'date' ) {
		$seedprod_unsupported_feature[] = 'pb';
	}
	if ( ! empty( $csp5_page_settings['countdown_launch'] ) ) {
		$seedprod_unsupported_feature[] = 'al';
	}
	if ( ! empty( $csp5_page_settings['enable_footercredit'] ) && ! empty( $csp5_page_settings['credit_type'] ) && ( $csp5_page_settings['credit_type'] == 'text' || $csp5_page_settings['credit_type'] == 'image' ) ) {
		$seedprod_unsupported_feature[] = 'fc';
	}
	if ( ! empty( $seedprod_unsupported_feature ) ) {
		update_option( 'seedprod_unsupported_feature', $seedprod_unsupported_feature );
	}

	return $csp5_template;
}

$seedprod_cspv5_settings = get_option( 'seed_cspv5_settings_content' );
if ( ! empty( $seedprod_cspv5_settings ) ) {
	try {
		global $wpdb;
		$wpdb->suppress_errors = true;
		$tablename             = $wpdb->prefix . 'cspv5_pages';
		$path                  = rtrim( ltrim( $_SERVER['REQUEST_URI'], '/' ), '/' );
		$path                  = preg_replace( '/\?.*/', '', $path );



		$url = home_url();

		$r = array_intersect( explode( '/', $path ), explode( '/', $url ) );

		$path = str_replace( $r, '', $path );

		$path = str_replace( '/', '', $path );

		if ( ! empty( $path ) ) {
			$sql      = "SELECT * FROM $tablename WHERE path = %s";
			$safe_sql = $wpdb->prepare( $sql, $path );
			$path     = $wpdb->get_row( $safe_sql );

			if ( ! empty( $path ) && $path->meta != 'migrated' ) {
				global $seedprod_legacy_lp_path;
				$seedprod_legacy_lp_path = $path->id;
				require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/cspv5-functions.php';
				add_action( 'init', 'seedprod_pro_cspv5_remove_ngg_print_scripts' );
				if ( function_exists( 'bp_is_active' ) ) {
					add_action( 'template_redirect', 'seedprod_pro_cspv5_render_landing_page', 9 );
				} else {
					$priority = 10;
					if ( function_exists( 'tve_frontend_enqueue_scripts' ) ) {
						$priority = 8;
					}
					add_action( 'template_redirect', 'seedprod_pro_cspv5_render_landing_page', $priority );
				}
			}
		}
	} catch ( Exception $e ) {
		//echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
}

