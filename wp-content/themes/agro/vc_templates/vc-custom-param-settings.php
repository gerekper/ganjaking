<?php

// Please ignore for theme check warning, it is not add_shortcode (Custom post-content shortcode) function

if(function_exists('vc_add_shortcode_param')) { // ignore for theme check

    vc_add_shortcode_param('agro_spacer', 'agro_param_settings_field');
    function agro_param_settings_field($settings, $value)
    {
        return '<div class="agro_spacer_block"></div>'; // emtpy param for group description
    }

    vc_add_shortcode_param('nt_spacer', 'charitty_param_settings_field');
    function charitty_param_settings_field($settings, $value)
    {
        return '<div name="' . esc_attr( $settings['param_name'] ) . '" class="nt_spacer_block wpb_vc_param_value ' . esc_attr( $settings['param_name'] ) . '""></div>'; // emtpy param for group description
    }

    vc_add_shortcode_param('nt_hr', 'charitty_param_type_hr_field');
    function charitty_param_type_hr_field($settings, $value)
    {
        return '<div name="' . esc_attr( $settings['param_name'] ) . '" class="nt_hr wpb_vc_param_value ' . esc_attr( $settings['param_name'] ) . '""></div>'; // emtpy param for group description
    }

}


// Filter to replace default css class names for vc_row shortcode and vc_column
add_filter('vc_shortcodes_css_class', 'agro_css_classes_for_vc_row_and_vc_column', 10, 2);
function agro_css_classes_for_vc_row_and_vc_column($class_string, $tag)
{
    if ($tag == 'vc_column' || $tag == 'vc_column_inner') {
        $class_string = preg_replace('/vc_col-xs-(\d{1,2})/', 'col-$1', $class_string);
        $class_string = preg_replace('/vc_col-xs-offset-(\d{1,2})/', 'col-$1', $class_string);
        $class_string = preg_replace('/vc_col-sm-(\d{1,2})/', 'col-sm-$1', $class_string);
        $class_string = preg_replace('/vc_col-sm-offset-(\d{1,2})/', 'offset-sm-$1', $class_string);
        $class_string = preg_replace('/vc_col-md-(\d{1,2})/', 'col-md-$1', $class_string);
        $class_string = preg_replace('/vc_col-md-offset-(\d{1,2})/', 'offset-md-$1', $class_string);
        $class_string = preg_replace('/vc_col-lg-(\d{1,2})/', 'col-lg-$1', $class_string);
        $class_string = preg_replace('/vc_col-lg-offset-(\d{1,2})/', 'offset-lg-$1', $class_string);
    }
    return $class_string; // Important: you should always return modified or original $class_string
}


/***************************************************
*custom vc responsive and extra css
*
*Usage fuction is available in the vc elements
*
*for example in vc_column and other vc_elements
****************************************************/
if (! function_exists('agro_vc_extra_css')) {
    function agro_vc_extra_css($atts, $agro_unique_class = '', $agro_extra = '')
    {
        extract($atts);

        //custom code start
        $unique_class = $agro_unique_class;

        // large device
        $lg_bg = array();
        $lg_bg[] = $agro_lg_bgpos != '' && $agro_lg_bgpos != 'custom' ? '.'.$unique_class.'{background-position:'.$agro_lg_bgpos.'!important;}' : '';
        $lg_bg[] = $agro_lg_bgpos=='customlgpos' && $agro_lg_custom_bgpos != '' ? '.'.$unique_class.'{background-position:'.$agro_lg_custom_bgpos.'!important;}' : '';
        $lg_bg = !empty($lg_bg) ? implode(' ', array_filter($lg_bg)) : '';

        // medium device
        $md_media = $md_custom_media == 'yes' && $md_custom_css_media != '' ? $md_custom_css_media : '@media (max-width: 992px)';
        $md_bg = array();
        $md_bg[] = $agro_md_hidebg == 'off' ? 'background-image:none!important;': '';
        $md_bg[] = $agro_md_bgpos != '' &&  $agro_md_bgpos != 'custom' ? 'background-position:'.$agro_md_bgpos.'!important;' : '';
        $md_bg[] = $agro_md_bgpos == 'custom' &&  $agro_md_custom_bgpos != '' ? 'background-position:'.$agro_md_custom_bgpos.'!important;' : '';
        $md_bg  = !empty($md_bg) ? implode(' ', array_filter($md_bg)) : '';

        // small device
        $sm_media = $sm_custom_media == 'yes' && $sm_custom_css_media != '' ? $sm_custom_css_media : '@media (max-width: 768px)';
        $sm_bg = array();
        $sm_bg[] = $agro_sm_hidebg == 'off' ? 'background-image:none!important;': '';
        $sm_bg[] = $agro_sm_bgpos != '' &&  $agro_sm_bgpos != 'custom' ? 'background-position:'.$agro_sm_bgpos.'!important;' : '';
        $sm_bg[] = $agro_sm_bgpos == 'custom' &&  $agro_sm_custom_bgpos != '' ? 'background-position:'.$agro_sm_custom_bgpos.'!important;' : '';
        $sm_bg  = !empty($sm_bg) ? implode(' ', array_filter($sm_bg)) : '';

        // extra small device
        $xs_media = $xs_custom_media == 'yes' && $xs_custom_css_media != '' ? $xs_custom_css_media : '@media (max-width: 576px)';
        $xs_bg = array();
        $xs_bg[] = $agro_xs_hidebg == 'off' ? 'background-image:none!important;': '';
        $xs_bg[] = $agro_xs_bgpos != '' &&  $agro_xs_bgpos != 'custom' ? 'background-position:'.$agro_xs_bgpos.'!important;' : '';
        $xs_bg[] = $agro_xs_bgpos == 'custom' &&  $agro_xs_custom_bgpos != '' ? 'background-position:'.$agro_xs_custom_bgpos.'!important;' : '';
        $xs_bg  = !empty($xs_bg) ? implode(' ', array_filter($xs_bg)) : '';


        //get only css properties from the css editor
        $md_css = preg_replace('/.vc_custom_[0-9]*{/', ' ', $agro_md_css);
        $md_css = preg_replace('/}/', ' ', $md_css);

        $sm_css = preg_replace('/.vc_custom_[0-9]*{/', ' ', $agro_sm_css);
        $sm_css = preg_replace('/}/', ' ', $sm_css);

        $xs_css = preg_replace('/.vc_custom_[0-9]*{/', ' ', $agro_xs_css);
        $xs_css = preg_replace('/}/', ' ', $xs_css);

        //create responsive media from css
        $respon = array();
        $respon[] = $lg_bg;
        $respon[] = $md_css != '' || $md_bg != '' ? $md_media.' {.'.$unique_class.'{'.$md_bg.$md_css.'}}' : '';
        $respon[] = $sm_css != '' || $sm_bg != '' ? $sm_media.' {.'.$unique_class.'{'.$sm_bg.$sm_css.'}}' : '';
        $respon[] = $xs_css != '' || $xs_bg != '' ? $xs_media.' {.'.$unique_class.'{'.$xs_bg.$xs_css.'}}' : '';

        $respon[] = is_array($agro_extra) && !empty($agro_extra) ? implode(' ', array_filter($agro_extra)) : $agro_extra;
        //add css to in attr data-res-css
        $respon = !empty($respon) ? implode(' ', array_filter($respon)) : false;
        $respon = $respon != false ? ' data-res-css="'.$respon.'"' : false;
        // end
        return $respon;
    }
}

