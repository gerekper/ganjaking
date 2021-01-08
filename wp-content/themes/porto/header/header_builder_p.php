<?php
global $porto_settings;
$hb_layout = porto_header_builder_layout();
if ( empty( $hb_layout ) ) {
	return;
}
?>

<header id="header" class="header-builder header-builder-p<?php echo porto_header_type_is_side() ? ' header-side sticky-menu-header' : '', $porto_settings['logo-overlay'] && $porto_settings['logo-overlay']['url'] ? ' logo-overlay-header' : ''; ?>">
<?php echo do_shortcode( '[porto_block id="' . $hb_layout['ID'] . '" post_type="porto_builder"]' ); ?>
<?php get_template_part( 'header/mobile_menu' ); ?>
</header>
