
function WYSIJAprepareMediaItem(fileObj, serverData) {
	var f = ( typeof shortform == 'undefined' ) ? 1 : 2, item = jQuery('#media-item-' + fileObj.id);
	// Move the progress bar to 100%
	jQuery('.bar', item).remove();
	jQuery('.progress', item).hide();

	// Old style: Append the HTML returned by the server -- thumbnail and form inputs
	if ( isNaN(serverData) || !serverData ) {
		item.append(serverData);
		WYSIJAprepareMediaItemInit(fileObj);
	}
	// New style: server data is just the attachment ID, fetch the thumbnail and form html from the server
	else {
		item.load('async-upload.php',
            {
                attachment_id:serverData,
                fetch:f
            },
            function(result){
                WYSIJAsetParams(result,fileObj);
                WYSIJAprepareMediaItemInit(fileObj);
                updateMediaForm();
            }
        );
	}
}

function WYSIJAprepareMediaItemInit(fileObj) {
	var item = jQuery('#media-item-' + fileObj.id);
	// Clone the thumbnail as a "pinkynail" -- a tiny image to the left of the filename
	jQuery('.thumbnail', item).clone().attr('className', 'pinkynail toggle').prependTo(item);

        jQuery('.pinkynail', item).addClass('thumbnail');
        jQuery('.pinkynail', item).removeClass('pinkynail');
        jQuery('.thumbnail', item).css('margin-top','0px');


        jQuery('.filename.new', item).remove();


	// Replace the original filename with the new (unique) one assigned during upload
	jQuery('.filename.original', item).replaceWith( jQuery('.filename.new', item) );
        jQuery('.describe-toggle-on , .describe-toggle-on', item).remove();

	// Open this item if it says to start open (e.g. to display an error)
	jQuery('#media-item-' + fileObj.id + '.startopen').removeClass('startopen').slideToggle(500).siblings('.toggle').toggle();
}




function WYSIJAuploadSuccess(fileObj, serverData) {
	// if async-upload returned an error message, place it in the media item div and return
	if ( serverData.match('media-upload-error') ) {
		jQuery('#media-item-' + fileObj.id).html(serverData);
		return;
	}

	WYSIJAprepareMediaItem(fileObj, serverData);
	updateMediaForm();

        jQuery('.media-item').addClass('wysija-thumb');
        jQuery('.media-item').addClass('selected');
        jQuery('.media-item').removeClass('media-item');
        jQuery('.wysija-thumb').css('border-color','transparent');
        jQuery('#media-item-'+fileObj.id).attr('alt', serverData);



	// Increment the counter.
	if ( jQuery('#media-item-' + fileObj.id).hasClass('child-of-' + post_id) )
		jQuery('#attachments-count').text(1 * jQuery('#attachments-count').text() + 1);


}


function WYSIJAuploadComplete(fileObj) {
	// If no more uploads queued, enable the submit button
	if ( swfu.getStats().files_queued == 0 ) {
		jQuery('#cancel-upload').attr('disabled', 'disabled');
		jQuery('#insert-gallery').attr('disabled', '');
	}



        return true;
}


function WYSIJAsetParams(result,fileObj){
    /* get the file uploaded and add it to the selection */
    var wpid=0;
    var dims="";
    var imgdimensions=null;

    wpid=jQuery('#media-item-'+fileObj.id).attr('alt');
    dims=jQuery('#media-dims-'+wpid).html();
    imgdimensions=dims.split('&nbsp;×&nbsp;');
    /*if(parseInt(imgdimensions[0])>1024){//if the image is bigger than 1024 we will use the 1024 image as original

        var dimsstring=jQuery('#image-size-large-'+wpid).siblings('label.help').html();
        dimsstring=dimsstring.replace("(","").replace(")","");

        imgdimensions=dimsstring.split('&nbsp;×&nbsp;');
        var fullurl=jQuery('#media-item-'+fileObj.id+' tbody button.urlfile').attr('title').replace(fileObj.type.toLowerCase(),"-"+imgdimensions[0]+"x"+imgdimensions[1]+fileObj.type.toLowerCase());
        var insertArray={
            identifier:"wp-"+wpid,
            width:imgdimensions[0],
            height:imgdimensions[1],
            url:fullurl,
            thumb_url:jQuery('#thumbnail-head-'+wpid+' img.thumbnail').attr('src')
        };
    }else{*/
        var insertArray={
            identifier:"wp-"+wpid,
            width:imgdimensions[0],
            height:imgdimensions[1],
            thumb_url:jQuery('#thumbnail-head-'+wpid+' img.thumbnail').attr('src')
        };
    /*}*/
    var elementUrl=jQuery('#media-item-'+fileObj.id+' tbody button.urlfile');

    if(elementUrl.attr('title')=== undefined)   insertArray.url=elementUrl.attr('data-link-url');
    else insertArray.url=elementUrl.attr('title');

    insert(insertArray);
    if ( swfu.getStats().files_queued == 0 ) {
                /* and then show the close popup button */
               closeLbox();
	}
    return true;
}
