<?php

/*******************************/
/* site menu
/******************************/
if (!function_exists('agro_vc_nav')) {
    function agro_vc_nav($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        "mstyle" => '1',
        "mtype" => '',
        "stickynav" => '',
        "menu" => '',
        "link" => '',
        "submenucheck" => '',
        "submenu" => '',
        "sublink" => '',
        "btnlink" => '',
        // slide menu css
        "mbg" => '',
        "mobbgimg" => '',
        "miclr" => '',
        "mihvr" => '',
        "miline" => '',
        // bg css
        "css" => '',
        ), $atts, 'agro_nav');

        // menu style
        $mobgimg = wp_get_attachment_url($atts['mobbgimg'], 'full');
        $menu_css = array();
        $menu_css[] = $atts['mbg'] != '' ? 'header.nt-header-shortcode-menu.top-bar { background:'. esc_attr($atts['mbg']) .'; }' : '';
        $menu_css[]= $mobgimg != '' ? 'header.nt-header-shortcode-menu top-bar__bg{ background-image: url( '. esc_url($mobgimg) .' ); background-repeat: no-repeat;
	background-position: left bottom;background-color: '.esc_attr($atts['mbg']).'; }' : '';
        $menu_css[] = $atts['miclr'] != '' ? 'header.nt-header-shortcode-menu .navigation li a:not(.custom-btn) { color:'. esc_attr($atts['miclr']) .'; }.top-bar__navigation li.has-submenu:before{border-color:'. esc_attr($atts['miclr']) .';}' : '';
        $menu_css[] = $atts['mihvr'] != '' ? 'header.nt-header-shortcode-menu .navigation li a:not(.custom-btn):hover { color:'. esc_attr($atts['mihvr']) .'; }' : '';
        $menu_css[] = $atts['miline'] != '' ? 'header.nt-header-shortcode-menu .navigation li a:not(.custom-btn):after { background:'. esc_attr($atts['miline']) .'; }' : '';
        $menu_css = ! empty($menu_css) ? ' data-res-css="'. implode(' ', $menu_css) .'"'  : '';

        $out = '';

        $headerstyle = $atts['mstyle'] ? $atts['mstyle'] : '1';
        $onepagemenu = $atts['mtype'] == 'custom' ? ' onepage-menu' : '';
        $stickynav = $atts['stickynav'] == 'yes' ? ' topbar-fixed' : '';

        $container = ($headerstyle == '1') ? 'container-fluid' : 'container position-relative' ;
        if ($headerstyle == '3') {
            $row = 'align-items-center no-gutters';
        } else {
            $row = 'align-items-center justify-content-between no-gutters';
        }

        $out .= '<header id="top-bar" class="nt-header-shortcode-menu top-bar top-bar--style-'.esc_attr($headerstyle).$stickynav.$onepagemenu.'"'.$menu_css.'>';
        $out .= '<div class="top-bar__bg"></div>';

        $out .= '<div class="'.esc_attr($container).'">';
        $out .= '<div class="row '.esc_attr($row).'">';

        // theme logo
        ob_start();
        agro_logo();
        $out .= '<div class="logo">'.ob_get_clean().'</div>';

        $out .= '<a id="top-bar__navigation-toggler" class="top-bar__navigation-toggler top-bar__navigation-toggler--light" href="javascript:void(0);"><span></span></a>';

        $out .= '<div id="top-bar__inner" class="top-bar__inner">';
        $out .= '<div>';
        $out .= '<nav id="top-bar__navigation" class="top-bar__navigation navigation" role="navigation">';

        if ($atts['mtype'] == 'custom') {
            $out .= '<ul>';

            $loop = (array) vc_param_group_parse_atts($atts['menu']);
            foreach ($loop as $item) {
                $link = !empty($item['link']) ? $item['link'] : '';
                $link = vc_build_link($link);
                $atitle = $link['title'];
                $href = $link['url'] != '' ? ' href="'. esc_url($link['url']) .'"' : ' href="#"';
                $target = $link['target'] != '' ? ' target="'. esc_attr($link['target']) .'"' : '';

                if (isset($item['submenucheck']) == 'yes') {
                    $out .= '<li class="has-submenu">';
                    $out .= '<a href="javascript:void(0);">'.$item['parenttitle'].'</a>';
                    $out .= '<ul class="submenu">';
                    $subloop = (array) vc_param_group_parse_atts($item['submenu']);
                    foreach ($subloop as $subitem) {
                        $sublink = !empty($subitem['sublink']) ? $subitem['sublink'] : '';
                        $sublink = vc_build_link($sublink);
                        $subtitle = $sublink['title'];
                        $subhref = $sublink['url'] != '' ? ' href="'. esc_url($sublink['url']) .'"' : ' href="#"';
                        $subtarget = $sublink['target'] != '' ? ' target="'. esc_attr($sublink['target']) .'"' : '';
                        $out .= '<li><a'.$subhref.'>'.$subtitle.'</a></li>';
                    }
                    $out .= '</ul>';
                    $out .= '</li>';
                } else {
                    $out .= '<li><a'.$href.'>'.$atitle.'</a></li>';
                }
            }

            if ($atts['mtype'] == 'custom') {
                if ($headerstyle == '3') {
                    $btnstyle = 'custom-btn--style-5';
                } else {
                    $btnstyle = 'custom-btn--style-4';
                }

                $btnlink = !empty($atts['btnlink']) ? $atts['btnlink'] : '';
                $btnlink = vc_build_link($btnlink);
                $btntitle = $btnlink['title'];
                $btnhref = $btnlink['url'] != '' ? ' href="'. esc_url($btnlink['url']) .'"' : ' href="#"';
                $btntarget = $btnlink['target'] != '' ? ' target="'. esc_attr($btnlink['target']) .'"' : '';

                if ($btntitle) {
                    $out .= '<li class="menu-item-last-child li-btn">';
                    $out .= '<a class="custom-btn custom-btn--small custom-btn--style-'.$btnstyle.'"'.$btnhref.$btntarget.'>'.esc_html($btntitle).'</a>';
                    $out .= '</li>';
                }
            }

            $out .= '</ul>';
        } else {
            ob_start();

            // default wp menu
            wp_nav_menu(
                array(
                      'menu' => 'header_menu_1',
                      'theme_location' => 'header_menu_1',
                      'container' => '', // menu wrapper element
                      'container_class' => '',
                      'container_id' => '',  // default: none
                      'menu_class' => '', // ul class
                      'menu_id' => '', // ul id
                      'items_wrap' => '%3$s',
                      'before' => '', // before <a>
                      'after' => '', // after <a>
                      'link_before' => '', // inside <a>, before text
                      'link_after' => '', // inside <a>, after text
                      'depth' => 2, // '0' to display all depths
                      'echo' => true,
                      'fallback_cb' => 'Agro_Wp_Bootstrap_Navwalker::fallback',
                      'walker' => new Agro_Wp_Bootstrap_Navwalker()
                )
            );

            $out .= '<ul>'. ob_get_clean().'</ul>';
        }
        $out .= '</nav>';

        $out .= '</div>';
        $out .= '</div>';

        $out .= '</div>';
        $out .= '</div>';
        $out .= '</header>';

        return $out;
    }
    add_shortcode('agro_nav', 'agro_vc_nav');
}


