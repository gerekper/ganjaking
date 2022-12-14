<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the final onboarding attempt modal.
 */
?>
<div class="wf-modal" id="wf-onboarding-final-attempt">
	<div class="wf-modal-header">
		<div class="wf-modal-header-content">
			<div class="wf-modal-title"><?php esc_html_e('Please Complete Wordfence Installation', 'wordfence'); ?></div>
		</div>
		<div class="wf-modal-header-action">
			<div class="wf-padding-add-left-small wf-modal-header-action-close"><a href="<?php echo esc_attr(network_admin_url('admin.php?page=Wordfence')); ?>"><i class="wf-fa wf-fa-times-circle" aria-hidden="true"></i></a></div>
		</div>
	</div>
	<div class="wf-modal-content">
		<div id="wf-onboarding-final-attempt-1" class="wf-onboarding-modal-content"<?php if (wfConfig::get('onboardingAttempt3') == wfOnboardingController::ONBOARDING_EMAILS) { echo ' style="display: none;"'; } ?>>
			<?php echo wfView::create('onboarding/registration-prompt', array('attempt' => 3)) ?>
		</div>
		<div id="wf-onboarding-final-attempt-2" class="wf-onboarding-modal-content"<?php if (wfConfig::get('onboardingAttempt3') != wfOnboardingController::ONBOARDING_EMAILS) { echo ' style="display: none;"'; } ?>>
			<h3><?php esc_html_e('Activate Premium', 'wordfence'); ?></h3>
			<p><?php esc_html_e('Enter your premium license key to enable real-time protection for your website.', 'wordfence'); ?></p>
			<div id="wf-onboarding-license-status" style="display: none;"></div>
			<div id="wf-onboarding-license"><input type="text" placeholder="<?php esc_html_e('Enter Premium Key', 'wordfence'); ?>"><a href="#" class="wf-onboarding-btn wf-onboarding-btn-primary wf-disabled" id="wf-onboarding-license-install" role="button"><?php esc_html_e('Install', 'wordfence'); ?></a></div>
			<div id="wf-onboarding-or"><span>or</span></div>
			<p id="wf-onboarding-premium-cta"><?php esc_html_e('If you don\'t have one, you can purchase one now.', 'wordfence'); ?></p>
			<div id="wf-onboarding-license-footer">
				<ul>
					<li><a href="https://www.wordfence.com/gnl1onboardingFinalGet/wordfence-signup/#premium-order-form" class="wf-onboarding-btn wf-onboarding-btn-primary" id="wf-onboarding-get" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Upgrade to Premium', 'wordfence'); ?><span class="screen-reader-text"> (<?php esc_html_e('opens in new tab', 'wordfence') ?>)</span></a></li>
					<li><a href="https://www.wordfence.com/gnl1onboardingFinalLearn/wordfence-signup/" class="wf-onboarding-btn wf-onboarding-btn-default" id="wf-onboarding-learn" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Learn More', 'wordfence'); ?><span class="screen-reader-text"> (<?php esc_html_e('opens in new tab', 'wordfence') ?>)</span></a></li>
					<li><a href="#" id="wf-onboarding-no-thanks" role="button"><?php esc_html_e('No Thanks', 'wordfence'); ?></a></li>
				</ul>
			</div>
			<div id="wf-onboarding-license-finished" style="display: none;">
				<ul>
					<li><a href="<?php echo esc_attr(network_admin_url('admin.php?page=Wordfence')); ?>" class="wf-onboarding-btn wf-onboarding-btn-primary"><?php esc_html_e('Close', 'wordfence'); ?></a></li>
				</ul>
			</div>
		</div>
	</div>
</div>