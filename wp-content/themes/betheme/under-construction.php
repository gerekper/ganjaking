<?php
/**
 * Template Name: Under Construction
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js<?php echo esc_attr(mfn_user_os()); ?>">

<head>

<meta charset="<?php bloginfo('charset'); ?>" />
<?php wp_head(); ?>

</head>

<?php
	$translate['days'] = mfn_opts_get('translate') ? mfn_opts_get('translate-days', 'days') : __('days', 'betheme');
	$translate['hours'] = mfn_opts_get('translate') ? mfn_opts_get('translate-hours', 'hours') : __('hours', 'betheme');
	$translate['minutes']	= mfn_opts_get('translate') ? mfn_opts_get('translate-minutes', 'minutes') : __('minutes', 'betheme');
	$translate['seconds']	= mfn_opts_get('translate') ? mfn_opts_get('translate-seconds', 'seconds') : __('seconds', 'betheme');

	$customID = mfn_opts_get('construction-page');
	$body_class = 'under-construction';
	if ($customID) {
		$body_class .= ' custom-uc';
	}
?>

<body <?php body_class($body_class); ?>>

	<div id="Content">
		<div class="content_wrapper clearfix">

			<?php if ($customID): ?>

				<div class="sections_group">
					<?php
						$mfn_builder = new Mfn_Builder_Front($customID, true);
						$mfn_builder->show();
					?>
				</div>

			<?php else: ?>

				<div class="sections_group">

					<div class="section center section-uc-1">
						<div class="section_wrapper clearfix">
							<div class="items_group clearfix">
								<div class="column one column_column">
									<?php
										$logo_src = mfn_opts_get('logo-img', get_theme_file_uri('/images/logo/logo.png'));
										echo '<a id="logo" href="'. esc_url(get_home_url()) .'" title="'. esc_attr(get_bloginfo('name')) .'">';
											echo '<img class="scale-with-grid" src="'. esc_url($logo_src) .'" alt="'. esc_attr(get_bloginfo('name')) .'" />';
										echo '</a>';
									?>
								</div>
							</div>
						</div>
					</div>

					<div class="section section-border-top section-uc-2">
						<div class="section_wrapper clearfix">
							<div class="items_group clearfix">

								<div class="column one column_fancy_heading">
									<div class="fancy_heading fancy_heading_icon">
										<div data-anim-type="bounceIn" class="animate bounceIn">
											<span class="icon_top"><i class="icon-clock"></i></span>
											<h2><?php echo wp_kses(mfn_opts_get('construction-title'), mfn_allowed_html()); ?></h2>
											<div class="inside">
												<span class="big"><?php echo wp_kses_post(mfn_opts_get('construction-text')); ?></span>
											</div>
										</div>
									</div>
								</div>

								<?php if (mfn_opts_get('construction-date')): ?>

									<div class="downcount" data-date="<?php echo esc_attr(mfn_opts_get('construction-date')); ?>" data-offset="<?php echo esc_attr(mfn_opts_get('construction-offset')); ?>">
										<div class="column one-fourth column_quick_fact">
											<div class="quick_fact">
												<div data-anim-type="zoomIn" class="animate zoomIn">
													<div class="number-wrapper">
														<div class="number days">00</div>
													</div>
													<h3 class="title"><?php echo esc_html($translate['days']); ?></h3>
													<hr class="hr_narrow">
												</div>
											</div>
										</div>
										<div class="column one-fourth column_quick_fact">
											<div class="quick_fact">
												<div data-anim-type="zoomIn" class="animate zoomIn">
													<div class="number-wrapper">
														<div class="number hours">00</div>
													</div>
													<h3 class="title"><?php echo esc_html($translate['hours']); ?></h3>
													<hr class="hr_narrow">
												</div>
											</div>
										</div>
										<div class="column one-fourth column_quick_fact">
											<div class="quick_fact">
												<div data-anim-type="zoomIn" class="animate zoomIn">
													<div class="number-wrapper">
														<div class="number minutes">00</div>
													</div>
													<h3 class="title"><?php echo esc_html($translate['minutes']); ?></h3>
													<hr class="hr_narrow">
												</div>
											</div>
										</div>
										<div class="column one-fourth column_quick_fact">
											<div class="quick_fact">
												<div data-anim-type="zoomIn" class="animate zoomIn">
													<div class="number-wrapper">
														<div class="number seconds">00</div>
													</div>
													<h3 class="title"><?php echo esc_html($translate['seconds']); ?></h3>
													<hr class="hr_narrow">
												</div>
											</div>
										</div>
									</div>

								<?php endif; ?>

							</div>
						</div>
					</div>

					<div class="section section-border-top section-uc-3">
						<div class="section_wrapper clearfix">
							<div class="items_group clearfix">
								<div class="column one-fourth column_column"></div>
								<div class="column one-second column_column">
									<div data-anim-type="fadeInUpLarge" class="animate fadeInUpLarge">
										<?php echo do_shortcode(mfn_opts_get('construction-contact')); ?>
									</div>
								</div>
								<div class="column one-fourth column_column"></div>
							</div>
						</div>
					</div>

				</div>

			<?php endif; ?>

		</div>
	</div>

<?php wp_footer(); ?>

</body>
</html>
