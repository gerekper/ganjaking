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
}
