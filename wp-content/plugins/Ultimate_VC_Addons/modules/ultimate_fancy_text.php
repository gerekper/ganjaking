<?php
/**
 *  UAVC Ultimate Fancy Text module file
 *
 *  @package Ultimate Fancy Text
 */

if ( ! class_exists( 'Ultimate_VC_Addons_FancyText' ) ) {
	/**
	 * Function that initializes Ultimate Fancy Text Module
	 *
	 * @class Ultimate_VC_Addons_FancyText
	 */
	class Ultimate_VC_Addons_FancyText {
		/**
		 * Constructor function that constructs default values for the Ultimate Fancy Text module.
		 *
		 * @method __construct
		 */
		public function __construct() {
			if ( Ultimate_VC_Addons::$uavc_editor_enable ) {
				add_action( 'init', array( $this, 'ultimate_fancytext_init' ) );
			}
			add_shortcode( 'ultimate_fancytext', array( $this, 'ultimate_fancytext_shortcode' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'register_fancytext_assets' ), 1 );
		}
		/**
		 * Function for Fancy text assets
		 *
		 * @since ----
		 * @access public
		 */
		public function register_fancytext_assets() {
			Ultimate_VC_Addons::ultimate_register_style( 'ultimate-vc-addons-fancytext-style', 'fancytext' );

			Ultimate_VC_Addons::ultimate_register_script( 'ultimate-vc-addons-typed-js', 'typed', false, array( 'jquery' ), ULTIMATE_VERSION, false );

			Ultimate_VC_Addons::ultimate_register_script( 'ultimate-vc-addons-easy-ticker-js', 'easy-ticker', false, array( 'jquery' ), ULTIMATE_VERSION, false );
		}
		/**
		 * Function to intialize the fancy text module
		 *
		 * @since ----
		 * @access public
		 */
		public function ultimate_fancytext_init() {
			if ( function_exists( 'vc_map' ) ) {
				vc_map(
					array(
						'name'        => __( 'Fancy Text', 'ultimate_vc' ),
						'base'        => 'ultimate_fancytext',
						'class'       => 'vc_ultimate_fancytext',
						'icon'        => 'vc_ultimate_fancytext',
						'category'    => 'Ultimate VC Addons',
						'description' => __( 'Fancy lines with animation effects.', 'ultimate_vc' ),
						'params'      => array(
							array(
								'type'       => 'textfield',
								'param_name' => 'fancytext_prefix',
								'heading'    => __( 'Prefix', 'ultimate_vc' ),
								'value'      => '',
							),
							array(
								'type'        => 'textarea',
								'heading'     => __( 'Fancy Text', 'ultimate_vc' ),
								'param_name'  => 'fancytext_strings',
								'description' => __( 'Enter each string on a new line', 'ultimate_vc' ),
								'admin_label' => true,
							),
							array(
								'type'       => 'textfield',
								'param_name' => 'fancytext_suffix',
								'heading'    => __( 'Suffix', 'ultimate_vc' ),
								'value'      => '',
							),
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Effect', 'ultimate_vc' ),
								'param_name' => 'fancytext_effect',
								'value'      => array(
									__( 'Type', 'ultimate_vc' ) => 'typewriter',
									__( 'Slide Up', 'ultimate_vc' ) => 'ticker',

								),
							),
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Alignment', 'ultimate_vc' ),
								'param_name' => 'fancytext_align',
								'value'      => array(
									__( 'Center', 'ultimate_vc' ) => 'center',
									__( 'Left', 'ultimate_vc' ) => 'left',
									__( 'Right', 'ultimate_vc' ) => 'right',
								),
							),
							array(
								'type'        => 'number',
								'heading'     => __( 'Type Speed', 'ultimate_vc' ),
								'param_name'  => 'strings_textspeed',
								'min'         => 0,
								'value'       => 35,
								'suffix'      => __( 'In Miliseconds', 'ultimate_vc' ),
								'group'       => 'Advanced Settings',
								'dependency'  => array(
									'element' => 'fancytext_effect',
									'value'   => array( 'typewriter' ),
								),
								'description' => __( 'Speed at which line progresses / Speed of typing effect.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'number',
								'heading'     => __( 'Backspeed', 'ultimate_vc' ),
								'param_name'  => 'strings_backspeed',
								'min'         => 0,
								'value'       => 0,
								'suffix'      => __( 'In Miliseconds', 'ultimate_vc' ),
								'group'       => 'Advanced Settings',
								'dependency'  => array(
									'element' => 'fancytext_effect',
									'value'   => array( 'typewriter' ),
								),
								'description' => __( 'Speed of delete / backspace effect.', 'ultimate_vc' ),
							),

							array(
								'type'        => 'number',
								'heading'     => __( 'Start Delay', 'ultimate_vc' ),
								'param_name'  => 'strings_startdelay',
								'min'         => 0,
								'value'       => '200',
								'suffix'      => __( 'In Miliseconds', 'ultimate_vc' ),
								'group'       => 'Advanced Settings',
								'dependency'  => array(
									'element' => 'fancytext_effect',
									'value'   => array( 'typewriter' ),
								),
								'description' => __( 'Example - If set to 5000, the first string will appear after 5 seconds.', 'ultimate_vc' ),
							),

							array(
								'type'        => 'number',
								'heading'     => __( 'Back Delay', 'ultimate_vc' ),
								'param_name'  => 'strings_backdelay',
								'min'         => 0,
								'value'       => '1500',
								'suffix'      => __( 'In Miliseconds', 'ultimate_vc' ),
								'group'       => 'Advanced Settings',
								'dependency'  => array(
									'element' => 'fancytext_effect',
									'value'   => array( 'typewriter' ),
								),
								'description' => __( 'Example - If set to 5000, the string will remain visible for 5 seconds before backspace effect.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'ult_switch',
								'heading'     => __( 'Enable Loop', 'ultimate_vc' ),
								'param_name'  => 'typewriter_loop',
								'value'       => 'true',
								'default_set' => true,
								'options'     => array(
									'true' => array(
										'label' => '',
										'on'    => 'Yes',
										'off'   => 'No',
									),
								),
								'group'       => 'Advanced Settings',
								'dependency'  => array(
									'element' => 'fancytext_effect',
									'value'   => array( 'typewriter' ),
								),
							),
							array(
								'type'        => 'ult_switch',
								'heading'     => __( 'Show Cursor', 'ultimate_vc' ),
								'param_name'  => 'typewriter_cursor',
								'value'       => 'true',
								'default_set' => true,
								'options'     => array(
									'true' => array(
										'label' => '',
										'on'    => 'Yes',
										'off'   => 'No',
									),
								),
								'group'       => 'Advanced Settings',
								'dependency'  => array(
									'element' => 'fancytext_effect',
									'value'   => array( 'typewriter' ),
								),
							),
							array(
								'type'       => 'textfield',
								'heading'    => __( 'Cursor Text', 'ultimate_vc' ),
								'param_name' => 'typewriter_cursor_text',
								'value'      => '|',
								'group'      => 'Advanced Settings',
								'dependency' => array(
									'element' => 'typewriter_cursor',
									'value'   => array( 'true' ),
								),
							),
							array(
								'type'        => 'number',
								'heading'     => __( 'Animation Speed', 'ultimate_vc' ),
								'param_name'  => 'strings_tickerspeed',
								'min'         => 0,
								'value'       => 200,
								'suffix'      => __( 'In Miliseconds', 'ultimate_vc' ),
								'group'       => 'Advanced Settings',
								'dependency'  => array(
									'element' => 'fancytext_effect',
									'value'   => array( 'ticker', 'ticker-down' ),
								),
								'description' => __( "Duration of 'Slide Up' animation", 'ultimate_vc' ),
							),
							array(
								'type'        => 'number',
								'heading'     => __( 'Pause Time', 'ultimate_vc' ),
								'param_name'  => 'ticker_wait_time',
								'min'         => 0,
								'value'       => '3000',
								'suffix'      => __( 'In Miliseconds', 'ultimate_vc' ),
								'group'       => 'Advanced Settings',
								'dependency'  => array(
									'element' => 'fancytext_effect',
									'value'   => array( 'ticker', 'ticker-down' ),
								),
								'description' => __( 'How long the string should stay visible?', 'ultimate_vc' ),
							),
							array(
								'type'        => 'number',
								'heading'     => __( 'Show Items', 'ultimate_vc' ),
								'param_name'  => 'ticker_show_items',
								'min'         => 1,
								'value'       => 1,
								'group'       => 'Advanced Settings',
								'dependency'  => array(
									'element' => 'fancytext_effect',
									'value'   => array( 'ticker', 'ticker-down' ),
								),
								'description' => __( 'How many items should be visible at a time?', 'ultimate_vc' ),
							),
							array(
								'type'       => 'ult_switch',
								'heading'    => __( 'Pause on Hover', 'ultimate_vc' ),
								'param_name' => 'ticker_hover_pause',
								'value'      => '',
								'options'    => array(
									'true' => array(
										'label' => '',
										'on'    => 'Yes',
										'off'   => 'No',
									),
								),
								'group'      => 'Advanced Settings',
								'dependency' => array(
									'element' => 'fancytext_effect',
									'value'   => array( 'ticker', 'ticker-down' ),
								),
							),
							array(
								'type'       => 'textfield',
								'heading'    => __( 'Extra Class', 'ultimate_vc' ),
								'param_name' => 'ex_class',
							),
							array(
								'type'             => 'ult_param_heading',
								'param_name'       => 'fancy_text_typography',
								'text'             => __( 'Fancy Text Settings', 'ultimate_vc' ),
								'value'            => '',
								'group'            => 'Typography',
								'class'            => 'ult-param-heading',
								'edit_field_class' => 'ult-param-heading-wrapper no-top-margin vc_column vc_col-sm-12',
							),
							array(
								'type'        => 'ultimate_google_fonts',
								'heading'     => __( 'Font Family', 'ultimate_vc' ),
								'param_name'  => 'strings_font_family',
								'description' => __( 'Select the font of your choice.', 'ultimate_vc' ) . ' ' . __( 'You can', 'ultimate_vc' ) . " <a target='_blank' rel='noopener' href='" . admin_url( 'admin.php?page=bsf-google-font-manager' ) . "'>" . __( 'add new in the collection here', 'ultimate_vc' ) . '</a>.',
								'group'       => 'Typography',
							),
							array(
								'type'       => 'ultimate_google_fonts_style',
								'heading'    => __( 'Font Style', 'ultimate_vc' ),
								'param_name' => 'strings_font_style',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => '',
								'heading'    => __( 'Font Size', 'ultimate_vc' ),
								'param_name' => 'strings_font_size',
								'unit'       => 'px',
								'media'      => array(

									'Desktop'          => '',
									'Tablet'           => '',
									'Tablet Portrait'  => '',
									'Mobile Landscape' => '',
									'Mobile'           => '',
								),
								'group'      => 'Typography',
							),

							array(
								'type'       => 'ultimate_responsive',
								'class'      => '',
								'heading'    => __( 'Line Height', 'ultimate_vc' ),
								'param_name' => 'strings_line_height',
								'unit'       => 'px',
								'media'      => array(

									'Desktop'          => '',
									'Tablet'           => '',
									'Tablet Portrait'  => '',
									'Mobile Landscape' => '',
									'Mobile'           => '',
								),
								'group'      => 'Typography',
							),
							array(
								'type'       => 'colorpicker',
								'heading'    => __( 'Fancy Text Color', 'ultimate_vc' ),
								'param_name' => 'fancytext_color',
								'group'      => 'Advanced Settings',
								'group'      => 'Typography',
								'dependency' => array(
									'element' => 'fancytext_effect',
									'value'   => array( 'typewriter', 'ticker', 'ticker-down' ),
								),
							),
							array(
								'type'       => 'colorpicker',
								'heading'    => __( 'Fancy Text Background', 'ultimate_vc' ),
								'param_name' => 'ticker_background',
								'group'      => 'Advanced Settings',
								'group'      => 'Typography',
								'dependency' => array(
									'element' => 'fancytext_effect',
									'value'   => array( 'typewriter', 'ticker', 'ticker-down' ),
								),
							),
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Transform', 'ultimate_vc' ),
								'param_name' => 'fancytext_trans',
								'value'      => array(
									__( 'Default', 'ultimate_vc' ) => 'unset',
									__( 'UPPERCASE', 'ultimate_vc' ) => 'uppercase',
									__( 'lowercase', 'ultimate_vc' ) => 'lowercase',
									__( 'Capitalize', 'ultimate_vc' ) => 'capitalize',
								),
								'group'      => 'Typography',
							),
							array(
								'type'             => 'ult_param_heading',
								'param_name'       => 'fancy_prefsuf_text_typography',
								'text'             => __( 'Prefix Suffix Text Settings', 'ultimate_vc' ),
								'value'            => '',
								'group'            => 'Typography',
								'class'            => 'ult-param-heading',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
							),
							array(
								'type'        => 'ultimate_google_fonts',
								'heading'     => __( 'Font Family', 'ultimate_vc' ),
								'param_name'  => 'prefsuf_font_family',
								'description' => __( 'Select the font of your choice.', 'ultimate_vc' ) . ' ' . __( 'You can', 'ultimate_vc' ) . " <a target='_blank' rel='noopener' href='" . admin_url( 'admin.php?page=bsf-google-font-manager' ) . "'>" . __( 'add new in the collection here', 'ultimate_vc' ) . '</a>.',
								'group'       => 'Typography',
							),
							array(
								'type'       => 'ultimate_google_fonts_style',
								'heading'    => __( 'Font Style', 'ultimate_vc' ),
								'param_name' => 'prefsuf_font_style',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => '',
								'heading'    => __( 'Font Size', 'ultimate_vc' ),
								'param_name' => 'prefix_suffix_font_size',
								'unit'       => 'px',
								'media'      => array(

									'Desktop'          => '',
									'Tablet'           => '',
									'Tablet Portrait'  => '',
									'Mobile Landscape' => '',
									'Mobile'           => '',
								),
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => '',
								'heading'    => __( 'Line Height', 'ultimate_vc' ),
								'param_name' => 'prefix_suffix_line_height',
								'unit'       => 'px',
								'media'      => array(

									'Desktop'          => '',
									'Tablet'           => '',
									'Tablet Portrait'  => '',
									'Mobile Landscape' => '',
									'Mobile'           => '',
								),
								'group'      => 'Typography',
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Prefix & Suffix Text Color', 'ultimate_vc' ),
								'param_name' => 'sufpref_color',
								'value'      => '',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Prefix & Suffix Background Color', 'ultimate_vc' ),
								'param_name' => 'sufpref_bg_color',
								'value'      => '',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'colorpicker',
								'heading'    => __( 'Cursor Color', 'ultimate_vc' ),
								'param_name' => 'typewriter_cursor_color',
								'group'      => 'Advanced Settings',
								'group'      => 'Typography',
								'dependency' => array(
									'element' => 'fancytext_effect',
									'value'   => array( 'typewriter' ),
								),
							),
							array(
								'type'        => 'dropdown',
								'heading'     => __( 'Markup', 'ultimate_vc' ),
								'param_name'  => 'fancytext_tag',
								'value'       => array(
									__( 'Default', 'ultimate_vc' ) => 'div',
									__( 'H1', 'ultimate_vc' ) => 'h1',
									__( 'H2', 'ultimate_vc' ) => 'h2',
									__( 'H3', 'ultimate_vc' ) => 'h3',
									__( 'H4', 'ultimate_vc' ) => 'h4',
									__( 'H5', 'ultimate_vc' ) => 'h5',
									__( 'H6', 'ultimate_vc' ) => 'h6',
									__( 'p', 'ultimate_vc' )  => 'p',
									__( 'span', 'ultimate_vc' ) => 'span',
								),
								'description' => __( 'Default is Div', 'ultimate_vc' ),
								'group'       => 'Typography',
							),
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Transform', 'ultimate_vc' ),
								'param_name' => 'fancypre_trans',
								'value'      => array(
									__( 'Default', 'ultimate_vc' ) => 'unset',
									__( 'UPPERCASE', 'ultimate_vc' ) => 'uppercase',
									__( 'lowercase', 'ultimate_vc' ) => 'lowercase',
									__( 'Capitalize', 'ultimate_vc' ) => 'capitalize',
								),
								'group'      => 'Typography',
							),
							array(
								'type'             => 'ult_param_heading',
								'text'             => "<span style='display: block;'><a href='http://bsf.io/t5ir4' target='_blank' rel='noopener'>" . __( 'Watch Video Tutorial', 'ultimate_vc' ) . " &nbsp; <span class='dashicons dashicons-video-alt3' style='font-size:30px;vertical-align: middle;color: #e52d27;'></span></a></span>",
								'param_name'       => 'notification',
								'edit_field_class' => 'ult-param-important-wrapper ult-dashicon ult-align-right ult-bold-font ult-blue-font vc_column vc_col-sm-12',
							),
							array(
								'type'             => 'css_editor',
								'heading'          => __( 'Css', 'ultimate_vc' ),
								'param_name'       => 'css_fancy_design',
								'group'            => __( 'Design ', 'ultimate_vc' ),
								'edit_field_class' => 'vc_col-sm-12 vc_column no-vc-background no-vc-border creative_link_css_editor',
							),
						),
					)
				);
			}
		}
		/**
		 * Shortcode handler function for block.
		 *
		 * @since ----
		 * @param array  $atts represts module attribuits.
		 * @param string $content value has been set to null.
		 * @access public
		 */
		public function ultimate_fancytext_shortcode( $atts, $content = null ) {

			$ult_ft_settings['fancytext_strings']         = '';
			$ult_ft_settings['fancytext_prefix']          = '';
			$ult_ft_settings['fancytext_suffix']          = '';
			$ult_ft_settings['fancytext_effect']          = '';
			$ult_ft_settings['strings_textspeed']         = '';
			$ult_ft_settings['strings_tickerspeed']       = '';
			$ult_ft_settings['typewriter_cursor']         = '';
			$ult_ft_settings['typewriter_cursor_text']    = '';
			$ult_ft_settings['typewriter_loop']           = '';
			$ult_ft_settings['fancytext_align']           = '';
			$ult_ft_settings['strings_font_family']       = '';
			$ult_ft_settings['strings_font_style']        = '';
			$ult_ft_settings['strings_font_size']         = '';
			$ult_ft_settings['sufpref_color']             = '';
			$ult_ft_settings['strings_line_height']       = '';
			$ult_ft_settings['strings_startdelay']        = '';
			$ult_ft_settings['strings_backspeed']         = '';
			$ult_ft_settings['strings_backdelay']         = '';
			$ult_ft_settings['ticker_wait_time']          = '';
			$ult_ft_settings['ticker_show_items']         = '';
			$ult_ft_settings['ticker_hover_pause']        = '';
			$ult_ft_settings['ex_class']                  = '';
			$ult_ft_settings['prefsuf_font_family']       = '';
			$ult_ft_settings['prefsuf_font_style']        = '';
			$ult_ft_settings['prefix_suffix_font_size']   = '';
			$ult_ft_settings['prefix_suffix_line_height'] = '';
			$ult_ft_settings['sufpref_bg_color']          = '';
			$ult_ft_settings['fancypre_trans']            = '';

			$ult_ft_settings['fancytext_trans'] = '';

			$id = uniqid( wp_rand() );

				$ult_ft_settings = shortcode_atts(
					array(
						'fancytext_strings'         => '',
						'fancytext_prefix'          => '',
						'fancytext_suffix'          => '',
						'fancytext_effect'          => 'typewriter',
						'strings_textspeed'         => '35',
						'strings_tickerspeed'       => '200',
						'typewriter_loop'           => 'true',
						'typewriter_cursor_color'   => '',
						'fancytext_tag'             => 'div',
						'fancytext_align'           => 'center',
						'strings_font_family'       => '',
						'strings_font_style'        => '',
						'strings_font_size'         => '',
						'sufpref_color'             => '',
						'strings_line_height'       => '',
						'strings_startdelay'        => '200',
						'strings_backspeed'         => '0',
						'strings_backdelay'         => '1500',
						'typewriter_cursor'         => 'true',
						'typewriter_cursor_text'    => '|',
						'ticker_wait_time'          => '3000',
						'ticker_show_items'         => '1',
						'ticker_hover_pause'        => '',
						'ticker_background'         => '',
						'fancytext_color'           => '',
						'prefsuf_font_family'       => '',
						'prefsuf_font_style'        => '',
						'prefix_suffix_font_size'   => '',
						'prefix_suffix_line_height' => '',
						'sufpref_bg_color'          => '',
						'ex_class'                  => '',
						'css_fancy_design'          => '',
						'fancypre_trans'            => 'unset',
						'fancytext_trans'           => 'unset',
					),
					$atts
				);

			$vc_version    = ( defined( 'WPB_VC_VERSION' ) ) ? WPB_VC_VERSION : 0;
			$is_vc_49_plus = ( version_compare( 4.9, $vc_version, '<=' ) ) ? 'ult-adjust-bottom-margin' : '';

			$string_inline_style = '';
			$string_align_style  = '';
			$vticker_inline      = '';
			$valign              = '';
			$prefsuf_style       = '';
			$css_design_style    = '';

			$css_design_style = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $ult_ft_settings['css_fancy_design'], ' ' ), 'ultimate_fancytext', $atts );

			$css_design_style = esc_attr( $css_design_style );

			if ( '' != $ult_ft_settings['strings_font_family'] ) {
				$font_family = get_ultimate_font_family( $ult_ft_settings['strings_font_family'] );
				if ( '' !== $font_family ) {
					$string_inline_style .= 'font-family:\'' . $font_family . '\';';
				}
			}

			$string_inline_style .= get_ultimate_font_style( $ult_ft_settings['strings_font_style'] );

			if ( '' != $ult_ft_settings['prefsuf_font_family'] ) {
				$font_family = get_ultimate_font_family( $ult_ft_settings['prefsuf_font_family'] );
				if ( '' !== $font_family ) {
					$prefsuf_style .= 'font-family:\'' . $font_family . '\';';
				}
			}
			$prefsuf_style .= get_ultimate_font_style( $ult_ft_settings['prefsuf_font_style'] );

			$fancy_text_id = 'uvc-type-wrap-' . wp_rand( 1000, 9999 );

			if ( is_numeric( $ult_ft_settings['strings_font_size'] ) ) {
				$ult_ft_settings['strings_font_size'] = 'desktop:' . $ult_ft_settings['strings_font_size'] . 'px;';
			}
			if ( is_numeric( $ult_ft_settings['strings_line_height'] ) ) {
				$ult_ft_settings['strings_line_height'] = 'desktop:' . $ult_ft_settings['strings_line_height'] . 'px;';
			}

			$fancy_args = array(
				'target'      => '#' . $fancy_text_id .
				'', // set targeted element e.g. unique class/id etc.
				'media_sizes' => array(
					'font-size'   => $ult_ft_settings['strings_font_size'], // set 'css property' & 'ultimate_responsive' sizes. Here $title_responsive_font_size holds responsive font sizes from user input.
					'line-height' => $ult_ft_settings['strings_line_height'],
				),
			);
			$data_list  = get_ultimate_vc_responsive_media_css( $fancy_args );

			if ( is_numeric( $ult_ft_settings['prefix_suffix_font_size'] ) ) {
				$ult_ft_settings['prefix_suffix_font_size'] = 'desktop:' . $ult_ft_settings['prefix_suffix_font_size'] . 'px !important;';
			}
			if ( is_numeric( $ult_ft_settings['prefix_suffix_line_height'] ) ) {
				$ult_ft_settings['prefix_suffix_line_height'] = 'desktop:' . $ult_ft_settings['prefix_suffix_line_height'] . 'px !important;';
			}

			$fancy_prefsuf_args = array(
				'target'      => '#' . $fancy_text_id .
				' .mycustfancy', // set targeted element e.g. unique class/id etc.
				'media_sizes' => array(
					'font-size'   => $ult_ft_settings['prefix_suffix_font_size'], // set 'css property' & 'ultimate_responsive' sizes. Here $title_responsive_font_size holds responsive font sizes from user input.
					'line-height' => $ult_ft_settings['prefix_suffix_line_height'],
				),
			);
			$prefsuf_data_list  = get_ultimate_vc_responsive_media_css( $fancy_prefsuf_args );

			if ( '' != $ult_ft_settings['sufpref_color'] ) {
				$prefsuf_style .= 'color:' . $ult_ft_settings['sufpref_color'] . ';';
			}
			if ( '' != $ult_ft_settings['sufpref_bg_color'] ) {
				$prefsuf_style .= 'background :' . $ult_ft_settings['sufpref_bg_color'] . ';';
			}

			if ( '' != $ult_ft_settings['fancytext_align'] ) {
				$string_align_style .= 'text-align:' . $ult_ft_settings['fancytext_align'] . ';';
			}

			// Order of replacement.
			$order   = array( "\r\n", "\n", "\r", '<br/>', '<br>', '<br/>' );
			$replace = '|';

			// Processes \r\n's first so they aren't converted twice.
			$str = str_replace( $order, $replace, $ult_ft_settings['fancytext_strings'] );

			$lines = explode( '|', $str );

			$count_lines = count( $lines );

			$ult_ft_settings['ex_class'] .= ' uvc-type-align-' . $ult_ft_settings['fancytext_align'] . ' ';
			if ( '' == $ult_ft_settings['fancytext_prefix'] ) {
				$ult_ft_settings['ex_class'] .= 'uvc-type-no-prefix';
			}

			if ( '' != $ult_ft_settings['fancytext_color'] ) {
				$vticker_inline .= 'color:' . $ult_ft_settings['fancytext_color'] . ';';
			}
			if ( '' != $ult_ft_settings['ticker_background'] ) {
				$vticker_inline .= 'background:' . $ult_ft_settings['ticker_background'] . ';';
				if ( 'typewriter' == $ult_ft_settings['fancytext_effect'] ) {
					$valign = 'fancytext-typewriter-background-enabled';
				} else {
					$valign = 'fancytext-background-enabled';
				}
			}
			// Fancy Text Transform.
			if ( '' != $ult_ft_settings['fancypre_trans'] ) {
				$fancy_trans = 'text-transform: ' . $ult_ft_settings['fancypre_trans'] . ';';
			}

			if ( '' != $ult_ft_settings['fancytext_trans'] ) {
				$fancyt_trans = 'text-transform: ' . $ult_ft_settings['fancytext_trans'] . ';';
			}

			$ultimate_js = get_option( 'ultimate_js' );

			$output = '<' . $ult_ft_settings['fancytext_tag'] . ' id="' . esc_attr( $fancy_text_id ) . '" ' . $data_list . ' class="uvc-type-wrap ' . esc_attr( $css_design_style ) . ' ' . esc_attr( $is_vc_49_plus ) . ' ult-responsive ' . esc_attr( $ult_ft_settings['ex_class'] ) . ' uvc-wrap-' . esc_attr( $id ) . '" style="' . esc_attr( $string_align_style ) . '">';

			if ( '' != trim( $ult_ft_settings['fancytext_prefix'] ) ) {
				$output .= '<span ' . $prefsuf_data_list . ' class="ultimate-' . esc_attr( $ult_ft_settings['fancytext_effect'] ) . '-prefix mycustfancy ult-responsive" style="' . esc_attr( $prefsuf_style ) . ' ' . esc_attr( $fancy_trans ) . '">' . esc_html( ltrim( $ult_ft_settings['fancytext_prefix'] ) ) . '</span>';
			}
			if ( 'ticker' == $ult_ft_settings['fancytext_effect'] || 'ticker-down' == $ult_ft_settings['fancytext_effect'] ) {
				if ( 'enable' != $ultimate_js ) {
					wp_enqueue_script( 'ultimate-vc-addons-easy-ticker-js' );
				}
				if ( '' != $ult_ft_settings['strings_font_size'] ) {
					$inherit_font_size = 'ultimate-fancy-text-inherit';
				} else {
					$inherit_font_size = '';
				}
				if ( 'true' != $ult_ft_settings['ticker_hover_pause'] ) {
					$ult_ft_settings['ticker_hover_pause'] = 0;
				} else {
					$ult_ft_settings['ticker_hover_pause'] = 1;
				}
				if ( 'ticker-down' == $ult_ft_settings['fancytext_effect'] ) {
					$direction = 'down';
				} else {
					$direction = 'up';
				}
				$output .= '<div id="vticker-' . esc_attr( $id ) . '" ' . $data_list . ' class="ultimate-vticker ' . esc_attr( $ult_ft_settings['fancytext_effect'] ) . ' ' . esc_attr( $valign ) . ' ' . esc_attr( $inherit_font_size ) . '" style="' . esc_attr( $vticker_inline ) . ' ' . esc_attr( $string_inline_style ) . ' ' . esc_attr( $fancyt_trans ) . '"><ul>';
				foreach ( $lines as $key => $line ) {
					if ( 0 == $key ) {
						$style = 'style="opacity:1"';
					} else {
						$style = 'style="opacity:0"';
					}
					$output .= '<li ' . $style . '>' . wp_strip_all_tags( $line ) . '</li>';
				}
					$output .= '</ul></div>';
			} else {
				if ( 'enable' != $ultimate_js ) {
					wp_enqueue_script( 'ultimate-vc-addons-typed-js' );
				}
				if ( 'true' != $ult_ft_settings['typewriter_loop'] ) {
					$ult_ft_settings['typewriter_loop'] = 'false';
				}
				if ( 'true' != $ult_ft_settings['typewriter_cursor'] ) {
					$ult_ft_settings['typewriter_cursor'] = 'false';
				}
				$strings = '[';
				foreach ( $lines as $key => $line ) {
					$strings .= '"' . __( trim( htmlspecialchars_decode( wp_strip_all_tags( $line ) ) ), 'js_composer' ) . '"';// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
					if ( ( $count_lines - 1 ) != $key ) {
						$strings .= ',';
					}
				}
					$strings .= ']';
					$output  .= '<span id="typed-' . esc_attr( $id ) . '" class="ultimate-typed-main ' . esc_attr( $valign ) . '" style="' . esc_attr( $vticker_inline ) . ' ' . esc_attr( $string_inline_style ) . ' ' . esc_attr( $fancyt_trans ) . '"></span>';
			}
			if ( '' != trim( $ult_ft_settings['fancytext_suffix'] ) ) {
				$output .= '<span ' . $prefsuf_data_list . ' class="ultimate-' . esc_attr( $ult_ft_settings['fancytext_effect'] ) . '-suffix mycustfancy ult-responsive" style="' . esc_attr( $prefsuf_style ) . ' ' . esc_attr( $fancy_trans ) . '">' . esc_html( rtrim( $ult_ft_settings['fancytext_suffix'] ) ) . '</span>';
			}
			if ( 'ticker' == $ult_ft_settings['fancytext_effect'] || 'ticker-down' == $ult_ft_settings['fancytext_effect'] ) {
				$output .= '<script type="text/javascript">
						jQuery(function($){
							$(document).ready(function(){
								if( typeof jQuery("#vticker-' . esc_attr( $id ) . '").easyTicker == "function"){
									$("#vticker-' . esc_attr( $id ) . '").find("li").css("opacity","1");
									
									$("#vticker-' . esc_attr( $id ) . '").easyTicker({
										direction: "up",
										easing: "swing",
										speed: ' . esc_attr( $ult_ft_settings['strings_tickerspeed'] ) . ',
										interval: ' . esc_attr( $ult_ft_settings['ticker_wait_time'] ) . ',
										height: "auto",
										visible: ' . esc_attr( $ult_ft_settings['ticker_show_items'] ) . ',
										mousePause: ' . esc_attr( $ult_ft_settings['ticker_hover_pause'] ) . ',
										controls: {
											up: "",
											down: "",
											toggle: "",
											playText: "Play",
											stopText: "Stop"
										}
									});
								}
							});
						});
					</script>';
			} else {
				$output .= '<script type="text/javascript"> 
						jQuery(function($){ 
							$(document).ready(function(){
								if( typeof jQuery("#typed-' . esc_attr( $id ) . '").typed == "function"){
									$("#typed-' . esc_attr( $id ) . '").typed({
										strings: ' . $strings . ',
										typeSpeed: ' . esc_attr( $ult_ft_settings['strings_textspeed'] ) . ',
										backSpeed: ' . esc_attr( $ult_ft_settings['strings_backspeed'] ) . ',
										startDelay: ' . esc_attr( $ult_ft_settings['strings_startdelay'] ) . ',
										backDelay: ' . esc_attr( $ult_ft_settings['strings_backdelay'] ) . ',
										loop: ' . esc_attr( $ult_ft_settings['typewriter_loop'] ) . ',
										loopCount: false,
										showCursor: ' . esc_attr( $ult_ft_settings['typewriter_cursor'] ) . ',
										cursorChar: "' . esc_attr( $ult_ft_settings['typewriter_cursor_text'] ) . '",
										attr: null
									});
								}
							});
						});
					</script>';
				if ( '' != $ult_ft_settings['typewriter_cursor_color'] ) {
					$output .= '<style>
							.uvc-wrap-' . esc_attr( $id ) . ' .typed-cursor {
								color:' . esc_attr( $ult_ft_settings['typewriter_cursor_color'] ) . ';
							}
						</style>';
				}
			}
			$output .= '</' . $ult_ft_settings['fancytext_tag'] . '>';

			return $output;
		}
	} // end class.
	new Ultimate_VC_Addons_FancyText();
	if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Ultimate_Fancytext' ) ) {
		/**
		 * Function that initializes Ultimate Fancy text Module
		 *
		 * @class WPBakeryShortCode_Ultimate_Fancytext
		 */
		class WPBakeryShortCode_Ultimate_Fancytext extends WPBakeryShortCode {
		}
	}
}
