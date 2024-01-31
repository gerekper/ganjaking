jQuery(function ($) {
    $(".a2w_reset_order_data").on('click', function(event){
        event.preventDefault();
        const id = $('#post_ID').val();
        const data = {'action': 'a2w_reset_order_data', 'id': id};
        $.post(ajaxurl, data).done(function (response) {
            const json = jQuery.parseJSON(response);
            if (json.state !== 'ok') {
                alert('error');
                console.log(json);
            } else {
                window.location.reload();
            }
        }).fail(function (xhr, status, error) {
            console.log(error);
        });
    });   	
});
