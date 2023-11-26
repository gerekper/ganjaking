<?php
/**
 * WooCommerce Instagram API Node: Page
 *
 * @package WC_Instagram/API/Nodes
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Instagram_API_Node', false ) ) {
	include_once 'abstract-class-wc-instagram-api-node.php';
}

/**
 * WooCommerce Instagram API Page Node class.
 *
 * @class WC_Instagram_API_Node_Page
 */
class WC_Instagram_API_Node_Page extends WC_Instagram_API_Node {

	/**
	 * The allowed actions for this node.
	 *
	 * @var array
	 */
	protected $actions = array(
		'get',
	);
}
