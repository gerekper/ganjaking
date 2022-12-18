<?php
global $porto_settings, $porto_layout;
?>
<header id="header" class="header-separate header-1 sticky-menu-header<?php echo ! $porto_settings['logo-overlay'] || ! $porto_settings['logo-overlay']['url'] ? '' : ' logo-overlay-header'; ?>">
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

				// show social links
				$social_links = porto_header_socials();
				if ( $social_links ) {
					if ( $top_nav ) {
						echo '<span class="gap">|</span>';
					}
					echo porto_filter_output( $social_links );
				}
				?>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<div class="header-main">
		<div class="container header-row">
			<div class="header-left">
				<a class="mobile-toggle" href="#" aria-label="Mobile Menu"><i class="fas fa-bars"></i></a>
				<?php
				// show logo
				echo porto_logo();
				?>
			</div>
			<div class="header-center">
				<?php
				// show search form
				echo porto_search_form();
				?>
			</div>
			<div class="header-right">
				<div class="header-minicart">
					<?php
					// show contact info and mini cart
					$contact_info = isset( $porto_settings['header-contact-info'] ) ? $porto_settings['header-contact-info'] : '';

					if ( $contact_info ) {
						echo '<div class="header-contact">';
						echo do_shortcode( $contact_info );
					}
					if ( ! empty( $porto_settings['header-woo-icon'] ) ) {
						if ( in_array( 'account', $porto_settings['header-woo-icon'] ) && class_exists( 'Woocommerce' ) ) {
							echo porto_account_menu( '' );
						}
						if ( in_array( 'wishlist', $porto_settings['header-woo-icon'] ) ) {
							echo porto_wishlist( '' );
						}
					}
					if ( $contact_info ) {
						echo '</div>';
					}
					echo porto_minicart();
					?>
				</div>

				<?php
				get_template_part( 'header/header_tooltip' );
				?>

			</div>
		</div>
		<?php
			get_template_part( 'header/mobile_menu' );
		?>
	</div>

	<?php
	// check main menu
	$main_menu = porto_main_menu();
	if ( $main_menu ) :
		?>
		<div class="main-menu-wrap<?php echo ! $porto_settings['menu-type'] ? '' : ' ' . esc_attr( $porto_settings['menu-type'] ); ?>">
			<div id="main-menu" class="container <?php echo esc_attr( $porto_settings['menu-align'] ); ?><?php echo ! $porto_settings['show-sticky-menu-custom-content'] ? ' hide-sticky-content' : ''; ?>">
				<?php if ( $porto_settings['show-sticky-logo'] ) : ?>
					<div class="menu-left">
						<?php
						// show logo
						echo porto_logo( true );
						?>
					</div>
				<?php endif; ?>
				<div class="menu-center">
					<?php
					// show main menu
					echo porto_filter_output( $main_menu );
					?>
				</div>
				<?php if ( $porto_settings['show-sticky-searchform'] || $porto_settings['show-sticky-minicart'] || ! empty( $porto_settings['show-sticky-contact-info'] ) ) : ?>
					<div class="menu-right">
						<?php
						// show search form
						echo porto_search_form();

						if ( ! empty( $porto_settings['show-sticky-contact-info'] ) ) {
							if ( $contact_info ) {
								echo '<div class="header-contact">';
								echo do_shortcode( $contact_info );
							}
							if ( ! empty( $porto_settings['header-woo-icon'] ) ) {
								if ( in_array( 'account', $porto_settings['header-woo-icon'] ) && class_exists( 'Woocommerce' ) ) {
									echo porto_account_menu( '' );
								}
								if ( in_array( 'wishlist', $porto_settings['header-woo-icon'] ) ) {
									echo porto_wishlist( '' );
								}
							}
							if ( $contact_info ) {
								echo '</div>';
							}
						}

						// show mini cart
						echo porto_minicart();
						?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>
</header>
