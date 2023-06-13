/**
 * Javascript: Slider for eventon
 * @version  2.0.6
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
			var cal_width = CAL.width();

			// slides visible 
				slides_visible = ('slides_visible' in SC)? SC.slides_visible: 1;
			
			// slider controls		
				if( SC.control_style == 'tb'|| SC.control_style == 'lr'|| SC.control_style == 'lrc'){
					if(CAL.find('.evoslider_dots').length == 0){
						html = "<span class='evoslider_nav nav prev'><i class='fa fa-angle-left'></i></span>";
						CAL.find('.evo_slider_outter').prepend(html);
						html = "<span class='evoslider_nav nav next'><i class='fa fa-angle-right'></i></span>";
						CAL.find('.evo_slider_outter').append(html);
						CAL.find('.evosl_footer').append( "<span class='evoslider_dots none'></span>" );
					}
				}else{
					html = "<span class='evoslider_nav nav prev'><i class='fa fa-angle-left'></i></span>";
					html += "<span class='evoslider_dots none'></span>";
					html += "<span class='evoslider_nav nav next'><i class='fa fa-angle-right'></i></span>";
					CAL.find('.evosl_footer').html( html );
				}

			slider_w = OUTTER.width();
			slider_w = cal_width;


			// colorize events
				EL.find('.eventon_list_event').each(function(index){
					var el = $(this);

					var c = el.data('colr');
					el.css('background-color', c);					
					if(!el.parent().hasClass('slide')) el.wrap('<div class="slide" data-index="' + index +'"></div>');
					if(!_hex_is_light( c )) el.addClass('sldark');
				});
			
			// slide width setting
				var cur_slide_index = parseInt(EL.data('slideindex'));
				if( cur_slide_index === undefined || !cur_slide_index ) cur_slide_index = 0;

				// all verticals
				if( SC.slider_type == 'vertical'){
					EL.fadeIn().data('slideindex',0);

					OUTTER.height(0);

					for (var i = 0; i < slides_visible; i++) {
						this_height = CAL.find('.slide').eq( i ).height();
						if( this_height == 0) return;

						slider_h += parseInt(this_height);
					}		

					OUTTER.height(slider_h);
				
				// all horizontals
				}else{
					one_slide_w = 0;
					slider_move_distance = slider_w;

					// slides visible
					if( SC.slider_type == 'micro'){
						slv = parseInt( slider_w/ 120);
						slides_visible = slv;
					}else if( SC.slider_type == 'mini'){
						slv = parseInt( slider_w/ 200);
						slides_visible = slv;
					
					}else if( SC.slider_type == 'multi'){
						// set default slide visible count to 4 for multi slide
						if( SC.slides_visible == 1) SC.slides_visible = slides_visible = 4;
						if( slider_w < 400 && SC.slides_visible > 1)	
							slides_visible =  1;
						if( slider_w > 401 && slider_w < 600 && SC.slides_visible > 2)	
							slides_visible =  2;
						if( slider_w > 601 && slider_w < 800 && SC.slides_visible > 3)	
							slides_visible =  3;
						if( slider_w > 801 && slider_w < 1000 && SC.slides_visible > 4) 
							slides_visible =  4;
					}

					//console.log(slider_w);
					//console.log(slides_visible);
					

					
					
					one_slide_w = parseInt(slider_w/ slides_visible);
					slider_move_distance = one_slide_w;		

					all_slides_w = ( slides +2) * one_slide_w + 1;
					//console.log(all_slides_w);

					visible_width = one_slide_w * slides_visible;

					EL.width( all_slides_w ).fadeIn().data('slideindex', cur_slide_index);
					CAL.find('.slide').width( one_slide_w );
					OUTTER.width(visible_width);

				}						

			// slider control dots 
				var dots_html = '';
				if( SC.slide_nav_dots == 'yes'){

					dot_max = slides - slides_visible +1;
					for(var dc = 0; dc< dot_max; dc++){
						dots_html += "<span class='evosl_dot "+ (dc == cur_slide_index? 'f':'') +"' data-index='"+ dc+"'><em></em></span>";
					}	

					var extra_class = dot_max <1 ? 'none':'';

					CAL.find('.evoslider_dots').html( dots_html).addClass(extra_class);		
				}

			// slide looping
				if( SC.slide_loop == 'yes'){
					if(EL.find('.dup').length ==0 ){
						first_slide = EL.find('.slide').clone().first();
						last_slide = EL.find('.slide').clone().last();

						last_slide.addClass('dup').removeClass('slide');
						first_slide.addClass('dup').removeClass('slide');

						EL.prepend( last_slide );
						EL.append( first_slide );
						console.log('dd');

						go_to_slide_index( cur_slide_index +1 , CAL, true, false);
					}					
				}else{
					go_to_slide_index( cur_slide_index , CAL, true, true, true);
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
		const slider_outter = $el.find('.evo_slider_outter');
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
				var slider_inter_area = $el.find('.evo_slider_outter');
				slider_inter_area.on('swiperight', function(event){
					if( !$(event.target).hasClass('evcal_list_a')) return;
					slide.changeSlide( 'prev');
				});

				slider_inter_area.on('swipeleft', function(event, data){
					if( !$(event.target).hasClass('evcal_list_a')) return;
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
	function go_to_slide_index(new_slide_index, CAL, instant = false, move_dots = true, initial_call = false){
		
		var slider = CAL.find('.evo_slider_slide_out');
		var SC = CAL.evo_shortcode_data();
		var EL = CAL.find('.eventon_events_list');
		var new_marl = new_mart = 0;

		var all_slides = CAL.find('.slide').length;
		var cur_slide_index = parseInt(EL.data('slideindex'));
		var slides_visible = parseInt(EL.data('slides_visible'));

		var cur_mart = parseFloat(EL.css('margin-top') );
		var cur_slider_height = slider.height();

		//console.log(all_slides);


		// vertical
		if( SC.slider_type == 'vertical' ){
			new_slider_h = 0;
			
			if( !initial_call){
				for (var i = new_slide_index; i < (new_slide_index + slides_visible); i++) {
					new_slider_h += CAL.find('.slide').eq( i ).height();
				}
				slider.animate({height: new_slider_h });
			}
			
			

			for (var i = 0; i < (new_slide_index ); i++) {
				new_mart += CAL.find('.slide').eq( i ).height();
			}
			new_mart = -1*new_mart;						
			
		
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
		if( move_dots){
			CAL.find('.evosl_footer .evosl_dot').removeClass('f');
			CAL.find('.evosl_footer .evosl_dot').eq( new_slide_index ).addClass('f');
		}
		

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