<?php

/**
 * Theme Template Render
 */
function seedprod_pro_setup_theme_override() {
	// enable preview mode if user want to show current theme while working new theme other wise just override if the plugin is active.
	$preview_mode_enabled   = get_option( 'seedprod_theme_template_preview_mode' );
	$seedprod_theme_enabled = get_option( 'seedprod_theme_enabled' );

	// filter to allow theme to be display on certain pages
	$allow_theme_display = apply_filters( 'seedprod_allow_theme_display', false );

	if ( ! empty( $seedprod_theme_enabled ) && false === $allow_theme_display ) {
		if ( ( $preview_mode_enabled && is_user_logged_in() ) || empty( $preview_mode_enabled ) ) {
			if ( $preview_mode_enabled ) {
				add_action( 'admin_bar_menu', 'seedprod_pro_admin_bar_menu', 999 );
			}
			// allow acf shortcode to work in block themes
			add_filter( 'acf/shortcode/allow_in_block_themes_outside_content', '__return_true' );
			// seedprod filters
			add_action( 'get_header', 'seedprod_pro_header_hook', PHP_INT_MAX );
			add_action( 'get_footer', 'seedprod_pro_footer_hook', PHP_INT_MAX );
			add_action( 'get_sidebar', 'seedprod_pro_sidebar_hook', PHP_INT_MAX );
			add_action( 'comments_template', 'seedprod_pro_comments_hook', PHP_INT_MAX );
			add_action( 'template_include', 'seedprod_template_include_override', PHP_INT_MAX );
			add_filter( 'show_admin_bar', 'seedprod_pro_filter_admin_bar_from_body_open' );
			add_filter( 'validate_current_theme', '__return_false' );
			add_action( 'wp_enqueue_scripts', 'seedprod_pro_deregister_theme_styles', PHP_INT_MAX );
			add_action( 'setup_theme', 'seedprod_pro_add_theme_support' );
			add_action( 'after_setup_theme', 'seedprod_pro_remove_theme_support', 99 );
			add_filter( 'template_directory', 'seedprod_pro_disable_theme_load', 1, 1 );
			add_filter( 'stylesheet_directory', 'seedprod_pro_disable_theme_load', 1, 1 );
			add_filter( 'body_class', 'seedprod_pro_body_class_layouts' );
			add_action( 'wp_enqueue_scripts', 'seedprod_pro_theme_template_enqueue_styles' );
			add_action( 'register_sidebar', 'seedprod_pro_widget_headers' );
			add_filter( 'embed_oembed_html', 'seedprod_pro_video_wrapper', 10, 4 );
			add_filter( 'embed_oembed_html', 'seedprod_pro_video' );
			// add_filter( 'the_content', 'seedprod_pro_edited_with_seedprod_the_content', 1 );
		}
	}
}
add_action( 'plugins_loaded', 'seedprod_pro_setup_theme_override' );
if ( defined( 'DOING_AJAX' ) ) {
	add_action( 'wp_ajax_seedprod_pro_get_template_part', 'seedprod_pro_get_template_part' );
}

/**
 * Override Theme Name so WooCommerce does not load default theme assets.
 */
// $seedprod_theme_enabled = get_option( 'seedprod_theme_enabled');
// if (!is_admin()) {
// if (!empty($seedprod_theme_enabled)) {
// if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
// add_filter('template', 'seedprod_pro_override_template');
// function seedprod_pro_override_template($template)
// {
// return 'seedprod';
// }
// }
// }
// }


/**
 * Show cleaned content (inline styles removed from code) if edited with SeedProd.
 */
function seedprod_pro_edited_with_seedprod_the_content( $content ) {
	$current_post_id         = get_the_ID();
	$current_post_type       = get_post_type();
	$is_edited_with_seedprod = get_post_meta( $current_post_id, '_seedprod_edited_with_seedprod', true );
	if ( 'page' === $current_post_type && ! empty( $is_edited_with_seedprod ) ) {
		$code = get_post_meta( $current_post_id, '_seedprod_html', true );
		if ( ! empty( $code ) ) {
			$content = $code;
		}
	}
	return $content;
}


/**
 * Responsive Videos
 */
function seedprod_pro_video_wrapper( $html, $url, $attr, $post_id ) {
	if ( strpos( $html, 'youtube.com' ) !== false || strpos( $html, 'youtu.be' ) !== false ) {
		return '<div class="embed-responsive  embed-responsive-16by9">' . $html . '</div>';
	} else {
		return $html;
	}
}


/**
 * Responsive Videos
 */
function seedprod_pro_video( $code ) {
	return str_replace( '<iframe', '<iframe class="embed-responsive-item"  ', $code );
};

/**
 * Override get_header()
 */
function seedprod_pro_widget_headers( $sidebar ) {
		global $wp_registered_sidebars;

		$id                      = $sidebar['id'];
		$sidebar['before_title'] = '<h3 class="widget-title">';
		$sidebar['after_title']  = '</h3>';

		$wp_registered_sidebars[ $id ] = $sidebar; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
};

/**
 * Override get_header()
 */
function seedprod_pro_header_hook( $name ) {
	require SEEDPROD_PRO_PLUGIN_PATH . 'resources/theme-template-views/header.php';

	$templates = array();
	$name      = (string) $name;
	if ( '' !== $name ) {
		$templates[] = "header-{$name}.php";
	}

	$templates[] = 'header.php';

	// Avoid running wp_head hooks again
	remove_all_actions( 'wp_head' );
	ob_start();
	// It cause a `require_once` so, in the get_header it self it will not be required again.
	@locate_template( $templates, true ); // phpcs:ignore 
	ob_get_clean();
}


/**
 * Override get_footer()
 */
function seedprod_pro_footer_hook( $name ) {
	require SEEDPROD_PRO_PLUGIN_PATH . 'resources/theme-template-views/footer.php';

	$templates = array();
	$name      = (string) $name;
	if ( '' !== $name ) {
		$templates[] = "footer-{$name}.php";
	}

	$templates[] = 'footer.php';

	ob_start();
	// It cause a `require_once` so, in the get_footer it self it will not be required again.
	@locate_template( $templates, true ); // phpcs:ignore
	ob_get_clean();
}

/**
 * Override get_sidebar()
 */
function seedprod_pro_sidebar_hook( $name ) {
	require SEEDPROD_PRO_PLUGIN_PATH . 'resources/theme-template-views/sidebar.php';

	$templates = array();
	$name      = (string) $name;
	if ( '' !== $name ) {
		$templates[] = "sidebar-{$name}.php";
	}

	$templates[] = 'sidebar.php';

	ob_start();
	// It cause a `require_once` so, in the get_sidebar it self it will not be required again.
	@locate_template( $templates, true ); // phpcs:ignore
	ob_get_clean();
}

