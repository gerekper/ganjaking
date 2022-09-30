<?php
$output                    = function_exists('betterdocs_generate_output') ? betterdocs_generate_output() : '';
$output_pro                = function_exists('betterdocs_generate_output_pro') ? betterdocs_generate_output_pro() : '';
$enable_toc                = BetterDocs_DB::get_settings('enable_toc') != 'off' ? true : false;
$enable_sticky_toc         = BetterDocs_DB::get_settings('enable_sticky_toc') != 'off' ? true : false;
$live_search               = BetterDocs_DB::get_settings('live_search') != 'off' ? true : false;
$enable_print_icon         = BetterDocs_DB::get_settings('enable_print_icon') != 'off' ? true : false; 
$enable_tags               = BetterDocs_DB::get_settings('enable_tags') != 'off' ? true : false;
$enable_navigation         = BetterDocs_DB::get_settings('enable_navigation') != 'off' ? true : false;
$enable_credit             = BetterDocs_DB::get_settings('enable_credit') != 'off' ? true : false;
$enable_comment            = BetterDocs_DB::get_settings('enable_comment') != 'off' ? true : false; 
$enable_sidebar            = BetterDocs_DB::get_settings('enable_sidebar_cat_list') != 'off' ? true : false; 
$terms_orderby             = BetterDocs_DB::get_settings('terms_orderby');
$terms_order               = BetterDocs_DB::get_settings('terms_order');
$credit_markup             = $enable_credit ? '<div class="betterdocs-credit"><p>'.__('Powered by ', 'betterdocs-pro').'<a href="https://betterdocs.co" target="_blank">'. __('BetterDocs', 'betterdocs-pro').'</a></p></div>' : '';
$live_search_markup        = $live_search ? BetterDocs_Public::search() : '';
$print_icon_markup         = $enable_print_icon ? '<div class="betterdocs-print-pdf"><span class="betterdocs-print-btn"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80" width="20px"><path fill="#66798f" d="M14 16H66V24H14z"></path><path fill="#b0c1d4" d="M8,63.5c-3,0-5.5-2.5-5.5-5.5V26c0-3,2.5-5.5,5.5-5.5h64c3,0,5.5,2.5,5.5,5.5v32 c0,3-2.5,5.5-5.5,5.5H8z"></path><path fill="#66798f" d="M72,21c2.8,0,5,2.2,5,5v32c0,2.8-2.2,5-5,5H8c-2.8,0-5-2.2-5-5V26c0-2.8,2.2-5,5-5H72 M72,20H8 c-3.3,0-6,2.7-6,6v32c0,3.3,2.7,6,6,6h64c3.3,0,6-2.7,6-6V26C78,22.7,75.3,20,72,20L72,20z"></path><path fill="#fff" d="M16.5 2.5H63.5V23.5H16.5z"></path><path fill="#788b9c" d="M63,3v20H17V3H63 M64,2H16v22h48V2L64,2z"></path><path fill="#8bb7f0" d="M22,41.5c-3,0-5.5-2.5-5.5-5.5V20.5h47V36c0,3-2.5,5.5-5.5,5.5H22z"></path><path fill="#4e7ab5" d="M63,21v15c0,2.8-2.2,5-5,5H22c-2.8,0-5-2.2-5-5V21H63 M64,20H16v16c0,3.3,2.7,6,6,6h36 c3.3,0,6-2.7,6-6V20L64,20z"></path><path fill="#fff" d="M16.5 50.5H63.5V77.5H16.5z"></path><path fill="#788b9c" d="M63,51v26H17V51H63 M64,50H16v28h48V50L64,50z"></path><path fill="#d6e3ed" d="M17 52H63V56H17z"></path><path fill="#788b9c" d="M26 59H54V60H26zM26 67H54V68H26z"></path><g><path fill="#ffeea3" d="M70 28A2 2 0 1 0 70 32A2 2 0 1 0 70 28Z"></path></g><path fill="#66798f" d="M17,56v-4h46v4h2c1.7,0,3-1.3,3-3l0,0c0-1.7-1.3-3-3-3H15c-1.7,0-3,1.3-3,3l0,0c0,1.7,1.3,3,3,3H17z"></path></svg></span></div>' : '';
$betterdocs_title_before   = '<'.BetterDocs_Helper::html_tag( $output['betterdocs_post_title_tag'] ).' id="betterdocs-entry-title" class="betterdocs-entry-title">';
$betterdocs_title_after    = '</'.BetterDocs_Helper::html_tag( $output['betterdocs_post_title_tag'] ).'>';
$betterdocs_sidebar        = do_shortcode('[betterdocs_sidebar_list terms_title_tag="'.$output_pro['betterdocs_sidebar_title_tag_layout6'].'"]');
$post_tags_markup          = $enable_tags && class_exists('BetterDocs_Helper') ? BetterDocs_Helper::get_post_tags_markup( get_the_ID() ) : '';
$post_social_share         = get_theme_mod('betterdocs_post_social_share', true);

