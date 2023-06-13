/**
 * Shortcode Generator
 * @version 4.3
 */
jQuery(document).ready(function($){


	// trigger actions		
		$('body').on('evo_trigger_shortcodegenerator',function(event, sc, type, other_id){
			open_sg_lightbox( sc , type, other_id );
		});
		$('body').on('click','.evolb_trigger_shortcodegenerator',function(event){
			event.preventDefault();
			event.stopPropagation();

			const sc = $(this).data('sc')
			open_sg_lightbox(  sc ,'general');
			
		});
		$('body').on('evo_ajax_success_evo_shortcode_generator',function(event, OO, data){			
			$('body').evo_shortcode_init({ 
				'sc': ('sc' in data ? data.sc : ''),
				'type': ('type' in data ? data.type : ''),
				'other_id': ('other_id' in data ? data.other_id : ''),
			});
		});

		function open_sg_lightbox(sc, type, other_id){	
			
			var data = {
				'lbc':'evo_shortcode_generator',				
				't': 'Shortcode Generator',
				'lightbox_loader': false,
				'ajax':'yes',
				'd': {
					'action': 'eventon_get_shortcode_generator',
					'sc':  sc,
					'load_new_content':true,
					'uid':'evo_shortcode_generator',
					'type': type,
					'other_id': other_id
				}
			};

			$('body').evo_lightbox_open(data);	
			//$('body').trigger('evo_lightbox_trigger',[data]);	
		}

	// functionalities
		$.fn.evo_shortcode_init = function(opt){
			el = this;

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

			const generator = $('body').find('.evo_shortcode_generator_box');
			const generator_in = generator.find('.ajdePOSH_inner');


			var def = {
				'sc':'add_eventon','type':'','other_id':''
			}
			var O = $.extend({}, def, opt);

			// process the supplied sc
			var process = function(){

				data.type = O.type;
				data.other_id = O.other_id;

				sc = O.sc;
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

				if( Object.keys( data.vars ).length > 0 )
					goto_inside_step(true);
			}

			var interaction = function(){

				// click on sc
				generator.on('click','.ajdePOSH_btn',function(){
					data.base = data.sc = $(this).attr('code');		
					data.vars = {};	
					goto_inside_step(true);			
				});

				// collapsable fields
				generator.on('click','.fieldline.collapsable',function(){
					$(this).next('.collapsable_fields').toggle();
					$(this).toggleClass('closed');
				});

				// back
				generator.on('click','#ajdePOSH_back',function(){
					$(this).animate({'left':'-30px'},'fast');	
			
					generator.find('h3.notifications').removeClass('back');				
					generator.find('.ajdePOSH_inner').animate({'margin-left':'0px'},'fast').find('.step2_in').fadeOut();
								
					// hide step 2
					$(this).closest('#ajdePOSH_outter').find('.step2').fadeOut();

					reset_sc();
					refresh_sc();

					// change subtitle
					generator.find('#ajdePOSH_subtitle').html( $('#ajdePOSH_subtitle').data('bf') );
				});

				// afterstatements within shortcode gen
				generator.on('click', '.trig_afterst',function(){
					$(this).next('.ajde_afterst').toggle();
				});

				// select field with child fields
				generator.on('change','.ajdePOSH_select_step', function(){
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
				generator.on('click','.ajde_yn_btn', function(){
					var obj = $(this);
					var codevar = $(this).attr('codevar');				
					var value = obj.hasClass('NO') ? 'yes':'no';

					data.vars[ codevar ] = value;			
					refresh_sc();
				});

				// copy shortcode
				generator.on('click','#ajdePOSH_code', function(){
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

				// when fields changed
				generator_in.on('change','.'+CODE+'POSH_input, .'+CODE+'POSH_select, .'+CODE+'POSH_select_step', function(){				
					var obj = $(this);
					var value = obj.val();
					var codevar = obj.attr('codevar');
					if(codevar === undefined) codevar = obj.data('codevar');

					if( codevar == 'undefined' || codevar === undefined) return;

					data.vars[ codevar ] = (value!='' && value!='undefined') ? value:'';		
					refresh_sc();		
				});

				// tax term selection
				generator_in.on('click','.ajdePOSH_tax', function(){
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
				generator_in.on('click', '.POSH_S2_box_item',function(){	
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

				// Passover created shortcode
				generator.on('click','.ajdePOSH_insert',function(){
					var obj = $(this);
					var shortcode = obj.siblings('#ajdePOSH_code').html();	


					if( 'type' in data &&  data.type != 'block' && data.type != 'elementor' ){
						
						// if shortcode insert textbox id given
						var textbox = obj.closest('.evolb_content').data('textbox');
						
						if(textbox === undefined){
							tinymce.activeEditor.execCommand('mceInsertContent', false, shortcode);	
						}else{
							$('#'+textbox).html(shortcode);
						}	
					}
					
					LB = $('body').find('.evo_lightbox.evo_shortcode_generator');
					LB.evo_lightbox_close();

					
					$('body').trigger('evo_shortcode_generator_saved', [shortcode, data]);
				});
			}

			var goto_inside_step = function(force){
				var code_base = data.base;

				if( Object.keys( data.vars ).length > 0 || force){

					var step = generator.find('.step2_in[data-code="'+ code_base +'"]');
					if( step.length>0){
						generator.find('.step2').show();
						step.show();

						if($('body').hasClass('rtl')){
							generator_in.animate({'margin-left':'-600px'},'fast');
						}else{
							generator_in.animate({'margin-left':'-600px'},'fast');
						}
						
						// show back button
						generator.find('#ajdePOSH_back').animate({'left':'0px'},'fast');		
						generator.find('h3.notifications').addClass('back');
						
						var section_name = generator.find('.ajdePOSH_btn[code="'+ code_base +'"]').html();
						
						generator.find('#ajdePOSH_subtitle').html(section_name);
					}
				}
				
				select_sc_vars();
				refresh_sc();
			}
			var select_sc_vars = function(){
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
			var refresh_sc = function(){
				var sc = data.base;
				if( data.vars){
					$.each( data.vars, function(f,v){
						if( v == '') return;
						if( f === undefined || f == 'undefined') return;
						sc += ' '+ f +'="'+ v +'"';
					});
				}	
				generator.find('#ajdePOSH_code').html( '['+ sc +']');
			}
			var reset_sc = function(){
				data.sc = data.base = 'add_eventon';
				data.vars = {};
				generator.find('.ajde_yn_btn').addClass('NO');
				generator.find('input.ajdePOSH_input').val( '' );
			}

			reset_sc();
			process();
			interaction();

		}

	
});