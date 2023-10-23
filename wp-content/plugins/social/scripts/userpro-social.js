/* Custom JS starts here */
jQuery(document).ready(function() {

	// refresh activity
	jQuery(document).on('click', '.userpro-sc-refresh', function(e){
		var container = jQuery(this).parents('.userpro');
		var link = jQuery(this);
		var loader = container.find('img.userpro-sc-refresh-loader');
		var per_page = container.data('activity_per_page');
		var activity_user = container.data('activity_user');
		var offset = 0;
		if ( container.find('.userpro-sc-load').data('user_id') ) {
			var user_id = container.find('.userpro-sc-load').data('user_id');
		} else {
			var user_id = 0;
		}
		link.hide();
		loader.show();
		jQuery.ajax({
			url: userpro_ajax_url,
			data: "action=userpro_sc_refreshactivity&per_page="+per_page+"&offset="+offset+'&user_id='+user_id+'&activity_user='+activity_user,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				if (data.res != ''){
				container.find('.userpro-sc').remove();
				container.find('.userpro-body-nopad').prepend( data.res );
				link.show();
				loader.hide();
				} else {
				link.show();
				loader.hide();
				}
				userpro_chosen();
			}
		});
	});

	// activity more
	jQuery(document).on('click', '.userpro-sc-load a', function(e){
		var container = jQuery(this).parents('.userpro-body');
		var link = jQuery(this);
		var loader = jQuery(this).parents('.userpro-sc-load').find('img');
		var per_page = jQuery(this).data('activity_per_page');
		var activity_user = jQuery(this).data('activity_user');
		var offset = jQuery(this).parents('.userpro').find('.userpro-sc').length;
		if ( link.parents('.userpro-sc-load').data('user_id') ) {
			var user_id = link.parents('.userpro-sc-load').data('user_id');
		} else {
			var user_id = 0;
		}
		link.hide();
		loader.show();
		jQuery.ajax({
			url: userpro_ajax_url,
			data: "action=userpro_sc_loadactivity&per_page="+per_page+"&offset="+offset+'&user_id='+user_id+'&activity_user='+activity_user,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				if (data.res != ''){
				container.find('.userpro-sc:last').after( data.res );
				link.show();
				loader.hide();
				} else {
				link.show();
				loader.hide();
				}
				userpro_chosen();
			}
		});
	});

	// follow/unfollow user
	jQuery(document).on('click', '.userpro-follow', function(e){

		var button = jQuery(this);
		button.addClass('up-btn--loading');
		var action = button.data('follow-action');
		var to = button.data('follow-to');

		var data = {
			'action': 'userpro_followAction',
			'to': to,
			'follow_action': action,
			'security': up_social_ajax.security
		};

		jQuery.ajax({
			url: up_social_ajax.ajaxurl,
			data: data,
			dataType: 'JSON',
			type: 'POST',
			success:function(response){

				button.data('follow-action', response.data.action );
				button.find('p').text(response.data.text);
				button.find('i').removeClass().addClass(response.data.icon);

				jQuery('.up-followers span').text(response.data.count);


				// fbpost();
			},
			complete: function () {
				button.removeClass('up-btn--loading');
			}
		});

	});
});
function fbpost(){
		var message = jQuery("#fb-post-data").data('message');
		var name = jQuery("#fb-post-data").data('name');
		var caption = jQuery("#fb-post-data").data('caption');
		var desc = jQuery('#fb-post-data').data('description');
		var link = jQuery('#fb-post-data').data('link');
		var fbappid = jQuery('#fb-post-data').data('fbappid');
		window.fbAsyncInit = function() {
			FB.init({
			  appId      : fbappid,
	          xfbml      : true,
	          version    : 'v2.2'
	        });
	      FB.getLoginStatus(function(response) {
	   	  if (response.status === 'connected') {
	      	    var uid = response.authResponse.userID;
	      	    var accessToken = response.authResponse.accessToken;
	   	    	FB.api('/me/feed', 'post', { message:message ,caption:caption,link:link,name:name ,description:desc}, function (response) { });
      	  	} else if (response.status === 'not_authorized') {
      	  		// the user is logged in to Facebook,
      	  		// but has not authenticated your app
      	  	} else {
      	  		// the user isn't logged in to Facebook.
      	  }
      	 });
      };
      (function(d, s, id){
         var js, fjs = d.getElementsByTagName(s)[0];
         if (d.getElementById(id)) {return;}
         js = d.createElement(s); js.id = id;
         js.src = "//connect.facebook.net/en_US/sdk.js";
         fjs.parentNode.insertBefore(js, fjs);
       }(document, 'script', 'facebook-jssdk'));
}