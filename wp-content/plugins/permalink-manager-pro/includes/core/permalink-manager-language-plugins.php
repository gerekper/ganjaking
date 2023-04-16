<?php

/**
 * WPML & Polylang integration functions
 */
class Permalink_Manager_Language_Plugins {

	public function __construct() {
		add_action( 'init', array( $this, 'init_hooks' ), 99 );
	}

	/**
	 * Register hooks adding support for WPML and Polylang
	 */
	function init_hooks() {
		global $sitepress_settings, $polylang, $translate_press_settings, $permalink_manager_options;

		// 1. WPML, Polylang & TranslatePress
		if ( $sitepress_settings || ! empty( $polylang->links_model->options ) || class_exists( 'TRP_Translate_Press' ) ) {
			// Detect Post/Term function
			add_filter( 'permalink_manager_detected_post_id', array( $this, 'fix_language_mismatch' ), 9, 3 );
			add_filter( 'permalink_manager_detected_term_id', array( $this, 'fix_language_mismatch' ), 9, 3 );

			// Fix posts page
			add_filter( 'permalink_manager_filter_query', array( $this, 'fix_posts_page' ), 5, 5 );

			// URI Editor
			add_filter( 'permalink_manager_uri_editor_extra_info', array( $this, 'uri_editor_get_lang_col' ), 9, 3 );

			// Adjust front page ID
			add_filter( 'permalink_manager_is_front_page', array( $this, 'wpml_is_front_page' ), 9, 3 );

			// Provide the language code for specific post/term
			add_filter( 'permalink_manager_get_language_code', array( $this, 'filter_get_language_code' ), 9, 2 );

			// Get translation mode
			$mode = 0;

			// A. WPML
			if ( isset( $sitepress_settings['language_negotiation_type'] ) ) {
				$url_settings = $sitepress_settings['language_negotiation_type'];

				if ( in_array( $url_settings, array( 1, 2 ) ) ) {
					$mode = 'prepend';
				} else if ( $url_settings == 3 ) {
					$mode = 'append';
				}
			} // B. Polylang
			else if ( isset( $polylang->links_model->options['force_lang'] ) ) {
				$url_settings = $polylang->links_model->options['force_lang'];

				if ( in_array( $url_settings, array( 1, 2, 3 ) ) ) {
					$mode = 'prepend';
				}
			} // C. TranslatePress
			else if ( class_exists( 'TRP_Translate_Press' ) ) {
				$translate_press_settings = get_option( 'trp_settings' );

				$mode = 'prepend';
			}

			if ( $mode === 'prepend' ) {
				add_filter( 'permalink_manager_detect_uri', array( $this, 'detect_uri_language' ), 9, 3 );
				add_filter( 'permalink_manager_filter_permalink_base', array( $this, 'prepend_lang_prefix' ), 9, 2 );
			} else if ( $mode === 'append' ) {
				add_filter( 'permalink_manager_filter_final_post_permalink', array( $this, 'append_lang_prefix' ), 5, 2 );
				add_filter( 'permalink_manager_filter_final_term_permalink', array( $this, 'append_lang_prefix' ), 5, 2 );
				add_filter( 'permalink_manager_detect_uri', array( $this, 'wpml_ignore_lang_query_parameter' ), 9 );
			}

			// Translate permastructures
			add_filter( 'permalink_manager_filter_permastructure', array( $this, 'translate_permastructure' ), 9, 2 );

			// Translate custom permalinks
			if ( $this->is_wpml_compatible() ) {
				add_filter( 'permalink_manager_filter_final_post_permalink', array( $this, 'translate_permalinks' ), 9, 2 );
			}

			// Translate post type slug
			if ( class_exists( 'WPML_Slug_Translation' ) ) {
				add_filter( 'permalink_manager_filter_post_type_slug', array( $this, 'wpml_translate_post_type_slug' ), 9, 3 );
			}

			// Translate WooCommerce endpoints
			if ( class_exists( 'WCML_Endpoints' ) ) {
				add_filter( 'request', array( $this, 'wpml_translate_wc_endpoints' ), 99999 );
			}

			// Edit custom URI using WPML Classic Translation Editor
			if ( class_exists( 'WPML_Translation_Editor_UI' ) ) {
				add_filter( 'wpml_tm_adjust_translation_fields', array( $this, 'wpml_cte_edit_uri_field' ), 999, 2 );
				add_action( 'icl_pro_translation_saved', array( $this, 'wpml_translation_save_uri' ), 999, 3 );
				add_filter( 'wpml_translation_editor_save_job_data', array( $this, 'wpml_translation_save_uri' ), 999, 2 );
			}

			// Generate custom permalink after WPML's Advanced Translation editor is used
			if ( ! empty( $sitepress_settings['translation-management'] ) && ! empty( $sitepress_settings['translation-management']['doc_translation_method'] ) && $sitepress_settings['translation-management']['doc_translation_method'] == 'ATE' ) {
				if ( apply_filters( 'permalink_manager_ate_uri_editor', false ) === true ) {
					add_filter( 'icl_job_elements', array( $this, 'wpml_ate_edit_uri_field' ), 99, 3 );
				}
				add_filter( 'wpml_pre_save_pro_translation', array( $this, 'wpml_prevent_uri_save_before_translation_completed' ), 99, 2 );
				add_action( 'wpml_pro_translation_completed', array( $this, 'wpml_save_uri_after_wpml_translation_completed' ), 99, 3 );
			}

			add_action( 'icl_make_duplicate', array( $this, 'wpml_duplicate_uri' ), 999, 4 );

			// Allow canonical redirect for default language if "Hide URL language information for default language" is turned on in Polylang settings
			if ( ! empty( $polylang ) && ! empty( $polylang->links_model ) && ! empty( $polylang->links_model->options['hide_default'] ) ) {
				add_filter( 'permalink_manager_filter_query', array( $this, 'pl_allow_canonical_redirect' ), 3, 5 );
			}
		}
	}

