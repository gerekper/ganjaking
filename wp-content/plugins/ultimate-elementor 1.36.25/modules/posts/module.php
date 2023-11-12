<?php
/**
 * UAEL Posts Module.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Posts;

use UltimateElementor\Base\Module_Base;
use UltimateElementor\Classes\UAEL_Helper;
use UltimateElementor\Modules\Posts\TemplateBlocks\Build_Post_Query;
use Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Module.
 */
class Module extends Module_Base {

	/**
	 * Module should load or not.
	 *
	 * @since 1.7.0
	 * @access public
	 *
	 * @return bool true|false.
	 */
	public static function is_enable() {
		return true;
	}

	/**
	 * Get Module Name.
	 *
	 * @since 1.7.0
	 * @access public
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'posts';
	}

	/**
	 * Video Widgets.
	 *
	 * @since 1.36.0
	 * @var all_posts_widgets
	 */
	private static $all_posts_widgets = array();

	/**
	 * Get Widgets.
	 *
	 * @since 1.7.0
	 * @access public
	 *
	 * @return array Widgets.
	 */
	public function get_widgets() {
		return array(
			'Posts',
		);
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();

		if ( ! wp_script_is( 'jquery', 'enqueued' ) ) {
			wp_enqueue_script( 'jquery' );
		}

		/**
		 * Pagination Break.
		 *
		 * @see https://codex.wordpress.org/Making_Custom_Queries_using_Offset_and_Pagination
		 */
		add_action( 'pre_get_posts', array( $this, 'fix_query_offset' ), 1 );
		add_filter( 'found_posts', array( $this, 'fix_query_found_posts' ), 1, 2 );

		add_action( 'wp_ajax_uael_get_post', array( $this, 'get_post_data' ) );
		add_action( 'wp_ajax_nopriv_uael_get_post', array( $this, 'get_post_data' ) );

		if ( UAEL_Helper::is_widget_active( 'Posts' ) ) {

			add_filter( 'elementor/frontend/builder_content_data', array( $this, 'get_widget_data' ), 10, 2 );
			add_action( 'wp_footer', array( $this, 'render_posts_schema' ) );
		}
	}

	/**
	 * Render the Posts Schema.
	 *
	 * @since 1.36.0
	 *
	 * @access public
	 */
	public function render_posts_schema() {
		if ( ! empty( self::$all_posts_widgets ) ) {
			$elementor    = \Elementor\Plugin::$instance;
			$widgets_data = self::$all_posts_widgets;

			foreach ( $widgets_data as $_widget ) {
				$widget = $elementor->elements_manager->create_element_instance( $_widget );
				if ( isset( $_widget['templateID'] ) ) {
					$type          = $this->get_global_widget_type( $_widget['templateID'], 1 );
					$element_class = $type->get_class_name();
					try {
						$widget = new $element_class( $_widget, array() );
					} catch ( \Exception $e ) {
						return null;
					}
				}
				$settings       = $widget->get_settings();
				$skin           = $widget->get_current_skin_id();
				$skin_style     = isset( $settings[ $skin . '_select_article' ] ) ? $settings[ $skin . '_select_article' ] : false;
				$skin_schema    = isset( $settings[ $skin . '_schema_support' ] ) ? $settings[ $skin . '_schema_support' ] : false;
				$skin_publisher = isset( $settings[ $skin . '_publisher_name' ] ) ? $settings[ $skin . '_publisher_name' ] : false;
				if ( ( $skin_style || $skin_schema || $skin_publisher ) === false ) {
					return;
				}
				$select_article = $skin_style;
				$schema_support = $skin_schema;
				$publisher_name = $skin_publisher;
				$publisher_logo = isset( $settings[ $skin . '_publisher_logo' ]['url'] ) ? $settings[ $skin . '_publisher_logo' ]['url'] : 0;
				$query_obj      = new Build_Post_Query( $skin, $settings, '' );
				$query_obj->query_posts();
				$query = $query_obj->get_query();

				if ( $query->have_posts() ) {
					$this->schema_generation( $query, $select_article, $schema_support, $publisher_logo, $publisher_name );
				}
			}
		}
	}

