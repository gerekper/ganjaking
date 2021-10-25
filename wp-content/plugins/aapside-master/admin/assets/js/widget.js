;(function ($) {
    "use strict";

    $(document).ready(function () {
        /***********************************
         appside Custom widget js
         ************************************/
        $(document).on('widget-updated',function (e,widget) {
            var widget_id = $(widget).attr('id');
            if (widget_id.indexOf('appside_about_us_widget') != 1){
                prefetch();
            }
        });

        $('body').off('click','.appside_flogo_uploader');

        $('body').on('click','.appside_flogo_uploader',function (e) {
            var el = this;

            var file_uploder_frame =  wp.media.frames.file_uploder_frame = wp.media({

                frame : 'post',
                state : 'insert',
                multiple : false

            });

            file_uploder_frame.on('insert',function () {

                var data = file_uploder_frame.state().get('selection');
                var jdata = data.toJSON();
                var selected_ids = _.pluck(jdata,"id");
                var img_prev_container = $(el).siblings('.appside-logo-preview');

                if (selected_ids.length > 0){
                    $(el).css({});
                    $(el).val('Change Image');
                }

                $(el).siblings().children('.appside_logo_id').val(selected_ids.join(","));
                $(el).siblings().children('.appside_logo_id').trigger('change');
                img_prev_container.html('');
                data.map(function (attachment) {
                    if ( attachment.attributes.subtype == "png" || attachment.attributes.subtype == "jpeg" || attachment.attributes().subtype == "jpg" ){
                        try{
                            img_prev_container.append('<img src="'+ attachment.attributes.sizes.full.url +'" />')
                        }catch (e) {

                        }
                    }
                });

            });

            file_uploder_frame.on('open',function () {
                var selection = file_uploder_frame.state().get('selection');
                console.log($(el).siblings().children('.appside_logo_id').val());
                var atts = $(el).siblings().children('.appside_logo_id').val().split(",");

                for (var i=0; i < atts.length; i++){
                    if (atts[i] > 0){
                        selection.add(wp.media.attachment(atts[i]));
                    }
                }

            });


            file_uploder_frame.open();

        });

        function prefetch () {
            $('.appside_logo_id').each(function () {
                var attid = $(this).val();
                var container = $(this).parent().siblings('.appside-logo-preview');
                container.html('');

                if ( attid ){
                    $(this).parent().parent().find('.appside_flogo_uploader').val('Change Image');
                    var attachment = new wp.media.model.Attachment.get(attid);
                    attachment.fetch({
                        success:function (att) {
                            container.append('<img src="'+att.attributes.sizes.full.url+'"/>');
                        }
                    })
                }

            });
        }
        if (wp.customize != undefined){
            $('.customoize-control').on('expand',function (e) {
                var widget_id = $(this).attr('id');
                if(widget_id.indexOf('appside_about_us_widget')!==-1){
                    prefetch();
                }
            })
        }

        prefetch();
    });

})(jQuery);