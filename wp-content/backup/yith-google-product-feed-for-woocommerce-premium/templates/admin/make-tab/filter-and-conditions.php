<?php

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

 $feed = get_post_meta( $post->ID, 'yith_wcgpf_save_feed', true );
?>

<div class="yith-wcact-filter-and-conditions-content">
    <div class="yith-wcgpf-filter-categories yith-wcact-filter-and-conditions">
        <label for="yith_category_selector"><?php _e('Filter by categories:', 'yith-google-product-feed-for-woocommerce'); ?></label>
            <?php
            $data_selected = array();
            if (! empty( $feed['categories_selected'] ) ) {
                $categories = is_array( $feed['categories_selected'] ) ? $feed['categories_selected'] : explode( ',', $feed['categories_selected'] );
                if ( $categories ) {
                    foreach ( $categories as $category_id ) {
                        $term = get_term_by( 'id', $category_id, 'product_cat', 'ARRAY_A' );
                        $data_selected[$category_id] = $term['name'];
                    }
                }
            }
            $search_cat_array = array(
                'type'              => '',
                'class'             => version_compare(WC()->version,'3.0.0','>=') ? 'yith-wcgpf-category-search yith-wcgpf-information' : 'yith-wcgpf-category-search',
                'id'                => 'yith_category_selector',
                'name'              => 'yith-feed-category',
                'data-placeholder'  => esc_attr__( 'Search for a category&hellip;', 'yith-google-product-feed-for-woocommerce' ),
                'data-allow_clear'  => false,
                'data-selected'     => $data_selected,
                'data-multiple'     => true,
                'data-action'       => '',
                'value'             => empty( $feed['categories_selected'] ) ? '' : $feed['categories_selected'],
                'style'             => ''
            );
            yit_add_select2_fields( $search_cat_array );
            ?>

    </div>
    <div class="yith-wcgpf-filter-tags yith-wcact-filter-and-conditions">
        <label for="yith_tags_selector"><?php _e('Filter by tags:', 'yith-google-product-feed-for-woocommerce'); ?></label>

        <?php
            $data_selected = array();
            if (! empty( $feed['tags_selected'] ) ) {
                $tags = is_array( $feed['tags_selected']) ? $feed['tags_selected'] : explode( ',', $feed['tags_selected'] );
                if ( $tags ) {
                    foreach ( $tags as $tag_id ) {
                        $term = get_term_by( 'id', $tag_id, 'product_tag', 'ARRAY_A' );
                        $data_selected[$tag_id] = $term['name'];
                    }
                }
            }
            $search_tag_array = array(
                'type'              => '',
                'class'             => version_compare(WC()->version,'3.0.0','>=') ? ' yith-wcgpf-tags-search yith-wcgpf-information' : 'yith-wcgpf-tags-search',
                'id'                => 'yith_tags_selector',
                'name'              => 'yith-feed-tags',
                'data-placeholder'  => esc_attr__( 'Search for a tag&hellip;', 'yith-google-product-feed-for-woocommerce' ),
                'data-allow_clear'  => false,
                'data-selected'     => $data_selected,
                'data-multiple'     => true,
                'data-action'       => '',
                'value'             => empty( $feed['tags_selected'] ) ? '' : $feed['tags_selected'],
                'style'             => ''
            );
            yit_add_select2_fields( $search_tag_array );
        ?>
    </div>

    <div class="yith-wcgpf-include-products yith-wcact-filter-and-conditions">
        <label for="yith_include_product_selector"><?php _e('Include products:', 'yith-google-product-feed-for-woocommerce'); ?></label>
            <?php

                $data_selected = array();

                if (! empty( $feed['include_products'] ) ) {
                    $products = is_array( $feed['include_products']) ? $feed['include_products'] : explode( ',', $feed['include_products'] );
                    if ( $products ) {
                        foreach ( $products as $product_id ) {
                            $product = wc_get_product( $product_id );
                            if ( $product ) {
	                            $data_selected[ $product_id ]
		                            = $product->get_formatted_name();
                            }
                        }
                    }
                }
                $search_include_products_array = array(
                    'type'              => '',
                    'class'             => version_compare(WC()->version,'3.0.0','>=') ? 'wc-product-search yith-wcgpf-information' : 'wc-product-search',
                    'id'                => 'yith_include_product_selector',
                    'name'              => 'yith-feed-include-product',
                    'data-placeholder'  => esc_attr__( 'Search for a product&hellip;', 'yith-google-product-feed-for-woocommerce' ),
                    'data-allow_clear'  => false,
                    'data-selected'     => $data_selected,
                    'data-multiple'     => true,
                    'data-action'       => 'woocommerce_json_search_products',
                    'value'             => empty( $feed['include_products'] ) ? '' : $feed['include_products'],
                    'style'             => ''
                );
                yit_add_select2_fields( $search_include_products_array );
            ?>
    </div>

    <div class="yith-wcgpf-exclude-products yith-wcact-filter-and-conditions">
        <label for="yith_exclude_product_selector"><?php _e('Exclude products:', 'yith-google-product-feed-for-woocommerce'); ?></label>
        <?php

            $data_selected = array();
            if (! empty( $feed['exclude_products'] ) ) {
                $products = is_array( $feed['exclude_products']) ? $feed['exclude_products'] : explode( ',', $feed['exclude_products'] );
                if ( $products ) {
                    foreach ( $products as $product_id ) {
                        $product = wc_get_product( $product_id );
                        if( $product instanceof WC_Product ) {
                            $data_selected[$product_id] = $product->get_formatted_name();
                        }
                    }
                }
            }
            $search_exclude_products_array = array(
                'type'              => '',
                'class'             => version_compare(WC()->version,'3.0.0','>=') ? 'wc-product-search yith-wcgpf-information' : 'wc-product-search',
                'id'                => 'yith_exclude_product_selector',
                'name'              => 'yith-feed-exclude-product',
                'data-placeholder'  => esc_attr__( 'Search for a product&hellip;', 'yith-google-product-feed-for-woocommerce' ),
                'data-allow_clear'  => false,
                'data-selected'     => $data_selected,
                'data-multiple'     => true,
                'data-action'       => 'woocommerce_json_search_products',
                'value'             => empty( $feed['exclude_products'] ) ? '' : $feed['exclude_products'],
                'style'             => ''
            );
            yit_add_select2_fields( $search_exclude_products_array );
        ?>
    </div>
</div>
