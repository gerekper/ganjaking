<?php

if ( ( ! empty( $atts['spacing'] ) || ( isset( $atts['spacing'] ) && '0' == $atts['spacing'] ) ) && ! empty( $atts['selector'] ) ) {
    echo '.' . sanitize_text_field( $atts['selector'] ) . ' i {';
    echo 'margin-' . ( isset( $atts['icon_pos'] ) && 'right' == $atts['icon_pos'] && ! is_rtl() ? 'left' : 'right' ) . ':' . esc_attr( $atts['spacing'] ) . ';';
    echo '}';
}