$toc_hierarchy          = BetterDocs_DB::get_settings('toc_hierarchy');
$toc_list_number        = BetterDocs_DB::get_settings('toc_list_number');
$collapsible_toc_mobile = BetterDocs_DB::get_settings('collapsible_toc_mobile');
$supported_tag          = BetterDocs_DB::get_settings('supported_heading_tag');
$htags                  = implode(',', $supported_tag);
$toc_markup             = $enable_toc && shortcode_exists('betterdocs_toc') ? do_shortcode("[betterdocs_toc htags='{$htags}' hierarchy='{$toc_hierarchy}' list_number='{$toc_list_number}' collapsible_on_mobile='{$collapsible_toc_mobile}']") : '';
$stick_toc_markup       = $enable_toc && $enable_sticky_toc ? '<div class="sticky-toc-container"><a class="close-toc" href="#"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path style="line-height:normal;text-indent:0;text-align:start;text-decoration-line:none;text-decoration-style:solid;text-decoration-color:#000;text-transform:none;block-progression:tb;isolation:auto;mix-blend-mode:normal" d="M 4.9902344 3.9902344 A 1.0001 1.0001 0 0 0 4.2929688 5.7070312 L 10.585938 12 L 4.2929688 18.292969 A 1.0001 1.0001 0 1 0 5.7070312 19.707031 L 12 13.414062 L 18.292969 19.707031 A 1.0001 1.0001 0 1 0 19.707031 18.292969 L 13.414062 12 L 19.707031 5.7070312 A 1.0001 1.0001 0 0 0 18.980469 3.9902344 A 1.0001 1.0001 0 0 0 18.292969 4.2929688 L 12 10.585938 L 5.7070312 4.2929688 A 1.0001 1.0001 0 0 0 4.9902344 3.9902344 z"></path></svg></a></div><!-- #sticky toc -->' : '';

get_header();