	/**
	 * Let users decide if they want Permalink Manager to force language code in the custom permalinks
	 */
	public static function is_wpml_compatible() {
		global $permalink_manager_options;

		// Use the current language if translation is not available but fallback mode is turned on
		return ( ! empty( $permalink_manager_options['general']['wpml_support'] ) ) ? $permalink_manager_options['general']['wpml_support'] : false;
	}

	/**
	 * Return the language code string for specific post or term
	 *
	 * @param string|int|WP_Post|WP_Term $element
	 *
	 * @return false|string
	 */
	public static function get_language_code( $element ) {
		global $TRP_LANGUAGE, $icl_adjust_id_url_filter_off, $sitepress, $polylang, $wpml_post_translations, $wpml_term_translations;

		// Disable WPML adjust ID filter
		$icl_adjust_id_url_filter_off = true;

		// Fallback
		if ( is_string( $element ) && strpos( $element, 'tax-' ) !== false ) {
			$element_id = intval( preg_replace( "/[^0-9]/", "", $element ) );
			$element    = get_term( $element_id );
		} else if ( is_numeric( $element ) ) {
			$element = get_post( $element );
		}

		// A. TranslatePress
		if ( ! empty( $TRP_LANGUAGE ) ) {
			$lang_code = self::get_translatepress_language_code( $TRP_LANGUAGE );
		} // B. Polylang
		else if ( ! empty( $polylang ) && function_exists( 'pll_get_post_language' ) ) {
			if ( isset( $element->post_type ) ) {
				$lang_code = pll_get_post_language( $element->ID, 'slug' );
			} else if ( isset( $element->taxonomy ) ) {
				$lang_code = pll_get_term_language( $element->term_id, 'slug' );
			}
		} // C. WPML
		else if ( ! empty( $sitepress ) ) {
			$is_wpml_compatible = ( method_exists( $sitepress, 'is_display_as_translated_post_type' ) ) ? self::is_wpml_compatible() : false;

			if ( isset( $element->post_type ) ) {
				$element_id   = $element->ID;
				$element_type = $element->post_type;

				$fallback_lang_on = ( $is_wpml_compatible ) ? $sitepress->is_display_as_translated_post_type( $element_type ) : false;
			} else if ( isset( $element->taxonomy ) ) {
				$element_id   = $element->term_id;
				$element_type = $element->taxonomy;

				$fallback_lang_on = ( $is_wpml_compatible ) ? $sitepress->is_display_as_translated_taxonomy( $element_type ) : false;
			} else {
				return false;
			}

			if ( ! empty( $fallback_lang_on ) && ! is_admin() && ! wp_doing_ajax() && ! defined( 'REST_REQUEST' ) ) {
				$current_language = $sitepress->get_current_language();

				if ( ! empty( $element->post_type ) ) {
					$force_current_lang = $wpml_post_translations->element_id_in( $element_id, $current_language ) ? false : $current_language;
				} else if ( ! empty( $element->taxonomy ) ) {
					$force_current_lang = $wpml_term_translations->element_id_in( $element_id, $current_language ) ? false : $current_language;
				}
			}

			$lang_code = ( ! empty( $force_current_lang ) ) ? $force_current_lang : apply_filters( 'wpml_element_language_code', null, array( 'element_id' => $element_id, 'element_type' => $element_type ) );
		}

		// Enable WPML adjust ID filter
		$icl_adjust_id_url_filter_off = false;

		// Use default language if nothing detected
		return ( ! empty( $lang_code ) ) ? $lang_code : self::get_default_language();
	}

