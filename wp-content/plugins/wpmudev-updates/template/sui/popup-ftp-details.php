<?php
/**
 * Dashboard popup template: Ask for FTP credentials before updating/installing.
 *
 * This is only loaded if direct FS access is not possible.
 *
 */

$ftp_host = preg_replace(
	'/www\./',
	'',
	wp_parse_url( admin_url(), PHP_URL_HOST )
);

$credentials             = get_option(
	'ftp_credentials',
	array(
		'hostname' => '',
		'username' => '',
	) );
$credentials['hostname'] = defined( 'FTP_HOST' ) ? FTP_HOST : $credentials['hostname'];
$credentials['username'] = defined( 'FTP_USER' ) ? FTP_USER : $credentials['username'];
$hostname                = isset( $credentials['hostname'] ) ? $credentials['hostname'] : '';
$username                = isset( $credentials['username'] ) ? $credentials['username'] : '';
?>

<div class="sui-dialog" aria-hidden="true" tabindex="-1" id="ftp-details">

	<div class="sui-dialog-overlay" data-a11y-dialog-hide></div>

	<div class="sui-dialog-content" aria-labelledby="dialogTitle" aria-describedby="ftpdialogDescription" role="dialog">

		<div class="sui-box" role="document">
			<form>

				<div class="sui-box-header">
					<h3 class="sui-box-title" id="dialogTitle"><?php esc_html_e( 'We need your help, boss!', 'wpmudev' ); ?></h3>
					<div class="sui-actions-right">
						<a href="#" data-a11y-dialog-hide class="sui-dialog-close" aria-label="<?php esc_html_e( 'Close this dialog window', 'wpmudev' ); ?>"></a>
					</div>
				</div>

				<div class="sui-box-body">
					<p id="ftpdialogDescription">
						<?php esc_html_e( 'Hang on a minute... It looks like your WordPress site isn\'t configured to allow one-click installations of plugins and themes. But don\'t worry! You can still install this plugin by entering your server\'s FTP credentials here:',
						                  'wpmudev' ); ?>
					</p>

					<div class="sui-notice sui-notice-error" style="display: none">
						<p><?php esc_html_e( 'Failed to save credentials', 'wpmudev' ); ?></p>
					</div>
					<div class="sui-notice sui-notice-success" style="display: none">
						<p><?php esc_html_e( 'Credential saved', 'wpmudev' ); ?></p>
					</div>

					<div class="sui-row">
						<div class="sui-col-md-12">
							<div class="sui-form-field">
								<label for="ftp_user" class="sui-label"><?php esc_html_e( 'FTP Username', 'wpmudev' ); ?></label>
								<input
										type="text"
										id="ftp_user"
									<?php if ( defined( 'FTP_USER' ) ) : ?>
										readonly="readonly"
										value="<?php echo esc_attr( FTP_USER ); ?>"
									<?php else : ?>
										value="<?php echo esc_attr( $username ); ?>"
									<?php endif; ?>
										placeholder="username..."
										class="sui-form-control"/>
							</div>
							<div class="sui-form-field">
								<label for="ftp_pass" class="sui-label"><?php esc_html_e( 'FTP Password', 'wpmudev' ); ?></label>
								<input
										type="password"
										id="ftp_pass"
									<?php if ( defined( 'FTP_PASS' ) ) : ?>
										readonly="readonly"
										value="<stored>"
									<?php else : ?>
										value=""
									<?php endif; ?>
										placeholder="*****"
										class="sui-form-control"/>
							</div>
							<label for="ftp_host" class="sui-label"><?php esc_html_e( 'FTP Host', 'wpmudev' ); ?></label>
							<input
									type="text"
									id="ftp_host"
								<?php if ( defined( 'FTP_HOST' ) ) : ?>
									readonly="readonly"
									value="<?php echo esc_attr( FTP_HOST ); ?>"
								<?php else : ?>
									value="<?php echo esc_attr( $hostname ); ?>"
								<?php endif; ?>
									placeholder="e.g. <?php echo esc_attr( $ftp_host ); ?>"
									class="sui-form-control"/>
						</div>
					</div>

				</div>
				<div class="sui-box-footer">
					<div class="sui-row">
						<div class="sui-col-md-12">
							<p>
								<?php
								printf(
									__( 'Or you can %senable one-click installations%s on this site by adding the following details to <code>wp-config.php</code>:', 'wpmudev' ),
									'<a href="https://codex.wordpress.org/Editing_wp-config.php#WordPress_Upgrade_Constants" target="_blank">',
									'</a>'
								);
								?>
							</p>
							<pre class="sui-code-snippet sui-no-copy">
define( 'FTP_USER', '<em>your FTP username</em>' );<br/>
define( 'FTP_PASS', '<em>your FTP password</em>' );<br/>
define( 'FTP_HOST', '<?php echo esc_html( $ftp_host ); ?>' );
							</pre>
							<p><?php esc_html_e( 'We will remember these details for 15 minutes in case you want to install or, update something else.', 'wpmudev' ); ?></p>
						</div>
					</div>
				</div>
				<div class="sui-box-footer">
					<input type="hidden" name="hash" value="<?php echo esc_attr( wp_create_nonce( 'credentials' ) ); ?>"/>
					<div class="sui-flex-child-right">
						<a href="#" class="sui-button" data-a11y-dialog-hide="ftp-details"><?php esc_html_e( 'Cancel', 'wpmudev' ); ?></a>
						<button class="sui-button sui-button-blue ftp-submit">
							<span class="sui-loading-text">
							<?php esc_html_e( 'Okay, continue!', 'wpmudev' ); ?>
							</span>
							<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
						</button>
					</div>
				</div>
			</form>
		</div>

	</div>

</div>
