
jQuery(document).ready(function() {
	jQuery('#load').hide();
	});
	
jQuery(function() {
		jQuery("#uploadrefresh").click(function() 
		{
		//	location.reload();
			jQuery('form').submit();
		})
});
		
jQuery(function() {
		jQuery(".delete").click(function() 
		{	
			var id = jQuery(this).attr("filedelete");
			if(!confirm( wc_email_attachments.delfile_yesno + "\r\n '" + id + " ?"))
				return false;			
				jQuery('.statusinfo').html(wc_email_attachments.delete_file);
				jQuery('#load').fadeIn();
			var senddata = {
				action: 'delete-file',
				filename: id,
				wc_ip_attachment_nonce: MyAjax.wc_ip_attachment_nonce
				}
			jQuery.ajax({
			   type: "POST",
			   url: ajaxurl,
			   data: senddata,
			   cache: false,
			   success: function(response, textStatus, jqXHR)
				  {
					  var t = response.message;
					  MyAjax.wc_ip_attachment_nonce = response.wc_ip_attachment_nonce;
					  if(response.message !== "success")
						{
							jQuery('a[filedelete = "' + id + '"]').each(
							function (i)
							{
								jQuery(this).parent().prev().prev().html('<span class="deleteerror">'+ response.message + "</span>");
							});
						}
						else
						{
							jQuery('a[filedelete = "' + id + '"]').each(
							function (i)
							{
								jQuery(this).parent().parent().slideUp('slow', function() {jQuery(this).remove();});
							})
						};
					jQuery('#load').fadeOut();
					jQuery('.statusinfo').html(wc_email_attachments.reset_message);
				  }

				});

			return false;
		});
	}
);
	
jQuery(document).ready(function($){
 	$(".wc_ip_toggle_section").click(function()
		{
			if($(this).attr('no-toggle')) return;
			$(this).toggleClass('open').next().toggle('slow');
		});
	});
	
jQuery(document).ready(function($){
			$(".wc_ip_toggle_section").toggleClass('open').next().toggle('slow');
	});

