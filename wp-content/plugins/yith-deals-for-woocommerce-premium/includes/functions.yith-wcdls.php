<?php

function yith_wcdls_restriction_type() {

    $condition = array(
        'default'                  => esc_html__('Type of restriction:','yith-deals-for-woocommerce'),
        'include_or'        => esc_html__('Include at least one of','yith-deals-for-woocommerce'),
        'include_and'       => esc_html__('Include all','yith-deals-for-woocommerce'),
        'exclude_or'        => esc_html__('Does not contain','yith-deals-for-woocommerce'),
    );

    return apply_filters('yith_wcdls_include_exclude',$condition);
}

function yith_wcdls_price_order() {

    $type_price = array(
        'less_than'               => esc_html__('Less than','yith-deals-for-woocommerce'),
        'less_or_equal'      => esc_html__('Less than or equal to','yith-deals-for-woocommerce'),
        'equal'             => esc_html__('Equal to','yith-deals-for-woocommerce'),
        'greater_or_equal'      => esc_html__('Greater than or equal to','yith-deals-for-woocommerce'),
        'greater_than'               => esc_html__('Greater than','yith-deals-for-woocommerce'),
    );

    return apply_filters('yith_wcdls_price_order',$type_price);
}


function yith_wcdls_get_dropdown($args = array()){

    $default_args = array(
        'id'        => '',
        'name'      => '',
        'class'     => '',
        'style'     => '',
        'options'   => array(),
        'value'     => '',
        'disabled'  => '',
        'multiple' => '',
        'echo'      => false,
        'custom-attributes' => array(),
    );

    $args = wp_parse_args( $args, $default_args );
    $custom_attributes = array();
    foreach ( $args[ 'custom-attributes' ] as $attribute => $attribute_value ) {
        $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
    }
    $custom_attributes = implode( ' ', $custom_attributes );
    extract( $args );
    /**
     * @var string $id
     * @var string $name
     * @var string $class
     * @var string $style
     * @var array  $options
     * @var string $value
     * @var bool   $echo
     * @var string $disabled
     */
    $html = "<select id='$id' name='$name' class='$class' $multiple style='$style' $custom_attributes>";

    foreach ( $options as $option_key => $option_label ) {
        $selected = selected( $option_key == $value, true, false );
        $disabled = disabled( $option_key == $disabled,true,false );
        $html .= "<option value='$option_key' $selected $disabled >$option_label</option>";
    }

    $html .= "</select>";

    if ( $echo ) {
        echo $html;
    } else {
        return $html;
    }
}

function yith_wcdls_get_dropdown_multiple($args = array()){

    $default_args = array(
        'id'        => '',
        'name'      => '',
        'class'     => '',
        'style'     => '',
        'options'   => array(),
        'value'     => '',
        'multiple'  => 'multiple',
        'disabled'  => '',
        'echo'      => false,
        'custom-attributes' => array()
    );

    $args = wp_parse_args( $args, $default_args );
    $custom_attributes = array();
    foreach ( $args[ 'custom-attributes' ] as $attribute => $attribute_value ) {
        $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
    }
    $custom_attributes = implode( ' ', $custom_attributes );
    extract( $args );
    /**
     * @var string $id
     * @var string $name
     * @var string $class
     * @var string $style
     * @var array  $options
     * @var string $value
     * @var bool   $echo
     */
    $html = "<select id='$id' name='$name' class='$class' $multiple style='$style' $custom_attributes>";
    foreach ( $options as $option_key => $option_label ) {
        $selected =  is_array($value) ? selected( in_array($option_key,$value), true, false ):'';
        $disabled = disabled( $option_key == $disabled,true,false );
        $html .= "<option value='$option_key' $selected >$option_label</option>";
    }
    $html .= "</select>";

    if ( $echo ) {
        echo $html;
    } else {
        return $html;
    }
}