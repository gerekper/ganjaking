<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the persistent banner.
 */
?>
<ul id="wf-onboarding-banner">
	<li><?php esc_html_e('Wordfence installation is incomplete', 'wordfence'); ?></li>
	<li>
		<?php if (isset($dismissable) && $dismissable): ?>
			<a href="#" class="wf-onboarding-btn wf-onboarding-btn-default" id="wf-onboarding-delay" data-timestamp="<?php echo time(); ?>"><?php esc_html_e('Remind Me Later', 'wordfence'); ?></a>
		<?php endif ?>
		<a href="<?php echo esc_attr(network_admin_url('admin.php?page=WordfenceSupport')); ?>" class="wf-onboarding-btn wf-onboarding-btn-default" id="wf-onboarding-resume"><?php esc_html_e('Resume Installation', 'wordfence'); ?></a>
	</li>
</ul>
<div style="display: none;">
	<div class="wf-modal" id="wf-onboarding-registration-delayed-template">
		<div class="wf-modal-header">
			<div class="wf-modal-header-content">
				<div class="wf-modal-title"><strong><?php esc_html_e('Notice Dismissed', 'wordfence') ?></strong></div>
			</div>
		</div>
		<div class="wf-modal-content"><span class="message"><?php esc_html_e('You will be reminded again in 12 hours.', 'wordfence') ?></span></div>
		<div class="wf-modal-footer">
			<ul class="wf-onboarding-flex-horizontal wf-onboarding-flex-align-right wf-onboarding-full-width">
				<li><a href="#" class="wf-onboarding-btn wf-onboarding-btn-primary" onclick="jQuery.wfcolorbox.close(); return false;" role="button">Close</a></li>
			</ul>
		</div>
	</div>
	<div class="wf-modal" id="wf-onboarding-registration-delayed-error-template">
		<div class="wf-modal-header">
			<div class="wf-modal-header-content">
				<div class="wf-modal-title"><strong><?php esc_html_e('Error', 'wordfence') ?></strong></div>
			</div>
		</div>
		<div class="wf-modal-content"><span class="message"><?php esc_html_e('An unexpected error occurred while attempting to dismiss the notice. Please try again.', 'wordfence') ?></span></div>
		<div class="wf-modal-footer">
			<ul class="wf-onboarding-flex-horizontal wf-onboarding-flex-align-right wf-onboarding-full-width">
				<li><a href="#" class="wf-onboarding-btn wf-onboarding-btn-primary" onclick="jQuery.wfcolorbox.close(); return false;" role="button">Close</a></li>
			</ul>
		</div>
	</div>
</div>