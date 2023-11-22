/**
 * Start comment widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetComment = function( $scope, $ ) {

		var $comment = $scope.find( '.bdt-comment-container' ),
            $settings = $comment.data('settings');
            
        if ( ! $comment.length ) {
            return;
        }

        if ($settings.layout === 'disqus') {

            var disqus_config = function () {
            this.page.url = $settings.permalink;  // Replace PAGE_URL with your page's canonical URL variable
            this.page.identifier = $comment; // Replace PAGE_IDENTIFIER with your page's unique identifier variable
            };
            
            (function() { // DON'T EDIT BELOW THIS LINE
            var d = document, s = d.createElement('script');
            s.src = '//' + $settings.username + '.disqus.com/embed.js';
            s.setAttribute('data-timestamp', +new Date());
            (d.head || d.body).appendChild(s);
            })();

        } else if ($settings.layout === 'facebook') {
            
            //var $fb_script = document.getElementById("facebook-jssdk");

            //console.log($fb_script);

            // if($fb_script){
            // 	$($fb_script).remove();
            // } else {
            // }

            // jQuery.ajax({
            // 	url: 'https://connect.facebook.net/en_US/sdk.js',
            // 	dataType: 'script',
            // 	cache: true,
            // 	success: function() {
            // 		FB.init( {
            // 			appId: config.app_id,
            // 			version: 'v2.10',
            // 			xfbml: false
            // 		} );
            // 		config.isLoaded = true;
            // 		config.isLoading = false;
            // 		jQuery( document ).trigger( 'fb:sdk:loaded' );
            // 	}
            // });
            // 
            // 
            (function(d, s, id){
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) {return;}
                js = d.createElement(s); js.id = id;
                js.src = 'https://connect.facebook.net/en_US/sdk.js';
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
                

            window.fbAsyncInit = function() {
                FB.init({
                    appId            : $settings.app_id,
                    autoLogAppEvents : true,
                    xfbml            : true,
                    version          : 'v3.2'
                });
            };

        }

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-comment.default', widgetComment );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End comment widget script
 */

