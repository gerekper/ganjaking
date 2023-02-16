/**
 * Admin Script
 * @version  0.1
 */
jQuery(document).ready(function($){
	// date and time picker
		var date_format = $('#evcal_dates').attr('date_format');
		var time_format = ($('body').find('input[name=_evo_time_format]').val()=='24h')? 'H:i':'h:i:A';

	
	// add new item form
		$('body').on('click','.evodp_block_item', function(){
			OBJ = $(this);
			var ajaxdataa = { };
				ajaxdataa['action']='get_form';
				ajaxdataa['type']= OBJ.data('type');
				ajaxdataa['eid']=  OBJ.data('eid');
				ajaxdataa['block']=  OBJ.data('block');
				ajaxdataa['mprice_status']=  $('body').find('input[name=_evodp_member_pricing]').val()

			// for edit
			if(OBJ.data('type')=='edit'){
				ajaxdataa['time_format']=  time_format;
				ajaxdataa['index']=  OBJ.closest('li').data('cnt');

				if(ajaxdataa.block=='una'){				
					OBJ.closest('li').find('input').each(function(index){
						name = index==0? 'start':'end';
						ajaxdataa[ name]=  $(this).val();
					});
				}else{
					OBJ.closest('li').find('input').each(function(index){
						if(index==0) name = 'start';
						if(index==1) name = 'end';
						if(index==2) name = 'price';
						if(index==3) name = 'mprice';
						ajaxdataa[ name]=  $(this).val();
					});
				}
			}

			$.ajax({
				beforeSend: function(){
					block_type_name = ajaxdataa.block == 'una'? 'Unavailable Block': 'Time Based Price Block';
					text = OBJ.data('type')=='new'? 'Add New '+block_type_name:'Edit '+block_type_name;
					$('.evodp_lightbox').find('.ajde_lightbox_title').html( text );
					$('.evodp_lightbox').find('.ajde_popup_text').addClass( 'loading');
				},
				type: 'POST',
				url:evodp_admin_ajax_script.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){						
						$('.evodp_lightbox').find('.ajde_popup_text').html( data.content);

						$('.evodp_lightbox').find('input[name=sd]').datepicker({dateFormat: date_format});
						$('.evodp_lightbox').find('input[name=ed]').datepicker({dateFormat: date_format});
						$('.evodp_lightbox').find('input[name=st]').timepicker({'step': 5,'timeFormat':time_format});
						$('.evodp_lightbox').find('input[name=et]').timepicker({'step': 5,'timeFormat':time_format});
					}else{}
				},complete:function(){
					$('.evodp_lightbox').find('.ajde_popup_text').removeClass( 'loading');
				}
			});	
		});
	
	// save new time based pricing blcok
		$('body').on('click','.evodp_form_submission',function(){
			BTN = $(this);
			OBJ = BTN.closest('.evodp_item_block_container');
			UL = $('body').find('ul.evodp_blocks_'+BTN.data('block'));

			OBJ.find('.message').hide();

			if(
				OBJ.find('input[name=sd]').val() &&
				OBJ.find('input[name=st]').val() &&
				OBJ.find('input[name=ed]').val() &&
				OBJ.find('input[name=et]').val() &&
				( (BTN.data('type') == 'tbp' && OBJ.find('input[name=p]').val() ) || (BTN.data('type') != 'tbp'))
			){
				if( UL.find('li').length > 0){
					index = parseInt(UL.find('li:last-child').data('cnt'))+1;	
				}else{	index = 1;	}

				var ajaxdataa = {};
				OBJ.find('input').each(function(index){
					if($(this).val() !=='') ajaxdataa[ $(this).attr('name')] = encodeURIComponent( $(this).val() );
				});

				// if saving edits
				if(BTN.data('type')=='edit'){
					index = BTN.data('index');
				}

				ajaxdataa['action']='add_new_time_block';
				ajaxdataa['index'] = index;
				ajaxdataa['eid'] = BTN.attr('data-eid');
				ajaxdataa['type'] = BTN.data('type');
				ajaxdataa['block_key'] = BTN.data('bkey');
				ajaxdataa['block'] = BTN.data('block');
				ajaxdataa['date_format'] = $('input[name=_evo_date_format]').val();
				ajaxdataa['date_format'] = $('input[name=_evo_date_format]').val();
				ajaxdataa['time_format'] = ($('body').find('input[name=_evo_time_format]').val()=='24h')? 'H:i':'h:i:A';
				
				$.ajax({
					beforeSend: function(){ 
						$('.evodp_lightbox').find('.ajde_popup_text').addClass( 'loading');
					},					
					url:	evodp_admin_ajax_script.ajaxurl,
					data: 	ajaxdataa,	dataType:'json', type: 	'POST',
					success:function(data){
						if(BTN.data('type')=='edit'){
							UL.find('li[data-cnt='+index+']').replaceWith(data.html);
						}else{
							if(index==1){
								UL.html(data.html);
							}else{
								UL.append(data.html);
							}							
						}
						
						OBJ.find('.message').html( data.msg ).show();
					},complete:function(){ 	
						setTimeout(function () {
						   $('.ajde_close_pop_btn').trigger('click');
						}, 2000);					
						$('.evodp_lightbox').find('.ajde_popup_text').removeClass( 'loading');
					}
				});

				
			}else{
				OBJ.find('.message').html('Missing required fields!').show();
			}
		});
	
	// delete a time based price block
		$('.evodp_blocks_list').on('click','li em.delete',function(){
			LI = $(this).closest('li');
			LI.slideUp(function(){	LI.remove();	});
		});


	// enable disable special member prices
		$('#_evodp_member_pricing').on('click',function(){
			TD = $(this).closest('td');
			if(!$(this).hasClass('NO') ){
				TD.addClass('nomp');
			}else{
				TD.removeClass('nomp');
			}
		});

});