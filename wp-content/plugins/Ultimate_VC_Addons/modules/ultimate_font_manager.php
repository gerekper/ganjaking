<?php
/**
 * Add-on Name: Google Font Manager
 * Add-on URI: http://dev.brainstormforce.com
 *
 *  @package UAVC Font manager
 */

/*
* Add-on Name: Google Font Manager
* Add-on URI: https://www.brainstormforce.com
* Usage:
	# VC Params Type: (Note - "ultimate_google_fonts_style" param must be next to "ultimate_google_fonts" param only)
		1) ultimate_google_fonts - for dropdown of google fonts in collection
			For Ex -
			array(
				"type" => "ultimate_google_fonts",
				"heading" => __("Font Family", "smile"),
				"param_name" => "heading_font"
			),
		2) ultimate_google_fonts_style - for respective google font style or default style
			For Ex -
			array(
				"type" => "ultimate_google_fonts_style",
				"heading" 		=>	__("Font Style", "smile"),
				"param_name"	=>	"heading_style"
			),
	# In respective comoponent shortcode process function
		1) Get font family -
			For Ex -
			$font_family = get_ultimate_font_family($heading_font);
		2) Get font style -
			For Ex -
			$font_style = get_ultimate_font_style($heading_style);
			// deprecated since 3.7.0 - automatically detected font and enqueue accordingly
		3) Enqueue the respective fonts - Note send number of font param as a parameter in array
			For Ex -
			$args = array(
				$heading_font
			);
			enquque_ultimate_google_fonts($args);
*/
if ( ! class_exists( 'Ultimate_Google_Font_Manager' ) ) {
	/**
	 * Function that initializes  Ultimate Parallax.
	 *
	 * @class Ultimate_Google_Font_Manager
	 */
	class Ultimate_Google_Font_Manager {
		/**
		 * Constructor function that constructs default values for the Ultimate Parallax.
		 *
		 * @method __construct
		 */
		public function __construct() {
			add_option( 'ultimate_google_fonts', array() );
			add_option( 'ultimate_selected_google_fonts', array() );
			add_action( 'wp_ajax_ultimate_google_fonts_refresh', array( $this, 'refresh_google_fonts_list' ) );
			add_action( 'wp_ajax_get_google_fonts', array( $this, 'get_google_fonts_list' ) );
			add_action( 'wp_ajax_add_google_font', array( $this, 'add_selected_google_font' ) );
			add_action( 'wp_ajax_delete_google_font', array( $this, 'delete_selected_google_font' ) );
			add_action( 'wp_ajax_update_google_font', array( $this, 'update_selected_google_font' ) );
			add_action( 'wp_ajax_get_font_variants', array( $this, 'get_font_variants_callback' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_selected_ultimate_google_fonts' ) );
		}
		/**
		 * Google font manager menu.
		 *
		 * @since ----
		 * @access public
		 */
		public function google_font_manager_menu() {
			$current_theme = wp_get_theme();
			if ( 'Smile' == $current_theme ) {
				$page = add_submenu_page(
					'smile_dashboard',
					__( 'Google Font Manager', 'ultimate_vc' ),
					__( 'Google Fonts', 'ultimate_vc' ),
					'administrator',
					'Ultimate_Font_Manager',
					array( $this, 'ultimate_font_manager_dashboard' )
				);
			} else {
				$page = add_submenu_page(
					'bsf-dashboard',
					__( 'Google Font Manager', 'ultimate_vc' ),
					__( 'Google Fonts', 'ultimate_vc' ),
					'administrator',
					'ultimate-font-manager',
					array( $this, 'ultimate_font_manager_dashboard' )
				);
			}
			add_action( 'admin_print_scripts-' . $page, array( $this, 'admin_google_font_scripts' ) );
		}
		/**
		 * Google font admin scripts.
		 *
		 * @since ----
		 * @access public
		 */
		public function admin_google_font_scripts() {
			wp_register_script( 'ultimate-google-fonts-script', UAVC_URL . 'admin/js/google-fonts-admin.js', array( 'jquery' ), ULTIMATE_VERSION, false );
			wp_enqueue_script( 'ultimate-google-fonts-script' );
			wp_localize_script(
				'ultimate-google-fonts-script',
				'uavc',
				array(
					'google_font_nonce'   => wp_create_nonce( 'uavc-google-font-nonce' ),
					'get_google_fonts'    => wp_create_nonce( 'uavc-get-google-fonts-nonce' ),
					'add_google_fonts'    => wp_create_nonce( 'uavc-add-google-fonts-nonce' ),
					'delete_google_fonts' => wp_create_nonce( 'uavc-delete-google-fonts-nonce' ),
					'update_google_fonts' => wp_create_nonce( 'uavc-update-google-fonts-nonce' ),
				)
			);

			Ultimate_VC_Addons::ultimate_register_style( 'ultimate-google-fonts-style', UAVC_URL . 'admin/css/google-fonts-admin.css', true );

			wp_enqueue_style( 'ultimate-google-fonts-style' );
		}
		/**
		 * Enqueue Google font.
		 *
		 * @since ----
		 * @access public
		 */
		public function enqueue_selected_ultimate_google_fonts() {
			$selected_fonts    = get_option( 'ultimate_selected_google_fonts' );
			$subset_main_array = array();
			if ( ! empty( $selected_fonts ) ) {

				$count     = count( $selected_fonts );
				$font_call = '';
				foreach ( $selected_fonts as $key => $sfont ) {
					$variants_array = array();
					if ( 0 != $key ) {
						$font_call .= '|';
					}
					$font_call .= $sfont['font_family'];
					if ( isset( $sfont['variants'] ) ) :
						$variants = $sfont['variants'];
						if ( ! empty( $variants ) ) {
							$font_call .= ':';
							foreach ( $variants as  $variant ) {
								$variant_selected = $variant['variant_selected'];
								if ( 'true' == $variant_selected || is_admin() ) {
									array_push( $variants_array, $variant['variant_value'] );
								}
							}
							$variants_count = count( $variants_array );
							if ( 0 != $variants_count ) {
								$font_call .= 'normal,';
							}
							foreach ( $variants_array as $vkey => $variant ) {
								$font_call .= $variant;
								if ( ( $variants_count - 1 ) != $vkey && $variants_count > 0 ) {
									$font_call .= ',';
								}
							}
						}
					endif;

					if ( ! empty( $sfont['subsets'] ) ) {
						$subset_array = array();
						foreach ( $sfont['subsets'] as $tsubset ) {
							if ( 'true' == $tsubset['subset_selected'] || true == $tsubset['subset_selected'] ) {
								array_push( $subset_main_array, $tsubset['subset_value'] );
							}
						}
					}
				}

				$subset_string = '';

				if ( ! empty( $subset_main_array ) ) {
					$subset_main_array = array_unique( $subset_main_array );

					$subset_string     = '&subset=';
					$subset_count      = count( $subset_main_array );
					$subset_main_array = array_values( $subset_main_array );

					foreach ( $subset_main_array as $skey => $subset ) {
						if ( '' !== $subset ) {
							$subset_string .= $subset;
							if ( ( $skey != $subset_count - 1 ) ) {
								$subset_string .= ',';
							}
						}
					}
				}

				$font_call = str_replace( '||', '|', $font_call );

				$link = 'https://fonts.googleapis.com/css?family=' . $font_call;

				$font_api_call = $link . $subset_string;

				wp_register_style( 'ultimate-selected-google-fonts-style', $font_api_call, array(), null );// @codingStandardsIgnoreLine.
				wp_enqueue_style( 'ultimate-selected-google-fonts-style' );
			}
		}
		/**
		 * Google font manager Dashboard menu.
		 *
		 * @since ----
		 * @access public
		 */
		public function ultimate_font_manager_dashboard() {            ?>
		<div class="wrap uavc-gfont">
			<h2>
					<?php esc_attr_e( 'Google Fonts Manager', 'ultimate_vc' ); ?>
					<input style="cursor:pointer" type="button" class="add-new-h2" id="refresh-google-fonts" value="<?php esc_attr_e( 'Refresh Font List', 'ultimate_vc' ); ?>"/>
				&nbsp;<span class="spinner"></span>
				</h2>
				<div id="vc-gf-msg"></div>
				<div class="nav">
					<input type="text" id="search_gfont" name="search_gfont" placeholder="<?php echo esc_attr__( 'Search font..', 'ultimate_vc' ); ?>"/>
				</div>
				<div>
				<div class="fonts-list">
					<div id="fonts-list-wrapper" style="overflow:auto" data-gstart="0" data-gfetch="20"></div>
					<div id="load-more" class="spinner" style="float:left"></div>
				</div>
				<div class="fonts-selected-list">
					<h3><?php echo esc_attr__( 'Your Font Collection', 'ultimate_vc' ); ?></h3>
					<div id="fonts-selected-wrapper">
						<?php
							$selected_fonts = get_option( 'ultimate_selected_google_fonts' );
						if ( ! empty( $selected_fonts ) ) {
							foreach ( $selected_fonts as $key => $sfont ) {
								?>
										<div class="selected-font">
											<div class="selected-font-top <?php echo ( ! empty( $sfont['variants'] ) || ! empty( $sfont['subsets'] ) ) ? 'have-variants' : ''; ?>">
												<div class="font-header" style="font-family:'<?php esc_attr_e( $sfont['font_name'] ); ?>'"><?php esc_html_e( $sfont['font_name'] ); ?></div>
											<?php if ( ! empty( $sfont['variants'] ) || ! empty( $sfont['subsets'] ) ) : ?>
													<i class="dashicons dashicons-arrow-down"></i>
												<?php endif; ?>
												<div class="clear"></div>
											</div>
											<span class="font-delete" data-font_name="<?php esc_attr_e( $sfont['font_name'] ); ?>"><i class="dashicons dashicons-no-alt"></i></span>
										<?php
											$is_varients = false;
										if ( ! empty( $sfont['variants'] ) || ! empty( $sfont['subsets'] ) ) :
											?>
												<div class="selected-font-content">
											<?php
												$lid          = str_replace( ' ', '-', $sfont['font_name'] );
												$variant_font = 'font-family:\'' . $sfont['font_name'] . '\';';
											if ( ! empty( $sfont['variants'] ) ) :
												$is_varients = true;
												?>
															<div class="selected-font-varient-wrapper">
													<?php
													foreach ( $sfont['variants'] as $svkey => $svariants ) {
														$variant_style = $variant_font;
														if ( preg_match( '/italic/i', $svariants['variant_value'] ) ) {
															$variant_style .= 'font-style:italic;';
														}
														$weight = 'normal';
														if ( $weight = preg_replace( '/\D/', '', $svariants['variant_value'] ) ) { // @codingStandardsIgnoreLine.
															$variant_style .= 'font-weight:' . $weight . ';';
														}
														$tlid = $lid . '-' . $svkey;
														?>
																		<span class="font-variant">
																			<input type="checkbox" id="<?php esc_attr_e( $tlid ); ?>" value="<?php esc_attr_e( $svariants['variant_value'] ); ?>" class="selected-variant-checkbox" <?php echo ( 'true' == $svariants['variant_selected'] ) ? 'checked' : ''; ?> />
																			<label style="<?php esc_attr_e( $variant_style ); ?>" for="<?php esc_attr_e( $tlid ); ?>"><?php echo $svariants['variant_value']; // @codingStandardsIgnoreLine. ?></label>
																		</span>
															<?php
													}
													?>
															</div>
													<?php
														endif;
											if ( ! empty( $sfont['subsets'] ) ) :
												?>
															<div class="<?php echo ( $is_varients ) ? 'selected-font-subset-wrapper' : ''; ?>">
													<?php
													foreach ( $sfont['subsets'] as $sbkey => $ssubset ) {
														$slid = $lid . '-subset-' . $sbkey;
														?>
																		<span class="font-subset">
																			<input type="checkbox" id="<?php esc_attr_e( $slid ); ?>" value="<?php esc_attr_e( $ssubset['subset_value'] ); ?>" class="selected-subset-checkbox" <?php echo ( 'true' == $ssubset['subset_selected'] ) ? 'checked' : ''; ?> />
																			<label style="" for="<?php esc_attr_e( $slid ); ?>"><?php echo $ssubset['subset_value']; // @codingStandardsIgnoreLine. ?></label>
																		</span>
															<?php
													}
													?>
															</div>
													<?php
														endif;
											?>
													<input type="button" class="button alignleft update-google-font-button" value="<?php echo esc_attr__( 'Update font', 'ultimate_vc' ); ?>" data-font_name="<?php esc_attr_e( $sfont['font_name'] ); ?>" />
													<span class="spinner fspinner"></span>
													<div class="clear"></div>
												</div>
												<?php
												endif;
										?>
										</div>
									<?php
							}
						}
						?>
					</div>
				</div>
				<div class="clear"></div>
				</div>
			</div>
			<?php
		}
		/**
		 * Google font manager list.
		 *
		 * @since ----
		 * @access public
		 */
		public function refresh_google_fonts_list() {

			if ( ! current_user_can( 'manage_options' ) ) {
				return false;
			}

			check_ajax_referer( 'uavc-google-font-nonce', 'security' );

			$fonts      = array();
			$temp_count = 0;
			$temp       = get_option( 'ultimate_google_fonts' );
			if ( ! empty( $temp ) ) {
				$temp_count = count( $temp );
			}
			$error = false;
			try {
				$fonts = file_get_contents( $filename = 'https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyD_6TR2RyX2VRf8bABDRXCcVqdMXB5FQvs' ); // @codingStandardsIgnoreLine.
				$fonts = json_decode( $fonts );
			} catch ( Exception $e ) {
				$error = true;
			}
			if ( true == $error || 0 == count( $fonts ) ) {
				$error = false;
				try {
					$fonts = wp_remote_get( $filename = 'https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyD_6TR2RyX2VRf8bABDRXCcVqdMXB5FQvs' ); // @codingStandardsIgnoreLine.
					$fonts = json_decode( $fonts['body'] );
				} catch ( Exception $e ) {
					$error = true;
				}
			}
			if ( true != $error || 0 == count( $fonts ) ) {
				$google_fonts      = $fonts->items;
				$google_font_count = count( $google_fonts );
				update_option( 'ultimate_google_fonts', $google_fonts );
				$response['count']   = ( $google_font_count - $temp_count );
				$response['message'] = __( ( $google_font_count - $temp_count ) . ' new fonts added. ', 'ultimate_vc' );
			} else {
				$response['count']   = 0;
				$response['message'] = __( 'Fonts could not be downloaded as there might be some issue with file_get_contents or wp_remote_get due to your server configuration.', 'ultimate_vc' );
			}
			echo wp_json_encode( $response );
			die();
		}
		/**
		 * Get Google font list.
		 *
		 * @since ----
		 * @access public
		 */
		public function get_google_fonts_list() {

			if ( ! current_user_can( 'manage_options' ) ) {
				return false;
			}

			check_ajax_referer( 'uavc-get-google-fonts-nonce', 'security' );

			$google_fonts       = get_option( 'ultimate_google_fonts' );
			$response           = array();
			$fonts              = array();
						$search = '';
			if ( ! empty( $google_fonts ) ) :
				$selected_google_fonts = get_option( 'ultimate_selected_google_fonts' );
				$temp_selected         = array();
				if ( ! empty( $selected_google_fonts ) ) {
					foreach ( $selected_google_fonts as $selected_font ) {
						array_push( $temp_selected, $selected_font['font_name'] );
					}
				}
				$start_count      = $_POST['start'];
				$fetch_count      = $_POST['fetch'];
				$search           = trim( $_POST['search'] );
				$font_slice_array = array();
				if ( '' != $search ) {
					$temp = array();
					foreach ( $google_fonts as $tkey => $tfont ) {
						if ( stripos( $tfont->family, $search ) !== false ) {
							array_push( $temp, $google_fonts[ $tkey ] );
						}
					}
					$font_slice_array = $temp;
				} else {
					$font_slice_array = array_slice( $google_fonts, $start_count, $fetch_count );
				}
				$count = count( $font_slice_array );
				foreach ( $font_slice_array as $key => $tempfont ) {
					$fontinfo = array();
					if ( in_array( $tempfont->family, $temp_selected ) ) {
						$already_selected = 'true';
					} else {
						$already_selected = 'false';
					}
					$font_call = str_replace( ' ', '+', $tempfont->family );
					$variants  = $tempfont->variants;
					$subsets   = $tempfont->subsets;
					$fontinfo  = array(
						'font_name' => $tempfont->family,
						'font_call' => $font_call,
						'variants'  => $variants,
						'subsets'   => $subsets,
						'selected'  => $already_selected,
					);
					array_push( $fonts, $fontinfo );
				}
			endif;
			$response['fonts']       = $fonts;
			$response['fonts_count'] = count( $google_fonts );
			if ( '' != $search ) {
				$response['is_search'] = 'true';
			} else {
				$response['is_search'] = 'false';
			}
			echo wp_json_encode( $response );
			die();
		}
		/**
		 * Add selected Google font.
		 *
		 * @since ----
		 * @access public
		 */
		public function add_selected_google_font() {

			if ( ! current_user_can( 'manage_options' ) ) {
				return false;
			}

			check_ajax_referer( 'uavc-add-google-fonts-nonce', 'security' );

			$font_family = $_POST['font_family'];
			$font_name   = $_POST['font_name'];
			$variants    = $_POST['variants'];
			$subsets     = $_POST['subsets'];
			$fonts       = get_option( 'ultimate_selected_google_fonts' );
			if ( empty( $fonts ) ) {
				$fonts = array();
			}
			$new_font = array(
				'font_family' => $font_family,
				'font_name'   => $font_name,
				'variants'    => $variants,
				'subsets'     => $subsets,
			);
			array_push( $fonts, $new_font );
			update_option( 'ultimate_selected_google_fonts', $fonts );
			echo 'Added';
			die();
		}
		/**
		 * Delete selected Google font.
		 *
		 * @since ----
		 * @access public
		 */
		public function delete_selected_google_font() {

			if ( ! current_user_can( 'manage_options' ) ) {
				return false;
			}

			check_ajax_referer( 'uavc-delete-google-fonts-nonce', 'security' );

			$font_name = $_POST['font_name'];
			$fonts     = get_option( 'ultimate_selected_google_fonts' );
			foreach ( $fonts as $key => $font ) {
				if ( $font['font_name'] == $font_name ) {
					unset( $fonts[ $key ] );
				}
			}
			$fonts = array_values( $fonts );
			update_option( 'ultimate_selected_google_fonts', $fonts );
			echo 'Deleted';
			die();
		}
		/**
		 * Update selected Google font.
		 *
		 * @since ----
		 * @access public
		 */
		public function update_selected_google_font() {

			if ( ! current_user_can( 'manage_options' ) ) {
				return false;
			}

			check_ajax_referer( 'uavc-update-google-fonts-nonce', 'security' );

			$font_name = $_POST['font_name'];
			$variants  = $_POST['variants'];
			$subsets   = $_POST['subsets'];
			$fonts     = get_option( 'ultimate_selected_google_fonts' );
			foreach ( $fonts as $key => $font ) {
				if ( $font['font_name'] == $font_name ) {
					$fonts[ $key ]['variants'] = $variants;
					$fonts[ $key ]['subsets']  = $subsets;
					$x                         = $key;
				}
			}
			update_option( 'ultimate_selected_google_fonts', $fonts );
			echo 'Updated';
			die();
		}
		/**
		 * Get Google font callback.
		 *
		 * @since ----
		 * @access public
		 */
		public function get_font_variants_callback() {

			if ( ! current_user_can( 'manage_options' ) ) {
				return false;
			}

			check_ajax_referer( 'uavc-get-font-variants-nonce', 'security' );

			$font_name              = $_POST['font_name'];
			$fonts                  = get_option( 'ultimate_selected_google_fonts' );
			$font_variants          = array();
			$json_variants          = array();
			$default_variant_styles = array(
				0 => array(
					'label' => 'Underline',
					'style' => 'text-decoration:underline;',
					'type'  => 'checkbox',
					'group' => 'ultimate_defaults_styles',
					'class' => 'ultimate_defaults_styles',
				),
				1 => array(
					'label' => 'Italic',
					'style' => 'font-style:italic;',
					'type'  => 'checkbox',
					'group' => 'ultimate_defaults_styles',
					'class' => 'ultimate_defaults_styles',
				),
				2 => array(
					'label' => 'Bold',
					'style' => 'font-weight:bold;',
					'type'  => 'checkbox',
					'group' => 'ultimate_defaults_styles',
					'class' => 'ultimate_defaults_styles',
				),
			);
			foreach ( $fonts as $key => $font ) {
				if ( $font['font_name'] == $font_name ) {
					$font_variants = $fonts[ $key ]['variants'];
				}
			}
			if ( ! empty( $font_variants ) ) {
				$is_italic            = false;
				$is_weight            = false;
				$uniq_grp             = uniqid( '_' );
				$pre_default_variants = array();
				foreach ( $font_variants as $key => $variant ) {
					if ( 'true' == $variant['variant_selected'] ) :
						$temp_array = array();
						$is_weight  = false;
						$is_italic  = false;
						if ( preg_match( '/italic/i', $variant['variant_value'] ) && $weight = preg_replace( '/\D/', '', $variant['variant_value'] ) ) { // @codingStandardsIgnoreLine.
							$temp_array['label'] = $variant['variant_value'];
							$temp_array['style'] = 'font-style:italic;font-weight:' . $weight . ';';
							$is_italic           = true;
							$is_weight           = true;
						} elseif ( preg_match( '/italic/i', $variant['variant_value'] ) ) {
							$temp_array['label'] = $variant['variant_value'];
							$temp_array['style'] = 'font-style:italic;';
							$is_italic           = true;
						} elseif ( $weight = preg_replace( '/\D/', '', $variant['variant_value'] ) ) { // @codingStandardsIgnoreLine.
							$temp_array['label'] = $variant['variant_value'];
							$temp_array['style'] = 'font-weight:' . $weight . ';';
							$is_weight           = true;
						}
						$temp_array['type']  = 'radio';
						$temp_array['group'] = 'style_by_google' . $uniq_grp;
						$temp_array['class'] = 'style_by_google';
						array_push( $json_variants, $temp_array );
					endif;
				}
				array_push( $pre_default_variants, $default_variant_styles[0] ); // 2 for bold
				if ( false == $is_italic ) {
					array_push( $pre_default_variants, $default_variant_styles[1] ); // 2 for bold
				}
				if ( false == $is_weight ) {
					array_push( $pre_default_variants, $default_variant_styles[2] ); // 2 for bold
				}
				$json_variants = array_merge( $pre_default_variants, $json_variants );
			} else {
				$json_variants = $default_variant_styles;
			}
			echo wp_json_encode( $json_variants );
			die();
		}

	}
	// Instantiate the Google Font Manager.
	new Ultimate_Google_Font_Manager();
}
if ( ! function_exists( 'enquque_ultimate_google_fonts' ) ) {
	/**
	 * Enqueue Google font.
	 *
	 * @since ----
	 * @param mixed $enqueue_fonts enqip fonts.
	 * @access public
	 */
	function enquque_ultimate_google_fonts( $enqueue_fonts ) {

		$selected_fonts = apply_filters( 'enquque_selected_ultimate_google_fonts_optimzed', get_option( 'ultimate_selected_google_fonts' ) );

		$skip_font_enqueue = apply_filters( 'enquque_ultimate_google_fonts_skip', false );

		if ( true == $skip_font_enqueue ) {
			return '';
		}

		$fonts       = array();
		$subset_call = '';
		if ( ! empty( $enqueue_fonts ) ) {
			foreach ( $enqueue_fonts as $key => $efont ) {
				$font_name       = '';
				$font_call       = '';
				$font_variant    = '';
				$font_arr        = array();
				$font_call_arr   = array();
				$font_weight_arr = array();
				$font_arr        = explode( '|', $efont );
				if ( isset( $font_arr[1] ) ) {
					$font_call_arr = explode( ':', $font_arr[1] );
					if ( isset( $font_arr[2] ) ) {
						$font_weight_arr = explode( ':', $font_arr[2] );
					}
					if ( isset( $font_call_arr[1] ) && '' != $font_call_arr[1] ) {
						$font_call = $font_call_arr[1];
						$font_name = $font_call_arr[1];

						foreach ( $selected_fonts as $sfont ) {
							if ( $font_name == $sfont['font_family'] ) {
								if ( ! empty( $sfont['subsets'] ) ) {
									$subset_array = array();
									foreach ( $sfont['subsets'] as $tsubset ) {
										if ( 'true' == $tsubset['subset_selected'] ) {
											array_push( $subset_array, $tsubset['subset_value'] );
										}
									}
									if ( ! empty( $subset_array ) ) :
										$subset_call = '&subset=';
										$j           = count( $subset_array );
										foreach ( $subset_array as $subkey => $subset ) {
											$subset_call .= $subset;
											if ( ( $j - 1 ) != $subkey ) {
												$subset_call .= ',';
											}
										}
									endif;
								}
							}
						}

						if ( isset( $font_weight_arr[1] ) && '' != $font_weight_arr[1] ) {
							$font_variant = $font_weight_arr[1];
						}
						$eq_name = str_replace( ' ', '-', $font_name );
						if ( '' != $font_variant || 'regular' != $font_variant ) {
							$font_call .= ':' . $font_variant;
							$eq_name   .= '-' . $font_variant;
						}
						$link = 'https://fonts.googleapis.com/css?family=' . $font_call . $subset_call;

						if ( ! wp_script_is( 'ultimate-' . $eq_name, 'registered' ) ) {
							wp_register_style( 'ultimate-' . $eq_name, $link );// @codingStandardsIgnoreLine.
							wp_enqueue_style( 'ultimate-' . $eq_name );
						} elseif ( wp_script_is( 'ultimate-' . $eq_name, 'registered' ) ) {
							wp_enqueue_style( 'ultimate-' . $eq_name );
						}
					}
				} else // font is without varients.
				{
					$eq_name = $font_arr[0];
					$link    = 'https://fonts.googleapis.com/css?family=' . $eq_name;

					if ( '' != $eq_name ) {
						if ( ! wp_script_is( 'ultimate-' . $eq_name, 'registered' ) ) {
							wp_register_style( 'ultimate-' . $eq_name, $link );// @codingStandardsIgnoreLine.
							wp_enqueue_style( 'ultimate-' . $eq_name );
						} elseif ( wp_script_is( 'ultimate-' . $eq_name, 'registered' ) ) {
							wp_enqueue_style( 'ultimate-' . $eq_name );
						}
					}
				}
			}
		}
	}
}
if ( ! function_exists( 'get_ultimate_font_family' ) ) {
	/**
	 * Get ultimate font family.
	 *
	 * @since ----
	 * @param mixed $font_attributes attr variable.
	 * @access public
	 */
	function get_ultimate_font_family( $font_attributes ) {
		if ( ! empty( $font_attributes ) ) {
			$font_family_arr = explode( '|', $font_attributes );
			$font_family_str = $font_family_arr[0];
			$font_family     = explode( ':', $font_family_str );
			if ( isset( $font_family[1] ) && '' != $font_family[1] ) {
				return $font_family[1];
			} else {
				return '';
			}
		} else {
			return '';
		}
	}
}
if ( ! function_exists( 'get_ultimate_font_style' ) ) {
	/**
	 * Get ultimate font style.
	 *
	 * @since ----
	 * @param mixed $font_style font style.
	 * @access public
	 */
	function get_ultimate_font_style( $font_style ) {
		$weight_match = 0;
		$temp         = '';
		if ( '' != $font_style ) {
			$font_styles = explode( ',', $font_style ); // split by comma<strong></strong>.
			foreach ( $font_styles as $fstyle ) {
				$temp .= $fstyle; // convert to css.
				if ( preg_match( '/font-weight:/i', $fstyle ) ) {
					$weight_match++;
				}
			}
		}
		// hack to font weight to normal if font weight not available.
		if ( 0 == $weight_match ) {
			$temp .= 'font-weight:normal;';
		}
		return $temp;
	}
}

if ( ! function_exists( 'enquque_ultimate_google_fonts_optimzed' ) ) {
	/**
	 * Get enquque ultimate google fonts optimzed.
	 *
	 * @since ----
	 * @param mixed $enqueue_fonts enquip fonts.
	 * @access public
	 */
	function enquque_ultimate_google_fonts_optimzed( $enqueue_fonts ) {

		$selected_fonts = apply_filters( 'enquque_selected_ultimate_google_fonts', get_option( 'ultimate_selected_google_fonts' ) );

		$skip_font_enqueue = apply_filters( 'enquque_ultimate_google_fonts_skip', false );

		if ( true == $skip_font_enqueue ) {
			return '';
		}

		$main              = array();
		$subset_main_array = array();
		$fonts             = array();
		$subset_call       = '';

		if ( ! empty( $enqueue_fonts ) ) {
			$font_count = 0;
			foreach ( $enqueue_fonts as $key => $efont ) {
				$font_name       = '';
				$font_call       = '';
				$font_variant    = '';
				$font_arr        = array();
				$font_call_arr   = array();
				$font_weight_arr = array();
				$font_arr        = explode( '|', $efont );

				$font_name = trim( $font_arr[0] );

				if ( ! isset( $main[ $font_name ] ) ) {
					$main[ $font_name ] = array();
				}

				if ( ! empty( $font_name ) ) :

					$font_count++;
					if ( isset( $font_arr[1] ) ) {
						$font_call_arr = explode( ':', $font_arr[1] );

						if ( isset( $font_arr[2] ) ) {
							$font_weight_arr = explode( ':', $font_arr[2] );
						}

						if ( isset( $font_call_arr[1] ) && '' != $font_call_arr[1] ) {
							$font_variant  = $font_call_arr[1];
							$pre_font_call = $font_name;

							if ( '' != $font_variant && 'regular' !== $font_variant ) {
								$main[ $font_name ]['varients'][] = $font_variant;
								array_push( $main[ $font_name ]['varients'], $font_variant );
								if ( ! empty( $main[ $font_name ]['varients'] ) ) {
									$main[ $font_name ]['varients'] = array_values( array_unique( $main[ $font_name ]['varients'] ) );
								}
							}
						}
					}

					foreach ( $selected_fonts as $sfont ) {
						if ( $sfont['font_family'] == $font_name ) {
							if ( ! empty( $sfont['subsets'] ) ) {
								$subset_array = array();
								foreach ( $sfont['subsets'] as $tsubset ) {
									if ( 'true' == $tsubset['subset_selected'] ) {
										array_push( $subset_array, $tsubset['subset_value'] );
									}
								}
								if ( ! empty( $subset_array ) ) :
									$subset_call = '';
									$j           = count( $subset_array );
									foreach ( $subset_array as $subkey => $subset ) {
										$subset_call .= $subset;
										if ( ( $j - 1 ) != $subkey ) {
											$subset_call .= ',';
										}
									}
									array_push( $subset_main_array, $subset_call );
								endif;
							}
						}
					}
				endif;
			}

			$link       = 'https://fonts.googleapis.com/css?family=';
			$main_count = count( $main );
			$mcount     = 0;

			foreach ( $main as $font => $font_data ) {
				if ( '' !== $font ) {
					$link .= $font;
					if ( 'Open+Sans+Condensed' == $font && empty( $font_data['varients'] ) ) {
						$link .= ':300';
					}
					if ( ! empty( $font_data['varients'] ) ) {
						$link         .= ':regular,';
						$varient_count = count( $font_data['varients'] );
						foreach ( $font_data['varients'] as $vkey => $varient ) {
							$link .= $varient;
							if ( ( $varient_count - 1 ) != $vkey ) {
								$link .= ',';
							}
						}
					}

					if ( ! empty( $font_data['subset'] ) ) {
						$subset_string .= '&subset=' . $font_data['subset'];
					}

					if ( ( $main_count - 1 ) != $mcount ) {
						$link .= '|';
					}
					$mcount++;
				}
			}

			$subset_string = '';

			if ( ! empty( $subset_array ) ) {
				$subset_main_array = array_unique( $subset_main_array );

				$subset_string     = '&subset=';
				$subset_count      = count( $subset_main_array );
				$subset_main_array = array_values( $subset_main_array );

				foreach ( $subset_main_array as $skey => $subset ) {
					if ( '' !== $subset ) {
						$subset_string .= $subset;
						if ( ( $subset_count - 1 ) != $skey ) {
							$subset_string .= ',';
						}
					}
				}
			}

			$font_api_call = $link . $subset_string;

			if ( $font_count > 0 ) {
				wp_enqueue_style( 'ultimate-google-fonts', $font_api_call, array(), null );// @codingStandardsIgnoreLine.
			}
		}
	}
}
