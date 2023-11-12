<?php
/**
 * White Label Form
 *
 * @package UAEL
 */

if ( defined( 'WP_UAEL_WL' ) && WP_UAEL_WL ) {
	return;
}

use UltimateElementor\Classes\UAEL_Helper;
$settings = UAEL_Helper::get_white_labels();

?>
<div class="uael-container uael-branding-wrapper">
<form method="post" class="wrap clear" action="" >
<div class="wrap uael-addon-wrap clear">
	<h1 class="screen-reader-text"><?php esc_html_e( 'White Label', 'uael' ); ?></h1>
	<div id="poststuff">
		<div id="post-body" class="columns-2">
			<div id="post-body-content">
				<ul class="uael-branding-list">
					<li>
						<div class="branding-form postbox">
							<h2 class="hndle ui-sortable-handle">
								<span><?php esc_html_e( 'Author Details', 'uael' ); ?></span>
							</h2>
							<div class="inside">
								<div class="form-wrap">
									<div class="form-field">
										<label for="uael-wl-agency-author"><?php esc_html_e( 'Author:', 'uael' ); ?></label>
										<input type="text" name="uael_white_label[agency][author]" id="uael-wl-agency-author" placeholder="Brainstorm Force" class="placeholder placeholder-active" value="<?php echo esc_attr( $settings['agency']['author'] ); ?>">
									</div>
									<div class="form-field">
										<label for="uael-wl-agency-author-url"><?php esc_html_e( 'Author URL:', 'uael' ); ?></label>
										<input type="url" placeholder="https://www.brainstormforce.com" name="uael_white_label[agency][author_url]" id="uael-wl-agency-author-url" class="placeholder placeholder-active" value="<?php echo esc_url( $settings['agency']['author_url'] ); ?>">
									</div>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</li>
					<li>
						<div class="branding-form postbox">
							<h2 class="hndle ui-sortable-handle">
								<span><?php esc_html_e( 'Plugin Details', 'uael' ); ?></span>
							</h2>

							<div class="inside">
								<div class="form-wrap">
									<div class="form-field">
										<label for="uael-wl-plugin-name"><?php esc_html_e( 'Plugin Name:', 'uael' ); ?></label>
										<input type="text" placeholder="<?php echo esc_attr( UAEL_PLUGIN_NAME ); ?>" name="uael_white_label[plugin][name]" id="uael-wl-plugin-name" class="placeholder placeholder-active" value="<?php echo esc_attr( $settings['plugin']['name'] ); ?>">
									</div>
									<div class="form-field">
										<label for="uael-wl-plugin-short_name"><?php esc_html_e( 'Plugin Short Name:', 'uael' ); ?></label>
										<input type="text" name="uael_white_label[plugin][short_name]" id="uael-wl-plugin-short_name" placeholder="<?php echo esc_attr( UAEL_PLUGIN_SHORT_NAME ); ?>" class="placeholder placeholder-active" value="<?php echo esc_attr( $settings['plugin']['short_name'] ); ?>">
									</div>
									<div class="form-field">
										<label for="uael-wl-plugin-desc"><?php esc_html_e( 'Plugin Description:', 'uael' ); ?></label>
										<textarea name="uael_white_label[plugin][description]" id="uael-wl-plugin-desc" placeholder="Ultimate Addons is a premium extension for Elementor that adds 35+ widgets and works on top of any Elementor Package (Free, Pro). You can use it with any WordPress theme." class="placeholder placeholder-active" rows="2"><?php echo esc_html( $settings['plugin']['description'] ); ?></textarea>
									</div>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</li>
					<li>
						<div class="branding-form postbox">
							<h2 class="hndle ui-sortable-handle">
								<span><?php esc_html_e( 'White Label Settings', 'uael' ); ?></span>
							</h2>
							<div class="inside">
								<div class="form-wrap">
									<div class="form-field">
										<p>
										<label for="uael-wl-hide-branding">
											<input type="checkbox" id="uael-wl-hide-branding" name="uael_white_label[agency][hide_branding]" value="1" <?php checked( $settings['agency']['hide_branding'], '1' ); ?>>
											<?php esc_attr_e( 'Hide White Label', 'uael' ); ?>
										</label>
										</p>
										<p class="admin-help"><?php esc_html_e( 'Enable this option to hide White Label settings. Re-activate the Ultimate Addons for Elementor to enable this settings tab again.', 'uael' ); ?></p>
									</div>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</li>
					<?php
					// Add form for white label with <li> element.
					do_action( 'uael_white_label_add_form', $settings );
					?>
				</ul>
			</div>
			<div class="postbox-container" id="postbox-container-1">
				<div id="side-sortables">
					<div class="postbox">
						<h2 class="hndle"><span><?php esc_html_e( 'Helpful Information', 'uael' ); ?></span>
						</h2>
						<div class="inside">
							<div class="form-wrap">
								<div class="form-field uael-p">
									<p class="admin-help uael-p"><?php esc_html_e( 'Not sure how White Label works? Take a look at below article and learn.', 'uael' ); ?></p>
									<a href='<?php echo esc_url( UAEL_DOMAIN . 'docs/how-to-white-label-uael/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin' ); ?> ' target="_blank" rel="noopener"><?php esc_html_e( 'How to White Label UAE? »', 'uael' ); ?></a>
								</div>
							</div>
						</div>
					</div>
					<div class="postbox">
						<h2 class="hndle"><span><?php esc_html_e( 'Admin Area', 'uael' ); ?></span>
						</h2>
						<div class="inside">
							<div class="form-wrap">
								<div class="form-field">
									<p>
									<label for="uael-wl-replace-logo">
										<input type="checkbox" id="uael-wl-replace-logo" name="uael_white_label[replace_logo]" value="enable" <?php checked( $settings['replace_logo'], 'enable' ); ?>>
										<?php esc_attr_e( 'Replace Logo', 'uael' ); ?>
									</label>
									</p>
									<p class="admin-help"><?php esc_html_e( 'Replace the header logo with your plugin name.', 'uael' ); ?></p>
								</div>
								<hr>
								<div class="form-field">
									<p>
									<label for="uael-wl-enable-custom-tag-line">
										<input type="checkbox" id="uael-wl-enable-custom-tag-line" name="uael_white_label[enable_custom_tagline]" value="enable" <?php checked( $settings['enable_custom_tagline'], 'enable' ); ?>>
										<?php esc_attr_e( 'Hide Tagline', 'uael' ); ?>
									</label>
									</p>
									<p class="admin-help"><?php esc_html_e( 'Hide "Take Elementor to The Next Level!" tagline in the header.', 'uael' ); ?></p>
								</div>
								<hr>
								<div class="form-field">
									<p>
									<label for="uael-wl-enable-knowledgebase">
										<input type="checkbox" id="uael-wl-enable-knowledgebase" name="uael_white_label[enable_knowledgebase]" value="enable" <?php checked( $settings['enable_knowledgebase'], 'enable' ); ?>>
										<?php esc_attr_e( 'Display Knowledge Base Box', 'uael' ); ?>
									</label>
									</p>
									<p class="uael-knowledgebase-url">
									<label for="uael-wl-knowledgebase-url"><?php esc_html_e( 'Knowledge Base URL', 'uael' ); ?></label>
									<input type="text" placeholder='<?php echo esc_url( UAEL_DOMAIN . 'docs/' ); ?>' name="uael_white_label[knowledgebase_url]" id="uael-wl-knowledgebase-url" class="placeholder placeholder-active" value="<?php echo esc_url( $settings['knowledgebase_url'] ); ?>">
									</p>
								</div>
								<hr>
								<div class="form-field">
									<p>
									<label for="uael-wl-enable-support">
										<input type="checkbox" id="uael-wl-enable-support" name="uael_white_label[enable_support]" value="enable" <?php checked( $settings['enable_support'], 'enable' ); ?>>
										<?php esc_attr_e( 'Display Support Box', 'uael' ); ?>
									</label>
									</p>
									<p class="uael-support-url">
									<label for="uael-wl-support-url"><?php esc_html_e( 'Support URL', 'uael' ); ?></label>
									<input type="text" placeholder='<?php echo esc_url( UAEL_DOMAIN . 'support/' ); ?>' name="uael_white_label[support_url]" id="uael-wl-support-url" class="placeholder placeholder-active" value="<?php echo esc_url( $settings['support_url'] ); ?>">
									</p>
								</div>
								<hr>
								<div class="form-field">
									<p>
									<label for="uael-wl-enable-beta-box">
										<input type="checkbox" id="uael-wl-enable-beta-box" name="uael_white_label[enable_beta_box]" value="enable" <?php checked( $settings['enable_beta_box'], 'enable' ); ?>>
										<?php esc_attr_e( 'Display Beta Update Box', 'uael' ); ?>
									</label>
									</p>
								</div>
							</div>
						</div>
					</div>
					<div class="postbox">
						<h2 class="hndle"><span><?php esc_html_e( 'Editor Area', 'uael' ); ?></span>
						</h2>
						<div class="inside">
							<div class="form-wrap">
								<div class="form-field">
									<p>
									<label for="uael-wl-internal-help-links">
										<input type="checkbox" id="uael-wl-internal-help-links" name="uael_white_label[internal_help_links]" value="enable" <?php checked( $settings['internal_help_links'], 'enable' ); ?>>
										<?php esc_attr_e( 'Display Help Links', 'uael' ); ?>
									</label>
									</p>
									<p class="admin-help"><?php esc_html_e( 'Display internal help links in widget editor area.', 'uael' ); ?></p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php submit_button( __( 'Save Changes', 'uael' ), 'uael-save-wl-options button-primary button button-hero' ); ?>
			<?php if ( is_multisite() ) : ?>
				<p class="install-help"><strong><?php esc_html_e( 'Note:', 'uael' ); ?></strong>  <?php esc_html_e( 'Whitelabel settings are applied to all the sites in the Network.', 'uael' ); ?></p>
			<?php endif; ?>
			<?php wp_nonce_field( 'white-label', 'uael-white-label-nonce' ); ?>
		</div>
		<!-- /post-body -->
		<br class="clear">
	</div>
</div>
</form>
<br class="clear">
</div>
