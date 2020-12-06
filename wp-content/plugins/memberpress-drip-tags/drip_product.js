jQuery(document).ready(function($) {
    if($('#meprdriptags_membership_tags_enabled').is(":checked")) {
        $('div#meprdriptags_membership_tags_area').show();
    } else {
        $('div#meprdriptags_membership_tags_area').hide();
    }

    $('#meprdriptags_membership_tags_enabled').click(function() {
        $('div#meprdriptags_membership_tags_area').slideToggle();
    });
}); //End main document.ready func
