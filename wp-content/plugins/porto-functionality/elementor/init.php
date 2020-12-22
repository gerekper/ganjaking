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

			// register custom section element
			add_action(
				'elementor/elements/elements_registered',
				function() {
					include_once dirname( PORTO_META_BOXES_PATH ) . '/elementor/elements/porto_section.php';
					Elementor\Plugin::$instance->elements_manager->unregister_element_type( 'section' );
					Elementor\Plugin::$instance->elements_manager->register_element_type( new Porto_Elementor_Section() );
				}
			);

			// register porto widgets
			add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_elementor_widgets' ), 10, 1 );
			add_action( 'wp_enqueue_scripts', array( $this, 'load_elementor_widgets_js' ), 1008 );

			// register custom controls
			add_action( 'elementor/controls/controls_registered', array( $this, 'register_custom_control' ), 10, 1 );

			if ( is_admin() ) {
				add_action(
					'sidebar_admin_setup',
					function() {
						if ( ! wp_doing_ajax() || ! isset( $_POST['id_base'] ) || ! isset( $_POST['widget-id'] ) ) {
							return;
						}
						$id_base    = wp_unslash( $_POST['id_base'] );
						$widget_id  = wp_unslash( $_POST['widget-id'] );
						$settings   = isset( $_POST[ 'widget-' . $id_base ] ) && is_array( $_POST[ 'widget-' . $id_base ] ) ? $_POST[ 'widget-' . $id_base ] : false;
						$sidebar_id = $_POST['sidebar'];
						$sidebars   = get_option( 'sidebars_widgets' );
						$sidebar    = isset( $sidebars[ $sidebar_id ] ) ? $sidebars[ $sidebar_id ] : array();
						if ( 'block-widget' != $id_base || ! $settings ) {
							return;
						}

						$block_widgets      = get_option( 'widget_block-widget', array() );
						$elementor_sidebars = get_theme_mod( 'elementor_sidebars', array() );
						$block_slugs        = array();

						global $wp_registered_widgets;
						if ( isset( $_POST['delete_widget'] ) && $_POST['delete_widget'] && isset( $wp_registered_widgets[ $widget_id ] ) && isset( $elementor_sidebars[ $sidebar_id ] ) && is_array( $elementor_sidebars[ $sidebar_id ] ) ) {
							unset( $sidebar[ $widget_id ] );
						} else {
							foreach ( $settings as $widget_number => $widget_settings ) {
								if ( is_array( $widget_settings ) ) {
									foreach ( $widget_settings as $key => $val ) {
										if ( 'name' == $key ) {
											$block_slugs[ $widget_id ] = $val;
											break;
										}
									}
								}
							}
						}

						$elementor_sidebars[ $sidebar_id ] = array();

						foreach ( $sidebar as $widget ) {
							$widget_type = trim( substr( $widget, 0, strrpos( $widget, '-' ) ) );
							$widget_id   = str_replace( 'block-widget-', '', $widget );
							if ( 'block-widget' == $widget_type && ! empty( $block_widgets[ $widget_id ] ) && ! empty( $block_widgets[ $widget_id ]['name'] ) && empty( $block_slugs[ $widget ] ) ) {
								$block_slugs[ $widget ] = $block_widgets[ $widget_id ]['name'];
							}
						}

						if ( ! empty( $block_slugs ) ) {
							foreach ( $block_slugs as $widget_id => $slug ) {
								$blocks = new WP_Query(
									array(
										'name'      => sanitize_text_field( $slug ),
										'post_type' => 'block',
									)
								);
								if ( $blocks->have_posts() ) {
									$blocks->the_post();
									if ( get_post_meta( get_the_ID(), '_elementor_edit_mode', true ) && get_post_meta( get_the_ID(), '_elementor_data', true ) ) {
										$elementor_sidebars[ sanitize_text_field( $sidebar_id ) ][] = sanitize_text_field( $widget_id );
									}
									wp_reset_postdata();
								}
							}
						}

						if ( empty( $elementor_sidebars[ $sidebar_id ] ) ) {
							unset( $elementor_sidebars[ $sidebar_id ] );
						}

						set_theme_mod( 'elementor_sidebars', $elementor_sidebars );
					}
				);

				add_action( 'save_post', array( $this, 'update_flag_use_elementor_blocks' ), 10, 2 );
				add_action( 'edit_term', array( $this, 'update_term_meta_fields' ), 10, 3 );
				add_action( 'delete_post', array( $this, 'delete_post_meta_fields' ) );
				add_action( 'delete_term', array( $this, 'delete_term_meta_fields' ), 10, 3 );

				add_action(
					'redux/options/porto_settings/saved',
					function( $options, $changed ) {
						if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
							return;
						}

						$html_blocks   = array( 'top', 'banner', 'content-top', 'content-inner-top', 'content-inner-bottom', 'content-bottom', 'bottom' );
						$block_changed = false;
						foreach ( $html_blocks as $b ) {
							if ( isset( $changed[ 'html-' . $b ] ) ) {
								$block_changed = true;
								break;
							}
						}

						$blog_blocks        = array( 'blog-content_top', 'blog-content_inner_top', 'blog-content_inner_bottom', 'blog-content_bottom' );
						$blog_block_changed = false;
						foreach ( $blog_blocks as $b ) {
							if ( isset( $changed[ $b ] ) ) {
								$blog_block_changed = true;
								break;
							}
						}

						if ( ! $block_changed && ! $blog_block_changed && ! isset( $changed['product-content_bottom'] ) && ! isset( $changed['product-tab-block'] ) && ! isset( $changed['product-single-content-layout'] ) && ! isset( $changed['product-single-content-builder'] ) && ! isset( $changed['hb'] ) ) {
							return;
						}

						$old_flag = get_theme_mod( 'elementor_edited', false );
						if ( 'header_builder_p' == $options['header-type-select'] && ! empty( $options['hb'] ) ) {
							global $wpdb;
							if ( get_post_meta( (int) $options['hb'], '_elementor_edit_mode', true ) ) {
								set_theme_mod( 'elementor_edited', true );
								$block_changed = false;
							}
						}

						if ( $block_changed ) {
							$block_slugs      = array();
							foreach ( $html_blocks as $b ) {
								if ( ! empty( $options[ 'html-' . $b ] ) && preg_match( '/\[porto_block\s[^]]*(id|name)="([^"]*)"/', $options[ 'html-' . $b ], $matches ) && isset( $matches[2] ) && $matches[2] ) {
									$block_slugs[] = trim( $matches[2] );
								}
							}

							if ( ! empty( $block_slugs ) ) {
								global $wpdb;
								foreach ( $block_slugs as $s ) {
									$where   = is_numeric( $s ) ? 'ID' : 'post_name';
									$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = 'block' AND $where = %s", sanitize_text_field( $s ) ) );
									if ( $post_id && get_post_meta( $post_id, '_elementor_edit_mode', true ) && get_post_meta( $post_id, '_elementor_data', true ) ) {
										$old_flag = true;
										break;
									}
								}
							}

							set_theme_mod( 'elementor_edited', $old_flag );
						}

						if ( $blog_block_changed ) {
							$elementor_edited = false;
							$block_slugs      = array();
							foreach ( $blog_blocks as $b ) {
								if ( ! empty( $options[ $b ] ) ) {
									$arr = explode( ',', $options[ $b ] );
									foreach ( $arr as $a ) {
										$a = trim( $a );
										if ( $a && ! in_array( $a, $block_slugs ) ) {
											$block_slugs[] = $a;
										}
									}
								}
							}

							if ( ! empty( $block_slugs ) ) {
								global $wpdb;
								foreach ( $block_slugs as $s ) {
									$where   = is_numeric( $s ) ? 'ID' : 'post_name';
									$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = 'block' AND $where = %s", sanitize_text_field( $s ) ) );
									if ( $post_id && get_post_meta( $post_id, '_elementor_edit_mode', true ) && get_post_meta( $post_id, '_elementor_data', true ) ) {
										$elementor_edited = true;
										break;
									}
								}
							}
							set_theme_mod( 'elementor_blog_edited', $elementor_edited );
						}

						$types = get_theme_mod( 'elementor_blocks_post_types', array() );
						foreach ( $types as $index => $type ) {
							if ( 'product' == $type ) {
								unset( $types[ $index ] );
								break;
							}
						}
						if ( 'builder' == $options['product-single-content-layout'] && ! empty( $options['product-single-content-builder'] ) ) {
							global $wpdb;
							$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = 'product_layout' AND post_name = %s", $options['product-single-content-builder'] ) );
							if ( $post_id && get_post_meta( $post_id, '_elementor_edit_mode', true ) ) {
								$types[] = 'product';
							}
						}

						if ( ! in_array( 'product', $types ) ) {
							$block_slugs = array();
							if ( ! empty( $options['product-content_bottom'] ) ) {
								$block_slugs = array_merge( $block_slugs, explode( ',', $options['product-content_bottom'] ) );
							}
							if ( ! empty( $options['product-tab-block'] ) ) {
								$block_slugs = array_merge( $block_slugs, explode( ',', $options['product-tab-block'] ) );
							}
							foreach ( $block_slugs as $slug ) {
								$blocks = new WP_Query(
									array(
										'name'      => sanitize_text_field( trim( $slug ) ),
										'post_type' => 'block',
									)
								);

								if ( $blocks->have_posts() ) {
									$blocks->the_post();
									if ( get_post_meta( get_the_ID(), '_elementor_edit_mode', true ) && get_post_meta( get_the_ID(), '_elementor_data', true ) ) {
										$types[] = 'product';
										wp_reset_postdata();
										break;
									}
									wp_reset_postdata();
								}
							}
						}
						set_theme_mod( 'elementor_blocks_post_types', $types );
					},
					11,
					2
				);

				add_action(
					'elementor/editor/after_enqueue_scripts',
					function() {
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
						$terms = wp_get_post_terms( $document->get_post()->ID, 'porto_builder_type' );
						if ( ! empty( $terms ) && 'header' == $terms[0]->name ) {
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
						}
					}

					if ( 'block' == $document->get_post()->post_type || 'product_layout' == $document->get_post()->post_type || 'porto_builder' == $document->get_post()->post_type ) {
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
								'default'   => 'right-sidebar',
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
								}

								update_post_meta( $post_id, str_replace( 'porto_', '', $meta ), wp_slash( $val ) );
							} else {
								delete_post_meta( $post_id, str_replace( 'porto_', '', $meta ) );
							}
						}
					},
					10,
					2
				);
				add_action(
					'elementor/document/after_save',
					function( $self, $data ) {
						if ( current_user_can( 'unfiltered_html' ) || empty( $data['settings'] ) || empty( $_REQUEST['editor_post_id'] ) ) {
							return;
						}
						$post_id = absint( $_REQUEST['editor_post_id'] );
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
				function( $config = array(), $post_id ) {
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

		public function update_term_meta_fields( $term_id, $tt_id, $taxonomy ) {
			$this->update_flag_use_elementor_blocks( $term_id, false, true, $taxonomy );
		}

		public function update_flag_use_elementor_blocks( $post_id, $post = false, $is_term = false, $taxonomy = false ) {
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			if ( false !== $post && defined( 'ELEMENTOR_VERSION' ) && 'product_layout' == $post->post_type ) {
				$types = get_theme_mod( 'elementor_blocks_post_types', array() );
				global $porto_settings;
				if ( ! in_array( 'product', $types ) && get_post_meta( $post_id, '_elementor_edit_mode', true ) && 'builder' == $porto_settings['product-single-content-layout'] && isset( $porto_settings['product-single-content-builder'] ) && $post->post_name == $porto_settings['product-single-content-builder'] ) {
					$types[] = 'product';
					set_theme_mod( 'elementor_blocks_post_types', $types );
				}
			}

			if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) {
				return;
			}

			$block_slugs = array();
			if ( isset( $_POST['banner_type'] ) && 'banner_block' == $_POST['banner_type'] && ! empty( $_POST['banner_block'] ) ) {
				$block_slugs[] = trim( $_POST['banner_block'] );
			}
			$blocks_fields = array( 'content_top', 'content_inner_top', 'content_inner_bottom', 'content_bottom', 'product_custom_block' );
			foreach ( $blocks_fields as $field ) {
				if ( ! empty( $_POST[ $field ] ) ) {
					$arr = explode( ',', $_POST[ $field ] );
					if ( ! empty( $arr ) ) {
						foreach ( $arr as $a ) {
							$a = trim( $a );
							if ( $a ) {
								$block_slugs[] = $a;
							}
						}
					}
				}
			}

			if ( ! empty( $block_slugs ) ) {
				foreach ( $block_slugs as $slug ) {
					$blocks = new WP_Query(
						array(
							'name'      => sanitize_text_field( $slug ),
							'post_type' => 'block',
						)
					);

					if ( $blocks->have_posts() ) {
						$blocks->the_post();
						if ( get_post_meta( get_the_ID(), '_elementor_edit_mode', true ) && get_post_meta( get_the_ID(), '_elementor_data', true ) ) {
							wp_reset_postdata();
							if ( $is_term ) {
								update_metadata( $taxonomy, $post_id, '_porto_use_elementor_blocks', true );
							} else {
								update_post_meta( $post_id, '_porto_use_elementor_blocks', true );
							}
							return;
						}
						wp_reset_postdata();
					}
				}
			}
			if ( $is_term ) {
				delete_metadata( $taxonomy, $post_id, '_porto_use_elementor_blocks' );
			} else {
				delete_post_meta( $post_id, '_porto_use_elementor_blocks' );
			}
		}

		public function delete_post_meta_fields( $post_id ) {
			delete_post_meta( $post_id, '_porto_use_elementor_blocks' );
		}

		public function delete_term_meta_fields( $term_id, $tt_id, $taxonomy ) {
			delete_metadata( $taxonomy, $term_id, '_porto_use_elementor_blocks' );
		}

		// Register Elementor widgets
		public function register_elementor_widgets( $self ) {
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
			$controls = array( 'image_choose' );

			foreach ( $controls as $control ) {
				include_once dirname( PORTO_META_BOXES_PATH ) . '/elementor/controls/control-' . $control . '.php';
				$class_name = 'Porto_Control_' . ucfirst( $control );
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
	}
endif;

new Porto_Elementor_Init;
