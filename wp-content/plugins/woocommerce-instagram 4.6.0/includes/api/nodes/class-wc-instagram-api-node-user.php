<?php
/**
 * WooCommerce Instagram API Node: User
 *
 * @package WC_Instagram/API/Nodes
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Instagram_API_Node', false ) ) {
	include_once 'abstract-class-wc-instagram-api-node.php';
}

/**
 * WooCommerce Instagram API User Node class.
 *
 * @class WC_Instagram_API_Node_User
 */
class WC_Instagram_API_Node_User extends WC_Instagram_API_Node {

	/**
	 * The allowed actions for this node.
	 *
	 * @var array
	 */
	protected $actions = array(
		'me',
		'get',
	);

	/**
	 * The available edges for this node.
	 *
	 * @var array
	 */
	protected $edges = array(
		'accounts',
		'permissions',
	);

	/**
	 * Gets the current user info.
	 *
	 * @since 2.0.0
	 *
	 * @param array $fields  Optional. The fields to retrieve.
	 * @return mixed An array with the user info. WP_Error on failure.
	 */
	protected function _me( $fields = array() ) {
		return $this->_get( 'me', $fields );
	}

	/**
	 * Gets the pages the user has a role on.
	 *
	 * @since 2.0.0
	 *
	 * @param string $user_id Optional. The user ID.
	 * @return mixed An array with the pages. WP_Error on failure.
	 */
	protected function _accounts( $user_id = 'me' ) {
		return $this->edge( 'accounts', $user_id );
	}

	/**
	 * Gets the permissions that the person has granted this app.
	 *
	 * @since 2.0.0
	 *
	 * @param string $user_id Optional. The user ID.
	 * @return mixed An array with the pages. WP_Error on failure.
	 */
	protected function _permissions( $user_id = 'me' ) {
		return $this->edge( 'permissions', $user_id );
	}

	/**
	 * Gets the data for the specified user edge.
	 *
	 * @since 2.0.0
	 *
	 * @param string $edge    The user edge name.
	 * @param string $user_id Optional. The user ID.
	 * @return mixed An array with the edge data. WP_Error on failure.
	 */
	protected function edge( $edge, $user_id = '' ) {
		if ( ! $user_id ) {
			$user_id = 'me';
		}

		return $this->request_endpoint( "{$user_id}/{$edge}" );
	}
}
