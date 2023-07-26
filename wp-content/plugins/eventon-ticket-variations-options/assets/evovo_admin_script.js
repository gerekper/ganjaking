/**
 * Admin Script
 * @version  1.1
 */
jQuery(document).ready(function($){
	
	// draggability
		$('body').on('evovo_trig_sortable', function(){
			$('body').find('.evovo_vo_list').each(function(){
				if( $(this).hasClass('evovo_variation_type_event')) return;
				$(this).sortable({
					update: function(event, ui){ 	change_order_update( $(this) );		}
				});
			});
		});
		
	// when vo list updated
		$('body').on('evo_ajax_success_evovo_save_vo_form',function(){
			$('body').trigger('evovo_trig_sortable');
		});
		$('body').on('evo_ajax_success_evovo_settings',function(){
			$('body').trigger('evovo_trig_sortable');
		});


	// populate with other passed values
		$('body').on('click','.evovo_vt_popupate_with',function(){
			var O = $(this);
			const form = O.closest('.evovo_add_block_form');

			form.find('.input[name="name"]').val( O.data('vn'));
			form.find('.input[name="options"]').val( O.data('vos'));
		});

	// save new data set
		$('body').on('click','.evovo_form_submission',function(event){
			event.preventDefault();

			BTN = $(this);
			LB = $(this).closest('.evo_lightbox');
			OBJ = BTN.closest('.evovo_add_options_form');
			
			LB.evo_lightbox_hide_msg();

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

				// submit the form
				BTN.evo_ajax_lightbox_form_submit( BTN.data('d') );

				return;
			
			}else{
				LB.evo_lightbox_show_msg({
					'type':'bad','message': 'Missing required fields!'
				});
			}
		});

	// save changed order or variations and options
		function change_order_update(OO){
			var ajaxdataa = {};

			var new_order = {};
			new_order['variation'] = {};
			new_order['option'] = {};

			LB = OO.closest('.evo_lightbox');
			const container = OO.closest('.evovo_vos_container');

			ajaxdataa['action']='evovo_save_neworder';
			ajaxdataa['parent_id']= container.data('pid');
			ajaxdataa['d']= container.data('d');

			LB.find('.evovo_option li').each(function(index){
				new_order['option'][index] = $(this).data('cnt');
			});
			LB.find('.evovo_variation li').each(function(index){
				new_order['variation'][index] = $(this).data('cnt');
			});

			ajaxdataa['data']= new_order;

			$(this).evo_admin_get_ajax({
				'lightbox_key': LB.data('lbc'),
				'ajaxdata':ajaxdataa,
				//'lightbox_key':'evost_lightbox_secondary',
				'uid':'evovo_rearrange',
				'load_new_content':false,
				'hide_lightbox':false
			});	
		}


	// delete an item
		$('body').on('click','.evovo_vo_list li em.delete',function(){
			if(!confirm('Are you sure you want to delete?')) return false;
			return;			
		});


});