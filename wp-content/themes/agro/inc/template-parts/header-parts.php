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
## HEADER MENU EXTRA BUTTON
*************************************************/

function agro_add_extrabtn_to_nav($items, $args)
{

    $p_n_btn = is_page() && agro_settings('nav_btn_onoff') == '1' ? rwmb_meta('agro_page_nav_btn_onoff') : agro_settings('nav_btn_onoff', '0');
    $navstyle = is_page() ? rwmb_meta('agro_page_header_style') : agro_settings('header_style', '1');

    if ($navstyle == '2') {
        $btnstyle = 'custom-btn--style-2';
    } elseif ($navstyle == '3') {
        $btnstyle = 'custom-btn--style-5';
    } else {
        $btnstyle = 'custom-btn--style-4';
    }
    if(agro_settings('search_header_popup_display') =='1'){
        $items .= '<li class="menu-item-last-child li-btn search-btn"><a class="header_menu_link header_search_open" href="#">';
            $items .= '<i class="fontello-search icon is-search"></i>';
        $items .= '</a></li>';
    }
    if (('1' == $p_n_btn) && ('' != agro_settings('nav_btn_title'))) {
        $target_btn = agro_settings('nav_btn_target');
        $target_btn = $target_btn ? ' target="'.esc_attr($target_btn).'"' : '';
        $items .= '<li class="menu-item-last-child li-btn"><a href="'.esc_url(agro_settings('nav_btn_url')).'"'.$target_btn.' class="custom-btn custom-btn--small '.$btnstyle.'">'.esc_html(agro_settings('nav_btn_title')).'</a></li>';

    }

    $items .= '';

    return $items;

}
add_filter('wp_nav_menu_items', 'agro_add_extrabtn_to_nav', 10, 2);


/*************************************************
##  LOGO
*************************************************/


if (! function_exists('agro_logo')) {
    function agro_logo()
    {
        if ('0' != agro_settings('logo_onoff', '1')) {

            $logotype = agro_settings('logo_type');
            $stickylogo = 'img' == agro_settings('logo_type')  && '' != agro_settings('img_logo') && '' != agro_settings('img_logo2') ? ' has-sticky-logo' : '';

            echo '<a href="' . esc_url(home_url('/')) . '" id="nt-logo" class="top-bar__logo site-logo '.esc_attr($logotype.$stickylogo).' ">';


            if ('img' == agro_settings('logo_type')  && '' != agro_settings('img_logo')) {
                // image logo
                echo '<img src="'.esc_url(agro_settings('img_logo')['url']).'" alt="'.esc_attr(get_bloginfo('name')).'"  class="img-fluid main-logo" />';
                // image logo 2 for sticky menu
                if ( !empty( agro_settings('img_logo2')['url'] ) ) {
                    echo '<img src="'.esc_url(agro_settings('img_logo2')['url']).'" alt="'.esc_attr(get_bloginfo('name')).'"  class="img-fluid sticky-logo" />';
                }
                if ( !empty( agro_settings('img_mobile_logo')['url'] ) ) {
                    echo '<img src="'.esc_url(agro_settings('img_mobile_logo')['url']).'" alt="'.esc_attr(get_bloginfo('name')).'"  class="mobile-logo img-fluid" />';
                }
                if ( !empty( agro_settings('img_smobile_logo')['url'] ) ) {
                    echo '<img src="'.esc_url(agro_settings('img_smobile_logo')['url']).'" alt="'.esc_attr(get_bloginfo('name')).'"  class="sticky-mobile-logo img-fluid" />';
                }
            } elseif ('sitename' == agro_settings('logo_type')) {

                // get bloginfo name
                echo esc_html(get_bloginfo('name'));
            } elseif ('customtext' == agro_settings('logo_type')) {

                // custom text logo
                echo esc_html(agro_settings('text_logo'));
            } else {
                $default_logo = get_theme_file_uri().'/images/default-logo.png';

                // default image logo
                echo '<img src="'.esc_url($default_logo).'" alt="'.esc_attr(get_bloginfo('name')).'"  class="img-fluid default-logo" />';
            }

            echo '</a>';
        }
    }
}


