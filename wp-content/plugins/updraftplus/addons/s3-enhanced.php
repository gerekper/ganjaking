<?php
// @codingStandardsIgnoreStart
/*
UpdraftPlus Addon: s3-enhanced:Amazon S3, enhanced
Description: Adds enhanced capabilities for Amazon S3 users
Version: 1.8
Shop: /shop/s3-enhanced/
RequiresPHP: 5.5
*/
// @codingStandardsIgnoreEnd

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

use Aws\Iam\IamClient;

new UpdraftPlus_Addon_S3_Enhanced;

class UpdraftPlus_Addon_S3_Enhanced {

	public function __construct() {
		add_filter('updraft_s3_extra_storage_options_configuration_template', array($this, 'extra_storage_options_configuration_template'), 10, 2);
		add_filter('updraftplus_options_s3_options', array($this, 'transform_options_s3_options'));
		add_filter('updraft_s3_storageclass', array($this, 'storageclass'), 10, 3);
		add_action('updraftplus_settings_page_init', array($this, 'updraftplus_settings_page_init'));
		add_action('updraft_s3_newuser', array($this, 'newuser'));
		add_filter('updraft_s3_apikeysetting', array($this, 'apikeysettings'));
		add_action('updraft_s3_print_new_api_user_form', array($this, 's3_print_new_api_user_form'));
		add_filter('updraft_s3_newuser_go', array($this, 'newuser_go'), 10, 2);
	}

	/**
	 * WordPress filter updraft_s3_storageclass
	 *
	 * @param String $class	  - suggested storage class
	 * @param Object $storage - storage object
	 * @param Array	 $opts	  - options
	 *
	 * @return String - filtered value
	 */
	public function storageclass($class, $storage, $opts) {
	
		if (((is_a($storage, 'UpdraftPlus_S3') || is_a($storage, 'UpdraftPlus_S3_Compat')) && is_array($opts) && !empty($opts['rrs']) && in_array($opts['rrs'], array('STANDARD', 'STANDARD_IA', 'INTELLIGENT_TIERING')))) $class = $opts['rrs'];

		return $class;
	}

