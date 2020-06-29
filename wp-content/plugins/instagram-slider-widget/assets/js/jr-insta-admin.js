(function($) {
	
	$(document).ready(function($){

		// Hide Custom Url if image link is not set to custom url
		$('body').on('change', '.jr-container select[id$="images_link"]', function(e){
			var images_link = $(this);
			if ( images_link.val() != 'custom_url' ) {
				images_link.closest('.jr-container').find('input[id$="custom_url"]').val('').parent().animate({opacity: 'hide' , height: 'hide'}, 200);
			} else {
				images_link.closest('.jr-container').find('input[id$="custom_url"]').parent().animate({opacity: 'show' , height: 'show'}, 200);
			}
		});

		// Modify options based on template selections
		$('body').on('change', '.jr-container select[id$="template"]', function(e){
			var template = $(this);
			if ( template.val() == 'thumbs' || template.val() == 'thumbs-no-border' ) {
				template.closest('.jr-container').find('.jr-slider-options').animate({opacity: 'hide' , height: 'hide'}, 200);
				template.closest('.jr-container').find('input[id$="columns"]').closest('p').animate({opacity: 'show' , height: 'show'}, 200);
			} else {
				template.closest('.jr-container').find('.jr-slider-options').animate({opacity: 'show' , height: 'show'}, 200);
				template.closest('.jr-container').find('input[id$="columns"]').closest('p').animate({opacity: 'hide' , height: 'hide'}, 200);
			}
		});

		// Modfiy options when search for is changed
		$('body').on('change', '.jr-container input:radio[id$="search_for"]', function(e){
			var search_for = $(this);
			if ( search_for.val() != 'username' ) {
				search_for.closest('.jr-container').find('[id$="attachment"]:checkbox').closest('p').animate({opacity: 'hide' , height: 'hide'}, 200);
				search_for.closest('.jr-container').find('select[id$="images_link"] option[value="user_url"]').animate({opacity: 'hide' , height: 'hide'}, 200);			
				search_for.closest('.jr-container').find('select[id$="images_link"] option[value="attachment"]').animate({opacity: 'hide' , height: 'hide'}, 200);
				search_for.closest('.jr-container').find('select[id$="images_link"]').val('image_link');				
				search_for.closest('.jr-container').find('select[id$="description"] option[value="username"]').animate({opacity: 'hide' , height: 'hide'}, 200);
				search_for.closest('.jr-container').find('input[id$="blocked_users"]').closest('p').animate({opacity: 'show' , height: 'show'}, 200);

			} else {
				search_for.closest('.jr-container').find('[id$="attachment"]:checkbox').closest('p').animate({opacity: 'show' , height: 'show'}, 200);				
				search_for.closest('.jr-container').find('select[id$="images_link"] option[value="user_url"]').animate({opacity: 'show' , height: 'show'}, 200);			
				search_for.closest('.jr-container').find('select[id$="images_link"] option[value="attachment"]').animate({opacity: 'show' , height: 'show'}, 200);		
				search_for.closest('.jr-container').find('select[id$="images_link"]').val('image_link');
				search_for.closest('.jr-container').find('select[id$="description"] option[value="username"]').animate({opacity: 'show' , height: 'show'}, 200);
				search_for.closest('.jr-container').find('input[id$="blocked_users"]').closest('p').animate({opacity: 'hide' , height: 'hide'}, 200);

			}
		});
	
		// Hide blocked images if not checked attachments
		$('body').on('change', '.jr-container [id$="attachment"]:checkbox', function(e){
			var attachment = $(this);
			if ( this.checked ) {
				attachment.closest('.jr-container').find('select[id$="images_link"] option[value="attachment"]').animate({opacity: 'show' , height: 'show'}, 200);
				attachment.closest('.jr-container').find('select[id$="images_link"]').val('image_link');
			} else {
				attachment.closest('.jr-container').find('select[id$="images_link"] option[value="attachment"]').animate({opacity: 'hide' , height: 'hide'}, 200);				
				attachment.closest('.jr-container').find('select[id$="images_link"]').val('image_link');
			}
		});

		// Toggle advanced options
		$('body').on('click', '.jr-advanced', function(e){
			e.preventDefault();
			var advanced_container = $(this).parent().next();
			
			if ( advanced_container.is(':hidden') ) {
				$(this).html('[ - Close ]');
			} else {
				$(this).html('[ + Open ]');
			}
			advanced_container.toggle();
		});	
		
		// Remove blocked images with ajax
		$('body').on('click', '.jr-container .jr-delete-instagram-dupes', function(e){
			e.preventDefault();
			var $this  = $(this),
				username  = $(this).data("username"),
				ajaxNonce = $(this).closest('.jr-container').find('input[name=delete_insta_dupes_nonce]').val();

			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action: 'jr_delete_insta_dupes',
					username : username,
					_ajax_nonce: ajaxNonce
				},
				beforeSend: function () {
					$this.prop('disabled', true);
					$this.closest('.jr-container').find('.jr-spinner').addClass( 'spinner' ).css({'visibility':'visible','float':'none'});
				},
				success: function(data, textStatus, XMLHttpRequest) {
					$this.closest('.jr-container').find('.deleted-dupes-info').text( 'Removed Duplicates: '+  data.deleted);
				},
				complete: function () {
					$this.prop('disabled', false);
					$this.closest('.jr-container').find('.jr-spinner').addClass( 'spinner' ).css({'visibility':'hidden','float':'none'});
				},				
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					//console.log(XMLHttpRequest.responseText);
				}
			});
		});

	}); // Document Ready

})(jQuery);