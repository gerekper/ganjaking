<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Prompts the user for a license key, directing to wordfence.com to register for a free license key
 */

if (!isset($existing))
	$existing = false;
if (!isset($email))
	$email = null;
if (!isset($license))
	$license = null;
$registrationLink = wfLicense::generateRegistrationLink();
$populated = $existing && $email && $license;
?>
<div class="wf-onboarding-registration-prompt">
	<p>
		<?php if ($existing): ?>
			<?php esc_html_e('Install your license to finish activating Wordfence.', 'wordfence') ?>
		<?php else: ?>
			<?php esc_html_e('Register with Wordfence to secure your site with the latest threat intelligence.', 'wordfence') ?>
		<?php endif ?>
	</p>
	<div class="wf-onboarding-install-new wf-onboarding-install-type"<?php if ($existing): ?> style="display: none;"<?php endif ?>>
		<div>
			<a class="wf-btn wf-btn-primary wf-onboardng-register" href="<?php esc_attr_e($registrationLink) ?>" target="_blank"><?php esc_html_e('Get Your Wordfence License', 'wordfence') ?></a>	
		</div>
		<div>
			<a class="wf-onboarding-install-type-toggle" href="#"><?php esc_html_e('Install an existing license', 'wordfence') ?></a>
		</div>
	</div>
	<div class="wf-onboarding-install-existing wf-onboarding-install-type" data-attempt="<?php echo esc_attr($attempt) ?>"
			data-option-value-emails="<?php esc_attr_e(wfOnboardingController::ONBOARDING_EMAILS) ?>"
			data-option-value-license="<?php esc_attr_e(wfOnboardingController::ONBOARDING_LICENSE) ?>"
			<?php if (!$existing): ?>style="display: none;"<?php endif ?>
			>
		<form class="wf-onboarding-form">
			<div class="wf-onboarding-form-group">
				<label for="wf-onboarding-email-input"><?php esc_html_e('Email', 'wordfence') ?></label>
				<input id="wf-onboarding-email-input" type="email" value="<?php echo esc_attr((string) $email) ?>" pattern="^[^@\s]+@[^@\s]+\.[^@\s]+$" title="<?php esc_html_e('Please enter a valid email address', 'wordfence') ?>" required>
				<small><?php esc_html_e('This is where future security alerts for your website will be sent. This can also be changed in Global Options.', 'wordfence') ?></small>
			</div>
			<div class="wf-onboarding-form-group">
				<label for="wf-onboarding-license-input"><?php esc_html_e('License Key', 'wordfence') ?></label>
				<textarea id="wf-onboarding-license-input" rows="3" required><?php echo esc_html((string) $license) ?></textarea>
			</div>
			<div class="wf-onboarding-form-group wf-onboarding-consent-group"<?php if ($populated): ?> style="display: none;"<?php endif ?>>
				<label id="wf-onboarding-subscription-options-label"><?php esc_html_e('Would you like WordPress security and vulnerability alerts sent to you via email?', 'wordfence') ?></label>
				<div class="wf-onboarding-subscription-options" role="radiogroup" aria-labelledby="wf-onboarding-subscription-options-label">
					<ul class="wf-switch">
						<li data-value="1" role="radio" tabindex="0"><?php esc_html_e('Yes', 'wordfence') ?></li>
						<li data-value="0" role="radio" tabindex="0"><?php esc_html_e('No', 'wordfence') ?></li>
					</ul>
					<small class="wf-onboarding-subscription-option-required" style="display: none;"><?php esc_html_e('You must select either "Yes" or "No"', 'wordfence') ?></small>
				</div>
			</div>
			<div class="wf-onboarding-form-group wf-onboarding-consent-group"<?php if ($populated): ?> style="display: none;"<?php endif ?>>
				<input type="checkbox" id="wf-onboarding-consent-input" required<?php if ($populated): ?> checked<?php endif ?>>
				<label for="wf-onboarding-consent-input"><?php echo wp_kses(__('I have read and agree to the <a href="https://www.wordfence.com/license-terms-and-conditions/" target="_blank" rel="noopener noreferrer">Wordfence License Terms and Conditions</a>, the <a href="https://www.wordfence.com/services-subscription-agreement" rel="noopener noreferrer" target="_blank">Services Subscription Agreement</a>, and <a href="https://www.wordfence.com/terms-of-service/" target="_blank" rel="noopener noreferrer">Terms of Service</a>, and have read and acknowledge the <a href="https://www.wordfence.com/privacy-policy/" target="_blank" rel="noopener noreferrer">Wordfence Privacy Policy</a>.', 'wordfence'), array('a' => array('href' => array(), 'target' => array(), 'rel' => array()))) ?></label>
			</div>
			<button class="wf-btn wf-btn-primary wf-onboarding-install-license" type="submit"><?php esc_html_e('Install License', 'wordfence') ?></button>
		</form>
		<?php if (!$populated): ?>
			<div>
				<a class="wf-onboarding-link" href="<?php esc_attr_e($registrationLink) ?>" target="_blank"><?php esc_html_e('Get a new license', 'wordfence') ?></a>
			</div>
		<?php endif ?>
	</div>
