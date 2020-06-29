<?php defined( 'LS_ROOT_FILE' ) || exit; ?>
<script type="text/html" id="tmpl-ls-transition-modal">
	<div id="ls-transition-window" class="<?php echo ( LS_Config::get('theme_bundle') && ! $lsActivated ) ? 'hide-special-effects' : '' ?>">

		<h1 class="kmw-modal-title"><?php _e('Select slide transitions', 'LayerSlider') ?></h1>

		<div id="transitiongallery-header">
			<div id="tryorigami">
				<img src="<?php echo LS_ROOT_URL ?>/static/admin/img/origami.png" alt="Try the Origami Effect!">
			</div>

			<div id="transitionmenu" class="filters">
				<span><?php _e('Show transitions:', 'LayerSlider') ?></span>
				<ul>
					<li class="active"><?php _e('2D', 'LayerSlider') ?></li>
					<li><?php _e('3D', 'LayerSlider') ?></li>
					<li><?php _e('Custom 2D &amp; 3D', 'LayerSlider') ?></li>

					<?php if( ! LS_Config::get('theme_bundle') || $lsActivated ) : ?>
					<li><?php _e('Special Effects', 'LayerSlider') ?></li>
					<?php endif ?>
				</ul>
				<i><?php _e('Apply to others', 'LayerSlider') ?></i>
				<i class="off"><?php _e('Select all', 'LayerSlider') ?></i>
			</div>
		</div>

		<div class="inner">
			<div id="ls-transitions-list">

				<!-- 2D -->
				<section data-tr-type="2d_transitions">
					<div></div>
				</section>

				<!-- 3D -->
				<section data-tr-type="3d_transitions">
					<div></div>
				</section>

				<!-- Custom 2D -->
				<section data-tr-type="custom_2d_transitions">
					<h4><?php _e('Custom 2D transitions', 'LayerSlider') ?></h4>
					<div>
						<p><?php _e('You haven’t created any custom 2D transitions yet.', 'LayerSlider') ?></p>
					</div>
				</section>

				<!-- Custom 3D -->
				<section data-tr-type="custom_3d_transitions">
					<h4><?php _e('Custom 3D transitions', 'LayerSlider') ?></h4>
					<div>
						<p><?php _e('You haven’t created any custom 3D transitions yet.', 'LayerSlider') ?></p>
					</div>
				</section>

				<!-- Special Effects -->
				<section data-tr-type="special_effects" id="ls-special-effects">

				<p class="ls-description">
					<small>
						<?php _e('Special effects are like regular slide transitions and they work in the same way. You can set them on each slide individually. Mixing them with other transitions on other slides is perfectly fine. You can also apply them on all of your slides at once by pressing the “Apply to others” button above. In case of 3D special effects, selecting additional 2D transitions can ensure backward compatibility for older browsers.', 'LayerSlider') ?>
					</small>
				</p>

					<div class="separated">

						<table>
							<tr>
								<td>
									<h4><?php _e('Origami transition', 'LayerSlider') ?></h4>
								</td>
								<td rowspan="2">
									<p>
										<?php _e('Share your gorgeous photos with the world or your loved ones in a truly inspirational way and create sliders with stunning effects with Origami.', 'LayerSlider') ?>
									</p>
									<small>
										<?php _e('Origami is a form of 3D transition and it works in the same way as regular slide transitions do. Besides Internet Explorer, Origami works in all the modern browsers (including Edge).', 'LayerSlider') ?>
									</small>
								</td>
							</tr>
							<tr>
								<td class="center">
									<div class="ls-select-special-transition <?php echo ! $lsActivated ? 'locked' : '' ?>" data-name="transitionorigami">
										<span class="dashicons dashicons-yes"></span>
										<?php _e('Use it on this slide', 'LayerSlider') ?>
										<?php if( ! $lsActivated ) : ?>
										<a class="ls-activation-lock dashicons dashicons-lock" target="_blank" href="<?php echo admin_url('admin.php?page=layerslider-addons' ) ?>" data-help="<?php _e('This feature requires product activation. Click on the padlock icon to learn more.', 'LayerSlider') ?>" data-help-delay="100"></a>
										<?php endif ?>
									</div>
									<div class="center ls-example-link">
										<a href="https://layerslider.kreaturamedia.com/sliders/origami/" target="_blank"><?php _e('Click here for live example', 'LayerSlider') ?></a>
									</div>
								</td>
							</tr>
						</table>

					</div>

					<div class="separated ls-future">
						<h4><?php _e('More effects are coming soon', 'LayerSlider') ?></h4>
					</div>

				</section>
			</div>
		</div>
	</div>
</script>