/* 
 * Contains all the callback handlers for pluplöoad
 * 
 * Return true, if all handling is done by this handler
 * Otherwise false for default handling 
 * or
 *	{	response['success']
 *		response['message']
 *	}
 */

	
function wc_plupload_handler_files_added(up, unique)
{
	return false;
}
	
	
function wc_plupload_handler_error(up, unique, error_object)
{
	var response = {};
	response['success'] = false;
	response['message'] = error_object.file.name + ': ' + wc_email_attachments.uploadinfo_error + error_object.message;
	
	return response;
}
	
/*
 * (also called on parser error)
 */
function wc_plupload_handler_files_uploaded(up, unique, file, response_object)
{
	if((typeof response_object == 'undefined') || (typeof response_object.response == 'undefined'))
		return false;
	
	var response = {};
	try
	{
		response = jQuery.parseJSON(response_object.response);
		if(response.message == 'success')
		{
			response['success'] = true;			
			response['message'] = file.name + ' ' + wc_email_attachments.uploadinfo_ok + response.newname; 
		}
		else
		{
			response['success'] = false;
			response['message'] = file.name + ': ' + wc_email_attachments.uploadinfo_error + response.message;
		}
	}
	catch(err)
	{
		response['success'] = false;
		response['message'] =  'Unknown error occured in uploading file ' + file.name;
	}
	
	return response;
	
}

