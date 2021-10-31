<?php
/**
 * Porto Page Layouts
 *
 * @since 6.2.0
 */
if ( ! class_exists( 'Porto_Page_Layouts' ) ) :
	class Porto_Page_Layouts {

		private $options = array();
		private $template_list;
		private $condition;


		public function __construct() {
			add_action( 'admin_menu', array( $this, 'admin_menu' ), 10 );
			add_action( 'wp_ajax_porto_page_layouts_display_condition', array( $this, 'builder_condition_template' ) );
			add_action( 'wp_ajax_porto_page_layouts_search_posts', array( $this, 'ajax_search' ) );
			add_action( 'wp_ajax_porto_page_layouts_remove_condition', array( $this, 'remove_condition' ) );
			add_action( 'wp_ajax_porto_page_layouts_open_page', array( $this, 'get_page_url' ) );
			add_action( 'wp_ajax_porto_page_layouts_save_condition', array( $this, 'save_condition' ) );
			if ( defined( 'PORTO_BUILDERS_PATH' ) ) {
				require_once PORTO_BUILDERS_PATH . 'lib/class-condition.php';
				$this->condition = new Porto_Builder_Condition( true );
			}
			if ( ! current_user_can( 'administrator' ) || ! isset( $_GET['page'] ) || 'porto-page-layouts' != $_GET['page'] ) {
				return;
			}
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ), 1001 );
		}

		public function enqueue() {
			if ( defined( 'PORTO_SHORTCODES_URL' ) ) {
				wp_enqueue_style( 'porto-builder-condition', str_replace( '/shortcodes', '/builders', PORTO_SHORTCODES_URL ) . 'assets/condition.css', array(), PORTO_SHORTCODES_VERSION );
			}
			wp_enqueue_script( 'porto-page-layouts', PORTO_JS . '/admin/page-layouts.js', array( 'porto-admin' ), PORTO_VERSION, true );
			wp_localize_script(
				'porto-admin',
				'porto_page_layouts',
				apply_filters(
					'porto_page_layouts',
					array(
						'nonce' => wp_create_nonce( 'porto-page-layouts-nonce' ),
					)
				)
			);
		}

		public function get_page_url() {
			check_ajax_referer( 'porto-page-layouts-nonce', '_nonce' );
			if ( ! empty( $_REQUEST['builder_id'] ) ) {
				$post_id = (int) $_REQUEST['builder_id'];
				$link    = get_edit_post_link( $post_id );
				if ( defined( 'ELEMENTOR_VERSION' ) && get_post_meta( $post_id, '_elementor_edit_mode', true ) ) {
					$link = add_query_arg( 'action', 'elementor', $link );
				}
				wp_send_json( array( 'link' => str_replace( '&amp;', '&', $link ) ) );
			}
			die();
		}

		public function builder_condition_template() {
			check_ajax_referer( 'porto-page-layouts-nonce', '_nonce' );
			if ( ! empty( $_REQUEST['builder_id'] ) ) {
				$post_id      = (int) $_REQUEST['builder_id'];
				$builder_type = get_post_meta( $post_id, PortoBuilders::BUILDER_TAXONOMY_SLUG, true );
				$is_page_layout = true;
				if ( defined( 'PORTO_BUILDERS_PATH' ) ) {
					include_once PORTO_BUILDERS_PATH . 'views/condition_template.php';
				}
			}
			die();
		}

		public function ajax_search() {
			check_ajax_referer( 'porto-page-layouts-nonce', '_nonce' );
			if ( ! empty( $this->condition ) ) {
				$this->condition->ajax_search( true );
			}
			die();
		}

		public function remove_condition() {
			check_ajax_referer( 'porto-page-layouts-nonce', '_nonce' );
			if ( ! empty( $_REQUEST['builder_id'] ) ) {
				$post_id      = (int) $_REQUEST['builder_id'];
				$builder_type = get_post_meta( $post_id, PortoBuilders::BUILDER_TAXONOMY_SLUG, true );
				if ( ! empty( $_POST['data_part'] ) && 'block' == $builder_type ) {
					$builder_type .= '_' . $_POST['data_part'];
				}
				/* remove old conditions */
				$old_conditions = get_post_meta( $post_id, '_porto_builder_conditions', true );
				if ( ! empty( $old_conditions ) ) {
					$builder_conditions = get_theme_mod( 'builder_conditions', array() );
					if ( ! isset( $builder_conditions[ $builder_type ] ) ) {
						$builder_conditions[ $builder_type ] = array();
					}
					foreach ( $old_conditions as $index => $condition ) {
						if ( ! is_array( $condition ) ) {
							continue;
						}
						if ( empty( $condition[0] ) ) {
							if ( isset( $builder_conditions[ $builder_type ]['all'] ) && $post_id === (int) $builder_conditions[ $builder_type ]['all'] ) {
								unset( $builder_conditions[ $builder_type ]['all'] );
							}
						} else {
							$type = $condition[0];
							if ( ! empty( $condition[2] ) ) {
								if ( ! empty( $condition[1] ) ) {
									if ( 0 === strpos( $condition[1], 'taxonomy/' ) ) {
										$p_type = 'taxonomy';
									} else {
										$p_type = 'post';
									}

									if ( 'post' == $p_type && $post_id === (int) get_post_meta( (int) $condition[2], '_porto_builder_' . $builder_type, true ) ) {
										delete_post_meta( (int) $condition[2], '_porto_builder_' . $builder_type );
									} elseif ( 'taxonomy' == $p_type ) {
										if ( 'single' == $type ) {
											$key = '_porto_builder_single_' . $builder_type;
										} else {
											$key = '_porto_builder_' . $builder_type;
										}

										if ( $post_id === (int) get_term_meta( (int) $condition[2], $key, true ) ) {
											delete_term_meta( (int) $condition[2], $key );
										}
									}
								}
							} elseif ( ! empty( $condition[1] ) ) {
								$o_type = $condition[1];
								if ( 'single' == $type ) {
									$o_type = 'single/' . $o_type;
								}
								if ( isset( $builder_conditions[ $builder_type ][ $o_type ] ) && $post_id === (int) $builder_conditions[ $builder_type ][ $o_type ] ) {
									unset( $builder_conditions[ $builder_type ][ $o_type ] );
								}
							} else {
								if ( isset( $builder_conditions[ $builder_type ][ $type ] ) && $post_id === (int) $builder_conditions[ $builder_type ][ $type ] ) {
									unset( $builder_conditions[ $builder_type ][ $type ] );
								}
							}
						}
					}
					set_theme_mod( 'builder_conditions', $builder_conditions );
				}

				if ( false !== strpos( $builder_type, 'block' ) ) {
					delete_post_meta( $post_id, '_porto_block_pos' );
				}
				delete_post_meta( $post_id, '_porto_builder_conditions' );
			}
			wp_send_json_success();
			die();
		}

		public function save_condition() {
			check_ajax_referer( 'porto-page-layouts-nonce' );
			if ( ! empty( $this->condition ) ) {
				if ( ! empty( $_POST['post_id'] ) ) {
					$this->condition->save_condition( true, (int) $_POST['post_id'] );
				}
			}
			die();
		}

		public function admin_menu() {
			add_submenu_page( 'porto', __( 'Page Layouts', 'porto' ), __( 'Page Layouts', 'porto' ), 'administrator', 'porto-page-layouts', array( $this, 'page_layouts_page' ) );
		}

		public function page_layouts_page() {
			if ( ! current_user_can( 'administrator' ) || ! isset( $_GET['page'] ) || 'porto-page-layouts' != $_GET['page'] ) {
				return;
			}
			$this->get_template_list();
			$this->options = array(
				'header'             => array(
					'note' => array(
						'control' => 'heading',
						'label'   => sprintf( esc_html__( 'Select one of existing headers or %1$screate a new header%2$s.', 'porto' ), '<a href="' . esc_url( admin_url( 'edit.php?post_type=porto_builder&porto_builder_type=header' ) ) . '" target="_blank">', '</a>' ),
					),
					'builder-blocks' => array(
						'control'     => 'select',
						'label'       => esc_html__( 'Select Header', 'porto' ),
						'choices'     => $this->template_list['header'],
					),
				),
				'footer'             => array(
					'note' => array(
						'control' => 'heading',
						'label'   => sprintf( esc_html__( 'Select one of existing footers or %1$screate a new footer%2$s.', 'porto' ), '<a href="' . esc_url( admin_url( 'edit.php?post_type=porto_builder&porto_builder_type=footer' ) ) . '" target="_blank">', '</a>' ),
					),
					'builder-blocks' => array(
						'control'     => 'select',
						'label'       => esc_html__( 'Select Footer', 'porto' ),
						'choices'     => $this->template_list['footer'],
					),
				),
				'shop'             => array(
					'note' => array(
						'control' => 'heading',
						'label'   => sprintf( esc_html__( 'Select one of existing product archive templates or %1$screate a new shop%2$s.', 'porto' ), '<a href="' . esc_url( admin_url( 'edit.php?post_type=porto_builder&porto_builder_type=shop' ) ) . '" target="_blank">', '</a>' ),
					),
					'builder-blocks' => array(
						'control'     => 'select',
						'label'       => esc_html__( 'Select Product Archive Template', 'porto' ),
						'choices'     => $this->template_list['shop'],
					),
				),
				'product'             => array(
					'note' => array(
						'control' => 'heading',
						'label'   => sprintf( esc_html__( 'Select one of existing single product templates or %1$screate a new single product%2$s.', 'porto' ), '<a href="' . esc_url( admin_url( 'edit.php?post_type=porto_builder&porto_builder_type=product' ) ) . '" target="_blank">', '</a>' ),
					),
					'builder-blocks' => array(
						'control'     => 'select',
						'label'       => esc_html__( 'Select Single Product Template', 'porto' ),
						'choices'     => $this->template_list['product'],
					),
				),
				'popup'             => array(
					'note' => array(
						'control' => 'heading',
						'label'   => sprintf( esc_html__( 'Select one of existing popup templates or %1$screate a new popup%2$s.', 'porto' ), '<a href="' . esc_url( admin_url( 'edit.php?post_type=porto_builder&porto_builder_type=popup' ) ) . '" target="_blank">', '</a>' ),
					),
					'builder-blocks' => array(
						'control'     => 'select',
						'label'       => esc_html__( 'Select Popup Template', 'porto' ),
						'choices'     => $this->template_list['popup'],
					),
				),
				'right-sidebar'     => array(
					'note' => array(
						'control' => 'heading',
						'label'   => sprintf( esc_html__( '%1$sRegister sidebars.%3$s Change page layout and set sidebar on %2$sTheme Option%3$s Panel', 'porto' ), '<a href="' . esc_url( admin_url( 'themes.php?page=multiple_sidebars' ) ) . '" target="_blank">', '<a href="' . esc_url( admin_url( 'themes.php?page=porto_settings' ) ) . '" target="_blank">', '</a>' ),
					),
				),
				'block'             => array(
					'note' => array(
						'control' => 'heading',
						'label'   => sprintf( esc_html__( 'Select one of existing blocks or %1$screate a new block%2$s.', 'porto' ), '<a href="' . esc_url( admin_url( 'edit.php?post_type=porto_builder&porto_builder_type=block' ) ) . '" target="_blank">', '</a>' ),
					),
					'builder-blocks' => array(
						'control'     => 'select',
						'label'       => esc_html__( 'Select Block', 'porto' ),
						'choices'     => $this->template_list['block'],
					),
				),
			);
			require_once PORTO_DIR . '/inc/admin/admin_pages/page-layouts.php';
		}

		public function get_template_list() {
			$types               = array( 'header', 'footer', 'block', 'popup', 'product', 'shop' );
			$this->template_list = array();

			// builder templates
			foreach ( $types as $type ) {
				$posts = get_posts(
					array(
						'post_type'   => 'porto_builder',
						'meta_key'    => 'porto_builder_type',
						'meta_value'  => $type,
						'numberposts' => -1,
					)
				);
				$this->template_list[ $type ]['']   = sprintf( esc_html__( 'Select %1$s', 'porto' ), ucfirst( $type ) );

				foreach ( $posts as $post ) {
					$this->template_list[ $type ][ $post->ID ] = $post->post_title;
				}
			}

			// sidebar
			global $wp_registered_sidebars;

			$this->template_list['sidebar']['']   = esc_html__( 'Select Sidebar', 'porto' );

			foreach ( $wp_registered_sidebars as $key => $value ) {
				$this->template_list['sidebar'][ $key ] = $value['name'];
			}
		}

		private function add_control( $setting, $args, $selected_block = '' ) {
			?>
		<div class="option<?php echo 'preset' == $selected_block ? ' preset' : ''; ?>"<?php echo isset( $args['condition'] ) ? 'data-condition=' . json_encode( $args['condition'] ) : ''; ?>>

			<?php if ( 'select' == $args['control'] ) { ?>

				<label><?php echo esc_html( $args['label'] ); ?></label>
				<select class="<?php echo esc_attr( $setting ); ?>">
				<?php foreach ( $args['choices'] as $key => $value ) { ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $selected_block, $key ); ?>><?php echo esc_html( $value ); ?></option>
				<?php } ?>
				</select>
				<a href="#" class="layout-action layout-action-condition" title="<?php esc_html_e( 'Display Condition', 'porto' ); ?>"><i class="fas fa-cog"></i></a>
				<a href="#" class="layout-action layout-action-open" title="<?php esc_html_e( 'Open', 'porto' ); ?>"><i class="fas fa-edit"></i></a>
				<a href="#" class="layout-action layout-action-remove" title="<?php esc_html_e( 'Remove', 'porto' ); ?>"><i class="fas fa-times"></i></a>
			<?php } elseif ( 'text' == $args['control'] ) { ?>

				<label><?php echo esc_html( $args['label'] ); ?></label>
				<input type="text" name="<?php echo esc_attr( $setting ); ?>" class="<?php echo esc_attr( $setting ); ?>" value="<?php echo esc_attr( $args['default'] ); ?>">

			<?php } elseif ( 'check' == $args['control'] ) { ?>

				<input type="checkbox" name="<?php echo esc_attr( $setting ); ?>" class="<?php echo esc_attr( $setting ); ?>" <?php checked( true, $args['default'] ); ?>>
				<span><?php echo esc_html( $args['label'] ); ?></span>

			<?php } elseif ( 'heading' == $args['control'] ) { ?>

				<h4 class="heading"><?php echo porto_filter_output( $args['label'] ); ?></h4>

			<?php } ?>

			<?php if ( isset( $args['tooltip'] ) ) { ?>
				<div class="tooltip-wrapper">
					<span class="tooltip-trigger"><span class="dashicons dashicons-editor-help"></span></span>
					<div class="tooltip-content tooltip-right hidden"><?php echo esc_html( $args['tooltip'] ); ?></div>
				</div>
			<?php } ?>

			<?php if ( isset( $args['description'] ) ) { ?>
				<p class="description"><?php echo porto_filter_output( $args['description'] ); ?></p>
			<?php } ?>
		</div>
			<?php
		}

	}
endif;
new Porto_Page_Layouts;
