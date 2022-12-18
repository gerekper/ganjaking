<?php
global $porto_settings, $porto_layout;
?>
<header id="header" class="header-separate header-9 sticky-menu-header<?php echo ! $porto_settings['logo-overlay'] || ! $porto_settings['logo-overlay']['url'] ? '' : ' logo-overlay-header'; ?>">
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

				// show mobile toggle
				?>
			</div>
			<div class="header-right">
				<div class="header-minicart">
					<?php
					// show contact info and mini cart
					$contact_info = $porto_settings['header-contact-info'];

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

					// show mini cart
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
	$toggle_menu = porto_main_toggle_menu();
	if ( $toggle_menu || $porto_settings['menu-block'] ) :
		?>
	<div class="main-menu-wrap">
		<div id="main-menu" class="container">
			<div class="menu-center">
				<div class="row">
					<div class="col-lg-3 sidebar">
						<?php
						// show toggle menu
						if ( $toggle_menu ) :
							?>
							<div id="main-toggle-menu" class="<?php echo ( ! $porto_settings['menu-toggle-onhome'] && is_front_page() ) ? 'show-always' : 'closed'; ?>">
								<div class="menu-title closed">
									<div class="toggle"></div>
									<?php if ( $porto_settings['menu-title'] ) : ?>
										<?php echo do_shortcode( $porto_settings['menu-title'] ); ?>
									<?php endif; ?>
								</div>
								<div class="toggle-menu-wrap side-nav-wrap">
									<?php echo porto_filter_output( $toggle_menu ); ?>
								</div>
							</div>
						<?php endif; ?>
					</div>
					<?php if ( $porto_settings['menu-block'] ) : ?>
					<div class="col-lg-9">
						<div class="menu-custom-block">
							<?php echo do_shortcode( $porto_settings['menu-block'] ); ?>
							<?php
							if ( isset( $porto_settings['menu-login-pos'] ) && 'main_menu' == $porto_settings['menu-login-pos'] ) {
								if ( is_user_logged_in() ) {
									$logout_link = '';
									if ( class_exists( 'WooCommerce' ) ) {
										$logout_link = wc_get_endpoint_url( 'customer-logout', '', wc_get_page_permalink( 'myaccount' ) );
									} else {
										$logout_link = wp_logout_url( get_home_url() );
									}
									echo '<a class="' . ( is_rtl() ? 'pull-left p-l-none' : 'pull-right p-r-none' ) . '" href="' . esc_url( $logout_link ) . '"><i class="avatar">' . get_avatar( get_current_user_id(), $size = '24' ) . '</i>' . esc_html__( 'Logout', 'porto' ) . '</a>';
								} else {
									$login_link    = '';
									$register_link = '';
									if ( class_exists( 'WooCommerce' ) ) {
										$login_link = wc_get_page_permalink( 'myaccount' );
										if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) {
											$register_link = wc_get_page_permalink( 'myaccount' );
										}
									} else {
										$login_link    = wp_login_url( get_home_url() );
										$active_signup = get_site_option( 'registration', 'none' );
										$active_signup = apply_filters( 'wpmu_active_signup', $active_signup );
										if ( 'none' != $active_signup ) {
											$register_link = wp_registration_url( get_home_url() );
										}
									}
									if ( $register_link && isset( $porto_settings['menu-enable-register'] ) && $porto_settings['menu-enable-register'] ) {
										echo '<a class="porto-link-register ' . ( is_rtl() ? 'pull-left p-l-none' : 'pull-right p-r-none' ) . '" href="' . esc_url( $register_link ) . '"><i class="fas fa-user-plus"></i>' . esc_html__( 'Register', 'porto' ) . '</a>';
									}
									echo '<a class="porto-link-login ' . ( is_rtl() ? 'pull-left p-l-none' : 'pull-right p-r-none' ) . '" href="' . esc_url( $login_link ) . '"><i class="fas fa-user"></i>' . esc_html__( 'Login', 'porto' ) . '</a>';
								}
							}
							?>
						</div>
					</div>
					<?php endif; ?>
				</div>
			</div>
			<?php if ( $porto_settings['show-sticky-searchform'] || $porto_settings['show-sticky-minicart'] ) : ?>
				<div class="menu-right">
					<?php
					if ( $porto_settings['show-sticky-searchform'] ) {
						echo porto_search_form();
					}

					if ( $porto_settings['show-sticky-minicart'] ) {
						echo porto_minicart();
					}
					?>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>
</header>
