/* Give selected element 'crnt' class */
jQuery(document).ready(function ($) {
    
    $(".wcc-sticky-list li").on("click", function () {

        var selectedItemHTML = $(this).html();
        $(".wcc-sticky-list").find(".crnt").removeClass("crnt");
        $(this).addClass("crnt");
       

        
        var code = $(this).data('code');
        $('.wccs_sticky_form .wcc_switcher').val(code);
        setTimeout(function(){ 
            $('form.wccs_sticky_form').submit();
        }, 500);
    });

    jQuery('.wcc-sticky-list').slick({
      vertical: true,
      dots: false,
      infinite: false,
      speed: 300,
      slidesToShow: 5,
      slidesToScroll: 5,
      arrow: true,
      prevArrow: '#wccs_sticky_up',
      nextArrow: '#wccs_sticky_down',
    });

    /*$("#wccs_sticky_down").on( 'mousedown', function () {
        //debugger;
        animateContent("down");

    }).on( 'mouseup', function() {
        $('.wcc-sticky-list').stop(); 
        $mT = Math.abs(parseFloat($('.wcc-sticky-list').css("margin-top")));
        $h = $('.wcc-sticky-list').height() - 266;
        if ( $mT == 0 || $mT == '' ) {
            $('#wccs_sticky_container').addClass('noMoreTop');
            $('#wccs_sticky_container').removeClass('noMoreBottom');
        } else if ( $mT < $h ) { 
            $('#wccs_sticky_container').removeClass('noMoreTop');
            $('#wccs_sticky_container').removeClass('noMoreBottom');
        } else {
            $('#wccs_sticky_container').removeClass('noMoreTop');
            $('#wccs_sticky_container').addClass('noMoreBottom');
        }
    });

    $("#wccs_sticky_up").on( 'mousedown', function () {
        animateContent("up");
    }).on( 'mouseup', function() { 
        $('.wcc-sticky-list').stop(); 
        $mT = Math.abs(parseFloat($('.wcc-sticky-list').css("margin-top")));
        //console.log('margin top is ' + $mT );
        if ( $mT == 0 || $mT == '' ) {
            $('#wccs_sticky_container').addClass('noMoreTop');
            $('#wccs_sticky_container').removeClass('noMoreBottom');
        } else if ( $mT < $h ) { 
            $('#wccs_sticky_container').removeClass('noMoreTop');
            $('#wccs_sticky_container').removeClass('noMoreBottom');
        } else {
            $('#wccs_sticky_container').removeClass('noMoreTop');
            $('#wccs_sticky_container').addClass('noMoreBottom');
        }
    });

    
    function animateContent(direction) {  

        var animationOffset = $('#wcc-sticky-list-wrapper').height() - $('.wcc-sticky-list').height();
        if (direction == 'up') {
            animationOffset = 0;
        }
        
        $('.wcc-sticky-list').animate({ "marginTop": animationOffset + "px" }, 2000);
    }*/

});