/*******************************/
/* Page Breadcrumbs
/******************************/
if (!function_exists('agro_vc_page_bread')) {
    function agro_vc_page_bread($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        // data
        'text' => '',
        'title' => '',
        'subtitle' => '',
        'desc' => '',
        'bread' => '',
        'line' => '',
        'align' => '',
        'stclr' => '',
        'tclr' => '',
        'descclr' => '',
        'breadclr' => '',
        'breadactvclr' => '',
        'lineclr' => '',
        'wrapperlineheight' => '',
        // background css
        'css' => '',
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
        ), $atts, 'agro_page_bread');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $uniq = 'section_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h1';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.nt-shortcode-hero.'.$uniq.' .__title {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.nt-shortcode-hero.'.$uniq.' .__title {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.nt-shortcode-hero.'.$uniq.' .__title {font-size:'. $tsize .';}' : '';

        $item_css[] = $atts['stclr'] ? '.nt-shortcode-hero.'.$uniq.' .__title span {color: '. esc_attr($atts['stclr']) .'; }' : '';
        $item_css[] = $atts['tclr'] ? '#hero.nt-shortcode-hero.'.$uniq.' .__title {color: '. esc_attr($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['descclr'] ? '.nt-shortcode-hero.'.$uniq.' .nt-hero-desc {color: '. esc_attr($atts['descclr']) .'; }' : '';
        $item_css[] = $atts['breadclr'] ? '.nt-shortcode-hero.'.$uniq.' .breadcrumbs__link,.nt-shortcode-hero.'.$uniq.' .bitem {color: '. esc_attr($atts['breadclr']) .'; }' : '';
        $item_css[] = $atts['breadactvclr'] ? '.nt-shortcode-hero.'.$uniq.' li.active, .nt-shortcode-hero.'.$uniq.' .breadcrumbs__link:hover {color: '. esc_attr($atts['breadactvclr']) .'; }' : '';
        $item_css[] = $atts['lineclr'] ? '.nt-shortcode-hero.'.$uniq.' span.page-scroll-discover:before, .nt-shortcode-hero.'.$uniq.' span.page-scroll-discover:after {background-color: '. esc_attr($atts['lineclr']) .'; }' : '';
        $item_css[] = $atts['wrapperlineheight'] ? '.nt-shortcode-hero.'.$uniq.' span.page-scroll-discover{height: '. esc_attr($atts['wrapperlineheight']) .'; }' : '';
        $item_css[] = $atts['wrapperlineheight'] ? '.nt-shortcode-hero.'.$uniq.' span.page-scroll-discover{bottom: calc(-'. esc_attr($atts['wrapperlineheight']) .' + 35px ); }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';

        $out .= '<div id="hero" class="nt-shortcode-hero jarallax'.$uniq.$bgcss.'" data-speed="0.7" data-img-position="50% 80%"'.$item_css.'>';
        $out .= '<div class="container">';
        $out .= '<div class="row">';
        $out .= '<div class="col-12 col-lg-7 '. esc_attr($atts['align']) .'">';

        if ($atts['text'] == 'custom') {
            $out .= '<'.$htag.' class="__title"><span>'. esc_html($atts['title']) .'</span> '. esc_html($atts['subtitle']) .'</'.$htag.'>';
        } else {
            $out .= '<'.$htag.' class="__title"><span>'.get_the_title().'</span> '. esc_html($atts['subtitle']) .'</'.$htag.'>';
        }
        if ($atts['desc'] != '') {
            $out .= '<p class="nt-hero-desc">'. esc_html($atts['desc']) .'</p>';
        }

        if ($atts['bread'] != 'hide') {
            ob_start();
            agro_breadcrumbs();
            $out .= '<div class="shortcode-page-breadcrumbs breadcrumb">'. ob_get_clean().'</div>';
        }
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        if ($atts['line'] == 'show') {
            $out .= '<span class="page-scroll-discover"></span>';
        }
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_page_bread', 'agro_vc_page_bread');
}



/*******************************/
/* about 1
/******************************/
if (!function_exists('agro_vc_about1')) {
    function agro_vc_about1($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        // data
        'title' => '',
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
        'thintitle' => '',
        'link' => '',
        'hidebg992' => '',
        // custom css
        'hmb' => '',
        'tclr' => '',
        'thclr' => '',
        'descclr' => '',
        'btnclr' => '',
        'btnhvrclr' => '',
        'btnbrd' => '',
        'btnhvrbrd' => '',
        'btnbg' => '',
        'btnhvrbg' => '',
        'btntype' => '',
        'btnstyle' => '',
        'btnsize' => '',
        // background css
        'css' => '',
        ), $atts, 'agro_about1');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $uniq = 'item_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.section.'.$uniq.' .section-heading .__title span{'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.section.'.$uniq.' .section-heading .__title {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.section.'.$uniq.' .section-heading .__title {font-size:'. $tsize .';}' : '';

        $btntype = $atts['btntype'] ? ' '.$atts['btntype'] : '';
        $btnstyle = $atts['btnstyle'] ? ' '.$atts['btnstyle'] : ' custom-btn--style-1';
        $btnsize = $atts['btnsize'] ? ' '.$atts['btnsize'] : ' custom-btn--medium';

        $item_css[] = $atts['hmb'] ? '.section.'.$uniq.' .section-heading {margin-bottom: '. esc_html($atts['hmb']) .'px; }' : '';
        $item_css[] = $atts['tclr'] ? '.section.'.$uniq.' .section-heading .__title {color: '. esc_html($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['thclr'] ? '.section.'.$uniq.' .section-heading .__title span{color: '. esc_attr($atts['thclr']) .'; }' : '';
        $item_css[] = $atts['descclr'] ? '.section.'.$uniq.' p{color: '. esc_attr($atts['descclr']) .'; }' : '';

        $item_css[] = $atts['btnclr'] ? '.section.'.$uniq.' .custom-btn {color: '. esc_attr($atts['btnclr']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrclr'] ? '.section.'.$uniq.' .custom-btn:hover {color: '. esc_attr($atts['btnhvrclr']) .'!important; }' : '';
        $item_css[] = $atts['btnbrd'] ? '.section.'.$uniq.' .custom-btn {border-color: '. esc_attr($atts['btnbrd']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrbrd'] ? '.section.'.$uniq.' .custom-btn:hover {border-color: '. esc_attr($atts['btnhvrbrd']) .'!important; }' : '';
        $item_css[] = $atts['btnbg'] ? '.section.'.$uniq.' .custom-btn {background-color: '. esc_attr($atts['btnbg']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrbg'] ? '.section.'.$uniq.' .custom-btn:hover {background-color: '. esc_attr($atts['btnhvrbg']) .'!important; }' : '';

        $item_css[] = $atts['btntype'] == 'btn-rounded' ? '.section.'.$uniq.' .custom-btn.btn-rounded {border-radius: 10px; }' : '';
        $item_css[] = $atts['btntype'] == 'btn-square' ? '.section.'.$uniq.' .custom-btn.btn-square {border-radius: 0px !important; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';
        $hidebg992 = $atts['hidebg992'] == 'hide' ? ' md-hide-bg' : '';
        $out .= '<div class="section section--no-pb about--1'.$hidebg992.$uniq.$bgcss.'"'.$item_css.'>';
        $out .= '<div class="container">';
        if ($atts['title'] || $atts['thintitle']) {
            $out .= '<div class="section-heading">';
            $out .= '<'.$htag.' class="__title">'.$atts['title'].' <span>'.$atts['thintitle'].'</span></'.$htag.'>';
            $out .= '</div>';
        }

        $out .= '<div class="row">';
        $out .= '<div class="col-12 col-lg-6 col-xl-8">';
        // custom content area
        $out .= do_shortcode($content);

        // section button
        $link = !empty($atts['link']) ? $atts['link'] : '';
        $link = vc_build_link($link);
        $atitle = $link['title'];
        $href = $link['url'] != '' ? ' href="'. esc_url($link['url']) .'"' : ' href="#"';
        $target = $link['target'] != '' ? ' target="'. esc_attr($link['target']) .'"' : '';

        if ($atitle != '') {
            $out .= '<p><a class="custom-btn'.$btnsize.$btnstyle.$btntype.'"'. $href . $target .'>'. esc_html($atitle) .'</a></p>';
        }
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_about1', 'agro_vc_about1');
}

/*******************************/
/* info 1
/******************************/
if (!function_exists('agro_vc_info1')) {
    function agro_vc_info1($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        // data
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
        'title' => '',
        'thintitle' => '',
        'link' => '',
        'img' => '',
        'imgw' => '',
        'imgh' => '',
        // custom css
        'tclr' => '',
        'thclr' => '',
        'descclr' => '',
        'btnclr' => '',
        'btnhvrclr' => '',
        'btnbrd' => '',
        'btnhvrbrd' => '',
        'btnbg' => '',
        'btnhvrbg' => '',
        'btntype' => '',
        'btnstyle' => '',
        'btnsize' => '',
        // background css
        'css' => '',
        ), $atts, 'agro_info1');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $uniq = 'item_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.section.'.$uniq.' .info-heading {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.section.'.$uniq.' .info-heading {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.section.'.$uniq.' .info-heading {font-size:'. $tsize .';}' : '';

        $btntype = $atts['btntype'] ? ' '.$atts['btntype'] : '';
        $btnstyle = $atts['btnstyle'] ? ' '.$atts['btnstyle'] : ' custom-btn--style-1';
        $btnsize = $atts['btnsize'] ? ' '.$atts['btnsize'] : ' custom-btn--medium';

        $item_css[] = $atts['tclr'] ? '.section.'.$uniq.' .info-heading {color: '. esc_html($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['thclr'] ? '.section.'.$uniq.' .info-heading span{color: '. esc_attr($atts['thclr']) .'; }' : '';
        $item_css[] = $atts['descclr'] ? '.section.'.$uniq.' p{color: '. esc_attr($atts['descclr']) .'; }' : '';

        $item_css[] = $atts['btnclr'] ? '.section.'.$uniq.' .custom-btn {color: '. esc_attr($atts['btnclr']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrclr'] ? '.section.'.$uniq.' .custom-btn:hover {color: '. esc_attr($atts['btnhvrclr']) .'!important; }' : '';
        $item_css[] = $atts['btnbrd'] ? '.section.'.$uniq.' .custom-btn {border-color: '. esc_attr($atts['btnbrd']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrbrd'] ? '.section.'.$uniq.' .custom-btn:hover {border-color: '. esc_attr($atts['btnhvrbrd']) .'!important; }' : '';
        $item_css[] = $atts['btnbg'] ? '.section.'.$uniq.' .custom-btn {background-color: '. esc_attr($atts['btnbg']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrbg'] ? '.section.'.$uniq.' .custom-btn:hover {background-color: '. esc_attr($atts['btnhvrbg']) .'!important; }' : '';

        $item_css[] = $atts['btntype'] == 'btn-rounded' ? '.section.'.$uniq.' .custom-btn.btn-rounded {border-radius: 10px; }' : '';
        $item_css[] = $atts['btntype'] == 'btn-square' ? '.section.'.$uniq.' .custom-btn.btn-square {border-radius: 0px !important; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';
        $out .= '<div class="section'.$uniq.$bgcss.'"'.$item_css.'>';
        $out .= '<div class="container">';
        $out .= '<div class="row">';
        $out .= '<div class="col-12 col-lg-6">';
        if ($atts['title'] || $atts['thintitle']) {
            if ($atts['title']) {
                $out .= '<'.$htag.' class="info-heading">'.$atts['title'].' <span>'.$atts['thintitle'].'</span></'.$htag.'>';
            }
        }
        $out .= do_shortcode($content);

        // section button
        $link = !empty($atts['link']) ? $atts['link'] : '';
        $link = vc_build_link($link);
        $atitle = $link['title'];
        $href = $link['url'] != '' ? ' href="'. esc_url($link['url']) .'"' : ' href="#"';
        $target = $link['target'] != '' ? ' target="'. esc_attr($link['target']) .'"' : '';

        if ($atitle != '') {
            $out .= '<p><a class="custom-btn'.$btnsize.$btnstyle.$btntype.'"'. $href . $target .'>'. esc_html($atitle) .'</a></p>';
        }
        $out .= '</div>';

        $out .= '<div class="col-12 my-3 d-lg-none"></div>';

        $out .= '<div class="col-12 col-lg-6">';
        $blankimg = '';
        $out .= agro_img($atts['img'], $blankimg, $imgclass='img-fluid', $atts['imgw'], $atts['imgh']);
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_info1', 'agro_vc_info1');
}

/*******************************/
/* info 2
/******************************/
if (!function_exists('agro_vc_info2')) {
    function agro_vc_info2($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        // data
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
        'title' => '',
        'thintitle' => '',
        'img' => '',
        'imgw' => '',
        'imgh' => '',
        // custom css
        'tclr' => '',
        'thclr' => '',
        'descclr' => '',
        // background css
        'css' => '',
    ), $atts, 'agro_info2');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $uniq = 'item_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.section.'.$uniq.' .info-heading {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.section.'.$uniq.' .info-heading {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.section.'.$uniq.' .info-heading {font-size:'. $tsize .';}' : '';

        $item_css[] = $atts['tclr'] ? '.section.'.$uniq.' .info-heading {color: '. esc_html($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['thclr'] ? '.section.'.$uniq.' .info-heading span{color: '. esc_attr($atts['thclr']) .'; }' : '';
        $item_css[] = $atts['descclr'] ? '.section.'.$uniq.' p{color: '. esc_attr($atts['descclr']) .'; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';
        $out .= '<div class="section'.$uniq.$bgcss.'"'.$item_css.'>';
        $out .= '<div class="container">';
        $out .= '<div class="row justify-content-xl-between">';
        $out .= '<div class="col-12 col-lg col-xl-7">';
        if ($atts['title'] || $atts['thintitle']) {
            if ($atts['title']) {
                $out .= '<'.$htag.' class="info-heading">'.$atts['title'].' <span>'.$atts['thintitle'].'</span></'.$htag.'>';
            }
        }
        $out .= do_shortcode($content);
        $out .= '</div>';

        $out .= '<div class="col-12 my-3 d-lg-none"></div>';

        $out .= '<div class="col-12 col-lg-auto  text-center">';
        $blankimg = '';
        $out .= agro_img($atts['img'], $blankimg, $imgclass='img-fluid', $atts['imgw'], $atts['imgh']);
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_info2', 'agro_vc_info2');
}


/*******************************/
/* info slider
/******************************/
if (!function_exists('agro_vc_info_slider')) {
    function agro_vc_info_slider($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        // data
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
        'stitle' => '',
        'sthintitle' => '',
        'sdesc' => '',
        'link' => '',
        'bgimg' => '',
        'bgimgw' => '',
        'bgimgh' => '',
        'img' => '',
        'imgw' => '',
        'imgh' => '',
        'loop' => '',
        'title' => '',
        'thintitle' => '',
        'desc' => '',
        // custom css
        'tclr' => '',
        'thclr' => '',
        'descclr' => '',
        'stclr' => '',
        'sthtclr' => '',
        'sdescclr' => '',
        // background css
        'css' => '',
        'speed' => '',
        'autoplay' => '',
        'dots' => '',
        'pauseonhover' => '',
        ), $atts, 'agro_info_slider');

        $speed = $atts['speed'] ? $atts['speed'] : 1000;
        $autoplay = $atts['autoplay'] == 'yes' ? 'true' : 'false';
        $dots = $atts['dots'] == 'yes' ? 'true' : 'false';
        $pauseonhover = $atts['pauseonhover'] == 'yes' ? 'true' : 'false';

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $uniq = 'item_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.section.'.$uniq.' .section-heading .__title {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.section.'.$uniq.' .section-heading .__title {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.section.'.$uniq.' .section-heading .__title {font-size:'. $tsize .';}' : '';

        $item_css[] = $atts['tclr'] ? '.section.'.$uniq.' .section-heading .__title {color: '. esc_html($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['thclr'] ? '.section.'.$uniq.' .section-heading .__title span{color: '. esc_attr($atts['thclr']) .'; }' : '';
        $item_css[] = $atts['descclr'] ? '.section.'.$uniq.' .p{color: '. esc_attr($atts['descclr']) .'; }' : '';
        $item_css[] = $atts['stclr'] ? '.section.'.$uniq.' .js-slick .__item h3{color: '. esc_attr($atts['stclr']) .'; }' : '';
        $item_css[] = $atts['sthtclr'] ? '.section.'.$uniq.' .js-slick .__item h3 span{color: '. esc_attr($atts['sthtclr']) .'; }' : '';
        $item_css[] = $atts['sdescclr'] ? '.section.'.$uniq.' .js-slick .__item p{color: '. esc_attr($atts['sdescclr']) .'; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';

        $out .= '<div class="section section--no-pb jarallax'.$uniq.$bgcss.'" data-speed="0.4" data-img-position="50% 65%"'.$item_css.'>';

        $out .= agro_img($atts['bgimg'], $blankimg='', $imgclass='jarallax-img', $atts['bgimgw'], $atts['bgimgh']);

        $out .= '<div class="container">';
        if ($atts['stitle'] || $atts['sthintitle'] || $atts['sdesc']) {
            $out .= '<div class="section-heading section-heading--center section-heading--white" data-aos="fade">';
            if ($atts['stitle'] || $atts['sthintitle']) {
                $out .= '<'.$htag.' class="__title">'. esc_html($atts['stitle']) .' <span>'. esc_html($atts['sthintitle']) .'</span></'.$htag.'>';
            }
            if ($atts['sdesc']) {
                $out .= '<p class="text-white">'. esc_html($atts['sdesc']) .'</p>';
            }
            $out .= '</div>';
        }

        $out .= '<div class="simple-text-block simple-text-block--no-pb">';
        $out .= '<div class="row justify-content-md-center">';
        $out .= '<div class="col-12 col-md-11">';

        $out .= '<div class="row justify-content-lg-between no-gutters">';
        $out .= '<div class="col-12 col-lg-5">';
        $blankimg = '';
        $out .= agro_img($atts['img'], $blankimg, $imgclass='img-fluid', $atts['imgw'], $atts['imgh']);
        $out .= '</div>';

        $out .= '<div class="col-12 my-3 d-lg-none"></div>';

        $out .= '<div class="col-12 col-lg-6">';
        $out .= '<div class="js-slick" data-slick=\'{"autoplay": '.$autoplay.', "arrows": false, "dots": '.$dots.', "pauseOnHover": '.$pauseonhover.', "speed": '.$speed.'}\'>';

        $loop = (array) vc_param_group_parse_atts($atts['loop']);
        foreach ($loop as $item) {
            $out .= '<div class="__item">';
            if (isset($item['title']) != '' || isset($item['thintitle']) != '') {
                $out .= '<h3>'.$item['title'].' <span>'.$item['thintitle'].'</span></h3>';
            }
            if (isset($item['desc']) != '') {
                $out .= wpautop($item['desc']);
            }
            $out .= '</div>';
        }

        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_info_slider', 'agro_vc_info_slider');
}


/*******************************/
/* slider_container
/******************************/
if (!function_exists('agro_vc_slider_container')) {
    function agro_vc_slider_container($atts, $content = null)
    {
        $atts = shortcode_atts(array(
            'duration' => '4000',
            'delay' => '5000',
            'autoplay' => 'no',
            'timer' => 'no',
            'dots' => 'yes',
            'overlay' => '05',
            'prev' => 'Prev',
            'next' => 'Next',
            'discover' => '',
            // color
            'progressclr' => '',
            'discoverclr' => '',
            'dotsclr' => '',
            'dotsactclr' => '',
            'sloop' => '',
            'transition' => '',
        ), $atts, 'agro_slider_container');

        $uniq = 'item_'.uniqid();
        $item_css = array();
        $item_css[] = $atts['progressclr'] ? '.vegas-timer-progress {background-color: '. esc_attr($atts['progressclr']) .' !important; }' : '';
        $item_css[] = $atts['dotsclr'] ? '#start-screen #vegas-slider .vegas-dots a {border-color: '. esc_attr($atts['dotsclr']) .'; }' : '';
        $item_css[] = $atts['dotsactclr'] ? '#start-screen #vegas-slider .vegas-dots a.active {background-color: '. esc_attr($atts['dotsactclr']) .';border-color: '. esc_attr($atts['dotsactclr']) .'; }' : '';
        $item_css[] = $atts['discoverclr'] ? '.start-screen[data-scroll-discover=true] .scroll-discover:after, .start-screen[data-scroll-discover=true] .scroll-discover:before {background-color: '. esc_attr($atts['discoverclr']) .' !important; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $default = "['fade', 'zoomOut', 'blur', 'swirlLeft', 'swirlRight']";
		$transition = array();
        $sloop = (array) vc_param_group_parse_atts($atts['sloop']);
        foreach ( $sloop as $s ) {
            if ( isset( $s['transition'] ) !='' ){
                array_push($transition, $s['transition']);
            }
        }
        if ( ! empty( $transition ) ) {
            $transition = ! empty( $transition ) ? '\''.implode("','",$transition).'\'' : $default;
        } else {
            $transition ="['fade', 'zoomOut', 'blur', 'swirlLeft', 'swirlRight']";
        }

        $out = '';
        $overlayimg = $atts['overlay'] != 'none' ? ' data-overlayimg="'.get_parent_theme_file_uri().'/images/overlays/'.$atts['overlay'].'.png"' : '';
        $autoplay = $atts['autoplay'] == 'yes' ? 'true' : 'false';
        $timer = $atts['timer'] == 'yes' ? 'true' : 'false';
        $dots = $atts['dots'] == 'yes' ? 'true' : 'false';
        $discover = $atts['discover'] == 'yes' || $atts['discover'] == '' ? 'true' : 'false';
        $out .= '<div id="start-screen" class="start-screen start-screen--style-1'.$uniq.'" data-scroll-discover="'.$discover.'"'.$item_css.'>';
        $out .= '<div id="start-screen__bg" class="start-screen__bg">';
        $out .= '<div id="vegas-slider" data-autoplay="'.$autoplay.'" data-timer="'.$timer.'" data-dots="'.$dots.'" data-duration="'.$atts['duration'].'" data-delay="'.$atts['delay'].'" data-transition="'.$transition.'"'.$overlayimg.'>';
        $out .= '<div class="vegas-control">';
        $out .= '<span id="vegas-control__prev" class="vegas-control__btn">'.$atts['prev'].'</span>';
        $out .= '<span id="vegas-control__next" class="vegas-control__btn">'.$atts['next'].'</span>';
        $out .= '</div>';
        $out .= '</div>';

        $out .= '</div>';

        $out .= '<div id="start-screen__content-container" class="start-screen__content-container text-white">';

        $out .= do_shortcode($content);

        $out .= '</div>';
        $out .= '<span class="scroll-discover"></span>';
        $out .= '</div>';


        return $out;
    }
    add_shortcode('agro_slider_container', 'agro_vc_slider_container');
}

/*******************************/
/* slider item style 1
/******************************/
if (!function_exists('agro_vc_slider_item1')) {
    function agro_vc_slider_item1($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        // data
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
        'img' => '',
        'subtitle' => '',
        'title' => '',
        'desc' => '',
        'link' => '',
        'xl' => 'col-xl-8',
        'alignment' => '',
        // custom css
        'stclr' => '',
        'tclr' => '',
        'dclr' => '',
        'btnclr' => '',
        'btnhvrclr' => '',
        'btnbrd' => '',
        'btnhvrbrd' => '',
        'btnbg' => '',
        'btnhvrbg' => '',
        'btntype' => '',
        'btnstyle' => '',
        'btnsize' => '',
        //video
        'video' => '',
        'videosrc' => '',
        'loop' => '',
        'mute' => '',
        'delay' => '',
        // background css
        'css' => '',
        ), $atts, 'agro_slider_item1');

        $btntype  = $atts['btntype'] ? ' '.$atts['btntype'] : '';
        $btnstyle = $atts['btnstyle'] ? ' '.$atts['btnstyle'] : ' custom-btn--style-3';
        $btnsize  = $atts['btnsize'] ? ' '.$atts['btnsize'] : ' custom-btn--big';

        $delay = $atts['delay'] == 'yes' ? ' data-item-delay="'.$atts['delay'].'"' : '';
        $mute  = $atts['mute'] == 'yes' ? ' data-video-mute="false"' : ' data-video-mute="true"';
        $loop  = $atts['loop'] == 'yes' ? ' data-video-loop="true"' : ' data-video-loop="false"';
        $video = $atts['video'] == 'yes' && $atts['videosrc'] ? ' data-video="true" data-video-src="'.$atts['videosrc'].'"'.$mute.$loop : ' data-video="false"';

        $uniq = 'item_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.start-screen__content__item.start-screen__content__item--1.'.$uniq.' .__title {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.start-screen__content__item.start-screen__content__item--1.'.$uniq.' .__title {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.start-screen__content__item.start-screen__content__item--1.'.$uniq.' .__title {font-size:'. $tsize .';}' : '';

        $item_css[] = $atts['stclr'] ? '.start-screen__content__item.start-screen__content__item--1.'.$uniq.' .__name {color: '. esc_attr($atts['stclr']) .' !important; }' : '';
        $item_css[] = $atts['tclr'] ? '.start-screen__content__item.start-screen__content__item--1.'.$uniq.' .__title {color: '. esc_attr($atts['tclr']) .' !important; }' : '';
        $item_css[] = $atts['dclr'] ? '.start-screen__content__item.start-screen__content__item--1.'.$uniq.' p.text-center {color: '. esc_attr($atts['dclr']) .' !important; }' : '';
        $item_css[] = $atts['btnclr'] ? '.start-screen__content__item.start-screen__content__item--1.'.$uniq.' .custom-btn {color: '. esc_attr($atts['btnclr']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrclr'] ? '.start-screen__content__item.start-screen__content__item--1.'.$uniq.' .custom-btn:hover {color: '. esc_attr($atts['btnhvrclr']) .'!important; }' : '';
        $item_css[] = $atts['btnbrd'] ? '.start-screen__content__item.start-screen__content__item--1.'.$uniq.' .custom-btn {border-color: '. esc_attr($atts['btnbrd']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrbrd'] ? '.start-screen__content__item.start-screen__content__item--1.'.$uniq.' .custom-btn:hover {border-color: '. esc_attr($atts['btnhvrbrd']) .'!important; }' : '';
        $item_css[] = $atts['btnbg'] ? '.start-screen__content__item.start-screen__content__item--1.'.$uniq.' .custom-btn {background-color: '. esc_attr($atts['btnbg']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrbg'] ? '.start-screen__content__item.start-screen__content__item--1.'.$uniq.' .custom-btn:hover {background-color: '. esc_attr($atts['btnhvrbg']) .'!important; }' : '';

        $item_css[] = $atts['btntype'] == 'btn-rounded' ? '.start-screen__content__item.start-screen__content__item--1.'.$uniq.' .custom-btn.btn-rounded {border-radius: 10px!important; }' : '';
        $item_css[] = $atts['btntype'] == 'btn-square' ? '.start-screen__content__item.start-screen__content__item--1.'.$uniq.' .custom-btn.btn-square {border-radius: 0px !important; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';
        $alignment = $atts['alignment'] ? ' '.$atts['alignment']  : '';

        $out = '';
        $imgurl = wp_get_attachment_url($atts['img'], 'full');
        $out .= '<div class="start-screen__content__item start-screen__content__item--1 align-items-center'.$uniq.'" data-img-src="'.$imgurl.'"'.$delay.$video.$item_css.'>';
        $out .= '<div class="container">';
        $out .= '<div class="row justify-content-center">';
        $out .= '<div class="col-12 col-md-10 '.$atts['xl'].$alignment.'">';

        if ($atts['subtitle']) {
            $out .= '<div class="__name">'.$atts['subtitle'].'</div>';
        }
        if ($atts['title']) {
            $out .= '<'.$htag.' class="__title text-white">'.$atts['title'].'</'.$htag.'>';
        }
        if ($atts['desc']) {
            $alignment = $atts['alignment'] ? ''  : ' class="text-center"';
            $out .= '<p'.$alignment.'>'.$atts['desc'].'</p>';
        }
        // slider button
        $link = !empty($atts['link']) ? $atts['link'] : '';
        $link = vc_build_link($link);
        $atitle = $link['title'];
        $href = $link['url'] != '' ? ' href="'. esc_url($link['url']) .'"' : ' href="#"';
        $target = $link['target'] != '' ? ' target="'. esc_attr($link['target']) .'"' : '';

        if ($atitle != '') {
            $alignment = $atts['alignment'] ? ''  : ' text-center';
            $out .= '<p class="mt-5 mt-md-10'.$alignment.'">';
            $out .= '<span class="d-none d-sm-block"><a class="custom-btn'.$btnsize.$btnstyle.$btntype.'"'. $href . $target .'>'. esc_html($atitle) .'</a></span>';
            $out .= '<span class="d-block d-sm-none"><a class="custom-btn custom-btn--small'.$btnstyle.$btntype.'"'. $href . $target .'>'. esc_html($atitle) .'</a></span>';
            $out .= '</p>';
        }

        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_slider_item1', 'agro_vc_slider_item1');
}


/*******************************/
/* slider item style 2
/******************************/
if (!function_exists('agro_vc_slider_item2')) {
    function agro_vc_slider_item2($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        // data
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
        'img' => '',
        'subtitle' => '',
        'title' => '',
        'thintitle' => '',
        'desc' => '',
        'link' => '',
        'vurl' => '',
        'xl' => 'col-xl-8',
        'alignment' => '',
        // custom css
        'stclr' => '',
        'tclr' => '',
        'thclr' => '',
        'dclr' => '',
        'playiconclr' => '',
        'playiconbg' => '',
        'playbrd' => '',
        'btnclr' => '',
        'btnhvrclr' => '',
        'btnbrd' => '',
        'btnhvrbrd' => '',
        'btnbg' => '',
        'btnhvrbg' => '',
        'btntype' => '',
        'btnstyle' => '',
        'btnsize' => '',
        //video
        'video' => '',
        'videosrc' => '',
        'loop' => '',
        'mute' => '',
        'delay' => '',
    ), $atts, 'agro_slider_item2');

    $delay = $atts['delay'] == 'yes' ? ' data-item-delay="'.$atts['delay'].'"' : '';
    $mute  = $atts['mute'] == 'yes' ? ' data-video-mute="true"' : ' data-video-mute="false"';
    $loop  = $atts['loop'] == 'yes' ? ' data-video-loop="true"' : ' data-video-loop="false"';
    $video = $atts['video'] == 'yes' && $atts['videosrc'] ? ' data-video="true" data-video-src="'.$atts['videosrc'].'"'.$mute.$loop : ' data-video="false"';

    $btntype  = $atts['btntype'] ? ' '.$atts['btntype'] : '';
    $btnstyle = $atts['btnstyle'] ? ' '.$atts['btnstyle'] : ' custom-btn--style-3';
    $btnsize  = $atts['btnsize'] ? ' '.$atts['btnsize'] : ' custom-btn--big';
    $uniq     = 'item_'.uniqid();
    $item_css = array();

    $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
    $f_family   = 'font_family:Abril%20Fatface%3Aregular';
    $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
    $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
    $item_css[] = $google ? '.start-screen__content__item.start-screen__content__item--2.'.$uniq.' .__title {'. $google .'}' : '';

    $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
    $tsize   = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
    $item_css[] = $atts['lheight'] ? '.start-screen__content__item.start-screen__content__item--2.'.$uniq.' .__title {line-height:'. $lheight .';}' : '';
    $item_css[] = $atts['tsize'] ? '.start-screen__content__item.start-screen__content__item--2.'.$uniq.' .__title {font-size:'. $tsize .';}' : '';

    $item_css[] = $atts['stclr'] ? '.start-screen__content__item.start-screen__content__item--2.'.$uniq.' .__name {color: '. esc_attr($atts['stclr']) .' !important; }' : '';
    $item_css[] = $atts['tclr'] ? '.start-screen__content__item.start-screen__content__item--2.'.$uniq.' .__title {color: '. esc_attr($atts['tclr']) .' !important; }' : '';
    $item_css[] = $atts['thclr'] ? '.start-screen__content__item.start-screen__content__item--2.'.$uniq.' .__title span {color: '. esc_attr($atts['thclr']) .' !important; }' : '';
    $item_css[] = $atts['dclr'] ? '.start-screen__content__item.start-screen__content__item--2.'.$uniq.' p {color: '. esc_attr($atts['dclr']) .' !important; }' : '';
    $item_css[] = $atts['playiconclr'] ? '.start-screen__content__item.start-screen__content__item--2.'.$uniq.' .play-btn span:before {border-color:transparent transparent transparent '. esc_attr($atts['playiconclr']) .' !important; }' : '';
    $item_css[] = $atts['playiconbg'] ? '.start-screen__content__item.start-screen__content__item--2.'.$uniq.' .play-btn span {background-color: '. esc_attr($atts['playiconbg']) .' !important; }' : '';
    $item_css[] = $atts['playbrd'] ? '.start-screen__content__item.start-screen__content__item--2.'.$uniq.' .play-btn {border-color: '. esc_attr($atts['playbrd']) .' !important; }' : '';
    $item_css[] = $atts['btnclr'] ? '.start-screen__content__item.start-screen__content__item--2.'.$uniq.' .custom-btn {color: '. esc_attr($atts['btnclr']) .'!important; }' : '';
    $item_css[] = $atts['btnhvrclr'] ? '.start-screen__content__item.start-screen__content__item--2.'.$uniq.' .custom-btn:hover {color: '. esc_attr($atts['btnhvrclr']) .'!important; }' : '';
    $item_css[] = $atts['btnbrd'] ? '.start-screen__content__item.start-screen__content__item--2.'.$uniq.' .custom-btn {border-color: '. esc_attr($atts['btnbrd']) .'!important; }' : '';
    $item_css[] = $atts['btnhvrbrd'] ? '.start-screen__content__item.start-screen__content__item--2.'.$uniq.' .custom-btn:hover {border-color: '. esc_attr($atts['btnhvrbrd']) .'!important; }' : '';
    $item_css[] = $atts['btnbg'] ? '.start-screen__content__item.start-screen__content__item--2.'.$uniq.' .custom-btn {background-color: '. esc_attr($atts['btnbg']) .'!important; }' : '';
    $item_css[] = $atts['btnhvrbg'] ? '.start-screen__content__item.start-screen__content__item--2.'.$uniq.' .custom-btn:hover {background-color: '. esc_attr($atts['btnhvrbg']) .'!important; }' : '';

    $item_css[] = $atts['btntype'] == 'btn-rounded' ? '.start-screen__content__item.start-screen__content__item--2.'.$uniq.' .custom-btn.btn-rounded {border-radius: 10px!important; }' : '';
    $item_css[] = $atts['btntype'] == 'btn-square' ? '.start-screen__content__item.start-screen__content__item--2.'.$uniq.' .custom-btn.btn-square {border-radius: 0px !important; }' : '';

    $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
    $uniq = $item_css ? ' '.$uniq  : '';
    $alignment = $atts['alignment'] ? ' '.$atts['alignment']  : '';

        $out = '';
        $imgurl = wp_get_attachment_url($atts['img'], 'full');
        $out .= '<div class="start-screen__content__item start-screen__content__item--2 align-items-center'.$uniq.'" data-img-src="'.$imgurl.'"'.$video.$delay.$item_css.'>';
        $out .= '<div class="container">';
        $out .= '<div class="row align-items-center '.$alignment.'">';
        $out .= '<div class="col-12 col-md col-lg-9 '.$atts['xl'].'">';

        if ($atts['subtitle']) {
            $out .= '<div class="__name">'.$atts['subtitle'].'</div>';
        }
        if ($atts['title']) {
            $out .= '<'.$htag.' class="__title text-white">'.$atts['title'].' <br><span>'.$atts['thintitle'].'</span></'.$htag.'>';
        }
        if ($atts['desc']) {
            $out .= '<p>'.$atts['desc'].'</p>';
        }

        // slider button
        $link = !empty($atts['link']) ? $atts['link'] : '';
        $link = vc_build_link($link);
        $atitle = $link['title'];
        $href = $link['url'] != '' ? ' href="'. esc_url($link['url']) .'"' : ' href="#"';
        $target = $link['target'] != '' ? ' target="'. esc_attr($link['target']) .'"' : '';

        if ($atitle != '') {
            $out .= '<p class="mt-5 mt-md-10">';
            $out .= '<span class="d-none d-sm-block"><a class="custom-btn'.$btnsize.$btnstyle.$btntype.'"'. $href . $target .'>'. esc_html($atitle) .'</a></span>';
            $out .= '<span class="d-block d-sm-none"><a class="custom-btn custom-btn--small'.$btnstyle.$btntype.'"'. $href . $target .'>'. esc_html($atitle) .'</a></span>';
            $out .= '</p>';
        }

        $out .= '</div>';

        $out .= '<div class="col-12 my-3 d-md-none"></div>';

        if ($atts['vurl'] != '') {
            $column = 'col-xl-4';
            switch ($atts['xl']) {
                case 'col-xl-6':
                    $column = 'col-xl-6 text-right';
                    break;
                case 'col-xl-7':
                    $column = 'col-xl-5 text-right';
                    break;
                case 'col-xl-8':
                    $column = 'col-xl-4 text-right';
                    break;
                case '':
                    $column = 'col-xl-4 text-right';
                    break;
                default:
                    $column = 'col-xl-12 text-center my-6';
                    break;
            }
            $out .= '<div class="col-12 col-md-auto col-lg-3 '.$column.'">';
            $out .= '<a class="play-btn" data-fancybox="" href="'.esc_url($atts['vurl']).'"><span></span></a>';
            $out .= '</div>';
        }


        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_slider_item2', 'agro_vc_slider_item2');
}


/*******************************/
/* slider item style 3
/******************************/
if (!function_exists('agro_vc_slider_item3')) {
    function agro_vc_slider_item3($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        // data
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
        'img' => '',
        'subtitle' => '',
        'title' => '',
        'spantitle' => '',
        'spantitleclr' => '',
        'xl' => 'col-xl-8',
        'link' => '',
        'alignment' => '',
        // custom css
        'stclr' => '',
        'tclr' => '',
        'spantitleclr' => '',
        'dclr' => '',
        'btnclr' => '',
        'btnhvrclr' => '',
        'btnbrd' => '',
        'btnhvrbrd' => '',
        'btnbg' => '',
        'btnhvrbg' => '',
        'btntype' => '',
        'btnstyle' => '',
        'btnsize' => '',
        //video
        'video' => '',
        'videosrc' => '',
        'loop' => '',
        'mute' => '',
        'delay' => '',
    ), $atts, 'agro_slider_item3');

    $delay = $atts['delay'] == 'yes' ? ' data-item-delay="'.$atts['delay'].'"' : '';
    $mute  = $atts['mute'] == 'yes' ? ' data-video-mute="true"' : ' data-video-mute="false"';
    $loop  = $atts['loop'] == 'yes' ? ' data-video-loop="true"' : ' data-video-loop="false"';
    $video = $atts['video'] == 'yes' && $atts['videosrc'] ? ' data-video="true" data-video-src="'.$atts['videosrc'].'"'.$mute.$loop : ' data-video="false"';

    $btntype  = $atts['btntype'] ? ' '.$atts['btntype'] : '';
    $btnstyle = $atts['btnstyle'] ? ' '.$atts['btnstyle'] : ' custom-btn--style-3';
    $btnsize  = $atts['btnsize'] ? ' '.$atts['btnsize'] : ' custom-btn--big';
    $uniq     = 'item_'.uniqid();
    $item_css = array();

    $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
    $f_family   = 'font_family:Abril%20Fatface%3Aregular';
    $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
    $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
    $item_css[] = $google ? '.start-screen__content__item.start-screen__content__item--3.'.$uniq.' .__title {'. $google .'}' : '';

    $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
    $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
    $item_css[] = $atts['lheight'] ? '.start-screen__content__item.start-screen__content__item--3.'.$uniq.' .__title {line-height:'. $lheight .';}' : '';
    $item_css[] = $atts['tsize'] ? '.start-screen__content__item.start-screen__content__item--3.'.$uniq.' .__title {font-size:'. $tsize .';}' : '';

    $item_css[] = $atts['stclr'] ? '.start-screen__content__item.start-screen__content__item--3.'.$uniq.' .__name {color: '. esc_attr($atts['stclr']) .' !important; }' : '';
    $item_css[] = $atts['tclr'] ? '.start-screen__content__item.start-screen__content__item--3.'.$uniq.' .__title {color: '. esc_attr($atts['tclr']) .' !important; }' : '';
    $item_css[] = $atts['spantitleclr'] ? '.start-screen__content__item.start-screen__content__item--3.'.$uniq.' .__title span {color: '. esc_attr($atts['spantitleclr']) .' !important; }' : '';
    $item_css[] = $atts['dclr'] ? '.start-screen__content__item.start-screen__content__item--3.'.$uniq.' p {color: '. esc_attr($atts['dclr']) .' !important; }' : '';
    // btn
    $item_css[] = $atts['btnclr'] ? '.start-screen__content__item.start-screen__content__item--3.'.$uniq.' .custom-btn {color: '. esc_attr($atts['btnclr']) .'!important; }' : '';
    $item_css[] = $atts['btnhvrclr'] ? '.start-screen__content__item.start-screen__content__item--3.'.$uniq.' .custom-btn:hover {color: '. esc_attr($atts['btnhvrclr']) .'!important; }' : '';
    $item_css[] = $atts['btnbrd'] ? '.start-screen__content__item.start-screen__content__item--3.'.$uniq.' .custom-btn {border-color: '. esc_attr($atts['btnbrd']) .'!important; }' : '';
    $item_css[] = $atts['btnhvrbrd'] ? '.start-screen__content__item.start-screen__content__item--3.'.$uniq.' .custom-btn:hover {border-color: '. esc_attr($atts['btnhvrbrd']) .'!important; }' : '';
    $item_css[] = $atts['btnbg'] ? '.start-screen__content__item.start-screen__content__item--3.'.$uniq.' .custom-btn {background-color: '. esc_attr($atts['btnbg']) .'!important; }' : '';
    $item_css[] = $atts['btnhvrbg'] ? '.start-screen__content__item.start-screen__content__item--3.'.$uniq.' .custom-btn:hover {background-color: '. esc_attr($atts['btnhvrbg']) .'!important; }' : '';

    $item_css[] = $atts['btntype'] == 'btn-rounded' ? '.start-screen__content__item.start-screen__content__item--3.'.$uniq.' .custom-btn.btn-rounded {border-radius: 10px!important; }' : '';
    $item_css[] = $atts['btntype'] == 'btn-square' ? '.start-screen__content__item.start-screen__content__item--3.'.$uniq.' .custom-btn.btn-square {border-radius: 0px !important; }' : '';

    $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
    $uniq = $item_css ? ' '.$uniq  : '';
    $alignment = $atts['alignment'] ? ' '.$atts['alignment']  : '';

        $out = '';
        $imgurl = wp_get_attachment_url($atts['img'], 'full');
        $out .= '<div class="start-screen__content__item start-screen__content__item--3 align-items-center'.$uniq.'" data-img-src="'.$imgurl.'"'.$video.$delay.$item_css.'>';
        $out .= '<div class="container">';
        $out .= '<div class="row justify-content-center">';
        $out .= '<div class="col-12 col-md-auto '.$atts['xl'].$alignment.'">';

        if ($atts['subtitle']) {
            $out .= '<div class="__name">'.$atts['subtitle'].'</div>';
        }
        if ($atts['title']) {
            $out .= '<'.$htag.' class="__title text-white">'.$atts['title'].'<span>'.$atts['spantitle'].'</span></'.$htag.'>';
        }

        // slider button
        $link = !empty($atts['link']) ? $atts['link'] : '';
        $link = vc_build_link($link);
        $atitle = $link['title'];
        $href = $link['url'] != '' ? ' href="'. esc_url($link['url']) .'"' : ' href="#"';
        $target = $link['target'] != '' ? ' target="'. esc_attr($link['target']) .'"' : '';

        if ($atitle != '') {
            $alignment = $atts['alignment'] ? '' : ' text-center';
            $out .= '<p class="mt-5 mt-md-10'.$alignment.'">';
            $out .= '<span class="d-none d-sm-block"><a class="custom-btn'.$btnsize.$btnstyle.$btntype.'"'. $href . $target .'>'. esc_html($atitle) .'</a></span>';
            $out .= '<span class="d-block d-sm-none"><a class="custom-btn custom-btn--small'.$btnstyle.$btntype.'"'. $href . $target .'>'. esc_html($atitle) .'</a></span>';
            $out .= '</p>';
        }

        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_slider_item3', 'agro_vc_slider_item3');
}

/*******************************/
/* slider item style 4
/******************************/
if (!function_exists('agro_vc_slider_item4')) {
    function agro_vc_slider_item4($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        // data
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
        'img' => '',
        'spantitle1' => '',
        'spantitleclr1' => '',
        'spantitle2' => '',
        'spantitleclr2' => '',
        'lg' => 'col-lg-8',
        'vurl' => '',
        'alignment' => '',
        //video
        'video' => '',
        'videosrc' => '',
        'loop' => '',
        'mute' => '',
        'delay' => '',
    ), $atts, 'agro_slider_item4');

        $delay = $atts['delay'] == 'yes' ? ' data-item-delay="'.$atts['delay'].'"' : '';
        $mute  = $atts['mute'] == 'yes' ? ' data-video-mute="true"' : ' data-video-mute="false"';
        $loop  = $atts['loop'] == 'yes' ? ' data-video-loop="true"' : ' data-video-loop="false"';
        $video = $atts['video'] == 'yes' && $atts['videosrc'] ? ' data-video="true" data-video-src="'.$atts['videosrc'].'"'.$mute.$loop : ' data-video="false"';

        $uniq = 'item_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.start-screen__content__item.start-screen__content__item--4.'.$uniq.' .__title {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize   = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.start-screen__content__item.start-screen__content__item--4.'.$uniq.' .__title {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.start-screen__content__item.start-screen__content__item--4.'.$uniq.' .__title {font-size:'. $tsize .';}' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';
        $alignment = $atts['alignment'] ? ' '.$atts['alignment']  : '';

        $out = '';
        $imgurl = wp_get_attachment_url($atts['img'], 'full');
        $out .= '<div class="start-screen__content__item start-screen__content__item--4 align-items-center'. $uniq.'"'.$video.$delay.$item_css.' data-img-src="'.esc_url($imgurl).'">';
        $out .= '<div class="container">';
        $out .= '<div class="row align-items-center">';
        $out .= '<div class="col-12 '.$atts['lg'].$alignment.'">';
        $out .= '<'.$htag.' class="__title">';
        $out .= '<span style="color: '.$atts['spantitleclr1'].'">'.$atts['spantitle1'].'</span> ';
        $out .= '<span style="color: '.$atts['spantitleclr2'].'">'.$atts['spantitle2'].'</span>';
        $out .= '</'.$htag.'>';
        $out .= '</div>';

        $out .= '<div class="col-12 my-3 d-lg-none"></div>';

        if ($atts['vurl'] != '') {
            $out .= '<div class="col-12 col-lg-4  text-center">';
            $out .= '<a class="play-btn" data-fancybox="" href="'.esc_url($atts['vurl']).'"><span></span></a>';
            $out .= '<span></span>';
            $out .= '</div>';
        }

        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_slider_item4', 'agro_vc_slider_item4');
}
/*******************************/
/* home 3 hero
/******************************/
if (!function_exists('agro_vc_home3hero')) {
    function agro_vc_home3hero($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
        'title' => '',
        'bgimg' => '',
        'md' => '',
        'alignment' => '',
        // background css
        'css' => '',
        ), $atts, 'agro_home3hero');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $uniq = 'item_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.start-screen.start-screen--style-2.'.$uniq.' .__title {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.start-screen.start-screen--style-2.'.$uniq.' .__title {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.start-screen.start-screen--style-2.'.$uniq.' .__title {font-size:'. $tsize .';}' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';
        $alignment = $atts['alignment'] ? ' '.$atts['alignment']  : '';
        $column = '' != $atts['md'] ? $atts['md']  : 'col-md-6';

        $out = '';
        $bgimg = wp_get_attachment_url($atts['bgimg'], 'full');
        $out .= '<div id="start-screen" class="start-screen start-screen--style-2'. $uniq.$bgcss .'"'.$item_css.'>';
        $out .= '<div id="start-screen__bg" class="start-screen__bg" style="background-image: url('. esc_url($bgimg) .');background-position: 70% 0;"></div>';

        $out .= '<div id="start-screen__content-container" class="start-screen__content-container">';
        $out .= '<div class="start-screen__content__item is-active  align-items-center">';
        $out .= '<div class="container">';
        $out .= '<div class="row">';
        $out .= '<div class="col-12 '.$column.$alignment.'">';
        $out .= '<'.$htag.' class="__title">'. esc_html($atts['title']) .'</'.$htag.'>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_home3hero', 'agro_vc_home3hero');
}

/*******************************/
/* slider_container
/******************************/
if (!function_exists('agro_vc_slick_slider_container')) {
    function agro_vc_slick_slider_container($atts, $content = null)
    {
        $atts = shortcode_atts(array(
            'speed' => '1200',
        ), $atts, 'agro_slick_slider_container');

        $out = '';

        $out .= '<div id="start-screen" class="start-screen start-screen--style-4 js-slick" data-slick=\'{ "autoplay":true,"fade":true,"speed":'.$atts['speed'].',"arrows":true,"dots":false}\'>';

            $out .= do_shortcode($content);

        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_slick_slider_container', 'agro_vc_slick_slider_container');
}
/*******************************/
/* slider item style 1
/******************************/
if (!function_exists('agro_vc_slick_slider_item1')) {
    function agro_vc_slick_slider_item1($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        // data
        'style' => '',
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
        'img' => '',
        'bgpos' => '',
        'custom_bgpos' => '',
        'subtitle' => '',
        'title' => '',
        'desc' => '',
        'link' => '',
        'xl' => 'col-xl-8',
        // custom css
        'tclr' => '',
        'dclr' => '',
        'btnclr' => '',
        'btnhvrclr' => '',
        'btnbrd' => '',
        'btnhvrbrd' => '',
        'btnbg' => '',
        'btnhvrbg' => '',
        'btntype' => '',
        'btnstyle' => '',
        'btnsize' => '',
        // background css
        'css' => '',
        ), $atts, 'agro_slick_slider_item1');

        $btntype  = $atts['btntype'] ? ' '.$atts['btntype'] : '';
        $btnstyle = $atts['btnstyle'] ? ' '.$atts['btnstyle'] : ' custom-btn--style-1';
        $btnsize  = $atts['btnsize'] ? ' '.$atts['btnsize'] : ' custom-btn--big';
        $cbgpos   = $atts['custom_bgpos'] ? 'background-position: '.$atts['custom_bgpos'].';' : '';
        $bgpos    = $atts['bgpos'] ? 'background-position: '.$atts['bgpos'].';' : '';
        $bg_pos   = $atts['bgpos'] == 'custom' ? $cbgpos : $bgpos;

        $uniq = 'item_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.start-screen__slide.'.$uniq.' .__title {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.start-screen__slide.'.$uniq.' .__title {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.start-screen__slide.'.$uniq.' .__title {font-size:'. $tsize .';}' : '';

        $item_css[] = $atts['tclr'] ? '.start-screen__slide.'.$uniq.' .__title {color: '. esc_attr($atts['tclr']) .' !important; }' : '';
        $item_css[] = $atts['dclr'] ? '.start-screen__slide.'.$uniq.' p.__desc {color: '. esc_attr($atts['dclr']) .' !important; }' : '';
        $item_css[] = $atts['btnclr'] ? '.start-screen__slide.'.$uniq.' .custom-btn {color: '. esc_attr($atts['btnclr']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrclr'] ? '.start-screen__slide.'.$uniq.' .custom-btn:hover {color: '. esc_attr($atts['btnhvrclr']) .'!important; }' : '';
        $item_css[] = $atts['btnbrd'] ? '.start-screen__slide.'.$uniq.' .custom-btn {border-color: '. esc_attr($atts['btnbrd']) .'; }' : '';
        $item_css[] = $atts['btnhvrbrd'] ? '.start-screen__slide.'.$uniq.' .custom-btn:hover {border-color: '. esc_attr($atts['btnhvrbrd']) .'!important; }' : '';
        $item_css[] = $atts['btnbg'] ? '.start-screen__slide.'.$uniq.' .custom-btn {background-color: '. esc_attr($atts['btnbg']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrbg'] ? '.start-screen__slide.'.$uniq.' .custom-btn:hover {background-color: '. esc_attr($atts['btnhvrbg']) .'!important; }' : '';

        $item_css[] = $atts['btntype'] == 'btn-rounded' ? '.start-screen__slide.'.$uniq.' .custom-btn.btn-rounded {border-radius: 10px!important; }' : '';
        $item_css[] = $atts['btntype'] == 'btn-square' ? '.start-screen__slide.'.$uniq.' .custom-btn.btn-square {border-radius: 0px !important; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        // slider button
        $link = !empty($atts['link']) ? $atts['link'] : '';
        $link = vc_build_link($link);
        $atitle = $link['title'];
        $href = $link['url'] != '' ? ' href="'. esc_url($link['url']) .'"' : ' href="#"';
        $target = $link['target'] != '' ? ' target="'. esc_attr($link['target']) .'"' : '';

        $out = '';
        $imgurl = wp_get_attachment_url($atts['img'], 'full');
        if($atts['style'] == '1') {
            $out .= '<div class="start-screen__slide'.$uniq.'"'.$item_css.'>';
                $out .= '<div class="start-screen__bg" style="background-image: url('.$imgurl.');'.$bg_pos.'"></div>';
                $out .= '<div class="start-screen__content__item align-items-center">';
                    $out .= '<div class="container">';
                        $out .= '<div class="row">';
                            $out .= '<div class="col-12 col-sm-10 col-md-9 col-lg-9 '.$atts['xl'].'">';
                                if ($atts['title']) {
                                    $out .= '<'.$htag.' class="__title">'.$atts['title'].'</'.$htag.'>';
                                }
                                if ($atitle != '') {
                                    $out .= '<p>';
                                        $out .= '<span class="d-none d-sm-block"><a class="custom-btn'.$btnsize.$btnstyle.$btntype.'"'. $href . $target .'>'. esc_html($atitle) .'</a></span>';
                                        $out .= '<span class="d-block d-sm-none"><a class="custom-btn custom-btn--small'.$btnstyle.$btntype.'"'. $href . $target .'>'. esc_html($atitle) .'</a></span>';
                                    $out .= '</p>';
                                }
                            $out .= '</div>';
                        $out .= '</div>';
                    $out .= '</div>';
                $out .= '</div>';
            $out .= '</div>';

        } elseif ($atts['style'] == '2') {

            $out .= '<div class="start-screen__slide'.$uniq.'"'.$item_css.'>';
                $out .= '<div class="start-screen__bg" style="background-image: url('.$imgurl.');'.$bg_pos.'"></div>';
                $out .= '<div class="start-screen__content__item align-items-center">';
                    $out .= '<div class="container">';
                        $out .= '<div class="row justify-content-end">';
                            $out .= '<div class="col-12 col-sm-10 col-md-9 col-lg-8 '.$atts['xl'].'">';
                                if ($atts['title']) {
                                    $out .= '<'.$htag.' class="__title">'.$atts['title'].'</'.$htag.'>';
                                }
                                if ($atts['desc']) {
                                    $out .= '<p class="__desc">'.$atts['desc'].'</p>';
                                }
                                if ($atitle != '') {
                                    $out .= '<p class="mt-5 mt-md-8">';
                                        $out .= '<span class="d-none d-sm-block"><a class="custom-btn'.$btnsize.$btnstyle.$btntype.'"'. $href . $target .'>'. esc_html($atitle) .'</a></span>';
                                        $out .= '<span class="d-block d-sm-none"><a class="custom-btn custom-btn--small'.$btnstyle.$btntype.'"'. $href . $target .'>'. esc_html($atitle) .'</a></span>';
                                    $out .= '</p>';
                                }
                            $out .= '</div>';
                        $out .= '</div>';
                    $out .= '</div>';
                $out .= '</div>';
            $out .= '</div>';

        } else {

            $out .= '<div class="start-screen__slide'.$uniq.'"'.$item_css.'>';
                $out .= '<div class="start-screen__bg" style="background-image: url('.$imgurl.');'.$bg_pos.'"></div>';
                $out .= '<div class="start-screen__content__item align-items-center">';
                    $out .= '<div class="container">';
                        $out .= '<div class="row justify-content-center text-center">';
                            $out .= '<div class="col-12 col-md-9 col-lg-8 '.$atts['xl'].'">';
                                if ($atts['title']) {
                                    $out .= '<'.$htag.' class="__title">'.$atts['title'].'</'.$htag.'>';
                                }
                                if ($atts['desc']) {
                                    $out .= '<p class="__desc">'.$atts['desc'].'</p>';
                                }
                                if ($atitle != '') {
                                    $out .= '<p class="mt-5 mt-md-8">';
                                        $out .= '<span class="d-none d-sm-block"><a class="custom-btn'.$btnsize.$btnstyle.$btntype.'"'. $href . $target .'>'. esc_html($atitle) .'</a></span>';
                                        $out .= '<span class="d-block d-sm-none"><a class="custom-btn custom-btn--small'.$btnstyle.$btntype.'"'. $href . $target .'>'. esc_html($atitle) .'</a></span>';
                                    $out .= '</p>';
                                }
                            $out .= '</div>';
                        $out .= '</div>';
                    $out .= '</div>';
                $out .= '</div>';
            $out .= '</div>';

        }
        return $out;
    }
    add_shortcode('agro_slick_slider_item1', 'agro_vc_slick_slider_item1');
}


/*******************************/
/* Section heading
/******************************/
if (!function_exists('agro_vc_section_heading')) {
    function agro_vc_section_heading($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        // data
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
        'title' => '',
        'thintitle' => '',
        'desc' => '',
        'anim' => '',
        'aos' => '',
        // custom css
        'tclr' => '',
        'thclr' => '',
        'descclr' => '',
        // background css
        'css' => '',
        ), $atts, 'agro_section_heading');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $uniq = 'item_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.section-heading.section-heading--center.'.$uniq.' .__title {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.section-heading.section-heading--center.'.$uniq.' .__title {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.section-heading.section-heading--center.'.$uniq.' .__title {font-size:'. $tsize .';}' : '';

        $item_css[] = $atts['tclr'] ? '.section-heading.'.$uniq.' .__title {color: '. esc_html($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['thclr'] ? '.section-heading.'.$uniq.' .__title span{color: '. esc_attr($atts['thclr']) .'; }' : '';
        $item_css[] = $atts['descclr'] ? '.section-heading.'.$uniq.' p{color: '. esc_attr($atts['descclr']) .'; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';
        $animaos = $atts['anim'] == 'yes' ? ' data-aos="'.$atts['aos'].'"' : '';
        if ($atts['title'] || $atts['thintitle'] || $atts['desc']) {
            $out .= '<div class="section-heading section-heading--center'. $uniq.$bgcss .'"'.$item_css.$animaos.'>';
            //section title
            if ($atts['title'] || $atts['thintitle']) {
                $out .= '<'. $htag .' class="__title">'. esc_html($atts['title']) .' <span>'. esc_html($atts['thintitle']) .'</span></'. $htag .'>';
            }
            // section description
            if ($atts['desc'] != '') {
                $out .= '<p>'. esc_html($atts['desc']) .'</p>';
            }

            $out .= '</div>';
        }

        return $out;
    }
    add_shortcode('agro_section_heading', 'agro_vc_section_heading');
}


/*******************************/
/* special_offer
/******************************/
if (!function_exists('agro_vc_special_offer')) {
    function agro_vc_special_offer($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        // data
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
        'title' => '',
        'img' => '',
        'anim' => '',
        'aos' => '',
        'delay' => '',
        'offset' => '',
        // background css
        'css' => '',
        ), $atts, 'agro_special_offer');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $uniq = 'item_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.special-offer.special-offer--style-1.'.$uniq.' .text {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.special-offer.special-offer--style-1.'.$uniq.' .text {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.special-offer.special-offer--style-1.'.$uniq.' .text {font-size:'. $tsize .';}' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';
        $animdelay = $atts['delay'] != '' ? ' data-aos-delay="'.$atts['delay'].'"' : '';
        $animoffset = $atts['offset'] != '' ? ' data-aos-offset="'.$atts['offset'].'"' : '';
        $animaos = $atts['anim'] == 'yes' ? ' data-aos="'.$atts['aos'].'"'.$animdelay.$animoffset : '';
        if ($atts['title']) {
            $out .= '<div class="special-offer special-offer--style-1'.$uniq.'" '.$animaos.$item_css.'>';
            $bgimg = wp_get_attachment_url($atts['img'], 'full');
            $out .= '<'.$htag.' class="text text-center lazy" data-src="'.esc_url($bgimg).'">'. esc_html($atts['title']) .'</'.$htag.'>';
            $out .= '</div>';
        }

        return $out;
    }
    add_shortcode('agro_special_offer', 'agro_vc_special_offer');
}


/*******************************/
/* special_section
/******************************/
if (!function_exists('agro_vc_special_section')) {
    function agro_vc_special_section($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        // data
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
        'bgimg' => '',
        'imgw1' => '',
        'imgh1' => '',
        'title' => '',
        'thintitle' => '',
        'link' => '',
        'img' => '',
        'imgw2' => '',
        'imgh2' => '',
        'desc' => '',
        'anim' => '',
        'aos' => '',
        'delay' => '',
        'offset' => '',
        'anim2' => '',
        'aos2' => '',
        'delay2' => '',
        'offset2' => '',
        'anim3' => '',
        'aos3' => '',
        'delay3' => '',
        'offset3' => '',
        // custom css
        'tclr' => '',
        'thclr' => '',
        'descclr' => '',
        'btnclr' => '',
        'btnhvrclr' => '',
        'btnbrd' => '',
        'btnhvrbrd' => '',
        'btnbg' => '',
        'btnhvrbg' => '',
        'btntype' => '',
        'btnstyle' => '',
        'btnsize' => '',
        // background css
        'css' => '',
        ), $atts, 'agro_special_section');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $btntype = $atts['btntype'] ? ' '.$atts['btntype'] : '';
        $btnstyle = $atts['btnstyle'] ? ' '.$atts['btnstyle'] : ' custom-btn--style-1';
        $btnsize = $atts['btnsize'] ? ' '.$atts['btnsize'] : ' custom-btn--medium';
        $uniq = 'item_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.section.'.$uniq.' .section-heading .__title {'. $google .'}' : '';


        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.section.'.$uniq.' .section-heading .__title {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.section.'.$uniq.' .section-heading .__title {font-size:'. $tsize .';}' : '';

        $item_css[] = $atts['tclr'] ? '.section.'.$uniq.' .section-heading .__title {color: '. esc_html($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['thclr'] ? '.section.'.$uniq.' .section-heading .__title span{color: '. esc_attr($atts['thclr']) .'; }' : '';
        $item_css[] = $atts['descclr'] ? '.section.'.$uniq.' p{color: '. esc_attr($atts['descclr']) .'; }' : '';

        $item_css[] = $atts['btnclr'] ? '.section.'.$uniq.' .custom-btn {color: '. esc_attr($atts['btnclr']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrclr'] ? '.section.'.$uniq.' .custom-btn:hover {color: '. esc_attr($atts['btnhvrclr']) .'!important; }' : '';
        $item_css[] = $atts['btnbrd'] ? '.section.'.$uniq.' .custom-btn {border-color: '. esc_attr($atts['btnbrd']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrbrd'] ? '.section.'.$uniq.' .custom-btn:hover {border-color: '. esc_attr($atts['btnhvrbrd']) .'!important; }' : '';
        $item_css[] = $atts['btnbg'] ? '.section.'.$uniq.' .custom-btn {background-color: '. esc_attr($atts['btnbg']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrbg'] ? '.section.'.$uniq.' .custom-btn:hover {background-color: '. esc_attr($atts['btnhvrbg']) .'!important; }' : '';

        $item_css[] = $atts['btntype'] == 'btn-rounded' ? '.section.'.$uniq.' .custom-btn.btn-rounded {border-radius: 10px!important; }' : '';
        $item_css[] = $atts['btntype'] == 'btn-square' ? '.section.'.$uniq.' .custom-btn.btn-square {border-radius: 0px !important; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        // slider button
        $link = !empty($atts['link']) ? $atts['link'] : '';
        $link = vc_build_link($link);
        $atitle = $link['title'];
        $href = $link['url'] != '' ? ' href="'. esc_url($link['url']) .'"' : ' href="#"';
        $target = $link['target'] != '' ? ' target="'. esc_attr($link['target']) .'"' : '';

        $animdelay = $atts['delay'] != '' ? ' data-aos-delay="'.$atts['delay'].'"' : '';
        $animoffset = $atts['offset'] != '' ? ' data-aos-offset="'.$atts['offset'].'"' : '';
        $animaos = $atts['anim'] == 'yes' ? ' data-aos="'.$atts['aos'].'"'.$animdelay.$animoffset : '';

        $animdelay2 = $atts['delay2'] != '' ? ' data-aos-delay="'.$atts['delay2'].'"' : '';
        $animoffset2 = $atts['offset2'] != '' ? ' data-aos-offset="'.$atts['offset2'].'"' : '';
        $animaos2 = $atts['anim2'] == 'yes' ? ' data-aos="'.$atts['aos2'].'"'.$animdelay.$animoffset : '';

        $animdelay3 = $atts['delay3'] != '' ? ' data-aos-delay="'.$atts['delay3'].'"' : '';
        $animoffset3 = $atts['offset3'] != '' ? ' data-aos-offset="'.$atts['offset3'].'"' : '';
        $animaos3 = $atts['anim2'] == 'yes' ? ' data-aos="'.$atts['aos3'].'"'.$animdelay.$animoffset : '';

        $blankimg = '';

        $out = '';

        $out .= '<div class="section'.$uniq.$bgcss.'"'.$item_css.'>';
        $out .= '<div class="d-none d-lg-block bg-absolute">';

        $out .= agro_img($atts['bgimg'], $blankimg, $imgclass='img-fluid', $atts['imgw1'], $atts['imgh1']);
        $out .= '</div>';

        $out .= '<div class="container">';
        $out .= '<div class="row align-items-center">';
        $out .= '<div class="col-12 col-lg-4">';
        $out .= '<div'.$animaos.'>';
        if ($atts['title'] || $atts['thintitle']) {
            $out .= '<div class="section-heading"><'.$htag.' class="__title">'. esc_html($atts['title']) .' <span>'. esc_html($atts['thintitle']) .'</span></'.$htag.'></div>';
        }
        if ($atitle != '') {
            $out .= '<p class="d-none d-sm-block"><a class="custom-btn'.$btnsize.$btnstyle.$btntype.'"'. $href . $target .'>'. esc_html($atitle) .'</a></p>';
        }
        $out .= '</div>';
        $out .= '</div>';

        $out .= '<div class="col-12 my-3 d-lg-none"></div>';

        $out .= '<div class="col-12 col-lg-4  text-center">';
        $out .= '<div'.$animaos2.'>';
        $out .= agro_img($atts['img'], $blankimg, $imgclass='img-fluid', $atts['imgw2'], $atts['imgh2']);
        $out .= '</div>';
        $out .= '</div>';

        $out .= '<div class="col-12 my-3 d-lg-none"></div>';

        $out .= '<div class="col-12 col-lg-4">';
        $out .= '<div'.$animaos3.'>';
        $out .= wpautop($atts['desc']);

        if ($atitle != '') {
            $out .= '<p class="d-sm-none"><a class="custom-btn'.$btnsize.$btnstyle.$btntype.'"'. $href . $target .'>'. esc_html($atitle) .'</a></p>';
        }
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';


        return $out;
    }
    add_shortcode('agro_special_section', 'agro_vc_special_section');
}

/*******************************/
/* counter
/******************************/
if (!function_exists('agro_vc_counter')) {
    function agro_vc_counter($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        'style' => '',
        'stitle' => '',
        'sthintitle' => '',
        'desc' => '',
        'hidept' => '',
        // loop
        'loop' => '',
        'title' => '',
        'number' => '',
        'afternumber' => '',
        'decimal' => '',
        'xl' => '',
        'lg' => '',
        'md' => '',
        'sm' => '',
        'img' => '',
        'imgw' => '',
        'imgh' => '',
        'anim' => '',
        'aos' => '',
        'delay' => '',
        'duration' => '',
        'offset' => '',
        // color
        'tclr' => '',
        'thclr' => '',
        'descclr' => '',
        'numclr' => '',
        'afternumclr' => '',
        'ntclr' => '',
        // background css
        'css' => '',
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
        ), $atts, 'agro_counter');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $uniq = 'counter_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.section.'.$uniq.' .section-heading .__title {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.section.'.$uniq.' .section-heading .__title {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.section.'.$uniq.' .section-heading .__title {font-size:'. $tsize .';}' : '';

        $item_css[] = $atts['tclr'] ? '.section.'.$uniq.' .section-heading .__title {color: '. esc_html($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['thclr'] ? '.section.'.$uniq.' .section-heading .__title span{color: '. esc_attr($atts['thclr']) .'; }' : '';
        $item_css[] = $atts['descclr'] ? '.section.'.$uniq.' .section-heading p{color: '. esc_attr($atts['descclr']) .'; }' : '';
        $item_css[] = $atts['numclr'] ? '.section.'.$uniq.' .counter .__item .__count{color: '. esc_attr($atts['numclr']) .'; }' : '';
        $item_css[] = $atts['afternumclr'] ? '.section.'.$uniq.' .counter .__item .after-number{color: '. esc_attr($atts['afternumclr']) .'; }' : '';
        $item_css[] = $atts['ntclr'] ? '.section.'.$uniq.' .counter .__item .__title{color: '. esc_attr($atts['ntclr']) .'; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $decimal = $atts['decimal'] == 'yes' ? ' data-deci="true"' : '';

        $out = '';
        if ($atts['style'] == '1') {
            $secpt = $atts['hidept'] != 'yes' ? '' : ' section--no-pt';
            $out .= '<div class="section'.$secpt.$uniq.$bgcss.'">';
            $out .= '<div class="container">';
            if ($atts['stitle'] || $atts['sthintitle'] || $atts['desc']) {
                $out .= '<div class="section-heading section-heading--center" data-aos="fade">';
                if ($atts['stitle'] || $atts['sthintitle']) {
                    $out .= '<'.$htag.' class="__title">'. esc_html($atts['stitle']) .' <span>'. esc_html($atts['sthintitle']) .'</span></'.$htag.'>';
                }
                if ($atts['desc']) {
                    $out .= '<p>'. esc_html($atts['desc']) .'</p>';
                }

                $out .= '</div>';
            }

            $out .= '<div class="counter">';
            $out .= '<div class="__inner">';
            $out .= '<div class="row justify-content-sm-center justify-content-lg-around">';

            $loop = (array) vc_param_group_parse_atts($atts['loop']);
            foreach ($loop as $item) {
                $delay = isset($item['delay']) != '' ? ' data-aos-delay="'.$item['delay'].'"' : '';
                $duration = isset($item['duration']) != '' ? ' data-aos-duration="'.$item['duration'].'"' : '';
                $offset = isset($item['offset']) != '' ? ' data-aos-offset="'.$item['offset'].'"' : '';
                $animaos = isset($item['anim']) == 'yes' ? ' data-aos="'.$item['aos'].'"'.$delay.$duration.$offset : '';
                $hasafter = isset($item['afternumber']) != '' ? ' has-after-number' : '';

                $out .= '<div class="col-12 '.$atts['sm'].' '.$atts['md'].' '.$atts['lg'].' '.$atts['xl'].'">';
                $out .= '<div class="__item"'.$animaos .'>';
                $out .= '<div class="d-table">';

                $out .= '<div class="d-table-cell align-middle">';
                if (isset($item['img']) != '') {
                    $out .= '<i class="__ico">';
                    $blankimg = '';
                    $out .= agro_img($item['img'], $blankimg, $imgclass='img-fluid', $atts['imgw'], $atts['imgh']);
                    $out .= '</i>';
                }
                $out .= '</div>';

                $out .= '<div class="d-table-cell align-middle">';
                if (isset($item['number']) != '') {
                    $out .= '<p class="__count js-count'.$hasafter.'" data-from="0" data-to="'. esc_html($item['number']) .'"'.$decimal.'>'. esc_html($item['number']) .'</p>';
                    if (isset($item['afternumber']) != '') {
                        $out .= '<span class="after-number"> '.$item['afternumber'] .'</span>';
                    }
                }
                if (isset($item['title']) != '') {
                    $out .= '<p class="__title">'. esc_html($item['title']) .'</p>';
                }
                $out .= '</div>';

                $out .= '</div>';
                $out .= '</div>';
                $out .= '</div>';
            }

            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';
        } else {
            $out .= '<div class="section section--gutter section--base-bg'.$uniq.$bgcss.'"'.$item_css.'>
                <div class="container">
                    <div class="counter">
                        <div class="__inner">
                            <div class="row justify-content-sm-center">';


            $loop = (array) vc_param_group_parse_atts($atts['loop']);
            foreach ($loop as $item) {
                $animdelay = isset($item['delay']) != '' ? ' data-aos-delay="'.$atts['delay'].'"' : '';
                $animoffset = isset($item['offset']) != '' ? ' data-aos-offset="'.$atts['offset'].'"' : '';
                $animaos = isset($item['anim']) == 'yes' ? ' data-aos="'.$atts['aos'].'"'.$animdelay.$animoffset : '';
                $hasafter = isset($item['afternumber']) != '' ? ' has-after-number' : '';

                $out .= '<div class="col-12 '.$atts['sm'].' '.$atts['md'].' '.$atts['lg'].' '.$atts['xl'].'">';
                $out .= '<div class="__item"'.$animaos.'>';
                $out .= '<div class="d-table">';
                $out .= '<div class="d-table-cell align-middle">';
                if (isset($item['img']) != '') {
                    $out .= '<i class="__ico">';
                    $blankimg = '';
                    $out .= agro_img($item['img'], $blankimg, $imgclass='img-fluid', $atts['imgw'], $atts['imgh']);
                    $out .= '</i>';
                }
                $out .= '</div>';

                $out .= '<div class="d-table-cell align-middle">';
                if (isset($item['number']) != '') {
                    $out .= '<p class="__count js-count'.$hasafter.'" data-from="0" data-to="'. esc_html($item['number']) .'"'.$decimal.'>'. esc_html($item['number']) .'</p>';
                    if (isset($item['afternumber']) != '') {
                        $out .= ' <span class="after-number">'.$item['afternumber'] .'</span>';
                    }
                }
                if (isset($item['title']) != '') {
                    $out .= '<p class="__title">'. esc_html($item['title']) .'</p>';
                }
                $out .= '</div>';
                $out .= '</div>';
                $out .= '</div>';
                $out .= '</div>';
            }

            $out .= '</div>
                    </div>
                </div>
            </div>
        </div>';
        }

        return $out;
    }
    add_shortcode('agro_counter', 'agro_vc_counter');
}


/*******************************/
/* timeline
/******************************/
if (!function_exists('agro_vc_timeline')) {
    function agro_vc_timeline($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
        'stitle' => '',
        'sthintitle' => '',
        'sdesc' => '',
        // loop
        'loop' => '',
        'year' => '',
        'title' => '',
        'desc' => '',
        'md' => '',
        // custom css
        'tclr' => '',
        'thclr' => '',
        'descclr' => '',
        'yearclr' => '',
        'timetclr' => '',
        'timedescclr' => '',
        'timelineclr' => '',
        // background css
        'css' => '',
        ), $atts, 'agro_timeline');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $uniq = 'section_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.section.'.$uniq.' .section-heading .__title {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.section.'.$uniq.' .section-heading .__title {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.section.'.$uniq.' .section-heading .__title {font-size:'. $tsize .';}' : '';

        $item_css[] = $atts['tclr'] ? '.section.'.$uniq.' .section-heading .__title {color: '. esc_html($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['thclr'] ? '.section.'.$uniq.' .section-heading .__title span{color: '. esc_attr($atts['thclr']) .'; }' : '';
        $item_css[] = $atts['descclr'] ? '.section.'.$uniq.' p{color: '. esc_attr($atts['descclr']) .'; }' : '';
        $item_css[] = $atts['yearclr'] ? '.section.'.$uniq.' .timeline .__year{color: '. esc_attr($atts['yearclr']) .'; }' : '';
        $item_css[] = $atts['timetclr'] ? '.section.'.$uniq.' .timeline .__title{color: '. esc_attr($atts['timetclr']) .'; }' : '';
        $item_css[] = $atts['timedescclr'] ? '.section.'.$uniq.' .timeline p{color: '. esc_attr($atts['timedescclr']) .'; }' : '';
        $item_css[] = $atts['timelineclr'] ? '.section.'.$uniq.' .timeline .__item:before{border-color: '. esc_attr($atts['timelineclr']) .'; }' : '';
        $item_css[] = $atts['timelineclr'] ? '.section.'.$uniq.' .timeline .__ico{border-color: '. esc_attr($atts['timelineclr']) .'; }' : '';
        $item_css[] = $atts['timelineclr'] ? '.section.'.$uniq.' .timeline .__ico:before{background-color: '. esc_attr($atts['timelineclr']) .'; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';

        $out .= '<div class="section section--gutter section--base-bg section--custom-02'. $uniq.$bgcss .'"'.$item_css.'>';
        $out .= '<div class="container">';
        if ($atts['stitle'] || $atts['sthintitle'] || $atts['sdesc']) {
            $out .= '<div class="section-heading" data-aos="fade">';
            if ($atts['stitle'] || $atts['sthintitle']) {
                $out .= '<'.$htag.' class="__title">'. esc_html($atts['stitle']) .' <span>'. esc_html($atts['sthintitle']) .'</span></'.$htag.'>';
            }
            if ($atts['sdesc']) {
                $out .= '<p>'. esc_html($atts['sdesc']) .'</p>';
            }
            $out .= '</div>';
        }

        $out .= '<div class="timeline">';
        $out .= '<div class="__inner">';
        $out .= '<div class="row">';

        $loop = (array) vc_param_group_parse_atts($atts['loop']);
        $md = $atts['md'] ? $atts['md'] : 'col-md-3';
        foreach ($loop as $item) {
            $out .= '<div class="col-12 '.$md.'">';
            $out .= '<div class="__item">';
            $out .= '<i class="__ico"></i>';

            $out .= '<div class="row">';
            $out .= '<div class="col-lg-11 col-xl-9">';

            if (isset($item['year']) != '') {
                $out .= '<p class="__year">'. esc_html($item['year']) .'</p>';
            }
            if (isset($item['title']) != '') {
                $out .= '<h5 class="__title">'. esc_html($item['title']) .'</h5>';
            }
            if (isset($item['desc']) != '') {
                $out .= '<p>'. esc_html($item['desc']) .'</p>';
            }
            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';
        }

        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_timeline', 'agro_vc_timeline');
}

/*******************************/
/* team
/******************************/
if (!function_exists('agro_vc_team')) {
    function agro_vc_team($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
        'stitle' => '',
        'sthintitle' => '',
        'sdesc' => '',
        'img_url' => '',
        // loop
        'loop' => '',
        'img' => '',
        'imgw' => '',
        'imgh' => '',
        'name' => '',
        'job' => '',
        'md' => '',
        'lg' => '',
        // custom css
        'tclr' => '',
        'thclr' => '',
        'descclr' => '',
        'nameclr' => '',
        'jobclr' => '',
        'contentclr' => '',
        // background css
        'css' => '',
        ), $atts, 'agro_team');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $uniq = 'section_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.section.'.$uniq.' .section-heading .__title {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.section.'.$uniq.' .section-heading .__title {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.section.'.$uniq.' .section-heading .__title {font-size:'. $tsize .';}' : '';

        $item_css[] = $atts['tclr'] ? '.section.'.$uniq.' .section-heading .__title {color: '. esc_html($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['thclr'] ? '.section.'.$uniq.' .section-heading .__title span{color: '. esc_attr($atts['thclr']) .'; }' : '';
        $item_css[] = $atts['descclr'] ? '.section.'.$uniq.' p{color: '. esc_attr($atts['descclr']) .'; }' : '';
        $item_css[] = $atts['nameclr'] ? '.section.'.$uniq.' .team .__item .__title{color: '. esc_attr($atts['nameclr']) .'; }' : '';
        $item_css[] = $atts['jobclr'] ? '.section.'.$uniq.' .team .__item .__content{color: '. esc_attr($atts['jobclr']) .'; }' : '';
        $item_css[] = $atts['contentclr'] ? '.section.'.$uniq.' .team .__item .__content{background-color: '. esc_attr($atts['contentclr']) .'; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';
        $out .= '<div class="section section--no-pb section--custom-03'. $uniq.$bgcss .'"'.$item_css.'>';
        $out .= '<div class="container">';
        if ($atts['stitle'] || $atts['sthintitle'] || $atts['sdesc']) {
            $out .= '<div class="section-heading section-heading--center" data-aos="fade">';
            if ($atts['stitle'] || $atts['sthintitle']) {
                $out .= '<'.$htag.' class="__title">'. esc_html($atts['stitle']) .' <span>'. esc_html($atts['sthintitle']) .'</span></'.$htag.'>';
            }
            if ($atts['sdesc']) {
                $out .= '<p>'. esc_html($atts['sdesc']) .'</p>';
            }
            $out .= '</div>';
        }

        $out .= '<div class="team">';
        $out .= '<div class="__inner">';
        $out .= '<div class="row">';

        $loop = (array) vc_param_group_parse_atts($atts['loop']);
        $md = $atts['md'] != '' ? $atts['md'] : 'col-md-6';
        $lg = $atts['lg'] != '' ? $atts['lg'] : 'col-lg-4';
        $animdelay = isset($item['delay']) != '' ? ' data-aos-delay="'.$atts['delay'].'"' : '';
        $animoffset = isset($item['offset']) != '' ? ' data-aos-offset="'.$atts['offset'].'"' : '';
        $animaos = isset($item['anim']) == 'yes' ? ' data-aos="'.$atts['aos'].'"'.$animdelay.$animoffset : '';
        foreach ($loop as $item) {
            if (isset($item['img']) != '') {
                $out .= '<div class="col-12 '.$md.' '.$lg.'">';
                $out .= '<div class="__item"'.$animaos.'>';
                $out .= '<figure class="__image">';
                $blankimg = '';
                if(isset($item['img_url']) != ''){
                    $out .= '<a href="'.esc_attr($item['img_url']).'" target="_blank">';
                }
                $out .= agro_img($item['img'], $blankimg, $imgclass='img-fluid', $atts['imgw'], $atts['imgh']);
                if(isset($item['img_url']) != ''){
                $out .= '<a>';
                }
                $out .= '</figure>';

                $out .= '<div class="__content">';
                if (isset($item['name']) != '') {
                    $out .= '<h5 class="__title">'. esc_html($item['name']) .'</h5>';
                }
                if (isset($item['job']) != '') {
                    $out .= '<span>'. esc_html($item['job']) .'</span>';
                }

                $out .= '</div>';
                $out .= '</div>';
                $out .= '</div>';
            }
        }

        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_team', 'agro_vc_team');
}


/*******************************/
/* section_mapmarker
/******************************/
if (!function_exists('agro_vc_section_mapmarker')) {
    function agro_vc_section_mapmarker($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        // data
        'bgimg' => '',
        'imgw' => '',
        'imgh' => '',
        'title' => '',
        'thintitle' => '',
        'desc' => '',
        'anim' => '',
        'aos' => '',
        'delay' => '',
        'offset' => '',
        'anim2' => '',
        'aos2' => '',
        'duration2' => '',
        'offset2' => '',
        // custom css
        'tclr' => '',
        'thclr' => '',
        'descclr' => '',
        // background css
        'css' => '',
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
        ), $atts, 'agro_section_mapmarker');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $uniq = 'section_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.section.'.$uniq.' .section-heading .__title {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.section.'.$uniq.' .section-heading .__title {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.section.'.$uniq.' .section-heading .__title {font-size:'. $tsize .';}' : '';

        $item_css[] = $atts['tclr'] ? '.section.'.$uniq.' .section-heading .__title {color: '. esc_html($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['thclr'] ? '.section.'.$uniq.' .section-heading .__title span{color: '. esc_attr($atts['thclr']) .'; }' : '';
        $item_css[] = $atts['descclr'] ? '.section.'.$uniq.' p{color: '. esc_attr($atts['descclr']) .'; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $animdelay = $atts['delay'] != '' ? ' data-aos-delay="'.$atts['delay'].'"' : '';
        $animoffset = $atts['offset'] != '' ? ' data-aos-offset="'.$atts['offset'].'"' : '';
        $animaos = $atts['anim'] == 'yes' ? ' data-aos="'.$atts['aos'].'"'.$animdelay.$animoffset : '';

        $animduration2 = $atts['duration2'] != '' ? ' data-aos-duration="'.$atts['duration2'].'"' : '';
        $animoffset2 = $atts['offset2'] != '' ? ' data-aos-offset="'.$atts['offset2'].'"' : '';
        $animaos2 = $atts['anim2'] == 'yes' ? ' data-aos="'.$atts['aos2'].'"'.$animduration2.$animoffset : '';

        $blankimg = '';

        $out = '';

        $out .= '<div class="section'.$uniq.$bgcss.'"'.$item_css.'>';
        $out .= '<div class="container">';
        if ($atts['title'] || $atts['thintitle'] || $atts['desc']) {
            $out .= '<div class="section-heading section-heading--center"'.$animaos.'>';
            $out .= '<'.$htag.' class="__title">'. esc_html($atts['title']) .' <span>'. esc_html($atts['thintitle']) .'</span></'.$htag.'>';

            $out .= '<p>'. esc_html($atts['desc']) .'</p>';
            $out .= '</div>';
        }
        if ($atts['bgimg']) {
            $out .= '<div'.$animaos2.'>';
            $out .= agro_img($atts['bgimg'], $blankimg, $imgclass='img-fluid', $atts['imgw'], $atts['imgh']);
            $out .= '</div>';
        }
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_section_mapmarker', 'agro_vc_section_mapmarker');
}


/*******************************/
/* product
/******************************/
if (!function_exists('agro_vc_product')) {
    function agro_vc_product($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        'fullwidth' => '',
        'sxl' => '',
        'smd' => '',
        'stitle' => '',
        'sthintitle' => '',
        'sdesc' => '',
        'link' => '',
        // product
        'loop' => '',
        'title' => '',
        'plink' => '',
        'tag' => '',
        'xl' => '',
        'xl_off' => '',
        'md' => '',
        'img' => '',
        'imgw' => '',
        'imgh' => '',
        //custom css
        'overlay' => '',
        'tclr' => '',
        'thvrclr' => '',
        'sectclr' => '',
        'dclr' => '',
        'btnclr' => '',
        'btnhvrclr' => '',
        'btnbrd' => '',
        'btnhvrbrd' => '',
        'btnbg' => '',
        'btnhvrbg' => '',
        'btntype' => '',
        'btnstyle' => '',
        'btnsize' => '',
        // background css
        'css' => '',
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
        ), $atts, 'agro_product');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);
        $btntype = $atts['btntype'] ? ' '.$atts['btntype'] : '';
        $btnstyle = $atts['btnstyle'] ? ' '.$atts['btnstyle'] : ' custom-btn--style-1';
        $btnsize = $atts['btnsize'] ? ' '.$atts['btnsize'] : ' custom-btn--medium';
        $fullwidth  = $atts['fullwidth'] == 'yes' ? '-off' : '';
        $uniq = 'item_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.section.'.$uniq.' .product-preview .__item .__intro-text h2 {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.section.'.$uniq.' .product-preview .__item .__intro-text h2 {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.section.'.$uniq.' .product-preview .__item .__intro-text h2 {font-size:'. $tsize .';}' : '';

        $item_css[] = $atts['overlay'] ? '.product-preview .__item:hover .__image+.__content {background-color: '. esc_attr($atts['overlay']) .'; }' : '';
        $item_css[] = $atts['tclr'] ? '.product-preview .__item .__title {color: '. esc_attr($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['thvrclr'] ? '.product-preview .__item:hover .__title {color: '. esc_attr($atts['thvrclr']) .'; }' : '';
        $item_css[] = $atts['sectclr'] ? '.product-preview .__item .__intro-text h2 {color: '. esc_attr($atts['sectclr']) .'; }' : '';
        $item_css[] = $atts['dclr'] ? '.product-preview .__item .__intro-text p {color: '. esc_attr($atts['dclr']) .'; }' : '';

        $item_css[] = $atts['btnclr'] ? '.'.$uniq.' .product-preview.product-preview--style-1 .custom-btn {color: '. esc_attr($atts['btnclr']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrclr'] ? '.'.$uniq.' .product-preview.product-preview--style-1 .custom-btn:hover {color: '. esc_attr($atts['btnhvrclr']) .'!important; }' : '';
        $item_css[] = $atts['btnbrd'] ? '.'.$uniq.' .product-preview.product-preview--style-1 .custom-btn {border-color: '. esc_attr($atts['btnbrd']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrbrd'] ? '.'.$uniq.' .product-preview.product-preview--style-1 .custom-btn:hover {border-color: '. esc_attr($atts['btnhvrbrd']) .'!important; }' : '';
        $item_css[] = $atts['btnbg'] ? '.'.$uniq.' .product-preview.product-preview--style-1 .custom-btn {background-color: '. esc_attr($atts['btnbg']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrbg'] ? '.'.$uniq.' .product-preview.product-preview--style-1 .custom-btn:hover {background-color: '. esc_attr($atts['btnhvrbg']) .'!important; }' : '';

        $item_css[] = $atts['btntype'] == 'btn-rounded' ? '.'.$uniq.' .product-preview.product-preview--style-1 .custom-btn.btn-rounded {border-radius: 10px; }' : '';
        $item_css[] = $atts['btntype'] == 'btn-square' ? '.'.$uniq.' .product-preview.product-preview--style-1 .custom-btn.btn-square {border-radius: 0px !important; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';

        $out .= '<div class="section section--no-pt section--no-pb section--gutter'.$fullwidth.''.$uniq.$bgcss.'"'.$item_css.'>
            <div class="container-fluid px-md-0">
                <div class="product-preview product-preview--style-1">
                    <div class="__inner">
                        <div class="row">';

                        $out .= '<div class="col-12 '.$atts['smd'].' '.$atts['sxl'].'">';
                            $out .= '<div class="__item">';
                                $out .= '<div class="__intro-text">';
                                    $out .= '<div class="row">';
                                        $out .= '<div class="col-md-11">';
                                            $out .= '<'.$htag.'>'. esc_html($atts['stitle']) .' <span>'. esc_html($atts['sthintitle']) .'</span></'.$htag.'>';

                                            $out .= '<p>'. esc_html($atts['sdesc']) .'</p>';

                                            $link = !empty($atts['link']) ? $atts['link'] : '';
                                            $link = vc_build_link($link);
                                            $atitle = $link['title'];
                                            $href = $link['url'] != '' ? ' href="'. esc_url($link['url']) .'"' : ' href="#"';
                                            $target = $link['target'] != '' ? ' target="'. esc_attr($link['target']) .'"' : '';

                                            if ($atitle != '') {
                                                $out .= '<p><a class="custom-btn'.$btnsize.$btnstyle.$btntype.'"'. $href . $target .'>'. esc_html($atitle) .'</a></p>';
                                            }
                                        $out .= '</div>';
                                    $out .= '</div>';
                                $out .= '</div>';
                            $out .= '</div>';
                        $out .= '</div>';

                    $loop = (array) vc_param_group_parse_atts($atts['loop']);
                    foreach ($loop as $item) {
                        $xl = isset($item['xl']) != '' ? $item['xl'] : 'col-xl-3';
                        $xl_off = isset($item['xl_off']) != '' ? $item['xl_off'] : '';
                        $md = isset($item['md']) != '' ? $item['md'] : 'col-md-4';
                        $out .= '<div class="col-12 '.$md.' '.$xl.' '.$xl_off.'">';
                            $out .= '<div class="__item">';

                                if (isset($item['img']) != '') {
                                    $out .= '<figure class="__image">';
                                    $blankimg = '';
                                    $out .= agro_img($item['img'], $blankimg, $imgclass='img-fluid', $atts['imgw'], $atts['imgh']);
                                    $out .= '</figure>';
                                }

                                if (isset($item['title']) != '') {
                                    $tag = isset($item['tag']) != '' ? $item['tag'] : 'h3';
                                    $out .= '<div class="__content">';
                                    $out .= '<'.$tag.' class="__title">'. $item['title'] .'</'.$tag.'>';
                                    $out .= '</div>';
                                }

                                // PRODUCT LİNK
                                $plink = !empty($item['plink']) ? $item['plink'] : '';
                                $plink = vc_build_link($plink);
                                $ptitle = $plink['title'];
                                $phref = $plink['url'] != '' ? ' href="'. esc_url($plink['url']) .'"' : ' href="#"';
                                $ptarget = $plink['target'] != '' ? ' target="'. esc_attr($plink['target']) .'"' : '';

                                if ($phref != '') {
                                    $out .= '<a class="__link"'. $phref . $ptarget .'></a>';
                                }
                            $out .= '</div>';
                        $out .= '</div>';
                    }

            $out .= '</div>
                    </div>
                </div>
            </div>
        </div>';

        return $out;
    }
    add_shortcode('agro_product', 'agro_vc_product');
}


/*******************************/
/* product list
/******************************/
if (!function_exists('agro_vc_product_list')) {
    function agro_vc_product_list($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        // product
        'loop' => '',
        'title' => '',
        'desc' => '',
        'plink' => '',
        'img' => '',
        'imgw' => '',
        'imgh' => '',
        'bgimg' => '',
        'bgimgw' => '',
        'bgimgh' => '',
        //custom css
        'tclr' => '',
        'descclr' => '',
        'btnclr' => '',
        'btnhvrclr' => '',
        'btnbrd' => '',
        'btnhvrbrd' => '',
        'btnbg' => '',
        'btnhvrbg' => '',
        'btntype' => '',
        'btnstyle' => '',
        'btnsize' => '',
        // background css
        'css' => '',
        ), $atts, 'agro_product_list');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $btntype = $atts['btntype'] ? ' '.$atts['btntype'] : '';
        $btnstyle = $atts['btnstyle'] ? ' '.$atts['btnstyle'] : ' custom-btn--style-1';
        $btnsize = $atts['btnsize'] ? ' '.$atts['btnsize'] : ' custom-btn--medium';
        $uniq = 'section_'.uniqid();
        $item_css = array();
        $item_css[] = $atts['tclr'] ? '.section.'.$uniq.' .products-list .__content .__title {color: '. esc_html($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['descclr'] ? '.section.'.$uniq.' .products-list .__content p{color: '. esc_attr($atts['descclr']) .'; }' : '';

        $item_css[] = $atts['btnclr'] ? '.section.'.$uniq.' .custom-btn {color: '. esc_attr($atts['btnclr']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrclr'] ? '.section.'.$uniq.' .custom-btn:hover {color: '. esc_attr($atts['btnhvrclr']) .'!important; }' : '';
        $item_css[] = $atts['btnbrd'] ? '.section.'.$uniq.' .custom-btn {border-color: '. esc_attr($atts['btnbrd']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrbrd'] ? '.section.'.$uniq.' .custom-btn:hover {border-color: '. esc_attr($atts['btnhvrbrd']) .'!important; }' : '';
        $item_css[] = $atts['btnbg'] ? '.section.'.$uniq.' .custom-btn {background-color: '. esc_attr($atts['btnbg']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrbg'] ? '.section.'.$uniq.' .custom-btn:hover {background-color: '. esc_attr($atts['btnhvrbg']) .'!important; }' : '';

        $item_css[] = $atts['btntype'] == 'btn-rounded' ? '.section.'.$uniq.' .custom-btn.btn-rounded {border-radius: 10px!important; }' : '';
        $item_css[] = $atts['btntype'] == 'btn-square' ? '.section.'.$uniq.' .custom-btn.btn-square {border-radius: 0px !important; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';

        $out .= '<div class="section section--no-pt'.$uniq.$bgcss.'"'.$item_css.'>';
        $out .= '<div class="container-fluid">';
        $out .= '<div class="products-list">';

        $loop = (array) vc_param_group_parse_atts($atts['loop']);
        foreach ($loop as $item) {
            $blankimg = '';
            if (isset($item['img']) != '') {
                $out .= '<div class="__item">';
                if (isset($item['bgimg']) != '') {
                    $out .= '<div class="__bg">';
                    $out .= '<div data-jarallax-element="-140" data-speed="0.4">';
                    $out .= agro_img($item['bgimg'], $blankimg, $imgclass='img-fluid', $atts['bgimgw'], $atts['bgimgh']);
                    $out .= '</div>';
                    $out .= '</div>';
                }

                $out .= '<div class="__inner">';
                $out .= '<div class="row align-items-lg-center justify-content-sm-center">';
                $out .= '<div class="col-12 col-md-9 col-lg-6">';
                $out .= '<div class="__image">';
                $out .= agro_img($item['img'], $blankimg, $imgclass='img-fluid', $atts['imgw'], $atts['imgh']);
                $out .= '</div>';
                $out .= '</div>';

                $out .= '<div class="col-12 my-3 d-lg-none"></div>';

                $out .= '<div class="col-12 col-md-9 col-lg-6">';
                $out .= '<div class="__content">';
                if (isset($item['title']) != '') {
                    $out .= '<h3 class="__title">'.$item['title'].'</h3>';
                }
                if (isset($item['desc']) != '') {
                    $out .= '<p>'.$item['desc'].'</p>';
                }
                // PRODUCT LİNK
                $plink = !empty($item['plink']) ? $item['plink'] : '';
                $plink = vc_build_link($plink);
                $ptitle = $plink['title'];
                $phref = $plink['url'] != '' ? ' href="'. esc_url($plink['url']) .'"' : ' href="#"';
                $ptarget = $plink['target'] != '' ? ' target="'. esc_attr($plink['target']) .'"' : '';

                if ($phref != '') {
                    $out .= '<p><a class="custom-btn'.$btnsize.$btnstyle.$btntype.'"'. $phref . $ptarget .'>'. esc_html($ptitle) .'</a><p>';
                }

                $out .= '</div>';
                $out .= '</div>';
                $out .= '</div>';
                $out .= '</div>';
                $out .= '</div>';
            }
        }

        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_product_list', 'agro_vc_product_list');
}

/*******************************/
/* product promo
/******************************/
if (!function_exists('agro_vc_product_promo')) {
    function agro_vc_product_promo($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        // product
        'loop' => '',
        'title' => '',
        'thintitle' => '',
        'h2y' => '',
        'tag' => '',
        'sm' => '',
        'md' => '',
        'img' => '',
        'imgw' => '',
        'imgh' => '',
        'plink' => '',
        'tclr' => '',
        'thclr' => '',
        // background css
        'css' => '',
    ), $atts, 'agro_product_promo');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $uniq = 'section_'.uniqid();
        $item_css = array();
        $item_css[] = $atts['tclr'] ? '.section.'.$uniq.' .product-promo .__content .__title {color: '. esc_html($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['thclr'] ? '.section.'.$uniq.' .product-promo .__content .__title span{color: '. esc_attr($atts['thclr']) .'; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $sm = $atts['sm'] ? $atts['sm'] : 'col-sm-6';
        $md = $atts['md'] ? $atts['md'] : 'col-md-4';

        $out = '';

        $out .= '<div class="section'. $uniq.$bgcss .'"'.$item_css.'>
            <div class="container-fluid">


                <div class="product-promo product-promo--style-2">
                    <div class="__inner">
                        <div class="row no-gutters  js-isotope" data-isotope-options=\'{"itemSelector": ".js-isotope__item","transitionDuration": "0.8s","percentPosition": "true",	"masonry": { "columnWidth": ".js-isotope__sizer" }}\'>
                            <div class="col-12 '.$sm.' '.$md.'  js-isotope__sizer"></div>';

        $loop = (array) vc_param_group_parse_atts($atts['loop']);
        foreach ($loop as $item) {
            $out .= '<div class="col-12 '.$sm.' '.$md.' js-isotope__item">';
            $h2y = isset($item['h2y']) == 'yes' ?  'data-y="2"' : '';
            $out .= '<div class="__item" '.$h2y.'>';
            if (isset($item['img']) != '') {
                $out .= '<figure class="__image">';
                $blankimg = get_theme_file_uri().'/images/blank.gif';
                $out .= agro_img($item['img'], $blankimg='', $imgclass='img-fluid', $atts['imgw'], $atts['imgh']);
                $out .= '</figure>';
            }

            if (isset($item['title']) != '' || isset($item['thintitle']) != '') {
                $tag = isset($item['tag']) != '' ? $item['tag'] : 'h3';
                $out .= '<div class="__content">';
                $out .= '<'.$tag.' class="__title">'. $item['title'] .' <br><span>'. $item['thintitle'] .'</span></'.$tag.'>';
                $out .= '</div>';
            }

            // PRODUCT LİNK
            $plink = !empty($item['plink']) ? $item['plink'] : '';
            $plink = vc_build_link($plink);
            $ptitle = $plink['title'];
            $phref = $plink['url'] != '' ? ' href="'. esc_url($plink['url']) .'"' : ' href="#"';
            $ptarget = $plink['target'] != '' ? ' target="'. esc_attr($plink['target']) .'"' : '';

            if ($ptitle != '') {
                $out .= '<a class="__link"'. $phref . $ptarget .'></a>';
            }
            $out .= '</div>';
            $out .= '</div>';
        }


        $out .= '</div>
                    </div>
                </div>
            </div>
        </div>';

        return $out;
    }
    add_shortcode('agro_product_promo', 'agro_vc_product_promo');
}


/*******************************/
/* review
/******************************/
if (!function_exists('agro_vc_review')) {
    function agro_vc_review($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        'style' => '',
        'bgimg' => '',
        'title' => '',
        'thintitle' => '',
        'desc' => '',
        // product
        'loop' => '',
        'quote' => '',
        'name' => '',
        'job' => '',
        'img' => '',
        'imgw' => '',
        'imgh' => '',
        //color
        'tclr' => '',
        'thclr' => '',
        'descclr' => '',
        'quoteclr' => '',
        'iconclr' => '',
        'authorclr' => '',
        'jobsclr' => '',
        'dotsclr' => '',
        'dotsactclr' => '',
        // background css
        'css' => '',
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
        'speed' => '',
        'autoplay' => '',
        'dots' => '',
        'pauseonhover' => '',
        ), $atts, 'agro_review');

        $speed = $atts['speed'] ? $atts['speed'] : 1000;
        $autoplay = $atts['autoplay'] == 'yes' ? 'true' : 'false';
        $dots = $atts['dots'] == 'yes' ? 'true' : 'false';
        $pauseOnHover = $atts['pauseonhover'] == 'yes' ? 'true' : 'false';

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $uniq = 'review_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.section--review.'.$uniq.' .section-heading .__title {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.section--review.'.$uniq.' .section-heading .__title {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.section--review.'.$uniq.' .section-heading .__title {font-size:'. $tsize .';}' : '';

        $item_css[] = $atts['tclr'] ? '.section--review.'.$uniq.' .section-heading .__title {color: '. esc_html($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['thclr'] ? '.section--review.'.$uniq.' .section-heading .__title span{color: '. esc_attr($atts['thclr']) .'; }' : '';
        $item_css[] = $atts['descclr'] ? '.section--review.'.$uniq.' .section-heading p{color: '. esc_attr($atts['descclr']) .'; }' : '';
        $item_css[] = $atts['quoteclr'] ? '.section--review.'.$uniq.' .review .review__item__text p{color: '. esc_attr($atts['quoteclr']) .'; }' : '';
        $item_css[] = $atts['iconclr'] ? '.section--review.'.$uniq.' .review:before{color: '. esc_attr($atts['iconclr']) .'; }' : '';
        $item_css[] = $atts['authorclr'] ? '.section--review.'.$uniq.' .review .review__item__author-name{color: '. esc_attr($atts['authorclr']) .'; }' : '';
        $item_css[] = $atts['jobsclr'] ? '.section--review.'.$uniq.' .review .review__item__author-position{color: '. esc_attr($atts['jobsclr']) .'; }' : '';
        $item_css[] = $atts['dotsclr'] ? '.section--review.'.$uniq.' .review .slick-dots button{border-color: '. esc_attr($atts['dotsclr']) .'; }' : '';
        $item_css[] = $atts['dotsactclr'] ? '.section--review.'.$uniq.' .review .slick-dots li.slick-active button{background-color: '. esc_attr($atts['dotsactclr']) .';border-color: '. esc_attr($atts['dotsactclr']) .'; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';

        $bgimg = wp_get_attachment_url($atts['bgimg'], 'full');

        if ($atts['style'] == 'parallax') {
            $out .= '<div class="section section--dark-bg section--review jarallax'.$uniq.'" data-speed="0.5" data-img-position="50% 80%"'.$item_css.'>';
            $out .= agro_img($atts['bgimg'], $blankimg='', $imgclass='jarallax-img', $atts['imgw'], $atts['imgh']);
        } else {
            $out .= '<div class="section section--review lazy'. $uniq.$bgcss .'" data-src="'.esc_url($bgimg).'"'.$item_css.'>';
        }
        $out .= '<div class="container">';

        if ($atts['title'] || $atts['thintitle'] || $atts['desc']) {
            $style = $atts['style'] == 'parallax' ? ' section-heading--white' : '';
            $out .= '<div class="section-heading section-heading--center'.$style.'" data-aos="fade">';
            if ($atts['title'] || $atts['thintitle']) {
                $out .= '<'.$htag.' class="__title">'. esc_html($atts['title']) .' <span>'. esc_html($atts['thintitle']) .'</span></'.$htag.'>';
            }
            if ($atts['desc']) {
                $out .= '<p>'. esc_html($atts['desc']) .'</p>';
            }
            $out .= '</div>';
        }

        $out .= '<div class="review review--slider">';
        $out .= '<div class="js-slick" data-slick=\'{"autoplay": '.$autoplay.', "arrows": false, "dots": '.$dots.', "pauseOnHover":'.$pauseOnHover.', "speed": '.$speed.'}\'>';

        $loop = (array) vc_param_group_parse_atts($atts['loop']);
        foreach ($loop as $item) {
            $out .= '<div class="review__item">';
            if (isset($item['quote']) != '') {
                $out .= '<div class="review__item__text">';
                $out .= '<p><i>'. esc_html($item['quote']) .'</i></p>';
                $out .= '</div>';
            }

            $out .= '<div class="review__item__author d-table">';

            if (isset($item['img']) != '') {
                $out .= '<div class="d-table-cell align-middle">';
                $out .= '<div class="review__item__author-image">';
                $out .= agro_img($item['img'], $blankimg='', $imgclass='circled', $atts['imgw'], $atts['imgh']);
                $out .= '</div>';
                $out .= '</div>';
            }

            $out .= '<div class="d-table-cell align-middle">';
            if (isset($item['name']) != '') {
                $out .= '<span class="review__item__author-name"><strong>'. esc_html($item['name']) .'</strong></span>';
            }
            if (isset($item['job']) != '') {
                $out .= '<span class="review__item__author-position">/'. esc_html($item['job']) .'</span>';
            }
            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';
        }

        $out .= '</div>
                </div>
            </div>
        </div>';

        return $out;
    }
    add_shortcode('agro_review', 'agro_vc_review');
}


/*******************************/
/* banner
/******************************/
if (!function_exists('agro_vc_banner')) {
    function agro_vc_banner($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        'title' => '',
        'thintitle' => '',
        'usecontent' => '',
        'desc' => '',
        'link' => '',
        'logo' => '',
        'imgw' => '',
        'imgh' => '',
        'price1' => '',
        'price2' => '',
        'weight' => '',
        // btn
        'thclr' => '',
        'titleclr' => '',
        'prcbg' => '',
        'prcclr' => '',
        'btnclr' => '',
        'btnhvrclr' => '',
        'btnbrd' => '',
        'btnhvrbrd' => '',
        'btnbg' => '',
        'btnhvrbg' => '',
        'btntype' => '',
        'btnstyle' => '',
        'btnsize' => '',
        // background css
        'css' => '',
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
        ), $atts, 'agro_banner');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $btntype = $atts['btntype'] ? ' '.$atts['btntype'] : '';
        $btnstyle = $atts['btnstyle'] ? ' '.$atts['btnstyle'] : ' custom-btn--style-1';
        $btnsize = $atts['btnsize'] ? ' '.$atts['btnsize'] : ' custom-btn--medium';
        $uniq = 'banner_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.simple-banner.simple-banner--style-1.'.$uniq.' .banner__text .__title {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.simple-banner.simple-banner--style-1.'.$uniq.' .banner__text .__title {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.simple-banner.simple-banner--style-1.'.$uniq.' .banner__text .__title {font-size:'. $tsize .';}' : '';

        $item_css[] = $atts['titleclr'] ? '.simple-banner.simple-banner--style-1.'.$uniq.' .banner__text .__title {display: block; color: '. esc_html($atts['titleclr']) .'; }' : '';
        $item_css[] = $atts['thclr'] ? '.simple-banner.simple-banner--style-1.'.$uniq.' .banner__text .__title span{color: '. esc_attr($atts['thclr']) .'; }' : '';

        $item_css[] = $atts['prcbg'] ? '.simple-banner.simple-banner--style-1.'.$uniq.' .__label{background-color: '. esc_attr($atts['prcbg']) .'; }' : '';
        $item_css[] = $atts['prcclr'] ? '.simple-banner.simple-banner--style-1.'.$uniq.' .__label .num-1{color: '. esc_attr($atts['prcclr']) .'; }' : '';
        $item_css[] = $atts['prcclr'] ? '.simple-banner.simple-banner--style-1.'.$uniq.' .__label .num-2{color: '. esc_attr($atts['prcclr']) .'; }' : '';
        $item_css[] = $atts['prcclr'] ? '.simple-banner.simple-banner--style-1.'.$uniq.' .__label span{color: '. esc_attr($atts['prcclr']) .'; }' : '';
        $item_css[] = $atts['btnclr'] ? '.simple-banner.simple-banner--style-1.'.$uniq.' .custom-btn {color: '. esc_attr($atts['btnclr']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrclr'] ? '.simple-banner.simple-banner--style-1.'.$uniq.' .custom-btn:hover {color: '. esc_attr($atts['btnhvrclr']) .'!important; }' : '';
        $item_css[] = $atts['btnbrd'] ? '.simple-banner.simple-banner--style-1.'.$uniq.' .custom-btn {border-color: '. esc_attr($atts['btnbrd']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrbrd'] ? '.simple-banner.simple-banner--style-1.'.$uniq.' .custom-btn:hover {border-color: '. esc_attr($atts['btnhvrbrd']) .'!important; }' : '';
        $item_css[] = $atts['btnbg'] ? '.simple-banner.simple-banner--style-1.'.$uniq.' .custom-btn {background-color: '. esc_attr($atts['btnbg']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrbg'] ? '.simple-banner.simple-banner--style-1.'.$uniq.' .custom-btn:hover {background-color: '. esc_attr($atts['btnhvrbg']) .'!important; }' : '';

        $item_css[] = $atts['btntype'] == 'btn-rounded' ? '.simple-banner.simple-banner--style-1.'.$uniq.' .custom-btn.btn-rounded {border-radius: 10px!important; }' : '';
        $item_css[] = $atts['btntype'] == 'btn-square' ? '.simple-banner.simple-banner--style-1.'.$uniq.' .custom-btn.btn-square {border-radius: 0px !important; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';

        $out .= '<div class="section section--no-pt section--no-pb section--gutter"'.$item_css.'>';

        $out .= '<div class="simple-banner simple-banner--style-1'. $uniq.$bgcss .'" data-aos="fade" data-aos-offset="50">';
        if ($atts['price1'] || $atts['price2'] || $atts['weight']) {
            $out .= '<div class="__label d-none d-md-block">';
            $out .= '<div class="d-table m-auto h-100">';
            $out .= '<div class="d-table-cell align-middle">';
            $out .= '<span class="num-1">'. esc_html($atts['price1']) .'</span>';
            $out .= '</div>';

            $out .= '<div class="d-table-cell align-middle">';
            $out .= '<span class="num-2">'. esc_html($atts['price2']) .'</span>';
            $out .= '<span>'. esc_html($atts['weight']) .'</span>';
            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';
        }
        $out .= '<div class="container">';
        $out .= '<div class="row">';
        $out .= '<div class="col-12">';
        $out .= '<div class="__inner">';
        $blankimg = '';
        $out .= agro_img($atts['logo'], $blankimg, $imgclass='img-fluid', $atts['imgw'], $atts['imgh']);

        $out .= '<div class="row">';
        $out .= '<div class="col-12 col-lg-7 col-xl-5">';
        $out .= '<div class="banner__text" data-aos="fade-left" data-delay="500">';
        $out .= '<'.$htag.' class="__title h1"><b>'. esc_html($atts['title']) .'</b> <span>'. esc_html($atts['thintitle']) .'</span></'.$htag.'>';
        if ($atts['usecontent'] == 'yes') {
            $out .= do_shortcode($content);
        } else {
            if ($atts['desc']) {
                $out .= wpautop($atts['desc']);
            }
        }

        $link = !empty($atts['link']) ? $atts['link'] : '';
        $link = vc_build_link($link);
        $atitle = $link['title'];
        $href = $link['url'] != '' ? ' href="'. esc_url($link['url']) .'"' : ' href="#"';
        $target = $link['target'] != '' ? ' target="'. esc_attr($link['target']) .'"' : '';

        if ($atitle != '') {
            $out .= '<p><a class="custom-btn'.$btnsize.$btnstyle.$btntype.'"'. $href . $target .'>'. esc_html($atitle) .'</a></p>';
        }

        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_banner', 'agro_vc_banner');
}

/*******************************/
/* banner2
/******************************/
if (!function_exists('agro_vc_banner2')) {
    function agro_vc_banner2($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        'link1' => '',
        'link2' => '',
        'logo' => '',
        'lgimgw' => '',
        'lgimgh' => '',
        'img1' => '',
        'imgw1' => '',
        'imgh1' => '',
        'img2' => '',
        'imgw2' => '',
        'imgh2' => '',
        // background css
        'css' => '',
    ), $atts, 'agro_banner2');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $out = '';
        $out .= '<div class="section section--no-pt section--no-pb section--gutter'. $bgcss .'">';
        $out .= '<div class="container-fluid px-md-0">';

        $out .= '<div class="simple-banner simple-banner--style-2" data-aos="fade" data-aos-offset="50">';
        $blankimg = '';
        $out .= agro_img($atts['logo'], $blankimg, $imgclass='img-logo img-fluid', $atts['lgimgw'], $atts['lgimgh']);

        $out .= '<div class="row no-gutters">';
        $out .= '<div class="col-12 col-md-6">';

        $link1 = !empty($atts['link1']) ? $atts['link1'] : '';
        $link1 = vc_build_link($link1);
        $atitle1 = $link1['title'];
        $href1 = $link1['url'] != '' ? ' href="'. esc_url($link1['url']) .'"' : ' href="#"';
        $target1 = $link1['target'] != '' ? ' target="'. esc_attr($link1['target']) .'"' : '';

        if ($href1 != '') {
            $out .= '<a'.$href1.$target1.'>'. agro_img($atts['img1'], $blankimg, $imgclass='img-fluid w-100', $atts['imgw1'], $atts['imgh1']).'</a>';
        } else {
            $out .= agro_img($atts['img1'], $blankimg, $imgclass='img-fluid w-100', $atts['imgw2'], $atts['imgh2']);
        }
        $out .= '</div>';

        $out .= '<div class="col-12 col-md-6">';

        $link2 = !empty($atts['link2']) ? $atts['link2'] : '';
        $link2 = vc_build_link($link2);
        $atitle2 = $link2['title'];
        $href2 = $link2['url'] != '' ? ' href="'. esc_url($link2['url']) .'"' : ' href="#"';
        $target2 = $link2['target'] != '' ? ' target="'. esc_attr($link2['target']) .'"' : '';

        if ($href2 != '') {
            $out .= '<a'.$href2.$target2.'>'. agro_img($atts['img2'], $blankimg, $imgclass='img-fluid w-100', $atts['imgw2'], $atts['imgh2']).'</a>';
        } else {
            $out .= agro_img($atts['img2'], $blankimg, $imgclass='img-fluid w-100', $atts['imgw2'], $atts['imgh2']);
        }
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_banner2', 'agro_vc_banner2');
}

/*******************************/
/* banner 3
/******************************/
if (!function_exists('agro_vc_banner3')) {
    function agro_vc_banner3($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        'title' => '',
        'img' => '',
        'imgw' => '',
        'imgh' => '',
        'tclr' => '',
        // background css
        'css' => '',
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
    ), $atts, 'agro_banner3');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $uniq = 'section_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.section.'.$uniq.' .banner__text .__title {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.section.'.$uniq.' .banner__text .__title {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.section.'.$uniq.' .banner__text .__title {font-size:'. $tsize .';}' : '';

        $item_css[] = $atts['tclr'] ? '.section.'.$uniq.' .banner__text .__title {color: '. esc_html($atts['tclr']) .'; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';

        $out .= '<div class="section section--no-pt section--no-pb'.$uniq.'"'.$item_css.'>';
        $out .= '<div class="simple-banner simple-banner--style-3'. $bgcss .'" data-aos="fade" data-aos-offset="50">';
        $out .= '<div class="__label" data-aos="zoom-in" data-delay="700">';
        $blankimg = get_theme_file_uri().'/images/blank.gif';
        $out .= agro_img($atts['img'], $blankimg='', $imgclass='', $atts['imgw'], $atts['imgh']);
        $out .= '</div>';
        $out .= '<div class="container">';
        $out .= '<div class="row">';
        $out .= '<div class="col">';
        $out .= '<div class="__inner">';
        $out .= '<div class="row">';
        $out .= '<div class="col-lg-5">';
        $out .= '<div class="banner__text" data-aos="fade" data-delay="500">';
        $out .= '<'.$htag.' class="__title">'. esc_html($atts['title']) .'</'.$htag.'>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_banner3', 'agro_vc_banner3');
}

/*******************************/
/* promo2
/******************************/
if (!function_exists('agro_vc_promo2')) {
    function agro_vc_promo2($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        'title1' => '',
        'link1' => '',
        'img1' => '',
        'imgw1' => '',
        'imgh1' => '',
        'title2' => '',
        'link2' => '',
        'img2' => '',
        'imgw2' => '',
        'imgh2' => '',
        'brdclr' => '',
        'tclr' => '',
        'tclr2' => '',
        // background css
        'css' => '',
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
        ), $atts, 'agro_promo2');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $uniq = 'section_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.section.'.$uniq.' .product-promo .__item--first .__content .__title {'. $google .'}' : '';
        $item_css[] = $google ? '.section.'.$uniq.' .product-promo .__item--second .__content .__title {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.section.'.$uniq.' .product-promo .__item--first .__content .__title {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.section.'.$uniq.' .product-promo .__item--first .__content .__title {font-size:'. $tsize .';}' : '';

        $item_css[] = $atts['brdclr'] ? '.section.'.$uniq.' .product-promo--style-1 .__item--first .__content:before{border-color: '. esc_html($atts['brdclr']) .'; }' : '';
        $item_css[] = $atts['tclr'] ? '.section.'.$uniq.' .product-promo .__item--first .__content .__title {color: '. esc_html($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['tclr2'] ? '.section.'.$uniq.' .product-promo .__item--second .__content .__title {color: '. esc_html($atts['tclr2']) .'; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';
        $out .= '<div class="section section--no-pb'. $uniq.$bgcss .'"'.$item_css.'>';
        $out .= '<div class="container">';

        $out .= '<div class="product-promo product-promo--style-1">';
        $out .= '<div class="__inner">';
        $out .= '<div class="row align-items-center">';

        $out .= '<div class="col-12 col-md-6">';
        $out .= '<div class="__item __item--first">';
        $out .= '<figure class="__image">';
        $out .= agro_img($atts['img1'], $blankimg='', $imgclass='', $atts['imgw1'], $atts['imgh1']);
        $out .= '</figure>';

        if ($atts['title1']) {
            $out .= '<div class="__content"><'.$htag.' class="__title">'. esc_html($atts['title1']) .'</'.$htag.'></div>';
        }


        $link1 = !empty($atts['link1']) ? $atts['link1'] : '';
        $link1 = vc_build_link($link1);
        $atitle1 = $link1['title'];
        $href1 = $link1['url'] != '' ? ' href="'. esc_url($link1['url']) .'"' : ' href="#"';
        $target1 = $link1['target'] != '' ? ' target="'. esc_attr($link1['target']) .'"' : '';

        $out .= '<a class="__link"'. $href1 . $target1 .'></a>';

        $out .= '</div>';
        $out .= '</div>';


        $out .= '<div class="col-12 col-md-6">';
        $out .= '<div class="__item __item--second">';

        $out .= '<figure class="__image">';
        $out .= agro_img($atts['img2'], $blankimg='', $imgclass='', $atts['imgw2'], $atts['imgh2']);
        $out .= '</figure>';

        if ($atts['title2']) {
            $out .= '<div class="__content"><'.$htag.' class="__title">'. esc_html($atts['title2']) .'</'.$htag.'></div>';
        }

        $link2 = !empty($atts['link2']) ? $atts['link2'] : '';
        $link2 = vc_build_link($link2);
        $atitle2 = $link2['title'];
        $href2 = $link2['url'] != '' ? ' href="'. esc_url($link2['url']) .'"' : ' href="#"';
        $target2 = $link2['target'] != '' ? ' target="'. esc_attr($link2['target']) .'"' : '';

        $out .= '<a class="__link"'. $href2 . $target2 .'></a>';

        $out .= '</div>';
        $out .= '</div>';

        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_promo2', 'agro_vc_promo2');
}

/*******************************/
/* Blog Post Loop
/******************************/
if (!function_exists('agro_vc_blog')) {
    function agro_vc_blog($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        'style'=> '',
        'build_query'=> '',
        'pag'=> '',
        'thintitle' => '',
        'title' => '',
        'desc' => '',
        'spb' => '',
        'spt' => '',
        // thumb options
        'hidethumb' => '',
        'imgw'=> '',
        'imgh'=> '',
        'fullthumb' => '',
        'hidecat' => '',
        'hidetitle' => '',
        'hidedate' => '',
        'hidetext' => '',
        'excerptsz' => '30',
        'readmore' => 'Read more',
        // color
        'tclr' => '',
        'thclr' => '',
        'descclr' => '',
        'catclr' => '',
        'ptclr' => '',
        'pthclr' => '',
        'dclr' => '',
        'datebg' => '',
        'dateclr' => '',
        'mounthclr' => '',
        'contentbg' => '',
        'btnclr' => '',
        'btnhvrclr' => '',
        'btnbrd' => '',
        'btnhvrbrd' => '',
        'btnbg' => '',
        'btnhvrbg' => '',
        'btntype' => '',
        'btnstyle' => '',
        'btnsize' => '',
        // background css
        'css' => '',
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
        ), $atts, 'agro_blog');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $btntype = $atts['btntype'] ? ' '.$atts['btntype'] : '';
        $btnstyle = $atts['btnstyle'] ? ' '.$atts['btnstyle'] : ' custom-btn--style-1';
        $btnsize = $atts['btnsize'] ? ' '.$atts['btnsize'] : ' custom-btn--medium';

        $uniq = 'blog_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.section-blog.'.$uniq.' .section-heading .__title {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.section-blog.'.$uniq.' .section-heading .__title {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.section-blog.'.$uniq.' .section-heading .__title {font-size:'. $tsize .';}' : '';

        $item_css[] = $atts['tclr'] ? '.section-blog.'.$uniq.' .section-heading .__title {color: '. esc_html($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['thclr'] ? '.section-blog.'.$uniq.' .section-heading .__title span{color: '. esc_attr($atts['thclr']) .'; }' : '';
        $item_css[] = $atts['descclr'] ? '.section-blog.'.$uniq.' .section-heading p{color: '. esc_attr($atts['descclr']) .'; }' : '';
        $item_css[] = $atts['catclr'] ? '.section-blog.'.$uniq.' .posts .__item .__category a{color: '. esc_attr($atts['catclr']) .'; }' : '';
        $item_css[] = $atts['ptclr'] ? '.section-blog.'.$uniq.' .posts .__item .__title{color: '. esc_attr($atts['ptclr']) .'; }' : '';
        $item_css[] = $atts['pthclr'] ? '.section-blog.'.$uniq.' .posts .__item .__title:hover{color: '. esc_attr($atts['pthclr']) .'; }' : '';
        $item_css[] = $atts['dclr'] ? '.section-blog.'.$uniq.' .posts .__item--preview p{color: '. esc_attr($atts['dclr']) .'; }' : '';
        $item_css[] = $atts['datebg'] ? '.section-blog.'.$uniq.' .posts .__item--preview .__date-post{background-color: '. esc_attr($atts['datebg']) .'; }' : '';
        $item_css[] = $atts['dateclr'] ? '.section-blog.'.$uniq.' .posts .__item--preview .__date-post strong{color: '. esc_attr($atts['dateclr']) .'; }' : '';
        $item_css[] = $atts['mounthclr'] ? '.section-blog.'.$uniq.' .posts .__item--preview .__date-post{color: '. esc_attr($atts['mounthclr']) .'; }' : '';
        $item_css[] = $atts['contentbg'] ? '.section-blog.'.$uniq.' .posts .__item--preview .__content{background-color: '. esc_attr($atts['contentbg']) .';padding-bottom: 40px; }' : '';

        // btn
        $item_css[] = $atts['btnclr'] ? '.section-blog.'.$uniq.' .custom-btn {color: '. esc_attr($atts['btnclr']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrclr'] ? '.section-blog.'.$uniq.' .custom-btn:hover {color: '. esc_attr($atts['btnhvrclr']) .'!important; }' : '';
        $item_css[] = $atts['btnbrd'] ? '.section-blog.'.$uniq.' .custom-btn {border-color: '. esc_attr($atts['btnbrd']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrbrd'] ? '.section-blog.'.$uniq.' .custom-btn:hover {border-color: '. esc_attr($atts['btnhvrbrd']) .'!important; }' : '';
        $item_css[] = $atts['btnbg'] ? '.section-blog.'.$uniq.' .custom-btn {background-color: '. esc_attr($atts['btnbg']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrbg'] ? '.section-blog.'.$uniq.' .custom-btn:hover {background-color: '. esc_attr($atts['btnhvrbg']) .'!important; }' : '';

        $item_css[] = $atts['btntype'] == 'btn-rounded' ? '.section-blog.'.$uniq.' .custom-btn.btn-rounded {border-radius: 10px!important; }' : '';
        $item_css[] = $atts['btntype'] == 'btn-square' ? '.section-blog.'.$uniq.' .custom-btn.btn-square {border-radius: 0px !important; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        global $post;

        list($args) = vc_build_loop_query($atts['build_query']);
        $args['paged'] = (get_query_var('paged')) ? get_query_var('paged') : 1;

        $out = '';

        $spb = ($atts['spb'] == 'hide') ? ' section--no-pb' : '';
        $spt = ($atts['spt'] == 'hide') ? ' section--no-pt' : '';

        $out .= '<div class="section section-blog'.$spt.$spb.$uniq.$bgcss.'"'.$item_css.'>';

        $out .= '<div class="container">';
        if ($atts['title'] || $atts['thintitle'] || $atts['desc']) {
            $out .= '<div class="section-heading section-heading--center" data-aos="fade">';
            // section title
            if ($atts['title'] || $atts['thintitle']) {
                $out .= '<'.$htag.' class="__title">'. esc_html($atts['title']) .' <span>'. esc_html($atts['thintitle']) .'</span></'.$htag.'>';
            }
            // section description
            if ($atts['desc']) {
                $out .= '<p>'. esc_html($atts['desc']) .'</p>';
            }
            $out .= '</div>';
        }

        if ($atts['style'] == '2') {
            $out .= '<div class="posts posts--style-2">';
        } else {
            $out .= '<div class="posts posts--style-1">';
        }
        $out .= '<div class="__inner">';
        $out .= '<div class="row">';

        // The Query
        $agro_query = new WP_Query($args);
        if ($agro_query->have_posts()) {
            // The Loop
            $counter = 1;
            while ($agro_query->have_posts()) {
                $agro_query->the_post();
                $delay = ($counter * 100);
                if (has_post_thumbnail()) {
                    ob_start();
                    post_class('nt-post col-12 col-sm-6 col-lg-4');
                    $out .= '<div '. ob_get_clean().'>';
                    $out .= '<div class="__item __item--preview" data-aos="flip-up" data-aos-delay="'.$delay.'" data-aos-offset="0">';


                    if ($atts['style'] == '2') {
                        $out .= '<figure class="__image">';
                        if ($atts['fullthumb'] == 'yes') {
                            $out .= get_the_post_thumbnail($post->ID, 'full');
                        } else {
                            $posthumbfull = wp_get_attachment_url(get_post_thumbnail_id(), 'full');
                            $img_w = ($atts['imgw'] != '') ? $atts['imgw'] : 767;
                            $img_h = ($atts['imgh'] != '') ? $atts['imgh'] : 812;
                            $posthumb = ntframework_aq_resize($posthumbfull, $img_w, $img_h, true, true, true);
                            $out .= '<img src="'.$posthumb.'" alt="'.get_the_title().'" />';
                        }

                        $out .= '<span class="__overlay"></span>';

                        $out .= '<div class="__content">';

                        // post date
                        if ($atts['hidedate'] != 'hide') {
                            $out .= '<span class="__date-post">';
                            $out .= '<strong>'.get_the_date('j').'</strong>'.get_the_date('M').'';
                            $out .= '</span>';
                        }
                        // post cat
                        if ($atts['hidecat'] != 'hide') {
                            ob_start();
                            the_category(' / ');
                            $out .= '<p class="__category">'.ob_get_clean().'</p>';
                        }
                        // post title
                        if ($atts['hidetitle'] != 'hide') {
                            $out .= '<h3 class="__title h5"><a href="'.get_permalink().'">'.get_the_title().'</a></h3>';
                        }

                        $out .= '</div>';
                        $out .= '</figure>';
                    } else {
                        $out .= '<figure class="__image">';
                        if ($atts['fullthumb'] == 'yes') {
                            $out .= get_the_post_thumbnail($post->ID, 'full');
                        } else {
                            $posthumbfull = wp_get_attachment_url(get_post_thumbnail_id(), 'full');
                            $img_w = ($atts['imgw'] != '') ? $atts['imgw'] : 767;
                            $img_h = ($atts['imgh'] != '') ? $atts['imgh'] : 812;
                            $posthumb = ntframework_aq_resize($posthumbfull, $img_w, $img_h, true, true, true);
                            $out .= '<img src="'.$posthumb.'" alt="'.get_the_title().'" />';
                        }
                        $out .= '</figure>';


                        $out .= '<div class="__content">';

                        // post cat
                        if ($atts['hidecat'] != 'hide') {
                            ob_start();
                            the_category(' / ');
                            $out .= '<p class="__category">'.ob_get_clean().'</p>';
                        }
                        // post title
                        if ($atts['hidetitle'] != 'hide') {
                            $out .= '<h3 class="__title h5"><a href="'.get_permalink().'">'.get_the_title().'</a></h3>';
                        }
                        // post excerpt
                        if ($atts['hidetext'] != 'hide') {
                            $out .= wpautop(wp_trim_words(get_the_excerpt(), $atts['excerptsz']));
                        }
                        // post button
                        $out .= '<a href="'.esc_url(get_permalink()).'" class="custom-btn'.$btnsize.$btnstyle.$btntype.'">'. $atts['readmore'].'</a>';

                        $out .= '</div>';

                        // post date
                        if ($atts['hidedate'] != 'hide') {
                            $out .= '<span class="__date-post">';
                            $out .= '<strong>'.get_the_date('j').'</strong>'.get_the_date('M').'';
                            $out .= '</span>';
                        }
                    }// end style

                    $out .= '</div>';
                    $out .= '</div>';
                }// end if has post thumbnail
                $counter++;
            }
            //pagination
            if ($atts['pag'] == 'yes') {
                ob_start();
                agro_index_loop_pagination();
                $out .= '<div class="col-12 pagination-shortcode pt-12">'. ob_get_clean().'</div>';
            }

            wp_reset_postdata();
        }

        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        $out .= '</div>';
        $out .= '</div>';


        return $out;
    }
    add_shortcode('agro_blog', 'agro_vc_blog');
}

/*******************************/
/* partners
/******************************/

if (!function_exists('agro_vc_partners')) {
    function agro_vc_partners($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        // section data
        'title' => '',
        'thintitle' => '',
        'desc' => '',
        'images' => '',
        'imgw' => '',
        'imgh' => '',
        'lgcount' => '5',
        'mdcount' => '4',
		'smcount' => '3',
		'xscount' => '1',
        // color
        'tclr' => '',
        'thclr' => '',
        'descclr' => '',
        // background css
        'css' => '',
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
        'speed' => '',
        'autoplay' => '',
        'dots' => '',
        'pauseonhover' => '',
        ), $atts, 'agro_partners');

        $speed = $atts['speed'] ? $atts['speed'] : 1000;
        $autoplay = $atts['autoplay'] == 'yes' ? 'true' : 'false';
        $dots = $atts['dots'] == 'yes' ? 'true' : 'false';
        $pauseonhover = $atts['pauseonhover'] == 'yes' ? 'true' : 'false';


        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $uniq = 'section_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.section.'.$uniq.' .section-heading .__title {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.section.'.$uniq.' .section-heading .__title {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.section.'.$uniq.' .section-heading .__title {font-size:'. $tsize .';}' : '';

        $item_css[] = $atts['tclr'] ? '.section.'.$uniq.' .section-heading .__title {color: '. esc_html($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['thclr'] ? '.section.'.$uniq.' .section-heading .__title span{color: '. esc_attr($atts['thclr']) .'; }' : '';
        $item_css[] = $atts['descclr'] ? '.section.'.$uniq.' .section-heading p{color: '. esc_attr($atts['descclr']) .'; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';

        $out .= '<div class="section '. $uniq.$bgcss .'"'.$item_css.'>';

        $out .= '<div class="container">';

        if ($atts['title'] || $atts['thintitle'] || $atts['desc']) {
            $out .= '<div class="section-heading section-heading--center" data-aos="fade">';
            // section title
            if ($atts['title'] || $atts['thintitle']) {
                $out .= '<'.$htag.' class="__title">'. esc_html($atts['title']) .' <span>'. esc_html($atts['thintitle']) .'</span></'.$htag.'>';
            }
            // section description
            if ($atts['desc']) {
                $out .= '<p>'. esc_html($atts['desc']) .'</p>';
            }
            $out .= '</div>';
        }

        $out .= '<div class="partners-list">';
        $out .= '<div class="js-slick" data-slick=\'{"slidesToShow": '.$atts['lgcount'].',"autoplay": '.$autoplay.',"arrows": false,"dots": '.$dots.',"pauseOnHover": '.$pauseonhover.',"speed": '.$speed.',"responsive": [{"breakpoint":576,"settings":{"slidesToShow": '.$atts['xscount'].'}},{"breakpoint":767,"settings":{"slidesToShow": '.$atts['smcount'].'}},{"breakpoint":991,"settings":{"slidesToShow": '.$atts['mdcount'].'}},{"breakpoint":1199,"settings":{"autoplay": false,"dots": false,"slidesToShow": '.$atts['lgcount'].'}}]}\'>';
        // brands images
        $image_ids = explode(',', $atts['images']);
        foreach ($image_ids as $image_id) {
            $images = agro_img($image_id, $blankimg='', $imgclass='img-fluid m-auto', $atts['imgw'], $atts['imgh']);
            $out .= '<div class="__item">';
            $out .= $images;
            $out .= '</div>';
            $images++;
        }

        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_partners', 'agro_vc_partners');
}

/*******************************/
/* google map
/******************************/
if (!function_exists('agro_vc_gmap')) {
    function agro_vc_gmap($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        // section data
        'useiframe' => '',
        'iframemap' => '',
        'apikey' => '',
        'longitude' => '',
        'latitude' => '',
        'zoom' => '',
        'markerimg' => '',
        'minh' => '255',
        // background css
        'css' => '',
        ), $atts, 'agro_partners');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $out = '';

        $out .= '<div class="section section--no-pt section--no-pb'. $bgcss .'" style="min-height: '.$atts['minh'].'px">';

            if ($atts['useiframe'] == 'yes' ) {

                $out .= '<div class="custom-map"><iframe src="'.$atts['iframemap'].'" width="'.$atts['minh'].'" height="'.$atts['minh'].'" frameborder="0" style="border:0" allowfullscreen></iframe></div>';

            } else {
                $zoom = $atts['zoom'] ? $atts['zoom'] : 15;
                $markerimg = wp_get_attachment_url($atts['markerimg'], 'full');
                $out .= '<div class="g_map" data-zoom="'.$zoom.'" data-api-key="'.$atts['apikey'].'" data-longitude="'.$atts['longitude'].'" data-latitude="'.$atts['latitude'].'" data-marker="'.esc_url($markerimg).'" style="min-height: '.$atts['minh'].'px"></div>';

            }

        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_gmap', 'agro_vc_gmap');
}
/*******************************/
/* google map & contact form
/******************************/
if (!function_exists('agro_vc_contactgmap')) {
    function agro_vc_contactgmap($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        // section data
        'title' => '',
        'thintitle' => '',
        'useiframe' => '',
        'iframemap' => '',
        'apikey' => '',
        'longitude' => '',
        'latitude' => '',
        'markerimg' => '',
        'minh' => '255',
        'tclr' => '',
        'thclr' => '',
        // background css
        'css' => '',
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
        ), $atts, 'agro_contactgmap');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $uniq = 'section_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.section.'.$uniq.' .section-heading .__title {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.section.'.$uniq.' .section-heading .__title {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.section.'.$uniq.' .section-heading .__title {font-size:'. $tsize .';}' : '';

        $item_css[] = $atts['tclr'] ? '.section.'.$uniq.' .section-heading .__title {color: '. esc_html($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['thclr'] ? '.section.'.$uniq.' .section-heading .__title span{color: '. esc_attr($atts['thclr']) .'; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';

        $out .= '<div class="section section--dark-bg section--contacts'.$uniq.'"'.$item_css.'>';
        $out .= '<div class="container">';
        $out .= '<div class="row ajustify-content-end">';
        $out .= '<div class="col-12 col-md-6 col-lg-4">';

        $out .= '<div class="row ajustify-content-end">';
        $out .= '<div class="col-12 col-md-11 col-lg-12">';
        if ($atts['title'] || $atts['thintitle']) {
            $out .= '<div class="section-heading section-heading--white">';
            if ($atts['title'] || $atts['thintitle']) {
                $out .= '<'.$htag.' class="__title">'.$atts['title'].' <span>'.$atts['thintitle'].'</span></'.$htag.'>';
            }
            $out .= '</div>';
        }

        $out .= do_shortcode($content);

        $out .= '</div>';
        $out .= '</div>';

        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        $out .= '<div class="row no-gutters">';
        $out .= '<div class="col-12 col-md-6 col-lg-7  map-container map-container--right">';

        if ($atts['useiframe'] == 'yes' ) {

            $out .= '<div class="custom-map"><iframe src="'.$atts['iframemap'].'" width="'.$atts['minh'].'" height="'.$atts['minh'].'" frameborder="0" style="border:0" allowfullscreen></iframe></div>';


        } else {

            $markerimg = wp_get_attachment_url($atts['markerimg'], 'full');
            $out .= '<div class="g_map" data-api-key="'.$atts['apikey'].'" data-longitude="'.$atts['longitude'].'" data-latitude="'.$atts['latitude'].'" data-marker="'.esc_url($markerimg).'" style="min-height: '.$atts['minh'].'px"></div>';

        }

        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_contactgmap', 'agro_vc_contactgmap');
}
/*******************************/
/* google map & contact form 2
/******************************/
if (!function_exists('agro_vc_contactgmap2')) {
    function agro_vc_contactgmap2($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        // section data
        'title' => '',
        'thintitle' => '',
        'desc' => '',
        'useiframe' => '',
        'iframemap' => '',
        'apikey' => '',
        'longitude' => '',
        'latitude' => '',
        'markerimg' => '',
        'minh' => '255',
        'tclr' => '',
        'thclr' => '',
        'descclr' => '',
        // background css
        'css' => '',
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
    ), $atts, 'agro_contactgmap2');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $uniq = 'section_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.section.'.$uniq.' .section-heading .__title {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.section.'.$uniq.' .section-heading .__title {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.section.'.$uniq.' .section-heading .__title {font-size:'. $tsize .';}' : '';

        $item_css[] = $atts['tclr'] ? '.section.'.$uniq.' .section-heading .__title {color: '. esc_html($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['thclr'] ? '.section.'.$uniq.' .section-heading .__title span{color: '. esc_attr($atts['thclr']) .'; }' : '';
        $item_css[] = $atts['descclr'] ? '.section.'.$uniq.' .section-heading .__title span{color: '. esc_attr($atts['descclr']) .'; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';

        $out .= '<div class="section section--dark-bg section--contacts'.$uniq.'"'.$item_css.'>';
        $out .= '<div class="container">';
        $out .= '<div class="row justify-content-end">';
        $out .= '<div class="col-12 col-md-6">';

        $out .= '<div class="row justify-content-end">';
        $out .= '<div class="col-md-11">';
        if ($atts['title'] || $atts['thintitle'] || $atts['desc']) {
            $out .= '<div class="section-heading section-heading--white">';
            if ($atts['title'] || $atts['thintitle']) {
                $out .= '<'.$htag.' class="__title">'.$atts['title'].' <span>'.$atts['thintitle'].'</span></'.$htag.'>';
            }
            if ($atts['desc']) {
                $out .= '<p>'.$atts['desc'].'</p>';
            }
            $out .= '</div>';
        }

        $out .= do_shortcode($content);

        $out .= '</div>';
        $out .= '</div>';

        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        $out .= '<div class="row no-gutters">';
        $out .= '<div class="col-md-6 map-container map-container--left">';

        if ($atts['useiframe'] == 'yes' ) {

            $out .= '<div class="custom-map"><iframe src="'.$atts['iframemap'].'" width="'.$atts['minh'].'" height="'.$atts['minh'].'" frameborder="0" style="border:0" allowfullscreen></iframe></div>';

        } else {

            $markerimg = wp_get_attachment_url($atts['markerimg'], 'full');
            $out .= '<div class="g_map" data-api-key="'.$atts['apikey'].'" data-longitude="'.$atts['longitude'].'" data-latitude="'.$atts['latitude'].'" data-marker="'.esc_url($markerimg).'" style="min-height: '.$atts['minh'].'px"></div>';

        }

        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_contactgmap2', 'agro_vc_contactgmap2');
}

/*******************************/
/* features style 1
/******************************/
if (!function_exists('agro_vc_features_one')) {
    function agro_vc_features_one($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        // data
        'title' => '',
        'img' => '',
        'imgw' => '',
        'imgh' => '',
        'anim' => '',
        'aos' => '',
        'delay' => '',
        'offset' => '',
        'tclr' => '',
        'link' => '',
        // background css
        'css' => '',
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
        ), $atts, 'agro_features_one');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $link = !empty($atts['link']) ? $atts['link'] : '';
        $link = vc_build_link($link);
        $atitle = $link['title'] ? ' title="'. esc_attr($link['title']) .'"' : '';;
        $href = $link['url'] != '' ? ' href="'. esc_url($link['url']) .'"' : '';
        $target = $link['target'] != '' ? ' target="'. esc_attr($link['target']) .'"' : '';

        $uniq = 'feature_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h5';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.feature--style-1.'.$uniq.' .__item .__title {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.feature--style-1.'.$uniq.' .__item .__title {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.feature--style-1.'.$uniq.' .__item .__title {font-size:'. $tsize .';}' : '';

        $item_css[] = $atts['tclr'] ? '.feature--style-1.'.$uniq.' .__item .__title {color: '. esc_html($atts['tclr']) .'; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';
        $animdelay = $atts['delay'] != '' ? ' data-aos-delay="'.$atts['delay'].'"' : '';
        $animoffset = $atts['offset'] != '' ? ' data-aos-offset="'.$atts['offset'].'"' : '';
        $animaos = $atts['anim'] == 'yes' ? ' data-aos="'.$atts['aos'].'"'.$animdelay.$animoffset : '';

        $out .= '<div class="feature feature--style-1'. $uniq.$bgcss .'"'.$item_css.'>';
        $out .= '<div class="__item  text-center"'.$animaos.'>';

        if ($href) {
            $out .= '<a class="features-link"'. $href . $target . $atitle.'>';
        }

        if ($atts['img']) {
            $out .= '<i class="__ico">';
            $blankimg = '';
            $out .= agro_img($atts['img'], $blankimg, $imgclass='img-fluid', $atts['imgw'], $atts['imgh']);
            $out .= '</i>';
        }

        if ($atts['title']) {
            $out .= '<'.$htag.' class="__title">'. esc_html($atts['title']) .'</'.$htag.'>';
        }
        if ($href) {
            $out .= '</a>';
        }
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_features_one', 'agro_vc_features_one');
}


/*******************************/
/* features style 2
/******************************/
if (!function_exists('agro_vc_features2')) {
    function agro_vc_features2($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        'lgimg' => '',
        'lgimgw' => '',
        'lgimgh' => '',
        'stitle' => '',
        'sthintitle' => '',
        'link' => '',
        'bgimg' => '',
        'bgimgw' => '',
        'bgimgh' => '',
        // product
        'loop' => '',
        'title' => '',
        'lg' => '',
        'sm' => '',
        'img' => '',
        'imgw' => '',
        'imgh' => '',
        'anim' => '',
        'aos' => '',
        'delay' => '',
        'offset' => '',
        // color
        'tclr' => '',
        'thclr' => '',
        'descclr' => '',
        'itclr' => '',
        'btnclr' => '',
        'btnhvrclr' => '',
        'btnbrd' => '',
        'btnhvrbrd' => '',
        'btnbg' => '',
        'btnhvrbg' => '',
        'btntype' => '',
        'btnstyle' => '',
        'btnsize' => '',
        // background css
        'css' => '',
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
        ), $atts, 'agro_features2');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $btntype = $atts['btntype'] ? ' '.$atts['btntype'] : '';
        $btnstyle = $atts['btnstyle'] ? ' '.$atts['btnstyle'] : ' custom-btn--style-1';
        $btnsize = $atts['btnsize'] ? ' '.$atts['btnsize'] : ' custom-btn--medium';
        $uniq = 'section_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.section.'.$uniq.' .section-heading .__title {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.section.'.$uniq.' .section-heading .__title {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.section.'.$uniq.' .section-heading .__title {font-size:'. $tsize .';}' : '';

        $item_css[] = $atts['tclr'] ? '.section.'.$uniq.' .section-heading .__title {color: '. esc_html($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['thclr'] ? '.section.'.$uniq.' .section-heading .__title span{color: '. esc_attr($atts['thclr']) .'; }' : '';
        $item_css[] = $atts['descclr'] ? '.section.'.$uniq.' .section-heading p{color: '. esc_attr($atts['descclr']) .'; }' : '';
        $item_css[] = $atts['itclr'] ? '.section.'.$uniq.' .feature--style-3 .__item .__title {color: '. esc_attr($atts['itclr']) .'; }' : '';

        // btn
        $item_css[] = $atts['btnclr'] ? '.section.'.$uniq.' .custom-btn {color: '. esc_attr($atts['btnclr']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrclr'] ? '.section.'.$uniq.' .custom-btn:hover {color: '. esc_attr($atts['btnhvrclr']) .'!important; }' : '';
        $item_css[] = $atts['btnbrd'] ? '.section.'.$uniq.' .custom-btn {border-color: '. esc_attr($atts['btnbrd']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrbrd'] ? '.section.'.$uniq.' .custom-btn:hover {border-color: '. esc_attr($atts['btnhvrbrd']) .'!important; }' : '';
        $item_css[] = $atts['btnbg'] ? '.section.'.$uniq.' .custom-btn {background-color: '. esc_attr($atts['btnbg']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrbg'] ? '.section.'.$uniq.' .custom-btn:hover {background-color: '. esc_attr($atts['btnhvrbg']) .'!important; }' : '';

        $item_css[] = $atts['btntype'] == 'btn-rounded' ? '.section.'.$uniq.' .custom-btn.btn-rounded {border-radius: 10px!important; }' : '';
        $item_css[] = $atts['btntype'] == 'btn-square' ? '.section.'.$uniq.' .custom-btn.btn-square {border-radius: 0px !important; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';

        $out .= '<div class="section'. $uniq.$bgcss .'"'.$item_css.'>';
        $out .= '<div class="d-none d-lg-block bg-absolute">';
        $blankimg = '';
        $out .= agro_img($atts['bgimg'], $blankimg, $imgclass='img-fluid', $atts['bgimgw']='', $atts['bgimgh']='');
        $out .= '</div>';

        $out .= '<div class="container">';
        $out .= '<div class="row">';
        $out .= '<div class="col-12 col-md-5 col-lg-4">';
        $out .= '<div class="section-heading section-heading--left" data-aos="fade">';
        $out .= '<p>';
        $blankimg = '';
        $out .= agro_img($atts['lgimg'], $blankimg, $imgclass='img-fluid', $atts['lgimgw'], $atts['lgimgh']);
        $out .= '</p>';
        if ($atts['stitle'] || $atts['sthintitle']) {
            $out .= '<'.$htag.' class="__title">'. esc_html($atts['stitle']) .' <span>'. esc_html($atts['sthintitle']) .'</span></'.$htag.'>';
        }

        $link = !empty($atts['link']) ? $atts['link'] : '';
        $link = vc_build_link($link);
        $atitle = $link['title'];
        $href = $link['url'] != '' ? ' href="'. esc_url($link['url']) .'"' : ' href="#"';
        $target = $link['target'] != '' ? ' target="'. esc_attr($link['target']) .'"' : '';

        if ($atitle != '') {
            $out .= '<p><a class="custom-btn'.$btnsize.$btnstyle.$btntype.'"'. $href . $target .'>'. esc_html($atitle) .'</a></p>';
        }

        $out .= '</div>';
        $out .= '</div>';

        $out .= '<div class="col-12 col-md-7 col-lg-8">';

        $out .= '<div class="feature feature--style-3">';
        $out .= '<div class="__inner">';
        $out .= '<div class="row">';

        $loop = (array) vc_param_group_parse_atts($atts['loop']);
        foreach ($loop as $item) {
            $lg = isset($item['lg']) != '' ? $item['lg'] : 'col-lg-3';
            $sm = isset($item['sm']) != '' ? $item['sm'] : 'col-sm-4';

            $animdelay = $item['delay'] != '' ? ' data-aos-delay="'.$item['delay'].'"' : '';
            $animoffset = $item['offset'] != '' ? ' data-aos-offset="'.$item['offset'].'"' : '';
            $animaos = $item['anim'] == 'yes' ? ' data-aos="'.$item['aos'].'"'.$animdelay.$animoffset : '';

            $out .= '<div class="col-6 '.$sm.' '.$lg.'">';
            $out .= '<div class="__item  text-center"'.$animaos.'>';
            if (isset($item['img']) != '') {
                $out .= '<i class="__ico">';
                $blankimg = '';
                $out .= agro_img($item['img'], $blankimg, $imgclass='img-fluid', $atts['imgw'], $atts['imgh']);
                $out .= '</i>';
            }
            if (isset($item['title']) != '') {
                $out .= '<h5 class="__title">'. $item['title'] .'</h5>';
            }
            $out .= '</div>';
            $out .= '</div>';
        }

        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_features2', 'agro_vc_features2');
}

/*******************************/
/* features style 3
/******************************/
if (!function_exists('agro_vc_features3')) {
    function agro_vc_features3($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        'stitle' => '',
        'sthintitle' => '',
        'desc' => '',
        // product
        'loop' => '',
        'title' => '',
        'lg' => '',
        'sm' => '',
        'img' => '',
        'imgw' => '',
        'imgh' => '',
        'anim' => '',
        'aos' => '',
        'delay' => '',
        'offset' => '',
        // color
        'tclr' => '',
        'thclr' => '',
        'descclr' => '',
        'itclr' => '',
        // background css
        'css' => '',
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
    ), $atts, 'agro_features3');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $uniq = 'section_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.section.'.$uniq.' .section-heading .__title {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.section.'.$uniq.' .section-heading .__title {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.section.'.$uniq.' .section-heading .__title {font-size:'. $tsize .';}' : '';

        $item_css[] = $atts['tclr'] ? '.section.'.$uniq.' .section-heading .__title {color: '. esc_html($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['thclr'] ? '.section.'.$uniq.' .section-heading .__title span{color: '. esc_attr($atts['thclr']) .'; }' : '';
        $item_css[] = $atts['descclr'] ? '.section.'.$uniq.' .section-heading p{color: '. esc_attr($atts['descclr']) .'; }' : '';
        $item_css[] = $atts['itclr'] ? '.section.'.$uniq.' .feature--style-3 .__item .__title {color: '. esc_attr($atts['itclr']) .'; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';
        $out .= '<div class="section section--no-pt'. $uniq.$bgcss .'"'.$item_css.'>';
        $out .= '<div class="container">';
        if ($atts['stitle'] || $atts['sthintitle'] || $atts['desc']) {
            $out .= '<div class="section-heading section-heading--center" data-aos="fade">';
            if ($atts['stitle'] != '' || $atts['sthintitle'] != '') {
                $out .= '<'.$htag.' class="__title">'. esc_html($atts['stitle']) .' <span>'. esc_html($atts['sthintitle']) .'</span></'.$htag.'>';
            }
            if ($atts['desc'] != '') {
                $out .= '<p>'. esc_html($atts['desc']) .'</p>';
            }

            $out .= '</div>';
        }

        $out .= '<div class="feature feature--style-3">';
        $out .= '<div class="__inner">';
        $out .= '<div class="row">';
        $lg = $atts['lg'] ? $atts['lg'] : 'col-lg-2';
        $sm = $atts['sm'] ? $atts['sm'] : 'col-sm-4';
        $loop = (array) vc_param_group_parse_atts($atts['loop']);
        foreach ($loop as $item) {
            $animdelay = isset($item['delay']) != '' ? ' data-aos-delay="'.$item['delay'].'"' : '';
            $animoffset = isset($item['offset']) != '' ? ' data-aos-offset="'.$item['offset'].'"' : '';
            $animaos = isset($item['anim']) == 'yes' ? ' data-aos="'.$item['aos'].'"'.$animdelay.$animoffset : '';

            $out .= '<div class="col-6 '.$sm.' '.$lg.'">';
            $out .= '<div class="__item text-center"'.$animaos.'>';

            if (isset($item['img']) != '') {
                $out .= '<i class="__ico">';
                $blankimg = '';
                $out .= agro_img($item['img'], $blankimg, $imgclass='img-fluid', $atts['imgw'], $atts['imgh']);
                $out .= '</i>';
            }
            if (isset($item['title']) != '') {
                $out .= '<h5 class="__title">'. $item['title'] .'</h5>';
            }
            $out .= '</div>';
            $out .= '</div>';
        }

        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_features3', 'agro_vc_features3');
}

/*******************************/
/* features style 4
/******************************/
if (!function_exists('agro_vc_features4')) {
    function agro_vc_features4($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        'stitle' => '',
        'sthintitle' => '',
        'sdesc' => '',
        // product
        'loop' => '',
        'title' => '',
        'desc' => '',
        'lg' => '',
        'md' => '',
        'img' => '',
        'imgw' => '',
        'imgh' => '',
        // color
        'tclr' => '',
        'thclr' => '',
        'descclr' => '',
        'itclr' => '',
        'idescclr' => '',
        // background css
        'css' => '',
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
    ), $atts, 'agro_features4');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $uniq = 'section_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.section.'.$uniq.' .section-heading .__title {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.section.'.$uniq.' .section-heading .__title {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.section.'.$uniq.' .section-heading .__title {font-size:'. $tsize .';}' : '';

        $item_css[] = $atts['tclr'] ? '.section.'.$uniq.' .section-heading .__title {color: '. esc_html($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['thclr'] ? '.section.'.$uniq.' .section-heading .__title span{color: '. esc_attr($atts['thclr']) .'; }' : '';
        $item_css[] = $atts['descclr'] ? '.section.'.$uniq.' .section-heading p{color: '. esc_attr($atts['descclr']) .'; }' : '';
        $item_css[] = $atts['itclr'] ? '.section.'.$uniq.' .feature--style-2 .__item .__title {color: '. esc_attr($atts['itclr']) .'; }' : '';
        $item_css[] = $atts['idescclr'] ? '.section.'.$uniq.' .feature--style-2 .__item p {color: '. esc_attr($atts['idescclr']) .'; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';

        $out .= '<div class="section'. $uniq.$bgcss .'"'.$item_css.'>';
        $out .= '<div class="container">';
        if ($atts['stitle'] != '' || $atts['sthintitle'] != '' || $atts['sdesc'] != '') {
            $out .= '<div class="section-heading section-heading--center">';
            if ($atts['stitle'] != '' || $atts['sthintitle'] != '') {
                $out .= '<'.$htag.' class="__title">'. esc_html($atts['stitle']) .' <span>'. esc_html($atts['sthintitle']) .'</span></'.$htag.'>';
            }
            if ($atts['sdesc'] != '') {
                $out .= '<p>'. esc_html($atts['sdesc']) .'</p>';
            }

            $out .= '</div>';
        }

        $out .= '<div class="feature feature--style-2">';
        $out .= '<div class="__inner">';
        $out .= '<div class="row">';
        $lg = $atts['lg'] ? $atts['lg'] : 'col-lg-4';
        $md = $atts['md'] ? $atts['md'] : 'col-md-6';
        $loop = (array) vc_param_group_parse_atts($atts['loop']);
        foreach ($loop as $item) {
            $out .= '<div class="col-12 '.$md.' '.$lg.'">';

            $out .= '<div class="__item">';
            if (isset($item['img']) != '') {
                $out .= '<i class="__ico">';
                $blankimg = '';
                $out .= agro_img($item['img'], $blankimg, $imgclass='img-fluid', $atts['imgw'], $atts['imgh']);
                $out .= '</i>';
            }
            if (isset($item['title']) != '') {
                $out .= '<h5 class="__title">'. $item['title'] .'</h5>';
            }
            if (isset($item['desc']) != '') {
                $out .= '<p>'. $item['desc'] .'</p>';
            }

            $out .= '</div>';
            $out .= '</div>';
        }

        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_features4', 'agro_vc_features4');
}

/*******************************/
/* special offer 2
/******************************/
if (!function_exists('agro_vc_special_offer2')) {
    function agro_vc_special_offer2($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        // product
        'loop' => '',
        'title' => '',
        'titleclr' => '',
        'anim' => '',
        'aos' => '',
        'duration' => '',
        'offset' => '',
        // background css
        'css' => '',
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
        ), $atts, 'agro_special_offer2');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);
        $animduration= $atts['duration'] != '' ? ' data-aos-duration="'.$atts['duration'].'"' : '';
        $animoffset = $atts['offset'] != '' ? ' data-aos-offset="'.$atts['offset'].'"' : '';
        $animaos = $atts['anim'] == 'yes' ? ' data-aos="'.$atts['aos'].'"'.$animduration.$animoffset : '';

        $uniq = 'section_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.section.'.$uniq.' .text {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.section.'.$uniq.' .text {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.section.'.$uniq.' .text {font-size:'. $tsize .';}' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';
        $out .= '<div class="section section--no-pt'. $uniq.$bgcss .'"'.$item_css.'>';
        $out .= '<div class="container">';
        $out .= '<div class="special-offer special-offer--style-2"'.$animaos.'>';
        $out .= '<'.$htag.' class="text text-center">';
        $loop = (array) vc_param_group_parse_atts($atts['loop']);
        foreach ($loop as $item) {
            if (isset($item['title']) != '') {
                $out .= '<span style="color: '. $item['titleclr'] .'">'. $item['title'] .'</span> ';
            }
        }
        $out .= '</'.$htag.'>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_special_offer2', 'agro_vc_special_offer2');
}

/*******************************/
/* special offer 3
/******************************/
if (!function_exists('agro_vc_special_offer3')) {
    function agro_vc_special_offer3($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        // product
        'img' => '',
        'imgw' => '',
        'imgh' => '',
        'loop' => '',
        'title' => '',
        'titleclr' => '',
        'anim' => '',
        'aos' => '',
        'duration' => '',
        'offset' => '',
        // background css
        'css' => '',
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
    ), $atts, 'agro_special_offer3');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);
        $animduration= $atts['duration'] != '' ? ' data-aos-duration="'.$atts['duration'].'"' : '';
        $animoffset = $atts['offset'] != '' ? ' data-aos-offset="'.$atts['offset'].'"' : '';
        $animaos = $atts['anim'] == 'yes' ? ' data-aos="'.$atts['aos'].'"'.$animduration.$animoffset : '';

        $uniq = 'section_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.section.'.$uniq.' .text {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.section.'.$uniq.' .text {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.section.'.$uniq.' .text {font-size:'. $tsize .';}' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';

        $out .= '<div class="section'. $uniq.$bgcss .'"'.$item_css.'>';
        $out .= '<div class="container">';
        $out .= '<div class="special-offer special-offer--style-3"'.$animaos.'>';
        $out .= '<div class="row align-items-center">';
        $out .= '<div class="col-12 col-lg-6">';
        $out .= '<figure class="image">';
        $blankimg = '';
        $out .= agro_img($atts['img'], $blankimg, $imgclass='img-fluid', $atts['imgw'], $atts['imgh']);
        $out .= '</figure>';
        $out .= '</div>';

        $out .= '<div class="col-12 my-3 d-lg-none"></div>';

        $out .= '<div class="col-12 col-lg-6">';
        $out .= '<'.$htag.' class="text">';
        $loop = (array) vc_param_group_parse_atts($atts['loop']);
        foreach ($loop as $item) {
            if (isset($item['title']) != '') {
                $out .= '<span style="color: '. $item['titleclr'] .'">'. $item['title'] .'</span> ';
            }
        }
        $out .= '</'.$htag.'>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_special_offer3', 'agro_vc_special_offer3');
}


/*******************************/
/* parallaxtext
/******************************/
if (!function_exists('agro_vc_parallaxtext')) {
    function agro_vc_parallaxtext($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        'bgimg' => '',
        'bgimgw' => '',
        'bgimgh' => '',
        'stitle' => '',
        'sthintitle' => '',
        'desc' => '',
        'link' => '',
        'img1' => '',
        'img2' => '',
        'img3' => '',
        'img4' => '',
        // color
        'tclr' => '',
        'thclr' => '',
        'descclr' => '',
        'itclr' => '',
        'btnclr' => '',
        'btnhvrclr' => '',
        'btnbrd' => '',
        'btnhvrbrd' => '',
        'btnbg' => '',
        'btnhvrbg' => '',
        'btntype' => '',
        'btnstyle' => '',
        'btnsize' => '',
        // background css
        'css' => '',
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
    ), $atts, 'agro_parallaxtext');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $btntype = $atts['btntype'] ? ' '.$atts['btntype'] : '';
        $btnstyle = $atts['btnstyle'] ? ' '.$atts['btnstyle'] : ' custom-btn--style-4';
        $btnsize = $atts['btnsize'] ? ' '.$atts['btnsize'] : ' custom-btn--medium';
        $uniq = 'section_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.section.'.$uniq.' .text-white {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.section.'.$uniq.' .text-white {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.section.'.$uniq.' .text-white {font-size:'. $tsize .';}' : '';

        $item_css[] = $atts['tclr'] ? '.section.'.$uniq.' .text-white {color: '. esc_html($atts['tclr']) .' !important; }' : '';
        $item_css[] = $atts['thclr'] ? '.section.'.$uniq.' .text-white span{color: '. esc_attr($atts['thclr']) .'; }' : '';
        $item_css[] = $atts['descclr'] ? '.section.'.$uniq.' p.text-white {color: '. esc_attr($atts['descclr']) .' !important; }' : '';

        // btn
        $item_css[] = $atts['btnclr'] ? '.section.'.$uniq.' .custom-btn {color: '. esc_attr($atts['btnclr']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrclr'] ? '.section.'.$uniq.' .custom-btn:hover {color: '. esc_attr($atts['btnhvrclr']) .'!important; }' : '';
        $item_css[] = $atts['btnbrd'] ? '.section.'.$uniq.' .custom-btn {border-color: '. esc_attr($atts['btnbrd']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrbrd'] ? '.section.'.$uniq.' .custom-btn:hover {border-color: '. esc_attr($atts['btnhvrbrd']) .'!important; }' : '';
        $item_css[] = $atts['btnbg'] ? '.section.'.$uniq.' .custom-btn {background-color: '. esc_attr($atts['btnbg']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrbg'] ? '.section.'.$uniq.' .custom-btn:hover {background-color: '. esc_attr($atts['btnhvrbg']) .'!important; }' : '';

        $item_css[] = $atts['btntype'] == 'btn-rounded' ? '.section.'.$uniq.' .custom-btn.btn-rounded {border-radius: 10px!important; }' : '';
        $item_css[] = $atts['btntype'] == 'btn-square' ? '.section.'.$uniq.' .custom-btn.btn-square {border-radius: 0px !important; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';

        $out .= '<div class="section section--no-pt'. $uniq.$bgcss .'"'.$item_css.'>';
        $out .= '<div class="container">';
        $out .= '<div class="simple-text-block simple-text-block--a jarallax" data-speed="0.8" data-img-position="50% 100%">';
        $out .= agro_img($atts['bgimg'], $blankimg='', $imgclass='jarallax-img', $atts['bgimgw'], $atts['bgimgh']);

        $out .= '<div class="imgs d-none d-lg-block">';
        $blankimg = '';
        $out .= agro_img($atts['img1'], $blankimg, $imgclass='img_1', $atts['imgw1']='', $atts['imgh1']='');
        $out .= agro_img($atts['img2'], $blankimg, $imgclass='img_2', $atts['imgw2']='', $atts['imgh2']='');
        $out .= agro_img($atts['img3'], $blankimg, $imgclass='img_3', $atts['imgw3']='', $atts['imgh3']='');
        $out .= agro_img($atts['img4'], $blankimg, $imgclass='img_4', $atts['imgw4']='', $atts['imgh4']='');
        $out .= '</div>';

        $out .= '<div class="row justify-content-md-center">';
        $out .= '<div class="col-12 col-md-11">';

        $out .= '<div class="row no-gutters">';
        $out .= '<div class="col-12 col-md-9 col-lg-7">';
        if ($atts['stitle'] || $atts['sthintitle']) {
            $out .= '<'.$htag.' class="text-white">'. esc_html($atts['stitle']) .' <br><span>'. esc_html($atts['sthintitle']) .'</span></'.$htag.'>';
        }
        if ($atts['desc']) {
            $out .= '<p class="text-white">'. esc_html($atts['desc']) .'</p>';
        }

        $link = !empty($atts['link']) ? $atts['link'] : '';
        $link = vc_build_link($link);
        $atitle = $link['title'];
        $href = $link['url'] != '' ? ' href="'. esc_url($link['url']) .'"' : ' href="#"';
        $target = $link['target'] != '' ? ' target="'. esc_attr($link['target']) .'"' : '';

        if ($atitle != '') {
            $out .= '<p><a class="custom-btn'.$btnsize.$btnstyle.$btntype.'"'. $href . $target .'>'. esc_html($atitle) .'</a></p>';
        }

        $out .= '</div>';
        $out .= '</div>';

        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_parallaxtext', 'agro_vc_parallaxtext');
}

/*******************************/
/* parallaxtext 2
/******************************/
if (!function_exists('agro_vc_parallaxtext2')) {
    function agro_vc_parallaxtext2($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        'bgimg' => '',
        'bgimgw' => '',
        'dtype' => '',
        'bgimgh' => '',
        'stitle' => '',
        'sthintitle' => '',
        'desc' => '',
        'link' => '',
        // color
        'tclr' => '',
        'thclr' => '',
        'descclr' => '',
        'itclr' => '',
        'btnclr' => '',
        'btnhvrclr' => '',
        'btnbrd' => '',
        'btnhvrbrd' => '',
        'btnbg' => '',
        'btnhvrbg' => '',
        'btntype' => '',
        'btnstyle' => '',
        'btnsize' => '',
        // background css
        'css' => '',
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
    ), $atts, 'agro_parallaxtext2');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $btntype = $atts['btntype'] ? ' '.$atts['btntype'] : '';
        $btnstyle = $atts['btnstyle'] ? ' '.$atts['btnstyle'] : ' custom-btn--style-4';
        $btnsize = $atts['btnsize'] ? ' '.$atts['btnsize'] : ' custom-btn--medium';
        $uniq = 'section_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.section.'.$uniq.' .text-white {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.section.'.$uniq.'.text-white {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.section.'.$uniq.' .text-white {font-size:'. $tsize .';}' : '';

        $item_css[] = $atts['tclr'] ? '.section.'.$uniq.' .text-white {color: '. esc_html($atts['tclr']) .' !important; }' : '';
        $item_css[] = $atts['thclr'] ? '.section.'.$uniq.' .text-white span{color: '. esc_attr($atts['thclr']) .'; }' : '';
        $item_css[] = $atts['descclr'] ? '.section.'.$uniq.' p.text-white {color: '. esc_attr($atts['descclr']) .' !important; }' : '';

        // btn
        $item_css[] = $atts['btnclr'] ? '.section.'.$uniq.' .custom-btn {color: '. esc_attr($atts['btnclr']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrclr'] ? '.section.'.$uniq.' .custom-btn:hover {color: '. esc_attr($atts['btnhvrclr']) .'!important; }' : '';
        $item_css[] = $atts['btnbrd'] ? '.section.'.$uniq.' .custom-btn {border-color: '. esc_attr($atts['btnbrd']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrbrd'] ? '.section.'.$uniq.' .custom-btn:hover {border-color: '. esc_attr($atts['btnhvrbrd']) .'!important; }' : '';
        $item_css[] = $atts['btnbg'] ? '.section.'.$uniq.' .custom-btn {background-color: '. esc_attr($atts['btnbg']) .'!important; }' : '';
        $item_css[] = $atts['btnhvrbg'] ? '.section.'.$uniq.' .custom-btn:hover {background-color: '. esc_attr($atts['btnhvrbg']) .'!important; }' : '';

        $item_css[] = $atts['btntype'] == 'btn-rounded' ? '.section.'.$uniq.' .custom-btn.btn-rounded {border-radius: 10px!important; }' : '';
        $item_css[] = $atts['btntype'] == 'btn-square' ? '.section.'.$uniq.' .custom-btn.btn-square {border-radius: 0px !important; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';
        $out .= '<div class="section section--dark-bg jarallax'. $uniq.$bgcss .'" data-speed="0.5" data-img-position="50% 80%"'.$item_css.'>';
        $out .= agro_img($atts['bgimg'], $blankimg='', $imgclass='jarallax-img', $atts['bgimgw'], $atts['bgimgh']);

        $out .= '<div class="container">';
        $out .= '<div class="row">';
        $out .= '<div class="col-12 col-md-8 col-lg-6">';
        $out .= '<div data-aos="fade-left" data-aos-easing="ease-out-quad" data-aos-duration="700">';
        if ($atts['stitle'] || $atts['sthintitle']) {
            $out .= '<'.$htag.' class="text-white">'. esc_html($atts['stitle']) .' <br><span>'. esc_html($atts['sthintitle']) .'</span></'.$htag.'>';
        }
        if ($atts['dtype'] =='' || $atts['dtype'] =='d' && $atts['desc'] !='' ) {
            $out .= '<p class="text-white">'.esc_html($atts['desc']).'</p>';
        }elseif($atts['dtype'] =='custom'){
            $out .= '<div class="text-white">'.do_shortcode($content).'</div>';
        }

        $link = !empty($atts['link']) ? $atts['link'] : '';
        $link = vc_build_link($link);
        $atitle = $link['title'];
        $href = $link['url'] != '' ? ' href="'. esc_url($link['url']) .'"' : ' href="#"';
        $target = $link['target'] != '' ? ' target="'. esc_attr($link['target']) .'"' : '';

        if ($atitle != '') {
            $out .= '<p><a class="custom-btn'.$btnsize.$btnstyle.$btntype.'"'. $href . $target .'>'. esc_html($atitle) .'</a></p>';
        }

        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        $out .= '<div class="py-4 py-lg-10"></div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_parallaxtext2', 'agro_vc_parallaxtext2');
}


/*******************************/
/* product_preview
/******************************/
if (!function_exists('agro_vc_product_preview')) {
    function agro_vc_product_preview($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        // product
        'loop' => '',
        'img' => '',
        'imgw' => '',
        'imgh' => '',
        'title' => '',
        'link' => '',
        'sm' => '',
        'lg' => '',
        'overlay' => '',
        'tclr' => '',
        'thvrclr' => '',
        // background css
        'css' => '',
    ), $atts, 'agro_product_preview');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $uniq = 'section_'.uniqid();
        $item_css = array();
        $item_css[] = $atts['overlay'] ? '.section.'.$uniq.' .product-preview .__item:hover .__image+.__content {background-color: '. esc_attr($atts['overlay']) .'; }' : '';
        $item_css[] = $atts['tclr'] ? '.section.'.$uniq.' .product-preview .__item .__title {color: '. esc_attr($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['thvrclr'] ? '.section.'.$uniq.' .product-preview .__item:hover .__title {color: '. esc_attr($atts['thvrclr']) .'; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';

        $out .= '<div class="section section--no-pt section--no-pb section--gutter'. $uniq.$bgcss .'"'.$item_css.'>';
        $out .= '<div class="container-fluid px-md-0">';
        $out .= '<div class="product-preview product-preview--style-2">';
        $out .= '<div class="__inner">';
        $out .= '<div class="row">';

        $loop = (array) vc_param_group_parse_atts($atts['loop']);
        $lg = $atts['lg'] != '' ? $atts['lg'] : 'col-lg-3';
        $sm = $atts['sm'] != '' ? $atts['sm'] : 'col-sm-6';
        foreach ($loop as $item) {
            if (isset($item['img']) != '') {
                $out .= '<div class="col-12 '.$sm.' '.$lg.'">';
                $out .= '<div class="__item">';
                $out .= '<figure class="__image">';
                $blankimg = '';
                $out .= agro_img($item['img'], $blankimg, $imgclass='', $atts['imgw'], $atts['imgh']);
                $out .= '</figure>';

                $out .= '<div class="__content">';
                if (isset($item['title']) != '') {
                    $out .= '<h3 class="__title">'. $item['title'] .'</h3>';
                }

                $link = !empty($item['link']) ? $item['link'] : '';
                $link = vc_build_link($link);
                $atitle = $link['title'];
                $href = $link['url'] != '' ? ' href="'. esc_url($link['url']) .'"' : ' href="#"';
                $target = $link['target'] != '' ? ' target="'. esc_attr($link['target']) .'"' : '';
                if ($atitle != '') {
                    $out .= '<a class="__link"'. $href . $target .'></a>';
                }
                $out .= '</div>';
                $out .= '</div>';
                $out .= '</div>';
            }
        }

        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_product_preview', 'agro_vc_product_preview');
}

/*******************************/
/* product details
/******************************/
if (!function_exists('agro_vc_product_details')) {
    function agro_vc_product_details($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        'stitle' => '',
        'sthintitle' => '',
        'img' => '',
        'imgw' => '',
        'imgh' => '',
        // product
        'subtitle' => '',
        'title' => '',
        'loop' => '',
        'num' => '',
        'dtitle' => '',
        'tclr' => '',
        'thclr' => '',
        'descclr' => '',
        'stclr' => '',
        'mtclr' => '',
        'nclr' => '',
        'ntclr' => '',
        // background css
        'css' => '',
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
    ), $atts, 'agro_product_details');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $uniq = 'section_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.section.'.$uniq.' .simple-text-block .col-lg-10 '.$htag.' {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.section.'.$uniq.' .simple-text-block .col-lg-10 '.$htag.' {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.section.'.$uniq.' .simple-text-block .col-lg-10 '.$htag.' {font-size:'. $tsize .';}' : '';

        $item_css[] = $atts['tclr'] ? '.section.'.$uniq.' .simple-text-block h2 {color: '. esc_attr($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['thclr'] ? '.section.'.$uniq.' .simple-text-block h2 span {color: '. esc_attr($atts['thclr']) .'; }' : '';
        $item_css[] = $atts['descclr'] ? '.section.'.$uniq.' .simple-text-block {color: '. esc_attr($atts['descclr']) .'; }' : '';
        $item_css[] = $atts['stclr'] ? '.section.'.$uniq.' .product-description .__title{color: '. esc_attr($atts['stclr']) .'; }' : '';
        $item_css[] = $atts['mtclr'] ? '.section.'.$uniq.' .product-description .__name{color: '. esc_attr($atts['mtclr']) .'; }' : '';
        $item_css[] = $atts['nclr'] ? '.section.'.$uniq.' .__details__inner .__num{color: '. esc_attr($atts['nclr']) .'; }' : '';
        $item_css[] = $atts['ntclr'] ? '.section.'.$uniq.' .__details__inner .__title{color: '. esc_attr($atts['ntclr']) .'; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';

        $out .= '<div class="section section--gray-bg'. $uniq.$bgcss .'"'.$item_css.'>';
        $out .= '<div class="container">';
        $out .= '<div class="simple-text-block">';
        $out .= '<div class="row justify-content-lg-center row--lg-middle">';
        $out .= '<div class="col-lg-10">';
        if ($atts['stitle'] || $atts['sthintitle']) {
            $out .= '<'.$htag.'>'. $atts['stitle'] .' <span>'. $atts['sthintitle'] .'</span></'.$htag.'>';
        }

        $out .= '<div class="row justify-content-lg-between no-gutters">';
        $out .= '<div class="col-12 col-lg-6">';
        $out .= do_shortcode($content);

        $out .= '</div>';

        $out .= '<div class="col-12 my-3 d-lg-none"></div>';

        $out .= '<div class="col-12 col-lg-5">';
        $blankimg = '';
        $out .= agro_img($atts['img'], $blankimg, $imgclass='img-fluid', $atts['imgw'], $atts['imgh']);
        $out .= '</div>';
        $out .= '</div>';

        $out .= '<div class="product-description">';
        $out .= '<div class="my-5">';
        if ($atts['subtitle']) {
            $out .= '<div class="__title">'. $atts['subtitle'] .'</div>';
        }
        if ($atts['subtitle']) {
            $out .= '<div class="__name">'. $atts['title'] .'</div>';
        }
        $out .= '</div>';

        $out .= '<div class="__details">';
        $out .= '<div class="__details__inner">';
        $out .= '<div class="row">';
        $loop = (array) vc_param_group_parse_atts($atts['loop']);
        foreach ($loop as $item) {
            $out .= '<div class="col-12 col-sm-auto">';
            $out .= '<div class="__details__item">';
            if (isset($item['num']) != '') {
                $out .= '<span class="__num">'. $item['num'] .'</span>';
            }
            if (isset($item['dtitle']) != '') {
                $out .= '<div class="__title">'. $item['dtitle'] .'</div>';
            }
            $out .= '</div>';
            $out .= '</div>';
        }

        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_product_details', 'agro_vc_product_details');
}

/************************************/
/* product features_container nested
/***********************************/
if (!function_exists('agro_vc_product_features_container')) {
    function agro_vc_product_features_container($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        // background css
        'css' => '',
    ), $atts, 'agro_product_features_container');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $out = '';
        $out .= '<div class="section'. $bgcss .'">';
        $out .= '<div class="container-fluid">';
        $out .= '<div class="product-features">';

        $out .= do_shortcode($content);

        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_product_features_container', 'agro_vc_product_features_container');
}

/************************************/
/* product features item nested
/***********************************/
if (!function_exists('agro_vc_product_features_item')) {
    function agro_vc_product_features_item($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        'title' => '',
        'thintitle' => '',
        'img' => '',
        'imgw' => '',
        'imgh' => '',
        'tclr' => '',
        'thclr' => '',
        'descclr' => '',
        'hidecounter' => '',
        // background css
        'css' => '',
    ), $atts, 'agro_product_features_item');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $uniq = 'item_'.uniqid();
        $item_css = array();
        $hidecounter = $atts['hidecounter'] == 'yes' ? ' hide-counter' : '';
        $item_css[] = $atts['tclr'] ? '.__item.'.$uniq.' .__content h3 {color: '. esc_attr($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['thclr'] ? '.__item.'.$uniq.' .__content h3 span {color: '. esc_attr($atts['thclr']) .'; }' : '';
        $item_css[] = $atts['descclr'] ? '.__item.'.$uniq.' .__content p{color: '. esc_attr($atts['descclr']) .'; }' : '';
        $item_css[] = $atts['hidecounter'] == 'yes' ? '.__item.'.$uniq.' .__content.hide-counter>div:before {content: none !important; }' : '';
        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';

        if ($atts['img']) {
            $out .= '<div class="__item'. $uniq.$bgcss .'"'.$item_css.'>';
            $out .= '<div class="__inner">';
            $out .= '<div class="row justify-content-sm-center">';
            $out .= '<div class="col-12 col-md-9 col-lg-6">';
            $out .= '<div class="__content'.$hidecounter.'">';
            if ($atts['title'] || $atts['thintitle']) {
                $out .= '<h3 class="__title">'. $atts['title'] .' <span>'. $atts['thintitle'] .'</span></h3>';
            }
            $out .= '<div>';
            $out .= wpautop(do_shortcode($content));
            $out .= '</div>';

            $out .= '</div>';
            $out .= '</div>';

            $out .= '<div class="col-12 col-md-9 col-lg-6">';
            $out .= '<div class="__image">';
            $blankimg = '';
            $out .= agro_img($atts['img'], $blankimg, $imgclass='img-fluid', $atts['imgw'], $atts['imgh']);
            $out .= '</div>';
            $out .= '</div>';

            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';
        }

        return $out;
    }
    add_shortcode('agro_product_features_item', 'agro_vc_product_features_item');
}


/*******************************/
/* recipes
/******************************/
if (!function_exists('agro_vc_recipes')) {
    function agro_vc_recipes($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        // product
        'lg' => '',
        'imgw' => '',
        'imgh' => '',
        'loop' => '',
        'img1' => '',
        'title1' => '',
        'desc1' => '',
        'link1' => '',
        'img2' => '',
        'title2' => '',
        'link2' => '',
        'desc2' => '',
        'tclr' => '',
        'thvrclr' => '',
        'descclr' => '',
        // background css
        'css' => '',
    ), $atts, 'agro_recipes');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $uniq = 'section_'.uniqid();
        $item_css = array();
        $item_css[] = $atts['tclr'] ? '.section.'.$uniq.' .recipes .__title {color: '. esc_attr($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['thvrclr'] ? '.section.'.$uniq.' .recipes .__title a:hover {color: '. esc_attr($atts['thvrclr']) .'; }' : '';
        $item_css[] = $atts['descclr'] ? '.section.'.$uniq.' .recipes  p{color: '. esc_attr($atts['descclr']) .'; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';
        $out .= '<div class="section section--no-pt section--gray-bg'. $uniq.$bgcss .'"'.$item_css.'>';
        $out .= '<div class="container">';
        $out .= '<div class="recipes">';
        $out .= '<div class="__inner">';
        $out .= '<div class="row no-gutters">';

        $loop = (array) vc_param_group_parse_atts($atts['loop']);
        $lg = $atts['lg'] != '' ? $atts['lg'] : 'col-lg-6';
        foreach ($loop as $item) {
            $out .= '<div class="col-12 '.$lg.'">';
            $out .= '<div class="__item">';
            $out .= '<div class="row no-gutters align-items-center">';
            $out .= '<div class="col-12 col-md-6 align-self-md-stretch">';
            if (isset($item['img1']) != '') {
                $out .= '<figure class="__image">';
                $blankimg = '';
                $out .= agro_img($item['img1'], $blankimg, $imgclass='', $atts['imgw'], $atts['imgh']);
                $out .= '</figure>';
            }
            $out .= '</div>';

            $out .= '<div class="col-12 col-md-6">';
            $out .= '<div class="__content">';

            $link1 = !empty($item['link1']) ? $item['link1'] : '';
            $link1 = vc_build_link($link1);
            $atitle1 = $link1['title'];
            $href1 = $link1['url'] != '' ? ' href="'. esc_url($link1['url']) .'"' : ' href="#"';
            $target1 = $link1['target'] != '' ? ' target="'. esc_attr($link1['target']) .'"' : '';
            if ($href1 != '') {
                $out .= '<h3 class="__title h5"><a'. $href1 . $target1 .'>'. $item['title1'] .'</a></h3>';
            } else {
                $out .= '<h3 class="__title h5">'. $item['title1'] .'</h3>';
            }
            if (isset($item['desc1']) != '') {
                $out .= '<p>'. $item['desc1'] .'</p>';
            }
            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';

            $out .= '<div class="__item">';
            $out .= '<div class="row flex-md-row-reverse no-gutters align-items-center">';
            $out .= '<div class="col-12 col-md-6 align-self-md-stretch">';
            if (isset($item['img2']) != '') {
                $out .= '<figure class="__image">';
                $blankimg = '';
                $out .= agro_img($item['img2'], $blankimg, $imgclass='', $atts['imgw'], $atts['imgh']);
                $out .= '</figure>';
            }
            $out .= '</div>';

            $out .= '<div class="col-12 col-md-6">';
            $out .= '<div class="__content">';
            $link2 = !empty($item['link2']) ? $item['link2'] : '';
            $link2 = vc_build_link($link2);
            $atitle2 = $link2['title'];
            $href2 = $link2['url'] != '' ? ' href="'. esc_url($link2['url']) .'"' : ' href="#"';
            $target2 = $link2['target'] != '' ? ' target="'. esc_attr($link2['target']) .'"' : '';

            if ($href2 != '') {
                $out .= '<h3 class="__title h5"><a'. $href2 . $target2 .'>'. $item['title2'] .'</a></h3>';
            } else {
                $out .= '<h3 class="__title h5">'. $item['title2'] .'</h3>';
            }
            if (isset($item['desc2']) != '') {
                $out .= '<p>'.$item['desc2'].'</p>';
            }
            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';
            $out .= '</div>';
        }


        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';


        return $out;
    }
    add_shortcode('agro_recipes', 'agro_vc_recipes');
}

/*******************************/
/* gallery 1
/******************************/
if (!function_exists('agro_vc_gallery1')) {
    function agro_vc_gallery1($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        'loop' => '',
        'title' => '',
        'xl' => '',
        'md' => '',
        'sm' => '',
        'img' => '',
        'imgw' => '',
        'imgh' => '',
        'overlay' => '',
        'tclr' => '',
        // background css
        'css' => '',
    ), $atts, 'agro_gallery1');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $uniq = 'section_'.uniqid();
        $item_css = array();
        $item_css[] = $atts['overlay'] ? '.section.'.$uniq.' .gallery--style-3 .__image .__content {background-color: '. esc_attr($atts['overlay']) .'; }' : '';
        $item_css[] = $atts['tclr'] ? '.section.'.$uniq.' .gallery--style-3 .__item .__content__title {color: '. esc_attr($atts['tclr']) .'; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';

        $out .= '<div class="section section--no-pt section--no-pb'. $uniq.$bgcss .'"'.$item_css.'>';
        $out .= '<div class="gallery gallery--style-3">';
        $out .= '<div class="__inner">';
        $out .= '<div class="row no-gutters">';

        $xl = $atts['xl'] ? $atts['xl'] : 'col-xl-3';
        $md = $atts['md'] ? $atts['md'] : 'col-md-4';
        $sm = $atts['sm'] ? $atts['sm'] : 'col-sm-6';
        $loop = (array) vc_param_group_parse_atts($atts['loop']);
        foreach ($loop as $item) {
            if (isset($item['img']) != '') {
                $out .= '<div class="col-12 '.$sm.' '.$md.' '.$xl.'">';
                $out .= '<div class="__item">';
                $out .= '<figure class="__image">';

                $blankimg = '';
                $out .= agro_img($item['img'], $blankimg, $imgclass='img-fluid', $atts['imgw'], $atts['imgh']);
                if (isset($item['title']) != '') {
                    $out .= '<div class="__content">';
                    $out .= '<h5 class="__content__title">'. $item['title'] .'</h5>';
                    $out .= '</div>';
                }
                $fullurl = wp_get_attachment_url($item['img'], '');
                $out .= '<a class="__link" data-fancybox="gallery" href="'.esc_url($fullurl).'"></a>';
                $out .= '</figure>';
                $out .= '</div>';
                $out .= '</div>';
            }
        }

        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_gallery1', 'agro_vc_gallery1');
}

/*******************************/
/* gallery 2
/******************************/
if (!function_exists('agro_vc_gallery2')) {
    function agro_vc_gallery2($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        'style' => '',
        'loop' => '',
        'alltitle' => 'All',
        'filtertitle' => '',
        'align' => 'text-center',
        'loop2' => '',
        'h2y' => '',
        'title' => '',
        'desc' => '',
        'column' => '',
        'img' => '',
        'imgw' => '',
        'imgh' => '',
        'fclr' => '',
        'factclr' => '',
        'overlay' => '',
        'tclr' => '',
        'catclr' => '',
        // background css
        'css' => '',
    ), $atts, 'agro_gallery2');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $uniq = 'section_'.uniqid();
        $item_css = array();
        $item_css[] = $atts['fclr'] ? '.section.'.$uniq.' #gallery-set a {color: '. esc_attr($atts['fclr']) .'; }' : '';
        $item_css[] = $atts['factclr'] ? '.section.'.$uniq.' #gallery-set a.selected,.section.'.$uniq.' #gallery-set a:hover {color: '. esc_attr($atts['factclr']) .'; }' : '';
        $item_css[] = $atts['overlay'] ? '.section.'.$uniq.' .gallery .__image .__content {background-color: '. esc_attr($atts['overlay']) .'; }' : '';
        $item_css[] = $atts['tclr'] ? '.section.'.$uniq.' .gallery--style-1 .__item .__content__title {color: '. esc_attr($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['catclr'] ? '.section.'.$uniq.' .gallery--style-1 .__item .__content span {color: '. esc_attr($atts['catclr']) .'; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';
        $out .= '<div class="section'. $uniq.$bgcss .'"'.$item_css.'>';
        $out .= '<div class="container">';
        $out .= '<ul id="gallery-set" class="'.$atts['align'].'">';
        $out .= '<li><a class="selected" data-cat="*" href="#">'.$atts['alltitle'].'</a></li>';
        $loop = (array) vc_param_group_parse_atts($atts['loop']);
        $countcat = 1;
        foreach ($loop as $item) {
            $out .= '<li><a data-cat="category-'.$countcat.'" href="#">'.$item['filtertitle'].'</a></li>';
            $countcat++;
        }
        $out .= '</ul>';

        $out .= '<div class="gallery gallery--style-1">';
        $out .= '<div class="__inner">';
        $out .= '<div class="row  js-isotope" data-isotope-options=\'{"itemSelector": ".js-isotope__item","transitionDuration": "0.8s","percentPosition": "true",	"masonry": { "columnWidth": ".js-isotope__sizer"}}\'>';

        $lg = $atts['column'] ? $atts['column'] : '4';
        $out .= '<div class="col-12 col-sm-6 col-lg-'.$lg.'  js-isotope__sizer"></div>';
        $countcat2 = 1;
        $loop = (array) vc_param_group_parse_atts($atts['loop']);
        foreach ($loop as $item) {
            $loop2 = (array) vc_param_group_parse_atts($item['loop2']);
            foreach ($loop2 as $item2) {
                $out .= '<div class="col-12 col-sm-6 col-lg-'.$lg.' js-isotope__item  category-'.$countcat2.'">';
                if ($atts['style'] == '1') {
                    $datay = isset($item2['h2y']) == 'yes' ? ' data-y="2"' : '';
                    $out .= '<div class="__item" '.$datay.'>';
                    $out .= '<figure class="__image">';
                    $blankimg = '';
                    $out .= agro_img($item2['img'], $blankimg, $imgclass='', $atts['imgw'], $atts['imgh']);

                    $out .= '<div class="__content">';
                    if (isset($item2['title']) != '') {
                        $out .= '<h5 class="__content__title">'. $item2['title'] .'</h5>';
                    }
                    if (isset($item2['desc']) != '') {
                        $out .= '<span>'. $item2['desc'] .'</span>';
                    }
                    $out .= '</div>';
                    $fullurl = wp_get_attachment_url($item2['img'], 'full');
                    $out .= '<a class="__link" data-fancybox="gallery" href="'.esc_url($fullurl).'"></a>';
                    $out .= '</figure>';
                    $out .= '</div>';
                } else {
                    $datay = isset($item2['h2y']) == 'yes' ? ' data-y="2"' : '';
                    $out .= '<div class="__item" '.$datay.'>';
                    $out .= '<figure class="__image">';
                    $blankimg = '';
                    $out .= agro_img($item2['img'], $blankimg, $imgclass='', $atts['imgw'], $atts['imgh']);
                    $fullurl = wp_get_attachment_url($item2['img'], 'full');
                    $out .= '<a class="__link" data-fancybox="gallery" href="'.esc_url($fullurl).'"></a>';
                    $out .= '</figure>';

                    $out .= '<div class="__content">';
                    if (isset($item2['title']) != '') {
                        $out .= '<h5 class="__content__title">'. $item2['title'] .'</h5>';
                    }
                    if (isset($item2['desc']) != '') {
                        $out .= '<span>'. $item2['desc'] .'</span>';
                    }
                    $out .= '</div>'; // end content

                    $out .= '</div>'; // end item
                }


                $out .= '</div>';
            }
            $countcat2++;
        }

        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_gallery2', 'agro_vc_gallery2');
}


/*******************************/
/* contac details
/******************************/
if (!function_exists('agro_vc_contact_details')) {
    function agro_vc_contact_details($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        'lgimg' => '',
        'lgimgw' => '',
        'lgimgh' => '',
        'stitle' => '',
        'sthintitle' => '',
        'link' => '',
        'bgimg' => '',
        'bgimgw' => '',
        'bgimgh' => '',
        // product
        'loop' => '',
        'title' => '',
        'lg' => '',
        'md' => '',
        'iconclr' => '',
        'tclr' => '',
        'descclr' => '',

        // background css
        'css' => '',
        ), $atts, 'agro_contact_details');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $uniq = 'section_'.uniqid();
        $item_css = array();

        $item_css[] = $atts['iconclr'] ? '.section.'.$uniq.' .company-contacts .__item .__ico {color: '. esc_attr($atts['iconclr']) .'; }' : '';
        $item_css[] = $atts['tclr'] ? '.section.'.$uniq.' .company-contacts .__item .__title {color: '. esc_attr($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['descclr'] ? '.section.'.$uniq.' .company-contacts .__item p {color: '. esc_attr($atts['descclr']) .'; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';

        $out .= '<div class="section'. $uniq.$bgcss .'"'.$item_css.'>';
        $out .= '<div class="container">';
        $out .= '<div class="company-contacts  text-center">';
        $out .= '<div class="__inner">';
        $out .= '<div class="row justify-content-around">';

        $loop = (array) vc_param_group_parse_atts($atts['loop']);
        foreach ($loop as $item) {
            $lg = isset($item['lg']) != '' ? $item['lg'] : 'col-lg-3';
            $md = isset($item['md']) != '' ? $item['md'] : 'col-md-4';

            $out .= '<div class="col-12 '.$md.' '.$lg.'">';
            $out .= '<div class="__item">';
            if (isset($item['icon']) != '') {
                if (isset($item['icontype']) == 'sb') {
                $out .= '<i class="__ico '. $item['icons'] .'"></i>';
                }else{
                $out .= '<i class="__ico '. $item['icon'] .'"></i>';
                }
            }
            if (isset($item['title']) != '') {
                $out .= '<h4 class="__title">'. $item['title'] .'</h4>';
            }
            if (isset($item['desc']) != '') {
                $out .= '<p>'. $item['desc'] .'</p>';
            }
            $out .= '</div>';
            $out .= '</div>';
        }

        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_contact_details', 'agro_vc_contact_details');
}


/*******************************/
/* footer shortcode
/******************************/
if (!function_exists('agro_vc_footer3')) {
    function agro_vc_footer3($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        // section data
        'useiframe' => '',
        'iframemap' => '',
        'apikey' => '',
        'longitude' => '',
        'latitude' => '',
        'markerimg' => '',
        'minh' => '255',
        // background css
        'css' => '',
    ), $atts, 'agro_footer3');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $out = '';

        $out .= '<footer id="footer" class="footer footer--style-3'. $bgcss .'" style="min-height: '.$atts['minh'].'px">';
        // custom html content for footer
        $out .= do_shortcode($content);

        $out .= '<div class="map-container">';

        if ($atts['useiframe'] == 'yes' ) {

            $out .= '<div class="custom-map"><iframe src="'.$atts['iframemap'].'" width="'.$atts['minh'].'" height="'.$atts['minh'].'" frameborder="0" style="border:0" allowfullscreen></iframe></div>';

        } else {

            $markerimg = wp_get_attachment_url($atts['markerimg'], 'full');
            $out .= '<div class="g_map" data-api-key="'.$atts['apikey'].'" data-longitude="'.$atts['longitude'].'" data-latitude="'.$atts['latitude'].'" data-marker="'.esc_url($markerimg).'"></div>';

        }

        $out .= '</div>';

        $out .= '</footer>';

        return $out;
    }
    add_shortcode('agro_footer3', 'agro_vc_footer3');
}


/*******************************/
/* woo bestseller shortcode
/******************************/
if (!function_exists('agro_vc_woo_bestseller')) {
    function agro_vc_woo_bestseller($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        'style'=> '',
        'build_query'=> '',
        'pag'=> '',
        'thintitle' => '',
        'title' => '',
        'desc' => '',
        'spb' => '',
        'spt' => '',
        // thumb options
        'hidethumb' => '',
        'imgw'=> '',
        'imgh'=> '',
        'fullthumb' => '',
        'hidetitle' => '',
        // color
        'tclr' => '',
        'thclr' => '',
        'descclr' => '',
        'catclr' => '',
        'ptclr' => '',
        'pthclr' => '',
        'starclr' => '',
        'discbg' => '',
        'discclr' => '',
        'priceclr' => '',
        'priceclr2' => '',
        'contentbg' => '',
        'btnclr' => '',
        'btnhvrclr' => '',
        'btnbrd' => '',
        'btnhvrbrd' => '',
        'btnbg' => '',
        'btnhvrbg' => '',
        'btntype' => '',
        'btnstyle' => '',
        'btnsize' => '',
        // background css
        'css' => '',
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
        ), $atts, 'agro_woo_bestseller');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $uniq = 'blog_'.uniqid();
        $item_css = array();

        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.section-woo.'.$uniq.' .section-heading .__title {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.section-woo.'.$uniq.' .section-heading .__title {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.section-woo.'.$uniq.' .section-heading .__title {font-size:'. $tsize .';}' : '';

        $item_css[] = $atts['tclr'] ? '.section-woo.'.$uniq.' .section-heading .__title {color: '. esc_html($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['thclr'] ? '.section-woo.'.$uniq.' .section-heading .__title span{color: '. esc_attr($atts['thclr']) .'; }' : '';
        $item_css[] = $atts['descclr'] ? '.section-woo.'.$uniq.' .section-heading p{color: '. esc_attr($atts['descclr']) .'; }' : '';
        $item_css[] = $atts['ptclr'] ? '.section-woo.'.$uniq.' .__item .__title a{color: '. esc_attr($atts['ptclr']) .'; }' : '';
        $item_css[] = $atts['pthclr'] ? '.section-woo.'.$uniq.' .__item .__title a:hover{color: '. esc_attr($atts['pthclr']) .'; }' : '';
        $item_css[] = $atts['starclr'] ? '.section-woo.'.$uniq.' .__item .woocommerce .star-rating{color: '. esc_attr($atts['starclr']) .'; }' : '';
        $item_css[] = $atts['priceclr'] ? '.section-woo.'.$uniq.' .__item .product-price__item--new{color: '. esc_attr($atts['priceclr']) .'; }' : '';
        $item_css[] = $atts['priceclr2'] ? '.section-woo.'.$uniq.' .__item .product-price__item--old{color: '. esc_attr($atts['priceclr2']) .'; }' : '';
        // btn
        $item_css[] = $atts['btnclr'] ? '.section-woo.'.$uniq.' .goods-btn a {color: '. esc_attr($atts['btnclr']) .'; }' : '';
        $item_css[] = $atts['btnhvrclr'] ? '.section-woo.'.$uniq.' .goods-btn a:hover {color: '. esc_attr($atts['btnhvrclr']) .'; }' : '';
        $item_css[] = $atts['btnbrd'] ? '.section-woo.'.$uniq.' .goods-btn a {border-color: '. esc_attr($atts['btnbrd']) .'; }' : '';
        $item_css[] = $atts['btnhvrbrd'] ? '.section-woo.'.$uniq.' .goods-btn a:hover {border-color: '. esc_attr($atts['btnhvrbrd']) .'; }' : '';
        $item_css[] = $atts['btnbg'] ? '.section-woo.'.$uniq.' .goods-btn a {background-color: '. esc_attr($atts['btnbg']) .'; }' : '';
        $item_css[] = $atts['btnhvrbg'] ? '.section-woo.'.$uniq.' .goods-btn a:hover {background-color: '. esc_attr($atts['btnhvrbg']) .'; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        list($args) = vc_build_loop_query($atts['build_query']);
        $args['post_type'] = 'product';

        $out = '';

        $spb = ($atts['spb'] == 'hide') ? ' section--no-pb' : '';
        $spt = ($atts['spt'] == 'hide') ? ' section--no-pt' : '';

        $out .= '<div class="section section-woo'.$spt.$spb.$uniq.$bgcss.'"'.$item_css.'>';
			$out .= '<div class="container">';
                if ($atts['title'] || $atts['thintitle'] || $atts['desc']) {
                    $out .= '<div class="section-heading section-heading--center" data-aos="fade">';
                    // section title
                    if ($atts['title'] || $atts['thintitle']) {
                        $out .= '<'.$htag.' class="__title">'. esc_html($atts['title']) .' <span>'. esc_html($atts['thintitle']) .'</span></'.$htag.'>';
                    }
                    // section description
                    if ($atts['desc']) {
                        $out .= '<p>'. esc_html($atts['desc']) .'</p>';
                    }
                    $out .= '</div>';
                }
                if ($atts['style'] == '1') {
    				$out .= '<div class="goods goods--style-2">';
    					$out .= '<div class="__inner">';
    						$out .= '<div class="row justify-content-sm-center">';
                            // The Query
                            $agro_query = new WP_Query($args);
                            if ($agro_query->have_posts()) {
                                while ($agro_query->have_posts()) {
                                    $agro_query->the_post();
                                    global $product;
                                    if (has_post_thumbnail()) {
            							$out .= '<div class="col-12 col-sm-6 col-lg-5 col-xl-4">';
            								$out .= '<div class="__item">';
            									$out .= '<div class="row">';
            										$out .= '<div class="col-12 col-md-6">';
            											$out .= '<figure class="__image">';
                                                            if ($atts['fullthumb'] == 'yes') {
                                                                $out .= get_the_post_thumbnail($post->ID, 'full');
                                                            } else {
                                                                $posthumbfull = wp_get_attachment_url(get_post_thumbnail_id(), 'full');
                                                                $posthumb = $atts['imgw'] && $atts['imgh'] ? ntframework_aq_resize($posthumbfull, $img_w, $img_h, true, true, true) : $posthumbfull;
                                                                $out .= '<img src="'.$posthumb.'" alt="'.get_the_title().'" />';
                                                            }
            											$out .= '</figure>';
            										$out .= '</div>';

            										$out .= '<div class="col-12 col-md-6">';
            											$out .= '<div class="__content">';
            												$out .= '<h4 class="h6 __title"><a href="'.get_permalink().'">'.get_the_title().'</a></h4>';
                                                            if(($product->get_rating_count()) > 0 ) {
                                                                $out .= '<div class="rating woocommerce">';
                                                                $average = $product->get_average_rating();
                                                                $out .= '<div class="star-rating" title="'.sprintf(__( 'Rated %s out of 5', 'agro' ), $average).'"><span style="width:'.( ( $average / 5 ) * 100 ) . '%"><strong itemprop="ratingValue" class="rating">'.$average.'</strong> '.__( 'out of 5', 'agro' ).'</span></div>';
                                                                $out .= '</div>';
                                                            }
            												$out .= '<div class="product-price">';
                                                                if ( ! empty( $product->get_sale_price() ) ) {
                                                                    if ( ! empty( $product->get_sale_price() ) ) {
                                                                        $out .= '<span class="product-price__item product-price__item--new">'.esc_html(get_woocommerce_currency_symbol()).' '.esc_html($product->get_sale_price()).'</span>';
                                                                    }
                                                                    $out .= '<span class="product-price__item product-price__item--old">'.esc_html(get_woocommerce_currency_symbol()).' '.esc_html($product->get_regular_price()).'</span>';
                                                                } else {
                                                                    if ( ! empty( $product->get_regular_price() ) ) {
                                                                        $out .= '<span class="product-price__item product-price__item--new">'.esc_html(get_woocommerce_currency_symbol()).' '.esc_html($product->get_regular_price()).'</span>';
                                                                    }
                                                                }
            												$out .= '</div>';

                                                                ob_start();
                                                                woocommerce_template_loop_add_to_cart();
                                                            $out .=  '<div class="goods-btn">'.ob_get_clean().'</div>';
            											$out .= '</div>';
            										$out .= '</div>';
            									$out .= '</div>';
                                                // Only on sale products on frontend and excluding min/max price on variable products
                                                if( $product->is_on_sale() && ! is_admin() && ! $product->is_type('variable')){
                                                    if ( ! empty( $product->get_regular_price() ) && ! empty( $product->get_sale_price() ) ) {
                                                        // Get product prices
                                                        $regular_price = (float) $product->get_regular_price(); // Regular price
                                                        $sale_price = (float) $product->get_price(); // Active price (the "Sale price" when on-sale)

                                                        // "Saving price" calculation and formatting
                                                        $saving_price = wc_price( $regular_price - $sale_price );

                                                        // "Saving Percentage" calculation and formatting
                                                        $precision = 1; // Max number of decimals
                                                        $saving_percentage = round( 100 - ( $sale_price / $regular_price * 100 ), 1 );
                                                        // Append to the formated html price
                                                        $out .= '<span class="product-label product-label--sale">-'.$saving_percentage.'%</span>';
                                                    }
                                                }
            								$out .= '</div>';
            							$out .= '</div>';
                                    }
                                }
                                wp_reset_postdata();
                            }
    						$out .= '</div>';
    					$out .= '</div>';
    				$out .= '</div>';
                } else {

    				$out .= '<div class="goods goods--style-3">';
    					$out .= '<div class="__inner">';
    						$out .= '<div class="row">';
                            // The Query
                            $agro_query = new WP_Query($args);
                            if ($agro_query->have_posts()) {
                                while ($agro_query->have_posts()) {
                                    $agro_query->the_post();
                                    global $product;
                                    if (has_post_thumbnail()) {
            							$out .= '<div class="col-12 col-sm-6 col-lg-3">';
            								$out .= '<div class="__item">';
            									$out .= '<figure class="__image">';
                                                if ($atts['fullthumb'] == 'yes') {
                                                    $out .= get_the_post_thumbnail($post->ID, 'full');
                                                } else {
                                                    $posthumbfull = wp_get_attachment_url(get_post_thumbnail_id(), 'full');
                                                    $posthumb = $atts['imgw'] && $atts['imgh'] ? ntframework_aq_resize($posthumbfull, $img_w, $img_h, true, true, true) : $posthumbfull;
                                                    $out .= '<img src="'.$posthumb.'" alt="'.get_the_title().'" />';
                                                }
            									$out .= '</figure>';

            									$out .= '<div class="__content">';
            										$out .= '<h4 class="h6 __title"><a href="'.get_permalink().'">'.get_the_title().'</a></h4>';
            										$out .= '<div class="__category">';
                                                        $out .= wc_get_product_category_list( get_the_id(), '', '',  '' );
                                                    $out .= '</div>';
                                                    if(($product->get_rating_count()) > 0 ) {
                                                        $out .= '<div class="rating woocommerce">';
                                                        $average = $product->get_average_rating();
                                                        $out .= '<div class="star-rating" title="'.sprintf(__( 'Rated %s out of 5', 'agro' ), $average).'"><span style="width:'.( ( $average / 5 ) * 100 ) . '%"><strong itemprop="ratingValue" class="rating">'.$average.'</strong> '.__( 'out of 5', 'agro' ).'</span></div>';
                                                        $out .= '</div>';
                                                    }

            										$out .= '<div class="product-price">';
                                                        if ( ! empty( $product->get_sale_price() ) ) {
                                                            if ( ! empty( $product->get_sale_price() ) ) {
                                                                $out .= '<span class="product-price__item product-price__item--new">'.esc_html(get_woocommerce_currency_symbol()).' '.esc_html($product->get_sale_price()).'</span>';
                                                            }
                                                            $out .= '<span class="product-price__item product-price__item--old">'.esc_html(get_woocommerce_currency_symbol()).' '.esc_html($product->get_regular_price()).'</span>';
                                                        } else {
                                                            if ( ! empty( $product->get_regular_price() ) ) {
                                                                $out .= '<span class="product-price__item product-price__item--new">'.esc_html(get_woocommerce_currency_symbol()).' '.esc_html($product->get_regular_price()).'</span>';
                                                            }
                                                        }
            										$out .= '</div>';

                                                    ob_start();
                                                    woocommerce_template_loop_add_to_cart();
                                                    $out .=  '<div class="goods-btn">'.ob_get_clean().'</div>';
            									$out .= '</div>';
                                                // Only on sale products on frontend and excluding min/max price on variable products
                                                if( $product->is_on_sale() && ! is_admin() && ! $product->is_type('variable')){
                                                    if ( ! empty( $product->get_regular_price() ) && ! empty( $product->get_sale_price() ) ) {
                                                        // Get product prices
                                                        $regular_price = (float) $product->get_regular_price(); // Regular price
                                                        $sale_price = (float) $product->get_price(); // Active price (the "Sale price" when on-sale)

                                                        // "Saving price" calculation and formatting
                                                        $saving_price = wc_price( $regular_price - $sale_price );

                                                        // "Saving Percentage" calculation and formatting
                                                        $precision = 1; // Max number of decimals
                                                        $saving_percentage = round( 100 - ( $sale_price / $regular_price * 100 ), 1 );
                                                        // Append to the formated html price
                                                        $out .= '<span class="product-label product-label--sale">-'.$saving_percentage.'%</span>';
                                                    }
                                                }
            								$out .= '</div>';
            							$out .= '</div>';
                                    }
                                }
                                wp_reset_postdata();
                            }
    						$out .= '</div>';
    					$out .= '</div>';
    				$out .= '</div>';
                }
			$out .= '</div>';
		$out .= '</div>';
		return $out;
    }
    add_shortcode('agro_woo_bestseller', 'agro_vc_woo_bestseller');
}


/*******************************/
/* woo featured shortcode
/******************************/
if (!function_exists('agro_vc_woo_featured')) {
    function agro_vc_woo_featured($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        'loop'=> '',
        'pimg'=> '',
        'xpos'=> '',
        'ypos'=> '',
        'width'=> '',
        'height'=> '',
        'build_query'=> '',
        'thintitle' => '',
        'title' => '',
        'desc' => '',
        'spb' => '',
        'spt' => '',
        // thumb options
        'autoplay' => '',
        'dots' => '',
        'speed' => '',
        'lgrows' => '',
        'lgshow' => '',
        'lgscroll' => '',
        'mdrows' => '',
        'mdshow' => '',
        'mdscroll' => '',
        'smrows' => '',
        'smshow' => '',
        'smscroll' => '',
        'xsrows' => '',
        'xsshow' => '',
        'xsscroll' => '',
        'rows' => '',
        'show' => '',
        'scroll' => '',
        'imgw'=> '',
        'imgh'=> '',
        'fullthumb' => '',
        'hidetitle' => '',
        // color
        'tclr' => '',
        'thclr' => '',
        'descclr' => '',
        'catclr' => '',
        'ptclr' => '',
        'pthclr' => '',
        'starclr' => '',
        'discbg' => '',
        'discclr' => '',
        'priceclr' => '',
        'priceclr2' => '',
        'contentbg' => '',
        'btnclr' => '',
        'btnhvrclr' => '',
        'btnbrd' => '',
        'btnhvrbrd' => '',
        'btnbg' => '',
        'btnhvrbg' => '',
        'btntype' => '',
        'btnstyle' => '',
        'btnsize' => '',
        // background css
        'css' => '',
        'usefonts' => '',
        'google_fonts' => '',
        'htag' => '',
        'tsize' => '',
        'lheight' => '',
        ), $atts, 'agro_woo_featured');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $uniq = 'blog_'.uniqid();
        $item_css = array();

        $rows     = $atts['rows'] ? $atts['rows'] : '2';
        $show     = $atts['show'] ? $atts['show'] : '4';
        $scroll   = $atts['scroll'] ? $atts['scroll'] : '2';
        $lgrows     = $atts['lgrows'] ? $atts['lgrows'] : '2';
        $lgshow     = $atts['lgshow'] ? $atts['lgshow'] : '4';
        $lgscroll   = $atts['lgscroll'] ? $atts['lgscroll'] : '2';
        $mdrows     = $atts['mdrows'] ? $atts['mdrows'] : '2';
        $mdshow     = $atts['mdshow'] ? $atts['mdshow'] : '4';
        $mdscroll   = $atts['mdscroll'] ? $atts['mdscroll'] : '2';
        $smrows     = $atts['smrows'] ? $atts['smrows'] : '2';
        $smshow     = $atts['smshow'] ? $atts['smshow'] : '3';
        $smscroll   = $atts['smscroll'] ? $atts['smscroll'] : '3';
        $xsrows     = $atts['xsrows'] ? $atts['xsrows'] : '2';
        $xsshow     = $atts['xsshow'] ? $atts['xsshow'] : '2';
        $xsscroll   = $atts['xsscroll'] ? $atts['xsscroll'] : '2';
        $autoplay   = $atts['autoplay'] == 'yes' ? 'true' : 'false';
        $dots       = $atts['dots'] == 'yes' ? 'true' : 'false';
        $speed      = $atts['speed'] ? $atts['speed'] : '1200';
        $htag       = $atts['htag'] ? $atts['htag'] : 'h2';
        $f_family   = 'font_family:Abril%20Fatface%3Aregular';
        $google     = $atts['usefonts'] == 'yes' && $atts['google_fonts'] != $f_family ? new WPBakeryShortCode_Vc_Custom_Google_Fonts() : '';
        $google     = $google ? $google->content( $atts['google_fonts'] ) : '';
        $item_css[] = $google ? '.section-woo.'.$uniq.' .section-heading .__title {'. $google .'}' : '';

        $lheight = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['lheight']) ) ? $atts['lheight'] : $atts['lheight'];
        $tsize = preg_match( '/(px|em|\%|pt|cm)$/', strtolower($atts['tsize']) ) ? $atts['tsize'] : $atts['tsize'].'px';
        $item_css[] = $atts['lheight'] ? '.section-woo.'.$uniq.' .section-heading .__title {line-height:'. $lheight .';}' : '';
        $item_css[] = $atts['tsize'] ? '.section-woo.'.$uniq.' .section-heading .__title {font-size:'. $tsize .';}' : '';

        $item_css[] = $atts['tclr'] ? '.section-woo.'.$uniq.' .section-heading .__title {color: '. esc_html($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['thclr'] ? '.section-woo.'.$uniq.' .section-heading .__title span{color: '. esc_attr($atts['thclr']) .'; }' : '';
        $item_css[] = $atts['descclr'] ? '.section-woo.'.$uniq.' .section-heading p{color: '. esc_attr($atts['descclr']) .'; }' : '';
        $item_css[] = $atts['ptclr'] ? '.section-woo.'.$uniq.' .__item .__title a{color: '. esc_attr($atts['ptclr']) .'; }' : '';
        $item_css[] = $atts['pthclr'] ? '.section-woo.'.$uniq.' .__item .__title a:hover{color: '. esc_attr($atts['pthclr']) .'; }' : '';
        $item_css[] = $atts['starclr'] ? '.section-woo.'.$uniq.' .__item .woocommerce .star-rating{color: '. esc_attr($atts['starclr']) .'; }' : '';
        $item_css[] = $atts['priceclr'] ? '.section-woo.'.$uniq.' .__item .product-price__item--new{color: '. esc_attr($atts['priceclr']) .'; }' : '';
        $item_css[] = $atts['priceclr2'] ? '.section-woo.'.$uniq.' .__item .product-price__item--old{color: '. esc_attr($atts['priceclr2']) .'; }' : '';
        // btn
        $item_css[] = $atts['btnclr'] ? '.section-woo.'.$uniq.' .goods-btn a {color: '. esc_attr($atts['btnclr']) .'; }' : '';
        $item_css[] = $atts['btnhvrclr'] ? '.section-woo.'.$uniq.' .goods-btn a:hover {color: '. esc_attr($atts['btnhvrclr']) .'; }' : '';
        $item_css[] = $atts['btnbrd'] ? '.section-woo.'.$uniq.' .goods-btn a {border-color: '. esc_attr($atts['btnbrd']) .'; }' : '';
        $item_css[] = $atts['btnhvrbrd'] ? '.section-woo.'.$uniq.' .goods-btn a:hover {border-color: '. esc_attr($atts['btnhvrbrd']) .'; }' : '';
        $item_css[] = $atts['btnbg'] ? '.section-woo.'.$uniq.' .goods-btn a {background-color: '. esc_attr($atts['btnbg']) .'; }' : '';
        $item_css[] = $atts['btnhvrbg'] ? '.section-woo.'.$uniq.' .goods-btn a:hover {background-color: '. esc_attr($atts['btnhvrbg']) .'; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';

        $spb = ($atts['spb'] == 'hide') ? ' section--no-pb' : '';
        $spt = ($atts['spt'] == 'hide') ? ' section--no-pt' : '';

        $out .= '<div class="section section-woo'.$spt.$spb.$uniq.$bgcss.'"'.$item_css.'>';
            $loop = (array) vc_param_group_parse_atts($atts['loop']);
            $counter = 1;
            foreach ($loop as $item) {
                if($item['pimg']){
                    $imagealt = esc_attr(get_post_meta($item['pimg'], '_wp_attachment_image_alt', true));
                    $imagealt = $imagealt ? $imagealt : basename ( get_attached_file( $item['pimg'] ) );
                    $posthumbfull = wp_get_attachment_url($item['pimg'], 'full');
                    $width =  isset($item['width']) ? ' width="'.$item['width'].'"' : '';
                    $height = isset($item['height']) ? ' height="'.$item['height'].'"' : '';
                    $xpos =  isset($item['xpos']) ? 'left:'.$item['xpos'].';' : '';
                    $ypos =  isset($item['ypos']) ? 'top:'.$item['ypos'].';' : '';
                    $position =  $xpos || $ypos ? ' style="'.$xpos.$ypos.'"' : '';
                    $out .= '<div class="decor-el decor-el--'.$counter.'" data-jarallax-element="-70" data-speed="0.2"'.$position.'>';
                        $out .= '<img '.$width.$height.' src="'. $posthumbfull .'" alt="'.$imagealt.'"/>';
                    $out .= '</div>';
                }
                $counter++;
            }

            $out .= '<div class="container">';

                if ($atts['title'] || $atts['thintitle'] || $atts['desc']) {
                    $out .= '<div class="section-heading section-heading--left">';
                    // section title
                    if ($atts['title'] || $atts['thintitle']) {
                        $out .= '<'.$htag.' class="__title">'. esc_html($atts['title']) .' <span>'. esc_html($atts['thintitle']) .'</span></'.$htag.'>';
                    }
                    // section description
                    if ($atts['desc']) {
                        $out .= '<p>'. esc_html($atts['desc']) .'</p>';
                    }
                    $out .= '</div>';
                }

                $out .= '<div class="goods goods--style-1 goods--slider">';
                    $out .= '<div class="js-slick"
                        data-slick=\'{"rows":'.$rows.',"slidesToShow": '.$show.',"slidesToScroll": '.$scroll.',"autoplay": '.$autoplay.',"arrows": false,"infinite": true,"dots": '.$dots.',"speed": '.$speed.',"responsive": [{"breakpoint": 575,"settings":{"rows": '.$xsrows.',"slidesToShow": '.$xsshow.',"slidesToScroll": '.$xsscroll.'}},{"breakpoint": 767,"settings":{"rows": '.$smrows.',"slidesToShow": '.$smshow.',"slidesToScroll": '.$smscroll.'}},{"breakpoint": 991,"settings":{"rows": '.$mdrows.',"slidesToShow": '.$mdshow.',"slidesToScroll": '.$mdscroll.'}},{"breakpoint": 1199,"settings":{"rows": '.$lgrows.',"slidesToShow": '.$lgshow.',"slidesToScroll": '.$lgscroll.'}}]}\'>';
                        // The Query
                        list($args) = vc_build_loop_query($atts['build_query']);
                        $args['post_type'] = 'product';
                        $agro_query = new WP_Query($args);
                        if ($agro_query->have_posts()) {
                            while ($agro_query->have_posts()) {
                                $agro_query->the_post();
                                global $product;
                                if (has_post_thumbnail()) {
                                    $out .= '<div class="__item">';
                                        $out .= '<figure class="__image">';
                                            if ($atts['fullthumb'] == 'yes') {
                                                $out .= get_the_post_thumbnail($post->ID, 'full');
                                            } else {
                                                $posthumbfull = wp_get_attachment_url(get_post_thumbnail_id(), 'full');
                                                $posthumb = $atts['imgw'] && $atts['imgh'] ? ntframework_aq_resize($posthumbfull, $img_w, $img_h, true, true, true) : $posthumbfull;
                                                $out .= '<img src="'.$posthumb.'" alt="'.get_the_title().'" />';
                                            }
                                        $out .= '</figure>';
                                        $out .= '<div class="__content">';
                                            $out .= '<h4 class="h6 __title"><a href="'.get_permalink().'">'.get_the_title().'</a></h4>';
                                            $out .= '<div class="__category">';
                                                $out .= wc_get_product_category_list( get_the_id(), '', '',  '' );
                                            $out .= '</div>';
                                            $out .= '<div class="product-price">';
                                                if ( ! empty( $product->get_sale_price() ) ) {
                                                    $out .= '<span class="product-price__item product-price__item--new">'.esc_html(get_woocommerce_currency_symbol()).' '.esc_html($product->get_sale_price()).'</span>';
                                                    $out .= '<span class="product-price__item product-price__item--old">'.esc_html(get_woocommerce_currency_symbol()).' '.esc_html($product->get_regular_price()).'</span>';
                                                } else {
                                                    if ( ! empty( $product->get_regular_price() ) ) {
                                                        $out .= '<span class="product-price__item product-price__item--new">'.esc_html(get_woocommerce_currency_symbol()).' '.esc_html($product->get_regular_price()).'</span>';
                                                    }
                                                }
                                            $out .= '</div>';
                                                ob_start();
                                                woocommerce_template_loop_add_to_cart();
                                            $out .=  '<div class="goods-btn">'.ob_get_clean().'</div>';
                                        $out .= '</div>';
                                        // Only on sale products on frontend and excluding min/max price on variable products

                                            if ( get_post_meta( $product->get_id(), 'agro_new_badge', true ) == 'hot' ) {
                                                $out .= '<div class="product-label product-label--hot">'.esc_html__('Hot','agro').'</div>';
                                            } elseif ( get_post_meta( $product->get_id(), 'agro_new_badge', true ) == 'new' ) {
                                                $out .= '<div class="product-label product-label--new">'.esc_html__('New','agro').'</div>';
                                            } else {
                                                if( $product->is_on_sale() && ! is_admin() && ! $product->is_type('variable')){
                                                    ob_start();
                                                    woocommerce_show_product_sale_flash();
                                                    $out .= '<div class="product-label product-label--sale">'.ob_get_clean().'</div>';
                                                }
                                            }
                                    $out .= '</div>';
                                }
                            }
                            wp_reset_postdata();
                        }
                    $out .= '</div>';
                $out .= '</div>';

            $out .= '</div>';
        $out .= '</div>';

		return $out;
    }
    add_shortcode('agro_woo_featured', 'agro_vc_woo_featured');
}



