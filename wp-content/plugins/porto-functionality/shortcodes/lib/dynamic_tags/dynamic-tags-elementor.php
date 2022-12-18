<?php
/**
 * Porto Dynamic Tags class
 *
 * @author     P-Themes
 * @since      2.3.0
 */

defined( 'ABSPATH' ) || die;

use Elementor\Controls_Manager;

if ( ! class_exists( 'Porto_El_Dynamic_Tags' ) ) {
	class Porto_El_Dynamic_Tags extends Elementor\Modules\DynamicTags\Module {

		/**
		 * Base dynamic tag group.
		 */
		const PORTO_GROUP = 'porto';

		public function __construct() {
			parent::__construct();

			if ( class_exists( 'ACF' ) ) {
				require_once PORTO_SHORTCODES_LIB . 'dynamic_tags/class-porto-func-acf.php';
				add_action( 'porto_dynamic_el_extra_fields', array( $this, 'acf_add_control' ), 10, 3 );
				add_filter( 'porto_dynamic_el_extra_fields_content', array( $this, 'acf_render' ), 10, 3 );
			}

			if ( class_exists( 'WooCommerce' ) ) {
				require_once PORTO_SHORTCODES_LIB . 'dynamic_tags/class-porto-func-woo.php';
			}

			// Dynamic Meta Box
			add_action( 'porto_dynamic_el_extra_fields', array( $this, 'metabox_add_control' ), 10, 3 );
			add_filter( 'porto_dynamic_el_extra_fields_content', array( $this, 'metabox_render' ), 10, 3 );
		}

		/**
		 * Register Tags for Dynamic Meta Box
		 *
		 * @since 2.3.0
		 * @access public
		 */
		public function register_tags( $dynamic_tags ) {
			foreach ( $this->get_tag_classes_names() as $tag_class ) {
				$file     = str_replace( 'Porto_El_', '', $tag_class );
				$file     = str_replace( '_', '-', strtolower( $file ) ) . '.php';
				$filepath = PORTO_SHORTCODES_LIB . 'dynamic_tags/tags/' . $file;

				if ( file_exists( $filepath ) ) {
					require_once $filepath;
				}

				if ( class_exists( $tag_class ) ) {
					$dynamic_tags->register( new $tag_class() );
				}
			}
			do_action( 'porto_dynamic_tags_register', $dynamic_tags );
		}

		/**
		 * Add Dynamic Tags
		 *
		 * @since 2.3.0
		 * @access public
		 */
		public function get_tag_classes_names() {
			if ( ! defined( 'PORTO_VERSION' ) ) {
				return array();
			}
			$tags = array(
				'Porto_El_Custom_Field_Post_User_Tag',
				'Porto_El_Custom_Link_Post_User_Tag',
				'Porto_El_Custom_Field_Taxonomies_Tag',
				'Porto_El_Custom_Field_Meta_Data_Tag',
				'Porto_El_Custom_Image_Post_User_Tag',
				'Porto_El_Custom_Field_Meta_Box_Tag',
				'Porto_El_Custom_Link_Meta_Box_Tag',
				'Porto_El_Custom_Image_Meta_Box_Tag',
				'Porto_El_Custom_Gallery_Tag',
			);
			if ( ! porto_is_elementor_preview() || ( PortoBuilders::BUILDER_SLUG == get_post_type() && ( 'archive' == get_post_meta( get_the_ID(), PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) || 'shop' == get_post_meta( get_the_ID(), PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) ) ) {
				array_push( $tags, 'Porto_El_Custom_Field_Term_Meta_Tag', 'Porto_El_Custom_Image_Term_Meta_Tag', 'Porto_El_Custom_Link_Term_Meta_Tag' );
			}
			// Filters the tags which added dynamically.
			return apply_filters( 'porto_dynamic_el_tags', $tags );
		}

		public function get_groups() {
			return array(
				self::PORTO_GROUP => array(
					'title' => esc_html__( 'Porto Dynamic Tags', 'porto-functionality' ),
				),
			);
		}

		/**
		 * Add control for ACF object
		 *
		 * @since 2.3.0
		 * @access public
		 */
		public function acf_add_control( $object, $widget = 'field', $plugin = 'acf' ) {
			if ( 'acf' == $plugin ) {
				$control_key = 'dynamic_acf_' . $widget;
				$object->add_control(
					$control_key,
					array(
						'label'   => esc_html__( 'ACF Field', 'porto-functionality' ),
						'type'    => Controls_Manager::SELECT,
						'default' => '',
						'groups'  => $this->get_acf_fields( $widget ),
					)
				);
			}
		}

		/**
		 * Retrieve ACF fields for each group
		 *
		 * @since 2.3.0
		 * @access public
		 */
		public function get_acf_fields( $widget ) {
			if ( is_404() ) {
				return;
			}
			$fields = array();

			$group_data = Porto_Func_ACF::get_instance()->get_acf_groups( $widget );
			if ( empty( $group_data ) ) {
				return $fields;
			}

			foreach ( $group_data as $data ) {
				$field     = array();
				$data_temp = $data['options'];

				foreach ( $data_temp as $key => $value ) {
					$field[ $key ] = isset( $value['label'] ) ? $value['label'] : '';
				}

				$field = array_filter( $field );

				$fields[] = array(
					'label'   => $data['label'],
					'options' => $field,
				);
			}
			return $fields;
		}

		/**
		 * Render ACF Field
		 *
		 * @since 2.3.0
		 * @access public
		 */
		public function acf_render( $result, $settings, $widget = 'field' ) {
			if ( 'acf' == $settings['dynamic_field_source'] ) {
				$option = 'dynamic_acf_' . $widget;
				$key    = isset( $settings[ $option ] ) ? $settings[ $option ] : false;
				if ( ! $key || ! preg_match( '/-/', $key ) ) {
					return null;
				}

				$keys   = explode( '-', $key );
				$key    = $keys[1];
				$result = Porto_Func_ACF::get_instance()->acf_get_meta( $key );
			}
			return $result;
		}

		/**
		 * Add control for Meta Box Field
		 *
		 * @since 2.3.0
		 * @access public
		 */
		public function metabox_add_control( $object, $widget = 'field', $plugin = 'meta-box' ) {
			if ( 'meta-box' == $plugin ) {
				$control_key = 'dynamic_metabox_' . $widget;

				if ( 'image' == $widget ) {
					$object->add_control(
						'add_featured_image',
						array(
							'label' => esc_html__( 'Add Featured Image', 'porto-functionality' ),
							'type'  => Elementor\Controls_Manager::SWITCHER,
						)
					);
				}
				$object->add_control(
					$control_key,
					array(
						'label'   => esc_html__( 'MetaBox Field', 'porto-functionality' ),
						'type'    => Elementor\Controls_Manager::SELECT,
						'default' => '',
						'options' => Porto_Func_Dynamic_Tags_Content::get_instance()->get_dynamic_metabox_fields( $widget ),
					)
				);
			} elseif ( 'term-meta' == $plugin ) {
				$control_key = 'dynamic_termmeta_' . $widget;
				$object->add_control(
					$control_key,
					array(
						'label'   => esc_html__( 'Term Meta Field', 'porto-functionality' ),
						'type'    => Elementor\Controls_Manager::SELECT,
						'default' => '',
						'options' => Porto_Func_Dynamic_Tags_Content::get_instance()->get_dynamic_metabox_fields( $widget, 'term' ),
					)
				);
			}
		}

		/**
		 * Render field
		 *
		 * @param  [type] $result   [description]
		 * @param  array  $settings [description]
		 * @return [type] $widget   [description]
		 * @since 2.3.0
		 * @access public
		 */
		public function metabox_render( $result, $settings, $widget = 'field' ) {
			if ( 'meta-box' == $settings['dynamic_field_source'] ) {
				$option = 'dynamic_metabox_' . $widget;
				$key    = isset( $settings[ $option ] ) ? $settings[ $option ] : false;
				$result = '';

				if ( isset( $settings['add_featured_image'] ) && 'yes' == $settings['add_featured_image'] ) {
					$result = get_post_thumbnail_id();
				}

				if ( ! $key ) {
					return $result;
				}
				$result = get_post_meta( get_the_ID(), $key );
			} elseif ( 'term-meta' == $settings['dynamic_field_source'] ) {
				$option = 'dynamic_termmeta_' . $widget;
				$key    = isset( $settings[ $option ] ) ? $settings[ $option ] : false;
				$result = '';
				$result = Porto_Func_Dynamic_Tags_Content::get_instance()->dynamic_get_data( 'term_meta', $key, $widget );
			}
			return $result;
		}
	}
	new Porto_El_Dynamic_Tags;
}
