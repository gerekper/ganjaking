<?php


namespace Premmerce\WooCommercePinterest\Tags;

/**
 * Class PinterestTagsTaxonomy
 *
 * @package Premmerce\WooCommercePinterest\Tags
 *
 * This class is responsible for registering  custom WordPress taxonomy Pinterest Tags
 */
class PinterestTagsTaxonomy {

	const PINTEREST_TAGS_TAXONOMY_SLUG = 'pinterest_tags';

	/**
	 * Register pinterest_tags custom taxonomy
	 */
	public function registerTaxonomy() {


		register_taxonomy(self::PINTEREST_TAGS_TAXONOMY_SLUG, 'product', array(
			'hierarchical' => false,
			'labels' => $this->getLabels(),
			'show_ui' => true,
			'meta_box_cb' => false,
			'show_admin_column' => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var' => true,
			'rewrite' => array( 'slug' => 'pinterest_tags' ),
		));

		register_taxonomy_for_object_type('pinterest_tags', 'product');

	}

	/**
	 * Return labels
	 *
	 * @return array
	 */
	public function getLabels() {
		$labels = array(
			'name' => _x( 'Pinterest hashtags', 'taxonomy general name', 'woocommerce-pinterest' ),
			'singular_name' => _x( 'Pinterest hastag', 'taxonomy singular name', 'woocommerce-pinterest' ),
			'search_items' =>  __( 'Search Pinterest hashtags', 'woocommerce-pinterest' ),
			'popular_items' => __( 'Popular Pinterest hashtags', 'woocommerce-pinterest' ),
			'all_items' => __( 'All Pinterest hashtags', 'woocommerce-pinterest' ),
			'parent_item' => null,
			'parent_item_colon' => null,
			'edit_item' => __( 'Edit Pinterest hashtag', 'woocommerce-pinterest' ),
			'update_item' => __( 'Update Pinterest hashtag', 'woocommerce-pinterest' ),
			'add_new_item' => __( 'Add new Pinterest hashtag', 'woocommerce-pinterest' ),
			'new_item_name' => __( 'New Pinterest hashtag', 'woocommerce-pinterest' ),
			'separate_items_with_commas' => __( 'Separate Pinterest hashtags with commas', 'woocommerce-pinterest' ),
			'add_or_remove_items' => __( 'Add or remove Pinterest hashtag', 'woocommerce-pinterest' ),
			'choose_from_most_used' => __( 'Choose from the most commonly used Pinterest hashtags', 'woocommerce-pinterest' ),
			'menu_name' => __( 'Pinterest hashtag', 'woocommerce-pinterest' ),
		);

		return $labels;
	}
}
