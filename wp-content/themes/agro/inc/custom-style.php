<?php

/*

** theme options panel and metabox settings
** will change some parts of theme via custom style

*/


function agro_custom_css()
{

  // stop on admin pages
    if (is_admin()) {
        return false;
    }

    // Redux global
    global $agro;


    /* CSS to output */
    $theCSS = '';


    // wp adminbar fixes on frontend
    if (is_admin_bar_showing() && !is_customize_preview()) {
        $theCSS .= '
		.header_search{ top: 31px!important; }
		.top-bar, #top-bar{ top: 32px!important; }
		@media (max-width: 782px) { .top-bar { top: 46px!important; } }
		@media (max-width: 782px) { .top-bar { top: 46px!important; } }
		@media (max-width: 768px) { .top-bar { top: 46px!important; } }
		@media (max-width: 600px) { .top-bar { top: 46px!important; } }

	';
    }


    /*************************************************
    ## PRELOADER SETTINGS
    *************************************************/


    if ( '' != agro_settings('theme_main_color') ) {

        $tmc_custom = agro_settings('theme_main_color');

        $theCSS .= '
        .top-bar__contacts .social-btns a:focus,
        .top-bar__contacts .social-btns a:hover,
        .checkbox input[type=checkbox]:checked~span a,
        .checkbox i:before,
        a:focus,
        a:hover,
        .nt-sidebar-inner-widget a:hover,
        .nt-blog-info-meta-link:hover,
        .pagination li:focus a,
        .pagination li:hover a,
        .product-promo--style-2 .__content,
        .content-container .dropcaps .first-letter,
        .post-nav__link .ico ,
        .company-contacts .__ico ,
        .section--dark-bg .contact-form .textfield:focus,
        .section--dark-bg .contact-form .textfield:hover ,
        #footer .social-btns i.active,
        #footer .social-btns i:hover,
        .footer--style-2 address a,
        .footer--style-3 address .__title,
        .simple-text-block .product-description .__num ,
        .review:before,
        #nt-logo.sitename,
        #nt-logo.customtext,a:focus,
        a:hover,.nt-sidebar-inner-widget a:hover,code
        a,span.caret {
            color: '.$tmc_custom.';
        }
        .start-screen[data-scroll-discover=true] .scroll-discover:after,
        .start-screen[data-scroll-discover=true] .scroll-discover:before,
        .nt-shortcode-hero span.page-scroll-discover:after,
        .nt-shortcode-hero span.page-scroll-discover:before,
        .start-screen .play-btn span,
        .navigation li a:not(.custom-btn):after,
        .custom-btn.custom-btn--style-1:focus,
        .custom-btn.custom-btn--style-1:hover,
        .custom-btn.custom-btn--style-2,
        .custom-btn.custom-btn--style-3:focus,
        .custom-btn.custom-btn--style-3:hover,
        .custom-btn.custom-btn--style-4,
        .custom-btn.custom-btn--style-5:focus,
        .custom-btn.custom-btn--style-5:hover,
        .post-password-form input[type=submit],
        .nt-blog-info-meta-item i ,
        .nt-single-navigation-item a,
        .nt-pagination.-style-default .nt-pagination-link:hover,
        .nt-pagination.-style-default .nt-pagination-item.active .nt-pagination-link,
        .post-password-form input[type=submit],
        .slick-dots li.slick-active button,
        .section--base-bg,
        .pagination li.active a,
        .simple-banner--style-1 .__label,
        .widget--tags a:focus,
        .widget--tags a:hover,
        .posts .__item--preview .__date-post,
        .custom-btn.custom-btn--style-4,.nt-blog-info-meta-item i,
        .custom-btn.custom-btn--style-1:focus,
        .custom-btn.custom-btn--style-1:hover,
        .custom-btn.custom-btn--style-2,#btn-to-top,
        .navigation li a:not(.custom-btn):after,
        .nt-tags-list-link:hover,
        .nt-single-navigation-item a, .top-bar__navigation-toggler.is-active + .top-bar__inner span.caret:hover, span.caret.opened  {
            background-color: '.$tmc_custom.' !important;
        }
        .custom-btn.custom-btn--style-4,
        .custom-btn.custom-btn--style-1,
        .nt-pagination.-style-outline .nt-pagination-item.active .nt-pagination-link,
        .top-bar__navigation li.has-submenu:before,
        .nt-tags-list-link:hover,
        .nt-single-navigation-item a,
        .top-bar__navigation li.has-submenu:before,
        .custom-btn,
        .custom-btn.custom-btn--style-5:focus,
        .custom-btn.custom-btn--style-5:hover,
        form .textfield.error,
        .post-password-form input[type=submit],
        .nt-single-navigation-item a,
        .post-password-form input[type=submit],
        .slick-dots li.slick-active button,
        .pagination li:focus a,
        .pagination li:hover a,
        .pagination li.active a,
        .feature--style-1 .__item:hover:before,
        .content-container .blockquot,
        .product-promo--style-1 .__item--first .__content:before
 {
            border-color: '.$tmc_custom.';
        }
        ';
    }

    if ('0' != agro_settings('pre_onoff')) {
        $pretype = agro_settings('pre_type');

        $prebg = '' != agro_settings('pre_bg') ? esc_attr(agro_settings('pre_bg')) : '#fff';
        $spinclr = '' != agro_settings('pre_spin') ? esc_attr(agro_settings('pre_spin')) : '#4ac4f3';

        if ('default' != $pretype) {
            $theCSS .= 'div#nt-preloader {background-color: '. esc_attr($prebg) .';overflow: hidden;background-repeat: no-repeat;background-position: center center;height: 100%;left: 0;position: fixed;top: 0;width: 100%;z-index: 999999999;}';

            $spinrgb   = agro_hex2rgb($spinclr);
            $spin_rgb  = implode(", ", $spinrgb);

            if ('01' == $pretype) {
                $theCSS .= '.loader01 {width: 56px;height: 56px;border: 8px solid '. $spinclr .';border-right-color: transparent;border-radius: 50%;position: relative;animation: loader-rotate 1s linear infinite;top: 50%;margin: -28px auto 0; }.loader01::after {content: "";width: 8px;height: 8px;background: '. $spinclr .';border-radius: 50%;position: absolute;top: -1px;left: 33px; }@keyframes loader-rotate {0% {transform: rotate(0); }100% {transform: rotate(360deg); } }';
            }

            if ('02' == $pretype) {
                $theCSS .= '.loader02 {width: 56px;height: 56px;border: 8px solid rgba('. $spin_rgb .', 0.25);border-top-color: '. $spinclr .';border-radius: 50%;position: relative;animation: loader-rotate 1s linear infinite;top: 50%;margin: -28px auto 0; }@keyframes loader-rotate {0% {transform: rotate(0); }100% {transform: rotate(360deg); } }';
            }

            if ('03' == $pretype) {
                $theCSS .= '.loader03 {width: 56px;height: 56px;border: 8px solid transparent;border-top-color: '. $spinclr .';border-bottom-color: '. $spinclr .';border-radius: 50%;position: relative;animation: loader-rotate 1s linear infinite;top: 50%;margin: -28px auto 0; }@keyframes loader-rotate {0% {transform: rotate(0); }100% {transform: rotate(360deg); } }';
            }

            if ('04' == $pretype) {
                $theCSS .= '.loader04 {width: 56px;height: 56px;border: 2px solid rgba('. $spin_rgb .', 0.5);border-radius: 50%;position: relative;animation: loader-rotate 1s ease-in-out infinite;top: 50%;margin: -28px auto 0; }.loader04::after {content: "";width: 10px;height: 10px;border-radius: 50%;background: '. $spinclr .';position: absolute;top: -6px;left: 50%;margin-left: -5px; }@keyframes loader-rotate {0% {transform: rotate(0); }100% {transform: rotate(360deg); } }';
            }

            if ('05' == $pretype) {
                $theCSS .= '.loader05 {width: 56px;height: 56px;border: 4px solid '. $spinclr .';border-radius: 50%;position: relative;animation: loader-scale 1s ease-out infinite;top: 50%;margin: -28px auto 0; }@keyframes loader-scale {0% {transform: scale(0);opacity: 0; }50% {opacity: 1; }100% {transform: scale(1);opacity: 0; } }';
            }

            if ('06' == $pretype) {
                $theCSS .= '.loader06 {width: 56px;height: 56px;border: 4px solid transparent;border-radius: 50%;position: relative;top: 50%;margin: -28px auto 0; }.loader06::before {content: "";border: 4px solid rgba('. $spin_rgb .', 0.5);border-radius: 50%;width: 67.2px;height: 67.2px;position: absolute;top: -9.6px;left: -9.6px;animation: loader-scale 1s ease-out infinite;animation-delay: 1s;opacity: 0; }.loader06::after {content: "";border: 4px solid '. $spinclr .';border-radius: 50%;width: 56px;height: 56px;position: absolute;top: -4px;left: -4px;animation: loader-scale 1s ease-out infinite;animation-delay: 0.5s; }@keyframes loader-scale {0% {transform: scale(0);opacity: 0; }50% {opacity: 1; }100% {transform: scale(1);opacity: 0; } }';
            }

            if ('07' == $pretype) {
                $theCSS .= '.loader07 {width: 16px;height: 16px;border-radius: 50%;position: relative;animation: loader-circles 1s linear infinite;top: 50%;margin: -8px auto 0; }@keyframes loader-circles {0% {box-shadow: 0 -27px 0 0 rgba('. $spin_rgb .', 0.05), 19px -19px 0 0 rgba('. $spin_rgb .', 0.1), 27px 0 0 0 rgba('. $spin_rgb .', 0.2), 19px 19px 0 0 rgba('. $spin_rgb .', 0.3), 0 27px 0 0 rgba('. $spin_rgb .', 0.4), -19px 19px 0 0 rgba('. $spin_rgb .', 0.6), -27px 0 0 0 rgba('. $spin_rgb .', 0.8), -19px -19px 0 0 '. $spinclr .'; }12.5% {box-shadow: 0 -27px 0 0 '. $spinclr .', 19px -19px 0 0 rgba('. $spin_rgb .', 0.05), 27px 0 0 0 rgba('. $spin_rgb .', 0.1), 19px 19px 0 0 rgba('. $spin_rgb .', 0.2), 0 27px 0 0 rgba('. $spin_rgb .', 0.3), -19px 19px 0 0 rgba('. $spin_rgb .', 0.4), -27px 0 0 0 rgba('. $spin_rgb .', 0.6), -19px -19px 0 0 rgba('. $spin_rgb .', 0.8); }25% {box-shadow: 0 -27px 0 0 rgba('. $spin_rgb .', 0.8), 19px -19px 0 0 '. $spinclr .', 27px 0 0 0 rgba('. $spin_rgb .', 0.05), 19px 19px 0 0 rgba('. $spin_rgb .', 0.1), 0 27px 0 0 rgba('. $spin_rgb .', 0.2), -19px 19px 0 0 rgba('. $spin_rgb .', 0.3), -27px 0 0 0 rgba('. $spin_rgb .', 0.4), -19px -19px 0 0 rgba('. $spin_rgb .', 0.6); }37.5% {box-shadow: 0 -27px 0 0 rgba('. $spin_rgb .', 0.6), 19px -19px 0 0 rgba('. $spin_rgb .', 0.8), 27px 0 0 0 '. $spinclr .', 19px 19px 0 0 rgba('. $spin_rgb .', 0.05), 0 27px 0 0 rgba('. $spin_rgb .', 0.1), -19px 19px 0 0 rgba('. $spin_rgb .', 0.2), -27px 0 0 0 rgba('. $spin_rgb .', 0.3), -19px -19px 0 0 rgba('. $spin_rgb .', 0.4); }50% {box-shadow: 0 -27px 0 0 rgba('. $spin_rgb .', 0.4), 19px -19px 0 0 rgba('. $spin_rgb .', 0.6), 27px 0 0 0 rgba('. $spin_rgb .', 0.8), 19px 19px 0 0 '. $spinclr .', 0 27px 0 0 rgba('. $spin_rgb .', 0.05), -19px 19px 0 0 rgba('. $spin_rgb .', 0.1), -27px 0 0 0 rgba('. $spin_rgb .', 0.2), -19px -19px 0 0 rgba('. $spin_rgb .', 0.3); }62.5% {box-shadow: 0 -27px 0 0 rgba('. $spin_rgb .', 0.3), 19px -19px 0 0 rgba('. $spin_rgb .', 0.4), 27px 0 0 0 rgba('. $spin_rgb .', 0.6), 19px 19px 0 0 rgba('. $spin_rgb .', 0.8), 0 27px 0 0 '. $spinclr .', -19px 19px 0 0 rgba('. $spin_rgb .', 0.05), -27px 0 0 0 rgba('. $spin_rgb .', 0.1), -19px -19px 0 0 rgba('. $spin_rgb .', 0.2); }75% {box-shadow: 0 -27px 0 0 rgba('. $spin_rgb .', 0.2), 19px -19px 0 0 rgba('. $spin_rgb .', 0.3), 27px 0 0 0 rgba('. $spin_rgb .', 0.4), 19px 19px 0 0 rgba('. $spin_rgb .', 0.6), 0 27px 0 0 rgba('. $spin_rgb .', 0.8), -19px 19px 0 0 '. $spinclr .', -27px 0 0 0 rgba('. $spin_rgb .', 0.05), -19px -19px 0 0 rgba('. $spin_rgb .', 0.1); }87.5% {box-shadow: 0 -27px 0 0 rgba('. $spin_rgb .', 0.1), 19px -19px 0 0 rgba('. $spin_rgb .', 0.2), 27px 0 0 0 rgba('. $spin_rgb .', 0.3), 19px 19px 0 0 rgba('. $spin_rgb .', 0.4), 0 27px 0 0 rgba('. $spin_rgb .', 0.6), -19px 19px 0 0 rgba('. $spin_rgb .', 0.8), -27px 0 0 0 '. $spinclr .', -19px -19px 0 0 rgba('. $spin_rgb .', 0.05); }100% {box-shadow: 0 -27px 0 0 rgba('. $spin_rgb .', 0.05), 19px -19px 0 0 rgba('. $spin_rgb .', 0.1), 27px 0 0 0 rgba('. $spin_rgb .', 0.2), 19px 19px 0 0 rgba('. $spin_rgb .', 0.3), 0 27px 0 0 rgba('. $spin_rgb .', 0.4), -19px 19px 0 0 rgba('. $spin_rgb .', 0.6), -27px 0 0 0 rgba('. $spin_rgb .', 0.8), -19px -19px 0 0 '. $spinclr .'; } }';
            }

            if ('08' == $pretype) {
                $theCSS .= '.loader08 {width: 20px;height: 20px;position: relative;animation: loader08 1s ease infinite;top: 50%;margin: -46px auto 0; }@keyframes loader08 {0%, 100% {box-shadow: -13px 20px 0 '. $spinclr .', 13px 20px 0 rgba('. $spin_rgb .', 0.2), 13px 46px 0 rgba('. $spin_rgb .', 0.2), -13px 46px 0 rgba('. $spin_rgb .', 0.2); }25% {box-shadow: -13px 20px 0 rgba('. $spin_rgb .', 0.2), 13px 20px 0 '. $spinclr .', 13px 46px 0 rgba('. $spin_rgb .', 0.2), -13px 46px 0 rgba('. $spin_rgb .', 0.2); }50% {box-shadow: -13px 20px 0 rgba('. $spin_rgb .', 0.2), 13px 20px 0 rgba('. $spin_rgb .', 0.2), 13px 46px 0 '. $spinclr .', -13px 46px 0 rgba('. $spin_rgb .', 0.2); }75% {box-shadow: -13px 20px 0 rgba('. $spin_rgb .', 0.2), 13px 20px 0 rgba('. $spin_rgb .', 0.2), 13px 46px 0 rgba('. $spin_rgb .', 0.2), -13px 46px 0 '. $spinclr .'; } }';
            }

            if ('09' == $pretype) {
                $theCSS .= '.loader09 {width: 10px;height: 48px;background: '. $spinclr .';position: relative;animation: loader09 1s ease-in-out infinite;animation-delay: 0.4s;top: 50%;margin: -28px auto 0; }.loader09::after, .loader09::before {content:  "";position: absolute;width: 10px;height: 48px;background: '. $spinclr .';animation: loader09 1s ease-in-out infinite; }.loader09::before {right: 18px;animation-delay: 0.2s; }.loader09::after {left: 18px;animation-delay: 0.6s; }@keyframes loader09 {0%, 100% {box-shadow: 0 0 0 '. $spinclr .', 0 0 0 '. $spinclr .'; }50% {box-shadow: 0 -8px 0 '. $spinclr .', 0 8px 0 '. $spinclr .'; } }';
            }

            if ('01' == $pretype) {
                $theCSS .= '.loader10 {width: 28px;height: 28px;border-radius: 50%;position: relative;animation: loader10 0.9s ease alternate infinite;animation-delay: 0.36s;top: 50%;margin: -42px auto 0; }.loader10::after, .loader10::before {content: "";position: absolute;width: 28px;height: 28px;border-radius: 50%;animation: loader10 0.9s ease alternate infinite; }.loader10::before {left: -40px;animation-delay: 0.18s; }.loader10::after {right: -40px;animation-delay: 0.54s; }@keyframes loader10 {0% {box-shadow: 0 28px 0 -28px '. $spinclr .'; }100% {box-shadow: 0 28px 0 '. $spinclr .'; } }';
            }

            if ('01' == $pretype) {
                $theCSS .= '.loader11 {width: 20px;height: 20px;border-radius: 50%;box-shadow: 0 40px 0 '. $spinclr .';position: relative;animation: loader11 0.8s ease-in-out alternate infinite;animation-delay: 0.32s;top: 50%;margin: -50px auto 0; }.loader11::after, .loader11::before {content:  "";position: absolute;width: 20px;height: 20px;border-radius: 50%;box-shadow: 0 40px 0 '. $spinclr .';animation: loader11 0.8s ease-in-out alternate infinite; }.loader11::before {left: -30px;animation-delay: 0.48s;}.loader11::after {right: -30px;animation-delay: 0.16s; }@keyframes loader11 {0% {box-shadow: 0 40px 0 '. $spinclr .'; }100% {box-shadow: 0 20px 0 '. $spinclr .'; } }';
            }

            if ('01' == $pretype) {
                $theCSS .= '.loader12 {width: 20px;height: 20px;border-radius: 50%;position: relative;animation: loader12 1s linear alternate infinite;top: 50%;margin: -50px auto 0; }@keyframes loader12 {0% {box-shadow: -60px 40px 0 2px '. $spinclr .', -30px 40px 0 0 rgba('. $spin_rgb .', 0.2), 0 40px 0 0 rgba('. $spin_rgb .', 0.2), 30px 40px 0 0 rgba('. $spin_rgb .', 0.2), 60px 40px 0 0 rgba('. $spin_rgb .', 0.2); }25% {box-shadow: -60px 40px 0 0 rgba('. $spin_rgb .', 0.2), -30px 40px 0 2px '. $spinclr .', 0 40px 0 0 rgba('. $spin_rgb .', 0.2), 30px 40px 0 0 rgba('. $spin_rgb .', 0.2), 60px 40px 0 0 rgba('. $spin_rgb .', 0.2); }50% {box-shadow: -60px 40px 0 0 rgba('. $spin_rgb .', 0.2), -30px 40px 0 0 rgba('. $spin_rgb .', 0.2), 0 40px 0 2px '. $spinclr .', 30px 40px 0 0 rgba('. $spin_rgb .', 0.2), 60px 40px 0 0 rgba('. $spin_rgb .', 0.2); }75% {box-shadow: -60px 40px 0 0 rgba('. $spin_rgb .', 0.2), -30px 40px 0 0 rgba('. $spin_rgb .', 0.2), 0 40px 0 0 rgba('. $spin_rgb .', 0.2), 30px 40px 0 2px '. $spinclr .', 60px 40px 0 0 rgba('. $spin_rgb .', 0.2); }100% {box-shadow: -60px 40px 0 0 rgba('. $spin_rgb .', 0.2), -30px 40px 0 0 rgba('. $spin_rgb .', 0.2), 0 40px 0 0 rgba('. $spin_rgb .', 0.2), 30px 40px 0 0 rgba('. $spin_rgb .', 0.2), 60px 40px 0 2px '. $spinclr .'; } }';
            }
        } else {
            $theCSS .= '.preloader {width: 100%;height: 100%;position: fixed;top:0;left:0;background-color: '. esc_attr($prebg) .';z-index: 9999;}';
        }
    }

    // use page/post ID for page settings
    $page_id = get_the_ID();


    /*************************************************
    ## THEME NAVIGATION COLOR SETTINGS
    *************************************************/
    $menu_a = agro_settings('nav_a_typo');
    $menu_a = isset($menu_a['color']) != '' ? $menu_a['color'] : '';
    if($menu_a !='') {
        $theCSS .= '.top-bar__navigation li.has-submenu:before {
            border-color:'.esc_attr($menu_a).';
        }';
        $theCSS .= '.navigation li a:not(.custom-btn):after {
            background-color:'.esc_attr($menu_a).';
        }';
    }
    $smenu_a = agro_settings('sticky_nav_a_typo');
    $smenu_a = isset($smenu_a['color']) != '' ? $smenu_a['color'] : '';
    if($smenu_a !='') {
        $theCSS .= 'header.topbar-fixed.fixed .top-bar__navigation li.has-submenu:before {
            border-color:'.esc_attr($smenu_a).';
        }';
        $theCSS .= 'header.topbar-fixed.fixed .navigation li a:not(.custom-btn):after {
            background-color:'.esc_attr($smenu_a).';
        }';
    }
    $smenu_hvr_a = agro_settings('sticky_nav_hvr_a');
    if($smenu_hvr_a !='') {
        $theCSS .= 'header.topbar-fixed.fixed .top-bar__navigation li.has-submenu:hover:before {
            border-color:'.esc_attr($smenu_hvr_a).';
        }';
    }
    // dropdown menu
    $menu_a = agro_settings('nav_dropdown_typo');
    $menu_a = isset($menu_a['color']) != '' ? $menu_a['color'] : '';
    if(isset($nav_dropdown_bg["rgba"]) !='') {
        $theCSS .= '@media (min-width: 992px){
            #top-bar.top-bar .top-bar__navigation .submenu {
                background-color:'.esc_attr($nav_dropdown_bg["rgba"]).';
            }
        }';
    }
    $nav_dropdown_bg = agro_settings('nav_dropdown_bg');
    if(isset($nav_dropdown_bg["rgba"]) !='') {
        $theCSS .= '@media (min-width: 992px){
            #top-bar.top-bar .top-bar__navigation .submenu {
                background-color:'.esc_attr($nav_dropdown_bg["rgba"]).';
            }
        }';
    }
    $nav_mob_bg_overlay = agro_settings('nav_mob_bg_overlay');
    if(isset($nav_mob_bg_overlay["rgba"]) !='') {
        $theCSS .= '.is-expanded .top-bar__bg:before {
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            left: 0;
            top: 0;
            background: '.esc_attr($nav_mob_bg_overlay["rgba"]).';
        }';
    }
    $nav_mob_hbg_overlay = agro_settings('nav_mob_hbg_overlay');
    if(isset($nav_mob_hbg_overlay["rgba"]) !='') {
        $theCSS .= '.top-bar__navigation-toggler{
            background: '.esc_attr($nav_mob_hbg_overlay["rgba"]).';
        }';
    }
    $nav_mob_hcbg_overlay = agro_settings('nav_mob_hcbg_overlay');
    if(isset($nav_mob_hcbg_overlay["rgba"]) !='') {
        $theCSS .= '.top-bar__navigation-toggler--light span, .top-bar__navigation-toggler--light span:after, .top-bar__navigation-toggler--light span:before{
            background: '.esc_attr($nav_mob_hcbg_overlay["rgba"]).';
        }';
    }
    $nav_btn_bg = agro_settings('nav_btn_bg');