	/**
	 * Filter the language code of a provided post or term
	 *
	 * @param string $lang_code
	 * @param string|integer $element
	 *
	 * @return false|string
	 */
	public function filter_get_language_code( $lang_code = '', $element = '' ) {
		return self::get_language_code( $element );
	}

	/**
	 * Return the language URL prefix code for TranslatePress
	 *
	 * @param string $lang
	 *
	 * @return false|string
	 */
	public static function get_translatepress_language_code( $lang ) {
		global $translate_press_settings;

		if ( ! empty( $translate_press_settings['url-slugs'] ) ) {
			$lang_code = ( ! empty( $translate_press_settings['url-slugs'][ $lang ] ) ) ? $translate_press_settings['url-slugs'][ $lang ] : '';
		}

		return ( ! empty( $lang_code ) ) ? $lang_code : false;
	}

	/**
	 * Return the language code for the default language
	 *
	 * @return false|string
	 */
	public static function get_default_language() {
		global $sitepress, $translate_press_settings;

		if ( function_exists( 'pll_default_language' ) ) {
			$def_lang = pll_default_language( 'slug' );
		} else if ( is_object( $sitepress ) ) {
			$def_lang = $sitepress->get_default_language();
		} else if ( ! empty( $translate_press_settings['default-language'] ) ) {
			$def_lang = self::get_translatepress_language_code( $translate_press_settings['default-language'] );
		} else {
			$def_lang = '';
		}

		return $def_lang;
	}

	/**
	 * Return the array with all defined languages
	 *
	 * @param bool $exclude_default_language
	 *
	 * @return array
	 */
	public static function get_all_languages( $exclude_default_language = false ) {
		global $sitepress, $sitepress_settings;

		$languages_array  = $active_languages = array();
		$default_language = self::get_default_language();

		if ( ! empty( $sitepress_settings['active_languages'] ) ) {
			$languages_array = $sitepress_settings['active_languages'];
		} elseif ( function_exists( 'pll_languages_list' ) ) {
			$languages_array = pll_languages_list( array( 'fields' => null ) );
		}

		/*if ( ! empty( $translate_press_settings['url-slugs'] ) ) {
			$languages_array = $translate_press_settings['url-slugs'];
		}*/

		// Get native language names as value
		if ( $languages_array ) {
			foreach ( $languages_array as $val ) {
				if ( ! empty( $sitepress ) ) {
					$lang          = $val;
					$lang_details  = $sitepress->get_language_details( $lang );
					$language_name = $lang_details['native_name'];
				} else if ( ! empty( $val->name ) ) {
					$lang          = $val->slug;
					$language_name = $val->name;
				}

				if ( ! empty( $lang ) ) {
					$active_languages[ $lang ] = ( ! empty( $language_name ) ) ? sprintf( '%s <span>(%s)</span>', $language_name, $lang ) : '-';
				}
			}

			// Exclude default language if needed
			if ( $exclude_default_language && $default_language && ! empty( $active_languages[ $default_language ] ) ) {
				unset( $active_languages[ $default_language ] );
			}
		}

		return $active_languages;
	}

	/**
	 * If the requested language code does not match the language of the requested content, modify which post/term is to be loaded
	 *
	 * @param string|int $item_id
	 * @param array $uri_parts
	 * @param bool $is_term
	 *
	 * @return false|int
	 */
	function fix_language_mismatch( $item_id, $uri_parts, $is_term = false ) {
		global $permalink_manager_options, $permalink_manager_uris, $pm_query, $polylang, $icl_adjust_id_url_filter_off;

		$mode = ( ! empty( $permalink_manager_options['general']['fix_language_mismatch'] ) ) ? $permalink_manager_options['general']['fix_language_mismatch'] : 0;

		// Stop WPML from changing the output of the get_term() and get_post() functions
		$icl_adjust_id_url_filter_off_prior = $icl_adjust_id_url_filter_off;
		$icl_adjust_id_url_filter_off       = true;

		if ( $is_term ) {
			$element = get_term( $item_id );
			if ( ! empty( $element ) && ! is_wp_error( $element ) ) {
				$element_id   = $element->term_id;
				$element_type = $element->taxonomy;
			} else {
				return false;
			}
		} else {
			$element = get_post( $item_id );

			if ( ! empty( $element->post_type ) ) {
				$element_id   = $item_id;
				$element_type = $element->post_type;
			}
		}

		// Stop if no term or post is detected
		if ( empty( $element_id ) || empty( $element_type ) ) {
			return false;
		}

		// Get the language code of the found post/term
		$element_language_code = self::get_language_code( $element );

		// Get the detected language code
		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			$detected_language_code = ICL_LANGUAGE_CODE;
		} else if ( ! empty( $uri_parts['lang'] ) ) {
			$detected_language_code = $uri_parts['lang'];
		} else {
			return $item_id;
		}

