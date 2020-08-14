jQuery( function ( $ ) {
    var post_id = loc_obj.post_id,
        plans_select = $('#_linked-plans');

    plans_select.find('option[value=' + post_id + ']').attr('disabled', true ).trigger('chosen:updated');
} );
