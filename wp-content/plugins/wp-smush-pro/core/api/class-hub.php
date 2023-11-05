<?php
/**
 * WPMU DEV Hub endpoints.
 *
 * Class allows syncing plugin data with the Hub.
 *
 * @since 3.7.0
 * @package Smush\Core\Api
 */

namespace Smush\Core\Api;

use Smush\Core\Array_Utils;
use Smush\Core\Settings;
use WP_Smush;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Hub
 */
class Hub {

	/**
	 * Endpoints array.
	 *
	 * @since 3.7.0
	 * @var array
	 */
	private $endpoints = array(
		'get_stats',
		'import_settings',
		'export_settings',
	);

	/**
	 * @var Array_Utils
	 */
	private $array_utils;

	/**
	 * Hub constructor.
	 *
	 * @since 3.7.0
	 */
	public function __construct() {
		$this->array_utils = new Array_Utils();

		add_filter( 'wdp_register_hub_action', array( $this, 'add_endpoints' ) );
	}

	/**
	 * Add Hub endpoints.
	 *
	 * Every Hub Endpoint name is build following the structure: 'smush-$endpoint-$action'
	 *
	 * @since 3.7.0
	 * @param array $actions  Endpoint action.
	 * @return array
	 */
	public function add_endpoints( $actions ) {
		foreach ( $this->endpoints as $endpoint ) {
			$actions[ "smush_{$endpoint}" ] = array( $this, 'action_' . $endpoint );
		}

		return $actions;
	}

	/**
	 * Retrieve data for endpoint.
	 *
	 * @since 3.7.0
	 * @param array  $params  Parameters.
	 * @param string $action  Action.
	 */
	public function action_get_stats( $params, $action ) {
		$status   = array();
		$core     = WP_Smush::get_instance()->core();
		$settings = Settings::get_instance();

		$status['cdn']   = $core->mod->cdn->is_active();
		$status['lossy'] = $settings->get_lossy_level_setting();

		$lazy = $settings->get_setting( 'wp-smush-lazy_load' );

		$status['lazy'] = array(
			'enabled' => $core->mod->lazy->is_active(),
			'native'  => is_array( $lazy ) && isset( $lazy['native'] ) ? $lazy['native'] : false,
		);

		$global_stats = $core->get_global_stats();
		// Total, Smushed, Unsmushed, Savings.
		$status['count_total']   = $this->array_utils->get_array_value( $global_stats, 'count_total' );
		$status['count_smushed'] = $this->array_utils->get_array_value( $global_stats, 'count_smushed' );
		// Considering the images to be resmushed.
		$status['count_unsmushed'] = $this->array_utils->get_array_value( $global_stats, 'count_unsmushed' );
		$status['savings']         = $this->get_savings_stats( $global_stats );

		$status['dir'] = $this->array_utils->get_array_value( $global_stats, 'savings_dir_smush' );

		wp_send_json_success( (object) $status );
	}

	private function get_savings_stats( $global_stats ) {
		// TODO: Is better to update the new change on hub?
		$map_stats_keys = array(
			'size_before'        => 'size_before',
			'size_after'         => 'size_after',
			'percent'            => 'savings_percent',
			'human'              => 'human_bytes',
			'bytes'              => 'savings_bytes',
			'total_images'       => 'count_images',
			'resize_count'       => 'count_resize',
			'resize_savings'     => 'savings_resize',
			'conversion_savings' => 'savings_conversion',
		);

		$hub_savings_stats = array();
		foreach ( $map_stats_keys as $hub_key => $global_stats_key ) {
			$hub_savings_stats[ $hub_key ] = $this->array_utils->get_array_value( $global_stats, $global_stats_key );
		}

		return $hub_savings_stats;
	}

	/**
	 * Applies the given config sent by the Hub via the Dashboard plugin.
	 *
	 * @since 3.8.5
	 *
	 * @param object $config_data The config sent by the Hub.
	 */
	public function action_import_settings( $config_data ) {
		if ( empty( $config_data->configs ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Missing config data', 'wp-smushit' ),
				)
			);
		}

		// The Hub returns an object, we use an array.
		$config_array = json_decode( wp_json_encode( $config_data->configs ), true );

		$configs_handler = new \Smush\Core\Configs();
		$configs_handler->apply_config( $config_array );

		wp_send_json_success();
	}

	/**
	 * Exports the current settings as a config for the Hub.
	 *
	 * @since 3.8.5
	 */
	public function action_export_settings() {
		$configs_handler = new \Smush\Core\Configs();
		$config          = $configs_handler->get_config_from_current();

		wp_send_json_success( $config['config'] );
	}
}