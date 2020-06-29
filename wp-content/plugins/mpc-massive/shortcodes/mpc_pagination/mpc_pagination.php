<?php
/*----------------------------------------------------------------------------*\
	PAGINATION SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Pagination' ) ) {
	class MPC_Pagination {
		public $shortcode = 'mpc_pagination';
		public $panel_section = array();
		public $defaults = array();
		private $parts = array();
		public $query;

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( $this->shortcode, array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}

			/* AJAX page load */
			add_action( 'wp_ajax_mpc_pagination_set', array( $this, 'load_content' ), 10, 2 );
			add_action( 'wp_ajax_nopriv_mpc_pagination_set', array( $this, 'load_content' ), 10, 2 );

			add_action( 'wp_ajax_mpc_pagination_refresh', array( $this, 'pagination_links' ) );
			add_action( 'wp_ajax_nopriv_mpc_pagination_refresh', array( $this, 'pagination_links' ) );

			$this->parts = array(
				'section_begin' => '',
				'section_end'   => '',
			);

			$this->defaults = array(
				'class'                       => '',
				'preset'                      => '',
				'type'                        => 'load-more',
				'align'                       => 'center',
				'end_size'                    => 1,
				'mid_size'                    => 1,
				'disable_ajax'                => '',
				'gap'                         => '1',
				'force_square'                => '',

				'title'                       => __( 'load more', 'mpc' ),
				'next_title'                  => '',
				'prev_title'                  => '',
				'font_preset'                 => '',
				'font_color'                  => '',
				'font_size'                   => '',
				'font_line_height'            => '',
				'font_align'                  => '',
				'font_transform'              => '',

				'padding_css'                 => '',
				'border_css'                  => '',
				'pag_margin_css'              => '',

				'icon_type'                   => 'icon',
				'icon'                        => '',
				'icon_character'              => '',
				'icon_image'                  => '',
				'icon_image_size'             => 'thumbnail',
				'icon_preset'                 => '',
				'icon_color'                  => '#333333',
				'icon_size'                   => '',

				'icon_effect'                 => 'none-none',
				'icon_gap'                    => '',

				'next_icon_type'                   => 'icon',
				'next_icon'                        => '',
				'next_icon_character'              => '',
				'next_icon_image'                  => '',
				'next_icon_image_size'             => 'thumbnail',
				'next_icon_preset'                 => '',
				'next_icon_color'                  => '#333333',
				'next_icon_size'                   => '',

				'next_icon_effect'                 => 'none-none',
				'next_icon_offset'                 => '',
				'next_icon_gap'                    => '',

				'prev_icon_type'                   => 'icon',
				'prev_icon'                        => '',
				'prev_icon_character'              => '',
				'prev_icon_image'                  => '',
				'prev_icon_image_size'             => 'thumbnail',
				'prev_icon_preset'                 => '',
				'prev_icon_color'                  => '#333333',
				'prev_icon_size'                   => '',

				'prev_icon_effect'                 => 'none-none',
				'prev_icon_offset'                 => '',
				'prev_icon_gap'                    => '',

				'prev_next_background_type'             => 'color',
				'prev_next_background_color'            => '',
				'prev_next_background_image'            => '',
				'prev_next_background_image_size'       => 'large',
				'prev_next_background_repeat'           => 'no-repeat',
				'prev_next_background_size'             => 'initial',
				'prev_next_background_position'         => 'middle-center',
				'prev_next_background_gradient'         => '#83bae3||#80e0d4||0;100||180||linear',

				'background_type'             => 'color',
				'background_color'            => '',
				'background_image'            => '',
				'background_image_size'       => 'large',
				'background_repeat'           => 'no-repeat',
				'background_size'             => 'initial',
				'background_position'         => 'middle-center',
				'background_gradient'         => '#83bae3||#80e0d4||0;100||180||linear',

				'hover_background_effect'     => 'fade-in',
				'hover_background_offset'     => '',

				'hover_border_css'            => '',

				'hover_font_color'            => '',
				'hover_icon_color'            => '',

				'hover_background_type'       => 'color',
				'hover_background_color'      => '',
				'hover_background_image'      => '',
				'hover_background_image_size' => 'large',
				'hover_background_repeat'     => 'no-repeat',
				'hover_background_size'       => 'initial',
				'hover_background_position'   => 'middle-center',
				'hover_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
			);
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_pagination-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_pagination/css/mpc_pagination.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_pagination-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_pagination/js/mpc_pagination' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* AJAX Pagination callback */
		function load_content() {
			$data = $_REQUEST[ 'query' ];
			$atts = $_REQUEST[ 'atts' ];

			if( !isset( $data[ 'callback' ] ) || $data[ 'callback' ] == '' ) {
				die();
			}

			if( strpos( $data[ 'callback' ], 'MPC' ) > -1 && !class_exists( $data[ 'callback' ] ) ) { // make sure that only MPC prefixed classes can be called
				die();
			}

			$handler = new $data[ 'callback' ];
			if( !method_exists( $handler, 'get_paginated_content' ) ) {
				die();
			}

			echo $handler->get_paginated_content( $data, $atts );
			die();
		}

		/* Generate Button Content */
		function create_button( $bt_atts ) {
			if( $bt_atts[ 'url' ] == '' ) {
				$button = '<a class="mpc-pagination__dots">';
					$button .= $bt_atts[ 'title' ] != '' ? '<span class="mpc-pagination__title mpc-transition">' . $bt_atts[ 'title' ] . '</span>' : '';
				$button .= '</a>';
			} else {
				$icon_effect_type = $bt_atts[ 'icon_effect' ][ 0 ] != '' ? 'mpc-effect-type--' . esc_attr( $bt_atts[ 'icon_effect' ][ 0 ] ) : '';
				$icon_effect_side = $bt_atts[ 'icon_effect' ][ 1 ] != '' ? 'mpc-effect-side--' . esc_attr( $bt_atts[ 'icon_effect' ][ 1 ] ) : '';

				$button = '<a class="' . $bt_atts[ 'classes' ] . '" href="' . $bt_atts[ 'url' ] . '">';
					$button .= '<div class="mpc-pagination__content ' . $icon_effect_type . ' ' . $icon_effect_side . '">';
					if ( $bt_atts[ 'icon_effect' ][ 1 ] == 'none' || $bt_atts[ 'icon_effect' ][ 1 ] == 'left' || $bt_atts[ 'icon_effect' ][ 1 ] == 'top' ) {
						$button .= $bt_atts[ 'icon' ][ 'class' ] != '' || $bt_atts[ 'icon' ][ 'content' ] != '' ? '<i class="mpc-pagination__icon mpc-transition ' . $bt_atts[ 'icon' ][ 'class' ] . $bt_atts[ 'classes_icon' ] . '">' . $bt_atts[ 'icon' ][ 'content' ] . '</i>' : '';
					}
					$button .= $bt_atts[ 'title' ] != '' ? '<span class="mpc-pagination__title mpc-transition">' . $bt_atts[ 'title' ] . '</span>' : '';
					if ( $bt_atts[ 'icon_effect' ][ 1 ] == 'right' || $bt_atts[ 'icon_effect' ][ 1 ] == 'bottom' ) {
						$button .= $bt_atts[ 'icon' ][ 'class' ] != '' || $bt_atts[ 'icon' ][ 'content' ] != '' ? '<i class="mpc-pagination__icon mpc-transition ' . $bt_atts[ 'icon' ][ 'class' ] . $bt_atts[ 'classes_icon' ] . '">' . $bt_atts[ 'icon' ][ 'content' ] . '</i>' : '';
					}
					$button .= '</div>';
					$button .= '<div class="mpc-pagination__background mpc-transition ' . $bt_atts[ 'background_effect_type' ] . ' ' . $bt_atts[ 'background_effect_side' ] . '"></div>';
				$button .= '</a>';
			}

			return $button;
		}

		/* Return shortcode markup for display */
		function pagination_links() {
			global $mpc_pagination_presets;
			$name = isset( $_REQUEST[ 'preset' ] ) ? $_REQUEST[ 'preset' ] : '';
			$data_query = isset( $_REQUEST[ 'query' ] ) ? $_REQUEST[ 'query' ] : '';

			if( $name == '' || $data_query == '' ) {
				echo __( 'Unexpected problem. Please reload the page.', 'mpc' );
				die();
			}

			if ( isset( $mpc_pagination_presets[ $name ] ) ) {
				$atts = $mpc_pagination_presets[ $name ];
				$atts[ 'preset' ] = $name;
			} else {
				$pagination_presets = get_option( 'mpc_presets_mpc_pagination' );

				$pagination_presets = json_decode( $pagination_presets, true );

				if ( isset( $pagination_presets[ $name ] ) ) {
					$atts = $pagination_presets[ $name ];
					$atts[ 'preset' ] = $name;

					$mpc_pagination_presets[ $name ] = $atts;
				} else {
					$atts = array();
					$save_default = true;
				}
			}

			if( empty( $atts ) ) {
				echo __( 'There is no such pagination preset available.', 'mpc' );
				die();
			}

			if( !isset( $data_query[ 'callback' ] ) || $data_query[ 'callback' ] == '' ) {
				die();
			}

			if( strpos( $data_query[ 'callback' ], 'MPC' ) > -1 && !class_exists( $data_query[ 'callback' ] ) ) { // make sure that only MPC prefixed classes can be called
				die();
			}

			$handler = new $data_query[ 'callback' ];
			if( !method_exists( $handler, 'get_posts' ) ) {
				die();
			}

			$handler->get_posts( $data_query );
			$query = $handler->get_query();

			$atts = shortcode_atts( $this->defaults, $atts );

			if( $query->max_num_pages <= 1 ) {
				return ''; // Nothing to paginate
			}

			$background_effect = explode( '-', $atts[ 'hover_background_effect' ] );
			if ( ! count( $background_effect ) == 2 ) {
				$background_effect = array( '', '' );
			}
			$bt_atts[ 'background_effect_type' ] = $background_effect[ 0 ] != '' ? 'mpc-effect-type--' . esc_attr( $background_effect[ 0 ] ) : '';
			$bt_atts[ 'background_effect_side' ] = $background_effect[ 1 ] != '' ? 'mpc-effect-side--' . esc_attr( $background_effect[ 1 ] ) : '';

			$classes = ' mpc-init';
			$classes .= $atts[ 'type' ] != '' ? ' mpc-pagination--' . esc_attr( $atts[ 'type' ] ) : '';
			$classes .= $atts[ 'preset' ] != '' ? ' mpc-pagination-preset--' . esc_attr( $atts[ 'preset' ] ) : '';
			$classes .= $atts[ 'align' ] != '' ? ' mpc-align--' . esc_attr( $atts[ 'align' ] ) : '';
			$classes .= $atts[ 'force_square' ] != '' ? ' mpc--square-init' : '';
			$classes .= $atts[ 'type' ] == 'classic' && $atts[ 'disable_ajax' ] != '' ? ' mpc-non-ajax' : '';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );
			$bt_classes = $atts[ 'font_preset' ] != '' ? 'mpc-typography--' . esc_attr( $atts[ 'font_preset' ] ) : '';

			$current = $data_query[ 'paged' ] && $atts[ 'type' ] == 'classic' ? intval( $data_query[ 'paged' ] ) : 1;

			$sh_atts = ' data-grid="' . $data_query[ 'target' ] . '"';
			$sh_atts .= ' data-current="' . $current . '" data-pages="' . $query->max_num_pages . '"';
			$sh_atts .= ' data-type="' . ( $atts[ 'type' ] != '' ? esc_attr( $atts[ 'type' ] ) : 'load-more' ) . '"';
			$sh_atts .= ' data-preset="' . $atts[ 'preset' ] . '"';

			$return = '<div class="mpc-pagination' . $classes . '"' . $sh_atts . '>';

			if( $atts[ 'type' ] != 'classic' ) {
				$bt_atts[ 'url' ]   = '#load-more';
				$bt_atts[ 'icon' ]  = MPC_Parser::icon( $atts );
				$bt_atts[ 'title' ] = esc_attr( $atts[ 'title' ] );

				$icon_effect = explode( '-', $atts[ 'icon_effect' ] );
				if ( ! count( $icon_effect ) == 2 ) {
					$icon_effect = array( '', '' );
				}
				$bt_atts[ 'icon_effect' ] = $icon_effect;

				$bt_atts[ 'classes' ]      = $bt_classes . ' mpc-pagination__link mpc-transition';
				$bt_atts[ 'classes_icon' ] = $atts[ 'icon_type' ] == 'image' ? ' mpc-icon--image' : '';

				$return .= $this->create_button( $bt_atts );
			} else {
				$prev_atts = $next_atts = $bt_atts;

				/* Pages */
				$pages = '';
				$bt_atts[ 'icon' ] = '';
				$bt_atts[ 'icon_effect' ] = array( '', '' );
				$bt_atts[ 'classes' ] = $bt_classes . ' mpc-pagination__link mpc-transition';

				$buttons = $this->paginate_links( array(
					'current' => $current,
					'end_size' => $atts[ 'end_size' ],
					'mid_size' => $atts[ 'mid_size' ],
				), $query );

				foreach( $buttons as $button ) {
					switch( $button[ 'title' ] ) {
						case 'prev':
							/* Prev Button */
							$prev_atts[ 'url' ]   = $button[ 'link' ];
							$prev_atts[ 'icon' ]  = MPC_Parser::icon( $atts, 'prev' );
							$prev_atts[ 'title' ] = esc_attr( $atts[ 'prev_title' ] );

							$icon_effect = explode( '-', $atts[ 'prev_icon_effect' ] );
							if ( ! count( $icon_effect ) == 2 ) {
								$icon_effect = array( '', '' );
							}
							$prev_atts[ 'icon_effect' ] = $icon_effect;

							$prev_atts[ 'classes' ]      = $bt_classes . ' mpc-pagination__prev mpc-transition';
							$prev_atts[ 'classes_icon' ] = $atts[ 'prev_icon_type' ] == 'image' ? ' mpc-icon--image' : '';
							$prev = '<li data-page="prev" ' . ( $current == 1 ? 'class="mpc-disabled"' : '' ) . '>' . $this->create_button( $prev_atts ) . '</li>';
							break;
						case 'next':
							/* Next Button */
							$next_atts[ 'icon' ]  = MPC_Parser::icon( $atts, 'next' );
							$next_atts[ 'title' ] = esc_attr( $atts[ 'next_title' ] );
							$next_atts[ 'url' ] = $button[ 'link' ];

							$icon_effect = explode( '-', $atts[ 'next_icon_effect' ] );
							if ( ! count( $icon_effect ) == 2 ) {
								$icon_effect = array( '', '' );
							}
							$next_atts[ 'icon_effect' ] = $icon_effect;

							$next_atts[ 'classes' ]      = $bt_classes . ' mpc-pagination__next mpc-transition';
							$next_atts[ 'classes_icon' ] = $atts[ 'next_icon_type' ] == 'image' ? ' mpc-icon--image' : '';
							$next = '<li data-page="next" ' . ( $current == $query->max_num_pages ? 'class="mpc-disabled"' : '' ) . '>' . $this->create_button( $next_atts ) . '</li>';
							break;
						case 'dots':
							$bt_atts[ 'title' ] = '&hellip;';
							$bt_atts[ 'url' ] = '';
							$class = ' class="mpc-dots"';
							$pages .= '<li data-page="' . $button[ 'title' ] . '"' . $class . '>' . $this->create_button( $bt_atts ) . '</li>';
							break;
						default:
							$bt_atts[ 'title' ] = $button[ 'title' ];
							$bt_atts[ 'url' ] = $button[ 'link' ];
							$class = intval( $button[ 'title' ] ) == $current ? ' class="mpc-current"' : '';
							$pages .= '<li data-page="' . $button[ 'title' ] . '"' . $class . '>' . $this->create_button( $bt_atts ) . '</li>';
							break;
					}
				}

				$return .= '<ul class="mpc-pagination__links">';
					$return .= isset( $prev ) ? $prev : '';
					$return .= $pages;
					$return .= isset( $next ) ? $next : '';
				$return .= '</ul>';
			}

			$return .= '</div>';

			wp_send_json_success( $return );
			die();
		}

		function shortcode_template( $name, $content = null, $shortcodes = null, $grid_id = '' ) {
			global $mpc_pagination_presets, $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			if ( isset( $mpc_pagination_presets[ $name ] ) ) {
				$atts = $mpc_pagination_presets[ $name ];
				$atts[ 'preset' ] = $name;
			} else {
				$pagination_presets = get_option( 'mpc_presets_mpc_pagination' );

				$pagination_presets = json_decode( $pagination_presets, true );

				if ( isset( $pagination_presets[ $name ] ) ) {
					$atts = $pagination_presets[ $name ];
					$atts[ 'preset' ] = $name;

					$mpc_pagination_presets[ $name ] = $atts;
				} else {
					$atts = array();
					$save_default = true;
				}
			}

			$atts = shortcode_atts( $this->defaults, $atts );

			if( $this->query->max_num_pages <= 1 ) {
				$this->query = null;
				return ''; // Nothing to paginate
			}

			$background_effect = explode( '-', $atts[ 'hover_background_effect' ] );
			if ( ! count( $background_effect ) == 2 ) {
				$background_effect = array( '', '' );
			}
			$bt_atts[ 'background_effect_type' ] = $background_effect[ 0 ] != '' ? 'mpc-effect-type--' . esc_attr( $background_effect[ 0 ] ) : '';
			$bt_atts[ 'background_effect_side' ] = $background_effect[ 1 ] != '' ? 'mpc-effect-side--' . esc_attr( $background_effect[ 1 ] ) : '';

			$classes = ' mpc-init';
			$classes .= $atts[ 'type' ] != '' ? ' mpc-pagination--' . esc_attr( $atts[ 'type' ] ) : '';
			$classes .= $atts[ 'preset' ] != '' ? ' mpc-pagination-preset--' . esc_attr( $atts[ 'preset' ] ) : '';
			$classes .= $atts[ 'align' ] != '' ? ' mpc-align--' . esc_attr( $atts[ 'align' ] ) : '';
			$classes .= $atts[ 'force_square' ] != '' ? ' mpc--square-init' : '';
			$classes .= $atts[ 'type' ] == 'classic' && $atts[ 'disable_ajax' ] != '' ? ' mpc-non-ajax' : '';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );
			$bt_classes = $atts[ 'font_preset' ] != '' ? 'mpc-typography--' . esc_attr( $atts[ 'font_preset' ] ) : '';

			$current = $this->query->query_vars[ 'paged' ] ? $this->query->query_vars[ 'paged' ] : 1;

			$sh_atts = ' data-grid="' . $grid_id . '"';
			$sh_atts .= ' data-current="' . $current . '" data-pages="' . $this->query->max_num_pages . '"';
			$sh_atts .= ' data-type="' . ( $atts[ 'type' ] != '' ? esc_attr( $atts[ 'type' ] ) : 'load-more' ) . '"';
			$sh_atts .= ' data-preset="' . $atts[ 'preset' ] . '"';

			$return = '<div class="mpc-pagination' . $classes . '"' . $sh_atts . '>';

			if( $atts[ 'type' ] != 'classic' ) {
				$bt_atts[ 'url' ]   = '#load-more';
				$bt_atts[ 'icon' ]  = MPC_Parser::icon( $atts );
				$bt_atts[ 'title' ] = esc_attr( $atts[ 'title' ] );

				$icon_effect = explode( '-', $atts[ 'icon_effect' ] );
				if ( ! count( $icon_effect ) == 2 ) {
					$icon_effect = array( '', '' );
				}
				$bt_atts[ 'icon_effect' ] = $icon_effect;

				$bt_atts[ 'classes' ]      = $bt_classes . ' mpc-pagination__link mpc-transition';
				$bt_atts[ 'classes_icon' ] = $atts[ 'icon_type' ] == 'image' ? ' mpc-icon--image' : '';

				$return .= $this->create_button( $bt_atts );
			} else {
				$prev_atts = $next_atts = $bt_atts;

				/* Pages */
				$pages = '';
				$bt_atts[ 'icon' ] = '';
				$bt_atts[ 'icon_effect' ] = array( '', '' );
				$bt_atts[ 'classes' ] = $bt_classes . ' mpc-pagination__link mpc-transition';

				$buttons = $this->paginate_links( array(
					'mid_size' => $atts[ 'mid_size' ],
					'end_size' => $atts[ 'end_size' ],
				), $this->query );

				foreach( $buttons as $button ) {
					switch( $button[ 'title' ] ) {
						case 'prev':
							/* Prev Button */
							$prev_atts[ 'url' ]   = $button[ 'link' ];
							$prev_atts[ 'icon' ]  = MPC_Parser::icon( $atts, 'prev' );
							$prev_atts[ 'title' ] = esc_attr( $atts[ 'prev_title' ] );

							$icon_effect = explode( '-', $atts[ 'prev_icon_effect' ] );
							if ( ! count( $icon_effect ) == 2 ) {
								$icon_effect = array( '', '' );
							}
							$prev_atts[ 'icon_effect' ] = $icon_effect;

							$prev_atts[ 'classes' ]      = $bt_classes . ' mpc-pagination__prev mpc-transition';
							$prev_atts[ 'classes_icon' ] = $atts[ 'prev_icon_type' ] == 'image' ? ' mpc-icon--image' : '';
							$prev = '<li data-page="prev" ' . ( $current == 1 ? 'class="mpc-disabled"' : '' ) . '>' . $this->create_button( $prev_atts ) . '</li>';
							break;
						case 'next':
							/* Next Button */
							$next_atts[ 'icon' ]  = MPC_Parser::icon( $atts, 'next' );
							$next_atts[ 'title' ] = esc_attr( $atts[ 'next_title' ] );
							$next_atts[ 'url' ] = $button[ 'link' ];

							$icon_effect = explode( '-', $atts[ 'next_icon_effect' ] );
							if ( ! count( $icon_effect ) == 2 ) {
								$icon_effect = array( '', '' );
							}
							$next_atts[ 'icon_effect' ] = $icon_effect;

							$next_atts[ 'classes' ]      = $bt_classes . ' mpc-pagination__next mpc-transition';
							$next_atts[ 'classes_icon' ] = $atts[ 'next_icon_type' ] == 'image' ? ' mpc-icon--image' : '';
							$next = '<li data-page="next" ' . ( $current == $this->query->max_num_pages ? 'class="mpc-disabled"' : '' ) . '>' . $this->create_button( $next_atts ) . '</li>';
							break;
						case 'dots':
							$bt_atts[ 'title' ] = '&hellip;';
							$bt_atts[ 'url' ] = '';
							$class = ' class="mpc-dots"';
							$pages .= '<li data-page="' . $button[ 'title' ] . '"' . $class . '>' . $this->create_button( $bt_atts ) . '</li>';
							break;
						default:
							$bt_atts[ 'title' ] = $button[ 'title' ];
							$bt_atts[ 'url' ] = $button[ 'link' ];
							$class = intval( $button[ 'title' ] ) == $current ? ' class="mpc-current"' : '';
							$pages .= '<li data-page="' . $button[ 'title' ] . '"' . $class . '>' . $this->create_button( $bt_atts ) . '</li>';
							break;
					}
				}

				$return .= '<ul class="mpc-pagination__links">';
					$return .= isset( $prev ) ? $prev : '';
					    $return .= $pages;
					$return .= isset( $next ) ? $next : '';
				$return .= '</ul>';
			}

			$return .= '</div>';

			$this->query = null;

			return $return;
		}

		function paginate_links( $args = '', $query = '' ) {
			global $wp_rewrite;

			// Setting up default values based on the current URL.
			$pagenum_link = html_entity_decode( get_pagenum_link() );
			$url_parts    = explode( '?', $pagenum_link );

			// Get max pages and current page out of the current query, if available.
			$total   = isset( $query->max_num_pages ) ? $query->max_num_pages : 1;
			$current = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

			// Append the format placeholder to the base URL.
			$pagenum_link = trailingslashit( $url_parts[0] ) . '%_%';

			// URL base depends on permalink settings.
			$format  = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
			$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';

			$defaults = array(
				'base' => $pagenum_link, // http://example.com/all_posts.php%_% : %_% is replaced by format (below)
				'format' => $format, // ?page=%#% : %#% is replaced by the page number
				'total' => $total,
				'current' => $current,
				'add_args' => array(), // array of query args to add
				'add_fragment' => '',
			);

			$args = wp_parse_args( $args, $defaults );

			if ( ! is_array( $args[ 'add_args' ] ) ) {
				$args[ 'add_args' ] = array();
			}

			// Merge additional query vars found in the original URL into 'add_args' array.
			if ( isset( $url_parts[ 1 ] ) ) {
				// Find the format argument.
				$format = explode( '?', str_replace( '%_%', $args[ 'format' ], $args[ 'base' ] ) );
				$format_query = isset( $format[ 1 ] ) ? $format[ 1 ] : '';
				wp_parse_str( $format_query, $format_args );

				// Find the query args of the requested URL.
				wp_parse_str( $url_parts[ 1 ], $url_query_args );

				// Remove the format argument from the array of query arguments, to avoid overwriting custom format.
				foreach ( $format_args as $format_arg => $format_arg_value ) {
					unset( $url_query_args[ $format_arg ] );
				}

				$args[ 'add_args' ] = array_merge( $args[ 'add_args' ], urlencode_deep( $url_query_args ) );
			}

			// Who knows what else people pass in $args
			$total = (int) $args[ 'total' ];
			if ( $total < 2 ) {
				return '';
			}
			$current  = (int) $args[ 'current' ];
			$end_size = (int) $args[ 'end_size' ]; // Out of bounds?  Make it the default.
			if ( $end_size < 1 ) {
				$end_size = 1;
			}
			$mid_size = (int) $args[ 'mid_size' ];
			if ( $mid_size < 0 ) {
				$mid_size = 2;
			}
			$add_args = $args[ 'add_args' ];
			$page_links = array();
			$dots = false;

			if ( $current && 1 < $current ) :
				$link = str_replace( '%_%', 2 == $current ? '' : $args[ 'format' ], $args[ 'base' ] );
				$link = str_replace( '%#%', $current - 1, $link );
				if ( $add_args )
					$link = add_query_arg( $add_args, $link );
				$link .= $args[ 'add_fragment' ];

				$page_links[] = array(
					'link' => apply_filters( 'paginate_links', $link ),
					'title' => 'prev',
				);
			endif;

			for ( $n = 1; $n <= $total; $n++ ) :
				if ( $n == $current ) :
					$page_links[] = array(
						'link' => '#',
						'title' => number_format_i18n( $n ),
					);
					$dots = true;
				else :
					if ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size ) :
						$link = str_replace( '%_%', 1 == $n ? '' : $args[ 'format' ], $args[ 'base' ] );
						$link = str_replace( '%#%', $n, $link );
						if ( $add_args )
							$link = add_query_arg( $add_args, $link );
						$link .= $args[ 'add_fragment' ];

						$page_links[] = array(
							'link' => apply_filters( 'paginate_links', $link ),
							'title' => number_format_i18n( $n ),
						);
						$dots = true;
					elseif ( $dots ) :
						$page_links[] = array(
							'link' => '',
							'title' => 'dots',
						);
						$dots = false;
					endif;
				endif;
			endfor;

			if ( $current && ( $current < $total || -1 == $total ) ) :
				$link = str_replace( '%_%', $args[ 'format' ], $args[ 'base' ] );
				$link = str_replace( '%#%', $current + 1, $link );
				if ( $add_args )
					$link = add_query_arg( $add_args, $link );
				$link .= $args[ 'add_fragment' ];

				$page_links[] = array(
					'link' => apply_filters( 'paginate_links', $link ),
					'title' => 'next',
				);
			endif;

			return $page_links;
		}

		/* Styles */
		function shortcode_styles( $styles, $preset ) {
			$style = '';
			$preset = '.mpc-pagination-preset--' . $preset;
			$button = $preset . ' a:not(.mpc-pagination__dots)';

			// Add 'px'
			$styles[ 'gap' ] = $styles[ 'gap' ] != '' ? $styles[ 'gap' ] . ( is_numeric( $styles[ 'gap' ] ) ? 'px' : '' ) : '';
			$styles[ 'font_size' ] = $styles[ 'font_size' ] != '' ? $styles[ 'font_size' ] . ( is_numeric( $styles[ 'font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'icon_gap' ]  = $styles[ 'icon_gap' ] != '' ? $styles[ 'icon_gap' ] . ( is_numeric( $styles[ 'icon_gap' ] ) ? 'px' : '' ) : '';
			if( $styles[ 'type' ] != 'classic' ) {
				$styles[ 'icon_size' ] = $styles[ 'icon_size' ] != '' ? $styles[ 'icon_size' ] . ( is_numeric( $styles[ 'icon_size' ] ) ? 'px' : '' ) : '';
			} else {
				$styles[ 'next_icon_size' ] = $styles[ 'next_icon_size' ] != '' ? $styles[ 'next_icon_size' ] . ( is_numeric( $styles[ 'next_icon_size' ] ) ? 'px' : '' ) : '';
				$styles[ 'prev_icon_size' ] = $styles[ 'prev_icon_size' ] != '' ? $styles[ 'prev_icon_size' ] . ( is_numeric( $styles[ 'prev_icon_size' ] ) ? 'px' : '' ) : '';
			}

			// Add '%'
			$styles[ 'hover_background_offset' ] = $styles[ 'hover_background_offset' ] != '' ? $styles[ 'hover_background_offset' ] . ( is_numeric( $styles[ 'hover_background_offset' ] ) ? '%' : '' ) : '';

			// Regular
			if ( $styles[ 'pag_margin_css' ] ) {
				$style .= $preset . ' {';
					$style .= $styles[ 'pag_margin_css' ];
				$style .= '}';
			}

			if( $styles[ 'gap' ] && $styles[ 'gap' ] != '0px' ) {
				$style .= $preset . ' li {';
					$style .= 'margin-right:' . $styles[ 'gap' ] . ';';
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'border_css' ] ) { $inner_styles[] = $styles[ 'border_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles ) ) { $inner_styles[] = $temp_style; }
			if ( $temp_style = MPC_CSS::font( $styles ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= $button . ' {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			$inner_styles = array();

			if( $styles[ 'type' ] != 'classic' ) {
				if ( $styles[ 'icon_gap' ] && $styles[ 'icon_effect' ] == 'stay-left' ) { $inner_styles[] = 'padding-right:' . $styles[ 'icon_gap' ] . ' !important;'; }
				if ( $styles[ 'icon_gap' ] && $styles[ 'icon_effect' ] == 'stay-right' ) { $inner_styles[] = 'padding-left:' . $styles[ 'icon_gap' ] . ' !important;'; }
				if ( $temp_style = MPC_CSS::icon( $styles ) ) { $inner_styles[] = $temp_style; }
				if ( $styles[ 'padding_css' ] ) { $inner_styles[] = $styles[ 'padding_css' ]; }
				if ( count( $inner_styles ) > 0 ) {
					$style .= $button . ' .mpc-pagination__icon {';
						$style .= join( '', $inner_styles );
					$style .= '}';
				}
			} else {
				/* Prev */
				$inner_styles = array();
				if ( $styles[ 'icon_gap' ] && $styles[ 'prev_icon_effect' ] == 'stay-left' ) { $inner_styles[] = 'padding-right:' . $styles[ 'icon_gap' ] . ' !important;'; }
				if ( $styles[ 'icon_gap' ] && $styles[ 'prev_icon_effect' ] == 'stay-right' ) { $inner_styles[] = 'padding-left:' . $styles[ 'icon_gap' ] . ' !important;'; }
				if ( $styles[ 'padding_css' ] ) { $inner_styles[] = $styles[ 'padding_css' ]; }
				if ( $temp_style = MPC_CSS::icon( $styles, 'prev' ) ) { $inner_styles[] = $temp_style; }
				if ( count( $inner_styles ) > 0 ) {
					$style .= $button . '.mpc-pagination__prev .mpc-pagination__icon {';
						$style .= join( '', $inner_styles );
					$style .= '}';
				}

				/* Next */
				$inner_styles = array();
				if ( $styles[ 'icon_gap' ] && $styles[ 'next_icon_effect' ] == 'stay-left' ) { $inner_styles[] = 'padding-right:' . $styles[ 'icon_gap' ] . ' !important;'; }
				if ( $styles[ 'icon_gap' ] && $styles[ 'next_icon_effect' ] == 'stay-right' ) { $inner_styles[] = 'padding-left:' . $styles[ 'icon_gap' ] . ' !important;'; }
				if ( $styles[ 'padding_css' ] ) { $inner_styles[] = $styles[ 'padding_css' ]; }
				if ( $temp_style = MPC_CSS::icon( $styles, 'next' ) ) { $inner_styles[] = $temp_style; }
				if ( count( $inner_styles ) > 0 ) {
					$style .= $button . '.mpc-pagination__next .mpc-pagination__icon {';
						$style .= join( '', $inner_styles );
					$style .= '}';
				}

				$inner_styles = array();
				if ( $temp_style = MPC_CSS::background( $styles, 'prev_next' ) ) { $inner_styles[] = $temp_style; }
				if ( count( $inner_styles ) > 0 ) {
					$style .= $button . '.mpc-pagination__next,';
					$style .= $button . '.mpc-pagination__prev {';
						$style .= join( '', $inner_styles );
					$style .= '}';
				}
			}

			if ( $styles[ 'padding_css' ] ) {
				$style .= $button . ' .mpc-pagination__title {';
					$style .= $styles[ 'padding_css' ];
				$style .= '}';
			}

			if ( $temp_style = MPC_CSS::background( $styles, 'hover' ) ) {
				$style .= $button . ' .mpc-pagination__background {';
					$style .= $temp_style;
				$style .= '}';
			}

			// Hover
			if ( $styles[ 'hover_border_css' ] ) {
				$style .= $button . ':hover,';
				$style .= $preset . ' .mpc-current a {';
					$style .= $styles[ 'hover_border_css' ];
				$style .= '}';
			}

			if ( $styles[ 'hover_icon_color' ] ) {
				$style .= $button . ':hover .mpc-pagination__icon,';
				$style .= $preset . ' .mpc-current .mpc-pagination__icon {';
					$style .= 'color:' . $styles[ 'hover_icon_color' ] . ';';
				$style .= '}';
			}

			if ( $styles[ 'hover_font_color' ] ) {
				$style .= $button . ':hover .mpc-pagination__title,';
				$style .= $preset . ' .mpc-current .mpc-pagination__title {';
					$style .= 'color:' . $styles[ 'hover_font_color' ] . ';';
				$style .= '}';
			}

			if ( $styles[ 'hover_background_offset' ] ) {
				$temp_style = '';

				if ( $styles[ 'hover_background_effect' ] == 'expand-horizontal' ) {
					$temp_style = 'left:' . $styles[ 'hover_background_offset' ] . ' !important;right:' . $styles[ 'hover_background_offset' ] . ' !important;';
				} elseif ( $styles[ 'hover_background_effect' ] == 'expand-vertical' ) {
					$temp_style = 'top:' . $styles[ 'hover_background_offset' ] . ' !important;bottom:' . $styles[ 'hover_background_offset' ] . ' !important;';
				} elseif ( $styles[ 'hover_background_effect' ] == 'expand-diagonal_left' || $styles[ 'hover_background_effect' ] == 'expand-diagonal_right' ) {
					$temp_style = 'top:-' . $styles[ 'hover_background_offset' ] . ' !important;bottom:-' . $styles[ 'hover_background_offset' ] . ' !important;';
				}

				if ( $temp_style ) {
					$style .= $button . ':hover .mpc-pagination__background,';
					$style .= $preset . ' .mpc-current .mpc-pagination__background {';
						$style .= $temp_style;
					$style .= '}';
				}
			}

			return $style;
		}

		/* Map all shortcode options to Visual Composer popup */
		function shortcode_map() {
			if ( ! function_exists( 'vc_map' ) ) {
				return '';
			}

			$base = array(
				array(
					'type'            => 'mpc_preset',
					'heading'         => __( 'Main Preset', 'mpc' ),
					'param_name'      => 'preset',
					'tooltip'         => MPC_Helper::style_presets_desc(),
					'value'           => '',
					'shortcode'       => $this->shortcode,
					'sub_type'        => 'pagination',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Type', 'mpc' ),
					'param_name'       => 'type',
					'tooltip'          => __( 'Select pagination type:<br><b>Load More</b>: displays load more button to load next items under current ones;<br><b>Infinity</b>: automatic load more when user reach last item;<br><b>Classic</b>: normal pagination with next/previous buttons and pages.', 'mpc' ),
					'value'            => array(
						__( 'Load More' ) => 'load-more',
						__( 'Infinity' )  => 'infinity',
						__( 'Classic' )   => 'classic',
					),
					'std'              => 'load-more',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Position', 'mpc' ),
					'param_name'       => 'align',
					'tooltip'          => __( 'Select pagination position.', 'mpc' ),
					'value'            => array(
						__( 'Left', 'mpc' )    => 'left',
						__( 'Right', 'mpc' )   => 'right',
						__( 'Center', 'mpc' )  => 'center',
					),
					'std'              => 'center',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Disable AJAX', 'mpc' ),
					'param_name'  => 'disable_ajax',
					'tooltip'     => __( 'Check to disable AJAX reloading and use traditional method.', 'mpc' ),
					'value'       => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'         => '',
					'dependency'  => array( 'element' => 'type', 'value' => 'classic' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Mid Size', 'mpc' ),
					'param_name'       => 'mid_size',
					'tooltip'          => __( 'Specify how many items should be displayed around the current page number.', 'mpc' ),
					'value'            => 1,
					'addon'            => array(
						'icon'  => 'dashicons-admin-settings',
						'align' => 'prepend',
					),
					'label'            => '',
					'validate'         => true,
					'dependency'       => array( 'element' => 'type', 'value' => 'classic' ),
					'edit_field_class' => 'vc_col-sm-3 vc_column',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'End Size', 'mpc' ),
					'param_name'       => 'end_size',
					'tooltip'          => __( 'Specify how many items should be displayed around Next/Prev buttons.', 'mpc' ),
					'value'            => 1,
					'addon'            => array(
						'icon'  => 'dashicons-admin-settings',
						'align' => 'prepend',
					),
					'label'            => '',
					'validate'         => true,
					'dependency'       => array( 'element' => 'type', 'value' => 'classic' ),
					'edit_field_class' => 'vc_col-sm-3 vc_column',
				),
//				array(
//					'type'        => 'checkbox',
//					'heading'     => __( 'Append Content', 'mpc' ),
//					'param_name'  => 'append_ajax',
//					'tooltip'     => __( 'Check to append next page instead of replacing current.', 'mpc' ),
//					'value'       => array( __( 'Enable', 'mpc' ) => 'true' ),
//					'std'         => '',
//					'dependency'  => array( 'element' => 'disable_ajax', 'value_not_equal_to' => 'true' ),
//					'edit_field_class' => 'vc_col-sm-6 vc_column',
//				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Gap', 'mpc' ),
					'param_name'       => 'gap',
					'tooltip'          => __( 'Choose gap between buttons.', 'mpc' ),
					'min'              => 0,
					'max'              => 50,
					'step'             => 1,
					'value'            => 1,
					'unit'             => 'px',
					'dependency'       => array( 'element' => 'type', 'value' => 'classic' ),
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Force Square Buttons', 'mpc' ),
					'param_name'  => 'force_square',
					'tooltip'     => __( 'Check to force square shape of buttons.', 'mpc' ),
					'value'       => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'         => '',
					'dependency'  => array(
						'element' => 'type',
						'value'   => 'classic'
					),
				),
			);

			$button_exclude = array( 'exclude_regex' => '/^preset|url|block|animation_(.*)|mpc_tooltip|margin_(.*)|class|class_divider/' );
			$integrate_button = vc_map_integrate_shortcode( 'mpc_button', '', '', $button_exclude );

			$margin    = MPC_Snippets::vc_margin( array( 'prefix' => 'pag', 'subtitle' => __( 'Pagination', 'mpc' ) ) );
			$prev_icon = MPC_Snippets::vc_icon( array( 'prefix' => 'prev', 'subtitle' => __( 'Previous Button','mpc' ), 'dependency' => array( 'element' => 'type', 'value' => 'classic' ) ) );
			$next_icon = MPC_Snippets::vc_icon( array( 'prefix' => 'next', 'subtitle' => __( 'Next Button','mpc' ), 'dependency' => array( 'element' => 'type', 'value' => 'classic' )  ) );
			$prev_next_bg = MPC_Snippets::vc_background( array( 'prefix' => 'prev_next', 'subtitle' => __( 'Previous/Next Button'), 'tooltip' => __( 'Leave empty to use default background.', 'mpc' ), 'dependency' => array( 'element' => 'type', 'value' => 'classic' ) ) );

			$prev_text = array(
				array(
					'type'             => 'textfield',
					'heading'          => __( 'Text', 'mpc' ),
					'param_name'       => 'prev_title',
					'tooltip'          => __( 'Define previous button text.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'dependency'       => array( 'element' => 'type', 'value' => 'classic' ),
				),
			);

			$next_text = array(
				array(
					'type'             => 'textfield',
					'heading'          => __( 'Text', 'mpc' ),
					'param_name'       => 'next_title',
					'tooltip'          => __( 'Define next button text.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'dependency'       => array( 'element' => 'type', 'value' => 'classic' ),
				),
			);

			$prev_icon_effect = array(
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Display Effect', 'mpc' ),
					'param_name'       => 'prev_icon_effect',
					'tooltip'          => __( 'Select icon display style:<br><b>None</b>: hide the icon;<br><b>Stay</b>: display icon on selected side;<br><b>Slide In</b>: slide icon in from selected side;<br><b>Push Out</b>: push out button text with icon from selected side.', 'mpc' ),
					'value'            => array(
						__( 'None', 'mpc' )                   => 'none-none',
						__( 'Stay - Left', 'mpc' )            => 'stay-left',
						__( 'Stay - Right', 'mpc' )           => 'stay-right',
						__( 'Slide In - from Left', 'mpc' )   => 'slide-left',
						__( 'Slide In - from Right', 'mpc' )  => 'slide-right',
						__( 'Push Out - from Top', 'mpc' )    => 'push_out-top',
						__( 'Push Out - from Right', 'mpc' )  => 'push_out-right',
						__( 'Push Out - from Bottom', 'mpc' ) => 'push_out-bottom',
						__( 'Push Out - from Left', 'mpc' )   => 'push_out-left',
					),
					'std'              => 'none-none',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency'       => array( 'element' => 'type', 'value' => 'classic' ),
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Custom Gap', 'mpc' ),
					'param_name'       => 'icon_gap',
					'tooltip'          => __( 'Define gap between icon and text.', 'mpc' ),
					'value'            => '',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-leftright',
						'align' => 'prepend',
					),
					'label'            => 'px',
					'validate'         => true,
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency'       => array( 'element' => 'prev_icon_effect', 'value' => array( 'stay-left', 'stay-right' ) ),
				),
			);

			$next_icon_effect = array(
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Display Effect', 'mpc' ),
					'param_name'       => 'next_icon_effect',
					'tooltip'          => __( 'Select icon display style:<br><b>None</b>: hide the icon;<br><b>Stay</b>: display icon on selected side;<br><b>Slide In</b>: slide icon in from selected side;<br><b>Push Out</b>: push out button text with icon from selected side.', 'mpc' ),
					'value'            => array(
						__( 'None', 'mpc' )                   => 'none-none',
						__( 'Stay - Left', 'mpc' )            => 'stay-left',
						__( 'Stay - Right', 'mpc' )           => 'stay-right',
						__( 'Slide In - from Left', 'mpc' )   => 'slide-left',
						__( 'Slide In - from Right', 'mpc' )  => 'slide-right',
						__( 'Push Out - from Top', 'mpc' )    => 'push_out-top',
						__( 'Push Out - from Right', 'mpc' )  => 'push_out-right',
						__( 'Push Out - from Bottom', 'mpc' ) => 'push_out-bottom',
						__( 'Push Out - from Left', 'mpc' )   => 'push_out-left',
					),
					'std'              => 'none-none',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency'       => array( 'element' => 'type', 'value' => 'classic' ),
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Custom Gap', 'mpc' ),
					'param_name'       => 'next_icon_gap',
					'tooltip'          => __( 'Define gap between icon and text.', 'mpc' ),
					'value'            => '',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-leftright',
						'align' => 'prepend',
					),
					'label'            => 'px',
					'validate'         => true,
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency'       => array( 'element' => 'next_icon_effect', 'value' => array( 'stay-left', 'stay-right' ) ),
				),
			);

			$button_dependency = array( 'element' => 'type', 'value_not_equal_to' => 'classic' );
			$button_params_for_dependency = array( 'title', 'icon_divider', 'icon_type', 'icon_effect' );

			foreach ( $integrate_button as $index => $values ) {
				if ( in_array( $values[ 'param_name'], $button_params_for_dependency ) ) {

					$values[ 'dependency' ] = $button_dependency;
					$integrate_button[ $index ] = $values;
				}
			}

			$class = MPC_Snippets::vc_class();

			$params = array_merge( $base, $integrate_button, $prev_icon, $prev_text, $prev_icon_effect, $next_icon, $next_text, $next_icon_effect, $prev_next_bg, $margin, $class );

			return array(
				'name'        => __( 'Pagination', 'mpc' ),
				'description' => __( 'Pagination buttons', 'mpc' ),
				'base'        => 'mpc_pagination',
				'class'       => '',
				'icon'        => 'mpc-shicon-pagination',
				'category'    => __( 'Massive', 'mpc' ),
				'as_child'    => array( 'only' => '' ),
				'params'      => $params,
			);
		}
	}
}
if ( class_exists( 'MPC_Pagination' ) ) {
	global $MPC_Pagination;
	$MPC_Pagination = new MPC_Pagination;
}
if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_mpc_pagination' ) ) {
	class WPBakeryShortCode_mpc_pagination extends WPBakeryShortCode {}
}
