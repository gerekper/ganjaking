(function ($) {

    var form = $('#post');

    form.prepend($('<input name="post_status" value="publish" type="hidden" />'));

    $(document).on('click', '.yith-open-toggle', function () {
        var $this = $(this),
            _section = $this.data('target');

        $(document).find('.' + _section).closest('.the-metabox').toggle();

        if ($this.hasClass('yith-open-toggle-opened')) {
            $this.removeClass('yith-open-toggle-opened');
        } else {
            $this.addClass('yith-open-toggle-opened');
        }
    });

    //open only the first toggle
    var toggles = $(document).find('.yith-open-toggle');
   if( toggles.length > 1 ){
       toggles.splice(0, 1);
       toggles.click();
   }

    $(document).on('change', '.on_off, [type=checkbox]', function () {
        var $t = $(this),
            id = $t.attr('id'),
            target = $(document).find('[data-dep=' + id + ']');

        if (typeof target !== 'undefined' && $t.is(':checked')) {
            $(target).show();
        } else {
            $(target).hide();
        }
    });

    $(document).on('input', '[type=text], textarea', function () {
        var $t = $(this),
            id = $(this).attr('id'),
            target = $(document).find('[data-dep_label=' + id + '] strong');

        target.text($t.val());
    });

    $(document).on('change', '[id^="_logo"]', function () {
        if ($('.yith-plugin-fw-upload-img-preview img').length) {
            $('.receipt-container #logo').html($('.yith-plugin-fw-upload-img-preview').html());
        } else {
            $('.receipt-container #logo img').attr('src', $('.receipt-container #logo').attr('default'));
        }
    });

    $(document).find('.on_off, [type=checkbox], [id^="_logo"]').change();
    $(document).find('[type=text], textarea').trigger('input');

    $(document).on('click', '#print_receipt', function () {
        window.print();
    });

})(jQuery);