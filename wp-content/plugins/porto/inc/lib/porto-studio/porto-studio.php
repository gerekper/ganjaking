<?php
/**
 * Porto Studio
 *
 * @author     Porto Themes
 * @category   Library
 * @since      5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Porto_Studio' ) ) :

	class Porto_Studio {

		/**
		 * total blocks per page
		 */
		private $limit = 30;

		/**
		 * default category id
		 */
		private $default_category_id = 0; // All

		/**
		 * block update period
		 */
		private $update_period = HOUR_IN_SECONDS * 24 * 14; // two weeks

		/**
		 * Page Builder Type
		 *
		 * This should be 'v' if using WPBakery, 'e' if using Elementor Page Builder and 'c' if using Visual Composer.
		 */
		private $page_type = 'v';

		/**
		 * Categories for template builders
		 *
		 * @since 2.3.0
		 */
		private $builder_categories = array(
			'header'  => 1,
			'footer'  => 25,
			'shop'    => 26,
			'product' => 27,
			'type'    => 28,
			'single'  => 29,
			'archive' => 30,
		);

		/**
		 * constructor
		 */
		public function __construct() {

			if ( isset( $_REQUEST['vc_editable'] ) && $_REQUEST['vc_editable'] && isset( $_POST['block_id'] ) ) {
				$vc_template_option_name = '';
				try {
					$vc_template_option_name = vc_manager()->vc()->templatesPanelEditor()->getOptionName();
					add_filter( 'pre_option_' . $vc_template_option_name, array( $this, 'render_frontend_block' ), 10, 3 );
				} catch ( Exception $e ) {
				}
				return;
			}

			if ( wp_doing_ajax() && isset( $_POST['type'] ) ) {
				$this->page_type = sanitize_text_field( $_POST['type'] );
			} elseif ( function_exists( 'get_current_screen' ) && get_current_screen() && get_current_screen()->is_block_editor() && ! ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) ) {
				$this->page_type = 'g';
			}
			add_action( 'wp_ajax_porto_studio_import', array( $this, 'import' ) );
			add_action( 'wp_ajax_nopriv_porto_studio_import', array( $this, 'import' ) );

			add_action( 'wp_ajax_porto_studio_filter_category', array( $this, 'filter_category' ) );
			add_action( 'wp_ajax_nopriv_porto_studio_filter_category', array( $this, 'filter_category' ) );

			add_action( 'wp_ajax_porto_studio_save', array( $this, 'update_custom_meta_fields_in_fronteditor' ) );
			add_action( 'wp_ajax_nopriv_porto_studio_save', array( $this, 'update_custom_meta_fields_in_fronteditor' ) );

			if ( 'post.php' == $GLOBALS['pagenow'] || 'post-new.php' == $GLOBALS['pagenow'] ) {
				if ( porto_is_elementor_preview() ) {
					add_action( 'elementor/editor/footer', array( $this, 'elementor_get_page_content' ) );
					add_action(
						'elementor/editor/after_enqueue_styles',
						function() {
							wp_enqueue_style( 'porto_admin', PORTO_CSS . '/admin.min.css', array( 'porto-studio-fonts' ), PORTO_VERSION, 'all' );
							wp_enqueue_script( 'porto-admin', PORTO_JS . '/admin/admin.min.js', array( 'common', 'jquery', 'media-upload', 'thickbox', 'wp-color-picker' ), PORTO_VERSION, true );
							$this->enqueue();
						},
						30
					);
				} elseif ( function_exists( 'porto_is_vc_preview' ) && porto_is_vc_preview() ) {
					add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ), 1001 );
					add_action( 'admin_footer', array( $this, 'vc_get_page_content' ) );
				} else {
					if ( defined( 'WPB_VC_VERSION' ) ) {
						add_filter( 'vc_nav_controls', array( $this, 'add_studio_control' ) );
						add_filter( 'vc_nav_front_controls', array( $this, 'add_studio_control' ) );
						add_filter( 'vc_nav_controls', array( $this, 'add_edit_area_control' ) );
						add_filter( 'vc_nav_front_controls', array( $this, 'add_edit_area_control' ) );
					}
					add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ), 1001 );
					add_action( 'admin_footer', array( $this, 'get_page_content' ) );
				}
			}
		}

		public function add_edit_area_control( $list ) {
			$add_flag = false;
			if ( doing_filter( 'vc_nav_controls' ) ) {
				$post_id = get_the_ID();
				if ( $post_id && class_exists( 'PortoBuilders' ) ) {
					$builder_type = get_post_meta( $post_id, PortoBuilders::BUILDER_TAXONOMY_SLUG, true );
					if ( 'single' == $builder_type || 'archive' == $builder_type ) {
						$add_flag = true;
					}
				}
			} else {
				$add_flag = true;
			}
			if ( $add_flag ) {
				$list[] = array( 'porto_editor_area', '<li><a href="javascript:;" class="vc_icon-btn porto-editor-area-button" id="porto-editor-area-button" title="' . esc_html__( 'Porto Preview Settings', 'porto' ) . '"><i class="vc-composer-icon fas fa-arrows-alt-h" style="font-weight: 900;"></i></a></li>' );
			}
			return $list;
		}

		public function add_studio_control( $list ) {
			$list[] = array( 'porto_studio', '<li><a href="javascript:;" class="vc_icon-btn porto-studio-editor-button" id="porto-studio-editor-button" title="Porto Studio">Porto Studio</a></li>' );
			return $list;
		}

		public function enqueue() {
			wp_enqueue_style( 'jquery-magnific-popup', PORTO_CSS . '/magnific-popup.min.css', false, '1.1.0', 'all' );
			wp_enqueue_style( 'porto-studio-fonts', '//fonts.googleapis.com/css?family=Poppins%3A400%2C600%2C700&ver=5.2.1' );
			wp_enqueue_script( 'jquery-magnific-popup', PORTO_JS . '/libs/jquery.magnific-popup.min.js', array( 'jquery-core' ), '1.1.0', true );
			if ( wp_script_is( 'isotope' ) ) {
				wp_enqueue_script( 'imagesloaded' );
			} else {
				wp_enqueue_script( 'isotope', PORTO_JS . '/libs/isotope.pkgd.min.js', array( 'jquery-core', 'imagesloaded' ), '3.0.6', true );
			}

			$post_id = false;
			if ( is_singular() ) {
				$post_id = get_the_ID();
			} elseif ( isset( $_GET['post'] ) ) {
				$post_id = (int) $_GET['post'];
			} elseif ( isset( $_GET['post_id'] ) ) {
				$post_id = (int) $_GET['post_id'];
			}

			wp_localize_script(
				'porto-admin',
				'porto_studio',
				array(
					'wpnonce' => wp_create_nonce( 'porto_studio_nonce' ),
					'post_id' => (int) $post_id,
				)
			);
		}

		/**
		 * Import porto blocks in Visual Composer backend editor
		 */
		public function import( $pure_return = false ) {
			check_ajax_referer( 'porto_studio_nonce', 'wpnonce' );

			if ( isset( $_POST['block_id'] ) ) {
				require_once PORTO_PLUGINS . '/importer/importer-api.php';
				$importer_api = new Porto_Importer_API();

				$args = $importer_api->generate_args( false );
				$url  = add_query_arg( $args, $importer_api->get_url( 'blocks_content' ) );
				$url  = add_query_arg( array( 'block_id' => ( (int) $_POST['block_id'] ) ), $url );

				$block = $importer_api->get_response( $url );
				if ( is_wp_error( $block ) || ! $block || ! isset( $block['content'] ) ) {
					if ( $pure_return ) {
						return false;
					}
					echo json_encode( array( 'error' => esc_js( __( 'Security issue found! Please try again later.', 'porto' ) ) ) );
					die();
				}

				$block_content = base64_decode( $block['content'] );

				// process attachments
				if ( isset( $block['images'] ) ) {
					$block_content = $this->process_posts( $block_content, $block['images'] );
				}

				// process contact forms
				if ( isset( $block['posts'] ) ) {
					$block_content = $this->process_posts( $block_content, $block['posts'], false );
				}
				if ( 'e' == $this->page_type ) {
					if ( defined( 'ELEMENTOR_VERSION' ) ) {
						$try_count = 0;
						while ( false !== strpos( $block_content, '"id":"PPPPPPP"' ) && $try_count < 100 ) {
							$block_content = preg_replace( '/"id":"PPPPPPP"/', '"id":"' . Elementor\Utils::generate_random_string() . '"', $block_content, 1 );
							$try_count++;
						}
					}
					$block_content = json_decode( $block_content, true );
				} elseif ( 'c' == $this->page_type ) {
					$block_content = str_replace( 'PORTO_FUNC_PATH', str_replace( '/shortcodes/', '', PORTO_SHORTCODES_URL ), $block_content );
					$block_content = str_replace( 'PORTO_PLUGINS_PATH', esc_url( plugins_url() ), $block_content );
					$block_content = str_replace( 'PORTO_URI', esc_url( PORTO_URI . '/' ), $block_content );
					$block_content = json_decode( $block_content, true );
				}
				$result = array( 'content' => $block_content );
				if ( isset( $block['meta'] ) && $block['meta'] ) {
					$result['meta'] = json_decode( $block['meta'], true );
				}

				if ( $pure_return ) {
					return $result;
				}
				return wp_send_json( $result );
			}
		}

		public function filter_category() {
			check_ajax_referer( 'porto_studio_nonce', 'wpnonce' );
			$count_per_page = $this->limit;
			$page           = isset( $_POST['page'] ) && $_POST['page'] ? (int) $_POST['page'] : 1;

			if ( 'e' == $this->page_type ) {
				$transient_key = 'porto_blocks_e';
			} elseif ( 'c' == $this->page_type ) {
				$transient_key = 'porto_blocks_c';
			} elseif ( 'g' == $this->page_type ) {
				$transient_key = 'porto_blocks_g';
			} else {
				$transient_key = 'porto_blocks';
			}
			$blocks = get_site_transient( $transient_key );
			if ( ! $blocks ) {
				require_once PORTO_PLUGINS . '/importer/importer-api.php';
				$importer_api = new Porto_Importer_API();
				$args         = $importer_api->generate_args( false );
				$args['type'] = $this->page_type;
				$blocks       = $importer_api->get_response( add_query_arg( $args, $importer_api->get_url( 'blocks' ) ) );
				if ( is_wp_error( $blocks ) || ! $blocks ) {
					echo 'error';
					exit;
				}
				set_site_transient( $transient_key, $blocks, $this->update_period );
			}

			if ( isset( $_POST['category_id'] ) && $_POST['category_id'] ) {
				foreach ( $blocks as $index => $block ) {
					$categories = explode( ',', $block['c'] );
					if ( ! in_array( $_POST['category_id'], $categories ) ) {
						unset( $blocks[ $index ] );
					}
				}
			}

			if ( ! empty( $_REQUEST['post_id'] ) ) {
				$post_id      = (int) $_REQUEST['post_id'];
				$builder_type = get_post_meta( $post_id, 'porto_builder_type', true );
				$builder_post_type = get_post_type( $post_id );
			}

			if ( isset( $_POST['demo_filter'] ) && is_array( $_POST['demo_filter'] ) ) {
				$blocked_builders = $this->builder_categories;

				if ( isset( $builder_post_type ) && 'porto_builder' == $builder_post_type && $builder_type && 'block' != $builder_type ) {
					unset( $blocked_builders[ $builder_type ] );
				}

				foreach ( $blocks as $index => $block ) {
					if ( ! in_array( $block['d'], $_POST['demo_filter'] ) ) {
						unset( $blocks[ $index ] );
						continue;
					}
					$categories = explode( ',', $block['c'] );
					if ( ! empty( array_intersect( $blocked_builders, $categories ) ) ) {
						unset( $blocks[ $index ] );
					}
				}
			}

			// title filter
			if ( ! empty( $_POST['text_filter'] ) ) {
				$keywords = trim( sanitize_text_field( $_POST['text_filter'] ) );

				$cats = array();
				if ( empty( $_POST['category_id'] ) ) {
					// get block categories
					if ( 'e' == $this->page_type ) {
						$transient_key = 'porto_block_categories_e';
					} elseif ( 'c' == $this->page_type ) {
						$transient_key = 'porto_block_categories_c';
					} elseif ( 'g' == $this->page_type ) {
						$transient_key = 'porto_block_categories_g';
					} else {
						$transient_key = 'porto_block_categories';
					}
					$block_categories = get_site_transient( $transient_key );
					if ( $block_categories ) {
						foreach ( $block_categories as $category ) {
							if ( false !== stripos( $category['title'], $keywords ) ) {
								$cats[] = $category['id'];
							}
						}
					}
				}

				foreach ( $blocks as $index => $block ) {
					if ( ! empty( $cats ) ) {
						$categories = explode( ',', $block['c'] );
						if ( ! empty( $categories ) && ! empty( array_intersect( $categories, $cats ) ) ) {
							continue;
						}
					}
					if ( false === stripos( $block['t'], $keywords ) && false === stripos( $block['k'], $keywords ) && false === stripos( $block['d'], $keywords ) ) {
						unset( $blocks[ $index ] );
					}
				}
			}

			if ( isset( $builder_post_type ) && 'porto_builder' == $builder_post_type && ! empty( $this->builder_categories[ $builder_type ] ) ) {
				$this->default_category_id = $this->builder_categories[ $builder_type ];
			}
			$this->sanitize_blocks( $blocks );

			if ( ! empty( $blocks ) ) {
				$total_pages = ceil( count( $blocks ) / $count_per_page );
				$blocks      = array_slice( $blocks, ( $page - 1 ) * $count_per_page, $count_per_page );
				$args        = array(
					'block_categories'    => array(),
					'blocks'              => $blocks,
					'default_category_id' => $this->default_category_id,
					'page_type'           => $this->page_type,
					'filter_total_pages'  => $total_pages,
				);
				porto_get_template_part(
					'inc/lib/porto-studio/blocks.tpl',
					null,
					$args
				);
			}
			die();
		}

		/**
		 * Import related posts such as attachments and contact forms
		 */
		private function process_posts( $block_content, $posts, $is_attachment = true ) {
			if ( ! trim( $posts ) ) {
				return $block_content;
			}
			$posts = json_decode( trim( $posts ), true );

			if ( isset( $posts['revslider'] ) ) {
				if ( class_exists( 'RevSlider' ) ) {
					// Importer remote API
					require_once PORTO_PLUGINS . '/importer/importer-api.php';
					$importer_api   = new Porto_Importer_API( $posts['revslider'][0] );
					$demo_file_path = $importer_api->get_remote_demo();
					if ( $demo_file_path && ! is_wp_error( $demo_file_path ) ) {
						$slider   = new RevSlider();
						$imported = $slider->importSliderFromPost( true, false, $demo_file_path . '/' . $posts['revslider'][1] );
						$importer_api->delete_temp_dir();
						if ( is_array( $imported ) && $imported['success'] && isset( $imported['sliderID'] ) ) {
							$block_content = str_replace( '{{{' . $posts['revslider'][2] . '}}}', $imported['sliderID'], $block_content );
						}
					}
				}
				unset( $posts['revslider'] );
			}

			if ( empty( $posts ) ) {
				return $block_content;
			}

			// Check if image is already imported by its ID.
			$id_arr = array();
			foreach ( array_keys( $posts ) as $old_id ) {
				$id_arr[] = ( (int) $_POST['block_id'] ) . '-' . ( (int) $old_id );
			}
			$args = array(
				'posts_per_page' => -1,
				'meta_query'     => array(
					array(
						'key'     => '_porto_studio_id',
						'value'   => $id_arr,
						'compare' => 'IN',
					),
				),
			);
			if ( $is_attachment ) {
				$args['post_type']   = 'attachment';
				$args['post_status'] = 'inherit';
			} else {
				$args['post_type']   = 'wpcf7_contact_form';
				$args['post_status'] = 'publish';
			}
			$query = new \WP_Query( $args );

			if ( $query->have_posts() ) {
				foreach ( $query->posts as $post ) {
					$old_id = str_replace( ( (int) $_POST['block_id'] ) . '-', '', get_post_meta( $post->ID, '_porto_studio_id', true ) );

					if ( 'c' == $this->page_type ) {
						$GLOBALS['porto_studio_attachment'] = $post->ID;
						$block_content                      = preg_replace_callback(
							'|\{\{\{' . ( (int) $old_id ) . ':([^\}]*)\}\}\}|',
							function( $match ) {
								$attachment_id = $GLOBALS['porto_studio_attachment'];
								return '"' . $match[1] . '":"' . esc_url( wp_get_attachment_image_url( $attachment_id, $match[1] ) ) . '"';
							},
							$block_content
						);
						unset( $GLOBALS['porto_studio_attachment'] );
					}

					$block_content = str_replace( '{{{' . ( (int) $old_id ) . '}}}', $post->ID, $block_content );
					unset( $posts[ $old_id ] );
				}
			}

			if ( ! empty( $posts ) ) {

				if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
					define( 'WP_LOAD_IMPORTERS', true ); // we are loading importers
				}

				if ( ! class_exists( 'WP_Importer' ) ) { // if main importer class doesn't exist
					require_once ABSPATH . 'wp-admin/includes/class-wp-importer.php';
				}

				if ( ! class_exists( 'WP_Import' ) ) { // if WP importer doesn't exist
					require_once PORTO_PLUGINS . '/importer/wordpress-importer.php';
				}

				if ( current_user_can( 'edit_posts' ) && class_exists( 'WP_Importer' ) && class_exists( 'WP_Import' ) ) {

					$importer                    = new WP_Import();
					$importer->fetch_attachments = true;

					if ( $is_attachment ) {

						add_filter( 'upload_mimes', array( $this, 'enable_svg_import' ), 99 );

						foreach ( $posts as $old_id => $image_url ) {
							$post_data = array(
								'post_title'   => substr( $image_url, strrpos( $image_url, '/' ) + 1, -4 ),
								'post_content' => '',
								'upload_date'  => date( 'Y-m-d H:i:s' ),
								'post_status'  => 'inherit',
							);
							$import_id = $importer->process_attachment( $post_data, $image_url );
							if ( ! is_wp_error( $import_id ) ) {
								update_post_meta( $import_id, '_porto_studio_id', ( (int) $_POST['block_id'] ) . '-' . ( (int) $old_id ) );

								if ( 'c' == $this->page_type ) {
									$GLOBALS['porto_studio_attachment'] = $import_id;
									$block_content                      = preg_replace_callback(
										'|\{\{\{' . ( (int) $old_id ) . ':([^\}]*)\}\}\}|',
										function( $match ) {
											$attachment_id = $GLOBALS['porto_studio_attachment'];
											return '"' . $match[1] . '":"' . esc_url( wp_get_attachment_image_url( $attachment_id, $match[1] ) ) . '"';
										},
										$block_content
									);
									unset( $GLOBALS['porto_studio_attachment'] );
								}

								$block_content = str_replace( '{{{' . ( (int) $old_id ) . '}}}', $import_id, $block_content );
							} else {
								$block_content = str_replace( '{{{' . ( (int) $old_id ) . '}}}', 0, $block_content );
							}
						}
					} else {
						foreach ( $posts as $old_id => $old_post_data ) {
							$post_data = array(
								'post_title'   => sanitize_text_field( $old_post_data['title'] ),
								'post_type'    => sanitize_text_field( $old_post_data['post_type'] ),
								'post_content' => $old_post_data['content'],
								'upload_date'  => date( 'Y-m-d H:i:s' ),
								'post_status'  => 'publish',
							);
							$post_data = wp_slash( $post_data );
							$import_id = wp_insert_post( $post_data, true );
							if ( ! is_wp_error( $import_id ) ) {
								update_post_meta( $import_id, '_porto_studio_id', ( (int) $_POST['block_id'] ) . '-' . ( (int) $old_id ) );
								if ( isset( $old_post_data['meta'] ) ) {
									foreach ( $old_post_data['meta'] as $meta_key => $meta_value ) {
										update_post_meta( $import_id, $meta_key, $meta_value );
									}
								}
								$block_content = str_replace( '{{{' . ( (int) $old_id ) . '}}}', $import_id, $block_content );
							}
						}
					}
				}
			}

			return $block_content;
		}

		public function get_page_content() {

			// get block categories
			if ( 'e' == $this->page_type ) {
				$transient_key = 'porto_block_categories_e';
			} elseif ( 'c' == $this->page_type ) {
				$transient_key = 'porto_block_categories_c';
			} elseif ( 'g' == $this->page_type ) {
				$transient_key = 'porto_block_categories_g';
			} else {
				$transient_key = 'porto_block_categories';
			}
			$block_categories = get_site_transient( $transient_key );
			if ( ! $block_categories ) {
				require_once PORTO_PLUGINS . '/importer/importer-api.php';
				$importer_api     = new Porto_Importer_API();
				$args             = $importer_api->generate_args( false );
				$args['limit']    = $this->limit;
				$args['type']     = $this->page_type;
				$block_categories = $importer_api->get_response( add_query_arg( $args, $importer_api->get_url( 'block_categories' ) ) );
				if ( is_wp_error( $block_categories ) || ! $block_categories ) {
					return esc_html__( 'Could not connect to the API Server! Please try again later.', 'porto' );
				}
				set_site_transient( $transient_key, $block_categories, $this->update_period );
			}
			// get blocks
			if ( 'e' == $this->page_type ) {
				$transient_key = 'porto_blocks_e';
			} elseif ( 'c' == $this->page_type ) {
				$transient_key = 'porto_blocks_c';
			} elseif ( 'g' == $this->page_type ) {
				$transient_key = 'porto_blocks_g';
			} else {
				$transient_key = 'porto_blocks';
			}
			$blocks = get_site_transient( $transient_key );
			if ( ! $blocks ) {
				if ( ! isset( $importer_api ) ) {
					require_once PORTO_PLUGINS . '/importer/importer-api.php';
					$importer_api = new Porto_Importer_API();
					$args         = $importer_api->generate_args( false );
					$args['type'] = $this->page_type;
				}
				$blocks = $importer_api->get_response( add_query_arg( $args, $importer_api->get_url( 'blocks' ) ) );
				if ( is_wp_error( $blocks ) || ! $blocks ) {
					return esc_html__( 'Could not connect to the API Server! Please try again later.', 'porto' );
				}
				set_site_transient( $transient_key, $blocks, $this->update_period );
			}

			if ( is_array( $block_categories ) ) {
				$post_id = false;
				if ( is_singular() ) {
					$post_id = get_the_ID();
				} elseif ( isset( $_GET['post'] ) ) {
					$post_id = (int) $_GET['post'];
				} elseif ( isset( $_GET['post_id'] ) ) {
					$post_id = (int) $_GET['post_id'];
				}
				if ( $post_id ) {
					$builder_type = get_post_meta( $post_id, 'porto_builder_type', true );
					if ( 'porto_builder' == get_post_type( $post_id ) && $builder_type && 'block' != $builder_type ) {
						$active_cat = false;
						foreach ( $block_categories as $index => $c ) {
							if ( isset( $this->builder_categories[ $builder_type ] ) && (int) $this->builder_categories[ $builder_type ] === (int) $c['id'] ) {
								$active_cat = $c;
								unset( $block_categories[ $index ] );
							} elseif ( in_array( $c['id'], $this->builder_categories ) ) {
								unset( $block_categories[ $index ] );
							}
						}
						if ( false !== $active_cat ) {
							$block_categories          = array_merge( array( $active_cat ), $block_categories );
							$this->default_category_id = (int) $active_cat['id'];
						}
					} else {
						foreach ( $block_categories as $index => $c ) {
							if ( in_array( $c['id'], $this->builder_categories ) ) {
								unset( $block_categories[ $index ] );
							}
						}
					}
				}
			}
			$this->sanitize_blocks( $blocks );
			if ( $this->default_category_id ) {
				$latest_blocks = array();
				foreach ( $blocks as $block ) {
					$categories = explode( ',', $block['c'] );
					if ( in_array( $this->default_category_id, $categories ) ) {
						$latest_blocks[] = $block;
					}
				}
			} else {
				$latest_blocks = $blocks;
			}

			if ( is_array( $block_categories ) /*&& ! empty( $latest_blocks )*/ ) {
				porto_get_template_part(
					'inc/lib/porto-studio/blocks.tpl',
					null,
					array(
						'block_categories'    => $block_categories,
						'blocks'              => array_slice( $latest_blocks, 0, $this->limit ),
						'default_category_id' => $this->default_category_id,
						'page_type'           => $this->page_type,
						'total_pages'         => ceil( count( $blocks ) / $this->limit ),
						'total_count'         => count( $blocks ),
					)
				);
			}

		}

		/**
		 * Sanitize the blocks.
		 * 
		 * @since 2.5.0
		 */
		public function sanitize_blocks( &$blocks ) {
			$exclude_category = $this->builder_categories;
			if ( $this->default_category_id ) {
				$exclude_category = array_diff( $this->builder_categories, array( $this->default_category_id ) );
			}
			foreach ( $blocks as $key => $block ) {
				if ( in_array( $block['c'], $exclude_category ) ) {
					unset( $blocks[$key] );
				}
			}
		}

		public function elementor_get_page_content() {
			$this->page_type = 'e';
			$this->get_page_content();
		}

		public function vc_get_page_content() {
			$this->page_type = 'c';
			$this->get_page_content();
		}

		/**
		 * Import porto blocks in Visual Composer frontend editor
		 */
		public function render_frontend_block( $flag, $option, $default ) {
			if ( isset( $_POST['meta'] ) ) {
				$GLOBALS['porto_studio_meta'] = $_POST['meta'];
				if ( isset( $_POST['meta']['custom_css'] ) ) {
					add_action( 'wp_print_scripts', array( $this, 'print_custom_css_frontend_eidtor' ) );
				}
				if ( isset( $_POST['meta']['custom_js_body'] ) ) {
					add_filter( 'print_footer_scripts', array( $this, 'print_custom_js_frontend_editor' ) );
				}
			}
			if ( isset( $_POST['content'] ) && $_POST['content'] ) {
				return array( '1' => array( 'template' => wp_unslash( $_POST['content'] ) ) );
			}
			return '';
		}

		public function print_custom_css_frontend_eidtor() {
			global $porto_studio_meta;
			if ( $porto_studio_meta && isset( $porto_studio_meta['custom_css'] ) ) {
				$output    = '';
				$first_tag = 'style';

				$output .= '<' . $first_tag . ' data-type="porto-studio-custom-css">';
				$output .= wp_strip_all_tags( wp_unslash( $porto_studio_meta['custom_css'] ) );
				$output .= '</' . $first_tag . '>';

				// @todo Check for wp_add_inline_style posibility
				// @codingStandardsIgnoreLine
				print porto_filter_output( $output );
			}
		}

		public function print_custom_js_frontend_editor( $flag ) {
			global $porto_studio_meta;
			if ( $porto_studio_meta && isset( $porto_studio_meta['custom_js_body'] ) ) {
				echo '<script data-type="porto-studio-custom-js">';
					echo trim( preg_replace( '#<script[^>]*>(.*)</script>#is', '$1', wp_unslash( $porto_studio_meta['custom_js_body'] ) ) );
				echo '</script>';
			}
			unset( $GLOBALS['porto_studio_meta'] );
			return $flag;
		}

		/**
		 * Save post meta fields such as custom css and js in frontend editor
		 */
		public function update_custom_meta_fields_in_fronteditor() {
			check_ajax_referer( 'porto_studio_nonce', 'nonce' );
			if ( isset( $_POST['fields'] ) && $_POST['post_id'] ) {
				$post_id = intval( $_POST['post_id'] );
				foreach ( $_POST['fields'] as $key => $value ) {
					if ( ! $value ) {
						continue;
					}
					$original_value = get_post_meta( $post_id, $key, true );
					if ( strpos( $original_value, $value ) === false ) {
						if ( strpos( $value, $original_value ) !== false ) {
							$original_value = '';
						}
						update_post_meta( $post_id, $key, $original_value . wp_strip_all_tags( wp_unslash( $value ) ) );
					}
				}
				wp_send_json_success();
			}
		}

		public function enable_svg_import( $mimes ) {
			$mimes['svg'] = 'image/svg+xml';
			return $mimes;
		}
	}

	new Porto_Studio;
endif;
