<?php
/**
 * Porto: Gutenberg Editor Style
 *
 * @package porto
 * @since 5.0
 */

global $porto_settings;
$porto_settings_backup = $porto_settings;
$b                     = porto_check_theme_options();
$porto_settings        = $porto_settings_backup;
$porto_is_dark         = ( 'dark' == $b['css-type'] );
$dark                  = $porto_is_dark;

if ( is_rtl() ) {
	$left_escaped  = 'right';
	$right_escaped = 'left';
	$rtl_escaped   = true;
} else {
	$left_escaped  = 'left';
	$right_escaped = 'right';
	$rtl_escaped   = false;
}

if ( $dark ) {
	$color_dark = $b['color-dark'];
} else {
	$color_dark = $b['dark-color'];
}
?>
/* Generals */
body .editor-styles-wrapper {
	font-family: <?php echo sanitize_text_field( $b['body-font']['font-family'] ); ?>, sans-serif;
	<?php if ( $b['body-font']['font-weight'] ) : ?>
		font-weight: <?php echo esc_html( $b['body-font']['font-weight'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['body-font']['font-size'] ) : ?>
		font-size: <?php echo esc_html( $b['body-font']['font-size'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['body-font']['line-height'] ) : ?>
		line-height: <?php echo esc_html( $b['body-font']['line-height'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['body-font']['letter-spacing'] ) : ?>
		letter-spacing: <?php echo esc_html( $b['body-font']['letter-spacing'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['body-font']['color'] ) : ?>
		color: <?php echo esc_html( $b['body-font']['color'] ); ?>;
	<?php endif; ?>
}

body .editor-styles-wrapper h1, body .editor-styles-wrapper h2, body .editor-styles-wrapper h3, body .editor-styles-wrapper h4, body .editor-styles-wrapper h5, body .editor-styles-wrapper h6 { color: <?php echo porto_if_dark( '#fff', $color_dark ); ?>; margin-top: 0; margin-bottom: 1rem }

body .editor-styles-wrapper h1 {
	<?php if ( $b['h1-font']['font-family'] ) : ?>
		font-family: <?php echo sanitize_text_field( $b['h1-font']['font-family'] ); ?>, sans-serif;
	<?php endif; ?>
	<?php if ( $b['h1-font']['font-weight'] ) : ?>
		font-weight: <?php echo esc_html( $b['h1-font']['font-weight'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['h1-font']['font-size'] ) : ?>
		font-size: <?php echo esc_html( $b['h1-font']['font-size'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['h1-font']['line-height'] ) : ?>
		line-height: <?php echo esc_html( $b['h1-font']['line-height'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['h1-font']['letter-spacing'] ) : ?>
		letter-spacing: <?php echo esc_html( $b['h1-font']['letter-spacing'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['h1-font']['color'] ) : ?>
		color: <?php echo esc_html( $b['h1-font']['color'] ); ?>;
	<?php endif; ?>
}
<?php for ( $i = 2; $i <= 6; $i++ ) { ?>
	body .editor-styles-wrapper h<?php echo (int) $i; ?> {
		<?php if ( $b[ 'h' . $i . '-font' ]['font-family'] ) : ?>
			font-family: <?php echo sanitize_text_field( $b[ 'h' . $i . '-font' ]['font-family'] ); ?>, sans-serif;
		<?php endif; ?>
		<?php if ( $b[ 'h' . $i . '-font' ]['font-weight'] ) : ?>
			font-weight: <?php echo esc_html( $b[ 'h' . $i . '-font' ]['font-weight'] ); ?>;
		<?php endif; ?>
		<?php if ( $b[ 'h' . $i . '-font' ]['font-size'] ) : ?>
			font-size: <?php echo esc_html( $b[ 'h' . $i . '-font' ]['font-size'] ); ?>;
		<?php endif; ?>
		<?php if ( $b[ 'h' . $i . '-font' ]['line-height'] ) : ?>
			line-height: <?php echo esc_html( $b[ 'h' . $i . '-font' ]['line-height'] ); ?>;
		<?php endif; ?>
		<?php if ( $b[ 'h' . $i . '-font' ]['letter-spacing'] ) : ?>
			letter-spacing: <?php echo esc_html( $b[ 'h' . $i . '-font' ]['letter-spacing'] ); ?>;
		<?php endif; ?>
		<?php if ( $b[ 'h' . $i . '-font' ]['color'] ) : ?>
			color: <?php echo esc_html( $b[ 'h' . $i . '-font' ]['color'] ); ?>;
		<?php endif; ?>
	}
<?php } ?>

body .editor-styles-wrapper p {
	font-size: 14px;
	<?php if ( $b['paragraph-font']['font-family'] ) : ?>
		font-family: <?php echo sanitize_text_field( $b['paragraph-font']['font-family'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['paragraph-font']['letter-spacing'] ) : ?>
		letter-spacing: <?php echo esc_html( $b['paragraph-font']['letter-spacing'] ); ?>;
	<?php endif; ?>
	margin-top: 0;
	margin-bottom: 1em;
}

<?php if ( ! class_exists( 'Woocommerce' ) ) : ?>
	.editor-styles-wrapper h1, .editor-styles-wrapper h2, .editor-styles-wrapper h3, .editor-styles-wrapper h4, .editor-styles-wrapper h5, .editor-styles-wrapper h6 { letter-spacing: -0.05em; }
<?php endif; ?>

/* Layouts */
@media (min-width: 768px) {
	.wp-block { max-width: 800px }
	.wp-block[data-align=wide] { max-width: 1140px; }
}

@media (min-width: 1680px) {
	.wp-block { max-width: 1140px; }
}
.wp-block .wp-block { width: 100%; }

.editor-styles-wrapper .wp-block-columns > .block-editor-inner-blocks > .block-editor-block-list__layout { margin-left: -<?php echo (int) $b['grid-gutter-width'] / 2; ?>px; margin-right: -<?php echo (int) $b['grid-gutter-width'] / 2; ?>px }
.editor-styles-wrapper .wp-block-columns > .block-editor-inner-blocks > .block-editor-block-list__layout > [data-type="core/column"],
ul.products li.product-col { padding-left: <?php echo (int) $b['grid-gutter-width'] / 2; ?>px; padding-right: <?php echo (int) $b['grid-gutter-width'] / 2; ?>px; margin-left: 0; margin-right: 0 }
.products.grid-creative li.product-col { padding-bottom: <?php echo (int) $b['grid-gutter-width']; ?>px; margin-bottom: 0 }
.editor-styles-wrapper ul.products { width: calc(100% + <?php echo (int) $b['grid-gutter-width']; ?>px); margin-left: -<?php echo (int) $b['grid-gutter-width'] / 2; ?>px; margin-right: -<?php echo (int) $b['grid-gutter-width'] / 2; ?>px }
.editor-styles-wrapper .posts-container[class*="columns-"] { grid-row-gap: <?php echo (int) $b['grid-gutter-width']; ?>px; grid-column-gap: <?php echo (int) $b['grid-gutter-width']; ?>px }

/* Theme Colors */
<?php
	$theme_colors = array(
		'primary'    => $skin_color,
		'secondary'  => $b['secondary-color'],
		'tertiary'   => $b['tertiary-color'],
		'quaternary' => $b['quaternary-color'],
		'dark'       => $b['dark-color'],
		'light'      => $b['light-color'],
	);
	foreach ( $theme_colors as $key => $theme_color ) {
		echo '.background-color-' . $key . '{background-color: ' . esc_html( $theme_color ) . ' !important }';
		echo '.text-color-' . $key . '{color: ' . esc_html( $theme_color ) . ' !important }';
	}
	?>
.editor-styles-wrapper a {
	color: <?php echo esc_html( $b['skin-color'] ); ?>; text-decoration: none; pointer-events: none;
}
.editor-styles-wrapper .wp-block-pullquote blockquote { border-<?php echo porto_filter_output( $left_escaped ); ?>-color: <?php echo esc_html( $b['skin-color'] ); ?>; text-align: <?php echo porto_filter_output( $left_escaped ); ?>; padding: 2em; }
ul.list li.product .add_to_cart_button,
ul.list li.product .add_to_cart_read_more,
ul.products li.product-default:hover .add-links .add_to_cart_button,
.product-image .viewcart:hover,
li.product-outimage_aq_onimage .add-links .quickview,
li.product-onimage .product-content .quickview,
li.product-onimage2 .quickview,
li.product-wq_onimage .links-on-image .quickview,
.products-slider .owl-dot.active span:after,
.owl-carousel .owl-nav button.owl-prev,
.owl-carousel .owl-nav button.owl-next { background-color: <?php echo esc_html( $b['skin-color'] ); ?>; border-color: <?php echo esc_html( $b['skin-color'] ); ?> }
.products-slider.owl-carousel .owl-dot span { border-color: rgba(<?php echo esc_html( $porto_color_lib->hexToRGB( $porto_color_lib->darken( $skin_color, 20 ) ) ); ?>, .4) }
.owl-carousel .owl-dots .owl-dot.active span,
.owl-carousel .owl-dots .owl-dot:hover span { border-color: <?php echo esc_html( $b['skin-color'] ); ?> }

ul.category-color-dark li.product-category .thumb-info-title,
ul.products li.cat-has-icon .thumb-info > i { color: <?php echo esc_html( $b['dark-color'] ); ?> }

/* Core Blocks */

/* Porto Blocks */
.owl-carousel.show-dots-title-right .owl-dots { right: <?php echo (int) $b['grid-gutter-width'] / 2 - 2; ?>px; }

/* Products */
.editor-styles-wrapper .add-links .add_to_cart_button,
.editor-styles-wrapper .add-links .add_to_cart_read_more,
.editor-styles-wrapper .add-links .quickview,
.editor-styles-wrapper .yith-wcwl-add-to-wishlist span { background-color: <?php echo esc_html( $b['shop-add-links-bg-color'] ); ?>; border: 1px solid <?php echo esc_html( $b['shop-add-links-border-color'] ); ?>; color: <?php echo esc_html( $b['shop-add-links-color'] ); ?>; border-radius: 0 }
.porto-products.title-border-bottom > .section-title { border-bottom: 1px solid rgba(0, 0, 0, .06); }
li.product-onimage .product-content { background: #fff; border-top: 1px solid rgba(0, 0, 0, .09) }
