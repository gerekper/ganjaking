<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_WC_Category_Accordion_Premium' ) ) {

	class YITH_WC_Category_Accordion_Premium extends YITH_WC_Category_Accordion {


		protected $custom_filename;
		protected $_rules = array();

		public function __construct() {
			parent::__construct();

			$this->custom_filename = 'ywcca_dynamics.css';
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_premium_style_script' ), 15 );
			add_filter( 'ywcca_script_params', array( $this, 'add_script_params' ) );

			if ( is_admin() ) {
				add_action( 'current_screen', array( $this, 'ywcca_add_shortcodes_button' ) );
				add_action( 'admin_enqueue_scripts', array( $this, 'include_admin_style_script' ) );
				add_filter( 'yith_category_accordion_admin_tabs', array( $this, 'add_premium_admin_tabs' ), 10, 1 );


				$this->_include();
				add_action( 'woocommerce_admin_field_typography', 'YWCCA_Typography::output' );

			}
			// register shortcodes to WPBackery Visual Composer
			add_action( 'vc_before_init', array( $this, 'register_vc_shortcodes' ) );
		}

		/**Returns single instance of the class
		 * @return YITH_WC_Category_Accordion_Premium
		 * @since 1.0.0
		 * @author YITHEMES
		 */
		public static function get_instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}


		private function _include() {
			include_once( YWCCA_TEMPLATE_PATH . '/admin/typography.php' );
		}

		/**
		 * Register plugins for activation tab
		 *
		 * @return void
		 * @since    1.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_activation() {

			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YWCCA_DIR . 'plugin-fw/licence/lib/yit-licence.php';
				require_once YWCCA_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YWCCA_INIT, YWCCA_SECRET_KEY, YWCCA_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since    1.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once( YWCCA_DIR . 'plugin-fw/lib/yit-upgrade.php' );
			}
			YIT_Upgrade()->register( YWCCA_SLUG, YWCCA_INIT );
		}


		/**add script params, for extend script free
		 *
		 * @param $args
		 *
		 * @return mixed
		 * @author YITHEMES
		 * @use ywcca_script_params
		 *
		 */
		public function add_script_params( $args ) {
			$args['highlight_current_cat'] = get_option( 'ywcca_highlight_category' ) == 'yes';
			$args['event_type']            = get_option( 'ywcca_event_type_start_acc' );
			$args['accordion_speed']       = get_option( 'ywcca_accordion_speed' );
			$args['accordion_close']       = get_option( 'ywcca_accordion_macro_cat_close' ) == 'yes';
			$args['open_sub_cat_parent']   = get_option( 'ywcca_open_sub_cat_parent_visit' ) == 'yes';
			$args['toggle_always']         = apply_filters( 'ywcca_toggle_always', true );

			return $args;
		}

		/**include style and script premium for frontend
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 */
		public function enqueue_premium_style_script() {

			wp_register_script( 'hover_intent', YWCCA_ASSETS_URL . 'js/jquery.hoverIntent.min.js', array( 'jquery' ), YWCCA_VERSION, true );
			wp_add_inline_style( 'ywcca_accordion_style',$this->write_dynamic_css() );
		}

		/**include admin premium style and premium script
		 *
		 * @param $hook
		 *
		 * @since 1.0.0
		 *
		 * @author YITHEMES
		 */
		public function include_admin_style_script( $hook ) {
			wp_register_script( 'ywcca_admin_script', YWCCA_ASSETS_URL . 'js/ywcca_admin' . $this->suffix . '.js', array( 'jquery' ), YWCCA_VERSION, true );


			if ( $hook == 'widgets.php' ) {


				if ( ! wp_script_is( 'wc-enhanced-select' ) ) {

					$args = array(
						'jquery',
						'select2'
					);

					if ( version_compare( WC()->version, '3.2.0', '>=' ) ) {
						$args[] = 'selectWoo';
					}
					wp_enqueue_script( 'wc-enhanced-select', WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select' . $this->suffix . '.js', $args, WC_VERSION );

				}

				if ( ! wp_style_is( 'woocommerce_admin_styles' ) ) {
					wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );

				}
				wp_enqueue_script( 'ywcca_widget', YWCCA_ASSETS_URL . 'js/ywcca_widget' . $this->suffix . '.js', array( 'jquery' ), YWCCA_VERSION, true );

			}
			wp_enqueue_script( 'ywcca_admin_script' );

		}

		/**check if the ywcca_dynamics.css exists (for first installation)
		 * @return bool|int
		 * @since 1.0.0
		 * @author YITHEMES
		 */
		public function check_file_exists() {

			$file_path = YWCCA_DIR . 'cache/' . $this->_get_stylesheet_name();

			if ( ! file_exists( $file_path ) ) {
				return $this->write_dynamic_css();
			} else {
				return true;
			}
		}

		/**write dynamic css
		 *
		 * @since 1.0.0
		 * @author YITHEMES
		 */
		public function write_dynamic_css() {

			$css = array();

			// collect all css rules

			if ( empty( $this->_rules ) ) {
				$this->get_theme_options_css_rules();
			}

			foreach ( $this->_rules as $rule => $args ) {
				$args_css = array();
				foreach ( $args as $arg => $value ) {

					$args_css[] = $arg . ': ' . $value . ';';
				}
				$css[] = $rule . ' { ' . implode( ' ', $args_css ) . ' }' . "\n\n";
			}

			$css = apply_filters( 'ywcca_dynamics_style', implode( "", $css ) );

			return $css;
		}

		/**get the css rules form theme option
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function get_theme_options_css_rules() {
			$styles = array( 'style1', 'style2', 'style3', 'style4' );

			foreach ( $styles as $style ) {

				$ywcca_options_rules = include( YWCCA_DIR . 'plugin-options/' . $style . '-options.php' );

				foreach ( $ywcca_options_rules as $sections => $fields ) {

					foreach ( $fields as $field ) {

						if ( isset( $field['id'] ) ) {
							$this->add_by_option( $field, get_option( $field['id'] ), $field );
						}

					}
				}
			}
		}

		/**
		 * return the stylesheet name of dynamics css
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		private function _get_stylesheet_name() {
			global $wpdb;
			$index = $wpdb->blogid != 0 ? '-' . $wpdb->blogid : '';

			return str_replace( '.css', $index . '.css', $this->custom_filename );
		}

		/**
		 * Css Option Parse -> Transform a panel options in a css rules
		 *
		 * @param $option string
		 * @param $value string
		 * @param $options mixed array
		 *
		 * @return mixed
		 * @since  1.0.0
		 * @access public
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function add_by_option( $option, $value, $options ) {

			if ( ! isset( $option['style'] ) ) {
				return;
			}

			// used to store the properties of the rules
			$args = array();

			if ( isset( $option['style']['selectors'] ) ) {
				$style = array(
					array(
						'selectors'  => $option['style']['selectors'],
						'properties' => $option['style']['properties']
					)
				);
			} elseif ( isset( $option['variations'] ) ) {
				$style = array( $option['style'] );
			} else {
				$style = $option['style'];
			}

			foreach ( $style as $style_option ) {
				$args            = array();
				$option['style'] = $style_option;

				if ( $option['type'] == 'color' ) {

					$properties = explode( ',', $option['style']['properties'] );

					foreach ( $properties as $property ) {
						$args[ $property ] = $value;
					}

					$this->add( $option['style']['selectors'], $args );

				} elseif ( $option['type'] == 'bgpreview' ) {

					$this->add( $option['style']['selectors'], array( 'background' => "{$value['color']} url('{$value['image']}')" ) );

				} elseif ( $option['type'] == 'typography' ) {

					if ( isset( $value['size'] ) && isset( $value['unit'] ) ) {

						$args['font-size'] = $value['size'] . $value['unit'];
					}

					if ( isset( $value['color'] ) ) {
						$args['color'] = $value['color'];
					}

					if ( isset( $value['background'] ) ) {
						$args['background'] = $value['background'];
					}

					if ( isset( $value['style'] ) ) {

						switch ( $value['style'] ) {

							case 'bold' :
								$args['font-style']  = 'normal';
								$args['font-weight'] = '700';
								break;

							case 'extra-bold' :
								$args['font-style']  = 'normal';
								$args['font-weight'] = '800';
								break;

							case 'italic' :
								$args['font-style']  = 'italic';
								$args['font-weight'] = 'normal';
								break;

							case 'bold-italic' :
								$args['font-style']  = 'italic';
								$args['font-weight'] = '700';
								break;

							case 'regular' :
							case 'normal' :
								$args['font-style']  = 'normal';
								$args['font-weight'] = '400';
								break;

							default:
								if ( is_numeric( $value['style'] ) ) {
									$args['font-style']  = 'normal';
									$args['font-weight'] = $value['style'];
								} else {
									$args['font-style']  = 'italic';
									$args['font-weight'] = str_replace( 'italic', '', $value['style'] );
								}
								break;
						}

					}

					if ( isset ( $value['align'] ) ) {
						$args['text-align'] = $value['align'];
					}

					if ( isset ( $value['transform'] ) ) {
						$args['text-transform'] = $value['transform'];
					}

					$this->add( $option['style']['selectors'], $args );

				} elseif ( $option['type'] == 'upload' && $value ) {

					$this->add( $option['style']['selectors'], array( $option['style']['properties'] => "url('$value')" ) );

				} elseif ( $option['type'] == 'number' ) {

					$this->add( $option['style']['selectors'], array( $option['style']['properties'] => "{$value}px" ) );

				} elseif ( $option['type'] == 'select' ) {

					$this->add( $option['style']['selectors'], array( $option['style']['properties'] => "$value" ) );
				}
			}

		}

		/**
		 * Add the rule css
		 *
		 * @param string $rule
		 * @param array $args
		 *
		 * @return bool
		 * @since  1.0.0
		 * @access public
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function add( $rule, $args = array() ) {

			if ( isset( $this->_rules[ $rule ] ) ) {
				$this->_rules[ $rule ] = array_merge( $this->_rules[ $rule ], $args );
			} else {
				$this->_rules[ $rule ] = $args;
			}
		}

		/**
		 * Update the dynamic style
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 */
		public function update_dynamics_css() {
			global $pagenow;
			// if ( isset( $_GET["page"] ) && $_GET["page"] == $this->_panel_page ) {
			$this->write_dynamic_css();

			// }
		}


		/**
		 *Add shortcode button to TinyMCE editor, adding filter on mce_external_plugins
		 *
		 * @param WP_Screen $current_screen
		 *
		 * @since 1.0.0
		 * @use admin_init
		 * @author YITHEMES
		 */
		public function ywcca_add_shortcodes_button( $current_screen ) {

			if ( ! is_null( $current_screen ) && ! in_array( $current_screen->post_type, array( 'post', 'page' ) ) ) {
				if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
					return;

				}

				if ( get_user_option( 'rich_editing' ) == 'true' ) {
					add_filter( 'mce_external_plugins', array( &$this, 'ywcca_add_shortcodes_tinymce_plugin' ) );
					add_filter( 'mce_buttons', array( &$this, 'ywcca_register_shortcodes_button' ) );
					add_action( 'media_buttons_context', array( &$this, 'ywcca_media_buttons_context' ) );
					add_action( 'admin_print_footer_scripts', array( &$this, 'ywcca_add_quicktags' ) );
					add_action( 'admin_action_ywcca_shortcode_popup', array( $this, 'shortcode_popup' ) );

				}


			}
		}

		/**
		 * Add a script to TinyMCE script list
		 *
		 * @param   $plugin_array
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywcca_add_shortcodes_tinymce_plugin( $plugin_array ) {

			$plugin_array['ywcca_shortcode'] = YWCCA_ASSETS_URL . 'js/ywcca-tinymce' . $this->suffix . '.js';

			return $plugin_array;
		}

		/**
		 * Make TinyMCE know a new button was included in its toolbar
		 *
		 * @param   $buttons
		 *
		 * @return  array()
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywcca_register_shortcodes_button( $buttons ) {

			array_push( $buttons, "|", "ywcca_shortcode" );

			return $buttons;

		}

		/**
		 * The markup of shortcode
		 *
		 * @param   $context
		 *
		 * @return  mixed
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywcca_media_buttons_context( $context ) {

			global $post_ID, $temp_ID;

			$iframe_ID = (int) ( 0 == $post_ID ? $temp_ID : $post_ID );


			$out = '<a id="ywcca_shortcode" style="display:none" href="' . admin_url( 'admin.php?action=ywcca_shortcode_popup&post_id=' . $iframe_ID . '&TB_iframe=1' ) . '" class="hide-if-no-js thickbox" title="' . __( "Add YITH WooCommerce Category Accordion shortcode", 'yith-woocommerce-category-accordion' ) . '"></a>';


			return $context . $out;

		}

		/**
		 * include lightbox template
		 */
		public function shortcode_popup() {


			require_once( YWCCA_TEMPLATE_PATH . '/admin/lightbox.php' );
		}

		/**
		 * Add quicktags to visual editor
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function ywcca_add_quicktags() {
			?>
            <script type="text/javascript">

                if (window.QTags !== undefined) {
                    QTags.addButton('ywcca_shortcode', 'add ywcca shortcode', function () {
                        jQuery('#ywcca_shortcode').click()
                    });
                }
            </script>
			<?php
		}

		public function register_vc_shortcodes() {


			$vc_map_params = array(
				'yith_wcca_category_accordion' => array(
					'name'     => 'YITH WooCommerce Category Accordion',
					'base'     => 'yith_wcca_category_accordion',
					'category' => 'Category Accordion',
					'params'   => array(
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Show in Accordion', 'yith-woocommerce-category-accordion' ),
							'value'      => array(
								__( 'Tags', 'yith-woocommerce-category-accordion' )                 => 'tag',
								__( 'Menu', 'yith-woocommerce-category-accordion' )                 => 'menu',
								__( 'WooCommerce Category', 'yith-woocommerce-category-accordion' ) => 'wc',
								__( 'WorPress Category', 'yith-woocommerce-category-accordion' )    => 'wp'
							),
							'param_name' => 'how_show'
						),
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Show WooCommerce Subcategories', 'yith-woocommerce-category-accordion' ),
							'value'      => array(
								__( 'No', 'yith-woocommerce-category-accordion' )  => 'off',
								__( 'Yes', 'yith-woocommerce-category-accordion' ) => 'on',
							),
							'param_name' => 'show_sub_cat',
							'dependency' => array( 'how_show' => 'wc' )
						),

						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Show WordPress Subcategories', 'yith-woocommerce-category-accordion' ),
							'value'      => array(
								__( 'No', 'yith-woocommerce-category-accordion' )  => 'off',
								__( 'Yes', 'yith-woocommerce-category-accordion' ) => 'on',
							),
							'param_name' => 'show_sub_cat',
							'dependency' => array( 'element' => 'how_show', 'value' => array( 'wc', 'wp' ) )
						),
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Highlight the current category', 'yith-woocommerce-category-accordion' ),
							'value'      => array(
								__( 'No', 'yith-woocommerce-category-accordion' )  => 'off',
								__( 'Yes', 'yith-woocommerce-category-accordion' ) => 'on',
							),
							'param_name' => 'highlight',

						),
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Show Count', 'yith-woocommerce-category-accordion' ),
							'value'      => array(
								__( 'No', 'yith-woocommerce-category-accordion' )  => 'off',
								__( 'Yes', 'yith-woocommerce-category-accordion' ) => 'on',
							),
							'param_name' => 'show_count',
						),
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Style', 'yith-woocommerce-category-accordion' ),
							'value'      => array(
								__( 'Style 1', 'yith-woocommerce-category-accordion' ) => 'style1',
								__( 'Style 2', 'yith-woocommerce-category-accordion' ) => 'style2',
								__( 'Style 3', 'yith-woocommerce-category-accordion' ) => 'style3',
								__( 'Style 4', 'yith-woocommerce-category-accordion' ) => 'style4'
							),
							'param_name' => 'acc_style',
						),
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Order By', 'yith-woocommerce-category-accordion' ),
							'value'      => array(
								__( 'Name', 'yith-woocommerce-category-accordion' )  => 'name',
								__( 'Count', 'yith-woocommerce-category-accordion' ) => 'count',
								__( 'ID', 'yith-woocommerce-category-accordion' )    => 'id'
							),
							'param_name' => 'orderby',
						),
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Order', 'yith-woocommerce-category-accordion' ),
							'value'      => array(
								__( 'ASC', 'yith-woocommerce-category-accordion' )  => 'asc',
								__( 'DESC', 'yith-woocommerce-category-accordion' ) => 'desc',

							),
							'param_name' => 'order',
						)
					)

				)
			);


			if ( ! empty( $vc_map_params ) && function_exists( 'vc_map' ) ) {
				foreach ( $vc_map_params as $params ) {
					vc_map( $params );
				}
			}
		}

		public function add_premium_admin_tabs( $tabs ) {

			unset( $tabs['premium-landing'] );
			$tabs['settings'] = __( 'Settings', 'yith-woocommerce-category-accordion' );
			$tabs['style1']   = __( 'Style 1', 'yith-woocommerce-category-accordion' );
			$tabs['style2']   = __( 'Style 2', 'yith-woocommerce-category-accordion' );
			$tabs['style3']   = __( 'Style 3', 'yith-woocommerce-category-accordion' );
			$tabs['style4']   = __( 'Style 4', 'yith-woocommerce-category-accordion' );

			return $tabs;
		}

		/**
		 * plugin_row_meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $plugin_meta
		 * @param $plugin_file
		 * @param $plugin_data
		 * @param $status
		 * @param  $init_file
		 *
		 * @return   array
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YWCCA_INIT' ) {

			$new_row_meta_args = parent::plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file );
			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;
		}

	}
}