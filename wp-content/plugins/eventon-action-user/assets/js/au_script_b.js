/**
 *  ActionUser Admin Script
 */
jQuery(document).ready(function($){

	// Load assigned users data to lightbox
		$('body').on('click','.evoau_load_lightbox_content', function(){
			var data_arg = {
				action: 		'evoau_load_assigned_users',
				e_id:			$(this).data('eventid'),
			};
			$.ajax({
				beforeSend: function(){},
				type: 'POST',
				url:evoau_ajax_script.ajaxurl,
				data: data_arg,	dataType:'json',
				success:function(data){
					if(data.status=='good'){
						$('.evoau_lightbox_assign ').find('.ajde_popup_text').html(data.content);
					}else{
						$('.evoau_lightbox_assign ').find('.ajde_popup_text').html('Could not gather required information!');
					}
				},complete:function(){}
			});

		});

	// Save assigned user data
		$('body').on('click','.evoau_save_assigned_user_data',function(event){
			event.preventDefault();
			OBJ = $(this);
			FORM = OBJ.siblings('form');
			LIGHTBOX = OBJ.closest('.ajde_popup');

			FORM.ajaxSubmit({
				beforeSubmit: function(){
					OBJ.closest('.ajde_popup_text').addClass('loading');
				},
				dataType:'json',type:'POST',
				url:evoau_ajax_script.ajaxurl,
				success:function(responseText, statusText, xhr, $form){
					if(responseText.status=='good'){
						$('.evoau_assigned_users_in').html(responseText.content);
						$('body').trigger('evoau_show_saved_assigned_user', [responseText]);
					}										
				},complete: function(){ OBJ.closest('.ajde_popup_text').removeClass('loading');
					LIGHTBOX.find('.ajde_close_pop_btn').trigger('click');
				}
			});	

		});

	// selecting all option from assign users
		$('.evoau_lightbox_assign').on('click','.evoau_assign_selection input',function(){
			OBJ = $(this);
			val = OBJ.val();
			ALLField = OBJ.closest('.evoau_assign_selection').find('input[value="all"]');

			if(val == 'all'){
				if(OBJ.is(':checked')){
					OBJ.closest('.evoau_assign_selection').find('input').prop('checked',true);
				}else{
					OBJ.closest('.evoau_assign_selection').find('input').prop('checked',false);
				}
			}else{
				if(ALLField.is(':checked')){
					ALLField.prop('checked',false);
				}
			}
		});
	
});