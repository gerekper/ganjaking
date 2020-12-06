<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Work with google fonts
 *
 * @link       https://grooni.com
 * @since      1.2.18
 */
class GroovyMenuGFonts {

	/**
	 * Google Fonts API URL
	 *
	 * @since    1.2.18
	 * @access   protected
	 * @var      string $g_font_url
	 */
	protected $g_font_url = 'https://google-webfonts-helper.herokuapp.com/';

	/**
	 * Options name for cache Google Fonts
	 *
	 * @since    1.2.18
	 * @access   protected
	 * @var      string $g_font_opt_name
	 */
	protected $g_font_opt_name = 'Groovy_Menu_GFonts_cache';

	/**
	 * Var for cache downloaded font before
	 *
	 * @since    1.2.18
	 * @access   protected
	 * @var      array $downloaded
	 */
	protected $downloaded = array();

	/**
	 * All fonts list
	 *
	 * @since    1.2.18
	 * @access   protected
	 * @var      array $g_fonts
	 */
	protected $g_fonts = array();


	/**
	 * All current fonts list
	 *
	 * @since    1.2.18
	 * @access   protected
	 * @var      array $g_fonts_current
	 */
	protected $g_fonts_current = array();


	public function __construct() {

		global $gm_supported_module;

		if ( ! empty( $gm_supported_module['g_font_opt_name'] ) ) {
			$this->g_font_opt_name = esc_attr( $gm_supported_module['g_font_opt_name'] );
		}

	}


	/**
	 * @return array|mixed|object
	 */
	public function get_all_gfonts() {

		if ( ! empty( $this->g_fonts ) && is_array( $this->g_fonts ) ) {
			return $this->g_fonts;
		} elseif ( false !== get_transient( $this->g_font_opt_name ) ) {
			$this->g_fonts = get_transient( $this->g_font_opt_name );

			return $this->g_fonts;
		}


		// GET GFonts from API.
		$response = wp_remote_get( $this->g_font_url . 'api/fonts', array(
			'timeout'     => 30,
			'httpversion' => '1.1',
		) );


		// Check if correct response.
		if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
			$response_body = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( ! empty( $response_body ) && is_array( $response_body ) ) {
				$this->g_fonts = $response_body;
				set_transient( $this->g_font_opt_name, $response_body, 12 * DAY_IN_SECONDS );
			}
		}

