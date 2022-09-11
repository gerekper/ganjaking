<?php
/**
 * @package Polylang-WC
 */

/**
 * Translates links (including endpoints) when the slugs are translatable.
 *
 * @since 0.1
 */
class PLLWC_Links_Pro extends PLLWC_Links {
	/**
	 * @var PLL_Translate_Slugs_Model
	 */
	protected $slugs_model;

	/**
	 * Constructor.
	 *
	 * @since 0.1
	 */
	public function __construct() {
		parent::__construct();

		if ( empty( PLL()->translate_slugs ) ) {
			return;
		}

		$this->slugs_model = PLL()->translate_slugs->slugs_model;

		// Endpoints slugs.
		add_filter( 'pll_translated_slugs', array( $this, 'pll_translated_slugs' ), 10, 3 );
		add_filter( 'woocommerce_get_endpoint_url', array( $this, 'get_endpoint_url' ), 10, 2 );
		add_filter( 'woocommerce_edit_address_slugs', array( $this, 'edit_address_slugs' ) );
	}

	/**
	 * Get the list of endpoints query vars.
	 *
	 * @since 0.4
	 *
	 * @return string[]
	 */
	protected function get_query_vars() {
		/**
		 * Filters the list of endpoints query vars
		 *
		 * @since 0.4
		 *
		 * @param string[] $slugs Endpoints slugs.
		 */
		return apply_filters( 'pllwc_endpoints_query_vars', WC()->query->get_query_vars() );
	}

	/**
	 * Removes the shop slug and adds endpoints slugs to the translatable slugs.
	 *
	 * @since 0.1
	 *
	 * @param array        $slugs    The list of slugs.
	 * @param PLL_Language $language Instance of PLL_Language.
	 * @param PLL_MO       $mo       The translations object, instance of PLL_MO.
	 * @return array
	 */
	public function pll_translated_slugs( $slugs, $language, &$mo ) {
		// Unset the shop slug to avoid conflict with the shop pages slugs.
		unset( $slugs['archive_product'] );

		// Remove /%product_cat% from the product base slug.
		if ( isset( $slugs['product'] ) ) {
			if ( $slug = preg_replace( '#\/?%.+?%#', '', $slugs['product']['slug'] ) ) {
				$slugs['product']['slug'] = $slug;
				$tr_slug = $mo->translate( $slug );
				$slugs['product']['translations'][ $language->slug ] = empty( $tr_slug ) ? $slug : $tr_slug;
			} else {
				unset( $slugs['product'] );
			}
		}

		$endpoints = $this->get_query_vars();
		$endpoints = array_merge( $endpoints, array( 'billing', 'shipping' ) ); // Adds edit-address slugs.
		foreach ( $endpoints as $slug ) {
			$slugs[ 'wc_' . $slug ]['slug'] = $slug;
			$tr_slug = $mo->translate( $slug );
			$slugs[ 'wc_' . $slug ]['translations'][ $language->slug ] = empty( $tr_slug ) ? $slug : $tr_slug;
		}
		return $slugs;
	}

	/**
	 * Prepares the rewrite rules filters to translate endpoints slugs.
	 *
	 * @since 0.1
	 *
	 * @param string[] $pre Not used.
	 * @return string[] Unmodified $pre.
	 */
	public function prepare_rewrite_rules( $pre ) {
		parent::prepare_rewrite_rules( $pre );

		if ( ! has_filter( 'page_rewrite_rules', array( $this, 'rewrite_translated_slug' ) ) ) {
			add_filter( 'page_rewrite_rules', array( $this, 'rewrite_translated_slug' ), 5 );
		}

		return $pre;
	}

	/**
	 * Modifies the rewrite rules to translate endpoints slugs.
	 * Hooked to the filter 'page_rewrite_rules'.
	 *
	 * @since 0.1
	 *
	 * @param string[] $rules Rewrite rules.
	 * @return string[] modified rewrite rules.
	 */
	public function rewrite_translated_slug( $rules ) {
		foreach ( $this->get_query_vars() as $slug ) {
			$rules = $this->translate_rule( $rules, 'wc_' . $slug );
		}
		return $rules;
	}

