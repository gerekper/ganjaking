<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
$class = isset( $css_class ) && ! empty( $css_class ) ? $css_class : 'vc_navbar';
$class .= wpb_get_name_post_custom_layout() ? ' vc_post-custom-layout-selected' : '';
$class .= ! empty( $post ) && ! empty( $post->post_content ) ? ' vc_not-empty' : '';
?>
<div class="<?php echo esc_attr( $class ); ?>"
	role="navigation"
	id="vc_navbar">
	<div class="vc_navbar-header">
		<?php
		// @codingStandardsIgnoreLine
		print $nav_bar->getLogo();
		?>
	</div>
	<ul class="vc_navbar-nav">
		<?php
		foreach ( $controls as $control ) :
			// @codingStandardsIgnoreLine
			print $control[1];
		endforeach;
		?>
	</ul>
	<!--/.nav-collapse -->
</div>
