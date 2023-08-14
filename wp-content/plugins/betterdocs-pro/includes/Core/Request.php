<?php

namespace WPDeveloper\BetterDocsPro\Core;

use WP_Query;
use WPDeveloper\BetterDocs\Core\Settings;
use WPDeveloper\BetterDocs\Core\Request as FreeRequest;

class Request extends FreeRequest {
    /**
     * Parse the request
     *
     * @param \WP $wp
     * @return void
     */
    public function parse( $wp ) {
        $_docs_perma_struct = betterdocs()->rewrite->permalink_structure( '', 'array' );
        $_kb_enabled        = betterdocs()->settings->get( 'multiple_kb', false );

        $_origin_vars = $wp->query_vars;
        $_request     = explode( '/', $wp->request );

        // dump( '_origin_vars', $_origin_vars );
        // dump( '$_docs_perma_struct', $_docs_perma_struct, '_request', $_request );

        /**
         * docs/kb-slug/
         * docs/kb-slug/cat-slug/
         *
         * docs/docs-slug
         * docs/cat-slug/docs-slug
         * docs/kb-slug/docs-slug
         * docs/kb-slug/cat-slug/docs-slug
         * docs/cat-slug/kb-slug/docs-slug
         */

        if ( $_kb_enabled ) {
            $_request_count = count( $_request );
            $_perma_count   = count( $_docs_perma_struct );

            switch ( true ) {
                case $_request_count == 2 && $_perma_count == 1:
                    if ( isset( $wp->query_vars['knowledge_base'] ) && ! term_exists( trim( $wp->query_vars['knowledge_base'] ), 'knowledge_base' ) ) {
                        $wp->query_vars['name']      = $wp->query_vars['knowledge_base'];
                        $wp->query_vars['docs']      = $wp->query_vars['knowledge_base'];
                        $wp->query_vars['post_type'] = 'docs';
                        unset( $wp->query_vars['knowledge_base'] );
                    }

                    break;
                case $_request_count == 3 && $_perma_count == 2:
                    if ( end( $_docs_perma_struct ) == 'doc_category' && ! term_exists( trim( $wp->query_vars['doc_category'] ), 'doc_category' ) ) {
                        $wp->query_vars['name']         = $wp->query_vars['doc_category'];
                        $wp->query_vars['docs']         = $wp->query_vars['doc_category'];
                        $wp->query_vars['post_type']    = 'docs';
                        $wp->query_vars['doc_category'] = $wp->query_vars['knowledge_base'];

                        unset( $wp->query_vars['knowledge_base'] );
                    }
                    if ( end( $_docs_perma_struct ) == 'knowledge_base' && ! term_exists( trim( $wp->query_vars['doc_category'] ), 'doc_category' ) ) {
                        $wp->query_vars['name']      = $wp->query_vars['doc_category'];
                        $wp->query_vars['docs']      = $wp->query_vars['doc_category'];
                        $wp->query_vars['post_type'] = 'docs';

                        unset( $wp->query_vars['doc_category'] );
                    }
                    $_cat_slug = end( $_request );
                    break;
                case $_request_count == 3 && $_perma_count === 3:
                    if ( isset( $wp->query_vars['knowledge_base'] ) && ! term_exists( trim( $wp->query_vars['knowledge_base'] ), 'knowledge_base' ) ) {
                        $_temp_cat                        = $wp->query_vars['doc_category'];
                        $wp->query_vars['doc_category']   = $wp->query_vars['knowledge_base'];
                        $wp->query_vars['knowledge_base'] = $_temp_cat;
                    }
                    break;
            }
        }

        /**
         * Decide which MKB is belong to this doc.
         */
        if ( $_kb_enabled ) {
            if ( isset( $wp->query_vars['name'] ) ) {
                $_kb_slug = '';
                if( isset( $wp->query_vars['knowledge_base'] ) ) {
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
                        $terms = wp_get_post_terms( $post->ID, 'doc_category' );
                        $_cat_slug = betterdocs()->query->get_doc_term_from_kb( $terms, $_kb_slug );

                        if( $_cat_slug ) {
                            $wp->query_vars['doc_category'] = $_cat_slug;
                        }
                    }
                }
            }
        }

        // if ( $_kb_enabled
        //     && isset( $wp->query_vars['knowledge_base'], $wp->query_vars['doc_category'] )
        //     && count( $_docs_perma_struct ) > 1 ) {
        //     switch ( true ) {
        //         case $_docs_perma_struct[1] == 'doc_category'
        //             && ! term_exists( trim( $wp->query_vars['doc_category'] ), 'doc_category' ):
        //             $wp->query_vars['name']         = $wp->query_vars['doc_category'];
        //             $wp->query_vars['doc_category'] = $wp->query_vars['knowledge_base'];
        //             $wp->query_vars['post_type']    = 'docs';

        //             $post  = get_page_by_path( $wp->query_vars['name'], OBJECT, 'docs' );
        //             if( isset( $post->ID ) ) {
        //                 $terms = wp_get_post_terms( $post->ID, 'knowledge_base' );
        //                 if ( is_array( $terms ) && ! empty( $terms ) ) {
        //                     $wp->query_vars['knowledge_base'] = $terms[0]->slug;
        //                 }
        //             }
        //             break;
        //         case $_docs_perma_struct[1] == 'knowledge_base'
        //             && ! term_exists( trim( $wp->query_vars['doc_category'] ), 'doc_category' ):
        //             $wp->query_vars['name']      = $wp->query_vars['doc_category'];
        //             $wp->query_vars['post_type'] = 'docs';

        //             unset( $wp->query_vars['doc_category'] );
        //             break;
        //     }
        // }

        // if ( $_kb_enabled && isset( $wp->query_vars['knowledge_base'] ) ) {
        //     if ( ! term_exists( trim( $wp->query_vars['knowledge_base'] ), 'knowledge_base' ) ) {
        //         $wp->query_vars['name']      = $wp->query_vars['knowledge_base'];
        //         $wp->query_vars['post_type'] = 'docs';

        //         unset( $wp->query_vars['knowledge_base'] );
        //     }
        // }
    }

    public static function fix_query_vars( $slug, $wp ) {
        $return = false;
        $post   = self::post_exists_by_slug( $slug );
        if ( ! empty( $post->post_type ) ) {
            if ( $post->post_type == 'docs' ) {
                $wp->query_vars['docs']      = $slug;
                $wp->query_vars['name']      = $slug;
                $wp->query_vars['post_type'] = 'docs';
                $return                      = true;
            } elseif ( $post->post_type == 'page' ) {
                $wp->query_vars['pagename'] = $slug;
                $return                     = true;
            } elseif ( $post->post_type == 'post' ) {
                $wp->query_vars['name'] = $slug;
                $return                 = true;
            }
        }
        return $return;
    }

    public static function post_exists_by_slug( $post_slug, $post_type = 'docs' ) {
        $loop_posts = new WP_Query( ['post_type' => $post_type, 'post_status' => 'any', 'name' => $post_slug, 'posts_per_page' => 1, 'fields' => 'all'] );

        return ( $loop_posts->have_posts() ? $loop_posts->posts[0] : false );
    }

    public function post_exists( $slug, $post_type = 'docs' ) {
        global $wpdb;
        $post_if = $wpdb->get_var( "SELECT count(ID) FROM $wpdb->posts WHERE post_type = '. $post_type .' AND post_name = '" . $slug . "' LIMIT 1" );

        return $post_if;
    }
}
