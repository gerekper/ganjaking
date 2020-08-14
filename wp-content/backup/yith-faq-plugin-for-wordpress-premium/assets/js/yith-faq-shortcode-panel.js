jQuery(function ($) {

    $('#enable_search_box, #enable_category_filter, #page_size, #icon_size, #style, #show_icon, #categories').change(function () {
        add_parameter()
    });

    $('.yit-icons-manager-wrapper').click(function () {
        add_parameter()
    });

    $('#style').change(function () {

        if ($('input[name="style"]:checked').val() === 'list') {
            $('#show_icon_row, #icon_size_row, #icon_row').hide()
        } else {
            $('#show_icon_row, #icon_size_row, #icon_row').show()
        }

    }).change();

    $('#show_icon').change(function () {

        if ($('input[name="show_icon"]:checked').val() === 'off' || $('input[name="style"]:checked').val() === 'list') {
            $('#icon_size_row, #icon_row').hide()
        } else {
            $('#icon_size_row, #icon_row').show()
        }

    }).change();

    function add_parameter() {

        var shortcode = '[yith_faq]',
            search_box = $('#enable_search_box').val(),
            category_filters = $('#enable_category_filter').val(),
            choose_style = $('input[name="style"]:checked').val(),
            page_size = $('#page_size').val(),
            categories = $('#categories').val(),
            show_icon = $('input[name="show_icon"]:checked').val(),
            icon_size = $('#icon_size').val(),
            icon = $('#icon').val(),
            args = [];

        if (search_box === 'yes') {
            args.push('search_box="on"');
        }

        if (category_filters === 'yes') {
            args.push('category_filters="on"');
        }

        if (choose_style !== 'list') {
            args.push('style="' + choose_style + '"');
        }

        if (categories !== null && categories.length > 0) {
            args.push('categories="' + categories.join(',') + '"');
        }

        if (page_size !== '10') {
            args.push('page_size="' + page_size + '"');
        }

        if (show_icon !== 'off' && choose_style !== 'list') {
            args.push('show_icon="' + show_icon + '"');
        }

        if (icon_size !== '14' && show_icon !== 'off') {
            args.push('icon_size="' + icon_size + '"');
        }

        if (icon !== 'FontAwesome:plus' && show_icon !== 'off') {
            args.push('icon="' + icon + '"');
        }

        if (typeof args !== 'undefined' && args.length > 0) {
            shortcode = '[yith_faq ' + args.join(' ') + ']';
        }

        $('#shortcode').val(shortcode);

    }

    $(document).on('click', '.insert-shortcode', function () {

        var win = window.dialogArguments || opener || parent || top,
            ed;

        win.send_to_editor($('#shortcode').val());

        if (typeof tinyMCE != 'undefined' && (ed = tinyMCE.activeEditor) && !ed.isHidden()) {
            ed.setContent(ed.getContent());
        }

    });

});