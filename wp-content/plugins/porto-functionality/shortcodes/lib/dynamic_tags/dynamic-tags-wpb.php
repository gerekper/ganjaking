<?php
/**
 * Porto Wpb Dynamic Tags class
 *
 * @author     P-Themes
 * @since      2.3.0
 */

defined( 'ABSPATH' ) || die;

if ( ! class_exists( 'Porto_Wpb_Dynamic_Tags' ) ) {
	class Porto_Wpb_Dynamic_Tags {

		/**
		 * Global Instance Objects
		 *
		 * @var array $instances
		 * @since 2.3.0
		 * @access private
		 */
		private static $instance = null;

		public static function get_instance() {
			if ( ! self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		public function __construct() {
			if ( class_exists( 'ACF' ) ) {
				require_once PORTO_SHORTCODES_LIB . 'dynamic_tags/class-porto-func-acf.php';
			}

			add_filter( 'porto_wpb_editor_vars', array( $this, 'add_wpb_dynamic_field' ) );
			// Dynamic vars on frontend edit
			if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
				add_action( 'wp_enqueue_scripts', array( $this, 'dynamic_register_wpb_vars' ), 2000 );
			}
			// Dynamic vars on backend edit
			if ( ( isset( $_REQUEST['action'] ) && 'edit' == $_REQUEST['action'] ) && isset( $_REQUEST['post'] ) ) {
				add_action( 'vc_backend_editor_render', array( $this, 'dynamic_register_wpb_vars' ), 2000 );
			}
		}

		/**
		 * Add dynamic field vars
		 * Get Bakery Dynamic Fields By Type( Field, Link, Image )
		 *
		 * @since 2.3.0
		 */
		public function dynamic_wpb_tags( $dynamic_type_heading, $index = '', $description = '' ) {
			$dynamic_type = $dynamic_type_heading;
			if ( ! empty( $index ) ) {
				$dynamic_type .= '_' . $index;
			}
			$result = array(
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Enable Dynamic ' . strtoupper( $index . ' ' . $dynamic_type_heading ), 'porto-functionality' ),
					'param_name'  => 'enable_' . $dynamic_type . '_dynamic',
					'description' => __( $description, 'porto-functionality' ),
				),
			);
			// Acf Field
			$acf_field = array();
			if ( class_exists( 'ACF' ) ) {
				$acf_field = array( __( 'Advanced Custom Field', 'porto-functionality' ) => 'acf' );
			}

			// Woocommerce Field
			$woo_field = array();
			if ( 'field' == $dynamic_type_heading && class_exists( 'WooCommerce' ) ) {
				if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
					$post_id = ! empty( $_REQUEST['vc_post_id'] ) ? $_REQUEST['vc_post_id'] : '';
				}
				if ( empty( $post_id ) ) {
					$post_id = ! empty( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : '';
				}
				if ( ! empty( $post_id ) ) {
					if ( PortoBuilders::BUILDER_SLUG == get_post_type( $post_id ) && 'product' == get_post_meta( $post_id, PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) {
						$woo_field = array( __( 'WooCommerce', 'porto-functionality' ) => 'woocommerce' );
					}
				}
			}
			// Term Meta
			$term_field = array();
			if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
				$post_id = ! empty( $_REQUEST['vc_post_id'] ) ? $_REQUEST['vc_post_id'] : '';
			}
			if ( empty( $post_id ) ) {
				$post_id = ! empty( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : '';
			}
			if ( ! empty( $post_id ) ) {
				if ( PortoBuilders::BUILDER_SLUG == get_post_type( $post_id ) && ( 'archive' == get_post_meta( $post_id, PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) || 'shop' == get_post_meta( $post_id, PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) ) {
					$term_field = array( __( 'Term Meta', 'porto-functionality' ) => 'term_meta' );
				}
			}
			// Post Link
			$post_info = array();
			if ( 'link' == $dynamic_type_heading ) {
				$post_info = array( __( 'Post Link', 'porto-functionality' ) => 'post_link' );
			} elseif ( 'field' == $dynamic_type_heading ) {
				$post_info = array(
					__( 'Post or Author Info', 'porto-functionality' ) => 'post_info',
					__( 'Taxonomy', 'porto-functionality' )            => 'taxonomy',
				);
			}

			if ( 'field' == $dynamic_type_heading || 'link' == $dynamic_type_heading ) {
				$result = array_merge(
					$result,
					array(
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Dynamic source', 'porto-functionality' ),
							'param_name' => $dynamic_type . '_dynamic_source',
							'value'      => array_merge(
								array(
									__( 'Select Source...', 'porto-functionality' ) => '',
									__( 'Meta Box Field', 'porto-functionality' )   => 'meta_box',
									__( 'Meta Field', 'porto-functionality' )       => 'meta_field',
								),
								$post_info,
								$term_field,
								$acf_field,
								$woo_field
							),
							'dependency' => array(
								'element'   => 'enable_' . $dynamic_type . '_dynamic',
								'not_empty' => true,
							),
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Dynamic Content', 'porto-functionality' ),
							'param_name' => $dynamic_type . '_dynamic_content_meta_' . $dynamic_type,
							'value'      => array(),
							'dependency' => array(
								'element' => $dynamic_type . '_dynamic_source',
								'value'   => 'meta_field',
							),
						),
					)
				);
			} elseif ( 'image' == $dynamic_type_heading ) {
				$result = array_merge(
					$result,
					array(
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Dynamic Source', 'porto-functionality' ),
							'param_name' => $dynamic_type . '_dynamic_source',
							'value'      => array_merge(
								array(
									__( 'Select Source...', 'porto-functionality' ) => '',
									__( 'Page of Post Info', 'porto-functionality' ) => 'post_info',
									__( 'Meta Box Field', 'porto-functionality' ) => 'meta_box',
								),
								$term_field,
								$acf_field
							),
							'dependency' => array(
								'element'   => 'enable_' . $dynamic_type . '_dynamic',
								'not_empty' => true,
							),
						),
					)
				);
			}
			$result = array_merge(
				$result,
				array(
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Dynamic Content', 'porto-functionality' ),
						'param_name' => $dynamic_type . '_dynamic_content',
						'value'      => array(),
						'dependency' => array(
							'element' => $dynamic_type . '_dynamic_source',
							'value'   => array( 'post_info', 'term_meta', 'meta_box', 'taxonomy', 'acf', 'woocommerce', 'post_link' ),
						),
					),
				)
			);
			if ( 'field' == $dynamic_type_heading ) {
				$result = array_merge(
					$result,
					array(
						array(
							'type'        => 'textfield',
							'heading'     => __( 'Date Format', 'porto-functionality' ),
							'param_name'  => 'date_format',
							'description' => __( 'j = 1-31, F = January-December, M = Jan-Dec, m = 01-12, n = 1-12', 'porto-functionality' ),
							'value'       => '',
							'dependency'  => array(
								'element' => $dynamic_type . '_dynamic_source',
								'value'   => 'post_info',
							),
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Before Text', 'porto-functionality' ),
							'param_name' => $dynamic_type . '_dynamic_before',
							'value'      => '',
							'dependency' => array(
								'element'   => 'enable_' . $dynamic_type . '_dynamic',
								'not_empty' => true,
							),
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'After Text', 'porto-functionality' ),
							'param_name' => $dynamic_type . '_dynamic_after',
							'value'      => '',
							'dependency' => array(
								'element'   => 'enable_' . $dynamic_type . '_dynamic',
								'not_empty' => true,
							),
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Fallback', 'porto-functionality' ),
							'param_name' => $dynamic_type . '_dynamic_fallback',
							'value'      => '',
							'dependency' => array(
								'element'   => 'enable_' . $dynamic_type . '_dynamic',
								'not_empty' => true,
							),
						),
					)
				);
			} elseif ( 'link' == $dynamic_type_heading ) {
				$result = array_merge(
					$result,
					array(
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Fallback', 'porto-functionality' ),
							'param_name' => $dynamic_type . '_dynamic_fallback',
							'value'      => '',
							'dependency' => array(
								'element'   => 'enable_' . $dynamic_type . '_dynamic',
								'not_empty' => true,
							),
						),
					)
				);
			} elseif ( 'image' == $dynamic_type_heading ) {
				$result = array_merge(
					$result,
					array(
						array(
							'type'       => 'attach_image',
							'heading'    => __( 'Fallback', 'porto-functionality' ),
							'param_name' => $dynamic_type . '_dynamic_fallback',
							'value'      => '',
							'dependency' => array(
								'element'   => 'enable_' . $dynamic_type . '_dynamic',
								'not_empty' => true,
							),
						),
					)
				);
			}

			return $result;
		}

		/**
		 * localize vars for dynamic tags
		 *
		 * @since 2.3.0
		 */
		public function dynamic_register_wpb_vars() {
			if ( doing_action( 'vc_backend_editor_render' ) && ( empty( $_REQUEST['wpb_vc_js_status'] ) && empty( $_REQUEST['action'] ) ) ) {
				return;
			}
			global $post;
			$temp_post = $post;
			if ( function_exists( 'vc_is_inline' ) && vc_is_inline() && isset( $_REQUEST['vc_post_id'] ) ) {
				$post = get_post( $_REQUEST['vc_post_id'] );
			}
			if ( doing_action( 'wp_enqueue_scripts' ) ) {
				$porto_js_filname = 'porto-vc-frontend-editor';
			} elseif ( doing_action( 'vc_backend_editor_render' ) ) {
				$porto_js_filname = 'porto-vc-backend-editor';
			}
			if ( ! empty( $porto_js_filname ) ) {
				$vars = array();
				if ( defined( 'PORTO_SHORTCODES_URL' ) ) {
					$vars['shortcodes_url'] = PORTO_SHORTCODES_URL;
				}
				wp_localize_script(
					$porto_js_filname,
					'porto_wpb_vars',
					apply_filters( 'porto_wpb_editor_vars', $vars )
				);
			}
			if ( ! empty( $temp_post ) ) {
				$post = $temp_post;
			}
		}

		/**
		 * register the dynmaic fields of current post
		 *
		 * @since 2.3.0
		 */
		public function add_wpb_dynamic_field( $wpb_vars ) {
			do_action( 'porto_dynamic_before_render' );
			// Post / Author Fields
			$fields = Porto_Func_Dynamic_Tags_Content::get_instance()->get_dynamic_post_object_fields();
			foreach ( $fields as $key => $array ) {
				foreach ( $array as $key => $value ) {
					if ( 'options' == $key ) {
						foreach ( $value as $param => $field ) {
							$wpb_vars['post_info']['field'][ $param ] = $field;
						}
					}
				}
			}

			$fields = Porto_Func_Dynamic_Tags_Content::get_instance()->get_dynamic_post_object_image();
			foreach ( $fields as $key => $field ) {
				$wpb_vars['post_info']['image'][ $key ] = $field;
			}

			// Post Links
			$fields = Porto_Func_Dynamic_Tags_Content::get_instance()->get_dynamic_post_object_links();
			foreach ( $fields as $key => $array ) {
				foreach ( $array as $key => $value ) {
					if ( 'options' == $key ) {
						foreach ( $value as $param => $field ) {
							$wpb_vars['post_link']['link'][ $param ] = $field;
						}
					}
				}
			}

			//Metabox
			foreach ( Porto_Func_Dynamic_Tags_Content::get_instance()->features as $key => $feature ) {
				$fields = Porto_Func_Dynamic_Tags_Content::get_instance()->get_dynamic_metabox_fields( $feature );
				foreach ( $fields as $key => $value ) {
					$wpb_vars['meta_box'][ $feature ][ $key ] = $value;
				}
			}

			//Term for shop, archive page
			foreach ( Porto_Func_Dynamic_Tags_Content::get_instance()->features as $key => $feature ) {
				$fields = Porto_Func_Dynamic_Tags_Content::get_instance()->get_dynamic_metabox_fields( $feature, 'term' );
				foreach ( $fields as $key => $value ) {
					$wpb_vars['term_meta'][ $feature ][ $key ] = $value;
				}
			}

			//Taxonomy
			$fields = Porto_Func_Dynamic_Tags_Content::get_instance()->get_dynamic_taxonomy();
			foreach ( $fields as $key => $field ) {
				$wpb_vars['taxonomy']['field'][ $key ] = $field;
				$wpb_vars['taxonomy']['link'][ $key ]  = $field;
			}
			//ACF
			if ( class_exists( 'ACF' ) ) {
				foreach ( Porto_Func_Dynamic_Tags_Content::get_instance()->features as $key => $feature ) {
					$fields = Porto_Func_ACF::get_instance()->get_acf_groups( $feature );
					if ( ! empty( $fields[0]['options'] ) ) {
						foreach ( $fields[0]['options'] as $key => $field ) {
							$key                                 = substr( $key, stripos( $key, '-' ) + 1, strlen( $key ) );
							$wpb_vars['acf'][ $feature ][ $key ] = $field['label'];
						}
					}
				}
			}
			//woocommerce
			if ( class_exists( 'Woocommerce' ) ) {
				$fields = Porto_Func_Dynamic_Tags_Content::get_instance()->get_woo_fields();
				foreach ( $fields as $key => $field ) {
					$wpb_vars['woocommerce']['field'][ $key ] = $field;
				}
			}
			do_action( 'porto_dynamic_after_render' );
			return $wpb_vars;
		}
	}
	Porto_Wpb_Dynamic_Tags::get_instance();
}