if(isset($nav_btn_bg["rgba"]) !='') {
    $theCSS .= '.top-bar__navigation li.li-btn .custom-btn {
        border-color: '.esc_attr($nav_btn_bg["rgba"]).';
        background-color: '.esc_attr($nav_btn_bg["rgba"]).' !important;
    }';
}
$nav_btn_hvrbg = agro_settings('nav_btn_hvrbg');
if(isset($nav_btn_hvrbg["rgba"]) !='') {
    $theCSS .= '.top-bar__navigation li.li-btn .custom-btn:hover {
        border-color: '.esc_attr($nav_btn_hvrbg["rgba"]).';
        background-color: '.esc_attr($nav_btn_hvrbg["rgba"]).' !important;
    }';
}
$nav_cbtn_clr = agro_settings('nav_btn_clr');
if(isset($nav_cbtn_clr) !='') {
    $theCSS .= '.top-bar__navigation li.li-btn .custom-btn {
        color: '.esc_attr($nav_cbtn_clr).' !important;
    }';
}
$nav_cbtn_hvrclr = agro_settings('nav_btn_hvrclr');
if(isset($nav_cbtn_hvrclr) !='') {
    $theCSS .= '.top-bar__navigation li.li-btn .custom-btn:hover {
        color: '.esc_attr($nav_cbtn_hvrclr).' !important;
    }';
}
    $fw_wa = agro_settings('fw_wa');
    if(isset($fw_wa) !='') {
        $theCSS .= '.nt-footer-widget .menu li.menu-item-has-children > a:after  {
            border-color: '.esc_attr($fw_wa).';
        }';
    }
    $fw_hvra = agro_settings('fw_hvra');
    if(isset($fw_hvra) !='') {
        $theCSS .= '.nt-footer-widget .menu li.menu-item-has-children:hover a:after  {
            border-color: '.esc_attr($fw_hvra).';
        }';
    }
    $fw_hvra = agro_settings('fw_hvra');
    if(isset($fw_hvra) !='') {
        $theCSS .= '.nt-footer-widget .menu li.menu-item-has-children:hover > a {
            color: '.esc_attr($fw_hvra).';
        }';
    }
    $img_logo_dimensions = agro_settings('img_logo_dimensions');
    if(isset($img_logo_dimensions) !='') {
        $theCSS .= '.site-logo img {
            max-width: 120px;
        }';
    }
    $img_logo2_dimensions = agro_settings('img_logo2_dimensions');
    if(isset($img_logo2_dimensions) !='') {
        $theCSS .= 'header.topbar-fixed.fixed .site-logo img {
            max-width: inherit;
        }';
    }
    $mob_logo_dimensions = agro_settings('mob_logo_dimensions');
    if(is_array($mob_logo_dimensions) && !empty(array_filter($mob_logo_dimensions))) {
        $theCSS .= '@media(max-width:992px){
			.site-logo img, header.topbar-fixed.fixed .site-logo img {';
			if($mob_logo_dimensions['width'] !='') {
				$theCSS .= 'width: '.$mob_logo_dimensions['width'].'!important;';
			}
			if($mob_logo_dimensions['height'] !='') {
				$theCSS .= 'height: '.$mob_logo_dimensions['height'].'!important;';
			}
			$theCSS .= '}
        }';
    }

    /*************************************************
    ## LOGO SETTINGS
    *************************************************/
        if ( !empty( agro_settings('img_mobile_logo')['url'] ) ) {
            $theCSS .= '.mobile-logo{
            display: none;
            }
            @media only screen and (max-width: 568px) {
                .mobile-logo{
                    display: block;
                }
                .main-logo,.sticky-logo{display:none!important}
            }';
        }
        if ( !empty( agro_settings('img_smobile_logo')['url'] ) ) {
            $theCSS .= '.sticky-mobile-logo{
            display: none;
            }
            @media only screen and (max-width: 568px) {
                .top-bar.fixed .sticky-mobile-logo{
                    display: block;
                }
                .top-bar.fixed .mobile-logo{
                    display: none;
                }
                .main-logo,.sticky-logo{display:none!important}
            }';
        }


        $blog_992_c = agro_settings('blog_content_992_pad');
        if(is_array($blog_992_c) && !empty(array_filter($blog_992_c))) {
            $blog_992_units = !empty($blog_992_c['units']) ? $blog_992_c['units'] : 'px';
            $theCSS .= '@media(max-width:992px){
                #nt-index .nt-theme-inner-container{';
                    if(!empty($blog_992_c['padding-top'])) {
                        $theCSS .= 'padding-top: '.$blog_992_c['padding-top'].$blog_992_units.'!important;';
                    }
                    if(!empty($blog_992_c['padding-right'])) {
                        $theCSS .= 'padding-right: '.$blog_992_c['padding-right'].$blog_992_units.'!important;';
                    }
                    if(!empty($blog_992_c['padding-bottom'])) {
                        $theCSS .= 'padding-bottom: '.$blog_992_c['padding-bottom'].$blog_992_units.'!important;';
                    }
                    if(!empty($blog_992_c['padding-left'])) {
                        $theCSS .= 'padding-left: '.$blog_992_c['padding-left'].$blog_992_units.'!important;';
                    }
                    $theCSS .= '}
                }';
            }

            $blog_768_c = agro_settings('blog_content_768_pad');
            if(is_array($blog_768_c) && !empty(array_filter($blog_768_c))) {
                $blog_768_units = !empty($blog_768_c['units']) ? $blog_768_c['units'] : 'px';
                $theCSS .= '@media(max-width:768px){
                    #nt-index .nt-theme-inner-container{';
                    if(!empty($blog_768_c['padding-top'])) {
                        $theCSS .= 'padding-top: '.$blog_768_c['padding-top'].$blog_768_units.'!important;';
                    }
                    if(!empty($blog_768_c['padding-right'])) {
                        $theCSS .= 'padding-right: '.$blog_768_c['padding-right'].$blog_768_units.'!important;';
                    }
                    if(!empty($blog_768_c['padding-bottom'])) {
                        $theCSS .= 'padding-bottom: '.$blog_768_c['padding-bottom'].$blog_768_units.'!important;';
                    }
                    if(!empty($blog_768_c['padding-left'])) {
                        $theCSS .= 'padding-left: '.$blog_768_c['padding-left'].$blog_768_units.'!important;';
                    }
                $theCSS .= '}
                }';
            }
            $single_992_c = agro_settings('single_content_992_pad');
            if(is_array($blog_992_c) && !empty(array_filter($blog_992_c))) {
                $blog_992_units = !empty($blog_992_c['units']) ? $blog_992_c['units'] : 'px';
                $theCSS .= '@media(max-width:992px){
                    #nt-single .nt-theme-inner-container{';
                    if(!empty($blog_992_c['padding-top'])) {
                        $theCSS .= 'padding-top: '.$blog_992_c['padding-top'].$blog_992_units.'!important;';
                    }
                    if(!empty($blog_992_c['padding-right'])) {
                        $theCSS .= 'padding-right: '.$blog_992_c['padding-right'].$blog_992_units.'!important;';
                    }
                    if(!empty($blog_992_c['padding-bottom'])) {
                        $theCSS .= 'padding-bottom: '.$blog_992_c['padding-bottom'].$blog_992_units.'!important;';
                    }
                    if(!empty($blog_992_c['padding-left'])) {
                        $theCSS .= 'padding-left: '.$blog_992_c['padding-left'].$blog_992_units.'!important;';
                    }
                $theCSS .= '}
                }';
            }
            $single_768_c = agro_settings('single_content_768_pad');
            if(is_array($blog_768_c) && !empty(array_filter($blog_768_c))) {
                $blog_768_units = !empty($blog_768_c['units']) ? $blog_768_c['units'] : 'px';
                $theCSS .= '@media(max-width:768px){
                    #nt-single .nt-theme-inner-container{';
                    if(!empty($blog_768_c['padding-top'])) {
                        $theCSS .= 'padding-top: '.$blog_768_c['padding-top'].$blog_768_units.'!important;';
                    }
                    if(!empty($blog_768_c['padding-right'])) {
                        $theCSS .= 'padding-right: '.$blog_768_c['padding-right'].$blog_768_units.'!important;';
                    }
                    if(!empty($blog_768_c['padding-bottom'])) {
                        $theCSS .= 'padding-bottom: '.$blog_768_c['padding-bottom'].$blog_768_units.'!important;';
                    }
                    if(!empty($blog_768_c['padding-left'])) {
                        $theCSS .= 'padding-left: '.$blog_768_c['padding-left'].$blog_768_units.'!important;';
                    }
                $theCSS .= '}
                }';
            }

            $blog_post_link_hvrclr = agro_settings('blog_post_link_hvrclr', '');
            if( $blog_post_link_hvrclr ) {
                $theCSS .= '.nt-blog-item .nt-blog-info-title a:hover,.nt-blog-item .nt-blog-info-meta-link:hover,
                .nt-blog-info-category a:hover {
                    color: '.esc_attr($blog_post_link_hvrclr).'!important;
                }';

            }
            $blog_post_btn_clr = agro_settings('blog_post_btn_clr');
            if( !empty( $blog_post_btn_clr['regular'] ) ) {
                $theCSS .= '.nt-blog-item .nt-blog-info-link .custom-btn{
                    color: '.esc_attr($blog_post_btn_clr['regular']).'!important;
                }';
            }
            if( !empty( $blog_post_btn_clr['hover'] ) ) {
                $theCSS .= '.nt-blog-item .nt-blog-info-link .custom-btn:hover{
                    color: '.esc_attr($blog_post_btn_clr['hover']).'!important;
                }';
            }
            $blog_post_btn_bg = agro_settings('blog_post_btn_bg');
            if( !empty( $blog_post_btn_bg['regular'] ) ) {
                $theCSS .= '.nt-blog-item .nt-blog-info-link .custom-btn{
                    background-color: '.esc_attr($blog_post_btn_bg['regular']).'!important;
                }';
            }
            if( !empty( $blog_post_btn_bg['hover'] ) ) {
                $theCSS .= '.nt-blog-item .nt-blog-info-link .custom-btn:hover{
                    background-color: '.esc_attr($blog_post_btn_bg['hover']).'!important;
                }';
            }
            $blog_post_btn_brd = agro_settings('blog_post_btn_brd');
            if( !empty( $blog_post_btn_brd['regular'] ) ) {
                $theCSS .= '.nt-blog-item .nt-blog-info-link .custom-btn{
                    border-color: '.esc_attr($blog_post_btn_brd['regular']).'!important;
                }';
            }
            if( !empty( $blog_post_btn_brd['hover'] ) ) {
                $theCSS .= '.nt-blog-item .nt-blog-info-link .custom-btn:hover{
                    border-color: '.esc_attr($blog_post_btn_brd['hover']).'!important;
                }';
            }
            $blog_post_btn_brdrad = agro_settings('blog_post_btn_brdrad');
            if( $blog_post_btn_brdrad ) {
                $theCSS .= '.nt-blog-item .nt-blog-info-link .custom-btn{
                    border-radius: '.esc_attr($blog_post_btn_brdrad).'px!important;
                }';
            }
    /*************************************************
    ## THEME GENERAL COLOR SETTINGS
    *************************************************/


    if (is_404()) { // error page
        $name = 'error';
    } elseif (is_archive()) { // blog and cpt archive page
        $name = 'archive';
    } elseif (is_search()) { // search page
        $name = 'search';
    } elseif (is_home() or is_front_page()) { // blog post loop page index.php or your choise on settings
        $name = 'blog';
    } elseif (is_single()) { // blog post single/singular page
        $name = 'single';
    } elseif (is_singular("portfolio")) { // it is cpt and if you want use another clone this condition and add your cpt name as portfolio
        $name = 'portfolio_single';
    } elseif (is_page()) {  // default or custom page
        $name	= 'page';
    }


    /*************************************************
    ## THEME PAGINATION
    *************************************************/

    $pag_clr = isset($agro['pag_clr']) && !empty($agro['pag_clr']) ? esc_attr($agro['pag_clr']) : '';
    $pag_hvrclr = isset($agro['pag_hvrclr']) && !empty($agro['pag_hvrclr']) ? esc_attr($agro['pag_hvrclr']) : '';
    $pag_nclr = isset($agro['pag_nclr']) && !empty($agro['pag_nclr']) ? esc_attr($agro['pag_nclr']) : '';
    $pag_hvrnclr = isset($agro['pag_hvrnclr']) && !empty($agro['pag_hvrnclr']) ? esc_attr($agro['pag_hvrnclr']) : '';

    // pagination color
    if ('' != $pag_clr) {
        $theCSS .= '
		.nt-pagination.-style-outline .nt-pagination-item .nt-pagination-link { border-color: '. esc_attr($pag_clr) .'; }
		.nt-pagination.-style-default .nt-pagination-link { background-color: '. esc_attr($pag_clr) .';
		}';
    }

    // pagination active and hover color
    if ('' != $pag_hvrclr) {
        $theCSS .= '
		.nt-pagination.-style-outline .nt-pagination-item.active .nt-pagination-link,
		.nt-pagination.-style-outline .nt-pagination-item .nt-pagination-link:hover { border-color: '. esc_attr($pag_hvrclr) .'; }
		.nt-pagination.-style-default .nt-pagination-item.active .nt-pagination-link,
		.nt-pagination.-style-default .nt-pagination-item .nt-pagination-link:hover { background-color: '. esc_attr($pag_hvrclr) .';
		}';
    }

    // pagination number color
    if ('' != $pag_nclr) {
        $theCSS .= '
		.nt-pagination.-style-outline .nt-pagination-item .nt-pagination-link,
		.nt-pagination.-style-default .nt-pagination-link { color: '. esc_attr($pag_nclr) .';
		}';
    }

    // pagination active and hover color
    if ('' != $pag_hvrnclr) {
        $theCSS .= '
		.nt-pagination.-style-outline .nt-pagination-item.active .nt-pagination-link,
		.nt-pagination.-style-outline .nt-pagination-item .nt-pagination-link:hover,
		.nt-pagination.-style-default .nt-pagination-item.active .nt-pagination-link,
		.nt-pagination.-style-default .nt-pagination-item .nt-pagination-link:hover { color: '. esc_attr($pag_hvrnclr) .';
		}';
    }
    /*************************************************
    ## Product hero settings
    *************************************************/
    $s_p_h_i_o = rwmb_meta('agro_s_p_h_i_o');
    if ($s_p_h_i_o =="private") {
        $p_hero_bg = rwmb_meta('agro_s_p_h_i');
        $p_hero_bg = is_array($p_hero_bg) ? $p_hero_bg : false;

        if (false != $p_hero_bg && !empty($p_hero_bg['image'])) {
            $theCSS .= '.single-product .page-id-'. $page_id.'#nt-hero {';

            if (!empty($p_hero_bg['image'])) {
                $theCSS .= 'background-image:url('. esc_attr($p_hero_bg['image']).')!important;';
            }

            if (!empty($p_hero_bg['color'])) {
                $theCSS .= 'background-color:'. esc_attr($p_hero_bg['color']).'!important;';
            }

            if (!empty($p_hero_bg['size'])) {
                $theCSS .= 'background-size:'. esc_attr($p_hero_bg['size']).'!important;';
            }

            if (!empty($p_hero_bg['repeat'])) {
                $theCSS .= 'background-repeat:'. esc_attr($p_hero_bg['repeat']).'!important;';
            }

            if (!empty($p_hero_bg['position'])) {
                $theCSS .= 'background-position:'. esc_attr($p_hero_bg['position']).'!important;';
            }

            if (!empty($p_hero_bg['attachment'])) {
                $theCSS .= 'background-attachment:'. esc_attr($p_hero_bg['attachment']).'!important;';
            }

            $theCSS .= '}';

        } // end if hero background image
    }

    /*************************************************
    ## PAGE METABOX SETTINGS
    *************************************************/

    if (is_page()) {

    /*************************************************
    ## PAGE HERO OPTIONS
    *************************************************/

        // page hero background image
        $p_hero_d = rwmb_meta('agro_page_hero_onoff');

        if ('0' != $p_hero_d) {
            $p_hero_bg = rwmb_meta('agro_page_hero_bg');
            $p_hero_bg = is_array($p_hero_bg) ? $p_hero_bg : false;

            if (false != $p_hero_bg && !empty($p_hero_bg['image'])) {
                $theCSS .= '#hero.page-id-'. $page_id.'.hero-container {';

                if (!empty($p_hero_bg['image'])) {
                    $theCSS .= 'background-image:url('. esc_attr($p_hero_bg['image']).')!important;';
                }

                if (!empty($p_hero_bg['color'])) {
                    $theCSS .= 'background-color:'. esc_attr($p_hero_bg['color']).'!important;';
                }

                if (!empty($p_hero_bg['size'])) {
                    $theCSS .= 'background-size:'. esc_attr($p_hero_bg['size']).'!important;';
                }

                if (!empty($p_hero_bg['repeat'])) {
                    $theCSS .= 'background-repeat:'. esc_attr($p_hero_bg['repeat']).'!important;';
                }

                if (!empty($p_hero_bg['position'])) {
                    $theCSS .= 'background-position:'. esc_attr($p_hero_bg['position']).'!important;';
                }

                if (!empty($p_hero_bg['attachment'])) {
                    $theCSS .= 'background-attachment:'. esc_attr($p_hero_bg['attachment']).'!important;';
                }

                $theCSS .= '}';

                // page hero bg image overlay
                $p_hero_bg_overlay = rwmb_meta('agro_page_hero_overlay');
                if (!empty($p_hero_bg_overlay)) {
                    $theCSS .= '#hero.page-id-'. $page_id.'.hero-container.hero-overlay {
                        background-color:'. esc_attr($p_hero_bg_overlay).';
                    }';
                }

            } // end if hero background image

            // page hero padding top
            $p_h_pt = rwmb_meta('agro_page_hero_pt');
            if ('' != $p_h_pt) {
                $theCSS .= '#hero.page-id-'. $page_id .' { padding-top: '. esc_attr($p_h_pt) .'px;}';
            }

            // page hero padding bottom
            $p_h_pb = rwmb_meta('agro_page_hero_pb');
            if ('' != $p_h_pb) {
                $theCSS .= '#hero.page-id-'. $page_id .' { padding-bottom: '. esc_attr($p_h_pb) .'px;}';
            }

            // page hero-title
            $p_t_clr = rwmb_meta('agro_page_hero_title_clr');
            if ('' != $p_t_clr) {
                $theCSS .= '#hero.page-id-'. $page_id .' .hero-title { color:'. esc_attr($p_t_clr) .';}';
            }

            // page hero-title font-size
            $p_t_fs = rwmb_meta('agro_page_hero_title_fs');
            if ('' != $p_t_fs) {
                $theCSS .= '#hero.page-id-'. $page_id .' .hero-title { font-size:'. esc_attr($p_t_fs) .'px;}';
            }

            // page hero title margin-bottom
            $p_t_mb = rwmb_meta('agro_page_hero_title_mb');
            if ('' != $p_t_mb) {
                $theCSS .= '#hero.page-id-'. $page_id .' .hero-title { margin-bottom:'. esc_attr($p_t_mb) .'px;}';
            }

            // page hero-slogan color
            $p_s_clr = rwmb_meta('agro_page_hero_slogan_clr');
            if ('' != $p_s_clr) {
                $theCSS .= '#hero.page-id-'. $page_id .' .hero-subtitle { color:'. esc_attr($p_s_clr) .';}';
            }

            // page hero slogan font-size
            $p_s_fs = rwmb_meta('agro_page_hero_slogan_fs');
            if ('' != $p_s_fs) {
                $theCSS .= '#hero.page-id-'. $page_id .' .hero-subtitle { font-size:'. esc_attr($p_s_fs) .'px;}';
            }

            // page hero description color
            $p_d_clr = rwmb_meta('agro_page_hero_desc_clr');
            if ('' != $p_d_clr) {
                $theCSS .= '#hero.page-id-'. $page_id .' .hero-desc { color:'. esc_attr($p_d_clr) .';}';
            }

            // page hero description font-size
            $p_d_fs = rwmb_meta('agro_page_hero_desc_fs');
            if ('' != $p_d_fs) {
                $theCSS .= '#hero.page-id-'. $page_id .' .hero-desc { font-size:'. esc_attr($p_d_fs) .'px;}';
            }

            // page content padding top
            $p_c_pt = rwmb_meta('agro_page_content_pt');
            if ('' != $p_c_pt) {
                $theCSS .= '.page-id-'. $page_id .' .nt-theme-inner-container { padding-top:'. esc_attr($p_c_pt) .'px;}';
            }
            // page content padding bottom
            $p_c_pb = rwmb_meta('agro_page_content_pb');
            if ('' != $p_c_pb) {
                $theCSS .= '.page-id-'. $page_id .' .nt-theme-inner-container { padding-bottom:'. esc_attr($p_c_pb) .'px;}';
            }
            // page sticky dispylay
            $p_s_d = rwmb_meta('agro_sticky_header_onoff');
            if ('1'!= $p_s_d) {
                $theCSS .= '.page-id-'. $page_id .' .top-bar.fixed{display:none}';
            }

        } // end if hero on-off
    } // end if is_page


    /* Add CSS to style.css */
    wp_register_style('agro-custom-style', false);
    wp_enqueue_style('agro-custom-style');
    wp_add_inline_style('agro-custom-style', $theCSS);
}

