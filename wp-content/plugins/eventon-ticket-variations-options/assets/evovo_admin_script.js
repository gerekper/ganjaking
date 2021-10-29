/**
 * Admin Script
 * @version  0.1
 */
jQuery(document).ready(function($){
	
	// draggability
		$('.evovo_vo_list').sortable({
			update: function(event, ui){ 	change_order_update( $(this) );		}
		});

	// GET FORM
		$('body').on('click','.evovo_options_item', function(){
			OBJ = $(this);
			var ajaxdataa = { };
				ajaxdataa['action']='evovo_new_options_form';
				ajaxdataa['json']= OBJ.data('json');
				ajaxdataa['all_vo_data'] = $('body').find('.evovo_all_vo_data').data('all_vo_data');

			$.ajax({
				beforeSend: function(){
					text = OBJ.attr('title'); // pass button title attr as title for lightbox
					$('.evovo_lightbox').find('.ajde_lightbox_title').html( text );
					$('.evovo_lightbox').find('.ajde_popup_text').addClass( 'loading');
				},
				type: 'POST',
				url:evovo_admin_ajax_script.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){						
						$('.evovo_lightbox').find('.ajde_popup_text').html( data.content);

					}else{}
				},complete:function(){
					$('.evovo_lightbox.ajde_admin_lightbox ').find('.ajde_popup_text').removeClass( 'loading');
				}
			});	
		});

	// populate with other passed values
		$('body').on('click','.evovo_vt_popupate_with',function(){
			var O = $(this);
			const form = O.closest('.evovo_add_block_form');

			form.find('.input[name="name"]').val( O.data('vn'));
			form.find('.input[name="options"]').val( O.data('vos'));
		});

	// save new data set
		$('body').on('click','.evovo_form_submission',function(){
			BTN = $(this);
			OBJ = BTN.closest('.evovo_add_options_form');
			
			MSG = BTN.closest('.ajde_admin_lightbox').find('.message');
			MSG.hide();

			req_check = true;

			OBJ.find('.req').each(function(index){
				if($(this).val() == '' || $(this).val() === undefined) req_check = false;
			});

			if(req_check){
				index = '';

				var ajaxdataa = {};

				// for each input field add to ajax data
				OBJ.find('.input').each(function(index){
					if($(this).val() !=='') ajaxdataa[ $(this).attr('name')] = encodeURIComponent( $(this).val() );
				});

				// if saving edits override previos set
				if(BTN.data('type')=='edit'){
					index = BTN.data('index');
				}

				ajaxdataa['action']='evovo_save_dataset';
				ajaxdataa['index'] = index;
				ajaxdataa['json'] = BTN.data('json');
				ajaxdataa['all_vo_data'] = $('body').find('.evovo_all_vo_data').data('all_vo_data');

				UL = $('#evovo_options_selection').find('ul.evovo_'+ ajaxdataa.json.method +'_list');
				
				$.ajax({
					beforeSend: function(){ 
						$('.evovo_lightbox').find('.ajde_popup_text').addClass( 'loading');
					},					
					url:	evovo_admin_ajax_script.ajaxurl,data: ajaxdataa, dataType:'json', type: 	'POST',
					success:function(data){
						// append to other items
						// NON Event
						$('body').find('.evovo_vos_container'+ '_'+ajaxdataa.json.parent_type).show().html( data.html );
						
						$('.evovo_vo_list').sortable({
							update: function(event, ui){ 	change_order_update( $(this));		}
						});						
						MSG.html( data.msg ).show();

						// populate all vo data to data holder
						$('body').find('.evovo_all_vo_data').data('all_vo_data', data.all_vo_data);

						$('body').trigger('evovo_admin_voform_submitted',[data, BTN]);

					},complete:function(){ 	
						setTimeout(function () {
							$('.evovo_lightbox').find('.ajde_close_pop_btn').trigger('click');
						}, 2000);					
						$('.evovo_lightbox').find('.ajde_popup_text').removeClass( 'loading');
					}
				});				
			}else{
				MSG.html('Missing required fields!').show();
			}
		});

	// save changed order or variations and options
		function change_order_update(OO){
			var ajaxdataa = {};

			var new_order = {};
			new_order['variation'] = {};
			new_order['option'] = {};

			const container = OO.closest('.evovo_vos_container');

			ajaxdataa['action']='evovo_save_neworder';
			ajaxdataa['parent_id']= container.data('pid');
			ajaxdataa['eid']= container.data('eid');
			ajaxdataa['wcid']= container.data('wcid');
			ajaxdataa['parent_type']= container.data('pt');
			ajaxdataa['all_vo_data'] = $('body').find('.evovo_all_vo_data').data('all_vo_data');

			$('#evovo_options_selection').find('ul.evovo_variation li').each(function(index){
				new_order['variation'][index] = $(this).data('cnt');
			});

			ajaxdataa['data']= new_order;

			$.ajax({
				beforeSend: function(){
					container.addClass( 'evoloading');
				},
				type: 'POST',
				url:evovo_admin_ajax_script.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){}else{}
				},complete:function(){
					container.removeClass( 'evoloading');
				}
			});	

		}



	// delete an item
		$('body').on('click','.evovo_vo_list li em.delete',function(){

			if(!confirm('Are you sure you want to delete?')) return false;

			OBJ = $(this);
			LI = OBJ.closest('li');
			UL = OBJ.closest('ul');
			var ajaxdataa = { };
				ajaxdataa['action']='evovo_delete_item';
				ajaxdataa['json']= OBJ.data('json');
				ajaxdataa['all_vo_data'] = $('body').find('.evovo_all_vo_data').data('all_vo_data');

			// for temporary added vo items
			if( ajaxdataa.json['type'] == 'temp'){
				LI.remove();

				if( ajaxdataa.all_vo_data !== undefined){

					var method = ajaxdataa.json.method;
					var vo_id = ajaxdataa.json.vo_id;
					delete ajaxdataa.all_vo_data[method][vo_id];

					$('body').find('.evovo_all_vo_data').data('all_vo_data', ajaxdataa.all_vo_data);
				}
			// delete item from the event
			}else{
				$.ajax({
					beforeSend: function(){	UL.addClass('evoloading');	},
					type: 'POST',url:evovo_admin_ajax_script.ajaxurl,data: ajaxdataa,dataType:'json',
					success:function(data){},
					complete:function(){	UL.removeClass('evoloading');	}
				});	

				LI.slideUp(function(){		LI.remove();	});
			}			
		});


});