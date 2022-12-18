<?php
global $porto_settings;
$footer_view = porto_get_meta_value( 'footer_view' );
$cols        = 0;

for ( $i = 1; $i <= 4; $i++ ) {
	if ( is_registered_sidebar( 'footer-column-' . $i ) && is_active_sidebar( 'footer-column-' . $i ) ) {
		$cols++;
	}
}
?>
<div id="footer" class="footer footer-1<?php echo ! $porto_settings['footer-ribbon'] ? '' : ' show-ribbon'; ?>"
<?php
if ( ! empty( $porto_settings['footer-parallax'] ) ) {
	wp_enqueue_script( 'skrollr' );
	echo ' data-plugin-parallax data-plugin-options="{&quot;speed&quot;: ' . esc_attr( $porto_settings['footer-parallax-speed'] ) . '}"';}
?>
>
	<?php if ( ! $footer_view && $cols ) : ?>
		<div class="footer-main">
			<div class="container">
				<?php if ( $porto_settings['footer-ribbon'] ) : ?>
					<div class="footer-ribbon"><?php echo wp_kses_post( $porto_settings['footer-ribbon'] ); ?></div>
				<?php endif; ?>

				<?php
				if ( $cols ) :
					$col_class = array();
					switch ( $cols ) {
						case 1:
							$col_class[1] = 'col-lg-' . ( ( ! empty( $porto_settings['footer-customize'] ) && $porto_settings['footer-widget1'] ) ? $porto_settings['footer-widget1'] : '12' );
							break;
						case 2:
							$col_class[1] = 'col-lg-' . ( ( ! empty( $porto_settings['footer-customize'] ) && $porto_settings['footer-widget1'] ) ? $porto_settings['footer-widget1'] : '6' );
							$col_class[2] = 'col-lg-' . ( ( ! empty( $porto_settings['footer-customize'] ) && $porto_settings['footer-widget2'] ) ? $porto_settings['footer-widget2'] : '6' );
							break;
						case 3:
							$col_class[1] = 'col-lg-' . ( ( ! empty( $porto_settings['footer-customize'] ) && $porto_settings['footer-widget1'] ) ? $porto_settings['footer-widget1'] : '4' );
							$col_class[2] = 'col-lg-' . ( ( ! empty( $porto_settings['footer-customize'] ) && $porto_settings['footer-widget2'] ) ? $porto_settings['footer-widget2'] : '4' );
							$col_class[3] = 'col-lg-' . ( ( ! empty( $porto_settings['footer-customize'] ) && $porto_settings['footer-widget3'] ) ? $porto_settings['footer-widget3'] : '4' );
							break;
						case 4:
							$col_class[1] = 'col-lg-' . ( ( ! empty( $porto_settings['footer-customize'] ) && $porto_settings['footer-widget1'] ) ? $porto_settings['footer-widget1'] : '3' );
							$col_class[2] = 'col-lg-' . ( ( ! empty( $porto_settings['footer-customize'] ) && $porto_settings['footer-widget2'] ) ? $porto_settings['footer-widget2'] : '3' );
							$col_class[3] = 'col-lg-' . ( ( ! empty( $porto_settings['footer-customize'] ) && $porto_settings['footer-widget3'] ) ? $porto_settings['footer-widget3'] : '3' );
							$col_class[4] = 'col-lg-' . ( ( ! empty( $porto_settings['footer-customize'] ) && $porto_settings['footer-widget4'] ) ? $porto_settings['footer-widget4'] : '3' );
							break;
					}
					?>
					<div class="row">
						<?php
						$cols = 1;
						for ( $i = 1; $i <= 4; $i++ ) {
							if ( is_registered_sidebar( 'footer-column-' . $i ) && is_active_sidebar( 'footer-column-' . $i ) ) {
								?>
								<div class="<?php echo esc_attr( $col_class[ $cols++ ] ); ?>">
									<?php dynamic_sidebar( 'footer-column-' . $i ); ?>
								</div>
								<?php
							}
						}
						?>
					</div>
				<?php endif; ?>

				<?php get_template_part( 'footer/footer_tooltip' ); ?>
			</div>
		</div>
	<?php endif; ?>

	<?php get_template_part( 'footer/footer', 'bottom' ); ?>
</div>
