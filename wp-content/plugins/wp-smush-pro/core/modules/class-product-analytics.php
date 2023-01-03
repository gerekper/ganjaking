<?php

namespace Smush\Core\Modules;

use Smush\Core\Integrations\Mixpanel;
use Smush\Core\Settings;
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

	public function __construct() {
		$this->settings = Settings::get_instance();

		$this->hook_actions();
	}

	private function hook_actions() {
		if ( $this->settings->get( 'usage' ) ) {
			add_action( 'wp_smush_directory_smush_start', array( $this, 'track_directory_smush' ) );
			add_action( 'wp_smush_bulk_smush_start', array( $this, 'track_bulk_smush_start' ) );
			add_action( 'wp_smush_config_applied', array( $this, 'track_config_applied' ) );
		}

		$this->hook_settings_update_interceptor( array( $this, 'track_opt_toggle' ) );
		$this->hook_settings_update_interceptor( array( $this, 'intercept_settings_update' ) );
		$this->hook_settings_delete_interceptor( array( $this, 'intercept_reset' ) );
	}

	private function hook_settings_update_interceptor( $callback ) {
		if ( $this->settings->is_network_enabled() ) {
			add_action(
				"update_site_option_wp-smush-settings",
				function ( $option, $settings, $old_settings ) use ( $callback ) {
					call_user_func_array( $callback, array( $old_settings, $settings ) );
				},
				10,
				3
			);
		} else {
			add_action( "update_option_wp-smush-settings", $callback, 10, 2 );
		}
	}

	private function hook_settings_delete_interceptor( $callback ) {
		if ( $this->settings->is_network_enabled() ) {
			add_action( "delete_site_option_wp-smush-settings", $callback );
		} else {
			add_action( "delete_option_wp-smush-settings", $callback );
		}
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
			'lossy'      => 'Super-Smush',
			'strip_exif' => 'Metadata',
			'resize'     => 'Resize Original Images',
			'original'   => 'Compress original images',
			'backup'     => 'Backup original images',
			'png_to_jpg' => 'Auto-convert PNGs to JPEGs (lossy)',
			'no_scale'   => 'Disable scaled images',
		);

		$image_sizes     = Settings::get_instance()->get_setting( 'wp-smush-image_sizes' );
		$bulk_properties = array(
			'Image Sizes' => empty( $image_sizes ) ? 'All' : 'Custom',
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
		global $wpdb, $wp_version;

		return array(
			'active_theme'   => get_stylesheet(),
			'locale'         => get_locale(),
			'mysql_version'  => $wpdb->get_var( 'SELECT VERSION()' ),
			'php_version'    => phpversion(),
			'plugin'         => 'Smush',
			'plugin_type'    => WP_Smush::is_pro() ? 'pro' : 'free',
			'plugin_version' => WP_SMUSH_VERSION,
			'server_type'    => $this->get_server_type(),
			'wp_type'        => is_multisite() ? 'multisite' : 'single',
			'wp_version'     => $wp_version,
		);
	}

	private function get_server_type() {
		if ( empty( $_SERVER['SERVER_SOFTWARE'] ) ) {
			return '';
		}

		$server_software = wp_unslash( $_SERVER['SERVER_SOFTWARE'] );
		if ( ! is_array( $server_software ) ) {
			$server_software = array( $server_software );
		}

		$server_software = array_map( 'strtolower', $server_software );
		$is_nginx        = $this->array_has_needle( $server_software, 'nginx' );
		if ( $is_nginx ) {
			return 'nginx';
		}

		$is_apache = $this->array_has_needle( $server_software, 'apache' );
		if ( $is_apache ) {
			return 'apache';
		}

		return '';
	}

	private function array_has_needle( $array, $needle ) {
		foreach ( $array as $item ) {
			if ( strpos( $item, $needle ) !== false ) {
				return true;
			}
		}

		return false;
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

	public function intercept_reset() {
		if ( $this->settings->get( 'usage' ) ) {
			$this->get_mixpanel()->track( 'Opt Out' );
		}
	}
}
