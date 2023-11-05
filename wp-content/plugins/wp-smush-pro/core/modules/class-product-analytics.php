<?php

namespace Smush\Core\Modules;

use Smush\Core\Array_Utils;
use Smush\Core\Integrations\Mixpanel;
use Smush\Core\Media_Library\Background_Media_Library_Scanner;
use Smush\Core\Media_Library\Media_Library_Scan_Background_Process;
use Smush\Core\Media_Library\Media_Library_Scanner;
use Smush\Core\Modules\Background\Background_Process;
use Smush\Core\Server_Utils;
use Smush\Core\Settings;
use Smush\Core\Stats\Global_Stats;
use WP_Smush;

class Product_Analytics {
	const PROJECT_TOKEN = '5d545622e3a040aca63f2089b0e6cae7';
	/**
	 * @var Mixpanel
	 */
	private $mixpanel;
	/**
	 * @var Settings
	 */
	private $settings;
	/**
	 * @var Server_Utils
	 */
	private $server_utils;
	/**
	 * @var Media_Library_Scan_Background_Process
	 */
	private $scan_background_process;

	public function __construct() {
		$this->settings                = Settings::get_instance();
		$this->server_utils            = new Server_Utils();
		$this->scan_background_process = Background_Media_Library_Scanner::get_instance()->get_background_process();

		$this->hook_actions();
	}

	private function hook_actions() {
		// Setting events.
		add_action( 'wp_smush_settings_updated', array( $this, 'track_opt_toggle' ), 10, 2 );
		add_action( 'wp_smush_settings_updated', array( $this, 'intercept_settings_update' ), 10, 2 );
		add_action( 'wp_smush_settings_deleted', array( $this, 'intercept_reset' ) );
		add_action( 'wp_smush_settings_updated', array( $this, 'track_integrations_saved' ), 10, 2 );

		if ( ! $this->settings->get( 'usage' ) ) {
			return;
		}

		// Other events.
		add_action( 'wp_smush_directory_smush_start', array( $this, 'track_directory_smush' ) );
		add_action( 'wp_smush_bulk_smush_start', array( $this, 'track_bulk_smush_start' ) );
		add_action( 'wp_smush_bulk_smush_completed', array( $this, 'track_bulk_smush_completed' ) );
		add_action( 'wp_smush_config_applied', array( $this, 'track_config_applied' ) );

		$identifier = $this->scan_background_process->get_identifier();

		add_action( "{$identifier}_before_start", array( $this, 'track_background_scan_start' ) );
		add_action( "{$identifier}_completed", array( $this, 'track_background_scan_end' ) );

		add_action(
			"{$identifier}_cancelled",
			array( $this, 'track_background_scan_process_cancellation' ),
			10, 2
		);
		add_action(
			"{$identifier}_dead",
			array( $this, 'track_background_scan_process_death' ),
			10, 2
		);
	}

	/**
	 * @return Mixpanel
	 */
	private function get_mixpanel() {
		if ( is_null( $this->mixpanel ) ) {
			$this->mixpanel = $this->prepare_mixpanel_instance();
		}

		return $this->mixpanel;
	}

	public function intercept_settings_update( $old_settings, $settings ) {
		if ( empty( $settings['usage'] ) ) {
			// Use the most up-to-data value of 'usage'
			return;
		}

		$settings = $this->remove_unchanged_settings( $old_settings, $settings );
		$handled  = $this->maybe_track_feature_toggle( $settings );

		if ( ! $handled ) {
			$handled = $this->maybe_track_cdn_update( $settings );
		}
	}

	private function maybe_track_feature_toggle( array $settings ) {
		foreach ( $settings as $setting_key => $setting_value ) {
			$handler = "track_{$setting_key}_feature_toggle";
			if ( method_exists( $this, $handler ) ) {
				call_user_func( array( $this, $handler ), $setting_value );

				return true;
			}
		}

		return false;
	}

	private function remove_unchanged_settings( $old_settings, $settings ) {
		$changed = array();
		foreach ( $settings as $setting_key => $setting_value ) {
			$old_setting_value = isset( $old_settings[ $setting_key ] ) ? $old_settings[ $setting_key ] : '';
			if ( $old_setting_value !== $setting_value ) {
				$changed[ $setting_key ] = $setting_value;
			}
		}

		return $changed;
	}

