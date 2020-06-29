<?php

namespace Premmerce\PrimaryCategory\Model;

use \WP_Term;

class Model
{
	const PRIMARY_CATEGORY_META_FIELD_KEY = 'premmerce_primary_category_id';

	public function __construct()
    {
        add_filter('woocommerce_product_data_store_cpt_get_products_query', [$this, 'extendWcProductQuery'], 10, 2);
    }

    /**
	 * @param int $postId
	 * @return int|null
	 */
	public function getPrimaryCategoryId($postId)
	{
		return get_post_meta($postId, self::PRIMARY_CATEGORY_META_FIELD_KEY, true) ?: null;
	}

	/**
	 * @param $postId
	 * @param $categoryId
	 */
	public function updatePrimaryCategory($postId, $categoryId)
	{
		update_post_meta($postId, self::PRIMARY_CATEGORY_META_FIELD_KEY, $categoryId);
	}

    /**
     * @param int $termId
     * @param int $termTaxonomyId
     */
	public function cleanDeletedCategoryInProductsMeta($termId, $termTaxonomyId)
    {
        $productsToClean = $this->getProductsIdsByPrimaryCategory($termTaxonomyId);
        foreach($productsToClean as $productId){
            $this->deletePrimaryCategory($productId);
        }
    }

    private function getProductsIdsByPrimaryCategory($categoryId)
    {
        return wc_get_products([
            'limit' => -1,
            'return' => 'ids',
            self::PRIMARY_CATEGORY_META_FIELD_KEY => $categoryId
        ]) ?: [];
    }

    private function deletePrimaryCategory($productId)
    {
        delete_post_meta($productId, self::PRIMARY_CATEGORY_META_FIELD_KEY);
    }

    public function cleanProductPrimaryCategoryIfDeleted($productId)
    {
        $productPrimaryCatId = $this->getPrimaryCategoryId($productId);

        if($productPrimaryCatId && ! $this->productHasCat($productId, $productPrimaryCatId)){
            $this->deletePrimaryCategory($productId);
        }
    }

    /**
     * @param int $productId
     * @param int $catId
     *
     * @return bool
     */
    private function productHasCat($productId, $catId)
    {
        $categoriesIds = wc_get_product_term_ids($productId, 'product_cat');

        return in_array($catId, $categoriesIds);
    }

    /**
     * @param array $wpQueryArgs
     * @param array $wcProductQueryVars
     *
     * @return array
     */
    public function extendWcProductQuery(array $wpQueryArgs, array $wcProductQueryVars)
    {
        if( ! empty($wcProductQueryVars[self::PRIMARY_CATEGORY_META_FIELD_KEY])){
            $wpQueryArgs['meta_query'][] = [
                'key' => self::PRIMARY_CATEGORY_META_FIELD_KEY,
                'value' => esc_attr($wcProductQueryVars[self::PRIMARY_CATEGORY_META_FIELD_KEY])
            ];
        }

        return $wpQueryArgs;
    }
}