/**********************************
*
*this is usefull when the neded some extra description
*or extra heading for group
*
*****************************************************/



    // add new option to vc elements
    $agro_add_responsive_spacing = array(
    array(
        'type' => 'dropdown',
        'heading' => esc_html__('Top / Bottom spacing', 'agro'),
        'param_name' => 'agro_row_prepad',
        'description' => esc_html__('Select prebuilt spacing', 'agro'),
        'group' => esc_html__('Design Options', 'agro'),
        'value' => array(
            esc_html__('Select a option', 'agro') => '',
            esc_html__('160px', 'agro') => 'ptb-160',
            esc_html__('150px', 'agro') => 'ptb-150',
            esc_html__('140px', 'agro') => 'ptb-140',
            esc_html__('130px', 'agro') => 'ptb-130',
            esc_html__('120px', 'agro') => 'ptb-120',
            esc_html__('110px', 'agro') => 'ptb-110',
            esc_html__('100px', 'agro') => 'ptb-100',
            esc_html__('90px', 'agro') => 'ptb-90',
            esc_html__('80px', 'agro') => 'ptb-80',
            esc_html__('70px', 'agro') => 'ptb-70',
            esc_html__('60px', 'agro') => 'ptb-60',
            esc_html__('50px', 'agro') => 'ptb-50',
            esc_html__('40px', 'agro') => 'ptb-40',
            esc_html__('30px', 'agro') => 'ptb-30',
            esc_html__('20px', 'agro') => 'ptb-20',
            esc_html__('10px', 'agro') => 'ptb-10',
        ),
        'edit_field_class' => 'vc_col-sm-6'
        ),
  );


    $agro_add_responsive_editor = array(

        // lg resolution
        array(
        'type' => 'dropdown',
        'heading' => esc_html__('Background position', 'agro'),
        'param_name' => 'agro_lg_bgpos',
        'description' => esc_html__('Select background-position', 'agro'),
        'group' => esc_html__('Design Options', 'agro'),
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
        'param_name' => 'agro_lg_custom_bgpos',
        'description' => esc_html__('Set background image position.e.g: 100% or 400px or 300px 500px or 50% 50% or .....etc', 'agro'),
        'group' => esc_html__('Design Options', 'agro'),
        'edit_field_class' => 'vc_col-sm-6',
        'dependency' => array(
            'element' => 'agro_lg_bgpos',
            'value' => 'custom'
        )
        ),
        // 992px resolution
        array(
        'type' => 'agro_new_param',
        'holder' => 'div',
        'heading' => esc_html__('Resposive options ( maximum device width 992px )', 'agro'),
        'param_name' => '992_desc',
        'weight' => -1,
        'group' => esc_html__('992px', 'agro')
        ),
        array(
        'type'       => 'checkbox',
        'heading'    => esc_html__('Use custom medium device size', 'agro'),
        'param_name' => 'md_custom_media',
        'weight' => -2,
        'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
        'description'  => sprintf(esc_html__('If checked, you can use custom css media.Default theme media is %s for medium device.', 'agro'), '<code>@media ( max-width:992px )</code>'),
        'group' 	  => esc_html__('992px', 'agro'),
        ),
        array(
            'type' => 'textfield',
            'heading' => esc_html__('Custom responsive medium media size', 'agro'),
            'param_name' => 'md_custom_css_media',
            'weight' => -3,
            'description'  => sprintf(esc_html__('Add your custom css media for medium device.e.g:  %s or %s or etc...', 'agro'), '<code>@media ( max-width:1023px )</code>', '<code>@media ( min-width:1023px )</code>'),
            'group' 	  => esc_html__('992px', 'agro'),
            'dependency' => array(
                'element' => 'md_custom_media',
                'not_empty' => true
            )
        ),
        array(
            'type' => 'css_editor',
            'heading' => esc_html__('Max width 992px resolution', 'agro'),
            'param_name' => 'agro_md_css',
            'weight' => -8,
            'description' => esc_html__('These options for 992px resolution - responsive media or your custom media size', 'agro'),
            'group' => esc_html__('992px', 'agro')
        ),
        array(
            'type' => 'dropdown',
            'heading' => esc_html__('Background position', 'agro'),
            'param_name' => 'agro_md_bgpos',
            'weight' => -9,
            'description' => esc_html__('Select background-position', 'agro'),
            'group' => esc_html__('992px', 'agro'),
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
            'type' => 'checkbox',
            'heading' => esc_html__('Disable background image?', 'agro'),
            'param_name' => 'agro_md_hidebg',
            'weight' => -10,
            'description' => esc_html__('If checked, disables background image on devices with a maximum width of 992 pixels or your custom media size.', 'agro'),
            'value' => array( esc_html__('Yes', 'agro') => 'off' ),
            'group' => esc_html__('992px', 'agro'),
            'edit_field_class' => 'vc_col-sm-6'
        ),
        array(
            'type' => 'textfield',
            'heading' => esc_html__('Custom background position', 'agro'),
            'param_name' => 'agro_md_custom_bgpos',
            'weight' => -11,
            'description' => esc_html__('Set background image position.e.g: 100% or 400px or 300px 500px or 50% 50% or .....etc', 'agro'),
            'group' => esc_html__('992px', 'agro'),
            'dependency' => array(
                'element' => 'agro_md_bgpos',
                'value' => 'custom'
            )
        ),
        // 768px resolution
        array(
        'type' => 'agro_new_param',
        'holder' => 'div',
        'weight' => -1,
        'heading' => esc_html__('Resposive options ( maximum device width 768px )', 'agro'),
        'param_name' => '768_desc',
        'group' => esc_html__('768px', 'agro')
        ),
        array(
        'type'       => 'checkbox',
        'heading'    => esc_html__('Use custom small device size', 'agro'),
        'param_name' => 'sm_custom_media',
        'weight' => -2,
        'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
        'description'  => sprintf(esc_html__('If checked, you can use custom css media.Default theme media is %s for small device.', 'agro'), '<code>@media ( max-width:768px )</code>'),
        'group' 	  => esc_html__('768px', 'agro')
        ),
        array(
        'type' => 'textfield',
        'heading' => esc_html__('Custom responsive small media size', 'agro'),
        'param_name' => 'sm_custom_css_media',
        'weight' => -3,
        'description'  => sprintf(esc_html__('Add your custom css media for small device.e.g:  %s or %s or etc...', 'agro'), '<code>@media ( max-width:800px )</code>', '<code>@media ( min-width:800px )</code>'),
        'group' 	  => esc_html__('768px', 'agro'),
        'dependency' => array(
            'element' => 'sm_custom_media',
            'not_empty' => true
        )
        ),
        array(
        'type' => 'css_editor',
        'heading' => esc_html__('Max width 768px resolution', 'agro'),
        'param_name' => 'agro_sm_css',
        'weight' => -8,
        'description' => esc_html__('These options for 768px resolution - responsive media or your custom media size', 'agro'),
        'group' => esc_html__('768px', 'agro')
        ),
        array(
        'type' => 'dropdown',
        'heading' => esc_html__('Background position', 'agro'),
        'param_name' => 'agro_sm_bgpos',
        'weight' => -9,
        'description' => esc_html__('Select background-position', 'agro'),
        'group' => esc_html__('768px', 'agro'),
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
        'type' => 'checkbox',
        'heading' => esc_html__('Disable background image?', 'agro'),
        'param_name' => 'agro_sm_hidebg',
        'weight' => -10,
        'description' => esc_html__('f checked, disables background image on devices with a maximum width of 768 pixels or your custom media size.', 'agro'),
        'value' => array( esc_html__('Yes', 'agro') => 'off' ),
        'group' => esc_html__('768px', 'agro'),
        'edit_field_class' => 'vc_col-sm-6'
        ),
        array(
        'type' => 'textfield',
        'heading' => esc_html__('Custom background position', 'agro'),
        'param_name' => 'agro_sm_custom_bgpos',
        'weight' => -11,
        'description' => esc_html__('Set background image position.e.g: 100% or 400px or 300px 500px or 50% 50% or .....etc', 'agro'),
        'group' => esc_html__('768px', 'agro'),
        'dependency' => array(
            'element' => 'agro_sm_bgpos',
            'value' => 'custom'
        )
        ),
        //576px resolution
        array(
        'type' => 'agro_new_param',
        'holder' => 'div',
        'heading' => esc_html__('Resposive options ( maximum device width 576px )', 'agro'),
        'param_name' => '576_desc',
        'weight' => -1,
        'group' => esc_html__('576px', 'agro')
        ),
        array(
        'type'       => 'checkbox',
        'heading'    => esc_html__('Use custom extra small device size', 'agro'),
        'param_name' => 'xs_custom_media',
        'weight' => -2,
        'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
        'description'  => sprintf(esc_html__('If checked, you can use custom css media.Default theme media is %s for extra small device.', 'agro'), '<code>@media ( max-width:576px )</code>'),
        'group' 	  => esc_html__('576px', 'agro')
        ),
        array(
        'type' => 'textfield',
        'heading' => esc_html__('Custom responsive extra small media size', 'agro'),
        'param_name' => 'xs_custom_css_media',
        'weight' => -3,
        'description'  => sprintf(esc_html__('Add your custom css media for extra small device.e.g:  %s or %s or etc...', 'agro'), '<code>@media ( max-width:600px )</code>', '<code>@media ( min-width:600px )</code>'),
        'group' 	  => esc_html__('576px', 'agro'),
        'dependency' => array(
            'element' => 'xs_custom_media',
            'not_empty' => true
        )
        ),
        array(
        'type' => 'css_editor',
        'heading' => esc_html__('Max width 576px resolution', 'agro'),
        'param_name' => 'agro_xs_css',
        'weight' => -8,
        'description' => esc_html__('These options for 576px resolution - responsive media or your custom media size', 'agro'),
        'group' => esc_html__('576px', 'agro')
        ),
        array(
        'type' => 'dropdown',
        'heading' => esc_html__('Background position', 'agro'),
        'param_name' => 'agro_xs_bgpos',
        'weight' => -9,
        'description' => esc_html__('Select background-position', 'agro'),
        'group' => esc_html__('576px', 'agro'),
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
        'type' => 'checkbox',
        'heading' => esc_html__('Disable background image?', 'agro'),
        'param_name' => 'agro_xs_hidebg',
        'weight' => -10,
        'description' => esc_html__('f checked, disables background image on devices with a maximum width of 576 pixels or your custom media size.', 'agro'),
        'value' => array( esc_html__('Yes', 'agro') => 'off' ),
        'group' => esc_html__('576px', 'agro'),
        'edit_field_class' => 'vc_col-sm-6'
        ),
        array(
        'type' => 'textfield',
        'heading' => esc_html__('Custom background position', 'agro'),
        'param_name' => 'agro_xs_custom_bgpos',
        'weight' => -11,
        'description' => esc_html__('Set background image position.e.g: 100% or 400px or 300px 500px or 50% 50% or .....etc', 'agro'),
        'group' => esc_html__('576px', 'agro'),
        'dependency' => array(
            'element' => 'agro_xs_bgpos',
            'value' => 'custom'
        )
        )
    );


    /*-----------------------------------------------------------------------------------*/
    /*	Shortcode Filter
    /*-----------------------------------------------------------------------------------*/
  function agro_vc_shortcode_extra()
  {

        //Get current values stored in the color param in "Call to Action" element
      $param = WPBMap::getParam('vc_row', 'full_width');
      //Append new value to the 'value' array
      $param['value'][esc_html__('Agro Container', 'agro')] = 'container';
      $param['value'][esc_html__('Agro Container-fluid', 'agro')] = 'container-fluid';
      $param['value'][esc_html__('Agro Container-stretch', 'agro')] = 'container-stretch';
      $param['value'][esc_html__('Agro Container-null', 'agro')] = 'container-null';
      $param['admin_label'] = true;
      //Finally "mutate" param with new values
      vc_update_shortcode_param('vc_row', $param);
      //Get current values stored in the color param in "Call to Action" element
      $param = WPBMap::getParam('vc_row', 'parallax');
      //Append new value to the 'value' array
      $param['value'][esc_html__('Theme scroll (extra)', 'agro')] = 'agro-scroll';
      $param['value'][esc_html__('Theme scale (extra)', 'agro')] = 'agro-scale';
      $param['value'][esc_html__('Theme opacity (extra)', 'agro')] = 'agro-opacity';
      $param['value'][esc_html__('Theme scroll-opacity (extra)', 'agro')] = 'agro-scroll-opacity';
      $param['value'][esc_html__('Theme scale-opacity (extra)', 'agro')] = 'agro-scale-opacity';
      //Finally "mutate" param with new values
      vc_update_shortcode_param('vc_row', $param);
      //Get current values stored in the color param in "Call to Action" element
      $param = WPBMap::getParam('vc_row', 'video_bg_parallax');
      //Append new value to the 'value' array
      $param['value'][esc_html__('Theme scroll (extra)', 'agro')] = 'agro-scroll';
      $param['value'][esc_html__('Theme scale (extra)', 'agro')] = 'agro-scale';
      $param['value'][esc_html__('Theme opacity (extra)', 'agro')] = 'agro-opacity';
      $param['value'][esc_html__('Theme scroll-opacity (extra)', 'agro')] = 'agro-scroll-opacity';
      $param['value'][esc_html__('Theme scale-opacity (extra)', 'agro')] = 'agro-scale-opacity';
      //Finally "mutate" param with new values
      vc_update_shortcode_param('vc_row', $param);


    //Get current values stored in the color param in "Call to Action" element
      $param = WPBMap::getParam('vc_row', 'parallax_speed_bg');
      $param['dependency']['not_empty']= 'value';
      WPBMap::mutateParam('vc_row', $param);

      $param = WPBMap::getParam('vc_row', 'parallax_speed_bg');
      $param['dependency']['value']= array('content-moving', 'content-moving-fade');
      WPBMap::mutateParam('vc_row', $param);

      $param = WPBMap::getParam('vc_row', 'parallax_speed_bg');
      $param['weight'] = -1;
      vc_update_shortcode_param('vc_row', $param);

      $param = WPBMap::getParam('vc_row', 'css_animation');
      $param['weight'] = -2;
      vc_update_shortcode_param('vc_row', $param);

      $param = WPBMap::getParam('vc_row', 'el_id');
      $param['weight'] = -3;
      vc_update_shortcode_param('vc_row', $param);

      $param = WPBMap::getParam('vc_row', 'disable_element');
      $param['weight'] = -4;
      vc_update_shortcode_param('vc_row', $param);

      $param = WPBMap::getParam('vc_row', 'el_class');
      $param['weight'] = -5;
      vc_update_shortcode_param('vc_row', $param);
  }
  add_action('vc_after_init', 'agro_vc_shortcode_extra');


    //FOR ROW EXTRA RESPONSIVE
    $agro_vc_row_extra_attributes = array(

    //new options for paralax
    array(
        'type' => 'textfield',
        'heading' => esc_html__('Theme Parallax speed', 'agro'),
        'description' => esc_html__('Enter parallax speed ratio (Note: Default value is 0.2, min value is 0.1)', 'agro'),
        'param_name' => 'agro_parallax_speed',
        'value' => '0.2',
        'edit_field_class' => 'vc_col-sm-6',
        'dependency' => array(
            'element' => 'parallax',
            'value' => array( 'agro-scroll', 'agro-scale','agro-opacity','agro-scroll-opacity','agro-scale-opacity' ),
        ),
    ),
    array(
        'type' => 'dropdown',
        'heading' => esc_html__('Parallax background opacity', 'agro'),
        'description' => esc_html__('Select background-attachment', 'agro'),
        'param_name' => 'agro_parallax_bg_opacity',
        'edit_field_class' => 'vc_col-sm-6',
        'value' => array(
            esc_html__('Select a option', 'agro') => '',
            esc_html__('0.1', 'agro') => '01',
            esc_html__('0.2', 'agro') => '02',
            esc_html__('0.3', 'agro') => '03',
            esc_html__('0.4', 'agro') => '04',
            esc_html__('0.5', 'agro') => '05',
            esc_html__('0.6', 'agro') => '06',
            esc_html__('0.7', 'agro') => '07',
            esc_html__('0.8', 'agro') => '08',
            esc_html__('0.9', 'agro') => '09',
            esc_html__('1', 'agro') => '1',
            ),
        'dependency' => array(
            'element' => 'parallax',
            'value' => array( 'agro-scroll', 'agro-scale','agro-opacity','agro-scroll-opacity','agro-scale-opacity' ),
        ),
    ),
    array(
        'type' => 'dropdown',
        'heading' => esc_html__('Background size', 'agro'),
        'description' => esc_html__('Select background-attachment', 'agro'),
        'param_name' => 'agro_bg_size',
        'edit_field_class' => 'vc_col-sm-4',
        'value' => array(
            esc_html__('Select a option', 'agro') => '',
            esc_html__('auto', 'agro') => 'auto',
            esc_html__('cover', 'agro') => 'cover',
            esc_html__('contain', 'agro') => 'contain',
            esc_html__('Custom size', 'agro') => 'custom',
            ),
        'dependency' => array(
            'element' => 'parallax',
            'value' => array( 'agro-scroll', 'agro-scale','agro-opacity','agro-scroll-opacity','agro-scale-opacity' ),
        ),
    ),
    array(
        'type' => 'dropdown',
        'heading' => esc_html__('Background position', 'agro'),
        'description' => esc_html__('Select background-position', 'agro'),
        'param_name' => 'agro_bg_pos',
        'edit_field_class' => 'vc_col-sm-4',
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
        'dependency' => array(
            'element' => 'parallax',
            'value' => array( 'agro-scroll', 'agro-scale','agro-opacity','agro-scroll-opacity','agro-scale-opacity' ),
        ),
    ),
    array(
        'type' => 'dropdown',
        'heading' => esc_html__('Background repeat', 'agro'),
        'description' => esc_html__('Select background-repeat', 'agro'),
        'param_name' => 'agro_bg_repet',
        'edit_field_class' => 'vc_col-sm-4',
        'value' => array(
            esc_html__('Select a option', 'agro') => '',
            esc_html__('no-repeat', 'agro') => 'no-repeat',
            esc_html__('repeat', 'agro') => 'repeat',
            esc_html__('repeat-x', 'agro') => 'repeat-x',
            esc_html__('repeat-y', 'agro') => 'repeat-y',
        ),
        'dependency' => array(
            'element' => 'parallax',
            'value' => array( 'agro-scroll', 'agro-scale','agro-opacity','agro-scroll-opacity','agro-scale-opacity' ),
        ),
    ),
    array(
        'type' => 'textfield',
        'heading' => esc_html__('Custom background size', 'agro'),
        'description' => esc_html__('Set background image size.e.g: 100% or 400px or 300px 500px or 50% 50% or .....etc', 'agro'),
        'param_name' => 'agro_custom_bg_size',
        'edit_field_class' => 'vc_col-sm-6',
        'dependency' => array(
            'element' => 'agro_bg_size',
            'value' => 'custom'
        ),
    ),
    array(
        'type' => 'textfield',
        'heading' => esc_html__('Custom background position', 'agro'),
        'description' => esc_html__('Set background image position.e.g: 100% or 400px or 300px 500px or 50% 50% or .....etc', 'agro'),
        'param_name' => 'agro_custom_bg_pos',
        'edit_field_class' => 'vc_col-sm-6',
        'dependency' => array(
            'element' => 'agro_bg_pos',
            'value' => 'custom'
        ),
    ),
    array(
        'type' => 'checkbox',
        'heading' => esc_html__('Disable Parallax on mobile devices?', 'agro'),
        'description' => esc_html__('Disables parallax on mobile devices if checked.', 'agro'),
        'param_name' => 'agro_mobile_parallax',
        'value' => array( esc_html__('Yes', 'agro') => 'off' ),
        'dependency' => array(
            'element' => 'parallax',
            'value' => array( 'agro-scroll', 'agro-scale','agro-opacity','agro-scroll-opacity','agro-scale-opacity' ),
        ),
    ),
    array(
        'type' => 'dropdown',
        'heading' => esc_html__('Row overflow', 'agro'),
        'description' => esc_html__('Select row overflow', 'agro'),
        'param_name' => 'agro_row_overflow',
        'value' => array(
            esc_html__('Select a option', 'agro') => '',
            esc_html__('visible', 'agro') => 'visible',
            esc_html__('hidden', 'agro') => 'hidden',
        ),
    ),
    array(
        'type' => 'textfield',
        'heading' => esc_html__('Row Z-index', 'agro'),
        'description' => esc_html__('Add z-idex number for overflowed row', 'agro'),
        'param_name' => 'agro_bg_zindex',
        'dependency' => array(
            'element' => 'agro_row_overflow',
            'value' => 'visible',
        ),
    ),
    //design options grup
    array(
        'type' => 'dropdown',
        'heading' => esc_html__('Background overlay type', 'agro'),
        'param_name' => 'agro_row_overlay_type',
        'group' => esc_html__('Design Options', 'agro'),
        'value' => array(
            esc_html__('Select a option', 'agro') => '',
            esc_html__('Black pattern image', 'agro') => 'pattern-black',
            esc_html__('White pattern image', 'agro') => 'pattern-white',
            esc_html__('Custom color', 'agro') => 'custom',
        ),
        'edit_field_class' => 'vc_col-sm-6',
    ),
    array(
        'type' => 'colorpicker',
        'heading' => esc_html__('Row overlay color', 'agro'),
        'description' => esc_html__('Add overlay on background image.', 'agro'),
        'param_name' => 'agro_row_overlayclr',
        'group' => esc_html__('Design Options', 'agro'),
        'dependency' => array(
            'element' => 'agro_row_overlay_type',
            'value' => 'custom',
        ),
        'edit_field_class' => 'vc_col-sm-6',
    ),
    array(
        'type' => 'dropdown',
        'heading' => esc_html__('Background attachment', 'agro'),
        'param_name' => 'agro_bg_attachment',
        'description' => esc_html__('Select background-position', 'agro'),
        'group' => esc_html__('Design Options', 'agro'),
        'value' => array(
            esc_html__('Select a option', 'agro') => '',
            esc_html__('fixed', 'agro') => 'fixed',
            esc_html__('scroll', 'agro') => 'scroll',
            esc_html__('inherit', 'agro') => 'inherit',
        ),
        'edit_field_class' => 'vc_col-sm-4',
    ),
  );

    //FOR ROW EXTRA RESPONSIVE
    $agro_disable_column_width = array(

        array(
            'type' => 'checkbox',
            'param_name' => 'agro_disable_column',
            'heading' => esc_html__('Disable Column Width?', 'agro'),
            'value' => array( esc_html__('Yes', 'agro') => 'yes' ),
            'weight' => 1
        ),
        array(
            'type' => 'dropdown',
            'heading' => esc_html__('XL Column Width', 'agro'),
            'param_name' => 'agro_xl_column_width',
            'description' => esc_html__('Select background-position', 'agro'),
            'group' => esc_html__('Responsive Options', 'agro'),
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
            'heading' => esc_html__('XL Column Offset', 'agro'),
            'param_name' => 'agro_xl_column_offset',
            'description' => esc_html__('Select background-position', 'agro'),
            'group' => esc_html__('Responsive Options', 'agro'),
            'edit_field_class' => 'vc_col-sm-6',
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
    );
    //FOR ROW
    vc_add_params('vc_row', $agro_add_responsive_spacing);
    vc_add_params('vc_row', $agro_vc_row_extra_attributes);
    vc_add_params('vc_row', $agro_add_responsive_editor);
    //FOR INNER ROW
    vc_add_params('vc_row_inner', $agro_add_responsive_spacing);
    vc_add_params('vc_row_inner', $agro_add_responsive_editor);
    //FOR COLUMN
    vc_add_params('vc_column', $agro_disable_column_width);
    vc_add_params('vc_column', $agro_add_responsive_editor);
    //FOR COLUMN INNER
    vc_add_params('vc_column_inner', $agro_disable_column_width);
    vc_add_params('vc_column_inner', $agro_add_responsive_editor);

    // Add new custom font to Font Family selection in icon box module
    function agro_add_ionicons()
    {
        $param = WPBMap::getParam('vc_icon', 'type');
        $param['value'][esc_html__('Ionicons', 'agro')] = 'ionicons';
        vc_update_shortcode_param('vc_icon', $param);
        $param1 = WPBMap::getParam('vc_icon', 'size');
        $param1['value'][esc_html__('Theme size', 'agro')] = 'c-summary-1-icon';
        vc_update_shortcode_param('vc_icon', $param1);
    }
    add_filter('init', 'agro_add_ionicons', 40);

    // Add font picker setting to icon box module when you select your font family from the dropdown
    function agro_add_font_picker()
    {
        vc_add_param(
            'vc_icon',
            array(
                'type' => 'iconpicker',
                'weight' => 1,
                'heading' => esc_html__('Icon', 'agro'),
                'param_name' => 'icon_ionicons',
                'settings' => array(
                        'emptyIcon' => false,
                        'type' => 'ionicons',
                        'iconsPerPage' => 200,
                ),
                'dependency' => array(
                        'element' => 'type',
                        'value' => 'ionicons',
                ),
            )
        );
    }
    add_filter('vc_after_init', 'agro_add_font_picker', 40);

    // Add array of your fonts so they can be displayed in the font selector
    function agro_icon_array()
    {
        return array(
         array('ionicons ion-alert' => 'alert'),
         array('ionicons ion-alert-circled' => 'alert-circled'),
         array('ionicons ion-android-add' => 'android-add'),
         array('ionicons ion-android-add-circle' => 'android-add-circle'),
         array('ionicons ion-android-alarm-clock' => 'android-alarm-clock'),
         array('ionicons ion-android-alert' => 'android-alert'),
         array('ionicons ion-android-apps' => 'android-apps'),
         array('ionicons ion-android-archive' => 'android-archive'),
         array('ionicons ion-android-arrow-back' => 'android-arrow-back'),
         array('ionicons ion-android-arrow-down' => 'android-arrow-down'),
         array('ionicons ion-android-arrow-dropdown' => 'android-arrow-dropdown'),
         array('ionicons ion-android-arrow-dropdown-circle' => 'android-arrow-dropdown-circle'),
         array('ionicons ion-android-arrow-dropleft' => 'android-arrow-dropleft'),
         array('ionicons ion-android-arrow-dropleft-circle' => 'android-arrow-dropleft-circle'),
         array('ionicons ion-android-arrow-dropright' => 'android-arrow-dropright'),
         array('ionicons ion-android-arrow-dropright-circle' => 'android-arrow-dropright-circle'),
         array('ionicons ion-android-arrow-dropup' => 'android-arrow-dropup'),
         array('ionicons ion-android-arrow-dropup-circle' => 'android-arrow-dropup-circle'),
         array('ionicons ion-android-arrow-forward' => 'android-arrow-forward'),
         array('ionicons ion-android-arrow-up' => 'android-arrow-up'),
         array('ionicons ion-android-attach' => 'android-attach'),
         array('ionicons ion-android-bar' => 'android-bar'),
         array('ionicons ion-android-bicycle' => 'android-bicycle'),
         array('ionicons ion-android-boat' => 'android-boat'),
         array('ionicons ion-android-bookmark' => 'android-bookmark'),
         array('ionicons ion-android-bulb' => 'android-bulb'),
         array('ionicons ion-android-bus' => 'android-bus'),
         array('ionicons ion-android-calendar' => 'android-calendar'),
         array('ionicons ion-android-call' => 'android-call'),
         array('ionicons ion-android-camera' => 'android-camera'),
         array('ionicons ion-android-cancel' => 'android-cancel'),
         array('ionicons ion-android-car' => 'android-car'),
         array('ionicons ion-android-cart' => 'android-cart'),
         array('ionicons ion-android-chat' => 'android-chat'),
         array('ionicons ion-android-checkbox' => 'android-checkbox'),
         array('ionicons ion-android-checkbox-blank' => 'android-checkbox-blank'),
         array('ionicons ion-android-checkbox-outline' => 'android-checkbox-outline'),
         array('ionicons ion-android-checkbox-outline-blank' => 'android-checkbox-outline-blank'),
         array('ionicons ion-android-checkmark-circle' => 'android-checkmark-circle'),
         array('ionicons ion-android-clipboard' => 'android-clipboard'),
         array('ionicons ion-android-close' => 'android-close'),
         array('ionicons ion-android-cloud' => 'android-cloud'),
         array('ionicons ion-android-cloud-circle' => 'android-cloud-circle'),
         array('ionicons ion-android-cloud-done' => 'android-cloud-done'),
         array('ionicons ion-android-cloud-outline' => 'android-cloud-outline'),
         array('ionicons ion-android-color-palette' => 'android-color-palette'),
         array('ionicons ion-android-compass' => 'android-compass'),
         array('ionicons ion-android-contact' => 'android-contact'),
         array('ionicons ion-android-contacts' => 'android-contacts'),
         array('ionicons ion-android-contract' => 'android-contract'),
         array('ionicons ion-android-create' => 'android-create'),
         array('ionicons ion-android-delete' => 'android-delete'),
         array('ionicons ion-android-desktop' => 'android-desktop'),
         array('ionicons ion-android-document' => 'android-document'),
         array('ionicons ion-android-done' => 'android-done'),
         array('ionicons ion-android-done-all' => 'android-done-all'),
         array('ionicons ion-android-download' => 'android-download'),
         array('ionicons ion-android-drafts' => 'android-drafts'),
         array('ionicons ion-android-exit' => 'android-exit'),
         array('ionicons ion-android-expand' => 'android-expand'),
         array('ionicons ion-android-favorite' => 'android-favorite'),
         array('ionicons ion-android-favorite-outline' => 'android-favorite-outline'),
         array('ionicons ion-android-film' => 'android-film'),
         array('ionicons ion-android-folder' => 'android-folder'),
         array('ionicons ion-android-folder-open' => 'android-folder-open'),
         array('ionicons ion-android-funnel' => 'android-funnel'),
         array('ionicons ion-android-globe' => 'android-globe'),
         array('ionicons ion-android-hand' => 'android-hand'),
         array('ionicons ion-android-hangout' => 'android-hangout'),
         array('ionicons ion-android-happy' => 'android-happy'),
         array('ionicons ion-android-home' => 'android-home'),
         array('ionicons ion-android-image' => 'android-image'),
         array('ionicons ion-android-laptop' => 'android-laptop'),
         array('ionicons ion-android-list' => 'android-list'),
         array('ionicons ion-android-locate' => 'android-locate'),
         array('ionicons ion-android-lock' => 'android-lock'),
         array('ionicons ion-android-mail' => 'android-mail'),
         array('ionicons ion-android-map' => 'android-map'),
         array('ionicons ion-android-menu' => 'android-menu'),
         array('ionicons ion-android-microphone' => 'android-microphone'),
         array('ionicons ion-android-microphone-off' => 'android-microphone-off'),
         array('ionicons ion-android-more-horizontal' => 'android-more-horizontal'),
         array('ionicons ion-android-more-vertical' => 'android-more-vertical'),
         array('ionicons ion-android-navigate' => 'android-navigate'),
         array('ionicons ion-android-notifications' => 'android-notifications'),
         array('ionicons ion-android-notifications-none' => 'android-notifications-none'),
         array('ionicons ion-android-notifications-off' => 'android-notifications-off'),
         array('ionicons ion-android-open' => 'android-open'),
         array('ionicons ion-android-options' => 'android-options'),
         array('ionicons ion-android-people' => 'android-people'),
         array('ionicons ion-android-person' => 'android-person'),
         array('ionicons ion-android-person-add' => 'android-person-add'),
         array('ionicons ion-android-phone-landscape' => 'android-phone-landscape'),
         array('ionicons ion-android-phone-portrait' => 'android-phone-portrait'),
         array('ionicons ion-android-pin' => 'android-pin'),
         array('ionicons ion-android-plane' => 'android-plane'),
         array('ionicons ion-android-playstore' => 'android-playstore'),
         array('ionicons ion-android-print' => 'android-print'),
         array('ionicons ion-android-radio-button-off' => 'android-radio-button-off'),
         array('ionicons ion-android-radio-button-on' => 'android-radio-button-on'),
         array('ionicons ion-android-refresh' => 'android-refresh'),
         array('ionicons ion-android-remove' => 'android-remove'),
         array('ionicons ion-android-remove-circle' => 'android-remove-circle'),
         array('ionicons ion-android-restaurant' => 'android-restaurant'),
         array('ionicons ion-android-sad' => 'android-sad'),
         array('ionicons ion-android-search' => 'android-search'),
         array('ionicons ion-android-send' => 'android-send'),
         array('ionicons ion-android-settings' => 'android-settings'),
         array('ionicons ion-android-share' => 'android-share'),
         array('ionicons ion-android-share-alt' => 'android-share-alt'),
         array('ionicons ion-android-star' => 'android-star'),
         array('ionicons ion-android-star-half' => 'android-star-half'),
         array('ionicons ion-android-star-outline' => 'android-star-outline'),
         array('ionicons ion-android-stopwatch' => 'android-stopwatch'),
         array('ionicons ion-android-subway' => 'android-subway'),
         array('ionicons ion-android-sunny' => 'android-sunny'),
         array('ionicons ion-android-sync' => 'android-sync'),
         array('ionicons ion-android-textsms' => 'android-textsms'),
         array('ionicons ion-android-time' => 'android-time'),
         array('ionicons ion-android-train' => 'android-train'),
         array('ionicons ion-android-unlock' => 'android-unlock'),
         array('ionicons ion-android-upload' => 'android-upload'),
         array('ionicons ion-android-volume-down' => 'android-volume-down'),
         array('ionicons ion-android-volume-mute' => 'android-volume-mute'),
         array('ionicons ion-android-volume-off' => 'android-volume-off'),
         array('ionicons ion-android-volume-up' => 'android-volume-up'),
         array('ionicons ion-android-walk' => 'android-walk'),
         array('ionicons ion-android-warning' => 'android-warning'),
         array('ionicons ion-android-watch' => 'android-watch'),
         array('ionicons ion-android-wifi' => 'android-wifi'),
         array('ionicons ion-aperture' => 'aperture'),
         array('ionicons ion-archive' => 'archive'),
         array('ionicons ion-arrow-down-a' => 'arrow-down-a'),
         array('ionicons ion-arrow-down-b' => 'arrow-down-b'),
         array('ionicons ion-arrow-down-c' => 'arrow-down-c'),
         array('ionicons ion-arrow-expand' => 'arrow-expand'),
         array('ionicons ion-arrow-graph-down-left' => 'arrow-graph-down-left'),
         array('ionicons ion-arrow-graph-down-right' => 'arrow-graph-down-right'),
         array('ionicons ion-arrow-graph-up-left' => 'arrow-graph-up-left'),
         array('ionicons ion-arrow-graph-up-right' => 'arrow-graph-up-right'),
         array('ionicons ion-arrow-left-a' => 'arrow-left-a'),
         array('ionicons ion-arrow-left-b' => 'arrow-left-b'),
         array('ionicons ion-arrow-left-c' => 'arrow-left-c'),
         array('ionicons ion-arrow-move' => 'arrow-move'),
         array('ionicons ion-arrow-resize' => 'arrow-resize'),
         array('ionicons ion-arrow-return-left' => 'arrow-return-left'),
         array('ionicons ion-arrow-return-right' => 'arrow-return-right'),
         array('ionicons ion-arrow-right-a' => 'arrow-right-a'),
         array('ionicons ion-arrow-right-b' => 'arrow-right-b'),
         array('ionicons ion-arrow-right-c' => 'arrow-right-c'),
         array('ionicons ion-arrow-shrink' => 'arrow-shrink'),
         array('ionicons ion-arrow-swap' => 'arrow-swap'),
         array('ionicons ion-arrow-up-a' => 'arrow-up-a'),
         array('ionicons ion-arrow-up-b' => 'arrow-up-b'),
         array('ionicons ion-arrow-up-c' => 'arrow-up-c'),
         array('ionicons ion-asterisk' => 'asterisk'),
         array('ionicons ion-at' => 'at'),
         array('ionicons ion-backspace' => 'backspace'),
         array('ionicons ion-backspace-outline' => 'backspace-outline'),
         array('ionicons ion-bag' => 'bag'),
         array('ionicons ion-battery-charging' => 'battery-charging'),
         array('ionicons ion-battery-empty' => 'battery-empty'),
         array('ionicons ion-battery-full' => 'battery-full'),
         array('ionicons ion-battery-half' => 'battery-half'),
         array('ionicons ion-battery-low' => 'battery-low'),
         array('ionicons ion-beaker' => 'beaker'),
         array('ionicons ion-beer' => 'beer'),
         array('ionicons ion-bluetooth' => 'beer'),
         array('ionicons ion-agrofire' => 'agrofire'),
         array('ionicons ion-bookmark' => 'bookmark'),
         array('ionicons ion-bowtie' => 'bowtie'),
         array('ionicons ion-briefcase' => 'briefcase'),
         array('ionicons ion-bug' => 'bug'),
         array('ionicons ion-calculator' => 'calculator'),
         array('ionicons ion-calendar' => 'calendar'),
         array('ionicons ion-camera' => 'camera'),
         array('ionicons ion-card' => 'card'),
         array('ionicons ion-cash' => 'cash'),
         array('ionicons ion-chatbox' => 'chatbox'),
         array('ionicons ion-chatbox-working' => 'chatbox-working'),
         array('ionicons ion-chatboxes' => 'chatboxes'),
         array('ionicons ion-chatbubble' => 'chatbubble'),
         array('ionicons ion-chatbubble-working' => 'chatbubble-working'),
         array('ionicons ion-chatbubbles' => 'chatbubbles'),
         array('ionicons ion-checkmark' => 'checkmark'),
         array('ionicons ion-checkmark-circled' => 'checkmark-circled'),
         array('ionicons ion-checkmark-round' => 'checkmark-round'),
         array('ionicons ion-chevron-down' => 'chevron-down'),
         array('ionicons ion-chevron-left' => 'chevron-left'),
         array('ionicons ion-chevron-right' => 'chevron-right'),
         array('ionicons ion-chevron-up' => 'chevron-up'),
         array('ionicons ion-clipboard' => 'clipboard'),
         array('ionicons ion-clock' => 'clock'),
         array('ionicons ion-close' => 'close'),
         array('ionicons ion-close-circled' => 'close-circled'),
         array('ionicons ion-close-round' => 'close-round'),
         array('ionicons ion-closed-captioning' => 'closed-captioning'),
         array('ionicons ion-cloud' => 'cloud'),
         array('ionicons ion-code' => 'code'),
         array('ionicons ion-code-download' => 'code-download'),
         array('ionicons ion-code-working' => 'code-working'),
         array('ionicons ion-coffee' => 'coffee'),
         array('ionicons ion-compass' => 'compass'),
         array('ionicons ion-compose' => 'compose'),
         array('ionicons ion-connection-bars' => 'connection-bars'),
         array('ionicons ion-contrast' => 'contrast'),
         array('ionicons ion-crop' => 'crop'),
         array('ionicons ion-cube' => 'cube'),
         array('ionicons ion-disc' => 'disc'),
         array('ionicons ion-document' => 'document'),
         array('ionicons ion-document-text' => 'document-text'),
         array('ionicons ion-drag' => 'drag'),
         array('ionicons ion-earth' => 'earth'),
         array('ionicons ion-easel' => 'easel'),
         array('ionicons ion-edit' => 'edit'),
         array('ionicons ion-egg' => 'egg'),
         array('ionicons ion-eject' => 'eject'),
         array('ionicons ion-email' => 'email'),
         array('ionicons ion-email-unread' => 'email-unread'),
         array('ionicons ion-erlenmeyer-flask' => 'erlenmeyer-flask'),
         array('ionicons ion-erlenmeyer-flask-bubbles' => 'erlenmeyer-flask-bubbles'),
         array('ionicons ion-eye' => 'eye'),
         array('ionicons ion-eye-disabled' => 'eye-disabled'),
         array('ionicons ion-female' => 'female'),
         array('ionicons ion-filing' => 'filing'),
         array('ionicons ion-film-marker' => 'film-marker'),
         array('ionicons ion-fireball' => 'fireball'),
         array('ionicons ion-flag' => 'flag'),
         array('ionicons ion-flame' => 'flame'),
         array('ionicons ion-flash' => 'flash'),
         array('ionicons ion-flash-off' => 'flash-off'),
         array('ionicons ion-folder' => 'folder'),
         array('ionicons ion-fork' => 'fork'),
         array('ionicons ion-fork-repo' => 'fork-repo'),
         array('ionicons ion-forward' => 'forward'),
         array('ionicons ion-funnel' => 'funnel'),
         array('ionicons ion-gear-a' => 'gear-a'),
         array('ionicons ion-gear-b' => 'gear-b'),
         array('ionicons ion-grid' => 'grid'),
         array('ionicons ion-hammer' => 'hammer'),
         array('ionicons ion-happy' => 'happy'),
         array('ionicons ion-happy-outline' => 'happy-outline'),
         array('ionicons ion-headphone' => 'headphone'),
         array('ionicons ion-heart' => 'heart'),
         array('ionicons ion-heart-broken' => 'heart-broken'),
         array('ionicons ion-help' => 'help'),
         array('ionicons ion-help-buoy' => 'help-buoy'),
         array('ionicons ion-help-circled' => 'help-circled'),
         array('ionicons ion-home' => 'home'),
         array('ionicons ion-icecream' => 'icecream'),
         array('ionicons ion-image' => 'image'),
         array('ionicons ion-images' => 'images'),
         array('ionicons ion-information' => 'information'),
         array('ionicons ion-information-circled' => 'information-circled'),
         array('ionicons ion-ionic' => 'ionic'),
         array('ionicons ion-ios-alarm' => 'ios-alarm'),
         array('ionicons ion-ios-alarm-outline' => 'ios-alarm-outline'),
         array('ionicons ion-ios-albums' => 'ios-albums'),
         array('ionicons ion-ios-albums-outline' => 'ios-albums-outline'),
         array('ionicons ion-ios-americanfootball' => 'ios-americanfootball'),
         array('ionicons ion-ios-americanfootball-outline' => 'ios-americanfootball-outline'),
         array('ionicons ion-ios-analytics' => 'ios-analytics'),
         array('ionicons ion-ios-analytics-outline' => 'ios-analytics-outline'),
         array('ionicons ion-ios-arrow-back' => 'ios-arrow-back'),
         array('ionicons ion-ios-arrow-down' => 'ios-arrow-down'),
         array('ionicons ion-ios-arrow-forward' => 'ios-arrow-forward'),
         array('ionicons ion-ios-arrow-left' => 'ios-arrow-left'),
         array('ionicons ion-ios-arrow-right' => 'ios-arrow-right'),
         array('ionicons ion-ios-arrow-thin-down' => 'ios-arrow-thin-down'),
         array('ionicons ion-ios-arrow-thin-left' => 'ios-arrow-thin-left'),
         array('ionicons ion-ios-arrow-thin-right' => 'ios-arrow-thin-right'),
         array('ionicons ion-ios-arrow-thin-up' => 'ios-arrow-thin-up'),
         array('ionicons ion-ios-arrow-up' => 'ios-arrow-up'),
         array('ionicons ion-ios-at' => 'ios-at'),
         array('ionicons ion-ios-at-outline' => 'ios-at-outline'),
         array('ionicons ion-ios-barcode' => 'ios-barcode'),
         array('ionicons ion-ios-barcode-outline' => 'ios-barcode-outline'),
         array('ionicons ion-ios-baseball' => 'ios-baseball'),
         array('ionicons ion-ios-baseball-outline' => 'ios-baseball-outline'),
         array('ionicons ion-ios-basketball' => 'ios-basketball'),
         array('ionicons ion-ios-basketball-outline' => 'ios-basketball-outline'),
         array('ionicons ion-ios-bell' => 'ios-bell'),
         array('ionicons ion-ios-bell-outline' => 'ios-bell-outline'),
         array('ionicons ion-ios-body' => 'ios-body'),
         array('ionicons ion-ios-body-outline' => 'ios-body-outline'),
         array('ionicons ion-ios-bolt' => 'ios-bolt'),
         array('ionicons ion-ios-bolt-outline' => 'ios-bolt-outline'),
         array('ionicons ion-ios-book' => 'ios-book'),
         array('ionicons ion-ios-book-outline' => 'ios-book-outline'),
         array('ionicons ion-ios-bookmarks' => 'ios-bookmarks'),
         array('ionicons ion-ios-bookmarks-outline' => 'ios-bookmarks-outline'),
         array('ionicons ion-ios-box' => 'ios-box'),
         array('ionicons ion-ios-box-outline' => 'ios-box-outline'),
         array('ionicons ion-ios-briefcase' => 'ios-briefcase'),
         array('ionicons ion-ios-briefcase-outline' => 'ios-briefcase-outline'),
         array('ionicons ion-ios-browsers' => 'ios-browsers'),
         array('ionicons ion-ios-browsers-outline' => 'ios-browsers-outline'),
         array('ionicons ion-ios-calculator' => 'ios-calculator'),
         array('ionicons ion-ios-calculator-outline' => 'ios-calculator-outline'),
         array('ionicons ion-ios-calendar' => 'ios-calendar'),
         array('ionicons ion-ios-calendar-outline' => 'ios-calendar-outline'),
         array('ionicons ion-ios-camera' => 'ios-camera'),
         array('ionicons ion-ios-camera-outline' => 'ios-camera-outline'),
         array('ionicons ion-ios-cart' => 'ios-cart'),
         array('ionicons ion-ios-cart-outline' => 'ios-cart-outline'),
         array('ionicons ion-ios-chatboxes' => 'ios-chatboxes'),
         array('ionicons ion-ios-chatboxes-outline' => 'ios-chatboxes-outline'),
         array('ionicons ion-ios-chatbubble' => 'ios-chatbubble'),
         array('ionicons ion-ios-chatbubble-outline' => 'ios-chatbubble-outline'),
         array('ionicons ion-ios-checkmark' => 'ios-checkmark'),
         array('ionicons ion-ios-checkmark-empty' => 'ios-checkmark-empty'),
         array('ionicons ion-ios-checkmark-outline' => 'ios-checkmark-outline'),
         array('ionicons ion-ios-circle-filled' => 'ios-circle-filled'),
         array('ionicons ion-ios-circle-outline' => 'ios-circle-outline'),
         array('ionicons ion-ios-clock' => 'ios-clock'),
         array('ionicons ion-ios-clock-outline' => 'ios-clock-outline'),
         array('ionicons ion-ios-close' => 'ios-close'),
         array('ionicons ion-ios-close-empty' => 'ios-close-empty'),
         array('ionicons ion-ios-close-outline' => 'ios-close-outline'),
         array('ionicons ion-ios-cloud' => 'ios-cloud'),
         array('ionicons ion-ios-cloud-download' => 'ios-cloud-download'),
         array('ionicons ion-ios-cloud-download-outline' => 'ios-cloud-download-outline'),
         array('ionicons ion-ios-cloud-outline' => 'ios-cloud-outline'),
         array('ionicons ion-ios-cloud-upload' => 'ios-cloud-upload'),
         array('ionicons ion-ios-cloud-upload-outline' => 'ios-cloud-upload-outline'),
         array('ionicons ion-ios-cloudy' => 'ios-cloudy'),
         array('ionicons ion-ios-cloudy-night' => 'ios-cloudy-night'),
         array('ionicons ion-ios-cloudy-night-outline' => 'ios-cloudy-night-outline'),
         array('ionicons ion-ios-cloudy-outline' => 'ios-cloudy-outline'),
         array('ionicons ion-ios-cog' => 'ios-cog'),
         array('ionicons ion-ios-cog-outline' => 'ios-cog-outline'),
         array('ionicons ion-ios-color-filter' => 'ios-color-filter'),
         array('ionicons ion-ios-color-filter-outline' => 'ios-color-filter-outline'),
         array('ionicons ion-ios-color-wand' => 'ios-color-wand'),
         array('ionicons ion-ios-color-wand-outline' => 'ios-color-wand-outline'),
         array('ionicons ion-ios-compose' => 'ios-compose'),
         array('ionicons ion-ios-compose-outline' => 'ios-compose-outline'),
         array('ionicons ion-ios-contact' => 'ios-contact'),
         array('ionicons ion-ios-contact-outline' => 'ios-contact-outline'),
         array('ionicons ion-ios-copy' => 'ios-copy'),
         array('ionicons ion-ios-copy-outline' => 'ios-copy-outline'),
         array('ionicons ion-ios-crop' => 'ios-crop'),
         array('ionicons ion-ios-crop-strong' => 'ios-crop-strong'),
         array('ionicons ion-ios-download' => 'ios-download'),
         array('ionicons ion-ios-download-outline' => 'ios-download-outline'),
         array('ionicons ion-ios-drag' => 'ios-drag'),
         array('ionicons ion-ios-email' => 'ios-email'),
         array('ionicons ion-ios-email-outline' => 'ios-email-outline'),
         array('ionicons ion-ios-eye' => 'ios-eye'),
         array('ionicons ion-ios-eye-outline' => 'ios-eye-outline'),
         array('ionicons ion-ios-fastforward' => 'ios-fastforward'),
         array('ionicons ion-ios-fastforward-outline' => 'ios-fastforward-outline'),
         array('ionicons ion-ios-filing' => 'ios-filing'),
         array('ionicons ion-ios-filing-outline' => 'ios-filing-outline'),
         array('ionicons ion-ios-film' => 'ios-film'),
         array('ionicons ion-ios-film-outline' => 'ios-film-outline'),
         array('ionicons ion-ios-flag' => 'ios-flag'),
         array('ionicons ion-ios-flag-outline' => 'flag-outline'),
         array('ionicons ion-ios-flame' => 'ios-flame'),
         array('ionicons ion-ios-flame-outline' => 'ios-flame-outline'),
         array('ionicons ion-ios-flask' => 'ios-flask'),
         array('ionicons ion-ios-flask-outline' => 'flask-outline'),
         array('ionicons ion-ios-flower' => 'ios-flower'),
         array('ionicons ion-ios-flower-outline' => 'ios-flower-outline'),
         array('ionicons ion-ios-folder' => 'ios-folder'),
         array('ionicons ion-ios-folder-outline' => 'ios-folder-outline'),
         array('ionicons ion-ios-football' => 'ios-football'),
         array('ionicons ion-ios-football-outline' => 'ios-football-outline'),
         array('ionicons ion-ios-game-controller-a' => 'ios-game-controller-a'),
         array('ionicons ion-ios-game-controller-a-outline' => 'ios-game-controller-a-outline'),
         array('ionicons ion-ios-game-controller-b' => 'ios-game-controller-b'),
         array('ionicons ion-ios-game-controller-b-outline' => 'ios-game-controller-b-outline'),
         array('ionicons ion-ios-gear' => 'ios-gear'),
         array('ionicons ion-ios-gear-outline' => 'ios-gear-outline'),
         array('ionicons ion-ios-glasses' => 'ios-glasses'),
         array('ionicons ion-ios-glasses-outline' => 'ios-glasses-outline'),
         array('ionicons ion-ios-grid-view' => 'ios-grid-view'),
         array('ionicons ion-ios-grid-view-outline' => 'ios-grid-view-outline'),
         array('ionicons ion-ios-heart' => 'ios-heart'),
         array('ionicons ion-ios-heart-outline' => 'ios-heart-outline'),
         array('ionicons ion-ios-help' => 'ios-help'),
         array('ionicons ion-ios-help-empty' => 'ios-help-empty'),
         array('ionicons ion-ios-help-outline' => 'ios-help-outline'),
         array('ionicons ion-ios-home' => 'ios-home'),
         array('ionicons ion-ios-home-outline' => 'ios-home-outline'),
         array('ionicons ion-ios-infinite' => 'ios-infinite'),
         array('ionicons ion-ios-infinite-outline' => 'ios-infinite-outline'),
         array('ionicons ion-ios-information' => 'ios-information'),
         array('ionicons ion-ios-information-empty' => 'ios-information-empty'),
         array('ionicons ion-ios-information-outline' => 'ios-information-outline'),
         array('ionicons ion-ios-ionic-outline' => 'ios-ionic-outline'),
         array('ionicons ion-ios-keypad' => 'ios-ionic-outline'),
         array('ionicons ion-ios-keypad-outline' => 'ios-keypad-outline'),
         array('ionicons ion-ios-lightbulb' => 'ios-lightbulb'),
         array('ionicons ion-ios-lightbulb-outline' => 'ios-lightbulb-outline'),
         array('ionicons ion-ios-list' => 'ios-list'),
         array('ionicons ion-ios-list-outline' => 'ios-list-outline'),
         array('ionicons ion-ios-location' => 'ios-location'),
         array('ionicons ion-ios-location-outline' => 'ios-location-outline'),
         array('ionicons ion-ios-locked' => 'ios-locked'),
         array('ionicons ion-ios-locked-outline' => 'ios-locked-outline'),
         array('ionicons ion-ios-loop' => 'ios-loop'),
         array('ionicons ion-ios-loop-strong' => 'ios-loop-strong'),
         array('ionicons ion-ios-medical' => 'ios-medical'),
         array('ionicons ion-ios-medical-outline' => 'ios-medical-outline'),
         array('ionicons ion-ios-medkit' => 'ios-medkit'),
         array('ionicons ion-ios-medkit-outline' => 'ios-medkit-outline'),
         array('ionicons ion-ios-mic' => 'ios-mic'),
         array('ionicons ion-ios-mic-off' => 'ios-mic-off'),
         array('ionicons ion-ios-mic-outline' => 'ios-mic-outline'),
         array('ionicons ion-ios-minus' => 'ios-minus'),
         array('ionicons ion-ios-minus-empty' => 'ios-minus-empty'),
         array('ionicons ion-ios-minus-outline' => 'ios-minus-outline'),
         array('ionicons ion-ios-monitor' => 'ios-monitor'),
         array('ionicons ion-ios-monitor-outline' => 'ios-monitor-outline'),
         array('ionicons ion-ios-moon' => 'ios-moon'),
         array('ionicons ion-ios-moon-outline' => 'ios-moon-outline'),
         array('ionicons ion-ios-more' => 'ios-more'),
         array('ionicons ion-ios-more-outline' => 'ios-more-outline'),
         array('ionicons ion-ios-musical-note' => 'ios-musical-note'),
         array('ionicons ion-ios-musical-notes' => 'ios-musical-notes'),
         array('ionicons ion-ios-navigate' => 'ios-navigate'),
         array('ionicons ion-ios-navigate-outline' => 'ios-navigate-outline'),
         array('ionicons ion-ios-nutrition' => 'ios-nutrition'),
         array('ionicons ion-ios-nutrition-outline' => 'nutrition-outline'),
         array('ionicons ion-ios-paper' => 'ios-paper'),
         array('ionicons ion-ios-paper-outline' => 'ios-paper-outline'),
         array('ionicons ion-ios-paperplane' => 'ios-paperplane'),
         array('ionicons ion-ios-paperplane-outline' => 'ios-paperplane-outline'),
         array('ionicons ion-ios-partlysunny' => 'ios-partlysunny'),
         array('ionicons ion-ios-partlysunny-outline' => 'ios-partlysunny-outline'),
         array('ionicons ion-ios-pause' => 'ios-pause'),
         array('ionicons ion-ios-pause-outline' => 'ios-pause-outline'),
         array('ionicons ion-ios-paw' => 'ios-paw'),
         array('ionicons ion-ios-paw-outline' => 'ios-paw-outline'),
         array('ionicons ion-ios-people' => 'ios-people'),
         array('ionicons ion-ios-people-outline' => 'ios-people-outline'),
         array('ionicons ion-ios-person' => 'ios-person'),
         array('ionicons ion-ios-person-outline' => 'ios-person-outline'),
         array('ionicons ion-ios-personadd' => 'ios-personadd'),
         array('ionicons ion-ios-personadd-outline' => 'ios-personadd-outline'),
         array('ionicons ion-ios-photos' => 'ios-photos'),
         array('ionicons ion-ios-photos-outline' => 'ios-photos-outline'),
         array('ionicons ion-ios-pie' => 'ios-pie'),
         array('ionicons ion-ios-pie-outline' => 'ios-pie-outline'),
         array('ionicons ion-ios-pint' => 'ios-pint'),
         array('ionicons ion-ios-pint-outline' => 'pint-outline'),
         array('ionicons ion-ios-play' => 'ios-play'),
         array('ionicons ion-ios-play-outline' => 'ios-play-outline'),
         array('ionicons ion-ios-plus' => 'ios-plus'),
         array('ionicons ion-ios-plus-empty' => 'ios-plus-empty'),
         array('ionicons ion-ios-plus-outline' => 'ios-plus-outline'),
         array('ionicons ion-ios-pricetag' => 'ios-pricetag'),
         array('ionicons ion-ios-pricetag-outline' => 'ios-pricetag-outline'),
         array('ionicons ion-ios-pricetags' => 'ios-pricetags'),
         array('ionicons ion-ios-pricetags-outline' => 'pricetags-outline'),
         array('ionicons ion-ios-printer' => 'ios-printer'),
         array('ionicons ion-ios-printer-outline' => 'printer-outline'),
         array('ionicons ion-ios-pulse' => 'ios-pulse'),
         array('ionicons ion-ios-pulse-strong' => 'ios-pulse-strong'),
         array('ionicons ion-ios-rainy' => 'ios-rainy'),
         array('ionicons ion-ios-rainy-outline' => 'ios-rainy-outline'),
         array('ionicons ion-ios-recording' => 'ios-recording'),
         array('ionicons ion-ios-recording-outline' => 'ios-recording-outline'),
         array('ionicons ion-ios-redo' => 'ios-redo'),
         array('ionicons ion-ios-redo-outline' => 'ios-redo-outline'),
         array('ionicons ion-ios-refresh' => 'ios-refresh'),
         array('ionicons ion-ios-refresh-empty' => 'ios-refresh-empty'),
         array('ionicons ion-ios-refresh-outline' => 'ios-refresh-outline'),
         array('ionicons ion-ios-reload' => 'ios-reload'),
         array('ionicons ion-ios-reverse-camera' => 'ios-reverse-camera'),
         array('ionicons ion-ios-reverse-camera-outline' => 'ios-reverse-camera-outline'),
         array('ionicons ion-ios-rewind' => 'ios-rewind'),
         array('ionicons ion-ios-rewind-outline' => 'ios-rewind-outline'),
         array('ionicons ion-ios-rose' => 'ios-rose'),
         array('ionicons ion-ios-rose-outline' => 'ios-rose-outline'),
         array('ionicons ion-ios-search' => 'ios-search'),
         array('ionicons ion-ios-search-strong' => 'ios-search-strong'),
         array('ionicons ion-ios-settings' => 'ios-settings'),
         array('ionicons ion-ios-settings-strong' => 'ios-settings-strong'),
         array('ionicons ion-ios-shuffle' => 'ios-shuffle'),
         array('ionicons ion-ios-shuffle-strong' => 'ios-shuffle-strong'),
         array('ionicons ion-ios-skipbackward' => 'ios-skipbackward'),
         array('ionicons ion-ios-skipbackward-outline' => 'ios-skipbackward-outline'),
         array('ionicons ion-ios-skipforward' => 'ios-skipforward'),
         array('ionicons ion-ios-skipforward-outline' => 'ios-skipforward-outline'),
         array('ionicons ion-ios-snowy' => 'ios-snowy'),
         array('ionicons ion-ios-speedometer' => 'ios-speedometer'),
         array('ionicons ion-ios-speedometer-outline' => 'ios-speedometer-outline'),
         array('ionicons ion-ios-star' => 'ios-star'),
         array('ionicons ion-ios-star-half' => 'ios-star-half'),
         array('ionicons ion-ios-star-outline' => 'ios-star-outline'),
         array('ionicons ion-ios-stopwatch' => 'ios-stopwatch'),
         array('ionicons ion-ios-stopwatch-outline' => 'ios-stopwatch-outline'),
         array('ionicons ion-ios-sunny' => 'ios-sunny'),
         array('ionicons ion-ios-sunny-outline' => 'ios-sunny-outline'),
         array('ionicons ion-ios-telephone' => 'ios-telephone'),
         array('ionicons ion-ios-telephone-outline' => 'ios-telephone-outline'),
         array('ionicons ion-ios-tennisball' => 'ios-tennisball'),
         array('ionicons ion-ios-tennisball-outline' => 'ios-tennisball-outline'),
         array('ionicons ion-ios-thunderstorm' => 'ios-thunderstorm'),
         array('ionicons ion-ios-thunderstorm-outline' => 'ios-thunderstorm-outline'),
         array('ionicons ion-ios-time' => 'ios-time'),
         array('ionicons ion-ios-time-outline' => 'time-outline'),
         array('ionicons ion-ios-timer' => 'ios-timer'),
         array('ionicons ion-ios-timer-outline' => 'ios-timer-outline'),
         array('ionicons ion-ios-toggle' => 'ios-toggle'),
         array('ionicons ion-ios-toggle-outline' => 'toggle-outline'),
         array('ionicons ion-ios-trash' => 'ios-trash'),
         array('ionicons ion-ios-trash-outline' => 'trash-outline'),
         array('ionicons ion-ios-undo' => 'ios-undo'),
         array('ionicons ion-ios-undo-outline' => 'ios-undo-outline'),
         array('ionicons ion-ios-unlocked' => 'ios-unlocked'),
         array('ionicons ion-ios-unlocked-outline' => 'ios-unlocked-outline'),
         array('ionicons ion-ios-upload' => 'ios-upload'),
         array('ionicons ion-ios-upload-outline' => 'ios-upload-outline'),
         array('ionicons ion-ios-videocam' => 'ios-videocam'),
         array('ionicons ion-ios-videocam-outline' => 'ios-videocam-outline'),
         array('ionicons ion-ios-volume-high' => 'ios-volume-high'),
         array('ionicons ion-ios-volume-low' => 'ios-volume-low'),
         array('ionicons ion-ios-wineglass' => 'ios-wineglass'),
         array('ionicons ion-ios-wineglass-outline' => 'ios-wineglass-outline'),
         array('ionicons ion-ios-world' => 'ios-world'),
         array('ionicons ion-ios-world-outline' => 'ios-world-outline'),
         array('ionicons ion-ipad' => 'ipad'),
         array('ionicons ion-iphone' => 'iphone'),
         array('ionicons ion-ipod' => 'ipod'),
         array('ionicons ion-jet' => 'jet'),
         array('ionicons ion-key' => 'key'),
         array('ionicons ion-knife' => 'knife'),
         array('ionicons ion-laptop' => 'laptop'),
         array('ionicons ion-leaf' => 'leaf'),
         array('ionicons ion-levels' => 'levels'),
         array('ionicons ion-lightbulb' => 'lightbulb'),
         array('ionicons ion-link' => 'link'),
         array('ionicons ion-load-a' => 'load-a'),
         array('ionicons ion-load-b' => 'load-b'),
         array('ionicons ion-load-c' => 'load-c'),
         array('ionicons ion-load-d' => 'load-d'),
         array('ionicons ion-location' => 'location'),
         array('ionicons ion-lock-combination' => 'lock-combination'),
         array('ionicons ion-locked' => 'locked'),
         array('ionicons ion-log-in' => 'log-in'),
         array('ionicons ion-log-out' => 'log-out'),
         array('ionicons ion-loop' => 'loop'),
         array('ionicons ion-magnet' => 'magnet'),
         array('ionicons ion-male' => 'male'),
         array('ionicons ion-man' => 'man'),
         array('ionicons ion-map' => 'map'),
         array('ionicons ion-medkit' => 'medkit'),
         array('ionicons ion-merge' => 'merge'),
         array('ionicons ion-mic-a' => 'mic-a'),
         array('ionicons ion-mic-b' => 'mic-b'),
         array('ionicons ion-mic-c' => 'mic-c'),
         array('ionicons ion-minus' => 'minus'),
         array('ionicons ion-minus-circled' => 'minus-circled'),
         array('ionicons ion-minus-round' => 'minus-round'),
         array('ionicons ion-model-s' => 'model-s'),
         array('ionicons ion-monitor' => 'monitor'),
         array('ionicons ion-more' => 'more'),
         array('ionicons ion-mouse' => 'mouse'),
         array('ionicons ion-music-note' => 'music-note'),
         array('ionicons ion-navicon' => 'navicon'),
         array('ionicons ion-navicon-round' => 'navicon-round'),
         array('ionicons ion-navigate' => 'navigate'),
         array('ionicons ion-network' => 'network'),
         array('ionicons ion-no-smoking' => 'no-smoking'),
         array('ionicons ion-nuclear' => 'nuclear'),
         array('ionicons ion-outlet' => 'outlet'),
         array('ionicons ion-paintbrush' => 'paintbrush'),
         array('ionicons ion-paintbucket' => 'paintbucket'),
         array('ionicons ion-paper-airplane' => 'paper-airplane'),
         array('ionicons ion-paperclip' => 'paperclip'),
         array('ionicons ion-pause' => 'pause'),
         array('ionicons ion-person' => 'person'),
         array('ionicons ion-person-add' => 'person-add'),
         array('ionicons ion-person-stalker' => 'person-stalker'),
         array('ionicons ion-pie-graph' => 'pie-graph'),
         array('ionicons ion-pin' => 'pin'),
         array('ionicons ion-pinpoint' => 'pinpoint'),
         array('ionicons ion-pizza' => 'pizza'),
         array('ionicons ion-plane' => 'plane'),
         array('ionicons ion-planet' => 'planet'),
         array('ionicons ion-play' => 'play'),
         array('ionicons ion-playstation' => 'playstation'),
         array('ionicons ion-plus' => 'plus'),
         array('ionicons ion-plus-circled' => 'plus-circled'),
         array('ionicons ion-plus-round' => 'plus-round'),
         array('ionicons ion-podium' => 'podium'),
         array('ionicons ion-pound' => 'pound'),
         array('ionicons ion-power' => 'power'),
         array('ionicons ion-pricetag' => 'pricetag'),
         array('ionicons ion-pricetags' => 'pricetags'),
         array('ionicons ion-printer' => 'printer'),
         array('ionicons ion-pull-request' => 'pull-request'),
         array('ionicons ion-qr-scanner' => 'qr-scanner'),
         array('ionicons ion-quote' => 'quote'),
         array('ionicons ion-radio-waves' => 'radio-waves'),
         array('ionicons ion-record' => 'record'),
         array('ionicons ion-refresh' => 'refresh'),
         array('ionicons ion-reply' => 'reply'),
         array('ionicons ion-reply-all' => 'reply-all'),
         array('ionicons ion-ribagro-a' => 'ribagro-a'),
         array('ionicons ion-ribagro-b' => 'ribagro-b'),
         array('ionicons ion-sad' => 'sad'),
         array('ionicons ion-sad-outline' => 'sad-outline'),
         array('ionicons ion-scissors' => 'scissors'),
         array('ionicons ion-search' => 'search'),
         array('ionicons ion-settings' => 'settings'),
         array('ionicons ion-share' => 'share'),
         array('ionicons ion-shuffle' => 'shuffle'),
         array('ionicons ion-skip-backward' => 'skip-backward'),
         array('ionicons ion-skip-forward' => 'skip-forward'),
         array('ionicons ion-social-android' => 'social-android'),
         array('ionicons ion-social-android-outline' => 'social-android-outline'),
         array('ionicons ion-social-angular' => 'social-angular'),
         array('ionicons ion-social-angular-outline' => 'social-angular-outline'),
         array('ionicons ion-social-apple' => 'social-apple'),
         array('ionicons ion-social-apple-outline' => 'social-apple-outline'),
         array('ionicons ion-social-bitcoin' => 'social-bitcoin'),
         array('ionicons ion-social-bitcoin-outline' => 'social-bitcoin-outline'),
         array('ionicons ion-social-buffer' => 'social-buffer'),
         array('ionicons ion-social-buffer-outline' => 'social-buffer-outline'),
         array('ionicons ion-social-chrome' => 'social-chrome'),
         array('ionicons ion-social-chrome-outline' => 'social-chrome-outline'),
         array('ionicons ion-social-codepen' => 'social-codepen'),
         array('ionicons ion-social-codepen-outline' => 'social-codepen-outline'),
         array('ionicons ion-social-css3' => 'social-css3'),
         array('ionicons ion-social-css3-outline' => 'social-css3-outline'),
         array('ionicons ion-social-designernews' => 'social-designernews'),
         array('ionicons ion-social-designernews-outline' => 'social-designernews-outline'),
         array('ionicons ion-social-dribbble' => 'social-dribbble'),
         array('ionicons ion-social-dribbble-outline' => 'social-dribbble-outline'),
         array('ionicons ion-social-dropbox' => 'social-dropbox'),
         array('ionicons ion-social-dropbox-outline' => 'social-dropbox-outline'),
         array('ionicons ion-social-euro' => 'social-euro'),
         array('ionicons ion-social-euro-outline' => 'social-euro-outline'),
         array('ionicons ion-social-facebook' => 'social-facebook'),
         array('ionicons ion-social-facebook-outline' => 'social-facebook-outline'),
         array('ionicons ion-social-foursquare' => 'social-foursquare'),
         array('ionicons ion-social-foursquare-outline' => 'social-foursquare-outline'),
         array('ionicons ion-social-freebsd-devil' => 'social-freebsd-devil'),
         array('ionicons ion-social-github' => 'social-github'),
         array('ionicons ion-social-github-outline' => 'social-github-outline'),
         array('ionicons ion-social-google' => 'social-google'),
         array('ionicons ion-social-google-outline' => 'social-google-outline'),
         array('ionicons ion-social-googleplus' => 'social-googleplus'),
         array('ionicons ion-social-googleplus-outline' => 'social-googleplus-outline'),
         array('ionicons ion-social-hackernews' => 'social-hackernews'),
         array('ionicons ion-social-hackernews-outline' => 'social-hackernews-outline'),
         array('ionicons ion-social-html5' => 'social-html5'),
         array('ionicons ion-social-html5-outline' => 'social-html5-outline'),
         array('ionicons ion-social-instagram' => 'social-instagram'),
         array('ionicons ion-social-instagram-outline' => 'social-instagram-outline'),
         array('ionicons ion-social-javascript' => 'social-javascript'),
         array('ionicons ion-social-javascript-outline' => 'social-javascript-outline'),
         array('ionicons ion-social-linkedin' => 'social-linkedin'),
         array('ionicons ion-social-linkedin-outline' => 'social-linkedin-outline'),
         array('ionicons ion-social-markdown' => 'social-markdown'),
         array('ionicons ion-social-nodejs' => 'social-nodejs'),
         array('ionicons ion-social-octocat' => 'social-octocat'),
         array('ionicons ion-social-pinterest' => 'social-pinterest'),
         array('ionicons ion-social-pinterest-outline' => 'social-pinterest-outline'),
         array('ionicons ion-social-python' => 'social-python'),
         array('ionicons ion-social-reddit' => 'social-reddit'),
         array('ionicons ion-social-reddit-outline' => 'social-reddit-outline'),
         array('ionicons ion-social-rss' => 'social-rss'),
         array('ionicons ion-social-rss-outline' => 'social-rss-outline'),
         array('ionicons ion-social-sass' => 'social-sass'),
         array('ionicons ion-social-skype' => 'social-skype'),
         array('ionicons ion-social-skype-outline' => 'social-skype-outline'),
         array('ionicons ion-social-snapchat' => 'social-snapchat'),
         array('ionicons ion-social-snapchat-outline' => 'social-snapchat-outline'),
         array('ionicons ion-social-tumblr' => 'social-tumblr'),
         array('ionicons ion-social-tumblr-outline' => 'social-tumblr-outline'),
         array('ionicons ion-social-tux' => 'social-tux'),
         array('ionicons ion-social-twitch' => 'social-twitch'),
         array('ionicons ion-social-twitch-outline' => 'social-twitch-outline'),
         array('ionicons ion-social-twitter' => 'social-twitter'),
         array('ionicons ion-social-twitter-outline' => 'social-twitter-outline'),
         array('ionicons ion-social-usd' => 'social-usd'),
         array('ionicons ion-social-usd-outline' => 'social-usd-outline'),
         array('ionicons ion-social-vimeo' => 'social-vimeo'),
         array('ionicons ion-social-vimeo-outline' => 'social-vimeo-outline'),
         array('ionicons ion-social-whatsapp' => 'social-whatsapp'),
         array('ionicons ion-social-whatsapp-outline' => 'social-whatsapp-outline'),
         array('ionicons ion-social-windows' => 'social-windows'),
         array('ionicons ion-social-windows-outline' => 'social-windows-outline'),
         array('ionicons ion-social-wordpress' => 'social-wordpress'),
         array('ionicons ion-social-wordpress-outline' => 'social-wordpress-outline'),
         array('ionicons ion-social-yahoo' => 'social-yahoo'),
         array('ionicons ion-social-yahoo-outline' => 'social-yahoo-outline'),
         array('ionicons ion-social-yen' => 'social-yen'),
         array('ionicons ion-social-yen-outline' => 'social-yen-outline'),
         array('ionicons ion-social-youtube' => 'social-youtube'),
         array('ionicons ion-social-youtube-outline' => 'social-youtube-outline'),
         array('ionicons ion-soup-can' => 'soup-can'),
         array('ionicons ion-soup-can-outline' => 'soup-can-outline'),
         array('ionicons ion-speakerphone' => 'speakerphone'),
         array('ionicons ion-speedometer' => 'speedometer'),
         array('ionicons ion-spoon' => 'spoon'),
         array('ionicons ion-star' => 'star'),
         array('ionicons ion-stats-bars' => 'stats-bars'),
         array('ionicons ion-steam' => 'steam'),
         array('ionicons ion-stop' => 'stop'),
         array('ionicons ion-thermometer' => 'thermometer'),
         array('ionicons ion-thumbsdown' => 'thumbsdown'),
         array('ionicons ion-thumbsup' => 'thumbsup'),
         array('ionicons ion-toggle' => 'toggle'),
         array('ionicons ion-toggle-filled' => 'toggle-filled'),
         array('ionicons ion-transgender' => 'transgender'),
         array('ionicons ion-trash-a' => 'trash-a'),
         array('ionicons ion-trash-b' => 'trash-b'),
         array('ionicons ion-trophy' => 'trophy'),
         array('ionicons ion-tshirt' => 'tshirt'),
         array('ionicons ion-tshirt-outline' => 'tshirt-outline'),
         array('ionicons ion-umbrella' => 'umbrella'),
         array('ionicons ion-university' => 'university'),
         array('ionicons ion-unlocked' => 'unlocked'),
         array('ionicons ion-upload' => 'upload'),
         array('ionicons ion-usb' => 'usb'),
         array('ionicons ion-videocamera' => 'videocamera'),
         array('ionicons ion-volume-high' => 'volume-high'),
         array('ionicons ion-volume-low' => 'volume-low'),
         array('ionicons ion-volume-medium' => 'volume-medium'),
         array('ionicons ion-volume-mute' => 'volume-mute'),
         array('ionicons ion-wand' => 'wand'),
         array('ionicons ion-waterdrop' => 'waterdrop'),
         array('ionicons ion-wifi' => 'wifi'),
         array('ionicons ion-wineglass' => 'wineglass'),
         array('ionicons ion-woman' => 'woman'),
         array('ionicons ion-wrench' => 'wrench'),
         array('ionicons ion-xbox' => 'xbox'),
        );
    }
    add_filter('vc_iconpicker-type-ionicons', 'agro_icon_array');
    // Add new custom font to Font Family selection in icon box module
    function agro_add_flaticon()
    {
        $param = WPBMap::getParam('vc_icon', 'type');
        $param['value'][esc_html__('Flaticon', 'agro')] = 'flaticon';
        vc_update_shortcode_param('vc_icon', $param);
    }
    add_filter('init', 'agro_add_flaticon', 40);
    // Add font picker setting to icon box module when you select your font family from the dropdown
    function agro_add_font_flaticon_picker()
    {
        vc_add_param(
            'vc_icon',
            array(
                'type' => 'iconpicker',
                'weight' => 1,
                'heading' => esc_html__('Icon', 'agro'),
                'param_name' => 'icon_flaticon',
                'settings' => array(
                    'emptyIcon' => false,
                    'type' => 'flaticon',
                    'iconsPerPage' => 200,
                ),
                'dependency' => array(
                    'element' => 'type',
                    'value' => 'flaticon',
                ),
            )
        );
    }
    add_filter('vc_after_init', 'agro_add_font_flaticon_picker', 40);
    function agro_flaticonicon_array()
    {
        return array(
        array('flaticon-align' => 'align'),
        array('flaticon-align-1' => 'align-1'),
        array('flaticon-anchor' => 'anchor'),
        array('flaticon-background' => 'background'),
        array('flaticon-background-1' => 'background-1'),
        array('flaticon-background-2' => 'background-2'),
        array('flaticon-bounding-box' => 'bounding-box'),
        array('flaticon-brush' => 'brush'),
        array('flaticon-bucket' => 'bucket'),
        array('flaticon-bucket-1' => 'bucket-1'),
        array('flaticon-center-align' => 'center-align'),
        array('flaticon-center-alignment' => 'center-alignment'),
        array('flaticon-center-alignment-1' => 'center-alignment-1'),
        array('flaticon-circle' => 'circle'),
        array('flaticon-circular' => 'circular'),
        array('flaticon-column' => 'column'),
        array('flaticon-cone' => 'cone'),
        array('flaticon-constraint' => 'constraint'),
        array('flaticon-coordinates' => 'coordinates'),
        array('flaticon-crop' => 'crop'),
        array('flaticon-crop-1' => 'crop-1'),
        array('flaticon-cube' => 'cube'),
        array('flaticon-cube-1' => 'cube-1'),
        array('flaticon-cube-2' => 'cube-2'),
        array('flaticon-cube-3' => 'cube-3'),
        array('flaticon-cube-4' => 'cube-4'),
        array('flaticon-cube-5' => 'cube-5'),
        array('flaticon-cursor' => 'cursor'),
        array('flaticon-cursor-1' => 'cursor-1'),
        array('flaticon-cursor-2' => 'cursor-2'),
        array('flaticon-customer-support' => 'customer-support'),
        array('flaticon-cylinder' => 'cylinder'),
        array('flaticon-distort' => 'distort'),
        array('flaticon-distort-1' => 'distort-1'),
        array('flaticon-divide' => 'divide'),
        array('flaticon-edit' => 'edit'),
        array('flaticon-edit-1' => 'edit-1'),
        array('flaticon-edit-2' => 'edit-2'),
        array('flaticon-edit-3' => 'edit-3'),
        array('flaticon-edit-4' => 'edit-4'),
        array('flaticon-edit-corner' => 'edit-corner'),
        array('flaticon-exclude' => 'exclude'),
        array('flaticon-eyedropper' => 'eyedropper'),
        array('flaticon-eyedropper-1' => 'eyedropper-1'),
        array('flaticon-flatten' => 'flatten'),
        array('flaticon-flip' => 'flip'),
        array('flaticon-flip-1' => 'flip-1'),
        array('flaticon-foreground' => 'foreground'),
        array('flaticon-grids' => 'grids'),
        array('flaticon-group' => 'group'),
        array('flaticon-intersection' => 'intersection'),
        array('flaticon-joint' => 'joint'),
        array('flaticon-left-alignment' => 'left-alignment'),
        array('flaticon-left-alignment-1' => 'left-alignment-1'),
        array('flaticon-left-alignment-2' => 'left-alignment-2'),
        array('flaticon-merge' => 'merge'),
        array('flaticon-mirror-horizontally' => 'mirror-horizontally'),
        array('flaticon-mirror-horizontally-1' => 'mirror-horizontally-1'),
        array('flaticon-move' => 'move'),
        array('flaticon-outline' => 'outline'),
        array('flaticon-oval' => 'oval'),
        array('flaticon-oval-1' => 'oval-1'),
        array('flaticon-oval-2' => 'oval-2'),
        array('flaticon-paint-palette' => 'paint-palette'),
        array('flaticon-pen' => 'pen'),
        array('flaticon-pen-1' => 'pen-1'),
        array('flaticon-perspective' => 'perspective'),
        array('flaticon-polygon' => 'polygon'),
        array('flaticon-right-alignment' => 'right-alignment'),
        array('flaticon-right-alignment-1' => 'right-alignment-1'),
        array('flaticon-right-alignment-2' => 'right-alignment-2'),
        array('flaticon-rotate' => 'rotate'),
        array('flaticon-rotate-1' => 'rotate-1'),
        array('flaticon-row' => 'row'),
        array('flaticon-ruler' => 'ruler'),
        array('flaticon-ruler-1' => 'ruler-1'),
        array('flaticon-scale' => 'scale'),
        array('flaticon-select' => 'select'),
        array('flaticon-sent' => 'sent'),
        array('flaticon-sent-1' => 'sent-1'),
        array('flaticon-shapes' => 'shapes'),
        array('flaticon-shapes-1' => 'shapes-1'),
        array('flaticon-spiral' => 'spiral'),
        array('flaticon-spray' => 'spray'),
        array('flaticon-square' => 'square'),
        array('flaticon-square-1' => 'square-1'),
        array('flaticon-square-10' => 'square-10'),
        array('flaticon-square-11' => 'square-11'),
        array('flaticon-square-12' => 'square-12'),
        array('flaticon-square-13' => 'square-13'),
        array('flaticon-square-14' => 'square-14'),
        array('flaticon-square-15' => 'square-15'),
        array('flaticon-square-16' => 'square-16'),
        array('flaticon-square-17' => 'square-17'),
        array('flaticon-square-18' => 'square-18'),
        array('flaticon-square-19' => 'square-19'),
        array('flaticon-square-2' => 'square-2'),
        array('flaticon-square-20' => 'square-20'),
        array('flaticon-square-21' => 'square-21'),
        array('flaticon-square-22' => 'square-22'),
        array('flaticon-square-23' => 'square-23'),
        array('flaticon-square-24' => 'square-24'),
        array('flaticon-square-25' => 'square-25'),
        array('flaticon-square-26' => 'square-26'),
        array('flaticon-square-27' => 'square-27'),
        array('flaticon-square-3' => 'square-3'),
        array('flaticon-square-4' => 'square-4'),
        array('flaticon-square-5' => 'square-5'),
        array('flaticon-square-6' => 'square-6'),
        array('flaticon-square-7' => 'square-7'),
        array('flaticon-square-8' => 'square-8'),
        array('flaticon-square-9' => 'square-9'),
        array('flaticon-tile' => 'tile'),
        array('flaticon-tile-1' => 'tile-1'),
        array('flaticon-transform' => 'transform'),
        array('flaticon-triangle' => 'triangle'),
        array('flaticon-triangle-1' => 'triangle-1'),
        array('flaticon-trim' => 'trim'),
        array('flaticon-unconstrained' => 'unconstrained'),
        array('flaticon-ungroup' => 'ungroup'),
        array('flaticon-unite' => 'unite'),
        array('flaticon-vector' => 'vector'),
        array('flaticon-vector-1' => 'vector-1'),
        array('flaticon-vector-2' => 'vector-2'),
        array('flaticon-vector-3' => 'vector-3'),
        array('flaticon-vertical-alignment' => 'vertical-alignment'),
        array('flaticon-vertical-alignment-1' => 'vertical-alignment-1'),
        array('flaticon-vertical-alignment-2' => 'vertical-alignment-2'),
        array('flaticon-vertical-alignment-3' => 'vertical-alignment-3'),
        array('flaticon-vertical-alignment-4' => 'vertical-alignment-4'),
        array('flaticon-vertical-alignment-5' => 'vertical-alignment-5'),
        array('flaticon-vertical-alignment-6' => 'vertical-alignment-6'),
        array('flaticon-scroll' => 'scroll'),
        array('flaticon-feature' => 'feature'),
        array('flaticon-intersect' => 'intersect'),
        array('flaticon-union' => 'union'),
        array('flaticon-rgb' => 'rgb'),
        array('flaticon-rgb-1' => 'rgb-1'),
        array('flaticon-resolution' => 'resolution'),
        array('flaticon-showcase' => 'showcase'),
        array('flaticon-tickets' => 'tickets'),
        array('flaticon-ticket' => 'ticket'),
        array('flaticon-conversation' => 'conversation'),
        array('flaticon-speech-bubble' => 'speech-bubble'),
        array('flaticon-email' => 'email'),
        array('flaticon-contact' => 'contact'),
        array('flaticon-file' => 'file'),
        array('flaticon-upload-information' => 'upload-information'),
        );
    }
    add_filter('vc_iconpicker-type-flaticon', 'agro_flaticonicon_array');

  /**  Register Backend and Frontend CSS Styles */
    add_action('vc_base_register_front_css', 'agro_vc_shortcodeicon_base_register_css');
    add_action('vc_base_register_admin_css', 'agro_vc_shortcodeicon_base_register_css');
    function agro_vc_shortcodeicon_base_register_css()
    {
        wp_register_style('nt-vc-shortcode-icon', get_template_directory_uri() . '/vc_templates/vc-custom-css/nt-shortcode.css');
    }
  /** Enqueue Backend and Frontend CSS Styles  */
    add_action('vc_backend_editor_enqueue_js_css', 'agro_vc_shortcodeicon_editor_jscss');
    add_action('vc_frontend_editor_enqueue_js_css', 'agro_vc_shortcodeicon_editor_jscss');
    function agro_vc_shortcodeicon_editor_jscss()
    {
        wp_enqueue_style('nt-vc-shortcode-icon');
    }


    /**  Register Backend and Frontend CSS Styles */
    add_action('vc_base_register_front_css', 'agro_vc_iconpicker_base_register_css');
    add_action('vc_base_register_admin_css', 'agro_vc_iconpicker_base_register_css');
    function agro_vc_iconpicker_base_register_css()
    {
        wp_register_style('ionicons', get_template_directory_uri() . '/css/ionicons.min.css');
        wp_register_style('flaticon', get_template_directory_uri() . '/css/flaticon.css');
    }

    /** Enqueue Backend and Frontend CSS Styles  */
    add_action('vc_backend_editor_enqueue_js_css', 'agro_vc_iconpicker_editor_jscss');
    add_action('vc_frontend_editor_enqueue_js_css', 'agro_vc_iconpicker_editor_jscss');
    function agro_vc_iconpicker_editor_jscss()
    {
        wp_enqueue_style('ionicons');
        wp_enqueue_style('flaticon');
    }

    /**  Enqueue CSS in Frontend when it's used  */
    add_action('vc_enqueue_font_icon_element', 'agro_enqueue_font_ionicons');
    function agro_enqueue_font_ionicons($font)
    {
        switch ($font) {
      case 'ionicons':
            wp_enqueue_style('ionicons');
            break;
            case 'flaticon':
            wp_enqueue_style('flaticon');
        }
    }
