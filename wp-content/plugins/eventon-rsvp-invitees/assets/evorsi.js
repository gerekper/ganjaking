/**
 * 
 */
jQuery(document).ready(function($){	

	json_temp = {};

	// when single event content loaded
		$('body').on('evo_single_event_content_loaded', function(event, data, obj){
			if( !data.json_temp_evorsi_wall) return false;

			json_temp.wall = data.json_temp_evorsi_wall;
			json_temp.notice = data.json_temp_evorsi_notice;
			T = Handlebars.compile(data.json_temp_evorsi_wall);
			W = obj.find('.evorsi_message_wall');
			W.html( T(data.mm) );

			hide_wall_messages( W );
		});

	// guest posting a message
		$('body').on('click','.evorsi_send_msg',function(){
			F = $(this).closest('.evorsi_msg_form');
			ajaxdataa = {};
			ajaxdataa['action']='evorsi_new_msg';
			ajaxdataa['invitee_id'] = F.find('input[name="iid"]').val();
			ajaxdataa['event_id'] = F.find('input[name="eid"]').val();
			ajaxdataa['m'] = F.find('.evorsi_msgs_msg').val();
			ajaxdataa['v'] = F.find('input[name="visibility"]').val();
			ajaxdataa['type'] = 'guest';
			ajaxdataa['end'] = 'front';

			W = $(this).closest('.evorsi_message').find('.evorsi_message_wall');
			T = Handlebars.compile( json_temp.wall );

			F.find('.evorsi_msgs_msg').val('');
			if(!F.find('.ajde_yn_btn').hasClass('NO')) F.find('.ajde_yn_btn').trigger('click');

			if(ajaxdataa.v == 'yes'){
				M = {};
				M.msgs = {};
				n = Date.now();
				M.msgs[n] = {};
				M.msgs[n]['t'] = ajaxdataa.m;
				M.msgs[n]['tm'] = 'now';

				W.append( T( M ));
			}
			
			$.ajax({
				beforeSend: function(){ 	},	
				url:	evorsi_ajax_script.ajaxurl,
				data: 	ajaxdataa,	dataType:'json', type: 	'POST',
				success:function(data){
					T2d = {};
					if(data.status=='good'){												
						W.html( T( data.content.msg_data ));
						hide_wall_messages( W );
						
						T2d['text'] = data.content.msg;
						
					}else{
						T2d['text'] = data.content.msg;
						T2d['error'] = 'error';
					}

					T2 = Handlebars.compile( json_temp.notice );
					F.find('textarea').after( T2( T2d ));
					setTimeout(function(){ F.find('.evorsi_send_msg_notice').hide();}, 3000);
				},complete:function(){ 	}
			});
		});

	function hide_wall_messages(W){
		if(W.data('s') == 'all' ) return false;
		Ms = W.find('p').length;

		if(Ms> W.data('s')){
			W.find('p').each(function(index){
				if((index+1) > W.data('s')) $(this).hide();
			});

			W.append( '<span class="evorsi_more">...</span>');
		}
	}

	// show more messages when clicked
		$('body').on('click', 'span.evorsi_more', function(){			
			$(this).parent().find('p').show();
			$(this).parent().data('s','all');
			$(this).remove();
		});
});