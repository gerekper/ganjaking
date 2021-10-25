<div class="wp-tab-panel" id="ma_api_keys" style="display: none;">

	<div class="master-addons-el-dashboard-header-wrapper">
		<div class="master-addons-el-dashboard-header-right">
			<button type="submit" class="master-addons-el-btn master-addons-el-js-element-save-setting">
				<?php _e('Save Settings', MELA_TD); ?>
			</button>
		</div>
	</div>

	<?php $jltma_api_options = get_option('jltma_api_save_settings'); ?>

	<form action="" method="POST" id="jltma-api-forms-settings" class="jltma-api-forms-settings" name="jltma-api-forms-settings">

		<div class="master_addons_feature">

			<!-- Start of reCaptcha Settings -->
			<div class="api-settings-element">
				<h3><?php echo esc_html__('reCaptcha Settings', MELA_TD); ?></h3>
				<div class="api-element-inner">
					<div class="api-forms">

						<div class="form-group">
							<label for="recaptcha_site_key">
								<?php echo esc_html__('reCAPTCHA Site key', MELA_TD); ?>
							</label>
							<input name="recaptcha_site_key" type="text" class="form-control recaptcha_site_key" value="<?php echo isset($jltma_api_options['recaptcha_site_key']) ? $jltma_api_options['recaptcha_site_key'] : ""; ?>">
						</div>

						<div class="form-group">
							<label for="recaptcha_secret_key">
								<?php echo esc_html__('reCAPTCHA Secret key', MELA_TD); ?>
							</label>
							<input type="text" name="recaptcha_secret_key" class="form-control recaptcha_secret_key" value="<?php echo isset($jltma_api_options['recaptcha_secret_key']) ? $jltma_api_options['recaptcha_secret_key'] : ""; ?>">
						</div>

						<p>
							<?php echo sprintf(__('Go to your Google <a href="%1$s" target="_blank"> reCAPTCHA</a> > Account > Generate Keys (reCAPTCHA V2 > Invisible) and Copy and Paste here.', MELA_TD), esc_url('https://www.google.com/recaptcha/about/'));
							?>
						</p>
					</div>
				</div><!-- /.api-element-inner -->
			</div><!-- /.api-settings-element -->
			<!-- End of reCaptcha Settings -->



			<!-- Start of Twitter Settings -->
			<div class="api-settings-element">
				<h3><?php echo esc_html__('Twitter Settings', MELA_TD); ?></h3>
				<div class="api-element-inner">
					<div class="api-forms">

						<div class="form-group">
							<label for="twitter_username">
								<?php echo esc_html__('Username', MELA_TD); ?>
							</label>
							<input name="twitter_username" type="text" class="form-control twitter_username" value="<?php echo isset($jltma_api_options['twitter_username']) ? $jltma_api_options['twitter_username'] : ""; ?>">
						</div>

						<div class="form-group">
							<label for="twitter_consumer_key">
								<?php echo esc_html__('Consumer Key', MELA_TD); ?>
							</label>
							<input name="twitter_consumer_key" type="text" class="form-control twitter_consumer_key" value="<?php echo isset($jltma_api_options['twitter_consumer_key']) ? $jltma_api_options['twitter_consumer_key'] : ""; ?>">
						</div>

						<div class="form-group">
							<label for="twitter_consumer_secret">
								<?php echo esc_html__('Consumer Secret', MELA_TD); ?>
							</label>
							<input type="text" name="twitter_consumer_secret" class="form-control twitter_consumer_secret" value="<?php echo isset($jltma_api_options['twitter_consumer_secret']) ? $jltma_api_options['twitter_consumer_secret'] : ""; ?>">
						</div>

						<div class="form-group">
							<label for="twitter_access_token">
								<?php echo esc_html__('Access Token', MELA_TD); ?>
							</label>
							<input type="text" name="twitter_access_token" class="form-control twitter_access_token" value="<?php echo isset($jltma_api_options['twitter_access_token']) ? $jltma_api_options['twitter_access_token'] : ""; ?>">
						</div>

						<div class="form-group">
							<label for="twitter_access_token_secret">
								<?php echo esc_html__('Access Token Secret', MELA_TD); ?>
							</label>
							<input type="text" name="twitter_access_token_secret" class="form-control twitter_access_token_secret" value="<?php echo isset($jltma_api_options['twitter_access_token_secret']) ? $jltma_api_options['twitter_access_token_secret'] : ""; ?>">
						</div>

						<p>
							<?php echo sprintf(__('Go to <a href="%1$s" target="_blank"> https://developer.twitter.com/en/apps/create</a> for creating your Consumer key and Access Token.', MELA_TD), esc_url('https://developer.twitter.com/en/apps/create'));
							?>
						</p>
					</div>
				</div><!-- /.api-element-inner -->
			</div><!-- /.api-settings-element -->
			<!-- End of Twitter Settings -->




		</div><!-- /.master_addons_feature -->
	</form>
</div>
