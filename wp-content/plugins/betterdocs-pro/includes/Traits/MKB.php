<?php

namespace WPDeveloper\BetterDocsPro\Traits;

trait MKB {
    public function reset_attributes() {
        $this->attributes['term_icon_meta_key'] = 'knowledge_base_image-id';

        // $this->attributes['terms_order'] = 'ASC';
        if ( betterdocs()->settings->get( 'alphabetically_order_term', false ) ) {
            $this->attributes['terms_orderby'] = 'name';
        } else {
            $this->attributes['meta_key']      = 'kb_order';
        }
    }

    public function term_permalink( $permalink, $term, $taxonomy ) {
        return get_term_link( $term->term_id, 'knowledge_base' );
    }

    public function kb_terms( $term, $taxonomy ) {
        $current_term = get_term_by( 'slug', $term->slug, $taxonomy, OBJECT );
        $_term_attr   = get_term_meta( $current_term->term_id, 'doc_category_knowledge_base', true );

        if ( ! empty( $_term_attr ) ) {
            $_term_attr = array_values( array_filter($_term_attr, function( $item ){
                return ! empty( $item );
            }) );
        }

        return $_term_attr;
    }

    public function get_first_kb_slug( $term, $taxonomy ) {
        $_term_attr = $this->kb_terms( $term, $taxonomy );

        if ( empty( $_term_attr ) ) {
            $_kb_slug = 'non-knowledgebase';
        } else {
            $_kb_slug = $_term_attr[0];
        }

        return $_kb_slug;
    }
}
