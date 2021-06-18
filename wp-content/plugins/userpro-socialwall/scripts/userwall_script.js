jQuery(function(){
	jQuery('.socialwall-datepicker').datepicker({

		dateFormat: 'yy-mm-dd',
		changeMonth: true,
		changeYear: true,
		showOtherMonths: true,
		selectOtherMonths: true,
		dayNamesMin: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
		yearRange: 'c-65:c+0'

 });
 });
jQuery(function(){
	jQuery('#post-loader').hide();
	jQuery('#loademore-loader').hide();
});
jQuery( document ).on('click','.socialwall-load-more span:not(.loaded)',function() { //Added By Yogesh  on 3 Dec 2014
		var url=jQuery('.socialwall-load-more').data('url');

		var posttype=jQuery('.socialwall-load-more').data('post-type');
		var count=jQuery('.socialwall-load-more').data('max-pages');
		var user_role=jQuery('.socialwall-load-more').data('user-role');
        var social_userid=jQuery('.socialwall-load-more').data('userid');



          total = +Number(count) + +total;


		socialwall_load_more(total,user_role,posttype ,url,count,social_userid);
	});

function sw_delete_post_by_date(formdate,todate)
{
	var formdate=jQuery('#'+formdate).val();
	var todate=jQuery('#'+todate).val();

   str = 'action=countpost&formdate='+formdate+'&todate='+todate;
	jQuery.ajax({
	url: ajaxurl,
	data: str,
	type: 'POST',
	success:function(data){


	str = 'action=socialwall_delete_post_by_date&formdate='+formdate+'&todate='+todate;
	var retVal = confirm(""+data+" post(s) and all comment(s) for those posts will be deleted. This action is irreversible. Please click on 'Delete' if you are sure you want to proceed.");
   	if( retVal == true ){
	jQuery.ajax({
	url: ajaxurl,
	data: str,
	type: 'POST',
	success:function(data){
		if(data!='notfound')
		{
		alert(""+data+" post(s) and all comment(s) for those posts has been deleted Successfully.");
		}
		else
		{
			alert("No Results Found");
		}
	},
	error:function(data){

		alert(data.error);
	}
		});
   	}
		},
	error:function(data){

		alert(data.error);
	}
		});


}


function userwall_ignore_post(post_id)
{

	str = 'action=socialwall_ignore_post&post_id='+post_id;
	var retVal = confirm("Are you sure you want to ignore the reported post?");
   	if( retVal == true ){
	jQuery.ajax({
	url: ajaxurl,
	data: str,
	type: 'POST',
	success:function(data){

		jQuery('#'+post_id).hide();
	},
	error:function(data){alert(data);

		alert(data.error);
	}
		});
   	}
}

function userwall_report_post(post_id,userid)
{

		str = 'action=socialwall_report_post&post_id='+post_id+'&userid='+userid;
	var retVal = confirm("Are you sure you want to report a post to Admin?");
   	if( retVal == true ){


		jQuery.ajax({
		url: ajaxurl,
		data: str,
		type: 'POST',
		success:function(data){



		},
		error:function(data){alert(data);

			alert(data.error);
		}
	});}
}

