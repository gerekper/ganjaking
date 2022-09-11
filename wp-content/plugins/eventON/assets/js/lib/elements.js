/**
 * EventON elements
 * version: 4.0.3
 */
jQuery(document).ready(function($){

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
// yes no button afterstatement hook
	$('body').on('evo_yesno_changed', function(event, newval, obj, afterstatement){

		if(afterstatement === undefined) return;
		
		if(newval == 'yes'){
			obj.closest('.evo_elm_row').next().show();
		}else{
			obj.closest('.evo_elm_row').next().hide();
		}
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
	 $('.evo_row_select').on('click','span.opt',function(){

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

// Color picker
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
		event.preventDefault();
		OBJ = $(this);

		const upload_box = OBJ.closest('.evo_data_upload_holder').find('.evo_data_upload_window');

		// show form
		upload_box.show();

		const msg_elm = upload_box.find('.msg');
		const form = upload_box.find('form');
		var fileSelect = upload_box.find('input');
		const acceptable_file_type = fileSelect.data('file_type');
		msg_elm.hide();

		// when form submitted
		$(form).submit(function(event){
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
		});
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
			event.stopPropagation();
		})
	;
});