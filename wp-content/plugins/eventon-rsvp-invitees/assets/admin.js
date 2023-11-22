/**
 * ADMIN
 */
jQuery(document).ready(function($){	
	j_data = {};
	j_temp = {};

	// LOAD manager view
		$('.evorsi_invitee_mgr').on('click',function(){
			OBJ = $(this);
			var ajaxdataa = {};
			ajaxdataa['action']='evorsi_content';
			ajaxdataa['e_id'] = OBJ .data('eid');
			
			$.ajax({
				beforeSend: function(){ $('.evorsi_lightbox .ajde_popup_text').addClass('loading');	},	
				url:	evorsi_admin_ajax_script.ajaxurl,
				data: 	ajaxdataa,	dataType:'json', type: 	'POST',
				success:function(data){
					if(data.status=='good'){
						
						$('.evorsi_lightbox').find('.ajde_popup_text').html( data.content );

						// load other templates
						j_temp.json_invitee_form_temp = data.json_invitee_form_temp; // form
						j_temp.json_invitee_msgs_temp = data.json_invitee_msgs_temp; // messages

						// stats
						j_temp.json_stats_temp = data.json_stats_temp;
						_draw_stats( data.json_stats_data);

						// invitees						
						j_temp.json_invitee_rows_temp = data.json_invitee_rows_temp;
						_draw_invitee_rows( data.json_invitee_rows_data );


					}else{}
				},complete:function(){ $('.evorsi_lightbox .ajde_popup_text').removeClass('loading');	}
			});
		});

	// Invitee form
		$('body').on('evorsi_invitee_form',function(event,type,invitee_id, event_id){
			OBJ = $(this);			

			formdata = {};
			formdata['e_id'] = event_id;
			formdata['invitee_id'] = invitee_id;
			formdata['type'] = type;
			formdata['repeat_interval'] = '0';
			formdata['lang'] = 'L1';
			formdata['rsvp_type'] = 'invitee';			

			_temp = Handlebars.compile( j_temp.json_invitee_form_temp );
			Handlebars.registerHelper('ifCond',function(v1,operator, v2, options){
				return checkCondition(v1, operator, v2)
                    ? options.fn(this)
                    : options.inverse(this);
			});

			// if editing add invitee data
			if( type == 'edit'){
				I = j_data['rows'][invitee_id];
				$.each(I, function(index, val){
					formdata[ index] = val;
				});
			}else{
				formdata['count'] = '1';
			}

			//console.log(formdata);
			_html = _temp( formdata );

			$('.evorsi_lightbox_two').find('.ajde_popup_text').html( _html );
			$('.evorsi_lightbox_two').find('.ajde_popup_text').removeClass( 'loading' );

			load_messages( invitee_id );

			f = $('.evorsi_lightbox_two').find('.evorsi_invitee_form');
			f.on('click','.evorsi_form_number_change',function(){
				p = $(this).closest('p');
				c = parseInt(p.find('input').val());
				add = $(this).hasClass('plus')? true: false;

				c = add? c+1: c-1;
				c = c<1? 1: c;
				p.find('input').val( c );
				p.find('i').html( c );
			});				
		});
		// NEW
		$('body').on('click','.evorsi_new', function(){
			O = $(this);
			$('body').trigger('evorsi_invitee_form',['new','na',O.data('eid')] );
		});
		// edit
		$('body').on('click','i.evorsi_view',function(){
			O = $(this);
			$('body').trigger('evorsi_invitee_form',['edit',O.data('iid'),O.data('eid')] );
		});

	// resend invitation
		$('body').on('click','.evorsi_resend_invitation',function(){
			var F = $(this).closest('.evorsi_invitee_form');
			var LB = $('.evorsi_lightbox_two');

			
			var ajaxdataa = {};
			ajaxdataa['action']='evorsi_resent_invite';
			ajaxdataa['RR_id'] = F.find('input[name="invitee_id"]').val();
			$.ajax({
				beforeSend: function(){ LB.find('.ajde_popup_text').addClass('loading');	},	
				url:	evorsi_admin_ajax_script.ajaxurl,
				data: 	ajaxdataa,	dataType:'json', type: 	'POST',
				success:function(data){
					LB.find('.message').html( data.msg ).show().delay(5000).fadeOut(0);
				},complete:function(){ 
					LB.find('.ajde_popup_text').removeClass('loading');
				}
			});

		});

	// Load messages
		function load_messages(invitee_id){
			var ajaxdataa = {};
			ajaxdataa['action']='evorsi_get_msgs';
			ajaxdataa['invitee_id'] = invitee_id;
			
			$.ajax({
				beforeSend: function(){ $('.evorsi_lightbox_two .ajde_popup_text').addClass('loading');	},	
				url:	evorsi_admin_ajax_script.ajaxurl,
				data: 	ajaxdataa,	dataType:'json', type: 	'POST',
				success:function(data){
					if(data.status=='good'){						
						_draw_msgs( data.content );	
					}else{}
				},complete:function(){ 
					$('.evorsi_lightbox_two .ajde_popup_text').removeClass('loading');
				}
			});
		}

	// Send new message
		$('body').on('click','.evorsi_send_msg', function(){
			var ajaxdataa = {};
			F = $(this).closest('.evorsi_invitee_form');
			ajaxdataa['action']='evorsi_new_msg';
			ajaxdataa['invitee_id'] = F.find('input[name="invitee_id"]').val();
			ajaxdataa['m'] = F.find('.evorsi_msgs_msg').val();
			ajaxdataa['v'] = F.find('input[name="visibility"]').val();
			ajaxdataa['type'] = 'admin';
			ajaxdataa['end'] = 'back';
			
			$.ajax({
				beforeSend: function(){ $('.evorsi_lightbox_two .ajde_popup_text').addClass('loading');	},	
				url:	evorsi_admin_ajax_script.ajaxurl,
				data: 	ajaxdataa,	dataType:'json', type: 	'POST',
				success:function(data){
					if(data.status=='good'){						
						_draw_msgs( data.content.msg_data );
						F.find('.evprsi_admin_message_notice').html( data.content.msg ).addClass('show');	
						setTimeout(function(){ F.find('.evprsi_admin_message_notice').removeClass('show');}, 3000);
					}else{}
				},complete:function(){ 
					$('.evorsi_lightbox_two .ajde_popup_text').removeClass('loading');
				}
			});
		});
	// delete message
		$('body').on('click','i.evorsi_msg_d',function(){
			var ajaxdataa = {};
			F = $(this).closest('.evorsi_invitee_form');
			ajaxdataa['action']='evorsi_d_msg';
			ajaxdataa['invitee_id'] = F.find('input[name="invitee_id"]').val();
			ajaxdataa['i'] = $(this).closest('p').data('i');

			//remove
			$(this).closest('p').remove();
			
			$.ajax({
				beforeSend: function(){ $('.evorsi_lightbox_two .ajde_popup_text').addClass('loading');	},	
				url:	evorsi_admin_ajax_script.ajaxurl,
				data: 	ajaxdataa,	dataType:'json', type: 	'POST',
				success:function(data){
					if(data.status=='good'){						
						_draw_msgs( data.content );	
					}else{}
				},complete:function(){ 
					$('.evorsi_lightbox_two .ajde_popup_text').removeClass('loading');
				}
			});
		});

	// ADD NEW and EDIT invitee
		$('body').on('click','a.evorsi_form_submit', function(){
			O = $(this);
			var LB = $('.evorsi_lightbox_two');

			var ajaxdataa = {};
			ajaxdataa['action']='evorsi_form_submit';
			ajaxdataa['type'] = O.data('t');

			f = O.closest('.evorsi_invitee_form');
			f.find('.field').each(function(){
				ajaxdataa[ $(this).attr('name')] = $(this).val();
			});

			// validate
			var error = false;
			f.find('.field.req').each(function(){
				var V = $(this).val();
				if( V == '' || V === undefined) error = true;
			});

			if(error){
				LB.find('.message').html( 'Required Fields Missing!' ).show().delay(5000).fadeOut(0);
			}else{
				$.ajax({
					beforeSend: function(){ $('.evorsi_lightbox_two .ajde_popup_text').addClass('loading');	},	
					url:	evorsi_admin_ajax_script.ajaxurl,
					data: 	ajaxdataa,	dataType:'json', type: 	'POST',
					success:function(data){
						if(data.status=='good'){
							
							_draw_invitee_rows( data.json_invitee_rows_data );
							$('body').trigger('evoadmin_lightbox_hide',['evorsi_lightbox_two']);
		
						}else{}
					},complete:function(){ 

					}
				});
			}
		});		

	// functions
		function _draw_msgs(DATA){
			T = Handlebars.compile( j_temp.json_invitee_msgs_temp);
			Handlebars.registerHelper('ifCond',function(v1,operator, v2, options){
				return checkCondition(v1, operator, v2)
                    ? options.fn(this)
                    : options.inverse(this);
			});
			$('.evorsi_lightbox_two').find('.evorsi_form_custom_m').html( T( DATA) );
		}
		function _draw_stats(DATA){
			template = Handlebars.compile( j_temp.json_stats_temp );
			Handlebars.registerHelper('ifCond',function(v1,operator, v2, options){
				return checkCondition(v1, operator, v2)
                    ? options.fn(this)
                    : options.inverse(this);
			});
			$('.evorsi_lightbox').find('.evorsi_stats').html( template( DATA) );
		}
		function _draw_invitee_rows(DATA){
			j_data = DATA;
			template2 = Handlebars.compile( j_temp.json_invitee_rows_temp);
			Handlebars.registerHelper('ifCond',function(v1,operator, v2, options){
				return checkCondition(v1, operator, v2)
                    ? options.fn(this)
                    : options.inverse(this);
			});
			$('.evorsi_lightbox').find('.evorsi_list_rows').html( template2(DATA) );
		}

	// form custom data viewing
		$('body').on('click','.evorsi_form_custom a',function(){
			if($(this).hasClass('f')) return false;

			O = $(this);

			O.parent().find('a').removeClass('f');
			O.addClass('f');

			B = O.parent().siblings('.evorsi_form_custom_boxes');
			B.find('.evorsi_box').removeClass('f');
			B.find('.evorsi_box').eq( O.index() ).addClass('f');
		});

	// SUP
		function checkCondition(v1, operator, v2) {
	        switch(operator) {
	            case '==':
	                return (v1 == v2);
	            case '===':
	                return (v1 === v2);
	            case '!==':
	                return (v1 !== v2);
	            case '<':
	                return (v1 < v2);
	            case '<=':
	                return (v1 <= v2);
	            case '>':
	                return (v1 > v2);
	            case '>=':
	                return (v1 >= v2);
	            case '&&':
	                return (v1 && v2);
	            case '||':
	                return (v1 || v2);
	            default:
	                return false;
	        }
	    }
});