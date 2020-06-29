wp.customize.controlConstructor.mailoptin_datetime = wp.customize.Control.extend({
    ready: function () {
        var control = this;
        wp.customize.Control.prototype.ready.call(control);

        var datetime_container = jQuery(".mo-date-picker");
        datetime_container.datetimepicker({
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'bottom'
            },
            sideBySide: false,
            // add option to disable this for start time. take cue from chosen single on how to pass config
            // minDate: moment(),
            icons: {
                time: 'dashicons dashicons-clock',
                date: 'dashicons dashicons-calendar-alt',
                up: 'dashicons dashicons-arrow-up-alt2',
                down: 'dashicons dashicons-arrow-down-alt2',
                previous: 'dashicons dashicons-arrow-left-alt2',
                next: 'dashicons dashicons-arrow-right-alt2',
                today: 'dashicons dashicons-screenoptions',
                clear: 'dashicons dashicons-trash'
            }
        });

        datetime_container.on('dp.change', function () {
            jQuery(this).trigger('change');
        });
    }
});