function userwall_postdislikecount_post(post_id,userid,totaldislikes,totalliks,cdate) {

	str = 'action=socialwall_dislikecount_post&post_id='+post_id+'&userid='+userid;

		jQuery.ajax({
		url: ajaxurl,
		data: str,
		type: 'POST',
		success:function(data){
                    /*
                     * Commnted by Samir - to avoid change in time.
                     * var m_names = new Array("Jan", "Feb", "Mar",
                                            "Apr", "May", "Jun", "Jul", "Aug", "Sep",
                                            "Oct", "Nov", "Dec");
                    var d = new Date(cdate*1000);
                    var curr_date = d.getDate();
                    var curr_month = d.getMonth();
                    var curr_year = d.getFullYear();
                    var curr_hrs = d.getHours();
                    var curr_min = d.getMinutes();
                    var date = curr_date + "-" + m_names[curr_month] + "-" + curr_year+' ' +curr_hrs +':'+curr_min;*/
					document.getElementById('userwall_postlikecount_post'+post_id).style.display="none";
                    jQuery('#countlike'+post_id).html(totaldislikes+1);

                    var total=totaldislikes+1;
                    jQuery('#userwall_postlikecount_post'+post_id).show();

                    if(data === 'yes'){
                    	var post_date = jQuery('#userwall_postlikecount_post'+post_id+' .post-date').text();
                        jQuery('#userwall_postlikecount_post'+post_id).html('<i style="color:black;opacity: 0.5;" class="socialwall_postlikecount_post userpro-icon-thumbsup"></i><i class="socialwall_postlikecount_post ">'+totalliks+'&nbsp</i><i style="color:black;opacity: 0.5;" class="socialwall_postlikecount_post userpro-icon-thumbsdown"></i><i class="socialwall_postlikecount_post ">'+total+'</i><i onclick="userwall_report_post('+post_id+' ,'+userid+')" class="reportpost fa fa-exclamation-circle" style="margin-left: 12px;color: red;"></i><span class="commented-date" style="color: #90949c;font-family: Arial;font-size:12px;margin-left: 1%;">'+post_date+'</span>');
                    }
                    else if(data === 'No'){
                    	var post_date = jQuery('#userwall_postlikecount_post'+post_id+' .post-date').text();
                        jQuery('#userwall_postlikecount_post'+post_id).html('<i style="color:black;opacity: 0.5;" class="socialwall_postlikecount_post userpro-icon-thumbsup"></i><i class="socialwall_postlikecount_post ">'+totalliks+'&nbsp</i><i style="color:black;opacity: 0.5;" class="socialwall_postlikecount_post userpro-icon-thumbsdown"></i><i class="socialwall_postlikecount_post ">'+total+'</i><i style="color:black;opacity: 0.5;cursor:default;margin-left: 12px;" class="fa fa-exclamation-circle"></i><span class="commented-date" style="color: #90949c;font-family: Arial;font-size:12px;margin-left: 1%;">'+post_date+'</span>');
                    }
		},
		error:function(data){alert(data);

			alert(data.error);
		}
	});
        return false;
}

function userwall_postlikecount_post(post_id,userid,totallikes,totaldislike,cdate) {
		/*var m_names = new Array("Jan", "Feb", "Mar",
                                "Apr", "May", "Jun", "Jul", "Aug", "Sep",
                                "Oct", "Nov", "Dec");*/

       /*
        * Commnted by Samir - to avoid change in time.
        * var d = new Date(cdate*1000);
        var curr_date = d.getDate();
        var curr_month = d.getMonth();
        var curr_year = d.getFullYear();
        var curr_hrs = d.getHours();
        var curr_min = d.getMinutes();
        var date = curr_date + "-" + m_names[curr_month] + "-" + curr_year+' ' +curr_hrs +':'+curr_min;*/
        str = 'action=socialwall_count_posts_like&post_id='+post_id+'&userid='+userid;

        jQuery.ajax({
		url: ajaxurl,
		data: str,
		type: 'POST',
		success:function(data){
			document.getElementById('userwall_postlikecount_post'+post_id).style.display="none";
			jQuery('#userwall_postlikecount_post'+post_id).show();
			var total=totallikes+1;

            if(data === 'yes'){
            	var post_date = jQuery('#userwall_postlikecount_post'+post_id+' .post-date').text();
            	jQuery('#userwall_postlikecount_post'+post_id).html('<i style="color:black;opacity: 0.5;" class="socialwall_postlikecount_post userpro-icon-thumbsup"></i><i class="socialwall_postlikecount_post ">'+total+'&nbsp</i><i style="color:black;opacity: 0.5;" class="socialwall_postlikecount_post userpro-icon-thumbsdown"></i><i class="socialwall_postlikecount_post ">'+totaldislike+'</i><i onclick="userwall_report_post('+post_id+' ,'+userid+')" class="reportpost fa fa-exclamation-circle" style="margin-left: 12px;color: red;"></i><span class="commented-date" style="color: #90949c;font-family: Arial;font-size:12px;margin-left: 1%;">'+post_date+'</span>');
            }
            else if(data === 'No'){
            	var post_date = jQuery('#userwall_postlikecount_post'+post_id+' .post-date').text();
            	jQuery('#userwall_postlikecount_post'+post_id).html('<i style="color:black;opacity: 0.5;" class="socialwall_postlikecount_post userpro-icon-thumbsup"></i><i class="socialwall_postlikecount_post ">'+total+'&nbsp</i><i style="color:black;opacity: 0.5;" class="socialwall_postlikecount_post userpro-icon-thumbsdown"></i><i class="socialwall_postlikecount_post ">'+totaldislike+'</i><i style="color:black;opacity: 0.5;cursor:default;margin-left: 12px;" class="fa fa-exclamation-circle"></i><span class="commented-date" style="color: #90949c;font-family: Arial;font-size:12px;margin-left: 1%;">'+post_date+'</span>');
            }

		},
		error:function(data){alert(data);

			alert(data.error);
		}

	});
        return false;
}


