<?php
/*----------------------------------------------------------------------------*\
	IHOVER ITEM SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_iHover_Item' ) ) {
	class MPC_iHover_Item {
		public $shortcode = 'mpc_ihover_item';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_ihover_item', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_ihover_item-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_ihover_item/css/mpc_ihover_item.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_ihover_item-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_ihover_item/js/mpc_ihover_item' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			global $MPC_Shortcode, $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'                => '',
				'preset'               => '',
				'url'                  => '',
				'style'                => 'default',

				'thumbnail'            => '',

				'disable_title'        => '',
				'title'                => '',

				'disable_divider'      => '',

				'disable_content'      => '',
				'globals'              => ''
			), $atts );

			$MPC_Shortcode[ 'ihover' ] = !empty( $atts[ 'globals' ] ) ? json_decode( rawurldecode( $atts[ 'globals' ] ), true ) : $MPC_Shortcode[ 'ihover' ];

			$title_classes = $MPC_Shortcode[ 'ihover' ][ 'title_font_preset' ] != '' ? ' mpc-typography--' . $MPC_Shortcode[ 'ihover' ][ 'title_font_preset' ] : '';
			$content_classes = $MPC_Shortcode[ 'ihover' ][ 'content_font_preset' ] != '' ? ' mpc-typography--' . $MPC_Shortcode[ 'ihover' ][ 'content_font_preset' ] : '';

			$url_settings = MPC_Parser::url( $atts[ 'url' ] );
			$url_settings = $url_settings === '' ? ' href="#" ' : $url_settings;

			$thumbnail = $atts[ 'thumbnail' ] != '' ? wp_get_attachment_url( $atts[ 'thumbnail' ] ) : '';
			$thumbnail = $thumbnail == false ? '' : esc_url( $thumbnail );

			$classes = 'mpc-ihover-item ih-item';
			$classes .= ' ' . $MPC_Shortcode[ 'ihover' ][ 'shape' ];
			$classes .= ' ' . $MPC_Shortcode[ 'ihover' ][ 'effect' ];
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );
			if ( $atts[ 'style' ] != 'default' ) {
				$classes .= ' ' . esc_attr( $atts[ 'style' ] );
			} else {
				$classes .= ' ' . $MPC_Shortcode[ 'ihover' ][ 'style' ];
			}

			$return = '<div class="' . $classes . '" data-effect="' . $MPC_Shortcode[ 'ihover' ][ 'effect' ] . '">';
				$return .= '<a ' . $url_settings . '>';
					if ( $MPC_Shortcode[ 'ihover' ][ 'shape' ] == 'circle' && $MPC_Shortcode[ 'ihover' ][ 'effect' ] == 'effect1' ) {
						$return .= '<div class="spinner"></div>';
					}

					if ( $MPC_Shortcode[ 'ihover' ][ 'shape' ] == 'circle' && $MPC_Shortcode[ 'ihover' ][ 'effect' ] == 'effect8' ) {
						$return .= '<div class="img-container">';
					}

					$return .= '<div class="img"><div class="mpc-image-box" style="background-image: url(' . $thumbnail . ');"></div></div>';

					if ( $MPC_Shortcode[ 'ihover' ][ 'shape' ] == 'circle' && $MPC_Shortcode[ 'ihover' ][ 'effect' ] == 'effect8' ) {
						$return .= '</div>';
					}

					if ( $MPC_Shortcode[ 'ihover' ][ 'shape' ] == 'square' && $MPC_Shortcode[ 'ihover' ][ 'effect' ] == 'effect4' ) {
						$return .= '<div class="mask1"></div>';
						$return .= '<div class="mask2"></div>';
					}

					if ( $MPC_Shortcode[ 'ihover' ][ 'shape' ] == 'circle' && $MPC_Shortcode[ 'ihover' ][ 'effect' ] == 'effect8' ) {
						$return .= '<div class="info-container">';
					}

					$return .= '<div class="info">';
						if ( $MPC_Shortcode[ 'ihover' ][ 'shape' ] == 'circle' && ( $MPC_Shortcode[ 'ihover' ][ 'effect' ] == 'effect5' || $MPC_Shortcode[ 'ihover' ][ 'effect' ] == 'effect20' ) ) {
							$return .= '<div class="info-back">';
						}

						$return .= '<div class="info-wrap">';
							if ( $atts[ 'disable_title' ] == '' ) {
								$return .= '<h3 class="mpc-ihover-title' . $title_classes . '">' . $atts[ 'title' ] . '</h3>';
							}

							if ( $atts[ 'disable_divider' ] == '' ) {
								$return .= '<div class="mpc-ihover-divider-wrap"><div class="mpc-ihover-divider"></div></div>';
							}

							if ( $atts[ 'disable_content' ] == '' ) {
								$return .= '<div class="mpc-ihover-content' . $content_classes . '">' . wpb_js_remove_wpautop( $content, true ) . '</div>';
							}
						$return .= '</div>';

						if ( $MPC_Shortcode[ 'ihover' ][ 'shape' ] == 'circle' && ( $MPC_Shortcode[ 'ihover' ][ 'effect' ] == 'effect5' || $MPC_Shortcode[ 'ihover' ][ 'effect' ] == 'effect20' ) ) {
							$return .= '</div>';
						}
					$return .= '</div>';

					if ( $MPC_Shortcode[ 'ihover' ][ 'shape' ] == 'circle' && $MPC_Shortcode[ 'ihover' ][ 'effect' ] == 'effect8' ) {
						$return .= '</div>';
					}
				$return .= '</a>';
			$return .= '</div>';

			return $return;
		}

		/* Map all shortcode options to Visual Composer popup */
		function shortcode_map() {
			if ( ! function_exists( 'vc_map' ) ) {
				return '';
			}

			$base = array(
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Hover Style', 'mpc' ),
					'param_name'       => 'style',
					'admin_label'      => true,
					'tooltip'          => __( 'Select hover effect style. You can overwrite the style selected in parent iHover shortcode. <b>Default</b> value will use the previously selected style.', 'mpc' ),
					'value'            => array(
						__( 'Default', 'mpc' )             => 'default',
						''                                 => 'none',
						__( 'Left to right', 'mpc' )       => 'left_to_right',
						__( 'Right to left', 'mpc' )       => 'right_to_left',
						__( 'Top to bottom', 'mpc' )       => 'top_to_bottom',
						__( 'Bottom to top', 'mpc' )       => 'bottom_to_top',
						__( 'Left and right', 'mpc' )      => 'left_and_right',
						__( 'From left and right', 'mpc' ) => 'from_left_and_right',
						__( 'From top and bottom', 'mpc' ) => 'from_top_and_bottom',
						__( 'Scale up', 'mpc' )            => 'scale_up',
						__( 'Scale down', 'mpc' )          => 'scale_down',
						__( 'Scale down and up', 'mpc' )   => 'scale_down_up',
					),
					'std'              => 'default',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-ihover-style mpc-advanced-field',
				),
				array(
					'type'             => 'vc_link',
					'heading'          => __( 'Link', 'mpc' ),
					'param_name'       => 'url',
					'admin_label'      => true,
					'tooltip'          => __( 'Choose target link for iHover.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-8 vc_column mpc-first-row',
				),
				array(
					'type'             => 'attach_image',
					'heading'          => __( 'Thumbnail', 'mpc' ),
					'param_name'       => 'thumbnail',
					'holder'           => 'img',
					'tooltip'          => __( 'Choose thumbnail image.', 'mpc' ),
					'value'            => '',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Title', 'mpc' ),
					'param_name'       => 'disable_title',
					'tooltip'          => __( 'Check to disable title.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field mpc-clear--both',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Content', 'mpc' ),
					'param_name'       => 'disable_content',
					'tooltip'          => __( 'Check to disable content.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Divider', 'mpc' ),
					'param_name'       => 'disable_divider',
					'tooltip'          => __( 'Check to disable divider.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Title', 'mpc' ),
					'param_name'  => 'title',
					'admin_label' => true,
					'tooltip'     => __( 'Define title.', 'mpc' ),
					'value'       => '',
					'dependency'  => array( 'element' => 'disable_title', 'value_not_equal_to' => 'true' ),
				),
				array(
					'type'        => 'textarea_html',
					'heading'     => __( 'Content', 'mpc' ),
					'param_name'  => 'content',
					'holder'      => 'div',
					'tooltip'     => __( 'Define content. Thanks to default WordPress WYSIWYG editor you can easily format the content.', 'mpc' ),
					'value'       => '',
					'dependency'  => array( 'element' => 'disable_content', 'value_not_equal_to' => 'true' ),
				),
			);

			$class = MPC_Snippets::vc_class();

			$params = array_merge( $base, $class );

			return array(
				'name'        => __( 'iHover Item', 'mpc' ),
				'description' => __( 'Single iHover item', 'mpc' ),
				'base'        => 'mpc_ihover_item',
				'class'       => '',
//				'icon'        => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-ihover.png',
				'icon'        => 'mpc-shicon-ihover-item',
				'category'    => __( 'Massive', 'mpc' ),
				'as_child'    => array( 'only' => 'mpc_ihover' ),
				'params'      => $params,
			);
		}
	}
}
if ( class_exists( 'MPC_iHover_Item' ) ) {
	global $MPC_iHover_Item;
	$MPC_iHover_Item = new MPC_iHover_Item;
}
if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_mpc_ihover_item' ) ) {
	class WPBakeryShortCode_mpc_ihover_item extends WPBakeryShortCode {}
}
