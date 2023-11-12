( function( $ ) {
	var SocialShareHandler = function( $scope, $ ) {

		if ( 'undefined' == typeof $scope )
			return;

		var scope_id = $scope.data( 'id' );
  		var wrapper = $scope.find( '.uael-container' );
  		var url = wrapper.data( 'pin_data_url' );
  		var share_url_type = wrapper.data( 'share_url_type' );
  		var share_url = wrapper.data( 'share_url' );
  		var uael_image_url, uael_page_url;

  		var top = window.screen.height - 400;
    	top = top > 0 ? top/2 : 0;
		var left = window.screen.width - 600;
	    left = left > 0 ? left/2 : 0;

		var location_url = window.location.href;
  		
		if( 'current_page' == share_url_type ){

	  		uael_page_url = location_url;
		} else {

	 		uael_page_url = share_url;

			if ( '' == uael_page_url ){

				uael_page_url = location_url;
			}
		 }

		uael_share_links = {
			twitter: 'https://twitter.com/intent/tweet?url=' + uael_page_url,
			pinterest: 'https://www.pinterest.com/pin/create/button/?url=' + uael_page_url + '&media='+url,
			facebook: 'https://www.facebook.com/sharer.php?u=' + uael_page_url,
			vk: 'https://vkontakte.ru/share.php?url=' + uael_page_url,
			linkedin: 'https://www.linkedin.com/shareArticle?mini=true&url=' + uael_page_url,
			odnoklassniki: 'https://connect.ok.ru/offer?url=' + uael_page_url,
			tumblr: 'https://www.tumblr.com/widgets/share/tool/preview?canonicalUrl=' + uael_page_url,
			delicious: 'https://del.icio.us?url='+encodeURIComponent(uael_page_url)+'&title=ShareButton',
			digg: 'https://digg.com/submit?url=' + uael_page_url,
			reddit: 'https://reddit.com/submit?url=' + uael_page_url,
			stumbleupon: 'https://www.stumbleupon.com/submit?url=' + uael_page_url,
			pocket: 'https://getpocket.com/edit?url=' + uael_page_url,
			whatsapp: 'https://api.whatsapp.com/send?text=' + uael_page_url,
			xing: 'https://www.xing.com/app/user?op=share&url=' + uael_page_url,
			email: 'mailto:?subject={SocialShare}&body={SocialShare}\n{' + uael_page_url + '}',
			telegram: 'https://telegram.me/share/url?url=' + uael_page_url,
			skype: 'https://web.skype.com/share?url=' + uael_page_url,
			buffer: 'https://buffer.com/add?url=' + uael_page_url,
		};

		if( ! elementorFrontend.isEditMode() ) {
			var uael_share_link_index = Object.keys( uael_share_links );

			$.each( uael_share_link_index , function( links ) {
				$scope.find( '.uael-share-btn-' + uael_share_link_index[links] ).on( 'click', function() {
					popupWindow = window.open( uael_share_links[uael_share_link_index[links]],"popUpWindow","height=400,width=600,left="+left+",top="+top+",resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no, status=yes" );
				});
			});

			$scope.find( '.uael-share-btn-print' ).on( 'click', function() {
    			window.print();	
			});
		}
			
		$scope.find( '.uael-share-btn-buffer .uael-share-btn .uael-share-btn__icon i' ).each(function() {
			$( this ).removeClass( 'fa' ).addClass( "fab" ).addClass( "fa-buffer" );
		});	

		$scope.find( '.uael-share-btn-print .uael-share-btn .uael-share-btn__icon i' ).each(function() {			
			$( this ).removeClass( 'fab' ).addClass( "fa" ).addClass( "fa-print" );
		});

	};

	$( window ).on( 'elementor/frontend/init', function () {

		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-social-share.default', SocialShareHandler );
	});

} )( jQuery );
