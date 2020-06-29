var topWin = window.dialogArguments || opener || parent || top, uploader, uploader_init, filesToAdd = 0;

function fileDialogStart() {
	jQuery("#media-upload-error").empty();
}

// progress and success handlers for media multi uploads
function fileQueued(fileObj) {
	// Get rid of unused form
	jQuery('.media-blank').remove();

	var items = jQuery('#media-items').children(), postid = post_id || 0;

	// Collapse a single item
	if ( items.length == 1 ) {
		items.removeClass('open').find('.slidetoggle').slideUp(200);
	}
	// Create a progress bar containing the filename
	jQuery('#media-items').append('<div id="media-item-' + fileObj.id + '" class="media-item child-of-' + postid + '"><div class="progress"><div class="percent">0%</div><div class="bar"></div></div><div class="filename original"> ' + fileObj.name + '</div></div>');

	// Disable submit
	jQuery('#insert-gallery').prop('disabled', true);
}

function uploadStart() {
	jQuery('#overlay').show();
    jQuery('.wysija-msg.ajax').html('').hide();
}

function uploadProgress(up, file) {
	/*var item = jQuery('#media-item-' + file.id);

	jQuery('.bar', item).width( (200 * file.loaded) / file.size );
	jQuery('.percent', item).html( file.percent + '%' );*/
}

// check to see if a large file failed to upload
function fileUploading(up, file) {
	var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10);

	if ( max > hundredmb && file.size > hundredmb ) {
		setTimeout(function(){
			var done;

			if ( file.status < 3 && file.loaded === 0 ) { // not uploading
				wpFileError(file, pluploadL10n.big_upload_failed.replace('%1$s', '<a class="uploader-html" href="#">').replace('%2$s', '</a>'));
				up.stop(); // stops the whole queue
				up.removeFile(file);
				up.start(); // restart the queue
			}
		}, 10000); // wait for 10 sec. for the file to start uploading
	}
}

function updateMediaForm() {
	var items = jQuery('#media-items').children();

	// Just one file, no need for collapsible part
	if ( items.length == 1 ) {
		items.addClass('open').find('.slidetoggle').show();
		jQuery('.insert-gallery').hide();
	} else if ( items.length > 1 ) {
		items.removeClass('open');
		// Only show Gallery button when there are at least two files.
		jQuery('.insert-gallery').show();
	}

	// Only show Save buttons when there is at least one file.
	if ( items.not('.media-blank').length > 0 )
		jQuery('.savebutton').show();
	else
		jQuery('.savebutton').hide();
}