/*******************************/
/* Advantages
/******************************/
if (!function_exists('agro_vc_advantages')) {
    function agro_vc_advantages($atts, $content = null)
    {
        $atts = shortcode_atts(array(
        // data
        'loop' => '',
        'title' => '',
        'desc' => '',
        'img' => '',
        'imgw' => '',
        'imgh' => '',
        'tclr' => '',
        'dclr' => '',
        // background css
        'css' => '',
        ), $atts, 'agro_advantages');

        $bgcss = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($atts['css'], ' '), $atts);

        $uniq = 'advantages_'.uniqid();
        $item_css = array();

        $item_css[] = $atts['tclr'] ? '.section-advantages.'.$uniq.' .__item .__title {color: '. esc_html($atts['tclr']) .'; }' : '';
        $item_css[] = $atts['dclr'] ? '.section-advantages.'.$uniq.' .__item .__title+ span {color: '. esc_html($atts['dclr']) .'; }' : '';

        $item_css = ! empty($item_css) ? ' data-res-css="'. implode(' ', $item_css) .'"'  : '';
        $uniq = $item_css ? ' '.$uniq  : '';

        $out = '';

        $out .= '<div class="section section--no-pt section--no-pb section-advantages'.$uniq.$bgcss.'"'.$item_css.'>';
            $out .= '<div class="container">';
                $out .= '<div class="advantages">';
                    $out .= '<div class="__inner">';
                        $out .= '<div class="row">';
                        $loop = (array) vc_param_group_parse_atts($atts['loop']);
                        foreach ($loop as $item) {
                            $out .= '<div class="col-12 col-sm-6 col-lg">';
                                $out .= '<div class="__item">';
                                if (isset($item['img']) != '') {
                                    $out .= '<i class="__ico">';
                                        $blankimg = '';
                                    if (isset($item['imgw']) != '' and isset($item['imgh']) != '') {
                                        $out .= agro_img($item['img'], $blankimg, $imgclass='img-fluid', $item['imgw'], $item['imgh']);
                                    }else{
                                        $out .= agro_img($item['img'], $blankimg, $imgclass='img-fluid',60,60);

                                    }
                                    $out .= '</i>';
                                }
                                if (isset($item['title']) != '') {
                                    $out .= '<h4 class="__title h6">'. $item['title'] .'</h4>';
                                }
                                if (isset($item['desc']) != '') {
                                    $out .= '<span>'. $item['desc'] .'</span>';
                                }
                                $out .= '</div>';
                            $out .= '</div>';
                        }
                        $out .= '</div>';
                    $out .= '</div>';
                $out .= '</div>';
            $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode('agro_advantages', 'agro_vc_advantages');
}
