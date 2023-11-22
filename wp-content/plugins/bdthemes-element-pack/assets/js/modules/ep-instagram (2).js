/**
 * Start instagram widget script
 */
 
( function( $, elementor ) {

	'use strict';

	var widgetInstagram = function( $scope, $ ) {

		var $instagram = $scope.find( '.bdt-instagram' ),
            $settings  = $instagram.data('settings'),
            $loadMoreBtn = $instagram.find('.bdt-load-more');

        if ( ! $instagram.length ) {
            return;
        }
    
        var $currentPage = $settings.current_page;

        
        
        callInstagram();

        $($loadMoreBtn).on('click', function(event){
            
            if ($loadMoreBtn.length) {
                $loadMoreBtn.addClass('bdt-load-more-loading');
            }

            $currentPage++;
            $settings.current_page = $currentPage;

            callInstagram();
        });


        function callInstagram(){
            var $itemHolder = $instagram.find('.bdt-grid');

            jQuery.ajax({
                url: window.ElementPackConfig.ajaxurl,
                type:'post',
                data: $settings,
                success:function(response){
                    if($currentPage === 1){
                        // $itemHolder.html(response);	 
                        $itemHolder.append(response);	
                    } else {
                        $itemHolder.append(response);
                    }

                    if ($loadMoreBtn.length) {
                        $loadMoreBtn.removeClass('bdt-load-more-loading');
                    }

                }
            });
        }

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-instagram.default', widgetInstagram );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-instagram.bdt-instagram-carousel', widgetInstagram );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-instagram.bdt-classic-grid', widgetInstagram );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End instagram widget script
 */

