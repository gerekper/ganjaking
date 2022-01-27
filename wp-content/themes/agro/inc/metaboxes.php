<?php
if (! function_exists('rwmb_meta') || ! is_admin()) {
    return false;
}

add_filter('rwmb_meta_boxes', 'agro_register_meta_boxes');
function agro_register_meta_boxes($meta_boxes)
{
    $meta_boxes = array();

    /* ----------------------------------------------------- */
    // PAGE BACKGROUND-HEADER-FOOTER COLOR
    /* ----------------------------------------------------- */

    $meta_boxes[] = array(
        'title' => esc_html__('Header - Footer', 'agro'),
        'id' => 'pageheadersettings',
        'pages' => array( 'page' ),
        'context' => 'normal',
        'tab_style' => 'box',
        'priority' => 'high',
        'tabs' => array(
            'tab1' => 'Header Menu',
            'tab2' => 'Main Footer',
            'tab3' => 'Contact Form',
            'tab4' => 'Google Map'
        ),
        'fields' => array(
            array(
                'id' => 'agro_page_header_onoff',
                'name' => esc_html__('Page Header Display', 'agro'),
                'type' => 'switch',
                'style' => 'rounded',
                'on_label' => esc_html__('Show', 'agro'),
                'off_label' => esc_html__('Hide', 'agro'),
                'std' => 1,
                'tab' => 'tab1'
            ),
            array(
                'id' => 'agro_page_header_style',
                'name' => esc_html__('Page Header Style', 'agro'),
                'type' => 'select',
                'options' => array(
                    '1' => esc_html__('Style 1', 'agro'),
                    '2' => esc_html__('Style 2', 'agro'),
                    '3' => esc_html__('Style 3', 'agro'),
                ),
                'std' => '1',
                'tab' => 'tab1'
            ),
            array(
                'id' => 'agro_sticky_header_onoff',
                'name' => esc_html__('Page Sticky Header Display', 'agro'),
                'desc' => esc_html__('The sticky header is only compatible with the header style 2', 'agro'),
                'type' => 'switch',
                'style' => 'rounded',
                'on_label' => esc_html__('Show', 'agro'),
                'off_label' => esc_html__('Hide', 'agro'),
                'std' => 1,
                'tab' => 'tab1'
            ),
            array(
                'id' => 'agro_page_nav_btn_onoff',
                'name' => esc_html__('Page Header Right Button Display', 'agro'),
                'type' => 'switch',
                'style' => 'rounded',
                'on_label' => esc_html__('Show', 'agro'),
                'off_label' => esc_html__('Hide', 'agro'),
                'std' => 1,
                'tab' => 'tab1'
            ),
            array(
                'id' => 'agro_page_topbar_onoff',
                'name' => esc_html__('Page Header Top Bar Display', 'agro'),
                'desc' => esc_html__('The header topbar is only compatible with the header style 2', 'agro'),
                'type' => 'switch',
                'style' => 'rounded',
                'on_label' => esc_html__('Show', 'agro'),
                'off_label' => esc_html__('Hide', 'agro'),
                'std' => 1,
                'tab' => 'tab1'
            ),

            // TAB2 FOOTER
            array(
                'id' => 'agro_page_footer_onoff',
                'name' => esc_html__('Page Footer Display', 'agro'),
                'type' => 'switch',
                'style' => 'rounded',
                'on_label' => esc_html__('Show', 'agro'),
                'off_label' => esc_html__('Hide', 'agro'),
                'std' => 1,
                'tab' => 'tab2'
            ),
            array(
                'id' => 'agro_page_footer_copyright_onoff',
                'name' => esc_html__('Page Footer Copyright Display', 'agro'),
                'type' => 'switch',
                'style' => 'rounded',
                'on_label' => esc_html__('Show', 'agro'),
                'off_label' => esc_html__('Hide', 'agro'),
                'std' => 1,
                'tab' => 'tab2'
            ),
            // TAB3 CONTACT FORM
            array(
                'id' => 'agro_page_footer_form_onoff',
                'name' => esc_html__('Page Contact Form Area Display', 'agro'),
                'type' => 'switch',
                'style' => 'rounded',
                'on_label' => esc_html__('Show', 'agro'),
                'off_label' => esc_html__('Hide', 'agro'),
                'std' => 0,
                'tab' => 'tab3'
            ),
            // TAB4 GOOGLE MAP
            array(
                'id' => 'agro_page_footer_map_onoff',
                'name' => esc_html__('Page Google Map Display', 'agro'),
                'type' => 'switch',
                'style' => 'rounded',
                'on_label' => esc_html__('Show', 'agro'),
                'off_label' => esc_html__('Hide', 'agro'),
                'std' => 0,
                'tab' => 'tab4'
            )
        )
    );

    /* ----------------------------------------------------- */
    // PAGE HERO OPTIONS
    /* ----------------------------------------------------- */

    $meta_boxes[] = array(
        'title' => esc_html__('Page Hero Options', 'agro'),
        'id' => 'pageherosettings',
        'pages' => array( 'page' ),
        'context' => 'normal',
        'priority' => 'high',
        'tab_style' => 'box',
        'show' => array( 'template' => 'default-page.php' ),
        'tabs' => array(
            'tab0' => 'Hero General',
            'tab1' => 'Title',
            'tab2' => 'Slogan',
            'tab3' => 'Description',
        ),
        'fields' => array(
            // tab0
            array(
                'id' => 'agro_page_hero_tab',
                'name' => esc_html__('Page Hero Options', 'agro'),
                'type' => 'heading',
                'tab' => 'tab0',
            ),
            array(
                'id' => 'agro_page_hero_onoff',
                'name' => esc_html__('Page Hero Display', 'agro'),
                'type' => 'switch',
                'style' => 'rounded',
                'on_label' => esc_html__('Show', 'agro'),
                'off_label' => esc_html__('Hide', 'agro'),
                'std' => 1,
                'tab' => 'tab0',
            ),
            array(
                'type' => 'divider',
                'tab' => 'tab0',
            ),
            // hero align
            array(
                'id' => 'agro_page_hero_align',
                'name' => esc_html__('Hero Text Alignment', 'agro'),
                'desc' => esc_html__('Select page hero text align.', 'agro'),
                'type' => 'select',
                'options' => array(
                    'text-left' => esc_html__('Left', 'agro'),
                    'text-center' => esc_html__('Center', 'agro'),
                    'text-right' => esc_html__('Right', 'agro'),
                ),
                'std' => 'text-center',
                'tab' => 'tab0',
            ),
            array(
                'type' => 'divider',
                'tab' => 'tab0',
            ),
            array(
                'id' => 'agro_page_hero_bg',
                'name' => esc_html__('Page Hero Background Image', 'agro'),
                'type' => 'background',
                'tab' => 'tab0',
            ),
            array(
                'id' => 'agro_page_hero_overlay',
                'name' => esc_html__('Background Image Overlay Color', 'agro'),
                'type' => 'color',
                'alpha_channel' => true,
                'tab' => 'tab0',
            ),
            array(
                'type' => 'divider',
                'tab' => 'tab0',
            ),
            array(
                'id' => 'agro_page_hero_pt',
                'name' => esc_html__('Hero Top Spacing (px)', 'agro'),
                'desc' => esc_html__('Use simple number without px or unit.Default : 150)', 'agro'),
                'type' => 'number',
                'min' => 0,
                'step' => 1,
                'placeholder' => 'padding-top',
                'tab' => 'tab0',
            ),
            array(
                'id' => 'agro_page_hero_pb',
                'name' => esc_html__('Hero Bottom Spacing (px)', 'agro'),
                'desc' => esc_html__('Use simple number without px or unit.Default : 150)', 'agro'),
                'type' => 'number',
                'min' => 0,
                'step' => 1,
                'placeholder' => 'padding-bottom',
                'tab' => 'tab0',
            ),
            array(
                'type' => 'custom_html',
                'std' => '<div id="page-hero-off-info" class="nt-page-info"><span class="dashicons dashicons-hidden"></span>'.esc_html__('The Hero Section is Disabled', 'agro').'</div>',
                'tab' => 'tab0',
            ),
            // tab1
            array(
                'id' => 'agro_page_title_tab',
                'name' => esc_html__('Page Title Options', 'agro'),
                'type' => 'heading',
                'tab' => 'tab1',
            ),
            array(
                'id' => 'agro_page_hero_title',
                'name' => esc_html__('Alternate Page Title', 'agro'),
                'clone' => false,
                'type' => 'text',
                'std' => '',
                'tab' => 'tab1',
            ),
            array(
                'id' => 'agro_page_hero_title_clr',
                'name' => esc_html__('Page Title Color', 'agro'),
                'type' => 'color',
                'tab' => 'tab1',
            ),
            array(
                'id' => 'agro_page_hero_title_fs',
                'name' => esc_html__('Page Title Font Size', 'agro'),
                'type' => 'number',
                'min' => 0,
                'step' => 1,
                'tab' => 'tab1',
            ),
            array(
                'id' => 'agro_page_hero_title_mb',
                'name' => esc_html__('Page Title margin-bottom', 'agro'),
                'type' => 'number',
                'min' => 0,
                'step' => 1,
                'tab' => 'tab1',
            ),
            array(
                'type' => 'divider',
                'tab' => 'tab1',
            ),
            // tab4
            array(
                'id' => 'agro_page_slogan_tab',
                'name' => esc_html__('Page Slogan Options', 'agro'),
                'type' => 'heading',
                'tab' => 'tab2',
            ),
            array(
                'id' => 'agro_page_hero_slogan',
                'name' => esc_html__('Page Slogan', 'agro'),
                'clone' => false,
                'type' => 'textarea',
                'tab' => 'tab2',
            ),
            array(
                'id' => 'agro_page_hero_slogan_clr',
                'name' => esc_html__('Page Slogan Color', 'agro'),
                'type' => 'color',
                'tab' => 'tab2',
            ),
            array(
                'id' => 'agro_page_hero_slogan_fs',
                'name' => esc_html__('Page Slogan Font Size', 'agro'),
                'type' => 'number',
                'min' => 0,
                'step' => 1,
                'tab' => 'tab2',
            ),
            // tab3
            array(
                'id' => 'agro_page_desc_tab',
                'name' => esc_html__('Page Description Options', 'agro'),
                'type' => 'heading',
                'tab' => 'tab3',
            ),
            array(
                'id' => 'agro_page_hero_desc',
                'name' => esc_html__('Page Description', 'agro'),
                'clone' => false,
                'type' => 'textarea',
                'tab' => 'tab3',
            ),
            array(
                'id' => 'agro_page_hero_desc_clr',
                'name' => esc_html__('Page Description Color', 'agro'),
                'type' => 'color',
                'tab' => 'tab3',
            ),
            array(
                'id' => 'agro_page_hero_desc_fs',
                'name' => esc_html__('Page Description Font Size', 'agro'),
                'type' => 'number',
                'min' => 0,
                'step' => 1,
                'tab' => 'tab3',
            ),
        )
    );

    /* ----------------------------------------------------- */
    // PAGE CONTENT OPTIONS
    /* ----------------------------------------------------- */

    $meta_boxes[] = array(
        'title' => esc_html__('Page Content Options', 'agro'),
        'id' => 'pagecontentsettings',
        'pages' => array( 'page' ),
        'context' => 'normal',
        'priority' => 'high',
        'tab_style' => 'box',
        'show' => array( 'template' => 'default-page.php' ),
        'fields' => array(
            array(
                'id' => 'agro_page_content_pt',
                'name' => esc_html__('Page Content Top Spacing (px)', 'agro'),
                'desc' => esc_html__('Use simple number without px or unit.', 'agro'),
                'type' => 'number',
                'min' => 0,
                'step' => 1,
                'placeholder' => 'padding-top',
            ),
            array(
                'id' => 'agro_page_content_pb',
                'name' => esc_html__('Page Content Bottom Spacing (px)', 'agro'),
                'desc' => esc_html__('Use simple number without px or unit.', 'agro'),
                'type' => 'number',
                'min' => 0,
                'step' => 1,
                'placeholder' => 'padding-bottom',
            )
        )
    );

    /* ----------------------------------------------------- */
    // PAGE SIDEBAR
    /* ----------------------------------------------------- */
    $meta_boxes[] = array(
        'title' => esc_html__('Page Sidebar Options', 'agro'),
        'id' => 'pagesidebarsettings',
        'pages' => array( 'page' ),
        'context' => 'normal',
        'priority' => 'high',
        'tab_style' => 'box',
        'show' => array( 'template' => 'default-page.php' ),
        'fields' => array(

            array(
                'id' => 'agro_page_layout',
                'name' => esc_html__('Page Layout', 'agro'),
                'type' => 'image_select',
                'options' => array(
                    'left-sidebar' => get_template_directory_uri().'/images/sidebar-left.png',
                    'full-width' => get_template_directory_uri().'/images/sidebar-none.png',
                    'right-sidebar' => get_template_directory_uri().'/images/sidebar-right.png',

                ),
                'multiple' => false,
                'std' => 'full-width',
            ),
        )
    );

    /* ----------------------------------------------------- */
    // PORTFOLIO SETTINGS
    /* ----------------------------------------------------- */

    $meta_boxes[] = array(
        'id' => 'portfoliosettings',
        'title' => esc_html__('Portfolio Post General', 'agro'),
        'pages' => array( 'portfolio' ),
        'context' => 'normal',
        'priority' => 'high',
        'fields' => array(
            array(
                'type' => 'heading',
                'id' => 'agro_page_design_section',
                'name' => esc_html__('Portfolio Popup Video', 'agro'),
            ),
            array(
                'name' => esc_html__('Show in lightbox', 'agro'),
                'desc' => esc_html__('If selected, the image will be displayed in the light box when the image is clicked.', 'agro'),
                'id' => 'agro_port_showlbx',
                'type' => 'checkbox',
                'std' => 0,
            ),
            array(
                'name' => esc_html__('Video url ( for simple in Lightbox )', 'agro'),
                'desc' => sprintf(esc_html__('This option for portfolio shortcode.You can add youtube or vimeo video for portfolio item. %s %s and %s %s', 'agro'), '<b>Youtube URL format:</b>', '<code>https://www.youtube.com/watch?v=ZqnAGgjQ7Rs</code>', '<b>Vimeo URL format:</b>', '<code>https://vimeo.com/203116933</code>', 'agro'),
                'id' => 'agro_port_vidurl',
                'type' => 'text',
            ),

        )
    );


    /* ----------------------------------------------------- */
    // EVENT POST SETTINGS
    /* ----------------------------------------------------- */

    $meta_boxes[] = array(
        'id' => 'portfoliosettings',
        'title' => esc_html__('Event Post General', 'agro'),
        'pages' => array( 'event' ),
        'context' => 'normal',
        'priority' => 'high',
        'fields' => array(

            array(
                'type' => 'heading',
                'id' => 'agro_post_design_section',
                'name' => esc_html__('Event Post Details', 'agro'),
            ),
            array(
                'id' => 'agro_event_map',
                'name' => esc_html__('Event avunue', 'agro'),
                'clone' => false,
                'type' => 'text',
            ),
            array(
                'id' => 'agro_event_eventdate',
                'name' => esc_html__('Event time', 'agro'),
                'clone' => false,
                'type' => 'text',
            ),
            array(
                'id' => 'agro_event_speakers',
                'name' => esc_html__('Event speakers', 'agro'),
                'clone' => false,
                'type' => 'text',
            ),
            array(
                'id' => 'agro_event_speakersjob',
                'name' => esc_html__('Event speakers position', 'agro'),
                'clone' => false,
                'type' => 'text',
            ),
            array(
                'id' => 'agro_event_speakersimg',
                'name' => esc_html__('Event speakers image', 'agro'),
                'type' => 'image_advanced',
                'max_file_uploads' => 1,
            ),
            array(
                'name' => esc_html__('Event single details slider images', 'agro'),
                'desc' => esc_html__('Select the images from the media library.', 'agro'),
                'id' => 'agro_single_gallery',
                'type' => 'image_advanced',
            )
        )
    );

    /* ----------------------------------------------------- */
    // Single-product
    /* ----------------------------------------------------- */

    $meta_boxes[] = array(
        'id' => 'product-settings',
        'title' => esc_html__('Product Settings', 'agro'),
        'pages' => array( 'Product' ),
        'context' => 'normal',
        'priority' => 'high',
        'fields' => array(
            array(
                'id' => 'agro_s_p_h_i_o',
                'name' => esc_html__('Hero image type', 'agro'),
                'desc' => esc_html__('Select page hero image type.', 'agro'),
                'type' => 'select',
                'options' => array(
                    '' => esc_html__('Select type', 'agro'),
                    'default' => esc_html__('Default Theme Options', 'agro'),
                    'private' => esc_html__('Private', 'agro'),
                ),
            ),
            array(
                'id' => 'agro_s_p_h_i',
                'name' => esc_html__('Hero Background Image', 'agro'),
                'type' => 'background',
            ),
        )
    );

    /*----------------------------------------------------------------------------------*/
    /*  GALLERY POST FORMAT
    /*-----------------------------------------------------------------------------------*/

    $meta_boxes[] = array(
        'title' => esc_html__('Gallery Settings', 'agro'),
        'id' => 'gallery-settings',
        'pages' => array('post'),
        'fields' => array(
            array(
                'name' => esc_html__('Select Images', 'agro'),
                'desc' => esc_html__('Select the images from the media library or upload your new ones.', 'agro'),
                'id' => 'agro_post_gallery',
                'type' => 'image_advanced',
            )
        )
    );


    /*----------------------------------------------------------------------------------*/
    /*  VIDEO & AUDIO EMBED POST FORMAT
    /*-----------------------------------------------------------------------------------*/

    $meta_boxes[] = array(
        'title' => esc_html__('Embeded Content', 'agro'),
        'id' => 'embed-settings',
        'pages' => array('post'),
        'fields' => array(
            array(
                'name' => esc_html__('Embeded Code', 'agro'),
                'desc' => esc_html__('You can add any content before post content box.', 'agro'),
                'id' => 'agro_embed_content',
                'type' => 'textarea',
                'rows' => 8
            )
        )
    );
    //end
    return $meta_boxes;
}
