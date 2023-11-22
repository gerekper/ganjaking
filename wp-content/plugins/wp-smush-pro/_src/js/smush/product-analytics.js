import MixPanel from "../mixpanel";

class ProductAnalytics {
    init() {
        this.trackUltraLinks();
    }

    trackUltraLinks() {
		const ultraUpsellLinks = document.querySelectorAll( '.wp-smush-upsell-ultra-compression' );
		if ( ! ultraUpsellLinks ) {
			return;
		}
		const getLocation = ( ultraLink ) => {
			const locations = {
				'settings': 'bulksmush_settings',
				'dashboard': 'dash_summary',
				'bulk': 'bulksmush_summary',
				'directory': 'directory_summary',
				'lazy-load': 'lazy_summary',
				'cdn': 'cdn_summary',
				'webp': 'webp_summary',
			};
			const locationId = ultraLink.classList.contains( 'wp-smush-ultra-compression-link' ) ? 'settings' : this.getCurrentPageSlug();
			return locations[locationId] || 'bulksmush_settings';
		}

		ultraUpsellLinks.forEach( ( ultraLink ) => {
			const eventName = 'ultra_upsell_modal';
			ultraLink.addEventListener( 'click', (e) => {
				MixPanel.getInstance().track( eventName, {
					'Location': getLocation( e.target ),
					'Modal Action': 'direct_cta',
				});
			});
		});
	}

    getCurrentPageSlug(){
		const searchParams = new URLSearchParams(document.location.search);
		const pageSlug = searchParams.get("page");
        return 'smush' === pageSlug ? 'dashboard' : pageSlug.replace( 'smush-', '' );
	}
}

( new ProductAnalytics() ).init();