jQuery(document).on('elementor/render/tp-field-video',function(e,id,ratio){
    var widget_element = '.elementor-element-'+ id;
    var iframe_load = widget_element + ' .tp-field-video iframe';
    var width = jQuery(iframe_load).width();
    var aspect_Ratio = ratio;
	var radio_a='';
    if(aspect_Ratio == 169){
        radio_a = [16,9];
    }else if(aspect_Ratio == 43){
        radio_a = [4,3]
    }else{
        radio_a = [3,2]
    }

    var iframe_height = width * (radio_a[1]/radio_a[0]);

    jQuery(iframe_load).height(iframe_height);
});