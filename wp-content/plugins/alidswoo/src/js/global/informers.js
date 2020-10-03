/**
 * Created by Denis Zharov on 10.04.2018.
 */
jQuery(function($){

    if(typeof alidAjaxFront !== 'undefined'){
        ajaxurl = alidAjaxFront.ajaxurl;
    }

    let $this;

    let $obj = {
        counter : $('#wp-admin-bar-upluad-image-informer .counter')
    };

    let ImportReviews = {

        request: function (callback) {
            $.ajax({
                url: ajaxurl,
                data: { action: 'adsw_get_image_upload_residue'},
                type: 'POST',
                dataType: 'json',
                success: callback
            });
        },

        set_result: function( response ){
            $obj.counter.text( response.count_images.message );

            if( parseFloat(response.count_images.count) > 0 ){
                setTimeout( $this.get_count_image,  5000);
            }

        },

        get_count_image: function(){
            $this.request($this.set_result)
        },

        init: function() {
            $this = this;
            if( parseFloat( $obj.counter.data('count') )  > 0 ){
                setTimeout( $this.get_count_image, 5000);
            }
        },

    };
    ImportReviews.init();

});