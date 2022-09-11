/**
 * Shortcode Generator
 * @version 4.0.6
 */
jQuery(document).ready(function($){

 	var CODE = 'ajde';

	var data = {
		type: '',
		other_id: '',
		sc: 'add_eventon',		
		base:'add_eventon',
		vars: {}
	};
	var shortcode;
	var shortcode_vars = [];
	var shortcode_keys = new Array();
	var ss_shortcode_vars = new Array();

	var generator = $('#ajdePOSH_outter');


	// trigger actions
		$('body').on('evo_open_shortcode_generator',function(event, sc, type, other_id){
			$('body').trigger('evo_open_admin_lightbox',['eventon_shortcode']); 


			//reset 
			reset_sc();

			_process_sc( sc );			
			data.type = type;
			data.other_id = other_id;
			goto_inside_step();
		});

		function _process_sc(sc){	


			sc = sc.replace('[','');
			sc = sc.replace(']','');

			var s1 = sc.split(' ');

			if( s1 == ''){
				data.base = data.sc = 'add_eventon';
				return;
			}


			$.each(s1, function(f,v){

				if( f == 0){
					data.base = data.sc = v;				
				}else{

					var s2 = v.split('=');

					if( v === undefined || v == '') return;
					if( s2[0] == undefined || s2[0] === undefined) return;
					if( s2[1] == undefined ) return;

					var vv = s2[1].replace(/"/g,'');
					vv = vv.replace("'",'');
					vv = vv.replace("]",'');

					data.vars[ s2[0] ]  = vv;
				}
			});
		}

	// 2ND Step
		function goto_inside_step(force = false){
			var code_base = data.base;

			if( Object.keys( data.vars ).length > 0 || force){

				var step = generator.find('.step2_in[data-code="'+ code_base +'"]');
				if( step.length>0){
					generator.find('.step2').show();
					step.show();

					if($('body').hasClass('rtl')){
						$('.ajdePOSH_inner').animate({'margin-left':'-600px'},'fast');
					}else{
						$('.ajdePOSH_inner').animate({'margin-left':'-600px'},'fast');
					}
									
					ajdePOSH_show_back_btn();

					var section_name = generator.find('.ajdePOSH_btn[code="'+ code_base +'"]').html();
					
					$('#ajdePOSH_subtitle').html(section_name);
				}
			}
			
			select_sc_vars();
			refresh_sc();
		}
		function select_sc_vars(){
			if( Object.keys( data.vars ).length > 0){
				$.each( data.vars , function( ff, vv){
					//console.log( ff+' '+vv);
					// yes no fields
					if(vv == 'yes'){
						generator.find('.ajde_yn_btn[codevar="'+ ff+'"]').removeClass('NO');
						if(generator.find('.ajde_yn_btn[codevar="'+ ff+'"]').parent().hasClass('trig_afterst') ){
							generator.find('.ajde_afterst.'+ff).show();
						}
					} 
					if(vv == 'no'){
						generator.find('.ajde_yn_btn[codevar="'+ ff+'"]').addClass('NO');
						if(generator.find('.ajde_yn_btn[codevar="'+ ff+'"]').parent().hasClass('trig_afterst') ){
							generator.find('.ajde_afterst.'+ff).hide();
						}
					}


					if(generator.find('input.ajdePOSH_input[codevar="'+ ff+'"]').length>0){
						generator.find('input.ajdePOSH_input[codevar="'+ ff+'"]').val( vv );
					}

					if(generator.find('.ajdePOSH_select[codevar="'+ ff+'"]').length>0){
						generator.find('.ajdePOSH_select[codevar="'+ ff+'"]').val( vv );
					}

					if(generator.find('.ajdePOSH_select_step[codevar="'+ ff+'"]').length>0){
						generator.find('.ajdePOSH_select_step[codevar="'+ ff+'"]').val( vv );
						generator.find('.ajde_open_ss.select_step_'+ vv).show();
					}
				});
			}else{
				generator.find('.ajde_yn_btn').addClass('NO');
				generator.find('input.ajdePOSH_input').val( '' );
			}
		}

	// Interactions
		// copy shortcode
			$('#ajdePOSH_code').on('click',function(){
				const COP_VAL = $(this).html();

				var $temp = $("<input>");
			    $("body").append($temp);
			    $temp.val( COP_VAL ).select();
			    document.execCommand("copy");
			    $temp.remove();

			    $(this).fadeOut(200)
			    	.queue(function(n){
			    		$(this).html( 'Shortcode Copied!'); n();
			    	})
			    	.fadeIn(200)
			    	.delay(2000)
			    	.fadeOut(200)
			    	.queue(function(n){
			    		$(this).html( COP_VAL); n();
			    	})
			    	.fadeIn(200);
			});
		// collapsable fields
			generator.on('click','.fieldline.collapsable',function(){
				$(this).next('.collapsable_fields').toggle();
				$(this).toggleClass('closed');
				console.log('t');
			});

		// click on back button
			$('#ajdePOSH_back').click(function(){	
				backto_one( $(this) );
			});
		// click on main shortcodes
			generator.on('click','.ajdePOSH_btn',function(){
				data.base = data.sc = $(this).attr('code');		
				data.vars = {};	
				goto_inside_step(true);			
			});
		// afterstatements within shortcode gen
			$('.ajdePOSH_inner').on('click', '.trig_afterst',function(){
				$(this).next('.ajde_afterst').toggle();
			});
		// select field with child fields
			$('.ajdePOSH_inner').on('change','.ajdePOSH_select_step', function(){
				var value = $(this).val();
				var codevar = $(this).data('codevar');
				child = $(this).closest('.step2_in').find('.ajde_open_ss.select_step_'+value);
				
				child.siblings('.ajde_open_ss').hide();
				child.delay(300).show();

				// update the current shortcode based on selection
				if(value=='ss_1') value = '';
				data.vars[ codevar ] = (value!='' && value!='undefined') ? value:'';	

				refresh_sc();
			});
		// yes no buttons
			$('.'+CODE+'POSH_inner').on('click','.ajde_yn_btn', function(){
				var obj = $(this);
				var codevar = $(this).attr('codevar');				
				var value = obj.hasClass('NO') ? 'yes':'no';

				data.vars[ codevar ] = value;			
				refresh_sc();
			});
		
		// when fields changed
			$('.'+CODE+'POSH_inner').on('change','.'+CODE+'POSH_input, .'+CODE+'POSH_select, .'+CODE+'POSH_select_step', function(){				
				var obj = $(this);
				var value = obj.val();
				var codevar = obj.attr('codevar');
				if(codevar === undefined) codevar = obj.data('codevar');

				if( codevar == 'undefined' || codevar === undefined) return;

				data.vars[ codevar ] = (value!='' && value!='undefined') ? value:'';		
				refresh_sc();		
			});
		
		// tax term selection
			$('.ajdePOSH_inner').on('click','.ajdePOSH_tax', function(){
				var O = $(this);
				var D = O.data('d');
				INPUT = O.siblings('input');

				// Get previously set values
					IV = INPUT.val();
					if(IV !== undefined) IV = IV.split(",");

				_H = '';
				$.each(D, function(i,v){
					S = ( Array.isArray(IV) && IV.includes(i))? 'fa-circle': 'fa-circle-o';
					C = ( Array.isArray(IV) && IV.includes(i))? 'select':'';
					_H += "<em class='POSH_S2_box_item "+C+"' data-id='"+i+"'><i class='fa "+S+"'></i>"+v+"</em>";
				});

				// Prepend sub inside box into the shortcode generator lightbox
				$(this).closest('.step2_in').prepend( "<span class='POSH_S2_box ajde_close_elm'><span class='POSH_S2_box_title'>"+ INPUT.attr('title') +"<a class='ajde_close_btn dark' data-remove='yes'>X</a></span><span class='POSH_S2_box_in' data-par='"+ INPUT.attr('codevar') +"'>"+ _H +"</span></span>");
			});	

		// click on a tax term item 
			$('.ajdePOSH_inner').on('click', '.POSH_S2_box_item',function(){	
				var O = $(this);		
				if(O.hasClass('select')){
					O.removeClass('select').find('i').attr('class','fa fa-circle-o');
				}else{
					O.addClass('select').find('i').attr('class','fa fa-circle');
				}

				// GET new values
				V = '';
				O.parent().find('em').each(function(){
					if(!($(this).hasClass('select')) ) return;
					V += $(this).data('id')+',';
				});
				V = V.substring(0, V.length -1);
				codevar = O.parent().data('par');

				// update values in the shortcode generator field
				O.closest('.step2_in').find('input[codevar="' + codevar +'"]').val( V );

				data.vars[ codevar ] = (V!='' && value!='undefined') ? '': V;					
				refresh_sc();			
				
			});
	// update current sc from local
		function refresh_sc(){
			var sc = data.base;
			if( data.vars){
				$.each( data.vars, function(f,v){
					if( v == '') return;
					if( f === undefined || f == 'undefined') return;
					sc += ' '+ f +'="'+ v +'"';
				});
			}	
			$('#ajdePOSH_code').html( '['+ sc +']');
		}
		function reset_sc(){
			data.sc = data.base = 'add_eventon';
			data.vars = {};

			generator.find('.ajde_yn_btn').addClass('NO');
			generator.find('input.ajdePOSH_input').val( '' );
		}


	// go back to 1st step
		function backto_one(O){
			O.animate({'left':'-30px'},'fast');	
			
			$('h3.notifications').removeClass('back');				
			$('.ajdePOSH_inner').animate({'margin-left':'0px'},'fast').find('.step2_in').fadeOut();
						
			// hide step 2
			O.closest('#ajdePOSH_outter').find('.step2').fadeOut();

			reset_sc();
			refresh_sc();

			// change subtitle
			$('#ajdePOSH_subtitle').html( $('#ajdePOSH_subtitle').data('bf') );
		}	
			
	// show back button
		function ajdePOSH_show_back_btn(){
			$('#ajdePOSH_back').animate({'left':'0px'},'fast');		
			$('h3.notifications').addClass('back');
		}

	// Passover created shortcode
		$('body').on('click','.ajdePOSH_insert',function(){
			var obj = $(this);
			var shortcode = obj.siblings('#ajdePOSH_code').html();	

			if( 'type' in data &&  data.type != 'block' && data.type != 'elementor' ){
				// if shortcode insert textbox id given
				var textbox = obj.closest('.ajde_popup').attr('data-textbox');
				
				if(textbox === undefined){
					tinymce.activeEditor.execCommand('mceInsertContent', false, shortcode);	
				}else{
					$('#'+textbox).html(shortcode);
				}	
			}
					
			hide_popupwindowbox();

			$('body').trigger('evo_shortcode_generator_saved', [shortcode, data]);
		});

	// Support
		function hide_popupwindowbox(){
			var container=$('#'+CODE+'POSH_outter').parent().parent();			
			$('body').trigger('evoadmin_lightbox_hide', ['eventon_shortcode']);
			container.removeClass('active');
			popup_open = false;	
		}
		function strip_brac(sc){
			sc = sc.replace('[', '');
			sc = sc.replace(']', '');
			return sc;
		}
});