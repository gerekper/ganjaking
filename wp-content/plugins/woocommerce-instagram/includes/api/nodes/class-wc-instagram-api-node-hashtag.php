<?php
/**
 * WooCommerce Instagram API Node: Hashtag
 *
 * @package WC_Instagram/API/Nodes
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Instagram_API_Node', false ) ) {
	include_once 'abstract-class-wc-instagram-api-node.php';
}

/**
 * WooCommerce Instagram API Hashtag Node class.
 *
 * @class WC_Instagram_API_Node_Hashtag
 */
class WC_Instagram_API_Node_Hashtag extends WC_Instagram_API_Node {

	/**
	 * The Instagram user ID.
	 *
	 * @var string
	 */
	protected $user_id;

	/**
	 * The allowed actions for this node.
	 *
	 * @var array
	 */
	protected $actions = array(
		'search',
		'get',
	);

	/**
	 * The available edges for this node.
	 *
	 * @var array
	 */
	protected $edges = array(
		'recent_media',
		'top_media',
	);

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param string $access_token The access token.
	 * @param string $user_id      The Instagram user ID.
	 */
	public function __construct( $access_token, $user_id ) {
		parent::__construct( $access_token );

		$this->user_id = $user_id;
	}

	/**
	 * Searches a hashtag.
	 *
	 * @since 2.0.0
	 *
	 * @param string $hashtag The hashtag name.
	 * @return mixed The hashtag ID. WP_Error on failure.
	 */
	protected function _search( $hashtag ) {
		$args = array(
			'user_id' => $this->user_id,
			'q'       => $hashtag,
		);

		$response = $this->request_endpoint( 'ig_hashtag_search', $args );

		if ( ! is_wp_error( $response ) && isset( $response['data'], $response['data'][0], $response['data'][0]['id'] ) ) {
			$response = intval( $response['data'][0]['id'] );
		}

		return $response;
	}

	/**
	 * Gets the recent media for the specified hashtag.
	 *
	 * @since 2.0.0
	 *
	 * @param int   $hashtag_id The hashtag ID.
	 * @param array $args       The request arguments.
	 * @return mixed An array with the response data. WP_Error on failure.
	 */
	protected function _recent_media( $hashtag_id, $args = array() ) {
		return $this->edge_media( 'recent_media', $hashtag_id, $args );
	}

	/**
	 * Gets the top media for the specified hashtag.
	 *
	 * @since 2.0.0
	 *
	 * @param int   $hashtag_id The hashtag ID.
	 * @param array $args       The request arguments.
	 * @return mixed An array with the response data. WP_Error on failure.
	 */
	protected function _top_media( $hashtag_id, $args = array() ) {
		return $this->edge_media( 'top_media', $hashtag_id, $args );
	}

	/**
	 * Gets the hashtag media objects for the specified edge.
	 *
	 * @since 2.0.0
	 *
	 * @param string $edge       The hashtag edge name.
	 * @param int    $hashtag_id The hashtag ID.
	 * @param array  $args       The request arguments.
	 * @return mixed An array with the response data. WP_Error on failure.
	 */
	protected function edge_media( $edge, $hashtag_id, $args = array() ) {
		$args = wp_parse_args( $args, array( 'user_id' => $this->user_id ) );

		if ( ! empty( $args['fields'] ) ) {
			$args['fields'] = join( ',', $args['fields'] );
		}

		return $this->request_endpoint( "{$hashtag_id}/{$edge}", $args );
	}
}
