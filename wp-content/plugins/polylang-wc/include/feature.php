<?php
/**
 * @package Polylang-WC
 */

use Automattic\WooCommerce\Utilities\FeaturesUtil;

/**
 * Class to declare compatibility with a WooCommerce feature.

 * @since 1.9
 * @since 1.9.1 Renamed from `PLLWC_HPOS_Feature` to `PLLWC_Feature`.
 */
class PLLWC_Feature {

	/**
	 * Cache.
	 *
	 * @var bool[]
	 *
	 * @phpstan-var array<non-falsy-string, bool>
	 */
	private $cache = array();

	/**
	 * Unique feature id.
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	private $feature_id;

	/**
	 * Condition to meet for the compatibility to be enabled along the feature.
	 *
	 * @var callable
	 */
	private $condition_to_meet;

	/**
	 * Constructor.
	 *
	 * @since 1.9.1
	 *
	 * @param string   $feature_id        Unique feature id.
	 * @param callable $condition_to_meet Condition to meet for our compatibility to be enabled along the feature.
	 *
	 * @phpstan-param non-empty-string $feature_id
	 */
	public function __construct( string $feature_id, callable $condition_to_meet ) {
		$this->feature_id        = $feature_id;
		$this->condition_to_meet = $condition_to_meet;
	}

	/**
	 * Tells if PLLWC can use the WC's feature.
	 *
	 * @since 1.9
	 *
	 * @return bool
	 */
	public function exists(): bool {
		if ( isset( $this->cache[ __FUNCTION__ ] ) ) {
			return $this->cache[ __FUNCTION__ ];
		}

		// Require WC 7.1+.
		if ( ! class_exists( FeaturesUtil::class ) ) {
			$this->cache[ __FUNCTION__ ] = false;
		} else {
			$features = FeaturesUtil::get_features( true );
			$this->cache[ __FUNCTION__ ] = ! empty( $features[ $this->feature_id ] );
		}

		return $this->cache[ __FUNCTION__ ];
	}

	/**
	 * Tells if the feature is enabled.
	 * Must not be used before {@see self::exists()}.
	 *
	 * @since 1.9
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		if ( isset( $this->cache[ __FUNCTION__ ] ) ) {
			return $this->cache[ __FUNCTION__ ];
		}

		if ( ! $this->exists() ) {
			$this->cache[ __FUNCTION__ ] = false;
			return $this->cache[ __FUNCTION__ ];
		}

		// Check for the whole feature.
		if ( ! FeaturesUtil::feature_is_enabled( $this->feature_id ) ) {
			$this->cache[ __FUNCTION__ ] = false;
			return $this->cache[ __FUNCTION__ ];
		}

		// Check that our compatibility can be enabled.
		$this->cache[ __FUNCTION__ ] = (bool) call_user_func( $this->condition_to_meet );
		return $this->cache[ __FUNCTION__ ];
	}

	/**
	 * Calls the method that declares this plugin compatible with WC's feature.
	 *
	 * @since 1.9
	 *
	 * @return void
	 */
	public function declare_compatibility() {
		if ( $this->exists() ) {
			add_action( 'before_woocommerce_init', array( $this, 'declare_compatibility_callback' ) );
		}
	}

	/**
	 * Declares this plugin compatible with WC's feature.
	 *
	 * @since 1.9
	 *
	 * @return void
	 */
	public function declare_compatibility_callback() {
		// Can only be used in the hook `before_woocommerce_init`. See https://github.com/woocommerce/woocommerce/blob/8.4.0/plugins/woocommerce/src/Utilities/FeaturesUtil.php#L45-L57.
		FeaturesUtil::declare_compatibility( $this->feature_id, PLLWC_FILE, true );
	}
}
