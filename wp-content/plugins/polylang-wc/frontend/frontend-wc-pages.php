<?php
/**
 * @package Polylang-WC
 */

/**
 * Manages WC pages when they are used as home page.
 *
 * @since 1.0
 */
class PLLWC_Frontend_WC_Pages {

	/**
	 * Constructor.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_filter( 'pll_set_language_from_query', array( $this, 'pll_set_language_from_query' ), 5, 2 ); // Before Polylang.
	}

	/**
	 * Get the queried page_id ( if it exists ).
	 *
	 * If permalinks are used, WordPress does set and use $query->queried_object_id and sets $query->query_vars['page_id'] to 0
	 * and does set and use $query->query_vars['page_id'] if permalinks are not used :(
	 *
	 * @since 0.3.2
	 *
	 * @param WP_Query $query Instance of WP_Query.
	 * @return int page_id
	 */
	protected function get_page_id( $query ) {
		if ( ! empty( $query->query_vars['pagename'] ) && isset( $query->queried_object_id ) ) {
			return $query->queried_object_id;
		}

		if ( isset( $query->query_vars['page_id'] ) ) {
			return $query->query_vars['page_id'];
		}

		return 0; // No page queried.
	}

	/**
	 * Fixes query vars on translated front page when the front page displays the shop, my account or the checkout.
	 *
	 * @since 0.3.2
	 *
	 * @param PLL_Language|false $lang  False or language object.
	 * @param WP_Query           $query WP_Query object.
	 * @return PLL_Language
	 */
	public function pll_set_language_from_query( $lang, $query ) {
		$qvars     = $query->query_vars;
		$languages = PLL()->model->get_languages_list();
		$pages     = wp_list_pluck( $languages, 'page_on_front' );

		// Shop on front.
		if ( in_array( wc_get_page_id( 'shop' ), $pages ) ) {
			// Redirect the language page to the homepage when using a static front page.
			if ( ( PLL()->options['redirect_lang'] || PLL()->options['hide_default'] ) && ( count( $query->query ) === 1 || ( ( is_preview() || is_paged() || ! empty( $query->query['page'] ) ) && count( $query->query ) === 2 ) || ( ( is_preview() && ( is_paged() || ! empty( $query->query['page'] ) ) ) && count( $query->query ) === 3 ) ) && is_tax( 'language' ) ) {
				$lang = PLL()->model->get_language( get_query_var( 'lang' ) );
				$query->is_home              = false;
				$query->is_tax               = false;
				$query->is_page              = true;
				$query->is_post_type_archive = true;
				$query->set( 'page_id', $lang->page_on_front );
				$query->set( 'post_type', 'product' );
				unset( $query->query_vars['lang'], $query->queried_object ); // Reset queried object.
			}

			// Set the language when requesting a static front page.
			elseif ( ( $page_id = $this->get_page_id( $query ) ) && false !== $n = array_search( $page_id, $pages ) ) {
				$lang = $languages[ $n ];
				$query->is_home              = false;
				$query->is_page              = true;
				$query->is_post_type_archive = true;
				$query->set( 'page_id', $page_id );
				$query->set( 'post_type', 'product' );
			}

			// Multiple domains (when the url contains the page slug).
			elseif ( is_post_type_archive( 'product' ) && ! empty( PLL()->curlang ) ) {
				$query->is_page = true;
				$query->set( 'page_id', PLL()->curlang->page_on_front );
			}

			// Language set from the content.
			elseif ( is_post_type_archive( 'product' ) && ! empty( $qvars['lang'] ) && $lang = PLL()->model->get_language( $qvars['lang'] ) ) {
				$query->is_page = true;
				$query->set( 'page_id', $lang->page_on_front );
			}
		}

		// My Account and checkout endpoints.
		if ( array_intersect( array( wc_get_page_id( 'myaccount' ), wc_get_page_id( 'checkout' ) ), $pages ) && array_intersect( array_keys( $query->query ), WC()->query->get_query_vars() ) ) {
			if ( ! $this->get_page_id( $query ) ) {
				if ( ! $lang = PLL()->model->get_language( get_query_var( 'lang' ) ) ) {
					// Language set from the content + language code hidden in the url.
					$lang = PLL()->model->get_language( PLL()->options['default_lang'] );
				}
				$query->is_home     = false;
				$query->is_tax      = false;
				$query->is_archive  = false;
				$query->is_page     = true;
				$query->is_singular = true;
				$query->set( 'page_id', $lang->page_on_front );
				unset( $query->queried_object );
			}
			add_filter( 'redirect_canonical', '__return_false' );
			add_filter( 'pll_check_canonical_url', '__return_false' );
		}

		return $lang;
	}
}
