<?php
/**
 * The template for displaying the footer.
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

$back_to_top_class = mfn_opts_get('back-top-top');

if ($back_to_top_class == 'hide') {
	$back_to_top_position = false;
} elseif (strpos($back_to_top_class, 'sticky') !== false) {
	$back_to_top_position = 'body';
} elseif (mfn_opts_get('footer-hide') == 1) {
	$back_to_top_position = 'footer';
} else {
	$back_to_top_position = 'copyright';
}
?>

<?php do_action('mfn_hook_content_after'); ?>

<?php if ('hide' != mfn_opts_get('footer-style')): ?>

	<footer id="Footer" class="clearfix">

		<?php if ($footer_call_to_action = mfn_opts_get('footer-call-to-action')): ?>
		<div class="footer_action">
			<div class="container">
				<div class="column one column_column">
					<?php echo do_shortcode($footer_call_to_action); ?>
				</div>
			</div>
		</div>
		<?php endif; ?>

		<?php
			$sidebars_count = 0;
			for ($i = 1; $i <= 5; $i++) {
				if (is_active_sidebar('footer-area-'. $i)) {
					$sidebars_count++;
				}
			}

			if ($sidebars_count > 0) {

				$align = mfn_opts_get('footer-align');

				echo '<div class="widgets_wrapper '. $align .'">';
					echo '<div class="container">';

						if ($footer_layout = mfn_opts_get('footer-layout')) {

							// Theme Options

							$footer_layout 	= explode(';', $footer_layout);
							$footer_cols = $footer_layout[0];

							for ($i = 1; $i <= $footer_cols; $i++) {
								if (is_active_sidebar('footer-area-'. $i)) {
									echo '<div class="column '. esc_attr($footer_layout[$i]) .'">';
										dynamic_sidebar('footer-area-'. $i);
									echo '</div>';
								}
							}

						} else {

							// default with equal width

							$sidebar_class = '';
							switch ($sidebars_count) {
								case 2: $sidebar_class = 'one-second'; break;
								case 3: $sidebar_class = 'one-third'; break;
								case 4: $sidebar_class = 'one-fourth'; break;
								case 5: $sidebar_class = 'one-fifth'; break;
								default: $sidebar_class = 'one';
							}

							for ($i = 1; $i <= 5; $i++) {
								if (is_active_sidebar('footer-area-'. $i)) {
									echo '<div class="column '. esc_attr($sidebar_class) .'">';
										dynamic_sidebar('footer-area-'. $i);
									echo '</div>';
								}
							}

						}

					echo '</div>';
				echo '</div>';
			}
		?>

		<?php if (mfn_opts_get('footer-hide') != 1): ?>

			<div class="footer_copy">
				<div class="container">
					<div class="column one">

						<?php
							if ($back_to_top_position == 'copyright') {
								echo '<a id="back_to_top" class="button button_js" href=""><i class="icon-up-open-big"></i></a>';
							}
						?>

						<div class="copyright">
							<?php
								if (mfn_opts_get('footer-copy')) {
									echo do_shortcode(mfn_opts_get('footer-copy'));
								} else {
									echo '&copy; '. esc_html(date('Y')) .' '. esc_html(get_bloginfo('name')) .'. All Rights Reserved. <a target="_blank" rel="nofollow" href="https://muffingroup.com">Muffin group</a>';
								}
							?>
						</div>

						<?php
							if (has_nav_menu('social-menu-bottom')) {
								mfn_wp_social_menu_bottom();
							} else {
								get_template_part('includes/include', 'social');
							}
						?>

					</div>
				</div>
			</div>

		<?php endif; ?>

		<?php
			if ($back_to_top_position == 'footer') {
				echo '<a id="back_to_top" class="button button_js in_footer" href=""><i class="icon-up-open-big"></i></a>';
			}
		?>

	</footer>
<?php endif; ?>

</div>

<?php
	// side slide menu
	if (mfn_opts_get('responsive-mobile-menu')) {
		get_template_part('includes/header', 'side-slide');
	}
?>

<?php
	if ($back_to_top_position == 'body') {
		echo '<a id="back_to_top" class="button button_js '. esc_attr($back_to_top_class) .'" href=""><i class="icon-up-open-big"></i></a>';
	}
?>

<?php if (mfn_opts_get('popup-contact-form')): ?>
	<div id="popup_contact">
		<a class="button button_js" href="#"><i class="<?php echo esc_attr(mfn_opts_get('popup-contact-form-icon', 'icon-mail-line')); ?>"></i></a>
		<div class="popup_contact_wrapper">
			<?php echo do_shortcode(mfn_opts_get('popup-contact-form')); ?>
			<span class="arrow"></span>
		</div>
	</div>
<?php endif; ?>

<?php do_action('mfn_hook_bottom'); ?>

<?php wp_footer(); ?>

</body>
</html>
