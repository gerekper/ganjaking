<?php

/**
 * Initialize Porto Elementor Page Builder
 *
 * @since 5.3.0
 */

if ( ! class_exists( 'Porto_Elementor_Init' ) ) :

	class Porto_Elementor_Init {

		private $widgets = array(
			'blog',
			'portfolio',
			'ultimate_heading',
			'info_box',
			'recent_posts',
			'stat_counter',
			'button',
			'modal',
			'sidebar_menu',
			'members',
			'recent_members',
			'pricing_table',
			'recent_portfolios',
			'circular_bar',
			'events',
			'fancytext',
			'countdown',
			'faqs',
			'google_map',
			'portfolios_category',
			'hotspot',
			'floating',
			'page_header',
			'social_icons',
			'image_comparison',
			'image_gallery',
			'360degree_image_viewer',
			'steps',
			'sticky_nav',
		);

		private $woo_widgets = array(
			'products',
			'product_categories',
			'one_page_category_products',
			'products_filter',
		);

		private $porto_metas = array(
			'porto_default',
			'porto_layout',
			'porto_sidebar',
			'porto_sidebar2',
			'porto_header_type',
			'porto_disable_sticky_sidebar',
			'porto_container',
			'porto_custom_css',
			'porto_custom_js_body',
		);

		/**
		 * Register Elementor Widgets
		 */
		public function __construct() {
			if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
				return;
			}

			// Include Partials
			// Mouse parallax
			include_once 'partials/addon.php';

			// register categories
			add_action(
				'elementor/elements/categories_registered',
				function( $self ) {
					$self->add_category(
						'porto-elements',
						array(
							'title'  => esc_html__( 'Porto', 'porto-functionality' ),
							'active' => true,
						)
					);
				}
			);

			// register custom section element
			add_action(
				'elementor/elements/elements_registered',
				function() {
					include_once dirname( PORTO_META_BOXES_PATH ) . '/elementor/tabs/porto-elementor-custom-tabs.php';

					include_once dirname( PORTO_META_BOXES_PATH ) . '/elementor/elements/porto_section.php';
					Elementor\Plugin::$instance->elements_manager->unregister_element_type( 'section' );
					Elementor\Plugin::$instance->elements_manager->register_element_type( new Porto_Elementor_Section() );

					include_once dirname( PORTO_META_BOXES_PATH ) . '/elementor/elements/porto_column.php';
					Elementor\Plugin::$instance->elements_manager->unregister_element_type( 'column' );
					Elementor\Plugin::$instance->elements_manager->register_element_type( new Porto_Elementor_Column() );
				}
			);

			// register porto widgets
			add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_elementor_widgets' ), 10, 1 );
			add_action( 'wp_enqueue_scripts', array( $this, 'load_elementor_widgets_js' ), 1008 );

			// register custom controls
			add_action( 'elementor/controls/controls_registered', array( $this, 'register_custom_control' ), 10, 1 );

			// register rest apis
			require_once( dirname( PORTO_META_BOXES_PATH ) . '/elementor/restapi/ajaxselect2.php' );

			if ( is_admin() ) {
				add_action(
					'elementor/editor/after_enqueue_scripts',
					function() {
						wp_enqueue_style( 'font-awesome', PORTO_CSS . '/font-awesome.min.css', false, PORTO_VERSION, 'all' );

						wp_enqueue_script( 'porto-elementor-admin', plugin_dir_url( __FILE__ ) . 'assets/admin.js', array( 'elementor-editor' ), PORTO_SHORTCODES_VERSION, true );
					}
				);

				// update default colors in color picker
				add_filter(
					'elementor/editor/localize_settings',
					function( $config ) {
						global $porto_settings;
						if ( ! get_option( 'elementor_disable_color_schemes', false ) || empty( $porto_settings ) || empty( $porto_settings['skin-color'] ) ) {
							return $config;
						}
						try {
							if ( isset( $config['schemes'] ) && ! empty( $config['schemes']['items']['color-picker'] ) ) {
								$default_colors             = $config['schemes']['items']['color-picker']['items'];
								$default_colors[1]['value'] = esc_js( $porto_settings['skin-color'] );
								if ( isset( $porto_settings['secondary-color'] ) ) {
									$default_colors[2]['value'] = esc_js( $porto_settings['secondary-color'] );
								}
								if ( isset( $porto_settings['tertiary-color'] ) ) {
									$default_colors[3]['value'] = esc_js( $porto_settings['tertiary-color'] );
								}
								if ( isset( $porto_settings['quaternary-color'] ) ) {
									$default_colors[4]['value'] = esc_js( $porto_settings['quaternary-color'] );
								}
								$default_colors[5]['value'] = ! empty( $porto_settings['body-font']['color'] ) ? esc_js( $porto_settings['body-font']['color'] ) : '#777';
								if ( ! empty( $porto_settings['h2-font']['color'] ) ) {
									$default_colors[6]['value'] = esc_js( $porto_settings['h2-font']['color'] );
								}
								if ( isset( $porto_settings['dark-color'] ) ) {
									$default_colors[7]['value'] = esc_js( $porto_settings['dark-color'] );
								}
								if ( isset( $porto_settings['light-color'] ) ) {
									$default_colors[8]['value'] = esc_js( $porto_settings['light-color'] );
								}
								$config['schemes']['items']['color-picker']['items'] = $default_colors;
							}
						} catch ( Exception $e ) {
						}
						return $config;
					}
				);

			}

			add_action(
				'elementor/documents/register_controls',
				function( $document ) {
					if ( ! $document instanceof Elementor\Core\DocumentTypes\PageBase && ! $document instanceof Elementor\Modules\Library\Documents\Page ) {
						return;
					}

					$document->start_controls_section(
						'porto_settings',
						array(
							'label' => __( 'Porto Settings', 'elementor' ),
							'tab'   => Elementor\Controls_Manager::TAB_SETTINGS,
						)
					);

					$document->add_control(
						'porto_settings_apply',
						array(
							'type'        => Elementor\Controls_Manager::BUTTON,
							'label'       => __( 'Update changes to page', 'porto-functionality' ),
							'text'        => __( 'Apply', 'porto-functionality' ),
							'button_type' => 'default porto-elementor-btn-reload elementor-button-success',
						)
					);

					if ( 'porto_builder' == $document->get_post()->post_type && $document->get_post()->ID ) {
						$builder_type = get_post_meta( $document->get_post()->ID, 'porto_builder_type', true );
						if ( 'header' == $builder_type ) {
							$document->add_control(
								'porto_header_type',
								array(
									'type'    => Elementor\Controls_Manager::SELECT,
									'label'   => __( 'Header Type', 'porto-functionality' ),
									'options' => array(
										''     => __( 'Default', 'porto-functionality' ),
										'side' => __( 'Side Header', 'porto-functionality' ),
									),
								)
							);
						} elseif ( 'product' == $builder_type ) {
							$document->add_control(
								'porto_disable_sticky_sidebar',
								array(
									'type'  => Elementor\Controls_Manager::SWITCHER,
									'label' => __( 'Disable Sticky Sidebar', 'porto-functionality' ),
								)
							);
						}
					}

					if ( 'porto_builder' == $document->get_post()->post_type ) {
						$document->add_control(
							'porto_container',
							array(
								'type'    => Elementor\Controls_Manager::SELECT,
								'label'   => __( 'Wrap as Container', 'porto-functionality' ),
								'options' => array(
									''      => __( 'Default', 'porto-functionality' ),
									'yes'   => __( 'Inner Container', 'porto-functionality' ),
									'fluid' => __( 'Fluid Container', 'porto-functionality' ),
								),
							)
						);
					} else {

						$document->add_control(
							'porto_default',
							array(
								'type'        => Elementor\Controls_Manager::SWITCHER,
								'label'       => __( 'Layout & Sidebar', 'porto-functionality' ),
								'description' => __( 'Use selected layout and sidebar options.', 'porto-functionality' ),
							)
						);

						$document->add_control(
							'porto_layout',
							array(
								'type'      => Elementor\Controls_Manager::SELECT,
								'label'     => __( 'Layout', 'porto-functionality' ),
								'options'   => porto_ct_layouts(),
								'condition' => array(
									'porto_default' => 'yes',
								),
							)
						);

						$document->add_control(
							'porto_sidebar',
							array(
								'type'        => Elementor\Controls_Manager::SELECT,
								'label'       => __( 'Sidebar', 'porto-functionality' ),
								'description' => __( '<strong>Note</strong>: You can create the sidebar under <strong>Appearance > Sidebars</strong>', 'porto-functionality' ),
								'options'     => porto_ct_sidebars(),
								'default'     => '',
								'condition'   => array(
									'porto_default' => 'yes',
									'porto_layout!' => array( 'widewidth', 'fullwidth' ),
								),
							)
						);

						$document->add_control(
							'porto_sidebar2',
							array(
								'type'        => Elementor\Controls_Manager::SELECT,
								'label'       => __( 'Sidebar 2', 'porto-functionality' ),
								'description' => __( '<strong>Note</strong>: You can create the sidebar under <strong>Appearance > Sidebars</strong>', 'porto-functionality' ),
								'options'     => porto_ct_sidebars(),
								'default'     => '',
								'condition'   => array(
									'porto_default' => 'yes',
									'porto_layout'  => array( 'wide-both-sidebar', 'both-sidebar' ),
								),
							)
						);
					}

					$document->add_control(
						'porto_custom_css',
						array(
							'type'  => Elementor\Controls_Manager::TEXTAREA,
							'rows'  => 20,
							'label' => __( 'Custom CSS', 'porto-functionality' ),
						)
					);

					$document->end_controls_section();

					// Porto Editor Area
					if ( 'porto_builder' == $document->get_post()->post_type ) {
						$document->start_controls_section(
							'porto_edit_area',
							array(
								'label' => esc_html__( 'Porto Editor Area', 'porto-functionality' ),
								'tab'   => Elementor\Controls_Manager::TAB_SETTINGS,
							)
						);

							$document->add_control(
								'porto_edit_area_width',
								array(
									'label'       => esc_html__( 'Edit Area Width', 'porto-functionality' ),
									'description' => esc_html__( "Control edit area width for this template's usage.", 'porto-functionality' ),
									'type'        => Elementor\Controls_Manager::SLIDER,
									'size_units'  => array(
										'px',
										'%',
										'vw',
									),
									'range'       => array(
										'px' => array(
											'step' => 1,
											'min'  => 100,
											'max'  => 500,
										),
										'%'  => array(
											'step' => 1,
											'min'  => 0,
											'max'  => 100,
										),
										'vw' => array(
											'step' => 1,
											'min'  => 0,
											'max'  => 100,
										),
									),
									'separator'   => 'after',
								)
							);

						$document->end_controls_section();
					}

					if ( 'porto_builder' == $document->get_post()->post_type && $document->get_post()->ID && 'popup' == get_post_meta( $document->get_post()->ID, 'porto_builder_type', true ) ) {

						$document->start_controls_section(
							'porto_popup_settings',
							array(
								'label' => esc_html__( 'Porto Popup Settings', 'porto-functionality' ),
								'tab'   => Elementor\Controls_Manager::TAB_SETTINGS,
							)
						);
						$document->add_control(
							'popup_width',
							array(
								'type'    => Elementor\Controls_Manager::NUMBER,
								'label'   => esc_html__( 'Popup Width (px)', 'porto-functionality' ),
								'default' => 740,
							)
						);

						$document->add_control(
							'popup_animation',
							array(
								'type'    => Elementor\Controls_Manager::SELECT,
								'label'   => esc_html__( 'Popup Animation', 'porto-functionality' ),
								'options' => array(
									'mfp-fade'       => __( 'Fade', 'porto-functionality' ),
									'my-mfp-zoom-in' => __( 'Zoom in', 'porto-functionality' ),
								),
								'default' => 'mfp-fade',
							)
						);

						$document->add_control(
							'load_duration',
							array(
								'type'    => Elementor\Controls_Manager::NUMBER,
								'label'   => esc_html__( 'Load Duration (ms)', 'porto-functionality' ),
								'default' => 4000,
							)
						);

						$document->add_control(
							'popup_pos_horizontal',
							array(
								'label'   => esc_html__( 'Horizontal Offset (%)', 'porto-functionality' ),
								'type'    => Elementor\Controls_Manager::NUMBER,
								'default' => 50,
							)
						);
						$document->add_control(
							'popup_pos_vertical',
							array(
								'label'   => esc_html__( 'Vertical Offset (%)', 'porto-functionality' ),
								'type'    => Elementor\Controls_Manager::NUMBER,
								'default' => 50,
							)
						);
						$document->end_controls_section();
					}
				}
			);

			if ( wp_doing_ajax() ) {
				add_action(
					'elementor/document/before_save',
					function( $self, $data ) {
						if ( empty( $data['settings'] ) || empty( $_REQUEST['editor_post_id'] ) ) {
							return;
						}

						$is_imported = false;
						$post_id     = absint( $_REQUEST['editor_post_id'] );
						foreach ( $this->porto_metas as $meta ) {
							if ( ! empty( $data['settings'][ $meta ] ) ) {
								$is_imported = true;
								$val         = porto_strip_script_tags( $data['settings'][ $meta ] );
								if ( 'porto_default' == $meta && 'yes' == $val ) {
									$val = 'default';
								} elseif ( 'porto_disable_sticky_sidebar' == $meta && 'yes' == $val ) {
									$val = 'disable_sticky_sidebar';
								}

								update_post_meta( $post_id, str_replace( 'porto_', '', $meta ), wp_slash( $val ) );
							} else {
								delete_post_meta( $post_id, str_replace( 'porto_', '', $meta ) );
							}
						}

						// Popup
						if ( isset( $post_id ) && 'popup' == get_post_meta( $post_id, 'porto_builder_type', true ) ) {
							$popup_options                  = array();
							$popup_options['width']         = wp_slash( '' != $data['settings']['popup_width'] ? $data['settings']['popup_width'] : 740 );
							$popup_options['animation']     = ! empty( $data['settings']['popup_animation'] ) ? wp_slash( $data['settings']['popup_animation'] ) : 'mfp-fade';
							$popup_options['load_duration'] = wp_slash( '' != $data['settings']['load_duration'] ? $data['settings']['load_duration'] : 4000 );

							$popup_options['horizontal'] = wp_slash( isset( $data['settings']['popup_pos_horizontal'] ) ? $data['settings']['popup_pos_horizontal'] : 50 );
							$popup_options['vertical']   = wp_slash( isset( $data['settings']['popup_pos_vertical'] ) ? $data['settings']['popup_pos_vertical'] : 50 );

							if ( empty( $popup_options ) ) {
								delete_post_meta( $post_id, 'popup_options' );
							} else {
								update_post_meta( $post_id, 'popup_options', wp_slash( $popup_options ) );
							}
						}
					},
					10,
					2
				);
				add_action(
					'elementor/document/after_save',
					function( $self, $data ) {
						$post_id = absint( $_REQUEST['editor_post_id'] );

						// save used blocks
						if ( ! empty( $data['elements'] ) ) {
							// check breadcrumbs element
							$elements_str = json_encode( $data['elements'] );
							preg_match( '/"breadcrumbs_type":"([^"]*)"/', $elements_str, $matches );
							if ( ! empty( $matches ) && isset( $matches[1] ) ) {
								update_post_meta( $post_id, 'porto_page_header_shortcode_type', (int) $matches[1] );
							} else {
								delete_post_meta( $post_id, 'porto_page_header_shortcode_type' );
							}
							// end check breadcrumbs element

							$block_slugs = $this->get_elementor_object_by_id( $data['elements'] );
							$used_blocks = get_theme_mod( '_used_blocks', array() );
							if ( ! isset( $used_blocks['el'] ) ) {
								$used_blocks['el'] = array();
							}
							if ( ! isset( $used_blocks['el']['post_c'] ) ) {
								$used_blocks['el']['post_c'] = array();
							}
							if ( ! empty( $block_slugs ) ) {
								$used_blocks['el']['post_c'][ $post_id ] = array_map( 'intval', $block_slugs );
							} else {
								unset( $used_blocks['el']['post_c'][ $post_id ] );
							}
							set_theme_mod( '_used_blocks', $used_blocks );
						}

						if ( current_user_can( 'unfiltered_html' ) || empty( $data['settings'] ) || empty( $_REQUEST['editor_post_id'] ) ) {
							return;
						}

						if ( ! empty( $data['settings']['porto_custom_css'] ) ) {
							$elementor_settings = get_post_meta( $post_id, '_elementor_page_settings', true );
							if ( is_array( $elementor_settings ) ) {
								$elementor_settings['porto_custom_css'] = porto_strip_script_tags( get_post_meta( $post_id, 'custom_css', true ) );
								update_post_meta( $post_id, '_elementor_page_settings', $elementor_settings );
							}
						}
					},
					10,
					2
				);
			}

			add_filter(
				'elementor/document/config',
				function( $config, $post_id ) {
					if ( empty( $config ) ) {
						$config = array();
					}
					if ( ! isset( $config['settings'] ) ) {
						$config['settings'] = array();
					}
					if ( ! isset( $config['settings']['settings'] ) ) {
						$config['settings']['settings'] = array();
					}
					foreach ( $this->porto_metas as $meta ) {
						$val = get_post_meta( $post_id, str_replace( 'porto_', '', $meta ), true );
						if ( 'porto_default' == $meta && 'default' == $val ) {
							$val = 'yes';
						} elseif ( 'porto_disable_sticky_sidebar' == $meta && 'disable_sticky_sidebar' == $val ) {
							$val = 'yes';
						}
						$config['settings']['settings'][ $meta ] = $val;
					}
					return $config;
				},
				10,
				2
			);

			add_filter( 'elementor/icons_manager/additional_tabs', array( $this, 'add_porto_icons' ), 10, 1 );
		}

		// Register Elementor widgets
		public function register_elementor_widgets( $self ) {
			include_once dirname( PORTO_META_BOXES_PATH ) . '/elementor/tabs/porto-elementor-custom-tabs.php';
			$self->unregister_widget_type( 'common' );
			include_once dirname( PORTO_META_BOXES_PATH ) . '/elementor/widgets/common.php';
			$self->register_widget_type( new Porto_Elementor_Common_Widget( array(), array( 'widget_name' => 'common' ) ) );
			
			foreach ( $this->widgets as $widget ) {
				include dirname( PORTO_META_BOXES_PATH ) . '/elementor/widgets/' . $widget . '.php';
				$class_name = 'Porto_Elementor_' . ucfirst( $widget ) . '_Widget';
				$self->register_widget_type( new $class_name( array(), array( 'widget_name' => $class_name ) ) );
			}
			if ( class_exists( 'Woocommerce' ) ) {
				foreach ( $this->woo_widgets as $widget ) {
					include dirname( PORTO_META_BOXES_PATH ) . '/elementor/widgets/' . $widget . '.php';
					$class_name = 'Porto_Elementor_' . ucfirst( $widget ) . '_Widget';
					$self->register_widget_type( new $class_name( array(), array( 'widget_name' => $class_name ) ) );
				}
			}
		}

		public function load_elementor_widgets_js() {
			if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) {

				wp_register_script( 'porto-elementor-widgets-js', plugin_dir_url( __FILE__ ) . 'assets/elementor.js', array( 'jquery' ), PORTO_SHORTCODES_VERSION, true );

				$masonry_layouts  = porto_sh_commons( 'masonry_layouts' );
				$creative_layouts = array();
				for ( $index = 1; $index <= count( $masonry_layouts ); $index++ ) {
					$layout = porto_creative_grid_layout( '' . $index );
					if ( is_array( $layout ) ) {
						$creative_layouts[ $index ] = array();
						foreach ( $layout as $pl ) {
							$creative_layouts[ $index ][] = esc_js( 'grid-col-' . $pl['width'] . ' grid-col-md-' . $pl['width_md'] . ( isset( $pl['width_lg'] ) ? ' grid-col-lg-' . $pl['width_lg'] : '' ) . ( isset( $pl['height'] ) ? ' grid-height-' . $pl['height'] : '' ) );
						}
					}
				}

				wp_enqueue_script( 'skrollr' );

				wp_enqueue_script( 'porto-elementor-widgets-js' );

				$admin_vars = array(
					'creative_layouts' => $creative_layouts,
					'gmt_offset'       => get_option( 'gmt_offset' ),
					'js_assets_url' => defined( 'PORTO_VERSION' ) ? PORTO_JS : '',
				);
				global $porto_settings;
				if ( ! empty( $porto_settings ) ) {
					$admin_vars['container_width'] = (int) $porto_settings['container-width'];
					$admin_vars['grid_spacing']    = (int) $porto_settings['grid-gutter-width'];
				}
				wp_localize_script(
					'porto-elementor-widgets-js',
					'porto_elementor_vars',
					$admin_vars
				);
			}
		}

		public function register_custom_control( $self ) {
			$controls = array( 'image_choose', 'porto_ajaxselect2' );

			foreach ( $controls as $control ) {
				$file_name = str_replace( 'porto_', '', $control );
				include_once dirname( PORTO_META_BOXES_PATH ) . '/elementor/controls/control-' . $file_name . '.php';
				$class_name = 'Porto_Control_' . ucfirst( $file_name );
				$self->register_control( $control, new $class_name( array(), array( 'control_name' => $class_name ) ) );
			}
		}

		public function add_porto_icons( $icons ) {
			$icons['porto-icons'] = array(
				'name'          => 'porto-icons',
				'label'         => __( 'Porto Icons', 'porto-functionality' ),
				'prefix'        => 'porto-icon-',
				'displayPrefix' => ' ',
				'labelIcon'     => 'porto-icon-country',
				'fetchJson'     => plugin_dir_url( __FILE__ ) . 'assets/porto-icons.js',
				'ver'           => PORTO_SHORTCODES_VERSION,
				'native'        => false,
			);

			$icons['simple-line-icons'] = array(
				'name'          => 'simple-line-icons',
				'label'         => __( 'Simple Line Icons', 'porto-functionality' ),
				'prefix'        => 'Simple-Line-Icons-',
				'displayPrefix' => ' ',
				'labelIcon'     => 'Simple-Line-Icons-flag',
				'fetchJson'     => plugin_dir_url( __FILE__ ) . 'assets/simple-line-icons.js',
				'ver'           => PORTO_SHORTCODES_VERSION,
				'native'        => false,
			);
			return $icons;
		}

		/**
		 * get block ids in shortcode and block widgets from the elementor data
		 */
		private function get_elementor_object_by_id( $objects ) {
			$result = array();

			$block_slugs = array();
			foreach ( $objects as $object ) {
				if ( ! empty( $object['elements'] ) ) {
					$result = array_merge( $result, $this->get_elementor_object_by_id( $object['elements'] ) );
				} else {
					if ( 'shortcode' == $object['widgetType'] && isset( $object['settings'] ) && ! empty( $object['settings']['shortcode'] ) && preg_match_all( '/\[porto_block\s[^]]*(id|name)="([^"]*)"/', $object['settings']['shortcode'], $matches ) && ! empty( $matches[2] ) ) {
						$block_slugs = array_merge( $block_slugs, $matches[2] );
					} elseif ( 'wp-widget-block-widget' == $object['widgetType'] && isset( $object['settings'] ) && isset( $object['settings']['wp'] ) && ! empty( $object['settings']['wp']['name'] ) ) {
						$block_slugs = array_merge( $block_slugs, array_map( 'trim', explode( ',', $object['settings']['wp']['name'] ) ) );
					}
				}
			}
			if ( ! empty( $block_slugs ) ) {
				$block_slugs = array_unique( $block_slugs );
				global $wpdb;
				foreach ( $block_slugs as $s ) {
					$where   = is_numeric( $s ) ? 'ID' : 'post_name';
					$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = 'porto_builder' AND $where = %s", sanitize_text_field( $s ) ) );
					if ( $post_id && get_post_meta( $post_id, '_elementor_edit_mode', true ) && get_post_meta( $post_id, '_elementor_data', true ) ) {
						$result[] = (int) $post_id;
					}
				}
			}
			return array_unique( $result );
		}
	}
endif;

new Porto_Elementor_Init;
