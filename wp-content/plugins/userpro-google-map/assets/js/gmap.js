jQuery(document).ready(function() {

//deleteAllCookies();
    myMap();
});
function deleteAllCookies() {
    var cookies = document.cookie.split(";");
    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i];
        var eqPos = cookie.indexOf("=");
        var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
        document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
    }
}
function myMap() {
    var add1, add2, add3, selectedCountry, final_add;
    add1 = jQuery( ".userpro-field[data-key=address_line_1]" ).find('input').val();
    if( typeof add1 == 'undefined' ){
        add1 = jQuery( ".userpro-field[data-key=address_line_1]" ).find('.userpro-input').text();
    }
    add2 = jQuery( ".userpro-field[data-key=address_line_2]" ).find('input').val();
    if( typeof add2 == 'undefined' ){
        add2 = jQuery( ".userpro-field[data-key=address_line_2]" ).find('.userpro-input').text();
    }
    add3 = jQuery( ".userpro-field[data-key=address_line_3]" ).find('input').val();
    if( typeof add3 == 'undefined' ){
        add3 = jQuery( ".userpro-field[data-key=address_line_3]" ).find('.userpro-input').text();
    }
    selectedCountry = jQuery( ".userpro-field[data-key=country]" ).find('select').val();

    if( typeof selectedCountry == 'undefined' ){
        selectedCountry = jQuery( ".userpro-field[data-key=country]" ).find('.userpro-input').text();
    }


    var to_add_val = [ ];
    if( add1.length )
        to_add_val.push( add1 );
    if( add2.length )
        to_add_val.push( add2 );
    if( add3.length )
        to_add_val.push( add3 );
    if( selectedCountry.length )
        to_add_val.push( selectedCountry );

    final_add = to_add_val.join( ',' );

    load_map(final_add);

	/*get address 1 on change */
    jQuery( ".userpro-field[data-key=address_line_1]" ).find('input').on('change', function() {
        //deleteAllCookies();
        add1 = jQuery( ".userpro-field[data-key=address_line_1]" ).find('input').val();
        final_add = '';

        final_add = add1+", "+add2+", "+add3+", "+selectedCountry;
        load_map(final_add);
    });

	/*get address 2 on change */
    jQuery( ".userpro-field[data-key=address_line_2]" ).find('input').on('change', function() {
        //deleteAllCookies();
        add2 = jQuery( ".userpro-field[data-key=address_line_2]" ).find('input').val();
        final_add = '';
        final_add = add1+", "+add2+", "+add3+", "+selectedCountry;
        load_map(final_add);
    });

	/*get address 3 on change */
    jQuery( ".userpro-field[data-key=address_line_3]" ).find('input').on('change', function() {
        //deleteAllCookies();
        add3 = jQuery( ".userpro-field[data-key=address_line_3]" ).find('input').val();
        final_add = '';
        final_add = add1+", "+add2+", "+add3+", "+selectedCountry;
        setTimeout(load_map(final_add), 20000);
    });

	/*get country on change */
    jQuery( ".userpro-field[data-key=country]" ).find('select').on('change', function() {
        //deleteAllCookies();
        var optionSelected = jQuery("option:selected", this);
        var selectedCountry = this.value;
        final_add = '';
        final_add = add1+", "+add2+", "+add3+", "+selectedCountry;
        setTimeout(load_map(final_add), 20000);
    });
}

function load_map(final_add){
    var gkey = gmap_object.gmap_key_value;
    jQuery("#map").addClass('up-google-map-holder');
    var address = final_add.replace(/\s/g, "+");

    jQuery.ajax({
        type: "GET",
        dataType: "json",
        url: "https://maps.googleapis.com/maps/api/geocode/json",
        data: {'address': address, 'key': gkey},
        success: function(data){
            if(data.results.length){
                var lat = data.results[0].geometry.location.lat;
                var long = data.results[0].geometry.location.lng;
                var formatted_address = data.results[0].formatted_address;
                var myOptions = {
                    zoom: 10
                }

                var map = new google.maps.Map(document.getElementById('map'), myOptions);
                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(lat,long),
                    map: map,
                    title:"Fast marker"
                });
                var new_add = formatted_address.split(' ').join('+');
                jQuery("#map").html('<div id="gmap_canvas" style="height:100%; width:100%;max-width:100%;"><iframe style="height:100%;width:100%;border:0;" frameborder="0" src="https://www.google.com/maps/embed/v1/place?q='+new_add+'&key='+gkey+'"></iframe></div>');
            }else{
                jQuery("#map").html('<span class="gmap-invalid-addr">Please enter valid address</span>');
                jQuery("#map").removeClass('up-google-map-holder');
            }
        }
    });
}