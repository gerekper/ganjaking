"use strict";

function UniteMediaDialogUC(){
	
	var t = this;
	var g_lastVideoData = null;		//last fetched data
	var g_lastVideoCallback = null;   //last callback from video dialog return
	var g_desc_small_size = 200;	//small description size
	var g_searchTimeout = 5000;		//timeout that after that show error
	

	/**
	 * start select image dialog to change the image
	 */
	function onChangePreviewImageClick(){
		
		var dialogTitle = jQuery("#dv_link_change_image").data("dialogtitle");
			
		g_ucAdmin.openAddImageDialog(dialogTitle, function(arrData, imageID){
			if(arrData.length == 0)
				return(false);
			
			var urlImage = "";			
			if(typeof arrData == "string"){
				urlImage = arrData;				
			}
			else{
				urlImage = arrData[0].url;
				var imageID = arrData[0].id;
			}
						
			jQuery("#dv_input_video_preview").val(urlImage).data("imageid", imageID);
			
			updatePropsImages();
			
			requestThumbUrl();			
		}, false);
		
	}

	
	/**
	 * start select image dialog to change the image
	 */
	function onChangeThumbImageClick(){
		
		var dialogTitle = jQuery("#dv_link_change_thumb").data("dialogtitle");
			
		g_ucAdmin.openAddImageDialog(dialogTitle, function(arrData){
			if(arrData.length == 0)
				return(false);

			var urlImage = "";			
			if(typeof arrData == "string")
				urlImage = arrData;				
			else
				urlImage = arrData[0].url;
			
			jQuery("#dv_input_video_thumb").val(urlImage);
			
			updatePropsImages();
			
		}, false);
		
	}
	
	
	/**
	 * get object from all the fields
	 */
	function getObjFromFields(){
		var radioType = jQuery(".check-video-type:checked");
		var videoType = radioType.data("type");
		
		var data = {};
		data.type = videoType;
		
		//collect the props fields
		data.title = jQuery("#dv_input_video_title").val();
		data.description = jQuery("#dv_input_video_desc").val();
		data.urlImage = jQuery("#dv_input_video_preview").val();
		data.urlThumb = jQuery("#dv_input_video_thumb").val();
		
		//set video specific data
		switch(videoType){
			case "youtube":
				var videoID = jQuery("#dv_youtube_id").val();
				data.videoid = getYoutubeIDFromUrl(videoID);
			break;
			case "vimeo":
				var videoID = jQuery("#dv_vimeo_id").val();
				data.videoid = getVimeoIDFromUrl(videoID);				
			break;
			case "wistia":
				var videoID = jQuery("#dv_wistia_id").val();
				data.videoid = jQuery.trim(videoID);
			break;
			case "html5video":
				data.urlVideo_mp4 = jQuery("#dv_html5_url_mp4").val();
				data.urlVideo_webm = jQuery("#dv_html5_url_webm").val();
				data.urlVideo_ogv = jQuery("#dv_html5_url_ogv").val();
			break;
		}
		
		return(data);
	}
	
	
	/**
	 * validate that field not empty by value
	 */
	function validateFieldNotEmpty(value, fieldName){
		
		if(value == "")
			throw new Error("Please fill <b>" + fieldName + "</b> field");
		
	}
	
	
	/**
	 * validate dialog before add
	 */
	function validateBeforeAdd(data){
		
		try{
			validateFieldNotEmpty(data.title, "title");
						
			switch(data.type){
				case "youtube":
					validateFieldNotEmpty(data.videoid, "Youtube ID");
					
				break;
				case "vimeo":
					validateFieldNotEmpty(data.videoid, "Vimeo ID");
				
				break;
				case "html5video":
					
					validateFieldNotEmpty(data.urlVideo_mp4, "Video MP4 Url");
					validateFieldNotEmpty(data.urlVideo_webm, "Video WEBM Url");
					validateFieldNotEmpty(data.urlVideo_ogv, "Video OGV Url");
					
				break;
			}
			
			
		}catch(objError){
			
			jQuery("#dv_button_video_add").blur();
			jQuery("#dv_error_message_bottom").show().html(objError.message);
			
			setTimeout('jQuery("#dv_error_message_bottom").hide()', 5000);
			
			return(false);
		}
		
		return(true);
	}
	
	
	/**
	 * on add video button click
	 */
	function onAddVideoClick(){
		
		var objDialog = jQuery("#dialog_video");
		var mode = objDialog.data("mode");
		
		var data = getObjFromFields();
		
		if(validateBeforeAdd(data) == false)
			return(false);
		
		if(mode == "update"){
			data.itemID = objDialog.data("itemID");
		}
		
		if(typeof g_lastVideoCallback == "function")
			g_lastVideoCallback(data);
		
		objDialog.dialog("close");		
	}

	
	/**
	 * on put example link, put the example text to target id
	 */
	function onPutExampleClick(){
		var text = jQuery(this).data("example");
		var targetID = jQuery(this).data("targetid");
		
		jQuery("#" + targetID).val(text);
	}
	
		
	/**
	 * init dialog events
	 */
	function initEvents(){
		
		jQuery(".check-video-type").on("click",function(){
			var mediaType = jQuery(this).data("type");
			changeMediaType(mediaType);
		});
		
		jQuery("#dv_youtube_id").keyup(function(event){
			if(event.keyCode == 13)
				searchYoutube();
		});
		
		jQuery("#dv_vimeo_id").keyup(function(event){
			if(event.keyCode == 13)
				searchVimeo();
		});		
		
		jQuery("#dv_wistia_id").keyup(function(event){
			if(event.keyCode == 13)
				searchWistia();
		});		
		
		//set search actions
		jQuery("#dv_button_youtube_search").on("click",searchYoutube);		
		jQuery("#dv_button_vimeo_search").on("click",searchVimeo);
		jQuery("#dv_button_wistia_search").on("click",searchWistia);
		
		jQuery("#dv_link_change_image").on("click",onChangePreviewImageClick);
		
		jQuery("#dv_link_change_thumb").on("click",onChangeThumbImageClick);

		jQuery("#dv_input_video_preview, #dv_input_video_thumb").change(updatePropsImages);

		jQuery(".dv_put_example").on("click",onPutExampleClick);
		
		//add the selected video to the callback function
		jQuery("#dv_button_video_add").on("click",onAddVideoClick);
		
	}
	
		
	
	/**
	 * show error message on the dialog
	 */
	this.videoDialogOnError = function(){
				
		//if ok, don't do nothing
		if(g_lastVideoData)
			return(false);
		
		var message = jQuery("#dv_error_message").data("notfound");
		
		showSearchErrorMessage(message);
	};
	
	
	
	
	/**
	 * init video dialog buttons
	 */
	function initVideoDialog(){
		
		//set youtube radio checked:
		jQuery("#video_radio_youtube").prop("checked",true);
		
						
		initEvents();
		
	}; //end initVideoDialog

	
	/**
	 * change the media type
	 */
	function changeMediaType(mediaType){
				
		jQuery(".video-select-block").hide();
		switch(mediaType){
			case "youtube":					
				jQuery("#video_block_youtube").show();
				jQuery("#dv_youtube_id").focus();
			break;
			case "vimeo":				
				jQuery("#video_block_vimeo").show();
				jQuery("#dv_vimeo_id").focus();
			break;
			case "html5video":
				showPropsFields();
				jQuery("#video_block_html5").show();
			break;
			case "wistia":
				jQuery("#video_block_wistia").show();
				jQuery("#dv_wistia_id").focus();
			break;
		}
		
		//check for hiduing props fields (on add mode if not searched yet)
		var dialogMode = jQuery("#dialog_video").data("mode");

		if(mediaType != "html5video" && g_lastVideoData == null && dialogMode == "add")
			hidePropsFields();
		
	}
	
	
	/**
	 * clear dialog fields
	 */
	function clearInitFields(){
				
		jQuery("#dv_youtube_id").val("");
		jQuery("#dv_vimeo_id").val("");	
		jQuery("#dv_wistia_id").val("");	
		
		jQuery("#dialog_video .video_props input[type='text']").val("");
		jQuery("#dv_input_video_desc").val("");
		
		jQuery("#dv_html5_url_mp4, #dv_html5_url_webm, #dv_html5_url_ogv").val("");
		
		updatePropsImages();
	}
	
	
	/**
	 * hide video content fields
	 */
	function hidePropsFields(){
		jQuery("#dialog_video .video_props").hide();
	}
	
	/**
	 * show video properties fields
	 */
	function showPropsFields(){
		jQuery("#dialog_video .video_props").show();		
	}
	
	
	/**
	 * fill video properties from the callback data
	 */
	function fillVideoProps_fromCallback(data){
		
		var urlThumb = data.thumb_medium.url;
		var urlPreview = data.preview_image.url;
				
		switch(data.type){
			case "youtube":
				jQuery("#dv_youtube_id").val(data.id);				
			break;
			case "vimeo":
				jQuery("#dv_vimeo_id").val(data.id);				
			break;
		}
		
		jQuery("#dv_input_video_title").val(data.title);
		jQuery("#dv_input_video_desc").val(data.desc_small);
		jQuery("#dv_input_video_preview").val(urlPreview);
		jQuery("#dv_input_video_thumb").val(urlThumb);	
		
		updatePropsImages();
	}
	
	
	/**
	 * fill video properties from the item data
	 */
	function fillVideoProps_fromItemData(data){
		
		switch(data.type){
			case "youtube":
				jQuery("#dv_youtube_id").val(data.videoid);
				jQuery("#video_radio_youtube").trigger("click");
			break;
			case "vimeo":
				jQuery("#dv_vimeo_id").val(data.videoid);
				jQuery("#video_radio_vimeo").trigger("click");
			break;
			case "html5video":
				jQuery("#video_radio_html5").trigger("click");
				jQuery("#dv_html5_url_mp4").val(data.video_mp4);
				jQuery("#dv_html5_url_webm").val(data.video_webm);
				jQuery("#dv_html5_url_ogv").val(data.video_ogv);
			break;
		}
		
		jQuery("#dv_input_video_title").val(data.title);
		jQuery("#dv_input_video_desc").val(data.description);
		jQuery("#dv_input_video_preview").val(data.url_image);
		jQuery("#dv_input_video_thumb").val(data.url_thumb);	
		
		updatePropsImages();
	}
	
	
	
	/**
	 * take the url's from the inputs and update image sources of thumb and big image
	 */
	function updatePropsImages(){
				
		var urlImage = jQuery("#dv_input_video_preview").val();
		var urlThumb = jQuery("#dv_input_video_thumb").val();
		
		urlImage = jQuery.trim(urlImage);
		urlThumb = jQuery.trim(urlThumb);
		
		jQuery("#dv_preview_image").css("background-image","url('"+urlImage+"')");
		jQuery("#dv_video_thumb").css("background-image","url('"+urlThumb+"')");
		
	}

	
	/**
	 * request thumb url from image url
	 */
	function requestThumbUrl(){
		
		var urlImage = jQuery("#dv_input_video_preview").val();	
		urlImage = jQuery.trim(urlImage);
		var imageID = jQuery("#dv_input_video_preview").data("imageid");
		
		jQuery("#dv_loader_thumb").show();	
		
		//ajax request for getting the thumb
		g_ucAdmin.requestThumbUrl(urlImage, imageID, function(urlThumb){
			jQuery("#dv_loader_thumb").hide();
			if(!urlThumb)
				return(false);
			
			var urlThumb = jQuery("#dv_input_video_thumb").val(urlThumb);
			
			updatePropsImages();
			
		});
		
	}
	
	
	/**
	 * treat dialog update mode, request item data,
	 * then fill the fields.
	 */
	function treatUpdateMode(objData){
		
		var itemID = objData.itemID;
		
		objData.requestFunction(itemID, function(response){
						
			fillVideoProps_fromItemData(response);
			
			jQuery("#video_dialog_loader").hide();
			jQuery("#video_dialog_inner").show();			
			showPropsFields();
		});
		
	}
	
	
	/**
	 * set dialog mode - add / update
	 */
	function setMode(mode, objData){
		
		var buttonVideoAdd = jQuery("#dv_button_video_add");
		var addText = buttonVideoAdd.data("textadd");
		var updateText = buttonVideoAdd.data("textupdate");
		var objDialog = jQuery("#dialog_video");
		var radioType = jQuery(".check-video-type:checked");
		var mediaType = radioType.data("type");
		
		clearInitFields();
		
		objDialog.data("mode", mode);
		
		jQuery("#dv_error_message_bottom").hide();
		
		switch(mode){
			case "add":
				jQuery("#video_dialog_loader").hide();
				jQuery("#video_dialog_inner").show();
				
				buttonVideoAdd.val(addText);
				hidePropsFields();
				changeMediaType(mediaType);
			break;
			case "update":
				jQuery("#video_dialog_loader").show();
				jQuery("#video_dialog_inner").hide();
				objDialog.data("itemID", objData.itemID);
				
				buttonVideoAdd.val(updateText);
				treatUpdateMode(objData);
			break;
			default:
				throw new Error("wrong mode: " + mode);
			break;
		}		
	}
	

	function __________Search_Related__________(){}
	
	/**
	 * pass youtube id or youtube url, and get the id
	 */
	function getYoutubeIDFromUrl(url){
		url = jQuery.trim(url);
		
		var video_id = url.split('v=')[1];
		if(video_id){
			var ampersandPosition = video_id.indexOf('&');
			if(ampersandPosition != -1) {
			  video_id = video_id.substring(0, ampersandPosition);
			}
		}else{
			video_id = url;
		}
		
		return(video_id);
	}

	
	/**
	 * get vimeo id from url
	 */
	function getVimeoIDFromUrl(url){
		url = jQuery.trim(url);
		
		var video_id = url.replace(/[^0-9]+/g, '');
		video_id = jQuery.trim(video_id);
		
		return(video_id);
	}
	
	/**
	 * get data from youtube callback object
	 */
	function getDataFromVimeo(obj){
		
		obj = obj[0];
		
		var data = {};
		data.video_type = "vimeo";
		data.id = obj.id;
		data.id = jQuery.trim(data.id);
		data.title = obj.title;
		data.link = obj.url;
		data.author = obj.user_name;		
		data.description = obj.description;
		data.desc_small = data.description;
		
		if(data.description.length > g_desc_small_size)
			data.desc_small = data.description.slice(0, g_desc_small_size)+"...";
		
		data.thumb_large = {url:obj.thumbnail_large,width:640,height:360};
		data.thumb_medium = {url:obj.thumbnail_medium,width:200,height:150};
		data.thumb_small = {url:obj.thumbnail_small,width:100,height:75};
		
		data.preview_image = {url:obj.thumbnail_large,width:640,height:360};
		
		//trace(data);
		
		return(data);
	}
	
	/**
	 * get data from youtube callback object
	 */
	function getDataFromYoutube(obj){
		
		var data = {};
				
		var entry = obj.entry;
		data.id = entry.media$group.yt$videoid.$t;
		
		data.video_type = "youtube";
		data.title = entry.title.$t;
		data.author = entry.author[0].name.$t;
		data.link = entry.link[0].href;
		data.description = entry.media$group.media$description.$t;
		data.desc_small = data.description;
		
		if(data.description.length > g_desc_small_size)
			data.desc_small = data.description.slice(0,g_desc_small_size)+"...";
		
		var thumbnails = entry.media$group.media$thumbnail;
		
		data.thumb_small = {url:thumbnails[0].url,width:thumbnails[0].width,height:thumbnails[0].height};
		data.thumb_medium = {url:thumbnails[1].url,width:thumbnails[1].width,height:thumbnails[1].height};
		data.thumb_big = {url:thumbnails[2].url,width:thumbnails[2].width,height:thumbnails[2].height};
		
		data.preview_image = {url:thumbnails[3].url,width:thumbnails[3].width,height:thumbnails[3].height};
				
		return(data);
	}
	
	
	/**
	 * get data from wistia callback object
	 */
	function getDataFromWistia(obj){
				
		var data = {};
		
		data.video_type = "wistia";
		data.title = obj.title;
		data.description = "";
		data.desc_small = "";
		
		var previewUrl = obj.thumbnail_url;
		var previewWidth = obj.thumbnail_width;
		var previewHeight = obj.thumbnail_height;
		
		//make thumb string
		var ratio = previewHeight / previewWidth;
		var thumbWidth = 320;
		var thumbHeight = Math.round(thumbWidth * ratio);
		
		var strReplace = previewWidth + "x" + previewHeight;
		var strReplaceTo = thumbWidth + "x" + thumbHeight;
		
		var thumbUrl = previewUrl.replace(strReplace, strReplaceTo);
				
		data.preview_image = {url:previewUrl, width:previewWidth, height:previewHeight};
		
		data.thumb_medium = {url:thumbUrl, width:thumbWidth, height:thumbHeight};
		
		return(data);
	}
	
	
	/**
	 * search for youtube video
	 */
	function searchYoutube(){
		
		g_lastVideoData = null;
		
		//prepare fields
		jQuery("#dv_youtube_loader").show();
		hidePropsFields();
		jQuery("#dv_error_message").hide();
		
		//prepare data
		var youtubeID = jQuery("#dv_youtube_id").val();	
		youtubeID = getYoutubeIDFromUrl(youtubeID);
				
		if(!youtubeID){
			showSearchErrorMessage("Empty Youtube ID");
			return(false);
		}
		
		//call API
		var urlAPI = "https://gdata.youtube.com/feeds/api/videos/"+youtubeID+"?v=2&alt=json-in-script&callback=g_ugMediaDialog.onYoutubeCallback";
		
		jQuery.getScript(urlAPI);
		
		//handle url don't pass:
		setTimeout("g_ugMediaDialog.videoDialogOnError()", g_searchTimeout);
	}
	
	
	/**
	 * search vimeo video
	 */
	function searchVimeo(){
		
		g_lastVideoData = null;
		
		//prepare fields
		jQuery("#dv_vimeo_loader").show();
		hidePropsFields();
		jQuery("#dv_error_message").hide();		
		
		var vimeoID = jQuery("#dv_vimeo_id").val();
		vimeoID = jQuery.trim(vimeoID);
		vimeoID = getVimeoIDFromUrl(vimeoID);
		
		var urlAPI = 'https://www.vimeo.com/api/v2/video/' + vimeoID + '.json?callback=g_ugMediaDialog.onVimeoCallback'; 
		
		jQuery.getScript(urlAPI);
		
		//handle url don't pass:
		setTimeout("g_ugMediaDialog.videoDialogOnError()", g_searchTimeout);
	}
	
	
	/**
	 * search wistia
	 */
	function searchWistia(){
		
		var videoID = jQuery("#dv_wistia_id").val();
		videoID = jQuery.trim(videoID);
		
		var url = "https://fast.wistia.net/oembed?url=http%3A//home.wistia.com/medias/"+videoID;

		//prepare fields
		jQuery("#dv_wistia_loader").show();
		hidePropsFields();
		jQuery("#dv_error_message").hide();		
		
		jQuery.get( url ,"", function(response){
			jQuery("#dv_wistia_loader").hide();
			jQuery("#dv_button_wistia_search").blur();
			
			var data = getDataFromWistia(response);
			
			fillVideoProps_fromCallback(data);
			showPropsFields();
			
			//refresh couple of times to show thumb
			setTimeout(updatePropsImages, 5000);
			setTimeout(updatePropsImages, 10000);
			setTimeout(updatePropsImages, 15000);
		});
		
	}
	
	
	/**
	 * show error message on search area
	 */
	function showSearchErrorMessage(message){
		
		jQuery("#dv_youtube_loader").hide();
		jQuery("#dv_vimeo_loader").hide();
		jQuery("#dv_wistia_loader").hide();
		
		hidePropsFields();
		
		jQuery("#dv_error_message").show().html(message);
		
		setTimeout('jQuery("#dv_error_message").hide()', 7000);
	}
	
	
	/**
	 * open dialog for youtube or vimeo import , add / update
	 */
	this.openVideoDialog = function(callback, itemData){
		
		g_lastVideoCallback = callback;
		
		var dialogVideo = jQuery("#dialog_video");
		
		//set buttons:
		var buttons = {
			"Close":function(){
				dialogVideo.dialog("close");
			}
		};

		var mode = itemData ? "update":"add";
		
		
		setMode(mode, itemData);
					
		var dialogOptions = {
			dialogClass:"unite-ui",
			buttons:buttons,
			minWidth:900,
			minHeight:530,
			modal:true
		};
		
		//set dilog title - custom or default
		if(mode == "update" && typeof itemData.dialogTitle != "undefined")
			dialogOptions.title = itemData.dialogTitle;
		else
			dialogOptions.title = dialogVideo.data("title");
		
		//open the dialog
		dialogVideo.dialog(dialogOptions);
	};

	
	/**
	 * youtube callback script, set and store youtube data, and add it to dialog
	 */
	this.onYoutubeCallback = function(obj){
		jQuery("#dv_youtube_loader").hide();
		
		try{
		
			//prepare data
			var data = getDataFromYoutube(obj);
					
			//store last video data
			g_lastVideoData = data;
			
			//show fields:
			fillVideoProps_fromCallback(data);
			showPropsFields();
			
		}catch(objError){
			
			g_lastVideoData = "error";
			showSearchErrorMessage(objError.message);
		}
		
	};
	
	
	/**
	 * vimeo callback script, set and store vimeo data, and add it to dialog
	 */	
	this.onVimeoCallback = function(obj){
		jQuery("#dv_vimeo_loader").hide();
		
		//prepare data
		var data = getDataFromVimeo(obj);
		
		//store last video data
		g_lastVideoData = data;
		
		//show fields:
		fillVideoProps_fromCallback(data);
		showPropsFields();		
		
	};
	
	
	/**
	 * init the variables
	 */
	this.init = function(){
		
		initVideoDialog();
	};
	
}	//class end

g_ugMediaDialog = new UniteMediaDialogUC();
