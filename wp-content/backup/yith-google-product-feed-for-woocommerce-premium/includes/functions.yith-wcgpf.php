<?php

/**
 * Construc a get a select dropdown
 */

function yith_wcgpf_get_dropdown($args = array()){
    $default_args = array(
        'id'      => '',
        'name'    => '',
        'class'   => '',
        'style'   => '',
        'options' => array(),
        'value'   => '',
        'echo'    => false
    );

    $args = wp_parse_args( $args, $default_args );
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
    $html = "<select id='$id' name='$name' class='$class' style='$style'>";

    foreach ( $options as $option_key => $option_label ) {
        $selected = selected( $option_key == $value, true, false );
        $html .= "<option value='$option_key' $selected>$option_label</option>";
    }

    $html .= "</select>";

    if ( $echo ) {
        echo $html;
    } else {
        return $html;
    }
}

/**
 * Construc a get a input
 */
function yith_wcgpf_get_input($args = array()) {
    $default_args = array(
        'id'      => '',
        'name'    => '',
        'class'   => '',
        'type'    => '',
       //'checked' => '',
        'value'   => '',
        'echo'    => false
    );

    $args = wp_parse_args( $args, $default_args );
    extract( $args );
    /**
     * @var string $id
     * @var string $name
     * @var string $class
     * @var string $type
     * @var string $checked
     * @var string $value
     * @var bool   $echo
     */
    $value = esc_html($value);
    $html = "<input type='$type' id='$id' name='$name' class='$class' value='$value'>";

    if ( $echo ) {
        echo $html;
    } else {
        return $html;
    }
}