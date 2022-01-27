<?php

if (is_admin()) {
    return false;
}

/**
 * Custom template parts for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package agro
*/



/*************************************************
##  FOOTER
*************************************************/
/**
* default theme footer Area
* @param void
* @return html
*/

if (! function_exists('agro_footer')) {
    function agro_footer()
    {
        $footer_mb = rwmb_meta('agro_page_footer_onoff');
        $footer_mb = '' == $footer_mb ? '1' : $footer_mb;

        $footer = is_page() && '0' != agro_settings('footer_onoff') ? $footer_mb : agro_settings('footer_onoff', '1');

        if ('0' != $footer) {
            echo '<footer id="footer" class="footer--style-1">
            <div class="container">';

            if (! empty(agro_settings('custom_widgets'))) {
                echo '<div class="row mb-40">';
                $sidebars = agro_settings('custom_widgets');
                foreach ($sidebars as $id => $column) {
                    $id = 'custom-footer-widget-'.($id + 1);
                    if (is_active_sidebar($id)) {
                        echo '<div class="nt-footer-widget '.$column.' '.$id.'">';
                        dynamic_sidebar($id);
                        echo '</div>';
                    }
                }
                echo '</div>';
                // end row
            }

            $reverse_footer = ('1' == agro_settings('footer_nav_onoff')) ? ' flex-lg-row-reverse' : '';
            echo '<div class="row'.$reverse_footer.'">';

            if ('1' == agro_settings('footer_nav_onoff')) {
                echo '<div class="col-12 col-lg-6">
                    <div class="footer__item nt-footer-nav">
                        <nav id="footer__navigation" class="navigation text-lg-right">
                            <ul>';
                                wp_nav_menu(
                                    array(
                                        'menu' => '',
                                        'theme_location' => 'footer_menu_1',
                                        'container' => '', // menu wrapper element
                                        'container_class' => '',
                                        'container_id' => '', // default: none
                                        'menu_class' => '', // ul class
                                        'menu_id' => '', // ul id
                                        'items_wrap' => '%3$s',
                                        'before' => '', // before <a>
                                        'after' => '', // after <a>
                                        'link_before' => '', // inside <a>, before text
                                        'link_after' => '', // inside <a>, after text
                                        'depth' => 1, // '0' to display all depths
                                        'echo' => true,
                                        'fallback_cb' => 'Agro_Wp_Bootstrap_Navwalker::fallback',
                                        'walker' => new Agro_Wp_Bootstrap_Navwalker()
                                    )
                                );
                            echo '</ul>
                        </nav>
                    </div>
                </div>';
            }
            echo '<div class="col-12 col-lg-6">
                <div class="footer__item nt-footer-copyright">';
                    if ('' != agro_settings('footer_copyright')) {
                        echo '<span class="__copy">'. wp_kses(agro_settings('footer_copyright'), agro_allowed_html()).'</span>';
                    } else {
                        echo'<span class="__copy">'. sprintf( esc_html__('Copyright - All Rights Reserved by %s ', 'agro'), '<a href="https://ninetheme.com">'.esc_html__('Ninetheme','agro') ).'</span>';
                    }
                echo '</div>';
            echo '</div>';
            // end row
            echo '</div>
            </div>
        </footer>';
        }
    }
}
add_action('agro_footer_action', 'agro_footer', 10);

/*************************************************
##  FOOTER CONTACT FORM 7 SHORTCODE
*************************************************/
/**
* default theme footer Area
* @param void
* @return html
*/
if (! function_exists('agro_footer_form')) {
    function agro_footer_form()
    {
        $footer_form_mb = rwmb_meta('agro_page_footer_form_onoff');
        $footer_form_bg_overlay = agro_settings('form_bottom_bg_overlay');
        $footer_form_bg_overlay = $footer_form_bg_overlay != '' ? ' has-overlay-color' : '';
        $footer_form = is_page() && agro_settings('footer_form_onoff') == '1' ? $footer_form_mb : agro_settings('footer_form_onoff', '0');
        if ('1' == $footer_form && '' != agro_settings('footer_form_shortcode')) {
            echo '<div class="section contact-form-area section--dark-bg'.esc_attr($footer_form_bg_overlay).'">
            <div class="container">';
            if ('' != agro_settings('footer_form_heading') || '' != agro_settings('footer_form_desc')) {
                echo '<div class="section-heading section-heading--center section-heading--white" data-aos="fade">';
                if ('' != agro_settings('footer_form_heading')) {
                    echo '<h2 class="__title">'. wp_kses(agro_settings('footer_form_heading'), agro_allowed_html()).'</h2>';
                }
                echo wp_kses(agro_settings('footer_form_desc'), agro_allowed_html());
                echo '</div>';
            }

            echo do_shortcode(agro_settings('footer_form_shortcode'));

            echo '</div>
            </div>';
        }
    }
}

/*************************************************
##  FOOTER CONTACT FORM 7 SHORTCODE
*************************************************/
/**
* default theme footer Area
* @param void
* @return html
*/
if (! function_exists('agro_footer_map')) {
    function agro_footer_map()
    {
        $footer_map_mb = rwmb_meta('agro_page_footer_map_onoff');
        $footer_map = is_page() && agro_settings('footer_map_onoff') ? $footer_map_mb : agro_settings('footer_map_onoff', '0');
        if ('1' == $footer_map && '' != agro_settings('footer_map_apikey') && '' != agro_settings('footer_map_longitude') && '' != agro_settings('footer_map_latitude')) {
            $mapmarkerimg =  ! empty(agro_settings('footer_map_marker')) ? agro_settings('footer_map_marker')['url'] : get_theme_file_uri().'/images/marker.png';
            $mapminheight =  ! empty(agro_settings('footer_map_minheight')) ? agro_settings('footer_map_minheight') : '255';
            echo '<div class="section section--no-pt section--no-pb">
            <div class="g_map" data-api-key="'.esc_attr(agro_settings('footer_map_apikey')).'" data-longitude="'.esc_attr(agro_settings('footer_map_longitude')).'" data-latitude="'.esc_attr(agro_settings('footer_map_latitude')).'" data-marker="'.esc_url($mapmarkerimg).'" style="min-height: '.esc_attr($mapminheight).'px"></div>
        </div>';
        }
    }
}