add_action('wp_enqueue_scripts', 'agro_custom_css');


// customization on admin pages
function agro_custom_editor_css()
{
    if(agro_settings('editor_css')){
        /* Add CSS to head */
        wp_register_style('agro-custom-editor-style', false);
        wp_enqueue_style('agro-custom-editor-style');
        wp_add_inline_style('agro-custom-editor-style', agro_settings('editor_css'));
    }


}
add_action('wp_enqueue_scripts', 'agro_custom_editor_css');


// customization on admin pages
function agro_admin_custom_css()
{
    if (! is_admin()) {
        return false;
    }

    /* CSS to output */
    $theCSS = '';

    $theCSS .= '

	#setting-error-tgmpa, #setting-error-agro {
		display: block !important;
	}

	.updated.vc_license-activation-notice {
		display:none;
	}

	.rwmb-tab-panel .rwmb-thickbox_image-wrapper ul li {
		display: inline-block;
		position: relative;
	}

	.rwmb-image-item .rwmb-media-preview {
		width: 150px;
		background: #eee;
	}

    div#meta-box-notification {
        display: none;
    }

	.wpb_vc_row {
		position: relative;
	}

	.wpb_element_wrapper {
		padding-top: 27px;
	}

	.wpb_vc_row >.wpb_element_wrapper>.vc_admin_label {
		font-size: 12px;
		font-style: italic;
		color: #fff;
		line-height: 24px;
		background: #0473aa;
		position: absolute;
		top: 27px;
		right: -5px;
		pointer-events: none;
		padding: 0 7px;
	}

	.wpb_vc_row >.wpb_element_wrapper>.vc_admin_label:hover:before {
		opacity:1;
	}

	.vc_license-activation-notice {
		display: none!important;
	}

	.vc_admin_label {
		font-weight: 500;
	}

	/* vc_btn */
	.vc_btn3-container .vc_general.vc_btn3{
		display: inline-block;
		font-size: 14px;
		font-weight: 600;
		line-height: 25px;
		text-transform: uppercase;
		-webkit-transition: all 0.35s ease-in-out;
		-moz-transition: all 0.35s ease-in-out;
		-ms-transition: all 0.35s ease-in-out;
		-o-transition: all 0.35s ease-in-out;
		transition: all 0.35s ease-in-out;
		font-weight: 700;
		letter-spacing: 1px;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
		overflow: hidden;
		-webkit-border-radius: 30px!important;
		-moz-border-radius: 30px!important;
		border-radius: 30px!important;
	}

	.vc_btn3-container .vc_btn3-style-border{
		background-color: transparent!important;
		color: #4ac4f3 !important;
		border: 2px solid #4ac4f3!important;
	}

	.vc_btn3-container .vc_btn3-style-effect{
		background-color: #4ac4f3 !important;
		color: #fff !important;
		border: none!important;
	}

	.vc_btn3-container .vc_btn3-style-effect{
		background-color: #4ac4f3 !important;
		color: #fff !important;
		border: none!important;
	}

	.vc_btn3-container .vc_btn3-style-border.vc_btn3-size-lg, .vc_btn3-container .vc_btn3-style-effect.vc_btn3-size-lg {
		padding: 12px 80px!important;
		font-size: 16px!important;
	}

	.vc_btn3-container .vc_btn3-style-border.vc_btn3-size-md, .vc_btn3-container .vc_btn3-style-effect.vc_btn3-size-md {
		padding: padding: 10px 40px!important;
		font-size: 14px!important;
	}

	.vc_btn3-container .vc_btn3-style-border.vc_btn3-size-sm, .vc_btn3-container .vc_btn3-style-effect.vc_btn3-size-sm {
		padding: 8px 35px!important;
		font-size: 12px!important;
	}

	.vc_btn3-container .vc_btn3-style-border.vc_btn3-size-xs, .vc_btn3-container .vc_btn3-style-effect.vc_btn3-size-xs {
		padding: 6px 30px!important;
		font-size: 10px!important;
	}

	.rwmb-tab-nav {
		margin-bottom: 22px !important;
	}

	.rwmb-tab-nav .rwmb-tab-active {
		border: 1px solid #0073aa !important;
		border-bottom-color: transparent !important;
		background: #0073aa !important;
		padding: 2px 10px !important;
	}

	.rwmb-tab-active a {
		color: #fff !important;
	}

	.rwmb-tab-panel {
		padding: 25px 11px !important;
	}

	.rwmb-label {
		width: 25%;
		margin-right: 10% !important;
	}

	.rwmb-label ~ .rwmb-input {
		width: 65% !important;
	}

	.rwmb-text_list-non-cloneable:not(.default-column) > .rwmb-input > label, .rwmb-text_list-clone > label {
		width: 47%;
	}

	.rwmb-text_list-non-cloneable:not(.default-column) > .rwmb-input > label > input, .rwmb-text_list-clone > label > input {
		width: 100%;
	}

	.rwmb-text_list-non-cloneable p {
		width: 100%;
	}

	.rwmb-field.rwmb-sidebar-wrapper .rwmb-inline li{
		margin-bottom:10px;
	}

	div.nt-page-info {
		padding: 15px;
		background: #e9e9e9;
		font-size: 16px;
		font-weight: 600;
		color: #222;
		border-left: 4px solid #E91E63;
	}

	div.nt-page-info > span.dashicons {
		margin-right: 15px;
	}

	.vc_col-xs-12.wpb_el_type_agro_new_param.vc_wrapper-param-type-agro_new_param.vc_shortcode-param.vc_column {
		border-bottom: 1px solid;
		padding: 7px 0px;
		width: 94%;
		margin: 0 auto;
		font-size: 16px;
		font-weight: 900;
		margin-bottom: 20px;
		margin-top: 15px;
	}

	body .pt15.vc_shortcode-param.vc_column {
		padding-top: 15px!important;
	}

    .wpb_el_type_nt_spacer.vc_wrapper-param-type-nt_spacer {
        padding: 8px 8px 4px 8px!important;
        text-align: center;
        font-size: 16px;
        font-weight: bold;
        background: #0473aa;
        color: #fff;
        margin: 15px;
    }

    .wpb_el_type_nt_hr.vc_wrapper-param-type-nt_hr {
        border-top: 1px solid #ddd;
        margin-top: 18px;
        padding: 0!important;
    }

    /* nested-container */

    .vc_nt-nested-container.vc_shortcodes_container > .vc_controls > .vc_control {
        margin-right: 10px;
        background-color: #fcb714;
        padding: 6px;
        color: #fff;
    }
    .wpb_agro_custom_slider_item.vc_nt-nested-container > .vc_controls > .vc_control,
    .wpb_agro_custom_slider_item_two.vc_nt-nested-container > .vc_controls > .vc_control{
        margin-right: 5px;
        background-color: #0085ba;
        padding: 4px;
        color: #fff;
    }
    .vc_nt-nested-container.vc_shortcodes_container > .vc_controls {
        padding: 6px;
        height: 40px;
    }
    .wpb_agro_custom_slider_container.wpb_sortable.vc_nt-nested-container.wpb_content_holder.vc_shortcodes_container,
    .wpb_agro_custom_slider_container_two.wpb_sortable.vc_nt-nested-container.wpb_content_holder.vc_shortcodes_container{
        margin: 15px 0;
        padding: 15px 0;
    }
    .vc_nt-nested-container .wpb_agro_custom_slider_item.wpb_sortable.vc_nt-nested-container.wpb_content_holder.vc_shortcodes_container,
    .vc_nt-nested-container .wpb_agro_custom_slider_item_two.wpb_sortable.vc_nt-nested-container.wpb_content_holder.vc_shortcodes_container {
        margin: 15px;
    }
    .wpb_agro_custom_slider_item.wpb_sortable.vc_nt-nested-container.wpb_content_holder.vc_shortcodes_container,
    .wpb_agro_custom_slider_item_two.wpb_sortable.vc_nt-nested-container.wpb_content_holder.vc_shortcodes_container{
        position: relative;
        border: 1px solid #ddd;
        padding: 10px 0;
    }
    .vc_nt-nested-container .vc_empty-container:after {
        display: flex;
    }
	.redux_field_th {
		color: #191919;
		font-weight: 700;
	}

	.redux-main .description {
		display: block;
		font-weight: normal;
	}

	#redux-header .rAds {
	  opacity: 0 !important;
	  display: none !important;
	  visibility : hidden;
	}

	#customize-controls img {
		max-width: 75%;
	}
    option[value="new"] {
        background: #4bbe60!important;
        color: #fff;
    }
    option[value="hot"] {
        background: #eb9f34!important;
        color: #fff;
    }
  '; // end $theCSS

    /* Add CSS to style.css */
    wp_register_style('agro-admin-custom-style', false);
    wp_enqueue_style('agro-admin-custom-style');
    wp_add_inline_style('agro-admin-custom-style', $theCSS);
}

add_action('admin_enqueue_scripts', 'agro_admin_custom_css');
