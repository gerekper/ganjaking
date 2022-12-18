<?php
/**
 * Porto ACF plugin compatibility for dynamic tags.
 *
 * @author     P-THEMES
 * @since      2.3.0
 */

defined( 'ABSPATH' ) || die;
if ( ! class_exists( 'Porto_Func_ACF' ) ) {
	class Porto_Func_ACF {

		protected static $instance = null;

		/**
		 * Constructor
		 *
		 * @since 2.3.0
		 */
		public function __construct() {
			add_filter( 'porto_gutenberg_editor_vars', array( $this, 'add_dynamic_field_vars' ) );
			if ( defined( 'ELEMENTOR_VERSION' ) ) {
				add_filter( 'porto_dynamic_el_tags', array( $this, 'acf_add_tags' ) );
			}
		}
		/**
		 * @return Porto_Func_ACF
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( empty( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}
		/**
		 * Returns support acf types
		 *
		 * @return array
		 */
		public function get_acf_types() {

			return array(
				'text'             => array( 'field' ),
				'textarea'         => array( 'field' ),
				'number'           => array( 'field' ),
				'range'            => array( 'field' ),
				'email'            => array( 'field' ),
				'url'              => array( 'link' ),
				'image'            => array( 'image' ),
				'select'           => array( 'field' ),
				'checkbox'         => array( 'field' ),
				'radio'            => array( 'field' ),
				'true_false'       => array( 'field' ),
				'link'             => array( 'link' ),
				'page_link'        => array( 'link' ),
				'post_object'      => array( 'field' ),
				'taxonomy'         => array( 'field' ),
				'date_picker'      => array( 'field' ),
				'date_time_picker' => array( 'field' ),
				'wysiwyg'          => array( 'field' ),
			);

		}

		public function acf_get_meta( $key ) {
			if ( ! $key ) {
				return null;
			}
			$post_id    = get_the_ID();
			$meta_value = get_post_meta( $post_id, $key, true );
			if ( ! $meta_value ) {
				return null;
			}
			return $meta_value;
		}

		/**
		 * Retrieve ACF Field groups
		 *
		 * @return array
		 * @since 1.0
		 */
		public function get_acf_groups( $widget, $object = null ) {
			if ( is_404() ) {
				return;
			}

			$acf_groups = array();
			if ( function_exists( 'acf_get_field_groups' ) ) {
				global $post;
				if ( $object ) {
					if ( isset( $object->term_id ) && isset( $object->taxonomy ) ) {
						$acf_groups = acf_get_field_groups(
							array(
								'taxonomy' => $object->taxonomy,
							)
						);
					} else {
						$acf_groups = acf_get_field_groups(
							array(
								'post_id'   => $object->ID,
								'post_type' => $object->post_type,
							)
						);
					}
				} elseif ( $post && PortoBuilders::BUILDER_SLUG == get_post_type( $post ) && 'type' == get_post_meta( $post->ID, PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) {
					$content_type = get_post_meta( $post->ID, 'content_type', true );
					if ( 'term' == $content_type ) {
						$term = get_post_meta( $post->ID, 'content_type_term', true );
						if ( $term ) {
							$acf_groups = acf_get_field_groups(
								array(
									'taxonomy' => $term,
								)
							);
						}
					}
				}

				if ( $post && empty( $acf_groups ) ) {
					$acf_groups = acf_get_field_groups(
						array(
							'post_id'   => $post->ID,
							'post_type' => $post->post_type,
						)
					);
				}
			} else {
				$acf_groups = apply_filters( 'acf/get_field_groups', array() );
			}
			$data = array();

			$acf_types = $this->get_acf_types();
			foreach ( $acf_groups as $acf_group ) {

				if ( function_exists( 'acf_get_fields' ) ) {
					$fields = acf_get_fields( $acf_group['ID'] );
				} else {
					$fields = array();
				}

				if ( empty( $fields ) ) {
					continue;
				}
				$options = array();

				foreach ( $fields as $field ) {
					if ( ! isset( $acf_types[ $field['type'] ] ) || ! in_array( $widget, $acf_types[ $field['type'] ] ) ) {
						continue;
					}

					$key             = $field['ID'] . '-' . $field['name'];
					$options[ $key ] = array(
						'type'  => $field['type'],
						'label' => $field['label'],
					);
				}

				if ( empty( $options ) ) {
					continue;
				}

				$data[] = array(
					'label'   => $acf_group['title'],
					'options' => $options,
				);
			}

			return $data;

		}

		/**
		 * Retrieve ACF meta fields
		 *
		 * @return array
		 * @since 2.3.0
		 */
		public function add_dynamic_field_vars( $block_vars = array() ) {
			foreach ( Porto_Func_Dynamic_Tags_Content::get_instance()->features as $field_type ) {
				$meta_fields = array();
				$group_data  = $this->get_acf_groups( $field_type );

				if ( empty( $group_data ) ) {
					continue;
				}

				foreach ( $group_data as $data ) {
					$field     = array();
					$data_temp = $data['options'];

					foreach ( $data_temp as $key => $value ) {
						$field[ $key ] = isset( $value['label'] ) ? $value['label'] : '';
					}

					$field = array_filter( $field );

					$meta_fields[] = array(
						'label'   => $data['label'],
						'options' => $field,
					);
				}

				if ( ! isset( $block_vars['acf'] ) ) {
					$block_vars['acf'] = array();
				}
				$block_vars['acf'][ $field_type ] = $meta_fields;
			}
			return $block_vars;
		}
		/**
		 * Add Dynamic Acf Tags
		 *
		 * @since 1.0
		 */
		public function acf_add_tags( $tags ) {
			array_push( $tags, 'Porto_El_Custom_Field_Acf_Tag', 'Porto_El_Custom_Link_Acf_Tag', 'Porto_El_Custom_Image_Acf_Tag' );
			return $tags;
		}
	}

	Porto_Func_ACF::get_instance();
}
