<?php
/**
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

$action_bar = mfn_opts_get('action-bar');

$translate['wpml-no'] = mfn_opts_get('translate') ? mfn_opts_get('translate-wpml-no', 'No translations available for this page') : __('No translations available for this page', 'betheme');
?>

<?php if ( ('1' === $action_bar) || isset($action_bar['show']) ): ?>
	<div id="Action_bar">
		<div class="container">
			<div class="column one">

				<?php
					get_template_part('includes/include', 'slogan');

					if (has_nav_menu('social-menu')) {
						mfn_wp_social_menu();
					} else {
						get_template_part('includes/include', 'social');
					}
				?>

			</div>
		</div>
	</div>
<?php endif; ?>

<?php
	if (mfn_header_style(true) == 'header-overlay') {

		// overlay menu

		echo '<div id="Overlay">';
			mfn_wp_overlay_menu();
		echo '</div>';

		// button

		echo '<a class="overlay-menu-toggle" href="#">';
			echo '<i class="open icon-menu-fine"></i>';
			echo '<i class="close icon-cancel-fine"></i>';
		echo '</a>';
	}
?>

<div class="header_placeholder"></div>

<div id="Top_bar" class="loading">

	<div class="container">
		<div class="column one">

			<div class="top_bar_left clearfix">

				<?php get_template_part('includes/include', 'logo'); ?>

				<div class="menu_wrapper">
					<?php
						if ((mfn_header_style(true) != 'header-overlay') && (mfn_opts_get('menu-style') != 'hide')) {

							// main menu

							mfn_wp_nav_menu();

							// responsive menu button

							$mb_class = '';
							if (mfn_opts_get('header-menu-mobile-sticky')) {
								$mb_class .= ' is-sticky';
							}

							echo '<a class="responsive-menu-toggle '. esc_attr($mb_class) .'" href="#">';
							if ($menu_text = trim(mfn_opts_get('header-menu-text'))) {
								echo '<span>'. wp_kses($menu_text, mfn_allowed_html()) .'</span>';
							} else {
								echo '<i class="icon-menu-fine"></i>';
							}
							echo '</a>';
						}
					?>
				</div>

				<div class="secondary_menu_wrapper">
					<?php mfn_wp_secondary_menu(); ?>
				</div>

				<div class="banner_wrapper">
					<?php echo wp_kses_post(mfn_opts_get('header-banner')); ?>
				</div>

				<div class="search_wrapper">
					<?php get_search_form(true); ?>
				</div>

			</div>

			<?php
				if (! mfn_opts_get('top-bar-right-hide')) {
					get_template_part('includes/header', 'top-bar-right');
				}
			?>

		</div>
	</div>
</div>
