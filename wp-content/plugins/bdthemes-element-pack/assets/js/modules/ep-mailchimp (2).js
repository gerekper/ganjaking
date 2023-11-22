/**
 * Start mailchimp widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetMailChimp = function( $scope, $ ) {

		var $mailChimp = $scope.find('.bdt-mailchimp');
			
        if ( ! $mailChimp.length ) {
            return;
        }

        var langStr = window.ElementPackConfig.mailchimp;

        $mailChimp.submit(function(){
            
            var mailchimpform = $(this);
            bdtUIkit.notification({message: '<span bdt-spinner></span> ' + langStr.subscribing, timeout: false, status: 'primary'});
            $.ajax({
                url:mailchimpform.attr('action'),
                type:'POST',
                data:mailchimpform.serialize(),
                success:function(data){
                    bdtUIkit.notification.closeAll();
                    bdtUIkit.notification({message: data, status: 'success'});
                    
                    // set local storage for coupon reveal
                    // localStorage.setItem("epCouponReveal", 'submitted');
                }
            });
            return false;

        });

        return false;

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-mailchimp.default', widgetMailChimp );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End mailchimp widget script
 */

