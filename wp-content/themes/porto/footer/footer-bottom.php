<?php

global $porto_settings;

if ( ( $porto_settings['footer-logo'] && $porto_settings['footer-logo']['url'] ) || is_active_sidebar( 'footer-bottom' ) || $porto_settings['footer-copyright'] ) :
	?>
<div class="footer-bottom">
	<div class="container">
		<?php if ( ( $porto_settings['footer-logo'] && $porto_settings['footer-logo']['url'] ) || 'left' == $porto_settings['footer-copyright-pos'] || ( 'right' == $porto_settings['footer-copyright-pos'] && is_active_sidebar( 'footer-bottom' ) ) ) : ?>
		<div class="footer-left">
			<?php
			// show logo
			if ( $porto_settings['footer-logo'] && $porto_settings['footer-logo']['url'] ) :
				?>
				<span class="logo">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?> - <?php bloginfo( 'description' ); ?>">
						<?php echo '<img class="img-responsive" src="' . esc_url( str_replace( array( 'http:', 'https:' ), '', $porto_settings['footer-logo']['url'] ) ) . '" alt="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" />'; ?>
					</a>
				</span>
			<?php endif; ?>
			<?php
			if ( 'left' == $porto_settings['footer-copyright-pos'] ) {
				echo '<span class="footer-copyright">' . wp_kses_post( $porto_settings['footer-copyright'] ) . '</span>';
			} elseif ( 'right' == $porto_settings['footer-copyright-pos'] && is_active_sidebar( 'footer-bottom' ) ) {
				dynamic_sidebar( 'footer-bottom' );
			}
			?>
		</div>
		<?php endif; ?>

		<?php if ( ( $porto_settings['footer-payments'] && $porto_settings['footer-payments-image'] && $porto_settings['footer-payments-image']['url'] ) || 'center' == $porto_settings['footer-copyright-pos'] ) : ?>
			<div class="<?php echo 'center' == $porto_settings['footer-copyright-pos'] || 'right' == $porto_settings['footer-copyright-pos'] || ( 'left' == $porto_settings['footer-copyright-pos'] && is_active_sidebar( 'footer-bottom' ) ) ? 'footer-center' : 'footer-right'; ?>">
				<?php if ( $porto_settings['footer-payments'] && $porto_settings['footer-payments-image'] && $porto_settings['footer-payments-image']['url'] ) : ?>
					<?php if ( $porto_settings['footer-payments-link'] ) : ?>
					<a href="<?php echo esc_url( $porto_settings['footer-payments-link'] ); ?>">
					<?php endif; ?>
						<img class="img-responsive footer-payment-img" src="<?php echo esc_url( str_replace( array( 'http:', 'https:' ), '', $porto_settings['footer-payments-image']['url'] ) ); ?>" alt="<?php echo esc_attr( $porto_settings['footer-payments-image-alt'] ); ?>" />
					<?php if ( $porto_settings['footer-payments-link'] ) : ?>
					</a>
					<?php endif; ?>
				<?php endif; ?>
				<?php
				if ( 'center' == $porto_settings['footer-copyright-pos'] ) {
					echo '<span class="footer-copyright">' . wp_kses_post( $porto_settings['footer-copyright'] ) . '</span>';
					dynamic_sidebar( 'footer-bottom' );
				}
				?>
			</div>
		<?php endif; ?>

		<?php if ( 'right' == $porto_settings['footer-copyright-pos'] ) { ?>
			<div class="footer-right"><?php echo '<span class="footer-copyright">' . wp_kses_post( $porto_settings['footer-copyright'] ) . '</span>'; ?></div>
		<?php } elseif ( 'left' == $porto_settings['footer-copyright-pos'] && is_active_sidebar( 'footer-bottom' ) ) { ?>
			<div class="footer-right"><?php dynamic_sidebar( 'footer-bottom' ); ?></div>
		<?php } ?>
	</div>
</div>
<?php endif; ?>
