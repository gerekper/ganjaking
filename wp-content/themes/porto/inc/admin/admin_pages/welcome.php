<div class="wrap porto-wrap">
	<h2 class="screen-reader-text"><?php esc_html_e( 'Dashboard', 'porto' ); ?></h2>
	<?php
		porto_get_template_part(
			'inc/admin/admin_pages/header',
			null,
			array(
				'active_item' => 'porto',
				'title'       => __( 'Welcome to Porto!', 'porto' ),
				'subtitle'    => __( 'Porto is now installed and ready to use! Read below for additional information. We hope you enjoy it!', 'porto' ),
			)
		);
		?>
	<main class="row">
		<div class="welcome col-left">
			<div class="porto-section">
				<div class="porto-important-notice registration-form-container">
					<?php if ( Porto()->is_registered() ) : ?>
						<p class="about-description"><?php esc_html_e( 'Congratulations! Your product is registered now.', 'porto' ); ?></p>
					<?php else : ?>
						<p class="about-description"><?php esc_html_e( 'Please enter your Purchase Code to complete registration.', 'porto' ); ?></p>
					<?php endif; ?>
					<div class="porto-registration-form">
						<form id="porto_registration" method="post" class="system-status">
							<?php
							$disable_field = '';
							$error_message = get_option( 'porto_register_error_msg' );
							update_option( 'porto_register_error_msg', '' );
							$purchase_code = Porto()->get_purchase_code_asterisk();
							?>
							<?php if ( $purchase_code && ! empty( $purchase_code ) ) : ?>
								<?php
								if ( Porto()->is_registered() ) :
									$disable_field = ' disabled=true';
									?>
									<span class="fas fa-check status yes"></span>
								<?php else : ?>
									<span class="fas fa-ban status no"></span>
								<?php endif; ?>
							<?php else : ?>
								<span class="dashicons dashicons-admin-network status"></span>
							<?php endif; ?>
							<input type="hidden" name="porto_registration" />
							<?php if ( Porto()->is_envato_hosted() ) : ?>
							<p class="confirm unregister">
								You are using Envato Hosted, this subscription code can not be deregistered.
							</p>
							<?php else : ?>
								<input type="text" name="code" class="regular-text" value="<?php echo esc_attr( $purchase_code ); ?>"<?php echo porto_filter_output( $disable_field ); ?> />
								<?php if ( Porto()->is_registered() ) : ?>
									<input type="hidden" name="action" value="unregister" />
									<?php submit_button( esc_attr__( 'Deactivate', 'porto' ), array( 'button-danger', 'large', 'porto-large-button' ), '', true ); ?>
								<?php else : ?>
									<input type="hidden" name="action" value="register" />
									<?php submit_button( esc_attr__( 'Submit', 'porto' ), array( 'primary', 'large', 'porto-large-button' ), '', true ); ?>
								<?php endif; ?>
							<?php endif; ?>
							<?php wp_nonce_field( 'porto-setup' ); ?>
						</form>
						<?php if ( $error_message ) : ?>
							<p class="error-invalid-code"><?php echo porto_strip_script_tags( $error_message ); ?></p>
						<?php endif; ?>

						<p><?php esc_html_e( 'Where can I find my purchase code?', 'porto' ); ?></p>
						<ol>
							<?php /* translators: $1: opening A tag which has link to the Themeforest downloads page $2: closing A tag */ ?>
							<li><?php printf( esc_html__( 'Please go to %1$sThemeForest.net/downloads%2$s', 'porto' ), '<a target="_blank" href="https://themeforest.net/downloads" rel="noopener noreferrer">', '</a>' ); ?></li>
							<?php /* translators: $1 and $2 opening and closing strong tags respectively */ ?>
							<li><?php printf( esc_html__( 'Click the %1$sDownload%2$s button in Porto row', 'porto' ), '<strong>', '</strong>' ); ?></li>
							<?php /* translators: $1 and $2 opening and closing strong tags respectively */ ?>
							<li><?php printf( esc_html__( 'Select %1$sLicense Certificate &amp; Purchase code%2$s', 'porto' ), '<strong>', '</strong>' ); ?></li>
							<?php /* translators: $1 and $2 opening and closing strong tags respectively */ ?>
							<li><?php printf( esc_html__( 'Copy %1$sItem Purchase Code%2$s', 'porto' ), '<strong>', '</strong>' ); ?></li>
						</ol>
					</div>
				</div>
				<p class="about-description">
					<?php /* translators: $1: opening A tag which has link to the Porto documentation $2: closing A tag */ ?>
					<?php printf( esc_html__( 'Before you get started, please be sure to always check out %1$sthis documentation%2$s. We outline all kinds of good information, and provide you with all the details you need to use Porto.', 'porto' ), '<a href="http://www.portotheme.com/wordpress/porto/documentation" target="_blank" rel="noopener noreferrer">', '</a>' ); ?>
				</p>
				<p class="about-description">
					<?php /* translators: $1: opening A tag which has link to the Porto support $2: closing A tag */ ?>
					<?php printf( esc_html__( 'If you are unable to find your answer in our documentation, we encourage you to contact us through %1$ssupport page%2$s with your site CPanel (or FTP) and WordPress admin details. We are very happy to help you and you will get reply from us more faster than you expected.', 'porto' ), '<a href="http://www.portotheme.com/support" target="_blank" rel="noopener noreferrer">', '</a>' ); ?>
				</p>
				<p class="about-description">
					<a href="https://www.portotheme.com/wordpress/porto/documentation/changelog/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Click here to view change logs.', 'porto' ); ?></a>
				</p>
			</div>
			<div class="porto-thanks">
				<p class="description"><?php esc_html_e( 'Thank you, we hope you to enjoy using Porto!', 'porto' ); ?></p>
			</div>
		</div>
		<div class="system-status col-right">
			<h3><?php esc_html_e( 'System Status', 'porto' ); ?></h3>
			<?php require_once PORTO_ADMIN . '/admin_pages/mini-status.php'; ?>
		</div>
	</main>
</div>
