<?php

namespace WPDeveloper\BetterDocsPro\Core;
use WPDeveloper\BetterDocs\Core\Rewrite as FreeRewrite;

class Rewrite extends FreeRewrite {
    public function init() {
        parent::init();

        add_action( 'term_link', [$this, 'term_link'], 10, 3 );
    }

    public function remove_knowledge_base_placeholder( $permalink ) {
        $permalink_array = $this->permalink_structure( $permalink, 'arraywithpercent' );
        $permalink_array = array_filter( $permalink_array, function ( $item ) {
            return $item !== '%knowledge_base%';
        } );

        return trailingslashit( implode( '/', $permalink_array ) );
    }

    /**
     * This method is hooked with an action called 'betterdocs::settings::saved'
     * also an override function Of
     *
     * @since 2.5.0
     *
     * @param bool $_saved
     * @param array $_settings
     * @param array $_old_settings
     *
     * @return void
     */
    public function save_permalink_structure( $_saved, $_settings, $_old_settings ) {
        if ( $_settings['multiple_kb'] == false ) {
            $_settings['permalink_structure'] = $this->remove_knowledge_base_placeholder( $_settings['permalink_structure'] );
        }

        parent::save_permalink_structure( $_saved, $_settings, $_old_settings );

        /**
         * This block of code decides whether it needs to be flushed or not.
         * Flush happens after register the post type.
         */
        switch ( true ) {
            case $_settings['multiple_kb'] !== $_old_settings['multiple_kb']:
                $this->database->set_transient( 'betterdocs_flush_rewrite_rules', true );
                break;
        }
    }

    public function rules() {
        $base = $this->get_base_slug();

        if ( betterdocs()->settings->get( 'multiple_kb', false ) ) {
            $this->add_rewrite_rule( $base . '/([^/]+)/?$', 'index.php?knowledge_base=$matches[1]' );
            $this->add_rewrite_rule(
                $base . '/([^/]+)/([^/]+)/?$',
                'index.php?knowledge_base=$matches[1]&doc_category=$matches[2]'
            );
        }

        parent::rules();
    }

    public function term_link( $termlink, $term, $taxonomy ) {
        if ( $taxonomy != 'doc_category' ) {
            return $termlink;
        }

        $_kb_slug = betterdocs_pro()->multiple_kb->get_kb_slug();

        if ( empty( $_kb_slug ) ) {
            $current_term = get_term_by( 'slug', $term->slug, $taxonomy, OBJECT );
            $_term_attr   = get_term_meta( $current_term->term_id, 'doc_category_knowledge_base', true );

            if ( empty( $_term_attr[0] ) ) {
                $_kb_slug = 'non-knowledgebase';
            } else {
                $_kb_slug = $_term_attr[0];
            }
        }

        return str_replace( '%knowledge_base%', $_kb_slug, $termlink );
    }
}
