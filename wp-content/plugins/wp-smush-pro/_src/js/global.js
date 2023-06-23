import '../scss/common.scss';

/* global ajaxurl */

document.addEventListener('DOMContentLoaded', function () {
	const dismissNoticeButton = document.querySelectorAll(
		'.smush-dismissible-notice .smush-dismiss-notice-button'
	);
	dismissNoticeButton.forEach((button) => {
		button.addEventListener('click', handleDismissNotice);
	});

	function handleDismissNotice(event) {
		event.preventDefault();

		const button = event.target;
		const notice = button.closest('.smush-dismissible-notice');
		const key = notice.getAttribute('data-key');

		dismissNotice( key, notice );
	}

	function dismissNotice( key, notice ) {
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


	// Show header notices.
	const handleHeaderNotice = () => {
		const headerNotice = document.querySelector('.wp-smush-dismissible-header-notice');
		if ( ! headerNotice || ! headerNotice.id ) {
			return;
		}

		const { dismissKey, message } = headerNotice.dataset;
		if ( ! dismissKey || ! message ) {
			return;
		}

		headerNotice.onclick = (e) => {
			const classList = e.target.classList;
			const isDismissButton = classList && ( classList.contains('sui-icon-check') || classList.contains('sui-button-icon') );
			if ( ! isDismissButton ) {
				return;
			}
			dismissNotice( dismissKey );
		}

		const noticeOptions = {
			type: 'warning',
			icon: 'info',
			dismiss: {
				show: true,
				label: wp_smush_msgs.noticeDismiss,
				tooltip: wp_smush_msgs.noticeDismissTooltip,
			},
		};

		window.SUI.openNotice(
			headerNotice.id,
			message,
			noticeOptions
		);
	}

	handleHeaderNotice();
	
});
