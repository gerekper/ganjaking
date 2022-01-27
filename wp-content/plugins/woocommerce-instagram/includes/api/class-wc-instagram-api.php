<?php
/**
 * WooCommerce Instagram API
 *
 * @package WC_Instagram/API
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Instagram_API Class.
 */
class WC_Instagram_API {

	use WC_Instagram_Singleton_Trait;

	/**
	 * The access token.
	 *
	 * @var string
	 */
	protected $access_token = '';

	/**
	 * The Instagram user ID.
	 *
	 * @var string
	 */
	protected $user_id = '';

	/**
	 * Store the instances that handle the different API nodes.
	 *
	 * @var array
	 */
	protected $nodes = array();

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	protected function __construct() {
		$settings = wc_instagram_get_settings();

		if ( is_array( $settings ) ) {
			$this->access_token = ( ! empty( $settings['access_token'] ) ? $settings['access_token'] : '' );
			$this->user_id      = ( isset( $settings['instagram_business_account'], $settings['instagram_business_account']['id'] ) ? $settings['instagram_business_account']['id'] : '' );
		}
	}

	/**
	 * Magic __call method.
	 *
	 * @since 2.0.0
	 *
	 * @param string $method     Method.
	 * @param mixed  $parameters Parameters.
	 * @return bool|mixed
	 */
	public function __call( $method, $parameters ) {
		if ( in_array( $method, array( 'user', 'hashtag', 'page' ), true ) ) {
			array_unshift( $parameters, $method );

			return call_user_func_array( array( $this, 'init_node' ), $parameters );
		}

		return null;
	}

	/**
	 * Gets the access token.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_access_token() {
		return $this->access_token;
	}

	/**
	 * Gets the user ID.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_user_id() {
		return $this->user_id;
	}

	/**
	 * Sets the access token.
	 *
	 * @since 2.0.0
	 *
	 * @param string $access_token The access token.
	 */
	public function set_access_token( $access_token ) {
		$this->access_token = $access_token;

		$this->reset_nodes();
	}

	/**
	 * Sets the user ID.
	 *
	 * @since 2.0.0
	 *
	 * @param string $user_id The user ID.
	 */
	public function set_user_id( $user_id ) {
		$this->user_id = $user_id;

		$this->reset_nodes();
	}

	/**
	 * Initializes an Instagram API Node.
	 *
	 * @since 2.0.0
	 *
	 * @param string $name The node name.
	 * @return WC_Instagram_API_Node
	 */
	protected function init_node( $name ) {
		if ( empty( $this->nodes[ $name ] ) ) {
			$class = 'WC_Instagram_API_Node_' . ucfirst( $name );

			// Hashtag node needs the Instagram user ID.
			if ( 'hashtag' === $name ) {
				$this->nodes[ $name ] = new $class( $this->access_token, $this->user_id );
			} else {
				$this->nodes[ $name ] = new $class( $this->access_token );
			}
		}

		return $this->nodes[ $name ];
	}

	/**
	 * Resets the nodes instances.
	 *
	 * @since 2.0.0
	 */
	public function reset_nodes() {
		$this->nodes = array();
	}
}