</div>
<div style="display: none;">
	<?php
		$licenseTypeModals = array(
			'response' => array(
				'title' => __('Response License Installed', 'wordfence'),
				'content' => __('Congratulations! Wordfence Response is now active on your website. Please note that some Response features are not enabled by default.', 'wordfence')
			),
			'care' => array(
				'title' => __('Care License Installed', 'wordfence'),
				'content' => __('Congratulations! Wordfence Care is now active on your website. Please note that some Care features are not enabled by default.', 'wordfence')
			),
			'premium' => array(
				'title' => __('Premium License Installed', 'wordfence'),
				'content' => __('Congratulations! Wordfence Premium is now active on your website. Please note that some Premium features are not enabled by default.', 'wordfence')
			),
			'free' => array(
				'title' => __('Free License Installed', 'wordfence'),
				'content' => __('Congratulations! Wordfence Free is now active on your website.', 'wordfence')
			),
		);
	?>
	<?php foreach ($licenseTypeModals as $key => $modal): ?>
		<div class="wf-modal wf-modal-success" id="<?php echo esc_attr("wf-onboarding-registration-success-$key-template") ?>">
			<div class="wf-model-success-wrapper">
				<div class="wf-modal-header">
					<div class="wf-modal-header-content">
						<div class="wf-modal-title"><?php echo esc_html($modal['title']) ?></div>
					</div>
				</div>
				<div class="wf-modal-content"><?php echo esc_html($modal['content']) ?></div>
			</div>
			<div class="wf-modal-footer">
				<ul class="wf-onboarding-flex-horizontal wf-onboarding-flex-align-right wf-onboarding-full-width">
					<li><a href="<?php echo esc_url(network_admin_url('admin.php?page=Wordfence')); ?>" class="wf-onboarding-btn wf-onboarding-btn-primary"><?php esc_html_e('Go To Dashboard', 'wordfence'); ?></a></li>
				</ul>
			</div>
		</div>
	<?php endforeach ?>
	<div class="wf-modal" id="wf-onboarding-registration-error-template">
		<div class="wf-modal-header">
			<div class="wf-modal-header-content">
				<div class="wf-modal-title"><strong><?php esc_html_e('Error Installing License', 'wordfence') ?></strong></div>
			</div>
		</div>
		<div class="wf-modal-content">
			<p class="message"><?php esc_html_e('An error occurred while installing your license key.', 'wordfence') ?></p>
			<p><?php echo wp_kses(__('Please try again. If the problem persists, please <a href="https://www.wordfence.com/help/api-key" target="_blank" rel="noopener noreferrer">contact Wordfence Support<span class="screen-reader-text">(opens in new tab)</span></a>', 'wordfence'), array('a' => array('href' => array(), 'target' => array(), 'rel' => array()), 'span' => array('class' => array()))) ?>
		</div>
		<div class="wf-modal-footer">
			<ul class="wf-onboarding-flex-horizontal wf-onboarding-flex-align-right wf-onboarding-full-width">
				<li><a href="#" class="wf-onboarding-btn wf-onboarding-btn-primary" onclick="jQuery.wfcolorbox.close(); return false;" role="button">Close</a></li>
			</ul>
		</div>
	</div>
</div>