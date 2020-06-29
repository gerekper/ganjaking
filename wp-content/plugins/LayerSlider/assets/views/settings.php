<?php

// Prevent direct file access
defined( 'LS_ROOT_FILE' ) || exit;

include LS_ROOT_PATH . '/helpers/admin.ui.tools.php';

// Custom capability
$custom_capability = $custom_role = get_option('layerslider_custom_capability', 'manage_options');

// Privacy
$gdpr_google_fonts = get_option('layerslider-google-fonts-enabled', true );


$default_capabilities = array(
	'manage_network',
	'manage_options',
	'publish_pages',
	'publish_posts',
	'edit_posts'
);

if( in_array( $custom_capability, $default_capabilities ) ) {
	$custom_capability = '';
} else {
	$custom_role = 'custom';
}

// Google Fonts
$googleFonts 		= get_option( 'ls-google-fonts', array() );
$googleFontScripts 	= get_option( 'ls-google-font-scripts', array( 'latin', 'latin-ext' ) );

// Notification messages
$notifications = array(

	'cacheEmpty' => __('Successfully emptied LayerSlider caches.', 'LayerSlider'),
	'permissionError' => __('Your account does not have the necessary permission you have chosen, and your settings have not been saved in order to prevent locking yourself out of the plugin.', 'LayerSlider'),
	'permissionSuccess' => __('Permission settings has been updated.', 'LayerSlider'),
	'privacySuccess' => __('Privacy settings has been updated.', 'LayerSlider'),
	'googleFontsUpdated' => __('Your Google Fonts library has been updated.', 'LayerSlider'),
	'generalUpdated' => __('Your settings have been updated.', 'LayerSlider')
);
?>

<!-- WP hack to place notification at the top of page -->
<div class="wrap ls-wp-hack">
	<h2></h2>

	<!-- Error messages -->
	<?php if(isset($_GET['message'])) : ?>
	<div class="ls-notification large <?php echo isset($_GET['error']) ? 'error' : 'updated' ?>">
		<div><?php echo $notifications[ $_GET['message'] ] ?></div>
	</div>
	<?php endif; ?>
	<!-- End of error messages -->
</div>