/**
 * Override comments_template()
 */
function seedprod_pro_comments_hook( $name ) {
	global $post;

	// Check if woocommerce is installed and active.
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		if ( 'product' === $post->post_type ) {
			// wc_get_template('single-product-reviews.php); - doesn't work???
			return SEEDPROD_PRO_PLUGIN_PATH . 'resources/theme-template-views/single-product-reviews.php';
		}
	}

	return SEEDPROD_PRO_PLUGIN_PATH . 'resources/theme-template-views/comments.php';
}


/**
 * Overide The Template
 */
function seedprod_template_include_override( $template ) {
	// var_dump($template);
	// var_dump(is_paged());
	global $seedprod_theme_requirements;
	get_the_theme_parts_requirements();

	// Exclude landing pages and theme template parts
	$excluded_types        = array(
		'lp',
		'cs',
		'mm',
		'p404',
		'loginp',
	);
	$is_excluded_page_type = false;
	global $post;
	if ( ! empty( $post->post_content_filtered ) ) {
		$settings = json_decode( $post->post_content_filtered );
		if ( ! empty( $settings->page_type ) ) {
			if ( in_array( $settings->page_type, $excluded_types ) ) {
				$is_excluded_page_type = true;
			}
		}
	}

	if ( isset( $_GET['s'] ) && empty( $_GET['s'] ) ) { // phpcs:ignore  WordPress.Security.NonceVerification.Recommended
		$is_excluded_page_type = false;
	}

	if ( ! empty( $post->post_type ) && 'seedprod' === $post->post_type ) {
		$is_excluded_page_type = true;
	}

	// exclude plugin templates filter
	$excluded_template = apply_filters( 'seedprod_excluded_template', false, $template );
	if ( false !== $excluded_template ) {
		$is_excluded_page_type = true;
	}

	// know post types to exclude
	$excluded_posttypes = array( 'mpcs-course', 'mpcs-lesson', 'mpcs-quiz' );

	// filter to exclude post types
	$excluded_posttypes = apply_filters( 'seedprod_excluded_posttypes', $excluded_posttypes );

	if ( false === $is_excluded_page_type ) {
			$compare_post_type = '';
		if ( ! empty( $post->post_type ) ) {
			$compare_post_type = $post->post_type;
		}
		if ( ! in_array( $compare_post_type, $excluded_posttypes ) ) {
			$template = SEEDPROD_PRO_PLUGIN_PATH . 'resources/theme-template-views/canvas.php';
		}
	} elseif ( true === $is_excluded_page_type && is_search() ) {
		// add for landing page search
		$template = SEEDPROD_PRO_PLUGIN_PATH . 'resources/theme-template-views/canvas.php';
	}
	return $template;
}


/**
 * Show admin bar
 */
function seedprod_pro_filter_admin_bar_from_body_open( $show_admin_bar ) {
	global $wp_current_filter;

	static $switched = false;

	if ( $show_admin_bar && in_array( 'wp_body_open', $wp_current_filter ) ) {
		$show_admin_bar = false;
		$switched       = true;
	} elseif ( $switched ) {
		$show_admin_bar = true;
	}

	return $show_admin_bar;
}

/**
 * Remove other plugin's style from our page so they don't conflict
 */
function seedprod_pro_deregister_theme_styles() {
	$theme = wp_get_theme();

	$theme_name       = $theme->template;
	$child_theme_name = $theme->stylesheet;

	global $wp_styles;

	$default_themes = array(
		'twentyten',
		'twentyeleven',
		'twentytwelve',
		'twentythirteen',
		'twentyfourteen',
		'twentyfifteen',
		'twentysixteen',
		'twentyseventeen',
		'twentynineteen',
		'twentytwenty',
		'twentytwentyone',
		'twentytwentytwo',
	);
	// deregister theme's styles
	foreach ( $wp_styles->queue as $handle ) {
		if ( strpos( $wp_styles->registered[ $handle ]->src, 'wp-content/themes' ) !== false ) {
			// var_dump($wp_styles->registered[$handle]->src);
			wp_dequeue_style( $handle );
			wp_deregister_style( $handle );
		}
		if ( 'woocommerce-general' == $wp_styles->registered[ $handle ]->handle ) {
			// var_dump($wp_styles->registered[$handle]->src);
			wp_dequeue_style( $handle );
			wp_deregister_style( $handle );
		}
	}

	// deregister theme's styles
	foreach ( $wp_styles->registered as $registered ) {
		if ( strpos( $registered->src, $theme_name ) != false ) {
			wp_dequeue_style( $registered->handle );
			wp_deregister_style( $registered->handle );
		}
		if ( strpos( $registered->src, $child_theme_name ) != false ) {
			wp_dequeue_style( $registered->handle );
			wp_deregister_style( $registered->handle );
		}
	}

	// deregister theme's scripts
	global $wp_scripts;
	foreach ( $wp_scripts->registered as $registered ) {
		if ( strpos( $registered->src, $theme_name ) != false ) {
			wp_dequeue_script( $registered->handle );
			wp_deregister_script( $registered->handle );
		}
		if ( strpos( $registered->src, $child_theme_name ) != false ) {
			wp_dequeue_script( $registered->handle );
			wp_deregister_script( $registered->handle );
		}
	}
}



/**
 * Add seedprod theme support
 */
function seedprod_pro_add_theme_support() {
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'title-tag' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'post-thumbnails' );
}

/**
 * Add theme's theme support
 */
function seedprod_pro_remove_theme_support() {
	remove_theme_support( 'editor-styles' );
	remove_editor_styles();
}

/**
 * Menu for Theme
 */
function seedprod_pro_register_my_menus() {
	register_nav_menus(
		array(
			'block-menu' => __( 'SP Block Menu' ),
		)
	);
}
add_action( 'init', 'seedprod_pro_register_my_menus' );

/**
 * Disable Parent and Child Theme
 */
function seedprod_pro_disable_theme_load( $stylesheet_dir ) {
	$disable_theme_load = apply_filters( 'seedprod_disable_theme_load', true );
	if ( true === $disable_theme_load ) {
		return 'seedprod';
	} else {
		return $stylesheet_dir;
	}

}

/**
 * Add seedprod body class
 */
function seedprod_pro_body_class_layouts( $classes ) {
	$theme = wp_get_theme();

	$theme_name       = $theme->template;
	$child_theme_name = $theme->stylesheet;

	foreach ( $classes as $k => $v ) {
		if ( strpos( $v, $theme_name ) != false ) {
			unset( $classes[ $k ] );
		}
		if ( strpos( $v, $child_theme_name ) != false ) {
			unset( $classes[ $k ] );
		}
	}

	$classes[] = 'theme-seedprod';

	return $classes;
}


