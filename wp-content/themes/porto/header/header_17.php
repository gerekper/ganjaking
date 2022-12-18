<?php
	global $porto_settings, $porto_layout;
?>
<header id="header" class="header-separate header-corporate header-17 sticky-menu-header<?php echo ! $porto_settings['logo-overlay'] || ! $porto_settings['logo-overlay']['url'] ? '' : ' logo-overlay-header'; ?>">
	<?php if ( $porto_settings['show-header-top'] ) : ?>
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

					// show welcome message
					if ( $porto_settings['welcome-msg'] ) {
						echo '<span class="welcome-msg">' . do_shortcode( $porto_settings['welcome-msg'] ) . '</span>';
					}
					?>
				</div>
				<div class="header-right">
					<?php
					// show top navigation
					$top_nav = porto_top_navigation();
					echo porto_filter_output( $top_nav );
					?>
					<?php
					$header_social = porto_header_socials();
					if ( $header_social ) {
						echo '<div class="block-inline">';
						// show social links
						echo porto_filter_output( $header_social );
						echo '</div>';
					}
					?>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<div class="header-main">
		<div class="container">
			<div class="header-left">
			<?php
				// show logo
				echo porto_logo();
			?>
			</div>
			<div class="header-right">
				<?php
				// show contact info and top navigation
				$contact_info = $porto_settings['header-contact-info'];

				if ( $contact_info ) {
					echo '<div class="header-contact">' . do_shortcode( $contact_info ) . '</div>';
				}

				// show search form
				echo porto_search_form();

				// show mobile toggle
				?>
				<a class="mobile-toggle" aria-label="Mobile Menu" href="#"><i class="fas fa-bars"></i></a>
				<?php
				if ( $porto_settings['show-header-top'] || $porto_settings['show-sticky-minicart'] ) {
					// show minicart
					echo porto_minicart();
				}
				if ( ! $porto_settings['show-header-top'] ) {
					$header_social = porto_header_socials();
					if ( $header_social ) {
						echo '<div class="block-inline">';
						// show social links
						echo porto_filter_output( $header_social );
						echo '</div>';
					}
				}
				?>

				<?php get_template_part( 'header/header_tooltip' ); ?>

			</div>
		</div>
		<?php get_template_part( 'header/mobile_menu' ); ?>
	</div>

	<?php
	// check main menu
	$main_menu = porto_main_menu();
	if ( $main_menu ) :
		?>
		<div class="main-menu-wrap<?php echo ! $porto_settings['menu-type'] ? '' : ' ' . esc_attr( $porto_settings['menu-type'] ); ?>">
			<div id="main-menu" class="container <?php echo esc_attr( $porto_settings['menu-align'] ); ?><?php echo ! $porto_settings['show-sticky-menu-custom-content'] ? ' hide-sticky-content' : ''; ?>">
				<div class="menu-center">
				<?php
					// show main menu
					echo porto_filter_output( $main_menu );
				?>
				</div>
				<div class="menu-right">
				<?php
					// show search form
					echo porto_search_form();

				if ( $porto_settings['show-sticky-minicart'] ) {
					// show mini cart
					echo porto_minicart();
				}
				?>
				</div>
			</div>
		</div>
	<?php endif; ?>

</header>
