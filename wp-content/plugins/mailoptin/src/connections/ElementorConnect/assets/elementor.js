(function ($) {

    $(window).on('load', function () {

        var e = elementor,
            ep = elementorPro,
            MailoptinIntegration = {
                fields: moElementor.fields,
                cache: {},

                getName: function getName() {
                    return 'mailoptin';
                },

                onElementChange: function onElementChange(setting) {
                    var self = this;
                    switch (setting) {
                        case 'mailoptin_connection':
                        case 'mailoptin_connection_list':
                            self.updateTags();
                            self.updateFieldsMap();
                            break;
                    }
                },

                onSectionActive: function onSectionActive() {
                    this.updateTags();
                    this.updateFieldsMap();
                },

                updateTags: function updateTags() {
                    var self = this;
                    var connect_service = self.getEditorControlView('mailoptin_connection').getControlValue();

                    if ($.inArray(connect_service, moElementor.select2_tag_connections) !== -1) {
                        //empty out the select2 options
                        self.updateOptions('mailoptin_tags_select2', []);

                        self.addControlSpinner('mailoptin_connection');

                        data = {
                            'action': 'mo_elementor_fetch_tags',
                            'nonce': moElementor.nonce,
                            'connection': self.getEditorControlView('mailoptin_connection').getControlValue()
                        };

                        self.getEditorControlView('mailoptin_tags_text').$el.hide();
                        self.getEditorControlView('mailoptin_tags_select2').$el.hide();

                        $.post(moElementor.ajax_url, data, function (response) {

                            if ('success' in response && response.success === true) {
                                self.updateOptions('mailoptin_tags_select2', response.data);
                                self.getEditorControlView('mailoptin_tags_select2').$el.show();
                            }

                            self.removeControlSpinner('mailoptin_connection');
                        });

                        return;
                    }

                    self.getEditorControlView('mailoptin_tags_select2').$el.hide();
                    self.getEditorControlView('mailoptin_tags_text').$el.hide();

                    if ($.inArray(connect_service, moElementor.text_tag_connections) !== -1) {
                        self.getEditorControlView('mailoptin_tags_text').$el.show();
                    }
                },

                removeControlSpinner: function removeControlSpinner(name) {
                    var $controlEl = this.getEditorControlView(name).$el;

                    $controlEl.find(':input').attr('disabled', false);
                    $controlEl.find('.elementor-control-spinner').remove();
                },

                updateFieldsMap: function updateFieldsMap() {
                    var self = this, data, key, controlView = self.getEditorControlView('mailoptin_connection_list'),
                        connection = self.getEditorControlView('mailoptin_connection').getControlValue();

                    if (connection === 'leadbank') return;

                    if (!controlView.getControlValue()) return;

                    data = {
                        'action': 'mo_elementor_fetch_custom_fields',
                        'nonce': moElementor.nonce,
                        'connection': connection,
                        'connection_email_list': controlView.getControlValue()
                    };

                    key = data.connection + '_' + data.connection_email_list;

                    if (typeof self.cache[key] != 'undefined' && !_.isEmpty(self.cache[key])) {
                        return self.getEditorControlView('mailoptin_fields_map').updateMap(self.cache[key]);
                    }

                    self.addControlSpinner('mailoptin_connection');
                    self.addControlSpinner('mailoptin_connection_list');

                    // hide the mapping view
                    self.getEditorControlView('mailoptin_fields_map').$el.hide();

                    $.post(moElementor.ajax_url, data, function (response) {
                        if ('success' in response && response.success === true) {
                            result = self.cache[key] = response.data.fields;
                            self.getEditorControlView('mailoptin_fields_map').updateMap(result);
                            self.getEditorControlView('mailoptin_fields_map').$el.show();
                        }

                        self.removeControlSpinner('mailoptin_connection');
                        self.removeControlSpinner('mailoptin_connection_list');
                    });
                },
            };

        ep.modules.forms.mailoptin = Object.assign(ep.modules.forms.mailchimp, MailoptinIntegration);

        ep.modules.forms.mailoptin.addSectionListener('section_mailoptin', MailoptinIntegration.onSectionActive);

    });

})(jQuery);