<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the fresh install modal.
 */
?>
<div id="wf-onboarding-fresh-install" class="wf-onboarding-modal">
	<div id="wf-onboarding-fresh-install-1" class="wf-onboarding-modal-content">
		<div class="wf-onboarding-logo"><img src="<?php echo esc_attr(wfUtils::getBaseURL() . 'images/wf-horizontal.svg'); ?>" alt="<?php esc_html_e('Wordfence - Securing your WordPress Website', 'wordfence'); ?>"></div>
		<h3><?php printf(/* translators: Wordfence version. */ esc_html__('You have successfully installed Wordfence %s', 'wordfence'), WORDFENCE_VERSION); ?></h3>
		<?php echo wfView::create('onboarding/registration-prompt', array('attempt' => 1)) ?>
	</div>
</div>
<script type="application/javascript">
	(function($) {
		$(function() {	
			$('#wf-onboarding-fresh-install').on('click', function(e) {
				e.stopPropagation();
			});

			$(window).on('wfOnboardingDismiss', function() {
				if ($('#wf-onboarding-fresh-install-1').is(':visible')) {
					wordfenceExt.setOption('onboardingAttempt1', '<?php echo esc_attr(wfOnboardingController::ONBOARDING_SKIPPED); ?>');
				}
			});
		});
	})(jQuery);
</script>