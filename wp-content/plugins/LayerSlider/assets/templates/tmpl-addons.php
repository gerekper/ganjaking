<?php

// Prevent direct file access
defined( 'LS_ROOT_FILE' ) || exit;

$isActivated = LS_Config::isActivatedSite();

?>

<?php include LS_ROOT_PATH . '/templates/tmpl-revisions-preferences.php'; ?>


<div class="wrap ls-addons-page">

	<!-- Page title -->
	<h2><?php _e('LayerSlider Add-Ons', 'LayerSlider') ?></h2>

	<!-- Activation notice -->
	<?php if( ! $isActivated ) : ?>
	<div class="ls-notification-info">
		<i class="dashicons dashicons-info"></i>

		<?php

			if( LS_Config::get('notices') ) {
				echo sprintf(
					__('Product activation is required to use Add-Ons. Add-Ons are optional, but they can enhance your content &amp; workflow. Activate your copy of LayerSlider in order to receive these additional benefits. <br><br> %sPurchase a license%s or %sread our documentation%s to learn more. %sGot LayerSlider in a theme?%s', 'LayerSlider'),
						'<a href="'.LS_Config::get('purchase_url').'" target="_blank">',
						'</a>',
						'<a href="https://layerslider.kreaturamedia.com/documentation/#activation" target="_blank">',
						'</a>',
						'<a href="https://layerslider.kreaturamedia.com/documentation/#activation-bundles" target="_blank">',
						'</a>'
					);
			} else {
				echo sprintf(
					__('Product activation is required in order to use Add-Ons. Add-Ons can enhance your content &amp; workflow, but they are optional and not required to build sliders. Product activation requires you to have a license key, which is payable if you have received LayerSlider with a theme. For more information, please read our %sactivation guide%s.', 'LayerSlider'),
					'<a href="https://layerslider.kreaturamedia.com/documentation/#activation" target="_blank">',
					'</a>'
				);
			}
		?>
	</div>
	<?php else : ?>
	<div class="ls-notification-info success">
		<i class="dashicons dashicons-yes"></i>
		<?php _e('You’ve successfully activated this copy of LayerSlider to use Add-Ons and receive the following benefits.', 'LayerSlider') ?>
	</div>
	<?php endif ?>

	<!-- List of Add-Ons -->
	<div class="layerslider-addons clearfix">

		<!-- Template Store -->
		<div id="ls-addon-templates" class="ls-addon-item ls-addon-light">
			<div class="ls-addon-bg"></div>
			<h3 class="ls-addon-title"><?php _e('Templates', 'LayerSlider') ?></h3>
			<div class="ls-addon-body">
				<?php _e('Unlock the full contents of the Template Store. The ever growing selection of fully crafted, customizable and importable slider templates are an ideal starting point for new projects and they cover every common use case from personal to corporate business.', 'LayerSlider') ?>
			</div>
			<div class="ls-addon-footer">
				<?php if( ! $isActivated ) : ?>
				<a href="https://layerslider.kreaturamedia.com/sliders/" target="_blank" class="button button-primary">
					<?php _e('View Selection', 'LayerSlider') ?>
				</a>
				<?php else : ?>
					<a href="<?php echo admin_url( 'admin.php?page=layerslider#open-template-store' ) ?>" class="button button-primary">
						<?php _e('Visit Template Store', 'LayerSlider') ?>
					</a>
				<?php endif ?>
			</div>
		</div>


		<!-- Popups -->
		<div id="ls-addon-popups" class="ls-addon-item">
			<div class="ls-addon-bg"></div>
			<h3 class="ls-addon-title"><?php _e('Popups', 'LayerSlider') ?></h3>
			<div class="ls-addon-body">
				<?php _e('Use sliders as a floating modal window with extensive layout options and advanced features like triggers & target audience.', 'LayerSlider') ?>
			</div>
			<?php if( ! $isActivated ) : ?>
			<div class="ls-addon-footer">
				<a href="https://layerslider.kreaturamedia.com/features/popups/" target="_blank" class="button button-primary"><?php _e('Preview &amp; Details', 'LayerSlider') ?></a>
			</div>
			<?php endif ?>
		</div>


		<!-- Revisions -->
		<div id="ls-addon-revisions" class="ls-addon-item ls-addon-light">
			<div class="ls-addon-bg"></div>
			<h3 class="ls-addon-title"><?php _e('Revisions', 'LayerSlider') ?></h3>
			<div class="ls-addon-body">
				<?php _e('Have a peace of mind knowing that your slider edits are always safe and you can revert back unwanted changes or faulty saves at any time. Revisions serves not just as a backup solution, but a complete version control system where you can visually compare the changes you have made along the way.', 'LayerSlider') ?>
			</div>
			<div class="ls-addon-footer">

				<?php if( ! $isActivated ) : ?>
				<a href="https://layerslider.kreaturamedia.com/features/revisions/" target="_blank" class="button button-primary">
					<?php _e('Preview &amp; Details', 'LayerSlider') ?>
				</a>
				<?php else : ?>
				<a href="#" class="button button-primary ls-revisions-options">
					<?php _e('Revisions Preferences', 'LayerSlider') ?>
				</a>
				<?php endif ?>
			</div>
		</div>


		<!-- Origami -->
		<div id="ls-addon-origami" class="ls-addon-item">
			<div class="ls-addon-bg"></div>
			<h3 class="ls-addon-title"><?php _e('Origami Slide Transition', 'LayerSlider') ?></h3>
			<div class="ls-addon-body">
				<?php _e('Origami is the perfect solution to share your gorgeous photos with the world or your loved ones in a truly inspirational way and create sliders with stunning effects.', 'LayerSlider') ?>
			</div>
			<?php if( ! $isActivated ) : ?>
			<div class="ls-addon-footer">
				<a href="https://layerslider.kreaturamedia.com/sliders/origami/" target="_blank" class="button button-primary"><?php _e('Preview Feature', 'LayerSlider') ?></a>
			</div>
			<?php endif ?>
		</div>


		<!-- Play By Scroll -->
		<div id="ls-addon-play-by-scroll" class="ls-addon-item ls-addon-light">
			<div class="ls-addon-bg"></div>
			<h3 class="ls-addon-title"><?php _e('Play By Scroll', 'LayerSlider') ?></h3>
			<div class="ls-addon-body">
				<?php _e('By using the Play By Scroll feature, you can interact sliders by scrolling with your mouse wheel or swiping up / down on mobile devices. Adding scroll-dependent interactive page blocks to your site has never been easier.', 'LayerSlider') ?>
			</div>
			<?php if( ! $isActivated ) : ?>
			<div class="ls-addon-footer">
				<a href="https://layerslider.kreaturamedia.com/sliders/play-by-scroll/" target="_blank" class="button button-primary"><?php _e('Preview Feature', 'LayerSlider') ?></a>
			</div>
			<?php endif ?>
		</div>


		<!-- Blend Mode -->
		<div id="ls-addon-blend-mode" class="ls-addon-item">
			<div class="ls-addon-bg">
				<div class="ls-addon-extras">
					<div class="ls-addon-e1"></div>
					<div class="ls-addon-e2"></div>
				</div>
			</div>
			<h3 class="ls-addon-title"><?php _e('Blend Mode', 'LayerSlider') ?></h3>
			<div class="ls-addon-body">
				<?php _e('Blend modes are an easy way to add eye-catching effects and is a frequently used feature in graphic and print design. With Blend Mode, you can apply texture to text or blend multiple images together in interesting ways.', 'LayerSlider') ?>
			</div>
		</div>


		<!-- Filters -->
		<div id="ls-addon-filters" class="ls-addon-item ls-addon-light">
			<div class="ls-addon-bg"></div>
			<h3 class="ls-addon-title"><?php _e('Filters', 'LayerSlider') ?></h3>
			<div class="ls-addon-body">
				<?php _e('Apply and animate filters on layers. Filters include: blur, brightness, contrast, drop shadow, grayscale, hue rotate, invert, saturation and sepia.', 'LayerSlider') ?>
			</div>
		</div>

	</div>
</div>