	/**
	 * This method gives template string to the page for the extra storage options.
	 *
	 * @param  Object $existing_partial_template_str - partial templete string to which this outputted template appended
	 * @param  Object $backup_module_object          - This is an instance of the remote storage object.
	 *
	 * @return String - the partial template, ready for substitutions to be carried out
	 */
	public function extra_storage_options_configuration_template($existing_partial_template_str, $backup_module_object) {
		$classes = $backup_module_object->get_css_classes();
		return $existing_partial_template_str.'<tr class="'.$classes.'">
			<th>'.__('Storage class', 'updraftplus').':<br><a aria-label="'.__('Read more about storage classes', 'updraftplus').'" href="https://aws.amazon.com/s3/storage-classes/" target="_blank"><em>'.__('(Read more)', 'updraftplus').'</em></a></th>
			<td>
				<select '.$backup_module_object->output_settings_field_name_and_id('rrs', true).' data-updraft_settings_test="rrs">
					<option value="STANDARD" {{#ifeq "STANDARD" rrs}}selected="selected"{{/ifeq}}>'.__('Standard', 'updraftplus').'</option>
					<option value="STANDARD_IA" {{#ifeq "STANDARD_IA" rrs}}selected="selected"{{/ifeq}}>'.__('Standard (infrequent access)', 'updraftplus').'</option>
					<option value="INTELLIGENT_TIERING" {{#ifeq "INTELLIGENT_TIERING" rrs}}selected="selected"{{/ifeq}}>'.__('Intelligent Tiering', 'updraftplus').'</option>
				</select>
			</td>
		</tr>
		<tr class="'.$classes.'">
			<th>'.__('Server-side encryption', 'updraftplus').':<br><a aria-label="'.__('Read more about server-side encryption', 'updraftplus').'" href="https://aws.amazon.com/blogs/aws/new-amazon-s3-server-side-encryption/" target="_blank"><em>'.__('(Read more)', 'updraftplus').'</em></a></th>
			<td><input data-updraft_settings_test="server_side_encryption" title="'.__("Check this box to use Amazon's server-side encryption", 'updraftplus').'" type="checkbox" '.$backup_module_object->output_settings_field_name_and_id('server_side_encryption', true).' value="1" {{#ifeq "1" server_side_encryption}}checked="checked"{{/ifeq}}/></td>
		</tr>';
	}
	
	/**
	 * Modifies handerbar template options
	 *
	 * @param array $opts handerbar template options
	 * @return array - New handerbar template options
	 */
	public function transform_options_s3_options($opts) {
		$rrs = empty($opts['rrs']) ? 'STANDARD' : $opts['rrs'];
		if (!empty($rrs) && 'STANDARD_IA' != $rrs && 'INTELLIGENT_TIERING' != $rrs) $rrs = 'STANDARD';
		$opts['rrs'] = $rrs;
		return $opts;
	}
	
	/**
	 * Runs upon the WP action updraftplus_settings_page_init
	 */
	public function updraftplus_settings_page_init() {
		add_action('admin_footer', array($this, 'admin_footer'));
	}

	public function apikeysettings($msg) {
		$msg = '<a href="'.esc_url(UpdraftPlus::get_current_clean_url()).'" id="updraft_s3_newapiuser_{{instance_id}}" class="updraft_s3_newapiuser" data-instance_id="{{instance_id}}"">'.__('If you have an AWS admin user, then you can use this wizard to quickly create a new AWS (IAM) user with access to only this bucket (rather than your whole account)', 'updraftplus').'</a>';
		return $msg;
	}

	/**
	 * Called upon the WP action updraft_s3_newuser. Dies.
	 *
	 * @param array $data - the posted data
	 *
	 * @return void
	 */
	public function newuser($data) {
		echo json_encode($this->newuser_go(array(), stripslashes_deep($data)));
		die;
	}
	
	/**
	 * Create a new user
	 *
	 * @param Array $initial_value	 - present because this method is used as a WP filter
	 * @param Array $settings_values - various keys indicating the access and desired bucket details
	 *
	 * @return Array - results (with keys dependent upon the outcome)
	 */
	public function newuser_go($initial_value = array(), $settings_values = array()) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found

		if (empty($settings_values['adminaccesskey'])) {
			return array('e' => 1, 'm' => __('You need to enter an admin access key', 'updraftplus'));
		}
		
		if (empty($settings_values['adminsecret'])) {
			return array('e' => 1, 'm' => __('You need to enter an admin secret key', 'updraftplus'));
		}
		
		if (empty($settings_values['newuser'])) {
			return array('e' => 1, 'm' => __('You need to enter a new IAM username', 'updraftplus'));
		}
		
		if (empty($settings_values['bucket'])) {
			return array('e' => 1, 'm' => __('You need to enter a bucket', 'updraftplus'));
		}
		
		if (empty($settings_values['region'])) $settings_values['region'] = 'us-east-1';
		
		if (empty($settings_values['rrs'])) $settings_values['rrs'] = false;
	
		$allow_download = empty($settings_values['allowdownload']) ? false : true;
		$allow_delete = empty($settings_values['allowdelete']) ? false : true;
	
		global $updraftplus;
	
		include_once(UPDRAFTPLUS_DIR.'/methods/s3.php');
		
		$method = new UpdraftPlus_BackupModule_s3;
	
		$useservercerts = !empty($settings_values['useservercerts']);
		$disableverify = !empty($settings_values['disableverify']);
		$nossl = !empty($settings_values['nossl']);
		
		$adminaccesskey = $settings_values['adminaccesskey'];
		$adminsecret = $settings_values['adminsecret'];
		$region = $settings_values['region'];
		
		$return_error = false;
		
		try {
			$storage = $method->getS3($adminaccesskey, $adminsecret, $useservercerts, $disableverify, $nossl);
			if (!is_a($storage, 'UpdraftPlus_S3_Compat') && !is_a($storage, 'UpdraftPlus_S3')) {
				$msg = __('Cannot create new AWS user, since an unknown AWS toolkit is being used.', 'updraftplus');
				$updraftplus->log('Cannot create new AWS user, since an unknown AWS toolkit is being used.');
				$updraftplus->log($msg, 'error');
				$return_error = array('e' => 1, 'm' => __('Error:', 'updraftplus').' '.$msg);
			}
		} catch (AuthenticationError $e) {
			$updraftplus->log('AWS authentication failed ('.$e->getMessage().')');
			$updraftplus->log(__('AWS authentication failed', 'updraftplus').' ('.$e->getMessage().')', 'error');
			$return_error = array('e' => 1, 'm' => __('Error:', 'updraftplus').' '.$e->getMessage());
		} catch (Exception $e) {
			$return_error = array('e' => 1, 'm' => __('Error:', 'updraftplus').' '.$e->getMessage());
		}
		
		if (is_array($return_error)) return $return_error;
		
		// Get the bucket
		$path = $settings_values['bucket'];
		
		if (preg_match("#^/*([^/]+)/(.*)$#", $path, $bmatches)) {
			$bucket = $bmatches[1];
			$path = trailingslashit($bmatches[2]);
		} else {
			$bucket = $path;
			$path = "";
		}
		
		$location = @$storage->getBucketLocation($bucket);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		if ($location) {
			$bucket_exists = true;
		}
		
		if (!isset($bucket_exists)) {
			$storage->useDNSBucketName(true);
			$gb = @$storage->getBucket($bucket, null, null, 1);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			if (false !== $gb) {
				$bucket_exists = true;
				$location = '';
			}
		}
		
		if (!isset($bucket_exists)) {
			$storage->setExceptions(true);
			try {
				$try_to_create_bucket = @$storage->putBucket($bucket, 'private', $region);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			} catch (Exception $e) {
				$try_to_create_bucket = false;
				$s3_error = $e->getMessage();
			}
			$storage->setExceptions(false);
			if ($try_to_create_bucket) {
				$gb = $try_to_create_bucket;
			} else {
				$msg = __("Failure: We could not successfully access or create such a bucket. Please check your access credentials, and if those are correct then try another bucket name (as another AWS user may already have taken your name).", 'updraftplus');
				if (isset($s3_error)) $msg .= "\n\n".sprintf(__('The error reported by %s was:', 'updraftplus'), 'S3').' '.$s3_error;
				return array('e' => 1, 'm' => $msg);
			}
		}
	
		// Create the new IAM user
	
		try {
			$response = $storage->createUser(array('Path' => '/updraftplus/', 'UserName' => $settings_values['newuser']));
		} catch (Exception $e) {
			return array('e' => 1, 'm' => sprintf(__('IAM operation failed (%s)', 'updraftplus'), 4).' ('.$e->getMessage().') ('.get_class($e).')');
		}
	
		if (403 == $response['code']) {
			return array('e' => 1, 'm' => __('Authorisation failed (check your credentials)', 'updraftplus'));
		} elseif (409 == $response['code']) {
			return array('e' => 1, 'm' => __('Conflict: that user already exists', 'updraftplus'));
		}
	
		if (empty($response['User']['UserId']) || empty($response['User']['CreateDate']) || empty($response['User']['UserName'])) {
			return array('e' => 1, 'm' => sprintf(__('IAM operation failed (%s)', 'updraftplus'), 5)." (".$response['error']['message'].')');
		}
		
		$user = $response['User']['UserName'];
		
		// Add the User to the bucket
		try {
			$response = $storage->createAccessKey($user);
		} catch (Exception $e) {
			return array('e' => 1, 'm' => __('Operation to create user Access Key failed', 'updraftplus'));
		}
		
		if (empty($response['AccessKey']['UserName']) || empty($response['AccessKey']['AccessKeyId']) || empty($response['AccessKey']['SecretAccessKey'])) {
			return array('e' => 1, 'm' => __('Operation to create user Access Key failed', 'updraftplus').' (2)');
		}
		
		$key = $response['AccessKey']['AccessKeyId'];
		$secret = $response['AccessKey']['SecretAccessKey'];
		
		// policy document
		$pol_doc = '{
	"Statement": [
	{
	  "Effect": "Allow",
	  "Action": [
		"s3:ListBucket",
		"s3:GetBucketLocation",
		"s3:ListBucketMultipartUploads"
	  ],
	  "Resource": "arn:aws:s3:::'.$bucket.'",
	  "Condition": {}
	},
	{
	  "Effect": "Allow",
	  "Action": [
		"s3:AbortMultipartUpload",';
		if ($allow_delete) $pol_doc .= '
		"s3:DeleteObject",
		"s3:DeleteObjectVersion",';
		if ($allow_download) $pol_doc .= '
		"s3:GetObject",
		"s3:GetObjectAcl",
		"s3:GetObjectVersion",
		"s3:GetObjectVersionAcl",';
		$pol_doc .= '
		"s3:PutObject",
		"s3:PutObjectAcl",
		"s3:PutObjectVersionAcl"
	  ],
	  "Resource": "arn:aws:s3:::'.$bucket.'/*",
	  "Condition": {}
	},
	{
	  "Effect": "Allow",
	  "Action": "s3:ListAllMyBuckets",
	  "Resource": "*",
	  "Condition": {}
	}
	]
	}';
	
		try {
			$response = $storage->putUserPolicy(array(
				'UserName' => $user,
				'PolicyName' => $user.'updraftpolicy',
				'PolicyDocument' => $pol_doc
			));
		} catch (Exception $e) {
			return array('e' => 1, 'm' => __('Failed to apply User Policy'.$e->getMessage()));
		}
	
		if (!empty($response['error'])) {
			return array('e' => 1, 'm' => __('Failed to apply User Policy', 'updraftplus')." (".$response['error']['message'].')');
		}
	
		return array(
			'e' => 0,
			'u' => htmlspecialchars($user),
			'k' => htmlspecialchars($key),
			's' => htmlspecialchars($secret),
			'l' => $region,
			'c' => $bucket,
			'm' => htmlspecialchars(sprintf(__("Username: %s", 'updraftplus'), $user))."<br>".htmlspecialchars(sprintf(__("Access Key: %s", 'updraftplus'), $key))."<br>".htmlspecialchars(sprintf(__("Secret Key: %s", 'updraftplus'), $secret))
		);
	
	}
	
	/**
	 * This is called both directly, and made available as an action
	 *
	 * @param  boolean $include_form_apparatus
	 */
	public function s3_print_new_api_user_form($include_form_apparatus = true) {
		?>
		<div id="updraft_s3newapiuser_form">
			<p class="updraft-s3newapiuser-first-para">
				<em><?php echo __('Enter your administrative Amazon S3 access/secret keys (this needs to be a key pair with enough rights to create new users and buckets), and a new (unique) username for the new user and a bucket name.', 'updraftplus').' '.__('These will be used to create a new user and key pair with an IAM policy attached which will only allow it to access the indicated bucket.', 'updraftplus').' '.__('Then, these lower-powered access credentials can be used, instead of storing your administrative keys.', 'updraftplus');?></em>
			</p>
			
			<div id="updraft-s3newapiuser-results"><p></p></div>

			<p class="updraft-s3newapiuser-settings-para">

			<label for="updraft_s3newapiuser_adminaccesskey"><?php _e('Admin access key', 'updraftplus');?></label> <input type="text" id="updraft_s3newapiuser_adminaccesskey" value="">
			<label for="updraft_s3newapiuser_adminsecret"><?php _e('Admin secret key', 'updraftplus');?></label> <input type="text" id="updraft_s3newapiuser_adminsecret" value="">
			<label for="updraft_s3newapiuser_newuser"><?php _e("New IAM username", 'updraftplus');?></label> <input type="text" id="updraft_s3newapiuser_newuser" value="">

			<label for="updraft_s3newapiuser_region"><?php _e('S3 storage region', 'updraftplus');?>:</label>
			<select id="updraft_s3newapiuser_region">
				<?php
					$regions = array(
						'us-east-1' => __('US East (N. Virginia) (default)', 'updraftplus'),
						'us-east-2' => __('US East (Ohio)', 'updraftplus'),
						'us-west-2' => __('US West (Oregon)', 'updraftplus'),
						'us-west-1' => __('US West (N. California)', 'updraftplus'),
						'us-gov-west-1' => __('US Government West (restricted)', 'updraftplus'),
						'ca-central-1' => __('Canada (Central)', 'updraftplus'),
						'eu-west-1' => __('Europe (Ireland)', 'updraftplus'),
						'eu-west-2' => __('Europe (London)', 'updraftplus'),
						'eu-west-3' => __('Europe (Paris)', 'updraftplus'),
						'eu-central-1' => __('Europe (Frankfurt)', 'updraftplus'),
						'eu-south-1' => __('Europe (Milan)', 'updraftplus'),
						'eu-north-1' => __('Europe (Stockholm)', 'updraftplus'),
						'me-south-1' => __('Middle East (Bahrain)', 'updraftplus'),
						'af-south-1' => __('Africa (Cape Town)', 'updraftplus'),
						'ap-northeast-2' => __('Asia Pacific (Seoul)', 'updraftplus'),
						'ap-southeast-1' => __('Asia Pacific (Singapore)', 'updraftplus'),
						'ap-southeast-2' => __('Asia Pacific (Sydney)', 'updraftplus'),
						'ap-south-1' => __('Asia Pacific (Mumbai)', 'updraftplus'),
						'ap-northeast-1' => __('Asia Pacific (Tokyo)', 'updraftplus'),
						'ap-northeast-3' => __('Asia Pacific (Osaka-Local) (restricted)', 'updraftplus'),
						'ap-east-1' => __('Asia Pacific (Hong Kong)', 'updraftplus'),
						'sa-east-1' => __('South America (São Paulo)', 'updraftplus'),
						'cn-northwest-1' => __('China (Ningxia) (restricted)', 'updraftplus'),
						'cn-north-1' => __('China (Beijing) (restricted)', 'updraftplus'),
					);
					$selregion = 'us-east-1';
					foreach ($regions as $reg => $desc) {
					?>
					<option <?php if ($selregion == $reg) echo 'selected="selected"'; ?> value="<?php echo $reg;?>"><?php echo htmlspecialchars($desc); ?></option>
					<?php
					}
				?>
			</select>
			<label for="updraft_s3newapiuser_bucket"><?php _e("S3 bucket", 'updraftplus');?></label><span class="updraft_s3newapiuser_textexplain">s3://</span><input type="text" id="updraft_s3newapiuser_bucket" value="">
			
			<label aria-label="<?php echo __("Allow download", 'updraftplus').'. '.__('Without this permission, you cannot directly download or restore using UpdraftPlus, and will instead need to visit the AWS website.', 'updraftplus'); ?>" for="updraft_s3newapiuser_allowdownload"><?php _e("Allow download", 'updraftplus');?></label>
			<input type="checkbox" id="updraft_s3newapiuser_allowdownload" value="1" checked="checked">
			<span class="updraft_s3newapiuser_checkboxexplain"><em><?php _e('Without this permission, you cannot directly download or restore using UpdraftPlus, and will instead need to visit the AWS website.', 'updraftplus');?></em></span>

			<label aria-label="<?php echo __("Allow deletion", 'updraftplus').'. '.__("Without this permission, UpdraftPlus cannot delete backups - you should also set your 'retain' settings very high to prevent seeing deletion errors.", 'updraftplus');?>" for="updraft_s3newapiuser_allowdelete"><?php _e("Allow deletion", 'updraftplus');?></label>
			<input type="checkbox" id="updraft_s3newapiuser_allowdelete" value="1" checked="checked">
			<span class="updraft_s3newapiuser_checkboxexplain"><em><?php _e("Without this permission, UpdraftPlus cannot delete backups - you should also set your 'retain' settings very high to prevent seeing deletion errors.", 'updraftplus');?></em></span>

			</p>
			<?php if ($include_form_apparatus) { ?>
			<fieldset>
				<input type="hidden" name="nonce" value="<?php echo wp_create_nonce('updraftplus-credentialtest-nonce');?>">
				<input type="hidden" name="action" value="updraft_ajax">
				<input type="hidden" name="subaction" value="s3_newuser">
				<input type="hidden" id="updraft_s3newapiuser_instance_id" name="updraft_s3newapiuser_instance_id" value="" />
			</fieldset>
			<?php } ?>
		</div>
		<?php
	}
	
	public function admin_footer() {
		?>
		<style type="text/css">
			#updraft_s3newapiuser_form label { float: left; clear:left; width: 170px;}
			#updraft_s3newapiuser_form input[type="text"], #updraft_s3newapiuser_form select { float: left; width: 310px; }
			#updraft_s3newapiuser_form input[type="checkbox"] { float: left; }
			#updraft_s3newapiuser_form p { padding-top:0; clear: left; float: left; }
			#updraft_s3newapiuser_form .updraft-s3newapiuser-first-para { margin:1px; }
			#updraft_s3newapiuser_form .updraft-s3newapiuser-settings-para { margin-top:3px; padding-top:0; clear: left; float: left; }
			#updraft_s3newapiuser_form #updraft-s3newapiuser-results { clear: left; float: left; }
			#updraft_s3newapiuser_form #updraft-s3newapiuser-results p { margin: 1px 0; padding: 1px 0; }
			#updraft_s3newapiuser_form .updraft_s3newapiuser_checkboxexplain { width:310px; float:left; }
			#updraft_s3newapiuser_form .updraft_s3newapiuser_textexplain { float:left; width:30px; position:relative; top:3px; }
			#updraft_s3newapiuser_form #updraft_s3newapiuser_bucket { width: 280px; }
		</style>
		<div id="updraft-s3newapiuser-modal" style="display:none;" title="<?php _e('Create new IAM user and S3 bucket', 'updraftplus');?>">
			<?php $this->s3_print_new_api_user_form(); ?>
		</div>

		<script>
		jQuery(function($) {
			$('#updraft-navtab-settings-content').on('click', '.updraft_s3_newapiuser', function(e) {
				e.preventDefault();
				jQuery('#updraft_s3newapiuser_instance_id').val(jQuery(this).data('instance_id'));
				$('#updraft-s3newapiuser-modal').dialog('open');
			});

			var updraft_s3newapiuser_modal_buttons = {};
			
			updraft_s3newapiuser_modal_buttons[updraftlion.cancel] = function() { $(this).dialog("close"); };
			updraft_s3newapiuser_modal_buttons[updraftlion.createbutton] = function() {
				$('#updraft-s3newapiuser-results').html('<p style="color:green">'+updraftlion.trying+'</p>');

				var data = {
					subsubaction: 'updraft_s3_newuser',
					adminaccesskey: $('#updraft_s3newapiuser_adminaccesskey').val(),
					adminsecret: $('#updraft_s3newapiuser_adminsecret').val(),
					newuser: $('#updraft_s3newapiuser_newuser').val(),
					bucket: $('#updraft_s3newapiuser_bucket').val(),
					region: $('#updraft_s3newapiuser_region').val(),
					useservercerts: $('#updraft_ssl_useservercerts').val(),
					disableverify: $('#updraft_ssl_disableverify').val(),
					nossl: $('#updraft_ssl_nossl').val(),
					allowdelete: $('#updraft_s3newapiuser_allowdelete').is(':checked') ? 1 : 0,
					allowdownload: $('#updraft_s3newapiuser_allowdownload').is(':checked') ? 1 : 0,
				};

				updraft_send_command('doaction', data, function(resp, status, response) {
					if (resp.e == 1) {
						$('#updraft-s3newapiuser-results').html('<p style="color:red;">'+resp.m+'</p>');
					} else if (resp.e == 0) {
						var instance_id = jQuery('#updraft_s3newapiuser_instance_id').val();
						$('#updraft-s3newapiuser-results').html('<p style="color:green;">'+resp.m+'</p>');
						$('#updraft_s3_accesskey_'+instance_id).val(resp.k);
						$('#updraft_s3_secretkey_'+instance_id).val(resp.s);
						$('#updraft_s3_server_side_encryption_'+instance_id).attr('checked', resp.r);
						$('#updraft_s3_path_'+instance_id).val(resp.c);
						
						//Clear Admin credentials
						$('#updraft_s3newapiuser_adminaccesskey').val("");
						$('#updraft_s3newapiuser_adminsecret').val("");
						$('#updraft_s3newapiuser_newuser').val("");
						$('#updraft_s3newapiuser_bucket').val("");
						
						//Change link to open dialog to reflect that using IAM user
						$('#updraft_s3_newapiuser_'+instance_id).html('<?php echo esc_js(__('You are now using a IAM user account to access your bucket.', 'updraftplus')).' <strong>'.esc_js(__('Do remember to save your settings.', 'updraftplus')).'</strong>';?>');
						
						$('#updraft-s3newapiuser-modal').dialog('close');
					}

				}, { error_callback: function(response, status, error_code, resp) {
						if (typeof resp !== 'undefined' && resp.hasOwnProperty('fatal_error')) {
							console.error(resp.fatal_error_message);
							$('#updraft-s3newapiuser-results').html('<p style="color:red;">'+resp.fatal_error_message+'</p>');
							alert(resp.fatal_error_message);
						} else {
							var error_message = "updraft_send_command: error: "+status+" ("+error_code+")";
							console.log(error_message);
							console.log(response);
							$('#updraft-s3newapiuser-results').html('<p style="color:red;">'+updraftlion.servererrorcode+'</p>');
							alert(updraftlion.unexpectedresponse+' '+response);
							return;
							
						}
					}
				});
			};
			$("#updraft-s3newapiuser-modal").dialog({
				autoOpen: false, height: 525, width: 555, modal: true,
				buttons: updraft_s3newapiuser_modal_buttons
			});

		});
		</script>
		<?php
	}
}
