/* global ajaxurl */

/**
 * External dependencies
 */
import assign from 'lodash/assign';

/**
 * Wrapper function for ajax calls to WordPress.
 *
 * @since 3.12.0
 */
function SmushFetcher() {
	/**
	 * Request ajax with a promise.
	 * Use FormData Object as data if you need to upload file
	 *
	 * @param {string}          action
	 * @param {Object|FormData} data
	 * @param {string}          method
	 * @return {Promise<any>} Request results.
	 */
	function request(action, data = {}, method = 'POST') {
		const args = {
			url: ajaxurl,
			method,
			cache: false
		};

		if (data instanceof FormData) {
			data.append('action', action);
			data.append('_ajax_nonce', window.wp_smush_msgs.nonce);
			args.contentType = false;
			args.processData = false;
		} else {
			data._ajax_nonce = data._ajax_nonce || window.wp_smush_msgs.nonce;
			data.action = action;
		}
		args.data = data;
		return new Promise((resolve, reject) => {
			jQuery.ajax(args).done(resolve).fail(reject);
		}).then((response) => {
			if (typeof response !== 'object') {
				response = JSON.parse(response);
			}
			return response;
		}).catch((error) => {
			console.error('Error:', error);
		});
	}

	const methods = {
		/**
		 * Manage ajax for background.
		 */
		background: {
			/**
			 * Start background process.
			 */
			start: () => {
				return request('bulk_smush_start');
			},

			/**
			 * Cancel background process.
			 */
			cancel: () => {
				return request('bulk_smush_cancel');
			},

			/**
			 * Initial State - Get stats on the first time.
			 */
			initState: () => {
				return request('bulk_smush_get_status');
			},

			/**
			 * Get stats.
			 */
			getStatus: () => {
				return request('bulk_smush_get_status');
			},

			getStats: () => {
				return request('bulk_smush_get_global_stats');
			}
		},
		smush: {
			/**
			 * Sync stats.
			 */
			syncStats: ( data ) => {
				data = data || {};
				return request('get_stats', data);
			},

			/**
             * Ignore All.
             */
			ignoreAll: ( type ) => {
                return request('wp_smush_ignore_all_failed_items', {
                    type: type,
                });
            },
		},

		/**
		 * Manage ajax for other requests
		 */
		common: {
			/**
			 * Dismiss Notice.
			 *
			 * @param {string} dismissId Notification id.
			 */
			dismissNotice: (dismissId) => {
				return request('smush_dismiss_notice', {
					key: dismissId
				});
			},

			/**
			 * Hide the new features modal.
			 *
			 * @param {string} modalID Notification id.
			 */
			hideModal: (modalID) => request('hide_modal', {
				modal_id: modalID,
			}),

			/**
			 * Custom request.
			 *
			 * @param {Object} data
			 */
			request: (data) => data.action && request(data.action, data),
		},
	};

	assign(this, methods);
}

const SmushAjax = new SmushFetcher();
export default SmushAjax;