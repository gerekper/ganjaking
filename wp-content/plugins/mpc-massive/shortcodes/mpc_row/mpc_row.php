<?php
/*----------------------------------------------------------------------------*\
	ROW SHORTCODE
\*----------------------------------------------------------------------------*/

global $mpc_can_link; $mpc_can_link = true;

if ( ! class_exists( 'MPC_Row' ) ) {
	class MPC_Row {
		public $shortcode = 'vc_row';
		public $styles    = array( 'id' => '', 'css' => '' );
		public $classes   = '';
		public $before    = '';
		public $after     = '';
		public $prepend   = '';
		public $append    = '';
		public $sh_atts   = array();
		public $g_atts    = array();
		private $atts     = array();
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

				add_filter( 'vc_shortcode_output', array( $this, 'row_output' ), 1000, 3 );

				add_filter( 'vc_shortcodes_css_class', array( $this, 'cache_atts' ), 1000, 3 );
				add_filter( 'shortcode_atts_vc_row', 'MPC_Helper::merge_atts', 1000, 4 );
				add_filter( 'shortcode_atts_vc_row_inner', 'MPC_Helper::merge_atts', 1000, 4 );

				add_action( 'admin_init', array( $this, 'shortcode_map' ), 1000 );

				$this->getDefaults();
			}
		}

		function cache_atts( $class, $tag = '', $atts = array() ) {
			if( !in_array( $tag, array( 'vc_row', 'vc_row_inner' ) ) ) { // vc_row | vc_row_inner
				return  $class;
			}

			$cache_id = 'mpc-atts-cache-' . MPC_Helper::generate_random_string();
			$this->g_atts[ $cache_id ] = $atts;

			return $class . ' ' . $cache_id;
		}

		function verify_node( DOMNode $row ) { // ToDo: Find more themes related classes
			$valid_classes = apply_filters( 'ma/row/valid_classes', array( 'vc_row', 'wpb_row', 'x-container' ) );
			$row_class = $row->getAttribute( 'class' );

			foreach( $valid_classes as $valid_class ) {
				if( preg_match( '/(?<![\S])' . $valid_class . '/mU', $row_class ) == 1 ) {
					return $row;
				}
			}

			$nodes = $this->html->getElementsByTagName( 'div' );
			foreach( $nodes as $node ) {
				$node_class = $node->getAttribute( 'class' );
				foreach( $valid_classes as $valid_class ) {
					if( preg_match( '/(?<![\S])' . $valid_class . '/mU', $node_class ) == 1 ) {
						return $node;
					}
				}
			}

			$nodes = $this->html->getElementsByTagName( 'section' );
			foreach( $nodes as $node ) {
				$node_class = $node->getAttribute( 'class' );
				foreach( $valid_classes as $valid_class ) {
					if( preg_match( '/(?<![\S])' . $valid_class . '/mU', $node_class ) == 1 ) {
						return $node;
					}
				}
			}

			return $row;
		}

		function detect_closing_node( $output ) {
			$return = array(
				'output'  => $output,
				'append'  => '',
				'prepend' => '',
			);

			// Find first opening tag and return if at 0/1
			preg_match( '/(<(?!\/))/im', $output, $position, PREG_OFFSET_CAPTURE );
			if( !isset( $position[ 0 ][ 1 ] ) || $position[ 0 ][ 1 ] <= 1 ) {
				return $return;
			}

			// Strip and save the closing elements
			if( !isset( $position[ 0 ][ 1 ] ) ) { return $return; }

			$return[ 'prepend' ] = substr( $output, 0, $position[ 0 ][ 1 ] );
			$return[ 'output' ]  = $output = substr_replace( $output, '', 0, $position[ 0 ][ 1 ] );

			// Detect last closing element and strip content after it
			$position = strpos( $output, '>', strrpos( $output, '</' ) ) + 1;
			$len = strlen( $output );

			if( $position < $len ) {
				$return[ 'append' ] = substr( $output, $position );
				$return[ 'output' ] = substr_replace( $output, '', $position );
			}

			return $return;
		}

		public function row_output( $output, $shortcode, $atts ) {
			$tag = $shortcode->settings( 'base' );

			if( $output === '' || !in_array( $tag, array( 'vc_row', 'vc_row_inner' ) ) ) { // vc_row | vc_row_inner
				return $output;
			}

			if( !function_exists( 'libxml_use_internal_errors' ) ) {
				return $output;
			}

			// Prepare and prechecks
			$this->atts = shortcode_atts( $this->defaults, $atts );
			$animation = MPC_Parser::animation( $this->atts, 'array' );
			if( $this->atts[ 'url' ] == ''
				&& $this->atts[ 'toggle_enable' ] == ''
			    && $this->atts[ 'enable_date' ] == ''
			    && $this->atts[ 'enable_first_overlay' ] == ''
			    && $this->atts[ 'enable_second_overlay' ] == ''
			    && $this->atts[ 'enable_parallax' ] == ''
			    && $this->atts[ 'enable_top_separator' ] == ''
			    && $this->atts[ 'enable_bottom_separator' ] == ''
			    && count( $animation ) == 0 ) {

				return $this->append_class( $output, $shortcode );
			}

			// Convert HTML markup inside scripts
			MPC_Helper::search_scripts( $output );
			MPC_Helper::search_styles( $output );
			MPC_Helper::pre_parse_namespaces( $output );

			// Save Prepend / Append markups
			$output_elements = $this->detect_closing_node( $output );

			// Set up DOMDocument content
			libxml_use_internal_errors( true ); // Prevent entity errors
			$content_html = new DOMDocument( '1.1', 'UTF-8' );

			$this->html->loadHTML( mb_convert_encoding( $output, 'HTML-ENTITIES', 'UTF-8' ) );
			$can_be_row = $this->html->documentElement->firstChild->childNodes->item( 0 );

			// Verify selected node
			$row = $this->verify_node( $can_be_row );

			$row_classes = $row->getAttribute( 'class' );
			$atts_cache  = stripos( $row_classes, 'mpc-atts-cache-' );
			if( $atts_cache !== false ) {
				$cache_id = substr( $row_classes, $atts_cache, 25 ); // 25 = 'mpc-atts-cache-XXXXXXXXXX'
				$row_classes = trim( str_replace( $cache_id, '', $row_classes ) );

				if( isset( $this->g_atts[ $cache_id ] ) && !empty( $this->g_atts[ $cache_id ] ) ) {
					$this->atts = shortcode_atts( $this->defaults, $this->g_atts[ $cache_id ] );
					unset( $this->g_atts[ $cache_id ] );
				}
			}

			$this->styles = $this->shortcode_styles( $this->atts );

			$this->sh_atts = is_array( $this->sh_atts ) ? $this->sh_atts : array();

			$this->sh_atts[ 'data-row-id' ] = $this->styles[ 'id' ];
			$this->row_class();

			$this->classes .= count( $animation ) > 0 ? ' mpc-animation' : '';
			foreach( $animation as $attr => $value ) {
				$this->sh_atts[ $attr ] = $value;
			}

			// Time Based Display
			if( $this->time_based() ) {
				return '';
			}

			// Prepare data
			$this->row_separator();
			$this->row_overlays();
			$this->toggle_row();

			// Link Block
			global $mpc_can_link;
			$mpc_can_link = $tag === 'vc_row_inner' ? $mpc_can_link : true;
			if( $mpc_can_link && $this->atts[ 'url' ] != '' && stripos( $output, '<a' ) !== false ) {
				if( current_user_can( 'edit_posts') ) {
					$this->before .= '<div class="mpc-notice mpc-cannot-link">' . __( '<strong>Massive Addons</strong>: Link Block could not be added to this row because it contains a link already. Check our documentation for more information: <a href="https://hub.mpcthemes.net/knowledgebase/link-column/">Link Block.</a> <br/>Psss.. This information is not visible for your visitors.', 'mpc' ) . '</div>';
				}
				$mpc_can_link = false;
			}

			// Append classes
			$row->setAttribute( 'class', $row_classes . $this->classes );

			// Append attributes
			if( count( $this->sh_atts ) > 0 ) {
				foreach( $this->sh_atts as $key => $attribute ) {
					$row->setAttribute( $key, $attribute );
				}
			}

			// Prepend Row
			if( $this->prepend != '' ) {
				$content_html->loadHTML( mb_convert_encoding( $this->prepend, 'HTML-ENTITIES', 'UTF-8' ) );
				$nodes = $content_html->documentElement->firstChild->childNodes;

				foreach( $nodes as $node ) {
					$new_node = $this->html->importNode( $node, true );
					$row->insertBefore( $new_node, $row->firstChild );
				}
			}

			// Append Row
			if( $this->append != '' ) {
				$content_html->loadHTML( mb_convert_encoding( $this->append, 'HTML-ENTITIES', 'UTF-8' ) );
				$nodes = $content_html->documentElement->firstChild->childNodes;

				foreach ( $nodes as $node ) {
					$new_node = $this->html->importNode( $node, true );
					$row->appendChild( $new_node );
				}
			}

			// Clear Parallax
			if( isset( $atts[ 'video_bg_parallax' ] ) && $atts[ 'video_bg_parallax' ] == '' ) {
				$row->removeAttribute( 'data-vc-parallax-image' );
			}

			// Link Block
			MPC_Helper::create_link_block( $row, $this->atts[ 'url' ], $mpc_can_link );

			// Clear content
			$output = $this->html->saveHTML();
			preg_match( "/<body>([\S|.|\s]*)<\/body>/mU", $output, $matches );

            if( isset( $matches[ 1 ] ) ) {
                $output = isset( $mpc_ma_options[ 'use_decoder' ] ) && $mpc_ma_options[ 'use_decoder' ] == '1' ? MPC_Helper::decoder( $matches[ 1 ] ) : $matches[ 1 ];
            }

			MPC_Helper::post_parse_namespaces( $output );
			$output = $output_elements[ 'prepend' ] . $output . $output_elements[ 'append' ];
			MPC_Helper::post_parse_scripts( $output );
			MPC_Helper::post_parse_styles( $output );

			$output = $this->before . $output . $this->after;

			unset( $row, $content_html, $nodes, $output_elements, $matches );

			// Clear
			$this->reset();

			// Output
			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$output .= '<style>' . $this->styles[ 'css' ] . '</style>';
			}

			return $output;
		}

		function append_class( $output, $shortcode, $atts = '' ) {
			global $mpc_ma_options;

			$tag = $shortcode->settings( 'base' );

			if( $output === '' || !in_array( $tag, array( 'vc_row', 'vc_row_inner' ) ) ) { // vc_row | vc_row_inner
				return $output;
			}

			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			if( !function_exists( 'libxml_use_internal_errors' ) ) {
				return $output;
			}

			// Pre-load HTML to check if child nodes exists at all

			$this->html->loadHTML( mb_convert_encoding( $output, 'HTML-ENTITIES', 'UTF-8' ) );

			if ( empty( $this->html->documentElement->firstChild->childNodes ) ) {
				return $output;
			}

			MPC_Helper::search_scripts( $output );
			MPC_Helper::search_styles( $output );
			MPC_Helper::pre_parse_namespaces( $output );

			// Save Prepend / Append markups
			$output_elements = $this->detect_closing_node( $output );

			// Set up DOMDocument content
			libxml_use_internal_errors( true ); // Prevent entity errors

			$this->html->loadHTML( mb_convert_encoding( $output, 'HTML-ENTITIES', 'UTF-8' ) );

			$can_be_row = $this->html->documentElement->firstChild->childNodes->item( 0 );

			// Verify selected node
			$row = $this->verify_node( $can_be_row );

			$row_classes = $row->getAttribute( 'class' );
			$atts_cache  = stripos( $row_classes, 'mpc-atts-cache-' );
			if( $atts_cache !== false ) {
				$cache_id = substr( $row_classes, $atts_cache, 25 ); // 25 = 'mpc-atts-cache-XXXXXXXXXX'
				$row_classes = trim( str_replace( $cache_id, '', $row_classes ) );

				if( isset( $this->g_atts[ $cache_id ] ) && !empty( $this->g_atts[ $cache_id ] ) ) {
					unset( $this->g_atts[ $cache_id ] );
				}
			}

			// Append classes
			$this->row_class();
			$row->setAttribute( 'class', $row_classes . $this->classes );

			// Clear content
			$output = $this->html->saveHTML();
			preg_match( "/<body>([\S|.|\s]*)<\/body>/mU", $output, $matches );

			if( isset( $matches[ 1 ] ) ) {
                $output = isset( $mpc_ma_options[ 'use_decoder' ] ) && $mpc_ma_options[ 'use_decoder' ] == '1' ? MPC_Helper::decoder( $matches[ 1 ] ) : $matches[ 1 ];
            }

			MPC_Helper::post_parse_namespaces( $output );
			$output = $output_elements[ 'prepend' ] . $output . $output_elements[ 'append' ];
			MPC_Helper::post_parse_scripts( $output );
			MPC_Helper::post_parse_styles( $output );

			unset( $row, $nodes, $output_elements, $matches );

			// Clear
			$this->reset();

			// Output
			return $output;
		}

		function reset() {
			$this->styles  = array( 'id' => '', 'css' => '' );
			$this->classes = '';
			$this->sh_atts = '';
			$this->before  = '';
			$this->after   = '';
			$this->prepend = '';
			$this->append  = '';
			$this->atts    = $this->defaults;

			libxml_clear_errors();
		}

		function row_class() {
			// classes logic
			$mpc_classes = ' mpc-row';

			if ( isset( $this->atts[ 'enable_bottom_separator' ] ) && $this->atts[ 'enable_bottom_separator' ] == 'true'
			     || isset( $this->atts[ 'enable_top_separator' ] ) && $this->atts[ 'enable_top_separator' ] == 'true'
			) {
				$mpc_classes .= ' mpc-with-separator';
			}

			if ( isset( $this->atts[ 'toggle_enable' ] ) ) {
				$mpc_classes .= $this->atts[ 'toggle_state' ] == 'opened' ? ' mpc-toggled' : '';
			}

			if ( isset( $this->atts[ 'divider_enable' ] ) ) {
				$mpc_classes .= $this->atts[ 'divider_enable' ] != '' ? ' mpc-divider-block' : '';
			}

			$this->classes = $mpc_classes;
		}

		function time_based() {
			if ( $this->atts[ 'enable_date' ] == 'true' && ( $this->atts[ 'from_date' ] != '' || $this->atts[ 'to_date' ] != '' ) ) {
				$current   = time();

				$from_date = $this->atts[ 'from_date' ] != '' ? strtotime( str_replace( '/', '-', $this->atts[ 'from_date' ] ) ) : $current - 1;
				$from_date = $from_date === false ? $current - 1 : $from_date;

				$to_date   = $this->atts[ 'to_date' ] != '' ? strtotime( str_replace( '/', '-', $this->atts[ 'to_date' ] ) ) : $current + 1;
				$to_date   = $to_date === false ? $current + 1 : $to_date;

				if ( $from_date >= $current || $to_date <= $current ) {
					return true;
				}
			}

			return false;
		}

		function row_overlays() {
			$mpc_first_overlay = $mpc_second_overlay = $mpc_parallax = '';

			if ( $this->atts[ 'enable_first_overlay' ] == 'true' ) {
				$mpc_first_overlay = '<div class="mpc-overlay mpc-overlay--first' . ( $this->atts[ 'first_background_type' ] == 'image' && $this->atts[ 'enable_first_overlay_scrolling' ] ? ' mpc-overlay--scrolling' : '' ) . '" data-speed="' . $this->atts[ 'first_overlay_speed' ] . '"></div>';
			}

			if ( $this->atts[ 'enable_second_overlay' ] == 'true' ) {
				$mpc_second_overlay = '<div class="mpc-overlay mpc-overlay--second' . ( $this->atts[ 'second_background_type' ] == 'image' && $this->atts[ 'enable_second_overlay_scrolling' ] ? ' mpc-overlay--scrolling' : '' ) . '" data-speed="' . $this->atts[ 'second_overlay_speed' ] . '"></div>';
			}

			if ( $this->atts[ 'enable_parallax' ] == 'true' && $this->atts[ 'parallax_background' ] != '' ) {
				$mpc_parallax_image = wp_get_attachment_url( $this->atts[ 'parallax_background' ] );

				if ( $mpc_parallax_image != false ) {
					$mpc_parallax_options = '';

					if ( $this->atts[ 'parallax_style' ] == 'classic' ) {
						$mpc_parallax_options = 'data-bottom-top="transform: translateY(-25%)" data-top-bottom="transform: translateY(0%)"';
					} elseif ( $this->atts[ 'parallax_style' ] == 'classic-fast' ) {
						$mpc_parallax_options = 'data-bottom-top="transform: translateY(-50%)" data-top-bottom="transform: translateY(0%)"';
					} elseif ( $this->atts[ 'parallax_style' ] == 'horizontal-left' ) {
						$mpc_parallax_options = 'data-bottom-top="transform: translateX(0%)" data-top-bottom="transform: translateX(-25%)"';
					} elseif ( $this->atts[ 'parallax_style' ] == 'horizontal-right' ) {
						$mpc_parallax_options = 'data-bottom-top="transform: translateX(-25%)" data-top-bottom="transform: translateX(0%)"';
					} elseif ( $this->atts[ 'parallax_style' ] == 'fade' ) {
						$this->sh_atts[ 'data-15p-center-bottom' ] = 'opacity: 1';
						$this->sh_atts[ 'data-top-bottom' ] = 'opacity: 0';
					}

					$mpc_parallax = '<div class="mpc-parallax-wrap"><div class="mpc-parallax mpc-parallax-style--' . $this->atts[ 'parallax_style' ] . '" ' . $mpc_parallax_options . '></div></div>';
				}
			}
			$this->prepend .= $mpc_second_overlay . $mpc_first_overlay . $mpc_parallax;
		}


		function row_separator() {
			$mpc_css_types = array(
				'tip-center',
				'tip-left',
				'tip-right',
				'split-inner',
				'split-outer',
				'teeth-left',
				'teeth-center',
				'teeth-right',
			);

			$mpc_separator = '';

			if ( $this->atts[ 'enable_top_separator' ] == 'true' ) {
				$mpc_separator .= '<div class="mpc-separator-spacer mpc-separator--top"></div>';
				$mpc_separator_color = $this->atts[ 'top_separator_color' ] != '' ? 'data-color="' . $this->atts[ 'top_separator_color' ] . '"' : '';

				if ( array_search( $this->atts[ 'top_separator_style' ], $mpc_css_types ) !== false ) {
					$mpc_separator .= '<div class="mpc-separator mpc-separator--css mpc-separator--top mpc-separator-style--' . $this->atts[ 'top_separator_style' ] . '" ' . $mpc_separator_color . '><div class="mpc-separator-content"></div></div>';
				} else {
					if ( strpos( $this->atts[ 'top_separator_style' ], 'circle' ) === 0 ) {
						$aspect_ratio = '';
						$viewbox = '0 0 200 100';
					} else {
						$aspect_ratio = 'preserveAspectRatio="none"';
						$viewbox = '0 0 100 100';
					}

					$mpc_separator .= '<svg class="mpc-separator mpc-separator--top mpc-separator-style--' . $this->atts[ 'top_separator_style' ] . '" ' . $mpc_separator_color . ' width="100%" height="100" viewBox="' . $viewbox . '" ' . $aspect_ratio . ' version="1.1" xmlns="http://www.w3.org/2000/svg">';
					$mpc_separator .= MPC_Row::get_shape_top( $this->atts[ 'top_separator_style' ] );
					$mpc_separator .= '</svg>';
				}

				$this->prepend .= $mpc_separator;
			}

			if ( $this->atts[ 'enable_bottom_separator' ] == 'true' ) {
				$mpc_separator = '<div class="mpc-separator-spacer mpc-separator--bottom"></div>';
				$mpc_separator_color = $this->atts[ 'bottom_separator_color' ] != '' ? 'data-color="' . $this->atts[ 'bottom_separator_color' ] . '"' : '';

				if ( array_search( $this->atts[ 'bottom_separator_style' ], $mpc_css_types ) !== false ) {
					$mpc_separator .= '<div class="mpc-separator mpc-separator--css mpc-separator--bottom mpc-separator-style--' . $this->atts[ 'bottom_separator_style' ] . '" ' . $mpc_separator_color . '><div class="mpc-separator-content"></div></div>';
				} else {
					if ( strpos( $this->atts[ 'bottom_separator_style' ], 'circle' ) === 0 ) {
						$aspect_ratio = '';
						$viewbox = '0 0 200 100';
					} else {
						$aspect_ratio = 'preserveAspectRatio="none"';
						$viewbox = '0 0 100 100';
					}

					$mpc_separator .= '<svg class="mpc-separator mpc-separator--bottom mpc-separator-style--' . $this->atts[ 'bottom_separator_style' ] . '" ' . $mpc_separator_color . ' width="100%" height="100" viewBox="' . $viewbox . '" ' . $aspect_ratio . ' version="1.1" xmlns="http://www.w3.org/2000/svg">';
					$mpc_separator .= MPC_Row::get_shape_bottom( $this->atts[ 'bottom_separator_style' ] );
					$mpc_separator .= '</svg>';
				}

				$this->append .= $mpc_separator;
			}
		}

		function toggle_row() {
			if ( $this->atts[ 'toggle_enable' ] ) {
				$mpc_icon       = MPC_Parser::icon( $this->atts, 'toggle' );
				$mpc_hover_icon = MPC_Parser::icon( $this->atts, 'hover_toggle' );

				$mpc_transition_classes = ' mpc-effect-' . $this->atts[ 'toggle_effect' ];
				$mpc_font_classes       = $this->atts[ 'toggle_font_preset' ] != '' ? ' mpc-typography--' . $this->atts[ 'toggle_font_preset' ] : '';
				$mpc_stretch_classes    = $this->atts[ 'toggle_stretch' ] != '' ? ' mpc-stretch' : '';

				$this->atts[ 'toggle_title' ]       = $this->atts[ 'toggle_title' ] != '' ? '<span class="mpc-toggle-row__title">' . $this->atts[ 'toggle_title' ] . '</span>' : '';
				$this->atts[ 'hover_toggle_title' ] = $this->atts[ 'hover_toggle_title' ] != '' ? '<span class="mpc-toggle-row__title">' . $this->atts[ 'hover_toggle_title' ] . '</span>' : '';

				$mpc_force_position = '';
				if ( $this->atts[ 'toggle_icon_position' ] == 'button-left' ) {
					$mpc_force_position .= ' mpc-position--left';
				} elseif ( $this->atts[ 'toggle_icon_position' ] == 'button-right' ) {
					$mpc_force_position .= ' mpc-position--right';
				}

				$mpc_hover_force_position = '';
				if ( $this->atts[ 'hover_toggle_icon_position' ] == 'button-left' ) {
					$mpc_hover_force_position .= ' mpc-position--left';
				} elseif ( $this->atts[ 'hover_toggle_icon_position' ] == 'button-right' ) {
					$mpc_hover_force_position .= ' mpc-position--right';
				}

				$mpc_icon       = '<span class="mpc-toggle-row__icon-wrap"><i class="mpc-toggle-row__icon mpc-transition' . $mpc_icon[ 'class' ] . '">' . $mpc_icon[ 'content' ] . '</i></span>';
				$mpc_hover_icon = '<span class="mpc-toggle-row__icon-wrap"><i class="mpc-toggle-row__icon mpc-transition' . $mpc_hover_icon[ 'class' ] . '">' . $mpc_hover_icon[ 'content' ] . '</i></span>';

				$mpc_regular = '';
				if ( $this->atts[ 'toggle_icon_position' ] == 'title-left' || $this->atts[ 'toggle_icon_position' ] == 'button-left' ) {
					$mpc_regular .= $mpc_icon . $this->atts[ 'toggle_title' ];
				} else {
					$mpc_regular .= $this->atts[ 'toggle_title' ] . $mpc_icon;
				}

				$mpc_hover = '';
				if ( $this->atts[ 'hover_toggle_icon_position' ] == 'title-left' || $this->atts[ 'hover_toggle_icon_position' ] == 'button-left' ) {
					$mpc_hover .= $mpc_hover_icon . $this->atts[ 'hover_toggle_title' ];
				} else {
					$mpc_hover .= $this->atts[ 'hover_toggle_title' ] . $mpc_hover_icon;
				}

				$mpc_toggle_row = '<div id="' . $this->styles[ 'id' ] . '" class="mpc-toggle-row' . $this->classes . $mpc_stretch_classes . $mpc_font_classes . $mpc_transition_classes . '">';
					$mpc_toggle_row .= '<div class="mpc-toggle-row__content">';
						$mpc_toggle_row .= '<div class="mpc-toggle-row__heading mpc-regular' . $mpc_force_position . '">' . $mpc_regular . '</div>';
						$mpc_toggle_row .= '<div class="mpc-toggle-row__heading mpc-hover' . $mpc_hover_force_position . '">' . $mpc_hover . '</div>';
					$mpc_toggle_row .= '</div>';
				$mpc_toggle_row .= '</div>';

				$this->before .= $mpc_toggle_row;
			}
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_row-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_row/css/mpc_row.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_row-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_row/js/mpc_row' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		function getDefaults() {
			$this->defaults = array(
				'content_preset' => '',

				// Toggle
				'toggle_enable'                           => '',
				'toggle_state'                            => 'closed',
				'toggle_stretch'                          => '',
				'toggle_effect'                           => 'none',

				'toggle_font_preset'                      => '',
				'toggle_font_color'                       => '',
				'toggle_font_size'                        => '',
				'toggle_font_line_height'                 => '',
				'toggle_font_align'                       => '',
				'toggle_font_transform'                   => '',
				'toggle_title'                            => '',

				'toggle_background_type'                  => 'color',
				'toggle_background_color'                 => '',
				'toggle_background_image'                 => '',
				'toggle_background_image_size'            => 'large',
				'toggle_background_repeat'                => 'no-repeat',
				'toggle_background_size'                  => 'initial',
				'toggle_background_position'              => 'middle-center',
				'toggle_background_gradient'              => '#83bae3||#80e0d4||0;100||180||linear',

				'toggle_border_css'                       => '',
				'toggle_padding_css'                      => '',
				'toggle_margin_css'                       => '',

				'toggle_icon_position'                    => 'title-left',

				'toggle_icon_type'                        => 'icon',
				'toggle_icon'                             => '',
				'toggle_icon_character'                   => '',
				'toggle_icon_image'                       => '',
				'toggle_icon_image_size'                  => 'thumbnail',
				'toggle_icon_preset'                      => '',
				'toggle_icon_color'                       => '#333333',
				'toggle_icon_size'                        => '',

				'toggle_icon_background_type'             => 'color',
				'toggle_icon_background_color'            => '',
				'toggle_icon_background_image'            => '',
				'toggle_icon_background_image_size'       => 'large',
				'toggle_icon_background_repeat'           => 'no-repeat',
				'toggle_icon_background_size'             => 'initial',
				'toggle_icon_background_position'         => 'middle-center',
				'toggle_icon_background_gradient'         => '#83bae3||#80e0d4||0;100||180||linear',

				'toggle_icon_border_css'                  => '',
				'toggle_icon_padding_css'                 => '',
				'toggle_icon_margin_css'                  => '',

				// Toggle Hover
				'hover_toggle_font_preset'                => '',
				'hover_toggle_font_color'                 => '',
				'hover_toggle_font_size'                  => '',
				'hover_toggle_font_line_height'           => '',
				'hover_toggle_font_align'                 => '',
				'hover_toggle_font_transform'             => '',
				'hover_toggle_title'                      => '',

				'hover_toggle_background_type'            => 'color',
				'hover_toggle_background_color'           => '',
				'hover_toggle_background_image'           => '',
				'hover_toggle_background_image_size'      => 'large',
				'hover_toggle_background_repeat'          => 'no-repeat',
				'hover_toggle_background_size'            => 'initial',
				'hover_toggle_background_position'        => 'middle-center',
				'hover_toggle_background_gradient'        => '#83bae3||#80e0d4||0;100||180||linear',

				'hover_toggle_border_css'                 => '',

				'hover_toggle_icon_position'              => 'title-left',

				'hover_toggle_icon_type'                  => 'icon',
				'hover_toggle_icon'                       => '',
				'hover_toggle_icon_character'             => '',
				'hover_toggle_icon_image'                 => '',
				'hover_toggle_icon_image_size'            => 'thumbnail',
				'hover_toggle_icon_preset'                => '',
				'hover_toggle_icon_color'                 => '#333333',
				'hover_toggle_icon_size'                  => '',

				'hover_toggle_icon_background_type'       => 'color',
				'hover_toggle_icon_background_color'      => '',
				'hover_toggle_icon_background_image'      => '',
				'hover_toggle_icon_background_image_size' => 'large',
				'hover_toggle_icon_background_repeat'     => 'no-repeat',
				'hover_toggle_icon_background_size'       => 'initial',
				'hover_toggle_icon_background_position'   => 'middle-center',
				'hover_toggle_icon_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				'hover_toggle_icon_border_css'            => '',

				// Separator
				'enable_top_separator'    => '',
				'top_separator_style'     => 'arrow-center',
				'top_separator_color'     => '',

				'enable_bottom_separator' => '',
				'bottom_separator_style'  => 'arrow-center',
				'bottom_separator_color'  => '',

				// Parallax
				'enable_parallax'         => '',
				'enable_parallax_pattern' => '',
				'parallax_style'          => 'classic',
				'parallax_background'     => '',

				// Overlay
				'enable_first_overlay'            => '',
				'first_overlay_opacity'           => '',

				'first_background_type'           => 'color',
				'first_background_color'          => '',
				'first_background_image'          => '',
				'first_background_image_size'     => 'large',
				'first_background_repeat'         => 'no-repeat',
				'first_background_size'           => 'initial',
				'first_background_position'       => 'middle-center',
				'first_background_gradient'       => '#83bae3||#80e0d4||0;100||180||linear',

				'enable_first_overlay_scrolling'  => '',
				'first_overlay_speed'             => '25',

				'enable_second_overlay'           => '',
				'second_overlay_opacity'          => '',

				'second_background_type'          => 'color',
				'second_background_color'         => '',
				'second_background_image'         => '',
				'second_background_image_size'    => 'large',
				'second_background_repeat'        => 'no-repeat',
				'second_background_size'          => 'initial',
				'second_background_position'      => 'middle-center',
				'second_background_gradient'      => '#83bae3||#80e0d4||0;100||180||linear',

				'enable_second_overlay_scrolling' => '',
				'second_overlay_speed'            => '25',

				// Date
				'enable_date'             => '',
				'from_date'               => '',
				'to_date'                 => '',

				// Animation
				'animation_in_type'       => 'none',
				'animation_in_duration'   => '300',
				'animation_in_delay'      => '0',
				'animation_in_offset'     => '100',

				'animation_loop_type'     => 'none',
				'animation_loop_duration' => '1000',
				'animation_loop_delay'    => '1000',
				'animation_loop_hover'    => '',

				// Link Block
				'url'                     => '',

				// Divider Block
				'divider_enable'          => '',

				// VC Row
				'css'                     => '',
			);
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles ) {
			global $mpc_massive_styles;
			$css_id = uniqid( 'mpc_row-' . rand( 1, 100 ) );
			$style = '';

			$border_radius = '';
			if ( $styles[ 'css' ] != '' ) {
				preg_match( '/border-radius:[^;]+;/', $styles[ 'css' ], $matches );

				if ( ! empty( $matches ) ) {
					$border_radius = $matches[ 0 ];
				}
			}

			// Add 'px'
			$styles[ 'toggle_font_size' ]       = $styles[ 'toggle_font_size' ] != '' ? $styles[ 'toggle_font_size' ] . ( is_numeric( $styles[ 'toggle_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'hover_toggle_font_size' ] = $styles[ 'hover_toggle_font_size' ] != '' ? $styles[ 'hover_toggle_font_size' ] . ( is_numeric( $styles[ 'hover_toggle_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'toggle_icon_size' ]       = $styles[ 'toggle_icon_size' ] != '' ? $styles[ 'toggle_icon_size' ] . ( is_numeric( $styles[ 'toggle_icon_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'hover_toggle_icon_size' ] = $styles[ 'hover_toggle_icon_size' ] != '' ? $styles[ 'hover_toggle_icon_size' ] . ( is_numeric( $styles[ 'hover_toggle_icon_size' ] ) ? 'px' : '' ) : '';

			if ( $styles[ 'toggle_enable' ] ) {
				// Regular
				$inner_styles = array();
				if ( $styles[ 'toggle_border_css' ] ) { $inner_styles[] = $styles[ 'toggle_border_css' ]; }
				if ( $styles[ 'toggle_padding_css' ] ) { $inner_styles[] = $styles[ 'toggle_padding_css' ]; }
				if ( $styles[ 'toggle_margin_css' ] ) { $inner_styles[] = $styles[ 'toggle_margin_css' ]; }
				if ( $temp_style = MPC_CSS::background( $styles, 'toggle' ) ) { $inner_styles[] = $temp_style; }

				if ( count( $inner_styles ) > 0 ) {
					$style .= '.mpc-toggle-row[id="' . $css_id . '"] {';
						$style .= join( '', $inner_styles );
					$style .= '}';
				}

				// Hover
				$inner_styles = array();
				if ( $styles[ 'hover_toggle_border_css' ] ) { $inner_styles[] = $styles[ 'hover_toggle_border_css' ]; }
				if ( $temp_style = MPC_CSS::background( $styles, 'hover_toggle' ) ) { $inner_styles[] = $temp_style; }

				if ( count( $inner_styles ) > 0 ) {
					$style .= '.mpc-toggle-row[id="' . $css_id . '"]:hover,';
					$style .= '.mpc-toggle-row[id="' . $css_id . '"].mpc-toggled {';
						$style .= join( '', $inner_styles );
					$style .= '}';
				}

				// Heading
				if ( $temp_style = MPC_CSS::font( $styles, 'toggle' ) ) {
					$style .= '.mpc-toggle-row[id="' . $css_id . '"] .mpc-toggle-row__heading {';
						$style .= $temp_style;
					$style .= '}';
				}

				// Hover Heading
				if ( $temp_style = MPC_CSS::font( $styles, 'hover_toggle' ) ) {
					$style .= '.mpc-toggle-row[id="' . $css_id . '"]:hover .mpc-toggle-row__heading,';
					$style .= '.mpc-toggle-row[id="' . $css_id . '"].mpc-toggled .mpc-toggle-row__heading {';
						$style .= $temp_style;
					$style .= '}';
				}

				// Icon
				$inner_styles = array();
				if ( $styles[ 'toggle_icon_border_css' ] ) { $inner_styles[] = $styles[ 'toggle_icon_border_css' ]; }
				if ( $styles[ 'toggle_icon_padding_css' ] ) { $inner_styles[] = $styles[ 'toggle_icon_padding_css' ]; }
				if ( $styles[ 'toggle_icon_margin_css' ] ) { $inner_styles[] = $styles[ 'toggle_icon_margin_css' ]; }
				if ( $temp_style = MPC_CSS::background( $styles, 'toggle_icon' ) ) { $inner_styles[] = $temp_style; }
				if ( $temp_style = MPC_CSS::icon( $styles, 'toggle' ) ) { $inner_styles[] = $temp_style; }

				if ( count( $inner_styles ) > 0 ) {
					$style .= '.mpc-toggle-row[id="' . $css_id . '"] .mpc-toggle-row__icon {';
						$style .= join( '', $inner_styles );
					$style .= '}';
				}

				// Hover Icon
				$inner_styles = array();
				if ( $styles[ 'hover_toggle_icon_border_css' ] ) { $inner_styles[] = $styles[ 'hover_toggle_icon_border_css' ]; }
				if ( $temp_style = MPC_CSS::background( $styles, 'hover_toggle_icon' ) ) { $inner_styles[] = $temp_style; }
				if ( $temp_style = MPC_CSS::icon( $styles, 'hover_toggle' ) ) { $inner_styles[] = $temp_style; }

				if ( count( $inner_styles ) > 0 ) {
					$style .= '.mpc-toggle-row[id="' . $css_id . '"]:hover .mpc-toggle-row__icon,';
					$style .= '.mpc-toggle-row[id="' . $css_id . '"].mpc-toggled .mpc-toggle-row__icon {';
						$style .= join( '', $inner_styles );
					$style .= '}';
				}
			}

			// Overlay
			$inner_styles = array();
			if ( $styles[ 'first_overlay_opacity' ] ) { $inner_styles[] = 'opacity:' . ( $styles[ 'first_overlay_opacity' ] * .01 ) . ';'; }
			if ( $border_radius ) { $inner_styles[] = $border_radius; }
			if ( $temp_style = MPC_CSS::background( $styles, 'first' ) ) { $inner_styles[] = $temp_style; }

			if ( $styles[ 'enable_first_overlay' ] == 'true' && count( $inner_styles ) > 0 ) {
				$style .= '.mpc-row[data-row-id="' . $css_id . '"] > .mpc-overlay--first {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'second_overlay_opacity' ] ) { $inner_styles[] = 'opacity:' . ( $styles[ 'second_overlay_opacity' ] * .01 ) . ';'; }
			if ( $border_radius ) { $inner_styles[] = $border_radius; }
			if ( $temp_style = MPC_CSS::background( $styles, 'second' ) ) { $inner_styles[] = $temp_style; }

			if ( $styles[ 'enable_second_overlay' ] == 'true' && count( $inner_styles ) > 0 ) {
				$style .= '.mpc-row[data-row-id="' . $css_id . '"] > .mpc-overlay--second {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Parallax
			if ( $styles[ 'enable_parallax' ] == 'true' && $styles[ 'parallax_background' ] != '' ) {
				$image = wp_get_attachment_url( $styles[ 'parallax_background' ] );

				$style .= '.mpc-row[data-row-id="' . $css_id . '"] > .mpc-parallax-wrap .mpc-parallax:before {'; //inner row issue
					$style .= $image != false ? 'background-image:url("' . $image . '");' : '';
					$style .= $styles[ 'enable_parallax_pattern' ] == 'true' ? 'background-repeat:repeat;' : 'background-size:cover;';
					$style .= $border_radius;
				$style .= '}';
			}

			$mpc_massive_styles .= $style;

			return array(
				'id'  => $css_id,
				'css' => $style,
			);
		}

		/* Map all shortcode options to Visual Composer popup */
		function shortcode_map() {
			if ( ! function_exists( 'vc_add_params' ) ) {
				return;
			}

			// TOGGLE ROW SECTION
			$toggle_dependency = array( 'element' => 'toggle_enable', 'value' => 'true' );

			$toggle = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Toggle Row', 'mpc' ),
					'subtitle'   => __( 'Specify settings for toggle row.', 'mpc' ),
					'param_name' => 'toggle_divider',
					'group'      => __( 'Toggle Row', 'mpc' ),
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Make row toggable', 'mpc' ),
					'param_name'       => 'toggle_enable',
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'description'      => __( 'Switch to enable toggle row.', 'mpc' ),
					'group'            => __( 'Toggle Row', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Default state', 'mpc' ),
					'param_name'       => 'toggle_state',
					'value'            => array(
						__( 'Opened' , 'mpc' ) => 'opened',
						__( 'Closed', 'mpc' )  => 'closed',
					),
					'std'              => 'closed',
					'description'      => __( 'Select the default state for toggle row.', 'mpc' ),
					'group'            => __( 'Toggle Row', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency'       => $toggle_dependency,
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Stretch with Row', 'mpc' ),
					'param_name'       => 'toggle_stretch',
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'description'      => __( 'Switch to stretch toggle with row.', 'mpc' ),
					'group'            => __( 'Toggle Row', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency'       => $toggle_dependency,
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Hover Effect', 'mpc' ),
					'param_name'       => 'toggle_effect',
					'value'            => array(
						__( 'None', 'mpc' )        => 'none',
						__( 'Slide Up', 'mpc' )    => 'slide-up',
						__( 'Slide Right', 'mpc' ) => 'slide-right',
						__( 'Slide Down', 'mpc' )  => 'slide-down',
						__( 'Slide Left', 'mpc' )  => 'slide-left',
						__( 'Fade', 'mpc' )        => 'fade',
					),
					'std'              => 'none',
					'description'      => __( 'Specify hover effect for toggle row button.', 'mpc' ),
					'group'            => __( 'Toggle Row', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency'       => $toggle_dependency,
				),
			);

			$toggle_title = array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Title', 'mpc' ),
					'param_name'  => 'toggle_title',
					'value'       => '',
					'description' => __( 'Specify toggle row title.', 'mpc' ),
					'group'       => __( 'Toggle Row', 'mpc' ),
					'dependency'  => $toggle_dependency,
				),
			);

			$toggle_atts = array( 'prefix' => 'toggle', 'subtitle' => __( 'Toggle Button', 'mpc' ), 'dependency' => $toggle_dependency, 'group' => __( 'Toggle Row', 'mpc' ) );

			$toggle_font       = MPC_Snippets::vc_font( $toggle_atts );
			$toggle_background = MPC_Snippets::vc_background( $toggle_atts );
			$toggle_border     = MPC_Snippets::vc_border( $toggle_atts );
			$toggle_padding    = MPC_Snippets::vc_padding( $toggle_atts );
			$toggle_margin     = MPC_Snippets::vc_margin( $toggle_atts );

			$toggle_icon_position = array(
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Icon Position', 'mpc' ),
					'param_name'       => 'toggle_icon_position',
					'value'            => array(
						__( 'Title Left Side', 'mpc' )   => 'title-left',
						__( 'Title Right Side', 'mpc' )  => 'title-right',
						__( 'Button Left Side', 'mpc' )  => 'button-left',
						__( 'Button Right Side', 'mpc' ) => 'button-right',
					),
					'std'              => 'title-left',
					'description'      => __( 'Specify toggle row icon position.', 'mpc' ),
					'group'            => __( 'Toggle Row', 'mpc' ),
					'dependency'       => $toggle_dependency,
				),
			);

			$toggle_icon_atts = array( 'prefix' => 'toggle_icon', 'subtitle' => __( 'Toggle Icon', 'mpc' ), 'dependency' => $toggle_dependency, 'group' => __( 'Toggle Row', 'mpc' ) );

			$toggle_icon            = MPC_Snippets::vc_icon( array( 'prefix' => 'toggle', 'subtitle' => __( 'Toggle Icon', 'mpc' ), 'dependency' => $toggle_dependency, 'group' => __( 'Toggle Row', 'mpc' ) ) );
			$toggle_icon_background = MPC_Snippets::vc_background( $toggle_icon_atts );
			$toggle_icon_border     = MPC_Snippets::vc_border( $toggle_icon_atts );
			$toggle_icon_padding    = MPC_Snippets::vc_padding( $toggle_icon_atts );
			$toggle_icon_margin     = MPC_Snippets::vc_margin( $toggle_icon_atts );

			$hover_toggle_title = array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Hover Title', 'mpc' ),
					'param_name'  => 'hover_toggle_title',
					'value'       => '',
					'description' => __( 'Specify hover toggle row title.', 'mpc' ),
					'group'       => __( 'Toggle Row', 'mpc' ),
					'dependency'  => $toggle_dependency,
				),
			);

			$hover_toggle_atts = array( 'prefix' => 'hover_toggle', 'subtitle' => __( 'Hover Toggle Button', 'mpc' ), 'dependency' => $toggle_dependency, 'group' => __( 'Toggle Row', 'mpc' ) );

			$hover_toggle_font       = MPC_Snippets::vc_font_simple( $hover_toggle_atts );
			$hover_toggle_background = MPC_Snippets::vc_background( $hover_toggle_atts );
			$hover_toggle_border     = MPC_Snippets::vc_border( $hover_toggle_atts );

			$hover_toggle_icon_position = array(
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Icon Position', 'mpc' ),
					'param_name'       => 'hover_toggle_icon_position',
					'value'            => array(
						__( 'Title Left Side', 'mpc' )   => 'title-left',
						__( 'Title Right Side', 'mpc' )  => 'title-right',
						__( 'Button Left Side', 'mpc' )  => 'button-left',
						__( 'Button Right Side', 'mpc' ) => 'button-right',
					),
					'std'              => 'title-left',
					'description'      => __( 'Specify toggle row icon position.', 'mpc' ),
					'group'            => __( 'Toggle Row', 'mpc' ),
					'dependency'       => $toggle_dependency,
				),
			);

			$hover_toggle_icon_atts = array( 'prefix' => 'hover_toggle_icon', 'subtitle' => __( 'Hover Toggle Icon', 'mpc' ), 'dependency' => $toggle_dependency, 'group' => __( 'Toggle Row', 'mpc' ) );

			$hover_toggle_icon            = MPC_Snippets::vc_icon( array( 'prefix' => 'hover_toggle', 'subtitle' => __( 'Hover Toggle Icon', 'mpc' ), 'dependency' => $toggle_dependency, 'group' => __( 'Toggle Row', 'mpc' ) ) );
			$hover_toggle_icon_background = MPC_Snippets::vc_background( $hover_toggle_icon_atts );
			$hover_toggle_icon_border     = MPC_Snippets::vc_border( $hover_toggle_icon_atts );

			$toggle_params = array_merge(
				$toggle,

			    $toggle_font,
				$toggle_title,
				$toggle_background,
				$toggle_border,
				$toggle_padding,
				$toggle_margin,

				$toggle_icon,
				$toggle_icon_position,
				$toggle_icon_background,
				$toggle_icon_border,
				$toggle_icon_padding,
				$toggle_icon_margin,

				$hover_toggle_font,
				$hover_toggle_title,
				$hover_toggle_background,
				$hover_toggle_border,

				$hover_toggle_icon,
				$hover_toggle_icon_position,
				$hover_toggle_icon_background,
				$hover_toggle_icon_border
			);

			// LINK BLOCK SECTION
			$block_params = array(
				array(
					'type'             => 'vc_link',
					'heading'          => __( 'Link', 'mpc' ),
					'param_name'       => 'url',
					'value'            => '',
					'description'      => __( 'Specify URL.', 'mpc' ),
					'weight'           => -3005,
					'group'            => __( 'Extras', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Divider Block', 'mpc' ),
					'param_name'       => 'divider_enable',
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'description'      => __( 'Made a divider from this row.', 'mpc' ),
					'weight'           => -3007,
					'group'            => __( 'Extras', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
			);

			$date_params = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Enable Time Based Display', 'mpc' ),
					'param_name'       => 'enable_date',
					'description'      => __( 'Display this row only in the specified time period.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'weight'           => -3010,
					'group'            => __( 'Extras', 'mpc' ),
				),
				array(
					'type'             => 'mpc_datetime',
					'heading'          => __( 'From Date', 'mpc' ),
					'param_name'       => 'from_date',
					'admin_label'      => true,
					'description'      => __( 'Choose starting date for displaying this row.', 'mpc' ),
					'value'            => '',
					'std'              => '',
					'dependency'       => array( 'element' => 'enable_date', 'value'   => 'true', ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'weight'           => -3015,
					'group'            => __( 'Extras', 'mpc' ),
				),
				array(
					'type'             => 'mpc_datetime',
					'heading'          => __( 'To Date', 'mpc' ),
					'param_name'       => 'to_date',
					'admin_label'      => true,
					'description'      => __( 'Choose ending date for displaying this row.', 'mpc' ),
					'value'            => '',
					'std'              => '',
					'dependency'       => array( 'element' => 'enable_date', 'value'   => 'true', ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'weight'           => -3020,
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
					'weight'      => -3000,
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
					'weight'      => -3001,
					'description' => __( 'Choose preset or create new one.', 'mpc' ),
					'group'       => __( 'Extras', 'mpc' ),
				),
			);

			$separator_types = array(
				__( 'Tip Left', 'mpc' )      => 'tip-left',
				__( 'Tip Center', 'mpc' )    => 'tip-center',
				__( 'Tip Right', 'mpc' )     => 'tip-right',
				__( 'Circle Left', 'mpc' )   => 'circle-left',
				__( 'Circle Center', 'mpc' ) => 'circle-center',
				__( 'Circle Right', 'mpc' )  => 'circle-right',
				__( 'Split Inner', 'mpc' )   => 'split-inner',
				__( 'Split Outer', 'mpc' )   => 'split-outer',
				__( 'Teeth Left', 'mpc' )    => 'teeth-left',
				__( 'Teeth Center', 'mpc' )  => 'teeth-center',
				__( 'Teeth Right', 'mpc' )   => 'teeth-right',
				__( 'Arrow Left', 'mpc' )    => 'arrow-left',
				__( 'Arrow Center', 'mpc' )  => 'arrow-center',
				__( 'Arrow Right', 'mpc' )   => 'arrow-right',
				__( 'Blob Left', 'mpc' )     => 'blob-left',
				__( 'Blob Center', 'mpc' )   => 'blob-center',
				__( 'Blob Right', 'mpc' )    => 'blob-right',
				__( 'Slope Left', 'mpc' )    => 'slope-left',
				__( 'Slope Right', 'mpc' )   => 'slope-right',
				__( 'Stamp', 'mpc' )         => 'stamp',
				__( 'Cloud', 'mpc' )         => 'cloud',
			);

			// SEPARATOR SECTION
			$top_separator = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Top Separator', 'mpc' ),
					'subtitle'   => __( 'Setup top row separator.', 'mpc' ),
					'param_name' => 'top_separator_divider',
					'group'      => __( 'Separator', 'mpc' ),
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Enable Top Separator', 'mpc' ),
					'param_name'  => 'enable_top_separator',
					'value'       => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'         => '',
					'description' => __( 'Switch to enable top separator display.', 'mpc' ),
					'group'       => __( 'Separator', 'mpc' ),
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Style', 'mpc' ),
					'param_name'       => 'top_separator_style',
					'value'            => $separator_types,
					'std'              => 'arrow-center',
					'dependency'       => array( 'element' => 'enable_top_separator', 'value' => 'true' ),
					'group'            => __( 'Separator', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Background', 'mpc' ),
					'param_name'       => 'top_separator_color',
					'value'            => '',
					'dependency'       => array( 'element' => 'enable_top_separator', 'value' => 'true' ),
					'group'            => __( 'Separator', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
			);

			$bottom_separator = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Bottom Separator', 'mpc' ),
					'subtitle'   => __( 'Setup bottom row separator.', 'mpc' ),
					'param_name' => 'bottom_separator_divider',
					'group'      => __( 'Separator', 'mpc' ),
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Enable Bottom Separator', 'mpc' ),
					'param_name'  => 'enable_bottom_separator',
					'value'       => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'         => '',
					'description' => __( 'Switch to enable bottom separator display.', 'mpc' ),
					'group'       => __( 'Separator', 'mpc' ),
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Style', 'mpc' ),
					'param_name'       => 'bottom_separator_style',
					'value'            => $separator_types,
					'std'              => 'arrow-center',
					'dependency'       => array( 'element' => 'enable_bottom_separator', 'value' => 'true' ),
					'group'            => __( 'Separator', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Background', 'mpc' ),
					'param_name'       => 'bottom_separator_color',
					'value'            => '',
					'dependency'       => array( 'element' => 'enable_bottom_separator', 'value' => 'true' ),
					'group'            => __( 'Separator', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
			);

			// OVERLAY SECTION
			$first_overlay = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'First Overlay', 'mpc' ),
					'subtitle'   => __( 'Setup first overlay.', 'mpc' ),
					'param_name' => 'first_overlay_divider',
					'group'      => __( 'Background', 'mpc' ),
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Enable First Overlay', 'mpc' ),
					'param_name'  => 'enable_first_overlay',
					'value'       => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'         => '',
					'description' => __( 'Switch to enable first overlay display.', 'mpc' ),
					'group'       => __( 'Background', 'mpc' ),
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Opacity', 'mpc' ),
					'param_name'       => 'first_overlay_opacity',
					'description'      => __( 'Define overlay opacity.', 'mpc' ),
					'min'              => 0,
					'max'              => 100,
					'step'             => 1,
					'value'            => 25,
					'unit'             => '%',
					'group'            => __( 'Background', 'mpc' ),
					'dependency'       => array( 'element' => 'enable_first_overlay', 'value' => 'true' ),
				),
			);

			$first_overlay_background = MPC_Snippets::vc_background( array( 'prefix' => 'first', 'subtitle' => __( 'First Overlay', 'mpc' ), 'dependency' => array( 'element' => 'enable_first_overlay', 'value' => 'true' ), 'group' => __( 'Background', 'mpc' ) ) );

			$first_overlay_background_scroll = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Enable Scrolling', 'mpc' ),
					'param_name'       => 'enable_first_overlay_scrolling',
					'description'      => __( 'Switch to enable first overlay background scrolling.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Background', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency'       => array( 'element' => 'first_background_type', 'value' => 'image' ),
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Scrolling Speed', 'mpc' ),
					'param_name'       => 'first_overlay_speed',
					'description'      => __( 'Define first overlay background scrolling speed over 1 second.', 'mpc' ),
					'min'              => 1,
					'max'              => 500,
					'step'             => 1,
					'value'            => 25,
					'unit'             => 'px',
					'group'            => __( 'Background', 'mpc' ),
					'dependency'       => array( 'element' => 'enable_first_overlay_scrolling', 'value' => 'true' ),
				),
			);

			$second_overlay = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Second Overlay', 'mpc' ),
					'subtitle'   => __( 'Setup second overlay.', 'mpc' ),
					'param_name' => 'second_overlay_divider',
					'group'      => __( 'Background', 'mpc' ),
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Enable Second Overlay', 'mpc' ),
					'param_name'  => 'enable_second_overlay',
					'value'       => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'         => '',
					'description' => __( 'Switch to enable second overlay display.', 'mpc' ),
					'group'       => __( 'Background', 'mpc' ),
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Opacity', 'mpc' ),
					'param_name'       => 'second_overlay_opacity',
					'description'      => __( 'Define overlay opacity.', 'mpc' ),
					'min'              => 0,
					'max'              => 100,
					'step'             => 1,
					'value'            => 25,
					'unit'             => '%',
					'group'            => __( 'Background', 'mpc' ),
					'dependency'       => array( 'element' => 'enable_second_overlay', 'value' => 'true' ),
				),
			);

			$second_overlay_background = MPC_Snippets::vc_background( array( 'prefix' => 'second', 'subtitle' => __( 'Second Overlay', 'mpc' ), 'dependency' => array( 'element' => 'enable_second_overlay', 'value' => 'true' ), 'group' => __( 'Background', 'mpc' ) ) );

			$second_overlay_background_scroll = array(
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Enable Scrolling', 'mpc' ),
					'param_name'  => 'enable_second_overlay_scrolling',
					'value'       => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'         => '',
					'description' => __( 'Switch to enable second overlay background scrolling.', 'mpc' ),
					'group'       => __( 'Background', 'mpc' ),
					'dependency'  => array( 'element' => 'second_background_type', 'value' => 'image' ),
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Scrolling Speed', 'mpc' ),
					'param_name'       => 'second_overlay_speed',
					'description'      => __( 'Define second overlay background scrolling speed over 1 second.', 'mpc' ),
					'min'              => 1,
					'max'              => 500,
					'step'             => 1,
					'value'            => 25,
					'unit'             => 'px',
					'group'            => __( 'Background', 'mpc' ),
					'dependency'       => array( 'element' => 'enable_second_overlay_scrolling', 'value' => 'true' ),
				),
			);

			// PARALLAX SECTION
			$parallax = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Parallax', 'mpc' ),
					'subtitle'   => __( 'Setup parallax.', 'mpc' ),
					'param_name' => 'parallax_divider',
					'group'      => __( 'Background', 'mpc' ),
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Enable Parallax', 'mpc' ),
					'param_name'       => 'enable_parallax',
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'description'      => __( 'Switch to enable parallax display.', 'mpc' ),
					'group'            => __( 'Background', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Enable Pattern', 'mpc' ),
					'param_name'       => 'enable_parallax_pattern',
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'description'      => __( 'Switch to enable parallax pattern display.', 'mpc' ),
					'group'            => __( 'Background', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency'       => array( 'element' => 'enable_parallax', 'value' => 'true' ),
				),
				array(
					'type'             => 'attach_image',
					'heading'          => __( 'Image', 'mpc' ),
					'param_name'       => 'parallax_background',
					'tooltip'          => __( 'Define background image.', 'mpc' ),
					'value'            => '',
					'description'      => __( 'Select background image.', 'mpc' ),
					'group'            => __( 'Background', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency'       => array( 'element' => 'enable_parallax', 'value' => 'true' ),
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Style', 'mpc' ),
					'param_name'       => 'parallax_style',
					'value'            => array(
						__( 'Classic', 'mpc' )               => 'classic',
						__( 'Classic - Fast', 'mpc' )        => 'classic-fast',
						__( 'Horizontal - to Left', 'mpc' )  => 'horizontal-left',
						__( 'Horizontal - to Right', 'mpc' ) => 'horizontal-right',
						__( 'Fade', 'mpc' )                  => 'fade',
						__( 'Fixed', 'mpc' )                 => 'fixed',
					),
					'description'      => __( '"Fixed" style will be disabled for mobile even with "Parallax on Mobile" ON in Massive Panel. iOS devices doesn\'t support it.', 'mpc' ),
					'std'              => 'classic',
					'dependency'       => array( 'element' => 'enable_parallax', 'value' => 'true' ),
					'group'            => __( 'Background', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
			);

			$animation = MPC_Snippets::vc_animation();

			$params = array_merge( $presets, $toggle_params, $block_params, $date_params, $first_overlay, $first_overlay_background, $first_overlay_background_scroll, $second_overlay, $second_overlay_background, $second_overlay_background_scroll, $parallax, $top_separator, $bottom_separator, $animation );
			MPC_Snippets::params_weight( $params );

			vc_remove_param( 'vc_row', 'parallax' );
			vc_remove_param( 'vc_row', 'parallax_image' );
			vc_remove_param( 'vc_row', 'enable_parallax' ); // The7

			$atts = vc_get_shortcode( 'vc_row' );
			$atts[ 'params' ] = array_merge( $atts[ 'params' ] , $params );
			unset( $atts[ 'base' ] );
			vc_map_update( 'vc_row', $atts );

			$atts = vc_get_shortcode( 'vc_row_inner' );
			$atts[ 'params' ] = array_merge( $atts[ 'params' ] , $params );
			unset( $atts[ 'base' ] );
			vc_map_update( 'vc_row_inner', $atts );
		}

		static function get_shape_top( $type ) {
			switch ( $type ) {
				case 'circle-center':
					return '<path d="M 0 100 A 100 100 0 0 1 200 100 L 4000 100 L 4000 -5 L -4000 -5 L -4000 100 Z" />';
				case 'circle-left':
					return '<path d="M 0 100 A 100 100 0 0 1 200 100 L 4000 100 L 4000 -5 L -4000 -5 L -4000 100 Z" transform="translate(-500, 0)" />';
				case 'circle-right':
					return '<path d="M 0 100 A 100 100 0 0 1 200 100 L 4000 100 L 4000 -5 L -4000 -5 L -4000 100 Z" transform="translate(500, 0)" />';
				case 'arrow-center':
					return '<polygon points="0,100 0,-5 100,-5 100,100 50,5"/>';
				case 'arrow-left':
					return '<polygon points="0,100 0,-5 100,-5 100,100 25,5"/>';
				case 'arrow-right':
					return '<polygon points="0,100 0,-5 100,-5 100,100 75,5"/>';
				case 'slope-left':
					return '<polygon points="0,100 0,-5 100,-5 100,5"/>';
				case 'slope-right':
					return '<polygon points="0,5 0,-5 100,-5 100,100"/>';
				case 'blob-center':
					return '<path d="M 0 100 L 0 -5 L 100 -5 L 100 100 Q 50 -75 0 100 Z" />';
				case 'blob-left':
					return '<path d="M 0 100 L 0 -5 L 100 -5 L 100 100 Q 10 -75 0 100 Z" />';
				case 'blob-right':
					return '<path d="M 0 100 L 0 -5 L 100 -5 L 100 100 Q 90 -75 0 100 Z" />';
				case 'stamp':
					return '<path d="M 0 100 Q 2.5 50 5 100 Q 7.5 50 10 100 Q 12.5 50 15 100 Q 17.5 50 20 100 Q 22.5 50 25 100 Q 27.5 50 30 100 Q 32.5 50 35 100 Q 37.5 50 40 100 Q 42.5 50 45 100 Q 47.5 50 50 100 Q 52.5 50 55 100 Q 57.5 50 60 100 Q 62.5 50 65 100 Q 67.5 50 70 100 Q 72.5 50 75 100 Q 77.5 50 80 100 Q 82.5 50 85 100 Q 87.5 50 90 100 Q 92.5 50 95 100 Q 97.5 50 100 100 L 100 -5 L 0 -5 Z" />';
				case 'cloud':
					return '<path d="M 62.08,83.56 c 1.52,-8.90 3.05,-10.85 4.58,-5.73 2.21,-23.58 4.43,-23.58 6.65,0 1.18,-3.97 2.37,-3.74 3.56,0.74 1.78,-15.58 3.57,-17.61 5.36,-6.34 2.02,-17.80 4.04,-15.94 6.07,5.59 1.15,-3.85 2.29,-3.73 3.44,0.34 1.83,-17.70 3.66,-20.27 5.49,-7.82 C 98.17,61.88 99.08,57.65 100,57.63 l 0,-57.63 -100,0 0,60.13 c 0.69,0.01 1.39,2.33 2.08,6.99 2.01,-23.50 4.03,-22.70 6.05,2.69 1.29,-6.73 2.59,-6.30 3.89,1.10 1.97,-21.05 3.94,-21.05 5.92,0 1.24,-7.11 2.49,-7.77 3.74,-1.83 2.05,-29.82 4.11,-31.72 6.16,-5.75 1.69,-13.01 3.39,-10.50 5.08,7.58 1.35,-7.75 2.71,-7.75 4.07,0 2.15,-22.97 4.30,-20.84 6.46,6.33 1.05,-3.16 2.11,-2.97 3.16,0.57 2.11,-22.52 4.23,-23.59 6.35,-3.09 1.73,-8.20 3.46,-5.60 5.19,7.80 1.28,-3.65 2.57,-3.28 3.86,1.02 Z" />';
				default:
					return '';
			}
		}
		static function get_shape_bottom( $type ) {
			switch ( $type ) {
				case 'circle-center':
					return '<path d="M 0 0 A 100 100 0 0 0 200 0 L 4000 0 L 4000 105 L -4000 105 L -4000 0 Z" />';
				case 'circle-left':
					return '<path d="M 0 0 A 100 100 0 0 0 200 0 L 4000 0 L 4000 105 L -4000 105 L -4000 0 Z" transform="translate(-500, 0)" />';
				case 'circle-right':
					return '<path d="M 0 0 A 100 100 0 0 0 200 0 L 4000 0 L 4000 105 L -4000 105 L -4000 0 Z" transform="translate(500, 0)" />';
				case 'arrow-center':
					return '<polygon points="0,105 0,0 50,95 100,0 100,105"/>';
				case 'arrow-left':
					return '<polygon points="0,105 0,0 25,95 100,0 100,105"/>';
				case 'arrow-right':
					return '<polygon points="0,105 0,0 75,95 100,0 100,105"/>';
				case 'slope-left':
					return '<polygon points="0,105 0,95 100,0 100,105"/>';
				case 'slope-right':
					return '<polygon points="0,105 0,0 100,95 100,105"/>';
				case 'blob-center':
					return '<path d="M 0 0 L 0 105 L 100 105 L 100 0 Q 50 175 0 0 Z" />';
				case 'blob-left':
					return '<path d="M 0 0 L 0 105 L 100 105 L 100 0 Q 10 175 0 0 Z" />';
				case 'blob-right':
					return '<path d="M 0 0 L 0 105 L 100 105 L 100 0 Q 90 175 0 0 Z" />';
				case 'stamp':
					return '<path d="M 0 0 Q 2.5 50 5 0 Q 7.5 50 10 0 Q 12.5 50 15 0 Q 17.5 50 20 0 Q 22.5 50 25 0 Q 27.5 50 30 0 Q 32.5 50 35 0 Q 37.5 50 40 0 Q 42.5 50 45 0 Q 47.5 50 50 0 Q 52.5 50 55 0 Q 57.5 50 60 0 Q 62.5 50 65 0 Q 67.5 50 70 0 Q 72.5 50 75 0 Q 77.5 50 80 0 Q 82.5 50 85 0 Q 87.5 50 90 0 Q 92.5 50 95 0 Q 97.5 50 100 0 L 100 105 L 0 105 Z" />';
				case 'cloud':
					return '<path d="M 37.91 16.43 C 36.38 25.33 34.85 27.29 33.32 22.17 C 31.10 45.76 28.89 45.76 26.67 22.17 C 25.48 26.15 24.29 25.91 23.11 21.42 C 21.32 37.01 19.53 39.04 17.74 27.76 C 15.72 45.56 13.69 43.71 11.67 22.17 C 10.52 26.02 9.37 25.91 8.22 21.82 C 6.39 39.53 4.56 42.10 2.73 29.65 C 1.82 38.11 0.91 42.34 0 42.36 L 0 105 L 100 105 L 100 39.86 C 99.30 39.85 98.60 37.53 97.91 32.87 C 95.89 56.38 93.87 55.58 91.85 30.17 C 90.55 36.91 89.25 36.48 87.96 29.07 C 85.98 50.12 84.01 50.12 82.03 29.07 C 80.79 36.19 79.54 36.85 78.29 30.90 C 76.24 60.73 74.18 62.63 72.12 36.66 C 70.43 49.67 68.73 47.17 67.03 29.07 C 65.67 36.83 64.32 36.83 62.96 29.07 C 60.80 52.05 58.65 49.91 56.49 22.74 C 55.43 25.90 54.38 25.71 53.32 22.17 C 51.20 44.70 49.09 45.76 46.97 25.26 C 45.24 33.47 43.50 30.86 41.77 17.46 C 40.48 21.12 39.19 20.74 37.91 16.43 Z" />';
				default:
					return '';
			}
		}
	}
}

if ( class_exists( 'MPC_Row' ) ) {
	global $MPC_Row;
	$MPC_Row = new MPC_Row();
}