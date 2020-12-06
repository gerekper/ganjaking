<?php


namespace Premmerce\WooCommercePinterest\Tags;

/**
 * Class PinterestTagsToCategoriesMapper
 *
 * @package Premmerce\WooCommercePinterest\Tags
 *
 * This class is responsible for connections between Pinterest Tags and WordPress categories
 */
class PinterestTagsToCategoriesMapper {

	const CATEGORY_PINTEREST_TAGS_IDS = 'woocommerce_pinterest_term_pinterest_tags_terms_ids';

	/**
	 * Get pinterest tags ids by category id
	 *
	 * @param $catId
	 *
	 * @return int[]
	 */
	public function getPinterestTagsIdsByCategoryId( $catId) {
		$pinterestTagsIds = get_term_meta($catId, self::CATEGORY_PINTEREST_TAGS_IDS, true);

		return is_array($pinterestTagsIds) ? $pinterestTagsIds : array();
	}

	/**
	 * Set category Pinterest tags ids
	 *
	 * @param int $catId
	 * @param int[]
	 */
	public function setCategoryPinterestTagsIds( $catId, array $tagsIds) {
		update_term_meta($catId, self::CATEGORY_PINTEREST_TAGS_IDS, $tagsIds);
	}
}
