jQuery( document ).ready( function()
{
	// Object containing all the plupload uploaders
	var inoplugs_image_uploaders = {},
		max;


	// Hide "Uploaded files" and "Progress" as long as there are no files uploaded
	// Note that we can have multiple upload forms in the page, so relative path to current element is important
	jQuery( 'div[inoplugs_plupload]' ).each( function()
	{
		var unique = jQuery(this).attr('inoplugs_plupload');
		
		var $uploaded = jQuery('#inoplugs_plupload_uploaded_'+unique);
		var $list = jQuery('#inoplugs_plupload_uploaded_files_'+unique).children();
		
		if($list.length == 0)
		{
			$uploaded.fadeOut();
		}
		else
		{
			$uploaded.fadeIn();
		}	
		
		$list = jQuery( '#inoplugs_plupload_progress_' + unique).find( 'li' );
		if($list.length == 0)
		{
			jQuery( '#inoplugs_plupload_progress_' + unique).fadeOut();
		}
		else
		{
			jQuery( '#inoplugs_plupload_progress_' + unique).fadeIn();
		}	
	} );

	// Hide "Uploaded files" title if there are no files uploaded after deleting files
	jQuery( '.inoplugs_images' ).on( 'click', '.inoplugs_delete_file', function()
	{
		// Check if we need to show drop target
		var $images = jQuery(this).parents( '.inoplugs_images' ),
			uploaded = $images.children().length - 1, // -1 for the one we just deleted
			$dragndrop = $images.siblings( '.inoplugs_drag_drop' );

		if ( 0 == uploaded )
		{
			$images.siblings( '.inoplugs_uploaded_title' ).addClass( 'hidden' );
			$images.addClass( 'hidden' );
		}

		// After delete files, show the Drag & Drop section
		$dragndrop.show();
	} );

		// Using all the upload containers
	jQuery( 'div[inoplugs_plupload]' ).each( function()
	{
		var unique = jQuery(this).attr('inoplugs_plupload');

		// Adding container, browser button and drag ang drop area
		var inoplugs_plupload_init = jQuery.extend( {
				container    : 'inoplugs_plupload_container_'+ unique,
				browse_button: 'inoplugs_plupload_browse_button_' + unique,
				drop_element : 'inoplugs_plupload_dragdrop_'+ unique
			}, inoplugs_plupload_defaults );
		
		// Add action and all hidden fields to the ajax call
		inoplugs_plupload_init['multipart_params'] = jQuery.extend( {
//				action  : 'plupload_image_upload'
			}, inoplugs_plupload_init['multipart_params']);

		jQuery('[inoplugs_plupload_hidden="'+ unique + '"]').each(function()
		{
			var fields = jQuery(this).serializeArray();
			jQuery.each(fields, function(i, field)
			{
				inoplugs_plupload_init['multipart_params'][field.name] = field.value;
			});
		});

			// Create new uploader
		inoplugs_image_uploaders[unique] = new plupload.Uploader( inoplugs_plupload_init );
		
		inoplugs_image_uploaders[unique].bind('Init', function(up)
		{
			var unique = this.settings.multipart_params.inoplugs_plupload_unique_id;
			var callback = this.settings.multipart_params.inoplugs_plupload_java_init;
			if((typeof callback != 'undefined') && (callback.length > 0))
			{
				var ret=false;
				var call = 'if(window.'+ callback + ') {ret = ' + callback + '(up, unique);};';
				eval(call);
				if((typeof ret == 'boolean') && ret)
					return;
			}
        });
				
		inoplugs_image_uploaders[unique].init();

		inoplugs_image_uploaders[unique].bind( 'FilesAdded', function( up, files )
		{
			var unique = this.settings.multipart_params.inoplugs_plupload_unique_id;
			var callback = this.settings.multipart_params.inoplugs_plupload_java_files_added;
			if((typeof callback != 'undefined') && (callback.length > 0))
			{
				var ret = false;		
				var call = 'if(window.'+ callback + ') {ret = ' + callback + '(up, unique);};';
				eval(call);
				if((typeof ret == 'boolean') && ret)
					return;
			}
			
			var max_file_uploads = jQuery('#inoplugs_plupload_max_file_upload_' + unique).val();
			var hide_on_max_files = jQuery('#inoplugs_plupload_hide_on_max_file_' + unique).val();
			var uploaded_list = jQuery('#inoplugs_plupload_uploaded_files_' + unique).children().length;
			
			var msg = 'You may only upload ' + max_file_uploads + ' file';
			if (max_file_uploads > 1)
				msg += 's';
			
				// Remove files from queue if exceed max file uploads
			if ((uploaded_list + files.length) > max_file_uploads)
			{
				for (var i = files.length; i--;)
				{
					up.removeFile( files[i] );
				}
				alert( msg );
				return false;
			}

			// Hide drag & drop section if reach max file uploads
			if (((uploaded_list + files.length) == max_file_uploads) && (hide_on_max_files == '1'))
			{
				jQuery('#inoplugs_plupload_upload_' + unique).hide();
			}

			max = parseInt(up.settings.max_file_size, 10);

			// Upload files
			plupload.each(files, function(file)
			{
				add_loading(up, file, unique);
				add_throbber(file);
				if (file.size >= max)
					remove_error(file);
			} );
			up.refresh();
			up.start();
		} );

		inoplugs_image_uploaders[unique].bind( 'Error', function( up, e )
		{
			var unique = this.settings.multipart_params.inoplugs_plupload_unique_id;
			var callback = this.settings.multipart_params.inoplugs_plupload_java_error;
			if((typeof callback != 'undefined') && (callback.length > 0))
			{
				var ret = false;
				var call = 'if(window.'+ callback + ') {ret = ' + callback + '(up, unique, e);};'
				eval(call);
				if((typeof ret == 'boolean') && ret)
					return;
			}
			
			if((typeof ret == 'boolean') && !ret)
			{
				ret = {
					success: false,
					message: e.file.name + ' ('+ e.file.size+' Byte) - Error occured: ' + e.message
				};
			}
			
			add_uploaded(up, unique, e.file, ret);
			
//			add_loading( up, e.file );
//			remove_error( e.file );
			up.removeFile( e.file );

		} );

		inoplugs_image_uploaders[unique].bind( 'UploadProgress', function( up, file )
		{
			var unique = this.settings.multipart_params.inoplugs_plupload_unique_id;			
			var callback = this.settings.multipart_params.inoplugs_plupload_java_upload_progress;
			if((typeof callback != 'undefined') && (callback.length > 0))
			{
				var ret = false;
				var call = 'if(window.'+ callback + ') {ret = ' + callback + '(up, unique);};'
				eval(call);
				if((typeof ret == 'boolean') && ret)
					return;
			}
			
			// Update the loading bar div
			var bar = jQuery('#inoplugs_plupload_progress_' + unique).find('#' + file.id).find('.inoplugs_image_uploading_progress_bar');
			bar.css( 'width', file.percent + '%' );
		} );

		//	also fired on parser error !!!!
		inoplugs_image_uploaders[unique].bind( 'FileUploaded', function( up, file, response )
		{
			var unique = this.settings.multipart_params.inoplugs_plupload_unique_id;
			
				//	remove upload progress
			jQuery( '#inoplugs_plupload_progress_' + unique).find( '#' + file.id ).remove();
			var list = jQuery( '#inoplugs_plupload_progress_' + unique).find( 'li' ).length;
			
			if(list == 0)
			{
				jQuery( '#inoplugs_plupload_progress_' + unique).fadeOut();
			}
			
			var callback = this.settings.multipart_params.inoplugs_plupload_java_file_uploaded;
			var ret = false;
			if((typeof callback != 'undefined') && (callback.length > 0))
			{
				var call = 'if(window.'+ callback + ') {ret = ' + callback + '(up, unique, file, response);};'
				eval(call);
				if((typeof ret == 'boolean') && ret)
					return;
			}
			if((typeof ret == 'boolean') && !ret)
			{
				ret = {
					success: true,
					message: file.name + ' ('+file.size+' Byte) uploaded successfully'
				};
			}
			
			add_uploaded(up, unique, file, ret);
			
			
			return;
			
			//	Currently not implemented
			
			var resp = response;
			
			var i = 1;

			
			
	//		var $xml = jQuery.parseXML( response.response );
	//		var res1 = wpAjax.parseAjaxResponse( $xml, 'ajax-response' );
			
			var res = wpAjax.parseAjaxResponse( jQuery.parseXML( response.response ), 'ajax-response' ),
				$uploaded = jQuery( '#' + this.settings.container + ' .inoplugs_uploaded' ),
				$uploaded_title = jQuery( '#' + this.settings.container + ' .inoplugs_uploaded_title' );
			false === res.errors ? jQuery( 'li#' + file.id ).replaceWith( res.responses[0].data ) : remove_error( file );

			// Show them all
			$uploaded.removeClass( 'hidden' );
			$uploaded_title.removeClass( 'hidden' );
		} );
	});

	/**
	 * Helper functions
	 */

	/**
	 * Removes li element if there is an error with the file
	 *
	 * @return void
	 */
	function remove_error(file)
	{
		jQuery( 'li#' + file.id )
			.addClass( 'inoplugs_image_error' )
			.delay( 1600 )
			.fadeOut( 'slow', function()
				{
					jQuery(this).remove();
				}
			);
	}

	/**
	 * Adds the li element for uploaded files.
	 * Message must be:
	 * {	success:  true | false
	 *		message:  string
	 *	}
	 */
	function add_uploaded(up, unique, file, response)
	{
		var $list = jQuery('#inoplugs_plupload_uploaded_' + unique).find( 'li' );
			
		if($list.length == 0)
		{
			jQuery( '#inoplugs_plupload_uploaded_' + unique).fadeIn();
		}
		
		$list = jQuery( '#inoplugs_plupload_uploaded_' + unique).find( 'ul' );
		var li_class = '';
		if(!response.success)
		{
			li_class = ' class="inoplugs_plupload_uploaded_error"';
		}
		
		var loaded_list = "<li id='" + file.id + "'"+li_class+">";
		loaded_list += response.message;
		loaded_list += '</li>';
		$list.append(loaded_list);
	}

	/**
	 * Adds loading li element
	 *
	 * @return void
	 */
	function add_loading( up, file, unique)
	{
		var $list = jQuery( '#inoplugs_plupload_progress_' + unique).find( 'li' );
			
		if($list.length == 0)
		{
			jQuery( '#inoplugs_plupload_progress_' + unique).fadeIn();
		}
		
		$list = jQuery( '#inoplugs_plupload_progress_' + unique).find( 'ul' );
		
		var load_list = "<li id='" + file.id + "'>";
		load_list += "<div id='" + file.id + "_throbber' class='inoplugs_image_uploading_status'></div>";
		load_list += "<div class='inoplugs_image_uploading_file'>" + file.name + "</div>";
		load_list += "<div class='inoplugs_image_uploading_progress_bar_container'>";
		load_list += "<div class='inoplugs_image_uploading_progress_bar'></div>";
		load_list += "</div>";
		
		load_list += "</li>";
		$list.append(load_list);
	}

	/**
	 * Adds loading throbber while waiting for a response
	 *
	 * @return void
	 */
	function add_throbber( file )
	{
		jQuery( '#' + file.id + '_throbber' ).html( "<img class='inoplugs_loader' height='64' width='64' src='" + inoplugs_plupload_general_data.url_loader_img + "'/>" );
	}
});
