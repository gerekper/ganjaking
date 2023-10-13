<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the fresh install plugin header.
 */
?>
<div id="wf-onboarding-plugin-header">
	<div id="wf-onboarding-plugin-header-header">
		<div id="wf-onboarding-plugin-header-title"><?php esc_html_e('Please Complete Wordfence Installation', 'wordfence'); ?></div>
		<div id="wf-onboarding-plugin-header-accessory"><a href="#" id="wf-onboarding-plugin-header-dismiss" role="button">&times;</a></div>
	</div>
	<div id="wf-onboarding-plugin-header-content">
		<ul>
			<li id="wf-onboarding-plugin-header-stage-content">
				<div id="wf-onboarding-plugin-header-stage-content-1">
					<?php echo wfView::create('onboarding/registration-prompt', array('attempt' => 2)) ?>
				</div>
			</li>
			<li id="wf-onboarding-plugin-header-stage-image"></li>
		</ul>
	</div>
</div>
<script type="application/javascript">
	(function($) {
		$(function() {
			$('#wf-onboarding-plugin-header-dismiss').on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();

				$(window).trigger('wfOnboardingDismiss2');
				$('#wf-onboarding-plugin-header').slideUp(400, function() {
					$('#wf-onboarding-plugin-overlay').remove();
				});

				if ($('#wf-onboarding-plugin-header-stage-content-1').is(':visible')) {
					wordfenceExt.setOption('onboardingAttempt2', '<?php echo esc_attr(wfOnboardingController::ONBOARDING_SKIPPED); ?>');
				}
			});
		});
	})(jQuery);
</script>