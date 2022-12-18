<?php
global $porto_settings;
?>
<header id="header" class="header-19 logo-center<?php echo ( isset( $porto_settings['logo-overlay'] ) && $porto_settings['logo-overlay'] && $porto_settings['logo-overlay']['url'] ) ? ' logo-overlay-header' : ''; ?>">
	<?php if ( $porto_settings['show-header-top'] ) : ?>
	<div class="header-top">
		<div class="container">
			<div class="header-left">
				<?php
					// show search form
					echo porto_search_form();

					// show social links
					echo porto_header_socials();
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

					// show contact info
					$contact_info = $porto_settings['header-contact-info'];

				if ( $contact_info ) {
					echo '<div class="header-contact">' . do_shortcode( $contact_info ) . '</div>';
				}

					// mini cart
					echo porto_minicart();
				?>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<div class="header-main">
		<div class="container" id="main-menu">
			<div class="header-left show-menu-search">
				<?php
					// show main menu
					echo porto_main_menu();
				?>
			</div>
			<div class="header-center">
				<?php
					// show logo
					echo porto_logo();
				?>
			</div>
			<div class="header-right">
				<?php
					// show main menu
					echo porto_secondary_menu();

					// show menu custom content
				if ( $porto_settings['menu-block'] ) {
					echo '<div class="menu-custom-block">' . wp_kses_post( $porto_settings['menu-block'] ) . '</div>';
				}
				?>

				<?php get_template_part( 'header/header_tooltip' ); ?>

				<a class="mobile-toggle" aria-label="Mobile Menu" href="#"><i class="fas fa-bars"></i></a>
			</div>
		</div>
		<?php get_template_part( 'header/mobile_menu' ); ?>
	</div>
</header>