	/**
	 * Render the Posts Schema.
	 *
	 * @since 1.36.0
	 *
	 * @param object $query object.
	 * @param string $select_article string.
	 * @param string $schema_support string.
	 * @param string $publisher_logo string.
	 * @param string $publisher_name string.
	 * @access public
	 */
	public function schema_generation( $query, $select_article, $schema_support, $publisher_logo, $publisher_name ) {
		$object_data            = array();
		$content_schema_warning = false;
		$post_data              = $query->posts;

		foreach ( $post_data as $posts_data ) {
			$headline     = $posts_data->post_title;
			$image        = get_the_post_thumbnail_url( $posts_data->ID, 'full' );
			$publishdate  = $posts_data->post_date_gmt;
			$modifieddate = $posts_data->post_modified_gmt;
			$description  = $posts_data->post_excerpt;
			$author_id    = $posts_data->post_author;
			$author_name  = get_the_author_meta( 'display_name', $posts_data->post_author );
			$author_url   = get_author_posts_url( $author_id, $author_name );

			if ( 'yes' === $schema_support && ( ( '' === $headline || '' === $publishdate || '' === $modifieddate ) || ( ! $image ) ) ) {
				$content_schema_warning = true;
			}
			if ( 'yes' === $schema_support && false === $content_schema_warning ) {
				$new_data = array(
					'@type'         => $select_article,
					'headline'      => $headline,
					'image'         => $image,
					'datePublished' => $publishdate,
					'dateModified'  => $modifieddate,
					'description'   => $description,
					'author'        => array(
						'@type' => 'Person',
						'name'  => $author_name,
						'url'   => $author_url,
					),
					'publisher'     => array(
						'@type' => 'Organization',
						'name'  => $publisher_name,
						'logo'  => array(
							'@type' => 'ImageObject',
							'url'   => $publisher_logo,
						),
					),
				);
				array_push( $object_data, $new_data );
			}
		}
		if ( $object_data ) {
			$schema_data = array(
				'@context' => 'https://schema.org',
				$object_data,
			);
			UAEL_Helper::print_json_schema( $schema_data );
		}
	}

	/**
	 * Get Post Data via AJAX call.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function get_post_data() {

		check_ajax_referer( 'uael-posts-widget-nonce', 'nonce' );

		$post_id   = isset( $_POST['page_id'] ) ? sanitize_text_field( $_POST['page_id'] ) : '';
		$widget_id = isset( $_POST['widget_id'] ) ? sanitize_text_field( $_POST['widget_id'] ) : '';
		$style_id  = isset( $_POST['skin'] ) ? sanitize_text_field( $_POST['skin'] ) : '';

		$elementor = \Elementor\Plugin::$instance;
		$meta      = $elementor->documents->get( $post_id )->get_elements_data();

		$widget_data = $this->find_element_recursive( $meta, $widget_id );

		$data = array(
			'message'    => __( 'Saved', 'uael' ),
			'ID'         => '',
			'skin_id'    => '',
			'html'       => '',
			'pagination' => '',
		);

		if ( null !== $widget_data ) {
			// Restore default values.
			$widget = $elementor->elements_manager->create_element_instance( $widget_data );

			// Return data and call your function according to your need for ajax call.
			// You will have access to settings variable as well as some widget functions.
			$skin = TemplateBlocks\Skin_Init::get_instance( $style_id );

			// Here you will just need posts based on ajax requst to attache in layout.
			$html = $skin->inner_render( $style_id, $widget );

			$pagination = $skin->page_render( $style_id, $widget );

			$data['ID']         = $widget->get_id();
			$data['skin_id']    = $widget->get_current_skin_id();
			$data['html']       = $html;
			$data['pagination'] = $pagination;
		}

		wp_send_json_success( $data );
	}

	/**
	 * Get Widget Setting data.
	 *
	 * @since 1.7.0
	 * @access public
	 * @param array  $elements Element array.
	 * @param string $form_id Element ID.
	 * @return Boolean True/False.
	 */
	public function find_element_recursive( $elements, $form_id ) {

		foreach ( $elements as $element ) {
			if ( $form_id === $element['id'] ) {
				return $element;
			}

			if ( ! empty( $element['elements'] ) ) {
				$element = $this->find_element_recursive( $element['elements'], $form_id );

				if ( $element ) {
					return $element;
				}
			}
		}

		return false;
	}

