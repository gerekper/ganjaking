<?php


namespace WCML\Reports\Products;


use WCML\Rest\Functions;

class Query implements \IWPML_REST_Action {

	/**
	 * Registers hooks.
	 */
	public function add_hooks() {
		if ( Functions::isAnalyticsRestRequest() ) {
			add_filter( 'woocommerce_analytics_products_select_query', [ $this, 'joinProductTranslations' ] );
			add_filter( 'woocommerce_analytics_products_select_query', [ $this, 'translateProductTitles' ] );
			add_filter( 'woocommerce_analytics_variations_select_query', [ $this, 'joinProductTranslations' ] );
			add_filter( 'woocommerce_analytics_variations_select_query', [ $this, 'translateProductTitles' ] );
		}
	}

	/**
	 * @param object $results Products query.
	 *
	 * @return object
	 */
	public function joinProductTranslations( $results ) {
		if ( $this->isValidResultsObject( $results ) ) {
			$previousCount = count( $results->data );
			$results->data = $this->merge( $results->data );
			if ( isset( $results->total ) ) {
				$results->total -= $previousCount - count( $results->data );
			}
		}

		return $results;
	}

	/**
	 * Validates if products query is valid.
	 *
	 * @param object $results
	 *
	 * @return bool
	 */
	private function isValidResultsObject( $results ) {
		if( empty( $results->data ) || ! is_array( $results->data ) ) {
			return false;
		}
		foreach ( $results->data as $key => $val ) {
			if ( ! isset( $val['product_id'] ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Removes translated products from results and updates numeric values in original product.
	 *
	 * @param array $products
	 *
	 * @return mixed
	 */
	private function merge( $products ) {
		$currentLanguage = apply_filters( 'wpml_current_language', null );
		foreach( $products as $index => $product ) {
			$productsDetails = $this->getProductLanguageDetails( $product );
			if ( $this->isProductTranslated( $productsDetails, $currentLanguage ) ) {
				$products = $this->mergeTranslationIntoDefault( $products, $index, $product, $productsDetails );
			}
		}

		return $products;
	}

	/**
	 * Checks if product is not in currently displayed language.
	 *
	 * @param object $productDetails
	 * @param string $currentLanguage
	 *
	 * @return bool
	 */
	private function isProductTranslated( $productDetails, $currentLanguage ) {
		return isset( $productDetails->language_code )
		       && $productDetails->language_code !== $currentLanguage;
	}

	/**
	 * @param array  $products
	 * @param int    $translatedProductIndex
	 * @param array  $translatedProduct
	 * @param object $productsDetails
	 *
	 * @return mixed
	 */
	private function mergeTranslationIntoDefault( $products, $translatedProductIndex, $translatedProduct, $productsDetails ) {
		$originalProductId = apply_filters( 'wpml_object_id', $productsDetails->element_id, 'product' );
		if ( $originalProductId ) {
			foreach ( $products as $index => $product ) {
				if ( $product['product_id'] === $originalProductId ) {
					$products = $this->removeTranslatedProduct( $products, $translatedProductIndex );
					$products = $this->updateOriginalProductFields( $products, $index, $translatedProduct );
				}
			}
		}
		return $products;
	}

	/**
	 * Remove translated product from product lists.
	 *
	 * @param array $products Filtered products list.
	 * @param int   $key      Index of translated product in array.
	 *
	 * @return array
	 */
	private function removeTranslatedProduct( $products, $key ) {
		return array_diff_key( $products, [ $key => '' ] );
	}

	/**
	 * Update numeric values in original product by adding values from translated.
	 *
	 * @param array $products
	 * @param int   $index
	 * @param array $translatedProduct
	 *
	 * @return array|mixed
	 */
	private function updateOriginalProductFields( $products, $index, $translatedProduct ) {
		$products = $this->sumValues( 'items_sold', $products, $index, $translatedProduct );
		$products = $this->sumValues( 'net_revenue', $products, $index, $translatedProduct );
		$products = $this->sumValues( 'orders_count', $products, $index, $translatedProduct );
		return $products;
	}

	/**
	 * @param string $field
	 * @param array  $products
	 * @param int    $index
	 * @param array  $translatedProduct
	 *
	 * @return mixed
	 */
	private function sumValues( $field, $products, $index, $translatedProduct ) {
		if ( isset( $translatedProduct[ $field ], $products[ $index ][ $field ] ) ) {
			$products[ $index ][ $field ] += $translatedProduct[ $field ];
		}
		return $products;
	}

	/**
	 * @param array $product
	 *
	 * @return mixed|void
	 */
	private function getProductLanguageDetails( $product ) {
		$args = [
			'element_id'   => $product['product_id'],
			'element_type' => 'product'
		];
		return apply_filters( 'wpml_element_language_details', null, $args );
	}

	/**
	 * @param object $results Categories query (note: in March 2021, WC Admin code is showing a PHPDoc returning an array which is not what we get)
	 *
	 * @return object
	 */
	public function translateProductTitles( $results ) {
		$results->data = wpml_collect( $results->data )
			->map( function( $row ) {
				if ( $row['extended_info']['name'] ) {
					$row['product_id'] = apply_filters( 'wpml_object_id', $row['product_id'], get_post_type( $row['product_id'] ), true );
					$product = wc_get_product( $row['product_id'] );
					if ( $product ) {
						$row['extended_info']['name'] = $product->get_title();
					}
					if ( isset( $row['extended_info']['category_ids'] ) ) {
						$row['extended_info']['category_ids'] = wpml_collect( $row['extended_info']['category_ids'] )
							->map( function( $id ) {
								return apply_filters( 'wpml_object_id', $id, 'product_cat', true );
							} )->toArray();
					}
				}
				return $row;
			} )->toArray();

		return $results;
	}

}