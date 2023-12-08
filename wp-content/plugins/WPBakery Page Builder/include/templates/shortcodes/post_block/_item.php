<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/** @var array $block_data */
$block = $block_data[0];
$settings = $block_data[1];
$link_setting = empty( $settings[0] ) ? '' : $settings[0];

$output = '';
if ( 'title' === $block ) {
	$output .= '<h2 class="post-title">';
	$output .= empty( $link_setting ) || 'no_link' !== $link_setting ? $this->getLinked( $post, $post->title, $link_setting, 'link_title' ) : $post->title;
	$output .= '</h2>';
} elseif ( 'image' === $block && ! empty( $post->thumbnail ) ) {
	$output .= '<div class="post-thumb">';
	$output .= empty( $link_setting ) || 'no_link' !== $link_setting ? $this->getLinked( $post, $post->thumbnail, $link_setting, 'link_image' ) : $post->thumbnail;
	$output .= '</div>';
} elseif ( 'text' === $block ) {
	$output .= '<div class="entry-content">';
	$output .= empty( $link_setting ) || 'text' === $link_setting ? $post->content : $post->excerpt;
	$output .= '</div>';
} elseif ( 'link' === $block ) {
	$output .= '<a href="' . esc_url( $post->link ) . '" class="vc_read_more" title="' . sprintf( esc_attr__( 'Permalink to %s', 'js_composer' ), esc_attr( $post->title_attribute ) ) . '" ';
	$output .= $this->link_target;
	$output .= '>';
	$output .= esc_html__( 'Read more', 'js_composer' );
	$output .= '</a>';
}

return $output;
