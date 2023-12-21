<?php
namespace Happy_Addons\Elementor;

defined( 'ABSPATH' ) || die();

class Extensions_Manager {
	const FEATURES_DB_KEY = 'happyaddons_inactive_features';

	/**
	 * Initialize
	 */
	public static function init() {
		// include_once HAPPY_ADDONS_DIR_PATH . 'extensions/column-extended.php';
		include_once HAPPY_ADDONS_DIR_PATH . 'extensions/widgets-extended.php';

		if ( is_user_logged_in() ) {
			include_once HAPPY_ADDONS_DIR_PATH . 'classes/review.php';
		}

		if ( is_user_logged_in() && ha_is_adminbar_menu_enabled() ) {
			include_once HAPPY_ADDONS_DIR_PATH . 'classes/admin-bar.php';
		}

		if ( is_user_logged_in() && ha_is_happy_clone_enabled() ) {
			include_once HAPPY_ADDONS_DIR_PATH . 'classes/clone-handler.php';
		}

		$inactive_features = self::get_inactive_features();

		foreach ( self::get_local_features_map() as $feature_key => $data ) {
			if ( ! in_array( $feature_key, $inactive_features ) ) {
				self::enable_feature( $feature_key );
			}
		}

		foreach ( self::get_pro_features_map() as $feature_key => $data ) {
			if ( in_array( $feature_key, $inactive_features ) ) {
				self::disable_pro_feature( $feature_key );
			}
		}
	}

	public static function get_features_map() {
		$features_map = [];

		$local_features_map = self::get_local_features_map();
		$features_map = array_merge( $features_map, $local_features_map );

		return apply_filters( 'happyaddons_get_features_map', $features_map );
	}

	public static function get_inactive_features() {
		return get_option( self::FEATURES_DB_KEY, [] );
	}

	public static function save_inactive_features( $features = [] ) {
		update_option( self::FEATURES_DB_KEY, $features );
	}

	/**
	 * Get the pro features map for dashboard only
	 *
	 * @return array
	 */
	public static function get_pro_features_map() {
		return [
			'display-conditions' => [
				'title' => __( 'Display Condition', 'happy-elementor-addons' ),
				'icon' => 'hm hm-display-condition',
				'demo' => 'https://happyaddons.com/display-condition/',
				'is_pro' => true,
			],
			'image-masking' => [
				'title' => __( 'Image Masking', 'happy-elementor-addons' ),
				'icon' => 'hm hm-image-masking',
				'demo' => 'https://happyaddons.com/image-masking-demo/',
				'is_pro' => true,
			],
			'happy-particle-effects' => [
				'title' => __( 'Happy Particle Effects', 'happy-elementor-addons' ),
				'icon' => 'hm hm-spark',
				'demo' => 'https://happyaddons.com/happy-particle-effect/',
				'is_pro' => true,
			],
			'happy-preset' => [
				'title' => __( 'Preset', 'happy-elementor-addons' ),
				'icon' => 'hm hm-color-card',
				'demo' => 'https://happyaddons.com/presets-demo/',
				'is_pro' => true,
			]
		];
	}

	/**
	 * Get the free features map
	 *
	 * @return array
	 */
	public static function get_local_features_map() {
		return [
			'background-overlay' => [
				'title' => __( 'Background Overlay', 'happy-elementor-addons' ),
				'icon' => 'hm hm-layer',
				'demo' => 'https://happyaddons.com/background-overlay-demo/',
				'is_pro' => false,
			],
			'grid-layer' => [
				'title' => __( 'Grid Layer', 'happy-elementor-addons' ),
				'icon' => 'hm hm-grid',
				'demo' => 'https://happyaddons.com/happy-grid-layout-demo/',
				'is_pro' => false,
			],
			'floating-effects' => [
				'title' => __( 'Floating Effects', 'happy-elementor-addons' ),
				'icon' => 'hm hm-weather-flood',
				'demo' => 'https://happyaddons.com/elementor-floating-effect-demo-2/',
				'is_pro' => false,
			],
			'wrapper-link' => [
				'title' => __( 'Wrapper Link', 'happy-elementor-addons' ),
				'icon' => 'hm hm-section-link',
				'demo' => 'https://happyaddons.com/wrapper-link-feature-demo/',
				'is_pro' => false,
			],
			'css-transform' => [
				'title' => __( 'CSS Transform', 'happy-elementor-addons' ),
				'icon' => 'hm hm-3d-rotate',
				'demo' => 'https://happyaddons.com/elementor-css-transform-demo-3/',
				'is_pro' => false,
			],
			'css-transform' => [
				'title' => __( 'CSS Transform', 'happy-elementor-addons' ),
				'icon' => 'hm hm-3d-rotate',
				'demo' => 'https://happyaddons.com/elementor-css-transform-demo-3/',
				'is_pro' => false,
			],
			'equal-height' => [
				'title' => __( 'Equal Height Column', 'happy-elementor-addons' ),
				'icon' => 'hm hm-grid-layout',
				'demo' => 'https://happyaddons.com/equal-height-feature/',
				'is_pro' => false,
			],
			'shape-divider' => [
				'title' => __( 'Shape Divider', 'happy-elementor-addons' ),
				'icon' => 'hm hm-map',
				'demo' => 'https://happyaddons.com/happy-shape-divider/',
				'is_pro' => false,
			],
			'column-extended' => [
				'title' => __( 'Column Order & Extension', 'happy-elementor-addons' ),
				'icon' => 'hm hm-flip-card2',
				'demo' => 'https://happyaddons.com/happy-column-control/',
				'is_pro' => false,
			],
			'advanced-tooltip' => [
				'title' => __( 'Happy Tooltip', 'happy-elementor-addons' ),
				'icon' => 'hm hm-comment-square',
				'demo' => 'https://happyaddons.com/happy-tooltip/',
				'is_pro' => false,
			],
			'text-stroke' => [
				'title' => __( 'Text Stroke', 'happy-elementor-addons' ),
				'icon' => 'hm hm-text-outline',
				'demo' => 'https://happyaddons.com/text-stroke/',
				'is_pro' => false,
			],
			'scroll-to-top' => [
				'title' => __( 'Scroll To Top', 'happy-elementor-addons' ),
				'icon' => 'hm hm-scroll-top',
				// 'demo' => 'https://happyaddons.com/text-stroke/',
				'is_pro' => false,
			],
		];
	}

	protected static function enable_feature( $feature_key ) {
		$feature_file = HAPPY_ADDONS_DIR_PATH . 'extensions/' . $feature_key . '.php';

		if ( is_readable( $feature_file ) ) {
			include_once( $feature_file );
		}
	}

	protected static function disable_pro_feature( $feature_key ) {
		switch ($feature_key) {
			case 'display-conditions':
				add_filter( 'happyaddons/extensions/display_condition', '__return_false' );
				break;

			case 'image-masking':
				add_filter( 'happyaddons/extensions/image_masking', '__return_false' );
				break;

			case 'happy-particle-effects':
				add_filter( 'happyaddons/extensions/happy_particle_effects', '__return_false' );
				break;

			// case 'happy-preset':
			// 	add_filter( 'happyaddons/extensions/happy_preset', '__return_false' );
			// 	break;
		}
	}
}

Extensions_Manager::init();
