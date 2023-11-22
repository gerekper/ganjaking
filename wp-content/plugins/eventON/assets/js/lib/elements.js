/**
 * EventON elements
 * version: 4.5.1
 */
jQuery(document).ready(function($){

const BB = $('body');

// process element interactivity on demand
	$.fn.evo_process_element_interactivity = function(O){
		setup_colorpicker();
		_evo_elm_load_datepickers();

		if( $('body').find('.evoelm_trumbowyg').length > 0 ){
			$('body').find('.evoelm_trumbowyg').each(function(){
				if ( $.isFunction($.fn.trumbowyg) ) {
					$(this).trumbowyg();
				}
			});	
		}
	}
	// on page load
	$('body').evo_process_element_interactivity();
	// on after elements load
	$('body').on('evo_elm_load_interactivity',function(){
		$(this).evo_process_element_interactivity();
	});

// angle button
	var dragging = false;
	$('body').on('mousedown', '.evo_elm_ang_hold',function(){
		dragging = true
	}).on('mouseup','.evo_elm_ang_hold',function(){
		dragging = false
	}).on('mousemove','.evo_elm_ang_hold',function(e){
		if (dragging) {
			//console.log(e);
			tar = $(this).find('.evo_elm_ang_center');
			var mouse_x = e.offsetX;
            var mouse_y = e.offsetY;
            var radians = Math.atan2(mouse_x - 10, mouse_y - 10);
            var degree = parseInt( (radians * (180 / Math.PI) * -1) + 180 );
			//console.log(degree+ ' '+ mouse_x +' '+mouse_y);

			tar.css('transform', 'rotate(' + degree + 'deg)');
			$(this).siblings('.evo_elm_ang_inp').val( degree +'°');

			$('body').trigger('evo_angle_set',[$(this), degree]);
		}
	}).on('keyup','.evo_elm_ang_inp',function(){
		deg = parseInt($(this).val());
		$(this).val( deg +'°');
		tar.css('transform', 'rotate(' + deg + 'deg)');
		
		$('body').trigger('evo_angle_set',[$(this), deg]);
	});

// yes no button		
	$('body').on('click','.ajde_yn_btn', function(){

		var obj = $(this);
		var afterstatement = obj.attr('afterstatement');
		var newval = 'yes';
		
		// yes
		if(obj.hasClass('NO')){
			obj.removeClass('NO');
			obj.siblings('input').val('yes');				
			
			// afterstatment
			if(afterstatement!=''){
				var type = (obj.attr('as_type')=='class')? '.':'#';
				$('body').find(type+afterstatement).show();
			}

		}else{//no
			obj.addClass('NO');
			obj.siblings('input').val('no');
			newval = 'no';

			
			if(afterstatement != ''){
				var type = (obj.attr('as_type')=='class')? '.':'#';
				$('body').find(type+afterstatement).hide();
			}
		}

		//console.log(newval);

		$('body').trigger('evo_yesno_changed',[newval, obj, afterstatement]);
	});

	// @since 4.5.2
	$.fn.evo_elm_change_yn_btn = function(val){
		el = this;
		el.val( val );
		if( val == 'no'){
			el.siblings('.evo_elm').addClass('NO');
		}else{
			el.siblings('.evo_elm').removeClass('NO');
		}
	}
	

// yes no button afterstatement hook
	BB.on('evo_yesno_changed', function(event, newval, obj, afterstatement){

		if(afterstatement === undefined) return;
		
		if(newval == 'yes'){
			obj.closest('.evo_elm_row').next().show();
		}else{
			obj.closest('.evo_elm_row').next().hide();
		}
	});

// Side panel @4.5.1
	// move the sidepanel to body
		var SP = $('.evo_sidepanel');
		$('.evo_sidepanel').remove();
		BB.append(SP);


// ICON font awesome selector	
	// move icon selector data to body end
		const FAA = $('#evo_icons_data');
		$('#evo_icons_data').remove();
		BB.append(FAA);


	// run icon selector interactive features
	BB.on('click','.evo_icons', function(){

		const el = $(this);
		el.addClass('onfocus');
		el.evo_open_sidepanel({
			'uid':'evo_open_icon_edit',
			'sp_title':'Edit Icons',
			'content_id': 'evo_icons_data',
			'other_data': el.data('val')
		});

		return;
	})
	.on('evo_sp_opened_evo_open_icon_edit',function(event, OO){
		BB.evo_run_icon_selector({icon_val : OO.other_data} );
	});


	$.fn.evo_run_icon_selector = function(options){
		const SP = BB.find('.evo_sp');
		var settings = $.extend({
            icon_val: "",
        }, options );

		var el = SP;
		var icon_on_focus = '';

		el.off('keyup','.evo_icon_search').off('search','.evo_icon_search');;

		var init = function(){
			scrollto_icon();
			icon_on_focus = BB.find('.evo_icons.onfocus.'+ settings.icon_val);

			// move search to header
			el.find('.evo_icon_search_bar').appendTo( el.find('.evosp_head') );
		}

		var scrollto_icon = function(){
			const icon_in_list = el.find('li[data-v="' +settings.icon_val+ '"]');
				icon_in_list.addClass('selected');
			$('#evops_content').scrollTop( icon_in_list.position().top -100);
		}

		// select an icon
		el.on('click','li',function(){
			icon_on_focus = BB.find('.evo_icons.onfocus.'+ settings.icon_val);
			var icon = $(this).find('i').data('name');

			el.find('li').removeClass('selected');
			el.find('li[data-v="'+ icon +'"]').addClass('selected');

			var extra_classes = '';
			if( icon_on_focus.hasClass('so')) extra_classes += ' so';

			//console.log(icon);

			icon_on_focus
				.attr({'class':'evo_icons ajde_icons default fa '+icon + extra_classes })
				.data('val', icon)
				.removeClass('onfocus');
			icon_on_focus.siblings('input').val(icon);

			el.off('click','li');
			el.evo_close_sidepanel();
		});

		// search icon
		el.on('search','.evo_icon_search',function(){
			el.find('li').show();
			scrollto_icon();
		});
		el.on('keyup', '.evo_icon_search',function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			var typed_val = $(this).val().toLowerCase();

			console.log('e');
			
			el.find('li').each(function(){
				const nn = $(this).data('v');
				const n = nn.substr(3);

				if( typed_val == ''){
					$(this).show();
				}else{
					if( n.includes(typed_val ) ){
						$(this).show();
					}else{
						$(this).hide();
					}
				}				
			});	
		});

		init();
	}

	// remove icon
		$('body').on('click','i.evo_icons em', function(){
			$(this).parent().attr({'class':'evo_icons ajde_icons default'}).data('val','');
			$(this).parent().siblings('input').val('');
		});
	

		
	

// select2 dropdown field - 4.0.3
	if ( $.isFunction($.fn.select2) )  $('.ajdebe_dropdown.evo_select2').select2();

// self hosted tooltips
// deprecating
	$('body').find('.ajdethistooltip').each(function(){
		tipContent = $(this).find('.ajdeToolTip em').html();
		toolTip = $(this).find('.ajdeToolTip');
		classes = toolTip.attr('class').split('ajdeToolTip');
		toolTip.remove();
		$(this).append('<em>' +tipContent +'</em>').addClass(classes[1]);
	});

// ELEMENTS
// tooltips
	$('body').on('mouseover','.ajdeToolTip',function(event){
		event.stopPropagation();
		if($(this).hasClass('show')) return;

		const t = $(this).data('d');
		var p = $(this).position();
		
		var cor = getCoords(event.target);

		$('.evo_tooltip_box').removeClass('show').removeClass('L').html(t);
		var box_height = $('.evo_tooltip_box').height();
		var box_width = $('.evo_tooltip_box').width();

		$('.evo_tooltip_box').css({'top': (cor.top - 55 - box_height), 'left': ( cor.left + 5 ) })
			.addClass('show');


		// left align
		if( $(this).hasClass('L')){
			//console.log(box_width);
			$('.evo_tooltip_box').css({'left': (cor.left - box_width - 15) }).addClass('L');			
		}
	})
	.on('mouseout','.ajdeToolTip',function(){	
		$('.evo_tooltip_box').removeClass('show');
	});


	function getCoords(elem) { // crossbrowser version
	    var box = elem.getBoundingClientRect();
	    //console.log(box);

	    var body = document.body;
	    var docEl = document.documentElement;

	    var scrollTop = window.pageYOffset || docEl.scrollTop || body.scrollTop;
	    var scrollLeft = window.pageXOffset || docEl.scrollLeft || body.scrollLeft;

	    var clientTop = docEl.clientTop || body.clientTop || 0;
	    var clientLeft = docEl.clientLeft || body.clientLeft || 0;

	    var top  = box.top +  scrollTop - clientTop;
	    var left = box.left + scrollLeft - clientLeft;

	    return { top: Math.round(top), left: Math.round(left) };
	}

// Select in a row	 
	 $('body').on('click','span.evo_row_select_opt',function(){

	 	var O = $(this);
	 	var P = O.closest('p');
	 	const multi = P.hasClass('multi')? true: false;
				
		if(multi){
			if(O.hasClass('select')){
				O.removeClass('select');
			}else{
				O.addClass('select');
			}

		}else{
			P.find('span.opt').removeClass('select');
			O.addClass('select');
		}

		var val = '';
		P.find('.opt').each(function(){
			if( $(this).hasClass('select')) val += $(this).attr('value')+',';
		});

		val = val.substring(0, val.length-1);

		P.find('input').val( val );		

		$('body').trigger('evo_row_select_selected',[P, $(this).attr('value'), val]);			
	});

// Color picker @+4.5
	setup_colorpicker();
	$('body').on('evo_page_run_colorpicker_setup',function(){
		setup_colorpicker();
	});
	function setup_colorpicker(){
		$('body').find('.evo_elm_color').each(function(){
			var elm = $(this);

			if( typeof elm.ColorPicker ==='function'){
				elm.ColorPicker({
					onBeforeShow: function(){
						$(this).ColorPickerSetColor( '#888888');
					},
					onChange:function(hsb, hex, rgb,el){
						elm.css({'background-color':'#'+hex});		
						elm.siblings('.evo_elm_hex').val( hex );
					},onSubmit: function(hsb, hex, rgb, el) {
						elm.css({'background-color':'#'+hex});		
						elm.siblings('.evo_elm_hex').val( hex );
						$(el).ColorPickerHide();

						var _rgb = get_rgb_min_value(rgb, 'rgb');
						elm.siblings('.evo_elm_rgb').val( _rgb );
					}
				});
			}
		});
	}

	function get_rgb_min_value(color,type){
			
		if( type === 'hex' ) {			
			var rgba = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(color);	
			var rgb = new Array();
			 rgb['r']= parseInt(rgba[1], 16);			
			 rgb['g']= parseInt(rgba[2], 16);			
			 rgb['b']= parseInt(rgba[3], 16);	
		}else{
			var rgb = color;
		}
		
		return parseInt((rgb['r'] + rgb['g'] + rgb['b'])/3);			
	}

	// color picker 2
	$.fn.evo_colorpicker_init = function(opt){
		var el = this;
		var el_color = el.find('.evo_set_color');

		var init = function(){
			el.ColorPicker({		
				color: get_default_set_color(),
				onChange:function(hsb, hex, rgb,el){
					set_hex_values(hex,rgb);
				},
				onSubmit: function(hsb, hex, rgb, el) {
					set_hex_values(hex,rgb);
					$(el).ColorPickerHide();

					// trigger
					$('body').trigger('evo_colorpicker_2_submit', [ el, hex, rgb]);
				}		
			});
		} 			

		var set_hex_values = function(hex,rgb){			
			el.find('.evcal_color_hex').html(hex);
			el.find('.evo_color_hex').val(hex);
			el_color.css({'background-color':'#'+hex});		
			
			// set RGB val
			rgb_val = $('body').evo_rgb_process({ data : rgb, type:'rgb',method:'rgb_to_val'});
			el.find('.evo_color_n').val( rgb_val );
		}
		
		var get_default_set_color = function(){
			var colorraw = el_color.css("background-color");						
			var def_color = el.evo_rgb_process({data: colorraw, method:'rgb_to_hex'});	
			return def_color;
		}

		init();
	}
	$('body').on('evo_eventedit_dom_loaded_evo_color',function(event, val){
		$('body').find('.evo_color_selector').each(function(){
			$(this).evo_colorpicker_init();	
		});					
	});



	
// plus minus changer
	$('body').on('click','.evo_plusminus_change', function(event){

        OBJ = $(this);

        QTY = parseInt(OBJ.siblings('input').val());
        MAX = OBJ.siblings('input').data('max');        
        if(!MAX) MAX = OBJ.siblings('input').attr('max');           

        NEWQTY = (OBJ.hasClass('plu'))?  QTY+1: QTY-1;

        NEWQTY =(NEWQTY <= 0)? 0: NEWQTY;

        // can not go below 1
        if( NEWQTY == 0 && OBJ.hasClass('min') ){    return;    }

        NEWQTY = (MAX!='' && NEWQTY > MAX)? MAX: NEWQTY;

        OBJ.siblings('input').val(NEWQTY);

        if( QTY != NEWQTY) $('body').trigger('evo_plusminus_changed',[NEWQTY, MAX, OBJ]);
       
        if(NEWQTY == MAX){
            PLU = OBJ.parent().find('b.plu');
            if(!PLU.hasClass('reached')) PLU.addClass('reached');   

            if(QTY == MAX)   $('body').trigger('evo_plusminus_max_reached',[NEWQTY, MAX, OBJ]);                 
        }else{            
            OBJ.parent().find('b.plu').removeClass('reached');
        } 
    });

// date time picker
	var RTL = $('body').hasClass('rtl');

	// load date picker libs
	_evo_elm_load_datepickers();
	$('body').on('evo_elm_load_datepickers',function(){
		_evo_elm_load_datepickers();
	});
	$('body').on('click','.evo_dpicker',function(){	
		_evo_elm_load_datepickers( true, $(this).attr('id') );
	});

	function _evo_elm_load_datepickers( call = false, OBJ_id){

		
		$('body').find('.evo_dpicker').each(function(){

			var OBJ = $(this);
			if( OBJ.hasClass('dp_loaded')) return;

			const this_id = OBJ.attr('id');
			var rand_id = OBJ.closest('.evo_date_time_select').data('id');			
			var D = $('body').find('.evo_dp_data').data('d');

			OBJ.addClass('dp_loaded');

			OBJ.datepicker({
				dateFormat: D.js_date_format,
				firstDay: D.sow,
				numberOfMonths: 1,
				altField: OBJ.siblings('input.alt_date'),
				altFormat: OBJ.siblings('input.alt_date_format').val(),
				isRTL: RTL,
				onSelect: function( selectedDate , ooo) {

					//var date = new Date(ooo.selectedYear, ooo.selectedMonth, ooo.selectedDay);
					var date = OBJ.datepicker('getDate');

					$('body').trigger('evo_elm_datepicker_onselect', [OBJ, selectedDate, date, rand_id]);

					if( OBJ.hasClass('start') ){
						// update end time
						var eO = $('body').find('.evo_date_time_select.end[data-id="'+rand_id+'"]').find('input.datepickerenddate');
						if(eO.length>0){
							
							eO.datepicker( 'setDate', date);
							eO.datepicker( "option", "minDate", date );
						}
					}
				}
			});

			var id_match = ( ( OBJ_id !== undefined && OBJ_id == this_id ) || OBJ_id === undefined )
				? true: false;

			if( call && id_match ) OBJ.datepicker('show');
		});
	}

	
// time picker
	$('body').on('change','.evo_timeselect_only',function(){
		var P = $(this).closest('.evo_time_edit');
		var min = 0;

		min += parseInt(P.find('._hour').val() ) *60;
		min += parseInt(P.find('._minute').val() );

		P.find('input').val( min );
	});

// Upload data files
// @version 4.0.2
	$('body').on('click','.evo_data_upload_trigger',function(event){
		if( event !== undefined ){
			event.preventDefault();
			event.stopPropagation();
		}
		OBJ = $(this);

		const upload_box = OBJ.closest('.evo_data_upload_holder').find('.evo_data_upload_window');
		upload_box.show();

		const msg_elm = upload_box.find('.msg');
		msg_elm.hide();		
	});

	$('body').on('click','.upload_settings_button',function(event){
		//event.preventDefault();
		OBJ = $(this);

		const upload_box = OBJ.closest('.evo_data_upload_window');

		// show form
		upload_box.show();
		//console.log('s');

		const msg_elm = upload_box.find('.msg');
		const form = upload_box.find('form');
		var fileSelect = upload_box.find('input');
		const acceptable_file_type = fileSelect.data('file_type');
		msg_elm.hide();
		
		// when form submitted
		$(form).submit(function(event){
			//console.log('d');
			event.preventDefault();
			msg_elm.html('Processing').show();

			var files = fileSelect.prop('files');

			if( !files ){
			 	msg_elm.html('Missing File.'); return;
			}
			
			var file = files[0];

			if (!window.File || !window.FileReader || !window.FileList || !window.Blob) {
		      	alert('The File APIs are not fully supported in this browser.');
		      	return;
		    }

		    if( file === undefined ){
		    	msg_elm.html('Missing File.'); return;
		    }
		    if( file.name.indexOf( acceptable_file_type ) == -1 ){
		  		msg_elm.html('Only accept '+acceptable_file_type+' file format.');
		  	}else{
		  		var reader = new FileReader();
			  	reader.readAsText(file);

	            reader.onload = function(reader_event) {
	            	$('body').trigger('evo_data_uploader_submitted', [reader_event, msg_elm, upload_box]);
	            };
	            reader.onerror = function() {
	            	msg_elm.html('Unable to read file.');
	            };
	        }	

	        return false;		
		});
		return true;
	});

	// close upload window
	$('body').on('click','.evo_data_upload_window_close',function(){
		$(this).parent().hide();
	});

// lightbox select
	$('body').on('click','.evo_elm_lb_field input',function(event){
		const lb = $(this).closest('.evo_elm_lb_select');
		$('body').find('.evo_elm_lb_window.show').removeClass('show').fadeOut(300);
		lb.find('.evo_elm_lb_window').show().delay(100).queue(function(){
		    $(this).addClass("show").dequeue();
		});
	});

	// close lightbox
		$(window).on('click', function(event) {
			if( !($(event.target).hasClass('evo_elm_lb_field_input')) )
				$('body').find('.evo_elm_lb_window').removeClass('show').fadeOut(300);
		});
		$(window).blur(function(){
			//$('body').find('.evo_elm_lb_window').removeClass('show').fadeOut(250);
		});

	// selecting options in lightbox select field
	$('body')
		.on('click','.eelb_in span',function(){
			const field = $(this).closest('.evo_elm_lb_select').find('input');
			if($(this).hasClass('select')){
				$(this).removeClass('select');
			}else{
				$(this).addClass('select');
			}

			var V = '';

			$(this).parent().find('span.select').each(function(){
				V += $(this).attr('value')+',';
			});

			field.val( V ).trigger('change');
			$('body').trigger('evo_elm_lb_option_selected',[ $(this), V]);
		})
		.on('click','.evo_elm_lb_window',function(event){
			if( event !== undefined ){
				event.preventDefault();
				event.stopPropagation();
			}
		})
	;
});