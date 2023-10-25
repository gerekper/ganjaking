<?php

$content = $settings->description;
//var_dump(has_shortcode($content));
if ( ! empty( $settings->enable_wp_head_footer ) ) {
	if ( ! empty( $settings->apply_content_filter ) ) {
		$content = apply_filters( 'the_content', $content );
	} else {
		if ( isset( $GLOBALS['wp_embed'] ) ) {
			$content = $GLOBALS['wp_embed']->autoembed( $content );
		}
		$content = do_shortcode( shortcode_unautop( wpautop( convert_chars( wptexturize( $content ) ) ) ) );
	}
} else {
	if ( isset( $GLOBALS['wp_embed'] ) ) {
		$content = $GLOBALS['wp_embed']->autoembed( $content );
	}
	$content = do_shortcode( shortcode_unautop( wpautop( convert_chars( wptexturize( $content ) ) ) ) );
}
echo '<div id="cspio-description">' . $content . '</div>';