		if ( $detected_language_code !== $element_language_code ) {
			// A. Display the content in requested language
			// B. Allow the canonical redirect
			if ( $mode == 1 || $mode == 2 ) {
				if ( ! empty( $polylang ) ) {
					if ( function_exists( 'pll_get_post' ) && ! $is_term ) {
						$translated_item_id = pll_get_post( $element_id, $detected_language_code );
					} else if ( function_exists( 'pll_get_term' ) && $is_term ) {
						$translated_item_id = pll_get_term( $element_id, $detected_language_code );
					}

					$item_id = ( isset( $translated_item_id ) ) ? $translated_item_id : $item_id;
				} else {
					$item_id = apply_filters( 'wpml_object_id', $element_id, $element_type );
				}

				// Compare the URIs to prevent the redirect loop
				if ( $mode == 2 && $item_id !== $element_id ) {
					$detected_element_uri   = Permalink_Manager_URI_Functions::get_single_uri( $element_id, false, false, $is_term );
					$translated_element_uri = Permalink_Manager_URI_Functions::get_single_uri( $item_id, false, false, $is_term );

					if ( ! empty( $detected_element_uri ) && ! empty( $translated_element_uri ) && $detected_element_uri !== $translated_element_uri ) {
						$pm_query['flag'] = 'language_mismatch';
					}
				}
			}
			 // C. Display "404 error"
			else {
				$item_id = 0;
			}
		}

		$icl_adjust_id_url_filter_off = $icl_adjust_id_url_filter_off_prior;