echo '<div class="betterdocs-single-wraper betterdocs-single-bg betterdocs-single-layout-6">';
    echo $live_search_markup;
    echo '<div class="betterdocs-full-width">';
        echo '<div class="betterdocs-content-area">';
            if( $enable_sidebar ) {
                echo '<aside id="betterdocs-sidebar">';
                    echo '<div class="betterdocs-sidebar-content betterdocs-category-sidebar">';
                        echo $betterdocs_sidebar;
                        echo $stick_toc_markup;
                    echo '</div>';
                echo '</aside>';
            }
            echo '<div id="betterdocs-single-main" class="docs-single-main">';
                if( have_posts() ) {
                    while( have_posts() ) {
                        the_post();
                        echo '<header class="betterdocs-entry-header">';
                            echo '<div class="docs-single-title">';
                                    echo $betterdocs_title_before.''.wp_kses(get_the_title(), BETTERDOCS_PRO_KSES_ALLOWED_HTML).''.$betterdocs_title_after;
                                    betterdocs_breadcrumbs();
                            echo '</div>';
                        echo '</header><!-- .entry-header -->';

                        echo '<div class="betterdocs-entry-content">';
                            echo $print_icon_markup;
                            echo $toc_markup;
                            $content = apply_filters('the_content', get_the_content());
                            echo BetterDocs_Public::betterdocs_the_content( $content, BetterDocs_Public::htag_support(), $enable_toc );
                        echo '</div><!-- .entry-content -->';

                        echo '<div class="betterdocs-entry-footer">';
                                echo $post_tags_markup;
                                do_action( 'betterdocs_docs_before_social' );
                                if ( $post_social_share == true ) {
                                    $social_sharing_text = get_theme_mod('betterdocs_social_sharing_text', esc_html__('Share This Article :','betterdocs-pro'));
                                    $facebook_sharing    = get_theme_mod('betterdocs_post_social_share_facebook', true);
                                    $twitter_sharing     = get_theme_mod('betterdocs_post_social_share_twitter', true);
                                    $linkedin_sharing    = get_theme_mod('betterdocs_post_social_share_linkedin', true);
                                    $pinterest_sharing   = get_theme_mod('betterdocs_post_social_share_pinterest', true);
                                    echo do_shortcode(
                                        "[betterdocs_social_share
                                        title='{$social_sharing_text}'
                                        facebook_sharing='{$facebook_sharing}'
                                        twitter_sharing='{$twitter_sharing}'
                                        linkedin_sharing='{$linkedin_sharing}'
                                        pinterest_sharing='{$pinterest_sharing}']" 
                                    );
                                }
                                require_once BETTERDOCS_DIR_PATH . '/public/partials/template-single/feedback-form.php';
                            echo '</div><!-- .entry-footer -->';
                    }

                    if( $enable_navigation ){
                        $nav  = get_previous_post_link( '%link', '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="42px" viewBox="0 0 50 50" version="1.1"><g id="surface1"><path style=" " d="M 11.957031 13.988281 C 11.699219 14.003906 11.457031 14.117188 11.28125 14.308594 L 1.015625 25 L 11.28125 35.691406 C 11.527344 35.953125 11.894531 36.0625 12.242188 35.976563 C 12.589844 35.890625 12.867188 35.625 12.964844 35.28125 C 13.066406 34.933594 12.972656 34.5625 12.71875 34.308594 L 4.746094 26 L 48 26 C 48.359375 26.003906 48.695313 25.816406 48.878906 25.503906 C 49.058594 25.191406 49.058594 24.808594 48.878906 24.496094 C 48.695313 24.183594 48.359375 23.996094 48 24 L 4.746094 24 L 12.71875 15.691406 C 13.011719 15.398438 13.09375 14.957031 12.921875 14.582031 C 12.753906 14.203125 12.371094 13.96875 11.957031 13.988281 Z "></path></g></svg> %title', TRUE, ' ', 'doc_category' ) . get_next_post_link( '%link', '%title <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="42px" viewBox="0 0 50 50" version="1.1"><g id="surface1"><path style=" " d="M 38.035156 13.988281 C 37.628906 13.980469 37.257813 14.222656 37.09375 14.59375 C 36.933594 14.96875 37.015625 15.402344 37.300781 15.691406 L 45.277344 24 L 2.023438 24 C 1.664063 23.996094 1.328125 24.183594 1.148438 24.496094 C 0.964844 24.808594 0.964844 25.191406 1.148438 25.503906 C 1.328125 25.816406 1.664063 26.003906 2.023438 26 L 45.277344 26 L 37.300781 34.308594 C 36.917969 34.707031 36.933594 35.339844 37.332031 35.722656 C 37.730469 36.105469 38.363281 36.09375 38.746094 35.691406 L 49.011719 25 L 38.746094 14.308594 C 38.5625 14.109375 38.304688 13.996094 38.035156 13.988281 Z "></path></g></svg>', TRUE, ' ', 'doc_category' );
                        echo '<div class="docs-navigation">';
                        echo  apply_filters( 'betterdocs_single_post_nav', $nav );
                        echo '</div>';
                    }

                    echo $credit_markup;

                    if( $enable_comment && ( comments_open() || get_comments_number() ) ) {
                        comments_template();
                    }

                }
            echo '</div>';
        echo '</div>';
    echo '</div>';
echo '</div>';

get_footer();