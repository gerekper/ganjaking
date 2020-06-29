<?php
$dkim_defaults = array(
	'enabled'       => '',
	'domain'        => '',
	'selector'      => '',
	'identity'      => '',
	'passphrase'    => '',
	'public_key'    => '',
	'private_key'   => '',
	'key_size'      => ''
);
$dkim = wp_parse_args( $dkim, $dkim_defaults );

$spf_defaults = array(
	'enabled'   => '',
	'check_ip'  => '',
	'domain'    => '',
	'spf'       => ''
);
$spf = wp_parse_args( $spf, $spf_defaults );
?>
<form action="admin-post.php" method="post" enctype="multipart/form-data">

	<?php wp_nonce_field( 'fue-update-settings-verify' ); ?>

	<h3><?php esc_html_e('DomainKeys Identified Mail (DKIM)', 'follow_up_emails'); ?></h3>
	<p>A DomainKeys Identified Mail (DKIM) record adds a digital signature to emails your organization sends. Email recipient servers perform a check: “Does the signature match?” If so, then the email hasn’t been modified and is from a legitimate sender. Your DKIM DNS record lets the recipient server perform this verification. The DKIM check verifies that the message is signed and associated with the correct domain.</p>

	<table class="form-table">
		<tr>
			<th><label for="dkim_enabled"><?php esc_html_e('Enable DKIM', 'follow_up_emails'); ?></label></th>
			<td>
				<input type="checkbox" name="dkim[enabled]" id="dkim_enabled" value="1" <?php checked( 1, $dkim['enabled'] ); ?> />
			</td>
		</tr>
		<tr class="dkim">
			<th><label for="dkim_domain"><?php esc_html_e('DKIM Domain', 'follow_up_emails'); ?></label></th>
			<td><input type="text" name="dkim[domain]" id="dkim_domain" value="<?php echo esc_attr( $dkim['domain'] ); ?>" /></td>
		</tr>
		<tr class="dkim">
			<th><label for="dkim_selector"><?php esc_html_e('DKIM Selector Prefix', 'follow_up_emails'); ?></label></th>
			<td><input type="text" name="dkim[selector]" id="dkim_selector" value="<?php echo esc_attr( $dkim['selector'] ); ?>" /></td>
		</tr>
		<tr class="dkim">
			<th><label for="dkim_identity"><?php esc_html_e('DKIM Identity', 'follow_up_emails'); ?></label></th>
			<td>
				<input type="text" name="dkim[identity]" id="dkim_identity" value="<?php echo esc_attr( $dkim['identity'] ); ?>" />
				<p class="description"><?php esc_html_e('Optional. Usually the email address used as the source of the email', 'follow_up_emails'); ?></p>
			</td>
		</tr>
		<tr class="dkim">
			<th><label for="dkim_passphrase"><?php esc_html_e('DKIM Passphrase', 'follow_up_emails'); ?></label></th>
			<td>
				<input type="text" name="dkim[passphrase]" id="dkim_passphrase" value="<?php echo esc_attr( $dkim['passphrase'] ); ?>" />
				<p class="description"><?php esc_html_e('Optional. Used if your key is encrypted.', 'follow_up_emails'); ?></p>
			</td>
		</tr>

		<tr class="dkim">
		  <?php if ( !function_exists( 'openssl_pkey_new' ) ): ?>
			  <th>&nbsp;</th>
			  <td>
				<?php esc_html_e('To generate new keys, please enable the OpenSSL extension in your PHP installation.', 'follow_up_emails'); ?>
			  </td>
		  <?php else: ?>
			<th><label for="dkim_key_size"><?php esc_html_e('Key Size', 'follow_up_emails'); ?></label></th>
			<td>
				<select name="dkim[key_size]" id="dkim_key_size">
					<option value="1024">1024</option>
					<option value="2048">2048</option>
				</select>
			</td>
		  <?php endif; ?>
		</tr>

	</table>

	<table class="form-table dkim">
	  <tr>
		  <th>&nbsp;</th>
		  <td>
			  <input type="button" class="button-secondary generate-dkim-keys" value="<?php esc_attr_e('Generate DKIM Keys', 'follow_up_emails'); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'generate_dkim_keys' ) ); ?>" />
			  <span class="spf-dkim-spinner spinner"></span>

		  </td>
	  </tr>

		<tr>
			<th><label for="dkim_public_key"><?php esc_html_e('DKIM Public Key', 'follow_up_emails'); ?></label></th>
			<td><textarea name="dkim[public_key]" id="dkim_public_key" rows="5" cols="80"><?php echo esc_attr( $dkim['public_key'] ); ?></textarea></td>
		</tr>
		<tr>
			<th><label for="dkim_private_key"><?php esc_html_e('DKIM Private Key', 'follow_up_emails'); ?></label></th>
			<td><textarea name="dkim[private_key]" id="dkim_private_key" rows="5" cols="80"><?php echo esc_attr( $dkim['private_key'] ); ?></textarea></td>
		</tr>



		<?php
		if ( $dkim['enabled'] && !empty( $dkim['public_key'] ) ):
			$public_key = str_replace("-----BEGIN PUBLIC KEY-----\r\n", '', $dkim['public_key'] );
			$public_key = str_replace("\r\n-----END PUBLIC KEY-----", '', $public_key );
		?>
			<tr>
				<th><?php esc_html_e('DNS Entry', 'follow_up_emails'); ?></th>
				<td>
					<p>
						<strong><?php esc_html_e('Host/Name', 'follow_up_emails'); ?>:</strong>
						<br/>
						<pre><code><?php echo esc_html( $dkim['selector'] .'._domainkey.'. $dkim['domain'] ); ?></code></pre>
					</p>
					<p>
						<strong><?php esc_html_e('TXT Value', 'follow_up_emails'); ?>:</strong>
						<br/>
						<pre><code><?php echo esc_html( 'k=rsa; p='. $public_key ); ?></code></pre>
					</p>
				</td>
			</tr>
		<?php endif; ?>

	</table>

	<hr>

	<h3><?php esc_html_e('Sender Policy Framework (SPF)', 'follow_up_emails'); ?></h3>
	<p>A Sender Policy Framework (SPF) record indicates which mail servers are authorized to send mail for a domain. Email recipient servers perform a check: <em>Is this email coming from an authorized mail server?</em> If not, then the email in question is more likely to be spam. Your SPF DNS record lets the recipient server perform this verification. The SPF check verifies that an email comes from authorized servers.</p>

	<table class="form-table">
		<tr>
			<th><label for="spf_enabled"><?php esc_html_e('Enable SPF', 'follow_up_emails'); ?></label></th>
			<td>
				<input type="checkbox" name="spf[enabled]" id="spf_enabled" value="1" <?php checked( 1, $spf['enabled'] ); ?> />
			</td>
		</tr>
		<tr class="spf">
			<th><label for="spf_check_ip"><?php esc_html_e('IP Check'); ?></label></th>
			<td>
				<input type="text" name="spf[check_ip]" id="ip_check" placeholder="8.8.8.8" value="<?php echo esc_attr( $spf['check_ip'] ); ?>" />
				<p class="description"><?php esc_html_e('Optional', 'follow_up_emails'); ?></p>
			</td>
		</tr>
		<tr class="spf">
			<th><label for="spf_domain"><?php esc_html_e('Your Domain', 'follow_up_emails'); ?></label></th>
			<td>
				<input type="text" name="spf[domain]" id="spf_domain" size="50" placeholder="example.com" value="<?php echo esc_attr( $spf['domain'] ); ?>" />
				<input type="button" class="button validate-spf" value="<?php esc_attr_e('Check SPF Record', 'follow_up_emails'); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'validate_spf' ) ); ?>" />
				<span class="spf-spinner spinner"></span>
			</td>
		</tr>
		<tr class="spf-dns-result" style="display: none;">
			<th>&nbsp;</th>
			<td>
				<div class="success">
					<p>
						<span class="dashicons dashicons-yes"></span> <?php echo wp_kses_post( __( 'SPF record found for <code id="spf_result_domain"></code> - <code id="spf_result_data"></code>', 'follow_up_emails' ) ); ?>
					</p>
				</div>
			</td>
		</tr>
		<tr class="spf">
			<th>&nbsp;</th>
			<td>
				<input type="button" class="button generate-spf-record" value="<?php esc_attr_e('Generate SPF Record', 'follow_up_email'); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'generate_spf' ) ); ?>" />
				<span class="spf-gen-spinner spinner"></span>
			</td>
		</tr>
		<tr class="spf-result">
			<th><label for="spf_dns"><?php esc_html_e('DNS Entry', 'follow_up_emails'); ?></label></th>
			<td>
				<div id="spf_dns"><?php echo esc_html( $spf['spf'] ); ?></div>
				<input type="hidden" name="spf[spf]" id="spf_spf" value="<?php echo esc_attr( $spf['spf'] ); ?>" />
				<p class="description">
					<?php esc_html_e('You should add this DNS record to your domain\'s DNS configuration.', 'follow_up_emails'); ?>
				</p>
			</td>
		</tr>
	</table>

	<?php do_action( 'fue_settings_auth' ); ?>

	<p class="submit">
		<input type="hidden" name="action" value="fue_followup_save_settings" />
		<input type="hidden" name="section" value="<?php echo esc_attr( $tab ); ?>" />
		<input type="submit" name="save" value="<?php esc_attr_e('Save Settings', 'follow_up_emails'); ?>" class="button-primary" />
	</p>
</form>
