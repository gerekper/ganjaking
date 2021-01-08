<?php

/**
 * Initialize Visual Composer
 *
 * @since 5.5.0
 */

use VisualComposer\Helpers\Traits\WpFiltersActions;
use VisualComposer\Helpers\Traits\EventsFilters;

if ( ! class_exists( 'Porto_VC_Init' ) ) :

	class Porto_VC_Init extends VisualComposer\Framework\Container {
		use WpFiltersActions;
		use EventsFilters;

		private $elements = array(
			'row',
			'portoInfoBox',
			'portoHeading',
			'portoUltimateHeading',
			'portoBlock',
			'portoCounter',
			'portoSidebarMenu',
			'portoGoogleMap',
			'portoTimer',
			'portoBanner',
			'portoBannerLayer',
			'portoBlog',
			'portoRecentPost',
			'portoButton',
			'portoCarousel',
			'portoMasonry',
			'portoMasonryItem',
			'portoMember',
			'portoRecentMember',
			'portoPortfolios',
			'portoRecentPortfolios',
			'portoTestimonial',
			'portoPricingTable',
			'portoModal',
			'portoProductsWidget',
			'portoHotspot',
			'portoProgressBar',
			'portoTab',
			'portoTabContent',
		);

		private $woo_elements = array(
			'portoProducts',
			'portoProductCategories',
			'portoProductsFilter',
		);

		private $porto_metas = array(
			'porto_default',
			'porto_container',
			'porto_layout',
			'porto_sidebar',
			'porto_sidebar2',
			'porto_header_type',
		);

		/**
		 * Register VC Elements
		 */
		public function __construct() {
			if ( ! defined( 'VCV_VERSION' ) ) {
				return;
			}

			add_action(
				'vcv:api',
				function( $api ) {
					$base_url = rtrim( plugins_url( basename( dirname( PORTO_FUNC_FILE ) ) ), '\\/' ) . '/visualcomposer';

					/**
					 * @var \VisualComposer\Modules\Elements\ApiController $elementsApi
					*/
					$elements_api = $api->elements;

					foreach ( $this->elements as $tag ) {
						$manifest_path = __DIR__ . '/elements/' . $tag . '/manifest.json';
						$element_url   = $base_url . '/elements/' . $tag;
						$elements_api->add( $manifest_path, $element_url );
					}

					if ( class_exists( 'Woocommerce' ) ) {
						foreach ( $this->woo_elements as $tag ) {
							$manifest_path = __DIR__ . '/elements/' . $tag . '/manifest.json';
							$element_url   = $base_url . '/elements/' . $tag;
							$elements_api->add( $manifest_path, $element_url );
						}
					}
				},
				8
			);

			if ( is_admin() && ( wp_doing_ajax() || ( isset( $_REQUEST['vcv-action'] ) && 'frontend' == $_REQUEST['vcv-action'] ) ) ) {
				$this->addFilter( 'vcv:dataAjax:setData', 'set_porto_settings' );
				$this->addFilter( 'vcv:dataAjax:getData', 'get_porto_settings' );
			}

			if ( ! is_admin() && isset( $_GET['vcv-ajax'] ) ) {
				/* update editor ajax response */
				$this->addFilter( 'vcv:ajax:elements:ajaxShortcode:adminNonce', 'update_ajax_render', -1 );
			}

			add_action( 'admin_enqueue_scripts', array( $this, 'load_elements_js' ), 1008 );
			add_action( 'wp_enqueue_scripts', array( $this, 'load_element_editor_js' ) );

			$this->wpAddFilter(
				'template_include',
				'set_temp_porto_settings',
				15
			);

			/* update flag if using Porto blocks */
			$editorPostTypeHelper = vchelper( 'AccessEditorPostType' );
			if ( $editorPostTypeHelper->isEditorEnabled( 'porto_builder' ) ) {
				add_action( 'save_post', array( $this, 'update_block_flag_post' ), 10, 2 );
				add_action( 'edit_term', array( $this, 'update_block_flag_term' ), 10, 3 );
				add_action( 'delete_post', array( $this, 'delete_block_flag_post' ) );
				add_action( 'delete_term', array( $this, 'delete_block_flag_term' ), 10, 3 );
				if ( is_admin() ) {
					add_action( 'sidebar_admin_setup', array( $this, 'update_block_flag_sidebar' ) );
					add_action( 'redux/options/porto_settings/saved', array( $this, 'update_block_flag_theme_options' ), 12, 2 );
					add_action( 'customize_save_after', array( $this, 'update_block_flag_hb' ), 50, 1 );
					add_action( 'wp_update_nav_menu_item', array( $this, 'update_block_flag_menu' ), 10, 3 );
					add_action( 'wp_update_nav_menu', array( $this, 'add_block_flag_menu' ) );
				}
			}

			add_action( 'save_post', array( $this, 'post_refresh_carousel' ), 20, 2 );

			/* enqueue vc block styles */
			add_action( 'porto_enqueue_css', array( $this, 'enqueue_block_styles' ) );
			/*$this->addEvent(
				'vcv:assets:enqueueVendorAssets',
				'enqueue_block_styles'
			);*/
		}

		protected function set_temp_porto_settings( $t, VisualComposer\Helpers\Frontend $frontendHelper ) {
			if ( $frontendHelper->isPageEditable() ) {
				if ( isset( $_COOKIE['porto_layout_settings'] ) ) {
					$settings = json_decode( wp_unslash( $_COOKIE['porto_layout_settings'] ), true );
					add_filter(
						'porto_meta_use_default',
						function( $val ) {
							$settings = json_decode( wp_unslash( $_COOKIE['porto_layout_settings'] ), true );
							if ( ! empty( $settings['porto_default'] ) ) {
								return false;
							} elseif ( isset( $settings['porto_default'] ) ) {
								return true;
							}
							return true;
						}
					);

					if ( ! empty( $settings['porto_default'] ) ) {
						add_filter(
							'porto_meta_layout',
							function( $arr ) {
								$settings = json_decode( wp_unslash( $_COOKIE['porto_layout_settings'] ), true );
								if ( ! empty( $settings['porto_layout'] ) ) {
									$arr[0] = sanitize_text_field( $settings['porto_layout'] );
								}
								if ( ! empty( $settings['porto_sidebar'] ) ) {
									$arr[1] = sanitize_text_field( $settings['porto_sidebar'] );
								}
								if ( ! empty( $settings['porto_sidebar2'] ) ) {
									$arr[2] = sanitize_text_field( $settings['porto_sidebar2'] );
								}
								return $arr;
							}
						);
					}
				}
			}
			return $t;
		}

		protected function set_porto_settings( $response, $payload, VisualComposer\Helpers\Request $requestHelper, VisualComposer\Helpers\Frontend $frontendHelper ) {
			$post_id = $payload['sourceId'];
			if ( $frontendHelper->isPreview() ) {
				$preview = wp_get_post_autosave( $post_id );
				if ( is_object( $preview ) ) {
					$post_id = $preview->ID;
				} else {
					$post_id = false;
				}
			}
			$settings = $requestHelper->input( 'vcv-extra' );
			if ( ! empty( $settings ) && $post_id ) {
				foreach ( $this->porto_metas as $meta ) {
					if ( ! empty( $settings[ $meta ] ) ) {
						$val = porto_strip_script_tags( $settings[ $meta ] );
						if ( 'porto_default' == $meta ) {
							$val = 'default';
						}

						update_post_meta( $post_id, str_replace( 'porto_', '', $meta ), wp_slash( $val ) );
					} else {
						delete_post_meta( $post_id, str_replace( 'porto_', '', $meta ) );
					}
				}
			}

			if ( isset( $payload['post'] ) && isset( $payload['post']->post_content ) && preg_match_all( '/\[porto_block\s[^]]*(id|name)="([^"]*)"/', $payload['post']->post_content, $matches ) && ! empty( $matches[2] ) ) {
				$block_slugs = $this->get_block_ids_from_slug( $matches[2] );
				if ( ! empty( $block_slugs ) ) {
					update_post_meta( $post_id, '_porto_vc_blocks_c', array_map( 'intval', $block_slugs ) );
				} else {
					delete_post_meta( $post_id, '_porto_vc_blocks_c' );
				}
			}
			return $response;
		}

		protected function get_porto_settings( $response, $payload ) {
			global $post;

			if ( ! isset( $response['saveExtraArgs'] ) ) {
				$response['saveExtraArgs'] = array();
			}
			foreach ( $this->porto_metas as $meta ) {
				$val = get_post_meta( $post->ID, str_replace( 'porto_', '', $meta ), true );
				if ( 'porto_default' == $meta && 'default' == $val ) {
					$val = '1';
				}
				$response['saveExtraArgs'][ $meta ] = esc_js( $val );
			}
			return $response;
		}

		protected function update_ajax_render( VisualComposer\Helpers\Request $requestHelper ) {
			if ( $requestHelper->exists( 'vcv-shortcode-string' ) && $requestHelper->exists( 'vcv-is-porto-shortcode' ) ) {
				remove_all_actions( 'wp_head' );
				remove_all_actions( 'wp_footer' );
			}
		}

		public function load_element_editor_js() {
			if ( current_user_can( 'edit_posts' ) && isset( $_REQUEST['vcv-editable'] ) && ! empty( $_REQUEST['vcv-source-id'] ) ) {
				wp_enqueue_script( 'porto-vc-editor', plugin_dir_url( __FILE__ ) . 'assets/live_update.js', array(), PORTO_SHORTCODES_VERSION, true );
			}
		}

		public function load_elements_js() {
			if ( isset( $_REQUEST['vcv-action'] ) && 'frontend' == $_REQUEST['vcv-action'] ) {
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
				$image_sizes = array();
				foreach ( porto_sh_commons( 'image_sizes' ) as $value => $key ) {
					$image_sizes[] = array(
						'label' => str_replace( '&amp;', '&', esc_js( $value ) ),
						'value' => esc_js( $key ),
					);
				}

				//wp_enqueue_script( 'wp-api-fetch' );

				wp_enqueue_script( 'porto-vc-editor', plugin_dir_url( __FILE__ ) . 'settings/public/dist/element.bundle.js', array( 'vcv:editors:frontend:script' ), PORTO_SHORTCODES_VERSION, true );

				global $porto_settings;

				if ( ! empty( $porto_settings ) ) {
					$vars = array(
						'ajax_url'          => esc_js( admin_url( 'admin-ajax.php' ) ),
						'nonce'             => wp_create_nonce( 'porto-nonce' ),
						'product_type'      => esc_js( $porto_settings['category-addlinks-pos'] ),
						'creative_layouts'  => $creative_layouts,
						'post_type'         => get_post_type(),

						'gmt_offset'        => get_option( 'gmt_offset' ),
						'grid_gutter_width' => intval( $porto_settings['grid-gutter-width'] ),
						'image_sizes'       => $image_sizes,
						'page_layouts'      => porto_ct_layouts(),
						'sidebars'          => porto_ct_sidebars(),
						'i18n'              => array(
							'porto_settings'     => esc_html__( 'Porto Settings', 'porto-functionality' ),
							'porto_default'      => esc_html__( 'Layout & Sidebar', 'porto-functionality' ),
							'apply_changes'      => esc_html__( 'Apply Changes', 'porto-functionality' ),
							'porto_default_desc' => esc_html__( 'Use selected layout and sidebar options.', 'porto-functionality' ),
							'container'          => esc_html__( 'Wrap as Container', 'porto-functionality' ),
							'layout'             => esc_html__( 'Layout', 'porto-functionality' ),
							'sidebar'            => esc_html__( 'Sidebar', 'porto-functionality' ),
							'sidebar_desc'       => esc_html__( 'You can create the sidebar under Appearance - Sidebars.', 'porto-functionality' ),
						),
					);
					if ( 'porto_builder' == get_post_type() ) {
						$terms = wp_get_post_terms( get_the_ID(), 'porto_builder_type', array( 'fields' => 'names' ) );
						if ( ! empty( $terms ) ) {
							$vars['builder_type']        = esc_js( $terms[0] );
							$vars['i18n']['header_type'] = esc_html__( 'Header Type', 'porto-functionality' );
						}
						if ( class_exists( 'Woocommerce' ) ) {
							$vars['myaccount_url'] = esc_url( wc_get_page_permalink( 'myaccount' ) );
						}
					}
					wp_localize_script(
						'porto-admin',
						'porto_vc_vars',
						$vars
					);
				}
			}
		}

		public function enqueue_block_styles() {
			if ( ! function_exists( 'porto_check_using_vc_style' ) ) {
				return;
			}
			$vc_blocks = porto_check_using_vc_style();
			if ( ! empty( $vc_blocks ) ) {
				foreach ( $vc_blocks as $post_id ) {
					vchelper( 'AssetsEnqueue' )->enqueueAssets( $post_id );
					$bundle_url = get_post_meta( $post_id, 'vcvSourceCssFileUrl', true );
					if ( $bundle_url ) {
						$version = get_post_meta( $post_id, '_' . VCV_PREFIX . 'sourceChecksum', true );

						if ( 0 !== strpos( $bundle_url, 'http' ) ) {
							if ( false === strpos( $bundle_url, 'assets-bundles' ) ) {
								$bundle_url = '/assets-bundles/' . $bundle_url;
							}
						}

						$handle = 'vcv:assets:source:main:styles:' . vchelper( 'Str' )->slugify( $bundle_url );
						wp_enqueue_style(
							$handle,
							vchelper( 'Assets' )->getAssetUrl( $bundle_url ),
							array(),
							VCV_VERSION . '.' . $version . '-' . $post_id
						);
					}
				}
			}
		}

		public function update_block_flag_post( $post_id, $post = false, $is_term = false, $taxonomy = false ) {
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			if ( false !== $post && defined( 'VCV_VERSION' ) && 'porto_builder' == $post->post_type ) {
				global $porto_settings;
				$builder_type = get_post_meta( $post->ID, 'porto_builder_type', true );
				if ( 'product' == $builder_type ) {
					$blocks = get_theme_mod( '_vc_blocks_product', array() );
					if ( ! in_array( 'product', $blocks ) && 'fe' == get_post_meta( $post_id, 'vcv-be-editor', true ) && get_post_meta( $post_id, 'vcvSourceCssFileUrl', true ) && 'builder' == $porto_settings['product-single-content-layout'] && isset( $porto_settings['product-single-content-builder'] ) && ( $post_id == $porto_settings['product-single-content-builder'] || $post->post_name == $porto_settings['product-single-content-builder'] ) && ! in_array( $post_id, $blocks ) ) {
						$blocks[] = (int) $post_id;
						set_theme_mod( '_vc_blocks_product', $blocks );
					}
				}
			}

			if ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && 'vcv-admin-ajax' == $_REQUEST['action'] && isset( $_REQUEST['vcv-admin-ajax'] ) && isset( $_REQUEST['vcv-zip'] ) ) {
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

			$block_slugs = $this->get_block_ids_from_slug( $block_slugs );
			if ( ! empty( $block_slugs ) ) {
				if ( $is_term ) {
					update_metadata( $taxonomy, $post_id, '_porto_vc_blocks', $block_slugs );
				} else {
					update_post_meta( $post_id, '_porto_vc_blocks', $block_slugs );
				}
			} else {
				if ( $is_term ) {
					delete_metadata( $taxonomy, $post_id, '_porto_vc_blocks' );
				} else {
					delete_post_meta( $post_id, '_porto_vc_blocks' );
				}
			}
		}

		public function update_block_flag_term( $term_id, $tt_id, $taxonomy ) {
			$this->update_block_flag_post( $term_id, false, true, $taxonomy );
		}

		public function delete_block_flag_post( $post_id ) {
			if ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && 'vcv-admin-ajax' == $_REQUEST['action'] && isset( $_REQUEST['vcv-admin-ajax'] ) && isset( $_REQUEST['vcv-zip'] ) ) {
				return;
			}
			delete_post_meta( $post_id, '_porto_vc_blocks' );
			delete_post_meta( $post_id, '_porto_vc_blocks_c' );
		}

		public function delete_block_flag_term( $term_id, $tt_id, $taxonomy ) {
			delete_metadata( $taxonomy, $term_id, '_porto_vc_blocks' );
		}

		public function update_block_flag_sidebar() {
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

			$block_widgets = get_option( 'widget_block-widget', array() );
			$vc_sidebars   = get_theme_mod( '_vc_blocks_sidebar', array() );
			$block_slugs   = array();

			global $wp_registered_widgets;
			if ( isset( $_POST['delete_widget'] ) && $_POST['delete_widget'] && isset( $wp_registered_widgets[ $widget_id ] ) && isset( $vc_sidebars[ $sidebar_id ] ) && is_array( $vc_sidebars[ $sidebar_id ] ) ) {
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

			$vc_sidebars[ $sidebar_id ] = array();

			foreach ( $sidebar as $widget ) {
				$widget_type = trim( substr( $widget, 0, strrpos( $widget, '-' ) ) );
				$widget_id   = str_replace( 'block-widget-', '', $widget );
				if ( 'block-widget' == $widget_type && ! empty( $block_widgets[ $widget_id ] ) && ! empty( $block_widgets[ $widget_id ]['name'] ) && empty( $block_slugs[ $widget ] ) ) {
					$block_slugs[ $widget ] = $block_widgets[ $widget_id ]['name'];
				}
			}

			if ( ! empty( $block_slugs ) ) {
				$vc_sidebars[ sanitize_text_field( $sidebar_id ) ] = $this->get_block_ids_from_slug( $block_slugs );
			}

			if ( empty( $vc_sidebars[ $sidebar_id ] ) ) {
				unset( $vc_sidebars[ $sidebar_id ] );
			}

			set_theme_mod( '_vc_blocks_sidebar', $vc_sidebars );
		}

		public function update_block_flag_theme_options( $options, $changed ) {
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

			if ( isset( $changed['header-type-select'] ) ) {
				if ( empty( $options['header-type-select'] ) ) {
					remove_theme_mod( '_vc_blocks_header' );
				} else {
					$this->update_block_flag_hb( false, true, $options );
				}
			}

			if ( ! $block_changed && ! $blog_block_changed && ! isset( $changed['product-content_bottom'] ) && ! isset( $changed['product-tab-block'] ) && ! isset( $changed['product-single-content-layout'] ) && ! isset( $changed['product-single-content-builder'] ) ) {
				return;
			}

			if ( $block_changed ) {
				$block_slugs = array();
				foreach ( $html_blocks as $b ) {
					if ( ! empty( $options[ 'html-' . $b ] ) && preg_match( '/\[porto_block\s[^]]*(id|name)="([^"]*)"/', $options[ 'html-' . $b ], $matches ) && isset( $matches[2] ) && $matches[2] ) {
						$block_slugs[] = trim( $matches[2] );
					}
				}

				$block_slugs = $this->get_block_ids_from_slug( $block_slugs );
				if ( ! empty( $block_slugs ) ) {
					set_theme_mod( '_vc_blocks', $block_slugs );
				} else {
					remove_theme_mod( '_vc_blocks' );
				}
			}

			if ( $blog_block_changed ) {
				$block_slugs = array();
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

				$block_slugs = $this->get_block_ids_from_slug( $block_slugs );
				if ( ! empty( $block_slugs ) ) {
					set_theme_mod( '_vc_blocks_blog', $block_slugs );
				} else {
					remove_theme_mod( '_vc_blocks_blog' );
				}
			}

			$product_block_ids = array();
			if ( 'builder' == $options['product-single-content-layout'] && ! empty( $options['product-single-content-builder'] ) ) {
				global $wpdb;
				$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = 'porto_builder' AND post_name = %s", $options['product-single-content-builder'] ) );
				if ( $post_id && 'fe' == get_post_meta( $post_id, 'vcv-be-editor', true ) && get_post_meta( $post_id, 'vcvSourceCssFileUrl', true ) ) {
					$product_block_ids[] = (int) $post_id;
				}
			}

			$block_slugs = array();
			if ( ! empty( $options['product-content_bottom'] ) ) {
				$block_slugs = array_merge( $block_slugs, explode( ',', $options['product-content_bottom'] ) );
			}
			if ( ! empty( $options['product-tab-block'] ) ) {
				$block_slugs = array_merge( $block_slugs, explode( ',', $options['product-tab-block'] ) );
			}
			if ( ! empty( $block_slugs ) ) {
				$product_block_ids = array_merge( $product_block_ids, $this->get_block_ids_from_slug( $block_slugs ) );
			}
			if ( ! empty( $product_block_ids ) ) {
				set_theme_mod( '_vc_blocks_product', $product_block_ids );
			} else {
				remove_theme_mod( '_vc_blocks_product' );
			}
		}

		private function get_block_id_from_hb( $elements ) {
			if ( ! $elements || empty( $elements ) ) {
				return array();
			}
			$result = array();
			foreach ( $elements as $element ) {
				if ( is_array( $element ) ) {
					$result = array_merge( $result, $this->get_block_id_from_hb( $element ) );
				} else {
					foreach ( $element as $key => $value ) {
						if ( 'porto_block' == $key && $value ) {
							$str = '';
							if ( is_string( $value ) ) {
								$str = $value;
							} elseif ( is_object( $value ) && isset( $value->html ) ) {
								$str = $value->html;
							}
							if ( $str ) {
								$result[] = $str;
							}
						}
					}
				}
			}
			return array_unique( $result );
		}

		private function get_block_ids_from_slug( $porto_blocks ) {
			if ( empty( $porto_blocks ) ) {
				return array();
			}
			$result = array();
			global $wpdb;
			foreach ( $porto_blocks as $s ) {
				$where   = is_numeric( $s ) ? 'ID' : 'post_name';
				$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = 'porto_builder' AND $where = %s", sanitize_text_field( $s ) ) );
				if ( $post_id && 'fe' == get_post_meta( $post_id, 'vcv-be-editor', true ) && get_post_meta( $post_id, 'vcvSourceCssFileUrl', true ) ) {
					$result[] = (int) $post_id;
				}
			}
			return array_unique( $result );
		}

		public function update_block_flag_hb( $obj, $in_theme_option = false, $options = false ) {
			if ( ! $in_theme_option ) {
				if ( ! isset( $_POST['customized'] ) || ( false === strpos( $_POST['customized'], 'porto_header_builder_elements' ) && false === strpos( $_POST['customized'], 'porto_header_builder[selected_layout]' ) ) ) {
					return;
				}
				global $porto_settings;
				if ( empty( $porto_settings['header-type-select'] ) ) {
					return;
				}
			}
			$header_layouts  = get_option( 'porto_header_builder_layouts', array() );
			$selected_layout = get_option( 'porto_header_builder', array() );
			$porto_blocks    = array();
			if ( ! empty( $selected_layout ) && isset( $selected_layout['selected_layout'] ) && $selected_layout['selected_layout'] && isset( $header_layouts[ $selected_layout['selected_layout'] ] ) ) {
				$porto_header_builder_layout = $header_layouts[ $selected_layout['selected_layout'] ];
				if ( ! empty( $porto_header_builder_layout['elements'] ) ) {
					$elements = $porto_header_builder_layout['elements'];

					$header_rows    = array( 'top', 'main', 'bottom' );
					$header_columns = array( 'left', 'center', 'right' );
					foreach ( $header_rows as $r ) {
						foreach ( $header_columns as $c ) {
							if ( ! empty( $elements[ $r . '_' . $c ] ) ) {
								$porto_blocks = array_merge( $porto_blocks, json_decode( $elements[ $r . '_' . $c ] ) );
							}
							if ( ! empty( $elements[ 'mobile_' . $r . '_' . $c ] ) ) {
								$porto_blocks = array_merge( $porto_blocks, json_decode( $elements[ 'mobile_' . $r . '_' . $c ] ) );
							}
						}
					}
					$porto_blocks = $this->get_block_id_from_hb( $porto_blocks );
					if ( ! empty( $porto_blocks ) ) {
						$porto_blocks = $this->get_block_ids_from_slug( $porto_blocks );
					}
				}
			}
			if ( ! empty( $porto_blocks ) ) {
				set_theme_mod( '_vc_blocks_header', $porto_blocks );
			} else {
				remove_theme_mod( '_vc_blocks_header' );
			}
		}

		public function update_block_flag_menu( $menu_id, $menu_item_db_id, $args ) {
			$key = 'block';

			if ( ! isset( $_POST[ 'menu-item-' . $key ][ $menu_item_db_id ] ) ) {
				if ( ! isset( $args[ 'menu-item-' . $key ] ) ) {
					$value = '';
				} else {
					$value = $args[ 'menu-item-' . $key ];
				}
			} else {
				$value = sanitize_text_field( $_POST[ 'menu-item-' . $key ][ $menu_item_db_id ] );
			}

			if ( $value ) {
				$block_slug = $this->get_block_ids_from_slug( array( $value ) );
				if ( is_array( $block_slug ) && isset( $block_slug[0] ) ) {
					$blocks = get_transient( '_porto_menu_blocks' );
					if ( ! $blocks || ! is_array( $blocks ) ) {
						$blocks = array();
					}
					$blocks[] = (int) $block_slug[0];
					set_transient( '_porto_menu_blocks', $blocks, 3 ); // 3 seconds
				}
			}
		}

		public function add_block_flag_menu( $menu_id ) {
			$blocks      = get_transient( '_porto_menu_blocks' );
			$menu_blocks = get_theme_mod( '_vc_blocks_menu', array() );
			if ( isset( $menu_blocks[ $menu_id ] ) ) {
				unset( $menu_blocks[ $menu_id ] );
			}
			if ( is_array( $blocks ) && ! empty( $blocks ) ) {
				$menu_blocks[ $menu_id ] = $blocks;
				delete_transient( '_porto_menu_blocks' );
			}
			if ( ! empty( $menu_blocks ) ) {
				set_theme_mod( '_vc_blocks_menu', $menu_blocks );
			} else {
				remove_theme_mod( '_vc_blocks_menu' );
			}
		}

		public function post_refresh_carousel( $post_id, $post ) {
			// filter content to remove auto added carousel divs
			if ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && 'vcv-admin-ajax' == $_REQUEST['action'] && isset( $_REQUEST['vcv-admin-ajax'] ) && isset( $_REQUEST['vcv-zip'] ) ) {
				if ( false !== $post && $post->post_content && 'inherit' != $post->post_status && ! defined( 'PORTO_VCV_POST_FILTERED' ) && ( preg_match( '/<div class="owl-carousel[^>]*><div class="owl-stage-outer">/', $post->post_content ) || false !== strpos( $post->post_content, 'porto-vc-progressbar' ) ) ) {
					ob_start();
					try {
						$no_format = false;
						if ( 0 === strpos( $post->post_content, '<!--vcv no format-->' ) ) {
							$no_format = true;
						}
						$dom = new DOMDocument;
						$dom->loadHTML( $post->post_content );
						$dom          = $this->refresh_carousel( $dom );
						$content_node = $dom->getElementsByTagName( 'body' );
						if ( count( $content_node ) ) {
							$post_content = str_replace( array( '<body>', '</body>' ), '', $dom->saveHTML( $content_node[0] ) );
						} else {
							$post_content = $dom->saveHTML();
						}
						$post->post_content = rawurldecode( utf8_decode( $post_content ) );
						if ( $no_format && 0 !== strpos( $post->post_content, '<!--vcv no format-->' ) ) {
							$post->post_content = '<!--vcv no format-->' . $post->post_content;
						}
						define( 'PORTO_VCV_POST_FILTERED', true );
						$result = wp_update_post( $post, true );
					} catch ( Exception $e ) {
					}
					ob_end_clean();
				}
			}
		}

		private function refresh_carousel( $dom ) {
			if ( ! $dom->childNodes ) {
				return;
			}
			foreach ( $dom->childNodes as $e ) {
				if ( method_exists( $e, 'getAttribute' ) ) {
					$cls = $e->getAttribute( 'class' );
				} else {
					$cls = '';
				}

				if ( $e->hasChildNodes() ) {
					if ( 'owl-stage-outer' == $cls ) {
						if ( $e->nextSibling ) {
							if ( $e->nextSibling->nextSibling ) {
								$e->parentNode->removeChild( $e->nextSibling->nextSibling );
							}
							$e->parentNode->removeChild( $e->nextSibling );
						}
						$items = $e->childNodes[0]->childNodes;
						foreach ( $items as $node ) {
							if ( false !== strpos( $node->getAttribute( 'class' ), 'owl-item' ) ) {
								$e->parentNode->appendChild( $node->childNodes[0] );
							} else {
								$e->parentNode->appendChild( $node );
							}
						}
						$e->parentNode->removeChild( $e );
					} elseif ( false !== strpos( $cls, 'owl-loaded' ) && false !== strpos( $cls, 'owl-carousel' ) ) {
						$e->setAttribute( 'class', str_replace( ' owl-loaded', '', $cls ) );
					}
					$this->refresh_carousel( $e );
				}
				if ( false !== strpos( $cls, 'progress-bar' ) ) {
					$e->removeAttribute( 'style' );
				}
			}
			return $dom;
		}
	}
endif;

new Porto_VC_Init;
