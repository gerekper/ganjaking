/**
 * Javascript: Slider for eventon
 * @version  0.1
 */
(function ($, undefined) {
	$.fn.evoCalendar = function(opt){
		
		var defaults = {
			api:	 '',
			calendar_url: '',
			new_window: '',
			_action: '',
			loading_text :'Loading Calendar...'			
		};
		var options = $.extend({}, defaults, opt); 
		
		if(options.api === undefined) return;

		var $el = this;

		$el.html( options.loading_text);

		$.getJSON( options.api, function( data ) {
			//console.log(data);
		  	
		  	$el.html( '<style type="text/css">'+ data.styles +'</style>');
		  	$el.append(data.html);
		});

		// click on events
		$el.on('click','a.desc_trig',function(){
			OBJ = $(this);
			var url = (options.calendar_url == ''  )? 
				OBJ.closest('.eventon_list_event').find('.evo_event_schema').find('a').attr('href'):
				options.calendar_url;
			
			$open = (options.new_window === undefined || options.new_window == false)? '_self':'_blank';
			//console.log(OBJ);
			if(url!== undefined && url!='') window.open(url, $open);
			return false;
		});

	};
}(jQuery));