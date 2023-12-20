var debounce = function (func, wait, immediate) {
    // 'private' variable for instance
    // The returned function will be able to reference this due to closure.
    // Each call to the returned function will share this common timer.
    var timeout;

    // Calling debounce returns a new anonymous function
    return function () {
        // reference the context and args for the setTimeout function
        var context = this,
            args = arguments;

        // Should the function be called now? If immediate is true
        //   and not already in a timeout then the answer is: Yes
        var callNow = immediate && !timeout;

        // This is the basic debounce behaviour where you can call this
        //   function several times, but it will only execute once
        //   [before or after imposing a delay].
        //   Each time the returned function is called, the timer starts over.
        clearTimeout(timeout);

        // Set the new timeout
        timeout = setTimeout(function () {

            // Inside the timeout function, clear the timeout variable
            // which will let the next execution run when in 'immediate' mode
            timeout = null;

            // Check if the function already ran with the immediate flag
            if (!immediate) {
                // Call the original function with apply
                // apply lets you define the 'this' object as well as the arguments
                //    (both captured before setTimeout)
                func.apply(context, args);
            }
        }, wait);

        // Immediate mode and no wait timer? Execute the function..
        if (callNow) func.apply(context, args);
    };
};

/** 
 * Start used on Social Share
 */

jQuery(document).ready(function() {
    jQuery(".bdt-ss-link").on("click", function() {
        var $temp = jQuery("<input>");
        jQuery("body").append($temp);
        $temp.val(jQuery(this).data("url")).select();
        document.execCommand("copy");
        $temp.remove();

        // Update the text to indicate that it has been copied
        jQuery(this).find('.bdt-social-share-title').html(jQuery(this).data('copied'));

        // Reset the text after a delay (e.g., 5 seconds)
        setTimeout(() => {
            jQuery(this).find('.bdt-social-share-title').html(jQuery(this).data('orginal'));
        }, 5000);
    });
});

/** 
 * end Social Share
 */

/**
 * Start Crypto Currency
 */

function returnCurrencySymbol(currency = null) {
    if (currency === null) return "";
    let currency_symbols = {
        USD: "$", // US Dollar
        EUR: "€", // Euro
        CRC: "₡", // Costa Rican Colón
        GBP: "£", // British Pound Sterling
        ILS: "₪", // Israeli New Sheqel
        INR: "₹", // Indian Rupee
        JPY: "¥", // Japanese Yen
        KRW: "₩", // South Korean Won
        NGN: "₦", // Nigerian Naira
        PHP: "₱", // Philippine Peso
        PLN: "zł", // Polish Zloty
        PYG: "₲", // Paraguayan Guarani
        THB: "฿", // Thai Baht
        UAH: "₴", // Ukrainian Hryvnia
        VND: "₫", // Vietnamese Dong
    };
    if (currency_symbols[currency] !== undefined) {
        return currency_symbols[currency];
    } else {
        return ""; // this is means there is not any
    }
}

/**
 * End Crypto Currency
 */

/**
 * Open Offcanvas on Mini Cart Update
 */

jQuery(document).ajaxComplete(function (event, request, settings) {
    if (request.responseJSON && typeof request.responseJSON.cart_hash !== 'undefined' && request.responseJSON.cart_hash) {
        if (jQuery('.bdt-offcanvas').hasClass('__update_cart')) {
            let id = jQuery('.bdt-offcanvas.__update_cart').attr('id');
            bdtUIkit.util.ready(function () {
                bdtUIkit.offcanvas('#' + id).show();
            });
        }
    }
});

/**
 * /Open Offcanvas on Mini Cart Update
 */
/**
 * Start accordion widget script
 */

;(function($, elementor) {
    'use strict';
    var widgetAccordion = function($scope, $) {
        var $accrContainer = $scope.find('.bdt-ep-accordion-container'),
        $accordion = $accrContainer.find('.bdt-ep-accordion');
        if (!$accrContainer.length) {
            return;
        }
        var $settings         = $accordion.data('settings');
        var activeHash        = $settings.activeHash;
        var hashTopOffset     = $settings.hashTopOffset;
        var hashScrollspyTime = $settings.hashScrollspyTime;
        var activeScrollspy   = $settings.activeScrollspy;

        if (activeScrollspy === null || typeof activeScrollspy === 'undefined'){
            activeScrollspy = 'no';
        }

        function hashHandler($accordion, hashScrollspyTime, hashTopOffset) {
            if (window.location.hash) {
                if ($($accordion).find('[data-title="' + window.location.hash.substring(1) + '"]').length) {
                        var hashTarget = $('[data-title="' + window.location.hash.substring(1) + '"]')
                        .closest($accordion)
                        .attr('id');

                        if(activeScrollspy == 'yes'){
                            $('html, body').animate({
                                easing    : 'slow',
                                scrollTop : $('#'+hashTarget).offset().top - hashTopOffset
                            }, hashScrollspyTime, function() {
                                }).promise().then(function() {
                                    bdtUIkit.accordion($accordion).toggle($('[data-title="' + window.location.hash.substring(1) + '"]').data('accordion-index'), false);
                                });
                        } else {
                            bdtUIkit.accordion($accordion).toggle($('[data-title="' + window.location.hash.substring(1) + '"]').data('accordion-index'), true);
                        }

                }
            }
        }
    if (activeHash == 'yes') {
        $(window).on('load', function() {
            if(activeScrollspy == 'yes'){
                hashHandler($accordion, hashScrollspyTime, hashTopOffset);
            }else{
                bdtUIkit.accordion($accordion).toggle($('[data-title="' + window.location.hash.substring(1) + '"]').data('accordion-index'), false);
            }
        });
        $($accordion).find('.bdt-ep-accordion-title').off('click').on('click', function(event) {
            window.location.hash = ($.trim($(this).attr('data-title')));
            hashHandler($accordion, hashScrollspyTime = 1000, hashTopOffset);
        });
        $(window).on('hashchange', function(e) {
            hashHandler($accordion, hashScrollspyTime = 1000, hashTopOffset);
        });
    }

    };

    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-accordion.default', widgetAccordion);
    });

}(jQuery, window.elementorFrontend));

/**
 * End accordion widget script
 */
/**
 * Start custom calculator widget script
 */

;
(function ($, elementor) {
    'use strict';
    var widgetCCalculator = function ($scope, $) {
        var $customCalculator = $scope.find('.bdt-ep-advanced-calculator'),
            $settings = $customCalculator.data('settings');

        if (!$customCalculator.length) {
            return;
        }

        // start main js
        function getVarableDataArray() {
            let data = [],
                variableIndex,
                onlyValueArray = [],
                formulaString = "",
                getIsRealValue,
                radioNameArrayStack = []; // radioNameArrayStack is for escaping duplicating value of radio button
            $.each(
                $($settings.id).find(
                    ".bdt-ep-advanced-calculator-field-wrap input[type=text], .bdt-ep-advanced-calculator-field-wrap input[type=hidden], .bdt-ep-advanced-calculator-field-wrap input[type=checkbox], .bdt-ep-advanced-calculator-field-wrap input[type=radio], .bdt-ep-advanced-calculator-field-wrap input[type=number], .bdt-ep-advanced-calculator-field-wrap select"
                ),
                function (index, item) {
                    variableIndex = parseInt(index) + 1;
                    let itemValue = parseInt($(item).val());
                    if ($(item).prop("type") === "radio") {
                        let currentRadioButtonName = $(item).attr('name');
                        if ($("input[name='" + currentRadioButtonName + "']").is(":checked") === true && radioNameArrayStack.indexOf(currentRadioButtonName) < 0) {
                            radioNameArrayStack.push(currentRadioButtonName);
                            getIsRealValue = getValueIfInteger($('input[name="' + currentRadioButtonName + '"]:checked').val());
                            if (Number.isInteger(getIsRealValue)) {
                                onlyValueArray.push({
                                    variable: "f" + variableIndex,
                                    value: getIsRealValue,
                                });
                            }
                            data.push({
                                type: $(item).prop("type"),
                                index: index,
                                value: $(item).val(),
                                variable: "f" + variableIndex,
                                //real_value: getValueIfInteger($(item).val())
                                real_value: getIsRealValue,
                            });
                            formulaString +=
                                Number.isInteger(itemValue) && itemValue < 0 ?
                                "-f" + variableIndex + ", " :
                                "f" + variableIndex + ", ";
                            variableIndex++;
                        }
                    } else if ($(item).prop("type") === "checkbox") {
                        // first check if this item is checkbox or radio
                        if ($(item).is(":checked") === true) {
                            getIsRealValue = getValueIfInteger($(item).val());
                            if (Number.isInteger(getIsRealValue)) {
                                onlyValueArray.push({
                                    variable: "f" + variableIndex,
                                    value: getIsRealValue,
                                });
                            }
                            data.push({
                                type: $(item).prop("type"),
                                index: index,
                                value: $(item).val(),
                                variable: "f" + variableIndex,
                                //real_value: getValueIfInteger($(item).val())
                                real_value: getIsRealValue,
                            });
                            formulaString +=
                                Number.isInteger(itemValue) && itemValue < 0 ?
                                "-f" + variableIndex + ", " :
                                "f" + variableIndex + ", ";
                            variableIndex++;
                        }
                    } else if ($(item).prop("type") === "number") {
                        getIsRealValue = getValueIfInteger($(item).val());
                        if (Number.isInteger(getIsRealValue)) {
                            onlyValueArray.push({
                                variable: "f" + variableIndex,
                                value: getIsRealValue,
                            });
                        }
                        data.push({
                            type: $(item).prop("type"),
                            index: index,
                            value: $(item).val(),
                            variable: "f" + variableIndex,
                            //real_value: getValueIfInteger($(item).val())
                            real_value: getIsRealValue,
                        });
                        formulaString +=
                            Number.isInteger(itemValue) && itemValue < 0 ?
                            "-f" + variableIndex + ", " :
                            "f" + variableIndex + ", ";
                        variableIndex++;
                    } else {
                        getIsRealValue = getValueIfInteger($(item).val());
                        if (Number.isInteger(getIsRealValue)) {
                            onlyValueArray.push({
                                variable: "f" + variableIndex,
                                value: getIsRealValue,
                            });
                        }

                        if (Number.isInteger(getIsRealValue) === false) {
                            onlyValueArray.push({
                                variable: "f" + variableIndex,
                                value: getIsRealValue,
                            });
                        }

                        data.push({
                            type: $(item).prop("type"),
                            index: index,
                            value: $(item).val(),
                            variable: "f" + variableIndex,
                            //real_value: getValueIfInteger($(item).val())
                            real_value: getIsRealValue,
                        });
                        formulaString +=
                            Number.isInteger(itemValue) && itemValue < 0 ?
                            "-f" + variableIndex + ", " :
                            "f" + variableIndex + ", ";
                        variableIndex++;
                    }
                }
            );
            return [data, onlyValueArray];
        }
        /**
         * casting value
         */
        function getValueIfInteger(value) {
            if (value === undefined) return null;
            // first convert this value to integer
            // let valueConvert = parseInt(value);
            let valueConvert = Number(value);
            // and then check if this item is integer or not. if integer then return that value otherwise return false
            return Number.isInteger(valueConvert) === true ? valueConvert : parseFloat(value);
            //return Number.isInteger(valueConvert) === true ? valueConvert : null;
        }

        /**
         * get the data settings from targetted element
         */
        function getFormulaStringFromDataSettings() {
            let str = $settings.formula,
                extract = str ? str.match(/'(.*)'/).pop() : false;
            return extract ? extract : false;
        }
        /**
         * processing calculation
         */
        function procesingFormDataWithFormulaJs() {
            let getDataArray = getVarableDataArray(),
                regexp = new RegExp("[f][1-9][0-9]{0,2}|1000$", "g"),
                str = getFormulaStringFromDataSettings(),
                match,
                value;
            let variableArray = getDataArray[1]; // here variableArray is just contain all variable information
            if (variableArray.length > 0) {
                while ((match = regexp.exec(str)) !== null) {
                    let isElementExistsCounter = 0;
                    for (let i = 0; i < variableArray.length; i++) {
                        if (variableArray[i]["variable"] === match[0]) {
                            str = str.replace(match[0], variableArray[i]["value"]);
                            isElementExistsCounter++;
                        }
                    }
                    if (isElementExistsCounter === 0) {
                        str = str.replace(match[0], null);
                    }
                }
                try {
                    value = eval("formulajs." + str);
                    $($settings.id).find(".bdt-ep-advanced-calculator-result span").text(value.toFixed(2));
                    //alert(value);
                } catch (error) {
                    // alert("error occured, invalid data format. please fix the data format and send again. thanks!");
                    $($settings.id).find('.bdt-ep-advanced-calculator-error').removeClass('bdt-hidden');
                    setTimeout(function () {
                        $($settings.id).find('.bdt-ep-advanced-calculator-error').addClass('bdt-hidden');
                    }, 5000);
                }
            }
        }

        if ($settings.resultShow == 'submit') {
            $($settings.id).find(".bdt-ep-advanced-calculator-form").submit(function (e) {
                e.preventDefault();
                procesingFormDataWithFormulaJs();
            });
        }
        if ($settings.resultShow == 'change') {
            $($settings.id).find(".bdt-ep-advanced-calculator-form input").change(function () {
                procesingFormDataWithFormulaJs();
            });
        }

    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-advanced-calculator.default', widgetCCalculator);
    });

}(jQuery, window.elementorFrontend));

/**
 * End custom calculator widget script
 */
/**
 * Start advanced counter widget script
 */

;(function($, elementor) {
    'use strict';
    // AdvancedCounter
    var widgetAdvancedCounter = function($scope, $) {
        var $AdvancedCounter = $scope.find('.bdt-ep-advanced-counter');
        if (!$AdvancedCounter.length) {
            return;
        }

        elementorFrontend.waypoint($AdvancedCounter, function() {

            var $this = $(this);
            var $settings = $this.data('settings');
            // start null checking
            var countStart = $settings.countStart;
            if (typeof countStart === 'undefined' || countStart == null) {
                countStart = 0;
            }
            
            var countNumber = $settings.countNumber;
            if (typeof countNumber === 'undefined' || countNumber == null) {
                countNumber = 0;
            }
            var decimalPlaces = $settings.decimalPlaces;
            if (typeof decimalPlaces === 'undefined' || decimalPlaces == null) {
                decimalPlaces = 0;
            }
            var duration = $settings.duration;
            if (typeof duration === 'undefined' || duration == null) {
                duration = 0;
            }
            var useEasing = $settings.useEasing;
            useEasing = !(typeof useEasing === 'undefined' || useEasing == null);
            var useGrouping = $settings.useGrouping;

            useGrouping = !(typeof useGrouping === 'undefined' || useGrouping == null);

            var counterSeparator = $settings.counterSeparator;
            if (typeof counterSeparator === 'undefined' || counterSeparator == null) {
                counterSeparator = '';
            }
            var decimalSymbol = $settings.decimalSymbol;
            if (typeof decimalSymbol === 'undefined' || decimalSymbol == null) {
                decimalSymbol = '';
            }
            var counterPrefix = $settings.counterPrefix;
            if (typeof counterPrefix === 'undefined' || counterPrefix == null) {
                counterPrefix = '';
            }
            var counterSuffix = $settings.counterSuffix;
            if (typeof counterSuffix === 'undefined' || counterSuffix == null) {
                counterSuffix = '';
            }

            // end null checking


            var options = {  
                startVal: countStart,
                numerals: $settings.language,
                decimalPlaces: decimalPlaces,
                duration: duration,
                useEasing: useEasing,
                useGrouping: useGrouping,
                separator: counterSeparator,
                decimal: decimalSymbol,
                prefix: counterPrefix,
                suffix: counterSuffix,


            };

            var demo = new CountUp($settings.id, countNumber, options);
            if (!demo.error) {
                demo.start();
            } else {
                console.error(demo.error);
            }
            //  start  for count 

        }, {
            offset: 'bottom-in-view'
        });

    };
    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-advanced-counter.default', widgetAdvancedCounter);
    });
}(jQuery, window.elementorFrontend));

/**
 * End advanced counter widget script
 */


/**
 * Start advanced divider widget script
 */

;(function($, elementor) {

    'use strict';

    // Accordion
    var widgetAdvancedDivider = function($scope, $) {

        var $avdDivider = $scope.find('.bdt-ep-advanced-divider'),
            $settings 	= $avdDivider.data('settings');

          
        if (!$avdDivider.length) {
            return;
        }

        if ($settings.animation === true) {
            elementorFrontend.waypoint($avdDivider, function() {
                var $divider = $(this).find('img');
                bdtUIkit.svg( $divider, {
                    strokeAnimation : true,
                });
            }, {
                offset: 'bottom-in-view',
                triggerOnce: (!$settings.loop)
            } );
        } else {
            var $divider = $($avdDivider).find('img');
            bdtUIkit.svg( $divider );
        }


    };


    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-advanced-divider.default', widgetAdvancedDivider);
    });

}(jQuery, window.elementorFrontend));

/**
 * End advanced divider widget script
 */


/**
 * Start advanced gmap widget script
 */
;(function ($, elementor) {
	'use strict';
	//Adavanced Google Map
	var widgetAvdGoogleMap = function ($scope, $) {

		var $advancedGoogleMap = $scope.find('.bdt-advanced-gmap'),
		 $GmapWrapper = $scope.find('.bdt-advanced-map'),

			map_settings = $advancedGoogleMap.data('map_settings'),
			markers = $advancedGoogleMap.data('map_markers'),
			map_lists = $scope.find('ul.bdt-gmap-lists div.bdt-gmap-list-item'),
			map_search_form = $scope.find('.bdt-search'),
			map_search_text_box = $scope.find('.bdt-search-input'),
			map_form = $scope.find('.bdt-gmap-search-wrapper > form');
			let listMarker,markupPhone, markupWebsite, markupPlace, markupTitle, markupContent;


		if (!$advancedGoogleMap.length) {
			return;
		}
		$GmapWrapper.removeAttr("style");
		var avdGoogleMap = new GMaps(map_settings);
		for (var i in markers) {
			listMarker = (markers[i].image !== undefined) ? markers[i].image[0]: markers[i].image;
			markupWebsite = (markers[i].website !== undefined) ? `<a href="${markers[i].website}">${markers[i].website}</a>`: '';
			markupPhone = (markers[i].phone !== undefined)? `<a href="tel:${markers[i].phone}">${markers[i].phone}</a>`: '';
			markupContent = (markers[i].content !== undefined) ? `<span class="bdt-tooltip-content">${markers[i].content}</span><br>`: '';
			markupPlace = (markers[i].place !== undefined) ? `<h5 class="bdt-tooltip-place">${markers[i].place}</h5>`: '';
			markupTitle = (markers[i].title !== undefined) ? `<h4 class="bdt-tooltip-title">${markers[i].title}</h4>`: '';
			var content = `<div class="bdt-map-tooltip-view">
						<div class="bdt-map-tooltip-view-inner">
							<div class="bdt-map-tooltip-top-image">
							<img class="bdt-map-image" src="${listMarker}" alt="" />
							</div>
							<div class="bdt-map-tooltip-bottom-footer">
								${markupTitle}
								${markupPlace}
								${markupContent}
								${markupWebsite}
								${markupPhone}
							</div>
						</div>
						</div>`;
			avdGoogleMap.addMarker({
				lat: markers[i].lat,
				lng: markers[i].lng,
				title: markers[i].title,
				icon: markers[i].icon,
				infoWindow: {
					content: content

				},
			});
		}

		if ($advancedGoogleMap.data('map_geocode')) {
			$(map_form).submit(function (e) {
				e.preventDefault();
				GMaps.geocode({
					address: $(this).find('.bdt-search-input').val().trim(),
					callback: function (results, status) {
						if (status === 'OK') {
							var latlng = results[0].geometry.location;
							avdGoogleMap.setCenter(
								latlng.lat(),
								latlng.lng()
							);
							avdGoogleMap.addMarker({
								lat: latlng.lat(),
								lng: latlng.lng()
							});
						}
					}
				});
			});
		}

		if ($advancedGoogleMap.data('map_style')) {
			avdGoogleMap.addStyle({
				styledMapName: 'Custom Map',
				styles: $advancedGoogleMap.data('map_style'),
				mapTypeId: 'map_style'
			});
			avdGoogleMap.setStyle('map_style');
		}

		$(map_lists).bind("click", function (e) {
			var mapList;
			var dataSettings = $(this).data("settings"),
			mapList = new GMaps({
				el: dataSettings.el,
				lat: dataSettings.lat,
				lng: dataSettings.lng,
				title: dataSettings.title,
				zoom: map_settings.zoom,
			});


			// console.log(dataSettings.icon);
			listMarker = (dataSettings.image !== undefined) ? dataSettings.image[0]: dataSettings.image;
			markupTitle= (dataSettings.title !== undefined) ?  `<h4 class="bdt-tooltip-title">${dataSettings.title}</h4>`: '';
			markupPlace = (dataSettings.place !== undefined) ? `<h5 class="bdt-tooltip-place">${dataSettings.place}</h5>`: '';
			markupContent =  (dataSettings.content !== undefined) ?  `<span class="bdt-tooltip-content">${dataSettings.content}</span><br>`:'';
			markupWebsite = (dataSettings.website !== undefined) ? `<a href="${dataSettings.website}">${dataSettings.website}</a>`: '';
			markupPhone = (dataSettings.phone !== undefined)? `<a href="tel:${dataSettings.phone}">${dataSettings.phone}</a>`: '';

			var content = `<div class="bdt-map-tooltip-view">
							<div class="bdt-map-tooltip-view-inner">
								<div class="bdt-map-tooltip-top-image">
								<img class="bdt-map-image" src="${listMarker}" alt="" />
								</div>
								<div class="bdt-map-tooltip-bottom-footer">
										${markupTitle}
										${markupPlace}
										${markupContent}
										${markupWebsite}
										${markupPhone}
								</div>
							</div>
						</div>`
			mapList.addMarker({
					lat: dataSettings.lat,
					lng: dataSettings.lng,
					title: dataSettings.title,
					icon: dataSettings.icon,
					infoWindow: {
					content: content,
					},
				});
		if ($advancedGoogleMap.data('map_style')) {
			mapList.addStyle({
				styledMapName: 'Custom Map',
				styles: $advancedGoogleMap.data('map_style'),
				mapTypeId: 'map_style'
			});
			mapList.setStyle('map_style');
		}


		

		
		});


		/**
			 * binding event for search form
			 */
		$(map_search_form).submit(function (e) {
			e.preventDefault();
			let searchValue = $(map_search_text_box).val().toLowerCase();
			$(map_lists).filter(function () {
				$(this).toggle($(this).text().toLowerCase().indexOf(searchValue) > -1)
			});
		});
		/**
		 * bind search event on key press
		 */
		$(map_search_text_box).keyup(function () {
			let searchValue = $(this).val().toLowerCase();
			$(map_lists).filter(function () {
				$(this).toggle($(this).text().toLowerCase().indexOf(searchValue) > -1)
			});
		});

	};
	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-advanced-gmap.default', widgetAvdGoogleMap);
	});

}(jQuery, window.elementorFrontend));

/**
 * End advanced gmap widget script
 */

/**
 * Start advanced heading widget script
 */

;
(function ($, elementor) {
    'use strict';
    var widgetAdavancedHeading = function ($scope, $) {
        var $advHeading = $scope.find('.bdt-ep-advanced-heading'),
            $advMainHeadeingInner = $advHeading.find('.bdt-ep-advanced-heading-main-title-inner');

        if (!$advHeading.length) {
            return;
        }
        var $settings = $advHeading.data('settings');
        if (typeof $settings.titleMultiColor !== "undefined") {
            if ($settings.titleMultiColor != 'yes') {
                return;
            }
            var word = $($advMainHeadeingInner).text();
            var words = word.split(" ");

            // console.log(words);
            $($advMainHeadeingInner).html('');
            var i;
            for (i = 0; i < words.length; ++i) {
                // $('#result').append('<span>'+words[i] +' </span>');
                $($advMainHeadeingInner).append('<span>' + words[i] + '&nbsp;</span>');
            }

            $($advMainHeadeingInner).find('span').each(function () {
                var randomColor = Math.floor(Math.random() * 16777215).toString(16);
                $(this).css({
                    'color': '#' + randomColor
                });
            });
        }

    };
    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-advanced-heading.default', widgetAdavancedHeading);
    });
}(jQuery, window.elementorFrontend));

/**
 * End advanced heading widget script
 */
/**
 * Start advanced icon box widget script
 */

(function($, elementor) {

    'use strict';

    // Accordion
    var widgetAdvancedIconBox = function($scope, $) {

        var $avdDivider = $scope.find('.bdt-ep-advanced-icon-box'),
            divider = $($avdDivider).find('.bdt-ep-advanced-icon-box-separator-wrap > img');

        if (!$avdDivider.length && !divider.length) {
            return;
        }

        elementorFrontend.waypoint(divider, function() {
            bdtUIkit.svg(this, {
                strokeAnimation: true
            });
        }, {
            offset: 'bottom-in-view'
        });

    };


    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-advanced-icon-box.default', widgetAdvancedIconBox);
    });

}(jQuery, window.elementorFrontend));

/**
 * End advanced icon box widget script
 */


/**
 * Start bdt advanced image gallery widget script
 */

(function ($, elementor) {

    'use strict';

    var widgetAdvancedImageGallery = function ($scope, $) {

        var $advancedImageGallery = $scope.find('.bdt-ep-advanced-image-gallery'),
            $settings = $advancedImageGallery.data('settings');

        if (!$advancedImageGallery.length) {
            return;
        }

        if ($settings.tiltShow == true) {
            var elements = document.querySelectorAll($settings.id + " [data-tilt]");
            VanillaTilt.init(elements);
        }

    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-advanced-image-gallery.default', widgetAdvancedImageGallery);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-advanced-image-gallery.bdt-carousel', widgetAdvancedImageGallery);
    });

}(jQuery, window.elementorFrontend));

/**
 * End bdt advanced image gallery widget script
 */ 
/**
 * Start advanced progress bar widget script
 */

(function($, elementor) {
    'use strict';
    // AdvancedProgressBar
    var widgetAdvancedProgressBar = function($scope, $) {
        var $advancedProgressBar = $scope.find('.bdt-ep-advanced-progress-bar-item');
        if (!$advancedProgressBar.length) {
            return;
        }
                    
 
        elementorFrontend.waypoint($advancedProgressBar, function() {
            var $this = $(this);
 
            //.bdt-progress-item .bdt-progress-fill
            var bar = $(this).find(" .bdt-ep-advanced-progress-bar-fill"),
                barPos,
                windowBtm = $(window).scrollTop() + $(window).height();
            bar.each(function() {
                barPos = $(this).offset().top;

                // if (barPos <= windowBtm) {
                    $(this).css("width", function() {
                         var thisMaxVal = $(this).attr("data-max-value");
                         var thisFillVal = $(this).attr("data-width").slice(0, -1); 
                         var formula = (thisFillVal*100) / thisMaxVal;
                         // console.log(formula);
                        // return $(this).attr("data-width");
                        return formula+'%';
                    });
                    $(this).children(".bdt-ep-advanced-progress-bar-parcentage").css({
                        '-webkit-transform': 'scale(1)',
                        '-moz-transform': 'scale(1)',
                        '-ms-transform': 'scale(1)',
                        '-o-transform': 'scale(1)',
                        'transform': 'scale(1)'
                    });
                // }
            });
        }, {
            offset: '90%'
        });
 
    };
    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-advanced-progress-bar.default', widgetAdvancedProgressBar);
    });
}(jQuery, window.elementorFrontend)); 

/**
 * End advanced progress bar widget script
 */


/**
 * Start age-gate script
 */

(function ($, elementor) {

    'use strict';

    var widgetAgeGate = function ($scope, $) {

        var $modal = $scope.find('.bdt-age-gate');

        if (!$modal.length) {
            return;
        }

        $.each($modal, function (index, val) {

            var $this = $(this),
                $settings = $this.data('settings'),
                modalID = $settings.id,
                displayTimes = $settings.displayTimes,
                closeBtnDelayShow = $settings.closeBtnDelayShow,
                delayTime = $settings.delayTime,
                widgetId = $settings.widgetId,
                requiredAge = $settings.requiredAge,
                redirect_link = $settings.redirect_link;
            var editMode = Boolean(elementorFrontend.isEditMode());

            if (editMode) {
                redirect_link = false;
            }

            var modal = {
                setLocalize: function () {
                    if (editMode) {
                        this.clearLocalize();
                        return;
                    }
                    this.clearLocalize();
                    var widgetID = widgetId,
                        localVal = 0,
                        // hours = 4;
                        hours = $settings.displayTimesExpire;

                    var expires = (hours * 60 * 60);
                    var now = Date.now();
                    var schedule = now + expires * 1000;

                    if (localStorage.getItem(widgetID) === null) {
                        localStorage.setItem(widgetID, localVal);
                        localStorage.setItem(widgetID + '_expiresIn', schedule);
                    }
                    if (localStorage.getItem(widgetID) !== null) {
                        var count = parseInt(localStorage.getItem(widgetID));
                        count++;
                        localStorage.setItem(widgetID, count);
                        // this.clearLocalize();
                    }
                },
                clearLocalize: function () {
                    var localizeExpiry = parseInt(localStorage.getItem(widgetId + '_expiresIn'));
                    var now = Date.now(); //millisecs since epoch time, lets deal only with integer
                    var schedule = now;
                    if (schedule >= localizeExpiry) {
                        localStorage.removeItem(widgetId + '_expiresIn');
                        localStorage.removeItem(widgetId);
                    }
                },
                modalFire: function () {
                    var displayTimes = 1;
                    var firedNotify = parseInt(localStorage.getItem(widgetId)) || 0;

                    if ((displayTimes !== false) && (firedNotify >= displayTimes)) {
                        return;
                    }
                    bdtUIkit.modal($this, {
                        bgclose: false,
                        keyboard: false
                    }).show();
                },
                ageVerify: function () {
                    var init = this;
                    var firedNotify = parseInt(localStorage.getItem(widgetId)) || 0;
                    $('#' + widgetId).find('.bdt-button').on('click', function () {
                        var input_age = parseInt($('#' + widgetId).find('.bdt-age-input').val());
                        if (input_age >= requiredAge) {
                            init.setLocalize();
                            firedNotify += 1;
                            bdtUIkit.modal($this).hide();
                        } else {
                            if (redirect_link == false) {
                                $('.modal-msg-text').removeClass('bdt-hidden');
                                return;
                            } else {
                                $('.modal-msg-text').removeClass('bdt-hidden');
                            }
                            window.location.replace(redirect_link);
                        }
                    });

                    bdtUIkit.util.on($this, 'hidden', function () {

                        if(editMode){
                            return;
                        }

                        if (redirect_link == false && firedNotify <= 0) {

                            setTimeout( function(){
                                init.modalFire();
                            }, 1500);

                            return;
                        }

                        if (redirect_link !== false && firedNotify <= 0) {
                            window.location.replace(redirect_link);
                        }
                    });
                },
                closeBtnDelayShow: function () {
                    var $modal = $('#' + modalID);
                    $modal.find('#bdt-modal-close-button').hide(0);
                    $modal.on("shown", function () {
                            $('#bdt-modal-close-button').hide(0).fadeIn(delayTime);
                        })
                        .on("hide", function () {
                            $modal.find('#bdt-modal-close-button').hide(0);
                        });
                },

                default: function () {
                    this.modalFire();
                },
                init: function () {
                    var init = this;
                    init.default();
                    init.ageVerify();

                    if (closeBtnDelayShow) {
                        init.closeBtnDelayShow();
                    }
                }
            };

            // kick the modal
            modal.init();

        });
    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-age-gate.default', widgetAgeGate);
    });

}(jQuery, window.elementorFrontend));

/**
 * End age-gate script
 */
; (function ($, elementor) {
  $(window).on("elementor/frontend/init", function () {
    var ModuleHandler = elementorModules.frontend.handlers.Base,
      AnimatedGradientBackground;

    AnimatedGradientBackground = ModuleHandler.extend({
      bindEvents: function () {
        this.run();
      },

      getDefaultSettings: function () {
        return {
          allowHTML: true,
        };
      },
      onElementChange: debounce(function (prop) {
        if (prop.indexOf('element_pack_agbg_') !== -1) {
          this.run();
        }
      }, 400),

      settings: function (key) {
        return this.getElementSettings("element_pack_agbg_" + key);
      },

      run: function () {
        if (this.settings('show') !== 'yes') {
          return;
        }
        const sectionID = this.$element.data("id");
        const widgetContainer = document.querySelector(".elementor-element-" + sectionID);
        const checkClass = $(widgetContainer).find(".bdt-animated-gradient-background");

        if ($(checkClass).length < 1) {
          $(widgetContainer).prepend('<canvas id="canvas-basic-' + sectionID + '" class="bdt-animated-gradient-background"></canvas>');
        }

        const gradientID = $(widgetContainer).find(".bdt-animated-gradient-background").attr("id");

        let color_list = this.settings("color_list");
        let colors = [];
        color_list.forEach((color) => {
          colors.push([color.start_color, color.end_color]);
        });

        var direction = (this.settings("direction") !== undefined) ? this.settings('direction') : 'diagonal';
        var transitionSpeed = (this.settings("transitionSpeed") !== undefined) ? this.settings('transitionSpeed.size') : '5500';
        var granimInstance = new Granim({
          element: "#" + gradientID,
          direction: direction,
          isPausedWhenNotInView: true,
          states: {
            "default-state": {
              gradients: colors,
              transitionSpeed: transitionSpeed,
            },
          },
        });
      },
    });

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/section",
      function ($scope) {
        elementorFrontend.elementsHandler.addHandler(AnimatedGradientBackground, {
          $element: $scope,
        });
      }
    );

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/container",
      function ($scope) {
        elementorFrontend.elementsHandler.addHandler(AnimatedGradientBackground, {
          $element: $scope,
        });
      }
    );

  });
})(jQuery, window.elementorFrontend);
/**
 * Start animated heading widget script
 */

;
(function ($, elementor) {

  'use strict';

  var widgetAnimatedHeading = function ($scope, $) {

    var $heading = $scope.find('.bdt-heading > *'),
      $animatedHeading = $heading.find('.bdt-animated-heading'),
      $settings = $animatedHeading.data('settings');

    if (!$heading.length) {
      return;
    }

    function kill() {
      var splitTextTimeline = gsap.timeline(),
        mySplitText = new SplitText($quote, {
          type: "chars, words, lines"
        });
      splitTextTimeline.clear().time(0);
      mySplitText.revert();
    }

    if ($settings.layout === 'animated') {
      elementorFrontend.waypoint($heading, function () {
        $($animatedHeading).Morphext($settings);
      }, {
        offset: 'bottom-in-view',
      });
    } else if ($settings.layout === 'typed') {
      elementorFrontend.waypoint($heading, function () {
        var animateSelector = $($animatedHeading).attr('id');
        var typed = new Typed('#' + animateSelector, $settings);
      }, {
        offset: 'bottom-in-view',
      });
    } else if ($settings.layout === 'split_text') {

      var $quote = $($heading);

      var splitTextTimeline = gsap.timeline(),
        mySplitText = new SplitText($quote, {
          type: "chars, words, lines"
        });


      gsap.set($quote, {
        perspective: $settings.anim_perspective //400
      });


      elementorFrontend.waypoint($heading, function () {
        kill();
        mySplitText.split({
          type: 'chars, words, lines'
        });
        var stringType = '';
        if ('lines' == $settings.animation_on) {
          stringType = mySplitText.lines;
        } else if ('chars' == $settings.animation_on) {
          stringType = mySplitText.chars;
        } else {
          stringType = mySplitText.words;
        }
        splitTextTimeline.staggerFrom(stringType, 0.5, {
          opacity: 0, //0
          scale: $settings.anim_scale, //0
          y: $settings.anim_rotation_y, //80
          rotationX: $settings.anim_rotation_x, //180
          transformOrigin: $settings.anim_transform_origin, //0% 50% -50  
        }, $settings.anim_duration);
      }, {

        offset: 'bottom-in-view',
        triggerOnce: ($settings.anim_repeat)
      });

    }

    $($heading).animate({
      easing: 'slow',
      opacity: 1,
    }, 500);


  };


  jQuery(window).on('elementor/frontend/init', function () {
    elementorFrontend.hooks.addAction('frontend/element_ready/bdt-animated-heading.default', widgetAnimatedHeading);
  });

}(jQuery, window.elementorFrontend));

/**
 * End animated heading widget script
 */
/**
 * Start audio player widget script
 */

( function( $, elementor ) {

	'use strict';

	//Audio Player
	var widgetAudioPlayer = function( $scope, $ ) {

		var $audioPlayer         = $scope.find( '.bdt-audio-player .jp-jplayer' ),
			$container 			 = $audioPlayer.next('.jp-audio').attr('id'),
			$settings 		 	 = $audioPlayer.data('settings');
			

		if ( ! $audioPlayer.length ) {
			return;
		}

		$($audioPlayer).jPlayer({
			ready: function (event) {
				$(this).jPlayer('setMedia', {
					title : $settings.audio_title,
					mp3   : $settings.audio_source
				});
				if($settings.autoplay) {
					$(this).jPlayer('play', 1);
				}
			},
			play: function() {
				$(this).next('.jp-audio').removeClass('bdt-player-played');
				$(this).jPlayer('pauseOthers');
			},
			ended: function() {
		    	$(this).next('.jp-audio').addClass('bdt-player-played');
		  	},
			timeupdate: function(event) {
				if($settings.time_restrict) {
					if ( event.jPlayer.status.currentTime > $settings.restrict_duration ) {
						$(this).jPlayer('stop');
					}
				}
			},

			cssSelectorAncestor : '#' + $container,
			useStateClassSkin   : true,
			autoBlur            : $settings.smooth_show,
			smoothPlayBar       : true,
			keyEnabled          : $settings.keyboard_enable,
			remainingDuration   : true,
			toggleDuration      : true,
			volume              : $settings.volume_level,
			loop                : $settings.loop
			
		});

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-audio-player.default', widgetAudioPlayer );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-audio-player.bdt-poster', widgetAudioPlayer );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * Start audio player widget script
 */


; (function ($, elementor) {

$(window).on('elementor/frontend/init', function () {

    elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {

        $scope.hasClass('elementor-element-edit-mode') && $scope.addClass('bdt-background-overlay-yes');

    });

});

}) (jQuery, window.elementorFrontend);
; (function ($, elementor) {
$(window).on('elementor/frontend/init', function () {
    var ModuleHandler = elementorModules.frontend.handlers.Base,
        BackgroundParallax;

    BackgroundParallax = ModuleHandler.extend({

        bindEvents: function () {
            this.run();
        },

        getDefaultSettings: function () {
            return {
                media: false,
                easing: 1,
                viewport: 1,
            };
        },

        onElementChange: debounce(function (prop) {
            if ((prop.indexOf('section_parallax_') !== -1) || (prop.indexOf('ep_parallax_') !== -1)) {
                this.run();
            }
        }, 400),

        settings: function (key) {
            // return this.getElementSettings('section_parallax_' + key);
            return this.getElementSettings(key);
        },

        run: function () {
            var options = this.getDefaultSettings(),
                element = this.findElement('.elementor-element').get(0);

            if (jQuery(this.$element).hasClass("elementor-element")) {
                element = this.$element.get(0);
            }

            if (this.settings('section_parallax_x_value.size')) {
                options.bgx = this.settings('section_parallax_x_value.size') || 0;
            }
            if (this.settings('section_parallax_value.size')) {
                options.bgy = this.settings('section_parallax_value.size') || 0;
            }


            if (this.settings('ep_parallax_bg_colors')) {
                if (this.settings('ep_parallax_bg_border_color_start') || this.settings('ep_parallax_bg_border_color_end')) {
                    options.borderColor = [this.settings('ep_parallax_bg_border_color_start') || 0, this.settings('ep_parallax_bg_border_color_end') || 0];
                }
            }
            if (this.settings('ep_parallax_bg_colors')) {
                if (this.settings('ep_parallax_bg_color_start') || this.settings('ep_parallax_bg_color_end')) {
                    options.backgroundColor = [this.settings('ep_parallax_bg_color_start') || 0, this.settings('ep_parallax_bg_color_end') || 0];
                }
            }

            if ((this.settings('section_parallax_on')) && (this.settings('section_parallax_on') === 'yes')) {
                if (
                    this.settings('section_parallax_x_value') ||
                    this.settings('section_parallax_value') ||
                    this.settings('ep_parallax_bg_colors')
                ) {
                    this.bgParallax = bdtUIkit.parallax(element, options);
                }
            }

        }
    });


    elementorFrontend.hooks.addAction('frontend/element_ready/section', function ($scope) {
        elementorFrontend.elementsHandler.addHandler(BackgroundParallax, {
            $element: $scope
        });
    });
    
    elementorFrontend.hooks.addAction('frontend/element_ready/container', function ($scope) {
        elementorFrontend.elementsHandler.addHandler(BackgroundParallax, {
            $element: $scope
        });
    });

});
})(jQuery, window.elementorFrontend);
;(function($, elementor){
    'use strict';

    $(window).on('elementor/frontend/init', function () {

        var ModuleHandler = elementorModules.frontend.handlers.Base, BarCode;


        BarCode = ModuleHandler.extend({
            bindEvents: function () {
                this.run();
            },

            getDefaultSettings: function () {
                return {
                    format: 'code128'
                };
            },

            onElementChange: debounce(function (prop) {
                if (prop.indexOf('ep_barcode') !== -1) {
                    this.run();
                }
            }, 400),

            settings: function (key) {
                return this.getElementSettings('ep_barcode_' + key);
            },

            run: function () {

                var options = this.getDefaultSettings();
                var element = this.findElement('.elementor-widget-container').get(0);
                if (jQuery(this.$element).hasClass('elementor-section')) {
                    element = this.$element.get(0);
                }
                var $container = this.$element.find(".bdt-ep-barcode");
                if (!$container.length) {
                    return;
                }

                var content = this.settings('content');
                options.displayValue = (this.settings('show_label') === 'yes');
                options.format = this.settings('format');
                options.text = (this.settings('label_text')) ? this.settings('label_text') : '';
                options.width = (this.settings('width.size')) ? this.settings('width.size') : 2;
                options.height = (this.settings('height.size')) ? this.settings('height.size') : 40;
                options.fontOptions = (this.settings('font_width')) ? this.settings('font_width') : 'normal';
                options.textAlign = (this.settings('label_alignment')) ? this.settings('label_alignment') : 'center';
                options.textPosition = (this.settings('label_position')) ? this.settings('label_position') : 'bottom';
                options.textMargin = (this.settings('label_spacing.size')) ? this.settings('label_spacing.size') : 2;
                options.margin = 0;

                JsBarcode('#bdt-ep-barcode-' + this.$element.data('id'), content, options);

            }
        });


        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-barcode.default',
            function ($scope) {
                elementorFrontend.elementsHandler.addHandler(BarCode, {
                    $element: $scope
                });
            }
        );
    });

})(jQuery, window.elementorFrontend);
(function ($, elementor) {
  "use strict";

  $(window).on("elementor/frontend/init", function () {
    var ModuleHandler = elementorModules.frontend.handlers.Base,
      SvgBlob;

    SvgBlob = ModuleHandler.extend({
      bindEvents: function () {
        this.run();
      },

      getDefaultSettings: function () {
        return {};
      },

      settings: function (key) {
        return this.getElementSettings("svg_blob_" + key);
      },

      run: function () {
        var options = this.getDefaultSettings();
        var element = this.findElement(".elementor-widget-container").get(0);
        if (jQuery(this.$element).hasClass("elementor-section")) {
          element = this.$element.get(0);
        }
        var $container = this.$element.find(".bdt-svg-blob");
        if (!$container.length) {
          return;
        }
        const path = $container.data("settings");
        const firstSVG = $container.find("path")[0];
        options = {
          targets: firstSVG,
          d: [{ value: path || [] }],
          easing: 'easeOutQuad',
          direction: 'alternate',
          loop: this.settings('loop') === 'yes',
          duration:
            this.settings('duration.size') !== ''
              ? this.settings('duration.size')
              : 2000,
          delay:
            this.settings('delay.size') !== ''
              ? this.settings('delay.size')
              : 10,
          endDelay:
            this.settings('end_delay.size') !== ''
              ? this.settings('end_delay.size')
              : 10,
        };
        anime(options);
      },
    });

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/bdt-svg-blob.default",
      function ($scope) {
        elementorFrontend.elementsHandler.addHandler(SvgBlob, {
          $element: $scope,
        });
      }
    );
  });
})(jQuery, window.elementorFrontend);

(function ($, elementor) {
  $(window).on("elementor/frontend/init", function () {
    let ModuleHandler = elementorModules.frontend.handlers.Base,
      ListMarquee;

    ListMarquee = ModuleHandler.extend({
      bindEvents: function () {
        this.run();
      },
      getDefaultSettings: function () {
        return {
          allowHTML: true,
        };
      },

      onElementChange: debounce(function (prop) {
        if (prop.indexOf("marquee_") !== -1) {
          this.run();
        }
      }, 400),

      settings: function (key) {
        return this.getElementSettings("marquee_" + key);
      },

      run: function () {
        let isElementorEditor = elementorFrontend.isEditMode();
        if (isElementorEditor) {
          return;
        }
        const widgetID = this.$element.data("id");
        var self = this;
        var options = this.getDefaultSettings();
        var element = this.findElement(".elementor-widget-container").get(0);
        if (jQuery(this.$element).hasClass("elementor-section")) {
          element = this.$element.get(0);
        }

        var $container = this.$element.find(".bdt-marquee");
        if (!$container) {
          return;
        }

        let advancedOptions =
          self.settings("advanced") === "yes" ? true : false;

        if (advancedOptions) {
          $("body").css("overflow-x", "hidden");
        }

        var widgetContainer = ".elementor-element-" + widgetID;

        const boxes = gsap.utils.toArray(widgetContainer + " .marquee-content");
        let reversedDirection =
            self.settings("direction") === "right" ? true : false,
          marqueeSpeed = self.settings("speed")
            ? self.settings("speed") / 100
            : 1,
          isClickable = self.settings("clickable") === "yes" ? true : false,
          isPauseOnHover =
            self.settings("pause_on_hover") === "yes" ? true : false,
          isDraggable = self.settings("draggable") === "yes" ? true : false,
          spacing = self.settings("spacing") ? self.settings("spacing") : 0,
          isLoop = self.settings("loop") === "yes" ? true : false;

        let activeElement;
        const loop = self.horizontalLoop(boxes, {
          paused: false,
          draggable: isDraggable,
          repeat: -1,
          speed: marqueeSpeed,
          paddingRight: spacing,
          reversed: reversedDirection,
          center: true,
          reversedDirection: reversedDirection,
          onChange: (element, index) => {
            // when the active element changes, this function gets called.
            activeElement && activeElement.classList.remove("active");
            element.classList.add("active");
            activeElement = element;
          },
        });

        if (isPauseOnHover) {
          self.pauseOnHover(loop, boxes);
        }

        if (isClickable) {
          $(boxes).on("mouseleave", () => {
            if (loop.reversed()) {
              loop.play();
              loop.reverse();
            } else {
              loop.play();
              loop.reversed();
            }
          });
          // loop.pause();
          boxes.forEach((box, i) => {
            box.addEventListener("click", () => {
              loop.toIndex(i, {
                duration: 0.8,
                ease: "power1.inOut",
              });
            });
          });
        }
      },

      pauseOnHover: function (tween, boxes) {
        $(boxes).on("mouseenter", () => {
          tween.pause();
        });

        $(boxes).on("mouseleave", () => {
          if (tween.reversed()) {
            tween.play();
            tween.reverse();
          } else {
            tween.play();
            tween.reversed();
          }
        });
      },
      horizontalLoop: function (items, config, reverseDirection = false) {
        var self = this;
        items = gsap.utils.toArray(items);
        config = config || {};
        let onChange = config.onChange,
          lastIndex = 0,
          tl = gsap.timeline({
            repeat: config.repeat,
            paused: config.paused,
            defaults: { ease: "none" },
            onReverseComplete: () =>
              tl.totalTime(tl.rawTime() + tl.duration() * 100),
          }),
          length = items.length,
          startX = items[0].offsetLeft,
          times = [],
          widths = [],
          spaceBefore = [],
          xPercents = [],
          curIndex = 0,
          center = config.center,
          pixelsPerSecond = (config.speed || 1) * 100,
          snap =
            config.snap === false
              ? (v) => v
              : gsap.utils.snap(config.snap || 1), // some browsers shift by a pixel to accommodate flex layouts, so for example if width is 20% the first element's width might be 242px, and the next 243px, alternating back and forth. So we snap to 5 percentage points to make things look more natural
          timeOffset = 0,
          container =
            center === true
              ? items[0].parentNode
              : gsap.utils.toArray(center)[0] || items[0].parentNode,
          totalWidth,
          getTotalWidth = () =>
            items[length - 1].offsetLeft +
            (xPercents[length - 1] / 100) * widths[length - 1] -
            startX +
            spaceBefore[0] +
            items[length - 1].offsetWidth *
              gsap.getProperty(items[length - 1], "scaleX") +
            (parseFloat(config.paddingRight) || 0),
          populateWidths = () => {
            let b1 = container.getBoundingClientRect(),
              b2;
            items.forEach((el, i) => {
              widths[i] = parseFloat(gsap.getProperty(el, "width", "px"));
              xPercents[i] = snap(
                (parseFloat(gsap.getProperty(el, "x", "px")) / widths[i]) *
                  100 +
                  gsap.getProperty(el, "xPercent")
              );
              b2 = el.getBoundingClientRect();
              spaceBefore[i] = b2.left - (i ? b1.right : b1.left);
              b1 = b2;
            });
            gsap.set(items, {
              // convert "x" to "xPercent" to make things responsive, and populate the widths/xPercents Arrays to make lookups faster.
              xPercent: (i) => xPercents[i],
            });
            totalWidth = getTotalWidth();
          },
          timeWrap,
          populateOffsets = () => {
            timeOffset = center
              ? (tl.duration() * (container.offsetWidth / 2)) / totalWidth
              : 0;
            center &&
              times.forEach((t, i) => {
                times[i] = timeWrap(
                  tl.labels["label" + i] +
                    (tl.duration() * widths[i]) / 2 / totalWidth -
                    timeOffset
                );
              });
          },
          getClosest = (values, value, wrap) => {
            let i = values.length,
              closest = 1e10,
              index = 0,
              d;
            while (i--) {
              d = Math.abs(values[i] - value);
              if (d > wrap / 2) {
                d = wrap - d;
              }
              if (d < closest) {
                closest = d;
                index = i;
              }
            }
            return index;
          },
          populateTimeline = () => {
            let i, item, curX, distanceToStart, distanceToLoop;
            tl.clear();
            for (i = 0; i < length; i++) {
              item = items[i];
              curX = (xPercents[i] / 100) * widths[i];
              distanceToStart =
                item.offsetLeft + curX - startX + spaceBefore[0];
              distanceToLoop =
                distanceToStart + widths[i] * gsap.getProperty(item, "scaleX");
              tl.to(
                item,
                {
                  xPercent: snap(((curX - distanceToLoop) / widths[i]) * 100),
                  duration: distanceToLoop / pixelsPerSecond,
                },
                0
              )
                .fromTo(
                  item,
                  {
                    xPercent: snap(
                      ((curX - distanceToLoop + totalWidth) / widths[i]) * 100
                    ),
                  },
                  {
                    xPercent: xPercents[i],
                    duration:
                      (curX - distanceToLoop + totalWidth - curX) /
                      pixelsPerSecond,
                    immediateRender: false,
                  },
                  distanceToLoop / pixelsPerSecond
                )
                .add("label" + i, distanceToStart / pixelsPerSecond);
              times[i] = distanceToStart / pixelsPerSecond;
            }
            timeWrap = gsap.utils.wrap(0, tl.duration());
          },
          refresh = (deep) => {
            let progress = tl.progress();
            tl.progress(0, true);
            populateWidths();
            deep && populateTimeline();
            populateOffsets();
            deep && tl.draggable
              ? tl.time(times[curIndex], true)
              : tl.progress(progress, true);
          },
          proxy;
        gsap.set(items, { x: 0 });
        populateWidths();
        populateTimeline();
        populateOffsets();
        window.addEventListener("resize", () => refresh(true));
        function toIndex(index, vars) {
          vars = vars || {};

          if (reverseDirection) {
            index = length - index;
          }
          Math.abs(index - curIndex) > length / 2 &&
            (index += index > curIndex ? -length : length); // always go in the shortest direction
          let newIndex = gsap.utils.wrap(0, length, index),
            time = times[newIndex];
          if (time > tl.time() !== index > curIndex) {
            // if we're wrapping the timeline's playhead, make the proper adjustments
            time += tl.duration() * (index > curIndex ? 1 : -1);
          }
          if (time < 0 || time > tl.duration()) {
            vars.modifiers = { time: timeWrap };
          }
          curIndex = newIndex;
          vars.overwrite = true;
          gsap.killTweensOf(proxy);
          return tl.tweenTo(time, vars);
        }
        tl.next = (vars) => toIndex(curIndex + 1, vars);
        tl.previous = (vars) => toIndex(curIndex - 1, vars);
        tl.current = () => curIndex;
        tl.toIndex = (index, vars) => toIndex(index, vars);
        tl.closestIndex = (setCurrent) => {
          let index = getClosest(times, tl.time(), tl.duration());
          setCurrent && (curIndex = index);
          return index;
        };
        tl.times = times;
        tl.progress(1, true).progress(0, true); // pre-render for performance
        if (config.reversed) {
          tl.vars.onReverseComplete();
          tl.reverse();
        }

        if (config.draggable && typeof Draggable === "function") {
          proxy = document.createElement("div");
          let wrap = gsap.utils.wrap(0, 1),
            ratio,
            startProgress,
            draggable,
            dragSnap,
            align = () =>
              tl.progress(
                wrap(startProgress + (draggable.startX - draggable.x) * ratio)
              ),
            syncIndex = () => tl.closestIndex(true);
          typeof InertiaPlugin === "undefined" &&
            console.warn(
              "InertiaPlugin required for momentum-based scrolling and snapping. https://greensock.com/club"
            );
          draggable = Draggable.create(proxy, {
            trigger: items[0].parentNode,
            type: "x",
            onPressInit() {
              gsap.killTweensOf(tl);
              startProgress = tl.progress();
              refresh();
              ratio = 1 / totalWidth;
              gsap.set(proxy, { x: startProgress / -ratio });
            },
            onDrag: align,
            onThrowUpdate: align,
            inertia: true,
            snap: (value) => {
              let time = -(value * ratio) * tl.duration(),
                wrappedTime = timeWrap(time),
                snapTime = times[getClosest(times, wrappedTime, tl.duration())],
                dif = snapTime - wrappedTime;
              Math.abs(dif) > tl.duration() / 2 &&
                (dif += dif < 0 ? tl.duration() : -tl.duration());
              return (time + dif) / tl.duration() / -ratio;
            },
            onRelease: syncIndex,
            onThrowComplete: syncIndex,
          })[0];
          tl.draggable = draggable;
        }
        tl.closestIndex(true);
        onChange && onChange(items[curIndex], curIndex);
        return tl;
      },
    });

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/bdt-marquee.default",
      function ($scope) {
        elementorFrontend.elementsHandler.addHandler(ListMarquee, {
          $element: $scope,
        });
      }
    );
  });
})(jQuery, window.elementorFrontend);

(function ($, elementor) {
  "use strict";
  $(window).on("elementor/frontend/init", function () {
    var ModuleHandler = elementorModules.frontend.handlers.Base,
      PostGrid;

    PostGrid = ModuleHandler.extend({
      bindEvents: function () {
        this.run();
      },

      getDefaultSettings: function () {
        return {};
      },

      onElementChange: debounce(function (prop) {
        if (prop.indexOf("post_grid") !== -1) {
          this.run();
        }
      }, 400),

      settings: function (key) {
        return this.getElementSettings("post_grid_" + key);
      },

      run: function () {
        var options = this.getDefaultSettings();
        var content = this.settings("ajax_loadmore");

        var element = this.findElement(".elementor-widget-container").get(0);
        if (jQuery(this.$element).hasClass("elementor-section")) {
          element = this.$element.get(0);
        }
        var $container = this.$element.find(".bdt-post-grid");
        if (!$container.length) {
          return;
        }
        if (content === undefined) {
          return;
        }
        var settingsLoadmore = this.settings("show_loadmore");
        var settingsInfiniteScroll = this.settings("show_infinite_scroll");

        var loadButtonContainer = this.$element.find(".bdt-loadmore-container");
        var grid = $container.find(".bdt-grid");
        var loadButton = loadButtonContainer.find(".bdt-loadmore");
        var loading = false;
        var settings = $container.data("settings");
        var readMore = $container.data("settings-button");
        // var page = 1;
        var currentItemCount = settings.posts_per_page;

        var loadMorePosts = function () {
          var dataSettings = {
            action: "ep_loadmore_posts",
            settings: settings,
            readMore: readMore,
            per_page: settings.ajax_item_load,
            offset: currentItemCount,
            paged: settings.paged,
            nonce: settings.nonce,
          };
          jQuery.ajax({
            url: window.ElementPackConfig.ajaxurl,
            type: "post",
            data: dataSettings,
            success: function (response) {
              $(grid).append(response.markup);
              currentItemCount += settings.ajax_item_load;

              // if(settings.paged === "yes") {
                settings.paged += 1;
              // }
              loading = false;
              if (settingsLoadmore === "yes") {
                loadButton.html("Load More");
              }

              if ($(response.markup).length < settings.ajax_item_load) {
                loadButton.hide();
                loadButtonContainer.hide();
              }
            },
          });
        };

        if (settingsLoadmore === "yes") {
          $(loadButton).on("click", function () {
            if (!loading) {
              loading = true;
              loadButton.html("loading...");
              loadMorePosts();
            }
          });
        }

        if (settingsInfiniteScroll === "yes") {
          $(window).scroll(function () {
            if (
              $(window).scrollTop() ==
              $(document).height() - $(window).height()
            ) {
              $(loadButton).css("display", "block");
              loadMorePosts();
            } else {
              return;
            }
          });
        }
      },
    });

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/bdt-post-grid.default",
      function ($scope) {
        elementorFrontend.elementsHandler.addHandler(PostGrid, {
          $element: $scope,
        });
      }
    );
  });
})(jQuery, window.elementorFrontend);

; (function ($, elementor) {
    'use strict';

    $(window).on('elementor/frontend/init', function () {

        var ModuleHandler = elementorModules.frontend.handlers.Base, MegaMenu;


        MegaMenu = ModuleHandler.extend({
            bindEvents: function () {
                this.run();
            },

            getDefaultSettings: function () {
                return {};
            },

            onElementChange: debounce(function (prop) {
                if (prop.indexOf('ep_megamenu_') !== -1) {
                    this.run();
                }
            }, 400),

            settings: function (key) {
                return this.getElementSettings('ep_megamenu_' + key);
            },

            run: function () {
                var $this = this;
                var options = this.getDefaultSettings(),
                    widgetID = this.$element.data('id');
                var element = this.findElement('.elementor-widget-container').get(0);
                if (jQuery(this.$element).hasClass('elementor-section')) {
                    element = this.$element.get(0);
                }
                var $container = this.$element.find(".ep-megamenu");


                if (!$container.length) {
                    return;
                }

                //initial breking issue fixed
                $container.find('.megamenu-header-mobile').removeAttr('style');
                $container.removeClass('initialized');

               var dropMenu =  $($container).find('.ep-megamenu-vertical-dropdown');

                bdtUIkit.drop(dropMenu, {
                    offset: (this.settings("vertical_dropdown_offset") !== undefined) ? this.settings('vertical_dropdown_offset') : '10',
                    animation: (this.settings("vertical_dropdown_animation_type") !== undefined) ? this.settings('vertical_dropdown_animation_type') : 'fade',
                    duration: (this.settings("vertical_dropdown_animation_duration") !== undefined) ? this.settings('vertical_dropdown_animation_duration') : '200',
                    mode: (this.settings("vertical_dropdown_mode") !== undefined) ? this.settings('vertical_dropdown_mode') : 'click',
                    animateOut: (this.settings("vertical_dropdown_animate_out") !== undefined) ? this.settings('vertical_dropdown_animate_out') : false,
                });

                //has megamenu
                var $megamenu_items = $container.find('.ep-has-megamenu');
                var $subDropdown = $container.find('.ep-default-submenu-panel');




                //dropdown options
                options.flip = false;
                options.offset = (this.settings("offset.size") !== '') ? this.settings('offset.size') : '10';
                options.animation = (this.settings("animation_type") !== undefined) ? this.settings('animation_type') : 'fade';
                options.duration = (this.settings("animation_duration") !== undefined) ? this.settings('animation_duration') : '200';
                options.mode = (this.settings("mode") !== undefined) ? this.settings('mode') : 'hover';
                options.animateOut = (this.settings("animate_out") !== undefined) ? this.settings('animate_out') : false;



                $($megamenu_items).each(function (index, item) {
                    var $drop = $(item).find('.ep-megamenu-panel');
                    var widthType = $(item).data('width-type');

                    var defaltWidthSelector = $(item).closest('.elementor-container');
                    if (defaltWidthSelector.length <= 0){
                        var defaltWidthSelector =
                          $(item).closest(".e-con-inner");
                    }

                    if ('horizontal' === $this.settings('direction')) {
                        switch (widthType) {
                            case 'custom':
                                options.stretch = null;
                                options.target = null;
                                options.boundary = null;
                                options.pos = $(item).data('content-pos');
                                $(this).find(".ep-megamenu-panel").css({
                                    "min-width": $(item).data('content-width'),
                                    "max-width": $(item).data('content-width'),
                                });
                                break;
                            case 'full':
                                options.stretch = 'x';
                                options.target = '#ep-megamenu-' + widgetID;
                                options.boundary = false;
                                break;
                            default:
                                options.stretch = 'x';
                                options.target = '#ep-megamenu-' + widgetID;
                                options.boundary = defaltWidthSelector
                            break;
                        }
                    } else if ('vertical' === $this.settings('direction')) {
                        switch (widthType) {
                            case 'custom':
                                options.stretch = false;
                                options.target = false;
                                options.boundary = false;
                                $(this).find(".ep-megamenu-panel").css({
                                    "min-width": $(item).data('content-width'),
                                    "max-width": $(item).data('content-width'),
                                });
                                break;
                            default:
                                options.stretch = 'x';
                                break;

                        }
                        //check is RTL
                        if ($($container).data("is-rtl") === 1) {
                            options.pos = 'left-top';
                        } else {
                            options.pos = 'right-top';
                        }
                    }

                    // options.toggle = $($drop).closest('.menu-item').find('.ep-menu-nav-link');

                    bdtUIkit.drop($drop, options);
                });


                $($subDropdown).each(function (index, item) {
                    if ('horizontal' === $this.settings('direction')) {
                        if ($(item).hasClass('ep-parent-element')){
                            options.pos = 'bottom-left';
                        }else{
                            options.pos = 'right-top';
                        }
                    } else if ('vertical' === $this.settings('direction')) {
                                options.stretch = false;
                                $(this).find(".ep-megamenu-panel").css({
                                    "padding-left": "20px"
                                });

                        //check is RTL
                        if ($($container).data("is-rtl") === 1) {
                            options.pos = 'left-top';
                        } else {
                            options.pos = 'right-top';
                        }
                    }
                    options.stretch = false;
                    options.target = false;
                    options.flip = true;


                    bdtUIkit.drop(item, options);
                });


                  var dropWrapper = $(element).closest('.elementor-top-section');
                if(dropWrapper.length <= 0){
                    var dropWrapper = $(element).closest(".elementor-element.e-con.e-parent");
                    if(dropWrapper.length <= 0){
                        var dropWrapper = $(element).closest(".elementor-widget-bdt-mega-menu");
                    }
                }


                if ($(dropWrapper).find('.ep-virtual-area').length === 0) {
                    // code to run if it isn't there
                    $('#ep-megamenu-' + widgetID).clone().appendTo(dropWrapper).wrapAll('<div class="ep-virtual-area" />');
                    dropWrapper.find('.ep-virtual-area [id]').each(function (index, obj) {
                        let old_id = $(obj).attr('id');
                        $(obj).attr('id', old_id + '-virtual');

                    });
                    dropWrapper.find('.ep-virtual-area [fill]').each(function (index, obj) {
                        let fill_id = $(obj).attr('fill');
                        if (fill_id.indexOf('url(#') == 0) {
                            $(obj).attr('fill', ('url(#', fill_id.slice(0, -1) + '-virtual)'));
                        }
                    });
                }


                /**
                 * Remove Attributes from Virtual DOMS
                 */

                dropWrapper.find('.ep-virtual-area .bdt-navbar-nav').removeAttr('class');
                dropWrapper.find('.ep-virtual-area .menu-item').removeAttr('data-width-type');
                dropWrapper.find('.ep-virtual-area .menu-item').removeAttr('data-content-width');
                dropWrapper.find('.ep-virtual-area .menu-item').removeAttr('data-content-pos');
                dropWrapper.find('.ep-virtual-area .menu-item-has-children').addClass('ep-has-megamenu');
                dropWrapper.find('.ep-virtual-area .ep-megamenu-panel').removeClass().addClass('bdt-accordion-content');
                dropWrapper.find('.ep-virtual-area .bdt-accordion-content').removeAttr('style');


                $(this).find('.details').removeClass("hidden");
                $($container).find(".sub-menu-toggle").remove();


                if ($(dropWrapper).find('.bdt-accrodion-title-megamenu').length === 0) {
                    dropWrapper.find('.ep-virtual-area .ep-menu-nav-link').wrap("<span class='bdt-accordion-title bdt-accrodion-title-megamenu'></span>");
                    dropWrapper.find('.ep-virtual-area .ep-menu-nav-link').attr("onclick", "event.stopPropagation();");
                    dropWrapper.find('.ep-virtual-area .bdt-megamenu-indicator').remove();
                    $('<i class="bdt-megamenu-indicator ep-icon-arrow-down-3"></i>').appendTo(dropWrapper.find('.ep-has-megamenu .bdt-accordion-title'));
                }


                /**
                 *  Mobile toggler
                 */
                var $toggler = $container.find('.bdt-navbar-toggle');
                var $toggleContent = dropWrapper.find('.ep-virtual-area');
                bdtUIkit.drop($toggleContent, {
                    offset: (this.settings("offset_mobile.size") !== '') ? this.settings('offset_mobile.size') : '5',
                    // offset:5,
                    toggle: $toggler,
                    animation: (this.settings("animation_type") !== undefined) ? this.settings('animation_type') : 'fade',
                    duration: (this.settings("animation_duration") !== undefined) ? this.settings('animation_duration') : '200',
                    mode: 'click',
                });


                //ACCORDION
                bdtUIkit.accordion('#ep-megamenu-' + widgetID + '-virtual', {
                    'offset': 10
                });

            }
        });


        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-mega-menu.default',
            function ($scope) {
                elementorFrontend.elementsHandler.addHandler(MegaMenu, {
                    $element: $scope
                });
            }
        );
    });

})(jQuery, window.elementorFrontend);
/**
 * Start twitter carousel widget script
 */

(function ($, elementor) {

	'use strict';

	var widgetBrandCarousel = function ($scope, $) {

		var $brandCarousel = $scope.find('.bdt-ep-brand-carousel');

		if (!$brandCarousel.length) {
			return;
		}

		var $brandCarouselContainer = $brandCarousel.find('.swiper-carousel'),
			$settings = $brandCarousel.data('settings');

		const Swiper = elementorFrontend.utils.swiper;
		initSwiper();
		async function initSwiper() {
			var swiper = await new Swiper($brandCarouselContainer, $settings); // this is an example
			if ($settings.pauseOnHover) {
				$($brandCarouselContainer).hover(function () {
					(this).swiper.autoplay.stop();
				}, function () {
					(this).swiper.autoplay.start();
				});
			}
		};
	};


	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-brand-carousel.default', widgetBrandCarousel);
	});

}(jQuery, window.elementorFrontend));

/**
 * End twitter carousel widget script
 */


/**
 * Start advanced divider widget script
 */

(function($, elementor) {
    'use strict'; 
    var widgetBusinessHours = function($scope, $) {
        var $businessHoursContainer = $scope.find('.bdt-ep-business-hours'),
        $businessHours = $businessHoursContainer.find('.bdt-ep-business-hours-current-time');
        if (!$businessHoursContainer.length) {
            return;
        }
        var $settings = $businessHoursContainer.data('settings');
        var dynamic_timezone = $settings.dynamic_timezone;
        var timeNotation = $settings.timeNotation;
        var business_hour_style = $settings.business_hour_style;

        if (business_hour_style != 'dynamic') return;

        $(document).ready(function() {
            var offset_val;
            var timeFormat = '%H:%M:%S', timeZoneFormat; 
            var dynamic_timezone = $settings.dynamic_timezone;
            
            if(business_hour_style == 'static'){
                offset_val = $settings.dynamic_timezone_default;
            }else{
                offset_val = dynamic_timezone;
            }

            // console.log(offset_val);
            if(timeNotation == '12h'){
                timeFormat = '%I:%M:%S %p';
            } 
            if (offset_val == '') return;
            var options = {
                // format:'<span class=\"dt\">%A, %d %B %I:%M:%S %P</span>',
                //    format:'<span class=\"dt\">  %I:%M:%S </span>',
                format: timeFormat,
                timeNotation: timeNotation, //'24h',
                am_pm: true,
                utc: true,
                utc_offset: offset_val
            }
            $($businessHoursContainer).find('.bdt-ep-business-hours-current-time').jclock(options);

        });

    };
    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-business-hours.default', widgetBusinessHours);
    });
}(jQuery, window.elementorFrontend));

/**
 * End business hours widget script
 */


(function ($, elementor) {

    'use strict';

    var widgetCarousel = function ($scope, $) {

        var $carousel = $scope.find('.bdt-ep-carousel');

        if (!$carousel.length) {
            return;
        }

        var $carouselContainer = $carousel.find('.swiper-carousel'),
            $settings = $carousel.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        async function initSwiper() {
            var swiper = await new Swiper($carouselContainer, $settings);

            if ($settings.pauseOnHover) {
                $($carouselContainer).hover(function () {
                    (this).swiper.autoplay.stop();
                }, function () {
                    (this).swiper.autoplay.start();
                });
            }

        };

    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-carousel.default', widgetCarousel);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-carousel.bdt-alice', widgetCarousel);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-carousel.bdt-vertical', widgetCarousel);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-carousel.bdt-ramble', widgetCarousel);
    });

}(jQuery, window.elementorFrontend));
/**
 * Start chart widget script
 */

(function ($, elementor) {

  'use strict';

  var widgetChart = function ($scope, $) {

    var $chart = $scope.find('.bdt-chart'),
      $chart_canvas = $chart.find('> canvas'),
      settings = $chart.data('settings'),
      suffixprefix = $chart.data('suffixprefix');

    if (!$chart.length) {
      return;
    }

    elementorFrontend.waypoint($chart_canvas, function () {
      var $this = $(this),
        ctx = $this[0].getContext('2d'),
        myChart = null;

      if (myChart != null) {
        myChart.destroy();
      }

      myChart = new Chart(ctx, settings);


      var thouSeparator = settings.valueSeparator,
        separatorSymbol = settings.separatorSymbol,
        xAxesSeparator = settings.xAxesSeparator,
        yAxesSeparator = settings.yAxesSeparator;
      var _k_formatter = (settings.kFormatter == 'yes') ? true : false;


      /**
       * start update
       * s_p_status = s = suffix, p = prefix
       */


      if (settings.type == 'pie' || settings.type == 'doughnut') {
        return;
      }

      var
        s_p_status = (typeof suffixprefix.suffix_prefix_status !== 'undefined') ? suffixprefix.suffix_prefix_status : 'no',

        x_prefix = (typeof suffixprefix.x_custom_prefix !== 'undefined') ? suffixprefix.x_custom_prefix : '',
        x_suffix = (typeof suffixprefix.x_custom_suffix !== 'undefined') ? suffixprefix.x_custom_suffix : '',

        y_suffix = (typeof suffixprefix.y_custom_suffix !== 'undefined') ? suffixprefix.y_custom_suffix : '',
        y_prefix = (typeof suffixprefix.y_custom_prefix !== 'undefined') ? suffixprefix.y_custom_prefix : '';


      function addCommas(nStr, separatorSymbol, _k_formatter) {
        nStr += '';
        var x = nStr.split('.');
        var x1 = x[0];
        var x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
          x1 = x1.replace(rgx, '$1' + separatorSymbol + '$2');
        }

        if (_k_formatter == true) {
          if (nStr >= 1000000000) {
            return (nStr / 1000000000).toFixed(1).replace(/\.0$/, '') + 'G';
          }
          if (nStr >= 1000000) {
            return (nStr / 1000000).toFixed(1).replace(/\.0$/, '') + 'M';
          }
          if (nStr >= 1000) {
            return (nStr / 1000).toFixed(1).replace(/\.0$/, '') + 'K';
          }
          return nStr;
        } else {
          return x1 + x2;
        }
      }


      function updateChartSetting(chart, thouSeparator = 'no', separatorSymbol = ',') {

        // chart.options.scales.x.ticks = {
        //   callback: function (value, index, ticks) {

        //     if (s_p_status == 'yes' && thouSeparator == 'yes' && xAxesSeparator == 'yes') {
        //       return x_prefix + addCommas(value, separatorSymbol, _k_formatter) + x_suffix;
        //     } else if (s_p_status == 'no' && thouSeparator == 'yes' && xAxesSeparator == 'yes') {
        //       return addCommas(value, separatorSymbol, _k_formatter);
        //     } else {
        //       return x_prefix + value + x_suffix;
        //     }

        //   }
        // }

        if (suffixprefix.type == 'horizontalBar') {
          chart.options.scales.x.ticks = {
            callback: function (value, index) {

              if (s_p_status == 'yes' && thouSeparator == 'yes' && yAxesSeparator == 'yes') {
                return y_prefix + addCommas(value, separatorSymbol, _k_formatter) + y_suffix;
              } else if (s_p_status == 'no' && thouSeparator == 'yes' && yAxesSeparator == 'yes') {
                return addCommas(value, separatorSymbol, _k_formatter);
              } else {
                return y_prefix + value + y_suffix;
              }
            }
          }
        } else if (suffixprefix.type == 'bar' || suffixprefix.type == 'line' || suffixprefix.type == 'bubble') {
          chart.options.scales.y.ticks = {
            callback: function (value, index) {

              if (s_p_status == 'yes' && thouSeparator == 'yes' && yAxesSeparator == 'yes') {
                return y_prefix + addCommas(value, separatorSymbol, _k_formatter) + y_suffix;
              } else if (s_p_status == 'no' && thouSeparator == 'yes' && yAxesSeparator == 'yes') {
                return addCommas(value, separatorSymbol, _k_formatter);
              } else {
                return y_prefix + value + y_suffix;
              }
            }
          }
        }

        chart.update();
      }
      if (s_p_status == 'yes' && thouSeparator == 'no') {
        updateChartSetting(myChart);
      } else if (s_p_status == 'yes' && thouSeparator == 'yes') {
        updateChartSetting(myChart, thouSeparator, separatorSymbol);
      } else if (s_p_status == 'no' && thouSeparator == 'yes') {
        updateChartSetting(myChart, thouSeparator, separatorSymbol);
      } else {

      }
      // end update

    }, {
      offset: 'bottom-in-view'
    });

  };

  jQuery(window).on('elementor/frontend/init', function () {
    elementorFrontend.hooks.addAction('frontend/element_ready/bdt-chart.default', widgetChart);
  });

}(jQuery, window.elementorFrontend));

/**
 * End chart widget script
 */
/**
 * Start circle info widget script
 */

// this is the main function, here impvaring all html into js DOM as a
// parameter. 
function circleJs(id, circleMoving, movingTime, mouseEvent) {
    var circles = document.querySelectorAll('#' + id + ' .bdt-ep-circle-info-sub-circle');
    var circleContents = document.querySelectorAll('#' + id + '  .bdt-ep-circle-info-item');
    var parent = document.querySelector('#' + id + ' .bdt-ep-circle-info-inner ');

    var i = 2;
    var prevNowPlaying = null;

    if (movingTime <= 0) {
        movingTime = '100000000000';
    }

    if (circleMoving === false) {
        movingTime = '100000000000';
    }

    function myTimer() {
        //console.log('setInterval');
        var dataTab = jQuery(' #' + id + ' .bdt-ep-circle-info-sub-circle.active').data('circle-index');
        var totalSubCircle = jQuery('#' + id + ' .bdt-ep-circle-info-sub-circle').length; // here

        if (dataTab > totalSubCircle || i > totalSubCircle) {
            dataTab = 1;
            i = 1;
        }

        jQuery('#' + id + '  .bdt-ep-circle-info-sub-circle').removeClass('active');
        jQuery('#' + id + ' .bdt-ep-circle-info-sub-circle.active').removeClass('active', this);
        jQuery('#' + id + '  ' + '[data-circle-index=\'' + i + '\']').addClass('active');
        jQuery('#' + id + '  .bdt-ep-circle-info-item').removeClass('active');
        jQuery('#' + id + '  .icci' + i).addClass('active');
        i++;
        var activeIcon = '#' + id + ' .bdt-ep-circle-info-sub-circle i,' + '#' + id + ' .bdt-ep-circle-info-sub-circle svg';
        jQuery(activeIcon).css({
            'transform': 'rotate(' + (360 - (i - 2) * 36) + 'deg)',
            'transition': '2s'
        });
        jQuery('#' + id + ' .bdt-ep-circle-info-inner').css({
            'transform': 'rotate(' + ((i - 2) * 36) + 'deg) ',
            'transition': '1s'
        });

    }
    if (circleMoving === true) {
        var prevNowPlaying = setInterval(myTimer, movingTime);
    }
    if (circleMoving === false) {
        clearInterval(prevNowPlaying);
    }


    // active class toggle methods
    var removeClasses = function removeClasses(nodes, value) {
        var nodes = nodes;
        var value = value;
        if (nodes) return nodes.forEach(function (node) {
            return node.classList.contains(value) && node.classList.remove(value);
        });
        else return false;
    };
    var addClass = function addClass(nodes, index, value) {
        var nodes = nodes;
        var index = index;
        var value = value;
        return nodes ? nodes[index].classList.add(value) : 0;
    };
    var App = {
        initServicesCircle: function initServicesCircle() {
            // info circle
            if (parent) {
                var spreadCircles = function spreadCircles() {
                    // spread the sub-circles around the circle
                    var parent = document.querySelector('#' + id + ' .bdt-ep-circle-info-inner ').getBoundingClientRect();
                    var centerX = 0;
                    var centerY = 0;
                    Array.from(circles).reverse().forEach(function (circle, index) {
                        var circle = circle;
                        var index = index;
                        var angle = index * (360 / circles.length);
                        var x = centerX + (parent.width / 2) * Math.cos((angle * Math.PI) / 180);
                        var y = centerY + (parent.height / 2) * Math.sin((angle * Math.PI) / 180);
                        circle.style.transform = 'translate3d(' + parseFloat(x).toFixed(5) + 'px,' + parseFloat(y).toFixed(5) + 'px,0)';
                    });
                };

                spreadCircles();

                var resizeTimer = void 0;
                window.addEventListener('resize', function () {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(function () {
                        spreadCircles();
                    }, 50);
                });
                circles.forEach(function (circle, index) {
                    var circle = circle;
                    var index = index;
                    var circlesToggleFnc = function circlesToggleFnc() {
                        this.index = circle.dataset.circleIndex;
                        if (!circle.classList.contains('active')) {
                            removeClasses(circles, 'active');
                            removeClasses(circleContents, 'active');
                            addClass(circles, index, 'active');
                            addClass(circleContents, index, 'active');
                        }
                    };
                    if (mouseEvent === 'mouseover') {
                        circle.addEventListener('mouseover', circlesToggleFnc, true);
                    } else if (mouseEvent === 'click') {
                        circle.addEventListener('click', circlesToggleFnc, true);
                    } else {
                        circle.addEventListener('mouseover', circlesToggleFnc, true);
                    }
                });
            }
        }
    };
    App.initServicesCircle();
}

(function ($, elementor) {
    'use strict';
    var widgetCircleInfo = function ($scope, $) {
        var $circleInfo = $scope.find('.bdt-ep-circle-info');

        if (!$circleInfo.length) {
            return;
        }

        elementorFrontend.waypoint($circleInfo, function () {
            var $this = jQuery(this);
            var $settings = $this.data('settings');

            circleJs($settings.id, $settings.circleMoving, $settings.movingTime, $settings.mouseEvent);

        }, {
            // offset: 'bottom-in-view'
            offset: '80%'
        });

    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-circle-info.default', widgetCircleInfo);
    });
}(jQuery, window.elementorFrontend));

/**
 * End circle info widget script
 */
/**
 * Start circle menu widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetCircleMenu = function( $scope, $ ) {

		var $circleMenu = $scope.find('.bdt-circle-menu'),
            $settings = $circleMenu.data('settings');

        if ( ! $circleMenu.length ) {
            return;
        }

        $($circleMenu[0]).circleMenu({
            direction           : $settings.direction,
            item_diameter       : $settings.item_diameter,
            circle_radius       : $settings.circle_radius,
            speed               : $settings.speed,
            delay               : $settings.delay,
            step_out            : $settings.step_out,
            step_in             : $settings.step_in,
            trigger             : $settings.trigger,
            transition_function : $settings.transition_function
        });

        var $tooltip = $circleMenu.find('.bdt-tippy-tooltip'),
            widgetID = $scope.data('id');

        $tooltip.each(function (index) {
            tippy(this, {
                //appendTo: $scope[0]
                //arrow: false,
                allowHTML: true,
                theme: 'bdt-tippy-' + widgetID
            });
        });

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-circle-menu.default', widgetCircleMenu );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End circle menu widget script
 */


/**
 * Start comment widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetComment = function( $scope, $ ) {

		var $comment = $scope.find( '.bdt-comment-container' ),
            $settings = $comment.data('settings');
            
        if ( ! $comment.length ) {
            return;
        }

        if ($settings.layout === 'disqus') {

            var disqus_config = function () {
            this.page.url = $settings.permalink;  // Replace PAGE_URL with your page's canonical URL variable
            this.page.identifier = $comment; // Replace PAGE_IDENTIFIER with your page's unique identifier variable
            };
            
            (function() { // DON'T EDIT BELOW THIS LINE
            var d = document, s = d.createElement('script');
            s.src = '//' + $settings.username + '.disqus.com/embed.js';
            s.setAttribute('data-timestamp', +new Date());
            (d.head || d.body).appendChild(s);
            })();

        } else if ($settings.layout === 'facebook') {
            
            //var $fb_script = document.getElementById("facebook-jssdk");

            //console.log($fb_script);

            // if($fb_script){
            // 	$($fb_script).remove();
            // } else {
            // }

            // jQuery.ajax({
            // 	url: 'https://connect.facebook.net/en_US/sdk.js',
            // 	dataType: 'script',
            // 	cache: true,
            // 	success: function() {
            // 		FB.init( {
            // 			appId: config.app_id,
            // 			version: 'v2.10',
            // 			xfbml: false
            // 		} );
            // 		config.isLoaded = true;
            // 		config.isLoading = false;
            // 		jQuery( document ).trigger( 'fb:sdk:loaded' );
            // 	}
            // });
            // 
            // 
            (function(d, s, id){
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) {return;}
                js = d.createElement(s); js.id = id;
                js.src = 'https://connect.facebook.net/en_US/sdk.js';
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
                

            window.fbAsyncInit = function() {
                FB.init({
                    appId            : $settings.app_id,
                    autoLogAppEvents : true,
                    xfbml            : true,
                    version          : 'v3.2'
                });
            };

        }

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-comment.default', widgetComment );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End comment widget script
 */


(function ($, elementor) {

    'use strict';

    $(window).on('elementor/frontend/init', function ($) {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            Confetti;

        Confetti = ModuleHandler.extend({

            bindEvents: function () {
                this.run();
            },

            getDefaultSettings: function () {
                return {
                    resize: true,
                    useWorker: true,
                };
            },

            onElementChange: debounce(function (prop) {
                if (prop.indexOf('ep_widget_cf_') !== -1) {
                    //  this.instance.reset();
                    this.run();

                }
            }, 400),

            settings: function (key) {
                return this.getElementSettings('ep_widget_cf_' + key);
            },
            randomInRange: function (min, max) {
                return Math.random() * (max - min) + min;
            },
            run: function () {
                var options = this.getDefaultSettings(),
                    $element = this.$element;

                if (this.settings('confetti') !== 'yes') {
                    return;
                }

                if (this.settings('z_index')) {
                    options.zIndex = this.settings('z_index');
                }
                if (this.settings('particle_count.size')) {
                    options.particleCount = this.settings('particle_count.size') || 100;
                }
                if (this.settings('start_velocity.size')) {
                    options.startVelocity = this.settings('start_velocity.size') || 45;
                }

                if (this.settings('spread.size')) {
                    options.spread = this.settings('spread.size') || 70;
                }
                if (this.settings('colors')) {
                    var colors = this.settings('colors');
                    options.colors = colors.split(',');
                }
                if (this.settings('shapes')) {
                    var shapes = this.settings('shapes');
                    options.shapes = shapes.split(/,|\|/);
                }

                if ('emoji' == this.settings('shape_type') && this.settings('shapes_emoji')) {
                    var shapes = this.settings('shapes_emoji');
                    let __shapes = [];

                    let shapesArray = shapes.split(/,|\|/);
                    shapesArray.forEach(function (shape, i) {
                        __shapes[i] = confetti.shapeFromText({
                            text: shape,
                        });
                    });
                    options.shapes = __shapes;
                }

                if ('svg' == this.settings('shape_type') && this.settings('shapes_svg')) {
                    let shapes = this.settings('shapes_svg');
                    let __path = [];
                    let shapesArray = shapes.split('|');

                    shapesArray.forEach(function (shape, i) {
                        __path[i] = confetti.shapeFromPath({
                            path: shape,
                            matrix: [0.03597122302158273, 0, 0, 0.03597122302158273, -4.856115107913669, -5.071942446043165]
                        });
                    });

                    options.shapes = __path;
                }


                if (this.settings('scalar.size')) {
                    options.scalar = this.settings('scalar.size') || 1;
                }

                if (this.settings('origin')) {
                    if (this.settings('origin_x.size') || this.settings('origin_y.size')) {
                        options.origin = {
                            x: this.settings('origin_x.size') || 0.5,
                            y: this.settings('origin_y.size') || 0.6
                        }
                    }
                }

                if (this.settings('angle.size')) {
                    options.angle = this.settings('angle.size') || 90;
                }

                var this_instance = this;
                var instanceConfetti = {
                    executeConfetti: function () {
                        if (this_instance.settings('type') == 'random') {
                            options.angle = this_instance.randomInRange(55, this_instance.settings('angle.size') || 90);
                            options.spread = this_instance.randomInRange(50, this_instance.settings('spread.size') || 70);
                            options.particleCount = this_instance.randomInRange(55, this_instance.settings('particle_count.size') || 100);
                        }
                        if (this_instance.settings('type') == 'fireworks') {
                            var duration = this_instance.settings('fireworks_duration.size') || 1500;
                            var animationEnd = Date.now() + duration;
                            var defaults = {
                                startVelocity: this_instance.settings('start_velocity.size') || 30,
                                spread: this_instance.settings('spread.size') || 360,
                                shapes: this_instance.settings('shapes') ? shapes.split(',') : ['circle', 'circle', 'square'],
                                ticks: 60,
                                zIndex: this_instance.settings('z_index') || 0
                            };

                            var interval = setInterval(function () {
                                var timeLeft = animationEnd - Date.now();

                                if (timeLeft <= 0) {
                                    return clearInterval(interval);
                                }

                                var particleCount = 50 * (timeLeft / duration);
                                // since particles fall down, start a bit higher than random
                                confetti(Object.assign({}, defaults, {
                                    particleCount,
                                    origin: {
                                        x: this_instance.randomInRange(0.1, 0.3),
                                        y: Math.random() - 0.2
                                    }
                                }));
                                confetti(Object.assign({}, defaults, {
                                    particleCount,
                                    origin: {
                                        x: this_instance.randomInRange(0.7, 0.9),
                                        y: Math.random() - 0.2
                                    }
                                }));
                            }, 250);
                        }

                        if (this_instance.settings('type') == 'school-pride') {
                            var duration = this_instance.settings('fireworks_duration.size') || 1500;
                            var end = Date.now() + (duration);

                            (function frame() {
                                confetti({
                                    particleCount: this_instance.settings('particle_count.size') || 2,
                                    angle: this_instance.settings('angle.size') || 60,
                                    spread: this_instance.settings('spread.size') || 55,
                                    shapes: this_instance.settings('shapes') ? shapes.split(',') : ['circle', 'circle', 'square'],
                                    origin: {
                                        x: 0
                                    },
                                    colors: colors.split(',')
                                });
                                confetti({
                                    particleCount: this_instance.settings('particle_count.size') || 2,
                                    angle: (this_instance.settings('angle.size') || 60) * 2, //120
                                    spread: this_instance.settings('spread.size') || 55,
                                    shapes: this_instance.settings('shapes') ? shapes.split(',') : ['circle', 'circle', 'square'],
                                    origin: {
                                        x: 1
                                    },
                                    colors: colors.split(',')
                                });

                                if (Date.now() < end) {
                                    requestAnimationFrame(frame);
                                }
                            }());
                        }

                        if (this_instance.settings('type') == 'snow') {
                            var duration = this_instance.settings('fireworks_duration.size') || 1500;
                            /**
                             * Infinite Animation Time
                             * Yes & Not in Editor
                             */
                            if ('yes' == this_instance.settings('anim_infinite') && false == Boolean(elementorFrontend.isEditMode())) {
                                duration = 24 * 60 * 60 * 1000;
                            }
                            var animationEnd = Date.now() + duration;
                            var skew = 1;

                            (function frame() {
                                var timeLeft = animationEnd - Date.now();
                                var ticks = Math.max(200, 500 * (timeLeft / duration));
                                skew = Math.max(0.8, skew - 0.001);

                                confetti({
                                    particleCount: this_instance.settings('particle_count.size') || 1,
                                    startVelocity: this_instance.settings('start_velocity.size') || 0,
                                    ticks: ticks,
                                    origin: {
                                        x: Math.random(),
                                        // since particles fall down, skew start toward the top
                                        y: (Math.random() * skew) - 0.2
                                    },
                                    colors: colors.split(','),
                                    shapes: this_instance.settings('shapes') ? shapes.split(',') : ['circle'],
                                    gravity: this_instance.randomInRange(0.4, 0.6),
                                    scalar: this_instance.randomInRange(0.4, 1),
                                    drift: this_instance.randomInRange(-0.4, 0.4)
                                });

                                if (timeLeft > 0) {
                                    requestAnimationFrame(frame);
                                }
                            }());

                            setInterval(function () {
                                // instanceConfetti.executeConfetti();
                            }, 5000);
                        }

                        if ((this_instance.settings('type') == 'basic') ||
                            (this_instance.settings('type') == 'random')) {
                            this_instance.instance = confetti(options);

                        }
                    }
                };

                if (this.settings('confetti') == 'yes') {

                    if ((this.settings('trigger_type') == 'click')) {
                        jQuery(this.settings('trigger_selector')).on('click', function () {
                            instanceConfetti.executeConfetti();
                            //  $(this).unbind('mouseenter mouseleave');
                        });
                    } else if (this.settings('trigger_type') == 'mouseenter') {
                        jQuery(this.settings('trigger_selector')).on('mouseenter', function () {
                            instanceConfetti.executeConfetti();
                            //  $(this).unbind('mouseenter mouseleave');
                        });
                    } else if (this.settings('trigger_type') == 'ajax-success') {
                        jQuery(document).ajaxComplete(function (event, jqxhr, settings) {
                            instanceConfetti.executeConfetti();
                        });
                    } else if (this.settings('trigger_type') == 'delay') {
                        setTimeout(function () {
                            instanceConfetti.executeConfetti();
                        }, this.settings('trigger_delay.size') ? this.settings('trigger_delay.size') : 1000);
                    } else if (this.settings('trigger_type') == 'onview') {
                        elementorFrontend.waypoint($element, function () {
                            instanceConfetti.executeConfetti();
                        }, {
                            // offset: 'bottom-in-view',
                            offset: '80%'
                        });
                    } else {
                        instanceConfetti.executeConfetti();
                    }

                }
            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(Confetti, {
                $element: $scope
            });
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/section', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(Confetti, {
                $element: $scope
            });
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/container', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(Confetti, {
                $element: $scope
            });
        });

    });

}(jQuery, window.elementorFrontend));
/**
 * Start contact form widget script
 */

(function ($, elementor) {

    'use strict';

    var widgetSimpleContactForm = function ($scope, $) {

        var $contactForm = $scope.find('.bdt-contact-form .without-recaptcha');

        if (!$contactForm.length) {
            return;
        }

        $contactForm.submit(function (e) {
            sendContactForm($contactForm);
            return false;
        });

        return false;

    };

    function sendContactForm($contactForm) {
        var langStr = window.ElementPackConfig.contact_form;

        $.ajax({
            url: $contactForm.attr('action'),
            type: 'POST',
            data: $contactForm.serialize(),
            beforeSend: function () {
                bdtUIkit.notification({
                    message: '<div bdt-spinner></div> ' + langStr.sending_msg,
                    timeout: false,
                    status: 'primary'
                });
            },
            success: function (data) {
                var redirectURL = $(data).data('redirect'),
                    isExternal = $(data).data('external'),
                    resetStatus = $(data).data('resetstatus');

                bdtUIkit.notification.closeAll();
                var notification = bdtUIkit.notification({
                    message: data
                });
                
                if (redirectURL){
                    if (redirectURL != 'no') {
                        bdtUIkit.util.on(document, 'close', function (evt) {
                            if (evt.detail[0] === notification) {
                                window.open(redirectURL, isExternal);
                            }
                        });
                    }
                }
                
                localStorage.setItem("bdtCouponCode", $contactForm.attr('id'));

                if (resetStatus) {
                    if (resetStatus !== 'no') {
                    $contactForm[0].reset();
                }
                }

                // $contactForm[0].reset();
            }
        });
        return false;
    }

    // google invisible captcha
    function elementPackGIC() {

        var langStr = window.ElementPackConfig.contact_form;

        return new Promise(function (resolve, reject) {

            if (grecaptcha === undefined) {
                bdtUIkit.notification({
                    message: '<div bdt-spinner></div> ' + langStr.captcha_nd,
                    timeout: false,
                    status: 'warning'
                });
                reject();
            }

            var response = grecaptcha.getResponse();

            if (!response) {
                bdtUIkit.notification({
                    message: '<div bdt-spinner></div> ' + langStr.captcha_nr,
                    timeout: false,
                    status: 'warning'
                });
                reject();
            }

            var $contactForm = $('textarea.g-recaptcha-response').filter(function () {
                return $(this).val() === response;
            }).closest('form.bdt-contact-form-form');

            var contactFormAction = $contactForm.attr('action');

            if (contactFormAction && contactFormAction !== '') {
                sendContactForm($contactForm);
            } else {
                // console.log($contactForm);
            }

            grecaptcha.reset();

        }); //end promise

    }

    //Contact form recaptcha callback, if needed
    window.elementPackGICCB = elementPackGIC;

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-contact-form.default', widgetSimpleContactForm);
    });


}(jQuery, window.elementorFrontend));

/**
 * End contact form widget script
 */
/**
 * Start cookie consent widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetCookieConsent = function( $scope, $ ) {

		var $cookieConsent = $scope.find('.bdt-cookie-consent'),
            $settings      = $cookieConsent.data('settings'),
            editMode       = Boolean( elementorFrontend.isEditMode() );
        
        if ( ! $cookieConsent.length || editMode ) {
            return;
        }

        window.cookieconsent.initialise($settings);

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-cookie-consent.default', widgetCookieConsent );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End cookie consent widget script
 */


/**
 * Start countdown widget script
 */
 
(function ($, elementor) {
    'use strict';
    var widgetCountdown = function ($scope, $) {
        var $countdown = $scope.find('.bdt-countdown-wrapper');
        if (!$countdown.length) {
            return;
        }
        var $settings = $countdown.data('settings'),
            endTime = $settings.endTime,
            loopHours = $settings.loopHours,
            isLogged = $settings.isLogged;

           
 
        var countDownObj = {
            setCookie: function (name, value, hours) {
                var expires = "";
                if (hours) {
                    var date = new Date();
                    date.setTime(date.getTime() + (hours * 60 * 60 * 1000));
                    expires = "; expires=" + date.toUTCString();
                }
                document.cookie = name + "=" + (value || "") + expires + "; path=/";
            },
            getCookie: function (name) {
                var nameEQ = name + "=";
                var ca = document.cookie.split(';');
                for (var i = 0; i < ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
                }
                return null;
            },
            randomIntFromInterval: function (min, max) { // min and max included 
                return Math.floor(Math.random() * (max - min + 1) + min)
            },
            getTimeSpan: function (date) {
                var total = date - Date.now();

                return {
                    total,
                    seconds: total / 1000 % 60,
                    minutes: total / 1000 / 60 % 60,
                    hours: total / 1000 / 60 / 60 % 24,
                    days: total / 1000 / 60 / 60 / 24
                };
            },
            showPost: function (endTime) {
                jQuery.ajax({
                    url: $settings.adminAjaxUrl,
                    type: 'post',
                    data: {
                        action: 'element_pack_countdown_end',
                        endTime: endTime,
                        couponTrickyId: $settings.couponTrickyId
                    },
                    success: function (data) {
                        if (data == 'ended') {
                            if ($settings.endActionType == 'message') {
                                jQuery($settings.msgId).css({
                                    'display': 'block'
                                });
                                jQuery($settings.id + '-timer').css({
                                    'display': 'none'
                                });
                            }
                            if ($settings.endActionType == 'url') {
                                setInterval(function () {
                                    jQuery(location).attr('href', $settings.redirectUrl);
                                }, $settings.redirectDelay);
                            }
                        } 
                    },
                    error: function () {
                        //error handling
                        console.log("Error");
                    }
                });
            },
            couponCode: function(){
                jQuery.ajax({
                    url: $settings.adminAjaxUrl,
                    type: 'post',
                    data: {
                        action: 'element_pack_countdown_end',
                        endTime: endTime,
                        couponTrickyId: $settings.couponTrickyId
                    },
                    success: function (data) {
                    },
                    error: function () {
                        //error handling
                        //console.log("Error");
                    }
                });
            },
            triggerFire : function(){
                jQuery.ajax({
                    url: $settings.adminAjaxUrl,
                    type: 'post',
                    data: {
                        action: 'element_pack_countdown_end',
                        endTime: endTime,
                        couponTrickyId: $settings.couponTrickyId
                    },
                    success: function (data) {
                         if (data == 'ended') {
                             setTimeout(function () {
                                if ($settings.triggerId){
                                    document.getElementById($settings.triggerId).click();
                                    
                                }
                                // document.getElementById($settings.triggerId).click();
                                //  jQuery('#' + $settings.triggerId).trigger('click');
                             }, 1500);
                         }
                    },
                    error: function () {
                        //console.log("Error");
                    }
                });
            },
            clearInterVal: function (myInterVal) {
                clearInterval(myInterVal);
            }

        };


        if (loopHours == false) {
            var countdown = bdtUIkit.countdown($($settings.id + '-timer'), {
                date: $settings.finalTime
            });

            var myInterVal = setInterval(function () {
                var seconds = countDownObj.getTimeSpan(countdown.date).seconds.toFixed(0);
                var finalSeconds = parseInt(seconds);
                if (finalSeconds < 0) {
                    if (!jQuery('body').hasClass('elementor-editor-active')) {
                        jQuery($settings.id + '-msg').css({
                            'display': 'none'
                        });
                        if ($settings.endActionType != 'none') {
                            countDownObj.showPost(endTime)
                        };
                    }
                    countDownObj.clearInterVal(myInterVal);
                }
            }, 1000);
            
            // for coupon code
            if ($settings.endActionType == 'coupon-code') {
                var myInterVal2 = setInterval(function () {
                    var seconds = countDownObj.getTimeSpan(countdown.date).seconds.toFixed(0);
                    var finalSeconds = parseInt(seconds);
                    if (finalSeconds < 0) {
                        if (!jQuery('body').hasClass('elementor-editor-active')) {
                            if ($settings.endActionType == 'coupon-code') {
                                countDownObj.couponCode(endTime)
                            };
                        }
                        countDownObj.clearInterVal(myInterVal2);
                    }
                }, 1000);
            }
            // custom trigger on the end

            if ($settings.triggerId !== false) {
                var myInterVal2 = setInterval(function () {
                    var seconds = countDownObj.getTimeSpan(countdown.date).seconds.toFixed(0);
                    var finalSeconds = parseInt(seconds);
                    if (finalSeconds < 0) {
                        if (!jQuery('body').hasClass('elementor-editor-active')) {
                                countDownObj.triggerFire();
                        }
                        countDownObj.clearInterVal(myInterVal2);
                    }
                }, 1000);
            }
 
        }


        if (loopHours !== false) {
            var now = new Date(),
                randMinute = countDownObj.randomIntFromInterval(6, 14),
                hours = loopHours * 60 * 60 * 1000 - (randMinute * 60 * 1000),
                timer = new Date(now.getTime() + hours),
                loopTime = timer.toISOString(),
                getCookieLoopTime = countDownObj.getCookie('bdtCountdownLoopTime');


            if ((getCookieLoopTime == null || getCookieLoopTime == 'undefined') && isLogged === false) {
                countDownObj.setCookie('bdtCountdownLoopTime', loopTime, loopHours);
            }

            var setLoopTimer;

            if (isLogged === false) {
                setLoopTimer = countDownObj.getCookie('bdtCountdownLoopTime');
            } else {
                setLoopTimer = loopTime;
            }

            $($settings.id + '-timer').attr('data-bdt-countdown', 'date: ' + setLoopTimer);
            var countdown = bdtUIkit.countdown($($settings.id + '-timer'), {
                date: setLoopTimer
            });

            var countdownDate = countdown.date;

            setInterval(function () {
                var seconds = countDownObj.getTimeSpan(countdownDate).seconds.toFixed(0);
                var finalSeconds = parseInt(seconds);
                // console.log(finalSeconds);
                if (finalSeconds > 0) {
                    if ((getCookieLoopTime == null || getCookieLoopTime == 'undefined') && isLogged === false) {
                        countDownObj.setCookie('bdtCountdownLoopTime', loopTime, loopHours);
                        bdtUIkit.countdown($($settings.id + '-timer'), {
                            date: setLoopTimer
                        });
                    }
                }

            }, 1000);


        }


    };
    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-countdown.default', widgetCountdown);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-countdown.bdt-tiny-countdown', widgetCountdown);
    });
}(jQuery, window.elementorFrontend));

/**
 * End countdown widget script
 */
/**
 * Start coupon reveal widget script
 */
(function ($, elementor) {
    'use strict';
    var widgetCoupon = function ($scope, $) {
        var $widgetContainer = $scope.find('.bdt-coupon-code'),
            editMode = Boolean(elementorFrontend.isEditMode()),
            $couponExecuted = false;
        if (!$widgetContainer.length) {
            return;
        }
        var $settings = $widgetContainer.data('settings'),
            triggerURL = $settings.triggerURL;

        if ($settings.triggerByAction != true) {
            var clipboard = new ClipboardJS($settings.couponMsgId, {
                target: function (trigger) {
                    // $trigger.nextElementSibling.addClass('bdt-coupon-showing');
                    return trigger.nextElementSibling;
                }
            });

            clipboard.on('success', function (event) {
                $(event.trigger).addClass('active');

                event.clearSelection();
                setTimeout(function () {
                    $(event.trigger).removeClass('active');
                    // $($settings.couponId).removeClass('bdt-coupon-showing');
                }, 3000);
            });
        }

        if (($settings.couponLayout == 'style-2') && ($settings.triggerByAction == true)) {
            var clipboard = new ClipboardJS($settings.couponId, {
                target: function (trigger) {
                    return trigger;
                }
            });

            clipboard.on('success', function (event) {
                $widgetContainer.find($settings.couponId).addClass('active');
                event.clearSelection();
                setTimeout(function () {
                    $widgetContainer.find($settings.couponId).removeClass('active');
                }, 2000);
            });

            //   attention
            $widgetContainer.on('click', function () {
                if (!$widgetContainer.hasClass('active') && ($settings.triggerAttention != false)) {
                    var $triggerSelector = $settings.triggerInputId;
                    $('[name="' + $triggerSelector.substring(1) + '"]').closest('form').addClass('ep-shake-animation-cc');
                    setTimeout(function () {
                        $('[name="' + $triggerSelector.substring(1) + '"]').closest('form').removeClass('ep-shake-animation-cc');
                    }, 5000);
                }

            });
        }

        var couponObj = {
            decodeCoupon: function (data) {
                jQuery.ajax({
                    url: $settings.adminAjaxURL,
                    type: 'post',
                    data: {
                        action: 'element_pack_coupon_code',
                        coupon_code: data
                    },
                    success: function (couponCode) {
                        $($settings.couponId).find('.bdt-coupon-code-text').html(couponCode);
                    },
                    error: function () {
                        $($settings.couponId).html('Something wrong, please contact support team.');
                    }
                });
            },
            displayCoupon: function ($widgetContainer) {
                $widgetContainer.addClass('active');

            },
            triggerURL: function (triggerURL) {
                var target = (true !== $settings.is_external) ? '_self' : '_blank';
                var redirectWindow = window.open(triggerURL, target);

                if (triggerURL) {
                    // Url contains a #
                    if (target == '_self' && triggerURL.indexOf('#') !== -1) {
                        var hash = triggerURL.split('#')[1];
                        if (hash) {
                            // console.log(hash);
                            $('html, body').animate({
                                scrollTop: $('#' + hash).offset().top - 100
                            }, 1500);
                            // return;
                        }
                    }
                    redirectWindow.location;
                }
                return false;
            },
            formSubmitted: function () {
                this.displayCoupon($widgetContainer);
                if (triggerURL !== false) {
                    this.triggerURL(triggerURL);
                }
                this.decodeCoupon($settings.couponCode);
                $couponExecuted = true;
            }
        };


        $widgetContainer.on('click', function () {
            if (!$widgetContainer.hasClass('active') && ($settings.triggerByAction !== true)) {
                couponObj.displayCoupon($widgetContainer);
                if (triggerURL !== false) {
                    setTimeout(function () {
                        couponObj.triggerURL(triggerURL);
                    }, 2000);
                }
            }
        });

        if (!editMode) {
            var triggerInput = $settings.triggerInputId;
            $(document).ajaxComplete(function (event, jqxhr, settings) {
                if (!$couponExecuted) {
                    if ((triggerInput !== false) && ($settings.triggerByAction === true)) {
                        var str = settings.data;
                        // console.log(str);
                        if (str.toLowerCase().indexOf(triggerInput.substring(1)) >= 0) {
                            couponObj.formSubmitted();
                        }
                    } else {
                        if ($settings.triggerByAction === true) {
                            couponObj.formSubmitted();
                        }
                    }
                }

            });

        }

    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-coupon-code.default', widgetCoupon);
    });

}(jQuery, window.elementorFrontend));

/**
 * End coupon reveal widget script
 */
; (function ($, elementor) {
$(window).on('elementor/frontend/init', function () {
    let ModuleHandler = elementorModules.frontend.handlers.Base,
        CursorEffect;

    CursorEffect = ModuleHandler.extend({
        bindEvents: function () {
            this.run();
        },
        getDefaultSettings: function () {
            return {

            };
        },
        onElementChange: debounce(function (prop) {
            if (prop.indexOf('element_pack_cursor_effects_') !== -1) {
                this.run();
            }
        }, 400),

        settings: function (key) {
            return this.getElementSettings('element_pack_cursor_effects_' + key);
        },

        run: function () {
            if (this.settings('show') !== 'yes') {
                return;
            }
            var options = this.getDefaultSettings(),
                widgetID = this.$element.data('id'),
                widgetContainer = '.elementor-element-' + widgetID,
                $element = this.$element,
                cursorStyle = this.settings('style');
            const checkClass = $(widgetContainer).find(".bdt-cursor-effects");
            var source = this.settings('source');
            if ($(checkClass).length < 1) {
                if (source === 'image') {
                    var image = this.settings('image_src.url');
                    $(widgetContainer).append('<div class="bdt-cursor-effects"><div id="bdt-ep-cursor-ball-effects-' + widgetID + '" class="ep-cursor-ball"><img class="bdt-cursor-image"src="' + image + '"></div></div>');
                }
                else if (source === 'icons') {
                    var svg = this.settings('icons.value.url');
                    var icons = this.settings('icons.value');
                    if (svg !== undefined) {
                        $(widgetContainer).append('<div class="bdt-cursor-effects"><div id="bdt-ep-cursor-ball-effects-' + widgetID + '" class="ep-cursor-ball"><img class="bdt-cursor-image" src="' + svg + '"></img></div></div>');
                    } else {
                        $(widgetContainer).append('<div class="bdt-cursor-effects"><div id="bdt-ep-cursor-ball-effects-' + widgetID + '" class="ep-cursor-ball"><i class="' + icons + ' bdt-cursor-icons"></i></div></div>');
                    }
                }
                else if (source === 'text') {
                    var text = this.settings('text_label');
                    $(widgetContainer).append('<div class="bdt-cursor-effects"><div id="bdt-ep-cursor-ball-effects-' + widgetID + '" class="ep-cursor-ball"><span class="bdt-cursor-text">' + text + '</span></div></div>');
                }
                else {
                    $(widgetContainer).append('<div class="bdt-cursor-effects ' + cursorStyle + '"><div id="bdt-ep-cursor-ball-effects-' + widgetID + '" class="ep-cursor-ball"></div><div id="bdt-ep-cursor-circle-effects-' + widgetID + '"  class="ep-cursor-circle"></div></div>');
                }
            }
            const cursorBallID = '#bdt-ep-cursor-ball-effects-' + this.$element.data('id');
            const cursorBall = document.querySelector(cursorBallID);
            options.models = widgetContainer + ' .elementor-widget-container';
            options.speed = 1;
            options.centerMouse = true;
            new Cotton(cursorBall, options);


            if (source === 'default') {
                const cursorCircleID = '#bdt-ep-cursor-circle-effects-' + this.$element.data('id');
                const cursorCircle = document.querySelector(cursorCircleID);
                options.models = widgetContainer + ' .elementor-widget-container';
                options.speed = this.settings('speed') ? this.settings('speed.size') : 0.725;
                options.centerMouse = true;
                new Cotton(cursorCircle, options);
            }

        }
    });

    elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
        elementorFrontend.elementsHandler.addHandler(CursorEffect, {
            $element: $scope
        });
    });
});
})(jQuery, window.elementorFrontend);

/**
 * Start custom carousel widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetCustomCarousel = function( $scope, $ ) {

		var $carousel = $scope.find( '.bdt-ep-custom-carousel' );
				
        if ( ! $carousel.length ) {
            return;
        }

        var $carouselContainer = $carousel.find('.swiper-carousel'),
			$settings 		 = $carousel.data('settings');

		const Swiper = elementorFrontend.utils.swiper;
		initSwiper();
		async function initSwiper() {
			var swiper = await new Swiper($carouselContainer, $settings);

			if ($settings.pauseOnHover) {
				$($carouselContainer).hover(function () {
					(this).swiper.autoplay.stop();
				}, function () {
					(this).swiper.autoplay.start();
				});
			}
		};

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-custom-carousel.default', widgetCustomCarousel );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-custom-carousel.bdt-custom-content', widgetCustomCarousel );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End custom carousel widget script
 */


/**
 * Start bdt custom gallery widget script
 */

(function($, elementor) {

    'use strict';

    var widgetCustomGallery = function($scope, $) {

        var $customGallery = $scope.find('.bdt-custom-gallery'),
            $settings 	= $customGallery.data('settings');
          
        if (!$customGallery.length) {
            return;
        }

        if ($settings.tiltShow == true) {
            var elements = document.querySelectorAll($settings.id + " [data-tilt]");
            VanillaTilt.init(elements);
        }

    };

    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-custom-gallery.default', widgetCustomGallery);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-custom-gallery.bdt-abetis', widgetCustomGallery);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-custom-gallery.bdt-fedara', widgetCustomGallery);
    });

}(jQuery, window.elementorFrontend));

/**
 * End bdt custom gallery widget script
 */


(function ($, elementor) {

    'use strict';

    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            widgetDarkMode;

        widgetDarkMode = ModuleHandler.extend({

            bindEvents: function () {
                this.run();
            },

            getDefaultSettings: function () {
                return {
                    left            : 'unset',
                    time            : '.5s',
                    mixColor        : '#fff',
                    backgroundColor : '#fff',
                    saveInCookies   : false,
                    label           : '🌓',
                    autoMatchOsTheme: false
                };
            },


            onElementChange: debounce(function (prop) {
                // if (prop.indexOf('time.size') !== -1) {
                this.run();
                // }
            }, 400),

            settings: function (key) {
                return this.getElementSettings(key);
            },

            setCookie: function (name, value, days) {
                var expires = "";
                if ( days ) {
                    var date = new Date();
                    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                    expires = "; expires=" + date.toUTCString();
                }
                document.cookie = name + "=" + (value || "") + expires + "; path=/";
            },
            getCookie: function (name) {
                var nameEQ = name + "=";
                var ca     = document.cookie.split(';');
                for ( var i = 0; i < ca.length; i++ ) {
                    var c = ca[i];
                    while ( c.charAt(0) == ' ' ) c = c.substring(1, c.length);
                    if ( c.indexOf(nameEQ) == 0 ) return c.substring(nameEQ.length, c.length);
                }
                return null;
            },

            eraseCookie: function (name) {
                document.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
            },


            run: function () {
                var options = this.getDefaultSettings(),
                    element = this.findElement('.elementor-widget-container').get(0);

                var autoMatchOsTheme = (this.settings('autoMatchOsTheme') === 'yes'
                    && this.settings('autoMatchOsTheme') !== 'undefined');

                var saveInCookies = (this.settings('saveInCookies') === 'yes'
                    && this.settings('saveInCookies') !== 'undefined');

                options.left             = 'unset';
                options.time             = this.settings('time.size') / 1000 + 's';
                options.mixColor         = this.settings('mix_color');
                options.backgroundColor  = this.settings('default_background');
                options.saveInCookies    = saveInCookies;
                options.label            = '🌓';
                options.autoMatchOsTheme = autoMatchOsTheme;

                $('body').removeClass(function (index, css) {
                    return (css.match(/\bbdt-dark-mode-\S+/g) || []).join(' '); // removes anything that starts with "page-"
                });
                $('body').addClass('bdt-dark-mode-position-' + this.settings('toggle_position'));

                $(this.settings('ignore_element')).addClass('darkmode-ignore');

                if ( options.mixColor ) {

                    $('.darkmode-toggle, .darkmode-layer, .darkmode-background').remove();

                    var darkmode = new Darkmode(options);
                    darkmode.showWidget();

                    if ( this.settings('default_mode') === 'dark' ) {
                        darkmode.toggle();
                        $('body').addClass('darkmode--activated');
                        $('.darkmode-layer').addClass('darkmode-layer--simple darkmode-layer--expanded');
                        // console.log(darkmode.isActivated()) // will return true
                    } else {
                        $('body').removeClass('darkmode--activated');
                        $('.darkmode-layer').removeClass('darkmode-layer--simple darkmode-layer--expanded');
                        // console.log(darkmode.isActivated()) // will return true
                    }

                    var global_this = this,
                        editMode    = $('body').hasClass('elementor-editor-active');

                    if ( editMode === false && saveInCookies === true ) {
                        $('.darkmode-toggle').on('click', function () {
                            if ( darkmode.isActivated() === true ) {
                                global_this.eraseCookie('bdtDarkModeUserAction');
                                global_this.setCookie('bdtDarkModeUserAction', 'dark', 10);
                            } else if ( darkmode.isActivated() === false ) {
                                global_this.eraseCookie('bdtDarkModeUserAction');
                                global_this.setCookie('bdtDarkModeUserAction', 'light', 10);
                            } else {

                            }
                        });

                        var userCookie = this.getCookie('bdtDarkModeUserAction')

                        if ( userCookie !== null && userCookie !== 'undefined' ) {
                            if ( userCookie === 'dark' ) {
                                darkmode.toggle();
                                $('body').addClass('darkmode--activated');
                                $('.darkmode-layer').addClass('darkmode-layer--simple darkmode-layer--expanded');
                            } else {
                                $('body').removeClass('darkmode--activated');
                                $('.darkmode-layer').removeClass('darkmode-layer--simple darkmode-layer--expanded');
                            }

                        }
                    }

                }


            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-dark-mode.default', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(widgetDarkMode, { $element: $scope });

        });
    });


}(jQuery, window.elementorFrontend));

/**
 * End Dark Mode widget script
 */
(function ($, elementor) {

    'use strict';

    var widgetCarousel = function ($scope, $) {

        var $carousel = $scope.find('.bdt-dynamic-carousel');
        if (!$carousel.length) {
            return;
        }

        var $carouselContainer = $carousel.find('.swiper-carousel'),
            $settings = $carousel.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        async function initSwiper() {
            var swiper = await new Swiper($carouselContainer, $settings);

            if ($settings.pauseOnHover) {
                $($carouselContainer).hover(function () {
                    (this).swiper.autoplay.stop();
                }, function () {
                    (this).swiper.autoplay.start();
                });
            }

        };

    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-dynamic-carousel.default', widgetCarousel);

    });

}(jQuery, window.elementorFrontend));
;
(function ($, elementor) {
    'use strict';
    $(window).on('elementor/frontend/init', function () {

        var ModuleHandler = elementorModules.frontend.handlers.Base,
            BackgroundExpand;

        BackgroundExpand = ModuleHandler.extend({

            bindEvents: function () {
                this.run();
            },

            getDefaultSettings: function () {
                return {
                    direction: 'alternate',
                };
            },

            settings: function (key) {
                return this.getElementSettings('ep_bg_expand_' + key);
            },

            onElementChange: debounce(function (prop) {
                if (prop.indexOf('ep_bg_expand_') !== -1) {
                    this.run();
                }
            }, 400),

            run: function () {
                var options = this.getDefaultSettings(),
                    element = this.$element.get(0);

                if ('yes' !== this.settings('enable')) {
                    return;
                }

                if (this.settings('selector')) {
                    element = this.settings('selector');
                }

                function initClass(e) {
                    $(element).addClass(e);
                }

                function terminateClass(e) {
                    $(element).removeClass(e);
                }

                var tl = gsap.timeline({
                    scrollTrigger: {
                        // markers      : true,
                        trigger      : $(element),
                        start        : "top center",
                        end          : '100% bottom',
                        toggleActions: "restart none none reverse",
                        onEnter      : () => initClass("bdt-bx-active"),
                        onEnterBack  : () => terminateClass("bdt-bx-active"),
                    }
                });

            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/section', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(BackgroundExpand, {
                $element: $scope
            });
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/container', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(BackgroundExpand, {
                $element: $scope
            });
        });

    });
}(jQuery, window.elementorFrontend));
/**
 * Start webhook form widget script
 */

(function ($, elementor) {
    "use strict";
    var widgetWebhookForm = function ($scope, $) {
        var $formWrapper = $scope.find(".bdt-ep-webhook-form"),
            $settings = $formWrapper.data("settings");

        if (!$formWrapper.length) {
            return;
        }

        $($settings.id)
            .find(".bdt-ep-webhook-form-form")
            .submit(function (e) {
                e.preventDefault();
                var formData = $(this).serialize();
                formData = formData + "&action=submit_webhook_form";
                formData = formData + "&nonce=" + ElementPackConfig.nonce;

                $.ajax({
                    url: ElementPackConfig.ajaxurl,
                    type: "post",
                    data: formData,
                    beforeSend: function () {
                        bdtUIkit.notification({
                            message: "<div bdt-spinner></div> Sending...",
                            timeout: false,
                            status: "primary",
                        });
                    },
                    success: function (res) {
                        let response = JSON.parse(res);
                        bdtUIkit.notification.closeAll();

                        if (true == response.success) {
                            bdtUIkit.notification({
                                message:
                                    '<div bdt-icon="icon: check"></div> ' + response.message,
                            });
                        } else {
                            bdtUIkit.notification({
                                message:
                                    '<div bdt-icon="icon: close"></div> ' + response.message,
                            });
                        }
                    },
                });
            });
    };

    jQuery(window).on("elementor/frontend/init", function () {
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/bdt-webhook-form.default",
            widgetWebhookForm
        );
    });
})(jQuery, window.elementorFrontend);

/**
 * End webhook form widget script
 */

(function ($, elementor) {

    'use strict';

    $(window).on('elementor/frontend/init', function ($) {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            ScrollFillEffect;

        ScrollFillEffect = ModuleHandler.extend({

            bindEvents: function () {
                this.run();
            },
            onElementChange: debounce(function (prop) {
                if (prop.indexOf('ep_widget_sf_fx_') !== -1) {
                    this.run();
                }
            }, 400),
            settings: function (key) {
                return this.getElementSettings('ep_widget_sf_fx_' + key);
            },
            run: function () {
                var $element = this.$element;

                if (this.settings('enable') !== 'yes') {
                    return;
                }

                elementorFrontend.waypoint($element, function () {
                    var $selector = jQuery($element).find('.elementor-heading-title, .bdt-heading-tag span, .bdt-ep-advanced-heading-main-title-inner');
                    gsap.to($selector, {
                        scrollTrigger: {
                            trigger: $selector,
                            start: "bottom center+=50%",
                            end: "bottom center",
                            scrub: true,
                        },
                        backgroundSize: '100% 200%',
                    });
                }, {
                    offset: 'bottom-in-view'
                });
               
            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/heading.default', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(ScrollFillEffect, {
                $element: $scope
            });
        });
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-animated-heading.default', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(ScrollFillEffect, {
                $element: $scope
            });
        });
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-advanced-heading.default', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(ScrollFillEffect, {
                $element: $scope
            });
        });
    });

}(jQuery, window.elementorFrontend));
/**
 * Start EDD Category carousel widget script
 */

(function ($, elementor) {
  "use strict";

  var widgetProductReviewCarousel = function ($scope, $) {
    var $ProductReviewCarousel = $scope.find(".ep-edd-product-review-carousel");

    if (!$ProductReviewCarousel.length) {
      return;
    }

    var $ProductReviewCarouselContainer = $ProductReviewCarousel.find(".swiper-carousel"),
      $settings = $ProductReviewCarousel.data("settings");

    const Swiper = elementorFrontend.utils.swiper;
    initSwiper();
    async function initSwiper() {
      var swiper = await new Swiper($ProductReviewCarouselContainer, $settings); // this is an example
      if ($settings.pauseOnHover) {
        $($ProductReviewCarouselContainer).hover(
          function () {
            this.swiper.autoplay.stop();
          },
          function () {
            this.swiper.autoplay.start();
          }
        );
      }
    }
  };

  jQuery(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/bdt-edd-product-review-carousel.default",
      widgetProductReviewCarousel
    );
  });
})(jQuery, window.elementorFrontend);

/**
 * End twitter carousel widget script
 */

/**
 * Start EDD Category carousel widget script
 */

(function ($, elementor) {
  "use strict";

  var widgetEddCategoryCarousel = function ($scope, $) {
    var $eddCategoryCarousel = $scope.find(".bdt-edd-category-carousel");

    if (!$eddCategoryCarousel.length) {
      return;
    }

    var $eddCategoryCarouselContainer = $eddCategoryCarousel.find(".swiper-carousel"),
      $settings = $eddCategoryCarousel.data("settings");

    const Swiper = elementorFrontend.utils.swiper;
    initSwiper();
    async function initSwiper() {
      var swiper = await new Swiper($eddCategoryCarouselContainer, $settings); // this is an example
      if ($settings.pauseOnHover) {
        $($eddCategoryCarouselContainer).hover(
          function () {
            this.swiper.autoplay.stop();
          },
          function () {
            this.swiper.autoplay.start();
          }
        );
      }
    }
  };

  jQuery(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/bdt-edd-category-carousel.default",
      widgetEddCategoryCarousel
    );
  });
})(jQuery, window.elementorFrontend);

/**
 * End twitter carousel widget script
 */

/**
 * Start EDD product carousel widget script
 */

(function ($, elementor) {
  "use strict";

  var widgetEddProductCarousel = function ($scope, $) {
    var $eddProductCarousel = $scope.find(".bdt-edd-product-carousel");

    if (!$eddProductCarousel.length) {
      return;
    }

    var $eddProductCarouselContainer = $eddProductCarousel.find(".swiper-carousel"),
      $settings = $eddProductCarousel.data("settings");

    const Swiper = elementorFrontend.utils.swiper;
    initSwiper();
    async function initSwiper() {
      var swiper = await new Swiper($eddProductCarouselContainer, $settings); // this is an example
      if ($settings.pauseOnHover) {
        $($eddProductCarouselContainer).hover(
          function () {
            this.swiper.autoplay.stop();
          },
          function () {
            this.swiper.autoplay.start();
          }
        );
      }
    }
  };

  jQuery(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/bdt-edd-product-carousel.default",
      widgetEddProductCarousel
    );
  });
})(jQuery, window.elementorFrontend);

/**
 * End twitter carousel widget script
 */

/**
 * Start EDD tabs widget script
 */

(function ($, elementor) {
    'use strict';
    var widgetTabs = function ($scope, $) {
        var $tabsArea = $scope.find('.bdt-tabs-area'),
            $tabs = $tabsArea.find('.bdt-tabs'),
            $tab = $tabs.find('.bdt-tab');
        if (!$tabsArea.length) {
            return;
        }
        var $settings = $tabs.data('settings');
        var animTime = $settings.hashScrollspyTime;
        var customOffset = $settings.hashTopOffset;
        var navStickyOffset = $settings.navStickyOffset;
        if (navStickyOffset == 'undefined') {
            navStickyOffset = 10;
        }

        $scope.find('.bdt-template-modal-iframe-edit-link').each(function () {
            var modal = $($(this).data('modal-element'));
            $(this).on('click', function (event) {
                bdtUIkit.modal(modal).show();
            });
            modal.on('beforehide', function () {
                window.parent.location.reload();
            });
        });


        function hashHandler($tabs, $tab, animTime, customOffset) {
            // debugger;
            if (window.location.hash) {
                if ($($tabs).find('[data-title="' + window.location.hash.substring(1) + '"]').length) {
                    var hashTarget = $('[data-title="' + window.location.hash.substring(1) + '"]').closest($tabs).attr('id');
                    $('html, body').animate({
                        easing: 'slow',
                        scrollTop: $('#' + hashTarget).offset().top - customOffset
                    }, animTime, function () {
                        //#code
                    }).promise().then(function () {
                        bdtUIkit.tab($tab).show($('[data-title="' + window.location.hash.substring(1) + '"]').data('tab-index'));
                    });
                }
            }
        }
        if ($settings.activeHash == 'yes' && $settings.status != 'bdt-sticky-custom') {
            $(window).on('load', function () {
                hashHandler($tabs, $tab, animTime, customOffset);
            });
            $($tabs).find('.bdt-tabs-item-title').off('click').on('click', function (event) {
                window.location.hash = ($.trim($(this).attr('data-title')));
            });
            $(window).on('hashchange', function (e) {
                hashHandler($tabs, $tab, animTime, customOffset);
            });
        }
        //# code for sticky and also for sticky with hash
        function stickyHachChange($tabs, $tab, navStickyOffset) {
            if ($($tabs).find('[data-title="' + window.location.hash.substring(1) + '"]').length) {
                var hashTarget = $('[data-title="' + window.location.hash.substring(1) + '"]').closest($tabs).attr('id');
                $('html, body').animate({
                    easing: 'slow',
                    scrollTop: $('#' + hashTarget).offset().top - navStickyOffset
                }, 1000, function () {
                    //#code
                }).promise().then(function () {
                    bdtUIkit.tab($tab).show($($tab).find('[data-title="' + window.location.hash.substring(1) + '"]').data('tab-index'));
                });
            }
        }
        if ($settings.status == 'bdt-sticky-custom') {
            $($tabs).find('.bdt-tabs-item-title').bind().click('click', function (event) {
                if ($settings.activeHash == 'yes') {
                    window.location.hash = ($.trim($(this).attr('data-title')));
                } else {
                    $('html, body').animate({
                        easing: 'slow',
                        scrollTop: $($tabs).offset().top - navStickyOffset
                    }, 500, function () {
                        //#code
                    });
                }
            });
            // # actived Hash#
            if ($settings.activeHash == 'yes' && $settings.status == 'bdt-sticky-custom') {
                $(window).on('load', function () {
                    if (window.location.hash) {
                        stickyHachChange($tabs, $tab, navStickyOffset);
                    }
                });
                $(window).on('hashchange', function (e) {
                    stickyHachChange($tabs, $tab, navStickyOffset);
                });
            }
        }

        // start linkWidget
        var editMode = Boolean(elementorFrontend.isEditMode());
        var $linkWidget = $settings['linkWidgetSettings'],
            $activeItem = ($settings['activeItem']) - 1;
        if ($linkWidget !== undefined && editMode === false) {

            $linkWidget.forEach(function (entry, index) {

                if (index == 0) {
                    $('#bdt-tab-content-'+$settings['linkWidgetId']).parent().remove();
                    $(entry).parent().wrapInner('<div class="bdt-switcher-wrapper" />');
                    $(entry).parent().wrapInner('<div id="bdt-tab-content-'+$settings['linkWidgetId']+'" class="bdt-switcher bdt-switcher-item-content" />');

                    if($settings['activeItem'] == undefined){
                        $(entry).addClass('bdt-active');
                    }
                }

                if($settings['activeItem'] !== undefined && index == $activeItem){
                    $(entry).addClass('bdt-active');
                }

                $(entry).attr('data-content-id', "tab-" + (index + 1));

            });

        }
        // end linkWidget


    };
    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-edd-tabs.default', widgetTabs);
    });
}(jQuery, window.elementorFrontend));

/**
 * End tabs widget script
 */



/**
 * Start event calendar widget script
 */

(function($, elementor) {

    'use strict';

    var widgetEventCarousel = function($scope, $) {
        var $eventCarousel = $scope.find('.bdt-event-calendar');

        if (!$eventCarousel.length) {
            return;
        }

        var $eventCarouselContainer = $eventCarousel.find('.swiper-carousel'),
            $settings = $eventCarousel.data('settings');
        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        async function initSwiper() {
            var swiper = await new Swiper($eventCarouselContainer, $settings);

            if ($settings.pauseOnHover) {
                $($eventCarouselContainer).hover(function () {
                    (this).swiper.autoplay.stop();
                }, function () {
                    (this).swiper.autoplay.start();
                });
            }
        };

    };


    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-event-carousel.default', widgetEventCarousel);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-event-carousel.fable', widgetEventCarousel);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-event-carousel.altra', widgetEventCarousel);
    });

}(jQuery, window.elementorFrontend));

/**
 * End event calendar widget script
 */


/**
 * Start fancy slider widget script
 */

(function ($, elementor) {

    'use strict';

    var widgetFancySlider = function ($scope, $) {

        var $slider = $scope.find('.bdt-ep-fancy-slider');

        if (!$slider.length) {
            return;
        }

        var $sliderContainer = $slider.find('.swiper-carousel'),
            $settings = $slider.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        async function initSwiper() {
            var swiper = await new Swiper($sliderContainer, $settings);

            if ($settings.pauseOnHover) {
                $($sliderContainer).hover(function () {
                    (this).swiper.autoplay.stop();
                }, function () {
                    (this).swiper.autoplay.start();
                });
            }
        };
    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-fancy-slider.default', widgetFancySlider);
    });

}(jQuery, window.elementorFrontend));

/**
 * End fancy slider widget script
 */
/**
 * Start fancy tabs widget script
 */

(function($, elementor) {

    'use strict';

    var widgetFancyTabs = function($scope, $) {


        var $fancyTabs = $scope.find('.bdt-ep-fancy-tabs'),
            $settings = $fancyTabs.data('settings');

        var iconBx = document.querySelectorAll('#' + $settings.tabs_id + ' .bdt-ep-fancy-tabs-item');
        var contentBx = document.querySelectorAll('#' + $settings.tabs_id + ' .bdt-ep-fancy-tabs-content');

        for (var i = 0; i < iconBx.length; i++) {
            iconBx[i].addEventListener($settings.mouse_event, function() {
                for (var i = 0; i < contentBx.length; i++) {
                    contentBx[i].className = 'bdt-ep-fancy-tabs-content';
                }
                document.getElementById(this.dataset.id).className = 'bdt-ep-fancy-tabs-content active';

                for (var i = 0; i < iconBx.length; i++) {
                    iconBx[i].className = 'bdt-ep-fancy-tabs-item';
                }
                this.className = 'bdt-ep-fancy-tabs-item active';

            });
        }

    };


    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-fancy-tabs.default', widgetFancyTabs);
    });

}(jQuery, window.elementorFrontend));

/**
 * End fancy tabs widget script
 */


/**
 * Start faq widget script
 */

(function($, elementor) {
    'use strict';
    var widgetPostGallery = function($scope, $) {
        var $faqWrapper = $scope.find('.bdt-faq-wrapper'),
            $faqFilter = $faqWrapper.find('.bdt-ep-grid-filters-wrapper');
        if (!$faqFilter.length) {
            return;
        }
        var $settings = $faqFilter.data('hash-settings');
        var activeHash = $settings.activeHash;
        var hashTopOffset = $settings.hashTopOffset;
        var hashScrollspyTime = $settings.hashScrollspyTime;

        function hashHandler($faqFilter, hashScrollspyTime, hashTopOffset) {
            if (window.location.hash) {
                if ($($faqFilter).find('[bdt-filter-control="[data-filter*=\'bdtf-' + window.location.hash.substring(1) + '\']"]').length) {
                    var hashTarget = $('[bdt-filter-control="[data-filter*=\'bdtf-' + window.location.hash.substring(1) + '\']"]').closest($faqFilter).attr('id');
                    $('html, body').animate({
                        easing: 'slow',
                        scrollTop: $('#' + hashTarget).offset().top - hashTopOffset
                    }, hashScrollspyTime, function() {
                        //#code
                    }).promise().then(function() {
                        $('[bdt-filter-control="[data-filter*=\'bdtf-' + window.location.hash.substring(1) + '\']"]').trigger("click");
                    });
                }
            }
        }

        if ($settings.activeHash == 'yes') {
            $(window).on('load', function() {
                hashHandler($faqFilter, hashScrollspyTime = 1500, hashTopOffset);
            });
            $($faqFilter).find('.bdt-ep-grid-filter').off('click').on('click', function(event) {
                window.location.hash = ($.trim($(this).context.innerText.toLowerCase())).replace(/\s+/g, '-');
                // hashHandler( $faqFilter, hashScrollspyTime, hashTopOffset);
            });
            $(window).on('hashchange', function(e) {
                hashHandler($faqFilter, hashScrollspyTime, hashTopOffset);
            });
        }
    };
    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-faq.default', widgetPostGallery);
    });
}(jQuery, window.elementorFrontend));

/**
 * End faq widget script
 */


/**
 * Start Flip Box widget script
 */

(function ($, elementor) {
    'use strict';
    var widgetFlipBox = function ($scope, $) {
        var $flipBox = $scope.find('.bdt-flip-box'),
            $settings = $flipBox.data('settings');
        if (!$flipBox.length) {
            return;
        }

        if ('click' === $settings.flipTrigger) {
            $($flipBox).on('click', function () {
                $(this).toggleClass('bdt-active');
            });
        }
        if ('hover' === $settings.flipTrigger) {
            $($flipBox).on('mouseenter', function () {
                $(this).addClass('bdt-active');
            });
            $($flipBox).on('mouseleave', function () {
                $(this).removeClass('bdt-active');
            });
        }


    };
    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-flip-box.default', widgetFlipBox);
    });
}(jQuery, window.elementorFrontend));

/**
 * End Flip Box widget script
 */
;(function ($, elementor) {
    'use strict';
    $(window).on('elementor/frontend/init', function () {

        var ModuleHandler = elementorModules.frontend.handlers.Base, FloatingEffect;

        FloatingEffect = ModuleHandler.extend({

            bindEvents: function () {
                this.run();
            },

            getDefaultSettings: function () {
                return {
                    direction: 'alternate',
                    easing   : 'easeInOutSine',
                    loop     : true
                };
            },

            settings: function (key) {
                return this.getElementSettings('ep_floating_effects_' + key);
            },

            onElementChange: debounce(function (prop) {
                if ( prop.indexOf('ep_floating') !== -1 ) {
                    this.anime && this.anime.restart();
                    this.run();
                }
            }, 400),

            run: function () {
                var options = this.getDefaultSettings(),
                    element = this.findElement('.elementor-widget-container').get(0);

                if ( this.settings('translate_toggle') ) {
                    if ( this.settings('translate_x.sizes.from').length !== 0 || this.settings('translate_x.sizes.to').length !== 0 ) {
                        options.translateX = {
                            value   : [this.settings('translate_x.sizes.from') || 0, this.settings('translate_x.sizes.to') || 0],
                            duration: this.settings('translate_duration.size'),
                            delay   : this.settings('translate_delay.size') || 0
                        };
                    }
                    // console.log(options);

                    if ( this.settings('translate_y.sizes.from').length !== 0 || this.settings('translate_y.sizes.to').length !== 0 ) {
                        options.translateY = {
                            value   : [this.settings('translate_y.sizes.from') || 0, this.settings('translate_y.sizes.to') || 0],
                            duration: this.settings('translate_duration.size'),
                            delay   : this.settings('translate_delay.size') || 0
                        };
                    }
                }

                if ( this.settings('rotate_toggle') ) {
                    if ( this.settings('rotate_infinite') !== 'yes' ) {
                        if ( this.settings('rotate_x.sizes.from').length !== 0 || this.settings('rotate_x.sizes.to').length !== 0 ) {
                            options.rotateX = {
                                value   : [this.settings('rotate_x.sizes.from') || 0, this.settings('rotate_x.sizes.to') || 0],
                                duration: this.settings('rotate_duration.size'),
                                delay   : this.settings('rotate_delay.size') || 0
                            };
                        }
                        if ( this.settings('rotate_y.sizes.from').length !== 0 || this.settings('rotate_y.sizes.to').length !== 0 ) {
                            options.rotateY = {
                                value   : [this.settings('rotate_y.sizes.from') || 0, this.settings('rotate_y.sizes.to') || 0],
                                duration: this.settings('rotate_duration.size'),
                                delay   : this.settings('rotate_delay.size') || 0
                            };
                        }
                        if ( this.settings('rotate_z.sizes.from').length !== 0 || this.settings('rotate_z.sizes.to').length !== 0 ) {
                            options.rotateZ = {
                                value   : [this.settings('rotate_z.sizes.from') || 0, this.settings('rotate_z.sizes.to') || 0],
                                duration: this.settings('rotate_duration.size'),
                                delay   : this.settings('rotate_delay.size') || 0
                            };
                        }
                    }
                }

                if ( this.settings('scale_toggle') ) {
                    if ( this.settings('scale_x.sizes.from').length !== 0 || this.settings('scale_x.sizes.to').length !== 0 ) {
                        options.scaleX = {
                            value   : [this.settings('scale_x.sizes.from') || 0, this.settings('scale_x.sizes.to') || 0],
                            duration: this.settings('scale_duration.size'),
                            delay   : this.settings('scale_delay.size') || 0
                        };
                    }
                    if ( this.settings('scale_y.sizes.from').length !== 0 || this.settings('scale_y.sizes.to').length !== 0 ) {
                        options.scaleY = {
                            value   : [this.settings('scale_y.sizes.from') || 0, this.settings('scale_y.sizes.to') || 0],
                            duration: this.settings('scale_duration.size'),
                            delay   : this.settings('scale_delay.size') || 0
                        };
                    }
                }

                if ( this.settings('skew_toggle') ) {
                    if ( this.settings('skew_x.sizes.from').length !== 0 || this.settings('skew_x.sizes.to').length !== 0 ) {
                        options.skewX = {
                            value   : [this.settings('skew_x.sizes.from') || 0, this.settings('skew_x.sizes.to') || 0],
                            duration: this.settings('skew_duration.size'),
                            delay   : this.settings('skew_delay.size') || 0
                        };
                    }
                    if ( this.settings('skew_y.sizes.from').length !== 0 || this.settings('skew_y.sizes.to').length !== 0 ) {
                        options.skewY = {
                            value   : [this.settings('skew_y.sizes.from') || 0, this.settings('skew_y.sizes.to') || 0],
                            duration: this.settings('skew_duration.size'),
                            delay   : this.settings('skew_delay.size') || 0
                        };
                    }
                }

                if ( this.settings('border_radius_toggle') ) {
                    jQuery(element).css('overflow', 'hidden');
                    if ( this.settings('border_radius.sizes.from').length !== 0 || this.settings('border_radius.sizes.to').length !== 0 ) {
                        options.borderRadius = {
                            value   : [this.settings('border_radius.sizes.from') || 0, this.settings('border_radius.sizes.to') || 0],
                            duration: this.settings('border_radius_duration.size'),
                            delay   : this.settings('border_radius_delay.size') || 0
                        };
                    }
                }

                if ( this.settings('opacity_toggle') ) {
                    if ( this.settings('opacity_start.size').length !== 0 || this.settings('opacity_end.size').length !== 0 ) {
                        options.opacity = {
                            value   : [this.settings('opacity_start.size') || 1, this.settings('opacity_end.size') || 0],
                            duration: this.settings('opacity_duration.size'),
                            easing  : 'linear'
                        };
                    }
                }

                if ( this.settings('easing') ) {
                    options.easing = this.settings('easing');
                }


                if ( this.settings('show') ) {
                    options.targets = element;
                    if (
                        this.settings('translate_toggle') ||
                        this.settings('rotate_toggle') ||
                        this.settings('scale_toggle') ||
                        this.settings('skew_toggle') ||
                        this.settings('border_radius_toggle') ||
                        this.settings('opacity_toggle')
                    ) {
                        this.anime = window.anime && window.anime(options);
                    }
                }

            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(FloatingEffect, {
                $element: $scope
            });
        });
    });
}(jQuery, window.elementorFrontend));
/**
 * Start helpdesk widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetHelpDesk = function( $scope, $ ) {

		var $helpdesk = $scope.find( '.bdt-helpdesk' ),
            $helpdeskTooltip = $helpdesk.find('.bdt-helpdesk-icons');

        if ( ! $helpdesk.length ) {
            return;
        }
		
		var $tooltip = $helpdeskTooltip.find('> .bdt-tippy-tooltip'),
			widgetID = $scope.data('id');
		
		$tooltip.each( function( index ) {
			tippy( this, {
				allowHTML: true,
				theme: 'bdt-tippy-' + widgetID
			});				
		});

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-helpdesk.default', widgetHelpDesk );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End helpdesk widget script
 */
 
/**
 * Start honeycombs widget script
 */

(function($, elementor) {
    'use strict';
    var widgetHoneycombs = function($scope, $) {
        var $honeycombsArea = $scope.find('.bdt-honeycombs-area'),
        $honeycombs = $honeycombsArea.find('.bdt-honeycombs');
        if (!$honeycombsArea.length) {
            return;
        }
        var $settings = $honeycombs.data('settings');

        $($honeycombs).honeycombs({
            combWidth: $settings.width,
            margin: $settings.margin,
            threshold: 3,
            widthTablet: $settings.width_tablet,
            widthMobile : $settings.width_mobile,
            viewportLg : $settings.viewport_lg,
            viewportMd : $settings.viewport_md
        });

        //loaded class for better showing
        $($honeycombs).addClass('honeycombs-loaded');



    };
    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-honeycombs.default', widgetHoneycombs);
    });
}(jQuery, window.elementorFrontend));

/**
 * End honeycombs widget script
 */


(function ($, elementor) {
  $(window).on("elementor/frontend/init", function () {
    let ModuleHandler = elementorModules.frontend.handlers.Base,
      HorizontalScroller;

    HorizontalScroller = ModuleHandler.extend({
      bindEvents: function () {
        this.run();
      },
      getDefaultSettings: function () {
        return {
          allowHTML: true,
        };
      },

      settings: function (key) {
        return this.getElementSettings("horizontal_scroller_" + key);
      },

      sectionJoiner: function () {
        const widgetID = this.$element.data("id"),
          sectionId = [],
          sectionList = this.settings("section_list"),
          widgetContainer = ".elementor-element-" + widgetID,
          widgetWrapper = widgetContainer + " .bdt-ep-hc-wrapper";

        sectionList.forEach((section) => {
          sectionId.push("#" + section.horizontal_scroller_section_id);
        });

        //check is id exists
        var haveIds = [];
        let elements;
        elements = document.querySelectorAll(".elementor-top-section");
        if (elements.length == 0) {
          elements = document.querySelectorAll(".elementor-element.e-con");
        }

        elements.forEach((element) => {
          var elementsWrapper = element.getAttribute("id");
          haveIds.push("#" + elementsWrapper);
        });
        function intersection(arr1, arr2) {
          var temp = [];
          for (var i in arr1) {
            var element = arr1[i];

            if (arr2.indexOf(element) > -1) {
              temp.push(element);
            }
          }
          return temp;
        }
        function multi_intersect() {
          var arrays = Array.prototype.slice.apply(arguments).slice(1);
          var temp = arguments[0];
          for (var i in arrays) {
            temp = intersection(arrays[i], temp);
            if (temp == []) {
              break;
            }
          }
          return temp;
        }
        var ids = multi_intersect(haveIds, sectionId).toString();
        var selectedIDs = document.querySelectorAll(ids);
        $(widgetWrapper).append(selectedIDs);
      },

      horizontalScroller: function () {
        gsap.registerPlugin(ScrollTrigger, ScrollToPlugin);
        let sections;
        const widgetID = this.$element.data("id"),
          widgetContainer = ".elementor-element-" + widgetID,
          widgetWrapper = widgetContainer + " .bdt-ep-hc-wrapper",
          scroller = document.querySelector(widgetWrapper),
          navLis = document.querySelectorAll(widgetContainer + " nav li");

        sections = gsap.utils.toArray(
          widgetContainer + " .elementor-top-section"
        );
        if (Object.keys(sections).length === 0) {
          sections = gsap.utils.toArray(
            widgetContainer + " .elementor-element.e-con"
          );
        }

        let numSections = sections.length - 1;
        let snapVal = 1 / numSections;
        let optionSnap;
        if (this.settings("auto_fill") !== undefined) {
          optionSnap = snapVal;
        } else {
          optionSnap = false;
        }
        let lastIndex = 0;

        let tween = gsap.to(sections, {
          xPercent: -100 * numSections,
          ease: "none",
          scrollTrigger: {
            trigger: widgetContainer + " .bdt-ep-hc-wrapper",
            pin: true,
            scrub: true,
            snap: optionSnap,
            end: () => "+=" + (scroller.offsetWidth - innerWidth),
            onUpdate: (self) => {
              const newIndex = Math.round(self.progress / snapVal);
              if (this.settings("show_dots") !== undefined) {
                if (newIndex !== lastIndex) {
                  navLis[lastIndex].classList.remove("is-active");
                  navLis[newIndex].classList.add("is-active");
                  lastIndex = newIndex;
                }
              }
            },
          },
        });
        navLis.forEach((anchor, i) => {
          anchor.addEventListener("click", function (e) {
            gsap.to(window, {
              scrollTo: {
                y:
                  tween.scrollTrigger.start +
                  i * innerWidth +
                  ((i + 1) * 1 + i * 1),
                autoKill: false,
              },
              duration: 1,
            });
          });
        });
      },

      run: function () {
        var editMode = Boolean(elementorFrontend.isEditMode());
        if (editMode) {
          return;
        }
        var $this = this;
        const widgetID = this.$element.data("id"),
          widgetContainer = ".elementor-element-" + widgetID;
        ScrollTrigger.matchMedia({
          "(min-width: 1024px)": function () {
            $(widgetContainer).addClass("bdt-ep-hc-active");
            $this.sectionJoiner();
            $this.horizontalScroller();
          },
          "(max-width:1023px)": function () {
            $(widgetContainer).removeClass("bdt-ep-hc-active");
          },
        });
      },
    });

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/bdt-horizontal-scroller.default",
      function ($scope) {
        elementorFrontend.elementsHandler.addHandler(HorizontalScroller, {
          $element: $scope,
        });
      }
    );
  });
})(jQuery, window.elementorFrontend);

(function ($, elementor) {
  $(window).on("elementor/frontend/init", function () {
    let ModuleHandler = elementorModules.frontend.handlers.Base,
      Stacker;

    Stacker = ModuleHandler.extend({
      bindEvents: function () {
        this.run();
      },
      getDefaultSettings: function () {
        return {
          allowHTML: true,
        };
      },

      settings: function (key) {
        return this.getElementSettings("stacker_" + key);
      },

      sectionJoiner: function () {
        const widgetID = this.$element.data("id"),
          sectionId = [],
          sectionList = this.settings("section_list"),
          widgetContainer = ".elementor-element-" + widgetID,
          widgetWrapper = widgetContainer + " .bdt-ep-stacker";

        sectionList.forEach((section) => {
          sectionId.push("#" + section.stacker_section_id);
        });

        //check is id exists
        let haveIds = [];
        let elements;

        const topSection = document.querySelector(".elementor-top-section");
        const eConElements = document.getElementsByClassName("e-con");

        if (topSection) {
          // Code to handle 'elementor-top-section' existence
          elements = document.querySelectorAll(".elementor-top-section");
        }

        if (eConElements.length > 0) {
          // Code to handle 'e-con' existence
          elements = document.querySelectorAll(".elementor-element.e-con");
        }

        elements.forEach((element) => {
          var elementsWrapper = element.getAttribute("id");
          haveIds.push("#" + elementsWrapper);
        });

        function intersection(arr1, arr2) {
          var temp = [];
          for (var i in arr1) {
            var element = arr1[i];

            if (arr2.indexOf(element) > -1) {
              temp.push(element);
            }
          }
          return temp;
        }
        function multi_intersect() {
          var arrays = Array.prototype.slice.apply(arguments).slice(1);
          var temp = arguments[0];
          for (var i in arrays) {
            temp = intersection(arrays[i], temp);
            if (temp == []) {
              break;
            }
          }
          return temp;
        }

        var ids = multi_intersect(haveIds, sectionId).toString();
        if (ids) {
          var selectedIDs = document.querySelectorAll(ids);
          $(widgetWrapper).append(selectedIDs);
        }
        //check is id exists
      },

      StackerOpacity: function () {},
      StackerScript: function () {
        gsap.registerPlugin(ScrollTrigger);
        let cards;
        const widgetID = this.$element.data("id"),
          widgetContainer = ".elementor-element-" + widgetID;
        let stickDistance = 0;
        let opacityEnabled = this.settings("stacking_opacity") == 'yes' ? true : false;
        let stackingSpace = this.settings("stacking_space")
          ? this.settings("stacking_space").size
          : 40;
        let scrollerStart = this.settings("scroller_start")
          ? this.settings("scroller_start").size + "%"
          : "10%";
        cards = gsap.utils.toArray(
          widgetContainer + " .bdt-ep-stacker > .elementor-element"
        );

        cards.forEach((card, i) => {
          let lastCardST = ScrollTrigger.create({
            trigger: cards[cards.length - 1],
            start: `top-=${0 * i} ${scrollerStart}`,
          });

          if(opacityEnabled) {
                gsap.set(card, { opacity: 0 });
                gsap.from(card, {
                  opacity: 1,
                  scrollTrigger: {
                    trigger: card,
                    scrub: true,
                    start: `top-=${stackingSpace * i} ${scrollerStart}`,
                    end: () => lastCardST.start + stickDistance,
                  },
                  ease: "none",
                });
        }

          ScrollTrigger.create({
            trigger: card,
            start: `top-=${stackingSpace * i} ${scrollerStart}`,
            end: () => lastCardST.start + stickDistance,
            endTrigger: cards[cards.length - 1],
            pin: true,
            pinSpacing: false,
            ease: "none",
            toggleActions: "restart none none reverse",
          });
        });
      },

      run: function () {
        const widgetID = this.$element.data("id"),
          widgetContainer = ".elementor-element-" + widgetID,
          widgetWrapper = widgetContainer + " .bdt-ep-stacker";

        var editMode = Boolean(elementorFrontend.isEditMode());
        if (editMode) {
          $(widgetWrapper).append(
            '<div class="bdt-alert-warning" bdt-alert><a class="bdt-alert-close" bdt-close></a><p>Stacker Widget Placed Here (Only Visible for Editor).</p></div>'
          );
          return;
        }
        this.sectionJoiner();
        this.StackerScript();
      },
    });

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/bdt-stacker.default",
      function ($scope) {
        elementorFrontend.elementsHandler.addHandler(Stacker, {
          $element: $scope,
        });
      }
    );
  });
})(jQuery, window.elementorFrontend);

/**
 * Start hover box widget script
 */

(function($, elementor) {

    'use strict';

    var widgetHoverBox = function($scope, $) {


        var $hoverBox = $scope.find('.bdt-ep-hover-box'),
            $settings = $hoverBox.data('settings');

        var iconBx = document.querySelectorAll('#' + $settings.box_id + ' .bdt-ep-hover-box-item');
        var contentBx = document.querySelectorAll('#' + $settings.box_id + ' .bdt-ep-hover-box-content');

        for (var i = 0; i < iconBx.length; i++) {
            iconBx[i].addEventListener($settings.mouse_event, function() {
                for (var i = 0; i < contentBx.length; i++) {
                    contentBx[i].className = 'bdt-ep-hover-box-content'
                }
                document.getElementById(this.dataset.id).className = 'bdt-ep-hover-box-content active';

                for (var i = 0; i < iconBx.length; i++) {
                    iconBx[i].className = 'bdt-ep-hover-box-item';
                }
                this.className = 'bdt-ep-hover-box-item active';

            })
        }

    };

    var widgetHoverBoxFlexure = function($scope, $) {
        var $hoverBoxFlexure = $scope.find('.bdt-ep-hover-box'),
            $settings = $hoverBoxFlexure.data('settings');
            
       var iconBox = $($hoverBoxFlexure).find('.bdt-ep-hover-box-item');

       $(iconBox).on($settings.mouse_event, function(){
        var target = $(this).attr('data-id');
        $('#'+target).siblings().removeClass('active');
        $('[data-id="' + target + '"]').siblings().removeClass('active');
        if($settings.mouse_event == 'click'){
            $('#'+target).toggleClass('active');
            $('[data-id="' + target + '"]').toggleClass('active');
            $('[data-id="' + target + '"]').siblings().addClass('invisiable');
            $($hoverBoxFlexure).find('.bdt-ep-hover-box-item.invisiable').on('click', function(){
                $('[data-id="' + target + '"]').siblings().addClass('invisiable');
                $('[data-id="' + target + '"]').addClass('invisiable');
            });
            $($hoverBoxFlexure).find('.bdt-ep-hover-box-item.active').on('click', function(){
                $('[data-id="' + target + '"]').siblings().removeClass('invisiable');
                $('[data-id="' + target + '"]').removeClass('invisiable');
            });

        }else{
            $('#'+target).addClass('active');
            $('[data-id="' + target + '"]').addClass('active');
            $('[data-id="' + target + '"]').siblings().addClass('invisiable');
        }
       });
       if($settings.mouse_event == 'mouseover'){
        $(iconBox).on('mouseleave', function(){
            var target = $(this).attr('data-id');
            $('#'+target).siblings().removeClass('active');
            $('#'+target).removeClass('active');
            $('[data-id="' + target + '"]').siblings().removeClass('active');
            $('[data-id="' + target + '"]').removeClass('active');
            $('[data-id="' + target + '"]').siblings().removeClass('invisiable');
        });
        }

    };

    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-hover-box.default', widgetHoverBox);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-hover-box.bdt-envelope', widgetHoverBox);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-hover-box.bdt-flexure', widgetHoverBoxFlexure);
    });

}(jQuery, window.elementorFrontend));

/**
 * End hover box widget script
 */


/**
 * Start hover video widget script
 */

 (function ($, elementor) {
    'use strict';

    // check video buffer 

    function videoBufferChecker(videoId) {
        var checkInterval = 50.0; // check every 50 ms (do not use lower values)
        var lastPlayPos = 0;
        var currentPlayPos = 0;
        var bufferingDetected = false;
        var player = document.getElementById(videoId);

        setInterval(checkBuffering, checkInterval)

        function checkBuffering() {
            currentPlayPos = player.currentTime;

            // checking offset should be at most the check interval
            // but allow for some margin
            var offset = (checkInterval - 20) / 2000;

            // if no buffering is currently detected,
            // and the position does not seem to increase
            // and the player isn't manually paused...
            if (!bufferingDetected &&
                currentPlayPos < (lastPlayPos + offset) &&
                !player.paused
                ) {
                // console.log("buffering = " + videoId);
            $('#' + videoId).closest('.bdt-hover-video').find('.hover-video-loader').addClass('active');

            bufferingDetected = true;
        }

            // if we were buffering but the player has advanced,
            // then there is no buffering
            if (
                bufferingDetected &&
                currentPlayPos > (lastPlayPos + offset) &&
                !player.paused
                ) {
                // console.log("not buffering anymore = " + videoId);
            $('#' + videoId).closest('.bdt-hover-video').find('.hover-video-loader').removeClass('active');
            bufferingDetected = false
        }
        lastPlayPos = currentPlayPos


    }
}


var widgetDefaultSkin = function ($scope, $) {
    var $instaVideo = $scope.find('.bdt-hover-video'),
    $settings = $instaVideo.data('settings');
    
    if (!$instaVideo.length) {
        return;
    }

    var video = $($instaVideo).find('.bdt-hover-wrapper-list  video');
    var videoProgress;
    setInterval(function () {
        videoProgress = $('.bdt-hover-progress.active');
    }, 100);

    $(video).on('mouseenter click', function (e) {

        if($settings.videoReplay == 'yes'){
            $(this)[0].currentTime = 0; 
        }

        $(this).trigger('play');

        videoBufferChecker($(this).attr('id'));

        var video = $($instaVideo).find('.bdt-hover-video  .bdt-hover-wrapper-list  video');

        var thisId = $(this).attr('id');

        $('#' + thisId).on('ended', function () {
            setTimeout(function (a) {
                $('#' + thisId).trigger('play');

                videoBufferChecker(thisId);

            }, 1500);
        });
    });


    $(video).on('mouseout', function (e) {
        // $(this).trigger('pause');

        if ($settings.posterAgain == 'yes') {
           $(this)[0].currentTime = 0;
           $(this).trigger('load');
        }else{
            $(this).trigger('pause');
        }
    });

    $(video).on('timeupdate', function () {
        var videoBarList = $(video).parent().find('video.active').attr('id');
        var ct = document.getElementById(videoBarList).currentTime;
        var dur = document.getElementById(videoBarList).duration;

        var videoProgressPos = ct / dur;
        $($instaVideo).find('.bdt-hover-bar-list').find("[data-id=" + videoBarList + "]").width(videoProgressPos * 100 + "%");
        $($instaVideo).find('.bdt-hover-btn-wrapper').find(".bdt-hover-progress[data-id=" + videoBarList + "]").width(videoProgressPos * 100 + "%");

            // if (video.ended) {
            // }


        });

    if ($($instaVideo).find('.autoplay').length > 0) {
        $($instaVideo).find(".bdt-hover-wrapper-list  video:first-child").trigger('play');

    }

    if ($($instaVideo).find('.autoplay').length > 0) {
        var playingVideo = $(video).parent().find('video.active');

        $(video).on('timeupdate', function () {
            playingVideo = $(video).parent().find('video.active');
        });

        setInterval(function () {
            $(playingVideo).on('ended', function () {

                var nextVideoId = $(playingVideo).next().attr('id');

                $('#' + nextVideoId).siblings().css("display", 'none').removeClass('active');
                $('#' + nextVideoId).css("display", 'block').addClass('active');

                $('#' + nextVideoId).trigger('play');


                if ($(playingVideo).next('video').length > 0) {
                    var firstVideo = $(playingVideo).siblings().first().attr('id');
                    $($instaVideo).find("[data-id=" + firstVideo + "]").closest('.bdt-hover-bar-list').find('.bdt-hover-progress').width(0 + '%');

                    $($instaVideo).find('.bdt-hover-btn-wrapper').find("[data-id=" + nextVideoId + "]").siblings().removeClass('active');
                    $($instaVideo).find('.bdt-hover-btn-wrapper').find("[data-id=" + nextVideoId + "]").addClass('active');

                    $($instaVideo).find('.bdt-hover-btn-wrapper').find(".bdt-hover-progress").width("0%");

                } else {
                    var firstVideo = $(playingVideo).siblings().first().attr('id');
                        // console.log("Dont exists"+firstVideo);
                        $('#' + firstVideo).siblings().css("display", 'none').removeClass('active');
                        $('#' + firstVideo).css("display", 'block').addClass('active');
                        $($instaVideo).find("[data-id=" + firstVideo + "]").closest('.bdt-hover-bar-list').find('.bdt-hover-progress').width(0 + '%');


                        $($instaVideo).find('.bdt-hover-btn-wrapper').find("[data-id=" + firstVideo + "]").siblings().removeClass('active');
                        $($instaVideo).find('.bdt-hover-btn-wrapper').find("[data-id=" + firstVideo + "]").addClass('active');

                        $($instaVideo).find('.bdt-hover-btn-wrapper').find(".bdt-hover-progress").width("0%");

                        $('#' + firstVideo).trigger('play');

                    }

                });
        }, 1000);

    }



    $('#'+$settings.id).find('.bdt-hover-btn').on('mouseenter click', function () {
        var videoId = $(this).attr('data-id');

        if($settings.videoReplay == 'yes'){
            $('#'+videoId)[0].currentTime = 0;
        }


        $('#' + videoId).trigger('play');

        videoBufferChecker(videoId);

        $('#' + videoId).siblings().css("display", 'none').removeClass('active');
        $('#' + videoId).css("display", 'block').addClass('active');
        $('.bdt-hover-bar-list .bdt-hover-progress').removeClass('active');
        $('.bdt-hover-bar-list').find("[data-id=" + videoId + "]").addClass('active');

        $('.bdt-hover-btn-wrapper').find("[data-id=" + videoId + "]")
        .siblings().removeClass('active');
        $('.bdt-hover-btn-wrapper').find("[data-id=" + videoId + "]")
        .addClass('active');


    });




};

var widgetVideoAccordion = function ($scope, $) {
    var $videoAccordion = $scope.find('.bdt-hover-video'),
    $settings = $videoAccordion.data('settings');

    if (!$videoAccordion.length) {
        return;
    }

    var video = $($videoAccordion).find('.bdt-hover-wrapper-list  video');

    var videoProgress;
    setInterval(function () {
        videoProgress = $('.bdt-hover-progress.active');
    }, 100);

    $(video).on('timeupdate', function () {
        var videoBarList = $(video).parent().find('video.active').attr('id');
        var ct = document.getElementById(videoBarList).currentTime;
        var dur = document.getElementById(videoBarList).duration;
        var videoProgressPos = ct / dur;
        $('.bdt-hover-bar-list').find("[data-id=" + videoBarList + "]").width(videoProgressPos * 100 + "%");
            // if (video.ended) {
            // }
        });

        // start autoplay 
        if ($($videoAccordion).find('.autoplay').length > 0) {
            $($videoAccordion).find(".hover-video-list  video:first-child").trigger('play');
        }

        if ($($videoAccordion).find('.autoplay').length > 0) {
            var playingVideo = $(video).parent().find('video.active');

            $(video).on('timeupdate', function () {
                playingVideo = $(video).parent().find('video.active');
            });

            setInterval(function () {
                $(playingVideo).on('ended', function () {

                    var nextVideoId = $(playingVideo).next().attr('id');

                    $('#' + nextVideoId).siblings().css("display", 'none').removeClass('active');
                    $('#' + nextVideoId).css("display", 'block').addClass('active');

                    // console.log('playingVideo = '+ $(playingVideo).attr('id'));

                    $('#' + nextVideoId).trigger('play');

                    if ($(playingVideo).next('video').length > 0) {
                        // console.log("Exists");
                        var firstVideo = $(playingVideo).siblings().first().attr('id');
                        $($videoAccordion).find("[data-id=" + firstVideo + "]").closest('.bdt-hover-bar-list').find('.bdt-hover-progress').width(0 + '%');

                    } else {
                        var firstVideo = $(playingVideo).siblings().first().attr('id');
                        // console.log("Dont exists"+firstVideo);
                        $('#' + firstVideo).siblings().css("display", 'none').removeClass('active');
                        $('#' + firstVideo).css("display", 'block').addClass('active');
                        $($videoAccordion).find("[data-id=" + firstVideo + "]").closest('.bdt-hover-bar-list').find('.bdt-hover-progress').width(0 + '%');



                        $('#' + firstVideo).trigger('play');
                    }

                });
            }, 1000);

        }
        // end autoplay 
        
        $('#'+$settings.id).find('.bdt-hover-mask-list .bdt-hover-mask').on('mouseenter click', function () {
            var videoId = $(this).attr('data-id');
            $('#' + videoId).siblings().css("display", 'none').removeClass('active');
            $('#' + videoId).css("display", 'block').addClass('active');
            $('#' + videoId).siblings().trigger('pause'); // play item on active
            
            if($settings.videoReplay == 'yes'){
                $('#'+videoId)[0].currentTime = 0;
                
            }
            
            $('#' + videoId).trigger('play'); // play item on active

            videoBufferChecker(videoId);

            $('.bdt-hover-bar-list .bdt-hover-progress').removeClass('active');
            $('.bdt-hover-bar-list').find("[data-id=" + videoId + "]").addClass('active');

            $('.bdt-hover-mask-list').find("[data-id=" + videoId + "]")
            .siblings().removeClass('active');
            $('.bdt-hover-mask-list').find("[data-id=" + videoId + "]")
            .addClass('active');


            $('#' + videoId).on('ended', function () {
                setTimeout(function (a) {
                    $('#' + videoId).trigger('play');

                    videoBufferChecker(videoId);

                }, 1500);
            });


        });
        $('.bdt-hover-mask-list').on('mouseout', function (e) {
            // $(this).siblings('.bdt-hover-wrapper-list .hover-video-list').find('video').trigger('pause');
            if ($settings.posterAgain == 'yes') {
                     $(this).siblings('.bdt-hover-wrapper-list .hover-video-list').find('video')[0].currentTime = 0;
                     $(this).siblings('.bdt-hover-wrapper-list .hover-video-list').find('video').trigger('load');
            } else {
                $(this).siblings('.bdt-hover-wrapper-list .hover-video-list').find('video').trigger('pause');
            }
        });


    };  
    
    
    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-hover-video.default', widgetDefaultSkin);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-hover-video.accordion', widgetVideoAccordion);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-hover-video.vertical', widgetVideoAccordion);
    });
}(jQuery, window.elementorFrontend));

/**
 * End hover video widget script
 */
/**
 * Start iconnav widget script
 */

( function( $, elementor ) {

	'use strict'; 

	var widgetIconNav = function( $scope, $ ) {

		var $iconnav        = $scope.find( 'div.bdt-icon-nav' ),
            $iconnavTooltip = $iconnav.find( '.bdt-icon-nav' );

        if ( ! $iconnav.length ) {
            return;
        }

		var $tooltip = $iconnavTooltip.find('> .bdt-tippy-tooltip'),
			widgetID = $scope.data('id');
		
		$tooltip.each( function( index ) {
			tippy( this, {
				allowHTML: true,
				theme: 'bdt-tippy-' + widgetID
			});				
		});

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-iconnav.default', widgetIconNav );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End iconnav widget script
 */


/**
 * Start iframe widget script
 */

(function ($, elementor) {

    'use strict';

    var widgetIframe = function ($scope, $) {

        var $iframe = $scope.find('.bdt-iframe > iframe'),
            $autoHeight = $iframe.data('auto_height');

        if (!$iframe.length) {
            return;
        }

        // Auto height only works when cross origin properly set

        $($iframe).recliner({
            throttle: $iframe.data('throttle'),
            threshold: $iframe.data('threshold'),
            live: $iframe.data('live')
        });

        if ($autoHeight) {
            $(document).on('lazyshow', $iframe, function () {
                var height = jQuery($iframe).contents().find('html').height();
                jQuery($iframe).height(height);
            });
        }
    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-iframe.default', widgetIframe);
    });

}(jQuery, window.elementorFrontend));

/**
 * End iframe widget script
 */

/**
 * Start image accordion widget script
 */

(function($, elementor) {

    'use strict';

    var widgetImageAccordion = function($scope, $) {

        var $imageAccordion = $scope.find('.bdt-ep-image-accordion'),
            $settings = $imageAccordion.data('settings');

        var accordionItem = $imageAccordion.find('.bdt-ep-image-accordion-item');
        var totalItems = $imageAccordion.children().length;

        if (($settings.activeItem == true) && ($settings.activeItemNumber <= totalItems)) {
            $imageAccordion.find('.bdt-ep-image-accordion-item').removeClass('active');
            $imageAccordion.children().eq($settings.activeItemNumber - 1).addClass('active');
        }


        $(accordionItem).on($settings.mouse_event, function() {
            $(this).siblings().removeClass('active');
            $(this).addClass('active');
        });

        if ($settings.activeItem != true) {
            $("body").on($settings.mouse_event, function(e) {
                if (e.target.$imageAccordion == "bdt-ep-image-accordion" || $(e.target).closest(".bdt-ep-image-accordion").length) {} else {
                    $imageAccordion.find('.bdt-ep-image-accordion-item').removeClass('active');
                }
            });
        }
    };

    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-image-accordion.default', widgetImageAccordion);
    });

}(jQuery, window.elementorFrontend));

/**
 * End image accordion widget script
 */
/**
 * Start image compare widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetImageCompare = function( $scope, $ ) {
        var $image_compare_main = $scope.find('.bdt-image-compare');
        var $image_compare      = $scope.find('.image-compare');
        if ( !$image_compare.length ) {
            return;
        }

        var $settings        = $image_compare.data('settings');
        
        var 
        default_offset_pct   = $settings.default_offset_pct,
        orientation          = $settings.orientation,
        before_label         = $settings.before_label,
        after_label          = $settings.after_label,
        no_overlay           = $settings.no_overlay,
        on_hover             = $settings.on_hover,
        add_circle_blur      = $settings.add_circle_blur,
        add_circle_shadow    = $settings.add_circle_shadow,
        add_circle           = $settings.add_circle,
        smoothing            = $settings.smoothing,
        smoothing_amount     = $settings.smoothing_amount,
        bar_color            = $settings.bar_color,
        move_slider_on_hover = $settings.move_slider_on_hover;
      
        var viewers = document.querySelectorAll('#' + $settings.id);
  
        var options = {

            // UI Theme Defaults
            controlColor : bar_color,
            controlShadow: add_circle_shadow,
            addCircle    : add_circle,
            addCircleBlur: add_circle_blur,
          
            // Label Defaults
            showLabels   : no_overlay,
            labelOptions : {
              before       : before_label,
              after        : after_label,
              onHover      : on_hover
            },
          
            // Smoothing
            smoothing      : smoothing,
            smoothingAmount: smoothing_amount,
          
            // Other options
            hoverStart     : move_slider_on_hover,
            verticalMode   : orientation,
            startingPoint  : default_offset_pct,
            fluidMode      : false
          };

          viewers.forEach(function (element){
            var view = new ImageCompare(element, options).mount();
          });

	};

	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-image-compare.default', widgetImageCompare );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End image compare widget script
 */


/**
 * Start image expand widget script
 */

(function ($, elementor) {

    'use strict';

    var widgetImageExpand = function ($scope, $) {

        var $imageExpand = $scope.find('.bdt-ep-image-expand'),
        $settings    = $imageExpand.data('settings');

        var wideitem = document.querySelectorAll('#' + $settings.wide_id + ' .bdt-ep-image-expand-item');

        $(wideitem).click(function () {
            $(this).toggleClass('active');
            $('body').addClass('bdt-ep-image-expanded');
        });

        $(document).on('click', 'body.bdt-ep-image-expanded', function (e) {
            if ( e.target.$imageExpand == 'bdt-ep-image-expand' || $(e.target).closest('.bdt-ep-image-expand').length ) {
            } else {
                $('.bdt-ep-image-expand-item').removeClass('active');

                $($imageExpand).find('.bdt-ep-image-expand-item .bdt-ep-image-expand-content *').removeClass('bdt-animation-'+$settings.default_animation_type);
                $($imageExpand).find('.bdt-ep-image-expand-button').removeClass('bdt-animation-slide-bottom');
            }
        });

        if ( $settings.animation_status == 'yes' ) {

            $($imageExpand).find('.bdt-ep-image-expand-item').each(function (i, e) {

                var self              = $(this),
                $quote            = self.find($settings.animation_of),
                mySplitText       = new SplitText($quote, {
                    type: 'chars, words, lines'
                }),
                splitTextTimeline = gsap.timeline();

                gsap.set($quote, {
                    perspective: 400
                });

                function kill() {
                    splitTextTimeline.clear().time(0);
                    mySplitText.revert();
                }

                $(this).on('click', function () {
                    $($imageExpand).find('.bdt-ep-image-expand-button').removeClass('bdt-animation-slide-bottom');
                    $($imageExpand).find('.bdt-ep-image-expand-button').addClass('bdt-invisible');
                    setTimeout(function () {

                        kill();
                        mySplitText.split({
                            type: 'chars, words, lines'
                        });
                        var stringType = '';


                        if ( 'lines' == $settings.animation_on ) {
                            stringType = mySplitText.lines;
                        } else if ( 'chars' == $settings.animation_on ) {
                            stringType = mySplitText.chars;
                        } else {
                            stringType = mySplitText.words;
                        }

                        splitTextTimeline.staggerFrom(stringType, 0.5,{
                            opacity        : 0,
                            scale          : $settings.anim_scale, //0
                            y              : $settings.anim_rotation_y, //80
                            rotationX      : $settings.anim_rotation_x, //180
                            transformOrigin: $settings.anim_transform_origin, //0% 50% -50
                        }, 0.1).then(function(){
                            $($imageExpand).find('.bdt-ep-image-expand-button').removeClass('bdt-invisible');
                            $($imageExpand).find('.bdt-ep-image-expand-item.active .bdt-ep-image-expand-button').addClass('bdt-animation-slide-bottom');
                        });

                        splitTextTimeline.play();
                    }, 1000);


                });

            });

        }else{
            $($imageExpand).on('click', '.bdt-ep-image-expand-item', function (e) {
            // $($imageExpand).find('.bdt-ep-image-expand-item').on('click', function (e) {
                var thisInstance = $(this).attr('id');
                $('#'+thisInstance).siblings('.bdt-ep-image-expand-item').find('.bdt-ep-image-expand-content *').removeClass('bdt-animation-'+$settings.default_animation_type);
                $('#'+thisInstance).find('.bdt-ep-image-expand-content *').removeClass('bdt-animation-'+$settings.default_animation_type);
                setTimeout(function () {
                   $('#'+thisInstance+'.active').find('.bdt-ep-image-expand-content *').addClass('bdt-animation-'+$settings.default_animation_type);
               }, 1000);
            });
        }


    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-image-expand.default', widgetImageExpand);
    });

}(jQuery, window.elementorFrontend));

/**
 * End image expand widget script
 */


/**
 * Start image magnifier widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetImageMagnifier = function( $scope, $ ) {

		var $imageMagnifier = $scope.find( '.bdt-image-magnifier' ),
            settings        = $imageMagnifier.data('settings'),
            magnifier       = $imageMagnifier.find('> .bdt-image-magnifier-image');

        if ( ! $imageMagnifier.length ) {
            return;
        }

        $(magnifier).ImageZoom(settings);

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-image-magnifier.default', widgetImageMagnifier );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End image magnifier widget script
 */


/**
 * Start image accordion widget script
 */

; (function ($, elementor) {

    'use strict';
    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            ImagePrallaxEffects;

        ImagePrallaxEffects = ModuleHandler.extend({
            bindEvents: function () {
                this.run();
            },
            onElementChange: debounce(function (prop) {
                if (prop.indexOf('element_pack_image_parallax_effects_') !== -1) {
                    this.run();
                }
            }, 400),

            settings: function (key) {
                return this.getElementSettings('element_pack_image_parallax_effects_' + key);
            },
            run: function () {
                var options = this.getDefaultSettings(),
                    element = this.findElement('.elementor-section').get(0),
                    widgetID = this.$element.data('id'),
                    widgetContainer = $('.elementor-element-' + widgetID).find('.elementor-widget-container .elementor-image');
                var image = widgetContainer.find('img').attr('src');
                // console.log(image);
                if ('yes' === this.settings('enable')) {
                    var $content = `<div class="bdt-image-parallax-wrapper" bdt-parallax="bgy: -200" style="background-image: url(${image});"></div>`;
                    $(widgetContainer).append($content);
                } else {
                    return;
                }
            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(ImagePrallaxEffects, {
                $element: $scope
            });
        });
    });

}(jQuery, window.elementorFrontend));

/**
 * End image expand widget script
 */

/**
 * Start price table widget script
 */

 ( function( $, elementor ) {

	'use strict';

	var widgetImageStack = function( $scope, $ ) {

		var $imageStack = $scope.find( '.bdt-image-stack' );

        if ( ! $imageStack.length ) {
            return;
        }

        var $tooltip = $imageStack.find('.bdt-tippy-tooltip'),
        	widgetID = $scope.data('id');
		
		$tooltip.each( function( index ) {
			tippy( this, {
				allowHTML: true,
				theme: 'bdt-tippy-' + widgetID
			});				
		});

    };
    
	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-image-stack.default', widgetImageStack );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End price table widget script
 */
/**
 * Start instagram widget script
 */
 
( function( $, elementor ) {

	'use strict';

	var widgetInstagram = function( $scope, $ ) {

		var $instagram = $scope.find( '.bdt-instagram' ),
            $settings  = $instagram.data('settings'),
            $loadMoreBtn = $instagram.find('.bdt-load-more');

        if ( ! $instagram.length ) {
            return;
        }
    
        var $currentPage = $settings.current_page;

        
        
        callInstagram();

        $($loadMoreBtn).on('click', function(event){
            
            if ($loadMoreBtn.length) {
                $loadMoreBtn.addClass('bdt-load-more-loading');
            }

            $currentPage++;
            $settings.current_page = $currentPage;

            callInstagram();
        });


        function callInstagram(){
            var $itemHolder = $instagram.find('.bdt-grid');

            jQuery.ajax({
                url: window.ElementPackConfig.ajaxurl,
                type:'post',
                data: $settings,
                success:function(response){
                    if($currentPage === 1){
                        // $itemHolder.html(response);	 
                        $itemHolder.append(response);	
                    } else {
                        $itemHolder.append(response);
                    }

                    if ($loadMoreBtn.length) {
                        $loadMoreBtn.removeClass('bdt-load-more-loading');
                    }

                }
            });
        }

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-instagram.default', widgetInstagram );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-instagram.bdt-instagram-carousel', widgetInstagram );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-instagram.bdt-classic-grid', widgetInstagram );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End instagram widget script
 */


/**
 * Start interactive card widget script
 */

(function ($, elementor) {

    'use strict';

    var widgetInteractiveCard = function ($scope, $) {
        var $i_card_main = $scope.find('.bdt-interactive-card');

        if ( !$i_card_main.length ) {
            return;
        }
        var $settings = $i_card_main.data('settings');

        if ( $($settings).length ) {
            var myWave = wavify(document.querySelector('#' + $settings.id), {
                height   : 60,
                bones    : $settings.wave_bones, //3
                amplitude: $settings.wave_amplitude, //40
                speed    : $settings.wave_speed //.25
            });

            setTimeout(function(){
                $($i_card_main).addClass('bdt-wavify-active');
            }, 1000);
        }
    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-interactive-card.default', widgetInteractiveCard);
    });

}(jQuery, window.elementorFrontend));

/**
 * End interactive card widget script
 */


(function ($, elementor) {
  "use strict";

  $(window).on("elementor/frontend/init", function () {
    var ModuleHandler = elementorModules.frontend.handlers.Base,
      SvgMaps;
    SvgMaps = ModuleHandler.extend({
      bindEvents: function () {
        this.run();
      },

      getDefaultSettings: function () {
        return {};
      },

      onElementChange: debounce(function (prop) {
        if (prop.indexOf("svg_maps") !== -1) {
          this.run();
        }
      }, 400),

      settings: function (key) {
        return this.getElementSettings("svg_maps_" + key);
      },
      createColorAxisArray: function () {
        const axisColorList = this.settings("region_axis_color");

        const colors = [];
        // set default color
        // if (this.settings("default_color") != "") {
        //   colors.push(this.settings("default_color"));
        // }

        // set color axis
        axisColorList.forEach((color) => {
          if (color.axis_color !== "") {
            colors.push(color.axis_color);
          }
        });

        return colors;
      },

      createCustomRegion: function (data, options, isLinkable) {
        const regionList = this.settings("array_regions");
        const currentRegionColors = [];
        data.addColumn("string", "Country");
        data.addColumn("number", "Population");
        data.addColumn({ type: "string", role: "tooltip", p: { html: true } });
        regionList.forEach((region, index) => {
          currentRegionColors.push(
            region.active_region_color ? region.active_region_color : "#146C94"
          );
          options.colors = currentRegionColors;
          data.addRows([
            [
              {
                v: region.active_region_code,
                f:
                  region.active_region_name !== ""
                    ? region.active_region_name
                    : region.active_region_code,
              },
              index,
              region.active_tooltip_content,
            ],
          ]);

          isLinkable[region.active_region_code] = {
            url: region.region_link ? region.region_link.url : "",
            target:
              region.region_link && !region.region_link.is_external
                ? "_self"
                : "",
          };
        });
      },
      createDataVisRegions: function (isLinkable) {
        const dataVisualArray = [];
        const dataVisualTitle = this.settings("region_value_title");
        const dataRegionList = this.settings("data_visual_array_regions");
        dataVisualArray[0] = ["Country", dataVisualTitle];

        dataRegionList.forEach((region) => {
          dataVisualArray.push([
            region.visual_data_region_name,
            region.visual_data_value,
          ]);

          isLinkable[region.visual_data_region_name] = {
            url: region.visual_data_region_link
              ? region.visual_data_region_link.url
              : "",
            target:
              region.visual_data_region_link &&
              !region.visual_data_region_link.is_external
                ? "_self"
                : "",
          };
        });

        var data = google.visualization.arrayToDataTable(dataVisualArray);
        return {
          data,
        };
      },
      run: function () {
        const self = this;
        var options = this.getDefaultSettings();
        var element = this.findElement(".elementor-widget-container").get(0),
          widgetID = this.$element.data("id");
        if (jQuery(this.$element).hasClass("elementor-section")) {
          element = this.$element.get(0);
        }
        var $container = this.$element.find(".bdt-svg-maps");
        if (!$container.length) {
          return;
        }
        const $mapWrapper = document.getElementById(`bdt-svg-maps-${widgetID}`);

        google.charts.load("current", {
          packages: ["geochart"],
        });
        google.charts.setOnLoadCallback(drawTable);

        function drawTable() {
          var data = new google.visualization.DataTable();
          let isLinkable = [];
          let markerIsLinkable = [];

          // set region
          switch (self.settings("region_type")) {
            case "continent":
              options.region = self.settings("display_region_continent")
                ? self.settings("display_region_continent")
                : "002";
              break;

            case "subcontinent":
              options.region = self.settings("display_region_sub_continent")
                ? self.settings("display_region_sub_continent")
                : "015";
              break;

            case "countries":
              options.region = self.settings("display_region_countries")
                ? self.settings("display_region_countries")
                : "AU";
              break;

            default:
              options.region = "world";
              break;
          }
          // options.region = self.settings("display_region") ? self.settings("display_region") : "world";


          options.width = self.settings("width")
            ? self.settings("width").size
            : 600;
          options.height = self.settings("height")
            ? self.settings("height").size
            : 400;
          options.backgroundColor = self.settings("background_color")
            ? self.settings("background_color")
            : "#81d4fa";
          options.datalessRegionColor = self.settings("dataless_region_color")
            ? self.settings("dataless_region_color")
            : "#f8bbd0";

          options.tooltip = {
            isHtml: true,
            trigger: self.settings("tooltip_trigger")
              ? self.settings("tooltip_trigger")
              : "focus",
            textStyle: {
              // fontSize: self.settings("tooltip_font_size")   ? self.settings("tooltip_font_size") : 14,
              bold:
                self.settings("tooltip_font_weight") === "yes" ? true : false,
              italic:
                self.settings("tooltip_font_style") === "yes" ? true : false,
            },
          };
          // show legend
          if (self.settings("show_legend") !== "yes") {
            options.legend = "none";
          } else {
            options.legend = {
              textStyle: {
                color: self.settings("legend_font_color")
                  ? self.settings("legend_font_color")
                  : "#000000",
                fontSize: self.settings("legend_font_size")
                  ? self.settings("legend_font_size")
                  : 16,
                bold:
                  self.settings("legend_font_weight") === "yes" ? true : false,
                italic:
                  self.settings("legend_font_style") === "yes" ? true : false,
              },
            };
          }

          // run initilize code here to get data
          if (self.settings("display_mode") === "regions") {
            if (self.settings("display_type") === "custom") {
              self.createCustomRegion(data, options, isLinkable);
            } else {
              const dataVisRegions = self.createDataVisRegions(isLinkable);
              data = dataVisRegions.data;
              // set color axis
              options.colorAxis = {
                colors: self.createColorAxisArray(),
              };
            }
          }

          // Instantiate and draw our chart, passing in some options.
          var chart = new google.visualization.GeoChart($mapWrapper);
          google.visualization.events.addListener(chart, "select", () => {
            const selection = chart.getSelection();
            if (selection.length === 1) {
              const selectedRow = selection[0].row;
              const selectedRegion = data.getValue(selectedRow, 0);
              switch (self.settings("display_mode")) {
                case "regions":
                  isLinkable[selectedRegion].url !== ""
                    ? window.open(
                        isLinkable[selectedRegion].url,
                        isLinkable[selectedRegion].target
                      )
                    : "";
                  break;
                case "markers":
                  markerIsLinkable[selectedRegion].url !== ""
                    ? window.open(
                        markerIsLinkable[selectedRegion].url,
                        markerIsLinkable[selectedRegion].target
                      )
                    : "";
                  break;
              }
            }
          });
          chart.draw(data, options);
        }
      },
    });

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/bdt-svg-maps.default",
      function ($scope) {
        elementorFrontend.elementsHandler.addHandler(SvgMaps, {
          $element: $scope,
        });
      }
    );
  });
})(jQuery, window.elementorFrontend);

/**
 * Start interactive tabs widget script
 */

 (function($, elementor) {

    'use strict';

    var widgetInteractiveTabs = function($scope, $) {

        var $slider = $scope.find('.bdt-interactive-tabs-content'),
            $tabs   = $scope.find('.bdt-interactive-tabs');

        if (!$slider.length) {
            return;
        }

        var $sliderContainer = $slider.find('.swiper-carousel'),
            $settings = $slider.data('settings'),
            $swiperId = $($settings.id).find('.swiper-carousel');

            const Swiper = elementorFrontend.utils.swiper;
            initSwiper();
            async function initSwiper() {
                var swiper = await new Swiper($swiperId, $settings);
                if ($settings.pauseOnHover) {
                    $($sliderContainer).hover(function () {
                        (this).swiper.autoplay.stop();
                    }, function () {
                        (this).swiper.autoplay.start();
                    });
                }
                // start video stop
                var stopVideos = function () {
                    var videos = document.querySelectorAll($settings.id + ' .bdt-interactive-tabs-iframe');
                    Array.prototype.forEach.call(videos, function (video) {
                        var src = video.src;
                        video.src = src;
                    });
                };
                // end video stop

                // $tabs.find('.bdt-interactive-tabs-item').eq(swiper.realIndex).addClass('bdt-active');
                $tabs.find('.bdt-interactive-tabs-item:first').addClass('bdt-active');
                console.log(swiper.realIndex);
                swiper.on('slideChange', function () {
                    $tabs.find('.bdt-interactive-tabs-item').removeClass('bdt-active');
                    $tabs.find('.bdt-interactive-tabs-item').eq(swiper.realIndex).addClass('bdt-active');

                    stopVideos();

                });

                $tabs.find('.bdt-interactive-tabs-wrap .bdt-interactive-tabs-item[data-slide]').on('click', function (e) {
                    e.preventDefault();
                    var slideno = $(this).data('slide');
                    swiper.slideTo(slideno + 1);
                });
            };
    };
    
    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-interactive-tabs.default', widgetInteractiveTabs);
    });

}(jQuery, window.elementorFrontend));

/**
 * End interactive tabs widget script
 */


/**
 * Start logo carousel widget script
 */

(function($, elementor) {

    'use strict';

    var widgetLogoCarousel = function($scope, $) {

        var $logocarousel = $scope.find('.bdt-logo-carousel-wrapper');

        if (!$logocarousel.length) {
            return;
        }

        var $tooltip = $logocarousel.find('> .bdt-tippy-tooltip'),
            widgetID = $scope.data('id'); 

        $tooltip.each(function(index) {
            tippy(this, {
                allowHTML: true,
                theme: 'bdt-tippy-' + widgetID
            });
        });

    };


    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-logo-carousel.default', widgetLogoCarousel);
    });

}(jQuery, window.elementorFrontend));

/**
 * End logo carousel widget script
 */


/**
 * Start logo grid widget script
 */

(function($, elementor) {

    'use strict'; 

    var widgetLogoGrid = function($scope, $) {

        var $logogrid = $scope.find('.bdt-logo-grid-wrapper');

        if (!$logogrid.length) {
            return;
        }

        var $tooltip = $logogrid.find('> .bdt-tippy-tooltip'),
            widgetID = $scope.data('id');

        $tooltip.each(function(index) {
            tippy(this, {
                allowHTML: true,
                theme: 'bdt-tippy-' + widgetID
            });
        });

    };


    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-logo-grid.default', widgetLogoGrid);
    });

}(jQuery, window.elementorFrontend));

/**
 * Start lottie icon box widget script
 */

(function($, elementor) {

    'use strict';

    // lottie-icon-box  
    var widgetLottieImage = function($scope, $) {

        var $lottie = $scope.find('.bdt-lottie-container'),
        $settings = $lottie.data('settings');

        if (!$lottie.length) {
            return;
        }

        var lottieContainer = document.getElementById($($lottie).attr('id'));

        function lottieRun(lottieContainer) {

            var json_path_url = "";

            if ($settings.is_json_url == 1) {
                if ($settings.json_path) {
                    json_path_url = $settings.json_path;
                }
            } else {
                if ($settings.json_code) {
                    var json_path_data = $settings.json_code;
                    var blob = new Blob([json_path_data], { type: 'application/javascript' });
                    json_path_url = URL.createObjectURL(blob);
                }
            }

            var animation = lottie.loadAnimation({
                container: lottieContainer, // Required
                path: json_path_url, // Required
                renderer: $settings.lottie_renderer, // Required
                autoplay: ('autoplay' === $settings.play_action), // Optional
                loop: $settings.loop, // Optional
            });
            URL.revokeObjectURL(json_path_url);

            animation.addEventListener('DOMLoaded', function(e) {
                var firstFrame = animation.firstFrame;
                var totalFrame = animation.totalFrames;

                function getFrameNumberByPercent(percent) {
                    percent = Math.min(100, Math.max(0, percent));
                    return firstFrame + (totalFrame - firstFrame) * percent / 100;
                }

                var startPoint = getFrameNumberByPercent($settings.start_point),
                endPoint = getFrameNumberByPercent($settings.end_point);

                animation.playSegments([startPoint, endPoint], true);

            });

            // if (1 >= $settings.speed) {
                animation.setSpeed($settings.speed);
            // }

            if ($settings.play_action) {


                if ('column' === $settings.play_action) {
                    lottieContainer = $scope.closest('.elementor-widget-wrap')[0];
                }

                if ('section' === $settings.play_action) {
                    lottieContainer = $scope.closest('.elementor-section')[0];
                }


                if ('click' === $settings.play_action) {
                    lottieContainer = $scope.closest('.elementor-widget-wrap')[0];
                    lottieContainer.addEventListener('click', function() {
                        animation.goToAndPlay(0);
                    });

                } else if ('autoplay' !== $settings.play_action) {
                    lottieContainer.addEventListener('mouseenter', function() {
                        animation.goToAndPlay(0);
                    });
                    // lottieContainer.addEventListener('mouseleave', function () {
                    //     animation.stop();
                    // });


                }

            }

        }


        if ('scroll' === $settings.view_type) {
            elementorFrontend.waypoint($lottie, function() {
                lottieRun(lottieContainer);
            });
        } else {
            lottieRun(lottieContainer);
        }
 
};


jQuery(window).on('elementor/frontend/init', function() {
    elementorFrontend.hooks.addAction('frontend/element_ready/bdt-lottie-icon-box.default', widgetLottieImage);
});

}(jQuery, window.elementorFrontend));

/**
 * End lottie icon box widget script
 */


/**
 * Start lottie image widget script
 */

(function($, elementor) {

    'use strict';

    // lottie-image 
    var widgetLottieImage = function($scope, $) {

        var $lottie = $scope.find('.bdt-lottie-container'),
        $settings = $lottie.data('settings');

        if (!$lottie.length) {
            return;
        }

        var lottieContainer = document.getElementById($($lottie).attr('id'));

        function lottieRun(lottieContainer) {
            var json_path_url = "";

            if ($settings.is_json_url == 1) {
                if ($settings.json_path) {
                    json_path_url = $settings.json_path;
                }
            } else {
                if ($settings.json_code) {
                    var json_path_data = $settings.json_code;
                    var blob = new Blob([json_path_data], {
                        type: 'application/javascript'
                    });
                    json_path_url = URL.createObjectURL(blob);
                }
            }

            var animation = lottie.loadAnimation({
                container: lottieContainer, // Required
                path: json_path_url, // Required
                renderer: $settings.lottie_renderer, // Required
                autoplay: ('autoplay' === $settings.play_action), // Optional
                loop: $settings.loop, // Optional
            });
            URL.revokeObjectURL(json_path_url);

            animation.addEventListener('DOMLoaded', function(e) {
                var firstFrame = animation.firstFrame;
                var totalFrame = animation.totalFrames;

                function getFrameNumberByPercent(percent) {
                    percent = Math.min(100, Math.max(0, percent));
                    return firstFrame + (totalFrame - firstFrame) * percent / 100;
                }

                var startPoint = getFrameNumberByPercent($settings.start_point),
                endPoint = getFrameNumberByPercent($settings.end_point);

                animation.playSegments([startPoint, endPoint], true);

            });

            //if (1 == $settings.speed) {
                animation.setSpeed($settings.speed);
            //}

            if ($settings.play_action) {


                if ('column' === $settings.play_action) {
                    lottieContainer = $scope.closest('.elementor-widget-wrap')[0];
                }

                if ('section' === $settings.play_action) {
                    lottieContainer = $scope.closest('.elementor-section')[0];
                }


                if ('click' === $settings.play_action) {
                    lottieContainer.addEventListener('click', function() {
                        animation.goToAndPlay(0);
                    });

                } else if ('autoplay' !== $settings.play_action) {

                    lottieContainer.addEventListener('mouseenter', function() {
                        animation.goToAndPlay(0);
                    });
                    // lottieContainer.addEventListener('mouseleave', function () {
                    //     animation.stop();
                    // });

                }

            }

        }


        if ('scroll' === $settings.view_type) {
            elementorFrontend.waypoint($lottie, function() {
                lottieRun(lottieContainer);
            });
        } else {
            lottieRun(lottieContainer);
        }
    };


    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-lottie-image.default', widgetLottieImage);
    });

}(jQuery, window.elementorFrontend));

/**
 * End lottie image widget script
 */


/**
 * Start mailchimp widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetMailChimp = function( $scope, $ ) {

		var $mailChimp = $scope.find('.bdt-mailchimp');
			
        if ( ! $mailChimp.length ) {
            return;
        }

        var langStr = window.ElementPackConfig.mailchimp;

        $mailChimp.submit(function(){
            
            var mailchimpform = $(this);
            bdtUIkit.notification({message: '<span bdt-spinner></span> ' + langStr.subscribing, timeout: false, status: 'primary'});
            $.ajax({
                url:mailchimpform.attr('action'),
                type:'POST',
                data:mailchimpform.serialize(),
                success:function(data){
                    bdtUIkit.notification.closeAll();
                    bdtUIkit.notification({message: data, status: 'success'});
                    
                    // set local storage for coupon reveal
                    // localStorage.setItem("epCouponReveal", 'submitted');
                }
            });
            return false;

        });

        return false;

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-mailchimp.default', widgetMailChimp );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End mailchimp widget script
 */


/**
 * Start marker widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetMarker = function( $scope, $ ) {

		var $marker = $scope.find( '.bdt-marker-wrapper' );

        if ( ! $marker.length ) {
            return;
        }

		var $tooltip = $marker.find('> .bdt-tippy-tooltip'),
			widgetID = $scope.data('id');
		
		$tooltip.each( function( index ) {
			tippy( this, {
				allowHTML: true,
				theme: 'bdt-tippy-' + widgetID
			});				
		});

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-marker.default', widgetMarker );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End marker widget script
 */


/**
 * Start modal widget script
 */
(function ($, elementor) {

    'use strict';

    var widgetModal = function ($scope, $) {

        var $modal = $scope.find('.bdt-modal');

        if (!$modal.length) {
            return;
        }

        $.each($modal, function (index, val) {

            var $this = $(this),
                $settings = $this.data('settings'),
                modalShowed = false,
                modalID = $settings.id,
                displayTimes = $settings.displayTimes,
                scrollDirection = $settings.scrollDirection,
                scrollSelector = $settings.scrollSelector,
                scrollOffset = $settings.scrollOffset,
                splashInactivity = $settings.splashInactivity,
                closeBtnDelayShow = $settings.closeBtnDelayShow,
                delayTime = $settings.delayTime,
                splashDelay = $settings.splashDelay,
                widgetId = $settings.widgetId,
                layout = $settings.layout;
            var editMode = Boolean(elementorFrontend.isEditMode()),
                inactiveTime;

            var modal = {
                setLocalize: function () {
                    
                    // clear cache if user logged in
                        if ($('body').hasClass('logged-in') || editMode) {
                            if ($settings.cacheOnAdmin !== true) {
                                localStorage.removeItem(widgetId + '_expiresIn');
                                localStorage.removeItem(widgetId);
                                return;
                            }
                        }
                    
                    this.clearLocalize();
                    var widgetID = widgetId,
                        localVal = 0,
                        // hours = 4;
                        hours = $settings.displayTimesExpire;

                    var expires = (hours * 60 * 60);
                    var now = Date.now();
                    var schedule = now + expires * 1000;

                    if (localStorage.getItem(widgetID) === null) {
                        localStorage.setItem(widgetID, localVal);
                        localStorage.setItem(widgetID + '_expiresIn', schedule);
                    }
                    if (localStorage.getItem(widgetID) !== null) {
                        var count = parseInt(localStorage.getItem(widgetID));
                        count++;
                        localStorage.setItem(widgetID, count);
                        // this.clearLocalize();
                    }
                },
                clearLocalize: function () {
                    var localizeExpiry = parseInt(localStorage.getItem(widgetId + '_expiresIn'));
                    var now = Date.now(); //millisecs since epoch time, lets deal only with integer
                    var schedule = now;
                    if (schedule >= localizeExpiry) {
                        localStorage.removeItem(widgetId + '_expiresIn');
                        localStorage.removeItem(widgetId);
                    }
                },
                modalFire: function () {
                    if (layout == 'splash' || layout == 'exit' || layout == 'on_scroll') {
                        modal.setLocalize();
                        var firedNotify = parseInt(localStorage.getItem(widgetId));
                        if ((displayTimes !== false) && (firedNotify > displayTimes)) {
                            return;
                        }
                    }
                    bdtUIkit.modal($this).show();
                },
                closeBtnDelayShow: function () {
                    var $modal = $('#' + modalID);
                    $modal.find('#bdt-modal-close-button').hide(0);
                    $modal.on("shown", function () {
                            $('#bdt-modal-close-button').hide(0).fadeIn(delayTime);
                        })
                        .on("hide", function () {
                            $modal.find('#bdt-modal-close-button').hide(0);
                        });
                },
                customTrigger: function () {
                    var init = this;
                    $(modalID).on('click', function (event) {
                        event.preventDefault();
                        init.modalFire();
                    });
                },
                scrollDetect: function (fn) {
                    let last_scroll_position = 0;
                    let ticking = false;
                    window.addEventListener("scroll", function () {
                        let prev_scroll_position = last_scroll_position;
                        last_scroll_position = window.scrollY;
                        if (!ticking) {
                            window.requestAnimationFrame(function () {
                                fn(last_scroll_position, prev_scroll_position);
                                ticking = false;
                            });
                            ticking = true;
                        }
                    });
                },
                modalFireOnSelector: function () {
                    var init = this;
                    if (scrollDirection) {
                        $(window).on('scroll', function () {
                            var hT = $(scrollSelector).offset().top,
                                hH = $(scrollSelector).outerHeight(),
                                wH = $(window).height(),
                                wS = $(this).scrollTop();
                            var firedId = widgetId + '-fired';
                            if (wS > (hT + hH - wH)) {
                                if (!$(scrollSelector).hasClass(firedId)) {
                                    init.modalFire();
                                }
                                $(scrollSelector).addClass(firedId); // tricks added
                            }
                        });
                    }
                },
                onScroll: function () {
                    var init = this;
                    this.scrollDetect((scrollPos, previousScrollPos) => {
                        var wintop = $(window).scrollTop(),
                            docheight = $(document).height(),
                            winheight = $(window).height();
                        var scrolltrigger = scrollOffset / 100;
                        var firedId = widgetId + '-fired';
                        if ((previousScrollPos > scrollPos) && scrollDirection == 'up') {
                            if (!$('body').hasClass(firedId)) {
                                init.modalFire();
                                $('body').addClass(firedId);
                            }
                        } else if ((previousScrollPos < scrollPos) && scrollDirection == 'down' && previousScrollPos !== 0) {
                            if ((wintop / (docheight - winheight)) > scrolltrigger) {
                                if (!$('body').hasClass(firedId)) {
                                    init.modalFire();
                                    $('body').addClass(firedId);
                                }
                            }
                        } else if ((previousScrollPos < scrollPos) && scrollDirection == 'selector' && previousScrollPos !== 0) {
                            init.modalFireOnSelector();
                        }
                    });
                },
                exitPopup: function () {
                    var init = this;
                    document.addEventListener('mouseleave', function (event) {
                        if (
                            event.clientY <= 0 ||
                            event.clientX <= 0 ||
                            (event.clientX >= window.innerWidth || event.clientY >= window.innerHeight)
                        ) {
                            if (!editMode){
                                init.modalFire();
                            }
                        }
                    });
                },
                resetTimer: function () {
                    clearTimeout(inactiveTime);
                    inactiveTime = setTimeout(this.modalFire, splashInactivity); // time is in milliseconds
                },
                splashInactivity: function () {
                    window.onload = this.resetTimer();
                    window.onmousemove = this.resetTimer();
                    window.onmousedown = this.resetTimer(); // catches touchscreen presses as well      
                    window.ontouchstart = this.resetTimer(); // catches touchscreen swipes as well 
                    window.onclick = this.resetTimer(); // catches touchpad clicks as well
                    window.onkeydown = this.resetTimer();
                    window.addEventListener('scroll', this.resetTimer(), true); // improved; see comments
                },
                splashTiming: function () {
                    var init = this;
                    setTimeout(function () {
                        init.modalFire();
                    }, splashDelay);
                },
                splashInit: function () {
                    if (!splashInactivity) {
                        this.splashTiming();
                        return;
                    }
                    this.splashInactivity();
                },
                default: function () {
                    $(modalID).on('click', function (event) {
                        event.preventDefault();
                        this.modalFire();
                    });
                },
                init: function () {
                    var init = this;

                    if (layout == 'default') {
                        init.default();
                    }
                    if (layout == 'splash') {
                        init.splashInit();
                    }
                    if (layout == 'exit') {
                        init.exitPopup();
                    }
                    if (layout == 'on_scroll') {
                        init.onScroll();
                    }
                    if (layout == 'custom') {
                        init.customTrigger();
                    }
                    if (closeBtnDelayShow) {
                        init.closeBtnDelayShow();
                    }
                }
            };

            // kick off the modal
            modal.init();
            

            // append section as content

            if ($settings !== undefined && editMode === false && $settings.custom_section != false) {
                $($settings.modal_id).addClass('elementor elementor-' + $settings.pageID)
                var $modalContent = $('.elementor-section' + $settings.custom_section);
                var $modalBody = $($settings.modal_id).find('.bdt-modal-body');
                $($modalContent).appendTo($modalBody);
            }

        });
    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-modal.default', widgetModal);
    });

}(jQuery, window.elementorFrontend));

/**
 * End modal widget script
 */

/**
 * Start news ticker widget script
 */

(function ($) {
    "use strict";
    $.epNewsTickerOld = function (element, options) {

        var defaults = {
            effect: 'fade',
            direction: 'ltr',
            autoPlay: false,
            interval: 4000,
            scrollSpeed: 2,
            pauseOnHover: false,
            position: 'auto',
            zIndex: 99999
        }

        var ticker = this;
        ticker.settings = {};
        ticker._element = $(element);

        ticker._label = ticker._element.children(".bdt-news-ticker-label"),
            ticker._news = ticker._element.children(".bdt-news-ticker-content"),
            ticker._ul = ticker._news.children("ul"),
            ticker._li = ticker._ul.children("li"),
            ticker._controls = ticker._element.children(".bdt-news-ticker-controls"),
            ticker._prev = ticker._controls.find(".bdt-news-ticker-prev").parent(),
            ticker._action = ticker._controls.find(".bdt-news-ticker-action").parent(),
            ticker._next = ticker._controls.find(".bdt-news-ticker-next").parent();

        ticker._pause = false;
        ticker._controlsIsActive = true;
        ticker._totalNews = ticker._ul.children("li").length;
        ticker._activeNews = 0;
        ticker._interval = false;
        ticker._frameId = null;

        /****************************************************/
        /**PRIVATE METHODS***********************************/
        /****************************************************/

        var setContainerWidth = function () {
            if (ticker._label.length > 0) {
                if (ticker.settings.direction == 'rtl')
                    ticker._news.css({
                        "right": ticker._label.outerWidth()
                    });
                else
                    ticker._news.css({
                        "left": ticker._label.outerWidth()
                    });
            }

            if (ticker._controls.length > 0) {
                var controlsWidth = ticker._controls.outerWidth();
                if (ticker.settings.direction == 'rtl')
                    ticker._news.css({
                        "left": controlsWidth
                    });
                else
                    ticker._news.css({
                        "right": controlsWidth
                    });
            }

            if (ticker.settings.effect === 'scroll') {
                var totalW = 0;
                ticker._li.each(function () {
                    totalW += $(this).outerWidth();
                });
                totalW += 50;
                ticker._ul.css({
                    'width': totalW
                });
            }
        }


        var startScrollAnimationLTR = function () {
            var _ulPosition = parseFloat(ticker._ul.css('marginLeft'));
            _ulPosition -= ticker.settings.scrollSpeed / 2;
            ticker._ul.css({
                'marginLeft': _ulPosition
            });

            if (_ulPosition <= -ticker._ul.find('li:first-child').outerWidth()) {
                ticker._ul.find('li:first-child').insertAfter(ticker._ul.find('li:last-child'));
                ticker._ul.css({
                    'marginLeft': 0
                });
            }
            if (ticker._pause === false) {
                ticker._frameId = requestAnimationFrame(startScrollAnimationLTR);
                (window.requestAnimationFrame && ticker._frameId) || setTimeout(startScrollAnimationLTR, 16);
            }
        }

        var startScrollAnimationRTL = function () {
            var _ulPosition = parseFloat(ticker._ul.css('marginRight'));
            _ulPosition -= ticker.settings.scrollSpeed / 2;
            ticker._ul.css({
                'marginRight': _ulPosition
            });

            if (_ulPosition <= -ticker._ul.find('li:first-child').outerWidth()) {
                ticker._ul.find('li:first-child').insertAfter(ticker._ul.find('li:last-child'));
                ticker._ul.css({
                    'marginRight': 0
                });
            }
            if (ticker._pause === false)
                ticker._frameId = requestAnimationFrame(startScrollAnimationRTL);
            (window.requestAnimationFrame && ticker._frameId) || setTimeout(startScrollAnimationRTL, 16);
        }

        var scrollPlaying = function () {
            if (ticker.settings.direction === 'rtl') {
                if (ticker._ul.width() > ticker._news.width())
                    startScrollAnimationRTL();
                else
                    ticker._ul.css({
                        'marginRight': 0
                    });
            } else
            if (ticker._ul.width() > ticker._news.width())
                startScrollAnimationLTR();
            else
                ticker._ul.css({
                    'marginLeft': 0
                });
        }

        var scrollGoNextLTR = function () {
            ticker._ul.stop().animate({
                marginLeft: -ticker._ul.find('li:first-child').outerWidth()
            }, 300, function () {
                ticker._ul.find('li:first-child').insertAfter(ticker._ul.find('li:last-child'));
                ticker._ul.css({
                    'marginLeft': 0
                });
                ticker._controlsIsActive = true;
            });
        }

        var scrollGoNextRTL = function () {
            ticker._ul.stop().animate({
                marginRight: -ticker._ul.find('li:first-child').outerWidth()
            }, 300, function () {
                ticker._ul.find('li:first-child').insertAfter(ticker._ul.find('li:last-child'));
                ticker._ul.css({
                    'marginRight': 0
                });
                ticker._controlsIsActive = true;
            });
        }

        var scrollGoPrevLTR = function () {
            var _ulPosition = parseInt(ticker._ul.css('marginLeft'), 10);
            if (_ulPosition >= 0) {
                ticker._ul.css({
                    'margin-left': -ticker._ul.find('li:last-child').outerWidth()
                });
                ticker._ul.find('li:last-child').insertBefore(ticker._ul.find('li:first-child'));
            }

            ticker._ul.stop().animate({
                marginLeft: 0
            }, 300, function () {
                ticker._controlsIsActive = true;
            });
        }

        var scrollGoPrevRTL = function () {
            var _ulPosition = parseInt(ticker._ul.css('marginRight'), 10);
            if (_ulPosition >= 0) {
                ticker._ul.css({
                    'margin-right': -ticker._ul.find('li:last-child').outerWidth()
                });
                ticker._ul.find('li:last-child').insertBefore(ticker._ul.find('li:first-child'));
            }

            ticker._ul.stop().animate({
                marginRight: 0
            }, 300, function () {
                ticker._controlsIsActive = true;
            });
        }

        var scrollNext = function () {
            if (ticker.settings.direction === 'rtl')
                scrollGoNextRTL();
            else
                scrollGoNextLTR();
        }

        var scrollPrev = function () {
            if (ticker.settings.direction === 'rtl')
                scrollGoPrevRTL();
            else
                scrollGoPrevLTR();
        }

        var effectTypography = function () {
            ticker._ul.find('li').hide();
            ticker._ul.find('li').eq(ticker._activeNews).width(30).show();
            ticker._ul.find('li').eq(ticker._activeNews).animate({
                width: '100%',
                opacity: 1
            }, 1500);
        }

        var effectFade = function () {
            ticker._ul.find('li').hide();
            ticker._ul.find('li').eq(ticker._activeNews).fadeIn();
        }

        var effectSlideDown = function () {
            if (ticker._totalNews <= 1) {
                ticker._ul.find('li').animate({
                    'top': 30,
                    'opacity': 0
                }, 300, function () {
                    $(this).css({
                        'top': -30,
                        'opacity': 0,
                        'display': 'block'
                    })
                    $(this).animate({
                        'top': 0,
                        'opacity': 1
                    }, 300);
                });
            } else {
                ticker._ul.find('li:visible').animate({
                    'top': 30,
                    'opacity': 0
                }, 300, function () {
                    $(this).hide();
                });

                ticker._ul.find('li').eq(ticker._activeNews).css({
                    'top': -30,
                    'opacity': 0
                }).show();

                ticker._ul.find('li').eq(ticker._activeNews).animate({
                    'top': 0,
                    'opacity': 1
                }, 300);
            }
        }

        var effectSlideUp = function () {
            if (ticker._totalNews <= 1) {
                ticker._ul.find('li').animate({
                    'top': -30,
                    'opacity': 0
                }, 300, function () {
                    $(this).css({
                        'top': 30,
                        'opacity': 0,
                        'display': 'block'
                    })
                    $(this).animate({
                        'top': 0,
                        'opacity': 1
                    }, 300);
                });
            } else {
                ticker._ul.find('li:visible').animate({
                    'top': -30,
                    'opacity': 0
                }, 300, function () {
                    $(this).hide();
                });

                ticker._ul.find('li').eq(ticker._activeNews).css({
                    'top': 30,
                    'opacity': 0
                }).show();

                ticker._ul.find('li').eq(ticker._activeNews).animate({
                    'top': 0,
                    'opacity': 1
                }, 300);
            }
        }

        var effectSlideRight = function () {
            if (ticker._totalNews <= 1) {
                ticker._ul.find('li').animate({
                    'left': '50%',
                    'opacity': 0
                }, 300, function () {
                    $(this).css({
                        'left': -50,
                        'opacity': 0,
                        'display': 'block'
                    })
                    $(this).animate({
                        'left': 0,
                        'opacity': 1
                    }, 300);
                });
            } else {
                ticker._ul.find('li:visible').animate({
                    'left': '50%',
                    'opacity': 0
                }, 300, function () {
                    $(this).hide();
                });

                ticker._ul.find('li').eq(ticker._activeNews).css({
                    'left': -50,
                    'opacity': 0
                }).show();

                ticker._ul.find('li').eq(ticker._activeNews).animate({
                    'left': 0,
                    'opacity': 1
                }, 300);
            }
        }

        var effectSlideLeft = function () {
            if (ticker._totalNews <= 1) {
                ticker._ul.find('li').animate({
                    'left': '-50%',
                    'opacity': 0
                }, 300, function () {
                    $(this).css({
                        'left': '50%',
                        'opacity': 0,
                        'display': 'block'
                    })
                    $(this).animate({
                        'left': 0,
                        'opacity': 1
                    }, 300);
                });
            } else {
                ticker._ul.find('li:visible').animate({
                    'left': '-50%',
                    'opacity': 0
                }, 300, function () {
                    $(this).hide();
                });

                ticker._ul.find('li').eq(ticker._activeNews).css({
                    'left': '50%',
                    'opacity': 0
                }).show();

                ticker._ul.find('li').eq(ticker._activeNews).animate({
                    'left': 0,
                    'opacity': 1
                }, 300);
            }
        }


        var showThis = function () {
            ticker._controlsIsActive = true;

            switch (ticker.settings.effect) {
                case 'typography':
                    effectTypography();
                    break;
                case 'fade':
                    effectFade();
                    break;
                case 'slide-down':
                    effectSlideDown();
                    break;
                case 'slide-up':
                    effectSlideUp();
                    break;
                case 'slide-right':
                    effectSlideRight();
                    break;
                case 'slide-left':
                    effectSlideLeft();
                    break;
                default:
                    ticker._ul.find('li').hide();
                    ticker._ul.find('li').eq(ticker._activeNews).show();
            }

        }

        var nextHandler = function () {
            switch (ticker.settings.effect) {
                case 'scroll':
                    scrollNext();
                    break;
                default:
                    ticker._activeNews++;
                    if (ticker._activeNews >= ticker._totalNews)
                        ticker._activeNews = 0;

                    showThis();

            }
        }

        var prevHandler = function () {
            switch (ticker.settings.effect) {
                case 'scroll':
                    scrollPrev();
                    break;
                default:
                    ticker._activeNews--;
                    if (ticker._activeNews < 0)
                        ticker._activeNews = ticker._totalNews - 1;

                    showThis();
            }
        }

        var playHandler = function () {
            ticker._pause = false;
            if (ticker.settings.autoPlay) {
                switch (ticker.settings.effect) {
                    case 'scroll':
                        scrollPlaying();
                        break;
                    default:
                        ticker.pause();
                        ticker._interval = setInterval(function () {
                            ticker.next();
                        }, ticker.settings.interval);
                }
            }
        }

        var resizeEvent = function () {
            if (ticker._element.width() < 480) {
                ticker._label.hide();
                if (ticker.settings.direction == 'rtl')
                    ticker._news.css({
                        "right": 0
                    });
                else
                    ticker._news.css({
                        "left": 0
                    });
            } else {
                ticker._label.show();
                if (ticker.settings.direction == 'rtl')
                    ticker._news.css({
                        "right": ticker._label.outerWidth()
                    });
                else
                    ticker._news.css({
                        "left": ticker._label.outerWidth()
                    });
            }
        }

        /****************************************************/
        /**PUBLIC METHODS************************************/
        /****************************************************/
        ticker.init = function () {
            ticker.settings = $.extend({}, defaults, options);

            //ticker._element.append('<div class="bdt-breaking-loading"></div>');
            //window.onload = function(){

            //ticker._element.find('.bdt-breaking-loading').hide();

            //adding effect type class
            ticker._element.addClass('bdt-effect-' + ticker.settings.effect + ' bdt-direction-' + ticker.settings.direction);

            setContainerWidth();

            if (ticker.settings.effect != 'scroll')
                showThis();

            playHandler();

            //set playing status class
            if (!ticker.settings.autoPlay)
                ticker._action.find('span').removeClass('bdt-news-ticker-pause').addClass('bdt-news-ticker-play');
            else
                ticker._action.find('span').removeClass('bdt-news-ticker-play').addClass('bdt-news-ticker-pause');


            ticker._element.on('mouseleave', function (e) {
                var activePosition = $(document.elementFromPoint(e.clientX, e.clientY)).parents('.bdt-breaking-news')[0];
                if ($(this)[0] === activePosition) {
                    return;
                }


                if (ticker.settings.pauseOnHover === true) {
                    if (ticker.settings.autoPlay === true)
                        ticker.play();
                } else {
                    if (ticker.settings.autoPlay === true && ticker._pause === true)
                        ticker.play();
                }

            });

            ticker._element.on('mouseenter', function () {
                if (ticker.settings.pauseOnHover === true)
                    ticker.pause();
            });

            ticker._next.on('click', function () {
                if (ticker._controlsIsActive) {
                    ticker._controlsIsActive = false;
                    ticker.pause();
                    ticker.next();
                }
            });

            ticker._prev.on('click', function () {
                if (ticker._controlsIsActive) {
                    ticker._controlsIsActive = false;
                    ticker.pause();
                    ticker.prev();
                }
            });

            ticker._action.on('click', function () {
                if (ticker._controlsIsActive) {
                    if (ticker._action.find('span').hasClass('bdt-news-ticker-pause')) {
                        ticker._action.find('span').removeClass('bdt-news-ticker-pause').addClass('bdt-news-ticker-play');
                        ticker.stop();
                    } else {
                        ticker.settings.autoPlay = true;
                        ticker._action.find('span').removeClass('bdt-news-ticker-play').addClass('bdt-news-ticker-pause');
                        //ticker._pause = false;
                    }
                }
            });

            resizeEvent();
            //}

            $(window).on('resize', function () {
                resizeEvent();
                ticker.pause();
                ticker.play();
            });

        }

        ticker.pause = function () {
            ticker._pause = true;
            clearInterval(ticker._interval);
            cancelAnimationFrame(ticker._frameId);
        }

        ticker.stop = function () {
            ticker._pause = true;
            ticker.settings.autoPlay = false;
        }

        ticker.play = function () {
            playHandler();
        }

        ticker.next = function () {
            nextHandler();
        }

        ticker.prev = function () {
            prevHandler();
        }
        /****************************************************/
        /****************************************************/
        /****************************************************/
        ticker.init();

    }

    $.fn.epNewsTickerOld = function (options) {

        return this.each(function () {
            if (undefined == $(this).data('epNewsTickerOld')) {
                var ticker = new $.epNewsTickerOld(this, options);
                $(this).data('epNewsTickerOld', ticker);
            }
        });

    }

})(jQuery);



(function ($, elementor) {

    'use strict';

    var widgetNewsTicker = function ($scope, $) {

        var $newsTicker = $scope.find('.bdt-news-ticker'),
            $settings = $newsTicker.data('settings');

        if (!$newsTicker.length) {
            return;
        }

        $($newsTicker).epNewsTickerOld($settings);

    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-news-ticker.default', widgetNewsTicker);
    });

}(jQuery, window.elementorFrontend));

/**
 * End news ticker widget script
 */

;
(function ($, elementor) {
    'use strict';

    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            Notation;

        Notation = ModuleHandler.extend({

            bindEvents: function () {
                this.run();
            },

            getDefaultSettings: function () {
                return {
                    type: 'underline',
                    multiline: true
                };
            },

            onElementChange: debounce(function (prop) {
                if (prop.indexOf('ep_notation_') !== -1) {
                    this.run();
                }
            }, 400),

            settings: function (key) {
                return this.getElementSettings('ep_notation_' + key);
            },

            run: function () {

                if (this.settings('active') != 'yes') {
                    return;
                }

                var
                    $element = this.$element,
                    $widgetId = 'ep-' + this.getID(),
                    $elementID = this.getID(),
                    $globalthis = this;

                var $list = this.settings('list');

                var rtl = ($("body").hasClass("rtl")) ? true : false;


                $list.forEach(element => {
                    var
                        $selectElement = '',
                        bracketOn = '',
                        options = this.getDefaultSettings();

                    if (element.ep_notation_select_type == 'widget') {
                        $($globalthis.findElement('.elementor-widget-container > :not(style)').get(0)).attr('data-notation', $widgetId);
                        $selectElement = '[data-notation="' + $widgetId + '"]';
                    }
                    if (element.ep_notation_select_type == 'custom') {
                        var customSelector = element.ep_notation_custom_selector;


                        if (element.ep_notation_custom_selector && customSelector.length > 1) {
                            $selectElement = '[data-id="' + $elementID + '"] ' + ' ' + customSelector;
                        } else {
                            $selectElement = '.-bdt-empty';
                        }

                    }

                    if (element.ep_notation_type == 'bracket') {
                        bracketOn = element.ep_notation_bracket_on;
                        bracketOn = bracketOn.split(',');
                        options.brackets = bracketOn;
                    }

                    if ($selectElement.length > 0) {

                        var n1 = document.querySelector($selectElement);

                        options.type = element.ep_notation_type;
                        options.color = element.ep_notation_color || '#f23427';
                        options.animationDuration = element.ep_notation_anim_duration.size || 800;
                        options.strokeWidth = element.ep_notation_stroke_width.size || 1;
                        options.rtl = rtl;



                        if ($($selectElement).length > 0) {
                            elementorFrontend.waypoint($($element), function () {

                                const t = "http://www.w3.org/2000/svg";
                                class e {
                                    constructor(t) {
                                        this.seed = t;
                                    }
                                    next() {
                                        return this.seed ? (2 ** 31 - 1 & (this.seed = Math.imul(48271, this.seed))) / 2 ** 31 : Math.random();
                                    }
                                }

                                function s(t, e, s, i, n) {
                                    return {
                                        type: "path",
                                        ops: c(t, e, s, i, n)
                                    };
                                }

                                function i(t, e, i) {
                                    const n = (t || []).length;
                                    if (n > 2) {
                                        const s = [];
                                        for (let e = 0; e < n - 1; e++) s.push(...c(t[e][0], t[e][1], t[e + 1][0], t[e + 1][1], i));
                                        return e && s.push(...c(t[n - 1][0], t[n - 1][1], t[0][0], t[0][1], i)), {
                                            type: "path",
                                            ops: s
                                        };
                                    }
                                    return 2 === n ? s(t[0][0], t[0][1], t[1][0], t[1][1], i) : {
                                        type: "path",
                                        ops: []
                                    };
                                }

                                function n(t, e, s, n, o) {
                                    return function (t, e) {
                                        return i(t, !0, e);
                                    }([
                                        [t, e],
                                        [t + s, e],
                                        [t + s, e + n],
                                        [t, e + n]
                                    ], o);
                                }

                                function o(t, e, s, i, n) {
                                    return function (t, e, s, i) {
                                        const [n, o] = l(i.increment, t, e, i.rx, i.ry, 1, i.increment * h(.1, h(.4, 1, s), s), s);
                                        let r = f(n, null, s);
                                        if (!s.disableMultiStroke) {
                                            const [n] = l(i.increment, t, e, i.rx, i.ry, 1.5, 0, s), o = f(n, null, s);
                                            r = r.concat(o);
                                        }
                                        return {
                                            estimatedPoints: o,
                                            opset: {
                                                type: "path",
                                                ops: r
                                            }
                                        };
                                    }(t, e, n, function (t, e, s) {
                                        const i = Math.sqrt(2 * Math.PI * Math.sqrt((Math.pow(t / 2, 2) + Math.pow(e / 2, 2)) / 2)),
                                            n = Math.max(s.curveStepCount, s.curveStepCount / Math.sqrt(200) * i),
                                            o = 2 * Math.PI / n;
                                        let r = Math.abs(t / 2),
                                            h = Math.abs(e / 2);
                                        const c = 1 - s.curveFitting;
                                        return r += a(r * c, s), h += a(h * c, s), {
                                            increment: o,
                                            rx: r,
                                            ry: h
                                        };
                                    }(s, i, n)).opset;
                                }

                                function r(t) {
                                    return t.randomizer || (t.randomizer = new e(t.seed || 0)), t.randomizer.next();
                                }

                                function h(t, e, s, i = 1) {
                                    return s.roughness * i * (r(s) * (e - t) + t);
                                }

                                function a(t, e, s = 1) {
                                    return h(-t, t, e, s);
                                }

                                function c(t, e, s, i, n, o = !1) {
                                    const r = o ? n.disableMultiStrokeFill : n.disableMultiStroke,
                                        h = u(t, e, s, i, n, !0, !1);
                                    if (r) return h;
                                    const a = u(t, e, s, i, n, !0, !0);
                                    return h.concat(a);
                                }

                                function u(t, e, s, i, n, o, h) {
                                    const c = Math.pow(t - s, 2) + Math.pow(e - i, 2),
                                        u = Math.sqrt(c);
                                    let f = 1;
                                    f = u < 200 ? 1 : u > 500 ? .4 : -.0016668 * u + 1.233334;
                                    let l = n.maxRandomnessOffset || 0;
                                    l * l * 100 > c && (l = u / 10);
                                    const g = l / 2,
                                        d = .2 + .2 * r(n);
                                    let p = n.bowing * n.maxRandomnessOffset * (i - e) / 200,
                                        _ = n.bowing * n.maxRandomnessOffset * (t - s) / 200;
                                    p = a(p, n, f), _ = a(_, n, f);
                                    const m = [],
                                        w = () => a(g, n, f),
                                        v = () => a(l, n, f);
                                    return o && (h ? m.push({
                                        op: "move",
                                        data: [t + w(), e + w()]
                                    }) : m.push({
                                        op: "move",
                                        data: [t + a(l, n, f), e + a(l, n, f)]
                                    })), h ? m.push({
                                        op: "bcurveTo",
                                        data: [p + t + (s - t) * d + w(), _ + e + (i - e) * d + w(), p + t + 2 * (s - t) * d + w(), _ + e + 2 * (i - e) * d + w(), s + w(), i + w()]
                                    }) : m.push({
                                        op: "bcurveTo",
                                        data: [p + t + (s - t) * d + v(), _ + e + (i - e) * d + v(), p + t + 2 * (s - t) * d + v(), _ + e + 2 * (i - e) * d + v(), s + v(), i + v()]
                                    }), m;
                                }

                                function f(t, e, s) {
                                    const i = t.length,
                                        n = [];
                                    if (i > 3) {
                                        const o = [],
                                            r = 1 - s.curveTightness;
                                        n.push({
                                            op: "move",
                                            data: [t[1][0], t[1][1]]
                                        });
                                        for (let e = 1; e + 2 < i; e++) {
                                            const s = t[e];
                                            o[0] = [s[0], s[1]], o[1] = [s[0] + (r * t[e + 1][0] - r * t[e - 1][0]) / 6, s[1] + (r * t[e + 1][1] - r * t[e - 1][1]) / 6], o[2] = [t[e + 1][0] + (r * t[e][0] - r * t[e + 2][0]) / 6, t[e + 1][1] + (r * t[e][1] - r * t[e + 2][1]) / 6], o[3] = [t[e + 1][0], t[e + 1][1]], n.push({
                                                op: "bcurveTo",
                                                data: [o[1][0], o[1][1], o[2][0], o[2][1], o[3][0], o[3][1]]
                                            });
                                        }
                                        if (e && 2 === e.length) {
                                            const t = s.maxRandomnessOffset;
                                            n.push({
                                                op: "lineTo",
                                                data: [e[0] + a(t, s), e[1] + a(t, s)]
                                            });
                                        }
                                    } else 3 === i ? (n.push({
                                        op: "move",
                                        data: [t[1][0], t[1][1]]
                                    }), n.push({
                                        op: "bcurveTo",
                                        data: [t[1][0], t[1][1], t[2][0], t[2][1], t[2][0], t[2][1]]
                                    })) : 2 === i && n.push(...c(t[0][0], t[0][1], t[1][0], t[1][1], s));
                                    return n;
                                }

                                function l(t, e, s, i, n, o, r, h) {
                                    const c = [],
                                        u = [],
                                        f = a(.5, h) - Math.PI / 2;
                                    u.push([a(o, h) + e + .9 * i * Math.cos(f - t), a(o, h) + s + .9 * n * Math.sin(f - t)]);
                                    for (let r = f; r < 2 * Math.PI + f - .01; r += t) {
                                        const t = [a(o, h) + e + i * Math.cos(r), a(o, h) + s + n * Math.sin(r)];
                                        c.push(t), u.push(t);
                                    }
                                    return u.push([a(o, h) + e + i * Math.cos(f + 2 * Math.PI + .5 * r), a(o, h) + s + n * Math.sin(f + 2 * Math.PI + .5 * r)]), u.push([a(o, h) + e + .98 * i * Math.cos(f + r), a(o, h) + s + .98 * n * Math.sin(f + r)]), u.push([a(o, h) + e + .9 * i * Math.cos(f + .5 * r), a(o, h) + s + .9 * n * Math.sin(f + .5 * r)]), [u, c];
                                }

                                function g(t, e) {
                                    return {
                                        maxRandomnessOffset: 2,
                                        roughness: "highlight" === t ? 3 : 1.5,
                                        bowing: 1,
                                        stroke: "#000",
                                        strokeWidth: 1.5,
                                        curveTightness: 0,
                                        curveFitting: .95,
                                        curveStepCount: 9,
                                        fillStyle: "hachure",
                                        fillWeight: -1,
                                        hachureAngle: -41,
                                        hachureGap: -1,
                                        dashOffset: -1,
                                        dashGap: -1,
                                        zigzagOffset: -1,
                                        combineNestedSvgPaths: !1,
                                        disableMultiStroke: "double" !== t,
                                        disableMultiStrokeFill: !1,
                                        seed: e
                                    };
                                }

                                function d(e, r, h, a, c, u) {
                                    const f = [];
                                    let l = h.strokeWidth || 2;
                                    const d = function (t) {
                                            const e = t.padding;
                                            if (e || 0 === e) {
                                                if ("number" == typeof e) return [e, e, e, e];
                                                if (Array.isArray(e)) {
                                                    const t = e;
                                                    if (t.length) switch (t.length) {
                                                        case 4:
                                                            return [...t];
                                                        case 1:
                                                            return [t[0], t[0], t[0], t[0]];
                                                        case 2:
                                                            return [...t, ...t];
                                                        case 3:
                                                            return [...t, t[1]];
                                                        default:
                                                            return [t[0], t[1], t[2], t[3]];
                                                    }
                                                }
                                            }
                                            return [5, 5, 5, 5];
                                        }(h),
                                        p = void 0 === h.animate || !!h.animate,
                                        _ = h.iterations || 2,
                                        m = h.rtl ? 1 : 0,
                                        w = g("single", u);
                                    switch (h.type) {
                                        case "underline": {
                                            const t = r.y + r.h + d[2];
                                            for (let e = m; e < _ + m; e++) e % 2 ? f.push(s(r.x + r.w, t, r.x, t, w)) : f.push(s(r.x, t, r.x + r.w, t, w));
                                            break;
                                        }
                                        case "strike-through": {
                                            const t = r.y + r.h / 2;
                                            for (let e = m; e < _ + m; e++) e % 2 ? f.push(s(r.x + r.w, t, r.x, t, w)) : f.push(s(r.x, t, r.x + r.w, t, w));
                                            break;
                                        }
                                        case "box": {
                                            const t = r.x - d[3],
                                                e = r.y - d[0],
                                                s = r.w + (d[1] + d[3]),
                                                i = r.h + (d[0] + d[2]);
                                            for (let o = 0; o < _; o++) f.push(n(t, e, s, i, w));
                                            break;
                                        }
                                        case "bracket": {
                                            const t = Array.isArray(h.brackets) ? h.brackets : h.brackets ? [h.brackets] : ["right"],
                                                e = r.x - 2 * d[3],
                                                s = r.x + r.w + 2 * d[1],
                                                n = r.y - 2 * d[0],
                                                o = r.y + r.h + 2 * d[2];
                                            for (const h of t) {
                                                let t;
                                                switch (h) {
                                                    case "bottom":
                                                        t = [
                                                            [e, r.y + r.h],
                                                            [e, o],
                                                            [s, o],
                                                            [s, r.y + r.h]
                                                        ];
                                                        break;
                                                    case "top":
                                                        t = [
                                                            [e, r.y],
                                                            [e, n],
                                                            [s, n],
                                                            [s, r.y]
                                                        ];
                                                        break;
                                                    case "left":
                                                        t = [
                                                            [r.x, n],
                                                            [e, n],
                                                            [e, o],
                                                            [r.x, o]
                                                        ];
                                                        break;
                                                    case "right":
                                                        t = [
                                                            [r.x + r.w, n],
                                                            [s, n],
                                                            [s, o],
                                                            [r.x + r.w, o]
                                                        ];
                                                }
                                                t && f.push(i(t, !1, w));
                                            }
                                            break;
                                        }
                                        case "crossed-off": {
                                            const t = r.x,
                                                e = r.y,
                                                i = t + r.w,
                                                n = e + r.h;
                                            for (let o = m; o < _ + m; o++) o % 2 ? f.push(s(i, n, t, e, w)) : f.push(s(t, e, i, n, w));
                                            for (let o = m; o < _ + m; o++) o % 2 ? f.push(s(t, n, i, e, w)) : f.push(s(i, e, t, n, w));
                                            break;
                                        }
                                        case "circle": {
                                            const t = g("double", u),
                                                e = r.w + (d[1] + d[3]),
                                                s = r.h + (d[0] + d[2]),
                                                i = r.x - d[3] + e / 2,
                                                n = r.y - d[0] + s / 2,
                                                h = Math.floor(_ / 2),
                                                a = _ - 2 * h;
                                            for (let r = 0; r < h; r++) f.push(o(i, n, e, s, t));
                                            for (let t = 0; t < a; t++) f.push(o(i, n, e, s, w));
                                            break;
                                        }
                                        case "highlight": {
                                            const t = g("highlight", u);
                                            l = .95 * r.h;
                                            const e = r.y + r.h / 2;
                                            for (let i = m; i < _ + m; i++) i % 2 ? f.push(s(r.x + r.w, e, r.x, e, t)) : f.push(s(r.x, e, r.x + r.w, e, t));
                                            break;
                                        }
                                    }
                                    if (f.length) {
                                        const s = function (t) {
                                                const e = [];
                                                for (const s of t) {
                                                    let t = "";
                                                    for (const i of s.ops) {
                                                        const s = i.data;
                                                        switch (i.op) {
                                                            case "move":
                                                                t.trim() && e.push(t.trim()), t = `M${s[0]} ${s[1]} `;
                                                                break;
                                                            case "bcurveTo":
                                                                t += `C${s[0]} ${s[1]}, ${s[2]} ${s[3]}, ${s[4]} ${s[5]} `;
                                                                break;
                                                            case "lineTo":
                                                                t += `L${s[0]} ${s[1]} `;
                                                        }
                                                    }
                                                    t.trim() && e.push(t.trim());
                                                }
                                                return e;
                                            }(f),
                                            i = [],
                                            n = [];
                                        let o = 0;
                                        const r = (t, e, s) => t.setAttribute(e, s);
                                        for (const a of s) {
                                            const s = document.createElementNS(t, "path");
                                            if (r(s, "d", a), r(s, "fill", "none"), r(s, "stroke", h.color || "currentColor"), r(s, "stroke-width", "" + l), p) {
                                                const t = s.getTotalLength();
                                                i.push(t), o += t;
                                            }
                                            e.appendChild(s), n.push(s);
                                        }
                                        if (p) {
                                            let t = 0;
                                            for (let e = 0; e < n.length; e++) {
                                                const s = n[e],
                                                    r = i[e],
                                                    h = o ? c * (r / o) : 0,
                                                    u = a + t,
                                                    f = s.style;
                                                f.strokeDashoffset = "" + r, f.strokeDasharray = "" + r, f.animation = `rough-notation-dash ${h}ms ease-out ${u}ms forwards`, t += h;
                                            }
                                        }
                                    }
                                }
                                class p {
                                    constructor(t, e) {
                                        this._state = "unattached", this._resizing = !1, this._seed = Math.floor(Math.random() * 2 ** 31), this._lastSizes = [], this._animationDelay = 0, this._resizeListener = () => {
                                            this._resizing || (this._resizing = !0, setTimeout(() => {
                                                this._resizing = !1, "showing" === this._state && this.haveRectsChanged() && this.show();
                                            }, 400));
                                        }, this._e = t, this._config = JSON.parse(JSON.stringify(e)), this.attach();
                                    }
                                    get animate() {
                                        return this._config.animate;
                                    }
                                    set animate(t) {
                                        this._config.animate = t;
                                    }
                                    get animationDuration() {
                                        return this._config.animationDuration;
                                    }
                                    set animationDuration(t) {
                                        this._config.animationDuration = t;
                                    }
                                    get iterations() {
                                        return this._config.iterations;
                                    }
                                    set iterations(t) {
                                        this._config.iterations = t;
                                    }
                                    get color() {
                                        return this._config.color;
                                    }
                                    set color(t) {
                                        this._config.color !== t && (this._config.color = t, this.refresh());
                                    }
                                    get strokeWidth() {
                                        return this._config.strokeWidth;
                                    }
                                    set strokeWidth(t) {
                                        this._config.strokeWidth !== t && (this._config.strokeWidth = t, this.refresh());
                                    }
                                    get padding() {
                                        return this._config.padding;
                                    }
                                    set padding(t) {
                                        this._config.padding !== t && (this._config.padding = t, this.refresh());
                                    }
                                    attach() {
                                        if ("unattached" === this._state && this._e.parentElement) {
                                            ! function () {
                                                if (!window.__rno_kf_s) {
                                                    const t = window.__rno_kf_s = document.createElement("style");
                                                    t.textContent = "@keyframes rough-notation-dash { to { stroke-dashoffset: 0; } }", document.head.appendChild(t);
                                                }
                                            }();
                                            const e = this._svg = document.createElementNS(t, "svg");
                                            e.setAttribute("class", "rough-annotation");
                                            const s = e.style;
                                            s.position = "absolute", s.top = "0", s.left = "0", s.overflow = "visible", s.pointerEvents = "none", s.width = "100px", s.height = "100px";
                                            const i = "highlight" === this._config.type;
                                            if (this._e.insertAdjacentElement(i ? "beforebegin" : "afterend", e), this._state = "not-showing", i) {
                                                const t = window.getComputedStyle(this._e).position;
                                                (!t || "static" === t) && (this._e.style.position = "relative");
                                            }
                                            this.attachListeners();
                                        }
                                    }
                                    detachListeners() {
                                        window.removeEventListener("resize", this._resizeListener), this._ro && this._ro.unobserve(this._e);
                                    }
                                    attachListeners() {
                                        this.detachListeners(), window.addEventListener("resize", this._resizeListener, {
                                            passive: !0
                                        }), !this._ro && "ResizeObserver" in window && (this._ro = new window.ResizeObserver(t => {
                                            for (const e of t) e.contentRect && this._resizeListener();
                                        })), this._ro && this._ro.observe(this._e);
                                    }
                                    haveRectsChanged() {
                                        if (this._lastSizes.length) {
                                            const t = this.rects();
                                            if (t.length !== this._lastSizes.length) return !0;
                                            for (let e = 0; e < t.length; e++)
                                                if (!this.isSameRect(t[e], this._lastSizes[e])) return !0;
                                        }
                                        return !1;
                                    }
                                    isSameRect(t, e) {
                                        const s = (t, e) => Math.round(t) === Math.round(e);
                                        return s(t.x, e.x) && s(t.y, e.y) && s(t.w, e.w) && s(t.h, e.h);
                                    }
                                    isShowing() {
                                        return "not-showing" !== this._state;
                                    }
                                    refresh() {
                                        this.isShowing() && !this.pendingRefresh && (this.pendingRefresh = Promise.resolve().then(() => {
                                            this.isShowing() && this.show(), delete this.pendingRefresh;
                                        }));
                                    }
                                    show() {
                                        switch (this._state) {
                                            case "unattached":
                                                break;
                                            case "showing":
                                                this.hide(), this._svg && this.render(this._svg, !0);
                                                break;
                                            case "not-showing":
                                                this.attach(), this._svg && this.render(this._svg, !1);
                                        }
                                    }
                                    hide() {
                                        if (this._svg)
                                            for (; this._svg.lastChild;) this._svg.removeChild(this._svg.lastChild);
                                        this._state = "not-showing";
                                    }
                                    remove() {
                                        this._svg && this._svg.parentElement && this._svg.parentElement.removeChild(this._svg), this._svg = void 0, this._state = "unattached", this.detachListeners();
                                    }
                                    render(t, e) {
                                        let s = this._config;
                                        e && (s = JSON.parse(JSON.stringify(this._config)), s.animate = !1);
                                        const i = this.rects();
                                        let n = 0;
                                        i.forEach(t => n += t.w);
                                        const o = s.animationDuration || 800;
                                        let r = 0;
                                        for (let e = 0; e < i.length; e++) {
                                            const h = o * (i[e].w / n);
                                            d(t, i[e], s, r + this._animationDelay, h, this._seed), r += h;
                                        }
                                        this._lastSizes = i, this._state = "showing";
                                    }
                                    rects() {
                                        const t = [];
                                        if (this._svg)
                                            if (this._config.multiline) {
                                                const e = this._e.getClientRects();
                                                for (let s = 0; s < e.length; s++) t.push(this.svgRect(this._svg, e[s]));
                                            } else t.push(this.svgRect(this._svg, this._e.getBoundingClientRect()));
                                        return t;
                                    }
                                    svgRect(t, e) {
                                        const s = t.getBoundingClientRect(),
                                            i = e;
                                        return {
                                            x: (i.x || i.left) - (s.x || s.left),
                                            y: (i.y || i.top) - (s.y || s.top),
                                            w: i.width,
                                            h: i.height
                                        };
                                    }
                                }

                                function _(t, e) {
                                    return new p(t, e);
                                }

                                function m(t) {
                                    let e = 0;
                                    for (const s of t) {
                                        const t = s;
                                        t._animationDelay = e;
                                        e += 0 === t.animationDuration ? 0 : t.animationDuration || 800;
                                    }
                                    const s = [...t];
                                    return {
                                        show() {
                                            for (const t of s) t.show();
                                        },
                                        hide() {
                                            for (const t of s) t.hide();
                                        }
                                    };
                                }

                                var a1 = _(n1, options);
                                a1.show();
                            }, {
                                offset: element.ep_notation_waypoint_offset || 'bottom-in-view',
                                // offset: '90%'
                            });
                        }

                    }
                    
                });


            }

        });

        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(Notation, {
                $element: $scope
            });
        });

    });

}(jQuery, window.elementorFrontend));
/**
 * Start notification widget script
 */

(function ($, elementor) {

    'use strict';

    // Notification
    var widgetNotification = function ($scope, $) {

        var $avdNotification = $scope.find('.bdt-notification-wrapper'),
            $settings = $avdNotification.data('settings');

        if (!$avdNotification.length) {
            return;
        }

        if (Boolean(elementorFrontend.isEditMode()) === false) {
            if ($($scope).is('.elementor-hidden-desktop, .elementor-hidden-tablet, .elementor-hidden-mobile')) {
                return;
            }

            if ($settings.externalSystem == 'yes' && $settings.externalSystemValid == false) {
                return;
            }

            if ($settings.linkWithConfetti === true){
                jQuery.ajax({
                    type: "post",
                    dataType: "json",
                    url: ElementPackConfig.ajaxurl,
                    data: {
                        action: "ep_connect_confetti",
                        data  : 'empty'
                    },
                    success: function () {
                        //  console.log('done');
                    }
                })
            }
           
        }


        var $settings = $avdNotification.data('settings'),
            id = '#' + $settings.id,
            timeOut = $settings.notifyTimeout,
            notifyType = $settings.notifyType,
            notifyFixPos = $settings.notifyFixPosition,
            editMode = Boolean(elementorFrontend.isEditMode());


        if (typeof $settings.notifyTimeout === "undefined") {
            timeOut = null;
        }

        bdtUIkit.util.on(document, 'beforehide', '[bdt-alert]', function (event) {
            if (notifyFixPos === 'top') {
                $('html').attr('style', 'margin-top: unset !important');
            }
        });

        var notification = {
            htmlMarginRemove: function () {
                $('html').css({
                    'margin-top': 'unset  !important'
                });
            },
            appendBody: function () {
                $('body > ' + id).slice(1).remove();
                $(id).prependTo($("body"));
            },
            showNotify: function () {
                $(id).removeClass('bdt-hidden');
            },
            notifyFixed: function () {
                this.htmlMarginRemove();
                setTimeout(function () {
                    if (notifyFixPos == 'top') {
                        var notifyHeight = $('.bdt-notify-wrapper').outerHeight();
                        if ($('.admin-bar').length) {
                            notifyHeight = notifyHeight + 32;
                            $(id).attr('style', 'margin-top: 32px !important');
                        }
                        $('html').attr('style', 'margin-top: ' + notifyHeight + 'px !important');
                        $('html').css({
                            'transition': 'margin-top .8s ease'
                        });
                        $(window).on('resize', function () {
                            notifyHeight = $('.bdt-notify-wrapper').outerHeight();
                            if ($('.admin-bar').length) {
                                notifyHeight = notifyHeight + 32;
                            }
                            $('html').attr('style', 'margin-top: ' + notifyHeight + 'px !important');
                        });
                    }
                }, 1000);
            },
            notifyRelative: function () {
                $('body > ' + id).remove();
            },
            notifyPopup: function () {
                bdtUIkit.notification({
                    message: $settings.msg,
                    status: $settings.notifyStatus,
                    pos: $settings.notifyPosition,
                    timeout: timeOut
                });
            },
            notifyFire: function () {
                if (notifyType === 'fixed') {
                    if (notifyFixPos !== 'relative') {
                        this.appendBody();
                        this.notifyFixed();
                    } else {
                        this.htmlMarginRemove();
                        this.notifyRelative();
                    }
                } else {
                    this.notifyPopup();
                }
            },
            setLocalize: function () {
                if (editMode) {
                    this.clearLocalize();
                    return;
                }
                var widgetID = $settings.id,
                    localVal = 0,
                    hours = $settings.displayTimesExpire;

                var expires = (hours * 60 * 60);
                var now = Date.now();
                var schedule = now + expires * 1000;

                if (localStorage.getItem(widgetID) === null) {
                    localStorage.setItem(widgetID, localVal);
                    localStorage.setItem(widgetID + '_expiresIn', schedule);
                }
                if (localStorage.getItem(widgetID) !== null) {
                    var count = parseInt(localStorage.getItem(widgetID));
                    count++;
                    localStorage.setItem(widgetID, count);
                    // this.clearLocalize();
                }
            },
            clearLocalize: function () {
                var localizeExpiry = parseInt(localStorage.getItem($settings.id + '_expiresIn'));
                var now = Date.now(); //millisecs since epoch time, lets deal only with integer
                var schedule = now;
                if (schedule >= localizeExpiry) {
                    localStorage.removeItem($settings.id + '_expiresIn');
                    localStorage.removeItem($settings.id);
                }
            },
            notificationInit: function () {
                var init = this;

                this.setLocalize();
                var displayTimes = $settings.displayTimes,
                    firedNotify = parseInt(localStorage.getItem($settings.id));

                if ((displayTimes !== false) && (firedNotify > displayTimes)) {
                    return;
                }

                this.showNotify();

                if ($settings.notifyEvent == 'onload' || $settings.notifyEvent == 'inDelay') {
                    $(document).ready(function () {
                        setTimeout(function () {
                            init.notifyFire();
                        }, $settings.notifyInDelay);
                    });
                }
                if ($settings.notifyEvent == 'click' || $settings.notifyEvent == 'mouseover') {
                    $($settings.notifySelector).on($settings.notifyEvent, function () {
                        init.notifyFire();
                    });
                }
            },
        };

        // kick off the notification widget
        notification.notificationInit();

        $('.bdt-notify-wrapper.bdt-position-fixed .bdt-alert-close').on('click', function (e) {
            $('html').attr('style', 'margin-top: unset !important');
            if ($('.admin-bar').length) {
                $('html').attr('style', 'margin-top: 32px !important');
            }
        });
    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-notification.default', widgetNotification);
    });

}(jQuery, window.elementorFrontend));

/**
 * End notification widget script
 */
/**
 * Start offcanvas widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetOffcanvas = function( $scope, $ ) {

		var $offcanvas = $scope.find( '.bdt-offcanvas' );
			
        if ( ! $offcanvas.length ) {
            return;
        }


        $.each($offcanvas, function(index, val) {
            
            var $this   	= $(this),
                $settings   = $this.data('settings'),
                offcanvasID = $settings.id;
            
            if ( $(offcanvasID).length ) {
                // global custom link for a tag
                $(offcanvasID).on('click', function(event){
                    event.preventDefault();       
                    bdtUIkit.offcanvas( $this ).show();
                });
            }

        });

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-offcanvas.default', widgetOffcanvas );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End offcanvas widget script
 */


/**
 * Start open street map widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetOpenStreetMap = function( $scope, $ ) {

		var $openStreetMap = $scope.find( '.bdt-open-street-map' ),
            settings       = $openStreetMap.data('settings'),
            markers        = $openStreetMap.data('map_markers'),
            tileSource = '';

        if ( ! $openStreetMap.length ) {
            return;
        }

        var avdOSMap = L.map($openStreetMap[0], {
                zoomControl: settings.zoomControl,
                scrollWheelZoom: false
            }).setView([
                    settings.lat,
                    settings.lng
                ], 
                settings.zoom
            );

        if (settings.mapboxToken !== '') {
          tileSource = 'https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=' + settings.mapboxToken;
            L.tileLayer( tileSource, {
                maxZoom: 18,
                attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery &copy; <a href="https://www.mapbox.com/">Mapbox</a>',
                id: 'mapbox/streets-v11',
                tileSize: 512,
                zoomOffset: -1
            }).addTo(avdOSMap);
        } else {
            L.tileLayer( 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(avdOSMap);
        }


        for (var i in markers) { 
            if( (markers[i]['iconUrl']) != '' && typeof (markers[i]['iconUrl']) !== 'undefined'){ 
                var LeafIcon = L.Icon.extend({
                    options: {
                        iconSize   : [25, 41],
                        iconAnchor : [12, 41],
                        popupAnchor: [2, -41]
                    }
                });
                var greenIcon = new LeafIcon({iconUrl: markers[i]['iconUrl'] });
                L.marker( [markers[i]['lat'], markers[i]['lng']], {icon: greenIcon} ).bindPopup(markers[i]['infoWindow']).addTo(avdOSMap);
            } else {
                if( (markers[i]['lat']) != '' && typeof (markers[i]['lat']) !== 'undefined'){ 
                    L.marker( [markers[i]['lat'], markers[i]['lng']] ).bindPopup(markers[i]['infoWindow']).addTo(avdOSMap);
                }
            }
        }

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-open-street-map.default', widgetOpenStreetMap );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End open street map widget script
 */


/**
 * Start panel slider widget script
 */

(function ($, elementor) {

	'use strict';

	var widgetPanelSlider = function ($scope, $) {

		var $slider = $scope.find('.bdt-panel-slider');

		if (!$slider.length) {
			return;
		}

		var $sliderContainer = $slider.find('.swiper-carousel'),
			$settings = $slider.data('settings'),
			$widgetSettings = $slider.data('widget-settings');

			const Swiper = elementorFrontend.utils.swiper;
			initSwiper();
			async function initSwiper() {
				var swiper = await new Swiper($sliderContainer, $settings);

				if ($settings.pauseOnHover) {
					$($sliderContainer).hover(function () {
						(this).swiper.autoplay.stop();
					}, function () {
						(this).swiper.autoplay.start();
					});
				}
			};

		if ($widgetSettings.mouseInteractivity == true) {
			var data = $($widgetSettings.id).find('.bdt-panel-slide-item');
			$(data).each((index, element) => {
				var scene = $(element).get(0);
				var parallaxInstance = new Parallax(scene, {
					selector: '.bdt-panel-slide-thumb',
					hoverOnly: true,
					pointerEvents: true
				});
			});
		}

	};


	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-panel-slider.default', widgetPanelSlider);
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-panel-slider.bdt-middle', widgetPanelSlider);
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-panel-slider.always-visible', widgetPanelSlider);
	});

}(jQuery, window.elementorFrontend));

/**
 * End panel slider widget script
 */
;(function ($, elementor) {
    'use strict';

    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            ScrollingEffect;

        ScrollingEffect = ModuleHandler.extend({

            bindEvents: function () {
                this.run();
            },

            getDefaultSettings: function () {
                return {
                    media   : false,
                    easing  : 1,
                    viewport: 1,
                };
            },

            onElementChange: debounce(function (prop) {
                if ( prop.indexOf('ep_parallax_effects') !== -1 ) {
                    this.run();
                }
            }, 400),

            settings: function (key) {
                return this.getElementSettings('ep_parallax_effects_' + key);
            },

            run: function () {
                var options   = this.getDefaultSettings(),
                    element   = this.findElement('.elementor-widget-container').get(0);

                if ( jQuery(this.$element).hasClass('elementor-section') ) {
                    element = this.$element.get(0);
                }

                if ( this.settings('y') ) {
                    if (this.settings('y_custom_show')) {
                        options.y = this.settings('y_custom_value');
                    } else {
                        if ( this.settings('y_start.size') || this.settings('y_end.size') ) {
                            options.y = [this.settings('y_start.size') || 0, this.settings('y_end.size') || 0];

                        }
                    }
                }

                if ( this.settings('x') ) {
                    if (this.settings('x_custom_show')) {
                        options.x = this.settings('x_custom_value');
                    } else {
                        if ( this.settings('x_start.size') || this.settings('x_end.size') ) {
                            options.x = [this.settings('x_start.size'), this.settings('x_end.size')];
                        }
                    }
                }

                if ( this.settings('opacity_toggole') ) {
                    if (this.settings('opacity_custom_show')) {
                        options.opacity = this.settings('opacity_custom_value');
                    } else {
                        if ( 'htov' === this.settings('opacity') ) {
                            options.opacity = [0, 1];
                        } else if ( 'vtoh' === this.settings('opacity') ) {
                            options.opacity = [1, 0];
                        }
                    }
                }

                if ( this.settings('blur') ) {
                    if ( this.settings('blur_start.size') || this.settings('blur_end.size') ) {
                        options.blur = [this.settings('blur_start.size') || 0, this.settings('blur_end.size') || 0];
                    }
                }

                if ( this.settings('rotate') ) {
                    if ( this.settings('rotate_start.size') || this.settings('rotate_end.size') ) {
                        options.rotate = [this.settings('rotate_start.size') || 0, this.settings('rotate_end.size') || 0];
                    }
                }

                if ( this.settings('scale') ) {
                    if ( this.settings('scale_start.size') || this.settings('scale_end.size') ) {
                        options.scale = [this.settings('scale_start.size') || 1, this.settings('scale_end.size') || 1];
                    }
                }

                if ( this.settings('hue') ) {
                    if ( this.settings('hue_value.size') ) {
                        options.hue = this.settings('hue_value.size');
                    }
                }

                if ( this.settings('sepia') ) {
                    if ( this.settings('sepia_value.size') ) {
                        options.sepia = this.settings('sepia_value.size');
                    }
                }

                if ( this.settings('viewport') ) {
                    if ( this.settings('viewport_start') ) {
                        options.start = this.settings('viewport_start');
                    }
                }

                if ( this.settings('viewport') ) {
                    if ( this.settings('viewport_end') ) {
                        options.end = this.settings('viewport_end');
                    }
                }

                if ( this.settings('media_query') ) {
                    if ( this.settings('media_query') ) {
                        options.media = this.settings('media_query');
                    }
                }

                if ( this.settings('easing') ) {
                    if ( this.settings('easing_value.size') ) {
                        options.easing = this.settings('easing_value.size');
                    }
                }

                if ( this.settings('target') ) {
                    if ( this.settings('target') === 'section' ) {
                        options.target = '.elementor-section.elementor-element-' + jQuery(element).closest('section').data('id');
                    }
                }


                if ( this.settings('show') ) {
                    if (
                        this.settings('y') ||
                        this.settings('x') ||
                        this.settings('opacity') ||
                        this.settings('blur') ||
                        this.settings('rotate') ||
                        this.settings('scale') ||
                        this.settings('hue') ||
                        this.settings('sepia') ||
                        this.settings('viewport') ||
                        this.settings('media_query') ||
                        this.settings('easing') ||
                        this.settings('target')
                    ) {
                        bdtUIkit.parallax(element, options);
                    }
                }

            }
        });

        //console.log($(this.$element).hasClass("elementor-section"));

        elementorFrontend.hooks.addAction('frontend/element_ready/section', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(ScrollingEffect, { $element: $scope });
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(ScrollingEffect, { $element: $scope });
        });
    });
}(jQuery, window.elementorFrontend));
;
(function ($, elementor) {
    'use strict';
    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            Particles;

        Particles = ModuleHandler.extend({

            bindEvents: function () {
                this.run();
            },

            getDefaultSettings: function () {
                return {
                    allowHTML: true,
                };
            },

            onElementChange: debounce(function (prop) {
                if (prop.indexOf('section_particles') !== -1) {
                    // window.pJSDom[0].pJS.fn.vendors.destroypJS();
                    this.run();
                }
            }, 400),

            settings: function (key) {
                return this.getElementSettings('section_particles_' + key);
            },

            run: function () {
                var particleID = 'bdt-particle-container-' + this.$element.data('id'),
                    widgetID = this.$element.data('id'),
                    widgetContainer = $('.elementor-element-' + widgetID);
                this.particleID = particleID;

                // start predefined json data
                var jsonData = {
                    'particles': {
                        'number': {
                            'value': 80,
                            'density': {
                                'enable': true,
                                'value_area': 800
                            }
                        },
                        'color': {
                            'value': '#ffffff'
                        },
                        'shape': {
                            'type': 'circle',
                            'stroke': {
                                'width': 0,
                                'color': '#000000'
                            },
                            'polygon': {
                                'nb_sides': 5
                            },
                            'image': {
                                'src': '',
                                'width': 100,
                                'height': 100
                            }
                        },
                        'opacity': {
                            'value': 0.5,
                            'random': false,
                            'anim': {
                                'enable': false,
                                'speed': 1,
                                'opacity_min': 0.1,
                                'sync': false
                            }
                        },
                        'size': {
                            'value': 3,
                            'random': true,
                            'anim': {
                                'enable': false,
                                'speed': 40,
                                'size_min': 0.1,
                                'sync': false
                            }
                        },
                        'line_linked': {
                            'enable': true,
                            'distance': 150,
                            'color': '#ffffff',
                            'opacity': 0.4,
                            'width': 1
                        },
                        'move': {
                            'enable': true,
                            'speed': 6,
                            'direction': 'none',
                            'random': false,
                            'straight': false,
                            'out_mode': 'out',
                            'bounce': false,
                            'attract': {
                                'enable': false,
                                'rotateX': 600,
                                'rotateY': 1200
                            }
                        }
                    },
                    'interactivity': {
                        'detect_on': 'canvas',
                        'events': {
                            'onhover': {
                                'enable': false,
                                'mode': 'repulse'
                            },
                            'onclick': {
                                'enable': true,
                                'mode': 'push'
                            },
                            'resize': true
                        },
                        'modes': {
                            'grab': {
                                'distance': 400,
                                'line_linked': {
                                    'opacity': 1
                                }
                            },
                            'bubble': {
                                'distance': 400,
                                'size': 40,
                                'duration': 2,
                                'opacity': 8,
                                'speed': 3
                            },
                            'repulse': {
                                'distance': 200,
                                'duration': 0.4
                            },
                            'push': {
                                'particles_nb': 4
                            },
                            'remove': {
                                'particles_nb': 2
                            }
                        }
                    },
                    'retina_detect': true
                };
                // end of predefine json data


                if (this.settings('js') && this.settings('js').length !== 0) {
                    jsonData = JSON.parse(this.settings('js'));
                }

                if (this.settings('on')) {
                    if ($('#' + particleID).length === 0) {
                        $(widgetContainer).prepend('<div id="' + particleID + '" class="bdt-particle-container"></div>');
                    }

                    particlesJS(particleID, jsonData);

                }
            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/section', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(Particles, {
                $element: $scope
            });
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/container', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(Particles, {
                $element: $scope
            });
        });

    });

}(jQuery, window.elementorFrontend));
/**
 * Start portfolio carousel widget script
 */

(function ($, elementor) {

    'use strict';

    var widgetPortfolioCarousel = function ($scope, $) {

        var $carousel = $scope.find('.bdt-portfolio-carousel');

        if (!$carousel.length) {
            return;
        }

        var $carouselContainer = $carousel.find('.swiper-carousel'),
            $settings = $carousel.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        async function initSwiper() {
            var swiper = await new Swiper($carouselContainer, $settings);

            if ($settings.pauseOnHover) {
                $($carouselContainer).hover(function () {
                    (this).swiper.autoplay.stop();
                }, function () {
                    (this).swiper.autoplay.start();
                });
            }

        };
    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-portfolio-carousel.default', widgetPortfolioCarousel);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-portfolio-carousel.bdt-abetis', widgetPortfolioCarousel);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-portfolio-carousel.bdt-fedara', widgetPortfolioCarousel);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-portfolio-carousel.bdt-trosia', widgetPortfolioCarousel);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-portfolio-carousel.bdt-janes', widgetPortfolioCarousel);
    });

}(jQuery, window.elementorFrontend));

/**
 * End portfolio carousel widget script
 */
/**
 * Start portfolio gallery widget script
 */

(function ($, elementor) {
    'use strict';
    // PortfolioGallery
    var widgetPortfolioGallery = function ($scope, $) {
        var $portfolioGalleryWrapper = $scope.find('.bdt-portfolio-gallery-wrapper'),
            $settings = $portfolioGalleryWrapper.data('settings'),
            $portfolioFilter = $portfolioGalleryWrapper.find('.bdt-ep-grid-filters-wrapper');

        if (!$portfolioGalleryWrapper.length) {
            return;
        }

        if ($settings.tiltShow == true) {
            var elements = document.querySelectorAll($settings.id + " [data-tilt]");
            VanillaTilt.init(elements);
        }

        if (!$portfolioFilter.length) {
            return;
        }
        var $settings = $portfolioFilter.data('hash-settings');
        var activeHash = $settings.activeHash;
        var hashTopOffset = $settings.hashTopOffset;
        var hashScrollspyTime = $settings.hashScrollspyTime;

        function hashHandler($portfolioFilter, hashScrollspyTime, hashTopOffset) {
            if (window.location.hash) {
                if ($($portfolioFilter).find('[bdt-filter-control="[data-filter*=\'' + window.location.hash.substring(1) + '\']"]').length) {
                    var hashTarget = $('[bdt-filter-control="[data-filter*=\'' + window.location.hash.substring(1) + '\']"]').closest($portfolioFilter).attr('id');
                    $('html, body').animate({
                        easing: 'slow',
                        scrollTop: $('#' + hashTarget).offset().top - hashTopOffset
                    }, hashScrollspyTime, function () {
                        //#code
                    }).promise().then(function () {
                        $('[bdt-filter-control="[data-filter*=\'' + window.location.hash.substring(1) + '\']"]').trigger("click");
                    });
                }
            }
        }
        if ($settings.activeHash == 'yes') {
            $(window).on('load', function () {
                hashHandler($portfolioFilter, hashScrollspyTime = 1500, hashTopOffset);
            });
            $($portfolioFilter).find('.bdt-ep-grid-filter').off('click').on('click', function (event) {
                window.location.hash = ($.trim($(this).context.innerText.toLowerCase())).replace(/\s+/g, '-');
                // hashHandler( $portfolioFilter, hashScrollspyTime, hashTopOffset);
            });
            $(window).on('hashchange', function (e) {
                hashHandler($portfolioFilter, hashScrollspyTime, hashTopOffset);
            });
        }


        //filter item count
        var categories = {},
            category;

        var arr = [];
        var totalItem = 0;
        $($portfolioGalleryWrapper).find(".bdt-portfolio-gallery div[data-filter]").each(function (i, el) {
            category = $(el).data("filter");
            let list = category.split(/\s+/);
            $(list).each(function (i, el) {
                arr.push(el);
            });
            totalItem = totalItem + 1;
        });

        var counts = {};
        arr.forEach(function (x) {
            counts[x] = (counts[x] || 0) + 1;
        });
        //print total item result
        $($portfolioGalleryWrapper).find('.bdt-all-count').text(totalItem);
        // print results
        for (var key in counts) {
            $($portfolioGalleryWrapper).find('[data-bdt-target=' + key + '] .bdt-count').text(counts[key]);
        }


    };
    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-portfolio-gallery.default', widgetPortfolioGallery);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-portfolio-gallery.bdt-abetis', widgetPortfolioGallery);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-portfolio-gallery.bdt-fedara', widgetPortfolioGallery);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-portfolio-gallery.bdt-trosia', widgetPortfolioGallery);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-portfolio-gallery.bdt-janes', widgetPortfolioGallery);
    });
}(jQuery, window.elementorFrontend));

/**
 * End portfolio gallery widget script
 */
/**
 * Start Post Gallery widget script
 */

(function ($, elementor) {
    'use strict';

    var widgetPostGallery = function ($scope, $) {
        var $postGalleryWrapper = $scope.find('.bdt-post-gallery-wrapper'),
            $bdtPostGallery = $scope.find('.bdt-post-gallery'),
            $settings = $bdtPostGallery.data('settings'),
            $postFilter = $postGalleryWrapper.find('.bdt-ep-grid-filters-wrapper');

        if (!$postGalleryWrapper.length) {
            return;
        }

        if ($settings.tiltShow == true) {
            var elements = document.querySelectorAll($settings.id + " [data-tilt]");
            VanillaTilt.init(elements);
        }

        if (!$postFilter.length) {
            return;
        }
        var $settings = $postFilter.data('hash-settings');
        var activeHash = $settings.activeHash;
        var hashTopOffset = $settings.hashTopOffset;
        var hashScrollspyTime = $settings.hashScrollspyTime;

        function hashHandler($postFilter, hashScrollspyTime, hashTopOffset) {
            if (window.location.hash) {
                if ($($postFilter).find('[data-bdt-filter-control="[data-filter*=\'' + window.location.hash.substring(1) + '\']"]').length) {
                    var hashTarget = $('[data-bdt-filter-control="[data-filter*=\'' + window.location.hash.substring(1) + '\']"]').closest($postFilter).attr('id');

                    $('html, body').animate({
                        easing: 'slow',
                        scrollTop: $('#' + hashTarget).offset().top - hashTopOffset
                    }, hashScrollspyTime, function () {
                        //#code
                    }).promise().then(function () {
                        bdtUIkit.filter($postGalleryWrapper, {
                            target: $settings.id,
                            selActive: document.querySelector('[data-bdt-filter-control="[data-filter*=\'' + window.location.hash.substring(1) + '\']"]'),
                        });
                    });
                }
            }
        }

        if ($settings.activeHash == true) {
            $(window).on('load', function () {
                hashHandler($postFilter, hashScrollspyTime = 1500, hashTopOffset);
            });

            $($postFilter).find('.bdt-ep-grid-filter').off('click').on('click', function (event) {
                window.location.hash = $(this).find('a').text().replace(/\s+/g, '-').toLowerCase();
            });
        }
    };
    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-post-gallery.default', widgetPostGallery);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-post-gallery.bdt-abetis', widgetPostGallery);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-post-gallery.bdt-fedara', widgetPostGallery);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-post-gallery.bdt-trosia', widgetPostGallery);
    });
}(jQuery, window.elementorFrontend));

/**
 * End Post Gallery widget script
 */
/**
 * Start post grid tab widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetPostGridTab = function( $scope, $ ) {

		var $postGridTab = $scope.find( '.bdt-post-grid-tab' ),
			gridTab      = $postGridTab.find('.gridtab');

		if ( ! $postGridTab.length ) {
			return;
		}

		$(gridTab).gridtab($postGridTab.data('settings'));

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-post-grid-tab.default', widgetPostGridTab );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End post grid tab widget script
 */

 
/**
 * Start price table widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetPriceTable = function( $scope, $ ) {

		var $priceTable = $scope.find( '.bdt-price-table' ),
            $featuresList = $priceTable.find( '.bdt-price-table-feature-inner' );

        if ( ! $priceTable.length ) {
            return;
        }

        var $tooltip = $featuresList.find('> .bdt-tippy-tooltip'),
        	widgetID = $scope.data('id');
		
		$tooltip.each( function( index ) {
			tippy( this, {
				allowHTML: true,
				theme: 'bdt-tippy-' + widgetID
			});				
		});

    };
    

	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-price-table.default', widgetPriceTable );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-price-table.bdt-partait', widgetPriceTable );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End price table widget script
 */


/**
 * Start progress pie widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetProgressPie = function( $scope, $ ) {

		var $progressPie = $scope.find( '.bdt-progress-pie' );

        if ( ! $progressPie.length ) {
            return;
        }

        elementorFrontend.waypoint( $progressPie, function() {
            var $this = $( this );
            
                $this.asPieProgress({
                    namespace: 'pieProgress',
                    classes: {
                        svg     : 'bdt-progress-pie-svg',
                        number  : 'bdt-progress-pie-number',
                        content : 'bdt-progress-pie-content'
                    }
                });
                
                $this.asPieProgress('start');

        }, {
            offset: 'bottom-in-view'
        } );

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-progress-pie.default', widgetProgressPie );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End progress pie widget script
 */


/**
 * Start qrcode widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetQRCode = function( $scope, $ ) {

		var $qrcode = $scope.find( '.bdt-qrcode' ),
            image   = $scope.find( '.bdt-qrcode-image' );

        if ( ! $qrcode.length ) {
            return;
        }
        var settings = $qrcode.data('settings');
            settings.image = image[0];

        $($qrcode).qrcode(settings);

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-qrcode.default', widgetQRCode );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End qrcode widget script
 */


/**
 * Start reading progress widget script
 */

 (function($, elementor) {

    'use strict';

    var readingProgressWidget = function($scope, $) {

        var $readingProgress = $scope.find('.bdt-reading-progress');

        if (!$readingProgress.length) {
            return;
        }
        var $settings = $readingProgress.data('settings');

        jQuery(document).ready(function(){
            // jQuery($readingProgress).progressScroll([$settings.progress_bg, $settings.scroll_bg]); 
            var settings = {
                borderSize: 10,
                mainBgColor: '#E6F4F7',
                lightBorderColor: '#A2ECFB',
                darkBorderColor: '#39B4CC'
            };

            var colorBg = $settings.progress_bg;  //'red'
            var progressColor = $settings.scroll_bg; //'green';
            var innerHeight, offsetHeight, netHeight,
            self = this,
            container = $($readingProgress),
            borderContainer = 'bdt-reading-progress-border',
            circleContainer = 'bdt-reading-progress-circle',
            textContainer = 'bdt-reading-progress-text';

            var getHeight = function () {
                innerHeight = window.innerHeight;
                offsetHeight = document.body.offsetHeight;
                netHeight = offsetHeight - innerHeight;
            };

            var addEvent = function () {
                var e = document.createEvent('Event');
                e.initEvent('scroll', false, false);
                window.dispatchEvent(e);
            };
            var updateProgress = function (percnt) {
                var per = Math.round(100 * percnt);
                var deg = per * 360 / 100;
                if (deg <= 180) {
                    $('.' + borderContainer, container).css('background-image', 'linear-gradient(' + (90 + deg) + 'deg, transparent 50%, ' + colorBg + ' 50%),linear-gradient(90deg, ' + colorBg + ' 50%, transparent 50%)');
                } else {
                    $('.' + borderContainer, container).css('background-image', 'linear-gradient(' + (deg - 90) + 'deg, transparent 50%, ' + progressColor + ' 50%),linear-gradient(90deg, ' + colorBg + ' 50%, transparent 50%)');
                }
                $('.' + textContainer, container).text(per + '%');
            };
            var prepare = function () {
                    //$(container).addClass("bdt-reading-progress");
                    $(container).html("<div class='" + borderContainer + "'><div class='" + circleContainer + "'><span class='" + textContainer + "'></span></div></div>");

                    $('.' + borderContainer, container).css({
                        'background-color': progressColor,
                        'background-image': 'linear-gradient(91deg, transparent 50%,' + settings.lightBorderColor + '50%), linear-gradient(90deg,' + settings.lightBorderColor + '50%, transparent 50%'
                    });
                    $('.' + circleContainer, container).css({
                        'width': settings.width - settings.borderSize,
                        'height': settings.height - settings.borderSize
                    });

                };
            var init = function () {
                    prepare();
                    $(window).on('scroll', function () {
                        var getOffset = window.pageYOffset || document.documentElement.scrollTop,
                        per = Math.max(0, Math.min(1, getOffset / netHeight));
                        updateProgress(per);
                    });
                    $(window).on('resize', function () {
                        getHeight();
                        addEvent();
                    });
                    $(window).on('load', function () {
                        getHeight();
                        addEvent();
                    });
                };
                 init();
            });

    };
    //	start progress with cursor
    var readingProgressCursorSkin = function($scope, $) {

        var $readingProgress = $scope.find('.bdt-progress-with-cursor');

        if (!$readingProgress.length) {
            return;
        }

        document.getElementsByTagName('body')[0].addEventListener('mousemove', function(n) {
            t.style.left = n.clientX + 'px';
            t.style.top = n.clientY + 'px';
            e.style.left = n.clientX + 'px';
            e.style.top = n.clientY + 'px';
            i.style.left = n.clientX + 'px';
            i.style.top = n.clientY + 'px';
        });
        var t = document.querySelector('.bdt-cursor'),
        e = document.querySelector('.bdt-cursor2'),
        i = document.querySelector('.bdt-cursor3');

        function n(t) {
            e.classList.add('hover'), i.classList.add('hover');
        }

        function s(t) {
            e.classList.remove('hover'), i.classList.remove('hover');
        }
        s();
        for (var r = document.querySelectorAll('.hover-target'), a = r.length - 1; a >= 0; a--) {
            o(r[a]);
        }

        function o(t) {
            t.addEventListener('mouseover', n);
            t.addEventListener('mouseout', s);
        }

        $(document).ready(function() {


            //Scroll indicator
            var progressPath = document.querySelector('.bdt-progress-wrap path');
            var pathLength = progressPath.getTotalLength();
            progressPath.style.transition = progressPath.style.WebkitTransition = 'none';
            progressPath.style.strokeDasharray = pathLength + ' ' + pathLength;
            progressPath.style.strokeDashoffset = pathLength;
            progressPath.getBoundingClientRect();
            progressPath.style.transition = progressPath.style.WebkitTransition = 'stroke-dashoffset 10ms linear';
            var updateProgress = function() {
                var scroll = $(window).scrollTop();
                var height = $(document).height() - $(window).height();
                var progress = pathLength - (scroll * pathLength / height);
                progressPath.style.strokeDashoffset = progress;
            };
            updateProgress();
            jQuery(window).on('scroll', updateProgress);


        });

    };
    //	end  progress with cursor

    // start progress horizontal 


    var readingProgressHorizontalSkin = function($scope, $) {

        var $readingProgress = $scope.find('.bdt-horizontal-progress');

        if (!$readingProgress.length) {
            return;
        }

        $('#bdt-progress').progress({ size: '3px', wapperBg: '#eee', innerBg: '#DA4453' });

    };

    // end progress horizontal 

    // start  progress back to top 


    var readingProgressBackToTopSkin = function($scope, $) {

        var $readingProgress = $scope.find('.bdt-progress-with-top');

        if (!$readingProgress.length) {
            return;
        }

        var progressPath = document.querySelector('.bdt-progress-wrap path');
        var pathLength = progressPath.getTotalLength();
        progressPath.style.transition = progressPath.style.WebkitTransition = 'none';
        progressPath.style.strokeDasharray = pathLength + ' ' + pathLength;
        progressPath.style.strokeDashoffset = pathLength;
        progressPath.getBoundingClientRect();
        progressPath.style.transition = progressPath.style.WebkitTransition = 'stroke-dashoffset 10ms linear';
        var updateProgress = function() {
            var scroll = jQuery(window).scrollTop();
            var height = jQuery(document).height() - jQuery(window).height();
            var progress = pathLength - (scroll * pathLength / height);
            progressPath.style.strokeDashoffset = progress;
        };
        updateProgress();
        jQuery(window).on('scroll', updateProgress);
        var offset = 50;
        var duration = 550;
        jQuery(window).on('scroll', function() {
            if (jQuery(this).scrollTop() > offset) {
                jQuery('.bdt-progress-wrap').addClass('active-progress');
            } else {
                jQuery('.bdt-progress-wrap').removeClass('active-progress');
            }
        });
        jQuery('.bdt-progress-wrap').on('click', function(event) {
            event.preventDefault();
            jQuery('html, body').animate({ scrollTop: 0 }, duration);
            return false;
        });


    };

    // end progress back to top

    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-reading-progress.default', readingProgressWidget);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-reading-progress.bdt-progress-with-cursor', readingProgressCursorSkin);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-reading-progress.bdt-horizontal-progress', readingProgressHorizontalSkin);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-reading-progress.bdt-back-to-top-with-progress', readingProgressBackToTopSkin);
    });

}(jQuery, window.elementorFrontend));

/**
 * End reading progress widget script
 */


(function ($, elementor) {
  $(window).on("elementor/frontend/init", function () {
    let ModuleHandler = elementorModules.frontend.handlers.Base,
      ReadingTimer;

    ReadingTimer = ModuleHandler.extend({
      bindEvents: function () {
        this.run();
      },
      getDefaultSettings: function () {
        return {
          allowHTML: true,
        };
      },

      settings: function (key) {
        return this.getElementSettings("reading_timer_" + key);
      },

      calculateReadingTime: function (ReadingContent) {
        let wordCount = ReadingContent.split(/\s+/).filter(function (word) {
            return word !== "";
          }).length,
          averageReadingSpeed = this.settings("avg_words_per_minute")
            ? this.settings("avg_words_per_minute").size
            : 200,
          readingTime = Math.floor(wordCount / averageReadingSpeed),
          reading_seconds = Math.floor(
            (wordCount % averageReadingSpeed) / (averageReadingSpeed / 60)
          ),
          minText = this.settings("minute_text")
            ? this.settings("minute_text")
            : "min read",
          secText = this.settings("seconds_text")
            ? this.settings("seconds_text")
            : "sec read";

        if (wordCount >= averageReadingSpeed) {
          return `${readingTime} ${minText}`;
        } else {
          return `${reading_seconds} ${secText}`;
        }
      },

      run: function () {
        const widgetID = this.$element.data("id"),
          widgetContainer = `.elementor-element-${widgetID} .bdt-reading-timer`,
          contentSelector = this.settings("content_id");
        let minText = this.settings("minute_text")
          ? this.settings("minute_text")
          : "min read";

        var editMode = Boolean(elementorFrontend.isEditMode());
        if (editMode) {
          $(widgetContainer).append("2 " + minText + "");
          return;
        }
        if (contentSelector) {
          ReadingContent = $(document).find(`#${contentSelector}`).text();
          var readTime = this.calculateReadingTime(ReadingContent);
          $(widgetContainer).append(readTime);
        } else return;
      },
    });

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/bdt-reading-timer.default",
      function ($scope) {
        elementorFrontend.elementsHandler.addHandler(ReadingTimer, {
          $element: $scope,
        });
      }
    );
  });
})(jQuery, window.elementorFrontend);

(function ($, elementor) {

    'use strict';
    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            RevealFX;

        RevealFX = ModuleHandler.extend({
            bindEvents: function () {
                this.run();
            },
            // onElementChange: debounce(function (prop) {
            //     if (prop.indexOf('element_pack_reveal_effects_') !== -1) {
            //         this.run();
            //     }
            // }, 1000),

            settings: function (key) {
                return this.getElementSettings('element_pack_reveal_effects_' + key);
            },
            run     : function () {

                if ( 'yes' !== this.settings('enable') ) {
                    return;
                }

                var options         = this.getDefaultSettings(),
                    widgetID        = this.$element.data('id'),
                    widgetContainer = $('.elementor-element-' + widgetID).find('.elementor-widget-container');

                $(widgetContainer).attr('data-ep-reveal', 'ep-reveal-' + widgetID + '');

                const revealID      = '*[data-ep-reveal="ep-reveal-' + widgetID + '"]';
                const revealWrapper = document.querySelector(revealID);
                const revealFX          = new RevealFx(revealWrapper, {
                    revealSettings: {
                        bgColors : this.settings('color') ? [this.settings('color')] : ['#333'],
                        direction: this.settings('direction') ? String(this.settings('direction')) : String('c'),
                        duration : this.settings('speed') ? Number(this.settings('speed.size') * 100) : Number(500),
                        easing   : this.settings('easing') ? String(this.settings('easing')) : String('easeOutQuint'),
                        onHalfway: function (contentEl, ngsrevealerEl) {
                            contentEl.style.opacity = 1;
                        }
                    }
                });
                var runReveal       = function () {
                    revealFX.reveal();
                    this.destroy();
                };
                var waypoint        = new Waypoint({
                    element: revealWrapper,
                    handler: runReveal,
                    offset: 'bottom-in-view',
                });
            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(RevealFX, {
                $element: $scope
            });
        });
    });

}(jQuery, window.elementorFrontend));
(function ($, elementor) {
    'use strict';
    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            RippleEffects;

        RippleEffects = ModuleHandler.extend({
            bindEvents: function () {
                this.run();
            },
            getDefaultSettings: function () {
                return {
                    // debug: true,
                    multi: true,
                };
            },
            onElementChange: debounce(function (prop) {
                if (prop.indexOf('ep_ripple_') !== -1) {
                    this.run();
                }
            }, 400),
            settings: function (key) {
                return this.getElementSettings('ep_ripple_' + key);
            },
            run: function () {
                if (this.settings('enable') !== 'yes') {
                    return;
                }

                var $element = this.$element,
                    options = this.getDefaultSettings(),
                    $widgetId = 'ep-' + this.getID(),
                    $widgetClassSelect = '.elementor-element-' + this.getID(),
                    $selector = '';

                if (this.settings('selector') === 'widgets') {
                    $selector = $widgetClassSelect + ' .elementor-widget-container';
                }
                if (this.settings('selector') === 'images') {
                    $selector = $widgetClassSelect + ' img';
                }
                if (this.settings('selector') === 'buttons') {
                    $selector = $widgetClassSelect + ' a';
                }
                if (this.settings('selector') === 'both') {
                    $selector = $widgetClassSelect + ' a,' + $widgetClassSelect + ' img';
                }
                if (this.settings('selector') === 'custom' && this.settings('custom_selector')) {
                    $selector = $widgetClassSelect + ' ' + this.settings('custom_selector');
                }

                if ('' === $selector ) {
                    return;
                }

                $(document).on('click', '[href="#"]', function (e) { e.preventDefault(); });
                if (this.settings('on')) {
                    options.on = this.settings('on');
                }
                if (this.settings('easing')) {
                    options.easing = this.settings('easing');
                }
                if (this.settings('duration.size')) {
                    options.duration = this.settings('duration.size');
                }
                if (this.settings('opacity.size')) {
                    options.opacity = this.settings('opacity.size');
                }
                if (this.settings('color')) {
                    options.color = this.settings('color');
                }

                document.querySelectorAll($selector).forEach(function (el) {
                    if ('IMG' == el.tagName) {
                        var $image = $(el);
                        $image.wrap('<div id="bdt-ripple-effect-img-wrapper-' + $widgetId + '"></div>');
                        window.rippler = $.ripple('#bdt-ripple-effect-img-wrapper-' + $widgetId, options);
                    } else {
                        window.rippler = $.ripple($selector, options);
                    }
                });
            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(RippleEffects, {
                $element: $scope
            });
        });
    });

}(jQuery, window.elementorFrontend));
/**
 * Start twitter carousel widget script
 */

(function ($, elementor) {

	'use strict';

	var widgetReviewCardCarousel = function ($scope, $) {

		var $reviewCardCarousel = $scope.find('.bdt-review-card-carousel');

		if (!$reviewCardCarousel.length) {
			return;
		}

		var $reviewCardCarouselContainer = $reviewCardCarousel.find('.swiper-carousel'),
			$settings = $reviewCardCarousel.data('settings');

		const Swiper = elementorFrontend.utils.swiper;
		initSwiper();
		async function initSwiper() {
			var swiper = await new Swiper($reviewCardCarouselContainer, $settings); // this is an example

			if ($settings.pauseOnHover) {
				$($reviewCardCarouselContainer).hover(function () {
					(this).swiper.autoplay.stop();
				}, function () {
					(this).swiper.autoplay.start();
				});
			}

		};


	};


	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-review-card-carousel.default', widgetReviewCardCarousel);
	});

}(jQuery, window.elementorFrontend));

/**
 * End twitter carousel widget script
 */


/**
 * Start scroll button widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetScrollButton = function( $scope, $ ) {
	    
			var $scrollButton = $scope.find('.bdt-scroll-button'),
			$selector         = $scrollButton.data('selector'),
			$settings         =  $scrollButton.data('settings');

	    if ( ! $scrollButton.length ) {
	    	return;
	    }

	    //$($scrollButton).find('.bdt-scroll-button').unbind();
	    
	    if ($settings.HideOnBeforeScrolling == true) {

			$(window).scroll(function() {
			  if ($(window).scrollTop() > 300) {
			    $scrollButton.css("opacity", "1");
			  } else {
			    $scrollButton.css("opacity", "0");
			  }
			});
	    }

	    $($scrollButton).on('click', function(event){
	    	event.preventDefault();
	    	bdtUIkit.scroll($scrollButton, $settings ).scrollTo($($selector));

	    });

	};

	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-scroll-button.default', widgetScrollButton );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End scroll button widget script
 */


/**
 * Start scrollnav widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetScrollNav = function( $scope, $ ) {

		var $scrollnav = $scope.find( '.bdt-dotnav > li' );

        if ( ! $scrollnav.length ) {
            return;
        }

		var $tooltip = $scrollnav.find('> .bdt-tippy-tooltip'),
			widgetID = $scope.data('id');
		
		$tooltip.each( function( index ) {
			tippy( this, {
				allowHTML: true,
				theme: 'bdt-tippy-' + widgetID
			});				
		});

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-scrollnav.default', widgetScrollNav );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End scrollnav widget script
 */


/**
 * Start search widget script
 */

(function ($, elementor) {
  'use strict';
  var serachTimer;
  var widgetAjaxSearch = function ($scope, $) {
    var $searchContainer = $scope.find('.bdt-search-container'),
      $searchWidget = $scope.find('.bdt-ajax-search');

    let $search;

    if (!$searchWidget.length) {
      return;
    }

    var $resultHolder = $($searchWidget).find('.bdt-search-result'),
      $settings = $($searchWidget).data('settings'),
      $connectSettings = $($searchContainer).data('settings'),
      $target = $($searchWidget).attr('anchor-target');

    if ('yes' === $target) {
      $target = '_blank';
    } else {
      $target = '_self';
    }

    clearTimeout(serachTimer);

    if ($connectSettings && $connectSettings.element_connect) {
      $($connectSettings.element_selector).hide();
    }

    $($searchWidget).on('keyup keypress', function (e) {
      var keyCode = e.keyCode || e.which;
      if (keyCode === 13) {
        e.preventDefault();
        return false;
      }
    });

    $searchWidget.find('.bdt-search-input').keyup(function () {
      $search = $(this).val();
      serachTimer = setTimeout(function () {
        $($searchWidget).addClass('bdt-search-loading');
        jQuery.ajax({
          url: window.ElementPackConfig.ajaxurl,
          type: 'post',
          data: {
            action: 'element_pack_search',
            s: $search,
            settings: $settings,
          },
          success: function (response) {
            var response = $.parseJSON(response);

            if (response.results.length > 0) {
              if ($search.length >= 3) {
                var output = `<div class="bdt-search-result-inner">
                          <h3 class="bdt-search-result-header">${window.ElementPackConfig.search.search_result}<i class="ep-icon-close bdt-search-result-close-btn"></i></h3>
                          <ul class="bdt-list bdt-list-divider">`;
                for (let i = 0; i < response.results.length; i++) {
                  const element = response.results[i];
                  output += `<li class="bdt-search-item" data-url="${element.url}">
                            <a href="${element.url}" target="${$target}">
                            <div class="bdt-search-title">${element.title}</div>
                            <div class="bdt-search-text">${element.text}</div>
                            </a>
                          </li>`;
                }
                output += `</ul><a class="bdt-search-more">${window.ElementPackConfig.search.more_result}</a></div>`;

                $resultHolder.html(output);
                $resultHolder.show();
                $(".bdt-search-result-close-btn").on("click", function (e) {
                  $(".bdt-search-result").hide();
                  $(".bdt-search-input").val("");
                });

                $($searchWidget).removeClass("bdt-search-loading");
                $(".bdt-search-more").on("click", function (event) {
                  event.preventDefault();
                  $($searchWidget).submit();
                });
              } else {
                $resultHolder.hide();
              }
            } else {
              if ($search.length > 3) {
                var not_found = `<div class="bdt-search-result-inner">
                                  <h3 class="bdt-search-result-header">${window.ElementPackConfig.search.search_result}<i class="ep-icon-close bdt-search-result-close-btn"></i></h3>
                                  <div class="bdt-search-text">${$search} ${window.ElementPackConfig.search.not_found}</div>
                                </div>`;
                $resultHolder.html(not_found);
                $resultHolder.show();
                $(".bdt-search-result-close-btn").on("click", function (e) {
                  $(".bdt-search-result").hide();
                  $(".bdt-search-input").val("");
                });
                $($searchWidget).removeClass("bdt-search-loading");

                if ($connectSettings && $connectSettings.element_connect) {
                  $resultHolder.hide();
                  setTimeout(function () {
                    $($connectSettings.element_selector).show();
                  }, 1500);
                }

              } else {
                $resultHolder.hide();
                $($searchWidget).removeClass("bdt-search-loading");
              }

            }
          }
        });
      }, 450);
    });

  };


  jQuery(window).on('elementor/frontend/init', function () {
    elementorFrontend.hooks.addAction('frontend/element_ready/bdt-search.default', widgetAjaxSearch);
  });

  //window.elementPackAjaxSearch = widgetAjaxSearch;

})(jQuery, window.elementorFrontend);

/**
 * End search widget script
 */
/**
 * Start section sticky widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetSectionSticky = function( $scope, $ ) {

        var $section   = $scope;

        //sticky fixes for inner section.
        jQuery($section).each(function( index ) {
            var $sticky      = jQuery(this),
                $stickyFound = $sticky.find('.elementor-inner-section.bdt-sticky');

            if ($stickyFound.length) {
                jQuery($stickyFound).wrap('<div class="bdt-sticky-wrapper"></div>');
            }
        });

	};


	jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction( 'frontend/element_ready/section', widgetSectionSticky );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End section sticky widget script
 */


/**
 * Start slider widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetSlider = function( $scope, $ ) {

		var $slider = $scope.find( '.bdt-slider' );
				
        if ( ! $slider.length ) {
            return;
        }

        var $sliderContainer = $slider.find('.swiper-carousel'),
			$settings 		 = $slider.data('settings');

		// Access swiper class
        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        
        async function initSwiper() {

			var swiper = await new Swiper($sliderContainer, $settings);

			if ($settings.pauseOnHover) {
				 $($sliderContainer).hover(function() {
					(this).swiper.autoplay.stop();
				}, function() {
					(this).swiper.autoplay.start();
				});
			}
		};

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-slider.default', widgetSlider );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End slider widget script
 */


/**
 * Start slideshow widget script
 */

(function($, elementor) {

    'use strict';

    var widgetSlideshow = function($scope, $) {

        var $slideshow = $scope.find( '.bdt-slideshow' ),
            $thumbNav  = $($slideshow).find('.bdt-thumbnav-wrapper > .bdt-thumbnav-scroller');

        if ( ! $slideshow.length ) {
            return;
        }

        $($thumbNav).mThumbnailScroller({
            axis: 'yx',
            type: 'hover-precise'
        });

    };


    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-slideshow.default', widgetSlideshow);
    });

}(jQuery, window.elementorFrontend));

/**
 * End slideshow widget script
 */


/**
 * Start vertical menu widget script
 */

(function ($, elementor) {
    'use strict';
    // Horizontal Menu
    var widgetSlinkyVerticalMenu = function ($scope, $) {
        var $vrMenu = $scope.find('.bdt-slinky-vertical-menu');
        var $settings = $vrMenu.attr('id');
        if (!$vrMenu.length) {
            return;
        }
        const slinky = $('#'+$settings).slinky();
    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-slinky-vertical-menu.default', widgetSlinkyVerticalMenu);
    });

}(jQuery, window.elementorFrontend));

/**
 * End vertical menu widget script
 */


;
(function ($, elementor) {
    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            SoundEffects;

        SoundEffects = ModuleHandler.extend({

            bindEvents: function () {
                this.run();
            },

            getDefaultSettings: function () {
                return {
                    event: 'hover',
                };
            },

            onElementChange: debounce(function (prop) {
                if (prop.indexOf('ep_sound_effects_') !== -1) {
                    this.run();
                }
            }, 400),

            settings: function (key) {
                return this.getElementSettings('ep_sound_effects_' + key);
            },

            run: function () {
                if (this.settings('active') != 'yes') {
                    return;
                }
                var options = this.getDefaultSettings(),
                    $element = '',
                    $widgetId = 'ep-sound-effects' + this.getID(),
                    $widgetIdSelect = '#' + $widgetId,
                    $soundAudioSource, $soundAudioSourceMp3;

                // selector creating...
                $(this.findElement('.elementor-widget-container').get(0)).attr('id', $widgetId);

                if (this.settings('select_type') === 'widget') {
                    $element = $($widgetIdSelect);
                } else if (this.settings('select_type') === 'anchor_tag') {
                    $element = $($widgetIdSelect + ' a');
                } else if (this.settings('select_type') === 'custom') {
                    if (this.settings('element_selector')) {
                        $element = $($widgetIdSelect).find(this.settings('element_selector'));
                    }
                } else {
                    // nothing...
                }

                // audio source

                if (this.settings('source') !== 'hosted_url') {
                    $soundAudioSource = this.settings('source_local_link') + this.settings('source');
                } else {
                    if (this.settings('hosted_url.url')) {
                        $soundAudioSource = this.settings('hosted_url.url').replace(/\.[^/.]+$/, "");
                        $soundAudioSourceMp3 = this.settings('hosted_url_mp3.url').replace(/\.[^/.]+$/, "");
                    }
                }

                if (!$soundAudioSource) {
                    return;
                }

                if (!document.createElement('audio').canPlayType) {
                    console.error('Oh man 😩! \nYour browser doesn\'t support audio awesomeness.');
                    return function () {}; // return an empty function if `loudLinks` is called again.
                }

                // Create audio element and make it awesome
                var audioPlayer = document.createElement('audio'),
                    mp3Source = document.createElement('source'),
                    oggSource = document.createElement('source'),
                    eventsSet = false;

                audioPlayer.setAttribute('preload', true); // audio element preload attribute

                mp3Source.setAttribute('type', 'audio/mpeg');
                oggSource.setAttribute('type', 'audio/ogg');

                // appending the sources to the player element
                audioPlayer.appendChild(mp3Source);
                audioPlayer.appendChild(oggSource);

                // appending audioplayer to body
                document.body.appendChild(audioPlayer);

                // Play audio
                function playAudio() {

                    // get the audio source and appending it to <audio>
                    var audioSrc = $soundAudioSource, //'http://192.168.1.100/cat-meow',
                        soundMp3Link,
                        soundOggLink;

                    if (!audioSrc) {
                        return;
                    }

                    if ($soundAudioSourceMp3) {
                        soundMp3Link = $soundAudioSourceMp3 + '.mp3';
                    }

                    soundOggLink = audioSrc + '.ogg';

                    if (!eventsSet) {
                        eventsSet = true;
                        // mp3Source.addEventListener('error', function () {
                        //     console.error('😶 D\'oh! The mp3 file URL is wrong!');
                        // });
                        oggSource.addEventListener('error', function () {
                            console.error('😶 D\'oh! The ogg file URL is wrong!');
                        });
                    }

                    // Only reset `src` and reload if source is different
                    if (soundMp3Link || soundOggLink) {
                        if ($soundAudioSourceMp3) {
                            mp3Source.setAttribute('src', soundMp3Link);
                        }
                        oggSource.setAttribute('src', soundOggLink);

                        audioPlayer.load();

                    }

                    audioPlayer.play();

                }

                // Stop audio
                function stopAudio() {
                    audioPlayer.pause();
                    audioPlayer.currentTime = 0; // reset to beginning
                }

                var initScope = this;

                jQuery(document).ready(function () {
                    //hover
                    if (initScope.settings('event') == 'hover') {

                        jQuery($element).on('mouseenter', function () {
                            playAudio();
                        });
                        jQuery($element).on('mouseleave', function () {
                            stopAudio();
                        });
                        // jQuery($element).on('touchmove', function () {
                        //     stopAudio();
                        // });
                        jQuery($element).on('click', function () {
                            stopAudio();
                        });

                        jQuery($element).on('touchstart', function () {
                            playAudio();
                        });

                    }

                    // click
                    if (initScope.settings('event') == 'click') {
                        $($element).on('click', function () {
                            playAudio();
                        });
                    }

                });

            }

        });

        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(SoundEffects, {
                $element: $scope
            });
        });

    });

})(jQuery, window.elementorFrontend);
/**
 * Start source code widget script
 */

( function( $, elementor ) {

    'use strict';

    var sourceCodeWidget = function( $scope, $ ) {
        var $sourceCode = $scope.find('.bdt-source-code'),
            $preCode = $sourceCode.find('pre > code');

        if ( ! $sourceCode.length ) {
            return;
        }

        // create clipboard for every copy element
        var clipboard = new ClipboardJS('.bdt-copy-button', {
            target: function target(trigger) {
                return trigger.nextElementSibling;
            }
        });

        // do stuff when copy is clicked
        clipboard.on('success', function (event) {
            event.trigger.textContent = 'copied!';
            setTimeout(function () {
                event.clearSelection();
                event.trigger.textContent = 'copy';
            }, 2000);
        });

        //if ($lng_type !== undefined && $code !== undefined) {
            Prism.highlightElement($preCode.get(0));
       // }

    };

    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-source-code.default', sourceCodeWidget );
    });

}( jQuery, window.elementorFrontend ) );

/**
 * End source code widget script
 */


/**
 * Start twitter carousel widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetProductCarousel = function( $scope, $ ) {

		var $ProductCarousel = $scope.find( '.bdt-ep-product-carousel' );
				
        if ( ! $ProductCarousel.length ) {
            return;
        }

		var $ProductCarouselContainer = $ProductCarousel.find('.swiper-carousel'),
			$settings 		 = $ProductCarousel.data('settings');

		// Access swiper class
        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        
        async function initSwiper() {

			var swiper = await new Swiper($ProductCarouselContainer, $settings);

			if ($settings.pauseOnHover) {
				 $($ProductCarouselContainer).hover(function() {
					(this).swiper.autoplay.stop();
				}, function() {
					(this).swiper.autoplay.start();
				});
			}
		};

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-product-carousel.default', widgetProductCarousel );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End twitter carousel widget script
 */


/**
 * Start twitter carousel widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetStaticCarousel = function( $scope, $ ) {

		var $StaticCarousel = $scope.find( '.bdt-static-carousel' );
				
        if ( ! $StaticCarousel.length ) {
            return;
        }

		var $StaticCarouselContainer = $StaticCarousel.find('.swiper-carousel'),
			$settings 		 = $StaticCarousel.data('settings');

		// Access swiper class
        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        
        async function initSwiper() {

			var swiper = await new Swiper($StaticCarouselContainer, $settings);

			if ($settings.pauseOnHover) {
				 $($StaticCarouselContainer).hover(function() {
					(this).swiper.autoplay.stop();
				}, function() {
					(this).swiper.autoplay.start();
				});
			}
		};

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-static-carousel.default', widgetStaticCarousel );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End twitter carousel widget script
 */


/**
 * Start post grid tab widget script
 */

;
(function ($, elementor) {

	'use strict';

	var widgetStaticPostTab = function ($scope, $) {

		var $postGridTab = $scope.find('.bdt-static-grid-tab'),
			gridTab = $postGridTab.find('.gridtab');

		var $settings = $postGridTab.data('settings');

		if (!$postGridTab.length) {
			return;
		}

		$(gridTab).gridtab($settings);

	};


	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-static-grid-tab.default', widgetStaticPostTab);
	});

}(jQuery, window.elementorFrontend));

/**
 * End post grid tab widget script
 */
/**
 * Start step flow widget script
 */

(function($, elementor) {

    'use strict';

    // Accordion
    var widgetStepFlow = function($scope, $) {

        var $avdDivider = $scope.find('.bdt-step-flow'),
            divider = $($avdDivider).find('.bdt-title-separator-wrapper > img');

        if (!$avdDivider.length) {
            return;
        }

        elementorFrontend.waypoint(divider, function() {
            bdtUIkit.svg(this, {
                strokeAnimation: true
            });
        }, {
            offset: 'bottom-in-view'
        });

    };


    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-step-flow.default', widgetStepFlow);
    });

}(jQuery, window.elementorFrontend));

/**
 * End step flow widget script
 */


/**
 * Start switcher widget script
 */

(function ($, elementor) {

	'use strict';

	var sectionSwitcher = function ($scope, $) {
		var $switcher = $scope.find('.bdt-switchers'),
			$settings = $switcher.data('settings'),
			$activatorSettings = $switcher.data('activator'),
			$settingsLinkWidget = $switcher.data('bdt-link-widget'),
			editMode = Boolean(elementorFrontend.isEditMode());


		if ($activatorSettings !== undefined) {
			// for A
			bdtUIkit.util.on($activatorSettings.switchA, "click", function () {
				bdtUIkit.switcher('#bdt-switcher-activator-' + $activatorSettings.id).show(0);
				bdtUIkit.switcher('#bdt-switcher-' + $activatorSettings.id).show(0);
			});
			// for B
			bdtUIkit.util.on($activatorSettings.switchB, "click", function () {
				bdtUIkit.switcher('#bdt-switcher-activator-' + $activatorSettings.id).show(1);
				bdtUIkit.switcher('#bdt-switcher-' + $activatorSettings.id).show(1);
			});

		}


		// if ( $settings === undefined || editMode ) {
		// 	return;
		// }

		if ($settings !== undefined && editMode === false) {
			var $switchAContainer = $switcher.find('.bdt-switcher > div > div > .bdt-switcher-item-a'),
				$switchBContainer = $switcher.find('.bdt-switcher > div > div > .bdt-switcher-item-b'),
				$switcherContentA = $('.elementor').find('.elementor-section' + '#' + $settings['switch-a-content']),
				$switcherContentB = $('.elementor').find('.elementor-section' + '#' + $settings['switch-b-content']);

			if ($settings.positionUnchanged !== true) {
				if ($switchAContainer.length && $switcherContentA.length) {
					$($switcherContentA).appendTo($switchAContainer);
				}

				if ($switchBContainer.length && $switcherContentB.length) {
					$($switcherContentB).appendTo($switchBContainer);
				}
			}

			if ($settings.positionUnchanged == true) {
				$('#bdt-tabs-' + $settings.id).find('.bdt-switcher').remove();

				var $switcherContentAAA = $('#' + $settings['switch-a-content']);
				var $switcherContentBBB = $('#' + $settings['switch-b-content']);

				$('#' + $settings['switch-a-content']).parent().append(`<div id="bdt-switcher-${$settings.id}" class="bdt-switcher bdt-switcher-item-content" style="width:100%;"></div>`);

				$($switcherContentAAA).appendTo($('#bdt-switcher-' + $settings.id));
				$($switcherContentBBB).appendTo($('#bdt-switcher-' + $settings.id));
				
				var $activeA, $activeB = '';
				if ($settings.defaultActive == 'a'){
						$activeA, $activeA = 'bdt-active';
				}else{
					$activeB = 'bdt-active';
				}

				$('#' + $settings['switch-a-content']).wrapAll('<div class="bdt-switcher-item-content-inner ' + $activeA + '"></div>');
				$('#' + $settings['switch-b-content']).wrapAll('<div class="bdt-switcher-item-content-inner ' + $activeB + '"></div>');
			}
		}
 

		if ($settingsLinkWidget !== undefined && editMode === false) {
			var $targetA = $($settingsLinkWidget.linkWidgetTargetA),
				$targetB = $($settingsLinkWidget.linkWidgetTargetB),
				$switcher = '#bdt-switcher-' + $settingsLinkWidget.id;

			$targetA.css({
				'opacity': 1,
				'display': 'block',
				'grid-row-start': 1,
				'grid-column-start': 1
			});

			$targetA.parent().css({
				'display': 'grid'
			});

			$targetB.css({
				'opacity': 0,
				'display': 'none',
				'grid-row-start': 1,
				'grid-column-start': 1
			});

			bdtUIkit.util.on($switcher, 'shown', function (e) {
				var index = bdtUIkit.util.index(e.target)
				if (index == 0) {
					$targetA.css({
						'opacity': 1,
						'display': 'block',
					});
					$targetB.css({
						'opacity': 0,
						'display': 'none',
					});
				} else {
					$targetB.css({
						'opacity': 1,
						'display': 'block',
					});
					$targetA.css({
						'opacity': 0,
						'display': 'none',
					});
				}

			})
		}


	};

	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-switcher.default', sectionSwitcher);
	});

}(jQuery, window.elementorFrontend));

/**
 * End switcher widget script
 */
/**
 * Start Content Switcher widget script
 */

(function ($, elementor) {

    'use strict';

    var widgetContentSwitcher = function ($scope, $) {

        var $contentSwitcher = $scope.find('.bdt-content-switcher'),
            $settings = $contentSwitcher.data('settings');

        if (!$contentSwitcher.length) {
            return;
        }

        if ('button' !== $settings.switcherStyle) {

            // Conten Switcher Checkbox
            var $checkbox = $contentSwitcher.find('input[type="checkbox"]');
            var primarySwitcher = $contentSwitcher.find('.bdt-primary-switcher');
            var secondarySwitcher = $contentSwitcher.find('.bdt-secondary-switcher');
            var primaryIcon = $contentSwitcher.find('.bdt-primary-icon');
            var secondaryIcon = $contentSwitcher.find('.bdt-secondary-icon');
            var primaryText = $contentSwitcher.find('.bdt-primary-text');
            var secondaryText = $contentSwitcher.find('.bdt-secondary-text');
            var primaryContent = $contentSwitcher.find('.bdt-switcher-content.bdt-primary');
            var secondaryContent = $contentSwitcher.find('.bdt-switcher-content.bdt-secondary');

            $checkbox.on('change', function () {
                if (this.checked) {
                    primarySwitcher.removeClass('bdt-active');
                    secondarySwitcher.addClass('bdt-active');
                    primaryIcon.removeClass('bdt-active');
                    secondaryIcon.addClass('bdt-active');
                    primaryText.removeClass('bdt-active');
                    secondaryText.addClass('bdt-active');
                    primaryContent.removeClass('bdt-active');
                    secondaryContent.addClass('bdt-active');
                } else {
                    primarySwitcher.addClass('bdt-active');
                    secondarySwitcher.removeClass('bdt-active');
                    primaryIcon.addClass('bdt-active');
                    secondaryIcon.removeClass('bdt-active');
                    primaryText.addClass('bdt-active');
                    secondaryText.removeClass('bdt-active');
                    primaryContent.addClass('bdt-active');
                    secondaryContent.removeClass('bdt-active');
                }
            });

        }

        

        if ('button' == $settings.switcherStyle) {

            var $tabs = $contentSwitcher.find('.bdt-switcher-content-wrapper');
            var $tab = $contentSwitcher.find('.bdt-content-switcher-tab');

            $tab.on('click', function () {
                var $this = $(this);
                var id = $this.attr('id');
                var $content = $contentSwitcher.find('.bdt-switcher-content[data-content-id="' + id + '"]');

                $tab.removeClass('bdt-active');
                $this.addClass('bdt-active');

                $tabs.find('.bdt-switcher-content').removeClass('bdt-active');
                $content.addClass('bdt-active');
            });
            
        }
    }


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-content-switcher.default', widgetContentSwitcher);
    });


}(jQuery, window.elementorFrontend));

/**
 * End Content Switcher widget script
 */
; (function ($, elementor) {
    'use strict';
    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base, epSVGImage;
        epSVGImage = ModuleHandler.extend({
            bindEvents: function () {
                this.run();
            },

            getDefaultSettings: function () {
                return {

                };
            },
            onElementChange: debounce(function (prop) {
                if (prop.indexOf('svg_image_') !== -1) {
                    this.run();
                }
            }, 400),

            settings: function (key) {
                return this.getElementSettings('svg_image_' + key);
            },

            run: function () {
                gsap.registerPlugin(DrawSVGPlugin, ScrollTrigger);
                var options = this.getDefaultSettings(),
                    widgetID = this.$element.data('id'),
                    element = this.findElement('.elementor-widget-container').get(0),
                    scrollTrigger = null;

                if (jQuery(this.$element).hasClass('elementor-section')) {
                    element = this.$element.get(0);
                }
                var $container = this.$element.find(".bdt-svg-image");
                if (!$container.length) {
                    return;
                }

                if ('yes' !== this.settings('draw')) {
                    return;
                }

                var shapes = $container.find("path, circle, rect, square, ellipse, polyline, line, polygon");
                const drawerType = this.settings('drawer_type');
                options.repeat = (this.settings('repeat') === 'yes') ? -1 : 0;
                options.yoyo = (this.settings('yoyo') === 'yes') ? true : false;

                if ('automatic' === drawerType) {
                    var reverseAnimation = this.settings('anim_rev') ? 'pause play reverse' : 'none',
                        triggerAnimation = 'custom' !== this.settings('animate_trigger') ? this.settings('animate_trigger') : this.settings('animate_offset.size') + "%";
                    options.scrollTrigger = {
                        trigger: '.elementor-element-' + widgetID,
                        toggleActions: "play " + reverseAnimation,
                        start: "top " + triggerAnimation
                    }
                    var timeLine = gsap.timeline(options);
                }

                if ('hover' === drawerType) {
                    var timeLine = gsap.timeline(options);
                    timeLine.pause();
                    $container.find("svg").hover(
                        function () {
                            timeLine.play();
                        },
                        function () {
                            timeLine.pause();
                        });


                }
                if ('viewport' === drawerType) {
                    var timeLine = gsap.timeline({
                        repeat: 1,
                        yoyo: true
                    });
                    scrollTrigger = (this.settings('animate_offset.size') / 100);
                    var controller = new ScrollMagic.Controller(),
                        scene = new ScrollMagic.Scene({
                            triggerElement: '.elementor-element-' + widgetID,
                            triggerHook: scrollTrigger,
                            duration: 0.6 * 1000
                        })
                    scene.setTween(timeLine).addTo(controller);
                }
                if ('viewport' === drawerType) {
                    timeLine.fromTo(shapes, { drawSVG: "0%" }, { duration: 200, drawSVG: "100%", stagger: 0.1 });
                } else {
                    timeLine.fromTo(shapes, { drawSVG: this.settings('animation_start_point.size') + "%" }, { duration: (this.settings('animation_duration.size') / 100), drawSVG: this.settings('animation_end_point.size') + "%", stagger: 0.1 });
                }
            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-svg-image.default',
            function ($scope) {
                elementorFrontend.elementsHandler.addHandler(epSVGImage, {
                    $element: $scope
                });
            }
        );
    });

})(jQuery, window.elementorFrontend);
; (function ($, elementor) {
$(window).on('elementor/frontend/init', function () {
    let ModuleHandler = elementorModules.frontend.handlers.Base,
        textReadMore;

    textReadMore = ModuleHandler.extend({
        bindEvents: function () {
            this.run();
        },
        getDefaultSettings: function () {
            return {
                allowHTML: true,
            };
        },

        onElementChange: debounce(function (prop) {
            if (prop.indexOf('ep_text_read_more_') !== -1) {
                this.run();
            }
        }, 400),

        settings: function (key) {
            return this.getElementSettings('ep_text_read_more_' + key);
        },

        run: function () {
            var tileScroll_ID = 'bdt-tile-scroll-container-' + this.$element.data('id'),
                widgetID = this.$element.data('id'),
                widgetContainer = $('.elementor-element-' + widgetID);
            var button_style = this.settings('button_style');
            if (this.settings('enable') === 'yes') {
                const dReadMore = new DReadMore();

                window.addEventListener('resize', function () {
                    dReadMore.forEach(function (item) {
                        item.update();
                    });
                });
                // let truncateElement = new Cuttr('.elementor-widget-container', {
                //     truncate: this.settings('truncate') ? this.settings('truncate') : 'characters',
                //     length: this.settings('length') ? this.settings('length') : 10,
                //     ending: this.settings('ending'),
                //     loadedClass: 'cuttr--loaded',
                //     title: this.settings('title') ? this.settings('title'): false,
                //     readMore: this.settings('show_button') ? this.settings('show_button'): false,
                //     readMoreText: this.settings('button_text') ? this.settings('button_text'): 'Read More',
                //     // readMoreText:'<i class="eicon-h-align-left">Read More</i>',
                //     readLessText: this.settings('button_text_less') ? this.settings('button_text_less'): 'Read Less',
                //     readMoreBtnPosition: this.settings('button_position') ? this.settings('button_position'): 'after',
                //     readMoreBtnTag: 'div',
                //     readMoreBtnSelectorClass: 'bdt-text-read-more-btn',
                //     readMoreBtnAdditionalClasses: this.settings('button_style') ? this.settings('button_style'): 'nnn',
                // })
            } else {
                return;
            }

        }
    });

    elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
        elementorFrontend.elementsHandler.addHandler(textReadMore, {
            $element: $scope
        });
    });
});
})(jQuery, window.elementorFrontend);
/**
 * Start table of content widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetTableOfContent = function( $scope, $ ) {

		var $tableOfContent = $scope.find( '.bdt-table-of-content' );
				
        if ( ! $tableOfContent.length ) {
            return;
        }			

        $($tableOfContent).tocify($tableOfContent.data('settings'));

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-table-of-content.default', widgetTableOfContent );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End table of content widget script
 */


/**
 * Start table widget script
 */

(function ($, elementor) {

    'use strict';

    var widgetTable = function ($scope, $) {

        var $tableContainer = $scope.find('.bdt-data-table'),
            $settings = $tableContainer.data('settings'),
            $table = $tableContainer.find('> table');

        if (!$tableContainer.length) {
            return;
        }

        $settings.language = window.ElementPackConfig.data_table.language;
        $($table).DataTable($settings);

    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-table.default', widgetTable);
    });

}(jQuery, window.elementorFrontend));

/**
 * End table widget script
 */
/**
 * Start tabs widget script
 */

(function ($, elementor) {
    'use strict';
    var widgetTabs = function ($scope, $) {
        const $tabsArea = $scope.find('.bdt-tabs-area'),
            $tabs = $tabsArea.find('.bdt-tabs'),
            $tab = $tabs.find('.bdt-tab'),
            editMode = Boolean(elementorFrontend.isEditMode());

        if (!$tabsArea.length) {
            return;
        }
        const $settings = $tabs.data('settings'),
            animTime = $settings.hashScrollspyTime,
            customOffset = $settings.hashTopOffset,
            navStickyOffset = $settings.navStickyOffset;

        if (navStickyOffset == 'undefined') {
            navStickyOffset = 10;
        }

        $scope.find('.bdt-template-modal-iframe-edit-link').each(function () {
            var modal = $($(this).data('modal-element'));
            $(this).on('click', function (event) {
                bdtUIkit.modal(modal).show();
            });
            modal.on('beforehide', function () {
                window.parent.location.reload();
            });
        });


        function hashHandler($tabs, $tab, animTime, customOffset) {
            // debugger;
            if (window.location.hash) {
                if ($($tabs).find('[data-title="' + window.location.hash.substring(1) + '"]').length) {
                    var hashTarget = $('[data-title="' + window.location.hash.substring(1) + '"]').closest($tabs).attr('id');
                    $('html, body').animate({
                        easing: 'slow',
                        scrollTop: $('#' + hashTarget).offset().top - customOffset
                    }, animTime, function () {
                        //#code
                    }).promise().then(function () {
                        bdtUIkit.tab($tab).show($('[data-title="' + window.location.hash.substring(1) + '"]').data('tab-index'));
                    });
                }
            }
        }
        if ($settings.activeHash == 'yes' && $settings.status != 'bdt-sticky-custom') {
            $(window).on('load', function () {
                hashHandler($tabs, $tab, animTime, customOffset);
            });
            $($tabs).find('.bdt-tabs-item-title').off('click').on('click', function (event) {
                window.location.hash = ($.trim($(this).attr('data-title')));
            });
            $(window).on('hashchange', function (e) {
                hashHandler($tabs, $tab, animTime, customOffset);
            });
        }
        //# code for sticky and also for sticky with hash
        function stickyHachChange($tabs, $tab, navStickyOffset) {
            if ($($tabs).find('[data-title="' + window.location.hash.substring(1) + '"]').length) {
                var hashTarget = $('[data-title="' + window.location.hash.substring(1) + '"]').closest($tabs).attr('id');
                $('html, body').animate({
                    easing: 'slow',
                    scrollTop: $('#' + hashTarget).offset().top - navStickyOffset
                }, 1000, function () {
                    //#code
                }).promise().then(function () {
                    bdtUIkit.tab($tab).show($($tab).find('[data-title="' + window.location.hash.substring(1) + '"]').data('tab-index'));
                });
            }
        }
        if ($settings.status == 'bdt-sticky-custom') {
            $($tabs).find('.bdt-tabs-item-title').bind().click('click', function (event) {
                if ($settings.activeHash == 'yes') {
                    window.location.hash = ($.trim($(this).attr('data-title')));
                } else {
                    $('html, body').animate({
                        easing: 'slow',
                        scrollTop: $($tabs).offset().top - navStickyOffset
                    }, 500, function () {
                        //#code
                    });
                }
            });
            // # actived Hash#
            if ($settings.activeHash == 'yes' && $settings.status == 'bdt-sticky-custom') {
                $(window).on('load', function () {
                    if (window.location.hash) {
                        stickyHachChange($tabs, $tab, navStickyOffset);
                    }
                });
                $(window).on('hashchange', function (e) {
                    stickyHachChange($tabs, $tab, navStickyOffset);
                });
            }
        }

        // start linkWidget


        var $linkWidget = $settings['linkWidgetSettings'],
            $activeItem = ($settings['activeItem']) - 1;
        if ($linkWidget !== undefined && editMode === false) {

            $linkWidget.forEach(function (entry, index) {

                if (index == 0) {
                    $('#bdt-tab-content-' + $settings['linkWidgetId']).parent().remove();
                    $(entry).parent().wrapInner('<div class="bdt-switcher-wrapper" />');
                    $(entry).parent().wrapInner('<div id="bdt-tab-content-' + $settings['linkWidgetId'] + '" class="bdt-switcher bdt-switcher-item-content" />');

                    if ($settings['activeItem'] == undefined) {
                        $(entry).addClass('bdt-active');
                    }
                }

                if ($settings['activeItem'] !== undefined && index == $activeItem) {
                    $(entry).addClass('bdt-active');
                }

                $(entry).attr('data-content-id', "tab-" + (index + 1));

            });

            /**
             * Sometimes not works UIKIT connect that's why below code
             */
            $tab.find('a').on('click', function () {
                let index = $(this).data('tab-index');
                $('#bdt-tab-content-' + $settings['linkWidgetId'] + '>').removeClass('bdt-active');
                $('#bdt-tab-content-' + $settings['linkWidgetId'] + '>').eq(index).addClass('bdt-active');
            });

        }
        // end linkWidget

        if (typeof $settings.sectionBg != "undefined") {
            if (typeof $settings.sectionBgSelector == "undefined") {
                return;
            }
            var $id = (($settings.sectionBgSelector) + '-ep-dynamic').substring(1);

            if ($(`#${$id}-wrapper`).length) {
                $(`#${$id}-wrapper`).remove();
            }

            var dynamicBG = `<div id="${$id}-wrapper"  style = "position: absolute; z-index: 0; top: 0; right: 0; bottom: 0; left: 0;" >`;

            $($settings.sectionBg).each(function (e) {

                let newLine = '<div class="bdt-hidden ' + $id + ' bdt-animation-' + $settings.sectionBgAnim + '" style=" width: 100%; height: 100%; transition: all .5s;">';
                newLine += '<img src = "' + $settings.sectionBg[e] + '" style = " height: 100%; width: 100%; object-fit: cover;" >';
                newLine += '</div>';
                dynamicBG += newLine;
            });

            dynamicBG += `</div>`;

            $($settings.sectionBgSelector).prepend(dynamicBG);
            var activeIndex = $tab.find('>.bdt-tabs-item.bdt-active').index();
            $(`.${$id}:eq('${activeIndex}')`).removeClass('bdt-hidden');

            $tabsArea.find('.bdt-tabs-item-title').on('click', function () {
                let $tabImg = $(this).data('tab-index');
                $('.' + $id + ':eq(' + $tabImg + ')').siblings().addClass('bdt-hidden');
                $('.' + $id + ':eq(' + $tabImg + ')').removeClass('bdt-hidden');
            });

        }

        // start section link
        var $linkSection = $settings['linkSectionSettings'];
        if ($linkSection !== undefined && editMode === false) {
            $linkSection.forEach(function (entry, index) {
                let $tabContent = $('#bdt-tab-content-' + $settings.linkWidgetId),
                $section = $(entry);
                $tabContent.find('.bdt-tab-content-item' + ':eq(' + index + ')').html($section);
            });
        }

    };
    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-tabs.default', widgetTabs);
    });
}(jQuery, window.elementorFrontend));

/**
 * End tabs widget script
 */
/**
 * Start tags cloud widget script
 */

(function($, elementor) {

    'use strict';

    var widgetTagsCloud = function($scope, $) {
        var $tags_cloud = $scope.find('.bdt-tags-cloud');
            
            if (!$tags_cloud.length) {
                return;
            }
            var $settings = $tags_cloud.data('settings');
            var $tags_color = $settings.basic_tags_bg_type;
            var tags_color_solid = $settings.basic_tags_solid_bg;
 

            jQuery.fn.prettyTag = function (options) {

                var setting = jQuery.extend({
                        randomColor: true, //false to off random color
                        tagicon: true, //false to turn off tags icon
                        tags_color: options.tags_color
                    }, options);


                return this.each(function () {
                    var target = this;
                        //add font awesome icon
                        if (setting.tagicon == true) {
                            var eachTag = $(target).find("a");
                            var ti = document.createElement("i");
                            $($tags_cloud).find(ti).addClass("fas fa-tags").prependTo(eachTag);
                        }

                        if( setting.tags_color == 'random' ){
                            coloredTags();
                        }else{
                            if (typeof(tags_color_solid) != "undefined"){
                                $($tags_cloud).find('.bdt-tags-list li a').css('background-color', tags_color_solid); 
                            }else{
                               $($tags_cloud).find('.bdt-tags-list li a').css('background-color', '#3FB8FD'); 
                           }
                       }

                        //function to make tags colorful
                        function coloredTags() {

                        var totalTags = $($tags_cloud).find("li").length; //to find total cloud tags
                        // console.log(totalTags);
                        var mct = $($tags_cloud).find("a"); //select all tags links to make them colorful
                        /*Array of Colors */
                        var tagColor = ["#ff0084", "#ff66ff", "#43cea2", "#D38312", "#73C8A9", "#9D50BB",
                        "#780206", "#FF4E50", "#ADD100",
                        "#0F2027", "#00c6ff", "#81D8D0", "#5CB3FF", "#95B9C7", "#C11B17", "#3B9C9C", "#FF7F50", "#FFD801", "#79BAEC", "#F660AB", "#3D3C3A", "#3EA055"
                        ];

                        var tag = 0;
                        var color = 0; //assign colors to tags with loop, unlimited number of tags can be added
                        do {
                            if (color > 21) {
                                color = 0;
                        } //Start again array index if it reaches at last

                        if (setting.randomColor == true) {
                            var $rc = Math.floor(Math.random() * 22);
                            $(mct).eq(tag).css({
                            //tags random color
                            'background': tagColor[$rc]
                        });
                        } else {
                            $(mct).eq(tag).css({
                        //tags color in a sequence
                        'background': tagColor[color]
                    });
                        }
                        tag++;
                        color++;
                    } while (tag <= totalTags)

                }
            });
            };


            /*   End */

            $($tags_cloud).find(".bdt-tags-list").prettyTag({'tags_color': $tags_color});

        };


        var widgetSkinAnimated = function($scope, $) {
            var $tags_globe = $scope.find('.bdt-tags-cloud');
            if (!$tags_globe.length) {
                return;
            }
            var $settings = $tags_globe.data('settings');
 
                try {
                    TagCanvas.Start($settings.idmyCanvas, $settings.idTags, { 
                        textColour         :  $settings.textColour,
                        outlineColour      :  $settings.outlineColour,
                        reverse            :  true,
                        depth              :  $settings.depth, 
                        maxSpeed           :  $settings.maxSpeed, 
                        activeCursor       :  $settings.activeCursor,
                        bgColour           :  $settings.bgColour, 
                        bgOutlineThickness :  $settings.bgOutlineThickness, 
                        bgRadius           :  $settings.bgRadius, 
                        dragControl        :  $settings.dragControl, 
                        fadeIn             :  $settings.fadeIn, 
                        freezeActive       :  $settings.freezeActive,
                        outlineDash        :  $settings.outlineDash,
                        outlineDashSpace   :  $settings.globe_outline_dash_space,
                        outlineDashSpeed   :  $settings.globe_outline_dash_speed,
                        outlineIncrease    :  $settings.outlineIncrease,
                        outlineMethod      :  $settings.outlineMethod, 
                        outlineRadius      :  $settings.outlineRadius,
                        outlineThickness   :  $settings.outlineThickness,
                        shadow             :  $settings.shadow,
                        shadowBlur         :  $settings.shadowBlur,
                        wheelZoom          :  $settings.wheelZoom

                    });
                } catch (e) {
                    document.getElementById($settings.idCanvas).style.display = 'none';
                }
           
        };


        var widgetSkinCloud = function($scope, $) {
            var $tags_cloud = $scope.find('.bdt-tags-cloud');

            if (!$tags_cloud.length) {
                return;
            }
            var $settings = $tags_cloud.data('settings');

            jQuery(document).ready(function($) {
                function resizeAwesomeCloud() {
                    jQuery("#"+$settings.idCloud).awesomeCloud({
                        "size": {
                            "grid": 9,
                            "factor": 1
                        },
                        "color" : {
                        "background" : "rgba(156,145,255,0)", // background color, transparent by default
                        // "background" : "rgba(156,145,255,0)", // background color, transparent by default
                        "start" : "#20f", // color of the smallest font, if options.color = "gradient""
                        "end" : "rgb(200,0,0)" // color of the largest font, if options.color = "gradient"
                    },
                    "options": {
                        "background" :"rgba(165,184,255,0)",
                        "color": $settings.cloudColor,  
                            "sort": "highest" // highest, lowest or random
                        },
                        "font": "'Times New Roman', Times, serif",
                        "shape": $settings.cloudStyle // default 
                    });
                }
                resizeAwesomeCloud();
                jQuery(window).on("resize", function($) { 
                    jQuery($tags_cloud).find('#awesomeCloud'+$settings.idCloud).remove();
                    resizeAwesomeCloud();
                });
            });

        };


        jQuery(window).on('elementor/frontend/init', function() {
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-tags-cloud.default', widgetTagsCloud);
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-tags-cloud.bdt-animated', widgetSkinAnimated); 
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-tags-cloud.bdt-cloud', widgetSkinCloud); 
        });

    }(jQuery, window.elementorFrontend));

/**
 * End tags cloud widget script
 */

 
/**
 * Start testimonial carousel widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetTCarousel = function( $scope, $ ) {

		var $tCarousel = $scope.find( '.bdt-testimonial-carousel' );
            
        if ( ! $tCarousel.length ) {
            return;
        }

		var $tCarouselContainer = $tCarousel.find('.swiper-carousel'),
			$settings 		 = $tCarousel.data('settings');

		// Access swiper class
        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        
        async function initSwiper() {

			var swiper = await new Swiper($tCarouselContainer, $settings);

			if ($settings.pauseOnHover) {
				 $($tCarouselContainer).hover(function() {
					(this).swiper.autoplay.stop();
				}, function() {
					(this).swiper.autoplay.start();
				});
			}
		};

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-testimonial-carousel.default', widgetTCarousel );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-testimonial-carousel.bdt-twyla', widgetTCarousel );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-testimonial-carousel.bdt-vyxo', widgetTCarousel );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End testimonial carousel widget script
 */


/**
 * Start testimonial slider widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetCustomCarousel = function( $scope, $ ) {

		var $carousel = $scope.find( '.bdt-testimonial-slider' );
				
        if ( ! $carousel.length ) {
            return;
        }

        var $carouselContainer = $carousel.find('.swiper-carousel'),
			$settings 		 = $carousel.data('settings');

		// Access swiper class
        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        
        async function initSwiper() {

			var swiper = await new Swiper($carouselContainer, $settings);

			if ($settings.pauseOnHover) {
				 $($carouselContainer).hover(function() {
					(this).swiper.autoplay.stop();
				}, function() {
					(this).swiper.autoplay.start();
				});
			}
		};

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-testimonial-slider.default', widgetCustomCarousel );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-testimonial-slider.bdt-single', widgetCustomCarousel );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End testimonial slider widget script
 */


; (function ($, elementor) {
$(window).on('elementor/frontend/init', function () {
    var ModuleHandler = elementorModules.frontend.handlers.Base,
        ThreedText;

    ThreedText = ModuleHandler.extend({

        bindEvents: function () {
            this.run();
        },

        getDefaultSettings: function () {
            return {
                depth: '30px',
                layers: 8,
            };
        },

        onElementChange: debounce(function (prop) {
            if (prop.indexOf('ep_threed_text_') !== -1) {
                this.run();
            }
        }, 400),

        settings: function (key) {
            return this.getElementSettings('ep_threed_text_' + key);
        },

        run: function () {
            var options = this.getDefaultSettings(),
                $element = this.findElement('.elementor-heading-title, .bdt-main-heading-inner'),
                $widgetId = 'ep-' + this.getID(),
                $widgetIdSelect = '#' + $widgetId;

            jQuery($element).attr('id', $widgetId);

            if (this.settings('depth.size')) {
                options.depth = this.settings('depth.size') + this.settings('depth.unit') || '30px';
            }
            if (this.settings('layers')) {
                options.layers = this.settings('layers') || 8;
            }
            if (this.settings('perspective.size')) {
                options.perspective = this.settings('perspective.size') + 'px' || '500px';
            }
            if (this.settings('fade')) {
                options.fade = !!this.settings('fade');
            }
            // if (this.settings('direction')) {
            //     options.direction = this.settings('direction') || 'forwards';
            // }
            if (this.settings('event')) {
                options.event = this.settings('event') || 'pointer';
            }
            if (this.settings('event_rotation') && this.settings('event') != 'none') {
                options.eventRotation = this.settings('event_rotation.size') + 'deg' || '35deg';
            }
            if (this.settings('event_direction') && this.settings('event') != 'none') {
                options.eventDirection = this.settings('event_direction') || 'default';
            }

            if (this.settings('active') == 'yes') {

                var $text = $($widgetIdSelect).html();
                $($widgetIdSelect).parent().append('<div class="ep-z-text-duplicate" style="display:none;">' + $text + '</div>');

                $text = $($widgetIdSelect).parent().find('.ep-z-text-duplicate:first').html();

                $($widgetIdSelect).find('.z-text').remove();

                var ztxt = new Ztextify($widgetIdSelect, options, $text);
            }

            if (this.settings('depth_color')) {
                var depthColor = this.settings('depth_color') || '#fafafa';
                $($widgetIdSelect).find('.z-layers .z-layer:not(:first-child)').css('color', depthColor);
            }

            // if (this.settings('bg_color')) {
            //     var bgColor = this.settings('bg_color') || 'rgba(96, 125, 139, .5)';
            //     $($widgetIdSelect).find('.z-text > .z-layers').css('background', bgColor);
            // }

        }
    });

    elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
        elementorFrontend.elementsHandler.addHandler(ThreedText, {
            $element: $scope
        });
    });

});
}) (jQuery, window.elementorFrontend);
/**
 * Start threesixty product viewer widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetTSProductViewer = function( $scope, $ ) {

		var $TSPV      	   = $scope.find( '.bdt-threesixty-product-viewer' ),
            $settings      = $TSPV.data('settings'),
            $container     = $TSPV.find('> .bdt-tspv-container'), 
            $fullScreenBtn = $TSPV.find('> .bdt-tspv-fb');  

        if ( ! $TSPV.length ) {
            return;
        }
        

        if ($settings.source_type === 'remote') {
            $settings.source = SpriteSpin.sourceArray( $settings.source, { frame: $settings.frame_limit, digits: $settings.image_digits} );
        }

        elementorFrontend.waypoint( $container, function() {
            var $this = $( this );
            $this.spritespin($settings);

        }, {
            offset: 'bottom-in-view'
        } );

        

        //if ( ! $fullScreenBtn.length ) {
            $($fullScreenBtn).click(function(e) {
                e.preventDefault();
                $($container).spritespin('api').requestFullscreen();
            });
        //}

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-threesixty-product-viewer.default', widgetTSProductViewer );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End threesixty product viewer widget script
 */


; (function ($, elementor) {
$(window).on('elementor/frontend/init', function () {
    let ModuleHandler = elementorModules.frontend.handlers.Base,
        TileScroll;

    TileScroll = ModuleHandler.extend({
        bindEvents: function () {
            this.run();
        },
        getDefaultSettings: function () {
            return {
                allowHTML: true,
            };
        },

        // onElementChange: debounce(function (prop) {
        //     if (prop.indexOf('element_pack_tile_scroll') !== -1) {
        //         this.run();
        //     }
        // }, 400),

        settings: function (key) {
            return this.getElementSettings('element_pack_tile_scroll_' + key);
        },

        run: function () {
            var tileScroll_ID = 'bdt-tile-scroll-container-' + this.$element.data('id'),
                widgetID = this.$element.data('id'),
                widgetContainer = $('.elementor-element-' + widgetID);

            if (this.settings('show') == 'yes') {
                if ($('#' + tileScroll_ID).length === 0) {
                    let display = this.settings('display');
                    var $content = `
                        <div id="${tileScroll_ID}" class="bdt-tile-scroll bdt-tile-scroll--${display}">
                            <div class="bdt-tile-scroll__wrap">`;
                    this.settings('elements').forEach(element => {
                        let images = element.element_pack_tile_scroll_images;

                        let x_start = element.element_pack_tile_scroll_x_start.size;
                        let x_end = element.element_pack_tile_scroll_x_end.size;
                        if (display === 'horizontal') {
                            var parallax = 'data-bdt-parallax="target: .elementor-element-' + widgetID + '; viewport: 1.1; x:' + x_start + ',' + x_end + '"';
                        } else {
                            var parallax = 'data-bdt-parallax="y:' + x_start + ',' + x_end + '"';
                        }
                        $content += `<div class="bdt-tile-scroll__line" ${parallax}>`;
                        images.forEach(image => {
                            $content += `<div class=" bdt-tile-scroll__line-img" style="background-image:url(${image.url})" loading="lazy"></div>`;
                        });
                        $content += `</div>`;
                    });
                    $content += `</div></div>`;

                    $(widgetContainer).prepend($content);
                }
            }
        }
    });

    elementorFrontend.hooks.addAction('frontend/element_ready/section', function ($scope) {
        if (!$scope.hasClass("bdt-tile-scroll-yes")) {
            return;
        }
        elementorFrontend.elementsHandler.addHandler(TileScroll, {
            $element: $scope
        });
    });
    elementorFrontend.hooks.addAction('frontend/element_ready/container', function ($scope) {
        if (!$scope.hasClass("bdt-tile-scroll-yes")) {
            return;
        }
        elementorFrontend.elementsHandler.addHandler(TileScroll, {
            $element: $scope
        });
    });
});
}) (jQuery, window.elementorFrontend);

/**
 * Start time zone widget script
 */

(function ($, elementor) {
    'use strict';
    var widgetTimeZone = function ($scope, $) {
        var $TimeZone = $scope.find('.bdt-time-zone');

        if (!$TimeZone.length) {
            return;
        }

        elementorFrontend.waypoint($TimeZone, function () {
            var $this = $(this),
                $settings = $this.data('settings'),
                timeFormat,
                offset = $settings.gmt,
                dateFormat = $settings.dateFormat;

            var timeZoneApp = {
                digitalClock: function () {
                    if ($settings.timeHour == '12h') {
                        timeFormat = '%I:%M:%S %p';
                    } else {
                        timeFormat = '%H:%M:%S';
                    }
                    var dateFormat = $settings.dateFormat;
                    if (dateFormat != 'emptyDate') {
                        dateFormat = '<div class=\"bdt-time-zone-date\"> ' + $settings.dateFormat + ' </div>'
                    } else {
                        dateFormat = '';
                    }
                    var country;
                    if ($settings.country != 'emptyCountry') {
                        country = '<div  class=\"bdt-time-zone-country\">' + $settings.country + '</div>';
                    } else {
                        country = ' ';
                    }
                    var timeZoneFormat = '<div class=\"bdt-time-zone-dt\"> ' + country + ' ' + dateFormat + ' <div class=\"bdt-time-zone-time\">' + timeFormat + ' </div> </div>';

                    if (offset == '') return;
                    var options = {
                        format: timeZoneFormat,
                        timeNotation: $settings.timeHour, //'24h',
                        am_pm: true,
                        utc: (offset == 'local') ? false : true,
                        utcOffset: (offset == 'local') ? null : offset,
                    }

                    $('#' + $settings.id).jclock(options);
                },
                convertToTimeZoneAndFormat: function (date, offset) {
                    // Get the UTC time in milliseconds
                    const utcTime = date.getTime() + (date.getTimezoneOffset() * 60000);

                    // Calculate the target time using the offset
                    const targetTime = new Date(utcTime + (offset * 3600000));

                    // Extract hours, minutes, and seconds
                    let hours = targetTime.getHours(),
                        minutes = targetTime.getMinutes(),
                        seconds = targetTime.getSeconds();
                    const ampm = hours >= 12 ? 'PM' : 'AM',
                        getDate = targetTime.toDateString();
                    hours = hours % 12 || 12; // Convert to 12-hour format and handle midnight (0 AM)

                    // Add leading zeros to single-digit minutes and seconds
                    minutes = minutes < 10 ? '0' + minutes : minutes;
                    seconds = seconds < 10 ? '0' + seconds : seconds;

                    return {
                        hours,
                        minutes,
                        seconds,
                        ampm,
                        getDate,
                    };
                },
                formatDate: function (inputDate, formatOption) {
                    var date = new Date(inputDate),
                        selectedFormat = formatOption;

                    if (!selectedFormat) {
                        console.error('Invalid format option');
                        return '';
                    }

                    // Replace format placeholders
                    var formattedDate = selectedFormat.replace(/%([a-zA-Z])/g, function (_, formatCode) {
                        switch (formatCode) {
                            case 'd':
                                return String(date.getDate()).padStart(2, '0');
                            case 'm':
                                return String(date.getMonth() + 1).padStart(2, '0');
                            case 'y':
                                return String(date.getFullYear()).slice(-2);
                            case 'Y':
                                return String(date.getFullYear());
                            case 'b':
                                return date.toLocaleString('default', {
                                    month: 'short'
                                });
                            case 'a':
                                return date.toLocaleString('default', {
                                    weekday: 'short'
                                });
                            default:
                                return formatCode;
                        }
                    });

                    return formattedDate;
                },
                date: function () {
                    let localDate = new Date(),
                        targetOffset = offset,
                        result = timeZoneApp.convertToTimeZoneAndFormat(localDate, targetOffset),
                        date = result.getDate;

                    const formattedDate = this.formatDate(date, dateFormat);
                    $($TimeZone).find('.bdt-time-zone-date').text(formattedDate);
                },
                updateTime: function () {
                    setInterval(function () {
                        let localDate = new Date(),
                            targetOffset = offset,
                            result = timeZoneApp.convertToTimeZoneAndFormat(localDate, targetOffset);

                        let second = result.seconds * 6,
                            minute = result.minutes * 6 + second / 60,
                            hour = ((result.hours % 12) / 12) * 360 + 90 + minute / 12;

                        $($TimeZone).find('.bdt-clock-hour').css("transform", "rotate(" + hour + "deg)");
                        $($TimeZone).find('.bdt-clock-minute').css("transform", "rotate(" + minute + "deg)");
                        $($TimeZone).find('.bdt-clock-second').css("transform", "rotate(" + second + "deg)");
                        $($TimeZone).find('.bdt-clock-am-pm').text(result.ampm);

                    }, 1000);

                    this.date();
                },
                init: function () {
                    if ('digital' == $settings.clock_style) {
                        this.digitalClock();
                    } else {
                        this.updateTime();
                    }
                }
            }
            timeZoneApp.init();
        }, {
            offset: 'bottom-in-view'
        });
    };
    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-time-zone.default', widgetTimeZone);
    });
}(jQuery, window.elementorFrontend));

/**
 * End time zone widget script
 */
/**
 * Start timeline widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetTimeline = function( $scope, $ ) {

		var $timeline = $scope.find( '.bdt-timeline-skin-olivier' );
				
        if ( ! $timeline.length ) {
            return;
        }

        $($timeline).timeline({
            visibleItems : $timeline.data('visible_items'),
        });

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-timeline.bdt-olivier', widgetTimeline );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End timeline widget script
 */


/**
 * Start toggle widget script
 */

(function ($, elementor) {
    'use strict';
    var widgetToggle = function ($scope, $) {
        var $toggleContainer = $scope.find('.bdt-show-hide-container');
        var $toggle          = $toggleContainer.find('.bdt-show-hide');

        if ( !$toggleContainer.length ) {
            return;
        } 
        var $settings            = $toggle.data('settings');
        var toggleId             = $settings.id;
        var animTime             = $settings.scrollspy_time;
        var scrollspy_top_offset = $settings.scrollspy_top_offset;

        var by_widget_selector_status = $settings.by_widget_selector_status;
        var toggle_initially_open     = $settings.toggle_initially_open;
        var source_selector           = $settings.source_selector;
        var widget_visibility         = $settings.widget_visibility;
        var widget_visibility_tablet  = $settings.widget_visibility_tablet;
        var widget_visibility_mobile  = $settings.widget_visibility_mobile;
        var viewport_lg               = $settings.viewport_lg;
        var viewport_md               = $settings.viewport_md;

        var widget_visibility_filtered = widget_visibility;

        if ( $settings.widget_visibility == 'undefined' || $settings.widget_visibility == null ) {
            widget_visibility_filtered = widget_visibility = 0;
        }

        if ( $settings.widget_visibility_tablet == 'undefined' || $settings.widget_visibility_tablet == null ) {
            widget_visibility_tablet = widget_visibility;
        }

        if ( $settings.widget_visibility_mobile == 'undefined' || $settings.widget_visibility_mobile == null ) {
            widget_visibility_mobile = widget_visibility;
        }

        function widgetVsibleFiltered() {
            if ( (window.outerWidth) > (viewport_lg) ) {
                widget_visibility_filtered = widget_visibility;
            } else if ( (window.outerWidth) > (viewport_md) ) {
                widget_visibility_filtered = widget_visibility_tablet;
            } else {
                widget_visibility_filtered = widget_visibility_mobile;
            }
        }

        $(window).resize(function () {
            widgetVsibleFiltered();
        });


        function scrollspyHandler($toggle, toggleId, toggleBtn, animTime, scrollspy_top_offset) {
            if ( $settings.status_scrollspy === 'yes' && by_widget_selector_status !== 'yes' ) {
                if ( $($toggle).find('.bdt-show-hide-item') ) {
                    if ( $settings.hash_location === 'yes' ) {
                        window.location.hash = ($.trim(toggleId));
                    }
                    var scrollspyWrapper = $('#bdt-show-hide-' + toggleId).find('.bdt-show-hide-item');
                    $('html, body').animate({
                        easing   : 'slow',
                        scrollTop: $(scrollspyWrapper).offset().top - scrollspy_top_offset
                    }, animTime, function () {
                        //#code
                    }).promise().then(function () {
                        $(toggleBtn).siblings('.bdt-show-hide-content').slideToggle('slow', function () {
                            $(toggleBtn).parent().toggleClass('bdt-open');
                        });
                    });
                }
            } else {
                if ( by_widget_selector_status === 'yes' ) {
                    $(toggleBtn).parent().toggleClass('bdt-open');
                    $(toggleBtn).siblings('.bdt-show-hide-content').slideToggle('slow', function () {
                    });
                }else{
                    $(toggleBtn).siblings('.bdt-show-hide-content').slideToggle('slow', function () {
                        $(toggleBtn).parent().toggleClass('bdt-open');
                    });
                }
                
            }
        }

        $($toggle).find('.bdt-show-hide-title').off('click').on('click', function (event) {
            var toggleBtn = $(this);
            scrollspyHandler($toggle, toggleId, toggleBtn, animTime, scrollspy_top_offset);
        });

        function hashHandler() {
            toggleId             = window.location.hash.substring(1);
            var toggleBtn        = $('#bdt-show-hide-' + toggleId).find('.bdt-show-hide-title');
            var scrollspyWrapper = $('#bdt-show-hide-' + toggleId).find('.bdt-show-hide-item');
            $('html, body').animate({
                easing   : 'slow',
                scrollTop: $(scrollspyWrapper).offset().top - scrollspy_top_offset
            }, animTime, function () {
                //#code
            }).promise().then(function () {
                $(toggleBtn).siblings('.bdt-show-hide-content').slideToggle('slow', function () {
                    $(toggleBtn).parent().toggleClass('bdt-open');
                });
            });
        }

        $(window).on('load', function () {
            if ( $($toggleContainer).find('#bdt-show-hide-' + window.location.hash.substring(1)).length != 0 ) {
                if ( $settings.hash_location === 'yes' ) {
                    hashHandler();
                }
            }
        });

        /* Function to animate height: auto */
        function autoHeightAnimate(element, time){
    var curHeight = element.height(), // Get Default Height
        autoHeight = element.css('height', 'auto').height(); // Get Auto Height
          element.height(curHeight); // Reset to Default Height
          element.stop().animate({ height: autoHeight }, time); // Animate to Auto Height
      }
      function byWidgetHandler() {
        if ( $settings.status_scrollspy === 'yes' ) {
            $('html, body').animate({
                easing   : 'slow',
                scrollTop: $(source_selector).offset().top - scrollspy_top_offset
            }, animTime, function () {
                    //#code
                }).promise().then(function () {
                    if ( $(source_selector).hasClass('bdt-fold-close') ) {
                        // $(source_selector).css({
                        //     'max-height': '100%'
                        // }).removeClass('bdt-fold-close toggle_initially_open').addClass('bdt-fold-open');
                        $(source_selector).removeClass('bdt-fold-close toggle_initially_open').addClass('bdt-fold-open');
                        autoHeightAnimate($(source_selector), 500);
                    } else {
                        $(source_selector).css({
                            'height': widget_visibility_filtered + 'px'
                        }).addClass('bdt-fold-close').removeClass('bdt-fold-open');
                    }
                });
            } else {
                if ( $(source_selector).hasClass('bdt-fold-close') ) {
                    // $(source_selector).css({
                    //     'max-height': '100%'
                    // }).removeClass('bdt-fold-close toggle_initially_open').addClass('bdt-fold-open');
                    $(source_selector).removeClass('bdt-fold-close toggle_initially_open').addClass('bdt-fold-open');
                    autoHeightAnimate($(source_selector), 500);

                } else {
                    $(source_selector).css({
                        'height': widget_visibility_filtered + 'px',
                        'transition' : 'all 1s ease-in-out 0s'
                    }).addClass('bdt-fold-close').removeClass('bdt-fold-open');    
                } 
            }

        }


        if ( by_widget_selector_status === 'yes' ) {
            $($toggle).find('.bdt-show-hide-title').on('click', function () {
                byWidgetHandler();
            });

            if ( toggle_initially_open === 'yes' ) {
                $(source_selector).addClass('bdt-fold-toggle bdt-fold-open toggle_initially_open');
            } else {
                $(source_selector).addClass('bdt-fold-toggle bdt-fold-close toggle_initially_open');
            }

            $(window).resize(function () {
                visibilityCalled();
            });
            visibilityCalled();
        }

        function visibilityCalled() {
            if ( $(source_selector).hasClass('bdt-fold-close') ) {
                $(source_selector).css({
                    'height': widget_visibility_filtered + 'px'
                });
            } else {
                // $(source_selector).css({
                //     'max-height': '100%'
                // });
                autoHeightAnimate($(source_selector), 500);
            }
        }


    };
    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-toggle.default', widgetToggle);
    });
}(jQuery, window.elementorFrontend));

/**
 * End toggle widget script
 */


; (function ($, elementor) {
$(window).on('elementor/frontend/init', function () {
    var ModuleHandler = elementorModules.frontend.handlers.Base,
        Tooltip;

    Tooltip = ModuleHandler.extend({

        bindEvents: function () {
            this.run();
        },

        getDefaultSettings: function () {
            return {
                allowHTML: true,
            };
        },

        onElementChange: debounce(function (prop) {
            if (prop.indexOf('element_pack_widget_') !== -1) {
                this.instance.destroy();
                this.run();
            }
        }, 400),

        settings: function (key) {
            return this.getElementSettings('element_pack_widget_' + key);
        },

        run: function () {
            var options = this.getDefaultSettings();
            var widgetID = this.$element.data('id');
            var widgetContainer = document.querySelector('.elementor-element-' + widgetID + ' .elementor-widget-container');

            if (this.settings('tooltip_text')) {
                options.content = this.settings('tooltip_text');
            }

            options.arrow = !!this.settings('tooltip_arrow');
            options.followCursor = !!this.settings('tooltip_follow_cursor');

            if (this.settings('tooltip_placement')) {
                options.placement = this.settings('tooltip_placement');
            }

            if (this.settings('tooltip_trigger')) {
                if (this.settings('tooltip_custom_trigger')) {
                    options.triggerTarget = document.querySelector(this.settings('tooltip_custom_trigger'));
                } else {
                    options.trigger = this.settings('tooltip_trigger');
                }
            }
            // if (this.settings('tooltip_animation_duration')) {
            //     options.duration = this.settings('tooltip_animation_duration.sizes.from');
            // }
            if (this.settings('tooltip_animation')) {
                if (this.settings('tooltip_animation') === 'fill') {
                    options.animateFill = true;
                } else {
                    options.animation = this.settings('tooltip_animation');
                }
            }
            if (this.settings('tooltip_x_offset.size') || this.settings('tooltip_y_offset.size')) {
                options.offset = [this.settings('tooltip_x_offset.size') || 0, this.settings('tooltip_y_offset.size') || 0];
            }
            if (this.settings('tooltip')) {
                options.theme = 'bdt-tippy-' + widgetID;
                this.instance = tippy(widgetContainer, options);
            }
        }
    });

    elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
        elementorFrontend.elementsHandler.addHandler(Tooltip, {
            $element: $scope
        });
    });
});
})(jQuery, window.elementorFrontend);
/**
 * Start advanced counter widget script
 */

;(function($, elementor) {
    'use strict';
    // AdvancedCounter
    var widgetAdvancedCounter = function($scope, $) {
        var $AdvancedCounter = $scope.find('.bdt-advanced-counter');
        if (!$AdvancedCounter.length) {
            return;
        }

        elementorFrontend.waypoint($AdvancedCounter, function() {

            var $this = $(this);
            var $settings = $this.data('settings');
            // start null checking
            var countStart = $settings.countStart;
            if (typeof countStart === 'undefined' || countStart == null) {
                countStart = 0;
            }
            
            var countNumber = $settings.countNumber;
            if (typeof countNumber === 'undefined' || countNumber == null) {
                countNumber = 0;
            }
            var decimalPlaces = $settings.decimalPlaces;
            if (typeof decimalPlaces === 'undefined' || decimalPlaces == null) {
                decimalPlaces = 0;
            }
            var duration = $settings.duration;
            if (typeof duration === 'undefined' || duration == null) {
                duration = 0;
            }
            var useEasing = $settings.useEasing;
            useEasing = !(typeof useEasing === 'undefined' || useEasing == null);
            var useGrouping = $settings.useGrouping;

            useGrouping = !(typeof useGrouping === 'undefined' || useGrouping == null);

            var counterSeparator = $settings.counterSeparator;
            if (typeof counterSeparator === 'undefined' || counterSeparator == null) {
                counterSeparator = '';
            }
            var decimalSymbol = $settings.decimalSymbol;
            if (typeof decimalSymbol === 'undefined' || decimalSymbol == null) {
                decimalSymbol = '';
            }
            var counterPrefix = $settings.counterPrefix;
            if (typeof counterPrefix === 'undefined' || counterPrefix == null) {
                counterPrefix = '';
            }
            var counterSuffix = $settings.counterSuffix;
            if (typeof counterSuffix === 'undefined' || counterSuffix == null) {
                counterSuffix = '';
            }

            // end null checking


            var options = {  
                startVal: countStart,
                numerals: $settings.language,
                decimalPlaces: decimalPlaces,
                duration: duration,
                useEasing: useEasing,
                useGrouping: useGrouping,
                separator: counterSeparator,
                decimal: decimalSymbol,
                prefix: counterPrefix,
                suffix: counterSuffix,


            };

            var demo = new CountUp($settings.id, countNumber, options);
            if (!demo.error) {
                demo.start();
            } else {
                console.error(demo.error);
            }
            //  start  for count 

        }, {
            offset: 'bottom-in-view'
        });

    };
    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-total-count.default', widgetAdvancedCounter);
    });
}(jQuery, window.elementorFrontend));

/**
 * End advanced counter widget script
 */


/**
 * Start tutor lms grid widget script
 */

(function ($, elementor) {

	'use strict';

	var widgetTutorLMSGrid = function ($scope, $) {

		var $tutorLMS = $scope.find('.bdt-tutor-lms-course-grid'),
			$settings = $tutorLMS.data('settings');

		if (!$tutorLMS.length) {
			return;
		}

		if ($settings.tiltShow == true) {
			var elements = document.querySelectorAll($settings.id + " .bdt-tutor-course-item");
			VanillaTilt.init(elements);
		}

	};

	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-tutor-lms-course-grid.default', widgetTutorLMSGrid);
	});

}(jQuery, window.elementorFrontend));

/**
 * End tutor lms grid widget script
 */

/**
 * Start tutor lms widget script
 */

(function ($, elementor) {

	'use strict';

	var widgetTutorCarousel = function ($scope, $) {

		var $tutorCarousel = $scope.find('.bdt-tutor-lms-course-carousel');

		if (!$tutorCarousel.length) {
			return;
		}

		var $tutorCarouselContainer = $tutorCarousel.find('.swiper-carousel'),
			$settings = $tutorCarousel.data('settings');

		// Access swiper class
		const Swiper = elementorFrontend.utils.swiper;
		initSwiper();

		async function initSwiper() {

			var swiper = await new Swiper($tutorCarouselContainer, $settings);

			if ($settings.pauseOnHover) {
				$($tutorCarouselContainer).hover(function () {
					(this).swiper.autoplay.stop();
				}, function () {
					(this).swiper.autoplay.start();
				});
			}
		};
	};


	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-tutor-lms-course-carousel.default', widgetTutorCarousel);
	});

}(jQuery, window.elementorFrontend));

/**
 * End tutor lms widget script
 */
/**
 * Start EDD product carousel widget script
 */

(function ($, elementor) {
  "use strict";

  var widgetEddProductCarousel = function ($scope, $) {
    var $eddProductCarousel = $scope.find(".ep-learnpress-carousel");

    if (!$eddProductCarousel.length) {
      return;
    }

    var $eddProductCarouselContainer = $eddProductCarousel.find(".swiper-carousel"),
      $settings = $eddProductCarousel.data("settings");

    const Swiper = elementorFrontend.utils.swiper;
    initSwiper();
    async function initSwiper() {
      var swiper = await new Swiper($eddProductCarouselContainer, $settings); // this is an example
      if ($settings.pauseOnHover) {
        $($eddProductCarouselContainer).hover(
          function () {
            this.swiper.autoplay.stop();
          },
          function () {
            this.swiper.autoplay.start();
          }
        );
      }
    }
  };

  jQuery(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/bdt-learnpress-carousel.default",
      widgetEddProductCarousel
    );
  });
})(jQuery, window.elementorFrontend);

/**
 * End twitter carousel widget script
 */

/**
 * Start twitter carousel widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetTwitterCarousel = function( $scope, $ ) {

		var $twitterCarousel = $scope.find( '.bdt-twitter-carousel' );
				
        if ( ! $twitterCarousel.length ) {
            return;
        }

        //console.log($twitterCarousel);

		var $twitterCarouselContainer = $twitterCarousel.find('.swiper-carousel'),
			$settings 		 = $twitterCarousel.data('settings');

		// Access swiper class
        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        
        async function initSwiper() {

			var swiper = await new Swiper($twitterCarouselContainer, $settings);

			if ($settings.pauseOnHover) {
				 $($twitterCarouselContainer).hover(function() {
					(this).swiper.autoplay.stop();
				}, function() {
					(this).swiper.autoplay.start();
				});
			}
		};
	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-twitter-carousel.default', widgetTwitterCarousel );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End twitter carousel widget script
 */


/**
 * Start twitter slider widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetTwitterSlider = function( $scope, $ ) {

		var $twitterSlider = $scope.find( '.bdt-twitter-slider' );
				
        if ( ! $twitterSlider.length ) {
            return;
        }

		var $twitterSliderContainer = $twitterSlider.find('.swiper-carousel'),
			$settings 		 = $twitterSlider.data('settings');

		// Access swiper class
        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        
        async function initSwiper() {

			var swiper = await new Swiper($twitterSliderContainer, $settings);

			if ($settings.pauseOnHover) {
				 $($twitterSliderContainer).hover(function() {
					(this).swiper.autoplay.stop();
				}, function() {
					(this).swiper.autoplay.start();
				});
			}
		};
	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-twitter-slider.default', widgetTwitterSlider );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End twitter slider widget script
 */


/**
 * Start user login widget script
 */

( function ($, elementor) {

        'use strict';

        window.is_fb_loggedin = false;
        window.is_google_loggedin = false;

        var widgetUserLoginForm = {
            loginFormSubmission: function (login_form) {
                var redirect_url = login_form.find('.redirect_after_login').val();

                $.ajax({
                    type     : 'POST',
                    dataType : 'json',
                    url      : element_pack_ajax_login_config.ajaxurl,
                    data     : login_form.serialize(),
                    beforeSend: function (xhr) {
                        bdtUIkit.notification({
                            message: '<div bdt-spinner></div> ' + element_pack_ajax_login_config.loadingmessage,
                            timeout: false
                        });
                    },
                    success: function (data) {
                        var recaptcha_field = login_form.find('.element-pack-google-recaptcha');
                        if (recaptcha_field.length > 0) {
                            var recaptcha_id = recaptcha_field.attr('data-widgetid');
                            grecaptcha.reset(recaptcha_id);
                            grecaptcha.execute(recaptcha_id);
                        }

                        if (data.loggedin == true) {
                            bdtUIkit.notification.closeAll();
                            bdtUIkit.notification({
                                message : '<span bdt-icon=\'icon: check\'></span> ' + data.message,
                                status  : 'primary'
                            });
                            document.location.href = redirect_url;
                        } else {
                            bdtUIkit.notification.closeAll();
                            bdtUIkit.notification({
                                message : '<div class="bdt-flex"><span bdt-icon=\'icon: warning\'></span><span>' + data.message + '</span></div>',
                                status  : 'warning'
                            });
                        }
                    },
                    error: function (data) {
                        bdtUIkit.notification.closeAll();
                        bdtUIkit.notification({
                            message : '<span bdt-icon=\'icon: warning\'></span>' + element_pack_ajax_login_config.unknownerror,
                            status  : 'warning'
                        });
                    }
                });
            },
            get_facebook_user_data: function (widget_wrapper) {
                var redirect_url = widget_wrapper.find('.redirect_after_login').val();

                FB.api('/me', {fields: 'id, name, first_name, last_name, email, link, gender, locale, picture'},
                    function (response) {

                        var userID = FB.getAuthResponse()['userID'];
                        var access_token = FB.getAuthResponse()['accessToken'];

                        window.is_fb_loggedin = true;

                        var fb_data = {
                            'id'         : response.id,
                            'name'       : response.name,
                            'first_name' : response.first_name,
                            'last_name'  : response.last_name,
                            'email'      : response.email,
                            'link'       : response.link,
                        };

                        $.ajax({
                            url: window.ElementPackConfig.ajaxurl,
                            method: 'post',
                            data: {
                                action          : 'element_pack_social_facebook_login',
                                data            : fb_data,
                                method          : 'post',
                                dataType        : 'json',
                                userID          : userID,
                                security_string : access_token,
                                'lang': element_pack_ajax_login_config.language
                            },
                            dataType: 'json',
                            beforeSend: function (xhr) {
                                bdtUIkit.notification({
                                    message: '<div bdt-spinner></div> ' + element_pack_ajax_login_config.loadingmessage,
                                    timeout: false
                                });
                            },
                            success: function (data) {
                                if (data.success === true) {
                                    if (undefined === redirect_url) {
                                        location.reload();
                                    } else {
                                        window.location = redirect_url;
                                    }
                                } else {
                                    location.reload();
                                }
                            },
                            complete: function (xhr, status) {

                                bdtUIkit.notification.closeAll();
                            }

                        });

                    });
            },

            load_recaptcha: function () {
                var reCaptchaFields = $('.element-pack-google-recaptcha'), widgetID;

                if (reCaptchaFields.length > 0) {
                    reCaptchaFields.each(function () {
                        var self = $(this),
                            attrWidget = self.attr('data-widgetid');
                        // alert(self.data('sitekey'))
                        // Avoid re-rendering as it's throwing API error
                        if (( typeof attrWidget !== typeof undefined && attrWidget !== false )) {
                            return;
                        } else {
                            widgetID = grecaptcha.render($(this).attr('id'), {
                                sitekey: self.data('sitekey'),
                                callback: function (response) {
                                    if (response !== '') {
                                        self.append(jQuery('<input>', {
                                            type  : 'hidden',
                                            value : response,
                                            class : 'g-recaptcha-response'
                                        }));
                                    }
                                }
                            });
                            self.attr('data-widgetid', widgetID);
                        }
                    });
                }
            }

        };

        window.onLoadElementPackLoginCaptcha = widgetUserLoginForm.load_recaptcha;

        var widgetUserLoginFormHandler = function ($scope, $) {
            var widget_wrapper  = $scope.find('.bdt-user-login');
            var login_form      = $scope.find('form.bdt-user-login-form');
            var recaptcha_field = $scope.find('.element-pack-google-recaptcha');
            var fb_button       = widget_wrapper.find('.fb_btn_link');
            var google_button   = widget_wrapper.find('#google_btn_link');
            var redirect_url    = widget_wrapper.find('.redirect_after_login').val();

            if (login_form.length > 0) {
                login_form.on('submit', function (e) {
                    e.preventDefault();
                    widgetUserLoginForm.loginFormSubmission(login_form);
                });
            }

            if (elementorFrontend.isEditMode() && undefined === recaptcha_field.attr('data-widgetid')) {
                onLoadElementPackLoginCaptcha();
            }

            if (recaptcha_field.length > 0) {
                grecaptcha.ready(function () {
                    var recaptcha_id = recaptcha_field.attr('data-widgetid');
                    grecaptcha.execute(recaptcha_id);
                });
            }

            if (fb_button.length > 0) {
                /**
                 * Login with Facebook.
                 *
                 */
                // Fetch the user profile data from facebook.

                fb_button.on('click', function () {
                    if (!is_fb_loggedin) {
                        FB.login(function (response) {
                            if (response.authResponse) {
                                // Get and display the user profile data.
                                widgetUserLoginForm.get_facebook_user_data(widget_wrapper);
                            } else {
                                // $scope.find( '.status' ).addClass( 'error' ).text( 'User cancelled login or did not fully authorize.' );
                            }
                        }, {scope: 'email'});
                    }

                });
            }


            /** google */
            if (google_button.length > 0) {

                var client_id = google_button.data('clientid');

                /**
                 * Login with Google.
                 */
                gapi.load('auth2', function () {
                    // Retrieve the singleton for the GoogleAuth library and set up the client.
                    var auth2 = gapi.auth2.init({
                        client_id: client_id,
                        cookiepolicy: 'single_host_origin',
                    });

                    auth2.attachClickHandler('google_btn_link', {},
                        function (googleUser) {

                            var profile = googleUser.getBasicProfile();
                            var name    = profile.getName();
                            var email   = profile.getEmail();

                            if (window.is_google_loggedin) {

                                var id_token = googleUser.getAuthResponse().id_token;

                                $.ajax({
                                    url: window.ElementPackConfig.ajaxurl,
                                    method: 'post',
                                    data: {
                                        action: 'element_pack_social_google_login',
                                        id_token: id_token
                                    },
                                    dataType: 'json',
                                    beforeSend: function (xhr) {
                                        bdtUIkit.notification({
                                            message: '<div bdt-spinner></div> ' + element_pack_ajax_login_config.loadingmessage,
                                            timeout: false
                                        });
                                    },
                                    success: function (data) {
                                        if (data.success === true) {
                                            if (undefined === redirect_url) {
                                                location.reload();
                                            } else {
                                                window.location = redirect_url;
                                            }
                                        }
                                    },
                                    complete: function (xhr, status) {
                                        bdtUIkit.notification.closeAll();
                                    }

                                });
                            }

                        }, function (error) {
                            // error here
                        }
                    );

                });

                google_button.on('click', function () {
                    window.is_google_loggedin = true;
                });
            }
        };


        jQuery(window).on('elementor/frontend/init', function () {
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-user-login.default', widgetUserLoginFormHandler);
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-user-login.bdt-dropdown', widgetUserLoginFormHandler);
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-user-login.bdt-modal', widgetUserLoginFormHandler);
        });

    }(jQuery, window.elementorFrontend));

/**
 * End user login widget script
 */


/**
 * Start user register widget script
 */

(function ($, elementor) {

    'use strict';

    var widgetUserRegistrationForm = {

        registraitonFormSubmit: function (_this, $scope) {

            bdtUIkit.notification({
                message: '<div bdt-spinner></div>' + $(_this).find('.bdt_spinner_message').val(),
                timeout: false
            });
            $(_this).find('button.bdt-button').attr("disabled", true);
            var redirect_url = $(_this).find('.redirect_after_register').val();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: element_pack_ajax_login_config.ajaxurl,
                data: {
                    'action': 'element_pack_ajax_register', //calls wp_ajax_nopriv_element_pack_ajax_register
                    'first_name': $(_this).find('.first_name').val(),
                    'last_name': $(_this).find('.last_name').val(),
                    'email': $(_this).find('.user_email').val(),
                    'password': $(_this).find('.user_password').val(),
                    'is_password_required': $(_this).find('.is_password_required').val(),
                    'g-recaptcha-response': $(_this).find('#g-recaptcha-response').val(),
                    'widget_id': $scope.data('id'),
                    'page_id': $(_this).find('.page_id').val(),
                    'security': $(_this).find('#bdt-user-register-sc').val(),
                    'lang': element_pack_ajax_login_config.language
                },
                success: function (data) {

                    var recaptcha_field = _this.find('.element-pack-google-recaptcha');
                    if (recaptcha_field.length > 0) {
                        var recaptcha_id = recaptcha_field.attr('data-widgetid');
                        grecaptcha.reset(recaptcha_id);
                        grecaptcha.execute(recaptcha_id);
                    }

                    if (data.registered === true) {
                        bdtUIkit.notification.closeAll();
                        bdtUIkit.notification({
                            message: '<div class="bdt-flex"><span bdt-icon=\'icon: info\'></span><span>' + data.message + '</span></div>',
                            status: 'primary'
                        });
                        if (redirect_url) {
                            document.location.href = redirect_url;
                        }
                    } else {
                        bdtUIkit.notification.closeAll();
                        bdtUIkit.notification({
                            message: '<div class="bdt-flex"><span bdt-icon=\'icon: warning\'></span><span>' + data.message + '</span></div>',
                            status: 'warning'
                        });
                    }
                    $(_this).find('button.bdt-button').attr("disabled", false);

                },
            });
        },
        load_recaptcha: function () {
            var reCaptchaFields = $('.element-pack-google-recaptcha'),
                widgetID;

            if (reCaptchaFields.length > 0) {
                reCaptchaFields.each(function () {
                    var self = $(this),
                        attrWidget = self.attr('data-widgetid');
                    // alert(self.data('sitekey'))
                    // Avoid re-rendering as it's throwing API error
                    if ((typeof attrWidget !== typeof undefined && attrWidget !== false)) {
                        return;
                    } else {
                        widgetID = grecaptcha.render($(this).attr('id'), {
                            sitekey: self.data('sitekey'),
                            callback: function (response) {
                                if (response !== '') {
                                    self.append(jQuery('<input>', {
                                        type: 'hidden',
                                        value: response,
                                        class: 'g-recaptcha-response'
                                    }));
                                }
                            }
                        });
                        self.attr('data-widgetid', widgetID);
                    }
                });
            }
        }

    }


    window.onLoadElementPackRegisterCaptcha = widgetUserRegistrationForm.load_recaptcha;

    var widgetUserRegisterForm = function ($scope, $) {
        var register_form = $scope.find('.bdt-user-register-widget'),
            recaptcha_field = $scope.find('.element-pack-google-recaptcha'),
            $userRegister = $scope.find('.bdt-user-register');

        // Perform AJAX register on form submit
        register_form.on('submit', function (e) {
            e.preventDefault();
            widgetUserRegistrationForm.registraitonFormSubmit(register_form, $scope)
        });

        if (elementorFrontend.isEditMode() && undefined === recaptcha_field.attr('data-widgetid')) {
            onLoadElementPackRegisterCaptcha();
        }

        if (recaptcha_field.length > 0) {
            grecaptcha.ready(function () {
                var recaptcha_id = recaptcha_field.attr('data-widgetid');
                grecaptcha.execute(recaptcha_id);
            });
        }

        var $settings = $userRegister.data('settings');

        if (!$settings || typeof $settings.passStrength === "undefined") {
            return;
        }

        console.log($settings);

        var percentage = 0,
            $selector = $('#' + $settings.id),
            $progressBar = $('#' + $settings.id).find('.bdt-progress-bar');

        var passStrength = {
            progress: function ($value = 0) {
                if ($value <= 100) {
                    $($progressBar).css({
                        'width': $value + '%'
                    });
                }
            },
            formula: function (input, length) {

                if (length < 6) {
                    percentage = 0;
                    $($progressBar).css('background', '#ff4d4d'); //red
                } else if (length < 8) {
                    percentage = 10;
                    $($progressBar).css('background', '#ffff1a'); //yellow
                } else if (input.match(/0|1|2|3|4|5|6|7|8|9/) == null && input.match(/[A-Z]/) == null) {
                    percentage = 40;
                    $($progressBar).css('background', '#ffc14d'); //orange
                }else{
                    if (length < 12){
                        percentage = 50;
                        $($progressBar).css('background', '#1aff1a'); //green
                    }else{
                        percentage = 60;
                        $($progressBar).css('background', '#1aff1a'); //green
                    }
                }


                //Lowercase Words only
                if ((input.match(/[a-z]/) != null)) {
                    percentage += 10;
                }

                //Uppercase Words only
                if ((input.match(/[A-Z]/) != null)) {
                    percentage += 10;
                }

                //Digits only
                if ((input.match(/0|1|2|3|4|5|6|7|8|9/) != null)) {
                    percentage += 10;
                }

                //Special characters
                if ((input.match(/\W/) != null) && (input.match(/\D/) != null)) {
                    percentage += 10;
                }
                return percentage;
            },
            forceStrongPass: function (result) {
                if (result >= 70) {
                    $($selector).find('.elementor-field-type-submit .bdt-button').prop('disabled', false);
                } else {
                    $($selector).find('.elementor-field-type-submit .bdt-button').prop('disabled', true);
                }
            },
            init: function () {
                $scope.find('.user_password').keyup(function () {
                    var input = $(this).val(),
                        length = input.length;
                    let result = passStrength.formula(input, length);
                    console.log(result);
                    passStrength.progress(result);

                    if (typeof $settings.forceStrongPass !== 'undefined') {
                        passStrength.forceStrongPass(result);
                    }
                });
                if (typeof $settings.forceStrongPass !== 'undefined') {
                    $($selector).find('.elementor-field-type-submit .bdt-button').prop('disabled', true);
                }
            }
        }

        passStrength.init();

    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-user-register.default', widgetUserRegisterForm);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-user-register.bdt-dropdown', widgetUserRegisterForm);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-user-register.bdt-modal', widgetUserRegisterForm);
    });

}(jQuery, window.elementorFrontend));

/**
 * End user register widget script
 */
/**
 * Start vertical menu widget script
 */

(function ($, elementor) {
    'use strict';
    // Vertical Menu
    var widgetVerticalMenu = function ($scope, $) {
        var $vrMenu = $scope.find('.bdt-vertical-menu');
        var $settings = $vrMenu.data('settings');
        if (!$vrMenu.length) {
            return;
        }

        //.attr("onclick", "event.stopPropagation();");
        $('#' + $settings.id).metisMenu();


        if ('yes' == $settings.removeParentLink) {
            $($vrMenu).find('.has-arrow').attr('href', 'javascript:void(0);')
            // $($vrMenu).find('.has-arrow').on('click', function () {
            //     return false;
            // });
        }
    }
    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-vertical-menu.default', widgetVerticalMenu);
    });


}(jQuery, window.elementorFrontend));

/**
 * End vertical menu widget script
 */
/**
 * Start video gallery widget script
 */

(function ($, elementor) {

	'use strict';

	var widgetVideoGallery = function ($scope, $) {

		var $video_gallery = $scope.find('.rvs-container');

		if (!$video_gallery.length) {
			return;
		}

		$($video_gallery).rvslider();

	};


	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-video-gallery.default', widgetVideoGallery);
	});

}(jQuery, window.elementorFrontend));

/**
 * End video gallery widget script
 */
(function ($, elementor) {
  "use strict";
  $(window).on("elementor/frontend/init", function () {
    var ModuleHandler = elementorModules.frontend.handlers.Base,
      WCProducts;

    WCProducts = ModuleHandler.extend({
      bindEvents: function () {
        this.run();
      },

      getDefaultSettings: function () {
        return {};
      },

      onElementChange: debounce(function (prop) {
        if (prop.indexOf("wc") !== -1) {
          this.run();
        }
      }, 400),

      settings: function (key) {
        return this.getElementSettings("wc_products_" + key);
      },

      run: function () {
        const options = this.getDefaultSettings(),
         content = this.settings("enable_ajax_loadmore"),
         container = this.$element.find(".bdt-wc-products");

        if (!container.length || content === undefined) {
          return;
        }

        const settingsLoadmore = this.settings("show_loadmore"),
         settingsInfiniteScroll = this.settings("show_infinite_scroll"),
         loadButtonContainer = this.$element.find(".bdt-loadmore-container"),
         products = container.find(".bdt-wc-products-wrapper"),
         loadButton = loadButtonContainer.find(".bdt-loadmore");
        let loading = false;
        const settings = container.data("settings");
        let currentItemCount = Number(settings.posts_per_page);

        const loadMorePosts = () => {
          const dataSettings = {
            action: "bdt_ep_wc_products_load_more",
            settings: settings,
            per_page: settings.ajax_item_load,
            offset: currentItemCount,
            nonce: settings.nonce,
            paged: settings.paged,
          };

          $.ajax({
            url: window.ElementPackConfig.ajaxurl,
            type: "post",
            data: dataSettings,
            success: (response) => {
              $(products).append(response.markup);
              currentItemCount += settings.ajax_item_load;
              settings.paged += 1;
              loading = false;

              if (settingsLoadmore === "yes") {
                loadButton.html("Load More");
              }

              if ($(response.markup).length < settings.ajax_item_load) {
                loadButton.hide();
                loadButtonContainer.hide();
              }
            },
          });
        };

        const handleButtonClick = () => {
          if (!loading) {
            loading = true;
            loadButton.html("Loading...");
            loadMorePosts();
          }
        };

        if (settingsLoadmore === "yes") {
          $(loadButton).on("click", handleButtonClick);
        }

        if (settingsInfiniteScroll === "yes") {
          $(window).scroll(() => {
            if (
              $(window).scrollTop() ===
                $(document).height() - $(window).height() &&
              !loading
            ) {
              $(loadButton).css("display", "block");
              loading = true;
              loadMorePosts();
            }
          });
        }
      },
    });
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/bdt-wc-products.default",
      function ($scope) {
        elementorFrontend.elementsHandler.addHandler(WCProducts, {
          $element: $scope,
        });
      }
    );
  });
})(jQuery, window.elementorFrontend);

/**
 * Start weather widget script
 */
 
(function($, elementor) {
    'use strict';
    var widgetWeather = function($scope, $) {
        var $weatherContainer = $scope.find('.bdt-weather');
        if (!$weatherContainer.length) {
            return;
        }
        var $settings = $weatherContainer.data('settings');
        var $weather = $($settings.id).closest('.elementor-widget-container');

        if ($settings.dynamicBG !== false){
            $($weather).css('background-image', 'url(' + $settings.url + ')');
            $($weather).css({
                'background-size': 'cover',
                'background-position': 'center center',
                'background-repeat': 'no-repeat'
            });
            
        }
        

    };

    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-weather.default', widgetWeather);
    });

}(jQuery, window.elementorFrontend));

/**
 * End weather widget script
 */
jQuery(document).ready(function () {
    jQuery('body').on('click', '.bdt-element-link', function () {
        var $el      = jQuery(this),
            settings = $el.data('ep-wrapper-link'),
            data     = settings,
            id   = 'bdt-element-link-' + $el.data('id');
            
        if (jQuery('#' + id).length === 0) {
            jQuery('body').append(
                jQuery(document.createElement('a')).prop({
                    target: data.is_external ? '_blank' : '_self',
                    href  : data.url,
                    class : 'bdt-hidden',
                    id    : id,
                    rel   : data.nofollow ? 'nofollow noreferer' : ''
                })
            );
        }

        jQuery('#' + id)[0].click();

    });
});

/**
 * Start remote arrows widget script
 */

;
(function ($, elementor) {
    'use strict';
    var widgetRemoteArrows = function ($scope, $) {
        var $remoteArrows = $scope.find('.bdt-remote-arrows'),
            $settings = $remoteArrows.data('settings'),
            editMode = Boolean(elementorFrontend.isEditMode());

        if (!$remoteArrows.length) {
            return;
        }

        if (!$settings.remoteId) {
            var $parentSection = $scope.closest('.elementor-section');
            $settings['remoteId'] = $parentSection;
        }

        if ($($settings.remoteId).find('.swiper-container, .swiper').length <= 0) {
            if (editMode == true) {
                $($settings.id + '-notice').removeClass('bdt-hidden');
            }
            return;
        }

        $($settings.id + '-notice').addClass('bdt-hidden');

        $(document).ready(function () {
            setTimeout(() => {
                const swiperInstance = $($settings.remoteId).find('.swiper-container, .swiper')[0].swiper;

                $($settings.id).find('.bdt-prev').on("click", function () {
                    swiperInstance.slidePrev();
                });

                $($settings.id).find('.bdt-next').on("click", function () {
                    swiperInstance.slideNext();
                });

            }, 3000);

        });

    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-remote-arrows.default', widgetRemoteArrows);
    });

}(jQuery, window.elementorFrontend));

/**
 * End remote arrows widget script
 */
/**
 * Start remote thumbs widget script
 */

;
(function ($, elementor) {
    'use strict';
    var widgetRemoteThumbs = function ($scope, $) {
        var $remoteThumbs = $scope.find('.bdt-remote-thumbs'),
            $settings = $remoteThumbs.data('settings'),
            editMode = Boolean(elementorFrontend.isEditMode());

        if (!$remoteThumbs.length) {
            return;
        }

        if (!$settings.remoteId) {
            // return;
            var $parentSection = $scope.closest('.elementor-section');

            $settings['remoteId'] = $parentSection;
        }

        if ($($settings.remoteId).find('.swiper-container, .swiper').length <= 0) {
            if (editMode == true) {
                $($settings.id + '-notice').removeClass('bdt-hidden');
            }
            return;
        }

        $($settings.id + '-notice').addClass('bdt-hidden');

        $(document).ready(function () {
            setTimeout(() => {
                const swiperInstance = $($settings.remoteId).find('.swiper-container, .swiper')[0].swiper;

                var $slideActive = $($settings.remoteId).find('.swiper-slide-active');
                var realIndex = $slideActive.data('swiper-slide-index')
                if (typeof realIndex === 'undefined') {
                    realIndex = $slideActive.index();
                }

                $($settings.id).find('.bdt-item:eq(' + realIndex + ')').addClass('bdt-active');

                $($settings.id).find('.bdt-item').on("click", function () {
                    var index = $(this).data('index');

                    if ($settings.loopStatus) {
                        swiperInstance.slideToLoop(index);
                    } else {
                        swiperInstance.slideTo(index);
                    }

                    $($settings.id).find('.bdt-item').removeClass('bdt-active');
                    $($settings.id).find('.bdt-item:eq(' + index + ')').addClass('bdt-active');
                    $($settings.id).addClass('wait--');

                });

                swiperInstance.on('slideChangeTransitionEnd', function (e) {
                    if ($($settings.id).hasClass('wait--')) {
                        $($settings.id).removeClass('wait--');
                        return;
                    } else {
                        $($settings.id).find('.bdt-item').removeClass('bdt-active');
                        $($settings.id).find('.bdt-item:eq(' + swiperInstance.realIndex + ')').addClass('bdt-active');
                        // console.log('*** mySwiper.activeIndex', swiperInstance.realIndex);
                    }

                });

            }, 2500);

        });
    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-remote-thumbs.default', widgetRemoteThumbs);
    });

}(jQuery, window.elementorFrontend));

/**
 * End remote thumbs widget script
 */
/**
 * Start remote pagination widget script
 */

;
(function ($, elementor) {
    'use strict';
    var widgetRemotePagination = function ($scope, $) {
        var $remotePagination = $scope.find('.bdt-remote-pagination'),
            $settings = $remotePagination.data('settings'),
            editMode = Boolean(elementorFrontend.isEditMode());

        if (!$remotePagination.length) {
            return;
        }

        if (!$settings.remoteId) {
            // return;
            // try to auto detect
            var $parentSection = $scope.closest('.elementor-section');

            $settings['remoteId'] = $parentSection;
        }

        if ($($settings.remoteId).find('.swiper-container, .swiper').length <= 0) {
            if (editMode == true) {
                $($settings.id + '-notice').removeClass('bdt-hidden');
            }
            return;
        }

        $($settings.id + '-notice').addClass('bdt-hidden');

        $(document).ready(function () {
            setTimeout(() => {
                const swiperInstance = $($settings.remoteId).find('.swiper-container, .swiper')[0].swiper;

                var $slideActive = $($settings.remoteId).find('.swiper-slide-active');
                var realIndex = $slideActive.data('swiper-slide-index')
                if (typeof realIndex === 'undefined') {
                    realIndex = $slideActive.index();
                }

                $($settings.id).find('.bdt-item:eq(' + realIndex + ')').addClass('bdt-active');

                $($settings.id).find('.bdt-item').on("click", function () {
                    var index = $(this).data('index');

                    if ($settings.loopStatus) {
                        swiperInstance.slideToLoop(index);
                    } else {
                        swiperInstance.slideTo(index);
                    }

                    $($settings.id).find('.bdt-item').removeClass('bdt-active');
                    $($settings.id).find('.bdt-item:eq(' + index + ')').addClass('bdt-active');
                    $($settings.id).addClass('wait--');

                });

                swiperInstance.on('slideChangeTransitionEnd', function (e) {
                    if ($($settings.id).hasClass('wait--')) {
                        $($settings.id).removeClass('wait--');
                        return;
                    } else {
                        $($settings.id).find('.bdt-item').removeClass('bdt-active');
                        $($settings.id).find('.bdt-item:eq(' + swiperInstance.realIndex + ')').addClass('bdt-active');
                        // console.log('*** mySwiper.activeIndex', swiperInstance.realIndex);
                    }

                });

            }, 2500);

        });
    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-remote-pagination.default', widgetRemotePagination);
    });

}(jQuery, window.elementorFrontend));

/**
 * End remote pagination widget script
 */
/**
 * Start remote fraction widget script
 */

;
(function ($, elementor) {
    'use strict';
    var widgetRemoteFraction = function ($scope, $) {
        var $remoteFraction = $scope.find('.bdt-remote-fraction'),
            $settings = $remoteFraction.data('settings'),
            $pad = $settings.pad,
            editMode = Boolean(elementorFrontend.isEditMode());

        if (!$remoteFraction.length) {
            return;
        }

        if (!$settings.remoteId) {
            // return;
            // try to auto detect
            var $parentSection = $scope.closest('.elementor-section');

            $settings['remoteId'] = $parentSection;
        }

        if ($($settings.remoteId).find('.swiper-container, .swiper').length <= 0) {
            if (editMode == true) {
                $($settings.id + '-notice').removeClass('bdt-hidden');
            }
            return;
        }

        $($settings.id + '-notice').addClass('bdt-hidden');

        $(document).ready(function () {
            setTimeout(() => {
                const swiperInstance = $($settings.remoteId).find('.swiper-container, .swiper')[0].swiper;

                var $slideActive = $($settings.remoteId).find('.swiper-slide-active');
                var realIndex = $slideActive.data('swiper-slide-index')
                if (typeof realIndex === 'undefined') {
                    realIndex = $slideActive.index();
                }

                var $totalSlides = $($settings.remoteId).find('.swiper-slide:not(.swiper-slide-duplicate)').length;
                $totalSlides = $totalSlides + '';
                realIndex = ((realIndex + 1) + '');
                
                $($settings.id).find('.bdt-current').text(realIndex.padStart($pad, "0"));
                $($settings.id).find('.bdt-total').text($totalSlides.padStart($pad, "0"));

                swiperInstance.on('slideChangeTransitionEnd', function (e) {
                    let item = swiperInstance.realIndex + 1 + '';
                    $($settings.id).find('.bdt-current').text(item.padStart($pad, "0"));
                });

            }, 2500);

        });
    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-remote-fraction.default', widgetRemoteFraction);
    });

}(jQuery, window.elementorFrontend));

/**
 * End remote fraction widget script
 */
jQuery(document).ready(function () {
    var $el       = jQuery('#ep-hash-link');

    if ($el.length <= 0) {
        return;
    }

    var $settings = $el.data('settings'),
        selector  = jQuery($settings.container).find($settings.selector);

    if (selector.length <= 0) {
        return;
    }

    jQuery(selector).addClass('ep-hash-link-inner-el');

    function ep_linker_builder(e) {
        var specialChars = "!@#$^&%*()+=-[]/{}|:<>?,.",
            rawText      = e,
            text         = rawText.replace(/\s+/g, "-").toLowerCase();
            text         = text.replace(new RegExp("\\" + specialChars, "g"), "");
        return text;
    }

    jQuery(selector).each(function (e) {
        var rawText = jQuery(this).text(),
            url     = ep_linker_builder(rawText);
        jQuery(this).wrapAll(
            '<a id="ep-hash-link-' +
            e +
            '" data-id="' +
            e +
            '" class="ep-hash-link" href="#' +
            e +
            "_" +
            url +
            '"/>'
        );
    });

    if (window.location.hash) {
        var hash = window.location.hash;
            hash = hash.slice(0, 2);
            hash = "ep-hash-link-" + hash.substring(1);
        jQuery("html, body").animate({
                scrollTop: jQuery("#" + hash).offset().top - 150
            },
            1000
        );
    }

});
/**
 * Start twitter carousel widget script
 */

(function ($, elementor) {

	'use strict';

	var widgetFbFeedCarousel = function ($scope, $) {

		var $fbCarousel = $scope.find('.bdt-facebook-feed-carousel');

		if (!$fbCarousel.length) {
			return;
		}

		var $fbCarouselContainer = $fbCarousel.find('.swiper-carousel'),
			$settings = $fbCarousel.data('settings');

		const Swiper = elementorFrontend.utils.swiper;
		initSwiper();
		async function initSwiper() {
			var swiper = await new Swiper($fbCarouselContainer, $settings); // this is an example
			if ($settings.pauseOnHover) {
				$($fbCarouselContainer).hover(function () {
					(this).swiper.autoplay.stop();
				}, function () {
					(this).swiper.autoplay.start();
				});
			}
		};
	};


	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-facebook-feed-carousel.default', widgetFbFeedCarousel);
	});

}(jQuery, window.elementorFrontend));

/**
 * End twitter carousel widget script
 */


/**
 * Start accordion widget script
 */

;
(function ($, elementor) {
    'use strict';
    var widgetCrypto = function ($scope, $) {
        var $cryptoWidget = $scope.find('.bdt-ep-crypto-currency-card'),
            $settings = $cryptoWidget.data('settings'),
            editMode = Boolean(elementorFrontend.isEditMode());

        if (!$cryptoWidget.length) {
            return;
        }

        var $options = {
            'currency': $settings.currency,
            'limit': 100,
            'order': 'market_cap_desc',
        };

        if ($settings.currency) {
            $options.currency = $settings.currency
        }
        // $options.limit = $settings.limit
        // if ($settings.limit) {
        // }
        if ($settings.order) {
            $options.order = $settings.order
        }

        let currency_selected, cryptoDataSettingsValue = $options;

        function getData() {
            $.ajax({
                type: "GET",
                // dataType: "json",
                url: ElementPackConfig.ajaxurl + '?action=ep_crypto_data',
                data: {
                    currency: currency_selected,
                    per_page: 1, //limit
                    order: cryptoDataSettingsValue.order,
                    ids: $settings.ids
                },

            }).done(function (data) {
                let itemData = $($cryptoWidget).find('.bdt-crypto-currency-card-item');
                /**
                 * @idPriceColumnArray is holding data from current items
                 */

                let idPriceColumnArray = [];
                for (let i = 0; i < itemData.length; i++) {
                    idPriceColumnArray.push({
                        // id: itemData[i]["id"],
                        id: $(itemData[i]).data('id'),
                        current_price: parseFloat($(itemData[i]).find('.bdt-price-text').text()),
                    });
                }

                // console.log(idPriceColumnArray);

                /**
                 * @crypDataParse holding data from crypto live data server
                 */
                let cryptDataParse = JSON.parse(data);
                /**
                 * changes array
                 */
                let changesIdArray = [];
                /**
                 * now have to compare this two array of object
                 */
                for (let i = 0; i < idPriceColumnArray.length; i++) {
                    $.map(cryptDataParse, function (elem, index) {
                        if (elem.id === idPriceColumnArray[i].id) {
                            // console.log(elem.current_price);
                            if (elem.current_price !== idPriceColumnArray[i].current_price) {
                                changesIdArray.push({
                                    id: idPriceColumnArray[i].id,
                                    current_price: elem.current_price,
                                    old_price: idPriceColumnArray[i].current_price,
                                });
                            }
                        }
                    });
                }

                // console.log(changesIdArray);

                if (changesIdArray.length !== 0) {
                    changesIdArray.forEach(element => {
                        $($cryptoWidget).find('[data-id="' + element.id + '"]').addClass('data-changed');
                        $($cryptoWidget).find('[data-id="' + element.id + '"]').find('.bdt-price-text').text(element.current_price);

                        let upperCaseCurrncyCode = currency_selected.toString().toUpperCase();
                        let amount = returnCurrencySymbol(upperCaseCurrncyCode) + element.current_price;
                        $($cryptoWidget).find('[data-id="' + element.id + '"] .price-int').text(amount);

                    });
                }
                

                setTimeout(function () {
                    $($cryptoWidget).find('.bdt-crypto-currency-card-item').removeClass('data-changed');
                    return getData();
                }, 10000);
            });
        }

        /**
         * number format
         */
        function numFormatter(num) {
            if (num > 999 && num < 1000000) {
                return (num / 1000).toFixed(2) + 'K'; // convert to K for number from > 1000 < 1 million 
            } else if (num > 1000000000) {
                return (num / 1000000000).toFixed(2) + 'B'; // convert to M for number from > 1 million 
            } else if (num > 1000000) {
                return (num / 1000000).toFixed(2) + 'M'; // convert to M for number from > 1 million 
            } else if (num < 900) {
                return num; // if value < 1000, nothing to do
            }
        }
        /**
         * defalt onload call will be here
         */
        if (cryptoDataSettingsValue !== undefined && cryptoDataSettingsValue.currency !== undefined) {
            currency_selected = cryptoDataSettingsValue.currency;
        } else {
            currency_selected = "usd"; // default currency settings here
        }

        $.ajax({
            type: "GET",
            dataType: "json",
            url: ElementPackConfig.ajaxurl + '?action=ep_crypto',
            data: {
                currency: currency_selected,
                per_page: cryptoDataSettingsValue.limit, //limit
                order: cryptoDataSettingsValue.order,
                ids: $settings.ids
            },
            success: function (result) {
                $($cryptoWidget).empty();

                if (typeof result.data !== "undefined" && result.data.length > 0 && true === result.apiErrors) {
                    let output = `<div class="bdt-alert-danger" bdt-alert>
                                    <a class="bdt-alert-close" bdt-close></a>
                                    <p>${result.data}</p>
                                </div>`;
                    $($cryptoWidget).append(output);
                    return;
                }


                var count = 0;
                result.data.forEach(element => {
                    count++;
                    if (count > 1) {
                        return;
                    }
                    // console.log(element.id);
                    let upperCaseCurrncyCode = currency_selected.toString().toUpperCase();
                    // let formatAmount = numFormatter(element.current_price);
                    let amount = returnCurrencySymbol(upperCaseCurrncyCode) + element.current_price;

                    let data = element.price_change_percentage_1h;
                    let OneHourData = (Number(data) === data && data % 1 !== 0) ? data.toFixed(2) + "%" : data + "%";

                    var img_html = '';
                    var name_html = '';
                    var symble_html = '';
                    var price_html = '';
                    var hourly_price_html = '';
                    var market_cap_rank_html = '';
                    var market_cap_html = '';
                    var total_volume_html = '';
                    var price_change_html = '';
                    if (true == $settings.showCurrencyImage) {
                        img_html = `<div class="bdt-ep-currency-image">
                        <img src="${element.image}" alt="${element.id}">
                    </div>`;
                    }
                    if (true == $settings.showCurrencyShortName) {
                        symble_html = `<div class="bdt-ep-currency-short-name">
                        <span>${element.symbol}</span>
                    </div>`;
                    }
                    if (true == $settings.showCurrencyName) {
                        name_html = `<div class="bdt-crypto-name-wrap"><div class="bdt-ep-currency-name">
                        <span>${element.id}</span>
                    </div> ${symble_html}</div>`;
                    }
                    if (true == $settings.showCurrencyChangePrice) {
                        hourly_price_html = `<div class="bdt-percentage" title="1 Hour Data Change">${OneHourData}</div>`;
                    }
                    if (true == $settings.showCurrencyCurrentPrice) {
                        price_html = `<div class="bdt-width-1-1 bdt-width-1-2@s"><div class="bdt-ep-current-price">
                        <div class="bdt-price">${amount}</div>
                        ${hourly_price_html}
                    </div></div>`;
                    }
                    if (true == $settings.showMarketCapRank) {
                        market_cap_rank_html = `<div class="bdt-ep-ccc-atribute">
                        <span class="bdt-ep-item-text">Market Cap Rank: </span>
                        <span>#${element.market_cap_rank}</span>
                    </div>`;
                    }
                    if (true == $settings.showMarketCap) {
                        market_cap_html = `<div class="bdt-ep-ccc-atribute">
                        <span class="bdt-ep-item-text">Market Cap: </span>
                        <span>${element.market_cap}</span>
                    </div>`;
                    }
                    if (true == $settings.showTotalVolume) {
                        total_volume_html = `<div class="bdt-ep-ccc-atribute">
                        <span class="bdt-ep-item-text">Total Volume: </span>
                        <span>${element.total_volume}</span>
                    </div>`;
                    }
                    if (true == $settings.showPriceChange) {
                        price_change_html = `<div class="bdt-ep-ccc-atribute">
                        <span class="bdt-ep-item-text">24H Change(%): </span>
                        <span>${element.price_change_percentage_24h}</span>
                    </div>`;
                    }

                    var output = `<div class="bdt-grid" bdt-grid data-id="${element.id}">

                                    <div class="bdt-width-1-1 bdt-width-1-2@s">
                                        <div class="bdt-ep-currency">
                                            ${img_html}
                                            ${name_html}
                                        </div>
                                    </div>
                                    
                                    ${price_html}
                                    
                                    <div class="bdt-width-1-1 bdt-margin-small-top bdt-ep-ccc-atributes bdt-grid-margin bdt-first-column">

                                        ${market_cap_rank_html}

                                        ${market_cap_html}

                                        ${total_volume_html}

                                        ${price_change_html}

                                    </div>
                                
                                <span class="bdt-price-text bdt-hidden">${element.current_price}</span>
                            </div>`;

                    $($cryptoWidget).append(output);

                });
            }
        });

        if (true !== editMode) {
            setTimeout(function () {
                getData();
            }, 5000);
        }

    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-crypto-currency-card.default', widgetCrypto);
    });

}(jQuery, window.elementorFrontend));

/**
 * End accordion widget script
 */
/**
 * Start accordion widget script
 */

;
(function ($, elementor) {
    'use strict';
    var widgetCrypto = function ($scope, $) {
        var $cryptoWidget = $scope.find('.bdt-crypto-currency-table'),
            $settings = $cryptoWidget.data('settings'),
            editMode = Boolean(elementorFrontend.isEditMode());

        if (!$cryptoWidget.length) {
            return;
        }

        

        var $options = {
            'currency': $settings.currency,
            'limit': 100,
            'order': 'market_cap_desc',
        };

        if ($settings.currency) {
            $options.currency = $settings.currency
        }
        if ($settings.limit) {
            $options.limit = $settings.limit
        }
        if ($settings.order) {
            $options.order = $settings.order
        }

        let table, currency_selected, cryptoDataSettingsValue = $options;

        function getData() {
            $.ajax({
                type: "GET",
                // dataType: "json",
                url: ElementPackConfig.ajaxurl + '?action=ep_crypto_data',
                data: {
                    currency: currency_selected,
                    per_page: cryptoDataSettingsValue.limit, //limit
                    order: cryptoDataSettingsValue.order,
                    ids: $settings.ids
                },

            }).done(function (data) {
                var tableData = table.rows().data();
                /**
                 * @idPriceColumnArray is holding data from current datatable
                 */
                let idPriceColumnArray = [];
                for (let i = 0; i < tableData.length; i++) {
                    idPriceColumnArray.push({
                        id: tableData[i]["id"],
                        current_price: tableData[i]["current_price"],
                    });
                }
                /**
                 * @crypDataParse holding data from crypto live data server
                 */
                let cryptDataParse = JSON.parse(data);
                /**
                 * changes array
                 */
                let changesIdArray = [];
                /**
                 * now have to compare this two array of object
                 */
                for (let i = 0; i < idPriceColumnArray.length; i++) {
                    $.map(cryptDataParse, function (elem, index) {
                        if (elem.id === idPriceColumnArray[i].id) {
                            if (elem.current_price !== idPriceColumnArray[i].current_price) {
                                changesIdArray.push({
                                    id: idPriceColumnArray[i].id,
                                    current_price: elem.current_price,
                                    old_price: idPriceColumnArray[i].current_price,
                                });
                            }
                        }
                    });
                }

                changesIdArray.filter(function (valueChangesArray, index) {
                    let foundindex = 0;
                    var filteredData = table.column(1)
                        .data()
                        .filter(function (value, index) {
                            if (value === valueChangesArray.id) {
                                foundindex = index;
                                table.column(2)
                                    .nodes()
                                    .each(function (node, colIndex, dt) {
                                        if (colIndex === index) {
                                            table.cell(node)
                                                .data(valueChangesArray.current_price);
                                            let nodes = table.column(2).nodes();
                                            $(nodes[index]).addClass("focus-item");
                                        }
                                    });
                            }
                            return value === valueChangesArray.id ? true : false;
                        });
                });
                setTimeout(function () {
                    table.column(2)
                        .nodes()
                        .each(function () {
                            $(this).removeClass("focus-item");
                        });
                    return getData();
                }, 10000);
            });
        }

        /**
         * number format
         */
        function numFormatter(num) {
            if (num > 999 && num < 1000000) {
                return (num / 1000).toFixed(2) + 'K'; // convert to K for number from > 1000 < 1 million 
            } else if (num > 1000000000) {
                return (num / 1000000000).toFixed(2) + 'B'; // convert to M for number from > 1 million 
            } else if (num > 1000000) {
                return (num / 1000000).toFixed(2) + 'M'; // convert to M for number from > 1 million 
            } else if (num < 900) {
                return num; // if value < 1000, nothing to do
            }
        }
        /**
         * defalt onload call will be here
         */
        if (cryptoDataSettingsValue !== undefined && cryptoDataSettingsValue.currency !== undefined) {
            currency_selected = cryptoDataSettingsValue.currency;
        } else {
            currency_selected = "usd"; // default currency settings here
        }
        table = $($settings.tableId).DataTable({
            language : window.ElementPackConfig.data_table.language, // default language override for proper translation
            destroy: true, // fixed the Alert issue in Edit mode
            processing: true,
            serverSide: false,
            searching: $settings.searching,
            ordering: $settings.ordering,
            paging: $settings.paging,
            info: $settings.info,
            pageLength: $settings.pageLength,
            ajax: {
                type: "GET",
                dataType: "json",
                url: ElementPackConfig.ajaxurl + '?action=ep_crypto',
                data: {
                    currency: currency_selected,
                    per_page: cryptoDataSettingsValue.limit, //limit
                    order: cryptoDataSettingsValue.order,
                    ids: $settings.ids
                },
            },
            columns: [{
                    data: "market_cap_rank"
                },
                {
                    data: "id",
                    render: function (data, type, row, meta) {
                        return (
                            '<div class="bdt-coin"><div class="bdt-coin-image"><img src="' +
                            row["image"] +
                            '" alt="' +
                            row["id"] +
                            '"></div><div class="bdt-coin-title"><div class="bdt-coin-name">' +
                            row["id"] +
                            '</div><div class="bdt-coin-symbol">' +
                            row["symbol"] +
                            "</div></div></div>"
                        );
                    },
                },
                {
                    data: "current_price",
                    render: function (data, type, row, meta) {
                        let upperCaseCurrncyCode = currency_selected.toString().toUpperCase();
                        let val = Number(data) === data && data % 1 !== 0 ? data.toFixed(2) : data; // this is just we are checking if this value is float. then we are just adjusting the two decimal point. otherwise returing the original value
                        //return returnCurrencySymbol(upperCaseCurrncyCode) + data;
                        return returnCurrencySymbol(upperCaseCurrncyCode) + val;
                    },
                },
                {
                    data: "price_change_percentage_24h",
                    render: function (data, type, row, meta) {
                        return Number(data) === data && data % 1 !== 0 ? data.toFixed(2) + "%" : data + "%";
                    },
                },
                {
                    data: "market_cap",
                    render: function (data, type, row, meta) {
                        let upperCaseCurrncyCode = currency_selected.toString().toUpperCase();
                        let formatAmout = numFormatter(data);
                        //return returnCurrencySymbol(upperCaseCurrncyCode) + data;
                        return returnCurrencySymbol(upperCaseCurrncyCode) + formatAmout;
                    },
                },
                {
                    data: "total_volume",
                    render: function (data, type, row, meta) {
                        let upperCaseCurrncyCode = currency_selected.toString().toUpperCase();
                        let formatAmout = numFormatter(data);
                        //return returnCurrencySymbol(upperCaseCurrncyCode) + data;
                        return returnCurrencySymbol(upperCaseCurrncyCode) + formatAmout;
                    },
                },
                {
                    data: "circulating_supply",
                    render: function (data, type, row, mata) {
                        let formatAmout = numFormatter(data);
                        //return data.toFixed(2);
                        return formatAmout;
                    }
                },
                {
                    data: "last_seven_days_changes",
                    render: function (data, type, row, meta) {
                        return (
                            '<input type="hidden" class="hdnInputCanvas-' + row["id"] + '"  value="' + data + '"/>' + '<div class="chart-container" style="position: relative; height: 100%; width: 250px"><canvas id="canvas-' + row["id"] + '"></canvas></div>'
                        );
                    },
                },
            ],
            columnDefs: [{
                searchable: false,
                orderable: false,
                //targets: [0, 8],
                targets: [7],
            }, ],
            //order: [[1, "asc"]],
            order: [
                [0, "asc"]
            ],
            createdRow: function (row, data, index) {
                let getCanvasElement = $("td", row).eq(7);
                //let canvas_id = $(getCanvasElement).find("canvas").attr("id");
                let getHiddenData = $(getCanvasElement).find("input").val();
                let splitData = getHiddenData.split(",");
                /***
                 * here we are just getting last 20 values value
                 */
                if (splitData && splitData.length > 15) {
                    splitData = splitData.slice(0, 14);
                }
                /**
                 * end of splice code. this we can remove if any further code found
                 */
                const dom_canvas_element = $(getCanvasElement).find("canvas");
                const labels = [],
                    dataPointvalue = [];
                splitData.forEach((element, index) => {
                    labels.push(index);
                    dataPointvalue.push(Number(element));
                });


                // gradient color chart
                

                const dataCharts = {
                    labels: labels,
                    datasets: [{
                        label: "",
                        backgroundColor: "rgba(30,135,240,0.2)",
                        borderColor: "#1e87f0",
                        fill: true,
                        lineTension: 0.4,
                        pointStyle: 'circle',
                        pointBackgroundColor: "transparent",
                        pointBorderWidth: 0,
                        borderWidth: 2,
                        data: dataPointvalue,
                    }, ],
                };
                const config = {
                    type: "line",
                    data: dataCharts,
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false,
                            },
                            tooltip: {
                                enabled: true,
                                callbacks: {
                                    title: () => ''
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    display: false,
                                },
                                grid: {
                                    display: false,
                                    drawBorder: false,
                                    drawOnChartArea: false,
                                    drawTicks: false,
                                },
                            },
                            y: {
                                ticks: {
                                    display: false,
                                },
                                grid: {
                                    display: false,
                                    drawBorder: false,
                                    drawOnChartArea: false,
                                    drawTicks: false,
                                },
                            },
                        },
                    },
                };
                const chart = new Chart(dom_canvas_element, config);
                chart.canvas.parentNode.style.width = "100%";
                chart.canvas.parentNode.style.height = "60px";
                chart.canvas.style.width = "100%";
                chart.canvas.style.height = "60px";
            },
        });

        if (true !== editMode) {
            setTimeout(function () {
                getData();
            }, 5000);
        }

    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-crypto-currency-table.default', widgetCrypto);
    });

}(jQuery, window.elementorFrontend));

/**
 * End accordion widget script
 */
/**
 * Start accordion widget script
 */

;
(function ($, elementor) {
    'use strict';
    var widgetCrypto = function ($scope, $) {
        var $cryptoWidget = $scope.find('.bdt-crypto-currency-grid'),
            $settings = $cryptoWidget.data('settings'),
            editMode = Boolean(elementorFrontend.isEditMode());

        if (!$cryptoWidget.length) {
            return;
        }

        var $options = {
            'currency': $settings.currency,
            'limit': 100,
            'order': 'market_cap_desc',
        };

        if ($settings.currency) {
            $options.currency = $settings.currency
        }
        if ($settings.limit) {
            $options.limit = $settings.limit
        }
        if ($settings.order) {
            $options.order = $settings.order
        }

        let currency_selected, cryptoDataSettingsValue = $options;

        function getData() {
            $.ajax({
                type: "GET",
                // dataType: "json",
                url: ElementPackConfig.ajaxurl + '?action=ep_crypto_data',
                data: {
                    currency: currency_selected,
                    per_page: cryptoDataSettingsValue.limit, //limit
                    order: cryptoDataSettingsValue.order,
                    ids: $settings.ids
                },

            }).done(function (data) {
                let itemData = $($cryptoWidget).find('.bdt-crypto-currency-grid-item');
                /**
                 * @idPriceColumnArray is holding data from current items
                 */

                let idPriceColumnArray = [];
                for (let i = 0; i < itemData.length; i++) {
                    idPriceColumnArray.push({
                        // id: itemData[i]["id"],
                        id: $(itemData[i]).data('id'),
                        current_price: parseFloat($(itemData[i]).find('.bdt-price-text').text()),
                    });
                }

                // console.log(idPriceColumnArray);

                /**
                 * @crypDataParse holding data from crypto live data server
                 */

                let cryptDataParse = JSON.parse(data);
                /**
                 * changes array
                 */
                let changesIdArray = [];
                /**
                 * now have to compare this two array of object
                 */
                for (let i = 0; i < idPriceColumnArray.length; i++) {
                    $.map(cryptDataParse, function (elem, index) {
                        if (elem.id === idPriceColumnArray[i].id) {
                            // console.log(elem.current_price);
                            if (elem.current_price !== idPriceColumnArray[i].current_price) {
                                changesIdArray.push({
                                    id: idPriceColumnArray[i].id,
                                    current_price: elem.current_price,
                                    old_price: idPriceColumnArray[i].current_price,
                                });
                            }
                        }
                    });
                }

                // console.log(changesIdArray);

                if (changesIdArray.length !== 0) {
                    changesIdArray.forEach(element => {
                        $($cryptoWidget).find('[data-id="' + element.id + '"]').addClass('data-changed');
                        $($cryptoWidget).find('[data-id="' + element.id + '"]').find('.bdt-price-text').text(element.current_price);

                        let upperCaseCurrncyCode = currency_selected.toString().toUpperCase();
                        let amount = returnCurrencySymbol(upperCaseCurrncyCode) + element.current_price;
                        $($cryptoWidget).find('[data-id="' + element.id + '"] .price-int').text(amount);

                    });
                }


                setTimeout(function () {
                    $($cryptoWidget).find('.bdt-crypto-currency-grid-item').removeClass('data-changed');
                    return getData();
                }, 10000);
            });
        }

        /**
         * number format
         */
        function numFormatter(num) {
            if (num > 999 && num < 1000000) {
                return (num / 1000).toFixed(2) + 'K'; // convert to K for number from > 1000 < 1 million 
            } else if (num > 1000000000) {
                return (num / 1000000000).toFixed(2) + 'B'; // convert to M for number from > 1 million 
            } else if (num > 1000000) {
                return (num / 1000000).toFixed(2) + 'M'; // convert to M for number from > 1 million 
            } else if (num < 900) {
                return num; // if value < 1000, nothing to do
            }
        }
        /**
         * defalt onload call will be here
         */
        if (cryptoDataSettingsValue !== undefined && cryptoDataSettingsValue.currency !== undefined) {
            currency_selected = cryptoDataSettingsValue.currency;
        } else {
            currency_selected = "usd"; // default currency settings here
        }

        $.ajax({
            type: "GET",
            dataType: "json",
            url: ElementPackConfig.ajaxurl + '?action=ep_crypto',
            data: {
                currency: currency_selected,
                per_page: cryptoDataSettingsValue.limit, //limit
                order: cryptoDataSettingsValue.order,
                ids: $settings.ids
            },
            success: function (result) {
                $($cryptoWidget).empty();

                
                if (typeof result.data !== "undefined" && result.data.length > 0 && true === result.apiErrors) {
                    let output = `<div class="bdt-alert-danger" bdt-alert>
                                    <a class="bdt-alert-close" bdt-close></a>
                                    <p>${result.data}</p>
                                </div>`;
                    $($cryptoWidget).append(output);
                    return;
                }

                var count = 0;
                result.data.forEach(element => {
                    count++;
                    if (count > cryptoDataSettingsValue.limit) {
                        return;
                    }
                    // console.log(element.id);
                    let upperCaseCurrncyCode = currency_selected.toString().toUpperCase();
                    // let formatAmount = numFormatter(element.current_price);
                    let amount = returnCurrencySymbol(upperCaseCurrncyCode) + element.current_price;

                    var img_html = '';
                    var name_html = '';
                    var symble_html = '';
                    var price_html = '';
                    var price_label_html = '';
                    if (true == $settings.showCurrencyImage) {
                        img_html = `<div class="bdt-crypto-currency-grid-img">
                                        <img src="${element.image}" alt="${element.id}">
                                    </div>`;
                    }
                    if (true == $settings.showCurrencyShortName) {
                        symble_html = `<span>(${element.symbol})</span>`;
                    }
                    if (true == $settings.showCurrencyName) {
                        name_html = `<div class="bdt-crypto-currency-grid-title">
                        <h4>${element.id} ${symble_html}</h4>
                    </div>`;
                    }
                    if (true == $settings.showCurrencyPriceLabel) {
                        price_label_html = `<div class="bdt-crypto-currency-grid-price-text">
                        <span>price</span>
                    </div>`;
                    }
                    if (true == $settings.showCurrencyCurrentPrice) {
                        price_html = `${price_label_html}
                    <div class="bdt-crypto-currency-grid-price-nu">
                        <span class="price-int">${amount}</span>
                    </div>`;
                    }

                    var output = `<div class="bdt-crypto-currency-grid-item" data-id="${element.id}">
                                <div class="bdt-crypto-currency-grid-content">
                                    <div class="bdt-crypto-currency-grid-bg">
                                        <img src="${element.image}" alt="${element.id}">
                                    </div>

                                    <div class="bdt-crypto-currency-grid-head-content">
                                        ${img_html}
                                        ${name_html}
                                    </div>
                                    <div class="bdt-crypto-currency-grid-bottom-content">
                                        ${price_html}
                                    </div>
                                </div>
                                <span class="bdt-price-text bdt-hidden">${element.current_price}</span>
                            </div>`;

                    $($cryptoWidget).append(output);

                });
            }
        });

        if (true !== editMode) {
            setTimeout(function () {
                getData();
            }, 5000);
        }

    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-crypto-currency-grid.default', widgetCrypto);
    });

}(jQuery, window.elementorFrontend));

/**
 * End accordion widget script
 */
/**
 * Start accordion widget script
 */

;
(function ($, elementor) {
    'use strict';
    var widgetCrypto = function ($scope, $) {
        var $cryptoWidget = $scope.find('.bdt-crypto-currency-list'),
            $settings = $cryptoWidget.data('settings'),
            editMode = Boolean(elementorFrontend.isEditMode());

        if (!$cryptoWidget.length) {
            return;
        }

        var $options = {
            'currency': $settings.currency,
            'limit': 100,
            'order': 'market_cap_desc',
        };

        if ($settings.currency) {
            $options.currency = $settings.currency
        }
        if ($settings.limit) {
            $options.limit = $settings.limit
        }
        if ($settings.order) {
            $options.order = $settings.order
        }

        let currency_selected, cryptoDataSettingsValue = $options;

        function getData() {
            $.ajax({
                type: "GET",
                // dataType: "json",
                url: ElementPackConfig.ajaxurl + '?action=ep_crypto_data',
                data: {
                    currency: currency_selected,
                    per_page: cryptoDataSettingsValue.limit, //limit
                    order: cryptoDataSettingsValue.order,
                    ids: $settings.ids
                },

            }).done(function (data) {
                let itemData = $($cryptoWidget).find('.bdt-crypto-currency-list-item');
                /**
                 * @idPriceColumnArray is holding data from current items
                 */

                let idPriceColumnArray = [];
                for (let i = 0; i < itemData.length; i++) {
                    idPriceColumnArray.push({
                        // id: itemData[i]["id"],
                        id: $(itemData[i]).data('id'),
                        current_price: parseFloat($(itemData[i]).find('.bdt-price-text').text()),
                    });
                }

                // console.log(idPriceColumnArray);

                /**
                 * @crypDataParse holding data from crypto live data server
                 */
                let cryptDataParse = JSON.parse(data);
                /**
                 * changes array
                 */
                let changesIdArray = [];
                /**
                 * now have to compare this two array of object
                 */
                for (let i = 0; i < idPriceColumnArray.length; i++) {
                    $.map(cryptDataParse, function (elem, index) {
                        if (elem.id === idPriceColumnArray[i].id) {
                            // console.log(elem.current_price);
                            if (elem.current_price !== idPriceColumnArray[i].current_price) {
                                changesIdArray.push({
                                    id: idPriceColumnArray[i].id,
                                    current_price: elem.current_price,
                                    old_price: idPriceColumnArray[i].current_price,
                                });
                            }
                        }
                    });
                }

                // console.log(changesIdArray);

                if (changesIdArray.length !== 0) {
                    changesIdArray.forEach(element => {
                        $($cryptoWidget).find('[data-id="' + element.id + '"]').addClass('data-changed');
                        $($cryptoWidget).find('[data-id="' + element.id + '"]').find('.bdt-price-text').text(element.current_price);

                        let upperCaseCurrncyCode = currency_selected.toString().toUpperCase();
                        let amount = returnCurrencySymbol(upperCaseCurrncyCode) + element.current_price;
                        $($cryptoWidget).find('[data-id="' + element.id + '"] .price-int').text(amount);

                    });
                }


                setTimeout(function () {
                    $($cryptoWidget).find('.bdt-crypto-currency-list-item').removeClass('data-changed');
                    return getData();
                }, 10000);
            });
        }

        /**
         * number format
         */
        function numFormatter(num) {
            if (num > 999 && num < 1000000) {
                return (num / 1000).toFixed(2) + 'K'; // convert to K for number from > 1000 < 1 million 
            } else if (num > 1000000000) {
                return (num / 1000000000).toFixed(2) + 'B'; // convert to M for number from > 1 million 
            } else if (num > 1000000) {
                return (num / 1000000).toFixed(2) + 'M'; // convert to M for number from > 1 million 
            } else if (num < 900) {
                return num; // if value < 1000, nothing to do
            }
        }
        /**
         * defalt onload call will be here
         */
        if (cryptoDataSettingsValue !== undefined && cryptoDataSettingsValue.currency !== undefined) {
            currency_selected = cryptoDataSettingsValue.currency;
        } else {
            currency_selected = "usd"; // default currency settings here
        }

        $.ajax({
            type: "GET",
            dataType: "json",
            url: ElementPackConfig.ajaxurl + '?action=ep_crypto',
            data: {
                currency: currency_selected,
                per_page: cryptoDataSettingsValue.limit, //limit
                order: cryptoDataSettingsValue.order,
                ids: $settings.ids
            },
            success: function (result) {
                $($cryptoWidget).empty();
                var count = 0;
                result.data.forEach(element => {
                    count++;
                    if (count > cryptoDataSettingsValue.limit) {
                        return;
                    }
                    // console.log(element.id);
                    let upperCaseCurrncyCode = currency_selected.toString().toUpperCase();
                    // let formatAmount = numFormatter(element.current_price);
                    let amount = returnCurrencySymbol(upperCaseCurrncyCode) + element.current_price;

                    var img_html = '';
                    var name_html = '';
                    var symble_html = '';
                    var price_html = '';
                    if (true == $settings.showCurrencyImage) {
                        img_html = `<div class="bdt-crypto-currency-list-img">
                        <img src="${element.image}" alt="${element.id}">
                    </div>`;
                    }
                    if (true == $settings.showCurrencyShortName) {
                        symble_html = `<span>(${element.symbol})</span>`;
                    }
                    if (true == $settings.showCurrencyName) {
                        name_html = `<div class="bdt-crypto-currency-list-title">
                        ${element.id} ${symble_html}
                    </div>`;
                    }
                    if (true == $settings.showCurrencyCurrentPrice) {
                        price_html = `<div class="bdt-crypto-currency-list-price">
                        <span>${amount}</span>
                    </div>`;
                    }

                    var output = `<div class="bdt-crypto-currency-list-item" data-id="${element.id}">

                                <div class="bdt-crypto-currency-list-content">
                                    <div class="bdt-crypto-currency-list-inner">
                                        ${img_html}
                                        ${name_html}
                                    </div>
                                    ${price_html}
                                </div>

                                <span class="bdt-price-text bdt-hidden">${element.current_price}</span>
                             </div>`;

                    $($cryptoWidget).append(output);

                });
            }
        });

        if (true !== editMode) {
            setTimeout(function () {
                getData();
            }, 5000);
        }

    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-crypto-currency-list.default', widgetCrypto);
    });

}(jQuery, window.elementorFrontend));

/**
 * End accordion widget script
 */
(function ($, elementor) {

    'use strict';

    var widgetCrypto = function ($scope, $) {

        var $carousel = $scope.find('.bdt-crypto-currency-carousel'),
            $cryptoWidget = $scope.find('.bdt-crypto-currency-carousel'),
            $settings = $cryptoWidget.data('crypto-settings'),
            editMode = Boolean(elementorFrontend.isEditMode());

        if (!$carousel.length) {
            return;
        }

        var $options = {
            'currency': $settings.currency,
            'limit': 100,
            'order': 'market_cap_desc',
        };

        if ($settings.currency) {
            $options.currency = $settings.currency
        }
        if ($settings.limit) {
            $options.limit = $settings.limit
        }
        if ($settings.order) {
            $options.order = $settings.order
        }

        let currency_selected, cryptoDataSettingsValue = $options;

        function getData() {
            $.ajax({
                type: "GET",
                // dataType: "json",
                url: ElementPackConfig.ajaxurl + '?action=ep_crypto_data',
                data: {
                    currency: currency_selected,
                    per_page: cryptoDataSettingsValue.limit, //limit
                    order: cryptoDataSettingsValue.order,
                    ids: $settings.ids
                },

            }).done(function (data) {
                let itemData = $($cryptoWidget).find('.bdt-crypto-currency-carousel-item');
                /**
                 * @idPriceColumnArray is holding data from current items
                 */

                let idPriceColumnArray = [];
                for (let i = 0; i < itemData.length; i++) {
                    idPriceColumnArray.push({
                        // id: itemData[i]["id"],
                        id: $(itemData[i]).data('id'),
                        current_price: parseFloat($(itemData[i]).find('.bdt-price-text').text()),
                    });
                }

                // console.log(idPriceColumnArray);

                /**
                 * @crypDataParse holding data from crypto live data server
                 */
                let cryptDataParse = JSON.parse(data);
                /**
                 * changes array
                 */
                let changesIdArray = [];
                /**
                 * now have to compare this two array of object
                 */
                for (let i = 0; i < idPriceColumnArray.length; i++) {
                    $.map(cryptDataParse, function (elem, index) {
                        if (elem.id === idPriceColumnArray[i].id) {
                            // console.log(elem.current_price);
                            if (elem.current_price !== idPriceColumnArray[i].current_price) {
                                changesIdArray.push({
                                    id: idPriceColumnArray[i].id,
                                    current_price: elem.current_price,
                                    old_price: idPriceColumnArray[i].current_price,
                                });
                            }
                        }
                    });
                }

                // console.log(changesIdArray);

                if (changesIdArray.length !== 0) {
                    changesIdArray.forEach(element => {
                        $($cryptoWidget).find('[data-id="' + element.id + '"]').addClass('data-changed');
                        $($cryptoWidget).find('[data-id="' + element.id + '"]').find('.bdt-price-text').text(element.current_price);

                        let upperCaseCurrncyCode = currency_selected.toString().toUpperCase();
                        let amount = returnCurrencySymbol(upperCaseCurrncyCode) + element.current_price;
                        $($cryptoWidget).find('[data-id="' + element.id + '"] .price-int').text(amount);

                    });
                }


                setTimeout(function () {
                    $($cryptoWidget).find('.bdt-crypto-currency-carousel-item').removeClass('data-changed');
                    return getData();
                }, 10000);
            });
        }

        /**
         * number format
         */
        function numFormatter(num) {
            if (num > 999 && num < 1000000) {
                return (num / 1000).toFixed(2) + 'K'; // convert to K for number from > 1000 < 1 million 
            } else if (num > 1000000000) {
                return (num / 1000000000).toFixed(2) + 'B'; // convert to M for number from > 1 million 
            } else if (num > 1000000) {
                return (num / 1000000).toFixed(2) + 'M'; // convert to M for number from > 1 million 
            } else if (num < 900) {
                return num; // if value < 1000, nothing to do
            }
        }
        /**
         * defalt onload call will be here
         */
        if (cryptoDataSettingsValue !== undefined && cryptoDataSettingsValue.currency !== undefined) {
            currency_selected = cryptoDataSettingsValue.currency;
        } else {
            currency_selected = "usd"; // default currency settings here
        }

        $.ajax({
            type: "GET",
            dataType: "json",
            url: ElementPackConfig.ajaxurl + '?action=ep_crypto',
            data: {
                currency: currency_selected,
                per_page: cryptoDataSettingsValue.limit, //limit
                order: cryptoDataSettingsValue.order,
                ids: $settings.ids
            },
            success: function (result) {
                $($cryptoWidget).find('.swiper-wrapper').empty();
                var count = 0;
                result.data.forEach(element => {
                    count++;
                    if (count > cryptoDataSettingsValue.limit) {
                        return;
                    }
                    // console.log(element.id);
                    let upperCaseCurrncyCode = currency_selected.toString().toUpperCase();
                    // let formatAmount = numFormatter(element.current_price);
                    let amount = returnCurrencySymbol(upperCaseCurrncyCode) + element.current_price;

                    var img_html = '';
                    var name_html = '';
                    var symble_html = '';
                    var price_html = '';
                    var price_label_html = '';
                    if (true == $settings.showCurrencyImage) {
                        img_html = `<div class="bdt-crypto-currency-carousel-img">
                                        <img src="${element.image}" alt="${element.id}">
                                    </div>`;
                    }
                    if (true == $settings.showCurrencyShortName) {
                        symble_html = `<span>(${element.symbol})</span>`;
                    }
                    if (true == $settings.showCurrencyName) {
                        name_html = `<div class="bdt-crypto-currency-carousel-title">
                        <h4>${element.id} ${symble_html}</h4>
                    </div>`;
                    }
                    if (true == $settings.showCurrencyPriceLabel) {
                        price_label_html = `<div class="bdt-crypto-currency-carousel-price-text">
                        <span>price</span>
                    </div>`;
                    }
                    if (true == $settings.showCurrencyCurrentPrice) {
                        price_html = `${price_label_html}
                    <div class="bdt-crypto-currency-carousel-price-nu">
                        <span class="price-int">${amount}</span>
                    </div>`;
                    }

                    var output = `<div class="swiper-slide"><div class="bdt-crypto-currency-carousel-item" data-id="${element.id}">
                                <div class="bdt-crypto-currency-carousel-content">
                                    <div class="bdt-crypto-currency-carousel-bg">
                                        <img src="${element.image}" alt="${element.id}">
                                    </div>

                                    <div class="bdt-crypto-currency-carousel-head-content">
                                        ${img_html}
                                        ${name_html}
                                    </div>
                                    <div class="bdt-crypto-currency-carousel-bottom-content">
                                        ${price_html}
                                    </div>
                                </div>
                                <span class="bdt-price-text bdt-hidden">${element.current_price}</span>
                            </div></div>`;

                    $($cryptoWidget).find('.swiper-wrapper').append(output);

                    var $carouselContainer = $carousel.find('.swiper-carousel'),
                        $carouselSettings = $carousel.data('settings');

                    const Swiper = elementorFrontend.utils.swiper;
                    initSwiper();
                    async function initSwiper() {
                        var swiper = await new Swiper($carouselContainer, $carouselSettings);

                        if ($carouselSettings.pauseOnHover) {
                            $($carouselContainer).hover(function () {
                                (this).swiper.autoplay.stop();
                            }, function () {
                                (this).swiper.autoplay.start();
                            });
                        }

                    };

                });
            }
        });

        if (true !== editMode) {
            setTimeout(function () {
                getData();
            }, 5000);
        }



    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-crypto-currency-carousel.default', widgetCrypto);
    });

}(jQuery, window.elementorFrontend));
/**
 * Start accordion widget script
 */

;
(function ($, elementor) {
    'use strict';
    var widgetCrypto = function ($scope, $) {
        var $cryptoWidget = $scope.find('.bdt-crypto-currency-chart'),
            $settings = $cryptoWidget.data('settings'),
            editMode = Boolean(elementorFrontend.isEditMode());

        if (!$cryptoWidget.length) {
            return;
        }

        var $options = {
            'currency': $settings.currency,
            'limit': 100,
            'order': 'market_cap_desc',
        };

        if ($settings.currency) {
            $options.currency = $settings.currency
        }
        if ($settings.limit) {
            $options.limit = $settings.limit
        }
        if ($settings.order) {
            $options.order = $settings.order
        }

        let currency_selected, cryptoDataSettingsValue = $options;

        function getData() {
            $.ajax({
                type: "GET",
                // dataType: "json",
                url: ElementPackConfig.ajaxurl + '?action=ep_crypto_data',
                data: {
                    currency: currency_selected,
                    per_page: cryptoDataSettingsValue.limit, //limit
                    order: cryptoDataSettingsValue.order,
                    ids: $settings.ids
                },

            }).done(function (data) {
                let itemData = $($cryptoWidget).find('.bdt-crypto-currency-chart-item');
                /**
                 * @idPriceColumnArray is holding data from current items
                 */

                let idPriceColumnArray = [];
                for (let i = 0; i < itemData.length; i++) {
                    idPriceColumnArray.push({
                        // id: itemData[i]["id"],
                        id: $(itemData[i]).data('id'),
                        current_price: parseFloat($(itemData[i]).find('.bdt-price-text').text()),
                    });
                }

                // console.log(idPriceColumnArray);

                /**
                 * @crypDataParse holding data from crypto live data server
                 */
                let cryptDataParse = JSON.parse(data);
                /**
                 * changes array
                 */
                let changesIdArray = [];
                /**
                 * now have to compare this two array of object
                 */
                for (let i = 0; i < idPriceColumnArray.length; i++) {
                    $.map(cryptDataParse, function (elem, index) {
                        if (elem.id === idPriceColumnArray[i].id) {
                            // console.log(elem.current_price);
                            if (elem.current_price !== idPriceColumnArray[i].current_price) {
                                changesIdArray.push({
                                    id: idPriceColumnArray[i].id,
                                    current_price: elem.current_price,
                                    old_price: idPriceColumnArray[i].current_price,
                                });
                            }
                        }
                    });
                }

                // console.log(changesIdArray);

                if (changesIdArray.length !== 0) {
                    changesIdArray.forEach(element => {
                        $($cryptoWidget).find('[data-id="' + element.id + '"]').addClass('data-changed');
                        $($cryptoWidget).find('[data-id="' + element.id + '"]').find('.bdt-price-text').text(element.current_price);

                        let upperCaseCurrncyCode = currency_selected.toString().toUpperCase();
                        let amount = returnCurrencySymbol(upperCaseCurrncyCode) + element.current_price;
                        $($cryptoWidget).find('[data-id="' + element.id + '"] .price-int').text(amount);

                    });
                }


                setTimeout(function () {
                    $($cryptoWidget).find('.bdt-crypto-currency-chart-item').removeClass('data-changed');
                    return getData();
                }, 10000);
            });
        }

        /**
         * number format
         */
        function numFormatter(num) {
            if (num > 999 && num < 1000000) {
                return (num / 1000).toFixed(2) + 'K'; // convert to K for number from > 1000 < 1 million 
            } else if (num > 1000000000) {
                return (num / 1000000000).toFixed(2) + 'B'; // convert to M for number from > 1 million 
            } else if (num > 1000000) {
                return (num / 1000000).toFixed(2) + 'M'; // convert to M for number from > 1 million 
            } else if (num < 900) {
                return num; // if value < 1000, nothing to do
            }
        }
        /**
         * defalt onload call will be here
         */
        if (cryptoDataSettingsValue !== undefined && cryptoDataSettingsValue.currency !== undefined) {
            currency_selected = cryptoDataSettingsValue.currency;
        } else {
            currency_selected = "usd"; // default currency settings here
        }

        $.ajax({
            type: "GET",
            dataType: "json",
            url: ElementPackConfig.ajaxurl + '?action=ep_crypto',
            data: {
                currency: currency_selected,
                per_page: cryptoDataSettingsValue.limit, //limit
                order: cryptoDataSettingsValue.order,
                ids: $settings.ids
            },
            success: function (result) {
                $($cryptoWidget).empty();
                var count = 0;
                result.data.forEach(element => {
                    count++;
                    if (count > cryptoDataSettingsValue.limit) {
                        return;
                    }
                    // console.log(element.id);
                    let upperCaseCurrncyCode = currency_selected.toString().toUpperCase();
                    // let formatAmount = numFormatter(element.current_price);
                    let amount = returnCurrencySymbol(upperCaseCurrncyCode) + element.current_price;

                    let data = element.price_change_percentage_1h;
                    let OneHourData = (Number(data) === data && data % 1 !== 0) ? data.toFixed(2) + "%" : data + "%";

                    var name_html = '';
                    var symble_html = '';
                    var price_html = '';
                    var price_change_percentage_1h_html = '';
                    if (true == $settings.showCurrencyShortName) {
                        symble_html = `<span>(${element.symbol})</span>`;
                    }
                    if (true == $settings.showCurrencyName) {
                        name_html = `<div class="bdt-crypto-currency-chart-title"><h4>${element.id} ${symble_html}</h4></div>`;
                    }
                    if (true == $settings.showCurrencyCurrentPrice) {
                        price_html = `<div class="bdt-crypto-currency-chart-price-l">
                        <span class="price-int">${amount}</span>
                    </div>`;
                    }
                    if (true == $settings.showPriceChangePercentage) {
                        price_change_percentage_1h_html = `<div class="bdt-crypto-currency-chart-change">
                        <span class="bdt-crypto-currency-chart-list-change up" title="1 Hour Data Change">${OneHourData}</span>
                    </div>`;
                    }

                    var output = `<div class="bdt-crypto-currency-chart-item" data-id="${element.id}">
                                    <div class="bdt-crypto-currency-chart-head-content">
                                        <div class="bdt-crypto-currency-chart-head-inner-content">
                                            ${name_html}
                                            ${price_change_percentage_1h_html}
                                            
                                        </div>
                                        <div class="bdt-crypto-currency-chart-bottom-inner-content">
                                            ${price_html}
                                        </div>
                                    </div>
                                    <div class="bdt-crypto-currency-chart-chart">
                                        <input type="hidden" class="hdnInputCanvas-${element.id}"  value="${element.last_seven_days_changes}"/><div class="chart-container" style="position: relative;"><canvas id="canvas-${element.id}"></canvas></div>
                                    </div>
                                    <span class="bdt-price-text bdt-hidden">${element.current_price}</span>
                                </div>`;

                    $($cryptoWidget).append(output);

                    let getCanvasElement = $($cryptoWidget).find('[data-id="' + element.id + '"]');
                    //let canvas_id = $(getCanvasElement).find("canvas").attr("id");
                    let getHiddenData = $(getCanvasElement).find("input").val();
                    let splitData = getHiddenData.split(",");
                    /***
                     * here we are just getting last 20 values value
                     */
                    if (splitData && splitData.length > 15) {
                        splitData = splitData.slice(0, 14);
                    }
                    /**
                     * end of splice code. this we can remove if any further code found
                     */
                    const dom_canvas_element = $(getCanvasElement).find("canvas");
                    const labels = [],
                        dataPointvalue = [];
                    splitData.forEach((element, index) => {
                        labels.push(index);
                        dataPointvalue.push(Number(element));
                    });

                    const dataCharts = {
                        labels: labels,
                        datasets: [{
                            label: "",
                            // backgroundColor: $settings.backgroundColor || "#777",
                            // borderColor: $settings.borderColor || "#777",
                            backgroundColor: "rgba(30,135,240,0.2)",
                            borderColor: "#1e87f0",
                            fill: true,
                            lineTension: 0.4,
                            pointStyle: 'circle',
                            pointBackgroundColor: "#1e87f0",
                            pointBorderWidth: 1,
                            borderWidth: 2,
                            data: dataPointvalue,
                        }, ],
                    };
                    const config = {
                        type: "line",
                        data: dataCharts,
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: false,
                                },
                                tooltip: {
                                    enabled: true
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        display: false,
                                    },
                                    grid: {
                                        display: false,
                                        drawBorder: false,
                                        drawOnChartArea: false,
                                        drawTicks: false,
                                    },
                                },
                                y: {
                                    ticks: {
                                        display: false,
                                    },
                                    grid: {
                                        display: false,
                                        drawBorder: false,
                                        drawOnChartArea: false,
                                        drawTicks: false,
                                    },
                                },
                            },
                        },
                    };
                    const chart = new Chart(dom_canvas_element, config);
                    // chart.canvas.parentNode.style.width = "100%";
                    // chart.canvas.parentNode.style.height = "80px";

                });
            }
        });

        if (true !== editMode) {
            setTimeout(function () {
                getData();
            }, 5000);
        }
    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-crypto-currency-chart.default', widgetCrypto);
    });

}(jQuery, window.elementorFrontend));

/**
 * End accordion widget script
 */
(function ($, elementor) {

    'use strict';

    var widgetCrypto = function ($scope, $) {

        var $carousel = $scope.find('.bdt-crypto-currency-chart-carousel'),
            $cryptoWidget = $scope.find('.bdt-crypto-currency-chart-carousel'),
            $settings = $cryptoWidget.data('crypto-settings'),
            editMode = Boolean(elementorFrontend.isEditMode());

        if (!$carousel.length) {
            return;
        }

        var $options = {
            'currency': $settings.currency,
            'limit': 100,
            'order': 'market_cap_desc',
        };

        if ($settings.currency) {
            $options.currency = $settings.currency
        }
        if ($settings.limit) {
            $options.limit = $settings.limit
        }
        if ($settings.order) {
            $options.order = $settings.order
        }

        let currency_selected, cryptoDataSettingsValue = $options;

        function getData() {
            $.ajax({
                type: "GET",
                // dataType: "json",
                url: ElementPackConfig.ajaxurl + '?action=ep_crypto_data',
                data: {
                    currency: currency_selected,
                    per_page: cryptoDataSettingsValue.limit, //limit
                    order: cryptoDataSettingsValue.order,
                    ids: $settings.ids
                },

            }).done(function (data) {
                let itemData = $($cryptoWidget).find('.bdt-crypto-currency-chart-carousel-item');
                /**
                 * @idPriceColumnArray is holding data from current items
                 */

                let idPriceColumnArray = [];
                for (let i = 0; i < itemData.length; i++) {
                    idPriceColumnArray.push({
                        // id: itemData[i]["id"],
                        id: $(itemData[i]).data('id'),
                        current_price: parseFloat($(itemData[i]).find('.bdt-price-text').text()),
                    });
                }

                // console.log(idPriceColumnArray);

                /**
                 * @crypDataParse holding data from crypto live data server
                 */
                let cryptDataParse = JSON.parse(data);
                /**
                 * changes array
                 */
                let changesIdArray = [];
                /**
                 * now have to compare this two array of object
                 */
                for (let i = 0; i < idPriceColumnArray.length; i++) {
                    $.map(cryptDataParse, function (elem, index) {
                        if (elem.id === idPriceColumnArray[i].id) {
                            // console.log(elem.current_price);
                            if (elem.current_price !== idPriceColumnArray[i].current_price) {
                                changesIdArray.push({
                                    id: idPriceColumnArray[i].id,
                                    current_price: elem.current_price,
                                    old_price: idPriceColumnArray[i].current_price,
                                });
                            }
                        }
                    });
                }

                // console.log(changesIdArray);

                if (changesIdArray.length !== 0) {
                    changesIdArray.forEach(element => {
                        $($cryptoWidget).find('[data-id="' + element.id + '"]').addClass('data-changed');
                        $($cryptoWidget).find('[data-id="' + element.id + '"]').find('.bdt-price-text').text(element.current_price);

                        let upperCaseCurrncyCode = currency_selected.toString().toUpperCase();
                        let amount = returnCurrencySymbol(upperCaseCurrncyCode) + element.current_price;
                        $($cryptoWidget).find('[data-id="' + element.id + '"] .price-int').text(amount);

                    });
                }


                setTimeout(function () {
                    $($cryptoWidget).find('.bdt-crypto-currency-chart-carousel-item').removeClass('data-changed');
                    return getData();
                }, 10000);
            });
        }

        /**
         * number format
         */
        function numFormatter(num) {
            if (num > 999 && num < 1000000) {
                return (num / 1000).toFixed(2) + 'K'; // convert to K for number from > 1000 < 1 million 
            } else if (num > 1000000000) {
                return (num / 1000000000).toFixed(2) + 'B'; // convert to M for number from > 1 million 
            } else if (num > 1000000) {
                return (num / 1000000).toFixed(2) + 'M'; // convert to M for number from > 1 million 
            } else if (num < 900) {
                return num; // if value < 1000, nothing to do
            }
        }
        /**
         * defalt onload call will be here
         */
        if (cryptoDataSettingsValue !== undefined && cryptoDataSettingsValue.currency !== undefined) {
            currency_selected = cryptoDataSettingsValue.currency;
        } else {
            currency_selected = "usd"; // default currency settings here
        }

        $.ajax({
            type: "GET",
            dataType: "json",
            url: ElementPackConfig.ajaxurl + '?action=ep_crypto',
            data: {
                currency: currency_selected,
                per_page: cryptoDataSettingsValue.limit, //limit
                order: cryptoDataSettingsValue.order,
                ids: $settings.ids
            },
            success: function (result) {
                $($cryptoWidget).find('.swiper-wrapper').empty();
                var count = 0;
                result.data.forEach(element => {
                    count++;
                    if (count > cryptoDataSettingsValue.limit) {
                        return;
                    }
                    // console.log(element.id);
                    let upperCaseCurrncyCode = currency_selected.toString().toUpperCase();
                    // let formatAmount = numFormatter(element.current_price);
                    let amount = returnCurrencySymbol(upperCaseCurrncyCode) + element.current_price;

                    let data = element.price_change_percentage_1h;
                    let OneHourData = (Number(data) === data && data % 1 !== 0) ? data.toFixed(2) + "%" : data + "%";

                    var name_html = '';
                    var symble_html = '';
                    var price_html = '';
                    var price_change_percentage_1h_html = '';
                    if (true == $settings.showCurrencyShortName) {
                        symble_html = `<span>(${element.symbol})</span>`;
                    }
                    if (true == $settings.showCurrencyName) {
                        name_html = `<div class="bdt-crypto-currency-chart-carousel-title"><h4>${element.id} ${symble_html}</h4></div>`;
                    }
                    if (true == $settings.showCurrencyCurrentPrice) {
                        price_html = `<div class="bdt-crypto-currency-chart-carousel-price-l">
                        <span>${amount}</span>
                    </div>`;
                    }
                    if (true == $settings.showPriceChangePercentage) {
                        price_change_percentage_1h_html = `<div class="bdt-crypto-currency-chart-carousel-change">
                        <span class="bdt-crypto-currency-chart-carousel-list-change up" title="1 Hour Data Change">${OneHourData}</span>
                    </div>`;
                    }

                    var output = `<div class="swiper-slide"><div class="bdt-crypto-currency-chart-carousel-item" data-id="${element.id}">
                                    <div class="bdt-crypto-currency-chart-carousel-head-content">
                                        <div class="bdt-crypto-currency-chart-carousel-head-inner-content">
                                            ${name_html}
                                            ${price_change_percentage_1h_html}
                                            
                                        </div>
                                        <div class="bdt-crypto-currency-chart-carousel-bottom-inner-content">
                                            ${price_html}
                                        </div>
                                    </div>
                                    <div class="bdt-crypto-currency-chart-carousel-chart">
                                        <input type="hidden" class="hdnInputCanvas-${element.id}"  value="${element.last_seven_days_changes}"/><div class="chart-container" style="position: relative;"><canvas id="canvas-${element.id}"></canvas></div>
                                    </div>
                                    <span class="bdt-price-text bdt-hidden">${element.current_price}</span>
                                </div></div>`;

                    $($cryptoWidget).find('.swiper-wrapper').append(output);

                    var $carouselContainer = $carousel.find('.swiper-carousel'),
                        $carouselSettings = $carousel.data('settings');

                    const Swiper = elementorFrontend.utils.swiper;
                    initSwiper();
                    async function initSwiper() {
                        var swiper = await new Swiper($carouselContainer, $carouselSettings);

                        if ($carouselSettings.pauseOnHover) {
                            $($carouselContainer).hover(function () {
                                (this).swiper.autoplay.stop();
                            }, function () {
                                (this).swiper.autoplay.start();
                            });
                        }

                    };



                    let getCanvasElement = $($cryptoWidget).find('[data-id="' + element.id + '"]');
                    //let canvas_id = $(getCanvasElement).find("canvas").attr("id");
                    let getHiddenData = $(getCanvasElement).find("input").val();
                    let splitData = getHiddenData.split(",");
                    /***
                     * here we are just getting last 20 values value
                     */
                    if (splitData && splitData.length > 15) {
                        splitData = splitData.slice(0, 14);
                    }
                    /**
                     * end of splice code. this we can remove if any further code found
                     */
                    const dom_canvas_element = $(getCanvasElement).find("canvas");
                    const labels = [],
                        dataPointvalue = [];
                    splitData.forEach((element, index) => {
                        labels.push(index);
                        dataPointvalue.push(Number(element));
                    });
                    const dataCharts = {
                        labels: labels,
                        datasets: [{
                            label: "",
                            // backgroundColor: $settings.backgroundColor || "#777",
                            // borderColor: $settings.borderColor || "#777",
                            backgroundColor: "rgba(30,135,240,0.2)",
                            borderColor: "#1e87f0",
                            fill: true,
                            lineTension: 0.4,
                            pointStyle: 'circle',
                            pointBackgroundColor: "#1e87f0",
                            pointBorderWidth: 1,
                            borderWidth: 2,
                            data: dataPointvalue,
                        }, ],
                    };
                    const config = {
                        type: "line",
                        data: dataCharts,
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: false,
                                },
                                tooltip: {
                                    enabled: true
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        display: false,
                                    },
                                    grid: {
                                        display: false,
                                        drawBorder: false,
                                        drawOnChartArea: false,
                                        drawTicks: false,
                                    },
                                },
                                y: {
                                    ticks: {
                                        display: false,
                                    },
                                    grid: {
                                        display: false,
                                        drawBorder: false,
                                        drawOnChartArea: false,
                                        drawTicks: false,
                                    },
                                },
                            },
                        },
                    };
                    const chart = new Chart(dom_canvas_element, config);
                    // chart.canvas.parentNode.style.width = "100%";
                    // chart.canvas.parentNode.style.height = "80px";



                });
            }
        });

        if (true !== editMode) {
            setTimeout(function () {
                getData();
            }, 5000);
        }


    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-crypto-currency-chart-carousel.default', widgetCrypto);
    });

}(jQuery, window.elementorFrontend));
/**
 * Start accordion widget script
 */

;
(function ($, elementor) {
    'use strict';
    var widgetCrypto = function ($scope, $) {
        var $cryptoWidget = $scope.find('.bdt-crypto-currency-ticker'),
            $settings = $cryptoWidget.data('settings'),
            $tickerSettings = $cryptoWidget.data('ticker-settings'),
            editMode = Boolean(elementorFrontend.isEditMode());

        if (!$cryptoWidget.length) {
            return;
        }

        // Crypto Data
        var $options = {
            'currency': $settings.currency,
            'limit': 100,
            'order': 'market_cap_desc',
        };

        if ($settings.currency) {
            $options.currency = $settings.currency
        }
        if ($settings.limit) {
            $options.limit = $settings.limit
        }
        if ($settings.order) {
            $options.order = $settings.order
        }

        let currency_selected, cryptoDataSettingsValue = $options;

        function getData() {
            $.ajax({
                type: "GET",
                // dataType: "json",
                url: ElementPackConfig.ajaxurl + '?action=ep_crypto_data',
                data: {
                    currency: currency_selected,
                    per_page: cryptoDataSettingsValue.limit, //limit
                    order: cryptoDataSettingsValue.order,
                    ids: $settings.ids
                },

            }).done(function (data) {
                let itemData = $($cryptoWidget).find('.bdt-crypto-currency-ticker-item');
                /**
                 * @idPriceColumnArray is holding data from current items
                 */

                let idPriceColumnArray = [];
                for (let i = 0; i < itemData.length; i++) {
                    idPriceColumnArray.push({
                        // id: itemData[i]["id"],
                        id: $(itemData[i]).data('id'),
                        current_price: parseFloat($(itemData[i]).find('.bdt-price-text').text()),
                    });
                }

                // console.log(idPriceColumnArray);

                /**
                 * @crypDataParse holding data from crypto live data server
                 */
                let cryptDataParse = JSON.parse(data);
                /**
                 * changes array
                 */
                let changesIdArray = [];
                /**
                 * now have to compare this two array of object
                 */
                for (let i = 0; i < idPriceColumnArray.length; i++) {
                    $.map(cryptDataParse, function (elem, index) {
                        if (elem.id === idPriceColumnArray[i].id) {
                            // console.log(elem.current_price);
                            if (elem.current_price !== idPriceColumnArray[i].current_price) {
                                changesIdArray.push({
                                    id: idPriceColumnArray[i].id,
                                    current_price: elem.current_price,
                                    old_price: idPriceColumnArray[i].current_price,
                                });
                            }
                        }
                    });
                }

                // console.log(changesIdArray);

                if (changesIdArray.length !== 0) {
                    changesIdArray.forEach(element => {
                        $($cryptoWidget).find('[data-id="' + element.id + '"]').addClass('data-changed');
                        $($cryptoWidget).find('[data-id="' + element.id + '"]').find('.bdt-price-text').text(element.current_price);

                        let upperCaseCurrncyCode = currency_selected.toString().toUpperCase();
                        let amount = returnCurrencySymbol(upperCaseCurrncyCode) + element.current_price;
                        $($cryptoWidget).find('[data-id="' + element.id + '"] .price-int').text(amount);

                    });
                }


                setTimeout(function () {
                    $($cryptoWidget).find('.bdt-crypto-currency-ticker-item').removeClass('data-changed');
                    return getData();
                }, 10000);
            });
        }

        /**
         * number format
         */
        function numFormatter(num) {
            if (num > 999 && num < 1000000) {
                return (num / 1000).toFixed(2) + 'K'; // convert to K for number from > 1000 < 1 million 
            } else if (num > 1000000000) {
                return (num / 1000000000).toFixed(2) + 'B'; // convert to M for number from > 1 million 
            } else if (num > 1000000) {
                return (num / 1000000).toFixed(2) + 'M'; // convert to M for number from > 1 million 
            } else if (num < 900) {
                return num; // if value < 1000, nothing to do
            }
        }
        /**
         * defalt onload call will be here
         */
        if (cryptoDataSettingsValue !== undefined && cryptoDataSettingsValue.currency !== undefined) {
            currency_selected = cryptoDataSettingsValue.currency;
        } else {
            currency_selected = "usd"; // default currency settings here
        }

        $.ajax({
            type: "GET",
            dataType: "json",
            url: ElementPackConfig.ajaxurl + '?action=ep_crypto',
            data: {
                currency: currency_selected,
                per_page: cryptoDataSettingsValue.limit, //limit
                order: cryptoDataSettingsValue.order,
                ids: $settings.ids
            },
            success: function (result) {
                $($cryptoWidget).find('ul').empty();
                var count = 0;
                result.data.forEach(element => {
                    count++;
                    if (count > cryptoDataSettingsValue.limit) {
                        return;
                    }
                    // console.log(element.id);
                    let upperCaseCurrncyCode = currency_selected.toString().toUpperCase();
                    // let formatAmount = numFormatter(element.current_price);
                    let amount = returnCurrencySymbol(upperCaseCurrncyCode) + element.current_price;

                    let data = element.price_change_percentage_1h;
                    let OneHourData = (Number(data) === data && data % 1 !== 0) ? data.toFixed(2) + "%" : data + "%";

                    var img_html = '';
                    var name_html = '';
                    var symble_html = '';
                    var price_html = '';
                    var price_change_percentage_1h_html = '';
                    if (true == $settings.showCurrencyImage) {
                        img_html = `<div class="bdt-crypto-currency-ticker-img">
                        <img src="${element.image}" alt="${element.id}">
                    </div>`;
                    }
                    if (true == $settings.showCurrencyShortName) {
                        symble_html = `<span>(${element.symbol})</span>`;
                    }
                    if (true == $settings.showCurrencyName) {
                        name_html = `<h3 class="bdt-crypto-currency-ticker-title">
                        ${element.id} ${symble_html}
                    </h3>`;
                    }
                    if (true == $settings.showCurrencyCurrentPrice) {
                        price_html = `<span class="bdt-crypto-currency-ticker-price">${amount}</span>`;
                    }
                    if (true == $settings.showPriceChangePercentage) {
                        price_change_percentage_1h_html = `<div class="bdt-crypto-currency-ticker-percentage">
                        <svg width="25" height="22" viewBox="0 0 20 20" fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M6.646 11.646a.5.5 0 01.708 0L10 14.293l2.646-2.647a.5.5 0 01.708.708l-3 3a.5.5 0 01-.708 0l-3-3a.5.5 0 010-.708z"
                                clip-rule="evenodd" />
                            <path fill-rule="evenodd"
                                d="M10 4.5a.5.5 0 01.5.5v9a.5.5 0 01-1 0V5a.5.5 0 01.5-.5z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>${OneHourData}</span>
                    </div>`;
                    }

                    var output = `<li class="bdt-crypto-currency-ticker-item" data-id="${element.id}">

                                <div class="bdt-crypto-currency-ticker-inner-item">
                                    ${img_html}
                                    <div class="bdt-crypto-currency-ticker-content">
                                        ${name_html}
                                        ${price_html}
                                        ${price_change_percentage_1h_html}
                                    </div>
                                </div>

                                <span class="bdt-price-text bdt-hidden">${element.current_price}</span>
                             </li>`;

                    $($cryptoWidget).find('ul').append(output);

                });
            }
        });

        if (true !== editMode) {
            setTimeout(function () {
                getData();
            }, 7000);
        }

        $(document).ready(function () {
            setTimeout(function () {
                $($cryptoWidget).epNewsTicker($tickerSettings);
            }, 7000);
        });

    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-crypto-currency-ticker.default', widgetCrypto);
    });

}(jQuery, window.elementorFrontend));

/**
 * End accordion widget script
 */
;
(function ($, elementor) {
    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            RealisticShadow;

        RealisticShadow = ModuleHandler.extend({

            bindEvents: function () {
                this.run();
            },

            getDefaultSettings: function () {
                return {
                    allowHTML: true,
                };
            },

            onElementChange: debounce(function (prop) {
                if (prop.indexOf('element_pack_ris_') !== -1) {
                    this.run();
                }
            }, 400),

            settings: function (key) {
                return this.getElementSettings('element_pack_ris_' + key);
            },

            run: function () {
                var options = this.getDefaultSettings();
                var widgetID = this.$element.data('id');
                var widgetContainer = $('.elementor-element-' + widgetID + ' .elementor-widget-container');
                var obj = this;

                if ('yes' !== this.settings('enable')) {
                    return;
                }

                if (this.settings('selector')) {
                    widgetContainer = $('.elementor-element-' + widgetID).find(this.settings('selector'));
                }

                var $image = widgetContainer.find('img');

                $image.each(function () {
                    var $this = $(this);
                    if (!$this.hasClass('element-pack-ris-image')) {
                        var $duplicateImage = $this.clone();
                        $duplicateImage.addClass('element-pack-ris-image');
                        

                        // Remove any existing 'element-pack-ris-image' elements except the first one
                        var $existingImages = $($this).parent().find('.element-pack-ris-image');
                        if ($existingImages.length > 1) {
                            $existingImages.not(':first').remove();
                        }

                        if ($existingImages.length < 1) {
                            $($this).parent().append($duplicateImage);
                        }

                        // Add class to parent
                        widgetContainer.addClass('bdt-realistic-image-shadow');

                        if (obj.settings('on_hover') === 'yes') {
                            widgetContainer.addClass('bdt-hover');
                        }
                    }
                });

            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(RealisticShadow, {
                $element: $scope
            });
        });
    });
})(jQuery, window.elementorFrontend);
/**
 * Start Content Switcher widget script
 */

(function ($, elementor) {

    'use strict';

    var widgetFloatingKnowledgebase = function ($scope, $) {

        var $floatingKnowledgebase = $scope.find('.bdt-floating-knowledgebase'),
            $settings = $floatingKnowledgebase.data('settings');

        if (!$floatingKnowledgebase.length) {
            return;
        }

        ;
        (function ($) {
            "use strict";
            /*
             *--------------------------------------------------------------------------
             * CONFIG OR SETTINGS - Customize the help center
             *--------------------------------------------------------------------------
             */
            var helpCenterConfig = {
                // primaryColor: "#007bff", // Floating button color
                linkColor: "#007bff", // Color of anchor tags or links
                showHelperText: true, // Helper text beside floating button(true|false)
                helperTextLabel: $settings.helperTextLabel || "Have any queries?<br /><strong>Check Help Center</strong>", // Helper text label
                showContactUsLink: true, // Hide or show contact us link(true|false)
                contactUsLabel: $settings.supportLinkText || "Still no luck? We can help!", // Text of contact us link
                contactUsLink: $settings.supportLink || "/contact-us", // Contact us link
                noResultsLabel: $settings.noSearchResultText || "Sorry, we don’t have any results. Updates are being added all the time.", // No results found text
                resetOnPopupClose: false, // Reset popup back to original state on close(true|false)
                btnZindex: 999, // Z-index property of floating button
                popupZindex: 998, // Z-index property of popup
                onPopupOpen: function () {}, // Callback function which runs on popup open
                onPopupClose: function () {} // Callback function which runs on popup close
            }

            var nodesStack = []; // Array to hande "nodes/html-content" back-forth pagination
            var titleStack = []; // Array to hande "title" back-forth pagination
            var jsonData; // JSON data

            /**
             * Get the JSON Data and Initialize plugin
             */


            $.fn.floatingHelpCenter = function () {
                jsonData = $settings.data_source;
                renderHelpCenterBtn(helpCenterConfig); // render popup button
                renderPopup(jsonData); // render popup with JSON data
            }
            /**
             * Renders the help center button using config
             * @param {Object} config - Contains config/preferences
             */
            function renderHelpCenterBtn(config) {
                var $btnWrap = $("<div>", {
                    class: "floating-help-center__btn"
                }).css('zIndex', helpCenterConfig.btnZindex);
                $btnWrap.click(togglePopup);
                var $helperText = $("<p>", {
                    class: "helper-txt"
                }).html(helpCenterConfig.helperTextLabel);
                var $btn = $("<button>", {
                    class: "btn"
                });
                $("#bdt-floating-help-center").append($btnWrap.append(function () {
                    if (helpCenterConfig.showHelperText) {
                        return $helperText;
                    }
                }, $btn));
            }

            /**
             * Shows or hide the popup on button click
             */
            function togglePopup() {
                var $popup = $("#bdt-floating-help-center .floating-help-center__popup");
                var $popBtn = $("#bdt-floating-help-center .floating-help-center__btn");
                var popupActiveClass = "floating-help-center__popup--active";
                if ($popup.hasClass(popupActiveClass)) {
                    $popup.removeClass(popupActiveClass);
                    $popBtn.removeClass(popupActiveClass);
                    helpCenterConfig.onPopupClose.call(this);
                } else {
                    if (helpCenterConfig.resetOnPopupClose) {
                        resetPopupContent();
                    }
                    $popup.addClass(popupActiveClass);
                    $popBtn.addClass(popupActiveClass);
                    helpCenterConfig.onPopupOpen.call(this);
                }
            }

            /**
             * Renders the popup with populated data
             * @param {Object} data - contains json data
             */
            function renderPopup(data) {



                var $outerWrap = $("<div>", {
                    id: "floatingHelpCenterPopup",
                    class: "floating-help-center__popup"
                }).css('zIndex', helpCenterConfig.popupZindex);;

                var $searchOuter = $("<div>", {
                    class: "searchbox"
                });
                var $searchIcon = $("<div>", {
                    class: "searchbox__search-icon"
                }).html(searchSVG);
                var $input = $("<input>", {
                    class: "searchbox__input",
                    type: "text",
                    placeholder: "Search..."
                });
                var $crossIcon = $("<div>", {
                    class: "searchbox__cross-icon"
                }).html(crossSVG).css({
                    display: "none"
                });
                var $resizerIcon = $("<div>", {
                    class: "bdt-resizer-icon"
                }).html(resizerSVG);

                $crossIcon.click(resetPopupContent);
                $resizerIcon.click(() => {
                    $outerWrap.toggleClass('bdt-content-expand');
                });
                $input.on("input", searchInputHandler);
                // $searchOuter.append($searchIcon, $input, $crossIcon);
                $searchOuter.append($searchIcon, $input, $resizerIcon);
                var $helpList = $("<ul>", {
                    id: "listItemsContainer",
                    class: "help-list"
                });
                var $externalLinkWrap = $("<div>", {
                    id: "externalLinkWrap",
                    class: "external"
                });
                var $externalLink = $("<a>", {
                    id: "externalLinkWrap",
                    class: "external__link",
                    target: "_blank",
                    href: helpCenterConfig.contactUsLink
                }).text(helpCenterConfig.contactUsLabel);
                var $externalArrow = $("<span>", {
                    class: "external__arrow"
                }).html(externalArrowSVG);
                $externalLinkWrap.append($externalLink.append($externalArrow));

                var $headerWrap = $("<div>", {
                    id: "headerWrap",
                    class: "bdt-header"
                });

                if ($settings.logo.url) {
                    if ($settings.title) {
                        $settings.logo.alt = $settings.title;
                    }

                    $headerWrap.append('<div class="bdt-header-logo"><img src="' + $settings.logo.url + '" alt="' + $settings.logo.alt + '"></div>');
                }

                if ($settings.title) {
                    $headerWrap.append('<div class="bdt-header-title">' + $settings.title + '</div>');
                }

                if ($settings.description) {
                    $headerWrap.append('<div class="bdt-header-description">' + $settings.description + '</div>');
                }

                $("#bdt-floating-help-center").append($outerWrap.append(function () {
                    if (helpCenterConfig.showContactUsLink) {
                        return [$headerWrap, $searchOuter, $helpList, $externalLinkWrap];
                    }
                    return [$headerWrap, $searchOuter, $helpList];
                }));
                setPopupContent(data);
            }

            /**
             * Search input listener and sends input query to
             * findObject() function
             */
            function searchInputHandler() {
                var $crossIcon = $("#bdt-floating-help-center .searchbox__cross-icon");
                var query = $(this).val();
                if (query && query !== "") {
                    $crossIcon.css({
                        display: "block"
                    });
                    var resultsArr = findObject(jsonData, "title", query, true);
                    setPopupContent(resultsArr, "search");
                } else {
                    $crossIcon.css({
                        display: "none"
                    });
                    resetPopupContent();
                }
            }

            /**
             * Enables or Disables Search Input
             */
            function searchInputReadonlyToggle() {
                var $searchInput = $("#bdt-floating-help-center .searchbox__input");
                $searchInput.attr('readonly', nodesStack.length > 1);
            }

            /**
             * Displays the list of questions/title
             * @param {Array} data - Data array
             */
            function renderPopupContentList(data) {
                $.each(data, function (index, listObj) {
                    var $listWrap = $("<li>", {
                        class: "help-list__item"
                    });
                    $listWrap.click(function () {
                        listItemClickHandler(this);
                    });
                    var $listArrow = $("<span>", {
                        class: "help-list__item-arrow"
                    }).html(listArrowSVG);
                    var $listText = $("<span>", {
                        class: "help-list__item-txt"
                    }).html(listObj["title"]);
                    $("#bdt-floating-help-center #listItemsContainer").append($listWrap.append($listText, $listArrow));
                });
            }

            /**
             * Hides or shows the back button on search input
             */
            function toggleBackButton() {
                $("#bdt-floating-help-center .searchbox__search-icon").unbind();
                nodesStack.length > 1 ?
                    $(".searchbox__search-icon").html(backSVG).click(backBtnHandler) :
                    $("#bdt-floating-help-center .searchbox__search-icon").html(searchSVG);

                if (nodesStack.length > 1) {
                    $('#floatingHelpCenterPopup').addClass('bdt-content-expand');
                } else {
                    $('#floatingHelpCenterPopup').removeClass('bdt-content-expand');
                }
            }

            /**
             * Resets the previous html content before 
             * populating with new content
             */
            function resetPreviousState() {
                $("#bdt-floating-help-center #htmlContent, #bdt-floating-help-center #noResultTxt").remove();
                $("#bdt-floating-help-center #listItemsContainer").html("");
                $('#floatingHelpCenterPopup').removeClass('bdt-content-open');
            }

            /**
             * Performs set of operations on back button click
             */
            function backBtnHandler() {
                nodesStack.pop();
                titleStack.pop();
                var lastNode = nodesStack.pop();
                var lastTitle = titleStack.pop();
                setInputTitle(lastTitle);
                setPopupContent(lastNode);
                searchInputReadonlyToggle();
            }

            /**
             * Checks the data type and calls the rendering functions accordingly
             * @param {Object|Array|String} data - Data to be rendered
             * @param {string} event - Event type(search)  
             */
            function setPopupContent(data, event) {
                if (data.length === 0) {
                    renderNoResults();
                    return;
                }
                if (data.length > 1) {
                    beforeSetPopupContent(data, event);
                    renderPopupContentList(data);
                    return;
                }
                if (event === "search") {
                    beforeSetPopupContent(data, event);
                    renderPopupContentList(data);
                    return;
                }
                var destructuredObj = data.pop();
                if (typeof destructuredObj === "string") {
                    beforeSetPopupContent([destructuredObj], event);
                    renderHTML("", destructuredObj);
                    return;
                }
                if (destructuredObj.hasOwnProperty("nodes")) {
                    beforeSetPopupContent(destructuredObj["nodes"], event);
                    renderPopupContentList(destructuredObj["nodes"]);
                    return;
                }
                beforeSetPopupContent([destructuredObj["html"]], event);
                renderHTML(destructuredObj["title"], destructuredObj["html"]);
            }

            /**
             * Performs set of operation before popup content is set
             * @param {Object|Array|string} data - Data to be rendered 
             * @param {string} event - Type of event
             */
            function beforeSetPopupContent(data, event) {
                if (event === undefined || event !== "search") {
                    nodesStack.push(data);
                }
                toggleBackButton();
                resetPreviousState();
            }

            /**
             * Links other objects title in anchor tags
             * in attribute "data-title"
             */
            function anchorDataTitleHandler() {
                $("#bdt-floating-help-center #htmlContent a").click(function () {
                    var dataTitle = $(this).attr("data-title");
                    if (dataTitle && dataTitle !== "") {
                        var resultsArr = findObject(jsonData, "title", dataTitle);
                        setInputTitle(dataTitle);
                        setPopupContent(resultsArr);
                        return false;
                    }
                });
            }

            /**
             * Resets the popup content to initial state
             */
            function resetPopupContent() {
                nodesStack = [];
                titleStack = [];
                setInputTitle("");
                $("#bdt-floating-help-center .searchbox__cross-icon").css({
                    display: "none"
                });
                setPopupContent(jsonData);
                $("#bdt-floating-help-center #htmlContent, #bdt-floating-help-center #noResultTxt").remove();
            }

            /**
             * Set's title in searchbar
             * @param {string} title - Title
             */
            function setInputTitle(title) {
                if (title && title !== "") {
                    // $("#bdt-floating-help-center .searchbox__input").val(title);
                    $("#bdt-floating-help-center .searchbox__input").val(" ");
                    titleStack.push(title);
                } else {
                    $("#bdt-floating-help-center .searchbox__input").val("");
                }
            }

            /**
             * Click handler for lists
             * @param {HTMLElement} listElement - list element
             */
            function listItemClickHandler(listElement) {
                var listTitle = $(listElement).find(".help-list__item-txt").text();
                var matchedObj = findObject(jsonData, "title", listTitle);
                setInputTitle(listTitle);
                setPopupContent(matchedObj);
                searchInputReadonlyToggle();
            }

            /**
             * Displays HTML help article
             * @param {string} title - Title of article
             * @param {string} htmlContent - Content of article
             */
            function renderHTML(title, htmlContent) {
                $(".searchbox__cross-icon").css({
                    display: "none"
                });
                var $htmlContentElement = $("<div>", {
                    id: "htmlContent",
                    class: "html-content"
                }).html(htmlContent);
                var articleTitle = title || $("#bdt-floating-help-center .searchbox__input").val();
                $htmlContentElement.prepend($("<h5>", {
                    id: "contentTitle",
                    class: "html-content__title"
                }).html(articleTitle));
                $htmlContentElement.find("a").not('.callout-block a').css({
                    "color": helpCenterConfig.linkColor
                });
                $htmlContentElement.insertBefore("#bdt-floating-help-center #listItemsContainer");
                $('#floatingHelpCenterPopup').addClass('bdt-content-open');

                anchorDataTitleHandler();
            }

            /**
             * Shows no results message
             */
            function renderNoResults() {
                resetPreviousState();
                var $noResultsLabel = $("<p>", {
                    id: "noResultTxt",
                    class: "no-result"
                }).html(helpCenterConfig.noResultsLabel);
                if ($("#noResultTxt").length === 0) {
                    $noResultsLabel.insertAfter("#bdt-floating-help-center .searchbox");
                }
            }

            /**
             * @param {Object} obj - Object where search has to be performed 
             * @param {string} key - Attribute Key
             * @param {string} value - Value 
             * @param {boolean} performSearch - Search perform or finding value by key
             * @returns {Object} obj - Result filtered object
             */
            function findObject(obj, key, value, performSearch) {
                value = performSearch === true ? value.toUpperCase() : value;
                var results = [];

                function recursiveSearch(obj) {
                    if (!obj || _typeof(obj) !== "object") {
                        return;
                    }
                    if (performSearch) {
                        if (obj[key] && obj[key].toUpperCase().indexOf(value) > -1) {
                            results.push(obj);
                        }
                    } else {
                        if (obj[key] === value) {
                            results.push(obj);
                        }
                    }
                    Object.keys(obj).forEach(function (k) {
                        recursiveSearch(obj[k]);
                    });
                }
                recursiveSearch(obj);
                return results;
            }


            /**
             * Helper function for old browsers
             */
            function _typeof(obj) {
                "@babel/helpers - typeof";
                if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
                    _typeof = function _typeof(obj) {
                        return typeof obj;
                    };
                } else {
                    _typeof = function _typeof(obj) {
                        return obj &&
                            typeof Symbol === "function" &&
                            obj.constructor === Symbol &&
                            obj !== Symbol.prototype ? "symbol" : typeof obj;
                    };
                }
                return _typeof(obj);
            }

            /**
             * floating help center window object
             * and public functions
             */
            return window.floatingHelpCenter = {
                init: function () {
                    $.fn.floatingHelpCenter();
                },
                toggle: function () {
                    togglePopup();
                },
                isOpen: function () {
                    return $("#bdt-floating-help-centerPopup").hasClass("floating-help-center__popup--active");
                }
            };

        })(jQuery);
        // Search icon svg
        var searchSVG = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M15.2 16.34a7.5 7.5 0 1 1 1.38-1.45l4.2 4.2a1 1 0 1 1-1.42 1.41l-4.16-4.16zm-4.7.16a6 6 0 1 0 0-12 6 6 0 0 0 0 12z"></path></svg>';

        // Cross icon svg
        var crossSVG = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M13.06 12.15l5.02-5.03a.75.75 0 1 0-1.06-1.06L12 11.1 6.62 5.7a.75.75 0 1 0-1.06 1.06l5.38 5.38-5.23 5.23a.75.75 0 1 0 1.06 1.06L12 13.2l4.88 4.87a.75.75 0 1 0 1.06-1.06l-4.88-4.87z"></path></svg>';

        // Resizer icon svg
        var resizerSVG = '<svg class="bdt-expand" xmlns="http://www.w3.org/2000/svg" height="24" width="24" fill="currentColor"><path d="M2.675 21.325v-8.65h2.65v4.15l11.5-11.5h-4.15v-2.65h8.65v8.65h-2.65v-4.15l-11.5 11.5h4.15v2.65Z"></path></svg>';

        resizerSVG += '<svg class="bdt-close" xmlns="http://www.w3.org/2000/svg" height="24" width="24" fill="currentColor"><path d="m3.075 22.775-1.85-1.85L7.5 14.65H3.35V12H12v8.65H9.35V16.5ZM12 12V3.35h2.65V7.5l6.275-6.275 1.85 1.85L16.5 9.35h4.15V12Z"></path></svg>';

        // back icon svg
        var backSVG = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M15.45 17.97L9.5 12.01a.25.25 0 0 1 0-.36l5.87-5.87a.75.75 0 0 0-1.06-1.06l-5.87 5.87c-.69.68-.69 1.8 0 2.48l5.96 5.96a.75.75 0 0 0 1.06-1.06z"></path></svg>';

        // list arrow svg
        var listArrowSVG = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><path fill="currentColor" d="M6.47 4.29l3.54 3.53c.1.1.1.26 0 .36L6.47 11.7a.75.75 0 1 0 1.06 1.06l3.54-3.53c.68-.69.68-1.8 0-2.48L7.53 3.23a.75.75 0 0 0-1.06 1.06z"></path></svg>';

        var externalArrowSVG = '<svg fill="#3a3f3f" width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="M11.268 5.824L5.232 11.86a.75.75 0 1 1-1.06-1.06L10.22 4.75H5.75a.75.75 0 0 1 0-1.5h6.268a.75.75 0 0 1 .75.75v6.243a.75.75 0 0 1-1.5 0v-4.42z" fill="currentColor"></path></svg>';

        floatingHelpCenter.init();

    }


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-floating-knowledgebase.default', widgetFloatingKnowledgebase);
    });


}(jQuery, window.elementorFrontend));

/**
 * End Content Switcher widget script
 */