/*************************************************
##  HEADER NAVIGATION
*************************************************/

if (! function_exists('agro_header')) {
    function agro_header()
    {

        $headerstyle_mb = rwmb_meta('agro_page_header_style');
        $header_onoff_mb = rwmb_meta('agro_page_header_onoff');
        $header_onoff_mb = '' == $header_onoff_mb ? '1' : $header_onoff_mb;
        $header_onoff = is_page() && '0' != agro_settings('header_onoff') ? $header_onoff_mb : agro_settings('header_onoff', '1');
        $headerstyle = is_page() && !empty($headerstyle_mb) ? $headerstyle_mb : agro_settings('header_style', '1');

        $container = ($headerstyle == '1') ? 'container-fluid' : 'container position-relative' ;
        if ($headerstyle == '2') {
            $row = 'justify-content-between no-gutters';
        } elseif ($headerstyle == '3') {
            $row = 'align-items-center no-gutters';
        } else {
            $row = 'align-items-center justify-content-between no-gutters';
        }

        $color = $headerstyle == '2' ? 'dark' : 'light';
        $align = $headerstyle == '2' ? ' text-lg-right' : '';

        $sticky_nav = '0' != agro_settings('sticky_topbar_onoff', '0') ? ' topbar-fixed' : '';

        if ('0' != $header_onoff) {
            echo'<header id="top-bar" class="top-bar top-bar--style-'.esc_attr($headerstyle.$sticky_nav).'" data-ntr-header>';
            echo agro_header_search_form_popup();
            echo'<div class="top-bar__bg"></div>

                <div class="'.esc_attr($container).'">
                    <div class="row '.esc_attr($row).'">';

                        // theme logo
                        agro_logo();

                        echo'<a id="top-bar__navigation-toggler" class="top-bar__navigation-toggler top-bar__navigation-toggler--'.esc_attr($color).'" href="javascript:void(0);"><span></span></a>
                        <div id="top-bar__inner" class="top-bar__inner'.esc_attr($align).'">
                        <div>';
                            if ($headerstyle == '2') {
                                echo'<div class="d-lg-flex flex-lg-column-reverse align-items-lg-end">';
                            }

                            echo'<nav id="top-bar__navigation" class="top-bar__navigation navigation" role="navigation">
                                <ul>';
                                // default wp menu
                                wp_nav_menu(
                                    array(
                                        'menu' => '',
                                        'theme_location' => 'header_menu_1',
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
                                        'depth' => 3, // '0' to display all depths
                                        'echo' => true,
                                        'fallback_cb' => 'Agro_Wp_Bootstrap_Navwalker::fallback',
                                        'walker' => new Agro_Wp_Bootstrap_Navwalker()
                                    )
                                );
                                echo'</ul>
                            </nav>';

                            if ($headerstyle == '2') {
                                agro_header_topbar();
                                echo'</div>';
                            }

                            echo'</div>
                        </div>
                    </div>
                </div>
            </header>';
        }
    }
}

add_action('agro_header_action', 'agro_header', 10);

if (! function_exists('agro_header_topbar')) {
    function agro_header_topbar()
    {
        $topbar_onoff = is_page() && '0' != agro_settings('topbar_onoff') ? rwmb_meta('agro_page_topbar_onoff') : agro_settings('topbar_onoff', '0');
        if ('1' == $topbar_onoff) {
            echo'<div class="top-bar__contacts">';
            if ('' != agro_settings('topbar_address')) {
                echo'<span>'.agro_settings('topbar_address').'</span>';
            }
            if ('' != agro_settings('topbar_phone')) {
                echo'<span>'.agro_settings('topbar_phone').'</span>';
            }
            if ('' != agro_settings('topbar_mail')) {
                echo'<span>'.agro_settings('topbar_mail').'</span>';
            }
            if ('' != agro_settings('topbar_socials')) {
                echo'<div class="social-btns">'.agro_settings('topbar_socials').'</div>';
            }
            echo'</div>';
        }
    }
}
