<?php

#Frontend
if (!function_exists('css_js_register')) {
    function css_js_register() {
        $version       = wp_get_theme()->get('Version');

        wp_register_script('gt3-theme', get_template_directory_uri() . '/js/theme.js', array('jquery'), $version, true);
        $translation_array = array(
            'ajaxurl' => esc_url(admin_url('admin-ajax.php')),
            'templateUrl' => esc_url(get_stylesheet_directory_uri())
        );
        wp_localize_script('gt3-theme', 'gt3_gt3theme', $translation_array);

        #CSS
        wp_enqueue_style('gt3-theme-default-style', get_bloginfo('stylesheet_url'), array(), $version);
        wp_enqueue_style('gt3-theme-icon', get_template_directory_uri() . '/fonts/theme-font/theme_icon.css');
        wp_enqueue_style('font-awesome', get_template_directory_uri() . '/css/font-awesome.min.css');
        if (class_exists('YITH_WCWL_Init')) {
            wp_dequeue_style('yith-wcwl-font-awesome');
        }
        wp_enqueue_style('select2', get_template_directory_uri() . '/css/select2.min.css', array(), '4.0.5', true);
        wp_enqueue_style('gt3-theme', get_template_directory_uri() . '/css/theme.css', array(), $version);
        wp_enqueue_style('gt3-elementor', get_template_directory_uri() . '/css/base-elementor.css', array(), $version);
        wp_enqueue_style('gt3-photo-modules', get_template_directory_uri() . '/css/photo_modules.css', array(), $version);
        wp_enqueue_style('gt3-responsive', get_template_directory_uri() . '/css/responsive.css', array(), $version);

        #JS
        wp_register_script('jquery-slick', get_template_directory_uri() . '/js/slick.min.js', array('jquery'), '1.8.0', true);
        wp_enqueue_script('cookie', get_template_directory_uri() . '/js/jquery.cookie.js', array(), false, true);
        wp_enqueue_script('gt3-theme', get_template_directory_uri() . '/js/theme.js', array('jquery'), $version, true);
        wp_enqueue_script('event-swipe', get_template_directory_uri() . '/js/jquery.event.swipe.js', array(), false, true);
        wp_enqueue_script('select2', get_template_directory_uri() . '/js/select2.full.min.js', array(), '4.0.5', false);

        wp_register_script('google-maps-api', add_query_arg('key', gt3_option('google_map_api_key'), '//maps.google.com/maps/api/js'), array(), '', true);
    }
}
add_action('wp_enqueue_scripts', 'css_js_register', 25);

#Admin
add_action('admin_enqueue_scripts', 'admin_css_js_register');
function admin_css_js_register() {
    #CSS (MAIN)
    wp_enqueue_style('font-awesome', get_template_directory_uri() . '/css/font-awesome.min.css');
    wp_enqueue_style('font-awesome', get_template_directory_uri() . '/css/fa-brands.min.css');
    wp_enqueue_style('gt3-admin', get_template_directory_uri() . '/core/admin/css/admin.css');
    wp_enqueue_style('gt3-admin-font', '//fonts.googleapis.com/css?family=Roboto:400,700,300');
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_style('gt3-admin-colorbox', get_template_directory_uri() . '/core/admin/css/colorbox.css');
    wp_enqueue_style('selectBox', get_template_directory_uri() . '/core/admin/css/jquery.selectBox.css');

    #JS (MAIN)
    wp_enqueue_script('gt3-admin', get_template_directory_uri() . '/core/admin/js/admin.js', array('jquery'), false, true);
    wp_enqueue_media();
    wp_enqueue_script('admin-colorbox', get_template_directory_uri() . '/core/admin/js/jquery.colorbox-min.js', array(), false, true);
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_script('selectBox', get_template_directory_uri() . '/core/admin/js/jquery.selectBox.js');

    if (class_exists('RWMB_Loader')) {
        wp_enqueue_script('gt3-metaboxes', get_template_directory_uri() . '/core/admin/js/metaboxes.js');
    }
}

