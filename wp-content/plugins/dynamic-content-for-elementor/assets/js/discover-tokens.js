jQuery(window).on('elementor/frontend/init', () => {
    class DiscoverTokens extends elementorModules.frontend.handlers.Base {

        getDefaultSettings() {
            return {};
        }

        getDefaultElements() {
            return {
                wrapper: this.$element[0]
            };
        }

        initStart() {
            let copyInstances = this.elements.wrapper.querySelectorAll("span.copy");

            copyInstances.forEach( () => {	
				let clipboard = new ClipboardJS('span.copy');
                clipboard.on( 'success', (e) => {
                    let tooltip = tippy( e.trigger, {
						content: 'Copied!',
                        arrow: true,
                    });
                    tooltip.setContent('Copied');
                    tooltip.show();
                    e.clearSelection();
                });
            });
        }

        onInit() {
            super.onInit();
            this.initStart();
        }
    }

    const addHandler = ($element) => {
        elementorFrontend.elementsHandler.addHandler(DiscoverTokens, { $element });
    };
    elementorFrontend.hooks.addAction('frontend/element_ready/dce-discover-tokens.default', addHandler);

});
