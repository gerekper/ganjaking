<?php

echo porto_filter_output( $atts['selector'] ) . '{margin-' . ( is_rtl() ? 'right' : 'left' ) . ':' . esc_html( $atts['spacing'] ) . '}';
