<?php

function is_checked_html ( $options, $value ) {
    echo ( isset( $options[ $value ] ) || ( is_array ( $options ) && array_key_exists ( $value, $options ) ) ) ? "checked" : '';
}

function is_option_selected_html ( $id, $carrier_name ) {
    echo ( $id === $carrier_name ) ? selected ( 1 ) : '';
}