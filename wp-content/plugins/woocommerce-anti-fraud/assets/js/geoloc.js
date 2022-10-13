jQuery(document).ready(function(){
    jQuery(document).on( "updated_checkout", function(){
        if(navigator.geolocation){

            navigator.geolocation.getCurrentPosition(function(position) {
            
                var latitude = position.coords.latitude;
                var longitude = position.coords.longitude;
            
                jQuery.ajax({
                    url: myAjax.ajaxurl,
                    type : "POST",
                    data: {
                        'action':'my_action_geo_country',
                        'latitude':latitude,
                        'longitude':longitude,
                    },
                    success:function(response) {
                        // This outputs the result of the ajax request
                        console.log(response);
                       //alert(response); 
                    }
                    
                }); 
                
            }, function(error) {
                if (error.code == error.PERMISSION_DENIED)
                    jQuery.ajax({
                        url: myAjax.ajaxurl,
                        type : "POST",
                        data: {
                            'action':'my_action_geo_country',
                            'latitude':'',
                            'longitude':'',
                        },
                        success:function(response) {
                            // This outputs the result of the ajax request
                            console.log(response);
                        }
                    
                }); 
            });
        } 
    }); 
});