function userwall_commentlikedislikecount_comment(meta_id,userid,totallikes,totaldislikes,action,postid,commentdate)
{
    /*
     * Commnted by Samir - to avoid change in time.
     * var m_names = new Array("Jan", "Feb", "Mar",
                                "Apr", "May", "Jun", "Jul", "Aug", "Sep",
                                "Oct", "Nov", "Dec");
    var d = new Date(commentdate*1000);
    var curr_date = d.getDate();
    var curr_month = d.getMonth();
    var curr_year = d.getFullYear();
    var curr_hrs = d.getHours();
    var curr_min = d.getMinutes();
    var comment_date = curr_date + "-" + m_names[curr_month] + "-" + curr_year +" "+ curr_hrs+":"+curr_min;*/

	str = 'action=socialwall_comment_like_dislike&meta_id='+meta_id+'&userid='+userid+'&request='+action+'&postid='+postid;
	jQuery.ajax({
		url: ajaxurl,
		data: str,
		type: 'POST',
		dataType:'json',
		success:function(data){
			document.getElementById('userwall_commentlikecount_comment'+meta_id).style.display="none";

			jQuery('#userwall_commentlikecount_comment'+meta_id).show();

            if( action == 'like' )
            {
            	var comment_date = jQuery('#userwall_commentlikecount_comment'+meta_id+' .commented-date').text();
            	totallikes = totallikes + 1;
            }
            else if( action == 'dislike' )
            {
            	var comment_date = jQuery('#userwall_commentlikecount_comment'+meta_id+' .commented-date').text();
            	totaldislikes = totaldislikes + 1;
            }
			jQuery('#userwall_commentlikecount_comment'+meta_id).html('<i style="color:black;opacity: 0.5;" class="socialwall_postlikecount_post userpro-icon-thumbsup"></i><i class="socialwall_postlikecount_post ">'+totallikes+'&nbsp</i><i style="color:black;opacity: 0.5;" class="socialwall_postlikecount_post userpro-icon-thumbsdown"></i><i class="socialwall_postlikecount_post ">'+totaldislikes+'</i><span class="commented-date" style="color: #90949c;font-family: Arial;font-size:12px;">'+comment_date+'</span>');
		},
		error:function(data){alert(data);

			alert(data.error);
		}
	});


}
function socialwall_load_more( total,user_role,posttype,url,count,social_userid) {

	jQuery('#loademore-loader').show();
	str = 'action=socialwall_load_posts&count='+total+'&role='+user_role+'&posttype='+posttype+'&url='+url+'&social_userid='+social_userid;

	jQuery.ajax({
		url: ajaxurl,
		data: str,
		type: 'POST',
		success:function(data){

			jQuery('#loademore-loader').hide();
			if(data!='hide')
			{
				jQuery('#userwalldata').append(stripslashes(data));

				var n = jQuery(".a2a_default_style").length;
				var load=(count+n);

				for (i = 0; i < load ; i++) {
					a2a.init();
				}
			}
			else
			{
				document.getElementById('socialwall-load-more').style.display="none";

			}


		},
		error:function(data){

			alert(data.error);
		}
	});
}


function htmlDecode(value){
  return jQuery('<div/>').html(value).text();
}

