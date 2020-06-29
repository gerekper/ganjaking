var array_unique_noempty, element_box;

jQuery(function ($) {

    $('body')
        .on('click', '.ywrr-bulk-actions', function () {

            var container = $(this).parent();

            container
                .find('.ywrr-ajax-result')
                .remove();

            container.append('<div class="ywrr-ajax-result progress">' + ywrr_admin.please_wait + '</div>');

            $.post(ywrr_admin.ajax_url, {action: $(this).data('action')}, function (response) {

                container
                    .find('.ywrr-ajax-result')
                    .removeClass('progress')
                    .addClass(response.success === true ? 'success' : 'fail')
                    .html(response.message);

            });

        })
        .on('click', '.ywrr-compact-list__show-more, .ywrr-compact-list__hide-more', function (e) {
            e.stopPropagation();
            var _list = $(this).closest('.ywrr-compact-list'),
                _hiddenItems = _list.find('.ywrr-compact-list__hidden-items');
            _list.toggleClass('ywrr-compact-list--open');
            if (_hiddenItems.length) {
                if (_list.is('.ywrr-compact-list--open')) {
                    _hiddenItems.slideDown(300);
                } else {
                    _hiddenItems.slideUp(300);
                }
            }
        });

    $(document).ready(function ($) {

        $('select#ywrr_mail_template').change(function () {
            $(this).parent().find('.ywrr-mailskin').remove();
            $(this).parent().append('<div class="ywrr-mailskin"><img src="#" /></div>');

            var skin = $(this).val(),
                preview = $('.ywrr-mailskin img');

            preview.fadeOut('fast', function () {
                preview.attr('src', ywrr_admin.assets_url + '/images/skins/' + skin + '.png').fadeIn('fast');
            });

        }).change();

        element_box.init();

    });

    array_unique_noempty = function (array) {
        var out = [];

        $.each(array, function (key, val) {
            val = $.trim(val);

            if (val && $.inArray(val, out) === -1) {
                out.push(val);
            }
        });

        return out;
    };

    element_box = {
        clean: function (tags) {
            tags = tags.replace(/\s*,\s*/g, ',').replace(/,+/g, ',').replace(/[,\s]+$/, '').replace(/^[,\s]+/, '');
            return tags;
        },

        parseTags: function (el) {
            var id = el.id,
                num = id.split('-check-num-')[1],
                element_box = $(el).closest('.ywcc-checklist-div'),
                values = element_box.find('.ywcc-values'),
                current_values = values.val().split(','),
                new_elements = [];

            delete current_values[num];

            $.each(current_values, function (key, val) {
                val = $.trim(val);
                if (val) {
                    new_elements.push(val);
                }
            });

            values.val(this.clean(new_elements.join(',')));

            this.quickClicks(element_box);
            return false;
        },

        quickClicks: function (el) {

            var values = $('.ywcc-values', el),
                values_list = $('.ywcc-value-list ul', el),
                id = $(el).attr('id'),
                current_values;

            if (!values.length)
                return;

            current_values = values.val().split(',');
            values_list.empty();

            $.each(current_values, function (key, val) {

                var item, xbutton;

                val = $.trim(val);

                if (!val)
                    return;

                item = $('<li class="select2-selection__choice" />');
                xbutton = $('<span id="' + id + '-check-num-' + key + '" class="select2-selection__choice__remove" tabindex="0"></span>');

                xbutton.on('click keypress', function (e) {

                    if (e.type === 'click' || e.keyCode === 13) {

                        if (e.keyCode === 13) {
                            $(this).closest('.ywcc-checklist-div').find('input.ywcc-insert').focus();
                        }

                        element_box.parseTags(this);
                    }

                });

                item.prepend(val).prepend(xbutton);

                values_list.append(item);

            });
        },

        flushTags: function (el, a, f) {
            var current_values,
                new_values,
                text,
                values = $('.ywcc-values', el),
                add_new = $('input.ywcc-insert', el);

            a = a || false;

            text = a ? $(a).text() : add_new.val();

            if ('undefined' === typeof (text)) {
                return false;
            }

            current_values = values.val();
            new_values = current_values ? current_values + ',' + text : text;
            new_values = this.clean(new_values);
            new_values = array_unique_noempty(new_values.split(',')).join(',');
            values.val(new_values);

            this.quickClicks(el);

            if (!a)
                add_new.val('');
            if ('undefined' === typeof (f))
                add_new.focus();

            return false;

        },

        init: function () {
            var ajax_div = $('.ywcc-checklist-ajax');

            $('.ywcc-checklist-div').each(function () {
                element_box.quickClicks(this);
            });

            $('input.ywcc-insert', ajax_div).keyup(function (e) {
                if (13 === e.which) {
                    element_box.flushTags($(this).closest('.ywcc-checklist-div'));
                    return false;
                }
            }).keypress(function (e) {
                if (13 === e.which) {
                    e.preventDefault();
                    return false;
                }
            });


        }
    };

});



