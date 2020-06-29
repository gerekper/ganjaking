<?php

// Prevent direct file access
defined( 'LS_ROOT_FILE' ) || exit;

include LS_ROOT_PATH . '/templates/tmpl-revisions-preferences.php';

?>
<div id="ls-revisions-welcome">

	<div class="wrap">

		<?php if( ! empty( $notification ) ) : ?>
		<div class="ls-notification-info">
			<i class="dashicons dashicons-info"></i>
			<?php echo $notification ?>
		</div>
		<?php endif ?>

		<?php if( ! LS_Config::isActivatedSite() ) : ?>
		<div class="ls-notification-info">
			<i class="dashicons dashicons-info"></i>
			<?php echo sprintf(__('Slider Revisions is a premium feature. Activate your copy of LayerSlider in order to enjoy our premium benefits. %sPurchase a license%s or %sread the documentation%s to learn more. %sGot LayerSlider in a theme?%s', 'LayerSlider'), '<a href="'.LS_Config::get('purchase_url').'" target="_blank">', '</a>', '<a href="https://layerslider.kreaturamedia.com/documentation/#activation" target="_blank">', '</a>', '<a href="https://layerslider.kreaturamedia.com/documentation/#activation-bundles" target="_blank">', '</a>') ?>
		</div>
		<?php endif ?>

		<h1><?php _e('You Can Now Rewind Time', 'LayerSlider') ?></h1>
		<p class="center">
			<?php echo _e('Have a peace of mind knowing that your slider edits are always safe and you can revert back unwanted changes or faulty saves at any time. This feature serves not just as a backup solution, but a complete version control system where you can visually compare the changes you have made along the way.', 'LayerSlider') ?>
			<br><br>
			<a href="#" class="ls-revisions-options"><?php _e('Customize Revisions Preferences', 'LayerSlider') ?></a>
			<a target="_blank" href="https://layerslider.kreaturamedia.com/documentation/#builder-revisions" class="ls-revisions-more-info"><?php _e('More Information', 'LayerSlider') ?></a>
		</p>
		<div class="center">
			<video autoplay loop muted poster="<?php echo LS_ROOT_URL ?>/static/admin/img/revisions.jpg">
				<source src="https://cdn1.kreaturamedia.com/media/revisions.mp4" type="video/mp4">
			</video>
		</div>
	</div>

</div>