function user_post_data(post_name,user_id,posttype,visibility){
	var check;
	if(isNaN(posttype)){
		check='posttype';
	}
	else{
		check='puser_id';
	}

	jQuery('#post-loader').show();
	var post_content=jQuery('#userpost').html();
	var find = '<';
	var re = new RegExp(find, 'g');
	post_content = post_content.replace(re, '&lt;');
	var find = '>';
	var re = new RegExp(find, 'g');
	post_content = post_content.replace(re, '&gt;');

	if(post_content!="")
	{
		var str = 'action=post_userdata&file_name='+htmlDecode(post_content)+'&user_id='+user_id+'&'+check+'='+posttype+'&visibility='+visibility;
		jQuery.ajax({
		url: ajaxurl,
		data: str,
		dataType:'json',
		type: 'POST',
		success:function(data){
			jQuery('#post-loader').hide();
			document.getElementById(post_name).value="";
			jQuery('#userwalldata').prepend(stripslashes(data.user_profile));
			jQuery('#userpost').html("");
			a2a.init();
		},
		error:function(data){alert(data);
			alert(data.error);
		}
	});
	var str = 'action=limit_post';
		jQuery.ajax({
		url: ajaxurl,
		data: str,
		type: 'POST',
		success:function(data){
			if(data=="hide")
			{
				jQuery('.textarea').hide();
				jQuery('.buttonpost').hide();
				jQuery('.upload').hide();
			}
		},
		error:function(data){alert(data);
			alert(data.error);
		}
	});



}
else
	{
	alert("Please enter some text to add to the wall");
	}
}
function userwall_delete_comment(post_id,comment,event){


	event.setAttribute('class' , 'fa fa-spinner');
	var str = 'action=delete_comment&post_id='+post_id;
	var retVal = confirm("Are you sure you want to delete the comment?");
   	if( retVal == true ){
	jQuery.ajax({
		url: ajaxurl,
		data: str,
		dataType:'json',
		type: 'POST',
		success:function(data){

			var parentdiv=event.parentNode;
			 parentdiv.parentNode.setAttribute("style","display:none");


		},
		error:function(data){alert(data);
			alert(data.error);
		}
	});
   	}

}



function stripslashes (str) {

	  return (str + '').replace(/\\(.?)/g, function (s, n1) {
	    switch (n1) {
	    case '\\':
	      return '\\';
	    case '0':
	      return '\u0000';
	    case '':
	      return '';
	    default:
	      return n1;
	    }
	  });
	}

function user_post_comment(elm,post_comment,user_id,postid,event,comment_count,commit_limit){
    //alert(elm);
    var pageid = jQuery(elm).parents('#userwalldata').data('pid');
	if(event.keyCode==13)
	{
		if (event.shiftKey === false)
	        {
	var postcomment=jQuery('#'+post_comment+"-"+postid).val();
	var str = 'action=post_usercomment&file_name='+postcomment+'&user_id='+user_id+'&post_id='+postid+'&pageid='+pageid;
	jQuery.ajax({
		url: ajaxurl,
		data: str,
		dataType:'JSON',
		type: 'POST',
		success:function(data){



			jQuery('#'+post_comment+"-"+postid).val('');
			//jQuery('.userwall-comment-data'+"-"+postid).append(stripslashes(data.user_comment));
			jQuery("#"+postid).find('.userwall-comment-container').find('.commenttext').before(data.user_comment);
			var comment_cnt=jQuery('#'+postid+' .userwall_comment_data').length;
			if(comment_cnt>=commit_limit)
			{
				jQuery('#userwall-comment'+"-"+postid).remove();
			}
			a2a.init();
		},
		error:function(data){
			alert(data.error);
		}
	});
	        }
	}

}

