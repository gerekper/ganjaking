<?php
$output = $show_dots_nav = $section_ids = $section_titles = $el_class = '';

extract(
	shortcode_atts(
		array(
			'show_dots_nav'  => true,
			'is_light'        => false,
			'section_ids'    => '',
			'section_titles' => '',
			'el_class'       => '',
		),
		$atts
	)
);

$el_class = porto_shortcode_extract_class( $el_class );

if ( $section_ids ) {
	wp_enqueue_script( 'porto_section_scroll_js' );
?>
	<script>
		jQuery(document).ready(function($) {
			$('<?php echo esc_js( trim( $section_ids ) ); ?>').addClass('section-scroll');
	<?php

		$section_ids    = explode( ',', $section_ids );
		$section_titles = explode( ',', $section_titles );
		foreach ( $section_ids as $index => $section_id ) {
			$section_id    = trim( $section_id );
			$section_title = isset( $section_titles[ $index ] ) ? trim( $section_titles[ $index ] ) : '';
	?>
			$('<?php echo esc_js( $section_id ); ?>').data('section-scroll-title', '<?php echo esc_js( $section_title ); ?>');
	<?php
		}
	?>
			var options = {};
			options.dotsNav = <?php echo ! $show_dots_nav ? 'false' : 'true'; ?>;
			options.dotsClass = '<?php echo ! $is_light ? '' : 'dots-nav-light'; ?>';

			setTimeout(function() {
				$(document.body).themePluginSectionScroll(options);
			}, 400);
		});
	</script>
<?php
}

echo porto_filter_output( $output );
