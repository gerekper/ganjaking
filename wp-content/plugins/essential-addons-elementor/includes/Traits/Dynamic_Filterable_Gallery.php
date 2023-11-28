<?php

namespace Essential_Addons_Elementor\Pro\Traits;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

trait Dynamic_Filterable_Gallery
{
    public static function get_dynamic_gallery_item_classes($show_category_child_items = 0, $show_product_cat_child_items = 0)
    {
        $classes = [];

        // collect post class
        $get_object_taxonomies = get_object_taxonomies( get_post_type( get_the_ID() ) );
        
        $taxonomies = wp_get_object_terms( get_the_ID(), $get_object_taxonomies, array( "fields" => "slugs" ) );
        
        if ( $taxonomies ) {
            foreach ( $taxonomies as $taxonomy ) {
                $classes[] = $taxonomy;
            }
        }

        $category_or_product_cat = '';
        if(1 === $show_category_child_items && !empty($get_object_taxonomies) && in_array('category', $get_object_taxonomies)) {
            $category_or_product_cat = 'category';
        }

        if(1 === $show_product_cat_child_items && !empty($get_object_taxonomies) && in_array('product_cat', $get_object_taxonomies)){
            $category_or_product_cat = 'product_cat';
        }

        if($category_or_product_cat){
            $terms = get_the_terms( get_the_ID() , $category_or_product_cat);
            if($terms) {
                foreach( $terms as $term ) {
                    $parent_list = get_term_parents_list($term->term_id, $category_or_product_cat, array( "format" => "slug", 'separator' => '/', "link" => 0, "inclusive" => 0 ) );
                    $parent_list = explode( '/', $parent_list );
                    $classes = array_merge($classes, $parent_list);
                }
            }
        }
        
        if ($categories = get_the_category(get_the_ID())) {
            foreach ($categories as $category) {
                $classes[] = $category->slug;
            }
        }

        if ($tags = get_the_tags()) {
            foreach ($tags as $tag) {
                $classes[] = $tag->slug;
            }
        }

        if ($product_cats = get_the_terms(get_the_ID(), 'product_cat')) {
            foreach ($product_cats as $cat) {
                if(is_object($cat)) {
                    $classes[] = $cat->slug;
                }
            }
        }

        return $classes;
    }
}