		return $this->g_fonts;
	}


	/**
	 * @param        $font_search
	 * @param string $search_type
	 *
	 * @return array|mixed
	 */
	public function get_font_data( $font_search, $search_type = 'family' ) {

		if ( empty( $font_search ) ) {
			return array();
		}

		foreach ( $this->get_all_gfonts() as $font_data ) {
			if ( isset( $font_data[ $search_type ] ) && $font_data[ $search_type ] === $font_search ) {
				return $font_data;
			}
		}

		return array();

	}


	/**
	 * @return string|void
	 */
	public function get_opt_name() {
		return $this->g_font_opt_name;
	}


	/**
	 * @param        $font_search
	 * @param string $search_type
	 *
	 * @return array|mixed|object
	 */
	public function get_font_info( $font_search, $search_type = 'family' ) {

		if ( empty( $font_search ) ) {
			return array();
		}

		$font_data = $this->get_font_data( $font_search, $search_type );

		if ( empty( $font_data ) || ! isset( $font_data['id'] ) ) {
			return array();
		}

		$cache_data = get_transient( $this->g_font_opt_name . '__current' );
		if ( false !== $cache_data && is_array( $cache_data ) ) {
			$this->g_fonts_current = $cache_data;
		} else {
			$this->g_fonts_current = array();
		}

		if ( ! empty( $this->g_fonts_current[ $font_data['id'] ] ) && is_array( $this->g_fonts_current[ $font_data['id'] ] ) ) {
			return $this->g_fonts_current[ $font_data['id'] ];
		}

		// GET google font from API.
		$response = wp_remote_get( $this->g_font_url . 'api/fonts/' . $font_data['id'], array(
			'timeout'     => 30,
			'httpversion' => '1.1',
		) );

		// Check if correct response.
		if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
			$response_body = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( ! empty( $response_body ) && is_array( $response_body ) ) {
				$this->g_fonts_current[ $font_data['id'] ] = $response_body;
				set_transient( $this->g_font_opt_name . '__current', $this->g_fonts_current, 6 * DAY_IN_SECONDS );

				return $response_body;
			}
		}

		return array();

	}


	/**
	 * @param string $preset_id
	 * @param array $specific_font_data
	 *
	 * @return array
	 */
	public function get_specific_fonts( $preset_id = '', $specific_font_data = array() ) {

		$need_fonts = array();

		if ( empty( $specific_font_data ) ) {
			$source_fonts = $this->get_preset_gfonts( $preset_id );
		} else {
			$source_fonts = array(
				array(
					'font-family' => $specific_font_data['family'],
					'font_option' => 'google_font',
					'variants'    => $specific_font_data['variant'],
				),
			);
		}

		if ( ! empty( $source_fonts ) ) {
			foreach ( $source_fonts as $source_gfont_data ) {

				$font_name   = $source_gfont_data['font-family'];
				$font_option = $source_gfont_data['font_option'];

				$font_info = $this->get_font_info( $font_name );

				if ( empty( $font_info ) ) {
					continue;
				}

				$need_fonts[ $font_name ] = array(
					'id'           => $font_info['id'],
					'family'       => $font_info['family'],
					'version'      => $font_info['version'],
					'defSubset'    => isset( $font_info['defSubset'] ) ? $font_info['defSubset'] : 'latin',
					'defVariant'   => isset( $font_info['defVariant'] ) ? $font_info['defVariant'] : 'regular',
					'variants'     => null,
					'subsets'      => null,
					'zip_url'      => null,
					'variants_css' => null,
					'font_option'  => $font_option,
				);

				if ( ! empty( $source_gfont_data['variants'] ) ) {
					if ( is_array( $source_gfont_data['variants'] ) ) {
						foreach ( $source_gfont_data['variants'] as $variant ) {
							$need_fonts[ $font_name ]['variants'][] = $variant;
						}
					} elseif ( is_string( $source_gfont_data['variants'] ) ) {
						$need_fonts[ $font_name ]['variants'][] = $source_gfont_data['variants'];
					}
				}

				if ( ! empty( $source_gfont_data['subsets'] ) ) {
					foreach ( $source_gfont_data['subsets'] as $subset ) {
						$need_fonts[ $font_name ]['subsets'][] = $subset;
					}
				}

				if ( ! empty( $need_fonts[ $font_name ]['variants'] ) ) {
					foreach ( $need_fonts[ $font_name ]['variants'] as $variant ) {
						$need_fonts[ $font_name ]['variants_css'][ $variant ] = $this->prepare_variant_css( $variant, $font_info, $need_fonts[ $font_name ]['subsets'] );
					}
				} else {
					$need_fonts[ $font_name ]['variants_css'][ $font_info['defVariant'] ] = $this->prepare_variant_css( $font_info['defVariant'], $font_info, $need_fonts[ $font_name ]['subsets'] );
				}


				$font_zip_url = 'https://google-webfonts-helper.herokuapp.com/api/fonts/' . $need_fonts[ $font_name ]['id'];
				$font_zip_url = add_query_arg( 'download', 'zip', $font_zip_url );
				$font_zip_url = add_query_arg( 'formats', 'woff,woff2', $font_zip_url );

				if ( ! empty( $need_fonts[ $font_name ]['variants'] ) ) {
					$variants = implode( ',', $need_fonts[ $font_name ]['variants'] );
				} elseif ( ! empty( $font_info['defVariant'] ) ) {
					$variants = $font_info['defVariant'];
				}

				if ( ! empty( $need_fonts[ $font_name ]['subsets'] ) ) {
					$subsets = implode( ',', $need_fonts[ $font_name ]['subsets'] );
				} elseif ( ! empty( $font_info['defSubset'] ) ) {
					$subsets = $font_info['defSubset'];
				}

				if ( ! empty( $variants ) ) {
					$font_zip_url = add_query_arg( 'variants', $variants, $font_zip_url );
				}
				if ( ! empty( $subsets ) ) {
					$font_zip_url = add_query_arg( 'subsets', $subsets, $font_zip_url );
				}

				$need_fonts[ $font_name ]['zip_url'] = $font_zip_url;

			}
		}


		return $need_fonts;

	}


	/**
	 * @param string $preset_id
	 *
	 * @return array
	 */
	public function get_preset_gfonts( $preset_id = '' ) {

		$fonts = array();

		$fonts_options = array(
			array(
				'section'     => 'general',
				'font_option' => 'logo_txt_font',
				'weight'      => array(
					'general' => [ 'logo_txt_weight', 'sticky_logo_txt_weight' ]
				),
				'subset'      => array(
					'general' => [ 'logo_txt_subset', 'sticky_logo_txt_subset' ]
				),
			),
			array(
				'section'     => 'styles',
				'font_option' => 'google_font',
				'weight'      => array(
					'styles'  => [ 'item_text_weight' ],
					'mobile'  => [ 'mobile_item_text_weight', 'mobile_subitem_text_weight' ],
					'general' => [ 'sub_level_item_text_weight', 'megamenu_title_text_weight' ]
				),
				'subset'      => array(
					'styles'  => [ 'item_text_subset' ],
					'general' => [ 'sub_level_item_text_subset', 'megamenu_title_text_subset' ]
				),
			),
		);

		if ( ! empty( $preset_id ) ) {
			$presets = array( GroovyMenuPreset::getById( $preset_id ) );
		} else {
			$presets = GroovyMenuPreset::getAll();
		}

		if ( empty( $presets ) ) {
			return $fonts;
		}

		foreach ( $presets as $preset ) {

			$preset_styles = new GroovyMenuStyle( $preset->id );

			foreach ( $fonts_options as $opt_data ) {

				$google_font = $preset_styles->get( $opt_data['section'], $opt_data['font_option'] );

				if ( ! empty( $google_font ) && 'none' !== $google_font ) {
					$params = array(
						'font-family' => $google_font,
						'font_option' => $opt_data['font_option'],
					);

					$variants = array();
					foreach ( $opt_data['weight'] as $_weight_section => $_weight ) {
						foreach ( $_weight as $weight_opt ) {
							$weight = $preset_styles->get( $_weight_section, $weight_opt );
							if ( ! empty( $weight ) && 'none' !== $weight ) {
								$variants[] = $weight;
							}
						}
					}
					$params['variants'] = array_unique( $variants );

					$subsets = array();
					foreach ( $opt_data['subset'] as $_subset_section => $_subset ) {
						foreach ( $_subset as $subset_opt ) {
							$subset = $preset_styles->get( $_subset_section, $subset_opt );
							if ( ! empty( $subset ) && 'none' !== $subset ) {
								$subsets[] = $subset;
							}
						}
					}

					$params['subsets'] = array_unique( $subsets );

					$fonts[] = $params;

				}
			}
		}

		return $fonts;

	}


	/**
	 * @param        $preset_id
	 * @param        $font_option
	 * @param string $common_font_family
	 * @param bool   $add_inline
	 *
	 * @return string
	 */
	public function add_gfont_face( $preset_id, $font_option, $common_font_family = '', $add_inline = false ) {

		$output = '';

		$google_fonts_local = false;
		$styles_class       = new GroovyMenuStyle( null );

		if ( $styles_class->getGlobal( 'tools', 'google_fonts_local' ) ) {
			$google_fonts_local = true;
		}

		if ( $google_fonts_local ) {

			$need_fonts = $this->get_specific_fonts( $preset_id );

			foreach ( $need_fonts as $_font ) {

				if ( $_font['font_option'] !== $font_option ) {
					continue;
				}

				if ( ! empty( $_font['zip_url'] ) ) {
					$this->download_font( $_font['zip_url'] );
				}

				if ( ! empty( $_font['variants_css'] ) && is_array( $_font['variants_css'] ) ) {
					foreach ( $_font['variants_css'] as $variant => $css_data ) {
						$output .= $this->generate_font_face( $css_data );
					}
				}
			}

		} else {

			$output = '@import url(https://fonts.googleapis.com/css?family=' . $common_font_family . ');';

		}

		if ( $add_inline ) {

			if ( $google_fonts_local ) {
				wp_add_inline_style( 'groovy-menu-style', $output );
			} else {

				\GroovyMenu\PreStorage::get_instance()->set_preset_data( $preset_id, 'font_family', $common_font_family );
				\GroovyMenu\PreStorage::get_instance()->set_preset_data( $preset_id, 'font_option', $font_option );

			}

		} else {
			$tag_name = 'style';

			return '<' . $tag_name . '>' . $output . '</' . $tag_name . '>';

		}

		return '';

	}


	/**
	 * @param string $common_font_family
	 * @param bool   $add_inline
	 *
	 * @return string
	 */
	public function add_gfont_face_simple( $font_family = '', $font_variant = '', $add_inline = false ) {

		$output = '';

		$common_font_family  = rawurlencode( $font_family );
		$common_font_variant = intval( $font_variant );
		if ( empty( $common_font_variant ) || 'regular' === $font_variant || 'italic' === $font_variant ) {
			$common_font_variant = 400;
		}
		$common_font_family = $common_font_family . ':' . $common_font_variant;

		$google_fonts_local = false;
		$styles_class       = new GroovyMenuStyle( null );

		if ( $styles_class->getGlobal( 'tools', 'google_fonts_local' ) ) {
			$google_fonts_local = true;
		}

		if ( $google_fonts_local ) {

			$specific_font_data = array( 'family' => $font_family, 'variant' => $font_variant );

			$need_fonts = $this->get_specific_fonts( '', $specific_font_data );

			foreach ( $need_fonts as $_font ) {

				if ( ! empty( $_font['zip_url'] ) ) {
					$this->download_font( $_font['zip_url'] );
				}

				if ( ! empty( $_font['variants_css'] ) && is_array( $_font['variants_css'] ) ) {
					foreach ( $_font['variants_css'] as $variant => $css_data ) {
						$output .= $this->generate_font_face( $css_data );
					}
				}
			}

		} else {

			$output = '@import url(https://fonts.googleapis.com/css?family=' . $font_family . ');';

		}

		if ( $add_inline ) {

			if ( $google_fonts_local ) {
				wp_add_inline_style( 'groovy-menu-style', $output );
			} else {

				\GroovyMenu\PreStorage::get_instance()->set_preset_data( 'nav_menu_badges', 'font_family', $common_font_family );
			}

		} else {
			$tag_name = 'style';

			return '<' . $tag_name . '>' . $output . '</' . $tag_name . '>';

		}

		return '';

	}


	/**
	 * Parse and prepare font setting array for css rules
	 *
	 * @param string $variant   font variant.
	 * @param array  $font_info family, variants, subset.
	 * @param array  $subsets   font subset.
	 * @param string $fonts_path path to font file.
	 *
	 * @return array
	 */
	public function prepare_variant_css( $variant, $font_info, $subsets, $fonts_path = '' ) {

		if ( empty( $fonts_path ) ) {
			$upload_dir = wp_upload_dir();
			$fonts_path = $upload_dir['baseurl'] . '/grooni-local-fonts/';
		}

		$font_face = array(
			'font-family' => $font_info['family'],
			'font-style'  => 'normal',
			'font-weight' => '400',
			'src'         => '',
		);

		$parse_variant = stristr( $variant, 'italic', true );

		if ( '400' === $variant ) {
			$variant = 'regular';
		}

		if ( false !== $parse_variant ) {
			$font_face['font-style'] = 'italic';
			if ( '' !== $parse_variant ) {
				$font_face['font-weight'] = esc_attr( $parse_variant );
			}
		} else {
			if ( 'regular' === $variant ) {
				$font_face['font-weight'] = '400';
			} else {
				$font_face['font-weight'] = esc_attr( $variant );
			}
		}

		if ( ! empty( $font_info['variants'] ) ) {

			foreach ( $font_info['variants'] as $_variant ) {

				if ( $_variant['id'] !== $variant ) {
					continue;
				}

				if ( ! empty( $_variant['fontStyle'] ) ) {
					$font_face['font-style'] = $_variant['fontStyle'];
				}
				if ( ! empty( $_variant['fontWeight'] ) ) {
					$font_face['font-weight'] = $_variant['fontWeight'];
				}
				if ( ! empty( $_variant['local'] ) && is_array( $_variant['local'] ) ) {
					$locals = array();
					foreach ( $_variant['local'] as $local_f ) {
						$locals[] = "local('" . $local_f . "')";
					}
					if ( ! empty( $locals ) ) {
						$font_face['src'] .= implode( ', ', $locals );
					}
				}

				break; // no need search more in that loop.

			}

		}

		$subset = '-' . $font_info['defSubset'];
		if ( ! empty( $subsets ) ) {
			sort( $subsets, SORT_STRING );
			$subset = '-' . implode( '_', $subsets );
		}

		if ( ! empty( $font_face['src'] ) ) {
			$font_face['src'] .= ', ';
		}

		$font_face['src'] .= "url('{$fonts_path}{$font_info['id']}-{$font_info['version']}{$subset}-{$variant}.woff2') format('woff2')";
		$font_face['src'] .= ", url('{$fonts_path}{$font_info['id']}-{$font_info['version']}{$subset}-{$variant}.woff') format('woff')";

		return $font_face;
	}


	/**
	 * Generate css style with font-face param
	 *
	 * @param array $css_data params ready for css rules.
	 *
	 * @return string
	 */
	public function generate_font_face( $css_data ) {

		$font_face = '';

		if ( ! empty( $css_data['font-family'] ) && ! empty( $css_data['font-style'] ) && ! empty( $css_data['font-weight'] ) && ! empty( $css_data['src'] ) ) {
			$font_face .= "
			@font-face {
			  font-family: '{$css_data['font-family']}';
			  font-style: {$css_data['font-style']};
			  font-weight: {$css_data['font-weight']};
			  src: {$css_data['src']};
			}
			";
		}

		return $font_face;
	}


	/**
	 * Download font by URL
	 *
	 * @param string $font_url url for download.
	 * @param string $_tmppath path for download.
	 *
	 * @return bool
	 */
	public function download_font( $font_url, $_tmppath = '' ) {

		if ( empty( $this->downloaded ) && false !== get_option( $this->g_font_opt_name . '__downloaded' ) ) {
			$this->downloaded = get_option( $this->g_font_opt_name . '__downloaded' );
		}

		if ( empty( $font_url ) ) {
			// if err.
			return false;
		}

		if ( in_array( $font_url, $this->downloaded, true ) ) {
			return true;
		}

		if ( empty( $_tmppath ) ) {
			$upload_dir = wp_get_upload_dir();
			$_cpath     = trailingslashit( $upload_dir['basedir'] );
			$_tmppath   = $_cpath . 'grooni-local-fonts/';
		}

		if ( ! defined( 'FS_METHOD' ) ) {
			define( 'FS_METHOD', 'direct' );
		}

		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			$file_path = str_replace( array( '\\', '/' ), DIRECTORY_SEPARATOR, ABSPATH . '/wp-admin/includes/file.php' );

			if ( file_exists( $file_path ) ) {
				require_once $file_path;
				WP_Filesystem();
			}
		}
		if ( empty( $wp_filesystem ) ) {
			// if err.
			return false;
		}

		$font_file = null;

		// create temp folder.
		$_tmp = wp_tempnam( $font_url );

		@unlink( $_tmp );

		$font_file = download_url( $font_url, 30 );

		if ( is_wp_error( $font_file ) ) {
			preg_match( '#&variants=(\d\d\d)#i', $font_url, $font_file_par );
			if ( ! empty( $font_file_par[1] ) ) {
				$font_url_par = str_replace( $font_file_par[0], '', $font_url );
				$font_file    = download_url( $font_url_par, 30 );
			}
		}

		if ( ! is_dir( $_tmppath ) ) {
			@mkdir( $_tmppath, 0755 );
		}

		if ( ! is_wp_error( $font_file ) ) {
			$unzip = unzip_file( $font_file, $_tmppath );

			if ( is_wp_error( $unzip ) ) {
				// if err.
				return false;
			}

			@unlink( $font_file );

			$this->downloaded[] = $font_url;
			update_option( $this->g_font_opt_name . '__downloaded', $this->downloaded, false );

		} else {
			// if err.
			return false;
		}


	}


}
