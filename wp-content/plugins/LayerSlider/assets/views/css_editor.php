<?php

	// Prevent direct file access
	defined( 'LS_ROOT_FILE' ) || exit;

	// Get uploads dir
	$upload_dir = wp_upload_dir();
	$file = $upload_dir['basedir'].'/layerslider.custom.css';

	// Get contents
	$contents = file_exists($file) ? file_get_contents($file) : '';

	// Get screen options
	$lsScreenOptions = get_option('ls-screen-options', '0');
	$lsScreenOptions = ($lsScreenOptions == 0) ? array() : $lsScreenOptions;
	$lsScreenOptions = is_array($lsScreenOptions) ? $lsScreenOptions : unserialize($lsScreenOptions);

	// Defaults
	if(!isset($lsScreenOptions['showTooltips'])) {
		$lsScreenOptions['showTooltips'] = 'true';
	}
?>

<div id="ls-screen-options" class="metabox-prefs hidden">
	<div id="screen-options-wrap" class="hidden">
		<form id="ls-screen-options-form" method="post">
			<?php wp_nonce_field('ls-save-screen-options'); ?>
			<h5><?php _e('Show on screen', 'LayerSlider') ?></h5>
			<label>
				<input type="checkbox" name="showTooltips"<?php echo $lsScreenOptions['showTooltips'] == 'true' ? ' checked="checked"' : ''?>> <?php _e('Tooltips', 'LayerSlider') ?>
			</label>
		</form>
	</div>
	<div id="screen-options-link-wrap" class="hide-if-no-js screen-meta-toggle">
		<button type="button" id="show-settings-link" class="button show-settings" aria-controls="screen-options-wrap" aria-expanded="false"><?php _e('Screen Options', 'LayerSlider') ?></button>
	</div>
</div>
<div class="wrap">

	<!-- Page title -->
	<h2>
		<?php _e('LayerSlider CSS Editor', 'LayerSlider') ?>
		<a href="<?php echo admin_url('admin.php?page=layerslider-options') ?>" class="add-new-h2"><?php _e('&larr; Options', 'LayerSlider') ?></a>
	</h2>

	<!-- Error messages -->
	<?php if(isset($_GET['edited'])) : ?>
	<div class="ls-notification updated">
		<div><?php _e('Your changes has been saved!', 'LayerSlider') ?></div>
	</div>
	<?php endif; ?>
	<!-- End of error messages -->

	<!-- Editor box -->
	<div class="ls-box ls-skin-editor-box">
		<h3 class="header medium">
			<?php _e('Contents of your custom CSS file', 'LayerSlider') ?>
			<figure><span>|</span><?php _e('Ctrl+Q to fold/unfold a block', 'LayerSlider') ?></figure>
		</h3>
		<form method="post" class="inner">
			<input type="hidden" name="ls-user-css" value="1">
			<?php wp_nonce_field('save-user-css'); ?>
			<textarea rows="10" cols="50" name="contents" class="ls-codemirror"><?php if(!empty($contents)) {
					echo htmlentities($contents);
				} else {
					echo '/*' . NL . __('You can type here custom CSS code, which will be loaded both on your admin and front-end pages. Please make sure to not override layout properties (positions and sizes), as they can interfere with the sliders built-in responsive functionality. Here are few example targets to help you get started:', 'LayerSlider');
					echo NL . '*/' . NL . NL;
					echo '.ls-container { /* Slider container */' . NL . NL . '}' .NL.NL;
					echo '.ls-layers { /* Layers wrapper */ ' . NL  . NL . '}' . NL.NL;
					echo '.ls-3d-box div { /* Sides of 3D transition objects */ ' . NL  . NL . '}';
				}?></textarea>
			<p class="footer">
				<?php if(!is_writable($upload_dir['basedir'])) { ?>
				<?php sprintf(__('You need to make your uploads folder writable in order to save your changes. See the %sCodex%s for more information.', 'LayerSlider'), '<a href="https://wordpress.org/support/article/changing-file-permissions/" target="_blank">', '</a>') ?>
				<?php } else { ?>
				<button class="button-primary"><?php _e('Save changes', 'LayerSlider') ?></button>
				<?php _e('Using invalid CSS code could break the appearance of your site or your sliders. Changes cannot be reverted after saving.','LayerSlider') ?>
				<?php } ?>
			</p>
		</form>
	</div>
</div>
<script type="text/javascript">
	// Screen options
	var lsScreenOptions = <?php echo json_encode($lsScreenOptions) ?>;
</script>
