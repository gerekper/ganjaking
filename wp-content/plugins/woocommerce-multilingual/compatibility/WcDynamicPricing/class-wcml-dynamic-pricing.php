<?php

use WPML\FP\Obj;

/**
 * Class WCML_Dynamic_Pricing
 */
class WCML_Dynamic_Pricing implements \IWPML_Action {

	/**
	 * @var SitePress
	 */
	private $sitepress;

	/**
	 * WCML_Dynamic_Pricing constructor.
	 *
	 * @param SitePress $sitepress
	 */
	public function __construct( SitePress $sitepress ) {
		$this->sitepress = $sitepress;
	}

	public function add_hooks() {
		if ( ! is_admin() ) {
			add_filter( 'woocommerce_dynamic_pricing_is_object_in_terms', [ $this, 'is_object_in_translated_terms' ], 10, 3 );
			add_filter( 'wc_dynamic_pricing_load_modules', [ $this, 'translate_collector_args' ] );
			add_filter( 'woocommerce_dynamic_pricing_is_applied_to', [ $this, 'woocommerce_dynamic_pricing_is_applied_to' ], 10, 5 );
		} else {
			$this->hide_language_switcher_for_settings_page();
		}
		add_filter( 'woocommerce_product_get__pricing_rules', [ $this, 'translate_variations_in_rules' ] );
	}

	/**
	 * @param array $modules
	 *
	 * @return array
	 */
	public function translate_collector_args( $modules ) {
		foreach ( $modules as $mod_key => $module ) {
			if ( isset( $module->available_advanced_rulesets ) ) {
				foreach ( $module->available_advanced_rulesets as $rule_key => $rule ) {
					$taxonomy = $this->get_taxonomy( $rule );
					if ( ! empty( $rule['targets'] ) ) {
						$modules[ $mod_key ]->available_advanced_rulesets[ $rule_key ]['targets'] = $this->adjust_cat_ids( $rule['targets'], $taxonomy );
					}
					if ( ! empty( $rule['collector']['args']['cats'] ) ) {
						$modules[ $mod_key ]->available_advanced_rulesets[ $rule_key ]['collector']['args']['cats'] = $this->adjust_cat_ids( $rule['collector']['args']['cats'], $taxonomy );
					}
				}
			}
		}

		return $modules;
	}

	/**
	 * @param boolean $result
	 * @param int     $product_id
	 * @param array   $categories
	 *
	 * @return boolean
	 */
	public function is_object_in_translated_terms( $result, $product_id, $categories ) {
		foreach ( $categories as &$cat_id ) {
			$cat_id = apply_filters( 'translate_object_id', $cat_id, 'product_cat', true );
		}

		$product_id = apply_filters( 'translate_object_id', $product_id, 'product', true );

		return is_object_in_term( $product_id, 'product_cat', $categories );
	}

	/**
	 * @param bool                                                            $process_discounts
	 * @param WC_Product                                                      $_product
	 * @param int                                                             $module_id
	 * @param WC_Dynamic_Pricing_Simple_Base|WC_Dynamic_Pricing_Advanced_Base $dynamic_pricing
	 * @param array|int                                                       $cat_ids
	 *
	 * @return bool|WP_Error
	 */
	public function woocommerce_dynamic_pricing_is_applied_to( $process_discounts, WC_Product $_product, $module_id, $dynamic_pricing, $cat_ids ) {
		if ( ! $_product || ! $cat_ids || ! $this->has_requirements( $dynamic_pricing ) ) {
			return $process_discounts;
		}
		$taxonomy   = $this->get_taxonomy( $dynamic_pricing );
		$product_id = 'product_variation' === Obj::prop( 'post_type', $_product )
			? $_product->get_parent_id()
			: $_product->get_id();

		$product_id = apply_filters( 'wpml_object_id', $product_id, 'product', true );

		return is_object_in_term( $product_id, $taxonomy, $this->adjust_cat_ids( $cat_ids, $taxonomy ) );
	}

	/**
	 * @param WC_Dynamic_Pricing_Simple_Base|WC_Dynamic_Pricing_Advanced_Base $dynamic_pricing
	 *
	 * @return string
	 */
	private function get_taxonomy( $dynamic_pricing ) {
		$taxonomy = 'product_cat';
		if ( $dynamic_pricing instanceof WC_Dynamic_Pricing_Simple_Taxonomy || $dynamic_pricing instanceof WC_Dynamic_Pricing_Advanced_Taxonomy ) {
			$taxonomy = $dynamic_pricing->taxonomy;
		}

		return $taxonomy;
	}

	/**
	 * @param WC_Dynamic_Pricing_Simple_Base|WC_Dynamic_Pricing_Advanced_Base $dynamic_pricing
	 *
	 * @return bool
	 */
	private function has_requirements( $dynamic_pricing ) {
		$requirements = [
			'WC_Dynamic_Pricing_Advanced_Category' => [
				'adjustment_sets',
			],
			'WC_Dynamic_Pricing_Advanced_Taxonomy' => [
				'adjustment_sets',
			],
			'WC_Dynamic_Pricing_Advanced_Totals'   => [
				'adjustment_sets',
			],
			'WC_Dynamic_Pricing_Simple_Membership' => [
				'available_rulesets',
			],
			'WC_Dynamic_Pricing_Simple_Category'   => [
				'available_rulesets',
			],
			'WC_Dynamic_Pricing_Simple_Taxonomy'   => [
				'available_rulesets',
			],
			'WC_Dynamic_Pricing_Simple_Base'       => [
				'available_rulesets',
			],
		];

		$class_name = wpml_collect( array_keys( $requirements ) )
			->first(
				function ( $class_name ) use ( $dynamic_pricing ) {
						return get_class( $dynamic_pricing ) === $class_name || is_subclass_of( $dynamic_pricing, $class_name );
				}
			);

		if ( $class_name ) {
			return (bool) wpml_collect( $requirements[ $class_name ] )
				->filter(
					function ( $property ) use ( $dynamic_pricing ) {
						return isset( $dynamic_pricing->$property ) && $dynamic_pricing->$property;
					}
				)
				->count();
		}

		return false;
	}

	/**
	 * @param array|int $cat_ids
	 * @param string    $taxonomy
	 *
	 * @return array
	 */
	private function adjust_cat_ids( $cat_ids, $taxonomy ) {
		if ( ! is_array( $cat_ids ) ) {
			$cat_ids = [ $cat_ids ];
		}

		return array_map(
			function ( $cat_id ) use ( $taxonomy ) {
					return apply_filters( 'translate_object_id', $cat_id, $taxonomy, true );
			},
			$cat_ids
		);
	}

	/**
	 * @param array $rules
	 *
	 * @return array
	 */
	public function translate_variations_in_rules( $rules ) {
		if ( is_array( $rules ) ) {
			foreach ( $rules as $r_key => $rule ) {
				if ( isset( $rule['variation_rules']['args']['variations'] ) ) {
					foreach ( $rule['variation_rules']['args']['variations'] as $i => $variation_id ) {
						$rules[ $r_key ]['variation_rules']['args']['variations'][ $i ] = apply_filters( 'translate_object_id', $variation_id, 'product_variation', true );
					}
				}
			}
		}

		return $rules;
	}

	public function hide_language_switcher_for_settings_page() {
		if ( 'wc_dynamic_pricing' === filter_input( INPUT_GET, 'page' ) ) {
			remove_action( 'wp_before_admin_bar_render', [ $this->sitepress, 'admin_language_switcher' ] );
		}
	}

}
