<?php
/**
 * Builder Condition
 *
 * @since 6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Porto_Builder_Condition' ) ) :

	class Porto_Builder_Condition {

		protected $post_id;

		protected $builder_type;

		/**
		 * Constructor
		 */
		public function __construct( $is_page_layout = false ) {
			if ( $is_page_layout ) {
				return;
			}
			if ( defined( 'ELEMENTOR_VERSION' ) && function_exists( 'porto_is_elementor_preview' ) && porto_is_elementor_preview() ) {
				add_action( 'elementor/editor/footer', array( $this, 'builder_condition_template' ) );
				add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueue' ), 30 );
			} else {
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ), 1001 );
				add_action( 'admin_footer', array( $this, 'builder_condition_template' ) );
			}
			if ( defined( 'WPB_VC_VERSION' ) ) {
				add_filter( 'vc_nav_controls', array( $this, 'add_condition_control' ) );
				add_filter( 'vc_nav_front_controls', array( $this, 'add_condition_control' ) );
			}

			add_action( 'wp_ajax_porto_builder_search_posts', array( $this, 'ajax_search' ) );
			add_action( 'wp_ajax_nopriv_porto_builder_search_posts', array( $this, 'ajax_search' ) );

			add_action( 'wp_ajax_porto_builder_save_condition', array( $this, 'save_condition' ) );
			add_action( 'wp_ajax_nopriv_porto_builder_save_condition', array( $this, 'save_condition' ) );
		}

		/**
		 * Enqueue needed scripts
		 */
		public function enqueue() {
			$this->post_id = is_singular() ? get_the_ID() : ( isset( $_GET['post'] ) ? (int) $_GET['post'] : ( isset( $_GET['post_id'] ) ? (int) $_GET['post_id'] : false ) );
			if ( ! $this->post_id ) {
				return;
			}
			$this->builder_type = get_post_meta( $this->post_id, PortoBuilders::BUILDER_TAXONOMY_SLUG, true );
			if ( ! $this->builder_type ) {
				return;
			}
			do_action( 'porto_builder_condition_pre_enqueue' );
			if ( defined( 'ELEMENTOR_VERSION' ) || defined( 'VCV_VERSION' ) ) {
				wp_dequeue_script( 'porto-builder-admin' );
			}
			wp_enqueue_style( 'porto-builder-condition', str_replace( '/shortcodes', '/builders', PORTO_SHORTCODES_URL ) . 'assets/condition.css', array(), PORTO_SHORTCODES_VERSION );
			wp_enqueue_script( 'porto-builder-condition', str_replace( '/shortcodes', '/builders', PORTO_SHORTCODES_URL ) . 'assets/condition.js', array( 'jquery-core' ), PORTO_SHORTCODES_VERSION, true );
			wp_localize_script(
				'porto-builder-condition',
				'porto_builder_condition',
				apply_filters(
					'porto_builder',
					array(
						'nonce' => wp_create_nonce( 'porto-builder-condition-nonce' ),
						'list_url' => esc_url( admin_url( 'edit.php?post_type=' . PortoBuilders::BUILDER_SLUG . '&' . PortoBuilders::BUILDER_TAXONOMY_SLUG . '=' . $this->builder_type ) ),
						'i18n' => array(
							'display_condition' => esc_html__( 'Display Conditions', 'porto-functionality' ),
							'back_to_list'      => esc_html__( 'Back To List', 'porto-functionality' ),
						),
					)
				)
			);
		}

		public function builder_condition_template() {
			$post_id      = $this->post_id;
			$builder_type = $this->builder_type;
			include_once PORTO_BUILDERS_PATH . 'views/condition_template.php';
		}

		public function ajax_search( $direct_call = false ) {
			if ( ! $direct_call ) {
				check_ajax_referer( 'porto-builder-condition-nonce', 'nonce' );
			}
			$query      = sanitize_text_field( $_REQUEST['query'] );
			$type_query = '';
			$type       = '';
			if ( ! empty( $_REQUEST['post_type'] ) ) {
				$search_type = $_REQUEST['post_type'];
				if ( 0 === strpos( $search_type, 'single/' ) ) {
					$type        = 'post';
					$search_type = str_replace( 'single/', '', $search_type );
				} else {
					$type        = 'taxonomy';
					$search_type = str_replace( 'taxonomy/', '', $search_type );
				}
				if ( 'post' == $type && post_type_exists( $search_type ) ) {
					$type       = 'post';
					$type_query = ' AND post_type="' . sanitize_text_field( $search_type ) . '"';
				} elseif ( 'taxonomy' == $type && taxonomy_exists( $search_type ) ) {
					$type = 'taxonomy';
				}
			}

			$response = array();
			if ( $search_type && $type ) {
				if ( 'post' == $type ) {
					global $wpdb;
					$results = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT ID AS id, post_title AS title
								FROM {$wpdb->posts} 
								WHERE post_status = 'publish' AND ( ID = %d OR post_title LIKE '%%%s%%' )" . $type_query,
							(int) $search_value > 0 ? (int) $search_value : -1,
							$wpdb->esc_like( stripslashes( $query ) )
						),
						ARRAY_A
					);

					if ( is_array( $results ) && ! empty( $results ) ) {
						foreach ( $results as $value ) {
							$response[] = array(
								'id'    => intval( $value['id'] ),
								'value' => esc_html( $value['title'] ),
							);
						}
					}
				} else {
					$cats = get_terms(
						array(
							'taxonomy'   => $search_type,
							'hide_empty' => false,
							'search'     => sanitize_text_field( $query ),
						)
					);
					if ( is_array( $cats ) && ! empty( $cats ) ) {
						foreach ( $cats as $value ) {
							$response[] = array(
								'id'    => intval( $value->term_id ),
								'value' => esc_html( $value->name ),
							);
						}
					}
				}
			}

			wp_send_json( array( 'suggestions' => $response ) );
		}

		public function save_condition( $direct_call = false, $post_id = false ) {
			if ( $direct_call ) {
				if ( ! $post_id ) {
					return false;
				}
			} else {
				check_ajax_referer( 'porto-builder-condition-nonce' );
				if ( empty( $_POST['post_id'] ) ) {
					wp_send_json_error();
					return;
				}
				$post_id = (int) $_POST['post_id'];
			}

			$conditions   = array();
			$builder_type = get_post_meta( $post_id, PortoBuilders::BUILDER_TAXONOMY_SLUG, true );

			if ( empty( $builder_type ) && ! $direct_call ) {
				wp_send_json_error();
			}

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
							if ( 'single' == $type && false === strpos( $o_type, 'single/' ) ) {
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

			/* add new conditions */
			if ( ! empty( $_POST['type'] ) ) {
				foreach ( $_POST['type'] as $index => $type ) {
					$object_type = ! empty( $_POST['object_type'] ) && ! empty( $_POST['object_type'][ $index ] ) ? sanitize_text_field( $_POST['object_type'][ $index ] ) : '';
					$object_id   = ! empty( $_POST['object_id'] ) && ! empty( $_POST['object_id'][ $index ] ) ? sanitize_text_field( $_POST['object_id'][ $index ] ) : '';
					$object_name = ! empty( $_POST['object_name'] ) && ! empty( $_POST['object_name'][ $index ] ) ? sanitize_text_field( $_POST['object_name'][ $index ] ) : '';

					if ( $object_id && $type ) {
						if ( 0 === strpos( $object_type, 'taxonomy/' ) ) {
							$p_type = 'taxonomy';
						} else {
							$p_type = 'post';
						}
						if ( 'post' == $p_type ) {
							update_post_meta( $object_id, '_porto_builder_' . $builder_type, $post_id );
						} elseif ( 'taxonomy' == $p_type ) {
							if ( 'single' == $type ) {
								$key = '_porto_builder_single_' . $builder_type;
							} else {
								$key = '_porto_builder_' . $builder_type;
							}
							update_term_meta( $object_id, $key, $post_id );
						}
					} else {
						$builder_conditions = get_theme_mod( 'builder_conditions', array() );
						if ( ! isset( $builder_conditions[ $builder_type ] ) ) {
							$builder_conditions[ $builder_type ] = array();
						}

						if ( ! $object_type && $type && in_array( $builder_type, array( 'product', 'shop' ) ) ) {
							$object_type = $type . '/' . $builder_type;
						}
						if ( $object_type ) {
							if ( 'single' == $type && false === strpos( $object_type, 'single/' ) ) {
								$builder_conditions[ $builder_type ][ 'single/' . $object_type ] = $post_id;
							} else {
								$builder_conditions[ $builder_type ][ $object_type ] = $post_id;
							}
						} elseif ( $type ) {
							$builder_conditions[ $builder_type ][ $type ] = $post_id;
						} else {
							$builder_conditions[ $builder_type ]['all'] = $post_id;
						}
						set_theme_mod( 'builder_conditions', $builder_conditions );
					}
					$conditions[] = array( $type, $object_type, $object_id, $object_name );
				}
			}
			if ( false !== strpos( $builder_type, 'block' ) ) {
				if ( count( $conditions ) ) {
					update_post_meta( $post_id, '_porto_block_pos', $builder_type );
				} else {
					delete_post_meta( $post_id, '_porto_block_pos' );
				}
			}
			update_post_meta( $post_id, '_porto_builder_conditions', $conditions );

			if ( ! $direct_call ) {
				wp_send_json_success();
			}
		}

		public function add_condition_control( $list ) {
			$list[] = array( 'porto_builder_condition', '<li><a href="javascript:;" class="vc_icon-btn porto-condition-button" id="porto-condition-button" title="' . esc_attr__( 'Porto Builder Condition', 'porto-functionality' ) . '"><i class="fas fa-network-wired"></i></a></li>' );
			return $list;
		}
	}
endif;
