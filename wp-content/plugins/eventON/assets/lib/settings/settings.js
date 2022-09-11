/**
 * EventON Settings scripts
 * @version  4.1
 */
jQuery(document).ready(function($){

	init();

	function init(){
		// focusing on correct settings tabs
		var hash = window.location.hash;
		//console.log(hash);

		if(hash=='' || hash=='undefined'){
		}else{
			var hashId = hash.split('#');

			$('.nfer').hide();
			$(hash).show();

			var obj = $('a[data-c_id='+hashId[1]+']');
			change_tab_position(obj);
		}
	}

// Settings
	// webhooks
		$.fn.evo_webhooks = function (options){

			var init = function(){
				interaction();
			}
			var interaction = function(){
				$('body').on('click','.save_webhook_config',function(){
					var FORM = $(this).closest('form'),
					dataajax = {},
					LB = $('.evo_webhook_settings.ajde_admin_lightbox');
					
					FORM.ajaxSubmit({
						beforeSubmit: function(){	
							$('body').trigger('evo_show_loading_animation',['evo_webhook_settings','saving']);	
						},
						dataType: 	'json',
						url: 		the_ajax_script.ajaxurl,
						type: 	'POST',
						success:function(data){
							if( data.status == 'good'){
								$('body').trigger('ajde_lightbox_show_msg',[ data.msg, 'evo_webhook_settings','good',true]);
								$('body').find('#evowhs_container').html( data.html );
							}else{
								$('body').trigger('ajde_lightbox_show_msg',[ data.msg, 'evo_webhook_settings','bad']);
							}
						},
						complete:function(){
							$('body').trigger('evo_hide_loading_animation',['evo_webhook_settings']);
						}
					});
				});

				// delete
				$('body').on('click','.evowh_del',function(){
					wh_id = $(this).closest('p').data('id');
					
					var dataajax = {};
					dataajax['id']= $(this).closest('p').data('id');
					dataajax['action']= 'evo_webhook_delete';

					$.ajax({
						beforeSend: function(){ },
						url:	the_ajax_script.ajaxurl,
						data: 	dataajax,	dataType:'json', type: 	'POST',
						success:function(data){
							if( data.status == 'good'){
								$('body').find('#evowhs_container').html( data.html );
							}else{

							}
						},
						complete:function(){ 
						}
					});
				});

			}
			init();
		}
		$('#ajde_customization').evo_webhooks();

// Other
	// colpase menu
		$('.ajde-collapse-menu').on('click', function(){
			if($(this).hasClass('close')){
				$(this).parent().removeClass('mini');
				$('.evo_diag').removeClass('mini');
				$(this).removeClass('close');
			}else{
				$(this).parent().addClass('mini');
				$('.evo_diag').addClass('mini');
				$(this).addClass('close');
			}
		});

	// switching between tabs
		$('#acus_left').find('a').click(function(){

			var nfer_id = $(this).data('c_id');
			$('.nfer').hide();
			$('#'+nfer_id).show();
			
			change_tab_position($(this));

			window.location.hash = nfer_id;

			if(nfer_id=='evcal_002'){
				$('#resetColor').show();
			}else{
				$('#resetColor').hide();
			}
			
			return false;
			
		});

		// position of the arrow
		function change_tab_position(obj){

			// class switch
			$('#acus_left').find('a').removeClass('focused');
			obj.addClass('focused');

			var menu_position = obj.position();
			//console.log(obj);
			$('#acus_arrow').css({'top':(menu_position.top+3)+'px'}).show();
		}

		// RESET colors
		$('#resetColor').on('click',function(){
			$('.colorselector ').each(function(){
				var item = $(this).siblings('input');
				item.attr({'value': item.attr('default') });
			});
			
		});

	// color circle guide popup
		$('#ajde_customization .hastitle').hover(function(){
			var poss = $(this).position();
			var title = $(this).attr('alt');
			//alert(poss.top)
			$('#ajde_color_guide').css({'top':(poss.top-33)+'px', 'left':(poss.left+11)}).html(title).show();
			//$('#ajde_color_guide').show();

		},function(){
			$('#ajde_color_guide').hide();
		});

	// COLOR PICKER
	// @version 2.0
	
	// font awesome icons
		var fa_icon_selection = '';
		$('.faicon').on('click','i', function(){
			var poss = $(this).position();
			var CL = $(this).attr('class').replace('fa ','');

			$('.fa_icons_selection').find('li').removeClass('select');

			$('.fa_icons_selection').css({'top':(poss.top-220)+'px', 'left':(poss.left-174)}).fadeIn('fast');

			$('.fa_icons_selection').find('i.'+ CL).parent().addClass('select');

			fa_icon_selection = $(this);
		});

		//selection of new font icon
		$('.fa_icons_selection').on('click','li', function(){

			var icon = $(this).find('i').data('name');
			//console.log(icon)

			fa_icon_selection.attr({'class':'fa '+icon});
			fa_icon_selection.siblings('input').val(icon);

			$('.fa_icons_selection').fadeOut('fast');
		});
		// close with click outside popup box when pop is shown
		$(document).mouseup(function (e){
			var container=$('.fa_icons_selection');
			
				if (!container.is(e.target) // if the target of the click isn't the container...
				&& container.has(e.target).length === 0) // ... nor a descendant of the container
				{
					$('.fa_icons_selection').fadeOut('fast');
				}			
		});

	// hideable section
		$('body').on('click','.evo_hideable_show',function(){
			var O = $(this);
			var cv = O.html();
			O.html( O.data('t')).data('t', cv);
			
			const fc = O.parent().next('p.field_container');
			const I = fc.find('input');			
			I.attr('type') == 'password' ? I.attr('type','text') : I.attr('type','password');
		});
	
	// multicolor title/name display
		$('.row_multicolor').on('mouseover','em',function(){
			var name = $(this).data('name');
			$(this).closest('.row_multicolor').find('.multicolor_alt').html(name);
		});
		$('.row_multicolor').on('mouseout','em',function(){
			$(this).closest('.row_multicolor').find('.multicolor_alt').html(' ');
		});	
	
	//legend
		$('.legend_icon').hover(function(){
			$(this).siblings('.legend').show();
		},function(){
			$(this).siblings('.legend').hide();
		});
		
	// image
		if($('.ajt_choose_image').length>0){
			var _custom_media = true,
			_orig_send_attachment = wp.media.editor.send.attachment;
		}
		
		$('.ajt_choose_image').click(function() {
			var send_attachment_bkp = wp.media.editor.send.attachment;
			var button = $(this),
				imagesection = button.parent();

			//var id = button.attr('id').replace('_button', '');
			_custom_media = true;
			wp.media.editor.send.attachment = function(props, attachment){
				if ( _custom_media ) {
					//console.log(attachment);

					imagesection.find('.ajt_image_id').val(attachment.id);					
					imagesection.find('.ajt_image_holder img').attr('src',attachment.url);
					imagesection.find('.ajt_image_holder').fadeIn();
					button.fadeOut();

					//$("#"+id).val(attachment.url);
				} else {
					return _orig_send_attachment.apply( this, [props, attachment] );
				};
			}

			wp.media.editor.open(button);
			return false;
		});
		$('.add_media').on('click', function(){
			_custom_media = false;
		});

		// removre image
		$('.ajde_remove_image').click(function() {  
			imagesection = $(this).closest('p');
			imagesection.find('.ajt_image_id').val('');					
			imagesection.find('.ajt_image_holder').fadeOut();
			imagesection.find('.ajt_choose_image').fadeIn();
	        return false;  
	    });
	

	// hidden section
		$('.ajdeSET_hidden_open').click(function(){
			$(this).next('.ajdeSET_hidden_body').toggle();
			if( $(this).hasClass('open')){
				$(this).removeClass('open')
			}else{
				$(this).addClass('open');
			}
		});	

	// sortable		
		$('.ajderearrange_box').sortable({		
			update: function(e, ul){
				var sortedID = $(this).sortable('toArray',{attribute:'val'});
				BOX = $(this).closest('.ajderearrange_box');
				BOX.siblings('.ajderearrange_order').val(sortedID);
				update_card_hides( BOX );
			}
		});
		
		// hide sortables
			$('.ajderearrange_box').on('click','span',function(){
				$(this).toggleClass('hide');
				BOX = $(this).closest('.ajderearrange_box');
				update_card_hides( BOX );
			});

			function update_card_hides(BOX){
				hidethese = '';
				BOX.find('span').each(function(index){
					if(!$(this).hasClass('hide')){
						FIELDNAME = $(this).parent().attr('val');
						console.log(FIELDNAME);
						hidethese += FIELDNAME+',';
					}
				});

				BOX.siblings('.ajderearrange_selected').val(hidethese);
			}
		
	// at first run a check on list items against saved list -
		var items='';
		$('#ajdeEVC_arrange_box').find('p').each(function(){
			if($(this).attr('val')!='' && $(this).attr('val')!='undefined'){
				items += $(this).attr('val')+',';
			}
		});
		$('.ajderearrange_order').val(items);	
});