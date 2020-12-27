<?php
/*----------------------------------------------------------------------------*\
	COLUMN SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Column' ) ) {
	class MPC_Column {
		public $shortcode      = 'vc_column';
		public $panel_section  = array();

		public $css_id    = '';
		public $classes   = '';
		public $sh_atts   = array();
		private $atts     = array();
		private $g_atts   = array();
		private $defaults = array();
		private $html     = '';

		function __construct() {
			global $mpc_ma_options;

			if ( ! class_exists( 'DOMDocument' ) ) {
				return;
			}

			if ( ! function_exists( 'mb_convert_encoding' ) ) {
				return;
			}

			if ( isset( $mpc_ma_options[ 'vc_row_addons' ] ) && $mpc_ma_options[ 'vc_row_addons' ] == '1' ) {
				$this->html = new DOMDocument( '1.1', 'UTF-8' );

				add_filter( 'vc_shortcode_output', array( $this, 'column_output' ), 1000, 3 );

				add_filter( 'vc_shortcodes_css_class', array( $this, 'cache_atts' ), 1000, 3 );
				add_filter( 'shortcode_atts_vc_column', 'MPC_Helper::merge_atts', 1000, 4 );
				add_filter( 'shortcode_atts_vc_column_inner', 'MPC_Helper::merge_atts', 1000, 4 );

				add_action( 'admin_init', array( $this, 'shortcode_map' ), 1000 );

				$this->getDefaults();
			}
		}

		function cache_atts( $class, $tag = '', $atts = array() ) {
			if( !in_array( $tag, array( 'vc_column', 'vc_column_inner' ) ) ) { // vc_column | vc_column_inner
				return  $class;
			}

			$cache_id = 'mpc-atts-cache-' . MPC_Helper::generate_random_string();
			$this->g_atts[ $cache_id ] = $atts;

			return $class . ' ' . $cache_id;
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_column-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_column/css/mpc_column.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_column-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_column/js/mpc_column' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function column_output( $output, $shortcode, $atts ) {
			$tag = $shortcode->settings( 'base' );

			if( !in_array( $tag, array( 'vc_column', 'vc_column_inner' ) ) ) { // vc_row | vc_row_inner
				return $output;
			}

			if ( !function_exists( 'libxml_use_internal_errors' ) ) {
				return $output;
			}

			global $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			// Handle </br> tags
			MPC_Helper::pre_parse_br_tags( $output );

			// Convert HTML markup inside scripts
			MPC_Helper::search_scripts( $output );
			MPC_Helper::search_styles( $output );
			MPC_Helper::pre_parse_namespaces( $output );

			// DOM manipulating
			libxml_use_internal_errors( true ); // Prevent entity errors

			$output = MPC_Helper::encoder( $output );
			$this->html->loadHTML( $output );
			$column = $this->html->getElementsByTagName( 'div' )->item( 0 );

			$column_classes = $column->getAttribute( 'class' );
			$atts_cache  = stripos( $column_classes, 'mpc-atts-cache-' );
			if( $atts_cache !== false ) {
				$cache_id = substr( $column_classes, $atts_cache, 25 ); // 25 = 'mpc-atts-cache-XXXXXXXXXX'
				$column_classes = trim( str_replace( $cache_id, '', $column_classes ) );

				if( isset( $this->g_atts[ $cache_id ] ) && !empty( $this->g_atts[ $cache_id ] ) ) {
					$atts = $this->g_atts[ $cache_id ];
					unset( $this->g_atts[ $cache_id ] );
				}
			}

			// Basic Shortcode Atts
			$this->atts = shortcode_atts( $this->defaults, $atts );
			$animation  = MPC_Parser::animation( $this->atts, 'array' );
			$this->css_id = $this->shortcode_styles( $this->atts );

			$this->sh_atts = is_array( $this->sh_atts ) ? $this->sh_atts : array();

			$this->sh_atts[ 'data-column-id' ] = $this->css_id;
			$this->column_class( $animation );

			if( $this->atts[ 'enable_sticky' ] != '' ) {
				$this->sh_atts[ 'data-offset' ] = esc_attr( $this->atts[ 'sticky_offset' ] );
			}

			foreach ( $animation as $attr => $value ) {
				$this->sh_atts[ $attr ] = $value;
			}

			// Link Block
			global $mpc_can_link;
			$output_prepend = '';
			$mpc_can_link = $tag === 'vc_row_inner' ? $mpc_can_link : true;
			if( $mpc_can_link && $this->atts[ 'url' ] != '' && stripos( $output, '<a' ) !== false ) {
				if( current_user_can( 'edit_posts') ) {
					$output_prepend = '<div class="mpc-notice mpc-cannot-link">' . __( '<strong>Massive Addons</strong>: Link Block could not be added to this column because it contains a link already. Check our documentation for more information: <a href="https://hub.mpcthemes.net/knowledgebase/link-column/">Link Block.</a> <br/>Psss.. This information is not visible for your visitors.', 'mpc' ) . '</div>';
				}
				$mpc_can_link = false;
			}

			// Append classes
			$column->setAttribute( 'class', $column_classes . $this->classes );

			// Append attributes
			if( count( $this->sh_atts ) > 0 ) {
				foreach ( $this->sh_atts as $key => $attribute ) {
					$column->setAttribute( $key, $attribute );
				}
			}

			// Link Block
			global $mpc_can_link;
			$mpc_can_link_next = $mpc_can_link;
			MPC_Helper::create_link_block( $column, $this->atts[ 'url' ], $mpc_can_link, true );
			$mpc_can_link = $mpc_can_link_next;

			// Clear content
			$output = $this->html->saveHTML();
			preg_match( "/<body>([\S|.|\s]*)<\/body>/mU", $output, $matches );

            if( isset( $matches[ 1 ] ) ) {
                $output = $matches[ 1 ];
            }

			MPC_Helper::post_parse_namespaces( $output );

			if( isset( $output_prepend ) ) {
				$output = $output_prepend . $output;
			}

			MPC_Helper::post_parse_scripts( $output );
			MPC_Helper::post_parse_styles( $output );

			// Clear
			unset( $html, $column, $mpc_can_link_next );
			$this->reset();

			// Jupiter Theme Fancy Title fix
			if( defined( 'THEME_NAME' ) && THEME_NAME === 'Jupiter' ) {
				MPC_Helper::jupiter_fancy_title( $output );
			}

			// Output
			return $output;
		}

		function reset() {
			$this->css_id  = '';
			$this->classes = '';
			$this->sh_atts = '';
			$this->atts    = $this->defaults;

			libxml_clear_errors();
		}

		function append_class( $output, $shortcode, $atts = '' ) {
			if( !in_array( $shortcode->settings( 'base' ), array( 'vc_column', 'vc_column_inner' ) ) ) { // vc_row | vc_row_inner
				return $output;
			}

			if( !function_exists( 'libxml_use_internal_errors' ) ) {
				return $output;
			}

			// Convert HTML markup inside scripts
			MPC_Helper::search_scripts( $output );
			MPC_Helper::search_styles( $output );
			MPC_Helper::pre_parse_namespaces( $output );

			// Set up DOMDocument
			libxml_use_internal_errors( true ); // Prevent entity errors

			$output = MPC_Helper::encoder( $output );
			$this->html->loadHTML( $output );
			$column = $this->html->getElementsByTagName( 'div' )->item( 0 );

			$column_classes = $column->getAttribute( 'class' );
			$atts_cache  = stripos( $column_classes, 'mpc-atts-cache-' );
			if( $atts_cache !== false ) {
				$cache_id = substr( $column_classes, $atts_cache, 25 ); // 25 = 'mpc-atts-cache-XXXXXXXXXX'
				$column_classes = trim( str_replace( $cache_id, '', $column_classes ) );
			}

			// Append classes
			$column->setAttribute( 'class', $column_classes . ' mpc-column' );

			// Clear content
			$output = $this->html->saveHTML();
			preg_match( "/<body>([\S|.|\s]*)<\/body>/mU", $output, $matches );

            if( isset( $matches[ 1 ] ) ) {
                $output = $matches[ 1 ];
            }

			MPC_Helper::post_parse_namespaces( $output );
			MPC_Helper::post_parse_scripts( $output );
			MPC_Helper::post_parse_styles( $output );

			// Clear
			$this->reset();

			// Jupiter Theme Fancy Title fix
			if( defined( 'THEME_NAME' ) && THEME_NAME === 'Jupiter' ) {
				MPC_Helper::jupiter_fancy_title( $output );
			}

			// Output
			return $output;
		}

		function column_class( $animation = array() ) {
			// classes logic
			$mpc_classes = ' mpc-column';
			$mpc_classes .= $this->atts[ 'enable_sticky' ] != '' ? ' mpc-column--sticky' : '';
			$mpc_classes .= $this->atts[ 'divider_enable' ] != '' ? ' mpc-column--divider' : '';
			$mpc_classes .= count( $animation ) > 0 ? ' mpc-animation' : '';

			$this->classes = $mpc_classes;
		}

		function getDefaults() {
			$this->defaults = array(
				'content_preset'          => '',
				'url'                     => '',
				'divider_enable'          => '',
				'enable_sticky'           => '',
				'sticky_offset'           => '',
				'alignment'               => '',

				'animation_in_type'       => 'none',
				'animation_in_duration'   => '300',
				'animation_in_delay'      => '0',
				'animation_in_offset'     => '100',

				'animation_loop_type'     => 'none',
				'animation_loop_duration' => '1000',
				'animation_loop_delay'    => '1000',
				'animation_loop_hover'    => '',
			);
		}

			/* Generate shortcode styles */
		function shortcode_styles( $styles ) {
			global $mpc_massive_styles;
			$css_id = uniqid( 'mpc_column-' . rand( 1, 100 ) );
			$style  = '';

			if ( $styles[ 'alignment' ] != '' ) {
				$style .= '.mpc-column[data-column-id="' . $css_id . '"] {';
					$style .= 'text-align: ' . $styles[ 'alignment' ] . ';';
				$style .= '}';
			}

			$mpc_massive_styles .= $style;

			return $css_id;
		}

		/* Map all shortcode options to Visual Composer popup */
		function shortcode_map() {
			if ( ! function_exists( 'vc_add_params' ) ) {
				return;
			}

			/* Column */
			$link_params = array(
				array(
					'type'        => 'vc_link',
					'heading'     => __( 'Link', 'mpc' ),
					'param_name'  => 'url',
					'value'       => '',
					'description' => __( 'Specify URL.', 'mpc' ),
					'weight'      => -1005,
					'group'       => __( 'Extras', 'mpc' ),
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Enable Sticky Column', 'mpc' ),
					'param_name'       => 'enable_sticky',
					'value'            => array( __( 'Yes', 'mpc' ) => 'true' ),
					'std'              => '',
					'description'      => __( 'Enable Sticky Column.', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'weight'           => -1010,
					'group'            => __( 'Extras', 'mpc' ),
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Top Offset', 'mpc' ),
					'param_name'       => 'sticky_offset',
					'value'            => 0,
					'addon'            => array(
						'icon'  => 'dashicons dashicons-arrow-down-alt',
						'align' => 'prepend',
					),
					'label'            => 'px',
					'validate'         => true,
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-clear--both',
					'dependency'       => array(
						'element' => 'enable_sticky',
						'value'   => 'true'
					),
					'weight'           => -1015,
					'group'            => __( 'Extras', 'mpc' ),
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Divider Block', 'mpc' ),
					'param_name'       => 'divider_enable',
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'description'      => __( 'Made a vertical divider from this column. Works only with Equal Height option from parent Row.', 'mpc' ),
					'weight'           => -1016,
					'group'            => __( 'Extras', 'mpc' ),
					'dependency'       => array(
						'element' => 'enable_sticky',
						'value_not_equal_to'   => 'true'
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Content Alignment', 'mpc' ),
					'param_name'       => 'alignment',
					'value'            => array(
						__( 'Default', 'mpc' ) => '',
						__( 'Left', 'mpc' )    => 'left',
						__( 'Center', 'mpc' )  => 'center',
						__( 'Right', 'mpc' )   => 'right',
						__( 'Justify', 'mpc' ) => 'justify',
					),
					'std'              => '',
					'description'      => __( 'Specify custom alignment for column content.', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'weight'           => -1020,
					'group'            => __( 'Extras', 'mpc' ),
				),
			);

			$presets = array(
				array(
					'type'        => 'mpc_preset',
					'heading'     => __( 'Style Preset', 'mpc' ),
					'param_name'  => 'preset',
					'tooltip'     => MPC_Helper::style_presets_desc(),
					'value'       => '',
					'shortcode'   => $this->shortcode,
					'wide_modal'  => true,
					'weight'      => -1000,
					'description' => __( 'Choose preset or create new one.', 'mpc' ),
					'group'       => __( 'Extras', 'mpc' ),
				),
				array(
					'type'        => 'mpc_content',
					'heading'     => __( 'Content Preset', 'mpc' ),
					'param_name'  => 'content_preset',
					'tooltip'     => MPC_Helper::content_presets_desc(),
					'value'       => '',
					'shortcode'   => $this->shortcode,
					'extended'    => true,
					'weight'      => -1001,
					'description' => __( 'Choose preset or create new one.', 'mpc' ),
					'group'       => __( 'Extras', 'mpc' ),
				),
			);

			$animation = MPC_Snippets::vc_animation();

			$params = array_merge( $presets, $link_params, $animation );
			MPC_Snippets::params_weight( $params );

			$atts = vc_get_shortcode( 'vc_column' );
			$atts[ 'params' ] = array_merge( $atts[ 'params' ] , $params );
			unset( $atts[ 'base' ] );
			vc_map_update( 'vc_column', $atts );

			$atts = vc_get_shortcode( 'vc_column_inner' );
			$atts[ 'params' ] = array_merge( $atts[ 'params' ] , $params );
			unset( $atts[ 'base' ] );
			vc_map_update( 'vc_column_inner', $atts );
		}
	}
}

if ( class_exists( 'MPC_Column' ) ) {
	global $MPC_Column;
	$MPC_Column = new MPC_Column();
}