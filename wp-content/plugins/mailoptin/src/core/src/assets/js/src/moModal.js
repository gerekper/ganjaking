define(["jquery"], function ($) {

    var methods = {
        open: function (option) {
            option = $.extend({}, $.MoModalBox.defaults, option);

            var elm = this;
            var modal_id = this.attr('id');
            var modal_css_id = modal_id + '_lightbox';

            elm.trigger($.MoModalBox.BEFORE_OPEN, [elm, option]);

            //add MoModalBox class to lightbox
            elm.addClass('moModal');

            // show lightbox
            elm.show();

            var windowHeight = $(window).height(),
                modal_obj = $('#' + modal_css_id + '_container'),
                optinHeight = modal_obj.outerHeight(true);

            // add 15px to the optin height of closIcon is set to be auto added to optin
            if ('iconClose' in option && option.iconClose === true) {
                optinHeight += 15;
            }

            if (optinHeight >= windowHeight) {
                modal_obj.css({top: 10});
            }
            else {
                var top = (windowHeight - (optinHeight)) / 2;
                modal_obj.css({top: top});
            }

            $(window).resize(function () {
                var windowHeight = $(window).height(),
                    modal_obj = $('#' + modal_css_id + '_container'),
                    optinHeight = modal_obj.outerHeight(true);

                if (optinHeight >= windowHeight) {
                    modal_obj.css({top: 10});
                }
                else {
                    var top = (windowHeight - (optinHeight)) / 2;
                    modal_obj.css({top: top});
                }
            });


            //to bind close event
            if ('iconClose' in option && option.iconClose === true) {
                var closeButton = $('<a href="#" rel="moOptin:close" class="mo-optin-form-close-icon mo-close-modal ' + option.closeClass + '">' + option.closeText + '</a>');
                elm.find('.mo-optin-form-wrapper').append(closeButton);
            }

            // determine if close by esc key is possible
            if (option.keyClose) {
                $(document).on('keyup.moModal', keyEvent);
            }

            if (option.bodyClose) {
                /* give close event to overlay and not in the body to come out of bubbling issue */
                var overlay = $('#' + modal_id + '.mo-optin-form-lightbox');

                overlay.on('click', function (event) {
                    if (event.target == overlay.get(0)) {
                        $.MoModalBox.close();
                    }
                });
            }

            // add flag to determine if a modal is currently active.
            $.MoModalBox.isActive = true;

            //call callback function
            option.onOpen.call(this);
            elm.data('closeFun', option.onClose);

            elm.trigger($.MoModalBox.OPEN, [elm, option]);
        },
        close: function () {
            var elm = this;

            if (elm.hasClass('moModal')) {

                elm.trigger($.MoModalBox.BEFORE_CLOSE, [elm]);

                elm.fadeOut(400, function () {
                    elm = $(this);

                    if (typeof elm.data !== "undefined" && typeof elm.data('closeFun') !== "undefined" && typeof elm.data('closeFun').call !== "undefined") {
                        //call callback function
                        elm.data('closeFun').call(this);
                    }

                    //restore modal box
                    elm.removeData('closeFun')
                        .removeClass('moModal'); //remove class

                    elm.trigger($.MoModalBox.CLOSE, [elm]);

                    //if all modal box is closed unbind all events.
                    if ($('.moModal').length === 0) {
                        $('.mo-optin-form-lightbox').hide();
                        $(document).off('keyup.moModal');
                    }

                    $.MoModalBox.isActive = false;

                    elm.trigger($.MoModalBox.AFTER_CLOSE, [elm]);

                });
            }
        }
    };

    $.fn.MoModalBox = function (method, option) {
        // if there exist an active modal, bail.
        if ($.MoModalBox.isActive) return this;

        // this here is the element collection
        if (methods[method]) {
            methods[method].call(this, option);
        } else if (typeof method === 'object' || !method) {
            methods.open.call(this, method);
        }

        return this;
    };

    $.MoModalBox = {};

    //default options
    $.MoModalBox.defaults = {
        overlay: true,
        iconClose: true,
        closeClass: '',
        closeText: 'Close',
        keyClose: true,
        bodyClose: true,

        //callback function
        onOpen: function () {
        },

        onClose: function () {
        }
    };

    //to close all modal box
    $.MoModalBox.close = function () {
        methods.close.call($('.moModal'));
    };

    // close if esc key is pressed.
    var keyEvent = function (e) {
        var keyCode = e.keyCode;
        //check for esc key is pressed.
        if (keyCode == 27) {
            $.MoModalBox.close();
        }
    };

    // close if close icon is clicked
    var closeClickEvent = function (e) {
        e.preventDefault();
        $.MoModalBox.close();
    };

    // Event constants
    $.MoModalBox.BEFORE_OPEN = 'moOptin:before-open';
    $.MoModalBox.OPEN = 'moOptin:open';
    $.MoModalBox.BEFORE_CLOSE = 'moOptin:before-close';
    $.MoModalBox.CLOSE = 'moOptin:close';
    $.MoModalBox.AFTER_CLOSE = 'moOptin:after-close';

    // Automatically bind links with rel="modal:close" to, well, close the modal.
    $(document).on('click.moOptin', 'a[rel~="moOptin:close"]', closeClickEvent);
    $(document).on('click.moOptin', '.mo-close-optin', closeClickEvent);
});