<?php

namespace wpbuddy\rich_snippets\pro;

use wpbuddy\rich_snippets\Rich_Snippet;
use wpbuddy\rich_snippets\Schema_Property;
use wpbuddy\rich_snippets\Schemas_Model;
use wpbuddy\rich_snippets\View;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Admin_Snippets_Forms_Controller.
 *
 * Manages creation and processing of Rich Snippets that can be overwritten.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.2.0
 */
final class Admin_Snippets_Overwrite_Controller {

	/**
	 * The instance.
	 *
	 * @var Admin_Snippets_Overwrite_Controller
	 *
	 * @since 2.2.0
	 */
	protected static $instance = null;


	/**
	 * If this instance has been initialized already.
	 *
	 * @since 2.2.0
	 *
	 * @var bool
	 */
	protected $initialized = false;


	/**
	 * Get the singleton instance.
	 *
	 * Creates a new instance of the class if it does not exists.
	 *
	 * @return   Admin_Snippets_Overwrite_Controller
	 *
	 * @since 2.2.0
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}


	/**
	 * Magic function for cloning.
	 *
	 * Disallow cloning as this is a singleton class.
	 *
	 * @since 2.2.0
	 */
	protected function __clone() {
	}


	/**
	 * Magic method for setting up the class.
	 *
	 * Disallow external instances.
	 *
	 * @since 2.2.0
	 */
	protected function __construct() {
	}


	/**
	 * Init.
	 *
	 * @since 2.2.0
	 */
	public function init() {

		if ( $this->initialized ) {
			return;
		}

		add_action( 'admin_footer', [ self::instance(), 'print_modal_window' ] );

		$this->add_metaboxes();

		$this->initialized = true;
	}


	/**
	 * @since 2.2.0
	 */
	public function print_modal_window() {

		View::admin_posts_modalwindow();
	}


	/**
	 * Add metaboxes.
	 *
	 * @since 2.2.0
	 */
	public function add_metaboxes() {

		if ( ! Helper_Model::instance()->magic() ) {
			return;
		}

		add_meta_box(
			'rswp-overwrite-mb',
			__( 'Global Snippets Values', 'rich-snippets-schema' ),
			array( '\wpbuddy\rich_snippets\View', 'admin_posts_metabox_overwrites' ),
			null,
			'side',
			'high'
		);
	}


	/**
	 * Returns the HTML code for the properties to use in the table.
	 *
	 * Uses Rich_Snippet:get_properties() if $prop_ids has no elements.
	 *
	 * @param Rich_Snippet $snippet
	 * @param \WP_Post $post
	 * @param int $overwrite_post_id The post_id where the overwrite data is stored.
	 * @param string $main_snippet_id
	 * @param string $parent_snippet_id
	 * @param string $input_name
	 *
	 * @return string[]
	 * @since 2.2.0 Added $post param.
	 * @since 2.14.0 Added $main_snippet_id param; removed $property_ids
	 *
	 * @since 2.0.0
	 */
	public function get_property_table_elements( $snippet, $post, $overwrite_post_id, $main_snippet_id, $parent_snippet_id, $parent_property_id, $input_name ) {

		$html_elements = array();

		/**
		 * Back compat
		 */
		$overwrite_data = Helper_Model::instance()->get_properties_to_overwrite( $overwrite_post_id, $main_snippet_id, $snippet->id, $parent_snippet_id );
		if ( count( $overwrite_data ) > 0 ) {
			if ( is_array( $overwrite_data ) && count( $overwrite_data ) > 0 ) {
				$snippet->overwrite_properties( $overwrite_data );
			}

			$list_data = Helper_Model::instance()->get_properties_to_list( $overwrite_post_id, $main_snippet_id, $snippet, $parent_snippet_id );
			if ( is_array( $list_data ) && count( $list_data ) > 0 ) {

				foreach ( $list_data as $snippet_id => $ld ) {
					$snippet->create_duplicate( $ld['prop_name'], $ld['properties'], $snippet_id );
				}
			}
		}

		# load the properties from the snippet
		$props = $snippet->get_overridable_properties( $overwrite_post_id, $input_name, 'Schema_Property' );

		# natural sort by id
		usort( $props, function ( $a, $b ) {
			return strcmp( $a->id, $b->id );
		} );

		foreach ( $props as $prop ) {
			if ( ! $prop instanceof Schema_Property ) {
				continue;
			}

			ob_start();
			View::admin_snippets_overwrite_row( $prop, $snippet, $post, $overwrite_post_id, $main_snippet_id, $parent_snippet_id, $parent_property_id, $input_name );
			$html_elements[] = ob_get_clean();
		}

		$is_enumeration = Schemas_Model::is_enumeration( [ $snippet->get_type() ] );

		if ( count( $html_elements ) <= 0 && ! $is_enumeration ) {
			$html_elements[] = sprintf(
				'<tr><td colspan="2">%s</td></tr>',
				sprintf(
					__( 'No overridable properties found. <a href="%s">Read how you can create properties that are overridable.</a>', 'rich-snippets-schema' ),
					Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/structured-data/module-2/lesson-3/', 'plugin-global-snippets-window' )
				)
			);
		}

		return $html_elements;
	}


	/**
	 * Builds a property table.
	 *
	 * Uses Rich_Snippet:get_properties() if $prop_ids has no elements.
	 *
	 * @param Rich_Snippet $snippet
	 * @param \WP_Post $post
	 * @param int $overwrite_post_id The post_id where the overwrite data is stored.
	 * @param string $main_snippet_id
	 * @param string $parent_snippet_id
	 * @param string $input_name
	 *
	 * @return string
	 * @since 2.2.0 Added $post parameter
	 * @since 2.14.0 Added $main_snippet_id parameter; removed $prop_ids; Added $prop
	 *
	 * @since 2.0.0
	 */
	public function get_property_table( $snippet, $post, $overwrite_post_id, $main_snippet_id, $parent_snippet_id = '', $parent_property_id = '', $input_name = '' ) {
		$props_rendered = $this->get_property_table_elements( $snippet, $post, $overwrite_post_id, $main_snippet_id, $parent_snippet_id, $parent_property_id, $input_name );

		ob_start();

		View::admin_snippets_overwrite_table( $props_rendered, $snippet, $post );

		return ob_get_clean();
	}
}
