function dceGetElementSettings($element) {
        var elementSettings = [];
        var modelCID = $element.data('model-cid');
        if (elementorFrontend.isEditMode() && modelCID) {
            var settings = elementorFrontend.config.elements.data[modelCID];
            var type = settings.attributes.widgetType || settings.attributes.elType;
            var settingsKeys = elementorFrontend.config.elements.keys[ type ];
            if (!settingsKeys) {
                settingsKeys = elementorFrontend.config.elements.keys[type] = [];
                jQuery.each(settings.controls, function (name, control) {
                    if (control.frontend_available) {
                        settingsKeys.push(name);
                    }
                });
            }
            jQuery.each(settings.getActiveControls(), function (controlKey) {
                if (-1 !== settingsKeys.indexOf(controlKey)) {
                    elementSettings[ controlKey ] = settings.attributes[ controlKey ];
                }
            });
        } else {
            elementSettings = $element.data('settings') || {};
        }
        return elementSettings;
}

function dceObserveElement( $target, $function_callback ){
    if (elementorFrontend.isEditMode()) {
        // Seleziona il nodo di cui monitare la mutazione
        var elemToObserve = $target;

        /*
        // NOTA: le proprietà di observe
        mutationObserver.observe(document.documentElement, {
          attributes: true,
          characterData: true,
          childList: true,
          subtree: true,
          attributeOldValue: true,
          characterDataOldValue: true
        });*/

        // Opzioni per il monitoraggio (quali mutazioni monitorare)
        var config = {
            attributes: true,
            childList: false,
            characterData: true
        };

        var MutationObserver = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver;
        // Creazione di un'istanza di monitoraggio collegata alla funzione di callback
        var observer = new MutationObserver( $function_callback );

        // Inizio del monitoraggio del nodo target riguardo le mutazioni configurate
        observer.observe(elemToObserve, config);

        // Successivamente si può interrompere il monitoraggio
    }

}

window.dynamicooo = {};

dynamicooo.getActiveBreakpointsMinPointAndPostfix = () => {
	let breakpoints = elementorFrontend.config.responsive.activeBreakpoints;
	let ret = {};
	for (let key in breakpoints) {
		ret[key] = {
			// Elementor widescreen value is actually the min breakpoint:
			min_point: elementorFrontend.breakpoints.getDeviceMinBreakpoint(key),
			postfix: `_${key}`,
		};
	}
	ret.desktop = {
		min_point: elementorFrontend.breakpoints.getDeviceMinBreakpoint('desktop'),
		postfix: '',
	}
	return ret;
}

/**
  * Create a Swiper settings breakpoints object .
  *
  * swiperSettings: An object with with Swiper settings keys as keys, and as values
  *  an object contains:
  * - elementor_key: The Elementor Settings Key from where the value of the
  *    Swiper Key should be fetched.
  * - default_value
  * - filter: function that modifies the Elementor Setting value.>
  *
  *
  *  Returns the breakpoints object as defined by Swiper, value are
  *  automatically fetched from the Elementor settings.
  */
dynamicooo.makeSwiperBreakpoints = (swiperSettings, elementorSettings, elementorSettingsPrefix) => {
	elementorSettingsPrefix = elementorSettingsPrefix || '';
	swiperBreakpoints = {}
	let elementorBreakpoints = dynamicooo.getActiveBreakpointsMinPointAndPostfix();
	let first = true;
	for(let device in elementorBreakpoints) {
		let min_point = elementorBreakpoints[device].min_point;
		if (first) {
			min_point = 0;
			first = false;
		}
		let postfix = elementorBreakpoints[device].postfix;
		let breakpointSettings = {}
		for(let swiperSettingsKey in swiperSettings) {

			// slidesPerGroup is not visible in the settings if slidesPerView is == 1. 
			// In this case we must force the value of slidesPerGroup to 1
			// otherwise it may inherit a wrong value set on another breakpoint
			if ( swiperSettingsKey == 'slidesPerGroup' ) {
				let slidesPerView = elementorSettings[ elementorSettingsPrefix + swiperSettings['slidesPerView'].elementor_key + postfix ];
				if ( slidesPerView == 1 ) {
					breakpointSettings[swiperSettingsKey] = 1;
					continue;
				}
			}
			let elementorSettingsKey = elementorSettingsPrefix + swiperSettings[swiperSettingsKey].elementor_key;
			let default_value = swiperSettings[swiperSettingsKey].default_value;
			let postfixedKey = elementorSettingsKey + postfix;
			let value;
			if (typeof elementorSettings[postfixedKey] !== "undefined") {
				value = elementorSettings[postfixedKey];
			} else {
				// fallback to desktop value:
				value = elementorSettings[elementorSettingsKey];
			}
			if (typeof value !== "undefined") {
				let filter = Number;
				if ( typeof swiperSettings[swiperSettingsKey].filter === "function" ) {
					filter = swiperSettings[swiperSettingsKey].filter;
				}
				value = filter(value);
			} else {
				value = default_value;
			}
			breakpointSettings[swiperSettingsKey] = value;
		}
		swiperBreakpoints[min_point] = breakpointSettings;
	}
	return swiperBreakpoints;
}

window.initMap = () => {
	const event = new Event('dce-google-maps-api-loaded');
	window.dispatchEvent(event);
}
