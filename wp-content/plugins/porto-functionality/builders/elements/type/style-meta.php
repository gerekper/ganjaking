<?php

$pos = empty( $atts['icon_pos'] ) ? 'right' : 'left';
if ( is_rtl() ) {
	$pos = empty( $atts['icon_pos'] ) ? 'left' : 'right';
}

echo porto_filter_output( $atts['selector'] ) . '{margin-' . $pos . ':' . intval( $atts['spacing'] ) . 'px}';