function userwall_img_upload(){
	jQuery(".userwall_upload").each(function(){
		var sw_personalwall = jQuery('#sw_personalwall').val();

	    var url = jQuery(this).data('url');

	    var filetype = jQuery(this).data('filetype');
	    var allowed = jQuery(this).data('allowed_extensions');
	    var media_type = jQuery(this).data('media_type');
	     var posttype=jQuery(this).data('posttype');
	    var form = jQuery(this).parents('.userpro').find('form');
            
	/***************** commented by kajal to use wordpress media uploader to uploade images **************/
	// attach a click event (or whatever you want) to some element on your page
            
  
//	    jQuery(this).uploadFile({
//	        url: userwall_upload_path,
//	       allowedTypes: allowed,
//	        onSubmit:function(files){
//	            var statusbar = jQuery('.ajax-file-upload-statusbar:visible');
//	            statusbar.parents('.userpro-input').find('.red').hide();
//	            if (statusbar.parents('.userpro-input').find('img.default').length){
//	                statusbar.parents('.userpro-input').find('img.default').show();
//	                statusbar.parents('.userpro-input').find('img.modified').remove();
//	            }
//	        },
//	        onSuccess:function(files,data,xhr){
//	            data= jQuery.parseJSON(data);
//	            try{
//	            var statusbar = jQuery('.ajax-file-upload-statusbar:visible');
//	            var src = data.target_file_uri;
//	            var srcname = data.target_file_name;
//	            var medianame=data.media_name;
//	            var thumbnail_path=data.thumbnail_path;
//	            if (statusbar.parents('.userpro-input').find('img.default').length){
//
//	            } else if (statusbar.parents('.userpro-input').find('img.avatar').length){
//	            var width = statusbar.parents('.userpro-input').find('img.avatar').attr('width');
//	            var height = statusbar.parents('.userpro-input').find('img.avatar').attr('height');
//	            }
//	            }
//	            catch(e){
//	                alert("File Exceeded Upload Limit.");
//	                var statusbar = jQuery('.ajax-file-upload-statusbar:visible');
//	                statusbar.hide();
//	                return;
//	            }
//
//							var previewFile = '<div id="file_preview" align="center">';
//							var fileExt = data.target_file_uri.split('.').pop();
//							if(fileExt == 'mp4' || fileExt == 'mkv' || fileExt == 'avi')
//							previewFile += '<video style="-moz-user-select: none; border-radius: 10px;max-width:49%; width: 50% !important;"controls><source src="'+data.target_file_uri+'"></video>';
//							else
//							previewFile += "<img src='"+data.target_file_uri+"' height='auto' width='50%'>";
//							previewFile += "</div>";
//
//	            jQuery('.upload').append(previewFile+'<textarea name="image_desc" class="image-desc" id="image_desc" placeholder="Enter media description" rows="2" style="width: 100%;" cols="50"></textarea> <button type="submit"  name="post_photo_desc" id="post_photo_desc" value="Send Photo"><i class="fa fa-send fa-fw"></i><b>Share Media</b></button>');
//
//	             jQuery('#post_photo_desc').click(function(){
//	            	 var photo_desc = jQuery('#image_desc').val();
//	            	 str = 'action=userwall_upload_img&sw_personalwall='+sw_personalwall+'&filetype='+filetype+'&width='+width+'&height='+height+'&src='+src+'&media_type='+media_type+'&srcname='+srcname+'&media_name='+medianame+'&thumbnail_path='+thumbnail_path+'&photo_desc='+photo_desc+'&posttype='+posttype+'&url='+url;
//	            	 jQuery.ajax({
//	  	                url: ajaxurl,
//	  	                data: str,
//	  	                dataType: 'JSON',
//	  	                type: 'POST',
//	  	                success:function(data){
//	  	                	jQuery('#userwalldata').prepend(data.user_profile);
//	  	                    jQuery('#image_desc').remove();
//	  	                    jQuery('#post_photo_desc').remove();
//													jQuery('#file_preview').remove();
//	  	                	statusbar.prev().fadeIn( function() {
//
//	  	                        statusbar.hide();
//					 										a2a.init();
//
//	  	                    });
//	  	                    statusbar.parents('.userpro-input').find('input:hidden').val( src );
//	  	                    statusbar.parents('.userpro-input').find('.userpro-pic-none').hide();
//
//	  	                    // re-validate
//	  	                    form.find('input').each(function(){
//	  	                        jQuery(this).trigger('blur');
//	  	                    });
//
//	  	                }
//	  	            });
//	            });
//	        }
//	    });
	});

}

function userwall_delete_post(postid , event)
{
	event.setAttribute('class' , 'fa fa-spinner');
	var str = 'action=userwall_delete_userpost&postid='+postid;
	var retVal = confirm("Are you sure you want to delete the post?");
   	if( retVal == true ){
	jQuery.ajax({
		url: ajaxurl,
		data: str,
		type: 'POST',
		success:function(data){
			if(data=="You do not have permission to delete this post")
			alert("You do not have permission to delete this post");
			else
			jQuery('#'+postid).hide();
		},
		error:function(data){alert(data);
			alert(data.error);
		}
	});
	}
}

