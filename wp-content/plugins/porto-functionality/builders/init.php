<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto builders libarary
 *
 * @since 6.0
 */
class PortoBuilders {

	const BUILDER_SLUG = 'porto_builder';

	const ADMIN_MENU_SLUG = 'edit.php?post_type=' . self::BUILDER_SLUG;

	const BUILDER_TAXONOMY_SLUG = 'porto_builder_type';

	const BUILDER_CAP = 'edit_pages';

	private $lib_condition = null;

	/**
	 * Constructor
	 */
	public function __construct() {
		global $porto_settings_optimize;
		if ( empty( $porto_settings_optimize ) ) {
			if ( ! is_customize_preview() ) {
				$porto_settings_optimize = get_option( 'porto_settings_optimize', array() );
			} else {
				$porto_settings_optimize = array();
			}
		}

		$this->builder_types = array(
			'block'   => __( 'Block', 'porto-functionality' ),
			'header'  => __( 'Header', 'porto-functionality' ),
			'footer'  => __( 'Footer', 'porto-functionality' ),
			'product' => __( 'Single Product', 'porto-functionality' ),
			'shop'    => __( 'Product Archive', 'porto-functionality' ),
		);

		if ( ! empty( $porto_settings_optimize['disabled_pbs'] ) && is_array( $porto_settings_optimize['disabled_pbs'] ) ) {
			foreach ( $porto_settings_optimize['disabled_pbs'] as $key ) {
				if ( isset( $this->builder_types[ $key ] ) ) {
					unset( $this->builder_types[ $key ] );
				}
			}
		}

		$this->builder_types = apply_filters( 'porto_templates_builder_types', $this->builder_types );

		add_action( 'init', array( $this, 'add_builder_type' ) );

		add_action( 'admin_menu', array( $this, 'add_builder_menu' ), 20 );

		register_activation_hook(
			PORTO_FUNC_FILE,
			function() {
				$this->add_builder_type();
				flush_rewrite_rules();
			}
		);

		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
			add_action( 'porto_builder_condition_pre_enqueue', array( $this, 'enqueue' ) );
			add_filter( 'views_edit-' . self::BUILDER_SLUG, array( $this, 'admin_print_tabs' ) );
			add_filter( 'manage_' . self::BUILDER_SLUG . '_posts_columns', array( $this, 'admin_column_header' ) );
			add_action( 'manage_' . self::BUILDER_SLUG . '_posts_custom_column', array( $this, 'admin_column_content' ), 10, 2 );
			add_action( 'admin_action_porto-new-builder', array( $this, 'add_builder_post' ) );

			add_action(
				'admin_footer',
				function() {
					include_once PORTO_BUILDERS_PATH . 'views/popup_content.php';
				}
			);

			add_action(
				'init',
				function() {
					$load_search_lib = false;
					if ( 'post.php' == $GLOBALS['pagenow'] && ( ( isset( $_REQUEST['post'] ) && self::BUILDER_SLUG == get_post_type( $_REQUEST['post'] ) ) || ( isset( $_REQUEST['post_id'] ) && self::BUILDER_SLUG == get_post_type( $_REQUEST['post_id'] ) ) ) ) {
						if ( isset( $_REQUEST['post'] ) ) {
							$post_id = $_REQUEST['post'];
						} else {
							$post_id = $_REQUEST['post_id'];
						}
						if ( 'block' != get_post_meta( (int) $post_id, self::BUILDER_TAXONOMY_SLUG, true ) ) {
							$load_search_lib = true;
						}
					}
					if ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && 0 === strpos( $_REQUEST['action'], 'porto_builder_' ) ) {
						$load_search_lib = true;
					}
					if ( $load_search_lib ) {
						require_once PORTO_BUILDERS_PATH . 'lib/class-condition.php';
						new Porto_Builder_Condition;
					}
				}
			);
		}

		// register builder elements
		add_action(
			'plugins_loaded',
			function() {
				if ( array_key_exists( 'header', $this->builder_types ) ) {
					require_once PORTO_BUILDERS_PATH . 'elements/header/init.php';
				}

				if ( class_exists( 'Woocommerce' ) ) {
					if ( array_key_exists( 'product', $this->builder_types ) ) {
						require_once PORTO_BUILDERS_PATH . 'elements/product/init.php';
					}

					if ( array_key_exists( 'shop', $this->builder_types ) ) {
						require_once PORTO_BUILDERS_PATH . 'elements/shop/init.php';
					}
				}
			}
		);
	}

	/**
	 * Enqueue needed scripts
	 */
	public function enqueue() {
		$screen = get_current_screen();
		if ( defined( 'PORTO_JS' ) /*&& $screen && ( ( 'edit' == $screen->base && 'edit-porto_builder' == $screen->id ) || ( 'post' == $screen->base && self::BUILDER_SLUG == $screen->id ) )*/ ) {
			wp_enqueue_style( 'porto-builder-fonts', '//fonts.googleapis.com/css?family=Poppins%3A400%2C600%2C700' );
			wp_enqueue_style( 'jquery-magnific-popup', PORTO_CSS . '/magnific-popup.min.css', false, '1.1.0', 'all' );
			wp_enqueue_script( 'jquery-magnific-popup', PORTO_JS . '/libs/jquery.magnific-popup.min.js', array( 'jquery-core' ), '1.1.0', true );
			wp_enqueue_script( 'porto-builder-admin', str_replace( '/shortcodes', '/builders', PORTO_SHORTCODES_URL ) . 'assets/admin.js', array( 'jquery-core' ), PORTO_SHORTCODES_VERSION, true );
		}
	}

	/**
	 * Register builder post type and builder types as taxonomies
	 */
	public function add_builder_type() {
		$singular_name = __( 'Template Builder', 'porto-functionality' );
		$name          = __( 'Templates Builder', 'porto-functionality' );
		$current_type  = $singular_name;
		if ( ! empty( $_REQUEST[ self::BUILDER_TAXONOMY_SLUG ] ) && isset( $this->builder_types[ $_REQUEST[ self::BUILDER_TAXONOMY_SLUG ] ] ) ) {
			$current_type = $this->builder_types[ $_REQUEST[ self::BUILDER_TAXONOMY_SLUG ] ];
		}
		$labels = array(
			'name'               => $name,
			'singular_name'      => $current_type,
			/* translators: current type */
			'add_new'            => sprintf( __( 'Add New %s', 'porto-functionality' ), str_replace( $singular_name, '', $current_type ) ),
			/* translators: %s: content type singular name */
			'add_new_item'       => sprintf( __( 'Add New %s', 'porto-functionality' ), $current_type ),
			/* translators: %s: content type singular name */
			'edit_item'          => sprintf( __( 'Edit %s', 'porto-functionality' ), $current_type ),
			/* translators: %s: content type singular name */
			'new_item'           => sprintf( __( 'New %s', 'porto-functionality' ), $current_type ),
			/* translators: %s: content type singular name */
			'view_item'          => sprintf( __( 'View %s', 'porto-functionality' ), $current_type ),
			/* translators: %s: content type singular label */
			'search_items'       => sprintf( __( 'Search %s', 'porto-functionality' ), $name ),
			/* translators: %s: content type singular label */
			'not_found'          => sprintf( __( 'No %s found', 'porto-functionality' ), $name ),
			/* translators: %s: content type singular label */
			'not_found_in_trash' => sprintf( __( 'No %s found in Trash', 'porto-functionality' ), $name ),
			'parent_item_colon'  => '',
		);

		$args = array(
			'labels'               => $labels,
			'public'               => true,
			'rewrite'              => false,
			'menu_icon'            => 'dashicons-admin-page',
			'show_ui'              => true,
			'show_in_menu'         => false,
			'show_in_nav_menus'    => false,
			'exclude_from_search'  => true,
			'capability_type'      => 'post',
			'hierarchical'         => false,
			'supports'             => array(
				'title',
				'thumbnail',
				'author',
				'editor',
			),
			'register_meta_box_cb' => array( $this, 'add_meta_boxes' ),
		);
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$args['supports'][] = 'elementor';
		}
		if ( is_admin() && current_user_can( PortoBuilders::BUILDER_CAP ) ) {
			if ( defined( 'VCV_VERSION' ) ) {
				$support_types = get_option( 'vcv-post-types', array() );
				if ( ! in_array( PortoBuilders::BUILDER_SLUG, $support_types ) ) {
					$support_types[] = PortoBuilders::BUILDER_SLUG;
					update_option( 'vcv-post-types', $support_types );
				}
			}
		}
		register_post_type( self::BUILDER_SLUG, $args );

		$args = array(
			'hierarchical'      => false,
			'show_ui'           => false,
			'show_in_nav_menus' => false,
			'show_admin_column' => true,
			'query_var'         => is_admin(),
			'rewrite'           => false,
			'public'            => false,
			'label'             => __( 'Type', 'porto-functionality' ),
		);
		register_taxonomy( self::BUILDER_TAXONOMY_SLUG, self::BUILDER_SLUG, $args );
	}

	public function add_builder_menu() {
		add_submenu_page( 'porto', __( 'Templates Builder', 'porto' ), __( 'Templates Builder', 'porto' ), 'administrator', 'edit.php?post_type=' . PortoBuilders::BUILDER_SLUG );
	}

	public function add_meta_boxes() {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( function_exists( 'add_meta_box' ) && $screen && 'post' == $screen->base && self::BUILDER_SLUG == $screen->id ) {
			add_meta_box(
				self::BUILDER_SLUG . '-meta-box',
				__( 'Layout Options', 'porto-functionality' ),
				'porto_block_meta_box',
				self::BUILDER_SLUG,
				'normal',
				'high'
			);
		}
	}

	public function admin_print_tabs( $views ) {
		if ( ! current_user_can( self::BUILDER_CAP ) ) {
			return;
		}

		$active_class = ' nav-tab-active';
		$current_type = '';

		if ( ! empty( $_REQUEST[ self::BUILDER_TAXONOMY_SLUG ] ) ) {
			$current_type = $_REQUEST[ self::BUILDER_TAXONOMY_SLUG ];
			$active_class = '';
		}

		$baseurl = add_query_arg( 'post_type', self::BUILDER_SLUG, admin_url( 'edit.php' ) );
		?>
		<div id="porto-builders-tabs" class="nav-tab-wrapper">
			<a class="nav-tab<?php echo esc_attr( $active_class ); ?>" href="<?php echo esc_url( $baseurl ); ?>"><?php esc_html_e( 'All', 'porto-functionality' ); ?></a>

		<?php
		foreach ( $this->builder_types as $type => $label ) :
			$active_class = '';
			if ( $current_type === $type ) {
				$active_class = ' nav-tab-active';
			}
			$builder_url = add_query_arg( self::BUILDER_TAXONOMY_SLUG, $type, $baseurl );
			echo '<a class="nav-tab' . $active_class . '" href="' . esc_url( $builder_url ) . '">' . esc_html( $label ) . '</a>';
		endforeach;
		?>
		</div>
		<?php
		return $views;
	}

	public function admin_column_header( $defaults ) {
		$defaults['shortcode'] = __( 'Shortcode', 'porto-functionality' );
		return $defaults;
	}

	public function admin_column_content( $column_name, $post_id ) {
		if ( 'shortcode' === $column_name ) {
			$shortcode = sprintf( '[porto_block id="%d"]', $post_id );
			printf( '<input class="porto-input-shortcode" type="text" readonly="readonly" onfocus="this.select()" value="%s" />', esc_attr( $shortcode ) );
		}
	}

	public function add_builder_post() {
		if ( current_user_can( self::BUILDER_CAP ) && ! empty( $_POST['builder_type'] ) && ! empty( $_POST['builder_name'] ) ) {
			check_admin_referer( 'porto-builder' );
			$builder_type = sanitize_text_field( $_POST['builder_type'] );
			$builder_name = sanitize_text_field( $_POST['builder_name'] );

			$post_meta = apply_filters( 'porto_create_new_builder_meta', array() );

			$post_data = array(
				'post_title' => $builder_name,
				'post_type'  => self::BUILDER_SLUG,
				'meta_input' => $post_meta,
			);
			$post_id   = wp_insert_post( $post_data );
			if ( $post_id && ! is_wp_error( $post_id ) ) {
				add_post_meta( $post_id, self::BUILDER_TAXONOMY_SLUG, $builder_type );
				wp_set_post_terms( $post_id, $builder_type, self::BUILDER_TAXONOMY_SLUG );
				wp_redirect(
					add_query_arg(
						array(
							'post'   => $post_id,
							'action' => 'edit',
						),
						esc_url( admin_url( 'post.php' ) )
					)
				);
				exit;
			}
		}
	}

	public static function check_load_wpb_elements( $type ) {
		if ( ! defined( 'WPB_VC_VERSION' ) ) {
			return false;
		}
		if ( 'post-new.php' == $GLOBALS['pagenow'] && isset( $_GET['post_type'] ) && PortoBuilders::BUILDER_SLUG == $_GET['post_type'] ) {
			return true;
		} elseif ( 'post.php' == $GLOBALS['pagenow'] && ( isset( $_GET['post'] ) || ! empty( $_REQUEST['post_ID'] ) ) ) {
			if ( isset( $_GET['post'] ) ) {
				$post      = get_post( intval( $_GET['post'] ) );
				if ( ! $post ) {
					return false;
				}
				$post_type = $post->post_type;
			} else {
				$post_type = $_REQUEST['post_type'];
			}
			$post_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : (int) $_REQUEST['post_ID'];

			if ( PortoBuilders::BUILDER_SLUG == $post_type && $type == get_post_meta( $post_id, PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) {
				return true;
			}
		} elseif ( function_exists( 'porto_is_ajax' ) && porto_is_ajax() && isset( $_REQUEST['post_id'] ) ) {
			$post = get_post( intval( $_REQUEST['post_id'] ) );
			if ( is_object( $post ) && ( PortoBuilders::BUILDER_SLUG == $post->post_type || $type == $post->post_type ) ) {
				return true;
			}
		} elseif ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
			if ( is_admin() && isset( $_GET['post_type'] ) && PortoBuilders::BUILDER_SLUG == $_GET['post_type'] && isset( $_GET['post_id'] ) ) {
				$terms = wp_get_post_terms( (int) $_GET['post_id'], PortoBuilders::BUILDER_TAXONOMY_SLUG, array( 'fields' => 'names' ) );
				if ( ! empty( $terms ) && $type == $terms[0] ) {
					return true;
				}
			} elseif ( ! is_admin() ) {
				$post_id = (int) vc_get_param( 'vc_post_id' );
				if ( $post_id ) {
					$post  = get_post( $post_id );
					$terms = wp_get_post_terms( $post_id, PortoBuilders::BUILDER_TAXONOMY_SLUG, array( 'fields' => 'names' ) );
					if ( is_object( $post ) && PortoBuilders::BUILDER_SLUG == $post->post_type && ! empty( $terms ) && $type == $terms[0] ) {
						return true;
					}
				}
			}
		}
		return false;
	}
}

new PortoBuilders;