	/**
	 * Translates the endpoint slug in the rewrite rules.
	 *
	 * @since 0.1
	 *
	 * @param string[] $rules Rewrite rules.
	 * @param string   $type  Type of slug to translate.
	 * @return string[] Modified rewrite rules.
	 */
	public function translate_rule( $rules, $type ) {
		/*
		 * FIXME the only difference with translate-slugs-model are
		 * '/' replaced by '(/' in old and new
		 * and the [1] which is not replaced
		 */
		if ( empty( $this->slugs_model->translated_slugs[ $type ] ) ) {
			return $rules;
		}

		$newrules = array();

		$old = $this->slugs_model->translated_slugs[ $type ]['slug'] . '(/';
		$new = '(' . implode( '|', $this->slugs_model->translated_slugs[ $type ]['translations'] ) . ')(/';

		foreach ( $rules as $key => $rule ) {
			if ( false !== $found = strpos( $key, $old ) ) {
				$new_key = 0 === $found ? str_replace( $old, $new, $key ) : str_replace( '/' . $old, '/' . $new, $key );
				$newrules[ $new_key ] = str_replace(
					array( '[8]', '[7]', '[6]', '[5]', '[4]', '[3]', '[2]' ),
					array( '[9]', '[8]', '[7]', '[6]', '[5]', '[4]', '[3]' ),
					$rule
				); // Hopefully it is sufficient!
			} else {
				$newrules[ $key ] = $rule;
			}
		}
		return $newrules;
	}

	/**
	 * Translates the endpoint url.
	 *
	 * @øince 0.1
	 *
	 * @param string $link     Endpoint url.
	 * @param string $endpoint Endpoint name.
	 * @return string
	 */
	public function get_endpoint_url( $link, $endpoint ) {
		$lang = PLL()->model->get_language( PLLWC_Admin::get_preferred_language() ); // The function translate_slug expects the language object.
		return $this->slugs_model->translate_slug( $link, $lang, 'wc_' . $endpoint );
	}

	/**
	 * Translates the edit-address slug.
	 *
	 * @øince 0.1
	 *
	 * @param string[] $slugs Edit address endpoint slugs, typically 'billing' and 'shipping'.
	 * @return string[]
	 */
	public function edit_address_slugs( $slugs ) {
		foreach ( array_keys( $slugs ) as $key ) {
			if ( isset( $this->slugs_model->translated_slugs[ 'wc_' . $key ] ) && $lang = pll_current_language() ) {
				$slugs[ $key ] = rawurlencode( $this->slugs_model->translated_slugs[ 'wc_' . $key ]['translations'][ $lang ] );
			} else {
				$slugs[ $key ] = rawurlencode( $key ); // Don't rely on woocommerce mo file and accept translation only from our own system.
			}
		}
		return $slugs;
	}

	/**
	 * Returns the translation of the current url.
	 * Overrides the parent method to translate slugs.
	 *
	 * @since 0.1
	 *
	 * @param string $url  Translation url.
	 * @param string $lang Language slug.
	 * @return string
	 */
	public function pll_translation_url( $url, $lang ) {
		global $wp;

		$url = parent::pll_translation_url( $url, $lang );

		// Endpoints.
		if ( ( $endpoint = WC()->query->get_current_endpoint() ) && isset( WC()->query->query_vars[ $endpoint ] ) ) {
			$language = PLL()->model->get_language( $lang );

			if ( $language ) {
				$url = $this->slugs_model->switch_translated_slug( $url, $language, 'wc_' . WC()->query->query_vars[ $endpoint ] );

				if ( 'edit-address' === $endpoint ) {
					$value = wc_edit_address_i18n( $wp->query_vars[ $endpoint ], true );
					$url = trailingslashit( $url );
					$url = $this->slugs_model->switch_translated_slug( $url, $language, 'wc_' . $value );
				}
			}
		}

		return $url;
	}
}
