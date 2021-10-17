/**
 * Javascript: Slider for eventon
 * @version  2.0
 */
jQuery(document).ready(function($){
	
// INIT
	  sliderfy_events();

// window resize
	$( window ).resize(function() {
		sliderfy_events();
	});

// EVO Slider code
	function sliderfy_events(){
		$('body').find('.evoslider').each(function(){
			var CAL = $(this);
			var SC = CAL.evo_shortcode_data();
			//console.log( SC);

			var OUTTER = CAL.find('.evo_slider_slide_out');
			var slides = CAL.find('.eventon_list_event').length;
			var EL = CAL.find('.eventon_events_list');
			
			var slider_move_distance = 0;
			var all_slides_w = 0;
			var slider_h = 0;

			// slides visible 
				slides_visible = ('slides_visible' in SC)? SC.slides_visible: 1;
			
			// slider controls		
				if( SC.control_style == 'tb'|| SC.control_style == 'lr'|| SC.control_style == 'lrc'){
					if(CAL.find('.evoslider_dots').length == 0){
						html = "<span class='evoslider_nav nav prev'><i class='fa fa-angle-left'></i></span>";
						CAL.find('.evo_slider_outter').prepend(html);
						html = "<span class='evoslider_nav nav next'><i class='fa fa-angle-right'></i></span>";
						CAL.find('.evo_slider_outter').append(html);
						CAL.find('.evosl_footer').append( "<span class='evoslider_dots'></span>" );
					}
				}else{
					html = "<span class='evoslider_nav nav prev'><i class='fa fa-angle-left'></i></span>";
					html += "<span class='evoslider_dots'></span>";
					html += "<span class='evoslider_nav nav next'><i class='fa fa-angle-right'></i></span>";
					CAL.find('.evosl_footer').html( html );
				}

			slider_w = OUTTER.width();

			// colorize events
				EL.find('.eventon_list_event').each(function(){
					var el = $(this);

					var c = el.data('colr');
					el.css('background-color', c);					
					if(!el.parent().hasClass('slide')) el.wrap('<div class="slide"></div>');
					if(!_hex_is_light( c )) el.addClass('sldark');
				});

			// slide width setting
				// all verticals
				if( SC.slider_type == 'vertical'){
					EL.fadeIn().data('slideindex',0);

					CAL.find('.evo_slider_slide_out').height(0);

					for (var i = 0; i < slides_visible; i++) {
						slider_h += CAL.find('.slide').eq( i ).height();
					}		

					CAL.find('.evo_slider_slide_out').height( slider_h );
				
				// all horizontals
				}else{
					one_slide_w = 0;
					slider_move_distance = slider_w;

					// slides visible
					if( SC.slider_type == 'micro'){
						slv = parseInt( slider_w/ 120);
						slides_visible = SC.slides_visible = slv;
					}else if( SC.slider_type == 'mini'){
						slv = parseInt( slider_w/ 200);
						slides_visible = SC.slides_visible = slv;
					}else if( SC.slider_type == 'multi'){
						if( slider_w < 400)	slides_visible = SC.slides_visible = 1;
						if( slider_w > 401 && slider_w < 600)	slides_visible = SC.slides_visible = 2;
						if( slider_w > 601 && slider_w < 800)	slides_visible = SC.slides_visible = 3;
						if( slider_w > 801 ) slides_visible = SC.slides_visible = 4;
					}

					var cur_slide_index = parseInt(EL.data('slideindex'));
					if( cur_slide_index === undefined || !cur_slide_index ) cur_slide_index = 0;
					
					one_slide_w = (slider_w/ slides_visible);
					slider_move_distance = one_slide_w;		

					all_slides_w = slides * one_slide_w;


					EL.width( all_slides_w ).fadeIn().data('slideindex', cur_slide_index);
					CAL.find('.slide').width( one_slide_w);

					// focus slider into the correct slide index
					go_to_slide_index( cur_slide_index , CAL, true);
				}		

			// slider control dots 
				var dots_html = '';
				if( SC.slide_nav_dots == 'yes'){

					dot_max = slides - slides_visible +1;
					for(var dc = 0; dc< dot_max; dc++){
						dots_html += "<span class='evosl_dot "+ (dc == cur_slide_index? 'f':'') +"' data-index='"+ dc+"'><em></em></span>";
					}	

					CAL.find('.evoslider_dots').html( dots_html);		
				}

			// set slider data for interaction
				EL.data({
					'slider_move_distance': slider_move_distance,
					'all_slides_w': all_slides_w,
					'slides_visible': slides_visible
				});

			// hide slider controls
				if( SC.slide_hide_control == 'yes')	CAL.find('.evosl_footer').hide();
			
		});
	}

// slider works
	$.fn.slider_work = function (options) {
		var slide = {},
		interval = null,
		$el = this;
		slide.$el = this;
		var SC = $el.evo_shortcode_data();
		var EL = $el.find('.eventon_events_list');
		var all_slides = $el.find('.eventon_list_event').length;

		slide = {
			iv: SC.slider_pause,
			running: false,
			init: function(){
				if( SC.slide_auto == 'yes'){
					slide.auto();

					// pause on hover
					if( SC.slide_pause_hover == 'yes'){
						$el.on('mouseover','.evo_slider_slide_out', function(){
							slide.pause();
						}).on('mouseout',function(){
							slide.auto();
						});	
					}				
				}
			},
			auto: function (){
				clearInterval( interval );

				if( SC.slide_auto == 'yes'){ // if auto slide enabled via shortcode
					interval = setInterval(function(){
						slide.gotoNextSlides();
					}, this.iv );
				}
			},
			resetInterval: function(){
				slide.auto();
			},
			pause: function(){
				clearInterval(interval);
			},
			gotoNextSlides: function(){
				var cur_slide_index = parseInt(EL.data('slideindex'));
				var slides_visible = parseInt(EL.data('slides_visible'));

				new_slide_index = (cur_slide_index == (all_slides- slides_visible) ) ? 0: cur_slide_index + 1;
				if( new_slide_index<0) new_slide_index = 0;
				go_to_slide_index(new_slide_index, $el);
				slide.resetInterval();
			},
			interaction: function(){
				// click on nav arrows
				$el.on('swiperight','.evo_slider_outter', function(){
					slide.changeSlide( 'prev');
				});
				$el.on('swipeleft','.evo_slider_outter', function(){
					slide.changeSlide( 'next');
				});

				$el.on('click','.evoslider_nav',function(){
					var direction = $(this).hasClass('next')? 'next':'prev';
					slide.changeSlide( direction);
				});

				// click on control dots
				$el.on('click','.evosl_dot', function(){
					go_to_slide_index( $(this).data('index') , $el);
				});
			},
			changeSlide: function(direction){
				var new_slide_index = 0;
					
				var cur_slide_index = parseInt(EL.data('slideindex'));
				var slides_visible = parseInt(EL.data('slides_visible'));

				if(direction == 'next'){
					new_slide_index = (cur_slide_index == (all_slides- slides_visible) ) ? 0: cur_slide_index + 1;
				}else{ //previous
					new_slide_index = (cur_slide_index == 0)? 
						all_slides - slides_visible : 
						cur_slide_index - 1;
				}

				if( new_slide_index<0) new_slide_index = 0;
				go_to_slide_index(new_slide_index, $el);
				
				slide.resetInterval();
			}
		};

		slide.init();
		slide.interaction();
	};
	
	$('body').find('.evoslider').each(function(){
		$(this).slider_work();
	});

// slider control interaction
	// hover over micro slides
		$('.ajde_evcal_calendar.microSlider').on('mouseover','.eventon_list_event', function(){
			O = $(this);
			OUT = O.closest('.evo_slider_outter');
			title = O.find('.evcal_event_title').html();

			p = O.position();

			OUT.append('<span class="evo_bub_box" style="">'+ title +"</span>");
			B = OUT.find('.evo_bub_box');

			l = p.left;
			t = p.top- B.height() -30;

			// adjust bubble to left if event on right edge
			LM = OUT.width();
			tl = p.left + B.width() + O.width();
			if(   tl > LM){
				l = l - B.width() +O.width()-20;
			}

			B.css({'top':t, 'left':l});

			OUT.find('.evo_bub_box').addClass('show');
		}).on('mouseout',function(){
			B = $(this).find('.evo_bub_box').remove();
		});

// go into a focused slide
	function go_to_slide_index(new_slide_index, CAL, instant = false){
		var slider = CAL.find('.evo_slider_slide_out');
		var SC = CAL.evo_shortcode_data();
		var EL = CAL.find('.eventon_events_list');
		var new_marl = new_mart = 0;

		var all_slides = CAL.find('.slide').length;
		var cur_slide_index = parseInt(EL.data('slideindex'));
		var slides_visible = parseInt(EL.data('slides_visible'));

		var cur_mart = parseFloat(EL.css('margin-top') );
		var cur_slider_height = slider.height();


		// vertical
		if( SC.slider_type == 'vertical' ){
			new_slider_h = 0;
			for (var i = new_slide_index; i < (new_slide_index + slides_visible); i++) {
				new_slider_h += CAL.find('.slide').eq( i ).height();
			}

			for (var i = 0; i < (new_slide_index ); i++) {
				new_mart += CAL.find('.slide').eq( i ).height();
			}
			new_mart = -1*new_mart;						
			slider.animate({height: new_slider_h });
		// horizontal
		}else{
			var slider_move_distance = EL.data('slider_move_distance');
			var cur_marl = parseFloat(EL.css('margin-left'));

			for (var i = 0; i < (new_slide_index ); i++) {
				new_marl += CAL.find('.slide').eq( i )[0].getBoundingClientRect().width;
			}

			new_marl = -1*new_marl;

			// end validation
			if( (new_marl -  slider.width())*-1 >  ( EL.width() )){
				new_marl = 0; new_slide_index = 0;
			} 
		}

		// set dot focus
		CAL.find('.evosl_footer .evosl_dot').removeClass('f');
		CAL.find('.evosl_footer .evosl_dot').eq( new_slide_index ).addClass('f');

		EL.data('slideindex', new_slide_index);
		if( instant){
			EL.css({
				marginLeft: new_marl,
				marginTop: new_mart,
			});
		}else{
			EL.animate({
				marginLeft: new_marl,
				marginTop: new_mart,
			}, parseInt(SC.slider_speed) , 'easeOutCirc');
		}
		
	}

// whether an event color is lighter or dark
	function _hex_is_light(color) {
		if( color === undefined ) return false;
	    const hex = color.replace('#', '');
	    const c_r = parseInt(hex.substr(0, 2), 16);
	    const c_g = parseInt(hex.substr(2, 2), 16);
	    const c_b = parseInt(hex.substr(4, 2), 16);
	    const brightness = ((c_r * 299) + (c_g * 587) + (c_b * 114)) / 1000;
	    return brightness > 220;
	}

});