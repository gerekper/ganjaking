/**
 * Admin Script
 * @version  0.1
 */
jQuery(document).ready(function($){

	// user roles related
		$('body').on('evoau_show_saved_assigned_user',function(event, response){
			if(response.status=='good')
				$('.evoau_assigned_usersroles_in').html(response.content_aup);
		});

	// draggable
		load_sortability();

	// get form
		$('body').on('click','.evoaup_sl_form', function(){
			OBJ = $(this);
			var ajaxdataa = { };
				ajaxdataa['action']='evoaup_get_form';
				ajaxdataa['type']= OBJ.data('type');

			// for edit
			if(OBJ.data('type')=='edit'){
				ajaxdataa['index']=  OBJ.closest('li').data('cnt');
				ajaxdataa['edata']=  OBJ.closest('li').find('.data').data('var');
			}

			$.ajax({
				beforeSend: function(){
					block_type_name = 'Submission Level';
					text = OBJ.data('type')=='new'? 'Add New '+block_type_name:'Edit '+block_type_name;
					$('.evoaup_lightbox').find('.ajde_lightbox_title').html( text );
					$('.evoaup_lightbox').find('.ajde_popup_text').addClass( 'loading');
				},
				type: 'POST',
				url:evoaup_admin_ajax_script.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){						
						$('.evoaup_lightbox').find('.ajde_popup_text').html( data.content);
					}else{}
				},complete:function(){
					$('.evoaup_lightbox.ajde_admin_lightbox ').find('.ajde_popup_text').removeClass( 'loading');
				}
			});	
		});

	// form interaction
		// color picker
			$('body').on('click','.evoaup_color_i',function(){
				var O = $(this);
				$(this).ColorPicker({		
					color: O.css("background-color"),
					onShow: function (colpkr){
						$(this).addClass('open');
					},
					onChange:function(hsb, hex, rgb,el){
						set_hex_values(hex,O);
					},onSubmit: function(hsb, hex, rgb, el) {
						set_hex_values(hex, O);
						$(el).ColorPickerHide();
					}		
				});
				if( !$(this).hasClass('open')){
					$(this).trigger('click');
				}
			});
			function set_hex_values(hex, O){
				$(O).css({'background-color':'#'+hex});
				$(O).siblings('input').val( hex);
			}
	
	// save new form
		$('body').on('click','.evoaup_form_submission',function(){
			BTN = $(this);
			FORM = BTN.closest('.evoaup_submission_form');
			
			UL = $('body').find('ul.evoaup_submission_levels');

			FORM.find('.message').hide();

			req_check = true;

			FORM.find('.req').each(function(index){
				if($(this).val() == '' || $(this).val() === undefined) req_check = false;
			});


			if(req_check){
				index = 1;

				var ajaxdataa = {};
				CHECKVALS = FORM.find('.checkfields:checked').map(function(){
					return this.value;
				}).get();

				FORM.find('.input').each(function(index){
					if($(this).val() !=='') ajaxdataa[ $(this).attr('name')] = encodeURIComponent( $(this).val() );
				});

				// if saving edits override previos set
				if(BTN.data('type')=='edit'){
					index = BTN.data('index');
				}

				ajaxdataa['action']='evoaup_save_form';
				ajaxdataa['index'] = index;
				ajaxdataa['type'] = BTN.data('type');
				ajaxdataa['fields'] = CHECKVALS;
				
				$.ajax({
					beforeSend: function(){ 
						$('.evoaup_lightbox').find('.ajde_popup_text').addClass( 'loading');
					},					
					url:	evoaup_admin_ajax_script.ajaxurl,
					data: 	ajaxdataa,	dataType:'json', type: 	'POST',
					success:function(data){
						if(BTN.data('type')=='edit'){
							UL.find('li[data-cnt='+index+']').replaceWith(data.html);
						}else{
							if( UL.find('li').length==0){
								UL.html(data.html);
							}else{
								UL.append(data.html);
							}							
						}

						save_submission_levels( UL, $('.evoaup_lightbox').find('.ajde_popup_text'));

						$('.evoto_options_list').sortable();
						
						$('.evoaup_lightbox').find('.message').html( data.msg ).show();
					},complete:function(){ 	
						setTimeout(function () {
						   $('.ajde_close_pop_btn').trigger('click');
						}, 2000);					
						$('.evoaup_lightbox').find('.ajde_popup_text').removeClass( 'loading');
					}
				});
				
			}else{
				$('.evoaup_lightbox').find('.message').html('Missing required fields!').show();
			}
		});
	
	// delete a submission level
		$('.evoaup_submission_levels').on('click','em.delete',function(){
			if(confirm('Are you sure you want to delete?')){
				OBJ = $(this);
				LI = OBJ.closest('li');
				UL = LI.parent();

				LI.slideUp(function(){					
					LI.remove();	
					save_submission_levels(UL, UL, false);
				});
				if(LI.parent().find('li').length <= 1){
					UL.html("<p class='none'>You do not have any ticket "+ OBJ.data('method')+"!</p>");
				}
			}else{}
		});
		

	// load sortability
		function load_sortability(){
			if($('body').find('.evoaup_submission_levels').length>0){
				$('.evoaup_submission_levels').sortable({
					update:function(event, ui){
						save_submission_levels($(this), $(this), false);
					}
				});
			}
		}

	// save all submission levels
		function save_submission_levels(UL, LOADING_OBJ, closepopup ){

			if(UL.find('li').length==0){
				var ajaxdataa = {};
				ajaxdataa['action']='evoaup_delete_data';
				$.ajax({
					beforeSend: function(){ 
						LOADING_OBJ.addClass( 'evoloading');
					},					
					url:	evoaup_admin_ajax_script.ajaxurl,
					data: 	ajaxdataa,	dataType:'json', type: 	'POST',
					success:function(data){
						load_sortability();
					},complete:function(){ 										
						LOADING_OBJ.removeClass( 'evoloading');
					}
				});
			}else{
				var ajaxdataa = {};

				ajaxdataa['action']='evoaup_save_data';
				leveldata = 'leveldata';
				ajaxdataa[leveldata] = {};
				levelorder = 'levelorder';
				ajaxdataa[levelorder] = {};
				
				UL.find('li').each(function(index){
					if($(this).find('span.data').length>0){
						indexy = index+1;
						ajaxdataa[levelorder][indexy] = $(this).data('cnt');
						ajaxdataa[leveldata][ $(this).data('cnt') ] =  $.parseJSON($(this).find('span').attr('data-var'));					
					}
				});
				
				$.ajax({
					beforeSend: function(){ 
						LOADING_OBJ.addClass( 'evoloading');
					},					
					url:	evoaup_admin_ajax_script.ajaxurl,
					data: 	ajaxdataa,	dataType:'json', type: 	'POST',
					success:function(data){
						load_sortability();
					},complete:function(){ 	
						if(closepopup != false){
							setTimeout(function () {
							   $('.ajde_close_pop_btn').trigger('click');
							}, 2000);	
						}				
						LOADING_OBJ.removeClass( 'evoloading');
					}
				});
			}
			
		}
	
	
});