<div class="wrap ls-settings-page">

	<!-- Page title -->
	<h2>
		<?php _e('LayerSlider Settings', 'LayerSlider') ?>
		<a href="<?php echo admin_url('admin.php?page=layerslider') ?>" class="add-new-h2"><?php _e('&larr; Sliders', 'LayerSlider') ?></a>
	</h2>

	<!-- Plugin Settings -->
	<div class="km-tabs ls-plugin-settings-tabs">
		<a href="#" class="active"><?php _e('Permissions', 'LayerSlider') ?></a>
		<a href="#"><?php _e('Privacy', 'LayerSlider') ?></a>
		<a href="#"><?php _e('Google Fonts', 'LayerSlider') ?></a>
		<a href="#"><?php _e('Advanced', 'LayerSlider') ?></a>
	</div>
	<div class="km-tabs-content ls-plugin-settings">


		<!-- Permissions -->
		<div class="active">
			<figure><?php _e('Allow non-admin users to change plugin settings and manage your sliders', 'LayerSlider') ?></figure>
			<form method="post" class="ls-box km-tabs-inner" id="ls-permission-form">
				<?php wp_nonce_field('save-access-permissions'); ?>
				<input type="hidden" name="ls-access-permission" value="1">
				<div class="inner">
					<?php _e('Choose a role', 'LayerSlider') ?>
					<select name="custom_role">
						<?php if( is_multisite() ) : ?>
						<option value="manage_network" <?php echo ($custom_role == 'manage_network') ? 'selected="selected"' : '' ?>> <?php _e('Super Admin', 'LayerSlider') ?></option>
						<?php endif; ?>
						<option value="manage_options" <?php echo ($custom_role == 'manage_options') ? 'selected="selected"' : '' ?>> <?php _e('Admin', 'LayerSlider') ?></option>
						<option value="publish_pages" <?php echo ($custom_role == 'publish_pages') ? 'selected="selected"' : '' ?>> <?php _e('Editor, Admin', 'LayerSlider') ?></option>
						<option value="publish_posts" <?php echo ($custom_role == 'publish_posts') ? 'selected="selected"' : '' ?>> <?php _e('Author, Editor, Admin', 'LayerSlider') ?></option>
						<option value="edit_posts" <?php echo ($custom_role == 'edit_posts') ? 'selected="selected"' : '' ?>> <?php _e('Contributor, Author, Editor, Admin', 'LayerSlider') ?></option>
						<option value="custom" <?php echo ($custom_role == 'custom') ? 'selected="selected"' : '' ?>> <?php _e('Custom', 'LayerSlider') ?></option>
					</select>

					<i><?php _e('or', 'LayerSlider') ?></i> <?php _e('enter a custom capability', 'LayerSlider') ?>
					<input type="text" name="custom_capability" value="<?php echo $custom_capability ?>" placeholder="<?php _e('Enter custom capability', 'LayerSlider') ?>">

					<p><?php echo sprintf(__('You can specify a custom capability if none of the pre-defined roles match your needs. You can find all the available capabilities on %sthis%s page.', 'LayerSlider'), '<a href="https://wordpress.org/support/article/roles-and-capabilities/#capability-vs-role-table" target="_blank">', '</a>') ?></a>.</p>
				</div>
				<div class="footer">
					<button class="button button-primary"><?php _e('Update', 'LayerSlider') ?></button>
				</div>
			</form>
		</div>


		<!-- Privacy -->
		<div>

			<figure><?php _e('Enable or disable external services to protect your privacy.', 'LayerSlider') ?></figure>
			<form method="post" id="ls-privacy-form" class="ls-box km-tabs-inner ls-global-settings">
				<input type="hidden" name="ls_save_gdpr_settings" value="1">
				<?php wp_nonce_field('ls-save-gdpr-settings'); ?>
				<table>
					<tr>
						<td><?php _e('Enable Google Fonts', 'LayerSlider') ?></td>
						<td data-warning="<?php _e("Many of our importable content in the Template Store use and rely on Google Fonts. If you disable this feature, you may not be able to add custom fonts and it might compromise the appearance of textual content in sliders. \n\n Are you sure you want to disable Google Fonts?", 'LayerSlider') ?>">
							<?php echo lsGetOptionField('checkbox', 'ls_gdpr_goole_fonts', $gdpr_google_fonts) ?>
						</td>
						<td class="desc">
							<?php echo sprintf( __('Google Fonts offers hundreds of custom fonts and is one of the most popular web services to customize website appearance with beautiful typography. Many of our importable content in the Template Store use and rely on Google Fonts. If you disable this feature, you may not be able to add custom fonts and it might compromise the appearance of textual content in sliders. Google might be able to track your activity when using their services. Please review Google’s %sPrivacy Policy%s and %sGDPR Compliance%s. As an external service, you can choose to disable Google Fonts if you disagree with Google’s data processing methods.', 'LayerSlider'), '<a href="https://privacy.google.com/" target="_blank">', '</a>', '<a href="https://privacy.google.com/businesses/compliance" target="_blank">', '</a>') ?>
						</td>
					</tr>
				</table>

				<div class="footer">
					<button type="submit" class="button button-primary"><?php _e('Save changes', 'LayerSlider') ?></button>
				</div>
			</form>
		</div>


		<!-- Google Fonts -->
		<div>
			<figure><?php _e('Choose from hundreds of custom fonts faces provided by Google Fonts', 'LayerSlider') ?></figure>
			<form method="post" class="ls-box km-tabs-inner ls-google-fonts">
				<?php wp_nonce_field('save-google-fonts'); ?>
				<input type="hidden" name="ls-save-google-fonts" value="1">

				<!-- Google Fonts list -->
				<div class="inner">
					<ul class="ls-font-list">
						<li class="ls-hidden">
							<a href="#" class="remove dashicons dashicons-dismiss" title="<?php _e('Remove this font', 'LayerSlider') ?>"></a>
							<input type="text" data-name="urlParams" readonly>
							<input type="checkbox" data-name="onlyOnAdmin">
							<?php _e('Load only on admin interface', 'LayerSlider') ?>
						</li>
						<?php if(is_array($googleFonts) && !empty($googleFonts)) : ?>
						<?php foreach($googleFonts as $item) : ?>
						<li>
							<a href="#" class="remove dashicons dashicons-dismiss" title="<?php _e('Remove this font', 'LayerSlider') ?>"></a>
							<input type="text" data-name="urlParams" value="<?php echo htmlspecialchars($item['param']) ?>" readonly>
							<input type="checkbox" data-name="onlyOnAdmin" <?php echo $item['admin'] ? ' checked="checked"' : '' ?>>
							<?php _e('Load only on admin interface', 'LayerSlider') ?>
						</li>
						<?php endforeach ?>
						<?php else : ?>
						<li class="ls-notice"><?php _e('You haven’t added any Google Font to your collection yet.', 'LayerSlider') ?></li>
						<?php endif ?>
					</ul>
				</div>
				<div class="inner ls-font-search">

					<input type="text" placeholder="<?php _e('Enter a font name to add to your collection', 'LayerSlider') ?>">
					<button class="button"><?php _e('Search', 'LayerSlider') ?></button>

					<!-- Google Fonts search pointer -->
					<div class="ls-box ls-pointer">
						<h3 class="header"><?php _e('Choose a font family', 'LayerSlider') ?></h3>
						<div class="fonts">
							<ul class="inner"></ul>
						</div>
						<div class="variants">
							<ul class="inner"></ul>
							<div class="inner">
								<button class="button add-font"><?php _e('Add font', 'LayerSlider') ?></button>
								<button class="button right"><?php _e('Back to results', 'LayerSlider') ?></button>
							</div>
						</div>
					</div>
				</div>

				<!-- Google Fonts search bar -->
				<div class="inner footer">
					<button type="submit" class="button button-primary"><?php _e('Save changes', 'LayerSlider') ?></button>
					<?php
						$scripts = array(
							'arabic' => __('Arabic', 'LayerSlider'),
							'bengali' => __('Bengali', 'LayerSlider'),
							'cyrillic' => __('Cyrillic', 'LayerSlider'),
							'cyrillic-ext' => __('Cyrillic Extended', 'LayerSlider'),
							'devanagari' => __('Devanagari', 'LayerSlider'),
							'greek' => __('Greek', 'LayerSlider'),
							'greek-ext' => __('Greek Extended', 'LayerSlider'),
							'gujarati' => __('Gujarati', 'LayerSlider'),
							'gurmukhi' => __('Gurmukhi', 'LayerSlider'),
							'hebrew' => __('Hebrew', 'LayerSlider'),
							'kannada' => __('Kannada', 'LayerSlider'),
							'khmer' => __('Khmer', 'LayerSlider'),
							'latin' => __('Latin', 'LayerSlider'),
							'latin-ext' => __('Latin Extended', 'LayerSlider'),
							'malayalam' => __('Malayalam', 'LayerSlider'),
							'myanmar' => __('Myanmar', 'LayerSlider'),
							'oriya' => __('Oriya', 'LayerSlider'),
							'sinhala' => __('Sinhala', 'LayerSlider'),
							'tamil' => __('Tamil', 'LayerSlider'),
							'telugu' => __('Telugu', 'LayerSlider'),
							'thai' => __('Thai', 'LayerSlider'),
							'vietnamese' => __('Vietnamese', 'LayerSlider')
						);
					?>
					<div class="right">
						<div>
							<select>
								<option><?php _e('Select new', 'LayerSlider') ?></option>
								<?php foreach($scripts as $key => $val) : ?>
								<option value="<?php echo $key ?>"><?php echo $val ?></option>
								<?php endforeach ?>
							</select>
						</div>
						<ul class="ls-google-font-scripts">
							<li class="ls-hidden">
								<span></span>
								<a href="#" class="dashicons dashicons-dismiss" title="<?php _e('Remove character set', 'LayerSlider') ?>"></a>
								<input type="hidden" name="scripts[]" value="">
							</li>
							<?php if(!empty($googleFontScripts) && is_array($googleFontScripts)) : ?>
							<?php foreach($googleFontScripts as $item) : ?>
							<li>
								<span><?php echo $scripts[$item] ?></span>
								<a href="#" class="dashicons dashicons-dismiss" title="<?php _e('Remove character set', 'LayerSlider') ?>"></a>
								<input type="hidden" name="scripts[]" value="<?php echo $item ?>">
							</li>
							<?php endforeach ?>
							<?php else : ?>
							<li>
								<span>Latin</span>
								<a href="#" class="dashicons dashicons-dismiss" title="<?php _e('Remove character set', 'LayerSlider') ?>"></a>
								<input type="hidden" name="scripts[]" value="latin">
							</li>
							<li>
								<span>Latin Extended</span>
								<a href="#" class="dashicons dashicons-dismiss" title="<?php _e('Remove character set', 'LayerSlider') ?>"></a>
								<input type="hidden" name="scripts[]" value="latin-ext">
							</li>
							<?php endif ?>
						</ul>
						<div><?php _e('Use character sets:', 'LayerSlider') ?></div>
					</div>
				</div>

			</form>
		</div>


		<!-- Advanced -->
		<div class="ls-global-settings">
			<figure>
				<?php _e('These options can help to increase performance and avoid 3rd party issues.', 'LayerSlider') ?>
				<span class="warning"><?php _e('Be careful with these options as incorrect settings might cause unexpected issues.', 'LayerSlider') ?></span>
			</figure>
			<form method="post" class="ls-box km-tabs-inner">
				<?php wp_nonce_field('save-advanced-settings'); ?>
				<input type="hidden" name="ls-save-advanced-settings">
				<figure class="ls-first">
					<?php _e('Performance Related Options') ?>
				</figure>
				<table>
					<tr class="ls-cache-options">
						<td><?php _e('Use slider markup caching', 'LayerSlider') ?></td>
						<td>
							<?php echo lsGetOptionField('checkbox', 'use_cache', true) ?>
						</td>
						<td class="desc">
							<?php _e('Enabled caching can drastically increase the plugin performance and spare your server from unnecessary load. LayerSlider will serve fresh, non-cached versions for admins and anyone who can manage sliders.', 'LayerSlider') ?>
							<a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=layerslider&action=empty_caches' ), 'empty_caches') ?>" class="button button-small"><?php _e('Empty caches', 'LayerSlider') ?></a>
						</td>
					</tr>
					<tr>
						<td><?php _e('Include scripts in the footer', 'LayerSlider') ?></td>
						<td>
							<?php echo lsGetOptionField('checkbox', 'include_at_footer', false) ?>
						<td class="desc"><?php _e('Including resources in the footer can improve load times and solve other type of issues. Outdated themes might not support this method.', 'LayerSlider') ?></td>
					</tr>
					<tr>
						<td><?php _e('Conditional script loading', 'LayerSlider') ?></td>
						<td>
							<?php echo lsGetOptionField('checkbox', 'conditional_script_loading', false) ?>
						</td>
						<td class="desc"><?php _e('Increase your site’s performance by loading resources only when necessary. Outdated themes might not support this method.', 'LayerSlider') ?></td>
					</tr>
					<tr>
						<td><?php _e('Concatenate output', 'LayerSlider') ?></td>
						<td>
							<?php echo lsGetOptionField('checkbox', 'concatenate_output', false) ?>
						</td>
						<td class="desc"><?php _e('Concatenating the plugin’s output could solve issues caused by custom filters your theme might use.', 'LayerSlider') ?></td>
					</tr>
					<tr>
						<td><?php _e('Defer JavaScript loading', 'LayerSlider') ?></td>
						<td>
							<?php echo lsGetOptionField('checkbox', 'defer_scripts', false) ?>
						</td>
						<td class="desc"><?php _e('Eliminates render-blocking JavaScript files, but might also delay a bit displaying sliders above the fold.', 'LayerSlider') ?></td>
					</tr>
				</table>



				<figure>
					<?php _e('Troubleshooting &amp; Advanced Settings', 'LayerSlider') ?>
				</figure>
				<table>
					<tr>
						<td><?php _e('RocketScript compatibility', 'LayerSlider') ?></td>
						<td>
							<?php echo lsGetOptionField('checkbox', 'rocketscript_ignore', false) ?>
						</td>
						<td class="desc"><?php _e('Enable this option to ignore LayerSlider files by CloudFlare’s Rocket Loader, which can help overcoming potential issues.', 'LayerSlider') ?></td>

					</tr>
					<tr>
						<td><?php _e('Always load all JavaScript files', 'LayerSlider') ?></td>
						<td>
							<?php echo lsGetOptionField('checkbox', 'load_all_js_files', false) ?>
						</td>
						<td class="desc"><?php _e('Enabling this option will likely help if you’re experiencing issues with CDN services or JavaScript minify/combine features in a 3rd party plugin. However, it can also negatively impact performance since resources will not be loaded conditionally.', 'LayerSlider' ) ?></td>
					</tr>
					<tr>
						<td><?php _e('Use GreenSock (GSAP) sandboxing', 'LayerSlider') ?></td>
						<td>
							<?php echo lsGetOptionField('checkbox', 'gsap_sandboxing', true) ?>
						</td>
						<td class="desc"><?php _e('Enabling GreenSock sandboxing can solve issues when other plugins are using multiple/outdated versions of this library.', 'LayerSlider') ?></td>
					</tr>
					<tr id="ls_use_custom_jquery">
						<td><?php _e('Use Google CDN version of jQuery', 'LayerSlider') ?></td>
						<td>
							<?php echo lsGetOptionField('checkbox', 'use_custom_jquery', false) ?>
						</td>
						<td class="desc"><?php _e('This option will likely solve “Old jQuery” issues, but can easily have other side effects. Use it only when it is necessary.', 'LayerSlider') ?></td>
					</tr>
					<tr>
						<td><?php _e('Scripts priority', 'LayerSlider') ?></td>
						<td>
							<?php echo lsGetOptionField('text', 'scripts_priority', 3, array( 'placeholder' => 3 ) ) ?>
						</td>
						<td class="desc"><?php _e('Used to specify the order in which scripts are loaded. Lower numbers correspond with earlier execution.', 'LayerSlider') ?></td>
					</tr>
				</table>



				<figure>
					<?php _e('Miscellaneous', 'LayerSlider') ?>
				</figure>
				<table>
					<tr>
						<td><?php _e('Suppress debug info', 'LayerSlider') ?></td>
						<td>
							<?php echo lsGetOptionField('checkbox', 'suppress_debug_info', false) ?>
						</td>
						<td class="desc"><?php _e('Hides useful information such as the version number in the browser’s debug console and in the site HTML markup. We recommend leaving this option disabled as it can be a significant help for debugging and supporting LayerSlider.', 'LayerSlider') ?></td>
					</tr>
					<tr>
						<td><?php _e('Enable TinyMCE helper', 'LayerSlider') ?></td>
						<td>
							<?php echo lsGetOptionField('checkbox', 'tinymce_helper', true ) ?>
						</td>
						<td class="desc"><?php _e('Allows the LayerSlider helper utility for the classic WordPress page editor, which makes it easy to insert sliders into your pages. Disable only if you’re experiencing issues with the editor.', 'LayerSlider') ?></td>
					</tr>
					<tr>
						<td><?php _e('Enable Gutenberg block', 'LayerSlider') ?></td>
						<td>
							<?php echo lsGetOptionField('checkbox', 'gutenberg_block', true ) ?>
						</td>
						<td class="desc"><?php _e('Allows the LayerSlider block for  WordPress’s new Gutenberg page editor, which makes it easy to insert sliders into your pages. Disable only if you’re experiencing issues with the editor.', 'LayerSlider') ?></td>
					</tr>
					<tr>
						<td><?php _e('Enable Elementor widget', 'LayerSlider') ?></td>
						<td>
							<?php echo lsGetOptionField('checkbox', 'elementor_widget', true ) ?>
						</td>
						<td class="desc"><?php _e('Allows the LayerSlider widget for Elementor, which makes it easy to insert sliders into your pages. Disable only if you’re experiencing issues with the editor.', 'LayerSlider') ?></td>
					</tr>
				</table>

				<div class="footer">
					<button type="submit" class="button button-primary"><?php _e('Save changes', 'LayerSlider') ?></button>
				</div>
			</form>
		</div>

	</div>

	<!-- System Status -->
	<a class="ls-subpage-link" href="<?php echo admin_url('admin.php?page=layerslider-options&section=system-status') ?>">
		<?php _e('System Status', 'LayerSlider') ?>
		<small><?php _e('Identify possible issues &amp; display relevant debug information.', 'LayerSlider') ?> </small>
		<i class="dashicons dashicons-arrow-right-alt2"></i>
	</a>


	<!-- Skin Editor -->
	<a class="ls-subpage-link" href="<?php echo admin_url('admin.php?page=layerslider-options&section=skin-editor') ?>">
		<?php _e('Skin Editor', 'LayerSlider') ?>
		<small><?php _e('Edit the CSS file of skins to apply modifications.', 'LayerSlider') ?> </small>
		<i class="dashicons dashicons-arrow-right-alt2"></i>
	</a>


	<!-- CSS Editor -->
	<a class="ls-subpage-link" href="<?php echo admin_url('admin.php?page=layerslider-options&section=css-editor') ?>">
		<?php _e('CSS Editor', 'LayerSlider') ?>
		<small><?php _e('Add your own CSS code that will be applied globally on your site.', 'LayerSlider') ?> </small>
		<i class="dashicons dashicons-arrow-right-alt2"></i>
	</a>


	<!-- Transition Builder -->
	<a class="ls-subpage-link" href="<?php echo admin_url('admin.php?page=layerslider-options&section=transition-builder') ?>">
		<?php _e('Transition Builder', 'LayerSlider') ?>
		<small><?php _e('Make new slide transitions easily with this drag &amp; drop editor.', 'LayerSlider') ?> </small>
		<i class="dashicons dashicons-arrow-right-alt2"></i>
	</a>


	<!-- About -->
	<a class="ls-subpage-link" href="<?php echo admin_url('admin.php?page=layerslider-options&section=about') ?>">
		<?php _e('About', 'LayerSlider') ?>
		<small><?php _e('About LayerSlider &amp; useful resources.', 'LayerSlider') ?> </small>
		<i class="dashicons dashicons-arrow-right-alt2"></i>
	</a>

</div>