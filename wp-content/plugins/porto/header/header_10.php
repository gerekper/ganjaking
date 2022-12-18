<?php
global $porto_settings, $porto_layout;
?>
<header id="header" class="header-corporate header-10<?php echo ! $porto_settings['logo-overlay'] || ! $porto_settings['logo-overlay']['url'] ? '' : ' logo-overlay-header'; ?>">
	<?php if ( ! empty( $porto_settings['show-header-top'] ) ) : ?>
		<div class="header-top">
			<div class="container">
				<div class="header-left">
					<?php
					// show currency and view switcher
					$currency_switcher = porto_currency_switcher();
					$view_switcher     = porto_view_switcher();

					if ( $currency_switcher || $view_switcher ) {
						echo '<div class="switcher-wrap">';
					}

					echo porto_filter_output( $view_switcher );

					if ( $currency_switcher && $view_switcher ) {
						echo '<span class="gap switcher-gap">|</span>';
					}

					echo porto_filter_output( $currency_switcher );

					if ( $currency_switcher || $view_switcher ) {
						echo '</div>';
					}
					?>
				</div>
				<div class="header-right">
					<?php
					// show welcome message and top navigation
					$top_nav = porto_top_navigation();

					if ( $porto_settings['welcome-msg'] ) {
						echo '<span class="welcome-msg">' . do_shortcode( $porto_settings['welcome-msg'] ) . '</span>';
					}

					if ( $porto_settings['welcome-msg'] && $top_nav ) {
						echo '<span class="gap">|</span>';
					}

					echo porto_filter_output( $top_nav );
					?>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<div class="header-main header-body" style="top: 0px;">
		<div class="header-container container">
			<div class="header-left">
				<?php echo porto_logo(); ?>
			</div>

			<div class="header-right">
				<div class="header-right-top">
					<?php
					// show contact info and top navigation
					$contact_info = isset( $porto_settings['header-contact-info'] ) ? $porto_settings['header-contact-info'] : '';

					if ( $contact_info ) {
						echo '<div class="header-contact">' . do_shortcode( $contact_info ) . '</div>';
					}

					// show search form
					echo porto_search_form();

					// show minicart
					echo porto_minicart();
					?>
				</div>
				<div class="header-right-bottom">
					<div id="main-menu">
						<?php echo porto_main_menu(); ?>
					</div>
					<?php echo porto_header_socials(); ?>

					<a class="mobile-toggle" href="#" aria-label="Mobile Menu"><i class="fas fa-bars"></i></a>
				</div>

				<?php get_template_part( 'header/header_tooltip' ); ?>

			</div>
		</div>

		<?php get_template_part( 'header/mobile_menu' ); ?>
	</div>
</header>