	/**
	 * Query Offset Fix.
	 *
	 * @since 1.8.4
	 * @access public
	 * @param object $query query object.
	 */
	public function fix_query_offset( &$query ) {
		if ( ! empty( $query->query_vars['offset_to_fix'] ) ) {
			if ( $query->is_paged ) {
				$query->query_vars['offset'] = $query->query_vars['offset_to_fix'] + ( ( $query->query_vars['paged'] - 1 ) * $query->query_vars['posts_per_page'] ); // PHPCS:Ignore WordPressVIPMinimum.Hooks.PreGetPosts.PreGetPosts
			} else {
				$query->query_vars['offset'] = $query->query_vars['offset_to_fix'];
			}
		}
	}

	/**
	 * Query Found Posts Fix.
	 *
	 * @since 1.8.4
	 * @access public
	 * @param int    $found_posts found posts.
	 * @param object $query query object.
	 * @return int string
	 */
	public function fix_query_found_posts( $found_posts, $query ) {
		$offset_to_fix = $query->get( 'offset_to_fix' );

		if ( $offset_to_fix ) {
			$found_posts -= $offset_to_fix;
		}

		return $found_posts;
	}

	/**
	 * Returns the type of elementor element.
	 *
	 * @param array $element The Element.
	 *
	 * @return Elementor\Widget_Base|Elementor\Widget_Base[]|mixed|string|null
	 */
	public function get_widget_type( $element ) {
		$type = '';
		if ( empty( $element['widgetType'] ) ) {
			$type = $element['elType'];
		} else {
			$type = $element['widgetType'];
		}

		if ( 'global' === $type && ! empty( $element['templateID'] ) ) {
			$type = $this->get_global_widget_type( $element['templateID'] );
		}

		return $type;
	}

	/**
	 * Returns the type of elementor element if global.
	 *
	 * @param int|string $template_id Template ID.
	 * @param bool       $return_type Return type.
	 *
	 * @return Elementor\Widget_Base|Elementor\Widget_Base[]|mixed|string|null
	 */
	public function get_global_widget_type( $template_id, $return_type = false ) {
		$template_data = Elementor\Plugin::$instance->templates_manager->get_template_data(
			array(
				'source'      => 'local',
				'template_id' => $template_id,
			)
		);

		if ( is_wp_error( $template_data ) ) {
			return '';
		}

		if ( empty( $template_data['content'] ) ) {
			return '';
		}

		$original_widget_type = Elementor\Plugin::$instance->widgets_manager->get_widget_types( $template_data['content'][0]['widgetType'] );

		if ( $return_type ) {
			return $original_widget_type;
		}

		return $original_widget_type ? $template_data['content'][0]['widgetType'] : '';
	}

	/**
	 * Get widget name.
	 *
	 * @since 1.36.0
	 * @access public
	 * @param array $data The builder content.
	 * @param int   $post_id The post ID.
	 */
	public function get_widget_data( $data, $post_id ) {

		Elementor\Plugin::$instance->db->iterate_data(
			$data,
			function ( $element ) use ( &$widgets ) {
				$type = $this->get_widget_type( $element );
				if ( 'uael-posts' === $type ) {
					self::$all_posts_widgets[] = $element;
				}
				return $element;
			}
		);

		return $data;
	}
}