jQuery(document).ready(function() {
    setTimeout(function(){
	userwall_img_upload();
	}, 3000);
        
        
        jQuery(".userwall_upload").each(function(){
		var sw_personalwall = jQuery('#sw_personalwall').val();

	    var url = jQuery(this).data('url');

	    var filetype = jQuery(this).data('filetype');
	    var allowed = jQuery(this).data('allowed_extensions');
	    var media_type = jQuery(this).data('media_type');
	     var posttype=jQuery(this).data('posttype');
	    var form = jQuery(this).parents('.userpro').find('form');
            var file_frame; // variable for the wp.media file_frame
            jQuery( '#frontend-button' ).on( 'click', function( event ) {
                        //event.preventDefault();

                // if the file_frame has already been created, just reuse it
                        if ( file_frame ) {
                                file_frame.open();
                                return;
                        } 

                        file_frame = wp.media.frames.file_frame = wp.media({
                                title: jQuery( this ).data( 'uploader_title' ),
                                button: {
                                        text: jQuery( this ).data( 'uploader_button_text' ),
                                },
                                multiple: false // set this to true for multiple file selection
                        });
                        file_frame.open();
                        file_frame.on( 'select', function() {
                            attachment = file_frame.state().get('selection').first().toJSON();
                          var fileExt = attachment.url.split('.').pop();
                        
                            try{
                                var statusbar = jQuery('.ajax-file-upload-statusbar:visible');
                                var src = attachment.url;
                                var srcname = attachment.filename;
                                var medianame=attachment.media_name;
                                var thumbnail_path=attachment.sizes.thumbnail.url;
                                if (statusbar.parents('.userpro-input').find('img.default').length){
                                } else if (statusbar.parents('.userpro-input').find('img.avatar').length){
                                    var width = statusbar.parents('.userpro-input').find('img.avatar').attr('width');
                                    var height = statusbar.parents('.userpro-input').find('img.avatar').attr('height');
                                }
                            }
                            catch(e){
                                if(fileExt !== 'mp4' && fileExt !== 'mkv' && fileExt !== 'avi')     {
                                alert("File Exceeded Upload Limit.");
                                var statusbar = jQuery('.ajax-file-upload-statusbar:visible');
                                statusbar.hide();
                                return;
                            }
                        }
                        var previewFile = '<div id="file_preview" align="center">';
                        var fileExt = attachment.url.split('.').pop();
                        if(fileExt == 'mp4' || fileExt == 'mkv' || fileExt == 'avi')
                            previewFile += '<video style="-moz-user-select: none; border-radius: 10px;max-width:49%; width: 50% !important;"controls><source src="'+attachment.url+'"></video>';
                        else
                            previewFile += "<img src='"+attachment.url+"' height='auto' width='50%'>";
                            previewFile += "</div>";

                        jQuery('.upload').append(previewFile+'<textarea name="image_desc" class="image-desc" id="image_desc" placeholder="Enter media description" rows="2" style="width: 100%;" cols="50"></textarea> <button type="submit"  name="post_photo_desc" id="post_photo_desc" value="Send Photo"><i class="fa fa-send fa-fw"></i><b>Share Media</b></button>');
                         jQuery('#post_photo_desc').click(function(){
                             
                             var photo_desc = jQuery('#image_desc').val();
                             str = 'action=userwall_upload_img&sw_personalwall='+sw_personalwall+'&filetype='+filetype+'&width='+width+'&height='+height+'&src='+src+'&media_type='+attachment.type+'&srcname='+srcname+'&media_name='+medianame+'&thumbnail_path='+thumbnail_path+'&photo_desc='+photo_desc+'&posttype='+posttype+'&url='+url;
                             jQuery.ajax({
                                    url: ajaxurl,
                                    data: str,
                                    dataType: 'JSON',
                                    type: 'POST',
                                    success:function(data){
                                            jQuery('#userwalldata').prepend(data.user_profile);
                                        jQuery('#image_desc').remove();
                                        jQuery('#post_photo_desc').remove();
                                            jQuery('#file_preview').remove();
                                            statusbar.prev().fadeIn( function() {

                                            statusbar.hide();
                                            a2a.init();

                                        });
                                        statusbar.parents('.userpro-input').find('input:hidden').val( src );
                                        statusbar.parents('.userpro-input').find('.userpro-pic-none').hide();

                                        // re-validate
                                        form.find('input').each(function(){
                                            jQuery(this).trigger('blur');
                                        });

                                    }
                                });
                        });
                });
           });     
    
        });
});

