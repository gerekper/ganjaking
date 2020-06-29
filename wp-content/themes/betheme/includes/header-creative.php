<?php
/**
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

$translate['wpml-no'] = mfn_opts_get('translate') ? mfn_opts_get('translate-wpml-no', 'No translations available for this page') : __('No translations available for this page', 'betheme');
$translate['search-placeholder'] = mfn_opts_get('translate') ? mfn_opts_get('translate-search-placeholder', 'Enter your search') : __('Enter your search', 'betheme');

$creative_classes = '';
$creative_options = mfn_opts_get('menu-creative-options');

if (is_array($creative_options)) {
	if (isset($creative_options['scroll'])) {
		$creative_classes .= ' scroll';
	}
	if (isset($creative_options['dropdown'])) {
		$creative_classes .= ' dropdown';
	}
}
?>

<div id="Header_creative" class="<?php echo esc_attr($creative_classes); ?>">
	<a href="#" class="creative-menu-toggle"><i class="icon-menu-fine"></i></a>

	<?php
		echo '<div class="creative-social">';
			get_template_part('includes/include', 'social');
		echo '</div>';
	?>

	<div class="creative-wrapper">

		<!-- .header_placeholder 4sticky  -->
		<div class="header_placeholder"></div>

		<div id="Top_bar">
			<div class="one clearfix">

				<div class="top_bar_left">

					<!-- Logo -->
					<?php get_template_part('includes/include', 'logo'); ?>

					<div class="menu_wrapper">
						<?php
							// #menu --------------------------
							mfn_wp_nav_menu();

							$mb_class = '';
							if (mfn_opts_get('header-menu-mobile-sticky')) {
								$mb_class .= ' is-sticky';
							}

							// responsive menu button ---------
							echo '<a class="responsive-menu-toggle '. $mb_class .'" href="#">';
								if ($menu_text = mfn_opts_get('header-menu-text')) {
									echo '<span>'. wp_kses($menu_text, mfn_allowed_html()) .'</span>';
								} else {
									echo '<i class="icon-menu-fine"></i>';
								}
							echo '</a>';
						?>
					</div>

					<div class="search_wrapper">

						<!-- #searchform -->
						<form method="get" id="searchform" action="<?php echo esc_url(home_url('/')); ?>">

							<?php if (mfn_opts_get('header-search') == 'shop'): ?>
								<input type="hidden" name="post_type" value="product" />
							<?php endif;?>

							<i class="icon_search icon-search-fine"></i>
							<a href="#" class="icon_close"><i class="icon-cancel-fine"></i></a>

							<input type="text" class="field" name="s" id="s" placeholder="<?php echo esc_html($translate['search-placeholder']); ?>" />
							<?php do_action('wpml_add_language_form_field'); ?>

							<input type="submit" class="submit" value="" style="display:none;" />

						</form>

					</div>

				</div>

				<?php get_template_part('includes/header', 'top-bar-right'); ?>

				<div class="banner_wrapper">
					<?php echo wp_kses_post(mfn_opts_get('header-banner')); ?>
				</div>

			</div>
		</div>

		<div id="Action_bar" class="creative">
			<?php
				$action_bar = mfn_opts_get('action-bar');
				if( isset($action_bar['creative']) ){
					get_template_part('includes/include', 'slogan');
				}

				if (has_nav_menu('social-menu')) {
					mfn_wp_social_menu();
				} else {
					get_template_part('includes/include', 'social');
				}
			?>
		</div>

	</div>

</div>
