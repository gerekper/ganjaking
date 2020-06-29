// I converted to using windows.load because customizer ready event wasn't firing in email customizer.
jQuery(window).on('load', function () {
    jQuery('[data-block-type="ace"]').each(function () {
        var editor_id = jQuery(this).attr('id');
        var theme = jQuery(this).data('ace-theme');
        var language = jQuery(this).data('ace-lang');

        var editor = ace.edit(editor_id);
        var session = editor.getSession();
        // disable syntax checker https://stackoverflow.com/a/13016089/2648410
        session.setUseWorker(false);
        editor.setTheme("ace/theme/" + theme);
        session.setMode("ace/mode/" + language);
        editor.$blockScrolling = Infinity;
        session.setValue(jQuery('textarea#' + editor_id + '-textarea').val());

        editor.on('change', function () {
            jQuery('textarea#' + editor_id + '-textarea').val(session.getValue()).change();

        });
    });
});