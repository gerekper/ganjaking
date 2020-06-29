jQuery(function($){
	$('.yith-wcet-color-picker').wpColorPicker();

    var logo_input                       = $('#yith-wcet-logo-url'),
        logo_up_image                    = $('#yith-wcet-logo-image'),
        logo_and_del_container           = $('#yith-wcet-logo-and-del-container'),
        custom_logo_url                  = $('#yith-wcet-custom-logo-url').val(),
        remove_logo_btn                  = $('#yith-wcet-remove-logo-btn');


    //upload button action
    $('#yith-wcet-upload-btn').on('click', function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload Image',
            multiple: false
        }).open()
        .on('select', function(e){
            var uploaded_image = image.state().get('selection').first(),
                logo_url = uploaded_image.toJSON().url;

                logo_input.val(logo_url);
                logo_up_image.attr('src', logo_url);
                logo_up_image.show();
        });
    });

    //custom logo button action
    $('#yith-wcet-custom-logo-btn').on('click', function(e) {
        logo_input.val(custom_logo_url);
        if ( custom_logo_url.length > 0 ){
            logo_up_image.attr('src', custom_logo_url);
            logo_up_image.show();
        }
    });

    logo_and_del_container.on('mouseover', function(e){
        remove_logo_btn.show();
    });

    logo_and_del_container.on('mouseout', function(e){
        remove_logo_btn.hide();
    });

    remove_logo_btn.on('click', function(){
        logo_input.val('');
        logo_up_image.attr('src', '');
        logo_up_image.hide();
    });
});