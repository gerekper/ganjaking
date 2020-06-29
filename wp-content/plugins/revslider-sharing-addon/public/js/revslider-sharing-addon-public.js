function initSocialSharing(id){
	//find all sliders on page with ID
	jQuery('div[id^="rev_slider_'+id+'"]').each(function(){
		var $slider=jQuery(this);
		if($slider.attr("id").lastIndexOf("wrapper")<0){
			//bind change to change ID
			source = jQuery('#'+$slider.attr("id")+"_wrapper").data("source");
			jQuery.globalEval('revapi'+id+'.bind("revolution.slide.onchange", function(e, data) { change_static(data,"#'+$slider.attr("id")+'_wrapper","'+source+'"); });');	
		}
	});
}

function change_static(data,slider,source){
	//run through static layer links
	jQuery(slider).find("a.tp-static-layer").each(function(){
		//check if social links are on static
		$link = jQuery(this);
		if($link.attr("href").lastIndexOf("tpurl")>0){
			//replace post id
			if( source != "gallery"){
				var tpurl = data.currentslide.data("index").replace("rs-","");
				$link.attr("href", $link.attr("href").replace(/tpurl=.*?&/, "tpurl="+tpurl+"&"));  
			}
			//replace media
			var media = data.currentslide.find(".slotholder").find("div").css("background-image").replace(")","").replace("url(","").replace(/\"/g, ""); 
			$link.attr("href", $link.attr("href").replace(/media=.*?,/, "media="+media+","));
		}
	});
}

function share_action(a,data){		

	context = data.layer.context.className;

	data_event_share = data.event.share;

	if( jQuery.inArray( data.event.action, [ "share_twitter", "share_facebook", "share_linkedin", "share_pinterest", "share_googleplus" ] ) != -1 ){
		
		var winHeight = 200;
        var winWidth = 450;

		switch(data.event.action){
        	case 'share_facebook':
        		winWidth = revslider_sharing_addon.revslider_sharing_addon_sizes['revslider-sharing-addon-facebook-width'];
        		winHeight = revslider_sharing_addon.revslider_sharing_addon_sizes['revslider-sharing-addon-facebook-height'];
        		break;
        	case 'share_twitter':
        		winWidth = revslider_sharing_addon.revslider_sharing_addon_sizes['revslider-sharing-addon-twitter-width'];
        		winHeight = revslider_sharing_addon.revslider_sharing_addon_sizes['revslider-sharing-addon-twitter-height'];
        		break;
        	case 'share_linkedin':
        		winWidth = revslider_sharing_addon.revslider_sharing_addon_sizes['revslider-sharing-addon-linkedin-width'];
        		winHeight = revslider_sharing_addon.revslider_sharing_addon_sizes['revslider-sharing-addon-linkedin-height'];
        		break;
        	case 'share_pinterest':
        		winWidth = revslider_sharing_addon.revslider_sharing_addon_sizes['revslider-sharing-addon-pinterest-width'];
        		winHeight = revslider_sharing_addon.revslider_sharing_addon_sizes['revslider-sharing-addon-pinterest-height'];
        		break;
        	case 'share_googleplus':
        		winWidth = revslider_sharing_addon.revslider_sharing_addon_sizes['revslider-sharing-addon-googleplus-width'];
        		winHeight = revslider_sharing_addon.revslider_sharing_addon_sizes['revslider-sharing-addon-googleplus-height'];
        		break;
        }
        var winTop = (screen.height / 2) - (winHeight / 2);
        var winLeft = (screen.width / 2) - (winWidth / 2);
        
        current_revslider = jQuery('#'+data.layer.context.id).closest(".rev_slider")
        current_revslider_id = current_revslider.attr("id");
        current_revslider_source = current_revslider.closest(".rev_slider_wrapper").data("source");

        if(current_revslider_source!="gallery"){
        	current_revapi = current_revslider_id.split("_");
        	current_revslider_send_id = current_revapi[2]; 
	        current_revapi = "revapi"+current_revapi[2];
	        current_slide_id = eval(current_revapi+".revcurrentslide()");
	        jQuery.ajax({
					url : revslider_sharing_addon.ajax_url,
					type : 'post',
					data : {
						action : 'get_post_info',
						revslider_sharing_slider_id: current_revslider_send_id,
						revslider_sharing_addon_post_id: jQuery("#"+current_revslider_id).find("ul:first").find("li:nth-child("+current_slide_id+")").data("index").replace("rs-",""),
						revslider_sharing_addon_service: data.event.action,
						revslider_sharing_addon_link: data.event.share,
						revslider_sharing_addon_source: current_revslider_source 
					},
					success : function( response ) {
						if(response){
							data.event.share = response;
							if(data.event.action=="share_pinterest"){
								background_img = jQuery("#"+current_revslider_id).find("ul:first").find("li:nth-child("+current_slide_id+")").find(".slotholder").find("div").css('background-image').replace(")","").replace("url(","").replace(/\"/g, "");
		        				data.event.share = data.event.share.replace("media=&", "media="+background_img+"&");
							}
							window.open( data.event.share , data.event.action, 'top=' + winTop + ',left=' + winLeft + ',location=no,toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight);	            	
							
							
							
							//jQuery(".md-content").load(data.event.share);

							data.event.share = data_event_share;
						}
					},
					error : function ( response ){
						console.log("request failed");
					}
				}); // End Ajax
        }
        else{
        	if(context.lastIndexOf("tp-static-layer")){
	        	if(data.event.action=="share_pinterest"){
		        	current_revslider_id = jQuery('#'+data.layer.context.id).closest(".rev_slider").attr("id");
			        current_revapi = current_revslider_id.split("_");
			        current_revapi = "revapi"+current_revapi[2];
			        current_slide_id = eval(current_revapi+".revcurrentslide()");
	        		background_img = jQuery("#"+current_revslider_id).find("ul:first").find("li:nth-child("+current_slide_id+")").find(".slotholder").find("div").css('background-image').replace(")","").replace("url(","").replace(/\"/g, "");
		        	data.event.share = data.event.share.replace("media=&", "media="+background_img+"&");
		        }
	        }
	        window.open( data.event.share , data.event.action, 'top=' + winTop + ',left=' + winLeft + ',location=no,toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight);	        	
			data.event.share = data_event_share;
        }

		
	} else {
		if(data.event.action == "share_email"){
			top.location.href=data.event.share;
		}
	}
}