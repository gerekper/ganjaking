(function($) {
	$(function() {

		function showRegistrationModal(id, message) {
			console.log("Registration error message: ", message);
			var content = $("#wf-onboarding-registration-" + id + "-template").clone().attr('id', null);
			if (message)
				content.find('.message').empty().text(message);
			$.wfcolorbox({
				width: (wordfenceExt.isSmallScreen ? '300px' : '500px'),
				html: content[0].outerHTML,
				overlayClose: false,
				closeButton: false,
				className: 'wf-modal'
			});
		}

		function toggleInstallType(event) {
			event.preventDefault();
			event.stopPropagation();
			$(event.target).parents('.wf-onboarding-registration-prompt').find('.wf-onboarding-install-type').toggle();
			$.wfcolorbox.resize();
		}

		$(document).on('click', '.wf-onboarding-install-type-toggle', toggleInstallType);
		$('.wf-onboarding-install-type-toggle').on('click', toggleInstallType);

		$(document).on('input', '#wf-onboarding-email-input,#wf-onboarding-license-input', function(event) {
			var context = $(event.target).parents('.wf-onboarding-registration-prompt');
			context.find('.wf-onboarding-consent-group').show();
			context.find('#wf-onboarding-consent-input').prop('checked', false);
		});

		$(document).on('submit', '.wf-onboarding-form', function(event) {
			event.preventDefault();
			var context = $(this);
			if (context.data('submitting'))
				return;
			context.data('submitting', true);
			var button = context.find('button');
			button.prop('disabled', true);
			var enable = function (result) {
				context.data('submitting', false);
				button.prop('disabled', false);
				if (typeof result !== 'undefined')
					return result;
			};
			var email = context.find('#wf-onboarding-email-input').val();
			var licenseKey = context.find('#wf-onboarding-license-input').val();
			var subscribe = context.find('#wf-onboarding-subscribe-input').prop('checked');
			var consent = context.find('#wf-onboarding-consent-input').prop('checked');
			if (!consent)
				return enable(false);
			var attempt = context.data('attempt');
			var optionKey = 'onboardingAttempt' + attempt;
			var optionValueEmail = context.data('option-value-email');
			var optionValueLicense = context.data('option-value-license');
			wordfenceExt.onboardingInstallLicense(
				licenseKey,
				function(licenseResponse) {
					wordfenceExt.setOption(
						optionKey,
						optionValueLicense,
						function(optionResponse) {
							wordfenceExt.onboardingProcessEmails(
								[
									email
								],
								subscribe,
								!!consent,
								function(success, error) {
									if (!success) {
										showRegistrationModal('error', error);
										enable();
										return;
									}
									if (licenseResponse.isPaid) {
										var modalType;
										if (licenseResponse.type === 'care' || licenseResponse.type === 'response') {
											modalType = licenseResponse.type;
										}
										else {
											modalType = 'premium';
										}
										showRegistrationModal('success-' + modalType);
									}
									else {
										showRegistrationModal('success-free');
									}
									enable();
								}
							);
						},
						function() {
							showRegistrationModal('error');
							enable();
						}
					);
				},
				function(error) {
					showRegistrationModal('error', (typeof error === 'string') ? error : null);
					enable();
				}
			);
		});

		$(document).on('click', '#wf-onboarding-delay', function() {
			wordfenceExt.setOption(
				'onboardingDelayedAt',
				$('#wf-onboarding-delay').data('timestamp'),
				function() {
					$('#wf-onboarding-banner').hide();
					showRegistrationModal('delayed');
				},
				function() {
					showRegistrationModal('delayed-error');
				}
			);
		});

	});
})(jQuery);
