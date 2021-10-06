<?php
use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;

/**
 * Class for integrating with WooCommerce Blocks
 *
 * @package WC-Points-Rewards/Classes
 * @since   1.7.0
 */
class WC_Points_Rewards_Integration implements IntegrationInterface {
	/**
	 * The name of the integration.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'points-and-rewards';
	}

	/**
	 * When called invokes any initialization/setup for the integration.
	 *
	 */
	public function initialize() {
		$script_path = '/build/index.js';
		$style_path  = '/build/style-index.css';

		$script_url = plugins_url( $script_path, \WC_Points_Rewards::$plugin_file );
		$style_url  = plugins_url( $style_path, \WC_Points_Rewards::$plugin_file );

		$script_asset_path = dirname( \WC_Points_Rewards::$plugin_file ) . '/build/index.asset.php';
		$script_asset      = file_exists( $script_asset_path )
			? require $script_asset_path
			: array(
				'dependencies' => array(),
				'version'      => $this->get_file_version( $script_path ),
			);

		wp_enqueue_style(
			'wc-points-and-rewards-blocks-integration',
			$style_url,
			[],
			$this->get_file_version( $style_path )
		);

		wp_register_script(
			'wc-points-and-rewards-blocks-integration',
			$script_url,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);
		wp_set_script_translations(
			'wc-points-and-rewards-blocks-integration',
			'woocommerce-points-and-rewards',
			dirname( \WC_Points_Rewards::$plugin_file ) . '/languages'
		);
	}

	/**
	 * Returns an array of script handles to enqueue in the frontend context.
	 *
	 * @return string[]
	 */
	public function get_script_handles() {
		return array( 'wc-points-and-rewards-blocks-integration' );
	}

	/**
	 * Returns an array of script handles to enqueue in the editor context.
	 *
	 * @return string[]
	 */
	public function get_editor_script_handles() {
		return array( 'wc-points-and-rewards-blocks-integration' );
	}

	/**
	 * An array of key, value pairs of data made available to the block on the client side.
	 *
	 * @return array
	 */
	public function get_script_data() {
		$labels = explode( ':', get_option( 'wc_points_rewards_points_label', ':' ) );
		$data = array(
			'woocommerce-points-and-rewards-blocks' => 'active',
			'points_available'                      => 0,
			'minimum_points_amount'                 => 0,
			'partial_redemption_enabled'            => 'yes' === get_option( 'wc_points_rewards_partial_redemption_enabled' ),
			'points_label_singular'                 => $labels[0],
			'points_label_plural'                   => $labels[1],
		);

		$minimum_discount              = (float) get_option( 'wc_points_rewards_cart_min_discount', '' );
		$minimum_points_amount         = WC_Points_Rewards_Manager::calculate_points_for_discount( $minimum_discount );
		$data['minimum_points_amount'] = $minimum_points_amount;

		$user = wp_get_current_user();
		if ( null !== $user && wc_coupons_enabled() ) {
			$available_user_discount = WC_Points_Rewards_Manager::get_users_points_value( $user->ID );
			$available_user_points = WC_Points_Rewards_Manager::get_users_points( $user->ID );
			$data['points_available'] = $available_user_points;

			// no discount
			if ( $available_user_discount <= 0 ) {
				$data['points_available'] = 0;
			}

			// Limit the discount available by the global minimum discount if set.
			if ( $minimum_discount > $available_user_discount ) {
				$data['points_available'] = 0;
			}
		}

		return $data;

	}

	/**
	 * Get the file modified time as a cache buster if we're in dev mode.
	 *
	 * @param string $file Local path to the file.
	 * @return string The cache buster value to use for the given file.
	 */
	protected function get_file_version( $file ) {
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG && file_exists( $file ) ) {
			return filemtime( $file );
		}
		return \WC_Points_Rewards::VERSION;
	}
}
