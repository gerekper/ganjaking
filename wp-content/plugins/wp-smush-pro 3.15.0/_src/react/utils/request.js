import $ from 'jquery';
import ajaxUrl from 'ajaxUrl';

export function post(action, nonce, data = {}) {
	return new Promise(function (resolve, reject) {
		const request = Object.assign({}, {
			action: action,
			_ajax_nonce: nonce
		}, data);

		$.post(ajaxUrl, request)
			.done((response) => {
				if (response.success) {
					resolve(
						response?.data
					);
				} else {
					reject(response?.data?.message);
				}
			})
			.fail(() => reject());
	});
}
