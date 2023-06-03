/* global wp_smush_mixpanel */

import mixpanel from "mixpanel-browser";

export default class MixPanel {
	constructor() {
		this.mixpanelInstance = mixpanel.init(wp_smush_mixpanel.token, {
			opt_out_tracking_by_default: !wp_smush_mixpanel.opt_in,
			loaded: (mixpanel) => {
				mixpanel.identify(wp_smush_mixpanel.unique_id);
				mixpanel.register(wp_smush_mixpanel.super_properties);

				const trackingActiveInSmushSettings = !!wp_smush_mixpanel.opt_in;
				if (mixpanel.has_opted_in_tracking() !== trackingActiveInSmushSettings) {
					// The status cached by MixPanel in the local storage is different from the settings. Clear the cache.
					mixpanel.clear_opt_in_out_tracking();
				}
			}
		}, 'smush');
	}

	track(event, properties = {}) {
		this.mixpanelInstance.track(event, properties);
	}

	trackBulkSmushCompleted( globalStats ) {
		const {
			savings_bytes,
			count_images,
			percent_optimized,
			savings_percent,
			count_resize,
			savings_resize
		 } = globalStats;
		this.track('Bulk Smush Completed', {
			'Total Savings': this.convertToMegabytes( savings_bytes ),
			'Total Images': count_images,
			'Media Optimization Percentage': parseFloat( percent_optimized ),
			'Percentage of Savings': parseFloat( savings_percent ),
			'Images Resized': count_resize,
			'Resize Savings': this.convertToMegabytes( savings_resize )
		});
	}

	trackBulkSmushCancel() {
		this.track('Bulk Smush Cancelled');
	}

	convertToMegabytes( sizeInBytes ) {
		const unitMB = Math.pow( 1024, 2 );
		const sizeInMegabytes = sizeInBytes/unitMB;
		return  sizeInMegabytes && parseFloat(sizeInMegabytes.toFixed(2)) || 0;
	}
}
