import '../scss/common.scss';

/* global ajaxurl */

document.addEventListener('DOMContentLoaded', function () {
	const dismissNoticeButton = document.querySelectorAll(
		'.smush-dismissible-notice .smush-dismiss-notice-button'
	);
	dismissNoticeButton.forEach((button) => {
		button.addEventListener('click', dismissNotice);
	});

	function dismissNotice(event) {
		event.preventDefault();

		const button = event.target;
		const notice = button.closest('.smush-dismissible-notice');
		const key = notice.getAttribute('data-key');

		const xhr = new XMLHttpRequest();
		xhr.open(
			'POST',
			ajaxurl + '?action=smush_dismiss_notice&key=' + key + '&_ajax_nonce=' + smush_global.nonce,
			true
		);
		xhr.onload = () => {
			if (notice) {
				notice.querySelector('button.notice-dismiss').dispatchEvent(new MouseEvent('click', {
					view: window,
					bubbles: true,
					cancelable: true
				}));
			}
		};
		xhr.send();
	}
});