	public function get_bulk_properties() {
		$bulk_property_labels = array(
			'auto'       => 'Automatic Compression',
			'strip_exif' => 'Metadata',
			'resize'     => 'Resize Original Images',
			'original'   => 'Compress original images',
			'backup'     => 'Backup original images',
			'png_to_jpg' => 'Auto-convert PNGs to JPEGs (lossy)',
			'no_scale'   => 'Disable scaled images',
		);

		$image_sizes     = Settings::get_instance()->get_setting( 'wp-smush-image_sizes' );
		$bulk_properties = array(
			'Image Sizes'         => empty( $image_sizes ) ? 'All' : 'Custom',
			'Mode'                => $this->settings->get_current_lossy_level_label(),
			'Parallel Processing' => defined( 'WP_SMUSH_PARALLEL' ) && WP_SMUSH_PARALLEL ? 'Enabled' : 'Disabled',
		);
		foreach ( $bulk_property_labels as $bulk_setting => $bulk_property_label ) {
			$property_value                          = Settings::get_instance()->get( $bulk_setting )
				? 'Enabled'
				: 'Disabled';
			$bulk_properties[ $bulk_property_label ] = $property_value;
		}

		return $bulk_properties;
	}

	private function track_detection_feature_toggle( $setting_value ) {
		return $this->track_feature_toggle( $setting_value, 'Image Resize Detection' );
	}

	private function track_webp_mod_feature_toggle( $setting_value ) {
		return $this->track_feature_toggle( $setting_value, 'Local WebP' );
	}

	private function track_cdn_feature_toggle( $setting_value ) {
		return $this->track_feature_toggle( $setting_value, 'CDN' );
	}

	private function track_lazy_load_feature_toggle( $setting_value ) {
		return $this->track_feature_toggle( $setting_value, 'Lazy Load' );
	}

	private function track_feature_toggle( $active, $feature ) {
		$event = $active
			? 'Feature Activated'
			: 'Feature Deactivated';

		$this->get_mixpanel()->track( $event, array(
			'Feature'        => $feature,
			'Triggered From' => $this->identify_referrer(),
		) );

		return true;
	}

	private function identify_referrer() {
		$onboarding_request = ! empty( $_REQUEST['action'] ) && 'smush_setup' === $_REQUEST['action'];
		if ( $onboarding_request ) {
			return 'Wizard';
		}

		$path       = parse_url( wp_get_referer(), PHP_URL_QUERY );
		$query_vars = array();
		parse_str( $path, $query_vars );
		$page           = empty( $query_vars['page'] ) ? '' : $query_vars['page'];
		$triggered_from = array(
			'smush'              => 'Dashboard',
			'smush-bulk'         => 'Bulk Smush',
			'smush-directory'    => 'Directory Smush',
			'smush-lazy-load'    => 'Lazy Load',
			'smush-cdn'          => 'CDN',
			'smush-webp'         => 'Local WebP',
			'smush-integrations' => 'Integrations',
			'smush-settings'     => 'Settings',
		);

		return empty( $triggered_from[ $page ] )
			? ''
			: $triggered_from[ $page ];
	}

	private function prepare_mixpanel_instance() {
		$mixpanel = new Mixpanel( $this->get_token() );
		$mixpanel->identify( $this->get_unique_id() );
		$mixpanel->registerAll( $this->get_super_properties() );

		return $mixpanel;
	}

	public function get_super_properties() {
		global $wp_version;

		return array(
			'active_theme'       => get_stylesheet(),
			'locale'             => get_locale(),
			'mysql_version'      => $this->server_utils->get_mysql_version(),
			'php_version'        => phpversion(),
			'plugin'             => 'Smush',
			'plugin_type'        => WP_Smush::is_pro() ? 'pro' : 'free',
			'plugin_version'     => WP_SMUSH_VERSION,
			'server_type'        => $this->server_utils->get_server_type(),
			'memory_limit'       => $this->convert_to_megabytes( $this->server_utils->get_memory_limit() ),
			'max_execution_time' => $this->server_utils->get_max_execution_time(),
			'wp_type'            => is_multisite() ? 'multisite' : 'single',
			'wp_version'         => $wp_version,
			'device'             => $this->get_device(),
			'user_agent'         => $this->server_utils->get_user_agent(),
		);
	}

	private function get_device() {
		if ( ! $this->is_mobile() ) {
			return 'desktop';
		}

		if ( $this->is_tablet() ) {
			return 'tablet';
		}

		return 'mobile';
	}

