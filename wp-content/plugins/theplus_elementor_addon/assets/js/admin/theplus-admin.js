(function ($) {
	"use strict";
	$(document).ready(function() {
		/* woo swatches
		* @since 4.1.8		
		* Woocommerce Custom Color Picker
		*/
		if($('.tp-color-picker').length > 0 ){
			$('input.tp-color-picker').wpColorPicker();
		}
		
		//Woocommerce Custom Image Uploader
		$(document).on('click', 'button.tp_upload_image_button', function(e){
				e.preventDefault();
                e.stopPropagation();

                var file_frame = void 0;
				var _this = this;

                if (typeof wp !== 'undefined' && wp.media && wp.media.editor) {

                    // already exists, reopen
                    if (file_frame) {
                        file_frame.open();
                        return;
                    }

                    // Create frame.
                    file_frame = wp.media.frames.select_image = wp.media({
                        title: 'Choose an Image',
                        button: {
                            text: 'Use Image'
                        },
                        multiple: false
                    });

                    // image
                    file_frame.on('select', function () {
                        var attachment = file_frame.state().get('selection').first().toJSON();

                        if ($.trim(attachment.id) !== '') {

                            var url = typeof attachment.sizes.thumbnail === 'undefined' ? attachment.sizes.full.url : attachment.sizes.thumbnail.url;

                            $(_this).prev().val(attachment.id);
                            $(_this).closest('.tp-meta-image-field-wrapper').find('img').attr('src', url);
                            $(_this).next().show();
                        }
                    });

                    // selected
                    file_frame.on('open', function () {
                        
                        var selection = file_frame.state().get('selection');
                        var current = $(_this).prev().val();
                        var attachment = wp.media.attachment(current);
                        attachment.fetch();
                        selection.add(attachment ? [attachment] : []);
                    });
                   
                    file_frame.open();
                }
		});
		
		//ajax get acf field on post id
		if($("#tp_preview_post_input").length){
			$("#tp_preview_post_input").focusout(function(){			
			var tp_render_mode = $('[name="tp_render_mode_type"]').val();
            if(tp_render_mode != 'acf_repeater'){
                return;
            }
            var post_id = $("#tp_preview_post").val();
            jQuery.ajax({
                url: ajaxurl,
                dataType: 'json',
				type: "post",
                data: {
                    action: 'plus_acf_repeater_field',
                    post_id: post_id,
					security: theplus_nonce,
                },
                success: function (res) {
				
                    jQuery("#tp_acf_field_name").find('option').remove().end();
                    if(res.data.length){
                        jQuery.each(res.data, function(i, d) {
                            jQuery("#tp_acf_field_name").append(jQuery("<option/>", {
                                value: d.meta_id,
                                text: d.text
                            }));
                        });
                    }
                }
            });
			});
		}
	});
})(window.jQuery);