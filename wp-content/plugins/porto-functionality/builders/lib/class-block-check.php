<?php
/**
 * Check if there is block when saving post, term, widget, menu, options, etc in Elementor and Visual Composer
 *
 * @since 6.1.0
 */

if ( ! class_exists( 'Porto_Block_Check' ) ) :
	class Porto_Block_Check {

		private $elementor_support;

		private $vc_support;

		public function __construct() {
			/*if ( ( ! defined( 'ELEMENTOR_VERSION' ) || ! post_type_supports( PortoBuilders::BUILDER_SLUG, 'elementor' ) ) &&
				( ! defined( 'VCV_VERSION' ) || ! vchelper( 'AccessEditorPostType' )->isEditorEnabled( PortoBuilders::BUILDER_SLUG ) ) ) {
				return;
			}*/
			if ( ! post_type_exists( PortoBuilders::BUILDER_SLUG ) ) {
				return;
			}

			$this->elementor_support = ( defined( 'ELEMENTOR_VERSION' ) && post_type_supports( PortoBuilders::BUILDER_SLUG, 'elementor' ) );
			$this->vc_support        = ( defined( 'VCV_VERSION' ) && vchelper( 'AccessEditorPostType' )->isEditorEnabled( PortoBuilders::BUILDER_SLUG ) );

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

		public function update_block_flag_post( $post_id, $post = false, $is_term = false, $taxonomy = false ) {
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			$used_blocks = get_theme_mod( '_used_blocks', array() );
			if ( ! isset( $used_blocks['el'] ) ) {
				$used_blocks['el'] = array();
			}
			if ( ! isset( $used_blocks['vc'] ) ) {
				$used_blocks['vc'] = array();
			}
			if ( false !== $post && PortoBuilders::BUILDER_SLUG == $post->post_type ) {
				global $porto_settings;
				$builder_type = get_post_meta( $post->ID, PortoBuilders::BUILDER_TAXONOMY_SLUG, true );
				if ( 'product' == $builder_type ) {
					if ( $this->elementor_support && get_post_meta( $post_id, '_elementor_edit_mode', true ) && get_post_meta( $post_id, '_elementor_data', true ) ) {
						if ( ! isset( $used_blocks['el']['product'] ) ) {
							$used_blocks['el']['product'] = array();
						}
						if ( 'builder' == $porto_settings['product-single-content-layout'] && isset( $porto_settings['product-single-content-builder'] ) && ( $post_id == $porto_settings['product-single-content-builder'] || $post->post_name == $porto_settings['product-single-content-builder'] ) && ! in_array( $post_id, $used_blocks['el']['product'] ) ) {
							$used_blocks['el']['product'][] = (int) $post_id;
							set_theme_mod( '_used_blocks', $used_blocks );
						}
					} elseif ( $this->vc_support && 'fe' == get_post_meta( $post_id, 'vcv-be-editor', true ) && get_post_meta( $post_id, 'vcvSourceCssFileUrl', true ) ) {
						if ( ! isset( $used_blocks['vc']['product'] ) ) {
							$used_blocks['vc']['product'] = array();
						}
						if ( 'builder' == $porto_settings['product-single-content-layout'] && isset( $porto_settings['product-single-content-builder'] ) && ( $post_id == $porto_settings['product-single-content-builder'] || $post->post_name == $porto_settings['product-single-content-builder'] ) && ! in_array( $post_id, $used_blocks['vc']['product'] ) ) {
							$used_blocks['vc']['product'][] = (int) $post_id;
							set_theme_mod( '_used_blocks', $used_blocks );
						}
					}
				}
			}

			if ( ( isset( $_REQUEST['action'] ) && ( 'elementor' == $_REQUEST['action'] || 'elementor_ajax' == $_REQUEST['action'] ) ) || isset( $_REQUEST['elementor-preview'] ) ) {
				return;
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
			if ( $is_term ) {
				delete_metadata( $taxonomy, $post_id, '_porto_use_elementor_blocks' );
				delete_metadata( $taxonomy, $post_id, '_porto_vc_blocks' );
			} else {
				delete_post_meta( $post_id, '_porto_use_elementor_blocks' );
				delete_post_meta( $post_id, '_porto_vc_blocks' );
			}

			for ( $i = 0; $i <= 1; $i++ ) {
				$prefix     = 0 === $i ? 'vc' : 'el';
				$array_name = $is_term ? 'term' : 'post';
				if ( ! isset( $used_blocks[ $prefix ][ $array_name ] ) ) {
					$used_blocks[ $prefix ][ $array_name ] = array();
				}
				if ( empty( $block_slugs[ $i ] ) ) {
					unset( $used_blocks[ $prefix ][ $array_name ][ $post_id ] );
				} else {
					$used_blocks[ $prefix ][ $array_name ][ $post_id ] = $block_slugs[ $i ];
				}
			}

			// update breadcrumb information
			$breadcrumb_type = $this->get_used_breadcrumbs_type( $block_slugs[2] );
			if ( $breadcrumb_type ) {
				$array_name = $is_term ? 'term' : 'post';
				if ( ! isset( $used_blocks['breadcrumbs'] ) ) {
					$used_blocks['breadcrumbs'] = array();
				}
				if ( ! isset( $used_blocks['breadcrumbs'][ $array_name ] ) ) {
					$used_blocks['breadcrumbs'][ $array_name ] = array();
				}
				$used_blocks['breadcrumbs'][ $array_name ][ $post_id ] = $breadcrumb_type;
			} else {
				unset( $used_blocks['breadcrumbs'][ $array_name ][ $post_id ] );
			}

			set_theme_mod( '_used_blocks', $used_blocks );
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
			delete_post_meta( $post_id, '_porto_use_elementor_blocks' );

			$used_blocks = get_theme_mod( '_used_blocks', array() );
			unset( $used_blocks['el']['post'][ $post_id ], $used_blocks['vc']['post'][ $post_id ] );
			if ( isset( $used_blocks['breadcrumbs']['post'][ $post_id ] ) ) {
				unset( $used_blocks['breadcrumbs']['post'][ $post_id ] );
			}
			set_theme_mod( '_used_blocks', $used_blocks );
		}

		public function delete_block_flag_term( $term_id, $tt_id, $taxonomy ) {
			delete_metadata( $taxonomy, $term_id, '_porto_vc_blocks' );
			delete_metadata( $taxonomy, $term_id, '_porto_use_elementor_blocks' );

			$used_blocks = get_theme_mod( '_used_blocks', array() );
			unset( $used_blocks['el']['term'][ $term_id ], $used_blocks['vc']['term'][ $term_id ] );
			if ( isset( $used_blocks['breadcrumbs']['term'][ $term_id ] ) ) {
				unset( $used_blocks['breadcrumbs']['term'][ $term_id ] );
			}
			set_theme_mod( '_used_blocks', $used_blocks );
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

			$used_blocks = get_theme_mod( '_used_blocks', array() );
			if ( ! isset( $used_blocks['el'] ) ) {
				$used_blocks['el'] = array();
				if ( ! isset( $used_blocks['el']['sidebar'] ) ) {
					$used_blocks['el']['sidebar'] = array();
				}
			}
			if ( ! isset( $used_blocks['vc'] ) ) {
				$used_blocks['vc'] = array();
				if ( ! isset( $used_blocks['vc']['sidebar'] ) ) {
					$used_blocks['vc']['sidebar'] = array();
				}
			}

			$block_widgets = get_option( 'widget_block-widget', array() );
			$block_slugs   = array();

			global $wp_registered_widgets;
			if ( isset( $_POST['delete_widget'] ) && $_POST['delete_widget'] && isset( $wp_registered_widgets[ $widget_id ] ) && ( ( isset( $used_blocks['vc']['sidebar'][ $sidebar_id ] ) && is_array( $used_blocks['vc']['sidebar'][ $sidebar_id ] ) ) || ( isset( $used_blocks['el']['sidebar'][ $sidebar_id ] ) && is_array( $used_blocks['el']['sidebar'][ $sidebar_id ] ) ) ) ) {
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

			$sidebar_id                                  = sanitize_text_field( $sidebar_id );
			$used_blocks['vc']['sidebar'][ $sidebar_id ] = array();
			$used_blocks['el']['sidebar'][ $sidebar_id ] = array();
			unset( $used_blocks['breadcrumbs']['sidebar'][ $sidebar_id ] );

			foreach ( $sidebar as $widget ) {
				$widget_type = trim( substr( $widget, 0, strrpos( $widget, '-' ) ) );
				$widget_id   = str_replace( 'block-widget-', '', $widget );
				if ( 'block-widget' == $widget_type && ! empty( $block_widgets[ $widget_id ] ) && ! empty( $block_widgets[ $widget_id ]['name'] ) && empty( $block_slugs[ $widget ] ) ) {
					$block_slugs[ $widget ] = $block_widgets[ $widget_id ]['name'];
				}
			}

			if ( ! empty( $block_slugs ) ) {
				$block_ids                                   = $this->get_block_ids_from_slug( $block_slugs );
				$used_blocks['vc']['sidebar'][ $sidebar_id ] = $block_ids[0];
				$used_blocks['el']['sidebar'][ $sidebar_id ] = $block_ids[1];

				// update breadcrumb information
				$breadcrumb_type = $this->get_used_breadcrumbs_type( $block_ids[2] );
				if ( $breadcrumb_type ) {
					if ( ! isset( $used_blocks['breadcrumbs'] ) ) {
						$used_blocks['breadcrumbs'] = array();
					}
					if ( ! isset( $used_blocks['breadcrumbs']['sidebar'] ) ) {
						$used_blocks['breadcrumbs']['sidebar'] = array();
					}
					$used_blocks['breadcrumbs']['sidebar'][ $sidebar_id ] = $breadcrumb_type;
				}
			}

			if ( empty( $used_blocks['vc']['sidebar'][ $sidebar_id ] ) ) {
				unset( $used_blocks['vc']['sidebar'][ $sidebar_id ] );
			}
			if ( empty( $used_blocks['el']['sidebar'][ $sidebar_id ] ) ) {
				unset( $used_blocks['el']['sidebar'][ $sidebar_id ] );
			}
			if ( empty( $used_blocks['breadcrumbs']['sidebar'][ $sidebar_id ] ) ) {
				unset( $used_blocks['breadcrumbs']['sidebar'][ $sidebar_id ] );
			}

			set_theme_mod( '_used_blocks', $used_blocks );
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

			$used_blocks = get_theme_mod( '_used_blocks', array() );
			if ( ! isset( $used_blocks['el'] ) ) {
				$used_blocks['el'] = array();
			}
			if ( ! isset( $used_blocks['vc'] ) ) {
				$used_blocks['vc'] = array();
			}

			if ( isset( $changed['header-type-select'] ) ) {
				if ( empty( $options['header-type-select'] ) ) {
					unset( $used_blocks['el']['header'], $used_blocks['vc']['header'] );
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
				if ( ! empty( $block_slugs[0] ) ) {
					$used_blocks['vc']['all'] = $block_slugs[0];
				} else {
					unset( $used_blocks['vc']['all'] );
				}
				if ( ! empty( $block_slugs[1] ) ) {
					$used_blocks['el']['all'] = $block_slugs[1];
				} else {
					unset( $used_blocks['el']['all'] );
				}

				// update breadcrumb information
				$breadcrumb_type = $this->get_used_breadcrumbs_type( $block_slugs[2] );
				if ( $breadcrumb_type ) {
					if ( ! isset( $used_blocks['breadcrumbs'] ) ) {
						$used_blocks['breadcrumbs'] = array();
					}
					$used_blocks['breadcrumbs']['all'] = $breadcrumb_type;
				} else {
					unset( $used_blocks['breadcrumbs']['all'] );
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
				if ( ! empty( $block_slugs[0] ) ) {
					$used_blocks['vc']['blog'] = $block_slugs[0];
				} else {
					unset( $used_blocks['vc']['blog'] );
				}
				if ( ! empty( $block_slugs[1] ) ) {
					$used_blocks['el']['blog'] = $block_slugs[1];
				} else {
					unset( $used_blocks['el']['blog'] );
				}

				// update breadcrumb information
				$breadcrumb_type = $this->get_used_breadcrumbs_type( $block_slugs[2] );
				if ( $breadcrumb_type ) {
					if ( ! isset( $used_blocks['breadcrumbs'] ) ) {
						$used_blocks['breadcrumbs'] = array();
					}
					$used_blocks['breadcrumbs']['blog'] = $breadcrumb_type;
				} else {
					unset( $used_blocks['breadcrumbs']['blog'] );
				}
			}

			$product_block_ids_e = array();
			$product_block_ids_v = array();
			if ( 'builder' == $options['product-single-content-layout'] && ! empty( $options['product-single-content-builder'] ) ) {
				global $wpdb;
				$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = 'porto_builder' AND post_name = %s", $options['product-single-content-builder'] ) );
				if ( $post_id && get_post_meta( $post_id, '_elementor_edit_mode', true ) && get_post_meta( $post_id, '_elementor_data', true ) ) {
					$product_block_ids_e[] = (int) $post_id;
				} elseif ( $post_id && 'fe' == get_post_meta( $post_id, 'vcv-be-editor', true ) && get_post_meta( $post_id, 'vcvSourceCssFileUrl', true ) ) {
					$product_block_ids_v[] = (int) $post_id;
				}
			}
			$used_blocks['vc']['product'] = $product_block_ids_v;
			$used_blocks['el']['product'] = $product_block_ids_e;
			unset( $used_blocks['breadcrumbs']['product'] );

			$block_slugs = array();
			if ( ! empty( $options['product-content_bottom'] ) ) {
				$block_slugs = array_merge( $block_slugs, explode( ',', $options['product-content_bottom'] ) );
			}
			if ( ! empty( $options['product-tab-block'] ) ) {
				$block_slugs = array_merge( $block_slugs, explode( ',', $options['product-tab-block'] ) );
			}
			if ( ! empty( $block_slugs ) ) {
				$tmp_blocks                   = $this->get_block_ids_from_slug( $block_slugs );
				$used_blocks['vc']['product'] = array_merge( $product_block_ids_v, $tmp_blocks[0] );
				$used_blocks['el']['product'] = array_merge( $product_block_ids_e, $tmp_blocks[1] );

				// update breadcrumb information
				$breadcrumb_type = $this->get_used_breadcrumbs_type( $block_slugs[2] );
				if ( $breadcrumb_type ) {
					if ( ! isset( $used_blocks['breadcrumbs'] ) ) {
						$used_blocks['breadcrumbs'] = array();
					}
					$used_blocks['breadcrumbs']['product'] = $breadcrumb_type;
				}
			}
			set_theme_mod( '_used_blocks', $used_blocks );
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
				return array( array(), array(), array() );
			}
			$result  = array();
			$result1 = array();
			$result2 = array();
			global $wpdb;
			foreach ( $porto_blocks as $s ) {
				$where   = is_numeric( $s ) ? 'ID' : 'post_name';
				$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = 'porto_builder' AND $where = %s", sanitize_text_field( $s ) ) );
				if ( $post_id ) {
					$result2[] = (int) $post_id;
					if ( $this->elementor_support && ( get_post_meta( $post_id, '_elementor_edit_mode', true ) && get_post_meta( $post_id, '_elementor_data', true ) ) ) {
						$result1[] = (int) $post_id;
					}
					if ( $this->vc_support && ( 'fe' == get_post_meta( $post_id, 'vcv-be-editor', true ) && get_post_meta( $post_id, 'vcvSourceCssFileUrl', true ) ) ) {
						$result[] = (int) $post_id;
					}
				}
			}
			return array( array_unique( $result ), array_unique( $result1 ), array_unique( $result2 ) );
		}

		private function get_used_breadcrumbs_type( $block_ids ) {
			if ( empty( $block_ids ) ) {
				return false;
			}
			foreach ( $block_ids as $block_id ) {
				$breadcrumb_type = get_post_meta( (int) $block_id, 'porto_page_header_shortcode_type', true );
				if ( $breadcrumb_type ) {
					return (int) $breadcrumb_type;
				}
			}
			return false;
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
			$used_blocks = get_theme_mod( '_used_blocks', array() );
			if ( ! isset( $used_blocks['el'] ) ) {
				$used_blocks['el'] = array();
			}
			if ( ! isset( $used_blocks['vc'] ) ) {
				$used_blocks['vc'] = array();
			}

			if ( ! empty( $porto_blocks[0] ) ) {
				$used_blocks['vc']['header'] = $porto_blocks[0];
			} else {
				unset( $used_blocks['vc']['header'] );
			}
			if ( ! empty( $porto_blocks[1] ) ) {
				$used_blocks['el']['header'] = $porto_blocks[1];
			} else {
				unset( $used_blocks['el']['header'] );
			}

			// update breadcrumb information
			$breadcrumb_type = $this->get_used_breadcrumbs_type( $porto_blocks[2] );
			if ( $breadcrumb_type ) {
				if ( ! isset( $used_blocks['breadcrumbs'] ) ) {
					$used_blocks['breadcrumbs'] = array();
				}
				$used_blocks['breadcrumbs']['header'] = $breadcrumb_type;
			} else {
				unset( $used_blocks['breadcrumbs']['header'] );
			}

			set_theme_mod( '_used_blocks', $used_blocks );
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
				$blocks     = get_transient( '_porto_menu_blocks' );
				if ( ! $blocks || ! is_array( $blocks ) ) {
					$blocks = array();
				}
				$blocks[] = $block_slug;
				set_transient( '_porto_menu_blocks', $blocks, 3 ); // 3 seconds
			}
		}

		public function add_block_flag_menu( $menu_id ) {
			$blocks = get_transient( '_porto_menu_blocks' );

			$used_blocks = get_theme_mod( '_used_blocks', array() );
			if ( ! isset( $used_blocks['el'] ) ) {
				$used_blocks['el'] = array();
			}
			if ( ! isset( $used_blocks['vc'] ) ) {
				$used_blocks['vc'] = array();
			}

			if ( isset( $used_blocks['el']['menu'] ) ) {
				unset( $used_blocks['el']['menu'][ $menu_id ] );
			}
			if ( isset( $used_blocks['vc']['menu'] ) ) {
				unset( $used_blocks['vc']['menu'][ $menu_id ] );
			}
			if ( isset( $used_blocks['breadcrumbs']['menu'] ) ) {
				unset( $used_blocks['breadcrumbs']['menu'][ $menu_id ] );
			}
			if ( ! empty( $blocks ) ) {
				if ( ! isset( $used_blocks['vc']['menu'] ) ) {
					$used_blocks['vc']['menu'] = array();
				}
				if ( ! isset( $used_blocks['el']['menu'] ) ) {
					$used_blocks['el']['menu'] = array();
				}
				$menu_blocks_el  = array();
				$menu_blocks_vc  = array();
				$menu_blocks_all = array();
				foreach ( $blocks as $el_vc_blocks ) {
					if ( ! empty( $el_vc_blocks[0] ) ) {
						$menu_blocks_vc = array_merge( $menu_blocks_vc, $el_vc_blocks[0] );
					}
					if ( ! empty( $el_vc_blocks[1] ) ) {
						$menu_blocks_el = array_merge( $menu_blocks_el, $el_vc_blocks[1] );
					}
					if ( ! empty( $el_vc_blocks[2] ) ) {
						$menu_blocks_all = array_merge( $menu_blocks_all, $el_vc_blocks[2] );
					}
				}
				if ( ! empty( $menu_blocks_vc ) ) {
					$used_blocks['vc']['menu'][ $menu_id ] = array_unique( $menu_blocks_vc );
				}
				if ( ! empty( $menu_blocks_el ) ) {
					$used_blocks['el']['menu'][ $menu_id ] = array_unique( $menu_blocks_el );
				}

				// update breadcrumb information
				$breadcrumb_type = $this->get_used_breadcrumbs_type( array_unique( $menu_blocks_all ) );
				if ( $breadcrumb_type ) {
					if ( ! isset( $used_blocks['breadcrumbs'] ) ) {
						$used_blocks['breadcrumbs'] = array();
					}
					if ( ! isset( $used_blocks['breadcrumbs']['menu'] ) ) {
						$used_blocks['breadcrumbs']['menu'] = array();
					}
					$used_blocks['breadcrumbs']['menu'][ $menu_id ] = $breadcrumb_type;
				}

				delete_transient( '_porto_menu_blocks' );
			}
			set_theme_mod( '_used_blocks', $used_blocks );
		}
	}
endif;

new Porto_Block_Check;