	private function is_tablet() {
		if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			return false;
		}
		/**
		 * It doesn't work with IpadOS due to of this:
		 * https://stackoverflow.com/questions/62323230/how-can-i-detect-with-php-that-the-user-uses-an-ipad-when-my-user-agent-doesnt-c
		 */
		$tablet_pattern = '/(tablet|ipad|playbook|kindle|silk)/i';
		return preg_match( $tablet_pattern, $_SERVER['HTTP_USER_AGENT'] );
	}

	private function is_mobile() {
		if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			return false;
		}
		// Do not use wp_is_mobile() since it doesn't detect ipad/tablet.
		$mobile_patten = '/Mobile|iP(hone|od|ad)|Android|BlackBerry|tablet|IEMobile|Kindle|NetFront|Silk|(hpw|web)OS|Fennec|Minimo|Opera M(obi|ini)|Blazer|Dolfin|Dolphin|Skyfire|Zune|playbook/i';
		return preg_match( $mobile_patten, $_SERVER['HTTP_USER_AGENT'] );
	}

	private function normalize_url( $url ) {
		$url = str_replace( array( 'http://', 'https://', 'www.' ), '', $url );

		return untrailingslashit( $url );
	}

	private function maybe_track_cdn_update( $settings ) {
		$cdn_properties      = array();
		$cdn_property_labels = $this->cdn_property_labels();
		foreach ( $settings as $setting_key => $setting_value ) {
			if ( array_key_exists( $setting_key, $cdn_property_labels ) ) {
				$property_label                    = $cdn_property_labels[ $setting_key ];
				$property_value                    = $setting_value ? 'Enabled' : 'Disabled';
				$cdn_properties[ $property_label ] = $property_value;
			}
		}

		if ( $cdn_properties ) {
			$this->get_mixpanel()->track( 'CDN Updated', $cdn_properties );

			return true;
		}

		return false;
	}

	private function cdn_property_labels() {
		return array(
			'background_images' => 'Background Images',
			'auto_resize'       => 'Automatic Resizing',
			'webp'              => 'WebP Conversions',
			'rest_api_support'  => 'Rest API',
		);
	}

	public function track_directory_smush() {
		$this->get_mixpanel()->track( 'Directory Smushed' );
	}

	public function track_bulk_smush_start() {
		$this->get_mixpanel()->track( 'Bulk Smush Started', $this->get_bulk_properties() );
	}

	public function track_bulk_smush_completed() {
		$this->get_mixpanel()->track( 'Bulk Smush Completed', $this->get_bulk_smush_stats() );
	}

	private function get_bulk_smush_stats() {
		$global_stats = WP_Smush::get_instance()->core()->get_global_stats();
		$array_util   = new Array_Utils();

		return array(
			'Total Savings'                 => $this->convert_to_megabytes( (int) $array_util->get_array_value( $global_stats, 'savings_bytes' ) ),
			'Total Images'                  => (int) $array_util->get_array_value( $global_stats, 'count_images' ),
			'Media Optimization Percentage' => (float) $array_util->get_array_value( $global_stats, 'percent_optimized' ),
			'Percentage of Savings'         => (float) $array_util->get_array_value( $global_stats, 'savings_percent' ),
			'Images Resized'                => (int) $array_util->get_array_value( $global_stats, 'count_resize' ),
			'Resize Savings'                => $this->convert_to_megabytes( (int) $array_util->get_array_value( $global_stats, 'savings_resize' ) ),
		);
	}

	public function track_config_applied( $config_name ) {
		$properties = $config_name
			? array( 'Config Name' => $config_name )
			: array();

		$properties['Triggered From'] = $this->identify_referrer();

		$this->get_mixpanel()->track( 'Config Applied', $properties );
	}

	public function get_unique_id() {
		return $this->normalize_url( home_url() );
	}

	public function get_token() {
		return self::PROJECT_TOKEN;
	}

	public function track_opt_toggle( $old_settings, $settings ) {
		$settings = $this->remove_unchanged_settings( $old_settings, $settings );

		if ( isset( $settings['usage'] ) ) {
			$this->get_mixpanel()->track( $settings['usage'] ? 'Opt In' : 'Opt Out' );
		}
	}

	public function track_integrations_saved( $old_settings, $settings ) {
		if ( empty( $settings['usage'] ) ) {
			return;
		}

		$settings = $this->remove_unchanged_settings( $old_settings, $settings );
		if ( empty( $settings ) ) {
			return;
		}

		$this->maybe_track_integrations_toggle( $settings );
	}

	private function maybe_track_integrations_toggle( $settings ) {
		$integrations = array(
			'gutenberg'  => 'Gutenberg',
			'gform'      => 'Gravity Forms',
			'js_builder' => 'WP Bakery',
			's3'         => 'Amazon S3',
			'nextgen'    => 'NextGen Gallery',
		);

		foreach ( $settings as $integration_slug => $is_activated ) {
			if ( ! array_key_exists( $integration_slug, $integrations ) ) {
				continue;
			}

			if ( $is_activated ) {
				$this->get_mixpanel()->track(
					'Integration Activated',
					array(
						'Integration' => $integrations[ $integration_slug ],
					)
				);
			} else {
				$this->get_mixpanel()->track(
					'Integration Deactivated',
					array(
						'Integration' => $integrations[ $integration_slug ],
					)
				);
			}
		}
	}

	public function intercept_reset() {
		if ( $this->settings->get( 'usage' ) ) {
			$this->get_mixpanel()->track( 'Opt Out' );
		}
	}

	public function track_background_scan_start() {
		$properties = array(
			'Scan Type' => $this->scan_background_process->get_status()->is_dead() ? 'Retry' : 'New',
		);

		$this->get_mixpanel()->track( 'Scan Started', array_merge(
			$properties,
			$this->get_bulk_properties(),
			$this->get_scan_properties()
		) );
	}

	public function track_background_scan_end() {
		$this->get_mixpanel()->track( 'Scan Ended', array_merge(
			$this->get_bulk_properties(),
			$this->get_scan_properties()
		) );
	}

	/**
	 * @param $identifier string
	 * @param $background_process Background_Process
	 *
	 * @return void
	 */
	public function track_background_scan_process_cancellation( $identifier, $background_process ) {
		$this->get_mixpanel()->track(
			'Background Scan Process Cancelled',
			$this->get_background_process_status_properties( $background_process )
		);
	}

	/**
	 * @param $identifier string
	 * @param $background_process Background_Process
	 *
	 * @return void
	 */
	public function track_background_scan_process_death( $identifier, $background_process ) {
		$scanner = new Media_Library_Scanner();

		$this->get_mixpanel()->track(
			'Background Scan Process Dead',
			array_merge(
				array( 'Slice Size' => $scanner->get_slice_size() ),
				$this->get_background_process_status_properties( $background_process )
			)
		);
	}

	private function get_scan_properties() {
		$global_stats       = Global_Stats::get();
		$global_stats_array = $global_stats->to_array();

		$labels = array(
			'image_attachment_count' => 'Image Attachment Count',
			'optimized_images_count' => 'Optimized Images Count',
			'optimize_count'         => 'Optimize Count',
			'reoptimize_count'       => 'Reoptimize Count',
			'ignore_count'           => 'Ignore Count',
			'animated_count'         => 'Animated Count',
			'error_count'            => 'Error Count',
			'percent_optimized'      => 'Percent Optimized',
			'size_before'            => 'Size Before',
			'size_after'             => 'Size After',
			'savings_percent'        => 'Savings Percent',
		);

		$savings_keys = array(
			'size_before',
			'size_after',
		);

		foreach ( $labels as $key => $label ) {
			if ( isset( $global_stats_array[ $key ] ) ) {
				$properties[ $label ] = $global_stats_array[ $key ];

				if ( in_array( $key, $savings_keys, true ) ) {
					$properties[ $label ] = $this->convert_to_megabytes( $properties[ $label ] );
				}
			}
		}

		return $properties;
	}

	/**
	 * @param Background_Process $background_process
	 *
	 * @return array
	 */
	private function get_background_process_status_properties( Background_Process $background_process ) {
		$background_process_status = $background_process ? $background_process->get_status() : false;
		$properties                = array();
		if ( $background_process_status ) {
			$properties = array(
				'Total Items'     => $background_process_status->get_total_items(),
				'Processed Items' => $background_process_status->get_processed_items(),
				'Failed Items'    => $background_process_status->get_failed_items(),
			);
		}

		return $properties;
	}

	private function convert_to_megabytes( $size_in_bytes ) {
		if ( empty( $size_in_bytes ) ) {
			return 0;
		}
		$unit_mb = pow( 1024, 2 );
		return round( $size_in_bytes / $unit_mb, 2 );
	}
}