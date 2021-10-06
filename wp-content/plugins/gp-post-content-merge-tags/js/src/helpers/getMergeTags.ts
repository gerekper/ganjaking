const $ = window.jQuery;

export default function getMergeTags(value: number) {
    const {ajaxurl, gppcmtData} = window;

    if (!gppcmtData.postId || !value) {
        return $.when(null);
    }

    return $.when($.post(ajaxurl, {
        action: 'gppcmt_get_form',
        nonce: gppcmtData.nonce,
        formId: value,
        postId: gppcmtData.postId
    })).then(function (response) {
        var result = $.parseJSON(response);

        // if single form is returned get field merge tags
        if (result.id) {
            if( typeof window.gf_vars == 'undefined' ) {
                window.gf_vars = {};
            }

            window.form = result;
            window.gf_vars.mergeTags = result.mergeTags;

            var GFMergeTags = new window.gfMergeTagsObj(result);
            return $.when(GFMergeTags.getMergeTags(result.fields, '#content'));
        } else {
            return null;
        }
    });
}