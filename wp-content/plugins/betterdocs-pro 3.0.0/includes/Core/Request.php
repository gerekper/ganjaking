<?php

namespace WPDeveloper\BetterDocsPro\Core;

use WPDeveloper\BetterDocs\Core\Request as FreeRequest;

/**
 * @property Settings $settings
 * @property Rewrite $rewrite
 * @method void set_perma_structure( $structure )
 * @method void set_query_vars( $vars )
 */
class Request extends FreeRequest {
    protected $mkb_enabled = false;

    public function init(){
        if( is_admin() ) {
            return;
        }

        if( isset( $this->settings ) && $this->settings instanceof Settings ) {
            $this->mkb_enabled = $this->settings->get('multiple_kb', false);
        }

        parent::init();

        if( $this->mkb_enabled ) {
            $_root_structure = trim( $this->rewrite->get_base_slug(), '/' );
            $_knowledge_base_structure = $_root_structure . '/%knowledge_base%';
            $_knowledge_base_category_structure = $_root_structure . '/%knowledge_base%/%doc_category%';

            $this->set_perma_structure([
                'is_knowledge_base' => $_knowledge_base_structure,
                'is_knowledge_base_category' => $_knowledge_base_category_structure,
            ]);

            $this->set_query_vars([
                'is_docs_feed' => [ 'doc_category', 'knowledge_base' ],
                'is_knowledge_base' => [ 'knowledge_base' ],
                'is_knowledge_base_category' => [ 'doc_category', 'knowledge_base' ],
            ]);
        }
    }

    public function is_knowledge_base( $query_vars ){
        return $this->term_exists( $query_vars, 'knowledge_base' );
    }

    public function is_knowledge_base_category( $query_vars ){
        return $this->term_exists( $query_vars, 'doc_category' );
    }

    protected function term_exists( $query_vars, $taxonomy ){
        if( ! isset( $query_vars[ $taxonomy ] ) ) {
            return false;
        }

        return term_exists( $query_vars[ $taxonomy ], $taxonomy );
    }

    /**
     * Parse the request
     *
     * @param \WP $wp
     * @return void
     */
    public function parse( $wp ){
        if( is_admin() ) {
            return;
        }

        parent::parse( $wp );

        /**
         * Decide which MKB is belong to this doc.
         */
        if ( $this->mkb_enabled ) {
            if ( isset( $wp->query_vars['name'], $wp->query_vars['post_type'] ) && $wp->query_vars['post_type'] === 'docs' ) {
                $_kb_slug = isset( $_COOKIE['last_knowledge_base'] ) ? trim( $_COOKIE['last_knowledge_base'] ) : '';

                if ( isset( $wp->query_vars['knowledge_base'] ) ) {
                    $_kb_slug = $wp->query_vars['knowledge_base'];
                }

                if ( $_kb_slug != '' ) {
                    $wp->query_vars['knowledge_base'] = $_kb_slug;
                } else {
                    $post = get_page_by_path( $wp->query_vars['name'], OBJECT, 'docs' );
                    if ( ! isset( $wp->query_vars['knowledge_base'] ) ) {
                        if ( isset( $post->ID ) ) {
                            $terms = wp_get_post_terms( $post->ID, 'knowledge_base' );
                            if ( is_array( $terms ) && ! empty( $terms ) ) {
                                $wp->query_vars['knowledge_base'] = $terms[0]->slug;
                            }
                        }
                    }
                }

                if ( ! isset( $wp->query_vars['doc_category'] ) ) {
                    $post = get_page_by_path( $wp->query_vars['name'], OBJECT, 'docs' );
                    if ( isset( $post->ID ) ) {
                        $terms     = wp_get_post_terms( $post->ID, 'doc_category' );
                        $_cat_slug = betterdocs()->query->get_doc_term_from_kb( $terms, $_kb_slug );

                        if ( $_cat_slug ) {
                            $wp->query_vars['doc_category'] = $_cat_slug;
                        }
                    }
                }
            }
        }
    }
}
