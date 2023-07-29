import MixPanel from "../mixpanel";

class ProductAnalytics {
    init() {
        this.trackUltraModal();
    }

    trackUltraModal() {
		const ultraModals = document.querySelectorAll( '.wp-smush-ultra-compression-modal' );
		if ( ! ultraModals ) {
			return;
		}
		const getLocation = ( modalId ) => {
			const locations = {
				'settings': 'bulksmush_settings',
				'dashboard': 'dash_summary',
				'bulk': 'bulksmush_summary',
				'directory': 'directory_summary',
				'lazy-load': 'lazy_summary',
				'cdn': 'cdn_summary',
				'webp': 'webp_summary',
			};
			const locationId = modalId.includes( '__settings' ) ? 'settings' : this.getCurrentPageSlug();
			return locations[locationId] || 'bulksmush_settings';
		}

		ultraModals.forEach( ( modal ) => {
			const eventName = 'ultra_upsell_modal';
			let modalAction;
            let location;
			modal.addEventListener( 'click', (e) => {
				if ( 'A' !== e.target?.nodeName ) {
					return;
				}
				const action = e.target.dataset?.action;
				const actions = {
					'upgrade': 'cta_clicked',
					'connect_dash': 'connect_dash',
				}

				modalAction = actions[action] || 'connect_site';
				MixPanel.getInstance().track( eventName, {
					'Location': location,
					'Modal Action': modalAction,
				});
			});

			modal.addEventListener( 'close', (e) => {
				setTimeout( () => {
					if ( modalAction && 'closed' !== modalAction ) {
						return;
					}
					modalAction = 'closed';
					MixPanel.getInstance().track( eventName, {
						'Location': location || getLocation( e.target.id ),
						'Modal Action': modalAction,
					});
				}, 1000);
			} );
			
		});
	}

    getCurrentPageSlug(){
		const searchParams = new URLSearchParams(document.location.search);
		const pageSlug = searchParams.get("page");
        return 'smush' === pageSlug ? 'dashboard' : pageSlug.replace( 'smush-', '' );
	}
}

( new ProductAnalytics() ).init();