		return $item_id;
	}

	/**
	 * Fix the language switcher on blog page (WPML bug)
	 *
	 * @param array $query
	 * @param array $old_query
	 * @param array $uri_parts
	 * @param array $pm_query
	 * @param string $content_type
	 *
	 * @return array
	 */
	function fix_posts_page( $query, $old_query, $uri_parts, $pm_query, $content_type ) {
		if ( empty( $pm_query['id'] ) || ! is_numeric( $pm_query['id'] ) ) {
			return $query;
		}

		$blog_page_id = apply_filters( 'wpml_object_id', get_option( 'page_for_posts' ), 'page' );
		$element_id   = apply_filters( 'wpml_object_id', $pm_query['id'], 'page' );

		if ( ! empty( $blog_page_id ) && ( $blog_page_id == $element_id ) && ! isset( $query['page'] ) ) {
			$query['page'] = '';
		}

		return $query;
	}

	/**
	 * Detect the language of requested content and add it to $uri_parts array
	 *
	 * @param array $uri_parts
	 * @param string $request_url
	 * @param string $endpoints
	 *
	 * @return array
	 */
	function detect_uri_language( $uri_parts, $request_url, $endpoints ) {
		global $sitepress_settings, $polylang, $translate_press_settings;

		if ( ! empty( $sitepress_settings['active_languages'] ) ) {
			$languages_list = (array) $sitepress_settings['active_languages'];
		} elseif ( function_exists( 'pll_languages_list' ) ) {
			$languages_array = pll_languages_list();
			$languages_list  = ( is_array( $languages_array ) ) ? $languages_array : "";
		} elseif ( $translate_press_settings['url-slugs'] ) {
			$languages_list = $translate_press_settings['url-slugs'];
		}

		if ( ! empty( $languages_list ) && is_array( $languages_list ) ) {
			$languages_list = implode( "|", $languages_list );
		} else {
			return $uri_parts;
		}

		$default_language = self::get_default_language();

		// Fix for multidomain language configuration
		if ( ( isset( $sitepress_settings['language_negotiation_type'] ) && $sitepress_settings['language_negotiation_type'] == 2 ) || ( ! empty( $polylang->options['force_lang'] ) && $polylang->options['force_lang'] == 3 ) ) {
			if ( ! empty( $polylang->options['domains'] ) ) {
				$domains = (array) $polylang->options['domains'];
			} else if ( ! empty( $sitepress_settings['language_domains'] ) ) {
				$domains = (array) $sitepress_settings['language_domains'];
			}

			if ( ! empty( $domains ) ) {
				foreach ( $domains as &$domain ) {
					$domain = preg_replace( '/((http(s)?:\/\/(www\.)?)|(www\.))?(.+?)\/?$/', 'http://$6', $domain );
				}

				$request_url = trim( str_replace( $domains, "", $request_url ), "/" );
			}
		}

		if ( ! empty( $languages_list ) ) {
			preg_match( "/^(?:({$languages_list})\/)?(.+?)(?|\/({$endpoints})(?|\/(.*)|$)|\/()([\d]+)\/?)?$/i", $request_url, $regex_parts );

			$uri_parts['lang']           = ( ! empty( $regex_parts[1] ) ) ? $regex_parts[1] : $default_language;
			$uri_parts['uri']            = ( ! empty( $regex_parts[2] ) ) ? $regex_parts[2] : "";
			$uri_parts['endpoint']       = ( ! empty( $regex_parts[3] ) ) ? $regex_parts[3] : "";
			$uri_parts['endpoint_value'] = ( ! empty( $regex_parts[4] ) ) ? $regex_parts[4] : "";
		}

		return $uri_parts;
	}

	/**
	 * Append the language code to the URL directly after the domain name
	 *
	 * @param string $base
	 * @param string|int|WP_Post|WP_Term $element
	 * @param string $language_code
	 *
	 * @return string
	 */
	static function prepend_lang_prefix( $base, $element, $language_code = '' ) {
		global $sitepress_settings, $polylang, $translate_press_settings;

		if ( ! empty( $element ) && empty( $language_code ) ) {
			$language_code = self::get_language_code( $element );

			// Last instance - use language parameter from &_GET array
			$language_code = ( is_admin() && empty( $language_code ) && ! empty( $_GET['lang'] ) ) ? sanitize_key( $_GET['lang'] ) : $language_code;
		}

		// Adjust URL base
		if ( ! empty( $language_code ) ) {
			$default_language_code = self::get_default_language();
			$home_url              = get_home_url();

			// Hide language code if "Use directory for default language" option is enabled
			$hide_prefix_for_default_lang = ( ( isset( $sitepress_settings['urls']['directory_for_default_language'] ) && $sitepress_settings['urls']['directory_for_default_language'] != 1 ) || ! empty( $polylang->links_model->options['hide_default'] ) || ( ! empty( $translate_press_settings ) && $translate_press_settings['add-subdirectory-to-default-language'] !== 'yes' ) ) ? true : false;

			// A. Different domain per language
			if ( ( isset( $sitepress_settings['language_negotiation_type'] ) && $sitepress_settings['language_negotiation_type'] == 2 ) || ( ! empty( $polylang->options['force_lang'] ) && $polylang->options['force_lang'] == 3 ) ) {
				if ( ! empty( $polylang->options['domains'] ) ) {
					$domains = $polylang->options['domains'];
				} else if ( ! empty( $sitepress_settings['language_domains'] ) ) {
					$domains = $sitepress_settings['language_domains'];
				}

				// Replace the domain name
				if ( ! empty( $domains ) && ! empty( $domains[ $language_code ] ) ) {
					$base = trim( $domains[ $language_code ], "/" );

					// Append URL scheme
					if ( ! preg_match( "~^(?:f|ht)tps?://~i", $base ) ) {
						$scheme = parse_url( $home_url, PHP_URL_SCHEME );
						$base   = "{$scheme}://{$base}";
					}
				}
			} // B. Prepend language code
			else if ( ! empty( $polylang->options['force_lang'] ) && $polylang->options['force_lang'] == 2 ) {
				if ( $hide_prefix_for_default_lang && ( $default_language_code == $language_code ) ) {
					return $base;
				} else {
					$base = preg_replace( '/(https?:\/\/)/', "$1{$language_code}.", $home_url );
				}
			} // C. Append prefix
			else {
				if ( $hide_prefix_for_default_lang && ( $default_language_code == $language_code ) ) {
					return $base;
				} else {
					$base .= "/{$language_code}";
				}
			}
		}

		return $base;
	}

	/**
	 * Append language code as a $_GET parameter to the end of URL
	 *
	 * @param string $permalink
	 * @param string|int|WP_Post|WP_Term $element
	 *
	 * @return string
	 */
	function append_lang_prefix( $permalink, $element ) {
		global $sitepress_settings;

		$language_code         = self::get_language_code( $element );
		$default_language_code = self::get_default_language();

		// Last instance - use language parameter from &_GET array
		if ( is_admin() ) {
			$language_code = ( empty( $language_code ) && ! empty( $_GET['lang'] ) ) ? $_GET['lang'] : $language_code;
		}

		// Append ?lang query parameter
		if ( isset( $sitepress_settings['language_negotiation_type'] ) && $sitepress_settings['language_negotiation_type'] == 3 ) {
			if ( $default_language_code == $language_code ) {
				return $permalink;
			} else if ( strpos( $permalink, "lang=" ) === false ) {
				$permalink .= "?lang={$language_code}";
			}
		}

		return $permalink;
	}

	/**
	 * Display the language code in a table column in bulk URI Editor
	 *
	 * @param string $output
	 * @param string $column
	 * @param WP_Post|WP_Term $element
	 *
	 * @return string
	 */
	function uri_editor_get_lang_col( $output, $column, $element ) {
		$language_code = self::get_language_code( $element );
		$output        .= ( ! empty( $language_code ) ) ? sprintf( " | <span><strong>%s:</strong> %s</span>", __( "Language" ), $language_code ) : "";

		return $output;
	}

	/**
	 * Check if requested URL is front page for any language
	 *
	 * @param bool $bool
	 * @param int $page_id
	 * @param int $front_page_id
	 *
	 * @return bool
	 */
	function wpml_is_front_page( $bool, $page_id, $front_page_id ) {
		$default_language_code = self::get_default_language();
		$page_id               = apply_filters( 'wpml_object_id', $page_id, 'page', true, $default_language_code );

		return ( ! empty( $page_id ) && $page_id == $front_page_id ) ? true : $bool;
	}

	/**
	 * Ignore ?lang query parameters added to the custom permalink's array
	 *
	 * @param array $uri_parts
	 *
	 * @return array
	 */
	function wpml_ignore_lang_query_parameter( $uri_parts ) {
		global $permalink_manager_uris;

		foreach ( $permalink_manager_uris as &$uri ) {
			$uri = trim( strtok( $uri, '?' ), "/" );
		}

		return $uri_parts;
	}

	/**
	 * Reapply WPML URL hooks and use them for custom permalinks filtered with Permalink Manager
	 *
	 * @param string $permalink
	 * @param int|WP_Post $post
	 *
	 * @return string
	 */
	function translate_permalinks( $permalink, $post ) {
		global $wpml_url_filters;

		if ( ! empty( $wpml_url_filters ) ) {
			$wpml_url_hook_name = _wp_filter_build_unique_id( 'post_link', array( $wpml_url_filters, 'permalink_filter' ), 1 );

			if ( has_filter( 'post_link', $wpml_url_hook_name ) ) {
				$permalink = $wpml_url_filters->permalink_filter( $permalink, $post );
			}
		}

		return $permalink;
	}

	/**
	 * Use the translated permastructure for the default custom permalinks
	 *
	 * @param string $permastructure
	 * @param WP_Post|WP_Term $element
	 *
	 * @return string
	 */
	function translate_permastructure( $permastructure, $element ) {
		global $permalink_manager_permastructs, $pagenow;

		// Get element language code
		if ( ! empty( $_REQUEST['data'] ) && is_string( $_REQUEST['data'] ) && strpos( $_REQUEST['data'], "target_lang" ) ) {
			$language_code = preg_replace( '/(.*target_lang=)([^=&]+)(.*)/', '$2', $_REQUEST['data'] );
		} else if ( in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) && ! empty( $_GET['lang'] ) ) {
			$language_code = $_GET['lang'];
		} else if ( ! empty( $_REQUEST['icl_post_language'] ) ) {
			$language_code = $_REQUEST['icl_post_language'];
		} else if ( ! empty( $_POST['action'] ) && $_POST['action'] == 'pm_save_permalink' && defined( 'ICL_LANGUAGE_CODE' ) ) {
			$language_code = ICL_LANGUAGE_CODE;
		} else {
			$language_code = self::get_language_code( $element );
		}

		if ( ! empty( $element->ID ) ) {
			$translated_permastructure = ( ! empty( $permalink_manager_permastructs["post_types"]["{$element->post_type}_{$language_code}"] ) ) ? $permalink_manager_permastructs["post_types"]["{$element->post_type}_{$language_code}"] : '';
		} else if ( ! empty( $element->term_id ) ) {
			$translated_permastructure = ( ! empty( $permalink_manager_permastructs["taxonomies"]["{$element->taxonomy}_{$language_code}"] ) ) ? $permalink_manager_permastructs["taxonomies"]["{$element->taxonomy}_{$language_code}"] : '';
		}

		return ( ! empty( $translated_permastructure ) ) ? $translated_permastructure : $permastructure;
	}

	/**
	 * Translate %post_type% tag in custom permastructures
	 *
	 * @param string $post_type_slug
	 * @param int|WP_Post $element
	 * @param string $post_type
	 *
	 * @return string
	 */
	function wpml_translate_post_type_slug( $post_type_slug, $element, $post_type ) {
		$post          = ( is_integer( $element ) ) ? get_post( $element ) : $element;
		$language_code = self::get_language_code( $post );

		return apply_filters( 'wpml_get_translated_slug', $post_type_slug, $post_type, $language_code );
	}

	/**
	 * Translate WooCommerce URL endpoints
	 *
	 * @param array $request
	 *
	 * @return array
	 */
	function wpml_translate_wc_endpoints( $request ) {
		global $woocommerce, $wpdb;

		if ( ! empty( $woocommerce->query->query_vars ) ) {
			// Get WooCommerce original endpoints
			// $endpoints = $woocommerce->query->query_vars;

			// Get all endpoint translations
			$endpoint_translations = $wpdb->get_results( "SELECT t.value AS translated_endpoint, t.language, s.value AS endpoint FROM {$wpdb->prefix}icl_string_translations AS t LEFT JOIN {$wpdb->prefix}icl_strings AS s ON t.string_id = s.id WHERE context = 'WP Endpoints'" );

			// Replace translate endpoint with its original name
			foreach ( $endpoint_translations as $endpoint ) {
				if ( isset( $request[ $endpoint->translated_endpoint ] ) && ( $endpoint->endpoint !== $endpoint->translated_endpoint ) ) {
					$request[ $endpoint->endpoint ] = $request[ $endpoint->translated_endpoint ];
					unset( $request[ $endpoint->translated_endpoint ] );
				}
			}
		}

		return $request;
	}

	/**
	 * Edit custom URI using WPML Advanced Translation Editor
	 *
	 * @param array $elements
	 * @param int $post_id
	 * @param int $job_id
	 *
	 * @return array
	 */
	function wpml_ate_edit_uri_field( $elements, $post_id, $job_id ) {
		global $wpdb;

		if ( is_array( $elements ) ) {
			// Get job element
			$job_query = $wpdb->prepare( "SELECT job_id, element_id, element_type, language_code, source_language_code FROM {$wpdb->prefix}icl_translate_job AS tj LEFT JOIN {$wpdb->prefix}icl_translation_status AS ts ON tj.rid = ts.rid LEFT JOIN {$wpdb->prefix}icl_translations AS t ON t.translation_id = ts.translation_id WHERE job_id = %d", $job_id );
			$job       = $wpdb->get_row( $job_query );

			// Check if the translated element is post or term
			if ( ! empty( $job->element_type ) ) {
				$translation_id      = $job->element_id;
				$original_custom_uri = Permalink_Manager_URI_Functions_Post::get_post_uri( $post_id, true );

				if ( ! empty( $translation_id ) ) {
					$translation_custom_uri   = Permalink_Manager_URI_Functions_Post::get_post_uri( $translation_id );
					$uri_translation_complete = ( ! empty( $translation_custom_uri ) ) ? 1 : 0;
				} else {
					$translation_custom_uri   = '';
					$uri_translation_complete = 0;
				}

				$custom_uri_element                        = new stdClass();
				$custom_uri_element->tid                   = 999999;
				$custom_uri_element->job_id                = $job_id;
				$custom_uri_element->content_id            = 0;
				$custom_uri_element->timestamp             = date( 'Y-m-d H:i:s' );
				$custom_uri_element->field_type            = 'Custom URI';
				$custom_uri_element->field_wrap_tag        = '';
				$custom_uri_element->field_format          = 'base64';
				$custom_uri_element->field_translate       = 1;
				$custom_uri_element->field_data            = base64_encode( $original_custom_uri );
				$custom_uri_element->field_data_translated = base64_encode( $translation_custom_uri );
				$custom_uri_element->field_finished        = $uri_translation_complete;

				$elements[] = $custom_uri_element;
			}
		}

		return $elements;
	}

	/**
	 * Prevents the custom permalink from being saved until the translation is completed and object terms are set
	 *
	 * @param array $postarr The post array that is being saved.
	 * @param stdClass $job The job object.
	 *
	 * @return array
	 */
	function wpml_prevent_uri_save_before_translation_completed( $postarr, $job ) {
		add_filter( 'permalink_manager_allow_new_post_uri', '__return_false' );

		return $postarr;
	}

	/**
	 * Generate custom permalink after WPML's Advanced Translation editor is used
	 *
	 * @param string|int $post_id
	 * @param array $postdata
	 * @param stdClass $job
	 */
	function wpml_save_uri_after_wpml_translation_completed( $post_id, $postdata, $job ) {
		global $permalink_manager_uris;

		$post_object = get_post( $post_id );

		// Check if post is allowed
		if ( empty( $post_object->post_type ) || Permalink_Manager_Helper_Functions::is_post_excluded( $post_object, true ) ) {
			return;
		}

		$default_uri = Permalink_Manager_URI_Functions_Post::get_default_post_uri( $post_id );

		// A. Use the translated custom permalink (if available)
		if ( ! empty( $postdata['Custom URI'] ) ) {
			$new_uri = ( ! empty( $postdata['Custom URI']['data'] ) && ! in_array( $postdata['Custom URI']['data'], array( '-', 'auto' ) ) ) ? Permalink_Manager_Helper_Functions::sanitize_title( $postdata['Custom URI']['data'] ) : $default_uri;
		} // B. Generate the new custom permalink
		else if ( empty( $permalink_manager_uris[ $post_id ] ) ) {
			$new_uri = $default_uri;
		}

		// Save the custom permalink
		if ( ! empty( $new_uri ) ) {
			Permalink_Manager_URI_Functions::save_single_uri( $post_id, $new_uri, false, true );
		}
	}

	/**
	 * Edit custom URI using WPML Classic Translation Editor
	 *
	 * @param array $fields
	 * @param stdClass $job
	 *
	 * @return array
	 */
	function wpml_cte_edit_uri_field( $fields, $job ) {
		global $permalink_manager_uris;

		$element_type = ( ! empty( $job->original_post_type ) && strpos( $job->original_post_type, 'post_' ) !== false ) ? preg_replace( '/^(post_)/', '', $job->original_post_type ) : '';

		if ( ! empty( $element_type ) ) {
			$original_id    = $job->original_doc_id;
			$translation_id = apply_filters( 'wpml_object_id', $original_id, $element_type, false, $job->language_code );

			$original_custom_uri = Permalink_Manager_URI_Functions_Post::get_post_uri( $original_id, true );

			if ( ! empty( $translation_id ) ) {
				$translation_custom_uri   = Permalink_Manager_URI_Functions_Post::get_post_uri( $translation_id, true );
				$uri_translation_complete = ( ! empty( $permalink_manager_uris[ $translation_id ] ) ) ? '1' : '0';
			} else {
				$translation_custom_uri   = $original_custom_uri;
				$uri_translation_complete = '0';
			}

			$fields[] = array(
				'field_type'            => 'pm-custom_uri',
				//'tid' => 9999,
				'field_data'            => $original_custom_uri,
				'field_data_translated' => $translation_custom_uri,
				'field_finished'        => $uri_translation_complete,
				'field_style'           => '0',
				'title'                 => 'Custom URI',
			);
		}

		return $fields;
	}

	/**
	 * Save the custom permalink in Classic WPML Translation Editor
	 *
	 * @param array|int $in
	 * @param array $data
	 * @param stdClass $job
	 *
	 * @return array|int
	 */
	function wpml_translation_save_uri( $in = '', $data = '', $job = '' ) {
		// A. Save the URI also when the translation is uncompleted
		if ( ! empty( $in['fields'] ) ) {
			$data = $in['fields'];

			$original_id  = $in['job_post_id'];
			$element_type = ( strpos( $in['job_post_type'], 'post_' ) !== false ) ? preg_replace( '/^(post_)/', '', $in['job_post_type'] ) : '';

			$translation_id = apply_filters( 'wpml_object_id', $original_id, $element_type, false, $in['target_lang'] );
		} // B. Save the URI also when the translation is completed
		else if ( is_numeric( $in ) ) {
			$translation_id = $in;
		}

		if ( isset( $data['pm-custom_uri'] ) && isset( $data['pm-custom_uri']['data'] ) && ! empty( $translation_id ) ) {
			$custom_uri = ( ! empty( $data['pm-custom_uri']['data'] ) ) ? Permalink_Manager_Helper_Functions::sanitize_title( $data['pm-custom_uri']['data'], true ) : Permalink_Manager_URI_Functions_Post::get_default_post_uri( $translation_id );

			Permalink_Manager_URI_Functions::save_single_uri( $translation_id, $custom_uri, false, true );
		}

		return $in;
	}

	/**
	 * Clone the custom permalink if post is duplicated with WPML
	 *
	 * @param int $master_post_id
	 * @param string $lang
	 * @param array $post_array
	 * @param int $id
	 */
	function wpml_duplicate_uri( $master_post_id, $lang, $post_array, $id ) {
		global $permalink_manager_uris;

		// Trigger the function only if duplicate is created in the metabox
		if ( empty( $_POST['action'] ) || $_POST['action'] !== 'make_duplicates' ) {
			return;
		}

		$permalink_manager_uris[ $id ] = Permalink_Manager_URI_Functions_Post::get_default_post_uri( $id );

		update_option( 'permalink-manager-uris', $permalink_manager_uris );
	}

	/**
	 * Allow the canonical redirect for default language if "Hide URL language information for default language" is turned on in Polylang settings
	 *
	 * @param array $query
	 * @param array $old_query
	 * @param array $uri_parts
	 * @param array $pm_query
	 * @param string $content_type
	 *
	 * @return array
	 */
	function pl_allow_canonical_redirect( $query, $old_query, $uri_parts, $pm_query, $content_type ) {
		global $polylang;

		// Run only if "Hide URL language information for default language" is turned on in Polylang settings
		if ( ! empty( $pm_query['id'] ) && ! empty( $pm_query['lang'] ) ) {
			$url_lang = $polylang->links_model->get_language_from_url();
			$def_lang = pll_default_language( 'slug' );

			// Check if the slug of default language is present in the requested URL
			if ( $url_lang == $def_lang ) {
				// Allow canonical redirect
				unset( $query['do_not_redirect'] );
			}
		}

		return $query;
	}

}
