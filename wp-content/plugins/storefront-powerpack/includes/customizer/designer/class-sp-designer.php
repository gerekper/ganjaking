<?php
/**
 * Storefront Powerpack Designer Class
 *
 * @package  Storefront_Powerpack
 * @author   Tiago Noronha
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Designer' ) ) :

	/**
	 * The Designer class
	 */
	class SP_Designer {

		const DESIGNER_SECTION = 'sp_designer_section';

		/**
		 * Setup class.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_action( 'customize_register', array( $this, 'customize_register' ), 30 );
			add_action( 'customize_controls_enqueue_scripts', array( $this, 'scripts' ) );
			add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_settings' ) );
			add_filter( 'customize_dynamic_setting_args', array( $this, 'filter_dynamic_setting_args' ), 10, 2 );
			add_filter( 'customize_dynamic_setting_class', array( $this, 'filter_dynamic_setting_class' ), 10, 3 );
			add_action( 'customize_preview_init', array( $this, 'customize_preview_init' ) );
			add_action( 'wp_head', array( $this, 'frontend' ), 9999 );
			add_action( 'wp_enqueue_scripts', array( $this, 'frontend_google_fonts' ) );
			add_filter( 'body_class', array( $this, 'body_class' ) );
		}

		/**
		 * Customizer Controls and settings
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 */
		public function customize_register( $wp_customize ) {
			require_once dirname( __FILE__ ) . '/includes/class-sp-designer-css-setting.php';
			require_once dirname( __FILE__ ) . '/includes/class-sp-designer-css-control.php';
			require_once dirname( __FILE__ ) . '/includes/class-sp-designer-notice-control.php';
			require_once dirname( __FILE__ ) . '/includes/class-sp-designer-action-control.php';

			/**
			 * Register control types
			 */
			$wp_customize->register_control_type( 'SP_Designer_CSS_Control' );

			/**
			 * Add a new section
			 */
			$wp_customize->add_section( self::DESIGNER_SECTION, array(
				'title'    => __( 'Designer', 'storefront-powerpack' ),
				'panel'    => 'sp_panel',
				'priority' => 10,
			) );

			/**
			 * Notice control
			 */
			if ( class_exists( 'SP_Designer_Notice_Control' ) ) {
				$wp_customize->add_control( new SP_Designer_Notice_Control( $wp_customize, 'sp_designer_notice', array(
					'section'  		  => self::DESIGNER_SECTION,
					'priority' 		  => 5,
				) ) );
			}

			/**
			 * Add saved controls
			 */
			$selectors = get_theme_mod( 'sp_designer_css_data' );

			if ( ! empty( $selectors ) ) {
				$priority  = 10;

				foreach ( $selectors as $id => $value ) {
					// Get label
					$saved_selector = $value['selector'];

					$selector_nicename = array_values( array_filter( $this->selectors_map() , function( $selector ) use ( $saved_selector ) {
						return ( $selector['selector'] === $saved_selector );
					} ) );

					$label = $saved_selector;

					if ( ! empty( $selector_nicename ) ) {
						$label = $selector_nicename[0]['name'];
					}

					// Create a setting for each CSS selector.
					$selector_id = 'sp_designer_css_data[' . $id . ']';

					$wp_customize->add_setting( new SP_Designer_CSS_Setting( $wp_customize, $selector_id, array(
						'value' => $value,
					) ) );

					// Create a control for each menu item.
					$wp_customize->add_control( new SP_Designer_CSS_Control( $wp_customize, $selector_id, array(
						'id'       => $id,
						'label'    => $label,
						'section'  => self::DESIGNER_SECTION,
						'priority' => $priority,
					) ) );

					$priority++;
				}
			}

			/**
			 * Action control
			 */
			if ( class_exists( 'SP_Designer_Action_Control' ) ) {
				$wp_customize->add_control( new SP_Designer_Action_Control( $wp_customize, 'sp_designer_action', array(
					'section'  		  => self::DESIGNER_SECTION,
					'priority' 		  => PHP_INT_MAX,
				) ) );
			}
		}

		/**
		 * Enqueue scripts.
		 *
		 * @return void
		 */
		public function scripts() {
			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
			wp_enqueue_script( 'sp-designer-js', SP_PLUGIN_URL . 'includes/customizer/designer/assets/js/sp-designer' . $suffix . '.js', array( 'jquery', 'wp-backbone', 'customize-controls' ), storefront_powerpack()->version, true );
			wp_enqueue_style( 'sp-designer-css', SP_PLUGIN_URL . 'includes/customizer/designer/assets/css/sp-designer.css', array(), storefront_powerpack()->version, 'all' );

			wp_enqueue_script( 'selectize-js', SP_PLUGIN_URL . 'includes/customizer/designer/assets/js/vendor/selectize.min.js', array( 'jquery', 'wp-backbone', 'customize-controls' ), storefront_powerpack()->version, true );
			wp_enqueue_style( 'selectize-css', SP_PLUGIN_URL . 'includes/customizer/designer/assets/js/vendor/selectize.min.css', array(), storefront_powerpack()->version, 'all' );
		}

		/**
		 * This function is triggered on the initialization of the Previewer in the Customizer.
		 * @access  public
		 * @since   1.0.0
		 * @return  void
		 */
		public function customize_preview_init() {
			add_action( 'wp_enqueue_scripts', array( $this, 'customize_preview_enqueue' ) );
		}

		/**
		 * Enqueue scripts for the Customizer preview.
		 * @access  public
		 * @since   1.0.0
		 * @return  void
		 */
		public function customize_preview_enqueue() {
			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
			wp_enqueue_script( 'sp-designer-preview-js', SP_PLUGIN_URL . 'includes/customizer/designer/assets/js/sp-designer-preview' . $suffix . '.js', array( 'jquery','customize-preview' ), storefront_powerpack()->version, true );
			wp_enqueue_script( 'webfont-js', SP_PLUGIN_URL . 'includes/customizer/designer/assets/js/vendor/webfont.js', array( 'customize-preview' ), '1.6.16', true );

			$web_fonts = array();

			foreach ( self::websafe_fonts() as $font ) {
				$web_fonts[] = $font['family'];
			}

			$prefix_body_class = apply_filters( 'sp_designer_body_class', '.sp-designer' );
			$prefix_global     = apply_filters( 'sp_designer_prefix_selector', '#page' );

			$settings = array(
				'webSafeFonts'       => $web_fonts,
				'selectorsMap'       => $this->selectors_map(),
				'prefixBodyClass'    => $prefix_body_class,
				'prefixOtherClasses' => $prefix_global,
			);

			$data = sprintf( 'var _wpCustomizeSPDesignerPreviewSettings = %s;', wp_json_encode( $settings ) );
			wp_scripts()->add_data( 'sp-designer-preview-js', 'data', $data );

			wp_enqueue_style( 'sp-designer-preview-css', SP_PLUGIN_URL . 'includes/customizer/designer/assets/css/sp-designer-preview.css', array(), storefront_powerpack()->version, 'all' );
		}

		/**
		 * Enqueue Settings.
		 * @access  public
		 * @since   1.0.0
		 * @return  void
		 */
		public function enqueue_settings() {
			$font_variants = array();
			foreach ( self::get_google_web_fonts() as $font ) {
				$variants = array();

				foreach ( $font['variants'] as $variant ) {
					$variants[] = array(
						'id'   => $variant,
						'text' => ucfirst( $variant ),
					);
				}

				$font_variants[] = array(
					'family'   => $font['family'],
					'variants' => $variants,
				);
			}

			$settings = array(
				'phpIntMax'    => PHP_INT_MAX,
				'section'      => self::DESIGNER_SECTION,
				'fontVariants' => $font_variants,
				'selectorsMap' => $this->selectors_map(),
				'bgImage'      => array(
					'mime_type' => 'image',
					'l10n'      => array(
						'set'    => __( 'Set as background', 'storefront-powerpack' ),
						'choose' => __( 'Choose background', 'storefront-powerpack' ),
					),
				),
			);

			$data = sprintf( 'var _wpCustomizeSPDesignerSettings = %s;', wp_json_encode( $settings ) );

			wp_scripts()->add_data( 'sp-designer-js', 'data', $data );
		}

		/**
		 * Filter a dynamic setting's constructor args.
		 *
		 * For a dynamic setting to be registered, this filter must be employed
		 * to override the default false value with an array of args to pass to
		 * the WP_Customize_Setting constructor.
		 *
		 * @access  public
		 * @since   1.0.0
		 * @param 	false|array $setting_args The arguments to the WP_Customize_Setting constructor.
		 * @param 	string      $setting_id   ID for dynamic setting, usually coming from `$_POST['customized']`.
		 * @return 	array|false
		 */
		public function filter_dynamic_setting_args( $setting_args, $setting_id ) {
			if ( ! class_exists( 'SP_Designer_CSS_Setting' ) ) {
				require_once dirname( __FILE__ ) . '/includes/class-sp-designer-css-setting.php';
			}

			if ( preg_match( SP_Designer_CSS_Setting::ID_PATTERN, $setting_id ) ) {
				$setting_args = array(
					'type' => SP_Designer_CSS_Setting::TYPE,
				);
			}
			return $setting_args;
		}

		/**
		 * Allow non-statically created settings to be constructed with custom WP_Customize_Setting subclass.
		 *
		 * @access  public
		 * @since   1.0.0
		 * @param 	string $setting_class WP_Customize_Setting or a subclass.
		 * @param 	string $setting_id    ID for dynamic setting, usually coming from `$_POST['customized']`.
		 * @param 	array  $setting_args  WP_Customize_Setting or a subclass.
		 * @return 	string
		 */
		public function filter_dynamic_setting_class( $setting_class, $setting_id, $setting_args ) {
			if ( ! class_exists( 'SP_Designer_CSS_Setting' ) ) {
				require_once dirname( __FILE__ ) . '/includes/class-sp-designer-css-setting.php';
			}

			unset( $setting_id );
			if ( ! empty( $setting_args['type'] ) && SP_Designer_CSS_Setting::TYPE === $setting_args['type'] ) {
				$setting_class = 'SP_Designer_CSS_Setting';
			}
			return $setting_class;
		}

		/**
		 * An array of websafe fonts.
		 * Taken from http://www.w3schools.com/cssref/css_websafe_fonts.asp.
		 *
		 * @access  public
		 * @since   1.0.0
		 * @return 	array The list of websafe font combinations.
		 */
		public static function websafe_fonts() {
			return apply_filters( 'sp_designer_websafe_font_combinations', array(
				array(
					'family'   => 'Arial',
					'category' => 'sans-serif',
					'variants' => array( 400, 700 ),
				),
				array(
					'family'   => 'Arial Black',
					'category' => 'sans-serif',
					'variants' => array( 400, 700 ),
				),
				array(
					'family'   => 'Comic Sans MS',
					'category' => 'sans-serif',
					'variants' => array( 400, 700 ),
				),
				array(
					'family'   => 'Courier New',
					'category' => 'monospace',
					'variants' => array( 400, 700 ),
				),
				array(
					'family'   => 'Georgia',
					'category' => 'serif',
					'variants' => array( 400, 700 ),
				),
				array(
					'family'   => 'Impact',
					'category' => 'sans-serif',
					'variants' => array( 400, 700 ),
				),
				array(
					'family'   => 'Lucida Console',
					'category' => 'monospace',
					'variants' => array( 400, 700 ),
				),
				array(
					'family'   => 'Lucida Sans',
					'category' => 'sans-serif',
					'variants' => array( 400, 700 ),
				),
				array(
					'family'   => 'Palatino',
					'category' => 'serif',
					'variants' => array( 400, 700 ),
				),
				array(
					'family'   => 'Tahoma',
					'category' => 'sans-serif',
					'variants' => array( 400, 700 ),
				),
				array(
					'family'   => 'Times New Roman',
					'category' => 'serif',
					'variants' => array( 400, 700 ),
				),
				array(
					'family'   => 'Trebuchet MS',
					'category' => 'sans-serif',
					'variants' => array( 400, 700 ),
				),
				array(
					'family'   => 'Verdana',
					'category' => 'sans-serif',
					'variants' => array( 400, 700 ),
				),
			) );
		}

		/**
		 * Loads Google Fonts from google-web-fonts.json.
		 *
		 * @access  public
		 * @since   1.0.0
		 * @return 	mixed Array of Google Fonts or False if it fails to load the list of fonts.
		 */
		public static function get_google_web_fonts() {
			$json = file_get_contents( dirname( __FILE__ ) . '/assets/json/google-web-fonts.json' );

			if ( $json ) {
				$fonts        = json_decode( $json, true );
				$google_fonts = array();

				if ( is_array( $fonts ) ) {
					foreach ( $fonts as $font ) {
						$google_fonts[] = array(
							'family'   => $font['family'],
							'category' => $font['category'],
							'variants' => $font['variants'],
						);
					}
				}

				return apply_filters( 'sp_designer_google_fonts', $google_fonts );
			}

			return false;
		}

		/**
		 * Combines Websafe fonts with the Google Fonts for output in the CSS Control.
		 *
		 * @access  public
		 * @since   1.0.0
		 * @return 	mixed Array of fonts.
		 */
		public static function customize_fonts() {
			$customize_fonts = array();

			// Theme
			$customize_fonts[] = array(
				'text'     => __( 'Theme', 'storefront-powerpack' ),
				'fonts'    => array( __( 'Default', 'storefront-powerpack' ) ),
			);

			// Websafe
			$websafe_fonts = self::websafe_fonts();
			$wf_output     = array();

			if ( $websafe_fonts ) {
				foreach ( $websafe_fonts as $font ) {
					$wf_output[] = $font['family'];
				}
			}

			$customize_fonts[] = array(
				'text'  => __( 'Web Safe Fonts', 'storefront-powerpack' ),
				'fonts' => $wf_output,
			);

			// Google Fonts
			$google_fonts = self::get_google_web_fonts();
			$gf_output    = array();

			if ( $google_fonts ) {
				foreach ( $google_fonts as $font ) {
					$gf_output[] = $font['family'];
				}
			}

			$customize_fonts[] = array(
				'text'  => __( 'Google Web Fonts', 'storefront-powerpack' ),
				'fonts' => $gf_output,
			);

			return apply_filters( 'sp_designer_customize_fonts', $customize_fonts );
		}

		/**
		 * Custom body class added when the Designer is in use.
		 *
		 * @access  public
		 * @since   1.4.4
		 * @return  array Body classes
		 */
		public function body_class( $classes ) {
			$selectors = get_theme_mod( 'sp_designer_css_data' );

			if ( ! $selectors || ! is_array( $selectors ) ) {
				return $classes;
			}

			$classes[] = 'sp-designer';

			return $classes;
		}

		/**
		 * Outputs the custom styles to the frontend.
		 *
		 * @access  public
		 * @since   1.0.0
		 * @return 	void.
		 */
		public function frontend() {
			$selectors = get_theme_mod( 'sp_designer_css_data' );

			if ( ! $selectors || ! is_array( $selectors ) ) {
				return;
			}

			// Add prefix to all selectors
			$sp_body_class = apply_filters( 'sp_designer_body_class', '.sp-designer' );
			$sp_prefix     = apply_filters( 'sp_designer_prefix_selector', '#page' );

			$output = '';

			foreach ( $selectors as $id => $css_properties ) {
				if ( ! isset( $css_properties['selector'] ) || '' === $css_properties['selector'] ) {
					continue;
				}

				if ( 'body' === $css_properties['selector'] ) {
					$selector = 'body' . $sp_body_class;
				} else {
					$selector = $sp_prefix . ' ' . $css_properties['selector'];
				}

				// Selector - start
				$output .= $selector . '{';

				// Display
				if ( isset( $css_properties['updateDisplay'] ) && 'none' == $css_properties['updateDisplay'] ) {
					$output .= 'display: none;';
				}

				// Font size
				if ( isset( $css_properties['fontSize'] ) && '' !== $css_properties['fontSize'] ) {
					$unit = 'px';
					if ( isset( $css_properties['fontSizeUnit'] ) && '' !== $css_properties['fontSizeUnit'] ) {
						$unit = esc_attr( $css_properties['fontSizeUnit'] );
					}
					$output .= 'font-size:' . esc_attr( $css_properties['fontSize'] ) . esc_attr( $unit ) . ';';
				}

				// Font family
				if ( isset( $css_properties['fontFamily'] ) && '' !== $css_properties['fontFamily'] ) {
					if ( 'Default' !== $css_properties['fontFamily'] ) {
						$output .= 'font-family:' . esc_attr( $css_properties['fontFamily'] ) . ';';
					}
				}

				// Letter spacing
				if ( isset( $css_properties['letterSpacing'] ) && '' !== $css_properties['letterSpacing'] ) {
					$unit = 'px';
					if ( isset( $css_properties['letterSpacingUnit'] ) && '' !== $css_properties['letterSpacingUnit'] ) {
						$unit = $css_properties['letterSpacingUnit'];
					}
					$output .= 'letter-spacing:' . esc_attr( $css_properties['letterSpacing'] ) . esc_attr( $unit ) . ';';
				}

				// Line height
				if ( isset( $css_properties['lineHeight'] ) && '' !== $css_properties['lineHeight'] ) {
					$output .= 'line-height:' . esc_attr( $css_properties['lineHeight'] ) . 'px' . ';';
				}

				// Font style
				if ( isset( $css_properties['fontStyle'] ) && 'italic' === $css_properties['fontStyle'] ) {
					$output .= 'font-style:italic;';
				} else {
					$output .= 'font-style:normal;';
				}

				// Font weight
				if ( isset( $css_properties['fontWeight'] ) && '' !== $css_properties['fontWeight'] ) {
					if ( 'bold' === $css_properties['fontWeight'] ) {
						$output .= 'font-weight:700;';
					} else {
						$output .= 'font-weight:' . esc_attr( $css_properties['fontWeight'] ) . ';';
					}
				}

				// Text decoration
				$text_decoration = array();
				if ( isset( $css_properties['textUnderline'] ) && 'underline' === $css_properties['textUnderline'] ) {
					$text_decoration[] = 'underline';
				}

				if ( isset( $css_properties['textLineThrough'] ) && 'line-through' === $css_properties['textLineThrough'] ) {
					$text_decoration[] = 'line-through';
				}

				if ( empty( $text_decoration ) ) {
					$text_decoration[] = 'none';
				}

				$output .= 'text-decoration:' . esc_attr( implode( ' ', $text_decoration ) ) . ';';

				// Margin top
				if ( isset( $css_properties['marginTop'] ) && '' !== $css_properties['marginTop'] ) {
					$unit = 'px';
					if ( isset( $css_properties['marginTopUnit'] ) && '' !== $css_properties['marginTopUnit'] ) {
						$unit = $css_properties['marginTopUnit'];
					}
					$output .= 'margin-top:' . esc_attr( $css_properties['marginTop'] ) . esc_attr( $unit ) . ';';
				}

				// Margin bottom
				if ( isset( $css_properties['marginBottom'] ) && '' !== $css_properties['marginBottom'] ) {
					$unit = 'px';
					if ( isset( $css_properties['marginBottomUnit'] ) && '' !== $css_properties['marginBottomUnit'] ) {
						$unit = $css_properties['marginBottomUnit'];
					}
					$output .= 'margin-bottom:' . esc_attr( $css_properties['marginBottom'] ) . esc_attr( $unit ) . ';';
				}

				// Margin left
				if ( isset( $css_properties['marginLeft'] ) && '' !== $css_properties['marginLeft'] ) {
					$unit = 'px';
					if ( isset( $css_properties['marginLeftUnit'] ) && '' !== $css_properties['marginLeftUnit'] ) {
						$unit = $css_properties['marginLeftUnit'];
					}
					$output .= 'margin-left:' . esc_attr( $css_properties['marginLeft'] ) . esc_attr( $unit ) . ';';
				}

				// Margin right
				if ( isset( $css_properties['marginRight'] ) && '' !== $css_properties['marginRight'] ) {
					$unit = 'px';
					if ( isset( $css_properties['marginRightUnit'] ) && '' !== $css_properties['marginRightUnit'] ) {
						$unit = $css_properties['marginRightUnit'];
					}
					$output .= 'margin-right:' . esc_attr( $css_properties['marginRight'] ) . esc_attr( $unit ) . ';';
				}

				// Padding top
				if ( isset( $css_properties['paddingTop'] ) && '' !== $css_properties['paddingTop'] ) {
					$unit = 'px';
					if ( isset( $css_properties['paddingTopUnit'] ) && '' !== $css_properties['paddingTopUnit'] ) {
						$unit = $css_properties['paddingTopUnit'];
					}
					$output .= 'padding-top:' . esc_attr( $css_properties['paddingTop'] ) . esc_attr( $unit ) . ';';
				}

				// Padding bottom
				if ( isset( $css_properties['paddingBottom'] ) && '' !== $css_properties['paddingBottom'] ) {
					$unit = 'px';
					if ( isset( $css_properties['paddingBottomUnit'] ) && '' !== $css_properties['paddingBottomUnit'] ) {
						$unit = $css_properties['paddingBottomUnit'];
					}
					$output .= 'padding-bottom:' . esc_attr( $css_properties['paddingBottom'] ) . esc_attr( $unit ) . ';';
				}

				// Padding left
				if ( isset( $css_properties['paddingLeft'] ) && '' !== $css_properties['paddingLeft'] ) {
					$unit = 'px';
					if ( isset( $css_properties['paddingLeftUnit'] ) && '' !== $css_properties['paddingLeftUnit'] ) {
						$unit = $css_properties['paddingLeftUnit'];
					}
					$output .= 'padding-left:' . esc_attr( $css_properties['paddingLeft'] ) . esc_attr( $unit ) . ';';
				}

				// Padding right
				if ( isset( $css_properties['paddingRight'] ) && '' !== $css_properties['paddingRight'] ) {
					$unit = 'px';
					if ( isset( $css_properties['paddingRightUnit'] ) && '' !== $css_properties['paddingRightUnit'] ) {
						$unit = $css_properties['paddingRightUnit'];
					}
					$output .= 'padding-right:' . esc_attr( $css_properties['paddingRight'] ) . esc_attr( $unit ) . ';';
				}

				// Color
				if ( isset( $css_properties['color'] ) && '' !== $css_properties['color'] ) {
					$output .= 'color:' . esc_attr( $css_properties['color'] ) . ';';
				}

				// Border width
				if ( isset( $css_properties['borderWidth'] ) && '' !== $css_properties['borderWidth'] ) {
					$unit = 'px';
					if ( isset( $css_properties['borderWidthUnit'] ) && '' !== $css_properties['borderWidthUnit'] ) {
						$unit = $css_properties['borderWidthUnit'];
					}
					$output .= 'border-width:' . esc_attr( $css_properties['borderWidth'] ) . esc_attr( $unit ) . ';';
				}

				// Border radius
				if ( isset( $css_properties['borderRadius'] ) && '' !== $css_properties['borderRadius'] ) {
					$unit = 'px';
					if ( isset( $css_properties['borderRadiusUnit'] ) && '' !== $css_properties['borderRadiusUnit'] ) {
						$unit = $css_properties['borderRadiusUnit'];
					}
					$output .= 'border-radius:' . esc_attr( $css_properties['borderRadius'] ) . esc_attr( $unit ) . ';';
				}

				// Border style
				if ( isset( $css_properties['borderStyle'] ) && '' !== $css_properties['borderStyle'] ) {
					$output .= 'border-style:' . esc_attr( $css_properties['borderStyle'] ) . ';';
				}

				// Border color
				if ( isset( $css_properties['borderColor'] ) && '' !== $css_properties['borderColor'] ) {
					$output .= 'border-color:' . esc_attr( $css_properties['borderColor'] ) . ';';
				}

				// Background color
				if ( isset( $css_properties['backgroundColor'] ) && '' !== $css_properties['backgroundColor'] ) {
					$output .= 'background-color:' . esc_attr( $css_properties['backgroundColor'] ) . ';';
				}

				// Background image
				if ( isset( $css_properties['backgroundImage'] ) ) {
					$bg_image = $css_properties['backgroundImage'];

					if ( isset( $bg_image['id'] ) && false !== wp_get_attachment_image_src( $bg_image['id'] ) ) {
						$output .= 'background-image:url(' . esc_url( $bg_image['url'] ) . ');';

						// Background repeat
						if ( isset( $css_properties['backgroundRepeat'] ) && '' !== $css_properties['backgroundRepeat'] ) {
							$output .= 'background-repeat:' . esc_attr( $css_properties['backgroundRepeat'] ) . ';';
						}

						// Background position
						if ( isset( $css_properties['backgroundPosition'] ) && '' !== $css_properties['backgroundPosition'] ) {
							$output .= 'background-position:' . esc_attr( $css_properties['backgroundPosition'] ) . ';';
						}

						// Background Attachment
						if ( isset( $css_properties['backgroundAttachment'] ) && '' !== $css_properties['backgroundAttachment'] ) {
							$output .= 'background-attachment:' . esc_attr( $css_properties['backgroundAttachment'] ) . ';';
						}
					}
				}

				// Selector end
				$output .= '}';
			}

			if ( '' !== $output ) {
				$output = '<style type="text/css" media="screen" id="storefront-powerpack-designer-css">' . esc_html( $output ) . '</style>';

				// Remove line breaks and tabs from the output.
				$output = str_replace( array( "\r", "\n", "\t" ), '', $output );

				echo $output;
			}
		}

		/**
		 * Enqueue Google Fonts for use in the frontend.
		 *
		 * @access  public
		 * @since   1.0.0
		 * @return 	void.
		 */
		public function frontend_google_fonts() {
			$selectors = get_theme_mod( 'sp_designer_css_data' );

			if ( ! $selectors || ! is_array( $selectors ) ) {
				return;
			}

			$font_families = array();

			// Go through all the selectors and figure out which fonts are Google Fonts.
			foreach ( $selectors as $id => $css_properties ) {
				if ( ! isset( $css_properties['selector'] ) || '' === $css_properties['selector'] ) {
					continue;
				}

				if ( isset( $css_properties['fontFamily'] ) && '' !== $css_properties['fontFamily'] ) {
					$google_font = array_filter( self::get_google_web_fonts(), function( $font ) use ( $css_properties ) {
						return ( $font['family'] === $css_properties['fontFamily'] );
					});

					if ( ! empty( $google_font ) ) {
						if ( ! array_key_exists( $css_properties['fontFamily'], $font_families ) ) {
							$font_families[ $css_properties['fontFamily'] ] = array();
						}

						if ( isset( $css_properties['fontVariant'] ) && ! in_array( $css_properties['fontVariant'], $font_families[ $css_properties['fontFamily'] ] ) ) {
							$font_families[ $css_properties['fontFamily'] ][] = $css_properties['fontVariant'];
						}
					}
				}
			}

			// Format font families and variants.
			if ( ! empty( $font_families ) ) {
				$output = '';
				foreach ( $font_families as $family => $variants ) {
					$output .= str_replace( ' ', '+', $family );

					if ( ! empty( $variants ) ) {
						$variants_output = ':';
						foreach ( $variants as $variant ) {
							$variants_output .= $variant . ',';
						}

						$output .= rtrim( $variants_output, ',' );
					}

					$output .= '|';
				}

				$output = rtrim( $output, '|' );

				// Enqueue fonts
				$query_args = array(
					'family' => $output
				);

				wp_enqueue_style( 'sp-google-fonts', add_query_arg( $query_args, '//fonts.googleapis.com/css' ), array(), null );
			}
		}

		/**
		 * A map of the selectors that are custumizable via the point & click interface.
		 *
		 * @access  public
		 * @since   1.0.0
		 * @return 	array $map A map of selectors.
		 */
		public function selectors_map() {
			$map = array();

			// Body
			$map['body'] = array(
				'selector' => 'body',
				'name'     => __( 'Body', 'storefront-powerpack' ),
			);

			// Headings
			for ( $i = 1; $i <= 6; $i++ ) {
				$map[ 'h' . $i ] = array(
					'selector' => '.site-main h' . $i,
					'name'     => __( 'Heading - H' . $i, 'storefront-powerpack' ),
				);
			}

			// Header
			$map['site-header'] = array(
				'selector' => '.site-header',
				'name'     => __( 'Header', 'storefront-powerpack' ),
			);

			$map['site-title'] = array(
				'selector' => '.site-branding .site-title a',
				'name'     => __( 'Site title / logo', 'storefront-powerpack' ),
			);

			$map['site-description'] = array(
				'selector' => '.site-branding .site-description',
				'name'     => __( 'Site Description', 'storefront-powerpack' ),
			);

			$map['site-logo'] = array(
				'selector' => '.site-header .site-branding img',
				'name'     => __( 'Site Logo', 'storefront-powerpack' ),
			);

			$map['site-search'] = array(
				'selector' => '.site-search',
				'name'     => __( 'Header search', 'storefront-powerpack' ),
			);

			$map['navigation-wrapper'] = array(
				'selector' => '.storefront-primary-navigation',
				'name'     => __( 'Navigation wrapper', 'storefront-powerpack' ),
			);

			$map['main-navigation'] = array(
				'selector' => '.main-navigation',
				'name'     => __( 'Main navigation', 'storefront-powerpack' ),
			);

			$map['main-navigation-link'] = array(
				'selector' => '.main-navigation ul li a',
				'name'     => __( 'Main navigation link', 'storefront-powerpack' ),
			);

			$map['secondary-navigation'] = array(
				'selector' => '.secondary-navigation',
				'name'     => __( 'Secondary navigation', 'storefront-powerpack' ),
			);

			$map['site-header-cart'] = array(
				'selector' => '.site-header-cart',
				'name'     => __( 'Header cart', 'storefront-powerpack' ),
			);

			// Breadcrumb
			$map['woocommerce-breadcrumb'] = array(
				'selector' => '.woocommerce-breadcrumb',
				'name'     => __( 'Breadcrumb', 'storefront-powerpack' ),
			);

			$map['woocommerce-breadcrumb-link'] = array(
				'selector' => '.woocommerce-breadcrumb a',
				'name'     => __( 'Breadcrumb link', 'storefront-powerpack' ),
			);

			// Main
			$map['site-main'] = array(
				'selector' => '.site-main',
				'name'     => __( 'Main content', 'storefront-powerpack' ),
			);

			$map['site-content'] = array(
				'selector' => '.site-content',
				'name'     => __( 'Content / sidebar wrapper', 'storefront-powerpack' ),
			);

			// Sidebar
			$map['sidebar'] = array(
				'selector' => '.widget-area',
				'name'     => __( 'Sidebar', 'storefront-powerpack' ),
			);

			$map['sidebar-widgets'] = array(
				'selector' => '.widget-area .widget',
				'name'     => __( 'Sidebar Widgets', 'storefront-powerpack' ),
			);

			$map['widget-title'] = array(
				'selector' => '.widget .widget-title',
				'name'     => __( 'Widget title', 'storefront-powerpack' ),
			);

			// Footer
			$map['site-footer'] = array(
				'selector' => '.site-footer',
				'name'     => __( 'Footer', 'storefront-powerpack' ),
			);

			$map['footer-widgets'] = array(
				'selector' => '.footer-widgets .widget',
				'name'     => __( 'Footer Widgets', 'storefront-powerpack' ),
			);

			// Post/Pages
			$map['post-container'] = array(
				'selector' => '.site-main article.type-post, article.type-page',
				'name'     => __( 'Post / Page Container', 'storefront-powerpack' ),
			);

			$map['page-content'] = array(
				'selector' => '.site-main .type-page .entry-content',
				'name'     => __( 'Page Content', 'storefront-powerpack' ),
			);

			$map['post-content'] = array(
				'selector' => '.site-main .type-post .entry-content',
				'name'     => __( 'Post Content', 'storefront-powerpack' ),
			);

			$map['post-meta'] = array(
				'selector' => '.site-main .type-post .entry-meta',
				'name'     => __( 'Post Meta', 'storefront-powerpack' ),
			);

			// Comments / Reviews
			$map['comments'] = array(
				'selector' => '#comments .comment-list .comment-content .comment-text, #reviews .commentlist li .comment_container',
				'name'     => __( 'Comment / Review content', 'storefront-powerpack' ),
			);

			// Buttons
			$map['button'] = array(
				'selector' => '.added_to_cart, .button, button:not(.menu-toggle), input[type=button], input[type=reset], input[type=submit]',
				'name'     => __( 'Button', 'storefront-powerpack' ),
			);

			$map['button-alt'] = array(
				'selector' => '.added_to_cart.alt, .button.alt, button.alt, input[type=button].alt, input[type=reset].alt, input[type=submit].alt',
				'name'     => __( 'Alternate Button', 'storefront-powerpack' ),
			);

			// Product Loop
			$map['product-loop'] = array(
				'selector' => 'ul.products',
				'name'     => __( 'Loop Container', 'storefront-powerpack' ),
			);

			$map['loop-product'] = array(
				'selector' => 'ul.products li.product',
				'name'     => __( 'Loop Product', 'storefront-powerpack' ),
			);

			$map['loop-product-image'] = array(
				'selector' => 'ul.products li.product .wp-post-image, ul.products li.product .attachment-woocommerce_thumbnail',
				'name'     => __( 'Loop Product Image', 'storefront-powerpack' ),
			);

			$map['loop-product-title'] = array(
				'selector' => 'ul.products li.product h2, ul.products li.product h3',
				'name'     => __( 'Loop Product Title', 'storefront-powerpack' ),
			);

			$map['loop-sale-marker'] = array(
				'selector' => 'ul.products li.product .onsale',
				'name'     => __( 'Loop Sale Marker', 'storefront-powerpack' ),
			);

			$map['loop-price'] = array(
				'selector' => 'ul.products li.product .price',
				'name'     => __( 'Loop Price', 'storefront-powerpack' ),
			);

			// Product Page
			$map['single-product-container'] = array(
				'selector' => '.single-product .type-product',
				'name'     => __( 'Single Product Container', 'storefront-powerpack' ),
			);

			$map['single-image-container'] = array(
				'selector' => '.single-product .images',
				'name'     => __( 'Single Image Container', 'storefront-powerpack' ),
			);

			$map['single-featured-image'] = array(
				'selector' => '.single-product .images img.wp-post-image',
				'name'     => __( 'Single Featured Image', 'storefront-powerpack' ),
			);

			$map['single-thumbnails-container'] = array(
				'selector' => '.single-product .images .thumbnails',
				'name'     => __( 'Single Thumbnails Container', 'storefront-powerpack' ),
			);

			$map['single-thumbnails'] = array(
				'selector' => '.single-product .images .thumbnails img',
				'name'     => __( 'Single Thumbnails', 'storefront-powerpack' ),
			);

			$map['single-product-summary'] = array(
				'selector' => '.single-product .summary',
				'name'     => __( 'Single Product Summary', 'storefront-powerpack' ),
			);

			$map['woocommerce-tabs'] = array(
				'selector' => '.woocommerce-tabs',
				'name'     => __( 'WooCommerce Tabs', 'storefront-powerpack' ),
			);

			return array_values( apply_filters( 'sp_designer_selectors_map', $map ) );
		}
	}

endif;

return new SP_Designer();