/**
 * get settings of pages by page id.
 */
function seedprod_pro_get_theme_template_by_page_id( $id ) {
	global $post, $wpdb;

	$all_settings     = '';
	$post_id          = $id;
	$tablename        = $wpdb->prefix . 'posts';
	$sql              = "SELECT post_content_filtered,post_modified FROM $tablename WHERE ID = %d";
	$safe_sql         = $wpdb->prepare( $sql, absint( $post_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$all_settings_row = $wpdb->get_row( $safe_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	if ( ! empty( $all_settings_row->post_content_filtered ) ) {
		$all_settings                     = json_decode( $all_settings_row->post_content_filtered );
		$all_settings->post_id            = $post_id;
		$all_settings->modified_timestamp = strtotime( $all_settings_row->post_modified );
	}
	return $all_settings;
}

/**
 * get template by type and conditions
 */
function seedprod_pro_get_theme_template_by_type_condition( $type, $id = false, $get_settings = false, $clean_code = false ) {
	global $post, $wpdb;

	// find theme template part by type sort by menu_order(priority) then loop and check conditions
	$tablename1      = $wpdb->prefix . 'posts';
	$tablename2      = $wpdb->prefix . 'postmeta';
	$sql             = "SELECT post_id,(Select meta_value from $tablename2 WHERE meta_key = '_seedprod_theme_template_condition' AND post_id = p.ID) conditions FROM $tablename1 p INNER JOIN $tablename2 pm ON p.ID = pm.post_id WHERE meta_value = %s AND meta_key = '_seedprod_page_template_type' AND post_status = 'publish' AND post_type = 'seedprod' ORDER BY menu_order DESC";
	$safe_sql        = $wpdb->prepare( $sql, $type ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$potential_pages = $wpdb->get_results( $safe_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

	$post_id = null;

	// allowed conditions
	$allowed_conditions = seedprod_pro_theme_template_conditons();
	$allowed_list       = array();
	foreach ( $allowed_conditions as $condition_group ) {
		foreach ( $condition_group as $condtion_value ) {
			$allowed_list[] = $condtion_value['value'];
		}
	}

	foreach ( $potential_pages  as $k => $v ) {
		// check conditons, if we meet exit loop and return template
		$conditions = array();
		if ( ! empty( json_decode( $v->conditions ) ) ) {
			$conditions          = new stdClass();
			$conditions->include = array();
			$conditions->exclude = array();
			$conditions->custom  = array();

			$conditions_raw = json_decode( $v->conditions );
			// group conditons
			foreach ( $conditions_raw as $k0 => $v0 ) {
				if ( 'include' === $v0->condition ) {
					$conditions->include[] = array(
						'type'  => $v0->type,
						'value' => $v0->value,
					);
				}
				if ( 'exclude' === $v0->condition ) {
					$conditions->exclude[] = array(
						'type'  => $v0->type,
						'value' => $v0->value,
					);
				}
				if ( 'custom' === $v0->condition ) {
					$conditions->custom[] = array(
						'type'  => $v0->type,
						'value' => $v0->value,
					);
				}
			}
		}

		$conditions_meet = false;
		$post_id         = null;

		// check if include conditions meet
		if ( ! empty( $conditions->include ) && is_array( $conditions->include ) ) {
			foreach ( $conditions->include as $k1 => $v1 ) {

				// var_dump(call_user_func(str_replace("(x)","",$v1['type']),$v1['value']));
				$values = explode( ',', $v1['value'] );
				if ( empty( $values[0] ) ) {
					$values = '';
				}

				// exit if condition not allowed
				if ( ! in_array( $v1['type'], $allowed_list ) ) {
					continue;
				}

				// look for post types
				$cond_func = str_replace( '(x)', '', $v1['type'] );
				if ( strpos( $cond_func, '(' ) !== false ) {
					preg_match( '#\((.*?)\)#', $cond_func, $match );
					$cond_func = str_replace( $match[0], '', $cond_func );
					$values    = $match[1];
				}

				// switch out is_product if id is passed in.
				if ( 'is_product' == $cond_func && ! empty( $values ) ) {
					$cond_func = 'is_single';
				}

				if ( strpos( $v1['type'], '_' ) != 0 && @call_user_func( $cond_func, $values ) ) { // phpcs:ignore
					// or logic
					$conditions_meet = true;
					break;
				} elseif ( strpos( $v1['type'], '_' ) == 0 ) {
					if ( '_entire_site' === $v1['type'] ) {
						$conditions_meet = true;
						break;
					}
				}
			}
		}

		// check if exclude conditions meet
		if ( ! empty( $conditions->exclude ) && is_array( $conditions->exclude ) ) {
			foreach ( $conditions->exclude as $k2 => $v2 ) {

				// var_dump(call_user_func(str_replace("(x)","",$v1['type']),$v1['value']));
				$values = explode( ',', $v2['value'] );
				if ( empty( $values[0] ) ) {
					$values = '';
				}

				// exit if condition not allowed
				if ( ! in_array( $v2['type'], $allowed_list ) ) {
					continue;
				}

				// look for post types
				$cond_func = str_replace( '(x)', '', $v2['type'] );
				if ( strpos( $cond_func, '(' ) !== false ) {
					preg_match( '#\((.*?)\)#', $cond_func, $match );
					$cond_func = str_replace( $match[0], '', $cond_func );
					$values    = $match[1];
				}

				// switch out is_product if id is passed in.
				if ( 'is_product' == $cond_func && ! empty( $values ) ) {
					$cond_func = 'is_single';
				}

				if ( strpos( $v2['type'], '_' ) != 0 && @call_user_func( $cond_func, $values ) ) { // phpcs:ignore
					// or logic
					$conditions_meet = false;
					break;
				} elseif ( strpos( $v2['type'], '_' ) == 0 ) {
					if ( '_entire_site' === $v2['type'] ) {
						$conditions_meet = false;
						break;
					}
				}
			}
		}

		// check custom condition
		// if ( ! empty( $conditions->custom ) ) {
		// #TODO - whitelist functions that can be passed in
		// try {
		// foreach ( $conditions->custom as $k3 => $v3 ) {
		// $cond_func = $v3['value'];

		// if ( strpos( $cond_func, '(' ) !== false ) {
		// preg_match( '#\((.*?)\)#', $cond_func, $match );
		// $cond_func = str_replace( $match[0], '', $cond_func );
		// $values    = $match[1];

		// $values = explode( ',', $values );
		// if ( empty( $values[0] ) ) {
		// $values = '';
		// }
		// }

		// if ( @call_user_func( $cond_func, $values ) ) {
		// $conditions_meet = true;
		// }
		// }
		// } catch ( Exception $e ) {
		// }
		// }

		// if no condition exists use it
		// if(empty($v->conditions) || $conditions_meet){
		if ( $conditions_meet ) {
			$post_id = $v->post_id;
			break;
		}
	}

	// wp_query example, let's us custom query so we know wtf the actual sql query is.
	// $args = array(
	// 'post_type' => 'seedprod',
	// 'meta_query' => array(
	// array(
	// 'key'     => '_seedprod_page_template_type',
	// 'value'   => 'header',
	// ),
	// array(
	// 'key' => '_seedprod_theme_template_condition',
	// 'value'   => 'post',
	// ),
	// ),
	// );
	// $the_query = new WP_Query( $args);
	// echo $the_query->request;
	// exit();

	// just return the id
	if ( true === $id ) {
		return $post_id;
	}

	// return the settings
	if ( true === $get_settings ) {
		$all_settings     = '';
		$tablename        = $wpdb->prefix . 'posts';
		$sql              = "SELECT post_content_filtered,post_modified FROM $tablename WHERE ID = %d";
		$safe_sql         = $wpdb->prepare( $sql, absint( $post_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$all_settings_row = $wpdb->get_row( $safe_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		if ( ! empty( $all_settings_row->post_content_filtered ) ) {
			$all_settings                     = json_decode( $all_settings_row->post_content_filtered );
			$all_settings->post_id            = $post_id;
			$all_settings->modified_timestamp = strtotime( $all_settings_row->post_modified );

			preg_match_all( '/\"templateparts":"(\d*?)"/', wp_json_encode( $all_settings ), $matches );

			if ( ! empty( $matches ) ) {
				if ( count( $matches ) > 0 ) {
					if ( isset( $matches[1] ) ) {
						foreach ( $matches[1] as $v ) {
							$all_settings->template_parts_id = $v;
						}
					}
				}
			}
		}
		return $all_settings;
	}

	// get code
	$code = '';
	if ( empty( $post_id ) ) {
		$status_code = http_response_code();
		if ( 404 === $status_code ) {
			$code = '<p class="sp-not-found">' . __( '404 - Page Not Found', 'seedprod-pro' ) . '</p>';
		} else {
			if ( 'page' === $type ) {
				$code = '<p class="sp-not-found">' . __( 'No Template Found', 'seedprod-pro' ) . '</p>';
			} else {
				$code = '';
			}
		}
	}
	if ( ! empty( $post_id ) ) {
		$tablename = $wpdb->prefix . 'posts';
		$sql       = "SELECT * FROM $tablename WHERE ID = %d";
		$safe_sql  = $wpdb->prepare( $sql, absint( $post_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$page      = $wpdb->get_row( $safe_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		// Check if page has content and seedprod is used
		if ( 'page' === $type ) {
			if ( ! empty( $page->post_content ) ) {
				// if post type is page and edited with seedprod return the content
				$current_post_id         = get_the_ID();
				$current_post_type       = get_post_type();
				$is_edited_with_seedprod = get_post_meta( $current_post_id, '_seedprod_edited_with_seedprod', true );
				if ( 'page' === $current_post_type && ! empty( $is_edited_with_seedprod ) && ! is_search() ) {
					// Check if page content is password protected.
					if ( post_password_required( $current_post_id ) ) {
						$code            = get_the_password_form( $page->ID );
						$current_post_id = get_the_ID();
					} else {
						if ( ! empty( $clean_code ) ) {
							$current_post_type       = get_post_type();
							$is_edited_with_seedprod = get_post_meta( $current_post_id, '_seedprod_edited_with_seedprod', true );
							$code                    = get_post_meta( $current_post_id, '_seedprod_html', true );
							ob_start();
						} else {
							the_content();
							$code            = ob_get_clean();
							$current_post_id = get_the_ID();
						}
					}
				} else {
					// get template code

					if ( have_posts() ) {
						while ( have_posts() ) {
							the_post();
							// get clean code
							if ( ! empty( $clean_code ) ) {
								$code = get_post_meta( $page->ID, '_seedprod_html', true );
							}
							if ( empty( $code ) ) {
								$code = do_shortcode( $page->post_content );
							}
						}
					} else {
						if ( ! empty( $clean_code ) ) {
							$code = get_post_meta( $page->ID, '_seedprod_html', true );
						}
						if ( empty( $code ) ) {
							$code = do_shortcode( $page->post_content );
						}
					}
				}
			} else {
				// render the_content
				ob_start();
				the_content();
				$code = ob_get_clean();
			}
		} else {
			// header and footer
			if ( ! empty( $page->post_content ) ) {
				if ( ! empty( $clean_code ) ) {
					$code = get_post_meta( $page->ID, '_seedprod_html', true );
				}
				if ( empty( $code ) ) {
					$code = do_shortcode( $page->post_content );
				}
			}
		}
	}

	// we need to process shortcode multiple time for embedded templates
	$code = do_shortcode( do_shortcode( $code ) );

	$code = apply_filters( 'seedprod_the_code', $code );
	// echo $page->post_title;
	return $code;
}

/**
 * enqueue css
 */
function seedprod_pro_theme_template_enqueue_styles() {
	global $seedprod_theme_requirements;
	if ( ! empty( $seedprod_theme_requirements ) ) {

		// woocommerce
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			wp_enqueue_style(
				'seedprod-woocommerce-layout',
				str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/css/woocommerce-layout.css',
				'',
				defined( 'WC_VERSION' ) ? WC_VERSION : null,
				'all'
			);
			wp_enqueue_style(
				'seedprod-woocommerce-smallscreen',
				str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/css/woocommerce-smallscreen.css',
				'',
				defined( 'WC_VERSION' ) ? WC_VERSION : null,
				'only screen and (max-width: 1088px)' // 768px default break + 320px for sidebar
			);
			wp_enqueue_style(
				'seedprod-woocommerce-general',
				str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/css/woocommerce.css',
				'',
				defined( 'WC_VERSION' ) ? WC_VERSION : null,
				'all'
			);
		}

		// theme base styles
		wp_enqueue_style(
			'seedprod-style',
			SEEDPROD_PRO_PLUGIN_URL . 'public/css/tailwind.min.css',
			false,
			SEEDPROD_PRO_VERSION
		);

		// font awesome
		wp_enqueue_style(
			'seedprod-font-awesome',
			SEEDPROD_PRO_PLUGIN_URL . 'public/fontawesome/css/all.min.css',
			false,
			SEEDPROD_PRO_VERSION
		);

		// theme global & parts css
		// get the global css last modified date
		$global_css_page_id = get_option( 'seedprod_global_css_page_id' );
		global $wpdb;
		$tablename                 = $wpdb->prefix . 'posts';
		$sql                       = "SELECT post_modified FROM $tablename WHERE id= %d";
		$safe_sql                  = $wpdb->prepare( $sql, absint( $global_css_page_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$global_css_page_timestamp = $wpdb->get_var( $safe_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$css_dir                   = trailingslashit( wp_upload_dir()['baseurl'] ) . 'seedprod-css/';
		// do not render global css if landing pages
		$current_post_id   = get_the_ID();
		$landing_page_type = get_post_meta( $current_post_id, '_seedprod_page_template_type', true );
		$lps_to_exclude    = array( 'lp', 'cs', 'mm', 'p404', 'loginp' );
		if ( ! in_array( $landing_page_type, $lps_to_exclude ) ) {
			wp_enqueue_style(
				'seedprod-css-global',
				$css_dir . 'style-global.css',
				false,
				strtotime( $global_css_page_timestamp )
			);
		}

		// page css
		$current_post_type       = get_post_type();
		$is_edited_with_seedprod = get_post_meta( $current_post_id, '_seedprod_edited_with_seedprod', true );
		if ( 'page' === $current_post_type && ! empty( $is_edited_with_seedprod ) ) {
			wp_enqueue_style(
				'seedprod-css-' . $current_post_id,
				$css_dir . 'style-' . $current_post_id . '.css',
				false,
				get_post_modified_time()
			);
		}

		// part ids to check for google fonts
		$part_ids_google_fonts = array( $current_post_id, $global_css_page_id );

		// get theme parts
		foreach ( $seedprod_theme_requirements as $k => $v ) {
			if ( strpos( $v, 'css:' ) === 0 ) {
				$css_files = explode( ':', $v );
				if ( ! empty( $css_files[1] ) ) {
					$css_files_arr = explode( ',', $css_files[1] );
					foreach ( $css_files_arr as $k1 => $v1 ) {
						$css_files_parts         = explode( '|', $v1 );
						$part_ids_google_fonts[] = $css_files_parts[0];
						if ( ! empty( $css_files_parts[0] ) ) {
							wp_enqueue_style(
								'seedprod-css-' . $css_files_parts[0],
								$css_dir . 'style-' . $css_files_parts[0] . '.css',
								false,
								$css_files_parts[1]
							);
						}
					}
				}
			}
		}

		// check if post or theme parts need to enqueue google fonts
		$allow_google_fonts = apply_filters( 'seedprod_allow_google_fonts', true );
		if ( $allow_google_fonts ) {
			foreach ( $part_ids_google_fonts as $part_id ) {
				// get settings
				$tablename         = $wpdb->prefix . 'posts';
				$sql               = "SELECT post_content_filtered FROM $tablename WHERE id = %d";
				$safe_sql          = $wpdb->prepare( $sql, absint( $part_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				$goog_page_setting = $wpdb->get_var( $safe_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				if ( ! empty( $goog_page_setting ) ) {
					$goog_page_setting = json_decode( $goog_page_setting, true );
					$google_fonts_str  = seedprod_pro_construct_font_str( $goog_page_setting );
					wp_enqueue_style(
						'seedprod-google-fonts-' . $part_id,
						$google_fonts_str,
						array(),
						SEEDPROD_PRO_VERSION
					);
				}
			}
		}

		if ( in_array( 'lightboxmedia', $seedprod_theme_requirements ) ) {

			wp_enqueue_script(
				'seedprod-lightbox-js',
				SEEDPROD_PRO_PLUGIN_URL . 'public/js/lightbox.min.js',
				array( 'jquery' ),
				SEEDPROD_PRO_VERSION,
				true
			);
			wp_enqueue_style(
				'seedprod-lightbox-css',
				SEEDPROD_PRO_PLUGIN_URL . 'public/css/lightbox.min.css',
				false,
				SEEDPROD_PRO_VERSION
			);
		}

		// animate headline scripts
		if ( in_array( 'animatedheadline', $seedprod_theme_requirements ) ) {
			wp_enqueue_script(
				'seedprod-lettering-js',
				SEEDPROD_PRO_PLUGIN_URL . 'public/js/jquery.lettering.min.js',
				array( 'jquery' ),
				SEEDPROD_PRO_VERSION,
				true
			);
			wp_enqueue_script(
				'seedprod-textillate',
				SEEDPROD_PRO_PLUGIN_URL . 'public/js/jquery.textillate.min.js',
				array( 'jquery' ),
				SEEDPROD_PRO_VERSION,
				true
			);
			wp_enqueue_script(
				'seedprod-animation',
				SEEDPROD_PRO_PLUGIN_URL . 'public/js/jquery.animation.min.js',
				array( 'jquery' ),
				SEEDPROD_PRO_VERSION,
				true
			);
			wp_enqueue_style(
				'seedprod-animation-css',
				SEEDPROD_PRO_PLUGIN_URL . 'public/css/sp-animate.min.css',
				false,
				SEEDPROD_PRO_VERSION
			);
		}

		// entrance animation css and scripts
		if ( in_array( 'animatedblocks', $seedprod_theme_requirements ) ) {
			wp_enqueue_style(
				'seedprod-entrance-animate-css',
				SEEDPROD_PRO_PLUGIN_URL . 'public/css/animate.css',
				false,
				SEEDPROD_PRO_VERSION
			);
			wp_register_script(
				'seedprod-entrance-animation-dynamic-css',
				SEEDPROD_PRO_PLUGIN_URL . 'public/js/animate-dynamic.js',
				array( 'jquery-core' ),
				SEEDPROD_PRO_VERSION,
				true
			);
			wp_enqueue_script( 'seedprod-entrance-animation-dynamic-css' );
		}

		// gallery scripts
		if ( in_array( 'seedprodgallery', $seedprod_theme_requirements ) || in_array( 'seedprodbasicgallery', $seedprod_theme_requirements ) ) {

			wp_enqueue_script(
				'seedprod-textillate',
				SEEDPROD_PRO_PLUGIN_URL . 'public/js/img-previewer.min.js',
				array( 'jquery' ),
				SEEDPROD_PRO_VERSION,
				true
			);

			wp_enqueue_style(
				'seedprod-builder-lightbox-index',
				SEEDPROD_PRO_PLUGIN_URL . 'public/css/seedprod-gallery-block.min.css',
				false,
				SEEDPROD_PRO_VERSION
			);

		}
		// dyanmic text
		if ( in_array( 'dynamictext', $seedprod_theme_requirements ) ) {
			wp_enqueue_script(
				'seedprod-lettering',
				SEEDPROD_PRO_PLUGIN_URL . 'public/js/dynamic-text.js',
				array( 'jquery' ),
				SEEDPROD_PRO_VERSION,
				true
			);
		}
		// before after toggle scripts
		if ( in_array( 'beforeaftertoggle', $seedprod_theme_requirements ) ) {

			wp_enqueue_script(
				'seedprod-eventmove',
				SEEDPROD_PRO_PLUGIN_URL . 'public/js/jquery.event.move.js',
				array( 'jquery' ),
				SEEDPROD_PRO_VERSION,
				true
			);
			wp_enqueue_script(
				'seedprod-twentytwenty-slider',
				SEEDPROD_PRO_PLUGIN_URL . 'public/js/jquery.twentytwenty.min.js',
				array( 'jquery' ),
				SEEDPROD_PRO_VERSION,
				true
			);

			wp_enqueue_style(
				'seedprod-twentytwenty-css',
				SEEDPROD_PRO_PLUGIN_URL . 'public/css/before-after-toggle.min.css',
				false,
				SEEDPROD_PRO_VERSION
			);

		}

		// Counter block
		if ( in_array( 'counter', $seedprod_theme_requirements ) ) {
			wp_enqueue_script(
				'seedprod-jquery-numerator',
				SEEDPROD_PRO_PLUGIN_URL . 'public/js/jquery-numerator.min.js',
				array( 'jquery' ),
				SEEDPROD_PRO_VERSION,
				true
			);
		}

		// Hotspot block scripts.
		if ( in_array( 'hotspot', $seedprod_theme_requirements ) ) {
			wp_enqueue_script(
				'seedprod-hotspot-tooltipster-js',
				SEEDPROD_PRO_PLUGIN_URL . 'public/js/tooltipster.bundle.min.js',
				array( 'jquery' ),
				SEEDPROD_PRO_VERSION,
				true
			);

			wp_enqueue_style(
				'seedprod-hotspot-tooltipster-css',
				SEEDPROD_PRO_PLUGIN_URL . 'public/css/tooltipster.bundle.min.css',
				false,
				SEEDPROD_PRO_VERSION
			);
		}

		// particles background js
		if ( in_array( 'particlesbackground', $seedprod_theme_requirements ) ) {
			wp_enqueue_script(
				'seedprod-tsparticles-js',
				SEEDPROD_PRO_PLUGIN_URL . 'public/js/tsparticles.min.js',
				array( 'jquery' ),
				SEEDPROD_PRO_VERSION,
				true
			);
		}

		// general scripts
		wp_enqueue_script(
			'seedprod-scripts',
			SEEDPROD_PRO_PLUGIN_URL . 'public/js/sp-scripts.min.js',
			array( 'jquery' ),
			SEEDPROD_PRO_VERSION,
			true
		);
	}
}

/**
 * Get header or footer template part for builder
 */
function seedprod_pro_get_template_part() {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}
		$return = array();
		if ( ! empty( $_POST['type'] ) ) {
			$type           = sanitize_text_field( wp_unslash( $_POST['type'] ) );
			$code           = seedprod_pro_get_theme_template_by_type_condition( $type );
			$id             = seedprod_pro_get_theme_template_by_type_condition( $type, true );
			$return['code'] = $code;
			$return['id']   = $id;
		}
		wp_send_json( $return );
	}
	exit();
}

/**
 * Get template tags for builder
 */
// function seedprod_pro_render_builder_template_tags() {
// if ( check_ajax_referer( 'seedprod_nonce' ) ) {
// $post_id = absint( $_POST['id'] );
// $tag     = sanitize_text_field( $_POST['tag'] );
// echo call_user_func( 'get_the_title', $post_id );
// }
// exit();
// }

/**
 * Render Shortcodes and Template Tags
 */
function seedprod_pro_render_template_tags_shortcode( $atts ) {
	$a = shortcode_atts(
		array(
			'tag'  => '',
			'id'   => '',
			'echo' => false,
			'loop' => false,
		),
		$atts
	);

	$code = '';

	$tag_allow_list = array(
		'the_post_thumbnail',
		'get_avatar',
		'the_title',
		'the_excerpt',
		'the_author_meta(display_name)',
		'the_author_meta(description)',
		'the_date(F j, Y)',
		'the_date(Y-m-d)',
		'the_date(d/m/Y)',
		'the_date(m/d/Y)',
		'the_content',
		'comments_template',
		'get_previous_post_link',
		'get_next_post_link',
		'the_archive_title',
		'the_modified_date(F j, Y)',
		'the_modified_date(Y-m-d)',
		'the_modified_date(m/d/Y)',
		'the_modified_date(d/m/Y)',
		'the_time(g:i a)',
		'the_time(g:i A)',
		'the_time(H:i)',
		'the_modified_time(g:i a)',
		'the_modified_time(g:i A)',
		'the_modified_time(H:i)',
		'get_comments_number',
		'the_tags',
		'the_category',
		'the_custom_logo',
		'home_url',
		'get_author_posts_url',
	);

	// If tag not allowed return empty string.
	if ( ! in_array( $a['tag'], $tag_allow_list ) ) {
		// error_log( 'Unallowed Tag: ' . $a['tag'] );
		return '';
	}

	// render template tag
	if ( ! empty( $a['tag'] ) ) {
		$values  = null;
		$values2 = null;

		if ( strpos( $a['tag'], '(' ) !== false ) {
			preg_match( '#\((.*?)\)#', $a['tag'], $match );
			$a['tag'] = str_replace( $match[0], '', $a['tag'] );
			$values   = $match[1];
		}
		if ( 'the_post_thumbnail' === $a['tag'] ) {
			$values2 = array( 'alt' => get_the_title() );
		}
		if ( 'get_avatar' === $a['tag'] ) {
			$values = get_the_author_meta( 'user_email' );
		}
		if ( 'get_previous_post_link' === $a['tag'] ) {
			$prev_icon  = '<svg class="sp-postnavigation-previous-icon" width="24" height="24" aria-hidden="true" role="img" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M20 13v-2H8l4-4-1-2-7 7 7 7 1-2-4-4z" fill="currentColor"></path></svg>';
			$prev_label = '<span class="sp-postnavigation-previous-label">' . $prev_icon . ' Previous</span>';
			$prev_title = '<span class="sp-postnavigation-previous-title">%title</span>';
			$values     = array( '%link', '<span class="sp-postnavigation-previous">' . $prev_label . $prev_title . '</span>', false, '', 'category' );
		}
		if ( 'get_next_post_link' === $a['tag'] ) {
			$next_icon  = '<svg class="sp-postnavigation-next-icon" width="24" height="24" aria-hidden="true" role="img" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="m4 13v-2h12l-4-4 1-2 7 7-7 7-1-2 4-4z" fill="currentColor"></path></svg>';
			$next_label = '<span class="sp-postnavigation-next-label">Next ' . $next_icon . ' </span>';
			$next_title = '<span class="sp-postnavigation-next-title">%title</span>';
			$values     = array( '%link', '<span class="sp-postnavigation-next">' . $next_label . $next_title . '</span>', false, '', 'category' );
		}
		if ( 'get_author_posts_url' === $a['tag'] ) {
			$values = get_the_author_meta( 'ID' );
		}
		if ( 'get_day_link' === $a['tag'] ) {
			$values = array( get_post_time( 'Y' ), get_post_time( 'm' ), get_post_time( 'j' ) );
		}
		if ( 'the_tags' === $a['tag'] ) {
			$values = array( '', ', ', '' );
		}
		if ( 'the_category' === $a['tag'] ) {
			$values = array( ', ' );
		}

		// phpcs:disable
		ob_start();
		if ( 'get_post_custom_values' === $a['tag'] ) {
			$output = @call_user_func( $a['tag'], $values, $values2 ); 
			if ( ! empty( $output[0] ) ) {
				$output = $output[0];
			}
			echo $output; 
		} elseif ( $a['tag'] == 'get_author_posts_url' ) {
			$output = @call_user_func( $a['tag'], $values ); 
			echo $output; 
		} elseif ( $a['tag'] == 'get_previous_post_link' || $a['tag'] == 'get_next_post_link' ) {
			$output = @call_user_func_array( $a['tag'], $values ); 
			echo $output; 
		} elseif ( $a['tag'] == 'get_day_link' || $a['tag'] == 'the_tags' || $a['tag'] == 'the_category' ) {
			$output = @call_user_func_array( $a['tag'], $values ); 
			echo $output; 
		} elseif ( $a['tag'] == 'comments_template' ) {
			global $withcomments;
			$withcomments = 1; 
			$output       = comments_template();
			echo $output;
		} elseif ( $a['tag'] == 'the_content' && $a['loop'] == 'true' ) {
			if ( have_posts() ) {
				while ( have_posts() ) {
					the_post(); 
					@call_user_func( $a['tag'] );
				} // end while
			} 
			//wp_reset_postdata();
		} elseif ( ! empty( $a['echo'] ) ) {
			$output = @call_user_func( $a['tag'], $values, $values2 ); 
			echo $output;
		} else {
			if ( $values == 'none' ) {
				$output = @call_user_func( $a['tag'] );
			} else {
				@call_user_func( $a['tag'], $values, $values2 );
			}
		}
		$code = ob_get_clean();
		
		return $code;
		// phpcs:enable
	}

	// render template_part
	if ( ! empty( $a['id'] ) ) {
		$code    = '';
		$post_id = absint( $a['id'] );

		// Get post and check permission, sanitize and filter
		$post = get_post( $post_id );
		if ( 'publish' === $post->post_status && 'seedprod' === $post->post_type ) {
			// Do NOT use get_the_content, breaks system
			$code = do_shortcode( $post->post_content );
		}
		return $code;
	}

	return $code;
}

add_shortcode( 'seedprod', 'seedprod_pro_render_template_tags_shortcode' );
/**
 * Render Template Part
 */
function seedprod_pro_render_template_part( $atts ) {
	$a = shortcode_atts(
		array(
			'id' => 0,
		),
		$atts
	);

	$code    = '';
	$post_id = absint( $a['id'] );

	// Get post and check permission, sanitize and filter
	$post = get_post( $post_id );
	if ( 'publish' === $post->post_status && 'seedprod' === $post->post_type ) {
		// Do NOT use get_the_content, breaks system
		$code = do_shortcode( $post->post_content );

	}
	return $code;

}
add_shortcode( 'sp_template_part', 'seedprod_pro_render_template_part' );

/**
 * Render Custom Field
 */
function seedprod_pro_render_custom_field( $atts ) {
	$a = shortcode_atts(
		array(
			'post_id' => get_the_ID(),
			'field'   => '',
		),
		$atts
	);

	// TODO AFC Support
	// if ( ! class_exists( 'ACF' ) ) {
	// }

	return get_post_meta( absint( $a['post_id'] ), $a['field'], true );

}
add_shortcode( 'sp_custom_field', 'seedprod_pro_render_custom_field' );

/**
 * Check if page is a child of another page.
 */
function seedprod_pro_is_child_of( $pid ) {
	// $pid = The ID of the page we're looking for pages underneath
	if ( ! empty( $pid[0] ) ) {
		$pid = $pid[0];
	}

	global $post;         // load details about this page
	$anc = get_post_ancestors( $post->ID );
	foreach ( $anc as $ancestor ) {
		if ( is_page() && $ancestor == $pid ) {
			return true;
		}
	}
	if ( is_page() && ( is_page( $pid ) ) ) {
		return true; // we're at the page or at a sub page
	} else {
		return false;
	} // we're elsewhere
};


/**
 * get block settings at current page
 */
function seedprod_pro_get_theme_template_js_css_block_files() {
	global $post, $wpdb;
	$post_id          = get_the_ID();
	$all_settings     = '';
	$tablename        = $wpdb->prefix . 'posts';
	$sql              = "SELECT post_content_filtered,post_modified FROM $tablename WHERE ID = %d";
	$safe_sql         = $wpdb->prepare( $sql, absint( $post_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$all_settings_row = $wpdb->get_row( $safe_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	if ( ! empty( $all_settings_row->post_content_filtered ) ) {
		$all_settings = json_decode( $all_settings_row->post_content_filtered );
		if ( ! empty( $all_settings ) ) {
			$all_settings->post_id            = $post_id;
			$all_settings->modified_timestamp = strtotime( $all_settings_row->post_modified );
		}
	}
	return $all_settings;
}

/**
 * Get Requirements for a Theme.
 */
function get_the_theme_parts_requirements() {
	$header       = seedprod_pro_get_theme_template_by_type_condition( 'header', false, true );
	$page         = seedprod_pro_get_theme_template_by_type_condition( 'page', false, true );
	$footer       = seedprod_pro_get_theme_template_by_type_condition( 'footer', false, true );
	$main_content = seedprod_pro_get_theme_template_js_css_block_files();

	$all_settings                 = array();
	$all_settings['header']       = wp_json_encode( $header );
	$all_settings['page']         = wp_json_encode( $page );
	$all_settings['main_content'] = wp_json_encode( $main_content );
	$all_settings['footer']       = wp_json_encode( $footer );
	$all_settings['all']          = $all_settings['header'] . $all_settings['page'] . $all_settings['main_content'] . $all_settings['footer'];

	if ( ! empty( $header->template_parts_id ) ) {
		$header_template_parts_id                 = seedprod_pro_get_theme_template_by_page_id( $header->template_parts_id );
		$all_settings['header_template_parts_id'] = wp_json_encode( $header_template_parts_id );
		$all_settings['all']                      = $all_settings['all'] . $all_settings['header_template_parts_id'];
	}

	if ( ! empty( $footer->template_parts_id ) ) {
		$footer_template_parts_id                 = seedprod_pro_get_theme_template_by_page_id( $footer->template_parts_id );
		$all_settings['footer_template_parts_id'] = wp_json_encode( $footer_template_parts_id );
		$all_settings['all']                      = $all_settings['all'] . $all_settings['footer_template_parts_id'];
	}

	if ( ! empty( $page->template_parts_id ) ) {
		$page_template_parts_id                 = seedprod_pro_get_theme_template_by_page_id( $page->template_parts_id );
		$all_settings['page_template_parts_id'] = wp_json_encode( $page_template_parts_id );
		$all_settings['all']                    = $all_settings['all'] . $all_settings['page_template_parts_id'];
	}

	// look for theme requirements
	global $seedprod_theme_requirements;
	$settings_str = $all_settings['all'];

	// css requirements
	$header_post_id = '';
	if ( ! empty( $header->post_id ) ) {
		$header_post_id = $header->post_id;
	}
	$header_modified_timestamp = '';
	if ( ! empty( $header->modified_timestamp ) ) {
		$header_modified_timestamp = $header->modified_timestamp;
	}
	$page_post_id = '';
	if ( ! empty( $page->post_id ) ) {
		$page_post_id = $page->post_id;
	}
	$page_modified_timestamp = '';
	if ( ! empty( $page->modified_timestamp ) ) {
		$page_modified_timestamp = $page->modified_timestamp;
	}
	$footer_post_id = '';
	if ( ! empty( $footer->post_id ) ) {
		$footer_post_id = $footer->post_id;
	}
	$footer_modified_timestamp = '';
	if ( ! empty( $footer->modified_timestamp ) ) {
		$footer_modified_timestamp = $footer->modified_timestamp;
	}

	$seedprod_theme_requirements[] = 'css:' . $header_post_id . '|' . $header_modified_timestamp . ',' . $page_post_id . '|' . $page_modified_timestamp . ',' . $footer_post_id . '|' . $footer_modified_timestamp;

	// facebook blocks
	if ( strpos( $settings_str, 'facebooklike' ) !== false || strpos( $settings_str, 'facebookpage' ) !== false ||
	strpos( $settings_str, 'facebookcomments' ) !== false || strpos( $settings_str, 'facebookembed' ) !== false ) {
		$seedprod_theme_requirements[] = 'facebook_sdk';
	}

	if ( strpos( $settings_str, 'twitterembedtimeline' ) !== false || strpos( $settings_str, 'twittertweetbutton' ) !== false ) {
		$seedprod_theme_requirements[] = 'twitter_sdk';
	}

	if ( strpos( $settings_str, '"linktype":"lightboxmedia"' ) !== false ) {
		$seedprod_theme_requirements[] = 'lightboxmedia';
	}

	if ( strpos( $settings_str, '"showLightboxGallery":true' ) !== false ) {
		$seedprod_theme_requirements[] = 'lightboxmedia';
	}

	// animated headline blocks
	if ( strpos( $settings_str, 'animatedheadline' ) !== false ) {
		$seedprod_theme_requirements[] = 'animatedheadline';
	}

	// animated blocks
	if ( strpos( $settings_str, 'ani_' ) !== false ) {
		$seedprod_theme_requirements[] = 'animatedblocks';
	}

	// seedprod gallery blocks
	if ( strpos( $settings_str, 'seedprodgallery' ) !== false ) {
		$seedprod_theme_requirements[] = 'seedprodgallery';
	}

	// Counter block
	if ( strpos( $settings_str, 'counter' ) !== false ) {
		$seedprod_theme_requirements[] = 'counter';
	}

	// Hotspot block
	if ( strpos( $settings_str, 'hotspot' ) !== false ) {
		$seedprod_theme_requirements[] = 'hotspot';
	}

	// seedprod gallery blocks
	if ( strpos( $settings_str, 'seedprodbasicgallery' ) !== false ) {
		$seedprod_theme_requirements[] = 'seedprodbasicgallery';
	}

	// seedprod beforeaftertoggle blocks
	if ( strpos( $settings_str, 'beforeaftertoggle' ) !== false ) {
		$seedprod_theme_requirements[] = 'beforeaftertoggle';
	}

	// optin blocks
	if ( strpos( $settings_str, 'optin-form' ) !== false ) {
		$seedprod_theme_requirements[] = 'optinform';
	}

	// optin blocks
	if ( strpos( $settings_str, '"enable_recaptcha":true' ) !== false ) {

		$seedprod_theme_requirements[] = 'recaptcha';
	}

	// dynamic text
	if ( strpos( $settings_str, '[#' ) !== false || strpos( $settings_str, '[q' ) !== false ) {
		$seedprod_theme_requirements[] = 'dynamictext';
	}

	// gallery block
	if ( strpos( $settings_str, '"lightboxEffect":"yes"' ) !== false ) {
		$seedprod_theme_requirements[] = 'seedprodgallery';
	}
	if ( strpos( $settings_str, '"galleryLink":"media"' ) !== false ) {
		$seedprod_theme_requirements[] = 'seedprodbasicgallery';
	}

	if ( strpos( $settings_str, 'particleBg' ) !== false ) {
		$seedprod_theme_requirements[] = 'particlesbackground';
	}

	return $all_settings;
}

/**
 * Remove data attributes from code.
 */
function seedprod_pro_clean_data_attributes( $code ) {
	// Get mobile css & Remove inline data attributes.
	preg_match_all( '/data-mobile-css="([^"]*)"/', $code, $matches );
	if ( ! empty( $matches ) ) {
		// remove inline data attributes
		foreach ( $matches[0] as $v ) {
			$code = str_replace( $v, '', $code );
		}
	}

	preg_match_all( '/data-mobile-visibility="([^"]*)"/', $code, $matches );
	if ( ! empty( $matches ) ) {
		// remove inline data attributes
		foreach ( $matches[0] as $v ) {
			$code = str_replace( $v, '', $code );
		}
	}

	preg_match_all( '/data-desktop-visibility="([^"]*)"/', $code, $matches );
	if ( ! empty( $matches ) ) {
		// remove inline data attributes
		foreach ( $matches[0] as $v ) {
			$code = str_replace( $v, '', $code );
		}
	}

	// remove vue comment bug
	$code = str_replace( 'function(e,n,r,i){return fn(t,e,n,r,i,!0)}', '', $code );

	return $code;
}
add_filter( 'seedprod_the_code', 'seedprod_pro_clean_data_attributes', 10, 2 );
