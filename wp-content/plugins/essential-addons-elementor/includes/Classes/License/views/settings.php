	<div class="eael-block p45 eael-activate__license__block --activation-form" style="display: <?php echo ( $status === false || $status !== 'valid' ) ? 'block' : 'none'; ?>">
		<div class="eael__flex eael__flex--wrap align__center mb30">
			<h3>Just one more step to go!</h3>
			<img src="<?php echo esc_url( EAEL_PLUGIN_URL . 'assets/admin/images/steps.svg' ); ?>" alt="">
		</div>
		<p><?php _e( 'Enter your license key here, to activate <strong>Essential Addons for Elementor</strong>, and get automatic updates and premium support.', $this->text_domain ); ?></p>
		<p><?php printf( __( 'Visit the <a href="%s" target="_blank">Validation Guide</a> for help.', $this->text_domain ), 'https://essential-addons.com/elementor/docs/getting-started/validating-license/' ); ?></p>
		<ol>
			<li>
				<p><?php printf( __( 'Log in to <a href="%s" target="_blank">your account</a> to get your license key.', $this->text_domain ), 'https://wpdeveloper.com/account/' ); ?></p>
			</li>
			<li>
				<p><?php printf( __( 'If you don\'t yet have a license key, get <a href="%s" target="_blank">Essential Addons for Elementor now</a>.', $this->text_domain ), 'https://wpdeveloper.com/in/upgrade-essential-addons-elementor' ); ?></p>
			</li>
			<li><?php _e( __( 'Copy the license key from your account and paste it below.', $this->text_domain ) ); ?></li>
			<li><?php _e( __( 'Click on <strong>"Activate License"</strong> button.', $this->text_domain ) ); ?></li>
		</ol>
		<div class="license__form__block">
			<div class="eael-license-form-block">
				<form method="post" action="#">
					<?php wp_nonce_field( $this->args['item_slug'] . '_license_nonce', $this->args['item_slug'] . '_license_nonce' ); ?>
					<input id="<?php echo $this->args['item_slug']; ?>-license-key" type="text" class="eael-form__control" placeholder="Place Your License Key & Activate">
					<button type="submit" class="eael-button button__themeColor" name="license_activate">Activate</button>
				</form>
			</div>
		</div>

		<div class="eael-verification-msg" style="display: none;">
			<p>License Verification code has been sent to this <span class="eael-customer-email"></span>. Please check your email for the code &amp; insert it below 👇</p>
			<div class="short-description">
				<b style="font-weight: 700;">Note: </b> Check out this <a href="https://essential-addons.com/docs/verify-essential-addons-pro-license-key/" target="_blank">guide</a> to
				verify your license key. If you need any assistance with retrieving your License Verification Key, please <a href="https://wpdeveloper.com/support/"
																															 target="_blank">contact support</a>.
			</div>
			<div class="eael-verification-input-container license__form__block">
				<div class="eael-license-form-block">
					<input type="text" id="<?php echo $this->args['item_slug']; ?>-license-otp" class="eael-form__control" placeholder="Enter Your Verification Code">
					<button type="submit" class="eael-button button__themeColor">Verify</button>
				</div>
				<p>Haven’t received an email? Please hit this <a href="#" class="eael-otp-resend">"Resend"</a> to retry. Please note that this verification code will
					expire after 15 minutes.</p>
			</div>
		</div>
		<p class="eael-license-error-msg error-message" style="display: none;"></p>
	</div>

	<div class="eael-block p45 eael-activate__license__block --deactivation-form" style="display: <?php echo ( $status !== false && $status === 'valid' ) ? 'block' : 'none'; ?>">
		<div class="eael-grid">
			<div class="eael-col-md-6">
				<ul class="eael-feature__list ls-none">
					<li class="feature__item">
						<span class="icon">
							<img src="<?php echo EAEL_PRO_PLUGIN_URL . 'assets/admin/images/icon-auto-update.svg'; ?>" alt="essential-addons-auto-update">
						</span>
						<div class="content">
							<h4>Premium Support</h4>
							<p>Supported by professional and courteous staff.</p>
						</div>
					</li>
					<li class="feature__item">
						<span class="icon">
							<img src="<?php echo EAEL_PRO_PLUGIN_URL . 'assets/admin/images/icon-auto-update.svg'; ?>" alt="essential-addons-auto-update">
						</span>
						<div class="content">
							<h4>Auto Update</h4>
							<p>Update the plugin right from your WordPress Dashboard.</p>
						</div>
					</li>
				</ul>
			</div>
			<div class="eael-col-md-6">
				<div class="license__form__block">
					<div class="eael-license-form-block">
						<form method="post" action="#">
							<input class="eael-form__control regular-text" disabled type="text" value="<?php echo esc_attr( $hidden_license_key ); ?>"
								   placeholder="Place Your License Key and Activate"/>
							<button type="submit" class="eael-button button__danger" name="license_deactivate">Deactivate</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