var space_flag = false;
var event_flag = true
jQuery(document).ready(function() {
	jQuery('#smilies a').click(function(){
		var smiley = jQuery(this).html();
		space_flag = true;
		if(event_flag) {
			setTimeout(function(){
				jQuery('.userpost br:last').remove();
				event_flag = false;
			} , 1);
		}
		jQuery('#userpost').append(smiley);
	});
});

jQuery(function(){
	jQuery('.userpost').keypress(function (e) {
		if(e.which == 32 && space_flag == true){
			e.preventDefault();
			jQuery('.userpost').append('&nbsp;');
		}else{
			space_flag = false;
		}
		if(e.which != 32 || e.which != 13)
			event_flag = true;

	});
});

jQuery(document).on('click', '.userpro-social-notify', function(){
	user_id = jQuery(this).data('user_id');
	up_sw_overlay_show( user_id );
});

function up_sw_overlay_show( user_id ) {

	sw_msg_cancel();

	jQuery('body').append('<div class="socialwall-overlay"></div>');
	jQuery('body').append('<div class="social-notify-overlay-loader"></div>');

	/* prepare ajax file */
	str = 'action=socialwall_display_notify&user_id=' + user_id;
	jQuery.ajax({
		url: userpro_ajax_url,
		data: str,
		dataType: 'JSON',
		type: 'POST',
		success:function(data){
			if (jQuery('.social-notify-overlay-loader').length == 1) {
				jQuery('.social-notify-overlay-loader').remove();
				jQuery('body').append( data.html );

				/* fancy textarea
				jQuery('.socialwall-overlay-content textarea').autoResize({
					animate: {enabled: true},
					maxHeight: '90px'
				});*/

				/* limit content by scrollbar */
				jQuery('.userpro-social-notify').remove();
				sw_notify_adjust();
				userpro_chosen();

			}
		}
	});

}

function sw_msg_cancel(){
	jQuery('.tipsy').remove();
	jQuery('.socialwall-overlay-content, .socialwall-overlay').remove();
}

jQuery(document).on('click', '.socialwall-overlay, a.socialwall-notify-close',function(){
	sw_msg_cancel();
});

function sw_notify_adjust(){

	if ( jQuery(window).width() > 800) {
	jQuery('.social-notify-body.alt').css({'max-height': jQuery(window).height() - 300 + 'px'});
	} else {
	jQuery('.social-notify-body.alt').css({'max-height': jQuery('.socialwall-overlay-content').innerHeight() - jQuery('.social-notify-user').innerHeight() - 15 + 'px'});
	}

/*//	jQuery('.social-notify-body.alt').mCustomScrollbar('destroy');
	jQuery('.social-notify-body.alt').mCustomScrollbar({
		theme:"dark-2",
		advanced:{
			updateOnContentResize: true,
			autoScrollOnFocus: false,
		}
	});
*/
	if ( jQuery(window).width() > 800) {
	jQuery('.userpro-conv-ajax').css({'height' :  jQuery(window).height() - 400 - jQuery('.userpro-send-form').innerHeight() + 'px' });
	} else {
	jQuery('.userpro-conv-ajax').css({'height' :  jQuery('.socialwall-overlay-content').innerHeight() - jQuery('.social-notify-user').innerHeight() - 15 - jQuery('.userpro-send-form').innerHeight() + 'px' });
	}

	/*jQuery('.userpro-conv-ajax').mCustomScrollbar('destroy');
	jQuery('.userpro-conv-ajax').mCustomScrollbar({
		theme:"dark-2",
		advanced:{
			updateOnContentResize: true,
		}
	});*/

	sw_notify_overlay_resize();

}

function sw_notify_overlay_resize(){
	jQuery('.socialwall-overlay-content').animate({
		'opacity' : 1,
		'margin-top' : '-' + jQuery('.socialwall-overlay-content').innerHeight() / 2 + 'px'
	});
}
