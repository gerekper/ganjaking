<?php

if ( ! empty( $step_item_list ) ) {
	foreach ( $step_item_list as $key => $atts ) {
		if ( $template = porto_shortcode_template( 'porto_schedule_timeline_item' ) ) {
			$content           = $atts['content'];
			$atts['item_type'] = $type;
			if ( is_array( $atts['image_id'] ) && ! empty( $atts['image_id']['id'] ) ) {
				$atts['image_id']  = (int) $atts['image_id']['id'];
			}
			if ( isset( $atts['icon_cl'] ) && isset( $atts['icon_cl']['value'] ) ) {
				if ( isset( $atts['icon_cl']['library'] ) && isset( $atts['icon_cl']['value']['id'] ) ) {
					$atts['icon_type'] = $atts['icon_cl']['library'];
					$atts['icon']      = $atts['icon_cl']['value']['id'];
				} else {
					$atts['icon'] = $atts['icon_cl']['value'];
				}
			}
			include $template;
		}
	}
} elseif ( ! empty( $content ) ) {
	echo do_shortcode( $content );
}
