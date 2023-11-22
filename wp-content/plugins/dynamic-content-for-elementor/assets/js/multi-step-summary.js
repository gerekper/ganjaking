(function ($) {
    var WidgetElements_FormHandler = function ($scope, $) {
        var elementSettings = dceGetElementSettings($scope);
        var id_scope = $scope.attr('data-id');
        var summary = elementSettings.dce_step_summary;
        if (summary) {
            $scope.find('input, textarea, select').on('change', function () {
                var custom_id = jQuery(this).attr('name').replace("form_fields[", "").replace("[", "").replace("]", "").replace("]", "").replace("[]", "");
                var input_type = jQuery(this).attr('type') ? jQuery(this).attr('type') : jQuery(this).prop("tagName");
                var input_value = '';
                switch (input_type) {
                    case 'radio':
                        input_value = jQuery('.elementor-element-' + id_scope + ' input[id^="form-field-' + custom_id + '-"]:checked').val();
                        break;
                    case 'checkbox':
                        input_value = jQuery('.elementor-element-' + id_scope + ' input[id^="form-field-' + custom_id + '-"]:checked').map(function () {
                            return jQuery(this).val();
                        }).get();
                        if (input_value) {
                            input_value = input_value.join(', ');
                        }
                        break;
                    case 'select':
                        input_value = jQuery(this).val();
                        // option text intead value
                        input_value = jQuery('.elementor-element-' + id_scope + ' input[id="form-field-' + custom_id + '"] option:selected"').text();
                        break;
                    case 'textarea':
                        input_value = jQuery(this).val();
                        if (input_value.length > 20) {
                            input_value = input_value.substr(0, 20);
                            input_value += '...';
                        }
                        break;
                    default:
                        input_value = jQuery(this).val();
                }
                jQuery('.elementor-element-' + id_scope + ' span[id="dce-summary-value-form-field-' + custom_id + '-' + id_scope + '"]').text(input_value);

                // check step visibility
                setTimeout(function () {
                    jQuery('.elementor-element-' + id_scope + ' .dce-form-step.dce-form-visibility-step').each(function () {
                        var step = jQuery(this);
                        var step_id = step.attr('data-custom_id');
                        var step_summary = jQuery('.elementor-element-' + id_scope + ' #dce-form-step-' + step_id + '-summary');
                        if (step.hasClass('dce-form-visibility-step-show-init')) {
                            if (step.hasClass('dce-form-visibility-step-show')) {
                                step_summary.show();
                            } else {
                                step_summary.hide();
                            }
                        }
                        if (step.hasClass('dce-form-visibility-step-hide-init')) {
                            if (step.hasClass('dce-form-visibility-step-hide')) {
                                step_summary.hide();
                            } else {
                                step_summary.show();
                            }
                        }
                    });

                    jQuery('.elementor-element-' + id_scope + ' *[name^="form_fields"]').each(function () {
                        var custom_id = jQuery(this).attr('name').replace("form_fields[", "").replace("[", "").replace("]", "").replace("]", "").replace("[]", "");
                        if (jQuery(this).prop('disabled')) {
                            jQuery('.elementor-element-' + id_scope + ' #dce-summary-form-field-'+custom_id).hide();
                        } else {
                            jQuery('.elementor-element-' + id_scope + ' #dce-summary-form-field-'+custom_id).show();
                        }
                    });
                }, 100);
            });

            $scope.on('click', '.dce-form-summary-wrapper .elementor-button-submit', function () {
                $scope.find('.dce-form-summary-wrapper').slideUp();
                $scope.find('.elementor-field-type-submit button').trigger('click');
            });
        }

    };

    // Make sure you run this code under Elementor..
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/form.default', WidgetElements_FormHandler);
    });
})(jQuery);