function gt3_custom_styles() {
    $RWMB_Loader = class_exists('RWMB_Loader');
    $custom_css  = '';

    // THEME COLOR
    $theme_color  = esc_attr(gt3_option("theme-custom-color"));
    // END THEME COLOR

    // BODY BACKGROUND
    $bg_body = esc_attr(gt3_option('body-background-color'));
    // END BODY BACKGROUND

    // BODY TYPOGRAPHY
    $main_font = gt3_option('main-font');
    if (!empty($main_font) && is_array($main_font)) {
        $content_font_family = !empty($main_font['font-family']) ? esc_attr($main_font['font-family']) : '';
        $content_line_height = !empty($main_font['line-height']) ? esc_attr($main_font['line-height']) : '';
        $content_font_size   = !empty($main_font['font-size']) ? esc_attr($main_font['font-size']) : '';
        $content_font_weight = !empty($main_font['font-weight']) ? esc_attr($main_font['font-weight']) : '';
        $content_color       = !empty($main_font['color']) ? esc_attr($main_font['color']) : '';
    } else {
        $content_font_family = '';
        $content_line_height = '';
        $content_font_size   = '';
        $content_font_weight = '';
        $content_color       = '';
    }
    $map_marker_font = gt3_option('map-marker-font');
    if (!empty($map_marker_font) && is_array($map_marker_font)) {
        $map_marker_font_family = !empty($map_marker_font['color']) ? esc_attr($map_marker_font['font-family']) : '';
        $map_marker_font_weight = !empty($map_marker_font['color']) ? esc_attr($map_marker_font['font-weight']) : '';
    } else {
        $map_marker_font_family = '';
        $map_marker_font_weight = '';
    }
    // END BODY TYPOGRAPHY

    // HEADER TYPOGRAPHY
    $header_font = gt3_option('header-font');
    if (!empty($header_font)) {
        $header_font_family = !empty($header_font['font-family']) ? esc_attr($header_font['font-family']) : '';
        $header_font_weight = !empty($header_font['font-weight']) ? esc_attr($header_font['font-weight']) : '';
        $header_font_color  = !empty($header_font['color']) ? esc_attr($header_font['color']) : '';
    } else {
        $header_font_family = '';
        $header_font_weight = '';
        $header_font_color  = '';
    }

    $h1_font = gt3_option('h1-font');
    if (!empty($h1_font && is_array($h1_font))) {
        $H1_font_family      = !empty($h1_font['font-family']) ? esc_attr($h1_font['font-family']) : '';
        $H1_font_weight      = !empty($h1_font['font-weight']) ? esc_attr($h1_font['font-weight']) : '';
        $H1_font_line_height = !empty($h1_font['line-height']) ? esc_attr($h1_font['line-height']) : '';
        $H1_font_size        = !empty($h1_font['font-size']) ? esc_attr($h1_font['font-size']) : '';
    } else {
        $H1_font_family      = '';
        $H1_font_weight      = '';
        $H1_font_line_height = '';
        $H1_font_size        = '';
    }

    $h2_font = gt3_option('h2-font');
    if (!empty($h2_font) && is_array($h2_font)) {
        $H2_font_family      = !empty($h2_font['font-family']) ? esc_attr($h2_font['font-family']) : '';
        $H2_font_weight      = !empty($h2_font['font-weight']) ? esc_attr($h2_font['font-weight']) : '';
        $H2_font_line_height = !empty($h2_font['line-height']) ? esc_attr($h2_font['line-height']) : '';
        $H2_font_size        = !empty($h2_font['font-size']) ? esc_attr($h2_font['font-size']) : '';
    } else {
        $H2_font_family      = '';
        $H2_font_weight      = '';
        $H2_font_line_height = '';
        $H2_font_size        = '';
    }

    $h3_font = gt3_option('h3-font');
    if (!empty($h3_font) && is_array($h3_font)) {
        $H3_font_family      = !empty($h3_font['font-family']) ? esc_attr($h3_font['font-family']) : '';
        $H3_font_weight      = !empty($h3_font['font-weight']) ? esc_attr($h3_font['font-weight']) : '';
        $H3_font_line_height = !empty($h3_font['line-height']) ? esc_attr($h3_font['line-height']) : '';
        $H3_font_size        = !empty($h3_font['font-size']) ? esc_attr($h3_font['font-size']) : '';
    } else {
        $H3_font_family      = '';
        $H3_font_weight      = '';
        $H3_font_line_height = '';
        $H3_font_size        = '';
    }

    $h4_font = gt3_option('h4-font');
    if (!empty($h4_font) && is_array($h4_font)) {
        $H4_font_family      = !empty($h4_font['font-family']) ? esc_attr($h4_font['font-family']) : '';
        $H4_font_weight      = !empty($h4_font['font-weight']) ? esc_attr($h4_font['font-weight']) : '';
        $H4_font_line_height = !empty($h4_font['line-height']) ? esc_attr($h4_font['line-height']) : '';
        $H4_font_size        = !empty($h4_font['font-size']) ? esc_attr($h4_font['font-size']) : '';
    } else {
        $H4_font_family      = '';
        $H4_font_weight      = '';
        $H4_font_line_height = '';
        $H4_font_size        = '';
    }

    $h5_font = gt3_option('h5-font');
    if (!empty($h5_font) && is_array($h5_font)) {
        $H5_font_family      = !empty($h5_font['font-family']) ? esc_attr($h5_font['font-family']) : '';
        $H5_font_weight      = !empty($h5_font['font-weight']) ? esc_attr($h5_font['font-weight']) : '';
        $H5_font_line_height = !empty($h5_font['line-height']) ? esc_attr($h5_font['line-height']) : '';
        $H5_font_size        = !empty($h5_font['font-size']) ? esc_attr($h5_font['font-size']) : '';
    } else {
        $H5_font_family      = '';
        $H5_font_weight      = '';
        $H5_font_line_height = '';
        $H5_font_size        = '';
    }

    $h6_font = gt3_option('h6-font');
    if (!empty($h6_font) && is_array($h6_font)) {
        $H6_font_family         = !empty($h6_font['font-family']) ? esc_attr($h6_font['font-family']) : '';
        $H6_font_weight         = !empty($h6_font['font-weight']) ? esc_attr($h6_font['font-weight']) : '';
        $H6_font_line_height    = !empty($h6_font['line-height']) ? esc_attr($h6_font['line-height']) : '';
        $H6_font_size           = !empty($h6_font['font-size']) ? esc_attr($h6_font['font-size']) : '';
        $H6_font_color          = !empty($h6_font['color']) ? esc_attr($h6_font['color']) : '';
        $H6_font_letter_spacing = !empty($h6_font['letter-spacing']) ? esc_attr($h6_font['letter-spacing']) : '';
        $H6_font_text_transform = !empty($h6_font['text-transform']) ? esc_attr($h6_font['text-transform']) : '';
    } else {
        $H6_font_family         = '';
        $H6_font_weight         = '';
        $H6_font_line_height    = '';
        $H6_font_size           = '';
        $H6_font_color          = '';
        $H6_font_letter_spacing = '';
        $H6_font_text_transform = '';
    }

    $menu_font = gt3_option('menu-font');
    if (!empty($menu_font)) {
        $menu_font_family      = !empty($menu_font['font-family']) ? esc_attr($menu_font['font-family']) : '';
        $menu_font_weight      = !empty($menu_font['font-weight']) ? esc_attr($menu_font['font-weight']) : '';
        $menu_font_line_height = !empty($menu_font['line-height']) ? esc_attr($menu_font['line-height']) : '';
        $menu_font_size        = !empty($menu_font['font-size']) ? esc_attr($menu_font['font-size']) : '';
        $menu_font_letter_spacing = !empty($menu_font['letter-spacing']) ? esc_attr($menu_font['letter-spacing']) : '';
        $menu_font_text_transform = !empty($menu_font['text-transform']) ? esc_attr($menu_font['text-transform']) : '';
    } else {
        $menu_font_family      = '';
        $menu_font_weight      = '';
        $menu_font_line_height = '';
        $menu_font_size        = '';
        $menu_font_letter_spacing = '';
        $menu_font_text_transform = '';
    }

    $sub_menu_bg          = gt3_option('sub_menu_background');
    $sub_menu_color       = gt3_option('sub_menu_color');
    $sub_menu_color_hover = gt3_option('sub_menu_color_hover');

    $logo_height          = gt3_option( 'logo_height' );
    $logo_tablet_width    = gt3_option("logo_teblet_width");
    $logo_mobile_width    = gt3_option("logo_mobile_width");

    $map_marker_info_bgr  = esc_attr(gt3_option("map_marker_info_background"));
    $map_marker_info_clr  = esc_attr(gt3_option("map_marker_info_color"));

    /* GT3 Header Builder */
    $sections = array('top','middle','bottom','top__tablet','middle__tablet','bottom__tablet','top__mobile','middle__mobile','bottom__mobile');
    $desktop_sides = array('top', 'middle', 'bottom');

    foreach ($sections as $section) {
        ${'side_' . $section . '_custom'} = gt3_option('side_'.$section.'_custom');
        ${'side_' . $section . '_background'} = gt3_option('side_'.$section.'_background');
        if (!empty(${'side_' . $section . '_background'}['rgba'])) {
            ${'side_' . $section . '_background'} = ${'side_' . $section . '_background'}['rgba'];
        }else{
            ${'side_' . $section . '_background'} = '';
        }


        ${'side_' . $section . '_background2'} = gt3_option('side_'.$section.'_background2');
        if (!empty(${'side_' . $section . '_background2'}['rgba'])) {
            ${'side_' . $section . '_background2'} = ${'side_' . $section . '_background2'}['rgba'];
        }else{
            ${'side_' . $section . '_background2'} = '';
        }

        ${'side_' . $section . '_spacing'}  = gt3_option('side_'.$section.'_spacing');

        ${'side_' . $section . '_color'}  = gt3_option('side_'.$section.'_color');
        ${'side_' . $section . '_color_hover'}  = gt3_option('side_'.$section.'_color_hover');
        ${'side_' . $section . '_height'} = gt3_option('side_'.$section.'_height');
        ${'side_' . $section . '_height'} = ${'side_' . $section . '_height'}['height'];
        ${'side_' . $section . '_border'} = (bool)gt3_option('side_' . $section . '_border');
        ${'side_' . $section . '_border_color'} = gt3_option('side_' . $section . '_border_color');

        ${'side_' . $section . '_border_radius'} = gt3_option('side_' . $section . '_border_radius');
    }

    $logo_limit_on_mobile = gt3_option("logo_limit_on_mobile");

    $header_sticky              = gt3_option("header_sticky");
    foreach ($desktop_sides as $sticky_side) {
        ${'side_'.$sticky_side.'_sticky'}            = gt3_option('side_'.$sticky_side.'_sticky');
        ${'side_'.$sticky_side.'_background_sticky'} = gt3_option('side_'.$sticky_side.'_background_sticky');
        ${'side_'.$sticky_side.'_color_sticky'}      = gt3_option('side_'.$sticky_side.'_color_sticky');
        ${'side_'.$sticky_side.'_color_hover_sticky'}= gt3_option('side_'.$sticky_side.'_color_hover_sticky');
        ${'side_'.$sticky_side.'_height_sticky'}     = gt3_option('side_'.$sticky_side.'_height_sticky');
        ${'side_'.$sticky_side.'_spacing_sticky'}     = gt3_option('side_'.$sticky_side.'_spacing_sticky');
    }

    /* mobile options */
    $id = gt3_get_queried_object_id();
    if ($RWMB_Loader && $id !== 0) {
        $mb_header_presets = rwmb_meta('mb_header_presets', array(), $id);
        $presets = gt3_option('gt3_header_builder_presets');
        if ($mb_header_presets != 'default' && isset($mb_header_presets) && !empty($presets[$mb_header_presets]) && !empty($presets[$mb_header_presets]['preset']) ) {

            $preset = $presets[$mb_header_presets]['preset'];
            $preset = json_decode($preset,true);

            $sub_menu_bg = gt3_option_presets($preset,'sub_menu_background');
            $sub_menu_color = gt3_option_presets($preset,'sub_menu_color');

            $logo_height       = gt3_option_presets($preset,'logo_height');
            $logo_tablet_width = gt3_option_presets($preset,"logo_teblet_width");
            $logo_mobile_width = gt3_option_presets($preset,"logo_mobile_width");

            foreach ($sections as $section) {
                ${'side_' . $section . '_background'} = gt3_option_presets($preset,'side_'.$section.'_background');
                ${'side_' . $section . '_background'} = ${'side_' . $section . '_background'}['rgba'];

                ${'side_' . $section . '_background2'} = gt3_option_presets($preset,'side_'.$section.'_background2');
                ${'side_' . $section . '_background2'} = ${'side_' . $section . '_background2'}['rgba'];

                ${'side_' . $section . '_spacing'}  = gt3_option_presets($preset,'side_'.$section.'_spacing');

                ${'side_' . $section . '_color'}  = gt3_option_presets($preset,'side_'.$section.'_color');
                ${'side_' . $section . '_color_hover'}  = gt3_option_presets($preset,'side_'.$section.'_color_hover');
                ${'side_' . $section . '_height'} = gt3_option_presets($preset,'side_'.$section.'_height');
                ${'side_' . $section . '_height'} = ${'side_' . $section . '_height'}['height'];
                ${'side_' . $section . '_border'} = (bool)gt3_option_presets($preset,'side_' . $section . '_border');
                ${'side_' . $section . '_border_color'} = gt3_option_presets($preset,'side_' . $section . '_border_color');

                ${'side_' . $section . '_border_radius'} = gt3_option_presets($preset,'side_' . $section . '_border_radius');
            }

            $header_sticky = gt3_option_presets($preset,"header_sticky");

            foreach ($desktop_sides as $sticky_side) {
                ${'side_'.$sticky_side.'_sticky'} = gt3_option_presets($preset,'side_'.$sticky_side.'_sticky');
                ${'side_'.$sticky_side.'_background_sticky'} = gt3_option_presets($preset,'side_'.$sticky_side.'_background_sticky');
                ${'side_'.$sticky_side.'_color_sticky'} = gt3_option_presets($preset,'side_'.$sticky_side.'_color_sticky');
                ${'side_'.$sticky_side.'_color_hover_sticky'} = gt3_option_presets($preset,'side_'.$sticky_side.'_color_hover_sticky');
                ${'side_'.$sticky_side.'_height_sticky'} = gt3_option_presets($preset,'side_'.$sticky_side.'_height_sticky');
                ${'side_'.$sticky_side.'_spacing_sticky'} = gt3_option_presets($preset,'side_'.$sticky_side.'_spacing_sticky');
            }
        }
    }

    /* End GT3 Header Builder */


    // END HEADER TYPOGRAPHY


    $custom_css .= '
    /* Custom CSS */
        
    body,
    body .widget .yit-wcan-select-open,
    body .widget-hotspot,
    body div[id*="ajaxsearchlitesettings"].searchsettings form fieldset legend,
    .prev_next_links_fullwidht .link_item,
    span.elementor-drop-cap span.elementor-drop-cap-letter,
    input[type="date"],
    input[type="email"],
    input[type="number"],
    input[type="password"],
    input[type="search"],
    input[type="tel"],
    input[type="text"],
    input[type="url"],
    select,
    textarea,
    blockquote cite,
    blockquote code,
    .single_prev_next_posts .gt3_post_navi:before,
    .woocommerce nav.woocommerce-pagination ul li a,
     .woocommerce nav.woocommerce-pagination ul li span{
        ' . (!empty($content_font_family) ? 'font-family:' . $content_font_family . ';' : '') . '
    }
    body {
        ' . (!empty($content_font_size) ? 'font-size:' . $content_font_size . ';' : '') . '
        ' . (!empty($content_line_height) ? 'line-height:' . $content_line_height . ';' : '') . '
        ' . (!empty($content_font_weight) ? 'font-weight:' . $content_font_weight . ';' : '') . '
        ' . (!empty($content_color) ? 'color:' . $content_color . ';' : '') . '
    }
    .post_share_block:hover > .post_share_wrap ul li {
        ' . (!empty($bg_body) ? 'background:' . $bg_body . ';' : '') . '
    }
    .single .post_share_block:hover > .post_share_wrap ul li {
        ' . (!empty($bg_body) ? 'background:' . $bg_body . ' !important;' : '') . '
    }
    p{
        line-height: ' . ((int)$content_line_height/(int)$content_font_size) . ';
    }
    
    /* Custom Fonts */
    .module_team .team_info,
    h1,
    h2,
    h3,
    h4,
    h5,
    h6,
    .gt3_header_builder_component.gt3_header_builder_search_cat_component .gt3-search_cat-select,
    .main_wrapper .gt3_search_form:before,
    .logged-in-as a:hover,
    .sidebar-container .widget.widget_posts .recent_posts .post_title a,
    .gt3_header_builder_component .woocommerce-mini-cart__empty-message,
    .elementor-widget-gt3-core-button.gt3_portfolio_view_more_link_wrapper .gt3_module_button_elementor:not(.hover_type2):not(.hover_type4):not(.hover_type5)   .elementor_gt3_btn_text,
    .single_prev_next_posts .gt3_post_navi:before,
    .gt3-wpcf7-subscribe-style input[type="date"], 
    .gt3-wpcf7-subscribe-style input[type="email"], 
    .gt3-wpcf7-subscribe-style input[type="number"], 
    .gt3-wpcf7-subscribe-style input[type="password"], 
    .gt3-wpcf7-subscribe-style input[type="search"], 
    .gt3-wpcf7-subscribe-style input[type="tel"], 
    .gt3-wpcf7-subscribe-style input[type="text"], 
    .gt3-wpcf7-subscribe-style input[type="url"], 
    .gt3-wpcf7-subscribe-style textarea, 
    .gt3-wpcf7-subscribe-style select,
    .elementor-widget-gt3-core-portfolio .portfolio_wrapper.hover_type6 .text_wrap .title,
     .blog_post_media--quote .quote_text,
     .blog_post_media__link_text{
        ' . (!empty($header_font_color) ? 'color:' . $header_font_color . ';' : '') . '
    }
    .search-results .blogpost_title a {
        ' . (!empty($header_font_color) ? 'color:' . $header_font_color . ' !important;' : '') . '
    }
    .search-results .blogpost_title a:hover {
        ' . (!empty($theme_color) ? 'color:' . $theme_color . ' !important;' : '') . '
    }
    .gt3_icon_box__icon--number,
    h1,
    h2,
    h3,
    h4,
    h5,
    h6,
    .strip_template .strip-item a span,
    .column1 .item_title a,
    .index_number,
    .price_item_btn a,
    .shortcode_tab_item_title,
    .gt3_twitter .twitt_title,
    .gt3_page_title_cats a,
    .quote_author,
	blockquote cite,
	blockquote code,
	.comment-reply-link,
    .contacts_form input.wpcf7-form-control.wpcf7-submit{
        ' . (!empty($header_font_family) ? 'font-family:' . $header_font_family . ';' : '') . '
        ' . (!empty($header_font_weight) ? 'font-weight:' . $header_font_weight . ';' : '') . '
    }
    .gt3-page-title .page_title_meta.cpt_portf * {
        font-weight: inherit;
    }
    .gt3_page_title_cats a:hover,
    .format-video .gt3_video__play_button:hover,
    .widget .calendar_wrap tbody td > a:before,
    .portfolio_wrapper .elementor-widget-gt3-core-button.gt3_portfolio_view_more_link_wrapper .gt3_module_button_elementor:not(.hover_type2):not(.hover_type4):not(.hover_type5) a:hover {
        ' . (!empty($theme_color) ? 'background:' . $theme_color . ';' : '') . '
    }
    h1,
    .elementor-widget-heading h1.elementor-heading-title {
        ' . (!empty($H1_font_family) ? 'font-family:' . $H1_font_family . ';' : '') . '
        ' . (!empty($H1_font_weight) ? 'font-weight:' . $H1_font_weight . ';' : '') . '
        ' . (!empty($H1_font_size) ? 'font-size:' . $H1_font_size . ';' : '') . '
        ' . (!empty($H1_font_line_height) ? 'line-height:' . $H1_font_line_height . ';' : '') . '
    }
    h2,
    .blog_post_media__link_text.blogpost_title > a,
    .woocommerce-Tabs-panel #comments h2,
    .woocommerce-Tabs-panel #comments h2 span,
    .elementor-widget-heading h2.elementor-heading-title,
    .elementor-widget-gt3-core-blog .blogpost_title {
        ' . (!empty($H2_font_family) ? 'font-family:' . $H2_font_family . ';' : '') . '
        ' . (!empty($H2_font_weight) ? 'font-weight:' . $H2_font_weight . ';' : '') . '
        ' . (!empty($H2_font_size) ? 'font-size:' . $H2_font_size . ';' : '') . '
        ' . (!empty($H2_font_line_height) ? 'line-height:' . $H2_font_line_height . ';' : '') . '
    }
    h3,
    .elementor-widget-heading h3.elementor-heading-title,
    #customer_login h2,
    .gt3_header_builder__login-modal_container h2,
    .sidepanel .title{
        ' . (!empty($H3_font_family) ? 'font-family:' . $H3_font_family . ';' : '') . '
        ' . (!empty($H3_font_weight) ? 'font-weight:' . $H3_font_weight . ';' : '') . '
        ' . (!empty($H3_font_size) ? 'font-size:' . $H3_font_size . ';' : '') . '
        ' . (!empty($H3_font_line_height) ? 'line-height:' . $H3_font_line_height . ';' : '') . '
    }
    h4,
    .elementor-widget-heading h4.elementor-heading-title {
        ' . (!empty($H4_font_family) ? 'font-family:' . $H4_font_family . ';' : '') . '
        ' . (!empty($H4_font_weight) ? 'font-weight:' . $H4_font_weight . ';' : '') . '
        ' . (!empty($H4_font_size) ? 'font-size:' . $H4_font_size . ';' : '') . '
        ' . (!empty($H4_font_line_height) ? 'line-height:' . $H4_font_line_height . ';' : '') . '
    }
    h5,
    .elementor-widget-heading h5.elementor-heading-title {
        ' . (!empty($H5_font_family) ? 'font-family:' . $H5_font_family . ';' : '') . '
        ' . (!empty($H5_font_weight) ? 'font-weight:' . $H5_font_weight . ';' : '') . '
        ' . (!empty($H5_font_size) ? 'font-size:' . $H5_font_size . ';' : '') . '
        ' . (!empty($H5_font_line_height) ? 'line-height:' . $H5_font_line_height . ';' : '') . '
    }
    h6,
    .elementor-widget-heading h6.elementor-heading-title {
        ' . (!empty($H6_font_family) ? 'font-family:' . $H6_font_family . ';' : '') . '
        ' . (!empty($H6_font_weight) ? 'font-weight:' . $H6_font_weight . ';' : '') . '
        ' . (!empty($H6_font_size) ? 'font-size:' . $H6_font_size . ';' : '') . '
        ' . (!empty($H6_font_line_height) ? 'line-height:' . $H6_font_line_height . ';' : '') . '
        ' . (!empty($H6_font_color) ? 'color:' . $H6_font_color . ';' : '') . '
        ' . (!empty($H6_font_letter_spacing) ? 'letter-spacing:' . $H6_font_letter_spacing . ';' : '') . '
        ' . (!empty($H6_font_text_transform) ? 'text-transform:' . $H6_font_text_transform . ';' : '') . '
    }
    
    a:hover,
	.woocommerce-MyAccount-navigation ul li a,
    .diagram_item .chart,
    .item_title a ,
    .contentarea ul,
    .blog_post_media--link .blog_post_media__link_text p,
    .elementor-shortcode .has_only_email input[type="text"],
    .elementor-shortcode .has_only_email .mc_merge_var label,
     .woocommerce-LostPassword a:hover,
    .quote_author,
	blockquote cite,
	blockquote code,
    .comment-reply-link:hover,
    .gt3_module_button_list a:hover{
        ' . (!empty($header_font_color) ? 'color:' . $header_font_color . ';' : '') . '
    }


    input[type="submit"],
    button,
    .woocommerce #respond input#submit,
    .woocommerce a.button,
    .woocommerce button.button,
    .woocommerce input.button,
    .gt3_header_builder_cart_component .buttons .button,
    .gt3_module_button a,
    .learn_more,
    .testimonials_title,
    blockquote p:last-child,
    .gt3_module_button_list a,
    .elementor-widget-gt3-core-button .elementor_gt3_btn_text,
    .mc_form_inside.has_only_email .mc_signup_submit,
    .single_prev_next_posts a,
    .woocommerce ul.product_list_widget li a .product-title{
        ' . (!empty($header_font_family) ? 'font-family:' . $header_font_family . ';' : '') . '
    }

    /* Theme color */
    a,
    .calendar_wrap thead,
    .gt3_practice_list__image-holder i,
    .load_more_works:hover,
    .copyright a:hover,
    .price_item .items_text ul li:before,
    .price_item.most_popular .item_cost_wrapper h3,
    .gt3_practice_list__title a:hover,
    #select2-gt3_product_cat-results li,
    .listing_meta,
    .ribbon_arrow,
    .flow_arrow,
    .main_wrapper #main_content ul.gt3_list_line li:before,
    .main_wrapper .elementor-section ul.gt3_list_line li:before,
    .main_wrapper #main_content ul.gt3_list_disc li:before,
    .main_wrapper .elementor-section ul.gt3_list_disc li:before,
    .top_footer a:hover,    
    .top_footer .widget.widget_nav_menu ul li > a:hover,
    .main_wrapper .sidebar-container .widget_categories ul > li.current-cat > a,
    .main_wrapper .sidebar-container .widget_categories ul > li > a:hover,
    .single_prev_next_posts a:hover .gt3_post_navi:after,
    .gt3_practice_list__link:before,
    .load_more_works,
    .woocommerce ul.products li.product .woocommerce-loop-product__title:hover,
    .woocommerce ul.cart_list li a:hover,
    ul.gt3_list_disc li:before,
	.woocommerce-MyAccount-navigation ul li a:hover,
	.elementor-widget-gt3-core-portfolio .portfolio_wrapper.hover_type6 .text_wrap:hover .title,
	footer.main_footer .mc_form_inside .mc_signup_submit:before,
	.header_search .header_search__icon:hover i,
	.search_form.button-hover:after,
	.header_search__inner .search_form.button-hover:after,
    .widget_product_search .gt3_search_form:hover:after,
    .mc_form_inside.has_only_email .mc_signup_submit input#mc_signup_submit:hover,
    input[type="submit"]:hover,
    .main_wrapper .content-container ul:not(.variable-items-wrapper) > li:before,
    .content-container ul > li:before,
    .main_wrapper #main_content ul[class*="gt3_list_"] li:before,
	.single_prev_next_posts a:hover{
        ' . (!empty($theme_color) ? 'color:' . $theme_color . ';' : '') . '
	}
    .tagcloud a:hover,
	.gt3_practice_list__link:before,
	.load_more_works,
    #back_to_top,
    .gt3_header_builder .menu_item_line,
    .contact-form-2 input.wpcf7-submit,
	.woocommerce .gt3-products-bottom nav.woocommerce-pagination ul li .page-numbers.current{
        ' . (!empty($theme_color) ? 'background-color:' . $theme_color . ';' : '') . '
    }
    .main_wrapper .gt3_product_list_nav li a:hover {
        ' . (!empty($theme_color) ? 'color:' . $theme_color . ';' : '') . '
    }
    .calendar_wrap caption,
    .widget .calendar_wrap table td#today:before {
        ' . (!empty($theme_color) ? 'background:' . $theme_color . ';' : '') . '
    }
    .wpcf7-form label,
    .woocommerce div.product .woocommerce-tabs ul.tabs li a:hover,
    div:not(.packery_wrapper) .blog_post_preview .listing_meta a:hover,
    .blog_post_media--quote .quote_text a:hover {
        ' . (!empty($theme_color) ? 'color:' . $theme_color . ';' : '') . '
    }
    .blogpost_title a:hover {
        ' . (!empty($theme_color) ? 'color:' . $theme_color . ' !important;' : '') . '
    }
    .gt3_icon_box__link a:before,
    .gt3_icon_box__link a:before,
    .stripe_item-divider,
	ul.pagerblock li a.current{
        ' . (!empty($theme_color) ? 'background-color:' . $theme_color . ';' : '') . '
    }
    .single-member-page .member-icon:hover,
    .single-member-page .team-link:hover,
    .module_testimonial blockquote:before,
    .module_testimonial .testimonials_title,
    .sidebar .widget_nav_menu .menu .menu-item > a:hover,
    .gt3_widget > ul > li a:hover,  
    #main_content ul.wp-block-archives li > a:hover,
    #main_content ul.wp-block-categories li > a:hover,
    #main_content ul.wp-block-latest-posts li > a:hover,
    #respond #commentform p[class*="comment-form-"] > label.gt3_onfocus,
    .comment-notes .required,
    #cancel-comment-reply-link,
    .top_footer .widget.widget_recent_entries ul li > a:hover {
        ' . (!empty($theme_color) ? 'color:' . $theme_color . ';' : '') . '
    }

    /* menu fonts */
    .main-menu>.gt3-menu-categories-title,
    .main-menu>ul,
    .main-menu>div>ul,
    .column_menu>ul,
    .column_menu>.gt3-menu-categories-title,
    .column_menu>div>ul {
        ' . (!empty($menu_font_family) ? 'font-family:' . $menu_font_family . ';' : '') . '
        ' . (!empty($menu_font_weight) ? 'font-weight:' . $menu_font_weight . ';' : '') . '
        ' . (!empty($menu_font_line_height) ? 'line-height:' . $menu_font_line_height . ';' : '') . '
        ' . (!empty($menu_font_size) ? 'font-size:' . $menu_font_size . ';' : '') . '
        ' . (!empty($menu_font_letter_spacing) ? 'letter-spacing:' . $menu_font_letter_spacing . ';' : '') . '
        ' . (!empty($menu_font_text_transform) ? 'text-transform:' . $menu_font_text_transform . ';' : '') . '
    }

    /* sub menu styles */
    .main-menu ul.sub-menu li.menu-item:hover > a:hover,
    .column_menu ul li.menu-item:hover > a:hover,
    .main-menu .current_page_item,
    .main-menu .current-menu-item,
    .main-menu .current-menu-ancestor,
    .gt3_header_builder_menu_component .column_menu .menu li.current_page_item > a,
    .gt3_header_builder_menu_component .column_menu .menu li.current-menu-item > a,
    .gt3_header_builder_menu_component .column_menu .menu li.current-menu-ancestor > a,
    .column_menu .current_page_item,
    .column_menu .current-menu-item,
    .column_menu .current-menu-ancestor{
        ' . (!empty($sub_menu_color_hover) ? 'color:' . $sub_menu_color_hover . ';' : '') . '
    }


    .main-menu ul li ul.sub-menu,
    .column_menu ul li ul.sub-menu,
    .main_header .header_search__inner .search_form,
    .mobile_menu_container {
        background-color: ' . (!empty($sub_menu_bg['rgba']) ? esc_attr($sub_menu_bg['rgba']) : "transparent") . ' ;
        ' . (!empty($sub_menu_color) ? 'color:' . $sub_menu_color . ';' : '') . '
    }
    .main_header .header_search__inner .search_text::-webkit-input-placeholder{
        ' . (!empty($sub_menu_color) ? 'color:' . $sub_menu_color . ' !important;' : '') . '
    }
    .main_header .header_search__inner .search_text:-moz-placeholder {
        ' . (!empty($sub_menu_color) ? 'color:' . $sub_menu_color . ' !important;' : '') . '
    }
    .main_header .header_search__inner .search_text::-moz-placeholder {
        ' . (!empty($sub_menu_color) ? 'color:' . $sub_menu_color . ' !important;' : '') . '
    }
    .main_header .header_search__inner .search_text:-ms-input-placeholder {
        ' . (!empty($sub_menu_color) ? 'color:' . $sub_menu_color . ' !important;' : '') . '
    }

    /* widgets */
    body div[id*=\'ajaxsearchlitesettings\'].searchsettings fieldset .label:hover,
    body div[id*=\'ajaxsearchlite\'] .probox .proclose:hover,
    .module_team.type2 .team_title__text,
    .widget.widget_rss > ul > li a,
    .sidebar-container .widget.widget_posts .recent_posts .listing_meta span,
    .woocommerce ul.cart_list li .quantity,
    .woocommerce ul.product_list_widget li .quantity,
    .gt3_header_builder_cart_component__cart-container .total{
        ' . (!empty($header_font_color) ? 'color:' . $header_font_color . ';' : '') . '
    }
    #back_to_top.show:hover{
        ' . (!empty($header_font_color) ? 'background-color:' . $header_font_color . ';' : '') . '
    }

    /* blog */
    .countdown-period,
    .gt3-page-title_default_color_a .gt3-page-title__content .gt3_breadcrumb a,
    .gt3-page-title_default_color_a .gt3-page-title__content .gt3_breadcrumb .gt3_pagination_delimiter,
    .module_team.type2 .team-positions,
    .gt3_widget > ul > li a,
    #main_content ul.wp-block-archives li > a,
    #main_content ul.wp-block-categories li > a,
    #main_content ul.wp-block-latest-posts li > a,
    .sidebar .widget_nav_menu .menu .menu-item > a,
    .blog_post_info,
    .likes_block.already_liked .icon,
    .likes_block.already_liked:hover .icon,
    .isotope-filter a,
    .top_footer .tagcloud a{
        ' . (!empty($content_color) ? 'color:' . $content_color . ';' : '') . '
    }
    div:not(.packery_wrapper) .blog_post_preview .listing_meta {
        ' . (!empty($content_color) ? 'color: rgba('.gt3_HexToRGB($content_color).', 1);' : '') . '
    }
    .listing_meta span.post_category a:after {
        ' . (!empty($content_color) ? 'color: rgba('.gt3_HexToRGB($content_color).', 0.85);' : '') . '
    }
    body .gt3_module_related_posts .blog_post_preview .listing_meta {
        ' . (!empty($content_color) ? 'color: rgba('.gt3_HexToRGB($content_color).', 0.65);' : '') . '
    }
    .blogpost_title i,
    .widget.widget_recent_comments > ul > li a:hover,
    .widget.widget_rss > ul > li:hover a,
    .sidebar-container .widget.widget_posts .recent_posts .post_title a:hover,
    .comment_info a:hover,
    .contacts_form input.wpcf7-form-control.wpcf7-submit:hover,    
    .isotope-filter a.active,    
    .isotope-filter a:hover {
        ' . (!empty($theme_color) ? 'color:' . $theme_color . ';' : '') . '
    }
    .gt3_header_builder_cart_component__cart-container .total strong,
    .prev_next_links .title{
        ' . (!empty($header_font_color) ? 'color:' . $header_font_color . ';' : '') . '
    }

    .gt3_module_title .carousel_arrows a:hover span,
    .stripe_item:after,
    .packery-item .packery_overlay,
    .ui-datepicker .ui-datepicker-buttonpane button.ui-state-hover,
    .woocommerce div.product form.cart .button,
    .contacts_form input.wpcf7-form-control.wpcf7-submit,
    .gt3-page-title__content .gt3_pagination_delimiter{
        ' . (!empty($theme_color) ? 'background:' . $theme_color . ';' : '') . '
    }
    button,
    .tagcloud a:hover,
    .ui-datepicker .ui-datepicker-buttonpane button.ui-state-hover,
    .woocommerce ul.products li.product .gt3_woocommerce_open_control_tag_bottom div a,
    .woocommerce ul.products li.product .gt3_woocommerce_open_control_tag_bottom div a:hover,
    .woocommerce div.product form.cart .button,
    .woocommerce div.product form.cart .button:hover,
    .woocommerce-account .woocommerce-MyAccount-content .woocommerce-message--info .button,
    .woocommerce-account .woocommerce-MyAccount-content .woocommerce-message--info .button:hover,
    .contact-form-2 input.wpcf7-submit:hover,
    .contact-form-2 input.wpcf7-submit,
    .gt3_module_title .carousel_arrows a:hover span:before,
    .elementor-widget-gt3-core-accordion .accordion_wrapper .item_title span.ui-accordion-header-icon:before,
    .elementor-widget-gt3-core-accordion .item_title:not(.ui-state-active) .ui-accordion-header-icon:after,
    .contacts_form input.wpcf7-form-control.wpcf7-submit,
    .contacts_form input.wpcf7-form-control.wpcf7-submit:hover{
        ' . (!empty($theme_color) ? 'border-color:' . $theme_color . ';' : '') . '
    }
    .gt3_module_title .carousel_arrows a span,
    .elementor-slick-slider .slick-slider .slick-prev:after,
    .elementor-slick-slider .slick-slider .slick-next:after{
        ' . (!empty($header_font_color) ? 'background:' . $header_font_color . ';' : '') . '
    }
    .gt3_module_title .carousel_arrows a span:before {
        ' . (!empty($header_font_color) ? 'border-color:' . $header_font_color . ';' : '') . '
    }
    .post_share_block:hover > a,
    .woocommerce ul.products li.product .gt3_woocommerce_open_control_tag_bottom div a:hover,
    .woocommerce ul.products.list li.product .gt3_woocommerce_open_control_tag div a:hover:before, 
    .woocommerce ul.products li.product .gt3_woocommerce_open_control_tag_bottom div a:hover:before,
    .woocommerce div.product form.cart .button:hover,
    .single-product.woocommerce div.product .product_meta a:hover,
    .woocommerce div.product span.price,
    .likes_block:hover .icon,
    .woocommerce .gt3-pagination_nav nav.woocommerce-pagination ul li a.prev:hover,
    .woocommerce .gt3-pagination_nav nav.woocommerce-pagination ul li a.next:hover,
    .woocommerce .gt3-pagination_nav nav.woocommerce-pagination ul li a.gt3_show_all:hover,
    .woocommerce div.product div.images div.woocommerce-product-gallery__trigger:hover,
    .contact-form-2 input.wpcf7-submit:hover,
    .top_footer .widget.widget_archive ul li:hover > a,
    .top_footer .widget.widget_categories ul li:hover > a,
    .top_footer .widget.widget_pages ul li:hover > a, 
    .top_footer .widget.widget_meta ul li:hover > a, 
    .top_footer .widget.widget_recent_comments ul li:hover > a, 
    .top_footer .widget.widget_recent_entries ul li:hover > a,
    .top_footer .widget.widget_nav_menu ul li > a:hover{
        ' . (!empty($theme_color) ? 'color:' . $theme_color . ';' : '') . '
    }
    .gt3_practice_list__filter,
    .isotope-filter,
    .woocommerce ul.products li.product .price {
        ' . (!empty($header_font_color) ? 'color:' . $header_font_color . ';' : '') . '
    }

    ul.products:not(.list) li.product:hover .gt3_woocommerce_open_control_tag div a{
        ' . (!empty($header_font_color) ? 'background:' . $header_font_color . ';' : '') . '
    }

    .gt3_module_title .external_link .learn_more {
        ' . (!empty($content_line_height) ? 'line-height:' . $content_line_height . ';' : '') . '
    }
    
    .comment_content p{
        ' . (!empty($content_font_size) ? 'font-size:' . $content_font_size . ';' : '') . '
        ' . (!empty($content_line_height) ? 'line-height:' . $content_line_height . ';' : '') . '
    }

    .gt3_image_rotate .gt3_image_rotate_title {
        ' . (!empty($theme_color) ? 'color:' . $theme_color . ';' : '') . '
        ' . (!empty($bg_body) ? 'background:' . $bg_body . ';' : '') . '
    }

	blockquote:before,
	.blog_post_media--quote .quote_text:before,
	.blog_post_media__link_text:before,
    .blog_post_media__link_text a:hover,
    h3#reply-title a,
    .comment_author_says a:hover,
    .dropcap,
    .gt3_custom_text a,
    .gt3_custom_button i {
        ' . (!empty($theme_color) ? 'color:' . $theme_color . ';' : '') . '
    }
    .single .post_tags > span,
    h3#reply-title a:hover,
    .comment_author_says,
    .comment_author_says a {
        ' . (!empty($header_font_color) ? 'color:' . $header_font_color . ';' : '') . '
    }

    ::-moz-selection{' . (!empty($theme_color) ? 'background:' . $theme_color . ';' : '') . '}
    ::selection{' . (!empty($theme_color) ? 'background:' . $theme_color . ';' : '') . '}
    ';

    //sticky header logo
    $header_sticky_height = gt3_option('header_sticky_height');
    $custom_css           .= '
    .gt3_practice_list__overlay:before,
    .format-standard .blog_content .post-password-form input[type="submit"],
    .post-password-form input[type="submit"]{
        ' . (!empty($theme_color) ? 'background-color:' . $theme_color . ';' : '') . '
    }

    @media only screen and (max-width: 767px){
        .gt3-hotspot-shortcode-wrapper .gt3_tooltip{
        ' . (!empty($theme_color) ? 'background-color:' . $theme_color . ';' : '') . '
        }
    }
    ';


    // footer styles
    $footer_text_color    = gt3_option_compare('footer_text_color', 'mb_footer_switch', 'yes');
    $footer_heading_color = gt3_option_compare('footer_heading_color', 'mb_footer_switch', 'yes');
    $custom_css           .= '
    .top_footer .widget.widget_posts .recent_posts li > .recent_posts_content .post_title a,
    .top_footer .widget.widget_archive ul li > a,
    .top_footer .widget.widget_categories ul li > a,
    .top_footer .widget.widget_pages ul li > a,
    .top_footer .widget.widget_meta ul li > a,
    .top_footer .widget.widget_recent_comments ul li > a,
    .top_footer .widget.widget_recent_entries ul li > a,
    .main_footer .top_footer .widget h3.widget-title,
    .top_footer strong,
    .top_footer .widget-title {
        ' . (!empty($footer_heading_color) ? 'color:' . $footer_heading_color . ';' : '') . '
    }
    .top_footer{
        ' . (!empty($footer_text_color) ? 'color:' . $footer_text_color . ';' : '') . '
    }
    ';

    $copyright_text_color = gt3_option_compare('copyright_text_color', 'mb_footer_switch', 'yes');
    $custom_css           .= '.main_footer .copyright{
        ' . (!empty($copyright_text_color) ? 'color:' . $copyright_text_color . ';' : '') . '
    }';

    $custom_css .= '
    .gt3_header_builder__section--top .gt3_currency_switcher:hover ul,
    .gt3_header_builder__section--top .gt3_lang_switcher:hover ul{
        background-color: #fff;
    }
    .gt3_header_builder__section--middle .gt3_currency_switcher:hover ul,
    .gt3_header_builder__section--middle .gt3_lang_switcher:hover ul{
        background-color: #fff;
    }
    .gt3_header_builder__section--bottom .gt3_currency_switcher:hover ul,
    .gt3_header_builder__section--bottom .gt3_lang_switcher:hover ul{
        background-color: #fff;
    }
    ';

    // Sticky Single Product !
    if ( !empty($logo_tablet_width) && is_array($logo_tablet_width) && !empty($logo_tablet_width["width"]) && $logo_tablet_width["width"] !== '') {
        $custom_css .= '
            @media only screen and (max-width: 1200px){
                .header_side_container .logo_container {
                    max-width: ' . (int)$logo_tablet_width["width"] . 'px;
                }
            }
        ';
    }elseif( !empty($logo_height) && is_array($logo_height) && !empty($logo_height['height'])){
        $custom_css .= '
            @media only screen and (max-width: 1200px){
                .header_side_container .logo_container .tablet_logo{
                    height: ' . (int)$logo_height['height'] . 'px;
                }
            }
        ';
    }


    if ( !empty($logo_mobile_width) && is_array($logo_mobile_width)&& !empty($logo_mobile_width["width"]) && $logo_mobile_width["width"] !== '') {
        $custom_css .= '
            @media only screen and (max-width: 767px){
                .header_side_container .logo_container {
                    max-width: ' . (int)$logo_mobile_width["width"] . 'px;
                }
            }
        ';
    }

    // Woocommerce
    $custom_css .= '
    .woocommerce div.product form.cart .qty {
        ' . (!empty($content_font_family) ? 'font-family:' . $content_font_family . ';' : '') . '
    }
    .quantity-spinner.quantity-up:hover,
    .quantity-spinner.quantity-down:hover,
    .woocommerce .gt3-products-header .gridlist-toggle:hover,
    .elementor-widget-gt3-core-accordion .item_title .ui-accordion-header-icon:before,
    .elementor-element.elementor-widget-gt3-core-accordion .accordion_wrapper .item_title.ui-accordion-header-active.ui-state-active,
    .elementor-widget-gt3-core-accordion .accordion_wrapper .item_title:hover,
    .elementor-widget-gt3-core-accordion .accordion_wrapper .item_title,
    .woocommerce .woocommerce-message a.button:hover{
        ' . (!empty($theme_color) ? 'color:' . $theme_color . ';' : '') . '
    }
    .woocommerce #respond input#submit:hover,
    .woocommerce a.button:hover,
    .woocommerce button.button:hover,
    .woocommerce input.button:hover,
    .woocommerce #respond input#submit.alt:hover,
    .woocommerce a.button.alt:hover,
    .woocommerce button.button.alt:hover,
    .woocommerce input.button.alt:hover,
    .woocommerce #reviews a.button:hover,
    .woocommerce #reviews button.button:hover,
    .woocommerce #reviews input.button:hover,
    .woocommerce #respond input#submit.disabled:hover,
    .woocommerce #respond input#submit:disabled:hover,
    .woocommerce #respond input#submit:disabled[disabled]:hover,
    .woocommerce a.button.disabled:hover,
    .woocommerce a.button:disabled:hover,
    .woocommerce a.button:disabled[disabled]:hover,
    .woocommerce button.button.disabled:hover,
    .woocommerce button.button:disabled:hover,
    .woocommerce button.button:disabled[disabled]:hover,
    .woocommerce input.button.disabled:hover,
    .woocommerce input.button:disabled:hover,
    .woocommerce input.button:disabled[disabled]:hover,
    .woocommerce div.product form.cart .button:hover,
    .woocommerce .woocommerce-message a.button:hover,
    .woocommerce .cart .button:hover,
    .woocommerce .cart input.button:hover{
        ' . (!empty($theme_color) ? 'color:' . $theme_color . ';' : '') . '
        ' . (!empty($theme_color) ? 'border-color:' . $theme_color . ';' : '') . '
        background-color: #fff;
    }
    .woocommerce #respond input#submit.alt.disabled:hover,
    .woocommerce #respond input#submit.alt:disabled:hover,
    .woocommerce #respond input#submit.alt:disabled[disabled]:hover,
    .woocommerce a.button.alt.disabled:hover,
    .woocommerce a.button.alt:disabled:hover,
    .woocommerce a.button.alt:disabled[disabled]:hover,
    .woocommerce button.button.alt.disabled:hover,
    .woocommerce button.button.alt:disabled:hover,
    .woocommerce button.button.alt:disabled[disabled]:hover,
    .woocommerce input.button.alt.disabled:hover,
    .woocommerce input.button.alt:disabled:hover,
    .woocommerce input.button.alt:disabled[disabled]:hover,
    .elementor-widget-gt3-core-accordion .accordion_wrapper .item_title,
    .contact-form-2 input.wpcf7-submit{
        ' . (!empty($header_font_family) ? 'font-family:' . $header_font_family . ';' : '') . '
	}
	.quantity-spinner.quantity-up:hover,
	.quantity-spinner.quantity-down:hover,
	.woocommerce .gt3-products-header .gridlist-toggle:hover,
    .elementor-widget-gt3-core-accordion .item_title .ui-accordion-header-icon:before,
    .elementor-element.elementor-widget-gt3-core-accordion .accordion_wrapper .item_title.ui-accordion-header-active.ui-state-active,
    .yith-wcqv-wrapper .woocommerce div.product p.price,
    .single-product.woocommerce div.product p.price{
        ' . (!empty($theme_color) ? 'color:' . $theme_color . ';' : '') . '
	}
	.woocommerce #respond input#submit.alt.disabled,
	.woocommerce #respond input#submit.alt:disabled,
	.woocommerce #respond input#submit.alt:disabled[disabled],
	.woocommerce a.button.alt.disabled,
	.woocommerce a.button.alt:disabled,
	.woocommerce a.button.alt:disabled[disabled],
	.woocommerce button.button.alt.disabled,
	.woocommerce button.button.alt:disabled,
	.woocommerce button.button.alt:disabled[disabled],
	.woocommerce input.button.alt.disabled,
	.woocommerce input.button.alt:disabled,
	.woocommerce input.button.alt:disabled[disabled]{
        ' . (!empty($theme_color) ? 'color:' . $theme_color . ';' : '') . '
	}
	.woocommerce #respond input#submit,
	.woocommerce a.button,
	.woocommerce button.button,
	.woocommerce input.button,
	.woocommerce #respond input#submit.alt,
	.woocommerce a.button.alt,
	.woocommerce button.button.alt,
	.woocommerce input.button.alt,
	.woocommerce ul.products li.product .gt3_woocommerce_open_control_tag .button,
	.woocommerce #respond input#submit.alt.disabled:hover,
	.woocommerce #respond input#submit.alt:disabled:hover,
	.woocommerce #respond input#submit.alt:disabled[disabled]:hover,
	.woocommerce a.button.alt.disabled:hover,
	.woocommerce a.button.alt:disabled:hover,
	.woocommerce a.button.alt:disabled[disabled]:hover,
	.woocommerce button.button.alt.disabled:hover,
	.woocommerce button.button.alt:disabled:hover,
	.woocommerce button.button.alt:disabled[disabled]:hover,
	.woocommerce input.button.alt.disabled:hover,
	.woocommerce input.button.alt:disabled:hover,
	.woocommerce input.button.alt:disabled[disabled]:hover,
    .woocommerce .woocommerce-message a.button,
    .woocommerce .cart .button,
    .woocommerce .cart input.button,
    .mc_form_inside .mc_signup_submit input,
    .mc_form_inside .mc_signup_submit button{
        ' . (!empty($theme_color) ? 'background-color:' . $theme_color . ';' : '') . '
        ' . (!empty($theme_color) ? 'border-color:' . $theme_color . ';' : '') . '
    }
    .woocommerce table.shop_table .product-quantity .qty.allotted,
    .woocommerce div.product form.cart .qty.allotted,
    .image_size_popup .close,
    #yith-quick-view-content .product_meta,
    .single-product.woocommerce div.product .product_meta,
    .woocommerce div.product form.cart .variations td,
    .woocommerce div.product .woocommerce-tabs ul.tabs li,
    .woocommerce .widget_shopping_cart .total,
    .woocommerce.widget_shopping_cart .total,
    .woocommerce table.shop_table thead th,
    .woocommerce table.woocommerce-checkout-review-order-table tfoot td .woocommerce-Price-amount,
    .gt3_custom_tooltip {
        ' . (!empty($header_font_color) ? 'color:' . $header_font_color . ';' : '') . '
    }
    .gt3_custom_tooltip:before {
        ' . (!empty($header_font_color) ? 'background:' . $header_font_color . ';' : '') . '
    }
    .gt3_custom_tooltip:after {
        ' . (!empty($header_font_color) ? 'border-color:' . $header_font_color . ' transparent transparent transparent;' : '') . '
    }
    #yith-quick-view-content .product_meta a,
    #yith-quick-view-content .product_meta .sku,
    .single-product.woocommerce div.product .product_meta a,
    .single-product.woocommerce div.product .product_meta .sku,
    .select2-container--default .select2-selection--single .select2-selection__rendered,
    .woocommerce ul.products li.product .woocommerce-loop-product__title,
    .gt3_404_search .search_form label,
    .search_result_form .search_form label,
    .woocommerce .star-rating::before,
    .woocommerce #reviews p.stars span a,
    .woocommerce p.stars span a:hover~a::before,
    .woocommerce p.stars.selected span a.active~a::before,
    .select2-container--default .select2-results__option--highlighted[aria-selected],
    .select2-container--default .select2-results__option--highlighted[data-selected],
    .cart_list.product_list_widget a.remove,
    .woocommerce .gt3-pagination_nav nav.woocommerce-pagination ul li .gt3_pagination_delimiter,
    .widget_categories ul li .post_count {
        ' . (!empty($content_color) ? 'color:' . $content_color . ';' : '') . '
    }   
    .woocommerce #reviews a.button:hover,
    .woocommerce #reviews button.button:hover,
    .woocommerce #reviews input.button:hover,
    .woocommerce div.product > .woocommerce-tabs ul.tabs li.active a,
    .woocommerce ul.products li.product a:hover .woocommerce-loop-product__title,
    .widget .calendar_wrap table td#today,
	.woocommerce .woocommerce-widget-layered-nav-list .woocommerce-widget-layered-nav-list__item span.count,
    .woocommerce ul.products li.product .woocommerce-loop-product__title:hover{
        ' . (!empty($theme_color) ? 'color:' . $theme_color . ';' : '') . '
	}

    .woocommerce.single-product #respond #commentform textarea:focus,
    .woocommerce div.product > .woocommerce-tabs ul.tabs li.active a,
     .woocommerce div.product .woocommerce-tabs ul.tabs li a:hover{
        ' . (!empty($theme_color) ? 'border-bottom-color:' . $theme_color . ';' : '') . '
    }
    .woocommerce .gridlist-toggle,
    .woocommerce .gt3-products-header .gt3-gridlist-toggle{
        ' . (!empty($bg_body) ? 'background-color:' . $bg_body . ';' : '') . '
    }
    ';

    $label_color_sale = gt3_option('label_color_sale');
    $label_color_hot  = gt3_option('label_color_hot');
    $label_color_new  = gt3_option('label_color_new');
    if (is_array($label_color_sale) && isset($label_color_sale['rgba'])) {
        $custom_css .= '
        .woocommerce ul.products li.product .onsale,
        #yith-quick-view-content .onsale,
        .woocommerce span.onsale{
            background-color: '.esc_attr($label_color_sale['rgba']).';
        }';
    }

    if (is_array($label_color_hot) && isset($label_color_hot['rgba'])) {
        $custom_css .= '
        .woocommerce ul.products li.product .onsale.hot-product,
        #yith-quick-view-content .onsale.hot-product,
        .woocommerce span.onsale.hot-product{
            background-color: '.esc_attr($label_color_hot['rgba']).';
        }';
    }

    if (is_array($label_color_new) && isset($label_color_new['rgba'])) {
        $custom_css .= '
        .woocommerce ul.products li.product .onsale.new-product,
        #yith-quick-view-content .onsale.new-product,
        .woocommerce span.onsale.new-product{
            background-color: '.esc_attr($label_color_new['rgba']).';
        }';
    }
    // Woocommerce end

    // Elementor start
    $custom_css .= '
    .price_item .item_cost_wrapper h3,
    .price_item-cost,
    .elementor-widget-slider-gt3 .slider_type_1 .controls .slick-position span:not(.all_slides),
    .elementor-widget-slider-gt3 .slider_type_3 .controls .slick-position span:not(.all_slides),
    .elementor-widget-slider-gt3 .controls .slick_control_text span:not(.all_slides),
    .ribbon_arrow .control_text span:not(.all_slides),
    .elementor-widget-tabs .elementor-tab-desktop-title,
    .woocommerce.widget_product_categories ul li:hover > a,
    .product-categories > li.cat-parent:hover .gt3-button-cat-open,
    .woocommerce .woocommerce-widget-layered-nav-list .woocommerce-widget-layered-nav-list__item:hover > a,
    .woocommerce .woocommerce-widget-layered-nav-list .woocommerce-widget-layered-nav-list__item:hover span,
    .cart_list.product_list_widget a.remove:hover,
    .woocommerce ul.products li.product a:hover,
    .woocommerce ul.products li.product .gt3_woocommerce_open_control_tag .button:hover,
    .woocommerce table.shop_table td.product-remove a:hover:before,
    .woocommerce table.shop_table td.product-name a:hover{
        ' . (!empty($theme_color) ? 'color:' . $theme_color . ';' : '') . '
    }
    .price_item .label_text span,
    a.bordered:hover,
    .woocommerce ul.products li.product .gt3_woocommerce_open_control_tag_bottom div a,
    .woocommerce #payment .woocommerce-page #place_order,
    .prev_next_links_fullwidht .link_item,
    span.ui-slider-handle.ui-state-default.ui-corner-all.ui-state-hover,
    body table.compare-list .add-to-cart td a:hover,
    .woocommerce .widget_price_filter .price_slider_amount .button:hover,
    .woocommerce-account .woocommerce-MyAccount-content .woocommerce-Message.woocommerce-Message--info.woocommerce-info .button,
    .woo_mini-count > span:not(:empty),
    button,
    .woocommerce .widget_price_filter .ui-slider .ui-slider-range,
    .infinite-scroll-request > div{
        ' . (!empty($theme_color) ? 'background-color:' . $theme_color . ';' : '') . '
	}
    ul.pagerblock li a,
    ul.pagerblock li span,
    .gt3_comments_pagination .page-numbers,
    .page-link .page-number,
    .woocommerce nav.woocommerce-pagination ul li a {
        ' . (!empty($content_color) ? 'color: rgba('.gt3_HexToRGB($content_color).', 0.5);' : '') . '
    }
    ul.pagerblock li a:hover,
    .woocommerce nav.woocommerce-pagination ul li a:hover{
        ' . (!empty($content_color) ? 'color:' . $content_color . ';' : '') . '
    }
    .gt3_comments_pagination .page-numbers.current,
    .page-link > span.page-number,
    button:hover{
        ' . (!empty($content_color) ? 'background-color:' . $content_color . ';' : '') . '
    }
    button:hover{
        ' . (!empty($content_color) ? 'border-color:' . $content_color . ';' : '') . '
    }

	a.bordered:hover,	
	.elementor-widget-tabs.elementor-tabs-view-horizontal .elementor-tab-desktop-title.elementor-active:after,
    .woocommerce .widget_price_filter .ui-slider .ui-slider-handle,
    .woocommerce .widget_price_filter .ui-slider .ui-slider-handle:before,
    .mc_form_inside.has_only_email .mc_signup_submit input:hover,
    .format-standard .blog_content .post-password-form input[type="submit"],
    .format-standard .blog_content .post-password-form input[type="submit"]:hover,
    .mc_form_inside.has_only_email .mc_signup_submit input,
    .post-password-form input[type="submit"],
    .post-password-form input[type="submit"]:hover{
        ' . (!empty($theme_color) ? 'border-color:' . $theme_color . ';' : '') . '
	}
	.elementor-progress-wrapper,
	.price_item-cost,
	.countdown-section{
        ' . (!empty($header_font_family) ? 'font-family:' . $header_font_family . ';' : '') . '
	}
    
    .price_item-cost span,
    .elementor-widget-slider-gt3 .controls .slick_control_text span.all_slides,
    .ribbon_arrow .control_text span.all_slides,
    .gt3_header_builder_cart_component ul.cart_list li a {
        ' . (!empty($content_color) ? 'color:' . $content_color . ';' : '') . '
    }
    .fs_gallery_wrapper .status .first,
    .fs_gallery_wrapper .status .divider,
    .countdown-section,
    .page_nav_ancor a,
    .gt3_widget span.woocommerce-Price-amount.amount,
    .woocommerce table.shop_table td.product-remove a,
    .woocommerce table.shop_table td.product-name a,
    .sidebar-container .widget.widget_posts .recent_posts .listing_meta span,
    .gt3_header_builder_cart_component:hover .gt3_header_builder_cart_component__cart{
        ' . (!empty($header_font_color) ? 'color:' . $header_font_color . ';' : '') . '
	}

    /* PixProof */
    .mfp-container button.mfp-arrow-right:hover {
        ' . (!empty($theme_color) ? 'border-left-color:' . $theme_color . ';' : '') . '
    }
    .mfp-container button.mfp-arrow-left:hover {
        ' . (!empty($theme_color) ? 'border-right-color:' . $theme_color . ';' : '') . '
    }
    /* End PixProof */

    /* Map */
    .map_info_marker {
        ' . (!empty($map_marker_info_bgr) ? 'background:' . $map_marker_info_bgr . ';' : '') . '
    }
    .map_info_marker:after {
        ' . (!empty($map_marker_info_bgr) ? 'border-color: ' . $map_marker_info_bgr . ' transparent transparent transparent;' : '') . '
    }
    .marker_info_street_number,
    .marker_info_street,
    .footer_back2top .gt3_svg_line_icon,
    .elementor-widget-gt3-core-testimonials .module_testimonial .slick-arrow:hover{
        ' . (!empty($theme_color) ? 'color:' . $theme_color . ';' : '') . '
    }
    .marker_info_desc {
        ' . (!empty($map_marker_info_clr) ? 'color:' . $map_marker_info_clr . ';' : '') . '
    }
    .map_info_marker_content {
        ' . (!empty($map_marker_font_family) ? 'font-family:' . $map_marker_font_family . ';' : '') . '
        ' . (!empty($map_marker_font_weight) ? 'font-weight:' . $map_marker_font_weight . ';' : '') . '
    }
    .marker_info_divider:after {
        ' . (!empty($map_marker_info_clr) ? 'background:' . $map_marker_info_clr . ';' : '') . '
    }
    ';
    // Elementor end


    /* Elementor Buttons */
    $custom_css .= '
    .elementor-widget-gt3-core-button .gt3_module_button_elementor:not(.hover_type2) a,
    .elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type2 .gt3_module_button__container span.gt3_module_button__cover.front {
        ' . (!empty($theme_color) ? 'border-color:' . $theme_color . ';' : '') . '
    }
    .elementor-element.elementor-widget-gt3-core-button .gt3_module_button_elementor a:not(.hover_type2):not(.hover_type5){
        ' . (!empty($theme_color) ? 'border-color:' . $theme_color . ';' : '') . '
        ' . (!empty($theme_color) ? 'background:' . $theme_color . ';' : '') . '
    }
    .elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type4 .gt3_module_button__cover:before {
        ' . (!empty($theme_color) ? 'background:' . $theme_color . ';' : '') . '
        ' . (!empty($theme_color) ? 'border: 0px solid ' . $theme_color . ';' : '') . '
    }
    .elementor-widget-gt3-core-button .gt3_module_button_elementor:not(.hover_type2):not(.hover_type4):not(.hover_type5) a,
    .elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type2 .gt3_module_button__container span.gt3_module_button__cover.front,
    .elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type4 .gt3_module_button__cover:before,
    .elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type5 .gt3_module_button__container .gt3_module_button__cover.front:before,
    .elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type5 .gt3_module_button__container .gt3_module_button__cover.front:after,
    .elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type6 {
        ' . (!empty($theme_color) ? 'background:' . $theme_color . ';' : '') . '
    }
    .elementor-widget-gt3-core-button .gt3_module_button_elementor.button_icon_icon:not(.hover_type2) a:hover .elementor_gt3_btn_icon,
    .elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type2 .gt3_module_button__container span.gt3_module_button__cover.back .elementor_btn_icon_container .elementor_gt3_btn_icon,
    .elementor-widget-gt3-core-button a:hover .icon_svg_btn,
    .elementor-element.elementor-widget-gt3-core-button .gt3_module_button_elementor a:hover,
    .elementor-widget-gt3-core-button a:not(.hover_type2):hover .elementor_gt3_btn_text,
    .elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type2 .gt3_module_button__container .gt3_module_button__cover.back .elementor_gt3_btn_text,
    .elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type4:hover .gt3_module_button__container .gt3_module_button__cover.front .elementor_gt3_btn_text {
        ' . (!empty($theme_color) ? 'color:' . $theme_color . ';' : '') . '
    }
    .elementor-widget-gt3-core-button .gt3_module_button_elementor:not(.hover_type2) a:hover,
    .elementor-widget-gt3-core-button .gt3_module_button_elementor .hover_type2 .gt3_module_button__container span.gt3_module_button__cover.back {
        ' . (!empty($theme_color) ? 'border-color:' . $theme_color . ';' : '') . '
    }
    ';
    /* Elementor Buttons end */


    function gt3_get_upper_responsive_value($options,$inherit_options){
        $options = $options === '' ? $inherit_options : $options;
        return $options;
    }

    // GT3 Header Builder styles
    foreach ($sections as $section) {

        if (strpos($section,'tablet')) {
            $responsive_res = explode('__',$section);
            if (is_array($responsive_res) && !empty($responsive_res[0])) {

                if (${'side_' . $section . '_custom'} == '1') {
                    ${'side_' . $section . '_background'} = gt3_get_upper_responsive_value(${'side_' . $section . '_background'},${'side_' . $responsive_res[0] . '_background'});
                    ${'side_' . $section . '_background2'} = gt3_get_upper_responsive_value(${'side_' . $section . '_background2'},${'side_' . $responsive_res[0] . '_background2'});
                    ${'side_' . $section . '_color'} = gt3_get_upper_responsive_value(${'side_' . $section . '_color'},${'side_' . $responsive_res[0] . '_color'});
                    ${'side_' . $section . '_color_hover'} = gt3_get_upper_responsive_value(${'side_' . $section . '_color_hover'},${'side_' . $responsive_res[0] . '_color_hover'});
                    ${'side_' . $section . '_height'} = gt3_get_upper_responsive_value(${'side_' . $section . '_height'},${'side_' . $responsive_res[0] . '_height'});
                    ${'side_' . $section . '_spacing'}['padding-left'] = gt3_get_upper_responsive_value(${'side_' . $section . '_spacing'}['padding-left'],${'side_' . $responsive_res[0] . '_spacing'}['padding-left']);
                    ${'side_' . $section . '_spacing'}['padding-right'] = gt3_get_upper_responsive_value(${'side_' . $section . '_spacing'}['padding-right'],${'side_' . $responsive_res[0] . '_spacing'}['padding-right']);
                    ${'side_' . $section . '_border_radius'} = gt3_get_upper_responsive_value(${'side_' . $section . '_border_radius'},${'side_' . $responsive_res[0] . '_border_radius'});
                    ${'side_' . $section . '_border'} = gt3_get_upper_responsive_value(${'side_' . $section . '_border'},${'side_' . $responsive_res[0] . '_border'});
                    ${'side_' . $section . '_border_color'}['rgba'] = gt3_get_upper_responsive_value(${'side_' . $section . '_border_color'}['rgba'],${'side_' . $responsive_res[0] . '_border_color'}['rgba']);
                }else{
                    ${'side_' . $section . '_background'} = ${'side_' . $responsive_res[0] . '_background'};
                    ${'side_' . $section . '_background2'} = ${'side_' . $responsive_res[0] . '_background2'};
                    ${'side_' . $section . '_color'} = ${'side_' . $responsive_res[0] . '_color'};
                    ${'side_' . $section . '_color_hover'} = ${'side_' . $responsive_res[0] . '_color_hover'};
                    ${'side_' . $section . '_height'} = ${'side_' . $responsive_res[0] . '_height'};
                    ${'side_' . $section . '_spacing'}['padding-left'] = ${'side_' . $responsive_res[0] . '_spacing'}['padding-left'];
                    ${'side_' . $section . '_spacing'}['padding-right'] = ${'side_' . $responsive_res[0] . '_spacing'}['padding-right'];
                    ${'side_' . $section . '_border_radius'} = ${'side_' . $responsive_res[0] . '_border_radius'};
                    ${'side_' . $section . '_border'} = ${'side_' . $responsive_res[0] . '_border'};
                    ${'side_' . $section . '_border_color'}['rgba'] = ${'side_' . $responsive_res[0] . '_border_color'}['rgba'];
                }

            }
        }

        if (strpos($section,'mobile')) {
            $responsive_res = explode('__',$section);
            if (is_array($responsive_res) && !empty($responsive_res[0])) {

                if (${'side_' . $section . '_custom'} == '1') {
                    ${'side_' . $section . '_background'} = gt3_get_upper_responsive_value(${'side_' . $section . '_background'},${'side_' . $responsive_res[0] . '__tablet_background'});
                    ${'side_' . $section . '_background2'} = gt3_get_upper_responsive_value(${'side_' . $section . '_background2'},${'side_' . $responsive_res[0] . '__tablet_background2'});
                    ${'side_' . $section . '_color'} = gt3_get_upper_responsive_value(${'side_' . $section . '_color'},${'side_' . $responsive_res[0] . '__tablet_color'});
                    ${'side_' . $section . '_color_hover'} = gt3_get_upper_responsive_value(${'side_' . $section . '_color_hover'},${'side_' . $responsive_res[0] . '__tablet_color_hover'});
                    ${'side_' . $section . '_height'} = gt3_get_upper_responsive_value(${'side_' . $section . '_height'},${'side_' . $responsive_res[0] . '__tablet_height'});
                    ${'side_' . $section . '_spacing'}['padding-left'] = gt3_get_upper_responsive_value(${'side_' . $section . '_spacing'}['padding-left'],${'side_' . $responsive_res[0] . '__tablet_spacing'}['padding-left']);
                    ${'side_' . $section . '_spacing'}['padding-right'] = gt3_get_upper_responsive_value(${'side_' . $section . '_spacing'}['padding-right'],${'side_' . $responsive_res[0] . '__tablet_spacing'}['padding-right']);
                    ${'side_' . $section . '_border_radius'} = gt3_get_upper_responsive_value(${'side_' . $section . '_border_radius'},${'side_' . $responsive_res[0] . '__tablet_border_radius'});
                    ${'side_' . $section . '_border'} = gt3_get_upper_responsive_value(${'side_' . $section . '_border'},${'side_' . $responsive_res[0] . '__tablet_border'});
                    ${'side_' . $section . '_border_color'}['rgba'] = gt3_get_upper_responsive_value(${'side_' . $section . '_border_color'}['rgba'],${'side_' . $responsive_res[0] . '__tablet_border_color'}['rgba']);
                }else{
                    ${'side_' . $section . '_background'} = ${'side_' . $responsive_res[0] . '__tablet_background'};
                    ${'side_' . $section . '_background2'} = ${'side_' . $responsive_res[0] . '__tablet_background2'};
                    ${'side_' . $section . '_color'} = ${'side_' . $responsive_res[0] . '__tablet_color'};
                    ${'side_' . $section . '_color_hover'} = ${'side_' . $responsive_res[0] . '__tablet_color_hover'};
                    ${'side_' . $section . '_height'} = ${'side_' . $responsive_res[0] . '__tablet_height'};
                    ${'side_' . $section . '_spacing'}['padding-left'] = ${'side_' . $responsive_res[0] . '__tablet_spacing'}['padding-left'];
                    ${'side_' . $section . '_spacing'}['padding-right'] = ${'side_' . $responsive_res[0] . '__tablet_spacing'}['padding-right'];
                    ${'side_' . $section . '_border_radius'} = ${'side_' . $responsive_res[0] . '__tablet_border_radius'};
                    ${'side_' . $section . '_border'} = ${'side_' . $responsive_res[0] . '__tablet_border'};
                    ${'side_' . $section . '_border_color'}['rgba'] = ${'side_' . $responsive_res[0] . '__tablet_border_color'}['rgba'];
                }

            }
        }



        $custom_css .= '
        .gt3_header_builder__section--'.$section.'{
        ' . (!empty(${'side_' . $section . '_background'}) ? 'background-color:' . esc_attr(${'side_' . $section . '_background'}) . ';' : '') . '
        ' . (!empty(${'side_' . $section . '_color'}) ? 'color:' . esc_attr(${'side_' . $section . '_color'}) . ';' : '') . '
        }
        .gt3_header_builder__section--'.$section.' .gt3_header_builder__section-container{
        ' . (!empty(${'side_' . $section . '_height'}) ? 'height:' . (int)${'side_' . $section . '_height'} . 'px;' : '') . '
        ' . (!empty(${'side_' . $section . '_background2'}) ? 'background-color:' . esc_attr(${'side_' . $section . '_background2'}) . ';' : '') . '
        }
        .gt3_header_builder__section--'.$section.' ul.menu{
        ' . (!empty(${'side_' . $section . '_height'}) ? 'line-height:' . (int)${'side_' . $section . '_height'} . 'px;' : '') . '
        }
        .gt3_header_builder__section--'.$section.' a:hover,
        .gt3_header_builder__section--'.$section.' .menu-item.active_item > a,
        .gt3_header_builder__section--'.$section.' .current-menu-item a,
        .gt3_header_builder__section--'.$section.' .current-menu-ancestor > a,
        .gt3_header_builder__section--'.$section.' .gt3_header_builder_login_component:hover .wpd_login__user_name,
        .gt3_header_builder__section--'.$section.' .gt3_header_builder_wpml_component .wpml-ls-legacy-dropdown a:hover, 
        .gt3_header_builder__section--'.$section.' .gt3_header_builder_wpml_component .wpml-ls-legacy-dropdown a:focus, 
        .gt3_header_builder__section--'.$section.' .gt3_header_builder_wpml_component .wpml-ls-legacy-dropdown .wpml-ls-current-language:hover > a, 
        .gt3_header_builder__section--'.$section.' .gt3_header_builder_wpml_component .wpml-ls-legacy-dropdown-click a:hover, 
        .gt3_header_builder__section--'.$section.' .gt3_header_builder_wpml_component .wpml-ls-legacy-dropdown-click a:focus, 
        .gt3_header_builder__section--'.$section.' .gt3_header_builder_wpml_component .wpml-ls-legacy-dropdown-click .wpml-ls-current-language:hover > a {
        ' . (!empty(${'side_' . $section . '_color_hover'}) ? 'color:' . esc_attr(${'side_' . $section . '_color_hover'}) . ';' : '') . '
        }
        ';

        if (!empty(${'side_' . $section . '_spacing'}) && is_array(${'side_' . $section . '_spacing'})) {
            if (!empty(${'side_' . $section . '_spacing'}['padding-left'])) {
                $custom_css .= '.gt3_header_builder__section--'.$section.' .gt3_header_builder__section-container{
                    padding-left:' . (int)${'side_' . $section . '_spacing'}['padding-left'] . 'px;
                }';
            }
            if (!empty(${'side_' . $section . '_spacing'}['padding-right'])) {
                $custom_css .= '.gt3_header_builder__section--'.$section.' .gt3_header_builder__section-container{
                    padding-right:' . (int)${'side_' . $section . '_spacing'}['padding-right'] . 'px;
                }';
            }
        }

        if (${'side_' . $section . '_border_radius'}) {
            $custom_css .= '.gt3_header_builder__section--'.$section.' .gt3_header_builder__section-container{
                    border-radius: 8px;
                }';
        }

        if (${'side_' . $section . '_border'}) {
            if (!empty(${'side_' . $section . '_border_color'}['rgba'])) {
                $custom_css .= '
                .gt3_header_builder__section--' . $section . '{
                    border-bottom: 1px solid ' . esc_attr(${'side_' . $section . '_border_color'}['rgba']) . ';
                }';
            }
        }
    }

    if ((bool)$header_sticky) {
        foreach ($desktop_sides as $sticky_side) {
            if ((bool)${'side_' . $sticky_side . '_sticky'}) {
                if (is_array(${'side_' . $sticky_side . '_background_sticky'}) && !empty(${'side_' . $sticky_side . '_background_sticky'}['rgba'])) {
                    ${'side_' . $sticky_side . '_background_sticky'} = ${'side_' . $sticky_side . '_background_sticky'}['rgba'];
                }
                if (is_array(${'side_' . $sticky_side . '_height_sticky'}) && ${'side_' . $sticky_side . '_height_sticky'}['height']) {
                    ${'side_' . $sticky_side . '_height_sticky'} = ${'side_' . $sticky_side . '_height_sticky'}['height'];
                }

                if (!empty(${'side_' . $sticky_side . '_spacing_sticky'}) && is_array(${'side_' . $sticky_side . '_spacing_sticky'})) {
                    if (!empty(${'side_' . $sticky_side . '_spacing_sticky'}['padding-left'])) {
                        $custom_css .= '.sticky_header .gt3_header_builder__section--'.$sticky_side.' .gt3_header_builder__section-container{
                            padding-left:' . (int)${'side_' . $sticky_side . '_spacing_sticky'}['padding-left'] . 'px;
                        }';
                    }
                    if (!empty(${'side_' . $sticky_side . '_spacing_sticky'}['padding-right'])) {
                        $custom_css .= '.sticky_header .gt3_header_builder__section--'.$sticky_side.' .gt3_header_builder__section-container{
                            padding-right:' . (int)${'side_' . $sticky_side . '_spacing_sticky'}['padding-right'] . 'px;
                        }';
                    }
                }
                $custom_css .= '
                .sticky_header .gt3_header_builder__section--' . $sticky_side . ',
                .sticky_header .gt3_header_builder__section--' . $sticky_side . '__tablet,
                .sticky_header .gt3_header_builder__section--' . $sticky_side . '__mobile{
                    ' . (!empty(${'side_' . $sticky_side . '_background_sticky'}) ? ' background-color:' . esc_attr(${'side_' . $sticky_side . '_background_sticky'}) . ';' : '') . '
                    ' . (!empty(${'side_' . $sticky_side . '_color_sticky'}) ? 'color:' . esc_attr(${'side_' . $sticky_side . '_color_sticky'}) . ';' : '') . '
                }
                .sticky_header .gt3_header_builder__section--'.$sticky_side.' a:hover,
                .sticky_header .gt3_header_builder__section--'.$sticky_side.' ul.menu > .menu-item.active_item > a,
                .sticky_header .gt3_header_builder__section--'.$sticky_side.' ul.menu > .current-menu-item > a,
                .sticky_header .gt3_header_builder__section--'.$sticky_side.' ul.menu > .current-menu-ancestor > a,
                .sticky_header .gt3_header_builder__section--'.$sticky_side.' .gt3_header_builder_login_component:hover .wpd_login__user_name,
                .sticky_header .gt3_header_builder__section--'.$sticky_side.' .gt3_header_builder_wpml_component .wpml-ls-legacy-dropdown a:hover, 
                .sticky_header .gt3_header_builder__section--'.$sticky_side.' .gt3_header_builder_wpml_component .wpml-ls-legacy-dropdown a:focus, 
                .sticky_header .gt3_header_builder__section--'.$sticky_side.' .gt3_header_builder_wpml_component .wpml-ls-legacy-dropdown .wpml-ls-current-language:hover > a, 
                .sticky_header .gt3_header_builder__section--'.$sticky_side.' .gt3_header_builder_wpml_component .wpml-ls-legacy-dropdown-click a:hover, 
                .sticky_header .gt3_header_builder__section--'.$sticky_side.' .gt3_header_builder_wpml_component .wpml-ls-legacy-dropdown-click a:focus, 
                .sticky_header .gt3_header_builder__section--'.$sticky_side.' .gt3_header_builder_wpml_component .wpml-ls-legacy-dropdown-click .wpml-ls-current-language:hover > a{
                    ' . (!empty(${'side_' . $sticky_side . '_color_hover_sticky'}) ? 'color:' . esc_attr(${'side_' . $sticky_side . '_color_hover_sticky'}) . ';' : '') . '
                }
                .sticky_header .gt3_header_builder__section--' . $sticky_side . ' .gt3_header_builder__section-container{
                    ' . (!empty(${'side_' . $sticky_side . '_height_sticky'}) ? 'height:' . (int)${'side_' . $sticky_side . '_height_sticky'} . 'px;' : '') . '
                }
                .sticky_header .gt3_header_builder__section--' . $sticky_side . ' ul.menu{
                    ' . (!empty(${'side_' . $sticky_side . '_height_sticky'}) ? 'line-height:' . (int)${'side_' . $sticky_side . '_height_sticky'} . 'px;' : '') . '
                }';
            }
        }
        $height_sticky = 30;
        if (!empty($side_top_sticky) && !empty($side_top_height_sticky) && (bool)$side_top_sticky) {
            $height_sticky = $height_sticky + (int)$side_top_height_sticky;
        }

        if (!empty($side_middle_sticky) && !empty($side_middle_height_sticky) && (bool)$side_middle_sticky) {
            $height_sticky = $height_sticky + (int)$side_middle_height_sticky;
        }

        if (!empty($side_bottom_sticky) && !empty($side_bottom_height_sticky) && (bool)$side_bottom_sticky) {
            $height_sticky = $height_sticky + (int)$side_bottom_height_sticky;
        }
        if (is_admin_bar_showing()) {
            $height_sticky = $height_sticky + 32;
        }
        $custom_css .= '
        div.gt3-single-product-sticky .gt3_thumb_grid,
        div.gt3-single-product-sticky .woocommerce-product-gallery:nth-child(1),
        div.gt3-single-product-sticky .gt3-single-content-wrapper{
        ' . (!empty($height_sticky) ? 'margin-top:' . (int)$height_sticky . 'px;' : '') . '
        }
        div.gt3-single-product-sticky{
        ' . (!empty($height_sticky) ? 'margin-top: -' . (int)$height_sticky . 'px;' : '') . '
        }';
    }
    // GT3 Header Builder end


    $custom_css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '   ', '    '), '', $custom_css);
    if (wp_style_is('gt3-responsive')) {
        wp_add_inline_style('gt3-responsive', $custom_css);
    } else {
        wp_add_inline_style('gt3-theme', $custom_css);
    }
}

add_action('wp_enqueue_scripts', 'gt3_custom_styles', 30);
