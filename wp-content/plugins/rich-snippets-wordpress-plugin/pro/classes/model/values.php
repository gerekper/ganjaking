<?php

namespace wpbuddy\rich_snippets\pro;

use wpbuddy\rich_snippets\Rich_Snippet;
use wpbuddy\rich_snippets\Snippets_Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Values.
 *
 * Prepares and fills registered values.
 *
 * @package wpbuddy\rich_snippets\pro
 *
 * @since   2.19.0
 */
class Values_Model extends \wpbuddy\rich_snippets\Values_Model {
	/**
	 * Values_Model constructor.
	 * @since 2.20.0
	 */
	public function __construct() {
		add_filter( 'wpbuddy/rich_snippets/fields/link_to_subselect/values', [ 'wpbuddy\rich_snippets\pro\Fields_Model', 'more_references' ] );

		parent::__construct();
	}

	/**
	 * Fetches a call to function that doesn't exist.
	 *
	 * @param string $name
	 * @param array $args
	 *
	 * @return mixed
	 * @since 2.19.0
	 */
	public function __call( $name, $args ) {

		if ( false !== stripos( $name, 'global_snippet_' ) ) {
			$args[2]['snippet_uid'] = str_replace( 'global_snippet_', '', $name );

			return $this->global_snippet( $args[0], $args[1], $args[2], $args[3] );
		}

		return parent::__call( $name, $args );
	}


	/**
	 * Returns a sub element to be included into JSON-LD.
	 *
	 * @param                                     $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 * @param bool $overwritten
	 *
	 * @return null|\wpbuddy\rich_snippets\Rich_Snippet
	 * @since 2.0.0
	 * @since 2.14.25 New parameter $overwritten
	 */
	public function global_snippet( $val, Rich_Snippet $rich_snippet, array $meta_info, bool $overwritten ) {

		if ( $overwritten ) {
			return $val;
		}

		$post_id = Helper_Model::instance()->get_post_id_by_snippet_uid( $meta_info['snippet_uid'] );

		$rich_snippets = Snippets_Model::get_snippets( $post_id );

		if ( count( $rich_snippets ) <= 0 ) {
			return null;
		}

		if ( ! isset( $rich_snippets[ $meta_info['snippet_uid'] ] ) ) {
			return null;
		}

		$child_snippet = $rich_snippets[ $meta_info['snippet_uid'] ];

		$child_snippet->prepare_for_output( array(
			'current_post_id' => $meta_info['current_post_id'],
			'snippet_post_id' => $post_id,
		) );

		return $child_snippet;

	}

}