<?php
/**
 * Porto Admin tools to clear transient and reset information about posts and terms which used blocks
 *
 * @since 6.1.0
 */
class Porto_Admin_Tools {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 12 );

		if ( ! current_user_can( 'administrator' ) || ! isset( $_GET['page'] ) || 'porto-tools' != $_GET['page'] ) {
			return;
		}
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ), 30 );
	}

	public function refresh_blocks() {
		$used_blocks = $this->reset_in_posts();
		$used_blocks = $this->reset_in_theme_options( $used_blocks );
		$used_blocks = $this->reset_in_widgets( $used_blocks );
		$used_blocks = $this->reset_in_menus( $used_blocks );
		set_theme_mod( '_used_blocks', $used_blocks );
	}

	public function admin_menu() {
		add_submenu_page( 'porto', __( 'Tools', 'porto' ), __( 'Tools', 'porto' ), 'administrator', 'porto-tools', array( $this, 'tools_page' ) );
	}

	public function enqueue() {
		wp_enqueue_style( 'porto-setup', PORTO_URI . '/inc/admin/setup_wizard/assets/css/style.css', array( 'porto_admin' ), PORTO_VERSION );
	}

	public function tools_page() {
		if ( ! current_user_can( 'administrator' ) || ! isset( $_GET['page'] ) || 'porto-tools' != $_GET['page'] ) {
			return;
		}

		$result_success = true;
		$message        = '';
		if ( ! empty( $_GET['action'] ) ) {
			if ( empty( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'porto-tools' ) ) {
				wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'porto' ) );
			}

			if ( 'clear_plugin_transient' == $_GET['action'] ) {
				delete_site_transient( 'porto_plugins' );
				$message = __( 'Plugin transients cleared', 'porto' );
			} elseif ( 'clear_studio_transient' == $_GET['action'] ) {
				delete_site_transient( 'porto_blocks' );
				delete_site_transient( 'porto_blocks_e' );
				delete_site_transient( 'porto_blocks_c' );
				delete_site_transient( 'porto_blocks_g' );
				delete_site_transient( 'porto_block_categories' );
				delete_site_transient( 'porto_block_categories_e' );
				delete_site_transient( 'porto_block_categories_c' );
				delete_site_transient( 'porto_block_categories_g' );
				$message = __( 'Porto Studio transients cleared', 'porto' );
			} elseif ( 'compile_css' == $_GET['action'] ) {
				$result = porto_compile_css( 'shortcodes' );
				porto_compile_css( 'bootstrap_rtl' );
				porto_compile_css( 'bootstrap' );
				do_action( 'porto_admin_save_theme_settings' );
				if ( ! $result ) {
					$result_success = false;
					$message        = __( 'Shortcode CSS compiled failed.', 'porto' );
				} else {
					$message = __( 'All CSS compiled successfully.', 'porto' );
				}
			} elseif ( 'refresh_blocks' == $_GET['action'] ) {
				$this->refresh_blocks();
				$message = __( 'Refreshed successfully.', 'porto' );
			} elseif ( 'refresh_conditions' == $_GET['action'] && defined( 'PORTO_BUILDERS_PATH' ) ) {
				$query = new WP_Query(
					array(
						'post_type'      => 'porto_builder',
						'post_status'    => 'publish',
						'posts_per_page' => -1,
						'fields'         => 'ids',
						'meta_query'     => array(
							array(
								'key'     => '_porto_builder_conditions',
								'compare' => 'EXISTS',
							),
						),
					)
				);
				if ( is_array( $query->posts ) && ! empty( $query->posts ) ) {
					require_once PORTO_BUILDERS_PATH . 'lib/class-condition.php';
					$cls = new Porto_Builder_Condition();

					set_theme_mod( 'builder_conditions', array() );

					foreach ( $query->posts as $post_id ) {
						$conditions = get_post_meta( $post_id, '_porto_builder_conditions', true );
						if ( empty( $conditions ) ) {
							continue;
						}
						$_POST['type']        = array();
						$_POST['object_type'] = array();
						$_POST['object_id']   = array();
						$_POST['object_name'] = array();
						foreach ( $conditions as $index => $condition ) {
							if ( ! is_array( $condition ) || 4 !== count( $condition ) ) {
								continue;
							}
							$_POST['type'][]        = $condition[0];
							$_POST['object_type'][] = $condition[1];
							$_POST['object_id'][]   = $condition[2];
							$_POST['object_name'][] = $condition[3];
						}
						$cls->save_condition( true, (int) $post_id );
					}
					unset( $_POST['type'], $_POST['object_type'], $_POST['object_id'], $_POST['object_name'] );
				}

				$message = __( 'Refreshed successfully.', 'porto' );
			}
		}
		porto_get_template_part(
			'inc/admin/admin_pages/tools',
			null,
			array(
				'result_success' => $result_success,
				'result_message' => $message,
			)
		);
	}

	private function reset_in_posts() {
		global $wpdb;
		$posts = $wpdb->get_results( "SELECT post_id, meta_key, meta_value FROM $wpdb->postmeta WHERE meta_key IN ('content_top', 'content_inner_top', 'content_inner_bottom', 'content_bottom', 'product_custom_block', 'banner_block') AND meta_value != ''" );

		$taxonomies = array(
			'product_cat',
			'category',
			'portfolio_cat',
			'member_cat',
		);
		if ( empty( $posts ) ) {
			$posts = array();
		}
		foreach ( $taxonomies as $taxonomy ) {
			$table_name = $wpdb->prefix . $taxonomy . 'meta';
			if ( ! $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) == $table_name ) {
				continue;
			}
			$terms = $wpdb->get_results( 'SELECT ' . $taxonomy . '_id, meta_key, meta_value FROM ' . esc_sql( $table_name ) . " WHERE meta_key IN ('content_top', 'content_inner_top', 'content_inner_bottom', 'content_bottom', 'product_custom_block', 'banner_block') AND meta_value != ''" );
			if ( ! empty( $terms ) ) {
				$posts = array_merge( $posts, $terms );
			}
		}

		$used_blocks = array(
			'el' => array(
				'post' => array(),
			),
			'vc' => array(
				'post' => array(),
			),
		);

		foreach ( $posts as $p ) {
			$block_slug  = '';
			$object_type = '';
			$object_id   = '';
			$type        = '';
			if ( isset( $p->post_id ) ) {
				$object_type = 'post';
				$type        = 'post';
				$object_id   = (int) $p->post_id;
			} else {
				foreach ( $taxonomies as $taxonomy ) {
					if ( isset( $p->{ $taxonomy . '_id' } ) ) {
						$object_type = $taxonomy;
						$type        = 'term';
						$object_id   = (int) $p->{ $taxonomy . '_id' };
						break;
					}
				}
			}
			if ( ! $object_type ) {
				continue;
			}

			if ( 'banner_block' == $p->meta_key ) {
				if ( 'post' == $object_type ) {
					$banner_type = get_post_meta( $object_id, 'banner_type', true );
				} else {
					$banner_type = get_metadata( $object_type, $object_id, 'banner_type', true );
				}
				if ( 'banner_block' == $banner_type ) {
					$block_slug = $p->meta_value;
				}
			} else {
				$block_slug = $p->meta_value;
			}
			if ( $block_slug ) {
				$blocks = $this->get_block_ids_from_slug( array( $block_slug ) );
				if ( ! empty( $blocks[0] ) ) {
					if ( ! isset( $used_blocks['vc'][ $type ][ $object_id ] ) ) {
						$used_blocks['vc'][ $type ][ $object_id ] = array();
					}
					$used_blocks['vc'][ $type ][ $object_id ][] = $blocks[0][0];
				}
				if ( ! empty( $blocks[1] ) ) {
					if ( ! isset( $used_blocks['el'][ $type ][ $object_id ] ) ) {
						$used_blocks['el'][ $type ][ $object_id ] = array();
					}
					$used_blocks['el'][ $type ][ $object_id ][] = $blocks[1][0];
				}

				// update breadcrumb information
				$breadcrumb_type = $this->get_used_breadcrumbs_type( $blocks[2] );
				if ( $breadcrumb_type ) {
					if ( ! isset( $used_blocks['breadcrumbs'] ) ) {
						$used_blocks['breadcrumbs'] = array();
					}
					if ( ! isset( $used_blocks['breadcrumbs'][ $type ] ) ) {
						$used_blocks['breadcrumbs'][ $type ] = array();
					}
					$used_blocks['breadcrumbs'][ $type ][ $object_id ] = $breadcrumb_type;
				}
			}
		}

		// search porto block in post contents
		$posts = $wpdb->get_results( "SELECT ID, post_content FROM $wpdb->posts WHERE post_type NOT IN ('revision', 'attachment', 'shop_order', 'nav_menu_item', 'customize_changeset', 'elementor_library') AND post_status = 'publish' AND post_content LIKE '%[porto_block %'" );
		if ( ! empty( $posts ) ) {
			foreach ( $posts as $p ) {
				$post_id = (int) $p->ID;
				preg_match_all( '/\[porto_block\s[^]]*(id|name)="([^"]*)"/', $p->post_content, $matches );
				if ( ! empty( $matches[2] ) ) {
					$blocks = $this->get_block_ids_from_slug( $matches[2] );
					if ( ! empty( $blocks[0] ) ) {
						if ( ! isset( $used_blocks['vc']['post_c'][ $post_id ] ) ) {
							$used_blocks['vc']['post_c'][ $post_id ] = array();
						}
						$used_blocks['vc']['post_c'][ $post_id ] = array_merge( $used_blocks['vc']['post_c'][ $post_id ], $blocks[0] );
					}
					if ( ! empty( $blocks[1] ) ) {
						if ( ! isset( $used_blocks['el']['post_c'][ $post_id ] ) ) {
							$used_blocks['el']['post_c'][ $post_id ] = array();
						}
						$used_blocks['el']['post_c'][ $post_id ] = array_merge( $used_blocks['el']['post_c'][ $post_id ], $blocks[1] );
					}

					// update breadcrumb information
					$breadcrumb_type = $this->get_used_breadcrumbs_type( $blocks[2] );
					if ( $breadcrumb_type ) {
						if ( ! isset( $used_blocks['breadcrumbs'] ) ) {
							$used_blocks['breadcrumbs'] = array();
						}
						if ( ! isset( $used_blocks['breadcrumbs']['post_c'] ) ) {
							$used_blocks['breadcrumbs']['post_c'] = array();
						}
						$used_blocks['breadcrumbs']['post_c'][ $post_id ] = $breadcrumb_type;
					}
				}
			}
		}

		return $used_blocks;
	}

	private function reset_in_theme_options( $used_blocks ) {
		global $porto_settings;
		if ( ! empty( $porto_settings['header-type-select'] ) ) {
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

						if ( ! empty( $porto_blocks[0] ) ) {
							$used_blocks['vc']['header'] = $porto_blocks[0];
						}
						if ( ! empty( $porto_blocks[1] ) ) {
							$used_blocks['el']['header'] = $porto_blocks[1];
						}

						// update breadcrumb information
						$breadcrumb_type = $this->get_used_breadcrumbs_type( $porto_blocks[2] );
						if ( $breadcrumb_type ) {
							if ( ! isset( $used_blocks['breadcrumbs'] ) ) {
								$used_blocks['breadcrumbs'] = array();
							}
							$used_blocks['breadcrumbs']['header'] = $breadcrumb_type;
						}
					}
				}
			}
		}

		$html_blocks = array( 'top', 'banner', 'content-top', 'content-inner-top', 'content-inner-bottom', 'content-bottom', 'bottom' );
		$block_slugs = array();
		foreach ( $html_blocks as $b ) {
			if ( ! empty( $porto_settings[ 'html-' . $b ] ) && preg_match( '/\[porto_block\s[^]]*(id|name)="([^"]*)"/', $porto_settings[ 'html-' . $b ], $matches ) && isset( $matches[2] ) && $matches[2] ) {
				$block_slugs[] = trim( $matches[2] );
			}
		}
		$block_slugs = $this->get_block_ids_from_slug( $block_slugs );
		if ( ! empty( $block_slugs[0] ) ) {
			$used_blocks['vc']['all'] = $block_slugs[0];
		}
		if ( ! empty( $block_slugs[1] ) ) {
			$used_blocks['el']['all'] = $block_slugs[1];
		}
		// update breadcrumb information
		$breadcrumb_type = $this->get_used_breadcrumbs_type( $block_slugs[2] );
		if ( $breadcrumb_type ) {
			if ( ! isset( $used_blocks['breadcrumbs'] ) ) {
				$used_blocks['breadcrumbs'] = array();
			}
			$used_blocks['breadcrumbs']['all'] = $breadcrumb_type;
		}

		$blog_blocks = array( 'blog-content_top', 'blog-content_inner_top', 'blog-content_inner_bottom', 'blog-content_bottom' );
		$block_slugs = array();
		foreach ( $blog_blocks as $b ) {
			if ( ! empty( $porto_settings[ $b ] ) ) {
				$arr = explode( ',', $porto_settings[ $b ] );
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
		}
		if ( ! empty( $block_slugs[1] ) ) {
			$used_blocks['el']['blog'] = $block_slugs[1];
		}
		// update breadcrumb information
		$breadcrumb_type = $this->get_used_breadcrumbs_type( $block_slugs[2] );
		if ( $breadcrumb_type ) {
			if ( ! isset( $used_blocks['breadcrumbs'] ) ) {
				$used_blocks['breadcrumbs'] = array();
			}
			$used_blocks['breadcrumbs']['blog'] = $breadcrumb_type;
		}

		$product_block_ids_e = array();
		$product_block_ids_v = array();
		if ( isset( $porto_settings['product-single-content-layout'] ) && 'builder' == $porto_settings['product-single-content-layout'] && ! empty( $porto_settings['product-single-content-builder'] ) ) {
			global $wpdb;
			$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = 'porto_builder' AND post_name = %s", $porto_settings['product-single-content-builder'] ) );
			if ( $post_id && get_post_meta( $post_id, '_elementor_edit_mode', true ) && get_post_meta( $post_id, '_elementor_data', true ) ) {
				$product_block_ids_e[] = (int) $post_id;
			} elseif ( $post_id && 'fe' == get_post_meta( $post_id, 'vcv-be-editor', true ) && get_post_meta( $post_id, 'vcvSourceCssFileUrl', true ) ) {
				$product_block_ids_v[] = (int) $post_id;
			}
		}

		$block_slugs = array();
		if ( ! empty( $porto_settings['product-content_bottom'] ) ) {
			$block_slugs = array_merge( $block_slugs, explode( ',', $porto_settings['product-content_bottom'] ) );
		}
		if ( ! empty( $porto_settings['product-tab-block'] ) ) {
			$block_slugs = array_merge( $block_slugs, explode( ',', $porto_settings['product-tab-block'] ) );
		}
		if ( ! empty( $block_slugs ) ) {
			$tmp_blocks                   = $this->get_block_ids_from_slug( $block_slugs );
			$used_blocks['vc']['product'] = array_merge( $product_block_ids_v, $tmp_blocks[0] );
			$used_blocks['el']['product'] = array_merge( $product_block_ids_e, $tmp_blocks[1] );
			// update breadcrumb information
			$breadcrumb_type = $this->get_used_breadcrumbs_type( $tmp_blocks[2] );
			if ( $breadcrumb_type ) {
				if ( ! isset( $used_blocks['breadcrumbs'] ) ) {
					$used_blocks['breadcrumbs'] = array();
				}
				$used_blocks['breadcrumbs']['product'] = $breadcrumb_type;
			}
		}
		return $used_blocks;
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

	private function reset_in_widgets( $used_blocks ) {
		$sidebars                     = get_option( 'sidebars_widgets' );
		$block_widgets                = get_option( 'widget_block-widget', array() );
		$used_blocks['el']['sidebar'] = array();
		$used_blocks['vc']['sidebar'] = array();

		foreach ( $sidebars as $sidebar_id => $sidebar ) {
			if ( empty( $sidebar ) || ! is_array( $sidebar ) ) {
				continue;
			}
			$block_slugs = array();
			foreach ( $sidebar as $widget ) {
				$widget_type = trim( substr( $widget, 0, strrpos( $widget, '-' ) ) );
				$widget_id   = str_replace( 'block-widget-', '', $widget );
				if ( 'block-widget' == $widget_type && ! empty( $block_widgets[ $widget_id ] ) && ! empty( $block_widgets[ $widget_id ]['name'] ) && empty( $block_slugs[ $widget ] ) ) {
					$block_slugs[ $widget ] = $block_widgets[ $widget_id ]['name'];
				}
			}
			if ( ! empty( $block_slugs ) ) {
				$block_ids = $this->get_block_ids_from_slug( $block_slugs );
				if ( ! empty( $block_ids[0] ) ) {
					$used_blocks['vc']['sidebar'][ $sidebar_id ] = $block_ids[0];
				}
				if ( ! empty( $block_ids[1] ) ) {
					$used_blocks['el']['sidebar'][ $sidebar_id ] = $block_ids[1];
				}
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
		}
		return $used_blocks;
	}

	private function reset_in_menus( $used_blocks ) {
		global $wpdb;
		$posts = $wpdb->get_results( "SELECT post_id, meta_value FROM $wpdb->postmeta WHERE meta_key = '_menu_item_block' AND meta_key != ''" );
		if ( ! empty( $posts ) ) {
			$used_blocks['el']['menu'] = array();
			$used_blocks['vc']['menu'] = array();
			foreach ( $posts as $p ) {
				$menu_item_id = (int) $p->post_id;
				$menu_id      = wp_get_post_terms( $menu_item_id, 'nav_menu', array( 'fields' => 'ids' ) );
				if ( ! empty( $menu_id ) ) {
					$menu_id     = $menu_id[0];
					$block_slugs = $this->get_block_ids_from_slug( array( $p->meta_value ) );
					if ( ! empty( $block_slugs[0] ) ) {
						if ( ! isset( $used_blocks['vc']['menu'][ $menu_id ] ) ) {
							$used_blocks['vc']['menu'][ $menu_id ] = array();
						}
						$used_blocks['vc']['menu'][ $menu_id ] = array_merge( $used_blocks['vc']['menu'][ $menu_id ], $block_slugs[0] );
					}
					if ( ! empty( $block_slugs[1] ) ) {
						if ( ! isset( $used_blocks['el']['menu'][ $menu_id ] ) ) {
							$used_blocks['el']['menu'][ $menu_id ] = array();
						}
						$used_blocks['el']['menu'][ $menu_id ] = array_merge( $used_blocks['el']['menu'][ $menu_id ], $block_slugs[1] );
					}
					// update breadcrumb information
					$breadcrumb_type = $this->get_used_breadcrumbs_type( $block_slugs[2] );
					if ( $breadcrumb_type ) {
						if ( ! isset( $used_blocks['breadcrumbs'] ) ) {
							$used_blocks['breadcrumbs'] = array();
						}
						if ( ! isset( $used_blocks['breadcrumbs']['menu'] ) ) {
							$used_blocks['breadcrumbs']['menu'] = array();
						}
						$used_blocks['breadcrumbs']['menu'][ $menu_id ] = $breadcrumb_type;
					}
				}
			}

			if ( ! empty( $used_blocks['el']['menu'] ) ) {
				foreach ( $used_blocks['el']['menu'] as $menu_id => $menu_blocks ) {
					if ( empty( $menu_blocks ) ) {
						continue;
					}
					$used_blocks['el']['menu'][ $menu_id ] = array_unique( $menu_blocks );
				}
			}
			if ( ! empty( $used_blocks['vc']['menu'] ) ) {
				foreach ( $used_blocks['vc']['menu'] as $menu_id => $menu_blocks ) {
					if ( empty( $menu_blocks ) ) {
						continue;
					}
					$used_blocks['vc']['menu'][ $menu_id ] = array_unique( $menu_blocks );
				}
			}
		}
		return $used_blocks;
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
				if ( defined( 'ELEMENTOR_VERSION' ) && ( get_post_meta( $post_id, '_elementor_edit_mode', true ) && get_post_meta( $post_id, '_elementor_data', true ) ) ) {
					$result1[] = (int) $post_id;
				}
				if ( defined( 'VCV_VERSION' ) && ( 'fe' == get_post_meta( $post_id, 'vcv-be-editor', true ) && get_post_meta( $post_id, 'vcvSourceCssFileUrl', true ) ) ) {
					$result[] = (int) $post_id;
				}
			}
		}
		return array( array_unique( $result ), array_unique( $result1 ), array_unique( $result2 ) );
	}
}

new Porto_Admin_Tools;
