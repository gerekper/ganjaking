var isAdminBar = false,
        isEditMode = false;


(function ($) {
    var get_acf_ElementSettings = function ($element) {
        var elementSettings = {},
                modelCID = $element.data('model-cid');

        if (elementorFrontend.isEditMode() && modelCID) {
            var settings = elementorFrontend.config.elements.data[ modelCID ],
                    settingsKeys = elementorFrontend.config.elements.keys[ settings.attributes.widgetType || settings.attributes.elType ];

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
    //
    var dropCap = function ($target) {
        if ($target.length) {
            $first_p = $target.html().trim(); // removes any leading whitespace
            if ($first_p.charAt(0) != '<') {
                // not a html tag
                $target.html('<span class="elementor-drop-cap"><span class="elementor-drop-cap-letter">' + $first_p.charAt(0) + '</span></span>' + $first_p.substring(1));
            } else {
                $first_p = $target.find('*:first-child').html().trim();
                $target.find('*:first-child').html('<span class="elementor-drop-cap"><span class="elementor-drop-cap-letter">' + $first_p.charAt(0) + '</span></span>' + $first_p.substring(1));
            }
        }
    };
    var WidgetElementsACFDCEHandler = function ($scope, $) {

        var elementSettings = get_acf_ElementSettings($scope);

		if( elementSettings.acf_type == 'number' ) {
			var acfNumbers = document.querySelectorAll("#" + elementSettings.acf_field_list);
			if ( elementSettings.acf_currency_mode && elementSettings.acf_settoDecimal ) {
				acfNumbers.forEach(function(acfNumber) {
					number = acfNumber.innerHTML;
					number = new Number(number).toLocaleString( elementSettings.acf_currency_type, { minimumFractionDigits: elementSettings.acf_integerDecimalOpt, maximumFractionDigits: elementSettings.acf_integerDecimalOpt });
					acfNumber.innerHTML = number;
				});
			} else if ( elementSettings.acf_currency_mode && !elementSettings.acf_settoDecimal ) {
				acfNumbers.forEach(function(acfNumber) {
					number = acfNumber.innerHTML;
					number = new Number(number).toLocaleString( elementSettings.acf_currency_type );
					acfNumber.innerHTML = number;
				});
			} else if ( !elementSettings.acf_currency_mode && elementSettings.acf_settoDecimal ) {
				acfNumbers.forEach(function(acfNumber) {
					number = acfNumber.innerHTML;
					number = new Number(number).toFixed( elementSettings.acf_integerDecimalOpt );
					acfNumber.innerHTML = number;
				});
			}
		}

        if (elementSettings.drop_cap) {
            var target = $scope.find('p:first');
            if (!target.length) {
                target = $scope.find('.edc-acf:first');
            }
            dropCap(target);
        }
        var bindEvents = function () {
            $scope.find('.elementor-custom-embed-image-overlay').on('click', handleVideo);
        };
        var handleVideo = function () {
            if (elementSettings.lightbox) {
                alert(elementSettings.lightbox);
            } else {
                $(this).fadeOut(1000, function () {
                    $(this).remove();
                    playVideo();
                });
            }
        };
        var playVideo = function () {
            var $videoFrame = $scope.find('iframe'),
                    newSourceUrl = $videoFrame[0].src.replace('&autoplay=0', '');
            $videoFrame[0].src = newSourceUrl + '&autoplay=1';
        };
        bindEvents();
    };

    // Make sure you run this code under Elementor..
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/dyncontel-acf.default', WidgetElementsACFDCEHandler);
    });
})(jQuery);