function uploadSuccess(fileObj, serverData) {
	var item = jQuery('#media-item-' + fileObj.id);

	// on success serverData should be numeric, fix bug in html4 runtime returning the serverData wrapped in a <pre> tag
	serverData = serverData.replace(/^<pre>(\d+)<\/pre>$/, '$1');

	// if async-upload returned an error message, place it in the media item div and return
	if ( serverData.match(/media-upload-error|error-div/) ) {
		item.html(serverData);
		return;
	} else {
		jQuery('.percent', item).html( pluploadL10n.crunching );
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

	// Increment the counter.
	/*if ( post_id && item.hasClass('child-of-' + post_id) )
		jQuery('#attachments-count').text(1 * jQuery('#attachments-count').text() + 1);*/
}

function WYSIJAprepareMediaItem(fileObj, serverData) {
	var f = ( typeof shortform == 'undefined' ) ? 1 : 2, item = jQuery('#media-item-' + fileObj.id);
	// Move the progress bar to 100%
	jQuery('.bar', item).remove();
	jQuery('.progress', item).hide();

	// trim the attachement_id
	serverData = serverData.trim();

	// Old style: Append the HTML returned by the server -- thumbnail and form inputs
	if ( isNaN(serverData) || !serverData ) {
		item.append(serverData);
		WYSIJAprepareMediaItemInit(fileObj);
	}
	// New style: server data is just the attachment ID, fetch the thumbnail and form html from the server
	else {
		item.load('async-upload.php', {attachment_id:serverData, fetch:f}, function(result){
                    WYSIJAsetParams(result,fileObj);
                    WYSIJAprepareMediaItemInit(fileObj);
                    updateMediaForm();
                });
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

function WYSIJAsetParams(result,fileObj){
    /* get the file uploaded and add it to the selection */
    var wpid=0;
    var dims="";
    var imgdimensions=null;

    wpid=jQuery('#media-item-'+fileObj.id).attr('alt');
    // trim the attachment id
    wpid = wpid.trim();
    dims=jQuery('#media-dims-'+wpid).html();
    imgdimensions=dims.split('&nbsp;×&nbsp;');

    var elementUrl = jQuery('#media-item-'+fileObj.id+' tbody button.urlfile');

    if(elementUrl.attr('title')=== undefined) {
    	var fullUrl = elementUrl.attr('data-link-url');
    } else {
	    var fullUrl = elementUrl.attr('title');
    }

    // If the image is bigger that 600px width, let's try to load our generated image size.
    // If our image size is not present, it's probably an old image uploaded before MailPoet install,
    // So we load the full url.
    if(parseInt(imgdimensions[0])>600) {

    		// Calculate 600px image size from our big image.
    		var currentWidth = imgdimensions[0];
    		var currentHeight = imgdimensions[1];
    		var aspectRatio = currentHeight / currentWidth;
    		var newWidth = 600;
    		var newHeight = parseInt(newWidth * aspectRatio);

    		// Generate full url.
        var newDimensions = '-' + newWidth + 'x' + newHeight;
        var ind1 = fullUrl.lastIndexOf('/');
        var ind2 = fullUrl.lastIndexOf('.');
        var newUrl = fullUrl.substring(0, ind2) + newDimensions + fullUrl.substring(ind2);

        // Check if our 600px cropped image size exists.
        jQuery.ajax({
            url:newUrl,
            type:'HEAD',
            async: false,
            success: function()
            {
                fullUrl = newUrl;
            }
        });

        imgdimensions[0] = newWidth;
        imgdimensions[1] = newHeight;

    }

  	var insertArray={
  	    identifier:"wp-"+wpid,
  	    width:imgdimensions[0],
  	    height:imgdimensions[1],
  	    url:fullUrl,
  	    thumb_url:jQuery('#thumbnail-head-'+wpid+' img.thumbnail').attr('src')
  	};

    insert(insertArray);

    return true;
}

function setResize(arg) {
	if ( arg ) {
		if ( uploader.features.jpgresize ){
			uploader.settings['resize'] = { width: resize_width, height: resize_height, quality: 100 };
		} else {
			uploader.settings.multipart_params.image_resize = true;
		}
	} else {
		delete(uploader.settings.resize);
		delete(uploader.settings.multipart_params.image_resize);
	}
}

// generic error message
function wpQueueError(message) {
	jQuery('#media-upload-error').show().html( '<div class="error"><p>' + message + '</p></div>' );
}

// file-specific error messages
function wpFileError(fileObj, message) {
	itemAjaxError(fileObj.id, message);
}

function itemAjaxError(id, message) {
	var item = jQuery('#media-item-' + id), filename = item.find('.filename').text(), last_err = item.data('last-err');

	if ( last_err == id ) // prevent firing an error for the same file twice
		return;

	item.html('<div class="error-div">'
				+ '<a class="dismiss" href="#">' + pluploadL10n.dismiss + '</a>'
				+ '<strong>' + pluploadL10n.error_uploading.replace('%s', jQuery.trim(filename)) + '</strong> '
				+ message
				+ '</div>').data('last-err', id);

}

function uploadComplete(files) {
    jQuery('.wysija-msg.ajax').html('<div class="notice-msg updated"><ul><li>' + pluploadL10n.files_successfully_uploaded.replace('%d', jQuery.trim(files.length)) + '</li></ul></div>').show();
    if (jQuery('#overlay')) {
        // Ensure that overlay is hidden when upload has completed
        jQuery('#overlay').hide();
    }
}

function switchUploader(s) {
	if ( s ) {
		deleteUserSetting('uploader');
		jQuery('.media-upload-form').removeClass('html-uploader');

		if ( typeof(uploader) == 'object' )
			uploader.refresh();
	} else {
		setUserSetting('uploader', '1'); // 1 == html uploader
		jQuery('.media-upload-form').addClass('html-uploader');
	}
}

function dndHelper(s) {
	var d = document.getElementById('dnd-helper');

	if ( s ) {
		d.style.display = 'block';
	} else {
		d.style.display = 'none';
	}
}

function uploadError(fileObj, errorCode, message, uploader) {
	var hundredmb = 100 * 1024 * 1024, max;

	switch (errorCode) {
		case plupload.FAILED:
			wpFileError(fileObj, pluploadL10n.upload_failed);
			break;
		case plupload.FILE_EXTENSION_ERROR:
			wpFileError(fileObj, pluploadL10n.invalid_filetype);
			break;
		case plupload.FILE_SIZE_ERROR:
			uploadSizeError(uploader, fileObj);
			break;
		case plupload.IMAGE_FORMAT_ERROR:
			wpFileError(fileObj, pluploadL10n.not_an_image);
			break;
		case plupload.IMAGE_MEMORY_ERROR:
			wpFileError(fileObj, pluploadL10n.image_memory_exceeded);
			break;
		case plupload.IMAGE_DIMENSIONS_ERROR:
			wpFileError(fileObj, pluploadL10n.image_dimensions_exceeded);
			break;
		case plupload.GENERIC_ERROR:
			wpQueueError(pluploadL10n.upload_failed);
			break;
		case plupload.IO_ERROR:
			max = parseInt(uploader.settings.max_file_size, 10);

			if ( max > hundredmb && fileObj.size > hundredmb )
				wpFileError(fileObj, pluploadL10n.big_upload_failed.replace('%1$s', '<a class="uploader-html" href="#">').replace('%2$s', '</a>'));
			else
				wpQueueError(pluploadL10n.io_error);
			break;
		case plupload.HTTP_ERROR:
			wpQueueError(pluploadL10n.http_error);
			break;
		case plupload.INIT_ERROR:
			jQuery('.media-upload-form').addClass('html-uploader');
			break;
		case plupload.SECURITY_ERROR:
			wpQueueError(pluploadL10n.security_error);
			break;
		default:
			wpFileError(fileObj, pluploadL10n.default_error);
	}
}

function uploadSizeError( up, file, over100mb ) {
	var message;

	if ( over100mb )
		message = pluploadL10n.big_upload_queued.replace('%s', file.name) + ' ' + pluploadL10n.big_upload_failed.replace('%1$s', '<a class="uploader-html" href="#">').replace('%2$s', '</a>');
	else
		message = pluploadL10n.file_exceeds_size_limit.replace('%s', file.name);

	jQuery('#media-items').append('<div id="media-item-' + file.id + '" class="media-item error"><p>' + message + '</p></div>');
	up.removeFile(file);
}

jQuery(document).ready(function($){
	$('.media-upload-form').bind('click.uploader', function(e) {
		var target = $(e.target), tr, c;

		if ( target.is('input[type="radio"]') ) { // remember the last used image size and alignment
			tr = target.closest('tr');

			if ( tr.hasClass('align') )
				setUserSetting('align', target.val());
			else if ( tr.hasClass('image-size') )
				setUserSetting('imgsize', target.val());

		} else if ( target.is('button.button') ) { // remember the last used image link url
			c = e.target.className || '';
			c = c.match(/url([^ '"]+)/);

			if ( c && c[1] ) {
				setUserSetting('urlbutton', c[1]);
				target.siblings('.urlfield').val( target.data('link-url') );
			}
		} else if ( target.is('a.dismiss') ) {
			target.parents('.media-item').fadeOut(200, function(){
				$(this).remove();
			});
		} else if ( target.is('.upload-flash-bypass a') || target.is('a.uploader-html') ) { // switch uploader to html4
			$('#media-items, p.submit, span.big-file-warning').css('display', 'none');
			switchUploader(0);
			e.preventDefault();
		} else if ( target.is('.upload-html-bypass a') ) { // switch uploader to multi-file
			$('#media-items, p.submit, span.big-file-warning').css('display', '');
			switchUploader(1);
			e.preventDefault();
		} else if ( target.is('a.describe-toggle-on') ) { // Show
			target.parent().addClass('open');
			target.siblings('.slidetoggle').fadeIn(250, function(){
				var S = $(window).scrollTop(), H = $(window).height(), top = $(this).offset().top, h = $(this).height(), b, B;

				if ( H && top && h ) {
					b = top + h;
					B = S + H;

					if ( b > B ) {
						if ( b - B < top - S )
							window.scrollBy(0, (b - B) + 10);
						else
							window.scrollBy(0, top - S - 40);
					}
				}
			});
			e.preventDefault();
		} else if ( target.is('a.describe-toggle-off') ) { // Hide
			target.siblings('.slidetoggle').fadeOut(250, function(){
				target.parent().removeClass('open');
			});
			e.preventDefault();
		}
	});

	// init and set the uploader
	uploader_init = function() {
		uploader = new plupload.Uploader(wpUploaderInit);

		$('#image_resize').bind('change', function() {
			var arg = $(this).prop('checked');

			setResize( arg );

			if ( arg )
				setUserSetting('upload_resize', '1');
			else
				deleteUserSetting('upload_resize');
		});

		uploader.bind('Init', function(up) {
			var uploaddiv = $('#plupload-upload-ui');

			setResize( getUserSetting('upload_resize', false) );

			if ( up.features.dragdrop && ! $(document.body).hasClass('mobile') ) {
				uploaddiv.addClass('drag-drop');
				$('#drag-drop-area').bind('dragover.wp-uploader', function(){ // dragenter doesn't fire right :(
					uploaddiv.addClass('drag-over');
				}).bind('dragleave.wp-uploader, drop.wp-uploader', function(){
					uploaddiv.removeClass('drag-over');
				});
			} else {
				uploaddiv.removeClass('drag-drop');
				$('#drag-drop-area').unbind('.wp-uploader');
			}
		});

		uploader.init();

		uploader.bind('FilesAdded', function(up, files) {
			var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10);

			$('#media-upload-error').html('');
			uploadStart();

			// prevent closing of the popup
			filesToAdd = files.length;

			plupload.each(files, function(file){
				if ( max > hundredmb && file.size > hundredmb && up.runtime != 'html5' )
					uploadSizeError( up, file, true );
				else
					fileQueued(file);
			});

			// Ok, this is a fix; Not the best solution for
			// We have to keep our heads looking for Plupload changes
			// at the WordPress Core
			up.settings.resize = {
				resize: true
			};

			up.refresh();
			up.start();
		});

		uploader.bind('UploadFile', function(up, file) {
			fileUploading(up, file);
		});

		uploader.bind('UploadProgress', function(up, file) {
			uploadProgress(up, file);
		});

		uploader.bind('Error', function(up, err) {
			uploadError(err.file, err.code, err.message, up);
			up.refresh();
		});

		uploader.bind('FileUploaded', function(up, file, response) {
			uploadSuccess(file, response.response);
		});

		uploader.bind('UploadComplete', function(up, files) {
			uploadComplete(files);
		});
	}

	if ( typeof(wpUploaderInit) == 'object' )
		uploader_init();

});
