<?php
/**
 * @package Polylang-WC
 */

/**
 * Manages compatibility with WooCommerce Dynamic Pricing.
 * Version tested: 3.1.6.
 *
 * @since 0.5
 */
class PLLWC_Dynamic_Pricing {

	/**
	 * Constructor.
	 * Setups actions and filters.
	 *
	 * @since 0.5
	 */
	public function __construct() {
		add_action( 'pllwc_copy_post_metas', array( $this, 'copy_metas' ) );
		add_action( 'pllwc_translate_product_meta', array( $this, 'translate_meta' ), 10, 3 );

		if ( isset( $_GET['page'], $_GET['tab'] ) && 'wc_dynamic_pricing' === $_GET['page'] && 'category' === $_GET['tab'] ) {  // phpcs:ignore WordPress.Security.NonceVerification
			add_action( 'get_terms_args', array( $this, 'get_terms_args' ), 5, 2 );
		}

		if ( is_ajax() && isset( $_POST['action'] ) && 'create_empty_category_ruleset' === $_POST['action'] ) {  // phpcs:ignore WordPress.Security.NonceVerification
			add_action( 'get_terms_args', array( $this, 'get_terms_args' ), 5, 2 );
		}

		add_filter( 'sanitize_option__s_category_pricing_rules', array( $this, 'category_pricing_rules' ), 20 );
		add_filter( 'sanitize_option__a_category_pricing_rules', array( $this, 'advanced_category_pricing_rules' ), 20 );
	}


	/**
	 * Add Pricing rules to metas to copy or synchronize.
	 * Hooked to the filter 'pllwc_copy_post_metas'.
	 *
	 * @since 1.0
	 *
	 * @param array $metas Meta keys to copy or synchronize.
	 * @return array
	 */
	public function copy_metas( $metas ) {
		$metas[] = '_pricing_rules';
		return $metas;
	}

	/**
	 * Translates the pricing rules.
	 * Hooked to the filter 'pllwc_translate_product_meta'.
	 *
	 * @since 1.0
	 *
	 * @param array  $pricing_rules Meta value.
	 * @param string $key           Meta key.
	 * @param string $lang          Language of target.
	 * @return array
	 */
	public function translate_meta( $pricing_rules, $key, $lang ) {
		if ( '_pricing_rules' === $key ) {
			$data_store = PLLWC_Data_Store::load( 'product_language' );

			foreach ( $pricing_rules as $k => $rule ) {
				if ( isset( $rule['collector']['args']['cats'] ) ) {
					$cats = array();
					foreach ( $rule['collector']['args']['cats'] as $term_id ) {
						$cats[] = pll_get_term( $term_id, $lang );
					}

					$pricing_rules[ $k ]['collector']['args']['cats'] = $cats;
				}

				if ( isset( $rule['variation_rules']['args']['variations'] ) ) {
					$ids = array();
					foreach ( $rule['variation_rules']['args']['variations'] as $id ) {
						$ids[] = $data_store->get( $id, $lang );
					}

					$pricing_rules[ $k ]['variation_rules']['args']['variations'] = $ids;
				}
			}
		}

		return $pricing_rules;
	}

	/**
	 * Makes sure that the product categories are displayed in only one language
	 * on the Dynamic Pricing > category page (even when the admin languages filter requests all languages)
	 * to avoid conflicts if inconsistent information would be given for products translations.
	 *
	 * @since 0.5
	 *
	 * @param array $args       WP_Term_Query arguments.
	 * @param array $taxonomies Taxonomies used for the terms query.
	 * @return array modified arguments
	 */
	public function get_terms_args( $args, $taxonomies ) {
		if ( in_array( 'product_cat', $taxonomies ) && empty( PLL()->curlang ) ) {
			$args['lang'] = PLL()->options['default_lang'];
		}
		return $args;
	}

	/**
	 * Adds the translated categories to the pricing rules sets (Category pricing tab).
	 * Hooked to the filter 'sanitize_option__s_category_pricing_rules'.
	 *
	 * @since 0.5
	 *
	 * @param array $rules Pricing rules set.
	 * @return array
	 */
	public function category_pricing_rules( $rules ) {
		foreach ( $rules as $set_id => $rule ) {
			$cat_id = (int) substr( $set_id, 4 );
			foreach ( pll_get_term_translations( $cat_id ) as $lang => $tr_id ) {
				if ( $tr_id !== $cat_id ) {
					if ( isset( $rule['collector']['args']['cats'][0] ) ) {
						$rule['collector']['args']['cats'][0] = pll_get_term( $rule['collector']['args']['cats'][0], $lang );
					}
					$rules[ 'set_' . $tr_id ] = $rule;
				}
			}
		}
		return $rules;
	}

	/**
	 * Adds the translated categories to the pricing rules sets (Advanced Category pricing tab).
	 * Hooked to the filter 'sanitize_option__a_category_pricing_rules'.
	 *
	 * @since 0.5
	 *
	 * @param array $rules Pricing rules set.
	 * @return array
	 */
	public function advanced_category_pricing_rules( $rules ) {
		foreach ( $rules as $set_id => $rule ) {
			if ( isset( $rule['collector']['args']['cats'] ) ) {
				$cats = array();
				foreach ( $rule['collector']['args']['cats'] as $term_id ) {
					$cats = array_merge( $cats, array_values( pll_get_term_translations( $term_id ) ) );
				}
				$rules[ $set_id ]['collector']['args']['cats'] = $cats;
			}

			if ( isset( $rule['targets'] ) ) {
				$cats = array();
				foreach ( $rule['targets'] as $term_id ) {
					$cats = array_merge( $cats, array_values( pll_get_term_translations( $term_id ) ) );
				}
				$rules[ $set_id ]['targets'] = $cats;
			}
		}
		return $rules;
	}
}
