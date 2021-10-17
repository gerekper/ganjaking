/**
 * Javascript code for single events - single box
 * @version  1.1.1
 */
jQuery(document).ready(function($){
	// redirect only if not set to open as popup
		$('.eventon_single_event').on('click', '.evcal_list_a',function(e){
			var obj = $(this),
				evodata = obj.closest('.ajde_evcal_calendar').find('.evo-data'),
				ux_val = evodata.data('ux_val');

			e.preventDefault();

			// open in event page
			if(ux_val == 4){ 
				var url = obj.parent().siblings('.evo_event_schema').find('[itemprop=url]').attr('href');
				window.location.href= url;
			}else if(ux_val == '2'){ // External Link
				var url = evodata.attr('data-exturl');
				window.location.href= url;
			}else if(ux_val == 'X'){ // do not do anything
				return false;
			}
		})

	// click on the single event box
		$('.eventon_single_event').find('.evcal_list_a').each(function(){			
			var obj = $(this),
				evObj = obj.parent(),
				evodata = obj.closest('.ajde_evcal_calendar').find('.evo-data');
			
			var ev_link = evObj.siblings('.evo_event_schema').find('a[itemprop=url]').attr('href');
			
			//console.log(ev_link);
			if(ev_link!=''){
				obj.attr({'href':ev_link, 'data-exlk':'1'});
			}
			
			// show event excerpt
			var ev_excerpt = evObj.siblings('.evcal_eventcard').find('.event_excerpt').html();
			
			if(ev_excerpt!='' && evodata.data('excerpt')=='1' ){
				var appendation = '<div class="event_excerpt_in">'+ev_excerpt+'</div>'
				evObj.append(appendation);
			}
		
		});

	// each single event box
		$('body').find('.eventon_single_event').each(function(){

			var _this = $(this);
			// show expanded eventCard
			if( _this.find('.evo-data').data('expanded')=='1'){
				_this.find('.evcal_eventcard').show();

				var idd = _this.find('.evcal_gmaps');

				// close button
				_this.find('.evcal_close').parent().css({'padding-right':0});
				_this.find('.evcal_close').hide();

				//console.log(idd);
				var obj = _this.find('.desc_trig');

				obj.evoGenmaps({'fnt':2});

			// open eventBox and lightbox	
			}else if( _this.data('uxval')=='3'){

				var obj = _this.find('.desc_trig');

				// remove other attr - that cause to redirect
				obj.removeAttr('data-exlk').attr({'data-ux_val':'3'});
			}
		})
	
});