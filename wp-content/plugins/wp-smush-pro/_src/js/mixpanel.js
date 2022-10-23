/* global wp_smush_mixpanel */

import mixpanel from "mixpanel-browser";

export default class MixPanel {
	constructor() {
		this.mixpanelInstance = mixpanel.init(wp_smush_mixpanel.token, {
			opt_out_tracking_by_default: !wp_smush_mixpanel.opt_in,
			loaded: (mixpanel) => {
				mixpanel.identify(wp_smush_mixpanel.unique_id);
				mixpanel.register(wp_smush_mixpanel.super_properties);
			}
		}, 'smush');
	}

	track(event, properties = {}) {
		this.mixpanelInstance.track(event, properties);
	}

	trackBulkSmushCompleted(totalSavingsSize, totalImageCount, optimizationPercentage, savingsPercentage) {
		this.track('Bulk Smush Completed', {
			'Total Savings': totalSavingsSize,
			'Total Images': totalImageCount,
			'Media Optimization Percentage': optimizationPercentage,
			'Percentage of Savings': savingsPercentage
		});
	}

	trackBulkSmushCancel() {
		this.track('Bulk Smush Cancelled');
	}
}
